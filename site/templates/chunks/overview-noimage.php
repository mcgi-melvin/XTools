<?php namespace ProcessWire;
/** @var PageTableContentBlock $block **/
/** @var Page $page **/
if(!isset($block)) { $block = $page; }
?>

<div class="uk-width-1-1 uk-dark <?php if($block->addclasses != "") echo $block->addclasses; ?>" data-chunk="overview-noimage" <?php if($block->divid != "") { echo 'id="'.$block->divid.'"'; } ?>>
  <div class="centreme">
    <div class="centremeinner">
      <div class="uk-child-width-expand@s uk-margin-remove uk-height-1-1" style="background: #e2e2e2;" uk-grid>
        <div class="uk-padding-small uk-position-relative uk-height-medium uk-margin-remove-top">
          <div class="uk-text-center uk-position-center uk-position-large">
            <h1 class="uk-heading-medium uk-margin-small-bottom"><?php echo $block->title; ?></h1>
            <p class="uk-margin-small-bottom"><?php echo strip_tags($block->body_content); ?></p>
          </div>
        </div>
        <div class="uk-padding-small uk-position-relative uk-height-medium uk-margin-remove-top">
          <div class="uk-text-center uk-position-center uk-position-large">
            <h1 class="uk-heading-medium uk-margin-small-bottom"><?php echo $block->title; ?></h1>
            <p class="uk-margin-small-bottom"><?php echo strip_tags($block->body_content); ?></p>
          </div>
        </div>
        <div class="uk-padding-small uk-position-relative uk-height-medium uk-margin-remove-top">
          <div class="uk-text-center uk-position-center uk-position-large">
            <h1 class="uk-heading-medium uk-margin-small-bottom"><?php echo $block->title; ?></h1>
            <p class="uk-margin-small-bottom"><?php echo strip_tags($block->body_content); ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
