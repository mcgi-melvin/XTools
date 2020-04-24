<?php
namespace ProcessWire;
/** @var Config $config */
/** @var Wire $wire */
/** @var Page $page */
/** @var Pages $pages */
/** @var User $user */
/** @var PageInstance $pageInstance */

include("chunks/init.php");
include "./chunks/dochead.php"; //all js goes in dochead, then homewrap div

header("location: ".$page->pdf_file->httpUrl);