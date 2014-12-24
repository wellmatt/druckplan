<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       03.05.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('incominginvoicetemplate.class.php');
require_once 'libs/modules/businesscontact/businesscontact.class.php';

if($_REQUEST["exec"] == "del")
{

	$inv = new Incominginvoicetemplate($_REQUEST["id"]);
	$ret = $inv->delete();

	$savemsg = getSaveMessage($ret);

}

if($_SESSION["invoiceem"]["month"] == "")
{
	$_SESSION["invoiceem"]["month"]     = (int)date('m');
	$_SESSION["invoiceem"]["year"]      = (int)date('Y');
	$_SESSION["invoiceem"]["companyid"] = $companies[0]["id"];
}
if($_REQUEST["filter_month"] != "")
$_SESSION["invoiceem"]["month"]  = $_REQUEST["filter_month"];
if($_REQUEST["filter_year"] != "")
$_SESSION["invoiceem"]["year"]   = $_REQUEST["filter_year"];
if($_REQUEST["filter_companyid"] != "")
$_SESSION["invoiceem"]["companyid"] = $_REQUEST["filter_companyid"];

if($_REQUEST["exec"] == "save")
{

	foreach(array_keys($_REQUEST) AS $reqkey)
	{
		if(strpos($reqkey, "invc_title_") !== false)
		{
			$idx = substr($reqkey, strrpos($reqkey, "_") +1);
			if((!empty($_REQUEST["invc_title_{$idx}"])) && (!empty($_REQUEST["invc_price_netto_{$idx}"]))) {
				$inv = new Incominginvoicetemplate((int)$_REQUEST["invc_existingid_{$idx}"]);

				$inv->setInvc_title(trim(addslashes($_REQUEST["invc_title_{$idx}"])));
				$inv->setInvc_taxes_active((int)$_REQUEST["invc_taxes_active_{$idx}"]);
				$inv->setInvc_supplierid((int)$_REQUEST["invc_supplierid_{$idx}"]);
				$invc_price_netto   = $_REQUEST["invc_price_netto_{$idx}"];
				$inv->setInvc_price_netto( (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $invc_price_netto))));
				$inv->setInvc_crtdat(mktime(0, 0, 0, $_SESSION["invoiceem"]["month"], 1, $_SESSION["invoiceem"]["year"]));

				if(!$_REQUEST["invc_uses_supplier_{$idx}"])
				$inv->setInvc_supplierid(0);

				$ret = $inv->save($idx);
			}
		}
		$savemsg = getSaveMessage($ret).$DB->getLastError();
	}
}

$invoices = Incominginvoicetemplate::getAllTemplates();
$suppliers = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME,BusinessContact::FILTER_SUPP);
?>

<table class="standard">
	<tr>
		<td style="height: 30"><nobr><b class="content_header"><img src="<?=$_MENU->getIcon($_SESSION["pid"])?>"> <?=$_LANG->get('Standardrechnungen')?></b></nobr></td>
		<td style="text-align: center"><?=$savemsg?></td>
	</tr>
	<tr>
		<td class="content_headerline" style="colspan: 3">&nbsp;</td>
	</tr>
</table>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="idx_invcform">
<div class="box1">
  <input type="hidden" name="exec" value="save" />
