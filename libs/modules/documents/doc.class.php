<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */
require_once 'libs/basic/model.php';

class Doc extends Model{
    public $name = '';
    public $requestId = 0;
    public $requestModule = '';
    public $type = 0;
    public $hash = '';
    public $crtdate = 0;
    public $crtuser = 0;
    public $sent = 0;
    public $reverse = 0;
    public $preview = 0;
    public $letterhead = false;

    protected $order;
    protected $template;
    protected $docfolder;

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

    const VERSION_EMAIL = 1;
    const VERSION_PRINT = 2;

    /**
     * Returns Array of Strings of all Document Types
     * @return array
     */
    public static function getTypes()
    {
        return [
            "Angebot" => 1,
            "Angebotsbestätigung" => 2,
            "Lieferschein" => 3,
            "Rechnung" => 4,
            "Drucktasche" => 5,
            "Mahnung" => 6,
            "Gutschrift" => 7,
            "Etikett" => 15,
            "Papierbestellung" => 20
        ];
    }

    /**
     * Gets Documents that match the filter
     */

    /**
     * @param int $version
     * @param bool $oldhash
     * @return bool|string
     */
    public function createDoc($version, $oldhash = false)
    {
        $order = $this->order;
        $template = $this->template;

        // Init pdffile
        if ($this->type == self::TYPE_PERSONALIZATION || $this->type == self::TYPE_PERSONALIZATION_ORDER) {

            $pdf_width = $order->getFormatwidth() + $order->getAnschnitt()*2;
            $pdf_height = $order->getFormatheight() + $order->getAnschnitt()*2;

            if ($pdf_width > $pdf_height) {
                $direction = 'L';
            } else {
                $direction = 'P';
            }

            $format = Array( $pdf_width, $pdf_height );

            $pdf = new PersoPDF($direction, 'mm', $format, true, 'UTF-8', false);

            if ($version == self::VERSION_EMAIL){
                $pdf->setHeaderfile('docs/personalization/'.$order->getPicture());
                $pdf->SetPrintHeader(true);
                $pdf->SetPrintFooter(false);
                $pdf->AddPage();
            } else {
                $pdf->SetPrintHeader(false);
                $pdf->SetPrintFooter(false);
                $pdf->AddPage();
            }

            if ($this->requestModule == self::REQ_MODULE_PERSONALIZATION) {
                if ($this->type == self::TYPE_PERSONALIZATION) {
                    require $template;
                }
                if ($this->type == self::TYPE_PERSONALIZATION_ORDER) {
                    require $template;
                }
            }

            if ($order->getType() == 1)
            {
                $this->setReverse(1);

                if ($version == self::VERSION_EMAIL){
                    $pdf->setHeaderfile('docs/personalization/'.$order->getPicture2());
                    $pdf->SetPrintHeader(true);
                    $pdf->SetPrintFooter(false);
                    $pdf->AddPage();
                } else {
                    $pdf->SetPrintHeader(false);
                    $pdf->SetPrintFooter(false);
                    $pdf->AddPage();
                }
                if ($this->requestModule == self::REQ_MODULE_PERSONALIZATION) {
                    if ($this->type == self::TYPE_PERSONALIZATION) {
                        require $template;
                    }
                    if ($this->type == self::TYPE_PERSONALIZATION_ORDER) {
                        require $template;
                    }
                }
            }
        } else {
            $letterhead = new Letterhead($this->letterhead);
            if ($this->letterhead === false || ($this->letterhead != false && $letterhead->getId() == 0)){
                $letterhead = Letterhead::fetchSingle(Array(Array('column'=>'std','value'=>1),Array('column'=>'type','value'=>$this->type)));
                $this->letterhead = $letterhead;
            }
            if ($letterhead->getId() > 0){

                $letterhead1 = $letterhead->getFilename1();
                $letterhead2 = $letterhead->getFilename2();
                $docformat1 = $letterhead->getDocformat1();
                $docformat2 = $letterhead->getDocformat2();
                $docwidth = $docformat1->getWidth();
                $docheight = $docformat1->getHeight();
                $docori = $docformat1->getOrientation();
                $docori2 = $docformat2->getOrientation();


                $pdf = new FPDIPdf($docori, 'mm', Array($docwidth,$docheight), true, 'UTF-8', false);
                if ($version == self::VERSION_EMAIL){
                    $pdf->setHeaderfile($letterhead1);
                    $pdf->SetPrintHeader(true);
                } else {
                    $pdf->SetPrintHeader(false);
                }
                $pdf->SetPrintFooter(false);


                $pdf->setPageOrientation($docori, TRUE, $docformat1->getMarginBottom());
                $pdf->SetMargins($this->tofloat($docformat1->getMarginLeft()), $this->tofloat($docformat1->getMarginTop()), $this->tofloat($docformat1->getMarginRight()), TRUE);
                $pdf->AddPage();

                if (self::VERSION_EMAIL) {
                    $pdf->setHeaderfile($letterhead2);
                    $pdf->setPageOrientation($docori2, TRUE, $docformat2->getMarginBottom());
                    $pdf->SetMargins($this->tofloat($docformat2->getMarginLeft()), $this->tofloat($docformat2->getMarginTop()), $this->tofloat($docformat2->getMarginRight()), TRUE);
                }

                // apply specific template
                require $template;

            } else {
                return false;
            }
        }

        // create PDF-File
        if ($this->hash == "" || $this->hash == 0)
            $this->hash = md5(microtime());

        if ($oldhash)
            $this->hash = $oldhash;

        ob_flush();
        $filename = $this->getFilename($version);

        if ($this->preview == 0)
            $pdf->Output($filename, 'F');
        else {
            $pdf->Output($filename, 'F');
        }

        return $this->hash;
    }

