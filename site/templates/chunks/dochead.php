<?php
namespace ProcessWire;
/** @var Config $config */
/** @var Wire $wire */
/** @var Page $page */
/** @var Pages $pages */
/** @var User $user */
/** @var PageInstance $pageInstance */
/** @var SiteSettings $settings */
/** @var Tools $tools */
/** @var Client $client */
/** @var Cart $cart */

$meta_keywords = $meta_description = "";
$pageTitle = $page->title . " - " . $settings->site_master_title;
if($page->id == 1) {
	$pageTitle = $settings->site_master_title; //just the site title, not "Home - Site Title"
}
//get the SEO meta info if it exists
if($page->get('meta_description')) {
	$meta_description = $page->get('meta_description');
}
else {
	if($page->get('body_content')) {
		$meta_description = $wire->sanitizer->truncate($page->body_content, 140, 'sentence');
	}
}
$meta_keywords = "";
if($page->get('meta_keywords')) {
	$meta_keywords = $page->get('meta_keywords');
}

$ogimage = "";
if($page->hero_image != "") {
	$heroFirstImage = $tools->getFirstImageFromPage($page, "hero_image");
	$heroimg = $heroFirstImage->size(1200, 450);
	$ogimage = $heroimg->httpUrl;
}
else { //get the default logo image
	$ogimage = $settings->social_logo->httpUrl;
}

$hamburger_breakpoint = $settings->hamburger_breakpoint;
if($hamburger_breakpoint == "") {
	$hamburger_breakpoint = 769;
}
?>
<!DOCTYPE HTML>
<html lang="en" dir="ltr">
<head>
<meta charset="utf-8" />
<title><?php echo $pageTitle ?></title>
<!--<link rel="shortcut icon" href="--><?php //echo $config->urls->root ?><!--favicon.ico" type="image/x-icon" />-->
<meta content="<?php echo $meta_keywords ?>" name="keywords" />
<meta content="<?php echo $meta_description ?>" name="description" />
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="<?php echo $config->urls->templates; ?>scripts/jqueryui/jquery-ui.min.js"></script>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="<?php echo $config->urls->templates . 'scripts/uikit/'; ?>css/uikit.min.css" />
<script src="<?php echo $config->urls->templates . 'scripts/uikit/'; ?>js/uikit.min.js"></script>
<script src="<?php echo $config->urls->templates . 'scripts/uikit/'; ?>js/uikit-icons.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $config->urls->templates . 'style.css?v='.time(); ?>" />
<meta property="og:url" content="<?php echo $page->httpUrl ?>" />
<meta property="og:type" content="website" />
<meta property="og:title" content="<?php echo $pageTitle ?>" />
<meta property="og:description" content="<?php echo $pageTitle ?>" />
<meta property="og:image" content="<?php echo $ogimage ?>" />
<script>
$(document).ready(function() {
	window.currentlink = "<?php echo $page->httpUrl ?>";
	window.pageid = "<?php echo $page->id ?>";
	window.jumpPageTargetId = "<?php echo $_GET['targPageId'] ?>";
	window.hamburger_breakpoint = parseInt("<?php echo $hamburger_breakpoint ?>");

	var targ = "<?php echo $_GET['target'] ?>";
	if(targ != "") {
		jump(targ);
	}

	//shows the current menu item as active if we are on that page (comment out to turn off)
	$("#desktop-menu li a").each(function() {
		var thislink = $(this).attr("href");
		if(thislink == window.currentlink) {
			$(this).addClass("menuActiveState");
		}
	});

	//use below if we need to change header colour on SECTION aggregate pages to match section colour
	// var currname = "<?php echo $page->name ?>"; //for section aggregate pages
	// $("#header, #header-sticky").addClass(currname+"_bg");

	//use below if we need to change header colour on ARTICLE pages to match section colour
	// var articleSection = "<?php //echo $articleSection ?>";
	// if(articleSection != "") {
		// $("#header, #header-sticky").addClass(articleSection+"_bg");
	// }

	//hover states for menu items (if changing background colour on hover)
	// $("#desktop-menu li a").hover(function() {
		// var thisbgclass = $(this).find(".menuTitle").attr("data-name");
		// $(this).addClass(thisbgclass);
		// $(this).find(".menuTitle").css("color","#FFFFFF");
	// },
	// function() {
		// var thisbgclass = $(this).find(".menuTitle").attr("data-name");
		// $(this).removeClass(thisbgclass);
		// $(this).find(".menuTitle").css("color","#000000");
	// }
	// );

	displaySize();

	//handle embedded vids in article body, change width to 100%
	$(".articlebody iframe").prop("width","100%");

	$(window).resize(function() {
		displaySize();
	});

	$(".datepick").datepicker({
		dateFormat : 'yy-mm-dd'
	});

	prepareProductWatcher();

	$(".productitem").on("mouseover",function() {
		$(this).find(".productoverlay").css("opacity","1");
	}).on("mouseout",function() {
		$(this).find(".productoverlay").css("opacity","0");
	});

	$('#searchbox').on('keypress', function(e){
		if ( e.keyCode == 13 ) {
			doSearch();
		}
	});

});

