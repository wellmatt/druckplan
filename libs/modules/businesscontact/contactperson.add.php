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
	$contactperson->setStreet($business->getStreet());
	$contactperson->setHouseno($business->getHouseno());
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

if ($_REQUEST["exec"] == "save_cp" && $_USER->hasRightsByGroup(Permission::CP_EDIT)){
    $contactperson->setActive(1);
    $contactperson->setTitle(trim(addslashes($_REQUEST["title"])));
    $contactperson->setName1(trim(addslashes($_REQUEST["name1"])));
    $contactperson->setName2(trim(addslashes($_REQUEST["name2"])));
    $contactperson->setStreet(trim(addslashes($_REQUEST["street"])));
    $contactperson->setHouseno(trim(addslashes($_REQUEST["houseno"])));
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
    $contactperson->setAltStreet(trim(addslashes($_REQUEST["alt_street"])));
    $contactperson->setAltHouseno(trim(addslashes($_REQUEST["alt_houseno"])));
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
    $contactperson->setPrivStreet(trim(addslashes($_REQUEST["priv_street"])));
    $contactperson->setPrivHouseno(trim(addslashes($_REQUEST["priv_houseno"])));
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
//            showOn: "button",
//            buttonImage: "images/icons/calendar-blue.png",
//            buttonImageOnly: true,
            onSelect: function(selectedDate) {
            checkDate(selectedDate);
            }
	});
});
</script>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','#',"document.location='index.php?page=".$_REQUEST['page']."&exec=edit&id=".$_REQUEST["id"]."&tabshow=4'",'glyphicon-step-backward');
if($_USER->hasRightsByGroup(Permission::CP_EDIT)) {
	$quickmove->addItem('Speichern', '#', "$('#user_form').submit();", 'glyphicon-floppy-disk');
}
if($_USER->hasRightsByGroup(Permission::CP_DELETE) || $_USER->isAdmin()){
	if($_REQUEST["exec"] != "new"){
		$quickmove->addItem('Löschen', '#',  "askDel('index.php?page=".$_REQUEST['page']."&exec=delete_cp&cpid=".$contactperson->getId()."&id=".$contactperson->getBusinessContact()->getID()."');", 'glyphicon-trash', true);
	}
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<? if ($contactperson->getId()) echo $_LANG->get('Ansprechpartner &auml;ndern'); else echo $_LANG->get('Ansprechpartner hinzuf&uuml;gen');?>
				<span class="pull-right">
					<?=$savemsg?>
				</span>
			</h3>
	  </div>
	  <div class="panel-body">
		  <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" class="form-horizontal" name="user_form" id="user_form"
				onsubmit="return checkform(new Array(this.name1))">
			  <!-- input type="hidden" name="exec" value="edit"-->
			  <input type="hidden" name="exec" value="save_cp">
			  <input type="hidden" name="cpid" value="<?=$contactperson->getId()?>">
			  <input type="hidden" name="id" 	value="<?=$contactperson->getBusinessContact()->getId()?>">

			   <div class="row">
				   <div class="col-md-4">
					   <br>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Anrede</label>
						   <div class="col-sm-9">
							   <select name="title" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
								   <option value="">Bitte w&auml;hlen</option>
								   <?php $titles = array("Herr", "Herrn", "Frau", "Frau Dr.", "Herr Dr.", "Frau Dr. med.", "Herr Dr. med.", "Frau Dr. dent.", "Herr Dr. dent.", "Frau Dr. iur.", "Herr Dr. iur.", "Frau Prof.", "Herr Prof.");
								   foreach ($titles as $title)
								   {
									   echo '<option value="'.$title.'"';
									   if($contactperson->getTitle() == $title) echo ' selected ="selected"';
									   echo '>'.$title.'</option>';
								   }
								   ?>
							   </select>
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Nachname</label>
						   <div class="col-sm-9">
							   <input name="name1" class="form-control" value="<?=$contactperson->getName1()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Vorname</label>
						   <div class="col-sm-9">
							   <input name="name2" class="form-control" value="<?=$contactperson->getName2()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Straße</label>
						   <div class="col-sm-9">
							   <input name="street" class="form-control" value="<?=$contactperson->getStreet()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Hausnummer</label>
						   <div class="col-sm-9">
							   <input name="houseno" class="form-control" value="<?=$contactperson->getHouseno()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Adresse.2</label>
						   <div class="col-sm-9">
							   <input name="address2" class="form-control" value="<?=$contactperson->getAddress2()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Plz</label>
						   <div class="col-sm-9">
							   <input name="zip" class="form-control" value="<?=$contactperson->getZip()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Stadt</label>
						   <div class="col-sm-9">
							   <input name="city" class="form-control" value="<?=$contactperson->getCity()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Land</label>
						   <div class="col-sm-9">
							   <select name="country" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
								   <? foreach($countries as $c){ ?>
									   <option value="<?=$c->getId()?>"
										   <?if($contactperson->getCountry()->getId() == $c->getId()) echo "selected";?>>
										   <?=$c->getName()?>
									   </option>
								   <?}

								   ?>
							   </select>
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Telefon</label>
						   <div class="col-sm-9">
							   <input name="phone" class="form-control" value="<?=$contactperson->getPhone()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Fax</label>
						   <div class="col-sm-9">
							   <input name="fax" class="form-control" value="<?=$contactperson->getFax()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Mobil</label>
						   <div class="col-sm-9">
							   <input name="mobil" class="form-control" value="<?=$contactperson->getMobil()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Email</label>
						   <div class="col-sm-9">
							   <input name="email" class="form-control" value="<?=$contactperson->getEmail()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Web</label>
						   <div class="col-sm-9">
							   <input name="web" class="form-control" value="<?=$contactperson->getWeb()?>"
									  onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Geburtstag</label>
						   <div class="col-sm-9">
							   <input name="birthdate" id="birthdate" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)"
								   <? if ($contactperson->getBirthDate() != 0 ) echo 'value="'.date("d.m.Y", $contactperson->getBirthDate()).'"';?>
									  title="<?=$_LANG->get('Geburtstag');?>">
						   </div>
					   </div>
					   <br>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Haupt-Asp</label>
						   <div class="col-sm-3">
							   <input name="main_contact" type="checkbox" value="1"
								   <?if($contactperson->isMainContact()) echo 'checked="checked"'; ?> class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Aktive Adresse</label>
						   <div class="col-sm-9">
							   <select name="active_adress" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
								   <option value="1" <?if($contactperson->getActiveAdress() == 1) echo "selected";?>>normale Adresse</option>
								   <option value="2" <?if($contactperson->getActiveAdress() == 2) echo "selected";?>>Alternativadresse</option>
								   <option value="3" <?if($contactperson->getActiveAdress() == 3) echo "selected";?>>Privatadresse</option>
							   </select>
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Shop-Login</label>
						   <div class="col-sm-9">
						   </div>
					   </div>
					   <? if(Address::getDefaultAddress($business,Address::FILTER_INVC) && Address::getDefaultAddress($business,Address::FILTER_DELIV)){?>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Benutzer</label>
						   <div class="col-sm-9">
							   <input name="shop_login" value="<?=$contactperson->getShopLogin()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)" class="form-control" >
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Passwort</label>
						   <div class="col-sm-9">
							   <input name="shop_pass" value="<?=$contactperson->getShopPassword()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)" class="form-control" >
						   </div>
					   </div>
					   <div id="notify_mail_adr" class="form-group">
						   <label for="" class="col-sm-6 control-label">Benachrichtigungs-Adr.</label>
						   <div class="col-sm-4">
							   <span class="glyphicons glyphicons-plus pointer" onclick="addMailRow()"></span>
							   <? if (count($contactperson->getNotifymailadr()) > 0) { foreach ($contactperson->getNotifyMailAdr() as $notifymailadr){?>
								   <input name="notifymailadr[]" type="mail" class="form-control" value="<?=$notifymailadr?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
								   <span class="glyphicons glyphicons-remove" onclick="JavaScript: $(this).prev().remove(); $(this).remove();"></span>
							   <? }}?>
						   </div>
					   </div>
					   <?}else{?>
					   <div class="form-group">
					     <label for="" class="col-sm-3 control-label"></label>
					     <div class="col-sm-9">
					        <span class="error">Sie m&uuml;ssen zuerst eine Standard Liefer- und Rechnungsadresse anlegen</br>bevor Sie dem Kunden Zugriff auf das Online Portal geben k&ouml;nnen</span>
					     </div>
					   </div>
					   <?}?>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Kommentar</label>
						   <div class="col-sm-9">
							   <textarea rows="5" name="comment" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
								 <?=$contactperson->getComment()?>
							   </textarea>
						   </div>
					   </div>
				   </div>
				   <div class="col-md-4">
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Alternativadresse</label>
						   <div class="col-sm-9">
						   </div>
					   </div>
					   <br>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Nachname</label>
						   <div class="col-sm-9">
							   <input name="alt_name1" value="<?=$contactperson->getAlt_name1()?>" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Vorname</label>
						   <div class="col-sm-9">
							   <input name="alt_name2" class="form-control" value="<?=$contactperson->getAlt_name2()?>"
									  onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Straße</label>
						   <div class="col-sm-9">
							   <input name="alt_street" class="form-control" value="<?=$contactperson->getAltStreet()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Hausnummer</label>
						   <div class="col-sm-9">
							   <input name="alt_houseno" class="form-control" value="<?=$contactperson->getAltHouseno()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Adresse.2</label>
						   <div class="col-sm-9">
							   <input name="alt_address2" class="form-control" value="<?=$contactperson->getAlt_address2()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Plz</label>
						   <div class="col-sm-9">
							   <input name="alt_zip" class="form-control" value="<?=$contactperson->getAlt_zip()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Stadt</label>
						   <div class="col-sm-9">
							   <input name="alt_city" class="form-control" value="<?=$contactperson->getAlt_city()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Land</label>
						   <div class="col-sm-9">
							   <select name="alt_country" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
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
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Telefon</label>
						   <div class="col-sm-9">
							   <input name="alt_phone" class="form-control" value="<?=$contactperson->getAlt_phone()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Fax</label>
						   <div class="col-sm-9">
							   <input name="alt_fax" class="form-control" value="<?=$contactperson->getAlt_fax()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Mobil</label>
						   <div class="col-sm-9">
							   <input name="alt_mobil" class="form-control" value="<?=$contactperson->getAlt_mobil()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">E-Mail</label>
						   <div class="col-sm-9">
							   <input name="alt_email" class="form-control" value="<?=$contactperson->getAlt_email()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <br>
					   <br>
					   <br>
					   <br>
					   <br>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Shop-Freigabe</label>
						   <div class="col-sm-9">
						   </div>
					   </div>
					   <br>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Tickets</label>
						   <div class="col-sm-9">
							   <input name="shop_tickets" class="form-control" type="checkbox" value="1"
								   <?if($contactperson->getEnabledTickets()) echo 'checked="checked"'; ?>
									  onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Personalisierung</label>
						   <div class="col-sm-9">
							   <input name="shop_personalization" type="checkbox" value="1" class="form-control"
								   <?if($contactperson->getEnabledPersonalization()) echo 'checked="checked"'; ?>
									  onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Artikel</label>
						   <div class="col-sm-9">
							   <input name="shop_article" type="checkbox" value="1" class="form-control"
								   <?if($contactperson->getEnabledArtikel()) echo 'checked="checked"'; ?>
									  onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Marketing</label>
						   <div class="col-sm-9">
							   <input name="shop_marketing" type="checkbox" value="1" class="form-control"
								   <?if($contactperson->getEnabledMarketing()) echo 'checked="checked"'; ?>
									  onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
				   </div>
				   <div class="col-md-4">
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Privatadresse</label>
						   <div class="col-sm-9">
						   </div>
					   </div>
					   <br>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Nachname</label>
						   <div class="col-sm-9">
							   <input name="priv_name1" class="form-control" value="<?=$contactperson->getPriv_name1()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Vorname</label>
						   <div class="col-sm-9">
							   <input name="priv_name2" class="form-control" value="<?=$contactperson->getPriv_name2()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Straße</label>
						   <div class="col-sm-9">
							   <input name="priv_street" class="form-control" value="<?=$contactperson->getPrivStreet()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Hausnummer</label>
						   <div class="col-sm-9">
							   <input name="priv_houseno" class="form-control" value="<?=$contactperson->getPrivHouseno()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Adresse.2</label>
						   <div class="col-sm-9">
							   <input name="priv_address2" class="form-control" value="<?=$contactperson->getPriv_address2()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Plz</label>
						   <div class="col-sm-9">
							   <input name="priv_zip" class="form-control" value="<?=$contactperson->getPriv_zip()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Stadt</label>
						   <div class="col-sm-9">
							   <input name="priv_city" class="form-control" value="<?=$contactperson->getPriv_city()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Land</label>
						   <div class="col-sm-9">
							   <select name="priv_country" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
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
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Telefon</label>
						   <div class="col-sm-9">
							   <input name="priv_phone" class="form-control" value="<?=$contactperson->getPriv_phone()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Fax</label>
						   <div class="col-sm-9">
							   <input name="priv_fax" class="form-control" value="<?=$contactperson->getPriv_fax()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Mobil</label>
						   <div class="col-sm-9">
							   <input name="priv_mobil" class="form-control" value="<?=$contactperson->getPriv_Mobil()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">E-Mail</label>
						   <div class="col-sm-9">
							   <input name="priv_email" class="form-control" value="<?=$contactperson->getPriv_email()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
				   </div>
			   </div>
			  <br>
			  <br>
			  <div class="panel panel-default">
			  	  <div class="panel-heading">
			  			<h3 class="panel-title">
							Merkmale
						</h3>
			  	  </div>
			  	 <div class="table-responsive">
			  	 	<table id="table-attributes" class="table table-hover">
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
							<tbody>
								<tr>
									<td class="content_row_header" valign="top"><?=$attribute->getTitle()?></td>
									<td class="content_row_clear">
										<? 	$allitems = $attribute->getItems();?>
										<table class="table table-hover">
											<?	$x=0;
											foreach ($allitems AS $item){
												if ($x%5 == 0) echo "<tr>";
												echo '<td>';
												echo '<input name="attribute_item_check_'.$attribute->getId().'_'.$item["id"].'" ';
												echo ' value="1" type="checkbox" onfocus="markfield(this,0)" onblur="markfield(this,1)"';
												if ($all_active_attributes["{$attribute->getId()}_{$item["id"]}"]["value"] == 1) echo "checked";
												echo ">";
												echo '&nbsp';
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
							</tbody>
						<?}?>
			  	 	</table>
			  	 </div>
			  </div>
			  <br>
			  <br>
			  <?php
			  $ticket_categories = TicketCategory::getAllCategories();
			  ?>
			  <input 	type="hidden" name="count_categories" id="count_categories"
						value="<? if(count($ticket_categories) > 0) echo count($ticket_categories); else echo "1";?>">
			  <?php foreach ($ticket_categories as $tc){?>
				  <div class="panel panel-default">
					  <div class="panel-heading">
							<h3 class="panel-title">
								<?php echo $tc->getTitle()?>
							</h3>
					  </div>
					  <div class="table-responsive">
						  <table class="table table-hover">
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
					  </div>
				  </div>
			  <?php }?>
		  </form>
	  </div>
</div>





