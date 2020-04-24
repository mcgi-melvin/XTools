<?php
namespace ProcessWire;

/**
 * Various tools
 */

class Tools {
	/** @var InlineParser $parser  */
	public $parser;

	function __construct() {
		$this->parser = new InlineParser();
	}

	/**
	 * Takes an integer 1 to True, 0 or other to False
	 * @param $int
	 * @return bool
	 */
	public function intToBool($int) {
		if($int === 1) {
			return true;
		}
		else if($int === 0) {
			return false;
		}
		else if(intval($int) === 1) {
			return true;
		}
		else if(intval($int) === 0) {
			return false;
		}
	}

	/**
	 * Splits a string separated by pipe to array eg "1" becomes [1], "1|4|2" becomes [1,4,2]
	 * @param string $valstr Original string to split
	 * @return array
	 */
	public function splitme($valstr) {
		if($valstr === "") {
			$ret = [];
		}
		else {
			if (stripos($valstr, "|") > 0) {
				$ret = explode("|", $valstr);
			}
			else {
				$ret = [];
				$ret[] = $valstr;
			}
		}
		return $ret;
	}

	/**
	 * Splits a string separated by comma to array eg "1" becomes [1], "1,4,2" becomes [1,4,2]
	 * @param string $incoming Original string to split
	 * @return array
	 */
	public function splitComma(string $incoming) {
		$outgoing = [];
		if(stripos($incoming,",") > 0) {
			$outgoing = explode(",",$incoming);
		}
		else {
			$outgoing[0] = $incoming;
		}
		return $outgoing;
	}

	/**
	 * Hash encodes incoming string
	 * @param string $incoming Unhashed string
	 * @return string Hashed string
	 */
	public function hashEncode(string $incoming) {
		return $this->my_simple_crypt($incoming, 'e');
	}

	/**
	 * Hash DECODES incoming string
	 * @param string $incoming Hashed string
	 * @return string Unhashed string
	 */
	public function hashDecode(string $incoming) {
		return $this->my_simple_crypt($incoming, 'd');
	}

	/**
	 * Used by hashEncode and hashDecode as the crypto key
	 * @param $string
	 * @param string $action
	 * @return bool|string
	 */
	protected function my_simple_crypt($string, $action = 'e' ) {
		$secret_key = 'M7Kad61k1lkdaG';
		$secret_iv = '096j62Kadjv2@am';
		$output = false;
		$encrypt_method = "AES-256-CBC";
		$key = hash( 'sha256', $secret_key );
		$iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
		if( $action == 'e' ) {
			$output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
		}
		else if( $action == 'd' ){
			$output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
		}
		return $output;
	}

	/**
	 * Checks if any element of $incomingArray exists in $haystackArray and returns true if so (false if not)
	 * Useful for checking permissions etc
	 * @param array $incomingArray
	 * @param array $haystackArray
	 * @return bool
	 */
	public function isAnyElementInArray(array $incomingArray, array $haystackArray) {
		if(count(array_intersect($incomingArray, $haystackArray)) === 0) {
			return false; //no items in the incoming array exist in the haystack array
		}
		else { //at least one item in the incoming array exist in the haystack array
			return true;
		}
	}

	/**
	 * Converts an integer timestampe to d-m-Y date string
	 * @param int $unixtime
	 * @return false|string
	 */
	public function unixToDMY(int $unixtime) {
		return date("d-m-Y",$unixtime);
	}

	/**
	 * Checks if supplied email address is valid
	 * @param string $email
	 * @return bool
	 */
	public function checkEmailValid(string $email) {
		$emailIsValid = false;
		if (!empty($email)) { //make sure not empty
			$domain = ltrim(stristr($email, '@'), '@'); //get email parts
			$user = stristr($email, '@', TRUE);
			if(!empty($user) and !empty($domain)) { //validate email address
				if(checkdnsrr($domain)) { //checks if the domain exists
					$emailIsValid = true;
				}
			}
		}
		return $emailIsValid;
	}

	/**
	 * Generates a random token/passstring with x number of characters
	 * @param int $numChars
	 * @return bool|string
	 */
	public function generateToken(int $numChars) {
		return substr(md5(rand()), 0, $numChars);
	}

	/**
	 * Used by the old Form Builder - creates key-val options from an array of values
	 * @param $arr
	 * @return array
	 */
	public function convertOptArrayToObjArray(array $arr) {
		$optionobj = [];
		foreach($arr as $val) {
			$o = new stdClass();
			if($val !== null) {
				$o->id = $this->cleanForSelectVal($val);
				$o->title = $val;
				$optionobj[] = $o;
			}
			unset($o);
		}
		return $optionobj;
	}

