<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			13.01.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once('incomingrevert.class.php');
require_once 'libs/modules/businesscontact/businesscontact.class.php';

$filename1 = './docs/'.$_USER->getId().'-Gutschrifteneingang.csv';

// Zeiten fuer die Filter korrekt setzen
if ((int)$_REQUEST["filter_from"] == 0){
	$this_month = date("m",time());
	$filter_from = mktime(2,0,0,$this_month,1);
} else {
	$filter_from = strtotime($_REQUEST["filter_from"]);
}
if ((int)$_REQUEST["filter_to"] == 0){
	$filter_to = mktime(22); // time();
} else {
	$filter_to = strtotime($_REQUEST["filter_to"]." 23:59:59");
}

// Eingangsrechnung loeschen
if($_REQUEST["exec"] == "del"){
	$del_revert = new Incomingrevert($_REQUEST["id"]);
	$ret = $del_revert->delete();
	$savemsg = getSaveMessage($ret);
}

/*
 if($_SESSION["invoiceem"]["month"] == "")
 {
$_SESSION["invoiceem"]["month"]     = (int)date('m');
$_SESSION["invoiceem"]["year"]      = (int)date('Y');

}
if($_REQUEST["filter_month"] != "")
	$_SESSION["invoiceem"]["month"]  = $_REQUEST["filter_month"];
if($_REQUEST["filter_year"] != "")
	$_SESSION["invoiceem"]["year"]   = $_REQUEST["filter_year"];
*/


if($_REQUEST["exec"] == "save"){
	foreach(array_keys($_REQUEST) AS $reqkey){

		if(strpos($reqkey, "rev_title_") !== false){
			$idx = substr($reqkey, strrpos($reqkey, "_") +1);

			if((!empty($_REQUEST["rev_price_netto_{$idx}"]))) { //(!empty($_REQUEST["rev_title_{$idx}"])) &&
				$inv = new Incomingrevert((int)$_REQUEST["rev_existingid_{$idx}"]);
				 
				$inv->setRev_title(trim(addslashes($_REQUEST["rev_title_{$idx}"])));
				$inv->setRev_number(trim(addslashes($_REQUEST["rev_number_{$idx}"])));
				$inv->setRev_taxes_active((int)$_REQUEST["rev_taxes_active_{$idx}"]);
				$inv->setRev_payed((int)$_REQUEST["rev_payed_{$idx}"]);
				$inv->setRev_supplierid((int)$_REQUEST["rev_supplierid_{$idx}"]);
				$rev_price_netto   = $_REQUEST["rev_price_netto_{$idx}"];
				$inv->setRev_price_netto( (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $rev_price_netto))));
				//$inv->setRev_crtdat(mktime(0, 0, 0, $_SESSION["invoiceem"]["month"], 1, $_SESSION["invoiceem"]["year"]));
				 
				if ((int)$_REQUEST["rev_payed_dat_{$idx}"] != 0){
					$rev_payed_dat      = explode(".", $_REQUEST["rev_payed_dat_{$idx}"]);
					$inv->setRev_payed_dat((int)mktime(12, 0, 0, $rev_payed_dat[1], $rev_payed_dat[0], $rev_payed_dat[2]));
				}

				if ((int)$_REQUEST["rev_crtdat_{$idx}"] != 0){
					$rev_crtdat      = explode(".", $_REQUEST["rev_crtdat_{$idx}"]);
					$inv->setRev_crtdat((int)mktime(12, 0, 0, $rev_crtdat[1], $rev_crtdat[0], $rev_crtdat[2]));
				}
				 
				/***
				if ((int)$_REQUEST["rev_payable_dat_{$idx}"] != 0){
					$rev_payable_dat    = explode(".", $_REQUEST["rev_payable_dat_{$idx}"]);
					$inv->setRev_payable_dat((int)mktime(3, 0, 0, $rev_payable_dat[1], $rev_payable_dat[0], $rev_payable_dat[2]));
				}***/
				 
				if($_REQUEST["rev_payed_dat_{$idx}"] > 0)
					$inv->setRev_payed(1);
				else
					$inv->setRev_payed(0);
				 
				// if(!$_REQUEST["rev_uses_supplier_{$idx}"])
				// $inv->setRev_supplierid(0);


				$ret = $inv->save($idx);
			}
		}
		$savemsg = getSaveMessage($ret).$DB->getLastError();
	}
}

