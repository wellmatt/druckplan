<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       04.07.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';

class Personalization {

	//Konstante
	const ORDER_TITLE		= " title ";
	const ORDER_CUSTOMER	= " customer ";
	const ORDER_CRTDATE		= " crtdate ";
	
	const TYPE_HAS_NO_REVERT	= 1;
	const TYPE_HAS_REVERT		= 1;

	private $id = 0;			// ID
	private $status = 1;		// Status (0=geloescht)
	private $title;				// Titel/Bezeichnung 
	private $comment;			// Kommentar
	private $article;			// Artikel der Verknuepft ist
	private $picture;			// Verweis auf das Bild (Vorderseite)
	private $picture2;			// Verweis auf das Bild (Rückseite)
	private $crtdate;			// Erstelldatum
	private $crtuser;			// ID des Erstellers
	private $uptdate;			// Datum, der letzten Aenderung
	private $uptuser;			// ID des Benutzers, der zuletzt bearbeitet hat
	private $fields = Array(); 	// Array mit Feldern
	private $customer;			// Kunde fuer den diese Personalisierung ist
	private $direction = 0;		// Ausrichtung 0=senkrecht, 1=waagerecht
	private $format = "A4";		// Format (durch Hoehe und Breite ersetzt)
	private $formatwidth;		// Breite der Personalisierung
	private $formatheight;		// Hoehe der Personalisierung
	private $type = 0;			// Type wird nun fuer ein- bzw. zweiseitig benutzt (ab 10.03.2014)
	private $linebyline = 0;	// Fix Positions links und rechts, rest wird darunter Zeile für Zeile eingerückt
	private $hidden = 0;        // Perso im Shop versteckt?
	private $anschnitt = 0;		// Anschnitt in mm
	private $preview = '';		// Vorschaubild

	/**
	 * Konstruktor einer Personalisierung, falls id > 0 wird die entsprechende Personalisierung aus der DB geholt
	 *
	 * @param int $id
	 */
	function __construct($id = 0){
		global $DB;
		global $_USER;

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
							if(is_object($cached->{$method}()) === false) {
								$this->{$var} = $cached->{$method}();
							} else {
								$class = get_class($cached->{$method}());
								$this->{$var} = new $class($cached->{$method}()->getId());
							}
						} elseif (method_exists($this,$method2)){
							if(is_object($cached->{$method2}()) === false) {
								$this->{$var} = $cached->{$method2}();
							} else {
								$class = get_class($cached->{$method2}());
								$this->{$var} = new $class($cached->{$method2}()->getId());
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
			if ($valid_cache === false)
			{
				$sql = "SELECT * FROM personalization WHERE id = {$id}";
				if($DB->num_rows($sql)){
					$r = $DB->select($sql);
					$r = $r[0];
					$this->id = $r["id"];
					$this->status = $r["status"];
					$this->title = $r["title"];
					$this->comment = $r["comment"];
					$this->picture = $r["picture"];
					$this->picture2 = $r["picture2"];
					$this->article = new Article($r["article"]);
					$this->customer = new BusinessContact($r["customer"]);
					$this->direction = $r["direction"];
					$this->format = $r["format"];
					$this->formatwidth = $r["format_width"];
					$this->formatheight = $r["format_height"];
					$this->type = $r["type"];
					$this->linebyline = $r["linebyline"];
					$this->hidden = $r["hidden"];
					$this->anschnitt = $r["anschnitt"];
					$this->preview = $r["preview"];

					if ($r["crtuser"] != 0 && $r["crtuser"] != "" ){
						$this->crt_user = new User($r["crtuser"]);
						$this->crt_date = $r["crtdate"];
					} else {
						$this->crt_user = new User(0);
						$this->crt_date = 0;
					}

					if ($r["uptuser"] != 0 && $r["uptuser"] != "" ){
						$this->upt_user = new User($r["uptuser"]);
						$this->upt_date = $r["uptdate"];
					} else {
						$this->upt_user = new User(0);
						$this->upt_date = 0;
					}
					Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
				}
			}
		} else {
			$this->customer = new BusinessContact(0);
			$this->article = new Article();
		}
	}