	/**
	 * Checks if article/post expiry date is OK - returns true if no date exists
	 * @param Page $pageObj
	 * @param string $expireDateFieldName
	 * @return bool
	 */
	public function checkExpiryDateIsOK(Page $pageObj, string $expireDateFieldName) {
		if(!$pageObj->get($expireDateFieldName)) { //a blank or non-existant field returns true in this case (ie no end date activated)
			return true;
		}
		else {
			$unixExpire = strtotime($pageObj->get($expireDateFieldName));
			$nowTime = time();
			if($unixExpire < $nowTime) {
				return false;
			}
			else {
				return true;
			}
		}
	}

	/**
	 * Checks if article/post start date is OK - returns true if no date exists
	 * @param Page $pageObj
	 * @param string $startDateFieldName
	 * @return bool
	 */
	public function checkStartDateIsOK(Page $pageObj, string $startDateFieldName) {
		if(!$pageObj->get($startDateFieldName)) { //a blank or non-existant field returns true in this case (ie no start date activated)
			return true;
		}
		else {
			$unixStart = strtotime($pageObj->get($startDateFieldName));
			$nowTime = time();
			if($unixStart >= $nowTime) {
				return false;
			}
			else {
				return true;
			}
		}
	}

	/**
	 * Gets the description field for supplied image object
	 * @param $imageObject
	 * @return string
	 */
	public function getImageDescription($imageObject) {
		$alt = "";
		if($imageObject->description != "") {
			$alt = $imageObject->description;
		}
		return $alt;
	}

	/**
	 * Cleans a redirect to an absolute URL (checking if it's an internal PW link)
	 * @param string $incoming
	 * @return bool|string
	 */
	public function cleanRedirect(string $incoming) {
		global $page, $config;
		$outlink = $incoming;
		if($incoming === "none") {
			$outlink = "javascript:void(0);";
		}
		else if(stripos($outlink,"tel:") === 0 or stripos($outlink, "mailto:") === 0) {
			$outlink = $incoming; //send it back out exactly as it came in
		}
		else if(stripos($outlink,"http") !== 0 and stripos($outlink,"www") !== 0) { //this is an internal (relative) page link
			if(stripos($outlink,"/") === 0) {
				$outlink = substr($outlink,1);
			}
			$outlink = $config->urls->httpRoot . $outlink;
		}
		else if(stripos($outlink,"www") === 0) {
			$outlink = "http://" . $outlink;
		}
		return $outlink;
	}

	/**
	 * Creates an alt tag for image looking at $image->description then $page->title if description does not exist
	 * @param $imageObject
	 * @param Page $pageObject
	 * @return string
	 */
	public function generateImageAltTag($imageObject, Page $pageObject) {
		$alt = "Image";
		if(is_object($imageObject) and $imageObject->description != "") {
			$alt = $imageObject->description;
		}
		else {
			$alt = $pageObject->title . " image";
		}
		return $alt;
	}

	/**
	 * Creates HTML wrapper for a page eg "<a href='pagelink'>$title</a>"
	 * @param Page $pageItem
	 * @return string
	 */
	public function createLinkFromPage(Page $pageItem) { //$item is a Page object
		$out = "";
		if($pageItem->id) {
			$alink = $pageItem->url;
			$title = $pageItem->title;
			$out .= "<a href='$alink'>$title</a>";
		}
		return $out;
	}

	/**
	 * Cleans invalid characters for use in Select options
	 * @param $str
	 * @return float|int|mixed
	 */
	public function cleanForSelectVal($str) {
		$str = str_ireplace(" ","",$str);
		$str = str_ireplace("&","",$str);
		if(stripos($str,"%") > 0) { //convert percentage to decimal
			$str = str_ireplace("%", "", $str);
			$str = floatval($str) / 100;
		}
		return $str;
	}

	/**
	 * Gets the first instance of an object for a field (even if multiple selected eg Sections)
	 * @param $object
	 * @param $fieldname
	 * @return mixed
	 */
	public function getFirstInstance($object, $fieldname) {
		//handles first instance even if multi selected eg sections
		//pass the subpage etc as $object
		if($this->isSingleObject($object) === true) {
			return $object->get($fieldname);
		}
		else { //this is an array, we need the first object in the array
			return $object->first()->$fieldname;
		}
	}

