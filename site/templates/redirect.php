<?php
namespace ProcessWire;
/** @var Config $config */
/** @var Wire $wire */
/** @var Page $page */
/** @var Pages $pages */
/** @var User $user */
/** @var PageInstance $pageInstance */
/** @var Tools $tools */

include("chunks/init.php");
$outlink = $tools->cleanRedirect($page->redirect_link);
header("location: $outlink");