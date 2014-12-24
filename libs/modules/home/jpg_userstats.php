<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       16.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once("../../classes/menu.php");
require_once("../../classes/page.php");
require_once("../../classes/mysql.php");
require_once("../../config.php");

include ("../../thirdparty/jpgraph-1.26/src/jpgraph.php");
include ("../../thirdparty/jpgraph-1.26/src/jpgraph_pie.php");
include ("../../thirdparty/jpgraph-1.26/src/jpgraph_pie3d.php");

//----------------------------------------------------------------------------------
session_start();

require_once("../../lang/{$_SESSION["_CONF"]["conf_lang_filename"]}");

//----------------------------------------------------------------------------------
$CON = new CMYSQL($_CONFIG[$_CONFIG["_MODUS"]]["DATABASE"]["NAME"],
                  $_CONFIG[$_CONFIG["_MODUS"]]["DATABASE"]["HOST"],
                  $_CONFIG[$_CONFIG["_MODUS"]]["DATABASE"]["USER"],
                  $_CONFIG[$_CONFIG["_MODUS"]]["DATABASE"]["PASS"]);
$CON->connect();

$sql = " select t1.menu_id, t1.counter
         from user_stats t1
         where
         t1.user_id = {$_SESSION["user_id"]}
         order by t1.counter desc
         LIMIT 0, 5";
$stats = $CON->select($sql);

for($x = 0; $x < count($stats) && $stats != false; $x++)
{
   $data[$x]   = $stats[$x]["counter"];

   $parentid   = $_SESSION["_MENU"]->getModuleVal($stats[$x]["menu_id"],"menu_parent");
   $legend[$x] = utf8_decode($_SESSION["_MENU"]->getModuleVal($parentid,"menu_name")."/"
                .$_SESSION["_MENU"]->getModuleVal($stats[$x]["menu_id"],"menu_name"));
}
//
$graph = new PieGraph(380,300,"auto");
$graph->SetFrameBevel(0,false,'white');
$graph->legend->Pos(NULL, NULL,"right","top");
$graph->legend->SetFillColor('gray@0.9');
$graph->legend->SetFont(FF_FONT1,FS_NORMAL);
$graph->legend->SetShadow('black@0.99',1);

$graph->SetBackgroundGradient($_SESSION["_PAGE"]->returnStyleVal("div", "box2", "background-color"),
                              $_SESSION["_PAGE"]->returnStyleVal("div", "box2", "background-color"),
                              GRAD_HOR,BGRAD_PLOT);
$graph->SetColor($_SESSION["_PAGE"]->returnStyleVal("div", "box2", "background-color"));
$p1 = new PiePlot3D($data);
$p1->ExplodeAll(10);
$p1->SetCenter(0.45, 0.56);
$p1->SetLegends($legend);

$graph->Add($p1);
$graph->Stroke();
?>
