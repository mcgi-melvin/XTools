<?php namespace ProcessWire;
/** @var PageTableContentBlock $block **/
/** @var Page $page **/
if(!isset($block)) { $block = $page; }
?>

<div class="full-width uk-light uk-height-large uk-height-1-1 uk-background-cover <?php if($block->addclasses != "") echo $block->addclasses; ?>" data-chunk="banner-singletitle-leftaligned" <?php if($block->divid != "") { echo 'id="'.$block->divid.'"'; } ?> style="background-image: url('<?php echo $block->hero_image->first->url;  ?>');" alt="Strap Work">
  <div class="centreme uk-height-1-1">
	  <div class="centremeinner uk-height-1-1 uk-child-width-expand@m uk-child-width-1-1@s uk-grid-collapse" data-uk-grid>
          <div class="uk-tile uk-padding-remove uk-width-1-2">
            <h1 class="uk-position-center uk-text-centremeinner uk-heading-medium uk-margin-small-bottom"><?php echo $block->title; ?></h1>
          </div>
          <div class="uk-tile uk-tile-small uk-text-right uk-width-1-2">
            <a class="uk-position-center uk-text-centremeinner uk-button uk-button-small uk-button-default" href="<?php echo $block->redirect_link; ?>" <?php if($block->redir_blankTarget = 1){echo 'target=_blank';} ?>><?php echo $block->link_title; ?></a>
          </div>
	 </div>
  </div>
</div>
