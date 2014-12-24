<?php

error_reporting(0);

if (!isset($_SESSION)) {
	session_start();
}
// if (!isset($_SESSION['username']))
// {
// header( 'Location: login.php' );
// }

require('../config.fc.php');

header("Content-type: application/download");
header("Content-Disposition: attachment; filename=form_".$_GET[id].".txt");
header("Pragma: no-cache");
header("Expires: 0");

require('../config.fc.php');

if (!(isset($_GET['id'])))
{
	exit;
}


$form_req['build'] = addslashes($_POST['build']); 
$form_req['options'] = addslashes($_POST['options']); 
$form_req['con'] = addslashes($_POST['con']); 
$form_req['recipients'] = addslashes($_POST['recipients']); 
$form_req['dir'] = addslashes($_POST['dir']); 

print(json_encode($form_req));


?>