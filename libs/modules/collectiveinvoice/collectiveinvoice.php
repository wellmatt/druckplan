<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */

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
global $_USER;

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

	$collectinv->setClient($_USER->getClient());
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
	if ($_REQUEST["thirdparty"] == 1) {
		$collectinv->setThirdparty(1);
		$collectinv->setThirdpartycomment($_REQUEST["thirdpartycomment"]);
	} else {
		$collectinv->setThirdparty(0);
		$collectinv->setThirdpartycomment('');
	}
	$collectinv->setDeliverydate(strtotime($_REQUEST["colinv_deliverydate"]));
	
	$savemsg = getSaveMessage($collectinv->save());
	
	if ($_REQUEST["asso_class"] != '' && $_REQUEST["asso_object"] != '')
	{
	    $new_asso = new Association();
	    $new_asso->setModule1(get_class($collectinv));
	    $new_asso->setObjectid1((int)$collectinv->getId());
	    $new_asso->setModule2($_REQUEST["asso_class"]);
	    $new_asso->setObjectid2((int)$_REQUEST["asso_object"]);
	    $new_asso->save();
		$_REQUEST["asso_class"] = '';
		$_REQUEST["asso_object"] = '';
	}
	
	if($collectinv->getId()==NULL){
		$collectinv = CollectiveInvoice::getLastSavedCollectiveInvoice();
	}

	if ($collectinv->getId() > 0){ // && ($collectinv->getStatus() == 5 || $collectinv->getStatus() == 7)
		$collectinv->saveArticleBuyPrices();
	}
	
	require_once 'collectiveinvoice.edit.php';
	break;
case 'docs':
	require_once 'collectiveinvoice.documents.php';
	break;
case 'notes':
	require_once 'collectiveinvoice.notes.php';
	break;
case 'setState':
	$collectinv->setStatus((int)$_REQUEST["state"]);
	$collectinv->save();
	echo "<script language='JavaScript'>location.href='index.php?page=".$_REQUEST['page']."&ciid=".$collectinv->getId()."&exec=edit'</script>";
	break;
case 'setState2':
	$collectinv->setStatus((int)$_REQUEST["state"]);
	$collectinv->save();
	echo "<script language='JavaScript'>location.href='index.php?page=".$_REQUEST['page']."&ciid=".$collectinv->getId()."&exec=edit'</script>";
	break;
