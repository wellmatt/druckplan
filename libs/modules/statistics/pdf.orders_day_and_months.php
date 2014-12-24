<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       16.08.2012
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
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/schedule/schedule.class.php';
require_once 'libs/modules/schedule/schedule.part.class.php';
require_once 'libs/modules/documents/document.class.php';
require_once 'libs/modules/schedule/schedule.machine.class.php';
require_once 'thirdparty/ezpdf/class.ezpdf.php';
require_once("thirdparty/jpgraph-1.26/src/jpgraph.php");
require_once("thirdparty/jpgraph-1.26/src/jpgraph_bar.php");




if(file_exists('libs/modules/calculation/order.class.php'))
    require_once 'libs/modules/calculation/order.class.php';

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


/* Tabelle Aufträge der letzten Monate */
$thismonth  = date('m');
$lastyear   = date('Y') -1;

$mincrtdat = mktime(0, 0, 0, $thismonth, 1, $lastyear);

$data = Order::getStatistic12monthsTab($mincrtdat);
$customers = BusinessContact::getAllBusinessContacts(BusinessContact::ORDER_NAME,BusinessContact::FILTER_CUST_IST);	


$month = Array();
foreach ($data as $count)
{
   $month[$count['order_month']] = $month[$count['order_month']] + $count['cc'];
}

/* Summe */
$data_sum_months = Order::getCountAllOrders12months($mincrtdat);


$sum_months = $data_sum_months[0]['count'] + $data_sum_months[1]['count'];

//----------------------------------------------------------------------------------
/* Tabelle Aufträge der letzten 30 Tage */
$mincrtdat = time() - (30* 86400);

$data_month = Order::getStatistic1monthTab($mincrtdat);

$day = Array();
foreach ($data_month as $count)
{
   $day[$count['order_day']][$count['status']] = $count['cc'];
}

$res1 = array();
$res2 = array();
$res3 = array();
$sum_res1 = 0;
$sum_res2 = 0;
$sum_res3 = 0;
for($x=1;$x<=30;$x++){
$res1[$x] = $day[date('Y-m-d', strtotime('-'.$x.' days'))][1] + $day[date('Y-m-d', strtotime('-'.$x.' days'))][2]; 
$sum_res1 = $sum_res1 + $res1[$x];
if($res1[$x]==0) $res1[$x]='';
$res2[$x] = $day[date('Y-m-d', strtotime('-'.$x.' days'))][3] + $day[date('Y-m-d', strtotime('-'.$x.' days'))][4]; 
$sum_res2 = $sum_res2 + $res2[$x];
if($res2[$x]==0) $res2[$x]='';
$res3[$x] = $day[date('Y-m-d', strtotime('-'.$x.' days'))][5];
$sum_res3 = $sum_res3 + $res3[$x]; 
}
/*******************************************************************************/

$pdf = new Cezpdf();
$pdf->selectFont("thirdparty/ezpdf/fonts/Helvetica.afm");
$pdf->ezSetMargins(20, 20, 20, 20);
$pdf->setColor(0.0,0.0,0.0);


$pdf->ezText($_LANG->get('Statistiken'),15);
$pdf->ezSetDy(-20);
$pdf->ezText($_LANG->get('Auftr&auml;ge der letzten 30 Tage'),12);
$pdf->ezSetDy(15);
$pdf->ezText($_LANG->get('Auftr&auml;ge der letzten Monate'),12, array(justification => right, right => 10));

$pdf->ezSetDy(-25);
$pdf->ezImage('temp/graph.projects.jpg',0,300,'none','left');
$pdf->ezSetY(792);
$pdf->ezImage('temp/graph.projects.month.jpg',50,200,'none','right');



$pdf->ezText($_LANG->get('Auftr&auml;ge der letzten 30 Tage tabellarisch'),12);
$pdf->ezSetDy(15);
$pdf->ezText($_LANG->get('Auftr&auml;ge der letzten Monate tabellarisch'),12, array(justification => right, right => 10));
$pdf->ezSetDy(-12);


