<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       23.09.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('contactperson.class.php');
require_once('address.class.php');
require_once('libs/modules/paymentterms/paymentterms.class.php');

class BusinessContact {
	const ORDER_NAME 	= "name1, name2";
	const ORDER_ID 		= " id ";
	const ORDER_TYPE 	= " customer, client ";
	const ORDER_CITY 	= " city ";
	const ORDER_HOMENAME= " t1.name1, t1.name2";
	const ORDER_CUST_NR	= " cust_number";
	
	// 17.04.2014 Sollkunde wird Interessent
	const FILTER_CUST_SOLL 	= " customer = 2 "; // IstKunde = 1, SollKunde = 2
	const FILTER_CUST_IST	= " customer = 1 "; // IstKunde = 1, SollKunde = 2
	const FILTER_CUST 		= " customer > 0 "; // Kunden und Interessenten
	const FILTER_SUPP 		= " supplier = 1 "; // Lieferant = 1
	const FILTER_ALL 		= " true ";
	const FILTER_NAME1 		= " name1 LIKE '";
	
	// Konstanten fuer den Konstruktor
	const LOADER_FULL = 0;
	const LOADER_MEDIUM = 1;
	const LOADER_BASICS = 2;
	
	private $id = 0;
	private $active; 		// Geloescht = 0, Aktiv = 1
    private $commissionpartner = 0;  //Provisionspartner
	private $customer; 		// IstKunde = 1, SollKunde = 2
	private $supplier; 		// Lieferant = 1
	private $client; 		// Mandant
	private $name1; 		// Firma
	private $name2; 		// Firmenzusatz
	private $address1;
	private $address2;
	private $zip;
	private $city;
	private $country;
	private $phone;
	private $fax;
	private $email;
	private $web;
	private $comment;
	private $shoplogin;
	private $shoppass;
	private $language; //Sprache
	private $lectorId = 0;
	private $discount = 0;
	private $paymentTerms = null;
	private $contactPersons = Array(); //Ansprechpartner
	private $deliveryAddresses = Array(); //Lieferadressen
	private $invoiceAddresses = Array(); //Rechnungsadressen
	private $loginexpire = 0;
	private $ticketenabled = 0;
	private $personalizationenabled = 0;
	private $articleenabled = 0;
	private $branche;
	private $type;
	private $produkte;
	private $bedarf;
	private $customernumber;		// Kundenummer
	private $numberatcustomer;		// Nummer beim Geschaeftskontakt
	private $debitor = 0;			// Debitornummer
	private $kreditor = 0;			// Kreditornummer
	private $bic;
	private $iban;
	
	private $alt_name1;  // Alternative-Adresse
	private $alt_name2; 
	private $alt_address1;
	private $alt_address2;
	private $alt_zip;
	private $alt_city;
	private $alt_country;
	private $alt_phone;
	private $alt_fax;
	private $alt_email;
	
	private $priv_name1; 	// Private Adresse
	private $priv_name2;
	private $priv_address1;
	private $priv_address2;
	private $priv_zip;
	private $priv_city;
	private $priv_country;
	private $priv_phone;
	private $priv_fax;
	private $priv_email;
	
	private $positiontitles = Array(); // für Personalisierung
	private $notifymailadr = Array(); // f�r gesonderte Benachrichtigungs Mails bei Bestellungen
	private $matchcode;
	private $supervisor;
	private $tourmarker;
	private $notes;
	private $salesperson;

