<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       03.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
class Finishing{
	private $id = 0;
	private $status;
	private $name;
	private $beschreibung;
	private $kosten;
	private $lector_id;
	
	function __construct($id = 0)
	{
		global $DB;
		global $_USER;
	
	if ($id>0)
		{
			$sql = "SELECT * FROM finishing WHERE id = {$id}";
			if ($DB->num_rows($sql))
			{
				$res = $DB->select($sql);
				$this->id = $res[0]["id"];
				$this->name = $res[0]["name"];
				$this->beschreibung = $res[0]["beschreibung"];
				$this->kosten = $res[0]["kosten"];
				$this->lector_id = $res[0]["lector_id"];
			}
		}
	}
	static function getAllFinishings()
	{
		global $DB; 
		$retval = Array();
		$sql = "SELECT id FROM finishing WHERE status = 1 ORDER BY ID";
		if($DB->num_rows($sql))
		{
			$res = $DB->select($sql);
			foreach($res as $r)
			{
				$retval[] = new Finishing($r["id"]);
			}
		}
		return $retval;
	}
	
	function delete()
	{
		global $DB;
		if($this->id)
		{
			$sql = "UPDATE finishing SET status = 0 WHERE id = {$this->id}";
			$res = $DB->no_result($sql);
			if($res)
			{
				unset($this);
				return true;
			}
			else
			{
				return false;
			}
		}
	}
	
	function save()
	{
		global $DB;
		if ($this->id > 0)
		{
			$sql = "UPDATE finishing SET
			name = '{$this->name}',
			beschreibung = '{$this->beschreibung}',
			kosten = '{$this->kosten}' 
			WHERE id = '{$this->id}'";
			$res = $DB->no_result($sql);
			if ($res)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		else 
		{
			$sql = "INSERT INTO finishing (status, name, beschreibung, kosten)
			VALUES (1, '{$this->name}', '{$this->beschreibung}', '{$this->kosten}')";
			$res = $DB->no_result($sql);
			if ($res)
			{
			    $sql = "SELECT max(id) id FROM finishing";
			    $thisid = $DB->select($sql);
			    $this->id = $thisid[0]["id"];
				return true;
			}
			else
			{
				return false;
			}
		}
			
	}
	
	function getId()
	{
		return $this->id;
	}
	
	function getStatus()
	{
		return $this->status;
	}
	
	function setStatus($status)
	{
		$this->status = $status;
	}
	
	function getName()
	{
		return $this->name;
	}
	
	function setName($name)
	{
		$this->name = $name;
	}
	
	function getBeschreibung()
	{
		return $this->beschreibung;
	}
	
	function setBeschreibung($beschreibung)
	{
		$this->beschreibung = $beschreibung;
	}
	
	function getKosten()
	{
		return $this->kosten;
	}
	
	function setKosten($kosten)
	{
		$this->kosten = $kosten;  
	}
	
	function getLectorId()
	{
		return $this->lector_id;
	}
	function getKostenEuro()
	{
		$zw = $this->kosten;
		return $zw .'.00 &euro;';
	}
	
	function clearID()
	{
		$this->id = 0;
	}
}
?>