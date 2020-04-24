<?php namespace ProcessWire;


/**
 * Class Button
 * @package ProcessWire
 * From the old classes.php file - used extensively by Hanna Code buttons for Squarepeg
 */
class Button {
	/** @var string $link  */
	public $link;
	/** @var string $cleanlink  */
	private $cleanlink;
	/** @var string $classes  */
	public $classes;
	/** @var string $btntext  */
	public $btntext;
	/** @var string $onclick  */
	public $onclick = "";
	/** @var string $out  */
	public $out = "";
	/** @var string $target Set to "blank" or "_blank" to make this open in new tab */
	public $target = "";

	/**
	 * Button constructor.
	 */
	function __construct() {

	}

	/**
	 * @param $classtext
	 */
	public function addClass($classtext) {
		$this->classes .= " ".$classtext;
	}

	/**
	 * @param $onclicktext
	 */
	public function addOnclick($onclicktext) {
		$this->onclick = 'onclick="'.$onclicktext.';"';
		$this->link = "javascript:void(0);";
	}

	/**
	 * @return string
	 */
	public function build() {
		$tools = new Tools();
		$this->cleanlink = $tools->cleanRedirect($this->link);
		$targout = "";
		$onclickattr = "";
		if($this->target == "blank" or $this->target == "_blank") {
			$targout = "target='_blank'";
		}
		if($this->onclick != "") {
			$this->cleanlink = "javascript:void(0);";
			$onclickattr = 'onclick="'.$this->onclick.';"';
		}
		if($this->btntext != "") {
			$this->out .= "<a href='" . $this->cleanlink . "' $targout class='$this->classes' $onclickattr>";
			$this->out .= $this->btntext;
			$this->out .= "</a>";
		}
		return $this->out;
	}
}