function nothing() { }

//functions to handle page resize and responsive embedded videos from YouTube etc (in hero banner)
function displaySize() { //called on resize and page load
	var f = viewport();
	window.winwidth = f.width;
	if(window.winwidth < hamburger_breakpoint) { //this is mobile view
		window.isMobile = 1;
		//do anything else that needs to change class-wise for this screen size
	}
	else { //this is desktop view
		window.isMobile = 0;
		//do anything else that needs to change class-wise for this screen size
	}
}

function viewport() {
	var e = window, a = 'inner';
	if (!('innerWidth' in window )) {
		a = 'client';
		e = document.documentElement || document.body;
	}
	return { width : e[ a+'Width' ] , height : e[ a+'Height' ] };
}

function genericReq(targ,sendstring,callback) {
	var xmlhttp = new XMLHttpRequest();
	xmlhttp.open("POST",targ,true);
	xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlhttp.send(sendstring);
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState === 4) {
			if (xmlhttp.status === 200) {
				reply  = xmlhttp.responseText;
				var fn = new Function(callback+"()");
				fn(); //runs the dynamically named callback function
			}
		}
	};
}

function genericAjax(targ, sendstring) {
	//usage genericAjax(targ,sendstring).done(callbackfuncname);
	//function callbackfuncname(data) { } where data is echoed from targ (eg what used to be reply)
	showloader();
	return $.ajax(targ, {
		type: 'POST',
		data: sendstring
	});
}

$(document).ajaxError(function(e, xhr, settings, exception) {
	hideloader();
});

//this needs to be added to allow IE Javascript to search for item in array using includes() function
if (!Array.prototype.includes) {
	Array.prototype.includes = function(search, start) {
		if (typeof start !== 'number') {
			start = 0;
		}
		if (start + search.length > this.length) {
			return false;
		} else {
			return this.indexOf(search, start) !== -1;
		}
	};
}

function jump(targ) { //scroll to section - used for menu items on single page sites
	$("html, body").animate({
		scrollTop: $("." + targ).offset().top - 60
	}, 900, "swing");
}

function validateEmailAddress(emailinput) {
	if(emailinput.indexOf("@") <= 0) {
		return false;
	}
	else {
		return true;
	}
}

function replier() {
	if(reply == "") {
		reply = "Sorry, but there seemed to be a problem.  Please try emailing us directly";
	}
	alert(reply);
	$(".submitter").hide();
}


function submitEventReply() {
	$(".submitter").text("Done");
	alert(reply);
	window.location = "<?php echo $config->urls->root; ?>";
}

function showUpload(type,targfilename) {
	$("#uploadType").val(type);
	$("#upload_div, #uploadBlanket").show();
	$("#uploadTargetFilename").val(targfilename);
}

function cancelUpload() {
	$("#upload_div, #uploadBlanket").hide();
	$("#uploadType").val("");
}

