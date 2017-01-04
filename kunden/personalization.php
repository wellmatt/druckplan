<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			07.08.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

$_USER = new User($_BUSINESSCONTACT->getSupervisor()->getId());

// error_reporting(-1); 
// ini_set('display_errors', 1);

require_once './libs/modules/documents/document.class.php';
require_once './libs/modules/personalization/personalization.class.php';
require_once './libs/modules/personalization/personalization.item.class.php';
require_once './libs/modules/personalization/personalization.order.class.php';
require_once './libs/modules/personalization/personalization.orderitem.class.php';

if ($_REQUEST["exec"] == "delete"){
	$del_perso_order = new Personalizationorder($_REQUEST["deleteid"]);
	$del_docs = Document::getDocuments(Array("type" => Document::TYPE_PERSONALIZATION_ORDER, 
										"requestId" => $del_perso_order->getId(), 
										 "module" => Document::REQ_MODULE_PERSONALIZATION));
	$tmp_del = $del_perso_order->delete();
    foreach ($del_docs as $del_doc){
		$del_doc->delete();
	}
} 

if ($_REQUEST["exec"] == "edit" && $_REQUEST["subexec"] == "save"){

    savePersonalization();

}

function savePersonalization() {
    global $busicon, $DB;

    $tmp_perso = new Personalization((int)$_REQUEST["persoid"]);
    $perso_order = new Personalizationorder($_REQUEST["persoorderid"]);
    $perso_order->setTitle(trim(addslashes($_REQUEST["persoorder_title"])));
    $perso_order->setComment(trim(addslashes($_REQUEST["persoorder_comment"])));
    $perso_order->setCustomerID($busicon->getID());
    $perso_order->setPersoID((int)$_REQUEST["persoid"]);

    $save_retval = $perso_order->save();

    if($perso_order->getStatus() == 1){
        // nicht-aenderbare Werte duerfen nur gespeichert werden, wenn Perso-Ordner noch nicht bestellt ist
        $perso_order->setContactPersonID((int)$_REQUEST["persoorder_cp_id"]);
        $perso_order->setDeliveryAddressID((int)$_REQUEST["persoorder_deliv_id"]);
        $perso_order->setAmount((int)$_REQUEST["persoorder_amount"]);
        $save_retval = $perso_order->save();
        if($save_retval){
            $all_items_counter = (int)$_REQUEST["count_quantity"];
            for ($i=0 ; $i <= $all_items_counter ; $i++){
                $item = new Personalizationorderitem((int)$_REQUEST["item_id_{$i}"]);
                $item->setValue($_REQUEST["item_value_{$i}"]);
                $item->setPersoID($perso_order->getPersoID());
                $item->setPersoorderID($perso_order->getId());
                $item->setPersoItemID($_REQUEST["item_persoitemid_{$i}"]);
                $item_save = $item->save();
            }
        }
    }

    $savemsg = getSaveMessage($save_retval);
    if($DB->getLastError() != NULL && $DB->getLastError() != ""){
        $savemsg .= " - ".$DB->getLastError();
    }

    // Alte Vorschau-Dokumente entfernen
    $del_docs = Document::getDocuments(Array("type" => Document::TYPE_PERSONALIZATION_ORDER,
                                             "requestId" => $perso_order->getId(),
                                             "module" => Document::REQ_MODULE_PERSONALIZATION));
    foreach ($del_docs as $del_doc){
        $del_doc->delete();
    }
    // PDF Dokument zur Vorschau erstellen
    $doc = new Document();
    $doc->setRequestId($perso_order->getId());
    $doc->setRequestModule(Document::REQ_MODULE_PERSONALIZATION);
    $doc->setType(Document::TYPE_PERSONALIZATION_ORDER);
    $doc->setReverse(0);
    $hash = $doc->createDoc(Document::VERSION_EMAIL, false, false);
    $doc->setReverse(0);
    $doc->createDoc(Document::VERSION_PRINT, $hash, false);
    $doc->setName("PERSO_ORDER");
    $doc->save();

    $_REQUEST["persoid"] = 0; // Damit die Bestellung aufgerufen wird und nicht die Personalisierung
    $_REQUEST["persoorderid"] = $perso_order->getId(); // Damit die korrekte Personalisierungsbestellung aufgerufen wird
}

