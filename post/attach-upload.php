<?php //this file is to upload attachments
namespace ProcessWire;
include_once "../index.php"; //for credentials etc, setting up access to the PW API

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

$savepath = $config->paths->root."uploads/";
$type = $_POST['uploadType'];

$file = $_FILES['uploadFile'];
$uploadname = $_FILES['uploadFile']['name'];
$fileparts = explode(".",$uploadname);
$ext = strtolower(end($fileparts));
if($type !== "") {
	$targ = $_POST['uploadTargetFilename'];

	$extOK = array("pdf", "jpg", "png", "gif", "docx", "xlsx", "pptx"); //check document extension type is OK
	$returnMsg = "";

	if (!in_array($ext, $extOK)) {
		$returnMsg = "Extension not allowed.  Must be an PDF or image file";
		echo '<script>window.top.window.alert("' . $returnMsg . '");</script>';
		exit;
	}

	if ($_FILES['uploadFile']['size'] > 2000000) { //2Mb
		$returnMsg = "File too big.  Must be less than 2Mb";
		echo '<script>window.top.window.alert("' . $returnMsg . '");</script>';
		exit;
	}
	move_uploaded_file($_FILES['uploadFile']['tmp_name'], $savepath . $uploadname);
	echo '<script>window.top.window.updateVal("' . $targ . '","' . $uploadname . '");</script>';
}