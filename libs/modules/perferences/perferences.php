<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

require_once('perferences.class.php');
require_once 'libs/modules/organizer/event_holiday.class.php';

$perf = new Perferences();

if ($_REQUEST["exec"] == "mergeArticles"){
	Article::mergePriceSeperation();
}

if ($_REQUEST["exec"] == "save")
{
	if($_FILES){
		if(move_uploaded_file($_FILES["pdf_back"]["tmp_name"], "./docs/templates/briefbogen.jpg")){
// 			$savemsg = getSaveMessage(true);
		} else {
// 			$savemsg = getSaveMessage(false);
		}
	}
	
	$tmp_formats_raw = Array();
	$all_formatsraw = (int)$_REQUEST["count_formatsraw"];
	for ($i=0 ; $i <= $all_formatsraw ; $i++){
	    $f_width = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["formatsraw_width_".$i])));
	    $f_height = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["formatsraw_height_".$i])));
	    if ($f_width > 0 && $f_height > 0){
	        $tmp_formats_raw[] = Array("id" => $i, "width" => $f_width, "height" => $f_height);
	    }
	}
	$perf->setFormats_raw($tmp_formats_raw);
	
	$perf->setZuschussProDP(str_replace(",",".",str_replace(".","",$_REQUEST["zuschussprodp"])));
	$perf->setZuschussPercent(str_replace(",",".",str_replace(".","",$_REQUEST["zuschusspercent"])));
	$perf->setCalc_detailed_printpreview($_REQUEST["calc_detailed_printpreview"]);
	$perf->setDt_show_default((int)$_REQUEST["datatables_showelements"]);
	$perf->setDt_state_save((bool)$_REQUEST["datatables_statesave"]);
	$savemsg = getSaveMessage($perf->save());
	
	HolidayEvent::removeAll();
    for ($i = 0; $i < count($_REQUEST["holiday"]["id"]);$i++)
    {
        $holiday = new HolidayEvent($_REQUEST["holiday"]["id"][$i]);
        $holiday->setBegin(strtotime($_REQUEST["holiday"]["start"][$i]));
        $holiday->setEnd(strtotime($_REQUEST["holiday"]["end"][$i]));
        $holiday->setColor($_REQUEST["holiday"]["color"][$i]);
        $holiday->setTitle($_REQUEST["holiday"]["title"][$i]);
        $holiday->save();
    }
}

$holidays = HolidayEvent::getAll();

?>

<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>

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
	
<script>
	$(function() {
		$( "#tabs" ).tabs({ selected: 0 });
	});
</script>
<script type="text/javascript">
function load_image(id,ext)
{
   if(validateExtension(ext) == false)
   {
      alert("Es wird nur das JPG Format unterstützt");
      document.getElementById("pdf_back").innerHTML = "";
      document.getElementById("pdf_back").value = "";
      document.getElementById("pdf_back").focus();
      return;
   }
}

function validateExtension(v)
{
      var allowedExtensions = new Array("jpg","JPG");
      for(var ct=0;ct<allowedExtensions.length;ct++)
      {
          sample = v.lastIndexOf(allowedExtensions[ct]);
          if(sample != -1){return true;}
      }
      return false;
}

function addFormatRawRow()
{
	var obj = document.getElementById('table-formatsraw');
	var count = parseInt(document.getElementById('count_formatsraw').value) + 1;

	var insert = '<tr><td class="content_row_clear">'+count+'</td>';
	insert += '<td class="content_row_clear">';
	insert += '<input name="formatsraw_width_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 50px">&nbsp;x&nbsp;';
	insert += '</td>';
	insert += '<td class="content_row_clear">';
	insert += '<input name="formatsraw_height_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 50px"> &nbsp;&nbsp;&nbsp;<span class="glyphicons glyphicons-remove pointer" onclick="deleteFormatRawRow(this)"></span>';
	insert += '</td></tr>';
	
	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('count_formatsraw').value = count;
}

