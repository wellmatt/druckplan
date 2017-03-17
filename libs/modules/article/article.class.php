<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       23.08.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/tradegroup/tradegroup.class.php';
require_once 'libs/modules/article/article.pricescale.class.php';
require_once 'libs/modules/article/article.qusers.class.php';
require_once 'libs/modules/article/article.orderamount.class.php';
require_once 'libs/modules/article/article.shopapproval.class.php';
require_once 'libs/modules/article/article.tag.class.php';
require_once 'libs/modules/customfields/custom.field.class.php';
require_once 'libs/modules/revenueaccounts/revenueaccount.class.php';
require_once 'libs/modules/taxkeys/taxkey.class.php';



class Article {

	//Konstanten
	const ORDER_ID = "id";
	const ORDER_TITLE = "title";
	const ORDER_NUMBER = "number";

	const STATUS_INACTIVE = 0;
	const STATUS_ACTIVE = 1;

	private $id = 0;				// Einzigartige interne ID
	private $status = 1;			// Status des Artikels (0=geloescht)
	private $shoprel = 0;			// Freigabe fuer den Shop
	private $number = 0;			// Artikelnummer
	private $title;					// Titel des Artikels
	private $desc;					// Beschreibung des Artikels
	private $tradegroup;			// ID der Warengruppe
	private $seperation; 			// Preis-Staffelungen
	private $picture;				// Verweis auf das Bild, das mit dem Artikel verknuepft ist
	private $crt_date;				// Erstelldatum
	private $crt_user;				// ID des Erstellers
	private $upt_date;				// Datum, der letzten Aenderung
	private $upt_user;				// ID des Benutzers, der zuletzt bearbeitet hat
	private $taxkey = 0;			// Steuer Objekt
	private $minorder = 0;			// Minimale Bestellmenge
	private $maxorder = 0;			// Maximale Bestellmenge
	private $orderunit = 0;			// Verpackungseinheit
	private	$orderunitweight = 0;	// Gewicht der Verpackungseinheit
	private $shopCustomerRel = 0;	// Freigabe fuer einen spez. Kunden im Shop
	private $shopCustomerID = 0;	// ID des freigegebenen Kunden
	private $isworkhourart = 0;		// Ist es ein Arbeits-Stunden Artikel
	private $show_shop_price = 0;	// Freigabe fuer den Shop
	private $shop_needs_upload = 0; // Datei upload im Warenkorb
	private $matchcode;             // Artikel Matchcode
	private $shop_approval;         // Shop Freigabe für BCs und CPs
	private $tags;                  // Artikel Tags
	private $orderid = 0;           // Verknuepfte Kalk
	private $usesstorage = 0;		// Lagerartikel
	private $revenueaccount;		// Erlöskonto
	private $costobject;			// Kostenträger

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
		$this->taxkey = new TaxKey(0);
		$this->revenueaccount = new RevenueaccountCategory(0);
		$this->costobject = new CostObject(0);