	/**
	 * Speicher-Funktion fuer eine Personalisierung
	 * 
	 * @return boolean
	 */
	function save(){
		global $DB;
		global $_USER;
		$now = time();
		
		if($this->id > 0){
			$sql = "UPDATE personalization SET
					title 	= '{$this->title}', 
					comment = '{$this->comment}', 
					picture	= '{$this->picture}',
					picture2	= '{$this->picture2}',
					uptuser = {$_USER->getId()},
					uptdate = {$now},
					article = {$this->article->getId()},
					picture = '{$this->picture}',
					format = '{$this->format}',
					customer = {$this->customer->getId()},
					direction = {$this->direction}, 
					type = {$this->type}, 
					format_width = {$this->formatwidth}, 
					format_height = {$this->formatheight},
					hidden = {$this->hidden},
					anschnitt = {$this->anschnitt},
					preview = '{$this->preview}',
					linebyline = {$this->linebyline} 
                    WHERE id = {$this->id}";

			Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
			return $DB->no_result($sql);
		} else {
			$sql = "INSERT INTO personalization 
					(status, comment, title, type, 
					crtdate, crtuser, article, format,  
					customer, picture, direction,
					format_width, format_height, picture2, linebyline, hidden, anschnitt, preview )
					VALUES
					({$this->status}, '{$this->comment}', '{$this->title}', {$this->type},  
					 {$now}, {$_USER->getId()}, {$this->article->getId()}, '{$this->format}', 
					 {$this->customer->getId()}, '{$this->picture}', {$this->direction},  
					 {$this->formatwidth}, {$this->formatheight}, '{$this->picture2}',
					 {$this->linebyline}, {$this->hidden}, {$this->anschnitt}, '{$this->preview}' )";
			$res = $DB->no_result($sql);
            
            if($res){
                $sql = "SELECT max(id) id FROM personalization WHERE title = '{$this->title}' ";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
				Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
                return true;
            } else {
                return false;
            }
		}
	}
	