	/* Konstruktor
     * Falls id uebergeben, werden die entsprechenden Daten direkt geladen
    */
	function __construct($id = 0, $loader = self::LOADER_FULL)
    {
        global $DB;
        global $_USER;
        
        $this->client = new Client(0);
        $this->paymentTerms = new PaymentTerms();
        $this->supervisor = new User();
        $this->salesperson = new User();

		$this->country = new Country(55); // Auf Deutschland setzen
		$this->alt_country = new Country(55); // Auf Deutschland setzen
		$this->priv_country = new Country(55); // Auf Deutschland setzen
		$this->language = new Translator(22); // Auf Deutsch setzen
        

//         $cached = Cachehandler::fromCache("obj_bc_" . $id);
		$cached = null;
        if (!is_null($cached))
        {
            $vars = array_keys(get_class_vars(get_class($this)));
            foreach ($vars as $var)
            {
                $method = "get".ucfirst($var);
                if (method_exists($this,$method))
                {
                    $this->$var = $cached->$method();
                } else {
                    echo "method: {$method}() not found!</br>";
                }
            }
            return true;
        }
        
        if ($id > 0 && is_null($cached))
        {
            $sql = " SELECT * FROM businesscontact WHERE id = {$id}";

            // sql returns only one record -> business contact is valid
            //if($DB->num_rows($sql) == 1){
                $res = $DB->select($sql);
                $this->id = $res[0]["id"];
                $this->active = $res[0]["active"];
                $this->commissionpartner = $res[0]["commissionpartner"];
                $this->customer = $res[0]["customer"];
                $this->supplier = $res[0]["supplier"];
                $this->name1 = $res[0]["name1"];
                $this->name2 = $res[0]["name2"];
                $this->address1 = $res[0]["address1"];
                $this->address2 = $res[0]["address2"];
                $this->zip = $res[0]["zip"];
                $this->city = $res[0]["city"];
                $this->phone = $res[0]["phone"];
                $this->fax = $res[0]["fax"];
                $this->email = $res[0]["email"];
                $this->web = $res[0]["web"];
                $this->comment = $res[0]["comment"];
                $this->lectorId = $res[0]["lector_id"];
                $this->discount = $res[0]["discount"];
                $this->customernumber = $res[0]["cust_number"];
                $this->matchcode = $res[0]["matchcode"];
                $this->supervisor = new User((int)$res[0]["supervisor"]);
                $this->salesperson = new User((int)$res[0]["salesperson"]);
                $this->tourmarker = $res[0]["tourmarker"];
                
                // Daten nur laden, wenn die Loader-Variable es hergibt
                if($loader <= self::LOADER_MEDIUM){
                	$this->client = new Client($res[0]["client"]);
                	$this->country = new Country ($res[0]["country"]);
        			$this->contactPersons = ContactPerson::getAllContactPersons($this);
        			$this->deliveryAddresses = Address::getAllAddresses($this,Address::ORDER_NAME,Address::FILTER_DELIV);
        			$this->invoiceAddresses = Address::getAllAddresses($this,Address::ORDER_NAME,Address::FILTER_INVC);
        			$this->language = new Translator($res[0]["language"]);
        			$this->paymentTerms = new PaymentTerms($res[0]["payment_terms"]);

        			$this->shoplogin = $res[0]["shop_login"];
        			$this->shoppass = $res[0]["shop_pass"];
        			$this->loginexpire = $res[0]["login_expire"];
        			$this->ticketenabled = $res[0]["ticket_enabled"];
        			$this->personalizationenabled= $res[0]["personalization_enabled"];
        			$this->articleenabled = $res[0]["enabled_article"];
        			$this->numberatcustomer = $res[0]["number_at_customer"];
        			$this->bic = $res[0]["bic"];
        			$this->iban = $res[0]["iban"];
        			
        			$this->alt_name1 = $res[0]["alt_name1"];
        			$this->alt_name2 = $res[0]["alt_name2"];
        			$this->alt_address1 = $res[0]["alt_address1"];
        			$this->alt_address2 = $res[0]["alt_address2"];
        			$this->alt_zip = $res[0]["alt_zip"];
        			$this->alt_city = $res[0]["alt_city"];
        			$this->alt_country = new Country ($res[0]["alt_country"]);
        			$this->alt_phone = $res[0]["alt_phone"];
        			$this->alt_fax = $res[0]["alt_fax"];
        			$this->alt_email = $res[0]["alt_email"];
        			
        			$this->priv_name1 = $res[0]["priv_name1"];
        			$this->priv_name2 = $res[0]["priv_name2"];
        			$this->priv_address1 = $res[0]["priv_address1"];
        			$this->priv_address2 = $res[0]["priv_address2"];
        			$this->priv_zip = $res[0]["priv_zip"];
        			$this->priv_city = $res[0]["priv_city"];
        			$this->priv_country = new Country ($res[0]["priv_country"]);
        			$this->priv_phone = $res[0]["priv_phone"];
        			$this->priv_fax = $res[0]["priv_fax"];
        			$this->priv_email = $res[0]["priv_email"];
        			
        			$this->debitor = $res[0]["debitor_number"];
        			$this->kreditor = $res[0]["kreditor_number"];
        			$this->positiontitles = unserialize($res[0]["position_titles"]);
        			$this->notifymailadr = unserialize($res[0]["notifymailadr"]);
                    $this->notes = $res[0]["notes"];
                }
        		
        		$this->branche = $res[0]["branche"];
        		$this->type = $res[0]["type"];
        		$this->produkte = $res[0]["produkte"];
        		$this->bedarf = $res[0]["bedarf"];

//         		Cachehandler::toCache("obj_bc_".$id, $this);
        	    return true;
                // sql returns more than one record, should not happen!
            //} 
            if ($DB->num_rows($sql) > 1)
            {
                $this->strError = "Mehr als einen Geschaeftskontakt gefunden";
                return false;
                // sql returns 0 rows -> login isn't valid
            }
        }
    }

	/**
	 * Liefert alle BusinessContacts des Systems
	 * @param string $order
	 * @param string $filter
	 * @param int $loader
	 * @return BusinessContact[]
	 */
    public static function getAllBusinessContacts($order = self::ORDER_ID, $filter = self::FILTER_ALL, $loader = self::LOADER_FULL){
    	global $_USER;
    	global $DB;
    	$businessContacts = Array();
    	$sql = "SELECT id FROM businesscontact WHERE active > 0 AND {$filter} AND client = {$_USER->getClient()->getID()} ORDER BY {$order}";
    	//$sql= "SELECT * FROM businesscontact";
    	if ($DB->num_rows($sql)){
    		$res = $DB->select($sql);
    		foreach ($res as $r)
    			$businessContacts[] = new BusinessContact($r["id"], $loader);
    	}
    
    	return $businessContacts;
    }
    
