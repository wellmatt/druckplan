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
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<? if ($address->getId()) echo $_LANG->get('Adresse &auml;ndern'); else echo $_LANG->get('Addresse hinzuf&uuml;gen');?>
				<span class="pull-right">
					<?=$savemsg?>
				</span>
			</h3>
	  </div>
	  <div class="panel-body">
		  <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" class="form-horizontal" name="user_form" onsubmit="return checkform(new Array(this.name1))">
			  <input type="hidden" name="exec" value="edit_<?php if($address->getActive == 1){ echo 'ai';}else{echo 'ad';}?>></input>">
			  <input type="hidden" name="exec" value="save_a">
			  <input type="hidden" name="active" value="<?=$address->getActive()?>">
			  <input type="hidden" name="id_a" value="<?=$address->getId()?>">
			  <input type="hidden" name="id" value="<?=$address->getBusinessContact()->getId()?>">

			  <div class="form-group">
				  <label for="" class="col-sm-3 control-label">Firma</label>
				  <div class="col-sm-4">
					  <input name="name1" class="form-control" value="<?=$address->getName1()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-3 control-label">Firmenzusatz</label>
				  <div class="col-sm-4">
					  <input name="name2" class="form-control" value="<?=$address->getName2()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-3 control-label">Adresse</label>
				  <div class="col-sm-4">
					  <input name="address1" class="form-control" value="<?=$address->getAddress1()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-3 control-label">Adresszusatz</label>
				  <div class="col-sm-4">
					  <input name="address2" class="form-control" value="<?=$address->getAddress2()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-3 control-label">Postleitzahl</label>
				  <div class="col-sm-4">
					  <input name="zip" class="form-control" value="<?=$address->getZip()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-3 control-label">Stadt</label>
				  <div class="col-sm-4">
					  <input name="city" class="form-control" value="<?=$address->getCity()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-3 control-label">Land</label>
				  <div class="col-sm-4">
					  <select name="country" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						  <?
						  foreach($countries as $c)
						  {?>
							  <option value="<?=$c->getId()?>"
								  <?if ($address->getCountry()->getId() == $c->getId()) echo "selected";?>>
								  <?=$c->getName()?>
							  </option>
						  <?}?>
					  </select>
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-3 control-label">Telefon</label>
				  <div class="col-sm-4">
					  <input name="phone" class="form-control" value="<?=$address->getPhone()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-3 control-label">Fax</label>
				  <div class="col-sm-4">
					  <input name="fax" class="form-control" value="<?=$address->getFax()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-3 control-label">Mobil</label>
				  <div class="col-sm-4">
					  <input name="mobil" class="form-control" value="<?=$address->getMobil()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-3 control-label">Shop-Freigabe</label>
				  <div class="col-sm-4">
					  <input id="adr_shoprel" name="adr_shoprel" class="text" type="checkbox" value="1" <?if ($address->getShoprel() == 1) echo "checked"; ?>>
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-3 control-label">Standard-Adresse</label>
				  <div class="col-sm-4">
					  <input id="adr_default" name="adr_default" class="text" type="checkbox" value="1" <?if ($address->getDefault() == 1) echo "checked"; ?>>
				  </div>
			  </div>
			  &nbsp;
			  &nbsp;
			  <div class="form-group">
				  <div class="col-sm-4">
					  <button class="btn btn-origin btn-success" type="button" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$_REQUEST["id"]?>'">
						  <?=$_LANG->get('Zur&uuml;ck')?>
					  </button>
				  </div>
				  <div class="col-sm-4">
					  <?if($_USER->getId() != 14){ ?>
						  <button class="btn btn-origin btn-success" type="submit">
							  <?=$_LANG->get('Speichern')?>
						  </button>
					  <?}?>
				  </div>
				  <div class="col-sm-4">
					  <? if($_USER->hasRightsByGroup(Permission::BC_DELETE) || $_USER->isAdmin()){ ?>
						  <?if($_REQUEST["exec"] != "new"){?>
							  <button class="btn btn-origin btn-danger" type="button" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete_a&id_a=<?=$address->getId()?>&id=<?=$address->getBusinessContact()->getID()?>')">
								  <?=$_LANG->get('L&ouml;schen')?>
							  </button>
						  <?}?>
					  <?}?>
				  </div>
			  </div>

	  </div>
</div>


	<table width="1000">
		<colgroup>
			<col width="180px">
			<col align="right">
		</colgroup>
		<tr>


	        <td class="content_row_clear" align="right">

	        </td>
		</tr>
	</table>
</form>