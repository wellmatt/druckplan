<?php // ---------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       26.02.2014
// Copyright:     2012-14 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

$_REQUEST["id_a"] = (int)$_REQUEST["id_a"];
$address = new Address($_REQUEST["id_a"]);

$address->setBusinessContact(new BusinessContact((int)$_REQUEST["id"]));
if ($_REQUEST["exec"] == "edit_ai")
{
	$address->setActive(1);
}elseif ($_REQUEST["exec"] == "edit_ad")
{
	$address->setActive(2);
}
if ($_REQUEST["exec"] == "save_a")
{
    $address->setActive(trim(addslashes($_REQUEST["active"])));
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
    $address->setShoprel($_REQUEST["adr_shoprel"]);
    $address->setDefault($_REQUEST["adr_default"]);
    
    $savemsg = getSaveMessage($address->save());
    $savemsg .= $DB->getLastError();
     
}
$countries = Country::getAllCountries();

?>

<table width="100%">
	<tr>
		<td width="200" class="content_header"><img
			src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <? if ($address->getId()) echo $_LANG->get('Adresse &auml;ndern'); else echo $_LANG->get('Addresse hinzuf&uuml;gen');?>
		</td>
		<td></td>
		<td width="200" class="content_header" align="right"><?=$savemsg?></td>
	</tr>
</table>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="user_form"
	onsubmit="return checkform(new Array(this.name1))">
	<input type="hidden" name="exec" value="edit_<?php if($address->getActive == 1){ echo 'ai';}else{echo 'ad';}?>></input>"> <input type="hidden"
		name="exec" value="save_a"><input type="hidden" name="active"
		value="<?=$address->getActive()?>"><input type="hidden" name="id_a"
		value="<?=$address->getId()?>"><input type="hidden" name="id"
		value="<?=$address->getBusinessContact()->getId()?>">
	<div class="box1">
	<table width="500px">
		<colgroup>
			<col width="180">
			<col>
		</colgroup>
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
			<td class="content_row_header"><?=$_LANG->get('Postleitzahl');?>
			</td>
			<td class="content_row_clear"><input name="zip"
				style="width: 300px" class="text" value="<?=$address->getZip()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Stadt');?>
			</td>
			<td class="content_row_clear"><input name="city"
				style="width: 300px" class="text" value="<?=$address->getCity()?>"
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
		<tr style="display:<?if($address->getActive() != 2){ echo 'none';}else{echo '';}?>"><?/*gln*/?>
			<td class="content_row_header"><?=$_LANG->get('Shop-Freigabe')?></td>
			<td class="content_row_clear">
				<input 	id="adr_shoprel" name="adr_shoprel" class="text" type="checkbox" 
						value="1" <?if ($address->getShoprel() == 1) echo "checked"; ?>>
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Standard-Adresse')?></td>
			<td class="content_row_clear">
				<input 	id="adr_default" name="adr_default" class="text" type="checkbox" 
						value="1" <?if ($address->getDefault() == 1) echo "checked"; ?>>
			</td>
		</tr>
	</table>
	</div>
	
	<table width="1000">
		<colgroup>
			<col width="180px">
			<col align="right">
		</colgroup>
		<tr>
			<td class="content_row_header">
			    <input type="button" class="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$_REQUEST["id"]?>'">
			</td>
			<td class="content_row_clear" align="right">
				<?if($_USER->getId() != 14){ ?>
					<input type="submit" value="<?=$_LANG->get('Speichern')?>">
				<?}?>
			</td>
	        <td class="content_row_clear" align="right">
	        	<? if($_USER->hasRightsByGroup(Group::RIGHT_DELETE_BC) || $_USER->isAdmin()){ ?>
		        	<?if($_REQUEST["exec"] != "new"){?>
		        		<input type="button" class="buttonRed" onclick="askDel('index.php?exec=delete_a&id_a=<?=$address->getId()?>&id=<?=$address->getBusinessContact()->getID()?>')" 
		        				value="<?=$_LANG->get('L&ouml;schen')?>">
		        	<?}?>
		        <?}?>
	        </td>
		</tr>
	</table>
</form>