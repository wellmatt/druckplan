<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       24.07.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/modules/calculation/order.class.php");
require_once("libs/modules/businesscontact/businesscontact.class.php");
require_once("thirdparty/jpgraph-1.26/src/jpgraph.php");
require_once("thirdparty/jpgraph-1.26/src/jpgraph_bar.php");

$DB = new DBMysql();
$DB->connect($_CONFIG->db);

global $_LANG;

/* Tabelle Aufträge der letzten Monate */
$thismonth  = date('m');
$lastyear   = date('Y') -1;

$mincrtdat = mktime(0, 0, 0, $thismonth, 1, $lastyear);

$data = Order::getStatistic12monthsTab($mincrtdat);


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

?>

<!-------------------------------- INNER START --------------------------->

<table cellpadding="0" cellspacing="0" width="980"
	style="table-layout: fixed">
	<colgroup>
		<col width="620" style="vertical-align: top">
		<col width="16">
		<col width="364" style="vertical-align: top">
	</colgroup>
	<tr>
		<td style="vertical-align: top">
			<div class="box1">
				<b><?=$_LANG->get('Auftr&auml;ge der letzten 30 Tage')?></b> <br/>
				
				<img src="./libs/modules/statistics/graph.projects.php?temporary=1">			
				
				<?php /***** <img src="./temp/graph.projects.jpg">
				<b><?=$_LANG->get('Auftr&auml;ge der letzten 30 Tage')?></b><br /> 
				<img src="./libs/modules/home/graph.projects.php"
					 title="<?=$_LANG->get('Auftr&auml;ge der letzten 30 Tage')?>">*/?>
			</div>
		</td>
		<td>&nbsp;</td>
		<td style="vertical-align: top">
			<div class="box1">
				<b><?=$_LANG->get('Auftr&auml;ge der letzten Monate')?></b> <br/>
				
				<img src="./libs/modules/statistics/graph.projects.month.php?temporary=1">
			</div>
		</td>
	</tr>
	<tr><td>&ensp;</td></tr>
	<tr>
		<td style="vertical-align: top">
			<div class="box1">
				<table class="standard" style="border-spacing: 3; width: 40%">
					<tr>
						<td colspan="4" class="content_row_header"><?=$_LANG->get('Auftr&auml;ge der letzten 30 Tage tabellarisch')?>
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Datum')?></td>
						<td class="content_row_header" colspan="3"><?=$_LANG->get('Auftr&auml;ge')?>
						</td>
					</tr>
					<? 
					for($x=1;$x<=30;$x++){?>
					<tr>
						<td><?
						echo date('d.m.Y', strtotime('-'.$x.' days'));?>
						</td>
						<td
							style='background-color: #FF5a5a; width: 50px; text-align: center'>
							<? 
							$res1 = ($day[date('Y-m-d', strtotime('-'.$x.' days'))][1] + $day[date('Y-m-d', strtotime('-'.$x.' days'))][2]);
  							if($res1 > 0) echo $res1;?>
						</td>
						<td
							style='background-color: #aaaaaa; width: 50px; width: 50px; text-align: center'>
							<? $res2 = ($day[date('Y-m-d', strtotime('-'.$x.' days'))][3] + $day[date('Y-m-d', strtotime('-'.$x.' days'))][4]); 
  							if($res2 > 0) echo $res2;?>
						</td>
						<td
							style='background-color: B4F277; width: 50px; width: 50px; text-align: center'>
							<? echo $day[date('Y-m-d', strtotime('-'.$x.' days'))][5];?>
						</td>
					</tr>
					<?
					}?>
				</table>
			</div>
		</td>
		<td>&nbsp;</td>
		<td style="vertical-align: top">
			<div class="box1">
				<table>
					<tr>
						<td colspan="2" class="content_row_header"><?=$_LANG->get('Auftr&auml;ge der letzten Monate tabellarisch')?>
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Monat')?></td>
						<td class="content_row_header" colspan="3"
							style='text-align: center'><?=$_LANG->get('Auftr&auml;ge')?></td>
					</tr>
					<? 
					for($x=1;$x<=12;$x++){?>
					<tr class="<?=getRowColor($x)?>">
						<td class="content_row"><?
						echo date('m.Y', strtotime('-'.$x.' months'));
						?>
						
						<td class="content_row" style='width: 150px; text-align: center'>
							<? 
							echo $month[date('m.Y', strtotime('-'.$x.' months'))];
							?> &nbsp;
						</td>
					</tr>
					<?
					} ?>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Summe')?>:</td>
						<td class="content_row_header"
							style='width: 150px; text-align: center'><?=$sum_months?></td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
	<tr>
		<td style="visibility: hidden"><img
			src="./libs/modules/statistics/graph.projects.php"></td>
		<td style="visibility: hidden"><img
			src="./libs/modules/statistics/graph.projects.month.php"></td>
	</tr>
</table>
<br />
<br />
<div>
	<ul class="postnav_save">
		<a href="libs/modules/statistics/pdf.orders_day_and_months.php"
			target="_blank"><?=$_LANG->get('PDF-Anzeige')?> </a>
	</ul>
</div>


<br />
<br />
