<? //-------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       16.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/products/product.class.php';
require_once 'libs/modules/foldtypes/foldtype.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/deliveryterms/deliveryterms.class.php';
require_once 'libs/modules/paymentterms/paymentterms.class.php';
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';
require_once 'libs/modules/documents/document.class.php';

class Order {
    const ORDER_NUMBER = "number desc";
    const ORDER_CUSTOMER = "businesscontact_id";
    const ORDER_STATUS = "status";
    const ORDER_TITLE = "title";
    
    const FILTER_CLOSED = "and status = 5";
    const FILTER_CONFIRMED = "and status = 3";
    const FILTER_OPEN = "and status <> 5";
    const FILTER_ALL = "";
    
    private $id = 0;
    private $number;
    private $customer;
    private $custContactperson;			// Anspr. des Kunden
    private $status = 1;
    private $title;
    private $product;
    private $notes;
    private $deliveryAddress;
    private $deliveryTerms;
    private $invoiceAddress;
    private $paymentTerms;
    private $deliveryDate = 0;
    private $deliveryCost = 0;
    private $textOffer;
    private $textOfferconfirm;
    private $textInvoice;
    private $crtdat = 0;
    private $crtusr;
    private $upddat = 0;
    private $collectiveinvoiceId = 0;
    private $internContact;				// Benutzer von KDM, der auf den Dokumenten auftauchen soll
    private $custMessage;				// Auf den Dokumenten "Ihre Nachricht"
    private $custSign;					// Auf den Dokumenten "Ihr Zeichen"
    private $invoiceAmount = 0;			// Tatsaechlich produzierte Menge (auf der Rechnung)
    private $invoicePriceUpdate	= 0;	// Betrag auf der Rechnung an tatsl. Menge anpassen
    private $deliveryAmount = 0;		// Tatsaechliche Liefermenge (auf dem Lieferschein)
    private $labelPalletAmount = 0;		// Menge auf Palette
    private $labelBoxAmount = 0;		// Menge in der Kiste
    private $labelTitle;				// Zur Erweiterung/Bearbeitung des Titels
    private $labelLogoActive = 0;		// Logo anzeigen oder nicht
    private $showProduct = 1;			// Produktdetails auf den Dokumenten ausgeben
    private $productName;				// Produktname in den Dokumenten ueberschreiben
    private $showPricePer1000 = 0;		// Preis pro 1000 Stk auf den Dokumenten anzeigen

    private $paper_order_boegen = "";	// Bogenanzahl für Papierbestellung
    private $paper_order_price = "";	// Preis für Papierbestellung
    private $paper_order_supplier = 0;	// Lieferant für Papierbestellung
    private $paper_order_calc = 0;		// Calculation für Papierbestellung
    
    private $beilagen;                  // Text Feld für Beilagen
    private $articleid = 0;             // Verknuepfter Artikel
    
