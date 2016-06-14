<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			29.08.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/attachment/attachment.class.php';

function reArrayFiles(&$file_post) {

	$file_ary = array();
	$file_count = count($file_post['name']);
	$file_keys = array_keys($file_post);

	for ($i=0; $i<$file_count; $i++) {
		foreach ($file_keys as $key) {
			$file_ary[$i][$key] = $file_post[$key][$i];
		}
	}

	return $file_ary;
}

$shopping_basket = new Shoppingbasket();
$shopping_basket_entrys = Array ();

if ($_SESSION["shopping_basket"]){ // Warenkorb aus der Session holen
	$shopping_basket = $_SESSION["shopping_basket"];
	$shopping_basket_entrys = $shopping_basket->getEntrys();
}

// Einen Eintrag bearbeiten
if ($_REQUEST["exec"] == 'edit_items'){
	$shopping_basket->setIntent(trim(addslashes($_REQUEST["shopping_intent"])));
	$shopping_basket->setNote(trim(addslashes($_REQUEST["shopping_note"])));
	$shopping_basket->setDeliveryAdressID((int)$_REQUEST["shopping_deliv_id"]);

	$tmp_entrys = $shopping_basket->getEntrys();
	$shopping_basket->clear();
	foreach($tmp_entrys as $tmp_item){
		//Menge aktualisieren
		$amount = (int)$_REQUEST["amount_{$tmp_item->getEntryid()}"];
		$tmp_item->setAmount($amount);
		if($tmp_item->getType() == Shoppingbasketitem::TYPE_ARTICLE){
			$tmp_article = new Article($tmp_item->getId());
			//ggf Preis anpassen
			$tmp_item->setPrice($tmp_article->getPrice($amount));
		}
		// liefer und rechnungsadresse setzen
		$tmp_item->setDeliveryAdressID((int)$_REQUEST["entry_deliv_{$tmp_item->getEntryid()}"]);
		$tmp_item->setInvoiceAdressID((int)$_REQUEST["entry_invoice_{$tmp_item->getEntryid()}"]);
		// Eintrag zum Warenkorb hinzufuegen
		$shopping_basket->addItem($tmp_item);
	}
	echo '<script language="JavaScript">document.location.href="index.php?pid=80";</script>';
}

// Einen Eintrag loeschen
if ($_REQUEST["delete_item"]){
//     var_dump($_REQUEST);
	$del_id = (int)$_REQUEST["del_id"];
	$shopping_basket->deleteItemByEntryId($del_id);
	$shopping_basket_entrys = $shopping_basket->getEntrys();
	header('Location: index.php?pid=80');
}

// Warenkorb leeren
if ($_REQUEST["exec"] == 'clear_shoppingbasket'){
	$shopping_basket->clear();
	$shopping_basket_entrys = Array ();
	header('Location: index.php?pid=80');
}

