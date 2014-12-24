<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			01.10.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
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
require_once 'libs/modules/documents/document.class.php';
require_once 'libs/modules/accounting/warnlevel.class.php';
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
if ($_REQUEST["exec"] == "updateInvoicewarningText") {
	$invoice = new Document((int)$_REQUEST["invid"]);
	$warnlevel = new Warnlevel((int)$_REQUEST["warnid"]);
	
	$ret_text = $warnlevel->getText();
	
	
	$ret_text = str_replace("%RECHNUNGSNUMMER%", str_replace(".pdf", "", $invoice->getName()), $ret_text);
	$ret_text = str_replace("%RECHNUNGSBETRAG%", printPrice($invoice->getPriceBrutto())." EUR", $ret_text);
	$ret_text = str_replace("%RECHNUNGSDATUM%", date('d.m.Y', $invoice->getCreateDate()), $ret_text);
	$ret_text = str_replace("%RECHNUNG_FRIST%", date('d.m.Y',$invoice->getPayable()), $ret_text);
	$ret_text = str_replace("%MAHNFRIST%", date('d.m.Y', time() + $warnlevel->getDeadline()*24*60*60), $ret_text);
	
	//$ret_test = str_replace("\r\n", '\n', $ret_test);
	
	/*
	%RECHNUNGSDATUM% = <?=$_LANG->get('Rechnungsdatum');?> <br/>
	%RECHNUNGSBETRAG% = <?=$_LANG->get('Rechnungsbetrag');?><br/>
	%RECHNUNGSNUMMER% = <?=$_LANG->get('Rechnungsnummer');?><br/>
	%RECHNUNGSFRIST% = <?=$_LANG->get('Frist der Rechnung');?><br/>
	%MAHNFRIST% = <?=$_LANG->get('Frist der Mahnung');?><br/>
	*/
	
	echo $ret_text;
}

?>