    function __construct($id)
    {
        $this->deliveryAddress = new Address();
        $this->invoiceAddress = new Address();
        $this->deliveryTerms = new DeliveryTerms();
        $this->paymentTerms = new PaymentTerms();
        $this->custContactperson = new ContactPerson();
        $this->customer = new BusinessContact();
        $this->internContact = new User();
        $this->crtusr = new User;
        
        global $DB;
        if($id > 0){
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
                $sql = "SELECT * FROM orders WHERE id = {$id}";
                if ($DB->num_rows($sql)) {
                    $res = $DB->select($sql);
                    $res = $res[0];

                    $this->id = $res["id"];
                    $this->number = $res["number"];
                    $this->customer = new BusinessContact($res["businesscontact_id"], BusinessContact::LOADER_FULL);
                    $this->status = $res["status"];
                    $this->title = $res["title"];
                    $this->product = new Product($res["product_id"]);
                    $this->notes = $res["notes"];
                    $this->deliveryDate = $res["delivery_date"];
                    $this->deliveryCost = $res["delivery_cost"];
                    if ($res["delivery_address_id"] > 0)
                        $this->deliveryAddress = new Address($res["delivery_address_id"]);
                    if ($res["delivery_terms_id"])
                        $this->deliveryTerms = new DeliveryTerms($res["delivery_terms_id"]);
                    if ($res["invoice_address_id"] > 0)
                        $this->invoiceAddress = new Address($res["invoice_address_id"]);
                    if ($res["payment_terms_id"] > 0)
                        $this->paymentTerms = new PaymentTerms($res["payment_terms_id"]);
                    if ($res["cust_contactperson"] > 0)
                        $this->custContactperson = new ContactPerson($res["cust_contactperson"]);
                    $this->textOffer = $res["text_offer"];
                    $this->textOfferconfirm = $res["text_offerconfirm"];
                    $this->textInvoice = $res["text_invoice"];
                    $this->crtdat = $res["crtdat"];
                    $this->upddat = $res["upddat"];
                    $this->collectiveinvoiceId = $res["collectiveinvoice_id"];
                    $this->internContact = new User($res["intern_contactperson"]);
                    $this->custMessage = $res["cust_message"];
                    $this->custSign = $res["cust_sign"];
                    $this->invoicePriceUpdate = $res["inv_price_update"];
                    $this->invoiceAmount = $res["inv_amount"];
                    $this->deliveryAmount = $res["deliv_amount"];
                    $this->labelLogoActive = $res["label_logo_active"];
                    $this->labelBoxAmount = $res["label_box_amount"];
                    $this->labelTitle = $res["label_title"];
                    $this->showProduct = $res["show_product"];
                    $this->productName = $res["productname"];
                    $this->showPricePer1000 = $res["show_price_per_thousand"];
                    $this->paper_order_boegen = $res["paper_order_boegen"];
                    $this->paper_order_price = $res["paper_order_price"];
                    $this->paper_order_supplier = $res["paper_order_supplier"];
                    $this->paper_order_calc = $res["paper_order_calc"];
                    $this->crtusr = new User($res["crtusr"]);
                    $this->beilagen = $res["beilagen"];
                    $this->articleid = $res["articleid"];

                    Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
                }
            }
        }
    }

    function save()
    {
        global $DB;
        global $_USER;
        if($this->id > 0){
            $sql = "UPDATE orders SET
                        number = '{$this->number}',
                        businesscontact_id = {$this->customer->getId()},
                        status = {$this->status},
                        title = '{$this->title}',
                        product_id = '{$this->product->getId()}',
                        notes = '{$this->notes}',
                        delivery_address_id = '{$this->deliveryAddress->getId()}',
                        invoice_address_id = '{$this->invoiceAddress->getId()}',
                        delivery_terms_id = '{$this->deliveryTerms->getId()}',
                        payment_terms_id = '{$this->paymentTerms->getId()}',
                        cust_contactperson = '{$this->custContactperson->getId()}',
                        delivery_date = {$this->deliveryDate},
                        delivery_cost = {$this->deliveryCost},
                        text_offer = '{$this->textOffer}',
                        text_offerconfirm = '{$this->textOfferconfirm}',
                        text_invoice = '{$this->textInvoice}',
                        upddat = UNIX_TIMESTAMP(),
                        updusr = {$_USER->getId()},
                        collectiveinvoice_id = {$this->collectiveinvoiceId},
                        intern_contactperson = {$this->internContact->getId()},
                        cust_message = '{$this->custMessage}',
                        cust_sign = '{$this->custSign}',
                        inv_amount = {$this->invoiceAmount},
                        inv_price_update = {$this->invoicePriceUpdate},
                        deliv_amount = {$this->deliveryAmount},
                        label_logo_active = {$this->labelLogoActive},
                        label_box_amount = {$this->labelBoxAmount},
                        label_title = '{$this->labelTitle}',
                        show_product = {$this->showProduct},
                		productname = '{$this->productName}',
                		paper_order_boegen = '{$this->paper_order_boegen}',
                		paper_order_price = '{$this->paper_order_price}',
                		paper_order_supplier = {$this->paper_order_supplier},
                		paper_order_calc = {$this->paper_order_calc},
                		beilagen = '{$this->beilagen}',
                		show_price_per_thousand = {$this->showPricePer1000},
                		articleid = {$this->articleid}
                    WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
        } else
        {
            if($this->number == "")
                $this->number = $_USER->getClient()->createOrderNumber(Client::NUMBER_ORDER);
            if($this->product != NULL){
                $tmp_product = $this->product->getId();
            } else {
                $tmp_product = 0;
            }
            $sql = "INSERT INTO orders
                        (number, status, businesscontact_id, product_id, title, notes,
                         delivery_address_id, invoice_address_id, delivery_terms_id,
                         payment_terms_id, crtdat, crtusr,
                         collectiveinvoice_id, intern_contactperson, cust_message,
                         cust_sign, cust_contactperson, inv_amount,
                         inv_price_update, deliv_amount, label_logo_active,
                         label_box_amount, label_title, show_product, productname,
                         show_price_per_thousand, paper_order_boegen, paper_order_price,
                         paper_order_supplier, paper_order_calc, beilagen, articleid )
                    VALUES
                        ('{$this->number}', 1, '{$this->customer->getId()}', $tmp_product,
                         '{$this->title}', '{$this->notes}', '0', '0', '0',
                         '{$this->paymentTerms->getId()}', UNIX_TIMESTAMP(), {$_USER->getId()},
            			 {$this->collectiveinvoiceId}, {$this->internContact->getId()}, '{$this->custMessage}',
            			 '{$this->custSign}', {$this->custContactperson->getId()}, {$this->invoiceAmount},
            			 {$this->invoicePriceUpdate}, {$this->deliveryAmount}, {$this->labelLogoActive},
            			 {$this->labelLogoActive}, '{$this->labelTitle}', {$this->showProduct}, '{$this->productName}',
            			 {$this->showPricePer1000}, '{$this->paper_order_boegen}', '{$this->paper_order_price}',
						 {$this->paper_order_supplier}, {$this->paper_order_calc}, '{$this->beilagen}', {$this->articleid} )";
            $res = $DB->no_result($sql);
            if($res)
            {
                $sql = "SELECT max(id) id FROM orders WHERE number = '{$this->number}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                $res = true;
            } else
                $res = false;
        }
        if ($res)
        {
            Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
            return true;
        }
        else
            return false;
    }

    function delete()
    {
        global $DB;
        if($this->id)
        {
            $sql = "UPDATE orders SET status = 0 WHERE id = {$this->id}";
            if($DB->no_result($sql))
            {
                Cachehandler::removeCache(Cachehandler::genKeyword($this));
                unset($this);
                return true;
            } else
                return false;
        }
    }

    static function getAllOrders($order = self::ORDER_NUMBER, $filter = self::FILTER_ALL)
    {
        $retval = Array();
        global $DB;
        $sql = "SELECT id FROM orders 
                WHERE status > 0 {$filter}
                ORDER BY {$order}";
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new Order($r["id"]);
            }
        }

        return $retval;
    }

    /**
     * @param $time
     *
     * @return Order[]
     */
    public static function getOrdersWithDeliveryDate($time) {
        $retval = Array();
        global $DB;
        $sql = "SELECT id, FROM_UNIXTIME(delivery_date - 36000, '%Y-%m-%d') AS deliveryDate
                    FROM orders
                    WHERE status > 0
                    HAVING deliveryDate = '{$time}'
                    ORDER BY delivery_date ASC";
#var_dump($sql);exit;
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new Order($r["id"]);
            }
        }

        return $retval;
    }

    /**
     * @param $start, $end
     *
     * @return Order[]
     */
    public static function getOrdersWithinTimeFrame($start, $end) {
        $retval = Array();
        global $DB;
        $sql = "SELECT id, delivery_date
                    FROM orders
                    WHERE status > 0
                    HAVING delivery_date >= {$start} AND delivery_date <= {$end}
                    ORDER BY delivery_date ASC";
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new Order($r["id"]);
            }
        }

        return $retval;
    }

    static function getAllOrdersByNumber($searchString, $order = self::ORDER_NUMBER, $filter = self::FILTER_ALL)
    {
        $retval = Array();
        global $DB;
        $sql = "SELECT id FROM orders
                WHERE
                  status > 0 {$filter} AND
                  number LIKE '%{$searchString}%'
                ORDER BY {$order}";
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new Order($r["id"]);
            }
        }

        return $retval;
    }
    
    /**
     * Liefert alle Orders eines Geschaeftskontakts
     * 
     * @param String $order
     * @param int $customerId
     * @return multitype:Order
     */
    static function getAllOrdersByCustomer($order = self::ORDER_TITLE, $customerId = 0, $filter = null){
    	$retval = Array();
    	global $DB;
    	$sql = "SELECT id FROM orders
    			WHERE status > 0 {$filter} ";
    	if($customerId > 0){
    		$sql .= " AND businesscontact_id  = {$customerId} ";
    	}
    	$sql .= " ORDER BY {$order}";
    	// error_log("SQL: ".$sql);
    	if($DB->num_rows($sql))
    	{
    		foreach($DB->select($sql) as $r)
    		{
    			$retval[] = new Order($r["id"]);
    		}
    	}

    	return $retval;
    }
    
    
    static function searchByNumber($number, $order = self::ORDER_NUMBER)
    {
        $retval = Array();
        global $DB;
        $sql = "SELECT id, number, status, title FROM orders
                    WHERE number like '%{$number}%'
                    ORDER BY {$order}";
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new Order($r["id"]);
            }
        }
    
        return $retval;
    }

    /**
     * Suchfunktion fuer Auftraege. Gesucht wird im Titel, Auftragsnummer, Geschaeftskontakten und Ansprechpartnern
     * 
     * @param String $search	: Suchstring
     * @param String $order		: Sortierung der Ausgabe
     * @return Array:Order
     */
	static function searchOrderByTitleCustomer($search, $order = self::ORDER_NUMBER){
        $retval = Array();
        global $DB;
        $sql = "SELECT orders.id, orders.number, orders.status, orders.title, businesscontact.name1 
                FROM orders 
                INNER JOIN businesscontact ON orders.businesscontact_id = businesscontact.id
                LEFT OUTER JOIN contactperson contact ON contact.businesscontact = businesscontact.id
                LEFT JOIN documents d ON d.doc_req_id = orders.id
                WHERE (orders.title like '%{$search}%'
                		OR orders.number like '%{$search}%'
                        OR businesscontact.name1 like '%{$search}%'
                        OR businesscontact.name2 like '%{$search}%'
                        OR (d.doc_name like '%{$search}%' AND d.doc_req_module = ".Document::REQ_MODULE_ORDER." )
                        OR contact.name1 like '%{$search}%'
                        OR contact.name2 like '%{$search}%')
                    AND orders.status > 0 
        			GROUP BY orders.id 
                    ORDER BY {$order} ";
        // echo $sql;
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new Order($r["id"]);
            }
        }
        return $retval;
    }
    
    /**
     * Suchfunktion fuer Auftraege. Sucht in Titel und Auftragsnummer.
     * Falls eine ID eines Geschaeftskontakts angegeben wird, wird danach gefiltert
     * 
     * @param String $search
     * @param String $order
     * @return Array : Order
     */
    static function searchOrderByTitleNumber($search, $custId = 0, $order = self::ORDER_TITLE){
		global $DB;
		$retval = Array();
		$sql = "SELECT id, number, status, title FROM orders
				WHERE status > 0 AND 
				(number like '%{$search}%'
				OR title like '%{$search}%')";
		if($custId != 0){
			$sql .=  "AND businesscontact_id = {$custId} ";
		}
		
		$sql .=	"ORDER BY {$order}";
		
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Order($r["id"]);
			}
		}
		return $retval;
	}

				  
