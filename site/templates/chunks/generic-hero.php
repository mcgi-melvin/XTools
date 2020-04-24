<?php
namespace ProcessWire;
/** @var Page $page */
if(!isset($block)) {
	$block = $page;
}
?>
<div class="generic-hero full-width <?php if($block->addclasses != "") echo $block->addclasses; ?>" data-chunk="generic-hero" <?php if($block->divid != "") { echo 'id="'.$block->divid.'"'; } ?>>
	<div class="hero-image-hold uk-inline uk-height-1-1">
		<img class="hero-image uk-height-1-1" src="<?= $block->hero_image->first->url ?>" alt="<?= $block->title ?> hero image" />
		<div class="uk-overlay uk-position-bottom hero-overlay uk-height-1-1 uk-padding-remove" >
			<div class="uk-flex uk-flex-middle uk-height-1-1 generic-hero-overlay-content">
				<div class="generic-hero-content">
					<?= $block->body_content ?>
				</div>
			</div>
		</div>
	</div>
</div>