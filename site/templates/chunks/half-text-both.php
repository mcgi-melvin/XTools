<?php namespace ProcessWire;
/** @var PageTableContentBlock $block **/
/** @var Page $page **/
if(!isset($block)) { $block = $page; }
?>

<!-- Two column grid (no gutter, both text) -->
<div class="full-width uk-child-width-1-2@m uk-grid-collapse <?php if($block->addclasses != "") echo $block->addclasses; ?>" data-uk-grid <?php if($block->divid != "") { echo 'id="'.$block->divid.'"'; } ?> data-chunk="half-text-both" >
	<div class="bg-darkgrey text-white">
		<div class="inside-left-half">
			<div class="inside-left-content wc-padding-vertical">
				<?= $block->body_content ?>
			</div>
		</div>
	</div>
	<div class="bg-grey no-content">
		<div class="inside-right-half">
			<div class="inside-right-content wc-padding-vertical">
				<?= $block->body_content3 ?>
			</div>
		</div>
	</div>
</div>
