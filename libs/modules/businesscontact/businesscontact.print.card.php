<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
error_reporting(-1);
ini_set('display_errors', 1);
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once ('libs/modules/tickets/ticket.class.php');
require_once ('libs/modules/commissioncontact/commissioncontact.class.php');
require_once 'libs/modules/businesscontact/attribute.class.php';


$_REQUEST["id"] = (int)$_REQUEST["id"];
$businessContact = new BusinessContact($_REQUEST["id"]);

$all_attributes = Attribute::getAllAttributesForCustomer();

$contactPersons = ContactPerson::getAllContactPersons($businessContact,ContactPerson::ORDER_NAME, "", ContactPerson::LOADER_BASIC);
$deliveryAddresses = Address::getAllAddresses($businessContact,Address::ORDER_NAME,Address::FILTER_DELIV);
$invoiceAddresses = Address::getAllAddresses($businessContact,Address::ORDER_NAME,Address::FILTER_INVC);

$all_active_attributes = $businessContact->getActiveAttributeItemsInput();


?>

<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<!-- <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1"> -->
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/ticket.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<link rel="stylesheet" type="text/css" href="../../../css/main.print.css" media="print"/>
<link href="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

<script type="text/javascript" language="JavaScript">
function printPage() {
    focus();
    if (window.print) 
    {
        jetztdrucken = confirm('Seite drucken ?');
        if (jetztdrucken) 
            window.print();
    }
}
</script>

<?
/**************************************************************************
 ******* 				HTML-Bereich								*******
 *************************************************************************/?>
