<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			13.01.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
//
// Dies ist eine Kopie der Eingangsrechnungen fuer die Behandlung von eingehenden
// Gutschriften
//
// ----------------------------------------------------------------------------------

class Incomingrevert {

	private $id;
	private $rev_title;
	private $rev_number;
	private $rev_price_netto = 0;
	private $rev_taxes_active = 0;
	private $rev_payed = 0;
	private $rev_payed_dat = 0;
	private $rev_payable_dat = 0;
	private $rev_crtusr = 0;
	private $rev_crtdat = 0;				// Rechnungsdatum ! nicht mehr, wann es eingegeben wurde (ab 08.01.2013)
	private $rev_companyid = 0;
	private $rev_supplierid = 0;

	function __construct($id = 0)
	{
		global $DB;

		if ($id > 0)
		{
			$sql = " SELECT * FROM invoices_reverts WHERE id = {$id}";

			// sql returns only one record
			if($DB->num_rows($sql) == 1)
			{
				$res = $DB->select($sql);
				$this->id = $res[0]["id"];
				$this->rev_title = $res[0]["rev_title"];
				$this->rev_number = $res[0]["rev_number"];
				$this->rev_price_netto = $res[0]["rev_price_netto"];
				$this->rev_taxes_active = $res[0]["rev_taxes_active"];
				$this->rev_payed = $res[0]["rev_payed"];
				$this->rev_payed_dat = $res[0]["rev_payed_dat"];
				$this->rev_payable_dat = $res[0]["rev_payable_dat"];
				$this->rev_crtusr = $res[0]["rev_crtusr"];
				$this->rev_crtdat = $res[0]["rev_crtdat"];
				$this->rev_companyid = $res[0]["rev_companyid"];
				$this->rev_supplierid = $res[0]["rev_supplierid"];
				return true;
				// sql returns more than one record, should not happen!
			} else if ($DB->num_rows($sql) > 1)
			{
				$this->strError = "Mehr als eine Gutschrift gefunden";
				return false;
			}
		}
	}

	public static function getAllRevertsByMonth(){
		global $DB;
		$startstamp = mktime(0, 0, 0, $_SESSION["invoiceem"]["month"], 1, $_SESSION["invoiceem"]["year"]);
		$endstamp   = mktime(23, 59, 59, $_SESSION["invoiceem"]["month"], date("t", $startstamp), $_SESSION["invoiceem"]["year"]);
		$retval = Array();

		$sql = " SELECT * FROM invoices_reverts
				WHERE
				rev_payed     = 0 AND
				rev_crtdat    between {$startstamp} and {$endstamp}
				ORDER by rev_payable_dat, id asc ";
		$res = $DB->select($sql);
		if ($DB->num_rows($sql))
			foreach ($res as $r)
			$retval[] = new Incomingrevert($r["id"]);

		return $retval;
	}

	public static function getAllPaidRevertsByMonth()
	{
		global $DB;
		$startstamp = mktime(0, 0, 0, $_SESSION["invoiceem"]["month"], 1, $_SESSION["invoiceem"]["year"]);
		$endstamp   = mktime(23, 59, 59, $_SESSION["invoiceem"]["month"], date("t", $startstamp), $_SESSION["invoiceem"]["year"]);
		$retval = Array();

		$sql = " SELECT * FROM invoices_reverts
				WHERE
				rev_payed     = 1 AND
				rev_crtdat    between {$startstamp} and {$endstamp}
				ORDER by rev_payable_dat, id asc ";
		$res = $DB->select($sql);
		if ($DB->num_rows($sql))
			foreach ($res as $r)
			$retval[] = new Incomingrevert($r["id"]);

		return $retval;

	}

	/**
	 * liefert alle Eingangsgutschriften
	 *
	 * @return multitype:IncimingRevert
	 */
	public static function getAllInvoices($filter){
		global $DB;

		$retval = Array();

		$sql = "SELECT * FROM invoices_reverts
				WHERE
				id > 0 ";

		if ($filter["date_from"] >0 && $filter["date_to"] > 0) {
			$sql .= " AND rev_crtdat > {$filter["date_from"]} AND rev_crtdat < {$filter["date_to"]} ";
		}
		if ($filter["payed_status"] == 1){
			$sql .= " AND rev_payed = 0 ";
		}
		if ($filter["payed_status"] == 2){
			$sql .= " AND rev_payed = 1 ";
		}
		if ($filter["cust_id"] > 0){
			$sql .= " AND rev_supplierid = {$filter["cust_id"]} ";
		}

		$sql .=	" ORDER by rev_crtdat, id asc";
		// echo $sql;
		$res = $DB->select($sql);
		echo $DB->getLastError();
		if ($DB->num_rows($sql)){
			foreach ($res as $r){
				$retval[] = new Incomingrevert($r["id"]);
			}
		}
		return $retval;
	}

