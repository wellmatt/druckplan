<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			27.01.2014
// Copyright:		2013-14 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

class Notes{
	
	const ORDER_CRTDATE = " crtdate DESC ";
	const ORDER_TITLE	= " title ";
	const ORDER_CRTUSER = " crtuser ";
	
	const MODULE_COMMISSIONCONTACT 	= 1;
	const MODULE_BUSINESSCONTACT 	= 1;
	const MODULE_TICKETS			= 2;
	const MODULE_ORDER				= 3;
	const MODULE_PLANER				= 4;
	const MODULE_CALCULATION		= 5; //gln
	
	const FILE_DESTINATION = "./docs/notes_files/";
	
	private $id = 0;		// ID der Notiz
	private $state = 1;			// Status
	private $crtuser;		// Ersteller
	private $crtdate;		// Erstelldatum
	private $module;		// zugehoeriges Modul
	private $objectid;		// ID des zugehoerigen Objekts
	private $title;			// Titel der Notiz
	private $comment;		// Inhalt der Notiz 
	private $fileName;		// Dateiname zur angehaengten Datei in Ordner "./docs/notes_files/"
	
	function __construct($id){
		global $DB;
		
		$this->crtuser = new User();
		
		if($id > 0){
			$sql = "SELECT * FROM notes WHERE id = {$id}";
			if($DB->num_rows($sql))
			{
				$res = $DB->select($sql);
				$res = $res[0];
		
				$this->id = $res["id"];
				$this->state = $res["state"];
				$this->crtuser = new User($res["crtuser"]);
				$this->crtdate = $res["crtdate"];
				$this->module = $res["module"];
				$this->objectid = $res["objectid"];
				$this->title = $res["title"];
				$this->comment = $res["comment"];
				$this->fileName = $res["file_name"];
			}
		}
		
    }
    
	function save() {
        global $DB;
        global $_USER;
        
        if($this->id > 0){
            $sql = "UPDATE notes SET
                        title = '{$this->title}', 
                        comment = '{$this->comment}', 
                        objectid = {$this->objectid},
                        module = {$this->module},
                        state = {$this->state}, 
                        file_name = '{$this->fileName}' 
                    WHERE id = {$this->id}";
//error_log( $sql);
            return $DB->no_result($sql);

        } else {
            $sql = "INSERT INTO notes
                        (state, title, comment, crtdate, crtuser, 
                         objectid, module, file_name)
                    VALUES
                        (1, '{$this->title}', '{$this->comment}', UNIX_TIMESTAMP(), {$_USER->getId()}, 
            			 {$this->objectid}, {$this->module}, '{$this->fileName}' )";
            $res = $DB->no_result($sql);
//error_log( $sql);
            if($res) {
                $sql = "SELECT max(id) id FROM notes WHERE title = '{$this->title}' AND objectid = {$this->objectid}";
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
            $sql = "UPDATE notes SET state = 0 WHERE id = {$this->id}";
            if($DB->no_result($sql)){
                unset($this);
                return true;
            } else
                return false;
        }
        return false;
	}
	
	static function getAllNotes($order = self::ORDER_TITLE, $module = 0, $objectid = 0){
		$retval = Array();
		global $DB;
		
		$sql = "SELECT id FROM notes
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
				$retval[] = new Notes($r["id"]);
			}
		}
		
		return $retval;
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

	public function getFileName()
	{
	    return $this->fileName;
	}

	public function setFileName($fileName)
	{
	    $this->fileName = $fileName;
	}
}
?>