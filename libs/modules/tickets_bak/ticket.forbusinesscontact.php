<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			25.06.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'ticket.class.php';

$filter["cust"] 	= $busiconID;		// siehe ./libs/modules/businesscontact/businesscontact.add.php

//  Sortierung verwalten
$orderby = Ticket::ORDER_TITLE;
$orderhow = " ASC ";

$all_tickets = Ticket::getAllTickets($orderby.$orderhow, $filter);

$alluser = User::getAllUser();
//$allcustomer = BusinessContact::getAllBusinessContacts(BusinessContact::ORDER_NAME);
?>

<table width="100%" cellpadding="0" cellspacing="0">
	<colgroup>
		<col width="80">
		<col>
		<col width="120">
		<col width="120">
		<col width="150">
		<col width="80">
		<col width="130">
		<col width="40">
		<col width="70">
	</colgroup>
	<tr>
		<td class="content_row_header"><?=$_LANG->get('Nr.')?></td>
		<td class="content_row_header"><?=$_LANG->get('Titel')?></td>
		<td class="content_row_header"><?=$_LANG->get('Erstellt von ')?></td>
		<td class="content_row_header"><?=$_LANG->get('Erstellt am ')?></td>
		<td class="content_row_header"><?=$_LANG->get('Verantwortliche(r)')?></td>
		<td class="content_row_header"><?=$_LANG->get('F&auml;llig am')?></td>
		<td class="content_row_header"><?=$_LANG->get('Status')?></td>
		<td class="content_row_header" align="center">&ensp;</td>
		<td class="content_row_header" align="center"><?=$_LANG->get('Optionen')?></td>
	</tr>
	<? if(count($all_tickets) > 0 && $all_tickets != FALSE){
		$x = 0;
		foreach($all_tickets as $ticket){?>
		<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId()?>'">
				<?=$ticket->getTicketnumber()?><?/*gln*/if($ticket->getPrivat() == 1) echo("(P)") ?> &ensp;
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId()?>'">
				<?=$ticket->getTitle()?>
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId()?>'">
				<?if ($ticket->getCrtuser()->getId() > 0){
						echo $ticket->getCrtuser()->getNameAsLine();
				} else {
					echo $_LANG->get('n.A.');
				}?>
			</td> 
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId()?>'">
			<?	if ($ticket->getCrtdate() > 0){
					echo date('d.m.Y - H:i',$ticket->getCrtdate());
				}?>    
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId()?>'">
				<?=$ticket->getContactperson()->getNameAsLine()?> &ensp;
				<?if ($ticket->getContactperson2()->getId() >0){
						echo "<br/>".$ticket->getContactperson2()->getNameAsLine();
				}?>
				<?if ($ticket->getContactperson3()->getId() >0){
						echo  "<br/>".$ticket->getContactperson3()->getNameAsLine();
				}?>    
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
		<?	if(Ticketcomment::getLastComment($ticket->getId()) != NULL && Ticketcomment::getLastComment($ticket->getId()) != ""){ ?>
				<img src="./images/icons/balloon-ellipsis.png" alt="Kommentar" 
					 title="<?=Ticketcomment::getLastComment($ticket->getId())?>" />	 
		<?	} else {
				echo "&emsp;";
			} ?>
				
				</td>
				<td class="content_row" align="center">
                	<a class="icon-link" href="index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId()?>"><img src="images/icons/pencil.png" title="<?=$_LANG->get('Details');?>"></a>
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