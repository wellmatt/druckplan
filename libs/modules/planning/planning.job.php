<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			08.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';
require_once 'libs/modules/collectiveinvoice/orderposition.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/article/article.class.php';

$jobs = Array();
if ($_REQUEST["type"] == "V")
{
    $colinv = new CollectiveInvoice((int)$_REQUEST["id"]);
    $header_parent_link = "index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid=".$colinv->getId();
    $header_title = $colinv->getTitle();
    $header_number = $colinv->getNumber();
    $header_crtdate = $colinv->getCrtdate();
    $header_crtusr = $colinv->getCrtuser();
    $header_comment = $colinv->getComment();
    $header_businessc = $colinv->getBusinesscontact();
    $header_businesscp = $colinv->getCustContactperson();
    $header_intcontact = $colinv->getInternContact();
    $header_duedate = $colinv->getDeliverydate();
    
    $orderpositions = Orderposition::getAllOrderposition($colinv->getId());
    foreach ($orderpositions as $opos)
    {
        $opos_article = new Article($opos->getObjectid());
        if ($opos_article->getIsWorkHourArt())
        {
            $jobs[] = Array("title" => $opos_article->getTitle(), "amount" => $opos->getQuantity(), "type" => "OP", "objectid" => $opos->getId());
        }
    }
} else
{
    $order = new Order((int)$_REQUEST["id"]);
    $header_parent_link = "index.php?page=libs/modules/calculation/order.php&exec=edit&step=4&id=".$order->getId();
    $header_title = $order->getTitle();
    $header_number = $order->getNumber();
    $header_crtdate = $order->getCrtdat();
    $header_crtusr = $order->getCrtusr();
    $header_comment = $order->getNotes();
    $header_businessc = $order->getCustomer();
    $header_businesscp = $order->getCustContactperson();
    $header_intcontact = $order->getInternContact();
    $header_duedate = $order->getDeliveryDate();
    
    $calcs = Calculation::getAllCalculations($order);
    
    foreach ($calcs as $calc)
    {
        if ($calc->getState())
        {
            $mes = Machineentry::getAllMachineentries($calc->getId());
            foreach ($mes as $me)
            {
                $jobs[] = Array("title" => $me->getMachine()->getName(), "amount" => $me->getTime()/60, "type" => "ME", "objectid" => $me->getId());
            }
        }
    }
}

?>

<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>
<link href='jscripts/calendar/fullcalendar.css' rel='stylesheet' />
<link href='jscripts/calendar/fullcalendar.print.css' rel='stylesheet' media='print' />
<script src='jscripts/calendar/moment.min.js'></script>
<script src='jscripts/calendar/fullcalendar.min.js'></script>
<script src='jscripts/calendar/twix.min.js'></script>
<script src='jscripts/calendar/de.js'></script>
<script src='jscripts/qtip/jquery.qtip.min.js'></script>
<link href='jscripts/qtip/jquery.qtip.min.css' rel='stylesheet'/>


<table width="100%">
	<tr>
		<td width="200" class="content_header">
			<img src="images/icons/application-form.png"> <? echo $_LANG->get('Jobs')?>
		</td>
		<td align="right"><?=$savemsg?></td>
	</tr>
