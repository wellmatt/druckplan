<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       09.05.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('incominginvoicetemplate.class.php');
require_once('incominginvoice.class.php');
require_once 'libs/modules/businesscontact/businesscontact.class.php';

$filename1 = './docs/'.$_USER->getId().'-Rechnungseingang.csv';
// $filename1 = './docs/'.$_USER->getId().'-Rechnungseingang_offen.csv';

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
	$inv = new Incominginvoice($_REQUEST["id"]);
	$ret = $inv->delete();
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

		if(strpos($reqkey, "invc_title_") !== false){
		    $idx = substr($reqkey, strrpos($reqkey, "_") +1);
		    $invc_orders = Array();

		    if((!empty($_REQUEST["invc_price_netto_{$idx}"]))) { //(!empty($_REQUEST["invc_title_{$idx}"])) &&
		        $inv = new Incominginvoice((int)$_REQUEST["invc_existingid_{$idx}"]);
		        	
		        $inv->setInvc_title(trim(addslashes($_REQUEST["invc_title_{$idx}"])));
		        $inv->setInvc_number(trim(addslashes($_REQUEST["invc_number_{$idx}"])));
		        $inv->setInvc_taxes_active((int)$_REQUEST["invc_taxes_active_{$idx}"]);
		        $inv->setInvc_payed((int)$_REQUEST["invc_payed_{$idx}"]);
		        $inv->setInvc_supplierid((int)$_REQUEST["invc_supplierid_{$idx}"]);
		        $invc_price_netto   = $_REQUEST["invc_price_netto_{$idx}"];
		        $inv->setInvc_price_netto( (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $invc_price_netto))));
		        //$inv->setInvc_crtdat(mktime(0, 0, 0, $_SESSION["invoiceem"]["month"], 1, $_SESSION["invoiceem"]["year"]));
		        	
				if ((int)$_REQUEST["invc_payed_dat_{$idx}"] != 0){
		        	$invc_payed_dat      = explode(".", $_REQUEST["invc_payed_dat_{$idx}"]);
		        	$inv->setInvc_payed_dat((int)mktime(12, 0, 0, $invc_payed_dat[1], $invc_payed_dat[0], $invc_payed_dat[2]));
				}
				
				if ((int)$_REQUEST["invc_crtdat_{$idx}"] != 0){
					$invc_crtdat      = explode(".", $_REQUEST["invc_crtdat_{$idx}"]);
					$inv->setInvc_crtdat((int)mktime(12, 0, 0, $invc_crtdat[1], $invc_crtdat[0], $invc_crtdat[2]));
				}
		        	
				if ((int)$_REQUEST["invc_payable_dat_{$idx}"] != 0){
		       		$invc_payable_dat    = explode(".", $_REQUEST["invc_payable_dat_{$idx}"]);
		        	$inv->setInvc_payable_dat((int)mktime(12, 0, 0, $invc_payable_dat[1], $invc_payable_dat[0], $invc_payable_dat[2]));
				}
		        	
		        if($_REQUEST["invc_payed_dat_{$idx}"] > 0)
		            $inv->setInvc_payed(1);
		        else
		            $inv->setInvc_payed(0);
		        	
		        // if(!$_REQUEST["invc_uses_supplier_{$idx}"])
		            // $inv->setInvc_supplierid(0);

// 		        print_r($_REQUEST["invc_order"][$idx]);
		        if($_REQUEST["invc_order"][$idx]){
	                if ($_REQUEST["invc_order"][$idx]['amount'] > 0){
	                  $invc_orders[] = $_REQUEST["invc_order"][$idx];
	                }
		        }
		        $inv->setInvc_orders($invc_orders);

		        $ret = $inv->save($idx);
		    }
		}
		$savemsg = getSaveMessage($ret).$DB->getLastError();
	}
}

