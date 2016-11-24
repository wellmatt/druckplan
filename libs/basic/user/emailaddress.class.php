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
	private $status = 1;			// 0 = geloescht, 1 = aktiv
	private $address;				// E-Mail-Adresse
	private $login;					// Login Name
	private $password;				// Passwort zur E-Mail-Adresse
	private $host;					// Serveradresse 
	private $port;					// Portnummer
	private $ssl = 0;
	private $tls = 0;
	private $signature;				// Signatur zu der E-Mail-Adresse
	private $smtp_host;
	private $smtp_port;
	private $smtp_user;
	private $smtp_password;
	private $smtp_ssl = 0;
	private $smtp_tls = 0;

	/**
	 * Konstruktor
	 * 
	 * @param Int $id
	*/
	function __construct($id = 0){
		global $DB;
		global $_USER;
		
		if ($id > 0){
			$sql = " SELECT * FROM emailaddress WHERE id = {$id}";
			if($DB->num_rows($sql) > 0){
				$res = $DB->select($sql);
				$this->id = $res[0]["id"];
				$this->status = $res[0]["status"];
				$this->address = $res[0]["address"];
				$this->login = $res[0]["login"];
				$this->password = $res[0]["password"];
				$this->host = $res[0]["host"];
				$this->port = $res[0]["port"];
				$this->ssl = $res[0]["ssl"];
				$this->tls = $res[0]["tls"];
				$this->signature = $res[0]["signature"];
				$this->smtp_host = $res[0]["smtp_host"];
				$this->smtp_port = $res[0]["smtp_port"];
				$this->smtp_user = $res[0]["smtp_user"];
				$this->smtp_password = $res[0]["smtp_password"];
				$this->smtp_ssl = $res[0]["smtp_ssl"];
				$this->smtp_tls = $res[0]["smtp_tls"];
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
			$sql = "UPDATE emailaddress SET
					`status` = {$this->status},
					address	= '{$this->address}',
					login	= '{$this->login}',
					`password` = '{$this->password}',
					`host` = '{$this->host}',
					`port`= {$this->port},
					`ssl`= {$this->ssl},
					`tls`= {$this->tls},
					signature = '{$this->signature}',
					smtp_host = '{$this->smtp_host}',
					smtp_port = '{$this->smtp_port}',
					smtp_user = '{$this->smtp_user}',
					smtp_ssl = '{$this->smtp_ssl}',
					smtp_tls = '{$this->smtp_tls}',
					smtp_password = '{$this->smtp_password}'
					WHERE id = {$this->id}";
			return $DB->no_result($sql);
		} else {
			$sql = "INSERT INTO emailaddress
					(`status`, address, login, `password`,
					 `host`, `port`, `ssl`, `tls`, signature,
					 smtp_host, smtp_port, smtp_user, smtp_password, smtp_ssl, smtp_tls)
					VALUES
					({$this->status}, '{$this->address}', '{$this->login}', '{$this->password}',
					'{$this->host}', {$this->port}, {$this->ssl}, {$this->tls}, '{$this->signature}',
					'{$this->smtp_host}', '{$this->smtp_port}', '{$this->smtp_user}', '{$this->smtp_password}', {$this->smtp_ssl}, {$this->smtp_tls} )";
			$res = $DB->no_result($sql);
			
			if($res){
				$sql = "SELECT max(id) id FROM emailaddress WHERE address = '{$this->address}' AND login = '{$this->login}'";
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
	 * @return Emailaddress[]
	 */
	static function getAllEmailaddress($order = Emailaddress::ORDER_ADDRESS){
		global $DB;
		$addresses = Array();
	
		$sql = " SELECT id FROM emailaddress WHERE `status` > 0 ";
		$sql .= " ORDER BY {$order}";
		$res = $DB->select($sql);
		foreach ($res as $r){
			$addresses[] = new Emailaddress($r["id"]);
		}
		return $addresses;
	}

	/**
	 * Gets one Emailaddress by address
	 * @param $address
	 * @return Emailaddress
	 */
	public static function getByAddress($address)
	{
		global $DB;
		$sql = "SELECT id FROM emailaddress WHERE `status` > 0 AND address LIKE '%{$address}%' ORDER BY id LIMIT 1";
		if ($DB->num_rows($sql)){
			$res = $DB->select($sql);
			return new Emailaddress($res[0]["id"]);
		}
		return false;
	}

	/**
	 * @param User $user
	 * @return Emailaddress[]
	 */
	public static function getAllEmailaddressForUser($user)
	{
		global $DB;
		$addresses = Array();

		$sql = "SELECT * FROM emailaddress
 				INNER JOIN user_emailaddress ON emailaddress.id = user_emailaddress.emailaddress
				WHERE user_emailaddress.`user` = {$user->getId()} AND emailaddress.status > 0 ";
		$res = $DB->select($sql);
		foreach ($res as $r){
			$addresses[] = new Emailaddress($r["id"]);
		}
		return $addresses;
	}

	/**
	 * @param Emailaddress $emailaddress
	 * @param User $user
	 * @return boolean
	 */
	public static function isDefault($emailaddress, $user)
	{
		global $DB;
		$sql = "SELECT default_address FROM user_emailaddress WHERE `user` = {$user->getId()} AND emailaddress = {$emailaddress->getId()}";
		if ($DB->num_rows($sql) > 0){
			$res = $DB->select($sql);
			$r = $res[0];
			if ($r["default_address"] == 1)
				return true;
		}
		return false;
	}

	/**
	 * @param Emailaddress $emailaddress
	 * @param User $user
	 */
	public static function assignToUser($emailaddress, $user)
	{
		global $DB;
		$sql = "INSERT INTO user_emailaddress VALUES ({$user->getId()}, {$emailaddress->getId()}, 0)";
		$DB->no_result($sql);
	}

	/**
	 * @param Emailaddress $emailaddress
	 * @param User $user
	 */
	public static function unassignFromUser($emailaddress, $user)
	{
		global $DB;
		$sql = "DELETE FROM user_emailaddress WHERE `user` = {$user->getId()} AND emailaddress = {$emailaddress->getId()}";
		prettyPrint($sql);
		$DB->no_result($sql);
	}

	/**
	 * @param Emailaddress $emailaddress
	 * @param User $user
	 */
	public static function setDefaultForUser($emailaddress, $user)
	{
		global $DB;
		$unsetsql = "UPDATE user_emailaddress SET default_address = 0 WHERE `user` = {$user->getId()}";
		$DB->no_result($unsetsql);
		$setstar = "UPDATE user_emailaddress SET default_address = 1 WHERE `user` = {$user->getId()} AND emailaddress = {$emailaddress->getId()}";
		$DB->no_result($setstar);
	}

	/**
	 * @param User $user
	 * @return Emailaddress
	 */
	public static function getDefaultOrFirstForUser($user)
	{
		global $DB;
		$sql = "SELECT emailaddress FROM user_emailaddress
 				INNER JOIN emailaddress ON user_emailaddress.emailaddress = emailaddress.id
				WHERE `user` = {$user->getId()} AND default_address = 1 AND `status` > 0";

		if ($DB->num_rows($sql)){
			$res = $DB->select($sql);
			return new Emailaddress((int)$res[0]["emailaddress"]);
		} else {
			$addresses = self::getAllEmailaddressForUser($user);
			if (count($addresses) > 0){
				return $addresses[0];
			}
		}
		return false;
	}
	
	/**
	 * Loeschfunktion fuer E-Mail-Addressen
	 * Achtung: Hier wird wirklich geloescht und nicht nur ein Status = 0 gesetzt
	 */
	function delete(){
		global $DB;
		$sql2 = "DELETE FROM user_emailaddress WHERE emailaddress = {$this->id}"; // Bugfix für leere nicht mehr existente mail adressen
		$DB->no_result($sql2);
		$sql = "DELETE FROM emailaddress WHERE id = {$this->id}";
		return $DB->no_result($sql);
	}

	function clearID()
	{
		$this->id = 0;
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

	public function getAddress()
	{
	    return $this->address;
	}

	public function setAddress($address)
	{
	    $this->address = $address;
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

	/**
	 * @return string
	 */
	public function getLogin()
	{
		return $this->login;
	}

	/**
	 * @param string $login
	 */
	public function setLogin($login)
	{
		$this->login = $login;
	}

	/**
	 * @return mixed
	 */
	public function getSmtpHost()
	{
		return $this->smtp_host;
	}

	/**
	 * @param mixed $smtp_host
	 */
	public function setSmtpHost($smtp_host)
	{
		$this->smtp_host = $smtp_host;
	}

	/**
	 * @return mixed
	 */
	public function getSmtpPort()
	{
		return $this->smtp_port;
	}

	/**
	 * @param mixed $smtp_port
	 */
	public function setSmtpPort($smtp_port)
	{
		$this->smtp_port = $smtp_port;
	}

	/**
	 * @return mixed
	 */
	public function getSmtpUser()
	{
		return $this->smtp_user;
	}

	/**
	 * @param mixed $smtp_user
	 */
	public function setSmtpUser($smtp_user)
	{
		$this->smtp_user = $smtp_user;
	}

	/**
	 * @return mixed
	 */
	public function getSmtpPassword()
	{
		return $this->smtp_password;
	}

	/**
	 * @param mixed $smtp_password
	 */
	public function setSmtpPassword($smtp_password)
	{
		$this->smtp_password = $smtp_password;
	}

	/**
	 * @return int
	 */
	public function getSsl()
	{
		return $this->ssl;
	}

	/**
	 * @param int $ssl
	 */
	public function setSsl($ssl)
	{
		$this->ssl = $ssl;
	}

	/**
	 * @return int
	 */
	public function getTls()
	{
		return $this->tls;
	}

	/**
	 * @param int $tls
	 */
	public function setTls($tls)
	{
		$this->tls = $tls;
	}

	/**
	 * @return int
	 */
	public function getSmtpSsl()
	{
		return $this->smtp_ssl;
	}

	/**
	 * @param int $smtp_ssl
	 */
	public function setSmtpSsl($smtp_ssl)
	{
		$this->smtp_ssl = $smtp_ssl;
	}

	/**
	 * @return int
	 */
	public function getSmtpTls()
	{
		return $this->smtp_tls;
	}

	/**
	 * @param int $smtp_tls
	 */
	public function setSmtpTls($smtp_tls)
	{
		$this->smtp_tls = $smtp_tls;
	}

}