//-----------------------------  Tabelle rechts -----------------------------
// ... besteht aus 3 Tabellen: Kopf, Inhalt und Fußzeile

//Kopf
$colsarr = Array( "Monat"          => Array("width" => "80"),
				  "Auftr&auml;ge"  => Array("justification"=>"center","width" => "60"));

$attr = Array("showHeadings" => 0, "shaded" => 1,  'xPos'=>'510', 'xOrientation'=>'left','width'=>100, "showLines" => 1,
		"rowGap" => 2, "colGap" => 4, "cols" => $colsarr);

$data = array(
		array('Monat' => '<b>'.$_LANG->get('Monat').'</b>', 'Auftr&auml;ge' => '<b>'.$_LANG->get('Auftr&auml;ge').'</b>'));

$pdf->ezTable($data,'','',$attr);
unset($data);

//Inhalt
$attr = Array("showHeadings" => 0, "shaded" => 1, "shadeCol" => Array(0.95,0.95,0.95),
                 'xPos'=>'510', 'xOrientation'=>'left','width'=>100, "showLines" => 1,
                "rowGap" => 2, "colGap" => 4, "cols" => $colsarr,);

$data = array(
 array('Monat'=>date('m.Y', strtotime('-1 months')),'Auftr&auml;ge'=>$month[date('m.Y', strtotime('-1 months'))])
,array('Monat'=>date('m.Y', strtotime('-2 months')),'Auftr&auml;ge'=>$month[date('m.Y', strtotime('-2 months'))])
,array('Monat'=>date('m.Y', strtotime('-3 months')),'Auftr&auml;ge'=>$month[date('m.Y', strtotime('-3 months'))])
,array('Monat'=>date('m.Y', strtotime('-4 months')),'Auftr&auml;ge'=>$month[date('m.Y', strtotime('-4 months'))])
,array('Monat'=>date('m.Y', strtotime('-5 months')),'Auftr&auml;ge'=>$month[date('m.Y', strtotime('-5 months'))])
,array('Monat'=>date('m.Y', strtotime('-6 months')),'Auftr&auml;ge'=>$month[date('m.Y', strtotime('-6 months'))])
,array('Monat'=>date('m.Y', strtotime('-7 months')),'Auftr&auml;ge'=>$month[date('m.Y', strtotime('-7 months'))])
,array('Monat'=>date('m.Y', strtotime('-8 months')),'Auftr&auml;ge'=>$month[date('m.Y', strtotime('-8 months'))])
,array('Monat'=>date('m.Y', strtotime('-9 months')),'Auftr&auml;ge'=>$month[date('m.Y', strtotime('-9 months'))])
,array('Monat'=>date('m.Y', strtotime('-10 months')),'Auftr&auml;ge'=>$month[date('m.Y', strtotime('-10 months'))])
,array('Monat'=>date('m.Y', strtotime('-11 months')),'Auftr&auml;ge'=>$month[date('m.Y', strtotime('-11 months'))])
,array('Monat'=>date('m.Y', strtotime('-12 months')),'Auftr&auml;ge'=>$month[date('m.Y', strtotime('-12 months'))]));       

$pdf->ezTable($data,'','',$attr);
unset($data);

//
$attr = Array("showHeadings" => 0, "shaded" => 1,  'xPos'=>'510', 'xOrientation'=>'left','width'=>100, "showLines" => 1,
                "rowGap" => 2, "colGap" => 4, "cols" => $colsarr);

$data = array(
         array('Monat' => '<b>'.$_LANG->get('Summe').'</b>', 'Auftr&auml;ge' => '<b>'.$sum_months.'</b>'));

$pdf->ezTable($data,'','',$attr);            
unset($data);


$pdf->ezSetDy(-10);
$pdf->ezSetY(535);
//-----------------------------  Tabelle links -----------------------------
// ... besteht aus 3 Tabellen: Kopf, Inhalt und Fußzeile

