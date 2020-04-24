<?php namespace ProcessWire;
/** @var PageTableContentBlock $block **/
/** @var Page $page **/
if(!isset($block)) { $block = $page; }
?>

<div class="full-width uk-light uk-background-cover <?php if($block->addclasses != "") echo $block->addclasses; ?>" data-chunk="banner-singletitle-nobutton" <?php if($block->divid != "") { echo 'id="'.$block->divid.'"'; } ?> style="background-image: url('<?php echo $block->hero_image->first->url;  ?>');" alt="Strap Work">
  <div class="centreme">
    <div class="centremeinner">
      <div class="uk-inline uk-width-1-1 uk-height-large uk-height-1-1">
        <div class="uk-position-center uk-text-center banner-content">
          <h1 class="uk-heading-medium uk-margin-small-bottom"><?php echo $block->title; ?></h1>
        </div>
      </div>
    </div>
  </div>
</div>
