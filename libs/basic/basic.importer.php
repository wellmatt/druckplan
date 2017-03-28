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
require_once 'libs/basic/cachehandler/cachehandler.class.php';
require_once 'thirdparty/phpfastcache/phpfastcache.php';
require_once 'libs/modules/organizer/contact.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/chat/chat.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/taxkeys/taxkey.class.php';
require_once 'libs/modules/costobjects/costobject.class.php';
require_once 'libs/modules/revenueaccounts/revenueaccount.class.php';
require_once 'libs/modules/accounting/receipt.class.php';

session_start();
global $_LANG;
global $_CONFIG;

$DB = new DBMysql();
$DB->connect($_CONFIG->db);

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);



if ($_USER == false){
	error_log("Login failed (basic-importer.php)");
	die("Login failed");
}
$_LANG = $_USER->getLang();

?>