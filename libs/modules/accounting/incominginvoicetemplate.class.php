<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       03.05.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------


class Incominginvoicetemplate {

	private $id;
	private $invc_title;
	private $invc_price_netto;
	private $invc_taxes_active; //0=keine 1=19%  2=7%
	private $invc_crtusr;
	private $invc_crtdat;
	private $invc_companyid;
	private $invc_supplierid;

	function __construct($id = 0)
	{
		global $DB;

		if ($id > 0)
		{
			$sql = " SELECT * FROM invoices_templates WHERE id = {$id}";

			// sql returns only one record
			if($DB->num_rows($sql) == 1)
			{
				$res = $DB->select($sql);
				$this->id = $res[0]["id"];
				$this->invc_title = $res[0]["invc_title"];
				$this->invc_price_netto = $res[0]["invc_price_netto"];
				$this->invc_taxes_active = $res[0]["invc_taxes_active"];
				$this->invc_crtusr = $res[0]["invc_crtusr"];
				$this->invc_crtdat = $res[0]["invc_crtdat"];
				$this->invc_companyid = $res[0]["invc_companyid"];
				$this->invc_supplierid = $res[0]["invc_supplierid"];
				return true;
				// sql returns more than one record, should not happen!
			} else if ($DB->num_rows($sql) > 1)
			{
				$this->strError = "Mehr als eine Rechnung gefunden";
				return false;
			}
		}
	}

	public static function getAllTemplates()
	{
	    global $DB;
	    $incominginvoicetemplate = Array();
	    $sql = " SELECT * FROM invoices_templates
	                ORDER by id asc";
	    $res = $DB->select($sql);
	    if ($DB->num_rows($sql))
	        foreach ($res as $r)
	            $Incominginvoicetemplate[] = new Incominginvoicetemplate($r["id"]);

		/*$sql = " SELECT * FROM invoices_templates
    	 WHERE invc_companyid = {$_SESSION["invoiceem"]["companyid"]}
    	 ORDER by id asc";*/
			
		return $Incominginvoicetemplate;

	}

	public function delete()
	{
		global $DB;
		$sql = " DELETE FROM invoices_templates WHERE id = {$this->id}";
		$res = $DB->no_result($sql);
		unset($this);
		if($res)
		return true;
		else
		return false;

	}

	public function save($x)
	{
		global $DB;
		global $_USER;
		if ($this->id > 0)
		{
				
			$sql = " UPDATE invoices_templates SET
			invc_title = '{$this->invc_title}',
            invc_price_netto = '{$this->invc_price_netto}',
            invc_taxes_active = '{$this->invc_taxes_active}',
            invc_supplierid = '{$this->invc_supplierid}'		
			WHERE id = {$this->id}";
				
			$res = $DB->no_result($sql); //Aenderungen speichern
		}
		elseif ($this->id == 0 && $this->invc_price_netto > 0.00)
		{
			$sql = " INSERT INTO invoices_templates
            (invc_title, invc_price_netto, invc_taxes_active, invc_crtusr, invc_crtdat, invc_companyid, 
            invc_supplierid)
            VALUES
            ('{$this->invc_title}', '{$this->invc_price_netto}', '{$this->invc_taxes_active}', '{$_USER->getId()}', 
			'{$this->invc_crtdat}', {$_USER->getClient()->getId()},  '{$this->invc_supplierid}')";
				
			$res = $DB->no_result($sql); //Datensatz neu einfuegen
			if ($res)
			{
				$sql = " SELECT max(id) id FROM invoices_templates";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["id"];
			}
		}
		if($res)
		return true;
		else
		return false;
	}

public function getBruttoPrice()
	{
		$brutto = $this->invc_price_netto + $this->invc_price_netto /100 * $this->invc_taxes_active;
		
		return printPrice($brutto);
	}

	public function getTaxPrice()
	{
		$tax = $this->invc_price_netto /100 * $this->invc_taxes_active;
		
		return printPrice($tax);
	}

	public function getTaxRate()
	{
		global $_CONFIG;
		global $_USER;
		if($this->invc_taxes_active!='')
		$tax = $this->invc_taxes_active." %";
		else
		$tax = "0 %";
			
		return $tax;

	}


	public function getId()
	{
		return $this->id;
	}

	public function getInvc_title()
	{
		return $this->invc_title;
	}

	public function setInvc_title($invc_title)
	{
		$this->invc_title = $invc_title;
	}

	public function getInvc_price_netto()
	{
		return $this->invc_price_netto;
	}

	public function setInvc_price_netto($invc_price_netto)
	{
		$this->invc_price_netto = $invc_price_netto;
	}

	public function getInvc_taxes_active()
	{
		return $this->invc_taxes_active;
	}

	public function setInvc_taxes_active($invc_taxes_active)
	{
		$this->invc_taxes_active = $invc_taxes_active;
	}


	public function getInvc_crtusr()
	{
		return $this->invc_crtusr;
	}

	public function setInvc_crtusr($invc_crtusr)
	{
		$this->invc_crtusr = $invc_crtusr;
	}

	public function getInvc_crtdat2()
	{
		return $this->invc_crtdat;
	}

	public function setInvc_crtdat($invc_crtdat)
	{
		$this->invc_crtdat = $invc_crtdat;
	}

	public function getInvc_companyid()
	{
		return $this->invc_companyid;
	}

	public function setInvc_companyid($invc_companyid)
	{
		$this->invc_companyid = $invc_companyid;
	}

	public function getInvc_supplierid()
	{
		return $this->invc_supplierid;
	}

	public function setInvc_supplierid($invc_supplierid)
	{
		$this->invc_supplierid = $invc_supplierid;
	}
}
?>