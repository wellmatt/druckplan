<?//--------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       29.09.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

class DeliveryTerms{
	
	const ORDER_NAME = "name1";
	
	private $id = 0;
	private $name1;
	private $comment;
	private $charges;
	private $status;
	private $client;
	private $shoprel;
	private $tax;
	
	
	/**
	 * Konstruktor-Funktion der Lieferbedingungen
	 * 
	 * @param int $id
	 */
	public function __construct($id=0)
	{
		global $DB;
		global $_USER;
		if($id>0){
			$sql = "SELECT * FROM deliveryterms WHERE id = {$id} ";
			if($DB->num_rows($sql)){
				$r = $DB->select($sql);
				$r = $r[0];
				$this->id = $r["id"];
				$this->status = $r["active"];
				$this->name1 = $r["name1"];
				$this->comment = $r["comment"];
				$this->charges = $r["charges"];
				$this->client = new Client($r["client"]);
				$this->shoprel = $r["shoprel"];
				$this->tax = $r["tax"];
			}
		}
	}
	
	/**
	 * Abrufen aller Lieferbedingungen
	 * 
	 * @param Sting $order Sortierung, nach der die Lieferbedingungen zur�ckgegeben werden sollen
	 * @return ARRAY DeliveryTerms
	 */
	public static function getAllDeliveryConditions($order = DeliveryTerms::ORDER_NAME){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM deliveryterms WHERE active > 0 ORDER BY {$order}";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new DeliveryTerms($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Speicher-Funktion f�r Lieferbedingungen
	 *
	 * @return boolean
	 */
	function save(){
		global $DB;
		global $_USER;
		$now = time();
	
		if($this->id > 0){
			$sql = "UPDATE deliveryterms SET
					name1 	= '{$this->name1}',
					comment = '{$this->comment}',
					shoprel = {$this->shoprel},
					charges	= {$this->charges},
					tax	= {$this->tax}
					WHERE id = {$this->id}";
				return $DB->no_result($sql);
		} else {
			$sql = "INSERT INTO deliveryterms
					(name1, comment, shoprel, charges, 
					active, client, tax)
					VALUES
					('{$this->name1}', '{$this->comment}', {$this->shoprel}, {$this->charges}, 
					1 , {$_USER->getClient()->getId()}, {$this->tax})";
			$res = $DB->no_result($sql);

			if($res){
				$sql = "SELECT max(id) id FROM deliveryterms WHERE name1 = '{$this->name1}'";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["id"];
			return true;
			} else {
			return false;
			}
		}
	}
	
	/**
	 * L�schfunktion f�r Lieferbedingungen.
	 * Der Artikel wird nicht entg�ltig gel�scht, der Status und die Freigabe wird auf 0 gesetzt
	 *
	 * @return boolean
	 */
	public function delete(){
		global $DB;
		if($this->id > 0){
			$sql = "UPDATE deliveryterms
					SET
					shoprel = 0,
					active = 0
					WHERE id = {$this->id}";
			if($DB->no_result($sql)){
				unset($this);
				return true;
			} else {
				return false;
			}
		}
	}

	public function getId()
	{
	    return $this->id;
	}

	public function getName1()
	{
	    return $this->name1;
	}

	public function setName1($name1)
	{
	    $this->name1 = $name1;
	}

	public function getComment()
	{
	    return $this->comment;
	}

	public function setComment($comment)
	{
	    $this->comment = $comment;
	}
	
	public function getDescription(){
		return $this->comment;
	}

	public function getCharges()
	{
	    return $this->charges;
	}

	public function setCharges($charges)
	{
	    $this->charges = $charges;
	}

	public function getStatus()
	{
	    return $this->status;
	}

	public function setStatus($status)
	{
	    $this->status = $status;
	}

	public function getClient()
	{
	    return $this->client;
	}

	public function setClient($client)
	{
	    $this->client = $client;
	}

	public function getShoprel()
	{
	    return $this->shoprel;
	}

	public function setShoprel($shoprel)
	{
	    $this->shoprel = $shoprel;
	}

    public function getTax()
    {
        return $this->tax;
    }

    public function setTax($tax)
    {
        $this->tax = $tax;
    }
}

?>