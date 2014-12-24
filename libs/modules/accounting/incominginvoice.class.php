<?php // ---------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       03.05.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------


class Incominginvoice {

	private $id;
	private $invc_title;
	private $invc_number;
	private $invc_price_netto = 0;
	private $invc_taxes_active = 0; 
	private $invc_payed = 0;
	private $invc_payed_dat = 0;
	private $invc_payable_dat = 0;
	private $invc_crtusr = 0;
	private $invc_crtdat = 0;				// Rechnungsdatum ! nicht mehr, wann es eingegeben wurde (ab 08.01.2013)
	private $invc_companyid = 0;
	private $invc_supplierid = 0;
	
	private $invc_orders = Array();

	function __construct($id = 0)
	{
		global $DB;

		if ($id > 0)
		{
			$sql = " SELECT * FROM invoices_emissions WHERE id = {$id}";

			// sql returns only one record
			if($DB->num_rows($sql) == 1)
			{
				$res = $DB->select($sql);
				$this->id = $res[0]["id"];
				$this->invc_title = $res[0]["invc_title"];
				$this->invc_number = $res[0]["invc_number"];
				$this->invc_price_netto = $res[0]["invc_price_netto"];
				$this->invc_taxes_active = $res[0]["invc_taxes_active"];
				$this->invc_payed = $res[0]["invc_payed"];
				$this->invc_payed_dat = $res[0]["invc_payed_dat"];
				$this->invc_payable_dat = $res[0]["invc_payable_dat"];
				$this->invc_crtusr = $res[0]["invc_crtusr"];
				$this->invc_crtdat = $res[0]["invc_crtdat"];
				$this->invc_companyid = $res[0]["invc_companyid"];
				$this->invc_supplierid = $res[0]["invc_supplierid"];
        		$this->invc_orders = unserialize($res[0]["invc_orders"]);
				return true;
				// sql returns more than one record, should not happen!
			} else if ($DB->num_rows($sql) > 1)
			{
				$this->strError = "Mehr als eine Rechnung gefunden";
				return false;
			}
		}
	}

	public static function getAllInvoicesByMonth(){
		global $DB;
		$startstamp = mktime(0, 0, 0, $_SESSION["invoiceem"]["month"], 1, $_SESSION["invoiceem"]["year"]);
		$endstamp   = mktime(23, 59, 59, $_SESSION["invoiceem"]["month"], date("t", $startstamp), $_SESSION["invoiceem"]["year"]);
		$incominginvoice = Array();

		$sql = " SELECT * FROM invoices_emissions
    	 WHERE 
    	 invc_payed     = 0 AND
    	 invc_crtdat    between {$startstamp} and {$endstamp}
    	 ORDER by invc_payable_dat, id asc
    	 ";
		$res = $DB->select($sql);
		if ($DB->num_rows($sql))
		foreach ($res as $r)
		$incominginvoice[] = new Incominginvoice($r["id"]);
			
		return $incominginvoice;
	}

	public static function getAllPaidInvoicesByMonth()
	{
		global $DB;
		$startstamp = mktime(0, 0, 0, $_SESSION["invoiceem"]["month"], 1, $_SESSION["invoiceem"]["year"]);
		$endstamp   = mktime(23, 59, 59, $_SESSION["invoiceem"]["month"], date("t", $startstamp), $_SESSION["invoiceem"]["year"]);
		$incominginvoice = Array();

		$sql = " SELECT * FROM invoices_emissions
    	 WHERE 
    	 invc_payed     = 1 AND
    	 invc_crtdat    between {$startstamp} and {$endstamp}
    	 ORDER by invc_payable_dat, id asc
    	 ";
		$res = $DB->select($sql);
		if ($DB->num_rows($sql))
		foreach ($res as $r)
		$incominginvoice[] = new Incominginvoice($r["id"]);
			
		return $incominginvoice;

	}
	
	/**
	 * liefert alle Eingangsrechnungen
	 * 
	 * @return multitype:Incominginvoice
	 */
	public static function getAllInvoices($filter){
		global $DB;
		
		$incominginvoice = Array();
	
		$sql = "SELECT * FROM invoices_emissions
				WHERE
				id > 0 "; 
		
		if ($filter["date_from"] > 0 && $filter["date_to"] > 0) {
			$sql .= " AND invc_crtdat > {$filter["date_from"]} AND invc_crtdat < {$filter["date_to"]} ";
		} 
		if ($filter["payed_status"] == 1){
			$sql .= " AND invc_payed = 0 ";
		}
		if ($filter["payed_status"] == 2){
			$sql .= " AND invc_payed = 1 ";
		}
		if ($filter["cust_id"] > 0){
			$sql .= " AND invc_supplierid = {$filter["cust_id"]} "; 
		}
		
		$sql .=	" ORDER by invc_crtdat, id asc";
		$res = $DB->select($sql);
		echo $DB->getLastError();
		if ($DB->num_rows($sql)){
			foreach ($res as $r){
				$incominginvoice[] = new Incominginvoice($r["id"]);
			}
		}
		return $incominginvoice;
	}

