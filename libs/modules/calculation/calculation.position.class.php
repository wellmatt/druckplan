<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			22.01.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
//
//	Klasse fuer die Behandlung von zus. Position in Kalkulationen
//
// ----------------------------------------------------------------------------------
class CalculationPosition{

	const TYPE_MANUELL			= 1;
	const TYPE_ARTICLE 			= 2;
	
	const SCALE_PER_KALKULATION = 1;
	const SCALE_PER_PIECE		= 2;

	private $id = 0;					// ID
	private $status = 0;				// Status z.B.: 0 = geloescht, 1 = aktiv
	private $quantity = 0;				// Menge
	private $price = 0;					// (Verkaufs-) Preis
	private $tax = 19; 					// MWST
	private $comment = ""; 				// Beschreibung
	private $calculationid = 0;			// ID der Kalkulation, in der diese Position steckt
	private $type = 1;					// Type -> siehe Konstanten
	private $objectid = 0;				// Id des zugehoerigen Objekts (z.B. des Artikels)
	private $invrel = 1;				// Relevanz fuer die Dokumente (mehr als nur fuer Rechnungen)
	private $scale = 0;					// Staffelung z.B: pro Kalkulation oder pro Stueck (-> siehe Konstanten)
	private $showPrice = 0;				// Preis auf Dokumenten ausgeben
	private $showQuantity = 0;			// Menge auf Dokumente ausgeben
	private $cost = 0;					// EK-Preis

	/**
	 * Konstruktor fuer (Positions-) Eintraege in Sammelrechungen
	 * @param int $id
	 */
	public function __construct($id=0){
		global $DB;
		if($id>0){
			$sql = "SELECT * FROM orders_calculationpositions WHERE id = ".$id;
			if($DB->num_rows($sql)){
				$rows = $DB->select($sql);
				$r = $rows[0];
				$this->id = $r["id"];
				$this->status =$r["status"];
				$this->quantity = $r["quantity"];
				$this->price = $r["price"];
				$this->tax = $r["tax"];
				$this->comment = $r["comment"];
				$this->calculationid = $r["calculation_id"];
				$this->type = $r["type"];
				$this->objectid = $r["object_id"];
				$this->invrel = $r["inv_rel"];
				$this->scale = $r["scale"];
				$this->showPrice = $r["show_price"];
				$this->showQuantity = $r["show_quantity"];
				$this->cost = $r["cost"];
			}
		}
	}

	/**
	 * ...liefert alle Eintraege/Positionen einer Kalkulation
	 *
	 * @param int $calc_id : Id einer Kallkulation
	 * @return Array : CalculationPosition
	 */
	static function getAllCalculationPositions($calc_id, $for_docs = false){
		global $DB;
		$sql = " SELECT id FROM orders_calculationpositions WHERE calculation_id = {$calc_id} AND status > 0 ";
		
		if ($for_docs){
			$sql .= " AND inv_rel = 1 ";
		}
		
		$orderpos = Array();
		if($DB->no_result($sql)){
			$result = $DB->select($sql);
			foreach($result as $r){
				$orderpos[] = new CalculationPosition($r["id"]);
			}
		}
		return $orderpos;
	}

	/**
	 * Speicher-Funktion fuer ein Array von Positionen einer Kalkulation
	 *
	 * @param Array $calc_positions
	 * @return boolean
	 */
	static function saveMultipleCalculationpositions($calc_positions){
		$result=FALSE;
		global $DB;
		foreach ($calc_positions as $cpos){
			if ($cpos->id == 0){
				// Neuer Eintrag in DB
				$sql = "INSERT INTO orders_calculationpositions
						(quantity, comment, price,
						tax, status, calculation_id, type,
						object_id, inv_rel, scale, 
						show_price, show_quantity, cost )
						VALUES
						({$cpos->getQuantity()}, '{$cpos->getComment()}', {$cpos->getPrice()},
						{$cpos->getTax()}, 1, {$cpos->getCalculationID()}, {$cpos->getType()},
						{$cpos->getObjectid()}, {$cpos->getInvrel()}, {$cpos->getScale()}, 
						{$cpos->getShowPrice()}, {$cpos->getShowQuantity()}, {$cpos->getCost()} )";
				$res = $DB->no_result($sql);
				if($res){
					$sql = " SELECT max(id) id FROM orders_calculationpositions";
					$thisid = $DB->select($sql);
					$cpos->id = $thisid[0]["id"];
					$result = true;
				} else {
					return false;
				}
			} else {
				//update
				$sql = "UPDATE orders_calculationpositions
						SET
						quantity = {$cpos->getQuantity()},
						comment = '{$cpos->getComment()}',
						price = {$cpos->getPrice()},
						tax = {$cpos->getTax()},
						type = {$cpos->getType()},
						object_id = {$cpos->getObjectid()},
						inv_rel = {$cpos->getInvrel()},
						calculation_id = {$cpos->getCalculationID()}, 
						scale = {$cpos->getScale()}, 
						show_price = {$cpos->getShowPrice()}, 
						show_quantity = {$cpos->getShowQuantity()}, 
						cost = {$cpos->getCost()} 
						WHERE id = {$cpos->getId()}";
				$res = $DB->no_result($sql);
				if($res){
					$result = true;
				} else {
					return false;
				}
			}
		}
		return $result;
	}

