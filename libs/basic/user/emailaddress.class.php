<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			14.10.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

/**
 * Klasse zur Verwaltung der E-Mail-Adressen eines Benutzers fuer das E-Mail-Modul.
 */
class Emailaddress {
	
	const ORDER_ADDRESS 	= " address ";
	const ORDER_ID 			= " id ";
	const ORDER_TYPE 		= " type ";
	 
	private $id = 0;
	private $userID;				// ID des zugehoerigen Benutzers
	private $status = 1;			// 0 = geloescht, 1 = aktiv
	private $address;				// E-Mail-Adresse
	private $type = 0;				// 0 = lesen, 1 = schreiben, 2 = beides
	private $password;				// Passwort zur E-Mail-Adresse
	private $host;					// Serveradresse 
	private $port;					// Portnummer
	private $signature;				// Signatur zu der E-Mail-Adresse	
	private $useSSL;				// Wird SSL genutzt?
	private $useIMAP;				// Wird IMAP genutzt?
	 
	/**
	 * Konstruktor
	 * 
	 * @param Int $id
	*/
	function __construct($id = 0){
		global $DB;
		global $_USER;
		
		if ($id > 0){
			$sql = " SELECT * FROM user_emailaddress WHERE id = {$id}";
			if($DB->num_rows($sql) > 0){
				$res = $DB->select($sql);
				$this->id = $res[0]["id"];
				$this->status = $res[0]["status"];
				$this->userID = $res[0]["user_id"];
				$this->address = $res[0]["address"];
				$this->password = $res[0]["password"];
				$this->type = $res[0]["type"];
				$this->host = $res[0]["host"];
				$this->port = $res[0]["port"];
				$this->signature = $res[0]["signature"];
				$this->useIMAP = $res[0]["use_imap"];
				$this->useSSL = $res[0]["use_ssl"];
			}
		}
	}
	
	/**
	 * Speicherfunktion fuer E-Mail-Adressen
	 * 
	 * @return boolean
	 */
	function save(){
		global $DB;
		global $_USER;
		
		if($this->id > 0){
			$sql = "UPDATE user_emailaddress SET 
					status = {$this->status}, 
					user_id = {$this->userID}, 
					address	= '{$this->address}',  
					password = '{$this->password}', 
					host = '{$this->host}', 
					type = {$this->type}, 
					port= {$this->port}, 
					signature = '{$this->signature}',
					use_imap = {$this->useIMAP},
					use_ssl = {$this->useSSL} 
					WHERE id = {$this->id}";
			return $DB->no_result($sql);
		} else {
			$sql = "INSERT INTO user_emailaddress
					(status, user_id, address, password,
					 host, type, port, signature, use_imap, use_ssl)
					VALUES
					({$this->status}, {$this->userID}, '{$this->address}', '{$this->password}', 
					'{$this->host}', {$this->type}, {$this->port}, '{$this->signature}', {$this->useIMAP}, {$this->useSSL} )";
			$res = $DB->no_result($sql);
			
			if($res){
				$sql = "SELECT max(id) id FROM user_emailaddress WHERE address = '{$this->address}'";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["id"];
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Abruf aller E-Mail-Adressen
	 * 
	 * @param String $order
	 * @param Int $user_id
	 * @return Array
	 */
	static function getAllEmailaddress($order = Emailaddress::ORDER_ADDRESS, $user_id=0){
		global $DB;
		$addresses = Array();
	
		$sql = " SELECT id FROM user_emailaddress WHERE status > 0 ";
		if ($user_id > 0)
			$sql .= " AND user_id = {$user_id}";
		$sql .= " ORDER BY {$order}";
		$res = $DB->select($sql);
		foreach ($res as $r){
			$addresses[] = new Emailaddress($r["id"]);
		}
		return $addresses;
	}
	
	/**
	 * Loeschfunktion fuer E-Mail-Addressen
	 * Achtung: Hier wird wirklich geloescht und nicht nur ein Status = 0 gesetzt
	 */
	function delete(){
		global $DB;
		$sql = "DELETE FROM user_emailaddress
				WHERE id = {$this->id}";
		return $DB->no_result($sql);
	}
	
	/**
	 * Abfrage, ob Benutzer die E-Mails dieser Adresse lesen darf
	 * @return boolean
	 */
	public function readable(){
		if ($this->getType() == 0 || $this->getType() == 2){
			return true;
		}	
		return false;
	}
	
	/**
	 * Abfrage, ob ein Benutzer E-Mail von dieser Adresse senden darf
	 * @return boolean
	 */
	public function writeable(){
		if ($this->getType() == 1 || $this->getType() == 2){
			return true;
		}
		return false;
	}

	public function getId()
	{
	    return $this->id;
	}

	public function getUserID()
	{
	    return $this->userID;
	}

	public function setUserID($userID)
	{
	    $this->userID = $userID;
	}

	public function getStatus()
	{
	    return $this->status;
	}

	public function setStatus($status)
	{
	    $this->status = $status;
	}

	public function getAddress()
	{
	    return $this->address;
	}

	public function setAddress($address)
	{
	    $this->address = $address;
	}

	public function getType()
	{
	    return $this->type;
	}

	public function setType($type)
	{
	    $this->type = $type;
	}

	public function getPassword()
	{
	    return $this->password;
	}

	public function setPassword($password)
	{
	    $this->password = $password;
	}

	public function getHost()
	{
	    return $this->host;
	}

	public function setHost($host)
	{
	    $this->host = $host;
	}

	public function getPort()
	{
		return $this->port;
	}
	
	public function setPort($port)
	{
	    $this->port = $port;
	}

	public function getUseSSL() {
		return $this->useSSL;
	}
	
	public function setUseSSL($useSSL) {
		$this->useSSL = $useSSL;
	}
	
	public function getUseIMAP() {
		return $this->useIMAP;
	}
	
	public function setUseIMAP($useIMAP) {
		$this->useIMAP = $useIMAP;
	}
}
?>