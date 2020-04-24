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

$tools->renderPageTable($page, "pt_content_repeat"); //render pagetable content blocks

include_once "chunks/footer.php";