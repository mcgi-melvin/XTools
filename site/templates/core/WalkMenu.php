<?php
namespace ProcessWire;

/**
 * Class WalkMenu
 * @package ProcessWire
 *
 */
class WalkMenu extends Page {

	/**
	 * @param Page $onpage The currently viewed page (eg home page)
	 * @return string
	 */
	public function renderFrontend(Page $onpage) {
		/** @var WalkMenuItem $menuItem */
		$out = "";
		foreach($this->children() as $thisItem) {
			$menuItem = new WalkMenuItem();
			$menuItem->setFromPage($thisItem, $onpage);
			$out .= $menuItem->renderFrontend();
		}
		return $out;
	}

	public function renderSearch(int $isMobile, PageInstance $pageInstance) {
		$out = "";
		if($pageInstance->hasSearchFunction === true) { //mobile version
			if ($isMobile === 1) {
				$out .= '<div class="nav-overlay uk-navbar-right">
					        <a class="uk-navbar-toggle" uk-search-icon uk-toggle="target: .nav-overlay; animation: uk-animation-fade" href="#"></a>
						</div>
    					<div class="nav-overlay uk-navbar-left uk-flex-1" hidden>
					        <div class="uk-navbar-item uk-width-expand">
            					<form class="uk-search uk-search-navbar uk-width-1-1" onsubmit="doSearch();">
                					<input class="uk-search-input" type="search" placeholder="Search..." autofocus>
            					</form>
        					</div>
        					<a class="uk-navbar-toggle" uk-close uk-toggle="target: .nav-overlay; animation: uk-animation-fade" href="#"></a>
					    </div>';
			}
			else { //not the mobile version
				$out .= '<div id="searchTopHolder">
           					<a class="uk-navbar-toggle" uk-search-icon href="#"></a>
           					<div class="uk-drop" uk-drop="mode: click; pos: left-center; offset: 0">
               					<form class="uk-search uk-search-navbar uk-width-1-1" onsubmit="doSearch();">
                    				<input class="uk-search-input" type="search" placeholder="Search..." autofocus>
                				</form>
            				</div>
						</div>';
			}
		}
		return $out;
	}

}