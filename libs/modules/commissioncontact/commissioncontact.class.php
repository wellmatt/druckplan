<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

require_once('libs/modules/businesscontact/contactperson.class.php');
require_once('libs/modules/businesscontact/address.class.php');
require_once('libs/modules/paymentterms/paymentterms.class.php');

error_reporting(E_ERROR);
ini_set('display_errors', 1);
class CommissionContact {
	const ORDER_NAME = " name1, name2 ";
	const ORDER_ID = " id ";
	const ORDER_TYPE 	= " customer, supplier ";
	const ORDER_CITY 	= " city ";
	const ORDER_HOMENAME = " t1.name1, t1.name2";
	
	const FILTER_CUST_SOLL = " customer = 2 "; //IstKunde = 1, SollKunde = 2
	const FILTER_CUST_IST = " customer = 1 "; //IstKunde = 1, SollKunde = 2
	const FILTER_SUPP = " supplier = 1 "; //Lieferant = 1
    const FILTER_ALL = " true ";
	const FILTER_NAME1 = " name1 LIKE '";
	const FILTER_ONLY_CUST = " customer > 0 AND supplier = 0 ";
	const FILTER_ONLY_SUPP = " customer = 0 AND supplier = 1 ";
	
	private $id = 0;
	private $active; //Geloescht = 0, Aktiv = 1
    private $commissionpartner;  //Provisionspartner
    private $customer; //IstKunde = 1, SollKunde = 2
	private $supplier; //Lieferant = 1
	private $client; //Mandant
	private $name1; //Firma
	private $name2; //Firmenzusatz
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
	private $contactPersons = Array(); 		//Ansprechpartner
	private $deliveryAddresses = Array(); 	//Lieferadressen
	private $invoiceAddresses = Array(); 	//Rechnungsadressen
	private $loginexpire = 0;
	private $ticketenabled = 0;
	private $kreditor = 0;					// Kreditorennummer
	private $debitor = 0;					// Debitorennummer
	private $bic;
	private $iban;
	private $num_at_customer;				// Kundennummer fuer Die Firma beim Lieferanten
	private $ust;
	private $taxnumber;
	private	$branche;
	private $customernumber = 0 ;			// Kundennummer
	private $provision = 10;

	
	/* Konstruktor
     * Falls id uebergeben, werden die entsprechenden Daten direkt geladen
    */
	function __construct($id = 0)
    {
        global $DB;
        global $_USER;
        
        if ($_USER != NULL){
	        $this->country = $_USER->getClient()->getCountry();
	        $this->language = $_USER->getLang();
	        $this->paymentTerms = new PaymentTerms();
        } else {
        	$this->country = new Country(55); // Auf Deutschland setzen
        	$this->language = new Translator(22); // Auf Deutsch setzen
        	$this->paymentTerms = NULL; //new PaymentTerms();
        }
        
        if ($id > 0)
        {
            $sql = " SELECT * FROM commissioncontact WHERE id = {$id}";

            // sql returns only one record -> commission contact is valid
            if($DB->num_rows($sql) == 1)
            {
                $res = $DB->select($sql);
                $this->id = $res[0]["id"];
                $this->active = $res[0]["active"];
                $this->commissionpartner = $res[0]["commissionpartner"];
                $this->customer = $res[0]["customer"];
                $this->supplier = $res[0]["supplier"];
                $this->client = new Client($res[0]["client"]);
                $this->name1 = $res[0]["name1"];
                $this->name2 = $res[0]["name2"];
                $this->address1 = $res[0]["address1"];
                $this->address2 = $res[0]["address2"];
                $this->zip = $res[0]["zip"];
                $this->city = $res[0]["city"];
                $this->country = new Country ($res[0]["country"]);
                $this->phone = $res[0]["phone"];
                $this->fax = $res[0]["fax"];
                $this->email = $res[0]["email"];
                $this->web = $res[0]["web"];
                $this->shoplogin = $res[0]["shop_login"];
                $this->shoppass = $res[0]["shop_pass"];
                $this->comment = $res[0]["comment"];
                $this->lectorId = $res[0]["lector_id"];
                $this->discount = $res[0]["discount"];
                $this->paymentTerms = new PaymentTerms($res[0]["payment_terms"]);
                $this->language = new Translator($res[0]["language"]);
        		$this->contactPersons = ContactPerson::getAllContactPersons($this);
        		$this->deliveryAddresses = Address::getAllAddresses($this,Address::ORDER_NAME,Address::FILTER_DELIV);
        		$this->invoiceAddresses = Address::getAllAddresses($this,Address::ORDER_NAME,Address::FILTER_INVC);
        		$this->loginexpire = $res[0]["login_expire"];
        		$this->ticketenabled = $res[0]["ticket_enabled"];
        		$this->debitor = $res[0]["debitor_number"];
        		$this->kreditor = $res[0]["kreditor_number"];
        		$this->bic = $res[0]["bic"];
        		$this->iban = $res[0]["iban"];
        		$this->num_at_customer = $res[0]["number_at_customer"];
        		$this->ust = $res[0]["ust"];
        		$this->taxnumber = $res[0]["tax_number"];
        		$this->branche = $res[0]["branche"];
        		$this->provision = $res[0]["provision"];

                return true;
                // sql returns more than one record, should not happen!
            } else if ($DB->num_rows($sql) > 1)
            {
                $this->strError = "Mehr als einen Gesch�ftskontakt gefunden";
                return false;
                // sql returns 0 rows -> login isn't valid
            }
        }
    }
    
