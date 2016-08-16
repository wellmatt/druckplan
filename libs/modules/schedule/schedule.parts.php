<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       30.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/documents/document.class.php';

$sched = new Schedule((int)$_REQUEST["id"]);

if($_REQUEST["subexec"] == "deletepart")
{
    $part = new SchedulePart((int)$_REQUEST["part_id"]);
    $savemsg = getSaveMessage($part->delete());
    echo $DB->getLastError();
}

$parts = SchedulePart::getAllScheduleParts($sched->getId());
$adj_tickets = Ticket::getTicketsForObject(get_class($sched), $sched->getId());
//gln, nur Lieferscheine und Etiketten anzeigen
//$adj_docs = Document::getDocuments(Array("requestId" => $sched->getDruckplanId(), "module" => Document::REQ_MODULE_ORDER));
$adj_docs = array();
if ($sched->getDruckplanId() > 0) {
	$adj_docs = Document::getDocuments(Array("type" => Document::TYPE_DELIVERY,"requestId" => $sched->getDruckplanId(), "module" => Document::REQ_MODULE_ORDER));
	$adj_docs = array_merge($adj_docs, Document::getDocuments(Array("type" => Document::TYPE_LABEL,"requestId" => $sched->getDruckplanId(), "module" => Document::REQ_MODULE_ORDER)));
}
?>

<script language="javascript">
function saveStats(mode, job_id, newval, objprefix, color)
{
    $.post("libs/modules/schedule/schedule.ajax.php", {exec: 'setstatus', id: job_id, mode: mode, newval: newval}, 
    	    function(data) {
        	if(data == "1")
        	{
        	    for(var x=0; x<5; x++)
        	    {
        	        var obj = document.getElementById(objprefix +'_' +eval(x));
        	        if(obj != null)
        	        {
        	            if(x == newval)
        	                obj.src = './images/status/'+color+'.gif';
        	            else
        	                obj.src = './images/status/black.gif';
        	        }
        	    }
        	}            	
	});
	
}
</script>

<?php 
if ($sched->getId() > 0){
    // Associations
    $association_object = $sched;
    include 'libs/modules/associations/association.include.php';
    //-> END Associations
}
?>

<table width="100%">
   <tr>
      <td width="600" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
          <?=$_LANG->get('Auftrag')?>: <?=$sched->getNumber()?>, <?=$_LANG->get('Erstellt von')?>: <?=$sched->getCreateuser()?>
      </td>
      <td align="right"><?=$savemsg?></td>
   </tr>
</table>

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
        <td class="content_row_subheader"><?=$_LANG->get('Verkn. Ticket')?></td>
        <td class="content_row_subheader"><?=$_LANG->get('Optionen')?></td>
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
        <td class="content_row" valign="top" align="center"><?=count(SchedulePart::getAllScheduleParts($sched->getId()))?></td>
        <td class="content_row" valign="top">
		<?	if(count($adj_tickets) > 0){ 
				foreach ($adj_tickets AS $tkt){ ?>
					<a href="index.php?page=libs/modules/tickets/tickets.php&exec=edit&tktid=<?=$tkt->getId()?>"><?=$tkt->getNumber()?></a> <br/><br/> 
		<?		}
			} ?>
		</td>	
        <td class="content_row" valign="top">
	        <nobr>
		        <?=$_LANG->get('Auftrag')?>:
		        <a class="link" href="index.php?page=libs/modules/calculation/order.php&exec=edit&step=4&id=<?=$sched->getDruckplanId()?>"><?=$_LANG->get('Bearbeiten')?></a>
	        </nobr>
	        <br>
	        <nobr> 
	        	<?=$_LANG->get('Teilauftrag')?>:
	        	<a class="link" href="index.php?page=<?=$_REQUEST['page']?>&exec=editparts&id=<?=$sched->getId()?>"><?=$_LANG->get('Anlegen')?></a>
	        </nobr>
	        <br/>
	        <nobr>
        	<?=$_LANG->get('Ticket')?>: 
        	<a class="link" href="index.php?page=libs/modules/tickets/tickets.php&exec=new&subexec=newforplan&planid=<?=$sched->getId()?>"
        		><?=$_LANG->get('Anlegen')?></a>
        </nobr>
        </td>
		
    </tr>
