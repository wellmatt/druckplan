<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       23.08.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/tradegroup/tradegroup.class.php';

class Article {

	//Konstanten
	const ORDER_ID = "id";
	const ORDER_TITLE = "title";
	const ORDER_NUMBER = "number";

	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;

	private $id = 0;				//Einzigartige interne ID
	private $status = 1;			// Status des Artikels (0=geloescht)
	private $shoprel = 0;			// Freigabe fuer den Shop
	private $number = 0;			// Artikelnummer
	private $title;					// Titel des Artikels
	private $desc;					// Beschreibung des Artikels
	private $tradegroup;			// ID der Warengruppe			
	private $seperation; 			// Preis-Staffelungen
	private $prices;				// VK-Preise
	private $costs;					// EK-Preise
	private $picture;				// Verweis auf das Bild, das mit dem Artikel verknuepft ist
	private $crt_date;				// Erstelldatum
	private $crt_user;				// ID des Erstellers
	private $upt_date;				// Datum, der letzten Aenderung
	private $upt_user;				// ID des Benutzers, der zuletzt bearbeitet hat
	private $tax = 19;				// Steuern
	private $minorder;				// Minimale Bestellmenge
	private $maxorder;				// Maximale Bestellmenge
	private $orderunit;				// Verpackungseinheit
	private	$orderunitweight;		// Gewicht der Verpackungseinheit
	private $shopCustomerRel;		// Freigabe fuer einen spez. Kunden im Shop
	private $shopCustomerID;		// ID des freigegebenen Kunden
	private $isworkhourart;			// Ist es ein Arbeits-Stunden Artikel
	private $show_shop_price = 1;	// Freigabe fuer den Shop
	private $shop_needs_upload;     // Datei upload im Warenkorb
	private $matchcode;             // Artikel Matchcode
	
	private $orderamounts = Array();// Falls keine manuellen Bestellmengen erwünscht befinden sich hier die möglichen Bestellmengen
	
	private $qualified_users = Array();

	/**
	 * Konstruktor eines Artikels, falls id>0 wird der entsprechende Artikel aus der DB geholt
	 *
	 * @param int $id
	 */
	function __construct($id = 0){
		global $DB;
		global $_USER;
		$this->tradegroup = new Tradegroup(0);

		$cached = Cachehandler::fromCache("obj_article_" . $id);
		if (!is_null($cached))
		{
		    $this->id = $cached->getId();
		    $this->status = $cached->getStatus();
		    $this->shoprel = $cached->getShoprel();
		    $this->title = $cached->getTitle();
		    $this->desc = $cached->getDesc();
		    $this->picture = $cached->getPicture();
		    $this->number = $cached->getNumber();
		    $this->tax = $cached->getTax();
		    $this->minorder = $cached->getMinorder();
		    $this->maxorder = $cached->getMaxorder();
		    $this->orderunit = $cached->getOrderunit();
		    $this->orderunitweight = $cached->getOrderunitweight();
		    $this->tradegroup = $cached->getTradegroup();
		    $this->shopCustomerID = $cached->getShopCustomerID();
		    $this->shopCustomerRel = $cached->getShopCustomerRel();
		    $this->isworkhourart = $cached->getIsWorkHourArt();
		    $this->show_shop_price = $cached->getShowShopPrice();
		    $this->shop_needs_upload = $cached->getShop_needs_upload();
		    $this->crt_user = $cached->getCrt_user();
		    $this->crt_date = $cached->getCrt_date();
		    $this->upt_user = $cached->getUpt_user();
		    $this->upt_date = $cached->getUpt_date();
		    $this->qualified_users = $cached->getQualified_users();
		    $this->orderamounts = $cached->getOrderamounts();
		    $this->matchcode = $cached->getMatchcode();
// 		    echo "Object loaded from Cache...</br>";
		}
		
		if ($id > 0 && is_null($cached)){
			$sql = "SELECT * FROM article WHERE id = {$id}";
			if($DB->num_rows($sql)){
				$r = $DB->select($sql);
				$r = $r[0];
				$this->id = $r["id"];
				$this->status = $r["status"];
				$this->shoprel = $r["shoprel"];
				$this->title = $r["title"];
				$this->desc = $r["description"];
				$this->picture = $r["picture"];
				$this->number = $r["number"];
				$this->tax = $r["tax"];
				$this->minorder = $r["minorder"];
				$this->maxorder = $r["maxorder"];
				$this->orderunit = $r["orderunit"];
				$this->orderunitweight = $r["orderunitweight"];
				$this->tradegroup = new Tradegroup($r["tradegroup"]);
				$this->shopCustomerID = $r["shop_customer_id"];
				$this->shopCustomerRel = $r["shop_customer_rel"];
				$this->isworkhourart = $r["isworkhourart"];
				$this->show_shop_price = $r["show_shop_price"];
				$this->shop_needs_upload = $r["shop_needs_upload"];
				$this->matchcode = $r["matchcode"];
				
				if ($r["tradegroup"] == 0){
					$this->tradegroup->setTitle(" &ensp; ");
				}
				
				if ($r["crtuser"] != 0 && $r["crtuser"] != "" ){
					$this->crt_user = new User($r["crtuser"]);
					$this->crt_date = $r["crtdate"];
				} else {
					$this->crt_user = 0;
					$this->crt_date = 0;
				}
				
				if ($r["uptuser"] != 0 && $r["uptuser"] != "" ){
					$this->upt_user = new User($r["uptuser"]);
					$this->upt_date = $r["uptdate"];
				} else {
					$this->upt_user = 0;
					$this->upt_date = 0;
				}
				
				// Arbeiter
				$tmp_qusrs = Array();
				$sql = "SELECT * FROM article_qualified_users WHERE article = {$this->id}";
				if($DB->num_rows($sql))
				{
				    foreach($DB->select($sql) as $r)
				    {
				        $tmp_qusrs[] = new User((int)$r["user"]);	//gln
				    }
				}
				$this->qualified_users = $tmp_qusrs;
				
				$sql = "SELECT * FROM article_orderamounts WHERE article_id = {$id}";
				if($DB->num_rows($sql)){
				    $retval = Array();
				    foreach($DB->select($sql) as $r){
				    	$retval[] = $r["amount"];
				    }
				    $this->orderamounts = $retval;
				}
			    Cachehandler::toCache("obj_article_".$id, $this);
			}
		}
	}

