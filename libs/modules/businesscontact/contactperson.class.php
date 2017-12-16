<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('businesscontact.class.php');
require_once('libs/basic/translator/translator.class.php');
require_once('libs/modules/tickets/ticket.category.class.php');

class ContactPerson {
	
	const ORDER_ID = " id";
	const ORDER_NAME = " name1 , name2 ";
	
	private $id = 0;
	private $active;
	private $businessContactId;
	private $title;
	private $name1;
	private $name2;
	private $street;
	private $houseno;
	private $address2;
	private $zip;
	private $city;
	private $country = Array();
	private $phone;
	private $mobil;
	private $fax;
	private $email;
	private $web;
	private $comment;
	private $isMainContact;
	private $activeAdress = 1;
	private $birthdate = 0;
	
	private $alt_name1;  			// Alternative-Adresse
	private $alt_name2;
	private $alt_street;
	private $alt_houseno;
	private $alt_address2;
	private $alt_zip;
	private $alt_city;
	private $alt_country;
	private $alt_phone;
	private $alt_fax;
	private $alt_mobil;
	private $alt_email;
	
	private $priv_name1; 			// Private Adresse
	private $priv_name2;
	private $priv_street;
	private $priv_houseno;
	private $priv_address2;
	private $priv_zip;
	private $priv_city;
	private $priv_country;
	private $priv_phone;
	private $priv_fax;
	private $priv_mobil;
	private $priv_email;
	
	private $shopLogin;					// zus. Login fuer das Kundenportal
	private $shopPassword;		
	private $enabledTickets;			// Freigabe fuer Tickets im Kundenportal
	private $enabledPersonalization; 	// Freigabe fuer Personalisierungen im Kundenportal
	private $enabledArtikel;			// Freigabe fuer Artikel im Kundenportal
	private $enabledMarketing;			// Freigabe fuer Marketingplan im Kundenportal
	
	private $notifymailadr = Array(); // f�r gesonderte Benachrichtigungs Mails bei Bestellungen
	
	private $categories_cansee = Array();
	private $categories_cancreate = Array();
	
	const LOADER_BASIC = 0;
	const LOADER_FULL = 1;
	
