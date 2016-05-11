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
require_once 'libs/modules/planning/planning.job.class.php';

if (!$_REQUEST["id"])
    die("something went wrong!!");

if ((int)$_REQUEST["delitem"]>0)
{
    $del_item = new PlanningJob((int)$_REQUEST["delitem"]);
    $del_ticket = $del_item->getTicket();
    $del_ticket->delete();
    $del_item->delete();
}

$jobs = Array();


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

if ($_REQUEST["delete"])
{
    $tmp_opos_pjs = PlanningJob::getJobsForObjectAndOpos($colinv->getId(),$_REQUEST["delete"]);
    if (count($tmp_opos_pjs)>0)
    {
        foreach ($tmp_opos_pjs as $tmp_pj)
        {
            $tmp_tick = $tmp_pj->getTicket();
            $tmp_tick->delete();
            $tmp_pj->delete();
        }
    }
}

    if ($_REQUEST["subexec"]=="save")
    {
//        echo '<pre>';print_r($_REQUEST);echo '</pre>';
        if ($_REQUEST["crt_job"])
        {
            $tickets = Array();
            $assigned = "";
            foreach ($_REQUEST["crt_job"] as $job)
            {
                for ($i = 0; $i < $job["numworkers"]; $i++)
                {
                    $pj = new PlanningJob();
                    $pj->setType($job["type"]);
                    $pj->setObject(new CollectiveInvoice((int)$job["object"]));
                    $pj->setOpos(new Orderposition((int)$job["opos"]));
                    if ($pj->getType() == PlanningJob::TYPE_V) {
                        $pj->setSubobject(new Article((int)$job["subobject"]));
                        $pj->setArtmach(new Article((int)$job["artmach"]));
                    } else {
                        $pj->setSubobject(new Order((int)$job["subobject"]));
                        $pj->setArtmach(new Machine((int)$job["artmach"]));
                    }
                    $pj->setTplanned(tofloat($job["workers"]["load"][$i]));
                    $pj->setStart(strtotime($job["start"]));
                    if (substr($job["workers"]["assigned"][$i], 0, 2) == "u_"){
                        $pj->setAssigned_user(new User((int)substr($job["workers"]["assigned"][$i], 2)));
                        $assigned = "user";
                    } elseif (substr($job["workers"]["assigned"][$i], 0, 2) == "g_") {
                        $pj->setAssigned_group(new Group((int)substr($job["workers"]["assigned"][$i], 2)));
                        $assigned = "group";
                    }
//                    print_r($pj); die();
                    $pj->createMyTicket();
                    $pj->save();
                    $ticketlist['"'.$job["opos"].'"'][] = $pj->getTicket()->getId();
                }
            }
            
//             echo "</br>"; print_r($ticketlist); echo "</br>";
            
            foreach ($ticketlist as $tickets)
            {

//                 echo "</br>"; print_r($tickets); echo "</br>";
                foreach ($tickets as $asso_ticket)
                {
                    foreach ($tickets as $asso_link)
                    {
                        if ($asso_ticket != $asso_link)
                        {
//                             echo $asso_ticket . " wird gelinkt mit " . $asso_link . "</br>";
                            $alry_asso[] = $asso_link;
                            $asso = new Association();
                            $asso->setCrtdate(time());
                            $asso->setCrtuser($_USER);
                            $asso->setModule1("Ticket");
                            $asso->setModule2("Ticket");
                            $asso->setObjectid1($asso_ticket);
                            $asso->setObjectid2($asso_link);
                            $asso->save();
                        }
                    }
                }
            }
            
            if ($assigned == "group")
            {
                foreach ($pj->getAssigned_group()->getMembers() as $grmem){
                    if (!Abonnement::hasAbo($pj->getTicket(),$grmem)){
                        $abo = new Abonnement();
                        $abo->setAbouser($grmem);
                        $abo->setModule(get_class($pj->getTicket()));
                        $abo->setObjectid($pj->getTicket()->getId());
                        $abo->save();
                        unset($abo);
                    }
                    if ($grmem->getId() != $_USER->getId()){
                        Notification::generateNotification($grmem, get_class($pj->getTicket()), "AssignGroup", $pj->getTicket()->getNumber(), $pj->getTicket()->getId(), $pj->getTicket()->getAssigned_group()->getName());
                    }
                }
            } else if ($assigned == "user")
            {
                if (!Abonnement::hasAbo($pj->getTicket(),$pj->getAssigned_user())){
                    $abo = new Abonnement();
                    $abo->setAbouser($pj->getAssigned_user());
                    $abo->setModule(get_class($pj->getTicket()));
                    $abo->setObjectid($pj->getTicket()->getId());
                    $abo->save();
                    unset($abo);
                }
                if ($pj->getTicket()->getAssigned_user()->getId() != $_USER->getId()){
                    Notification::generateNotification($pj->getAssigned_user(), get_class($pj->getTicket()), "Assign", $pj->getTicket()->getNumber(), $pj->getTicket()->getId());
                }
            }
            
            $colinv->setStatus(4);
            $colinv->save();
        }
    }


