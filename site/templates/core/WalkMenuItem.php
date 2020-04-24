<?php
namespace ProcessWire;


/**
 * Class WalkMenuItem
 * @package ProcessWire
 *
 * @property Page $menu_page
 * @property string $anchor_id
 * @property string $redirect_link
 * @property int $redir_blankTarget
 * @property int $menu_hasLevel2
 * @property string $selectorString String used for PW selector for child elements (optional)
 *
 * @property int $menu_lvl2UseChildrenOfParent If checked this uses real pages of parent page selected, if unchecked uses subpages of this menu item
 * @property int $menu_showChevron1
 * @property int $menu_lvl2UseImg
 * @property int $menu_lvl2ImgLeft
 * @property int $menu_hasLevel3
 * @property int $menu_showChevron2
 *
 * @property int $menu_lvl3UseImg
 * @property int $menu_lvl3ImgLeft
 * @property int $menu_lvl3UseChildrenOfParent If checked this uses real pages of parent page selected, if unchecked uses subpages of this menu item
 */
class WalkMenuItem extends Page {
	/** @var Tools $tools  */
	protected $tools;
	/** @var string $link The final URL link for this menu item */
	public $link = "";
	/** @var string $linkAppend String to append to the link (eg GET variables) */
	protected $linkAppend = "?";
	/** @var string $js Any Javascript to append to this link item */
	protected $js = "";
	/** @var string $itemType Will be either normal or anchor */
	protected $itemType; //normal, anchor
	/** @var int $currentRenderedPageId The ID of the page we are rendering on (eg home page = 1) */
	protected $currentRenderedPageId;
	/** @var string $inactiveClass  */
	protected $inactiveClass = "inactiveMenuTop";
	/** @var string $inactiveText  */
	protected $inactiveText = "inactiveMenu";
	/** @var string $linkTitle The actual link Title for the main menu item */
	protected $linkTitle = "";
	/** @var bool $isTopLevelItem Will be set to false for submenu items */
	public $isTopLevelItem = true;

	/**
	 * WalkMenuItem constructor.
	 * @param Template|null $tpl
	 */
	function __construct(Template $tpl = null) {
		parent::__construct($tpl);
		$this->tools = new Tools();
		if($this->parent->template == "menu_item") {
			$this->isTopLevelItem = false;
		}
	}

	/**
	 * @return string
	 */
	public function renderFrontend() {
		$out = "";
		$chev = "";
		$targblank = "";
//		if($this->menu_hasLevel2 == 1 and $this->menu_showChevron1 == 1) {
//			$chev = "<i class='fa fa-chevron-down'></i>";
//		}
		if($this->redir_blankTarget == 1 and $this->redirect_link != "") {
			$targblank = "target='_blank'";
		}
		$out .= "<li><a href='$this->link' $this->js $targblank>";
//		$out .= "<span class='menuTopBar ".$this->menu_page->name." $this->inactiveClass'></span>;
		$out .= "<span class='menuTitle ".$this->menu_page->name."_text $this->inactiveText' data-name='".$this->menu_page->name."'>$this->linkTitle $chev</span>";
//		$out .= "<span class='menuUnderline ".$this->menu_page->name." $this->inactiveClass'></span>";
		$out .= "</a>";

		//now create dropdowns for any that need it
		if($this->menu_hasLevel2 == 1) {
			$subs = $this->getSubmenuPages($this, $this->isTopLevelItem);
			$out .= $this->createSubMenu($subs);
		}
		$out .= "</li>";
		return $out;
	}

	public function getSubmenuPages($parentPage, bool $isTopLevel) {
		if($isTopLevel === true and $parentPage->menu_lvl2UseChildrenOfParent == 1) { //use subpages of main menu item
			$subs = $this->wire->pages->find("parent=" . $this->menu_page->id); //will hold an array of Pages
		}
		else if($isTopLevel === false and $parentPage->menu_lvl3UseChildrenOfParent == 1) { //this is a secondary level of parent
			$subs = $this->wire->pages->find("parent=" . $parentPage->id); //will hold an array of Pages
		}
		else if($parentPage->selectorString != "") {
			$subs = $this->wire->pages->find($this->tools->parser->parse($parentPage->selectorString, $parentPage));
		}
		else { //use subpages of this menu element page (pages created within tools/menus/ etc
			$subs = $this->wire->pages->find("parent=".$parentPage->id);
		}
		return $subs;
	}