function closeUpload() {
	$("#upload_div, #uploadBlanket").hide();
}

function updateVal(targ,storename) {
	$("#"+targ).val(storename);
	$("#uploadFile, #uploadType").val("");
}

function loginPopupShow() {
	$("#popupBlanket, #loginbox").fadeIn(150);
}

function loginPopupHide() {
	$("#popupBlanket, #loginbox, #createAccountBox").fadeOut(150);
}

function doLogin() {
	var emval = $("#loginbox #emailinput").val();
	if(validateEmailAddress(emval) === true) {
		var pwdin = $("#loginbox #passinput").val();
		if(pwdin.length > 0) {
			var targ = "<?php echo $config->urls->root ?>post/do-login.php";
			var sendstring = "func=dologin&email="+emval+"&pwd="+pwdin;
			var callback = "handleLoginResponse";
			genericReq(targ,sendstring,callback);
		}
		else {
			alert("Please enter your password");
		}
	}
}

function handleLoginResponse() {
	if(reply === "logintrue") {
		window.location = "<?php echo $config->urls->root ?>";
	}
	else {
		alert("There was an error logging in, please try again.  If you don't know your password please use the Forget Password button, or if you don't have an account please request one below");
	}
}

function showRequestAccount() {
	$("#loginbox").hide();
	$("#createAccountBox").show();
}

function requestAccount() {
	var emval = $("#loginbox #emailinputreq").val();
	if(validateEmailAddress(emval) === true) {
		var targ = "<?php echo $config->urls->root ?>post/request-client-account.php";
		var sendstring = "func=requestAcct&email="+emval;
		var callback = "alertReply";
		genericReq(targ,sendstring,callback);
	}
}

function forgetPwd() {
	var emval = $("#loginbox #emailinput").val();
	if(validateEmailAddress(emval) === true) {
		var targ = "<?php echo $config->urls->root ?>post/user-password-remind.php";
		var sendstring = "func=forgetPwd&email="+emval;
		var callback = "alertReply";
		genericReq(targ,sendstring,callback);
	}
        else {
		alert("Please enter a valid email address in the form above.");
	}
}

function alertReply() {
	alert(reply);
        loginPopupHide();
}

function logout() {
//	var c = confirm("This will also clear any items in your shopping cart - are you sure?");
//	if(c === true) {
		var targ = "<?php echo $config->urls->root ?>post/logout.php";
		var sendstring = "func=logout";
		var callback = "goToLanding";
		genericReq(targ,sendstring,callback);
//	}
}

function goToLanding() {
	window.location = "<?php echo $config->urls->root ?>";
}

//cart functions
function getCatItems(catid) {
	window.location = "<?php echo $config->urls->root ?>shop/?category="+catid;
}

function prepareProductWatcher() { //call on load and after ajax return for cart, cat and product work
	$(".numItems").on("focus", function () {
		var tv = $(this).val();
		if (tv == "Qty" || parseInt(tv) < 1) {
			$(this).val("");
		}
	});

	//$(".addToCartBtn").on("click", function () {
	//	var paritem = $(this).closest(".productitem");
	//	var proditemid = $(paritem).attr("id");
	//	var prodid = parseInt(proditemid.replace("product", ""));
	//	if (prodid > 0) {
	//		var numItems = $(paritem).find(".numItems").val();
	//		var price = parseFloat($(paritem).find(".priceVal").html());
	//		if (parseInt(numItems) > 0) {
	//			var targ = "<?php //echo $config->urls->root ?>//post/cart-add.php";
	//			var sendstring = "func=addToCart&prodid=" + prodid + "&numitems=" + numItems;
	//			var callback = "handleCartReply";
	//			genericReq(targ, sendstring, callback);
	//		}
	//	}
	//});

	$(".cartnumupdatebtn").on("click",function() {
		var paritem = $(this).closest(".cartitem");
		var prodid = parseInt($(paritem).find(".cartitemid").val());
		var size = parseInt($(paritem).attr("data-size"));
		var colour = parseInt($(paritem).attr("data-colour"));
		if (prodid > 0) {
			var numItems = $(paritem).find(".cartitemnum").val();
			if (parseInt(numItems) >= 0) {
				var targ = "<?php echo $config->urls->root ?>post/cart-update.php";
				var sendstring = "func=updateCart&prodid=" + prodid + "&numitems=" + numItems+"&sz="+size+"&cl="+colour;
				var callback = "handleCartReplyCartpage";
				genericReq(targ, sendstring, callback);
			}
		}
	});

	$(".cartnumremovebtn").on("click",function() {
		var paritem = $(this).closest(".cartitem");
		$(paritem).find(".cartitemnum").val(0);
		$(paritem).find(".cartnumupdatebtn").click();
	});

	$(".wishlistremovebtn").on("click",function() {
		var paritem = $(this).closest(".cartitem");
		var pr = new ProductItem();
		pr.getInfoFromProdPage();
		var targ = "<?php echo $config->urls->root ?>post/wishlist-remove.php";
		var sendstring = "func=removeFromWishlist&prodid=" + pr.id + "&cl=" + pr.colour + "&sz=" + pr.size;;
		var callback = "reloadPage";
		genericReq(targ, sendstring, callback);
	});

}

