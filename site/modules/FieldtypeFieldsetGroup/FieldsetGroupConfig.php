<?php namespace ProcessWire;

/**
 * Fieldset Group configuration helper class
 *
 */

class FieldsetGroupConfig extends WireData {

	/**
	 * @var FieldtypeFieldsetGroup
	 * 
	 */
	protected $fieldtype;

	/**
	 * Construct
	 *
	 * @param FieldtypeFieldsetGroup $fieldtype
	 * 
	 */
	public function __construct(FieldtypeFieldsetGroup $fieldtype) {
		parent::__construct();
		$this->fieldtype = $fieldtype;
	}
	
	/**
	 * Field configuration
	 *
	 * @param Field $field
	 * @param InputfieldWrapper $inputfields
	 * @return InputfieldWrapper
	 *
	 */
	public function getConfigInputfields(Field $field, InputfieldWrapper $inputfields) {

		/** @var array $fieldsetFields */
		$fieldsetFields = $field->get('fieldsetFields');
		if(!is_array($fieldsetFields)) $fieldsetFields = array();

		/** @var Modules $modules */
		$modules = $this->wire('modules');

		/** @var Template|null $template */
		$template = $this->fieldtype->getFieldsetTemplate($field, !empty($fieldsetFields));

		/** @var Config $config */
		$config = $this->wire('config');

		$results = $this->fieldtype->checkTemplatesUsingFieldset($field, true);
		$extraNotes = array();
		
		foreach($results as $templateName => $result) {
			foreach($result as $resultType => $fields) {
				if(empty($fields)) continue;
				$fieldNames = implode(', ', array_keys($fields));
				$info = sprintf($this->_('(in template “%1$s” for fields: %2$s)'), $templateName, $fieldNames);
				$note = '';
				switch($resultType) {
					case 'missingFields':
						$note = $this->_('Added fields to fieldset');
						break;
					case 'outFields':
						$note = $this->_('Moved fields outside the fieldset into the fieldset');
						break;
					case 'extraFields':
						$extraNotes[] = sprintf($this->_('Template “%1$s” has extra field(s) in the fieldset: %2$s'), $templateName, $fieldNames);
						break;
					case 'reorderFields':
						$note = $this->_('Updated order');
						break;
					case 'updateFields':
						$note = $this->_('Updated context/settings');
						break;
				}
				if($note) $this->message("$note $info");
			}
		}
		$this->getConfigCheckDeleteFields($inputfields, $field, $fieldsetFields);

		/** @var InputfieldAsmSelect $select */
		$select = $modules->get('InputfieldAsmSelect');
		$select->label = $this->_x('Fields in this fieldset', 'field-label');
		$select->icon = 'cube';
		$select->description =
			$this->_('Define the fields that appear in this fieldset. You may also drag and drop fields to the desired order.') . ' ' .
			$this->_('You may also click selected fields to configure them within the context of this fieldset') . ' ' .
			$this->_('(for newly added fields, you must save first).');
		$select->notes =
			$this->_('You may add this fieldset to template(s) from the Actions tab here, or edit a template and add this fieldset from there.') . ' ' .
			$this->_('When adding from a template, note that the fields within the fieldset will appear after you click to Save the template.');
		$select->attr('name', 'fieldsetFields');
		$select->attr('id', 'fieldsetFields');
		$select->attr('title', $this->_('Add Field'));
		$select->setAsmSelectOption('sortable', true);
		$select->setAsmSelectOption('fieldset', true);
		$select->setAsmSelectOption('hideDeleted', false);

		if($template) {
			$select->setAsmSelectOption('editLink',
				$config->urls->admin .
				"setup/field/edit?id={value}&fieldgroup_id={$template->fieldgroup->id}&modal=1&process_template=1"
			);
			foreach($template->fieldgroup as $f) {
				/** @var Field $f */
				$f = $template->fieldgroup->getField($f->id, true); // get in context
				$columnWidth = (int) $f->get('columnWidth');

				$attrs = array(
					'selected' => 'selected',
					'data-status' => str_replace('Fieldtype', '', $f->type) . ' ' . ($columnWidth > 0 ? $columnWidth . '%' : '100%'),
					'data-desc' => $f->getLabel(),
				);
				$icon = $f->getIcon();
				if($icon) $attrs['data-handle'] = "<i class='fa fa-fw fa-$icon'></i>";
				$select->addOption($f->id, $f->name, $attrs);
			}
		}

		foreach($this->wire('fields') as $f) {
			if($f->id == $field->id) continue;
			if($template && $template->fieldgroup->has($f)) continue;
			if($f->name == $field->name || $f->name == $field->name . '_END') continue;
			if(($f->flags & Field::flagPermanent) && !$this->wire('config')->advanced) continue;
			$name = $f->name;
			if($f->flags & Field::flagSystem) $name .= "*";
			$columnWidth = (int) $f->get('columnWidth');
			$attrs = array(
				'data-desc' => $f->getLabel(),
				'data-status' => str_replace('Fieldtype', '', $f->type) . ' ' . ($columnWidth > 0 ? $columnWidth . '%': '100%'),
			);
			$icon = $f->getIcon();
			if($icon) $attrs['data-handle'] = "<i class='fa fa-fw fa-$icon'></i>";
			$select->addOption($f->id, $name, $attrs);
		}

		$select->attr('value', $fieldsetFields);

		$select->addHookAfter('processInput', function(HookEvent $event) use($field, $fieldsetFields) {
			/** @var FieldtypeFieldsetGroup $fieldtype */
			$fieldtype = $field->type;
			/** @var InputfieldSelect $select */
			$select = $event->object;
			$fieldtype->getConfigHelper()->getConfigCheckAddFields($select, $field, $fieldsetFields);
		});

		$inputfields->add($select);

		/** @var InputfieldRadios $f */
		$f = $modules->get('InputfieldRadios');
		$f->attr('name', 'useTabs');
		$f->label = $this->_('Fieldset type');
		$f->description = $this->_('The tab option applies to non-nested usage in page editor.');
		$f->addOption(0, $this->_('Fieldset'));
		$f->addOption(1, $this->_('Tab'));
		$f->val((int) $field->get('useTabs'));
		$f->optionColumns = 1;
		$f->collapsed = $f->val() ? Inputfield::collapsedNo : Inputfield::collapsedYes;
		$inputfields->add($f);

		if(count($extraNotes)) {
			$f = $modules->get('InputfieldMarkup');
			$f->attr('name', '_extraNotes');
			$f->label = $this->_('Just FYI');
			$icon = "<i class='fa fa-caret-right'></i> ";
			$f->value = '<p>' . $icon . implode('<br />' . $icon, $extraNotes) . '</p>';
			$f->notes = $this->_('If needed, you may remove or adjust these extra fields by editing the templates.');
			$inputfields->add($f);
		}

		return $inputfields;
	}

