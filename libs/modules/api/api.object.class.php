<?php

class APIObject
{
    private $id;
    private $api;
    private $object;
    private $type;
    
    function __construct($id = 0)
    {
       global $DB;
       global $_USER;
       
       if ($id > 0){
           $sql = "SELECT * FROM apis_objects WHERE id = {$id}";
           if($DB->num_rows($sql)){
               $r = $DB->select($sql);
               $r = $r[0];
               $this->id        = $r["id"];
               $this->api       = $r["api"];
               $this->object    = $r["object"];
               $this->type      = $r["type"];
           }
       }
    }
    
    function save(){
        global $DB;
        global $_USER;
    
        if($this->id > 0){
            $sql = "UPDATE apis_objects SET
                    api 	= {$this->api},
                    object 	= {$this->object},
                    type 	= {$this->type},
                    WHERE  id = {$this->id}";
            $res = $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO apis_objects
            (api, object, type)
            VALUES
            ({$this->api}, {$this->object}, {$this->type})";
            $res = $DB->no_result($sql);
            
		}
		
        if($res){
            $sql = "SELECT max(id) id FROM apis_objects WHERE api = {$this->api} AND object = {$this->object} AND type = {$this->type}";
            $thisid = $DB->select($sql);
            $this->id = $thisid[0]["id"];
            $res = true;
        } else {
            $res = false;
        }
    }
    
	public function delete(){
		global $DB;
		if($this->id > 0){
			$sql = "DELETE FROM apis_objects  
					WHERE id = {$this->id}";
			if($DB->no_result($sql)){
				unset($this);
				return true;
			} else {
				return false;
			}
		}
	}
	
	public static function getAllForObject($object,$type)
	{
	    global $DB;
	    $retval = Array();
	    $sql = "SELECT id FROM apis_objects WHERE type = {$type} AND object = {$object}";
	    if($DB->num_rows($sql))
	    {
			foreach($DB->select($sql) as $r){
				$retval[] = new APIObject($r["id"]);
			}
	    }
	    return $retval;
	}
	
	/**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

	/**
     * @return the $api
     */
    public function getApi()
    {
        return $this->api;
    }

	/**
     * @return the $object
     */
    public function getObject()
    {
        return $this->object;
    }

	/**
     * @return the $type
     */
    public function getType()
    {
        return $this->type;
    }

	/**
     * @param field_type $api
     */
    public function setApi($api)
    {
        $this->api = $api;
    }

	/**
     * @param field_type $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

	/**
     * @param field_type $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }
}

?>