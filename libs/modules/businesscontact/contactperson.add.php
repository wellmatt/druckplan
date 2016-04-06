<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       08.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/businesscontact/attribute.class.php';

$_REQUEST["cpid"] = (int)$_REQUEST["cpid"];
$contactperson = new ContactPerson($_REQUEST["cpid"]);
$business = new BusinessContact((int)$_REQUEST["id"]);

$all_attributes = Attribute::getAllAttributesForContactperson();

// Bei neuem Ansprechpartner sollen die Daten der Firma kopiert werden, da sich dort meist nciht viel aendert
if($_REQUEST["cpid"] == 0){
	$contactperson->setAddress1($business->getAddress1());
	$contactperson->setAddress2($business->getAddress2());
	$contactperson->setZip($business->getZip());
	$contactperson->setCity($business->getCity());
	$contactperson->setCountry($business->getCountry());
	$contactperson->setFax($business->getFax());
	$contactperson->setPhone($business->getPhone());
	$contactperson->setWeb($business->getWeb());
	$contactperson->setEmail($business->getEmail());
}

$contactperson->setBusinessContact(new BusinessContact((int)$_REQUEST["id"]));

if ($_REQUEST["exec"] == "save_cp" && $_USER->hasRightsByGroup(Group::RIGHT_EDIT_CP)){
    $contactperson->setActive(1);
    $contactperson->setTitle(trim(addslashes($_REQUEST["title"])));
    $contactperson->setName1(trim(addslashes($_REQUEST["name1"])));
    $contactperson->setName2(trim(addslashes($_REQUEST["name2"])));
    $contactperson->setAddress1(trim(addslashes($_REQUEST["address1"])));
    $contactperson->setAddress2(trim(addslashes($_REQUEST["address2"])));
    $contactperson->setZip(trim(addslashes($_REQUEST["zip"])));
    $contactperson->setCity(trim(addslashes($_REQUEST["city"])));
    $contactperson->setCountry(new Country (trim(addslashes($_REQUEST["country"]))));
    $contactperson->setEmail(trim(addslashes($_REQUEST["email"])));
    $contactperson->setPhone(trim(addslashes($_REQUEST["phone"])));
    $contactperson->setMobil(trim(addslashes($_REQUEST["mobil"])));
    $contactperson->setFax(trim(addslashes($_REQUEST["fax"])));
    $contactperson->setWeb(trim(addslashes($_REQUEST["web"])));
    $contactperson->setComment(trim(addslashes($_REQUEST["comment"])));
    $contactperson->setActiveAdress((int)$_REQUEST["active_adress"]);
    
    $contactperson->setShopLogin(trim(addslashes($_REQUEST["shop_login"])));
    $contactperson->setShopPassword(trim(addslashes($_REQUEST["shop_pass"])));
    $contactperson->setEnabledTickets((int)$_REQUEST["shop_tickets"]);		
    $contactperson->setEnabledPersonalization((int)$_REQUEST["shop_personalization"]);
    $contactperson->setEnabledArtikel((int)$_REQUEST["shop_article"]);
	$contactperson->setEnabledMarketing((int)$_REQUEST["shop_marketing"]);
    
    if((int)$_REQUEST["main_contact"] == 1){
    	ContactPerson::clearMainContact($contactperson->getBusinessContact()->getId());
    }
    $contactperson->setIsMainContact((int)$_REQUEST["main_contact"]);
    
    $contactperson->setAlt_name1(trim(addslashes($_REQUEST["alt_name1"])));
    $contactperson->setAlt_name2(trim(addslashes($_REQUEST["alt_name2"])));
    $contactperson->setAlt_address1(trim(addslashes($_REQUEST["alt_address1"])));
    $contactperson->setAlt_address2(trim(addslashes($_REQUEST["alt_address2"])));
    $contactperson->setAlt_zip(trim(addslashes($_REQUEST["alt_zip"])));
    $contactperson->setAlt_city(trim(addslashes($_REQUEST["alt_city"])));
    $contactperson->setAlt_country(new Country (trim(addslashes($_REQUEST["alt_country"]))));
    $contactperson->setAlt_email(trim(addslashes($_REQUEST["alt_email"])));
    $contactperson->setAlt_phone(trim(addslashes($_REQUEST["alt_phone"])));
    $contactperson->setAlt_fax(trim(addslashes($_REQUEST["alt_fax"])));
    $contactperson->setAlt_mobil(trim(addslashes($_REQUEST["alt_mobil"])));
    
    $contactperson->setPriv_name1(trim(addslashes($_REQUEST["priv_name1"])));
    $contactperson->setPriv_name2(trim(addslashes($_REQUEST["priv_name2"])));
    $contactperson->setPriv_address1(trim(addslashes($_REQUEST["priv_address1"])));
    $contactperson->setPriv_address2(trim(addslashes($_REQUEST["priv_address2"])));
    $contactperson->setPriv_zip(trim(addslashes($_REQUEST["priv_zip"])));
    $contactperson->setPriv_city(trim(addslashes($_REQUEST["priv_city"])));
    $contactperson->setPriv_country(new Country (trim(addslashes($_REQUEST["priv_country"]))));
    $contactperson->setPriv_email(trim(addslashes($_REQUEST["priv_email"])));
    $contactperson->setPriv_phone(trim(addslashes($_REQUEST["priv_phone"])));
    $contactperson->setPriv_fax(trim(addslashes($_REQUEST["priv_fax"])));
    $contactperson->setPriv_mobil(trim(addslashes($_REQUEST["priv_mobil"])));
    
	if($_REQUEST["birthdate"] != ""){
		$tmp_date = explode('.', trim(addslashes($_REQUEST["birthdate"])));
		$tmp_date = mktime(2,0,0,$tmp_date[1],$tmp_date[0],$tmp_date[2]);
// 		echo $tmp_date;
	} else {
		$tmp_date = 0;
	}
	$contactperson->setBirthDate($tmp_date);
	
	if ($_REQUEST["notifymailadr"]){
	    $tmp_array_notify_adr = Array();
	    foreach ($_REQUEST["notifymailadr"] as $tmp_notify_adr){
	        if ($_REQUEST["notifymailadr"] != "")
	            $tmp_array_notify_adr[] = $tmp_notify_adr;
	    }
	}
	$contactperson->setNotifymailadr($tmp_array_notify_adr);
	
	$all_categories = TicketCategory::getAllCategories();
    $tmp_cansee = Array();
    $tmp_cancreate = Array();
        
	foreach ($all_categories as $category){
        $cat = $category;
        $cid = $cat->getId();

        if ($_REQUEST["categories_rights_cansee_".$cid] == 1){
            $tmp_cansee[] = $cat;
        }
        if ($_REQUEST["categories_rights_cancreate_".$cid] == 1){
            $tmp_cancreate[] = $cat;
        }

        $contactperson->setCategories_cansee($tmp_cansee);
        $contactperson->setCategories_cancreate($tmp_cancreate);
	}
	
    $savemsg = getSaveMessage($contactperson->save());
    $savemsg .= $DB->getLastError();
    
    $new_attributes = Array();
    
    /**$x=0;
    foreach ($_REQUEST["attribute_id"] as $att){
    	$new_attributes[$x] = new Attribute($att);
    	$new_attributes[$x]->setComment($_REQUEST["attribute_value"][$x]);
    	$new_attributes[$x]->setTitle($_REQUEST["attribute_title"][$x]);
    	$new_attributes[$x]->setObjectid($contactperson->getId());
    	$new_attributes[$x]->setModule(Attribute::MODULE_CONTACTPERSON);
    	if($new_attributes[$x]->getTitle() != "" && $new_attributes[$x]->getTitle() != NULL){
    		$new_attributes[$x]->save();
    	}
    	$x++;
    }**/
    
    // Merkmale speichern
    $contactperson->clearAttributes();	// Erstmal alle loeschen und dann nur aktive neu setzen
    $save_attributes = Array();
    $i=1;
    foreach ($all_attributes AS $attribute){
    	$allitems = $attribute->getItems();
    	foreach ($allitems AS $item){
    		if((int)$_REQUEST["attribute_item_check_{$attribute->getId()}_{$item["id"]}"] == 1){
			    if($item["input"] == 1 && $_REQUEST["attribute_item_input_{$attribute->getId()}_{$item["id"]}"] != "" || $item["input"] == 0)
			    {
        			$tmp_attribute["id"] = 0;
        			$tmp_attribute["value"] = 1;
        			$tmp_attribute["attribute_id"] = $attribute->getId();
        			$tmp_attribute["item_id"] = $item["id"];
    				$tmp_attribute["inputvalue"] = $_REQUEST["attribute_item_input_{$attribute->getId()}_{$item["id"]}"];
        			$save_attributes[] = $tmp_attribute;
        			$i++;
			    }
    		}
    	}
    }
    $contactperson->saveActiveAttributes($save_attributes);
}
$countries = Country::getAllCountries();
//$all_attributes = Attribute::getAllAttributesByObject(Attribute::ORDER_TITLE, Attribute::MODULE_CONTACTPERSON, $contactperson->getId());
$all_attributes = Attribute::getAllAttributesForContactperson();
$all_active_attributes = $contactperson->getActiveAttributeItemsInput();
?>
<script language="javascript">
    function addMailRow()
    {
    	var obj = document.getElementById('notify_mail_adr');
    	var insert = '<input name="notifymailadr[]" type="mail" value="" onfocus="markfield(this,0)" onblur="markfield(this,1)"></br>';
    	obj.insertAdjacentHTML("BeforeEnd", insert);
    }