	function __construct($id = 0, $loader = ContactPerson::LOADER_FULL){
        global $DB;
        global $_USER;
        global $_LANG;

        if ($_USER != NULL){
        	$this->country = $_USER->getClient()->getCountry();
        	$this->alt_country = $_USER->getClient()->getCountry();
        	$this->priv_country = $_USER->getClient()->getCountry();
        } else {
        	$this->country = new Country(55); // Auf Deutschland setzen
        	$this->alt_country = new Country(55); 
        	$this->priv_country = new Country(55); 
        }

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
				$sql = " SELECT * FROM contactperson WHERE id = {$id}";

				// sql returns only one record -> business contact is valid
				if($DB->num_rows($sql) == 1) {
					$res = $DB->select($sql);
					$this->id = $res[0]["id"];
					$this->businessContactId = $res[0]["businesscontact"];
					$this->active = $res[0]["active"];
					$this->title = $res[0]["title"];
					$this->name1 = $res[0]["name1"];
					$this->name2 = $res[0]["name2"];
					$this->street = $res[0]["street"];
					$this->houseno = $res[0]["houseno"];
					$this->address2 = $res[0]["address2"];
					$this->zip = $res[0]["zip"];
					$this->city = $res[0]["city"];
					$this->country = new Country ($res[0]["country"]);
					$this->phone = $res[0]["phone"];
					$this->mobile = $res[0]["mobil"];
					$this->fax = $res[0]["fax"];
					$this->email = $res[0]["email"];
					$this->mobil = $res[0]["mobil"];
					$this->web = $res[0]["web"];
					$this->comment = $res[0]["comment"];
					$this->isMainContact = $res[0]["main_contact"];
					$this->activeAdress = $res[0]["active_adress"];
					$this->birthdate = $res[0]["birthdate"];

					$this->notifymailadr = unserialize($res[0]["notifymailadr"]);
					$this->shopLogin = $res[0]["shop_login"];
					$this->shopPassword = $res[0]["shop_pass"];
					$this->enabledArtikel = $res[0]["enabled_article"];
					$this->enabledTickets = $res[0]["enabled_ticket"];
					$this->enabledPersonalization = $res[0]["enabled_personalization"];
					$this->enabledMarketing = $res[0]["enabled_marketing"];

					$this->alt_name1 = $res[0]["alt_name1"];
					$this->alt_name2 = $res[0]["alt_name2"];
					$this->alt_street = $res[0]["alt_street"];
					$this->alt_houseno = $res[0]["alt_houseno"];
					$this->alt_address2 = $res[0]["alt_address2"];
					$this->alt_zip = $res[0]["alt_zip"];
					$this->alt_city = $res[0]["alt_city"];
					$this->alt_country = new Country ($res[0]["alt_country"]);
					$this->alt_phone = $res[0]["alt_phone"];
					$this->alt_fax = $res[0]["alt_fax"];
					$this->alt_mobil = $res[0]["alt_mobil"];
					$this->alt_email = $res[0]["alt_email"];

					$this->priv_name1 = $res[0]["priv_name1"];
					$this->priv_name2 = $res[0]["priv_name2"];
					$this->priv_street = $res[0]["priv_street"];
					$this->priv_houseno = $res[0]["priv_houseno"];
					$this->priv_address2 = $res[0]["priv_address2"];
					$this->priv_zip = $res[0]["priv_zip"];
					$this->priv_city = $res[0]["priv_city"];
					$this->priv_country = new Country ($res[0]["priv_country"]);
					$this->priv_phone = $res[0]["priv_phone"];
					$this->priv_fax = $res[0]["priv_fax"];
					$this->priv_mobil = $res[0]["priv_mobil"];
					$this->priv_email = $res[0]["priv_email"];


					$sql = "SELECT * FROM contactperson_categories_perm WHERE cpid = {$id}";
					$tmp_categories_cansee = Array();
					$tmp_categories_cancreate = Array();
					if ($DB->num_rows($sql)) {
						foreach ($DB->select($sql) as $r) {
							if ((int)$r["cansee"] == 1) {
								$tmp_cat = new TicketCategory($r["categoryid"]);
								$tmp_categories_cansee[] = $tmp_cat;
							}
							if ((int)$r["cancreate"] == 1) {
								$tmp_cat = new TicketCategory($r["categoryid"]);
								$tmp_categories_cancreate[] = $tmp_cat;
							}
						}
					}
					$this->categories_cansee = $tmp_categories_cansee;
					$this->categories_cancreate = $tmp_categories_cancreate;
				}

				Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
            }
        }
    }

    function TC_cansee(TicketCategory $category){
        foreach ($this->categories_cansee as $see){
            if ($see == $category){
                return true;
            }
        }
        return false;
    }

    function TC_cancreate(TicketCategory $category){
        foreach ($this->categories_cancreate as $see){
            if ($see == $category){
                return true;
            }
        }
        return false;
    }
    
    /**
     * Liefert den Namen wie folgt: Anrede Nachname, Vorname 
     * 
     * @return string
     */
	public function getNameAsLine()
	{
	    if($this->title)
	        $retval = $this->title." ";
	    $retval .= $this->name1;
	    if ($this->name2)
	    	$retval .= ", " . $this->name2;
		return  $retval;
	}

	/**
	 * Liefert den Namen wie folgt: Anrede Nachname, Vorname
	 *
	 * @return string
	 */
	public function getNameAsLineAlt()
	{
		if($this->title)
			$retval = $this->title." ";
		$retval .= $this->alt_name1;
		if ($this->alt_name2)
			$retval .= ", " . $this->alt_name2;
		return  $retval;
	}

	/**
	 * Liefert den Namen wie folgt: Anrede Nachname, Vorname
	 *
	 * @return string
	 */
	public function getNameAsLinePrivate()
	{
		if($this->title)
			$retval = $this->title." ";
		$retval .= $this->priv_name1;
		if ($this->priv_name2)
			$retval .= ", " . $this->priv_name2;
		return  $retval;
	}
	
	/**
	 * Selbe Funktion wie getNameAsLine, nur ohne Anrede
	 * 
	 * @return string
	 */
	public function getNameAsLine2()
	{
		$retval = $this->name1;
		if ($this->name2)
			$retval .= ", " . $this->name2;
		return  $retval;
	}
	
	/**
	 * Liefert den Namen wie folgt: Anrede Vorname Nachname. z.B. fuer Dokumente
	 *
	 * @return string
	 */
	public function getNameAsLine3()
	{
		if($this->title)
			$retval = $this->title." ";
		if ($this->name2)
			$retval .= $this->name2." ";
		$retval .= $this->name1;
		return  $retval;
	}
	
	public function getAddressAsLine()
	{
		$retval = $this->street . ' ' . $this->houseno;
        if($this->address2 != "")
            $retval .= "\n".$this->address2;
        if($this->postcode || $this->city)
            $retval .= "\n".$this->country->getCode()."-".$this->zip." ".$this->city;
        return $retval;		
	}

	/**
	 * @param null $businessContact
	 * @param string $order
	 * @param string $filter
	 * @param int $loader
	 * @return ContactPerson[]
	 */
	public static function getAllContactPersons($businessContact = NULL, $order = self::ORDER_ID, $filter = "", $loader = ContactPerson::LOADER_FULL)
	{
		global $DB;
		$contactPersons = Array();
    	$sql = " SELECT * FROM contactperson WHERE active > 0 " . (($businessContact == NULL) ? "" : " AND businesscontact = " . $businessContact->getID()) . " ".$filter." ORDER BY {$order}";
    	$res = $DB->select($sql);
    	if($DB->num_rows($sql))
	    	foreach ($res as $r)
	    		$contactPersons[] = new ContactPerson($r["id"], $loader);
    	
    	return $contactPersons;
	}

	/**
	 * @param null $businessContact
	 * @return int
	 */
	public static function getTotalCount($businessContact = NULL)
	{
		global $DB;
		$sql = " SELECT count(id) as `count` FROM contactperson WHERE active > 0 " . (($businessContact == NULL) ? "" : " AND businesscontact = " . $businessContact->getID());
		$res = $DB->select($sql);
		return $res[0]['count'];
	}

	/**
	 * @param null $businessContact
	 * @param string $order
	 * @param string $filter
	 * @param int $loader
	 * @return int
	 */
	public static function getAllContactPersonsCount($businessContact = NULL, $order = self::ORDER_ID, $filter = "", $loader = ContactPerson::LOADER_FULL)
	{
		global $DB;
		$contactPersons = 0;
		$sql = " SELECT count(id) as counted FROM contactperson WHERE active > 0 " . (($businessContact == NULL) ? "" : " AND businesscontact = " . $businessContact->getID()) . " ".$filter." ORDER BY {$order}";
		if($DB->num_rows($sql)){
			$result = $DB->select($sql);
			$result = $result[0];
			$contactPersons = $result['counted'];
		}
		return $contactPersons;
	}
	
	public static function getAllContactPersonsBDay($start,$end)
	{
	    global $DB;
	    
	    $contactPersons = Array();
	    $sql = " SELECT id, birthdate FROM contactperson WHERE active > 0 AND birthdate > 0";
// 	    echo $sql."</br>";
	    $res = $DB->select($sql);
	    if($DB->num_rows($sql))
	    {
	        foreach ($res as $r)
	        {
	            if ((date("n",$r["birthdate"]) >= date("m",$start) && date("n",$r["birthdate"]) <= date("m",$end)) || (date("n",$r["birthdate"])==1 && date("m",$start)==12))
	                $contactPersons[] = new ContactPerson($r["id"]);
	        }
	        return $contactPersons;
	    }
	}

	public static function getAllContactPersonsWithBC($filter = "")
	{
	    global $DB;
	    $retarray = Array();
	    $sql = " SELECT
	             contactperson.id AS cid,
	             contactperson.name1 AS cname1,
	             contactperson.name2 AS cname2,
	             businesscontact.id AS bid,
	             businesscontact.matchcode AS bmatch,
	             businesscontact.name1 AS bname1,
	             businesscontact.name2 AS bname2, 
	             businesscontact.tourmarker as tourmarker 
	             FROM
	             contactperson
	             INNER JOIN businesscontact ON contactperson.businesscontact = businesscontact.id 
	             WHERE contactperson.active > 0 AND businesscontact.active > 0 AND
	             (contactperson.name1 LIKE '%{$filter}%' OR contactperson.name2 LIKE '%{$filter}%' OR businesscontact.matchcode LIKE '%{$filter}%' 
	             OR businesscontact.name1 LIKE '%{$filter}%' OR businesscontact.name2 LIKE '%{$filter}%') 
	             ORDER BY  businesscontact.name1, businesscontact.name2, contactperson.name1, contactperson.name2";
	    $res = $DB->select($sql);
	    if($DB->num_rows($sql))
	        foreach ($res as $r)
	            $retarray[] = Array(
	                "cid" => $r["cid"], 
	                "bid" => $r["bid"], 
	                "label" => $r["bname1"]." ".$r["bname2"]." - ".$r["cname1"].", ".$r["cname2"], 
	                "bclabel" => $r["bname1"]." ".$r["bname2"], 
	                "tourmarker" => $r["tourmarker"] 
	                );
	        return $retarray;
	}
	
	/**
	 * Liefert den Ansprechpartner zum angegebenen Shoplogin-Namen und PAsswort
	 *
	 * @return ContactPerson
	 */
	static function getBusinessContactsByShoplogin($username, $password){
		global $DB;
		$retval = false;
		$sql = "SELECT id FROM contactperson
				WHERE active > 0 AND
				shop_login = '{$username}' AND  
				shop_pass = '{$password}' ";
//		error_log("SQL: ".$sql);
		if($DB->num_rows($sql)){
			$r = $DB->select($sql);
				$retval = new ContactPerson($r[0]["id"]);
			} else {
			return false;
		}
		return $retval;
	}
	
	/**
	 * ... liefert alle Ansprechpartner eines Kunden, der die Suchkriterien erfuellt
	 * 
	 * @param String $order
	 * @param int $busiconID
	 * @param String $search_string
	 * @return ContactPerson[]
	 */
	public static function searchContactPersonsByBussinessContact($order = self::ORDER_ID, $busiconID, $search_string){
		global $DB;
		$contactPersons = Array();
		
		$sql = "SELECT id FROM contactperson 
				WHERE 
				active > 0 
				AND businesscontact = {$busiconID}
				AND (
				name1 LIKE '%{$search_string}%' OR
		    	name2 LIKE '%{$search_string}%' OR 
		    	street LIKE '%{$search_string}%' OR
		    	address2 LIKE '%{$search_string}%' OR 
		    	city LIKE '%{$search_string}%' OR
		    	zip LIKE '%{$search_string}%' ) 
				ORDER BY {$order}";
		 
		$res = $DB->select($sql);
		if($DB->num_rows($sql))
			foreach ($res as $r)
				$contactPersons[] = new ContactPerson($r["id"]);
		 
		return $contactPersons;
	}
	
	public function save()
	{
		global $DB;
		
		$tmp_notify_mail_adr = serialize($this->notifymailadr);
		
		if ($this->id > 0)
		{
			$sql = " UPDATE contactperson SET
            active = '{$this->active}',
            businesscontact = '{$this->businessContactId}',
            title = '{$this->title}',
            name1 = '{$this->name1}',
            name2 = '{$this->name2}',
            street = '{$this->street}',
            houseno = '{$this->houseno}',
            address2 = '{$this->address2}',
            zip = '{$this->zip}',
            city = '{$this->city}',
            country = '{$this->country->getId()}',
            phone = '{$this->phone}',
            mobil = '{$this->mobil}',
            fax = '{$this->fax}',
            email = '{$this->email}',
            web = '{$this->web}', 
            main_contact = {$this->isMainContact}, 
            active_adress = {$this->activeAdress},
            shop_login = '{$this->shopLogin}', 
            shop_pass = '{$this->shopPassword}',  
            alt_name1 = '{$this->alt_name1}',
            alt_name2 = '{$this->alt_name2}',
            alt_street = '{$this->alt_street}',
            alt_houseno = '{$this->alt_houseno}',
            alt_address2 = '{$this->alt_address2}',
            alt_zip = '{$this->alt_zip}',
            alt_city = '{$this->alt_city}',
            alt_country = '{$this->alt_country->getId()}',
            alt_phone = '{$this->alt_phone}',
            alt_fax = '{$this->alt_fax}',
            alt_mobil = '{$this->alt_mobil}',
            alt_email = '{$this->alt_email}',
            priv_name1 = '{$this->priv_name1}',
            priv_name2 = '{$this->priv_name2}',
            priv_street = '{$this->priv_street}',
            priv_houseno = '{$this->priv_houseno}',
            priv_address2 = '{$this->priv_address2}',
            priv_zip = '{$this->priv_zip}',
            priv_city = '{$this->priv_city}',
            priv_country = '{$this->priv_country->getId()}',
            priv_phone = '{$this->priv_phone}',
            priv_fax = '{$this->priv_fax}',
            priv_mobil = '{$this->priv_mobil}',
            priv_email = '{$this->priv_email}',
            enabled_article = {$this->enabledArtikel}, 
            enabled_ticket = {$this->enabledTickets}, 
            enabled_personalization = {$this->enabledPersonalization},
            enabled_marketing = {$this->enabledMarketing},
        	birthdate = '{$this->birthdate}', 
		    notifymailadr = '{$tmp_notify_mail_adr}', 
            comment = '{$this->comment}' 
			WHERE id = {$this->id}";
			$res = $DB->no_result($sql); //Aenderungen speichern
		}
		else
		{
			$sql = " INSERT INTO contactperson
            (active, businesscontact, title, name1, name2, street, houseno, address2, 
            zip, city, country, phone, mobil, 
            fax, email, web, alt_name1, alt_name2, alt_street, alt_houseno, alt_address2, 
            alt_zip, alt_city, alt_country, alt_phone, alt_fax, alt_mobil,  
            alt_email, priv_name1, priv_name2, priv_street, priv_houseno, priv_address2, priv_zip, 
            priv_city, priv_country, priv_phone, priv_fax, priv_mobil,  
            priv_email, comment, main_contact, active_adress,
            shop_login, shop_pass, 
            enabled_article, enabled_ticket, enabled_personalization, birthdate, notifymailadr, enabled_marketing )
            VALUES
            ('{$this->active}', '{$this->businessContactId}', '{$this->title}', '{$this->name1}', '{$this->name2}', '{$this->street}', '{$this->houseno}', '{$this->address2}', 
			'{$this->zip}', '{$this->city}',  '{$this->country->getId()}',  '{$this->phone}', '{$this->mobil}',
            '{$this->fax}', '{$this->email}', '{$this->web}', '{$this->alt_name1}', '{$this->alt_name2}', '{$this->alt_street}', '{$this->alt_houseno}', '{$this->alt_address2}', 
            '{$this->alt_zip}', '{$this->alt_city}',  '{$this->alt_country->getId()}', '{$this->alt_phone}', '{$this->alt_fax}', '{$this->alt_mobil}', 
            '{$this->alt_email}', '{$this->priv_name1}', '{$this->priv_name2}', '{$this->priv_street}', '{$this->priv_houseno}', '{$this->priv_address2}', '{$this->priv_zip}', 
            '{$this->priv_city}', '{$this->priv_country->getId()}', '{$this->priv_phone}', '{$this->priv_fax}', '{$this->priv_mobil}', 
			'{$this->priv_email}', '{$this->comment}', {$this->isMainContact}, {$this->activeAdress}, 
			'{$this->shopLogin}', '{$this->shopPassword}', 
			 {$this->enabledArtikel}, {$this->enabledTickets}, {$this->enabledPersonalization}, '{$this->birthdate}', '{$tmp_notify_mail_adr}', {$this->enabledMarketing} )";
			$res = $DB->no_result($sql); //Datensatz neu einfuegen
			
			if ($res)
            {
                $sql = " SELECT max(id) id FROM contactperson";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
            }
		}
		

		$sql = "DELETE FROM contactperson_categories_perm WHERE cpid = {$this->id}";
		$DB->no_result($sql);
		
		foreach (TicketCategory::getAllCategories() as $category){
		    $cansee = 0;
		    $cancreate = 0;
		    if (in_array($category, $this->categories_cansee)){
		        $cansee = 1;
		    }
		    if (in_array($category, $this->categories_cancreate)){
		        $cancreate = 1;
		    }
		    $sql = "INSERT INTO contactperson_categories_perm
		    (categoryid, cpid, cansee, cancreate)
		    VALUES ( {$category->getId()}, {$this->id}, {$cansee}, {$cancreate} )";
		    $DB->no_result($sql);
		}

		if ($res)
		{
			Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
			return true;
		}
		else
			return false;
		
	}
	
	public function delete(){
		global $DB;
		$sql = "DELETE FROM contactperson WHERE id = {$this->id}";
		$res = $DB->no_result($sql);
		if($res) {
			Cachehandler::removeCache(Cachehandler::genKeyword($this));
			unset($this);
			return true;
		} else
			return false;		
	}
	
	/**
	 * Liefert Alle aktivierten Optionen von Merkmalen zu einem Ansprechpartner
	 *
	 * @return boolean|Array
	 */
	public function getActiveAttributeItems(){
		global $DB;
		$sql = "SELECT * FROM contactperson_attributes
				WHERE
				contactperson_id = {$this->id}";

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
		$sql = "SELECT * FROM contactperson_attributes 
				WHERE 
				contactperson_id = {$this->id}";
		
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
	* Speichert alle aktivierten Merkmals-Optionen des Ansprechpartners
	*
	* @param Array $active_items
	*/
	public function saveActiveAttributes($active_items){
		global $DB;
	
		foreach($active_items as $item){
			if((int)$item["id"] > 0){
				$sql = "UPDATE contactperson_attributes SET
						value = '{$item["value"]}', 
						inputvalue = '{$item["inputvalue"]}' 
						WHERE id = {$item["id"]}";
				$DB->no_result($sql);
			} else {
				$sql = "INSERT INTO contactperson_attributes
						(value, item_id, attribute_id, contactperson_id, inputvalue )
						VALUES
						({$item["value"]}, {$item["item_id"]}, {$item["attribute_id"]}, {$this->id}, '{$item["inputvalue"]}' )";
				$DB->no_result($sql);
			}
		}
	}
	
	/**
	 * Loescht alle aktivierten Attribut-Optionen des Ansprechpartners
	 * @return boolean
	 */
	public function clearAttributes(){
		global $DB;
		$sql = "DELETE FROM contactperson_attributes WHERE contactperson_id = {$this->id} ";
		return $DB->no_result($sql);
	}
	
	/**
	 * liefert die Telefonnummer des Kontakts in einer lesbaren Formatierung fuer das Snom-Telefon 
	 * unter Beruecksichtigung der aktivierten Adresse. 
	 * 
	 * @param String $type : n=normal m=mobile
	 * @return String: wenn Nummer vorhanden; FALSE, wenn keine Nummer gefunden
	 */
	public function getPhoneForDial($type="n"){	
		
		if($type == "n"){
			switch($this->activeAdress){
				case 1: $tmp_phone = $this->phone; break;
				case 2: $tmp_phone = $this->alt_phone; break;
				case 3: $tmp_phone = $this->priv_phone; break;
				default: $tmp_phone = $this->phone; break;
			}
		}
		if($type == "m"){
			switch($this->activeAdress){
				case 1: $tmp_phone = $this->mobil; break;
				case 2: $tmp_phone = $this->alt_mobil; break;
				case 3: $tmp_phone = $this->priv_mobil; break;
				default: $tmp_phone = $this->mobil; break;
			}
		}
		
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
	 * Funktion entschiedet, ob der Ansprechpartner der Haupt-Ansprechpartner ist
	 * 
	 * @return boolean
	 */
	public function isMainContact(){
		if($this->isMainContact == 1){
			return true;
		}
		return false;
	}
	
	/**
	 * loescht alle Markierungen der Hauptkontakte
	 * 
	 * @param int $busiconID
	 * @return boolean
	 */
	static function clearMainContact($busiconID){
		global $DB;
		$sql = "Update contactperson SET main_contact = 0 WHERE businesscontact = {$busiconID} ";
		return $DB->no_result($sql);
	}
	
	/**
	 * Liefert den Haupt-Ansprechpartner eines GEschaeftskontakts
	 * 
	 * @param Businesscontact $busicon
	 * @return ContactPerson
	 */
	static function getMainContact($busicon){
		global $DB;
		$retval = new ContactPerson();
		$sql = " SELECT * FROM contactperson 
				WHERE 
				active > 0 AND 
				businesscontact = " . $busicon->getId() . " ";
    	$res = $DB->select($sql);
    	if ($res[0]["id"] > 0) { $retval = new ContactPerson($res[0]["id"]); }
		return $retval;
	}
	
	/***************************************************************************
	 ***************** 		GETTER und SETTER 					****************
	 **************************************************************************/

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

	public function getBusinessContact(){
		$tmp_busi = new BusinessContact($this->businessContactId);
	    return $tmp_busi;
	}

	/**
	 * setzt die ID des Businesscontacts-Objekts
	 * @param unknown_type $businessContact
	 */
	public function setBusinessContact($businessContact)
	{
	    $this->businessContactId = $businessContact->getId();
	}

	public function getTitle()
	{
	    return $this->title;
	}

	public function setTitle($title)
	{
	    $this->title = $title;
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

	public function getAddress2()
	{
	    return $this->address2;
	}

	public function setAddress2($address2)
	{
	    $this->address2 = $address2;
	}

	public function getZip()
	{
	    return $this->zip;
	}

	public function setZip($zip)
	{
	    $this->zip = $zip;
	}

	public function getCity()
	{
	    return $this->city;
	}

	public function setCity($city)
	{
	    $this->city = $city;
	}
	
	public function getCountry()
	{
		return $this->country;
	}
	
	public function setCountry($country)
	{
		$this->country = $country;
	}
	public function getPhone()
	{
	    return $this->phone;
	}

	public function setPhone($phone)
	{
	    $this->phone = $phone;
	}

	public function getMobil()
	{
	    return $this->mobil;
	}

	public function setMobil($mobil)
	{
	    $this->mobil = $mobil;
	}

	public function getFax()
	{
	    return $this->fax;
	}

	public function setFax($fax)
	{
	    $this->fax = $fax;
	}
	public function getAlt_mobil()
	{
	    return $this->alt_mobil;
	}

	public function setAlt_mobil($alt_mobil)
	{
	    $this->alt_mobil = $alt_mobil;
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
	public function getPriv_mobil()
	{
	    return $this->priv_mobil;
	}

	public function setPriv_mobil($priv_mobil)
	{
	    $this->priv_mobil = $priv_mobil;
	}


	public function getPriv_email()
	{
	    return $this->priv_email;
	}

	public function setPriv_email($priv_email)
	{
	    $this->priv_email = $priv_email;
	}

	public function getIsMainContact()
	{
	    return $this->isMainContact;
	}

	public function setIsMainContact($isMainContact)
	{
	    $this->isMainContact = $isMainContact;
	}

	public function getActiveAdress()
	{
	    return $this->activeAdress;
	}

	public function setActiveAdress($activeAdress)
	{
	    $this->activeAdress = $activeAdress;
	}

	public function getShopLogin()
	{
	    return $this->shopLogin;
	}

	public function setShopLogin($shopLogin)
	{
	    $this->shopLogin = $shopLogin;
	}

	public function getShopPassword()
	{
	    return $this->shopPassword;
	}

	public function setShopPassword($shopPassword)
	{
	    $this->shopPassword = $shopPassword;
	}

	public function getEnabledTickets()
	{
	    return $this->enabledTickets;
	}

	public function setEnabledTickets($enabledTickets)
	{
	    $this->enabledTickets = $enabledTickets;
	}

	public function getEnabledPersonalization()
	{
	    return $this->enabledPersonalization;
	}

	public function setEnabledPersonalization($enabledPersonalization)
	{
	    $this->enabledPersonalization = $enabledPersonalization;
	}

	public function getEnabledArtikel()
	{
	    return $this->enabledArtikel;
	}

	public function setEnabledArtikel($enabledArtikel)
	{
	    $this->enabledArtikel = $enabledArtikel;
	}

	public function getBusinessContactId()
	{
	    return $this->businessContactId;
	}

	public function setBusinessContactId($businessContactId)
	{
	    $this->businessContactId = $businessContactId;
	}

    public function getBirthDate()
    {
        return $this->birthdate;
    }

    public function setBirthDate($birthdate)
    {
        $this->birthdate = $birthdate;
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
     * @return the $categories_cansee
     */
    public function getCategories_cansee()
    {
        return $this->categories_cansee;
    }

	/**
     * @return the $categories_cancreate
     */
    public function getCategories_cancreate()
    {
        return $this->categories_cancreate;
    }

	/**
     * @param multitype: $categories_cansee
     */
    public function setCategories_cansee($categories_cansee)
    {
        $this->categories_cansee = $categories_cansee;
    }

	/**
     * @param multitype: $categories_cancreate
     */
    public function setCategories_cancreate($categories_cancreate)
    {
        $this->categories_cancreate = $categories_cancreate;
    }

	/**
	 * @return mixed
	 */
	public function getEnabledMarketing()
	{
		return $this->enabledMarketing;
	}

	/**
	 * @param mixed $enabledMarketing
	 */
	public function setEnabledMarketing($enabledMarketing)
	{
		$this->enabledMarketing = $enabledMarketing;
	}

    /**
     * @return mixed
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param mixed $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return mixed
     */
    public function getHouseno()
    {
        return $this->houseno;
    }

    /**
     * @param mixed $houseno
     */
    public function setHouseno($houseno)
    {
        $this->houseno = $houseno;
    }

    /**
     * @return mixed
     */
    public function getAltStreet()
    {
        return $this->alt_street;
    }

    /**
     * @param mixed $alt_street
     */
    public function setAltStreet($alt_street)
    {
        $this->alt_street = $alt_street;
    }

    /**
     * @return mixed
     */
    public function getAltHouseno()
    {
        return $this->alt_houseno;
    }

    /**
     * @param mixed $alt_houseno
     */
    public function setAltHouseno($alt_houseno)
    {
        $this->alt_houseno = $alt_houseno;
    }

    /**
     * @return mixed
     */
    public function getPrivStreet()
    {
        return $this->priv_street;
    }

    /**
     * @param mixed $priv_street
     */
    public function setPrivStreet($priv_street)
    {
        $this->priv_street = $priv_street;
    }

    /**
     * @return mixed
     */
    public function getPrivHouseno()
    {
        return $this->priv_houseno;
    }

    /**
     * @param mixed $priv_houseno
     */
    public function setPrivHouseno($priv_houseno)
    {
        $this->priv_houseno = $priv_houseno;
    }
}
?>