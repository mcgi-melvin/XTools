<?php
namespace ProcessWire;
/** @var Config $config */
/** @var Wire $wire */
/** @var Page $page */
/** @var Pages $pages */
/** @var User $user */

//this page should be called at the top of every template
error_reporting(0);

$pageInstance = new PageInstance(); //stores custom properties for this web page, eg already shown articles for aggregates etc

//some site settings here
$pageInstance->paginateAjax = false;
$pageInstance->hasSearchFunction = false;
$pageInstance->showSchema = true;
$pageInstance->hasCart = false;
$pageInstance->hasPaypal = false;
$pageInstance->hasWishlist = false;

/** @var SiteSettings $settings */
$settings = $wire->pages->get("/tools/settings/"); //get the remaining settings from the CMS Settings page
$pageInstance->settings = $settings; //create reference for use later
$tools = new Tools();
$pageInstance->tools = $tools; //create reference for use later

$host = $_SERVER['HTTP_HOST'];
$fails = ['www.livesiteaddress.com.au', 'livesiteaddress.com.au'];
if(in_array($host,$fails) and $page->name != "coming-soon") {
	header("location: ".$config->urls->root."coming-soon/");
	exit;
}
else if(!in_array($host,$fails) and $page->name == "coming-soon") {
	header("location: ".$config->urls->root);
	exit;
}

$articleSection = "";
if($page->template == "article") {
	$pageInstance->getPageSection();
	$articleSection = "";
	if(is_object($pageInstance->pageSection)) {
		$articleSection = $pageInstance->pageSection->name;
	}
}

if(isset($_SESSION['clientID']) and $_SESSION['clientID'] > 0) {
	$clientArray = $wire->pages->find("template=client, id=".intval($_SESSION['clientID']));
	if(count($clientArray) === 1) {
		/** @var Client $client */
		$client = $clientArray->first();
		$client->isLoggedIn = true;
	}
}

if($pageInstance->hasCart === true) {
	$cart = new Cart;
	if (isset($_SESSION['cart'])) {
		/** @var Cart $cart */
		$cart = $_SESSION['cart'];
		if($cart == "") {
			$cart = new Cart;
		}
		else {
			$cart->getCartTotals();
		}
	}
	else {
		$_SESSION['cart'] = new Cart();
		$cart = new $_SESSION['cart'];
	}
}
if($pageInstance->hasWishlist === true) {
	$wishlist = new Wishlist;
	if (isset($_SESSION['wishlist'])) {
		/** @var Wishlist $wishlist */
		$wishlist = $_SESSION['wishlist'];
	}
	else {
		$_SESSION['wishlist'] = new Wishlist;
		$wishlist = $_SESSION['wishlist'];
	}
}