<?//--------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       17.09.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

/**
 * Klasse fuer Auftrags Positionen in Sammelrechnungen
 */
class Orderposition{
	
	const TYPE_MANUELL			= 0;
	const TYPE_ORDER			= 1;
	const TYPE_ARTICLE 			= 2;
	const TYPE_PERSONALIZATION 	= 3;
	private $id = 0;
	private $status = 0;				// Status z.B.: 0 = geloescht, 1 = aktiv 
	private $quantity = 0;				// Menge/Stueckzahl
	private $price = 0;					// Einzelpreis
	private $tax = 19; 					// MWST
	private $comment = ""; 				// Beschreibung
	private $collectiveinvoice = 0;		// ID der Sammelrechnung
	private $type;						// Typ (Artikel/Kalkulation/Manuell)
	private $objectid;					// ObjectID, fall mit artikel oder Auftrag verknuepft
	private $invrel = 1;				// Rechnungs-Relevanz
	private $revrel = 0;				// Gutschein-Relevanz
	
	/**
	 * Konstruktor fuer Eintraege (Auftragspositionen) in Sammelrechungen
	 * @param int $id
	 */
	public function __construct($id=0){
		global $DB;
		if($id>0){
			$sql = "SELECT * FROM collectiveinvoice_orderposition WHERE id = ".$id;
			if($DB->num_rows($sql)){
				$rows = $DB->select($sql);
				$r = $rows[0];
				$this->id = $r["id"];
				$this->status =$r["status"];
				$this->quantity = $r["quantity"];
				$this->price = $r["price"];
				$this->tax = $r["tax"];
				$this->comment = $r["comment"];
				$this->collectiveinvoice = $r["collectiveinvoice"];
				$this->type = $r["type"];
				$this->objectid = $r["object_id"];
				$this->invrel = $r["inv_rel"];
				$this->revrel = $r["rev_rel"];
			}
		}
	}
	
	/**
	 * ...liefert alle Eintraege/Auftragspositionen einer Sammelrechnung
	 * 
	 * @param int $collectiveId : Id einer Sammelrechnung
	 * @return Array : Orderposition
	 */
	static function getAllOrderposition($collectiveId){
		global $DB;
		//aus demo1 auskommentiert
		//$sql = "SELECT * FROM collectiveinvoice_orderposition WHERE collectiveinvoice = {$collectiveId} AND status > 0";
		$sql = "SELECT id FROM collectiveinvoice_orderposition WHERE collectiveinvoice = {$collectiveId} AND status > 0";
		$orderpos = Array();
		if($DB->no_result($sql)){
			$result = $DB->select($sql);
			foreach($result as $r){
				$orderpos[] = new Orderposition($r["id"]);
			}
		}
		return $orderpos;
	}
	
	/**
	 * Speicher-Funktion fuer ein Array von Orderpositionen
	 * 
	 * @param Array $orderpositions
	 * @return boolean
	 */
	static function saveMultipleOrderpositions($orderpositions){
		$result=FALSE;
		global $DB;
		foreach ($orderpositions as $opos){
			//aus demo1 auskommentiert
			//if ($opos->getId() == 0){
			if ($opos->id == 0){
				//Neuer Eintrag in DB
				$sql = "INSERT INTO collectiveinvoice_orderposition
						(quantity, comment, price, 
						tax, status, collectiveinvoice, type, 
						object_id, inv_rel, rev_rel )
						VALUES
						({$opos->getQuantity()}, '{$opos->getComment()}', {$opos->getPrice()}, 
						{$opos->getTax()}, 1, {$opos->getCollectiveinvoice()}, {$opos->getType()},
						{$opos->getObjectid()}, {$opos->getInvrel()}, {$opos->getRevrel()} )";
// 				echo $sql . "</br>";
				$res = $DB->no_result($sql);
				if($res){
					$sql = " SELECT max(id) id FROM collectiveinvoice_orderposition";
					$thisid = $DB->select($sql);
					$opos->id = $thisid[0]["id"];
					$result = true;
				} else {
					return false;
				}
			} else {
				//update
				$sql = "UPDATE collectiveinvoice_orderposition
						SET
						quantity = {$opos->getQuantity()},
						comment = '{$opos->getComment()}',
						price = {$opos->getPrice()},
						tax = {$opos->getTax()},
						type = {$opos->getType()},
						object_id = {$opos->getObjectid()},
						inv_rel = {$opos->getInvrel()},
						rev_rel = {$opos->getRevrel()},  
						collectiveinvoice = {$opos->getCollectiveinvoice()}
						WHERE id = {$opos->getId()}";
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
	 * Loeschfunktion fuer Auftragspositionen
	 * 
	 * @return boolean
	 */
	public function delete(){
		global $DB;
		$sql = "UPDATE collectiveinvoice_orderposition SET status = 0 WHERE id = {$this->id}";
		$res = $DB->no_result($sql);
		if($res){
			unset($this);
			return true;
		} else {
			return false;
		}
	}
	
	/****************************************************************
	 * 						GETTER u. SETTER						*
	 ***************************************************************/
/**
	 * Liefert den Title des zugehoerigen Objekts
	 * @return string
	 */
	public function getTitle(){
		$retval = "N.A.";
		if ($this->type == self::TYPE_ARTICLE){
			$tmp_art = new Article($this->objectid);
			$retval = $tmp_art->getTitle() . " (".$tmp_art->getNumber().")"; 
		} 
		if ($this->type == self::TYPE_PERSONALIZATION){
			$tmp_perso = new Personalizationorder($this->objectid);
			$retval = $tmp_perso->getTitle();
		}
		return $retval;
	}
	
	public function getCommentForShop(){
		global $_LANG;
		$retval = "";
		if ($this->type == self::TYPE_ARTICLE){
			$tmp_art = new Article($this->objectid);
			$retval = $tmp_art->getNumber()." - ".$tmp_art->getTitle()." (".$this->quantity.$_LANG->get('Stk.').")";
		}
		if ($this->type == self::TYPE_PERSONALIZATION){
			$tmp_perso = new Personalizationorder($this->objectid);
			$retval = $tmp_perso->getTitle()." (".$tmp_perso->getAmount().$_LANG->get('Stk.').")";
		}
		return $retval;
	}
	
	public function getId(){
		return $this->id;
	}
	
	/**
     * @param number $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
	
	public function getCollectiveinvoice(){
		return $this->collectiveinvoice;
	}
	
	public function setCollectiveinvoice($collectiveinvoice){
		$this->collectiveinvoice = $collectiveinvoice;
	}
	
	public function getNetto()
	{
	    return $this->getPrice() * $this->getQuantity();
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

	public function getRevrel()
	{
	    return $this->revrel;
	}

	public function setRevrel($revrel)
	{
	    $this->revrel = $revrel;
	}
}