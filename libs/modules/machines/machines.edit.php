<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       14.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$machine = new Machine($_REQUEST["id"]);

// Falls kopieren, ID loeschen -> Maschine wird neu angelegt
if($_REQUEST["exec"] == "copy")
    $machine->clearId();

if($_REQUEST["subexec"] == "save")
{
    $chromas = Array();
    $clicks = Array(); //gln
    $units = Array();
    $difficulties = Array();
    foreach (array_keys($_REQUEST) as $key)
    {
        if (preg_match("/chroma_(?P<id>\d+)/", $key, $m))
		{ 
            $chromas[] = new Chromaticity($m["id"]);
            //gln
            if ((int)$_REQUEST["machine_type"] == Machine::TYPE_DRUCKMASCHINE_DIGITAL && (int)$_REQUEST["machine_pricebase"] == Machine::PRICE_MINUTE)
   				$clicks[] = (float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["click_{$m["id"]}"])));
   			else
   				$clicks[] = (float)0;
			//echo "REQUEST ".$_REQUEST["click_{$m["id"]}"]." "."click_{$m["id"]}";
			//echo "matype ".$_REQUEST["machine_type"]." ".$_REQUEST["machine_pricebase"];
			//echo "clicks".$clicks[$i]."";$i++;
		}
    }

    foreach (array_keys($_REQUEST) as $key)
    {
        if (preg_match("/units_from_(?P<id>\d+)/", $key, $m))
        {
            if((int)str_replace('.', "", $_REQUEST["units_per_hour_{$m["id"]}"]) > 0)
            {
                $units["{$m["id"]}"]["from"] = (int)str_replace('.', "", $_REQUEST["units_from_{$m["id"]}"]);
                $units["{$m["id"]}"]["per_hour"] = (int)str_replace('.', "", $_REQUEST["units_per_hour_{$m["id"]}"]);
            }
        }
    }
    

    foreach ($_REQUEST["machine_difficulty"] as $tmp_req_difficulty){
        if ($tmp_req_difficulty["unit"] != 0 && count($tmp_req_difficulty["values"]) > 0){
            $difficulties[$tmp_req_difficulty["id"]]["id"] = $tmp_req_difficulty["id"];
            $difficulties[$tmp_req_difficulty["id"]]["unit"] = $tmp_req_difficulty["unit"];
            $difficulties[$tmp_req_difficulty["id"]]["values"] = $tmp_req_difficulty["values"];
            $difficulties[$tmp_req_difficulty["id"]]["percents"] = $tmp_req_difficulty["percents"];
        }
    }
    
    
    $machine->setName(trim(addslashes($_REQUEST["machine_name"])));
    $machine->setDocumentText(trim(addslashes($_REQUEST["machine_documenttext"])));
    $machine->setGroup(new MachineGroup((int)$_REQUEST["machine_group"]));
    $machine->setType((int)$_REQUEST["machine_type"]);
    $machine->setPriceBase((int)$_REQUEST["machine_pricebase"]);
    $machine->setPrice((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["machine_price"]))));
    $machine->setBorder_bottom((int)$_REQUEST["border_bottom"]);
    $machine->setBorder_left((int)$_REQUEST["border_left"]);
    $machine->setBorder_right((int)$_REQUEST["border_right"]);
    $machine->setBorder_top((int)$_REQUEST["border_top"]);
    $machine->setColors_front((int)$_REQUEST["colors_front"]);
    $machine->setColors_back((int)$_REQUEST["colors_back"]);
    $machine->setTimeColorchange((int)$_REQUEST["time_colorchange"]);
    $machine->setTimePlatechange((int)$_REQUEST["time_platechange"]);
    $machine->setTimeBase((int)$_REQUEST["time_base"]);
    $machine->setAllUnitsPerHour($units);
    $machine->setUnit((int)$_REQUEST["unit"]);
    $machine->setChromaticities($chromas);
    $machine->setClickprices($clicks);		//gln
    $machine->setFinish((int)$_REQUEST["finish"]);
    $machine->setFinishPlateCost((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["finish_plate_cost"]))));
    $machine->setUmschlUmst((int)$_REQUEST["umschl_umst"]);		//gln
    $machine->setMaxHours((int)$_REQUEST["max_hours"]);
    $machine->setPaperSizeHeight((int)$_REQUEST["paper_size_height"]);
    $machine->setPaperSizeWidth((int)$_REQUEST["paper_size_width"]);
    $machine->setPaperSizeMinHeight((int)$_REQUEST["paper_size_min_height"]);
    $machine->setPaperSizeMinWidth((int)$_REQUEST["paper_size_min_width"]);
    $machine->setDifficulties($difficulties);
    $machine->setAnzStations((int)$_REQUEST["anz_stations"]);
    $machine->setTimeSetupStations((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["time_setup_stations"]))));
    $machine->setPagesPerStation((int)$_REQUEST["pages_per_station"]);
    $machine->setAnzSignatures((int)$_REQUEST["anz_signatures"]);
    $machine->setTimeSignatures((int)$_REQUEST["time_signatures"]);
    $machine->setTimeEnvelope((int)$_REQUEST["time_envelope"]);
    $machine->setTimeTrimmer((int)$_REQUEST["time_trimmer"]);
    $machine->setTimeStacker((int)$_REQUEST["time_stacker"]);
    $machine->setCutPrice((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["cut_price"]))));
   
    $machine->setInternalText($_REQUEST["machine_internaltext"]);
    $machine->setHersteller($_REQUEST["machine_hersteller"]);
    $machine->setBaujahr($_REQUEST["machine_baujahr"]);

    $machine->setDPHeight((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["machine_DPHeight"]))));
    $machine->setDPWidth((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["machine_DPWidth"]))));
    
    if ($machine->getId() == 0 &&  											// Wenn Maschine noch nicht existiert und eine Druckmaschine ist,
    		$machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET){		// muss geschaut werden, ob verfuegbare Anzahl erreicht ist
    	$printer_counter = Machine::getNumberOfPrintingmachines();
    	if($printer_counter < $_CONFIG->NumberOfPrintingmachines && $printer_counter =! false){
    		$savemsg = $printer_counter." - ".getSaveMessage($machine->save()).$DB->getLastError();
    	} else {
    		$savemsg = '<span class="error">'.$_LANG->get('Anzahl verf&uuml;gbarer Druckmaschinen erreicht').'</span>';
    	}
    } else { // andernfalls darf die Maschine angelegt werden
    	$savemsg = getSaveMessage($machine->save()).$DB->getLastError();
    }
    
    /***
     ($machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET || 	// und eine Druckmaschine ist,
    		$machine->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL) ){	// muss geschaut werden, ob verfuegbare Anzahl erreicht ist
     */
}
?>
<script language="javascript">

