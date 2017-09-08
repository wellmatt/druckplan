<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       22.01.2014
// Copyright:     2012-14 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------


class Client {
    const ORDER_NAME = "client_name";
    const ORDER_ID = "id";
    
    const NUMBER_ORDER = 1;
    const NUMBER_OFFER = 2;
    const NUMBER_OFFERCONFIRM = 3;
    const NUMBER_DELIVERY = 4;
    const NUMBER_INVOICE = 5;
    const NUMBER_REVERT = 6;
    const NUMBER_WARNING = 7;
    const NUMBER_WORK = 8; // Drucktasche
    const NUMBER_PAPER_ORDER = 9; // Papier Bestellung
    const NUMBER_COLINV = 10;
    const NUMBER_SUPORDER = 11;
    const NUMBER_BULKLETTER = 12;

    private $id;
    private $name;
    private $street1;
    private $street2;
    private $street3;
    private $postcode;
    private $city;
    private $phone;
    private $fax;
    private $country;
    private $email;
    private $website;
    private $bank_name;
    private $bank_kto;
    private $bank_blz;
    private $bank_iban;
    private $bank_bic;
    private $gericht;
    private $steuernummer;
    private $ustid;
    private $active;
    private $currency;
    private $decimal;
    private $thousand;
    private $taxes = 0;
    private $margin = 0;
    
    private $bankName2;
    private $bankIban2;
    private $bankBic2;
    private $bankName3;
    private $bankIban3;
    private $bankBic3;

    // numbers + counter

    private $number_format_order;
    private $number_counter_order;
    private $number_format_colinv;
    private $number_counter_colinv;
    private $number_format_offer;
    private $number_counter_offer;
    private $number_format_offerconfirm;
    private $number_counter_offerconfirm;
    private $number_format_delivery;
    private $number_counter_delivery;
    private $number_format_paper_order;
    private $number_counter_paper_order;
    private $number_format_invoice;
    private $number_counter_invoice;
    private $number_format_revert;
    private $number_counter_revert;
    private $number_format_warning;
    private $number_counter_warning;
    private $number_format_work;
    private $number_counter_work;
    private $number_format_suporder;
    private $number_counter_suporder;
    private $number_counter_customer;
    private $number_format_bulkletter;
    private $number_counter_bulkletter;

    private $uptdate = 0;
    private $uptuser = 0;
     