function reloadPage() {
	window.location.reload();
}

var cartPopupTimeout;
function handleCartReplyCartpage() {
	var reparr = reply.split("|");
	handleCartReply(reparr);
	$("#cartcontents").html(reparr[3]);
	prepareProductWatcher(); //make sure any new items are handled
}

function handleCartReply() {
	var reparr = reply.split("|");
	handleMainCartReply(reparr);
}

function handleMainCartReply(reparr) {
	var msg = "Cart: ";
	var newnumitems = parseInt(reparr[0]);
	if(newnumitems === 0) {
		msg = msg + "0 items";
	}
	else if(newnumitems === 1) {
		msg = msg + "1 item ($";
	}
	else {
		msg = msg + newnumitems + " items ($";
	}
	if(newnumitems > 0) {
		msg = msg + reparr[1] + ")"
	}
	$("#carttopcontent").html(msg);
	$("#carttopcontent").addClass("cartmsghighlight");
	$("#cartPopupMsg").html(reparr[2]);
	$("#cartPopupHolder").fadeIn(150);
	//prepareProductWatcher(); //make sure any new items are handled
	//having it in causes addtocart to be fired multiple times
	//should only be enabled when using ajax to load products to page
	//leave comments here in case it needs to be put in again for more ajaxy carts (not sure if that's the case or not)
	clearTimeout(cartPopupTimeout);
	cartPopupTimeout = setTimeout(function() {
		hideCartPopup();
	},3000);
}

function hideCartPopup() {
	$("#cartPopupHolder").fadeOut(150);
	$("#carttopcontent").removeClass("cartmsghighlight");
}

//search functions
function doSearch() {
	var inputval = $("#searchbox").val();
	var mobinputval = $("#mobile-searchbox").val();
	if(inputval == "") { //grab from mobile input if the main box is blank (as it will be on mobile)
		inputval = mobinputval;
	}
	if(inputval != "") {
		window.location = "<?php echo $config->urls->root ?>search-results/?q="+inputval;
	}
}

function showSearch() {
	if($("#searchbox").css("display") == "inline-block") {
		cancelSearch();
	}
	else {
		$("#searchbox, #searchbtn").css("display", "inline-block");
		$("#searchbox").animate({
			width: "250px"
		}, 500);
		$("#searchbox").focus();
	}
}

function cancelSearch() {
	$("#searchbox, #searchbtn").fadeOut(200);
	setTimeout(function() {
		$("#searchbox").css("width","50px");
		$("#searchbox, #searchbtn").hide();
	},300);
}

//wishlist function
function addToWishlist(prodid) {
	var targ = "<?php echo $config->urls->root ?>post/wishlist-add.php";
	var sendstring = "func=addToWishlist&prodid=" + prodid;
	var callback = "handleWishlistReply";
	genericReq(targ, sendstring, callback);
}

