<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			17.09.2012
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
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/personalization/personalization.order.class.php';
require_once('libs/modules/paymentterms/paymentterms.class.php');
require_once('libs/modules/deliveryterms/deliveryterms.class.php');
require_once('libs/modules/businesscontact/address.class.php');
require_once('libs/modules/collectiveinvoice/collectiveinvoice.class.php');
require_once('libs/modules/collectiveinvoice/orderposition.class.php');
require_once 'libs/modules/warehouse/warehouse.class.php';
require_once('libs/modules/documents/document.class.php');
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/associations/association.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;
// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();
// header("Content-type: application/pdf");
// header('Content-Disposition: inline;');
if ($_REQUEST["ciid"])
{
    $collectinv = new CollectiveInvoice((int)$_REQUEST["ciid"]);
    $doc = new Document();
    $doc->setRequestId($collectinv->getId());
    $doc->setRequestModule(Document::REQ_MODULE_COLLECTIVEORDER);
    $doc->setType(Document::TYPE_OFFER);
    $doc->setPreview(1);
    $hash = $doc->createDoc(Document::VERSION_EMAIL);
}
?>