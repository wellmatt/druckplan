<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			26.06.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

class Ticketcomment {

	const ORDER_CRTDATE			= " tc_crtdate desc";						// Erstalldatum
	const ORDER_STATUS_CRTDATE 	= " tc_state asc, tc_crtdate desc ";	// Status, dann Erstelldatum

	private $id = 0;				// Id des Tickets
	private $state = 1;				// Status des Tickets
	private $ticketid;				// Ticketnummer
	private $comment;				// Kommentarinhalt
	private $crtuser;				// Benutzer, der den Kommentar verfasst hat
	private $crtdate;				// Erstelldatum
	private $custVisible;			// Sichtbarkeit beim Kunden (Kundenportal)
	
	/**
	 * Kostruktor fuer Kommentare von Tickets 
	 * 
	 * @param int $id
	 */
	function __construct($id = 0){
		global $DB;
		global $_USER;
	
		$this->crtuser 			= new User(0);
		$this->customer 		= new BusinessContact(0);
	
		if($id>0){
			$sql = "SELECT * FROM ticketcomments WHERE id = {$id}";
			if($DB->num_rows($sql)){
				$r = $DB->select($sql);
				$r = $r[0];
				$this->id 			= (int)$r["id"];
				$this->state 		= (int)$r["tc_state"];
				$this->ticketid		= $r["tc_ticketid"];
				$this->comment		= $r["tc_comment"];
				$this->crtdate		= $r["tc_crtdate"];
				$this->crtuser		= new User($r["tc_crtuser"]);
				$this->custVisible	= $r["tc_cust_visible"];
			}
		}
	}
	
	/**
	 * Speicherfunktion fuer Ticketkommentare
	 * 
	 * @return boolean
	 */
	function save(){
		global $DB;
		global $_USER;
		$now = time();
		
		if($this->id > 0){
			$sql = "UPDATE ticketcomments SET
					tc_state			= {$this->state},
					tc_ticketid	 		= {$this->ticketid},
					tc_comment 			= '{$this->comment}',
					tc_crtuser			= {$this->crtuser->getId()}, 
					tc_cust_visible		= {$this->custVisible} 
					WHERE id = {$this->id}";
			return $DB->no_result($sql);
		} else {
			$sql = "INSERT INTO ticketcomments
					(tc_state, tc_ticketid, tc_crtuser,
					tc_comment, tc_crtdate, tc_cust_visible )
					VALUES
					({$this->state}, {$this->ticketid}, {$this->crtuser->getId()},
					'{$this->comment}', {$now} , {$this->custVisible} )";
			// error_log($sql);
			$res = $DB->no_result($sql);
			
			if($res){
				$sql = "SELECT max(id) id FROM ticketcomments WHERE tc_comment = '{$this->comment}'";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["id"];
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Funktion liefert alle Kommentare zu einem Ticket
	 * 
	 * @param String $order
	 * @param int $ticketid
	 * @return multitype:Ticketkomment
	 */
	static function getAllTicketcomments( $ticketid, $order = self::ORDER_STATUS_CRTDATE){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM ticketcomments WHERE 
				tc_state > 0 AND
				tc_ticketid = {$ticketid}
				ORDER BY {$order} ";
		if($DB->num_rows($sql)) {
			foreach($DB->select($sql) as $r){
				$retval[] = new Ticketcomment($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Liefert alle Kommentare zu einem Ticket, die fuer einen Kunden sichtbar sind
	 *
	 * @param String $order
	 * @param int $ticketid
	 * @return multitype:Ticketkomment
	 */
	static function getAllTicketcommentsForCustomer( $ticketid, $order = self::ORDER_CRTDATE){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM ticketcomments WHERE
				tc_state > 0 AND 
				tc_cust_visible = 1 AND 
				tc_ticketid = {$ticketid} 
				ORDER BY {$order} ";
		if($DB->num_rows($sql)) {
			foreach($DB->select($sql) as $r){
				$retval[] = new Ticketcomment($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Liefert die letzten 3 Kommentar zu einem Ticket als formatierten String 
	 * 
	 * @param int $ticketid
	 * @return String
	 */
	static function getLastComment($ticketid, $order = self::ORDER_STATUS_CRTDATE){
		global $DB;
		$retval = "";
		$comment = Array();
		
		$sql = "SELECT id FROM ticketcomments WHERE
				tc_state > 0 AND
				tc_ticketid = {$ticketid}
				ORDER BY {$order} ";
		if($DB->num_rows($sql)) {
			$r = $DB->select($sql);
			$comment[0] = new Ticketcomment($r[0]["id"]);
			$comment[1] = new Ticketcomment($r[1]["id"]);
			$comment[2] = new Ticketcomment($r[2]["id"]);
		} else {
			return "";
		}
		
		for ($i=0; $i<=2;$i++){
			if($comment[$i]->getComment() != NULL && $comment[$i]->getComment() != ""){
				if ($comment[$i]->getCrtuser()->getId() > 1 ){
					$username = $comment[$i]->getCrtuser()->getNameAsLine();
				} else {
					$username = "Der Kunde ";
				}
				$retval .= $username." - " .date('d.m.y-H:i',$comment[$i]->getCrtdate())." ";
				$retval .= " (".$comment[$i]->getStateToken().") \n";
				$retval .= $comment[$i]->getComment()."\n \n";
			}
		}
		return $retval;
	}
	
	/**
	 * Liefert den letzten Status eines Kommentars zu einem Ticket als formatierten String
	 *
	 * @param int $ticketid
	 * @return String
	 */
	static function getLastCommentStatus($ticketid, $order=self::ORDER_STATUS_CRTDATE){
		global $DB;
		$retval = false;
		$comment = new Ticketcomment();

		$sql = "SELECT id FROM ticketcomments WHERE
				tc_state > 0 AND
				tc_ticketid = {$ticketid}
				ORDER BY {$order} ";
		if($DB->num_rows($sql)) {
			$r = $DB->select($sql);
			$comment = new Ticketcomment($r[0]["id"]);
		} else {
			return false;
		}

		return $comment->getState();
	}
	
	/**
	 * Loeschfunktion fuer Ticketkommentare
	 */
	public function delete(){
		global $DB;
		$sql = "DELETE FROM ticketcomments 
				WHERE 
				id = {$this->id}";
		$DB->no_result($sql);
		unset($this);
	}
	
	/**
	 *Liefert die Abkuerzung fuer den Status eines Ticket
	 * 
	 * @return string
	 */	
	 public function getStateToken(){
		$retval= " ";
		
		switch($this->state){
			case 1: $retval = "N"; break;
			case 2: $retval = "iA"; break;
			case 3: $retval = "E"; break;
			default: $retval = "."; break; 
		}
		return $retval;
	}
	

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

	public function getTicketid()
	{
	    return $this->ticketid;
	}

	public function setTicketid($ticketid)
	{
	    $this->ticketid = $ticketid;
	}

	public function getComment()
	{
	    return $this->comment;
	}

	public function setComment($comment)
	{
	    $this->comment = $comment;
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

    public function getCustVisible()
    {
        return $this->custVisible;
    }

    public function setCustVisible($custVisible)
    {
        $this->custVisible = $custVisible;
    }
}
?>