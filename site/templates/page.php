<?php
namespace ProcessWire;
/** @var Config $config */
/** @var Wire $wire */
/** @var Page $page */
/** @var Pages $pages */
/** @var User $user */
/** @var PageInstance $pageInstance */
/** @var Tools $tools */
/** @var SiteSettings $settings */

include "chunks/init.php";
include "./chunks/dochead.php"; //all js goes in dochead

include "./chunks/banner.php";
include "./chunks/panel.php";
include "./chunks/side-content.php";




include_once "chunks/footer.php";
