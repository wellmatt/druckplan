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
    private $status;
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
    
    function __construct($id)
    {
        $this->deliveryAddress = new Address();
        $this->invoiceAddress = new Address();
        $this->deliveryTerms = new DeliveryTerms();
        $this->paymentTerms = new PaymentTerms();
        $this->custContactperson = new ContactPerson();
        $this->customer = new BusinessContact();
        $this->internContact = new User();
        
        global $DB;
        if($id > 0)
        {
            $sql = "SELECT * FROM orders WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
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
                if($res["delivery_address_id"] > 0)
                    $this->deliveryAddress = new Address($res["delivery_address_id"]);
                if($res["delivery_terms_id"])
                    $this->deliveryTerms = new DeliveryTerms($res["delivery_terms_id"]);
                if($res["invoice_address_id"] > 0)
                    $this->invoiceAddress = new Address($res["invoice_address_id"]);
                if($res["payment_terms_id"] > 0)
                    $this->paymentTerms = new PaymentTerms($res["payment_terms_id"]);
                if($res["cust_contactperson"] > 0)
                    $this->custContactperson = new ContactPerson($res["cust_contactperson"]);
                $this->textOffer = $res["text_offer"];
                $this->textOfferconfirm = $res["text_offerconfirm"];
                $this->textInvoice = $res["text_invoice"];
                $this->crtdat = $res["crtdat"];
                $this->upddat = $res["upddat"];
                $this->collectiveinvoiceId = $res["collectiveinvoice_id"];
                $this->internContact = new User($res["intern_contactperson"]);
                $this->custMessage = $res["cust_message"];
                $this->custSign	= $res["cust_sign"];
                $this->invoicePriceUpdate = $res["inv_price_update"];
                $this->invoiceAmount= $res["inv_amount"];
                $this->deliveryAmount = $res["deliv_amount"];
                $this->labelLogoActive = $res["label_logo_active"];
                $this->labelBoxAmount= $res["label_box_amount"];
                $this->labelTitle = $res["label_title"];
                $this->showProduct = $res["show_product"];
                $this->productName = $res["productname"];
                $this->showPricePer1000 = $res["show_price_per_thousand"];
                $this->paper_order_boegen = $res["paper_order_boegen"];
                $this->paper_order_price = $res["paper_order_price"];
                $this->paper_order_supplier = $res["paper_order_supplier"];
                $this->paper_order_calc = $res["paper_order_calc"];
            }
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
    static function getAllOrdersByCustomer($order = self::ORDER_TITLE, $customerId = 0){
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
                		show_price_per_thousand = {$this->showPricePer1000} 
                    WHERE id = {$this->id}";
			// echo $sql . "</br>";
            return $DB->no_result($sql);
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
                         show_price_per_thousand, paper_order_boegen, paper_order_price, paper_order_supplier, paper_order_calc )
                    VALUES
                        ('{$this->number}', 1, '{$this->customer->getId()}', $tmp_product,
                         '{$this->title}', '{$this->notes}', '0', '0', '0',
                         '{$this->paymentTerms->getId()}', UNIX_TIMESTAMP(), {$_USER->getId()}, 
            			 {$this->collectiveinvoiceId}, {$this->internContact->getId()}, '{$this->custMessage}',
            			 '{$this->custSign}', {$this->custContactperson->getId()}, {$this->invoiceAmount}, 
            			 {$this->invoicePriceUpdate}, {$this->deliveryAmount}, {$this->labelLogoActive}, 
            			 {$this->labelLogoActive}, '{$this->labelTitle}', {$this->showProduct}, '{$this->productName}', 
            			 {$this->showPricePer1000}, '{$this->paper_order_boegen}', '{$this->paper_order_price}', 
						 {$this->paper_order_supplier}, {$this->paper_order_calc} )";
            $res = $DB->no_result($sql);
//             echo $sql . "</br>";
            if($res)
            {
                $sql = "SELECT max(id) id FROM orders WHERE number = '{$this->number}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else 
                return false;
        }
    }

    function delete()
    {
        global $DB;
        if($this->id)
        {
            $sql = "UPDATE orders SET status = 0 WHERE id = {$this->id}";
            if($DB->no_result($sql))
            {
                unset($this);
                return true;
            } else
                return false;
        }
    }
    
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
}
         
?>