<?/*gln*/?>
function showHideClpr(val)
{
	var i=0;
	var maxanz=document.getElementsByName('anz_click1').length;
	if(val == <?=Machine::PRICE_MINUTE?> )
	{
		for (i=0; i<maxanz; i++)
		{
			document.getElementsByName('anz_click1')[i].style.display = '';
			document.getElementsByName('anz_click2')[i].style.display = '';
		}
	}
	else
	{
		for (i=0; i<maxanz; i++)
		{
			document.getElementsByName('anz_click1')[i].style.display = 'none';
			document.getElementsByName('anz_click2')[i].style.display = 'none';
		}
	}
}	
function showHide(val)
{
	if(val == <?=Machine::TYPE_DRUCKMASCHINE_OFFSET?>)
	{
		document.getElementById('tr_machine_paper_size').style.display = '';
		document.getElementById('tr_machine_paper_min_size').style.display = '';
	    document.getElementById('tr_machine_border').style.display = '';
	    document.getElementById('tr_machine_colors_front').style.display = '';
	    document.getElementById('tr_machine_colors_back').style.display = '';
	    document.getElementById('tr_machine_timeplatechange').style.display = '';
	    document.getElementById('tr_machine_timecolorchange').style.display = '';
	    document.getElementById('tr_machine_paperperhour').style.display = '';
	    document.getElementById('tr_machine_chromaticity').style.display = '';
	    document.getElementById('tr_machine_finish').style.display = '';
	    document.getElementById('tr_machine_umschl_umst').style.display = '';	//gln
	    document.getElementById('tr_machine_setup_stations').style.display = 'none';
	    document.getElementById('tr_machine_anz_stations').style.display = 'none';
	    document.getElementById('tr_machine_pages_per_station').style.display = 'none';
	    document.getElementById('tr_machine_anz_signatures').style.display = 'none';
	    document.getElementById('tr_machine_time_signatures').style.display = 'none';
	    document.getElementById('tr_machine_time_envelope').style.display = 'none';
	    document.getElementById('tr_machine_time_trimmer').style.display = 'none';
	    document.getElementById('tr_machine_time_stacker').style.display = 'none';
	    
	    showHideClpr(0); <?/*gln*/?>
	    
	} else if (val == <?=Machine::TYPE_DRUCKMASCHINE_DIGITAL?>) {
		document.getElementById('tr_machine_paper_size').style.display = '';
		document.getElementById('tr_machine_paper_min_size').style.display = '';
		document.getElementById('tr_machine_border').style.display = '';
		document.getElementById('tr_machine_colors_front').style.display = 'none';
		document.getElementById('tr_machine_colors_back').style.display = 'none';
	    document.getElementById('tr_machine_timeplatechange').style.display = 'none';
	    document.getElementById('tr_machine_timecolorchange').style.display = 'none';
	    document.getElementById('tr_machine_paperperhour').style.display = '';
	    document.getElementById('tr_machine_chromaticity').style.display = '';
	    document.getElementById('tr_machine_finish').style.display = 'none';
	    document.getElementById('tr_machine_umschl_umst').style.display = 'none';	//gln
	    document.getElementById('tr_machine_setup_stations').style.display = 'none';
	    document.getElementById('tr_machine_anz_stations').style.display = 'none';
	    document.getElementById('tr_machine_pages_per_station').style.display = 'none';
	    document.getElementById('tr_machine_anz_signatures').style.display = 'none';
	    document.getElementById('tr_machine_time_signatures').style.display = 'none';
	    document.getElementById('tr_machine_time_envelope').style.display = 'none';
	    document.getElementById('tr_machine_time_trimmer').style.display = 'none';
	    document.getElementById('tr_machine_time_stacker').style.display = 'none';
	    <?/*gln*/?>
		if (document.getElementById('machine_pricebase').value == <?=Machine::PRICE_MINUTE?> )
	    	showHideClpr(<?=Machine::PRICE_MINUTE?>);
	    else
		   	showHideClpr(0);
	} else if (val == <?=Machine::TYPE_CTP?>) {
		document.getElementById('tr_machine_paper_size').style.display = 'none';
		document.getElementById('tr_machine_paper_min_size').style.display = 'none';
		document.getElementById('tr_machine_border').style.display = 'none';
		document.getElementById('tr_machine_colors_front').style.display = 'none';
		document.getElementById('tr_machine_colors_back').style.display = 'none';
	    document.getElementById('tr_machine_timeplatechange').style.display = 'none';
	    document.getElementById('tr_machine_timecolorchange').style.display = 'none';
	    document.getElementById('tr_machine_paperperhour').style.display = '';
	    document.getElementById('tr_machine_chromaticity').style.display = 'none';
	    document.getElementById('tr_machine_finish').style.display = 'none';
	    document.getElementById('tr_machine_umschl_umst').style.display = 'none';	//gln
	    document.getElementById('tr_machine_setup_stations').style.display = 'none';
	    document.getElementById('tr_machine_anz_stations').style.display = 'none';
	    document.getElementById('tr_machine_pages_per_station').style.display = 'none';
	    document.getElementById('tr_machine_anz_signatures').style.display = 'none';
	    document.getElementById('tr_machine_time_signatures').style.display = 'none';
	    document.getElementById('tr_machine_time_envelope').style.display = 'none';
	    document.getElementById('tr_machine_time_trimmer').style.display = 'none';
	    document.getElementById('tr_machine_time_stacker').style.display = 'none';
	    showHideClpr(0); <?/*gln*/?>
	} else if (val == <?=Machine::TYPE_LAGENFALZ?>) {
		document.getElementById('tr_machine_paper_size').style.display = '';
		document.getElementById('tr_machine_paper_min_size').style.display = '';
		document.getElementById('tr_machine_border').style.display = 'none';
		document.getElementById('tr_machine_colors_front').style.display = 'none';
		document.getElementById('tr_machine_colors_back').style.display = 'none';
	    document.getElementById('tr_machine_timeplatechange').style.display = 'none';
	    document.getElementById('tr_machine_timecolorchange').style.display = 'none';
	    document.getElementById('tr_machine_paperperhour').style.display = '';
	    document.getElementById('tr_machine_chromaticity').style.display = 'none';
	    document.getElementById('tr_machine_finish').style.display = 'none';
	    document.getElementById('tr_machine_umschl_umst').style.display = 'none';	//gln
	    document.getElementById('tr_machine_setup_stations').style.display = '';
	    document.getElementById('tr_machine_anz_stations').style.display = '';
	    document.getElementById('tr_machine_pages_per_station').style.display = '';
	    document.getElementById('tr_machine_anz_signatures').style.display = 'none';
	    document.getElementById('tr_machine_time_signatures').style.display = 'none';
	    document.getElementById('tr_machine_time_envelope').style.display = 'none';
	    document.getElementById('tr_machine_time_trimmer').style.display = 'none';
	    document.getElementById('tr_machine_time_stacker').style.display = 'none';
	    showHideClpr(0); <?/*gln*/?>
	} else if (val == <?=Machine::TYPE_SAMMELHEFTER?>) {
		document.getElementById('tr_machine_paper_size').style.display = '';
		document.getElementById('tr_machine_paper_min_size').style.display = '';
		document.getElementById('tr_machine_border').style.display = 'none';
		document.getElementById('tr_machine_colors_front').style.display = 'none';
		document.getElementById('tr_machine_colors_back').style.display = 'none';
	    document.getElementById('tr_machine_timeplatechange').style.display = 'none';
	    document.getElementById('tr_machine_timecolorchange').style.display = 'none';
	    document.getElementById('tr_machine_paperperhour').style.display = '';
	    document.getElementById('tr_machine_chromaticity').style.display = 'none';
	    document.getElementById('tr_machine_finish').style.display = 'none';
	    document.getElementById('tr_machine_umschl_umst').style.display = 'none';	//gln
	    document.getElementById('tr_machine_setup_stations').style.display = 'none';
	    document.getElementById('tr_machine_anz_stations').style.display = 'none';
	    document.getElementById('tr_machine_pages_per_station').style.display = 'none';
	    document.getElementById('tr_machine_anz_signatures').style.display = '';
	    document.getElementById('tr_machine_time_signatures').style.display = '';
	    document.getElementById('tr_machine_time_envelope').style.display = '';
	    document.getElementById('tr_machine_time_trimmer').style.display = '';
	    document.getElementById('tr_machine_time_stacker').style.display = '';
	    showHideClpr(0); <?/*gln*/?>
	} else {
		document.getElementById('tr_machine_paper_size').style.display = '';
		document.getElementById('tr_machine_paper_min_size').style.display = '';
		document.getElementById('tr_machine_border').style.display = 'none';
		document.getElementById('tr_machine_colors_front').style.display = 'none';
		document.getElementById('tr_machine_colors_back').style.display = 'none';
	    document.getElementById('tr_machine_timeplatechange').style.display = 'none';
	    document.getElementById('tr_machine_timecolorchange').style.display = 'none';
	    document.getElementById('tr_machine_paperperhour').style.display = '';
	    document.getElementById('tr_machine_chromaticity').style.display = 'none';
	    document.getElementById('tr_machine_finish').style.display = 'none';
	    document.getElementById('tr_machine_umschl_umst').style.display = 'none';	//gln
	    document.getElementById('tr_machine_setup_stations').style.display = 'none';
	    document.getElementById('tr_machine_anz_stations').style.display = 'none';
	    document.getElementById('tr_machine_pages_per_station').style.display = 'none';
	    document.getElementById('tr_machine_anz_signatures').style.display = 'none';
	    document.getElementById('tr_machine_time_signatures').style.display = 'none';
	    document.getElementById('tr_machine_time_envelope').style.display = 'none';
	    document.getElementById('tr_machine_time_trimmer').style.display = 'none';
	    document.getElementById('tr_machine_time_stacker').style.display = 'none';
	    showHideClpr(0); <?/*gln*/?>
	}
}