	/**
	 * Loeschfunktion setzt den Status auf 0, loescht also nicht wirklich
	 * @return boolean
	 */
	public function delete(){
		global $DB;
		$sql = "UPDATE orders_calculationpositions SET status = 0 WHERE id = {$this->id}";
		$res = $DB->no_result($sql);
		if($res){
			unset($this);
			return true;
		} else {
			return false;
		}
	}

	public function getId(){
		return $this->id;
	}

	public function getStatus(){
		return $this->status;
	}

	public function setStatus($status){
		$this->status = $status;
	}

	public function getQuantity(){
		return $this->quantity;
	}

	public function setQuantity($quantity){
		$this->quantity = $quantity;
	}
	
	/**
	 * Berechnet den Gesamtpreis der Position (ohne MWST) 
	 * @return number
	 */
	public function getCalculatedPrice(){
		$ret_price = 0;
		$tmp_calc = new Calculation($this->calculationid);
		
		if($this->scale == self::SCALE_PER_KALKULATION){
			// Presi pro Kalkulation
			$ret_price = $this->price * $this->quantity;
		} else {
			// Preis pro Stueck
			$ret_price = $this->price * $this->quantity * $tmp_calc->getAmount();
		}
		return $ret_price;
	}
	
	/**
	 * Berechnet den Gesamt-EK-Preis der Position (ohne MWST)
	 * @return number
	 */
	public function getCalculatedCosts(){
		$ret_price = 0;
		$tmp_calc = new Calculation($this->calculationid);
	
		if($this->scale == self::SCALE_PER_KALKULATION){
			// Presi pro Kalkulation
			$ret_price = $this->cost * $this->quantity;
		} else {
			// Preis pro Stueck
			$ret_price = $this->cost * $this->quantity * $tmp_calc->getAmount();
		}
		return $ret_price;
	}

	/**
	 * Liefert den eingegebenen Brutto-Preis (Stueckpreis)
	 * 
	 * @return number
	 */
	public function getPrice(){
		return $this->price;
	}

	public function setPrice($price){
		$this->price = $price;
	}

	public function getTax(){
		return $this->tax;
	}

	public function setTax($tax){
		$this->tax = $tax;
	}

	public function getComment(){
		return $this->comment;
	}

	public function setComment($comment){
		$this->comment = $comment;
	}

	public function getType()
	{
		return $this->type;
	}

	public function setType($type)
	{
		$this->type = $type;
	}

	public function getObjectid()
	{
		return $this->objectid;
	}

	public function setObjectid($objectid)
	{
		$this->objectid = $objectid;
	}

	public function getInvrel()
	{
		return $this->invrel;
	}

	public function setInvrel($invrel)
	{
		$this->invrel = $invrel;
	}

	public function getCalculationid()
	{
	    return $this->calculationid;
	}

	public function setCalculationid($calculationid)
	{
	    $this->calculationid = $calculationid;
	}

	public function getScale()
	{
	    return $this->scale;
	}

	public function setScale($scale)
	{
	    $this->scale = $scale;
	}

	public function getShowPrice()
	{
	    return $this->showPrice;
	}

	public function setShowPrice($showPrice)
	{
	    $this->showPrice = $showPrice;
	}

	public function getShowQuantity()
	{
	    return $this->showQuantity;
	}

	public function setShowQuantity($showQuantity)
	{
	    $this->showQuantity = $showQuantity;
	}

	public function getCost()
	{
	    return $this->cost;
	}

	public function setCost($cost)
	{
	    $this->cost = $cost;
	}
	
	public function clearId()
	{
	    $this->id = 0;
	}
}