// -----------------------------------------------  SEPA-XML-Dokument generieren --------------------------------------
$sepa_inv = Array();
if($_REQUEST["exec"] == "SEPA_gen"){
	$now = time();
	$number_of_payments = 0;
	$sum_of_payments = 0.00;
	
	// Alle Eingangsrechnungen heraussuchen, die bezahlt werden sollen
	foreach(array_keys($_REQUEST) AS $reqkey){
		if(strpos($reqkey, "invc_title_") !== false){
			$idx = substr($reqkey, strrpos($reqkey, "_") +1);
	
			if($_REQUEST["invc_sepa_activation_{$idx}"] == 1) { 
				$tmp_inv = new Incominginvoice((int)$_REQUEST["invc_existingid_{$idx}"]);
				$sepa_inv[] = $tmp_inv;
				$number_of_payments++;
				$sum_of_payments += $tmp_inv->getBrutto(); 
			}
		}
	}
	
	// SEPA-XML-Dokument bauen
	if ($number_of_payments > 0){
	
	 // echo "---- ";
	 // var_dump($_REQUEST["bankname"]);
		
		$client_name = $_USER->getClient()->getName();
		$bank_iban = $_USER->getClient()->getBankIban();
		$bank_bic  = $_USER->getClient()->getBankBic();
		
		if((int)$_REQUEST["bankname"] == 2){
			// $bank_name = $_USER->getClient()->getName2();
			$bank_iban = $_USER->getClient()->getBankIban2();
			$bank_bic  = $_USER->getClient()->getBankBic2();
		}
		if((int)$_REQUEST["bankname"] == 3){
			// $bank_name = $_USER->getClient()->getName3();
			$bank_iban = $_USER->getClient()->getBankIban3();
			$bank_bic  = $_USER->getClient()->getBankBic3();
		}
		
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
				$sepa_string .= "<CtrlSum>".number_format($sum_of_payments, 2, ".", "")."</CtrlSum>  \n";
				$sepa_string .= "<PmtTpInf> <SvcLvl> <Cd>SEPA</Cd> </SvcLvl> </PmtTpInf> \n";
				$sepa_string .= "<ReqdExctnDt>".date("Y-m-d", $now)."</ReqdExctnDt> \n";
				$sepa_string .= "<Dbtr> <Nm>".formatStringForXML($client_name)."</Nm> </Dbtr> \n";
				$sepa_string .= "<DbtrAcct> <Id> <IBAN>".$bank_iban."</IBAN> </Id> </DbtrAcct> \n";
				$sepa_string .= "<DbtrAgt> <FinInstnId> <BIC>".$bank_bic."</BIC> </FinInstnId> </DbtrAgt> \n";
				$sepa_string .= "<ChrgBr>SLEV</ChrgBr> \n";
				
				// Ab hier kommen die einzelnen Buchungen
				$i=0;
				foreach ($sepa_inv AS $tmp_inv){
					$tmp_busicon = new BusinessContact($tmp_inv->getInvc_supplierid());
					$supp_name = $tmp_busicon->getName1() ; // iconv('UTF-8', 'ISO-8859-1', $tmp_busicon->getName1());
					$supp_name = formatStringForXML($supp_name);
					$sepa_string .= "<CdtTrfTxInf> \n";
						$sepa_string .= "<PmtId> <EndToEndId>OriginatorID-".date("YmdHis", $now)."-".$i."</EndToEndId> </PmtId> \n";
						$sepa_string .= '<Amt> <InstdAmt Ccy="EUR">'.number_format($tmp_inv->getBrutto(), 2, ".", "")."</InstdAmt> </Amt> \n";
						$sepa_string .= "<CdtrAgt> <FinInstnId> <BIC>".$tmp_busicon->getBic()."</BIC> </FinInstnId> </CdtrAgt>  \n";
						$sepa_string .= "<Cdtr> <Nm>".$supp_name."</Nm> </Cdtr> \n";
						$sepa_string .= "<CdtrAcct> <Id> <IBAN>".$tmp_busicon->getIban()."</IBAN> </Id> </CdtrAcct>  \n";
						$sepa_string .= "<RmtInf> <Ustrd>";
							if($tmp_inv->getInvc_number() != "" && $tmp_inv->getInvc_number() != NULL){
								$sepa_string .= $tmp_inv->getInvc_number()." - ";
							}
							$sepa_string .= $tmp_inv->getInvc_title();
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
}



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

// Fuer offene-Posten Liste
$invoices = Incominginvoice::getAllInvoices($filters_open);
// Fuer bezahlte-Posten Liste
$paid = Incominginvoice::getAllInvoices($filters_payed);
// Fuer die Dokuemntengenerierung beide Listen zusammen holen in passender sortierung
$all_invoices = Incominginvoice::getAllInvoices($filters_all);

$suppliers = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME,BusinessContact::FILTER_SUPP);


// ---------------------------------------------- CSV-Liste generieren ------------------------------------------------
$sum_taxes = 0;
$sum_brutto = 0;
$sum_netto = 0;

// $csv_file = fopen($filename1, "w");

// Tabellenkopf der CSV-Datei schreiben
$csv_string .= "RE-Datum; Betrag Netto ; MWST ; Brutto ; MWST-Satz; ";
$csv_string .= "Lieferant; Kreditor-Nr. ; RE-Nr.\n"; 	// Bezahlt am; Kundennummer beim Lieferant ; Zahlbar bis \n";

foreach ($all_invoices as $invoice){
	$tmp_supp = new BusinessContact($invoice->getInvc_supplierid());
	// Datei mit den offenen Eingangsrechnungen fuellen
	$csv_string .= date('d.m.Y', $invoice->getInvc_crtdat()).";";
	$csv_string .= printPrice($invoice->getInvc_price_netto())." ".$_USER->getClient()->getCurrency().";";
	$csv_string .= $invoice->getTaxPrice()." ".$_USER->getClient()->getCurrency().";";
	$csv_string .= printPrice($invoice->getBrutto())." ".$_USER->getClient()->getCurrency().";".$invoice->getTaxRate().";";
	$csv_string .= $tmp_supp->getNameAsLine().";".$tmp_supp->getKreditor().";";
	$csv_string .= $invoice->getInvc_number().";"; // .$tmp_supp->getNum_at_customer().";";
	/* if ($invoice->getInvc_payable_dat() > 0){
	 $csv_string .= date('d.m.Y', $invoice->getInvc_payable_dat());
	} 
	if ($invoice->getInvc_payed_dat() > 0){
		$csv_string .= date('d.m.Y', $invoice->getInvc_payed_dat());
	}*/
	$csv_string .= " \n";
	
	$sum_taxes += $invoice->getTax();
	$sum_brutto += $invoice->getBrutto();
	$sum_netto += $invoice->getInvc_price_netto();
}

// Zeile fuer die Summen eingeben
$csv_string .= "; ".printPrice($sum_netto)." ".$_USER->getClient()->getCurrency().";";
$csv_string .= printPrice($sum_taxes)." ".$_USER->getClient()->getCurrency().";";
$csv_string .= printPrice($sum_brutto)." ".$_USER->getClient()->getCurrency()." ;;; \n";

// $csv_string = iconv('UTF-8', 'ISO-8859-1', $csv_string);
// fwrite($csv_file, $csv_string);
// fclose($csv_file);


// Fuer zusaetzliche Eingabefelder
if($invoices == false || count($invoices) == 0){
	$rowcount = 5;
} else {
	$rowcount = count($invoices)+5;
}

// ----------------------------------------------- JaVaScript ---------------------------------------------------------
?>
<script type="text/javascript">
$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	
	$('.invc_payable_dat').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
//                showOn: "button",
//                buttonImage: "images/icons/glyphicons-46-calendar.svg",
//                buttonImageOnly: true
			}
     );

	$('.invc_payed_dat').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
