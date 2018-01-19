﻿<?php
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
	if($_FILES["pdf_back"]){
		move_uploaded_file($_FILES["pdf_back"]["tmp_name"], "./docs/templates/briefbogen.jpg");
	}
	if($_FILES["page_logo"]){
		move_uploaded_file($_FILES["page_logo"]["tmp_name"], "./images/page_logo.jpg");
	}
	if($_FILES["shop_logo"]){
		move_uploaded_file($_FILES["shop_logo"]["tmp_name"], "./images/shop_logo.jpg");
	}
	if($_FILES["login_logo"]){
		move_uploaded_file($_FILES["login_logo"]["tmp_name"], "./images/login_logo.jpg");
	}
	if($_FILES["shop_bg"]){
		move_uploaded_file($_FILES["shop_bg"]["tmp_name"], "./images/shop_bg.jpg");
	}
	if ($_REQUEST["shop_bg_remove"] == 1){
		unlink("./images/shop_bg.jpg");
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
	$perf->setInkusage(str_replace(",",".",str_replace(".","",$_REQUEST["inkusage"])));
	$perf->setFinishingusage(str_replace(",",".",str_replace(".","",$_REQUEST["finishingusage"])));
	$perf->setCalc_detailed_printpreview($_REQUEST["calc_detailed_printpreview"]);
	$perf->setDt_show_default((int)$_REQUEST["datatables_showelements"]);
	$perf->setDt_state_save((bool)$_REQUEST["datatables_statesave"]);

	$perf->setCalcPercentMaterial(str_replace(",",".",str_replace(".","",$_REQUEST["calc_percent_material"])));
	$perf->setCalcPercentProcessing(str_replace(",",".",str_replace(".","",$_REQUEST["calc_percent_processing"])));
	$perf->setCalcMargin(str_replace(",",".",str_replace(".","",$_REQUEST["calc_margin"])));
	$perf->setCalcDiscount(str_replace(",",".",str_replace(".","",$_REQUEST["calc_discount"])));

	if ($_REQUEST["calc_material_processing_charge"] == 1){
		$perf->setCalcMaterialProcessingCharge(1);
	} else {
		$perf->setCalcMaterialProcessingCharge(0);
	}

	if ($_REQUEST["deactivate_manual_articles"] == 1){
		$perf->setDeactivateManualArticles(1);
	} else {
		$perf->setDeactivateManualArticles(0);
	}
	if ($_REQUEST["decativate_manual_delivcost"] == 1){
		$perf->setDecativateManualDelivcost(1);
	} else {
		$perf->setDecativateManualDelivcost(0);
	}
	if ($_REQUEST["toggle_useadrmail"] == 1){
		$perf->setToggleUseadrmail(1);
	} else {
		$perf->setToggleUseadrmail(0);
	}

	$perf->setMailSender($_REQUEST["mail_sender"]);
	$perf->setSmtpAddress($_REQUEST["smtp_address"]);
	$perf->setSmtpHost($_REQUEST["smtp_host"]);
	$perf->setSmtpPort($_REQUEST["smtp_port"]);
	$perf->setSmtpUser($_REQUEST["smtp_user"]);
	$perf->setSmtpPassword($_REQUEST["smtp_password"]);
	$perf->setImapAddress($_REQUEST["imap_address"]);
	$perf->setImapHost($_REQUEST["imap_host"]);
	$perf->setImapPort($_REQUEST["imap_port"]);
	$perf->setImapUser($_REQUEST["imap_user"]);
	$perf->setImapPassword($_REQUEST["imap_password"]);
	$perf->setSystemSignature($_REQUEST["system_signature"]);
	if ($_REQUEST["imap_ssl"] == 1){
		$perf->setImapSsl(1);
	} else {
		$perf->setImapSsl(0);
	}
	if ($_REQUEST["imap_tls"] == 1){
		$perf->setImapTls(1);
	} else {
		$perf->setImapTls(0);
	}
	if ($_REQUEST["smtp_ssl"] == 1){
		$perf->setSmtpSsl(1);
	} else {
		$perf->setSmtpSsl(0);
	}
	if ($_REQUEST["smtp_tls"] == 1){
		$perf->setSmtpTls(1);
	} else {
		$perf->setSmtpTls(0);
	}

	$perf->setMailtextConfirmation(trim(addslashes($_REQUEST["mailtext_confirmation"])));
	$perf->setMailtextSenddocs(trim(addslashes($_REQUEST["mailtext_senddocs"])));
	$perf->setMailtextSenddocsTitle(trim(addslashes($_REQUEST["mailtext_senddocs_title"])));
	$perf->setDefaultRevenue(new RevenueaccountCategory($_REQUEST["revenueaccount"]));
	$perf->setDefaultCostobject(new CostObject($_REQUEST["costobject"]));
	$perf->setMinmargin(str_replace(",",".",str_replace(".","",$_REQUEST["minmargin"])));

	$perf->setSaxoapikey($_REQUEST["saxoapikey"]);
	$perf->setSaxocp($_REQUEST["saxo_contactperson"]);
	$perf->setSaxobc($_REQUEST["saxo_customer"]);

	$perf->setSaasdoCustomerId($_REQUEST["SaasdoCustomerId"]);

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


<script>
	$(function() {
		$( "#search_saxobc" ).autocomplete({
			delay: 0,
			source: 'libs/modules/tickets/ticket.ajax.php?ajax_action=search_customer_and_cp',
			minLength: 2,
			dataType: "json",
			select: function(event, ui) {
				$('#saxo_customer').val(ui.item.bid);
				$('#saxo_contactperson').val(ui.item.cid);
			}
		});
	});
</script>
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
		CKEDITOR.replace( 'system_signature' );
	});
