<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			26.06.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
//
//	Diese Datei zeigt die ueberfaelligen Tickets des eigeloggten Benutzers an 
//	(z.B. fuer die Home-Seite)
//
// ----------------------------------------------------------------------------------
require_once 'ticket.class.php';

$all_tickets = Ticket::getAllDueTickets(Ticket::ORDER_DUE);
?>


<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.1/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    var due_tickets = $('#due_tickets').DataTable( {
        "scrollY": "350px",
        "paging": true,
		"stateSave": true,
		"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Alle"] ],
		"language": 
					{
						"emptyTable":     "Keine Daten vorhanden",
						"info":           "Zeige _START_ bis _END_ von _TOTAL_ Eintr&auml;gen",
						"infoEmpty": 	  "Keine Seiten vorhanden",
						"infoFiltered":   "(gefiltert von _MAX_ gesamten Eintr&auml;gen)",
						"infoPostFix":    "",
						"thousands":      ".",
						"lengthMenu":     "Zeige _MENU_ Eintr&auml;ge",
						"loadingRecords": "Lade...",
						"processing":     "Verarbeite...",
						"search":         "Suche:",
						"zeroRecords":    "Keine passenden Eintr&auml;ge gefunden",
						"paginate": {
							"first":      "Erste",
							"last":       "Letzte",
							"next":       "N&auml;chste",
							"previous":   "Vorherige"
						},
						"aria": {
							"sortAscending":  ": aktivieren um aufsteigend zu sortieren",
							"sortDescending": ": aktivieren um absteigend zu sortieren"
						}
					}
    } );
    $('a.due_tickets-toggle-vis').on( 'click', function (e) {
        e.preventDefault();
        var column = due_tickets.column( $(this).attr('data-column') );
        column.visible( ! column.visible() );
    } );
} );
</script>

<?/*gln 29.01.14*/?>
<h1><?=$_LANG->get('Meine &uuml;berf&auml;lligen Tickets')?></h1> 
<div>
Toggle: 
	<a data-column="0" class="due_tickets-toggle-vis"><?=$_LANG->get('Nr.')?></a> - 
	<a data-column="1" class="due_tickets-toggle-vis"><?=$_LANG->get('Titel')?></a> - 
	<a data-column="2" class="due_tickets-toggle-vis"><?=$_LANG->get('Erstellt')?></a> - 
	<a data-column="3" class="due_tickets-toggle-vis"><?=$_LANG->get('F&auml;llig am')?></a> - 
	<a data-column="4" class="due_tickets-toggle-vis"><?=$_LANG->get('Status')?></a> - 
	<a data-column="5" class="due_tickets-toggle-vis"><?=$_LANG->get('Kommentar')?></a>
</div>
<table id="due_tickets" cellpadding="0" cellspacing="0">
	<? if (count($all_tickets) >= 1 && $all_tickets != false){ ?>
        <thead>
            <tr>
                <th><?=$_LANG->get('Nr.')?></th>
                <th><?=$_LANG->get('Titel')?></th>
                <th><?=$_LANG->get('Erstellt')?></th>
                <th><?=$_LANG->get('F&auml;llig am')?></th>
                <th><?=$_LANG->get('Status')?></th>
                <th><?=$_LANG->get('Kommentar')?></th>
            </tr>
        </thead>
	<?	$x = 0;
		foreach($all_tickets as $ticket){
			if($ticket->getState() == 1) $ticketstate = 'red.gif';
			if($ticket->getState() == 2) $ticketstate = 'orange.gif';
			if($ticket->getState() == 3) $ticketstate = 'yellow.gif';
			if($ticket->getState() == 4) $ticketstate = 'lila.gif';
			if($ticket->getState() == 5) $ticketstate = 'green.gif';?>
			<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
				<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId()?>'">
					<?=$ticket->getTicketnumber()?><?/*gln*/if($ticket->getPrivat() == 1) echo("(P)") ?>&ensp;
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId()?>'">
					<?=$ticket->getTitle()?>
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId()?>'">
					<?	if ($ticket->getCrtdate() > 0){
						echo date('d.m.Y',$ticket->getCrtdate());
					}?>
					<?if ($ticket->getCrtuser()->getId() > 0){
							echo " von <br>".$ticket->getCrtuser()->getNameAsLine();
					} else {
						echo $_LANG->get('n.A.');
					}?>
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId()?>'">
					<?if($ticket->getDue() != 0){ echo date('d.m.Y', $ticket->getDue());}?> &ensp; 
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId()?>'">
				<?	// Ticketstatus ausgeben ?>
					<? // if($ticket->getState1() >0) echo "A: ".getTicketStatus1($ticket->getState1(), true)."<br/>"?>
					<? if($ticket->getState3() >0) echo "V: ".getTicketStatus3($ticket->getState3(), true)."<br/>"?>
					<? if($ticket->getState2() >0) echo "G/P: ".getTicketStatus2($ticket->getState2(), true)."<br/>"?>
					<? if($ticket->getState4() >0) echo "K: ".getTicketStatus4($ticket->getState4(), true)."<br/>"?>
					<!-- table border="0" cellpadding="1" cellspacing="0">
		                <tr>
		                	<? if($ticket->getState1() > 0 && $ticket->getState1() < 11){?>
			                	<td width="50">
			                        &emsp; A: <br/> 
			                        <img class="select" src="./images/status/<?=$ticket->getActiveTicketStatusImg(1)?>" 
			                        		title="<?=getTicketStatus1($ticket->getState1())?> ">
			                    </td>
		                    <? } 
		                    if($ticket->getState3() > 0){ ?>
			                    <td width="50">
			                        &ensp; V: <br/> 
			                        <img class="select" src="./images/status/<?=$ticket->getActiveTicketStatusImg(3)?>" 
			                        		title="<?=getTicketStatus3($ticket->getState3())?> ">
			                    </td>
		                    <? } 
		                    if($ticket->getState2() > 0){ ?>
								<td width="50" >
			                        &ensp; P: <br/> 
			                        <img class="select" src="./images/status/<?=$ticket->getActiveTicketStatusImg(2)?>" 
			                        		title="<?=getTicketStatus2($ticket->getState2())?> ">
			                    </td>
		                    <? } 
		                    if($ticket->getState4() > 0){ ?>
			                    <td width="50">
			                        &ensp; K: <br/> 
			                        <img class="select" src="./images/status/<?=$ticket->getActiveTicketStatusImg(4)?>" 
			                        		title="<?=getTicketStatus4($ticket->getState4())?> ">
			                    </td>
		                    <? } ?>
			             </tr>
                	</table-->
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$ticket->getId()?>'">	
				<?	// letzten 3 Kommentare ausgeben
					if(Ticketcomment::getLastComment($ticket->getId()) != NULL && Ticketcomment::getLastComment($ticket->getId()) != ""){?>
						<img src="./images/icons/balloon-ellipsis.png" alt="Kommentar" 
							 title="<?=Ticketcomment::getLastComment($ticket->getId())?>"  />	 
				<?	} else {
						echo "&ensp;";
					}?>
				</td>
			</tr>
			<? $x++;
		}
	} else {
		echo '<tr class="'.getRowColor(0) .'"> <td colspan="6" align="center" class="content_row">';
		echo '<span class="error">'.$_LANG->get('Keine Tickets &uuml;berf&auml;llig.').'</span>';
		echo '</td></tr>';
	}
	?>
</table>