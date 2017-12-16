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
	$newContact->setStreet(trim(addslashes($_REQUEST["street"])));
	$newContact->setHouseno(trim(addslashes($_REQUEST["houseno"])));
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

	echo '<script language="JavaScript">parent.$.fancybox.close();</script>'; // parent.location.href=parent.location.href;
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
<!-- Glyphicons -->
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-bootstrap.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-halflings.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-filetypes.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-social.css" />
<!-- /Glyphicons -->

<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<script language="javascript" src="jscripts/basic.js"></script>



<!-- MegaNavbar -->
<link href="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="../../../thirdparty/MegaNavbar/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../../../thirdparty/MegaNavbar/assets/css/MegaNavbar.css"/>
<link rel="stylesheet" type="text/css" href="../../../thirdparty/MegaNavbar/assets/css/skins/navbar-default.css" title="inverse">
<script src="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<!-- /MegaNavbar -->

<!-- jQuery -->
<link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<!-- /jQuery -->

</head>

<body>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				Gesch&auml;ftskontakt hinzuf&uuml;gen
				<span class="pull-right">
					<?=$savemsg?>
				</span>
			</h3>
	  </div>
	  <div class="panel-body">
		  <form action="businesscontact.add.fancy.php" method="post" name="newuser_form_fancy" class="form-horizontal" id="newuser_form_fancy" onsubmit="return checkform(new Array(this.name1))" >
			  <input type="hidden" name="test_exec" value="test_vancy" >
			   <div class="row">
				   <div class="col-md-6">
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Firma</label>
						   <div class="col-sm-5">
							   <input name="name1" id="name1"  class="form-control" value="<?=$newContact->getName1()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Firmenzusatz</label>
						   <div class="col-sm-5">
							   <input name="name2" class="form-control" value="<?=$newContact->getName2()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Straße</label>
						   <div class="col-sm-5">
							   <input name="street" class="form-control" value="<?=$newContact->getStreet()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Hausnummer</label>
						   <div class="col-sm-5">
							   <input name="houseno" class="form-control" value="<?=$newContact->getHouseno()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Adressfeld 2</label>
						   <div class="col-sm-5">
							   <input name="address2" class="form-control" value="<?=$newContact->getAddress2()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Postleitzahl</label>
						   <div class="col-sm-5">
							   <input name="zip" class="form-control" value="<?=$newContact->getZip()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Stadt</label>
						   <div class="col-sm-5">
							   <input name="city" class="form-control" value="<?=$newContact->getCity()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Land</label>
						   <div class="col-sm-5">
							   <select name="country" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
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
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Telefon</label>
						   <div class="col-sm-5">
							   <input name="phone" class="form-control" value="<?=$newContact->getPhone()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Fax</label>
						   <div class="col-sm-5">
							   <input name="fax" class="form-control" value="<?=$newContact->getFax()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
				   </div>
				   <div class="col-md-6">
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">E-Mail</label>
						   <div class="col-sm-6">
							   <input name="email" class="form-control" value="<?=$newContact->getEmail()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Internetseite</label>
						   <div class="col-sm-6">
							   <input name="web" class="form-control" value="<?=$newContact->getWeb()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Kunde</label>
						   <div class="col-sm-6">
							   <select name="customer" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
								   <option value="1" selected>
									   <?=$_LANG->get('Bestandskunde')?>
								   </option>
							   </select>
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Lieferant</label>
						   <div class="col-sm-6">
							   <input name="supplier" type="checkbox" value="1"<? if ($newContact->isSupplier()) echo "checked";?> onfocus="markfield(this,0)" onblur="markfield(this,1)">
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Mandant</label>
						   <div class="col-sm-6">
							   <select name="client" class="form-control" onfocus="markfield(this,0)"	onblur="markfield(this,1)">
								   <option value="<?=$_USER->getClient()->getId()?>" selected>
									   <?if(!$_USER->getClient()->isActive()) echo '<span color="red">';?>
									   <?=$_USER->getClient()->getName()?>
									   <?if(!$_USER->getClient()->isActive()) echo '</span>';?>
								   </option>
							   </select>
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Sprache</label>
						   <div class="col-sm-6">
							   <select name="language" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
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
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Zahlungsart</label>
						   <div class="col-sm-6">
							   <select name="payment" class="form-control">
								   <?
								   foreach(PaymentTerms::getAllPaymentConditions(PaymentTerms::ORDER_NAME) as $pt)
								   {
									   echo '<option value="'.$pt->getId().'">'.$pt->getName1().'</option>';
								   }
								   ?>
							   </select>
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Rabatt</label>
						   <div class="col-sm-6">
							   <div class="input-group">
								   <input class="form-control" name="discount" value="<?=printPrice($newContact->getDiscount())?>">
								   <span class="input-group-addon">%</span>
							   </div>
						   </div>
					   </div>
					   <div class="form-group">
						   <label for="" class="col-sm-3 control-label">Kommentar</label>
						   <div class="col-sm-6">
							   <textarea name="comment" class="form-control"><?=$newContact->getComment()?></textarea>
						   </div>
					   </div>
				   </div>
			   </div>
			  <br>
			  <span class="pull-right">
				  <button class="btn btn-origin btn-default" type="submit" value="Speichern">
					  <?= $_LANG->get('Speichern') ?>
				  </button>
			  </span>
		  </form>
	  </div>
</div>
</body>
</html>
