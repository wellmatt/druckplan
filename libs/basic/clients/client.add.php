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

	$client->setNumberFormatOrder($_REQUEST["number_format_order"]);
	$client->setNumberCounterOrder($_REQUEST["number_counter_order"]);
	$client->setNumberFormatColinv($_REQUEST["number_format_colinv"]);
	$client->setNumberCounterColinv($_REQUEST["number_counter_colinv"]);
	$client->setNumberFormatOffer($_REQUEST["number_format_offer"]);
	$client->setNumberCounterOffer($_REQUEST["number_counter_offer"]);
	$client->setNumberFormatOfferconfirm($_REQUEST["number_format_offerconfirm"]);
	$client->setNumberCounterOfferconfirm($_REQUEST["number_counter_offerconfirm"]);
	$client->setNumberFormatDelivery($_REQUEST["number_format_delivery"]);
	$client->setNumberCounterDelivery($_REQUEST["number_counter_delivery"]);
	$client->setNumberFormatPaperOrder($_REQUEST["number_format_paper_order"]);
	$client->setNumberCounterPaperOrder($_REQUEST["number_counter_paper_order"]);
	$client->setNumberFormatInvoice($_REQUEST["number_format_invoice"]);
	$client->setNumberCounterInvoice($_REQUEST["number_counter_invoice"]);
	$client->setNumberFormatRevert($_REQUEST["number_format_revert"]);
	$client->setNumberCounterRevert($_REQUEST["number_counter_revert"]);
	$client->setNumberFormatWarning($_REQUEST["number_format_warning"]);
	$client->setNumberCounterWarning($_REQUEST["number_counter_warning"]);
	$client->setNumberFormatWork($_REQUEST["number_format_work"]);
	$client->setNumberCounterWork($_REQUEST["number_counter_work"]);

    $savemsg = getSaveMessage($client->save());
    $savemsg .= $DB->getLastError();
}

