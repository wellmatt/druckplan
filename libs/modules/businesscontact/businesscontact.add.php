<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('libs/modules/organizer/nachricht.class.php');
require_once ('libs/modules/tickets/ticket.class.php');
require_once ('libs/modules/commissioncontact/commissioncontact.class.php');

global $_CONFIG;
$_USER;

$_REQUEST["id"] = (int)$_REQUEST["id"];
$businessContact = new BusinessContact($_REQUEST["id"]);

$all_attributes = Attribute::getAllAttributesForCustomer();

// Nachricht senden, dann Speichern
if($_REQUEST["subexec"] == "send"){
	$send_mail = true;
	// Damit nach dem Senden auch gespeichert wird
	$_REQUEST["subexec"] = "save";
}

if ($_REQUEST["subexec"] == "save")
{
	if ($_REQUEST["subform"] == "user_details"){ //Form von Tab1 auslesen
		
	}
	if ($_REQUEST["subform"] == "web_login"){ // Form von Tab 4 auslesen
	
	}
	if ($_REQUEST["supplier"]==""){
		$_REQUEST["supplier"]=0;
	}
    if ($_REQUEST["commissionpartner"]==""){
        $_REQUEST["commissionpartner"]=0;
    }
	
	$positiontitles = Array();
	foreach($_REQUEST['position_titles'] as $position_title)
	{
		$positiontitles[] = $position_title;
	}
	
    $businessContact->setActive(1);
    $businessContact->setCommissionpartner(trim(addslashes($_REQUEST["commissionpartner"])));
    $businessContact->setcustomer(trim(addslashes($_REQUEST["customer"])));
    $businessContact->setSupplier(trim(addslashes($_REQUEST["supplier"])));
    $businessContact->setName1(trim(addslashes($_REQUEST["name1"])));
    $businessContact->setName2(trim(addslashes($_REQUEST["name2"])));
    $businessContact->setAddress1(trim(addslashes($_REQUEST["address1"])));
    $businessContact->setAddress2(trim(addslashes($_REQUEST["address2"])));
    $businessContact->setZip(trim(addslashes($_REQUEST["zip"])));
    $businessContact->setCity(trim(addslashes($_REQUEST["city"])));
    $businessContact->setCountry(new Country (trim(addslashes($_REQUEST["country"]))));
    
    $businessContact->setEmail(trim(addslashes($_REQUEST["email"])));
    $businessContact->setPhone(trim(addslashes($_REQUEST["phone"])));
    $businessContact->setFax(trim(addslashes($_REQUEST["fax"])));
    $businessContact->setWeb(trim(addslashes($_REQUEST["web"])));
    $businessContact->setClient(new Client((int)$_REQUEST["client"]));
    $businessContact->setLanguage(new Translator((int)$_REQUEST["language"]));
    $businessContact->setDiscount((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["discount"]))));
    $businessContact->setPaymentTerms(new PaymentTerms((int)$_REQUEST["payment"]));
    //$businessContact->setComment(trim(addslashes($_REQUEST["comment"])));
    $businessContact->setNumberatcustomer(trim(addslashes($_REQUEST["numberatcustomer"])));
    $businessContact->setCustomernumber(trim(addslashes($_REQUEST["customernumber"])));
    $businessContact->setKreditor((int)($_REQUEST["kreditor"]));
    $businessContact->setDebitor((int)($_REQUEST["debitor"]));
    $businessContact->setBic(trim(addslashes($_REQUEST["bic"])));
    $businessContact->setIban(trim(addslashes($_REQUEST["iban"])));
    
    if ($_REQUEST["notifymailadr"]){
        $tmp_array_notify_adr = Array();
        foreach ($_REQUEST["notifymailadr"] as $tmp_notify_adr){
            if ($_REQUEST["notifymailadr"] != "")
                $tmp_array_notify_adr[] = $tmp_notify_adr;
        }
    }
    
    $businessContact->setNotifymailadr($tmp_array_notify_adr);
    
    $businessContact->setShoplogin(trim(addslashes($_REQUEST["shop_login"])));
    $businessContact->setShoppass(trim(addslashes($_REQUEST["shop_pass"])));
    $businessContact->setTicketenabled((int)$_REQUEST["ticket_enabled"]);
    $businessContact->setPersonalizationenabled((int)$_REQUEST["personalization_enabled"]);
    $businessContact->setArticleenabled((int)$_REQUEST["article_enabled"]);
    if ((int)$_REQUEST["login_expire"] != 0){
    	$_REQUEST["login_expire"] = explode(".", $_REQUEST["login_expire"]);
    	$businessContact->setLoginexpire((int)mktime(12, 0, 0, $_REQUEST["login_expire"][1], $_REQUEST["login_expire"][0], $_REQUEST["login_expire"][2]));
    } else {
    	$businessContact->setLoginexpire(0);
    }
    
    $businessContact->setAlt_name1(trim(addslashes($_REQUEST["alt_name1"])));
    $businessContact->setAlt_name2(trim(addslashes($_REQUEST["alt_name2"])));
    $businessContact->setAlt_address1(trim(addslashes($_REQUEST["alt_address1"])));
    $businessContact->setAlt_address2(trim(addslashes($_REQUEST["alt_address2"])));
    $businessContact->setAlt_zip(trim(addslashes($_REQUEST["alt_zip"])));
    $businessContact->setAlt_city(trim(addslashes($_REQUEST["alt_city"])));
    $businessContact->setAlt_country(new Country (trim(addslashes($_REQUEST["alt_country"]))));
    $businessContact->setAlt_email(trim(addslashes($_REQUEST["alt_email"])));
    $businessContact->setAlt_phone(trim(addslashes($_REQUEST["alt_phone"])));
    $businessContact->setAlt_fax(trim(addslashes($_REQUEST["alt_fax"])));
    
    $businessContact->setPriv_name1(trim(addslashes($_REQUEST["priv_name1"])));
    $businessContact->setPriv_name2(trim(addslashes($_REQUEST["priv_name2"])));
    $businessContact->setPriv_address1(trim(addslashes($_REQUEST["priv_address1"])));
    $businessContact->setPriv_address2(trim(addslashes($_REQUEST["priv_address2"])));
    $businessContact->setPriv_zip(trim(addslashes($_REQUEST["priv_zip"])));
    $businessContact->setPriv_city(trim(addslashes($_REQUEST["priv_city"])));
    $businessContact->setPriv_country(new Country (trim(addslashes($_REQUEST["priv_country"]))));
    $businessContact->setPriv_email(trim(addslashes($_REQUEST["priv_email"])));
    $businessContact->setPriv_phone(trim(addslashes($_REQUEST["priv_phone"])));
    $businessContact->setPriv_fax(trim(addslashes($_REQUEST["priv_fax"])));
    
    $businessContact->setType((int)$_REQUEST["cust_type"]);
    $businessContact->setBedarf((int)$_REQUEST["cust_bedarf"]);
    $businessContact->setProdukte((int)$_REQUEST["cust_produkte"]);
    $businessContact->setBranche((int)$_REQUEST["cust_branche"]);
   
    $businessContact->setPositionTitles($positiontitles);
    
    $businessContact->setMatchcode($_REQUEST["matchcode"]);
   
    $savemsg = getSaveMessage($businessContact->save());
	if($DB->getLastError()!=NULL && $DB->getLastError()!=""){
    	$savemsg .= $DB->getLastError();
    }
    
    if (count(Address::getAllAddresses($businessContact,Address::ORDER_NAME,Address::FILTER_INVC)) < 1) {
        if ($_REQUEST["name1"] && $_REQUEST["address1"] && $_REQUEST["zip"] && $_REQUEST["city"] && $_REQUEST["country"]) {
            $address = new Address();
            $address->setActive(1);
            $address->setName1(trim(addslashes($_REQUEST["name1"])));
            $address->setName2(trim(addslashes($_REQUEST["name2"])));
            $address->setAddress1(trim(addslashes($_REQUEST["address1"])));
            $address->setAddress2(trim(addslashes($_REQUEST["address2"])));
            $address->setZip(trim(addslashes($_REQUEST["zip"])));
            $address->setCity(trim(addslashes($_REQUEST["city"])));
            $address->setPhone(trim(addslashes($_REQUEST["phone"])));
            $address->setFax(trim(addslashes($_REQUEST["fax"])));
            $address->setCountry(new Country (trim(addslashes($_REQUEST["country"]))));
            $address->setBusinessContact($businessContact);
            $address->save();
        }
    }
    if (count(Address::getAllAddresses($businessContact,Address::ORDER_NAME,Address::FILTER_DELIV)) < 1) {
        if ($_REQUEST["name1"] && $_REQUEST["address1"] && $_REQUEST["zip"] && $_REQUEST["city"] && $_REQUEST["country"]) {
            $address = new Address();
            $address->setActive(2);
            $address->setName1(trim(addslashes($_REQUEST["name1"])));
            $address->setName2(trim(addslashes($_REQUEST["name2"])));
            $address->setAddress1(trim(addslashes($_REQUEST["address1"])));
            $address->setAddress2(trim(addslashes($_REQUEST["address2"])));
            $address->setZip(trim(addslashes($_REQUEST["zip"])));
            $address->setCity(trim(addslashes($_REQUEST["city"])));
            $address->setPhone(trim(addslashes($_REQUEST["phone"])));
            $address->setFax(trim(addslashes($_REQUEST["fax"])));
            $address->setCountry(new Country (trim(addslashes($_REQUEST["country"]))));
            $address->setShoprel(1);
            $address->setBusinessContact($businessContact);
            $address->save();
        }
    }
    
    // Merkmale speichern
    $businessContact->clearAttributes();	// Erstmal alle loeschen und dann nur aktive neu setzen
    $save_attributes = Array();
    $i=1;
    foreach ($all_attributes AS $attribute){
		$allitems = $attribute->getItems();
		foreach ($allitems AS $item){
			if((int)$_REQUEST["attribute_item_check_{$attribute->getId()}_{$item["id"]}"] == 1){
				$tmp_attribute["id"] = 0;
				$tmp_attribute["value"] = 1;
				$tmp_attribute["attribute_id"] = $attribute->getId();
				$tmp_attribute["item_id"] = $item["id"];
				$save_attributes[] = $tmp_attribute;
				$i++;
			}
		}
	}
	$businessContact->saveActiveAttributes($save_attributes);
}