<body OnLoad="printPage()">
<div class="demo">	
		<? // ---------------------------- Uebesicht ueber den Geschaeftskontakt --------------------------?>
		<br>
		<table width="100%">
		<tr>
			<td width="700" class="content_header">
			    <h3><?=$businessContact->getNameAsLine() ?><small><span style="display: inline-block; vertical-align: top;" class="
			    <?php 
			    if($businessContact->isExistingCustomer())
			    {
			        echo "label label-success";
			    } else if($businessContact->isPotentialCustomer())
			    {
			        echo "label label-info";
			    } else if($businessContact->isPartnerCustomer())
				{
					echo "label label-info";
				}
			    ?>">
			    <?php 
			    if($businessContact->isExistingCustomer())
			    {
			        echo $_LANG->get('Bestandskunde');
			    } else if($businessContact->isPotentialCustomer())
			    {
			        echo $_LANG->get('Interessent');
			    } else if($businessContact->isPartnerCustomer())
				{
					echo $_LANG->get('Partner');
				}
			    ?>
			    </span></small></h3>
			</td>
			<td align="left"></td>
		</tr>
		</table>
		
		<table width="100%">
			<colgroup>
			<col width="600">
			<col>
			</colgroup>
			<tr>
			<td valign="top">
				<table width="100%">
						<colgroup>
							<col width="350">
							<col>
						</colgroup>
						<tr>
							<td class="content_row_clear" valign="top">
								<?=$businessContact->getAddress1()?> <?=$businessContact->getAddress2()?> <br>
								<?=$businessContact->getZip()?> <?=$businessContact->getCity()?><br>
								<?=$businessContact->getCountry()->getName()?>
							</td>
							<td class="content_row_clear" valign="top">
								<span  onClick="dialNumber('<?=$_USER->getTelefonIP()?>/command.htm?number=<?=$businessContact->getPhoneForDial()?>')"
									title="<?=$businessContact->getPhoneForDial()." ".$_LANG->get('anrufen');?>" class="pointer icon-link">
									<span class="glyphicons glyphicons-phone-alt"><?=$businessContact->getPhone()?></span>
								</span>
								<br>
								<span class="glyphicons glyphicons-phone-alt"><?=$businessContact->getFax()?></span><br>
								<span class="glyphicons glyphicons-globe-af"></span>
									<a class="icon-link" href="<?=$businessContact->getWebForHref()?>" target="_blank"><?=$businessContact->getWeb()?></a> <br>
								<span class="glyphicons glyphicons-envelope"><?=$businessContact->getEmail()?></span>
							</td>
						</tr>
						<tr>
							<td class="content_row_clear">&ensp;</td>
						</tr>
						<tr>
							<td class="content_row_clear" valign="top">
							<?	if ($businessContact->getCustomernumber() != NULL && $businessContact->getCustomernumber() != ""){
									echo $_LANG->get('Kundennummer').": ".$businessContact->getCustomernumber(). " <br/>";
								} ?>
							<?	if ($businessContact->getNumberatcustomer() != NULL && $businessContact->getNumberatcustomer() != ""){
									echo $_LANG->get('Unsere KD-Nr.').": ".$businessContact->getNumberatcustomer(). " <br/>";
								} ?>
							<?	if ($businessContact->getMatchcode() != NULL && $businessContact->getMatchcode() != ""){
									echo $_LANG->get('Matchcode').": ".$businessContact->getMatchcode(). " <br/>";
								} ?>
							<?	if ($businessContact->getSupervisor()->getId()>0){
									echo $_LANG->get('Betreuer').": ".$businessContact->getSupervisor()->getNameAsLine(). " <br/>";
								} ?>
							<?	if ($businessContact->getTourmarker()){
									echo $_LANG->get('Tourenmerkmal').": ".$businessContact->getTourmarker(). " <br/>";
								} ?>
							</td>
							<td class="content_row_clear" valign="top">
								<?=$_LANG->get('Tickets:');?>: 
								<?if ($businessContact->getTicketenabled() == 1) echo $_LANG->get('AN'); else echo $_LANG->get('AUS'); ?> <br/>
								<?=$_LANG->get('Personalisierung:');?>: 
								<?if ($businessContact->getPersonalizationenabled() == 1) echo $_LANG->get('AN'); else echo $_LANG->get('AUS'); ?> <br/>
								<?=$_LANG->get('Artikel:');?>: 
								<?if ($businessContact->getArticleenabled() == 1) echo $_LANG->get('AN'); else echo $_LANG->get('AUS'); ?> <br/>
								</br>
								<?if ($businessContact->getNotes()!="") echo "Bemerkung:</br>".$businessContact->getNotes();?></br>
							</td>
						</tr>
						<tr>
							<td class="content_row_clear" valign="top">
							<?	foreach ($all_attributes AS $attribute){
									$tmp_output = "<b>".$attribute->getTitle().":</b> ";
									$allitems = $attribute->getItems();
									$j=0;
									foreach ($allitems AS $item){
										if ($all_active_attributes["{$attribute->getId()}_{$item["id"]}"]["value"] == 1){
											$tmp_output .= $item["title"];
											if ($all_active_attributes["{$attribute->getId()}_{$item["id"]}"]["inputvalue"] != ""){
											    $tmp_output .= ": '".$all_active_attributes["{$attribute->getId()}_{$item["id"]}"]["inputvalue"]."'";
											}
											$tmp_output .= ", ";
											$j++;
										}
									}
									$tmp_output = substr($tmp_output, 0, -2); // Letztes Komma entfernen
									if($j>0){ 
										echo $tmp_output.= "<br/>";
									}
								}?>
							</td>
						</tr>
				</table>
			</td>
			<td valign="top">
			&ensp;<b><?=$_LANG->get('Ansprechpartner');?></b>
			<br/><br/>
			<table>
				<colgroup>
					<col width="200">
					<col>
				</colgroup>
			<?	foreach($contactPersons as $cp) { 
					$phone = $cp->getPhoneForDial("n");
					$mobilephone = $cp->getPhoneForDial("m");?>
					<tr <?if($cp->isMainContact())echo 'style="font-weight:bold;"'?>>
						<td class="content_row_clear">
							<?php echo $cp->getNameAsLine(); ?>
						</td>
						<td class="content_row_clear">
							<?	$contact_attributes = Attribute::getAllAttributesForContactperson();
								$active_cust_attributes= $cp->getActiveAttributeItemsInput();
								foreach ($contact_attributes AS $attribute){
									$allitems = $attribute->getItems();
									$j=0; $tmp_output = "";
									foreach ($allitems AS $item){
										if ($active_cust_attributes["{$attribute->getId()}_{$item["id"]}"]["value"] == 1){
											$tmp_output .= $item["title"];
											if ($active_cust_attributes["{$attribute->getId()}_{$item["id"]}"]["inputvalue"] != ""){
											    $tmp_output .= ": '".$active_cust_attributes["{$attribute->getId()}_{$item["id"]}"]["inputvalue"]."'";
											}
											$tmp_output .= ", ";
											$j++;
										}
									}
									$tmp_output = substr($tmp_output, 0, -2); // Letztes Komma entfernen
									if($j>0 && $attribute->getId() == 21){ 		// An dieser STelle nur Attribut "Funktion" ausgeben 
										echo $tmp_output;
									}
								}?>
						</td>
			        </tr>
					<tr>
						<td>
							<?if ($phone != false){?>
								<span class="glyphicons glyphicons-phone-alt"></span><?php echo $phone;?>
							<?}?>
						</td>
						<td>
							<span class="glyphicons glyphicons-envelope"></span><?php echo $cp->getEmail();?>
						</td>
						<td>
							<?if ($mobilephone != false){?>
								<span class="glyphicons glyphicons-iphone"></span><?php echo $mobilephone;?>
							<?}?>
						</td>
					</tr>
	        <?	}  ?>
			</table>
			</td>
			</tr>
		</table>
</div>