/***
// SEPA-XML-Dokument generieren
if($_REQUEST["exec"] == "SEPA_gen"){
	$now = time();
	$number_of_payments = 0;
	$sepa_inv = Array();
	$sum_of_payments = 0.00;

	// Alle Eingangsrechnungen heraussuchen, die bezahlt werden sollen
	foreach(array_keys($_REQUEST) AS $reqkey){
		if(strpos($reqkey, "rev_title_") !== false){
			$idx = substr($reqkey, strrpos($reqkey, "_") +1);

			if($_REQUEST["rev_sepa_activation_{$idx}"] == 1) {
				$tmp_inv = new Incominginvoice((int)$_REQUEST["rev_existingid_{$idx}"]);
				$sepa_inv[] = $tmp_inv;
				$number_of_payments++;
				$sum_of_payments += $tmp_inv->getBrutto();
			}
		}
	}

	// SEPA-XML-Dokument bauen
	if ($number_of_payments > 0){
		$sepa_filename= './docs/'.$_USER->getId().'-SEPA-Buchungen.xml';
		// CSV-Datei der offenen Posten vorbereiten
		$sepa_file_open = fopen($sepa_filename, "w");

		$sepa_string = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
		$sepa_string .= '<Document xmlns="urn:iso:std:iso:20022:tech:xsd:pain.001.002.03" ';
		$sepa_string .= 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ';
		$sepa_string .= 'xsi:schemaLocation="urn:iso:std:iso:20022:tech:xsd:pain.001.002.03 pain.001.002.03.xsd">'."\n";
		$sepa_string .= "<CstmrCdtTrfInitn> \n";
		$sepa_string .= "<GrpHdr> \n";
		// Header-Infos
		$sepa_string .= "<MsgId>Message-ID-".date("YmdHis", $now)."</MsgId> \n";
		$sepa_string .= "<CreDtTm>".date("Y-m-d", $now)."T".date("H:i:s", $now)."</CreDtTm> \n";
		$sepa_string .= "<NbOfTxs>".$number_of_payments."</NbOfTxs>  \n";
		$sepa_string .= "<InitgPty> <Nm>".formatStringForXML($_USER->getNameAsLine())."</Nm> </InitgPty> \n";
		$sepa_string .= "</GrpHdr> \n";
		$sepa_string .= "<PmtInf> \n";
		$sepa_string .= "<PmtInfId>Payment-Info-ID-".date("YmdHis", $now)."</PmtInfId> \n";
		$sepa_string .= "<PmtMtd>TRF</PmtMtd> \n";
		$sepa_string .= "<BtchBookg>true</BtchBookg> \n";
		$sepa_string .= "<NbOfTxs>".$number_of_payments."</NbOfTxs> \n";
		$sepa_string .= "<CtrlSum>".$sum_of_payments."</CtrlSum>  \n";
		$sepa_string .= "<PmtTpInf> <SvcLvl> <Cd>SEPA</Cd> </SvcLvl> </PmtTpInf> \n";
		$sepa_string .= "<ReqdExctnDt>".date("Y-m-d", $now)."</ReqdExctnDt> \n";
		$sepa_string .= "<Dbtr> <Nm>".formatStringForXML($_USER->getClient()->getName())."</Nm> </Dbtr> \n";
		$sepa_string .= "<DbtrAcct> <Id> <IBAN>".$_USER->getClient()->getBankIban()."</IBAN> </Id> </DbtrAcct> \n";
		$sepa_string .= "<DbtrAgt> <FinInstnId> <BIC>".$_USER->getClient()->getBankBic()."</BIC> </FinInstnId> </DbtrAgt> \n";
		$sepa_string .= "<ChrgBr>SLEV</ChrgBr> \n";

		// Ab hier kommen die einzelnen Buchungen
		$i=0;
		foreach ($sepa_inv AS $tmp_inv){
			$tmp_busicon = new BusinessContact($tmp_inv->getRev_supplierid());
			$supp_name = $tmp_busicon->getName1() ; // iconv('UTF-8', 'ISO-8859-1', $tmp_busicon->getName1());
			$supp_name = formatStringForXML($supp_name);
			$sepa_string .= "<CdtTrfTxInf> \n";
			$sepa_string .= "<PmtId> <EndToEndId>OriginatorID-".date("YmdHis", $now)."-".$i."</EndToEndId> </PmtId> \n";
			$sepa_string .= '<Amt> <InstdAmt Ccy="EUR">'.$tmp_inv->getBrutto()."</InstdAmt> </Amt> \n";
			$sepa_string .= "<CdtrAgt> <FinInstnId> <BIC>".$tmp_busicon->getBic()."</BIC> </FinInstnId> </CdtrAgt>  \n";
			$sepa_string .= "<Cdtr> <Nm>".$supp_name."</Nm> </Cdtr> \n";
			$sepa_string .= "<CdtrAcct> <Id> <IBAN>".$tmp_busicon->getIban()."</IBAN> </Id> </CdtrAcct>  \n";
			$sepa_string .= "<RmtInf> <Ustrd>";
			if($tmp_inv->getRev_number() != "" && $tmp_inv->getRev_number() != NULL){
				$sepa_string .= $tmp_inv->getRev_number()." - ";
			}
			$sepa_string .= $tmp_inv->getRev_title();
			$sepa_string .= "</Ustrd> </RmtInf> \n";
			$sepa_string .= "</CdtTrfTxInf> \n";
			$i++;
		}
		$sepa_string .= "</PmtInf> \n";
		$sepa_string .= "</CstmrCdtTrfInitn> \n";
		$sepa_string .= "</Document>";

		// $sepa_string = iconv('UTF-8', 'ISO-8859-1', $sepa_string); // Hier nicht benutzen, da encoding="UTF8"
		fwrite($sepa_file_open, $sepa_string);
		fclose($sepa_file_open);
	} else {?>
		<script type="text/javascript">alert('<?=$_LANG->get('Keine Eingangsrechnung mit SFirm-Relevanz gefunden.');?>');</script>
<?	}
}***/

