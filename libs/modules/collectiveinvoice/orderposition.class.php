<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */

require_once 'libs/modules/collectiveinvoice/contentpdf.class.php';
require_once 'libs/modules/taxkeys/taxkey.class.php';

/**
 * Klasse fuer Auftrags Positionen in Sammelrechnungen
 */
class Orderposition{
	
	const TYPE_MANUELL			= 0;
	const TYPE_ORDER			= 1;
	const TYPE_ARTICLE 			= 2;
	const TYPE_PERSONALIZATION 	= 3;
	private $id = 0;
	private $status = 1;				// Status z.B.: 0 = geloescht, 1 = aktiv, 2 = soft gelöscht
	private $quantity = 0;				// Menge/Stueckzahl
	private $price = 0.0;				// Einzelpreis
	private $cost = 0.0;				// Einkaufspreis
	private $profit = 0.0;				// Marge (Profit)
	private $taxkey = 0;				// Steuerschlüssel
	private $comment = ""; 				// Beschreibung
	private $collectiveinvoice = 0;		// ID der Sammelrechnung
	private $type;						// Typ (Artikel/Kalkulation/Manuell)
	private $objectid;					// ObjectID, fall mit artikel oder Auftrag verknuepft
	private $invrel = 1;				// Rechnungs-Relevanz
	private $revrel = 0;				// Gutschein-Relevanz
	private $file_attach = 0;           // Artikle File
	private $perso_order = 0;           // Falls Perso Order Bestellung
	private $sequence = 0;				// Sortierung Reihenfolge

	/**
	 * Konstruktor fuer Eintraege (Auftragspositionen) in Sammelrechungen
	 * @param int $id
	 */
	public function __construct($id=0){
		global $DB;

		$this->taxkey = new TaxKey(0);

		if($id>0){
			$valid_cache = true;
			if (Cachehandler::exists(Cachehandler::genKeyword($this,$id))){
				$cached = Cachehandler::fromCache(Cachehandler::genKeyword($this,$id));
				if (get_class($cached) == get_class($this)){
					$vars = array_keys(get_class_vars(get_class($this)));
					foreach ($vars as $var)
					{
						$method = "get".ucfirst($var);
						$method2 = $method;
						$method = str_replace("_", "", $method);
						if (method_exists($this,$method))
						{
							if(is_object($cached->{$method}()) === false) {
								$this->{$var} = $cached->{$method}();
							} else {
								$class = get_class($cached->{$method}());
								$this->{$var} = new $class($cached->{$method}()->getId());
							}
						} elseif (method_exists($this,$method2)){
							if(is_object($cached->{$method2}()) === false) {
								$this->{$var} = $cached->{$method2}();
							} else {
								$class = get_class($cached->{$method2}());
								$this->{$var} = new $class($cached->{$method2}()->getId());
							}
						} else {
							prettyPrint('Cache Error: Method "'.$method.'" not found in Class "'.get_called_class().'"');
							$valid_cache = false;
						}
					}
				} else {
					$valid_cache = false;
				}
			} else {
				$valid_cache = false;
			}
			if ($valid_cache === false) {
				$sql = "SELECT * FROM collectiveinvoice_orderposition WHERE id = " . $id;
				if ($DB->num_rows($sql)) {
					$rows = $DB->select($sql);
					$r = $rows[0];
					$this->id = $r["id"];
					$this->status = $r["status"];
					$this->quantity = $r["quantity"];
					$this->price = $r["price"];
					$this->cost = $r["cost"];
					$this->profit = $r["profit"];
					$this->taxkey = new TaxKey($r["taxkey"]);
					$this->comment = $r["comment"];
					$this->collectiveinvoice = $r["collectiveinvoice"];
					$this->type = $r["type"];
					$this->objectid = $r["object_id"];
					$this->invrel = $r["inv_rel"];
					$this->revrel = $r["rev_rel"];
					$this->file_attach = $r["file_attach"];
					$this->perso_order = $r["perso_order"];
					$this->sequence = $r["sequence"];

					Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
				}
			}
			// -- Temporary measure to assure default taxkey if none is set! //
			if ($this->taxkey->getId() == 0){
				$defaulttaxkey = TaxKey::getDefaultTaxKey();
				$this->taxkey = $defaulttaxkey; // grabbing the default taxkey just to be sure that one is set
			}

			// fix for #58 - Rückwärtskompatibilität für orderposition sequence
			if ($this->sequence == 0){
				$this->sequence = Orderposition::getNextSequence(new CollectiveInvoice($this->collectiveinvoice));
				if ($this->sequence > 0)
					$this->save();
			}
			//
		}
	}

