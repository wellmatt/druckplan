<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			29.08.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';
require_once 'libs/modules/collectiveinvoice/orderposition.class.php';
require_once 'libs/modules/personalization/personalization.order.class.php';
require_once 'shoppingbasketitem.class.php';
require_once 'libs/modules/businesscontact/address.class.php';
require_once 'libs/modules/warehouse/warehouse.class.php';
require_once 'libs/modules/businesscontact/contactperson.class.php';
require_once 'libs/modules/notifications/notification.class.php';
require_once 'libs/modules/attachment/attachment.class.php';

class Shoppingbasket{
	
	const STATUS_BUY = "buying"; 	// Wenn noch eingekauft wird
	const STATUS_WAIT = "waiting";	// Eingekauf fertig, aber noch nicht entgueltig bestaetigt/abgeschickt
	const STATUS_SEND = "send";		// Warekorb ist gefuellt und bestaetigt
	
	private $id;
	private $customer; 				// Eingelogter Kunde/Geschaeftskontakt 
	private $totalprice;			// Gesamtpreis des ganzen Warenkorbs	
	private $status = "buying";		// Status des Warenkorbs
	private $entrys = Array();		// Eintraege im Warenkorb
	private $intent;				// Zweck / Kostenstelle / ...
	private $note;
	private $deliveryAdressID;		// Lieferadresse
	
	/**
	 * Konstruktor fuer einen Warekorb
	 * 
	 * @param int $id
	 */
	function __construct($id=0){
		if ($id>0){
			/* 
			 * Wird spaeter mal gebraucht, wenn ein Warenkorb zwischen gespeichert werden soll !
			 * 			 
			$this->id ........ */
		}
	}
	
	/**
	 * Fuegt einen Eintrag zum Warenkorb hinzu
	 * 
	 * @param ShoppingBasketItem $item
	 */
	public function addItem($item){
		$this->entrys[] = $item;
	}
	
	/**
	 * Leert alle Eintraege eines Warenkorbs
	 */
	public function clear(){
		$this->entrys = Array();
	}
	
	/**
	 * Loescht einen Eintrag aus dem Warenkorb und liefert das geloeschte Item zurueck
	 *
	 * @param int $id
	 * @param int $type
	 * @return Shopppingbasketitem 
	 */
	public function deleteItem($id, $type){
		$newitemlist = Array();
		$ret_item = NULL;
		foreach($this->entrys as $entry){
			if (($entry->getId()==$id) && ($entry->getType()==$type)){
				$ret_item = $entry;
			} else {
				$newitemlist[] = $entry;
			}
		}
		$this->entrys = $newitemlist;
		return $ret_item;
	}

	/**
	 * Loescht einen Eintrag aus dem Warenkorb und liefert das geloeschte Item zurueck
	 *
	 * @param int $id
	 * @param int $type
	 * @return Shopppingbasketitem
	 */
	public function deleteItemByEntryId($entryid){
	    $newitemlist = Array();
	    $ret_item = NULL;
	    foreach($this->entrys as $entry){
	        if (($entry->getEntryid()==$entryid)){
	            $ret_item = $entry;
	        } else {
	            $newitemlist[] = $entry;
	        }
	    }
	    $this->entrys = $newitemlist;
	    return $ret_item;
	}
	
