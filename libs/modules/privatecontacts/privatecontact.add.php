<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       08.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/privatecontacts/privatecontact.class.php';

$_REQUEST["id"] = (int)$_REQUEST["id"];

$privatecontact = new PrivateContact($_REQUEST["id"]);

if ($_REQUEST["exec"] == "delete"){
    $privatecontact->delete();
    echo '<script language="JavaScript">parent.location.href="index.php?page=libs/modules/privatecontacts/privatecontact.overview.php";</script>';
}
if ($_REQUEST["exec"] == "save"){
    $privatecontact->setActive(1);
    $privatecontact->setTitle(trim(addslashes($_REQUEST["title"])));
    $privatecontact->setName1(trim(addslashes($_REQUEST["name1"])));
    $privatecontact->setName2(trim(addslashes($_REQUEST["name2"])));
    $privatecontact->setAddress1(trim(addslashes($_REQUEST["address1"])));
    $privatecontact->setAddress2(trim(addslashes($_REQUEST["address2"])));
    $privatecontact->setZip(trim(addslashes($_REQUEST["zip"])));
    $privatecontact->setCity(trim(addslashes($_REQUEST["city"])));
    $privatecontact->setCountry(new Country (trim(addslashes($_REQUEST["country"]))));
    $privatecontact->setEmail(trim(addslashes($_REQUEST["email"])));
    $privatecontact->setPhone(trim(addslashes($_REQUEST["phone"])));
    $privatecontact->setMobil(trim(addslashes($_REQUEST["mobil"])));
    $privatecontact->setFax(trim(addslashes($_REQUEST["fax"])));
    $privatecontact->setWeb(trim(addslashes($_REQUEST["web"])));
    $privatecontact->setComment(trim(addslashes($_REQUEST["comment"])));
    $tmp_busi = new BusinessContact((int)$_REQUEST["customer"]);
    $privatecontact->setBusinessContact($tmp_busi);
    
	if($_REQUEST["birthdate"] != ""){
		$tmp_date = explode('.', trim(addslashes($_REQUEST["birthdate"])));
		$tmp_date = mktime(2,0,0,$tmp_date[1],$tmp_date[0],$tmp_date[2]);
	} else {
		$tmp_date = 0;
	}
	$privatecontact->setBirthDate($tmp_date);
	
	$user_list = Array();
	if ($_REQUEST["access"]){
	    foreach ($_REQUEST["access"] as $qusr)
	    {
	        $user_list[] = new User((int)$qusr);
	    }
	}
	$privatecontact->setAccess($user_list);
	
    $savemsg = getSaveMessage($privatecontact->save());
    $_REQUEST["id"] = $privatecontact->getId();
    
    $privatecontact = new PrivateContact($_REQUEST["id"]);
}
$countries = Country::getAllCountries();
?>
<script language="javascript">
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
 <script>
$(function() {
   $( "#customer_search" ).autocomplete({
        delay: 0,
        source: 'libs/modules/tickets/ticket.ajax.php?ajax_action=search_customer',
		minLength: 2,
		dataType: "json",
        select: function(event, ui) {
			$('#customer').val(ui.item.value);
			$('#customer_search').val(ui.item.label);
			return false;
        }
    });
});
</script>

<table width="100%">
	<tr>
		<td width="300" class="content_header"><img
			src="images/icons/user-detective.png"> <? if ($privatecontact->getId()) echo $_LANG->get('Kontakt &auml;ndern'); else echo $_LANG->get('Kontakt hinzuf&uuml;gen');?>
		</td>
		<td align="right"><?=$savemsg?></td>
	</tr>
</table>

<div id="fl_menu">
	<div class="label">Quick Move</div>
	<div class="menu">
        <a href="#top" class="menu_item">Seitenanfang</a>
        <a href="#" class="menu_item" onclick="document.location='index.php?page=libs/modules/privatecontacts/privatecontact.overview.php'">Zurück</a>
        <a href="#" class="menu_item" onclick="$('#user_form').submit();">Speichern</a>
    	<? if(($privatecontact->getId()>0 && $privatecontact->getCrtuser()->getId() == $_USER->getId()) || $_USER->isAdmin()){ ?>
        	<?if($_REQUEST["exec"] != "new"){?>
                <a href="#" class="menu_item_delete" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$privatecontact->getId()?>')">Löschen</a>
        	<?}?>
        <?}?>
    </div>
