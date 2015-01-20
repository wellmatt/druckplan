<? // -----------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			08.08.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// --------------------------------------------------------------------------------------
//
//	Datei zur Behandlung von Bestellungen von Personalisierungen z.B: im Kunden-Portal
//
// --------------------------------------------------------------------------------------
require_once 'libs/modules/personalization/personalization.orderitem.class.php';
require_once 'libs/modules/personalization/personalization.item.class.php';
require_once 'libs/modules/personalization/personalization.class.php';
require_once 'libs/modules/personalization/persofont.class.php';

class Personalizationorder{
	
	const ORDER_CRTDATE = " crtdate ";
	const ORDER_TITLE	= " title ";
	
	const FILE_PATH = "docs/personalization/";
	
	private $id = 0;				// ID der Bestellung der Personalisierung
	private $status=1;				// Status der Bestellung
	private $title;					// Titel der Bestellung
	private $persoID;				// ID der zugehoerigen Personalisierung
	private $customerID;			// ID des bestellenden Kunden
	private $documentID;			// ID des fertigen Dokuments
	private $crtdate;				// Erstelldatum
	private $crtuser;				// Ersteller
	private $comment;				// Kommentar
	private $orderdate;				// Bestelldatum
	private $amount;				// Bestellmenge
	private $contactPersonID = 0;	// ID des ausgewaehlten Ansprechpartners
	private $deliveryAddressID = 0; // ID der ausgewaehlten Lieferaddresse

	function __construct($id = 0){
		global $DB;
		global $_USER;
		
		$this->crtuser = new User();
		
		if ($id > 0){
			$sql = "SELECT * FROM personalization_orders WHERE id = {$id}";
			if($DB->num_rows($sql)){
				$r = $DB->select($sql);
				$r = $r[0];
				$this->id = $r["id"];
				$this->status = $r["status"];
				$this->title = $r["title"];
				$this->persoID = $r["persoid"];
				$this->documentID = $r["documentid"];
				$this->customerID = $r["customerid"];
				$this->comment = $r["comment"];
				$this->orderdate = $r["orderdate"];
				$this->amount = $r["amount"];
				$this->contactPersonID = $r["contact_person_id"];
				$this->deliveryAddressID = $r["deliveryaddress_id"];
					
				if ($r["crtuser"] != 0 && $r["crtuser"] != "" ){
					$this->crtuser = new User($r["crtuser"]);
					$this->crtdate = $r["crtdate"];
				} else {
					$this->crtuser = new User(0);
					$this->crtdate = 0;
				}
			}
		}
	}
	
