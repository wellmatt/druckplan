<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       22.05.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

error_reporting(-1);
ini_set('display_errors', 1);

chdir("../../../");
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/organizer/event.class.php';

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_LANG = new Translator(22);


if ($_REQUEST["exec"] == "moveEvent") {
    $event = new Event($_REQUEST["event_id"]);
	$new_start = $_REQUEST["new_start"];
	$new_end = $_REQUEST["new_end"];
	$event->setBegin($new_start);
	$event->setEnd($new_end);
	// $event->save();
	echo getSaveMessage($event->save()).$DB->getLastError();
}
if ($_REQUEST["exec"] == "resizeEvent") {
    $event = new Event($_REQUEST["event_id"]);
	$new_start = $_REQUEST["new_start"];
	$new_end = $_REQUEST["new_end"];
	$event->setBegin($new_start);
	$event->setEnd($new_end);
	// $event->save();
	echo getSaveMessage($event->save()).$DB->getLastError();
}
if ($_REQUEST["exec"] == "newEvent") {
	$new_title = $_REQUEST["title"];
	$new_start = $_REQUEST["new_start"];
	$new_end = $_REQUEST["new_end"];
	$new_user = new User($_REQUEST["user"]);
    $event = new Event();
	$event->setTitle($new_title);
	$event->setBegin($new_start);
	$event->setEnd($new_end);
	$event->setUser($new_user);
	echo getSaveMessage($event->save()).$DB->getLastError();
}

?>