</table>
</div>
<br>
<div class="box1">
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td width="80" class="content_row_clear"><?=$_LANG->get('DTP-Status:')?></td>
			<td width="25" class="content_row_clear"><a
				href="javascript: saveStats('dtp','<?=$sched->getId()?>','0','idx_img_dtp_0','red')">
					<img id="idx_img_dtp_0_0" class="select"
					src="./images/status/<? 
         if((int)$sched->getStatusDtp() == 0) 
            echo 'red';
         else 
             echo 'black';
         ?>.gif"
					alt="<?=$_LANG->get('DTP-Status:')?> <?=$_LANG->get('Rot')?>" title="<?=$_LANG->get('TODO');?>">
			</a>
			</td>
			<td width="25" class="content_row_clear"><a
				href="javascript: saveStats('dtp','<?=$sched->getId()?>','1','idx_img_dtp_0','yellow')">
					<img id="idx_img_dtp_0_1" class="select"
					src="./images/status/<? 
         if((int)$sched->getStatusDtp() == 1) 
            echo 'yellow';
         else 
             echo 'black';
         ?>.gif"
					alt="<?=$_LANG->get('DTP-Status:')?> <?=$_LANG->get('Gelb')?>" title="<?=$_LANG->get('In Arbeit');?>">
			</a>
			</td>
			<!-- td width="25" class="content_row_clear"><a
				href="javascript: saveStats('dtp','<?=$sched->getId()?>','2','idx_img_dtp_0','orange')">
					<img id="idx_img_dtp_0_2" class="select"
					src="./images/status/<? 
         if((int)$sched->getStatusDtp() == 2) 
            echo 'orange';
         else 
             echo 'black';
         ?>.gif"
					alt="<?=$_LANG->get('DTP-Status:')?> <?=$_LANG->get('Orange')?>" title="<?=$_LANG->get('In Arbeit');?>">
			</a>
			</td-->
			<td width="25" class="content_row_clear"><a
				href="javascript: saveStats('dtp','<?=$sched->getId()?>','3','idx_img_dtp_0','green')">
					<img id="idx_img_dtp_0_3" class="select"
					src="./images/status/<? 
         if((int)$sched->getStatusDtp() == 3) 
            echo 'green';
         else 
             echo 'black';
         ?>.gif"
					alt="<?=$_LANG->get('DTP-Status:')?> <?=$_LANG->get('Gr&uuml;n')?>" title="<?=$_LANG->get('Platte fertig');?>">
			</a>
			</td>
			<td width="10" class="content_row_clear"></td>
			<td width="100" class="content_row_clear">Papier-Status:</td>
			<td width="25" class="content_row_clear"><a
				href="javascript: saveStats('paper','<?=$sched->getId()?>','0','idx_img_paper_0','red')">
					<img id="idx_img_paper_0_0" class="select"
					src="./images/status/<?
         if((int)$sched->getStatusPaper() == 0) 
            echo "red";
         else
             echo "black";
         ?>.gif"
					alt="<?=$_LANG->get('Papier-Status:')?> <?=$_LANG->get('Rot')?>" title="<?=$_LANG->get('TODO');?>">
			</a>
			</td>
			<td width="25" class="content_row_clear"><a
				href="javascript: saveStats('paper','<?=$sched->getId()?>','1','idx_img_paper_0','yellow')">
					<img id="idx_img_paper_0_1" class="select"
					src="./images/status/<?
         if((int)$sched->getStatusPaper() == 1) 
            echo "yellow";
         else
             echo "black";
         ?>.gif"
					alt="<?=$_LANG->get('Papier-Status:')?> <?=$_LANG->get('Gelb')?>" title="<?=$_LANG->get('bestellt');?>">
			</a>
			</td>
			<td width="25" class="content_row_clear"><a
				href="javascript: saveStats('paper','<?=$sched->getId()?>','2','idx_img_paper_0','green')">
					<img id="idx_img_paper_0_2" class="select"
					src="./images/status/<?
         if((int)$sched->getStatusPaper() == 2) 
            echo "green";
         else
             echo "black";
         ?>.gif"
					alt="<?=$_LANG->get('Papier-Status:')?> <?=$_LANG->get('Gr&uuml;n')?>" title="<?=$_LANG->get('Auf Lager');?>">
			</a>
			</td>
			<td width="10" class="content_row_clear"></td>
			
			<? // ----------------------- Auftragsstatus --------------------------------- ?>
			<td width="110" class="content_row_clear"><?=$_LANG->get('Auftrags-Status')?>:</td>
			<td width="25" class="content_row_clear"><a
				href="javascript: saveStats('status','<?=$sched->getId()?>','1','idx_img_jobstat_0','red')">
					<img id="idx_img_jobstat_0_1" class="select"
					src="./images/status/<?
         if((int)$sched->getStatus() == 1) 
            echo "red";
         else
             echo "black";
         ?>.gif"
					alt="<?=$_LANG->get('Auftrags-Status:')?> <?=$_LANG->get('Rot')?>" title="<?=$_LANG->get('Angelegt');?>">
			</a>
			</td>
			<td width="25" class="content_row_clear"><a
				href="javascript: saveStats('status','<?=$sched->getId()?>','2','idx_img_jobstat_0','yellow')">
					<img id="idx_img_jobstat_0_2" class="select"
					src="./images/status/<?
         if((int)$sched->getStatus() == 2) 
            echo "yellow";
         else
             echo "black";
         ?>.gif"
					alt="<?=$_LANG->get('Auftrags-Status:')?> <?=$_LANG->get('Rot')?>" title="<?=$_LANG->get('Platte fertig');?>">
			</a>
			</td>
			<td width="25" class="content_row_clear"><a
				href="javascript: saveStats('status','<?=$sched->getId()?>','3','idx_img_jobstat_0','lila')">
					<img id="idx_img_jobstat_0_3" class="select"
					src="./images/status/<?
         if((int)$sched->getStatus() == 3) 
            echo "lila";
         else
             echo "black";
         ?>.gif"
					alt="<?=$_LANG->get('Auftrags-Status:')?> <?=$_LANG->get('Lila')?>" title="<?=$_LANG->get('Druck fertig');?>">
			</a>
			</td>
			<td width="25" class="content_row_clear"><a
				href="javascript: saveStats('status','<?=$sched->getId()?>','4','idx_img_jobstat_0','green')">
					<img id="idx_img_jobstat_0_4" class="select"
					src="./images/status/<?
         if((int)$sched->getStatus() == 4) 
            echo "green";
         else
             echo "black";
         ?>.gif"
					alt="<?=$_LANG->get('Auftrags-Status:')?> <?=$_LANG->get('Gr&uuml;n')?>" title="<?=$_LANG->get('Auftrag fertig');?>">
			</a>
			</td>
			
			<td width="85">&emsp; <? /* &emsp;<?=$_LANG->get('Dokumente');?>: */ ?></td>
			<td> 
			<? /*	foreach ($adj_docs AS $doc){?>
					<a href="libs/modules/documents/document.get.iframe.php?getDoc=<?=$doc->getId()?>&version=print"><?=$doc->getName()?></a>
					&emsp;
			<?	} */ ?>
			</td>
			
		</tr>
	</table>
