<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			23.08.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

class PaymentTerms{
	const ORDER_NAME = ' name1 ';
	const ORDER_ID = ' id ';
	
	private $id = 0;
	private $active;
	private	$client = NULL;
	private $name;
	private $comment;
	private $skonto1;
	private $skontodays1;		// Tage, wie lange der 1.Skonto gilt
	private $skonto2;
	private $skontodays2;		// Tage, wie lange der 2. Skonto gilt
	private $nettodays;			// Ender der ganzen Zahlungsfrist
	private $shoprel = 0;
	
	/**
	 * Konstruktor f�r die Zahlungsbedingung-Klasse
	 * 
	 * @param int $id
	 */
	public function __construct($id=0){
		global $DB;
		global $_USER;
		
		if($id>0){
			$sql = "SELECT * FROM paymentterms WHERE id=".$id." AND active > 0 ";
			if($DB->num_rows($sql)){
                
				$res = $DB->select($sql);
                $res = $res[0];
                
                $this->id 			= $res["id"];
                $this->name 		= $res["name1"];
                $this->client 		= NULL;
                $this->active 		= $res["active"];
                $this->comment 		= $res["comment"];
                $this->skonto1 		= $res["skonto1"];
                $this->skontodays1 	= $res["skonto_days1"];
                $this->skonto2		= $res["skonto2"];
                $this->skontodays2	= $res["skonto_days2"];
                $this->nettodays 	= $res["netto_days"];
                $this->shoprel 	= $res["shop_rel"];
            }
		}
	}
	
	
	/**
	 * Funktion liefert alle Zahlungsmethoden
	 * 
	 * @param String $order
	 * @return Array von Zahlungsmethoden
	 */
	static function getAllPaymentConditions($order = self::ORDER_ID){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM paymentterms 
				WHERE active > 0
				ORDER BY {$order}";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new PaymentTerms($r["id"]);
			}
		}
		return $retval;
	}

	
	/**
	 * Funktion f�r die Abfrage aller Zahlungsbedingungen/Zahlungsarten
	 * ohne die ActiveVoodoo Klasse
	 * 
	 * @return PaymentTerms[]
	 */
	public static function getAllPaymentTerms(){
		global $DB;
		$sql = "SELECT * FROM paymentterms WHERE active > 0";
		$paymentterms = Array();
		if($DB->no_result($sql)){
			$result = $DB->select($sql);
			foreach($result as $r){
				$paymentterms[] = new PaymentTerms($r["id"]);
			}
		}
		return $paymentterms; 
	}
	
	/**
	 * Speicherfunktion f�r Zahlungsbedingungen
	 * 
	 * @return boolean
	 */
	public function save(){
		global $DB;
		if($this->id > 0){
            $sql = "UPDATE paymentterms SET
                        name1 = '{$this->name}',
                        comment = '{$this->comment}',
                        netto_days = {$this->nettodays},
                        skonto1 = {$this->skonto1},
                        skonto2 = {$this->skonto2},
                        skonto_days1 = {$this->skontodays1},
                        skonto_days2 = {$this->skontodays2},
                        shop_rel = {$this->shoprel}
                    WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO paymentterms
                        (name1, comment, netto_days, active, shop_rel,
                         skonto1, skonto2, skonto_days1, skonto_days2)
                    VALUES
                        ('{$this->name}', '{$this->comment}', {$this->nettodays}, 1, {$this->shoprel},
                         {$this->skonto1}, {$this->skonto2}, {$this->skontodays1}, {$this->skontodays2})";
            $res = $DB->no_result($sql);
            if($res) {
                $sql = "SELECT max(id) id FROM paymentterms WHERE name1 = '{$this->name}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else  
                return false;
        }
	}
	
	/**
	 * L�schfunktion f�r Zahlungsbedingungen
	 * 
	 * @return boolean
	 */
	public function delete(){
		global $DB;
		if($this->id > 0){
			$sql = "UPDATE paymentterms SET active = 0 WHERE id = {$this->id}";
			$res = $DB->no_result($sql);
			if($res){
				unset($this);
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Funktion liefert alle Zahlungsmethoden, die f�r den Shop freigegeben sind
	 * 
	 * @return Array von PaymentTerms
	 */
	static function getAllPaymentTermsforShop(){
		global $DB;
		$paymentterms = Array();
		
		$sql = "SELECT * FROM paymentterms WHERE active > 0 AND shop_rel = 1 ";
		if($DB->no_result($sql)){
			$result = $DB->select($sql);
			foreach($result as $r){
				$paymentterms[] = new PaymentTerms($r["id"]);
			}
		}
		return $paymentterms;
	}

	public function getId()
	{
	    return $this->id;
	}

	public function setId($id)
	{
	    $this->id = $id;
	}

	public function getActive()
	{
	    return $this->active;
	}

	public function setActive($active)
	{
	    $this->active = $active;
	}

	public function getClient()
	{
	    return $this->client;
	}

	public function setClient($client)
	{
	    $this->client = $client;
	}

	public function getName()
	{
	    return $this->name;
	}

	public function setName($name)
	{
	    $this->name = $name;
	}
	
	public function getName1()
	{
		return $this->name;
	}

	public function getComment()
	{
	    return $this->comment;
	}

	public function setComment($comment)
	{
	    $this->comment = $comment;
	}

	public function getSkonto1()
	{
	    return $this->skonto1;
	}

	public function setSkonto1($skonto1)
	{
	    $this->skonto1 = $skonto1;
	}

	public function getSkontodays1()
	{
	    return $this->skontodays1;
	}

	public function setSkontodays1($skontodays1)
	{
	    $this->skontodays1 = $skontodays1;
	}

	public function getSkonto2()
	{
	    return $this->skonto2;
	}

	public function setSkonto2($skonto2)
	{
	    $this->skonto2 = $skonto2;
	}

	public function getSkontodays2()
	{
	    return $this->skontodays2;
	}

	public function setSkontodays2($skontodays2)
	{
	    $this->skontodays2 = $skontodays2;
	}

	public function getNettodays()
	{
	    return $this->nettodays;
	}

	public function setNettodays($nettodays)
	{
	    $this->nettodays = $nettodays;
	}

	public function getShoprel()
	{
	    return $this->shoprel;
	}

	public function setShoprel($shoprel)
	{
	    $this->shoprel = $shoprel;
	}
}?>