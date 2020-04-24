<?php
namespace ProcessWire;

/**
 * Class Answer
 * @package ProcessWire
 * A single Answer for the Walk Form Builder, used to handle response
 */

class Answer {
	/** @var int $fieldid The page Id of the form input object (question page ID) */
	public $fieldid = 0;
	/** @var string $inputfieldname The field name (PW page name) of input field */
	public $inputfieldname;
	/** @var string $val Answer value for this question */
	public $val = "";
	/** @var string $valText Answer value (cleaned from Title if input is page) */
	public $valText = "";
	/** @var string $fieldtype Gets type of field for this Answer from JSON */
	public $fieldtype = "";
	/** @var Field $targetField The target field object for this answer (ie the field to save to when creating a page) */
	public $targetField;
	/** @var string $targetFieldName Name of the target field to save Answer value to */
	public $targetFieldName = "";
	/** @var string $fieldlabel The nicename of the input eg Name, Email Address, Add Your Message Here */
	public $fieldlabel = "";
	/** @var bool $isEmailField */
	public $isEmailField = false;

	/**
	 * @param Answer $ansob
	 * Creates the Answer object from a JSON object passed in (already generated the New Answer)
	 */
	public function getFromPost($ansob) {
		global $wire;
		$this->fieldid = intval($ansob->fieldid);
		$this->inputfieldname = $wire->pages->get($this->fieldid)->name;
		$this->fieldtype = $wire->sanitizer->text($ansob->fieldtype);
		$this->fieldlabel = $wire->sanitizer->text($ansob->fieldlabel);
		if($this->targetField->inputfieldClass == "InputfieldCKEditor") { //for RTE fields
			$this->val = $wire->sanitizer->textarea($ansob->val, ['multiLine' => false, 'newlineReplacement' => "<br/>", 'stripTags' => false]);
		}
		else { //for plaintext fields
			$this->val = $wire->sanitizer->textarea($ansob->val);
		}
		$this->valText = $this->val;
		if(in_array($this->fieldtype, ['checkbox','radio','select'])) {
			$answerPageId = intval($this->val);
			$answerPage = $wire->pages->get($answerPageId);
			if($answerPage->id != "") {
				$this->valText = $answerPage->title;
			}
		}
		//$this->getTargetFieldFromFieldId();
	}

	/**
	 * Get the Save Target Field from the ID of the incoming JSON
	 */
	public function getTargetFieldFromFieldId() {
		global $wire;
		$selectedFieldVal = $wire->pages->get($this->fieldid)->get("field_select");
		$selField = $wire->fields->get($selectedFieldVal[0]);
		$this->targetField = $selField;
		$this->targetFieldName = $selField->name;
	}
}