	/**
	 * Speicher-Funktion fuer Artikel
	 * 
	 * @return boolean
	 */
	function save(){
		global $DB;
		global $_USER;
		$now = time();
		$groupid = $this->tradegroup->getId();
		
		if($this->id > 0){
			$sql = "UPDATE article SET
					title 		= '{$this->title}',  
					tradegroup 	= {$groupid}, 
					number		= '{$this->number}',  
					description = '{$this->desc}', 
					shoprel 	= {$this->shoprel}, 
					picture		= '{$this->picture}', 
					uptuser 	= {$_USER->getId()}, 
					tax			= {$this->tax}, 
					uptdate 	= UNIX_TIMESTAMP(), 
					minorder 	= {$this->minorder}, 
					maxorder 	= {$this->maxorder}, 
					orderunit 	= {$this->orderunit}, 
					orderunitweight 	= {$this->orderunitweight}, 
					shop_customer_rel	= {$this->shopCustomerRel}, 
					shop_customer_id	= {$this->shopCustomerID},
					show_shop_price		= {$this->show_shop_price},
					shop_needs_upload	= {$this->shop_needs_upload},
					isworkhourart		= {$this->isworkhourart},
					matchcode		    = '{$this->matchcode}' 
                    WHERE id = {$this->id}";
			$res = $DB->no_result($sql);
		} else {
			$sql = "INSERT INTO article 
					(status, description, title, 
					tradegroup, crtdate, crtuser, 
					shoprel, picture, number, tax, 
					minorder, maxorder, orderunit,  
					orderunitweight, shop_customer_rel, shop_customer_id, isworkhourart, show_shop_price, shop_needs_upload, matchcode )
					VALUES
					({$this->status}, '{$this->desc}', '{$this->title}',  
					{$groupid}, {$now}, {$_USER->getId()}, 
					{$this->shoprel}, '{$this->picture}', '{$this->number}', {$this->tax}, 
					{$this->minorder}, {$this->maxorder}, {$this->orderunit}, 
					{$this->orderunitweight}, {$this->shopCustomerRel}, {$this->shopCustomerID}, 
					{$this->isworkhourart}, {$this->show_shop_price}, {$this->shop_needs_upload}, '{$this->matchcode}' )";
			$res = $DB->no_result($sql);
            
            if($res){
                $sql = "SELECT max(id) id FROM article WHERE title = '{$this->title}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                $res = true;
            } else {
                $res = false;
            }
		}

		$sql = "DELETE FROM article_qualified_users WHERE article = {$this->id}";
		$DB->no_result($sql);
		
		foreach($this->qualified_users as $qusr)
		{
		    $sql = "INSERT INTO article_qualified_users
		    (article, user)
		    VALUES
		    ({$this->id}, {$qusr->getId()})";
		    $DB->no_result($sql);
		}
		
		$sql = "DELETE FROM article_orderamounts 
		        WHERE article_id = {$this->id}";
		$DB->no_result($sql);
		
		if (count($this->orderamounts)>0)
		{
		    foreach ($this->orderamounts as $orderamount)
		    {
		        $sql = "INSERT INTO article_orderamounts
		        (article_id, amount)
		        VALUES
		        ({$this->id}, {$orderamount})";
		        $res = $DB->no_result($sql);
// 		        echo $sql;
		    }
		}

		Cachehandler::toCache("obj_article_".$this->id, $this);
		return $res;
		
	}
	