	/**
	 * Ueberpruefen, ob ein Eintrag schon im Warenkorb ist, oder nicht
	 * 
	 * @param ShoppingBasketItem $item
	 * @return boolean
	 */
	public function itemExists($item){
		foreach($this->entrys as $entry){
			if (($entry->getId() == $item->getId()) && ($entry->getType() == $item->getType()) && ($entry->getDeliveryAdressID() == $item->getDeliveryAdressID())){
				return true;
			}
		}
		return false;
	}
	
// 	/**
// 	 * Funktion fuer das Absenden eines Warenkorbs. 
// 	 * Es wird eine Sammelrechnung erzeugt und alle Warenkorbeinträge in Auftragspositionen uebernommen.
// 	 * @return boolean
// 	 */
// 	public function send(){
// 		global $busicon;
// 		global $_LANG;
// 		$save_items = Array();
		
// 		$col_inv = new CollectiveInvoice();
// 		$col_inv->setBusinesscontact($busicon);
// 		$col_inv->setIntent($this->intent);
// 		$col_inv->setTitle($_LANG->get("Bestellung aus dem Kunden-Portal"));
// 		$col_inv->setPaymentterm($busicon->getPaymentTerms());
// 		$col_inv->setDeliveryaddressById($this->deliveryAdressID);
// 		$tmp_saver = $col_inv->save();
		
// 		// Wenn Sammelrechnung gespeichert/angelegt, dann Positionen hinzufuegen
// 		if($tmp_saver){
// 			foreach ($this->entrys AS $entry){
// 				$tmp_order_pos =  new Orderposition();
// 				$tmp_order_pos->setPrice($entry->getPrice());
// 				$tmp_order_pos->setObjectid($entry->getId());
// 				$tmp_order_pos->setCollectiveinvoice($col_inv->getId());
// 				$tmp_order_pos->setStatus(1);
				
// 				if($entry->getType() == Shoppingbasketitem::TYPE_ARTICLE){
// 					$tmp_article = new Article($entry->getId());
// 					$tmp_order_pos->setType(Orderposition::TYPE_ARTICLE);
// 					$tax = $tmp_article->getTax();
// 					$tmp_order_pos->setTax($tax);
// 					$tmp_order_pos->setComment($tmp_article->getDesc());
// 					$tmp_order_pos->setQuantity($entry->getAmount());
// 				}
// 				if($entry->getType() == Shoppingbasketitem::TYPE_PERSONALIZATION){
// 					$tmp_perso = new Personalizationorder($entry->getId());
// 					$tmp_order_pos->setType(Orderposition::TYPE_PERSONALIZATION);
// 					$tmp_order_pos->setTax(CollectiveInvoice::TAX_PEROSALIZATION);
// 					$tmp_order_pos->setComment($tmp_perso->getTitle()." (".$entry->getAmount()." ".$_LANG->get("Stk.").")");
// 					$tmp_order_pos->setQuantity(1);
// 					// Bestellung aktualisieren, damit sie im Backend auftaucht
// 					// $tmp_perso->setStatus(2);	// nicht mehr den Status umsetzen, damit diese als Vorlage bleibt
// 					$tmp_perso->setOrderdate(time());
// 					$tmp_perso->save();
// 					$tmp_perso->copyPersoOrderForShopOrder();
// 				} 
// 				$save_items[] = $tmp_order_pos;
// 			}
// 			$tmp_saver2 = Orderposition::saveMultipleOrderpositions($save_items);
// 			// echo "Debug $tmp_saver2: "; 
// 			// echo $tmp_saver2 ? 'true' : 'false';
// 			// echo "</br>";
// 		}
// 		return 	(bool)($tmp_saver && $tmp_saver2);
// 	}