function addHolidayRow()
{
	var obj = document.getElementById('holidays');

	var insert = '<tr>';
	insert += '<td class="content_row_clear" valign="top">';
    insert += '#0';
    insert += '<input type="hidden" name="holiday[id][]" value="0"/>';
    insert += '</td>';
	insert += '<td class="content_row_clear" valign="top">';
	insert += '<input type="text" name="holiday[title][]" value=""/>';
    insert += '</td>';
	insert += '<td class="content_row_clear" valign="top">';
	insert += '<input type="text" class="cal" name="holiday[start][]" value=""/>';
    insert += '</td>';
	insert += '<td class="content_row_clear" valign="top">';
	insert += '<input type="text" class="cal" name="holiday[end][]" value=""/>';
    insert += '</td>';
	insert += '<td class="content_row_clear" valign="top">';
	insert += '<input type="text" name="holiday[color][]" value=""/>';
    insert += '</td></tr>';
	
	obj.insertAdjacentHTML("BeforeEnd", insert);
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
}

function deleteFormatRawRow(obj)
{
	$(obj).parents("tr").remove();
}
</script>

<table width="100%">
   <tr>
      <td width="200" class="content_header"><span class="glyphicons glyphicons-cogwheel"></span> <?=$_LANG->get('Einstellungen')?></td>
	  <td align="right"><?=$savemsg?></td>
   </tr>
</table>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#perf_form').submit();",'glyphicon-floppy-disk');