    public function getFilename($version)
    {
        global $_CONFIG;
        $filename = $_CONFIG->docsBaseDir;

        $filename .= $this->docfolder . $this->hash;

        if ($version == self::VERSION_PRINT)
            $filename .= "_p";
        else
            $filename .= "_e";
        $filename .= '.pdf';
        return $filename;
    }

    /**
     * @param $num
     * @return float
     */
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @param int $requestId
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     * @return string
     */
    public function getRequestModule()
    {
        return $this->requestModule;
    }

    /**
     * @param string $requestModule
     */
    public function setRequestModule($requestModule)
    {
        $this->requestModule = $requestModule;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return int
     */
    public function getCrtdate()
    {
        return $this->crtdate;
    }

    /**
     * @param int $crtdate
     */
    public function setCrtdate($crtdate)
    {
        $this->crtdate = $crtdate;
    }

    /**
     * @return int
     */
    public function getCrtuser()
    {
        return $this->crtuser;
    }

    /**
     * @param int $crtuser
     */
    public function setCrtuser($crtuser)
    {
        $this->crtuser = $crtuser;
    }

    /**
     * @return int
     */
    public function getSent()
    {
        return $this->sent;
    }

    /**
     * @param int $sent
     */
    public function setSent($sent)
    {
        $this->sent = $sent;
    }

    /**
     * @return int
     */
    public function getReverse()
    {
        return $this->reverse;
    }

    /**
     * @param int $reverse
     */
    public function setReverse($reverse)
    {
        $this->reverse = $reverse;
    }

    /**
     * @return int
     */
    public function getPreview()
    {
        return $this->preview;
    }

    /**
     * @param int $preview
     */
    public function setPreview($preview)
    {
        $this->preview = $preview;
    }

    /**
     * @return boolean
     */
    public function isLetterhead()
    {
        return $this->letterhead;
    }

    /**
     * @param boolean $letterhead
     */
    public function setLetterhead($letterhead)
    {
        $this->letterhead = $letterhead;
    }
}