/***
// Absenden der Personalisierungsbestellung ohne Warenkorb
if ($_REQUEST["exec"] == "edit" && $_REQUEST["subexec"] == "send"){
	$perso_order = new Personalizationorder($_REQUEST["persoorderid"]);
	$perso_order->setStatus(2); 
	$perso_order->setOrderdate(time());
	$perso_order->save();
	$_REQUEST["persoid"] = 0;
}
*/

if ($_REQUEST["exec"] == "edit" && $_REQUEST["subexec"] == "addToSchoppingbasket"){

    savePersonalization();
	
	if ($_SESSION["shopping_basket"]){
		$shopping_basket = $_SESSION["shopping_basket"];
	} else {
		$shopping_basket = new Shoppingbasket();
	}
	
	$perso_order = new Personalizationorder((int)$_REQUEST["persoorderid"]);

	// Entscheiden, ob der Kunden den Preis sehen darf
	$tmp_price_visible = $perso_order->isPriceVisible((int)$_REQUEST["persoorder_amount"]);
	if($tmp_price_visible){
		$tmp_price = $perso_order->getPrice((int)$_REQUEST["persoorder_amount"]);
	} else {
		$tmp_price = 0.00;
	}
	
	if ($_REQUEST["persoorder_amount"]>0){
		// Warenkorbeintrag gestallten
		$attributes["id"] 		= $perso_order->getId();
		$attributes["title"] 	= $perso_order->getTitle();
		$attributes["amount"] 	= (int)$_REQUEST["persoorder_amount"];
		$attributes["price"]	= $tmp_price;
		$attributes["type"]		= Shoppingbasketitem::TYPE_PERSONALIZATION ;
		$attributes["entryid"]	= count($shopping_basket->getEntrys())+1;
		$item = new Shoppingbasketitem($attributes);
		
		//schauen, ob Artikel schon im Warenkorb ist
		if($shopping_basket->itemExists($item)){
			// Altes loeschen, aber temporaer zwischenspeichern
			$del_item = $shopping_basket->deleteItem($item->getId(), $item->getType());
			if ($del_item != NULL){
				
				/*** // Menge und Preis aktualisieren nur bei Artikeln
				// Neue Menge berechnen
				$newamount = $del_item->getAmount() + $item->getAmount();
				$item->setAmount($newamount);
				// ggf Preis anpassen (an die neue Menge)
				$newprice = $article->getPrice($newamount); // $item->getAmount());
				$item->setPrice($newprice);**/
	
				$shopping_basket->addItem($item);
			}
		}else{
		    $tmp_def_invc_ad = Address::getDefaultAddress($busicon, Address::FILTER_INVC);
		    $tmp_def_deli_ad = Address::getDefaultAddress($busicon, Address::FILTER_DELIV);
		    $item->setInvoiceAdressID($tmp_def_invc_ad->getId());
		    $item->setDeliveryAdressID($tmp_def_deli_ad->getId());
			$shopping_basket->addItem($item);
		}
		// Einkaufskorb auch wieder in die Session schreiben
		$_SESSION["shopping_basket"] = $shopping_basket;
	}
	$_REQUEST["persoid"] = 0;
	echo '<script language="JavaScript">document.location.href="index.php?pid=40&persoorderid='.$perso_order->getId().'&exec=edit";</script>';
}

// Entscheiden, ob man in einer neuen Personalisierung ist, oder in einer gespeicherten Bestellung
if ((int)$_REQUEST["persoid"] > 0){
	$page_type = "perso";
	$header_info = "Personalisierungsdetails";
} else {
	$page_type = "perso_order";
	$header_info = "Bestelldetails";
}
// Tabelle um den Warenkorb rechts anzeigen lasssen zu können?>


