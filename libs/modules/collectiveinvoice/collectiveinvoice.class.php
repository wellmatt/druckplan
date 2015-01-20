<?//--------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       19.09.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('libs/basic/user/user.class.php');
require_once('libs/modules/paymentterms/paymentterms.class.php');
require_once('libs/modules/deliveryterms/deliveryterms.class.php');
require_once('libs/modules/businesscontact/businesscontact.class.php');
require_once('libs/modules/businesscontact/address.class.php');

global $_USER;

class CollectiveInvoice{
	
	const ORDER_NUMBER = "number";
	const ORDER_CRTDATE			= " crtdate ";
	const ORDER_CRTDATE_DESC	= " crtdate desc";
	
	const TAX_ARTICLE			= 19;
	const TAX_PEROSALIZATION	= 19;

	private $id = 0;
	private $status	= 1;
	private $title = "";
	private $number = "- - -";
	private $crtdate;
	private $crtuser;
	private $uptdate;
	private $uptuser;
	private $deliverycosts = 0;
	private $comment = "";				// interner Kommentar
	private $businesscontact = 0;		// zugehoeriger Geschaeftskontakt
    private $custContactperson;			// Anspr. des Kunden
	private $client = 0;
	private $deliveryterm = 0;
	private $paymentterm = 0;
	private $deliveryaddress = 0;
    private $invoiceAddress;
	private $intent;					// Kostenstelle, Zweck, ... wird vom Kunden bei einer Bestellung angegeben (fuer Rechnung)
	
    private $internContact;				// Benutzer von KDM, der auf den Dokumenten auftauchen soll
    private $custMessage;				// Auf den Dokumenten "Ihre Nachricht"
    private $custSign;					// Auf den Dokumenten "Ihr Zeichen"

	/**
	 * Konstruktor f�r die Sammelrechnungen
	 * 
	 * @param int $id
	 */
	function __construct($id = 0){
		global $DB;
		global $_USER;
		
		$this->businesscontact = new BusinessContact();
		$this->client = $_USER->getClient();
		$this->crtuser = new User();
		$this->uptuser = new User();
		$this->paymentterm = new PaymentTerms();
		$this->deliveryterm = new DeliveryTerms();
		$this->deliveryaddress = new Address();
        $this->invoiceAddress = new Address();
        $this->internContact = new User();
        $this->custContactperson = new ContactPerson();
		
		if($id>0){
			$sql = "SELECT * FROM collectiveinvoice WHERE id = ".$id;
			if($DB->num_rows($sql)){
				$rows = $DB->select($sql);
				$r = $rows[0];
				$this->id = (int)$r["id"];
				$this->status =$r["status"];
				$this->title = $r["title"];
				$this->number = $r["number"];
				$this->comment = $r["comment"];
				$this->crtdate = $r["crtdate"];
				$this->uptdate = $r["uptdate"];
				$this->uptuser = new User((int)$r["uptuser"]);
				$this->crtuser = new User((int)$r["crtuser"]);
				$this->deliverycosts = $r["deliverycosts"];
				$this->client = new Client((int)$r["client"]);
				$this->businesscontact = new BusinessContact((int)$r["businesscontact"]);
				$this->deliveryterm = new DeliveryTerms((int)$r["deliveryterm"]);
				$this->paymentterm = new PaymentTerms((int)$r["paymentterm"]);
				$this->deliveryaddress = new Address($r["deliveryaddress"]);
				$this->invoiceAddress = new Address($r["invoiceaddress"]);
				$this->intent = $r["intent"];
                $this->internContact = new User($r["intern_contactperson"]);
                $this->custMessage = $r["cust_message"];
                $this->custSign	= $r["cust_sign"];
				$this->custContactperson = new ContactPerson($r["custContactperson"]);
			}
		}
	}//Ende vom Konstruktor

