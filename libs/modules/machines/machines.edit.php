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

if($_REQUEST["dellock"] && $_REQUEST["dellock"] != ""){
    $tmp_lock = new MachineLock((int)$_REQUEST["dellock"]);
    $tmp_lock->delete();
}

if($_REQUEST["subexec"] == "save")
{
    $chromas = Array();
    $clicks = Array();
    $units = Array();
    $difficulties = Array();
    foreach (array_keys($_REQUEST) as $key)
    {
        if (preg_match("/chroma_(?P<id>\d+)/", $key, $m))
		{ 
            $chromas[] = new Chromaticity($m["id"]);
            if ((int)$_REQUEST["machine_type"] == Machine::TYPE_DRUCKMASCHINE_DIGITAL && (int)$_REQUEST["machine_pricebase"] == Machine::PRICE_MINUTE)
   				$clicks[] = (float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["click_{$m["id"]}"])));
   			else
   				$clicks[] = (float)0;
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
	$machine->setMachurl($_REQUEST["machine_url"]);

    $machine->setDPHeight((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["machine_DPHeight"]))));
    $machine->setDPWidth((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["machine_DPWidth"]))));
    
    $machine->setBreaks((int)$_REQUEST["breaks"]);
    $machine->setBreaks_time((int)$_REQUEST["breaks_time"]);
    
    $machine->setColor($_REQUEST["machine_color"]);
	$machine->setMaxstacksize($_REQUEST["machine_maxstacksize"]);

    $quser_list = Array();
    if ($_REQUEST["qusr"]){
        foreach ($_REQUEST["qusr"] as $qusr)
        {
            $quser_list[] = new User((int)$qusr);
        }
    }
    $machine->setQualified_users($quser_list);
    

    $tmp_wtime_arr = Array();
    if ($_REQUEST["wotime"])
    {
        for($i=0;$i<7;$i++)
        {
            if (count($_REQUEST["wotime"][$i])>0)
            {
                foreach ($_REQUEST["wotime"][$i] as $wtime)
                {
                    if ($wtime["start"]>0 && $wtime["end"]>0)
                    {
                        $tmp_wtime_arr[$i][] = Array("start"=>strtotime($wtime["start"]),"end"=>strtotime($wtime["end"]));
                    }
                }
            }
        }
    }
    $machine->setRunninghours($tmp_wtime_arr);
    
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
    
    if ($_REQUEST["lock_start"] && $_REQUEST["lock_start"] != "" && $_REQUEST["lock_stop"] && $_REQUEST["lock_stop"] != ""){
        $mlock = new MachineLock();
        $mlock->setMachineid($machine->getId());
        $mlock->setStart(strtotime($_REQUEST["lock_start"]));
        $mlock->setStop(strtotime($_REQUEST["lock_stop"]));
        $mlock->save();
    }
    
    /***
     ($machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET || 	// und eine Druckmaschine ist,
    		$machine->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL) ){	// muss geschaut werden, ob verfuegbare Anzahl erreicht ist
     */
}
?>

<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>
<link rel="stylesheet" media="screen" type="text/css" href="jscripts/colorpicker/colorpicker.css" />
<script type="text/javascript" src="jscripts/colorpicker/colorpicker.js"></script>

<script	type="text/javascript" src="jscripts/timepicker/jquery-ui-timepicker-addon.js"></script>
<link href='jscripts/timepicker/jquery-ui-timepicker-addon.css' rel='stylesheet'/>
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
	    document.getElementById('tr_machine_breaks').style.display = 'none';
	    document.getElementById('tr_machine_breaks_time').style.display = 'none';
	    
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
		document.getElementById('tr_machine_finish').style.display = '';
	    document.getElementById('tr_machine_umschl_umst').style.display = 'none';	//gln
	    document.getElementById('tr_machine_setup_stations').style.display = 'none';
	    document.getElementById('tr_machine_anz_stations').style.display = 'none';
	    document.getElementById('tr_machine_pages_per_station').style.display = 'none';
	    document.getElementById('tr_machine_anz_signatures').style.display = 'none';
	    document.getElementById('tr_machine_time_signatures').style.display = 'none';
	    document.getElementById('tr_machine_time_envelope').style.display = 'none';
	    document.getElementById('tr_machine_time_trimmer').style.display = 'none';
	    document.getElementById('tr_machine_time_stacker').style.display = 'none';
	    document.getElementById('tr_machine_breaks').style.display = 'none';
	    document.getElementById('tr_machine_breaks_time').style.display = 'none';
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
	    document.getElementById('tr_machine_breaks').style.display = 'none';
	    document.getElementById('tr_machine_breaks_time').style.display = 'none';
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
	    document.getElementById('tr_machine_breaks').style.display = 'none';
	    document.getElementById('tr_machine_breaks_time').style.display = 'none';
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
	    document.getElementById('tr_machine_breaks').style.display = 'none';
	    document.getElementById('tr_machine_breaks_time').style.display = 'none';
	    showHideClpr(0); <?/*gln*/?>
	} else if (val == <?=Machine::TYPE_FOLDER?>) {
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
	    document.getElementById('tr_machine_breaks').style.display = '';
	    document.getElementById('tr_machine_breaks_time').style.display = '';
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
	    document.getElementById('tr_machine_breaks').style.display = 'none';
	    document.getElementById('tr_machine_breaks_time').style.display = 'none';
	    showHideClpr(0); <?/*gln*/?>
	}
}

function addUnitsPerHour()
{
	var count = parseInt(document.getElementsByName('units_per_hour_counter')[0].value);
	var text = '<label for="" class="col-sm-2 control-label">ab</label><div class="col-sm-3"><div class="input-group"><input name="units_from_'+count+'" class="form-control"><span class="input-group-addon">St.</span></div></div>';
	text += '<label for="" class="col-sm-1 control-label">bis</label><div class="col-sm-3"><div class="input-group"><input name="units_per_hour_'+count+'" class="form-control"><span class="input-group-addon">St.</span></div></div>';
	text += '<div class="col-sm-3 form-text" style="font-size: 14px; height: 34px;"><?= $_LANG->get('pro Stunde')?></div>';

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
	console.log("updateDifficultyFields");
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
	console.log("addDifficultyField");
	var count = parseInt(document.getElementsByName('difficulty_counter_'+id)[0].value);

	var text ='<label for="" class="col-sm-3 control-label">&nbsp;</label><div class="col-sm-3"><input class="form-control" name="machine_difficulty['+id+'][values][]"></div>';
    text += '<div class="col-sm-3"><div class="input-group"><input class="form-control" name="machine_difficulty['+id+'][percents][]"><span class="input-group-addon">%</span></div></div>';
	text += '<div class="col-sm-3 form-text" style="font-size: 14px; height: 34px;">&nbsp;</div>';
	$('#tr_difficulty_fields_'+id).append(text);
//    document.getElementById('tr_difficulty_fields_'+id).insertAdjacentHTML("beforeEnd", text);
    document.getElementsByName('difficulty_counter_'+id)[0].value = count+1;
}