	public static function getNextSequence(CollectiveInvoice $collectiveinvoice)
	{
		global $DB;
		$sql = " SELECT max(sequence) sequence FROM collectiveinvoice_orderposition WHERE collectiveinvoice = {$collectiveinvoice->getId()}";
		$selseq = $DB->select($sql);
		$seq = $selseq[0]["sequence"] + 1;
		if ($seq <= 0 )
			$seq = 1;
		return $seq;
	}

	/**
	 * ...liefert alle Eintraege/Auftragspositionen einer Sammelrechnung
	 * 
	 * @param int $collectiveId : Id einer Sammelrechnung
	 * @return Orderposition[]
	 */
	static function getAllOrderposition($collectiveId,$softdeleted = false,$relevant = false){
		global $DB;
		$status = '';
		if ($softdeleted)
		    $status = " AND status > 0 ";
		else
		    $status = " AND status = 1 ";
		if ($relevant)
		    $status .= " AND inv_rel > 0 ";
		$sql = "SELECT id FROM collectiveinvoice_orderposition WHERE collectiveinvoice = {$collectiveId} {$status} ORDER BY sequence";
		$orderpos = Array();
		if($DB->no_result($sql)){
			$result = $DB->select($sql);
			foreach($result as $r){
				$orderpos[] = new Orderposition($r["id"]);
			}
		}
		return $orderpos;
	}