</div>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="user_form" id="user_form" 
	onsubmit="return checkform(new Array(this.name1))">
	<input type="hidden" name="exec" value="save"> 
	<input type="hidden" name="id" value="<?=$privatecontact->getId()?>">
	<div class="box1">
    <b>Kontaktdaten</b>
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
							<?php $titles = array("Herr", "Frau", "Dr.", "Prof.");
							foreach ($titles as $title)
							{
							  echo '<option value="'.$title.'"';
							  if($privatecontact->getTitle() == $title) echo ' selected ="selected"';
							  echo '>'.$title.'</option>';
							}
							?>
					</select>
					</td>
				</tr>
				
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Nachname');?> *</td>
					<td class="content_row_clear"><input name="name1" style="width: 300px"
						class="text" value="<?=$privatecontact->getName1()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Vorname');?></td>
					<td class="content_row_clear"><input name="name2"
						style="width: 300px" class="text" value="<?=$privatecontact->getName2()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Adressfeld 1');?>
					</td>
					<td class="content_row_clear"><input name="address1"
						style="width: 300px" class="text" value="<?=$privatecontact->getAddress1()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Adressfeld 2');?>
					</td>
					<td class="content_row_clear"><input name="address2"
						style="width: 300px" class="text" value="<?=$privatecontact->getAddress2()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Postleitzahl');?>
					</td>
					<td class="content_row_clear"><input name="zip"
						style="width: 300px" class="text" value="<?=$privatecontact->getZip()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Stadt');?>
					</td>
					<td class="content_row_clear"><input name="city"
						style="width: 300px" class="text" value="<?=$privatecontact->getCity()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Land')?></td>
					<td class="content_row_clear"><select name="country" style="width: 300px"
						class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
							<? foreach($countries as $c){ ?>
							<option value="<?=$c->getId()?>"
							<?if($privatecontact->getCountry()->getId() == $c->getId()) echo "selected";?>>
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
						style="width: 300px" class="text" value="<?=$privatecontact->getPhone()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Fax');?>
					</td>
					<td class="content_row_clear"><input name="fax"
						style="width: 300px" class="text" value="<?=$privatecontact->getFax()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Mobil');?>
					</td>
					<td class="content_row_clear"><input name="mobil"
						style="width: 300px" class="text" value="<?=$privatecontact->getMobil()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Email');?>
					</td>
					<td class="content_row_clear"><input name="email"
						style="width: 300px" class="text" value="<?=$privatecontact->getEmail()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Web');?>
					</td>
					<td class="content_row_clear" ><input name="web"
						style="width: 300px" class="text" value="<?=$privatecontact->getWeb()?>"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Geburtstag');?>
					</td>
					<td class="content_row_clear" ><input name="birthdate" id="birthdate"
						style="width:70px;" class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)"
						<? if ($privatecontact->getBirthDate() != 0 ) echo 'value="'.date("d.m.Y", $privatecontact->getBirthDate()).'"';?> 
							title="<?=$_LANG->get('Geburtstag');?>">
					</td>
				</tr>
				<tr>
					<td class="content_row_header">&nbsp;</td>
					<td class="content_row_clear">&nbsp;</td>
				</tr>
                <tr>
                   <td class="content_row_header"><?=$_LANG->get('Kunde');?>:</td>
                   <td class="content_row_clear">
                      <input name="customer_search" id="customer_search" type="text" 
                      <?php if ($privatecontact->getBusinessContactId()>0) echo ' value="'.$privatecontact->getBusinessContact()->getNameAsLine().'" ';?> 
                      style="width:350px;" class="text"/>
                      <input name="customer" id="customer" 
                      <?php if ($privatecontact->getBusinessContactId()>0) echo ' value="'.$privatecontact->getBusinessContact()->getId().'" ';?> 
                      type="hidden"/>
                   </td>
                </tr>
				<tr>
					<td class="content_row_header" valign="top"><?=$_LANG->get('Kommentar');?></td>
					<td class="content_row_clear" id="notify_mail_adr">
					       <textarea name="comment" style="width: 250px;height: 150px;" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   <?=$privatecontact->getComment()?></textarea></br>
					</td>
				</tr>
			</table>
		</td>
		</tr>
	</table>
	</div>
	<br/>
    <div class="box1">
        <b>Freigabe</b>
    	<table width="100%" cellpadding="0" cellspacing="0" border="0">
    	    <?php 
    	    $all_users = User::getAllUser();
    	    $qid_arr = Array();
    	    foreach ($privatecontact->getAccess() as $qid)
    	    {
    	        $qid_arr[] = $qid->getId();
    	    }
    	    $qi = 0;
    	    foreach ($all_users as $qusr){
    	       if ($qi==0) echo '<tr>';
    	       ?>
    		   <td class="content_row_header" valign="top" width="20%">
    		   <input type="checkbox" name="access[]" <?php if(in_array($qusr->getId(), $qid_arr)) echo ' checked ';?> value="<?php echo $qusr->getId();?>"/> 
    		   <?php echo $qusr->getNameAsLine();?></td>
    		   <?php if ($qi==4) { echo '</tr>'; $qi = -1; }?>
    		<?php $qi++;}?>
    	</table>
    </div>
</form>