if($businessContact->getId()){
	$contactPersons = ContactPerson::getAllContactPersons($businessContact,ContactPerson::ORDER_NAME);
	$deliveryAddresses = Address::getAllAddresses($businessContact,Address::ORDER_NAME,Address::FILTER_DELIV);
	$invoiceAddresses = Address::getAllAddresses($businessContact,Address::ORDER_NAME,Address::FILTER_INVC);
}

if($send_mail){
	if($_REQUEST["email"] != "" && $_REQUEST["email"] != NULL){
		$text = 'Sehr geehrte Damen und Herren, <br><br> ';
		$text .= 'mit diesen Logindaten melden Sie sich im Kunden-Portal an:<br>';
		$text .= 'Benutzer:'.$_REQUEST["shop_login"].'<br>';
		$text .= 'Passwort:'.$_REQUEST["shop_pass"].'<br><br>';
		$text .= 'Mit freundlichem Gru&szlig; aus Steeden...';
		$to = Array();
		$to[] = $businessContact;
	
		$nachricht = new Nachricht();
		$nachricht->setFrom($_USER);
		$nachricht->setTo($to);
		$nachricht->setSubject("Kundenportal-Login");
		$nachricht->setText($text);
		$ret = $nachricht->send();
	}
}

$show_tab=(int)$_REQUEST["tabshow"];
$languages = Translator::getAllLangs(Translator::ORDER_NAME);
$countries = Country::getAllCountries();
$all_active_attributes = $businessContact->getActiveAttributeItems();



/**************************************************************************
 ******* 				Java-Script									*******
 *************************************************************************/
?>


<script>
	$(function() {
		$( "#tabs" ).tabs({ selected: <?=$show_tab?> });
	});
</script>


<script language="javascript">
function checkpass(obj){
	
	// war mal angedacht als Sicherung fuer die Eingabe von 2 Passworten zum Vergleich auf Gleichheit
	
	// var shop_pass1 = document.getElementById('shop_pass1').value;
	// var shop_pass2 = document.getElementById('shop_pass2').value;
	// if (shop_pass1 != shop_pass2){
	// 	alert('<? // =$_LANG->get('Passw&ouml;rter stimmen nicht &uuml;berein')?>');
	// 	document.getElementById('shop_pass1').focus();
	// 	return false;
	// }
	return checkform(obj);
}

function dialNumber(link){
	$.get('http://'+link, //{exec: 'dial_number', link: link},
			function(data) {
				alert('Nummer gesendet.');
			}
	);
}

