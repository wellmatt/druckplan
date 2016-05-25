<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       19.09.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('libs/modules/documents/document.class.php');
require_once('libs/modules/collectiveinvoice/collectiveinvoice.class.php');
require_once('libs/modules/calculation/order.class.php');
require_once('libs/modules/commissioncontact/commissioncontact.class.php');


if ((int)$_REQUEST["filter_from"] == 0){
	$this_month = date("m",time());
	$filter_from = mktime(12,0,0,$this_month,1);
} else {
	$filter_from = strtotime($_REQUEST["filter_from"]);
}
if ((int)$_REQUEST["filter_to"] == 0){
	$filter_to = time();
} else {
	$filter_to = strtotime($_REQUEST["filter_to"]." 23:59:59");
}

$filters= array("module"=>"",
				"type"=>"4",
				"requestId"=>"", 
				"cust_id"=> (int)$_REQUEST["filter_cust"],
				"payed_status"=> (int)$_REQUEST["payed_status"],
				"date_from"=>$filter_from,
				"date_to"=>$filter_to);


if($_REQUEST["exec"] == "save")
{
	foreach(array_keys($_REQUEST) AS $reqkey)
	{

		if(strpos($reqkey, "date_") !== false)
		{
			$idx = substr($reqkey, strrpos($reqkey, "_") +1);
				
			if(!empty($_REQUEST["date_{$idx}"])) {
				$doc = new Document((int)$_REQUEST["doc_existingid_{$idx}"]);
				$doc_payed = strtotime($_REQUEST["date_{$idx}"]);
				$doc->setPayed($doc_payed);
				
			 $ret = $doc->save($idx);
			}
		}
		$savemsg = getSaveMessage($ret);
	}
}

if($_REQUEST["exec"] == "storno"){
	$storno_doc = new Document((int)$_REQUEST["invid"]);
	$storno_doc->setStornoDate(time());
	$ret = $storno_doc->save();
	$savemsg = getSaveMessage($ret);
}

$allcustomer = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, BusinessContact::FILTER_CUST_IST);
$documents= Document::getDocuments($filters);
$sum_netto = 0;
$sum_brutto = 0;
?>

<script type="text/javascript"">
$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	
	$('.date').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "images/icons/glyphicons-46-calendar.svg",
                buttonImageOnly: true
			}
     );
});

$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	
	$('.filter_from').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "images/icons/glyphicons-46-calendar.svg",
                buttonImageOnly: true
			}
     );
});

$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	
	$('.filter_to').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "images/icons/glyphicons-46-calendar.svg",
                buttonImageOnly: true
			}
     );
});
</script>
<table class="standard">
	<tr>
		<td style="height: 30"><nobr><b class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Rechnungs&uuml;bersicht')?></b></nobr></td>
		<td style="text-align: center"><?=$savemsg?></td>
	</tr>
	<tr>
		<td class="content_headerline" style="colspan: 3">&nbsp;</td>
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
				<td class="content_row_header"><?=$_LANG->get('Kunde');?></td>
				<td class="content_row_clear">
					<select type="text" id="filter_cust" name="filter_cust" style="width:150px"
						onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
						<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
					<? 	foreach ($allcustomer as $cust){?>
							<option value="<?=$cust->getId()?>"
								<?if ($filters["cust_id"] == $cust->getId()) echo "selected" ?>><?= $cust->getNameAsLine()?></option>
					<?	} ?>
					</select>
				</td>
				<td class="content_row_header"> <?=$_LANG->get('Zeitraum');?></td>
				<td class="content_row_clear">
					Von <input  type="text" name="filter_from" id="filter_from" class="text date" style="width: 65px" 
								value="<?=date("d.m.Y",$filter_from)?>" />
					Bis <input  type="text" name="filter_to" id="filter_to" class="text date" style="width: 65px" 
								value="<?=date("d.m.Y",$filter_to)?>" />
				</td>
			</tr>
			<tr>
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
			</tr>
			<tr>
				
				<td class="content_row_clear" align="right" colspan="4">
					<input type="submit" value="<?=$_LANG->get('Suche starten')?>">
				</td>
			</tr>
		</table>
	</div>
</form>
<br/>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="idx_invcform"> 
   <input type="hidden" name="exec" value="save" />
   <input type="hidden" name="payed_status" value="<?=$filters["payed_status"]?>" />
   <input type="hidden" name="filter_from" value="<?=date("d.m.Y",$filter_from)?>" />
   <input type="hidden" name="filter_to" value="<?=date("d.m.Y",$filter_to)?>" />
   <input type="hidden" name="filter_cust" value="<?=$filters["cust_id"]?>" />
   
<div class="box1">

