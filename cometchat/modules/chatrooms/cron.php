<?php

if (!defined('CC_CRON')) { echo "NO DICE"; exit; }

if(isset($_REQUEST['url'])){
	include_once(dirname(dirname(dirname(__FILE__))).DIRECTORY_SEPARATOR."modules.php");
}

$auth = md5(md5(ADMIN_USER).md5(ADMIN_PASS));

if($_REQUEST['auth'] == $auth ) {
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR."config.php");

	if ((!empty($_REQUEST['cron']['type']) && $_REQUEST['cron']['type'] == "all") || !empty($_REQUEST['cron']['modules'])) {
		chatrooms();
		chatroommessages();
		chatroomsusers();
	} else {
		if(isset($_REQUEST['cron']['inactiverooms'])){chatrooms();}
		if(isset($_REQUEST['cron']['chatroommessages'])){chatroommessages();}
		if(isset($_REQUEST['cron']['inactiveusers'])){chatroomsusers();}
	}
	
} else {
	echo 'Sorry you don`t have permission.';
}

function chatrooms() {
	global $chatroomTimeout;
	$sql = ("delete from cometchat_chatrooms where createdby <> 0 and (".getTimeStamp()."-lastactivity)> ".$chatroomTimeout * 100);
	$query = mysqli_query($GLOBALS['dbh'],$sql);
	if (defined('DEV_MODE') && DEV_MODE == '1') { echo mysqli_error($GLOBALS['dbh']); }
}

function chatroommessages() {
	$sql = ("delete from cometchat_chatroommessages where (".getTimeStamp()."-sent)>10800");
	$query = mysqli_query($GLOBALS['dbh'],$sql);
	if (defined('DEV_MODE') && DEV_MODE == '1') { echo mysqli_error($GLOBALS['dbh']); }
}

function chatroomsusers() {
	$sql = ("delete from cometchat_chatrooms_users where (".getTimeStamp()."-lastactivity)>3600");
	$query = mysqli_query($GLOBALS['dbh'],$sql);
	if (defined('DEV_MODE') && DEV_MODE == '1') { echo mysqli_error($GLOBALS['dbh']); }
}