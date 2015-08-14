<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.09.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
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
//require_once 'libs/modules/paper/paper.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
/***require_once 'libs/modules/foldtypes/foldtype.class.php';
require_once 'libs/modules/paperformats/paperformat.class.php';
require_once 'libs/modules/products/product.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/chromaticity/chromaticity.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/finishings/finishing.class.php';***/
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

// Such nach Artikel oder Kalkulationen
if ($_REQUEST["exec"] == "checkArticleNumber") {
	$newnumber = trim(addslashes($_REQUEST["newnumber"]));
	
	$res = Article::checkArticleNumber($newnumber);
	if ($res===true){
		echo "DA";
	} else {
		echo "NO";
	}
}
if ($_REQUEST["ajax_action"] == "search_customer"){
    $retval = Array();
    $customers = BusinessContact::getAllBusinessContacts(BusinessContact::ORDER_NAME, " (name1 LIKE '%{$_REQUEST['term']}%' OR name2 LIKE '%{$_REQUEST['term']}%' OR matchcode LIKE '%{$_REQUEST['term']}%') ");
    foreach ($customers as $c){
        $retval[] = Array("label" => $c->getNameAsLine(), "value" => $c->getId());
	} 
	$retval = json_encode($retval);
	header("Content-Type: application/json");
	echo $retval;
}
if ($_REQUEST["ajax_action"] == "search_customer_cp"){
    $retval = Array();
	$allContactPerson = ContactPerson::getAllContactPersons(NULL, ContactPerson::ORDER_NAME, " AND (name1 LIKE '%{$_REQUEST['term']}%' OR name2 LIKE '%{$_REQUEST['term']}%') ");
	foreach ($allContactPerson as $cp){
	    $retval[] = Array("label" => $cp->getNameAsLine(), "value" => $cp->getId());
	} 
	$retval = json_encode($retval);
	header("Content-Type: application/json");
	echo $retval;
}

?>

