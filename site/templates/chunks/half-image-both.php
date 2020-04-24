<?php namespace ProcessWire;
/** @var PageTableContentBlock $block **/
/** @var Page $page **/
if(!isset($block)) { $block = $page; }
?>

<div class="full-width uk-child-width-1-2@m uk-grid-collapse <?php if($block->addclasses != "") echo $block->addclasses; ?>" data-uk-grid <?php if($block->divid != "") { echo 'id="'.$block->divid.'"'; } ?> data-chunk="half-image-both" >
	<div class="bg-white">
		<div class="column-image-hold uk-width-1-1 uk-height-1-1">
			<img src="<?php echo $block->hero_image->first->url ?>" />
		</div>
	</div>
	<div class="bg-white">
		<div class="column-image-hold uk-width-1-1 uk-height-1-1">
			<img src="<?php echo $block->hero_image2->first->url ?>" />
		</div>
	</div>
</div>