		if ($id > 0){
			$valid_cache = true;
			if (Cachehandler::exists(Cachehandler::genKeyword($this,$id))){
				$cached = Cachehandler::fromCache(Cachehandler::genKeyword($this,$id));
				if (get_class($cached) == get_class($this)){
					$vars = array_keys(get_class_vars(get_class($this)));
					foreach ($vars as $var)
					{
						$method = "get".ucfirst($var);
						$method2 = $method;
						$method = str_replace("_", "", $method);
						if (method_exists($this,$method))
						{
							if(is_object($cached->$method()) === false) {
								$this->$var = $cached->$method();
							} else {
								$class = get_class($cached->$method());
								$this->$var = new $class($cached->$method()->getId());
							}
						} elseif (method_exists($this,$method2)){
							if(is_object($cached->$method2()) === false) {
								$this->$var = $cached->$method2();
							} else {
								$class = get_class($cached->$method2());
								$this->$var = new $class($cached->$method2()->getId());
							}
						} else {
							prettyPrint('Cache Error: Method "'.$method.'" not found in Class "'.get_called_class().'"');
							$valid_cache = false;
						}
					}
				} else {
					$valid_cache = false;
				}
			} else {
				$valid_cache = false;
			}
			if ($valid_cache === false) {
				$sql = "SELECT * FROM article WHERE id = {$id}";
				if ($DB->num_rows($sql)) {
					$r = $DB->select($sql);
					$r = $r[0];
					$this->id = $r["id"];
					$this->status = $r["status"];
					$this->shoprel = $r["shoprel"];
					$this->title = $r["title"];
					$this->desc = $r["description"];
					$this->picture = $r["picture"];
					$this->number = $r["number"];
					$this->taxkey = new TaxKey($r["taxkey"]);
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
					$this->orderid = $r["orderid"];
					$this->usesstorage = $r["usesstorage"];
					$this->revenueaccount = new RevenueaccountCategory($r["revenueaccount"]);
					$this->costobject = new CostObject($r["costobject"]);


					if ($r["tradegroup"] == 0) {
						$this->tradegroup->setTitle(" &ensp; ");
					}

					if ($r["crtuser"] != 0 && $r["crtuser"] != "") {
						$this->crt_user = new User($r["crtuser"]);
						$this->crt_date = $r["crtdate"];
					} else {
						$this->crt_user = 0;
						$this->crt_date = 0;
					}

					if ($r["uptuser"] != 0 && $r["uptuser"] != "") {
						$this->upt_user = new User($r["uptuser"]);
						$this->upt_date = $r["uptdate"];
					} else {
						$this->upt_user = 0;
						$this->upt_date = 0;
					}

					// Arbeiter
					$this->qualified_users = ArticleQualifiedUser::getAllForArticleAsArray($this);

					// Bestellmengen
					$this->orderamounts = ArticleOrderAmount::getAllForArticleAsArray($this);

					// Shop Freigaben
					$this->shop_approval = ArticleShopApproval::getAllForArticleAsArray($this);

					// Tags
					$this->tags = ArticleTag::getAllForArticleAsArray($this);

					Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
				}
			}

			// -- Temporary measure to assure default taxkey if none is set! //
			if ($this->taxkey->getId() == 0){
				$defaulttaxkey = TaxKey::getDefaultTaxKey();
				$this->taxkey = $defaulttaxkey; // grabbing the default taxkey just to be sure that one is set
			}
			//
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

		if($this->id > 0){
			$sql = "UPDATE article SET
					title 		= '{$this->title}',
					tradegroup 	= {$this->tradegroup->getId()},
					number		= '{$this->number}',
					description = '{$this->desc}',
					shoprel 	= {$this->shoprel},
					picture		= '{$this->picture}',
					uptuser 	= {$_USER->getId()},
					taxkey		= {$this->taxkey->getId()},
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
					matchcode		    = '{$this->matchcode}',
					usesstorage		    = {$this->usesstorage},
					orderid             = {$this->orderid},
					revenueaccount      = {$this->revenueaccount->getId()},
					costobject      = {$this->costobject->getId()}
                    WHERE id = {$this->id}";
			$res = $DB->no_result($sql);
		} else {
			$sql = "INSERT INTO article
					(status, description, title,
					tradegroup, crtdate, crtuser,
					shoprel, picture, number, taxkey,
					minorder, maxorder, orderunit,
					orderunitweight, shop_customer_rel,
					shop_customer_id, isworkhourart, show_shop_price,
					shop_needs_upload, matchcode, orderid, usesstorage, revenueaccount, costobject )
					VALUES
					({$this->status}, '{$this->desc}', '{$this->title}',
					{$this->tradegroup->getId()}, {$now}, {$_USER->getId()},
					{$this->shoprel}, '{$this->picture}', '{$this->number}', {$this->taxkey->getId()},
					{$this->minorder}, {$this->maxorder}, {$this->orderunit},
					{$this->orderunitweight}, {$this->shopCustomerRel}, {$this->shopCustomerID},
					{$this->isworkhourart}, {$this->show_shop_price}, {$this->shop_needs_upload},
					'{$this->matchcode}', {$this->orderid}, {$this->usesstorage}, {$this->revenueaccount->getId()}, {$this->costobject->getId()} )";
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
			}
		}

		$sql = "DELETE FROM article_tags
		        WHERE article = {$this->id}";
		$DB->no_result($sql);

		if (count($this->tags)>0)
		{
			foreach ($this->tags as $tag)
			{
				$sql = "INSERT INTO article_tags
		        (article, tag)
		        VALUES
		        ({$this->id}, '{$tag}')";
				$res = $DB->no_result($sql);
			}
		}

		$sql = "DELETE FROM article_shop_approval
		        WHERE article = {$this->id}";
		$DB->no_result($sql);

		if (count($this->shop_approval["BCs"]>0))
		{
			foreach($this->shop_approval["BCs"] as $shopappr)
			{
				$sql = "INSERT INTO article_shop_approval
		        (article, bc, cp)
		        VALUES
		        ({$this->id}, {$shopappr}, 0)";
				$DB->no_result($sql);
			}
		}
		if (count($this->shop_approval["CPs"]>0))
		{
			foreach($this->shop_approval["CPs"] as $shopappr)
			{
				$sql = "INSERT INTO article_shop_approval
		        (article, bc, cp)
		        VALUES
		        ({$this->id}, 0, {$shopappr})";
				$DB->no_result($sql);
			}
		}

