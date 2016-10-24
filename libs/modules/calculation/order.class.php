<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/products/product.class.php';
require_once 'libs/modules/foldtypes/foldtype.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/deliveryterms/deliveryterms.class.php';
require_once 'libs/modules/paymentterms/paymentterms.class.php';
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';
require_once 'libs/modules/documents/document.class.php';

class Order {
    const ORDER_NUMBER = "number desc";
    const ORDER_CUSTOMER = "businesscontact_id";
    const ORDER_STATUS = "status";
    const ORDER_TITLE = "title";
    
    const FILTER_CLOSED = "and status = 5";
    const FILTER_CONFIRMED = "and status = 3";
    const FILTER_OPEN = "and status <> 5";
    const FILTER_ALL = "";
    
    private $id = 0;
    private $number;
    private $status = 1;
    private $title;
    private $product;
    private $notes;
    private $crtdat = 0;
    private $crtusr;
    private $upddat = 0;
    private $collectiveinvoiceId = 0;
    private $internContact;				// Benutzer von KDM, der auf den Dokumenten auftauchen soll
    private $productName;				// Produktname in den Dokumenten ueberschreiben
    private $beilagen = "";                  // Text Feld für Beilagen
    private $articleid = 0;             // Verknuepfter Artikel
    
    function __construct($id)
    {
        $this->internContact = new User();
        $this->crtusr = new User;
        
        global $DB;
        if($id > 0){
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
                $sql = "SELECT * FROM orders WHERE id = {$id}";
                if ($DB->num_rows($sql)) {
                    $res = $DB->select($sql);
                    $res = $res[0];

                    $this->id = $res["id"];
                    $this->number = $res["number"];
                    $this->status = $res["status"];
                    $this->title = $res["title"];
                    $this->product = new Product($res["product_id"]);
                    $this->notes = $res["notes"];
                    $this->crtdat = $res["crtdat"];
                    $this->upddat = $res["upddat"];
                    $this->collectiveinvoiceId = $res["collectiveinvoice_id"];
                    $this->internContact = new User($res["intern_contactperson"]);
                    $this->productName = $res["productname"];
                    $this->crtusr = new User($res["crtusr"]);
                    $this->beilagen = $res["beilagen"];
                    $this->articleid = $res["articleid"];

                    Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
                }
            }
        }
    }

    function save()
    {
        global $DB;
        global $_USER;
        if($this->id > 0){
            $sql = "UPDATE orders SET
                        number = '{$this->number}',
                        status = {$this->status},
                        title = '{$this->title}',
                        product_id = '{$this->product->getId()}',
                        notes = '{$this->notes}',
                        upddat = UNIX_TIMESTAMP(),
                        updusr = {$_USER->getId()},
                        collectiveinvoice_id = {$this->collectiveinvoiceId},
                        intern_contactperson = {$this->internContact->getId()},
                		productname = '{$this->productName}',
                		beilagen = '{$this->beilagen}',
                		show_price_per_thousand = {$this->showPricePer1000},
                		articleid = {$this->articleid}
                    WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
        } else
        {
            if($this->number == "")
                $this->number = $_USER->getClient()->createOrderNumber(Client::NUMBER_ORDER);

            $sql = "INSERT INTO orders
                        (number, status, product_id, title, notes, crtdat, crtusr,
                         collectiveinvoice_id, intern_contactperson, productname,
                         beilagen, articleid )
                    VALUES
                        ('{$this->number}', 1, {$this->product->getId()},
                         '{$this->title}', '{$this->notes}', UNIX_TIMESTAMP(), {$_USER->getId()},
            			 {$this->collectiveinvoiceId}, {$this->internContact->getId()}, '{$this->productName}',
            			 '{$this->beilagen}', {$this->articleid} )";
            $res = $DB->no_result($sql);
            if($res)
            {
                $sql = "SELECT max(id) id FROM orders WHERE number = '{$this->number}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                $res = true;
            } else
                $res = false;
        }
        if ($res)
        {
            Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
            return true;
        }
        else
            return false;
    }

    function delete()
    {
        global $DB;
        if($this->id)
        {
            $sql = "UPDATE orders SET status = 0 WHERE id = {$this->id}";
            if($DB->no_result($sql))
            {
                Cachehandler::removeCache(Cachehandler::genKeyword($this));
                unset($this);
                return true;
            } else
                return false;
        }
    }

    static function getAllOrders($order = self::ORDER_NUMBER, $filter = self::FILTER_ALL)
    {
        $retval = Array();
        global $DB;
        $sql = "SELECT id FROM orders 
                WHERE status > 0 {$filter}
                ORDER BY {$order}";
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new Order($r["id"]);
            }
        }

        return $retval;
    }

    static function searchByNumber($number, $order = self::ORDER_NUMBER)
    {
        $retval = Array();
        global $DB;
        $sql = "SELECT id, number, status, title FROM orders
                    WHERE number like '%{$number}%'
                    ORDER BY {$order}";
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new Order($r["id"]);
            }
        }
    
        return $retval;
    }

    /**
     * @param $order
     * @param $colinv
     * @return string
     */
    public static function generateSummary(Order $order, CollectiveInvoice $colinv)
    {
        global $_USER;
        $html = "";
        
        $html .= '<h1>Kalkulationsbersicht</h1>';
        $html .= '<div class="outer"><table cellpadding="0" cellspacing="0" border="0" width="100%">';
        $html .= '<colgroup><col width="10%"><col width="23%"><col width="10%"><col width="23%"><col width="10%"><col></colgroup>';
        $html .= '<tr><td><b>Kundennummer:</b></td>';
        $html .= '<td>'.$colinv->getCustomer()->getCustomernumber().'</td>';
        $html .= '<td><b>Vorgang:</b></td>';
        $html .= '<td>'.$colinv->getNumber().'</td>';
        $html .= '<td><b>Telefon:</b></td>';
        $html .= '<td>'.$colinv->getCustomer()->getPhone().'</td>';
        $html .= '</tr><tr>';
        $html .= '<td valign="top"><b>Name:</b></td>';
        $html .= '<td valign="top">'.nl2br($colinv->getCustomer()->getNameAsLine()).'</td>';
        $html .= '<td valign="top"><b>Adresse:</b></td>';
        $html .= '<td valign="top">'.nl2br($colinv->getCustomer()->getAddressAsLine()).'</td>';
        $html .= '<td valign="top"><b>E-Mail:</b></td>';
        $html .= '<td valign="top">'.$colinv->getCustomer()->getEmail().'</td>';
        $html .= '</tr></table></div><br><div class="outer"><table cellpadding="0" cellspacing="0" border="0" width="100%">';
        $html .= '<colgroup><col width="10%"><col width="23%"><col width="10%"><col width="23%"><col width="10%"><col></colgroup>';
        $html .= '<tr>';
        $html .= '<td valign="top"><b>Produkt:</b></td>';
        $html .= '<td valign="top">'.$order->getProduct()->getName().'</td>';
        $html .= '<td valign="top"><b>Beschreibung:</b></td>';
        $html .= '<td valign="top">'.$order->getProduct()->getDescription().'</td>';
        $html .= '<td valign="top"><b>Bemerkungen:</b></td>';
        $html .= '<td valign="top">'.nl2br($order->getNotes()).'</td>';
        $html .= '</tr><tr>';
        $html .= '<td><b>Lieferadresse:</b></td>';
        $html .= '<td>'.nl2br($colinv->getDeliveryAddress()->getAddressAsLine()).'</td>';
        $html .= '<td><b>Lieferbedingungen:</b></td>';
        $html .= '<td>'.$colinv->getDeliveryterm()->getComment().'</td>';
        $html .= '<td><b>Lieferdatum:</b></td>';
        $html .= '<td>';
        if($colinv->getDeliveryDate() > 0)
            $html .= date('d.m.Y', $colinv->getDeliveryDate());
        $html .= '</td>';
        $html .= '</tr><tr>';
        $html .= '<td><b>Zahlungsadresse:</b></td>';
        $html .= '<td>'.nl2br($colinv->getInvoiceAddress()->getAddressAsLine()).'</td>';
        $html .= '<td><b>Zahlungsbedingungen:</b></td>';
        $html .= '<td>'.$colinv->getPaymentTerm()->getComment().'</td>';
        $html .= '<td><b>&nbsp;</b></td><td>&nbsp;</td></tr></table></div><br>';
        
        $i = 1; 
        foreach(Calculation::getAllCalculations($order) as $calc) {
            if ($calc->getState() == 0)
                continue;
            
            $calc_sorts = $calc->getSorts();
            if ($calc_sorts == 0)
                $calc_sorts = 1;
            
            $html .= '<h2>Teilauftag # '.$i.' - Auflage '.printBigInt($calc->getAmount()).' ('.$calc_sorts.' Sorte(n)* '.$calc->getAmount()/$calc_sorts.' Auflage)</h2>';
            $html .= '<div class="outer"><table cellpadding="0" cellspacing="0" border="0" width="100%"><colgroup><col width="15%">';
            $html .= '<col width="35%"><col width="15%"><col width="35%"></colgroup><tr>';
            $html .= '<td valign="top"><b>Inhalt:</b></td>';
            $html .= '<td valign="top">';
            $html .= $calc->getPaperContent()->getName().', '.$calc->getPaperContentWeight().' g';
            $html .= '</td>';
            $html .= '<td valign="top"><b>zus. Inhalt:</b></td>';
            $html .= '<td valign="top">';
            
            if($calc->getPaperAddContent()->getId()) {
                $html .= $calc->getPaperAddContent()->getName().', '.$calc->getPaperAddContentWeight().' g';
            }
            
            $html .= '</td></tr><tr><td valign="top"></td><td valign="top">';
            $html .= $calc->getPagesContent().' Seiten, '.$calc->getProductFormat()->getName().', '.$calc->getChromaticitiesContent()->getName();
            $html .= '</td><td valign="top"></td><td valign="top">';
            
            if($calc->getPaperAddContent()->getId()) {
                $html .= $calc->getPagesAddContent().' Seiten, '.$calc->getProductFormat()->getName().', '.$calc->getChromaticitiesAddContent()->getName();
            }
            
            $html .= '</td></tr><tr><td colspan="4">&nbsp;</td></tr><tr><td valign="top">';
            
            if($calc->getPaperAddContent2()->getId() > 0) {
                $html .= '<b>zus. Inhalt 2:</b>';
            }
            
            $html .= '</td><td valign="top">';
            
            if($calc->getPaperAddContent2()->getId() > 0) {
                $html .= $calc->getPaperAddContent2()->getName().', '.$calc->getPaperAddContent2Weight().' g';
            }
            
            $html .= '</td><td valign="top">';
            
            if($calc->getPaperAddContent3()->getId() > 0) {
                $html .= '<b>zus. Inhalt 3:</b>';
            }
            
            $html .= '</td><td valign="top">';
            
            if($calc->getPaperAddContent3()->getId()) {
                $html .= $calc->getPaperAddContent3()->getName().', '.$calc->getPaperAddContent3Weight().' g';
            }
            
            $html .= '</td></tr><tr><td valign="top"></td><td valign="top">';
            
            if($calc->getPaperAddContent2()->getId()) {
                $html .= $calc->getPagesAddContent2().' Seiten, '.$calc->getProductFormat()->getName().', '.$calc->getChromaticitiesAddContent2()->getName();
            }
            
            $html .= '</td><td valign="top"></td><td valign="top">';
            
            if($calc->getPaperAddContent3()->getId()) {
                $html .= $calc->getPagesAddContent3().' Seiten, '.$calc->getProductFormat()->getName().', '.$calc->getChromaticitiesAddContent3()->getName();
            }
            
            $html .= '</td></tr>';
            
            if($calc->getPaperEnvelope()->getId()) {
                $html .= '<tr><td colspan="4">&nbsp;</td></tr><tr>';
                $html .= '<td valign="top"><b>Umschlag:</b></td>';
                $html .= '<td valign="top">';
                $html .= $calc->getPaperEnvelope()->getName().', '.$calc->getPaperEnvelopeWeight().' g';
                $html .= '</td></tr><tr><td valign="top"></td><td valign="top">';
                $html .= $calc->getPagesEnvelope().' Seiten, '.$calc->getProductFormat()->getName().', '.$calc->getChromaticitiesEnvelope()->getName();
                $html .= '</td></tr>';
            }
            
            $html .= '</table></div><br>';
            $html .= '<h3>Papierpreise</h3>';
            $html .= '<div class="outer"><table cellpadding="0" cellspacing="0" border="0" width="100%">';
            $html .= '<colgroup><col width="15%"><col width="35%"><col width="15%"><col width="35%"></colgroup><tr>';
            $html .= '<td valign="top"><b>Inhalt:</b></td>';
            $html .= '<td valign="top">';
            $html .= 'Bogenformat: '.$calc->getPaperContentWidth().' mm x '.$calc->getPaperContentHeight().' mm <br>';
            $html .= 'Produktformat: '.$calc->getProductFormatWidth().' mm x '.$calc->getProductFormatHeight().' mm,'; 
            $html .= $calc->getProductFormatWidthOpen().' mm x '.$calc->getProductFormatHeightOpen().' mm (offen)<br>';
            $html .= 'Nutzen pro Bogen: '.$calc->getProductsPerPaper(Calculation::PAPER_CONTENT).',';
            $html .= 'Anzahl B&ouml;gen pro Auflage: '.printPrice($calc->getPagesContent() / $calc->getProductsPerPaper(Calculation::PAPER_CONTENT)).'<br>';
            $html .= 'B&ouml;gen insgesamt:';
             
            $sheets = ceil($calc->getPagesContent() / $calc->getProductsPerPaper(Calculation::PAPER_CONTENT) * $calc->getAmount());
            $html .= printBigInt($sheets);
            $html .= ' + Zuschuss';
            $html .= printBigInt($calc->getPaperContentGrant());
            
            $sheets += $calc->getPaperContentGrant();
            $sheets_content = $sheets;
            
            $html .= '<br>Papiergewicht:';
             
            $area = $calc->getPaperContentWidth() * $calc->getPaperContentHeight();

            $html .= printPrice((($area * $calc->getPaperContentWeight() / 10000 / 100) * $sheets) / 1000);
            $html .= ' kg,';
            $html .= 'Papierpreis: '.printPrice($calc->getPaperContent()->getSumPrice($sheets)).' €<br>';
            $html .= 'Preisbasis: '; 
            
            if ($calc->getPaperContent()->getPriceBase() == Paper::PRICE_PER_100KG) 
                $html .= 'Preis pro 100 kg';
            else 
                $html .= 'Preis pro 1000 B&ouml;gen';

            $html .= '</td><td valign="top"><b>zus. Inhalt:</b></td><td valign="top">';
            
            if($calc->getPaperAddContent()->getId()) {
                $html .= 'Bogenformat: '.$calc->getPaperAddContentWidth().' mm x '.$calc->getPaperAddContentHeight().' mm <br>';
                $html .= 'Produktformat: '.$calc->getProductFormatWidth().' mm x '.$calc->getProductFormatHeight().' mm,';
                $html .= $calc->getProductFormatWidthOpen().' mm x '.$calc->getProductFormatHeightOpen().' mm (offen)<br>';
                $html .= 'Nutzen pro Bogen: '.$calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT).',';
                $html .= 'Anzahl B&ouml;gen pro Auflage: '.printPrice($calc->getPagesAddContent() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT)).'<br>';
                $html .= 'B&ouml;gen insgesamt:';
                 
                $sheets = ceil($calc->getPagesAddContent() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT) * $calc->getAmount());
                $html .= printBigInt($sheets);
                $html .= ' + Zuschuss';
                $html .= printBigInt($calc->getPaperAddContentGrant());
                
                $sheets += $calc->getPaperAddContentGrant();
                $sheets_addcontent = $sheets;
                
                $html .= '<br>Papiergewicht:';
                 
                $area = $calc->getPaperAddContentWidth() * $calc->getPaperAddContentHeight();
                $html .= printPrice((($area * $calc->getPaperAddContentWeight() / 10000 / 100) * $sheets) / 1000);
                $html .= ' kg,';
                $html .= 'Papierpreis: '.printPrice($calc->getPaperAddContent()->getSumPrice($sheets)).' €<br>';
                $html .= 'Preisbasis: ';
                 
                if ($calc->getPaperAddContent()->getPriceBase() == Paper::PRICE_PER_100KG)
                    $html .= 'Preis pro 100 kg';
                else 
                    $html .= 'Preis pro 1000 B&ouml;gen';
            }
            
            $html .= '</td></tr><tr><td colspan="4">&nbsp;</td></tr><tr>';
            $html .= '<td valign="top"><b>zus. Inhalt 2:</b></td>';
            $html .= '<td valign="top">';
            
            if($calc->getPaperAddContent2()->getId()) {
                $html .= 'Bogenformat: '.$calc->getPaperAddContent2Width().' mm x '.$calc->getPaperAddContent2Height().' mm <br>';
                $html .= 'Produktformat: '.$calc->getProductFormatWidth().' mm x '.$calc->getProductFormatHeight().' mm,';
                $html .= $calc->getProductFormatWidthOpen().' mm x '.$calc->getProductFormatHeightOpen().' mm (offen)<br>';
                $html .= 'Nutzen pro Bogen: '.$calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2).',';
                $html .= 'Anzahl B&ouml;gen pro Auflage:';
                $html .= printPrice($calc->getPagesAddContent2() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2)).'<br>';
                $html .= 'B&ouml;gen insgesamt:';
                
                $sheets = ceil($calc->getPagesAddContent2() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2) * $calc->getAmount());
                $html .= printBigInt($sheets);
                $html .= ' + Zuschuss';
                $html .= printBigInt($calc->getPaperAddContent2Grant());
                
                $sheets += $calc->getPaperAddContent2Grant();
                $sheets_addcontent2 = $sheets;
                
                $html .= '<br>Papiergewicht:';
                 
                $area = $calc->getPaperAddContent2Width() * $calc->getPaperAddContent2Height();
                $html .= printPrice((($area * $calc->getPaperAddContent2Weight() / 10000 / 100) * $sheets) / 1000);
                $html .= ' kg,';
                $html .= 'Papierpreis: '.printPrice($calc->getPaperAddContent2()->getSumPrice($sheets)).' €<br>';
                $html .= 'Preisbasis: '; 
                
                if ($calc->getPaperAddContent2()->getPriceBase() == Paper::PRICE_PER_100KG)
                    $html .= 'Preis pro 100 kg';
                else
                    $html .= 'Preis pro 1000 B&ouml;gen';
            }
            
            $html .= '</td><td valign="top"><b>zus. Inhalt 3:</b></td><td valign="top">';
            
            if($calc->getPaperAddContent3()->getId()) {
                $html .= 'Bogenformat: '.$calc->getPaperAddContent3Width().' mm x '.$calc->getPaperAddContent3Height().' mm <br>';
                $html .= 'Produktformat: '.$calc->getProductFormatWidth().' mm x '.$calc->getProductFormatHeight().' mm,';
                $html .= $calc->getProductFormatWidthOpen().' mm x '.$calc->getProductFormatHeightOpen().' mm (offen)<br>';
                $html .= 'Nutzen pro Bogen: '.$calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3).',';
                $html .= 'Anzahl B&ouml;gen pro Auflage:';
                $html .= printPrice($calc->getPagesAddContent3() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3)).'<br>';
                $html .= 'B&ouml;gen insgesamt:';
                
                $sheets = ceil($calc->getPagesAddContent3() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3) * $calc->getAmount());
                $html .= printBigInt($sheets);
                $html .= ' + Zuschuss';
                $html .= printBigInt($calc->getPaperAddContent3Grant());
                
                $sheets += $calc->getPaperAddContent3Grant();
                $sheets_addcontent3 = $sheets;
                
                $html .= '<br>Papiergewicht:';
                 
                $area = $calc->getPaperAddContent3Width() * $calc->getPaperAddContent3Height();
                $html .= printPrice((($area * $calc->getPaperAddContent3Weight() / 10000 / 100) * $sheets) / 1000);
                $html .= ' kg,';
                $html .= 'Papierpreis: '.printPrice($calc->getPaperAddContent3()->getSumPrice($sheets)).' €<br>';
                $html .= 'Preisbasis: '; 
                
                if ($calc->getPaperAddContent3()->getPriceBase() == Paper::PRICE_PER_100KG)
                    $html .= 'Preis pro 100 kg';
                else
                    $html .= 'Preis pro 1000 B&ouml;gen';
            }
            
            $html .= '</td></tr>';
            
            if($calc->getPaperEnvelope()->getId()) {
                $html .= '<tr><td colspan="4">&nbsp;</td></tr><tr>';
                $html .= '<td valign="top"><b>Umschlag:</b></td><td valign="top">';
                $html .= 'Bogenformat: '.$calc->getPaperEnvelopeWidth().' mm x '.$calc->getPaperEnvelopeHeight().' mm <br>';
                $html .= 'Produktformat: '.$calc->getProductFormatWidth().' mm x '.$calc->getProductFormatHeight().' mm,';
                $html .= $calc->getEnvelopeWidthOpen().' mm x '.$calc->getEnvelopeHeightOpen().' mm (offen)<br>';
                $html .= 'Nutzen pro Bogen: '.$calc->getProductsPerPaper(Calculation::PAPER_ENVELOPE).',';
                $html .= 'Anzahl B&ouml;gen pro Auflage: '.printPrice($calc->getPagesEnvelope() / $calc->getProductsPerPaper(Calculation::PAPER_ENVELOPE)).'<br>';
                $html .= 'B&ouml;gen insgesamt:';

                $sheets = ceil($calc->getPagesEnvelope() / $calc->getProductsPerPaper(Calculation::PAPER_ENVELOPE) * $calc->getAmount());
                $html .= printBigInt($sheets);
                $html .= ' + Zuschuss';
                $html .= printBigInt($calc->getPaperEnvelopeGrant());
                
                $sheets += $calc->getPaperEnvelopeGrant();
                $sheets_envelope = $sheets;
                
                $html .= '<br>Papiergewicht:';
                 
                $area = $calc->getPaperEnvelopeWidth() * $calc->getPaperEnvelopeHeight();
                $html .= printPrice((($area * $calc->getPaperEnvelopeWeight() / 10000 / 100) * $sheets) / 1000);
                $html .= ' kg,';
                $html .= 'Papierpreis: '.printPrice($calc->getPaperEnvelope()->getSumPrice($sheets)).' €<br>';
                $html .= 'Preisbasis: ';

                if ($calc->getPaperEnvelope()->getPriceBase() == Paper::PRICE_PER_100KG)
                    $html .= 'Preis pro 100 kg';
                else
                    $html .= 'Preis pro 1000 B&ouml;gen';
                
                $html .= '</td></tr>';
            }

            $html .= '</table></div><br>';
            $html .= '<h3>Rohb&ouml;gen</h3><div class="outer"><table cellpadding="0" cellspacing="0" border="0" width="100%">';
            $html .= '<colgroup><col width="20%"><col width="20%"><col width="20%"><col width="20%"><col width="20%"></colgroup><tr>';
            foreach (Machineentry::getAllMachineentries($calc->getId(), Machineentry::ORDER_ID) as $me)
            {
                if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ||
                   $me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
                {
                    switch($me->getPart())
                    {
                        case Calculation::PAPER_CONTENT:
                            if ($calc->getFormat_in_content() != ""){
                                $format_in = explode("x", $calc->getFormat_in_content());
                                $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperContentHeight() * $calc->getPaperContentWidth()));
                                $roh2 = ceil($sheets_content / $roh);
                                $html .= '<td valign="top"><b>Inhalt:</b></br>';
                                $html .= 'Format: '.$calc->getFormat_in_content().' mm</br>';
                                $html .= 'Anzahl: '.$roh2.' B&ouml;gen</br>';
                                $html .= 'Rechnung: Abrunden(('.$format_in[0].' * '.$format_in[1].') / ('.$calc->getPaperContentHeight().' * '.$calc->getPaperContentWidth().')) / B&ouml;gen</br>';
                                $html .= '</td>';
                            } else {
                                $html .= '<td valign="top"><b>Inhalt:</b></td>';
                            }
                            break;
                        case Calculation::PAPER_ADDCONTENT:
                            if ($calc->getFormat_in_addcontent() != ""){
                                $format_in = explode("x", $calc->getFormat_in_addcontent());
                                $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperAddContentHeight() * $calc->getPaperAddContentWidth()));
                                $roh2 = ceil($sheets_addcontent / $roh);
                                $html .= '<td valign="top"><b>Zus. Inhalt:</b></br>';
                                $html .= 'Format: '.$calc->getFormat_in_addcontent().' mm</br>';
                                $html .= 'Anzahl: '.$roh2.' B&ouml;gen</br>';
                                $html .= 'Rechnung: Abrunden(('.$format_in[0].' * '.$format_in[1].') / ('.$calc->getPaperAddContentHeight().' * '.$calc->getPaperAddContentWidth().')) / B&ouml;gen</br>';
                                $html .= '</td>';
                            } else {
                                $html .= '<td valign="top"><b>Zus. Inhalt:</b></td>';
                            }
                            break;
                        case Calculation::PAPER_ADDCONTENT2:
                            if ($calc->getFormat_in_addcontent2() != ""){
                                $format_in = explode("x", $calc->getFormat_in_addcontent2());
                                $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperAddContent2Height() * $calc->getPaperAddContent2Width()));
                                $roh2 = ceil($sheets_addcontent2 / $roh);
                                $html .= '<td valign="top"><b>Zus. Inhalt 2:</b></br>';
                                $html .= 'Format: '.$calc->getFormat_in_addcontent2().' mm</br>';
                                $html .= 'Anzahl: '.$roh2.' B&ouml;gen</br>';
                                $html .= 'Rechnung: Abrunden(('.$format_in[0].' * '.$format_in[1].') / ('.$calc->getPaperAddContent2Height().' * '.$calc->getPaperAddContent2Width().')) / B&ouml;gen</br>';
                                $html .= '</td>';
                            } else {
                                $html .= '<td valign="top"><b>Zus. Inhalt 2:</b></td>';
                            }
                            break;
                        case Calculation::PAPER_ADDCONTENT3:
                            if ($calc->getFormat_in_addcontent3() != ""){
                                $format_in = explode("x", $calc->getFormat_in_addcontent3());
                                $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperAddContent3Height() * $calc->getPaperAddContent3Width()));
                                $roh2 = ceil($sheets_addcontent3 / $roh);
                                $html .= '<td valign="top"><b>Zus. Inhalt 3:</b></br>';
                                $html .= 'Format: '.$calc->getFormat_in_addcontent3().' mm</br>';
                                $html .= 'Anzahl: '.$roh2.' B&ouml;gen</br>';
                                $html .= 'Rechnung: Abrunden(('.$format_in[0].' * '.$format_in[1].') / ('.$calc->getPaperAddContent3Height().' * '.$calc->getPaperAddContent3Width().')) / B&ouml;gen</br>';
                                $html .= '</td>';
                            } else {
                                $html .= '<td valign="top"><b>Zus. Inhalt 3:</b></td>';
                            }
                            break;
                        case Calculation::PAPER_ENVELOPE:
                            if ($calc->getFormat_in_envelope() != ""){
                                $format_in = explode("x", $calc->getFormat_in_envelope());
                                $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperEnvelopeHeight() * $calc->getPaperEnvelopeWidth()));
                                $roh2 = ceil($sheets_envelope / $roh);
                                $html .= '<td valign="top"><b>Umschlag:</b></br>';
                                $html .= 'Format: '.$calc->getFormat_in_envelope().' mm</br>';
                                $html .= 'Anzahl: '.$roh2.' B&ouml;gen</br>';
                                $html .= 'Rechnung: Abrunden(('.$format_in[0].' * '.$format_in[1].') / ('.$calc->getPaperEnvelopeHeight().' * '.$calc->getPaperEnvelopeWidth().')) / B&ouml;gen</br>';
                                $html .= '</td>';
                            } else {
                                $html .= '<td valign="top"><b>Umschlag:</b></td>';
                            }
                            break;
                    }
                }
            }
            $html .= '</tr>';
            
            $html .= '<tr><td class="content_row_header" valign="top">Nutzen Rohb.</td><td class="content_row_clear">';
    		if ($calc->getPagesContent() > 0 && $calc->getPaperContent()->getId() > 0) {
    				$html .= '<b>Inhalt:</b></br>'; 
    				
                    $format_in = explode("x", $calc->getFormat_in_content());
                    $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperContentHeight() * $calc->getPaperContentWidth());
                	$html .= 'Nutzen: ' . (int)$roh_schnitte . '</br>';
			}
            $html .= '</td><td class="content_row_clear">';
            if ($calc->getPagesAddContent() > 0 && $calc->getPaperAddContent()->getId() > 0) {
    				$html .= '<b>Zus. Inhalt:</b></br>'; 
    				
                    $format_in = explode("x", $calc->getFormat_in_addcontent());
                    $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperAddContentHeight() * $calc->getPaperAddContentWidth());
                	$html .= 'Nutzen: ' . (int)$roh_schnitte . '</br>';
            }
            $html .= '</td><td class="content_row_clear">';
            if ($calc->getPagesAddContent2() > 0 && $calc->getPaperAddContent3()->getId() > 0) {
    				$html .= '<b>Zus. Inhalt 2:</b></br>'; 
    				
                    $format_in = explode("x", $calc->getFormat_in_addcontent2());
                    $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperAddContent2Height() * $calc->getPaperAddContent2Width());
                	$html .= 'Nutzen: ' . (int)$roh_schnitte . '</br>';
            }
            $html .= '</td><td class="content_row_clear">';
            if ($calc->getPagesAddContent2() > 0 && $calc->getPaperAddContent3()->getId() > 0) {
    				$html .= '<b>Zus. Inhalt 3:</b></br>'; 
    				
                    $format_in = explode("x", $calc->getFormat_in_addcontent3());
                    $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperAddContent3Height() * $calc->getPaperAddContent3Width());
                	$html .= 'Nutzen: ' . (int)$roh_schnitte . '</br>';
            }
            $html .= '</td><td class="content_row_clear">';
            if ($calc->getPagesEnvelope() > 0 && $calc->getPaperEnvelope()->getId() > 0) {
    				echo '<b>Umschlag:</b></br>'; 
    				
                    $format_in = explode("x", $calc->getFormat_in_envelope());
                    $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperEnvelopeHeight() * $calc->getPaperEnvelopeWidth());
                	$html .= 'Nutzen: ' . (int)$roh_schnitte . '</br>';
            }

            $html .= '</td></tr>';
            $html .= '</table></div><br><h3>Fertigungsprozess</h3>';
            $html .= '<div class="outer"><table cellpadding="0" cellspacing="0" border="0" width="100%">';
            $html .= '<colgroup><col width="15%"><col width="35%"><col width="15%"><col width="35%"></colgroup>';
            
            foreach(MachineGroup::getAllMachineGroups(MachineGroup::ORDER_POSITION) as $mg) {
                $machentries = Machineentry::getAllMachineentries($calc->getId(), Machineentry::ORDER_ID, $mg->getId()); 
                if(count($machentries) > 0)
                {
                    $html .= '<tr><td valign="top"><b>'.$mg->getName().'</b></td>';
                    $html .= '<td valign="top">';
                    
                    foreach($machentries as $me) {
                        $html .= 'Maschine '.$me->getMachine()->getName();
                        
                        if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ||
                               $me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET ||
                               $me->getMachine()->getType() == Machine::TYPE_FOLDER) {
                                switch($me->getPart())
                                {
                                    case Calculation::PAPER_CONTENT:
                                        $html .= '(Inhalt)';
                                        break;
                                    case Calculation::PAPER_ADDCONTENT:
                                        $html .= '(zus. Inhalt)';
                                        break;
                                    case Calculation::PAPER_ENVELOPE:
                                        $html .= '(Umschlag)';
                                        break;
                                    case Calculation::PAPER_ADDCONTENT2:
                                    	$html .= '(zus. Inhalt 2)';
                                    	break;
        							case Calculation::PAPER_ADDCONTENT3:
                                        $html .= '(zus. Inhalt 3)';
                                      	break;
                                }
                        }
                        $html .= '<br>';
                        
                        if($me->getMachine()->getType() == Machine::TYPE_CTP) { 
                            $html .= 'Anzahl Druckplatten: '.$calc->getPlateCount();
                            $html .= '<br>';
                        }
                        
                        if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) {
                            $html .= 'Druckart: ';
                            if ((int)$me->getUmschl() == 1)
                                $html .= 'Umschlagen';
                            elseif ((int)$me->getUmst() == 1)
                                $html .= 'Umscht&uuml;lpen';
                            else
                                $html .= 'Sch&ouml;n & Wider';
                            $html .= '</br>';
                        }
                        
                        $html .= 'Grundzeit: '.$me->getMachine()->getTimeBase().' min.,';
                        
                        if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) {
                            $html .= 'Einrichtzeit Druckplatten:';
                            $html .= $calc->getPlateCount($me) * $me->getMachine()->getTimePlatechange().' min.';
                            $html .= 'Laufzeit:';
                            $html .= $me->getTime() - ($calc->getPlateCount($me) * $me->getMachine()->getTimePlatechange()) - $me->getMachine()->getTimeBase().' min.';
                        } else {
                            $html .= 'Laufzeit inkl. maschinenspez. R&uuml;stzeiten:';
                            $html .= $me->getTime() - $me->getMachine()->getTimeBase().' min.';
                        }
                        
                        $html .= '<br>';
                        $html .= 'Zeit: '.$me->getTime().' min.,';
                        $html .= 'Preis: '.printPrice($me->getPrice()).' €<br>';
                        $html .= '<br>';
                    }
                    
                    $html .= '</td></tr>';
                }
            }
            
        	if (count($calc->getPositions())>0 && $calc->getPositions() != FALSE) {
        	    $html .= '<tr><td valign="top"><b>Zus. Positionen</b></td>';
        	    $html .= '<td>';

        	    foreach($calc->getPositions() as $pos){
        	        $html .= $pos->getComment() ." : ";
        	        $html .= printPrice($pos->getCalculatedPrice())." ".$_USER->getClient()->getCurrency()."<br/>";
        	        $html .= '<br/>';
        	    }
        	    $html .= '</td></tr>';
            }
            
            $html .= '</table></div><br><div class="outer"><table cellpadding="0" cellspacing="0" border="0" width="100%">';
            $html .= '<colgroup><col width="15%"><col width="35%"><col width="15%"><col width="35%"></colgroup><tr>';
            $html .= '<td valign="top"><b>Produktionskosten:</b></td><td valign="top"><b>';
            $html .= printPrice($calc->getPricesub()).' €</b>';
            $html .= '</td></tr></table></div><br>';
            
            $i++;
        }
        
        return $html;
    }
    
    public function clearId()
    {
        $this->id = 0;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setNumber($number)
    {
        $this->number = $number;
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

    public function getProduct()
    {
        return $this->product;
    }

    public function setProduct($product)
    {
        $this->product = $product;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    public function getCrtdat()
    {
        return $this->crtdat;
    }

    public function getUpddat()
    {
        return $this->upddat;
    }

    public function getCollectiveinvoiceId()
    {
        return $this->collectiveinvoiceId;
    }

    public function setCollectiveinvoiceId($collectiveinvoiceId)
    {
        $this->collectiveinvoiceId = $collectiveinvoiceId;
    }

    public function getInternContact()
    {
        return $this->internContact;
    }

    public function setInternContact($internContact)
    {
        $this->internContact = $internContact;
    }

	public function getProductName()
	{
	    return $this->productName;
	}

	public function setProductName($productName)
	{
	    $this->productName = $productName;
	}
	
	/**
     * @return the $beilagen
     */
    public function getBeilagen()
    {
        return $this->beilagen;
    }

	/**
     * @param field_type $beilagen
     */
    public function setBeilagen($beilagen)
    {
        $this->beilagen = $beilagen;
    }
    
	/**
     * @return the $crtusr
     */
    public function getCrtusr()
    {
        return $this->crtusr;
    }
    
	/**
     * @return the $articleid
     */
    public function getArticleid()
    {
        return $this->articleid;
    }

	/**
     * @param field_type $articleid
     */
    public function setArticleid($articleid)
    {
        $this->articleid = $articleid;
    }
}