<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			07.08.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
//
// Datei zur Behandlung von Eingaben von Personalisierungen von Kunden.
//
// ----------------------------------------------------------------------------------

class Personalizationorderitem {
	
	const SITE_FRONT 	= 0;
	const SITE_BACK		= 1;
	const SITE_ALL		= 2;

	private $id = 0;			// ID
	private $persoID;			// ID der zugehoerigen Personalisierung
	private $persoItemID;		// ID der zugehoerigen Personalisierungseintrags
	private $persoorderID;		// ID der zugehoerigen Personalisierungs-Bestellung
	private $value;				// Inhalt den der Kunde eingetragen hat
		
	function __construct($id = 0){
		global $DB;
		global $_USER;
	
		$this->crtuser = new User();
	
		if ($id > 0){
			$sql = "SELECT * FROM personalization_orderitems WHERE id = {$id}";
			if($DB->num_rows($sql)){
				$r = $DB->select($sql);
				$r = $r[0];
				$this->id = $r["id"];
				$this->persoID = $r["persoid"];
				$this->persoorderID = $r["persoorderid"];
				$this->persoItemID = $r["persoitemid"];
				$this->value = $r["value"];
			}
		}
	}
	
	/**
	 * Speicher-Funktion fuer ein Textfeld einer Personalisierung
	 *
	 * @return boolean
	 */
	function save(){
		global $DB;
		global $_USER;
		$now = time();

		if($this->id > 0){
			$sql = "UPDATE personalization_orderitems SET
					value = '{$this->value}' 
					WHERE id = {$this->id}";
			return $DB->no_result($sql);
		} else {
			$sql = "INSERT INTO personalization_orderitems
					(value, persoitemid, persoid, persoorderid )
					VALUES
					('{$this->value}', {$this->persoItemID}, {$this->persoID}, {$this->persoorderID} )";
			$res = $DB->no_result($sql);

			if($res){
				$sql = "SELECT max(id) id FROM personalization_orderitems WHERE persoid = {$this->persoID} AND persoorderid = {$this->persoorderID} ";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["id"];
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	* Loeschfunktion fuer Bestellungen von Personalisierungen
	*
	* @return boolean
	*/
	public function delete(){
		global $DB;
		if($this->id > 0){
			$sql = "DELETE FROM personalization_orderitems WHERE id = {$this->id}";
			if($DB->no_result($sql)){
				unset($this);
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Funktion liefert alle aktiven Eingabefelder einer Personalisierugs-Bestellung nach y-Pos sortiert
	 *
	 * @param STRING $order Reihenfolge, in der die Eingbelfelder sortiert werden
	 * @return Array : Personalizationitem
	 */
	static function getAllPersonalizationorderitems($perso_order_id, $site = self::SITE_FRONT, $orderby = 't2.ypos'){
		global $DB;
		$retval = Array();
		$sql = "SELECT t1.id, t2.ypos, t2.reverse 
				FROM personalization_orderitems t1, personalization_items t2 
				WHERE 
				t1.persoorderid = {$perso_order_id} AND  
				t1.persoitemid = t2.id ";
		if ($site == 0 || $site == 1){
			$sql .= " AND t2.reverse = {$site} ";
		}
		$sql.= "ORDER BY " . $orderby;
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Personalizationorderitem($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * <p>Liefert die Y-Position eines Elemts unter Beruecksichtigung der Abhaengigkeiten des 
	 * zugehoerigen Elemnst der PErsonalisierung</p>
	 * @return unknown
	 */
	public function calculateYPos(){
		$perso_item = new Personalizationitem($this->getPersoItemID());
		$retval = $perso_item->getYpos();
		
		if ($perso_item->getDependencyID() > 0){
			$depend_orderitem = self::getOrderitem_By_Orderid_PersoItemID($this->persoorderID, $perso_item->getDependencyID());
			if($depend_orderitem->getValue() == ""){
				return $depend_orderitem->calculateYPos();
			}
		}
		return $retval;
	}
	
	/**
	 * <p>Liefert ein Element einer Personalisierungs-Bestellung in Abhaengigkeit von der BestellungsID
	 * und der ID des Eintrags der zugehoerigen Personalisierung</p>
	 * 
	 * @param Int $order_id
	 * @param Int $persoitem
	 * @return boolean | Personalizationorderitem
	 */
	public function getOrderitem_By_Orderid_PersoItemID($order_id=0, $persoitem=0){
		global $DB;
		$retval = false;
		$sql = "SELECT id FROM personalization_orderitems
				WHERE
				persoorderid = {$order_id} AND 
				persoitemid = {$persoitem} ";
		// error_log("SQL: ".$sql);
		if($DB->num_rows($sql)){
			$ret = $DB->select($sql);
			$ret = $ret[0];
			$retval = new Personalizationorderitem($ret["id"]);
		}
		return $retval;
	}
	

	/****************************************************
	 * 				GETTER und SETTER					*
	 ***************************************************/
	public function getId()
	{
	    return $this->id;
	}

	public function getPersoID()
	{
	    return $this->persoID;
	}

	public function setPersoID($persoID)
	{
	    $this->persoID = $persoID;
	}

	public function getPersoItemID()
	{
	    return $this->persoItemID;
	}

	public function setPersoItemID($persoItemID)
	{
	    $this->persoItemID = $persoItemID;
	}

	public function getPersoorderID()
	{
	    return $this->persoorderID;
	}

	public function setPersoorderID($persoorderID)
	{
	    $this->persoorderID = $persoorderID;
	}

	public function getValue()
	{
	    return $this->value;
	}

	public function setValue($value)
	{
	    $this->value = $value;
	}
	
	/** Funktion liefert den Inhalt des Textfeldes */
	public function getTitle()
	{
		return $this->value;
	}
	
	/**
	 * Liefert den Rueckseite-Wert des zugehoerigen Items 
	 * @return number
	 */
	public function getReverse(){
		$p_item = new Personalizationitem($this->persoItemID);
		return $p_item->getReverse();
	}
}

?>