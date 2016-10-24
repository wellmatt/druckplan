<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			22.10.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';
require_once 'libs/modules/article/article.class.php';

/**
 * Klasse zur Verwaltung von Zeiten
 */
class Timekeeper {
	
	const ORDER_ID 			= " id ";
	const ORDER_START		= " startdate ";
	const ORDER_START_ASC	= " startdate asc ";
	const ORDER_START_DESC	= " startdate desc ";
	const ORDER_END			= " enddate ";
	
	// Modultype des Objekts - Bei Aenderungen bitte in getModuleName() und getObjectName() anpassen
	const MODULE_TICKET 		= 1;
	const MODULE_CALCULATION	= 2;
	const MODULE_COLLECTIVE		= 3;
	const MODULE_PLANNING 		= 4;
	 
	private $id = 0;
	private $userID;				// ID des zugehoerigen Benutzers
	private $status = 1;			// 0 = geloescht, 1 = aktiv
	private $startdate = 0;			// Anfangsdatum
	private $enddate = 0;			// Enddatum
	private $objectID;				// ID des zugehoerigen Objekts
	private $subObjectID = 0;		// ID des zugehoerigen Teil eines Objekts, z.B. Maschine(nplanung) in der Planung
	private $module;				// ID des zugehoerigen Moduls (s.o.)
	private $comment;				// Kommentar des Benutzers
	private $articleId = 0;			// ID des verknuepften Artikels
	private $articleAmount = 0;		// Menge des verknuepften Artikels
	 
	/**
	 * Konstruktor
	 * 
	 * @param Int $id
	*/
	function __construct($id = 0){
		global $DB;
		global $_USER;
		
		if ($id > 0){
			$sql = " SELECT * FROM timekeeper WHERE id = {$id}";
			if($DB->num_rows($sql) > 0){
				$res = $DB->select($sql);
				$this->id = $res[0]["id"];
				$this->status = $res[0]["status"];
				$this->userID = $res[0]["user_id"];
				$this->module = $res[0]["module"];
				$this->objectID = $res[0]["object_id"];
				$this->startdate = $res[0]["startdate"];
				$this->enddate = $res[0]["enddate"];
				$this->comment = $res[0]["comment"];
				$this->subObjectID = $res[0]["sub_object_id"];
				$this->articleId = $res[0]["article_id"];
				$this->articleAmount = $res[0]["article_amount"];
			}
		}
	}
	