<?php 
if($machine->getId() > 0){
    echo 'showHide('.$machine->getType().');';
}
?>
</script>
<script language="JavaScript">
$(function() {
	$('#lock_start').datetimepicker({
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
	$('#lock_stop').datetimepicker({
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
	$('#machine_color').ColorPicker({
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		}
	})
	.bind('keyup', function(){
		$(this).ColorPickerSetColor(this.value);
	});
});
</script>
<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#machine_form').submit();",'glyphicon-floppy-disk');

if ($machine->getId()>0){
	$quickmove->addItem('Löschen', '#',  "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&id".$machine->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>
<form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" class="form-horizontal" id="machine_form"
	  name="machine_form"
	  onSubmit="return checkform(new Array(this.machine_name, this.machine_group,this.machine_type,this.machine_pricebase))">
	<input type="hidden" name="exec" value="edit">
	<input type="hidden" name="subexec" value="save">
	<input type="hidden" name="id" value="<?= $machine->getId() ?>">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				<? if ($_REQUEST["exec"] == "copy") echo $_LANG->get('Maschine kopieren') ?>
				<? if ($_REQUEST["exec"] == "edit" && $machine->getId() == 0) echo $_LANG->get('Maschine anlegen') ?>
				<? if ($_REQUEST["exec"] == "edit" && $machine->getId() != 0) echo $_LANG->get('Maschine bearbeiten') ?>
				<span class="pull-right">
					<?= $savemsg ?>

					<?php
					if ($machine->getId() > 0) {
						$association_object = $machine;
						$associations = Association::getAssociationsForObject(get_class($association_object), $association_object->getId());
					}
					?>
					<script type="text/javascript">
						function removeAsso(id) {
							$.ajax({
								type: "POST",
								url: "libs/modules/associations/association.ajax.php",
								data: {ajax_action: "delete_asso", id: id}
							})
						}
					</script>

<script>
	$(function() {
		$("a#association_hiddenclicker").fancybox({
			'type'    : 'iframe',
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600,
			'speedOut'		:	200,
			'height'		:	350,
			'overlayShow'	:	true,
			'helpers'		:   { overlay:null, closeClick:true }
		});
	});
	function callBoxFancyAsso(my_href) {
		var j1 = document.getElementById("association_hiddenclicker");
		j1.href = my_href;
		$('#association_hiddenclicker').trigger('click');
	}
</script>
<div id="association_hidden_clicker" style="display:none"><a id="association_hiddenclicker" href="http://www.google.com" >Hidden Clicker</a></div>

<script>
	$(function() {
		$("a#hiddenclicker").fancybox({
			'type'          :   'iframe',
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600,
			'speedOut'		:	200,
			'width'         :   1024,
			'height'		:	768,
			'scrolling'     :   'yes',
			'overlayShow'	:	true,
			'helpers'		:   { overlay:null, closeClick:true }
		});
	});
	function callBoxFancyPreview(my_href) {
		var j1 = document.getElementById("hiddenclicker");
		j1.href = my_href;
		$('#hiddenclicker').trigger('click');
	}
</script>
<div id="hidden_clicker" style="display:none"><a id="hiddenclicker" href="http://www.google.com" >Hidden Clicker</a></div>

		<div class="btn-group dropdown">
			<button type="button" class="btn btn-sm dropdown-toggle btn-default"
					data-toggle="dropdown" aria-expanded="false">
				Verknüpfungen <span class="badge"><?php echo count($associations); ?></span> <span
					class="caret"></span>
			</button>
			<ul class="dropdown-menu" role="menu">
				<?php
				if (count($associations) > 0) {
					$as = 0;
					foreach ($associations as $association) {
						if ($association->getModule1() == get_class($association_object) && $association->getObjectid1() == $association_object->getId()) {
							$classname = $association->getModule2();
							$object = new $classname($association->getObjectid2());
							$link_href = Association::getPath($classname);
							$object_name = Association::getName($object);
						} else {
							$classname = $association->getModule1();
							$object = new $classname($association->getObjectid1());
							$link_href = Association::getPath($classname);
							$object_name = Association::getName($object);
						}
						echo '<li id="as_' . $as . '"><a href="index.php?page=' . $link_href . $object->getId() . '">';
						echo $object_name;
						echo '</a>';
						if ($_USER->isAdmin() || $_USER->hasRightsByGroup(Group::RIGHT_ASSO_DELETE))
							echo '<span class="glyphicons glyphicons-remove pointer" onclick=\'removeAsso(' . $association->getId() . '); $("#as_' . $as . '").remove();\'></span>';
						echo '</li>';
						$as++;
					}
				}
				echo '<li class="divider"></li>';
				echo '<li><a href="#" onclick="callBoxFancyAsso(\'libs/modules/associations/association.frame.php?module=' . get_class($association_object) . '&objectid=' . $association_object->getId() . '\');">Neue Verknüpfung</a></li>';
				?>
			</ul>
		</div>
				</span>
			</h3>
		</div>



		<div class="panel-body">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						Kopfdaten
					</h3>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Bezeichnung</label>
								<div class="col-sm-9">
									<input name="machine_name" id="" value="<?= $machine->getName() ?>"
										   class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Dokumententext (wird bei "Verarbeitung"
									gedruckt)</label>
								<div class="col-sm-9">
									<textarea name="machine_documenttext"
											  class="form-control"><?= $machine->getDocumentText() ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Maschinengruppe</label>
								<div class="col-sm-9">
									<select name="machine_group" class="form-control">
										<option value="">&lt; <?= $_LANG->get('Bitte w&auml;hlen') ?> &gt;
											<?
											foreach (MachineGroup::getAllMachineGroups() as $g) {
												echo '<option value="' . $g->getId() . '" ';
												if ($machine->getGroup()->getId() == $g->getId()) echo "selected";
												echo '>' . $g->getName() . '</option>';
											}
											?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Maschinentyp</label>
								<div class="col-sm-9">
									<select id="machine_type" name="machine_type" class="form-control"
											onchange="checkMachineType(this.value);showHide(this.value);">
										<option value="">&lt; <?= $_LANG->get('Bitte w&auml;hlen') ?> &gt;</option>
										<option
											value="<?= Machine::TYPE_DRUCKMASCHINE_OFFSET ?>" <? if ($machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) echo "selected"; ?>><?= $_LANG->get('Druckmaschine Offset / Buchdruck') ?></option>
										<option
											value="<?= Machine::TYPE_DRUCKMASCHINE_DIGITAL ?>" <? if ($machine->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL) echo "selected"; ?>><?= $_LANG->get('Druckmaschine Digital') ?></option>
										<option
											value="<?= Machine::TYPE_CTP ?>" <? if ($machine->getType() == Machine::TYPE_CTP) echo "selected"; ?>><?= $_LANG->get('Computer To Plate') ?></option>
										<option
											value="<?= Machine::TYPE_FOLDER ?>" <? if ($machine->getType() == Machine::TYPE_FOLDER) echo "selected"; ?>><?= $_LANG->get('Falzmaschine') ?></option>
										<option
											value="<?= Machine::TYPE_CUTTER ?>" <? if ($machine->getType() == Machine::TYPE_CUTTER) echo "selected"; ?>><?= $_LANG->get('Schneidemaschine') ?></option>
										<option
											value="<?= Machine::TYPE_LASERCUTTER ?>" <? if ($machine->getType() == Machine::TYPE_LASERCUTTER) echo "selected"; ?>><?= $_LANG->get('Stanze / Laser-Stanze') ?></option>
										<option
											value="<?= Machine::TYPE_LAGENFALZ ?>" <? if ($machine->getType() == Machine::TYPE_LAGENFALZ) echo "selected"; ?>><?= $_LANG->get('Lagenfalz-/Zusammentragmaschine') ?></option>
										<option
											value="<?= Machine::TYPE_SAMMELHEFTER ?>" <? if ($machine->getType() == Machine::TYPE_SAMMELHEFTER) echo "selected"; ?>><?= $_LANG->get('Sammelhefter') ?></option>
										<option
											value="<?= Machine::TYPE_MANUELL ?>" <? if ($machine->getType() == Machine::TYPE_MANUELL) echo "selected"; ?>><?= $_LANG->get('Manuelle Arbeit') ?></option>
										<option
											value="<?= Machine::TYPE_OTHER ?>" <? if ($machine->getType() == Machine::TYPE_OTHER) echo "selected"; ?>><?= $_LANG->get('Andere') ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Preistyp</label>
								<div class="col-sm-9">
									<? /*gln <select name="machine_pricebase" style="width:300px" class="text">*/ ?>
									<select id="machine_pricebase" name="machine_pricebase" class="form-control"
											onchange="if (document.getElementById('machine_type').value == <?= Machine::TYPE_DRUCKMASCHINE_DIGITAL ?>) showHideClpr(this.value);">
										<option value="">&lt; <?= $_LANG->get('Bitte w&auml;hlen') ?> &gt;</option>
										<option
											value="<?= Machine::PRICE_AUFLAGE ?>" <? if ($machine->getPriceBase() == Machine::PRICE_AUFLAGE) echo "selected"; ?>><?= $_LANG->get('Preis nach Auflage') ?></option>
										<option
											value="<?= Machine::PRICE_BOGEN ?>" <? if ($machine->getPriceBase() == Machine::PRICE_BOGEN) echo "selected"; ?>><?= $_LANG->get('Preis nach Bogen') ?></option>
										<option
											value="<?= Machine::PRICE_DRUCKPLATTE ?>" <? if ($machine->getPriceBase() == Machine::PRICE_DRUCKPLATTE) echo "selected"; ?>><?= $_LANG->get('Preis nach Druckplatten') ?></option>
										<option
											value="<?= Machine::PRICE_MINUTE ?>" <? if ($machine->getPriceBase() == Machine::PRICE_MINUTE) echo "selected"; ?>><?= $_LANG->get('Preis nach Minuten') ?></option>
										<option
											value="<?= Machine::PRICE_PAUSCHAL ?>" <? if ($machine->getPriceBase() == Machine::PRICE_PAUSCHAL) echo "selected"; ?>><?= $_LANG->get('Pauschalpreis') ?></option>
										<option
											value="<?= Machine::PRICE_SQUAREMETER ?>" <? if ($machine->getPriceBase() == Machine::PRICE_SQUAREMETER) echo "selected"; ?>><?= $_LANG->get('Preis pro Quadratmeter Bogen/Rolle') ?></option>
										<option
											value="<?= Machine::PRICE_DPSQUAREMETER ?>" <? if ($machine->getPriceBase() == Machine::PRICE_DPSQUAREMETER) echo "selected"; ?>><?= $_LANG->get('Preis pro Quadratmeter Druckplatte') ?></option>
										<option
											value="<?= Machine::PRICE_VARIABEL ?>" <? if ($machine->getPriceBase() == Machine::PRICE_VARIABEL) echo "selected"; ?>><?= $_LANG->get('Preis variabel') ?></option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Preis</label>
								<div class="col-sm-9">
									<div class="input-group">
										<input id="machine_price" name="machine_price"
											   value="<?= printPrice($machine->getPrice(), 4) ?>" class="form-control">
										<span class="input-group-addon"><?= $_USER->getClient()->getCurrency() ?></span>
									</div>
								</div>
							</div>
							<? if ($machine->getLectorId() != 0) { ?>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label"><span
											class="error"><?= $_LANG->get('Importiert von Lector') ?></span></label>
									<div class="col-sm-9">
										Lector-ID: <?= $machine->getLectorId() ?>
									</div>
								</div>
							<? } ?>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Farbe (Planung)</label>
								<div class="col-sm-9">
									<input id="machine_color" name="machine_color" value="<?= $machine->getColor() ?>"
										   class="form-control" size="6" maxlength="6">
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">int. Beschreibung</label>
								<div class="col-sm-9">
									<textarea name="machine_internaltext"
											  class="form-control"><?= $machine->getInternalText() ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Hersteller</label>
								<div class="col-sm-9">
									<textarea name="machine_hersteller"
											  class="form-control"><?= $machine->getHersteller() ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Baujahr</label>
								<div class="col-sm-9">
									<textarea name="machine_baujahr"
											  class="form-control"><?= $machine->getBaujahr() ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Machinen URL</label>
								<div class="col-sm-7">
										<input id="machine_url" name="machine_url" value="<?= $machine->getMachurl(); ?>" class="form-control">
								</div>
								<div class="col-sm-2">
									<span class="glyphicons glyphicons-home"></span>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Druckplatten</label>
								<div class="col-sm-4">
									<div class="input-group">
										<input id="machine_DPWidth" name="machine_DPWidth"
											   title="<?= $_LANG->get('Breite'); ?>"
											   value="<?= printPrice($machine->getDPWidth(), 4) ?>"
											   class="form-control">
										<span class="input-group-addon">mm</span>
									</div>
								</div>
								<label for="" class="col-sm-1 control-label">x</label>
								<div class="col-sm-4">
									<div class="input-group">
										<input id="machine_DPHeight" name="machine_DPHeight"
											   title="<?= $_LANG->get('Höhe'); ?>"
											   value="<?= printPrice($machine->getDPHeight(), 4) ?>"
											   class="form-control">
										<span class="input-group-addon">mm</span>
									</div>

								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Max Stapelhöhe</label>
								<div class="col-sm-4">
									<div class="input-group">
										<input id="machine_maxstacksize" name="machine_maxstacksize"
											   value="<?= $machine->getMaxstacksize(); ?>" class="form-control">
										<span class="input-group-addon">mm</span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						Maschinendaten
					</h3>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-md-6">
							<div id="tr_machine_colors_front" class="form-group">
								<label for="" class="col-sm-3 control-label">Farben Vorderseite</label>
								<div class="col-sm-2">
									<input name="colors_front" class="form-control"
										   value="<?= $machine->getColors_front() ?>">
								</div>
							</div>
							<div id="tr_machine_colors_back" class="form-group">
								<label for="" class="col-sm-3 control-label">Farben R&uuml;ckseite</label>
								<div class="col-sm-2">
									<input name="colors_back" value="<?= $machine->getColors_back() ?>"
										   class="form-control">
								</div>
							</div>
							<div id="tr_machine_paper_size" class="form-group">
								<label for="" class="col-sm-3 control-label">Max. Papiergr&ouml;&szlig;e</label>
								<div class="col-sm-3">
									<div class="input-group">
										<input name="paper_size_width" class="form-control"
											   title="<?= $_LANG->get('Breite'); ?>"
											   value="<?= $machine->getPaperSizeWidth() ?>">
										<span class="input-group-addon">mm</span>
									</div>
								</div>
								<label for="" class="col-sm-1 control-label">x</label>
								<div class="col-sm-3">
									<div class="input-group">
										<input name="paper_size_height" class="form-control"
											   title="<?= $_LANG->get('Höhe'); ?>"
											   value="<?= $machine->getPaperSizeHeight() ?>">
										<span class="input-group-addon">mm</span>
									</div>
								</div>
							</div>
							<div id="tr_machine_paper_min_size" class="form-group">
								<label for="" class="col-sm-3 control-label">Min. Papiergr&ouml;&szlig;e</label>
								<div class="col-sm-3">
									<div class="input-group">
										<input name="paper_size_min_width" class="form-control"
											   title="<?= $_LANG->get('Breite'); ?>"
											   value="<?= $machine->getPaperSizeMinWidth() ?>">
										<span class="input-group-addon">mm</span>
									</div>
								</div>
								<label for="" class="col-sm-1 control-label">x</label>
								<div class="col-sm-3">
									<div class="input-group">
										<input name="paper_size_min_height" class="form-control"
											   title="<?= $_LANG->get('Höhe'); ?>"
											   value="<?= $machine->getPaperSizeMinHeight() ?>">
										<span class="input-group-addon">mm</span>
									</div>
								</div>
							</div>
							<div id="tr_machine_border" class="form-group">
								<label for="" class="col-sm-3 control-label">nicht bedruckbarer Bereich</label>
								<div class="col-sm-7">
									<table class="table table-hover">
										<tr>
											<td  style="border-top: medium none;"></td>
											<td  style="border-top: medium none;">
												<div class="input-group">
													<input class="form-control" name="border_top"
														   value="<?= $machine->getBorder_top() ?>">
													<span class="input-group-addon">mm</span>
												</div>
											</td>

											<td  style="border-top: medium none;"></td>
										</tr>
										<tr>
											<td  style="border-top: medium none;">
												<div class="input-group">
													<input class="form-control" name="border_left"
														   value="<?= $machine->getBorder_left() ?>">
													<span class="input-group-addon">mm</span>
												</div>
											</td  style="border-top: medium none;">
											<td  style="border-top: medium none;"></td>
											<td  style="border-top: medium none;">
												<div class="input-group">
													<input class="form-control" name="border_right"
														   value="<?= $machine->getBorder_right() ?>">
													<span class="input-group-addon">mm</span>
												</div>
											</td>
										</tr>
										<tr>
											<td  style="border-top: medium none;"></td>
											<td  style="border-top: medium none;">
												<div class="input-group">
													<input class="form-control" name="border_bottom"
														   value="<?= $machine->getBorder_bottom() ?>">
													<span class="input-group-addon">mm</span>
												</div>
											</td>
											<td  style="border-top: medium none;"></td>
										</tr>
									</table>
								</div>
							</div>
							<div id="tr_machine_timebase" class="form-group">
								<label for="" class="col-sm-3 control-label">Grundzeit</label>
								<div class="col-sm-3">
									<div class="input-group">
										<input name="time_base" class="form-control"
											   value="<?= $machine->getTimeBase() ?>">
										<span class="input-group-addon"> min</span>
									</div>
								</div>
							</div>
							<div id="tr_machine_breaks" class="form-group"
								 style="display:<? if ($machine->getType() == Machine::TYPE_FOLDER) echo ""; else echo "none;" ?>">
								<label for="" class="col-sm-3 control-label">Max. Br&uuml;che</label>
								<div class="col-sm-3">
									<input name="breaks" value="<?= $machine->getBreaks() ?>" class="form-control">
								</div>
							</div>
							<div id="tr_machine_breaks_time" class="form-group"
								 style="display:<? if ($machine->getType() == Machine::TYPE_FOLDER) echo ""; else echo "none;" ?>">
								<label for="" class="col-sm-3 control-label">R&uuml;sten pro Bruch</label>
								<div class="col-sm-3">
									<div class="input-group">
										<input name="breaks_time" value="<?= $machine->getBreaks_time() ?>"
											   class="form-control">
										<span class="input-group-addon">min</span>
									</div>
								</div>
							</div>
							<div id="tr_machine_timeplatechange" class="form-group"
								 style="display:<? if ($machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) echo ""; else echo "none;" ?>">
								<label for="" class="col-sm-3 control-label">Zeit pro Plattenwechsel</label>
								<div class="col-sm-3">
									<div class="input-group">
										<input name="time_platechange" class="form-control"
											   value="<?= $machine->getTimePlatechange() ?>">
										<span class="input-group-addon">min</span>
									</div>
								</div>
							</div>
							<div id="tr_machine_timecolorchange" class="form-group"
								 style="display:<? if ($machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) echo ""; else echo "none;" ?>">
								<label for="" class="col-sm-3 control-label">Zeit pro Farbwechsel</label>
								<div class="col-sm-3">
									<div class="input-group">
										<input name="time_colorchange" class="form-control"
											   value="<?= $machine->getTimeColorchange() ?>">
										<span class="input-group-addon">min</span>
									</div>
								</div>
							</div>
							<div id="tr_machine_setup_stations" class="form-group"
								 style="display:<? if ($machine->getType() == Machine::TYPE_LAGENFALZ) echo ""; else echo "none;" ?>">
								<label for="" class="col-sm-3 control-label">Zeit Einrichtung Station</label>
								<div class="col-sm-3">
									<div class="input-group">
										<input name="time_setup_stations" class="form-control"
											   value="<?= $machine->getTimeSetupStations() ?>">
										<span class="input-group-addon">min</span>
									</div>
								</div>
							</div>
							<div id="tr_machine_anz_stations" class="form-group"
								 style="display:<? if ($machine->getType() == Machine::TYPE_LAGENFALZ) echo ""; else echo "none;" ?>">
								<label for="" class="col-sm-3 control-label">Anzahl Stationen</label>
								<div class="col-sm-3">
									<input name="anz_stations" class="form-control"
										   value="<?= $machine->getAnzStations() ?>">
								</div>
							</div>
							<div id="tr_machine_pages_per_station" class="form-group"
								 style="display:<? if ($machine->getType() == Machine::TYPE_LAGENFALZ) echo ""; else echo "none;" ?>">
								<label for="" class="col-sm-3 control-label">Seiten pro Station</label>
								<div class="col-sm-3">
									<input name="pages_per_station" class="form-control"
										   value="<?= $machine->getPagesPerStation() ?>">
								</div>
							</div>
							<div id="tr_machine_anz_signatures" class="form-group"
								 style="display:<? if ($machine->getType() == Machine::TYPE_SAMMELHEFTER) echo ""; else echo "none;" ?>">
								<label for="" class="col-sm-3 control-label">Anzahl Signaturen</label>
								<div class="col-sm-3">
									<input name="anz_signatures" class="form-control"
										   value="<?= $machine->getAnzSignatures() ?>">
								</div>
							</div>
							<div id="tr_machine_time_signatures" class="form-group"
								 style="display:<? if ($machine->getType() == Machine::TYPE_SAMMELHEFTER) echo ""; else echo "none;" ?>">
								<label for="" class="col-sm-3 control-label">R&uuml;stzeit pro Signatur</label>
								<div class="col-sm-3">
									<div class="input-group">
										<input name="time_signatures" class="form-control"
											   value="<?= $machine->getTimeSignatures() ?>">
										<span class="input-group-addon">min</span>
									</div>
								</div>
							</div>
							<div id="tr_machine_time_envelope" class="form-group"
								 style="display:<? if ($machine->getType() == Machine::TYPE_SAMMELHEFTER) echo ""; else echo "none;" ?>">
								<label for="" class="col-sm-3 control-label">R&uuml;stzeit Umschlaganleger</label>
								<div class="col-sm-3">
									<div class="input-group">
										<input name="time_envelope" class="form-control"
											   value="<?= $machine->getTimeEnvelope() ?>">
										<span class="input-group-addon">min</span>
									</div>
								</div>
							</div>
							<div id="tr_machine_time_trimmer" class="form-group"
								 style="display:<? if ($machine->getType() == Machine::TYPE_SAMMELHEFTER) echo ""; else echo "none;" ?>">
								<label for="" class="col-sm-3 control-label">R&uuml;stzeit Dreischneider</label>
								<div class="col-sm-3">
									<div class="input-group">
										<input name="time_trimmer" class="form-control"
											   value="<?= $machine->getTimeTrimmer() ?>">
										<span class="input-group-addon">min</span>
									</div>
								</div>
							</div>
							<div id="tr_machine_time_stacker" class="form-group"
								 style="display:<? if ($machine->getType() == Machine::TYPE_SAMMELHEFTER) echo ""; else echo "none;" ?>">
								<label for="" class="col-sm-3 control-label">R&uuml;stzeit Kreuzleger</label>
								<div class="col-sm-3">
									<div class="input-group">
										<input name="time_stacker" class="form-control"
											   value="<?= $machine->getTimeStacker() ?>">
										<span class="input-group-addon">min</span>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div id="tr_machine_chromaticity" class="form-group"
								 style="display:<? if ($machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET || $machine->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL) echo ""; else echo "none;" ?>">
								<div class="form-group" id="tr_machine_finish"
									 style="display:<? if ($machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET || $machine->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL) echo ""; else echo "none;" ?>">
									<label for="" class="col-sm-3 control-label">Option Lack verf&uuml;gbar</label>
									<div class="col-sm-2">
										<input name="finish" class="form-control" value="1"
											   type="checkbox" <? if ($machine->getFinish() == 1) echo "checked"; ?>>
									</div>
									<label for="" class="col-sm-3 control-label">Preis Lackplatte</label>
									<div class="col-sm-3">
										<div class="input-group">
											<input name="finish_plate_cost" class="form-control"
												   value="<?= printPrice($machine->getFinishPlateCost()) ?>">
											<span
												class="input-group-addon"><?= $_USER->getClient()->getCurrency() ?></span>
										</div>
									</div>
								</div>
								<? /* gln 13.05.2014, Umschlagen/Umstuelpen */ ?>
								<div class="form-group" id="tr_machine_umschl_umst"
									 style="display:<? if ($machine->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) echo ""; else echo "none;" ?>">
									<label for="" class="col-sm-3 control-label">Option
										umschlagen/umst&uuml;lpen</label>
									<div class="col-sm-2">
										<input name="umschl_umst" value="1" class="form-control"
											   type="checkbox" <? if ($machine->getUmschlUmst() == 1) echo "checked"; ?>>
									</div>
								</div>
								<? /* ascherer 19.08.14, Schneidemaschine */ ?>
								<div class="form-group" id="tr_machine_cut_price"
									 style="display:<? if ($machine->getType() == Machine::TYPE_CUTTER) echo ""; else echo "none;" ?>">
									<label for="" class="col-sm-3 control-label">Schnittpreis</label>
									<div class="col-sm-9">
										<input name="cut_price" class="form-control"
											   value="<?= printPrice($machine->getCutPrice(), 4) ?>">
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				  <div class="panel-heading">
						<h3 class="panel-title">
							Verfügbare Farbigkeit
						</h3>
				  </div>
				  <div class="panel-body">
					  <div class="table-responsive">
						  <?php

						  function chromaActive($chr, $machine)
						  {
							  foreach ($machine->getChromaticities() as $c) {
								  if ($chr->getId() == $c->getId())
									  return true;
							  }
							  return false;
						  }
						  $anzeige = true;
						  $chrs = Chromaticity::getAllChromaticities(Chromaticity::ORDER_NAME);

						  if ($chrs)
							  $chrs = break_array($chrs,3);
						  else
							  $chrs = [];
						  ?>
						  <table width="1200px">
							  <?php
							  if (count($chrs)>0) {
								  foreach ($chrs as $item) {
									  echo '<tr>';
									  foreach ($item as $chr) {
										  ?>
										  <td width="12%">
											  <?
											  echo '<input name="chroma_' . $chr->getId() . '" type="checkbox" value="1" ';
											  if (chromaActive($chr, $machine) === true) echo "checked";
											  echo '> '  . $chr->getName() . "<br>";
											  ?>
										  </td>
										  <td width="16,6%" name="anz_click2" style="display:<? if ($machine->getPriceBase() == Machine::PRICE_MINUTE && $machine->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL) echo ""; else echo "none;" ?>">
											  <?
											  if (chromaActive($chr, $machine) === true)
												  echo '<div class="col-sm-12"><div class="input-group"><span class="input-group-addon">CPC</span><input name="click_' . $chr->getId() . '" class="form-control" value="' . printPrice($machine->getCurrentClickprice($chr), 4) . '"><span class="input-group-addon">' . $_USER->getClient()->getCurrency(). '</span></div></div>';
											  else
												  echo '<div class="col-sm-12"><div class="input-group"><span class="input-group-addon">CPC</span><input name="click_' . $chr->getId() . '" class="form-control" value="' . printPrice(0, 4) . '"><span class="input-group-addon">' . $_USER->getClient()->getCurrency() . '</span></div></div>';
											  ?>
										  </td>
										  <?php
									  }
									  echo '</tr>';
									  $anzeige = false;
								  }
							  }
							  ?>
						  </table>
					  </div>
				  </div>
			</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						Laufleistung
					</h3>
				</div>
				<div class="panel-body">
					<div id="tr_machine_paperperhour">
						<input type="hidden" name="units_per_hour_counter" value="<? if (count($machine->getAllUnitsPerHour()) == 0) echo "1"; else echo count($machine->getAllUnitsPerHour()); ?>">
						 <div class="row">
							 <div class="col-md-6">
								 <div class="form-group">
									 <label for="" class="col-sm-2 control-label">Laufleistung</label>
									 <div class="col-sm-7">
										 <select name="unit" class="form-control">
											 <option
												 value="<?= Machine::UNIT_PERHOUR_BOGEN ?>" <? if ($machine->getUnit() == Machine::UNIT_PERHOUR_BOGEN) echo "selected" ?>><?= $_LANG->get('Bogen') ?></option>
											 <option
												 value="<?= Machine::UNIT_PERHOUR_AUFLAGE ?>" <? if ($machine->getUnit() == Machine::UNIT_PERHOUR_AUFLAGE) echo "selected" ?>><?= $_LANG->get('Auflagen') ?></option>
											 <option
												 value="<?= Machine::UNIT_PERHOUR_SEITEN ?>" <? if ($machine->getUnit() == Machine::UNIT_PERHOUR_SEITEN) echo "selected" ?>><?= $_LANG->get('Seiten') ?></option>
											 <option
												 value="<?= Machine::UNIT_PERHOUR_DRUCKPLATTEN ?>" <? if ($machine->getUnit() == Machine::UNIT_PERHOUR_DRUCKPLATTEN) echo "selected" ?>><?= $_LANG->get('Druckplatten') ?></option>
											 <option
												 value="<?= Machine::UNIT_PERHOUR_MM ?>" <? if ($machine->getUnit() == Machine::UNIT_PERHOUR_MM) echo "selected" ?>><?= $_LANG->get('mm') ?></option>
											 <option
												 value="<?= Machine::UNIT_PERHOUR_M ?>" <? if ($machine->getUnit() == Machine::UNIT_PERHOUR_M) echo "selected" ?>><?= $_LANG->get('Laufmeter') ?></option>
											 <option
												 value="<?= Machine::UNIT_PERHOUR_CUTS ?>" <? if ($machine->getUnit() == Machine::UNIT_PERHOUR_CUTS) echo "selected" ?>><?= $_LANG->get('Schnitte') ?></option>
										 </select>
									 </div>
									 <div class="col-sm-2">
										 <button type="button" class="btn btn-origin btn-success pointer icon-link" onclick="addUnitsPerHour()">
											 <span class="glyphicons glyphicons-plus"></span>
										 </button>
									 </div>
								 </div>
								 <br>
								 <div class="form-group" id="td_machine_paperperhour">
									 <? $x = 0;
									 $first = true;
									 foreach ($machine->getAllUnitsPerHour() as $unit) {
										 ?>
										 <label for="" class="col-sm-2 control-label">ab</label>
										 <div class="col-sm-3">
											 <div class="input-group">
												 <input name="units_from_<?= $x ?>" class="form-control" value="<?= printBigInt($unit["from"]) ?>">
												 <span class="input-group-addon">St.</span>
											 </div>
										 </div>
										 <label for="" class="col-sm-1 control-label">bis</label>
										 <div class="col-sm-3">
											 <div class="input-group">
												 <input name="units_per_hour_<?= $x ?>" class="form-control" value="<?= printBigInt($unit["per_hour"]) ?>">
												 <span class="input-group-addon">St.</span>
											 </div>
										 </div>
										 <div class="col-sm-3 form-text" style="font-size: 14px; height: 34px;">&nbsp;</div>
										 <? $first = false;
										 $x++;
									 } // foreach?>
									 <? if (count($machine->getAllUnitsPerHour()) == 0) { ?>
										 <div id="tr_machine_paperperhour">
											 <label for="" class="col-sm-2 control-label">ab</label>
											 <div class="col-sm-3">
												 <div class="input-group">
													 <input name="units_from_0" class="form-control" value="0">
													 <span class="input-group-addon">St.</span>
												 </div>
											 </div>
											 <label for="" class="col-sm-1 control-label">bis</label>
											 <div class="col-sm-3">
												 <div class="input-group">
													 <input name="units_per_hour_0" class="form-control" value="">
													 <span class="input-group-addon">St.</span>
												 </div>
											 </div>
											 <div class="col-sm-3 form-text" style="font-size: 14px; height: 34px;">&nbsp;</div>
										 </div>
									 <? } ?>
								 </div>
							 </div>
							 <div class="col-md-6">
								 <?
								 //     print_r($machine->getDifficulties());

								 $tmp_diff_highid = 0;
								 $tmp_diff_array = $machine->getDifficulties();
								 foreach ($tmp_diff_array as $tmp_diff) {
									 if ($tmp_diff["id"] > $tmp_diff_highid) {
										 $tmp_diff_highid = $tmp_diff["id"];
									 }
								 }
								 $tmp_diff_highid = $tmp_diff_highid + 1;

								 $tmp_diff_array[$tmp_diff_count]["id"] = $tmp_diff_highid;
								 $tmp_diff_array[$tmp_diff_count]["unit"] = 0;
								 $tmp_diff_array[$tmp_diff_count]["values"] = Array();
								 $tmp_diff_array[$tmp_diff_count]["percents"] = Array();
								 foreach ($tmp_diff_array as $difficulty){
								 ?>

								 <div id="tr_machine_difficulty">
									 <div class="form-group">
										 <label for="" class="col-sm-3 control-label">Erschwerniszuschlag</label>
										 <div class="col-sm-6">
											 <select name="machine_difficulty[<?php echo $difficulty["id"]; ?>][unit]" id="machine_difficulty_<?php echo $difficulty["id"]; ?>" class="form-control"
													 onchange="updateDifficultyFields(this.value, <?php echo $difficulty['unit']; ?>, <?php echo $difficulty['id']; ?>)">
												 <option value="">&lt; <?php echo $_LANG->get('Bitte w&auml;hlen'); ?>
													 &gt;</option>
												 <option
													 value="<?= Machine::DIFFICULTY_GAMMATUR ?>" <? if ($difficulty["unit"] == Machine::DIFFICULTY_GAMMATUR) echo "selected" ?>><?= $_LANG->get('Grammatur') ?></option>
												 <option
													 value="<?= Machine::DIFFICULTY_PAGES ?>" <? if ($difficulty["unit"] == Machine::DIFFICULTY_PAGES) echo "selected" ?>><?= $_LANG->get('Seiten') ?></option>
												 <option
													 value="<?= Machine::DIFFICULTY_STATIONS ?>" <? if ($difficulty["unit"] == Machine::DIFFICULTY_STATIONS) echo "selected" ?>><?= $_LANG->get('Stationen') ?></option>
												 <option
													 value="<?= Machine::DIFFICULTY_BRUECHE ?>" <? if ($difficulty["unit"] == Machine::DIFFICULTY_BRUECHE) echo "selected" ?>><?= $_LANG->get('Br&uuml;che') ?></option>
												 <option
													 value="<?= Machine::DIFFICULTY_UNITS_PER_HOUR ?>" <? if ($difficulty["unit"] == Machine::DIFFICULTY_UNITS_PER_HOUR) echo "selected" ?>><?= $_LANG->get('Laufleistung') ?></option>
											 </select>
										 </div>
										 <div class="col-sm-2">
											 <button type="button" class="btn btn-origin btn-success pointer icon-link" onclick="addDifficultyField(<?php echo $difficulty['id']; ?>)">
												 <span class="glyphicons glyphicons-plus"></span>
											 </button>
										 </div>
									 </div>
									 <br>
									 <div id="div_difficulty_fields_<?php echo $difficulty["id"]; ?>">
										 <? $x = 0;
										 if (count($difficulty["values"]) > 0) {
											 ?>
											 <div class="form-group" id="tr_difficulty_fields_<?php echo $difficulty["id"]; ?>">
												 <?php
												 foreach ($difficulty["values"] as $diff) {
													 ?>
													 <label for="" class="col-sm-3 control-label">&nbsp;</label>
													 <div class="col-sm-3">
														 <input class="form-control" name="machine_difficulty[<?php echo $difficulty["id"]; ?>][values][]" value="<?php echo $difficulty["values"][$x]; ?>">
													 </div>
													 <div class="col-sm-3">
														 <div class="input-group">
															 <input class="form-control" name="machine_difficulty[<?php echo $difficulty["id"]; ?>][percents][]" value="<?php echo $difficulty["percents"][$x]; ?>">
															 <span class="input-group-addon">%</span>
														 </div>
													 </div>
													 <div class="col-sm-3 form-text" style="font-size: 14px; height: 34px;">
														 &nbsp;</div>
													 <?php $x++;
												 } ?>
											 </div>
											 <?php
										 } else {
											 ?>
											 <div class="form-group" id="tr_difficulty_fields_<?php echo $difficulty["id"]; ?>">
												 <label for="" class="col-sm-3 control-label">&nbsp;</label>
												 <div class="col-sm-3">
													 <input class="form-control" name="machine_difficulty[<?php echo $difficulty["id"]; ?>][values][]">
												 </div>
												 <div class="col-sm-3">
													 <div class="input-group">
														 <input class="form-control" name="machine_difficulty[<?php echo $difficulty["id"]; ?>][percents][]">
														 <span class="input-group-addon">%</span>
													 </div>
												 </div>
												 <div class="col-sm-3 form-text" style="font-size: 14px; height: 34px;">&nbsp;</div>
											 </div>
											 <?php $x++;
										 }
										 ?>
									 </div>
									 <div class="form-group">
										 <label for="" class="col-sm-2 control-label">&nbsp;</label>
										 <div class="col-sm-6">
											 <input type="hidden" name="machine_difficulty[<?php echo $difficulty["id"]; ?>][id]" value="<?php echo $difficulty["id"]; ?>">
										 </div>
									 </div>
									 <div class="form-group">
										 <div class="col-sm-12">
											 <input name="difficulty_counter_<?php echo $difficulty["id"]; ?>"
													id="difficulty_counter_<?php echo $difficulty["id"]; ?>"
													value="<?php echo $x; ?>" type="hidden">
										 </div>
									 </div>
								 </div>
									 <?
								 }
								 ?>
							 </div>
						 </div>
						<br>
					</div>
				</div>
			</div>

			<br>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						Arbeiter
					</h3>
				</div>
				<div class="panel-body">
					<?php
					$all_users = User::getAllUser();
					$qid_arr = Array();
					foreach ($machine->getQualified_users() as $qid) {
						$qid_arr[] = $qid->getId();
					}
					$qi = 0;
					foreach ($all_users as $qusr) {
						if ($qi == 0) echo '';
						?>
							<div class="col-sm-2">
								<?php echo $qusr->getNameAsLine(); ?>
							</div>
							<div class="col-sm-1">
								<input type="checkbox" name="qusr[]" <?php if (in_array($qusr->getId(), $qid_arr)) echo ' checked '; ?> value="<?php echo $qusr->getId(); ?>"/>
							</div>
						<?php if ($qi == 4) {
							$qi = -1;
						} ?>
						<?php $qi++;
					} ?>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						Laufzeiten
					</h3>
				</div>
				<?php
				unset($whours);
				unset($times);
				$times = $machine->getRunninghours();
				$daynames = Array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag");
				?>
				<div class="table-responsive">
					<table class="table table-hover">
						<?php
						for ($i = 0; $i < 7; $i++) {
							?>
							<tr>
								<td  width="10%"><?= $_LANG->get($daynames[$i]); ?></td>
								<td>
									<?php
									$count = 0;
									if (count($times[$i]) > 0) {
										foreach ($times[$i] as $whours) {
											?>
											<div class="form-group">
												<div class="col-sm-2">
													<input id="wotime_<?php echo $i; ?>_<?php echo $count; ?>_start"
														   type="text" class="form-control"
														   value="<?php echo date("H:i", $whours["start"]); ?>"
														   name="wotime[<?php echo $i; ?>][<?php echo $count; ?>][start]">
												</div>
												<label for="" style="text-align: center;" class="col-sm-1 control-label">bis</label>
												<div class="col-sm-2">
													<input id="wotime_<?php echo $i; ?>_<?php echo $count; ?>_end"
														   type="text" class="form-control"
														   value="<?php echo date("H:i", $whours["end"]); ?>"
														   name="wotime[<?php echo $i; ?>][<?php echo $count; ?>][end]">
												</div>
											</div>
											<script language="JavaScript">
												$(document).ready(function () {
													var startTimeTextBox = $('#wotime_<?php echo $i;?>_<?php echo $count;?>_start');
													var endTimeTextBox = $('#wotime_<?php echo $i;?>_<?php echo $count;?>_end');

													$.timepicker.timeRange(
														startTimeTextBox,
														endTimeTextBox,
														{
															minInterval: (1000 * 900), // 0,25hr
															timeFormat: 'HH:mm',
															start: {}, // start picker options
															end: {} // end picker options
														}
													);
												});
											</script>
											<?php
											$count++;
										}
									}
									?>
									<div class="form-group">
										<div class="col-sm-2">
											<input id="wotime_<?php echo $i; ?>_<?php echo $count; ?>_start" type="text"
												   class="form-control" value=""
												   name="wotime[<?php echo $i; ?>][<?php echo $count; ?>][start]">
										</div>
										<label for="" style="text-align: center;" class="col-sm-1 control-label">bis</label>
										<div class="col-sm-2">
											<input id="wotime_<?php echo $i; ?>_<?php echo $count; ?>_end" type="text"
												   class="form-control" value=""
												   name="wotime[<?php echo $i; ?>][<?php echo $count; ?>][end]">
										</div>
									</div>
									<script language="JavaScript">
										$(document).ready(function () {
											var startTimeTextBox = $('#wotime_<?php echo $i;?>_<?php echo $count;?>_start');
											var endTimeTextBox = $('#wotime_<?php echo $i;?>_<?php echo $count;?>_end');

											$.timepicker.timeRange(
												startTimeTextBox,
												endTimeTextBox,
												{
													minInterval: (1000 * 900), // 0,25hr
													timeFormat: 'HH:mm',
													start: {}, // start picker options
													end: {} // end picker options
												}
											);
										});
									</script>
								</td>
							</tr>
							<?php
						}
						?>
					</table>
				</div>
			</div>
		</div>

		<?php if ($machine->getId() > 0) { ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Sperrzeiten</h3>
				</div>
				<div class="table-responsive">
					<table class="table table-hover">
						<tr>
							<td></td>
							<td>
								 <div class="row">
									 <div class="col-md-3">
										 <label for="" class="control-label">Sperrzeit Start</label>
									 </div>
									 <div class="col-md-3">
										 <label for="" class="control-label">Sperrzeit Ende</label>
									 </div>
								 </div>
							</td>
						</tr>
						<?php
						$all_locks = MachineLock::getAllMachineLocksForMachine($machine->getId());
						foreach ($all_locks as $lock) {
							if ($lock->getStart() >= time() || $lock->getStop() >= time()) {
								?>
								<tr>
									<td><?php echo date("d.m.Y H:i", $lock->getStart()); ?> -</td>
									<td><?php echo date("d.m.Y H:i", $lock->getStop()); ?>
										<a href="index.php?page=<?= $_REQUEST['page'] ?>&exec=edit&id=<?= $machine->getId() ?>&dellock=<?= $lock->getId() ?>"><img
												src="images/icons/cross-script.png"/></a></td>
								</tr>
							<?php }
						} ?>
						<tr>
							<td width="10%"><label for="" class="control-label">neue Sperrzeit:</label></td>
							<td>
								<div class="form-group">
									<div class="col-sm-2">
										<input type="text" id="lock_start" name="lock_start"
											   class="form-control format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
											   onfocus="markfield(this,0)" onblur="markfield(this,1)"/>
									</div>
									<label for="" style="text-align: center;"  class="col-sm-1 control-label">bis</label>
									<div class="col-sm-2">
										<input type="text" id="lock_stop" name="lock_stop"
											   class="form-control format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
											   onfocus="markfield(this,0)" onblur="markfield(this,1)"/>
									</div>
								</div>
							</td>
						</tr>
					</table>
				</div>
			</div>
		<?php } ?>
</form>