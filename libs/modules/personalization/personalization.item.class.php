<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			08.07.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

class Personalizationitem {
	
	// Konstanten
	const ORDER_TITLE 		= " title ";
	const ORDER_XPOS 		= " xpos ";
	const ORDER_YPOS 		= " ypos ";
	const ORDER_SITE_YPOS 	= " reverse, ypos ";
	
	const SITE_FRONT 	= 0;
	const SITE_BACK		= 1;
	const SITE_ALL		= 2;

	private $id = 0;			// ID
	private $status = 1;		// Status (0=geloescht)
	private $title;				// Titel/Bezeichnung/Platzhalter
	private $xpos;				// x-Position
	private $ypos;				// y-Position
	private $width; 			// Breite
	private $height;			// Hoehe
	private	$boxtype = 1;		// Typ ; 1=Textfeld, 2=Textbox
	private $peronalid;			// ID der Personalisierung, zu der dieses Eingabefeld gehoert			
	private $textsize;			// Schriftgroesse
	private $justification=0;	// Ausrichtung: 0=links, 1=mitte, 2=rechts
	private $font;				// Schriftart -> siehe /docs/templates/personalization.tmpl.php
	private $color_c;			//
	private $color_m;			// Farben in RGB
	private $color_y;			//
	private $color_k;			//
	private $spacing;			// Zeilenabstand bei Textboxen
	private $dependencyID; 		// ID des Objekts (Perso-Item) an dessen y-Stelle dieses Objekt rueckt, wenn das andere leer bleibt
	private $reverse = 0;		// Zugehoerigkeit: zur Vorder- oder Rueckseite
	private $predefined = 0;	// Vordefiniertes Feld JA/NEIN
	private $position = 0;		// Feld ist f�r Titel/Position (Dropdown im Frontend)
	private $readonly = 0;		// Vordefiniertes Feld / nicht editierbar im Frontend
	private $tab = 0;		    // Abstand in mm f�r \t
	private $group;             // Gruppe fuer Zeile fuer Zeile
	private $sort;

	/**
	 * Konstruktor eines Eingabefelds einer Personalisierung
	 *
	 * @param int $id
	 */
	function __construct($id = 0){
		global $DB;
		global $_USER;

		if ($id > 0){
			$sql = "SELECT * FROM personalization_items WHERE id = {$id}";
			if($DB->num_rows($sql)){
				$r = $DB->select($sql);
				$r = $r[0];
				$this->id = $r["id"];
				$this->status = $r["status"];
				$this->title = $r["title"];
				$this->xpos = $r["xpos"];
				$this->ypos = $r["ypos"];
				$this->width = $r["width"];
				$this->height = $r["height"];
				$this->boxtype = $r["boxtype"];
				$this->peronalid = $r["personal_id"];
				$this->textsize = $r["text_size"];
				$this->justification = $r["justification"];
				$this->font = $r["font"];
				$this->color_c = $r["color_c"];
				$this->color_m = $r["color_m"];
				$this->color_y = $r["color_y"];
				$this->color_k = $r["color_k"];
				$this->spacing = $r["spacing"];
				$this->dependencyID = $r["dependency_id"];
				$this->reverse = $r["reverse"];
				$this->predefined = $r["predefined"];
				$this->position = $r["position"];
				$this->readonly = $r["readonly"];
				$this->tab = $r["tab"];
				$this->group = $r["zzgroup"];
				$this->sort = $r["sort"];
			}
		}
	}

	/**
	 * Speicher-Funktion fuer Eingabefelder einer Personalisierung
	 *
	 * @return boolean
	 */
	function save(){
		global $DB;
		global $_USER;
		$now = time();

		if($this->id > 0){
			$sql = "UPDATE personalization_items SET 
					title 				= '{$this->title}',
					xpos				= {$this->xpos},
					ypos 				= {$this->ypos},
					boxtype 			= {$this->boxtype},
					width 				= {$this->width},
					height 				= {$this->height},
					text_size			= {$this->textsize},
					justification		= {$this->justification},
					font				= {$this->font},
					color_c				= {$this->color_c},
					color_m				= {$this->color_m},
					color_y				= {$this->color_y},
					color_k				= {$this->color_k},
					spacing				= {$this->spacing},
					dependency_id		= {$this->dependencyID}, 
					personalization_id 	= {$this->peronalid}, 
					predefined 			= {$this->predefined}, 
					position 			= {$this->position}, 
					readonly 			= {$this->readonly}, 
					tab 			    = {$this->tab}, 
					zzgroup 			= {$this->group},
					sort 			    = {$this->sort},
					reverse				= {$this->reverse} 
					WHERE id = {$this->id}";
// 			echo $sql . "</br>";
			return $DB->no_result($sql);
		} else {
			$sql = "INSERT INTO personalization_items
					(status, boxtype, title, personalization_id, 
					 xpos, ypos, font, spacing, 
					 width, height, text_size, justification,
					 color_c, color_m, color_y, color_k, 
					 dependency_id, reverse, predefined, position, readonly, tab, zzgroup, sort )	
					VALUES
					({$this->status}, {$this->boxtype}, '{$this->title}', {$this->peronalid},
					 {$this->xpos}, {$this->ypos}, {$this->font}, {$this->spacing},  
					 {$this->width}, {$this->height}, {$this->textsize}, {$this->justification},  
					 {$this->color_c}, {$this->color_m}, {$this->color_y}, {$this->color_k}, 
					 {$this->dependencyID}, {$this->reverse}, {$this->predefined}, {$this->position}, {$this->readonly}, {$this->tab}, {$this->group}, {$this->sort} )";
			$res = $DB->no_result($sql);

			if($res){
				$sql = "SELECT max(id) id FROM personalization_items WHERE title = '{$this->title}' ";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["id"];
				return true;
			} else {
				return false;
			}
		}
	}

