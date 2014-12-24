<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       16.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

chdir('../../../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/modules/calculation/order.class.php");
require_once("libs/modules/businesscontact/businesscontact.class.php");
require_once("thirdparty/jpgraph-1.26/src/jpgraph.php");
require_once("thirdparty/jpgraph-1.26/src/jpgraph_bar.php");

$DB = new DBMysql();
$DB->connect($_CONFIG->db);

$thismonth  = date('m');
$lastyear   = date('Y') -1;

$mincrtdat = mktime(0, 0, 0, $thismonth, 1, $lastyear);

$data = Order::getStatistic12months($mincrtdat);

//----------------------------------------------------------------------------------


for($x = 0; $x < count($data[0]) && $data != false; $x++)
{
    $row = $data[0][$x];
  $idx = date('m/Y', $row["crtdat"]);
   
 $struct[$idx] += $row["cc"];
}


$data1y = Array();
foreach(array_keys($struct) AS $ddate)
{
   array_push($data1y, $struct[$ddate]);
}


// Create the graph. These two calls are always required
$graph = new Graph(330,300,"auto");
$graph->SetScale("textlin");

$graph->SetShadow(0);
$graph->img->SetMargin(40,30,0,70);
$top = 40;
$bottom = 0;
$left = 70;
$right = 30;
$graph->Set90AndMargin($left,$right,$top,$bottom);

// Create the bar plots
$b1plot = new BarPlot($data1y);
$b1plot->SetFillColor("#AAAAAA");
$b1plot->value->SetFormat('%d');
$b1plot->value->Show();
$graph->Add($b1plot);

$graph->xaxis->title->Set("");
$graph->yaxis->title->Set("");

$graph->title->SetFont(FF_FONT1,FS_BOLD);
$graph->yaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->title->SetFont(FF_FONT1,FS_BOLD);
$graph->xaxis->SetTickLabels(array_keys($struct));
$graph->xaxis->SetFont(FF_ARIAL,FS_NORMAL,8);
$graph->SetFrame(false);
$graph->SetFrameBevel(0,false,'#F0EDDD'); 
$graph->SetBackgroundGradient('#F0EDDD','#F0EDDD',GRAD_HOR,BGRAD_MARGIN);

// Display the graph
$graph->Stroke();
?>