	/**
	 * Loeschfunktion fuer Artikel.
	 * Der Artikel wird nicht entgueltig geloescht, der Status und die Freigabe wird auf 0 gesetzt
	 * 
	 * @return boolean
	 */
	public function delete(){
		global $DB;
		if($this->id > 0){
			$sql = "UPDATE article 
					SET
					shoprel = 0,
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
	 * Ueberpruefung, ob eine eingegebene Artikelnummer schon vergeben ist.
	 * 
	 * @param String $number
	 * @return boolean : true, wenn vergeben
	 */
	static function checkArticleNumber($number){
		global $DB;

		$sql = "SELECT id FROM article WHERE status > 0 AND number = '{$number}'";
		if($DB->select($sql)){
			return true;
		}
		return false;
	}
	
	/**
     * Suchfunktion fuer Artikel. Sucht in Titel und Auftragsnummer.
	 * 
	 * @param STING $str
	 * @return Array : Article
	 */
	static function searchArticleByTitleNumber($search, $order = self::ORDER_NUMBER){
		global $DB;
		$retval = Array();
		$sql = "SELECT id, number, status, title FROM article
				WHERE status > 0 AND
				(number like '%{$search}%'
				OR title like '%{$search}%')
				ORDER BY {$order}";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Article($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
     * Suchfunktion für Artikel. Sucht in Titel und Auftragsnummer in festgelegter Warengruppe.
	 * 
	 * @param STING $str
	 * @return Array : Article
	 */
	static function searchArticleByTitleNumberByGroup($search, $tg_id, $order = self::ORDER_NUMBER){
		global $DB;
		$retval = Array();
		$sql = "SELECT id, number, status, title FROM article
				WHERE status > 0 AND
				(number like '%{$search}%'
				OR title like '%{$search}%')
				AND tradegroup = {$tg_id}
				ORDER BY {$order}";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Article($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Funktion liefert alle aktiven Artikel der Datenbank nach angegebener Reighenfolge
	 *
	 * @param STRING $order Reihenfolge, in der die Artikel sortiert werden
	 * @return Array : Article
	 */
	static function getAllArticle($order = self::ORDER_NUMBER, $filter = ""){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM article WHERE status > 0 {$filter} ORDER BY {$order}";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Article($r["id"]);
			}
		}
		return $retval;
	}
	
	
	/**
	 * Funktion liefert alle aktiven Arbeitszeit-Artikel der Datenbank nach angegebener Reighenfolge
	 *
	 * @param STRING $order Reihenfolge, in der die Artikel sortiert werden
	 * @return Array : Article
	 */
	static function getAllWorkHourArticle($order = self::ORDER_NUMBER){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM article WHERE status > 0 AND isworkhourart = 1 ORDER BY {$order}";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Article($r["id"]);
			}
		}
		return $retval;
	}
	
	
	/**
	 * Funktion liefert alle aktiven Artikel der angebenen Warengruppe
	 *
	 * @param int $tg_id ID der Warengruppe, zu der die Artikel gescuht werden
	 * @return Array : Article
	 */
	static function getAllArticleByGroup($tg_id){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM article WHERE status > 0 AND tradegroup = {$tg_id}";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Article($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Funktion liefert alle aktiven Artikel der angebenen Warengruppe
	 *
	 * @param int $tg_id ID der Warengruppe, zu der die Artikel gescuht werden
	 * @param STRING $order Reihenfolge, in der die Artikel sortiert werden
	 * @return Array : Article
	 */
	static function getAllShopArticleByGroup($tg_id, $order = self::ORDER_ID){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM article WHERE 
				status > 0 AND 
				shoprel = 1 AND
				tradegroup = {$tg_id}
				ORDER BY {$order}";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Article($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Funktion liefert alle aktiven Artikel, die fuer den Benutzer freigegeben sind
	 *
	 * @param int $cust_id : ID des Gestaeftskontakts
	 * @param STRING $order : Reihenfolge, in der die Artikel geliefert werden
	 * @return Array : Article
	 */
	static function getAllShopArticleByCustomer($cust_id, $order = self::ORDER_ID){
		global $DB;
		$retval = Array();
		
		$sql = "SELECT id FROM article WHERE
				status > 0 AND
				( shoprel = 1 
				  OR 
				  (shop_customer_id = {$cust_id} AND shop_customer_rel = 1 )
				)
				ORDER BY {$order} ";
		
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Article($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Funktion liefert alle aktiven Artikel mit angegebenen Suchstring
	 *
	 * @param STRING $search_str Teilstring, nachdem gesucht werden soll
	 * @param STRING $order Reihenfolge, in der die Artikel sortiert werden
	 * @return Array : Article
	 */
	static function getAllShopArticleSearch($search_str, $order = self::ORDER_ID){
		global $DB;
		$retval = Array();
		//TODO String in teile aufteilen falls nach mehreren Worten gesucht wird
		$sql = "SELECT id FROM article WHERE
				status > 0 AND
				shoprel = 1 AND
				(title LIKE '%{$search_str}%' OR
				 desc LIKE '%{$search_str}%' ) 
				ORDER BY {$order}";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Article($r["id"]);
			}
		}
		echo mysql_error();
		return $retval;
	}
	
	/******************************* VK-Preise ***************************************************/

	/**
	 * Funktion speichert eine Preisstaffelung eines Artikels
	 *
	 * @param int $min : St�ckzahl, von dem der Preis gilt
	 * @param int $max : St�ckzahl, bis zu der der Preis gilt
	 * @param float $price : Preis der f�r diese Staffelung gilt
	 */
	function savePrice($min, $max, $price){
		global $DB;
		$sql = "INSERT INTO article_seperation
				(sep_articleid, sep_min, sep_max, sep_price)
				VALUES
				({$this->id}, {$min}, {$max}, {$price})";
		$DB->no_result($sql);
	}
	
	/**
	* Loeschfunktion fuer alle Preisstaffelungen
	*/
	function deltePriceSeperations(){
		global $DB;
			$sql = "DELETE FROM article_seperation
			WHERE sep_articleid = {$this->id}";
		$DB->no_result($sql);
		//echo mysql_error();
	}
	
	/**
	 * Funktion liefert alle Preisstaffelungen eines Artikels als Array
	 */
	public function getPrices(){
		global $DB;
		$retval = Array();
		$sql = "SELECT * FROM article_seperation WHERE sep_articleid = {$this->id} ORDER BY sep_min";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = $r;
			}
		}
		return $retval;
	}
	