	/**
	 * Loeschfunktion fuer Eingabefelder von Personalisierungen
	 *
	 * @return boolean
	 */
	public function delete(){
		global $DB;
		if($this->id > 0){
			$sql = "UPDATE personalization_items
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
	 * Funktion liefert alle aktiven Eingabefelder einer Personalisierug nach angegebener Reighenfolge
	 *
	 * @param STRING $order Reihenfolge, in der die Eingbelfelder sortiert werden
	 * @return Array : Personalizationitem
	 */
	static function getAllPersonalizationitems($personalid, $order = self::ORDER_TITLE, $site = self::SITE_FRONT){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM personalization_items 
				WHERE 
				status > 0 AND 
				personalization_id = {$personalid} ";
		
		if ($site == 0 || $site == 1){
			$sql .= " AND reverse = {$site} ";
		}
		
		$sql .= " ORDER BY sort ASC, {$order} ";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Personalizationitem($r["id"]);
			}
		}
		return $retval;
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

	public function getXpos()
	{
	    return $this->xpos;
	}

	public function setXpos($xpos)
	{
	    $this->xpos = $xpos;
	}

	public function getYpos()
	{
	    return $this->ypos;
	}

	public function setYpos($ypos)
	{
	    $this->ypos = $ypos;
	}

	public function getWidth()
	{
	    return $this->width;
	}

	public function setWidth($width)
	{
	    $this->width = $width;
	}

	public function getHeight()
	{
	    return $this->height;
	}

	public function setHeight($height)
	{
	    $this->height = $height;
	}

	public function getBoxtype()
	{
	    return $this->boxtype;
	}

	public function setBoxtype($boxtype)
	{
	    $this->boxtype = $boxtype;
	}

	public function getPeronalid()
	{
	    return $this->peronalid;
	}

	public function setPeronalid($peronalid)
	{
	    $this->peronalid = $peronalid;
	}

	public function getTextsize()
	{
	    return $this->textsize;
	}

	public function setTextsize($textsize)
	{
	    $this->textsize = $textsize;
	}

	public function getJustification()
	{
	    return $this->justification;
	}

	public function setJustification($justification)
	{
	    $this->justification = $justification;
	}

	public function getFont()
	{
	    return $this->font;
	}

	public function setFont($font)
	{
	    $this->font = $font;
	}

	public function getSpacing()
	{
	    return $this->spacing;
	}

	public function setSpacing($spacing)
	{
	    $this->spacing = $spacing;
	}

	public function getColor_c()
	{
	    return $this->color_c;
	}

	public function setColor_c($color_c)
	{
	    $this->color_c = $color_c;
	}

	public function getColor_m()
	{
	    return $this->color_m;
	}

	public function setColor_m($color_m)
	{
	    $this->color_m = $color_m;
	}

	public function getColor_y()
	{
	    return $this->color_y;
	}

	public function setColor_y($color_y)
	{
	    $this->color_y = $color_y;
	}

	public function getColor_k()
	{
	    return $this->color_k;
	}

	public function setColor_k($color_k)
	{
	    $this->color_k = $color_k;
	}

	/**
	 * Liefert das Perosnalisierungs-Attribut von dem dieses in der y-Stelle abhaengig ist
	 * @return Personalizationitem
	 */
	public function getDependency(){
		$retval = new Personalizationitem($this->dependencyID);
		return $retval;
	}
	
	public function getDependencyID()
	{
	    return $this->dependencyID;
	}

	public function setDependencyID($dependencyID)
	{
	    $this->dependencyID = $dependencyID;
	}

	public function getReverse()
	{
	    return $this->reverse;
	}

	public function setReverse($reverse)
	{
	    $this->reverse = $reverse;
	}
	
	public function getPreDefined()
	{
	    return $this->predefined;
	}

	public function setPreDefined($predefined)
	{
	    $this->predefined = $predefined;
	}
	
	public function getPosition()
	{
	    return $this->position;
	}

	public function setPosition($position)
	{
	    $this->position = $position;
	}
	
	public function getReadOnly()
	{
	    return $this->readonly;
	}

	public function setReadOnly($readonly)
	{
	    $this->readonly = $readonly;
	}
	
	/**
     * @return the $tab
     */
    public function getTab()
    {
        return $this->tab;
    }

	/**
     * @param number $tab
     */
    public function setTab($tab)
    {
        $this->tab = $tab;
    }
    
	/**
     * @return the $group
     */
    public function getGroup()
    {
        return $this->group;
    }

	/**
     * @param field_type $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }
    
	/**
     * @return the $sort
     */
    public function getSort()
    {
        return $this->sort;
    }

	/**
     * @param field_type $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }
    
}
?>
