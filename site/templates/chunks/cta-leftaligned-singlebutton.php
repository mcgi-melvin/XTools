<?php namespace ProcessWire;
/** @var PageTableContentBlock $block **/
/** @var Page $page **/
if(!isset($block)) { $block = $page; }
?>

<div class="uk-width-1-1 uk-background-cover <?php if($block->addclasses != "") echo $block->addclasses; ?>" data-chunk="cta-leftaligned-singlebutton" <?php if($block->divid != "") { echo 'id="'.$block->divid.'"'; } ?> style="background-image: url('<?php echo $block->hero_image->first->url;  ?>');" alt="Strap Work">
  <div class="centreme">
    <div class="centremeinner">
      <div class="uk-light uk-child-width-expand@m uk-child-width-1-1@s uk-grid-collapse uk-height-large uk-height-1-1" uk-grid>
          <div class="uk-tile uk-padding-remove uk-width-1-2@m uk-width-1-1@s">
            <div class="uk-position-center-left uk-position-relative uk-position-large uk-text-center uk-text-left@m">
              <h1 class="uk-heading-medium uk-margin-small-bottom"><?php echo $block->title; ?></h1>
              <p class="uk-margin-small-bottom"><?php echo $block->subheading; ?></p>
              <a class="uk-button uk-button-default" <?php if($block->redir_blankTarget == 1) { echo "target='_blank'"; } ?> href="<?= $block->redirect_link ?>"><?= $block->link_title ?></a>
            </div>
          </div>
      </div>
    </div>
  </div>
</div>
