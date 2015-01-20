<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			29.01.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once './libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/warehouse/warehouse.reservation.class.php';

class Warehouse{
	const ORDER_NAME 		= "wa.wh_name";			    // Lagerplatz-Name
	const ORDER_ORDERNUMBER	= "wa.wh_ordernumber";		// Auftragsnummer
	const ORDER_RECALL		= "wa.wh_recall";			// Lagerabruf
	const ORDER_CUSTOMER	= "bus.wh_customer";		// Kunde/Lieferant

	private $id=0;
	private $name;			   // Name des Lagerplatzes (Bezeichnung)
	private $customer;		   // Kunde
	private $input;			   // Artikel/Material/Dinge , die da stehen
	private $amount;		   // Menge die dort steht
	private $amount_reserved = 0;  // Reservierte Menge
	private $recall = 0;	   // Lagerabruf: Datum, bis wann eingelagert wird (bzw. bis wann bezahlt ist)
	private $ordernumber;	   // Auftragsnummer
	private $status = 1;	   // Status
	private $comment;	       // Freier Kommentar
	private $minimum;		   // Warnmenge (Mindestbestand)
	private $contactperson;    // Ansprechpartner, wenn Warnmenge (Mindestbestand) unterschritten wird
	private $article;		   // Verknuepfter Artikel

	function __construct($id = 0){
		global $DB;
        global $_USER;
        
        $this->customer 	= new BusinessContact(0);
        $this->contactperson= new User(0);
        $this->article 	= new Article();
        
		if ($this->crtdate == 0){
			$this->crtdate= time();
		}
		
		if ($id > 0){
			$sql = "SELECT * FROM warehouse WHERE id = {$id}";
			$r = $DB->select($sql);
			if (is_array($r)){
				$this->id 			= $r[0]["id"];
				$this->name 		= $r[0]["wh_name"];
				$this->customer 	= new BusinessContact($r[0]["wh_customer"]);
				$this->input 		= $r[0]["wh_input"];
				$this->amount 		= $r[0]["wh_amount"];
				$this->amount_reserved 		= $r[0]["wh_amount_reserved"];
				$this->recall 		= $r[0]["wh_recall"];
				$this->ordernumber	= $r[0]["wh_ordernumber"];
				$this->status 		= $r[0]["wh_status"];
				$this->comment 		= $r[0]["wh_comment"];
				$this->minimum 		= $r[0]["wh_minimum"];
				$this->contactperson= new User($r[0]["wh_contactperson"]);
				$this->article		= new Article($r[0]["wh_articleid"]);
			}
		}
	}

