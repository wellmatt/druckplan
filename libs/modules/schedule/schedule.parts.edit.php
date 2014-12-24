<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       03.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$sched = new Schedule((int)$_REQUEST["id"]);
$part = new SchedulePart((int)$_REQUEST["part_id"]);


if($_REQUEST["subexec"] == "save")
{
    // Teilauftrag anlegen
    if($part->getId() < 1)
    {
        $part->setScheduleId($sched->getId());
        $part->save();
        echo $DB->getLastError();
    }
    
    foreach(array_keys($_REQUEST) as $key)
    {
        if(strpos($key, "machine_id") === 0)
        {
            $cat_subid  = substr($key, strrpos($key,"_") +1);
            $tmpreqkey  = substr($key, 0, strrpos($key,"_"));
            $cat_id     = substr($tmpreqkey, strrpos($tmpreqkey,"_") +1);
            
            $deadline = explode(".", trim(addslashes($_REQUEST["deadline_{$cat_id}_{$cat_subid}"])));
            $deadline = mktime(0,0,0,$deadline[1], $deadline[0], $deadline[2]);
            
            if($_REQUEST[$key] != "")
            {
                $sm = new ScheduleMachine((int)$_REQUEST["sm_id_{$cat_id}_{$cat_subid}"]);
                $sm->setSchedulePartId($part->getId());
                $sm->setMachine(new Machine((int)$_REQUEST["machine_id_{$cat_id}_{$cat_subid}"]));
                $sm->setTargetTime((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["target_time_{$cat_id}_{$cat_subid}"]))));
                $sm->setDeadline($deadline);
                $sm->setNotes(trim(addslashes($_REQUEST["notes_{$cat_id}_{$cat_subid}"])));
                $sm->save();
                //echo $DB->getLastError();

            } else if((int)$_REQUEST["sm_id_{$cat_id}_{$cat_subid}"] > 0)
            {
                $sm = new ScheduleMachine((int)$_REQUEST["sm_id_{$cat_id}_{$cat_subid}"]);
                $sm->delete();
            }
        }
    }
}

?>

<script language="javascript">
function get_machine_time(source_object, sm_id, cat_id, rowid)
{
    var machine_id    = document.getElementById('idx_machine_' +cat_id +'_' +rowid).value;
    var deadline      = document.getElementById('idx_deadline_' +cat_id +'_' +rowid).value;
    var target_time   = document.getElementById('idx_target_time_' +cat_id +'_' +rowid).value;

    if(machine_id != '' && deadline != '' && source_object.value != '')
    {
        $.post("libs/modules/schedule/schedule.ajax.php", 
        	    {exec: "getMachineTime", addvalue: target_time, sm_id: sm_id, machine_id: machine_id,
                     deadline: deadline}, 
        	    function(data) {
        	    	document.getElementById('idx_res_' +cat_id +'_' +rowid).innerHTML = data;
    	});

    }
    else
    {
        document.getElementById('idx_res_' +cat_id +'_' +rowid).innerHTML = '&nbsp;';
    }
}


</script>

<div class="box1">
<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
        <td class="content_row_subheader"><?=$_LANG->get('ID')?></td>
        <td class="content_row_subheader"><?=$_LANG->get('Ersteller')?></td>
        <td class="content_row_subheader"><?=$_LANG->get('Auftr.-Nr.')?></td>
        <td class="content_row_subheader"><?=$_LANG->get('Kunde')?></td>
        <td class="content_row_subheader"><?=$_LANG->get('Objekt')?></td>
        <td class="content_row_subheader"><?=$_LANG->get('ges. Auflage')?></td>
        <td class="content_row_subheader"><?=$_LANG->get('LT')?></td>
        <td class="content_row_subheader"><?=$_LANG->get('Lieferort')?></td>
        <td class="content_row_subheader"><?=$_LANG->get('Versandart')?></td>
        <td class="content_row_subheader"><?=$_LANG->get('Bemerkungen')?></td>
        <td class="content_row_subheader"><?=$_LANG->get('Teilauftr.')?></td>
    </tr>
    
    <tr class="<?=getRowColor(0)?>">
        <td class="content_row" valign="top" align="center"><?=$sched->getId()?></td>
        <td class="content_row" valign="top"><?=$sched->getCreateuser()?>&nbsp;</td>
        <td class="content_row" valign="top"><?=$sched->getNumber()?>&nbsp;</td>
        <td class="content_row" valign="top"><?=$sched->getCustomer()->getNameAsLine()?>&nbsp;</td>
        <td class="content_row" valign="top"><?=$sched->getObject()?>&nbsp;</td>
        <td class="content_row" valign="top"><?=$sched->getAmount()?>&nbsp;</td>
        <td class="content_row" valign="top"><?=date('d.m.Y', $sched->getDeliveryDate())?>&nbsp;</td>
        <td class="content_row" valign="top"><?=$sched->getDeliveryLocation()?>&nbsp;</td>
        <td class="content_row" valign="top"><?=$sched->getDeliveryterms()->getName1()?>&nbsp;</td>
        <td class="content_row" valign="top"><?=$sched->getNotes()?>&nbsp;</td>
        <td class="content_row" valign="top" align="center">&nbsp;</td>
    </tr>