/**
     * Suchfunktion mit diversen Abfrage-Variablen
     * 
     * @param int $customerId	: Id des Geschaeftskontakts
     * @param String $number	: Auftragsnummer
     * @param String $title		: SuchString fuer den Titel
     * @param String $inv_name	: ScuhString der Rechnungsnummer
     * @param String $order 	: Sortierung der Ergebnisse
     * @return multitype:Order
     */
    static function searchOrderByCustomeridNumberTitle($customerId, $number, $title, $inv_name, $order = self::ORDER_NUMBER)
    {
        $retval = Array();
        global $DB;
        $sql = "SELECT o.id 
        			FROM orders o";
        if($inv_name != ""){
        	$sql .= " LEFT JOIN documents d ON d.doc_req_id = o.id
                    WHERE 
                    (d.doc_name LIKE 'RE%{$inv_name}%' ) AND ";
        } else {
        	$sql .= " WHERE ";
        } 
		if ($number != ""){
			$sql .=" o.number like '%{$number}%' AND ";
		}
        if ($customerId > 0){
        	$sql .= " o.businesscontact_id  = {$customerId} AND ";
        }
        if ($title != ""){
        	$sql .= " o.title like '%{$title}%' AND ";   
        }
		$sql .= " o.status > 0
				ORDER BY {$order}";  
		      
        /*SELECT id, number, status, title FROM orders
        WHERE businesscontact_id like '%{$customerId}%'
        		AND number like '%{$number}%'
        				AND title like '%{$title}%'*/  
		            
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new Order($r["id"]);
            }
        }
        return $retval;
    }


    /**
     * @param $order
     * @param $colinv
     * @return string
     */
    public static function generateSummary(Order $order, CollectiveInvoice $colinv)
    {
        global $_USER;
        $html = "";
        
        $html .= '<h1>Kalkulationsbersicht</h1>';
        $html .= '<div class="outer"><table cellpadding="0" cellspacing="0" border="0" width="100%">';
        $html .= '<colgroup><col width="10%"><col width="23%"><col width="10%"><col width="23%"><col width="10%"><col></colgroup>';
        $html .= '<tr><td><b>Kundennummer:</b></td>';
        $html .= '<td>'.$order->getCustomer()->getCustomernumber().'</td>';
        $html .= '<td><b>Vorgang:</b></td>';
        $html .= '<td>'.$order->getNumber().'</td>';
        $html .= '<td><b>Telefon:</b></td>';
        $html .= '<td>'.$order->getCustomer()->getPhone().'</td>';
        $html .= '</tr><tr>';
        $html .= '<td valign="top"><b>Name:</b></td>';
        $html .= '<td valign="top">'.nl2br($order->getCustomer()->getNameAsLine()).'</td>';
        $html .= '<td valign="top"><b>Adresse:</b></td>';
        $html .= '<td valign="top">'.nl2br($order->getCustomer()->getAddressAsLine()).'</td>';
        $html .= '<td valign="top"><b>E-Mail:</b></td>';
        $html .= '<td valign="top">'.$order->getCustomer()->getEmail().'</td>';
        $html .= '</tr></table></div><br><div class="outer"><table cellpadding="0" cellspacing="0" border="0" width="100%">';
        $html .= '<colgroup><col width="10%"><col width="23%"><col width="10%"><col width="23%"><col width="10%"><col></colgroup>';
        $html .= '<tr>';
        $html .= '<td valign="top"><b>Produkt:</b></td>';
        $html .= '<td valign="top">'.$order->getProduct()->getName().'</td>';
        $html .= '<td valign="top"><b>Beschreibung:</b></td>';
        $html .= '<td valign="top">'.$order->getProduct()->getDescription().'</td>';
        $html .= '<td valign="top"><b>Bemerkungen:</b></td>';
        $html .= '<td valign="top">'.nl2br($order->getNotes()).'</td>';
        $html .= '</tr><tr>';
        $html .= '<td><b>Lieferadresse:</b></td>';
        $html .= '<td>'.nl2br($order->getDeliveryAddress()->getAddressAsLine()).'</td>';
        $html .= '<td><b>Lieferbedingungen:</b></td>';
        $html .= '<td>'.$order->getDeliveryTerms()->getComment().'</td>';
        $html .= '<td><b>Lieferdatum:</b></td>';
        $html .= '<td>';
        if($order->getDeliveryDate() > 0) 
            $html .= date('d.m.Y', $order->getDeliveryDate());
        $html .= '</td>';
        $html .= '</tr><tr>';
        $html .= '<td><b>Zahlungsadresse:</b></td>';
        $html .= '<td>'.nl2br($order->getInvoiceAddress()->getAddressAsLine()).'</td>';
        $html .= '<td><b>Zahlungsbedingungen:</b></td>';
        $html .= '<td>'.$order->getPaymentTerms()->getComment().'</td>';
        $html .= '<td><b>&nbsp;</b></td><td>&nbsp;</td></tr></table></div><br>';
        
        $i = 1; 
        foreach(Calculation::getAllCalculations($order) as $calc) {
            if ($calc->getState() == 0)
                continue;
            
            $calc_sorts = $calc->getSorts();
            if ($calc_sorts == 0)
                $calc_sorts = 1;
            
            $html .= '<h2>Teilauftag # '.$i.' - Auflage '.printBigInt($calc->getAmount()).' ('.$calc_sorts.' Sorte(n)* '.$calc->getAmount()/$calc_sorts.' Auflage)</h2>';
            $html .= '<div class="outer"><table cellpadding="0" cellspacing="0" border="0" width="100%"><colgroup><col width="15%">';
            $html .= '<col width="35%"><col width="15%"><col width="35%"></colgroup><tr>';
            $html .= '<td valign="top"><b>Inhalt:</b></td>';
            $html .= '<td valign="top">';
            $html .= $calc->getPaperContent()->getName().', '.$calc->getPaperContentWeight().' g';
            $html .= '</td>';
            $html .= '<td valign="top"><b>zus. Inhalt:</b></td>';
            $html .= '<td valign="top">';
            
            if($calc->getPaperAddContent()->getId()) {
                $html .= $calc->getPaperAddContent()->getName().', '.$calc->getPaperAddContentWeight().' g';
            }
            
            $html .= '</td></tr><tr><td valign="top"></td><td valign="top">';
            $html .= $calc->getPagesContent().' Seiten, '.$calc->getProductFormat()->getName().', '.$calc->getChromaticitiesContent()->getName();
            $html .= '</td><td valign="top"></td><td valign="top">';
            
            if($calc->getPaperAddContent()->getId()) {
                $html .= $calc->getPagesAddContent().' Seiten, '.$calc->getProductFormat()->getName().', '.$calc->getChromaticitiesAddContent()->getName();
            }
            
            $html .= '</td></tr><tr><td colspan="4">&nbsp;</td></tr><tr><td valign="top">';
            
            if($calc->getPaperAddContent2()->getId() > 0) {
                $html .= '<b>zus. Inhalt 2:</b>';
            }
            
            $html .= '</td><td valign="top">';
            
            if($calc->getPaperAddContent2()->getId() > 0) {
                $html .= $calc->getPaperAddContent2()->getName().', '.$calc->getPaperAddContent2Weight().' g';
            }
            
            $html .= '</td><td valign="top">';
            
            if($calc->getPaperAddContent3()->getId() > 0) {
                $html .= '<b>zus. Inhalt 3:</b>';
            }
            
            $html .= '</td><td valign="top">';
            
            if($calc->getPaperAddContent3()->getId()) {
                $html .= $calc->getPaperAddContent3()->getName().', '.$calc->getPaperAddContent3Weight().' g';
            }
            
            $html .= '</td></tr><tr><td valign="top"></td><td valign="top">';
            
            if($calc->getPaperAddContent2()->getId()) {
                $html .= $calc->getPagesAddContent2().' Seiten, '.$calc->getProductFormat()->getName().', '.$calc->getChromaticitiesAddContent2()->getName();
            }
            
            $html .= '</td><td valign="top"></td><td valign="top">';
            
            if($calc->getPaperAddContent3()->getId()) {
                $html .= $calc->getPagesAddContent3().' Seiten, '.$calc->getProductFormat()->getName().', '.$calc->getChromaticitiesAddContent3()->getName();
            }
            
            $html .= '</td></tr>';
            
            if($calc->getPaperEnvelope()->getId()) {
                $html .= '<tr><td colspan="4">&nbsp;</td></tr><tr>';
                $html .= '<td valign="top"><b>Umschlag:</b></td>';
                $html .= '<td valign="top">';
                $html .= $calc->getPaperEnvelope()->getName().', '.$calc->getPaperEnvelopeWeight().' g';
                $html .= '</td></tr><tr><td valign="top"></td><td valign="top">';
                $html .= $calc->getPagesEnvelope().' Seiten, '.$calc->getProductFormat()->getName().', '.$calc->getChromaticitiesEnvelope()->getName();
                $html .= '</td></tr>';
            }
            
            $html .= '</table></div><br>';
            $html .= '<h3>Papierpreise</h3>';
            $html .= '<div class="outer"><table cellpadding="0" cellspacing="0" border="0" width="100%">';
            $html .= '<colgroup><col width="15%"><col width="35%"><col width="15%"><col width="35%"></colgroup><tr>';
            $html .= '<td valign="top"><b>Inhalt:</b></td>';
            $html .= '<td valign="top">';
            $html .= 'Bogenformat: '.$calc->getPaperContentWidth().' mm x '.$calc->getPaperContentHeight().' mm <br>';
            $html .= 'Produktformat: '.$calc->getProductFormatWidth().' mm x '.$calc->getProductFormatHeight().' mm,'; 
            $html .= $calc->getProductFormatWidthOpen().' mm x '.$calc->getProductFormatHeightOpen().' mm (offen)<br>';
            $html .= 'Nutzen pro Bogen: '.$calc->getProductsPerPaper(Calculation::PAPER_CONTENT).',';
            $html .= 'Anzahl B&ouml;gen pro Auflage: '.printPrice($calc->getPagesContent() / $calc->getProductsPerPaper(Calculation::PAPER_CONTENT)).'<br>';
            $html .= 'B&ouml;gen insgesamt:';
             
            $sheets = ceil($calc->getPagesContent() / $calc->getProductsPerPaper(Calculation::PAPER_CONTENT) * $calc->getAmount());
            $html .= printBigInt($sheets);
            $html .= ' + Zuschuss';
            $html .= printBigInt($calc->getPaperContentGrant());
            
            $sheets += $calc->getPaperContentGrant();
            $sheets_content = $sheets;
            
            $html .= '<br>Papiergewicht:';
             
            $area = $calc->getPaperContentWidth() * $calc->getPaperContentHeight();

            $html .= printPrice((($area * $calc->getPaperContentWeight() / 10000 / 100) * $sheets) / 1000);
            $html .= ' kg,';
            $html .= 'Papierpreis: '.printPrice($calc->getPaperContent()->getSumPrice($sheets)).' €<br>';
            $html .= 'Preisbasis: '; 
            
            if ($calc->getPaperContent()->getPriceBase() == Paper::PRICE_PER_100KG) 
                $html .= 'Preis pro 100 kg';
            else 
                $html .= 'Preis pro 1000 B&ouml;gen';

            $html .= '</td><td valign="top"><b>zus. Inhalt:</b></td><td valign="top">';
            
            if($calc->getPaperAddContent()->getId()) {
                $html .= 'Bogenformat: '.$calc->getPaperAddContentWidth().' mm x '.$calc->getPaperAddContentHeight().' mm <br>';
                $html .= 'Produktformat: '.$calc->getProductFormatWidth().' mm x '.$calc->getProductFormatHeight().' mm,';
                $html .= $calc->getProductFormatWidthOpen().' mm x '.$calc->getProductFormatHeightOpen().' mm (offen)<br>';
                $html .= 'Nutzen pro Bogen: '.$calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT).',';
                $html .= 'Anzahl B&ouml;gen pro Auflage: '.printPrice($calc->getPagesAddContent() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT)).'<br>';
                $html .= 'B&ouml;gen insgesamt:';
                 
                $sheets = ceil($calc->getPagesAddContent() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT) * $calc->getAmount());
                $html .= printBigInt($sheets);
                $html .= ' + Zuschuss';
                $html .= printBigInt($calc->getPaperAddContentGrant());
                
                $sheets += $calc->getPaperAddContentGrant();
                $sheets_addcontent = $sheets;
                
                $html .= '<br>Papiergewicht:';
                 
                $area = $calc->getPaperAddContentWidth() * $calc->getPaperAddContentHeight();
                $html .= printPrice((($area * $calc->getPaperAddContentWeight() / 10000 / 100) * $sheets) / 1000);
                $html .= ' kg,';
                $html .= 'Papierpreis: '.printPrice($calc->getPaperAddContent()->getSumPrice($sheets)).' €<br>';
                $html .= 'Preisbasis: ';
                 
                if ($calc->getPaperAddContent()->getPriceBase() == Paper::PRICE_PER_100KG)
                    $html .= 'Preis pro 100 kg';
                else 
                    $html .= 'Preis pro 1000 B&ouml;gen';
            }
            
            $html .= '</td></tr><tr><td colspan="4">&nbsp;</td></tr><tr>';
            $html .= '<td valign="top"><b>zus. Inhalt 2:</b></td>';
            $html .= '<td valign="top">';
            
            if($calc->getPaperAddContent2()->getId()) {
                $html .= 'Bogenformat: '.$calc->getPaperAddContent2Width().' mm x '.$calc->getPaperAddContent2Height().' mm <br>';
                $html .= 'Produktformat: '.$calc->getProductFormatWidth().' mm x '.$calc->getProductFormatHeight().' mm,';
                $html .= $calc->getProductFormatWidthOpen().' mm x '.$calc->getProductFormatHeightOpen().' mm (offen)<br>';
                $html .= 'Nutzen pro Bogen: '.$calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2).',';
                $html .= 'Anzahl B&ouml;gen pro Auflage:';
                $html .= printPrice($calc->getPagesAddContent2() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2)).'<br>';
                $html .= 'B&ouml;gen insgesamt:';
                
                $sheets = ceil($calc->getPagesAddContent2() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2) * $calc->getAmount());
                $html .= printBigInt($sheets);
                $html .= ' + Zuschuss';
                $html .= printBigInt($calc->getPaperAddContent2Grant());
                
                $sheets += $calc->getPaperAddContent2Grant();
                $sheets_addcontent2 = $sheets;
                
                $html .= '<br>Papiergewicht:';
                 
                $area = $calc->getPaperAddContent2Width() * $calc->getPaperAddContent2Height();
                $html .= printPrice((($area * $calc->getPaperAddContent2Weight() / 10000 / 100) * $sheets) / 1000);
                $html .= ' kg,';
                $html .= 'Papierpreis: '.printPrice($calc->getPaperAddContent2()->getSumPrice($sheets)).' €<br>';
                $html .= 'Preisbasis: '; 
                
                if ($calc->getPaperAddContent2()->getPriceBase() == Paper::PRICE_PER_100KG)
                    $html .= 'Preis pro 100 kg';
                else
                    $html .= 'Preis pro 1000 B&ouml;gen';
            }
            
            $html .= '</td><td valign="top"><b>zus. Inhalt 3:</b></td><td valign="top">';
            
            if($calc->getPaperAddContent3()->getId()) {
                $html .= 'Bogenformat: '.$calc->getPaperAddContent3Width().' mm x '.$calc->getPaperAddContent3Height().' mm <br>';
                $html .= 'Produktformat: '.$calc->getProductFormatWidth().' mm x '.$calc->getProductFormatHeight().' mm,';
                $html .= $calc->getProductFormatWidthOpen().' mm x '.$calc->getProductFormatHeightOpen().' mm (offen)<br>';
                $html .= 'Nutzen pro Bogen: '.$calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3).',';
                $html .= 'Anzahl B&ouml;gen pro Auflage:';
                $html .= printPrice($calc->getPagesAddContent3() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3)).'<br>';
                $html .= 'B&ouml;gen insgesamt:';
                
                $sheets = ceil($calc->getPagesAddContent3() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3) * $calc->getAmount());
                $html .= printBigInt($sheets);
                $html .= ' + Zuschuss';
                $html .= printBigInt($calc->getPaperAddContent3Grant());
                
                $sheets += $calc->getPaperAddContent3Grant();
                $sheets_addcontent3 = $sheets;
                
                $html .= '<br>Papiergewicht:';
                 
                $area = $calc->getPaperAddContent3Width() * $calc->getPaperAddContent3Height();
                $html .= printPrice((($area * $calc->getPaperAddContent3Weight() / 10000 / 100) * $sheets) / 1000);
                $html .= ' kg,';
                $html .= 'Papierpreis: '.printPrice($calc->getPaperAddContent3()->getSumPrice($sheets)).' €<br>';
                $html .= 'Preisbasis: '; 
                
                if ($calc->getPaperAddContent3()->getPriceBase() == Paper::PRICE_PER_100KG)
                    $html .= 'Preis pro 100 kg';
                else
                    $html .= 'Preis pro 1000 B&ouml;gen';
            }
            
            $html .= '</td></tr>';
            
            if($calc->getPaperEnvelope()->getId()) {
                $html .= '<tr><td colspan="4">&nbsp;</td></tr><tr>';
                $html .= '<td valign="top"><b>Umschlag:</b></td><td valign="top">';
                $html .= 'Bogenformat: '.$calc->getPaperEnvelopeWidth().' mm x '.$calc->getPaperEnvelopeHeight().' mm <br>';
                $html .= 'Produktformat: '.$calc->getProductFormatWidth().' mm x '.$calc->getProductFormatHeight().' mm,';
                $html .= $calc->getEnvelopeWidthOpen().' mm x '.$calc->getEnvelopeHeightOpen().' mm (offen)<br>';
                $html .= 'Nutzen pro Bogen: '.$calc->getProductsPerPaper(Calculation::PAPER_ENVELOPE).',';
                $html .= 'Anzahl B&ouml;gen pro Auflage: '.printPrice($calc->getPagesEnvelope() / $calc->getProductsPerPaper(Calculation::PAPER_ENVELOPE)).'<br>';
                $html .= 'B&ouml;gen insgesamt:';

                $sheets = ceil($calc->getPagesEnvelope() / $calc->getProductsPerPaper(Calculation::PAPER_ENVELOPE) * $calc->getAmount());
                $html .= printBigInt($sheets);
                $html .= ' + Zuschuss';
                $html .= printBigInt($calc->getPaperEnvelopeGrant());
                
                $sheets += $calc->getPaperEnvelopeGrant();
                $sheets_envelope = $sheets;
                
                $html .= '<br>Papiergewicht:';
                 
                $area = $calc->getPaperEnvelopeWidth() * $calc->getPaperEnvelopeHeight();
                $html .= printPrice((($area * $calc->getPaperEnvelopeWeight() / 10000 / 100) * $sheets) / 1000);
                $html .= ' kg,';
                $html .= 'Papierpreis: '.printPrice($calc->getPaperEnvelope()->getSumPrice($sheets)).' €<br>';
                $html .= 'Preisbasis: ';

                if ($calc->getPaperEnvelope()->getPriceBase() == Paper::PRICE_PER_100KG)
                    $html .= 'Preis pro 100 kg';
                else
                    $html .= 'Preis pro 1000 B&ouml;gen';
                
                $html .= '</td></tr>';
            }

            $html .= '</table></div><br>';
            $html .= '<h3>Rohb&ouml;gen</h3><div class="outer"><table cellpadding="0" cellspacing="0" border="0" width="100%">';
            $html .= '<colgroup><col width="20%"><col width="20%"><col width="20%"><col width="20%"><col width="20%"></colgroup><tr>';
            foreach (Machineentry::getAllMachineentries($calc->getId(), Machineentry::ORDER_ID) as $me)
            {
                if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ||
                   $me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
                {
                    switch($me->getPart())
                    {
                        case Calculation::PAPER_CONTENT:
                            if ($calc->getFormat_in_content() != ""){
                                $format_in = explode("x", $calc->getFormat_in_content());
                                $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperContentHeight() * $calc->getPaperContentWidth()));
                                $roh2 = ceil($sheets_content / $roh);
                                $html .= '<td valign="top"><b>Inhalt:</b></br>';
                                $html .= 'Format: '.$calc->getFormat_in_content().' mm</br>';
                                $html .= 'Anzahl: '.$roh2.' B&ouml;gen</br>';
                                $html .= 'Rechnung: Abrunden(('.$format_in[0].' * '.$format_in[1].') / ('.$calc->getPaperContentHeight().' * '.$calc->getPaperContentWidth().')) / B&ouml;gen</br>';
                                $html .= '</td>';
                            } else {
                                $html .= '<td valign="top"><b>Inhalt:</b></td>';
                            }
                            break;
                        case Calculation::PAPER_ADDCONTENT:
                            if ($calc->getFormat_in_addcontent() != ""){
                                $format_in = explode("x", $calc->getFormat_in_addcontent());
                                $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperAddContentHeight() * $calc->getPaperAddContentWidth()));
                                $roh2 = ceil($sheets_addcontent / $roh);
                                $html .= '<td valign="top"><b>Zus. Inhalt:</b></br>';
                                $html .= 'Format: '.$calc->getFormat_in_addcontent().' mm</br>';
                                $html .= 'Anzahl: '.$roh2.' B&ouml;gen</br>';
                                $html .= 'Rechnung: Abrunden(('.$format_in[0].' * '.$format_in[1].') / ('.$calc->getPaperAddContentHeight().' * '.$calc->getPaperAddContentWidth().')) / B&ouml;gen</br>';
                                $html .= '</td>';
                            } else {
                                $html .= '<td valign="top"><b>Zus. Inhalt:</b></td>';
                            }
                            break;
                        case Calculation::PAPER_ADDCONTENT2:
                            if ($calc->getFormat_in_addcontent2() != ""){
                                $format_in = explode("x", $calc->getFormat_in_addcontent2());
                                $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperAddContent2Height() * $calc->getPaperAddContent2Width()));
                                $roh2 = ceil($sheets_addcontent2 / $roh);
                                $html .= '<td valign="top"><b>Zus. Inhalt 2:</b></br>';
                                $html .= 'Format: '.$calc->getFormat_in_addcontent2().' mm</br>';
                                $html .= 'Anzahl: '.$roh2.' B&ouml;gen</br>';
                                $html .= 'Rechnung: Abrunden(('.$format_in[0].' * '.$format_in[1].') / ('.$calc->getPaperAddContent2Height().' * '.$calc->getPaperAddContent2Width().')) / B&ouml;gen</br>';
                                $html .= '</td>';
                            } else {
                                $html .= '<td valign="top"><b>Zus. Inhalt 2:</b></td>';
                            }
                            break;
                        case Calculation::PAPER_ADDCONTENT3:
                            if ($calc->getFormat_in_addcontent3() != ""){
                                $format_in = explode("x", $calc->getFormat_in_addcontent3());
                                $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperAddContent3Height() * $calc->getPaperAddContent3Width()));
                                $roh2 = ceil($sheets_addcontent3 / $roh);
                                $html .= '<td valign="top"><b>Zus. Inhalt 3:</b></br>';
                                $html .= 'Format: '.$calc->getFormat_in_addcontent3().' mm</br>';
                                $html .= 'Anzahl: '.$roh2.' B&ouml;gen</br>';
                                $html .= 'Rechnung: Abrunden(('.$format_in[0].' * '.$format_in[1].') / ('.$calc->getPaperAddContent3Height().' * '.$calc->getPaperAddContent3Width().')) / B&ouml;gen</br>';
                                $html .= '</td>';
                            } else {
                                $html .= '<td valign="top"><b>Zus. Inhalt 3:</b></td>';
                            }
                            break;
                        case Calculation::PAPER_ENVELOPE:
                            if ($calc->getFormat_in_envelope() != ""){
                                $format_in = explode("x", $calc->getFormat_in_envelope());
                                $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperEnvelopeHeight() * $calc->getPaperEnvelopeWidth()));
                                $roh2 = ceil($sheets_envelope / $roh);
                                $html .= '<td valign="top"><b>Umschlag:</b></br>';
                                $html .= 'Format: '.$calc->getFormat_in_envelope().' mm</br>';
                                $html .= 'Anzahl: '.$roh2.' B&ouml;gen</br>';
                                $html .= 'Rechnung: Abrunden(('.$format_in[0].' * '.$format_in[1].') / ('.$calc->getPaperEnvelopeHeight().' * '.$calc->getPaperEnvelopeWidth().')) / B&ouml;gen</br>';
                                $html .= '</td>';
                            } else {
                                $html .= '<td valign="top"><b>Umschlag:</b></td>';
                            }
                            break;
                    }
                }
            }
            $html .= '</tr>';
            
            $html .= '<tr><td class="content_row_header" valign="top">Nutzen Rohb.</td><td class="content_row_clear">';
    		if ($calc->getPagesContent() > 0 && $calc->getPaperContent()->getId() > 0) {
    				$html .= '<b>Inhalt:</b></br>'; 
    				
                    $format_in = explode("x", $calc->getFormat_in_content());
                    $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperContentHeight() * $calc->getPaperContentWidth());
                	$html .= 'Nutzen: ' . (int)$roh_schnitte . '</br>';
			}
            $html .= '</td><td class="content_row_clear">';
            if ($calc->getPagesAddContent() > 0 && $calc->getPaperAddContent()->getId() > 0) {
    				$html .= '<b>Zus. Inhalt:</b></br>'; 
    				
                    $format_in = explode("x", $calc->getFormat_in_addcontent());
                    $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperAddContentHeight() * $calc->getPaperAddContentWidth());
                	$html .= 'Nutzen: ' . (int)$roh_schnitte . '</br>';
            }
            $html .= '</td><td class="content_row_clear">';
            if ($calc->getPagesAddContent2() > 0 && $calc->getPaperAddContent3()->getId() > 0) {
    				$html .= '<b>Zus. Inhalt 2:</b></br>'; 
    				
                    $format_in = explode("x", $calc->getFormat_in_addcontent2());
                    $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperAddContent2Height() * $calc->getPaperAddContent2Width());
                	$html .= 'Nutzen: ' . (int)$roh_schnitte . '</br>';
            }
            $html .= '</td><td class="content_row_clear">';
            if ($calc->getPagesAddContent2() > 0 && $calc->getPaperAddContent3()->getId() > 0) {
    				$html .= '<b>Zus. Inhalt 3:</b></br>'; 
    				
                    $format_in = explode("x", $calc->getFormat_in_addcontent3());
                    $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperAddContent3Height() * $calc->getPaperAddContent3Width());
                	$html .= 'Nutzen: ' . (int)$roh_schnitte . '</br>';
            }
            $html .= '</td><td class="content_row_clear">';
            if ($calc->getPagesEnvelope() > 0 && $calc->getPaperEnvelope()->getId() > 0) {
    				echo '<b>Umschlag:</b></br>'; 
    				
                    $format_in = explode("x", $calc->getFormat_in_envelope());
                    $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperEnvelopeHeight() * $calc->getPaperEnvelopeWidth());
                	$html .= 'Nutzen: ' . (int)$roh_schnitte . '</br>';
            }

            $html .= '</td></tr>';
            $html .= '</table></div><br><h3>Fertigungsprozess</h3>';
            $html .= '<div class="outer"><table cellpadding="0" cellspacing="0" border="0" width="100%">';
            $html .= '<colgroup><col width="15%"><col width="35%"><col width="15%"><col width="35%"></colgroup>';
            
            foreach(MachineGroup::getAllMachineGroups(MachineGroup::ORDER_POSITION) as $mg) {
                $machentries = Machineentry::getAllMachineentries($calc->getId(), Machineentry::ORDER_ID, $mg->getId()); 
                if(count($machentries) > 0)
                {
                    $html .= '<tr><td valign="top"><b>'.$mg->getName().'</b></td>';
                    $html .= '<td valign="top">';
                    
                    foreach($machentries as $me) {
                        $html .= 'Maschine '.$me->getMachine()->getName();
                        
                        if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ||
                               $me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET ||
                               $me->getMachine()->getType() == Machine::TYPE_FOLDER) {
                                switch($me->getPart())
                                {
                                    case Calculation::PAPER_CONTENT:
                                        $html .= '(Inhalt)';
                                        break;
                                    case Calculation::PAPER_ADDCONTENT:
                                        $html .= '(zus. Inhalt)';
                                        break;
                                    case Calculation::PAPER_ENVELOPE:
                                        $html .= '(Umschlag)';
                                        break;
                                    case Calculation::PAPER_ADDCONTENT2:
                                    	$html .= '(zus. Inhalt 2)';
                                    	break;
        							case Calculation::PAPER_ADDCONTENT3:
                                        $html .= '(zus. Inhalt 3)';
                                      	break;
                                }
                        }
                        $html .= '<br>';
                        
                        if($me->getMachine()->getType() == Machine::TYPE_CTP) { 
                            $html .= 'Anzahl Druckplatten: '.$calc->getPlateCount();
                            $html .= '<br>';
                        }
                        
                        if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) {
                            $html .= 'Druckart: ';
                            if ((int)$me->getUmschl() == 1)
                                $html .= 'Umschlagen';
                            elseif ((int)$me->getUmst() == 1)
                                $html .= 'Umscht&uuml;lpen';
                            else
                                $html .= 'Sch&ouml;n & Wider';
                            $html .= '</br>';
                        }
                        
                        $html .= 'Grundzeit: '.$me->getMachine()->getTimeBase().' min.,';
                        
                        if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) {
                            $html .= 'Einrichtzeit Druckplatten:';
                            $html .= $calc->getPlateCount($me) * $me->getMachine()->getTimePlatechange().' min.';
                            $html .= 'Laufzeit:';
                            $html .= $me->getTime() - ($calc->getPlateCount($me) * $me->getMachine()->getTimePlatechange()) - $me->getMachine()->getTimeBase().' min.';
                        } else {
                            $html .= 'Laufzeit inkl. maschinenspez. R&uuml;stzeiten:';
                            $html .= $me->getTime() - $me->getMachine()->getTimeBase().' min.';
                        }
                        
                        $html .= '<br>';
                        $html .= 'Zeit: '.$me->getTime().' min.,';
                        $html .= 'Preis: '.printPrice($me->getPrice()).' €<br>';
                        $html .= '<br>';
                    }
                    
                    $html .= '</td></tr>';
                }
            }
            
        	if (count($calc->getPositions())>0 && $calc->getPositions() != FALSE) {
        	    $html .= '<tr><td valign="top"><b>Zus. Positionen</b></td>';
        	    $html .= '<td>';

        	    foreach($calc->getPositions() as $pos){
        	        $html .= $pos->getComment() ." : ";
        	        $html .= printPrice($pos->getCalculatedPrice())." ".$_USER->getClient()->getCurrency()."<br/>";
        	        $html .= '<br/>';
        	    }
        	    $html .= '</td></tr>';
            }
            
            $html .= '</table></div><br><div class="outer"><table cellpadding="0" cellspacing="0" border="0" width="100%">';
            $html .= '<colgroup><col width="15%"><col width="35%"><col width="15%"><col width="35%"></colgroup><tr>';
            $html .= '<td valign="top"><b>Produktionskosten:</b></td><td valign="top"><b>';
            $html .= printPrice($calc->getPricesub()).' €</b>';
            $html .= '</td></tr></table></div><br>';
            
            $i++;
        }
        
        return $html;
    }
