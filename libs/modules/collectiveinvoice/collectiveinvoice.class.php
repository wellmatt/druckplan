<?//--------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       19.09.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('libs/basic/user/user.class.php');
require_once('libs/modules/paymentterms/paymentterms.class.php');
require_once('libs/modules/deliveryterms/deliveryterms.class.php');
require_once('libs/modules/businesscontact/businesscontact.class.php');
require_once('libs/modules/businesscontact/address.class.php');
require_once 'libs/modules/collectiveinvoice/orderposition.class.php';
require_once 'libs/modules/associations/association.class.php';

global $_USER;

class CollectiveInvoice{
	
	const ORDER_NUMBER = "number";
	const ORDER_CRTDATE			= " crtdate ";
	const ORDER_CRTDATE_DESC	= " crtdate desc";
	
	const TAX_ARTICLE			= 19;
	const TAX_PEROSALIZATION	= 19;

	private $id = 0;
	private $status	= 1;
	private $type = 1;					// Vorgangstyp 1: Manuell 2: Bestellung aus Kundenportal
	private $title = "";
	private $number = "- - -";
	private $crtdate;
	private $crtuser;
	private $uptdate;
	private $uptuser;
	private $deliverycosts = 0;
	private $comment = "";				// interner Kommentar
	private $ext_comment = "";			// externer Kommentar
	private $businesscontact = 0;		// zugehoeriger Geschaeftskontakt
    private $custContactperson;			// Anspr. des Kunden
	private $client = 0;
	private $deliveryterm = 0;
	private $paymentterm = 0;
	private $deliveryaddress = 0;
    private $invoiceAddress;
	private $intent;					// Kostenstelle, Zweck, ... wird vom Kunden bei einer Bestellung angegeben (fuer Rechnung)
	
    private $internContact;				// Benutzer von KDM, der auf den Dokumenten auftauchen soll
    private $custMessage;				// Auf den Dokumenten "Ihre Nachricht"
    private $custSign;					// Auf den Dokumenten "Ihr Zeichen"
    
    private $needs_planning = 0;
    private $deliverydate = 0;
	private $rdyfordispatch = 0;		// Ware bereit zur Lieferung
	private $thirdparty = 0;			// Fremdleistung ja/nein
	private $thirdpartycomment = "";	// Hinweis zur Fremdleistung

	private $ticket = 0;				// TicketID falls durch ticket erstellt
	private $savedcost = 0;				// Einkaufspreise und Profit gesetzt
    
    // Doc texts

    
    private $offer_header;
    private $offer_footer;
    private $offerconfirm_header;
    private $offerconfirm_footer;
    private $factory_header;
    private $factory_footer;
    private $delivery_header;
    private $delivery_footer;
    private $invoice_header;
    private $invoice_footer;
    private $revert_header;
    private $revert_footer;

	/**
	 * Konstruktor fuer die Sammelrechnungen
	 * 
	 * @param int $id
	 */
	function __construct($id = 0){
		global $DB;
		global $_USER;
		
		$this->resetDocTexts();
		$this->businesscontact = new BusinessContact();
		$this->client = new Client();
		$this->crtuser = new User();
		$this->uptuser = new User();
		$this->paymentterm = new PaymentTerms();
		$this->deliveryterm = new DeliveryTerms();
		$this->deliveryaddress = new Address();
        $this->invoiceAddress = new Address();
        $this->internContact = new User();
        $this->custContactperson = new ContactPerson();
		
		if($id>0){
			$valid_cache = true;
			if (Cachehandler::exists(Cachehandler::genKeyword($this,$id))){
				$cached = Cachehandler::fromCache(Cachehandler::genKeyword($this,$id));
				if (get_class($cached) == get_class($this)){
					$vars = array_keys(get_class_vars(get_class($this)));
					foreach ($vars as $var)
					{
						$method = "get".ucfirst($var);
						$method2 = $method;
						$method = str_replace("_", "", $method);
						if (method_exists($this,$method))
						{
							if(is_object($cached->$method()) === false) {
								$this->$var = $cached->$method();
							} else {
								$class = get_class($cached->$method());
								$this->$var = new $class($cached->$method()->getId());
							}
						} elseif (method_exists($this,$method2)){
							if(is_object($cached->$method2()) === false) {
								$this->$var = $cached->$method2();
							} else {
								$class = get_class($cached->$method2());
								$this->$var = new $class($cached->$method2()->getId());
							}
						} else {
							prettyPrint('Cache Error: Method "'.$method.'" not found in Class "'.get_called_class().'"');
							$valid_cache = false;
						}
					}
				} else {
					$valid_cache = false;
				}
			} else {
				$valid_cache = false;
			}
			if ($valid_cache === false) {
				$sql = "SELECT * FROM collectiveinvoice WHERE id = " . $id;
				if ($DB->num_rows($sql)) {
					$rows = $DB->select($sql);
					$r = $rows[0];
					$this->id = (int)$r["id"];
					$this->status = $r["status"];
					$this->type = $r["type"];
					$this->title = $r["title"];
					$this->number = $r["number"];
					$this->comment = $r["comment"];
					$this->crtdate = $r["crtdate"];
					$this->uptdate = $r["uptdate"];
					$this->uptuser = new User((int)$r["uptuser"]);
					$this->crtuser = new User((int)$r["crtuser"]);
					$this->deliverycosts = $r["deliverycosts"];
					$this->client = new Client((int)$r["client"]);
					$this->businesscontact = new BusinessContact((int)$r["businesscontact"]);
					$this->deliveryterm = new DeliveryTerms((int)$r["deliveryterm"]);
					$this->paymentterm = new PaymentTerms((int)$r["paymentterm"]);
					$this->deliveryaddress = new Address($r["deliveryaddress"]);
					$this->invoiceAddress = new Address($r["invoiceaddress"]);
					$this->intent = $r["intent"];
					$this->internContact = new User($r["intern_contactperson"]);
					$this->custMessage = $r["cust_message"];
					$this->custSign = $r["cust_sign"];
					$this->custContactperson = new ContactPerson($r["custContactperson"]);
					$this->needs_planning = $r["needs_planning"];
					$this->deliverydate = $r["deliverydate"];
					$this->ext_comment = $r["ext_comment"];
					$this->rdyfordispatch = $r["rdyfordispatch"];
					$this->thirdparty = $r["thirdparty"];
					$this->thirdpartycomment = $r["thirdpartycomment"];
					$this->ticket = $r["ticket"];
					$this->savedcost = $r["savedcost"];

					// doc texts
					$this->offer_header = $r["offer_header"];
					$this->offer_footer = $r["offer_footer"];
					$this->offerconfirm_header = $r["offerconfirm_header"];
					$this->offerconfirm_footer = $r["offerconfirm_footer"];
					$this->factory_header = $r["factory_header"];
					$this->factory_footer = $r["factory_footer"];
					$this->delivery_header = $r["delivery_header"];
					$this->delivery_footer = $r["delivery_footer"];
					$this->invoice_header = $r["invoice_header"];
					$this->invoice_footer = $r["invoice_footer"];
					$this->revert_header = $r["revert_header"];
					$this->revert_footer = $r["revert_footer"];

					Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
				}
			}
		}
	}//Ende vom Konstruktor

