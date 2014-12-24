<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			27.09.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

/**
 * Klasse fuer die Mahnstufen
 *
 */
class Warnlevel{
	
	const ORDER_ID 		= " id ";
	const ORDER_TITLE 	= " title ";
	
	private $id = 0;
	private $status = 1;
	private $title;
	private $text;
	private $crt_date;
	private $crt_user;
	private $upd_date;
	private $upd_user;
	private $deadline;
	
	/**
	 * Konstruktor einer Mahnstufen, falls id>0 wird die entsprechende Warnstufe aus der DB geholt
	 *
	 * @param int $id
	 */
	function __construct($id = 0){
		global $DB;
		
		$this->crt_user = new User();
		$this->upd_user = new User();
		
		if ($id > 0){
			$sql = "SELECT * FROM warnlevel WHERE id = {$id}";
			if($DB->num_rows($sql)){
				$r = $DB->select($sql);
				$r = $r[0];
				$this->id = $r["id"];
				$this->status = $r["status"];
				$this->title = $r["title"];
				$this->text = $r["text"];				
				$this->crt_user = new User($r["crt_user"]);
				$this->crt_date = $r["crt_date"];
				$this->upd_user = new User($r["upd_user"]);
				$this->upd_date= $r["upd_date"];
				$this->deadline = $r["deadline"];
			}
		}
	}
	
	/**
	 * Speicher-Funktion für Mahnstufen
	 *
	 * @return boolean
	 */
	function save(){
		global $DB;
		global $_USER;
		$now = time();
	
		if($this->id > 0){
			$sql = "UPDATE warnlevel SET
					title 	= '{$this->title}', 
					text	= '{$this->text}', 
					deadline = {$this->deadline}, 
					status = {$this->status}, 
					upd_user = {$_USER->getId()}, 
					upd_date = UNIX_TIMESTAMP()
					WHERE id = {$this->id}";
				return $DB->no_result($sql);
		} else {
			$sql = "INSERT INTO warnlevel
					(status, title, text, 
					crt_date, crt_user, deadline)
					VALUES
					({$this->status}, '{$this->title}', '{$this->text}', 
					{$now}, {$_USER->getId()}, {$this->deadline} )";
			$res = $DB->no_result($sql);
	
			if($res){
				$sql = "SELECT max(id) id FROM warnlevel WHERE title = '{$this->title}'";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["id"];
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Loeschfunktion fuer Mahnstufen. Kein echtes Loechen, der Status wird auf 0 gesetzt.
	 *
	 * @return boolean
	 */
	public function delete(){
		global $DB;
		if($this->id > 0){
			$sql = "UPDATE warnlevel 
					SET
					status = 0
					WHERE id = {$this->id}";
			if($DB->no_result($sql)){
				unset($this);
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Funktion liefert alle aktiven Mahnstufen nach angegebener Reighenfolge
	 *
	 * @param STRING $order Reihenfolge
	 * @return Array : Warnlevel
	 */
	static function getAllWarnlevel($order = self::ORDER_TITLE){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM warnlevel WHERE status > 0 ORDER BY {$order}";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Warnlevel($r["id"]);
			}
		}
		return $retval;
	}
	

	public function getId()
	{
	    return $this->id;
	}

	public function getStatus()
	{
	    return $this->status;
	}

	public function setStatus($status)
	{
	    $this->status = $status;
	}

	public function getTitle()
	{
	    return $this->title;
	}

	public function setTitle($title)
	{
	    $this->title = $title;
	}

	public function getText()
	{
	    return $this->text;
	}

	public function setText($text)
	{
	    $this->text = $text;
	}

	public function getCrt_date()
	{
	    return $this->crt_date;
	}

	public function setCrt_date($crt_date)
	{
	    $this->crt_date = $crt_date;
	}

	public function getCrt_user()
	{
	    return $this->crt_user;
	}

	public function setCrt_user($crt_user)
	{
	    $this->crt_user = $crt_user;
	}

	public function getUpd_date()
	{
	    return $this->upd_date;
	}

	public function setUpd_date($upd_date)
	{
	    $this->upd_date = $upd_date;
	}

	public function getUpd_user()
	{
	    return $this->upd_user;
	}

	public function setUpd_user($upd_user)
	{
	    $this->upd_user = $upd_user;
	}

	public function getDeadline()
	{
	    return $this->deadline;
	}

	public function setDeadline($deadline)
	{
	    $this->deadline = $deadline;
	}
}
?>