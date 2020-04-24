<?php namespace ProcessWire;
/** @var PageTableContentBlock $block **/
/** @var Page $page **/
if(!isset($block)) { $block = $page; }
?>

<div class="full-width uk-dark <?php if($block->addclasses != "") echo $block->addclasses; ?>" data-chunk="content-area-title" <?php if($block->divid != "") { echo 'id="'.$block->divid.'"'; } ?>>
  <div class="centreme">
    <div class="centremeinner">
      <div class="uk-inline uk-width-1-1 uk-height-large uk-height-1-1" style="background: #e2e2e2;">
        <div class="uk-position-center uk-padding-large uk-text-center banner-content">
          <h1 class="uk-heading-medium"><?php echo $block->title; ?></h1>
          <p class="uk-margin-medium-bottom"><?php echo $block->subheading; ?></p>
          <p class="uk-margin-small-bottom"><?php echo strip_tags($block->body_content); ?></p>
        </div>
      </div>
    </div>
  </div>
</div>
