<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       28.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'schedule.class.php';
require_once 'schedule.machine.class.php';
require_once 'schedule.part.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/tickets/ticket.class.php';

if($_REQUEST["schedule_filter"] != "")
    $_SESSION["schedule_filter"] = $_REQUEST["schedule_filter"];

if($_REQUEST["exec"] == "delete")
{
    $sched = new Schedule((int)$_REQUEST["id"]);
    $savemsg = getSaveMessage($sched->delete());
}

if($_SESSION["schedule_filter"] == "both")
    $schedules = Schedule::getAllSchedules(Schedule::ORDER_ID);
else if($_SESSION["schedule_filter"] == "closed")
    $schedules = Schedule::getAllSchedules(Schedule::ORDER_ID, Schedule::STATUS_ORDER_FINISHED);
else
    $schedules = Schedule::getAllSchedules(Schedule::ORDER_ID, Schedule::STATUS_ORDER_OPEN);


//gln
if($_REQUEST["exec"] == "edit" or $_REQUEST["exec"] == "new")
{
    require_once('schedule.edit.php');
} else {

?>
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.1/css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/date-uk.js"></script>

<link rel="stylesheet" type="text/css" href="./css/schedule.css" />
<script>
function hideShowMachines()
{
	var obj = document.getElementById('div-machines');

	if(obj.style.display == 'none')
	{
		$(function() { $("#div-machines").show("blind"); });
		//obj.style.display = '';
	} else
	{
		$(function() { $("#div-machines").hide("blind"); });
	} 
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
    var schedule = $('#schedule').DataTable( {
        "paging": true,
		"stateSave": true,
		"pageLength": 50,
		"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Alle"] ],
		"aoColumnDefs": [ { "sType": "uk_date", "aTargets": [ 6 ] } ],
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

<ul class="graphicalButton pointer" onclick="hideShowMachines()"><?=$_LANG->get('Maschinen anzeigen')?></ul>

<div id="div-machines" class="box1" style="display:none;">
<table class="content_table" border="0" cellspacing="0" cellpadding="3" width="100%">
<tr>
<? 
$groups = MachineGroup::getAllMachineGroups();
$width= (int)(100 / count($groups));
foreach($groups as $g) {
?>
    <td class="content_row_header" width="<?=$width?>%"><b><?=$g->getName()?></b></td>
<?  } ?>
</tr>

<tr>
<? 
foreach($groups as $g) {
?>

    <td class="content_row" valign="top">
        <ul>
        <? 
        $machs = Machine::getAllMachines(Machine::ORDER_NAME, $g->getId());
        foreach ($machs as $m)
            echo '<li><a href="index.php?page='.$_REQUEST['page'].'&exec=showmachine&id='.$m->getId().'">'.$m->getName().'</a></li>';
        ?>
        </ul>
        &nbsp;
    </td>
<? } ?>
</tr>
</table>
</div>
<br>

<? 

// Alle Seiten die die Maschinen�bersicht haben sollen.
if($_REQUEST["exec"] == "parts")
{
    require_once 'schedule.parts.php';
} else if($_REQUEST["exec"] == "editparts")
{
    require_once 'schedule.parts.edit.php'; 
} else if($_REQUEST["exec"] == "showmachine")
{
    require_once 'schedule.machine.php';
} else
{

?>

<table>
    <tr>
        <td class="content_row_clear" width="450">
            <b><? echo count($schedules); echo " ".$_LANG->get('Auftr&auml;ge wurden gefunden')?>. <br>
            <?=$_LANG->get('Die Anzeige wurde aus Performancegr&uuml;nden auf 200 Datens&auml;tze limitiert!')?></b><br>
            <?=$_LANG->get('Um die Details einzusehen, auf die Auftr.-Nr. klicken.')?>
        </td>
        <td class="content_row_clear" valign="bottom">
            <form method="post" action="index.php?page=<?=$_REQUEST['page']?>" name="filter_form" id="filter_form">
                <?=$_LANG->get('Filter')?>:
                <select name="schedule_filter" style="width:300px" onchange="document.getElementById('filter_form').submit()">
                    <option value="both" <?if($_SESSION["schedule_filter"] == "both") echo "selected"?>><?=$_LANG->get('Offene und geschlossene Auftr&auml;ge')?></option>
                    <option value="open" <?if($_SESSION["schedule_filter"] == "open") echo "selected"?>><?=$_LANG->get('Offene Auftr&auml;ge')?></option>
                    <option value="closed" <?if($_SESSION["schedule_filter"] == "closed") echo "selected"?>><?=$_LANG->get('Erledigte Auftr&auml;ge')?></option>
                </select>
            </form>
        </td>
        <td class="content_row_header" align="right" valign="bottom" width="180"><?/*gln*/?>
            <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=new"><img src="images/icons/calendar--plus.png"> <?=$_LANG->get('Neuen Auftrag hinzuf&uuml;gen')?></a>
        </td>
    </tr>
</table>


<div class="box1">
<table id="schedule" border="0" cellpadding="0" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th><?=$_LANG->get('ID')?></th>
			<th><?=$_LANG->get('Ersteller')?></th>
			<th><?=$_LANG->get('Auftr.-Nr.')?></th>
			<th><?=$_LANG->get('Kunde')?></th>
			<th><?=$_LANG->get('Objekt')?></th>
			<th><?=$_LANG->get('ges. Auflage')?></th>
			<th><?=$_LANG->get('LT')?></th>
			<th><?=$_LANG->get('Lieferort')?></th>
			<th><?=$_LANG->get('Versandart')?></th>
			<th><?=$_LANG->get('Bemerkungen')?></th>
			<th><?=$_LANG->get('Teilauftr.')?></th>
			<th><?=$_LANG->get('Verkn. Tickets')?></th>
			<th><?=$_LANG->get('Optionen')?></th>
		</tr>
	</thead>
    <? $x = 1; 
    foreach($schedules as $s) { 
    	$adj_tickets = Ticket::getTicketsForObject(get_class($s), $s->getId());?>
    <tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
        <td class="content_row" valign="top" align="center" style="background-color:<?if($s->getFinished()) echo "green"; else echo "red"?>;color:white"><?=$s->getId()?></td>
        <td class="content_row" valign="top"><?=$s->getCreateuser()?>&nbsp;</td>
        <td class="content_row" valign="top"><a href="index.php?page=<?=$_REQUEST['page']?>&exec=parts&id=<?=$s->getId()?>"><?=$s->getNumber()?></a>&nbsp;</td>
        <td class="content_row" valign="top"><?=$s->getCustomer()->getNameAsLine()?>&nbsp;</td>
        <td class="content_row" valign="top"><?=$s->getObject()?>&nbsp;</td>
        <td class="content_row" valign="top"><?=$s->getAmount()?>&nbsp;</td>
        <td class="content_row" valign="top"><?=date('d.m.Y', $s->getDeliveryDate())?></td>
        <td class="content_row" valign="top"><?=$s->getDeliveryLocation()?>&nbsp;</td>
        <td class="content_row" valign="top"><?=$s->getDeliveryterms()->getName1()?>&nbsp;</td>
        <td class="content_row" valign="top"><?=$s->getNotes()?>&nbsp;</td>
        <td class="content_row" valign="top" align="center"><?=count(SchedulePart::getAllScheduleParts($s->getId()))?></td>
        <td class="content_row" valign="top">
		<?	if(count($adj_tickets) > 0){ 
				foreach ($adj_tickets AS $tkt){ ?>
					<a href="index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$tkt->getId()?>"><?=$tkt->getNumber()?></a> <br/> 
		<?		}
			} else { 
				echo "&ensp;" ;
			}?>
		</td>	
        <td class="content_row" valign="top">
        <nobr>
        	<?=$_LANG->get('Auftrag')?>:
        	<a class="link" href="index.php?page=<?=$_REQUEST['page']?>&exec=parts&id=<?=$s->getId()?>"><?=$_LANG->get('Bearbeiten')?></a>
        	<a class="link" href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$s->getId()?>')"><?=$_LANG->get('L&ouml;schen')?></a>
        </nobr>
        <br>
        
        <nobr><?=$_LANG->get('Teilauftrag')?>:<a class="link" href="index.php?page=<?=$_REQUEST['page']?>&exec=editparts&subexec=edit&id=9710"><?=$_LANG->get('Anlegen')?></a></nobr>
        <br>

        <nobr>
        	<?=$_LANG->get('Ticket')?>: 
        	<a class="link" href="index.php?page=libs/modules/tickets/tickets.php&&exec=new&subexec=newforplan&planid=<?=$s->getId()?>"
        		><?=$_LANG->get('Anlegen')?></a>
        </nobr>
        </td>
    </tr>
    <? $x++;} ?>
</table>
</div>

<? }}?>