    /**
     * Liefert alle BusinessContacts des Systems auf eine andere Art und Weise wie getAllBusinessContacts
     * 
     * Diese Funktion holt die Bais-Daten aller Gesch.Kontakte in einer Datenbankabfrage, erstellt leere Objekte und  
     * fuellt diese dann sukzessive mit den Basis-Daten (z. fuer DropDown-Boxen)
     *
     * @param String $order : Reihenfolge
     * @param String $filter : Filter
     * @return multitype:BusinessContact
     */
    public static function getAllBusinessContactsForLists($order = self::ORDER_ID, $filter = self::FILTER_ALL, $filter_attrib = 0, $filter_item = 0){
    	global $_USER;
    	global $DB;
    	$businessContacts = Array();
    	$sql = "SELECT id, name1, name2, customer, supplier, cust_number, 
    					address1, address2, zip, city, country, phone, fax		
    			FROM businesscontact 
    			WHERE 
    			active > 0 
    			AND {$filter} 
    			AND client = {$_USER->getClient()->getID()} 
    			ORDER BY {$order}";
    	// echo $sql;
    	if ($DB->num_rows($sql)){
    		$res = $DB->select($sql);
    		foreach ($res as $r){
    			$tmp_busicon = new BusinessContact();
    			$tmp_busicon->setId((int)$r["id"]);
    			$tmp_busicon->setName1($r["name1"]);
    			$tmp_busicon->setName2($r["name2"]);
    			$tmp_busicon->setCustomer($r["customer"]);
    			$tmp_busicon->setCustomernumber($r["cust_number"]);
    			$tmp_busicon->setSupplier($r["supplier"]);
    			$tmp_busicon->setAddress1($r["address1"]);
    			$tmp_busicon->setAddress2($r["address2"]);
    			$tmp_busicon->setZip($r["zip"]);
    			$tmp_busicon->setCity($r["city"]);
    			$tmp_busicon->setCountry(new Country((int)$r["country"]));
    			$tmp_busicon->setPhone($r["phone"]);
    			$tmp_busicon->setFax($r["fax"]);
				if ($filter_attrib != 0 && $filter_item != 0) {
					if ($tmp_busicon->getIsAttributeItemActive($filter_attrib, $filter_item)) {
						$businessContacts[] = $tmp_busicon;
					}
				} else {
					$businessContacts[] = $tmp_busicon;
				}
    		}
    	}
    	return $businessContacts;
    }
    
    /**
     * Liefert alle BusinessContacts des Systems fuer die Startseite (Suche)
     * incl. Suche nach Ansprechpartnern
     *
     * @param unknown_type $order : Reihenfolge
     * @param STRING $search_string : Suchtext
     * @return multitype:BusinessContact
     */
	//
	// REMOVED 11.08.14 by ascherer
    			 // OR t2.address1 LIKE '%{$search_string}%' 
    			 // OR t2.address2 LIKE '%{$search_string}%' 
	 public static function getAllBusinessContactsForHome($order = self::ORDER_ID, $search_string)
{
    	global $_USER;
    	global $DB;
    	$businessContacts = Array();
    	$sql = "SELECT t1.id 
    			FROM businesscontact t1
    			LEFT OUTER JOIN contactperson t2 ON t1.id = t2.businesscontact  
    			WHERE t1.active > 0 
    			AND
    			(  t1.name1 LIKE '%{$search_string}%' 
    			 OR t1.name2 LIKE '%{$search_string}%' 
    			 OR t1.address1 LIKE '%{$search_string}%' 
    			 OR t1.address2 LIKE '%{$search_string}%'  
    			 OR t1.city LIKE '%{$search_string}%' 
    			 OR t1.zip LIKE '%{$search_string}%'
    			 OR t1.cust_number LIKE '%{$search_string}%'
    			 OR t1.phone LIKE '%{$search_string}%'       
    			 OR t2.name1 LIKE '%{$search_string}%' 
    			 OR t2.name2 LIKE '%{$search_string}%' 
 
    			 OR t2.city LIKE '%{$search_string}%' 
    			 OR t2.zip LIKE '%{$search_string}%' 
    			) 
    			AND t1.client = {$_USER->getClient()->getID()} 
    			GROUP BY t1.id  
    			ORDER BY {$order} ";
    	
    	if ($DB->num_rows($sql)){
	    	$res = $DB->select($sql);
	    	foreach ($res as $r){	    	
	    		$businessContacts[] = new BusinessContact($r["id"], self::LOADER_BASICS);
	    	}	    	
    	}
    	return $businessContacts;
    }
    
    
    public static function searchByLectorId($lectorId, $order = self::ORDER_ID, $filter = self::FILTER_ALL)
    {
        global $DB;
        global $_USER;
        $retval = Array();
        $sql = "SELECT * FROM businesscontact 
                WHERE active > 0 
                    AND {$filter} 
                    AND client = {$_USER->getClient()->getID()}
                    AND lector_id = {$lectorId} 
                ORDER BY {$order}";
        
        if ($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
        
            foreach ($res as $r)
                $retval[] = new BusinessContact($r["id"]);
        }
        
        return $retval;
    }
    
