<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			17.07.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
//
//		Diese Datei behandelt die globale Suchfunktion auf der Startseite
//
// ----------------------------------------------------------------------------------
require_once 'ticket.class.php';

if ($main_searchstring != "" && $main_searchstring!=NULL){
	// $main_searchstring siehe /libs/basic/home.php
	$all_tickets= Ticket::getAllTicketsForHome(Ticket::ORDER_TITLE,$main_searchstring);
} else {
	$all_tickets=FALSE;
}
?>
<h1><?=$_LANG->get('Suchergebnisse Tickets');?></h1>
<table width="100%" cellpadding="0" cellspacing="0">
	<colgroup>
		<col width="80">
		<col>
		<col width="150">
		<col width="180">
		<col width="80">
		<col width="130">
		<col width="30">
	</colgroup>
	<? if(count($all_tickets) > 0 && $all_tickets != FALSE){?>
	<tr>
		<td class="content_row_header"><?=$_LANG->get('Nr.')?></td>
		<td class="content_row_header"><?=$_LANG->get('Titel')?></td>
		<td class="content_row_header"><?=$_LANG->get('Verantwortlicher (intern)')?></td>
		<td class="content_row_header"><?=$_LANG->get('Kunde/Lieferant')?></td>
		<td class="content_row_header"><?=$_LANG->get('F&auml;llig am')?></td>
		<td class="content_row_header"><?=$_LANG->get('Status')?></td>
		<td class="content_row_header" align="center">&ensp;</td>
	</tr>
	<?	$x = 0;
		foreach($all_tickets as $ticket){?>
		<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId()?>'">
				<a href="index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId();?>" title="Ticketdetails"><?=$ticket->getTicketnumber()?><?/*gln*/if($ticket->getPrivat() == 1) echo("(P)") ?></a>&ensp;
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId()?>'">
				<a href="index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId();?>" title="Ticketdetails"><?=$ticket->getTitle()?></a>
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId()?>'">
				<?=$ticket->getContactperson()->getNameAsLine()?> &ensp; 
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId()?>'">
				<?=$ticket->getCustomer()->getNameAsLine()?> &ensp; 
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId()?>'">
				<?if($ticket->getDue() != 0){ echo date('d.m.Y', $ticket->getDue());}?> &ensp;
			</td>
			<td class="content_row" align="left">
				<? // if($ticket->getState1() >0) echo "A: ".getTicketStatus1($ticket->getState1(), true)."<br/>"?>
				<? if($ticket->getState3() >0) echo "V: ".getTicketStatus3($ticket->getState3(), true)."<br/>"?>
				<? if($ticket->getState2() >0) echo "G/P: ".getTicketStatus2($ticket->getState2(), true)."<br/>"?>
				<? if($ticket->getState4() >0) echo "K: ".getTicketStatus4($ticket->getState4(), true)."<br/>"?>
			</td>
			<td class="content_row pointer icon-link" onclick="document.location='index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId()?>'" align="center">
		<?	if(Ticketcomment::getLastComment($ticket->getId()) != NULL && Ticketcomment::getLastComment($ticket->getId()) != ""){?>
				<img src="./images/icons/balloon-ellipsis.png" alt="Kommentar" title="<?=Ticketcomment::getLastComment($ticket->getId())?>" />
				<!-- img src="./images/icons/exclamation-octagon.png" alt="Kommentar" title="<?=$ticket->getCommentintern()?>" /-->	 
		<?	} else {
				echo "&ensp;";
			}?>
				&ensp;
				</td>
			</tr>
			<? $x++;
		} 
	} else {
		echo '<tr class="'.getRowColor(0) .'"> <td colspan="8" align="center" class="content_row">';
		echo '<span class="error">'.$_LANG->get('Keine Tickets gefunden.').'</span>';
		echo '</td></tr>';
	}
	?>
</table>