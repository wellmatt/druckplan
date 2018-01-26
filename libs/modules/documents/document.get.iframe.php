<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       19.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
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
require_once 'libs/modules/documents/document.class.php';
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$id = (int)$_REQUEST["getDoc"];
$doc = new Document($id);

if($doc->getRequestModule() == Document::REQ_MODULE_ORDER)
    $filename = $_CONFIG->docsBaseDir;
else if ($doc->getRequestModule() == Document::REQ_MODULE_MANORDER)
    $filename = $_CONFIG->docsBaseDir."man";
else if ($doc->getRequestModule() == Document::REQ_MODULE_COLLECTIVEORDER)
    $filename = $_CONFIG->docsBaseDir."col";

if($doc->getType() == Document::TYPE_OFFER)
    $filename .= "offer/".$_USER->getClient()->getId().'.'.$doc->getHash();
if($doc->getType() == Document::TYPE_OFFERCONFIRM)
    $filename .= "offerconfirm/".$_USER->getClient()->getId().'.'.$doc->getHash();
if($doc->getType() == Document::TYPE_DELIVERY)
    $filename .= "delivery/".$_USER->getClient()->getId().'.'.$doc->getHash();
if($doc->getType() == Document::TYPE_INVOICE)
    $filename .= "invoice/".$_USER->getClient()->getId().'.'.$doc->getHash();
if($doc->getType() == Document::TYPE_FACTORY)
    $filename .= "factory/".$_USER->getClient()->getId().'.'.$doc->getHash();
if($doc->getType() == Document::TYPE_PAPER_ORDER)
    $filename .= "paper_order/".$_USER->getClient()->getId().'.'.$doc->getHash();
if($doc->getType() == Document::TYPE_LABEL)
	$filename .= "label/".$_USER->getClient()->getId().'.'.$doc->getHash();
if($doc->getType() == Document::TYPE_INVOICEWARNING)
	$filename .= "invoicewarning/".$_USER->getClient()->getId().'.'.$doc->getHash();
if($doc->getType() == Document::TYPE_PROOF)
    $filename .= "proof/".$_USER->getClient()->getId().'.'.$doc->getHash();

if($_REQUEST["version"] == "email")
    $filename .= '_e';
else 
    $filename .= '_p';

$filename .= '.pdf';

header("Content-Type: {$_REQUEST["mime"]}");
header("Content-disposition: attachment; filename=\"{$doc->getName()}.pdf\"");
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');
header('Content-Length: ' . filesize($filename));

ob_clean();
flush();
if(file_exists($filename))
    readfile($filename);
?>