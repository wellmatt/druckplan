<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */

require_once 'libs/modules/personalization/persopdf.php';
require_once 'thirdparty/tcpdf/tcpdf.php';
require_once 'thirdparty/tcpdf/contilas.tcpdf.php';
require_once 'libs/modules/perferences/perferences.class.php';
require_once 'libs/modules/documentformats/documentformat.class.php';
require_once 'libs/modules/letterhead/letterhead.class.php';
require_once 'fpdipdf.class.php';
require_once 'libs/modules/bulkLetter/bulkLetter.class.php';

class Document
{
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
    const TYPE_BULKLETTER = 21; // Etiketten fuer Kartons/Palette

    const REQ_MODULE_ORDER = 1;
    const REQ_MODULE_MANORDER = 2;
    const REQ_MODULE_COLLECTIVEORDER = 3;
    const REQ_MODULE_PERSONALIZATION = 4;
    const REQ_MODULE_BULKLETTER = 5;

    const VERSION_EMAIL = 1;
    const VERSION_PRINT = 2;

    private $id = 0;
    private $name;
    private $requestId = 0;
    private $requestModule = '';
    private $type = 0;
    private $hash = 0;
    private $createDate = 0;
    private $createUser = 0;
    private $priceNetto = 0;
    private $priceBrutto = 0;
    private $payable = 0;
    private $payed = 0;
    private $sent = 0;
    private $warningId = 0;
    private $reverse = 0; // z.B. fuer Rueckseite bei Personalisierungen
    private $stornoDate = 0;
    private $paper_order_pid = 0; // nur f�r Papier Bestellungen
    private $preview = 0; // vorschau
    private $letterhead = false;

    function __construct($id = 0)
    {
        global $DB;
        if ($id > 0) {
            $sql = "SELECT * FROM documents WHERE id = {$id}";
            if ($DB->num_rows($sql)) {
                $res = $DB->select($sql);
                $res = $res[0];
                
                $this->id = $res["id"];
                $this->name = $res["doc_name"];
                $this->requestId = $res["doc_req_id"];
                $this->requestModule = $res["doc_req_module"];
                $this->type = $res["doc_type"];
                $this->hash = $res["doc_hash"];
                $this->createDate = $res["doc_crtdat"];
                $this->createUser = new User($res["doc_crtusr"]);
                $this->priceNetto = $res["doc_price_netto"];
                $this->priceBrutto = $res["doc_price_brutto"];
                $this->payable = $res["doc_payable"];
                $this->payed = $res["doc_payed"];
                $this->sent = $res["doc_sent"];
                $this->warningId = $res["doc_warning_id"];
                $this->reverse = $res["doc_reverse"];
                $this->stornoDate = $res["doc_storno_date"];
                $this->paper_order_pid = $res["paper_order_pid"];
            }
        }
    }

    function save()
    {
        global $DB;
        global $_USER;
        $set = "doc_name = '{$this->name}',
                doc_req_id = {$this->requestId},
                doc_req_module = {$this->requestModule},
                doc_type = {$this->type},
                doc_hash = '{$this->hash}',
                doc_price_netto = {$this->priceNetto},
                doc_price_brutto = {$this->priceBrutto},
                doc_payable = {$this->payable},
                doc_payed = {$this->payed},
                doc_sent = {$this->sent},
                doc_warning_id = {$this->warningId}, 
        		doc_reverse = {$this->reverse}, 
				paper_order_pid = {$this->paper_order_pid}, 
        		doc_storno_date= {$this->stornoDate}  ";
        
        if ($this->id > 0) {
            $sql = "UPDATE documents SET {$set} WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO documents SET {$set}, doc_crtdat = UNIX_TIMESTAMP(), doc_crtusr = {$_USER->getId()}";
            $res = $DB->no_result($sql);
            echo $DB->getLastError();
            if ($res) {
                $sql = "SELECT max(id) id FROM documents WHERE doc_crtusr = {$_USER->getId()}";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            }
        }
        return false;
    }