	/**
	 * Speicher-Funktion fuer eine Bestellung einer Personalisierung
	 *
	 * @return boolean
	 */
	function save(){
		global $DB;
		global $_USER;
		$now = time();

		if($this->id > 0){
			$sql = "UPDATE personalization_orders SET
					status = {$this->status},
					title  = '{$this->title}', 
					comment = '{$this->comment}',
					orderdate = {$this->orderdate}, 
					amount = {$this->amount}, 
					contact_person_id = {$this->contactPersonID}, 
					deliveryaddress_id = {$this->deliveryAddressID}
					WHERE id = {$this->id}";
			return $DB->no_result($sql);
		} else {
			$sql = "INSERT INTO personalization_orders
					( status, comment, crtdate, crtuser, amount, 
					 customerid, persoid, title, contact_person_id,
					 deliveryaddress_id )
					VALUES
					({$this->status}, '{$this->comment}', {$now}, {$_USER->getId()}, {$this->amount},  
					{$this->customerID}, {$this->persoID}, '{$this->title}', {$this->contactPersonID},  
					{$this->deliveryAddressID})";
			$res = $DB->no_result($sql);

			if($res){
				$sql = "SELECT max(id) id FROM personalization_orders WHERE persoid = {$this->persoID} AND customerid = {$this->customerID} ";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["id"];
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Loeschfunktion fuer Bestellungen von Personalisierungen
	 *
	 * @return boolean
	 */
	public function delete(){
		global $DB;
		if($this->id > 0){
			$sql = "UPDATE personalization_orders
					SET
					status = 0
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
	 * Funktion liefert alle aktiven Bestellungen von Personalisierungen nach angegebener Reighenfolge
	 * 
	 * @param int $customerID
	 * @param String $order
	 * @return Array:Personalizationorder
	 */
	static function getAllPersonalizationorders($customerID, $order = self::ORDER_CRTDATE, $status_filter=false){
		global $DB;
		$retval = Array();
		
		// Damit nur "Bestellte" ( Status > 1 ) anzeigt werden 
		if ($status_filter){ $status = 1; } else { $status = 0; }
		
		$sql = "SELECT id FROM personalization_orders WHERE status >= {$status} AND customerid = {$customerID} AND orderdate > 0 ORDER BY {$order}";

		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){ 
				$retval[] = new Personalizationorder($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * <p>Funktion liefert alle  Bestellungen von Personalisierungen mit Status = 1 nach angegebener Reighenfolge</p>
	 *
	 * @param int $customerID
	 * @param String $order
	 * @return Array:Personalizationorder
	 */
	static function getAllPersonalizationordersForShop($customerID, $order = self::ORDER_CRTDATE, $search_string){
		global $DB;
		$retval = Array();
	
		$sql = "SELECT id FROM personalization_orders 
				WHERE 
				status = 1 
				AND customerid = {$customerID} ";
		
		if ($search_string != NULL && $search_string != ""){
			$sql .= " AND title LIKE '%{$search_string}%' ";
		}
				
		$sql .= "ORDER BY {$order} ";
	
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Personalizationorder($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * ... liefert das Bild zum zugehoerigen Status
	 * 
	 * @return string
	 */
	function getStatusImage(){
		$retval="gray.gif";
		switch ($this->status) {
			case 0: $retval = "black.gif";break;
			case 1: $retval = "red.gif";break;
			case 2: $retval = "orange.gif";break;
			case 3: $retval = "yellow.gif";break;
			case 4: $retval = "lila.gif";break;
			case 5: $retval = "green.gif";break;
			default: $retval="gray.gif";
		}
		return $retval;
	}
	
	/**
	 * ... liefert die Beschreibung zum aktuellen Status
	 *
	 * @return string
	 */
	public function getStatusDescription($status = 0){
		$retval="gray.gif";
		
		if($status == 0){
			$status = $this->status;
		}
		
		switch ($status) {
			case 1: $retval = "Angelegt";break;
			case 2: $retval = "Gesendet u. Bestellt";break;
			case 3: $retval = "In Bearbeitung";break;
			case 4: $retval = "Fertig u. im Versand";break;
			case 5: $retval = "Fertig u. Abholbereit";break;
			default: $retval="...";
		}
		return $retval;
	}
	
	/**
	 * Liefert den Preis der angegebenen Menge von der zugehoerigen Personalisierung
	 * @param int $amount
	 * @return float
	 */
	public function getPrice($amount){
		$tmp_perso = new Personalization($this->persoID);
		$tmp_price = $tmp_perso->getPrice($amount);
		return $tmp_price;
	}
	
	/**
	 * Funktion entscheidet, ob ein Preis fuer den Kunden sichtbar ist
	 * @param int $amount
	 * @return boolean
	 */
	public function isPriceVisible($amount){
		$ret = false;
		$tmp_perso = new Personalization($this->persoID);
		$tmp_visible = $tmp_perso->getPriceVisible($amount);
		if($tmp_visible == 1){
			$ret = true;	
		}
		return $ret;
	}
	
	/**
	 * Kopiert eine Personalisierungs-Bestellung und liefert das entsprechende Objekt
	 * 
	 * @return Personalizationorder
	 */
	public function copyPersoOrderForShopOrder(){
		$new_persosorder = new Personalizationorder();
		$new_persosorder->setAmount($this->amount);
		$new_persosorder->setComment($this->comment);
		$new_persosorder->setContactPersonID($this->contactPersonID);
		$new_persosorder->setCrtdate($this->crtdate);
		$new_persosorder->setCrtuser($this->crtuser);
		$new_persosorder->setCustomerID($this->customerID);
		$new_persosorder->setOrderdate(time());
		$new_persosorder->setPersoID($this->persoID);
		$new_persosorder->setStatus(2);
		$new_persosorder->setTitle($this->title);
		$new_persosorder->save();
		
		$all_items = Personalizationorderitem::getAllPersonalizationorderitems($this->id);
		foreach ($all_items AS $item){
			$new_item = new Personalizationorderitem();
			$new_item->setPersoID($item->getPersoID());
			$new_item->setPersoItemID($item->getPersoItemID());
			$new_item->setPersoorderID($new_persosorder->getId());
			$new_item->setValue($item->getValue());
			$new_item->save();
		}
		// PDF Dokument erstellen
		$new_doc = new Document();
		$new_doc->setRequestId($new_persosorder->getId());
		$new_doc->setRequestModule(Document::REQ_MODULE_PERSONALIZATION);
		$new_doc->setType(Document::TYPE_PERSONALIZATION_ORDER);
		$new_doc->setReverse(0);
		$hash = $new_doc->createDoc(Document::VERSION_EMAIL, false, false);
		$new_doc->createDoc(Document::VERSION_PRINT, $hash);
		$new_doc->setName("PERSO_ORDER");
		$new_doc->save();
		
		
		
		/**********************************************************************/
		
		// TODO: Rueckseite generieren
		
		/**********************************************************************/
		
		
		
		return $new_persoorder;
	}

	public function getId()
	{
	    return $this->id;
	}
	public function getStatus()
	{
	    return $this->status;
	}

	public function setStatus($status)
	{
	    $this->status = $status;
	}
	public function getTitle()
	{
	    return $this->title;
	}

	public function setTitle($title)
	{
	    $this->title = $title;
	}

	public function getPersoID()
	{
	    return $this->persoID;
	}

	public function setPersoID($persoID)
	{
	    $this->persoID = $persoID;
	}

	public function getCustomerID()
	{
	    return $this->customerID;
	}

	public function setCustomerID($customerID)
	{
	    $this->customerID = $customerID;
	}

	public function getCrtdate()
	{
	    return $this->crtdate;
	}

	public function setCrtdate($crtdate)
	{
	    $this->crtdate = $crtdate;
	}

	public function getCrtuser()
	{
	    return $this->crtuser;
	}

	public function setCrtuser($crtuser)
	{
	    $this->crtuser = $crtuser;
	}

	public function getComment()
	{
	    return $this->comment;
	}

	public function setComment($comment)
	{
	    $this->comment = $comment;
	}

	public function getOrderdate()
	{
	    return $this->orderdate;
	}

	public function setOrderdate($orderdate)
	{
	    $this->orderdate = $orderdate;
	}

	public function getAmount()
	{
	    return $this->amount;
	}

	public function setAmount($amount)
	{
	    $this->amount = $amount;
	}

    public function getContactPersonID()
    {
        return $this->contactPersonID;
    }

    public function setContactPersonID($contactPersonID)
    {
        $this->contactPersonID = $contactPersonID;
    }

    public function getDeliveryAddressID()
    {
        return $this->deliveryAddressID;
    }

    public function setDeliveryAddressID($deliveryAddressID)
    {
        $this->deliveryAddressID = $deliveryAddressID;
    }
}
?>