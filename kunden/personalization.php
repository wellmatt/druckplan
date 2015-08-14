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
                $item->setValue(addslashes($_REQUEST["item_value_{$i}"]));
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
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<colgroup>
	<col>
	<col width="2">
	<col width="200">
	</colgroup>
	<tr>
		<td valign="top">
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
		
		?>			
			<div class="box2" align="center">
				<table width="100%">
					<tr>
						<td width="33%"><h1><?=$header_info?></h1></td>
						<td width="33%" align="center"><b><?=$perso->getTitle()?></b></td>
						<td align="right"><?=$savemsg?></td>
					</tr>
				</table>
				<br/>
			<? // =$docs[0]->getHash()
				// PDF hohlen
				if($page_type == "perso"){
					$docs = Document::getDocuments(Array("type" => Document::TYPE_PERSONALIZATION, 
														 "requestId" => $perso->getId(), 
														 "module" => Document::REQ_MODULE_PERSONALIZATION));
				} else {
					$docs = Document::getDocuments(Array("type" => Document::TYPE_PERSONALIZATION_ORDER,
														 "requestId" => $perso_order->getId(),
														 "module" => Document::REQ_MODULE_PERSONALIZATION));
				} ?>
				<table>
				<tr>
					<td align="center">
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
					</td>
				</tr>
				</table>
			</div>
		
			<form action="index.php" method="post" name="cust_perso_edit" id="cust_perso_edit" 
					onSubmit="return checkform(new Array(this.persoorder_title))">
				<input type="hidden" name="persoid" value="<?=$perso->getId()?>">
				<input type="hidden" name="persoorderid" value="<?=$perso_order->getId()?>">
				<input type="hidden" name="pid" value="<?=$_REQUEST["pid"]?>">
				<input type="hidden" name="exec" value="edit">
				<input type="hidden" name="subexec" id="subexec" value="save">
				<input	type="hidden" name="count_quantity" id="count_quantity"
					value="<? if($count_quantity > 0) echo $count_quantity; else echo "0";?>">
				<div class="box2">
				<table>
				<tr>
				<td valign="top">
                    <?php
                    /**
                     * Um festzustellen, ob das Formular bereits abgesendet wurde,
                     * halten wir die Anzahl der bereits ausgefüllten Textfelder fest.
                     */
                    $filledInputs = 0;

                    ?>
					<table id="table-items">
						<colgroup>
				        	<col width="150">
				        	<col width="200">
				    	</colgroup>
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
							<tr>
								<td class="content_row">
									<b><? if ($tmp_label == "spacer") { echo "&nbsp;"; } else { echo $tmp_label; }?></b>
								</td>
								<td class="content_row">
									<input type="hidden" name="item_id_<?=$y?>" value="<?=$itemID?>">
									<input type="hidden" name="item_persoitemid_<?=$y?>" value="<?=$persoitemID?>">
									<? if ($page_type == "perso" && $item->getPosition() == 1){
										$position_titles = $busicon->getPositionTitles();
										// print_r($position_titles);
										?>
										<select name="item_value_<?=$y?>" style="width:230px" class="text">
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
										<select name="item_value_<?=$y?>" style="width:230px" class="text">
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
										<input 	name="item_value_<?=$y?>" class="text" <? if ($tmp_label == "spacer") { echo ' type="hidden" '; } else { echo ' type="text" '; } ?> 
												<? if ($perso_order->getStatus() > 1) echo "disabled "; ?>
												<? if ($persoitem->getReadOnly() == 1) echo "readonly ";
												if ($tmp_label == "spacer") { 
													echo 'value ="' . htmlspecialchars($tmp_label) . '" '; 
												} else { 
													if($page_type == "perso"){
														if ($item->getPreDefined() == 1) {
															echo 'value ="' . htmlspecialchars($tmp_label) . '" '; 
														} else {
															echo 'value ="' . htmlspecialchars($tmp_titel) . '" '; 
														}
													} else {
														echo 'value ="' . htmlspecialchars($tmp_titel) . '" '; 
													}
												} 
												?> 
												style="width: 230px" >
									<?} else {?>
										<textarea 	name="item_value_<?=$y?>" class="text" type="text" style="width: 227px; height: 30px;"  
												<? if ($perso_order->getStatus() > 1) echo "disabled";?> 
												<? if ($item->getPreDefined() == 1) echo ' value="' . $tmp_label . '" ';?>
												><?=$tmp_titel?></textarea>
												
									<?}
									}?>
								</td>
							</tr>
					<? 	$y++;
						} 	
					} else { ?>
						<tr>
							<td class="content_row" colspan="6" id="td_empty" align="center">
								<?=$_LANG->get('Keine Felder angelegt');?>
							</td>
						</tr>
				<?	} ?>
					</table>
				</td>
				<td valign="top">
					<table id="table-items">
						<colgroup>
				        	<col width="150">
				        	<col width="200">
				    	</colgroup>
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
							<tr>
								<td class="content_row">
									<b><? if ($tmp_label == "spacer") { echo "&nbsp;"; } else { echo $tmp_label; }?></b>
								</td>
								<td class="content_row">
									<input type="hidden" name="item_id_<?=$y?>" value="<?=$itemID?>">
									<input type="hidden" name="item_persoitemid_<?=$y?>" value="<?=$persoitemID?>">
									<? if ($page_type == "perso" && $item->getPosition() == 1){
										$position_titles = $busicon->getPositionTitles();
										?>
										<select name="item_value_<?=$y?>" style="width:230px" class="text">
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
										<select name="item_value_<?=$y?>" style="width:230px" class="text">
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
										<input 	name="item_value_<?=$y?>" class="text" <? if ($tmp_label == "spacer") { echo ' type="hidden" '; } else { echo ' type="text" '; } ?>
												<? if ($perso_order->getStatus() > 1) echo "disabled "; ?>
												<? if ($persoitem->getReadOnly() == 1) echo "readonly ";
												if ($tmp_label == "spacer") { 
													echo 'value ="' . $tmp_label . '" '; 
												} else { 
													if($page_type == "perso"){
														if ($item->getPreDefined() == 1) {
															echo 'value ="' . $tmp_label . '" '; 
														} else {
															echo 'value ="' . $tmp_titel . '" '; 
														}
													} else {
														echo 'value ="' . $tmp_titel . '" '; 
													}
												} 
												?>
												style="width: 230px" >
									<?} else {?>
										<textarea 	name="item_value_<?=$y?>" class="text" type="text" style="width: 227px; height: 30px;"  
												<? if ($perso_order->getStatus() > 1) echo "disabled";?>
												<? if ($item->getPreDefined() == 1) echo ' value="' . $tmp_label . '" ';?>
												><?=$tmp_titel?></textarea>
												
									<?}
									}?>
								</td>
							</tr>
					<? 	$y++;
						} 	
					} else { ?>
						<tr>
							<td class="content_row" colspan="6" id="td_empty" align="center">
								<?=$_LANG->get('Keine Felder angelegt');?>
							</td>
						</tr>
				<?	} ?>
					</table>
				</td></tr></table>
				</div>

                <div class="__box2">
                    <table width="100%">
                        <tr>
                            <td style="width: 400px;">&nbsp;</td>
                            <td colspan="2">
                                <input type="submit" class="button" value="<?=$_LANG->get('Speichern')?>"> &ensp;
                            </td>
                        </tr>
                    </table>
                </div>
				
				<div class="box2" style="display: <?= ($filledInputs > 0) ? 'block' : 'none' ?>">
					<table>
					<colgroup>
				        	<col width="150">
				        	<col width="310">
				        	<col width="130">
				        	<col>
				    	</colgroup>
				    	<tr>
				    		<td valign="top"><b>Dokument speichern unter*</b></td>
				    		<td valign="top">
				    			<input  type="text" name="persoorder_title" value="<?= (empty($tmp_title)) ? '(Unbenannt)' : $tmp_title ?>" style="width: 150px;">
				    		</td>
				    		<td rowspan="5" valign="top"><b>Bemerkung </b></td>
				    		<td rowspan="5" valign="top">
				    			<textarea name="persoorder_comment" rows="4" cols="35"><?=stripslashes($perso_order->getComment())?></textarea>
				    		</td>
				    	</tr>
				    	<tr>
				    		<td valign="top"><b>Bestellmenge</b></td>
				    		<td valign="top">
				    			
				    			<select name="persoorder_amount" class="text" <? if ($perso_order->getStatus() > 1) echo "disabled";?> 
				    					style="width: 150px;">
				    				<?
				    				foreach($allprices AS $price){ ?>
				    					<option value="<?=$price["sep_max"]?>"
				    							<?if($price["sep_max"] == $perso_order->getAmount()) echo "selected";?>>
				    					<?	echo $price["sep_max"];
				    						if($price["sep_show"]==1) echo " (".printPrice($price["sep_price"])." €)";	?>
				    					</option>
								<?	} ?>
				    			</select> Stk.
				    			
				    			<!-- input  type="text" name="persoorder_amount" <? if ($perso_order->getStatus() > 1) echo "disabled";?> 
				    					value="<?=$perso_order->getAmount()?>" style="width:50Px"> Stk. -->
				    		</td>
				    	</tr>
				    	<tr>
				    		<td valign="top"><b>Anprechpartner</b></td>
				    		<td valign="top">
				    			<select name="persoorder_cp_id" class="text" <? if ($perso_order->getStatus() > 1) echo "disabled";?> 
				    					style="width: 150px;">
				    				<option value="0" > &lt; <?=$_LANG->get('Bitte w&auml;len');?> &gt;</option>
				    			<?	foreach($allCostomerContactPersons AS $cp){ ?>
				    					<option value="<?=$cp->getId()?>"
				    							<?if($cp->getId() == $perso_order->getContactPersonId()) echo "selected";?>>
				    					<?=$cp->getNameAsLine2()?>
				    					</option>
								<?	} ?>
				    			</select> 
				    		</td>
				    	</tr>
				    	<!-- tr>
				    		<td valign="top"><b>Lieferaddresse</b></td>
				    		<td valign="top">
				    			<select name="persoorder_deliv_id" class="text" <? if ($perso_order->getStatus() > 1) echo "disabled";?> 
				    					style="width: 150px;">
				    				<option value="0" > &lt; <?=$_LANG->get('Bitte w&auml;len');?> &gt;</option>
				    			<?	foreach($all_deliveryAddresses AS $deliv){ ?>
				    					<option value="<?=$deliv->getId()?>"
				    							<?if($deliv->getId() == $perso_order->getDeliveryAddressID()) echo "selected";?>>
				    					<?=$deliv->getAddressAsLine()?>
				    					</option>
								<?	} ?>
				    			</select> 
				    		</td>
				    	</tr -->
				    	<tr>
				    		<td><b>Status</b></td>
				    		<td>
				    			<img src="../images/status/<?=$perso_order->getStatusImage()?>" 
				        			 title="<?=$perso_order->getStatusDescription()?>" 
				        			 alt="<?=$perso_order->getStatusDescription()?>" >
				    		</td>
				    	</tr>
				    	<tr>
				    		<td><b>Erstelldatum</b></td>
				    		<td><?
				    			if($perso_order->getCrtdate() > 0){
				    				echo date("d.m.Y - H:i",$perso_order->getCrtdate());
								} else {
									echo "-";
								}?>
				    		</td>
				    	</tr>
				    	<tr>
				    		<td><b>Bestelldatum</b></td>
				    		<td><?
				    			if($perso_order->getOrderdate() > 0){
				    				echo date("d.m.Y - H:i",$perso_order->getOrderdate());
								} else {
									echo "-";
								}?>
				    		</td>
				    	</tr>
					</table>
				</div>


				
				<?// Speicher & Navigations-Button ?>
				<table width="100%" style="display: <?= ($filledInputs > 0) ? 'block' : 'none' ?>">
				    <colgroup>
				        <col width="180">
				        <col >
				        <col width="180">
				    </colgroup> 
				    <tr>
				        <td>
				         	&ensp; 
				        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
				        			onclick="window.location.href='index.php?pid=<?=$_REQUEST["pid"]?>'">
				        </td>
				        
				        <td class="content_row_clear" align="right">
				        	<? if ($perso_order->getStatus() == 1){?>
				        	<input type="button" class="button" value="<?=$_LANG->get('In den Warenkorb')?>"
				        			onclick="document.getElementById('subexec').value='addToSchoppingbasket'; 
					        				 document.getElementById('cust_perso_edit').submit(); "> &ensp;
				        	<?}?> &ensp; 
				        </td>
				        <td class="content_row_clear" align="right">
				        	<!--<input type="submit" class="button" value="<?/*=$_LANG->get('Speichern')*/?>"> &ensp;-->
				        </td>
				    </tr>
				</table>
			</form>
		<?
		
		} else { // ----------------------------- Auflistung der Personalisierungen -----------------------------------------------------
		
		$search_string = $_REQUEST["search_string"];
		$search_ver_string = $_REQUEST["search_ver_string"];
		
			
			$all_persos = Personalization::getAllPersonalizationsByCustomerSearch($busicon->getId(), "title ASC",$search_ver_string);
// 			$all_persoorders = Personalizationorder::getAllPersonalizationordersForShop($busicon->getId(), Personalizationorder::ORDER_CRTDATE, $search_string);
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
            var art_table = $('#porder_table').DataTable( {
                "processing": true,
                "bServerSide": true,
                "sAjaxSource": "personalization.dt.ajax.php?customerid=<?php echo $busicon->getId();?>",
                "paging": true,
        		"stateSave": false,
        		"pageLength": "25",
        		"aaSorting": [[ 2, "asc" ]],
        		"dom": 'flrtip',        
        		"lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
        		"columns": [
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
        } );
        </script>
		 
		<div class="box2" style="min-height:180px;">
			<table style="width:100%">
				<tr>
					<td width="400px">
						<h1>Verf&uuml;gbare Personalisierungen</h1>
			    	</td>
			    	<td width="200px" align="right">
			    		<form action="index.php" method="post" name="perso_ver_search" id="perso_ver_search" >
			    			<input name="pid" type="hidden" value="<?=$_REQUEST["pid"]?>" />
			    			<input name="search_ver_string" type="text" value="<?=$search_ver_string?>" style="width:150px;"/>
			    			<img src="../images/icons/magnifier-left.png" alt="<?=$_LANG->get('Suchen');?>" class="pointer"
			    				 onClick="document.getElementById('perso_ver_search').submit()" />
			    		</form>
			    	</td>
			    </tr>
			</table>
			<table cellpadding="2" cellspacing="0" border="0" width="100%">
			    <colgroup>
			        <col width="100">
			        <col>
			        <col width="160">
			    </colgroup>
			    <tr>
			        <td class="filerow_header">Bild</td>
			        <td class="filerow_header">Titel</td>
			        <td class="filerow_header">Optionen</td>
			    </tr>
			    <?foreach ($all_persos AS $perso){ ?>
			    <tr class="filerow">
					<td class="filerow">
						<img src="../images/products/<?=$perso->getPicture()?>" alt="..." width="80px">
					</td>
			        <td class="filerow"><?=$perso->getTitle()?></td>
			        <td class="filerow">
			        	<a href="index.php?pid=<?=$_REQUEST["pid"]?>&persoid=<?=$perso->getId()?>&exec=edit" class="button">Ansehen/Bestellen</a>
			        </td>
			    </tr>
			    <? $x++; } ?>
			</table>
			
			<br/><br/>
			
			<table style="width:100%">
				<tr>
					<td width="400px">
			    		<h1>Angelegte Personalisierungen</h1>
			    	</td>
			    </tr>
			</table>
			
        	<table id="porder_table" width="100%" cellpadding="0" cellspacing="0" class="stripe hover row-border order-column">
                <thead>
                    <tr>
                        <th width="105"><?=$_LANG->get('Beschreibung')?></th>
                        <th><?=$_LANG->get('Titel')?></th>
                        <th width="80"><?=$_LANG->get('Erstelldatum')?></th>
                        <th width="80"><?=$_LANG->get('Bestelldatum')?></th>
                        <th width="160"><?=$_LANG->get('Menge')?></th>
                        <th width="160"><?=$_LANG->get('Optionen')?></th>
                    </tr>
                </thead>
        	</table>
			<?php /*
			<table cellpadding="2" cellspacing="0" style="width:100%; border:0px;">
			    <colgroup>
			        <col>
			        <col width="100">
			        <col width="90">
			        <col width="80">
			        <col width="80">
			        <col width="100">
			    </colgroup>
			    <? if(count($all_persoorders) > 0  && $all_persoorders != false) {?>
			    <tr>
			        <td class="filerow_header"><?=$_LANG->get('Beschreibung');?></td>
			        <td class="filerow_header"><?=$_LANG->get('Titel');?></td>
			        <td class="filerow_header" align="center"><?=$_LANG->get('Erstelldatum');?></td>
			        <td class="filerow_header" align="center"><?=$_LANG->get('Bestelldatum');?></td>
			        <td class="filerow_header" align="right"><?=$_LANG->get('Menge');?></td>
			        <td class="filerow_header"><?=$_LANG->get('Optionen');?></td>
			    </tr>
			    <?foreach ($all_persoorders AS $perso_order){ 
			    	$perso = new Personalization($perso_order->getPersoID()); ?>
			    <tr class="filerow">
			        <td class="filerow"><?=$perso_order->getTitle()?></td>
			        <td class="filerow"><?=$perso->getTitle()?></td>
			        <td class="filerow" align="center">
			        	<?=date("d.m.Y",$perso_order->getCrtdate()) //  - H:i?>
			        </td>
			        <td class="filerow" align="center">
			        	<?if ($perso_order->getOrderdate() > 0) echo date("d.m.Y",$perso_order->getOrderdate()) // - H:i?>
			        </td>
			        <td class="filerow" align="right"><?=$perso_order->getAmount()?> Stk.</td>
			        <td class="filerow">
			        	<a href="index.php?pid=<?=$_REQUEST["pid"]?>&persoorderid=<?=$perso_order->getId()?>&exec=edit" class="button">Ansehen</a>
			        <? 	if($perso_order->getStatus() >= 1){ ?>
			        	&ensp;
			        	<a href="index.php?pid=<?=$_REQUEST["pid"]?>&deleteid=<?=$perso_order->getId()?>&exec=delete" 
			        		class="button" onclick="return confirm('<?=$_LANG->get('Personalisierung wirklich l&ouml;schen?') ?>')"
			        		style="border: solid 1px red; color: red;">X</a>
			        <?	} ?>
			        </td>
			    </tr>
			    <? $x++; } 
			    } else {
					echo '<tr><td class="filerow" colspan="4" align="center">'.$_LANG->get('Keine Personalisierungen aktiv').'</td></tr>';
			    }?>
			</table>
			*/ ?>
		</div>
		<? } ?>
		</td>
		<td>&ensp;</td>
		<td valign="top">
			<div class="box1"  style="min-height:600px;">
			<? // Warenkorb laden
				require_once 'kunden/modules/shoppingbasket/shopping_sidebar.php';?>
			</div>
		</td>
	</tr>
</table>
	