$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	
	$('#login_expire').datepicker(
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

<? /*************************************
	* Liefert eine neue Debitoren-Nr.
	***********************************/ ?>
function generadeDebitorNumber(){
	$.post("libs/modules/businesscontact/businesscontact.ajax.php", {exec: 'generadeDebitorNr'}, function(data) {
		// Work on returned data
		document.getElementById('debitor').value= data;
	});
}

<? /*************************************
	* Liefert eine neue Debitoren-Nr.
	***********************************/ ?>
function generadeCreditorNumber(){
	$.post("libs/modules/businesscontact/businesscontact.ajax.php", {exec: 'generadeCreditorNr'}, function(data) {
		// Work on returned data
		document.getElementById('kreditor').value= data;
	});
}

<? /*************************************
	* Liefert eine neue Debitoren-Nr.
	***********************************/ ?>
function generadeCustomerNumber(){
	$.post("libs/modules/businesscontact/businesscontact.ajax.php", {exec: 'generadeCustomerNr'}, function(data) {
		// Setzen der neu-generierten Nummer
		document.getElementById('customernumber').value= data;
	});
}

<? /*******************************************************
	* Prueft, ob die Kundennummer bereits vergeben
	******************************************************/ ?>
function checkCustomerNumber(obj){
	var thisnumber = '<?=$businessContact->getCustomernumber()?>';
	var newnumber  = document.getElementById('customernumber').value;

	<?//Erst ueberpruefen ob Art-Nr leer ist, dann ob vorhanden?>
	if (newnumber == "" || parseInt(newnumber) == 0){
		return checkform(obj);
	}

	if (thisnumber != newnumber){
		$.post("libs/modules/businesscontact/businesscontact.ajax.php", 
				{exec: 'checkCustomerNumber', newnumber : newnumber}, 
				 function(data) {
					 data = data.substring(0,2);
					if(data == "DA"){
						alert('<?=$_LANG->get('Kundennummer bereits vergeben!') ?>');
						document.getElementById('customernumber').focus();
						return false;
					} else {
						if (checkform(obj)==true){ 
							document.getElementById('user_form').submit();
						}
					}
				});
	} else { 
		return checkform(obj);
	}
	return false;
}

function addTitlePosition(){
	var count = parseInt(document.getElementById('position_titles_count').value) + 1;
	var obj = document.getElementById('table_positiontitles');
	var insert = '<tr id="tr_positiontitle_'+count+'"><td colspan="3"><input type="text" style="width:350px" name="position_titles[]"><img onclick="removeTitlePosition(this)" alt="'+count+'" src="images/icons/cross-script.png"/></td></tr>';
	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('position_titles_count').value = count;
}

function removeTitlePosition(item){
	var id = parseInt(item.alt);
	document.getElementById('tr_positiontitle_'+id).remove();
	var count = parseInt(document.getElementById('position_titles_count').value) - 1;
	document.getElementById('position_titles_count').value = count;
}

function commi_checkbox(){
  if(document.getElementById('has_commissionpartner').checked){
	document.getElementById('commi_title').style.display= '';


	document.getElementById('commissionpartner').style.display= '';
  }
  else {
	document.getElementById('commi_title').style.display= 'none';
	document.getElementById('commissionpartner').style.display= 'none';
  }
}
</script>
<?
/**************************************************************************
 ******* 				HTML-Bereich								*******
 *************************************************************************/?>
	
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="user_form" id="user_form" enctype="multipart/form-data"
	onSubmit="return checkCustomerNumber(new Array(this.name1));" > 
	<?// gucken, ob die Passwoerter (Webshop-Login) gleich sind und ob alle notwendigen Felder gef�llt sind?>
	
	<input type="hidden" name="exec" value="edit"> 
	<input type="hidden" name="subexec" id="subexec" value="save"> 
	<input type="hidden" name="subform" value="user_details">
	<input type="hidden" name="id" value="<?=$businessContact->getId()?>">
	
<div class="demo">	
	<div id="tabs">
		<ul>
			<li><a href="#tabs-0"><? echo $_LANG->get('&Uuml;bersicht');?></a></li>
			<li><a href="#tabs-1"><? echo $_LANG->get('Stammdaten');?></a></li>
			<li><a href="#tabs-5"><? echo $_LANG->get('Merkmale');?></a></li> 
			<li><a href="#tabs-2"><? echo $_LANG->get('Adressen');?></a></li> 
			<li><a href="#tabs-3"><? echo $_LANG->get('Ansprechpartner');?></a></li>
			<?if ($_CONFIG->shopActivation){?>
				<li><a href="#tabs-4"><? echo $_LANG->get('Kundenportal');?></a></li>
			<?}?>
			<li><a href="#tabs-6"><? echo $_LANG->get('Notizen');?></a></li>
			<li><a href="#tabs-7"><? echo $_LANG->get('Tickets');?></a></li>
			<li><a href="#tabs-8"><? echo $_LANG->get('Personalisierung');?></a></li>
			<li><a href="#tabs-9"><? echo $_LANG->get('Rechnungsausgang');?></a></li>
			<li><a href="#tabs-10"><? echo $_LANG->get('Rechnungseingang');?></a></li>
			<li><a href="#tabs-11"><? echo $_LANG->get('Vorg&auml;nge');?></a></li>
		</ul>
		
		<? // ---------------------------- Uebesicht ueber den Geschaeftskontakt --------------------------?>
		
	<div id="tabs-0">
		<br>
		<table width="100%">
		<tr>
			<td width="700" class="content_header">
				<h1><?=$businessContact->getNameAsLine() ?></h1>
			</td>
			<td></td>
			<td width="200" class="content_header" align="right"><?=$savemsg?></td>
		</tr>
		</table>
		
		<table width="100%">
			<colgroup>
			<col width="750">
			<col>
			</colgroup>
			<tr>
			<td valign="top">
				<table width="100%">
						<colgroup>
							<col width="350">
							<col>
						</colgroup>
						<tr>
							<td class="content_row_clear" valign="top">
								<?=$businessContact->getAddress1()?> <?=$businessContact->getAddress2()?> <br>
								<?=$businessContact->getZip()?> <?=$businessContact->getCity()?><br>
								<?=$businessContact->getCountry()->getName()?>
							</td>
							<td class="content_row_clear" valign="top">
								<span  onClick="dialNumber('<?=$_USER->getTelefonIP()?>/command.htm?number=<?=$businessContact->getPhoneForDial()?>')"
									title="<?=$businessContact->getPhoneForDial()." ".$_LANG->get('anrufen');?>" class="pointer icon-link">
									<img src=" images/icons/telephone.png" alt="TEL"> <?=$businessContact->getPhone()?>
								</span>
								<br>
								<img src="images/icons/telephone-fax.png" alt="FAX"> <?=$businessContact->getFax()?><br>
								<img src="images/icons/globe-green.png" alt="WEB"> 
									<a class="icon-link" href="<?=$businessContact->getWebForHref()?>" target="_blank"><?=$businessContact->getWeb()?></a> <br>
								<img src="images/icons/mail.png" alt="MAIL"> <?=$businessContact->getEmail()?>
							</td>
						</tr>
						<tr>
							<td class="content_row_clear">&ensp;</td>
						</tr>
						<tr>
							<td class="content_row_clear" valign="top">
							<?	if ($businessContact->getCustomernumber() != NULL && $businessContact->getCustomernumber() != ""){
									echo $_LANG->get('Kundennummer').": ".$businessContact->getCustomernumber(). " <br/>";
								} ?>
							<?	if ($businessContact->getNumberatcustomer() != NULL && $businessContact->getNumberatcustomer() != ""){
									echo $_LANG->get('Unsere KD-Nr.').": ".$businessContact->getNumberatcustomer(). " <br/>";
								} ?>
							<?	if ($businessContact->getMatchcode() != NULL && $businessContact->getMatchcode() != ""){
									echo $_LANG->get('Matchcode').": ".$businessContact->getMatchcode(). " <br/>";
								} ?>
							</td>
							<td class="content_row_clear" valign="top">
								<?=$_LANG->get('Tickets:');?>: 
								<?if ($businessContact->getTicketenabled() == 1) echo $_LANG->get('AN'); else echo $_LANG->get('AUS'); ?> <br/>
								<?=$_LANG->get('Personalisierung:');?>: 
								<?if ($businessContact->getPersonalizationenabled() == 1) echo $_LANG->get('AN'); else echo $_LANG->get('AUS'); ?> <br/>
								<?=$_LANG->get('Artikel:');?>: 
								<?if ($businessContact->getArticleenabled() == 1) echo $_LANG->get('AN'); else echo $_LANG->get('AUS'); ?> <br/>
							</td>
						</tr>
						<tr>
							<td class="content_row_clear" valign="top">
							<?	foreach ($all_attributes AS $attribute){
									$tmp_output = "<b>".$attribute->getTitle().":</b> ";
									$allitems = $attribute->getItems();
									$j=0;
									foreach ($allitems AS $item){
										if ($all_active_attributes["{$attribute->getId()}_{$item["id"]}"] == 1){
											$tmp_output .= $item["title"].", ";
											$j++;
										}
									}
									$tmp_output = substr($tmp_output, 0, -2); // Letztes Komma entfernen
									if($j>0){ 
										echo $tmp_output.= "<br/>";
									}
								}?>
							</td>
						</tr>
				</table>
			</td>
			<td valign="top">
			&ensp;<b><?=$_LANG->get('Ansprechpartner');?></b>
			<br/><br/>
			<table>
				<colgroup>
					<col width="200">
					<col>
				</colgroup>
			<?	foreach($contactPersons as $cp) { 
					$phone = $cp->getPhoneForDial("n");
					$mobilephone = $cp->getPhoneForDial("m");?>
					<tr <?if($cp->isMainContact())echo 'style="font-weight:bold;"'?>>
						<td class="content_row_clear">
							<?php echo $cp->getNameAsLine(); ?>
						</td>
						<td class="content_row_clear">
							<?	$contact_attributes = Attribute::getAllAttributesForContactperson();
								$active_cust_attributes= $cp->getActiveAttributeItems();
								foreach ($contact_attributes AS $attribute){
									$allitems = $attribute->getItems();
									$j=0; $tmp_output = "";
									foreach ($allitems AS $item){
										if ($active_cust_attributes["{$attribute->getId()}_{$item["id"]}"] == 1){
											$tmp_output .= $item["title"].", ";
											$j++;
										}
									}
									$tmp_output = substr($tmp_output, 0, -2); // Letztes Komma entfernen
									if($j>0 && $attribute->getId() == 21){ 		// An dieser STelle nur Attribut "Funktion" ausgeben 
										echo $tmp_output;
									}
								}?>
						</td>
						<td class="content_row_clear">
							<!-- index.php?exec=edit&id=<?=$businessContact->getID()?>&subexec=phone -->
							<table>
								<colgroup>
								<col width="25">
								<col width="25">
								<col width="25">
								</colgroup>
								<tr>
									<td>
										<?if ($phone != false){?>  
										<a class="icon-link" href="#" title="<?=$phone." ".$_LANG->get('anrufen');?>"
											onClick="dialNumber('<?=$_USER->getTelefonIP()?>/command.htm?number=<?=$phone?>')"											
											><img src="images/icons/telephone.png"></a>
										<?}?>
									</td>
									<td>
										<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=phone" title="<?=$cp->getEmail();?>"><img src="images/icons/mail.png"></a>
									</td>
									<td>
										<?if ($mobilephone != false){?>
										<a class="icon-link" href="#" title="<?=$mobilephone." ".$_LANG->get('anrufen');?>"
											onClick="dialNumber('<?=$_USER->getTelefonIP()?>/command.htm?number=<?=$mobilephone?>')"
											><img src="images/icons/mobile-phone.png"></a>
										<?}?>
									</td>
								</tr>
							</table>
						</td>
			        </tr>
	        <?	}  ?>
			</table>
			</td>
			</tr>
		</table>
	</div>
	
	<? /* ------------------------------------- STAMMDATEN ------------------------------------------------------ */ ?>

	<div id="tabs-1">	
	<table width="100%">
		<tr>
			<td width="200" class="content_header">
				<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"><? if ($businessContact->getId()) echo $_LANG->get('Gesch&auml;ftskontakt &auml;ndern'); else echo $_LANG->get('Gesch&auml;ftskontakt hinzuf&uuml;gen');?>
			</td>
			<td></td>
			<td width="200" class="content_header" align="right"><?=$savemsg?></td>
		</tr>
	</table>
	
	<table>
		<tr>
		<td width="400">
			<table width="100%">
				<colgroup>
					<col width="170">
					<col>
				</colgroup>
				<tr>
					<td class="content_row_header" colspan="2"><?=$_LANG->get('Adresse (allgemein)');?></td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Firma');?> *</td>
					<td class="content_row_clear"><input name="name1" style="width: 250px"
						class="text" value="<?=$businessContact->getName1()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Firmenzusatz');?></td>
					<td class="content_row_clear"><input name="name2"
						style="width: 250px" class="text" value="<?=$businessContact->getName2()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Adresse');?>
					</td>
					<td class="content_row_clear"><input name="address1"
						style="width: 250px" class="text" value="<?=$businessContact->getAddress1()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Adresszusatz');?>
					</td>
					<td class="content_row_clear"><input name="address2"
						style="width: 250px" class="text" value="<?=$businessContact->getAddress2()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Postleitzahl');?>
					</td>
					<td class="content_row_clear"><input name="zip"
						style="width: 250px" class="text" value="<?=$businessContact->getZip()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Stadt');?>
					</td>
					<td class="content_row_clear"><input name="city"
						style="width: 250px" class="text" value="<?=$businessContact->getCity()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Land')?></td>
					<td class="content_row_clear"><select name="country" style="width: 250px"
						class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
							<?
							foreach($countries as $c)
							{?>
							<option value="<?=$c->getId()?>"
							<?if ($businessContact->getCountry()->getId() == $c->getId()) echo "selected";?>>
								<?=$c->getName()?>
							</option>
							<?}
		
							?>
					</select>
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Telefon');?>
					</td>
					<td class="content_row_clear"><input name="phone"
						style="width: 250px" class="text" value="<?=$businessContact->getPhone()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Fax');?>
					</td>
					<td class="content_row_clear"><input name="fax"
						style="width: 250px" class="text" value="<?=$businessContact->getFax()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('E-Mail');?>
					</td>
					<td class="content_row_clear"><input name="email"
						style="width: 250px" class="text" value="<?=$businessContact->getEmail()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Internetseite');?>
					</td>
					<td class="content_row_clear"><input name="web"
						style="width: 250px" class="text" value="<?=$businessContact->getWeb()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr><td>&emsp;</td></tr>
				<tr><td>&emsp;</td></tr>
			</table>
		</td>
		<td>&emsp;</td>
		<td valign="top" width="400px">	
			<table width="100%">
				<colgroup>
					<col width="170">
					<col>
				</colgroup>
				<tr>
					<td class="content_row_header" colspan="2">&nbsp;</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Kundennummer')?></td>
					<td class="content_row_clear icon-link">
					    <input class="text" style="width:100px" name="customernumber" id="customernumber" 
					    		value="<?=$businessContact->getCustomernumber()?>">
					    <img src="images/icons/arrow-curve-180.png" onclick="generadeCustomerNumber()" class="pointer"
					    	 title="<?=$_LANG->get('Neue Kunden-Nr. erzeugen');?>">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Matchcode')?></td>
					<td class="content_row_clear icon-link">
					    <input class="text" style="width:100px" name="matchcode" id="matchcode" 
					    		value="<?=$businessContact->getMatchcode()?>">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Kunde');?></td>
					<td class="content_row_clear"><select name="customer" style="width: 250px"
						class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
							<option value="0" <? if(! ($businessContact->isExistingCustomer() && $businessContact->isPotentialCustomer())) echo "selected";?>></option>
							<option value="1" <? if($businessContact->isExistingCustomer()) echo "selected";?>>
								<?=$_LANG->get('Bestandskunde')?>
							</option>
							<option value="2" <? if($businessContact->isPotentialCustomer()) echo "selected";?>>
								<?=$_LANG->get('Interessent')?>
							</option>
					</select>
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Lieferant');?></td>
					<td class="content_row_clear">
					 	<input name="supplier" type="checkbox" value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)"
						<? if ($businessContact->isSupplier()) echo "checked";?>>
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Unsere KD-Nr.')?></td>
					<td class="content_row_clear">
					    <input class="text" style="width:100px" name="numberatcustomer" 
					    		value="<?=$businessContact->getNumberatcustomer()?>">
					</td>
				</tr>
				<tr>
					<td class="content_row_header">&nbsp;</td>
					<td class="content_row_clear">&nbsp;</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Zahlungsart')?></td>
					<td class="content_row_clear">
					    <select name="payment" style="width:250px" class="text">
					    	<option value="0" <? if ($businessContact->getPaymentTerms()->getId() == 0) 
					    							echo "selected"?> >
					    	</option>
					        <? 
					        foreach(PaymentTerms::getAllPaymentConditions(PaymentTerms::ORDER_NAME) as $pt)
					        {
					            echo '<option value="'.$pt->getId().'"';
					            if ($pt->getId() == $businessContact->getPaymentTerms()->getId()){
									echo "selected";
								}
					            echo'>'.$pt->getName().'</option>';
					        }
					        ?>
					    </select>
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('IBAN')?></td>
					<td class="content_row_clear">
					    <input class="text" style="width:300px" name="iban" 
					    		value="<?=$businessContact->getIban()?>"> 
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('BIC')?></td>
					<td class="content_row_clear">
					    <input class="text" style="width:150px" name="bic" 
					    		value="<?=$businessContact->getBic()?>"> 
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Rabatt')?></td>
					<td class="content_row_clear">
					    <input class="text" style="width:60px" name="discount" 
					    		value="<?=printPrice($businessContact->getDiscount())?>"> %
					</td>
				</tr>
				<? if($businessContact->getLectorId() > 0) { ?>
				<tr>
					<td class="content_row_header"><span class="error"><?=$_LANG->get('Lector-Import')?>: </span></td>
					<td class="content_row_clear">ID: <?=$businessContact->getId()?></td>
				</tr>
				<tr>
					<td class="content_row_header">&nbsp;</td>
					<td class="content_row_clear">&nbsp;</td>
				</tr>
				<?  } ?>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Kreditor-Nr.')?></td>
					<td class="content_row_clear icon-link">
					    <input class="text" style="width:100px" name="kreditor" id="kreditor"
					    		value="<?=$businessContact->getKreditor()?>"> 
					    <img src="images/icons/arrow-curve-180.png" onclick="generadeCreditorNumber()" class="pointer"
					    		title="<?=$_LANG->get('Neue Kreditoren-Nr. erzeugen');?>">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Debitor-Nr.')?></td>
					<td class="content_row_clear icon-link">
					    <input class="text" style="width:100px" name="debitor" id="debitor"
					    		value="<?=$businessContact->getDebitor()?>">
					    <img src="images/icons/arrow-curve-180.png" onclick="generadeDebitorNumber()" class="pointer"
					    		title="<?=$_LANG->get('Neue Debitoren-Nr. erzeugen');?>">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Provisionspartner')?></td>
					<td class="content_row_clear">
						<input name="has_commissionpartner" id="has_commissionpartner" type="checkbox" value="1"
							<? if ($businessContact->getCommissionpartner()>0) echo "checked";?>
							onclick="commi_checkbox()">
					</td>
				</tr>
				<tr>
					<td class="content_row_header" style="display: <? if (!$businessContact->getCommissionpartner()>0) echo 'none';?>" id="commi_title"><?=$_LANG->get('Partner: ')?></td>
					<td>
						<select name="commissionpartner" style="display: <? if (!$businessContact->getCommissionpartner()>0) echo 'none';?>" class="text" id="commissionpartner">
							<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
							<? $commissioncontacts = CommissionContact::getAllCommissionContacts();
							foreach($commissioncontacts as $comcon)
							{ 
								echo '<option value="'.$comcon->getId().'" ';
								if($businessContact->getCommissionpartner() == $comcon->getId()) echo 'selected="selected"';
								echo '>'.$comcon->getName1().', '.$comcon->getCity().'</option>';
							}?>
						</select>
					</td>
				</tr>
				<? /********** ------------------ Alternativ-Adresse ------------------------------------------ ?>
				<tr>
					<td class="content_row_header" colspan="2"><?=$_LANG->get('Alternativadresse');?></td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Firma');?> *</td>
					<td class="content_row_clear">
						<input name="alt_name1" style="width: 250px" value="<?=$businessContact->getAlt_name1()?>"
								class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Firmenzusatz');?></td>
					<td class="content_row_clear"><input name="alt_name2"
						style="width: 250px" class="text" value="<?=$businessContact->getAlt_name2()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Adressfeld 1');?>
					</td>
					<td class="content_row_clear"><input name="alt_address1"
						style="width: 250px" class="text" value="<?=$businessContact->getAlt_address1()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Adressfeld 2');?>
					</td>
					<td class="content_row_clear"><input name="alt_address2"
						style="width: 250px" class="text" value="<?=$businessContact->getAlt_address2()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Postleitzahl');?>
					</td>
					<td class="content_row_clear"><input name="alt_zip"
						style="width: 250px" class="text" value="<?=$businessContact->getAlt_zip()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Stadt');?>
					</td>
					<td class="content_row_clear"><input name="alt_city"
						style="width: 250px" class="text" value="<?=$businessContact->getAlt_city()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Land')?></td>
					<td class="content_row_clear"><select name="alt_country" style="width: 250px"
						class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
							<?
							foreach($countries as $c)
							{?>
							<option value="<?=$c->getId()?>"
							<?if ($businessContact->getAlt_country()->getId() == $c->getId()) echo "selected";?>>
								<?=$c->getName()?>
							</option>
							<?}
		
							?>
					</select>
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Telefon');?>
					</td>
					<td class="content_row_clear"><input name="alt_phone"
						style="width: 250px" class="text" value="<?=$businessContact->getAlt_phone()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Fax');?>
					</td>
					<td class="content_row_clear"><input name="alt_fax"
						style="width: 250px" class="text" value="<?=$businessContact->getAlt_fax()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('E-Mail');?>
					</td>
					<td class="content_row_clear"><input name="alt_email"
						style="width: 250px" class="text" value="<?=$businessContact->getAlt_email()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr> <? ******/?>
			</table>
		</td>
		<td>&emsp;</td>
		<? /******* ?>
		<td valign="top" width="400px"> <? // ------------------ Privat-Adresse ---------------------------------------------- ?>
			<table width="100%">
				<colgroup>
					<col width="170">
					<col>
				</colgroup>
				<tr>
					<td class="content_row_header" colspan="2"><?=$_LANG->get('Privatadresse');?></td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Firma');?> *</td>
					<td class="content_row_clear"><input name="priv_name1" style="width: 250px"
						class="text" value="<?=$businessContact->getPriv_name1()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Firmenzusatz');?></td>
					<td class="content_row_clear"><input name="priv_name2"
						style="width: 250px" class="text" value="<?=$businessContact->getPriv_name2()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Adressfeld 1');?>
					</td>
					<td class="content_row_clear"><input name="priv_address1"
						style="width: 250px" class="text" value="<?=$businessContact->getPriv_address1()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Adressfeld 2');?>
					</td>
					<td class="content_row_clear"><input name="priv_address2"
						style="width: 250px" class="text" value="<?=$businessContact->getPriv_address2()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Postleitzahl');?>
					</td>
					<td class="content_row_clear"><input name="priv_zip"
						style="width: 250px" class="text" value="<?=$businessContact->getPriv_zip()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Stadt');?>
					</td>
					<td class="content_row_clear"><input name="priv_city"
						style="width: 250px" class="text" value="<?=$businessContact->getPriv_city()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Land')?></td>
					<td class="content_row_clear"><select name="priv_country" style="width: 250px"
						class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
							<?
							foreach($countries as $c)
							{?>
							<option value="<?=$c->getId()?>"
							<?if ($businessContact->getPriv_country()->getId() == $c->getId()) echo "selected";?>>
								<?=$c->getName()?>
							</option>
							<?}
		
							?>
					</select>
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Telefon');?>
					</td>
					<td class="content_row_clear"><input name="priv_phone"
						style="width: 250px" class="text" value="<?=$businessContact->getPriv_phone()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Fax');?>
					</td>
					<td class="content_row_clear"><input name="priv_fax"
						style="width: 250px" class="text" value="<?=$businessContact->getPriv_fax()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('E-Mail');?>
					</td>
					<td class="content_row_clear"><input name="priv_email"
						style="width: 250px" class="text" value="<?=$businessContact->getPriv_email()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
			</table>
		</td> **/?>
		</tr>
	</table>
	</div>
	
	<? // -------------------------------- ADRESSEN -------------------------------------------------------?>
	
	<div id="tabs-2">
	
	<?if($businessContact->getId()){?>
		<table width="100%">
			<colgroup>
				<col width="300">
				<col width="300">
				<col>
				<col>
			</colgroup>
			
			<tr>
				<td class="content_row_header"> <?php echo $_LANG->get('Rechnungsadresse');?></td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear" align="right">
					<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit_ai&id=<?=$businessContact->getID()?>"><img src="images/icons/plus.png"> <?=$_LANG->get('Rechnungsadresse hinzuf&uuml;gen')?></a>
				</td>
			</tr>
			<?php $addressInvoice = Address::getAllAddresses($businessContact,Address::ORDER_NAME,Address::FILTER_INVC);
			foreach($addressInvoice as $ai)
			{
			?>
			<tr>
				<td><? echo $ai->getName1() . ' ' . $ai->getName2();
				    if ($ai->getDefault() == 1) echo ' (Standard)';?>
				</td>
				<td><? echo $ai->getAddress1() ." ". $ai->getAddress2();?></td>
				<td><? echo $ai->getZip()." ".$ai->getCity();?></td>
				<td class="content_row_clear" align="right">
	            	<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit_ai&id_a=<?=$ai->getId()?>&id=<?=$businessContact->getID()?>"><img src="images/icons/pencil.png"></a>
	            	<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=delete_a&id_a=<?=$ai->getId()?>&id=<?=$businessContact->getID()?>" onclick="askDel('index.php?exec=delete_a&id_a=<?=$ai->getId()?>&id=<?=$businessContact->getID()?>')"><img src="images/icons/cross-script.png"></a>
	        	</td>
	        </tr>
	        <?php 
				
			}
			?>
		</table>
	<?}?>

	<?if($businessContact->getId()){?>
		<table width="100%">
			<colgroup>
				<col width="300">
				<col width="300">
				<col width="300">
				<col>
			</colgroup>
			
			<tr>
				<td class="content_row_header"> <?php echo $_LANG->get('Lieferadresse');?></td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear" align="right">
					<a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit_ad&id=<?=$businessContact->getID()?>" class="icon-link"
						><img src="images/icons/plus.png"> <?=$_LANG->get('Lieferadresse hinzuf&uuml;gen')?></a>
				</td>
			</tr>
			<?php 
			$addressDelivery = Address::getAllAddresses($businessContact,Address::ORDER_NAME,Address::FILTER_DELIV);
			foreach($addressDelivery as $ad)
			{
			?>
			<tr>
				<td><? echo $ad->getName1() . ' ' . $ad->getName2();
				    if ($ad->getDefault() == 1) echo ' (Standard)';?>
				</td>
				<td><? echo $ad->getAddress1()." ". $ad->getAddress2();?></td>
				<td><? echo $ad->getZip()." ".$ad->getCity();?></td>
				<td class="content_row_clear" align="right">
					<?/*gln*/?>
					<img src="images/status/
					<? if ($ad->getShoprel() == 0){
							echo "red_small.gif";
						} else {
							echo "green_small.gif";
						}
					?>" title="<?=$_LANG->get('Shop-Freigabe')?>"> &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
	            	<a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit_ad&id_a=<?=$ad->getId()?>&id=<?=$businessContact->getID()?>"><img src="images/icons/pencil.png"></a>
	            	<a href="index.php?page=<?=$_REQUEST['page']?>&exec=delete_a&id_a=<?=$ad->getId()?>&id=<?=$businessContact->getID()?>" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete_a&id_a=<?=$ad->getId()?>&id=<?=$businessContact->getID()?>')"><img src="images/icons/cross-script.png"></a>
	        	</td>
	        </tr>
	        <?php 
			}
			?>
		</table>
	<?}?>
	</div>
	
	<? // ------------------------------------- Ansprechpartner ----------------------------------------------?>
	<div id="tabs-3">

	<?if($businessContact->getId()){?>
		<table width="100%" cellpadding="0" cellspacing="0">
			<colgroup>
				<col width="300">
				<col width="300">
				<col width="300">
				<col width="20">
				<col width="120">
			</colgroup>
			
			<tr>
				<td class="content_row_header"><? echo $_LANG->get('Ansprechpartner');?></td>
				<td class="content_row_clear">&emsp;</td>
				<td class="content_row_clear" align="right" colspan="3">
					<a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit_cp&id=<?=$businessContact->getID()?>" class="icon-link"
						><img src="images/icons/plus.png"> <?=$_LANG->get('Ansprechpartner hinzuf&uuml;gen')?></a>
				</td>
			</tr>
			<? $contactPerson = ContactPerson::getAllContactPersons($businessContact,ContactPerson::ORDER_NAME);
			foreach($contactPerson as $cp){
			?>
			<tr <?if($cp->isMainContact())echo 'style="font-weight:bold;"'?>>
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit_cp&cpid=<?=$cp->getId()?>&id=<?=$businessContact->getID()?>'"
					valign="top">
					<?php echo $cp->getNameAsLine(); ?> &ensp; </br>
					<? if ($cp->getBirthDate() > 0 ) echo '<img src="images/icons/cake.png"/>&nbsp;'.date("d.m.Y", $cp->getBirthDate());?>
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit_cp&cpid=<?=$cp->getId()?>&id=<?=$businessContact->getID()?>'"
					valign="top">
					<?
					$contact_attributes = Attribute::getAllAttributesForContactperson();
					$active_cust_attributes= $cp->getActiveAttributeItems();
					foreach ($contact_attributes AS $attribute){
						$tmp_output = "<i>".$attribute->getTitle().":</i> ";
						$allitems = $attribute->getItems();
						$j=0;
						foreach ($allitems AS $item){
							if ($active_cust_attributes["{$attribute->getId()}_{$item["id"]}"] == 1){
								$tmp_output .= $item["title"].", ";
								$j++;
							}
						}
						$tmp_output = substr($tmp_output, 0, -2); // Letztes Komma entfernen
						if($j>0){ 
							echo $tmp_output . "<br/>";
						}
					}?> &ensp;
				</td>
				<td class="content_row pointer" valign="top"
					onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit_cp&cpid=<?=$cp->getId()?>&id=<?=$businessContact->getID()?>'">
					<? 	if($cp->getActiveAdress() == 1){ echo $cp->getPhone(); }
						if($cp->getActiveAdress() == 2){ echo $cp->getAlt_phone(); }
						if($cp->getActiveAdress() == 3){ echo $cp->getPriv_phone(); }
					?> &ensp;
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit_cp&cpid=<?=$cp->getId()?>&id=<?=$businessContact->getID()?>'"> &ensp;</td>
				<td class="content_row" align="right">
	            	<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit_cp&cpid=<?=$cp->getId()?>&id=<?=$businessContact->getID()?>"><img src="images/icons/pencil.png"></a>
	            	<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=delete_cp&cpid=<?=$cp->getId()?>&id=<?=$businessContact->getID()?>" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete_cp&cpid=<?=$cp->getId()?>&id=<?=$businessContact->getID()?>')"><img src="images/icons/cross-script.png"></a>
	        	 </td>
	        </tr>
	        <?
			}
			?>
		</table>
	<?}?>
	<p></p>
	
	<? // ------------------------------------- Kundenportal ----------------------------------------------?>
	</div>
	<?if ($_CONFIG->shopActivation){?>
	
        <script language="javascript">
            function addMailRow()
            {
            	var obj = document.getElementById('notify_mail_adr');
            	var insert = '<input name="notifymailadr[]" type="mail" value="" onfocus="markfield(this,0)" onblur="markfield(this,1)"></br>';
            	obj.insertAdjacentHTML("BeforeEnd", insert);
            }
        </script>
	
	
		<div id="tabs-4"><p>
		
		<table width="100%">
			<tr>
				<td width="200" class="content_header">
					<img src="<?$_MENU->getIcon($_REQUEST['page'])?>"> 
					<?=$_LANG->get('Kunden-Login &auml;ndern');?>
				</td>
				<td></td>
				<td width="200" class="content_header" align="right"><?=$savemsg?></td>
			</tr>
		</table>
		
		<?if($businessContact->getId()){?>
		  <? if(Address::getDefaultAddress($businessContact,Address::FILTER_INVC) && Address::getDefaultAddress($businessContact,Address::FILTER_DELIV)){?>
			<table width="100%">
				<colgroup>
					<col width="180">
					<col>
				</colgroup>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Benutzername')?> *</td>
					<td class="content_row_clear">
						<input 	name="shop_login" style="width: 300px"
								class="text" value="<?=$businessContact->getShoplogin()?>"
								onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Passwort')?></td>
					<td class="content_row_clear">
						<input 	name="shop_pass" id="shop_pass" style="width: 300px" class="text" 
								value="<?=$businessContact->getShoppass()?>" 
								type="text"	onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('G&uuml;ltigkeit')?></td>
					<td class="content_row_clear">
						<input type="text" style="width:80px" id="login_expire" name="login_expire"
								class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
								onfocus="markfield(this,0)" onblur="markfield(this,1)"
								value="<?if($businessContact->getLoginexpire() != 0){ echo date('d.m.Y', $businessContact->getLoginexpire());}?>">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Ticket-Freigabe');?></td>
					<td class="content_row_clear">
						<input name="ticket_enabled" type="checkbox" value="1" <? if ($businessContact->getTicketenabled()) echo "checked";?>
							   onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Personalisierung-Freigabe');?></td>
					<td class="content_row_clear">
						<input name="personalization_enabled" type="checkbox" value="1" <? if ($businessContact->getPersonalizationenabled()) echo "checked";?>
							   onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Artikel-Freigabe');?></td>
					<td class="content_row_clear">
						<input name="article_enabled" type="checkbox" value="1" <? if ($businessContact->getArticleenabled()) echo "checked";?>
							   onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Benachrichtigungs-Adr.');?></td>
					<td class="content_row_clear" id="notify_mail_adr"><img src="images/icons/plus.png" class="pointer icon-link" onclick="addMailRow()"></br>
						<? foreach ($businessContact->getNotifyMailAdr() as $notifymailadr){?>
						    <input name="notifymailadr[]" type="mail" value="<?=$notifymailadr?>" onfocus="markfield(this,0)" onblur="markfield(this,1)"></br>
						<? }?>
					</td>
				</tr>
			</table>
			<?}else{?>
			</br>
			<span class="error">Sie m&uuml;ssen zuerst eine Standard Liefer- und Rechnungsadresse anlegen</br>bevor Sie dem Kunden Zugriff auf das Online Portal geben k&ouml;nnen</span>
			<?}?>
		<?}?>
		
		</div>
	<?}?>
	
	<? // ------------------------------------- Merkmale ----------------------------------------------?>
		<div id="tabs-5">
			<table width="100%">
					<tr>
						<td width="200" class="content_header">
							<img src="<?$_MENU->getIcon($_REQUEST['page'])?>"> 
							<?=$_LANG->get('Merkmale');?>
						</td>
						<td></td>
						<td width="200" class="content_header" align="right"><?=$savemsg?></td>
					</tr>
			</table>
			
			<table width="100%">
					<colgroup>
						<col width="180">
						<col>
					</colgroup>
					<!-- tr>
						<td class="content_row_header"><?=$_LANG->get('Typen')?></td>
						<td class="content_row_clear">
							<select name="cust_type" style="width: 200px" class="text" 
									onfocus="markfield(this,0)" onblur="markfield(this,1)">
									<option value="0" >&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
									<option value="1" <?if($businessContact->getType()==1) echo "selected";?> ><?=$_LANG->get('diverse Adresse');?></option>
									<option value="2" <?if($businessContact->getType()==2) echo "selected";?> ><?=$_LANG->get('Intressenten');?></option>
									<option value="3" <?if($businessContact->getType()==3) echo "selected";?> ><?=$_LANG->get('Kunde');?></option>
									<option value="4" <?if($businessContact->getType()==4) echo "selected";?> ><?=$_LANG->get('Lieferant');?></option>
									<option value="5" <?if($businessContact->getType()==5) echo "selected";?> ><?=$_LANG->get('Partner');?></option>
									<option value="6" <?if($businessContact->getType()==6) echo "selected";?> ><?=$_LANG->get('Projektpartner');?></option>
									<option value="7" <?if($businessContact->getType()==7) echo "selected";?> ><?=$_LANG->get('Referenz');?></option>
									<option value="8" <?if($businessContact->getType()==8) echo "selected";?> ><?=$_LANG->get('Wettbewerber');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Branche')?></td>
						<td class="content_row_clear">
							<select name="cust_branche" style="width: 200px" class="text" 
									onfocus="markfield(this,0)" onblur="markfield(this,1)">
									<option value="0" >&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
									<option value="1" <?if($businessContact->getBranche()==1) echo "selected";?> ><?=$_LANG->get('Automobil');?></option>
									<option value="2" <?if($businessContact->getBranche()==2) echo "selected";?> ><?=$_LANG->get('Buchbindereien');?></option>
									<option value="3" <?if($businessContact->getBranche()==3) echo "selected";?> ><?=$_LANG->get('Dienstleistungen');?></option>
									<option value="4" <?if($businessContact->getBranche()==4) echo "selected";?> ><?=$_LANG->get('Digital');?></option>
									<option value="5" <?if($businessContact->getBranche()==5) echo "selected";?> ><?=$_LANG->get('Druckereien');?></option>
									<option value="6" <?if($businessContact->getBranche()==6) echo "selected";?> ><?=$_LANG->get('Einzelhandel');?></option>
									<option value="7" <?if($businessContact->getBranche()==7) echo "selected";?> ><?=$_LANG->get('Energie');?></option>
									<option value="8" <?if($businessContact->getBranche()==8) echo "selected";?> ><?=$_LANG->get('Etiketten');?></option>
									<option value="9" <?if($businessContact->getBranche()==9) echo "selected";?> ><?=$_LANG->get('Financial Service');?></option>
									<option value="10" <?if($businessContact->getBranche()==10) echo "selected";?> ><?=$_LANG->get('Gastronomie');?></option>
									<option value="11" <?if($businessContact->getBranche()==11) echo "selected";?> ><?=$_LANG->get('Handel');?></option>
									<option value="12" <?if($businessContact->getBranche()==12) echo "selected";?> ><?=$_LANG->get('Handwerk');?></option>
									<option value="13" <?if($businessContact->getBranche()==13) echo "selected";?> ><?=$_LANG->get('Industrie');?></option>
									<option value="14" <?if($businessContact->getBranche()==14) echo "selected";?> ><?=$_LANG->get('Lettershop');?></option>
									<option value="15" <?if($businessContact->getBranche()==15) echo "selected";?> ><?=$_LANG->get('Logistik');?></option>
									<option value="16" <?if($businessContact->getBranche()==16) echo "selected";?> ><?=$_LANG->get('Non-Profit');?></option>
									<option value="17" <?if($businessContact->getBranche()==17) echo "selected";?> ><?=$_LANG->get('Papier');?></option>
									<option value="18" <?if($businessContact->getBranche()==18) echo "selected";?> ><?=$_LANG->get('Privatkunden');?></option>
									<option value="19" <?if($businessContact->getBranche()==19) echo "selected";?> ><?=$_LANG->get('Rechtsanw&auml;lte');?></option>
									<option value="20" <?if($businessContact->getBranche()==20) echo "selected";?> ><?=$_LANG->get('Reise + Transport');?></option>
									<option value="21" <?if($businessContact->getBranche()==21) echo "selected";?> ><?=$_LANG->get('Software');?></option>
									<option value="22" <?if($businessContact->getBranche()==22) echo "selected";?> ><?=$_LANG->get('Telekommunikation');?></option>
									<option value="23" <?if($businessContact->getBranche()==23) echo "selected";?> ><?=$_LANG->get('Transport');?></option>
									<option value="24" <?if($businessContact->getBranche()==24) echo "selected";?> ><?=$_LANG->get('Trauer');?></option>
									<option value="25" <?if($businessContact->getBranche()==25) echo "selected";?> ><?=$_LANG->get('Verband');?></option>
									<option value="26" <?if($businessContact->getBranche()==26) echo "selected";?> ><?=$_LANG->get('Veredler');?></option>
									<option value="27" <?if($businessContact->getBranche()==27) echo "selected";?> ><?=$_LANG->get('Vereine');?></option>
									<option value="28" <?if($businessContact->getBranche()==28) echo "selected";?> ><?=$_LANG->get('Verlag');?></option>
									<option value="29" <?if($businessContact->getBranche()==29) echo "selected";?> ><?=$_LANG->get('Versicherung');?></option>
									<option value="30" <?if($businessContact->getBranche()==30) echo "selected";?> ><?=$_LANG->get('Werbemittelhersteller');?></option>
									<option value="31" <?if($businessContact->getBranche()==31) echo "selected";?> ><?=$_LANG->get('Werbung/Agentur');?></option>
									<option value="32" <?if($businessContact->getBranche()==32) echo "selected";?> ><?=$_LANG->get('Zubeh&ouml;r');?></option>
									<option value="33" <?if($businessContact->getBranche()==33) echo "selected";?> ><?=$_LANG->get('&Auml;rzte/Gesundheit');?></option>
									<option value="34" <?if($businessContact->getBranche()==34) echo "selected";?> ><?=$_LANG->get('&Ouml;ffentliche Einrichtungen');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Produkte')?></td>
						<td class="content_row_clear">
							<select name="cust_produkte" style="width: 200px" class="text" 
									onfocus="markfield(this,0)" onblur="markfield(this,1)">
									<option value="0" >&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
									<option value="1" <?if($businessContact->getProdukte()==1) echo "selected";?> ><?=$_LANG->get('BB');?></option>
									<option value="2" <?if($businessContact->getProdukte()==2) echo "selected";?> ><?=$_LANG->get('Blocks-S&auml;tze');?></option>
									<option value="3" <?if($businessContact->getProdukte()==3) echo "selected";?> ><?=$_LANG->get('Cou-H&uuml;-Kuv');?></option>
									<option value="4" <?if($businessContact->getProdukte()==4) echo "selected";?> ><?=$_LANG->get('Fly-Kar-PL');?></option>
									<option value="5" <?if($businessContact->getProdukte()==5) echo "selected";?> ><?=$_LANG->get('Kalender');?></option>
									<option value="6" <?if($businessContact->getProdukte()==6) echo "selected";?> ><?=$_LANG->get('Mailings');?></option>
									<option value="7" <?if($businessContact->getProdukte()==7) echo "selected";?> ><?=$_LANG->get('MedFlat');?></option>
									<option value="8" <?if($businessContact->getProdukte()==8) echo "selected";?> ><?=$_LANG->get('POS');?></option>
									<option value="9" <?if($businessContact->getProdukte()==9) echo "selected";?> ><?=$_LANG->get('Pros-Map');?></option>
									<option value="10" <?if($businessContact->getProdukte()==10) echo "selected";?> ><?=$_LANG->get('Verp-Aufk');?></option>
									<option value="11" <?if($businessContact->getProdukte()==11) echo "selected";?> ><?=$_LANG->get('Visi');?></option>
									<option value="11" <?if($businessContact->getProdukte()==11) echo "selected";?> ><?=$_LANG->get('Website');?></option>
									<option value="11" <?if($businessContact->getProdukte()==11) echo "selected";?> ><?=$_LANG->get('WtoP');?></option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Bedarf');?></td>
						<td class="content_row_clear">
							<select name="cust_bedarf" style="width: 200px" class="text" 
									onfocus="markfield(this,0)" onblur="markfield(this,1)">
									<option value="0" >&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
									<option value="1" <?if($businessContact->getBedarf()==1) echo "selected";?> ><?=$_LANG->get('BB');?></option>
									<option value="2" <?if($businessContact->getBedarf()==2) echo "selected";?> ><?=$_LANG->get('Blocks-S&auml;tze');?></option>
									<option value="3" <?if($businessContact->getBedarf()==3) echo "selected";?> ><?=$_LANG->get('Cou-H&uuml;-Kuv');?></option>
									<option value="4" <?if($businessContact->getBedarf()==4) echo "selected";?> ><?=$_LANG->get('Fly-Kar-PL');?></option>
									<option value="5" <?if($businessContact->getBedarf()==5) echo "selected";?> ><?=$_LANG->get('Kalender');?></option>
									<option value="6" <?if($businessContact->getBedarf()==6) echo "selected";?> ><?=$_LANG->get('Mailings');?></option>
									<option value="7" <?if($businessContact->getBedarf()==7) echo "selected";?> ><?=$_LANG->get('MedFlat');?></option>
									<option value="8" <?if($businessContact->getBedarf()==8) echo "selected";?> ><?=$_LANG->get('POS');?></option>
									<option value="9" <?if($businessContact->getBedarf()==9) echo "selected";?> ><?=$_LANG->get('Pros-Map');?></option>
									<option value="10" <?if($businessContact->getBedarf()==10) echo "selected";?> ><?=$_LANG->get('Verp-Aufk');?></option>
									<option value="11" <?if($businessContact->getBedarf()==11) echo "selected";?> ><?=$_LANG->get('Visi');?></option>
									<option value="11" <?if($businessContact->getBedarf()==11) echo "selected";?> ><?=$_LANG->get('Website');?></option>
									<option value="11" <?if($businessContact->getBedarf()==11) echo "selected";?> ><?=$_LANG->get('WtoP');?></option>
							</select>
						</td>
					</tr -->
					
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Sprache')?></td>
						<td class="content_row_clear">
							<select name="language" style="width: 200px"
									class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
								<? foreach($languages as $l){ ?>
									<option value="<?=$l->getId()?>"
										<?if ($businessContact->getLanguage()->getId() == $l->getId()) echo "selected";?>>
										<?=$l->getName()?>
									</option>
								<?}?>
							</select>
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Mandant')?></td>
						<td class="content_row_clear">
							<select name="client" style="width: 200px" class="text" onfocus="markfield(this,0)"
									onblur="markfield(this,1)">
								<option value="<?=$_USER->getClient()->getId()?>" selected>
									<?if(!$_USER->getClient()->isActive()) echo '<span color="red">';?>
									<?=$_USER->getClient()->getName()?>
									<?if(!$_USER->getClient()->isActive()) echo '</span>';?>
								</option>
						</select>
						</td>
					</tr>
					<tr>
						<td class="content_row_header" colspan="2">&ensp;</td>
					</tr>
					<?foreach ($all_attributes AS $attribute){?>
						<tr>
							<td class="content_row_header" valign="top"><?=$attribute->getTitle()?></td>
							<td class="content_row_clear">
							<? 	$allitems = $attribute->getItems();?>
								<table>
								<?	$x=0;
									foreach ($allitems AS $item){
										if ($x%5 == 0) echo "<tr>";
										echo '<td width="200px">';
										echo '<input name="attribute_item_check_'.$attribute->getId().'_'.$item["id"].'" ';
										echo ' value="1" type="checkbox" onfocus="markfield(this,0)" onblur="markfield(this,1)"';
												if ($all_active_attributes["{$attribute->getId()}_{$item["id"]}"] == 1) echo "checked";
										echo ">";
										echo $item["title"]."</td>";
										if ($x%5 == 4) echo "</tr>";
								 		$x++;
									}?>
								</table>
							</td>
						</tr>
					<?}?>
				</table>
			<p></p>
		</div>
	
		<? // ------------------------------------- verbundene Tickets (Notizen) ----------------------------------------------?>
		
		<div id="tabs-6">
		<?if($businessContact->getId()){?>
			<table width="100%">
					<tr>
						<td width="200" class="content_header">
							<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
							<?=$_LANG->get('Notizen');?>
						</td>
						<td></td>
						<td width="200" class="content_header" align="right">&ensp;</td>
					</tr>
			</table>
			
			<? // Tickets laden, die dem Kunden zugeordnet wurden
				$from_busicon = true;
				$notes_only = true;
				$contactID = $businessContact->getId();
				require_once 'libs/modules/tickets/ticket.notesfor.php';?>
		<? } ?>
		</div>

		<? // ------------------------------------- verbundene Tickets ----------------------------------------------?>
		
		<div id="tabs-7">
		<?if($businessContact->getId()){?>
			<table width="100%">
					<tr>
						<td width="200" class="content_header">
							<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
							<?=$_LANG->get('Verbundene Tickets');?>
						</td>
						<td></td>
						<td width="200" class="content_header" align="right">&ensp;</td>
					</tr>
			</table>
			
			<? // Tickets laden, die dem Kunden zugeordnet wurden
				$from_busicon = true;
				$notes_only = false;
				$contactID = $businessContact->getId();
				require_once 'libs/modules/tickets/ticket.for.php';?>
		<? } ?>
		</div>

		<? // ------------------------------------- Personalisierung ----------------------------------------------?>
		
		<div id="tabs-8">
		<?if($businessContact->getId()){?>
			<table id="table_positiontitles" width="100%">
					<tr>
						<td width="280" class="content_header">
							<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
							<?=$_LANG->get('Personalisierung (Positions Titel)');?>
							<img onclick="addTitlePosition()" class="pointer icon-link" src="images/icons/plus.png">
						</td>
						<td></td>
						<td width="120" class="content_header" align="right">&ensp;</td>
					</tr>
					<?
					if (count($businessContact->getPositionTitles()) > 0 ) {
						$i = 0;
						foreach($businessContact->getPositionTitles() as $position_title)
						{?>
							<tr id="tr_positiontitle_<?=$i?>">
								<td colspan="3">
									<input type="text" value="<?=$position_title?>" style="width:350px" name="position_titles[]"><img onclick="removeTitlePosition(this)" alt="<?=$i?>" class="pointer icon-link" src="images/icons/cross-script.png"/>
								</td>
							</tr>
						<?$i++; }
					}
					?>
			</table>
			<input type="hidden" value="<?=$i?>" name="position_titles_count" id="position_titles_count">
		<? } ?>
		</div>
