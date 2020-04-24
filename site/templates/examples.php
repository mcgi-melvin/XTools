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

//this page should only be shown to logged in user
if(!$user->isLoggedin()) {
	throw new Wire404Exception();
}

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

<!-- Two column grid example (no gutter, both text) -->
<div class="full-width uk-child-width-1-2@m uk-grid-collapse" data-uk-grid>
	<div class="bg-darkgrey text-white">
		<div class="inside-left-half">
			<div class="inside-left-content wc-padding-vertical">
				<?= $page->body_content2 ?>
			</div>
		</div>
	</div>
	<div class="bg-grey">
		<div class="inside-right-half">
			<div class="inside-right-content wc-padding-vertical">
				<?= $page->body_content3 ?>
			</div>
		</div>
	</div>
</div>
<!-- End Two column grid example -->

<!-- Two column grid example (no gutter, image and image/textoverlay) -->
<div class="full-width uk-child-width-1-2@m uk-grid-collapse" data-uk-grid>
	<!-- first column is image with text overlay -->
	<?php
		if($page->hero_image2 != "") {
			$colbg = "background-image: url('".$page->hero_image2->first->url."');";
		}
		?>
	<div class="bg-grey uk-position-relative col-has-background-image text-white" style="<?= $colbg ?>">
		<div class='bg-grey-rgba'></div>
		<div class="inside-left-half uk-position-relative zind2">
			<div class="inside-left-content wc-padding-vertical">
				<?= $page->body_content3 ?>
			</div>
		</div>
	</div>
	<!-- second column is just an image that responds in height to text content of sibling column -->
	<div class="bg-white column-image-background" style="background-image: url('<?php echo $page->hero_image2->first->url ?>');"></div>
</div>
<!-- End Two column grid example -->

<!-- Usage for image as separate <img> item inside parent div (not quite as good responsively -->
<div class="full-width uk-child-width-1-2@m uk-grid-collapse" data-uk-grid>
	<div class="bg-white">
		<div class="column-image-hold uk-width-1-1 uk-height-1-1">
			<img src="<?php echo $page->hero_image2->first->url ?>" />
		</div>
	</div>
	<div class="bg-white">
		<div class="column-image-hold uk-width-1-1 uk-height-1-1">
			<img src="<?php echo $page->hero_image2->first->url ?>" />
		</div>
	</div>
</div>

<?php $tools->renderPageTable($page, "pt_content_repeat"); //render pagetable content blocks ?>

<?php include "./chunks/footer.php"; //homewrap div is closed in footer ?>
