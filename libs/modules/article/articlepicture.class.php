<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			15.07.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

class Articlepicture {

	//Konstanten
	const ORDER_ID = "id";

	private $id = 0;			// Einzigartige interne ID
	private $articleid = 0;		// Artikelnummer
	private $url;				// Verweis auf das Bild
	private $crtdate;			// Erstelldateum (Hochlade-Datum)

	/**
	 * Konstruktor eines Artikelbildes
	 *
	 * @param int $id
	 */
	function __construct($id = 0){
		global $DB;
		global $_USER;

		if ($id > 0){
			$sql = "SELECT * FROM article_pictures WHERE id = {$id}";
			if($DB->num_rows($sql)){
				$r = $DB->select($sql);
				$r = $r[0];
				$this->id = $r["id"];
				$this->articleid = $r["articleid"];
				$this->url = $r["url"];
				$this->crtdate = $r["crtdate"];
			}
		}
	}

	/**
	 * Speicher-Funktion fuer Artikelbilder
	 *
	 * @return boolean
	 */
	function save(){
		global $DB;
		global $_USER;
		$now = time();

		if($this->id > 0){
			$sql = "UPDATE article_pictures SET
					url = '{$this->url}',
					articleid = {$groupid},
					WHERE id = {$this->id}";
			return $DB->no_result($sql);
		} else {
			$sql = "INSERT INTO article_pictures
					(url, crtdate, articleid )
					VALUES
					( '{$this->url}', {$now}, {$this->articleid} )";
			$res = $DB->no_result($sql);

			if($res){
				$sql = "SELECT max(id) id FROM article_pictures WHERE title = '{$this->title}'";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["id"];
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * Loeschfunktion f�r Artikel.
	 * Der Artikel wird nicht entgueltig geloescht, der Status und die Freigabe wird auf 0 gesetzt
	 *
	 * @return boolean
	 */
	public function delete(){
		global $DB;
		if($this->id > 0){
			$sql = "UPDATE article_pictures
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
	
	/*************************************************
	 ****** 		GETTER u. SETTER			******
	 *************************************************/

	public function getId()
	{
	    return $this->id;
	}

	public function getArticleid()
	{
	    return $this->articleid;
	}

	public function setArticleid($articleid)
	{
	    $this->articleid = $articleid;
	}

	public function getUrl()
	{
	    return $this->url;
	}

	public function setUrl($url)
	{
	    $this->url = $url;
	}
}