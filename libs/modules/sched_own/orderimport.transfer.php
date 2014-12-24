<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       11.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

$_REQUEST["order_number"] = trim(addslashes($_REQUEST["order_number"]));

// Alten Auftrag löschen?
if($_REQUEST["lector_overwrite"] == "new")
{
    $scheds = Schedule::searchByJobNumber(trim(addslashes($_REQUEST["order_number"])));
    foreach ($scheds as $sched)
    {
        foreach(SchedulePart::getAllScheduleParts($sched->getId()) as $part)
        {
            foreach(ScheduleMachine::getAllScheduleMachines($part->getId()) as $mach)
                $mach->delete();

            $part->delete();
        }
        $sched->delete(false);
    }

    unset($scheds);
}

if (count(Schedule::searchByJobNumber(trim(addslashes($_REQUEST["order_number"])))))
{?>
<table border="0" cellpadding="3" cellspacing="0" width="100%">
	<tr class="<?=getRowColor(1)?>">
		<td class="content_row" width="100" valign="top"><b>Druckplan</b></td>
		<td class="content_row"><?=$_LANG->get('Der Auftrag')?> <b style='color: #912f4e'><?=$_REQUEST["order_number"]?>
		</b> <?=$_LANG->get('ist bereits in Druckplan vorhanden.')?> <br> <br> <font
			style='color: #912f4e'><?=$_LANG->get('Soll der alte Auftrag gel&ouml;scht und neu erstellt werden?')?></font> <br> <br>
			<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="rewritelector_form" id="rewritelector_form">
				<input type="hidden" name="mid" value="<?=$_REQUEST["mid"]?>"> 
				<input type="hidden" name="exec" value="transfer"> 
				<input type="hidden" name="order_number" value="<?=$_REQUEST["order_number"]?>">
				<input type="hidden" name="lector_overwrite" value="new">
				<table border="0" cellpadding="0" cellspacing="0" width="360">
					<tr>
						<td width="160">
							<ul class="postnav_del">
								<a href="#" onclick="document.getElementById('rewritelector_form').submit()"><?=$_LANG->get('L&ouml;schen')?></a>
							</ul>
						</td>
						<td>&nbsp;</td>
						<td width="160">
							<ul class="postnav">
								<a href="index.php"><?=$_LANG->get('Zur&uuml;ck')?></a>
							</ul>
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
</table>
<? } else { ?>

    <div class="box1">
    <table border="0" cellspacing="0" cellpadding="0" width="100%"> 
    <tr>
       <td>&nbsp;</td>
       <td align="right" width="300">
          <ul class="postnav_save">
             <a id="idx_headerlink" href="#" style="padding:17px 60px"><?=$_LANG->get('Auftrag aufrufen')?></a>
          </ul>
       </td>
    </tr>
    </table>
    </div>
    <br>
    
    <table border="0" cellpadding="3" cellspacing="0" width="100%">
    <tr class="<?=getRowColor(0)?>">
    <td class="content_row" width="100"><b><?=$_LANG->get('Kalkulation')?></b></td>
    <td class="content_row"><?=$_LANG->get('Ermittle Projektdaten')?>...</td>
    </tr>
    </table>
    <?
    flush(); ob_flush();
    
    $orders = Order::searchByNumber($_REQUEST["order_number"]);
    
    if(count($orders) && $orders != false)
        $dsp_count = count($orders);
    else
        $dsp_count = 0;
    
    ?>
    <table border="0" cellpadding="3" cellspacing="0" width="100%">
    <tr class="<?=getRowColor(0)?>">
    <td class="content_row" width="100"><b><?=$_LANG->get('Kalkulation')?></b></td>
    <td class="content_row"><?=$_LANG->get('Gefundene Datens&auml;tze')?>: <b style="color:green"><?=$dsp_count?></td>
    </tr>
    </table>
    <?
    flush(); ob_flush();
    
    //-------------------------------------------------------------------
    // Auftrag importieren
    //-------------------------------------------------------------------
    $y = 0;
    if($dsp_count > 0)
    {  $y++;?>
        <br>
        <table border="0" cellpadding="3" cellspacing="0" width="100%">
        <tr class="<?=getRowColor(0)?>">
        <td class="content_row" width="100"><b><?=$_LANG->get('Kalkulation')?></b></td>
        <td class="content_row"><?=$_LANG->get('Transfer')?>: <?=$_LANG->get('Auftrag')?> #<?=$y?>...</td>
        </tr>
        </table>
        <?
        flush(); ob_flush();
        foreach($orders as $ord)
        {
            if($ord->getDeliveryDate())
                $delivdate = $ord->getDeliveryDate();
            else 
            {
                $delivdate = mktime(0,0,0, date('m'), date('d'), date('Y'));
                ?>
                <table border="0" cellpadding="3" cellspacing="0" width="100%">
                <tr class="<?=getRowColor(0)?>">
                <td class="content_row" width="100"><b><?=$_LANG->get('Kalkulation')?></b></td>
                <td class="content_row"><span class="error"><?=$_LANG->get('Transfer')?>: <?=$_LANG->get('Liefertermin auf heutiges Datum gesetzt')?> ...</span></td>
                </tr>
                </table>
                <? 
            }
            
            
            if($ord->getDeliveryAddress()->getId())
                $delivaddr = $ord->getDeliveryAddress()->getAddressAsLine();
            else 
                $delivaddr = $ord->getCustomer()->getAddressAsLine();
            
            $sched = new Schedule();
            $sched->setNumber($ord->getNumber());
            $sched->setCustomer($ord->getCustomer());
            $sched->setObject($ord->getTitle());
            $sched->setDeliveryLocation($delivaddr);
            $sched->setDeliveryterms($ord->getDeliveryTerms());
            $sched->setDeliveryDate($delivdate);
            $sched->setCreateuser($_USER->getFirstname()." ".$_USER->getLastname());
            $sched->setDruckplanId($ord->getId());
            $sched->setNotes($ord->getNotes());
            $sched->save();
            echo $DB->getLastError();
            
            foreach(Calculation::getAllCalculations($ord) as $calc)
            {
                if($calc->getState() > 0)
                {
                    //-------------------------------------------------------------------
                    // Teilaufträge anlegen
                    //-------------------------------------------------------------------
                    $schedPart = new SchedulePart();
                    $schedPart->setFinished(0);
                    $schedPart->setDruckplanId($calc->getId());
                    $schedPart->setScheduleId($sched->getId());
                    $schedPart->save();
                    echo $DB->getLastError();
                    
                    $amount = $sched->getAmount() + $calc->getAmount();
                    $sched->setAmount($amount);
                    $sched->save();
                    
                    //-------------------------------------------------------------------
                    // Maschinen zuteilen
                    //-------------------------------------------------------------------
                    foreach(Machineentry::getAllMachineentries($calc->getId()) as $me)
                    {
                        $bemerkungen = "";
                        if($me->getPart() == Calculation::PAPER_CONTENT)
                            $bemerkungen .= "Inhalt";
                        if($me->getPart() == Calculation::PAPER_ADDCONTENT)
                            $bemerkungen .= "zus. Inhalt"; 
                        if($me->getPart() == Calculation::PAPER_ENVELOPE)
                            $bemerkungen .= "Umschlag";
							
                        if($me->getInfo()) // modified by ascherer 22.09.14 to show entry info from order step 3 in schedueles
                            $bemerkungen .= " / " . $me->getInfo();
                        
                        $colors = "";
                        if($me->getPart() == Calculation::PAPER_CONTENT)
                            $colors .= $calc->getChromaticitiesContent()->getName();
                        if($me->getPart() == Calculation::PAPER_ADDCONTENT)
                            $colors .= $calc->getChromaticitiesAddContent()->getName();
                        if($me->getPart() == Calculation::PAPER_ENVELOPE)
                            $colors .= $calc->getChromaticitiesEnvelope()->getName();
                        
                        $schedMach = new ScheduleMachine();
                        $schedMach->setMachine($me->getMachine());
                        $schedMach->setMachineGroup($me->getMachine()->getGroup()->getId());
                        $schedMach->setTargetTime($me->getTime() / 60);
                        $schedMach->setDeadline($delivdate);
                        $schedMach->setSchedulePartId($schedPart->getId());
                        $schedMach->setNotes($bemerkungen);
                        $schedMach->setAmount($calc->getAmount());
                        $schedMach->setColors($colors);
                        if($me->getFinishing()->getId())
                            $schedMach->setFinishing($me->getFinishing()->getBeschreibung());
                        $schedMach->save();
                        echo $DB->getLastError();
                        

                    }
                }
            }
        }
    } else {
        ?>
        <table border="0" cellpadding="3" cellspacing="0" width="100%">
        <tr class="<?=getRowColor(0)?>">
        <td class="content_row" width="100"><b><?=$_LANG->get('Kalkulation')?></b></td>
        <td class="content_row"><b style="color:#912f4e"><?=$_LANG->get('Keine Daten f&uuml;r Transfer gefunden.')?></b></td>
        </tr>
        </table>
        <?
    }
    
    ?>
    <table border="0" cellpadding="3" cellspacing="0" width="100%">
    <tr class="<?=getRowColor(1)?>">
    <td class="content_row" width="100"><b>Druckplan</b></td>
    <td class="content_row"><b style="color:green"><?=$_LANG->get('Transfer abgeschlossen.')?></b></td>
    </tr>
    </table>
    <?
    
 }
 
 
if($sched != null && (int)$sched->getId())
{  ?>
    <br>
    <div class="box1">
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td>&nbsp;</td>
        <td align="right" width="300">
            <ul class="postnav_save">
                <a href="index.php?page=libs/modules/schedule/schedule.php&exec=parts&id=<?=$sched->getId()?>" style="padding:17px 60px"><?=$_LANG->get('Auftrag aufrufen')?></a>
            </ul>
        </td>
    </tr>
    </table>
    </div>
    <br>
    <script language="JavaScript">
        document.getElementById('idx_headerlink').href = 'index.php?page=libs/modules/schedule/schedule.php&exec=parts&id=<?=$sched->getId()?>';
        //document.all.idx_headertable.style.display = '';
    </script>
    <?
}