//                showOn: "button",
//                buttonImage: "images/icons/glyphicons-46-calendar.svg",
//                buttonImageOnly: true
			}
     );

	$('.invc_crtdat').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
//                showOn: "button",
//                buttonImage: "images/icons/glyphicons-46-calendar.svg",
//                buttonImageOnly: true
			}
     );
 
	$('.filter_from').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
//                showOn: "button",
//                buttonImage: "images/icons/glyphicons-46-calendar.svg",
//                buttonImageOnly: true
			}
     ); // test 123

	$('.filter_to').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
//                showOn: "button",
//                buttonImage: "images/icons/glyphicons-46-calendar.svg",
//                buttonImageOnly: true
			}
     );
});
</script>
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
				Eingangs-&Uuml;bersicht
				<span class="pull-right">
					<?=$savemsg?>
				</span>
			</h3>
	  </div>
	  <div class="panel-body">
			<div class="panel panel-default">
				  <div class="panel-heading">
						<h3 class="panel-title">
							Filteroptionen
						</h3>
				  </div>
				  <div class="panel-body">
					  <form action="index.php?page=<?=$_REQUEST['page']?>" class="form-horizontal" method="post" name="xform_invoicesearch">
						  <input type="hidden" name="subexec" value="search">
						  <input type="hidden" name="mid" value="<?=$_REQUEST["mid"]?>">
						   <div class="row">
							   <div class="col-md-5">
								   <div class="form-group">
									   <label for="" class="col-sm-3 control-label">Lieferant</label>
									   <div class="col-sm-9">
										   <select type="text" id="filter_cust" name="filter_cust" onfocus="markfield(this,0)" onblur="markfield(this,1)" class="form-control">
											   <option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
											       <? foreach ($suppliers as $cust){?>
											   <option value="<?=$cust->getId()?>"
												   <?if ($filters_all["cust_id"] == $cust->getId()) echo "selected" ?>><?=stripslashes($cust->getNameAsLine())?></option>
											   <?	} ?>
										   </select>
									   </div>
								   </div>
							   </div>
							   <div class="col-md-7">
								   <div class="col-sm-2">
									   <label for=" control-label">Zeitraum</label>
								   </div>
								   <div class="col-sm-1">Von</div>
								   <div class="col-sm-4">
									   <input  type="text" name="filter_from" id="filter_from" class="form-control filter_from" value="<?=date("d.m.Y",$filter_from)?>" />
								   </div>
								   <div class="col-sm-1">Bis</div>
								   <div class="col-sm-4">
									   <input  type="text" name="filter_to" id="filter_to" class="form-control filter_to"  value="<?=date("d.m.Y",$filter_to)?>" />
								   </div>
							   </div>
						   </div>
						  <span class="pull-right">
							  <button class="btn btn-md btn-success" type="submit">
								  <?=$_LANG->get('Suche starten')?>
							  </button>
						  </span>
					  </form>
				  </div>


					  <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" class="form-horizontal" name="idx_invcform" id="idx_invcform">
						  <input type="hidden" name="exec" id="exec" value="save">
						  <input type="hidden" name="payed_status" value="<?=$filters_open["payed_status"]?>" />
						  <input type="hidden" name="filter_from" value="<?=date("d.m.Y",$filter_from)?>" />
						  <input type="hidden" name="filter_to" value="<?=date("d.m.Y",$filter_to)?>" />
						  <input type="hidden" name="filter_cust" value="<?=$filters_open["cust_id"]?>" />

						  <div class="panel panel-default">
							  <div class="panel-heading">
								  <h3 class="panel-title">
									  Erfassung / Offen
								  </h3>
							  </div>
							  <div class="table-responsive">
								  <table class="table table-hover">
									  <thead>
									  <tr>
										  <th><?=$_LANG->get('RE-Datum')?></th>
										  <th><?=$_LANG->get('Lieferant / Grund der Ausgabe')?></th>
										  <th><?=$_LANG->get('Vernk. Auftr.')?></th>
										  <th><?=$_LANG->get('Lief')?></th>
										  <th><?=$_LANG->get('Netto')?></th>
										  <th><?=$_LANG->get('MwSt-Satz')?></th>
										  <th><?=$_LANG->get('MwSt')?></th>
										  <th><?=$_LANG->get('Brutto')?></th>
										  <th><?=$_LANG->get('Re-Nummer')?></th>
										  <th><?=$_LANG->get('F&auml;llig')?></th>
										  <th><?=$_LANG->get('Bezahlt')?></th>
										  <th><?=$_LANG->get('SF-rel')?></th>
										  <th>&nbsp;</th>
									  </tr>
									  <? // CSV-Datei der offenen Posten vorbereiten
									  $csv_file_open = fopen($filename1, "w");
									  //fwrite($csv_file, "Firma iPactor - �bersicht\n");

									  //Tabellenkopf der CSV-Datei (offene Posten) schreiben
									  $csv_string_open .= "RE-Datum; Betrag Netto ; MWST ; Brutto ; MWST-Satz; ";
									  $csv_string_open .= "Lieferant; Kreditor-Nr. ; RE-Nr.; Bezahlt am \n"; // Kundennummer beim Lieferant ; Zahlbar bis \n";

									  $xi = 0;
									  $x = 0;
									  foreach ($invoices as $invoice)
									  {
									  $tmp_supp = new BusinessContact($invoice->getInvc_supplierid());
									  // Datei mit den offenen Eingangsrechnungen fuellen
									  $csv_string_open .= date('d.m.Y', $invoice->getInvc_crtdat()).";".printPrice($invoice->getInvc_price_netto()).";";
									  $csv_string_open .= $invoice->getTaxPrice().";".$invoice->getBruttoPrice().";".$invoice->getTaxRate().";";
									  $csv_string_open .= iconv("UTF-8", "cp1252", $tmp_supp->getNameAsLine()).";".$tmp_supp->getKreditor().";";
									  $csv_string_open .= $invoice->getInvc_number().";".$tmp_supp->getNumberatcustomer().";";
									  /* if ($invoice->getInvc_payable_dat() > 0){
                                          $csv_string_open .= date('d.m.Y', $invoice->getInvc_payable_dat());
                                      } */
									  $csv_string_open .= " \n";

									  // if ($invoices[$x]->getInvc_payable_dat() > 0 && $invoices[$x]->getInvc_payable_dat() + 86400 < time())
									  if ($_REQUEST["invc_payable_dat"] > 0 && $_REQUEST["invc_payable_dat"] + 86400 < time())
										  $color = "#F5D5D5";
									  else
										  $color = getRowColor($x);
									  ?>
									  </thead>
									  <tbody>
									  <tr class="<?=$color?>" onmouseover="mark(this, 0)"
										  onmouseout="mark(this, 1)">
										  <td>
											  <input type="text" name="invc_crtdat_<?=$x?>" id="invc_crtdat_<?=$x?>"
													 value="<? if ($invoice->getInvc_crtdat() > 0) echo date('d.m.Y', $invoice->getInvc_crtdat())?>"
													 onfocus="markfield(this,0)" onblur="markfield(this,1)"  class="form-control invc_crtdat" />
										  </td>
										  <td>
											  <input type="hidden" name="invc_existingid_<?=$x?>" name="invc_existingid_<?=$x?>"
													 value="<?=(int)$invoice->getId()?>" />
											  <select class="form-control" name="invc_supplierid_<?=$x?>" id="invc_supplierid_<?=$x?>"
													  onfocus="markfield(this,0)" onblur="markfield(this,1)">
												  <option value="">&lt;<?=$_LANG->get('Lieferant ausw&auml;hlen')?>&gt;</option>
												  <?	foreach($suppliers AS $supplier) { ?>
													  <option value="<?=$supplier->getId()?>"
														  <? if($supplier->getId() == $invoice->getInvc_supplierid()) echo 'selected="selected"'?>><?=$supplier->getNameAsLine()?></option>
												  <?	} 	?>
											  </select>
											  <input type="text" class="form-control" name="invc_title_<?=$x?>" value="<?=$invoice->getInvc_title()?>"
													 onfocus="markfield(this,0)" onblur="markfield(this,1)">
										  </td>
										  <td>
											  <?
											  if ($invoice->getInvc_orders()){
												  foreach ($invoice->getInvc_orders() as $tmp_invc_order){
													  $tmp_order = new Order($tmp_invc_order['id']);
													  echo '<input type="text" class="form-control" value="'.$tmp_invc_order['amount'].'" name="invc_order['.$x.'][amount]"><a href="index.php?page=libs/modules/calculation/order.php&exec=edit&id='.$tmp_order->getId().'&step=6"> '.$tmp_order->getNumber().'</a></br>';
													  echo '<input type="hidden" value="'.$tmp_order->getId().'" name="invc_order['.$x.'][id]">';
													  $xi++;
												  }
												  echo '</br>';
											  }
											  ?>
											  Summe: <input type="text" class="form-control" value="0" name="invc_order[<?=$x?>][amount]"></br>
											  Auftrag: <select class="text" name="invc_order[<?=$x?>][id]">
												  <option value="">&lt;<?=$_LANG->get('Auftrag ausw&auml;hlen')?>&gt;</option>
												  <?	$all_sup_orders = Order::getAllOrdersByCustomer($invoice->getInvc_supplierid());
												  foreach($all_sup_orders AS $tmp_sup_order) { ?>
													  <option value="<?=$tmp_sup_order->getId()?>"><?=$tmp_sup_order->getNumber()?></option>
												  <?	} 	?>
											  </select>
										  </td>
										  <td>
											  <input type="checkbox" class="form-control" name="invc_uses_supplier_<?=$x?>" value="1" <?if($invoice->getInvc_supplierid() > 0) echo 'checked="checked"'?>
													 onclick="if(this.checked)
														 document.getElementById('invc_supplierid_<?=$x?>').style.display='';
														 else
														 document.getElementById('invc_supplierid_<?=$x?>').style.display='none';"
												  <? if((int)$invoice->getInvc_supplierid()) echo "checked='checked'"?>>
										  </td>
										  <td>
											  <input type="text" class="form-control" name="invc_price_netto_<?=$x?>"
													 value="<?=printPrice($invoice->getInvc_price_netto());?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
											  <?=$_USER->getClient()->getCurrency()?>
										  </td>
										  <td>
											  <input type="text" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)"
													 name="invc_taxes_active_<?=$x?>" id="invc_taxes_active_<?=$x?>"	value="<?=$invoice->getInvc_taxes_active();?>" /> %
										  </td>
										  <td>
											  <? if((int)$invoice->getId()) {echo $invoice->getTaxPrice();echo " ".$_USER->getClient()->getCurrency();} else echo "- - - "?>
										  </td>
										  <td>
											  <? if((int)$invoice->getId()) {echo $invoice->getBruttoPrice(); echo " ".$_USER->getClient()->getCurrency();} else echo "- - - "?>
										  </td>
										  <td>
											  <input type="text" class="form-control"	name="invc_number_<?=$x?>" value="<?=$invoice->getInvc_number()?>">
										  </td>
										  <td>
											  <input type="text" name="invc_payable_dat_<?=$x?>" id="invc_payable_dat_<?=$x?>"
													 value="<? if ($invoice->getInvc_payable_dat() > 0) echo date('d.m.Y', $invoice->getInvc_payable_dat())?>"
													 onfocus="markfield(this,0)" onblur="markfield(this,1)" class="form-control invc_payable_dat" />
										  </td>
										  <td>
											  <input type="text" name="invc_payed_dat_<?=$x?>" id="invc_payed_dat_<?=$x?>"
													 value="<? if ($invoice->getInvc_payed_dat() > 0) echo date('d.m.Y', $invoice->getInvc_payed_dat())?>"
													 onfocus="markfield(this,0)" onblur="markfield(this,1)" class="form-control invc_payed_dat" />
										  </td>
										  <td>
											  <input type="checkbox" class="form-control"	name="invc_sepa_activation_<?=$x?>" value="1"
												  <? if (in_array($invoice, $sepa_inv)) echo 'checked="checked"';?>>
										  <td>
											  <ul class="postnav_del_small_invoice">
												  <?	if($invoice->getId()){  ?>
													  <a href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&id=<?=$invoice->getId()?>&exec=del')"><?=$_LANG->get('L&ouml;schen')?></a>

													  <!-- input type="button" class="buttonRed" value="<?=$_LANG->get('L&ouml;schen')?>" onclick="askDel('index.php?pid=<?=$_REQUEST["pid"]?>&id=<?=$invoice->getId()?>&exec=del')"-->
												  <?	} else { ?>
													  <a href="#" ><?=$_LANG->get('L&ouml;schen')?></a>
												  <?	}?>
											  </ul>
										  </td>

									  </tr>
									  <?
									  $x++;

									  }

									  /***
									   * Aus 2 CSV-Listen soll nun (11.02.2013) eine gemacht werden
									   * ***/

									  // Datei mit den offenen Eingangsrechnungen schliessen
									  $csv_string = iconv('UTF-8', 'ISO-8859-1', $csv_string);
									  fwrite($csv_file_open, $csv_string_open);
									  fclose($csv_file_open);

									  // leeere Felder einfuegen

									  for($y=$x;$y<$x+5;$y++){  ?>
										  <tr class="<?=getRowColor($y)?>" onmouseover="mark(this, 0)"
											  onmouseout="mark(this, 1)">
											  <td class="content_row" >
												  <input type="text" name="invc_crtdat_<?=$y?>" id="invc_crtdat_<?=$y?>"
														 onfocus="markfield(this,0)" onblur="markfield(this,1)" class="form-control invc_crtdat" />
											  </td>
											  <td>
												  <input type="hidden" name="invc_existingid_<?=$y?>" name="invc_existingid_<?=$y?>" value=0 />
												  <select class="form-control" name="invc_supplierid_<?=$y?>" id="invc_supplierid_<?=$y?>"
														  onfocus="markfield(this,0)" onblur="markfield(this,1)">
													  <option value="">&lt; <?=$_LANG->get('Lieferant ausw&auml;hlen')?> &gt;</option>
													  <?	foreach($suppliers AS $supplier){?>
														  <option value="<?=$supplier->getId()?>"><?=$supplier->getNameAsLine()?></option>
													  <?	} ?>
												  </select>
												  <input type="text" class="form-control" name="invc_title_<?=$y?>" value=""
														 onfocus="markfield(this,0)" onblur="markfield(this,1)">
											  </td>
											  <td></td>
											  <td>
												  <input type="checkbox" class="form-control" name="invc_uses_supplier_<?=$y?>" value="1" checked="checked"
														 onclick="if(this.checked)
															 document.getElementById('invc_supplierid_<?=$y?>').style.display='';
															 else
															 document.getElementById('invc_supplierid_<?=$y?>').style.display='none';">
											  </td>
											  <td>
												  <div class="input-group">
													  <input type="text" class="form-control" name="invc_price_netto_<?=$y?>" value=""
															 onfocus="markfield(this,0)" onblur="markfield(this,1)">
													  <span class="input-group-addon"><?=$_USER->getClient()->getCurrency()?></span>
												  </div>

											  </td>
											  <td>
												  <div class="input-group">
													  <input class="form-control" name="invc_taxes_active_<?=$y?>" id="invc_taxes_active_<?=$y?>"
															 value="<?=$_USER->getClient()->getTaxes()?>"
															 onfocus="markfield(this,0)" onblur="markfield(this,1)" onclick="this.value=''" />
													  <span class="input-group-addon">%</span>
												  </div>

											  </td>
											  <td><? echo "- - - "?></td>
											  <td><? echo "- - - "?></td>
											  <td>
												  <input type="text" class="form-control"	name="invc_number_<?=$y?>">
											  </td>
											  <td>
												  <input type="text" name="invc_payable_dat_<?=$y?>" id="invc_payable_dat_<?=$y?>" onfocus="markfield(this,0)"
														 onblur="markfield(this,1)" class="form-control invc_payable_dat">
											  </td>
											  <td>
												  <input type="text" name="invc_payed_dat_<?=$y?>" id="invc_payed_dat_<?=$y?>" onfocus="markfield(this,0)"
														 onblur="markfield(this,1)" class="form-control invc_payed_dat">
											  </td>
											  <td>
												  <ul class="postnav_del_small_invoice">
													  <button class="btn btn-xs btn-success" onclick="document.location.href='#';">
														  <?=$_LANG->get('L&ouml;schen')?>
													  </button>
												  </ul>
											  </td>
										  </tr>
									  <?	} ?>
									  </tbody>
								  </table>
							  </div>
						  </div>
						  <div class="panel panel-default">
							  <div class="panel-heading">
								  <h3 class="panel-title">
									  Export
								  </h3>
							  </div>
							  <div class="table-responsive">
								  <table class="table table-hover">
									  <thead>
									  <tr>
										  <th></th>
									  </tr>
									  </thead>
									  <tbody>
									  <tr>
										  <td>
											  <button class="btn btn-xs btn-success" onclick="document.location.href='<?=$filename1?>'" title="<?=$_LANG->get('Offene Posten als CSV-Datei exportieren')?>">
												  <span class="glyphicons glyphicons-calculator"></span><?=$_LANG->get('Export')?>
											  </button>

											  <? /**
											   * Seit 11.02.2014 soll alles in eine Liste geschrieben werden
											   *
											   *
											  <br> <br>
											  <a href="./docs/<?=$_USER->getId()?>-Rechnungseingang_bezahlt.csv"
											  title="<?=$_LANG->get('Bezahlte Posten als CSV-Datei exportieren')?>"
											  ><span class="glyphicons glyphicons-calculator"></span><?=$_LANG->get('Bezahlte Posten')?></a>
											  <?***/?>

										  </td>
										  <td>
											  <?	if($_REQUEST["exec"] == "SEPA_gen"){	// Wenn SEPA-Datei erzeugen, dann Datei zum Download bereitstellen ...
												  if ($number_of_payments > 0){?>
													  &emsp;
													  <button class="btn btn-xs btn-success" onclick="document.location.href='<?=$sepa_filename?>'" title="<?=$_LANG->get('XML-Datei f&uuml;r SFirm &ouml;ffnen')?>">
														  <span class="glyphicons glyphicons-package">SFrim-Export</span>
													  </button>
													  <br/><br/>
												  <? 		}
											  } // Bank auswaehlen ?> &emsp;
										  </td>
										  <input type="hidden" name="bankname" id="bankname"/>
										  <?	if($_USER->getClient()->getBankName() != "" && $_USER->getClient()->getBankName() != FALSE){?>
											  <td>
												  <button class="btn btn-xs btn-success" onclick="document.getElementById('bankname').value='1';document.getElementById('exec').value='SEPA_gen';document.getElementById('idx_invcform').submit();">
													  (<?=substr($_USER->getClient()->getBankName(),0,10);?>) erzeugen"
												  </button>
											  </td>
										  <?	} ?>
										  <?	if($_USER->getClient()->getBankName2() != "" && $_USER->getClient()->getBankName2() != FALSE){?>
											  <td>
												  <button class="btn btn-xs btn-success" onclick="document.getElementById('bankname').value='2';document.getElementById('exec').value='SEPA_gen';document.getElementById('idx_invcform').submit();">
													  (<?=substr($_USER->getClient()->getBankName2(),0,10);?>) erzeugen"
												  </button>
											  </td>
										  <?	} ?>
										  <?	if($_USER->getClient()->getBankName3() != "" && $_USER->getClient()->getBankName3() != FALSE){?>
											  <td>
												  <button class="btn btn-xs btn-success" onclick="document.getElementById('bankname').value='3';document.getElementById('exec').value='SEPA_gen';document.getElementById('idx_invcform').submit();">
													  (<?=substr($_USER->getClient()->getBankName3(),0,10);?>) erzeugen"
												  </button>
											  </td>
										  <?	} ?>
										  <td>
											  <button type="submit" class="btn btn-xs btn-success" >
												  <?=$_LANG->get('Speichern')?>
											  </button>
										  </td>
									  </tr>
									  </tbody>
								  </table>
							  </div>
						  </div>
					  </form>
					  <br />
					  <div class="panel panel-default">
						  <div class="panel-heading">
							  <h3 class="panel-title">
								  Bezahlte Vorg&auml;nge
							  </h3>
						  </div>
						  <div class="table-responsive">
							  <table class="table table-hover">
								  <thead>
								  <tr>
									  <th class="content_row_header" style="color: green"><?=$_LANG->get('Bezahlt am')?></th>
									  <th class="content_row_header"><?=$_LANG->get(' Lieferant / Grund der Ausgabe')?></th>
									  <th class="content_row_header" style="text-align: right"><?=$_LANG->get('Brutto-Betrag')?></th>
									  <th class="content_row_header" style="text-align: right"><?=$_LANG->get('MwSt')?></th>
									  <th class="content_row_header" style="text-align: right"><?=$_LANG->get('MwSt-Betrag')?></th>
									  <th class="content_row_header" style="text-align: right"><?=$_LANG->get('Netto-Betrag')?></th>
									  <th class="content_row_header" style="text-align: right"><?=$_LANG->get('Re-Nummer')?></th>
									  <th class="content_row_header" style="text-align: center"><?=$_LANG->get('RE-Datum')?></th>
									  <th class="content_row_header">&nbsp;</th>
								  </tr>
								  </thead>
								  <tbody>
								  <?  /******
								   * Seit 11.02.2014 soll alles in eine Liste geschrieben werden
								   ********/
								  // CSV-Datei der bezahlten Posten vorbereiten
								  $csv_file_payed = fopen($filename1, "w");
								  // fwrite($csv_file, "Firma iPactor - Übersicht\n");

								  //Tabellenkopf der CSV-Datei (offene Posten) schreiben
								  // 	$csv_string_payed .= "RE-Datum; Betrag Netto ; MWST ; Brutto; MWST-Satz; ";
								  // $csv_string_payed .= "Lieferant; Kreditor-Nr. ; RE-Nr. ; Bezahlt am \n";
								  foreach ($paid as $invoice)
								  {
									  $tmp_supp = new BusinessContact($invoice->getInvc_supplierid());
									  // Datei mit den bezahlten Eingangsrechnungen fuellen
									  $csv_string_payed .= date('d.m.Y', $invoice->getInvc_crtdat()).";".printPrice($invoice->getInvc_price_netto()).";";
									  $csv_string_payed .= $invoice->getTaxPrice().";".$invoice->getBruttoPrice().";".$invoice->getTaxRate().";";
									  $csv_string_payed .= iconv("UTF-8", "cp1252", $tmp_supp->getNameAsLine()).";".$tmp_supp->getKreditor().";";
									  $csv_string_payed .= $invoice->getInvc_number().";";
									  if ($invoice->getInvc_payed_dat() > 0){
										  $csv_string_payed .= date('d.m.Y', $invoice->getInvc_payed_dat());
									  }
									  $csv_string_payed .= " \n";

									  if((int)$invoice->getInvc_taxes_active())
										  $img_status = "status_green.gif";
									  else
										  $img_status = "status_red.gif";

									  if((int)$invoice->getInvc_payed())
										  $img_status2 = "status_green.gif";
									  else
										  $img_status2 = "status_red.gif";
									  ?>
									  <tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)"
										  onmouseout="mark(this,1)">
										  <td class="content_row" style="color: green"><?=date('d.m.Y', $invoice->getInvc_payed_dat())?></td>
										  <td class="content_row"><?=$tmp_supp->getNameAsLine()."<br/>".$invoice->getInvc_title()?></td>
										  <td class="content_row"><?=$invoice->getBruttoPrice()?> <?=$_USER->getClient()->getCurrency()?></td>
										  <td class="content_row"><?=$invoice->getTaxRate()?></td>
										  <td class="content_row"><?=$invoice->getTaxPrice()?> <?=$_USER->getClient()->getCurrency()?></td>
										  <td class="content_row"><?=$invoice->getInvc_price_netto()?> <?=$_USER->getClient()->getCurrency()?></td>
										  <td class="content_row"><?if($invoice->getInvc_number()!="") echo $invoice->getInvc_number(); else echo "- - - ";?></td>
										  <td class="content_row">
											  <? if ($invoice->getInvc_crtdat() > 0) echo date('d.m.Y', $invoice->getInvc_crtdat())?>
										  </td>
										  <td class="content_row">
											  <ul class="postnav_del_small">
												  <a href="#" style="padding:10px 40px 10px 28px;height:26px"
													 onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&&id=<?=$invoice->getId()?>&exec=del')"><?=$_LANG->get('L&ouml;schen')?></a>
											  </ul>
										  </td>
									  </tr>
									  <?
								  }

								  /****
								   * Seit 11.02.2014 soll alles in eine Liste geschrieben werden
								   *****/
								  // Datei mit den offenen Eingangsrechnungen schliessen
								  $csv_string_open .= $csv_string_payed;
								  $csv_string_payed = $csv_string_open;

								  $csv_string_payed = iconv('UTF-8', 'ISO-8859-1', $csv_string_payed);
								  fwrite($csv_file_payed, $csv_string_payed);
								  fclose($csv_file_payed);

								  if(!$paid){  ?>
									  <tr class="<?=getRowColor($x)?>">
										  <td class="content_row" colspan="10" style="text-align: center"><br>
											  <b class="msg_save_err"><?=$_LANG->get('Es sind keine bezahlten Vorg&auml;nge in diesem Monat vorhanden.')?>
											  </b> <br>
											  <br>
										  </td>
									  </tr>
									  <?
								  }
								  ?>
								  </tbody>
							  </table>
						  </div>
					  </div>
				  </div>
			</div>
	  </div>
</div>
<br/>


