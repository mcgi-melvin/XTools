<?php
namespace ProcessWire;
/** @var Page $page */
if(!isset($block)) { //some pages eg person template, use a different page as their hero content
	$block = $page;
}
?>
<div class="generic-hero full-width <?php if($block->addclasses != "") echo $block->addclasses; ?>" data-chunk="generic-hero-parallax" <?php if($block->divid != "") { echo 'id="'.$block->divid.'"'; } ?>>
	<div class="hero-image-hold uk-inline uk-height-1-1 uk-flex uk-background-cover" uk-parallax="bgy: -400" style="background-image: url('<?= $block->hero_image->first->url ?>');">
		<div class="uk-overlay uk-position-bottom hero-overlay uk-height-1-1 uk-padding-remove" >
			<div class="uk-flex uk-flex-middle uk-height-1-1 generic-hero-overlay-content">
				<div class="generic-hero-content">
					<?= $block->body_content ?>
				</div>
			</div>
		</div>
	</div>
</div>