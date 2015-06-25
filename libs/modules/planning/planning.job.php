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
<script src='jscripts/calendar/moment.min.js'></script>
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
			<td class="content_row" valign="top">&nbsp;</td>
			<td class="content_row" valign="top">&nbsp;</td>
		</tr>
	</table>
</div>
<br/>
<div class="box1">
    <b>Aufgaben</b>
    <form action="index.php?page=<?=$_REQUEST['page']?>&step=1" method="post" name="job_create_<?=$job['objectid']?>" id="job_create_<?=$job['objectid']?>">
    <input type="hidden" name="id" value="<?=$_REQUEST["id"]?>"> 
    <input type="hidden" name="type" value="<?=$_REQUEST["type"]?>"> 
	<table width="100%">
	   <thead>
    		<tr>
    			<td class="content_row content_row_header" valign="top">Artikel/Maschine</td>
    			<td class="content_row content_row_header" valign="top">Soll Zeit (Std.)</td>
    			<td class="content_row content_row_header" valign="top">Simultan?</td>
    			<td class="content_row content_row_header" valign="top">Anz. Arbeiter/Jobs</td>
    		</tr>
	   </thead>
	   <?php 
	       if (count($jobs)>0){
    	       $time_total = 0;
    	       $x = 0;
        	   foreach ($jobs as $job){
            	   if($job["amount"]>0){?>
                		<tr>
                            <input type="hidden" name="job[<?php echo $x;?>][type]" value="<?=$job['type']?>"/> 
                            <input type="hidden" name="job[<?php echo $x;?>][object]" value="<?=$job['objectid']?>"/> 
                			<td class="content_row" valign="top"><?php echo $job["title"];?></td>
                			<td class="content_row" valign="top"><?php echo printPrice($job["amount"],2);?></td>
                			<td class="content_row" valign="top"><input type="checkbox" value="1" name="job[<?php echo $x;?>][simultaneous]" <?php if ($_REQUEST["job"][$x]["simultaneous"]) echo "checked";?>/></td>
                			<td class="content_row" valign="top"><input type="number" value="<?php if ($_REQUEST["job"][$x]["workers"]) echo $_REQUEST["job"][$x]["workers"]; else echo "1";?>" name="job[<?php echo $x;?>][workers]" style="width: 60px;"/>
                		</tr>
            	       <?php $time_total += $job["amount"];
            	   }
            	   $x++;
        	   }?>
        		<tr>
        			<td class="content_row content_row_header" valign="top">Gesamt</td>
        			<td class="content_row content_row_header" valign="top"><?php echo printPrice($time_total,2);?></td>
        			<td class="content_row content_row_header" valign="top">&nbsp;</td>
        			<td class="content_row content_row_header" valign="top"><?php if (count($jobs)>0) echo '<button type="submit" class="btn btn-primary btn-xs">Job(s) erstellen</button></td>';?></td>
        		</tr>
	   <?php }?>
	</table>
	</form>