function handleWishlistReply() {
	alert(reply);
	reloadPage();
}

function checkIfFormInputMandatory(inputParentDiv) {
	if($(inputParentDiv).hasClass("mand")) {
		return true;
	}
	return false;
}

function submitForm(buttonElem) {
	var formparentelem = $(buttonElem).closest("fieldset");
	var inputters = [];
	//get input vals
	var robotfld = $(formparentelem).find(".robotfield");
	if(robotfld.length > 0) {
		if($(robotfld).val() != "") { //this is a hidden field that has been filled in by a robot
			return false;
		}
	}
	var userMasterEmailAddr = "";
	window.numfails = 0;
	$(formparentelem).find("input").each(function() {
		var parentDiv = $(this).closest(".form-parent-div");
		var inputParentDiv = $(this).closest(".form-input-parent");
		var isMandatory = checkIfFormInputMandatory(inputParentDiv);
		if($(parentDiv).css("display") != "none") { //ignore if this parent div isn't displayed
			var thisin = new Answer();
			thisin.fieldtype = $(this).attr("type");
			if (thisin.fieldtype == "email") {
				thisin.val = $(this).val();
				thisin.fieldlabel = $(this).attr("data-label");
				if (validateEmailAddress(thisin.val) == false) {
					alert("Please enter a valid email address");
					window.numfails++;
					return false;
				}
			}
			else if (thisin.fieldtype == "checkbox" || thisin.type == "radio") {
				if($(this).is(":checked")) {
					thisin.val = "Yes";
				}
				thisin.fieldlabel = $(this).attr("data-label");
			}
			else if (thisin.fieldtype == "text" || thisin.fieldtype == "hidden") {
				thisin.val = encodeURIComponent(cleanMSWord($(this).val()));
				thisin.fieldlabel = $(this).attr("data-label");
			}
			if(isMandatory === true && thisin.val === "") {
				alert("Please fill out all required fields");
				$(this).addClass("redborder");
				window.numfails++;
				return false;
			}
			else {
				$(this).removeClass("redborder");
				inputters.push(thisin);
			}
			if($(this).attr("data-useremail") == "1") {
				userMasterEmailAddr = thisin.val;
			}
		}
	});
	if(window.numfails > 0) { return false; }
	//get textarea vals
	$(formparentelem).find("textarea").each(function() {
		var parentDiv = $(this).closest(".form-parent-div");
		var inputParentDiv = $(this).closest(".form-input-parent");
		var isMandatory = checkIfFormInputMandatory(inputParentDiv);
		if($(parentDiv).css("display") != "none") { //ignore if this parent div isn't displayed
			var thisin = new Answer();
			thisin.fieldtype = "textarea";
			thisin.fieldlabel = $(this).attr("data-label");
			thisin.val = encodeURIComponent(cleanMSWord($(this).val()));
			if(isMandatory === true && thisin.val === "") {
				alert("Please fill out all required fields");
				$(this).addClass("redborder");
				window.numfails++;
				return false;
			}
			else {
				$(this).removeClass("redborder");
				inputters.push(thisin);
			}
		}
	});
	if(window.numfails > 0) { return false; }
	//get select vals
	$(formparentelem).find("select").each(function() {
		var parentDiv = $(this).closest(".form-parent-div");
		var inputParentDiv = $(this).closest(".form-input-parent");
		var isMandatory = checkIfFormInputMandatory(inputParentDiv);
		if($(parentDiv).css("display") != "none") { //ignore if this parent div isn't displayed
			var thisin = new Answer();
			thisin.fieldtype = "select";
			thisin.fieldlabel = $(this).attr("data-label");
			thisin.val = encodeURIComponent(cleanMSWord($(this).val()));
			if(isMandatory === true && thisin.val === "") {
				alert("Please fill out all required fields");
				$(this).addClass("redborder");
				window.numfails++;
				return false;
			}
			else {
				$(this).removeClass("redborder");
				inputters.push(thisin);
			}
		}
	});
	if(window.numfails > 0) { return false; }

	//send
	var targ = "<?= $config->urls->root ?>post/"+$(formparentelem).attr("data-formtarget");
	var sendstring = "formpageid="+$(formparentelem).attr("data-formid")+
		"&userMasterEmail="+userMasterEmailAddr+"&answers="+JSON.stringify(inputters);
	var callback = "formreturn";
	genericAjax(targ,sendstring).done(formreturn);
}

