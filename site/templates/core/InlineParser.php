<?php
namespace ProcessWire;


/**
 * Class InlineParser
 * @package ProcessWire
 * Parses the text fields for values to replace for things like Id, htmlAttributes and Classes
 */
class InlineParser {
	/** @var string $opentags  */
	protected $opentags = "{";
	/** @var string $closetags  */
	protected $closetags = "}";
	/** @var string $rawstring  */
	protected $rawstring = "";
	/** @var int $nValue Optionally set by object (code) to allow parsing of {n} */
	public $nValue;

	/**
	 * @param $stringToParse
	 * @param $currentPage
	 * @return string
	 */
	public function parse($stringToParse, $currentPage) {
		global $wire;
		/** @var Page $currentPage */
		if($currentPage !== NULL) {
			$parentPage = $currentPage->parent();
		}
		$this->rawstring = $stringToParse;
		$nstarts = substr_count($stringToParse, $this->opentags);
		$nends = substr_count($stringToParse, $this->closetags);
		$rawbits = [];
		$ret = "";
		if($nstarts > 0) { //we have a match
			if($nstarts === $nends) {
				$pointer = 0; //running position of the interior pointer/cursor in the string
				for($i=0; $i<$nstarts; $i++) {
					$startpos = stripos($stringToParse, $this->opentags, $pointer);
					$endpos = stripos($stringToParse, $this->closetags, $pointer);
					$len = $endpos - $startpos - 1;
					$subby = substr($stringToParse, $startpos + 1, $len); //this is the actual remaining string - the bit we replace later
					if(stripos($subby,".") > -1) { //this has a period, split it out
						$subbits = explode(".", $subby);
						$fieldname = $subbits[1];
						if($subbits[0] == "parent" and isset($parentPage)) { //handle parent stuff
							$pageToCheck = $parentPage;
						}
					}
					else {
						$pageToCheck = $currentPage;
						$fieldname = $subby;
					}
					//handle the {n} value if set
					if(isset($this->nValue) and $subby === "n") {
						$rawbits[$subby] = $this->nValue;
					}
					else if(isset($pageToCheck)) { //handle the page field lookups, if $pageToCheck is set
						//$fieldname can be different to $subby eg $subby = parent.id, $fieldname = id (checked on $parent)
						//$subby is the key in the $rawbits array as that's what we replace later
						if(!array_key_exists($subby, $rawbits)) {
							$rawbits[$subby] = $this->getFieldValue($fieldname, $pageToCheck);
						}
					}
					unset($pageToCheck);
				}
				//do the swaps including opentags and closetags
				foreach($rawbits as $search => $replacewith) {
					$stringToParse = str_ireplace($this->opentags.$search.$this->closetags, $replacewith, $stringToParse);
				}
			}
		}
		unset($this->nValue); //TODO not sure if this is right here? We want to clear it after each object is parsed
		return $stringToParse; //return the updated string
	}

	/**
	 * @param $fieldname
	 * @param $pageToCheck
	 * @return string
	 */
	protected function getFieldValue($fieldname, $pageToCheck) {
		$out = "";
		if(is_object($pageToCheck)) {
			$out = $pageToCheck->get($fieldname);
		}
		return $out;
	}

}