function addUnitsPerHour()
{
	var count = parseInt(document.getElementsByName('units_per_hour_counter')[0].value);
	var text = '<br> ';
	text += '<?=$_LANG->get('ab')?> ';
	text += '<input name="units_from_'+count+'" style="width:45px;text-align:center"> '; 
	text += '<?=$_LANG->get('St.')?> - ';
    text += '<input name="units_per_hour_'+count+'" style="width:45px;text-align:center"> ';
    text += '<?=$_LANG->get('pro Stunde')?>';

    document.getElementById('td_machine_paperperhour').insertAdjacentHTML("beforeEnd", text);
    document.getElementsByName('units_per_hour_counter')[0].value = count+1;
}

function checkMachineType(val)
{
	if(val == <?=Machine::TYPE_DRUCKMASCHINE_DIGITAL?> || val == <?=Machine::TYPE_DRUCKMASCHINE_OFFSET?>)
	{
    	var mach = <?=$machine->getId()?>;
    	$.post("libs/modules/machines/machines.ajax.php", {exec: "checkMachineType", machId: mach}, function(data) {
    		// Work on returned data
    		if(data == 0)
    			alert('<?=$_LANG->get('Maximale Anzahl an Druckmaschinen bereits erreicht.')?>');
    	});
	}
}

function updateDifficultyFields(val, unit, id)
{

	if(val != '')
	{
        $.post("libs/modules/machines/machines.ajax.php", {exec: "updateDifficultyFields", val: val, machId: <?=$machine->getId()?>, unit: unit, id: id}, 
        	    function(data) {
    		document.getElementById('div_difficulty_fields_'+id).innerHTML = data;
    	});
        document.getElementById('div_difficulty_fields_'+id).style.display = '';
	} else
	{
		document.getElementById('div_difficulty_fields_'+id).innerHTML = '';
        document.getElementById('div_difficulty_fields_'+id).style.display = 'none';
	}
}