case 'createFromTicket':
    if ($_REQUEST["tktid"]){
        $src_ticket = new Ticket((int)$_REQUEST["tktid"]);

        $tmp_number = $collectinv->getClient()->createOrderNumber(1);
        
        $collectinv->setClient($_USER->getClient());
        
        $collectinv->setBusinesscontact($src_ticket->getCustomer());
        $collectinv->setNumber($tmp_number);
        $collectinv->setTitle("Ticket: " . $src_ticket->getNumber() . " - " . $src_ticket->getTitle());
        $collectinv->setInternContact($_USER);
        $collectinv->setCustContactperson($src_ticket->getCustomer_cp());
		$collectinv->setTicket($src_ticket->getId());
		$collectinv->setPaymentterm($src_ticket->getCustomer()->getPaymentTerms());
		$collectinv->setInvoiceAddress(Address::getDefaultAddress($src_ticket->getCustomer(),Address::FILTER_INVC));
		$collectinv->setDeliveryaddress(Address::getDefaultAddress($src_ticket->getCustomer(),Address::FILTER_DELIV));
        
        $savemsg = getSaveMessage($collectinv->save());
        
        echo mysql_error();
        
        if($collectinv->getId()==NULL){
            $collectinv = CollectiveInvoice::getLastSavedCollectiveInvoice();
        }
        
        // Positionen speichern/ändern/erstellen
        $orderpositions = Array();
        $art_array = Array();
        
        $all_comments = Comment::getCommentsForObject(get_class($src_ticket),$src_ticket->getId());

		$sequ = Orderposition::getNextSequence($collectinv);
        foreach ($all_comments as $comment){
            if ($comment->getState() > 0){
				if (count($comment->getArticles()) > 0) {
					foreach ($comment->getArticles() as $c_article) {
						if ($c_article->getState() > 0) {
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
							$newpos->setTaxkey(TaxKey::evaluateTax($collectinv, $tmp_art));
							$newpos->setCollectiveinvoice((int)$collectinv->getId());
							$newpos->setSequence($sequ);
							$sequ++;

							$orderpositions[] = $newpos;

							$art_array[$c_article->getArticle()->getId()]["name"] = $c_article->getArticle()->getTitle();
							$art_array[$c_article->getArticle()->getId()]["count"] += $c_article->getAmount();
							$art_array[$c_article->getArticle()->getId()]["id"] = $c_article->getArticle()->getId();
						}
					}
				}
				$sub_comments = Comment::getCommentsForObject(get_class($comment),$comment->getId());
				if (count($sub_comments)>0){
					foreach ($sub_comments as $sub_comment) {
						if ($sub_comment->getState() > 0 && count($sub_comment->getArticles())>0){
							foreach ($sub_comment->getArticles() as $c_article) {
								$tmp_art = $c_article->getArticle();
								$newpos = new Orderposition();
								$tmp_price = 0;
								$tmp_price += $tmp_art->getPrice($c_article->getAmount());
								$newpos->setPrice($tmp_price);
								$newpos->setComment(strip_tags($sub_comment->getComment()));
								$newpos->setQuantity($c_article->getAmount());
								$newpos->setType(Orderposition::TYPE_ARTICLE);
								$newpos->setInvrel(1);
								$newpos->setRevrel(1);
								$newpos->setObjectid($c_article->getArticle()->getId()); // Artikelnummer
								$newpos->setTaxkey(TaxKey::evaluateTax($collectinv, $tmp_art));
								$newpos->setCollectiveinvoice((int)$collectinv->getId());
								$newpos->setSequence($sequ);
								$sequ++;

								$orderpositions[] = $newpos;

								$art_array[$c_article->getArticle()->getId()]["name"] = $c_article->getArticle()->getTitle();
								$art_array[$c_article->getArticle()->getId()]["count"] += $c_article->getAmount();
								$art_array[$c_article->getArticle()->getId()]["id"] = $c_article->getArticle()->getId();
							}
						}
					}
				}
            }
        }

        if (count($art_array)>0){
            $sumpos = new Orderposition();
            
            $tmp_price = 0;
			$sumpos->setPrice($tmp_price);
            
            $tmp_comment = "Zusammenfassung:<br>";
            foreach ($art_array as $art){
		        $tmp_art = new Article((int)$art["id"]);
                $tmp_comment .= printPrice($art["count"],2) . "x " . $art["name"] . ": " . printPrice($tmp_art->getPrice($art["count"])*$art["count"],2) . "€<br>";
            }

			$sumpos->setComment($tmp_comment);
			$sumpos->setQuantity(1);
			$sumpos->setType(Orderposition::TYPE_MANUELL);
			$sumpos->setInvrel(1);
			$sumpos->setRevrel(0);
			$sumpos->setObjectid(0); // Artikelnummer
			$sumpos->setTaxkey(TaxKey::evaluateTax($collectinv, new Article()));
			$sumpos->setCollectiveinvoice((int)$collectinv->getId());
			$sumpos->setSequence($sequ);
			$sequ++;
            
            $orderpositions[] = $sumpos;
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
	case 'updatefromticket':
		if ($collectinv->getTicket()>0){
			$src_ticket = new Ticket((int)$collectinv->getTicket());

			$collectinv->setBusinesscontact($src_ticket->getCustomer());
			$collectinv->setCustContactperson($src_ticket->getCustomer_cp());
			$savemsg = getSaveMessage($collectinv->save());

			// Positionen speichern/ändern/erstellen
			$orderpositions = Array();
			$art_array = Array();

			$all_comments = Comment::getCommentsForObject(get_class($src_ticket),$src_ticket->getId());

			$sequ = Orderposition::getNextSequence($collectinv);
			foreach ($all_comments as $comment){
				if ($comment->getState() > 0){
					if (count($comment->getArticles()) > 0) {
						foreach ($comment->getArticles() as $c_article) {
							if ($c_article->getState() > 0) {
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
								$newpos->setTaxkey(TaxKey::evaluateTax($collectinv, $tmp_art));
								$newpos->setCollectiveinvoice((int)$collectinv->getId());
								$newpos->setSequence($sequ);
								$sequ++;

								$orderpositions[] = $newpos;

								$art_array[$c_article->getArticle()->getId()]["name"] = $c_article->getArticle()->getTitle();
								$art_array[$c_article->getArticle()->getId()]["count"] += $c_article->getAmount();
								$art_array[$c_article->getArticle()->getId()]["id"] = $c_article->getArticle()->getId();
							}
						}
					}
					$sub_comments = Comment::getCommentsForObject(get_class($comment),$comment->getId());
					if (count($sub_comments)>0){
						foreach ($sub_comments as $sub_comment) {
							if ($sub_comment->getState() > 0 && count($sub_comment->getArticles())>0){
								foreach ($sub_comment->getArticles() as $c_article) {
									$tmp_art = $c_article->getArticle();
									$newpos = new Orderposition();
									$tmp_price = 0;
									$tmp_price += $tmp_art->getPrice($c_article->getAmount());
									$newpos->setPrice($tmp_price);
									$newpos->setComment(strip_tags($sub_comment->getComment()));
									$newpos->setQuantity($c_article->getAmount());
									$newpos->setType(Orderposition::TYPE_ARTICLE);
									$newpos->setInvrel(1);
									$newpos->setRevrel(1);
									$newpos->setObjectid($c_article->getArticle()->getId()); // Artikelnummer
									$newpos->setTaxkey(TaxKey::evaluateTax($collectinv, $tmp_art));
									$newpos->setCollectiveinvoice((int)$collectinv->getId());
									$newpos->setSequence($sequ);
									$sequ++;

									$orderpositions[] = $newpos;

									$art_array[$c_article->getArticle()->getId()]["name"] = $c_article->getArticle()->getTitle();
									$art_array[$c_article->getArticle()->getId()]["count"] += $c_article->getAmount();
									$art_array[$c_article->getArticle()->getId()]["id"] = $c_article->getArticle()->getId();
								}
							}
						}
					}
				}
			}

			if (count($art_array)>0){
				$sumpos = new Orderposition();

				$tmp_price = 0;
				$sumpos->setPrice($tmp_price);

				$tmp_comment = "Zusammenfassung:<br>";
				foreach ($art_array as $art){
					$tmp_art = new Article((int)$art["id"]);
					$tmp_comment .= printPrice($art["count"],2) . "x " . $art["name"] . ": " . printPrice($tmp_art->getPrice($art["count"])*$art["count"],2) . "€<br>";
				}

				$sumpos->setComment($tmp_comment);
				$sumpos->setQuantity(1);
				$sumpos->setType(Orderposition::TYPE_MANUELL);
				$sumpos->setInvrel(1);
				$sumpos->setRevrel(0);
				$sumpos->setObjectid(0); // Artikelnummer
				$newpos->setTaxkey(TaxKey::evaluateTax($collectinv, new Article()));
				$sumpos->setCollectiveinvoice((int)$collectinv->getId());
				$sumpos->setSequence($sequ);
				$sequ++;

				$orderpositions[] = $sumpos;
			}

			//Positionen der Rechnung speichern
			$old = Orderposition::getAllOrderposition($collectinv->getId(),true);
			foreach ($old as $item) {
				$item->delete();
			}

			Orderposition::saveMultipleOrderpositions($orderpositions);

			require_once 'collectiveinvoice.edit.php';
		}
		break;
case 'createFromTicketComments':
	if ($_REQUEST["tktid"] && $_REQUEST["tktcids"]){
		$src_ticket = new Ticket((int)$_REQUEST["tktid"]);
		$commentids = explode(",",$_REQUEST["tktcids"]);

		$tmp_number = $collectinv->getClient()->createOrderNumber(1);

		$collectinv->setClient($_USER->getClient());

		$collectinv->setBusinesscontact($src_ticket->getCustomer());
		$collectinv->setNumber($tmp_number);
		$collectinv->setTitle("Ticket: " . $src_ticket->getNumber() . " - " . $src_ticket->getTitle());
		$collectinv->setInternContact($_USER);
		$collectinv->setCustContactperson($src_ticket->getCustomer_cp());
		$collectinv->setTicket($src_ticket->getId());
		$collectinv->setPaymentterm($src_ticket->getCustomer()->getPaymentTerms());
		$collectinv->setInvoiceAddress(Address::getDefaultAddress($src_ticket->getCustomer(),Address::FILTER_INVC));
		$collectinv->setDeliveryaddress(Address::getDefaultAddress($src_ticket->getCustomer(),Address::FILTER_DELIV));

		$savemsg = getSaveMessage($collectinv->save());

		echo mysql_error();

		if($collectinv->getId()==NULL){
			$collectinv = CollectiveInvoice::getLastSavedCollectiveInvoice();
		}

		// Positionen speichern/ändern/erstellen
		$orderpositions = Array();
		$art_array = Array();

		$all_comments = Comment::getCommentsForObject(get_class($src_ticket),$src_ticket->getId());

		$sequ = Orderposition::getNextSequence($collectinv);
		foreach ($all_comments as $comment){
			if ($comment->getState() > 0){
				if (count($comment->getArticles()) > 0 && in_array($comment->getId(),$commentids)) {
					foreach ($comment->getArticles() as $c_article) {
						if ($c_article->getState() > 0) {
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
							$newpos->setTaxkey(TaxKey::evaluateTax($collectinv, $tmp_art));
							$newpos->setCollectiveinvoice((int)$collectinv->getId());
							$newpos->setSequence($sequ);
							$sequ++;

							$orderpositions[] = $newpos;

							$art_array[$c_article->getArticle()->getId()]["name"] = $c_article->getArticle()->getTitle();
							$art_array[$c_article->getArticle()->getId()]["count"] += $c_article->getAmount();
							$art_array[$c_article->getArticle()->getId()]["id"] = $c_article->getArticle()->getId();
						}
					}
				}
				$sub_comments = Comment::getCommentsForObject(get_class($comment),$comment->getId());
				if (count($sub_comments)>0){
					foreach ($sub_comments as $sub_comment) {
						if ($sub_comment->getState() > 0 && count($sub_comment->getArticles())>0 && in_array($comment->getId(),$commentids)){
							foreach ($sub_comment->getArticles() as $c_article) {
								$tmp_art = $c_article->getArticle();
								$newpos = new Orderposition();
								$tmp_price = 0;
								$tmp_price += $tmp_art->getPrice($c_article->getAmount());
								$newpos->setPrice($tmp_price);
								$newpos->setComment(strip_tags($sub_comment->getComment()));
								$newpos->setQuantity($c_article->getAmount());
								$newpos->setType(Orderposition::TYPE_ARTICLE);
								$newpos->setInvrel(1);
								$newpos->setRevrel(1);
								$newpos->setObjectid($c_article->getArticle()->getId()); // Artikelnummer
								$newpos->setTaxkey(TaxKey::evaluateTax($collectinv, $tmp_art));
								$newpos->setCollectiveinvoice((int)$collectinv->getId());
								$newpos->setSequence($sequ);
								$sequ++;

								$orderpositions[] = $newpos;

								$art_array[$c_article->getArticle()->getId()]["name"] = $c_article->getArticle()->getTitle();
								$art_array[$c_article->getArticle()->getId()]["count"] += $c_article->getAmount();
								$art_array[$c_article->getArticle()->getId()]["id"] = $c_article->getArticle()->getId();
							}
						}
					}
				}
			}
		}

		if (count($art_array)>0){
			$sumpos = new Orderposition();

			$tmp_price = 0;
			$sumpos->setPrice($tmp_price);

			$tmp_comment = "Zusammenfassung:<br>";
			foreach ($art_array as $art){
				$tmp_art = new Article((int)$art["id"]);
				$tmp_comment .= printPrice($art["count"],2) . "x " . $art["name"] . ": " . printPrice($tmp_art->getPrice($art["count"])*$art["count"],2) . "€<br>";
			}

			$sumpos->setComment($tmp_comment);
			$sumpos->setQuantity(1);
			$sumpos->setType(Orderposition::TYPE_MANUELL);
			$sumpos->setInvrel(1);
			$sumpos->setRevrel(0);
			$sumpos->setObjectid(0); // Artikelnummer
			$newpos->setTaxkey(TaxKey::evaluateTax($collectinv, new Article()));
			$sumpos->setCollectiveinvoice((int)$collectinv->getId());
			$sumpos->setSequence($sequ);
			$sequ++;

			$orderpositions[] = $sumpos;
		}

		//Positionen der Rechnung speichern
		Orderposition::saveMultipleOrderpositions($orderpositions);

		$association = new Association();
		$association->setModule1("Ticket");
		$association->setObjectid1($src_ticket->getId());
		$association->setModule2("CollectiveInvoice");
		$association->setObjectid2($collectinv->getId());
		$save_ok = $association->save();

		require_once 'collectiveinvoice.edit.php';
	}
	break;
case 'createNewRevert':
	require_once 'collectiveinvoice.newrevert.php';
	break;
default:
	require_once('collectiveinvoice.overview.php');
}