	/**
	 * Speicherfunktion fuer Zeitmessungen
	 * 
	 * @return boolean
	 */
	function save(){
		global $DB;
		global $_USER;
		
		if($this->id > 0){
			$sql = "UPDATE timekeeper SET 
					status = {$this->status}, 
					user_id = {$this->userID}, 
					comment	= '{$this->comment}', 
					module = {$this->module}, 
					object_id = {$this->objectID},
					sub_object_id = {$this->subObjectID},  
					startdate= {$this->startdate}, 
					enddate= {$this->enddate}, 
					article_id = {$this->articleId}, 
					article_amount = {$this->articleAmount} 
					WHERE id = {$this->id}";
			return $DB->no_result($sql);
		} else {
			$sql = "INSERT INTO timekeeper
					(status, user_id, object_id, module, 
					comment, startdate, enddate,
					sub_object_id, article_id, article_amount )
					VALUES
					(1, {$this->userID}, {$this->objectID}, {$this->module}, 
					'{$this->comment}', {$this->startdate}, {$this->enddate},
					{$this->subObjectID}, {$this->articleId}, {$this->articleAmount} )";
			$res = $DB->no_result($sql);

			if($res){
				$sql = "SELECT max(id) id FROM timekeeper WHERE user_id = {$this->userID} AND object_id = {$this->objectID} ";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["id"];
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Abruf aller Zeitmessungen
	 * 
	 * @param String $order
	 * @param Int $user_id
	 * @return Array
	 */
	static function getAllTimekeeper($order = Timekeeper::ORDER_ID, $user_id=0, $object_id=0, $module=0){
		global $DB;
		$timekeeper = Array();
	
		$sql = " SELECT id FROM timekeeper WHERE status > 0 AND enddate > 0  ";
		if ($user_id > 0){
			$sql .= " AND user_id = {$user_id} ";
		}
		if ($module > 0){
			$sql .= " AND module = {$module} ";
		}
		if ($object_id > 0){
			$sql .= " AND object_id = {$object_id} ";
		}
		$sql .= " ORDER BY {$order}";
		$res = $DB->select($sql);
		
		foreach ($res as $r){
			$timekeeper[] = new Timekeeper($r["id"]);
		}
		return $timekeeper;
	}
	
	/**
	 * Loeschfunktion fuer Zeitmessungen
	 * Achtung: Hier wird wirklich geloescht und nicht nur ein Status = 0 gesetzt
	 */
	public function delete(){
		global $DB;
			$sql = "DELETE FROM timekeeper WHERE id = {$this->id}";
		return $DB->no_result($sql);
	}
	
	/**
	 * Berechnet und liefert die Zeit dieser Messung in Stunden
	 * @return String
	 */
	public function printDurationInHour(){
		global $_LANG;
		$ret= "";
		$hour = floor(($this->enddate - $this->startdate) / (60 * 60));
		$minutes = (int) ceil((($this->enddate - $this->startdate) % (60 * 60)) / 60);
		if($hour > 0){
			$ret .= $hour." ".$_LANG->get('Std').". ";
		}
		if($minutes > 0){
			$ret .= $minutes." ".$_LANG->get('Min').".";
		}
		return $ret;
	}
	
	/**
	 * Berechnet und liefert die Zeit dieser Messung in Minuten ohne volle Stunden 
	 * (liefert die Rest-Minuten ohne volle Stunden)
	 * @return number
	 */
	public function getDurationMinutes(){
		$minutes = (int) ceil((($this->enddate - $this->startdate) % (60 * 60)) / 60);
		return $minutes;
	}
	
	/**
	 * Berechnet und liefert die gesamte Zeit dieser Messung in Stunden (nur ganze Stunden)
	 * @return number
	 */
	public function getDurationHour(){
		$hour = floor(($this->enddate - $this->startdate) / (60 * 60));
		return $hour;
	}
	
	/**
	 * Berechnet und liefert die gesamte Zeit dieser Messung in Sekunden
	 * @return number
	 */
	public function getDurationInSecond(){
		$duration = ($this->enddate - $this->startdate);
		return $duration;
	}
	
	/**
	 * Berechnet und liefert die gesamte Zeit dieser Messung in Minuten
	 * @return number
	 */
	public function getDurationInMinutes(){
		$duration = ($this->enddate - $this->startdate) / 60;
		return $duration;
	}
	
	/**
	 * Berechnet und liefert die gesamte Zeit dieser Messung in Stunden
	 * @return number
	 */
	public function getDurationInHour(){
		$duration = ($this->enddate - $this->startdate) / (60*60);
		return $duration;
	}
	
	/**
	 * Liefert den Benutzer zu dieser Zeitmessung
	 * @return User
	 */
	public function getUser(){
		$tmp_user= new User($this->userID);
		return $tmp_user;
	}
	
	/**
	 * Liefert den Namen zum aktiven Modul
	 * @return string
	 */
	public function getModuleName(){
		switch ($this->module){
			case self::MODULE_TICKET: $ret = "Ticket"; break;
			case self::MODULE_CALCULATION: $ret = "Kalkulation"; break;
			case self::MODULE_COLLECTIVE: $ret = "Sammelrechnung"; break;
			case self::MODULE_PLANNING: $ret = "Planung"; break;
		}
		return $ret;
	}
	
	/**
	 * Liefert den Namen zum aktiven Modul
	 * @return string
	 */
	public function getObjectName(){
		switch ($this->module){
			case self::MODULE_TICKET :
				$obj = new Ticket($this->objectID);
				$ret = $obj->getTitle();
				break;
			case self::MODULE_CALCULATION:
				$obj = new Order($this->objectID); 
				$ret = $obj->getTitle();
				break;
			case self::MODULE_COLLECTIVE: 
				$obj = new CollectiveInvoice($this->objectID);
				$ret = $obj->getTitle();
				break;
		}
		return $ret;
	}
	
	/**
	 * Rechnet die angegebene Zeitspanne (Sek) in Stunden und Minuten um und gibt diesen String aus
	 * @param int $duration: unzurechnende Sekunden
	 * @return String
	 */
	static function printSecondsInHour($duration){
		global $_LANG;
		$ret= "";
		$hour = floor(($duration) / (60 * 60));
		$minutes = (int) round((($duration) % (60 * 60)) / 60);
		if($hour > 0){
			$ret .= $hour." ".$_LANG->get('Std').". ";
		}
		$ret .= $minutes." ".$_LANG->get('Min').".";
		return $ret;
	}
	
	/**
	 * Rechnet die Stunden und Minuten um und gibt einen String aus
	 * @param int $minutes
	 * @param int $hours
	 * @return String
	 */
	static function printCalculateDuration($minutes, $hours){
		global $_LANG;
		$ret ="";
		// ganze stunden aus den Minuten herausziehen
		$hour_from_min = floor($minutes / 60 );
		
		// und zu den Stunden hinzu addieren
		$hours += $hour_from_min; 
		
		// Restminuten ausrechnen ohne ganze Stunden
		$minutes = (int) $minutes % 60;
		
		if($hours > 0){
			$ret .= $hours." ".$_LANG->get('Std').". ";
		}
		$ret .= $minutes." ".$_LANG->get('Min').".";
		return $ret;
	}

    public static function getOrderTimes($orderId) {
        global $DB;
        $q = "SELECT tk.object_id, t.tkt_title,
                  GROUP_CONCAT(tk.`comment` SEPARATOR '|') AS projectComments,
                  tk.startdate, tk.enddate, SUM(enddate - startdate) AS seconds ,
                  COUNT(t.id) AS ticketcount
                FROM timekeeper tk
                LEFT JOIN tickets t ON tk.object_id = t.id
                WHERE t.tkt_order_id = {$orderId}
                GROUP BY tk.object_id";
        $res = $DB->select($q);

        return $res;
    }
	
	public function getId()
	{
	    return $this->id;
	}

	public function getUserID()
	{
	    return $this->userID;
	}

	public function setUserID($userID)
	{
	    $this->userID = $userID;
	}

	public function getStatus()
	{
	    return $this->status;
	}

	public function setStatus($status)
	{
	    $this->status = $status;
	}

	public function getStartdate()
	{
	    return $this->startdate;
	}

	public function setStartdate($startdate)
	{
	    $this->startdate = $startdate;
	}

	public function getEnddate()
	{
	    return $this->enddate;
	}

	public function setEnddate($enddate)
	{
	    $this->enddate = $enddate;
	}

	public function getObjectID()
	{
	    return $this->objectID;
	}

	public function setObjectID($objectID)
	{
	    $this->objectID = $objectID;
	}

	public function getModule()
	{
	    return $this->module;
	}

	public function setModule($module)
	{
	    $this->module = $module;
	}

	public function getComment()
	{
	    return $this->comment;
	}

	public function setComment($comment)
	{
	    $this->comment = $comment;
	}

    public function getSubObjectID()
    {
        return $this->subObjectID;
    }

    public function setSubObjectID($subObjectID)
    {
        $this->subObjectID = $subObjectID;
    }

	public function getArticleId()
	{
	    return $this->articleId;
	}

	public function setArticleId($articleId)
	{
	    $this->articleId = $articleId;
	}
	
	/**
	 * Liefert des verknuepften Artikel
	 * @return Article
	 */
	public function getArticle(){
		$tmp_article = new Article($this->articleId);
		return $tmp_article;
	}

	public function getArticleAmount()
	{
	    return $this->articleAmount;
	}

	public function setArticleAmount($articleAmount)
	{
	    $this->articleAmount = $articleAmount;
	}
}
?>