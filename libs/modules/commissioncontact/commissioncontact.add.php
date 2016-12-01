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

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#user_form').submit();",'glyphicon-floppy-disk');
if ($commissionContact->getId()>0){
	$quickmove->addItem('Löschen', '#', "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&id=".$commissionContact->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" id="user_form" name="user_form"
	  enctype="multipart/form-data"
	  onSubmit="return checkpass(new Array(this.name1));">
	<? // gucken, ob die Passw�rter (Webshop-Login) gleich sind und ob alle notwendigen Felder gef�llt sind?>

	<input type="hidden" name="exec" value="edit">
	<input type="hidden" name="subexec" value="save">
	<input type="hidden" name="subform" value="user_details">
	<input type="hidden" name="id" value="<?= $commissionContact->getId() ?>">

	<div class="demo">
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1"><? echo $_LANG->get('Stammdaten'); ?></a></li>
				<li><a href="#tabs-4"><? echo $_LANG->get('Ansprechpartner'); ?></a></li>
				<!--<li><a href="#tabs-2"><? /* echo $_LANG->get('Adressen');*/ ?></a></li>-->
				<? /*if ($_CONFIG->shopActivation){*/ ?><!--
				<li><a href="#tabs-4"><? /* echo $_LANG->get('Kundenportal');*/ ?></a></li>
			<? /*}*/ ?>
			<li><a href="#tabs-6"><? /* echo $_LANG->get('Notizen/Dokumente');*/ ?></a></li>-->
			</ul>

			<div id="tabs-1"><p>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">
							<? if ($commissionContact->getId()) echo $_LANG->get('Provisionskontakt &auml;ndern'); else echo $_LANG->get('Provisionskontakt hinzuf&uuml;gen'); ?>
							<span class="pull-right">
						<?= $savemsg ?>
					</span>
						</h3>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Firma</label>
									<div class="col-sm-9">
										<input name="name1" class="form-control"
											   value="<?= $commissionContact->getName1() ?>" onfocus="markfield(this,0)"
											   onblur="markfield(this,1)">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Firmenzusatz</label>
									<div class="col-sm-9">
										<input name="name2" class="form-control"
											   value="<?= $commissionContact->getName2() ?>" onfocus="markfield(this,0)"
											   onblur="markfield(this,1)">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Stra&szlig;e</label>
									<div class="col-sm-9">
										<input name="address1" class="form-control"
											   value="<?= $commissionContact->getAddress1() ?>"
											   onfocus="markfield(this,0)" onblur="markfield(this,1)">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Adresszusatz</label>
									<div class="col-sm-9">
										<input name="address2" class="form-control"
											   value="<?= $commissionContact->getAddress2() ?>"
											   onfocus="markfield(this,0)" onblur="markfield(this,1)">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Postleitzahl</label>
									<div class="col-sm-9">
										<input name="zip" class="form-control"
											   value="<?= $commissionContact->getZip() ?>" onfocus="markfield(this,0)"
											   onblur="markfield(this,1)">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Stadt</label>
									<div class="col-sm-9">
										<input name="city" class="form-control"
											   value="<?= $commissionContact->getCity() ?>" onfocus="markfield(this,0)"
											   onblur="markfield(this,1)">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Land</label>
									<div class="col-sm-9">
										<select name="country" class="form-control" onfocus="markfield(this,0)"
												onblur="markfield(this,1)">
											<?
											foreach ($countries as $c) {
												?>
												<option value="<?= $c->getId() ?>"
													<? if ($commissionContact->getCountry()->getId() == $c->getId()) echo "selected"; ?>>
													<?= $c->getName() ?>
												</option>
											<?
											}

											?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Telefon</label>
									<div class="col-sm-9">
										<input name="phone" class="form-control"
											   value="<?= $commissionContact->getPhone() ?>" onfocus="markfield(this,0)"
											   onblur="markfield(this,1)">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Fax</label>
									<div class="col-sm-9">
										<input name="fax" class="form-control"
											   value="<?= $commissionContact->getFax() ?>" onfocus="markfield(this,0)"
											   onblur="markfield(this,1)">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">E-Mai</label>
									<div class="col-sm-9">
										<input name="email" class="form-control"
											   value="<?= $commissionContact->getEmail() ?>" onfocus="markfield(this,0)"
											   onblur="markfield(this,1)">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Internetseite</label>
									<div class="col-sm-9">
										<input name="web" class="form-control"
											   value="<?= $commissionContact->getWeb() ?>" onfocus="markfield(this,0)"
											   onblur="markfield(this,1)">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Kunde</label>
									<div class="col-sm-9">
										<select name="customer" class="form-control" onfocus="markfield(this,0)"
												onblur="markfield(this,1)">
											<option
												value="0" <? if (!($commissionContact->isExistingCustomer() && $commissionContact->isPotentialCustomer())) echo "selected"; ?>>

											</option>
											<option
												value="1" <? if ($commissionContact->isExistingCustomer()) echo "selected"; ?>>
												<?= $_LANG->get('Bestandskunde') ?>
											</option>
											<option
												value="2" <? if ($commissionContact->isPotentialCustomer()) echo "selected"; ?>>
												<?= $_LANG->get('Sollkunde') ?>
											</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Lieferant</label>
									<div class="col-sm-9">
							   <span class="pull-left">
								   <input name="supplier" style="margin: 0" class="form-control" type="checkbox"
										  value="1"<? if ($commissionContact->isSupplier()) echo "checked"; ?>
										  onfocus="markfield(this,0)" onblur="markfield(this,1)">
							   </span>
									</div>

								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Mandant</label>
									<div class="col-sm-9">
										<select name="client" class="form-control" onfocus="markfield(this,0)"
												onblur="markfield(this,1)">
											<option value="<?= $_USER->getClient()->getId() ?>" selected>
												<? if (!$_USER->getClient()->isActive()) echo '<span color="red">'; ?>
												<?= $_USER->getClient()->getName() ?>
												<? if (!$_USER->getClient()->isActive()) echo '</span>'; ?>
											</option>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Sprache</label>
									<div class="col-sm-9">
										<select name="language" class="form-control" onfocus="markfield(this,0)"
												onblur="markfield(this,1)">
											<?
											foreach ($languages as $l) {
												?>
												<option value="<?= $l->getId() ?>"
													<? if ($commissionContact->getLanguage()->getId() == $l->getId()) echo "selected"; ?>>
													<?= $l->getName() ?>
												</option>
											<?
											}

											?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Kommentar</label>
									<div class="col-sm-9">
						 		 <textarea rows="7" name="comment" class="form-control">
									  <?= $commissionContact->getComment() ?>
							 	 </textarea>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Kreditor-Nr.</label>
									<div class="col-sm-9">
										<input class="form-control" name="kreditor"
											   value="<?= $commissionContact->getKreditor() ?>">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Debitor-Nr.</label>
									<div class="col-sm-9">
										<input class="form-control" name="debitor"
											   value="<?= $commissionContact->getDebitor() ?>">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">USt.-ID</label>
									<div class="col-sm-9">
										<input class="form-control" name="ust"
											   value="<?= $commissionContact->getUst() ?>">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Steuernummer</label>
									<div class="col-sm-9">
										<input class="form-control" name="taxnumber"
											   value="<?= $commissionContact->getTaxnumber() ?>">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Zahlungsart</label>
									<div class="col-sm-9">
										<select name="payment" class="form-control">
											<option
												value="0" <? if ($commissionContact->getPaymentTerms()->getId() == 0)
												echo "selected" ?> >
											</option>
											<?
											foreach (PaymentTerms::getAllPaymentConditions(PaymentTerms::ORDER_NAME) as $pt) {
												echo '<option value="' . $pt->getId() . '"';
												if ($pt->getId() == $commissionContact->getPaymentTerms()->getId()) {
													echo "selected";
												}
												echo '>' . $pt->getName() . '</option>';
											}
											?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">IBAN</label>
									<div class="col-sm-9">
										<input class="form-control" name="iban"
											   value="<?= $commissionContact->getIban() ?>">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">BIC</label>
									<div class="col-sm-9">
										<input class="form-control" name="bic"
											   value="<?= $commissionContact->getBic() ?>">
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Rabatt</label>
									<div class="col-sm-9">
										<div class="input-group">
											<input class="form-control" name="discount"
												   value="<?= printPrice($commissionContact->getDiscount()) ?>">
											<span class="input-group-addon">%</span>
										</div>
									</div>
								</div>
								<div class="form-group">
									<label for="" class="col-sm-3 control-label">Provision (%)</label>
									<div class="col-sm-9">
										<div class="input-group">
											<input class="form-control" name="provision"
												   value="<?= $commissionContact->getProvision() ?>">
											<span class="input-group-addon">%</span>
										</div>
									</div>
								</div>
								<? if ($commissionContact->getLectorId() > 0) { ?>
									<div class="form-group">
										<label for="" class="col-sm-3 control-label"><span
												class="error"><?= $_LANG->get('Lector-Import') ?>: </span></label>
										<div class="col-sm-9">
											ID: <?= $commissionContact->getId() ?>
										</div>
									</div>
								<? } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
				<!-- tr>
			<td class="content_row_header"><?= $_LANG->get('Branche') ?></td>
			<td class="content_row_clear">
			    <input class="text" style="width:300px" name="branche" 
			    		value="<?= $commissionContact->getBranche() ?>">
			</td>
		</tr-->
				<!-- tr>
			<td class="content_row_header"><?= $_LANG->get('KD-Nr. beim Lieferanten') ?></td>
			<td class="content_row_clear">
			    <input class="text" style="width:100px" name="kdnr_at_cust" 
			    		value="<?= $commissionContact->getNum_at_customer() ?>">
			</td>
		</tr-->
				<!-- Provision -->
				<!--<tr>
                <td class="content_row_header"><? /*=$_LANG->get('Provisionspartner')*/ ?></td>
                <td class="content_row_clear">
                    <input name="commissionpartner" type="checkbox" value="1"
                        <? /* if ($commissionContact->isCommissionpartner()) echo "checked";*/ ?>
                        onfocus="markfield(this,0)" onblur="markfield(this,1)">
                </td>
            </tr>-->

				<? /**************************************** Adressen **************************************************/ ?>
				<!--<div id="tabs-2"><p>
	
	<? /*if($commissionContact->getId()){*/ ?>
		<table width="100%">
			<colgroup>
				<col>
				<col>
				<col>
				<col>
				<col>
			</colgroup>
			
			<tr>
				<td class="content_row_header"> <?php /*echo $_LANG->get('Rechnungsadresse');*/ ?></td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear" align="right"><a href="index.php?exec=edit_ai&id=<? /*=$commissionContact->getID()*/ ?>"><img src="images/icons/user--plus.png"> <? /*=$_LANG->get('Addresse hinzuf&uuml;gen')*/ ?></a></td>
			</tr>
			<?php /*$addressInvoice = Address::getAllAddresses($commissionContact,Address::ORDER_NAME,Address::FILTER_INVC);
			foreach($addressInvoice as $ai)
			{
			*/ ?>
			<tr>
				<td><? /* echo $ai->getName1() . ' ' . $ai->getName2();*/ ?></td>
				<td><? /* echo $ai->getAddress1();*/ ?></td>
				<td><? /* echo $ai->getAddress2();*/ ?></td>
				<td><? /* echo $ai->getCity();*/ ?></td>
				<td class="content_row_clear" align="right">
	            	<a href="index.php?exec=edit_ai&id_a=<? /*=$ai->getId()*/ ?>&id=<? /*=$commissionContact->getID()*/ ?>"><img src="images/icons/pencil.png"></a>
	            	<a href="index.php?exec=delete_a&id_a=<? /*=$ai->getId()*/ ?>&id=<? /*=$commissionContact->getID()*/ ?>" onclick="askDel('index.php?exec=delete_a&id_a=<? /*=$ai->getId()*/ ?>&id=<? /*=$commissionContact->getID()*/ ?>')"><img src="images/icons/cross-script.png"></a>
	        	</td>
	        </tr>
	        <?php /*
				
			}
			*/ ?>
		</table>
	<? /*}*/ ?>

	<? /*if($commissionContact->getId()){*/ ?>
		<table width="100%">
			<colgroup>
				<col>
				<col>
				<col>
				<col>
				<col>
			</colgroup>
			
			<tr>
				<td class="content_row_header"> <?php /*echo $_LANG->get('Lieferadresse');*/ ?></td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear">&nbsp;</td>
				<td class="content_row_clear" align="right"><a href="index.php?exec=edit_ad&id=<? /*=$commissionContact->getID()*/ ?>"><img src="images/icons/user--plus.png"> <? /*=$_LANG->get('Addresse hinzuf&uuml;gen')*/ ?></a></td>
			</tr>
			<?php /*
			$addressDelivery = Address::getAllAddresses($commissionContact,Address::ORDER_NAME,Address::FILTER_DELIV);
			foreach($addressDelivery as $ad)
			{
			*/ ?>
			<tr>
				<td><? /* echo $ad->getName1() . ' ' . $ad->getName2();*/ ?></td>
				<td><? /* echo $ad->getAddress1();*/ ?></td>
				<td><? /* echo $ad->getAddress2();*/ ?></td>
				<td><? /* echo $ad->getCity();*/ ?></td>
				<td class="content_row_clear" align="right">
	            	<a href="index.php?exec=edit_ad&id_a=<? /*=$ad->getId()*/ ?>&id=<? /*=$commissionContact->getID()*/ ?>"><img src="images/icons/pencil.png"></a>
	            	<a href="index.php?exec=delete_a&id_a=<? /*=$ad->getId()*/ ?>&id=<? /*=$commissionContact->getID()*/ ?>" onclick="askDel('index.php?exec=delete_a&id_a=<? /*=$ad->getId()*/ ?>&id=<? /*=$commissionContact->getID()*/ ?>')"><img src="images/icons/cross-script.png"></a>
	        	</td>
	        </tr>
	        <?php /*
			}
			*/ ?>
		</table>
	<? /*}*/ ?>


	</p></div>-->

				<? /****************************************** Ansprechpartner **************************************/ ?>

						<div id="tabs-4"><p>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h3 class="panel-title">
										Ansprechpartner
													<span class="pull-right">
														<a href="index.php?page=<?= $_REQUEST['page'] ?>&exec=edit_cp&id=<?= $commissionContact->getID() ?>">
															<button class="btn btn-xs btn-success" type="button">
																<span class="glyphicons glyphicons-user"
																	  style="color: white;"></span>
																<?= $_LANG->get('Ansprechpartner hinzuf&uuml;gen') ?>
															</button>
														</a>
													</span>
									</h3>
								</div>
								<div class="panel-body">
									<? if ($commissionContact->getId()) { ?>
										<?php $contactPerson = ContactPerson::getAllContactPersons($commissionContact, ContactPerson::ORDER_NAME);
										foreach ($contactPerson as $cp) { ?>

											<div class="form-text">
												<div class="col-sm-12">
													<?php echo $cp->getNameAsLine(); ?>
												</div>
											</div>
											<div class="form-text">
												<div class="col-sm-12">
													<?php echo $cp->getCity(); ?>
												</div>
											</div>
											<div class="form-text">
												<div class="col-sm-12">
													<?php echo $cp->getPhone(); ?>
												</div>
											</div>
											<div class="form-text">
												<div class="col-sm-3">
													<a href="index.php?page=<?= $_REQUEST['page'] ?>&exec=edit_cp&cpid=<?= $cp->getId() ?>&id=<?= $commissionContact->getID() ?>">
														<button class="btn btn-xs btn-success" type="button">
															<span class="glyphicons glyphicons-pencil" style="color: white;"></span>
															<?= $_LANG->get('Ansprechpartner ändern') ?>
														</button>
													</a>
												</div>
												<div class="col-sm-3">
													<a href="index.php?page=<?= $_REQUEST['page'] ?>&exec=delete_cp&cpid=<?= $cp->getId() ?>&id=<?= $commissionContact->getID() ?>"
													   onclick="askDel('index.php?page=<?= $_REQUEST['page'] ?>&exec=delete_cp&cpid=<?= $cp->getId() ?>&id=<?= $commissionContact->getID() ?>')">
														<button class="btn btn-xs btn-success" type="button">
															<span class="glyphicons glyphicons-remove" style="color: red;"></span>
															<?= $_LANG->get('Ansprechpartner löschen') ?>
														</button>
													</a>
												</div>
											</div>
										<? } ?>
									<? } ?>
								</div>
							</div>
						</div>

			<? /********************************** Kundenportal ******************************************/ ?>

				<? /*if ($_CONFIG->shopActivation){*/ ?><!--
		<div id="tabs-4"><p>
		
		<table width="100%">
			<tr>
				<td width="200" class="content_header">
					<img src="<? /*=$_MENU->getIcon($_SESSION["pid"])*/ ?>">
					<? /*=$_LANG->get('Shop-Login &auml;ndern');*/ ?>
				</td>
				<td></td>
				<td width="200" class="content_header" align="right"><? /*=$savemsg*/ ?></td>
			</tr>
		</table>
		
		<? /*if($commissionContact->getId()){*/ ?>
			<table width="100%">
				<colgroup>
					<col width="180">
					<col>
				</colgroup>
				<tr>
					<td class="content_row_header"><? /*=$_LANG->get('Benutzername')*/ ?> *</td>
					<td class="content_row_clear">
						<input 	name="shop_login" style="width: 300px"
								class="text" value="<? /*=$commissionContact->getShoplogin()*/ ?>"
								onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><? /*=$_LANG->get('Passwort')*/ ?></td>
					<td class="content_row_clear">
						<input 	name="shop_pass1" id="shop_pass1" style="width: 300px" 
								class="text" value="<? /*=$commissionContact->getShoppass()*/ ?>"
								type="text"	onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr>
				<!-- tr>
					<td class="content_row_header"><? /*=$_LANG->get('Passwort wiederholen')*/ ?></td>
					<td class="content_row_clear">
						<input 	name="shop_pass2" id="shop_pass2" style="width: 300px" class="text" value="" 
								type="password"	onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr-->
				<!--<tr>
					<td class="content_row_header"><? /*=$_LANG->get('G&uuml;ltigkeit')*/ ?></td>
					<td class="content_row_clear">
						<input type="text" style="width:80px" id="login_expire" name="login_expire"
								class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
								onfocus="markfield(this,0)" onblur="markfield(this,1)"
								value="<? /*if($commissionContact->getLoginexpire() != 0){ echo date('d.m.Y', $commissionContact->getLoginexpire());}*/ ?>">
					</td>
				</tr>-->
				<!-- tr>
					<td class="content_row_header"><? /*=$_LANG->get('Ticket-Freigabe');*/ ?></td>
					<td class="content_row_clear">
						<input name="ticket_enabled" type="checkbox" value="1" <? /* if ($commissionContact->getTicketenabled()) echo "checked";*/ ?>
							   onfocus="markfield(this,0)" onblur="markfield(this,1)">
					</td>
				</tr-->
							</table>
				<? /*}*/ ?>

			<? /*}*/ ?>
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
	</div>
</form>



<!--//-->