/*
if($_REQUEST["exec"] == "addTemplates")
{
    $templates = Incominginvoicetemplate::getAllTemplates();
    foreach($templates as $tmpl)
    {
        $invc = new Incominginvoice();
        $invc->setRev_title($tmpl->getRev_title());
        $invc->setRev_price_netto($tmpl->getRev_price_netto());
        $invc->setRev_taxes_active($tmpl->getRev_taxes_active());
        $invc->setRev_supplierid($tmpl->getRev_supplierid());
        $invc->save();
    }
}*/
$filters_open= array("cust_id"=> (int)$_REQUEST["filter_cust"],
					 "payed_status"=> 1,
					 "date_from"=>$filter_from,
					 "date_to"=>$filter_to);
$filters_payed = array("cust_id"=> (int)$_REQUEST["filter_cust"],
						"payed_status"=> 2,
						"date_from"=>$filter_from,
						"date_to"=>$filter_to);
$filters_all = array( "cust_id"=> (int)$_REQUEST["filter_cust"],
						"payed_status"=> 0,
						"date_from"=>$filter_from,
						"date_to"=>$filter_to);

$invoices = Incomingrevert::getAllInvoices($filters_open);
$paid = Incomingrevert::getAllInvoices($filters_payed);
// Fuer die Dokuemntengenerierung: beide Listen zusammen holen in passender sortierung
$all_invoices = Incomingrevert::getAllInvoices($filters_all);

$suppliers = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME,BusinessContact::FILTER_SUPP);

// ---------------------------------------------- CSV-Liste generieren ------------------------------------------------
$sum_taxes = 0;
$sum_brutto = 0;
$sum_netto = 0;

$csv_file = fopen($filename1, "w");

// Tabellenkopf der CSV-Datei schreiben
$csv_string .= "RE-Datum; Betrag Netto ; MWST ; Brutto ; MWST-Satz; ";
$csv_string .= "Lieferant; Kreditor-Nr. ; RE-Nr.; Branche \n"; 	// Bezahlt am; Kundennummer beim Lieferant ; Zahlbar bis \n";

foreach ($all_invoices as $invoice){
	$tmp_supp = new BusinessContact($invoice->getRev_supplierid());
	// Datei mit den offenen Eingangsrechnungen fuellen
	$csv_string .= date('d.m.Y', $invoice->getRev_crtdat()).";";
	$csv_string .= printPrice($invoice->getRev_price_netto())." ".$_USER->getClient()->getCurrency().";";
	$csv_string .= $invoice->getTaxPrice()." ".$_USER->getClient()->getCurrency().";";
	$csv_string .= printPrice($invoice->getBrutto())." ".$_USER->getClient()->getCurrency().";".$invoice->getTaxRate().";";
	$csv_string .= $tmp_supp->getNameAsLine().";".$tmp_supp->getKreditor().";";
	$csv_string .= $invoice->getRev_number().";"; // .$tmp_supp->getNum_at_customer().";";
	/* if ($invoice->getInvc_payable_dat() > 0){
	 $csv_string .= date('d.m.Y', $invoice->getInvc_payable_dat());
	}
	if ($invoice->getInvc_payed_dat() > 0){
	$csv_string .= date('d.m.Y', $invoice->getInvc_payed_dat());
	}*/
	$csv_string .= $tmp_supp->getBranche();
	$csv_string .= " \n";

	$sum_taxes += $invoice->getTax();
	// echo "--- <br> ".$invoice->getTax()."<br>";
	$sum_brutto += $invoice->getBrutto();
	$sum_netto += $invoice->getRev_price_netto();
}

// Zeile fuer die Summen eingeben
$csv_string .= "; ".printPrice($sum_netto)." ".$_USER->getClient()->getCurrency().";";
$csv_string .= printPrice($sum_taxes)." ".$_USER->getClient()->getCurrency().";";
$csv_string .= printPrice($sum_brutto)." ".$_USER->getClient()->getCurrency()." ;;; \n";

$csv_string = iconv('UTF-8', 'ISO-8859-1', $csv_string);
fwrite($csv_file, $csv_string);
fclose($csv_file);

