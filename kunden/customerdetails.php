<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.06.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------

$busicon = $_SESSION["businesscontact"]; // gln hier Umbau auf globale Variable aus index.php noetig

$address = new Address();
$r_address = new Address();

if($_REQUEST["subexec"] == "save"){
	
	// Standart-Clienten setzen, meist ID=1
	$client = new Client(1);
	
	/*$busicon =  $_SESSION["businesscontact"];
	$busicon->setName1(trim(addslashes($_REQUEST["name1"])));
	$busicon->setName2(trim(addslashes($_REQUEST["name2"])));
	$busicon->setAddress1(trim(addslashes($_REQUEST["adress1"])));
	$busicon->setAddress2(trim(addslashes($_REQUEST["adress2"])));
	$busicon->setCity(trim(addslashes($_REQUEST["city"])));
	$busicon->setZip(trim(addslashes($_REQUEST["plz"])));
	$busicon->setCountry(new Country (trim(addslashes($_REQUEST["country"]))));
	//$busicon->setLanguage(new Translator((int)$_REQUEST["language"]));
	
	$busicon->setPhone(trim(addslashes($_REQUEST["phone"])));
	$busicon->setEmail(trim(addslashes($_REQUEST["email"])));
	$busicon->setFax(trim(addslashes($_REQUEST["fax"])));
	$busicon->setWeb(trim(addslashes($_REQUEST["web"])));
	
	//$busicon->setShoplogin(trim(addslashes($_REQUEST["cust_username"])));
	//$busicon->setShoppass(trim(addslashes($_REQUEST["cust_password"])));
	
	$busicon->setClient($client);
	$busicon->setSupplier(0);
	$busicon->setCustomer(1);
	$busicon->setActive(1);
	$busicon->setLanguage(new Translator(22));

	$busicon->save();
	
	$savemsg = $_LANG->get("Erfolgreich gespeichert");
	
	$_SESSION["businesscontact"] = $busicon; 
	*/
	//gln, 11.02.14: neue Lieferadresse hinzufuegen
	if (strlen($_REQUEST["name1"]) > 0){ 
		$address->setActive(2);
		$address->setShoprel(1);	//gln
    	$address->setName1(trim(addslashes($_REQUEST["name1"])));
	    $address->setName2(trim(addslashes($_REQUEST["name2"])));
    	$address->setAddress1(trim(addslashes($_REQUEST["address1"])));
	    $address->setAddress2(trim(addslashes($_REQUEST["address2"])));
	    $address->setZip(trim(addslashes($_REQUEST["zip"])));
	    $address->setCity(trim(addslashes($_REQUEST["city"])));
	    $address->setMobil(trim(addslashes($_REQUEST["mobil"])));
	    $address->setPhone(trim(addslashes($_REQUEST["phone"])));
	    $address->setFax(trim(addslashes($_REQUEST["fax"])));
	    $address->setCountry(new Country (trim(addslashes($_REQUEST["country"]))));
	    $address->setBusinessContact($busicon);

		$res = getSaveMessage($address->save());
		if($res){
			$savemsg = $_LANG->get("Erfolgreich gespeichert");
		}
	}
	//ascherer, 16.09.14: neue Rechnungsadresse hinzufuegen
	if (strlen($_REQUEST["r_name1"]) > 0){ 
		$r_address->setActive(1);
		$r_address->setShoprel(1);	//gln
    	$r_address->setName1(trim(addslashes($_REQUEST["r_name1"])));
	    $r_address->setName2(trim(addslashes($_REQUEST["r_name2"])));
    	$r_address->setAddress1(trim(addslashes($_REQUEST["r_address1"])));
	    $r_address->setAddress2(trim(addslashes($_REQUEST["r_address2"])));
	    $r_address->setZip(trim(addslashes($_REQUEST["r_zip"])));
	    $r_address->setCity(trim(addslashes($_REQUEST["r_city"])));
	    $r_address->setMobil(trim(addslashes($_REQUEST["r_mobil"])));
	    $r_address->setPhone(trim(addslashes($_REQUEST["r_phone"])));
	    $r_address->setFax(trim(addslashes($_REQUEST["r_fax"])));
	    $r_address->setCountry(new Country (trim(addslashes($_REQUEST["r_country"]))));
	    $r_address->setBusinessContact($busicon);

		$res = getSaveMessage($r_address->save());
		if($res){
			$savemsg = $_LANG->get("Erfolgreich gespeichert");
		}
	}
    //$savemsg = getSaveMessage($address->save());
    //$savemsg .= $DB->getLastError();
	echo $savemsg;
	// refresh page ?>
	<script language="JavaScript">
		location.href = 'index.php?pid=<?=$_REQUEST["pid"]?>';
	</script><?
}

