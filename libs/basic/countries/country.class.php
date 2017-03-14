<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/modules/taxkeys/taxkey.class.php';
require_once 'libs/modules/costobjects/costobject.class.php';
require_once 'libs/modules/revenueaccounts/revenueaccount.class.php';

class Country {
    const ORDER_ID = "id";
    const ORDER_NAME = "country_name";
    const ORDER_ACTIVE = "country_active";
    const ORDER_ACTIVE_NAME = "country_active desc, country_name";
    
    private $id;
    private $name;
    private $nameInt;
    private $code;
    private $active;
    private $eu = 0;
    private $taxkey;
    
    /**
     * Konstruktor der Land-Klasse. Holt ein Land aus der Datenbank, wenn die ID > 0 ist.
     * 
     * @param int $id
     */
    function __construct($id = 0)
    {
        global $DB;

        $this->taxkey = new TaxKey();

        if($id > 0)
        {
            $sql = "SELECT * FROM countries WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $this->id = $r[0]["id"];
                $this->name = $r[0]["country_name"];
                $this->nameInt = $r[0]["country_name_int"];
                $this->code = $r[0]["country_code"];
                $this->active = $r[0]["country_active"];
                $this->eu = $r[0]["country_eu"];
                $this->taxkey = new TaxKey((int)$r[0]["country_taxkey"]);
            }
        }
    }

    /**
     * Liefer alle Laender, die aktiviert sind
     * 
     * @param String $order
     * @return multitype:Country
     */
    static function getAllCountries($order = self::ORDER_NAME)
    {
        global $DB;
        $retval = Array();
        
        $sql = "SELECT id FROM countries WHERE country_active = 1 ORDER BY {$order}";
        if ($DB->no_result($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new Country($r["id"]);
            }
        }
        return $retval;
    }
    
    /**
     * Liefer alle Laender, auch die, die nicht aktiviert sind
     *
     * @param String $order
     * @return Country[]
     */
    static function getEveryCountry($order = self::ORDER_ACTIVE_NAME)
    {
    	global $DB;
    	$retval = Array();
    
    	$sql = "SELECT id FROM countries ORDER BY {$order}";
    	if ($DB->no_result($sql))
    	{
    		foreach ($DB->select($sql) as $r)
    		{
    			$retval[] = new Country($r["id"]);
    		}
    	}
    	return $retval;
    }
    
    /**
     * Speicherfunktion für Laender
     */
    public function save($allcountries){
    	global $DB;
    	global $_USER;
    	
    	if($this->id > 0){
    		$sql = "UPDATE countries SET
		    		country_name = '{$this->name}',
		    		country_name_int = '{$this->nameInt}',
		    		country_code = '{$this->code}',
		    		country_active = {$this->active},
		    		country_eu = {$this->eu},
		    		country_taxkey = {$this->taxkey->getId()}
    				WHERE id = {$this->id} ";
    		return $DB->no_result($sql);
    	}
    	return false;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNameInt()
    {
        return $this->nameInt;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setNameInt($nameInt)
    {
        $this->nameInt = $nameInt;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getActive()
    {
        return $this->active;
    }

    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return int
     */
    public function getEu()
    {
        return $this->eu;
    }

    /**
     * @param int $eu
     */
    public function setEu($eu)
    {
        $this->eu = $eu;
    }

    /**
     * @return TaxKey
     */
    public function getTaxkey()
    {
        return $this->taxkey;
    }

    /**
     * @param TaxKey $taxkey
     */
    public function setTaxkey($taxkey)
    {
        $this->taxkey = $taxkey;
    }
}