	/**
	 * Speichert die aufrufende manuelle Rechnung
	 * 
	 * @return boolean
	 */
	function save(){
		global $DB;
		global $_USER;
		$now = time();
		$tmp_number= "'".$this->number."'";
		if($this->id > 0){	//number, crtdate, crtuser und id d�rfen/sollen nicht ver�ndert werden
			$sql = "UPDATE collectiveinvoice SET
					status = {$this->status},
					title = '{$this->title}',
					comment = '{$this->comment}',
					number = '{$this->number}',
					comment = '{$this->comment}',
					uptuser = {$_USER->getId()},
					uptdate = {$now},
					deliverycosts = {$this->deliverycosts},
					businesscontact = {$this->businesscontact->getId()},
					client = {$this->client->getId()},
					deliveryterm = {$this->deliveryterm->getId()},
					paymentterm = {$this->paymentterm->getId()}, 
					deliveryaddress = {$this->deliveryaddress->getId()},
					invoiceaddress = {$this->invoiceAddress->getId()},
                    intern_contactperson = {$this->internContact->getId()},
                    cust_message = '{$this->custMessage}',  
                    cust_sign = '{$this->custSign}', 
                    custContactperson = {$this->custContactperson->getId()},
					intent = '{$this->intent}'
					WHERE id = {$this->id}";
			return $DB->no_result($sql);
		} else {
			$this->number = $this->getClient()->createOrderNumber(Client::NUMBER_ORDER);
			$sql = "INSERT INTO collectiveinvoice
				(status, title, number, crtdate, crtuser, 
				 deliverycosts, comment, businesscontact, client,
				 deliveryterm, paymentterm, deliveryaddress, invoiceaddress,
				 intern_contactperson, cust_message, cust_sign, custContactperson,
				 intent)
			VALUES
				({$this->status}, '{$this->title}', '{$this->number}', {$now}, {$_USER->getId()},
				 {$this->deliverycosts}, '{$this->comment}', {$this->businesscontact->getId()}, {$this->client->getId()},
				 {$this->deliveryterm->getId()}, {$this->paymentterm->getId()}, {$this->deliveryaddress->getId()}, {$this->invoiceAddress->getId()},
				 {$this->internContact->getId()}, '{$this->custMessage}', '{$this->custSign}', {$this->custContactperson->getId()},
				 '{$this->intent}')";
			$res = $DB->no_result($sql);

			if($res){
				$sql = "SELECT max(id) id FROM collectiveinvoice WHERE status > 0 ";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["id"];
				return true;
			} else
				return false;
		}
	}