</script>
<script type="text/javascript">
function load_image(id,ext)
{
   if(validateExtension(ext) == false)
   {
      alert("Es wird nur das JPG Format unterstützt");
      document.getElementById(id).innerHTML = "";
      document.getElementById(id).value = "";
      document.getElementById(id).focus();
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

	var insert = '<tr><td>'+count+'</td>';
	insert += '<td>';
	insert += '<input name="formatsraw_width_'+count+'" class="form-control" type="text"';
	insert += 'value ="">';
	insert += '</td>';
	insert += '<td style="text-align:center">x</td>';
	insert += '<td>';
	insert += '<input name="formatsraw_height_'+count+'" class="form-control" type="text"';
	insert += 'value ="">';
	insert += '</td><td>';
	insert += '<span class="glyphicons glyphicons-remove pointer" onclick="deleteFormatRawRow(this)"></span>';
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
	insert += '<input type="text" class="form-control" name="holiday[title][]" value=""/>';
    insert += '</td>';
	insert += '<td class="content_row_clear" valign="top">';
	insert += '<input type="text" class="cal form-control" name="holiday[start][]" value=""/>';
    insert += '</td>';
	insert += '<td class="content_row_clear" valign="top">';
	insert += '<input type="text" class="cal form-control" name="holiday[end][]" value=""/>';
    insert += '</td>';
	insert += '<td class="content_row_clear" valign="top">';
	insert += '<input type="text" class="form-control" name="holiday[color][]" value=""/>';
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

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#perf_form').submit();",'glyphicon-floppy-disk');

echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
			Einstellungen
				<span class="pull-right">
					<?= $savemsg ?>
				</span>
		</h3>
	</div>
	<div class="panel-body">
		<form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" class="form-horizontal" enctype="multipart/form-data"
			  name="perf_form" id="perf_form">
			<input type="hidden" name="exec" value="save">

			<div id="tabs">
				<ul>
					<li><a href="#tabs-0"><? echo $_LANG->get('Allgemein'); ?></a></li>
					<li><a href="#tabs-1"><? echo $_LANG->get('Kalkulation'); ?></a></li>
					<li><a href="#tabs-2"><? echo $_LANG->get('System Mail'); ?></a></li>
					<li><a href="#tabs-3"><? echo $_LANG->get('Roh-Formate'); ?></a></li>
					<li><a href="#tabs-4"><? echo $_LANG->get('Datatables'); ?></a></li>
					<li><a href="#tabs-5"><? echo $_LANG->get('Kalender'); ?></a></li>
					<li><a href="#tabs-6"><? echo $_LANG->get('Update Funktionen'); ?></a></li>
					<li><a href="#tabs-7"><? echo $_LANG->get('Schalter'); ?></a></li>
					<li><a href="#tabs-8"><? echo $_LANG->get('Texte'); ?></a></li>
					<li><a href="#tabs-9"><? echo $_LANG->get('Buchhaltung'); ?></a></li>
					<li><a href="#tabs-10"><? echo $_LANG->get('Schnittstellen'); ?></a></li>
				</ul>

				<div id="tabs-0">
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Briefbogen</label>
						<div class="col-sm-10">
							<input type="file" name="pdf_back" onChange="load_image(this.id,this.value)" id="pdf_back"/>
						</div>
					</div>
					<br>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label"></label>
						<div class="col-sm-10">
							vorhandenen in neuem Fenster <a target="_blank"	href="docs/templates/briefbogen.jpg"><b>öffnen</b></a></br></br>
							Möglichst hohe Auflösung bei geringer Dateigröße </br>
							(300dpi / 100-300KB) ist empfolen!
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Seiten Logo</label>
						<div class="col-sm-10">
							<input type="file" name="page_logo" id="page_logo"/>
						</div>
					</div>
					<br>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label"></label>
						<div class="col-sm-10">
							vorhandenen in neuem Fenster <a target="_blank"	href="images/page_logo.jpg"><b>öffnen</b></a></br></br>
							Bild wird auf eine Höhe von 50px skaliert! </br>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Shop Logo</label>
						<div class="col-sm-10">
							<input type="file" name="shop_logo" id="shop_logo"/>
						</div>
					</div>
					<br>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label"></label>
						<div class="col-sm-10">
							vorhandenen in neuem Fenster <a target="_blank"	href="images/shop_logo.jpg"><b>öffnen</b></a></br></br>
							Bild wird auf eine Höhe von 50px skaliert! </br>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Login Logo</label>
						<div class="col-sm-10">
							<input type="file" name="login_logo" id="login_logo"/>
						</div>
					</div>
					<br>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label"></label>
						<div class="col-sm-10">
							vorhandenen in neuem Fenster <a target="_blank"	href="images/login_logo.jpg"><b>öffnen</b></a></br></br>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Shop Hintergrund</label>
						<div class="col-sm-10">
							<input type="file" name="shop_bg" id="shop_bg"/>
						</div>
					</div>
					<br>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label"></label>
						<div class="col-sm-10">
							vorhandenen in neuem Fenster <a target="_blank"	href="images/shop_bg.jpg"><b>öffnen</b></a></br></br>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Hintergrundbild entfernen</label>
						<div class="col-sm-10">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="shop_bg_remove" id="shop_bg_remove" value="1">
								</label>
							</div>
						</div>
					</div>
				</div>
				<div id="tabs-1">
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Zuschuss pro DP:</label>
						<div class="col-sm-4">
							<input type="text" name="zuschussprodp" id="zuschussprodp" class="form-control" value="<?= str_replace(".", ",", $perf->getZuschussProDP()); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Fortdruckzuschuss & Weiterverarbeitung:</label>
						<div class="col-sm-4">
							<div class="input-group">
								<input type="text" name="zuschusspercent" class="form-control" id="zuschusspercent" value="<?= str_replace(".", ",", $perf->getZuschussPercent()); ?>"/>
								<span class="input-group-addon">%</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Farbverbrauch g/m²:</label>
						<div class="col-sm-4">
							<div class="input-group">
								<input type="text" name="inkusage" class="form-control" id="inkusage" value="<?=str_replace(".", ",", $perf->getInkusage()); ?>"/>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Lackverbrauch g/m²:</label>
						<div class="col-sm-4">
							<div class="input-group">
								<input type="text" name="finishingusage" class="form-control" id="finishingusage" value="<?=str_replace(".", ",", $perf->getFinishingusage()); ?>"/>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Detailierte Druckbogenvorschau:</label>
						<div class="col-sm-1">
							<input type="checkbox" name="calc_detailed_printpreview" class="form-control" id="calc_detailed_printpreview"
								   value="1" <? if ($perf->getCalc_detailed_printpreview()) echo "checked"; ?>/>
						</div>
					</div>
					<hr>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Manueller Auf-/Abschlag Material und Fertigung:</label>
						<div class="col-sm-1">
							<input type="checkbox" name="calc_material_processing_charge" class="form-control" id="calc_material_processing_charge"
								   value="1" <? if ($perf->getCalcMaterialProcessingCharge()) echo "checked"; ?>/>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Maximaler Aufschlag Material:</label>
						<div class="col-sm-4">
							<div class="input-group">
								<input type="text" name="calc_percent_material" class="form-control" id="calc_percent_material" value="<?=str_replace(".", ",", $perf->getCalcPercentMaterial()); ?>"/>
								<span class="input-group-addon">%</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Maximaler Aufschlag Fertigung:</label>
						<div class="col-sm-4">
							<div class="input-group">
								<input type="text" name="calc_percent_processing" class="form-control" id="calc_percent_processing" value="<?=str_replace(".", ",", $perf->getCalcPercentProcessing()); ?>"/>
								<span class="input-group-addon">%</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Maximale Marge:</label>
						<div class="col-sm-4">
							<div class="input-group">
								<input type="text" name="calc_margin" class="form-control" id="calc_margin" value="<?=str_replace(".", ",", $perf->getCalcMargin()); ?>"/>
								<span class="input-group-addon">%</span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Maximaler Rabatt:</label>
						<div class="col-sm-4">
							<div class="input-group">
								<input type="text" name="calc_discount" class="form-control" id="calc_discount" value="<?=str_replace(".", ",", $perf->getCalcDiscount()); ?>"/>
								<span class="input-group-addon">%</span>
							</div>
						</div>
					</div>
				</div>
				<div id="tabs-2">
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">eMail-Absender:</label>
						<div class="col-sm-4">
							<input type="text" name="mail_sender" id="mail_sender" class="form-control" value="<?=$perf->getMailSender(); ?>" placeholder="Manfred Mustermann"/>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">SMTP eMail-Adresse:</label>
						<div class="col-sm-4">
							<input type="text" name="smtp_address" id="smtp_address" class="form-control" value="<?=$perf->getSmtpAddress(); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">SMTP Server:</label>
						<div class="col-sm-4">
							<input type="text" name="smtp_host" id="smtp_host" class="form-control" value="<?=$perf->getSmtpHost(); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">SMTP Port:</label>
						<div class="col-sm-4">
							<input type="text" name="smtp_port" id="smtp_port" class="form-control" value="<?=$perf->getSmtpPort(); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">SMTP User:</label>
						<div class="col-sm-4">
							<input type="text" name="smtp_user" id="smtp_user" class="form-control" value="<?=$perf->getSmtpUser(); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">SMTP Passwort:</label>
						<div class="col-sm-4">
							<input type="password" name="smtp_password" id="smtp_password" class="form-control" value="<?=$perf->getSmtpPassword(); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">SSL</label>
						<div class="col-sm-9">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="smtp_ssl" id="smtp_ssl" value="1" <?php if ($perf->getSmtpSsl()) echo ' checked ';?>>
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">TLS</label>
						<div class="col-sm-9">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="smtp_tls" id="smtp_tls" value="1" <?php if ($perf->getSmtpTls()) echo ' checked ';?>>
								</label>
							</div>
						</div>
					</div>
					<hr>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">IMAP eMail-Adresse:</label>
						<div class="col-sm-4">
							<input type="text" name="imap_address" id="imap_address" class="form-control" value="<?=$perf->getImapAddress(); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">IMAP Server:</label>
						<div class="col-sm-4">
							<input type="text" name="imap_host" id="imap_host" class="form-control" value="<?=$perf->getImapHost(); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">IMAP Port:</label>
						<div class="col-sm-4">
							<input type="text" name="imap_port" id="imap_port" class="form-control" value="<?=$perf->getImapPort(); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">IMAP User:</label>
						<div class="col-sm-4">
							<input type="text" name="imap_user" id="imap_user" class="form-control" value="<?=$perf->getImapUser(); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">IMAP Passwort:</label>
						<div class="col-sm-4">
							<input type="password" name="imap_password" id="imap_password" class="form-control" value="<?=$perf->getImapPassword(); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">SSL</label>
						<div class="col-sm-9">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="imap_ssl" id="imap_ssl" value="1" <?php if ($perf->getImapSsl()) echo ' checked ';?>>
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">TLS</label>
						<div class="col-sm-9">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="imap_tls" id="imap_tls" value="1" <?php if ($perf->getImapTls()) echo ' checked ';?>>
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Signatur</label>
						<div class="col-sm-10">
							<textarea class="form-control" name="system_signature" id="system_signature"><?php echo $perf->getSystemSignature();?></textarea>
						</div>
					</div>
				</div>
				<div id="tabs-3">
					<?php
					$formats_raw = $perf->getFormats_raw();
					?>
					<input type="hidden" name="count_formatsraw" id="count_formatsraw"
						   value="<? if (count($formats_raw) > 0) echo count($formats_raw); else echo "1"; ?>">
					<div class="table-responsive">
						<table id="table-formatsraw" class="table table-hover">
							<thead>
								<tr>
									<th  width="10%"><?= $_LANG->get('Nr') ?></th>
									<th  style="text-align: center" width="20%"><?= $_LANG->get('Breite') ?></th>
									<th width="10%">&nbsp;</th>
									<th  style="text-align: center" width="20%"><?= $_LANG->get('Höhe') ?></th>
									<th width="200%"></th>
								</tr>
							</thead>
							<?
							$x = count($formats_raw);
							if ($x < 1) {
								$x++;
							}
							for ($y = 0; $y < $x; $y++) { ?>
							<tbody>
							<tr>
								<td>
									<?= $y + 1 ?>
								</td>
								<td>
										<input name="formatsraw_width_<?= $y ?>" class="form-control" type="text" value="<?= printPrice($formats_raw[$y]["width"]); ?>">
								</td>
								<td style="text-align: center" >x</td>
								<td>
									<input name="formatsraw_height_<?= $y ?>" class="form-control" type="text"
										   value="<?= printPrice($formats_raw[$y]["height"]); ?>">
								</td>
								<td>
									<span class="glyphicons glyphicons-remove pointer"
										  onclick="deleteFormatRawRow(this)"></span>&nbsp;
									<? if ($y == $x - 1) { //Plus-Knopf nur beim letzten anzeigen
										echo '<span class="glyphicons glyphicons-plus pointer" onclick="addFormatRawRow()"></span>';
									} ?>
								</td>
							</tr>
							</tbody>
							<? } ?>
						</table>
					</div>
				</div>
				<div id="tabs-4">
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Def. Anzahl Elemente:</label>
						<div class="col-sm-4">
							<input type="text" class="form-control" name="datatables_showelements" id="datatables_showelements"
								   value="<?php echo $perf->getDt_show_default(); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Speichere Status</label>
						<div class="col-sm-1">
							<input type="checkbox" name="datatables_statesave" class="form-control" id="datatables_statesave" <?php if ($perf->getDt_state_save()) echo " checked "; ?> />
						</div>
					</div>
				</div>
				<div id="tabs-5">
					<div class="form-group">
						<label for="" class="col-sm-1 control-label">Feiertage:</label>
						<span class="pull-right">
								<button class="btn btn-xs btn-success" type="button" onclick="addHolidayRow()">
									<span class="glyphicons glyphicons-plus pointer"></span>
									<?= $_LANG->get('Feiertag hinzuf&uuml;gen') ?>
								</button>
						</span>
					</div>

					<div class="table-responsive">
						<table id="holidays" class="table table-hover">
							<thead>
								<tr>
									<th>ID</th>
									<th>Titel</th>
									<th>Startdatum</th>
									<th>Enddatum</th>
									<th>Hex-Farbe</th>
									<th></th>
								</tr>
							</thead>
							<?php if (count($holidays) > 0) {
							foreach ($holidays as $holiday) {
							?>
							<tbody>
								<tr>
									<td>
										#<?php echo $holiday->getId(); ?>
										<input type="hidden" name="holiday[id][]" value="<?php echo $holiday->getId(); ?>"/>
									</td>
									<td>
										<input type="text" class="form-control" name="holiday[title][]" value="<?php echo $holiday->getTitle(); ?>"/>
									</td>
									<td>
										<input type="text" class="cal form-control" name="holiday[start][]" value="<?php echo date("d.m.Y H:i", $holiday->getBegin()); ?>"/>
									</td>
									<td>
										<input type="text" class="cal form-control" name="holiday[end][]" value="<?php echo date("d.m.Y H:i", $holiday->getEnd()); ?>"/>
									</td>
									<td>
										<input type="text"  class="form-control" name="holiday[color][]" value="<?php echo $holiday->getColor(); ?>"/>
									</td>
									<td><span class="glyphicons glyphicons-remove pointer" onclick="$(this).parent().parent().remove();"></span></td>
								</tr>
							</tbody>
							<?php }
							} ?>
						</table>
					</div>
				</div>
				<div id="tabs-6">
					Spezielle Update Funktionen:<br>
					<button class="btn-danger"
							onclick="location.href='index.php?page=libs/modules/perferences/perferences.php&exec=mergeArticles';">
						Artikel Preisstaffeln zusammenführen
					</button>
				</div>
				<div id="tabs-7">
					Schalter für Funktionen:<br>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Deaktiviere manuelle Artikel</label>
						<div class="col-sm-9">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="deactivate_manual_articles" id="deactivate_manual_articles" value="1" <?php if ($perf->getDeactivateManualArticles()) echo ' checked ';?>>
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Deaktiviere manuelle Versandkosten</label>
						<div class="col-sm-9">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="decativate_manual_delivcost" id="decativate_manual_delivcost" value="1" <?php if ($perf->getDecativateManualDelivcost()) echo ' checked ';?>>
								</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Nutze Adress-Email statt ASP Email beim Versenden von Dokumenten</label>
						<div class="col-sm-9">
							<div class="checkbox">
								<label>
									<input type="checkbox" name="toggle_useadrmail" id="toggle_useadrmail" value="1" <?php if ($perf->getToggleUseadrmail()) echo ' checked ';?>>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div id="tabs-8">
					Kundenportal:<br>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Bestätigungsmail</label>
						<div class="col-sm-10">
							<textarea rows="10" id="mailtext_confirmation" name="mailtext_confirmation" class="form-control"><?= $perf->getMailtextConfirmation() ?></textarea>
							** Verfügbare Variablen: %POSITIONEN%, %KOSTENSTELLE%, %HINWEIS%, %DATEI%
						</div>
					</div>
					Vorgang:<br>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Dokumente versenden Betreff</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="mailtext_senddocs_title" id="mailtext_senddocs_title" value="<?= $perf->getMailtextSenddocsTitle() ?>">
							** Verfügbare Variablen: %VONR%, %VONAME%
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Dokumente versenden</label>
						<div class="col-sm-10">
							<textarea rows="10" id="mailtext_senddocs" name="mailtext_senddocs" class="form-control"><?= $perf->getMailtextSenddocs() ?></textarea>
							** Verfügbare Variablen: %CP%
						</div>
					</div>
				</div>
				<div id="tabs-9">
					Buchhaltung:<br>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Erlöskonto</label>
						<div class="col-sm-10">
							<select name="revenueaccount" id="revenueaccount" class="form-control" style="width: 200px">
								<?php
								$racs = RevenueaccountCategory::getAll();
								foreach ($racs as $rac) { ?>
									<?php
									if ($rac->getId() == $perf->getDefaultRevenue()->getId()) { ?>
										<option
											value="<?php echo $perf->getDefaultRevenue()->getId(); ?>"
											selected><?php echo $perf->getDefaultRevenue()->getTitle(); ?></option>
									<?php } else { ?>
										<option
											value="<?php echo $rac->getId(); ?>"><?php echo $rac->getTitle(); ?></option>
									<?php } ?>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Kostenträger</label>
						<div class="col-sm-10">
							<select name="costobject" id="costobject" class="form-control" style="width: 200px">
								<?php
								$costobjects = CostObject::getAll();
								foreach ($costobjects as $costobject) { ?>
									<?php
										if ($costobject->getId() == $perf->getDefaultCostobject()->getId()) { ?>
											<option
												value="<?php echo $perf->getDefaultCostobject()->getId(); ?>"
												selected><?php echo $perf->getDefaultCostobject()->getTitle(); ?></option>
										<?php } else { ?>
											<option
												value="<?php echo $costobject->getId(); ?>"><?php echo $costobject->getTitle(); ?></option>
										<?php } ?>
								<?php } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Marge (minimum) %</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="minmargin" id="minmargin" placeholder="" value="<?php echo printPrice($perf->getMinmargin());?>">
						</div>
					</div>
				</div>
				<div id="tabs-10">
					Saxoprint:<br>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">API Key</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="saxoapikey" id="saxoapikey" placeholder="" value="<?php echo $perf->getSaxoapikey();?>">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">GK / ASP für Aufträge</label>
						<div class="col-sm-10">
							<?php
							if ($perf->getSaxobc()>0 && $perf->getSaxocp()>0){
								$saxobc = new BusinessContact($perf->getSaxobc());
								$saxocp = new ContactPerson($perf->getSaxocp());
								$saxostring = $saxobc->getNameAsLine() . ' - ' . $saxocp->getNameAsLine();
							}
							?>
							<input type="text" name="search_saxobc" id="search_saxobc" class="form-control" value="<?php echo $saxostring;?>">
							<input type="hidden" name="saxo_customer" id="saxo_customer" value="<?php echo $perf->getSaxobc();?>">
							<input type="hidden" name="saxo_contactperson" id="saxo_contactperson" value="<?php echo $perf->getSaxocp();?>">
						</div>
					</div>
					Saasdo:<br>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Kunden ID</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" name="SaasdoCustomerId" id="SaasdoCustomerId" value="<?php echo $perf->getSaasdoCustomerId();?>">
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>


<script>
	$(function () {
		CKEDITOR.replace( 'mailtext_confirmation' );
		CKEDITOR.replace( 'mailtext_senddocs' );
		$("#revenueaccount").select2();
		$("#costobject").select2();
	});
</script>