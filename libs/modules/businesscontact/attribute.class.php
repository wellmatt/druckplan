<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			26.08.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

class Attribute{
	
	const ORDER_CRTDATE = " crtdate ";
	const ORDER_TITLE	= " title ";
	const ORDER_CRTUSER = " crtuser ";
	
	const MODULE_BUSINESSCONTACT 	= 1;
	const MODULE_TICKETS			= 2;
	const MODULE_ORDER				= 3;
	const MODULE_PLANER				= 4;
	const MODULE_CONTACTPERSON		= 5;
	
	private $id = 0;			// ID des Attributes/Merkmals
	private $state;				// Status
	private $crtuser;			// Ersteller
	private $crtdate;			// Erstelldatum
	private $module;			// zugehoeriges Modul
	private $objectid;			// ID des zugehoerigen Objekts
	private $title;				// Name des Attributes
	private $comment;			// Inhalt des Attributes
	private $enable_customer;	// Aktivierung fuer Geschaeftskontakte
	private $enable_contact;	// Aktivierung fuer Kontaktpersonen
	
	function __construct($id){
		global $DB;
		
		$this->crtuser = new User();
		
		if($id > 0){
			$sql = "SELECT * FROM attributes WHERE id = {$id}";
			if($DB->num_rows($sql))
			{
				$res = $DB->select($sql);
				$res = $res[0];
		
				$this->id = $res["id"];
				$this->state = $res["state"];
				$this->crtuser = new User($res["crtuser"]);
				$this->crtdate = $res["crtdate"];
				$this->module = $res["module"];
				$this->objectid = $res["onjectid"];
				$this->title = $res["title"];
				$this->comment = $res["comment"];
				$this->enable_contact = $res["enable_contacts"];
				$this->enable_customer = $res["enable_customer"];
			}
		}
		
    }
    