</table>
</br>
<div class="box1">
    <b>Auftrags-Kopfdaten</b>
	<table width="100%">
		<tr>
			<td class="content_row content_row_header" valign="top"><?=$_LANG->get('Titel')?></td>
			<td class="content_row" valign="top"><a target="_blank" href="<?php echo $header_parent_link;?>"><?php echo $header_title;?></a></td>
			<td class="content_row content_row_header" valign="top"><?=$_LANG->get('Nummer')?></td>
			<td class="content_row" valign="top"><?php echo $header_number;?></td>
		</tr>
		<tr>
			<td class="content_row content_row_header" valign="top"><?=$_LANG->get('Erst. Datum')?></td>
			<td class="content_row" valign="top"><?php echo date("d.m.Y",$header_crtdate);?></td>
			<td class="content_row content_row_header" valign="top"><?=$_LANG->get('Erst. Benutzer')?></td>
			<td class="content_row" valign="top"><?php echo $header_crtusr->getNameAsLine();?></td>
		</tr>
		<tr>
			<td class="content_row content_row_header" valign="top"><?=$_LANG->get('Kunde')?></td>
			<td class="content_row" valign="top">
			 <a target="_blank" href="index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id=<?php echo $header_businessc->getId();?>">
			     <?php echo $header_businessc->getNameAsLine();?>
			 </a>
			</td>
			<td class="content_row content_row_header" valign="top"><?=$_LANG->get('Kunde Ansprechp.')?></td>
			<td class="content_row" valign="top">
			 <a target="_blank" href="index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit_cp&cpid=<?php echo $header_businesscp->getId();?>&id=<?php echo $header_businessc->getId();?>">
			     <?php echo $header_businesscp->getNameAsLine();?>
			 </a>
			</td>
		</tr>
		<tr>
			<td class="content_row content_row_header" valign="top"><?=$_LANG->get('Int. Ansprechp.')?></td>
			<td class="content_row" valign="top"><a href="javascript:void(0)" onclick="javascript:jqcc.cometchat.chatWith('<?php echo $header_intcontact->getId();?>');"><?php echo $header_intcontact->getNameAsLine();?></a></td>
			<td class="content_row content_row_header" valign="top"><?=$_LANG->get('Fällig')?></td>
			<td class="content_row" valign="top"><?php if ($header_duedate>0) { echo date("d.m.Y",$header_duedate); } else { echo "N/A"; };?></td>
		</tr>
		<tr>
			<td class="content_row content_row_header" valign="top"><?=$_LANG->get('Bemerkung')?></td>
			<td class="content_row" valign="top"><?php echo $header_comment;?></td>
		</tr>
	</table>
</div>
<br/>
<div class="box1">
    <b>Aufgaben</b>
	<table width="100%">
	   <thead>
    		<tr>
    			<td class="content_row content_row_header" valign="top">Artikel/Maschine</td>
    			<td class="content_row content_row_header" valign="top">Soll Zeit (Std.)</td>
    			<td class="content_row content_row_header" valign="top">&nbsp;</td>
    		</tr>
	   </thead>
	   <?php if (count($jobs)>0){
	       $time_total = 0;
    	   foreach ($jobs as $job){
    	   if($job["amount"]>0){?>
    		<tr>
    		    <form action="index.php?page=<?=$_REQUEST['page']?>&step=1" method="post" name="job_create_<?=$job['objectid']?>" id="job_create_<?=$job['objectid']?>">
    		    <input type="hidden" name="id" value="<?=$_REQUEST["id"]?>"> 
    		    <input type="hidden" name="type" value="<?=$_REQUEST["type"]?>"> 
    		    <input type="hidden" name="jtype" value="<?=$job['type']?>"> 
    		    <input type="hidden" name="jobjectid" value="<?=$job['objectid']?>"> 
    			<td class="content_row" valign="top"><?php echo $job["title"];?></td>
    			<td class="content_row" valign="top"><?php echo printPrice($job["amount"],2);?></td>
    			<td class="content_row" valign="top"><input type="number" value="1" name="workers" style="width: 60px;"/><button type="submit" class="btn btn-primary btn-xs">Job(s) erstellen</button></td>
    			<?php /* Zuweisung muss erfolgen für Typ "MachineEntry" oder "Orderposition" */?>
    			</form>
    		</tr>
	       <?php $time_total += $job["amount"];}}?>
    		<tr>
    			<td class="content_row content_row_header" valign="top">Gesamt</td>
    			<td class="content_row content_row_header" valign="top"><?php echo printPrice($time_total,2);?></td>
    			<td class="content_row content_row_header" valign="top">&nbsp;</td>
    		</tr>
	   <?php }?>
	</table>