	public function createSubMenu($subs) {
		$out = "";
		if(count($subs) > 0) {
			$out .= "<div class='uk-navbar-dropdown'>";
			$myname = "";
			if($this->menu_lvl2UseChildrenOfParent == 1) {
				$myname = $this->menu_page->name;
			}
			else {
				$myname = $this->name;
			}
			$out .= "<ul class='uk-nav uk-navbar-dropdown-nav'>";
			foreach($subs as $subcat) {
				if($subcat instanceof WalkMenuItem or $subcat instanceof WalkMenuItemSub) {
					$subtargetpage = $subcat->menu_page;
					$subcat->getItemType();
					$subcat->getLink();
				}
				else {
					$subtargetpage = $subcat;
					$subcat->link = $subcat->httpUrl;
				}
				$subcathref = $subcat->link;
				$out .= "<li>";
				if($subcathref != "") {
					$out .=	"<a href='$subcathref'>";
				}
				else {
					$out .=	"<a href='javascript:void(0);'>";
				}
				$catimghtml = "";
				if($subcat->menu_lvl2UseImg == 1) {
					$catimg = $this->tools->getFirstImageFromPage($subtargetpage, "hero_image");
					if ($catimg) {
						$catimgfile = $catimg->size(60, 60);
						$catimghtml = "<div class='shopdropdiv'>";
						$catimghtml .= "<img src='" . $catimgfile->httpUrl . "' alt='" . $subcat->title . "' title='" . $subcat->title . "' />";
						$catimghtml .= "</div>";
					}
					if($subcat->menu_lvl2ImgLeft == 1) {
						$out .= $catimghtml;
					}
				}
				$out .= "<div><p>".$subcat->title . " ";
				if($subcat->menu_showChevron2 == 1) {
					$out .= "<i class='fa fa-chevron-right'></i>";
				}
				$out .= "</p></div>";
				if($subcat->menu_lvl2UseImg == 1 and $subcat->menu_lvl2ImgLeft != 1) {
					$out .= $catimghtml;
				}
				$out .= "</a>";
//				if($subcat instanceof WalkMenuItem or $subcat instanceof WalkMenuItemSub) { //this is a menu item - check third menu
//					$substhree = $this->getSubmenuPages($subcat, $this->isTopLevelItem);
//				}
//				else if($subcat->menu_hasLevel3 == 1) { //we are using third menu here if possible
//					$substhree = $this->getSubmenuPages($subcat, $this->isTopLevelItem);
//				}
//				if(isset($substhree) and count($substhree) > 0) {
//					$out .= $this->createThirdSubmenu($substhree);
//				}
				$out .= "</li>";
			}
			$out .= "</ul>";
			$out .= "</div>";
		}
		return $out;
	}

//	public function createThirdSubmenu($subs) {
//		$out = "";
//		if(count($subs) > 0) {
//			$out .= "<div class='submenuholderthree'>";
//			$myname = "";
//			if ($this->menu_lvl2UseChildrenOfParent == 1) {
//				$myname = $this->menu_page->name;
//			}
//			else {
//				$myname = $this->name;
//			}
//			$out .= "<ul class='sub-menuthree " . $myname . "_submenu'>";
//			foreach ($subs as $subcat) {
//				if($subcat instanceof WalkMenuItemSub or $subcat instanceof WalkMenuItem) {
//					$subcat->getItemType();
//					$subcat->getLink();
//				}
//				else {
//					$subcat->link = $subcat->httpUrl;
//				}
//				$subcathref = $subcat->link; //if this is a direct link to page
//				$out .= "<li class='subcatmenuitem'><a href='$subcathref'>";
//				$catimghtml = "";
//				if($subcat instanceof WalkMenuItem) {
//					$lname = "menu_lvl2UseImg";
//					$lnameimg = "menu_lvl2ImgLeft";
//					$chev = "menu_showChevron2";
//				}
//				else {
//					$lname = "menu_lvl3UseImg";
//					$lnameimg = "menu_lvl3ImgLeft";
//					$chev = "menu_showChevron3";
//				}
//				if ($this->$lname == 1) {
//					$catimg = $this->tools->getFirstImageFromPage($subcat, "hero_image");
//					if ($catimg) {
//						$catimgfile = $catimg->size(60, 60);
//						$catimghtml = "<div class='shopdropdiv'>";
//						$catimghtml .= "<img src='" . $catimgfile->httpUrl . "' alt='" . $subcat->title . "' title='" . $subcat->title . "' />";
//						$catimghtml .= "</div>";
//					}
//					if ($this->$lnameimg == 1) {
//						$out .= $catimghtml;
//					}
//				}
//				$out .= "<div class='shopdroptextcontent'><p>" . $subcat->title . " ";
//				if ($this->$chev == 1) {
//					$out .= "<i class='fa fa-chevron-right'></i>";
//				}
//				$out .= "</p></div>";
//				if ($this->$lname == 1 and $this->$lname != 1) {
//					$out .= $catimghtml;
//				}
//				$out .= "</a>";
//				$out .= "</li>";
//			}
//			$out .= "</ul>";
//			$out .= "</div>";
//		}
//		return $out;
//	}