// Warenkorb
if ($_REQUEST["exec"] == 'send_shoppingbasket'){
	$shopping_basket->setIntent(trim(addslashes($_REQUEST["shopping_intent"])));
	$shopping_basket->setNote(trim(addslashes($_REQUEST["shopping_note"])));
	$shopping_basket->setDeliveryAdressID((int)$_REQUEST["shopping_deliv_id"]);

	$tmp_entrys = $shopping_basket->getEntrys();
	$shopping_basket->clear();
	foreach($tmp_entrys as $tmp_item){
		//Menge aktualisieren
		$amount = (int)$_REQUEST["amount_{$tmp_item->getEntryid()}"];
		$tmp_item->setAmount($amount);
		if($tmp_item->getType() == Shoppingbasketitem::TYPE_ARTICLE){
			$tmp_article = new Article($tmp_item->getId());
			//ggf Preis anpassen
			$tmp_item->setPrice($tmp_article->getPrice($amount));
			if ($tmp_article->getShop_needs_upload()==1)
			{
				$file = $_FILES["myfile_".$tmp_item->getEntryid()];
				if ($file["name"] != ""){
					$tmp_attachment = new Attachment();
					$tmp_attachment->setCrtdate(time());
					$tmp_attachment->setCrtuser($_USER);
					$tmp_attachment->setModule("Orderposition");
					$tmp_attachment->setObjectid(0);
					$tmp_attachment->move_save_file($file);
					$save_ok = $tmp_attachment->save();
					$savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
					if ($save_ok === true){
						$tmp_item->setFile($tmp_attachment->getId());
					}
				}
			}
		}
		// liefer und rechnungsadresse setzen
		$tmp_item->setDeliveryAdressID((int)$_REQUEST["entry_deliv_{$tmp_item->getEntryid()}"]);
		$tmp_item->setInvoiceAdressID((int)$_REQUEST["entry_invoice_{$tmp_item->getEntryid()}"]);
		// Eintrag zum Warenkorb hinzufuegen
		$shopping_basket->addItem($tmp_item);
	}

	$save_msg = $shopping_basket->send();

	// nur wenn erfolgreich gespeichert wurde darf geleert werden
	if ($save_msg == true){
		$shopping_basket->clear();
		$_SESSION["shopping_basket"] = new Shoppingbasket();

		$to = Array();
		$x=1;
		// Bestaetigungs-Mails senden
		$text = 'Sehr geehrter Kunde, <br> ihr Auftrag ist bei uns eingegangen und wird bearbeitet. <br> ';
		$text .= "Vielen Dank <br><br> Sie haben folgende Angaben gemacht:";
		$text .= "<table>";
		$text .= "<tr><td>Pos.</td><td>Artikel</td><td>Menge</td></tr>";
		foreach ($shopping_basket_entrys AS $entry){
			$text.="<tr><td>{$x}</td><td>{$entry->getTitle()}</td><td>{$entry->getAmount()}</td></tr>";
			$x++;
		}
		$text .= "</table> <br> <br>";
		$text .= "Kostenstelle: '" . $shopping_basket->getIntent() . "'<br>";
		$text .= "Hinweis: '" . $shopping_basket->getNote() . "'<br>";

		if ($tmp_attachment)
		{
			$text .= "Dateiname: " . $tmp_attachment->getOrig_filename() . "<br>";
		}

		if ($_SESSION["login_type"] == "businesscontact"){
			$to[] = $_BUSINESSCONTACT;
		}
		if($_SESSION["login_type"] == "contactperson"){
			$to[] = $_CONTACTPERSON;
		}

		foreach ($_CONTACTPERSON->getNotifymailadr() as $tmp_mail_adr){
			$to[] = $tmp_mail_adr;
		}

		$nachricht = new Nachricht();
		$nachricht->setFrom($_USER);
		$nachricht->setTo($to);
		$nachricht->setSubject("Bestellbestätigung");
		$nachricht->setText($text);
		$ret = $nachricht->send();

		$shopping_basket_entrys = Array ();
	}
	$save_msg = getSaveMessage($save_msg);
	$save_msg .= $DB->getLastError();
	echo '<script language="JavaScript">document.location.href="index.php?pid=80";</script>';
}

$overall_price = 0;
//gln $all_deliveryAddresses = Address::getAllAddresses($busicon, Address::ORDER_NAME, Address::FILTER_DELIV);
$all_deliveryAddresses = Address::getAllAddresses($busicon, Address::ORDER_ID, Address::FILTER_DELIV_SHOP);
$all_invoiceAddresses = Address::getAllAddresses($busicon, Address::ORDER_ID, Address::FILTER_INVC);
?>
<script>
	function BasketSubmit() {
		var isFormValid = true;

		$(".artfile").each(function () {
			if ($.trim($(this).val()).length == 0) {
				isFormValid = false;
			}
		});

		if (!isFormValid) {
			alert("Bitte die zugehörigen Dateiuploads auswählen!");
			return isFormValid;
		}

		if (confirm('Warenkorb wirklich absenden ?')) {
			$('#exec').val('send_shoppingbasket');
			$('#form_shopbasket').submit();
		}
	}
</script>