	/**
	 * Checks if this is a single object
	 * @param $object
	 * @return bool
	 */
	public function isSingleObject($object) { //can be any object, not just page object
		//do we handle this as an array or a single item (for images, section pages etc)
		$isMulti = true; //default treat as multi
		$multiTypes = ["ProcessWire\PageArray", "ProcessWire\Pageimages"];
		$singleTypes = ["ProcessWire\Page","ProcessWire\Pageimage","ProcessWire\PageInstance"];
		$objectType = get_class($object);
		if(in_array($objectType,$singleTypes)) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Gets the first image from a page field
	 * @param $pageObj
	 * @param $imageFieldName
	 * @return bool
	 */
	public function getFirstImageFromPage($pageObj, $imageFieldName) {
		$counter = 0;
		$heroImages = $pageObj->get($imageFieldName);
		if($heroImages == "") { //there is no image in the field
			return false;
		}
		else {
			if ($this->isSingleObject($heroImages) === true) { //treat as single
				$heroFirstImage = $heroImages;
			}
			else { //treat as array
				foreach ($heroImages as $heroImageItem) {
					if ($counter > 0) {
						break;
					}
					else {
						$heroFirstImage = $heroImageItem; //image object
					}
					$counter++;
				}
			}
			return $heroFirstImage; //returns object
		}
	}

	/**
	 * Adds a class to an existing class string (checking for duplicates)
	 * @param string $existingClassString
	 * @param string $newClassText
	 * @return string
	 */
	public function addClassToString(string $existingClassString, string $newClassText) {
		$newClassArr = explode(" ", $newClassText); //in case there is more than one to add
		foreach($newClassArr as $newClassItemName) {
			if ($existingClassString != "") {
				if ($this->doesElementHaveClass($existingClassString, $newClassItemName) === false) { //this class isn't already in there
					$existingClassString .= " " . $newClassItemName;
				}
			}
			else { //blank so far
				$existingClassString = $newClassItemName;
			}
		}
		return $existingClassString;
	}

	/**
	 * Checks if a class string contains a specific class already
	 * @param string $existingClassString
	 * @param string $classtext
	 * @return bool
	 */
	public function doesElementHaveClass(string $existingClassString, string $classtext) {
		$existClassArr = explode(" ",$existingClassString);
		if(in_array($classtext, $existClassArr)) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Removes a specific class string from an existing class string if it exists
	 * @param string $existingClassString
	 * @param string $removeClassText
	 * @return string
	 */
	public function removeClassFromString(string $existingClassString, string $removeClassText) {
		if($existingClassString != "") {
			if($this->doesElementHaveClass($existingClassString, $removeClassText) === true) { //this class is in there
				$existClassArr = explode(" ",$existingClassString);
				$ind = array_search($removeClassText, $existClassArr);
				unset($existClassArr[$ind]);
				$existingClassString = implode(" ",$existClassArr);
			}
		}
		return $existingClassString;
	}

	/**
	 * Generates ID HTML output eg "id='myid'" where myid is supplied as parameter
	 * @param string $divid
	 * @param Page|NULL $onPage
	 * @return string
	 */
	public function generateIdHTML(string $divid, $onPage) {
		$out = "";
		if($divid != "") {
			if($onPage !== NULL) {
				$divid = $this->parser->parse($divid, $onPage);
			}
			$out .= "id='".$divid."'";
		}
		return $out;
	}

	/**
	 * Converts string to integer (specifically removing "px" and "%")
	 * @param string $incoming
	 * @return int|mixed
	 */
	public function toInt(string $incoming) {
		$out = str_ireplace("px","", $incoming);
		$out = str_ireplace("%","",$out);
		$out = intval($out);
		return $out;
	}

	/**
	 * Adds user to Mailchimp list (as set in PW)
	 * @param array $data
	 * @param string $myAPI
	 * @param string $listid
	 * @return mixed
	 */
	public function syncMailchimp(array $data, string $myAPI, string $listid) {
		$apiKey = $myAPI;
		$listId = $listid;

		$memberId = md5(strtolower($data['email']));
		$dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
		$url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;

		$json = json_encode($data);

		$ch = curl_init($url);

		curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

		$result = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return $httpCode;
	}

	/**
	 * If the number passed is even returns True, otherwise returns False
	 * @param int $number
	 * @return bool
	 */
	public function isEven(int $number) {
		if($number % 2 == 0) { //it's even
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * @param Page $p Current $page value
	 * @param string $fieldname Fieldname of the pagetable (usually pt_content_repeat)
	 * @throws WireException
	 */
	public function renderPageTable($p, string $fieldname) {
		global $wire;
		foreach($p->$fieldname as $block) {
			/** @var PageTableContentBlock $block **/
			$ext = "";
			if(substr($block->chunkname, -4) != ".php") {
				$ext = ".php";
			}
			include $wire->config->paths->templates."chunks/".$block->chunkname.$ext;
		}
	}

}