/******************************************** Statistik **********************************/
    
    static function getStatistic1monthTab($mincrtdat)
    {
        $retval = Array();
        global $DB;
       $sql = " select * from (
       (select DATE(FROM_UNIXTIME(crtdat)) as order_day, status, count(*) 'cc'
         from orders
         where
         status > 0 and
         crtdat >= {$mincrtdat} 
         group by 1,2)";
		
		$sql .= "UNION
		(select DATE(FROM_UNIXTIME(crtdate)) as order_day, status as 'status', count(*) 'cc'
		from collectiveinvoice
		where
		status > 0 and
		crtdate >= {$mincrtdat}
		group by 1,2)
		)as combined_table 
		group by 1,2";
        if($DB->num_rows($sql))
        {
           $retval = $DB->select($sql);
        }
    
        return $retval;
    }
    
    
    static function getStatistic1month($mincrtdat)
    {
        $retval = Array();
        global $DB;
       $sql = " select * from (
       (select crtdat, status, count(*) 'cc'
         from orders
         where
         status IN (1,2,3,4,5) and
         crtdat >= {$mincrtdat} 
         group by 1,2)";
		
		$sql .= "UNION
		(select crtdate as 'crtdat', status as 'status', count(*) 'cc'
		from collectiveinvoice
		where
		status IN (1,2,3,4,5) and
		crtdate >= {$mincrtdat}
		group by 1,2)
		)as combined_table 
		group by 1,2";
        if($DB->num_rows($sql))
        {
           $retval[] = $DB->select($sql);
        }
    
        return $retval;
    } 
    
    static function getCountAllOrders12months($mincrtdat)
    {
        $retval = Array();
        global $DB;
        $sql = "SELECT count(id) as 'count' FROM orders 
                WHERE status > 2 and
                crtdat >= {$mincrtdat}
                UNION
                SELECT count(id) as 'count' FROM collectiveinvoice 
                WHERE status > 2 and
                crtdate >= {$mincrtdat} ";
        if($DB->num_rows($sql))
        {  
                $retval = $DB->select($sql);
        }
        
        return $retval;
    }
    
    
    static function getStatistic12monthsTab($mincrtdat)
    {
        $retval = Array();
        global $DB;
       $sql = " select * from (
       (select FROM_UNIXTIME(crtdat, '%m.%Y') as 'order_month', count(*) 'cc'
         from orders
         where
         status > 2 and
         crtdat >= {$mincrtdat} 
         group by 1)";
		
		$sql .= "UNION
		(select FROM_UNIXTIME(crtdate, '%m.%Y') as 'order_month', count(*) 'cc'
		from collectiveinvoice
		where
        status > 2 and
		crtdate >= {$mincrtdat}
		group by 1)
		)as combined_table 
		";
        if($DB->num_rows($sql))
        {
           $retval = $DB->select($sql);
        }
    
        return $retval;
    } 
    
      
    static function getStatistic12months($mincrtdat)
    {
        $retval = Array();
        global $DB;
       $sql = " select * from (
       			(select crtdat, count(*) 'cc'
         		FROM orders
         		WHERE
         		status > 2 and
         		crtdat >= {$mincrtdat} 
         		group by 1)";
		
		$sql .= "UNION
				(select crtdate as 'crtdat', count(*) 'cc'
				FROM collectiveinvoice
				WHERE
        		status > 2 and
				crtdate >= {$mincrtdat}
				group by 1)
				)as combined_table 
				group by 1";
        if($DB->num_rows($sql))
        {
           $retval[] = $DB->select($sql);
        }
    
        return $retval;
    } 

   
    