function formreturn(data) { //data is return from ajax call page
	var jsob = JSON.parse(data);
	if(jsob.error == 1) {
		alert(jsob.message);
	}
	else {
		if(jsob.action == 'pagechange') {
			window.location = jsob.url;
		}
		else if(jsob.action == 'alertThenPagechange') {
			alert(jsob.message);
			window.location = jsob.url;
		}
		else if(jsob.action == 'alertToHome') {
			alert(jsob.message);
			window.location = jsob.url;
		}
		else if(jsob.action == "alert") {
			alert(jsob.message);
		}
		else if(jsob.action == "alertRefresh") {
			alert(jsob.message);
			window.location.reload();
		}
		if(jsob.console != "") {
			console.log(jsob.console);
		}
	}
}

// Answer prototype
function Answer() {
	this.fieldtype = "";
	this.val = "";
	this.fieldid = 0; //not really used anymore unless for targeting page save
	this.fieldlabel = "";
}

function isInt(value) {
	var x;
	if (isNaN(value)) {
		return false;
	}
	x = parseFloat(value);
	return (x | 0) === x;
}

function cleanMSWord(text) {
	var s = text;
	s = s.replace(/[\u2018|\u2019|\u201A]/g, "\'"); // smart single quotes and apostrophe
	s = s.replace(/[\u201C|\u201D|\u201E]/g, "\'"); // smart double quotes - make single to avoid breaking json
	s = s.replace(/\u2026/g, "..."); // ellipsis
	s = s.replace(/[\u2013|\u2014]/g, "-"); // dashes
	s = s.replace(/\u02C6/g, "^"); // circumflex
	s = s.replace(/\u2039/g, ""); // open angle bracket
	s = s.replace(/\u2022/g, "-"); // replace bullet with dash
	s = s.replace(/[\u02DC|\u00A0]/g, " "); // spaces
	s = s.replace(/</g, ""); // less than
	s = s.replace(/>/g, ""); // greater than
	s = s.replace(/\n/g, "\\n"); //line break
	return s;
}

function showloader() {
	$("#loadholdblanket").hide();
}

function hideloader() {
	$("#loadholdblanket").hide();
}

function doNothing() { }

<?php if($user->isSuperuser()) { ?>
//function to fill info into form automatically for superusers only
function testFillForm(elem) {
	var form = $(elem).closest("form");
	$(form).find("input").each(function() {
		if($(this).attr("type") == "email") {
			$(this).val("archives2@hotmail.com");
		}
		else {
			if($(this).hasClass("robotfield") === false) {
				$(this).val("Test "+$(this).attr("data-label"));
			}
		}
	});
	$(form).find("textarea").each(function() {
		$(this).val("This is my test \nAnd this is line 2");
	});
	$(form).find("select").each(function() {
		$(this).find("option").each(function() {
			$(this).attr("selected","selected");
		});
	});
}
<?php } ?>