	public function setPrices($prices)
	{
		$this->prices = $prices;
	}
	
	/**
	 * Funktion liefert einen Preis zu einer bestimmten Menge
	 */
	public function getPrice($amount){
		global $DB;
		$sql = "SELECT * FROM article_seperation WHERE 
				sep_articleid = ".$this->id." AND
				sep_min <= ".$amount." AND
				".$amount." <= sep_max 
				ORDER BY sep_min";
		if($DB->num_rows($sql)){
			$res = $DB->select($sql);
			$retval = $res[0]["sep_price"];
		} else {
			// Wenn kein Wert zwischen MIN und MAX, nehme den groessten der zugehoerigen Preise
			$sql = "SELECT * FROM article_seperation
					WHERE sep_articleid = ".$this->id."
					ORDER BY sep_max DESC LIMIT 0, 1";
			$res = $DB->select($sql);
			return $res[0]["sep_price"];
		}
		return $retval;
	}
	
	/**************************************** EK-Preise ****************************************************/
	
	/**
	 * Funktion speichert eine EK-Preisstaffelung eines Artikels
	 *
	 * @param int $min : Stueckzahl, von dem der Preis gilt
	 * @param int $max : Stueckzahl, bis zu der der Preis gilt
	 * @param float $price : Preis der f�r diese Staffelung gilt
	 */
	function saveCost($min, $max, $price){
		global $DB;
		$sql = "INSERT INTO article_costs
				(sep_articleid, sep_min, sep_max, sep_price)
				VALUES
				({$this->id}, {$min}, {$max}, {$price})";
		$DB->no_result($sql);
	}

