<?
//----------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:		22.08.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

class Tradegroup {

	private $id = 0;
	private $state = 1;
	private $title;
	private $desc;
	private $shoprel;
	private $parentID = 0;
	private $revenueaccount;
	
	/**
	 * Konstruktor der Warengruppen-Klasse
	 * Falls id > 0, wird die entsprechende Warengruppe aus der DB geholt
	 * 
	 * @param int $id
	 */
	function __construct($id = 0){
		global $DB;
		global $_USER;
	
		if($id>0){
			$sql = "SELECT * FROM tradegroup WHERE id = {$id}";
			if($DB->num_rows($sql))
			{
				$r = $DB->select($sql);
				$r = $r[0];
				$this->id 		= (int)$r["id"];
				$this->state 	= (int)$r["tradegroup_state"];
				$this->title 	= $r["tradegroup_title"];
				$this->desc 	= $r["tradegroup_desc"];
				$this->shoprel 	= $r["tradegroup_shoprel"];
				$this->parentID	= $r["tradegroup_parentid"];
				$this->revenueaccount = new RevenueAccount($r["revenueaccount"]);
			}
		}
	}
	
	/**
	 * Speicher-Funktion fuer Warengruppen
	 * 
	 * @return boolean
	 */
	function save(){
		global $DB;
		global $_USER;
		$now = time();
		
		if($this->id > 0){
			$sql = "UPDATE tradegroup SET
					tradegroup_title 	= '{$this->title}', 
					tradegroup_desc 	= '{$this->desc}', 
					tradegroup_parentid	= {$this->parentID}, 
					tradegroup_shoprel 	= {$this->shoprel},
					revenueaccount = {$this->revenueaccount->getId()}
                    WHERE id = {$this->id}";
			return $DB->no_result($sql);
		} else {
			$sql = "INSERT INTO tradegroup 
					(tradegroup_state, tradegroup_title, tradegroup_desc, tradegroup_shoprel, tradegroup_parentid, revenueaccount)
					VALUES
					({$this->state}, '{$this->title}', '{$this->desc}', {$this->shoprel}, {$this->parentID}, {$this->revenueaccount->getId()} )";
			$res = $DB->no_result($sql);
            
            if($res){
                $sql = "SELECT max(id) id FROM tradegroup WHERE tradegroup_title = '{$this->title}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else {
                return false;
            }
		}
	}
	
	/**
	 * Loeschfunktion fuer Warengruppen.
	 * Die Warengruppe wird nicht entgueltig geloescht, der Status wird auf 0 gesetzt
	 *
	 * @return boolean
	 */
	public function delete(){
		global $DB;
		if($this->id > 0){
			$sql = "UPDATE tradegroup SET
					tradegroup_state = 0
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
	 * Funktion liefert alle aktiven Warengruppen
	 *
	 * @return Array : Warengruppe
	 */
	static function getAllTradegroups($parentid = 0){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM tradegroup WHERE tradegroup_state = 1";

		$sql .= " AND tradegroup_parentid = {$parentid} ";
		
		$sql .= " ORDER BY id ";
		
		if($DB->num_rows($sql))
		{
			foreach($DB->select($sql) as $r)
			{
				$retval[] = new Tradegroup($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Funktion liefert alle aktiven Warengruppen mit Shop-Freigabe
	 *
	 * @return Array : Warengruppe
	 */
	static function getAllTradegroupsForShop(){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM tradegroup WHERE 
				tradegroup_state = 1 AND 
				tradegroup_shoprel = 1 
				ORDER BY id";
		if($DB->num_rows($sql))
		{
			foreach($DB->select($sql) as $r)
			{
				$retval[] = new Tradegroup($r["id"]);
			}
		}
		return $retval;
	}
	
	/********************************************************************************
	 * 						GETTER und SETTER										*
	 *******************************************************************************/

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

	public function getTitle()
	{
	    return $this->title;
	}

	public function setTitle($title)
	{
	    $this->title = $title;
	}

	public function getDesc()
	{
	    return $this->desc;
	}

	public function setDesc($desc)
	{
	    $this->desc = $desc;
	}

	public function getShoprel()
	{
	    return $this->shoprel;
	}

	public function setShoprel($shoprel)
	{
	    $this->shoprel = $shoprel;
	}

	public function getParentID()
	{
	    return $this->parentID;
	}

	public function setParentID($parentID)
	{
	    $this->parentID = $parentID;
	}

	/**
	 * @return RevenueAccount
	 */
	public function getRevenueaccount()
	{
		return $this->revenueaccount;
	}

	/**
	 * @param RevenueAccount $revenueaccount
	 */
	public function setRevenueaccount($revenueaccount)
	{
		$this->revenueaccount = $revenueaccount;
	}
}
