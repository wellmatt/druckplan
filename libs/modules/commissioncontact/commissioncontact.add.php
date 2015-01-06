<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       27.01.2014
// Copyright:     2012-2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/commissioncontact/commissioncontact.class.php';


$_REQUEST["id"] = (int)$_REQUEST["id"];
$commissionContact = new CommissionContact($_REQUEST["id"]);

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
    $commissionContact->setActive(1);
    $commissionContact->setcustomer(trim(addslashes($_REQUEST["customer"])));
    $commissionContact->setSupplier(trim(addslashes($_REQUEST["supplier"])));
    $commissionContact->setName1(trim(addslashes($_REQUEST["name1"])));
    $commissionContact->setName2(trim(addslashes($_REQUEST["name2"])));
    $commissionContact->setAddress1(trim(addslashes($_REQUEST["address1"])));
    $commissionContact->setAddress2(trim(addslashes($_REQUEST["address2"])));
    $commissionContact->setZip(trim(addslashes($_REQUEST["zip"])));
    $commissionContact->setCity(trim(addslashes($_REQUEST["city"])));
    $commissionContact->setCountry(new Country (trim(addslashes($_REQUEST["country"]))));
    $commissionContact->setEmail(trim(addslashes($_REQUEST["email"])));
    $commissionContact->setPhone(trim(addslashes($_REQUEST["phone"])));
    $commissionContact->setFax(trim(addslashes($_REQUEST["fax"])));
    $commissionContact->setBic(trim(addslashes($_REQUEST["bic"])));
    $commissionContact->setIban(trim(addslashes($_REQUEST["iban"])));
    $commissionContact->setWeb(trim(addslashes($_REQUEST["web"])));
    $commissionContact->setClient(new Client((int)$_REQUEST["client"]));
    $commissionContact->setLanguage(new Translator((int)$_REQUEST["language"]));
    $commissionContact->setDiscount((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["discount"]))));
    $commissionContact->setPaymentTerms(new PaymentTerms((int)$_REQUEST["payment"]));
    $commissionContact->setComment(trim(addslashes($_REQUEST["comment"])));
    $commissionContact->setShoplogin(trim(addslashes($_REQUEST["shop_login"])));
    $commissionContact->setKreditor((int)($_REQUEST["kreditor"]));
    $commissionContact->setDebitor((int)($_REQUEST["debitor"]));
    $commissionContact->setUst(trim(addslashes($_REQUEST["ust"])));
    $commissionContact->setNum_at_customer(trim(addslashes($_REQUEST["kdnr_at_cust"])));
    $commissionContact->setTaxnumber(trim(addslashes($_REQUEST["taxnumber"])));
    $commissionContact->setBranche(trim(addslashes($_REQUEST["branche"])));
    $commissionContact->setCommissionpartner(trim(addslashes($_REQUEST["commissionpartner"])));
    $commissionContact->setProvision(trim(addslashes($_REQUEST["provision"])));
    
    //if ($_REQUEST["shop_pass1"] != "" && $_REQUEST["shop_pass1"] == $_REQUEST["shop_pass2"]){
    	$commissionContact->setShoppass(trim(addslashes($_REQUEST["shop_pass1"])));
    //}
    
    if ((int)$_REQUEST["login_expire"] != 0){
    	$_REQUEST["login_expire"] = explode(".", $_REQUEST["login_expire"]);
    	$commissionContact->setLoginexpire((int)mktime(12, 0, 0, $_REQUEST["login_expire"][1], $_REQUEST["login_expire"][0], $_REQUEST["login_expire"][2]));
    } else {
    	$commissionContact->setLoginexpire(0);
    }
   
    $savemsg = getSaveMessage($commissionContact->save());
    $savemsg .= $DB->getLastError();
}
global $_CONFIG;
$_USER;
$languages = Translator::getAllLangs(Translator::ORDER_NAME);
$countries = Country::getAllCountries();
if($commissionContact->getId()){
	$contactPersons = ContactPerson::getAllContactPersons($commissionContact,ContactPerson::ORDER_NAME);
	$deliveryAddresses = Address::getAllAddresses($commissionContact,Address::ORDER_NAME,Address::FILTER_DELIV);
	$invoiceAddresses = Address::getAllAddresses($commissionContact,Address::ORDER_NAME,Address::FILTER_INVC);
}
?>