//Kopf
$colsarr = Array( "Datum"        => Array("width" => "80"),
				  "angeboten"    => Array("justification"=>"center","width" => "60"),
                  "in Bearb."    => Array("justification"=>"center","width" => "60"),
				  "erledigt"     => Array("justification"=>"center","width" => "60"));

$attr = Array("showHeadings" => 0, "shaded" => 1, "width" => "260", "xpos" => "left", 'xOrientation' => 'left', "showLines" => 1,
		"rowGap" => 2, "colGap" => 4, "cols" => $colsarr);

$data = array(
		array('Datum'=>'<b>'.$_LANG->get('Summe').'</b>',
			  'angeboten'=>'<b>'.$_LANG->get('Angeboten').'<b>',
			  'in Bearb.'=>'<b>'.$_LANG->get('In Bearbeitung').'<b>',
			  'erledigt'=>'<b>'.$_LANG->get('Erledigt').'<b>'));

$pdf->ezTable($data,'','',$attr);
unset($data);

//
$attr = Array("showHeadings" => 0, "shaded" => 1,"width" => "260", "shadeCol" => Array(0.95,0.95,0.95),
                 "xpos" => "left", 'xOrientation' => 'left', "showLines" => 1,
                "rowGap" => 2, "colGap" => 4, "cols" => $colsarr);

