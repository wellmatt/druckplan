<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			25.06.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/schedule/schedule.class.php';

//Wenn gesucht wird 
if ($_REQUEST["subexec"]=="search"){
	$_SESSION["ticket"]["filter_user"] 			= $_REQUEST["filter_crtuser"];
	$_SESSION["ticket"]["filter_contact"] 		= $_REQUEST["filter_contactperson"];
	$_SESSION["ticket"]["filter_cust"] 			= $_REQUEST["filter_cust"];
	$_SESSION["ticket"]["filter_status"] 		= $_REQUEST["filter_status"];
	$_SESSION["ticket"]["filter_status_value"] 	= $_REQUEST["filter_status_value"]; 
    /* gln 28.01.2014 zusaetzliche Filteroption: Anzeige privater Tickets */
	$_SESSION["ticket"]["filter_privat"]		= $_REQUEST["filter_privat"]; 
}

// Filter setzen
$filter["user"] 		= $_SESSION["ticket"]["filter_user"];
$filter["contact"] 		= $_SESSION["ticket"]["filter_contact"];
$filter["cust"] 		= $_SESSION["ticket"]["filter_cust"];
$filter["status"] 		= $_SESSION["ticket"]["filter_status"];
$filter["status_value"] = $_SESSION["ticket"]["filter_status_value"];
/* gln 28.01.2014 zusaetzliche Filteroption: Anzeige privater Tickets */
$filter["privat"]		= $_SESSION["ticket"]["filter_privat"];

if ($_REQUEST["archiv"] == 1){
	$archiv_on = 1;
	$archiv_output = 0;	// Fuer Links um umzuschalten
	$filter["archiv"] = 1;
	$all_tickets = Ticket::getAllTickets(Ticket::ORDER_TITLE, $filter);
} else {
	//  Sortierung verwalten
	$orderby = Ticket::ORDER_CRTDATE;
	if($_REQUEST["orderby"] == "number") $orderby = Ticket::ORDER_NUMBER;
	if($_REQUEST["orderby"] == "title") $orderby = Ticket::ORDER_TITLE;
	if($_REQUEST["orderby"] == "contactperson") $orderby = Ticket::ORDER_CUSTOMER;
	if($_REQUEST["orderby"] == "due") $orderby = Ticket::ORDER_DUE;
	if($_REQUEST["orderby"] == "cust") $orderby = Ticket::ORDER_CUSTOMER;
	if($_REQUEST["orderby"] == "crtdate") $orderby = Ticket::ORDER_CRTDATE;
	
	$orderhow = " DESC";
	
	if($_SESSION["ticket"]["order"] == $_REQUEST["orderby"] && $_REQUEST["orderby"] != NULL){
		if($_SESSION["ticket"]["orderhow"] == " ASC"){
			$orderhow = " DESC";
		} else {
			$orderhow = " ASC";
		}
	}
	// Sortierung in Session schreiben, zum merken
	$_SESSION["ticket"]["order"] = $_REQUEST["orderby"];
	$_SESSION["ticket"]["orderhow"] = $orderhow;
	
	$archiv_on = 0;
	$archiv_output = 1;
	$all_tickets = Ticket::getAllTickets($orderby.$orderhow, $filter);
} 

$alluser = User::getAllUser();
$allcustomer = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, BusinessContact::FILTER_ALL);
?>

<? // echo $DB->getLastError();?>

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.1/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/date-uk.js"></script>

<script type="text/javascript">
<? // Status der Abteilung aktualisieren?>
function UpdateStatusValues(statusID){
	$.post("libs/modules/tickets/ticket.ajax.php", 
		{ajax_action: 'updateStatusSearch', statusID : statusID}, 
		 function(data) {
			var input = '<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>'+data;
			document.getElementById('filter_status_value').innerHTML = input;
		});
}
function ResetForm()
{
	document.getElementById('filter_crtuser').selectedIndex = -1;
	document.getElementById('filter_contactperson').selectedIndex = -1;
	document.getElementById('filter_cust').selectedIndex = -1;
	document.getElementById('filter_status').selectedIndex = -1;
	document.getElementById('filter_privat').value = "";
	document.getElementById('filter_status_value').selectedIndex = -1;
}

