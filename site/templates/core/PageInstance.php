<?php
namespace ProcessWire;
/** @var Config $config */
/** @var Wire $wire */
/** @var Page $page */
/** @var Pages $pages */

/**
 * Class PageInstance
 * @package ProcessWire
 */
class PageInstance { //this is used for the instance of loading a visible page (as compared to PW pages which can be used as subpages, chunks etc)
	/** @var array $alreadyShownOnPage List of page IDs already shown on aggregate/home pages  */
	public $alreadyShownOnPage = [];
	/** @var int $nowTime Unix time now */
	public $nowTime;
	/** @var bool $isHomepage Is this page the Home Page? */
	public $isHomepage = false;
	/** @var string $currentLink Is the link of the current page (internal URL not absolute), set blank initially - used in JS */
	public $currentLink = "";
	/** @var Page $pageSection Used on article pages to know which section is active (if articles can have more than one section) */
	public $pageSection;
	/** @var bool $paginateAjax True means using the API with ajax rather than page reload (section.php) */
	public $paginateAjax = false;
	/** @var bool $hasSearchFunction */
	public $hasSearchFunction = false;
	/** @var bool $showSchema */
	public $showSchema = true;
	/** @var bool $hasPaypal */
	public $hasPaypal = false;
	/** @var bool $hasCart */
	public $hasCart = false;
	/** @var bool $hasWishlist */
	public $hasWishlist = false;
	/** @var SiteSettings $settings Stored reference to the global $settings var */
	public $settings;
	/** @var Tools $tools Stored reference to the global $tools var */
	public $tools;

	function __construct() {
		global $wire;
		$this->nowTime = time();
		$this->currentLink = $wire->page->url;
		if($wire->page->template == "home") {
			$this->isHomepage = true;
		}
	}

	public function pushToAlreadyShown($addThisID) { //pushes an ID to the array of IDs that already have been shown on this page
		if(!in_array($addThisID,$this->alreadyShownOnPage)) {
			$this->alreadyShownOnPage[] = $addThisID;
		}
	}

	public function getPageSection() {
		global $wire, $tools;
		if(isset($_GET['section'])) {
			$this->pageSection = $wire->pages->get(intval($_GET['section'])); //page object
		}
		else {
			if($wire->page->section != "") {
				if ($tools->isSingleObject($wire->page->section)) {
					$this->pageSection = $wire->page->section;
				}
				else {
					$this->pageSection = $wire->page->section->first();
				}
			}
		}
		if(is_object($this->pageSection)) {
			return $this->pageSection;
		}
		else {
			return NULL;
		}
	}

}