$data = array(
array('Datum'=>date('d.m.Y', strtotime('-1 days')),'angeboten'=>$res1[1],'in Bearb.'=>$res2[1],'erledigt'=>$res3[1])
,array('Datum'=>date('d.m.Y', strtotime('-2 days')),'angeboten'=>$res1[2],'in Bearb.'=>$res2[2],'erledigt'=>$res3[2])
,array('Datum'=>date('d.m.Y', strtotime('-3 days')),'angeboten'=>$res1[3],'in Bearb.'=>$res2[3],'erledigt'=>$res3[3])
,array('Datum'=>date('d.m.Y', strtotime('-4 days')),'angeboten'=>$res1[4],'in Bearb.'=>$res2[4],'erledigt'=>$res3[4])
,array('Datum'=>date('d.m.Y', strtotime('-5 days')),'angeboten'=>$res1[5],'in Bearb.'=>$res2[5],'erledigt'=>$res3[5])
,array('Datum'=>date('d.m.Y', strtotime('-6 days')),'angeboten'=>$res1[6],'in Bearb.'=>$res2[6],'erledigt'=>$res3[6])
,array('Datum'=>date('d.m.Y', strtotime('-7 days')),'angeboten'=>$res1[7],'in Bearb.'=>$res2[7],'erledigt'=>$res3[7])
,array('Datum'=>date('d.m.Y', strtotime('-8 days')),'angeboten'=>$res1[8],'in Bearb.'=>$res2[8],'erledigt'=>$res3[8])
,array('Datum'=>date('d.m.Y', strtotime('-9 days')),'angeboten'=>$res1[9],'in Bearb.'=>$res2[9],'erledigt'=>$res3[9])
,array('Datum'=>date('d.m.Y', strtotime('-10 days')),'angeboten'=>$res[10],'in Bearb.'=>$res2[10],'erledigt'=>$res3[10])
,array('Datum'=>date('d.m.Y', strtotime('-11 days')),'angeboten'=>$res1[11],'in Bearb.'=>$res2[11],'erledigt'=>$res3[11])
,array('Datum'=>date('d.m.Y', strtotime('-12 days')),'angeboten'=>$res1[12],'in Bearb.'=>$res2[12],'erledigt'=>$res3[12])
,array('Datum'=>date('d.m.Y', strtotime('-13 days')),'angeboten'=>$res1[13],'in Bearb.'=>$res2[13],'erledigt'=>$res3[13])
,array('Datum'=>date('d.m.Y', strtotime('-14 days')),'angeboten'=>$res1[14],'in Bearb.'=>$res2[14],'erledigt'=>$res3[14])
,array('Datum'=>date('d.m.Y', strtotime('-15 days')),'angeboten'=>$res1[15],'in Bearb.'=>$res2[15],'erledigt'=>$res3[15])
,array('Datum'=>date('d.m.Y', strtotime('-16 days')),'angeboten'=>$res1[16],'in Bearb.'=>$res2[16],'erledigt'=>$res3[16])
,array('Datum'=>date('d.m.Y', strtotime('-17 days')),'angeboten'=>$res1[17],'in Bearb.'=>$res2[17],'erledigt'=>$res3[17])
,array('Datum'=>date('d.m.Y', strtotime('-18 days')),'angeboten'=>$res1[18],'in Bearb.'=>$res2[18],'erledigt'=>$res3[18])
,array('Datum'=>date('d.m.Y', strtotime('-19 days')),'angeboten'=>$res1[19],'in Bearb.'=>$res2[19],'erledigt'=>$res3[19])
,array('Datum'=>date('d.m.Y', strtotime('-20 days')),'angeboten'=>$res1[20],'in Bearb.'=>$res2[20],'erledigt'=>$res3[20])
,array('Datum'=>date('d.m.Y', strtotime('-21 days')),'angeboten'=>$res1[21],'in Bearb.'=>$res2[21],'erledigt'=>$res3[21])
,array('Datum'=>date('d.m.Y', strtotime('-22 days')),'angeboten'=>$res1[22],'in Bearb.'=>$res2[22],'erledigt'=>$res3[22])
,array('Datum'=>date('d.m.Y', strtotime('-23 days')),'angeboten'=>$res1[23],'in Bearb.'=>$res2[23],'erledigt'=>$res3[23])
,array('Datum'=>date('d.m.Y', strtotime('-24 days')),'angeboten'=>$res1[24],'in Bearb.'=>$res2[24],'erledigt'=>$res3[24])
,array('Datum'=>date('d.m.Y', strtotime('-25 days')),'angeboten'=>$res1[25],'in Bearb.'=>$res2[25],'erledigt'=>$res3[25])
,array('Datum'=>date('d.m.Y', strtotime('-26 days')),'angeboten'=>$res1[26],'in Bearb.'=>$res2[26],'erledigt'=>$res3[26])
,array('Datum'=>date('d.m.Y', strtotime('-27 days')),'angeboten'=>$res1[27],'in Bearb.'=>$res2[27],'erledigt'=>$res3[27])
,array('Datum'=>date('d.m.Y', strtotime('-28 days')),'angeboten'=>$res1[28],'in Bearb.'=>$res2[28],'erledigt'=>$res3[28])
,array('Datum'=>date('d.m.Y', strtotime('-29 days')),'angeboten'=>$res1[29],'in Bearb.'=>$res2[29],'erledigt'=>$res3[29])
,array('Datum'=>date('d.m.Y', strtotime('-30 days')),'angeboten'=>$res1[30],'in Bearb.'=>$res2[30],'erledigt'=>$res3[30])
);


$pdf->ezTable($data,'','',$attr);
unset($data);

$attr = Array("showHeadings" => 0, "shaded" => 1, "width" => "260", "xpos" => "left", 'xOrientation' => 'left', "showLines" => 1,
                "rowGap" => 2, "colGap" => 4, "cols" => $colsarr);

$data = array(
array('Datum'=>'<b>'.$_LANG->get('Summe').'</b>','angeboten'=>'<b>'.$sum_res1.'<b>','in Bearb.'=>'<b>'.$sum_res2.'<b>','erledigt'=>'<b>'.$sum_res3.'<b>'));

$pdf->ezTable($data,'','',$attr);            
unset($data);


unlink("temp/graph.projects.jpg");
unlink("temp/graph.projects.month.jpg");

header("Content-Type: application/pdf");
header("Content-disposition: attachment; filename=\"pdf.orders_day_and_months.pdf\"");
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');

ob_clean();
flush();

$pdf->ezStream();
 
?>