	/**
	 * Speichert die aufrufende manuelle Rechnung
	 * 
	 * @return boolean
	 */
	function save(){
		global $DB;
		global $_USER;
		$now = time();
		if($this->id > 0){	//number, crtdate, crtuser und id d�rfen/sollen nicht ver�ndert werden // number = '{$this->number}',
			$sql = "UPDATE collectiveinvoice SET
					status = {$this->status},
					type = {$this->type},
					title = '{$this->title}',
					comment = '{$this->comment}',
					ext_comment = '{$this->ext_comment}',
					uptuser = {$_USER->getId()},
					uptdate = {$now},
					deliverycosts = {$this->deliverycosts},
					businesscontact = {$this->businesscontact->getId()},
					client = {$this->client->getId()},
					deliveryterm = {$this->deliveryterm->getId()},
					paymentterm = {$this->paymentterm->getId()}, 
					deliveryaddress = {$this->deliveryaddress->getId()},
					invoiceaddress = {$this->invoiceAddress->getId()},
                    intern_contactperson = {$this->internContact->getId()},
                    cust_message = '{$this->custMessage}',  
                    cust_sign = '{$this->custSign}', 
                    needs_planning = {$this->needs_planning}, 
                    deliverydate = {$this->deliverydate}, 
                    custContactperson = {$this->custContactperson->getId()},
                    rdyfordispatch = {$this->rdyfordispatch},
                    thirdparty = {$this->thirdparty},
                    thirdpartycomment = '{$this->thirdpartycomment}',
                    offer_header = '{$this->offer_header}',
                    offer_footer = '{$this->offer_footer}',  
                    offerconfirm_header = '{$this->offerconfirm_header}',  
                    offerconfirm_footer = '{$this->offerconfirm_footer}',  
                    factory_header = '{$this->factory_header}',  
                    factory_footer = '{$this->factory_footer}',  
                    delivery_header = '{$this->delivery_header}',  
                    delivery_footer = '{$this->delivery_footer}',  
                    invoice_header = '{$this->invoice_header}',  
                    invoice_footer = '{$this->invoice_footer}',  
                    revert_header = '{$this->revert_header}',  
                    revert_footer = '{$this->revert_footer}',
                    ticket = {$this->ticket},
                    savedcost = {$this->savedcost},

					intent = '{$this->intent}'
					WHERE id = {$this->id}";
			$res = $DB->no_result($sql);
		} else {
			if ($this->number == "- - -" || $this->number == null)
				$this->number = $this->getClient()->createOrderNumber(Client::NUMBER_COLINV);
			$this->crtdate = $now;
			$this->crtuser = $_USER;
			$sql = "INSERT INTO collectiveinvoice
				(status, type, title, number, crtdate, crtuser,
				 deliverycosts, comment, businesscontact, client,
				 deliveryterm, paymentterm, deliveryaddress, invoiceaddress,
				 intern_contactperson, cust_message, cust_sign, custContactperson,
				 intent, needs_planning, deliverydate, ext_comment, rdyfordispatch,
				 offer_header, offer_footer, offerconfirm_header, offerconfirm_footer,
				 factory_header, factory_footer, delivery_header, delivery_footer,
				 invoice_header, invoice_footer, revert_header, revert_footer,thirdparty,thirdpartycomment,ticket,savedcost)
			VALUES
				({$this->status}, {$this->type},'{$this->title}', '{$this->number}', {$now}, {$_USER->getId()},
				 {$this->deliverycosts}, '{$this->comment}', {$this->businesscontact->getId()}, {$this->client->getId()},
				 {$this->deliveryterm->getId()}, {$this->paymentterm->getId()}, {$this->deliveryaddress->getId()}, {$this->invoiceAddress->getId()},
				 {$this->internContact->getId()}, '{$this->custMessage}', '{$this->custSign}', {$this->custContactperson->getId()},
				 '{$this->intent}', {$this->needs_planning}, {$this->deliverydate}, '{$this->ext_comment}', {$this->rdyfordispatch},
				 '{$this->offer_header}','{$this->offer_footer}','{$this->offerconfirm_header}','{$this->offerconfirm_footer}',
				 '{$this->factory_header}','{$this->factory_footer}','{$this->delivery_header}','{$this->delivery_footer}',
				 '{$this->invoice_header}','{$this->invoice_footer}','{$this->revert_header}','{$this->revert_footer}',
				 {$this->thirdparty},'{$this->thirdpartycomment}', {$this->ticket}, 0)";
			$res = $DB->no_result($sql);
			if($res){
				$sql = "SELECT max(id) id FROM collectiveinvoice WHERE status > 0 ";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["id"];
			}
		}
		if ($res)
		{
			Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
			if ($this->status == 5 || $this->status == 7){
				$this->saveArticleBuyPrices();
			}
			return true;
		}
		else
			return false;
	}

