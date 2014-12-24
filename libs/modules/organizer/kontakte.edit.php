<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       08.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$contact = new UserContact((int)$_REQUEST["id"]);
if($contact->getCountry() == NULL)
    $contact->setCountry($_USER->getLang());

if($_REQUEST["subexec"] == "save")
{
    $contact->setName1(trim(addslashes($_REQUEST["contact_name1"])));
    $contact->setName2(trim(addslashes($_REQUEST["contact_name2"])));
    $contact->setAddress1(trim(addslashes($_REQUEST["contact_address1"])));
    $contact->setAddress2(trim(addslashes($_REQUEST["contact_address2"])));
    $contact->setPostcode(trim(addslashes($_REQUEST["contact_plz"])));
    $contact->setCity(trim(addslashes($_REQUEST["contact_city"])));
    $contact->setPhone(trim(addslashes($_REQUEST["contact_phone"])));
    $contact->setCellphone(trim(addslashes($_REQUEST["contact_cellphone"])));
    $contact->setFax(trim(addslashes($_REQUEST["contact_fax"])));
    $contact->setEmail(trim(addslashes($_REQUEST["contact_email"])));
    $contact->setWebsite(trim(addslashes($_REQUEST["contact_website"])));
    $contact->setNotes(trim(addslashes($_REQUEST["contact_notes"])));
    $contact->setCountry(new Country((int)$_REQUEST["contact_country"]));
    $savemsg = getSaveMessage($contact->save());
    echo $DB->getLastError();

}

?>

<link rel="stylesheet" type="text/css" href="./css/contact.css" />
<table width="100%">
	<tr>
		<td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
		<? if($_REQUEST["id"] == "") echo $_LANG->get('Kontakt hinzuf&uuml;gen'); else echo $_LANG->get('Kontakt bearbeiten')?>
		</td>
		<td class="content_header"><?=$savemsg?></td>
    </tr>
</table>

<div class="box1">
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" onsubmit="checkForm(new Array(this.name1))" name="contact_form">
<input name="exec" value="<?=$_REQUEST["exec"]?>" type="hidden">
<input name="subexec" value="save" type="hidden">
<input name="id" value="<?=$_REQUEST["id"]?>" type="hidden">
<table width="100%">
    <colgroup>
        <col width="180">
        <col>
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Name 1')?> *</td>
        <td class="content_row_clear"><input name="contact_name1" value="<?=$contact->getName1()?>" class="text" style="width:300px"></td>
    </tr>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Name 2')?></td>
        <td class="content_row_clear"><input name="contact_name2" value="<?=$contact->getName2()?>" class="text" style="width:300px"></td>
    </tr>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Adresse 1')?></td>
        <td class="content_row_clear"><input name="contact_address1" value="<?=$contact->getAddress1()?>" class="text" style="width:300px"></td>
    </tr>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Adresse 2')?></td>
        <td class="content_row_clear"><input name="contact_address2" value="<?=$contact->getAddress2()?>" class="text" style="width:300px"></td>
    </tr>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('PLZ/Ort')?></td>
        <td class="content_row_clear">
            <input name="contact_plz" value="<?=$contact->getPostcode()?>" class="text" style="width:60px">
            <input name="contact_city" value="<?=$contact->getCity()?>" class="text" style="width:235px">
        </td>
    </tr>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Land')?></td>
        <td class="content_row_header">
            <select name="contact_country" style="width:300px;" class="text">        
            <? 
            $lands = Country::getAllCountries();

            foreach($lands as $l)
            {
            ?>
                <option value="<?=$l->getId()?>" <?if($contact->getCountry()->getId() == $l->getId()) echo "selected";?>><?=$l->getName()?></option>
            <? } ?>
            </select>
        </td>
    </tr>
    
    <tr>
        <td class="content_row_header">&nbsp;</td>
        <td class="content_row_clear">&nbsp;</td>
    </tr>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Telefon')?></td>
        <td class="content_row_clear"><input name="contact_phone" value="<?=$contact->getPhone()?>" class="text" style="width:300px"></td>
    </tr>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Mobiltelefon')?></td>
        <td class="content_row_clear"><input name="contact_cellphone" value="<?=$contact->getCellphone()?>" class="text" style="width:300px"></td>
    </tr>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Fax')?></td>
        <td class="content_row_clear"><input name="contact_fax" value="<?=$contact->getFax()?>" class="text" style="width:300px"></td>
    </tr>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('E-Mail')?></td>
        <td class="content_row_clear"><input name="contact_email" value="<?=$contact->getEmail()?>" class="text" style="width:300px"></td>
    </tr>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Webseite')?></td>
        <td class="content_row_clear"><input name="contact_website" value="<?=$contact->getWebsite()?>" class="text" style="width:300px"></td>
    </tr>
    <tr>
        <td class="content_row_header">&nbsp;</td>
        <td class="content_row_clear">&nbsp;</td>
    </tr>    
    <tr>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Bemerkungen')?></td>
        <td class="content_row_clear"><textarea name="contact_notes" class="text" style="width:300px;height:150px"><?=$contact->getNotes()?></textarea></td>
    </tr>
</table>
</div>
<input type="submit" value="<?=$_LANG->get('Speichern')?>">
<input type="button" value="<?=$_LANG->get('L&ouml;schen')?>" class="buttonRed" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$contact->getId()?>')">
</form>