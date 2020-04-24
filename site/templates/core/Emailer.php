<?php
namespace ProcessWire;

/**
 * This class calls on PHPMailer, and contains project settings, email address and credentials etc
 */


class Emailer {
	/** @var \PHPMailer $mail PHPMailer object */
	public $mail; //PHPMailer object

	function __construct() {
		global $config, $settings;
		include_once $config->paths->templates . "scripts/PHPMailer/PHPMailerAutoload.php";
		$this->mail = new \PHPMailer();
		if($settings->smtp_debug == 1) { //debug checkbox is ticked in PW
			$this->mail->SMTPDebug = 2;
		}
		$this->mail->isSMTP();
		$this->mail->isHTML(true);
		$this->mail->SMTPAuth = true;
		$this->mail->SMTPSecure = false;
		$this->mail->SMTPOptions = array(
			'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		);
		$this->mail->Host = gethostname();
		if($settings->smtp_host != "") {
			$this->mail->Host = $settings->smtp_host; // eg "s312.syd1.hostingplatform.net.au";
		}
		$this->mail->Port = 587;
		if($settings->smtp_port != "") {
			$this->mail->Port = $settings->smtp_port;
		}
		$this->mail->From = $settings->site_from_address;
		$this->mail->Username = $settings->site_from_address;
		if($settings->smtp_username != "") { //this is sometimes different to from_address eg Elastic
			$this->mail->Username = $settings->smtp_username;
		}
		$this->mail->FromName = $settings->site_from_name;
		$this->mail->Password = $settings->smtp_pass;
	}

//	/**
//	 * @param string $fromAddress Set the From address for this email, and also get the sending server credentials
//	 */
//	public function fromSet($fromAddress) {
//		$this->mail->From = $fromAddress;
//		$this->generateCredentials($fromAddress);
//	}

//	/**
//	 * @param string $fromAddress Generate server sending credentials based on incoming server email username
//	 */
//	private function generateCredentials($fromAddress) {
//		$this->mail->Username = $fromAddress;
////		if($fromAddress === "noreply@squarepeg.agency") {
////			$this->mail->FromName = "SquarePeg";
////			$this->mail->Password = ''; //password for the email account
////		}
//	}

	public function createEmailFromPage($emailTemplatePage, array $attributes) {
		global $wire;
		$this->mail->Subject = $emailTemplatePage->subheading;
		$bodwork = $emailTemplatePage->body_content;
		//attrname is the name of field to replace eg cand_token
		foreach($attributes as $attrname => $attrReplacementVal) {
			$bodwork = str_ireplace("[[".$attrname."]]", $attrReplacementVal, $bodwork);
		}
		//get rid of any leftover square brackets (in case of mismatch attributes)
		$bodwork = str_ireplace("[[", "", $bodwork);
		$bodwork = str_ireplace("]]", "", $bodwork);
		//now send the email
		$this->mail->Body = $bodwork;
	}
}