<? // ------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			27.08.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
chdir('../../../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/notes/notes.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$_REQUEST["exec"] = trim(addslashes($_REQUEST["exec"]));

// Such nach Artikel oder Kalkulationen
if ($_REQUEST["exec"] == "loadNoteDetails") {
	$note = new Notes($_REQUEST["noteid"]);
	
	echo $note->getId();
	echo "_+-+_+-+_";		// Trennzeichen
	echo $note->getTitle();
	echo "_+-+_+-+_";
	echo $note->getComment();
}

?>