function addDifficultyField(id)
{
	var count = parseInt(document.getElementsByName('difficulty_counter_'+id)[0].value);

	var text = '';
	text += '<td>';
    text += '<input style="width:40px" name="machine_difficulty['+id+'][values][]"><br>';
    text += '<nobr><input style="width:40px" name="machine_difficulty['+id+'][percents][]"> %</nobr> ';
    text += "</td>";
	
    document.getElementById('tr_difficulty_fields_'+id).insertAdjacentHTML("beforeEnd", text);
    document.getElementsByName('difficulty_counter_'+id)[0].value = count+1;
}

</script>

<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
          <? if ($_REQUEST["exec"] == "copy") echo $_LANG->get('Maschine kopieren')?>
          <? if ($_REQUEST["exec"] == "edit" && $machine->getId() == 0) echo $_LANG->get('Maschine anlegen')?>
          <? if ($_REQUEST["exec"] == "edit" && $machine->getId() != 0) echo $_LANG->get('Maschine bearbeiten')?>
      </td>
      <td align="right"><?=$savemsg?></td>
   </tr>
</table>

<?php 
if ($machine->getId() > 0){
      // Associations
      $association_object = $machine;
      include 'libs/modules/associations/association.include.php';
      //-> END Associations
}
?>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="machine_form" onSubmit="return checkform(new Array(this.machine_name, this.machine_group,this.machine_type,this.machine_pricebase))">
<input type="hidden" name="exec" value="edit">
<input type="hidden" name="subexec" value="save">
<input type="hidden" name="id" value="<?=$machine->getId()?>">
<div class="box1">
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign="top">
			<table width="500" cellpadding="0" cellspacing="0" border="0">
				<colgroup>
					<col width="180">
					<col>
				</colgroup>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Bezeichnung')?> *</td>
					<td class="content_row_clear">
						<input name="machine_name" value="<?=$machine->getName()?>" style="width:300px;" class="text">
					</td>
				</tr>
				<tr>
					<td class="content_row_header" valign="top"><?=$_LANG->get('Dokumententext (wird bei "Verarbeitung" gedruckt)')?></td>
					<td class="content_row_clear">
						<textarea name="machine_documenttext" style="width:300px;height:100px" class="text"><?=$machine->getDocumentText()?></textarea>
					</td>
				</tr>
				<tr>
					<td class="content_row_header" valign="top"><?=$_LANG->get('Maschinengruppe')?> *</td>
					<td class="content_row_clear">
						<select name="machine_group" style="width:300px" class="text">
							<option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;
							<?
							foreach (MachineGroup::getAllMachineGroups() as $g)
							{
								echo '<option value="'.$g->getId().'" ';
								if($machine->getGroup()->getId() == $g->getId()) echo "selected";
								echo '>'.$g->getName().'</option>';
							} 
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="content_row_header" valign="top"><?=$_LANG->get('Maschinentyp')?> *</td>
					<td class="content_row_clear">
						<select id="machine_type" name="machine_type" style="width:300px" class="text" onchange="checkMachineType(this.value);showHide(this.value);">
							<option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
							<option value="<?=Machine::TYPE_DRUCKMASCHINE_OFFSET?>" <? if($machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) echo "selected";?>><?=$_LANG->get('Druckmaschine Offset / Buchdruck')?></option>
							<option value="<?=Machine::TYPE_DRUCKMASCHINE_DIGITAL?>" <? if($machine->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL) echo "selected";?>><?=$_LANG->get('Druckmaschine Digital')?></option>
							<option value="<?=Machine::TYPE_CTP?>" <? if($machine->getType() == Machine::TYPE_CTP) echo "selected";?>><?=$_LANG->get('Computer To Plate')?></option>
							<option value="<?=Machine::TYPE_FOLDER?>" <? if($machine->getType() == Machine::TYPE_FOLDER) echo "selected";?>><?=$_LANG->get('Falzmaschine')?></option>
							<option value="<?=Machine::TYPE_CUTTER?>" <? if($machine->getType() == Machine::TYPE_CUTTER) echo "selected";?>><?=$_LANG->get('Schneidemaschine')?></option>
							<option value="<?=Machine::TYPE_LAGENFALZ?>" <? if($machine->getType() == Machine::TYPE_LAGENFALZ) echo "selected";?>><?=$_LANG->get('Lagenfalz-/Zusammentragmaschine')?></option>
							<option value="<?=Machine::TYPE_SAMMELHEFTER?>" <? if($machine->getType() == Machine::TYPE_SAMMELHEFTER) echo "selected";?>><?=$_LANG->get('Sammelhefter')?></option>
							<option value="<?=Machine::TYPE_MANUELL?>" <? if($machine->getType() == Machine::TYPE_MANUELL) echo "selected";?>><?=$_LANG->get('Manuelle Arbeit')?></option>
							<option value="<?=Machine::TYPE_OTHER?>" <? if($machine->getType() == Machine::TYPE_OTHER) echo "selected";?>><?=$_LANG->get('Andere')?></option>
						</select>
					</td>
				</tr>    
				<tr>
					<td class="content_row_header" valign="top"><?=$_LANG->get('Preistyp')?> *</td>
					<td class="content_row_clear">
						<?/*gln <select name="machine_pricebase" style="width:300px" class="text">*/?>
						<select id="machine_pricebase" name="machine_pricebase" style="width:300px" class="text" onchange="if (document.getElementById('machine_type').value == <?=Machine::TYPE_DRUCKMASCHINE_DIGITAL?>) showHideClpr(this.value);">
							<option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
							<option value="<?=Machine::PRICE_AUFLAGE?>" <? if($machine->getPriceBase() == Machine::PRICE_AUFLAGE) echo "selected";?>><?=$_LANG->get('Preis nach Auflage')?></option>
							<option value="<?=Machine::PRICE_BOGEN?>" <? if($machine->getPriceBase() == Machine::PRICE_BOGEN) echo "selected";?>><?=$_LANG->get('Preis nach Bogen')?></option>
							<option value="<?=Machine::PRICE_DRUCKPLATTE?>" <? if($machine->getPriceBase() == Machine::PRICE_DRUCKPLATTE) echo "selected";?>><?=$_LANG->get('Preis nach Druckplatten')?></option>
							<option value="<?=Machine::PRICE_MINUTE?>" <? if($machine->getPriceBase() == Machine::PRICE_MINUTE) echo "selected";?>><?=$_LANG->get('Preis nach Minuten')?></option>
							<option value="<?=Machine::PRICE_PAUSCHAL?>" <? if($machine->getPriceBase() == Machine::PRICE_PAUSCHAL) echo "selected";?>><?=$_LANG->get('Pauschalpreis')?></option>
							<option value="<?=Machine::PRICE_SQUAREMETER?>" <? if($machine->getPriceBase() == Machine::PRICE_SQUAREMETER) echo "selected";?>><?=$_LANG->get('Preis pro Quadratmeter Bogen/Rolle')?></option>
							<option value="<?=Machine::PRICE_DPSQUAREMETER?>" <? if($machine->getPriceBase() == Machine::PRICE_DPSQUAREMETER) echo "selected";?>><?=$_LANG->get('Preis pro Quadratmeter Druckplatte')?></option>
							<option value="<?=Machine::PRICE_VARIABEL?>" <? if($machine->getPriceBase() == Machine::PRICE_VARIABEL) echo "selected";?>><?=$_LANG->get('Preis variabel')?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Preis')?></td>
					<td class="content_row_clear">
						<input id="machine_price" name="machine_price" value="<?=printPrice($machine->getPrice(),4)?>" style="width:60px;text-align:right;" class="text"> <?=$_USER->getClient()->getCurrency()?>
					</td>
				</tr>
				<? if($machine->getLectorId() != 0) { ?>
				<tr>
					<td class="content_row_header"><span class="error"><?=$_LANG->get('Importiert von Lector')?></span></td>
					<td class="content_row_clear">
						Lector-ID: <?=$machine->getLectorId()?>
					</td>
				</tr>
				<? } ?>
			</table>
		</td>
		<td valign="top">
			<table width="500" cellpadding="0" cellspacing="0" border="0">
				<colgroup>
					<col width="180">
					<col>
				</colgroup>
				<tr>
					<td class="content_row_header" valign="top">int. Beschreibung</td>
					<td class="content_row_clear" valign="top">
						<textarea name="machine_internaltext" style="width:300px;height:100px" class="text"><?=$machine->getInternalText()?></textarea>
					</td>
				</tr>
				<tr>
					<td class="content_row_header" valign="top">Hersteller</td>
					<td class="content_row_clear" valign="top">
						<textarea name="machine_hersteller" style="width:300px;height:100px" class="text"><?=$machine->getHersteller()?></textarea>
					</td>
				</tr>
				<tr>
					<td class="content_row_header" valign="top">Baujahr</td>
					<td class="content_row_clear" valign="top">
						<textarea name="machine_baujahr" style="width:300px;height:100px" class="text"><?=$machine->getBaujahr()?></textarea>
					</td>
				</tr>
				<tr>
					<td class="content_row_header" valign="top">Druckplatten</td>
					<td class="content_row_clear" valign="top">
						<input id="machine_DPHeight" name="machine_DPHeight" value="<?=printPrice($machine->getDPHeight(),4)?>" style="width:60px;text-align:right;" class="text"> x 
						<input id="machine_DPWidth" name="machine_DPWidth" value="<?=printPrice($machine->getDPWidth(),4)?>" style="width:60px;text-align:right;" class="text"> mm
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
</div>
<br>
<div class="box2">
<table cellpadding="0" cellspacing="0" border="0"><tr><td valign="top">
<table width="500">
    <colgroup>
        <col width="180">
        <col>
    </colgroup>    
    <tr id="tr_machine_colors_front" style="display:<? if($machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) echo ""; else echo "none;"?>">
        <td class="content_row_header"><?=$_LANG->get('Farben')?> <?=$_LANG->get('Vorderseite')?></td>
        <td class="content_row_clear">
            <input name="colors_front" style="width:60px;text-align:center" value="<?=$machine->getColors_front()?>">
            
        </td>
    </tr>    
    <tr id="tr_machine_colors_back" style="display:<? if($machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) echo ""; else echo "none;"?>">
        <td class="content_row_header"><?=$_LANG->get('Farben')?> <?=$_LANG->get('R&uuml;ckseite')?></td>
        <td class="content_row_clear">
            <input name="colors_back" style="width:60px;text-align:center" value="<?=$machine->getColors_back()?>">
        </td>
    </tr>
    <tr id="tr_machine_paper_size" style="display:<? if($machine->getType() != Machine::TYPE_CTP) echo ""; else echo "none;"?>">
        <td class="content_row_header"><?=$_LANG->get('Max. Papiergr&ouml;&szlig;e')?></td>
        <td class="content_row_clear">
            <?=$_LANG->get('Breite')?>: <input name="paper_size_width" style="width:40px;text-align:center" value="<?=$machine->getPaperSizeWidth()?>"> mm x 
            <?=$_LANG->get('H&ouml;he')?>: <input name="paper_size_height" style="width:40px;text-align:center" value="<?=$machine->getPaperSizeHeight()?>"> mm
        </td>
    </tr>
    <tr id="tr_machine_paper_min_size" style="display:<? if($machine->getType() != Machine::TYPE_CTP) echo ""; else echo "none;"?>">
        <td class="content_row_header"><?=$_LANG->get('Min. Papiergr&ouml;&szlig;e')?></td>
        <td class="content_row_clear">
            <?=$_LANG->get('Breite')?>: <input name="paper_size_min_width" style="width:40px;text-align:center" value="<?=$machine->getPaperSizeMinWidth()?>"> mm x 
            <?=$_LANG->get('H&ouml;he')?>: <input name="paper_size_min_height" style="width:40px;text-align:center" value="<?=$machine->getPaperSizeMinHeight()?>"> mm
        </td>
    </tr>
    <tr id="tr_machine_border" style="display:<? if($machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET || $machine->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL) echo ""; else echo "none;"?>">
        <td class="content_row_header"><?=$_LANG->get('nicht bedruckbarer Bereich')?></td>
        <td class="content_row_clear" align="center">
            <table>
                <tr>
                    <td></td>
                    <td class="content_row_clear"><input name="border_top" style="width:40px;text-align:center" value="<?=$machine->getBorder_top()?>"> mm</td>
                    <td></td>
                </tr>
                <tr>
                    <td class="content_row_clear"><input name="border_left" style="width:40px;text-align:center" value="<?=$machine->getBorder_left()?>"> mm</td>
                    <td></td>
                    <td class="content_row_clear"><input name="border_right" style="width:40px;text-align:center" value="<?=$machine->getBorder_right()?>"> mm</td>
                </tr>
                <tr>
                    <td></td>
                    <td class="content_row_clear"><input name="border_bottom" style="width:40px;text-align:center" value="<?=$machine->getBorder_bottom()?>"> mm</td>
                    <td></td>
                </tr>
                        
            </table>
            
        </td>
    </tr>
    <tr id="tr_machine_timebase">
        <td class="content_row_header"><?=$_LANG->get('Grundzeit')?></td>
        <td class="content_row_clear">
            <input name="time_base" style="width:60px;text-align:center" value="<?=$machine->getTimeBase()?>"> min
        </td>
    </tr>
    <tr id="tr_machine_timeplatechange" style="display:<? if($machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) echo ""; else echo "none;"?>">
        <td class="content_row_header"><?=$_LANG->get('Zeit pro Plattenwechsel')?></td>
        <td class="content_row_clear">
            <input name="time_platechange" style="width:60px;text-align:center" value="<?=$machine->getTimePlatechange()?>"> min
        </td>
    </tr>
    <tr id="tr_machine_timecolorchange" style="display:<? if($machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) echo ""; else echo "none;"?>">
        <td class="content_row_header"><?=$_LANG->get('Zeit pro Farbwechsel')?></td>
        <td class="content_row_clear">
            <input name="time_colorchange" style="width:60px;text-align:center" value="<?=$machine->getTimeColorchange()?>"> min
        </td>
    </tr>
    <tr id="tr_machine_setup_stations" style="display:<? if($machine->getType() == Machine::TYPE_LAGENFALZ) echo ""; else echo "none;"?>">
        <td class="content_row_header"><?=$_LANG->get('Zeit Einrichtung Station')?></td>
        <td class="content_row_clear">
            <input name="time_setup_stations" style="width:60px;text-align:center" value="<?=$machine->getTimeSetupStations()?>"> min
        </td>
    </tr>
    <tr id="tr_machine_anz_stations" style="display:<? if($machine->getType() == Machine::TYPE_LAGENFALZ) echo ""; else echo "none;"?>">
        <td class="content_row_header"><?=$_LANG->get('Anzahl Stationen')?></td>
        <td class="content_row_clear">
            <input name="anz_stations" style="width:60px;text-align:center" value="<?=$machine->getAnzStations()?>">
        </td>
    </tr>
    <tr id="tr_machine_pages_per_station" style="display:<? if($machine->getType() == Machine::TYPE_LAGENFALZ) echo ""; else echo "none;"?>">
        <td class="content_row_header"><?=$_LANG->get('Seiten pro Station')?></td>
        <td class="content_row_clear">
            <input name="pages_per_station" style="width:60px;text-align:center" value="<?=$machine->getPagesPerStation()?>">
        </td>
    </tr>
    <tr id="tr_machine_anz_signatures" style="display:<? if($machine->getType() == Machine::TYPE_SAMMELHEFTER) echo ""; else echo "none;"?>">
        <td class="content_row_header"><?=$_LANG->get('Anzahl Signaturen')?></td>
        <td class="content_row_clear">
            <input name="anz_signatures" style="width:60px;text-align:center" value="<?=$machine->getAnzSignatures()?>">
        </td>
    </tr>
    <tr id="tr_machine_time_signatures" style="display:<? if($machine->getType() == Machine::TYPE_SAMMELHEFTER) echo ""; else echo "none;"?>">
        <td class="content_row_header"><?=$_LANG->get('R&uuml;stzeit pro Signatur')?></td>
        <td class="content_row_clear">
            <input name="time_signatures" style="width:60px;text-align:center" value="<?=$machine->getTimeSignatures()?>"> min
        </td>
    </tr>
    <tr id="tr_machine_time_envelope" style="display:<? if($machine->getType() == Machine::TYPE_SAMMELHEFTER) echo ""; else echo "none;"?>">
        <td class="content_row_header"><?=$_LANG->get('R&uuml;stzeit Umschlaganleger')?> *</td>
        <td class="content_row_clear">
            <input name="time_envelope" style="width:60px;text-align:center" value="<?=$machine->getTimeEnvelope()?>"> min
        </td>
    </tr>
    <tr id="tr_machine_time_trimmer" style="display:<? if($machine->getType() == Machine::TYPE_SAMMELHEFTER) echo ""; else echo "none;"?>">
        <td class="content_row_header"><?=$_LANG->get('R&uuml;stzeit Dreischneider')?> *</td>
        <td class="content_row_clear">
            <input name="time_trimmer" style="width:60px;text-align:center" value="<?=$machine->getTimeTrimmer()?>"> min
        </td>
    </tr>
    <tr id="tr_machine_time_stacker" style="display:<? if($machine->getType() == Machine::TYPE_SAMMELHEFTER) echo ""; else echo "none;"?>">
        <td class="content_row_header"><?=$_LANG->get('R&uuml;stzeit Kreuzleger')?> *</td>
        <td class="content_row_clear">
            <input name="time_stacker" style="width:60px;text-align:center" value="<?=$machine->getTimeStacker()?>"> min
        </td>
    </tr>