</script>
<script language="javascript">
function addAttribute(){
	var obj = document.getElementById('table-attributes');
	var insert = '<tr><td valign="top">';
		insert += '<input type="hidden" class="text" name="attribute_id[]" value="0">';
		insert += '&emsp;<input type="text" class="text" name="attribute_title[]" value="">';
		insert += '</td><td>';
		insert += '<textarea name="attribute_value[]" rows="1" cols="35"></textarea>';
		insert += '</td></tr>';
	obj.insertAdjacentHTML("BeforeEnd", insert);
}
$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	$('#birthdate').datepicker(
		{
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'dd.mm.yy',
            showOn: "button",
            buttonImage: "images/icons/calendar-blue.png",
            buttonImageOnly: true,
            onSelect: function(selectedDate) {
            checkDate(selectedDate);
            }
	});
});
</script>

<table width="100%">
	<tr>
		<td width="300" class="content_header"><img
			src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <? if ($contactperson->getId()) echo $_LANG->get('Ansprechpartner &auml;ndern'); else echo $_LANG->get('Ansprechpartner hinzuf&uuml;gen');?>
		</td>
		<td align="right"><?=$savemsg?></td>
	</tr>
</table>

<div id="fl_menu">
	<div class="label">Quick Move</div>
	<div class="menu">
        <a href="#top" class="menu_item">Seitenanfang</a>
        <a href="#" class="menu_item" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$_REQUEST["id"]?>&tabshow=4'">Zurück</a>
        <?php if($_USER->hasRightsByGroup(Group::RIGHT_EDIT_CP)){?>
        <a href="#" class="menu_item" onclick="$('#user_form').submit();">Speichern</a>
        <?php } ?>
    	<? if($_USER->hasRightsByGroup(Group::RIGHT_DELETE_CP) || $_USER->isAdmin()){ ?>
        	<?if($_REQUEST["exec"] != "new"){?>
                <a href="#" class="menu_item_delete" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete_cp&cpid=<?=$contactperson->getId()?>&id=<?=$contactperson->getBusinessContact()->getID()?>')">Löschen</a>
        	<?}?>
        <?}?>
    </div>