	/**
	 * Loeschfunktion fuer Personalisierungen
	 * 
	 * @return boolean
	 */
	public function delete(){
		global $DB;
		if($this->id > 0){
			$sql = "UPDATE personalization
					SET
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
	
	static function getAllCustomerWithPersos(){
		global $DB;
		$retval = Array();
		$sql = "SELECT DISTINCT personalization.customer FROM personalization WHERE status > 0";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new BusinessContact($r["customer"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Funktion liefert alle aktiven Personalisierugnen nach angegebener Reighenfolge
	 *
	 * @param STRING $order Reihenfolge, in der die Artikel sortiert werden
	 * @return Array : Article
	 */
	static function getAllPersonalizations($order = self::ORDER_TITLE){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM personalization WHERE status > 0 ORDER BY {$order}";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Personalization($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Funktion liefert alle aktiven Personalisierugnen nach angegebener Reighenfolge
	 *
	 * @param STRING $order Reihenfolge, in der die Artikel sortiert werden
	 * @return Array : Article
	 */
	static function getAllPersonalizationsSearch($order = self::ORDER_TITLE, $search_string){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM personalization WHERE status > 0 AND title LIKE '%{$search_string}%' ORDER BY {$order}";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Personalization($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Funktion liefert alle aktiven Personalisierugnen nach angegebener Reighenfolge
	 *
	 * @param STRING $order Reihenfolge, in der die Artikel sortiert werden
	 * @return Array : Article
	 */
	static function getAllPersonalizationsByCustomer($custId, $order = self::ORDER_TITLE){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM personalization WHERE status > 0 AND customer = {$custId} ORDER BY {$order}";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Personalization($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Funktion liefert alle aktiven Personalisierugnen nach angegebener Reighenfolge
	 *
	 * @param STRING $order Reihenfolge, in der die Artikel sortiert werden
	 * @return Array : Article
	 */
	static function getAllPersonalizationsByCustomerSearch($custId, $order = self::ORDER_TITLE, $search_string){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM personalization WHERE status > 0 AND customer = {$custId} AND title LIKE '%{$search_string}%' AND hidden = 0 ORDER BY {$order}";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Personalization($r["id"]);
			}
		}
		return $retval;
	}
	
	/**
	 * Funktion speichert eine Preisstaffelung einer Personalisierung
	 *
	 * @param int $min : St�ckzahl, von dem der Preis gilt
	 * @param int $max : St�ckzahl, bis zu der der Preis gilt
	 * @param float $price : Preis der f�r diese Staffelung gilt
	 */
	function savePrice($min, $max, $price, $show){
		// Funktion leicht abgewandelt, Max ist nun die genaue Stueckzahl 		
		global $DB;
		$sql = "INSERT INTO personalization_seperation
				(sep_personalizationid, sep_min, sep_max, sep_price, sep_show)
				VALUES
				({$this->id}, 0, {$max}, {$price}, {$show} )";
		$DB->no_result($sql);
	}
	
	/**
	* Loeschfunktion fuer alle Preisstaffelungen
	*/
	function deltePriceSeperations(){
	global $DB;
		$sql = "DELETE FROM personalization_seperation WHERE sep_personalizationid = {$this->id}";
		$DB->no_result($sql);
		//echo mysql_error();
	}
	
	/**
	 * Funktion liefert alle Preisstaffelungen eines Artikels
	 */
	public function getPrices(){
		global $DB;
		$retval = Array();
		$sql = "SELECT * FROM personalization_seperation WHERE sep_personalizationid = {$this->id} ORDER BY sep_max";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = $r;
			}
		}
		return $retval;
	}
	
	/**
	 * Funktion liefert einen Preis zu einer bestimmten Menge
	 */
	public function getPrice($amount){
		global $DB;
		$sql = "SELECT * FROM personalization_seperation WHERE
				sep_personalizationid = ".$this->id." AND
				".$amount." <= sep_max
				ORDER BY sep_max";
		if($DB->num_rows($sql)){
			$res = $DB->select($sql);
			$retval = $res[0]["sep_price"];
		} else {
			// Wenn kein Wert zwischen MIN und MAX, nehme den groessten der zugehoerigen Preise
			$sql = "SELECT * FROM personalization_seperation
					WHERE sep_personalizationid = ".$this->id."
					ORDER BY sep_max DESC LIMIT 0, 1";
			$res = $DB->select($sql);
			return $res[0]["sep_price"];
		}
		return $retval;
	}
	
	/**
	 * Liefert die Sichtbarkeit eines Preises (ob der Kunden den Preis sehen darf)
	 * @return integer
	 */
	public function getPriceVisible($amount){
		global $DB;
		$sql = "SELECT * FROM personalization_seperation WHERE
				sep_personalizationid = ".$this->id." AND
				".$amount." <= sep_max
				ORDER BY sep_max";
		
		if($DB->num_rows($sql)){
			$res = $DB->select($sql);
			$retval = $res[0]["sep_show"];
		} else {
			// Wenn kein Wert zwischen MIN und MAX, nehme den groessten der zugehoerigen Preise
			$sql = "SELECT * FROM personalization_seperation
					WHERE sep_personalizationid = ".$this->id."
					ORDER BY sep_max DESC LIMIT 0, 1";
			$res = $DB->select($sql);
			return $res[0]["sep_show"];
		}
		return $retval;
	}
	
	/**
	 * Loeschfunktion fuer die Id
	 */
	 function clearId(){
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
	
	public function getLineByLine()
	{
	    return $this->linebyline;
	}

	public function setLineByLine($linebyline)
	{
	    $this->linebyline = $linebyline;
	}

	public function getTitle()
	{
	    return $this->title;
	}

	public function setTitle($title)
	{
	    $this->title = $title;
	}

	public function getComment()
	{
	    return $this->comment;
	}

	public function setComment($comment)
	{
	    $this->comment = $comment;
	}

	public function getArticle()
	{
	    return $this->article;
	}

	public function setArticle($article)
	{
	    $this->article = $article;
	}

	public function getPicture()
	{
	    return $this->picture;
	}

	public function setPicture($picture)
	{
	    $this->picture = $picture;
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

	public function getFields()
	{
	    return $this->fields;
	}

	public function setFields($fields)
	{
	    $this->fields = $fields;
	}

	public function getCustomer()
	{
	    return $this->customer;
	}

	public function setCustomer($customer)
	{
	    $this->customer = $customer;
	}

	public function getDirection()
	{
	    return $this->direction;
	}

	public function setDirection($direction)
	{
	    $this->direction = $direction;
	}

	public function getFormat()
	{
	    return $this->format;
	}

	public function setFormat($format)
	{
	    $this->format = $format;
	}

	public function getFormatwidth()
	{
	    return $this->formatwidth;
	}

	public function setFormatwidth($formatwidth)
	{
	    $this->formatwidth = $formatwidth;
	}

	public function getFormatheight()
	{
	    return $this->formatheight;
	}

	public function setFormatheight($formatheight)
	{
	    $this->formatheight = $formatheight;
	}

	public function getType()
	{
	    return $this->type;
	}

	public function setType($type)
	{
	    $this->type = $type;
	}

	public function getPicture2()
	{
	    return $this->picture2;
	}

	public function setPicture2($picture2)
	{
	    $this->picture2 = $picture2;
	}
	
	/**
     * @return the $hidden
     */
    public function getHidden()
    {
        return $this->hidden;
    }

	/**
     * @param number $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

	/**
	 * @return float
	 */
	public function getAnschnitt()
	{
		return $this->anschnitt;
	}

	/**
	 * @param float $anschnitt
	 */
	public function setAnschnitt($anschnitt)
	{
		$this->anschnitt = $anschnitt;
	}

	/**
	 * @return string
	 */
	public function getPreview()
	{
		return $this->preview;
	}

	/**
	 * @param string $preview
	 */
	public function setPreview($preview)
	{
		$this->preview = $preview;
	}
	
}
?>