</div>
</br>
<?php if ($_REQUEST["step"]==1){
    ?>
    <script type="text/javascript">
//         function workerSelected(i)
//         {
//             if ($("#worker_"+i).val()!="")
//             {
//                 $("#jobevent_"+i).show();
//             } else {
//             	$("#jobevent_"+i).hide();
//             }
//         }
    </script>
    
    <div style="overflow: hidden;">
        <div style="width: 40%; float: left;">
        <?php
        if ($_REQUEST["jtype"] == "ME")
        {
            $me = new Machineentry($_REQUEST["jobjectid"]);
            $workamount = $me->getTime()/60;
            $eachamount = round_up($workamount/$_REQUEST["workers"],2);
            $jobname = $me->getMachine()->getName();
            $color = $me->getMachine()->getColor();
            $qual_users = $me->getMachine()->getQualified_users();
        } else
        {
            $op = new Orderposition($_REQUEST["jobjectid"]);
            $workamount = $op->getQuantity();
            $eachamount = round_up($workamount/$_REQUEST["workers"],2);
            $jobart = new Article($op->getObjectid());
            $jobname = $jobart->getTitle();
            $color = "3a87ad";
            $qual_users = $jobart->getQualified_users();
        }
        $workamount_last = $workamount;
        $t_total = 0;
        $t_total_perc = 0;
        
//         $userselect_opt = '<option value=""> Bitte wählen </option>';
        $userselect_opt = '';
        foreach ($qual_users as $job_usr)
        {
            $user_time = Ticket::getUserSpareTime($job_usr);
            if ($user_time>0 && $user_time>$eachamount)
            {
                $userselect_opt .= '<option value="'.$job_usr->getId().'">'.$job_usr->getNameAsLine2().' ('.printPrice($user_time,2).')</option>';
            }
        }
        ?>
        <div class="box2">
            <b>Job(s) - <?php echo $jobname;?></b>
        	<table>
        	    <thead>
        	       <tr>
        	           <td class="content_row_header">&nbsp;</td>
        	           <td width="110" class="content_row_header">Soll-Zeit</td>
        	           <td width="110" class="content_row_header">%-Ges.Zeit</td>
        	           <td width="110" class="content_row_header">Arbeiter (Std. verf.)</td>
        	           <td width="110" class="content_row_header">Event</td>
        	       </tr>
        	    </thead>
        	    <?php for ($i = 1; $i <= $_REQUEST["workers"]; $i++){?>
        		<tr>
        			<td class="" valign="top">&nbsp;</td>
        			<td class="content_row" valign="top">
        			    <input type="text" style="width:100px" name="work_<?=$i?>" onfocus="markfield(this,0)" 
        			    onblur="markfield(this,1)" value="<?php if ($i == $_REQUEST["workers"]) { echo printPrice($workamount_last,2); } else { echo printPrice($eachamount,2); }?>"/>
        			    <?php if ($i == $_REQUEST["workers"]) { $t_total += $workamount_last; $duration = $workamount_last;} else { $t_total += tofloat(printPrice($eachamount,2)); ; $duration = $eachamount;} 
        			    if ($i < $_REQUEST["workers"]) $workamount_last -= tofloat(printPrice($eachamount,2));?>
        			</td>
        			<td class="content_row" valign="top">
        			    <span id="perc_<?=$i?>"><?php if ($i == $_REQUEST["workers"]) { echo printPrice(percentage($workamount_last, $workamount, 2),2); } else { echo printPrice(percentage($eachamount, $workamount, 2),2); }?>%</span>
        			    <?php if ($i == $_REQUEST["workers"]) { $t_total_perc += tofloat(percentage($workamount_last, $workamount, 2)); } else { $t_total_perc += tofloat(percentage($eachamount, $workamount, 2)); }?>
        			</td>
        			<td class="content_row" valign="top">
        			     <select name="worker_<?=$i?>" id="worker_<?=$i?>" style="width:110px" onchange="workerSelected(<?=$i?>);">
        			     <?php echo $userselect_opt;?>
        			     </select>
        			</td>
        			<td class="content_row" valign="top">
        			<?php $duration = sprintf('%02d:%02d', (int) $duration, fmod($duration, 1) * 60); ?>
        			     <div id="jobevent_<?php echo $i;?>" class="fc-event" style="background-color:#<?php echo $color;?>;border:1px solid #<?php echo $color;?>" 
            			 data-event='{"id":"<?php echo $i;?>","title":"<?php echo $header_number.': '.$jobname.' #'.$i;?>","duration":"<?php echo $duration;?>","stick":"true","constraint":"businessHours","color":"green","due":"<?php echo date("d.m.Y",$header_duedate);?>","users":"[]","usernames":"[]"}'>
            			     <?php echo $header_number.': '.$jobname.' #'.$i;?>
            			</div>
            		</td>
        		</tr>
        		<?php }?>
        		<tr>
        			<td class="" valign="top">Gesamt:</td>
        			<td class="content_row" valign="top"><span id="total_jtime"><?php echo printPrice($t_total,2);?></span></td>
        			<td class="content_row" valign="top"><span id="totaljperc"><?php echo printPrice($t_total_perc,2);?></span></td>
        			<td class="content_row" valign="top">&nbsp;</td>
        			<td class="content_row" valign="top">&nbsp;</td>
        		</tr>
        		<tr>
        			<td class="content_row" valign="top">zu vergeben:</td>
        			<td class="content_row" valign="top"><span id="total_jtime_open"><?php echo "0";?></span></td>
        			<td class="content_row" valign="top"><span id="totaljperc_open"><?php echo "0%";?></span></td>
        			<td class="content_row" valign="top">&nbsp;</td>
        			<td class="content_row" valign="top">&nbsp;</td>
        		</tr>
        	</table>
        </div>
        <br/>
        </div>
        <div style="width: 60%; float: right;">
            <div class="box1">
            	<div id='loading'>loading...</div>
            	<div id='planning_calendar'></div>
            	<script>
                	$(document).ready(function() {
                		$('#planning_calendar').fullCalendar({
                    		height: "auto",
                			header: {
                				left: 'prev,next today',
                				center: 'title',
                				right: 'month,agendaWeek,agendaDay'
                			},
                			defaultView: 'agendaWeek',
                			droppable: true, // this allows things to be dropped onto the calendar
                			editable: true,
                			eventLimit: true, // allow "more" link when too many events
                			eventStartEditable: true,
                			eventDurationEditable: false,
                			weekNumbers: true,
                			events: {
                				url: 'libs/modules/planning/planning.ajax.php',
                				type: 'GET',
                				data: {
                					exec: 'getCalEvents'
                				},
                				error: function() {
                					alert('there was an error while fetching events!');
                				},
                				color: 'red',   // a non-ajax option
                				// textColor: 'black' // a non-ajax option
                			},
                			eventOverlap: true,
                			allDayDefault: false,
                			slotDuration: '00:30:00',
                			snapDuration: '00:01:00',
                			loading: function(bool) {
                				$('#loading').toggle(bool);
                			},
                			drop: function(date) {
                    			$(this).toggle();
                			},
                			eventReceive: function(event) {
                				var check = moment(event.start);
                			    var today = moment();
                			    if(check < today)
                			    {
                		            $('#planning_calendar').fullCalendar( 'removeEvents', event.id )
                		            $('#jobevent_'+event.id).toggle();
                			    }
                			},
                			businessHours: {
                			    start: '00:00',
                			    end: '<?php if ($_REQUEST["jtype"] == "ME") echo $me->getMachine()->getMaxHours().':00'; else echo '23:59';?>',
                			    dow: [ 0, 1, 2, 3, 4, 5, 6 ]
                			}    
                			,eventDrop: function(event, delta, revertFunc) {
                				var check = moment(event.start);
                			    var today = moment();
                			    if(check < today)
                			    {
                			    	revertFunc();
                			    }

                		    }
                		    ,eventRender: function(event, element) {
                    	        element.attr('title', event.title);
                    	        var t = moment(event.start).twix(event.end);
                    	        var duration = event.end-event.start;
                    	        var content = '<h4>'+event.title+'</h4></br>';
                    	        content += 'gepl. Start: '+moment(event.start).format('LLL')+'</br>';
                    	        content += 'gepl. Ende: '+moment(event.end).format('LLL')+'</br>';
                    	        content += 'gepl. Dauer: '+moment(duration).format('HH:mm')+'</br>';
                    	        content += '</br>';
                    	        content += '<b>Fällig: '+event.due+'</b></br>';
                    	        element.qtip({
                    	            content: {
                    	                text: content
                    	            }
                    	        });
                		    }
                		});
                		$('.fc-event').each(function() {
                			$(this).draggable({
                				zIndex: 999,
                				revert: true,      // will cause the event to go back to its
                				revertDuration: 0  //  original position after the drag
                			});
            
                		});
                	});
                </script>
            </div>
        </div>
    </div>
<?php }?>
</br>
</br>
</br>