<?php namespace ProcessWire;

/**
 * Admin template just loads the admin application controller, 
 * and admin is just an application built on top of ProcessWire. 
 *
 * This demonstrates how you can use ProcessWire as a front-end 
 * to another application. 
 *
 * Feel free to hook admin-specific functionality from this file, 
 * but remember to leave the require() statement below at the end.
 * 
 */
/** @var Config $config */
/** @var WireInput $input */
/** @var Modules $modules */

// put the modal toggle variable into the admin Javascript config variable
$isModal = 0;
if($input->get->modal == 1) {
	$isModal = 1;
}
$config->js("isModal", $isModal);
////

require($config->paths->adminTemplates . 'controller.php');
