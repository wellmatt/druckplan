<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       22.05.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
chdir("../../../");
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once("libs/basic/countries/country.class.php");
require_once 'libs/modules/organizer/contact.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';

// error_reporting(-1);
// ini_set('display_errors', 1);

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


if ($_USER == false)
	die("Login failed");

$_REQUEST["id"] = (int)$_REQUEST["id"];
$newContact = new BusinessContact($_REQUEST["id"]);

if($_REQUEST["test_exec"]=="test_vancy"){
	$newContact->setActive(1);
	$newContact->setCustomer((int)$_REQUEST["customer"]);
	$newContact->setName1(trim(addslashes($_REQUEST["name1"])));
	$newContact->setName2(trim(addslashes($_REQUEST["name2"])));
	$newContact->setAddress1(trim(addslashes($_REQUEST["address1"])));
	$newContact->setAddress2(trim(addslashes($_REQUEST["address2"])));
	$newContact->setZip(trim(addslashes($_REQUEST["zip"])));
	$newContact->setCity(trim(addslashes($_REQUEST["city"])));
	$newContact->setCountry(new Country (trim(addslashes($_REQUEST["country"]))));
	$newContact->setEmail(trim(addslashes($_REQUEST["email"])));
	$newContact->setPhone(trim(addslashes($_REQUEST["phone"])));
	$newContact->setFax(trim(addslashes($_REQUEST["fax"])));
	$newContact->setWeb(trim(addslashes($_REQUEST["web"])));
	$newContact->setClient(new Client((int)$_REQUEST["client"]));
	$newContact->setLanguage(new Translator((int)$_REQUEST["language"]));
	$newContact->setPaymentTerms(new PaymentTerms((int)$_REQUEST["payment"]));
	$newContact->setDiscount((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["discount"]))));
	$newContact->setSupplier((int)$_REQUEST["supplier"]);
	$newContact->setComment(trim(addslashes($_REQUEST["comment"])));
	$newContact->setLectorId(0);
	
	$newContact->setType((int)$_REQUEST["cust_type"]);
	$newContact->setBedarf((int)$_REQUEST["cust_bedarf"]);
	$newContact->setProdukte((int)$_REQUEST["cust_produkte"]);
	$newContact->setBranche((int)$_REQUEST["cust_branche"]);
	
// 	echo $newContact->getId() . "</br>";
	
	$savemsg = getSaveMessage($newContact->save());
	$savemsg .= $DB->getLastError();
	
	$inputdata = '<option value="'.$newContact->getId().'"> '.$newContact->getNameAsLine().','.$newContact->getCity().'</option>';
	//echo $inputdata;
	
	echo '<script language="javascript">';
	$js = 'parent.document.getElementById("order_customer").innerHTML = \''.$inputdata.'\'';
	echo $js;
	echo '</script>';
	
}

$_USER;
$languages = Translator::getAllLangs(Translator::ORDER_NAME);
$countries = Country::getAllCountries();
if($newContact->getId()){
	$contactPersons = ContactPerson::getAllContactPersons($newContact,ContactPerson::ORDER_NAME);
	$deliveryAddresses = Address::getAllAddresses($newContact,Address::ORDER_NAME,Address::FILTER_DELIV);
	$invoiceAddresses = Address::getAllAddresses($newContact,Address::ORDER_NAME,Address::FILTER_INVC);
}
?>

<script type="text/javascript">
function checkform(obj) {
	var xc = 0;
	for (x = 0; x < obj.length; x++) {
		if (obj[x].value == '') {
			xc++;
			obj[x].style.backgroundColor = '#EEEEEE';
			obj[x].style.borderColor = '#FF0000';
			if (xc == 1)
				obj[x].focus();
		} else {
			obj[x].style.backgroundColor = '';
			obj[x].style.borderColor = '';
		}
	}
	if (xc > 0) {
		alert("Die Formulardaten sind unvollstaendig.");
		return false;
	}
	return true;
}
</script>

<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<script language="javascript" src="jscripts/basic.js"></script>

<!-- jQuery -->
<link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<!-- /jQuery -->

</head>