	/**
	 * L�scht die aufrufende Sammelrechnung nicht wirklich, 
	 * sondern setzt des Status auf 0 und l�scht die Auftragsnummer
	 * 
	 * @return boolean
	 */
	public function delete(){
		global $DB;
		if($this->id > 0){
			$sql = "UPDATE collectiveinvoice SET status = 0 WHERE id = {$this->id}";
			if($DB->no_result($sql)){
				unset($this);
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Liefert die Sammelrechnung, die als letztes erstellt wurde (mit der groessten ID )
	 * 
	 * @return CollectiveInvoice
	 */
	static function getLastSavedCollectiveInvoice(){
		global $DB;		
		$sql = "SELECT max(id) id FROM collectiveinvoice WHERE status > 0";
		if($DB->no_result($sql)){
			$row = $DB->select($sql);
			$collectiveinvoice = new CollectiveInvoice((int)$row[0]["id"]);
		}
		return $collectiveinvoice;
	}
	
	/**
	 * Liefert alle Sammelrechnungen
	 * 
	 * @return Array : CollectiveInvoice
	 */
	static function getAllCollectiveInvoice($order = self::ORDER_NUMBER, $filter = ""){
		global $DB;
		$sql = "SELECT * FROM collectiveinvoice WHERE status > 0 {$filter} ORDER By {$order}";
		$collectiveInvoices = Array();
		if($DB->no_result($sql)){
			$result = $DB->select($sql);
			foreach($result as $r){
				$collectiveInvoices[] = new CollectiveInvoice($r["id"]);
			}
		}
		return $collectiveInvoices;
	}
	
	/**
	 * Liefert alle Sammelrechnungen für einen Kunden
	 * 
	 * @return Array : CollectiveInvoice
	 */
	static function getAllCollectiveInvoiceForBcon($order = self::ORDER_NUMBER, $bcon = 0){
		global $DB;
		$sql = "SELECT * FROM collectiveinvoice WHERE status > 0 AND businesscontact = {$bcon} ORDER By {$order}";
		$collectiveInvoices = Array();
		if($DB->no_result($sql)){
			$result = $DB->select($sql);
			foreach($result as $r){
				$collectiveInvoices[] = new CollectiveInvoice($r["id"]);
			}
		}
		return $collectiveInvoices;
	}
	
	/**
	* Liefert alle Sammelrechnungen fuer einen bestimmten Kunden, die �ber den Shop abgeschickt wurden (Ersteller = ShopUser (ID=1)) 
	 *
	 * @return Array : CollectiveInvoice
	 */
	static function getAllCollectiveInvoiceForShop($order = self::ORDER_CRTDATE, $busiconID = 0, $search_string = ""){
		global $DB;
		$collectiveInvoices = Array();
		
		$sql = " SELECT t1.id FROM collectiveinvoice t1, collectiveinvoice_orderposition t2
				WHERE 
				t1.status > 0 AND t1.status < 11
				AND t1.crtuser = 1  
				AND t1.id = t2.collectiveinvoice ";
		if($busiconID > 0){ 
			$sql.= " AND t1.businesscontact = {$busiconID} ";
		}  
		if($search_string != "" && $search_string != NULL){
			$sql .= " AND (
							t1.number LIKE '%{$search_string}%' OR
							t1.intent LIKE '%{$search_string}%' OR
							t2.comment LIKE '%{$search_string}%' 
						 )";	
		}
		$sql .= "GROUP BY t1.id 
				 ORDER BY t1.{$order} ";
		error_log("SQL: ".$sql." -- ");
		error_log($DB->getLastError());
		if($DB->no_result($sql)){
			$result = $DB->select($sql);
			foreach($result as $r){
				$collectiveInvoices[] = new CollectiveInvoice($r["id"]);
			}
		}
		return $collectiveInvoices;
	}
	
	/**
	 * Liefert die h�chste bisher vergebene (Sammel-)Auftragsnummer
	 */
	static function getHighestNumber(){
		global $DB;
		$ret_number=0;
		
		$jahr = date("Y", time()); 	// Derzeitige vierstellige Jahreszahl
		$monat = date("m", time()); // Derzeitige zweistellige Monatszahl
		
		$filter = "AU-".$jahr.$monat."-";
		
		$ret_number=$filter."0000"; // Falls es f�r den Monat noch keine Nummer gibt
		
		$sql = "SELECT * FROM collectiveinvoice WHERE status > 0 AND number LIKE '%{$filter}%'";
		$manualInvoices = Array();
		if($DB->no_result($sql)){
			$result = $DB->select($sql);
			foreach($result as $r){
				if ($ret_number<$r["number"]){
					$ret_number = $r["number"];
				}
			}
		}
		return $ret_number;
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
	
		// falls kein status uebergeben, setze den Aktuellen
		if($status == 0){
			$status = $this->status;
		}
	
		switch ($status) {
			case 1: $retval = "Angelegt";break;
			case 2: $retval = "Gesendet u. Bestellt";break;
			case 3: $retval = "angenommen";break;
			case 4: $retval = "In Produktion";break;
			case 5: $retval = "Erledigt";break;
			default: $retval="...";
		}
		return $retval;
	}
	
	/**
	 * Liefert alle OrderPositionen f�r die aufrufende manuelle Rechnung 
	 *  
	 * @return multitype:OrderPosition
	 */
	public function getPositions(){
        return Orderposition::getAllOrderposition($this->id);
	}
	
	public function getCustomer(){
		return $this->businesscontact;
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

	public function getNumber()
	{
	    return $this->number;
	}

	public function setNumber($number)
	{
	    $this->number = $number;
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

	public function getUptdate()
	{
	    return $this->uptdate;
	}

	public function setUptdate($uptdate)
	{
	    $this->uptdate = $uptdate;
	}

	public function getUptuser()
	{
	    return $this->uptuser;
	}

	public function setUptuser($uptuser)
	{
	    $this->uptuser = $uptuser;
	}

	public function getDeliverycosts()
	{
	    return $this->deliverycosts;
	}

	public function setDeliverycosts($deliverycosts)
	{
	    $this->deliverycosts = $deliverycosts;
	}

	public function getComment()
	{
	    return $this->comment;
	}

	public function setComment($comment)
	{
	    $this->comment = $comment;
	}

	public function getBusinesscontact()
	{
	    return $this->businesscontact;
	}

	public function setBusinesscontact($businesscontact)
	{
	    $this->businesscontact = $businesscontact;
	}

	public function getDeliveryterm()
	{
	    return $this->deliveryterm;
	}

	public function setDeliveryterm($deliveryterm)
	{
	    $this->deliveryterm = $deliveryterm;
	}

	public function getPaymentterm()
	{
	    return $this->paymentterm;
	}

	public function setPaymentterm($paymentterm)
	{
	    $this->paymentterm = $paymentterm;
	}

	public function getDeliveryaddress()
	{
	    return $this->deliveryaddress;
	}

	public function setDeliveryaddress($deliveryaddress)
	{
	    $this->deliveryaddress = $deliveryaddress;
	}
	public function setDeliveryaddressById($deliveryID){
		$this->deliveryaddress = new Address($deliveryID);
	}
	public function getClient()
	{
	    return $this->client;
	}

	public function setClient($client)
	{
	    $this->client = $client;
	}
	
    public function getIntent()
	{
	    return $this->intent;
	}

	public function setIntent($intent)
	{
	    $this->intent = $intent;
	}
	
	public function getInternContact()
	{
	    return $this->internContact;
	}
	
	public function setInternContact($internContact)
	{
	    $this->internContact = $internContact;
	}
	
	public function getCustMessage()
	{
	    return $this->custMessage;
	}
	
	public function setCustMessage($custMessage)
	{
	    $this->custMessage = $custMessage;
	}
	
	public function getCustSign()
	{
	    return $this->custSign;
	}
	
	public function setCustSign($custSign)
	{
	    $this->custSign = $custSign;
	}

    public function getInvoiceAddress()
    {
        return $this->invoiceAddress;
    }

    public function setInvoiceAddress($invoiceAddress)
    {
        $this->invoiceAddress = $invoiceAddress;
    }
    
	/**
     * @return the $custContactperson
     */
    public function getCustContactperson()
    {
        return $this->custContactperson;
    }

	/**
     * @param ContactPerson $custContactperson
     */
    public function setCustContactperson($custContactperson)
    {
        $this->custContactperson = $custContactperson;
    }

    
    
}
?>
