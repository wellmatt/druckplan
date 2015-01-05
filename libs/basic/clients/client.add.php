<?
$_REQUEST["id"] = (int)$_REQUEST["id"];
$client = new Client($_REQUEST["id"]);
$cl_streets = $client->getStreets();

if($_REQUEST["subexec"] == "save")
{
    $cl_streets[0] = trim(addslashes($_REQUEST["client_street1"]));
    $cl_streets[1] = trim(addslashes($_REQUEST["client_street2"]));
    $cl_streets[2] = trim(addslashes($_REQUEST["client_street3"]));
    $client->setName(trim(addslashes($_REQUEST["client_name"])));
    $client->setStreets($cl_streets);
    $client->setPostcode(trim(addslashes($_REQUEST["client_postcode"])));
    $client->setCity(trim(addslashes($_REQUEST["client_city"])));
    $client->setPhone(trim(addslashes($_REQUEST["client_phone"])));
    $client->setFax(trim(addslashes($_REQUEST["client_fax"])));
    $client->setEmail(trim(addslashes($_REQUEST["client_email"])));
    $client->setActive((int)$_REQUEST["client_active"]);
    $client->setWebsite(trim(addslashes($_REQUEST["client_website"])));
    $client->setBankName(trim(addslashes($_REQUEST["client_bank_name"])));
    $client->setBankBlz(trim(addslashes($_REQUEST["client_bank_blz"])));
    $client->setBankKto(trim(addslashes($_REQUEST["client_bank_kto"])));
    $client->setBankIban(trim(addslashes($_REQUEST["client_bank_iban"])));
    $client->setBankBic(trim(addslashes($_REQUEST["client_bank_bic"])));
    $client->setGericht(trim(addslashes($_REQUEST["client_gericht"])));
    $client->setSteuerNummer(trim(addslashes($_REQUEST["client_steuernummer"])));
    $client->setUstId(trim(addslashes($_REQUEST["client_ustid"])));
    $client->setCountry(new Country((int)$_REQUEST["client_country"]));
    $client->setCurrency(trim(addslashes($_REQUEST["client_currency"])));
    $client->setDecimal(trim(addslashes($_REQUEST["client_decimal"])));
    $client->setThousand(trim(addslashes($_REQUEST["client_thousand"])));
    $client->setTaxes((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["client_taxes"]))));
    $client->setMargin((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["client_margin"]))));
    $client->setBankName2(trim(addslashes($_REQUEST["client_bank_name2"])));
    $client->setBankIban2(trim(addslashes($_REQUEST["client_bank_iban2"])));
    $client->setBankBic2(trim(addslashes($_REQUEST["client_bank_bic2"])));
    $client->setBankName3(trim(addslashes($_REQUEST["client_bank_name3"])));
    $client->setBankIban3(trim(addslashes($_REQUEST["client_bank_iban3"])));
    $client->setBankBic3(trim(addslashes($_REQUEST["client_bank_bic3"])));
    $savemsg = getSaveMessage($client->save());
    $savemsg .= $DB->getLastError();
}

?>
<table width="100%">
	<tr>
		<td width="200" class="content_header"><img
			src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <? if ($client->getId()) echo $_LANG->get('Mandanten &auml;ndern'); else echo $_LANG->get('Mandanten hinzuf&uuml;gen');?>
		</td>
		<td></td>
		<td width="200" class="content_header" align="right"><?=$savemsg?></td>
	</tr>
</table>
<div class="box1">
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="client_form"
	onsubmit="return checkform(new Array(this.client_name, this.client_street1, this.client_plz, this.client_city))">