</table>
</td><td valign="top">
<table width="500">
    <colgroup>
        <col width="180">
        <col>
    </colgroup> 
    <tr id="tr_machine_chromaticity" style="display:<? if($machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET || $machine->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ) echo ""; else echo "none;"?>">
        <td class="content_row_header" valign="top"><?=$_LANG->get('verf&uuml;gbare Farbigkeiten')?></td>
        <td class="content_row_clear">
        <table cellpadding="0" cellspacing="0" border="0">
	    <colgroup>
	        <col>
	        <col width="60">
	        <col>
    	</colgroup> 
        <? 

        function chromaActive($chr, $machine)
        {
            foreach ($machine->getChromaticities() as $c)
            {
                if($chr->getId() == $c->getId())
                    return true;
            }
            return false;
        }
        //gln
        $anzeige = true;
        $chrs = Chromaticity::getAllChromaticities(Chromaticity::ORDER_NAME);
        foreach($chrs as $chr)
        {
        ?>
        	<tr> 
	        <td class="content_row_header">
        <?	
            echo '<input name="chroma_'.$chr->getId().'" type="checkbox" value="1" ';
            if(chromaActive($chr, $machine) === true) echo "checked";
            echo '>'.$chr->getName()."<br>";
            ?>
            </td>
	        <td name="anz_click1" class="content_row_header" valign="top" align="right" style="display:<? if($machine->getPriceBase() == Machine::PRICE_MINUTE && $machine->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ) echo ""; else echo "none;"?>"><?if($anzeige) echo $_LANG->get('Click');else echo "";?></td>
            <td name="anz_click2" style="display:<? if($machine->getPriceBase() == Machine::PRICE_MINUTE && $machine->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ) echo ""; else echo "none;"?>">
            <?
            if(chromaActive($chr, $machine) === true) 
	            echo '<input name="click_'.$chr->getId().'" style="width:60px;text-align:center" value="'. printPrice($machine->getCurrentClickprice($chr), 4).'"> '. $_USER->getClient()->getCurrency();
			else
	            echo '<input name="click_'.$chr->getId().'" style="width:60px;text-align:center" value="'. printPrice(0, 4).'"> '. $_USER->getClient()->getCurrency();
			?>
			</td>
			</tr>
			<?
			$anzeige = false;
        }
        ?>
        </table>
        </td>
    </tr>
    <tr id="tr_machine_finish" style="display:<? if($machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) echo ""; else echo "none;"?>">
        <td class="content_row_header" valign="top"><?=$_LANG->get('Option Lack verf&uuml;gbar')?></td>
        <td class="content_row_clear">
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <td><input name="finish" value="1" type="checkbox" <?if($machine->getFinish() == 1) echo "checked";?>></td>


                    <td class="content_row_clear"> &emsp;&emsp; <?=$_LANG->get('Preis Lackplatte')?></td> 
       <?/* gln </tr>
                <tr> */?>
                    <td class="content_row_clear">
                        <input name="finish_plate_cost" value="<?=printPrice($machine->getFinishPlateCost())?>" style="width:40px;text-align:center;">
                        <?=$_USER->getClient()->getCurrency()?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>  
     <?/* gln 13.05.2014, Umschlagen/Umstuelpen */?>
    <tr id="tr_machine_umschl_umst" style="display:<? if($machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) echo ""; else echo "none;"?>">
        <td class="content_row_header" valign="top"><?=$_LANG->get('Option umschlagen/umst&uuml;lpen')?></td>
        <td class="content_row_clear">
            <input name="umschl_umst" value="1" type="checkbox" <?if($machine->getUmschlUmst() == 1) echo "checked";?>>
        </td>
    </tr>  
     <?/* ascherer 19.08.14, Schneidemaschine */?>
    <tr id="tr_machine_cut_price" style="display:<? if($machine->getType() == Machine::TYPE_CUTTER) echo ""; else echo "none;"?>">
        <td class="content_row_header" valign="top"><?=$_LANG->get('Schnittpreis')?></td>
        <td class="content_row_clear">
            <input name="cut_price" value="<?=printPrice($machine->getCutPrice(), 4)?>">
        </td>
    </tr>  
    <tr id="tr_machine_paperperhour">
        <td class="content_row_header" valign="top"><?=$_LANG->get('Laufleistung')?></td>
        <td class="content_row_clear" id="td_machine_paperperhour">
            <input type="hidden" name="units_per_hour_counter" value="<?if(count($machine->getAllUnitsPerHour()) == 0) echo "1"; else echo count($machine->getAllUnitsPerHour());?>">
            <? $x = 0; $first = true;
            foreach($machine->getAllUnitsPerHour() as $unit)
            {
            ?>
            
            <?=$_LANG->get('ab')?> 
            <input name="units_from_<?=$x?>" style="width:45px;text-align:center" value="<?=printBigInt($unit["from"])?>"> <?=$_LANG->get('St.')?> -
            <input name="units_per_hour_<?=$x?>" style="width:45px;text-align:center" value="<?=printBigInt($unit["per_hour"])?>">
            <? if($first) { ?>
            <select name="unit" style="width:90px" class="text">
                <option value="<?=Machine::UNIT_PERHOUR_BOGEN?>" <?if($machine->getUnit() == Machine::UNIT_PERHOUR_BOGEN) echo "selected"?>><?=$_LANG->get('Bogen')?></option>
                <option value="<?=Machine::UNIT_PERHOUR_AUFLAGE?>" <?if($machine->getUnit() == Machine::UNIT_PERHOUR_AUFLAGE) echo "selected"?>><?=$_LANG->get('Auflagen')?></option>
                <option value="<?=Machine::UNIT_PERHOUR_SEITEN?>" <?if($machine->getUnit() == Machine::UNIT_PERHOUR_SEITEN) echo "selected"?>><?=$_LANG->get('Seiten')?></option>
                <option value="<?=Machine::UNIT_PERHOUR_DRUCKPLATTEN?>" <?if($machine->getUnit() == Machine::UNIT_PERHOUR_DRUCKPLATTEN) echo "selected"?>><?=$_LANG->get('Druckplatten')?></option>
            </select> <? } ?>
            <?=$_LANG->get('pro Stunde')?>
            <? if($first) { ?>            
            <img src="images/icons/plus.png" class="pointer icon-link" onclick="addUnitsPerHour()">
            <? } // if?>
            <br>
            <? $first = false; $x++;} // foreach?>
            
            <? if(count($machine->getAllUnitsPerHour()) == 0) { ?>
            ab <input name="units_from_0" style="width:45px;text-align:center" value="0"> <?=$_LANG->get('St.')?> -
            <input name="units_per_hour_0" style="width:45px;text-align:center" value="">
            <select name="unit" style="width:90px" class="text">
                <option value="<?=Machine::UNIT_PERHOUR_BOGEN?>"><?=$_LANG->get('Bogen')?></option>
                <option value="<?=Machine::UNIT_PERHOUR_AUFLAGE?>"><?=$_LANG->get('Auflagen')?></option>
                <option value="<?=Machine::UNIT_PERHOUR_SEITEN?>"><?=$_LANG->get('Seiten')?></option>
                <option value="<?=Machine::UNIT_PERHOUR_DRUCKPLATTEN?>"><?=$_LANG->get('Druckplatten')?></option>
            </select>
            <?=$_LANG->get('pro Stunde')?>
            <img src="images/icons/plus.png" class="pointer icon-link" onclick="addUnitsPerHour()">
            
            <? } ?>
        </td>
    </tr>
    
    <?
