<?php
// ----------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
 

class Association {
    
    private $id;
    private $module1;
    private $objectid1;
    private $module2;
    private $objectid2;
    private $crtdate;
    private $crtuser;
    
    
    function __construct($id = 0){
        global $DB;
    
        $this->crtuser	= new User(0);
        
        if($id>0){
            $sql = "SELECT * FROM association WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                $this->id 				= (int)$r["id"];
                $this->module1		    = $r["module1"];
                $this->objectid1		= $r["objectid1"];
                $this->module2		    = $r["module2"];
                $this->objectid2		= $r["objectid2"];
                $this->crtdate		    = $r["crtdate"];
                $this->crtuser		    = new User($r["crtuser"]);
    
            }
        }
    }

    /**
     * Speicher-Funktion fuer Association
     *
     * @return boolean
     */
    function save()
    {
        global $DB;
        global $_USER;
        $now = time();
        
        $sql = "INSERT INTO association
        (module1, objectid1, module2, objectid2, crtdate, crtuser)
        VALUES
        ( '{$this->module1}', {$this->objectid1}, '{$this->module2}', {$this->objectid2}, {$now}, {$_USER->getId()} )";
        $res = $DB->no_result($sql);
        if ($res) {
            $sql = "SELECT max(id) id FROM association WHERE crtdate = {$now}";
            $thisid = $DB->select($sql);
            $this->id = $thisid[0]["id"];
            $this->crtdate = $now;
            $this->crtuser = $_USER;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Loeschfunktion fuer Association
     *
     * @return boolean
     */
    public function delete()
    {
        global $DB;
        if ($this->id > 0) {
            $sql = "DELETE FROM association 
    			    WHERE id = {$this->id}";
            if ($DB->no_result($sql)) {
                unset($this);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    public static function getAssociationsForObject($module,$objectid)
    {
        global $DB;
        $retval = Array();
    
        $sql = "SELECT id FROM association WHERE (module1 = '{$module}' AND objectid1 = {$objectid}) OR (module2 = '{$module}' AND objectid2 = {$objectid})";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new Association($r["id"]);
            }
        }
        return $retval;
    }
    
    public static function getName($object)
    {
        $classname = get_class($object);
        switch ($classname)
        {
            case "Order":
                return $object->getNumber() . ' ' . $object->getTitle();
                break;
            case "CollectiveInvoice":
                return $object->getNumber() . ' ' . $object->getTitle();
                break;
            case "Event":
                return $object->getTitle();
                break;
            case "Schedule":
                return $object->getNumber() . ' ' . $object->getObject();
                break;
            case "Machine":
                return $object->getName();
                break;
        }
    }
    
    public static function getPath($classname)
    {
        switch ($classname)
        {
            case "Order":
                return 'libs/modules/calculation/order.php&exec=edit&step=4&id=';
                break;
            case "CollectiveInvoice":
                return 'libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid=';
                break;
            case "Event":
                return 'libs/modules/organizer/calendar.php&exec=showevent&id=';
                break;
            case "Schedule":
                return 'libs/modules/schedule/schedule.php&exec=parts&id=';
                break;
            case "Machine":
                return 'libs/modules/machines/machines.php&exec=edit&id=';
                break;
        }
    }
    
	/**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

	/**
     * @return the $module1
     */
    public function getModule1()
    {
        return $this->module1;
    }

	/**
     * @return the $objectid1
     */
    public function getObjectid1()
    {
        return $this->objectid1;
    }

	/**
     * @return the $module2
     */
    public function getModule2()
    {
        return $this->module2;
    }

	/**
     * @return the $objectid2
     */
    public function getObjectid2()
    {
        return $this->objectid2;
    }

	/**
     * @return the $crtdate
     */
    public function getCrtdate()
    {
        return $this->crtdate;
    }

	/**
     * @return the $crtuser
     */
    public function getCrtuser()
    {
        return $this->crtuser;
    }

	/**
     * @param number $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

	/**
     * @param field_type $module1
     */
    public function setModule1($module1)
    {
        $this->module1 = $module1;
    }

	/**
     * @param field_type $objectid1
     */
    public function setObjectid1($objectid1)
    {
        $this->objectid1 = $objectid1;
    }

	/**
     * @param field_type $module2
     */
    public function setModule2($module2)
    {
        $this->module2 = $module2;
    }

	/**
     * @param field_type $objectid2
     */
    public function setObjectid2($objectid2)
    {
        $this->objectid2 = $objectid2;
    }

	/**
     * @param Ambigous <number, unknown> $crtdate
     */
    public function setCrtdate($crtdate)
    {
        $this->crtdate = $crtdate;
    }

	/**
     * @param Ambigous <User, boolean, string> $crtuser
     */
    public function setCrtuser($crtuser)
    {
        $this->crtuser = $crtuser;
    }
    
}


?>