<table class="standard" style="padding: 3px">
	<colgroup>
		<col>
		<col width="30px">
		<col width="90px">
		<col width="60px">
		<col width="90px">
		<col width="90px">
		<col width="110px">
		<col width="100px">
	</colgroup>
	<tr>
		<td class="content_row_header" colspan="9"><?=$_LANG->get('Erfassung / Offen')?></td>
	</tr>
	<tr>
		<td class="content_row_header"><?=$_LANG->get('Grund der Ausgabe')?></td>
		<td class="content_row_header"><?=$_LANG->get('Lief')?></td>
		<td class="content_row_header"><?=$_LANG->get('Netto-Betrag')?></td>
		<td class="content_row_header"><?=$_LANG->get('MwSt-Satz')?></td>
		<td class="content_row_header" style="text-align: right"><?=$_LANG->get('MwSt-Betrag')?></td>
		<td class="content_row_header" style="text-align: right"><?=$_LANG->get('Brutto-Betrag')?></td>
		<td class="content_row_header">&nbsp;</td>
		<td class="content_row_header">&nbsp;</td>
	</tr>

	<?

	$x = 0;
	foreach ($invoices as $invoice)
	{  ?>
	<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)"
		onmouseout="mark(this, 1)">
		<td class="content_row pointer"><input type="text" class="text"
			style="width: 220px" name="invc_title_<?=$x?>"
			value="<?=$invoice->getInvc_title()?>" onfocus="markfield(this,0)"
			onblur="markfield(this,1)" /> <input type="hidden"
			name="invc_existingid_<?=$x?>" value="<?=$invoice->getId()?>" /> <select class="text" style="width:220px;margin-top:3px;<? if(!$invoice->getInvc_supplierid()) echo "display:none"?>"
         name="invc_supplierid_<?=$x?>" id="invc_supplierid_<?=$x?>"
         onfocus="markfield(this,0)" onblur="markfield(this,1)">
			<option value="">&lt;<?=$_LANG->get('Lieferant ausw�hlen')?>&gt;</option>
			<?
	foreach($suppliers AS $supplier)
			{
			?>
			<option value="<?=$supplier->getId()?>"
			<? if($supplier->getId() == $invoice->getInvc_supplierid()) echo 'selected="selected"'?>><?=$supplier->getNameAsLine()?></option>
			<?
			}
			?>
		</select></td>
		<td class="content_row pointer" align="center"><input type="checkbox"
			name="invc_uses_supplier_<?=$x?>" value="1"
			onclick="if(this.checked)
                     document.getElementById('invc_supplierid_<?=$x?>').style.display='';
                  else
                     document.getElementById('invc_supplierid_<?=$x?>').style.display='none';"
                     <? if($invoice->getInvc_supplierid()) echo "checked='checked'"?> />
		</td>
		<td class="content_row pointer"><input type="text" class="text"
			style="width: 65px;text-align: right" name="invc_price_netto_<?=$x?>"
			value="<?echo printPrice($invoice->getInvc_price_netto());?> "
			onfocus="markfield(this,0)" onblur="markfield(this,1)" /> <?=$_USER->getClient()->getCurrency()?></td>
		<td class="content_row pointer" style="text-align: center">
		<input type="text" class="text"
			style="width: 30px;text-align:right" name="invc_taxes_active_<?=$x?>" id="invc_taxes_active_<?=$x?>"
			value="<?=$invoice->getInvc_taxes_active();?>"
			onfocus="markfield(this,0)" onblur="markfield(this,1)" /> %
		</td>
		<td class="content_row pointer" style="text-align: right"><?if($invoice->getInvc_price_netto()) {echo $invoice->getTaxPrice();echo " ".$_USER->getClient()->getCurrency();}?></td>
		<td class="content_row pointer" style="text-align: right"><?if($invoice->getInvc_price_netto()) {echo $invoice->getBruttoPrice();echo " ".$_USER->getClient()->getCurrency();}?></td>
		<td class="content_row pointer">&nbsp;</td>
		<td class="content_row pointer"><?
		if($invoice->getId())
		{  ?>
		<ul class="postnav_del" style="margin-top: 7px;">
			<a href="#"
				onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&id=<?=$invoice->getId()?>&exec=del')"><?=$_LANG->get('L&ouml;schen')?></a>
		</ul>
<?
		}
		else
		echo "&nbsp;";
		?></td>
	</tr>
	<?
	$x++;
	}


	$y=0;
	for($y=$x;$y<$x+5;$y++)
	{  ?>
	<tr class="<?=getRowColor($y)?>" onmouseover="mark(this, 0)"
		onmouseout="mark(this, 1)">
		<td class="content_row pointer"><input type="text" class="text"
			style="width: 220px" name="invc_title_<?=$y?>" value=""
			onfocus="markfield(this,0)" onblur="markfield(this,1)"> <input
			type="hidden" name="invc_existingid_<?=$y?>"
			name="invc_existingid_<?=$y?>" value=0 /> <select class="text"
			style="width: 220px; margin-top: 3px; display: none"
			name="invc_supplierid_<?=$y?>" id="invc_supplierid_<?=$y?>"
			onfocus="markfield(this,0)" onblur="markfield(this,1)">
			<option value="">&lt; <?=$_LANG->get('Lieferant ausw&auml;hlen')?> &gt;</option>
			<?
	foreach($suppliers AS $supplier)
			{
			?>
			<option value="<?=$supplier->getId()?>"><?=$supplier->getNameAsLine()?></option>
			<?
			}
			?>
		</select></td>
		<td class="content_row pointer" align="center"><input type="checkbox"
			name="invc_uses_supplier_<?=$y?>" value="1"
			onclick="if(this.checked)
                     document.getElementById('invc_supplierid_<?=$y?>').style.display='';
                  else
                     document.getElementById('invc_supplierid_<?=$y?>').style.display='none';">
		</td>
		<td class="content_row pointer"><input class="text"
			style="width: 65px;text-align: right" name="invc_price_netto_<?=$y?>" value=""
			onfocus="markfield(this,0)" onblur="markfield(this,1)">  <?=$_USER->getClient()->getCurrency()?></td>
		<td class="content_row pointer" style="text-align: center"><input class="text"
			style="width: 30px;text-align: right" name="invc_taxes_active_<?=$y?>" id="invc_taxes_active_<?=$y?>"
			value="<?=$_USER->getClient()->getTaxes()?>"
			onfocus="markfield(this,0)" onblur="markfield(this,1)" onclick="this.value=''" /> %
		</td>
		<td class="content_row pointer" style="text-align: right"><? echo "- - - "?></td>
		<td class="content_row pointer" style="text-align: right"><? echo "- - - "?></td>
		<td class="content_row pointer">&nbsp;</td>
		<td class="content_row pointer">
		<ul class="postnav_del" style="margin-top:7px;">
			<a href="#" style="visibility: hidden"><?=$_LANG->get('L&ouml;schen')?></a>
		</ul>
		</td>
	</tr>
	<?
	}
	?>

</table>
</div>
<table class="standard">
	<tr>
		<td>&nbsp;</td>
		<td style="text-align: right; width: 130px"><input type="submit"
			class="button" value="<?=$_LANG->get('Speichern')?>"
			 />
		</td>
	</tr>
</table>
</form>