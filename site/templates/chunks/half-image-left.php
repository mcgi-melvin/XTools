<?php namespace ProcessWire;
/** @var PageTableContentBlock $block **/
/** @var Page $page **/
if(!isset($block)) { $block = $page; }
?>

<div class="full-width uk-child-width-1-2@m uk-grid-collapse <?php if($block->addclasses != "") echo $block->addclasses; ?>" data-uk-grid <?php if($block->divid != "") { echo 'id="'.$block->divid.'"'; } ?> data-chunk="half-image-left" >
	<div class="column-image-background" style="background-image: url('<?php echo $block->hero_image->first->url ?>');"></div>
	<div class="">
		<div class="inside-right-half">
			<div class="inside-right-content wc-padding-vertical">
				<?= $block->body_content ?>
			</div>
		</div>
	</div>
</div>