    function delete()
    {
        global $_USER;
        global $DB;
        global $_CONFIG;
        
        if ($this->id > 0) {
            $sql = "DELETE FROM documents WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
            if ($res) {
                if ($this->type != self::TYPE_LABEL) {
                    if ($this->name != "") {
                        $sql = "INSERT INTO documents_freednumbers
	                                (type, number, client_id)
	                            VALUES
	                                ({$this->type}, '{$this->name}', {$_USER->getClient()->getId()})";
                        $DB->no_result($sql);
                    }
                }
                
                if ($this->requestModule == self::REQ_MODULE_ORDER || $this->requestModule == self::REQ_MODULE_BULKLETTER) {
                    $filename = $_CONFIG->docsBaseDir;
                } elseif ($this->requestModule == self::REQ_MODULE_COLLECTIVEORDER) {
                    $filename = $_CONFIG->docsBaseDir . "col";
                } elseif ($this->requestModule == self::REQ_MODULE_PERSONALIZATION) {
                    $filename = $_CONFIG->docsBaseDir . "per";
                }
                
                switch ($this->type) {
                    case self::TYPE_OFFER:
                        $filename .= 'offer/' . $_USER->getClient()->getId() . '.' . $this->hash;
                        break;
                    case self::TYPE_OFFERCONFIRM:
                        $filename .= 'offerconfirm/' . $_USER->getClient()->getId() . '.' . $this->hash;
                        break;
                    case self::TYPE_FACTORY:
                        $filename .= 'factory/' . $_USER->getClient()->getId() . '.' . $this->hash;
                        break;
                    case self::TYPE_INVOICE:
                        $filename .= 'invoice/' . $_USER->getClient()->getId() . '.' . $this->hash;
                        break;
                    case self::TYPE_DELIVERY:
                        $filename .= 'delivery/' . $_USER->getClient()->getId() . '.' . $this->hash;
                        break;
                    case self::TYPE_PAPER_ORDER:
                        $filename .= 'paper_order/' . $_USER->getClient()->getId() . '.' . $this->hash;
                        break;
                    case self::TYPE_LABEL:
                        $filename .= 'label/' . $_USER->getClient()->getId() . '.' . $this->hash;
                        break;
                    case self::TYPE_INVOICEWARNING:
                        $filename .= 'invoicewarning/' . $_USER->getClient()->getId() . '.' . $this->hash;
                        break;
                    case self::TYPE_REVERT:
                        $filename .= 'revert/' . $_USER->getClient()->getId() . '.' . $this->hash;
                        break;
                    case self::TYPE_PERSONALIZATION: // Bei Personalization ist $order eine Personalisierung
                        $filename .= "sonalization/" . $_USER->getClient()->getId() . ".per_" . $this->hash;
                        break; // Bitte auch bei der Create-Funktion anpassen
                    case self::TYPE_PERSONALIZATION_ORDER: // Bei Personalization ist $order eine Personalisierungsbestellung
                        $filename .= 'sonalization/' . $_USER->getClient()->getId() . '.' . "per_" . $this->hash;
                        break; // Bitte auch bei der Create-Funktion anpassen
                    case self::TYPE_BULKLETTER:
                        $filename .= 'bulkletter/' . $_USER->getClient()->getId() . '.' . $this->hash;
                        break;
                }
                
                if (! $this->requestModule == self::REQ_MODULE_PERSONALIZATION) {
                    unlink($filename . "_p.pdf"); // Delete Print
                }
                unlink($filename . "_e.pdf"); // Delete E-Mail
                unset($this);
            }
        }
        return false;
    }

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
            "Papierbestellung" => 20,
            "Serienbrief" => 21
        ];
    }

    /**
     * Gets Documents for given filter.
     * Possible filteroptions are:
     *
     * module => constant Module
     * type => constant Type
     * requestId => id of requesting object in module
     *
     * @return Document[]
     */
    static public function getDocuments($filter)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT t1.id FROM documents t1, orders t2
                WHERE 1 = 1";
        
        if ($filter["module"])
            $sql .= " AND t1.doc_req_module = {$filter["module"]} ";
        if ($filter["type"])
            $sql .= " AND t1.doc_type = {$filter["type"]} ";
        if ($filter["requestId"])
            $sql .= " AND t1.doc_req_id = {$filter["requestId"]} ";
        if ($filter["cust_id"] > 0) {
            $sql .= " AND t2.businesscontact_id = {$filter["cust_id"]} AND t1.doc_req_id = t2.id ";
        }
        if ($filter["payed_status"] == 1) {
            $sql .= " AND t1.doc_payed = 0 ";
        }
        if ($filter["payed_status"] == 2) {
            $sql .= " AND t1.doc_payed > 0 ";
        }
        if ($filter["sent"] === false) {
            $sql .= " AND t1.doc_sent = 0 ";
        }
        if ($filter["storno"] != true) {
            $sql .= " AND t1.doc_storno_date = 0 ";
        }
        
        if ($filter["date_from"] > 0 && $filter["date_to"]) {
            $sql .= " AND t1.doc_crtdat > {$filter["date_from"]} AND t1.doc_crtdat < {$filter["date_to"]} ";
        }
        $sql .= "GROUP BY t1.id";
