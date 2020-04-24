<?php namespace ProcessWire;
/** @var PageTableContentBlock $block **/
/** @var Page $page **/
if(!isset($block)) { $block = $page; }
?>

<div class="full-width uk-background-cover <?php if($block->addclasses != "") echo $block->addclasses; ?>" data-chunk="banner-single-subtitle-leftaligned" <?php if($block->divid != "") { echo 'id="'.$block->divid.'"'; } ?> style="background-image: url('<?php echo $block->hero_image->first->url;  ?>');" alt="Strap Work">
  <div class="centreme">
    <div class="centremeinner">
      <div class="uk-light uk-child-width-expand@m uk-child-width-1-1@s uk-grid-collapse uk-height-large uk-height-1-1" data-uk-grid>
          <div class="uk-tile uk-padding-remove uk-width-1-2">
            <div class="uk-position-center uk-text-centremeinner uk-text-left@m">
              <h1 class="uk-heading-medium uk-margin-small-bottom"><?php echo $block->title; ?></h1>
              <p class="uk-margin-small-bottom"><?php echo $block->subheading; ?></p>
            </div>
          </div>
          <div class="uk-tile uk-tile-small uk-text-right uk-width-1-2">
            <a <?php if($block->redir_blankTarget = 1){ echo 'target="_blank"';} ?> class="uk-position-center uk-text-centremeinner uk-button uk-button-small uk-button-default" href="<?php echo $block->redirect_link; ?>"><?php echo $block->link_title; ?></a>
          </div>
      </div>
    </div>
  </div>
</div>