	public function delete()
	{
		global $DB;
		$sql = " DELETE FROM invoices_reverts WHERE id = {$this->id}";
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
		if ($this->id > 0)
		{
			$sql = " UPDATE invoices_reverts SET
					rev_title = '{$this->rev_title}',
					rev_number = '{$this->rev_number}',
					rev_price_netto = '{$this->rev_price_netto}',
					rev_taxes_active = '{$this->rev_taxes_active}',
					rev_payed = '{$this->rev_payed}',
					rev_payed_dat = '{$this->rev_payed_dat}',
					rev_payable_dat = '{$this->rev_payable_dat}',
					rev_supplierid = '{$this->rev_supplierid}',
					rev_crtdat = '{$this->rev_crtdat}'
					WHERE id = {$this->id}";
			$res = $DB->no_result($sql); //Aenderungen speichern
		}

		elseif ($this->id == 0 && $this->rev_price_netto > 0.00)
		{
			$sql = " INSERT INTO invoices_reverts
					(rev_title, rev_number, rev_price_netto, rev_taxes_active,
					rev_payed, rev_payed_dat, rev_payable_dat,
					rev_supplierid, rev_crtusr, rev_crtdat)
					VALUES
					('{$this->rev_title}','{$this->rev_number}','{$this->rev_price_netto}','{$this->rev_taxes_active}',
					'{$this->rev_payed}','{$this->rev_payed_dat}','{$this->rev_payable_dat}',
					'{$this->rev_supplierid}', {$_USER->getId()}, {$this->rev_crtdat} )";

			$res = $DB->no_result($sql); //Datensatz neu einfuegen
			if ($res)
			{
				$sql = " SELECT max(id) id FROM invoices_reverts";
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

		$brutto = $this->rev_price_netto + $this->rev_price_netto /100 * $this->rev_taxes_active;
		return printPrice($brutto);
	}

	/**
	 * berechnet den Brutto Preis und gibt ihn als Komma-Zahl (Float) zurueck
	 *
	 * @return string
	 */
	public function getBrutto(){

		$brutto = $this->rev_price_netto + $this->rev_price_netto /100 * $this->rev_taxes_active;
		return $brutto;
	}

	/**
	 * Berechnet die Steuern (MWST) und  gibt sie als formatierten String aus
	 *
	 * @return string
	 */
	public function getTaxPrice(){

		$tax = $this->rev_price_netto /100 * $this->rev_taxes_active;
		return printPrice($tax);
	}
	
	/**
	 * Berechnet die Steuern (MWST)
	 *
	 * @return float
	 */
	public function getTax(){
	
		$tax = $this->rev_price_netto /100 * $this->rev_taxes_active;
		return $tax;
	}

	public function getTaxRate(){
		if($this->rev_taxes_active!='')
			$tax = $this->rev_taxes_active." %";
		else
			$tax = "0 %";
			
		return $tax;

	}

	// --------------------------------- GETTER und SETTER ------------------------------------------------------------
	 
	public function getId()
	{
		return $this->id;
	}

	public function getRev_title()
	{
	    return $this->rev_title;
	}

	public function setRev_title($rev_title)
	{
	    $this->rev_title = $rev_title;
	}

	public function getRev_number()
	{
	    return $this->rev_number;
	}

	public function setRev_number($rev_number)
	{
	    $this->rev_number = $rev_number;
	}

	public function getRev_price_netto()
	{
	    return $this->rev_price_netto;
	}

	public function setRev_price_netto($rev_price_netto)
	{
	    $this->rev_price_netto = $rev_price_netto;
	}

	public function getRev_taxes_active()
	{
	    return $this->rev_taxes_active;
	}

	public function setRev_taxes_active($rev_taxes_active)
	{
	    $this->rev_taxes_active = $rev_taxes_active;
	}

	public function getRev_payed()
	{
	    return $this->rev_payed;
	}

	public function setRev_payed($rev_payed)
	{
	    $this->rev_payed = $rev_payed;
	}

	public function getRev_payed_dat()
	{
	    return $this->rev_payed_dat;
	}

	public function setRev_payed_dat($rev_payed_dat)
	{
	    $this->rev_payed_dat = $rev_payed_dat;
	}

	public function getRev_payable_dat()
	{
	    return $this->rev_payable_dat;
	}

	public function setRev_payable_dat($rev_payable_dat)
	{
	    $this->rev_payable_dat = $rev_payable_dat;
	}

	public function getRev_crtusr()
	{
	    return $this->rev_crtusr;
	}

	public function setRev_crtusr($rev_crtusr)
	{
	    $this->rev_crtusr = $rev_crtusr;
	}

	public function getRev_crtdat()
	{
	    return $this->rev_crtdat;
	}

	public function setRev_crtdat($rev_crtdat)
	{
	    $this->rev_crtdat = $rev_crtdat;
	}

	public function getRev_companyid()
	{
	    return $this->rev_companyid;
	}

	public function setRev_companyid($rev_companyid)
	{
	    $this->rev_companyid = $rev_companyid;
	}

	public function getRev_supplierid()
	{
	    return $this->rev_supplierid;
	}

	public function setRev_supplierid($rev_supplierid)
	{
	    $this->rev_supplierid = $rev_supplierid;
	}
}
?>