//         echo $sql."<br>";
        
        if ($DB->num_rows($sql)) {
            foreach ($DB->select($sql) as $res) {
                $retval[] = new Document($res["id"]);
            }
        }
        
        return $retval;
    }

    /**
     * ************************* Statistiken **********************************
     */
    static public function salesPerCustMod1($cust_id, $year)
    {
        global $DB;
        $retval = Array();
        $sql = "select doc.id, doc.doc_name, doc.doc_req_id, doc.doc_req_module, doc.doc_type, 
                doc.doc_price_netto, doc.doc_crtdat, sum(doc_price_netto) as 'total',";
        
        $sql .= " ord.id, ord.businesscontact_id ";
        
        $sql .= "FROM documents as doc
            INNER JOIN orders ord ON doc.doc_req_id = ord.id
            WHERE doc.doc_type = 4
            AND YEAR(FROM_UNIXTIME(doc.doc_crtdat)) = {$year}
            AND ord.businesscontact_id = {$cust_id}
            AND doc.doc_req_module = 1";
        
        if ($DB->num_rows($sql)) {
            $retval[] = $DB->select($sql);
        }
        
        return $retval;
    }

    static public function salesPerCustMod2($cust_id, $year)
    {
        global $DB;
        $retval = Array();
        $sql = "select doc.id, doc.doc_name, doc.doc_req_id, doc.doc_req_module, doc.doc_type, 
                doc.doc_price_netto, doc.doc_crtdat, sum(doc_price_netto) as 'total',
                 mi.id, mi.businesscontact
	            FROM documents as doc
	            INNER JOIN manualinvoice mi ON doc.doc_req_id = mi.id
	            WHERE doc.doc_type = 4
	            AND YEAR(FROM_UNIXTIME(doc.doc_crtdat)) = {$year}
	            AND mi.businesscontact = {$cust_id}
	            AND doc.doc_req_module = 2  
	            AND doc.doc_storno_date = 0 ";
        
        if ($DB->num_rows($sql)) {
            $retval[] = $DB->select($sql);
        }
        
        return $retval;
    }

    static public function salesPerCustMonthMod1($cust_id, $year, $m)
    {
        global $DB;
        $retval = Array();
        $sql = "select doc.id, doc.doc_name, doc.doc_req_id, doc.doc_req_module, doc.doc_type, 
                doc.doc_price_netto, doc.doc_crtdat, sum(doc_price_netto) as 'total',
                ord.id, ord.businesscontact_id
            FROM documents as doc
            INNER JOIN orders ord ON doc.doc_req_id = ord.id
            WHERE doc.doc_type = 4
            AND YEAR(FROM_UNIXTIME(doc.doc_crtdat)) = {$year}
            AND MONTH(FROM_UNIXTIME(doc_crtdat)) = {$m}
            AND ord.businesscontact_id = {$cust_id}
            AND doc.doc_req_module = 1 
            AND doc.doc_storno_date = 0 ";
        
        if ($DB->num_rows($sql)) {
            $retval[] = $DB->select($sql);
        }
        
        return $retval;
    }

    static public function salesPerCustMonthMod2($cust_id, $year, $m)
    {
        global $DB;
        $retval = Array();
        $sql = "select doc.id, doc.doc_name, doc.doc_req_id, doc.doc_req_module, doc.doc_type, 
                doc.doc_price_netto, doc.doc_crtdat, sum(doc_price_netto) as 'total',
                mi.id, mi.businesscontact
            FROM documents as doc
            INNER JOIN manualinvoice mi ON doc.doc_req_id = mi.id
            WHERE doc.doc_type = 4
            AND YEAR(FROM_UNIXTIME(doc.doc_crtdat)) = {$year}
            AND MONTH(FROM_UNIXTIME(doc_crtdat)) = {$m}
            AND mi.businesscontact = {$cust_id}
            AND doc.doc_req_module = 2 
            AND doc.doc_storno_date = 0 ";
        
        if ($DB->num_rows($sql)) {
            $retval[] = $DB->select($sql);
        }
        
        return $retval;
    }

    /**
     * ************************* Ende Statistiken **********************************************************
     */
    
    /**
     * Berechnet den Index fuer ein Dokument, und liefert ihn mit Bindestrich
     * 
     * @param const $module
     *            : Modul
     * @param const $docType
     *            : Type des Dokuments
     * @param int $reqId
     *            : zugehoeriges Objekt
     * @return string
     */
    static function getDocIndexCounter($module, $docType, $reqId)
    {
        $filter = Array(
            "module" => $module,
            "type" => $docType,
            "requestId" => $reqId
        );
        $index_counter = self::getDocuments($filter);
        if (count($index_counter) == 0 || $index_counter == false) {
            return "";
        } else {
            return "-" . count($index_counter);
        }
    }

    /**
     * Funktion erstellt das eigentliche PDF-Dokument und legt es auf der Festplatte ab.
     *
     * @param String $version            
     * @param boolean $oldhash
     * @param boolean $withheader
     * @return number
     */
    function createDoc($version, $oldhash = false, $withheader = true)
    {
        global $DB;
        global $_USER;
        global $_CONFIG;
        global $_LANG;

        switch ($this->requestModule){
            case self::REQ_MODULE_ORDER:
                $order = new Order($this->requestId);
                break;
            case self::REQ_MODULE_COLLECTIVEORDER:
                $order = new CollectiveInvoice($this->requestId);
                break;
            case self::REQ_MODULE_PERSONALIZATION:
                if ($this->type == self::TYPE_PERSONALIZATION) {
                    $order = new Personalization($this->requestId);
                }
                if ($this->type == self::TYPE_PERSONALIZATION_ORDER) {
                    $perso_order = new Personalizationorder($this->requestId);
                    $order = new Personalization($perso_order->getPersoID());
                }
                break;
            case self::REQ_MODULE_BULKLETTER:
                $order = new Bulkletter($this->requestId);
                break;
            default:
                return false;
        }

        // create Document Name if empty
        if ($this->name == "" && $this->preview == 0) {
            // Test if we have already a freed number
            $sql = "SELECT * FROM documents_freednumbers
                    WHERE client_id = {$_USER->getClient()->getId()}
                        AND type = {$this->type}
                    ORDER BY number";
            if ($DB->num_rows($sql)) {
                $res = $DB->select($sql);
                $this->name = $res[0]["number"];
                
                $sql = "DELETE FROM documents_freednumbers 
                        WHERE number = '{$this->name}'
                            AND client_id = {$_USER->getClient()->getId()}";
                $DB->no_result($sql);
            } else {
                switch ($this->type) {
                    case self::TYPE_OFFER:
                        $this->name = $_USER->getClient()->createOrderNumber(Client::NUMBER_OFFER);
//                          $tmp_index = self::getDocIndexCounter($this->requestModule, Document::TYPE_OFFER, $order->getId());
//                          $this->name = "AN" . substr($order->getNumber(), 2) . $tmp_index;
                        break;
                    case self::TYPE_OFFERCONFIRM:
                        $this->name = $_USER->getClient()->createOrderNumber(Client::NUMBER_OFFERCONFIRM);
//                          $tmp_index = self::getDocIndexCounter($this->requestModule, Document::TYPE_OFFERCONFIRM, $order->getId());
//                          $this->name = "AB" . substr($order->getNumber(), 2) . $tmp_index;
                        break;
                    case self::TYPE_FACTORY:
                        $this->name = $_USER->getClient()->createOrderNumber(Client::NUMBER_WORK);
                        break;
                    case self::TYPE_INVOICE:
                        $this->name = $_USER->getClient()->createOrderNumber(Client::NUMBER_INVOICE);
                        break;
                    case self::TYPE_DELIVERY:
                        $this->name = $_USER->getClient()->createOrderNumber(Client::NUMBER_DELIVERY);
                        break;
                    case self::TYPE_PAPER_ORDER:
                        $this->name = $_USER->getClient()->createOrderNumber(Client::NUMBER_PAPER_ORDER);
                        break;
                    case self::TYPE_LABEL:
                        $this->name = "ETI-" . $order->getNumber();
                        break;
                    case self::TYPE_REVERT:
                        $this->name = $_USER->getClient()->createOrderNumber(Client::NUMBER_REVERT);
                        break;
                    case self::TYPE_INVOICEWARNING:
                        $this->name = $_USER->getClient()->createOrderNumber(Client::NUMBER_WARNING);
                        break;
                    case self::TYPE_PERSONALIZATION: // Bei Personalization ist $order eine Personalisierung
                        $this->name = $_USER->getClient()->getId() . "_" . $order->getId() . "_" . $order->getArticle()->getId();
                        break; // Bitte auch bei der Loeschfunktion anpassen
                    case self::TYPE_PERSONALIZATION_ORDER: // Bei Personalization ist $order eine Personalisierungsbestellung
                        $this->name = $_USER->getClient()->getId() . "_" . $order->getId() . "_" . $order->getArticle()->getId();
                        break; // Bitte auch bei der Loeschfunktion anpassen
                    case self::TYPE_BULKLETTER:
                        $this->name = $_USER->getClient()->createOrderNumber(Client::NUMBER_BULKLETTER);
                        break;
                }
            }
        }
        
        // Init pdffile
        if ($this->type == self::TYPE_PERSONALIZATION || $this->type == self::TYPE_PERSONALIZATION_ORDER) {

            $pdf_width = $order->getFormatwidth() + $order->getAnschnitt()*2;
            $pdf_height = $order->getFormatheight() + $order->getAnschnitt()*2;

            if ($pdf_width > $pdf_height) {
                $direction = 'L';
            } else {
                $direction = 'P';
            }
            Global $_BASEDIR;
            
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
                    require 'docs/templates/personalization.tmpl.php';
                }
                if ($this->type == self::TYPE_PERSONALIZATION_ORDER) {
                    require 'docs/templates/personalization.order.tmpl.php';
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
                        require 'docs/templates/personalization.tmpl.php';
                    }
                    if ($this->type == self::TYPE_PERSONALIZATION_ORDER) {
                        require 'docs/templates/personalization.order.tmpl.php';
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

            } else {
                return false;
            }
        }
        
        // apply specific template
        if ($this->requestModule == self::REQ_MODULE_ORDER) {
            if ($this->type == self::TYPE_OFFER)
                require 'docs/templates/offer.tmpl.php';
            if ($this->type == self::TYPE_OFFERCONFIRM)
                require 'docs/templates/offerconfirm.tmpl.php';
            if ($this->type == self::TYPE_FACTORY)
                require 'docs/templates/factory.tmpl.php';
            if ($this->type == self::TYPE_INVOICE)
                require 'docs/templates/invoice.tmpl.php';
            if ($this->type == self::TYPE_LABEL)
                require 'docs/templates/label.tmpl.php';
            if ($this->type == self::TYPE_DELIVERY)
                require 'docs/templates/delivery.tmpl.php';
            if ($this->type == self::TYPE_PAPER_ORDER)
                require 'docs/templates/paper_order.tmpl.php';
            if ($this->type == self::TYPE_INVOICEWARNING)
                require 'docs/templates/invoicewarning.tmpl.php';
        } else if ($this->requestModule == self::REQ_MODULE_COLLECTIVEORDER) {
            if ($this->type == self::TYPE_OFFER)
                require 'docs/templates/coloffer.tmpl.php';
            if ($this->type == self::TYPE_OFFERCONFIRM)
                require 'docs/templates/colofferconfirm.tmpl.php';
            if ($this->type == self::TYPE_FACTORY)
                require 'docs/templates/factory.tmpl.php';
            if ($this->type == self::TYPE_INVOICE)
                require 'docs/templates/colinvoice.tmpl.php';
            if ($this->type == self::TYPE_LABEL)
                require 'docs/templates/label.tmpl.php';
            if ($this->type == self::TYPE_DELIVERY)
                require 'docs/templates/coldelivery.tmpl.php';
            if ($this->type == self::TYPE_INVOICEWARNING)
                require 'docs/templates/invoicewarning.tmpl.php';
            if ($this->type == self::TYPE_REVERT)
                require 'docs/templates/revert.tmpl.php';
        } else if ($this->requestModule == self::REQ_MODULE_BULKLETTER){
            if ($this->type == self::TYPE_BULKLETTER)
                require 'docs/templates/bulkletter.tmpl.php';
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

    /**
     * Funktion erstellt den Namen des Dukuments, um das PDF auf der Festplatte wiederzufinden
     *
     * @param String $version            
     * @return String
     */
    public function getFilename($version)
    {
        global $_USER;
        global $_CONFIG;
        if ($this->requestModule == self::REQ_MODULE_ORDER || $this->requestModule == self::REQ_MODULE_BULKLETTER)
            $filename = $_CONFIG->docsBaseDir;
        if ($this->requestModule == self::REQ_MODULE_MANORDER)
            $filename = $_CONFIG->docsBaseDir . "man";
        if ($this->requestModule == self::REQ_MODULE_COLLECTIVEORDER)
            $filename = $_CONFIG->docsBaseDir . "col";
        if ($this->requestModule == self::REQ_MODULE_PERSONALIZATION)
            $filename = $_CONFIG->docsBaseDir . "per";
        
        switch ($this->type) {
            case self::TYPE_OFFER:
                $filename .= 'offer/' . $_USER->getClient()->getId() . '.' . $this->hash;
                break;
            case self::TYPE_OFFERCONFIRM:
                $filename .= 'offerconfirm/' . $_USER->getClient()->getId() . '.' . $this->hash;
                break;
            case self::TYPE_FACTORY:
                $filename .= 'factory/' . $_USER->getClient()->getId() . '.' . $this->hash;
                break;
            case self::TYPE_INVOICE:
                $filename .= 'invoice/' . $_USER->getClient()->getId() . '.' . $this->hash;
                break;
            case self::TYPE_DELIVERY:
                $filename .= 'delivery/' . $_USER->getClient()->getId() . '.' . $this->hash;
                break;
            case self::TYPE_PAPER_ORDER:
                $filename .= 'paper_order/' . $_USER->getClient()->getId() . '.' . $this->hash;
                break;
            case self::TYPE_LABEL:
                $filename .= 'label/' . $_USER->getClient()->getId() . '.' . $this->hash;
                break;
            case self::TYPE_INVOICEWARNING:
                $filename .= 'invoicewarning/' . $_USER->getClient()->getId() . '.' . $this->hash;
                break;
            case self::TYPE_PERSONALIZATION:
                $filename .= 'sonalization/' . $_USER->getClient()->getId() . '.' . "per_" . $this->hash;
                break;
            case self::TYPE_PERSONALIZATION_ORDER:
                $filename .= 'sonalization/' . $_USER->getClient()->getId() . '.' . "per_" . $this->hash;
                break;
            case self::TYPE_BULKLETTER:
                $filename .= 'bulkletter/' . $_USER->getClient()->getId() . '.' . "per_" . $this->hash;
                break;
        }
        
        if ($version == self::VERSION_PRINT)
            $filename .= "_p";
        else 
            if ($version == self::VERSION_EMAIL)
                $filename .= "_e";
        $filename .= '.pdf';
        return $filename;
    }
    
    // --------------------------------------------------------------------
    // Getter / Setter
    // --------------------------------------------------------------------
    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getRequestId()
    {
        return $this->requestId;
    }

    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    public function getRequestModule()
    {
        return $this->requestModule;
    }

    public function setRequestModule($requestModule)
    {
        $this->requestModule = $requestModule;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    public function getCreateDate()
    {
        return $this->createDate;
    }

    public function getCreateUser()
    {
        return $this->createUser;
    }

    public function getPriceNetto()
    {
        return $this->priceNetto;
    }

    public function setPriceNetto($priceNetto)
    {
        $this->priceNetto = $priceNetto;
    }

    public function getPriceBrutto()
    {
        return $this->priceBrutto;
    }

    public function setPriceBrutto($priceBrutto)
    {
        $this->priceBrutto = $priceBrutto;
    }

    public function getPayable()
    {
        return $this->payable;
    }

    public function setPayable($payable)
    {
        $this->payable = $payable;
    }

    public function getPayed()
    {
        return $this->payed;
    }

    public function setPayed($payed)
    {
        $this->payed = $payed;
    }

    public function getWarningId()
    {
        return $this->warningId;
    }

    public function setWarningId($warningId)
    {
        $this->warningId = $warningId;
    }

    public function getSent()
    {
        return $this->sent;
    }

    public function setSent($sent)
    {
        $this->sent = $sent;
    }

    public function getReverse()
    {
        return $this->reverse;
    }

    public function setReverse($reverse)
    {
        $this->reverse = $reverse;
    }

    public function getStornoDate()
    {
        return $this->stornoDate;
    }

    public function setStornoDate($stornoDate)
    {
        $this->stornoDate = $stornoDate;
    }

    public function getPaperOrderPid()
    {
        return $this->paper_order_pid;
    }

    public function setPaperOrderPid($paper_order_pid)
    {
        $this->paper_order_pid = $paper_order_pid;
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
     * @return the $preview
     */
    public function getPreview()
    {
        return $this->preview;
    }

	/**
     * @param number $preview
     */
    public function setPreview($preview)
    {
        $this->preview = $preview;
    }

    /**
     * @return int
     */
    public function getLetterhead()
    {
        return $this->letterhead;
    }

    /**
     * @param int $letterhead
     */
    public function setLetterhead($letterhead)
    {
        $this->letterhead = $letterhead;
    }
}
?>