<form method="post" action="index.php" name="form_shopbasket" id="form_shopbasket" enctype="multipart/form-data">
	<input type="hidden" name="pid" value="<?=(int)$_REQUEST["pid"]?>">
	<input type="hidden" name="del_id" id="del_id" value="">
	<input type="hidden" id="exec" name="exec" value="edit_items">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				Warenkorb
				<span class="pull-right">
					<?=$save_msg?>
				</span>
			</h3>
		</div>
		<?	$x=1;
		if (count($shopping_basket_entrys) > 0){?>
			<div class="table-responsive">
				<table class="table table-hover">
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Nr.')?></td>
						<td class="content_row_header">&ensp;</td>
						<td class="content_row_header"><?=$_LANG->get('Titel')?></td>
						<td class="content_row_header" align="right"><?=$_LANG->get('Menge')?></td>
						<td class="content_row_header" align="right"><?=$_LANG->get('Preis')?></td>
						<td class="content_row_header" align="right"><?=$_LANG->get('ges. Preis')?></td>
						<td class="content_row_header" align="right"><?=$_LANG->get('Lief.-Adresse')?></td>
						<td class="content_row_header" align="right"><?=$_LANG->get('Rech.-Adresse')?></td>
						<td class="content_row_header">&ensp;</td>
					</tr>
					<?foreach ($shopping_basket_entrys as $entry){
						$perso_order = new Personalizationorder($entry->getId());
						$perso = new Personalization($perso_order->getPersoID());
						$allprices = $perso->getPrices();
						?>
						<tr>
							<td class="filerow"><?=$x?></td>
							<td class="filerow" >
								<?
								if($entry->getType() == Shoppingbasketitem::TYPE_ARTICLE){
									$artic = new Article($entry->getId());
									$all_pictures = $artic->getAllPictures();
									if ($all_pictures[0]["url"] != NULL && $all_pictures[0]["url"] !=""){?>
										<img src="../images/products/<?=$all_pictures[0]["url"]?>" width="100px">&nbsp;
									<?}} // Ende if(Bild gesetzt)?>
								<?
								if($entry->getType() == Shoppingbasketitem::TYPE_PERSONALIZATION){
									$tmp_pero_order = new Personalizationorder($entry->getId());
									$tmp_pero = new Personalization($tmp_pero_order->getPersoID());
									$artic = $tmp_pero->getArticle();
									if ($artic->getPicture() != "" && $artic->getPicture()!= NULL){?>
										<img src="../images/products/<?=$artic->getPicture()?>" width="100px">&nbsp;
									<?}} // Ende if(Bild gesetzt)
								// 						echo $artic->getId();
								?>
								&ensp;
							</td>
							<td class="filerow">
								<table><tr><td>
											<?	if($entry->getType() == Shoppingbasketitem::TYPE_ARTICLE){
												$tmp_pid = 60;
												$tmp_obj = "articleid=".$entry->getId();
												$tmp_exec= "exec=showArticleDetails";
											} else if($entry->getType() == Shoppingbasketitem::TYPE_PRODUCTS){
												$tmp_pid = 100;
												$tmp_obj = "productid=".$entry->getId();
												$tmp_exec= "exec=edit";
											}else if($entry->getType() == Shoppingbasketitem::TYPE_PERSONALIZATION){
												$tmp_pid = 40;
												$tmp_obj = "persoorderid=".$entry->getId();
												$tmp_exec= "exec=edit";
											}?>
											<a href="index.php?pid=<?=$tmp_pid?>&<?=$tmp_obj?>&<?=$tmp_exec?>">
												<?=substr($entry->getTitle(),0,21)?></a></td>
										<?php if ($entry->getType() == Shoppingbasketitem::TYPE_ARTICLE){if($artic->getShop_needs_upload()==1){?>
											<td><label class="filebutton"<span class="glyphicons glyphicons-floppy-disk pointer"></span>
												<input type="file" class="artfile" id="myfile_<?=$entry->getEntryid()?>" name="myfile_<?=$entry->getEntryid()?>" style="display: none"></label></td>
										<?php }}?>
									</tr></table>
							</td>
							<td class="filerow"  align="right">
								<?	if($entry->getType() == Shoppingbasketitem::TYPE_ARTICLE){
									$tmp_amount = $entry->getAmount();
									$tmp_readonly = "";
									?>
									<input 	style="width:50px;" name="amount_<?=$entry->getEntryid()?>"
											  value="<?=$entry->getAmount()?>" <?=$tmp_readonly?>>
									<?
								} else if($entry->getType() == Shoppingbasketitem::TYPE_PERSONALIZATION){?>
									<select name="amount_<?=$entry->getEntryid()?>" class="text" <? if ($perso_order->getStatus() > 1) echo "disabled";?>
											style="width: 150px;">
										<?
										foreach($allprices AS $price){ ?>
											<option value="<?=$price["sep_max"]?>"
												<?if($price["sep_max"] == $entry->getAmount()) echo "selected";?>>
												<?	echo $price["sep_max"];
												if($price["sep_show"]==1) echo " (".printPrice($price["sep_price"])." €)";	?>
											</option>
										<?	} ?>
									</select>
									<?
								}?>
							</td>
							<td class="filerow" align="right"><?=printPrice($entry->getPrice())?> &euro; </td>
							<td class="filerow" align="right">
								<?	if ($entry->getType() == Shoppingbasketitem::TYPE_ARTICLE){
									if ($artic->getOrderid()>0)
									{
										$ges_price = $entry->getPrice();
										$overall_price += $ges_price;
									} else {
										$ges_price = $entry->getAmount()*$entry->getPrice();
										$overall_price += $ges_price;
									}
								} else if ($entry->getType() == Shoppingbasketitem::TYPE_PERSONALIZATION){
									$ges_price = $entry->getPrice();
									if ($ges_price >0.00){
										$overall_price += $ges_price;
									}
								}
								echo printPrice($ges_price);?>&euro;
							</td>
							<td class="filerow" align="right">
								<select name="entry_deliv_<?=$entry->getEntryid()?>" class="text"	style="width: 200px;">
									<?	foreach($all_deliveryAddresses AS $deliv){ ?>
										<option value="<?=$deliv->getId()?>"
											<?if($deliv->getId() == $entry->getDeliveryAdressID()){echo 'selected="selected"';} else if ($deliv->getDefault()){echo 'selected="selected"';}?>>
											<?=$deliv->getNameAsLine()?> (<?=$deliv->getAddressAsLine()?>)
										</option>
									<?	} ?>
								</select>
							</td>
							<td class="filerow" align="right">
								<select name="entry_invoice_<?=$entry->getEntryid()?>" class="text"	style="width: 200px;">
									<?	foreach($all_invoiceAddresses AS $invoice){ ?>
										<option value="<?=$invoice->getId()?>"
											<?if($invoice->getId() == $entry->getInvoiceAdressID()){echo 'selected="selected"';} else if ($invoice->getDefault()){echo 'selected="selected"';}?>>
											<?=$invoice->getNameAsLine()?> (<?=$invoice->getAddressAsLine()?>)
										</option>
									<?	} ?>
								</select>
							</td>
							<td class="filerow" align="center">
								<input 	type="submit" value="X" name="delete_item" onClick="document.getElementById('del_id').value = <?=$entry->getEntryid()?>;">
							</td>
						</tr>
						<?$x++;
					}?>
				</table>
			</div>
			<?php
		};
		?>

		<div class="panel-body">
			<?
			if (count($shopping_basket_entrys) == 0){?>
				<div class="alert alert-info" role="alert"><?echo $_LANG->get("Der Warenkorb ist leer")?></div>
				<?
			} ?>
			<div class="form-horizontal">
				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Kostenstelle/Zweck</label>
					<div class="col-sm-10">
						<input type="text"class="form-control" id="shopping_intent" name="shopping_intent"
							   value="<?=$shopping_basket->getIntent()?>">
					</div>
				</div>

				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Hinweis/Bemerkung</label>
					<div class="col-sm-10">
						<textarea rows="4" cols="50" id="shopping_note" name="shopping_note" class="form-control"><?=$shopping_basket->getNote()?></textarea>
						<!-- <input type="text" id="shopping_note" name="shopping_note" style="width: 200px;"
					value="<?=$shopping_basket->getNote()?>"> -->
					</div>
				</div>

				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Preis des Warenkorbs</label>
					<div class="col-sm-10">
						<div class="form-control"><b><?=printPrice($overall_price)?></b></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-5">
						<button name="clear_shoppingbasket" class="btn btn-success"onclick="if (confirm('<?=$_LANG->get('Warenkorb wirklich leeren ?') ?>')) { $('#exec').val('clear_shoppingbasket'); $('#form_shopbasket').submit();} ">
							<?=$_LANG->get('Warenkorb leeren')?>
						</button>
					</div>

					<div class="col-md-6">
						<button type="submit" name="clear_shoppingbasket" class="btn btn-success">
							<?=$_LANG->get('&Auml;nderung speichern')?>
						</button>
					</div>

					<div class="col-md-1">
						<button   name="send_shoppingbasket" type="submit" class="btn btn-success" onclick="return BasketSubmit();">
							<?=$_LANG->get('Senden')?>
						</button>
					</div>
				</div>

			</div>
		</div>
	</div>
</form>