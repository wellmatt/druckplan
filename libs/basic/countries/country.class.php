<?//--------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       18.10.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
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
    
    /**
     * Konstruktor der Land-Klasse. Holt ein Land aus der Datenbank, wenn die ID > 0 ist.
     * 
     * @param int $id
     */
    function __construct($id = 0)
    {
        global $DB;
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
     * @return multitype:Country
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
		    		country_active = {$this->active} 
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
}
?>