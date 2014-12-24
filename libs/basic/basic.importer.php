<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			11.09.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
// 
// Diese Datei ist fuer den Import in allen Dateien, die irgendwelche FancyBoxen
// bedienen und mit Inhalt fuellen, oder fuer die *.ajax.php-Dateien
//
// ----------------------------------------------------------------------------------
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once("libs/basic/countries/country.class.php");
require_once 'libs/modules/organizer/contact.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/chat/chat.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/schedule/schedule.class.php';

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


if ($_USER == false){
	error_log("Login failed (basic-importer.php)");
	die("Login failed");
}

?>