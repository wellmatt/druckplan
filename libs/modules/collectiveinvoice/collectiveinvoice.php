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
require_once('libs/modules/documents/document.class.php');
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/associations/association.class.php';

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
	
	$delete_invoice->delete();
	
	$savemsg = $msg = '<span class="ok">'.$del_title." ".$_LANG->get('wurde gelöscht').'</span>';
	// Hier könnte auch die globale Funktion angepasst werden, dass es spezifischeres Feedback gibt
	
// 	require_once('collectiveinvoice.overview.php');
    echo "<script language='JavaScript'>location.href='index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.overview.php'</script>";
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
	
	require_once('collectiveinvoice.edit.php');
	break;
case 'softdeletepos':
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
	
	$savemsg = getSaveMessage($delpos->deletesoft());
	
	require_once('collectiveinvoice.edit.php');
	break;
case 'restorepos':
	$delpos = new Orderposition((int)$_REQUEST['delpos']);
	
	if($delpos->getType() == 1){
		$tmp_order = new Order($delpos->getObjectid());
		$tmp_order->setCollectiveinvoiceId($delpos->getCollectiveinvoice());
		$tmp_order->save();
	}
	
	$savemsg = getSaveMessage($delpos->restore());
	
	require_once('collectiveinvoice.edit.php');
	break;
case 'save':
	
	$tmp_deliv = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["colinv_deliverycosts"])));

	$needs_planning = false;
	$collectinv->setClient($_USER->getClient());
	// $collectinv->setStatus((int)$_REQUEST["colinv_status"]);
	$collectinv->setBusinesscontact(new BusinessContact((int)$_REQUEST["colinv_businesscontact"]));
	$collectinv->setDeliveryterm(new DeliveryTerms((int)$_REQUEST["colinv_deliveryterm"]));
	$collectinv->setDeliverycosts($tmp_deliv);
	$collectinv->setPaymentterm(new PaymentTerms((int)$_REQUEST["colinv_paymentterm"]));
	$collectinv->setTitle(trim(addslashes($_REQUEST["colinv_title"])));
	$collectinv->setIntent(trim(addslashes($_REQUEST["colinv_intent"])));
	$collectinv->setComment(trim(addslashes($_REQUEST["colinv_comment"])));
	$collectinv->setExt_comment(trim(addslashes($_REQUEST["colinv_extcomment"])));
	$collectinv->setDeliveryaddress(new Address((int)$_REQUEST["colinv_deliveryadress"]));
	$collectinv->setInternContact(new User((int)$_REQUEST["intern_contactperson"]));
	$collectinv->setCustMessage(trim(addslashes($_REQUEST["cust_message"])));
	$collectinv->setCustSign(trim(addslashes($_REQUEST["cust_sign"])));
    $collectinv->setInvoiceAddress(new Address((int)$_REQUEST["invoice_address"]));
    $collectinv->setCustContactperson(new ContactPerson((int)$_REQUEST["custContactperson"]));
    $collectinv->setDeliverydate(strtotime($_REQUEST["colinv_deliverydate"]));
	
	$savemsg = getSaveMessage($collectinv->save());
	
	if ($_REQUEST["asso_class"] && $_REQUEST["asso_object"])
	{
	    $new_asso = new Association();
	    $new_asso->setModule1(get_class($collectinv));
	    $new_asso->setObjectid1((int)$collectinv->getId());
	    $new_asso->setModule2($_REQUEST["asso_class"]);
	    $new_asso->setObjectid2((int)$_REQUEST["asso_object"]);
	    $new_asso->save();
	}
	
	echo mysql_error();
	
	if($collectinv->getId()==NULL){
		$collectinv = CollectiveInvoice::getLastSavedCollectiveInvoice();
	}
	
	// Positionen speichern/ändern/erstellen
	$orderpositions = Array(); 
	$xi=0;
	$au_suffix=1;
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
			$newpos->setQuantity((float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["orderpos"][$xi]["quantity"]))));
			$newpos->setType((int)$_REQUEST["orderpos"][$xi]["type"]);
			$newpos->setInvrel((int)$_REQUEST["orderpos"][$xi]["inv_rel"]);
			$newpos->setRevrel((int)$_REQUEST["orderpos"][$xi]["rev_rel"]);
			$newpos->setObjectid((int)$_REQUEST["orderpos"][$xi]["obj_id"]); // Artikelnummer
			$newpos->setTax((int)$_REQUEST["orderpos"][$xi]["tax"]);
			$newpos->setCollectiveinvoice((int)$collectinv->getId());
			
			$tmp_art = new Article($newpos->getObjectid());
			if ($tmp_art->getIsWorkHourArt() || $tmp_art->getOrderid()>0)
			    $needs_planning = true;
			
			//AUftragsnummer anpassen (mit Suffix versehen)
			if ($newpos->getType() == 1){
				$tmp_order = new Order($newpos->getObjectid());
				$tmp_order->setCollectiveinvoiceId($collectinv->getId());
				$tmp_order->save();
				$au_suffix++;
			}
			$orderpositions[] = $newpos;
			$xi++;
		}
	}
	//Positionen der Rechnung speichern
