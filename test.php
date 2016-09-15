<?php
error_reporting(-1);
ini_set('display_errors', 1);
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
require_once 'libs/modules/collectiveinvoice/orderposition.class.php';
require_once 'libs/modules/personalization/personalization.order.class.php';
require_once "thirdparty/phpfastcache/phpfastcache.php";
require_once 'libs/basic/cachehandler/cachehandler.class.php';
require_once 'libs/basic/eventqueue/eventqueue.class.php';
require_once 'libs/basic/eventqueue/eventclass.interface.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);

$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$_REQUEST["ciid"] = "68";
$_REQUEST["type"] = "1";

if ($_REQUEST["type"])
    $type = (int)$_REQUEST["type"];
else
    $type = Document::TYPE_OFFER;
$collectinv = new CollectiveInvoice((int)$_REQUEST["ciid"]);
$doc = new Document();
$doc->setRequestId($collectinv->getId());
$doc->setRequestModule(Document::REQ_MODULE_COLLECTIVEORDER);
$doc->setType($type);
$doc->setPreview(1);
if ($type == 5 || $type == 15) {
    $hash = $doc->createDoc(Document::VERSION_PRINT, false, false);
    $file = $doc->getFilename(Document::VERSION_PRINT);
} else {
    $hash = $doc->createDoc(Document::VERSION_EMAIL);
    $file = $doc->getFilename(Document::VERSION_EMAIL);
}