//     print_r($machine->getDifficulties());
    
    $tmp_diff_highid = 0;
    $tmp_diff_array = $machine->getDifficulties();
    foreach ($tmp_diff_array as $tmp_diff){
        if ($tmp_diff["id"]>$tmp_diff_highid){
            $tmp_diff_highid = $tmp_diff["id"];
        }
    }
    $tmp_diff_highid = $tmp_diff_highid+1;
    
    $tmp_diff_array[$tmp_diff_count]["id"] = $tmp_diff_highid;
    $tmp_diff_array[$tmp_diff_count]["unit"] = 0;
    $tmp_diff_array[$tmp_diff_count]["values"] = Array();
    $tmp_diff_array[$tmp_diff_count]["percents"] = Array();
    foreach ($tmp_diff_array as $difficulty){
        ?>
        
        <tr id="tr_machine_difficulty">
            <td class="content_row_header" valign="top"><?=$_LANG->get('Erschwerniszuschlag')?></td>
            <td class="content_row_clear"> <?=$_LANG->get('f&uuml;r')?> 
                <input type="hidden" name="machine_difficulty[<?=$difficulty["id"]?>][id]" value="<?=$difficulty["id"]?>">
                <select name="machine_difficulty[<?=$difficulty["id"]?>][unit]" id="machine_difficulty_<?=$difficulty["id"]?>" style="width:160px" onchange="updateDifficultyFields(this.value, <?=$difficulty['unit']?>, <?=$difficulty['id']?>)">
                    <option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
                    <option value="<?=Machine::DIFFICULTY_GAMMATUR?>" <? if($difficulty["unit"] == Machine::DIFFICULTY_GAMMATUR) echo "selected"?>><?=$_LANG->get('Grammatur')?></option>
                    <option value="<?=Machine::DIFFICULTY_PAGES?>" <? if($difficulty["unit"] == Machine::DIFFICULTY_PAGES) echo "selected"?>><?=$_LANG->get('Seiten')?></option>
                    <option value="<?=Machine::DIFFICULTY_STATIONS?>" <? if($difficulty["unit"] == Machine::DIFFICULTY_STATIONS) echo "selected"?>><?=$_LANG->get('Stationen')?></option>
                    <option value="<?=Machine::DIFFICULTY_BRUECHE?>" <? if($difficulty["unit"] == Machine::DIFFICULTY_BRUECHE) echo "selected"?>><?=$_LANG->get('Br&uuml;che')?></option>
                    <option value="<?=Machine::DIFFICULTY_UNITS_PER_HOUR?>" <? if($difficulty["unit"] == Machine::DIFFICULTY_UNITS_PER_HOUR) echo "selected"?>><?=$_LANG->get('Laufleistung')?></option>
                </select>
                <div id="div_difficulty_fields_<?=$difficulty["id"]?>">
                <?$x = 0; if(count($difficulty["values"]) > 0)
                {
                    echo "<table><tr id=\"tr_difficulty_fields_".$difficulty["id"]."\">";
                    foreach($difficulty["values"] as $diff)
                    {
                        echo '<td>';
                        echo '<input style="width:40px" name="machine_difficulty['.$difficulty["id"].'][values][]" value="'.$difficulty["values"][$x].'"><br>';
                        echo '<nobr><input style="width:40px" name="machine_difficulty['.$difficulty["id"].'][percents][]" value="'.$difficulty["percents"][$x].'"> %</nobr> ';
                        echo "</td>";
                        $x++;
                    }
                    echo "</tr></table>";
                    echo '<img src="images/icons/plus.png" onclick="addDifficultyField('.$difficulty['id'].')" class="pointer icon-link">';
                } else
                {
                    echo "<table><tr id=\"tr_difficulty_fields_".$difficulty["id"]."\"><td>";
                    echo '<input style="width:40px" name="machine_difficulty['.$difficulty["id"].'][values][]"><br>';
   				    echo '<input style="width:40px" name="machine_difficulty['.$difficulty["id"].'][percents][]"> % ';
                    echo "</td></tr></table>";
                    echo '<img src="images/icons/plus.png" onclick="addDifficultyField('.$difficulty['id'].')" class="pointer icon-link">';
                    $x++;
                }
                
                ?>
                </div>
                <? echo '<input name="difficulty_counter_'.$difficulty["id"].'" id="difficulty_counter_'.$difficulty["id"].'" value="'.$x.'" type="hidden">'; ?>
            </td>
        </tr>
    <?
    }
    ?>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Tageslaufzeit')?></td>
        <td class="content_row_clear"> 
            <input name="max_hours" value="<?=$machine->getMaxHours()?>" style="width:60px">
            <?=$_LANG->get('Stunden')?>
        </td>
    </tr>  
</table>
</td></tr></table>
* <?=$_LANG->get('Falls Werte = 0, nicht vorhanden')?>

</div>
<br>
<table width="100%">
    <colgroup>
        <col width="180">
        <col>
    </colgroup>    
    <tr>
        <td class="content_row_header">
	        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
	        			onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
		</td>
        <td class="content_row_clear" align="right">
            <input type="submit" value="<?=$_LANG->get('Speichern')?>">
        </td>
    </tr>
</table>
</form>