	function save() {
        global $DB;
        global $_USER;
        
        if($this->id > 0){
            $sql = "UPDATE attributes SET
                    title = '{$this->title}', 
                    comment = '{$this->comment}', 
                    enable_contacts = {$this->enable_contact},
                    enable_customer = {$this->enable_customer},
                    state = {$this->state}
                    WHERE id = {$this->id}";

            return $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO attributes
                        (state, title, comment, crtdate, crtuser, 
                         enable_contacts, enable_customer)
                    VALUES
                        (1, '{$this->title}', '{$this->comment}', UNIX_TIMESTAMP(), {$_USER->getId()}, 
            			 {$this->enable_contact}, {$this->enable_customer} )";
            $res = $DB->no_result($sql);

            if($res) {
                $sql = "SELECT max(id) id FROM attributes WHERE title = '{$this->title}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else 
                return false;
        }
    }
	
	function delete(){
        global $DB;
        if($this->id){
            $sql = "UPDATE attributes SET state = 0 WHERE id = {$this->id}";
            if($DB->no_result($sql)){
                unset($this);
                return true;
            } else
                return false;
        }
        return false;
	}
	
	static function getAllAttributes($order = self::ORDER_TITLE, $module = 0, $objectid = 0){
		$retval = Array();
		global $DB;
		
		$sql = "SELECT id FROM attributes
				WHERE 
				state > 0 ";
		
		if($module > 0){
			$sql .= " AND module = {$module} "; 
		}
		
		if($objectid > 0){
			$sql .= " AND objectid = {$objectid} ";
		}
		
		$sql .= " ORDER BY {$order} ";
		 
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Attribute($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * ... liefert alle Attribute, die fuer Geschaeftskontakte freigeschaltet sind
	 * 
	 * @param String $order: Sortierung
	 * @return multitype:Attribute
	 */
	static function getAllAttributesForCustomer($order = self::ORDER_TITLE){
		$retval = Array();
		global $DB;
	
		$sql = "SELECT id FROM attributes
				WHERE
				state > 0
				AND enable_customer = 1 
				ORDER BY {$order} ";
				
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Attribute($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * ... liefert alle Attribute, die fuer Kontaktpersonen freigeschaltet sind
	 *
	 * @param String $order: Sortierung
	 * @return multitype:Attribute
	 */
	static function getAllAttributesForContactperson($order = self::ORDER_TITLE){
		$retval = Array();
		global $DB;
	
		$sql = "SELECT id FROM attributes
				WHERE
				state > 0
				AND enable_contacts = 1
				ORDER BY {$order} ";
	
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Attribute($r["id"]);
			}
		}
		return $retval;
	}
	
	static function getAllAttributesByObject($order = self::ORDER_TITLE, $module = 0, $objectid = 0){
		$retval = Array();
		global $DB;
	
		$sql = "SELECT id FROM attributes
				WHERE
				state > 0 ";
	
		if($module > 0){
			$sql .= " AND module = {$module} ";
		}
		
		$sql .= " AND objectid = {$objectid} 
				  ORDER BY {$order} ";
				
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Attribute($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Liefert alle Optionen/Eintraege zu einem Merkmal
	 *
	 * @return Array 
	 */
	function getItems($order=self::ORDER_TITLE){
		$retval = Array();
		global $DB;
	
		$sql = "SELECT * FROM attributes_items
				WHERE
				status > 0 
				AND attribute_id = {$this->getId()} 
				ORDER BY {$order} ";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = $r;
			}
		}
		return $retval;
	}
	
	/**
	 * Speicher ein Array von Optionen eines Merkmals
	 * 
	 * @param Array $items
	 */
	function saveItems($items){
		global $DB;
		
		foreach($items as $item){
			if((int)$item["id"] > 0){
	            $sql = "UPDATE attributes_items SET
	                    title = '{$item["title"]}', 
	                    input = {$item["input"]} 
	                    WHERE id = {$item["id"]}";
	            
	            $DB->no_result($sql);
	        } else {
	            $sql = "INSERT INTO attributes_items
	                        (status, title, attribute_id, input )
	                    VALUES
	                        (1, '{$item["title"]}', {$this->id}, {$item["input"]} )";
	            $DB->no_result($sql);
			}
		}
	}
	
	/**
	 * Entfert eine Option eines Merkmals
	 * 
	 * @param int $itemID
	 */
	function deleteItem($itemID){
		global $DB;
		$sql = "UPDATE attributes_items SET
				status = 0
				WHERE id = {$itemID}";
		
		$DB->no_result($sql);
	}
		
	// ************************************ GETTER & SETTER *********************************************************************
	
	public function getId()
	{
	    return $this->id;
	}

	public function getState()
	{
	    return $this->state;
	}

	public function setState($state)
	{
	    $this->state = $state;
	}

	public function getCrtuser()
	{
	    return $this->crtuser;
	}

	public function setCrtuser($crtuser)
	{
	    $this->crtuser = $crtuser;
	}

	public function getCrtdate()
	{
	    return $this->crtdate;
	}

	public function setCrtdate($crtdate)
	{
	    $this->crtdate = $crtdate;
	}

	public function getModule()
	{
	    return $this->module;
	}

	public function setModule($module)
	{
	    $this->module = $module;
	}

	public function getObjectid()
	{
	    return $this->objectid;
	}

	public function setObjectid($objectid)
	{
	    $this->objectid = $objectid;
	}

	public function getTitle()
	{
	    return $this->title;
	}

	public function setTitle($title)
	{
	    $this->title = $title;
	}

	public function getComment()
	{
	    return $this->comment;
	}

	public function setComment($comment)
	{
	    $this->comment = $comment;
	}
	
	/**
	 * ... macht das selbe wie getComment
	 */
	public function getValue()
	{
		return $this->comment;
	}

	/**
	 * ... macht das selbe wie setComment
	 */
	public function setValue($value)
	{
	    $this->comment = $value;
	} 

	public function getEnable_customer()
	{
	    return $this->enable_customer;
	}

	public function setEnable_customer($enable_customer)
	{
	    $this->enable_customer = $enable_customer;
	}

	public function getEnable_contact()
	{
	    return $this->enable_contact;
	}

	public function setEnable_contact($enable_contact)
	{
	    $this->enable_contact = $enable_contact;
	}
}
?>
