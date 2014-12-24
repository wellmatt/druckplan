<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			02.09.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'event.class.php';

if ($main_searchstring != "" && $main_searchstring!=NULL){
	// $main_searchstring siehe /libs/basic/home.php
	$all_events= Event::getAllEventsForHome(Event::ORDER_TITLE, $main_searchstring);
} else {
	$all_events=FALSE;
}
?>
<h1><?=$_LANG->get('Suchergebnisse Termine');?></h1>
<table width="100%" cellpadding="0" cellspacing="0">
	<colgroup>
		<col width="40">
		<col>
		<col width="450">
		<col width="180">
		<col width="180">
		<col width="30">
	</colgroup>
	<? if(count($all_events) > 0 && $all_events != FALSE){?>
	<tr>
		<td class="content_row_header"><?=$_LANG->get('Nr.')?></td>
		<td class="content_row_header"><?=$_LANG->get('Titel')?></td>
		<td class="content_row_header"><?=$_LANG->get('Beschreibung')?></td>
		<td class="content_row_header"><?=$_LANG->get('Start')?></td>
		<td class="content_row_header"><?=$_LANG->get('Ende')?></td>
		<td class="content_row_header" align="center">&ensp;</td>
	</tr>
	<?	$x = 0;
		foreach($all_events as $event){?>
			<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
				<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/organizer/calendar.php&exec=newevent&id=<?=$event->getId()?>'">
					<?=$x+1?>
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/organizer/calendar.php&exec=newevent&id=<?=$event->getId()?>'">
					<?=$event->getTitle()?>
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/organizer/calendar.php&exec=newevent&id=<?=$event->getId()?>'">
					<?= substr($event->getDesc(), 0, 150)?> 
					<? if(strlen($event->getDesc()) > 150) echo "...";?> &ensp;
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/organizer/calendar.php&exec=newevent&id=<?=$event->getId()?>'">
					<?=date("d.m.Y - H:i",$event->getBegin())?> &ensp; 
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/organizer/calendar.php&exec=newevent&id=<?=$event->getId()?>'">
					<?=date("d.m.Y - H:i",$event->getEnd())?> &ensp; 
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/organizer/calendar.php&exec=newevent&id=<?=$event->getId()?>'">
					&ensp; 
				</td>
			</tr>
			<? $x++;
		} 
	} else {
		echo '<tr class="'.getRowColor(0) .'"> <td colspan="6" align="center" class="content_row">';
		echo '<span class="error">'.$_LANG->get('Keine Termine gefunden.').'</span>';
		echo '</td></tr>';
	}
	?>
</table>