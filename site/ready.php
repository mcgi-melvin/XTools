<?php
namespace ProcessWire;
/** @var Config $config */
/** @var Wire $wire */
/** @var Page $page */
/** @var Pages $pages */
/** @var WireInput $input */

if($page->template == "admin") {
	if($page->name == "settings") {
		$input->get->id = $pages->get("/tools/settings/")->id;
	}
	else if($page->name == "home-page") {
		$input->get->id = 1;
	}
	else if($page->name == "footer") {
		$input->get->id = $pages->get("/block-elements/footer/")->id;
	}
}

$wire->addHookAfter('InputfieldPage::getSelectablePages', function($event) {
	if($event->object->hasField == 'responseEmailField') {
		$editpage = $event->arguments('page');
		$event->return = $event->pages->find("template=form-field, form_field_type=email, has_parent=$editpage");
	}
});