<?php namespace ProcessWire;
/** @var PageTableContentBlock $block **/
/** @var Page $page **/
if(!isset($block)) { $block = $page; }
?>

<div class="full-width uk-child-width-1-2@m uk-grid-collapse <?php if($block->addclasses != "") echo $block->addclasses; ?>" data-uk-grid <?php if($block->divid != "") { echo 'id="'.$block->divid.'"'; } ?> data-chunkname="half-image-both-text-left">
	<!-- first column is image with text overlay -->
	<div class="bg-grey uk-position-relative column-image-background text-white" style="background-image: url('<?= $block->hero_image->first->url ?>');">
		<div class='bg-grey-rgba'></div>
		<div class="inside-left-half uk-position-relative zind2">
			<div class="inside-left-content wc-padding-vertical">
					<?= $block->body_content ?>
			</div>
		</div>
	</div>
	<!-- second column is just an image that responds in height to text content of sibling column -->
	<div class="bg-white column-image-background" style="background-image: url('<?php echo $block->hero_image2->first->url ?>');"></div>
</div>