</table>
</div>
<br>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" onsubmit="return checkpartsform(this)" name="parts_form">
<input type="hidden" name="exec" value="editparts">
<input type="hidden" name="subexec" value="save">
<input type="hidden" name="id" value="<?=$sched->getId()?>">
<input type="hidden" name="part_id" value="<?=$part->getId()?>">

<div class="box2">
<table border="0" cellspacing="0" cellpadding="3" width="100%">
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Teilauftrag bearbeiten')?></td>
    </tr>
    <tr>
        <td class="content_row_subheader"><?=$_LANG->get('Kategorie')?></td>
        <td class="content_row_subheader"><?=$_LANG->get('Maschine')?></td>
        <td class="content_row_subheader"><?=$_LANG->get('Dauer')?> <?=$_LANG->get('soll')?></td>
        <td class="content_row_subheader"><?=$_LANG->get('Termin')?></td>
        <td class="content_row_subheader"><?=$_LANG->get('Bemerkungen')?></td>
        <td class="content_row_subheader" align="right" colspan="2"><?=$_LANG->get('Maschinenzeit')?><br><?=$_LANG->get('insgesamt')?></td>
    </tr>
    <? 
        $x = 0;
        foreach(MachineGroup::getAllMachineGroups() as $mg)
        {
            if($part->getId())
                $selmachs = ScheduleMachine::getAllScheduleMachines($part->getId(), ScheduleMachine::FILTER_MACHINEGROUP, $mg->getId());
            else 
                $selmachs = Array();
            $rows = count($selmachs);
            if ($rows == 0)
                $rows = 1;
            
            for ($i = 0; $i < $rows; $i++)
            {
                if (count($selmachs) > 0)
                    $sm = $selmachs[$i];
                else
                    $sm = new ScheduleMachine(0);
                
                $date = date('Y, m - 1, d', $oldDeadline);
                
                echo '
                <script lang="javascript">
                var groupsAfter = [ ';
                $str = '';
                foreach(MachineGroup::getAllMachineGroups() as $mg2)
                {
                    if($mg2->getPosition() > $mg->getPosition())
                        $str .= '"'.$mg2->getId().'", ';
                }
                echo substr($str, 0, -2);
                echo '];
                $(function() {
                    $.datepicker.setDefaults($.datepicker.regional[\''.$_LANG->getCode().'\']);
                    $(\'#idx_deadline_'.$mg->getId().'_'.$i.'\').datepicker(
                    {
                        showOtherMonths: true,
                        selectOtherMonths: true,
                        dateFormat: \'dd.mm.yy\',
                        showOn: "button",
                        buttonImage: "images/icons/calendar-blue.png",
                        buttonImageOnly: true,
                        minDate: new Date('.$date.'),
                        onSelect: function(selectedDate) {
                            get_machine_time(this, \''.$sm->getId().'\',\''.$mg->getId().'\', \''.$i.'\')
                            checkDate(selectedDate);
                            
                            // change first Date
        					instance = $( this ).data( "datepicker" ),
        					date = $.datepicker.parseDate(
        						instance.settings.dateFormat ||
        						$.datepicker._defaults.dateFormat,
        						selectedDate, instance.settings );

        			        ';
                            foreach(MachineGroup::getAllMachineGroups() as $mg2)
                            {
                                if($mg2->getPosition() > $mg->getPosition())
                                {
                                    $c_temp = count(ScheduleMachine::getAllScheduleMachines($part->getId(), ScheduleMachine::FILTER_MACHINEGROUP, $mg2->getId()));
                                    if($c_temp == 0)
                                        $c_temp = 1;
                                    for($j = 0; $j < $c_temp; $j++)
                                        echo '$(\'#idx_deadline_'.$mg2->getId().'_'.$j.'\').datepicker("option", "minDate", date);'."\n";
                                }
                            }
                            echo '
    			            
                        }
                    }
                    );

                });
               
                
                </script>
                ';
                echo '<tr class="'.getRowColor($x).'">';
                    echo '<td class="content_row">'.$mg->getName().'</td>';
                    
                    echo '<td class="content_row">';
                        echo '<input type="hidden" name="sm_id_'.$mg->getId().'_'.$i.'" value="'.$sm->getId().'">';
                        echo '<select name="machine_id_'.$mg->getId().'_'.$i.'" id="idx_machine_'.$mg->getId().'_'.$i.'" class="text" style="width:150px"
                                onchange="get_machine_time(this, \''.$sm->getId().'\',\''.$mg->getId().'\', \''.$i.'\')">';
                        echo '<option value="">&lt; '.$_LANG->get('Bitte w&auml;hlen').' &gt;</option>';
                    foreach(Machine::getAllMachines(Machine::ORDER_NAME, $mg->getId()) as $m)
                    {
                        echo '<option value="'.$m->getId().'" ';
                        if($sm->getMachine()->getId() == $m->getId()) echo "selected";
                        echo '>'.$m->getName().'</option>';
                    }
                    echo '</select>';
                    echo '</td>';
                    
                    echo '<td class="content_row">';
                        echo '<nobr><input name="target_time_'.$mg->getId().'_'.$i.'" id="idx_target_time_'.$mg->getId().'_'.$i.'" type="text" class="text" style="width:50px"
                                value="'.printPrice($sm->getTargetTime()).'" onchange="get_machine_time(this, \''.$sm->getId().'\',\''.$mg->getId().'\', \''.$i.'\')"> Std.
                                </nobr>';
                    echo '</td>';
                    
                    echo '<td class="content_row">';
                        echo '<input name="deadline_'.$mg->getId().'_'.$i.'" id="idx_deadline_'.$mg->getId().'_'.$i.'" type="text"
                                class="text" style="width:70px;" value="'.date('d.m.Y', $sm->getDeadline()).'"
                                onchange="get_machine_time(this, \''.$sm->getId().'\',\''.$mg->getId().'\', \''.$i.'\')">';
                    echo '</td>';
                    echo '<td class="content_row">';
                        echo '<input name="notes_'.$mg->getId().'_'.$i.'" type="text" class="text" style="width:280px"
                                value="'.$sm->getNotes().'">';
                    echo '</td>';
                    echo '<td class="content_row" align="right"  id="idx_res_'.$mg->getId().'_'.$i.'">';
                        if($sm->getId())
                        {
                            $hoursThisDay = ScheduleMachine::getMachineTimeForDay($sm->getDeadline(), $sm->getMachine()->getId());
                            echo '<span ';
                            if($hoursThisDay > $sm->getMachine()->getMaxHours() && $sm->getMachine()->getMaxHours() > 0) echo 'class="error"';
                            echo '>'.printPrice(ScheduleMachine::getMachineTimeForDay($sm->getDeadline(), $sm->getMachine()->getId())).' '.$_LANG->get('Std.').'</span>';
                        }
                        echo '&nbsp;&nbsp;&nbsp;';
                        // Nicht mehr "document.all" verwenden
                        //echo '<a href="javascript:get_machine_time(document.all.idx_machine_'.$mg->getId().'_'.$i.', \''.$sm->getId().'\',\''.$mg->getId().'\', \''.$i.'\')">';
                        //echo '<img border="0" src="./images/icons/arrow-circle-225.png"></a>';
                        

                    echo '</td>';
                echo '</tr>';
                $x++;
                if($sm->getDeadline() > 0)
                    $oldDeadline = $sm->getDeadline();
            }
            
        }
    ?>

<!-- 
<tr>
      <td class="content_row" colspan="7">
         <table border="0" cellpadding="2" cellspacing="0">
         <colgroup>
            <col width="342">
         </colgroup>
         <tr>
            <td class="content_row_clear">Datum</td>
                           <td class="content_row_clear">03.04.2012</td>
                              <td class="content_row_clear">04.04.2012</td>
                              <td class="content_row_clear">05.04.2012</td>
                              <td class="content_row_clear">06.04.2012</td>
                              <td class="content_row_clear">07.04.2012</td>
                              <td class="content_row_clear">08.04.2012</td>
                              <td class="content_row_clear">09.04.2012</td>
                              <td class="content_row_clear">10.04.2012</td>
                        </tr>
         <tr>
            <td class="content_row"><b>Gesamt vergebene Falzstunden</b></td>
                           <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="3,50">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="2,78">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="6,00">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="4,32">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="4,02">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="5,40">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="5,60">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="2,00">&nbsp;&nbsp;
               </td>
                        </tr>
         <tr>
            <td class="content_row"><b>Gesamt vergebene Buchdruckstunden</b></td>
                           <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="3,52">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="2,24">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="8,65">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="10,25">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="6,56">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="8,52">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="9,25">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="8,62">&nbsp;&nbsp;
               </td>
                        </tr>
         <tr>
            <td class="content_row"><b>Gesamt vergebene Brosch&uuml;renfertigung</b></td>
                           <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="3,65">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="6,32">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="7,25">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="7,20">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="8,53">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="6,50">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="7,00">&nbsp;&nbsp;
               </td>
                              <td class="content_row">
                  <input type="text" class="text" style="width:55px;background-color:#F0EDDD;font-weight:bold" tabindex="-1" readonly
                  value="8,50">&nbsp;&nbsp;
               </td>
                        </tr>
         </table>
      </td>
  </tr>
  -->
</table>
</div>   
<br>
<div class="box1">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
   <td align="right" width="130">
      <ul class="postnav">
         <a href="index.php?page=<?=$_REQUEST['page']?>&exec=parts&id=<?=$part->getScheduleId()?>"><?=$_LANG->get('Zur&uuml;ck')?></a>
      </ul>
   </td>
   <td>&nbsp;</td>
   <td align="right" width="130">
      <ul class="postnav_save">
         <a href="#"
         onclick="document.parts_form.submit()"><?=$_LANG->get('Speichern')?></a>
      </ul>
   </td>
</tr>
</table>
</div>
</form>