	/**
		* Loeschfunktion fuer alle EK-Preisstaffelungen
		*/
	function delteCostSeperations(){
		global $DB;
		$sql = "DELETE FROM article_costs
				WHERE sep_articleid = {$this->id}";
		$DB->no_result($sql);
		//echo mysql_error();
	}

	/**
	 * Funktion liefert alle EK-Preisstaffelungen eines Artikels als Array
	 */
	public function getCosts(){
		global $DB;
		$retval = Array();
		$sql = "SELECT * FROM article_costs WHERE sep_articleid = {$this->id} ORDER BY sep_min";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = $r;
			}
		}
		return $retval;
	}

	public function setCosts($costs)
	{
		$this->costs = $costs;
	}

	/**
	 * Funktion liefert einen EK-Preis zu einer bestimmten Menge
	 */
	public function getCost($amount){
		global $DB;
		$sql = "SELECT * FROM article_costs 
				WHERE
				sep_articleid = ".$this->id." AND
				sep_min <= ".$amount." AND
				".$amount." <= sep_max
				ORDER BY sep_min";
		if($DB->num_rows($sql)){
			$res = $DB->select($sql);
			$retval = $res[0]["sep_price"];
		} else {
			// Wenn kein Wert zwischen MIN und MAX, nehme den groessten der zugehoerigen Preise
			$sql = "SELECT * FROM article_costs
					WHERE 
					sep_articleid = ".$this->id."
					ORDER BY sep_max DESC LIMIT 0, 1";
			$res = $DB->select($sql);
			return $res[0]["sep_price"];
		}
		return $retval;
	}
	
	/*********************************** Artikelbilder ************************************************/
	
	/**
	 * ... liefert alle Bilder eines Artikels
	 * 
	 * @return Array
	 */
	public function getAllPictures(){
		global $DB;
		$retval=FALSE;
		
		$sql = "SELECT * FROM article_pictures WHERE articleid = {$this->id} ORDER BY id ASC ";
		
		if($DB->num_rows($sql)){
			$retval = $DB->select($sql);
		} 
		return $retval;
	}
	
	/**
	 * ... liefert die Details zu einem Bild als Array
	 *  
	 * @param int $pic_id
	 * @return Array 
	 */
	public function getPictureUrl($pic_id){
		global $DB;
		$retval=FALSE;
		
		$sql = "SELECT * FROM article_pictures WHERE articleid = {$this->id} AND id = {$pic_id}";
		
		if($DB->num_rows($sql)){
			$retval = $DB->select($sql);
			$retval = $retval[0];
		}
		return $retval;
	}
	
	/**
	 * Loescht das angegeben Artikelbild
	 * 
	 * @param int $picid
	 * @return boolean
	 */
	public function deletePicture($picid){
		global $DB;
		$sql = "DELETE FROM article_pictures WHERE id = {$picid}";
		return $DB->no_result($sql);
	}
	
	/**
	 * ... speichert ein Artikelbild 
	 *
	 * @param string $picurl
	 * @return boolean
	 */
	public function addPicture($picurl){
		global $DB;
		$now = time();
		
		$sql = "INSERT INTO article_pictures 
				( url, crtdate, articleid )
				VALUES
				( '{$picurl}', $now, {$this->id} )";
		return $DB->no_result($sql);
	}
	
	
	/**
	 * Loeschfunktion fuer die Id
	 */
	function clearId(){
		$this->id = 0;
	}
	
	/*********************************** GETTER u. SETTER *********************************************/

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

	public function getShoprel()
	{
	    return $this->shoprel;
	}

	public function setShoprel($shoprel)
	{
	    $this->shoprel = $shoprel;
	}

	public function getTitle()
	{
	    return $this->title;
	}

	public function setTitle($title)
	{
	    $this->title = $title;
	}

	public function getDesc()
	{
	    return $this->desc;
	}

	public function setDesc($desc)
	{
	    $this->desc = $desc;
	}

	public function getTradegroup()
	{
	    return $this->tradegroup;
	}

	public function setTradegroup($tradegroup)
	{
	    $this->tradegroup = $tradegroup;
	}

	public function getSeperation()
	{
	    return $this->seperation;
	}

	public function setSeperation($seperation)
	{
	    $this->seperation = $seperation;
	}
	
	public function getPicture()
	{
	    return $this->picture;
	}

	public function setPicture($picture)
	{
	    $this->picture = $picture;
	}

	public function getCrt_date()
	{
	    return $this->crt_date;
	}

	public function setCrt_date($crt_date)
	{
	    $this->crt_date = $crt_date;
	}

	public function getCrt_user()
	{
	    return $this->crt_user;
	}

	public function setCrt_user($crt_user)
	{
	    $this->crt_user = $crt_user;
	}

	public function getUpt_date()
	{
	    return $this->upt_date;
	}

	public function setUpt_date($upt_date)
	{
	    $this->upt_date = $upt_date;
	}

	public function getUpt_user()
	{
	    return $this->upt_user;
	}

	public function setUpt_user($upt_user)
	{
	    $this->upt_user = $upt_user;
	}

	public function getNumber()
	{
	    return $this->number;
	}

	public function setNumber($number)
	{
	    $this->number = $number;
	}

    public function getTax()
    {
        return $this->tax;
    }

    public function setTax($tax)
    {
        $this->tax = $tax;
    }

	public function getMinorder()
	{
	    return $this->minorder;
	}

	public function setMinorder($minorder)
	{
	    $this->minorder = $minorder;
	}

	public function getMaxorder()
	{
	    return $this->maxorder;
	}

	public function setMaxorder($maxorder)
	{
	    $this->maxorder = $maxorder;
	}

	public function getOrderunit()
	{
	    return $this->orderunit;
	}

	public function setOrderunit($orderunit)
	{
	    $this->orderunit = $orderunit;
	}

	public function getOrderunitweight()
	{
	    return $this->orderunitweight;
	}

	public function setOrderunitweight($orderunitweight)
	{
	    $this->orderunitweight = $orderunitweight;
	}

    public function getShopCustomerRel()
    {
        return $this->shopCustomerRel;
    }

    public function setShopCustomerRel($shopCustomerRel)
    {
        $this->shopCustomerRel = $shopCustomerRel;
    }

    public function getShopCustomerID()
    {
        return $this->shopCustomerID;
    }

    public function setShopCustomerID($shopCustomerID)
    {
        $this->shopCustomerID = $shopCustomerID;
    }

    public function getIsWorkHourArt()
    {
        return $this->isworkhourart;
    }

    public function setIsWorkHourArt($isworkhourart)
    {
        $this->isworkhourart = $isworkhourart;
    }

    public function getShowShopPrice()
    {
        return $this->show_shop_price;
    }

    public function setShowShopPrice($show_shop_price)
    {
        $this->show_shop_price = $show_shop_price;
    }
    
	/**
     * @return the $orderamounts
     */
    public function getOrderamounts()
    {
        return $this->orderamounts;
    }

	/**
     * @param multitype: $orderamounts
     */
    public function setOrderamounts($orderamounts)
    {
        $this->orderamounts = $orderamounts;
    }
    
	/**
     * @return the $qualified_users
     */
    public function getQualified_users()
    {
        return $this->qualified_users;
    }

	/**
     * @param multitype: $qualified_users
     */
    public function setQualified_users($qualified_users)
    {
        $this->qualified_users = $qualified_users;
    }
    
	/**
     * @return the $shop_needs_upload
     */
    public function getShop_needs_upload()
    {
        return $this->shop_needs_upload;
    }

	/**
     * @param field_type $shop_needs_upload
     */
    public function setShop_needs_upload($shop_needs_upload)
    {
        $this->shop_needs_upload = $shop_needs_upload;
    }
    
	/**
     * @return the $matchcode
     */
    public function getMatchcode()
    {
        return $this->matchcode;
    }

	/**
     * @param field_type $matchcode
     */
    public function setMatchcode($matchcode)
    {
        $this->matchcode = $matchcode;
    }
    
}
?>