</div>
</br>
<?php if ($_REQUEST["step"]==1){
    ?>
    <script language="JavaScript">
    	function recalc(id,job,iterator)
    	{
    	    var total = $('#total_jtime').html();
    	    total = parseFloat(total.replace(",", "."));
    	    var total_left = $('#total_jtime_open').html();
    	    total_left = parseFloat(total_left.replace(",", "."));
    	    var total_fixed = $('#total_jtime_fixed').html();
    	    total_fixed = parseFloat(total_fixed.replace(",", "."));
    	    var old_value = $('#job_amount_old_'+job+'_'+id).val();
    	    old_value = parseFloat(old_value.replace(",", "."));
    	    var new_value = $('#job_amount_'+job+'_'+id).val();
    	    $('#job_amount_old_'+job+'_'+id).val(new_value);
    	    new_value = parseFloat(new_value.replace(",", "."));
    	    if (old_value > new_value)
    	    {
    	       var diff = old_value-new_value;
    	       total = total-diff;
    	    }
    	    else
    	    {
 	    	   var diff = new_value-old_value;
 	    	   total = total+diff;
    	    }
    	    for (i=1;i<=iterator;i++)
    	    {
    	    	var amount = $('#job_amount_'+job+'_'+i).val();
    	    	amount = parseFloat(amount.replace(",", "."));
    	    	var perc = (amount / total) * 100;
    	    	perc = perc.toFixed(2);
    	    	perc = perc.toString().replace(".", ",");
    	    	$('#perc_'+job+'_'+i).html(perc+"%");
    	    }
    	    total = total.toFixed(2);
    	    total_left = total_fixed - total;
    	    if (total_left != 0)
    	    {
        	    total_left = total_left.toFixed(2).toString().replace(".", ",");
        	    $('#total_jtime_open').html(total_left);
        	    $('#total_jtime_open').addClass('error');
    	    } else
    	    {
        	    total_left = total_left.toFixed(2).toString().replace(".", ",");
        	    $('#total_jtime_open').html(total_left);
        	    $('#total_jtime_open').removeClass('error');
    	    }
    	    total = total.toString().replace(".", ",");
    	    $('#total_jtime').html(total);
    	}
    </script>
    <div>
    <?php
    $jobx = 0;
    foreach ($_REQUEST["job"] as $req_job)
    {
        if ($req_job["type"] == "ME")
        {
            $me = new Machineentry($req_job["object"]);
            $workamount = $me->getTime()/60;
            $eachamount = round_up($workamount/$req_job["workers"],2);
            $jobname = $me->getMachine()->getName();
            $color = $me->getMachine()->getColor();
            $qual_users = $me->getMachine()->getQualified_users();
        } else
        {
            $op = new Orderposition($req_job["object"]);
            $workamount = $op->getQuantity();
            $eachamount = round_up($workamount/$req_job["workers"],2);
            $jobart = new Article($op->getObjectid());
            $jobname = $jobart->getTitle();
            $color = "3a87ad";
            $qual_users = $jobart->getQualified_users();
        }
        $workamount_last = $workamount;
        $t_total = 0;
        $t_total_perc = 0;
        
        ?>
        <div class="box2">
            <form action="index.php?page=<?=$_REQUEST['page']?>&step=2&subexec=save" method="post">
            <input type="hidden" name="job[<?php echo $jobx;?>][type]" value="<?=$job['type']?>"/> 
            <input type="hidden" name="job[<?php echo $jobx;?>][object]" value="<?=$job['objectid']?>"/> 
            <b>Job(s) - <?php echo "<font color='{$color}'>".$jobname."</font>";?></b>
        	<table width="100%">
        	    <thead>
        	       <tr>
        	           <td width="80" class="content_row_header">&nbsp;</td>
        	           <td width="110" class="content_row_header">Soll-Zeit</td>
        	           <td width="110" class="content_row_header">%-Ges.Zeit</td>
        	           <td width="350" class="content_row_header">Fällig</td>
        	           <td width="190" class="content_row_header">Ticket MA</td>
        	           <td class="content_row_header">&nbsp;</td>
        	       </tr>
        	    </thead>
        	    <?php 
        	    $percent_left = 100;
        	    for ($i = 1; $i <= $req_job["workers"]; $i++){
        	        if ($i == $req_job["workers"]) 
        	        { 
        	            $t_total += $workamount_last; 
        	            $duration = $workamount_last;
        	            $t_total_perc += $percent_left;
        	            $print_workamount = printPrice($workamount_last,2);
        	            $print_percentage = printPrice($percent_left,2);
        	        } else { 
        	            $t_total += tofloat(printPrice($eachamount,2));
        	            $duration = $eachamount;
        	            $t_total_perc += tofloat(percentage($eachamount, $workamount, 2));
        	            $percent_left -= tofloat(percentage($eachamount, $workamount, 2));
        	            $print_workamount = printPrice($eachamount,2);
        	            $print_percentage = printPrice(percentage($eachamount, $workamount, 2),2);
        	        }
        	        if ($i < $req_job["workers"]) 
        	            $workamount_last -= tofloat(printPrice($eachamount,2));
        	        ?>
                    <script language="JavaScript">
                        $(function() {
                        	$('#job_due_<?=$jobx?>_<?=$i?>').datetimepicker({
                        		 lang:'de',
                        		 i18n:{
                        		  de:{
                        		   months:[
                        		    'Januar','Februar','März','April',
                        		    'Mai','Juni','Juli','August',
                        		    'September','Oktober','November','Dezember',
                        		   ],
                        		   dayOfWeek:[
                        		    "So.", "Mo", "Di", "Mi", 
                        		    "Do", "Fr", "Sa.",
                        		   ]
                        		  }
                        		 },
                        		 timepicker:true,
                        		 format:'d.m.Y H:i',
                        		 minDate:'0',
                        		 inline: true,
                        		 weeks:true,
                        		 onChangeMonth:function(ct,$i){
                            		 var amount = $('#job_amount_<?=$jobx?>_<?=$i?>').val();
                        			 $.ajax({
                     		    		type: "GET",
                     		    		url: "libs/modules/planning/planning.ajax.php",
                     		    		data: { exec: "ajax_getDisabledDates", date: ct.dateFormat('d.m.Y'), amount: amount, type: "<?=$job['type']?>", objectid: "<?=$req_job["object"]?>" },
                     		    		success: function(data) 
                     		    		    {
                     		        			return;
                     		    		    }
                     		    	});
                       			 }
                        	});
                        	$('#job_amount_<?=$jobx?>_<?=$i?>').change(function() {
                            	recalc(<?=$i?>,<?=$jobx?>,<?=count($req_job["workers"])+1?>);
                            	
                        		$('#job_due_<?=$jobx?>_<?=$i?>').datetimepicker({
                        			disabledDates: ['25.06.2015','26.06.2015']
                     		        ,formatDate:'d.m.Y'
                        		});
                        	});
                    	});
                    </script>
            		<tr style="background-color: <?php if($i % 2 != 0) echo '#eaeaea'; else echo '#eadddd';?>;">
            			<td class="content_row" valign="top">#<?php echo $i;?></td>
            			<td class="content_row" valign="top">
            			    <input type="text" style="width:100px" id="job_amount_<?=$jobx?>_<?=$i?>" name="job[<?php echo $jobx;?>][jobs][<?=$i?>][amount]" onfocus="markfield(this,0)" 
            			    onblur="markfield(this,1)" value="<?php echo $print_workamount;?>"/>
            			    <input type="hidden" id="job_amount_old_<?=$jobx?>_<?=$i?>" value="<?php echo $print_workamount;?>"/>
            			</td>
            			<td class="content_row" valign="top">
            			    <span id="perc_<?=$jobx?>_<?=$i?>"><?php echo $print_percentage;?>%</span>
            			</td>
        			    <td class="content_row" valign="top">
        			         <input type="text" style="width:350px" id="job_due_<?=$jobx?>_<?=$i?>" name="job[<?php echo $jobx;?>][jobs][<?=$i?>][due]" 
                			 class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency" 
                			 onfocus="markfield(this,0)" onblur="markfield(this,1)" 
                			 value="<? echo date('d.m.Y H:i');?>"/>
            			     <input type="hidden" id="job_duemonth_<?=$jobx?>_<?=$i?>" value="<?php echo date("m");?>"/>
        			    </td>
            			<td class="content_row" valign="top">
            			     <select name="job[<?php echo $jobx;?>][jobs][<?=$i?>][worker]" id="job_worker_<?=$jobx?>_<?=$i?>" style="width:180px"></select>
            			</td>
        			    <td class="content_row" valign="top">&nbsp;</td>
            		</tr>
        		<?php }?>
        		<tr>
        			<td class="content_row" valign="top">Gesamt:</td>
        			<td class="content_row" valign="top"><span id="total_jtime"><?php echo printPrice($t_total,2);?></span><span id="total_jtime_fixed" style="display: none;"><?php echo printPrice($t_total,2);?></span></td>
        			<td class="content_row" valign="top">&nbsp;</td>
        			<td class="content_row" valign="top">&nbsp;</td>
        			<td class="content_row" valign="top">&nbsp;</td>
        			<td class="content_row" valign="top">&nbsp;</td>
        		</tr>
        		<tr>
        			<td class="content_row" valign="top">zu vergeben:</td>
        			<td class="content_row" valign="top"><span id="total_jtime_open"><?php echo "0";?></span></b></td>
        			<td class="content_row" valign="top">&nbsp;</td>
        			<td class="content_row" valign="top">&nbsp;</td>
        			<td class="content_row" valign="top">&nbsp;</td>
        			<td class="content_row" valign="top">&nbsp;</td>
        		</tr>
        	</table>
            </form>
        </div>
        </br>
    <?php
    $jobx++;
    }
    ?>
    <br/>
    </div>
<?php }?>
</br>
</br>
</br>