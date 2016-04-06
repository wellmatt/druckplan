<?php
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
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


error_reporting(-1);
ini_set('display_errors', 1);




function GetMonth($sStartDate, $sEndDate){

    $sStartDate = date('Y-m-d', strtotime($sStartDate));
    $sEndDate = date('Y-m-d', strtotime($sEndDate));
    // dem $aDays Array das erste Datum hinzufügen
    $aDays[] = date('Y-m-d',strtotime($sStartDate));
    // $sCurrentDate auf das Startdatum setzen
    $sCurrentDate = $sStartDate;
    // Schleife die solange läuft bis das $sCurrentDate nicht mehr kleiner als das Enddatum ist
    while($sCurrentDate < $sEndDate){
        // auf $sCurrentDate +1 Monat draufrechnen
        $sCurrentDate = date('Y-m-d', strtotime("+1 month", strtotime($sCurrentDate)));
        // das $sCurrentDate dem $aDays Array hinzufügen
        $aDays[] = date('Y-m-d',strtotime($sCurrentDate));
    }
    return $aDays;
}


$start = 1451645980;
$end = 1470045580;

prettyPrint(GetMonth(date('d.m.Y',$start),date('d.m.Y',$end)));