<body>
<img src="../../../images/icons/user-black.png" alt="" /> Gesch&auml;ftskontakt hinzuf&uuml;gen 
&emsp; &emsp; &emsp;<? echo $savemsg.'<br/>';?>
<table><tr><td width="500">
<form action="businesscontact.add.fancy.php" method="post" name="newuser_form_fancy" id="newuser_form_fancy"
	  onsubmit="return checkform(new Array(this.name1))" >
	<input type="hidden" name="test_exec" value="test_vancy" >
	<table>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Firma');?> *</td>
			<td class="content_row_clear">
				<input name="name1" id="name1" style="width: 300px" class="text" value="<?=$newContact->getName1()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Firmenzusatz');?></td>
			<td class="content_row_clear"><input name="name2"
				style="width: 300px" class="text" value="<?=$newContact->getName2()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Adressfeld 1');?>
			</td>
			<td class="content_row_clear"><input name="address1"
				style="width: 300px" class="text" value="<?=$newContact->getAddress1()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Adressfeld 2');?>
			</td>
			<td class="content_row_clear"><input name="address2"
				style="width: 300px" class="text" value="<?=$newContact->getAddress2()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Postleitzahl');?>
			</td>
			<td class="content_row_clear"><input name="zip"
				style="width: 300px" class="text" value="<?=$newContact->getZip()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Stadt');?>
			</td>
			<td class="content_row_clear"><input name="city"
				style="width: 300px" class="text" value="<?=$newContact->getCity()?>"
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
					<?if ($newContact->getCountry()->getId() == $c->getId()) echo "selected";?>>
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
				style="width: 300px" class="text" value="<?=$newContact->getPhone()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Fax');?>
			</td>
			<td class="content_row_clear"><input name="fax"
				style="width: 300px" class="text" value="<?=$newContact->getFax()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('E-Mail');?>
			</td>
			<td class="content_row_clear"><input name="email"
				style="width: 300px" class="text" value="<?=$newContact->getEmail()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Internetseite');?>
			</td>
			<td class="content_row_clear"><input name="web"
				style="width: 300px" class="text" value="<?=$newContact->getWeb()?>"
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Kunde');?></td>
			<td class="content_row_clear"><select name="customer" style="width: 300px"
				class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
					<option value="1" selected>
						<?=$_LANG->get('Bestandskunde')?>
					</option>
			</select>
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Lieferant');?></td>
			<td class="content_row_clear">
				<input name="supplier" type="checkbox" value="1"
					<? if ($newContact->isSupplier()) echo "checked";?>
					onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Mandant')?></td>
			<td class="content_row_clear">
				<select name="client"style="width: 300px" class="text" 
						onfocus="markfield(this,0)"	onblur="markfield(this,1)">
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
			<td class="content_row_clear">
				<select name="language" style="width: 300px"
						class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
					<?
					foreach($languages as $l)
					{?>
					<option value="<?=$l->getId()?>"
					<?if ($newContact->getLanguage()->getId() == $l->getId()) echo "selected";?>>
						<?=$l->getName()?>
					</option>
					<?}

					?>
			</select>
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Zahlungsart')?></td>
			<td class="content_row_clear">
			    <select name="payment" style="width:300px" class="text">
			        <? 
			        foreach(PaymentTerms::getAllPaymentConditions(PaymentTerms::ORDER_NAME) as $pt)
			        {
			            echo '<option value="'.$pt->getId().'">'.$pt->getName1().'</option>';
			        }
			        ?>
			    </select>
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Rabatt')?></td>
			<td class="content_row_clear">
			    <input class="text" style="width:60px" name="discount" 
			    value="<?=printPrice($newContact->getDiscount())?>"> %
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Kommentar')?></td>
			<td class="content_row_clear" colspan="2">
				<textarea name="comment"style="width: 300px; height: 130px"><?=$newContact->getComment()?></textarea>
			</td>
		</tr>
		
		<tr>
			<td class="content_row_header">&nbsp;</td>
			<td class="content_row_clear" align="right">
				<input type="submit" value="Speichern">
			</td>
		</tr>
	</table> 
</form>
</td></tr></table>

</body>
</html>
