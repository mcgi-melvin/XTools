function genericAjaxAdmin(targ, sendstring) {
	return $.ajax(targ, {
		type: 'POST',
		data: sendstring
	});
	//usage - genericAjaxAdmin(targ,sendstring).done(showPortalPopupContents);
}

function reloadPage() {
	window.location.href = window.location.pathname + window.location.search + window.location.hash;
}

function goToEditPage(editpageid) {
	window.location = config.urls.admin + "page/edit/?id=" + editpageid;
}

function alerter(data) {
	alert(data);
}

function doWCimgFilter(type) {
	var terms = $("#sqpg-search-terms").val();
	var searchtype = $("#sqpg-search-type").val();
	if(terms != "") {
		if(type == "cand") {
			window.location = config.urls.admin + "image-manager/?st="+searchtype+"&t=" + terms;
		}
	}
}

function walkCloseModal() {
	window.location.reload();
}

function doNothing() { }

$(document).ready(function() {
	$(".wcmagnific").magnificPopup({type:'image'});
});