    static function getAllBusinessContactsByName1($loader = self::LOADER_BASICS)
    {
        $retval = Array();
        global $_USER;
        global $DB;
        $sql = "SELECT id,name1,name2 FROM businesscontact
                WHERE active > 0
        		AND client = {$_USER->getClient()->getID()}
                ORDER BY name1";
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new BusinessContact($r["id"], $loader);
            }
        }
    
        return $retval;
    }
    
    /**
     * Liefert den BusinessContact zum angegebenen Shoplogin-Namen
     * 
     * @return BusinessContact
     */
    static function getBusinessContactsByShoplogin($shoplogin){
    	global $DB;
    	$sql = "SELECT id FROM businesscontact
                WHERE active > 0 AND 
    			shop_login = '{$shoplogin}' ";
    	if($DB->num_rows($sql)){
    		$r = $DB->select($sql);
    		$retval = new BusinessContact($r[0]["id"]);
    	} else {
    		return false;
    	}
    	return $retval;
    }
    
    /**
     * Liefert einen Bussinesscontact zur�ck, falls die Shop-Login-Daten korrekt sind 
     * 
     * @param String $shoplogin
     * @param String $shoppass
     * @return BusinessContact|boolean
     */
    static function shoplogin($shoplogin, $shoppass){
    	global $DB;
    	$sql = "SELECT id FROM businesscontact
    			WHERE active > 0 AND
    			shop_login = '{$shoplogin}' AND
    			shop_pass =  '{$shoppass}' ";
    	if($DB->num_rows($sql)){
    		$r = $DB->select($sql);
    		return (new BusinessContact($r[0]["id"]));
    	}
    	return false;
    }
    
	public function getNameAsLine()
	{
		return $this->name1 . " " . $this->name2;
	}
	
	public function getAddressAsLine()
	{
		$retval = $this->address1;
        if($this->address2 != "")
            $retval .= "\n".$this->address2;
        if($this->postcode || $this->city)
            $retval .= "\n".strtoupper($this->country->getCode())."-".$this->zip." ".$this->city;
        return $retval;		   
	}
	
	public function isSupplier()
	{
		return $this->getSupplier() == 1;
	}

    public function isCommissionpartner()
    {
        return $this->getCommissionpartner() == 1;
    }
	
	public function isExistingCustomer()
	{
		return $this->getCustomer() == 1;
	}
	
	public function isPotentialCustomer()
	{
		return $this->getCustomer() == 2;
	}
	
	public function isSpezialCustomer()
	{
		return $this->getCustomer() == 3;
	}
	
	public function addContactPersons($contactPerson)
	{
		$contactPerson.setBusinessContact($this);
		$contactPerson.save();
		$this->contactPersons[] = $contactPerson;
	}
	
	public function delContactPersons($contactPerson)
	{
		foreach ($this->$contactPersons as $i => $cp)
		{
			if ($this->$contactPersons[$i]->getID() == $cp->getID())
			{
				$cp.delete();
				unset($this->$contactPersons[$i]);
			}
		}
	}
	
	public function delete()
	{
		global $DB;
		$sql = "UPDATE businesscontact SET active = 0 WHERE id = {$this->id}";
		$res = $DB->no_result($sql);
		unset($this);
		if($res)
			return true;
		else
			return false;
	}

	public function save()
	{
		global $DB;
		$positiontitles = serialize($this->positiontitles);
		$tmp_notify_mail_adr = serialize($this->notifymailadr);
		if ($this->id > 0)
		{
			$sql = " UPDATE businesscontact SET
		            active = '{$this->active}',
					commissionpartner = '{$this->commissionpartner}',
		            customer = '{$this->customer}',
		            supplier = '{$this->supplier}',
		            client = '{$this->client->getID()}',
		            name1 = '{$this->name1}',
		            name2 = '{$this->name2}',
		            address1 = '{$this->address1}',
		            address2 = '{$this->address2}',
		            zip = '{$this->zip}',
		            city = '{$this->city}',
		            country = '{$this->country->getId()}',
		            phone = '{$this->phone}',
		            fax = '{$this->fax}',
		            email = '{$this->email}',
		            alt_name1 = '{$this->alt_name1}',
		            alt_name2 = '{$this->alt_name2}',
		            alt_address1 = '{$this->alt_address1}',
		            alt_address2 = '{$this->alt_address2}',
		            alt_zip = '{$this->alt_zip}',
		            alt_city = '{$this->alt_city}',
		            alt_country = '{$this->alt_country->getId()}',
		            alt_phone = '{$this->alt_phone}',
		            alt_fax = '{$this->alt_fax}',
		            alt_email = '{$this->alt_email}',
		            priv_name1 = '{$this->priv_name1}',
		            priv_name2 = '{$this->priv_name2}',
		            priv_address1 = '{$this->priv_address1}',
		            priv_address2 = '{$this->priv_address2}',
		            priv_zip = '{$this->priv_zip}',
		            priv_city = '{$this->priv_city}',
		            priv_country = '{$this->priv_country->getId()}',
		            priv_phone = '{$this->priv_phone}',
		            priv_fax = '{$this->priv_fax}',
		            priv_email = '{$this->priv_email}',
		            web = '{$this->web}',
		            iban = '{$this->iban}', 
					bic= '{$this->bic}', 
		            comment = '{$this->comment}',
		            language = '{$this->language->getID()}',
		            lector_id = {$this->lectorId},
		            discount = {$this->discount},
		            payment_terms = {$this->paymentTerms->getId()},
		            shop_login = '{$this->shoplogin}',
		            shop_pass = '{$this->shoppass}',
		            login_expire = {$this->loginexpire},
		            ticket_enabled = {$this->ticketenabled}, 
		            personalization_enabled = {$this->personalizationenabled}, 
		            enabled_article = {$this->articleenabled}, 
		            branche = {$this->branche},   
		            type = {$this->type}, 
		            produkte = {$this->produkte}, 
		            bedarf = {$this->bedarf}, 
		    		kreditor_number = {$this->kreditor},
		    		debitor_number = {$this->debitor}, 
		            cust_number = '{$this->customernumber}', 
		            position_titles = '{$positiontitles}', 
		            notifymailadr = '{$tmp_notify_mail_adr}', 
        			number_at_customer = '{$this->numberatcustomer}',
        			supervisor = {$this->supervisor->getId()},
        			salesperson = {$this->salesperson->getId()},
        			matchcode = '{$this->matchcode}', 
        			notes = '{$this->notes}', 
        			tourmarker = '{$this->tourmarker}' 
					WHERE id = {$this->id}";
			$res = $DB->no_result($sql); //Aenderungen speichern
// 			echo $sql;
		}
		else
		{
			$sql = " INSERT INTO businesscontact
		            (active, commissionpartner, customer, supplier, client, name1,
		            name2, address1, address2, zip, city, country, phone,
		            fax, email, alt_name1, alt_name2, alt_address1, alt_address2, 
		            alt_zip, alt_city, alt_country, alt_phone, alt_fax, alt_email, 
		            priv_name1, priv_name2, priv_address1, priv_address2, priv_zip, priv_city, 
		            priv_country, priv_phone, priv_fax, priv_email,  web, 
		            comment, language, discount, iban, bic, 
		            payment_terms, lector_id, ticket_enabled, enabled_article, 
		            shop_login, shop_pass, login_expire, personalization_enabled,
		            branche, type, produkte, bedarf, 
		            cust_number, number_at_customer, kreditor_number, debitor_number, position_titles, notifymailadr,
		            matchcode, supervisor, tourmarker, notes, salesperson )
		            VALUES
		            ('{$this->active}', '{$this->commissionpartner}', '{$this->customer}', '{$this->supplier}', '{$this->client->getID()}', '{$this->name1}',
		            '{$this->name2}', '{$this->address1}', '{$this->address2}', '{$this->zip}', '{$this->city}', '{$this->country->getId()}', '{$this->phone}',
		            '{$this->fax}', '{$this->email}',  '{$this->alt_name1}', '{$this->alt_name2}', '{$this->alt_address1}', '{$this->alt_address2}', 
		            '{$this->alt_zip}', '{$this->alt_city}',  '{$this->alt_country->getId()}', '{$this->alt_phone}', '{$this->alt_fax}', '{$this->alt_email}',
		             '{$this->priv_name1}', '{$this->priv_name2}', '{$this->priv_address1}', '{$this->priv_address2}', '{$this->priv_zip}', '{$this->priv_city}', 
		            '{$this->priv_country->getId()}', '{$this->priv_phone}', '{$this->priv_fax}', '{$this->priv_email}', '{$this->web}', 
		            '{$this->comment}', {$this->language->getID()}, {$this->discount}, '{$this->iban}', '{$this->bic}',  
		            '{$this->paymentTerms->getId()}', '{$this->lectorId}', {$this->ticketenabled}, {$this->articleenabled}, 
		            '{$this->shoplogin}', '{$this->shoppass}', {$this->loginexpire}, {$this->personalizationenabled}, 
		            {$this->branche}, {$this->type}, {$this->produkte}, {$this->bedarf},
		            '{$this->customernumber}', '{$this->numberatcustomer}', {$this->kreditor}, {$this->debitor}, '{$positiontitles}', '{$tmp_notify_mail_adr}',
		            '{$this->matchcode}', {$this->supervisor->getId()}, '{$this->tourmarker}', '{$this->notes}', {$this->salesperson->getId()} )";
			$res = $DB->no_result($sql); //Datensatz neu einfuegen
			echo $DB->getLastError();
// 			echo "</br>" . $sql . "</br>";
//			prettyPrint($sql);
			if ($res)
            {
                $sql = " SELECT max(id) id FROM businesscontact";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
            }
		}
		if($res)
		{
//     		Cachehandler::toCache("obj_bc_".$this->id, $this);
			return true;
		}
		else
			return false;
	}
	
	/**
	 * ... liefert Alle aktivierten Optionen von Merkmalen zu einem Geschaeftskontakt
	 * 
	 * @return boolean|Array
	 */
	public function getActiveAttributeItems(){
		global $DB;
		$sql = "SELECT * FROM businesscontact_attributes 
				WHERE 
				businesscontact_id = {$this->id}";
		
		if($DB->num_rows($sql)){
			$res = $DB->select($sql);
			foreach ($res AS $r){
				$retval["{$r["attribute_id"]}_{$r["item_id"]}"] = $r["value"];
			}
		} else {
			return false;
		}
		return $retval;
	}
	
	/**
	 * ... liefert Alle aktivierten Optionen inkl. Input von Merkmalen zu einem Geschaeftskontakt
	 * 
	 * @return boolean|Array
	 */
	public function getActiveAttributeItemsInput(){
		global $DB;
		$sql = "SELECT * FROM businesscontact_attributes 
				WHERE 
				businesscontact_id = {$this->id}";
		
		if($DB->num_rows($sql)){
			$res = $DB->select($sql);
			foreach ($res AS $r){
				$retval["{$r["attribute_id"]}_{$r["item_id"]}"]["value"] = $r["value"];
				$retval["{$r["attribute_id"]}_{$r["item_id"]}"]["inputvalue"] = $r["inputvalue"];
			}
		} else {
			return false;
		}
		return $retval;
	}
	
	/**
	 * Entschiedet ob eine Option eines Merkmals aktiv ist
	 * 
	 * @param int $attribute_id
	 * @param int $item_id
	 * @return boolean
	 */
	public function getIsAttributeItemActive($attribute_id, $item_id){
		global $DB;
		$sql = "SELECT value FROM businesscontact_attributes
				WHERE
				businesscontact_id = {$this->id} AND  
				attribute_id = {$attribute_id} AND 
				item_id = {$item_id} ";
		
		if($DB->num_rows($sql)){
			$res = $DB->select($sql);
			if ($res[0]["value"] == 1){ 
				return true;
			}
		}
		return false;
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
	            $sql = "UPDATE businesscontact_attributes SET
	                    value = '{$item["value"]}', 
	                    inputvalue = '{$item["inputvalue"]}' 
	                    WHERE id = {$item["id"]}";
	            $DB->no_result($sql);
	        } else {
	            $sql = "INSERT INTO businesscontact_attributes
	                        (value, item_id, attribute_id, businesscontact_id, inputvalue )
	                    VALUES
	                        ({$item["value"]}, {$item["item_id"]}, {$item["attribute_id"]}, {$this->id}, '{$item["inputvalue"]}' )";
	            $DB->no_result($sql);
	        }
		}
	}
	
	/**
	 * ... loescht alle aktivierten Attribut-Optionen des GEschaeftskontakts
	 * @return boolean
	 */
	public function clearAttributes(){
		global $DB;
		$sql = "DELETE FROM businesscontact_attributes WHERE businesscontact_id = {$this->id} ";
		return $DB->no_result($sql);
	}
	
	/**
	 * liefert die Telefonnummer des Geschaeftskontakts in einer lesbaren Formatierung fuer das Snom-Telefon
	 *
	 * @param String $type : n=normal m=mobile
	 * @return String: wenn Nummer vorhanden; FALSE, wenn keine Nummer gefunden
	 */
	public function getPhoneForDial($type="n"){
	
		$tmp_phone = $this->phone; 
			
		// Wenn keine Nummer gefunden, FALSE zurueckgeben
		if ($tmp_phone == "" || $tmp_phone == NULL){
			return false;
		}
	
		$phone = str_replace(" ", "", $tmp_phone);  	// leerzeichen entfernen
		$phone = str_replace("+", "", $phone);			// + entfernen
		$phone = str_replace("/", "", $phone);			// / entfernen
		$phone = str_replace("-", "", $phone);			// - entfernen
		if (substr($phone, 0, 2) == "49"){
			$phone = "0".substr($phone, 2);	 // Landesvorwahl (0049) von Deutschladn durch 0 ersetzen
		} else {
			$phone = "00".$phone;							// 00 voransetzen, wenn Ausland
		}
		return $phone;
	}
	
	/**
	 * Liefert den Haupt-Ansprechpartner dieses Geschaeftkontakts
	 */
	public function getMainContactperson(){
		return ContactPerson::getMainContact($this);
	}
	
	/**
	 * Ueberpruefung, ob eine eingegebene Kunden-Nummer schon vergeben ist.
	 *
	 * @param String $number
	 * @return boolean : true, wenn vergeben
	 */
	static function checkCustomerNumber($number){
		global $DB;
	
		$sql = "SELECT id FROM businesscontact WHERE active > 0 AND cust_number = '{$number}'";
		// error_log($sql);
		if($DB->select($sql)){
			return true;
		}
		return false;
	}
	
	// ***************************** GETTER & SETTER ********************************************************
	
	public function getId()
	{
	    return $this->id;
	}
	
	public function setId($id)
	{
		$this->id = $id;
	}

	public function getActive()
	{
	    return $this->active;
	}

	public function setActive($active)
	{
	    $this->active = $active;
	}

    public function getCommissionpartner()
    {
        return $this->commissionpartner;
    }

    public function setCommissionpartner($commissionPartner)
    {
        $this->commissionpartner = $commissionPartner;
    }

	public function getCustomer()
	{
	    return $this->customer;
	}

	public function setCustomer($customer)
	{
	    $this->customer = $customer;
	}

	public function getSupplier()
	{
	    return $this->supplier;
	}

	public function setSupplier($supplier)
	{
	    $this->supplier = $supplier;
	}

	public function getClient()
	{
	    return $this->client;
	}

	public function setClient($client)
	{
	    $this->client = $client;
	}

	public function getName1()
	{
	    return $this->name1;
	}

	/**
	 * @param string $name1
	 */
	public function setName1($name1)
	{
	    $this->name1 = $name1;
	}

	public function getName2()
	{
	    return $this->name2;
	}

	public function setName2($name2)
	{
	    $this->name2 = $name2;
	}

	public function getAddress1()
	{
	    return $this->address1;
	}

	public function setAddress1($address1)
	{
	    $this->address1 = $address1;
	}

	public function getAddress2()
	{
	    return $this->address2;
	}

	public function setAddress2($address2)
	{
	    $this->address2 = $address2;
	}

	public function getCity()
	{
	    return $this->city;
	}

	public function setCity($city)
	{
	    $this->city = $city;
	}

	public function getZip()
	{
	    return $this->zip;
	}

	public function setZip($zip)
	{
	    $this->zip = $zip;
	}

	public function getPhone()
	{
	    return $this->phone;
	}

	public function setPhone($phone)
	{
	    $this->phone = $phone;
	}

	public function getFax()
	{
	    return $this->fax;
	}

	public function setFax($fax)
	{
	    $this->fax = $fax;
	}

	public function getEmail()
	{
	    return $this->email;
	}

	public function setEmail($email)
	{
	    $this->email = $email;
	}

	public function getWeb()
	{
	    return $this->web;
	}
	
	public function getWebForHref(){
		$ret="";
		if (substr($this->web, 0, 4) != "http"){
			$ret = "http://".$this->web;
		} else {
			$ret = $this->web; 	
		}
		return $ret;
	}

	public function setWeb($web)
	{
	    $this->web = $web;
	}

	public function getComment()
	{
	    return $this->comment;
	}

	public function setComment($comment)
	{
	    $this->comment = $comment;
	}

	public function getCountry()
	{
	    return $this->country;
	}

	public function setCountry($country)
	{
	    $this->country = $country;
	}

	public function getLanguage()
	{
	    return $this->language;
	}

	public function setLanguage($language)
	{
	    $this->language = $language;
	}

	public function getContactpersons()
	{
	    return $this->contactPersons;
	}

	public function setContactpersons($Contactpersons)
	{
	    $this->contactPersons = $Contactpersons;
	}

	public function getDeliveryAddresses()
	{
	    return $this->deliveryAddresses;
	}

	public function setDeliveryAddresses($deliveryAddresses)
	{
	    $this->deliveryAddress = $deliveryAddresses;
	}

	public function getInvoiceAddresses()
	{
	    return $this->invoiceAddresses;
	}

	public function setInvoiceAddresses($invoiceAddresses)
	{
	    $this->invoiceAddresses = $invoiceAddresses;
	}

	public function getLectorId()
	{
	    return $this->lectorId;
	}

	public function setLectorId($lectorId)
	{
	    $this->lectorId = $lectorId;
	}

	public function getDiscount()
	{
	    return $this->discount;
	}

	public function setDiscount($discount)
	{
	    $this->discount = $discount;
	}

	public function getPaymentTerms()
	{
	    return $this->paymentTerms;
	}

	public function setPaymentTerms($paymentTerms)
	{
	    $this->paymentTerms = $paymentTerms;
	}

	public function getShoplogin()
	{
	    return $this->shoplogin;
	}

	public function setShoplogin($shoplogin)
	{
	    $this->shoplogin = $shoplogin;
	}

	public function setShoppass($shoppass)
	{
	    $this->shoppass = $shoppass;
	}

	public function getUploadlogin()
	{
	    return $this->uploadlogin;
	}

	public function setUploadlogin($uploadlogin)
	{
	    $this->uploadlogin = $uploadlogin;
	}

	public function getUploadpassword()
	{
	    return $this->uploadpassword;
	}

	public function setUploadpassword($uploadpassword)
	{
	    $this->uploadpassword = $uploadpassword;
	}

	public function getShoppass()
	{
	    return $this->shoppass;
	}

	public function getLoginexpire()
	{
	    return $this->loginexpire;
	}

	public function setLoginexpire($loginexpire)
	{
	    $this->loginexpire = $loginexpire;
	}

	public function getTicketenabled()
	{
	    return $this->ticketenabled;
	}

	public function setTicketenabled($ticketenabled)
	{
	    $this->ticketenabled = $ticketenabled;
	}

	public function getPersonalizationenabled()
	{
	    return $this->personalizationenabled;
	}

	public function setPersonalizationenabled($personalizationenabled)
	{
	    $this->personalizationenabled = $personalizationenabled;
	}

	public function getBranche()
	{
	    return $this->branche;
	}

	public function setBranche($branche)
	{
	    $this->branche = $branche;
	}

	public function getType()
	{
	    return $this->type;
	}

	public function setType($type)
	{
	    $this->type = $type;
	}

	public function getProdukte()
	{
	    return $this->produkte;
	}

	public function setProdukte($produkte)
	{
	    $this->produkte = $produkte;
	}

	public function getBedarf()
	{
	    return $this->bedarf;
	}

	public function setBedarf($bedarf)
	{
	    $this->bedarf = $bedarf;
	}

	public function getAlt_name1()
	{
	    return $this->alt_name1;
	}

	public function setAlt_name1($alt_name1)
	{
	    $this->alt_name1 = $alt_name1;
	}

	public function getAlt_name2()
	{
	    return $this->alt_name2;
	}

	public function setAlt_name2($alt_name2)
	{
	    $this->alt_name2 = $alt_name2;
	}

	public function getAlt_address1()
	{
	    return $this->alt_address1;
	}

	public function setAlt_address1($alt_address1)
	{
	    $this->alt_address1 = $alt_address1;
	}

	public function getAlt_address2()
	{
	    return $this->alt_address2;
	}

	public function setAlt_address2($alt_address2)
	{
	    $this->alt_address2 = $alt_address2;
	}

	public function getAlt_zip()
	{
	    return $this->alt_zip;
	}

	public function setAlt_zip($alt_zip)
	{
	    $this->alt_zip = $alt_zip;
	}

	public function getAlt_city()
	{
	    return $this->alt_city;
	}

	public function setAlt_city($alt_city)
	{
	    $this->alt_city = $alt_city;
	}

	public function getAlt_country()
	{
	    return $this->alt_country;
	}

	public function setAlt_country($alt_country)
	{
	    $this->alt_country = $alt_country;
	}

	public function getAlt_phone()
	{
	    return $this->alt_phone;
	}

	public function setAlt_phone($alt_phone)
	{
	    $this->alt_phone = $alt_phone;
	}

	public function getAlt_fax()
	{
	    return $this->alt_fax;
	}

	public function setAlt_fax($alt_fax)
	{
	    $this->alt_fax = $alt_fax;
	}

	public function getAlt_email()
	{
	    return $this->alt_email;
	}

	public function setAlt_email($alt_email)
	{
	    $this->alt_email = $alt_email;
	}

	public function getPriv_name1()
	{
	    return $this->priv_name1;
	}

	public function setPriv_name1($priv_name1)
	{
	    $this->priv_name1 = $priv_name1;
	}

	public function getPriv_name2()
	{
	    return $this->priv_name2;
	}

	public function setPriv_name2($priv_name2)
	{
	    $this->priv_name2 = $priv_name2;
	}

	public function getPriv_address1()
	{
	    return $this->priv_address1;
	}

	public function setPriv_address1($priv_address1)
	{
	    $this->priv_address1 = $priv_address1;
	}

	public function getPriv_address2()
	{
	    return $this->priv_address2;
	}

	public function setPriv_address2($priv_address2)
	{
	    $this->priv_address2 = $priv_address2;
	}

	public function getPriv_zip()
	{
	    return $this->priv_zip;
	}

	public function setPriv_zip($priv_zip)
	{
	    $this->priv_zip = $priv_zip;
	}

	public function getPriv_city()
	{
	    return $this->priv_city;
	}

	public function setPriv_city($priv_city)
	{
	    $this->priv_city = $priv_city;
	}

	public function getPriv_country()
	{
	    return $this->priv_country;
	}

	public function setPriv_country($priv_country)
	{
	    $this->priv_country = $priv_country;
	}

	public function getPriv_phone()
	{
	    return $this->priv_phone;
	}

	public function setPriv_phone($priv_phone)
	{
	    $this->priv_phone = $priv_phone;
	}

	public function getPriv_fax()
	{
	    return $this->priv_fax;
	}

	public function setPriv_fax($priv_fax)
	{
	    $this->priv_fax = $priv_fax;
	}

	public function getPriv_email()
	{
	    return $this->priv_email;
	}

	public function setPriv_email($priv_email)
	{
	    $this->priv_email = $priv_email;
	}

	public function getCustomernumber()
	{
	    return $this->customernumber;
	}

	public function setCustomernumber($customernumber)
	{
	    $this->customernumber = $customernumber;
	}

	public function getNumberatcustomer()
	{
	    return $this->numberatcustomer;
	}

	public function setNumberatcustomer($numberatcustomer)
	{
	    $this->numberatcustomer = $numberatcustomer;
	}

	public function getArticleenabled()
	{
	    return $this->articleenabled;
	}

	public function setArticleenabled($articleenabled)
	{
	    $this->articleenabled = $articleenabled;
	}

	public function getDebitor()
	{
	    return $this->debitor;
	}

	public function setDebitor($debitor)
	{
	    $this->debitor = $debitor;
	}

	public function getKreditor()
	{
	    return $this->kreditor;
	}

	public function setKreditor($kreditor)
	{
	    $this->kreditor = $kreditor;
	}

	public function getBic()
	{
	    return $this->bic;
	}

	public function setBic($bic)
	{
	    $this->bic = $bic;
	}

	public function getIban()
	{
	    return $this->iban;
	}

	public function setIban($iban)
	{
	    $this->iban = $iban;
	}
	
    public function getPositionTitles()
    {
        return $this->positiontitles;
    }

    public function setPositionTitles($position_titles)
    {
        $this->positiontitles = $position_titles;
    }
    
    /**
     * @return the $notifymailadr
     */
    public function getNotifymailadr()
    {
        return $this->notifymailadr;
    }
    
    /**
     * @param multitype: $notifymailadr
     */
    public function setNotifymailadr($notifymailadr)
    {
        $this->notifymailadr = $notifymailadr;
    }
	/**
     * @return the $matchcode
     */
    public function getMatchcode()
    {
        return $this->matchcode;
    }

	/**
     * @param field_type $matchcode
     */
    public function setMatchcode($matchcode)
    {
        $this->matchcode = $matchcode;
    }
    
	/**
     * @return the $supervisor
     */
    public function getSupervisor()
    {
        return $this->supervisor;
    }

	/**
     * @param User $supervisor
     */
    public function setSupervisor($supervisor)
    {
        $this->supervisor = $supervisor;
    }
    
	/**
     * @return the $tourmarker
     */
    public function getTourmarker()
    {
        return $this->tourmarker;
    }

	/**
     * @param field_type $tourmarker
     */
    public function setTourmarker($tourmarker)
    {
        $this->tourmarker = $tourmarker;
    }
    
	/**
     * @return the $notes
     */
    public function getNotes()
    {
        return $this->notes;
    }

	/**
     * @param field_type $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }
    
	/**
     * @return the $salesperson
     */
    public function getSalesperson()
    {
        return $this->salesperson;
    }

	/**
     * @param User $salesperson
     */
    public function setSalesperson($salesperson)
    {
        $this->salesperson = $salesperson;
    }
    
    
}
?>