$countries = Country::getAllCountries();
$languages = Translator::getAllLangs(Translator::ORDER_NAME);
//gln $all_deliveryAddresses = Address::getAllAddresses($busicon, Address::ORDER_NAME, Address::FILTER_DELIV);
$all_deliveryAddresses = Address::getAllAddresses($busicon, Address::ORDER_NAME, Address::FILTER_DELIV_SHOP);
$all_invoiceAddresses = Address::getAllAddresses($busicon, Address::ORDER_NAME, Address::FILTER_INVC);
?>

<script language="javascript">
function askDel(myurl)
{
   if(confirm("Sind Sie sicher?"))
   {
      if(myurl != '')
         location.href = myurl;
      else
         return true;
   }
   return false;
}
</script>
<form id="change_customer" method="post">
	<input type="hidden" name="pid" value="<?=$_REQUEST["pid"]?>" >
	<?/*<input type="hidden" name="subexec" value="save" > */?>
	<div class="panel panel-default">
		  <div class="panel-heading">
				<h3 class="panel-title">
					<b>Profil</b>
				</h3>
		  </div>
		  <div class="panel-body">
			  <div class="table-responsive">
				  <table class="table table-hover">
					  <tr>
						  <td colspan="2"><b><?=$_LANG->get('Adressdaten');?> </b></td>
					  </tr>
					  <tr>
						  <td><?=$_LANG->get('Firmenname');?></td>
						  <td><?/*gln, 05.02.14 <input type="text" name="name1" class="text" value=""/>*/?><?=$busicon->getName1()?> </td>
						  <td>&ensp;</td>
					  </tr>
					  <tr>
						  <td><?=$_LANG->get('Firmenname (Zusatz)');?></td>
						  <td><?/*gln, 05.02.14 <input type="text" name="name2" class="text" value="" />*/?><?=$busicon->getName2()?></td>
					  </tr>
					  <tr>
						  <td><?=$_LANG->get('Stra&szlig;e');?></td>
						  <td><?/*gln, 05.02.14 <input type="text" name="adress1" class="text" value="" />*/?><?=$busicon->getAddress1()?></td>
					  </tr>
					  <tr>
						  <td>&ensp;</td>
						  <td><?/*gln, 05.02.14 <input type="text" name="adress2" class="text" value="*/?><?=$busicon->getAddress2()?></td>
					  </tr>
					  <tr>
						  <td><?=$_LANG->get('PLZ/Stadt');?></td>
						  <td>
							  <?/*gln, 05.02.14 <input type="text" name="plz" class="text" style="width:60px"  value=""/> */?><?=$busicon->getZip()?>
							  <?/*gln, 05.02.14 <input type="text" name="city" class="text" style="width:174px" value=""/>*/?><?=$busicon->getCity()?>
						  </td>
					  </tr>
					  <tr>
						  <td><?=$_LANG->get('Land');?></td>
						  <td>
							  <?/*gln, 05.02.14 <select name="country" style="width:240px"
					class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
	<? 					foreach($countries as $c) { ?>
						<option value="<?=$c->getId()?>"
						<?if ($busicon->getCountry()->getId() == $c->getId()) echo "selected";?>>
							<?=$c->getName()?>
						</option>
	<?					} ?>
				</select> */?> <?$c=new Country($busicon->getCountry()->getId()); echo $c->getName()?>
						  </td>
					  </tr>
					  <tr><td colspan="3">&ensp;</td></tr>
					  <tr><td colspan="3"><b><?=$_LANG->get('Kontaktdaten');?></b></td></tr>
					  <tr>
						  <td><?=$_LANG->get('E-Mail');?></td>
						  <td><?/*<input type="text" name="email" class="text" value="<?=$busicon->getEmail()?>" />*/?><?=$busicon->getEmail()?></td>
					  </tr>
					  <tr>
						  <td><?=$_LANG->get('Telefon');?></td>
						  <td><?/*<input type="text" name="phone" class="text" value="<?=$busicon->getPhone()?>" />*/?><?=$busicon->getPhone()?></td>
					  </tr>
					  <tr>
						  <td><?=$_LANG->get('Fax');?></td>
						  <td><?/*<input type="text" name="fax" class="text" value="<?=$busicon->getFax()?>" />*/?><?=$busicon->getFax()?></td>
					  </tr>
					  <tr>
						  <td><?=$_LANG->get('Web');?></td>
						  <td><?/*<input type="text" name="web" class="text" value="<?=$busicon->getWeb()?>" />*/?><?=$busicon->getWeb()?></td>
					  </tr>
					  <tr><td colspan="3">&ensp;</td></tr>
					  <tr><td colspan="3">&ensp;</td></tr>

					  <!-- tr>
	    	<td><b><?=$_LANG->get("Sprache")?></b></td>
	    	<td>
	    		<select name="language" style="width: 243px"
					class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						<?
					  foreach($languages as $l)
					  {?>
						<option value="<?=$l->getId()?>"
						<?if ($busicon->getLanguage()->getId() == $l->getId()) echo "selected";?>>
							<?=$l->getName()?>
						</option>
						<?}

					  ?>
				</select>
			</td>
	    </tr -->
					  <tr>
						  <td>&ensp;</td>
						  <?/*
	        <td align="right">
	        	<input type="submit" name="submit" style="width:80px" value="<?=$_LANG->get('Speichern');?>" />
	        </td>
	        <td>&ensp;</td>
	*/?>
					  </tr>
				  </table>
			  </div>
