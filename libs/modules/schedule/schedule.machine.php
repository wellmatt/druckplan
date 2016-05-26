<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       30.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/timekeeping/timekeeper.class.php';
require_once 'libs/modules/organizer/urlaub.class.php';

// error_reporting(-1);
// ini_set('display_errors', 1);

// Compare by Priority of schedmachs
function cmpByPrio($a, $b)
{
    if($a->getPriority() == $b->getPriority())
        return 0;
    return ($a->getPriority() < $b->getPriority()) ? -1 : 1;
}


$mach = new Machine((int)$_REQUEST["id"]);
$downtimes = ScheduleDowntime::getAllScheduleDowntimes();

$days = ScheduleMachine::getOpenScheduledDays((int)$_REQUEST["id"], 99);
if($_REQUEST["day"] != "")
    $_SESSION["schedule_machine_day"] = $_REQUEST["day"];

if($_SESSION["schedule_machine_day"] == "" || $_SESSION["schedule_machine_day"] == "all")
    $seldays = $days;
else
    $seldays = Array($_SESSION["schedule_machine_day"]);

// Urlaube
$vacs = Array();
foreach (User::getAllUser(User::ORDER_NAME, $_USER->getClient()->getId()) as $u)
{
    for ($i = 0; $i < 6; $i++)
    {
        $tme = time()+86400*$i;
        $id = Urlaub::isVacationOnDay($u, date('d', $tme), date('m', $tme), date('Y', $tme));
        if ($id)
        {
            $vac = new Urlaub($id);
            if($vac->getState() == Urlaub::STATE_APPROVED || $vac->getState() == Urlaub::STATE_WAIT)
            {
                $vacs[$u->getId()][$i]["state"] = $vac->getState();
                $vacs[$u->getId()][$i]["reason"] = $vac->getReason();
            }
            $id = 0;
        } 
    }
}

// Positionen tauschen
if($_REQUEST["down"] != "")
{
    $sm = new ScheduleMachine((int)$_REQUEST["down"]);
    $sm->setPriority($sm->getPriority() + 1);
    $sm->save();
}
if($_REQUEST["up"] != "")
{
    $sm = new ScheduleMachine((int)$_REQUEST["up"]);
    $sm->setPriority($sm->getPriority() - 1);
    $sm->save();
}

// Abgeschlossene Auftr�ge ausblenden
if($_REQUEST["show_finished_jobs"])
    $_SESSION["show_finished_jobs"] = 1;
else
    $_SESSION["show_finished_jobs"] = 0;
?>
<script language="javascript">
function saveStats(mode, job_id, newval, objprefix, color)
{
    $.post("libs/modules/schedule/schedule.ajax.php", {exec: 'setstatus', id: job_id, mode: mode, newval: newval}, 
    	    function(data) {
			// alert(data);
        	if(data == "1")
        	{
        	    for(var x=0; x<4; x++)
        	    {
        	        var obj = document.getElementById(objprefix +'_' +eval(x));
        	        if(obj != null)
        	        {
        	            if(x == newval)
        	                obj.src = './images/status/' +color +'.gif';
        	            else
        	                obj.src = './images/status/black.gif';
        	        }
        	    }
        	}            	
	});
	
}

function setMachineStart(job_machines_id, idx, mode)
{
    val1 = '';
    val2 = 'none';
    
    if(mode == 'start')
    {
      val1 = 'none';
      val2 = '';
    }
    
    machine_btnstart  = document.getElementById('idx_machine_btnstart_' +idx);
    machine_btnstop   = document.getElementById('idx_machine_btnstop_' +idx);
    actual_time       = document.getElementById('idx_actual_time_' +idx);
    machine_loading   = document.getElementById('idx_machine_loading_' +idx);
    machine_down      = document.getElementById('idx_machine_down_' +idx);
    down_time_type    = document.getElementById('idx_down_time_type_' +idx);
    down_time         = document.getElementById('idx_down_time_' +idx);
    databtn           = document.getElementById('idx_databtn_' +idx);
    std               = document.getElementById('idx_std_' +idx);
    
    machine_btnstart.style.display   = val1;
    actual_time.style.display        = val1;
    machine_down.style.display       = val1;
    down_time_type.style.display     = val1;
    down_time.style.display          = val1;
    databtn.style.display            = val1;
    std.style.display                = val1;
    
    machine_btnstop.style.display    = val2;
    machine_loading.style.display    = val2;
    
    $.post("libs/modules/schedule/schedule.ajax.php", {exec: 'setcounter', id: job_machines_id, mode: mode}, 
        function(data) {
            if(data != '')
            {
                var obj = document.getElementById('idx_actual_time_'+idx);
                obj.value = parseFloat(data) + parseFloat(obj.value.replace(',','.'));
                obj.value = obj.value.replace('.',',');
                obj.value = obj.value.substr(0, obj.value.indexOf(",")+3);
            }
    });

}