	/**
	 * @param Page $incomingMenuItemPage This is the menu repeater item page passed from WalkMenu
	 * @param Page $onPage This is the rendered page eg Home page
	 */
	public function setFromPage(Page $incomingMenuItemPage, Page $onPage) {
		if(is_object($incomingMenuItemPage)) {
			$vars = get_object_vars($incomingMenuItemPage);
			foreach($vars as $var => $value) {
				$this->$var = $value;
			}
		}
		$this->currentRenderedPageId = $onPage->id;
		//each section name is a class with its color injected (in dochead to allow PHP injection)
		if($onPage->url == $this->menu_page->url) { //we are on the page that this link goes to - show as active
			$this->inactiveClass = "";
			$this->inactiveText = "";
		}
		$this->doPreparation();
	}

	/**
	 * Like an init() function for this menu item
	 */
	protected function doPreparation() {
		$this->itemType = "normal";
		$this->getItemType();
		$this->getLink();
	}

	/**
	 * Is this an anchor or normal href item?
	 */
	protected function getItemType() {
		if($this->menu_page->id != "" and $this->anchor_id != "") {
			$this->itemType = "anchor";
			$this->linkAppend .= "target=".$this->anchor_id."&targPageId=".$this->menu_page->id;
		}
	}

	/**
	 * Sets the main link href, javascript and title attributes for this main menu item
	 */
	protected function getLink() {
		$link = "";
		$js = "";
		if($this->redirect_link != "") { //an explicit redirect link overrides a selected page
			$link = $this->tools->cleanRedirect($this->redirect_link);
		}
		else if($this->menu_page->id != "") { //we have a page, use their URL
			$link = $this->menu_page->httpUrl;
			$this->linkTitle = $this->menu_page->title;
			if($this->itemType == "anchor") { //replace with anchor link and js
				if($this->currentRenderedPageId == $this->menu_page->id) { //we are on the correct target page already
					$js = "onclick='jump(&apos;".$this->anchor_id."&apos;);'";
					$link = "javascript:void(0);";
				}
				else { //we are on another page, use the href with target appended
					$link .= $this->linkAppend;
				}
			}
			else if($this->redirect_link == "none") { //we do not want this link to be clickable (eg for top level menu)
				$link = "javascript:void(0);";
			}
		}
		$this->js = $js;
		$this->link = $link;
		if($this->title != "") { //overrides the Title using the text field if required
			$this->linkTitle = $this->title;
		}
	}

}