$all_user = User::getAllUser(User::ORDER_NAME);
$all_groups = Group::getAllGroups(Group::ORDER_NAME);
$button = " disabled ";
?>

<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>
<script src='jscripts/calendar/moment.min.js'></script>
<script src='jscripts/calendar/twix.min.js'></script>
<script src='jscripts/qtip/jquery.qtip.min.js'></script>
<link href='jscripts/qtip/jquery.qtip.min.css' rel='stylesheet'/>
<script src="jscripts/jvalidation/dist/jquery.validate.min.js"></script>
<script src="jscripts/format.20110630-1100.min.js"></script>

<script language="JavaScript">
$(function() {
	$('.cal').datetimepicker({
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
		 format:'d.m.Y H:i'
	});
});
</script>
<script type="text/javascript">
function createSelects(id,count,workload)
{
	var x = 0;
	var load = workload / count;
	var html = "";
	for (i = 0; i < count; i++) { 
		html += '<input type="text" name="crt_job['+id+'][workers][load]['+i+']" value="'+format( "#.##0,##", load)+'" style="width: 40px;"/> ';
    	html += '<select name="crt_job['+id+'][workers][assigned]['+i+']" style="width:160px" required>';
        html += '<option disabled>-- Users --</option>';
        <?php 
        foreach ($all_user as $tkt_user){
            ?>
            html += '<option value="u_<?php echo $tkt_user->getId()?>"><?php echo $tkt_user->getNameAsLine()?></option>';
            <?php 
        }
        ?>
        html += '<option disabled>-- Groups --</option>';
        <?php 
        foreach ($all_groups as $tkt_groups){
            ?>
            html += '<option value="g_<?php echo $tkt_groups->getId()?>"><?php echo $tkt_groups->getName()?></option>';
            <?php 
        }
        ?>
        html += '</select></br>';
	}
    $('#workerstd_'+id).html(html);
}
</script>

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
    <b>Job-Kopfdaten</b>
	<table width="100%">
		<tr>
			<td class="content_row content_row_header" valign="top"><?=$_LANG->get('Titel')?></td>
			<td class="content_row" valign="top"><a href="<?php echo $header_parent_link;?>"><?php echo $header_title;?></a></td>
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
			 <a href="index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id=<?php echo $header_businessc->getId();?>">
			     <?php echo $header_businessc->getNameAsLine();?>
			 </a>
			</td>
			<td class="content_row content_row_header" valign="top"><?=$_LANG->get('Kunde Ansprechp.')?></td>
			<td class="content_row" valign="top">
			 <a href="index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit_cp&cpid=<?php echo $header_businesscp->getId();?>&id=<?php echo $header_businessc->getId();?>">
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
    <b>Job-Positionen</b>
    <form action="index.php?page=<?=$_REQUEST['page']?>&id=<?=$_REQUEST["id"]?>" method="post" name="job_create" id="job_create">
    <input type="hidden" name="id" value="<?=$_REQUEST["id"]?>"> 
    <input type="hidden" name="subexec" value="save"> 
    <div style="display: none;" id="removeoposdiv"></div>
	<table width="100%">
	   <thead>
    		<tr>
    			<td class="content_row content_row_header" valign="top">Artikel/Maschine</td>
    			<td class="content_row content_row_header" valign="top">Prod. Beginn</td>
    			<td class="content_row content_row_header" valign="top">Soll Zeit (Std.)</td>
    			<td class="content_row content_row_header" valign="top">Anz. Arbeiter/Jobs</td>
    			<td class="content_row content_row_header" valign="top">Zeit in Std. / Zugew.</td>
    		</tr>
	   </thead>
	   <?php 
       $time_total = 0;
       $x = 0;
	   
	   $orderpositions = Orderposition::getAllOrderposition($colinv->getId());
	   foreach ($orderpositions as $opos)
	   {
	       $opos_pjs = PlanningJob::getJobsForObjectAndOpos($opos->getCollectiveinvoice(),$opos->getId());
	       if (count($opos_pjs)>0 && !empty($opos_pjs))
	       {
	           $opos_article = new Article($opos->getObjectid());
               ?>
               <tr>
                   <td class="content_row" valign="top"><b><?php echo $opos_article->getTitle();?></b></td>
                   <td class="content_row" valign="top"><?php echo date('d.m.Y H:i',$opos_pjs[0]->getStart());?></td>
                   <td class="content_row" valign="top">&nbsp;</td>
                   <td class="content_row" valign="top">&nbsp;</td>
                   <td class="content_row" valign="top"><img src="images/icons/cross.png" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&id=<?=$colinv->getId()?>&delete=<?php echo $opos->getId();?>');"></td>
                   <?php /* $('.col_<?php echo $opos->getCollectiveinvoice();?>opos_<?php echo $opos->getId();?>').each(function(index){this.remove();}); $(this).parent().parent().remove(); $('#removeoposdiv').append('<input type=\'hidden\' name=\'removeopos[]\' value=\'<?php echo $opos->getId();?>\'>'); */?>
               </tr>
               <?php
	           foreach ($opos_pjs as $opj)
	           {

	               ?>
                   <tr class="col_<?php echo $opos->getCollectiveinvoice();?>opos_<?php echo$opos->getId();?>">
                          <td class="content_row" valign="top">
                            <?php 
                            if ($opj->getType() == PlanningJob::TYPE_V)
                                echo $opj->getArtmach()->getTitle();
                            else
                                echo $opj->getArtmach()->getName();
                            ?>
                          </td>
                          <td class="content_row" valign="top"><?php echo date('d.m.Y H:i',$opos_pjs[0]->getStart());?></td>
              			   <td class="content_row" valign="top"><?php echo printPrice($opj->getTplanned(),2);?></td>
                  		   <td class="content_row" valign="top">&nbsp;</td>
                  		   <td class="content_row" valign="top">
                  		    <?php if ($opj->getAssigned_user()->getId()>0) echo $opj->getAssigned_user()->getNameAsLine(); else echo $opj->getAssigned_group()->getName();
                  		    echo ' (<a href="index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid='. $opj->getTicket()->getId() .'">' . $opj->getTicket()->getNumber() . ': '.$opj->getTicket()->getState()->getTitle().'</a>)'; ?>
                  		   </td>
                  	   </tr>
                   <?php
	           }
	       } else {
	           $button = " enabled ";
    	       $opos_article = new Article($opos->getObjectid());
               ?>
               <tr>
                   <td class="content_row" valign="top"><b><?php echo $opos_article->getTitle();?></b></td>
                   <td class="content_row" valign="top">
                       <input type="text" style="width:100px" id="<?php echo $opos->getCollectiveinvoice()."_".$opos->getId();?>" 
              			class="cal text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
               			onfocus="markfield(this,0)" onblur="markfield(this,1)"
               			value="<?php echo date('d.m.Y H:i');?>" onchange="$('.artcal_<?php echo $opos->getCollectiveinvoice()."_".$opos->getId();?>').val($(this).val());"/>
             	   </td>
                   <td class="content_row" valign="top">&nbsp;</td>
                   <td class="content_row" valign="top">&nbsp;</td>
                   <td class="content_row" valign="top">&nbsp;</td>
               </tr>
               <?php
    	       if ($opos_article->getOrderid()>0)
    	       {
    	           $order = new Order($opos_article->getOrderid());
    	           $calcs = Calculation::getAllCalculations($order);
    	   
    	           foreach ($calcs as $calc)
    	           {
    	               if ($calc->getState() && $calc->getAmount()==$opos->getQuantity())
    	               {
    	                   $mes = Machineentry::getAllMachineentries($calc->getId());
    	                   foreach ($mes as $me)
    	                   {
                               ?>
    	                       <tr>
    	                           <input type="hidden" name="crt_job[<?php echo $x;?>][type]" value="<?php echo PlanningJob::TYPE_K;?>"/>
    	                           <input type="hidden" name="crt_job[<?php echo $x;?>][object]" value="<?php echo $opos->getCollectiveinvoice();?>"/>
    	                           <input type="hidden" name="crt_job[<?php echo $x;?>][opos]" value="<?php echo $opos->getId();?>"/>
    	                           <input type="hidden" name="crt_job[<?php echo $x;?>][subobject]" value="<?php echo $opos_article->getOrderid();?>"/>
    	                           <input type="hidden" name="crt_job[<?php echo $x;?>][artmach]" value="<?php echo $me->getMachine()->getId();?>"/>
    	                           <input type="hidden" id="crt_job_workload_<?php echo $x;?>" value="<?php echo $me->getTime();?>"/>
                                   <td class="content_row" valign="top"><?php echo $me->getMachine()->getName();?></td>
                                   <td class="content_row" valign="top">
                                        <input type="text" name="crt_job[<?php echo $x;?>][start]" 
                              			class="artcal_<?php echo $opos->getCollectiveinvoice()."_".$opos->getId();?> cal text format-d-m-y divider-dot"
                               			value="<?php echo date('d.m.Y H:i');?>" style="width:100px;"/>
               			           </td>
                       			   <td class="content_row" valign="top"><?php echo printPrice($me->getTime()/60,2);?></td>
                           		   <td class="content_row" valign="top"><input type="number" pattern="^[0-9]" min="1" step="1" value="<?php echo "1";?>" name="crt_job[<?php echo $x;?>][numworkers]" onchange="createSelects(<?php echo $x;?>,$(this).val(),$('#crt_job_workload_<?php echo $x;?>').val());" style="width: 60px;"/></td>
                           		   <td class="content_row" valign="top" id="workerstd_<?php echo $x;?>">
       		                            <input type="text" name="crt_job[<?php echo $x;?>][workers][load][0]" value="<?php echo printPrice($me->getTime()/60,2);?>" style="width: 40px;"/>
                   		                <select name="crt_job[<?php echo $x;?>][workers][assigned][0]" style="width:160px" required>
                                        <option disabled>-- Users --</option>
                                        <?php 
                                        foreach ($all_user as $tkt_user){
                                            echo '<option value="u_'.$tkt_user->getId().'">'.$tkt_user->getNameAsLine().'</option>';
                                        }
                                        ?>
                                        <option disabled>-- Groups --</option>
                                        <?php 
                                        foreach ($all_groups as $tkt_groups){
                                            echo '<option value="g_'.$tkt_groups->getId().'">'.$tkt_groups->getName().'</option>';
                                        }
                                        ?>
                                        </select>
                           		   </td>
                           	   </tr>
    	                       <?php
    	                       $time_total += printPrice($me->getTime()/60,2);
    	                       $x++;
    	                       $tmp_planned_jobs = PlanningJob::getAllJobs(" AND object = {$_REQUEST["id"]} AND subobject = {$me->getId()} AND artmach = {$me->getMachine()->getId()}");
    	                       if (count($tmp_planned_jobs)==0)
    	                           $jobs[] = Array("article"=>$opos_article->getTitle(), "title" => $me->getMachine()->getName(), "amount" => $me->getTime()/60, "type" => "ME", "objectid" => $me->getId());
    	                   }
    	               }
    	           }
    	       } else {
    	           ?>
                   <tr>
                       <input type="hidden" name="crt_job[<?php echo $x;?>][type]" value="<?php echo PlanningJob::TYPE_V;?>"/>
                       <input type="hidden" name="crt_job[<?php echo $x;?>][object]" value="<?php echo $opos->getCollectiveinvoice();?>"/>
                       <input type="hidden" name="crt_job[<?php echo $x;?>][opos]" value="<?php echo $opos->getId();?>"/>
                       <input type="hidden" name="crt_job[<?php echo $x;?>][subobject]" value="<?php echo $opos_article->getId();?>"/>
                       <input type="hidden" name="crt_job[<?php echo $x;?>][artmach]" value="<?php echo $opos_article->getId();?>"/>
                       <input type="hidden" id="crt_job_workload_<?php echo $x;?>" value="<?php echo $opos->getQuantity();?>"/>
                       <td class="content_row" valign="top"><?php echo $opos_article->getTitle();?></td>
                       <td class="content_row" valign="top">
                            <input type="text" name="crt_job[<?php echo $x;?>][start]" 
                  			class="artcal_<?php echo $opos->getCollectiveinvoice()."_".$opos->getId();?> cal text format-d-m-y divider-dot" 
                   			value="<?php echo date('d.m.Y H:i');?>" readonly style="width:100px; background-color:#EBEBE4;border:1px solid #ABADB3;padding:2px 1px;color:rgb(84, 84, 84);"/>
    		           </td>
              		   <td class="content_row" valign="top"><?php echo printPrice($opos->getQuantity(),2);?></td>
               		   <td class="content_row" valign="top"><input type="number" pattern="^[0-9]" min="1" step="1" value="<?php echo "1";?>" name="crt_job[<?php echo $x;?>][numworkers]" onchange="createSelects(<?php echo $x;?>,$(this).val(),$('#crt_job_workload_<?php echo $x;?>').val());" style="width: 60px;"/></td>
               		   <td class="content_row" valign="top" id="workerstd_<?php echo $x;?>">
       		                <input type="text" name="crt_job[<?php echo $x;?>][workers][load][0]" value="<?php echo printPrice($opos->getQuantity(),2);?>" style="width: 40px;"/>
       		                <select name="crt_job[<?php echo $x;?>][workers][assigned][0]" style="width:160px" required>
                            <option disabled>-- Users --</option>
                            <?php 
                            foreach ($all_user as $tkt_user){
                                echo '<option value="u_'.$tkt_user->getId().'">'.$tkt_user->getNameAsLine().'</option>';
                            }
                            ?>
                            <option disabled>-- Groups --</option>
                            <?php 
                            foreach ($all_groups as $tkt_groups){
                                echo '<option value="g_'.$tkt_groups->getId().'">'.$tkt_groups->getName().'</option>';
                            }
                            ?>
                            </select>
               		   </td>
              	   </tr>
                   <?php
                   $time_total += printPrice($opos->getQuantity(),2);
                   $x++;
                   
    	           $tmp_planned_jobs = PlanningJob::getAllJobs(" AND object = {$_REQUEST["id"]} AND subobject = {$opos->getId()} AND artmach = {$opos_article->getId()}");
    	           if ($opos_article->getIsWorkHourArt() && count($tmp_planned_jobs)==0)
    	           {
    	               $jobs[] = Array("article"=>$opos_article->getTitle(), "title" => $opos_article->getTitle(), "amount" => $opos->getQuantity(), "type" => "OP", "objectid" => $opos->getId());
    	           }
    	       }
	       }
	   }
	   ?>
		<tr>
			<td class="content_row content_row_header" valign="top">Gesamt</td>
            <td class="content_row" valign="top">&nbsp;</td>
			<td class="content_row content_row_header" valign="top"><?php echo printPrice($time_total,2);?></td>
            <td class="content_row" valign="top">&nbsp;</td>
			<td class="content_row content_row_header" valign="top">
			 <button type="submit" <?php if (count($opos_pjs)>0 && !empty($opos_pjs)) echo $button;?>class="btn btn-primary btn-xs">Job(s) erstellen</button>
			</td>
		</tr>
	</table>
	</form>
</div>
</br>