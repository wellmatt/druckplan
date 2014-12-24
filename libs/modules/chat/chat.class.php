<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			26.08.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
//
// Klasse fuer Chat-Nachrichten
//
// ----------------------------------------------------------------------------------

class Chat{
	
	const ORDER_CRTDATE = " crtdate ";
	const ORDER_DATE 	= " crtdate desc";
	const ORDER_TITLE	= " title ";
	const ORDER_CRTUSER = " crtuser ";
	
	private $id = 0;		// ID der ChatNachricht
	private $state;			// Status
	private $crtdate;		// Erstelldatum
	private $title;			// Titel der Notiz
	private $comment;		// Inhalt der Notiz
	private $from;			// Verfasser 
	private $to;			// Empfaenger 
	
	function __construct($id){
		global $DB;
		
		$this->from = new User();
		$this->to	= new User();
		
		if($id > 0){
			$sql = "SELECT * FROM chat WHERE id = {$id}";
			if($DB->num_rows($sql))
			{
				$res = $DB->select($sql);
				$res = $res[0];
		
				$this->id = $res["id"];
				$this->state = $res["state"];
				$this->from = new User($res["from_id"]);
				$this->to	= new User($res["to_id"]);
				$this->crtdate = $res["crtdate"];
				$this->title = $res["title"];
				$this->comment = $res["comment"];
			}
		}
		
    }
    
	function save() {
        global $DB;
        global $_USER;
        
        if($this->id > 0){
            $sql = "UPDATE chat SET
                        state = {$this->state}
                    WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO chat
                        (state, title, comment, crtdate, from_id, 
                         to_id)
                    VALUES
                        (1, '{$this->title}', '{$this->comment}', UNIX_TIMESTAMP(), {$this->from->getId()}, 
            			 {$this->to->getId()} )";
            $res = $DB->no_result($sql);
            if($res) {
                $sql = "SELECT max(id) id FROM chat WHERE title = '{$this->title}' AND from_id= {$_USER->getId()}";
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
            $sql = "UPDATE chat SET state = 0 WHERE id = {$this->id}";
            if($DB->no_result($sql)){
                unset($this);
                return true;
            } else
                return false;
        }
        return false;
	}
	
	static function getAllChatsForMe($order = self::ORDER_DATE, $to = 0){
		$retval = Array();
		global $DB;
		
		$sql = "SELECT id FROM chat
				WHERE 
				state > 0 ";
		
		if($to > 0){
			$sql .= " AND to_id = {$to} ";
		}
		
		$sql .= " ORDER BY {$order} ";
		
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Chat($r["id"]);
			}
		}
		
		return $retval;
	}
	
	static function getChatProtokoll($order = self::ORDER_DATE, $from = 0, $to = 0){
		$retval = Array();
		global $DB;
	
		$sql = "SELECT id FROM chat
				WHERE
				state > 0 
				AND (( from_id = {$from}  AND  to_id = {$to} ) OR (from_id = {$to}  AND  to_id = {$from} ))
				ORDER BY {$order} ";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
			$retval[] = new Chat($r["id"]);
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

	public function getCrtdate()
	{
	    return $this->crtdate;
	}

	public function setCrtdate($crtdate)
	{
	    $this->crtdate = $crtdate;
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

	public function getFrom()
	{
	    return $this->from;
	}

	public function setFrom($from)
	{
	    $this->from = $from;
	}

	public function getTo()
	{
	    return $this->to;
	}

	public function setTo($to)
	{
	    $this->to = $to;
	}
}
?>