<?php
namespace ProcessWire;
/** @var PageArray $people */
/** @var Pages $pages */
/** @var Page $page */
/** @var Config $config */

?>
<div class="full-width">
	<div class="centreme">
		<div class="uk-child-width-1-4@l uk-child-width-1-3@m uk-child-width-1-2@s uk-child-width-1-1 uk-grid-match" data-uk-grid>
			<?php
			$people = $pages->find("template=person, parent=/our-people/, id!=$page->id");
			foreach($people as $person) {
				$personImage = $config->urls->assets . "images/person-blank.jpg";
				if($person->hero_image != "") {
					$personImage = $person->hero_image->first->size(200, 200)->url;
				}
				?>
				<div class="person-card-hold">
					<a href="<?= $person->url ?>">
						<div class="uk-card person-card">
							<div class="uk-card-media-top person-image-hold" >
								<div class="person-card-image-top-background"></div>
								<div class="">
									<img src="<?= $personImage ?>" alt="<?= $person->title ?> photo" class="uk-border-circle person-tile-image" />
									<div class="person-tile-image-tint uk-border-circle"></div>
								</div>
							</div>
							<div class="uk-card-body person-card-body">
								<p class="person-name"><?= $person->title ?></p>
								<p class="person-jobtitle"><?= $person->subheading ?></p>
							</div>
						</div>
					</a>
				</div>
			<?php } ?>
		</div>
	</div>
</div>