</div>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="user_form" id="user_form" 
	onsubmit="return checkform(new Array(this.name1))">
	<!-- input type="hidden" name="exec" value="edit"--> 
	<input type="hidden" name="exec" value="save_cp"> 
	<input type="hidden" name="cpid" value="<?=$contactperson->getId()?>">
	<input type="hidden" name="id" 	value="<?=$contactperson->getBusinessContact()->getId()?>">
	<div class="box1">
	<table>
		<tr>
		<td width="400">
			<table width="500">
				<colgroup>
					<col width="180px">
					<col width="300px" align="right">
				</colgroup>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Anrede');?></td>
					<td class="content_row_clear">
					  <select name="title" style="width: 100px"
						class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						<option value="">Bitte w&auml;hlen</option>
							<?php $titles = array("Herr", "Herrn", "Frau", "Dr.", "Prof.");
							foreach ($titles as $title)
							{
							  echo '<option value="'.$title.'"';
							  if($contactperson->getTitle() == $title) echo ' selected ="selected"';
							  echo '>'.$title.'</option>';
							}
							?>
					</select>
					</td>
				</tr>
				
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Nachname');?> *</td>
					<td class="content_row_clear"><input name="name1" style="width: 300px"
						class="text" value="<?=$contactperson->getName1()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Vorname');?></td>
					<td class="content_row_clear"><input name="name2"
						style="width: 300px" class="text" value="<?=$contactperson->getName2()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Adressfeld 1');?>
					</td>
					<td class="content_row_clear"><input name="address1"
						style="width: 300px" class="text" value="<?=$contactperson->getAddress1()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Adressfeld 2');?>
					</td>
					<td class="content_row_clear"><input name="address2"
						style="width: 300px" class="text" value="<?=$contactperson->getAddress2()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Postleitzahl');?>
					</td>
					<td class="content_row_clear"><input name="zip"
						style="width: 300px" class="text" value="<?=$contactperson->getZip()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Stadt');?>
					</td>
					<td class="content_row_clear"><input name="city"
						style="width: 300px" class="text" value="<?=$contactperson->getCity()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Land')?></td>
					<td class="content_row_clear"><select name="country" style="width: 300px"
						class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
							<? foreach($countries as $c){ ?>
							<option value="<?=$c->getId()?>"
							<?if($contactperson->getCountry()->getId() == $c->getId()) echo "selected";?>>
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
						style="width: 300px" class="text" value="<?=$contactperson->getPhone()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Fax');?>
					</td>
					<td class="content_row_clear"><input name="fax"
						style="width: 300px" class="text" value="<?=$contactperson->getFax()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Mobil');?>
					</td>
					<td class="content_row_clear"><input name="mobil"
						style="width: 300px" class="text" value="<?=$contactperson->getMobil()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Email');?>
					</td>
					<td class="content_row_clear"><input name="email"
						style="width: 300px" class="text" value="<?=$contactperson->getEmail()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Web');?>
					</td>
					<td class="content_row_clear" ><input name="web"
						style="width: 300px" class="text" value="<?=$contactperson->getWeb()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Geburtstag');?>
					</td>
					<td class="content_row_clear" ><input name="birthdate" id="birthdate"
						style="width:70px;" class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)"
						<? if ($contactperson->getBirthDate() != 0 ) echo 'value="'.date("d.m.Y", $contactperson->getBirthDate()).'"';?> 
							title="<?=$_LANG->get('Geburtstag');?>">
					</td>
				</tr>
				<tr>
					<td class="content_row_header">&nbsp;</td>
					<td class="content_row_clear">&nbsp;</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Haupt-Ansprechpartner');?>
					</td>
					<td class="content_row_clear" >
						<input name="main_contact" type="checkbox" value="1" 
								<?if($contactperson->isMainContact()) echo 'checked="checked"'; ?>
								onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Aktive Adresse')?></td>
					<td class="content_row_clear">
						<select name="active_adress" style="width: 250px" class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
							<option value="1" <?if($contactperson->getActiveAdress() == 1) echo "selected";?>>normale Adresse</option>
							<option value="2" <?if($contactperson->getActiveAdress() == 2) echo "selected";?>>Alternativadresse</option>
							<option value="3" <?if($contactperson->getActiveAdress() == 3) echo "selected";?>>Privatadresse</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Shop-Login');?></td>
					<td class="content_row_clear">&nbsp;</td>
				</tr>
				<? if(Address::getDefaultAddress($business,Address::FILTER_INVC) && Address::getDefaultAddress($business,Address::FILTER_DELIV)){?>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Benutzer');?></td>
					<td class="content_row_clear" >
						<input name="shop_login" style="width: 250px" value="<?=$contactperson->getShopLogin()?>"
								onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text" >
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Passwort');?>
					</td>
					<td class="content_row_clear" >
						<input name="shop_pass" style="width: 250px" value="<?=$contactperson->getShopPassword()?>"
								onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text" >
					</td>
				</tr>
				
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Benachrichtigungs-Adr.');?></td>
					<td class="content_row_clear" id="notify_mail_adr"><img src="images/icons/plus.png" class="pointer icon-link" onclick="addMailRow()"></br>
						<? if (count($contactperson->getNotifymailadr()) > 0) { foreach ($contactperson->getNotifyMailAdr() as $notifymailadr){?>
						    <input name="notifymailadr[]" type="mail" value="<?=$notifymailadr?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						    <img src="images/icons/cross.png" onclick="JavaScript: $(this).prev().remove(); $(this).remove();"/></br>
						<? }}?>
					</td>
				</tr>
				
    			<?}else{?>
    			</br>
				<tr>
					<td class="content_row_header">&nbsp;
					</td>
					<td class="content_row_clear" >
						<span class="error">Sie m&uuml;ssen zuerst eine Standard Liefer- und Rechnungsadresse anlegen</br>bevor Sie dem Kunden Zugriff auf das Online Portal geben k&ouml;nnen</span>
					</td>
				</tr>
    			<?}?>
				<tr>
					<td class="content_row_header" valign="top"><?=$_LANG->get('Kommentar');?></td>
					<td class="content_row_clear" id="notify_mail_adr">
					       <textarea name="comment" style="width: 250px;height: 150px;" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   <?=$contactperson->getComment()?></textarea></br>
					</td>
				</tr>
			</table>
		</td>
		<td>&emsp;</td>
		<td valign="top" width="400px">	<? // ------------------ Alternativ-Adresse ------------------------------------------ ?>
			<table width="100%">
				<colgroup>
					<col width="170">
					<col>
				</colgroup>
				<tr>
					<td class="content_row_header" colspan="2"><?=$_LANG->get('Alternativadresse');?></td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Nachname');?></td>
					<td class="content_row_clear">
						<input name="alt_name1" style="width: 250px" value="<?=$contactperson->getAlt_name1()?>"
								class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Vorname');?></td>
					<td class="content_row_clear"><input name="alt_name2"
						style="width: 250px" class="text" value="<?=$contactperson->getAlt_name2()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Adressfeld 1');?>
					</td>
					<td class="content_row_clear"><input name="alt_address1"
						style="width: 250px" class="text" value="<?=$contactperson->getAlt_address1()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Adressfeld 2');?>
					</td>
					<td class="content_row_clear"><input name="alt_address2"
						style="width: 250px" class="text" value="<?=$contactperson->getAlt_address2()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Postleitzahl');?>
					</td>
					<td class="content_row_clear"><input name="alt_zip"
						style="width: 250px" class="text" value="<?=$contactperson->getAlt_zip()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Stadt');?>
					</td>
					<td class="content_row_clear"><input name="alt_city"
						style="width: 250px" class="text" value="<?=$contactperson->getAlt_city()?>"
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
							<?if ($contactperson->getAlt_country()->getId() == $c->getId()) echo "selected";?>>
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
						style="width: 250px" class="text" value="<?=$contactperson->getAlt_phone()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Fax');?>
					</td>
					<td class="content_row_clear"><input name="alt_fax"
						style="width: 250px" class="text" value="<?=$contactperson->getAlt_fax()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Mobil');?>
					</td>
					<td class="content_row_clear"><input name="alt_mobil"
						style="width: 250px" class="text" value="<?=$contactperson->getAlt_mobil()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('E-Mail');?>
					</td>
					<td class="content_row_clear"><input name="alt_email"
						style="width: 250px" class="text" value="<?=$contactperson->getAlt_email()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header">&emsp;</td>
					<td class="content_row_clear">&nbsp;</td>
				</tr>
				<tr>
					<td class="content_row_header">&emsp;</td>
					<td class="content_row_clear"><br><br></td>
				</tr>
				<tr>
					<td class="content_row_header">&emsp;</td>
					<td class="content_row_clear">&emsp;</td>
				</tr>
				<tr>
					<td class="content_row_header">&emsp;</td>
					<td class="content_row_clear">&nbsp;</td>
				</tr>
				<tr>
					<td class="content_row_header">&emsp;</td>
					<td class="content_row_clear">&nbsp;</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Shop-Freigabe');?></td>
					<td class="content_row_clear">&nbsp;</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Tickets');?>
					</td>
					<td class="content_row_clear" >
						<input name="shop_tickets" type="checkbox" value="1" 
								<?if($contactperson->getEnabledTickets()) echo 'checked="checked"'; ?>
								onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Personalisierung');?>
					</td>
					<td class="content_row_clear" >
						<input name="shop_personalization" type="checkbox" value="1" 
								<?if($contactperson->getEnabledPersonalization()) echo 'checked="checked"'; ?>
								onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Artikel');?>
					</td>
					<td class="content_row_clear" >
						<input name="shop_article" type="checkbox" value="1" 
								<?if($contactperson->getEnabledArtikel()) echo 'checked="checked"'; ?>
								onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Marketing');?>
					</td>
					<td class="content_row_clear" >
						<input name="shop_marketing" type="checkbox" value="1"
							<?if($contactperson->getEnabledMarketing()) echo 'checked="checked"'; ?>
							   onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
			</table>
		</td>
		<td>&emsp;</td>
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
					<td class="content_row_header"><?=$_LANG->get('Nachname');?> *</td>
					<td class="content_row_clear"><input name="priv_name1" style="width: 250px"
						class="text" value="<?=$contactperson->getPriv_name1()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Vorname');?></td>
					<td class="content_row_clear"><input name="priv_name2"
						style="width: 250px" class="text" value="<?=$contactperson->getPriv_name2()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Adressfeld 1');?>
					</td>
					<td class="content_row_clear"><input name="priv_address1"
						style="width: 250px" class="text" value="<?=$contactperson->getPriv_address1()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Adressfeld 2');?>
					</td>
					<td class="content_row_clear"><input name="priv_address2"
						style="width: 250px" class="text" value="<?=$contactperson->getPriv_address2()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Postleitzahl');?>
					</td>
					<td class="content_row_clear"><input name="priv_zip"
						style="width: 250px" class="text" value="<?=$contactperson->getPriv_zip()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Stadt');?>
					</td>
					<td class="content_row_clear"><input name="priv_city"
						style="width: 250px" class="text" value="<?=$contactperson->getPriv_city()?>"
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
							<?if ($contactperson->getPriv_country()->getId() == $c->getId()) echo "selected";?>>
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
						style="width: 250px" class="text" value="<?=$contactperson->getPriv_phone()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Fax');?>
					</td>
					<td class="content_row_clear"><input name="priv_fax"
						style="width: 250px" class="text" value="<?=$contactperson->getPriv_fax()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Mobil');?>
					</td>
					<td class="content_row_clear"><input name="priv_mobil"
						style="width: 250px" class="text" value="<?=$contactperson->getPriv_Mobil()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('E-Mail');?>
					</td>
					<td class="content_row_clear"><input name="priv_email"
						style="width: 250px" class="text" value="<?=$contactperson->getPriv_email()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
			</table>
		</td>
		</tr>
	</table>
	</div>
	<br/>
	<div class="box2">
	<h1><?=$_LANG->get('Merkmale');?> <!--&emsp;  img src="images/icons/plus.png" class="pointer" onclick="addAttribute()">--> </h1>
	<table id="table-attributes" >
		<colgroup>
		<col width="140">
		<col>
		</colgroup>
		<? /****$x=0;
		foreach ($all_attributes as $attribute){
			if ($x%3 == 0){?>
			<tr>
			<?}?>
				<td valign="top" align="center">
					<input type="hidden" class="text" name="attribute_id[]" value="<?=$attribute->getId()?>" >
					&emsp;<input type="text" class="text" name="attribute_title[]" value="<?=$attribute->getTitle()?>"> <br/>
					<img src="images/icons/cross-script.png" onclick="askDel('index.php?exec=delete_attribute&attid=<?=$attribute->getId()?>&id=<?=$business->getID()?>&cpid=<?=$contactperson->getId()?>')">
				</td>
				<td valign="top">
					<textarea name="attribute_value[]" rows="1" cols="35"><?=$attribute->getComment()?></textarea>
				</td>
			<? if ($x%3 == 2){?>
			<tr>
			<?}?>
		<? $x++;
		} ***/?>
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
									if ($all_active_attributes["{$attribute->getId()}_{$item["id"]}"]["value"] == 1) echo "checked";
							echo ">";
							echo $item["title"];
							if ($item["input"] == 1)
							{
							    echo ' <input name="attribute_item_input_'.$attribute->getId().'_'.$item["id"].'" ';
							    echo ' value="';
							    echo $all_active_attributes["{$attribute->getId()}_{$item["id"]}"]["inputvalue"];
							    echo '" type="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">';
							}
							echo "</td>";
							if ($x%5 == 4) echo "</tr>";
					 		$x++;
						}?>
					</table>
				</td>
			</tr>
		<?}?>
	</table>
	</div>
	</br>
	<?php 
    $ticket_categories = TicketCategory::getAllCategories();
    ?>
	<input 	type="hidden" name="count_categories" id="count_categories" 
		value="<? if(count($ticket_categories) > 0) echo count($ticket_categories); else echo "1";?>">
    <div class="box2">
		<table>
		<?php foreach ($ticket_categories as $tc){?>
		<tr>
		  <td><h4><?php echo $tc->getTitle()?></h4></td>
		</tr>
		<tr>
		  <td>
		      <table>
		          <thead>
		              <tr>
		                  <th>Einsehen&nbsp;&nbsp;</th>
		                  <th>Erstellen</th>
		              </tr>
		          </thead>
		          <tr>
		              <td><input type="checkbox" name="categories_rights_cansee_<?=$tc->getId()?>" id="categories_rights_cansee_<?=$tc->getId()?>" 
		                   value="1" style="width: 40px" <?php if ($contactperson->TC_cansee($tc)) echo " checked ";?>/></td>
		              <td><input type="checkbox" name="categories_rights_cancreate_<?=$tc->getId()?>" id="categories_rights_cancreate_<?=$tc->getId()?>" 
		                   value="1" style="width: 40px" <?php if ($contactperson->TC_cancreate($tc)) echo " checked ";?>/></td>
		          </tr>
		      </table>
		  </td>
		</tr>
		<?php }?>
		</table>
    </div>
</form>