<input type="hidden" name="exec" value="edit"> 
<input type="hidden" name="subexec" value="save"> 
<input type="hidden" name="id" value="<?=$client->getId()?>">
<table width="100%">
<tr>
<td valign="top">
	<table width="490px" border="0" cellpadding="0" cellspacing="0">
		<colgroup>
			<col width="180">
			<col>
		</colgroup>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Mandant');?> *</td>
			<td class="content_row_clear"><input name="client_name"
				style="width: 300px" class="text" value="<?=$client->getName()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Adresse');?> 1 *</td>
			<td class="content_row_clear"><input name="client_street1"
				style="width: 300px" class="text" value="<?=$cl_streets[0]?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Adresse');?> 2</td>
			<td class="content_row_clear"><input name="client_street2"
				style="width: 300px" class="text" value="<?=$cl_streets[1]?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Adresse');?> 3</td>
			<td class="content_row_clear"><input name="client_street3"
				style="width: 300px" class="text" value="<?=$cl_streets[2]?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('PLZ / Ort');?> *</td>
			<td class="content_row_clear"><input name="client_postcode"
				style="width: 60px" class="text" value="<?=$client->getPostcode()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)"> <input
				name="client_city" style="width: 236px" class="text"
				value="<?=$client->getCity()?>" onfocus="markfield(this,0)"
				onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Land')?></td>
			<td class="content_row_header"><select name="client_country"
				style="width: 300px;" class="text">
					<? 
					$countries = Country::getAllCountries();

					foreach($countries as $co)
					{
					    ?>
					<option value="<?=$co->getId()?>"
					<?if($client->getCountry()->getId() == $co->getId()) echo "selected";?>>
						<?=$co->getName()?>
					</option>
					<? } ?>
			</select>
			</td>
		</tr>

		<tr>
			<td class="content_row_header"><?=$_LANG->get('Telefon');?></td>
			<td class="content_row_clear"><input name="client_phone"
				style="width: 300px" class="text" value="<?=$client->getPhone()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Fax');?></td>
			<td class="content_row_clear"><input name="client_fax" style="width: 300px"
				class="text" value="<?=$client->getFax()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('E-Mail');?></td>
			<td class="content_row_clear"><input name="client_email"
				style="width: 300px" class="text" value="<?=$client->getEmail()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Mandant aktiv')?></td>
			<td class="content_row_clear"><input name="client_active" type="checkbox"
				value="1"
				<? if ($client->isActive() || $_REQUEST["id"] == "") echo "checked";?>
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header">&nbsp;</td>
			<td class="content_row_clear">&nbsp;</td>
		</tr>
		<tr>
			<td class="content_header"><?=$_LANG->get('Zus&auml;tzliche Daten')?>
			</td>
			<td class="content_row_clear">&nbsp;</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Webseite');?></td>
			<td class="content_row_clear"><input name="client_website"
				style="width: 300px" class="text" value="<?=$client->getWebsite()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Bank');?></td>
			<td class="content_row_clear"><input name="client_bank_name"
				style="width: 300px" class="text"
				value="<?=$client->getBankName()?>" onfocus="markfield(this,0)"
				onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('BLZ');?></td>
			<td class="content_row_clear"><input name="client_bank_blz"
				style="width: 300px" class="text" value="<?=$client->getBankBlz()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Kontonummer');?></td>
			<td class="content_row_clear"><input name="client_bank_kto"
				style="width: 300px" class="text" value="<?=$client->getBankKto()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('IBAN');?></td>
			<td class="content_row_clear"><input name="client_bank_iban"
				style="width: 300px" class="text"
				value="<?=$client->getBankIban()?>" onfocus="markfield(this,0)"
				onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('BIC');?></td>
			<td class="content_row_clear"><input name="client_bank_bic"
				style="width: 300px" class="text" value="<?=$client->getBankBic()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Amtsgericht');?></td>
			<td class="content_row_clear"><input name="client_gericht"
				style="width: 300px" class="text" value="<?=$client->getGericht()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Steuernummer');?></td>
			<td class="content_row_clear"><input name="client_steuernummer"
				style="width: 300px" class="text"
				value="<?=$client->getSteuerNummer()?>" onfocus="markfield(this,0)"
				onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('UstID');?></td>
			<td class="content_row_clear"><input name="client_ustid"
				style="width: 300px" class="text" value="<?=$client->getUstId()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>

	</table>
</td>
<td valign="top">
    <table width="490px" border="0" cellpadding="0" cellspacing="0">
        <colgroup>
    	    <col width="180">
			<col>
	    </colgroup>
        <tr>
            <td class="content_row_header"><?=$_LANG->get('W&auml;hrung')?></td>
            <td class="content_row_clear"><input name="client_currency" class="text" style="width:100px" value="<?=$client->getCurrency()?>"></td>
        </tr>
        <tr>
            <td class="content_row_header"><?=$_LANG->get('Dezimaltrenner')?></td>
            <td class="content_row_clear"><input name="client_decimal" class="text" style="width:100px" value="<?=$client->getDecimal()?>"></td>
        </tr>
        <tr>
            <td class="content_row_header"><?=$_LANG->get('Tausendertrenner')?></td>
            <td class="content_row_clear"><input name="client_thousand" class="text" style="width:100px" value="<?=$client->getThousand()?>"></td>
        </tr>      
        <tr>
            <td class="content_row_header"><?=$_LANG->get('Umsatzsteuer')?></td>
            <td class="content_row_clear"><input name="client_taxes" class="text" style="width:100px" value="<?=printPrice($client->getTaxes())?>"> %</td>
        </tr>
        <tr>
            <td class="content_row_header"><?=$_LANG->get('Standardmarge')?></td>
            <td class="content_row_clear"><input name="client_margin" class="text" style="width:100px" value="<?=printPrice($client->getMargin())?>"> %</td>
        </tr>
		<tr>
			<td class="content_row_header"  style="height:136px;">&emsp;</td>
			<td class="content_row_clear">&emsp;</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Bank 2');?></td>
			<td class="content_row_clear">&nbsp;</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Name');?></td>
			<td class="content_row_clear">
				<input name="client_bank_name2" style="width: 300px" class="text" value="<?=$client->getBankName2()?>" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('IBAN');?></td>
			<td class="content_row_clear">
				<input name="client_bank_iban2" style="width: 300px" class="text" value="<?=$client->getBankIban2()?>" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('BIC');?></td>
			<td class="content_row_clear">
				<input name="client_bank_bic2" style="width: 300px" class="text" value="<?=$client->getBankBic2()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header" style="height:26px;">&emsp;</td>
			<td class="content_row_clear">&nbsp;</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Bank 3');?></td>
			<td class="content_row_clear">&nbsp;</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Name');?></td>
			<td class="content_row_clear">
				<input name="client_bank_name3" style="width: 300px" class="text" value="<?=$client->getBankName3()?>" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('IBAN');?></td>
			<td class="content_row_clear">
				<input name="client_bank_iban3" style="width: 300px" class="text" value="<?=$client->getBankIban3()?>" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('BIC');?></td>
			<td class="content_row_clear">
				<input name="client_bank_bic3" style="width: 300px" class="text" value="<?=$client->getBankBic3()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
    </table>
</td>
</tr>
<tr>
	<td class="content_row_header">&nbsp;</td>
	<td class="content_row_clear" align="right"><input type="submit"
		value=<?=$_LANG->get('Speichern')?> />
	</td>
</tr>
</table>
</div>
</form>