if($invoices == false || count($invoices) == 0){
	$rowcount = 5;
} else {
	$rowcount = count($invoices)+5;
}?>
<script type="text/javascript">
$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	
	$('.rev_payable_dat').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "images/icons/calendar-blue.png",
                buttonImageOnly: true
			}
     );

	$('.rev_payed_dat').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "images/icons/calendar-blue.png",
                buttonImageOnly: true
			}
     );

	$('.rev_crtdat').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "images/icons/calendar-blue.png",
                buttonImageOnly: true
			}
     );
 
	$('.filter_from').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "images/icons/calendar-blue.png",
                buttonImageOnly: true
			}
     );

	$('.filter_to').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "images/icons/calendar-blue.png",
                buttonImageOnly: true
			}
     );
});
</script>
<table class="standard">
	<tr>
		<td style="height: 30px"><nobr><b class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Eingangs-&Uuml;bersicht')?></b></nobr></td>
		<td style="text-align: center"><?=$savemsg?></td>
		<td style="text-align: right"> &emsp;
				<?/*** table class="standard">
					<tr>
						<td class="content_row_clear" width="60"><b><?=$_LANG->get('Monat:')?></b>
						<select class="text" name="filter_month"
							onchange="location.href='index.php?pid=<?=$_REQUEST["pid"]?>&filter_month=' +this.value">
							<?
							for($x = 1; $x <= 12; $x++)
							{
								$dsp_month = $x;
								if($dsp_month < 10)
								$dsp_month = "0{$dsp_month}";
								?>
							<option value="<?=$x?>"
							<? if($x == $_SESSION["invoiceem"]["month"]) echo 'selected="selected"' ?>><?=$dsp_month?></option>
							<?
							}
							?>
						</select></td>
						<td class="content_row_clear" width="88"><b><?=$_LANG->get('Jahr:')?></b> <select
							class="text" name="filter_year"
							onchange="location.href='index.php?pid=<?=$_REQUEST["pid"]?>&filter_year=' +this.value">
							<?
							$startyear  = date('Y') -3;
							$endyear    = date('Y');
		
							for($x = $startyear; $x <= $endyear; $x++)
							{
								?>
							<option value="<?=$x?>"
							<? if($x == $_SESSION["invoiceem"]["year"]) echo 'selected="selected"' ?>><?=$x?></option>
							<?
							}
							?>
						</select></td>
						<td class="content_row_clear" style="width: 280px"></td>
					</tr>
				</table***/?>
		</td>
	</tr>
	<tr>
		<td class="content_headerline" colspan="3">&nbsp;</td>
	</tr>
</table>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="xform_invoicesearch">
	<input type="hidden" name="subexec" value="search">
	<input type="hidden" name="mid" value="<?=$_REQUEST["mid"]?>">
	<div class="box2" style="width:600px;" >
		<table width="100%" cellpadding="00" cellspacing="0">
			<colgroup>
				<col width="110">
				<col>
				<col width="110">
				<col>
			</colgroup>
			<tr>
				<td class="content_row_header" colspan="2">Filteroptionen</td>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Lieferant');?></td>
				<td class="content_row_clear">
					<select type="text" id="filter_cust" name="filter_cust" style="width:150px"
						onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
						<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
					<? 	foreach ($suppliers as $cust){?>
							<option value="<?=$cust->getId()?>"
								<?if ($filters["cust_id"] == $cust->getId()) echo "selected" ?>><?= $cust->getNameAsLine()?></option>
					<?	} ?>
					</select>
				</td>
				<td class="content_row_header"> <?=$_LANG->get('Zeitraum');?></td>
				<td class="content_row_clear">
					Von <input  type="text" name="filter_from" id="filter_from" class="text filter_from" style="width: 65px" 
								value="<?=date("d.m.Y",$filter_from)?>" />
					Bis <input  type="text" name="filter_to" id="filter_to" class="text filter_to" style="width: 65px" 
								value="<?=date("d.m.Y",$filter_to)?>" />
				</td>
			</tr>
			<? /*** tr>
				<td class="content_row_header"><?=$_LANG->get('Status');?></td>
				<td class="content_row_clear">
					<select type="text" id="payed_status" name="payed_status" style="width:150px"
						onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
						<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
						<option value="1"
								<?if ($filters["payed_status"] == 1) echo "selected" ?>><?=$_LANG->get('offen')?></option>
						<option value="2"
								<?if ($filters["payed_status"] == 2) echo "selected" ?>><?=$_LANG->get('bezahlt')?></option>
					</select>
				</td>
				<td colspan="2"> &emsp; </td>
			</tr **/?>
			<tr>
				
				<td class="content_row_clear" align="right" colspan="4">
					<input type="submit" value="<?=$_LANG->get('Suche starten')?>">
				</td>
			</tr>
		</table>
	</div>
</form>
<br/>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="idx_invcform" id="idx_invcform">
	<input type="hidden" name="exec" id="exec" value="save">
   <input type="hidden" name="payed_status" value="<?=$filters_open["payed_status"]?>" />
   <input type="hidden" name="filter_from" value="<?=date("d.m.Y",$filter_from)?>" />
   <input type="hidden" name="filter_to" value="<?=date("d.m.Y",$filter_to)?>" />
   <input type="hidden" name="filter_cust" value="<?=$filters_open["cust_id"]?>" />
