<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('libs/modules/businesscontact/businesscontact.class.php');
require_once('libs/basic/translator/translator.class.php');

class PrivateContact {
	
	const ORDER_ID = " id ";
	const ORDER_NAME = " privatecontacts.name1 , privatecontacts.name2 ";
	const ORDER_BCON = " businesscontact.name1 ";
	
	private $id = 0;
	private $crtuser;
	private $active;
	private $businessContactId;
	private $title;
	private $name1;
	private $name2;
	private $address1;
	private $address2;
	private $zip;
	private $city;
	private $country;
	private $phone;
	private $mobil;
	private $fax;
	private $email;
	private $web;
	private $comment;
	private $birthdate = 0;
	private $access = Array();

	private $alt_title;
	private $alt_name1;
	private $alt_name2;
	private $alt_address1;
	private $alt_address2;
	private $alt_zip;
	private $alt_city;
	private $alt_country;
	private $alt_phone;
	private $alt_mobil;
	private $alt_fax;
	private $alt_email;
	private $alt_web;
	
	function __construct($id = 0){
        global $DB;
        global $_USER;
        global $_LANG;

        if ($_USER != NULL){
        	$this->country = $_USER->getClient()->getCountry();
			$this->alt_country = $_USER->getClient()->getCountry();
        } else {
        	$this->country = new Country(55);
			$this->alt_country = new Country(55);
        }
        $this->crtuser = new User();
        
//        $cached = Cachehandler::fromCache("obj_prvtc_" . $id);
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
            $sql = " SELECT * FROM privatecontacts WHERE id = {$id}";

            if($DB->num_rows($sql) == 1)
            {
                $res = $DB->select($sql);
                $this->id = $res[0]["id"];
                $this->crtuser = new User((int)$res[0]["crtuser"]);
                $this->businessContactId = $res[0]["businesscontact"];
                $this->active = $res[0]["active"];
                $this->title = $res[0]["title"];
                $this->name1 = $res[0]["name1"];
                $this->name2 = $res[0]["name2"];
                $this->address1 = $res[0]["address1"];
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
                $this->birthdate = $res[0]["birthdate"];

				$this->alt_title = $res[0]["alt_title"];
				$this->alt_name1 = $res[0]["alt_name1"];
				$this->alt_name2 = $res[0]["alt_name2"];
				$this->alt_address1 = $res[0]["alt_address1"];
				$this->alt_address2 = $res[0]["alt_address2"];
				$this->alt_zip = $res[0]["alt_zip"];
				$this->alt_city = $res[0]["alt_city"];
				$this->alt_country = new Country ($res[0]["alt_country"]);
				$this->alt_phone = $res[0]["alt_phone"];
				$this->alt_mobil = $res[0]["alt_mobil"];
				$this->alt_fax = $res[0]["alt_fax"];
				$this->alt_email = $res[0]["alt_email"];
				$this->alt_web = $res[0]["alt_web"];
                
                $sql = "SELECT * FROM privatecontacts_access WHERE prvtc_id = {$id}";
                $tmp_prvt_access = Array();
                if($DB->num_rows($sql)){
                    foreach($DB->select($sql) as $r){
                        $tmp_prvt_access[] = new User($r["userid"]);
                    }
                }
                $this->access = $tmp_prvt_access;
				
//                Cachehandler::toCache("obj_prvtc_".$id, $this);
                return true;
            } else if ($DB->num_rows($sql) > 1)
            {
                $this->strError = $_LANG->get('Mehr als einen Kontakt gefunden.');
                return false;
            }
        }
    }

	public function save()
	{
		global $DB;
		global $_USER;
		$this->crtuser = $_USER;

		if ($this->id > 0)
		{
			$sql = " UPDATE privatecontacts SET
            active = '{$this->active}',
            businesscontact = '{$this->businessContactId}',
            title = '{$this->title}',
            name1 = '{$this->name1}',
            name2 = '{$this->name2}',
            address1 = '{$this->address1}',
            address2 = '{$this->address2}',
            zip = '{$this->zip}',
            city = '{$this->city}',
            country = '{$this->country->getId()}',
            phone = '{$this->phone}',
            mobil = '{$this->mobil}',
            fax = '{$this->fax}',
            email = '{$this->email}',
            web = '{$this->web}',
        	birthdate = '{$this->birthdate}',

			alt_title = '{$this->alt_title}',
        	alt_name1 = '{$this->alt_name1}',
        	alt_name2 = '{$this->alt_name2}',
        	alt_address1 = '{$this->alt_address1}',
        	alt_address2 = '{$this->alt_address2}',
        	alt_zip = '{$this->alt_zip}',
        	alt_city = '{$this->alt_city}',
        	alt_country = '{$this->alt_country->getId()}',
        	alt_phone = '{$this->alt_phone}',
        	alt_mobil = '{$this->alt_mobil}',
        	alt_fax = '{$this->alt_fax}',
        	alt_email = '{$this->alt_email}',
        	alt_web = '{$this->alt_web}',

            comment = '{$this->comment}'
			WHERE id = {$this->id}";
			$res = $DB->no_result($sql);
		}
		else
		{
			$sql = " INSERT INTO privatecontacts
            (crtuser, active, businesscontact, title, name1, name2, address1, address2,
            zip, city, country, phone, mobil,
            fax, email, web, birthdate, comment,
            alt_title, alt_name1, alt_name2, alt_address1, alt_address2, alt_zip, alt_city, alt_country,
            alt_phone, alt_mobil, alt_fax, alt_email, alt_web)
            VALUES
            ({$_USER->getId()}, '{$this->active}', '{$this->businessContactId}', '{$this->title}', '{$this->name1}', '{$this->name2}', '{$this->address1}', '{$this->address2}',
			'{$this->zip}', '{$this->city}',  '{$this->country->getId()}',  '{$this->phone}', '{$this->mobil}',
            '{$this->fax}', '{$this->email}', '{$this->web}', '{$this->birthdate}', '{$this->comment}',
            '{$this->alt_title}', '{$this->alt_name1}', '{$this->alt_name2}', '{$this->alt_address1}', '{$this->alt_address2}',
            '{$this->alt_zip}', '{$this->alt_city}', '{$this->alt_country->getId()}', '{$this->alt_phone}',
            '{$this->alt_mobil}', '{$this->alt_fax}', '{$this->alt_email}', '{$this->alt_web}')";
			$res = $DB->no_result($sql);

			if ($res)
			{
				$sql = " SELECT max(id) id FROM privatecontacts";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["id"];
			}
		}


		$sql = "DELETE FROM privatecontacts_access WHERE prvtc_id = {$this->id}";
		$DB->no_result($sql);

		foreach ($this->access as $access_user){
			$sql = "INSERT INTO privatecontacts_access
		    (prvtc_id, userid)
		    VALUES ( {$this->id}, {$access_user->getId()} )";
			$DB->no_result($sql);
		}

//		Cachehandler::removeCache("obj_prvtc_".$this->id);
		if($res)
		{
//			Cachehandler::toCache("obj_prvtc_".$this->id, $this);
			return true;
		}
		else
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
		$retval = $this->address1;
        if($this->address2 != "")
            $retval .= "\n".$this->address2;
        if($this->postcode || $this->city)
            $retval .= "\n".$this->country->getCode()."-".$this->zip." ".$this->city;
        return $retval;		
	}
	
	public static function countPrivateContacts()
	{
		global $DB;
	    $sql = "SELECT count(id) as count FROM privatecontacts WHERE privatecontacts.active = 1";
	    $res = $DB->select($sql);
	    $count = $res[0]["count"];
	    return $count;
	}
	
	public static function getAllPrivateContacts($order = self::ORDER_ID, $filter = "", $userid = null, $search = null, $limit = null)
	{
		global $DB;
		if ($userid != null)
		{
		    $useraccess = Array();
		    $sql = "SELECT prvtc_id FROM privatecontacts_access WHERE userid = {$userid}";
    	    $res = $DB->select($sql);
            if($DB->num_rows($sql))
    	    	foreach ($res as $r)
    	    		$useraccess[] = $r["prvtc_id"];
    	    	
    	    $useraccess = join(",",$useraccess);
    	    if ($useraccess != "")
    	        $useraccess = " OR privatecontacts.id IN (".$useraccess.") ";
		}
		
		$privatecontacts = Array();
    	$sql = " SELECT
        	     privatecontacts.id,
                 privatecontacts.crtuser,
                 privatecontacts.active,
                 privatecontacts.businesscontact,
                 privatecontacts.title,
                 privatecontacts.name1,
                 privatecontacts.name2,
                 privatecontacts.address1,
                 privatecontacts.address2,
                 privatecontacts.zip,
                 privatecontacts.city,
                 privatecontacts.country,
                 privatecontacts.phone,
                 privatecontacts.mobil,
                 privatecontacts.fax,
                 privatecontacts.email,
                 privatecontacts.web,
                 privatecontacts.`comment`,
                 privatecontacts.birthdate,
                 businesscontact.name1 
    	         FROM privatecontacts 
    	         LEFT JOIN businesscontact ON businesscontact.id = privatecontacts.businesscontact
    	         WHERE privatecontacts.active > 0 ".$filter." 
    	         " . (($search == NULL) ? "" : " AND (privatecontacts.name1 LIKE '%" . $search . "%' OR privatecontacts.name2 LIKE '%" . $search . "%' OR concat(privatecontacts.name1,' ',privatecontacts.name2) LIKE '%" . $search . "%')") . "
    	         " . (($userid == NULL) ? "" : " AND (crtuser = " . $userid . "{$useraccess})") . "
    	         ORDER BY {$order} {$limit}";
    	$res = $DB->select($sql);
//     	echo $sql . "</br></br>";
    	if($DB->num_rows($sql))
	    	foreach ($res as $r)
	    		$privatecontacts[] = new PrivateContact($r["id"]);
    	
    	return $privatecontacts;
	}
	
	public function delete(){
		global $DB;
		$sql = "UPDATE privatecontacts SET active = 0 WHERE id = {$this->id}";
		$res = $DB->no_result($sql);
		unset($this);
		if($res)
		{
//		    Cachehandler::removeCache("obj_prvtc_".$this->id);
			return true;
		}
		else
			return false;		
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
			$tmp_phone = $this->phone;
		}
		if($type == "m"){
			$tmp_phone = $this->mobil;
		}
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

	public function getBusinessContactId()
	{
	    return $this->businessContactId;
	}

	public function setBusinessContactId($businessContactId)
	{
	    $this->businessContactId = $businessContactId;
	}
    
	/**
     * @return the $crtuser
     */
    public function getCrtuser()
    {
        return $this->crtuser;
    }

	/**
     * @return the $birthdate
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

	/**
     * @param field_type $crtuser
     */
    public function setCrtuser($crtuser)
    {
        $this->crtuser = $crtuser;
    }

	/**
     * @param number $birthdate
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;
    }
    
	/**
     * @return the $access
     */
    public function getAccess()
    {
        return $this->access;
    }

	/**
     * @param multitype: $access
     */
    public function setAccess($access)
    {
        $this->access = $access;
    }

	/**
	 * @return mixed
	 */
	public function getAltName1()
	{
		return $this->alt_name1;
	}

	/**
	 * @param mixed $alt_name1
	 */
	public function setAltName1($alt_name1)
	{
		$this->alt_name1 = $alt_name1;
	}

	/**
	 * @return mixed
	 */
	public function getAltName2()
	{
		return $this->alt_name2;
	}

	/**
	 * @param mixed $alt_name2
	 */
	public function setAltName2($alt_name2)
	{
		$this->alt_name2 = $alt_name2;
	}

	/**
	 * @return mixed
	 */
	public function getAltAddress1()
	{
		return $this->alt_address1;
	}

	/**
	 * @param mixed $alt_address1
	 */
	public function setAltAddress1($alt_address1)
	{
		$this->alt_address1 = $alt_address1;
	}

	/**
	 * @return mixed
	 */
	public function getAltAddress2()
	{
		return $this->alt_address2;
	}

	/**
	 * @param mixed $alt_address2
	 */
	public function setAltAddress2($alt_address2)
	{
		$this->alt_address2 = $alt_address2;
	}

	/**
	 * @return mixed
	 */
	public function getAltZip()
	{
		return $this->alt_zip;
	}

	/**
	 * @param mixed $alt_zip
	 */
	public function setAltZip($alt_zip)
	{
		$this->alt_zip = $alt_zip;
	}

	/**
	 * @return mixed
	 */
	public function getAltCity()
	{
		return $this->alt_city;
	}

	/**
	 * @param mixed $alt_city
	 */
	public function setAltCity($alt_city)
	{
		$this->alt_city = $alt_city;
	}

	/**
	 * @return mixed
	 */
	public function getAltCountry()
	{
		return $this->alt_country;
	}

	/**
	 * @param mixed $alt_country
	 */
	public function setAltCountry($alt_country)
	{
		$this->alt_country = $alt_country;
	}

	/**
	 * @return mixed
	 */
	public function getAltPhone()
	{
		return $this->alt_phone;
	}

	/**
	 * @param mixed $alt_phone
	 */
	public function setAltPhone($alt_phone)
	{
		$this->alt_phone = $alt_phone;
	}

	/**
	 * @return mixed
	 */
	public function getAltMobil()
	{
		return $this->alt_mobil;
	}

	/**
	 * @param mixed $alt_mobil
	 */
	public function setAltMobil($alt_mobil)
	{
		$this->alt_mobil = $alt_mobil;
	}

	/**
	 * @return mixed
	 */
	public function getAltFax()
	{
		return $this->alt_fax;
	}

	/**
	 * @param mixed $alt_fax
	 */
	public function setAltFax($alt_fax)
	{
		$this->alt_fax = $alt_fax;
	}

	/**
	 * @return mixed
	 */
	public function getAltEmail()
	{
		return $this->alt_email;
	}

	/**
	 * @param mixed $alt_email
	 */
	public function setAltEmail($alt_email)
	{
		$this->alt_email = $alt_email;
	}

	/**
	 * @return mixed
	 */
	public function getAltWeb()
	{
		return $this->alt_web;
	}

	/**
	 * @param mixed $alt_web
	 */
	public function setAltWeb($alt_web)
	{
		$this->alt_web = $alt_web;
	}

	/**
	 * @return mixed
	 */
	public function getAltTitle()
	{
		return $this->alt_title;
	}

	/**
	 * @param mixed $alt_title
	 */
	public function setAltTitle($alt_title)
	{
		$this->alt_title = $alt_title;
	}
}
?>