echo $quickmove->generate();
// end of Quickmove generation ?>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" enctype="multipart/form-data" name="perf_form" id="perf_form">
<input type="hidden" name="exec" value="save">
<div class="box1">
	<div id="tabs">
		<ul>
			<li><a href="#tabs-0"><? echo $_LANG->get('Allgemein');?></a></li>
			<li><a href="#tabs-1"><? echo $_LANG->get('Kalkulation');?></a></li>
			<li><a href="#tabs-3"><? echo $_LANG->get('Roh-Formate');?></a></li>
			<li><a href="#tabs-4"><? echo $_LANG->get('Datatables');?></a></li>
			<li><a href="#tabs-5"><? echo $_LANG->get('Kalender');?></a></li>
			<li><a href="#tabs-6"><? echo $_LANG->get('Update Funktionen');?></a></li>
		</ul>

		<div id="tabs-0">
                
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
               <colgroup>
                  <col width="180">
                  <col>
               </colgroup>
               <tr>
                  <td class="content_row_header" valign="top">Briefbogen:</td>
                  <td class="content_row_clear">
                     <input type="file" name="pdf_back" onChange="load_image(this.id,this.value)" id="pdf_back" /></br>
            		 vorhandenen in neuem Fenster <a target="_blank" href="docs/templates/briefbogen.jpg"><b>öffnen</b></a></br></br>
            		 Möglichst hohe Auflösung bei geringer Dateigröße </br>
            		 (300dpi / 100-300KB) ist empfolen!
                  </td>
               </tr>
            </table>
       </div>
       <div id="tabs-1">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
               <colgroup>
                  <col width="180">
                  <col>
               </colgroup>
               <tr>
                  <td class="content_row_header" valign="top">Zuschuss pro DP:</td>
                  <td class="content_row_clear">
                     <input type="text" name="zuschussprodp" id="zuschussprodp" value="<?=str_replace(".",",",$perf->getZuschussProDP());?>" />
                  </td>
               </tr>
				<tr>
					<td class="content_row_header" valign="top">Fortdruckzuschuss & Weiterverarbeitung:</td>
					<td class="content_row_clear">
						<input type="text" name="zuschusspercent" id="zuschusspercent" value="<?=str_replace(".",",",$perf->getZuschussPercent());?>" /> %
					</td>
				</tr>
               <tr>
                  <td class="content_row_header" valign="top">Detailierte Druckbogenvorschau:</td>
                  <td class="content_row_clear">
                     <input type="checkbox" name="calc_detailed_printpreview" id="calc_detailed_printpreview" value="1" <? if ($perf->getCalc_detailed_printpreview()) echo "checked"; ?>/>
                  </td>
               </tr>
            </table>
       </div>
       <div id="tabs-3">
            <?php 
            $formats_raw = $perf->getFormats_raw();
            ?>
   			<input 	type="hidden" name="count_formatsraw" id="count_formatsraw" 
				value="<? if(count($formats_raw) > 0) echo count($formats_raw); else echo "1";?>">
            <table border="0" cellpadding="0" cellspacing="0" id="table-formatsraw">
				<colgroup>
		        	<col width="40">
		        	<col width="100">
		        	<col width="120">
		    	</colgroup>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Nr')?></td>
					<td class="content_row_header"><?=$_LANG->get('Breite')?></td>
					<td class="content_row_header"><?=$_LANG->get('Höhe')?></td>
				</tr>
				<?
				$x = count($formats_raw);
				if ($x < 1){
					$x++;
				}
				for ($y=0; $y < $x ; $y++){ ?>
					<tr>
						<td class="content_row_clear">
						<?=$y+1?>
						</td>
						<td class="content_row_clear">
							<input 	name="formatsraw_width_<?=$y?>" class="text" type="text"
									value ="<?=printPrice($formats_raw[$y]["width"]);?>" style="width: 50px">&nbsp;x&nbsp;
						</td>
						<td class="content_row_clear">
							<input 	name="formatsraw_height_<?=$y?>" class="text" type="text"
									value ="<?=printPrice($formats_raw[$y]["height"]);?>" style="width: 50px">
							&nbsp;&nbsp;&nbsp;<span class="glyphicons glyphicons-remove pointer"onclick="deleteFormatRawRow(this)"></span>&nbsp;
							<? if ($y == $x-1){ //Plus-Knopf nur beim letzten anzeigen
								echo '<span class="glyphicons glyphicons-plus pointer" onclick="addFormatRawRow()"></span>';
							}?> 
						</td>
					</tr>
				<? } ?>
			</table>
       </div>
       <div id="tabs-4">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
               <colgroup>
                  <col width="180">
                  <col>
               </colgroup>
               <tr>
                  <td class="content_row_header" valign="top">Def. Anzahl Elemente:</td>
                  <td class="content_row_clear">
                     <input type="text" name="datatables_showelements" id="datatables_showelements" value="<?php echo $perf->getDt_show_default();?>"/></br>
                  </td>
               </tr>
               <tr>
                  <td class="content_row_header" valign="top">Speichere Status</td>
                  <td class="content_row_clear">
                     <input type="checkbox" name="datatables_statesave" id="datatables_statesave" <?php if($perf->getDt_state_save()) echo " checked ";?> /></br>
                  </td>
               </tr>
            </table>
       </div>
       <div id="tabs-5">
            Feiertage: <span class="glyphicons glyphicons-plus pointer" onclick="addHolidayRow()"></span>
            <table width="100%" border="0" cellpadding="0" cellspacing="0" id="holidays">
               <colgroup>
                  <col>
                  <col>
                  <col>
                  <col>
                  <col>
               </colgroup>
               <tr>
                <td class="content_row_clear" valign="top">ID</td>
                <td class="content_row_clear" valign="top">Titel</td>
                <td class="content_row_clear" valign="top">Startdatum</td>
                <td class="content_row_clear" valign="top">Enddatum</td>
                <td class="content_row_clear" valign="top">Hex-Farbe</td>
               </tr>
               <?php if (count($holidays)>0){
               foreach ($holidays as $holiday){?>
               <tr>
                  <td class="content_row_clear" valign="top">
                     #<?php echo $holiday->getId();?>
                     <input type="hidden" name="holiday[id][]" value="<?php echo $holiday->getId();?>"/>
                  </td>
                  <td class="content_row_clear" valign="top">
                     <input type="text" name="holiday[title][]" value="<?php echo $holiday->getTitle();?>"/>
                  </td>
                  <td class="content_row_clear" valign="top">
                     <input type="text" class="cal" name="holiday[start][]" value="<?php echo date("d.m.Y H:i",$holiday->getBegin());?>"/>
                  </td>
                  <td class="content_row_clear" valign="top">
                     <input type="text" class="cal" name="holiday[end][]" value="<?php echo date("d.m.Y H:i",$holiday->getEnd());?>"/>
                  </td>
                  <td class="content_row_clear" valign="top">
                     <input type="text" name="holiday[color][]" value="<?php echo $holiday->getColor();?>"/><span class="glyphicons glyphicons-remove pointer"onclick="$(this).parent().parent().remove();"></span>
                  </td>
               </tr>
               <?php }}?>
            </table>
       </div>
		<div id="tabs-6">
			Spezielle Update Funktionen:<br>
			<button class="btn-danger" onclick="location.href='index.php?page=libs/modules/perferences/perferences.php&exec=mergeArticles';">Artikel Preisstaffeln zusammenführen</button>
		</div>

</form>