<div class="box1">
<table class="standard" style="padding: 3px">
	<colgroup>
		<col width="130">
		<col>
		<col width="30">
		<col width="90">
		<col width="60">
		<col width="80">
		<col width="90">
		<col width="90">
		<!-- col width="130"-->
		<col width="130">
		<!-- col width="60"-->
		<col width="100">
	</colgroup>
	<tr>
		<td class="content_tbl_header" colspan="9"><h1><?=$_LANG->get('Erfassung / Offen')?></h1></td>
	</tr>
	<tr>
		<td class="content_row_header"><?=$_LANG->get('GS-Datum')?></td>
		<td class="content_row_header"><?=$_LANG->get('Lieferant / Grund der Gutschrift')?></td>
		<td class="content_row_header"><?=$_LANG->get('Lief')?></td>
		<td class="content_row_header"><?=$_LANG->get('Netto')?></td>
		<td class="content_row_header"><?=$_LANG->get('MwSt-Satz')?></td>
		<td class="content_row_header" style="text-align: right"><?=$_LANG->get('MwSt')?></td>
		<td class="content_row_header" style="text-align: right"><?=$_LANG->get('Brutto')?></td>
		<td class="content_row_header"><?=$_LANG->get('GS-Nummer')?></td>
		<!-- td class="content_row_header"><?=$_LANG->get('F&auml;llig')?></td-->
		<td class="content_row_header"><?=$_LANG->get('Ausbezahlt')?></td>
		<!-- td class="content_row_header"><?=$_LANG->get('SF-rel')?></td-->
		<td class="content_row_header">&nbsp;</td>
	</tr>
	<? // CSV-Datei der offenen Posten vorbereiten
	$csv_file_open = fopen('./docs/'.$_USER->getId().'-Gutschrifteneingang_offen.csv', "w");
	//fwrite($csv_file, "Firma iPactor - �bersicht\n");
	
	//Tabellenkopf der CSV-Datei (offene Posten) schreiben
	$csv_string_open .= "Grund der Gutschrift; Betrag Netto ; MWST ; Brutto ; MWST-Satz; ";
	$csv_string_open .= "Lieferant; Kreditor-Nr. ; GS-Nr.; Kundennummer beim Lieferant ; Zahlbar bis \n";
	$x = 0;
	foreach ($invoices as $invoice)
	{
		$tmp_supp = new BusinessContact($invoice->getRev_supplierid());
		// Datei mit den offenen Eingangsrechnungen fuellen
		$csv_string_open .= $invoice->getRev_title().";".printPrice($invoice->getRev_price_netto()).";";
		$csv_string_open .= $invoice->getTaxPrice().";".$invoice->getBruttoPrice().";".$invoice->getTaxRate().";";
		$csv_string_open .= $tmp_supp->getNameAsLine().";".$tmp_supp->getKreditor().";";
		$csv_string_open .= $invoice->getRev_number().";".$tmp_supp->getNumberatcustomer().";";
		if ($invoice->getRev_payable_dat() > 0){
			$csv_string_open .= date('d.m.Y', $invoice->getRev_payable_dat());
		} 
		$csv_string_open .= " \n";
		
		/*/ if ($invoices[$x]->getRev_payable_dat() > 0 && $invoices[$x]->getRev_payable_dat() + 86400 < time())
	    if ($_REQUEST["rev_payable_dat"] > 0 && $_REQUEST["rev_payable_dat"] + 86400 < time())
	        $color = "#F5D5D5";
	    else*/
	    $color = getRowColor($x);
		?>
	<tr class="<?=$color?>" onmouseover="mark(this, 0)"
		onmouseout="mark(this, 1)">
		<td class="content_row" style="color: #912f4e">
			<input type="text" name="rev_crtdat_<?=$x?>" id="rev_crtdat_<?=$x?>"
				value="<? if ($invoice->getRev_crtdat() > 0) echo date('d.m.Y', $invoice->getRev_crtdat())?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)" style="width: 60px;" class="text rev_crtdat" />
		</td>
		<td class="content_row">
			<input type="hidden" name="rev_existingid_<?=$x?>" name="rev_existingid_<?=$x?>"
					value="<?=(int)$invoice->getId()?>" /> 
			<select class="text" style="width:170px; margin-bottom: 3px; <?if($invoice->getRev_supplierid() == 0) echo "display:none"?>"
            		name="rev_supplierid_<?=$x?>" id="rev_supplierid_<?=$x?>"
            		onfocus="markfield(this,0)" onblur="markfield(this,1)">
				<option value="">&lt;<?=$_LANG->get('Lieferant ausw&auml;hlen')?>&gt;</option>
			<?	foreach($suppliers AS $supplier) { ?>
					<option value="<?=$supplier->getId()?>"
				<? if($supplier->getId() == $invoice->getRev_supplierid()) echo 'selected="selected"'?>><?=$supplier->getNameAsLine()?></option>
			<?	} 	?>
			</select>
			<input type="text" class="text" style="width: 170px" name="rev_title_<?=$x?>" value="<?=$invoice->getRev_title()?>" 
					onfocus="markfield(this,0)" onblur="markfield(this,1)">
		</td>
		<td class="content_row" align="center">
			<input type="checkbox" 	name="rev_uses_supplier_<?=$x?>" value="1" <?if($invoice->getRev_supplierid() > 0) echo 'checked="checked"'?>
					onclick="if(this.checked)
		                        document.getElementById('rev_supplierid_<?=$x?>').style.display='';
		                     else
		                        document.getElementById('rev_supplierid_<?=$x?>').style.display='none';"
		                        <? if((int)$invoice->getRev_supplierid()) echo "checked='checked'"?>>
		</td>
		<td class="content_row">
			<input type="text" class="text" style="width: 55px;text-align: right" name="rev_price_netto_<?=$x?>"
					value="<?=printPrice($invoice->getRev_price_netto());?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
			  <?=$_USER->getClient()->getCurrency()?>
		</td>
		<td class="content_row" style="text-align: center">
			<input type="text" class="text" style="width: 30px;text-align: right" onfocus="markfield(this,0)" onblur="markfield(this,1)" 
					name="rev_taxes_active_<?=$x?>" id="rev_taxes_active_<?=$x?>"	value="<?=$invoice->getRev_taxes_active();?>" /> %
		</td>
		<td class="content_row" style="text-align: right">
			<? if((int)$invoice->getId()) {echo $invoice->getTaxPrice();echo " ".$_USER->getClient()->getCurrency();} else echo "- - - "?>
		</td>
		<td class="content_row" style="text-align: right">
			<? if((int)$invoice->getId()) {echo $invoice->getBruttoPrice(); echo " ".$_USER->getClient()->getCurrency();} else echo "- - - "?>
		</td>
		<td class="content_row">
			<input type="text" class="text"	name="rev_number_<?=$x?>" style="width: 88px"
					value="<?=$invoice->getRev_number()?>">
		</td>
		<!-- td class="content_row">
			<input type="text" name="rev_payable_dat_<?=$x?>" id="rev_payable_dat_<?=$x?>"
				value="<? if ($invoice->getRev_payable_dat() > 0) echo date('d.m.Y', $invoice->getRev_payable_dat())?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)" style="width: 60px;" class="text rev_payable_dat" />
		</td-->
		<td class="content_row">
			<input type="text" name="rev_payed_dat_<?=$x?>" id="rev_payed_dat_<?=$x?>"
				value="<? if ($invoice->getRev_payed_dat() > 0) echo date('d.m.Y', $invoice->getRev_payed_dat())?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)" style="width: 60px;" class="text rev_payed_dat" />
		</td>
		<!-- td class="content_row" align="center">
			<input type="checkbox" 	name="rev_sepa_activation_<?=$x?>" value="1" 
				<? if (in_array($invoice, $sepa_inv)) echo 'checked="checked"';?>>
		</td-->
		<td class="content_row">
			<ul class="postnav_del_small_invoice" style="margin-top: 7px; padding-left: 0px; padding-right: 0px;">
			<?	if($invoice->getId()){  ?>
					<a href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&id=<?=$invoice->getId()?>&exec=del')"><?=$_LANG->get('L&ouml;schen')?></a>
	
					<!-- input type="button" class="buttonRed" value="<?=$_LANG->get('L&ouml;schen')?>" onclick="askDel('index.php?pid=<?=$_REQUEST["pid"]?>&id=<?=$invoice->getId()?>&exec=del')"-->
			<?	} else { ?>
				<a href="#" style="visibility: hidden"><?=$_LANG->get('L&ouml;schen')?></a>
			<?	}?>
			</ul>
		</td>

	</tr>
	<?
	$x++;

	}
	
	// Datei mit den offenen Eingangsrechnungen schliessen
	$csv_string = iconv('UTF-8', 'ISO-8859-1', $csv_string);
	fwrite($csv_file_open, $csv_string_open);
	fclose($csv_file_open);

	// leeere Felder einfuegen

	for($y=$x;$y<$x+5;$y++){  ?>
	<tr class="<?=getRowColor($y)?>" onmouseover="mark(this, 0)"
		onmouseout="mark(this, 1)">
		<td class="content_row" >
			<input type="text" name="rev_crtdat_<?=$y?>" id="rev_crtdat_<?=$y?>" 
				onfocus="markfield(this,0)" onblur="markfield(this,1)" style="width: 60px;" class="text rev_crtdat" />
		</td>
		<td class="content_row pointer"> 
			<input type="hidden" name="rev_existingid_<?=$y?>" name="rev_existingid_<?=$y?>" value=0 /> 
			<select class="text" style="width: 170px; margin-bottom: 3px; "
					name="rev_supplierid_<?=$y?>" id="rev_supplierid_<?=$y?>"
					onfocus="markfield(this,0)" onblur="markfield(this,1)">
				<option value="">&lt; <?=$_LANG->get('Lieferant ausw&auml;hlen')?> &gt;</option>
			<?	foreach($suppliers AS $supplier){?>
					<option value="<?=$supplier->getId()?>"><?=$supplier->getNameAsLine()?></option>
			<?	} ?>
			</select>	
			<input type="text" class="text" style="width: 170px; padding-top:2px;" name="rev_title_<?=$y?>" value=""
					onfocus="markfield(this,0)" onblur="markfield(this,1)">
		</td>
		<td class="content_row pointer" align="center">
			<input type="checkbox"name="rev_uses_supplier_<?=$y?>" value="1" checked="checked"
			onclick="if(this.checked)
                     document.getElementById('rev_supplierid_<?=$y?>').style.display='';
                  else
                     document.getElementById('rev_supplierid_<?=$y?>').style.display='none';">
		</td>
		<td class="content_row pointer">
			<input type="text" class="text" style="width: 55px" name="rev_price_netto_<?=$y?>" value=""
				onfocus="markfield(this,0)" onblur="markfield(this,1)">  <?=$_USER->getClient()->getCurrency()?>
		</td>
		<td class="content_row pointer" style="text-align: center">
			<input class="text" style="width: 30px;text-align: right" name="rev_taxes_active_<?=$y?>" id="rev_taxes_active_<?=$y?>"
			value="<?=$_USER->getClient()->getTaxes()?>"
			onfocus="markfield(this,0)" onblur="markfield(this,1)" onclick="this.value=''" /> %
		</td>
		<td class="content_row pointer" style="text-align: right"><? echo "- - - "?></td>
		<td class="content_row pointer" style="text-align: right"><? echo "- - - "?></td>
		<td class="content_row pointer">
			<input type="text" class="text"	name="rev_number_<?=$y?>" style="width: 88px">
		</td>
		<!-- td class="content_row pointer">
			<input type="text" name="rev_payable_dat_<?=$y?>" id="rev_payable_dat_<?=$y?>" onfocus="markfield(this,0)"
					onblur="markfield(this,1)" style="width: 60px;" class="text rev_payable_dat">
		</td-->
		<td class="content_row pointer">
			<input type="text" name="rev_payed_dat_<?=$y?>" id="rev_payed_dat_<?=$y?>" onfocus="markfield(this,0)"
			onblur="markfield(this,1)" style="width: 60px;" class="text rev_payed_dat">
		</td>
		<td class="content_row" colspan="2">
			<ul class="postnav_del_small_invoice">
				<a href="#" style="visibility: hidden"><?=$_LANG->get('L&ouml;schen')?></a>
			</ul>
		</td>
	</tr>
<?	} ?>
</table>
</div>
<br />
<div class="box1">
<table class="standard">
	<tr>
		<td style="text-align: left; width: 130px">
			&ensp;
			<!-- input type="button" class="button" value="<?=$_LANG->get('Standardrechnungen')?>"
					onclick="location.href='index.php?exec=addTemplates'" /-->
		</td>
		<td align="center">
			<a href="<?=$filename1?>"
				title="<?=$_LANG->get('Offene Posten als CSV-Datei exportieren')?>"   class="icon-link"
				><img src="images/icons/calculator--arrow.png"> <?=$_LANG->get('Export')?></a>
				
			<? /*** ?>
			<a href="./docs/<?=$_USER->getId()?>-Gutschrifteneingang_offen.csv"
				title="<?=$_LANG->get('Offene Posten als CSV-Datei exportieren')?>" 
				><img src="images/icons/calculator--minus.png"><?=$_LANG->get('Offene Posten')?></a>
			&emsp; 
			<a href="./docs/<?=$_USER->getId()?>-Gutschrifteneingang_ausbezahlt.csv" 
				title="<?=$_LANG->get('Bezahlte Posten als CSV-Datei exportieren')?>"
				><img src="images/icons/calculator-gray.png" ><?=$_LANG->get('Ausbezahlte Posten')?></a>
			**/?>
				
		<?/*	if($_REQUEST["exec"] == "SEPA_gen"){
				if ($number_of_payments > 0){?>
				&emsp;
				<a href="<?=$sepa_filename?>" title="<?=$_LANG->get('XML-Datei f&uuml;r SFirm &ouml;ffnen')?>"
					> <img src="images/icons/compile.png" >SFrim-Export</a>
		<? 		}
			}*/?>
		</td>
		<td style="text-align: left; width: 140px">
			<!-- input type="button" class="button" value="<?=$_LANG->get('SFirm-Datei erzeugen')?>" 
					title="<?=$_LANG->get('Erzeuge eine XML-Datei zum Verbuchen der ausgew&auml;hlten Rechnungen in SFirm')?>"
					onclick="document.getElementById('exec').value='SEPA_gen';document.getElementById('idx_invcform').submit();" /-->
		</td>
		<td style="text-align: right; width: 130px">
			<input type="submit" class="button" value="<?=$_LANG->get('Speichern')?>" />
		</td>
	</tr>