// 	var_dump($orderpositions);
	Orderposition::saveMultipleOrderpositions($orderpositions);
    if ($needs_planning)
    {
        $collectinv->setNeeds_planning(1);
        $collectinv->save();
    }
	
	//echo "<br><br>".$DB->getLastError();
	
	require_once 'collectiveinvoice.edit.php';
	break;
case 'docs':
	require_once 'collectiveinvoice.documents.php';
	break;
case 'setState':
	$collectinv->setStatus((int)$_REQUEST["state"]);
	$collectinv->save();
	echo "<script language='JavaScript'>location.href='index.php?page=".$_REQUEST['page']."&ciid=".$collectinv->getId()."&exec=edit'</script>";
// 	require_once('collectiveinvoice.overview.php');
	break;
case 'setState2':
	$collectinv->setStatus((int)$_REQUEST["state"]);
	$collectinv->save();
// 	echo "<script language='JavaScript'>location.href='index.php?page=libs/modules/calculation/order.php'</script>";
	echo "<script language='JavaScript'>location.href='index.php?page=".$_REQUEST['page']."&ciid=".$collectinv->getId()."&exec=edit'</script>";
// 	require_once('collectiveinvoice.edit.php');
	break;
case 'createFromTicket':
    if ($_REQUEST["tktid"]){
        $src_ticket = new Ticket((int)$_REQUEST["tktid"]);

        $tmp_number = $collectinv->getClient()->createOrderNumber(1);
        
        $collectinv->setClient($_USER->getClient());
        
        $collectinv->setBusinesscontact($src_ticket->getCustomer());
        $collectinv->setNumber($tmp_number);
        $collectinv->setTitle("Ticket: " . $src_ticket->getNumber());
        $collectinv->setInternContact($_USER);
        $collectinv->setCustContactperson($src_ticket->getCustomer_cp());
        
        $savemsg = getSaveMessage($collectinv->save());
        
        echo mysql_error();
        
        if($collectinv->getId()==NULL){
            $collectinv = CollectiveInvoice::getLastSavedCollectiveInvoice();
        }
        
        // Positionen speichern/ändern/erstellen
        $orderpositions = Array();
        $art_array = Array();
        
        $all_comments = Comment::getCommentsForObject(get_class($src_ticket),$src_ticket->getId());
        
        foreach ($all_comments as $comment){
            if ($comment->getState() > 0 && count($comment->getArticles()) > 0){
                foreach ($comment->getArticles() as $c_article){
                    $tmp_art = $c_article->getArticle();
                    $newpos = new Orderposition();
                    $tmp_price = 0;
                    $tmp_price += $tmp_art->getPrice($c_article->getAmount());
                    $newpos->setPrice($tmp_price);
                    $newpos->setComment(strip_tags($comment->getComment()));
                    $newpos->setQuantity($c_article->getAmount());
                    $newpos->setType(Orderposition::TYPE_ARTICLE);
                    $newpos->setInvrel(1);
                    $newpos->setRevrel(1);
                    $newpos->setObjectid($c_article->getArticle()->getId()); // Artikelnummer
                    $newpos->setTax($c_article->getArticle()->getTax());
                    $newpos->setCollectiveinvoice((int)$collectinv->getId());
                    
                    $orderpositions[] = $newpos;
                    
                    $art_array[$c_article->getArticle()->getId()]["name"] = $c_article->getArticle()->getTitle();
                    $art_array[$c_article->getArticle()->getId()]["count"] += $c_article->getAmount();
                    $art_array[$c_article->getArticle()->getId()]["id"] = $c_article->getArticle()->getId();
                }
            }
        }
        
        if (count($art_array)>0){
            $sumpos = new Orderposition();
            
            $tmp_price = 0;
            $newpos->setPrice($tmp_price);
            
            $tmp_comment = "Zusammenfassung:\n";
            foreach ($art_array as $art){
		        $tmp_art = new Article((int)$art["id"]);
                $tmp_comment .= $art["count"] . "x " . $art["name"] . ": " . $tmp_art->getPrice($art["count"])*$art["count"] . "€\n";
            }
            
            $newpos->setComment($tmp_comment);
            $newpos->setQuantity(1);
            $newpos->setType(Orderposition::TYPE_MANUELL);
            $newpos->setInvrel(1);
            $newpos->setRevrel(0);
            $newpos->setObjectid(0); // Artikelnummer
            $newpos->setTax(0);
            $newpos->setCollectiveinvoice((int)$collectinv->getId());
            
            $orderpositions[] = $newpos;
        }
        
        //Positionen der Rechnung speichern
        Orderposition::saveMultipleOrderpositions($orderpositions);
        
        $association = new Association();
        $association->setModule1("Ticket");
        $association->setObjectid1($src_ticket->getId());
        $association->setModule2("CollectiveInvoice");
        $association->setObjectid2($collectinv->getId());
        $save_ok = $association->save();
        
//         echo "<br><br>".$DB->getLastError();
        
        require_once 'collectiveinvoice.edit.php';
    }
    break;
default:
	require_once('collectiveinvoice.overview.php');
}
?>
