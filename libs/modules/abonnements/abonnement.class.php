<? 
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			23.02.2015
// Copyright:		2015 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/notifications/notification.class.php';

class Abonnement {

    private $id;
    private $abouser;
    private $module;
    private $objectid;

    function __construct($id = 0){
        global $DB;
        global $_USER;

        $this->abouser	= new User(0);

        if($id>0){
            $sql = "SELECT * FROM abonnements WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                $this->id 				= (int)$r["id"];
                $this->abouser 			= new User((int)$r["abouser"]);
                $this->module		    = $r["module"];
                $this->objectid 		= (int)$r["objectid"];
            }
        }
    }

    /**
     * Speicher-Funktion fuer Abonnements
     *
     * @return boolean
     */
    function save()
    {
        global $DB;
        global $_USER;
        $now = time();

        if ($this->id == 0) {
            $sql = "INSERT INTO abonnements
            (abouser, module, objectid )
            VALUES
            ( {$this->abouser->getId()}, '{$this->module}', {$this->objectid} )";
            $res = $DB->no_result($sql);
//             echo $sql;
            if ($res) {
                $sql = "SELECT max(id) id FROM abonnements WHERE module = '{$this->module}' AND objectid = {$this->objectid} ";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Loeschfunktion fuer Abonnements.
     *
     * @return boolean
     */
    public function delete()
    {
        global $DB;
        if ($this->id > 0) {
            $sql = "DELETE FROM abonnements 
            WHERE id = {$this->id}";
            if ($DB->no_result($sql)) {
                unset($this);
                return true;
            } else {
                return false;
            }
        }
    }
    
    public static function getMyAbonnements($limit = 10){
        global $DB;
        global $_USER;
        $retval = Array();
    
        $sql = "SELECT id FROM abonnements WHERE abouser = {$_USER->getId()} ORDER BY module, objectid DESC LIMIT {$limit}";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new Abonnement($r["id"]);
            }
        }
        return $retval;
    }
    
    public static function getMyTicketAbonnementsForDtList(){
        global $DB;
        global $_USER;
        $retval = Array();
    
        $sql = "SELECT id FROM abonnements WHERE abouser = {$_USER->getId()} AND module = 'Ticket' ORDER BY objectid DESC";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new Abonnement($r["id"]);
            }
        }
        
        if (count($retval)>0)
        {
            $retstring = "";
            foreach ($retval as $tmp_abo)
            {
                $retstring .= $tmp_abo->getObjectid() . ",";
            }
            $retstring = substr($retstring, 0, strlen($retstring)-1);
            $retstring .= "";
        } else {
            $retstring = "";
        }
        
        return $retstring;
    }

    /**
     * @param $module
     * @param $objectid
     * @return Abonnement[]
     */
    public static function getAbonnementsForObject($module,$objectid)
    {
        global $DB;
        $retval = Array();
        
        $sql = "SELECT id FROM abonnements WHERE module = '{$module}' AND objectid = {$objectid} ORDER BY id ASC";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
            	$retval[] = new Abonnement($r["id"]);
            }
        }
        
        return $retval;
    }
    
    public static function getAbonnement($user,$module,$objectid)
    {
        global $DB;
        
        $sql = "SELECT id FROM abonnements WHERE abouser = {$user->getId()} AND module = '{$module}' AND objectid = {$objectid} ORDER BY id ASC";
//         echo $sql;
        if($DB->num_rows($sql)){
            $r = $DB->select($sql);
            return new Abonnement($r[0]["id"]);
        } else {
            return new Abonnement();
        }
        
        return $retval;
    }
    
    public static function hasAbo($object,$user = NULL)
    {
        global $_USER;
        global $DB;
        $objectid = $object->getId();
        $module = get_class($object);
        if ($user == NULL){
            $user = $_USER;
        }
        
        $sql = "SELECT id FROM abonnements WHERE abouser = {$user->getId()} AND module = '{$module}' AND objectid = {$objectid}";
//         echo $sql . "</br>";
        if($DB->num_rows($sql)>0){
            return true;
        } else {
            return false;
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
     * @return the $abouser
     */
    public function getAbouser()
    {
        return $this->abouser;
    }

	/**
     * @return the $module
     */
    public function getModule()
    {
        return $this->module;
    }

	/**
     * @return the $objectid
     */
    public function getObjectid()
    {
        return $this->objectid;
    }

	/**
     * @param number $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

	/**
     * @param User $abouser
     */
    public function setAbouser($abouser)
    {
        $this->abouser = $abouser;
    }

	/**
     * @param field_type $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

	/**
     * @param number $objectid
     */
    public function setObjectid($objectid)
    {
        $this->objectid = $objectid;
    }
}


?>