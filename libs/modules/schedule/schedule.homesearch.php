<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			02.09.2013
// Copyright:		2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
//
//		Diese Datei behandelt die globale Suchfunktion auf der Startseite
//
// ----------------------------------------------------------------------------------
require_once 'schedule.class.php';

if ($main_searchstring != "" && $main_searchstring!=NULL){
	// $main_searchstring siehe /libs/basic/home.php
	$schedules = Schedule::getAllSchedulesForHome(Schedule::ORDER_NUMBER, $main_searchstring); 
} else {
	$schedules = FALSE;
}

?>
<h1><?=$_LANG->get('Suchergebnisse Planung');?></h1>
<table width="100%" cellpadding="0" cellspacing="0">
	<colgroup>
		<col width="160">
		<col width="300">
		<col width="380">
		<col width="120">
		<col>
	</colgroup>
	<? if(count($schedules) > 0 && $schedules != FALSE){?>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Auftragsnummer')?></td>
			<td class="content_row_header"><?=$_LANG->get('Kunde')?></td>
			<td class="content_row_header"><?=$_LANG->get('Objekt')?></td>
			<td class="content_row_header"><?=$_LANG->get('Menge')?></td>
			<td class="content_row_header"><?=$_LANG->get('Liefertermin')?></td>
		</tr>
		<?
		$x = 0;
		foreach ($schedules as $sched){
		?>
		<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/schedule/schedule.php&exec=parts&id=<?=$sched->getId()?>'">
				<?=$sched->getNumber()?>&nbsp;
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/schedule/schedule.php&exec=parts&id=<?=$sched->getId()?>'">
				<?=$sched->getCustomer()->getNameAsLine()?>&nbsp;
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/schedule/schedule.php&exec=parts&id=<?=$sched->getId()?>'">
				<?=$sched->getObject()?>&nbsp;
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/schedule/schedule.php&exec=parts&id=<?=$sched->getId()?>'">
				<?=$sched->getAmount()?> <?=$_LANG->get('Stk.'); ?>&nbsp;
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/schedule/schedule.php&exec=parts&id=<?=$sched->getId()?>'">
				<? if($sched->getDeliveryDate() > 0) echo date("d.m.Y",$sched->getDeliveryDate())?> &ensp; 	
			</td>
		</tr>
		<?
		$x++;
		}
	} else {
		echo '<tr class="'.getRowColor(0) .'"> <td colspan="5" align="center" class="content_row">';
		echo '<span class="error">'.$_LANG->get('Keine Eintr&auml;ge gefunden.').'</span>';
		echo '</td></tr>';
	}
	?>
</table>