    function __construct($id = 0) {
        global $DB;
        global $_USER;



        if ($id > 0){
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
                            if(is_object($cached->{$method}()) === false) {
                                $this->{$var} = $cached->{$method}();
                            } else {
                                $class = get_class($cached->{$method}());
                                $this->{$var} = new $class($cached->{$method}()->getId());
                            }
                        } elseif (method_exists($this,$method2)){
                            if(is_object($cached->{$method2}()) === false) {
                                $this->{$var} = $cached->{$method2}();
                            } else {
                                $class = get_class($cached->{$method2}());
                                $this->{$var} = new $class($cached->{$method2}()->getId());
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
            if ($valid_cache === false)
            {
                $sql = " SELECT * FROM clients WHERE id = {$id} AND client_status = 1";
                if ($DB->num_rows($sql)){
                    $res = $DB->select($sql);
                    $this->id = $res[0]["id"];
                    $this->name = $res[0]["client_name"];
                    $this->street1 = $res[0]["client_street1"];
                    $this->street2 = $res[0]["client_street2"];
                    $this->street3 = $res[0]["client_street3"];
                    $this->postcode = $res[0]["client_postcode"];
                    $this->city = $res[0]["client_city"];
                    $this->phone = $res[0]["client_phone"];
                    $this->fax = $res[0]["client_fax"];
                    $this->email = $res[0]["client_email"];
                    $this->website = $res[0]["client_website"];
                    $this->bank_name = $res[0]["client_bank_name"];
                    $this->bank_kto = $res[0]["client_bank_kto"];
                    $this->bank_blz = $res[0]["client_bank_blz"];
                    $this->bank_iban = $res[0]["client_bank_iban"];
                    $this->bank_bic = $res[0]["client_bank_bic"];
                    $this->gericht = $res[0]["client_gericht"];
                    $this->steuernummer = $res[0]["client_steuernummer"];
                    $this->ustid = $res[0]["client_ustid"];
                    $this->country = new Country($res[0]["client_country"]);
                    $this->active = $res[0]["active"] == 1;
                    $this->currency = $res[0]["client_currency"];
                    $this->decimal = $res[0]["client_decimal"];
                    $this->thousand = $res[0]["client_thousand"];
                    $this->taxes = $res[0]["client_taxes"];
                    $this->margin = $res[0]["client_margin"];
                    $this->bankName2 = $res[0]["client_bank2"];
                    $this->bankBic2 = $res[0]["client_bic2"];
                    $this->bankIban2 = $res[0]["client_iban2"];
                    $this->bankName3 = $res[0]["client_bank3"];
                    $this->bankBic3 = $res[0]["client_bic3"];
                    $this->bankIban3 = $res[0]["client_iban3"];

                    $this->uptdate = $res[0]["uptdate"];
                    $this->uptuser = $res[0]["uptuser"];

                    $this->number_format_order = $res[0]["number_format_order"];
                    $this->number_counter_order = $res[0]["number_counter_order"];
                    $this->number_format_colinv = $res[0]["number_format_colinv"];
                    $this->number_counter_colinv = $res[0]["number_counter_colinv"];
                    $this->number_format_offer = $res[0]["number_format_offer"];
                    $this->number_counter_offer = $res[0]["number_counter_offer"];
                    $this->number_format_offerconfirm = $res[0]["number_format_offerconfirm"];
                    $this->number_counter_offerconfirm = $res[0]["number_counter_offerconfirm"];
                    $this->number_format_delivery = $res[0]["number_format_delivery"];
                    $this->number_counter_delivery = $res[0]["number_counter_delivery"];
                    $this->number_format_paper_order = $res[0]["number_format_paper_order"];
                    $this->number_counter_paper_order = $res[0]["number_counter_paper_order"];
                    $this->number_format_invoice = $res[0]["number_format_invoice"];
                    $this->number_counter_invoice = $res[0]["number_counter_invoice"];
                    $this->number_format_revert = $res[0]["number_format_revert"];
                    $this->number_counter_revert = $res[0]["number_counter_revert"];
                    $this->number_format_warning = $res[0]["number_format_warning"];
                    $this->number_counter_warning = $res[0]["number_counter_warning"];
                    $this->number_format_work = $res[0]["number_format_work"];
                    $this->number_counter_work = $res[0]["number_counter_work"];
                    $this->number_format_suporder = $res[0]["number_format_suporder"];
                    $this->number_counter_suporder = $res[0]["number_counter_suporder"];
                    $this->number_counter_customer = $res[0]["number_counter_customer"];
                    $this->number_counter_bulkletter = $res[0]["number_counter_bulkletter"];
                    $this->number_format_bulkletter = $res[0]["number_format_bulkletter"];

                    Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
                }
            }
        } else {
            $this->country = new Country(22);
        }
    }
     
    // Get all available Clients
    static function getAllClients($order = self::ORDER_ID, $noinactive = false)
    {
        global $DB;
        $clients = Array();
        $sql = " SELECT * FROM clients WHERE client_status = 1";
        if ($noinactive === true)
            $sql .= " AND active = 1";
        $sql .= " ORDER BY {$order}";
        $res = $DB->select($sql);

        foreach ($res as $r)
        {
            $clients[] = new Client($r["id"]);
        }

        return $clients;
    }
     
     
    function save() {
        global $DB;
        global $_USER;
        $time = time();
        $user = $_USER->getId();
        $this->setUptuser($user);
        $this->setUptdate($time);
        if ($this->id > 0)
        {
            $sql = "UPDATE clients SET
            client_name = '{$this->name}',
            client_street1 = '{$this->street1}',
            client_street2 = '{$this->street2}',
            client_street3 = '{$this->street3}',
            client_postcode = '{$this->postcode}',
            client_city = '{$this->city}',
            client_phone = '{$this->phone}',
            client_fax = '{$this->fax}',
            client_email = '{$this->email}',
            client_website = '{$this->website}',
            client_bank_name = '{$this->bank_name}',
            client_bank_blz = '{$this->bank_blz}',
            client_bank_kto = '{$this->bank_kto}',
            client_bank_iban = '{$this->bank_iban}',
            client_bank_bic = '{$this->bank_bic}',
            client_gericht = '{$this->gericht}',
            client_steuernummer = '{$this->steuernummer}',
            client_ustid = '{$this->ustid}',
            client_country = {$this->country->getId()},
            client_decimal = '{$this->decimal}',
            client_thousand = '{$this->thousand}',
            client_currency = '{$this->currency}',
            client_taxes = {$this->taxes},
            client_margin = {$this->margin},
            active = {$this->active}, 
            client_bank2 = '{$this->bankName2}', 
			client_bic2  = '{$this->bankBic2}', 
            client_iban2 = '{$this->bankIban2}', 
            client_bank3 = '{$this->bankName3}', 
            client_bic3  = '{$this->bankBic3}', 
			client_iban3 = '{$this->bankIban3}',
			
			uptuser = '{$this->uptuser}', 
			uptdate = '{$this->uptdate}', 

            number_format_order  = '{$this->number_format_order}', 
            number_counter_order  = '{$this->number_counter_order}', 
            number_format_colinv  = '{$this->number_format_colinv}', 
            number_counter_colinv  = '{$this->number_counter_colinv}', 
            number_format_offer  = '{$this->number_format_offer}', 
            number_counter_offer  = '{$this->number_counter_offer}', 
            number_format_offerconfirm  = '{$this->number_format_offerconfirm}', 
            number_counter_offerconfirm  = '{$this->number_counter_offerconfirm}', 
            number_format_delivery  = '{$this->number_format_delivery}', 
            number_counter_delivery  = '{$this->number_counter_delivery}', 
            number_format_paper_order  = '{$this->number_format_paper_order}', 
            number_counter_paper_order  = '{$this->number_counter_paper_order}', 
            number_format_invoice  = '{$this->number_format_invoice}', 
            number_counter_invoice  = '{$this->number_counter_invoice}', 
            number_format_revert  = '{$this->number_format_revert}', 
            number_counter_revert  = '{$this->number_counter_revert}', 
            number_format_warning  = '{$this->number_format_warning}', 
            number_counter_warning  = '{$this->number_counter_warning}', 
            number_format_work  = '{$this->number_format_work}', 
            number_counter_work  = '{$this->number_counter_work}', 
            number_format_suporder  = '{$this->number_format_suporder}', 
            number_counter_suporder  = '{$this->number_counter_suporder}', 
            number_counter_customer = '{$this->number_counter_customer}', 
            number_counter_bulkletter = '{$this->number_counter_bulkletter}', 
            number_format_bulkletter = '{$this->number_format_bulkletter}' 

            WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
        } else
        {
            $sql = "INSERT INTO clients
            (client_name, client_street1, client_street2, client_street3, 
            client_postcode, client_city, client_phone, client_fax, client_email, 
            client_website, client_bank_name, client_bank_blz, client_bank_kto, 
            client_bank_iban, client_bank_bic, client_gericht, client_steuernummer, 
            client_ustid, client_country, active, client_decimal, client_thousand, 
            client_currency, client_taxes, client_margin, 
            client_bank2, client_bic2, client_iban2, 
            client_bank3, client_bic3, client_iban3, 
            number_format_order, number_counter_order, number_format_colinv, 
            number_counter_colinv, number_format_offer, number_counter_offer, 
            number_format_offerconfirm, number_counter_offerconfirm, number_format_delivery, 
            number_counter_delivery, number_format_paper_order, number_counter_paper_order, 
            number_format_invoice, number_counter_invoice, number_format_revert, number_counter_revert, 
            number_format_warning, number_counter_warning, number_format_work, number_counter_work, 
            number_format_suporder, number_counter_suporder, number_counter_customer, 
            number_counter_bulkletter, number_format_bulkletter, uptuser, uptdate )
            VALUES
            ('{$this->name}', '{$this->street1}', '{$this->street2}', '{$this->street3}',
            '{$this->postcode}', '{$this->city}', '{$this->phone}', '{$this->fax}', '{$this->email}',
            '{$this->website}', '{$this->bank_name}', '{$this->bank_blz}', '{$this->bank_kto}',
            '{$this->bank_iban}', '{$this->bank_bic}', '{$this->gericht}', '{$this->steuernummer}',
            '{$this->ustid}', {$this->country->getId()}, {$this->active}, '{$this->decimal}', '{$this->thousand}',
            '{$this->currency}', {$this->taxes}, {$this->margin}, 
            '{$this->bankName2}', '{$this->bankBic2}', '{$this->bankIban2}', 
            '{$this->bankName3}', '{$this->bankBic3}', '{$this->bankIban3}',
            '{$this->number_format_order}','{$this->number_counter_order}','{$this->number_format_colinv}',
            '{$this->number_counter_colinv}','{$this->number_format_offer}','{$this->number_counter_offer}',
            '{$this->number_format_offerconfirm}','{$this->number_counter_offerconfirm}','{$this->number_format_delivery}',
            '{$this->number_counter_delivery}','{$this->number_format_paper_order}','{$this->number_counter_paper_order}',
            '{$this->number_format_invoice}','{$this->number_counter_invoice}','{$this->number_format_revert}',
            '{$this->number_counter_revert}','{$this->number_format_warning}','{$this->number_counter_warning}',
            '{$this->number_format_work}','{$this->number_counter_work}', '{$this->number_format_suporder}','{$this->number_counter_suporder}',
            '{$this->number_counter_customer}', '{$this->number_counter_bulkletter}', '{$this->number_format_bulkletter}', '{$this->uptuser}', '{$this->uptdate}')";
            $res = $DB->no_result($sql);
             
            if ($res)
            {
                $sql = " SELECT max(id) id FROM clients";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
            }
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
        $sql = "UPDATE clients SET client_status = 0 WHERE id = {$this->id}";
        $res = $DB->no_result($sql);
        unset($this);
        if ($res){
            Cachehandler::removeCache(Cachehandler::genKeyword($this));
            return true;
        }
        else
            return false;
    }

    /**
     * Increments number counter in client table
     * DAFUQ Stupid glgn FIX!!!
     * @param $type
     */
    function incrementOrderNumber($type){
        global $DB;

        $fields = [
            self::NUMBER_ORDER => ['number_format_order','number_counter_order'],
            self::NUMBER_COLINV => ['number_format_colinv','number_counter_colinv'],
            self::NUMBER_OFFER => ['number_format_offer','number_counter_offer'],
            self::NUMBER_OFFERCONFIRM => ['number_format_offerconfirm','number_counter_offerconfirm'],
            self::NUMBER_DELIVERY => ['number_format_delivery','number_counter_delivery'],
            self::NUMBER_PAPER_ORDER => ['number_format_paper_order','number_counter_paper_order'],
            self::NUMBER_INVOICE => ['number_format_invoice','number_counter_invoice'],
            self::NUMBER_REVERT => ['number_format_revert','number_counter_revert'],
            self::NUMBER_WARNING => ['number_format_warning','number_counter_warning'],
            self::NUMBER_WORK => ['number_format_work','number_counter_work'],
            self::NUMBER_SUPORDER => ['number_format_suporder','number_counter_suporder'],
            self::NUMBER_BULKLETTER => ['number_format_bulkletter','number_counter_bulkletter']
        ];
        $nfield = $fields[$type][1];

        $sql = " BEGIN WORK";
        $DB->no_result($sql);

        $sql = " update clients
                        set {$nfield} = {$nfield} +1
                    where
                        id = {$this->id}";
        $res = $DB->no_result($sql);

        $sql = "COMMIT";
        $DB->no_result($sql);
    }


    /**
     * Creates new number from client table
     * DAFUQ Stupid glgn FIX!!!
     * @param $type
     * @return string|boolean
     */
    function generateOrderNumber($type){
        global $DB;

        $fields = [
            self::NUMBER_ORDER => ['number_format_order','number_counter_order'],
            self::NUMBER_COLINV => ['number_format_colinv','number_counter_colinv'],
            self::NUMBER_OFFER => ['number_format_offer','number_counter_offer'],
            self::NUMBER_OFFERCONFIRM => ['number_format_offerconfirm','number_counter_offerconfirm'],
            self::NUMBER_DELIVERY => ['number_format_delivery','number_counter_delivery'],
            self::NUMBER_PAPER_ORDER => ['number_format_paper_order','number_counter_paper_order'],
            self::NUMBER_INVOICE => ['number_format_invoice','number_counter_invoice'],
            self::NUMBER_REVERT => ['number_format_revert','number_counter_revert'],
            self::NUMBER_WARNING => ['number_format_warning','number_counter_warning'],
            self::NUMBER_WORK => ['number_format_work','number_counter_work'],
            self::NUMBER_SUPORDER => ['number_format_suporder','number_counter_suporder'],
            self::NUMBER_BULKLETTER => ['number_format_bulkletter','number_counter_bulkletter']
        ];

        $ffield = $fields[$type][0];
        $nfield = $fields[$type][1];

        $sql = "SELECT {$ffield}, {$nfield} 
                FROM clients 
                WHERE id = {$this->id}";
        $ordernumber = $DB->select($sql);

        if($ordernumber[0][$ffield] != "")
        {
            $ordernumber = (int)$ordernumber[0]["{$nfield}"] +1;
            $order_number_format = $ordernumber[0]["{$ffield}"];
            $numlen = strlen($ordernumber) *-1;
            $order_number_format = substr($order_number_format, 0, $numlen);
            $order_number_format = $order_number_format.$ordernumber;
            $order_number_format = str_replace("X","0", $order_number_format);
            $order_number_format = str_replace("YYYY",date('Y'), $order_number_format);
            $order_number_format = str_replace("YY",date('y'), $order_number_format);
            $order_number_format = str_replace("MM",date('m'), $order_number_format);
            return $order_number_format;
        } else
            return false;
    }

    function createOrderNumber($type)
    {
        global $DB;
        switch($type) {
            case self::NUMBER_ORDER:
                $ffield = 'number_format_order';
                $nfield = 'number_counter_order';
                break;
            case self::NUMBER_COLINV:
                $ffield = 'number_format_colinv';
                $nfield = 'number_counter_colinv';
                break;
            case self::NUMBER_OFFER:
                $ffield = 'number_format_offer';
                $nfield = 'number_counter_offer';
                break;
            case self::NUMBER_OFFERCONFIRM:
                $ffield = 'number_format_offerconfirm';
                $nfield = 'number_counter_offerconfirm';
                break;
            case self::NUMBER_DELIVERY:
                $ffield = 'number_format_delivery';
                $nfield = 'number_counter_delivery';
                break;
            case self::NUMBER_PAPER_ORDER:
                $ffield = 'number_format_paper_order';
                $nfield = 'number_counter_paper_order';
                break;
            case self::NUMBER_INVOICE:
                $ffield = 'number_format_invoice';
                $nfield = 'number_counter_invoice';
                break;
            case self::NUMBER_REVERT:
                $ffield = 'number_format_revert';
                $nfield = 'number_counter_revert';
                break;
            case self::NUMBER_WARNING:
                $ffield = 'number_format_warning';
                $nfield = 'number_counter_warning';
                break;
            case self::NUMBER_WORK:
                $ffield = 'number_format_work';
                $nfield = 'number_counter_work';
                break;
            case self::NUMBER_SUPORDER:
                $ffield = 'number_format_suporder';
                $nfield = 'number_counter_suporder';
                break;
            case self::NUMBER_BULKLETTER:
                $ffield = 'number_format_bulkletter';
                $nfield = 'number_counter_bulkletter';
                break;
        }
        
        $sql = "SELECT {$ffield}, {$nfield} 
                FROM clients 
                WHERE id = {$this->id}";
        $ordernumber = $DB->select($sql);

        if($ordernumber[0][$ffield] != "")
        {
            $sql = " BEGIN WORK";
            $DB->no_result($sql);
            
            
            $sql = " update clients
                        set {$nfield} = {$nfield} +1
                    where
                        id = {$this->id}";
            $res = $DB->no_result($sql);
            
            $sql = "COMMIT";
            $DB->no_result($sql);
            
            if($res)
            {
                $ordernumber = (int)$ordernumber[0]["{$nfield}"] +1;
            
                $sql = " select {$ffield}
                            from clients
                        where
                            id = {$this->id}";
                $order_number_format = $DB->select($sql);
                $order_number_format = $order_number_format[0]["{$ffield}"];
            
                $numlen = strlen($ordernumber) *-1;
                $order_number_format = substr($order_number_format, 0, $numlen);
                $order_number_format = $order_number_format.$ordernumber;
                $order_number_format = str_replace("X","0", $order_number_format);
                $order_number_format = str_replace("YYYY",date('Y'), $order_number_format);
                $order_number_format = str_replace("YY",date('y'), $order_number_format);
                $order_number_format = str_replace("MM",date('m'), $order_number_format);
                return $order_number_format;
            }
        } else 
            return false;
    }
    
    /**
     * Funktion fuer das Generieren von Ticketnummern
     */
    function createTicketnumber(){
    	global $DB;
    	
    	$sql = "SELECT number_counter_ticket, ticketnumberreset
    			FROM clients
    			WHERE id = {$this->id}";
    	$tktnumber = $DB->select($sql);
    	$tktnumber = $tktnumber[0];
    	$tktcounter = $tktnumber["number_counter_ticket"];
    	$last_tkt_reset = (int)$tktnumber["ticketnumberreset"];
    	
    	// Wenn ein neuer Monat da ist, muss der Counter auf 2 gesetzt werden, weil das jetzige Ticket die 1 bekommt 
    	if ($last_tkt_reset == date("n")){
    		$sql = "UPDATE clients
    				SET number_counter_ticket = {$tktcounter} +1 ";
    	} else {
    		$sql = "UPDATE clients
    				SET number_counter_ticket = 2,
    				ticketnumberreset = ".date("n")." ";
    		$tktcounter = 1;
    	}
		$sql.="WHERE id = {$this->id}";
		// error_log($sql);
    	$res = $DB->no_result($sql);
    	
    	if($tktcounter < 10){
    		$tktcounter = "0".$tktcounter;
    	}
    	if($tktcounter < 100){
    		$tktcounter = "0".$tktcounter;
    	}
    	
    	if($tktcounter < 1000){
    		$tktcounter = "0".$tktcounter;
    	}
    	
    	if ($res){
    		return "TK-".date('y')."-".date('m')."-".$tktcounter;
    	}
    	return "-";
    }
    
    /**
     * Holt den aktuellen Zaehlerstand des Kreditoren-Zaehlers erhoeht ihn und gibt den aktuellen Stand aus
     * 
     * @return int | boolean
     */
    function generadeCreditorNumber(){
    	global $DB;
    	$retval = 0;
    	$sql = "SELECT number_counter_creditor
    			FROM clients
    			WHERE id = {$this->id}";
    	$cred_number = $DB->select($sql);
    	$cred_number = $cred_number[0]["number_counter_creditor"];
    	
    	$sql = "UPDATE clients
    			SET number_counter_creditor = ".($cred_number+1)."
    			WHERE id = {$this->id}";
    	//error_log($sql);
    	$res = $DB->no_result($sql);
    	if ($res){
    		return $cred_number;
    	}
    	return false;
    }
    
    /**
     * Holt den aktuellen Zaehlerstand des Debitoren-Zaehlers erhoeht ihn und gibt den aktuellen Stand aus
     *
     * @return int | boolean
     */
    function generadeDebitorNumber(){
    	global $DB;
    	$retval = 0;
    	$sql = "SELECT number_counter_debitor
    			FROM clients
    			WHERE id = {$this->id}";
    	$deb_number = $DB->select($sql);
    	$deb_number = $deb_number[0]["number_counter_debitor"];
   
    	$sql = "UPDATE clients
    			SET number_counter_debitor = ".($deb_number+1)."
    			WHERE id = {$this->id}";
    	//error_log($sql);
    	$res = $DB->no_result($sql);
    	if ($res){
    		return $deb_number;
    	}
    	return false;
    }
    
    /**
     * Holt den aktuellen Zaehlerstand des Kundennummer-Zaehlers erhoeht ihn und gibt den aktuellen Stand aus
     *
     * @return int | boolean
     */
    function generadeCustomerNumber(){
    	global $DB;
    	$retval = 0;
    	$sql = "SELECT number_counter_customer
    			FROM clients
    			WHERE id = {$this->id}";
    	$cust_number = $DB->select($sql);
    	$cust_number = $cust_number[0]["number_counter_customer"];
  
    	$sql = "UPDATE clients
        		SET number_counter_customer = ".($cust_number+1)."
        		WHERE id = {$this->id}";
        //error_log($sql);
    	$res = $DB->no_result($sql);
    	if ($res){
    		return $cust_number;
    	}
    	return false;
    }
    
     
    // returns the Name
    function getName() {
        return $this->name;
    }
     
    function getStreet1(){
        return $this->street1;
    }
    
    function getStreets() {
        $t = Array();
        $t[] = $this->street1;
        $t[] = $this->street2;
        $t[] = $this->street3;
        return $t;
    }
     
    function getPostcode() {
        return $this->postcode;
    }
     
    function getCity() {
        return $this->city;
    }
     
    function getPhone() {
        return $this->phone;
    }
     
    function getFax() {
        return $this->fax;
    }
     
    function getCountry() {
        return $this->country;
    }
     
    function getEmail() {
        return $this->email;
    }
     
    function getWebsite() {
        return $this->website;
    }
     
    function getBankName() {
        return $this->bank_name;
    }
     
    function getBankKto() {
        return $this->bank_kto;
    }
     
    function getBankBlz() {
        return $this->bank_blz;
    }
     
    function getBankIban() {
        return $this->bank_iban;
    }
     
    function getBankBic() {
        return $this->bank_bic;
    }
     
    function getGericht() {
        return $this->gericht;
    }
     
    function getSteuerNummer() {
        return $this->steuernummer;
    }
     
    function getUstId() {
        return $this->ustid;
    }
     
    // returns ID
    function getId() {
        return $this->id;
    }
     
    function isActive() {
        if ($this->active == 1)
            return true;
        else
            return false;
    }
     
    // Sets Name
    function setName($val) {
        $this->name = $val;
    }
     
    function setStreets($val) {
        $this->street1 = $val[0];
        $this->street2 = $val[1];
        $this->street3 = $val[2];
    }
     
    function setPostcode($val) {
        $this->postcode = $val;
    }
     
    function setCity($val) {
        $this->city = $val;
    }
     
    function setPhone($val) {
        $this->phone = $val;
    }
     
    function setFax($val) {
        $this->fax = $val;
    }
     
    function setCountry($val) {
        $this->country = $val;
    }
     
    function setActive($val) {
        if ($val == 1 || $val === true)
            $this->active = 1;
        else
            $this->active = 0;
    }
     
    function setEmail($val) {
        $this->email = $val;
    }
     
    function setWebsite($val) {
        $this->website = $val;
    }
     
    function setBankName($val) {
        $this->bank_name = $val;
    }
     
    function setBankKto($val) {
        $this->bank_kto = $val;
    }
     
    function setBankBlz($val) {
        $this->bank_blz = $val;
    }

    function setBankIban($val) {
        $this->bank_iban = $val;
    }
     
    function setBankBic($val) {
        $this->bank_bic = $val;
    }
     
    function setGericht($val) {
        $this->gericht = $val;
    }
     
    function setSteuerNummer($val) {
        $this->steuernummer = $val;
    }
     
    function setUstId($val) {
        $this->ustid = $val;
    }

    public function getCurrency()
    {
        return $this->currency;
    }

    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    public function getDecimal()
    {
        return $this->decimal;
    }

    public function setDecimal($decimal)
    {
        $this->decimal = $decimal;
    }

    public function getThousand()
    {
        return $this->thousand;
    }

    public function setThousand($thousand)
    {
        $this->thousand = $thousand;
    }

    public function getTaxes()
    {
        return $this->taxes;
    }

    public function setTaxes($taxes)
    {
        $this->taxes = $taxes;
    }

    public function getMargin()
    {
        return $this->margin;
    }

    public function setMargin($margin)
    {
        $this->margin = $margin;
    }

	public function getBankName2()
	{
	    return $this->bankName2;
	}

	public function setBankName2($bankName2)
	{
	    $this->bankName2 = $bankName2;
	}

	public function getBankIban2()
	{
	    return $this->bankIban2;
	}

	public function setBankIban2($bankIban2)
	{
	    $this->bankIban2 = $bankIban2;
	}

	public function getBankBic2()
	{
	    return $this->bankBic2;
	}

	public function setBankBic2($bankBic2)
	{
	    $this->bankBic2 = $bankBic2;
	}

	public function getBankName3()
	{
	    return $this->bankName3;
	}

	public function setBankName3($bankName3)
	{
	    $this->bankName3 = $bankName3;
	}

	public function getBankIban3()
	{
	    return $this->bankIban3;
	}

	public function setBankIban3($bankIban3)
	{
	    $this->bankIban3 = $bankIban3;
	}

	public function getBankBic3()
	{
	    return $this->bankBic3;
	}

	public function setBankBic3($bankBic3)
	{
	    $this->bankBic3 = $bankBic3;
	}
	
	/**
     * @return the $street2
     */
    public function getStreet2()
    {
        return $this->street2;
    }

	/**
     * @return the $street3
     */
    public function getStreet3()
    {
        return $this->street3;
    }

	/**
     * @return the $active
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @return mixed
     */
    public function getNumberFormatOrder()
    {
        return $this->number_format_order;
    }

    /**
     * @param mixed $number_format_order
     */
    public function setNumberFormatOrder($number_format_order)
    {
        $this->number_format_order = $number_format_order;
    }

    /**
     * @return mixed
     */
    public function getNumberCounterOrder()
    {
        return $this->number_counter_order;
    }

    /**
     * @param mixed $number_counter_order
     */
    public function setNumberCounterOrder($number_counter_order)
    {
        $this->number_counter_order = $number_counter_order;
    }

    /**
     * @return mixed
     */
    public function getNumberFormatColinv()
    {
        return $this->number_format_colinv;
    }

    /**
     * @param mixed $number_format_colinv
     */
    public function setNumberFormatColinv($number_format_colinv)
    {
        $this->number_format_colinv = $number_format_colinv;
    }

    /**
     * @return mixed
     */
    public function getNumberCounterColinv()
    {
        return $this->number_counter_colinv;
    }

    /**
     * @param mixed $number_counter_colinv
     */
    public function setNumberCounterColinv($number_counter_colinv)
    {
        $this->number_counter_colinv = $number_counter_colinv;
    }

    /**
     * @return mixed
     */
    public function getNumberFormatOffer()
    {
        return $this->number_format_offer;
    }

    /**
     * @param mixed $number_format_offer
     */
    public function setNumberFormatOffer($number_format_offer)
    {
        $this->number_format_offer = $number_format_offer;
    }

    /**
     * @return mixed
     */
    public function getNumberCounterOffer()
    {
        return $this->number_counter_offer;
    }

    /**
     * @param mixed $number_counter_offer
     */
    public function setNumberCounterOffer($number_counter_offer)
    {
        $this->number_counter_offer = $number_counter_offer;
    }

    /**
     * @return mixed
     */
    public function getNumberFormatOfferconfirm()
    {
        return $this->number_format_offerconfirm;
    }

    /**
     * @param mixed $number_format_offerconfirm
     */
    public function setNumberFormatOfferconfirm($number_format_offerconfirm)
    {
        $this->number_format_offerconfirm = $number_format_offerconfirm;
    }

    /**
     * @return mixed
     */
    public function getNumberCounterOfferconfirm()
    {
        return $this->number_counter_offerconfirm;
    }

    /**
     * @param mixed $number_counter_offerconfirm
     */
    public function setNumberCounterOfferconfirm($number_counter_offerconfirm)
    {
        $this->number_counter_offerconfirm = $number_counter_offerconfirm;
    }

    /**
     * @return mixed
     */
    public function getNumberFormatDelivery()
    {
        return $this->number_format_delivery;
    }

    /**
     * @param mixed $number_format_delivery
     */
    public function setNumberFormatDelivery($number_format_delivery)
    {
        $this->number_format_delivery = $number_format_delivery;
    }

    /**
     * @return mixed
     */
    public function getNumberCounterDelivery()
    {
        return $this->number_counter_delivery;
    }

    /**
     * @param mixed $number_counter_delivery
     */
    public function setNumberCounterDelivery($number_counter_delivery)
    {
        $this->number_counter_delivery = $number_counter_delivery;
    }

    /**
     * @return mixed
     */
    public function getNumberFormatPaperOrder()
    {
        return $this->number_format_paper_order;
    }

    /**
     * @param mixed $number_format_paper_order
     */
    public function setNumberFormatPaperOrder($number_format_paper_order)
    {
        $this->number_format_paper_order = $number_format_paper_order;
    }

    /**
     * @return mixed
     */
    public function getNumberCounterPaperOrder()
    {
        return $this->number_counter_paper_order;
    }

    /**
     * @param mixed $number_counter_paper_order
     */
    public function setNumberCounterPaperOrder($number_counter_paper_order)
    {
        $this->number_counter_paper_order = $number_counter_paper_order;
    }

    /**
     * @return mixed
     */
    public function getNumberFormatInvoice()
    {
        return $this->number_format_invoice;
    }

    /**
     * @param mixed $number_format_invoice
     */
    public function setNumberFormatInvoice($number_format_invoice)
    {
        $this->number_format_invoice = $number_format_invoice;
    }

    /**
     * @return mixed
     */
    public function getNumberCounterInvoice()
    {
        return $this->number_counter_invoice;
    }

    /**
     * @param mixed $number_counter_invoice
     */
    public function setNumberCounterInvoice($number_counter_invoice)
    {
        $this->number_counter_invoice = $number_counter_invoice;
    }

    /**
     * @return mixed
     */
    public function getNumberFormatRevert()
    {
        return $this->number_format_revert;
    }

    /**
     * @param mixed $number_format_revert
     */
    public function setNumberFormatRevert($number_format_revert)
    {
        $this->number_format_revert = $number_format_revert;
    }

    /**
     * @return mixed
     */
    public function getNumberCounterRevert()
    {
        return $this->number_counter_revert;
    }

    /**
     * @param mixed $number_counter_revert
     */
    public function setNumberCounterRevert($number_counter_revert)
    {
        $this->number_counter_revert = $number_counter_revert;
    }

    /**
     * @return mixed
     */
    public function getNumberFormatWarning()
    {
        return $this->number_format_warning;
    }

    /**
     * @param mixed $number_format_warning
     */
    public function setNumberFormatWarning($number_format_warning)
    {
        $this->number_format_warning = $number_format_warning;
    }

    /**
     * @return mixed
     */
    public function getNumberCounterWarning()
    {
        return $this->number_counter_warning;
    }

    /**
     * @param mixed $number_counter_warning
     */
    public function setNumberCounterWarning($number_counter_warning)
    {
        $this->number_counter_warning = $number_counter_warning;
    }

    /**
     * @return mixed
     */
    public function getNumberFormatWork()
    {
        return $this->number_format_work;
    }

    /**
     * @param mixed $number_format_work
     */
    public function setNumberFormatWork($number_format_work)
    {
        $this->number_format_work = $number_format_work;
    }

    /**
     * @return mixed
     */
    public function getNumberCounterWork()
    {
        return $this->number_counter_work;
    }

    /**
     * @param mixed $number_counter_work
     */
    public function setNumberCounterWork($number_counter_work)
    {
        $this->number_counter_work = $number_counter_work;
    }

    /**
     * @return mixed
     */
    public function getNumberCounterSuporder()
    {
        return $this->number_counter_suporder;
    }

    /**
     * @param mixed $number_counter_suporder
     */
    public function setNumberCounterSuporder($number_counter_suporder)
    {
        $this->number_counter_suporder = $number_counter_suporder;
    }

    /**
     * @return mixed
     */
    public function getNumberFormatSuporder()
    {
        return $this->number_format_suporder;
    }

    /**
     * @param mixed $number_format_suporder
     */
    public function setNumberFormatSuporder($number_format_suporder)
    {
        $this->number_format_suporder = $number_format_suporder;
    }

    /**
     * @return mixed
     */
    public function getNumberCounterCustomer()
    {
        return $this->number_counter_customer;
    }

    /**
     * @param mixed $number_counter_customer
     */
    public function setNumberCounterCustomer($number_counter_customer)
    {
        $this->number_counter_customer = $number_counter_customer;
    }

    /**
     * @return mixed
     */
    public function getNumberFormatBulkletter()
    {
        return $this->number_format_bulkletter;
    }

    /**
     * @param mixed $number_format_bulkletter
     */
    public function setNumberFormatBulkletter($number_format_bulkletter)
    {
        $this->number_format_bulkletter = $number_format_bulkletter;
    }

    /**
     * @return mixed
     */
    public function getNumberCounterBulkletter()
    {
        return $this->number_counter_bulkletter;
    }

    /**
     * @param mixed $number_counter_bulkletter
     */
    public function setNumberCounterBulkletter($number_counter_bulkletter)
    {
        $this->number_counter_bulkletter = $number_counter_bulkletter;
    }

    /**
     * @return int
     */
    public function getUptdate()
    {
        return $this->uptdate;
    }

    /**
     * @param int $uptdate
     */
    public function setUptdate($uptdate)
    {
        $this->uptdate = $uptdate;
    }

    /**
     * @return int
     */
    public function getUptuser()
    {
        return $this->uptuser;
    }

    /**
     * @param int $uptuser
     */
    public function setUptuser($uptuser)
    {
        $this->uptuser = $uptuser;
    }
}