</table>
</div>
</form>
<br />
<div class="box1">
<table class="standard">
	<colgroup>
		<col width="80">
		<col>
		<col width="95">
		<col width="60">
		<col width="95">
		<col width="95">
		<col width="130">
		<col width="50">
		<col width="100">
	</colgroup>
	<tr>
		<td class="content_tbl_header" colspan="8"><h1><?=$_LANG->get('Ausbezahlte Gutschriften')?></h1></td>
	</tr>
	<tr>
		<td class="content_row_header" style="color: green"><?=$_LANG->get('Ausbezahlt am')?></td>
		<td class="content_row_header"><?=$_LANG->get(' Lieferant / Grund der Gutschrift')?></td>
		<td class="content_row_header" style="text-align: right"><?=$_LANG->get('Brutto-Betrag')?></td>
		<td class="content_row_header" style="text-align: right"><?=$_LANG->get('MwSt')?></td>
		<td class="content_row_header" style="text-align: right"><?=$_LANG->get('MwSt-Betrag')?></td>
		<td class="content_row_header" style="text-align: right"><?=$_LANG->get('Netto-Betrag')?></td>
		<td class="content_row_header" style="text-align: right"><?=$_LANG->get('GS-Nummer')?></td>
		<td class="content_row_header"><?=$_LANG->get('ausbezahlt')?></td>
		<td class="content_row_header">&nbsp;</td>
	</tr>
	<? // CSV-Datei der bezahlten Posten vorbereiten
	$csv_file_payed = fopen('./docs/'.$_USER->getId().'-Gutschrifteneingang_ausbezahlt.csv', "w");
	//fwrite($csv_file, "Firma iPactor - �bersicht\n");
	
	//Tabellenkopf der CSV-Datei (offene Posten) schreiben
	$csv_string_payed .= "Grund der Gutschrift; Betrag Netto ; MWST ; Brutto; MWST-Satz; ";
	$csv_string_payed .= "Lieferant; Kreditor-Nr. ; GS-Nr. ; \n";
	foreach ($paid as $invoice)
	{
		$tmp_supp = new BusinessContact($invoice->getRev_supplierid());
		// Datei mit den bezahlten Eingangsrechnungen fuellen
		$csv_string_payed .= $invoice->getRev_title().";".printPrice($invoice->getRev_price_netto()).";";
		$csv_string_payed .= $invoice->getTaxPrice().";".$invoice->getBruttoPrice().";".$invoice->getTaxRate().";";
		$csv_string_payed .= $tmp_supp->getNameAsLine().";".$tmp_supp->getKreditor().";";
		$csv_string_payed .= $invoice->getRev_number().";";
		/*if ($invoice->getRev_payed_dat() > 0){
			$csv_string_payed .= date('d.m.Y', $invoice->getRev_payed_dat());
		}*/
		$csv_string_payed .= " \n";
		
		if((int)$invoice->getRev_taxes_active())
		$img_status = "status_green.gif";
		else
		$img_status = "status_red.gif";

		if((int)$invoice->getRev_payed())
		$img_status2 = "status_green.gif";
		else
		$img_status2 = "status_red.gif";
		?>
	<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)"
		onmouseout="mark(this,1)">
		<td class="content_row" style="color: green"><?=date('d.m.Y', $invoice->getRev_payed_dat())?></td>
		<td class="content_row"><?=$tmp_supp->getNameAsLine()."<br/>".$invoice->getRev_title()?></td>
		<td class="content_row" style="text-align: right"><?=$invoice->getBruttoPrice()?> <?=$_USER->getClient()->getCurrency()?></td>
		<td class="content_row" style="text-align: right"><?=$invoice->getTaxRate()?></td>
		<td class="content_row" style="text-align: right"><?=$invoice->getTaxPrice()?> <?=$_USER->getClient()->getCurrency()?></td>
		<td class="content_row" style="text-align: right"><?=$invoice->getRev_price_netto()?> <?=$_USER->getClient()->getCurrency()?></td>
		<td class="content_row" style="text-align: right"><?if($invoice->getRev_number()!="") echo $invoice->getRev_number(); else echo "- - - ";?></td>
		<td class="content_row" style="text-align: center"><img
			src="./images/icons/<?=$img_status2?>"></td>
		<td class="content_row">
		<ul class="postnav_del_small" style="margin-bottom:3px;">
			<a href="#" style="padding:10px 40px 10px 28px;height:26px"
				onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&id=<?=$invoice->getId()?>&exec=del')"><?=$_LANG->get('L&ouml;schen')?></a>
		</ul>
		</td>
	</tr>
	<?
	}
	
	// Datei mit den offenen Eingangsrechnungen schliessen
	$csv_string = iconv('UTF-8', 'ISO-8859-1', $csv_string);
	fwrite($csv_file_payed, $csv_string_payed);
	fclose($csv_file_payed);
	
	if(!$paid)
	{  ?>
	<tr class="<?=getRowColor($x)?>">
		<td class="content_row" colspan="10" style="text-align: center"><br>
		<b class="msg_save_err"><?=$_LANG->get('Es sind keine ausbezahlten Vorg&auml;nge vorhanden.')?>
		</b> <br>
		<br>
		</td>
	</tr>
	<?
	}
	?>
</table>
</div>