<table class="standard" style="padding: 3px">
	<colgroup>
		<col width="80px">
		<col width="30px">
		<col width="85px">
		<col width="70px">
		<col width="70px">
		<col>
		<col>
		<col>
		<col>
		<col width="60px">
		<col width="60px">
		<col width="90px">
		<col width="100px">
	</colgroup>
	<tr>
		<td class="content_row_header" colspan="11"><?=$_LANG->get('Rechnungen')?></td>
	</tr>
	<tr>
		<td class="content_row_header"><?=$_LANG->get('Re-Nr.')?></td>
		<td class="content_row_header"><?=$_LANG->get('Re-Typ')?></td>
		<td class="content_row_header"><?=$_LANG->get('Auftragsnr.')?></td>
		<td class="content_row_header" align="right"><?=$_LANG->get('Brutto')?></td>
		<td class="content_row_header" align="right"><?=$_LANG->get('Netto')?></td>
		<td class="content_row_header"><?=$_LANG->get('Kunde')?></td>
		<td class="content_row_header"><?=$_LANG->get('Provisionspartner')?></td>
		<td class="content_row_header"><?=$_LANG->get('Provision')?></td>
		<td class="content_row_header"><?=$_LANG->get('Titel')?></td>
		<td class="content_row_header"><?=$_LANG->get('erstellt')?></td>
		<td class="content_row_header"><?=$_LANG->get('F&auml;llig')?></td>
		<td class="content_row_header"><?=$_LANG->get('Bezahlt')?></td>
		<td class="content_row_header" align="center"><?=$_LANG->get('Optionen')?></td>
	</tr>

	<? // CSV-Datei der Rechnungen vorbereiten
	$csv_file = fopen('./docs/'.$_USER->getId().'-Rechnungsausgang.csv', "w");
	//fwrite($csv_file, "Firma iPactor - �bersicht\n");
	
	// Tabellenkopf der CSV-Datei (Rechnungen) schreiben
	$csv_string .= "Re-Nr.; Auftragstitel; ";
	$csv_string .= "Betrag Netto ; MWST ; Brutto ;";
	$csv_string .= "Kunde; Debitor-Nr. ; Erstellt; Zahlbar bis; Bezahlt am; Bemerkung \n";

	$x = 0;
	foreach ($documents as $document){  
		
	    $order = null;
	    if($document->getRequestModule()== Document::REQ_MODULE_ORDER){
	        $order = new Order($document->getRequestId());
	    } else if ($document->getRequestModule()== Document::REQ_MODULE_COLLECTIVEORDER){
	        $order = new CollectiveInvoice($document->getRequestId());
	    } 
	    $tmp_mwst = $document->getPriceBrutto()-$document->getPriceNetto();
	    $csv_string .= $document->getName().";".$order->getTitle().";";
	    $csv_string .= printPrice($document->getPriceNetto()).";".printPrice($tmp_mwst).";".printPrice($document->getPriceBrutto()).";";
	    $csv_string .= $order->getCustomer()->getNameAsLine().";".$order->getCustomer()->getDebitor().";";
	    $csv_string .= date("d.m.Y", $document->getCreateDate()).";".date("d.m.Y",$document->getPayable()).";";
	    if($document->getPayed()>0){
			$csv_string .= date("d.m.Y",$document->getPayed());
		}
		$csv_string .= ";";
		if($document->getStornoDate() > 0){
			$csv_string .= " STORNO ";
		}
	    $csv_string .= " \n";
	    
	    if($document->getStornoDate() == 0){
	    	$sum_netto += $document->getPriceNetto();
	    	$sum_brutto += $document->getPriceBrutto();
	    }
	    ?>
	<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)"
		onmouseout="mark(this, 1)">
		
		<td class="content_row" align="center">
			<a href="#" 
			onclick="document.getElementById('idx_iframe_doc').src='libs/modules/documents/document.get.iframe.php?getDoc=<?=$document->getId()?>&version=print'">
				<?=$document->getName() ?>
			</a>
			<input type="hidden" name="doc_existingid_<?=$x?>" name="doc_existingid_<?=$x?>" value="<?=(int)$document->getId()?>" />
			<?if($document->getStornoDate() > 0){?>
				<span class="glyphicons glyphicons-exclamation-sign"
				title="<?=$_LANG->get('Storno am')." ".date("d.m.Y",$document->getStornoDate())?>">
				</span>

			<?}?>
		</td>
		<td class="content_row">
			<?	if($document->getRequestModule()== Document::REQ_MODULE_ORDER) echo $_LANG->get('Kalkulation');
				if($document->getRequestModule()== Document::REQ_MODULE_COLLECTIVEORDER) echo $_LANG->get('Sammel');
			?>
		</td>
		<td class="content_row pointer">
			<?if($document->getRequestModule()== Document::REQ_MODULE_ORDER){?>
				<a href="index.php?page=libs/modules/calculation/order.php&exec=edit&id=<?=$order->getId()?>&step=4"><?=$order->getNumber()?></a>	
			<?}
			if($document->getRequestModule()== Document::REQ_MODULE_COLLECTIVEORDER){?> 
				<a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid=<?=$order->getId()?>"><?=$order->getNumber()?></a>
			<?}?>
		</td>
		<td class="content_row" style="text-align:right"><?=printPrice($document->getPriceBrutto())?> <?=$_USER->getClient()->getCurrency()?></td>
		<td class="content_row" style="text-align:right"><?=printPrice($document->getPriceNetto())?> <?=$_USER->getClient()->getCurrency()?></td>
		<td class="content_row">
			<?=$order->getCustomer()->getNameAsLine()?>
			&nbsp;
		</td>
		<td class="content_row">
			<?
			if ($order->getCustomer()->getCommissionpartner() > 0)
			{
				$tmp_bcontact = new CommissionContact($order->getCustomer()->getCommissionpartner());
				echo $tmp_bcontact->getName1();
			}
			else
			{
				echo 'kein';
			}
			?>
			&nbsp;
		</td>
		<td class="content_row">
			<?
			if ($order->getCustomer()->getCommissionpartner() > 0)
			{
				$tmp_bcontact = new CommissionContact($order->getCustomer()->getCommissionpartner());
				echo printPrice(($document->getPriceBrutto() / 100 * $tmp_bcontact->getProvision()))?> <?=$_USER->getClient()->getCurrency();
			}
			else
			{
				echo '';
			}
			?>
			&nbsp;
		</td>
		<td class="content_row">
			<?=$order->getTitle()?>
			&nbsp;
		</td>
		<td class="content_row"><?=date("d.m.Y", $document->getCreateDate())?></td>
		<td class="content_row" 
		<?php if($document->getPayed()==0){
			if(strtotime(date("d.m.Y 23:59:59",$document->getPayable())) > time()) 
		       echo "style='color:green'"; 
		       else echo "style='color:red'";
		}?>>
		<? echo date("d.m.Y",$document->getPayable());?>&nbsp;</td>
		
		<td class="content_row">
			<input  type="text" name="date_<?=$x?>" id="date_<?=$x?>" 
					class="text date" style="width: 60px" 
					value="<?if($document->getPayed()>0) echo date("d.m.Y",$document->getPayed());?>" />
		</td>
		<td class="content_row">
			<!-- ul class="postnav_save_small_outinvc"><a href="#" 
			onclick="document.getElementById('idx_iframe_doc').src='libs/modules/documents/document.get.iframe.php?getDoc=<?=$document->getId()?>&version=print'">
			<?=$_LANG->get('Anzeigen')?></a>
			</ul-->
			<ul class="postnav_save_small_outinvc">
				<a href="index.php?page=libs/modules/accounting/invoicewarning.php&exec=new&invid=<?=$document->getId()?>"><?=$_LANG->get('Mahnung');?></a>
			</ul>
			<?if($document->getStornoDate() == 0){?>
			<ul class="postnav_save_small_outinvc">
				<a href="#"
					onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=storno&invid=<?=$document->getId()?>')"><?=$_LANG->get('Storno');?>&emsp;&ensp;</a>
			</ul>
			<?} else { ?>
			<br/>
			<?}?>
		</td>
	</tr>
	<? $x++;
	} ?>
	<tr>
		<td colspan="3" class="content_row_header" align="center"><?=$_LANG->get('Gesamtsumme')?></td>
		<td class="content_row_header" style="text-align:right">
			<?=printPrice($sum_brutto);?> <?=$_USER->getClient()->getCurrency()?>
		</td>
		<td class="content_row_header" style="text-align:right">
			<?=printPrice($sum_netto);?> <?=$_USER->getClient()->getCurrency()?>
		</td>
		<td colspan="5" align="right">
			<a href="./docs/<?=$_USER->getId()?>-Rechnungsausgang.csv"  class="icon-link"
					title="Rechnugen als CSV-Datei exportieren"><img src="images/icons/glyphicons-420-disk-export.svg">Export</a>
		</td>
		<td>&ensp;</td>
	</tr>
</table>
</div>

<? // Datei mit den offenen Rechnungen schliessen
	$csv_string .= ";".$_LANG->get('Summe').":;".printPrice($sum_netto).";".printPrice($sum_brutto)."; ; ;";
	$csv_string = iconv('UTF-8', 'ISO-8859-1', $csv_string);
	fwrite($csv_file, $csv_string);
	fclose($csv_file); ?>

<table class="standard">
	<tr>
		<td>&nbsp;</td>
		<td style="text-align: right; width: 130px">
		<input type="submit" class="button" value="<?=$_LANG->get('Speichern')?>" />
		</td>
	</tr>
</table>
</form>
<iframe style="width:1px;height:1px;display:none" id="idx_iframe_doc" src=""></iframe>