	/**
	 * L�scht die aufrufende Sammelrechnung nicht wirklich, 
	 * sondern setzt des Status auf 0 und l�scht die Auftragsnummer
	 * 
	 * @return boolean
	 */
	public function delete(){
		global $DB;
		if($this->id > 0){
			$sql = "UPDATE collectiveinvoice SET status = 0 WHERE id = {$this->id}";
			if($DB->no_result($sql)){
				Cachehandler::removeCache(Cachehandler::genKeyword($this));
                Notification::removeForObject("CollectiveInvoice", $this->getId());
				unset($this);
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * Saves article cost and profit margin to database
	 */
	private function saveArticleBuyPrices(){
		if ($this->savedcost == 0) {
			$orderpositions = Orderposition::getAllOrderposition($this->getId());
			foreach ($orderpositions as $orderposition) {
				if ($orderposition->getType() == 1 || $orderposition->getType() == 2) {
					$article = new Article((int)$orderposition->getObjectid());
					$cost = $orderposition->getAmount() * PriceScale::getPriceForAmount($article, $orderposition->getAmount(), PriceScale::TYPE_BUY);
					$profit = ($orderposition->getAmount() * $orderposition->getPrice()) - $cost;
					$orderposition->setCost($cost);
					$orderposition->setProfit($profit);
				}
			}
			Orderposition::saveMultipleOrderpositions($orderpositions);
			$this->savedcost = 1;
			$this->save();
		}
	}


	/**
	 * Returns total profit margin for this colinv
	 * @return float|int
	 */
	public function getMyProfit()
	{
		if ($this->savedcost == 1) {
			$profit = 0.0;
			$orderpositions = Orderposition::getAllOrderposition($this->getId());
			foreach ($orderpositions as $orderposition) {
				$profit += $orderposition->getProfit();
			}
			return $profit;
		} else {
			return 0.0;
		}
	}

	
	static function getAllCustomerWithColInvs(){
	    global $DB;
	    $retval = Array();
	    $sql = "SELECT DISTINCT collectiveinvoice.businesscontact FROM collectiveinvoice WHERE status > 0";
	    if($DB->num_rows($sql)){
	        foreach($DB->select($sql) as $r){
	            $retval[] = new BusinessContact($r["businesscontact"]);
	        }
	    }
	    return $retval;
	}
	
	public static function combineColInvs(Array $ids){
		global $_USER;
	    $maininv = new CollectiveInvoice($ids[0]);
		$needs_planning = false;
	    
	    $newinv = new CollectiveInvoice();
	    $newinv->setNumber($maininv->getClient()->createOrderNumber(1));
	    $newinv->setClient($_USER->getClient());
	    $newinv->setBusinesscontact($maininv->getBusinesscontact());
	    $newinv->setDeliveryterm($maininv->getDeliveryterm());
	    $newinv->setDeliverycosts($maininv->getDeliverycosts());
	    $newinv->setPaymentterm($maininv->getPaymentterm());
	    $newinv->setTitle("K: " . $maininv->getTitle());
	    $newinv->setIntent($maininv->getIntent());
	    $newinv->setComment($maininv->getComment());
	    $newinv->setExt_comment($maininv->getExt_comment());
	    $newinv->setDeliveryaddress($maininv->getDeliveryaddress());
	    $newinv->setInternContact($maininv->getInternContact());
	    $newinv->setCustMessage($maininv->getCustMessage());
	    $newinv->setCustSign($maininv->getCustSign());
	    $newinv->setInvoiceAddress($maininv->getInvoiceAddress());
	    $newinv->setCustContactperson($maininv->getCustContactperson());
	    $newinv->setDeliverydate($maininv->getDeliverydate());
	    
	    $savemsg = getSaveMessage($newinv->save());
	    
	    if ($savemsg){
	       $collectinv = CollectiveInvoice::getLastSavedCollectiveInvoice();
	    

	       $orderpositions = Array();
	       $xi=0;
	       foreach ($ids as $id){
	           $tmp_colinv = new CollectiveInvoice($id);
               $tmp_positions = Orderposition::getAllOrderposition($tmp_colinv->getId());
	           
               foreach ($tmp_positions as $position){
    	           $newpos = new Orderposition();
                   	
                   $newpos->setPrice($position->getPrice());
                   $newpos->setComment($position->getComment());
                   $newpos->setQuantity($position->getQuantity());
                   $newpos->setType($position->getType());
                   $newpos->setInvrel($position->getInvrel());
                   $newpos->setRevrel($position->getRevrel());
                   $newpos->setObjectid($position->getObjectid()); // Artikelnummer
                   $tmp_art = new Article($position->getObjectid());
                   if ($tmp_art->getIsWorkHourArt())
                       $needs_planning = true;
                   $newpos->setTax($position->getTax());
                   $newpos->setCollectiveinvoice($collectinv->getId());
                   if ($newpos->getType() == 1){
                       $tmp_order = new Order($newpos->getObjectid());
                       $tmp_order->setCollectiveinvoiceId($collectinv->getId());
                       $tmp_order->save();
                   }
                   $orderpositions[] = $newpos;
               }
               $association = new Association();
               $association->setModule1(get_class($collectinv));
               $association->setObjectid1((int)$collectinv->getId());
               $association->setModule2(get_class($collectinv));
               $association->setObjectid2((int)$tmp_colinv->getId());
               $save_ok = $association->save();
               unset($association);
	       }
	       Orderposition::saveMultipleOrderpositions($orderpositions);
	       if ($needs_planning)
	       {
	           $newinv->setNeeds_planning(1);
	           $newinv->save();
	       }
	       return $collectinv;
	    }
	    return false;
	}
	
	public static function duplicate($id)
	{
		global $_USER;
	    $col = new CollectiveInvoice((int)$id);
		$attribs = $col->getActiveAttributeItemsInput();
		$newnumber = $_USER->getClient()->createOrderNumber(Client::NUMBER_COLINV);
	    $newcol = $col;
	    $newcol->resetId();
	    $newcol->setTitle($newcol->getTitle() . " Kopie");
		$newcol->setNumber($newnumber);
	    $newcol->save();
		$newcol->saveActiveAttributes($attribs);
	    
	    $newops = Array();
	    $ops = Orderposition::getAllOrderposition((int)$id);
	    foreach ($ops as $op)
	    {
	        if ($op->getType() == Orderposition::TYPE_ARTICLE || $op->getType() == Orderposition::TYPE_MANUELL)
	        {
	            $op->setCollectiveinvoice($newcol->getId());
	            $op->setId(0);
	            $newops[] = $op;
	        }
	    }
	    if (!empty($newops))
	        Orderposition::saveMultipleOrderpositions($newops);
	    
	    return $newcol->getId();
	}
	
	/**
	 * Liefert die Sammelrechnung, die als letztes erstellt wurde (mit der groessten ID )
	 * 
	 * @return CollectiveInvoice
	 */
	static function getLastSavedCollectiveInvoice(){
		global $DB;		
		$sql = "SELECT max(id) id FROM collectiveinvoice WHERE status > 0";
		if($DB->no_result($sql)){
			$row = $DB->select($sql);
			$collectiveinvoice = new CollectiveInvoice((int)$row[0]["id"]);
		}
		return $collectiveinvoice;
	}

	/**
	 * Liefert alle Sammelrechnungen die bereit zur Lieferung sind
	 *
	 * @return CollectiveInvoice[]
	 */
	public static function getAllRdyForDispatch(){
		global $DB;
		$sql = "SELECT * FROM collectiveinvoice WHERE status = 5";
		$collectiveInvoices = Array();
		if($DB->no_result($sql)){
			$result = $DB->select($sql);
			foreach($result as $r){
				$collectiveInvoices[] = new CollectiveInvoice($r["id"]);
			}
		}
		return $collectiveInvoices;
	}

	/**
	 * Liefert alle Sammelrechnungen mit Status = 1
	 *
	 * @return CollectiveInvoice[]
	 */
	public static function getAllNew(){
		global $DB;
		$sql = "SELECT * FROM collectiveinvoice WHERE status = 1";
		$collectiveInvoices = Array();
		if($DB->no_result($sql)){
			$result = $DB->select($sql);
			foreach($result as $r){
				$collectiveInvoices[] = new CollectiveInvoice($r["id"]);
			}
		}
		return $collectiveInvoices;
	}

	/**
	 * Liefert Summe aller Sammelrechnungen mit Status = 1
	 *
	 * @return int
	 */
	public static function getAllNewCount(){
		global $DB;
		$ret = 0;
		$sql = "SELECT count(id) as counted FROM collectiveinvoice WHERE status = 1";
		if($DB->no_result($sql)){
			$result = $DB->select($sql);
			$result = $result[0];
			$ret = $result['counted'];
		}
		return $ret;
	}
	
	/**
	 * Liefert alle Sammelrechnungen
	 * 
	 * @return CollectiveInvoice[]
	 */
	static function getAllCollectiveInvoice($order = self::ORDER_NUMBER, $filter = ""){
		global $DB;
		$sql = "SELECT * FROM collectiveinvoice WHERE status > 0 {$filter} ORDER By {$order}";
		$collectiveInvoices = Array();
		if($DB->no_result($sql)){
			$result = $DB->select($sql);
			foreach($result as $r){
				$collectiveInvoices[] = new CollectiveInvoice($r["id"]);
			}
		}
		return $collectiveInvoices;
	}

	/**
	 * @param $search string
	 * @return Array
	 */
	static function searchByNumberOrTitle($search){
		global $DB;
		$retval = Array();
		$sql = "SELECT `id`, `title`, `number` FROM collectiveinvoice WHERE `status` > 0 AND (`number` LIKE '%{$search}%' OR `title` LIKE '%{$search}%') ORDER By id desc";
		if($DB->no_result($sql)){
			$result = $DB->select($sql);
			foreach($result as $r){
				$retval[] = Array("label" => $r["number"].' - '.$r["title"], "value" => $r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Liefert alle Sammelrechnungen für einen Kunden
	 * 
	 * @return Array : CollectiveInvoice
	 */
	static function getAllCollectiveInvoiceForBcon($order = self::ORDER_NUMBER, $bcon = 0){
		global $DB;
		$sql = "SELECT * FROM collectiveinvoice WHERE status > 0 AND businesscontact = {$bcon} ORDER By {$order}";
		$collectiveInvoices = Array();
		if($DB->no_result($sql)){
			$result = $DB->select($sql);
			foreach($result as $r){
				$collectiveInvoices[] = new CollectiveInvoice($r["id"]);
			}
		}
		return $collectiveInvoices;
	}
	
	/**
	* Liefert alle Sammelrechnungen fuer einen bestimmten Kunden, die �ber den Shop abgeschickt wurden (Ersteller = ShopUser (ID=1)) 
	 *
	 * @return Array : CollectiveInvoice
	 */
	static function getAllCollectiveInvoiceForShop($order = self::ORDER_CRTDATE, $busiconID = 0, $search_string = ""){
		global $DB;
		$collectiveInvoices = Array();
		
		$sql = " SELECT t1.id FROM collectiveinvoice t1, collectiveinvoice_orderposition t2
				WHERE 
				t1.status > 0 AND t1.status < 11
				AND t1.id = t2.collectiveinvoice "; // AND t1.crtuser = 1  
		if($busiconID > 0){ 
			$sql.= " AND t1.businesscontact = {$busiconID} ";
		}  
		if($search_string != "" && $search_string != NULL){
			$sql .= " AND (
							t1.number LIKE '%{$search_string}%' OR
							t1.intent LIKE '%{$search_string}%' OR
							t2.comment LIKE '%{$search_string}%' 
						 )";	
		}
		$sql .= "GROUP BY t1.id 
				 ORDER BY t1.{$order} ";
		error_log("SQL: ".$sql." -- ");
		error_log($DB->getLastError());
		if($DB->no_result($sql)){
			$result = $DB->select($sql);
			foreach($result as $r){
				$collectiveInvoices[] = new CollectiveInvoice($r["id"]);
			}
		}
		return $collectiveInvoices;
	}
	
	/**
	 * Liefert die h�chste bisher vergebene (Sammel-)Auftragsnummer
	 */
	static function getHighestNumber(){
		global $DB;
		$ret_number=0;
		
		$jahr = date("Y", time()); 	// Derzeitige vierstellige Jahreszahl
		$monat = date("m", time()); // Derzeitige zweistellige Monatszahl
		
		$filter = "AU-".$jahr.$monat."-";
		
		$ret_number=$filter."0000"; // Falls es f�r den Monat noch keine Nummer gibt
		
		$sql = "SELECT * FROM collectiveinvoice WHERE status > 0 AND number LIKE '%{$filter}%'";
		$manualInvoices = Array();
		if($DB->no_result($sql)){
			$result = $DB->select($sql);
			foreach($result as $r){
				if ($ret_number<$r["number"]){
					$ret_number = $r["number"];
				}
			}
		}
		return $ret_number;
	}
	
	/**
	* ... liefert das Bild zum zugehoerigen Status
	 *
	 * @return string
	 */
	function getStatusImage(){
		$retval="gray.gif";
		switch ($this->status) {
			case 0: $retval = "black.gif";break;
			case 1: $retval = "red.gif";break;
			case 2: $retval = "orange.gif";break;
			case 3: $retval = "yellow.gif";break;
			case 4: $retval = "lila.gif";break;
			case 5: $retval = "lila.gif";break;
			case 6: $retval = "lila.gif";break;
			case 7: $retval = "green.gif";break;
			default: $retval="gray.gif";
		}
		return $retval;
	}


	function getStatusColor(){
		switch ($this->status) {
			case 0: $retval = "black";break;
			case 1: $retval = "red";break;
			case 2: $retval = "orange";break;
			case 3: $retval = "#e4de02";break;
			case 4: $retval = "purple";break;
			case 5: $retval = "blue";break;
			case 6: $retval = "lightblue";break;
			case 7: $retval = "green";break;
			default: $retval="gray";
		}
		return $retval;
	}
	
	/**
	 * ... liefert die Beschreibung zum aktuellen Status
	 *
	 * @return string
	 */
	public function getStatusDescription($status = 0){
	
		// falls kein status uebergeben, setze den Aktuellen
		if($status == 0){
			$status = $this->status;
		}
	
		switch ($status) {
			case 1: $retval = "Angelegt";break;
			case 2: $retval = "Gesendet u. Bestellt";break;
			case 3: $retval = "angenommen";break;
			case 4: $retval = "In Produktion";break;
			case 5: $retval = "Versandbereit";break;
			case 6: $retval = "Ware versand";break;
			case 7: $retval = "Erledigt";break;
			default: $retval="...";
		}
		return $retval;
	}
	
	/**
	 * ... loescht alle aktivierten Attribut-Optionen des Vorgangs
	 * @return boolean
	 */
	public function clearAttributes(){
		global $DB;
		$sql = "DELETE FROM collectiveinvoice_attributes WHERE collectiveinvoice_id = {$this->id} ";
		return $DB->no_result($sql);
	}
	
	/**
	 * ... liefert Alle aktivierten Optionen inkl. Input von Merkmalen zu einem Vorgang
	 * 
	 * @return boolean|Array
	 */
	public function getActiveAttributeItemsInput(){
		global $DB;
		$retval = Array();
		$sql = "SELECT * FROM collectiveinvoice_attributes 
				WHERE 
				collectiveinvoice_id = {$this->id}";
		
		if($DB->num_rows($sql)){
			$res = $DB->select($sql);
			foreach ($res AS $r){
				$retval["{$r["attribute_id"]}_{$r["item_id"]}"]["value"] = $r["value"];
				$retval["{$r["attribute_id"]}_{$r["item_id"]}"]["inputvalue"] = $r["inputvalue"];
			}
		}
// 		print_r($retval);
		return $retval;
	}
	
	/**
	 * ... speichert alle aktivierten Merkmals-Optionen
	 * 
	 * @param Array $active_items
	 */
	public function saveActiveAttributes($active_items){
		global $DB;
		
		foreach($active_items as $item){
			if((int)$item["id"] > 0){
	            $sql = "UPDATE collectiveinvoice_attributes SET
	                    value = '{$item["value"]}', 
	                    inputvalue = '{$item["inputvalue"]}' 
	                    WHERE id = {$item["id"]}";
	            $DB->no_result($sql);
	        } else {
	            $sql = "INSERT INTO collectiveinvoice_attributes
	                        (value, item_id, attribute_id, collectiveinvoice_id, inputvalue )
	                    VALUES
	                        ({$item["value"]}, {$item["item_id"]}, {$item["attribute_id"]}, {$this->id}, '{$item["inputvalue"]}' )";
	            $DB->no_result($sql);
	        }
		}
	}

	/**
	 * Gibt die Gesamt-Netto-Summe des Auftrags zurück
	 * @return float
	 */
	public function getTotalNetSum()
	{
		$sum = 0;
		$mypositions = Orderposition::getAllOrderposition($this->getId());
		foreach ($mypositions as $myposition) {
			if ($myposition->getType() == 2){
				$sum += $myposition->getNetto();
			}
		}
		return $sum;
	}

	/**
	 * Gibt die Gesamt-Brutto-Summe des Auftrags zurück
	 * @return float
	 */
	public function getTotalGrossSum()
	{
		$sum = 0;
		$mypositions = Orderposition::getAllOrderposition($this->getId());
		foreach ($mypositions as $myposition) {
			if ($myposition->getType() == 2){
				$sum += $myposition->getNetto() + ($myposition->getNetto()/100*$myposition->getTax());
			}
		}
		return $sum;
	}

	/**
	 * Gibt die Gesamt-Summe des EKs des Auftrags zurück
	 * @return float
	 * TODO: Funktion schreiben
	 */
	public function getTotalPrimeSum()
	{

	}

	/**
	 * Gibt die Gewinn-Summe des Auftrags zurück
	 * @return float
	 * TODO: Funktion schreiben
	 */
	public function getTotalProfit()
	{

	}
	
	public function resetDocTexts()
	{
	    $offer_header = '';
	    $offer_footer = '';
	    $offerconfirm_header = '';
	    $offerconfirm_footer = '';
	    $factory_header = '';
	    $factory_footer = '';
	    $delivery_header = '';
	    $delivery_footer = '';
	    $invoice_header = '';
	    $invoice_footer = '';
	    $revert_header = '';
	    $revert_footer = '';
	}
	
	private function resetId()
	{
	    $this->id = 0;
	}
	
	/**
	 * Liefert alle OrderPositionen für die aufrufende manuelle Rechnung
	 *  
	 * @return OrderPosition[]
	 */
	public function getPositions($softdeleted = false,$relevant = false){
        return Orderposition::getAllOrderposition($this->id, $softdeleted,$relevant);
	}
	
	public function getCustomer(){
		return $this->businesscontact;
	}

	public function getId()
	{
	    return $this->id;
	}

	public function getStatus()
	{
	    return $this->status;
	}

	public function setStatus($status)
	{
	    $this->status = $status;
	}

	public function getTitle()
	{
	    return $this->title;
	}

	public function setTitle($title)
	{
	    $this->title = $title;
	}

	public function getNumber()
	{
	    return $this->number;
	}

	public function setNumber($number)
	{
	    $this->number = $number;
	}

	public function getCrtdate()
	{
	    return $this->crtdate;
	}

	public function getCrtdat()
	{
	    return $this->crtdate;
	}

	public function setCrtdate($crtdate)
	{
	    $this->crtdate = $crtdate;
	}

	public function getCrtuser()
	{
	    return $this->crtuser;
	}

	public function setCrtuser($crtuser)
	{
	    $this->crtuser = $crtuser;
	}

	public function getUptdate()
	{
	    return $this->uptdate;
	}

	public function setUptdate($uptdate)
	{
	    $this->uptdate = $uptdate;
	}

	public function getUptuser()
	{
	    return $this->uptuser;
	}

	public function setUptuser($uptuser)
	{
	    $this->uptuser = $uptuser;
	}

	public function getDeliverycosts()
	{
	    return $this->deliverycosts;
	}

	public function setDeliverycosts($deliverycosts)
	{
	    $this->deliverycosts = $deliverycosts;
	}

	public function getComment()
	{
	    return $this->comment;
	}

	public function setComment($comment)
	{
	    $this->comment = $comment;
	}

	public function getBusinesscontact()
	{
	    return $this->businesscontact;
	}

	public function setBusinesscontact($businesscontact)
	{
	    $this->businesscontact = $businesscontact;
	}

	public function getDeliveryterm()
	{
	    return $this->deliveryterm;
	}

	public function setDeliveryterm($deliveryterm)
	{
	    $this->deliveryterm = $deliveryterm;
	}

	public function getPaymentterm()
	{
	    return $this->paymentterm;
	}

	public function setPaymentterm($paymentterm)
	{
	    $this->paymentterm = $paymentterm;
	}

	public function getDeliveryaddress()
	{
	    return $this->deliveryaddress;
	}

	public function setDeliveryaddress($deliveryaddress)
	{
	    $this->deliveryaddress = $deliveryaddress;
	}
	public function setDeliveryaddressById($deliveryID){
		$this->deliveryaddress = new Address($deliveryID);
	}
	public function getClient()
	{
	    return $this->client;
	}

	public function setClient($client)
	{
	    $this->client = $client;
	}
	
    public function getIntent()
	{
	    return $this->intent;
	}

	public function setIntent($intent)
	{
	    $this->intent = $intent;
	}
	
	public function getInternContact()
	{
	    return $this->internContact;
	}
	
	public function setInternContact($internContact)
	{
	    $this->internContact = $internContact;
	}
	
	public function getCustMessage()
	{
	    return $this->custMessage;
	}
	
	public function setCustMessage($custMessage)
	{
	    $this->custMessage = $custMessage;
	}
	
	public function getCustSign()
	{
	    return $this->custSign;
	}
	
	public function setCustSign($custSign)
	{
	    $this->custSign = $custSign;
	}

    public function getInvoiceAddress()
    {
        return $this->invoiceAddress;
    }

    public function setInvoiceAddress($invoiceAddress)
    {
        $this->invoiceAddress = $invoiceAddress;
    }
    
	/**
     * @return the $custContactperson
     */
    public function getCustContactperson()
    {
        return $this->custContactperson;
    }

	/**
     * @param ContactPerson $custContactperson
     */
    public function setCustContactperson($custContactperson)
    {
        $this->custContactperson = $custContactperson;
    }
    
	/**
     * @return the $needs_planning
     */
    public function getNeeds_planning()
    {
        return $this->needs_planning;
    }
	public function getNeedsplanning()
	{
		return $this->needs_planning;
	}

	/**
     * @param field_type $needs_planning
     */
    public function setNeeds_planning($needs_planning)
    {
        $this->needs_planning = $needs_planning;
    }
    
	/**
     * @return the $deliverydate
     */
    public function getDeliverydate()
    {
        return $this->deliverydate;
    }

	/**
	 * @return string $deliverydate
	 */
	public function getDeliverydateFormated()
	{
		return date('d.m.y',$this->deliverydate);
	}

	/**
     * @param number $deliverydate
     */
    public function setDeliverydate($deliverydate)
    {
        $this->deliverydate = $deliverydate;
    }
    
	/**
     * @return the $ext_comment
     */
    public function getExt_comment()
    {
        return $this->ext_comment;
    }
	public function getExtcomment()
	{
		return $this->ext_comment;
	}

	/**
     * @param string $ext_comment
     */
    public function setExt_comment($ext_comment)
    {
        $this->ext_comment = $ext_comment;
    }
    
    /**
     * @return the $offer_header
     */
    public function getOffer_header()
    {
        return $this->offer_header;
    }
	public function getOfferheader()
	{
		return $this->offer_header;
	}

    /**
     * @return the $offer_footer
     */
    public function getOffer_footer()
    {
        return $this->offer_footer;
    }
	public function getOfferfooter()
	{
		return $this->offer_footer;
	}

    /**
     * @return the $offerconfirm_header
     */
    public function getOfferconfirm_header()
    {
        return $this->offerconfirm_header;
    }
	public function getOfferconfirmheader()
	{
		return $this->offerconfirm_header;
	}

    /**
     * @return the $offerconfirm_footer
     */
    public function getOfferconfirm_footer()
    {
        return $this->offerconfirm_footer;
    }
	public function getOfferconfirmfooter()
	{
		return $this->offerconfirm_footer;
	}

    /**
     * @return the $factory_header
     */
    public function getFactory_header()
    {
        return $this->factory_header;
    }
	public function getFactoryheader()
	{
		return $this->factory_header;
	}

    /**
     * @return the $factory_footer
     */
    public function getFactory_footer()
    {
        return $this->factory_footer;
    }
	public function getFactoryfooter()
	{
		return $this->factory_footer;
	}

    /**
     * @return the $delivery_header
     */
    public function getDelivery_header()
    {
        return $this->delivery_header;
    }
	public function getDeliveryheader()
	{
		return $this->delivery_header;
	}

    /**
     * @return the $delivery_footer
     */
    public function getDelivery_footer()
    {
        return $this->delivery_footer;
    }
	public function getDeliveryfooter()
	{
		return $this->delivery_footer;
	}

    /**
     * @return the $invoice_header
     */
    public function getInvoice_header()
    {
        return $this->invoice_header;
    }
	public function getInvoiceheader()
	{
		return $this->invoice_header;
	}

    /**
     * @return the $invoice_footer
     */
    public function getInvoice_footer()
    {
        return $this->invoice_footer;
    }
	public function getInvoicefooter()
	{
		return $this->invoice_footer;
	}

    /**
     * @return the $revert_header
     */
    public function getRevert_header()
    {
        return $this->revert_header;
    }
	public function getRevertheader()
	{
		return $this->revert_header;
	}

    /**
     * @return the $revert_footer
     */
    public function getRevert_footer()
    {
        return $this->revert_footer;
    }
	public function getRevertfooter()
	{
		return $this->revert_footer;
	}

    /**
     * @param field_type $offer_header
     */
    public function setOffer_header($offer_header)
    {
        $this->offer_header = $offer_header;
    }

    /**
     * @param field_type $offer_footer
     */
    public function setOffer_footer($offer_footer)
    {
        $this->offer_footer = $offer_footer;
    }

    /**
     * @param field_type $offerconfirm_header
     */
    public function setOfferconfirm_header($offerconfirm_header)
    {
        $this->offerconfirm_header = $offerconfirm_header;
    }

    /**
     * @param field_type $offerconfirm_footer
     */
    public function setOfferconfirm_footer($offerconfirm_footer)
    {
        $this->offerconfirm_footer = $offerconfirm_footer;
    }

    /**
     * @param field_type $factory_header
     */
    public function setFactory_header($factory_header)
    {
        $this->factory_header = $factory_header;
    }

    /**
     * @param field_type $factory_footer
     */
    public function setFactory_footer($factory_footer)
    {
        $this->factory_footer = $factory_footer;
    }

    /**
     * @param field_type $delivery_header
     */
    public function setDelivery_header($delivery_header)
    {
        $this->delivery_header = $delivery_header;
    }

    /**
     * @param field_type $delivery_footer
     */
    public function setDelivery_footer($delivery_footer)
    {
        $this->delivery_footer = $delivery_footer;
    }

    /**
     * @param field_type $invoice_header
     */
    public function setInvoice_header($invoice_header)
    {
        $this->invoice_header = $invoice_header;
    }

    /**
     * @param field_type $invoice_footer
     */
    public function setInvoice_footer($invoice_footer)
    {
        $this->invoice_footer = $invoice_footer;
    }

    /**
     * @param field_type $revert_header
     */
    public function setRevert_header($revert_header)
    {
        $this->revert_header = $revert_header;
    }

    /**
     * @param field_type $revert_footer
     */
    public function setRevert_footer($revert_footer)
    {
        $this->revert_footer = $revert_footer;
    }

	/**
	 * @return int
	 */
	public function getRdyfordispatch()
	{
		return $this->rdyfordispatch;
	}

	/**
	 * @param int $rdyfordispatch
	 */
	public function setRdyfordispatch($rdyfordispatch)
	{
		$this->rdyfordispatch = $rdyfordispatch;
	}

	/**
	 * @return int
	 */
	public function getThirdparty()
	{
		return $this->thirdparty;
	}

	/**
	 * @param int $thirdparty
	 */
	public function setThirdparty($thirdparty)
	{
		$this->thirdparty = $thirdparty;
	}

	/**
	 * @return string
	 */
	public function getThirdpartycomment()
	{
		return $this->thirdpartycomment;
	}

	/**
	 * @param string $thirdpartycomment
	 */
	public function setThirdpartycomment($thirdpartycomment)
	{
		$this->thirdpartycomment = $thirdpartycomment;
	}

	/**
	 * @return int
	 */
	public function getTicket()
	{
		return $this->ticket;
	}

	/**
	 * @param int $ticket
	 */
	public function setTicket($ticket)
	{
		$this->ticket = $ticket;
	}

	/**
	 * @return int
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @param int $type
	 */
	public function setType($type)
	{
		$this->type = $type;
	}

	/**
	 * @return int
	 */
	public function getSavedcost()
	{
		return $this->savedcost;
	}

	/**
	 * @param int $savedcost
	 */
	public function setSavedcost($savedcost)
	{
		$this->savedcost = $savedcost;
	}
}