		if ($res)
		{
			Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
			return true;
		}
		else
			return false;

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
				Cachehandler::removeCache(Cachehandler::genKeyword($this));
				unset($this);
				return true;
			} else {
				return false;
			}
		}
	}

	public static function mergePriceSeperation()
	{
		global $DB;
		$sql = "SELECT * FROM article_pricescale LIMIT 1";
		if(!$DB->num_rows($sql)){
			$sql = "SELECT * FROM article_costs";
			if($DB->num_rows($sql)){
				foreach($DB->select($sql) as $r){
					$create = [
						'article'=>$r["sep_articleid"],
						'type'=>2,
						'min'=>$r["sep_min"],
						'max'=>$r["sep_max"],
						'price'=>$r["sep_price"],
						'supplier'=>0,
						'artnum'=>''
					];
					$pricescale = new PriceScale(0,$create);
					$pricescale->save();
				}
			}

			$sql = "SELECT * FROM article_seperation";
			if($DB->num_rows($sql)){
				foreach($DB->select($sql) as $r){
					$create = [
						'article'=>$r["sep_articleid"],
						'type'=>1,
						'min'=>$r["sep_min"],
						'max'=>$r["sep_max"],
						'price'=>$r["sep_price"],
						'supplier'=>0,
						'artnum'=>''
					];
					$pricescale = new PriceScale(0,$create);
					$pricescale->save();
				}
			}
		}
	}

	public static function searchTags($term)
	{
		global $DB;
		$retval = Array();
		$sql = "SELECT DISTINCT tag FROM article_tags WHERE tag LIKE '%{$term}%'";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = Array("label" => $r["tag"], "value" => $r["tag"]);
			}
		}
		return $retval;
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
	 * Liefert alle Lagerartikel.
	 *
	 * @param string $order
	 * @return Article[]
	 */
	static function getAllArticlesNeedingStorage($order = self::ORDER_TITLE){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM article
				WHERE status > 0 AND
				usesstorage = 1
				ORDER BY {$order}";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Article($r["id"]);
			}
		}
		return $retval;
	}

	/**
	 * Suchfunktion fuer Artikel. Sucht in Titel und Auftragsnummer.
	 *
	 * @param string $search
	 * @param string $order
	 * @return Article[]
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
	 * @return Article[]
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

	static function getAllArticleIdsForApi($apiid){
		global $DB;
		$retval = Array();
		$sql = "SELECT article.id, article.uptdate
                FROM
                apis_objects
                INNER JOIN article ON apis_objects.object = article.id
                WHERE article.`status` > 0 AND apis_objects.api = {$apiid} AND apis_objects.type = 1
                ORDER BY article.id ASC";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = Array("id"=>$r["id"],"uptdate"=>$r["uptdate"]);
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

	static function getAllShopArticleByCustomerAndCp($cust_id, $cp_id, $filter = null){
		global $DB;
		$retval = Array();

		$sql = "SELECT DISTINCT id, title FROM
				(
				SELECT article.id, article.title FROM article WHERE
				status > 0 AND article.shoprel = 1
				UNION ALL
				SELECT article_shop_approval.article as id, article.title FROM article_shop_approval
				INNER JOIN article ON article_shop_approval.article = article.id
				WHERE
				(article_shop_approval.bc = {$cust_id} OR article_shop_approval.cp = {$cp_id})
				) t1
				{$filter}
				ORDER BY id ASC";

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
	 * @param int $min : Stueckzahl, von dem der Preis gilt
	 * @param int $max : Stueckzahl, bis zu der der Preis gilt
	 * @param float $price : Preis der fuer diese Staffelung gilt
	 */
	function savePrice($min, $max, $price){
		$create = [
			'article'=>$this->getId(),
			'min'=>$min,
			'max'=>$max,
			'price'=>$price,
			'type'=>1
		];
		$pricescale = new PriceScale(0, $create);
		$pricescale->save();
	}

	/**
	 * Loeschfunktion fuer alle Preisstaffelungen
	 */
	function deltePriceSeperations(){
		PriceScale::deleteAllForArticle($this,PriceScale::TYPE_SELL);
	}

	/**
	 * Funktion liefert alle Preisstaffelungen eines Artikels als Array
	 */
	public function getPrices(){
		$retval = Array();

		$pricescales = PriceScale::getAllForArticle($this,PriceScale::TYPE_SELL);
		if ($pricescales){
			foreach ($pricescales as $pricescale) {
				$retval[] = [
					'sep_articleid' => $pricescale->getArticle()->getId(),
					'sep_min' => $pricescale->getMin(),
					'sep_max' => $pricescale->getMax(),
					'sep_price' => $pricescale->getPrice()
				];
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
	 * @param int $amount
	 * @return float
	 */
	public function getPrice($amount){
		return PriceScale::getPriceForAmount($this,$amount);
	}

	/**************************************** EK-Preise ****************************************************/

	/**
	 * Funktion speichert eine EK-Preisstaffelung eines Artikels
	 *
	 * @param int $min : Stueckzahl, von dem der Preis gilt
	 * @param int $max : Stueckzahl, bis zu der der Preis gilt
	 * @param int $supplier : Lieferanten ID
	 * @param float $price : Preis der fuer diese Staffelung gilt
	 * @param string $artnum : Artikel Nummer beim Lieferanten
	 */
	function saveCost($min, $max, $price, $supplier, $artnum){
		$create = [
			'article'=>$this->getId(),
			'min'=>$min,
			'max'=>$max,
			'price'=>$price,
			'type'=>2,
			'supplier'=>$supplier,
			'artnum'=>$artnum
		];
		$pricescale = new PriceScale(0, $create);
		$pricescale->save();
	}

	/**
	 * Loeschfunktion fuer alle EK-Preisstaffelungen
	 */
	function delteCostSeperations(){
		PriceScale::deleteAllForArticle($this,PriceScale::TYPE_BUY);
	}

	/**
	 * Funktion liefert alle EK-Preisstaffelungen eines Artikels als Array
	 */
	public function getCosts(){
		$retval = Array();

		$pricescales = PriceScale::getAllForArticle($this,PriceScale::TYPE_BUY);
		if ($pricescales){
			foreach ($pricescales as $pricescale) {
				$retval[] = [
					'sep_articleid' => $pricescale->getArticle()->getId(),
					'sep_min' => $pricescale->getMin(),
					'sep_max' => $pricescale->getMax(),
					'sep_price' => $pricescale->getPrice(),
					'supplier' => $pricescale->getSupplier()->getId(),
					'supplier_artnum' => $pricescale->getArtnum()
				];
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
	 * @param int $amount
	 * @return float
	 */
	public function getCost($amount){
		return PriceScale::getPriceForAmount($this,$amount,PriceScale::TYPE_BUY);
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
	public function getCrtdate()
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
	public function getCrtuser()
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
	public function getUptdate()
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
	public function getUptuser()
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
		return $this->taxkey->getValue();
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
	 * @return array $orderamounts
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
	public function getQualifiedusers()
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
	public function getShopneedsupload()
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
	/**
	 * @return the $shop_approval
	 */
	public function getShop_approval()
	{
		return $this->shop_approval;
	}
	public function getShopapproval()
	{
		return $this->shop_approval;
	}

	/**
	 * @param field_type $shop_approval
	 */
	public function setShop_approval($shop_approval)
	{
		$this->shop_approval = $shop_approval;
	}

	/**
	 * @return the $tags
	 */
	public function getTags()
	{
		return $this->tags;
	}

	/**
	 * @param Ambigous <multitype:unknown , multitype:Ambigous <multitype:> > $tags
	 */
	public function setTags($tags)
	{
		$this->tags = $tags;
	}

	/**
	 * @return the $orderid
	 */
	public function getOrderid()
	{
		return $this->orderid;
	}

	/**
	 * @param number $orderid
	 */
	public function setOrderid($orderid)
	{
		$this->orderid = $orderid;
	}

	/**
	 * @return int
	 */
	public function getUsesstorage()
	{
		return $this->usesstorage;
	}

	/**
	 * @param int $usesstorage
	 */
	public function setUsesstorage($usesstorage)
	{
		$this->usesstorage = $usesstorage;
	}

	/**
	 * @return RevenueaccountCategory
	 */
	public function getRevenueaccount()
	{
		return $this->revenueaccount;
	}

	/**
	 * @param RevenueaccountCategory $revenueaccount
	 */
	public function setRevenueaccount($revenueaccount)
	{
		$this->revenueaccount = $revenueaccount;
	}

	/**
	 * @return TaxKey
	 */
	public function getTaxkey()
	{
		return $this->taxkey;
	}

	/**
	 * @param TaxKey $taxkey
	 */
	public function setTaxkey($taxkey)
	{
		$this->taxkey = $taxkey;
	}

	/**
	 * @return CostObject
	 */
	public function getCostobject()
	{
		return $this->costobject;
	}

	/**
	 * @param CostObject $costobject
	 */
	public function setCostobject($costobject)
	{
		$this->costobject = $costobject;
	}
}