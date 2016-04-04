<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			23.08.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

class DocumentFormat{

    private $id = 0;
    private $doctype;
    private $width = 210;
    private $height = 297;
    private $orientation = self::ORI_PORTRAIT;
    private $margin_top = 0;
    private $margin_bottom = 0;
    private $margin_left = 0;
    private $margin_right = 0;

    const ORI_LANDSCAPE = 'L';
    const ORI_PORTRAIT = 'P';

    const TYPE_OFFER = 1; // Angebot
    const TYPE_OFFERCONFIRM = 2; // Angebotsbestaetigung
    const TYPE_DELIVERY = 3; // Lieferschein
    const TYPE_INVOICE = 4; // Rechnung
    const TYPE_FACTORY = 5; // Drucktasche
    const TYPE_INVOICEWARNING = 6; // Mahnung
    const TYPE_REVERT = 7; // Gutschrift
    const TYPE_PERSONALIZATION = 10; // Personalisierung
    const TYPE_PERSONALIZATION_ORDER = 11; // Personalisierungsbestellung
    const TYPE_LABEL = 15; // Etiketten fuer Kartons/Palette
    const TYPE_PAPER_ORDER = 20; // Etiketten fuer Kartons/Palette

    public $types = Array(
        self::TYPE_DELIVERY => 'Lieferschein',
        self::TYPE_FACTORY => 'Auftragstasche',
        self::TYPE_INVOICE => 'Rechnung',
        self::TYPE_INVOICEWARNING => 'Mahnung',
        self::TYPE_LABEL => 'Etikett',
        self::TYPE_OFFER => 'Angebot',
        self::TYPE_OFFERCONFIRM => 'Auftragsbestätigung',
        self::TYPE_PAPER_ORDER => 'Papier-Bestellung',
        self::TYPE_REVERT => 'Gutschrift'
    );

    /**
     *
     * @param int $id
     */
    public function __construct($id=0){
        global $DB;
        global $_USER;

        if($id>0){
            $sql = "SELECT * FROM document_formats WHERE id=".$id." ";
            if($DB->num_rows($sql)){

                $res = $DB->select($sql);
                $res = $res[0];

                $this->id 			= $res["id"];
                $this->width 		= $res["width"];
                $this->height 		= $res["height"];
                $this->orientation 	= $res["orientation"];
                $this->doctype 	    = $res["doctype"];
                $this->margin_top 	= $res["margin_top"];
                $this->margin_bottom= $res["margin_bottom"];
                $this->margin_left 	= $res["margin_left"];
                $this->margin_right = $res["margin_right"];
            }
        }
    }

    /**
     *
     * @return boolean
     */
    public function save(){
        global $DB;
        if($this->id > 0){
            $sql = "UPDATE document_formats SET
                        width = {$this->width},
                        height = {$this->height},
                        orientation = '{$this->orientation}',
                        doctype = {$this->doctype},
                        margin_top = {$this->margin_top},
                        margin_bottom = {$this->margin_bottom},
                        margin_left = {$this->margin_left},
                        margin_right = {$this->margin_right}
                    WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO document_formats
                        (width, height, orientation, doctype, margin_top, margin_bottom, margin_left, margin_right)
                    VALUES
                        ({$this->width}, {$this->height}, '{$this->orientation}', {$this->doctype},
                         {$this->margin_top}, {$this->margin_bottom}, {$this->margin_left}, {$this->margin_right})";
            $res = $DB->no_result($sql);
            if($res) {
                $sql = "SELECT max(id) id FROM document_formats WHERE doctype = {$this->doctype}";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else
                return false;
        }
    }


    /**
     * @param int $doctype
     * @return bool|DocumentFormat
     */
    public static function getForDocType($doctype = 0){
        global $DB;
        $sql = "SELECT id FROM document_formats WHERE doctype = {$doctype}";
        $docformat = new DocumentFormat();
        if($DB->no_result($sql)){
            $result = $DB->select($sql);
            foreach($result as $r){
                $docformat = new DocumentFormat($r["id"]);
            }
        }
        return $docformat;
    }

    /**
     *
     * @return boolean
     */
    public function delete(){
        global $DB;
        if($this->id > 0){
            $sql = "DELETE FROM document_formats WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
            if($res){
                unset($this);
                return true;
            } else {
                return false;
            }
        }
    }

    public static function getAllTypes(){
        $types = Array(
            self::TYPE_DELIVERY => 'Lieferschein',
            self::TYPE_FACTORY => 'Auftragstasche',
            self::TYPE_INVOICE => 'Rechnung',
            self::TYPE_INVOICEWARNING => 'Mahnung',
            self::TYPE_LABEL => 'Etikett',
            self::TYPE_OFFER => 'Angebot',
            self::TYPE_OFFERCONFIRM => 'Auftragsbestätigung',
            self::TYPE_PAPER_ORDER => 'Papier-Bestellung',
            self::TYPE_REVERT => 'Gutschrift'
        );
        return $types;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getDoctype()
    {
        return $this->doctype;
    }

    /**
     * @param mixed $doctype
     */
    public function setDoctype($doctype)
    {
        $this->doctype = $doctype;
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param float $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return float
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param float $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return string
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * @param string $orientation
     */
    public function setOrientation($orientation)
    {
        $this->orientation = $orientation;
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @param array $types
     */
    public function setTypes($types)
    {
        $this->types = $types;
    }

    /**
     * @return int
     */
    public function getMarginTop()
    {
        return $this->margin_top;
    }

    /**
     * @param int $margin_top
     */
    public function setMarginTop($margin_top)
    {
        $this->margin_top = $margin_top;
    }

    /**
     * @return int
     */
    public function getMarginBottom()
    {
        return $this->margin_bottom;
    }

    /**
     * @param int $margin_bottom
     */
    public function setMarginBottom($margin_bottom)
    {
        $this->margin_bottom = $margin_bottom;
    }

    /**
     * @return int
     */
    public function getMarginLeft()
    {
        return $this->margin_left;
    }

    /**
     * @param int $margin_left
     */
    public function setMarginLeft($margin_left)
    {
        $this->margin_left = $margin_left;
    }

    /**
     * @return int
     */
    public function getMarginRight()
    {
        return $this->margin_right;
    }

    /**
     * @param int $margin_right
     */
    public function setMarginRight($margin_right)
    {
        $this->margin_right = $margin_right;
    }
}