<script>
	$(function() {
		$( "#tabs" ).tabs();
	});
</script>

<script language="javascript">
function checkpass(obj){
	//var shop_pass1 = document.getElementById('shop_pass1').value;
	//var shop_pass2 = document.getElementById('shop_pass2').value;
	//if (shop_pass1 != shop_pass2){
	//	alert('<?=$_LANG->get('Passw&ouml;rter stimmen nicht &uuml;berein')?>');
	//	document.getElementById('shop_pass1').focus();
	//	return false;
	//}
	return checkform(obj);
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
</script>

	
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="user_form" enctype="multipart/form-data"
		onSubmit="return checkpass(new Array(this.name1));" > 
	<?// gucken, ob die Passw�rter (Webshop-Login) gleich sind und ob alle notwendigen Felder gef�llt sind?>
	
	<input type="hidden" name="exec" value="edit"> 
	<input type="hidden" name="subexec" value="save"> 
	<input type="hidden" name="subform" value="user_details">
	<input type="hidden" name="id" value="<?=$commissionContact->getId()?>">
	
<div class="demo">	
	<div id="tabs">
		<ul>
			<li><a href="#tabs-1"><? echo $_LANG->get('Stammdaten');?></a></li> 
			<li><a href="#tabs-3"><? echo $_LANG->get('Ansprechpartner');?></a></li>
            <!--<li><a href="#tabs-2"><?/* echo $_LANG->get('Adressen');*/?></a></li>-->
            <?/*if ($_CONFIG->shopActivation){*/?><!--
				<li><a href="#tabs-4"><?/* echo $_LANG->get('Kundenportal');*/?></a></li>
			<?/*}*/?>
			<li><a href="#tabs-6"><?/* echo $_LANG->get('Notizen/Dokumente');*/?></a></li>-->
		</ul>
		

<div id="tabs-1"><p>

<table width="100%">
	<tr>
		<td width="200" class="content_header"><img
			src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <? if ($commissionContact->getId()) echo $_LANG->get('Provisionskontakt &auml;ndern'); else echo $_LANG->get('Provisionskontakt hinzuf&uuml;gen');?>
		</td>
		<td></td>
		<td width="200" class="content_header" align="right"><?=$savemsg?></td>
	</tr>
</table>

<table><tr><td width="500">
	<table width="100%">
		<colgroup>
			<col width="180">
			<col>
		</colgroup>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Firma');?> *</td>
			<td class="content_row_clear"><input name="name1" style="width: 300px"
				class="text" value="<?=$commissionContact->getName1()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Firmenzusatz');?></td>
			<td class="content_row_clear"><input name="name2"
				style="width: 300px" class="text" value="<?=$commissionContact->getName2()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Stra&szlig;e');?>
			</td>
			<td class="content_row_clear"><input name="address1"
				style="width: 300px" class="text" value="<?=$commissionContact->getAddress1()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Adresszusatz');?>
			</td>
			<td class="content_row_clear"><input name="address2"
				style="width: 300px" class="text" value="<?=$commissionContact->getAddress2()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Postleitzahl');?>
			</td>
			<td class="content_row_clear"><input name="zip"
				style="width: 300px" class="text" value="<?=$commissionContact->getZip()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Stadt');?>
			</td>
			<td class="content_row_clear"><input name="city"
				style="width: 300px" class="text" value="<?=$commissionContact->getCity()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
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
					<?if ($commissionContact->getCountry()->getId() == $c->getId()) echo "selected";?>>
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
				style="width: 300px" class="text" value="<?=$commissionContact->getPhone()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Fax');?>
			</td>
			<td class="content_row_clear"><input name="fax"
				style="width: 300px" class="text" value="<?=$commissionContact->getFax()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('E-Mail');?>
			</td>
			<td class="content_row_clear"><input name="email"
				style="width: 300px" class="text" value="<?=$commissionContact->getEmail()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Internetseite');?>
			</td>
			<td class="content_row_clear"><input name="web"
				style="width: 300px" class="text" value="<?=$commissionContact->getWeb()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Kunde');?></td>
			<td class="content_row_clear"><select name="customer" style="width: 300px"
				class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
					<option value="0" <? if(! ($commissionContact->isExistingCustomer() && $commissionContact->isPotentialCustomer())) echo "selected";?>>
						
					</option>
					<option value="1" <? if($commissionContact->isExistingCustomer()) echo "selected";?>>
						<?=$_LANG->get('Bestandskunde')?>
					</option>
					<option value="2" <? if($commissionContact->isPotentialCustomer()) echo "selected";?>>
						<?=$_LANG->get('Sollkunde')?>
					</option>
			</select>
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Lieferant');?></td>
			<td class="content_row_clear"><input name="supplier"
				type="checkbox" value="1"
				<? if ($commissionContact->isSupplier()) echo "checked";?>
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Mandant')?></td>
			<td class="content_row_clear"><select name="client"
				style="width: 300px" class="text" onfocus="markfield(this,0)"
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
			<td class="content_row_header"><?=$_LANG->get('Sprache')?></td>
			<td class="content_row_clear"><select name="language" style="width: 300px"
				class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
					<?
					foreach($languages as $l)
					{?>
					<option value="<?=$l->getId()?>"
					<?if ($commissionContact->getLanguage()->getId() == $l->getId()) echo "selected";?>>
						<?=$l->getName()?>
					</option>
					<?}

					?>
			</select>
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Kommentar')?></td>
			<td class="content_row_clear">&nbsp;</td>
		</tr>
		<tr>
			<td class="content_row_clear" colspan="2"><textarea name="comment"
					style="width: 482px; height: 150px">
					<?=$commissionContact->getComment()?>
				</textarea>
			</td>
		</tr>
		<tr>
			<td class="content_row_header">&nbsp;</td>
			<td class="content_row_clear">&nbsp;</td>
		</tr>
	</table>
	</td><td valign="top">
		<table width="100%">
		<colgroup>
			<col width="180">
			<col>
		</colgroup>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Kreditor-Nr.')?></td>
			<td class="content_row_clear">
			    <input class="text" style="width:100px" name="kreditor" 
			    		value="<?=$commissionContact->getKreditor()?>"> 
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Debitor-Nr.')?></td>
			<td class="content_row_clear">
			    <input class="text" style="width:100px" name="debitor" 
			    		value="<?=$commissionContact->getDebitor()?>"> 
			</td>
		</tr>
		<!-- tr>
			<td class="content_row_header"><?=$_LANG->get('Branche')?></td>
			<td class="content_row_clear">
			    <input class="text" style="width:300px" name="branche" 
			    		value="<?=$commissionContact->getBranche()?>"> 
			</td>
		</tr-->
		<tr>
			<td class="content_row_header">&ensp;</td>
			<td class="content_row_clear">&ensp;</td>
		</tr>
		<!-- tr>
			<td class="content_row_header"><?=$_LANG->get('KD-Nr. beim Lieferanten')?></td>
			<td class="content_row_clear">
			    <input class="text" style="width:100px" name="kdnr_at_cust" 
			    		value="<?=$commissionContact->getNum_at_customer()?>"> 
			</td>
		</tr-->
		<tr>
			<td class="content_row_header"><?=$_LANG->get('USt.-ID')?></td>
			<td class="content_row_clear">
			    <input class="text" style="width:100px" name="ust" 
			    		value="<?=$commissionContact->getUst()?>"> 
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Steuernummer')?></td>
			<td class="content_row_clear">
			    <input class="text" style="width:100px" name="taxnumber" 
			    		value="<?=$commissionContact->getTaxnumber()?>"> 
			</td>
		</tr>
		<tr>
			<td class="content_row_header">&ensp;</td>
			<td class="content_row_clear">&ensp;</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Zahlungsart')?></td>
			<td class="content_row_clear">
			    <select name="payment" style="width:300px" class="text">
			    	<option value="0" <? if ($commissionContact->getPaymentTerms()->getId() == 0) 
			    							echo "selected"?> >
			    	</option>
			        <? 
			        foreach(PaymentTerms::getAllPaymentConditions(PaymentTerms::ORDER_NAME) as $pt)
			        {
			            echo '<option value="'.$pt->getId().'"';
			            if ($pt->getId() == $commissionContact->getPaymentTerms()->getId()){
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
			    		value="<?=$commissionContact->getIban()?>"> 
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('BIC')?></td>
			<td class="content_row_clear">
			    <input class="text" style="width:150px" name="bic" 
			    		value="<?=$commissionContact->getBic()?>"> 
			</td>
		</tr>
		<tr>
                <td class="content_row_header"><?=$_LANG->get('Rabatt')?></td>
                <td class="content_row_clear">
                    <input class="text" style="width:80px" name="discount"
                           value="<?=printPrice($commissionContact->getDiscount())?>"> %
                </td>
            </tr>
			<tr>
                <td class="content_row_header"><?=$_LANG->get('Provision (%)')?></td>
                <td class="content_row_clear">
                    <input class="text" style="width:80px" name="discount"
                           value="<?=$commissionContact->getProvision()?>"> %
                </td>
            </tr>
            <!-- Provision -->
            <!--<tr>
                <td class="content_row_header"><?/*=$_LANG->get('Provisionspartner')*/?></td>
                <td class="content_row_clear">
                    <input name="commissionpartner" type="checkbox" value="1"
                        <?/* if ($commissionContact->isCommissionpartner()) echo "checked";*/?>
                        onfocus="markfield(this,0)" onblur="markfield(this,1)">
                </td>
            </tr>-->
		<tr>
			<td class="content_row_header">&ensp;</td>
			<td class="content_row_clear">&ensp;</td>
		</tr>
		<? if($commissionContact->getLectorId() > 0) { ?>
		<tr>
			<td class="content_row_header"><span class="error"><?=$_LANG->get('Lector-Import')?>: </span></td>
			<td class="content_row_clear">ID: <?=$commissionContact->getId()?></td>
		</tr>
		<?  } ?>
		</table>
	</td></tr></table>
</p></div>
	
	<? /**************************************** Adressen **************************************************/ ?>
	<!--<div id="tabs-2"><p>
	
	<?/*if($commissionContact->getId()){*/?>
		<table width="100%">
			<colgroup>
				<col>
				<col>
				<col>
				<col>
				<col>
			</colgroup>
			
			<tr>
				<td class="content_row_header"> <?php /*echo $_LANG->get('Rechnungsadresse');*/?></td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear" align="right"><a href="index.php?exec=edit_ai&id=<?/*=$commissionContact->getID()*/?>"><img src="images/icons/user--plus.png"> <?/*=$_LANG->get('Addresse hinzuf&uuml;gen')*/?></a></td>
			</tr>
			<?php /*$addressInvoice = Address::getAllAddresses($commissionContact,Address::ORDER_NAME,Address::FILTER_INVC);
			foreach($addressInvoice as $ai)
			{
			*/?>
			<tr>
				<td><?/* echo $ai->getName1() . ' ' . $ai->getName2();*/?></td>
				<td><?/* echo $ai->getAddress1();*/?></td>
				<td><?/* echo $ai->getAddress2();*/?></td>
				<td><?/* echo $ai->getCity();*/?></td>
				<td class="content_row_clear" align="right">
	            	<a href="index.php?exec=edit_ai&id_a=<?/*=$ai->getId()*/?>&id=<?/*=$commissionContact->getID()*/?>"><img src="images/icons/pencil.png"></a>
	            	<a href="index.php?exec=delete_a&id_a=<?/*=$ai->getId()*/?>&id=<?/*=$commissionContact->getID()*/?>" onclick="askDel('index.php?exec=delete_a&id_a=<?/*=$ai->getId()*/?>&id=<?/*=$commissionContact->getID()*/?>')"><img src="images/icons/cross-script.png"></a>
	        	</td>
	        </tr>
	        <?php /*
				
			}
			*/?>
		</table>
	<?/*}*/?>

	<?/*if($commissionContact->getId()){*/?>
		<table width="100%">
			<colgroup>
				<col>
				<col>
				<col>
				<col>
				<col>
			</colgroup>
			
			<tr>
				<td class="content_row_header"> <?php /*echo $_LANG->get('Lieferadresse');*/?></td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear" align="right"><a href="index.php?exec=edit_ad&id=<?/*=$commissionContact->getID()*/?>"><img src="images/icons/user--plus.png"> <?/*=$_LANG->get('Addresse hinzuf&uuml;gen')*/?></a></td>
			</tr>
			<?php /*
			$addressDelivery = Address::getAllAddresses($commissionContact,Address::ORDER_NAME,Address::FILTER_DELIV);
			foreach($addressDelivery as $ad)
			{
			*/?>
			<tr>
				<td><?/* echo $ad->getName1() . ' ' . $ad->getName2();*/?></td>
				<td><?/* echo $ad->getAddress1();*/?></td>
				<td><?/* echo $ad->getAddress2();*/?></td>
				<td><?/* echo $ad->getCity();*/?></td>
				<td class="content_row_clear" align="right">
	            	<a href="index.php?exec=edit_ad&id_a=<?/*=$ad->getId()*/?>&id=<?/*=$commissionContact->getID()*/?>"><img src="images/icons/pencil.png"></a>
	            	<a href="index.php?exec=delete_a&id_a=<?/*=$ad->getId()*/?>&id=<?/*=$commissionContact->getID()*/?>" onclick="askDel('index.php?exec=delete_a&id_a=<?/*=$ad->getId()*/?>&id=<?/*=$commissionContact->getID()*/?>')"><img src="images/icons/cross-script.png"></a>
	        	</td>
	        </tr>
	        <?php /*
			}
			*/?>
		</table>
	<?/*}*/?>


	</p></div>-->

	<? /****************************************** Ansprechpartner **************************************/ ?>
	
	<div id="tabs-3"><p>

	<?if($commissionContact->getId()){?>
		<table width="100%">
			<colgroup>
				<col>
				<col>
				<col>
				<col>
				<col>
			</colgroup>
			
			<tr>
				<td class="content_row_header"><? echo $_LANG->get('Ansprechpartner');?></td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear" align="right"><a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit_cp&id=<?=$commissionContact->getID()?>"><img src="images/icons/user--plus.png"> <?=$_LANG->get('Ansprechpartner hinzuf&uuml;gen')?></a></td>
			</tr>
			<?php $contactPerson = ContactPerson::getAllContactPersons($commissionContact,ContactPerson::ORDER_NAME);
			foreach($contactPerson as $cp)
			{
			?>
			<tr>
				<td>
					<?php echo $cp->getNameAsLine(); ?>
				</td>
				<td>
					<?php echo $cp->getCity();?>
				</td>
				<td>
					<?php echo $cp->getPhone();?>
				</td>
				<td></td>
				<td class="content_row_clear" align="right">
	            	<a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit_cp&cpid=<?=$cp->getId()?>&id=<?=$commissionContact->getID()?>"><img src="images/icons/pencil.png"></a>
	            	<a href="index.php?page=<?=$_REQUEST['page']?>&exec=delete_cp&cpid=<?=$cp->getId()?>&id=<?=$commissionContact->getID()?>" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete_cp&cpid=<?=$cp->getId()?>&id=<?=$commissionContact->getID()?>')"><img src="images/icons/cross-script.png"></a>
	        	 </td>
	        </tr>
	        <?
			}
			?>
		</table>
	<?}?>

	</p><p></p></div>
	
	<? /********************************** Kundenportal ******************************************/ ?>
	
	<?/*if ($_CONFIG->shopActivation){*/?><!--
		<div id="tabs-4"><p>
		
		<table width="100%">
			<tr>
				<td width="200" class="content_header">
					<img src="<?/*=$_MENU->getIcon($_SESSION["pid"])*/?>">
					<?/*=$_LANG->get('Shop-Login &auml;ndern');*/?>
				</td>
				<td></td>
				<td width="200" class="content_header" align="right"><?/*=$savemsg*/?></td>
			</tr>
		</table>
		
		<?/*if($commissionContact->getId()){*/?>
			<table width="100%">
				<colgroup>
					<col width="180">
					<col>
				</colgroup>
				<tr>
					<td class="content_row_header"><?/*=$_LANG->get('Benutzername')*/?> *</td>
					<td class="content_row_clear">
						<input 	name="shop_login" style="width: 300px"
								class="text" value="<?/*=$commissionContact->getShoplogin()*/?>"
								onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?/*=$_LANG->get('Passwort')*/?></td>
					<td class="content_row_clear">
						<input 	name="shop_pass1" id="shop_pass1" style="width: 300px" 
								class="text" value="<?/*=$commissionContact->getShoppass()*/?>"
								type="text"	onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<!-- tr>
					<td class="content_row_header"><?/*=$_LANG->get('Passwort wiederholen')*/?></td>
					<td class="content_row_clear">
						<input 	name="shop_pass2" id="shop_pass2" style="width: 300px" class="text" value="" 
								type="password"	onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr-->
				<!--<tr>
					<td class="content_row_header"><?/*=$_LANG->get('G&uuml;ltigkeit')*/?></td>
					<td class="content_row_clear">
						<input type="text" style="width:80px" id="login_expire" name="login_expire"
								class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
								onfocus="markfield(this,0)" onblur="markfield(this,1)"
								value="<?/*if($commissionContact->getLoginexpire() != 0){ echo date('d.m.Y', $commissionContact->getLoginexpire());}*/?>">
					</td>
				</tr>-->
				<!-- tr>
					<td class="content_row_header"><?/*=$_LANG->get('Ticket-Freigabe');*/?></td>
					<td class="content_row_clear">
						<input name="ticket_enabled" type="checkbox" value="1" <?/* if ($commissionContact->getTicketenabled()) echo "checked";*/?>
							   onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr-->
			</table>
		<?/*}*/?>
		
		</p>
		</div>
	<?/*}*/?>
	
		<? /*// ------------------------------------- verbundene Tickets ----------------------------------------------?>
		
		<div id="tabs-7">
		<?if($cp->getId()){?>
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
				$from_cc = true;
				$contactID = $cp->getId();
				require_once 'libs/modules/tickets/ticket.for.php';?>
		<? } ?>
		</div>
	    <? */ ?>
	
	</div>
	
	<table width="100%">
	    <colgroup>
	        <col width="180">
	        <col>
	    </colgroup> 
	    <tr>
	        <td class="content_row_header">
	        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
	        			onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
	        </td>
	        <td class="content_row_clear" align="right">
	        	<input type="submit" value="<?=$_LANG->get('Speichern')?>">
	        </td>
	    </tr>
	</table>
</form>	
	
</div>
	
<!--//-->