</form>		

		<? // ------------------------------------- Rechnungsausgang ----------------------------------------------?>
		
		<div id="tabs-9">
		<?if($businessContact->getId()){
		    $_REQUEST['page'] = "libs/modules/accounting/outgoinginvoice.php";
			$_REQUEST["filter_from"] = 1;
			$_REQUEST["filter_cust"] = $businessContact->getId();
			require_once('libs/modules/accounting/outgoinginvoice.php');
		} ?>
		</div>

		<? // ------------------------------------- Rechnungseingang ----------------------------------------------?>
		
		<div id="tabs-10">
		<?if($businessContact->getId()){
		    $_REQUEST['page'] = "libs/modules/accounting/incominginvoice.php";
			$_REQUEST["filter_from"] = 1;
			$_REQUEST["filter_cust"] = $businessContact->getId();
			require_once('libs/modules/accounting/incominginvoice.php');
		} ?>
		</div>

		<? // ------------------------------------- Vorgänge ----------------------------------------------?>
		
		<div id="tabs-11">
		<?if($businessContact->getId()){
		    $_REQUEST['page'] = "libs/modules/calculation/order.php";
			$_REQUEST['cust_id'] = $businessContact->getId();
			require_once('libs/modules/calculation/order.php');
		} 
		$_REQUEST['page'] = "libs/modules/businesscontact/businesscontact.php";?>
		</div>

	<? // ------------------------------------- Navigations und Speicher Buttons ------------------------------------?>

	</div>
</div>
	<table width="100%">
	    <colgroup>
	        <col width="150">
	        <col width="380">
	        <col width="420">
	        <col>
	    </colgroup> 
	    <tr>
	        <td class="content_row_header">
	        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
	        			onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
	        </td>
	        <td class="content_row_clear" align="center">
	        		<input type="submit" onclick='$("#user_form").submit()' value="<?=$_LANG->get('Speichern')?>">
	        </td>
	        <td class="content_row_clear" align="right">
		        	<?if($businessContact->getId() != 0){?>
		        		<input type="Button" value="<?=$_LANG->get('Speichern u. KD-Login versch.')?>" class="button"
		        			   onclick=" document.getElementById('subexec').value='send'; askSubmit(document.getElementById('user_form'));">
					<?}?>
	        </td>
	        <td class="content_row_clear" align="right">
	        	<? if($_USER->isAdmin()){ ?>
		        	<?if($_REQUEST["exec"] != "new"){?>
		        		<input type="button" class="buttonRed" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$businessContact->getId()?>')" 
		        				value="<?=$_LANG->get('L&ouml;schen')?>">
		        	<?}?>
		        <?}?>
	        </td>
	    </tr>
	</table>
<!--//-->