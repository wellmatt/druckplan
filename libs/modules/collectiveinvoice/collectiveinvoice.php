<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       20.06.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('libs/modules/paymentterms/paymentterms.class.php');
require_once('libs/modules/deliveryterms/deliveryterms.class.php');
require_once('libs/modules/businesscontact/businesscontact.class.php');
require_once('libs/modules/businesscontact/address.class.php');
require_once('libs/modules/calculation/order.class.php');
require_once 'libs/modules/personalization/personalization.order.class.php';
require_once('collectiveinvoice.class.php');
require_once('orderposition.class.php');
require_once 'libs/modules/warehouse/warehouse.class.php';

global $_LANG;
global $_MENU;
$_USER;

$collectinv = new CollectiveInvoice((int)$_REQUEST['ciid']);
$ci = $collectiveinvoice;

switch($_REQUEST['exec']){
case 'edit':
	require_once('collectiveinvoice.edit.php');
	break;
case 'select_user':
	require_once('collectiveinvoice.select_cust.php');
	break;
case 'delete':
	$delete_invoice = new CollectiveInvoice((int)$_REQUEST["del_id"]);
	$del_title = $delete_invoice->getTitle(); 
	
	$del_positions = Orderposition::getAllOrderposition($delete_invoice->getId());
	foreach ($del_positions as $del_position){
	    if ($del_position->getType() == Orderposition::TYPE_ARTICLE){
	        Warehouse::addRemoveReservation($del_position->getObjectid(), 0-$del_position->getQuantity());
	    }
	}
	
	$delete_invoice->delete();
	
	$savemsg = $msg = '<span class="ok">'.$del_title." ".$_LANG->get('wurde gelöscht').'</span>';
	// Hier könnte auch die globale Funktion angepasst werden, dass es spezifischeres Feedback gibt
	
// 	require_once('collectiveinvoice.overview.php');
    echo "<script language='JavaScript'>location.href='index.php?page=libs/modules/calculation/order.php'</script>";
	break;
case 'deletepos':
	$delpos = new Orderposition((int)$_REQUEST['delpos']);
	
	//Beim L�schen von Aufträgen werden diese insgesammt gelöscht (also status=0 gesetzt)
	if($delpos->getType() == 1){
		$tmp_order = new Order($delpos->getObjectid());
		$tmp_order->setCollectiveinvoiceId(0);
		$tmp_order->save();
	}
	$tmp_del_amount = 0 - $delpos->getQuantity();
	$tmp_del_article = $delpos->getObjectid();
	$tmp_del_type = $delpos->getType();
	
	$savemsg = getSaveMessage($delpos->delete());
	if ($savemsg && $tmp_del_type == Orderposition::TYPE_ARTICLE){
	    Warehouse::addRemoveReservation($tmp_del_article, $tmp_del_amount);
	}
	
	require_once('collectiveinvoice.edit.php');
	break;
case 'save':
	//Number berechnen bei neuer Rechnung
	if($_REQUEST["ciid"] == NULL || $collectinv->getId() == 0 ){
		$tmp_number = $collectinv->getClient()->createOrderNumber(1);
	} else {					//wenn Bearbeiten oder Position hinzugef�gt, wieder �ffnen
		$tmp_number=$_REQUEST["colinv_number"];
	}
	
	$tmp_deliv = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["colinv_deliverycosts"])));
	
	$collectinv->setClient($_USER->getClient());
	// $collectinv->setStatus((int)$_REQUEST["colinv_status"]);
	$collectinv->setBusinesscontact(new BusinessContact((int)$_REQUEST["colinv_businesscontact"]));
	$collectinv->setDeliveryterm(new DeliveryTerms((int)$_REQUEST["colinv_deliveryterm"]));
	$collectinv->setDeliverycosts($tmp_deliv);
	$collectinv->setPaymentterm(new PaymentTerms((int)$_REQUEST["colinv_paymentterm"]));
	$collectinv->setNumber($tmp_number);
	$collectinv->setTitle(trim(addslashes($_REQUEST["colinv_title"])));
	$collectinv->setIntent(trim(addslashes($_REQUEST["colinv_intent"])));
	$collectinv->setComment(trim(addslashes($_REQUEST["colinv_comment"])));
	$collectinv->setDeliveryaddress(new Address((int)$_REQUEST["colinv_deliveryadress"]));
	$collectinv->setInternContact(new User((int)$_REQUEST["intern_contactperson"]));
	$collectinv->setCustMessage(trim(addslashes($_REQUEST["cust_message"])));
	$collectinv->setCustSign(trim(addslashes($_REQUEST["cust_sign"])));
    $collectinv->setInvoiceAddress(new Address((int)$_REQUEST["invoice_address"]));
	
	$savemsg = getSaveMessage($collectinv->save());
	
	echo mysql_error();
	
	if($collectinv->getId()==NULL){
		$collectinv = CollectiveInvoice::getLastSavedCollectiveInvoice();
	}
	
	// Positionen speichern/ändern/erstellen
	$orderpositions = Array(); 
	$xi=0;
	$au_suffix=1;
	//echo "<br/> <br/> <br/> <br/> <br/> <br/>---<br/>---";
	//var_dump($_REQUEST["orderpos"]);
	foreach ($_REQUEST["orderpos"] as $single_order){
		if ( !( $_REQUEST["orderpos"][$xi]["id"] == "0" && 			// Wenn in den "Neu"-Feldern nichts drin steht
				$_REQUEST["orderpos"][$xi]["comment"] == "" &&		// soll er auch nichts speichern
				$_REQUEST["orderpos"][$xi]["quantity"] == "")){
			
			if ($_REQUEST["orderpos"][$xi]["id"]==""){ 		// Fuer bestehende Orderpositionen
				$newpos = new Orderposition();
				$old_amount = 0;
			} else {											// Fuer neue Orderpositionen
				$newpos = new Orderposition((int)$_REQUEST["orderpos"][$xi]["id"]);
				$old_amount = $newpos->getQuantity();
			}
			//Daten passend konvertieren
			$tmp_oprice = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["orderpos"][$xi]["price"])));
			
			$newpos->setPrice($tmp_oprice);
			$newpos->setComment(trim(addslashes($_REQUEST["orderpos"][$xi]["comment"])));
			$newpos->setQuantity((int)$_REQUEST["orderpos"][$xi]["quantity"]);
			$newpos->setType((int)$_REQUEST["orderpos"][$xi]["type"]);
			$newpos->setInvrel((int)$_REQUEST["orderpos"][$xi]["inv_rel"]);
			$newpos->setRevrel((int)$_REQUEST["orderpos"][$xi]["rev_rel"]);
			$newpos->setObjectid((int)$_REQUEST["orderpos"][$xi]["obj_id"]);
			$newpos->setTax((int)$_REQUEST["orderpos"][$xi]["tax"]);
			$newpos->setCollectiveinvoice((int)$collectinv->getId());
			//AUftragsnummer anpassen (mit Suffix versehen)
			if ($newpos->getType() == 1){
				$tmp_order = new Order($newpos->getObjectid());
				$tmp_order->setCollectiveinvoiceId($collectinv->getId());
				$tmp_order->save();
				$au_suffix++;
			}
			if ($newpos->getType() == Orderposition::TYPE_ARTICLE){
			    if ($old_amount != $newpos->getQuantity()){
			        $reservation_amount = $newpos->getQuantity() - $old_amount;
			        Warehouse::addRemoveReservation($newpos->getObjectid(), $reservation_amount);
			    }
			}
			$orderpositions[] = $newpos;
			$xi++;
		}
	}
	//Positionen der Rechnung speichern
	Orderposition::saveMultipleOrderpositions($orderpositions);
	
	//echo "<br><br>".$DB->getLastError();
	
	require_once 'collectiveinvoice.edit.php';
	break;
case 'docs':
	require_once 'collectiveinvoice.documents.php';
	break;
case 'setState':
	$collectinv->setStatus((int)$_REQUEST["state"]);
	$collectinv->save();
	require_once('collectiveinvoice.overview.php');
	break;
case 'setState2':
	$collectinv->setStatus((int)$_REQUEST["state"]);
	$collectinv->save();
	require_once('collectiveinvoice.edit.php');
	break;
default:
	require_once('collectiveinvoice.overview.php');
}
?>