    public static function getAllCommissionContacts($order = self::ORDER_ID, $filter = self::FILTER_ALL)
    {
    	global $_USER;
    	global $DB;
    	$commissionContacts = Array();
    	$sql = "SELECT * FROM commissioncontact WHERE active > 0 AND {$filter} AND client = {$_USER->getClient()->getID()} ORDER BY {$order}";
    	//$sql= "SELECT * FROM commissioncontact";
    	if ($DB->num_rows($sql))
    	{
	    	$res = $DB->select($sql);
	    	
	    	foreach ($res as $r)	    	
	    		$commissionContacts[] = new CommissionContact($r["id"]);	    	
    	}
    	
    	return $commissionContacts;
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
    public static function getAllCommissionContactsForLists($order = self::ORDER_ID, $filter = self::FILTER_ALL){
    	global $_USER;
    	global $DB;
    	$commissionContacts = Array();
    	$sql = "SELECT id, name1, name2, customer, supplier, cust_number,
		    	address1, address2, zip, city, country, phone, fax
		    	FROM commissioncontact
		    	WHERE
		    	active > 0
		    	AND {$filter} 
		    	AND client = {$_USER->getClient()->getID()}
		    	ORDER BY {$order} ";
    	// echo $sql;
    	if ($DB->num_rows($sql)){
    		$res = $DB->select($sql);
	    	foreach ($res as $r){
		    	$tmp_busicon = new CommissionContact();
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
    			$commissionContacts[] = $tmp_busicon;
	    	}
    	}
    	return $commissionContacts;
    }
    
    public static function searchByLectorId($lectorId, $order = self::ORDER_ID, $filter = self::FILTER_ALL)
    {
        global $DB;
        global $_USER;
        $retval = Array();
        $sql = "SELECT * FROM commissioncontact
                WHERE active > 0 
                    AND {$filter} 
                    AND client = {$_USER->getClient()->getID()}
                    AND lector_id = {$lectorId} 
                ORDER BY {$order}";
        
        if ($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
        
            foreach ($res as $r)
                $retval[] = new CommissionContact($r["id"]);
        }
        
        return $retval;
    }
    
//    static function searchById($id, $order = self::ORDER_ID)
//    {
//        $retval = Array();
//        global $DB;
//        $sql = "SELECT id, name1, name2, address1,zip, city FROM businesscontact
//                WHERE active > 0
//                    AND id like '%{$id}%'
//                ORDER BY {$order}";
//        if($DB->num_rows($sql))
//        {
//            foreach($DB->select($sql) as $r)
//            {
//                $retval[] = new CommissionContact($r["id"]);
//            }
//        }
//    
//        return $retval;
//    }
    
    static function getAllCommissionContactsByName1()
    {
        $retval = Array();
        global $DB;
        $sql = "SELECT id,name1,name2 FROM commissioncontact
                WHERE active > 0
                ORDER BY name1";
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new CommissionContact($r["id"]);
            }
        }
    
