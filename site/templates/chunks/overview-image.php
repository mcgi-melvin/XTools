<?php namespace ProcessWire;
/** @var PageTableContentBlock $block **/
/** @var Page $page **/
if(!isset($block)) { $block = $page; }
?>

<div class="uk-width-1-1 uk-dark <?php if($block->addclasses != "") echo $block->addclasses; ?>" data-chunk="overview-image" <?php if($block->divid != "") { echo 'id="'.$block->divid.'"'; } ?>>
  <div class="centreme">
    <div class="centremeinner">
      <div class="uk-grid uk-grid-collapse uk-padding-small uk-child-width-expand@s uk-margin-remove " style="background: #e2e2e2;" uk-grid>
        <div class="uk-padding-remove uk-margin-remove-top">
          <div class="uk-text-center uk-padding">
            <img data-src="<?php echo $block->feature_image; ?>" height="300px" uk-img>
            <h1 class="uk-heading-medium uk-margin-small-bottom"><?php echo $block->title; ?></h1>
            <p class="uk-margin-small-bottom uk-text-justify"><?php echo strip_tags($block->body_content); ?></p>
          </div>
        </div>
        <div class="uk-padding-remove uk-margin-remove-top">
          <div class="uk-text-center uk-padding">
            <img data-src="<?php echo $block->feature_image; ?>" height="300px" uk-img>
            <h1 class="uk-heading-medium uk-margin-small-bottom"><?php echo $block->title; ?></h1>
            <p class="uk-margin-small-bottom uk-text-justify"><?php echo strip_tags($block->body_content); ?></p>
          </div>
        </div>
        <div class="uk-padding-remove uk-margin-remove-top">
          <div class="uk-text-center uk-padding">
            <img data-src="<?php echo $block->feature_image; ?>" height="300px" uk-img>
            <h1 class="uk-heading-medium uk-margin-small-bottom"><?php echo $block->title; ?></h1>
            <p class="uk-margin-small-bottom uk-text-justify"><?php echo strip_tags($block->body_content); ?></p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