<?
if ($_REQUEST["exec"] == "edit"){
	//Ab hier wird die Personalisierung bearbeitet

	if($page_type == "perso"){
		$perso_order = new Personalizationorder();
		$perso = new Personalization($_REQUEST["persoid"]);
		$all_items = Personalizationitem::getAllPersonalizationitems($perso->getId(), "id", Personalizationitem::SITE_FRONT);
		$all_items2 = Personalizationitem::getAllPersonalizationitems($perso->getId(), "id", Personalizationitem::SITE_BACK);
		$tmp_title = "" ; // $perso->getTitle();
	} else {
		$perso_order = new Personalizationorder((int)$_REQUEST["persoorderid"]);			// nicht nocheinmal setzen
		$perso = new Personalization($perso_order->getPersoID());
		$all_items = Personalizationorderitem::getAllPersonalizationorderitems($perso_order->getId(), Personalizationitem::SITE_FRONT, "t2.id");
		$all_items2 = Personalizationorderitem::getAllPersonalizationorderitems($perso_order->getId(), Personalizationitem::SITE_BACK, "t2.id");
		$tmp_title = $perso_order->getTitle();
	}

	$count_quantity = count($all_items)+count($all_items2);
	$allprices = $perso->getPrices();
	$allCostomerContactPersons = ContactPerson::getAllContactPersons($busicon, BusinessContact::ORDER_NAME);
	$all_deliveryAddresses = Address::getAllAddresses($busicon, Address::ORDER_NAME, Address::FILTER_DELIV);

	if($page_type == "perso"){
		$docs = Document::getDocuments(Array("type" => Document::TYPE_PERSONALIZATION,
			"requestId" => $perso->getId(),
			"module" => Document::REQ_MODULE_PERSONALIZATION));
	} else {
		$docs = Document::getDocuments(Array("type" => Document::TYPE_PERSONALIZATION_ORDER,
			"requestId" => $perso_order->getId(),
			"module" => Document::REQ_MODULE_PERSONALIZATION));
	}
	?>
	<script language="JavaScript">
		function checkField(id)
		{
			// Nach absprache mit Anissa wieder rausgenommen vorerst
//			var original = String($("#item_value_default_"+id).val());
//			var value = String($("#item_value_"+id).val());
//			if (original.indexOf("\\t") > -1)
//			{
//				if (value.indexOf("\\t") < 0)
//				{
//					$("#item_value_"+id).val(original);
//					alert('Sie dürfen "\\t" aus diesem Feld nicht entfernen!');
//				}
//			}
		}
	</script>

	<form action="index.php" method="post" name="cust_perso_edit" id="cust_perso_edit"
		  onSubmit="return checkform(new Array(this.persoorder_title))" class="form-horizontal">
		<input type="hidden" name="persoid" value="<?=$perso->getId()?>">
		<input type="hidden" name="persoorderid" value="<?=$perso_order->getId()?>">
		<input type="hidden" name="pid" value="<?=$_REQUEST["pid"]?>">
		<input type="hidden" name="exec" value="edit">
		<input type="hidden" name="subexec" id="subexec" value="save">
		<input	type="hidden" name="count_quantity" id="count_quantity"
				  value="<? if($count_quantity > 0) echo $count_quantity; else echo "0";?>">

		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					  <div class="panel-heading">
							<h3 class="panel-title">
								<b>Personalisierungen</b>
							</h3>
					  </div>
					  <div class="panel-body">
						  <div class="panel panel-default">
							  <div class="panel-heading">
									<h3 class="panel-title">
										<?=$header_info?> - <?=$perso->getTitle()?>
										<span class="pull-right"><?=$savemsg?></span>
									</h3>
							  </div>
							  <div class="panel-body">
								  <div class="row">
									  <?
									  // PDF ausgeben
									  if (count($docs) && $docs != false){
										  $tmp_id =$_USER->getClient()->getId();
										  $hash = $docs[0]->getHash();

										  $obj_height = ($perso->getFormatheight() / 10 * 300 / 2.54 + 20) / 2;
										  $obj_width = ($perso->getFormatwidth() / 10 * 300 / 2.54 + 20) / 2;
										  ?>
										  <object data="../docs/personalization/<?=$tmp_id?>.per_<?=$hash?>_e.pdf" type="application/pdf"
												  width="<?=$obj_width?>" height="<?=$obj_height?>" ></object>
									  <? } ?>
								  </div>
								  <div class="row">
									  <br>
									  <?php
									  /**
									   * Um festzustellen, ob das Formular bereits abgesendet wurde,
									   * halten wir die Anzahl der bereits ausgefüllten Textfelder fest.
									   */
									  $filledInputs = 0;

									  ?>

									  <div class="col-md-12">
										  <div class="row">
											  <div class="col-md-6"> <!-- vorderseite -->
												  <? 	if (count($all_items) > 0 && $all_items != FALSE){
													  $y=1;
													  foreach ($all_items as $item){
														  // if ($item->getTitle() == "spacer") { continue; }
														  if($page_type == "perso"){
															  // Wenn es gerade eine Personalisierung aufgerufen wurde
															  $itemID = 0;
															  $persoitemID = $item->getID();
															  $persoitem = new Personalizationitem($persoitemID);
															  $tmp_type = $item->getBoxtype();
															  $tmp_label = $item->getTitle();
															  $tmp_titel = "";
														  } else {
															  // Wenn gerade eine Bestellung einer Personalisierung ausfegrufen wurde
															  $itemID = $item->getId();
															  $persoitemID = $item->getPersoItemID();
															  $persoitem = new Personalizationitem($persoitemID);
															  $tmp_type = $persoitem->getBoxtype();
															  $tmp_label = $persoitem->getTitle();
															  $tmp_titel = $item->getValue();
															  if(!empty($tmp_titel)) {
																  $filledInputs++;
															  }
														  }
														  ?>

														  <input type="hidden" name="item_id_<?=$y?>" value="<?=$itemID?>">
														  <input type="hidden" name="item_persoitemid_<?=$y?>" value="<?=$persoitemID?>">

														  <div class="form-group">
															  <label for="" class="col-sm-6 control-label">
																  <? if ($tmp_label == "spacer") { echo "&nbsp;"; } else { echo $tmp_label; }?>
															  </label>
															  <div class="col-sm-6">

																  <? if ($page_type == "perso" && $item->getPosition() == 1){
																	  $position_titles = $busicon->getPositionTitles();
																	  // print_r($position_titles);
																	  ?>
																	  <select name="item_value_<?=$y?>" id="item_value_<?=$y?>" class="form-control">
																		  <option value="" selected></option>
																		  <?
																		  foreach($position_titles as $pt)
																		  {?>
																			  <option value="<?=$pt?>"><?=$pt?></option>
																		  <?}?>
																	  </select>
																	  <?
																  } elseif ($page_type == "perso_order" && $persoitem->getPosition() == 1){
																	  $position_titles = $busicon->getPositionTitles();
																	  ?>
																	  <select name="item_value_<?=$y?>" id="item_value_<?=$y?>" class="form-control">
																		  <option value="<?=$tmp_titel?>" selected><?=$tmp_titel?></option>
																		  <?
																		  foreach($position_titles as $pt)
																		  {?>
																			  <option value="<?=$pt?>"><?=$pt?></option>
																		  <?}?>
																	  </select>
																	  <?
																  } else {
																	  if ($tmp_type == 1) {?>
																		  <input name="item_value_<?=$y?>" id="item_value_<?=$y?>" class="form-control" <? if ($tmp_label == "spacer") { echo ' type="hidden" '; } else { echo ' type="text" '; } ?>
																			  <? if ($perso_order->getStatus() > 1) echo "disabled "; ?>
																			  <? if ($persoitem->getReadOnly() == 1) echo "readonly ";
																			  if ($tmp_label == "spacer") {
																				  echo 'value ="' . htmlspecialchars($tmp_label) . '" ';
																			  } else {
																				  if($page_type == "perso"){
																					  if ($item->getPreDefined() == 1) {
																						  echo 'value ="' . htmlspecialchars($tmp_label) . '" onchange="checkField('.$y.')" ';
																					  } else {
																						  echo 'value ="' . htmlspecialchars($tmp_titel) . '" ';
																					  }
																				  } else {
																					  echo 'value ="' . htmlspecialchars($tmp_titel) . '" ';
																				  }
																			  }
																			  ?> >
																		  <?php
																		  if($page_type == "perso"){
																			  if ($item->getPreDefined() == 1){
																				  ?>
																				  <input type="hidden" id="item_value_default_<?=$y?>" value="<?php echo htmlspecialchars($tmp_label);?>">
																				  <?php
																			  }
																		  }?>
																	  <?} else {?>
																		  <textarea name="item_value_<?=$y?>" class="form-control" type="text" style="height: 30px;"
																			  <? if ($perso_order->getStatus() > 1) echo "disabled";?>
																			  <? if ($item->getPreDefined() == 1) echo ' value="' . $tmp_label . '" ';?>
																		  ><?=$tmp_titel?></textarea>

																	  <?}
																  }?>
															  </div>
														  </div>
														  <? 	$y++;
													  }
												  } else { ?>
													  <span class="alert-info"><?=$_LANG->get('Keine Felder angelegt');?></span>
												  <?	} ?>
											  </div>
											  <div class="col-md-6"> <!-- rueckseite -->
												  <? 	if (count($all_items2) > 0 && $all_items2 != FALSE){
													  foreach ($all_items2 as $item){
														  // if ($item->getTitle() == "spacer") { continue; }
														  if($page_type == "perso"){
															  // Wenn es gerade eine Personalisierung aufgerufen wurde
															  $itemID = 0;
															  $persoitemID = $item->getID();
															  $persoitem = new Personalizationitem($persoitemID);
															  $tmp_type = $item->getBoxtype();
															  $tmp_label = $item->getTitle();
															  $tmp_titel = "";
														  } else {
															  // Wenn gerade eine Bestellung einer Personalisierung ausfegrufen wurde
															  $itemID = $item->getId();
															  $persoitemID = $item->getPersoItemID();
															  $persoitem = new Personalizationitem($persoitemID);
															  $tmp_type = $persoitem->getBoxtype();
															  $tmp_label = $persoitem->getTitle();
															  $tmp_titel = $item->getValue();
														  }
														  ?>

														  <input type="hidden" name="item_id_<?=$y?>" value="<?=$itemID?>">
														  <input type="hidden" name="item_persoitemid_<?=$y?>" value="<?=$persoitemID?>">

														  <div class="form-group">
															  <label for="" class="col-sm-6 control-label">
																  <? if ($tmp_label == "spacer") { echo "&nbsp;"; } else { echo $tmp_label; }?>
															  </label>
															  <div class="col-sm-6">

																  <? if ($page_type == "perso" && $item->getPosition() == 1){
																	  $position_titles = $busicon->getPositionTitles();
																	  // print_r($position_titles);
																	  ?>
																	  <select name="item_value_<?=$y?>" id="item_value_<?=$y?>" class="form-control">
																		  <option value="" selected></option>
																		  <?
																		  foreach($position_titles as $pt)
																		  {?>
																			  <option value="<?=$pt?>"><?=$pt?></option>
																		  <?}?>
																	  </select>
																	  <?
																  } elseif ($page_type == "perso_order" && $persoitem->getPosition() == 1){
																	  $position_titles = $busicon->getPositionTitles();
																	  ?>
																	  <select name="item_value_<?=$y?>" id="item_value_<?=$y?>" class="form-control">
																		  <option value="<?=$tmp_titel?>" selected><?=$tmp_titel?></option>
																		  <?
																		  foreach($position_titles as $pt)
																		  {?>
																			  <option value="<?=$pt?>"><?=$pt?></option>
																		  <?}?>
																	  </select>
																	  <?
																  } else {
																	  if ($tmp_type == 1) {?>
																		  <input name="item_value_<?=$y?>" class="form-control" <? if ($tmp_label == "spacer") { echo ' type="hidden" '; } else { echo ' type="text" '; } ?>
																			  <? if ($perso_order->getStatus() > 1) echo "disabled "; ?>
																			  <? if ($persoitem->getReadOnly() == 1) echo "readonly ";
																			  if ($tmp_label == "spacer") {
																				  echo 'value ="' . htmlspecialchars($tmp_label) . '" ';
																			  } else {
																				  if($page_type == "perso"){
																					  if ($item->getPreDefined() == 1) {
																						  echo 'value ="' . htmlspecialchars($tmp_label) . '" onchange="checkField('.$y.')" ';
																					  } else {
																						  echo 'value ="' . htmlspecialchars($tmp_titel) . '" ';
																					  }
																				  } else {
																					  echo 'value ="' . htmlspecialchars($tmp_titel) . '" ';
																				  }
																			  }
																			  ?> >
																			  <?php
																			  if($page_type == "perso"){
																				  if ($item->getPreDefined() == 1){
																					  ?>
																					  <input type="hidden" id="item_value_default_<?=$y?>" value="<?php echo htmlspecialchars($tmp_label);?>">
																					  <?php
																				  }
																			  }?>
																	  <?} else {?>
																		  <textarea name="item_value_<?=$y?>" class="form-control" type="text" style="height: 30px;"
																			  <? if ($perso_order->getStatus() > 1) echo "disabled";?>
																			  <? if ($item->getPreDefined() == 1) echo ' value="' . $tmp_label . '" ';?>
																		  ><?=$tmp_titel?></textarea>

																	  <?}
																  }?>
															  </div>
														  </div>
														  <? 	$y++;
													  }
												  } else { ?>
													  <span class="alert-info"><?=$_LANG->get('Keine Felder angelegt');?></span>
												  <?	} ?>
											  </div>
										  </div>
									  </div>
								  </div>

								  <div class="row">
									  <div class="col-md-12">
										  <button type="submit" class="btn btn-success"><?=$_LANG->get('Speichern')?></button>
										  <br>&nbsp;
										  <div class="row" style="display: <?= ($filledInputs > 0) ? 'block' : 'none' ?>">
											  <div class="form-group">
												  <label for="" class="col-sm-2 control-label">Titel</label>
												  <div class="col-sm-10">
													  <input type="text" class="form-control" name="persoorder_title" id="persoorder_title" value="<?= (empty($tmp_title)) ? '(Unbenannt)' : $tmp_title ?>">
												  </div>
											  </div>
											  <div class="form-group">
												  <label for="" class="col-sm-2 control-label">Menge</label>
												  <div class="col-sm-10">
													  <div class="input-group">
														  <select name="persoorder_amount" id="persoorder_amount" class="form-control">
															  <?
															  foreach($allprices AS $price){ ?>
																  <option value="<?=$price["sep_max"]?>"
																	  <?if($price["sep_max"] == $perso_order->getAmount()) echo "selected";?>>
																	  <?	echo $price["sep_max"];
																	  if($price["sep_show"]==1) echo " (".printPrice($price["sep_price"])." €)";	?>
																  </option>
															  <?	} ?>
														  </select>
														  <div class="input-group-addon"><span>Stk.</span></div>
													  </div>
												  </div>
											  </div>
											  <div class="form-group">
												  <label for="" class="col-sm-2 control-label">Anprechpartner</label>
												  <div class="col-sm-10">
													  <select name="persoorder_cp_id" id="persoorder_cp_id" class="form-control" <? if ($perso_order->getStatus() > 1) echo "disabled";?>>
														  <option value="0" > &lt; <?=$_LANG->get('Bitte w&auml;hlen');?> &gt;</option>
														  <?	foreach($allCostomerContactPersons AS $cp){ ?>
															  <option value="<?=$cp->getId()?>"
																  <?if($cp->getId() == $perso_order->getContactPersonId()) echo "selected";?>>
																  <?=$cp->getNameAsLine2()?>
															  </option>
														  <?	} ?>
													  </select>
												  </div>
											  </div>
											  <div class="form-group">
												  <label for="" class="col-sm-2 control-label">Status</label>
												  <div class="col-sm-10">
													  <div class="form-control">
														  <img src="../images/status/<?=$perso_order->getStatusImage()?>"
															   title="<?=$perso_order->getStatusDescription()?>"
															   alt="<?=$perso_order->getStatusDescription()?>" > <?=$perso_order->getStatusDescription()?>
													  </div>
												  </div>
											  </div>
											  <div class="form-group">
												  <label for="" class="col-sm-2 control-label">Erstelldatum</label>
												  <div class="col-sm-10">
													  <div class="form-control">
														  <?
														  if($perso_order->getCrtdate() > 0){
															  echo date("d.m.Y - H:i",$perso_order->getCrtdate());
														  } else {
															  echo "-";
														  }?>
													  </div>
												  </div>
											  </div>
											  <div class="form-group">
												  <label for="" class="col-sm-2 control-label">Bestelldatum</label>
												  <div class="col-sm-10">
													  <div class="form-control">
														  <?
														  if($perso_order->getOrderdate() > 0){
															  echo date("d.m.Y - H:i",$perso_order->getOrderdate());
														  } else {
															  echo "-";
														  }?>
													  </div>
												  </div>
											  </div>
											  &nbsp;<br>&nbsp;
											  <button type="submit" class="btn btn-success">
												  <?=$_LANG->get('Speichern')?>
											  </button>
											  <button type="button" class="btn btn-success" onclick="window.location.href='index.php?pid=<?=$_REQUEST["pid"]?>'">
												  <?=$_LANG->get('Zur&uuml;ck')?>
											  </button>
											  <button type="button" class="btn btn-success" onclick="document.getElementById('subexec').value='addToSchoppingbasket';
												 document.getElementById('cust_perso_edit').submit();">
												  <?=$_LANG->get('in den Warenkorb')?>
											  </button>
										  </div>
									  </div>
								  </div>
							  </div>
						  </div>
					  </div>
				</div>
			</div>
		</div>
	</form>

<?php } else { // ----------------------------- Auflistung der Personalisierungen -----------------------------------------------------
		
	$search_string = $_REQUEST["search_string"];
	$search_ver_string = $_REQUEST["search_ver_string"];
	$all_persos = Personalization::getAllPersonalizationsByCustomerSearch($busicon->getId(), "title ASC",$search_ver_string);
	?>

	<script language="javascript">
	function askDel(myurl){
	   if(confirm("Sind Sie sicher?")){
		  if(myurl != '')
			 location.href = myurl;
		  else
			 return true;
	   }
	   return false;
	}
	</script>
	<!-- DataTables -->
	<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.css">
	<link rel="stylesheet" type="text/css" href="../css/dataTables.bootstrap.css">
	<script type="text/javascript" charset="utf8" src="../jscripts/datatable/jquery.dataTables.min.js"></script>
	<script type="text/javascript" charset="utf8" src="../jscripts/datatable/numeric-comma.js"></script>
	<script type="text/javascript" charset="utf8" src="../jscripts/datatable/dataTables.bootstrap.js"></script>
	<script type="text/javascript">
	$(document).ready(function() {
		var porder_table = $('#porder_table').DataTable( {
			"processing": true,
			"bServerSide": true,
			"sAjaxSource": "personalization.dt.ajax.php?customerid=<?php echo $busicon->getId();?>",
			"paging": true,
			"stateSave": false,
			"pageLength": "25",
			"aaSorting": [[ 3, "desc" ]],
			"dom": 'lrtip',
			"lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
			"columns": [
						null,
						null,
						null,
						null,
						null,
						null,
						null
					  ],
			"language":
						{
							"emptyTable":     "Keine Daten vorhanden",
							"info":           "Zeige _START_ bis _END_ von _TOTAL_ Eintr&auml;gen",
							"infoEmpty": 	  "Keine Seiten vorhanden",
							"infoFiltered":   "(gefiltert von _MAX_ gesamten Eintr&auml;gen)",
							"infoPostFix":    "",
							"thousands":      ".",
							"lengthMenu":     "Zeige _MENU_ Eintr&auml;ge",
							"loadingRecords": "Lade...",
							"processing":     "Verarbeite...",
							"search":         "Suche:",
							"zeroRecords":    "Keine passenden Eintr&auml;ge gefunden",
							"paginate": {
								"first":      "Erste",
								"last":       "Letzte",
								"next":       "N&auml;chste",
								"previous":   "Vorherige"
							},
							"aria": {
								"sortAscending":  ": aktivieren um aufsteigend zu sortieren",
								"sortDescending": ": aktivieren um absteigend zu sortieren"
							}
						}
		} );
		$('#search').keyup(function(){
			porder_table.search( $(this).val() ).draw();
		});
	} );
	</script>

	<div class="panel panel-default">
		  <div class="panel-heading">
				<h3 class="panel-title">Personalisierungen</h3>
		  </div>
		  <div class="panel-body">
			  <div class="panel panel-default">
			  	  <div class="panel-heading">
			  			<h3 class="panel-title">
							Verfügbar
							<span class="pull-right">
								<form action="index.php" method="post" class="form-horizontal" name="perso_ver_search" id="perso_ver_search" >
									<input name="pid" type="hidden" value="<?=$_REQUEST["pid"]?>" />
									<div class="form-group">
										<div class="col-sm-10">
											<input name="search_ver_string" class="form-control" type="text" value="<?=$search_ver_string?>"/>
										</div>
										<div class="col-sm-1">
											<span class="glyphicons glyphicons-search pointer"
												  alt="<?=$_LANG->get('Suchen');?>"
												  onClick="document.getElementById('perso_ver_search').submit()">
											</span>
										</div>
									</div>
								</form>
							</span>
						</h3>
			  	  </div>
				  <div class="table-responsive">
					  <table class="table table-hover">
						  <thead>
							  <tr>
								  <th>Bild</th>
								  <th>Titel</th>
								  <th>Optionen</th>
							  </tr>
						  </thead>
						  <tbody>
						  <?foreach ($all_persos AS $perso){ ?>
							  <tr class="filerow">
								  <td class="filerow">
									  <img src="../docs/personalization/<?=$perso->getPreview()?>" width="80px">
								  </td>
								  <td class="filerow"><?=$perso->getTitle()?></td>
								  <td class="filerow">
									  <a href="index.php?pid=<?=$_REQUEST["pid"]?>&persoid=<?=$perso->getId()?>&exec=edit" class="button">Ansehen/Bestellen</a>
								  </td>
							  </tr>
						  <? $x++; } ?>
						  </tbody>
					  </table>
				  </div>
			  </div>
			  <div class="panel panel-default">
			  	  <div class="panel-heading">
			  			<h3 class="panel-title">Gespeichert</h3>
			  	  </div>
				  <div class="panel-body">
					  <div class="panel panel-default">
						  <div class="panel-heading">
							  <h3 class="panel-title">
								  Filter
							  </h3>
						  </div>
						  <div class="panel-body">
							  <div class="form-group">
								  <label for="" class="col-sm-2 control-label">Suche</label>
								  <div class="col-sm-4">
									  <input type="text" id="search1" class="form-control" placeholder="">
								  </div>
							  </div>
						  </div>
					  </div>
				  </div>
				  <div class="table-responsive">
					  <table id="porder_table" width="100%" cellpadding="0" cellspacing="0" class="stripe hover row-border order-column">
						  <thead>
						  <tr>
							  <th width="20"><?=$_LANG->get('ID')?></th>
							  <th width="105"><?=$_LANG->get('Beschreibung')?></th>
							  <th><?=$_LANG->get('Titel')?></th>
							  <th width="80"><?=$_LANG->get('Erstelldatum')?></th>
							  <th width="80"><?=$_LANG->get('Bestelldatum')?></th>
							  <th width="160"><?=$_LANG->get('Menge')?></th>
							  <th width="160"><?=$_LANG->get('Optionen')?></th>
						  </tr>
						  </thead>
					  </table>
				  </div>
			  </div>
		  </div>
	</div>
<? } ?>
	