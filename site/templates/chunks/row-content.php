<?php namespace ProcessWire;
/** @var PageTableContentBlock $block **/
/** @var Page $page **/
if(!isset($block)) { $block = $page; }
?>
<div class="full-width wc-padding-vertical <?php if($block->addclasses != "") echo $block->addclasses; ?>" <?php if($block->divid != "") { echo 'id="'.$block->divid.'"'; } ?> data-chunk="row-content">
	<div class="centreme">
		<div class="centremeinner ">
			<?= $block->body_content ?>
		</div>
	</div>
</div>