static function getCountOrdersPerCust()
    {
        $retval = Array();
        global $DB;
        $sql = "SELECT count(id) as 'count', businesscontact_id as 'cust', YEAR(FROM_UNIXTIME(crtdat)) as 'year'  
                FROM orders 
                WHERE status > 2
                GROUP BY businesscontact_id, year";
        if($DB->num_rows($sql))
        {
           $retval = $DB->select($sql);
        }
    
        return $retval;
    }
    
    
static function getCountOrdersPerCustMonth($year)
    {
        $retval = Array();
        global $DB;
        $sql = "SELECT count(id) as 'count', businesscontact_id as 'cust', MONTH(FROM_UNIXTIME('crtdat')) as month
                FROM orders 
                WHERE status > 2
                AND YEAR(FROM_UNIXTIME(crtdat)) = {$year}
                GROUP BY businesscontact_id, month";
        if($DB->num_rows($sql))
        {
           $retval = $DB->select($sql);
        }
    
        return $retval;
    }

 /******************************* Ende Statistik *********************************************/

    
    /**
     * Liefert das Bild mit Pfad fuer den aktuellen Status
     * @return string
     */
    function getStatusImage(){
    	$img_path = "images/status/black.gif";
    	switch ($this->getStatus()){
    		case 0 : $img_path = "images/status/black.gif"; break;
    		case 1 : $img_path = "images/status/red.gif"; break;
    		case 2 : $img_path = "images/status/orange.gif"; break;
    		case 3 : $img_path = "images/status/yellow.gif"; break;
    		case 4 : $img_path = "images/status/lila.gif"; break;
    		case 5 : $img_path = "images/status/green.gif"; break;
    		default: $img_path = "images/status/black.gif"; break;
    	}
    	return $img_path;
    }
    
    /**
     * Liefert die Fremdleistungen (Maschinen-Einträge)
     * @return multitype:Machineentry
     */
    function getFL(){
    	$calculations = Calculation::getAllCalculations($this, Calculation::ORDER_AMOUNT);
    	
    	// Aktive Kalkulationen holen
    	$active_calcs = Array();
    	foreach ($calculations AS $calc){
    		if ($calc->getState() == 1){
    			$active_calcs[] = $calc;
    		}
    	}
    	
    	// Fremdleistungen aus den Maschinen-Eintraegen holen
    	$fl_maEntrys = Array();
    	foreach ($active_calcs AS $aCalc){
    		$mach_entries = Machineentry::getAllMachineentries($aCalc->getId());
    		foreach ($mach_entries AS $maEntry){
    			if ($maEntry->getSupplierStatus() > 0){
    				$fl_maEntrys[] = $maEntry;
    			}
    		}
    	}
    	return $fl_maEntrys;
    }

    /**
     * @return Order[]
     */
    public static function getOrderWithTickets() {
        global $DB;
        $q = 'SELECT t.tkt_order_id
                FROM tickets t
                WHERE t.tkt_order_id > 0
                GROUP BY t.tkt_order_id
                ORDER BY t.tkt_title ASC';
        $res = $DB->select($q);
        $orders = array();
        foreach($res as $r) {
            $orders[] = new Order($r['tkt_order_id']);
        }
        return $orders;
    }
    
    public function clearId()
    {
        $this->id = 0;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function getCustomer()
    {
        return $this->customer;
    }

    public function setCustomer($customer)
    {
        $this->customer = $customer;
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

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct($product)
    {
        $this->product = $product;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    public function getDeliveryAddress()
    {
        return $this->deliveryAddress;
    }

    public function setDeliveryAddress($deliveryAddress)
    {
        $this->deliveryAddress = $deliveryAddress;
    }

    public function getInvoiceAddress()
    {
        return $this->invoiceAddress;
    }

    public function setInvoiceAddress($invoiceAddress)
    {
        $this->invoiceAddress = $invoiceAddress;
    }

    public function getDeliveryTerms()
    {
        return $this->deliveryTerms;
    }

    public function setDeliveryTerms($deliveryTerms)
    {
        $this->deliveryTerms = $deliveryTerms;
    }

    public function getPaymentTerms()
    {
        return $this->paymentTerms;
    }

    public function getPaymentterm()
    {
        return $this->paymentTerms;
    }

    public function setPaymentTerms($paymentTerms)
    {
        $this->paymentTerms = $paymentTerms;
    }

    public function getDeliveryDate()
    {
        return $this->deliveryDate;
    }

    public function setDeliveryDate($deliveryDate)
    {
        $this->deliveryDate = $deliveryDate;
    }

    public function getDeliveryCost()
    {
        return $this->deliveryCost;
    }

    public function setDeliveryCost($deliveryCost)
    {
        $this->deliveryCost = $deliveryCost;
    }

    public function getTextOffer()
    {
        return $this->textOffer;
    }

    public function setTextOffer($textOffer)
    {
        $this->textOffer = $textOffer;
    }

    public function getTextOfferconfirm()
    {
        return $this->textOfferconfirm;
    }

    public function setTextOfferconfirm($textOfferconfirm)
    {
        $this->textOfferconfirm = $textOfferconfirm;
    }

    public function getTextInvoice()
    {
        return $this->textInvoice;
    }

    public function setTextInvoice($textInvoice)
    {
        $this->textInvoice = $textInvoice;
    }

    public function getCustContactperson()
    {
        return $this->custContactperson;
    }

    public function setCustContactperson($custContactperson)
    {
        $this->custContactperson = $custContactperson;
    }

    public function getCrtdat()
    {
        return $this->crtdat;
    }

    public function getUpddat()
    {
        return $this->upddat;
    }

    public function getCollectiveinvoiceId()
    {
        return $this->collectiveinvoiceId;
    }

    public function setCollectiveinvoiceId($collectiveinvoiceId)
    {
        $this->collectiveinvoiceId = $collectiveinvoiceId;
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

    public function getInvoiceAmount()
    {
        return $this->invoiceAmount;
    }

    public function setInvoiceAmount($invoiceAmount)
    {
        $this->invoiceAmount = $invoiceAmount;
    }

    public function getInvoicePriceUpdate()
    {
        return $this->invoicePriceUpdate;
    }

    public function setInvoicePriceUpdate($invoicePriceUpdate)
    {
        $this->invoicePriceUpdate = $invoicePriceUpdate;
    }

    public function getDeliveryAmount()
    {
        return $this->deliveryAmount;
    }

    public function setDeliveryAmount($deliveryAmount)
    {
        $this->deliveryAmount = $deliveryAmount;
    }

    public function getLabelBoxAmount()
    {
        return $this->labelBoxAmount;
    }

    public function setLabelBoxAmount($labelBoxAmount)
    {
        $this->labelBoxAmount = $labelBoxAmount;
    }

    public function getLabelTitle()
    {
        return $this->labelTitle;
    }

    public function setLabelTitle($labelTitle)
    {
        $this->labelTitle = $labelTitle;
    }

    public function getLabelLogoActive()
    {
        return $this->labelLogoActive;
    }

    public function setLabelLogoActive($labelLogoActive)
    {
        $this->labelLogoActive = $labelLogoActive;
    }

    public function getLabelPalletAmount()
    {
        return $this->labelPalletAmount;
    }

    public function setLabelPalletAmount($labelPalletAmount)
    {
        $this->labelPalletAmount = $labelPalletAmount;
    }

	public function getShowProduct()
	{
	    return $this->showProduct;
	}

	public function setShowProduct($showProduct)
	{
	    $this->showProduct = $showProduct;
	}

	public function getProductName()
	{
	    return $this->productName;
	}

	public function setProductName($productName)
	{
	    $this->productName = $productName;
	}

    public function getShowPricePer1000()
    {
        return $this->showPricePer1000;
    }

    public function setShowPricePer1000($showPricePer1000)
    {
        $this->showPricePer1000 = $showPricePer1000;
    }

	public function getPaperOrderBoegen()
	{
	    return $this->paper_order_boegen;
	}

	public function setPaperOrderBoegen($paper_order_boegen)
	{
	    $this->paper_order_boegen = $paper_order_boegen;
	}

	public function getPaperOrderPrice()
	{
	    return $this->paper_order_price;
	}

	public function setPaperOrderPrice($paper_order_price)
	{
	    $this->paper_order_price = $paper_order_price;
	}

	public function getPaperOrderSupplier()
	{
	    return $this->paper_order_supplier;
	}

	public function setPaperOrderSupplier($paper_order_supplier)
	{
	    $this->paper_order_supplier = $paper_order_supplier;
	}

	public function getPaperOrderCalc()
	{
	    return $this->paper_order_calc;
	}

	public function setPaperOrderCalc($paper_order_calc)
	{
	    $this->paper_order_calc = $paper_order_calc;
	}
	
	/**
     * @return the $beilagen
     */
    public function getBeilagen()
    {
        return $this->beilagen;
    }

	/**
     * @param field_type $beilagen
     */
    public function setBeilagen($beilagen)
    {
        $this->beilagen = $beilagen;
    }
    
	/**
     * @return the $crtusr
     */
    public function getCrtusr()
    {
        return $this->crtusr;
    }
    
	/**
     * @return the $articleid
     */
    public function getArticleid()
    {
        return $this->articleid;
    }

	/**
     * @param field_type $articleid
     */
    public function setArticleid($articleid)
    {
        $this->articleid = $articleid;
    }
	
}
         
?>