        return $retval;
    }
    
    /**
     * Liefert alle BusinessContacts des Systems fuer die Startseite (Suche)
     * incl. Suche nach Ansprechpartnern
     *
     * @param unknown_type $order : Reihenfolge
     * @param STRING $search_string : Suchtext
     * @return multitype:BusinessContact
     */
    public static function getAllCommissionContactsForHome($order = self::ORDER_ID, $search_string){
    	global $_USER;
    	global $DB;
    	$commissionContacts = Array();
    	$sql = "SELECT t1.id
		    	FROM commissioncontact t1
		    	LEFT OUTER JOIN contactperson t2 ON t1.id = t2.commissioncontact
		    	WHERE t1.active > 0
		    	AND
		    	(  t1.name1 LIKE '%{$search_string}%'
		    	OR t1.name2 LIKE '%{$search_string}%'
		    	OR t1.address1 LIKE '%{$search_string}%'
		    	OR t1.address2 LIKE '%{$search_string}%'
		    	OR t1.city LIKE '%{$search_string}%'
		    	OR t1.zip LIKE '%{$search_string}%'
		    	OR t2.name1 LIKE '%{$search_string}%'
		    	OR t2.name2 LIKE '%{$search_string}%'
		    	OR t2.address1 LIKE '%{$search_string}%'
		    	OR t2.address2 LIKE '%{$search_string}%'
		    	OR t2.city LIKE '%{$search_string}%'
		    	OR t2.zip LIKE '%{$search_string}%'
		    	)
		    	AND t1.client = {$_USER->getClient()->getID()}
		    	GROUP BY t1.id
		    	ORDER BY {$order} ";
    	 
    	if ($DB->num_rows($sql)){
    		$res = $DB->select($sql);
    		foreach ($res as $r)
    		$commissionContacts[] = new CommissionContact($r["id"]);
    	}
     
    	return $commissionContacts;
    }
    
    /**
     * Liefert den commissionContact zum angegebenen Shoplogin-Namen
     * 
     * @return commissionContact
     */
    static function getCommissionContactsByShoplogin($shoplogin){
    	global $DB;
    	$sql = "SELECT id FROM commissioncontact
                WHERE active > 0 AND 
    			shop_login = '{$shoplogin}' ";
    	if($DB->num_rows($sql)){
    		$r = $DB->select($sql);
    		$retval = new CommissionContact($r[0]["id"]);
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
     * @return commissionContact|boolean
     */
    static function shoplogin($shoplogin, $shoppass){
    	global $DB;
    	$shoppass = md5($shoppass);
    	$sql = "SELECT id FROM commissioncontact
    			WHERE active > 0 AND
    			shop_login = '{$shoplogin}' AND
    			shop_pass =  '{$shoppass}' ";
    	if($DB->num_rows($sql)){
    		$r = $DB->select($sql);
    		return (new CommissionContact($r[0]["id"]));
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
		return $this->getCustomer() ==2;
	}
	
	public function addContactPersons($contactPerson)
	{
		$contactPerson.setCommissionContact($this);
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
		$sql = "UPDATE commissioncontact SET active = 0 WHERE id = {$this->id}";
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
		if ($this->id > 0)
		{
			$sql = " UPDATE commissioncontact SET
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
            web = '{$this->web}',
            comment = '{$this->comment}',
            shop_login = '{$this->shoplogin}',
            shop_pass = '{$this->shoppass}',
            language = '{$this->language->getID()}',
            lector_id = {$this->lectorId},
            discount = {$this->discount},
            payment_terms = {$this->paymentTerms->getId()}, 
            login_expire = {$this->loginexpire}, 
		    ticket_enabled = {$this->ticketenabled},  
		    kreditor_number = {$this->kreditor},
		    debitor_number = {$this->debitor}, 
			iban = '{$this->iban}', 
			bic= '{$this->bic}',   
        	ust = '{$this->ust}',  
        	tax_number = '{$this->taxnumber}',
        	provision = '{$this->provision}'
			WHERE id = {$this->id}";
			$res = $DB->no_result($sql); 
		}
		else
		{
			$sql = " INSERT INTO commissioncontact
		            (active, commissionpartner, customer, supplier, client, name1,
		            name2, address1, address2, zip, city, country, phone,
		            fax, email, web, comment, language, discount, 
		            payment_terms, lector_id, iban, bic, 
		            shop_login, shop_pass, login_expire, kreditor_number, debitor_number,
		            ust, tax_number, provision )
		            VALUES
		            ('{$this->active}', '{$this->commissionpartner}', '{$this->customer}', '{$this->supplier}', '{$this->client->getID()}', '{$this->name1}',
		            '{$this->name2}', '{$this->address1}', '{$this->address2}', '{$this->zip}', '{$this->city}', '{$this->country->getId()}', '{$this->phone}',
		            '{$this->fax}', '{$this->email}', '{$this->web}', '{$this->comment}', {$this->language->getID()}, {$this->discount},  
		            '{$this->paymentTerms->getId()}', '{$this->lectorId}', '{$this->iban}', '{$this->bic}',  
		            '{$this->shoplogin}', '{$this->shoppass}', {$this->loginexpire}, {$this->kreditor}, {$this->debitor},
					'{$this->ust}', '{$this->taxnumber}', '{$this->provision}' )";
			$res = $DB->no_result($sql); //Datensatz neu einfuegen
			
			// echo "-------".$sql."---------";
			//echo $DB->getLastError();
			if ($res)
            {
                $sql = " SELECT max(id) id FROM commissioncontact";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
            }
		}
		if($res)
			return true;
		else
			return false;
	}
	
	public function setId($id){
		$this->id = $id;	
	}
	
	public function getId()
	{
	    return $this->id;
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

	public function getProvision()
	{
	    return $this->provision;
	}

	public function setProvision($provision)
	{
	    $this->provision = $provision;
	}
	
	public function getName1()
	{
	    return $this->name1;
	}

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

	public function getKreditor()
	{
	    return $this->kreditor;
	}

	public function setKreditor($kreditor)
	{
	    $this->kreditor = $kreditor;
	}

	public function getDebitor()
	{
	    return $this->debitor;
	}

	public function setDebitor($debitor)
	{
	    $this->debitor = $debitor;
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

	public function getNum_at_customer()
	{
	    return $this->num_at_customer;
	}

	public function setNum_at_customer($num_at_customer)
	{
	    $this->num_at_customer = $num_at_customer;
	}

	public function getUst()
	{
	    return $this->ust;
	}

	public function setUst($ust)
	{
	    $this->ust = $ust;
	}

	public function getTaxnumber()
	{
	    return $this->taxnumber;
	}

	public function setTaxnumber($taxnumber)
	{
	    $this->taxnumber = $taxnumber;
	}

	public function getBranche()
	{
	    return $this->branche;
	}

	public function setBranche($branche)
	{
	    $this->branche = $branche;
	}

	public function getCustomernumber()
	{
	    return $this->customernumber;
	}

	public function setCustomernumber($customernumber)
	{
	    $this->customernumber = $customernumber;
	}
}
?>