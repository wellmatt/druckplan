<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			30.01.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
chdir('../../../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/paper/paper.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/foldtypes/foldtype.class.php';
require_once 'libs/modules/paperformats/paperformat.class.php';
require_once 'libs/modules/products/product.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/chromaticity/chromaticity.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/finishings/finishing.class.php';
require_once 'libs/modules/article/article.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$_REQUEST["exec"] = trim(addslashes($_REQUEST["exec"]));

/**
 * Neue Debitoren-Nr. generieren und zurueckgeben
 */
if ($_REQUEST["exec"] == "generadeDebitorNr") {
		echo $_USER->getClient()->generadeDebitorNumber();
}

/**
 * Neue Kreditoren-Nr. generieren und zurueckgeben
 */
if ($_REQUEST["exec"] == "generadeCreditorNr") {
	echo $_USER->getClient()->generadeCreditorNumber();
}

/**
 * Neue Kunden-Nr. generieren und zurueckgeben
 */
if ($_REQUEST["exec"] == "generadeCustomerNr") {
	echo $_USER->getClient()->generadeCustomerNumber();
}

/**
 * Ueberpruefung, ob die KD-Nr. bereits vergeben
 */
if ($_REQUEST["exec"] == "checkCustomerNumber") {
	$newnumber = trim(addslashes($_REQUEST["newnumber"]));

	$res = BusinessContact::checkCustomerNumber($newnumber);
	if ($res===true){
		echo "DA";
	} else {
		echo "NO";
	}
}

?>