?>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="client_form"
	  class="form-horizontal" onsubmit="return checkform(new Array(this.client_name, this.client_street1, this.client_plz, this.client_city))">
	<input type="hidden" name="exec" value="edit">
	<input type="hidden" name="subexec" value="save">
	<input type="hidden" name="id" value="<?=$client->getId()?>">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title"
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
			<? if ($client->getId()) echo $_LANG->get('Mandanten &auml;ndern'); else echo $_LANG->get('Mandanten hinzuf&uuml;gen');?>
			<span class="pull-right"><?=$savemsg?></span>
			</h3>
		</div>
		<div class="panel-body">
				 <div class="row">
					 <div class="col-md-6">

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">Mandant</label>
							 <div class="col-sm-6">
								 <input name="client_name" class="form-control" value="<?=$client->getName()?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">Adresse</label>
							 <div class="col-sm-6">
								 <input name="client_street1" class="form-control" value="<?=$cl_streets[0]?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">Adresse/2</label>
							 <div class="col-sm-6">
								 <input name="client_street2" class="form-control" value="<?=$cl_streets[1]?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">Adresse/3</label>
							 <div class="col-sm-6">
								 <input name="client_street3" class="form-control" value="<?=$cl_streets[2]?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">PLZ / Ort</label>
							 <div class="col-sm-6">
								 <input name="client_postcode" class="form-control" value="<?=$client->getPostcode()?>"
										 onfocus="markfield(this,0)" onblur="markfield(this,1)">
								 <input name="client_city" class="form-control"
									 value="<?=$client->getCity()?>" onfocus="markfield(this,0)"
									 onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">Land</label>
							 <div class="col-sm-6">
								 <select name="client_country" class="form-control">
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
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">Telefon</label>
							 <div class="col-sm-6">
								 <input name="client_phone" class="form-control" value="<?=$client->getPhone()?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">Fax</label>
							 <div class="col-sm-6">
								 <input name="client_fax" class="form-control" value="<?=$client->getFax()?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">E-Mail</label>
							 <div class="col-sm-6">
								 <input name="client_email" class="form-control" value="<?=$client->getEmail()?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">Mandant aktiv</label>
							 <div class="col-sm-6">
								 <input name="client_active" type="checkbox" class="form-control"
										value="1"
									 <? if ($client->isActive() || $_REQUEST["id"] == "") echo "checked";?>
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-4 control-label">Zusätzliche Daten</label>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">Webseite</label>
							 <div class="col-sm-6">
								 <input name="client_website" class="form-control" value="<?=$client->getWebsite()?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">Bank</label>
							 <div class="col-sm-6">
								 <input name="client_bank_name" class="form-control" value="<?=$client->getBankName()?>" onfocus="markfield(this,0)"
										onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">BLZ</label>
							 <div class="col-sm-6">
								 <input name="client_bank_blz" class="form-control" value="<?=$client->getBankBlz()?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">Kontonr</label>
							 <div class="col-sm-6">
								 <input name="client_bank_kto" class="form-control" value="<?=$client->getBankKto()?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">IBAN</label>
							 <div class="col-sm-6">
								 <input name="client_bank_iban" class="form-control" value="<?=$client->getBankIban()?>" onfocus="markfield(this,0)"
										onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">BIC</label>
							 <div class="col-sm-6">
								 <input name="client_bank_bic" class="form-control" value="<?=$client->getBankBic()?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">Amtsgericht</label>
							 <div class="col-sm-6">
								 <input name="client_gericht" class="form-control" value="<?=$client->getGericht()?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">Steuernr</label>
							 <div class="col-sm-6">
								 <input name="client_steuernummer" class="form-control" value="<?=$client->getSteuerNummer()?>" onfocus="markfield(this,0)"
										onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-2 control-label">UstID</label>
							 <div class="col-sm-6">
								 <input name="client_ustid" class="form-control" value="<?=$client->getUstId()?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>


					 </div>

					 <div class="col-md-6">

						 <div class="form-group">
							 <label for="" class="col-sm-3 control-label">Währung</label>
							 <div class="col-sm-6">
								 <input name="client_currency" class="form-control" value="<?=$client->getCurrency()?>">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-3 control-label">Dezimaltrenner</label>
							 <div class="col-sm-6">
								 <input name="client_decimal" class="form-control" value="<?=$client->getDecimal()?>">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-3 control-label">Tausendertrenner</label>
							 <div class="col-sm-6">
								 <input name="client_thousand" class="form-control" value="<?=$client->getThousand()?>">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-3 control-label">Umsatzsteuer</label>
							 <div class="col-sm-6">
								 <div class="input-group">
									 <input name="client_taxes" class="form-control" value="<?=printPrice($client->getTaxes())?>">
									 <span class="input-group-addon">%</span>
								 </div>
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-3 control-label">Standardmarge</label>
							 <div class="col-sm-6">
								 <div class="input-group">
									 <input name="client_margin" class="form-control" value="<?=printPrice($client->getMargin())?>">
									 <span class="input-group-addon">%</span>
								 </div>
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-3 control-label">Bank 2 </label>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-3 control-label">Name</label>
							 <div class="col-sm-6">
								 <input name="client_bank_name2" class="form-control" value="<?=$client->getBankName2()?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-3 control-label">IBAN</label>
							 <div class="col-sm-6">
								 <input name="client_bank_iban2" class="form-control" value="<?=$client->getBankIban2()?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-3 control-label">BIC</label>
							 <div class="col-sm-6">
								 <input name="client_bank_bic2" class="form-control" value="<?=$client->getBankBic2()?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-3 control-label">Bank 3 </label>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-3 control-label">Name</label>
							 <div class="col-sm-6">
								 <input name="client_bank_name3" class="form-control" value="<?=$client->getBankName3()?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-3 control-label">IBAN</label>
							 <div class="col-sm-6">
								 <input name="client_bank_iban3" class="form-control" value="<?=$client->getBankIban3()?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>

						 <div class="form-group">
							 <label for="" class="col-sm-3 control-label">BIC</label>
							 <div class="col-sm-6">
								 <input name="client_bank_bic3" class="form-control" value="<?=$client->getBankBic3()?>"
										onfocus="markfield(this,0)" onblur="markfield(this,1)">
							 </div>
						 </div>


					 </div>
				 </div>
				 <div class="row">
					 <div class="col-md-12">
						 <span class="pull-right">
							 <button class="btn btn-success">
								 <?=$_LANG->get('Speichern')?>
							 </button>
						 </span>
					 </div>

				 </div>

			</br>

			</div>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					Nummernkreise
				</h3>
			</div>
			<div class="table-responsive">
				<table class="table table-hover">
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Betrifft');?></td>
						<td class="content_row_header"><?=$_LANG->get('Format');?></td>
						<td class="content_row_header"><?=$_LANG->get('Counter');?></td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Kalkulation');?></td>
						<td class="content_row_clear">
							<input name="number_format_order" style="width: 300px" class="text" value="<?=$client->getNumberFormatOrder()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
						<td class="content_row_clear">
							<input name="number_counter_order" style="width: 300px" class="text" value="<?=$client->getNumberCounterOrder()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Vorgang');?></td>
						<td class="content_row_clear">
							<input name="number_format_colinv" style="width: 300px" class="text" value="<?=$client->getNumberFormatColinv()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
						<td class="content_row_clear">
							<input name="number_counter_colinv" style="width: 300px" class="text" value="<?=$client->getNumberCounterColinv()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Angebot');?></td>
						<td class="content_row_clear">
							<input name="number_format_offer" style="width: 300px" class="text" value="<?=$client->getNumberFormatOffer()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
						<td class="content_row_clear">
							<input name="number_counter_offer" style="width: 300px" class="text" value="<?=$client->getNumberCounterOffer()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('AB');?></td>
						<td class="content_row_clear">
							<input name="number_format_offerconfirm" style="width: 300px" class="text" value="<?=$client->getNumberFormatOfferconfirm()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
						<td class="content_row_clear">
							<input name="number_counter_offerconfirm" style="width: 300px" class="text" value="<?=$client->getNumberCounterOfferconfirm()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Liefers.');?></td>
						<td class="content_row_clear">
							<input name="number_format_delivery" style="width: 300px" class="text" value="<?=$client->getNumberFormatDelivery()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
						<td class="content_row_clear">
							<input name="number_counter_delivery" style="width: 300px" class="text" value="<?=$client->getNumberCounterDelivery()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Papier Best.');?></td>
						<td class="content_row_clear">
							<input name="number_format_paper_order" style="width: 300px" class="text" value="<?=$client->getNumberFormatPaperOrder()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
						<td class="content_row_clear">
							<input name="number_counter_paper_order" style="width: 300px" class="text" value="<?=$client->getNumberCounterPaperOrder()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Rechnung');?></td>
						<td class="content_row_clear">
							<input name="number_format_invoice" style="width: 300px" class="text" value="<?=$client->getNumberFormatInvoice()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
						<td class="content_row_clear">
							<input name="number_counter_invoice" style="width: 300px" class="text" value="<?=$client->getNumberCounterInvoice()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Gutschrift');?></td>
						<td class="content_row_clear">
							<input name="number_format_revert" style="width: 300px" class="text" value="<?=$client->getNumberFormatRevert()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
						<td class="content_row_clear">
							<input name="number_counter_revert" style="width: 300px" class="text" value="<?=$client->getNumberCounterRevert()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Mahnung');?></td>
						<td class="content_row_clear">
							<input name="number_format_warning" style="width: 300px" class="text" value="<?=$client->getNumberFormatWarning()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
						<td class="content_row_clear">
							<input name="number_counter_warning" style="width: 300px" class="text" value="<?=$client->getNumberCounterWarning()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Tasche');?></td>
						<td class="content_row_clear">
							<input name="number_format_work" style="width: 300px" class="text" value="<?=$client->getNumberFormatWork()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
						<td class="content_row_clear">
							<input name="number_counter_work" style="width: 300px" class="text" value="<?=$client->getNumberCounterWork()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Lief. Bestellung');?></td>
						<td class="content_row_clear">
							<input name="number_format_work" style="width: 300px" class="text" value="<?=$client->getNumberFormatSuporder()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
						<td class="content_row_clear">
							<input name="number_counter_work" style="width: 300px" class="text" value="<?=$client->getNumberCounterSuporder()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</form>
