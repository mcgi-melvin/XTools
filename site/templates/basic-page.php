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
//this template is also used by the 404 page

include "chunks/init.php";
include "./chunks/dochead.php"; //all js goes in dochead
if($page->hero_image != "") {
	include "chunks/generic-hero.php";
}

?>
<!-- First text content section -->
<div class="full-width wc-padding-vertical">
	<div class="centreme">
		<div class="centremeinner ">
			<?= $page->body_content ?>
		</div>
	</div>
</div>
<!-- End First text content section -->

<?php include "./chunks/footer.php"; //homewrap div is closed in footer ?>
