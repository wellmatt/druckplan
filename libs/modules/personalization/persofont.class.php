<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			26.02.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

class PersoFont {

	//Konstanten
	const ORDER_ID = "id";
	const ORDER_TITLE = "title";

	private $id = 0;			// Einzigartige interne ID
	private $status = 1;		// Status (0=geloescht)
	private $title;				// Titel 
	private $filename;			// Name der zughoerigen Datei (afm)
	private $filename2;			// Name der Zugehörigen Datei (pfb)
	
	const FILE_DESTINATION = "./thirdparty/tcpdf/fonts/tmp/";
	
	/**
	 * Konstruktor
	 * @param int $id
	 */
	function __construct($id = 0){
		global $DB;
	
		if ($id > 0){
			$sql = "SELECT * FROM persofont WHERE id = {$id}";
			if($DB->num_rows($sql)){
				$r = $DB->select($sql);
				$r = $r[0];
				$this->id = $r["id"];
				$this->status = $r["status"];
				$this->filename = $r["filename"];
				$this->filename2 = $r["filename2"];
				$this->title = $r["title"];
			}
		}
	}
	
	/**
	 * Speicherfunktion fuer Schriftarten
	 * @return boolean
	 */
	public function save(){
		global $DB;
	
		if($this->id > 0){
			$sql = "UPDATE persofont SET 
					title 		= '{$this->title}', 
					filename	= '{$this->filename}', 
					filename2	= '{$this->filename2}' 
					WHERE id = {$this->id}";
			return $DB->no_result($sql);
		} else {
			$sql = "INSERT INTO persofont 
					(status, title, filename, filename2) 
					VALUES 
					({$this->status}, '{$this->title}', '{$this->filename}', '{$this->filename2}' )";
			$res = $DB->no_result($sql);
		
			if($res){
				$sql = "SELECT max(id) id FROM persofont WHERE title = '{$this->title}'";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["id"];
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Loeschfunktion fuer Schriftarten 
	 */
	public function delete(){
		global $DB;
		$sql = "UPDATE persofont SET status	= 0 WHERE id = {$this->id}";
		return $DB->no_result($sql);
	}
	
	/**
	 * Liefert alle aktiven Schriftarten
	 * @param String $order
	 * @return multitype:PersoFont
	 */
	static function getAllPersoFonts($order = self::ORDER_TITLE){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM persofont WHERE status > 0 ORDER BY {$order}";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new PersoFont($r["id"]);
			}
		}
		return $retval;
	}
	
	/************************************** GETTER und SETTER ********************************************************/		

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

	public function getFilename()
	{
	    return $this->filename;
	}

	public function setFilename($filename)
	{
	    $this->filename = $filename;
	}

	public function getFilename2()
	{
	    return $this->filename2;
	}

	public function setFilename2($filename2)
	{
	    $this->filename2 = $filename2;
	}
}
?>