	/**
	 * Funktion fuer das Absenden eines Warenkorbs.
	 * Es wird eine Sammelrechnung erzeugt und alle Warenkorbeinträge in Auftragspositionen uebernommen.
	 * @return boolean
	 */
	public function send(){
	    global $busicon;
	    global $_LANG;
	    $inv_ad = Array();
	    $deli_ad = Array();
	
	    foreach ($this->entrys AS $entry){
	        if (!in_array($entry->getInvoiceAdressID(), $inv_ad)) {
	           $inv_ad[] = $entry->getInvoiceAdressID();
	        }
	    }
	    
	    foreach ($inv_ad as $address) {
	        $save_items = Array();
	        $tmp_inv_ad = new Address($address);
	        
	        $col_inv = new CollectiveInvoice();
	        $col_inv->setBusinesscontact($busicon);
	        $col_inv->setIntent($this->intent);
	        $col_inv->setExt_comment($this->note);
	        $col_inv->setTitle($_LANG->get("Bestellung aus dem Kunden-Portal"));
	        $col_inv->setPaymentterm($busicon->getPaymentTerms());
	        $col_inv->setInvoiceAddress($tmp_inv_ad);
	        $col_inv->setClient(new Client(1));
			$col_inv->setType(2);
	        
	        if ($_SESSION["login_type"]	== "contactperson"){
	            $col_inv->setCustContactperson(new ContactPerson((int)$_SESSION["contactperson_id"]));
	        }
	        
	        $tmp_saver = $col_inv->save();
	        if ($tmp_saver == false) {
	            return false;
	        }
	        
	        $colid = $col_inv->getId();
	        Notification::generateNotification($busicon->getSupervisor(), "CollectiveInvoice", "NewOrderShop", $busicon->getNameAsLine(), $colid);
	        
	        // Wenn Sammelrechnung gespeichert/angelegt, dann Positionen hinzufuegen
	        if($tmp_saver){
	            $deli_ad = Array();
	            foreach ($this->entrys AS $entry){
	                if (!in_array($entry->getDeliveryAdressID(), $deli_ad) && $entry->getInvoiceAdressID() == $address) {
	                    $deli_ad[] = $entry->getDeliveryAdressID();
	                }
	            }
	            
	            foreach ($deli_ad as $deli_address) {
                    
	                $tmp_deli_ad = new Address($deli_address);
	                
	                $tmp_deli_pos =  new Orderposition();
	                $tmp_deli_pos->setCollectiveinvoice($col_inv->getId());
	                $tmp_deli_pos->setStatus(1);
    	            $tmp_deli_pos->setType(Orderposition::TYPE_MANUELL);
    	            $tmp_deli_pos->setQuantity(1);
    	            $tmp_deli_pos->setObjectid(0);
    	            $tmp_deli_pos->setComment("Lieferadresse:\n".$tmp_deli_ad->getNameAsLine()."\n".$tmp_deli_ad->getAddressAsLine()."\n
    	                                       Rechnungsadresse:\n".$tmp_inv_ad->getNameAsLine()."\n".$tmp_inv_ad->getAddressAsLine());
    	            $save_items[] = $tmp_deli_pos;
    	            
	                
	                
    	            foreach ($this->entrys AS $entry){
    	                if ($entry->getDeliveryAdressID() == $deli_address && $entry->getInvoiceAdressID() == $address) {
        	                $tmp_order_pos =  new Orderposition();
        	                $tmp_order_pos->setPrice($entry->getPrice());
        	                $tmp_order_pos->setObjectid($entry->getId());
        	                $tmp_order_pos->setCollectiveinvoice($col_inv->getId());
        	                $tmp_order_pos->setStatus(1);
        	        
        	                if($entry->getType() == Shoppingbasketitem::TYPE_ARTICLE){
        	                    $tmp_article = new Article($entry->getId());

								if ($tmp_article->getIsWorkHourArt() || $tmp_article->getOrderid()>0)
									$needs_planning = true;

        	                    if ($tmp_article->getOrderid()>0)
        	                        $tmp_order_pos->setType(Orderposition::TYPE_ORDER);
        	                    else 
        	                        $tmp_order_pos->setType(Orderposition::TYPE_ARTICLE);
        	                    $tax = $tmp_article->getTax();
        	                    $tmp_order_pos->setTax($tax);
        	                    $tmp_order_pos->setComment($tmp_article->getDesc());
        	                    $tmp_order_pos->setQuantity($entry->getAmount());
        	                    if ($tmp_article->getShop_needs_upload()==1 && (int)$entry->getFile()>0)
        	                    {
        	                        $tmp_attach = new Attachment((int)$entry->getFile());
        	                        $tmp_order_pos->setFile_attach($tmp_attach->getId());
        	                    }
        	                }
        	                if($entry->getType() == Shoppingbasketitem::TYPE_PERSONALIZATION){
        	                    $tmp_perso_order = new Personalizationorder($entry->getId());
        	                    $tmp_perso = new Personalization($tmp_perso_order->getPersoID());
        	                    
        	                    $tmp_article = $tmp_perso->getArticle();
        	                    $tmp_order_pos->setObjectid($tmp_article->getId());
        	                    
        	                    $tmp_order_pos->setType(Orderposition::TYPE_ARTICLE);
        	                    $tmp_order_pos->setTax(CollectiveInvoice::TAX_PEROSALIZATION);
        	                    $tmp_order_pos->setComment($tmp_perso_order->getTitle());
        	                    $tmp_order_pos->setQuantity($entry->getAmount());
        	                    $tmp_order_pos->setPerso_order($tmp_perso_order->getId());
        	                    $tmp_order_pos->setPrice($entry->getPrice()/$entry->getAmount());
        	                    
        	                    // Bestellung aktualisieren, damit sie im Backend auftaucht
        	                    // $tmp_perso->setStatus(2);	// nicht mehr den Status umsetzen, damit diese als Vorlage bleibt
        	                    $tmp_perso_order->setOrderdate(time());
        	                    $tmp_perso_order->save();
        	                    $tmp_perso_order->copyPersoOrderForShopOrder();
        	                }

        	                $save_items[] = $tmp_order_pos;
    	                }
    	            }
	            }
	            $tmp_saver2 = Orderposition::saveMultipleOrderpositions($save_items);
				if ($needs_planning)
				{
					$col_inv->setNeeds_planning(1);
					$col_inv->save();
				}
	        }
	    }
	    
	    
	    return 	true;
	}

	public function getId()
	{
	    return $this->id;
	}

	public function getCustomer()
	{
	    return $this->customer;
	}

	public function setCustomer($customer)
	{
	    $this->customer = $customer;
	}

	public function getTotalprice()
	{
	    return $this->totalprice;
	}

	public function setTotalprice($totalprice)
	{
	    $this->totalprice = $totalprice;
	}

	public function getStatus()
	{
	    return $this->status;
	}

	public function setStatus($status)
	{
	    $this->status = $status;
	}

	public function getEntrys()
	{
	    return $this->entrys;
	}

	public function setEntrys($entrys)
	{
	    $this->entrys = $entrys;
	}

    public function getIntent()
    {
        return $this->intent;
    }

    public function setIntent($intent)
    {
        $this->intent = $intent;
    }

    public function getDeliveryAdressID()
    {
        return $this->deliveryAdressID;
    }

    public function setDeliveryAdressID($deliveryAdressID)
    {
        $this->deliveryAdressID = $deliveryAdressID;
    }
    
	/**
     * @return the $note
     */
    public function getNote()
    {
        return $this->note;
    }

	/**
     * @param field_type $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }
    
    
}
?>