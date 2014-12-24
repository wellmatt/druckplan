<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       05.06.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------
?>
	
<table cellpadding="0" cellspacing="0" width="100%" style="table-layout: fixed">
	<colgroup>
		<col width="610" valign="top">
		<col width="16">
		<col width="364" valign="top">
	</colgroup>
	<tr>
		<td valign="top">
				<b><?=$_LANG->get('Auftr&auml;ge der letzten 30 Tage')?></b> <br/> 
				<img src="./libs/modules/statistics/graph.projects.php?temporary=1">
				<?// style="visibility: hidden" 
				  // <img src="./temp/graph.projects.jpg">?>
		</td>
		<td>&nbsp;</td>
		<td valign="top">
				<b><?=$_LANG->get('Auftr&auml;ge der letzten Monate')?></b> <br/>
				<img src="./libs/modules/statistics/graph.projects.month.php?temporary=1">
				<?//<img src="./temp/graph.projects.month.jpg">?>
		</td>
	</tr>
	<tr>
		<td valign="top" colspan="3" style="height: 400px;">
				<? //require_once 'libs/modules/statistics/schedule.machines.stats.php';?>
				<iframe frameborder="0" src="./libs/modules/statistics/schedule.machines.stats.php" width="100%" height="100%"></iframe>
		</td>
	</tr>
	<tr>
		<td valign="top" colspan="3" style="height: 750px;">
				<? //require_once 'libs/modules/statistics/ticket.stats.php';?>
				<iframe frameborder="0" src="./libs/modules/statistics/ticket.stats.php" width="100%" height="100%"></iframe>
		</td>
	</tr>
	<tr>
		<td valign="top" colspan="3" style="height: 450px;">
				<? //require_once 'libs/modules/statistics/ticket.stats.php';?>
				<iframe frameborder="0" src="./libs/modules/statistics/order.statistic.php" width="100%" height="100%"></iframe>
		</td>
	</tr>
</table>
