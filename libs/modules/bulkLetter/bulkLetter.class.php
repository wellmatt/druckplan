<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			28.10.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'thirdparty/tcpdf/tcpdf.php';
require_once 'thirdparty/tcpdf/contilas.tcpdf.php';

/**
 * Klasse fuer die Serienbriefen bzw. Massenanschreiben
 */
class Bulkletter{
	
	const ORDER_ID 				= " id ";
	const ORDER_TITLE 			= " title ";
	const ORDER_CREATE_DESC 	= " crt_date DESC";
	const ORDER_CREATE_ASC		= " crt_date ASC";
	
	private $id = 0;				// ID
	private $status = 1;			// 1 = erstellt, 2 = Versandfertig, 3=Verschickt
	private $title;					// Titel // Betreff
	private $text;					// Eigentliches Anschreiben
	private $crt_date;				// Erstelldatum
	private $crt_user;				// Ersteller
	private $upd_date;				// letztes Aenderungsdatum
	private $upd_user;				// zuletzt bearbeitet von
    private $contactperson;         // Ansprechpartner der Firma
    private $doc_email_created=0;	// Ob das Dokument mit Birefpapier erstellt wurde
	private $doc_print_created=0;	// Ob das Dokument ohne Birefpapier erstellt wurde

	private $customer_filter = 4;   // Kunden Filter
	private $customer_attrib = Array();       // Kunden Filter Attribute
	
	/**
	 * Konstruktor eines Serienbriefs
	 *
	 * @param int $id
	 */
	function __construct($id = 0){
		global $DB;
		
		$this->crt_user = new User();
		$this->upd_user = new User();
		
		if ($id > 0){
			$sql = "SELECT * FROM bulkletter WHERE id = {$id}";
			if($DB->num_rows($sql)){
				$r = $DB->select($sql);
				$r = $r[0];
				$this->id = $r["id"];
				$this->status = $r["status"];
				$this->title = $r["title"];
				$this->text = $r["text"];				
				$this->crt_user = new User($r["crt_user"]);
				$this->crt_date = $r["crt_date"];
                $this->upd_user = new User($r["upd_user"]);
				$this->upd_date = $r["upd_date"];
				$this->doc_email_created = $r["doc_email_created"];
				$this->doc_print_created = $r["doc_print_created"];
				$this->customer_filter = $r["customer_filter"];
        		$this->customer_attrib = unserialize($r["customer_attrib"]);
			}
		}

    }
	
	/**
	 * Speicher-Funktion fuer Mahnstufen
	 *
	 * @return boolean
	 */
	function save(){
		global $DB;
		global $_USER;
		$now = time();
		$custatrib = serialize($this->customer_attrib);
	
		if($this->id > 0){
			$sql = "UPDATE bulkletter SET
					title 	= '{$this->title}', 
					text	= '{$this->text}', 
					status = {$this->status}, 
					upd_user = {$_USER->getId()}, 
					upd_date = UNIX_TIMESTAMP(), 
					doc_email_created = {$this->doc_email_created}, 
					customer_filter = {$this->customer_filter}, 
					customer_attrib = '{$custatrib}', 
					doc_print_created = {$this->doc_print_created}
					WHERE id = {$this->id}";
				return $DB->no_result($sql);
		} else {
			$sql = "INSERT INTO bulkletter
					(status, title, text, 
					crt_date, crt_user, customer_filter, customer_attrib )
					VALUES
					({$this->status}, '{$this->title}', '{$this->text}', 
					{$now}, {$_USER->getId()}, {$this->customer_filter}, '{$custatrib}' )";
			$res = $DB->no_result($sql);
	
			if($res){
				$sql = "SELECT max(id) id FROM bulkletter WHERE title = '{$this->title}'";
				$thisid = $DB->select($sql);
				$this->id = $thisid[0]["id"];
				return true;
			} else {
				return false;
			}
		}
	}
	
