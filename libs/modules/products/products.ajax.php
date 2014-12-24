<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       16.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
chdir("../../../");
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/paper/paper.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);

$_REQUEST["paperId"] = (int)$_REQUEST["paperId"];
$_REQUEST["idx"] = (int)$_REQUEST["idx"];
$_REQUEST["part"] = trim(addslashes($_REQUEST["part"]));

$paper = new Paper($_REQUEST["paperId"]);

foreach($paper->getWeights() as $w) {
    echo '<input type="checkbox" name="paper_weight_'.$_REQUEST["part"].'_'.$_REQUEST["idx"].'_'.$_REQUEST["paperId"].'_'.$w.'" value="1" checked>'.$w." ";
}
?>