	/**
	 * Part of getConfigInputfields: Handle add field requests
	 *
	 * @param Inputfield $f
	 * @param Field $field
	 * @param array $fieldsetFields
	 *
	 */
	public function getConfigCheckAddFields(Inputfield $f, Field $field, array $fieldsetFields) {

		$ids = $f->val();
		$addedIDs = array_diff($ids, $fieldsetFields);
		$templates = $this->fieldtype->getTemplatesUsingField($field);
		$hasErrors = false;

		foreach($addedIDs as $id) {
			$addField = $this->wire('fields')->get((int) $id);
			if(!$addField) continue;
			foreach($templates as $template) {
				if(!$template->fieldgroup->hasField($addField)) continue;
				if($this->fieldtype->allowAddCollide) {
					$field->warning("Warning: field '$addField' was already on template '$template' and has been moved into this fieldset.");
				} else {
					$key = array_search($id, $ids);
					if($key !== false) unset($ids[$key]);
					$hasErrors = true;
					$field->error(
						"Cannot add field '$addField' because template '$template' uses this fieldset " .
						"and already uses field '$addField` for another purpose."
					);
				}
			}
		}

		if($hasErrors) $f->val($ids);
	}

	/**
	 * Part of getConfigInputfields(): Handle delete field requests
	 *
	 * @param InputfieldWrapper $inputfields
	 * @param Field $field
	 * @param array $fieldsetFields
	 *
	 */
	protected function getConfigCheckDeleteFields(InputfieldWrapper $inputfields, Field $field, array $fieldsetFields) {

		/** @var WireInput $input */
		$input = $this->wire('input');

		/** @var array $fieldsetFieldsPrev */
		$fieldsetFieldsPrev = $field->get('fieldsetFieldsPrev');

		if(!is_array($fieldsetFieldsPrev) || empty($fieldsetFieldsPrev)) $fieldsetFieldsPrev = $fieldsetFields;
		$fieldsetFieldsPrev = array_unique(array_merge($fieldsetFieldsPrev, $fieldsetFields)); // account for additions
		$removedFieldIDs = array_diff($fieldsetFieldsPrev, $fieldsetFields);
		$field->set('fieldsetFieldsPrev', $fieldsetFieldsPrev);

		if(!count($removedFieldIDs)) return;

		$deleteFieldIDs = $input->post('_deleteFieldIDs');
		$removeFieldIDs = $input->post('_removeFieldIDs');

		if(!empty($deleteFieldIDs)) {
			$deleteFieldIDs = $this->wire('sanitizer')->intArray($deleteFieldIDs);
			$deleteFieldIDs = array_intersect($removedFieldIDs, $deleteFieldIDs);
		} else {
			$deleteFieldIDs = array();
		}

		if(!empty($removeFieldIDs)) {
			$removeFieldIDs = $this->wire('sanitizer')->intArray($removeFieldIDs);
			$removeFieldIDs = array_intersect($removedFieldIDs, $removeFieldIDs);
		} else {
			$removeFieldIDs = array();
		}

		$nd = count($deleteFieldIDs);
		$nr = count($removeFieldIDs);

		if($nd) {
			// delete deletes from all templates using fieldset
			$this->fieldtype->checkTemplatesUsingFieldset($field, false, $deleteFieldIDs);
			$this->message(sprintf($this->_n('Deleted %d field', 'Deleted %d fields', $nd), $nd));
		}

		if($nr) {
			// remove affects only the main template
			$t = $this->fieldtype->getFieldsetTemplate($field);
			/** @var array $fieldsetFields */
			$fieldsetFields = $field->get('fieldsetFields');
			foreach($t->fieldgroup as $f) {
				if(in_array($f->id, $removeFieldIDs)) {
					$t->fieldgroup->remove($f);
					$key = array_search($f->id, $fieldsetFields);
					if($key !== false) unset($fieldsetFields[$key]);
				}
			}
			$this->message(sprintf($this->_n('Removed %d field', 'Removed %d fields', $nr), $nr));
		}

		if($nd || $nr) {
			$fieldsetFieldsPrev = array_diff($fieldsetFieldsPrev, $deleteFieldIDs, $removeFieldIDs);
			$field->set('fieldsetFieldsPrev', $fieldsetFieldsPrev);
			return;
		}

		$fs = $this->wire('modules')->get('InputfieldFieldset');
		$fs->label = $this->_('Confirm deletion or removal of fields');
		$fs->description =
			$this->_('Please confirm deletion or removal of field(s) from fieldset.') . ' ' .
			$this->_('If you do not want to delete or remove a field, please add it back to the fieldset. Otherwise select an option below.');
		$fs->icon = 'warning';
		$tpls = $this->fieldtype->getTemplatesUsingField($field);
		if(count($tpls)) {
			$fs->notes = sprintf($this->_('This action affects the following template(s): %s'), '**' . implode('**, **', $tpls) . '**');
		}
		$inputfields->add($fs);

		$f = $this->wire('modules')->get('InputfieldCheckboxes');
		$f->attr('name', '_deleteFieldIDs');
		$f->label = $this->_('Delete fields and data (destructive)');
		$f->columnWidth = 50;
		$f->description =
			$this->_('This will remove the fields from the fieldset and permanently delete the field data for all templates using it.');
		foreach($removedFieldIDs as $fieldID) {
			$_field = $this->wire('fields')->get((int) $fieldID);
			if(!$_field) continue;
			$f->addOption($_field->id, $_field->name);
		}
		$fs->add($f);

		$f = clone $f;
		$f->attr('name', '_removeFieldIDs');
		$f->label = $this->_('Remove fields and leave data (non-destructive)');
		$f->description =
			$this->_('This will remove the fields from the fieldset definition, but leave them as they are in each template.') . ' ' .
			$this->_('No field data will be deleted.');
		$fs->add($f);
	}
}