	/**
	 * Loeschfunktion fuer Mahnstufen. Kein echtes Loechen, der Status wird auf 0 gesetzt.
	 *
	 * @return boolean
	 */
	public function delete(){
		global $DB;
		if($this->id > 0){
			$sql = "UPDATE bulkletter 
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
	 * Erstellt das PDF-Dokument eines Anschreibens in Abhaengigkeit der Version (E-Mail vs. Print)
	 */
	public function createDocument(){
		global $_CONFIG;
		global $_USER;

		foreach (Array(Document::VERSION_EMAIL,Document::VERSION_PRINT) as $version){
			$docformat = DocumentFormat::getForDocType(DocumentFormat::TYPE_BULKLETTER);
			$format[0] = $docformat->getWidth();
			$format[1] = $docformat->getHeight();

			$filename = $this->getPdfLink($version);

			if ($version == Document::VERSION_EMAIL)
			{
				$pdf = new TCPDF_BG($docformat->getOrientation(), 'mm', $format, true, 'UTF-8', false);
				$pdf->SetPrintHeader(true);
				$pdf->SetPrintFooter(false);
			}
			else
			{
				$pdf = new TCPDF($docformat->getOrientation(), 'mm', $format, true, 'UTF-8', false);
				$pdf->SetPrintHeader(false);
				$pdf->SetPrintFooter(false);
			}
			$pdf->setPageOrientation($docformat->getOrientation(), TRUE, $docformat->getMarginBottom());
			$pdf->SetMargins($this->tofloat($docformat->getMarginLeft()), $this->tofloat($docformat->getMarginTop()), $this->tofloat($docformat->getMarginRight()), TRUE);
			$pdf->AddPage();

			require 'docs/templates/bulkletter.tmpl.php';
			$pdf->Output($filename, 'F');
		}
	}
	
	/**
	 * liefert den Link zur PDF-Datei in Abhaengigkeit der Version (E-Mail vs. Print)
	 * 
	 * @param int $version
	 * @return string
	 */
	public function getPdfLink($version = Document::VERSION_PRINT){
		global $_CONFIG;
		
		if($version == Document::VERSION_EMAIL){
			$link = $_CONFIG->docsBaseDir."bulkletter/".$this->id."_Anschreiben_e.pdf";
		} else {
			$link = $_CONFIG->docsBaseDir."bulkletter/".$this->id."_Anschreiben_p.pdf";
		}
		
		return $link;
	}

	function tofloat($num) {
		$dotPos = strrpos($num, '.');
		$commaPos = strrpos($num, ',');
		$sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
			((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

		if (!$sep) {
			return floatval(preg_replace("/[^0-9]/", "", $num));
		}

		return floatval(
			preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
			preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
		);
	}
	
	/**
	 * Funktion liefert alle aktiven Mahnstufen nach angegebener Reighenfolge
	 *
	 * @param STRING $order Reihenfolge
	 * @return Array : Bulkletter
	 */
	static function getAllBulkletter($order = self::ORDER_TITLE){
		global $DB;
		$retval = Array();
		$sql = "SELECT id FROM bulkletter WHERE status > 0 ORDER BY {$order}";
		if($DB->num_rows($sql)){
			foreach($DB->select($sql) as $r){
				$retval[] = new Bulkletter($r["id"]);
			}
		}
		return $retval;
	}
	
	/*******************************************************
	 ************	GETTER und SETTER			************
	******************************************************/

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

	public function getText()
	{
	    return $this->text;
	}

	public function setText($text)
	{
	    $this->text = $text;
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

	public function getUpd_date()
	{
	    return $this->upd_date;
	}

	public function setUpd_date($upd_date)
	{
	    $this->upd_date = $upd_date;
	}

	public function getUpd_user()
	{
	    return $this->upd_user;
	}

	public function setUpd_user($upd_user)
	{
	    $this->upd_user = $upd_user;
	}
	/**
     * @return the $customer_filter
     */
    public function getCustomerFilter()
    {
        return $this->customer_filter;
    }

	/**
     * @return the $customer_attrib
     */
    public function getCustomerAttrib()
    {
        return $this->customer_attrib;
    }

	/**
     * @param number $customer_filter
     */
    public function setCustomerFilter($customer_filter)
    {
        $this->customer_filter = $customer_filter;
    }

	/**
     * @param multitype: $customer_attrib
     */
    public function setCustomerAttrib($customer_attrib)
    {
        $this->customer_attrib = $customer_attrib;
    }

	
	

}
?>