function setMachineTimes(job_machines_id, idx)
{
	actual_time       = document.getElementById('idx_actual_time_' +idx);
    down_time_type    = document.getElementById('idx_down_time_type_' +idx);
    down_time         = document.getElementById('idx_down_time_' +idx);

	$.post("libs/modules/schedule/schedule.ajax.php", 
			{exec: 'setMachineTimes', actualTime: actual_time.value, downTimeType: down_time_type.value, 
	              downTime: down_time.value, machineId: job_machines_id}, 
			function(data) {
		      		document.getElementById('idx_upd_actual_time_'+idx).innerHTML = actual_time.value;
		      		document.getElementById('idx_upd_down_time_'+idx).innerHTML = down_time.value;
	});
}

function updateMoveField(start, end)
{
	if(start < end -1 )
	{
		for(var x = start; x < end; x++)
		{
			next = x + 1;
			last = x - 1;
			if (x != start)
			{
			    document.getElementById('idx_move_up_'+x).src = './images/status/up.gif';
			    document.getElementById('idx_move_up_'+x).style.cursor = 'pointer'; 
			}

			if (x != end -1)
			{
			    document.getElementById('idx_move_down_'+x).src = './images/status/down.gif';
			    document.getElementById('idx_move_down_'+x).style.cursor = 'pointer';
			}
		}
	}
}

function moveElement(obj, mode)
{
    var arr_thisidx = obj.id.split("_");
    var thisidx = arr_thisidx[3];
	
	var id = document.getElementById('idx_move_thisid_'+thisidx).value;

	document.location='index.php?page=<?=$_REQUEST['page']?>&exec=showmachine&id=<?=$mach->getId()?>&'+mode+'='+id;
	
}

function setFinished(smid, idx)
{
	if(document.getElementById('btn_finished_'+smid).src.substr(-8) == 'tick.png')
		var newval = 0;
	else 
		var newval = 1;
	
	$.post("libs/modules/schedule/schedule.ajax.php", {exec: "setSchedMachFinished", smid: smid, val: newval}, 
	function(data) {
		// Work on returned data
		if(newval == 1)
		{
		    if(data == '1')
		    {
		    	document.getElementById('btn_finished_'+smid).src = 'images/icons/layer-tick.png';
		    	if(document.getElementById('show_finished_jobs').checked != true)
		    	{
			    	$(function() {
				    	var options = {};
				    	$('#idx_tr_main_'+idx).hide('highlight', options, 600);
		    			$('#idx_tr_sub_'+idx).hide('highlight', options, 600);
			    	});
		    	}
		    }
		} else
		{
		    if(data == '1')
		    	document.getElementById('btn_finished_'+smid).src = 'images/icons/layer.png';
		}
	});
	
}
</script>

<link rel="stylesheet" href="css/urlaub.css">

<table width="1300">
<tr><td>
    <a href="index.php?page=<?=$_REQUEST['page']?>&exec=showmachine&id=<?=$mach->getId()?>&day=all"><?=$_LANG->get('Alle Tage anzeigen')?></a>
    <table cellpadding="3" cellspacing="0" border="0">
        <tr>
            <? 
            $x = 1;
            foreach($days as $d)
            {
                echo '<td class="content_row" width="70"><a href="index.php?page='.$_REQUEST['page'].'&exec=showmachine&id='.$mach->getId().'&day='.$d.'">'.date('d.m.Y', $d).'</td>';
                if($x % 8 == 0)
                    echo '</tr><tr>';
                $x++;
            }
                
            ?>
        </tr>
    </table>
