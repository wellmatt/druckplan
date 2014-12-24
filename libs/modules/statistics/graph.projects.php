<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       16.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

/* Duplicate graph in order to get the graph as jpg to display it in the pdf-file *****************/

error_reporting(-1);
ini_set('display_errors', 1);

chdir('../../../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once("libs/modules/calculation/order.class.php");
require_once("libs/modules/businesscontact/businesscontact.class.php");
require_once("thirdparty/jpgraph-1.26/src/jpgraph.php");
require_once("thirdparty/jpgraph-1.26/src/jpgraph_bar.php");
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$mincrtdat = time() - (30* 86400);

$data = Order::getStatistic1month($mincrtdat);



for($x = 0; $x < count($data[0]) && $data != false; $x++)
{
   $row = $data[0][$x];

   $idx = date('d.m.Y', $row["crtdat"]);
   if((int)$row["status"] == 1 || (int)$row["status"] == 2)
      $sidx = 2;
   elseif((int)$row["status"] == 5)
      $sidx = 1;
   elseif((int)$row["status"] == 3 || (int)$row["status"] == 4)
      $sidx = 0;
   $struct[$idx][$sidx] += $row["cc"];
}

$data1y = Array();
$data2y = Array();
$data3y = Array();
foreach(array_keys($struct) AS $ddate)
{
   array_push($data1y, $struct[$ddate][0]);
   array_push($data2y, $struct[$ddate][1]);
   array_push($data3y, $struct[$ddate][2]);
}

// Create the graph. These two calls are always required
$graph = new Graph(600,300,"auto");
$graph->SetScale("textlin");

$graph->SetShadow(0);
$graph->img->SetMargin(40,30,20,70);

// Create the bar plots
$b1plot = new BarPlot($data1y);
$b1plot->SetFillColor("#AAAAAA");
$b2plot = new BarPlot($data2y);
$b2plot->SetFillColor("#B4F277");
$b3plot = new BarPlot($data3y);
$b3plot->SetFillColor("#FF5a5a");

// Create the grouped bar plot
$gbplot = new GroupBarPlot(array($b3plot,$b1plot,$b2plot));
$gbplot->SetWidth(0.7);

$b1plot->SetLegend($_LANG->get('In Bearbeitung'));
$b2plot->SetLegend($_LANG->get('Erledigt'));
$b3plot->SetLegend($_LANG->get('Angeboten'));

// ...and add it to the graph
$graph->Add($gbplot);

$graph->xaxis->title->Set("");
$graph->yaxis->title->Set($_LANG->get('Anzahl'));

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->SetTickLabels(array_keys($struct));
$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,8);
$graph->xaxis->SetLabelAngle(45);
$graph->SetFrame(false);
$graph->SetFrameBevel(0,false,'#F0EDDD'); 
$graph->SetBackgroundGradient('#F0EDDD','#F0EDDD',GRAD_HOR,BGRAD_MARGIN);
$graph->legend->Pos(0.03,0.03,"right","top");

// Display the graph
if ((int)$_REQUEST["temporary"] == 1){
	$graph->Stroke();
} else {
	$graph->Stroke("temp/graph.projects.jpg");
}

//$graph->StrokeStore("temp/graph.projects.jpg"); // Erst bei neuerer Version verfügbar
?>