</div>
<br>
<div class="box2">
    
<? 
$ta = 1;
foreach($parts as $part)
{
    unset($machs);
    $machs = Array();
    // Maschinengruppen gehen von 1 - 7
    foreach(MachineGroup::getAllMachineGroups() as $g)
    {
        $t = ScheduleMachine::getAllScheduleMachines($part->getId(), ScheduleMachine::FILTER_MACHINEGROUP, $g->getId());
        if($t)
            $machs[$g->getId()] = $t;
        else 
            $machs[$g->getId()] = Array();
    }
    
    $cols = 8;
    // Anzahl Spalten z�hlen
    foreach($machs as $m)
        if(count($m))
            $cols += count($m)-1;

    $colWidth = (int)(100 / $cols);
    
    $i = 0;
    ?>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <colgroup>
        <? 
        for ($x = 0; $x < $cols; $x++)
        {
            echo '<col width="'.$colWidth.'%">';
        }
        ?>
        </colgroup>
        <tr>
            <td class="content_row_header">
                <?=$_LANG->get('Teilauftrag')?> #<?=$ta?>
            </td>
            <?
                foreach (MachineGroup::getAllMachineGroups() as $g)
                {
                    echo '<td class="content_row_header">'.$g->getName().'</td>';
                    foreach($machs[$g->getId()] as $key2 => $sm)
                    {
                        if($key2 > 0)
                            echo '<td class="content_row_header">'.$g->getName().'</td>';
                    }
                } 
        
            ?>
           
        </tr>
        
        <tr class="<?=getRowColor($i)?>">
            <td class="content_row"><?=$_LANG->get('Maschine')?></td>
            <?
                foreach ($machs as $val)
                {
                    if(count($val))
                        foreach($val as $key2 => $sm)
                        {
                            echo '<td class="content_row">';
                            if ($sm->getFinished())
                                echo '<span class="ok">';
                            echo '<a href="index.php?page=libs/modules/schedule/schedule.php&exec=showmachine&id='.$sm->getMachine()->getId().'">'.$sm->getMachine()->getName().'</a>';
                            if ($sm->getFinished())
                                echo '</span>';
                            echo '</td>';
                        }
                    else
                        echo '<td class="content_row">&nbsp;</td>';
                } 
                $i++;
            ?>

        </tr>
         
        <? if($_USER->hasRightsByGroup(Group::RIGHT_SEE_TARGETTIME)) { ?>
        <tr class="<?=getRowColor($i)?>">
            <td class="content_row"><?=$_LANG->get('Sollzeit in Std.')?></td>
            <?
                foreach ($machs as $val)
                {
                    if(count($val))
                        foreach($val as $key2 => $sm)
                        {
                            echo '<td class="content_row">'.$sm->getTargetTime().'</td>';
                        }
                    else
                        echo '<td class="content_row">&nbsp;</td>';
                } 
                $i++;
            ?>
        </tr> 
        <tr class="<?=getRowColor($i)?>">
            <td class="content_row"><?=$_LANG->get('Istzeit in Std.')?></td>
            <?
                foreach ($machs as $val)
                {
                    if(count($val))
                        foreach($val as $key2 => $sm)
                        {
                            echo '<td class="content_row">'.$sm->getActualTime().'</td>';
                        }
                    else
                        echo '<td class="content_row">&nbsp;</td>';
                } 
                $i++;
            ?>
        </tr> 
        <? } ?>
        <tr class="<?=getRowColor($i)?>">
            <td class="content_row"><?=$_LANG->get('Termin')?></td>
            <?
                foreach ($machs as $val)
                {
                    if(count($val))
                        foreach($val as $key2 => $sm)
                        {
                            echo '<td class="content_row">'.date('d.m.Y', $sm->getDeadline()).'</td>';
                        }
                    else
                        echo '<td class="content_row">&nbsp;</td>';
                } 
                $i++;
            ?>
        </tr> 
        <tr class="<?=getRowColor($i)?>">
            <td class="content_row"><?=$_LANG->get('Auflage')?></td>
            <?
                foreach ($machs as $val)
                {
                    if(count($val))
                        foreach($val as $key2 => $sm)
                        {
                            echo '<td class="content_row">'.$sm->getAmount().'</td>';
                        }
                    else
                        echo '<td class="content_row">&nbsp;</td>';
                } 
                $i++;
            ?>
        </tr> 
        <tr class="<?=getRowColor($i)?>">
            <td class="content_row" valign="top"><?=$_LANG->get('Bemerkungen')?></td>
            <?
                foreach ($machs as $val)
                {
                    if(count($val))
                        foreach($val as $key2 => $sm)
                        {
                            echo '<td class="content_row" valign="top">'.$sm->getNotes().'&nbsp;</td>';
                        }
                    else
                        echo '<td class="content_row">&nbsp;</td>';
                } 
                $i++;
            ?>
        </tr> 
        
        <tr class="<?=getRowColor($i)?>" >
            <td class="content_row"><?=$_LANG->get('Optionen')?></td>
            <td class="content_row" colspan="<?=($cols-1)?>">
            <? if($_USER->hasRightsByGroup(Group::RIGHT_PARTS_EDIT)) { ?>
                <a href="index.php?page=<?=$_REQUEST['page']?>&exec=editparts&id=<?=$sched->getId()?>&part_id=<?=$part->getId()?>"><span class="glyphicons glyphicons-pencil"></span></a>
	            <? if($_USER->hasRightsByGroup(Group::RIGHT_DELETE_SCHEDULE) || $_USER->isAdmin()){ ?>
                    <span class="glyphicons glyphicons-remove icon-link"
                    onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=parts&id=<?=$sched->getId()?>&subexec=deletepart&part_id=<?=$part->getId()?>')"></span>
	            <?}?>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <?=$_LANG->get('Teilauftrag freigegeben')?>:
                <a href="javascript: saveStats('jobpartfinish','<?=$part->getId()?>','0','idx_img_jobpartstat_<?=$i?>','red')"
                ><img id="idx_img_jobpartstat_<?=$i?>_0" class="select" src="./images/status/<?
                     if((int)$part->getFinished() == 0) 
                         echo "red";
                     else
                         echo "black"; 
                     ?>.gif" alt="Teilauftrags-Status: Rot"></a>
                <a href="javascript: saveStats('jobpartfinish','<?=$part->getId()?>','1','idx_img_jobpartstat_<?=$i?>','green')"
                ><img id="idx_img_jobpartstat_<?=$i?>_1" class="select" src="./images/status/<?
                     if((int)$part->getFinished() == 1)
                         echo "green";
                     else
                         echo "black";
                     ?>.gif" alt="Teilauftrags-Status: Gr&uuml;n">
                </a>
                <? } ?>            
            </td>
        </tr> 
    </table>        
    <? 
    $ta++;
}
?>
</div>
<br>
<div class="box1">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr>
   <td align="right" width="130">
      <ul class="postnav">
         <a href="index.php?page=<?=$_REQUEST['page']?>"><?=$_LANG->get('Zur&uuml;ck')?></a>
      </ul>
   </td>
   <td>&nbsp;</td>
</tr>
</table>
</div>