</td>
<td>&nbsp;</td>
<td align="right" width="400">
<? if (count($vacs)) { ?>
<table class="urlaubTable" cellspacing="0" cellpadding="0">
    <tr>
        <td class="content_row_header">
            <?=$_LANG->get('Urlaub in den n&auml;chsten 6 Tagen')?>
        </td>
    </tr>
    <tr>
        <td class="urlaubHeader" style="border:1px solid;padding:2px;"><?=$_LANG->get('Name')?></td>
        <?
            for($i = 0; $i < 6; $i++)
            {
                 echo '<td class="urlaubHeader" style="border:1px solid;border-left:none;padding:2px;">'.date('d.m.', time()+$i*86400).'</td>';
            } 
        ?>
        
    </tr>
    <?  $x = 1;
        foreach($vacs as $u => $v)
        {
            $user = new User($u);
            echo '<tr class="'.getRowColor($x).'"><td class="urlaubNames" width="180">'.$user->getNameAsLine().'</td>';
            for ($i = 0; $i < 6; $i++)
            {
                if($v[$i]["state"] == Urlaub::STATE_APPROVED)
                    echo '<td class="urlaubDays approved">';
                else if ($v[$i]["state"] == Urlaub::STATE_WAIT)
                    echo '<td class="urlaubDays wait">';
                else
                    echo '<td class="urlaubDays">&nbsp;';
                
                if($v[$i]["reason"] == Urlaub::TYPE_URLAUB)
                    echo "U";
                else if($v[$i]["reason"] == Urlaub::TYPE_KRANKHEIT)
                    echo "K";
                else if($v[$i]["reason"] == Urlaub::TYPE_UEBERSTUNDEN)
                    echo "M";
                else if($v[$i]["reason"] == Urlaub::TYPE_SONSTIGES)
                    echo "S";
                
                echo "</td>";
            }
            echo '</tr>';
            $x++;
        }
    ?>
</table>
<? } ?>
<? 
$all_locks = MachineLock::getAllMachineLocksForMachine($mach->getId());?>
<table class="locksTable" cellspacing="0" cellpadding="0">
    <tr>
        <td class="content_row_header" colspan="2" align="right">
            <font color="red"><u><b><?=$_LANG->get('Sperrzeiten')?></b></u></font>
        </td>
    </tr>
    <?php 
	foreach ($all_locks as $lock){
	    if ($lock->getStart() >= time() || $lock->getStop() >= time()){
	?>
	<tr>
		<td class="content_row_clear" valign="top"><?php echo date("d.m.Y H:i", $lock->getStart());?> -</td>
		<td class="content_row_clear" valign="top"><?php echo date("d.m.Y H:i", $lock->getStop());?></td>
	</tr>
	<?php }} ?>
</table>
</td></tr>
</table>
<br><br>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="showForm">
<input name="exec" value="showmachine" type="hidden">
<input name="id" value="<?=$mach->getId()?>" type="hidden">
<table width="100%">
    <tr>
        <td width="300" class="machine_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
            <?=$_LANG->get('Maschine')?>: <?=$mach->getName()?> (<a href="./docs/<?=$_USER->getId()?>-Maschinen-Statistik.csv">Export</a>)
        </td>
        <td width="200">
            <input type="checkbox" name="show_finished_jobs" id="show_finished_jobs" value="1" 
                <?if($_SESSION["show_finished_jobs"] == 1) echo "checked";?>>
            <?=$_LANG->get('Abgeschlossene Auftr&auml;ge anzeigen')?>
        </td>      
        <td align="left" width="120">
            <ul class="postnav_save_small">
            <a href="#" onclick="document.showForm.submit()" style="padding-left:16px">Aktualisieren</a>
            </ul>            
        </td>
        <td align="right"><?=$savemsg?></td>
    </tr>
</table>
</form>

<? // CSV-Datei der Rechnungen vorbereiten
$csv_file = fopen('./docs/'.$_USER->getId().'-Maschinen-Statistik.csv', "w");
//fwrite($csv_file, "Firma iPactor - �bersicht\n");