</form>
			  <form method="post" id="neue_lieferadr" >
					<input type="hidden" name="subexec" value="save" >
					<div class="panel panel-default">
						  <div class="panel-heading">
								<h3 class="panel-title">
									<b>Neue Lieferadresse</b>
								</h3>
						  </div>
							  <div class="table-responsive">
								  <table class="table table-hover">
									  <tr>
										  <td class="content_row_header"><?=$_LANG->get('Firma');?> *</td>
										  <td class="content_row_clear"><input name="name1" style="width: 300px"
																			   class="text" value="<?=$address->getName1()?>"
																			   onfocus="markfield(this,0)" onblur="markfield(this,1)">
										  </td>
									  </tr>
									  <tr>
										  <td class="content_row_header"><?=$_LANG->get('Firmenzusatz');?></td>
										  <td class="content_row_clear"><input name="name2"
																			   style="width: 300px" class="text" value="<?=$address->getName2()?>"
																			   onfocus="markfield(this,0)" onblur="markfield(this,1)">
										  </td>
									  </tr>
									  <tr>
										  <td class="content_row_header"><?=$_LANG->get('Adresse');?>
										  </td>
										  <td class="content_row_clear"><input name="address1"
																			   style="width: 300px" class="text" value="<?=$address->getAddress1()?>"
																			   onfocus="markfield(this,0)" onblur="markfield(this,1)">
										  </td>
									  </tr>
									  <tr>
										  <td class="content_row_header"><?=$_LANG->get('Adresszusatz');?>
										  </td>
										  <td class="content_row_clear"><input name="address2"
																			   style="width: 300px" class="text" value="<?=$address->getAddress2()?>"
																			   onfocus="markfield(this,0)" onblur="markfield(this,1)">
										  </td>
									  </tr>
									  <tr>
										  <td class="content_row_header"><?=$_LANG->get('PLZ / Stadt');?>
										  </td>
										  <td class="content_row_clear">
											  <nobr>
												  <input name="zip" style="width: 50px" class="text" value="<?=$address->getZip()?>"
														 onfocus="markfield(this,0)" onblur="markfield(this,1)">
												  <input name="city" style="width: 243px" class="text" value="<?=$address->getCity()?>"
														 onfocus="markfield(this,0)" onblur="markfield(this,1)">
											  </nobr>
										  </td>
									  </tr>
									  <tr>
										  <td class="content_row_header"><?=$_LANG->get('Land')?></td>
										  <td class="content_row_clear"><select name="country" style="width: 300px"
																				class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
												  <?
												  foreach($countries as $c)
												  {?>
													  <option value="<?=$c->getId()?>"
														  <?if ($address->getCountry()->getId() == $c->getId()) echo "selected";?>>
														  <?=$c->getName()?>
													  </option>
												  <?}?>
											  </select>
										  </td>
									  </tr>
									  <tr>
										  <td class="content_row_header">&nbsp;</td>
										  <td class="content_row_clear">&nbsp;</td>
									  </tr>
									  <tr>
										  <td class="content_row_header"><?=$_LANG->get('Telefon');?>
										  </td>
										  <td class="content_row_clear">
											  <input name="phone" style="width: 300px" class="text" value="<?=$address->getPhone()?>"
													 onfocus="markfield(this,0)" onblur="markfield(this,1)">
										  </td>
									  </tr>
									  <tr>
										  <td class="content_row_header"><?=$_LANG->get('Fax');?>
										  </td>
										  <td class="content_row_clear">
											  <input name="fax" style="width: 300px" class="text" value="<?=$address->getFax()?>"
													 onfocus="markfield(this,0)" onblur="markfield(this,1)">
										  </td>
									  </tr>
									  <tr>
										  <td class="content_row_header"><?=$_LANG->get('Mobil');?>
										  </td>
										  <td class="content_row_clear">
											  <input name="mobil" style="width: 300px" class="text" value="<?=$address->getMobil()?>"
													 onfocus="markfield(this,0)" onblur="markfield(this,1)">
										  </td>
									  </tr>
									  <tr>
										  <td colspan="2" class="content_row_clear" align="right">
											  <input type="submit" name="submit" value="<?=$_LANG->get('Speichern')?>">
											  <?=$savemsg?>
										  </td>
									  </tr>
								  </table>
							  </div>
					</div>
				</form>


				<form method="post" id="neue_rechadr" style="display:none">
					<input type="hidden" name="subexec" value="save" >
					<div class="panel panel-default">
						  <div class="panel-heading">
								<h3 class="panel-title">
									<b>Neue Rechnungsadresse</b>
								</h3>
						  </div>
						<div class="table-responsive">
							<table class="table table-hover">
								<tr>
									<td class="content_row_header"><?=$_LANG->get('Firma');?> *</td>
									<td class="content_row_clear"><input name="r_name1" style="width: 300px"
																		 class="text" value="<?=$r_address->getName1()?>"
																		 onfocus="markfield(this,0)" onblur="markfield(this,1)">
									</td>
								</tr>
								<tr>
									<td class="content_row_header"><?=$_LANG->get('Firmenzusatz');?></td>
									<td class="content_row_clear"><input name="r_name2"
																		 style="width: 300px" class="text" value="<?=$r_address->getName2()?>"
																		 onfocus="markfield(this,0)" onblur="markfield(this,1)">
									</td>
								</tr>
								<tr>
									<td class="content_row_header"><?=$_LANG->get('Adresse');?>
									</td>
									<td class="content_row_clear"><input name="r_address1"
																		 style="width: 300px" class="text" value="<?=$r_address->getAddress1()?>"
																		 onfocus="markfield(this,0)" onblur="markfield(this,1)">
									</td>
								</tr>
								<tr>
									<td class="content_row_header"><?=$_LANG->get('Adresszusatz');?>
									</td>
									<td class="content_row_clear"><input name="r_address2"
																		 style="width: 300px" class="text" value="<?=$r_address->getAddress2()?>"
																		 onfocus="markfield(this,0)" onblur="markfield(this,1)">
									</td>
								</tr>
								<tr>
									<td class="content_row_header"><?=$_LANG->get('PLZ / Stadt');?>
									</td>
									<td class="content_row_clear">
										<nobr>
											<input name="r_zip" style="width: 50px" class="text" value="<?=$r_address->getZip()?>"
												   onfocus="markfield(this,0)" onblur="markfield(this,1)">
											<input name="r_city" style="width: 243px" class="text" value="<?=$r_address->getCity()?>"
												   onfocus="markfield(this,0)" onblur="markfield(this,1)">
										</nobr>
									</td>
								</tr>
								<tr>
									<td class="content_row_header"><?=$_LANG->get('Land')?></td>
									<td class="content_row_clear"><select name="r_country" style="width: 300px"
																		  class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
											<?
											foreach($countries as $c)
											{?>
												<option value="<?=$c->getId()?>"
													<?if ($r_address->getCountry()->getId() == $c->getId()) echo "selected";?>>
													<?=$c->getName()?>
												</option>
											<?}?>
										</select>
									</td>
								</tr>
								<tr>
									<td class="content_row_header">&nbsp;</td>
									<td class="content_row_clear">&nbsp;</td>
								</tr>
								<tr>
									<td class="content_row_header"><?=$_LANG->get('Telefon');?>
									</td>
									<td class="content_row_clear">
										<input name="r_phone" style="width: 300px" class="text" value="<?=$r_address->getPhone()?>"
											   onfocus="markfield(this,0)" onblur="markfield(this,1)">
									</td>
								</tr>
								<tr>
									<td class="content_row_header"><?=$_LANG->get('Fax');?>
									</td>
									<td class="content_row_clear">
										<input name="r_fax" style="width: 300px" class="text" value="<?=$r_address->getFax()?>"
											   onfocus="markfield(this,0)" onblur="markfield(this,1)">
									</td>
								</tr>
								<tr>
									<td class="content_row_header"><?=$_LANG->get('Mobil');?>
									</td>
									<td class="content_row_clear">
										<input name="r_mobil" style="width: 300px" class="text" value="<?=$r_address->getMobil()?>"
											   onfocus="markfield(this,0)" onblur="markfield(this,1)">
									</td>
								</tr>
								<tr>
									<td colspan="2" class="content_row_clear" align="right">
										<input type="submit" name="submit" value="<?=$_LANG->get('Speichern')?>">
										<?=$savemsg?>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</form>
				<?=$savemsg?>

				<?/*11.02.2014,gln: Anzeige Lieferadressen */?>
				<div class="panel panel-default">
					  <div class="panel-heading">
							<h3 class="panel-title">
								<b>Lieferadressen</b>
								<img onclick="document.getElementById('neue_lieferadr').style.display='' " src="../images/icons/user--plus.png"
									 title="<?=$_LANG->get('Neue Lieferadresse anlegen');?>" alt="<?=$_LANG->get('Neue Lieferadresse anlegen');?>">
							</h3>
					  </div>
						  <div class="table-responsive">
							  <table class="table table-hover">
								  <tr>
									  <td class="content_row"><b>Firma</b></td>
									  <td class="content_row"><b>Adresse</b></td>
									  <td class="content_row"><b>PLZ/Ort</b></td>
									  <td class="content_row"><b>Land</b></td>
									  <td class="content_row"><b>Telefon</b></td>
								  </tr>
								  <?/*<tr>
								<td colspan="2"><b><?=$_LANG->get('Lieferadressen');?> </b></td>
							</tr> */?>
								  <tr>
									  <?/* 	<td><b><?=$_LANG->get('Name');?> </b></td>
								<td><b><?=$_LANG->get('Adresse');?> </b></td> */?>
								  </tr>
								  <?	foreach($all_deliveryAddresses AS $deliv){ ?>
									  <tr>
										  <td>
											  <?=$deliv->getNameAsLine()?>
											  <? if ($deliv->getDefault() == 1) echo ' (Standard)'; ?>
										  </td>
										  <td>
											  <?=$deliv->getAddress1()?> <?=$deliv->getAddress2()?>
										  </td>
										  <td>
											  <?=$deliv->getZip()?> <?=$deliv->getCity()?>
										  </td>
										  <td>
											  <?$c=new Country($deliv->getCountry()->getId()); echo $c->getName()?>
										  </td>
										  <td>
											  <?=$deliv->getPhone()?>
										  </td>
									  </tr>
								  <?	} ?>
							  </table>
						  </div>
				</div>


				<div class="panel panel-default">
					  <div class="panel-heading">
							<h3 class="panel-title">
								<b>Rechnungsadressen</b>
								<img onclick="document.getElementById('neue_rechadr').style.display='' " src="../images/icons/user--plus.png"
									 title="<?=$_LANG->get('Neue Rechnungsadresse anlegen');?>" alt="<?=$_LANG->get('Neue Rechnungsadresse anlegen');?>">
							</h3>
					  </div>
						  <div class="table-responsive">
							  <table class="table table-hover">
								  <tr>
									  <td class="content_row"><b>Firma</b></td>
									  <td class="content_row"><b>Adresse</b></td>
									  <td class="content_row"><b>PLZ/Ort</b></td>
									  <td class="content_row"><b>Land</b></td>
									  <td class="content_row"><b>Telefon</b></td>
								  </tr>
								  <?/*<tr>
								<td colspan="2"><b><?=$_LANG->get('Lieferadressen');?> </b></td>
							</tr> */?>
								  <tr>
									  <?/* 	<td><b><?=$_LANG->get('Name');?> </b></td>
								<td><b><?=$_LANG->get('Adresse');?> </b></td> */?>
								  </tr>
								  <?	foreach($all_invoiceAddresses AS $invoiceadr){ ?>
									  <tr>
										  <td>
											  <?=$invoiceadr->getNameAsLine()?>
											  <? if ($invoiceadr->getDefault() == 1) echo ' (Standard)'; ?>
										  </td>
										  <td>
											  <?=$invoiceadr->getAddress1()?> <?=$invoiceadr->getAddress2()?>
										  </td>
										  <td>
											  <?=$invoiceadr->getZip()?> <?=$invoiceadr->getCity()?>
										  </td>
										  <td>
											  <?$c=new Country($invoiceadr->getCountry()->getId()); echo $c->getName()?>
										  </td>
										  <td>
											  <?=$invoiceadr->getPhone()?>
										  </td>
									  </tr>
								  <?	} ?>
							  </table>
						  </div>
				</div>
		  </div>
	</div>



