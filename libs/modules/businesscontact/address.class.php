<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once('businesscontact.class.php');

class Address {
	
	const ORDER_NAME = " 5, 4 ";
	const ORDER_ID = 1;
	const FILTER_INVC = " active = 1 ";
	const FILTER_DELIV = " active = 2 ";
	const FILTER_DELIV_SHOP = " active = 2 and shoprel = 1 ";
	const FILTER_ALL = " active > 0 ";
	
	private $id = 0;
	private $active; // Geloescht = 0, Rechnungsadresse = 1, Lieferadresse = 2
	private $businessContact;
	private $name1;
	private $name2;
	private $street;
    private $houseno;
	private $address2;
	private $zip;
	private $city;
	private $country;
	private $phone;
	private $mobil;
	private $fax;
	private $email;
	private $shoprel;	//gln, Shopfreigabe (nur fuer Lieferadresse)
	private $default = 0;

	function __construct($id = 0)
    {
        global $DB;
        global $_USER;
        $this->country = new Country(22);


        if ($id > 0)
        {
            $sql = " SELECT * FROM address WHERE id = {$id}";

            // sql returns only one record -> business contact is valid
            if($DB->num_rows($sql) == 1)
            {
                $res = $DB->select($sql);
                $this->id = $res[0]["id"];
                $this->active = $res[0]["active"];
                $this->name1 = $res[0]["name1"];
                $this->name2 = $res[0]["name2"];
                $this->street = $res[0]["street"];
                $this->houseno = $res[0]["houseno"];
                $this->address2 = $res[0]["address2"];
                $this->zip = $res[0]["zip"];
                $this->city = $res[0]["city"];
                $this->fax = $res[0]["fax"];
                $this->email = $res[0]["email"];
                $this->phone = $res[0]["phone"];
                $this->mobil = $res[0]["mobile"];
                $this->shoprel = $res[0]["shoprel"];
                $this->default = $res[0]["is_default"];
                $this->country = new Country ($res[0]["country"]);
                return true;
                // sql returns more than one record, should not happen!
            } else if ($DB->num_rows($sql) > 1)
            {
                $this->strError = "Mehr als eine Addresse gefunden";
                return false;
                // sql returns 0 rows -> login isn't valid
            }
        }
    }

    /**
     * @param null $businessContact
     * @param int $order
     * @param string $filter
     * @return Address[]
     */
	public static function getAllAddresses($businessContact = NULL, $order = Address::ORDER_ID, $filter = Address::FILTER_ALL)
	{
		global $DB;
		$addresses = Array();
    	$sql = " SELECT * FROM address WHERE " . $filter . (($businessContact == NULL) ? "" : " AND businesscontact = " . $businessContact->getID()) . " ORDER BY {$order}";
    	$res = $DB->select($sql);
    	if ($DB->num_rows($sql))
	    	foreach ($res as $r)
	    		$addresses[] = new Address($r["id"]);
    	
    	return $addresses;
	}

    /**
     * @param null $businessContact
     * @param string $filter
     * @return Address
     */
	public static function getDefaultAddress($businessContact = NULL, $filter = Address::FILTER_ALL)
	{
	    global $DB;
	    $sql = " SELECT * FROM address WHERE is_default = 1 AND "  . $filter . (($businessContact == NULL) ? "" : " AND businesscontact = " . $businessContact->getID()) . " LIMIT 1";
	    $res = $DB->select($sql);
	    if ($DB->num_rows($sql))
	        $address = new Address($res[0]["id"]);
	         
	    return $address;
	}
	
	public function getNameAsLine()
	{
		return $this->name1 . " " . $this->name2;		
	}
	
	public function getAddressAsLine()
	{
        $retval = $this->street . ' ' . $this->houseno;
        if($this->address2 != "")
            $retval .= "\n".$this->address2;
        if($this->zip || $this->city)
            $retval .= "\n".$this->country->getCode()."-".$this->zip." ".$this->city;
        return $retval;
	}
	
	public function delete()
	{
		global $DB;
		$sql = " UPDATE address SET active = 0 WHERE id = {$this->id}";
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
		
		if ($this->default == 1) {
		    if ($this->active == 1) {
		        $tmp_filter = Address::FILTER_INVC;
		    } else {
		        $tmp_filter = Address::FILTER_DELIV;
		    }
		    $tmp_addresses = Address::getAllAddresses($this->businessContact, Address::ORDER_ID, $tmp_filter);
		    
		    foreach ($tmp_addresses as $tmp_ad) {
		        $tmp_ad->setBusinessContact($this->businessContact);
		        $tmp_ad->setDefault(0);
		        $tmp_ad->save();
		    }
		}
		
		if ($this->id > 0)
		{
			$sql = " UPDATE address SET
            active = '{$this->active}',
            businesscontact = '{$this->businessContact->getID()}',
            name1 = '{$this->name1}',
            name2 = '{$this->name2}',
            street = '{$this->street}',
            houseno = '{$this->houseno}',
            address2 = '{$this->address2}',
            zip = '{$this->zip}',
            city = '{$this->city}',
            fax = '{$this->fax}',
            email = '{$this->email}',
            phone = '{$this->phone}',
            mobile = '{$this->mobil}',
            country = '{$this->country->getId()}',		
            shoprel = '{$this->shoprel}', 
            is_default = '{$this->default}' 
			WHERE id = {$this->id}";
			$res = $DB->no_result($sql); //Aenderungen speichern
// 			echo $sql;
		}
		else
		{
			$sql = " INSERT INTO address
		            (active, businesscontact, name1, name2, 
		            street, houseno, address2, zip, city, 
		            country, fax, email, phone, mobile, shoprel, is_default)
		            VALUES
		            ('{$this->active}', '{$this->businessContact->getID()}', '{$this->name1}', '{$this->name2}', 
		            '{$this->street}', '{$this->houseno}', '{$this->address2}', '{$this->zip}', '{$this->city}',  
					'{$this->country->getId()}', '{$this->fax}', '{$this->email}', '{$this->phone}','{$this->mobil}','{$this->shoprel}','{$this->default}')";
					//gln, neu: shoprel
			$res = $DB->no_result($sql); //Datensatz neu einfuegen
			if ($res)
            {
                $sql = " SELECT max(id) id FROM address";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
            }
		}
		if($res)
		{
			return true;
		}
		else
			return false;
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

	public function getBusinessContact()
	{
	    return $this->businessContact;
	}

	public function setBusinessContact($businessContact)
	{
	    $this->businessContact = $businessContact;
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
	
	public function getShoprel()
	{
	    return $this->shoprel;
	}

	public function setShoprel($shoprel)
	{
	    $this->shoprel = $shoprel;
	}

	public function getDefault()
	{
	    return $this->default;
	}
	
	public function setDefault($default)
	{
	    $this->default = $default;
	}

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $street
     */
    public function setStreet($street)
    {
        $this->street = $street;
    }

    /**
     * @return string
     */
    public function getHouseno()
    {
        return $this->houseno;
    }

    /**
     * @param string $houseno
     */
    public function setHouseno($houseno)
    {
        $this->houseno = $houseno;
    }
}
?>