// Tabellenkopf der CSV-Datei (Rechnungen) schreiben
$csv_string .= "Datum; Auftr.-Nr.; Kunde; Objekt; Auflage; Farben; Lack; S-Zeit; I-Zeit; A-Zeit; LT; L-Ort; Versandart; Bemerkung;\n";

$x = 0; 
foreach($seldays as $selday) {
    $dayStartIdx = $x;?>

<div class="box1">
	<table border="0" class="content_table" cellpadding="3" cellspacing="0"
		width="100%" style="table-layout: fixed">
		<colgroup>
			<col width="100">
			<col>
			<col width="150">
			<col width="55">
			<col>
			<col width="50">
			<col width="45">
			<col width="45">
			<col width="45">			<?// Ausfallzeit war mal 45Px?>
			<col width="70">
			<col>
			<col>
			<col>
		</colgroup>
		<tr>
			<td align="left" colspan="13" class="delivery_header">
			<?=date('d.m.Y', $selday)?> | <?=$_LANG->get('Insgesamt')?>:
				<?=printPrice(ScheduleMachine::getMachineTimeForDay($selday, $mach->getId()))?>
				<?=$_LANG->get('Std.')?>
			</td>
		</tr>

		<tr>
			<td class="content_tbl_subheader"><?=$_LANG->get('Auftr.-Nr.')?></td>
			<td class="content_tbl_subheader"><nobr><?=$_LANG->get('Kunde')?></nobr></td>
			<td class="content_tbl_subheader"><nobr><?=$_LANG->get('Objekt')?></nobr></td>
			<td class="content_tbl_subheader"><nobr><?=$_LANG->get('Auflage')?></nobr></td>
			<td class="content_tbl_subheader"><nobr><?=$_LANG->get('Farben')?></nobr></td>
			<td class="content_tbl_subheader"><nobr><?=$_LANG->get('Lack')?></nobr></td>
			<td class="content_tbl_subheader"><nobr><?=$_LANG->get('S-Zeit')?></nobr></td>
			<td class="content_tbl_subheader"><nobr><?=$_LANG->get('I-Zeit')?></nobr></td>
			<td class="content_tbl_subheader"><nobr><?=$_LANG->get('A-Zeit')?></nobr></td>
			<td class="content_tbl_subheader"><nobr><?=$_LANG->get('LT')?></nobr></td>
			<td class="content_tbl_subheader"><nobr><?=$_LANG->get('Lieferort')?></nobr></td>
			<td class="content_tbl_subheader"><nobr><?=$_LANG->get('Versandart')?></nobr></td>
			<td class="content_tbl_subheader"><nobr><?=$_LANG->get('Bemerkungen')?></nobr></td>
		</tr>
		<?  
		    // Order SchedMachs by priority

		    $smentries = Array();
		    foreach(ScheduleMachine::getPartsForDay($selday, $mach->getId()) as $part)
		    {
		        foreach(ScheduleMachine::getSmEntriesForMachineAndPart($part->getId(), $mach->getId()) as $sm)
		        {
		            if (($_SESSION["show_finished_jobs"] == 1 || $sm->getFinished() == 0) 
		                    && ($part->getFinished() || $_USER->hasRightsByGroup(Group::RIGHT_PARTS_EDIT)))
		                $smentries[] = $sm;
		        }
		    }
		    usort($smentries, "cmpByPrio");
		    
		    unset($sm); unset($part);
		    foreach($smentries as $sm) 
		    {
		        $part = new SchedulePart($sm->getSchedulePartId());
		        $sched = new Schedule($part->getScheduleId());
		        
		        $tmp_deliv_loc = str_replace("\n", " / ", $sched->getDeliveryLocation());
		        $csv_string .= date('d.m.Y', $selday)."; {$sched->getNumber()}; {$sched->getCustomer()->getName1()}; {$sched->getObject()}; {$sm->getAmount()};"; 
		        $csv_string .= "{$sm->getColors()}; {$sm->getFinishing()}; {$sm->getTargetTime()}; {$sm->getActualTime()}; {$sm->getDownTime()};"; 
		        $csv_string .= date('d.m.Y', $sched->getDeliveryDate())."; {$tmp_deliv_loc}; {$sched->getDeliveryterms()->getName1()}; {$sm->getNotes()};\n";
        ?>
		<tr id="idx_tr_main_<?=$x?>" class="<?=getRowColor(0)?>"
			onmouseover="mark(this, 0); mark(document.getElementById('idx_tr_sub_<?=$x?>'), 0)"
			onmouseout="mark(this,1); mark(document.getElementById('idx_tr_sub_<?=$x?>'), 1)">
			<td class="content_row" valign="top"><a href="index.php?page=<?=$_REQUEST['page']?>&exec=parts&id=<?=$sched->getId()?>"><?=$sched->getNumber()?></a></td>
			<td class="content_row" valign="top"><?=$sched->getCustomer()->getName1()?></td>
			<td class="content_row" valign="top"><?=$sched->getObject()?>&nbsp;</td>
			<td class="content_row" valign="top"><?=$sm->getAmount()?>&nbsp;</td>
			<td class="content_row" valign="top"><?=$sm->getColors()?>&nbsp;</td>
			<td class="content_row" valign="top"><?=$sm->getFinishing()?>&nbsp;</td>
			<td class="content_row" valign="top"><nobr><? if($_USER->hasRightsByGroup(Group::RIGHT_SEE_TARGETTIME)) echo printPrice($sm->getTargetTime()); else echo "&nbsp;";?></nobr></td>
			<td class="content_row" valign="top" id="idx_upd_actual_time_<?=$x?>"><nobr><?=printPrice($sm->getActualTime())?></nobr>
			</td>
			<td class="content_row" valign="top" id="idx_upd_down_time_<?=$x?>"><nobr><?=printPrice($sm->getDownTime())?></nobr>
			</td>
			<td class="content_row" valign="top"><nobr><?=date('d.m.Y', $sched->getDeliveryDate())?></nobr>
			</td>
			<td class="content_row" valign="top"><?=$sched->getDeliveryLocation()?>&nbsp;</td>
			<td class="content_row" valign="top"><?=$sched->getDeliveryterms()->getName1()?>&nbsp;</td>
			<td class="content_row" valign="top">
				<div align="left">
					<!-- <a href="javascript:showMachineNotes('<?=$x?>')"><img
						src="./images/icons/report--plus.png" border="0"
						alt="Klicken, um Bemerkung zu bearbeiten."> </a> --> 
				</div>
				<div id="idx_upd_notes_currtxt_<?=$x?>"><?=$sm->getNotes()?></div>
				<div id="idx_upd_notes_<?=$x?>" style="display: none">
					<textarea id="idx_upd_notes_newtext_<?=$x?>" class="text"
						style="height: 80px; width: 100%"></textarea>
					<table border="0" cellpadding="0" cellspacing="0" width="100%">
						<tr>
							<td style="padding-top: 3px">
								<ul class="postnav_save">
									<a href="javascript: saveMachineNotes('<?=$sm->getId()?>', '<?=$x?>')">Speichern</a>
								</ul>
							</td>
						</tr>
						<tr>
							<td style="padding-top: 3px">
								<ul class="postnav_del">
									<a href="javascript: showMachineNotes('<?=$x?>')">Abbruch</a>
								</ul>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>

		<tr id="idx_tr_sub_<?=$x?>" bgcolor="#F0F3F5"
			onmouseover="mark(this, 0); mark(document.getElementById('idx_tr_main_<?=$x?>'), 0)"
			onmouseout="mark(this,1); mark(document.getElementById('idx_tr_main_<?=$x?>'), 1)">
			<td align="left" class="content_row" colspan="13">
				<table border="0" cellpadding="3" cellspacing="0" width="100%">
					<tr>
						<!--td class="content_row_clear" width="340" rowspan="6">Ist-Zeit:</td-->
						<? /*
						<td class="content_row_clear" width="440" rowspan="6">
							<div style="overflow: auto; padding-left:16px;">
							<?	$timer_moduleID = Timekeeper::MODULE_PLANNING;
								$timer_objectID = $sched->getId();
								$timer_subObjectID = $sm->getId();
								// $div_height = "260px";
								require 'libs/modules/timekeeping/timekeeper.buttonsForPlaning.php'; ?>
							</div>
						</td>
						*/?>
						<td class="content_row_clear" width="35">
							<input type="text" class="text" id="idx_actual_time_<?=$x?>" 
									value="<?=printPrice($sm->getActualTime())?>" style="width: 35px;"> 
							<img src="./images/status/loading2.gif" id="idx_machine_loading_<?=$x?>" style="display: none;">
						</td>
						<td class="content_row_clear" width="40">
							<ul class="postnav_save_small" id="idx_machine_btnstart_<?=$x?>" style="padding:0px">
								<a href="javascript: setMachineStart('<?=$sm->getId()?>','<?=$x?>', 'start')">Start</a>
							</ul>
							<ul class="postnav_del_small" id="idx_machine_btnstop_<?=$x?>"
								style="display: none;">
								<a href="javascript: setMachineStart('<?=$sm->getId()?>','<?=$x?>', 'stop')">Stop</a>
							</ul>
						</td>
						<td class="content_row_clear" width="60">
							<div id="idx_machine_down_<?=$x?>" style="">Ausfallzeit:</div>
						</td>
						<td class="content_row_clear" width="180"><select class="text"
							id="idx_down_time_type_<?=$x?>" style="width: 180px;">
								<option value="">&lt; <?=$_LANG->get('Bitte ausw&auml;hlen')?> &gt;</option>
                                <? 
                                foreach($downtimes as $dt)
                                {
                                    echo '<option value="'.$dt->getId().'" ';
                                    if($dt->getId() == $sm->getDownTimeType()) echo "selected";
                                    echo '>'.$dt->getName().'</option>';
                                }
                                ?>
						</select>
						</td>
						<td class="content_row_clear" width="35"><input type="text"
							class="text" id="idx_down_time_<?=$x?>" value="<?=printPrice($sm->getDownTime())?>"
							style="width: 35px;">
						</td>
						<td class="content_row_clear" width="20">
							<div id="idx_std_<?=$x?>" style="">Std.</div>
						</td>
						<td class="content_row_clear" width="70">
							<ul class="postnav_save_small" id="idx_databtn_<?=$x?>" style="">
								<a href="javascript: setMachineTimes('<?=$sm->getId()?>','<?=$x?>')" style="padding-left:20px;">Speichern</a>
							</ul>
						</td>
						
						<td class="content_row_clear">
                        	<table border="0" cellpadding="0" cellspacing="0">
                        		<tr>
                        			<td width="80" class="content_row_clear"><?=$_LANG->get('DTP-Status:')?></td>
                        			<td width="25" class="content_row_clear"><a
                        				href="javascript: saveStats('dtp','<?=$sched->getId()?>','0','idx_img_dtp_<?=$x?>','red')">
                        					<img id="idx_img_dtp_<?=$x?>_0" class="select"
                        					src="./images/status/<? 
                                 if((int)$sched->getStatusDtp() == 0) 
                                    echo 'red';
                                 else 
                                     echo 'black';
                                 ?>.gif"
                        					alt="<?=$_LANG->get('DTP-Status:')?> <?=$_LANG->get('Rot')?>">
                        			</a>
                        			</td>
                        			<td width="25" class="content_row_clear"><a
                        				href="javascript: saveStats('dtp','<?=$sched->getId()?>','1','idx_img_dtp_<?=$x?>','yellow')">
                        					<img id="idx_img_dtp_<?=$x?>_1" class="select"
                        					src="./images/status/<? 
                                 if((int)$sched->getStatusDtp() == 1) 
                                    echo 'yellow';
                                 else 
                                     echo 'black';
                                 ?>.gif"
                        					alt="<?=$_LANG->get('DTP-Status:')?> <?=$_LANG->get('Gelb')?>">
                        			</a>
                        			</td>
                        			<td width="25" class="content_row_clear"><a
                        				href="javascript: saveStats('dtp','<?=$sched->getId()?>','2','idx_img_dtp_<?=$x?>','orange')">
                        					<img id="idx_img_dtp_<?=$x?>_2" class="select"
                        					src="./images/status/<? 
                                 if((int)$sched->getStatusDtp() == 2) 
                                    echo 'orange';
                                 else 
                                     echo 'black';
                                 ?>.gif"
                        					alt="<?=$_LANG->get('DTP-Status:')?> <?=$_LANG->get('Orange')?>">
                        			</a>
                        			</td>
                        			<td width="25" class="content_row_clear"><a
                        				href="javascript: saveStats('dtp','<?=$sched->getId()?>','3','idx_img_dtp_<?=$x?>','green')">
                        					<img id="idx_img_dtp_<?=$x?>_3" class="select"
                        					src="./images/status/<? 
                                 if((int)$sched->getStatusDtp() == 3) 
                                    echo 'green';
                                 else 
                                     echo 'black';
                                 ?>.gif"
                        					alt="<?=$_LANG->get('DTP-Status:')?> <?=$_LANG->get('Gr&uuml;n')?>">
                        			</a>
                        			</td>
                        			<td width="10" class="content_row_clear"></td>
                        			<td width="100" class="content_row_clear">Papier-Status:</td>
                        			<td width="25" class="content_row_clear"><a
                        				href="javascript: saveStats('paper','<?=$sched->getId()?>','0','idx_img_paper_<?=$x?>','red')">
                        					<img id="idx_img_paper_<?=$x?>_0" class="select"
                        					src="./images/status/<?
                                 if((int)$sched->getStatusPaper() == 0) 
                                    echo "red";
                                 else
                                     echo "black";
                                 ?>.gif"
                        					alt="<?=$_LANG->get('Papier-Status:')?> <?=$_LANG->get('Rot')?>">
                        			</a>
                        			</td>
                        			<td width="25" class="content_row_clear"><a
                        				href="javascript: saveStats('paper','<?=$sched->getId()?>','1','idx_img_paper_<?=$x?>','yellow')">
                        					<img id="idx_img_paper_<?=$x?>_1" class="select"
                        					src="./images/status/<?
                                 if((int)$sched->getStatusPaper() == 1) 
                                    echo "yellow";
                                 else
                                     echo "black";
                                 ?>.gif"
                        					alt="<?=$_LANG->get('Papier-Status:')?> <?=$_LANG->get('Gelb')?>">
                        			</a>
                        			</td>
                        			<td width="25" class="content_row_clear"><a
                        				href="javascript: saveStats('paper','<?=$sched->getId()?>','2','idx_img_paper_<?=$x?>','green')">
                        					<img id="idx_img_paper_<?=$x?>_2" class="select"
                        					src="./images/status/<?
                                 if((int)$sched->getStatusPaper() == 2) 
                                    echo "green";
                                 else
                                     echo "black";
                                 ?>.gif"
                        					alt="<?=$_LANG->get('Papier-Status:')?> <?=$_LANG->get('Gr&uuml;n')?>">
                        			</a>
                        			</td>
                    			</tr>
                			</table>
						</td>
						
						<td class="content_row_clear" align="right">
						    <?=$_LANG->get('Pos.')?> <?=$sm->getPriority()?> &nbsp;&nbsp;
						    <input type="hidden" name="move_lastid_<?=$x?>" id="idx_move_lastid_<?=$x?>" value="">
						    <input type="hidden" name="move_thisid_<?=$x?>" id="idx_move_thisid_<?=$x?>" value="<?=$sm->getId()?>">
						    <input type="hidden" name="move_nextid_<?=$x?>" id="idx_move_nextid_<?=$x?>" value="">
						    <nobr>
								<img src="./images/status/up_inactive.gif" id="idx_move_up_<?=$x?>" onclick="moveElement(this, 'up')"> 
								<img src="./images/status/down_inactive.gif" id="idx_move_down_<?=$x?>" onclick="moveElement(this, 'down')">
							</nobr>
						</td>
						<td class="content_row_clear" width="10">&nbsp;</td>
						<td class="content_row_clear" align="right">
							<span class="glyphicons glyphicons-unchecked pointer"<?if($sm->getFinished()) echo "-tick";?>
							    onclick="setFinished(<?=$sm->getId()?>, <?=$x?>)" id="btn_finished_<?=$sm->getId()?>"></span>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<? $x++;}  ?>
	</table>
	<script language="javascript">updateMoveField(<?=$dayStartIdx?>, <?=$x?>)</script>
</div>
<br>
<? } 

$csv_string = iconv('UTF-8', 'ISO-8859-1', $csv_string);
fwrite($csv_file, $csv_string);
fclose($csv_file); ?>

?>