<?php
namespace ProcessWire;

/** @var Config $config */
/** @var Wire $wire */
/** @var Page $page */
/** @var Pages $pages */
/** @var User $user */
/** @var PageInstance $pageInstance */
/** @var SiteSettings $settings */
/** @var Tools $tools */
/** @var Client $client */
/** @var Cart $cart */

include "../index.php"; //PW bootstrap
include $config->paths->templates."chunks/init.php";

$formPageId = intval($_POST['formpageid']);
/** @var WalkForm $formPage */
$formPage = $wire->pages->get($formPageId);

$answers = []; //holds Answer objects
$rawAnswersArr = json_decode($_POST['answers']);
$attributes = []; //holds array inputfieldname => val. Used to hanna-replace in emailTemplates
foreach($rawAnswersArr as $ans) {
	$answer = new Answer;
	$answer->getFromPost($ans);
	$answers[] = $answer;
	$attributes[$answer->fieldlabel] = $answer->valText;
}

$emailval = $_POST['userMasterEmail'];
if($tools->checkEmailValid($emailval) === false) { //check if email is valid format
	echo "alert|Please check your email address - it appears to be invalid.";
	exit;
}

$nextAction = $formPage->onsubmitAction->name;
$nextSuccess = $formPage->onsubmitSuccessText; //either URL (for page-change) or string for alert
if($nextSuccess == "") {
	$nextSuccess = "Thanks for your message, we will be in touch shortly!";
}
$nextFail = $formPage->onsubmitFailText;
if($nextFail == "") {
	$nextFail = "Sorry, there was an error submitting the form, please try again or contact us directly.";
}
$overallfail = false;

//save to a page if required
if($formPage->saveToPage == 1) {
	$p = new Page();
	$p->template = $formPage->savePageTemplate;
	$p->parent = $formPage->savePageParent;
	if(is_object($p->parent) and $p->template != "") {
		if ($formPage->newpage_published == 1) {
			$p->removeStatus("unpublished");
		}
		else {
			$p->addStatus("unpublished");
		}
		if ($p->template == "form-dump") { //this template dumps everything in a single body_content field
			$mytitle = date("Y m d H:i") . " " . $emailval;
			$dump = "";
			foreach ($answers as $myans) {
				/** @var Answer $myans */
				$fldname = $myans->fieldlabel;
				if ($fldname != "") {
					$dump .= $fldname . ": " . $myans->valText . "<br/>"; //valtext has already been sanitised
				}
			}
			$p->title = $mytitle;
			$p->body_content = $dump;
		}
		else { //all other templates save directly to fields 1:1
			foreach ($answers as $myans) {
				/** @var Answer $myans */
				$fldname = $myans->targetFieldName;
				if ($fldname != "") {
					try {
						$p->$fldname = $myans->val; //has already been sanitised
					}
					catch(\Exception $e) { }
				}
			}
		}
		$p->save();
	}
}

//handle email response from email page template (response email to form submitter/visitor)
if($formPage->sendResponseEmail == 1 and $emailval != "") {
	$email = new Emailer();
	$emailTemplatePage = $pages->get($formPage->emailTemplateSelect->id);
	$email->createEmailFromPage($emailTemplatePage, $attributes);
	$email->mail->addAddress($emailval);
	$email->mail->send();
}

//notify site owner that a form has been completed
if($formPage->sendNotifyEmail == 1) {
	$email = new Emailer();
	$emailTemplatePage = $pages->get($formPage->notify_emailTemplateSelect->id);
	if($emailTemplatePage->id != "") {
		$email->createEmailFromPage($emailTemplatePage, $attributes); //this is to use an email template Page for email body
	}
	else { //most cases - just a form dump with the dump of fields in the email body
		$email->mail->Subject = $formPage->title . " form submission";
		$email->mail->Body = $dump;
	}
	if($formPage->contact_form_recipient !== "") {
		$recipients = $tools->splitComma($formPage->contact_form_recipient);
	}
	else { //use the default global settings
		$recipients = $tools->splitComma($settings->contact_form_recipient);
	}
	for ($t = 0; $t < count($recipients); $t++) {
		$thisTo = $recipients[$t];
		$email->mail->addAddress("$thisTo");
	}
	if($email->mail->send()) {

	}
	else {
		$overallfail = true;
	}
}

if($overallfail === true) {
	$outputjs = ['error' => 1, 'message' => 'There seemed to be a problem, please try again or contact us directly.'];
	echo json_encode($outputjs);
	exit;
}
$outputjs = [];
if($nextAction === "alert") {
	$outputjs = ['action' => 'alert', 'message' => $nextSuccess];
}
else if($nextAction === "alert-refresh") {
	$outputjs = ['action' => 'alertRefresh', 'message' => $nextSuccess];
}
else if($nextAction === "alert-to-home") {
	$outputjs = ['action' => 'alertToHome', 'message' => $nextSuccess, 'url' => $config->urls->root];
}
else if($nextAction === "page-change") {
	$outputjs = ['action' => 'alertThenPagechange', 'message' => $nextSuccess, 'url' => $formPage->onsubmitSuccessText];
}

echo json_encode($outputjs);

