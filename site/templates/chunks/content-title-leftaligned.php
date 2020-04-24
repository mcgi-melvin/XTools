<?php namespace ProcessWire;
/** @var PageTableContentBlock $block **/
/** @var Page $page **/
if(!isset($block)) { $block = $page; }
?>

<div class="full-width <?php if($block->addclasses != "") echo $block->addclasses; ?>" data-chunk="content-title-leftaligned" <?php if($block->divid != "") { echo 'id="'.$block->divid.'"'; } ?>>
  <div class="centreme">
    <div class="centremeinner">
      <div class="uk-dark uk-child-width-expand@m uk-child-width-1-1@s uk-grid-collapse uk-height-large uk-height-1-1" style="background: #e2e2e2;" data-uk-grid>
          <div class="uk-tile uk-padding-remove uk-width-1-2@m uk-width-1-1@s">
            <div class="uk-position-center-left uk-position-large uk-text-center uk-text-left@m">
              <h1 class="uk-heading-medium uk-margin-small-bottom"><?php echo $block->title; ?></h1>
              <p class="uk-margin-small-bottom"><?php echo strip_tags($block->body_content); ?></p>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
