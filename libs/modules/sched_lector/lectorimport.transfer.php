<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       10.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

// Alten Auftrag l�schen?
if($_REQUEST["lector_overwrite"] == "new")
{
    $scheds = Schedule::searchByJobNumber(trim(addslashes($_REQUEST["lector_jobnumber"])));
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

if (count(Schedule::searchByJobNumber(trim(addslashes($_REQUEST["lector_jobnumber"])))))
{?>
<table border="0" cellpadding="3" cellspacing="0" width="100%">
	<tr class="<?=getRowColor(1)?>">
		<td class="content_row" width="100" valign="top"><b>Druckplan</b></td>
		<td class="content_row"><?=$_LANG->get('Der Auftrag')?> <b style='color: red'><?=$_REQUEST["lector_jobnumber"]?>
		</b> <?=$_LANG->get('ist bereits in Druckplan vorhanden.')?> <br> <br> <font
			style='color: red'><?=$_LANG->get('Soll der alte Auftrag gel&ouml;scht und neu erstellt werden?')?></font> <br> <br>
			<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="rewritelector_form" id="rewritelector_form">
				<input type="hidden" name="mid" value="<?=$_REQUEST["mid"]?>"> 
				<input type="hidden" name="exec" value="transfer"> 
				<input type="hidden" name="lector_jobnumber" value="<?=$_REQUEST["lector_jobnumber"]?>">
				<input type="hidden" name="lector_overwrite" value="new">
				<table border="0" cellpadding="0" cellspacing="0" width="360">
					<tr>
						<td width="160">
							<ul class="postnav_del">
								<a href="#" onclick="document.getElementById('rewritelector_form').submit()"><?=$_LANG->get('L&ouml;schen & neu erstellen')?></a>
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
<? 
} else { 
    
    ?>
    <div class="box1">
    <table border="0" cellspacing="0" cellpadding="0" width="100%"> 
    <tr>
       <td>&nbsp;</td>
       <td align="right" width="130">
          <ul class="postnav_save">
             <a id="idx_headerlink" href="#"><?=$_LANG->get('Auftrag aufrufen')?></a>
          </ul>
       </td>
    </tr>
    </table>
    </div>
    <br>
    
    <table border="0" cellpadding="3" cellspacing="0" width="100%">
    <tr class="<?=getRowColor(0)?>">
    <td class="content_row" width="100"><b>Lector</b></td>
    <td class="content_row"><?=$_LANG->get('Ermittle Projektdaten')?>...</td>
    </tr>
    </table>
    <?
    flush(); ob_flush();
    
    $vgs = $import->searchJobs(trim(addslashes($_REQUEST["lector_jobnumber"])));

    if(count($vgs) && $vgs != false)
        $dsp_count = count($vgs);
    else
        $dsp_count = 0;
    
    ?>
    <table border="0" cellpadding="3" cellspacing="0" width="100%">
    <tr class="<?=getRowColor(0)?>">
    <td class="content_row" width="100"><b>Lector</b></td>
    <td class="content_row"><?=$_LANG->get('Gefundene Datens&auml;tze')?>: <b style="color:green"><?=$dsp_count?></td>
    </tr>
    </table>
    <?
    flush(); ob_flush();

    //------------------------------------------------------------------------
    // Daten aus Lector importieren / updaten
    //------------------------------------------------------------------------
    
    for($x = 0; $x < count($vgs) && $vgs != false; $x++)
    {
        ?>
        <table border="0" cellpadding="3" cellspacing="0" width="100%">
        <tr class="<?=getRowColor(0)?>">
        <td class="content_row" width="100"><b>Lector</b></td>
        <td class="content_row"><?=$_LANG->get('Transfer')?>: <?=$_LANG->get('Kundenstammdaten')?>...</td>
        </tr>
        </table>
        <?
        flush(); ob_flush();
        
        //----------------------------------------------------------------------------------
        // Kundendatensaetze
        //----------------------------------------------------------------------------------
        $temp                      = $vgs[$x]["CITY"];
        $vgs[$x]["POSTCODE"]       = trim(substr($temp, 0, strpos($temp, " ")));
        $vgs[$x]["CITY"]           = trim(substr($temp, strpos($temp, " ")));
         
        $idxkey = array_keys($vgs[$x]);
        for($y = 0; $y < count($idxkey); $y++)
            $vgs[$x][$idxkey[$y]] = $vgs[$x][$idxkey[$y]];
      
        
        $cust = BusinessContact::searchByLectorId($vgs[$x]["CUST_LECTOR_ID"], BusinessContact::ORDER_ID, BusinessContact::FILTER_CUST_IST);

        $vgs[$x]["COMPANY"]        = trim(str_replace("'","", utf8_encode($vgs[$x]["COMPANY"])));
        $vgs[$x]["CONTACTPERSON"]  = str_replace("'","", $vgs[$x]["CONTACTPERSON"]);
        $vgs[$x]["STREET"]         = utf8_encode(str_replace("'","", $vgs[$x]["STREET"]));
        $vgs[$x]["CITY"]           = utf8_encode(str_replace("'","", $vgs[$x]["CITY"]));
        $vgs[$x]["PHONE"]          = str_replace("'","", $vgs[$x]["PHONE"]);
        $vgs[$x]["FAX"]            = str_replace("'","", $vgs[$x]["FAX"]);
        $vgs[$x]["EMAIL"]          = str_replace("'","", $vgs[$x]["EMAIL"]);
        $vgs[$x]["WEBSITE"]        = str_replace("'","", $vgs[$x]["WEBSITE"]);
        $vgs[$x]["SACHBEARBEITER"] = str_replace("'","", utf8_encode($vgs[$x]["SACHBEARBEITER"]));
        $vgs[$x]["NOTES"]          = str_replace("'","", utf8_encode($vgs[$x]["NOTES"]));
        $vgs[$x]["AUFTRAGSDATUM"]  = explode("-", $vgs[$x]["AUFTRAGSDATUM"]);
        $vgs[$x]["AUFTRAGSDATUM"]  = mktime(0, 0, 0, $vgs[$x]["AUFTRAGSDATUM"][1],
                                                     $vgs[$x]["AUFTRAGSDATUM"][2],
                                                     $vgs[$x]["AUFTRAGSDATUM"][0]);

        if($vgs[$x]["COMPANY"] != "")
        {
            if(trim($vgs[$x]["CONTACTPERSON"]) != "")
                $vgs[$x]["COMPANY"] .= " ".$vgs[$x]["CONTACTPERSON"];
        }
        else
            $vgs[$x]["COMPANY"] = $vgs[$x]["CONTACTPERSON"];
        
        //----------------------------------------------------------------------------------
        // Kunde ist vorhanden
        //----------------------------------------------------------------------------------
        if(count($cust))
            $cust = $cust[0];
        else 
        {
            $cust = new BusinessContact();
            $cust->setCustomer(1);
            $cust->setClient($_USER->getClient());
            $cust->setActive(1);
        }
            
        $cust->setName1($vgs[$x]["COMPANY"]);
        $cust->setAddress1($vgs[$x]["STREET"]);
        $cust->setZip($vgs[$x]["POSTCODE"]);
        $cust->setCity($vgs[$x]["CITY"]);
        $cust->setPhone($vgs[$x]["PHONE"]);
        $cust->setFax($vgs[$x]["FAX"]);
        $cust->setEmail($vgs[$x]["EMAIL"]);
        $cust->setWeb($vgs[$x]["WEBSITE"]);
        $cust->setComment($vgs[$x]["NOTES"]);
        $cust->setLectorId($vgs[$x]["CUST_LECTOR_ID"]);
        $cust->save();
        
        if($cust->getId())
        {
            ?>
            <table border="0" cellpadding="3" cellspacing="0" width="100%">
            <tr class="<?=getRowColor(0)?>">
            <td class="content_row" width="100"><b>Lector</b></td>
            <td class="content_row"><?=$_LANG->get('Transfer')?>: <?=$_LANG->get('Auftragskopfdaten')?>...</td>
            </tr>
            </table>
            <?
            flush(); ob_flush();
            
            $scheds = $import->getJobs($vgs[$x]["VG_ID"]);
            
            for($y = 0; $y < count($scheds) && $scheds != false; $y++)
            {
                ?>
                <br>
                <table border="0" cellpadding="3" cellspacing="0" width="100%">
                <tr class="<?=getRowColor(0)?>">
                <td class="content_row" width="100"><b>Lector</b></td>
                <td class="content_row"><?=$_LANG->get('Transfer')?>: <?=$_LANG->get('Auftrag')?> #<?=($y +1)?>...</td>
                </tr>
                </table>
                <?
                flush(); ob_flush();
                if(trim($vgs[$x]["DELIVERY_DATE"]) != "")
                {
                    $importcounter++;
                    
                    $vgs[$x]["DELIVERY_DATE"]  = explode("-", $vgs[$x]["DELIVERY_DATE"]);
                    $vgs[$x]["DELIVERY_DATE"]  = mktime(0, 0, 0, $vgs[$x]["DELIVERY_DATE"][1],
                                                     $vgs[$x]["DELIVERY_DATE"][2],
                                                     $vgs[$x]["DELIVERY_DATE"][0]);
                    
                    $sched = new Schedule();
                    $sched->setNumber($vgs[$x]["VG_LECTOR_ID"]);
                    $sched->setCustomer($cust);
                    $sched->setObject(utf8_encode($scheds[$y]["OBJECT"]));
                    $sched->setAmount($scheds[$y]["AMOUNT"]);
                    $sched->setColors($scheds[$y]["COLOURS"]);
                    $sched->setDeliveryLocation(utf8_encode($scheds[$y]["DELIVERY"]));
                    $sched->setDeliveryDate($vgs[$x]["DELIVERY_DATE"]);
                    $sched->setCreateuser($vgs[$x]["CREATEUSER"]);
                    $sched->setLectorId($scheds[$y]["LECTOR_JOB_ID"]);
                    $sched->save();
                    echo $DB->getLastError();
                    
                    ?>
                    <table border="0" cellpadding="3" cellspacing="0" width="100%">
                    <tr class="<?=getRowColor(0)?>">
                    <td class="content_row" width="100"><b>Lector</b></td>
                    <td class="content_row"><?=$_LANG->get('Ermittle Teilauftr&auml;ge')?>...</td>
                    </tr>
                    </table>
                    <table border="0" cellpadding="3" cellspacing="0" width="100%">
                    <tr class="<?=getRowColor(0)?>">
                    <td class="content_row" width="100"><b>Lector</b></td>
                    <td class="content_row"><?=$_LANG->get('Maschinen und Gruppen transferieren')?>...</td>
                    </tr>
                    </table>
                    <?
                    flush(); ob_flush();
                    
                    $parts = $import->getParts($vgs[$x]["VG_ID"], $scheds[$y]["PRODUKTID"]);
                    $last_objectid = -99;
                    $ctp_insert    = true;
                    
                    for($z = 0; $z < count($parts) && $parts != false; $z++)
                    {
                        //----------------------------------------------------------------------------------
                        // Maschinengruppen transferieren
                        //----------------------------------------------------------------------------------
                        $machGroup = MachineGroup::getMachineGroupByLectorId($parts[$z]["PHASEID"]);
                        $machGroup->setName($parts[$z]["PHASEDESC"]);
                        $machGroup->setPosition($parts[$z]["PHASEPRIO"]);
                        $machGroup->setLectorId($parts[$z]["PHASEID"]);
                        $machGroup->save();
                        
                        //----------------------------------------------------------------------------------
                        // Maschinen transferieren
                        //----------------------------------------------------------------------------------
                        $mach = Machine::getMachineByLectorId($parts[$z]["KSTID"]);
                        $mach->setState(1);
                        $mach->setName($parts[$z]["KSTDESC"]);
                        $mach->setGroup($machGroup);
                        $mach->setLectorId($parts[$z]["KSTID"]);
                        $mach->save();
                        
                        //----------------------------------------------------------------------------------
                        // Teilauftraege transferieren
                        //----------------------------------------------------------------------------------
                        
                        if($parts[$z]["OBJECTID"] != $last_objectid)
                        {
                            $last_objectid = $parts[$z]["OBJECTID"];
                        
                            ?>
                            <table border="0" cellpadding="3" cellspacing="0" width="100%">
                            <tr class="<?=getRowColor(1)?>">
                            <td class="content_row" width="100"><b>Druckplan</b></td>
                            <td class="content_row"><?=$_LANG->get('Import')?> <?=$_LANG->get('Teilauftrag')?>: <?=$last_objectid?>...</td>
                            </tr>
                            </table>
                            <?
                            flush(); ob_flush();
                            
                            $part = new SchedulePart();
                            $part->setFinished(0);
                            $part->setScheduleId($sched->getId());
                            $part->setLectorId($last_objectid);
                            $part->save();
                        }
                        
                        //----------------------------------------------------------------------------------
                        // Maschinenzeiten transferieren
                        //----------------------------------------------------------------------------------
                        $parts[$z]["ZEIT"] = $parts[$z]["ZEIT"] / 60;
    
                        if(strpos(strtoupper($parts[$z]["JOBDESC"]), "COMPUTER TO PLATE") !== false && $ctp_insert)
                        {
                            $ctp_insert = false;
                        
                            $schedMach = new ScheduleMachine();
                            $schedMach->setSchedulePartId($part->getId());
                            $schedMach->setMachineGroup($machGroup->getId());
                            $schedMach->setMachine($mach);
                            $schedMach->setTargetTime(1);
                            $schedMach->setDeadline($vgs[$x]["DELIVERY_DATE"]);
                            $schedMach->setNotes("Standardzeit - {$parts[$z]["JOBDESC"]}");
                            $schedMach->setLectorId($parts[$z]["JOBID"]);
                            $schedMach->save();
                        }
                        
                        if(strpos(strtoupper($parts[$z]["JOBDESC"]), "COMPUTER TO PLATE") === false)
                        {
    
                            // Umschreiben Digitaldruck Klick + Std.Satz- Preis -> Schwarz wei� Digitaldruck Klick + Std.Satz- Preis
                            if(strpos($scheds[$y]["COLOURS"], "1/1") !== false && $mach->getName() == "Digitaldruck Klick + Std.Satz- Preis")
                                $mach = Machine::getMachineByName("Schwarz wei� Digitaldruck  Klick + Std.Satz- Preis");
                            
                            // Umschreiben SM 102-4 -> SM 102-5 falls Lack
                            if(strpos($scheds[$y]["COLOURS"], "4/4") !== false && strpos(strtolower($scheds[$y]["COLOURS"]), "lack") !== false && $mach->getName() == "SM 102-4")
                                $mach = Machine::getMachineByName("SM 102-5");
                            
                            $schedMach = new ScheduleMachine();
                            $schedMach->setSchedulePartId($part->getId());
                            $schedMach->setMachineGroup($machGroup->getId());
                            $schedMach->setMachine($mach);
                            $schedMach->setTargetTime($parts[$z]["ZEIT"]);
                            $schedMach->setDeadline($vgs[$x]["DELIVERY_DATE"]);
                            $schedMach->setNotes("{$parts[$z]["OBJECTDESC"]} - {$parts[$z]["JOBDESC"]}");
                            $schedMach->setLectorId($parts[$z]["JOBID"]);
                            $schedMach->save();
                        }
                    }
                
                    //----------------------------------------------------------------------------------
                    // Teilauftr�ge zusammenf�hren
                    //----------------------------------------------------------------------------------
    
                    // TODO Teilauftraege zusammenfuehren
                }
                else
                {  ?>
                    <table border="0" cellpadding="3" cellspacing="0" width="100%">
                    <tr class="<?=getRowColor(1)?>">
                    <td class="content_row" width="100"><b>Druckplan</b></td>
                    <td class="content_row"><font style="color:red"><?=$_LANG->get('Kein Auslieferungsdatum')?> #<?=($y +1)?>...</font></td>
                    </tr>
                    </table>
                    <?
                    flush(); ob_flush();
                }             
            }
        }
    }

    if(!(int)$importcounter)
    {  ?>
        <table border="0" cellpadding="3" cellspacing="0" width="100%">
        <tr class="<?=getRowColor(0)?>">
        <td class="content_row" width="100"><b>Lector</b></td>
        <td class="content_row"><b style="color:red"><?=$_LANG->get('Keine Daten f&uuml;r Transfer gefunden.')?></b></td>
        </tr>
        </table>
    <?
    }
    else
    {  ?>
        <table border="0" cellpadding="3" cellspacing="0" width="100%">
        <tr class="<?=getRowColor(1)?>">
        <td class="content_row" width="100"><b>Druckplan</b></td>
        <td class="content_row"><b style="color:green"><?=$_LANG->get('Transfer erfolgreich abgeschlossen.')?></b></td>
        </tr>
        </table>
    <?
    }
} 


if($sched != null && (int)$sched->getId())
{  ?>
    <br>
    <div class="box1">
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td>&nbsp;</td>
        <td align="right" width="130">
            <ul class="postnav_save">
                <a href="index.php?page=libs/modules/schedule/schedule.php&exec=parts&id=<?=$sched->getId()?>"><?=$_LANG->get('Auftrag aufrufen')?></a>
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