	/**
	 * Abrufen aller Lagerplaetze (Suchparameter mit UND verknuepft)
	 * 
	 * @param String $order : Reihenfolge der Rueckgabe
	 * @param Array $search :  Array mit such
	 * @return multitype:Warehouse
	 */
	static function getAllStocks($order = self::ORDER_NAME, $search){
		global $DB;
        global $_USER;
		$retval = Array();

		$sql = "SELECT wa.id FROM warehouse as wa WHERE wa.wh_status > 0 ";

		//SucheFelder beachten, falls angegeben
		if($search["name"] != "" && $search["name"] != NULL){
			$sql .= " AND wa.wh_name LIKE '%{$search["name"]}%' ";
		}
		if($search["cust"] != 0 ){
			$sql .= " AND wa.wh_customer = {$search["cust"]} ";
		}
		if($search["input"] != "" && $search["input"] != NULL){
			$sql .= " AND wa.wh_input LIKE '%{$search["input"]}%' ";
		}
		if($search["ordernumber"] != "" && $search["ordernumber"] != NULL){
			$sql .= " AND wa.wh_ordernumber LIKE '%{$search["ordernumber"]}%' ";
		}
		/*gln 27.01.2014, zusaetzliche Auswahl Artikelnr.*/ 
		if($search["artid"] != 0 ){
			$sql .= " AND wa.wh_articleid = {$search["artid"]} ";
		}
		
		// Sortierung beachten
		$sql .= " ORDER BY {$order} ";
		
		//error_log($sql);
		$res = $DB->select($sql);
		
		if (is_array($res)){
			foreach ($res as $wh){
				$retval[] = new Warehouse($wh["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Abrufen aller Lagerplaetze (Suchfelder mit ODER verknuepft)
	 * 
	 * @param unknown_type $order
	 * @param unknown_type $search
	 * @return multitype:Warehouse
	 */
	static function getAllStocksForHome($order = self::ORDER_NAME, $search){
		global $DB;
		global $_USER;
		$retval = Array();
	
		$sql = "SELECT wa.id FROM warehouse as wa
				WHERE wh_status > 0   
				AND (
					wa.wh_name LIKE '%{$search}%' 
					OR wa.wh_input LIKE '%{$search}%' 
					OR wa.wh_ordernumber LIKE '%{$search}%' 
				) ORDER BY {$order} ";
		//error_log("SQL: ".$sql);
		$res = $DB->select($sql);
	
		if (is_array($res)){
			foreach ($res as $wh){
				$retval[] = new Warehouse($wh["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Abrufen aller Lagerplaetze mit Lagerabruf am aktuellen Tag
	 *
	 * @param STRING $order
	 * @return multitype:Warehouse
	 */
	static function getAllStocksforToday($order = self::ORDER_NAME){
		global $DB;
		global $_USER;
		$retval = Array();
		$time1 = mktime(0,0,0);	// Zeit1: morgens 00:00:01
		$time2 = mktime(23,23,59);	// Zeit2: abends  23:23:59
	
		$sql = "SELECT wa.id FROM warehouse as wa
				WHERE 
				wa.wh_status > 0 
				AND wa.wh_recall > {$time1}
				AND wa.wh_recall < {$time2}";
	
		// Sortierung beachten
		$sql .= " ORDER BY {$order} ";
		//error_log($sql);
		$res = $DB->select($sql);
	
		if (is_array($res)){
			foreach ($res as $wh){
				$retval[] = new Warehouse($wh["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Abrufen aller Lagerplaetze mit Lagermenge <= Mindestmenge
	 *
	 * @param STRING $order
	 * @return multitype:Warehouse
	 */
	static function getAllStocksWithLowAmount($order = self::ORDER_NAME){
		global $DB;
		global $_USER;
		$retval = Array();
	
		$sql = "SELECT wa.id FROM warehouse as wa
				WHERE
				wa.wh_status > 0
				AND wa.wh_amount <= wa.wh_minimum
				AND wa.wh_minimum > 0"; 
	
		// Sortierung beachten
		$sql .= " ORDER BY {$order} ";
		//error_log($sql);
		$res = $DB->select($sql);
	
		if (is_array($res)){
			foreach ($res as $wh){
				$retval[] = new Warehouse($wh["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Speicherfunktion fuer Lagerplaetze
	 * @return boolean
	 */
	function save(){
		global $DB;
        global $_USER;
		
		if($this->id > 0){
			$sql = "UPDATE warehouse
					SET
					wh_name 		= '{$this->name}',
					wh_customer		= '{$this->getCustomer()->getId()}',
					wh_input 		= '{$this->input}',
					wh_ordernumber	= '{$this->ordernumber}',
					wh_amount 		= {$this->amount}, 
					wh_amount_reserved = {$this->amount_reserved}, 
					wh_recall		= {$this->recall},  
					wh_status		= {$this->status}, 
					wh_comment		= '{$this->comment}',
					wh_minimum		= {$this->minimum}, 
					wh_contactperson= {$this->getContactperson()->getId()},
					wh_articleid	= {$this->article->getId()}
					WHERE
					id = {$this->id}";
			$res = $DB->no_result($sql);
		}else{
			$sql = "INSERT INTO warehouse
					( wh_name, wh_customer, wh_input, wh_ordernumber,
					  wh_amount, wh_recall, wh_status, wh_comment,
					  wh_minimum, wh_contactperson, wh_articleid )
					VALUES
					( '{$this->name}', '{$this->getCustomer()->getId()}', '{$this->input}', '{$this->ordernumber}',
					  '{$this->amount}', {$this->recall}, {$this->status}, '{$this->comment}',
					  {$this->minimum}, {$this->getContactperson()->getId()}, {$this->article->getId()} )";
			$res = $DB->no_result($sql);
			if($res)
			{
				$sql = "SELECT MAX(id) 'thisid'
						FROM warehouse
						WHERE
						wh_name = {$this->name}";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["thisid"];
			}
		}
		return $res;
	}

	/**
	 * Funktion zum entfernen eines Lagerplatzes
	 * @return boolean
	 */
	function delete(){
		global $DB;
        global $_USER;
		$sql = "UPDATE warehouse SET wh_status = 0 WHERE id = {$this->id}";
		$res = $DB->no_result($sql);
		return $res;
	}
	
	/**
	 * Funktion zur Validierung, ob ein Benutzer die Stellplaetze bearbeiten darf
	 * @return boolean
	 */
	static function hasGroupRight($userId){
		
		// TODO  definitiv ueberarbeiten
		global $DB;
        global $_USER;
		$ret = true;  // normalerweise immer false !
		
		/*
		$sql = "SELECT user_id, group_id FROM user_group WHERE user_id = {$userId} ";
		$res = $DB->select($sql);
		
		foreach ($res as $r){
			if($r[group_id] == 15){
				return true;
			}
		}*/
		return $ret;
	}
	
	/**
	 * ... liefert alle Lagerplaetze mit denen der Artikel verbunden ist
	 * 
	 * @param Int $articleid
	 * @param String $order
	 * @return multitype:Warehouse
	 */
	static function getAllStocksByArticle($articleid, $order = self::ORDER_NAME){
		global $DB;
		
		$retval = Array();
		
		$sql = "SELECT wa.id FROM warehouse as wa
				WHERE
				wa.wh_status > 0
				AND wa.wh_articleid = {$articleid}  
				ORDER BY {$order} ";
		//error_log($sql);
		$res = $DB->select($sql);
		if (is_array($res)){
			foreach ($res as $wh){
				$retval[] = new Warehouse($wh["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * ... Reserviert Artikel bzw. hebt Reservierung eines Artikels auf
	 *
	 * @param Int $articleid
	 * @param Int $amount
	 * @return boolean
	 */
	public static function addRemoveReservation($articleid, $amount)
	{
	    if (self::getTotalStockByArticle($articleid) < $amount){
	        return false;
	    } else {
	        $whs = self::getAllStocksByArticle($articleid);
	        if ($amount > 0){
	            foreach ($whs as $wh){
	                if (($wh->getAmount() - $wh->getAmount_reserved()) > $amount){
	                    $wh->setAmount_reserved($wh->getAmount_reserved()+$amount);
	                } else {
	                    $tmp_amount = $wh->getAmount() - $wh->getAmount_reserved();
	                    $wh->setAmount_reserved($wh->getAmount_reserved()+$tmp_amount);
	                    $amount += $tmp_amount;
	                }
	                $wh->save();
	                if ($amount == 0){
	                    return true;
	                    break;
	                }
	            }
	        } else {
	            $amount = abs($amount);
	            foreach ($whs as $wh){
	                if (($wh->getAmount_reserved()) > $amount){
	                    $wh->setAmount_reserved($wh->getAmount_reserved()-$amount);
	                } else {
	                    $tmp_amount = $wh->getAmount_reserved();
	                    $wh->setAmount_reserved(0);
	                    $amount += $tmp_amount;
	                }
	                $wh->save();
	                if ($amount == 0){
	                    return true;
	                    break;
	                }
	            }
	        }
	    }
	}
	
	/**
	 * ... liefert verfügbare NICHT reservierte Menge auf Lager eines Artikels
	 *
	 * @param Int $articleid
	 * @param String $order
	 * @return multitype:Warehouse
	 */
	static function getTotalStockByArticle($articleid, $order = self::ORDER_NAME){
	    global $DB;
	    
	    $stock = 0;
	
	    $sql = "SELECT wa.id FROM warehouse as wa
	    WHERE
	    wa.wh_status > 0
	    AND wa.wh_articleid = {$articleid}
	    ORDER BY {$order} ";
	    //error_log($sql);
	    $res = $DB->select($sql);
	    if (is_array($res)){
	        foreach ($res as $wh){
	            $tmp_wh = new Warehouse($wh["id"]);
	            $tmp_count = $tmp_wh->getAmount() - Reservation::getTotalReservationByWarehouse($tmp_wh->getId());
	            $stock += $tmp_count;
	        }
	    }
	    return $stock;
	}

	public function getId()
	{
	    return $this->id;
	}
	
	public function getTitle(){
		return $this->name;
	}
	
	public function getName()
	{
	    return $this->name;
	}

	public function setName($name)
	{
	    $this->name = $name;
	}

	public function getComment()
	{
	    return $this->comment;
	}

	public function setComment($comment)
	{
	    $this->comment = $comment;
	}

	public function getCustomer()
	{
	    return $this->customer;
	}

	public function setCustomer($customer)
	{
	    $this->customer = $customer;
	}

	public function getInput()
	{
	    return $this->input;
	}

	public function setInput($input)
	{
	    $this->input = $input;
	}
	public function getOrdernumber()
	{
	    return $this->ordernumber;
	}

	public function setOrdernumber($ordernumber)
	{
	    $this->ordernumber = $ordernumber;
	}

	public function getAmount()
	{
	    return $this->amount;
	}

	public function setAmount($amount)
	{
	    $this->amount = $amount;
	}

	public function getRecall()
	{
	    return $this->recall;
	}

	public function setRecall($recall)
	{
	    $this->recall = $recall;
	}

	public function getStatus()
	{
	    return $this->status;
	}

	public function setStatus($status)
	{
	    $this->status = $status;
	}

	public function getMinimum()
	{
	    return $this->minimum;
	}

	public function setMinimum($minimum)
	{
	    $this->minimum = $minimum;
	}

	public function getContactperson()
	{
	    return $this->contactperson;
	}

	public function setContactperson($contactperson)
	{
	    $this->contactperson = $contactperson;
	}

	public function getArticle()
	{
	    return $this->article;
	}

	public function setArticle($article)
	{
	    $this->article = $article;
	}
	/**
     * @return the $amount_reserved
     */
    public function getAmount_reserved()
    {
        return $this->amount_reserved;
    }

	/**
     * @param field_type $amount_reserved
     */
    public function setAmount_reserved($amount_reserved)
    {
        $this->amount_reserved = $amount_reserved;
    }

	
	
}
?>