</script>
<?php
if($page->id == 1 and $pageInstance->showSchema === true) { //only show on the home page
	echo $page->schema;
}
?>
</head>
<body class="<?php echo $page->template . " " . $page->name . " " . $page->classes ?>" data-pageid="<?php echo $page->id; ?>">
	<!-- mobile slide menu -->
	<div id="offcanvas-reveal" class="uk-offcanvas" data-uk-offcanvas="mode: push; flip:true; overlay:true" >
		<div class="uk-offcanvas-bar">
			<ul class="uk-nav">
				<li>
					<a href="javascript:void(0);" data-uk-toggle="target: #offcanvas-reveal" style="text-align:right;"><i class="fa fa-close"></i></a>
				</li>
				<?php //this is the mobile menu
				/** @var WalkMenu $mainMenuPage */
				$mainMenuPage = $pages->get(1296); //page id of the Main Menu
				echo $mainMenuPage->renderFrontend($page); //pass the current rendered page as well
				if($pageInstance->hasSearchFunction === true) {
				?>
					<li id="mobilesearchholder"><!-- mobile search icon / box -->
						<i class="fa fa-search"></i>
						<input type="text" id="mobile-searchbox"/>
						<button id="mobile-searchbtn" onclick="doSearch();" type="button">Search</button>
					</li>
				<?php } ?>
			</ul>
		</div>
	</div>
	<div class="">
		<div id="cartPopupHolder">
			<div id="cartPopup">
				<p id="cartPopupMsg">Cart updated!</p>
			</div>
		</div>
		<div id="loadholdblanket">
			<div id="loadhold">
				<img src="<?php echo $config->urls->assets; ?>images/loader.gif" alt="Loading" />
				<br/>
			</div>
		</div>
		<div id="uploadBlanket">
			<div id="upload_div" class="uploadPopup">
				<h3>Image Upload</h3>
				<p>File size must be less than 350kb</p>
				<p>You must have online publishing permission or full ownership of this image</p>
				<p>File types (jpg, png, gif)</p><br/><br/>
				<form id="attach_file" method="post" target="upload_target" action="<?php echo $config->urls->root; ?>post/attach-upload.php" enctype="multipart/form-data">
					<input type="file" name="uploadFile" id="uploadFile" />
					<input type="hidden" name="uploadType" id="uploadType" value="" />
					<input type="hidden" name="uploadTargetFilename" id="uploadTargetFilename" value="" />
					<br/><br/><br/>
					<input type="submit" id="uploadsubmit" onclick="closeUpload();" value="Submit" />
					<button type="button" id="uploadcancel" onclick="cancelUpload();">Cancel</button>
				</form>
			</div>
			<!--THIS IS ONLY USED FOR THE AJAX UPLOAD CALLBACK FUNCTION - NEVER DISPLAYED -->
			<iframe name="upload_target" id="upload_target"> </iframe>
			<!-- END OF THIS BIT -->
		</div>

		<?php
		/** @var Cart $cart */
		?>

		<nav class="uk-navbar-container uk-navbar" data-uk-navbar>
			<div class="uk-navbar-left">
				<a id="logo" class="uk-logo uk-navbar-item" href="<?php echo $config->urls->root ?>">
					<img src="<?php echo $settings->header_logo->url; ?>" alt="<?php echo $pages->get(1)->site_master_title; ?> logo" />
				</a>
			</div>
			<div class="uk-navbar-right">
				<ul class="uk-navbar-nav" id="desktop-menu">
				<?php //this is the full desktop menu
					/** @var WalkMenu $mainMenuPage */
					//$mainMenuPage = $pages->get(1296); //page id of the Main Menu
				//	echo $mainMenuPage->renderFrontend($page); //pass the current rendered page as well
				?>
					<li> <a href="#"> home </a> </li>
					<li> <a href="#"> How it Works </a> </li>
					<li> <a href="#"> Tools </a> </li>
					<li> <a href="#"> Contact </a> </li>

				</ul>
			</div>
			<?php
			if($pageInstance->hasSearchFunction === true) {
			?>
				<div id="searchTopHolder"><!-- desktop search icon -->
					<div class="fontawe" id="searchHolder">
						<i id="searchfa" class="fa fa-search" onclick="showSearch();"></i>
						<input type="text" id="searchbox"/>
						<button id="searchbtn" onclick="doSearch();" type="button">Search</button>
					</div>
				</div>
			<?php
			}
			?>
			<div class="uk-navbar-right" id="mobilehamburger">
				<span class="uk-button" data-uk-toggle="target: #offcanvas-reveal">
					<i class="fa fa-bars"></i>
				</span>
			</div>
		</nav>
		<div class="header-sticky-padding"></div>
		<script src="//use.fontawesome.com/2e7f521c3f.js"></script>