jQuery.fn.dataTableExt.oSort['uk_date-asc']  = function(a,b) {
    var ukDatea = a.split('.');
    var ukDateb = b.split('.');
     
    var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;
     
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['uk_date-desc'] = function(a,b) {
    var ukDatea = a.split('.');
    var ukDateb = b.split('.');
     
    var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;
     
    return ((x < y) ? 1 : ((x > y) ?  -1 : 0));
};

$(document).ready(function() {
    var tickets = $('#tickets').DataTable( {
        "paging": true,
		"stateSave": true,
		"pageLength": 50,
		"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Alle"] ],
		"aoColumnDefs": [ { "sType": "uk_date", "aTargets": [ 4, 7 ] } ],
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
} );
</script>

<table width="100%">
	<tr>
		<td width="150" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
			<span style="font-size: 13px"><?=$_LANG->get('Tickets')?></span>
		</td>
		<td width="250" class="content_header" align="right">
		<?=$savemsg?>
		</td>
	</tr>
</table>

<script type="text/javascript">
	UpdateStatusValues(<?=$filter["status"]?>);
</script>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="xform_ticketsearch">
	<input type="hidden" name="subexec" value="search">
	<input type="hidden" name="mid" value="<?=$_REQUEST["mid"]?>">
	<table>
	<tr>
		<td>
			<div class="box2" style="width:630px;" >
				<table width="100%" cellpadding="0" cellspacing="0">
					<colgroup>
						<col width="120">
						<col>
						<col width="120">
						<col>
					</colgroup>
					<tr>
						<td class="content_row_header" colspan="2">Filteroptionen</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Erstellt von')?></td>
						<td class="content_row_clear">
							<select id="filter_crtuser" name="filter_crtuser" style="width:150px"
									onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
								<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
							<? 	foreach ($alluser as $us){?>
									<option value="<?=$us->getId()?>"
										<?if ($filter["user"] == $us->getId()) echo "selected" ?>><?= $us->getNameAsLine()?></option>
							<?	} //Ende ?>
							</select>
						</td>
		
						<td class="content_row_header"><?=$_LANG->get('Verantwortlicher')?></td>
						<td class="content_row_clear">
							<select id="filter_contactperson" name="filter_contactperson" style="width:150px"
									onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
								<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
							<? 	foreach ($alluser as $us){?>
									<option value="<?=$us->getId()?>"
										<?if ($filter["contact"] == $us->getId()) echo "selected" ?>><?= $us->getNameAsLine()?></option>
							<?	} //Ende ?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="content_row_header">Kunde</td>
						<td class="content_row_clear">
							<select id="filter_cust" name="filter_cust" style="width:150px"
								onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
								<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
							<? 	foreach ($allcustomer as $cust){?>
									<option value="<?=$cust->getId()?>"
										<?if ($filter["cust"] == $cust->getId()) echo "selected" ?>><?= $cust->getNameAsLine()?></option>
							<?	} ?>
							</select>
						</td>
		
						<td class="content_row_header"><?=$_LANG->get('Abteilung')?></td>
						<td class="content_row_clear">
							<select id="filter_status" name="filter_status" style="width:150px"
									onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text"
									onchange="UpdateStatusValues(this.value)">
								<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
								<!-- option value="1" <?if ($filter["status"] == 1) echo "selected" ?>><?=$_LANG->get('Algemein')?></option-->
								<option value="3" <?if ($filter["status"] == 3) echo "selected" ?>><?=$_LANG->get('Vertrieb')?></option>
								<option value="2" <?if ($filter["status"] == 2) echo "selected" ?>><?=$_LANG->get('Grafik/Produktion')?></option>
								<option value="4" <?if ($filter["status"] == 4) echo "selected" ?>><?=$_LANG->get('Kunden')?></option>
							</select>
						</td>
					</tr>
					<tr>
						<?/*<td class="content_row_header">&emsp;</td>
						    <td class="content_row_header">&emsp;</td>
						*/?>    
					<?/* gln 28.01.2014 zusaetzliche Filteroption: Anzeige privater Tickets */?>
					<td class="content_row_header"><?=$_LANG->get('Private anzeigen');?></td>
					<td class="content_row_clear">
    	                <input type="checkbox" id="filter_privat" name="filter_privat" value="1" class="text" <?if ($filter["privat"] == 1) echo 'checked="checked"'?>>
	                </td>
						<td class="content_row_header">
							<span style="display:<?=$view_cust_cp?>;" id="span_status_value">
								<?=$_LANG->get('Status')?>:
							</span>
						</td>
						<td class="content_row_clear">
							<select id="filter_status_value" name="filter_status_value" style="width:150px" 
									onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
								<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
							</select>
						</td>
					</tr>
				<tr>
					<td >&emsp;</td> 
						<td class="content_row_clear" align="right" colspan="2" >
							<input type="submit" value="<?=$_LANG->get('Zurücksetzen')?>" onclick="ResetForm(); location.href = 'index.php';">
						</td>
						<td class="content_row_clear" align="right" colspan="2" >
							<input type="submit" value="<?=$_LANG->get('Suche starten')?>">
						</td>
				</tr>
				</table>
			</div>
		</td>
		<td width="120">&emsp;&emsp;</td>
		<td>
			<div class="box2" style="width:600px;min-height:150px;">
				<br/><br/>
				<a href="index.php?page=<?=$_REQUEST['page']?>&archiv=<?=$archiv_output?>" class="icon-link">
					<img src="images/icons/databases.png"> 
					<span style="font-size: 13px">
						<? if ($archiv_on == 0 ) echo $_LANG->get('Archiv'); else echo $_LANG->get('Alle Tickets'); ?>
					</span>
				</a>
				<br/><br/>
				<a href="index.php?page=<?=$_REQUEST['page']?>&exec=new" class="icon-link">
					<img src="images/icons/ticket--plus.png"> 
					<span style="font-size: 13px"><?=$_LANG->get('Ticket erstellen')?></span>
				</a>
			</div>
		</td>
	</tr>
	</table>
</form>
<br/>
<div class="box1">
	<table id="tickets" width="100%" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="80"><?=$_LANG->get('Nr.')?></th>
                <th width="100"><?=$_LANG->get('Planung')?></th>
                <th><?=$_LANG->get('Titel')?></th>
                <th width="120"><?=$_LANG->get('Erstellt von ')?></th>
                <th width="80"><?=$_LANG->get('Erstellt am ')?></th>
                <th width="150"><?=$_LANG->get('Verantwortliche(r)')?></th>
                <th width="250"><?=$_LANG->get('Kunde/Lieferant')?></th>
                <th width="60"><?=$_LANG->get('F&auml;llig am')?></th>
                <th width="150"><?=$_LANG->get('Status')?></th>
                <th width="40">&ensp;</th>
                <th width="40">&ensp;</th>
            </tr>
        </thead>
		<? if(count($all_tickets) > 0 && $all_tickets != FALSE){
			$x = 0;
			foreach($all_tickets as $ticket){?>
			<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$ticket->getId()?>'">
					<?=$ticket->getTicketnumber()?><?/*gln*/if($ticket->getPrivat() == 1) echo("(P)") ?>  &ensp;
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$ticket->getId()?>'">
					<a href="index.php?pid=<?=$_CONFIG->planPid?>&exec=parts&id=<?=$ticket->getPlanning()->getId()?>"
						><?=$ticket->getPlanning()->getNumber()?></a>
					
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$ticket->getId()?>'">
					<?=$ticket->getTitle()?>
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$ticket->getId()?>'">
					<?if ($ticket->getCrtuser()->getId() > 0){
							echo $ticket->getCrtuser()->getNameAsLine();
					} else {
						echo $_LANG->get('n.A.');
					}?>
				</td> 
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$ticket->getId()?>'">
				<?	if ($ticket->getCrtdate() > 0){
						echo date('d.m.Y',$ticket->getCrtdate()); //  - H:i
					}?>    
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$ticket->getId()?>'">
					<?=$ticket->getContactperson()->getNameAsLine()?> &ensp;
					<?if ($ticket->getContactperson2()->getId() >0){
							echo "<br/>".$ticket->getContactperson2()->getNameAsLine();
					}?>
					<?if ($ticket->getContactperson3()->getId() >0){
							echo  "<br/>".$ticket->getContactperson3()->getNameAsLine();
					}?>    
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$ticket->getId()?>'">
					<?=$ticket->getCustomer()->getNameAsLine()?> &ensp; 
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$ticket->getId()?>'">
					<?if($ticket->getDue() != 0){ echo date('d.m.Y', $ticket->getDue());}?> &ensp;
				</td>
				<td class="content_row" align="left">
					<? // if($ticket->getState1() >0) echo "A: ".getTicketStatus1($ticket->getState1(), true)."<br/>"?>
					<? if($ticket->getState3() >0) echo "V: ".getTicketStatus3($ticket->getState3(), true)."<br/>"?>
					<? if($ticket->getState2() >0) echo "G/P: ".getTicketStatus2($ticket->getState2(), true)."<br/>"?>
					<? if($ticket->getState4() >0) echo "K: ".getTicketStatus4($ticket->getState4(), true)."<br/>"?>
					<? // wenn kein Status aktiv, muss ein Leerzeichen ausgegeben werden, damit die Linien korrekt gezeichnet werden 
						if($ticket->getState2() == 0 && $ticket->getState3() == 0 && $ticket->getState4() == 0) echo "&emsp;"?>
				</td>
				<td class="content_row pointer icon-link" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$ticket->getId()?>'" align="center">
				<?	$notes_info = $ticket->getNotesInfo();
					if($notes_info != "" && $notes_info != NULL){ ?>
					<img src="./images/icons/navigation-090-white.png" 
							title="<?=$notes_info?>" alt="<?=$notes_info?> " >
				<?	} else {
						echo "&ensp;";
					} ?>
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$ticket->getId()?>'" align="center" >
				
			<?	
				if (Ticketcomment::getLastCommentStatus($ticket->getId()) != FALSE){?>
					<img src="./images/status/<?
						if(Ticketcomment::getLastCommentStatus($ticket->getId()) == 1){ echo 'white_small.gif';} 
						elseif(Ticketcomment::getLastCommentStatus($ticket->getId()) == 2){ echo 'lila_small.gif';} 
						elseif(Ticketcomment::getLastCommentStatus($ticket->getId()) == 3){ echo 'green_small.gif';} 
						else { echo 'black_small.gif';}?>"
						class="select" title="<?=Ticketcomment::getLastComment($ticket->getId())?>"  width="10">
				<?} else {
					echo "&emsp;";
				} ?>
				
				</td>
			</tr>
			<? $x++;
			} 
		} else {
			echo '<tr class="'.getRowColor(0) .'"> <td colspan="9" align="center" class="content_row">';
			echo '<span class="error">'.$_LANG->get('Keine Tickets gefunden.').'</span>';
			echo '</td></tr>';
		}
		?>
	</table>
</div>