	public function save()
	{
		global $DB;

		if ($this->id == 0){
			//Neuer Eintrag in DB
			$sql = "INSERT INTO collectiveinvoice_orderposition
						(`quantity`, `comment`, price, cost, profit,
						taxkey, `status`, collectiveinvoice, type,
						object_id, inv_rel, rev_rel, file_attach, perso_order, sequence )
						VALUES
						({$this->getQuantity()}, '{$this->getComment()}', {$this->getPrice()}, {$this->getCost()}, {$this->getProfit()},
						{$this->taxkey->getId()}, {$this->getStatus()}, {$this->getCollectiveinvoice()}, {$this->getType()},
						{$this->getObjectid()}, {$this->getInvrel()}, {$this->getRevrel()}, {$this->getFile_attach()}, {$this->getPerso_order()}, {$this->getSequence()} )";
			$res = $DB->no_result($sql);
//			prettyPrint($sql);
			if($res){
				$sql = " SELECT max(id) id FROM collectiveinvoice_orderposition";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["id"];
				Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
				return true;
			} else {
				return false;
			}
		} else {
			//update
			$sql = "UPDATE collectiveinvoice_orderposition
						SET
						`quantity` = {$this->getQuantity()},
						`comment` = '{$this->getComment()}',
						price = {$this->getPrice()},
						cost = {$this->getCost()},
						profit = {$this->getProfit()},
						taxkey = {$this->taxkey->getId()},
						type = {$this->getType()},
						`status` = {$this->getStatus()},
						object_id = {$this->getObjectid()},
						inv_rel = {$this->getInvrel()},
						rev_rel = {$this->getRevrel()},
						file_attach = {$this->getFile_attach()},
						collectiveinvoice = {$this->getCollectiveinvoice()},
						sequence = {$this->getSequence()},
						perso_order = {$this->getPerso_order()}
						WHERE id = {$this->getId()}";
			$res = $DB->no_result($sql);
//			prettyPrint($sql);
			if($res){
				Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Speicher-Funktion fuer ein Array von Orderpositionen
	 * 
	 * @param Orderposition[] $orderpositions
	 * @return boolean
	 */
	static function saveMultipleOrderpositions($orderpositions){
		$result=FALSE;
		global $DB;
		foreach ($orderpositions as $opos){
			//aus demo1 auskommentiert
			if ($opos->id == 0){
				//Neuer Eintrag in DB
				$sql = "INSERT INTO collectiveinvoice_orderposition
						(quantity, comment, price, cost, profit,
						taxkey, status, collectiveinvoice, type,
						object_id, inv_rel, rev_rel, file_attach, perso_order, sequence )
						VALUES
						({$opos->getQuantity()}, '{$opos->getComment()}', {$opos->getPrice()}, {$opos->getCost()}, {$opos->getProfit()},
						{$opos->taxkey->getId()}, 1, {$opos->getCollectiveinvoice()}, {$opos->getType()},
						{$opos->getObjectid()}, {$opos->getInvrel()}, {$opos->getRevrel()}, {$opos->getFile_attach()}, {$opos->getPerso_order()}, {$opos->getSequence()} )";
				$res = $DB->no_result($sql);
				if($res){
					$sql = " SELECT max(id) id FROM collectiveinvoice_orderposition";
					$thisid = $DB->select($sql);
					$opos->id = $thisid[0]["id"];
					$result = true;
					Cachehandler::toCache(Cachehandler::genKeyword($opos),$opos);
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
						cost = {$opos->getCost()},
						profit = {$opos->getProfit()},
						taxkey = {$opos->taxkey->getId()},
						type = {$opos->getType()},
						object_id = {$opos->getObjectid()},
						inv_rel = {$opos->getInvrel()},
						rev_rel = {$opos->getRevrel()},  
						file_attach = {$opos->getFile_attach()},  
						collectiveinvoice = {$opos->getCollectiveinvoice()},
						sequence = {$opos->getSequence()},
						perso_order = {$opos->getPerso_order()}
						WHERE id = {$opos->getId()}";
				$res = $DB->no_result($sql);
				if($res){
					Cachehandler::toCache(Cachehandler::genKeyword($opos),$opos);
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
			Cachehandler::removeCache(Cachehandler::genKeyword($this));
			unset($this);
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Soft Loeschfunktion fuer Auftragspositionen
	 * 
	 * @return boolean
	 */
	public function deletesoft(){
		global $DB;
		$sql = "UPDATE collectiveinvoice_orderposition SET status = 2 WHERE id = {$this->id}";
		$res = $DB->no_result($sql);
		if($res){
			Cachehandler::removeCache(Cachehandler::genKeyword($this));
			unset($this);
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Soft Loeschfunktion fuer Auftragspositionen
	 * 
	 * @return boolean
	 */
	public function restore()
	{
		global $DB;
		$sql = "UPDATE collectiveinvoice_orderposition SET status = 1 WHERE id = {$this->id}";
		$res = $DB->no_result($sql);
		if ($res) {
			Cachehandler::removeCache(Cachehandler::genKeyword($this));
			unset($this);
			return true;
		} else {
			return false;
		}
	}


	public function getMyArticle(){
		if ($this->type == self::TYPE_ARTICLE || $this->type == self::TYPE_ORDER)
			return new Article($this->objectid);
		else if ($this->type == self::TYPE_PERSONALIZATION){
			$tmp_perso_order = new Personalizationorder($this->objectid);
			$tmp_perso = new Personalization($tmp_perso_order->getPersoID());
			return $tmp_perso->getArticle();
		} else {
			return new Article();
		}
	}

    /**
     * @param $colinv CollectiveInvoice
     * @return bool
     */
    public static function checkMarginWarn($colinv)
    {
        $orderpositions = self::getAllOrderposition($colinv->getId());
        $perf = new Perferences();
        $price = 0.0;
        $buy = 0.0;

        foreach ($orderpositions as $orderposition) {
            $price += $orderposition->getPrice();
            $buy += $orderposition->getMyArticle()->getCost($orderposition->getQuantity());
        }

        if ($price < ($buy * ( 1+$perf->getMinmargin()/100 ))){
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
		$retval = "Manuell";
		if ($this->type == self::TYPE_ARTICLE || $this->type == self::TYPE_ORDER){
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
		if ($this->type == self::TYPE_PERSONALIZATION){
			$tmp_perso = new Personalizationorder($this->objectid);
//			var_dump($tmp_perso);
			$retval = $tmp_perso->getTitle()." (".$tmp_perso->getAmount().'Stk.'.")";
		} else {
			$tmp_art = new Article($this->objectid);
			$retval = $tmp_art->getNumber()." - ".$tmp_art->getTitle()." (".$this->quantity.'Stk.'.")";
		}
		return $retval;
	}
	
	public function getName(){
	    if ($this->type != 3)
	    {
	        $tmp_art = new Article($this->objectid);
	        return $tmp_art->getTitle();
	    } else {
	        $tmp_persoord = new Personalizationorder($this->objectid);
	        return $tmp_persoord->getTitle();
	    }
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

	public function getAmount(){
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
		return $this->taxkey->getValue();
	}
	
	public function getComment(){
		return $this->comment;
	}

	public function getCommentClean(){
		$ret = str_replace('<p>','',$this->comment);
		$ret = str_replace('</p>','',$ret);
		return $ret;
	}

	public function getCommentStripped(){
		$ret = strip_tags($this->comment);
		return $ret;
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
	    if ($this->getType() != self::TYPE_ORDER)
	        return $this->getPrice() * $this->getQuantity();
	    else
	        return $this->getPrice();
	}

	public function getGross()
	{
		if ($this->getType() != self::TYPE_ORDER)
			return ($this->getPrice() * $this->getQuantity()) + ($this->getPrice() * $this->getQuantity() / 100 * $this->taxkey->getValue());
		else
			return $this->getPrice() + ($this->getPrice() / 100 * $this->taxkey->getValue());
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
	
	/**
     * @return the $file_attach
     */
    public function getFile_attach()
    {
        return $this->file_attach;
    }
	public function getFileattach()
	{
		return $this->file_attach;
	}

	/**
     * @param field_type $file_attach
     */
    public function setFile_attach($file_attach)
    {
        $this->file_attach = $file_attach;
    }

	/**
	 * @return the $perso_order
	 */
	public function getPerso_order()
	{
		return $this->perso_order;
	}
	public function getPersoorder()
	{
		return $this->perso_order;
	}

	/**
     * @param number $perso_order
     */
    public function setPerso_order($perso_order)
    {
        $this->perso_order = $perso_order;
    }

	/**
	 * @return float
	 */
	public function getCost()
	{
		return $this->cost;
	}

	/**
	 * @param float $cost
	 */
	public function setCost($cost)
	{
		$this->cost = $cost;
	}

	/**
	 * @return float
	 */
	public function getProfit()
	{
		return $this->profit;
	}

	/**
	 * @param float $profit
	 */
	public function setProfit($profit)
	{
		$this->profit = $profit;
	}

	/**
	 * @return int
	 */
	public function getSequence()
	{
		return $this->sequence;
	}

	/**
	 * @param int $sequence
	 */
	public function setSequence($sequence)
	{
		$this->sequence = $sequence;
	}

	/**
	 * @return TaxKey
	 */
	public function getTaxkey()
	{
		return $this->taxkey;
	}

	/**
	 * @param TaxKey $taxkey
	 */
	public function setTaxkey($taxkey)
	{
		$this->taxkey = $taxkey;
	}
}