	public function delete()
	{
		global $DB;
		$sql = " DELETE FROM invoices_emissions WHERE id = {$this->id}";
		$res = $DB->no_result($sql);
		unset($this);
		if($res)
		return true;
		else
		return false;

	}

	public function save()
	{
		global $DB;
		global $_USER;
		
		$tmp_invc_orders = serialize($this->invc_orders);
		
		if ($this->id > 0)
		{
			$sql = " UPDATE invoices_emissions SET
		            invc_title = '{$this->invc_title}',
		            invc_number = '{$this->invc_number}',
		            invc_price_netto = '{$this->invc_price_netto}',
		            invc_taxes_active = '{$this->invc_taxes_active}',
		            invc_payed = '{$this->invc_payed}',
		            invc_payed_dat = '{$this->invc_payed_dat}',
		            invc_payable_dat = '{$this->invc_payable_dat}',
		            invc_supplierid = '{$this->invc_supplierid}', 
		            invc_orders = '{$tmp_invc_orders}', 
		            invc_crtdat = '{$this->invc_crtdat}' 		
					WHERE id = {$this->id}";
			$res = $DB->no_result($sql); //Aenderungen speichern
		}

		elseif ($this->id == 0 && $this->invc_price_netto > 0.00)
		{
			$sql = " INSERT INTO invoices_emissions
		            (invc_title, invc_number, invc_price_netto, invc_taxes_active, 
		             invc_payed, invc_payed_dat, invc_payable_dat, 
		             invc_supplierid, invc_crtusr, invc_crtdat, invc_orders)
		            VALUES
		            ('{$this->invc_title}','{$this->invc_number}','{$this->invc_price_netto}','{$this->invc_taxes_active}', 
		            '{$this->invc_payed}','{$this->invc_payed_dat}','{$this->invc_payable_dat}',
		            '{$this->invc_supplierid}', {$_USER->getId()}, {$this->invc_crtdat}, '{$tmp_invc_orders}' )";
			// echo "<br>---- ".$sql."<br>";
			$res = $DB->no_result($sql); //Datensatz neu einfuegen
			if ($res)
			{
				$sql = " SELECT max(id) id FROM invoices_emissions";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["id"];
			}
		}

		if($res)
		return true;
		else
		return false;
	}


	/**
	 * berechnet den Brutto Preis  und gibt ihn als formatierten String aus
	 * 
	 * @return string
	 */
	public function getBruttoPrice(){
		
		$brutto = $this->invc_price_netto + $this->invc_price_netto /100 * $this->invc_taxes_active;
		return printPrice($brutto);
	}
	
	/**
	 * berechnet den Brutto Preis und gibt ihn als Komma-Zahl (Float) aus
	 *
	 * @return string
	 */
	public function getBrutto(){
		
		$brutto = $this->invc_price_netto + $this->invc_price_netto /100 * $this->invc_taxes_active;
		return $brutto;
	}

	/**
	 * Berechnet die Steuern (MWST) und  gibt sie als formatierten String aus
	 *
	 * @return string
	 */
	public function getTaxPrice(){
		
		$tax = $this->invc_price_netto /100 * $this->invc_taxes_active;
		return printPrice($tax);
	}
	
	/**
	 * Berechnet die Steuern (MWST)
	 *
	 * @return float
	 */
	public function getTax(){
	
		$tax = $this->invc_price_netto /100 * $this->invc_taxes_active;
		return $tax;
	}

	/**
	 * Liefert die MWST-Rate mit Prozent-Zeichen
	 * @return string
	 */
	public function getTaxRate()
	{
		if($this->invc_taxes_active != '')
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

	public function getInvc_number()
	{
		return $this->invc_number;
	}

	public function setInvc_number($invc_number)
	{
		$this->invc_number = $invc_number;
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


	public function getInvc_payed()
	{
		return $this->invc_payed;
	}

	public function setInvc_payed($invc_payed)
	{
		$this->invc_payed = $invc_payed;
	}

	public function getInvc_payed_dat()
	{
		return $this->invc_payed_dat;
	}

	public function setInvc_payed_dat($invc_payed_dat)
	{
		$this->invc_payed_dat = $invc_payed_dat;
	}

	public function getInvc_payable_dat()
	{
		return $this->invc_payable_dat;
	}

	public function setInvc_payable_dat($invc_payable_dat)
	{
		$this->invc_payable_dat = $invc_payable_dat;
	}


	public function getInvc_crtusr()
	{
		return $this->invc_crtusr;
	}

	public function setInvc_crtusr($invc_crtusr)
	{
		$this->invc_crtusr = $invc_crtusr;
	}

	public function getInvc_crtdat()
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
	
	/**
     * @return the $invc_orders
     */
    public function getInvc_orders()
    {
        return $this->invc_orders;
    }

	/**
     * @param multitype: $invc_orders
     */
    public function setInvc_orders($invc_orders)
    {
        $this->invc_orders = $invc_orders;
    }
}
?>