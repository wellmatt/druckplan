<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       20.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/paperformats/paperformat.class.php';
require_once 'libs/modules/paper/paper.class.php';
require_once 'libs/modules/foldtypes/foldtype.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'calculation.machineentry.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'calculation.position.class.php';

class Calculation
{
    const ORDER_AMOUNT = "product_amount";
    const ORDER_ID = "id";
    
    const PAPER_CONTENT 	= 1;
    const PAPER_ADDCONTENT 	= 2;
    const PAPER_ENVELOPE 	= 3;
    const PAPER_ADDCONTENT2 = 4;
    const PAPER_ADDCONTENT3 = 5;
    
    private $id;
    private $orderId = 0;
    private $state = 1;
    private $productFormat = null;
    private $productFormatWidth = 0;
    private $productFormatHeight = 0;
    private $productFormatWidthOpen = 0;
    private $productFormatHeightOpen = 0;
    private $pagesContent = 0;
    private $pagesAddContent = 0;
    private $pagesEnvelope = 0;
    private $amount = 0;
    private $sorts = 1;
    private $paperContent = null;
    private $paperContentWidth = 0;
    private $paperContentHeight = 0;
    private $paperContentWeight = 0;
    private $paperAddContent = null;
    private $paperAddContentWidth = 0;
    private $paperAddContentHeight = 0;
    private $paperAddContentWeight = 0;
    private $paperEnvelope = null;
    private $paperEnvelopeWidth = 0;
    private $paperEnvelopeHeight = 0;
    private $paperEnvelopeWeight = 0;
    private $envelopeHeightOpen = 0;
    private $envelopeWidthOpen = 0;
    private $folding;
    private $addCharge = 0;
    private $margin = 0;
    private $discount = 0;
    private $calcAutoValues = 1;
    private $chromaticitiesContent = null;
    private $chromaticitiesAddContent = null;
    private $chromaticitiesEnvelope = null;
    private $paperContentGrant = 0;
    private $paperAddContentGrant = 0;
    private $paperEnvelopeGrant = 0;
    private $textProcessing;
    private $foldschemeContent;
    private $foldschemeAddContent;
    private $foldschemeEnvelope;
    private $articles= NULL;
    private $articleamounts= NULL;
    private $articlescales= NULL;
    
    private $cutContent = 3.0;					// Anschnitt fuer den Inhalt
    private $cutAddContent = 3.0;				// Anschnitt fuer den zusl. Inhalt
	private $cutAddContent2 = 3.0;				// Anschnitt fuer den zusl. Inhalt 2
	private $cutAddContent3 = 3.0;				// Anschnitt fuer den zusl. Inhalt 3
    private $cutEnvelope = 3.0;					// Anschnitt fuer Umschlag 
    private $colorControl = 1;					// Farbkontrollstreifen anzeigen 
	
    // Zus. Inhalt 2
    private $pagesAddContent2 = 0;
    private $paperAddContent2 = null;
    private $paperAddContent2Width = 0;
    private $paperAddContent2Height = 0;
    private $paperAddContent2Weight = 0;
    private $paperAddContent2Grant = 0;
    private $chromaticitiesAddContent2 = null;
    private $foldschemeAddContent2;
    
    // Zus. Inhalt 3
    private $pagesAddContent3 = 0;
    private $paperAddContent3 = null;
    private $paperAddContent3Width = 0;
    private $paperAddContent3Height = 0;
    private $paperAddContent3Weight = 0;
    private $paperAddContent3Grant = 0;
    private $chromaticitiesAddContent3 = null;
    private $foldschemeAddContent3;
	
	// Schneidemachine (added by ascherer 22.07.14)
	
	private $cutter_weight = 0;
	private $cutter_height = 0;
	private $roll_dir = 0;
	
	private $title; // Kalkulation's Titel
    
    function __construct($id = 0) 
    {
        global $DB;
        
        $this->productFormat = new Paperformat();
        $this->paperContent = new Paper();
        $this->paperAddContent = new Paper();
        $this->paperEnvelope = new Paper();
        $this->folding = new Foldtype();
        $this->chromaticitiesContent= new Chromaticity();
        $this->chromaticitiesAddContent= new Chromaticity();
        $this->chromaticitiesEnvelope= new Chromaticity();
        
        $this->paperAddContent2 = new Paper();
        $this->chromaticitiesAddContent2= new Chromaticity();
        
        $this->paperAddContent3 = new Paper();
        $this->chromaticitiesAddContent3= new Chromaticity();
        
        if($id > 0)
        {
            $sql = "SELECT * FROM orders_calculations WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                $this->id = $r["id"];
                $this->orderId = $r["order_id"];
                $this->productFormat = new Paperformat($r["product_format"]);
                $this->productFormatHeight = $r["product_format_height"];
                $this->productFormatWidth = $r["product_format_width"];
                $this->productFormatHeightOpen = $r["product_format_height_open"];
                $this->productFormatWidthOpen = $r["product_format_width_open"];
                $this->pagesContent = $r["product_pages_content"];
                $this->pagesAddContent = $r["product_pages_addcontent"];
                $this->pagesEnvelope = $r["product_pages_envelope"];
                $this->amount = $r["product_amount"];
                $this->sorts = $r["product_sorts"];
                $this->paperContent = new Paper($r["paper_content"]);
                $this->paperContentHeight = $r["paper_content_height"];
                $this->paperContentWidth = $r["paper_content_width"];
                $this->paperContentWeight = $r["paper_content_weight"];
                $this->paperAddContent = new Paper($r["paper_addcontent"]);
                $this->paperAddContentHeight = $r["paper_addcontent_height"];
                $this->paperAddContentWidth = $r["paper_addcontent_width"];
                $this->paperAddContentWeight = $r["paper_addcontent_weight"];
                $this->paperEnvelope = new Paper($r["paper_envelope"]);
                $this->paperEnvelopeHeight = $r["paper_envelope_height"];
                $this->paperEnvelopeWidth = $r["paper_envelope_width"];
                $this->paperEnvelopeWeight = $r["paper_envelope_weight"];
                $this->envelopeHeightOpen = $r["envelope_height_open"];
                $this->envelopeWidthOpen = $r["envelope_width_open"];
                $this->folding = new Foldtype($r["product_folding"]);
                $this->addCharge = $r["add_charge"];
                $this->margin = $r["margin"];
                $this->discount = $r["discount"];
                $this->state = $r["state"];
                $this->calcAutoValues = $r["calc_auto_values"];
                $this->chromaticitiesContent= new Chromaticity($r["chromaticities_content"]);
                $this->chromaticitiesAddContent= new Chromaticity($r["chromaticities_addcontent"]);
                $this->chromaticitiesEnvelope= new Chromaticity($r["chromaticities_envelope"]);
                $this->paperContentGrant = $r["paper_content_grant"];
                $this->paperAddContentGrant = $r["paper_addcontent_grant"];
                $this->paperEnvelopeGrant = $r["paper_envelope_grant"];
                $this->textProcessing = $r["text_processing"];
                $this->foldschemeContent = $r["foldscheme_content"];
                $this->foldschemeAddContent = $r["foldscheme_addcontent"];
                $this->foldschemeEnvelope = $r["foldscheme_envelope"];
                
                // Anschnitt setzen
				$this->cutContent = $r["cut_content"];
                $this->cutAddContent = $r["cut_addcontent"];
				$this->cutAddContent2 = $r["cut_addcontent2"];
				$this->cutAddContent3 = $r["cut_addcontent3"];
                $this->cutEnvelope = $r["cut_envelope"];
                $this->colorControl = $r["color_control"];
                // Zus Inhalt 2 fuellen
                $this->pagesAddContent2 = $r["product_pages_addcontent2"];
                $this->paperAddContent2 = new Paper($r["paper_addcontent2"]);
                $this->paperAddContent2Width = $r["paper_addcontent2_width"];
                $this->paperAddContent2Height = $r["paper_addcontent2_height"];
                $this->paperAddContent2Weight = $r["paper_addcontent2_weight"];
                $this->paperAddContent2Grant = $r["paper_addcontent2_grant"];
                $this->chromaticitiesAddContent2 = new Chromaticity($r["chromaticities_addcontent2"]);
                $this->foldschemeAddContent2 = $r["foldscheme_addcontent2"];
                
                // Zus Inhalt 3 fuellen
                $this->pagesAddContent3 = $r["product_pages_addcontent3"];
                $this->paperAddContent3 = new Paper($r["paper_addcontent3"]);
                $this->paperAddContent3Width = $r["paper_addcontent3_width"];
                $this->paperAddContent3Height = $r["paper_addcontent3_height"];
                $this->paperAddContent3Weight = $r["paper_addcontent3_weight"];
                $this->paperAddContent3Grant = $r["paper_addcontent3_grant"];
                $this->chromaticitiesAddContent3 = new Chromaticity($r["chromaticities_addcontent3"]);
                $this->foldschemeAddContent3 = $r["foldscheme_addcontent3"];
				
				// Schneidemachine (added by ascherer 22.07.14)
				
				$this->cutter_weight = $r["cutter_weight"];
				$this->cutter_height = $r["cutter_height"];
				$this->roll_dir = $r["roll_dir"];
				
				$this->title = $r["title"];
                
                // configure Paper
                $this->paperContent->setSelectedWeight($this->paperContentWeight);
                $this->paperContent->setSelectedSize(Array("width" => $this->paperContentWidth, "height" => $this->paperContentHeight));
                $this->paperAddContent->setSelectedWeight($this->paperAddContentWeight);
                $this->paperAddContent->setSelectedSize(Array("width" => $this->paperAddContentWidth, "height" => $this->paperAddContentHeight));
                $this->paperEnvelope->setSelectedWeight($this->paperEnvelopeWeight);
                $this->paperEnvelope->setSelectedSize(Array("width" => $this->paperEnvelopeWidth, "height" => $this->paperEnvelopeHeight));
                $this->paperAddContent2->setSelectedWeight($this->paperAddContent2Weight);
                $this->paperAddContent2->setSelectedSize(Array("width" => $this->paperAddContent2Width, "height" => $this->paperAddContent2Height));
                $this->paperAddContent3->setSelectedWeight($this->paperAddContent3Weight);
                $this->paperAddContent3->setSelectedSize(Array("width" => $this->paperAddContent3Width, "height" => $this->paperAddContent3Height)); 
            }
            
            //--------------------------Artikel----------------------------------------------
            $sql = "SELECT * FROM orders_articles WHERE calc_id = {$id}";
            $all_art = Array();
            if($DB->num_rows($sql)){
            	$rows = $DB->select($sql);
            	foreach ($rows as $ro){
            		$all_art[] =  new Article($ro['article_id']);
            		$this->articleamounts[$ro['article_id']] = $ro['amount'];
            		$this->articlescales[$ro['article_id']] = $ro['scale'];
            	}
            	$this->articles = $all_art;
            }
        }
    }
    
    static function getAllCalculations($order, $itemorder = self::ORDER_ID)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM orders_calculations 
                WHERE order_id = {$order->getId()}
                ORDER BY {$itemorder}";
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new Calculation($r["id"]);
            }
        }
        return $retval;
    }
    
	//gln, speziell f�r Niemann, 
	//um geaenderte Werte in einem Teilauftrag automatisch in alle anderen Teilauftraege zu uebertragen
    static function getCalculationsForUpdate($order, $calc_geaendert, $itemorder = self::ORDER_ID)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id, product_amount FROM orders_calculations 
                WHERE order_id = {$order->getId()} and id <> {$calc_geaendert->getId()} 
                ORDER BY {$itemorder}";

        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new Calculation($r["id"]);
            }
        }
        return $retval;
    }

	//gln, speziell f�r Niemann, 
	//um geaenderte Werte in einem Teilauftrag automatisch in alle anderen Teilauftraege zu uebertragen
    static function deleteCalculationsForUpdate($order_id, $calc_geaendert)
    {
        	global $DB;
         	$sql = "DELETE FROM orders_machines  
                WHERE calc_id in (select id from orders_calculations WHERE order_id = {$order_id} and id <> {$calc_geaendert}) ";
// 			echo "geloescht in orders_machines fuer order_id ".$order_id." calc_geaendert ".$calc_geaendert.", ".$DB->num_rows($sql)." Saetze";
         	$sql = "DELETE FROM orders_machines  
                WHERE calc_id in (select id from orders_calculations WHERE order_id = {$order_id} and id <> {$calc_geaendert}) ";
// 			echo "geloescht in orders_calculations fuer order_id ".$order_id." calc_geaendert ".$calc_geaendert.", ".$DB->num_rows($sql)." Saetze";
        	
        	$sql = "DELETE FROM orders_calculations  
                WHERE order_id = {$order_id} and id <> {$calc_geaendert} ";
            if($DB->no_result($sql))
            {
                 return true;
            } else 
                return false;
    }
    
    function save()
    {
        global $DB;
        global $_USER;
        $set = "        order_id = {$this->orderId},
                        product_format = {$this->productFormat->getId()},
                        product_format_height = {$this->productFormatHeight},
                        product_format_width = {$this->productFormatWidth},
                        product_format_height_open = {$this->productFormatHeightOpen},
                        product_format_width_open = {$this->productFormatWidthOpen},
                        product_pages_content = {$this->pagesContent},
                        product_pages_addcontent = {$this->pagesAddContent},
                        product_pages_envelope = {$this->pagesEnvelope},
                        product_amount = {$this->amount},
                        product_sorts = {$this->sorts},
                        product_folding = {$this->folding->getId()},
                        paper_content = {$this->paperContent->getId()},
                        paper_content_width = {$this->paperContentWidth},
                        paper_content_height = {$this->paperContentHeight},
                        paper_content_weight = {$this->paperContentWeight},
                        paper_content_grant = {$this->paperContentGrant},
                        paper_addcontent = {$this->paperAddContent->getId()},
                        paper_addcontent_width = {$this->paperAddContentWidth},
                        paper_addcontent_height = {$this->paperAddContentHeight},
                        paper_addcontent_weight = {$this->paperAddContentWeight},
                        paper_addcontent_grant = {$this->paperAddContentGrant},
                        paper_envelope = {$this->paperEnvelope->getId()},
                        paper_envelope_width = {$this->paperEnvelopeWidth},
                        paper_envelope_height = {$this->paperEnvelopeHeight},
                        paper_envelope_weight = {$this->paperEnvelopeWeight},
                        paper_envelope_grant = {$this->paperEnvelopeGrant},
                        envelope_height_open = {$this->envelopeHeightOpen},
                        envelope_width_open = {$this->envelopeWidthOpen},
                        add_charge = {$this->addCharge},
                        margin = {$this->margin},
                        discount = {$this->discount},
                        chromaticities_content = {$this->chromaticitiesContent->getId()},
                        chromaticities_addcontent = {$this->chromaticitiesAddContent->getId()},
                        chromaticities_envelope = {$this->chromaticitiesEnvelope->getId()},
                        state = {$this->state},
                        calc_auto_values = {$this->calcAutoValues},
                        text_processing = '{$this->textProcessing}',
                        foldscheme_content = '{$this->foldschemeContent}',
                        foldscheme_addcontent = '{$this->foldschemeAddContent}',
                        foldscheme_envelope = '{$this->foldschemeEnvelope}',
	        			product_pages_addcontent2 = {$this->pagesAddContent2},
	        			paper_addcontent2 = {$this->paperAddContent2->getId()},
	                    paper_addcontent2_width = {$this->paperAddContent2Width},
	                    paper_addcontent2_height = {$this->paperAddContent2Height},
	                    paper_addcontent2_weight = {$this->paperAddContent2Weight},
	                    paper_addcontent2_grant = {$this->paperAddContent2Grant}, 
	        			chromaticities_addcontent2 = {$this->chromaticitiesAddContent2->getId()},
	        			foldscheme_addcontent2 = '{$this->foldschemeAddContent2}', 
	        			product_pages_addcontent3 = {$this->pagesAddContent3},
	        			paper_addcontent3 = {$this->paperAddContent3->getId()},
	                    paper_addcontent3_width = {$this->paperAddContent3Width},
	                    paper_addcontent3_height = {$this->paperAddContent3Height},
	                    paper_addcontent3_weight = {$this->paperAddContent3Weight},
	                    paper_addcontent3_grant = {$this->paperAddContent3Grant}, 
	        			chromaticities_addcontent3 = {$this->chromaticitiesAddContent3->getId()},
	        			foldscheme_addcontent3 = '{$this->foldschemeAddContent3}', 
						cut_content = {$this->cutContent}, 
                        cut_addcontent = {$this->cutAddContent}, 
						cut_addcontent2 = {$this->cutAddContent2}, 
						cut_addcontent3 = {$this->cutAddContent3}, 
        				cut_envelope = {$this->cutEnvelope},  
						cutter_weight =	{$this->cutter_weight},
						cutter_height =	{$this->cutter_height},
						roll_dir =	{$this->roll_dir},
						title =	'{$this->title}',
        				color_control = {$this->colorControl}, ";
        
        if($this->id > 0){
        	// Erst Artikel speichern
        	$sql = "DELETE FROM orders_articles WHERE calc_id = {$this->id}";
        	$DB->no_result($sql);
        	// echo mysql_error();
        	
        	if (count($this->articleamounts) > 0){
	        	$sql = "INSERT INTO orders_articles (calc_id, article_id, amount, scale) VALUES";
	        	foreach ($this->articleamounts as $key => $value){
	        		$sql .= "( {$this->id}, {$key} , {$value} , {$this->articlescales[$key]}), ";
	        	}
	        	$sql = substr($sql, 0, -2); // Das letzte Komma und Leerzeichen entfernen
	        	$DB->no_result($sql);
				// echo $sql . "</br>";
	        	// echo mysql_error();
        	}
        	       	
        	// Dann Kalkulation speichern
            $sql = "UPDATE orders_calculations
                    SET
                        {$set}
                        upddat = UNIX_TIMESTAMP(),
                        updusr = {$_USER->getId()}
                    WHERE id = {$this->id}";
            return $DB->no_result($sql);
// 			echo $sql . "</br>";
        } else
        {
            $sql = "INSERT INTO orders_calculations SET 
                    {$set}
                    crtdat = UNIX_TIMESTAMP(),
                    crtusr = {$_USER->getId()}";
			//error_log("CALC-SQL: ".$sql);
            $res = $DB->no_result($sql);
// 			echo $sql . "</br>";
            //error_log("CALC-Err: ".mysql_error());
            if($res)
            {
                $sql = "SELECT max(id) id FROM orders_calculations WHERE order_id = {$this->orderId}";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else
                return false;
        }
    }

    public function delete()
    {
        global $DB;
        if($this->id > 0)
        {
            $sql = "DELETE FROM orders_calculations WHERE id = {$this->id}";
            if($DB->no_result($sql))
            {
                unset($this);
                return true;
            } else 
                return false;
        }
    }
    
    public function getPaperCount($papertype)
    {
        $productsPerPaper = $this->getProductsPerPaper($papertype);

        // Papiertyp nicht angegeben
        if($productsPerPaper == 0)
            return 0;

        if ($papertype == Calculation::PAPER_CONTENT)
        {
            return ceil($this->amount * $this->pagesContent / $productsPerPaper);
        } else if($papertype == Calculation::PAPER_ADDCONTENT)
        {
            return ceil($this->amount * $this->pagesAddContent / $productsPerPaper);
        } else if($papertype == Calculation::PAPER_ENVELOPE)
        {
            return ceil($this->amount * $this->pagesEnvelope / $productsPerPaper);
        } else if($papertype == Calculation::PAPER_ADDCONTENT2)
        {
            return ceil($this->amount * $this->pagesAddContent2 / $productsPerPaper);
        } else if($papertype == Calculation::PAPER_ADDCONTENT3)
        {
            return ceil($this->amount * $this->pagesAddContent3 / $productsPerPaper);
        }            
    }

    function getProductsPerPaper($ptype)
    {
        global $_CONFIG;
        // Papiergroesse auswaehlen
        if($ptype == Calculation::PAPER_CONTENT)
        {
            $paperH = $this->paperContentHeight;
            $paperW = $this->paperContentWidth;
        } else if ($ptype == Calculation::PAPER_ADDCONTENT)
        {
            $paperH = $this->paperAddContentHeight;
            $paperW = $this->paperAddContentWidth;
        } elseif($ptype == Calculation::PAPER_ENVELOPE)
        {
            $paperH = $this->paperEnvelopeHeight;
            $paperW = $this->paperEnvelopeWidth;
        } else if ($ptype == Calculation::PAPER_ADDCONTENT2)
        {
            $paperH = $this->paperAddContent2Height;
            $paperW = $this->paperAddContent2Width;
        } else if ($ptype == Calculation::PAPER_ADDCONTENT3)
        {
            $paperH = $this->paperAddContent3Height;
            $paperW = $this->paperAddContent3Width;
        } 
            
        if($ptype != Calculation::PAPER_ENVELOPE)
        {
            $width = $this->productFormatWidthOpen;
            $height = $this->productFormatHeightOpen;
        } else {
            $width = $this->envelopeWidthOpen;
            $height = $this->envelopeHeightOpen;
        }
        $width_closed     = $this->productFormatWidth;
        $height_closed     = $this->productFormatHeight;
        
        // Wie oft geschlossenes Format in offenem Format?
        if ($width_closed < $width && $width_closed != 0 )
            $anz_rows = floor(ceil($width * 1.01) / $width_closed);
        else
            $anz_rows = 1;
        if ($height_closed < $height && $height_closed != 0 )
            $anz_rows = floor(ceil($height * 1.01) / $height_closed);
        else
            $anz_cols = 1;
        
        // Maschine fuer ausgewaehlten Papiertyp
        $mach = Machineentry::getMachineForPapertype($ptype, $this->id);
        if($mach)
        {
            $calc = new Calculation($mach[0]->getCalcId());
            if($mach[0]->getPart() == Calculation::PAPER_CONTENT)
                $chr = $calc->getChromaticitiesContent();
            else if ($mach[0]->getPart() == Calculation::PAPER_ADDCONTENT)
                $chr = $calc->getChromaticitiesAddContent();
            else if($mach[0]->getPart() == Calculation::PAPER_ENVELOPE)
                $chr = $calc->getChromaticitiesEnvelope();
            else if ($mach[0]->getPart() == Calculation::PAPER_ADDCONTENT2)
            	$chr = $calc->getChromaticitiesAddContent2();
            else if ($mach[0]->getPart() == Calculation::PAPER_ADDCONTENT3)
            	$chr = $calc->getChromaticitiesAddContent3();
            
            if($chr->getReversePrinting())
                $duplex = 2;
            else 
                $duplex = 1;
            // Anschnitt setzen
            $tmp_anschnitt = $_CONFIG->anschnitt;
            if($ptype == Calculation::PAPER_CONTENT){
            	$tmp_anschnitt = $calc->getCutContent();
            } else if ($ptype == Calculation::PAPER_ADDCONTENT){
            	$tmp_anschnitt = $calc->getCutAddContent();
            } else if ($ptype == Calculation::PAPER_ADDCONTENT2){
            	$tmp_anschnitt = $calc->getCutAddContent2();
            } else if ($ptype == Calculation::PAPER_ADDCONTENT3){
            	$tmp_anschnitt = $calc->getCutAddContent3();
            } elseif($ptype == Calculation::PAPER_ENVELOPE){
            	$tmp_anschnitt = $calc->getCutEnvelope();
            }
            
            // Farbrand (Farbkontrollstreifen) setzen
            $tmp_farbrand = $_CONFIG->farbRandBreite;
            if($calc->getColorControl() == 0){
            	// Wenn der Farbrand in der Kalkulation ausgestellt ist
            	$tmp_farbrand = 0;
            }
        
            // Papier um nicht bedruckbaren Bereich verkleinern
            $paperH = $paperH - $mach[0]->getMachine()->getBorder_bottom() - $mach[0]->getMachine()->getBorder_top() - $_CONFIG->farbRandBreite;
            $paperW = $paperW - $mach[0]->getMachine()->getBorder_left() - $mach[0]->getMachine()->getBorder_right();
    
            // Ausrechnen
            $productRows = floor($paperH / ($height + $_CONFIG->anschnitt * 2));
            $productCols = floor($paperW / ($width + $_CONFIG->anschnitt * 2));
            $productPerPaper1 = $productCols * $productRows;
            
            $productCols = floor($paperW / ($height + $_CONFIG->anschnitt * 2));
            $productRows = floor($paperH / ($width + $_CONFIG->anschnitt * 2));
            $productPerPaper2 = $productCols * $productRows;
                        
            if($productPerPaper1 > $productPerPaper2)
                $rv = $productPerPaper1 * $anz_cols * $anz_rows * $duplex;
            else 
                $rv = $productPerPaper2 * $anz_cols * $anz_rows * $duplex;
            
            return $rv;
        } else
            return 0;
    }
    
    public function getPlateCount($machineEntry = null) {
        $plates = 0;
        if($machineEntry == null)
        {
            $machEntries = Machineentry::getAllMachineentries($this->getId());

            foreach ($machEntries as $me)
            {
				$tmp_plates = 0;
                $calc = new Calculation($me->getCalcId());
                $order = new Order($calc->getOrderId());
                if($me->getPart() == Calculation::PAPER_CONTENT)
                    $chr = $calc->getChromaticitiesContent();
                else if ($me->getPart() == Calculation::PAPER_ADDCONTENT)
                    $chr = $calc->getChromaticitiesAddContent();
                else if ($me->getPart() == Calculation::PAPER_ENVELOPE)
                    $chr = $calc->getChromaticitiesEnvelope();
                else if ($me->getPart() == Calculation::PAPER_ADDCONTENT2)
                	$chr = $calc->getChromaticitiesAddContent2();
                else if ($me->getPart() == Calculation::PAPER_ADDCONTENT3)
                	$chr = $calc->getChromaticitiesAddContent3();
                
                if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
                {
                    $papers = 0;
                    if($me->getPart() == Calculation::PAPER_CONTENT && $order->getProduct()->getSingleplateset() == 0) 
                    {
                        $papers = $this->getPagesContent() / $this->getProductsPerPaper(Calculation::PAPER_CONTENT); 
                    } else if($me->getPart() == Calculation::PAPER_ADDCONTENT && $order->getProduct()->getSingleplateset() == 0)
                    {
                        $papers = $this->getPagesAddContent() / $this->getProductsPerPaper(Calculation::PAPER_ADDCONTENT); 
                    } else if($me->getPart() == Calculation::PAPER_ENVELOPE && $order->getProduct()->getSingleplateset() == 0)
                    {
                        $papers = $this->getPagesEnvelope() / $this->getProductsPerPaper(Calculation::PAPER_ENVELOPE); 
                    } else if($me->getPart() == Calculation::PAPER_ADDCONTENT2 && $order->getProduct()->getSingleplateset() == 0)
                    {
                        $papers = $this->getPagesAddContent2() / $this->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2); 
                    }
                    else if($me->getPart() == Calculation::PAPER_ADDCONTENT3 && $order->getProduct()->getSingleplateset() == 0)
                    {
                    	$papers = $this->getPagesAddContent3() / $this->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3);
                    }
                    if ($order->getProduct()->getSingleplateset() == 0){
                        $tmp_plates += $chr->getColorsFront() * ceil($papers);
                        $tmp_plates += $chr->getColorsBack() * ceil($papers); // round($papers, 0, PHP_ROUND_HALF_DOWN);
                    } elseif ($order->getProduct()->getSingleplateset() == 1 && $me->getPart() == Calculation::PAPER_CONTENT){
                        $tmp_plates += $chr->getColorsFront();
                        $tmp_plates += $chr->getColorsBack();
                    }
	            	//gln, umschlagen/umstuelpen
            		//if($me->getMachine()->getUmschlUmst())
            		if($me->getUmschlagenUmstuelpen() && $order->getProduct()->getSingleplateset() == 0)
        	        {
       	        	   $plates += ceil($tmp_plates / 2);
        	        } else {
        	           $plates += $tmp_plates;
        	        }
                }
            }
        } else
        {
            if($machineEntry->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
            {
                $calc = new Calculation($machineEntry->getCalcId());
                $order = new Order($calc->getOrderId());
                
                if($machineEntry->getPart() == Calculation::PAPER_CONTENT)
                    $chr = $calc->getChromaticitiesContent();
                else if ($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT)
                    $chr = $calc->getChromaticitiesAddContent();
                else if ($machineEntry->getPart() == Calculation::PAPER_ENVELOPE)
                    $chr = $calc->getChromaticitiesEnvelope();
                else if ($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT2)
                	$chr = $calc->getChromaticitiesAddContent2();
                else if ($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT3)
                	$chr = $calc->getChromaticitiesAddContent3();
                
                $papers = 0;
                if($machineEntry->getPart() == Calculation::PAPER_CONTENT && $order->getProduct()->getSingleplateset() == 0)
                {
                    $papers = $this->getPagesContent() / $this->getProductsPerPaper(Calculation::PAPER_CONTENT);
                } else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT && $order->getProduct()->getSingleplateset() == 0)
                {
                    $papers = $this->getPagesAddContent() / $this->getProductsPerPaper(Calculation::PAPER_ADDCONTENT);
                } else if($machineEntry->getPart() == Calculation::PAPER_ENVELOPE && $order->getProduct()->getSingleplateset() == 0)
                {
                    $papers = $this->getPagesEnvelope() / $this->getProductsPerPaper(Calculation::PAPER_ENVELOPE);
                } else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT2 && $order->getProduct()->getSingleplateset() == 0)
                {
                    $papers = $this->getPagesAddContent2() / $this->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2);
                } else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT3 && $order->getProduct()->getSingleplateset() == 0)
                {
                    $papers = $this->getPagesAddContent3() / $this->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3);
                }
                if ($order->getProduct()->getSingleplateset() == 0){
                    $tmp_plates += $chr->getColorsFront() * ceil($papers);
                    $tmp_plates += $chr->getColorsBack() * ceil($papers); // round($papers, 0, PHP_ROUND_HALF_DOWN);
                } elseif ($order->getProduct()->getSingleplateset() == 1 && $machineEntry->getPart() == Calculation::PAPER_CONTENT){
                    $tmp_plates += $chr->getColorsFront();
                    $tmp_plates += $chr->getColorsBack();
                }
            	//gln, umschlagen/umstuelpen
           		//if($machineEntry->getMachine()->getUmschlUmst())
           		if($machineEntry->getUmschlagenUmstuelpen() && $order->getProduct()->getSingleplateset() == 0)
       	        {
   	        	   $plates += ceil($tmp_plates / 2);
    	        } else {
    	           $plates += $tmp_plates;
    	        }
 
            }
            
        }
        return $plates;
    }
    
    public function getPlateSetCount($machineEntry = null) {
        $platesets = 0;
        if($machineEntry == null)
        {
            $machEntries = Machineentry::getAllMachineentries($this->getId());
    
            foreach ($machEntries as $me)
            {
                $calc = new Calculation($me->getCalcId());
    
                if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
                {
                    $papers = 0;
                    if($me->getPart() == Calculation::PAPER_CONTENT)
                    {
                        $papers = $this->getPagesContent() / $this->getProductsPerPaper(Calculation::PAPER_CONTENT);
                    } else if($me->getPart() == Calculation::PAPER_ADDCONTENT)
                    {
                        $papers = $this->getPagesAddContent() / $this->getProductsPerPaper(Calculation::PAPER_ADDCONTENT);
                    } else if($me->getPart() == Calculation::PAPER_ENVELOPE)
                    {
                        $papers = $this->getPagesEnvelope() / $this->getProductsPerPaper(Calculation::PAPER_ENVELOPE);
                    } else if($me->getPart() == Calculation::PAPER_ADDCONTENT2)
                    {
                        $papers = $this->getPagesAddContent2() / $this->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2);
                    } else if($me->getPart() == Calculation::PAPER_ADDCONTENT3)
                    {
                        $papers = $this->getPagesAddContent3() / $this->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3);
                    }
                    $tmp_platesets += ceil($papers);
                    $tmp_platesets += round($papers, 0, PHP_ROUND_HALF_DOWN);
	            	//gln, umschlagen/umstuelpen
	           		//if($me->getMachine()->getUmschlUmst())
	           		if($me->getUmschlagenUmstuelpen())
	       	        {
	       	        	$platesets += ceil($tmp_platesets / 2);
	       	        } else {
	       	            $platesets += $tmp_platesets;
	       	        }
                }
            }
        } else
        {
            if($machineEntry->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
            {
                $calc = new Calculation($machineEntry->getCalcId());
    
                $papers = 0;
                if($machineEntry->getPart() == Calculation::PAPER_CONTENT)
                {
                    $papers = $this->getPagesContent() / $this->getProductsPerPaper(Calculation::PAPER_CONTENT);
                } else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT)
                {
                    $papers = $this->getPagesAddContent() / $this->getProductsPerPaper(Calculation::PAPER_ADDCONTENT);
                } else if($machineEntry->getPart() == Calculation::PAPER_ENVELOPE)
                {
                    $papers = $this->getPagesEnvelope() / $this->getProductsPerPaper(Calculation::PAPER_ENVELOPE);
                } else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT2)
                {
                    $papers = $this->getPagesAddContent2() / $this->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2);
                } else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT3)
                {
                    $papers = $this->getPagesAddContent3() / $this->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3);
                }
                $tmp_platesets += ceil($papers);
                $tmp_platesets += round($papers, 0, PHP_ROUND_HALF_DOWN);
            	//gln, umschlagen/umstuelpen
           		//if($me->getMachine()->getUmschlUmst())
           		if($machineEntry->getUmschlagenUmstuelpen())
       	        {
       	        	$platesets += ceil($tmp_platesets / 2);
       	        } else {
       	            $platesets += $tmp_platesets;
       	        }
            }
    
        }
        return $platesets;
    }
    
    public function getSummaryPrice()
    {
        $sum = $this->getSubTotal();
        $sum += $sum * $this->getMargin() / 100; // Mage
        $sum -= $sum * $this->getDiscount() / 100; // Rabatt
        $sum += $this->getAddCharge(); // Sonstiger Auf/Abschlag
        return $sum;
        
    }
    
    public function getSubTotal(){
    	$sum = 0;
    	
        $me = Machineentry::getAllMachineentries($this->getId());
        foreach($me as $m){
            $machines[$m->getMachine()->getId()] += $m->getPrice();
        }
        
        /****
        // Summe der Kosten fuer die zusaetzlichen Artikel
        $total_article_price = 0;	 
        $all_calc_article = $this->articles;
		if (count($all_calc_article) > 0){
			foreach ($all_calc_article as $calc_art){
				$tmpart_amount = $this->getArticleamount($calc_art->getId());
				$tmpart_scale = $this->getArticlescale($calc_art->getId());
				if ($tmpart_scale == 0){
					$tmp_price = ($tmpart_amount * $calc_art->getPrice($tmpart_amount));
				} elseif ($tmpart_scale == 1){
					$tmp_price = ($tmpart_amount * $calc_art->getPrice($tmpart_amount * $this->amount) * $this->amount);
				}
				$total_article_price += $tmp_price; 
			}
		}
        
		$sum += $total_article_price;
		***/
        
        // Kosten der Positionen aufsummieren
        $total_position_price = 0;
        $all_positions = CalculationPosition::getAllCalculationPositions($this->id);
        if (count($all_positions) > 0){
        	foreach ($all_positions AS $pos){
        		$total_position_price += $pos->getCalculatedPrice();
        	}
        }
        $sum += $total_position_price;
        
        $sum += $this->getPaperContent()->getSumPrice($this->getPaperCount(Calculation::PAPER_CONTENT) + $this->paperContentGrant);
        $sum += $this->getPaperAddContent()->getSumPrice($this->getPaperCount(Calculation::PAPER_ADDCONTENT) + $this->paperAddContentGrant);
        $sum += $this->getPaperEnvelope()->getSumPrice($this->getPaperCount(Calculation::PAPER_ENVELOPE) + $this->paperEnvelopeGrant);
        $sum += $this->getPaperAddContent2()->getSumPrice($this->getPaperCount(Calculation::PAPER_ADDCONTENT2) + $this->paperAddContentGrant2);
        $sum += $this->getPaperAddContent3()->getSumPrice($this->getPaperCount(Calculation::PAPER_ADDCONTENT3) + $this->paperAddContentGrant3);
        foreach ($machines as $m) {
            $sum += $m;
        }
        return $sum;
        
    }
    
    public function getAvailableFoldschemes()
    {
        if($this->pagesContent)
        {
            $schemes[1] = Array();
            $x = 0;
            
            // Beginn mit 16er Bogen
            $schemes[1][$x][16] = (int)($this->pagesContent / 16);
            $rest = $this->pagesContent % 16;
            if($rest > 0)
            {
                $schemes[1][$x][8] = (int)($rest / 8);
                $rest = $rest % 8;
            }
            
            if($rest > 0)
            {
                $schemes[1][$x][4] = (int)($rest / 4);
            }
            $x++;
            
            // Beginn mit 8er Bogen
            $schemes[1][$x][8] = (int)($this->pagesContent / 8);
            $rest = $rest % 8;
            if($rest > 0)
            {
                $schemes[1][$x][4] = (int)($rest / 4);
            }
            
            $x++;
            // Beginn mit 4er Bogen
            $schemes[1][$x][4] = (int)($this->pagesContent / 4);
        }
        
        if($this->pagesAddContent)
        {
            $schemes[2] = Array();
            $x = 0;
        
            // Beginn mit 16er Bogen
            $schemes[2][$x][16] = (int)($this->pagesAddContent / 16);
            $rest = $this->pagesAddContent % 16;
            if($rest > 0)
            {
                $schemes[2][$x][8] = (int)($rest / 8);
                $rest = $rest % 8;
            }
        
            if($rest > 0)
            {
                $schemes[2][$x][4] = (int)($rest / 4);
            }
            $x++;
        
            // Beginn mit 8er Bogen
            $schemes[2][$x][8] = (int)($this->pagesAddContent / 8);
            $rest = $rest % 8;
            if($rest > 0)
            {
                $schemes[2][$x][4] = (int)($rest / 4);
            }
        
            $x++;
            // Beginn mit 4er Bogen
            $schemes[2][$x][4] = (int)($this->pagesAddContent / 4);
        }
        
        if($this->pagesEnvelope)
        {
            $schemes[3][0][$this->pagesEnvelope] = 1;
        }
        
        if($this->pagesAddContent2)
        {
        	$schemes[4] = Array();
        	$x = 0;
        
        	// Beginn mit 16er Bogen
        	$schemes[4][$x][16] = (int)($this->pagesAddContent2 / 16);
        	$rest = $this->pagesAddContent2 % 16;
        	if($rest > 0)
        	{
        		$schemes[4][$x][8] = (int)($rest / 8);
        		$rest = $rest % 8;
        	}
        
        	if($rest > 0)
        	{
        		$schemes[4][$x][4] = (int)($rest / 4);
        	}
        	$x++;
        
        	// Beginn mit 8er Bogen
        	$schemes[4][$x][8] = (int)($this->pagesAddContent / 8);
        	$rest = $rest % 8;
        	if($rest > 0)
        	{
        		$schemes[4][$x][4] = (int)($rest / 4);
        	}
        
        	$x++;
        	// Beginn mit 4er Bogen
        	$schemes[4][$x][4] = (int)($this->pagesAddContent / 4);
        }
        
        if($this->pagesAddContent3)
        {
        	$schemes[5] = Array();
        	$x = 0;
        
        	// Beginn mit 16er Bogen
        	$schemes[5][$x][16] = (int)($this->pagesAddContent3 / 16);
        	$rest = $this->pagesAddContent3 % 16;
        	if($rest > 0)
        	{
        		$schemes[5][$x][8] = (int)($rest / 8);
        		$rest = $rest % 8;
        	}
        
        	if($rest > 0)
        	{
        		$schemes[5][$x][4] = (int)($rest / 4);
        	}
        	$x++;
        
        	// Beginn mit 8er Bogen
        	$schemes[5][$x][8] = (int)($this->pagesAddContent3 / 8);
        	$rest = $rest % 8;
        	if($rest > 0)
        	{
        		$schemes[5][$x][4] = (int)($rest / 4);
        	}
        
        	$x++;
        	// Beginn mit 4er Bogen
        	$schemes[5][$x][4] = (int)($this->pagesAddContent3 / 4);
        }
        
        return $schemes;
    }
    
    public function getSumFoldingSheets()
    {
        $sum = 0;
        foreach(explode(",", $this->foldschemeContent) as $fs)
        {
            $fs = explode("x", $fs);
            $sum += $fs[0];
        }
        
        foreach(explode(",", $this->foldschemeAddContent) as $fs)
        {
            $fs = explode("x", $fs);
            $sum += $fs[0];
        }
        
        foreach(explode(",", $this->foldschemeEnvelope) as $fs)
        {
            $fs = explode("x", $fs);
            $sum += $fs[0];
        }
        
        foreach(explode(",", $this->foldschemeAddContent2) as $fs)
        {
        	$fs = explode("x", $fs);
        	$sum += $fs[0];
        }
        
        foreach(explode(",", $this->foldschemeAddContent3) as $fs)
        {
        	$fs = explode("x", $fs);
        	$sum += $fs[0];
        }
        
        return $sum;
    }
    
    /**
     * Liefert eine (die) entsprechende Menge fuer den agegebenen Artikel
     * 
     * @param float $artid : ID des Artikels
     * @return float : Menge des Artikels
     */
    public function getArticleamount($artid){
    	return $this->articleamounts[$artid];
    }
    
    /**
     * Liefert eine (die) entsprechende Staffelung fuer den agegebenen Artikel
     * 0 = pro Kalkulation, 1 = pro Stueck/Auflage 
     *
     * @param int $artid : ID des Artikels
     * @return int : Staffelung des Artikels
     */
    public function getArticlescale($artid){
    	return $this->articlescales[$artid];
    }
    
    /**
     * Liefert alle zus. Positionen einer Kalkulation
     * 
     * @return multitype:CalculationPosition
     */
    public function getPositions(){
    	$all_positions = CalculationPosition::getAllCalculationPositions($this->getId());
    	return $all_positions; 
    }
    
    /**
     * Liefert alle zus. Positionen einer Kalkulation, die auf den Dokumenten ausgewiesen werden sollen
     *
     * @return multitype:CalculationPosition
     */
    public function getPositionsForDocuments(){
    	$all_positions = CalculationPosition::getAllCalculationPositions($this->getId(), true);
    	return $all_positions;
    }
    
    // -------------------------------- Getter und Setter -------------------------------------------------------------
    
    public function clearId()
    {
        $this->id = 0;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }
	
    public function getOrderId()
    {
        return $this->orderId;
    }

    public function getProductFormat()
    {
        return $this->productFormat;
    }

    public function setProductFormat($productFormat)
    {
        $this->productFormat = $productFormat;
    }

    public function getProductFormatWidth()
    {
        return $this->productFormatWidth;
    }

    public function setProductFormatWidth($productFormatWidth)
    {
        $this->productFormatWidth = $productFormatWidth;
    }

    public function getProductFormatHeight()
    {
        return $this->productFormatHeight;
    }

    public function setProductFormatHeight($productFormatHeight)
    {
        $this->productFormatHeight = $productFormatHeight;
    }

    public function getPagesContent()
    {
        return $this->pagesContent;
    }

    public function setPagesContent($pagesContent)
    {
        $this->pagesContent = $pagesContent;
    }

    public function getPagesAddContent()
    {
        return $this->pagesAddContent;
    }

    public function setPagesAddContent($pagesAddContent)
    {
        $this->pagesAddContent = $pagesAddContent;
    }

    public function getPagesEnvelope()
    {
        return $this->pagesEnvelope;
    }

    public function setPagesEnvelope($pagesEnvelope)
    {
        $this->pagesEnvelope = $pagesEnvelope;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getPaperContent()
    {
        return $this->paperContent;
    }

    public function setPaperContent($paperContent)
    {
        $this->paperContent = $paperContent;
    }

    public function getPaperContentWidth()
    {
        return $this->paperContentWidth;
    }

    public function setPaperContentWidth($paperContentWidth)
    {
        $this->paperContentWidth = $paperContentWidth;
    }

    public function getPaperContentHeight()
    {
        return $this->paperContentHeight;
    }

    public function setPaperContentHeight($paperContentHeight)
    {
        $this->paperContentHeight = $paperContentHeight;
    }

    public function getPaperContentWeight()
    {
        return $this->paperContentWeight;
    }

    public function setPaperContentWeight($paperContentWeight)
    {
        $this->paperContentWeight = $paperContentWeight;
    }

    public function getPaperAddContent()
    {
        return $this->paperAddContent;
    }

    public function setPaperAddContent($paperAddContent)
    {
        $this->paperAddContent = $paperAddContent;
    }

    public function getPaperAddContentWidth()
    {
        return $this->paperAddContentWidth;
    }

    public function setPaperAddContentWidth($paperAddContentWidth)
    {
        $this->paperAddContentWidth = $paperAddContentWidth;
    }

    public function getPaperAddContentHeight()
    {
        return $this->paperAddContentHeight;
    }

    public function setPaperAddContentHeight($paperAddContentHeight)
    {
        $this->paperAddContentHeight = $paperAddContentHeight;
    }

    public function getPaperAddContentWeight()
    {
        return $this->paperAddContentWeight;
    }

    public function setPaperAddContentWeight($paperAddContentWeight)
    {
        $this->paperAddContentWeight = $paperAddContentWeight;
    }

    public function getPaperEnvelope()
    {
        return $this->paperEnvelope;
    }

    public function setPaperEnvelope($paperEnvelope)
    {
        $this->paperEnvelope = $paperEnvelope;
    }

    public function getPaperEnvelopeWidth()
    {
        return $this->paperEnvelopeWidth;
    }

    public function setPaperEnvelopeWidth($paperEnvelopeWidth)
    {
        $this->paperEnvelopeWidth = $paperEnvelopeWidth;
    }

    public function getPaperEnvelopeHeight()
    {
        return $this->paperEnvelopeHeight;
    }

    public function setPaperEnvelopeHeight($paperEnvelopeHeight)
    {
        $this->paperEnvelopeHeight = $paperEnvelopeHeight;
    }

    public function getPaperEnvelopeWeight()
    {
        return $this->paperEnvelopeWeight;
    }

    public function setPaperEnvelopeWeight($paperEnvelopeWeight)
    {
        $this->paperEnvelopeWeight = $paperEnvelopeWeight;
    }

    public function getFolding()
    {
        return $this->folding;
    }

    public function setFolding($folding)
    {
        $this->folding = $folding;
    }

    public function getProductFormatWidthOpen()
    {
        return $this->productFormatWidthOpen;
    }

    public function setProductFormatWidthOpen($productFormatWidthOpen)
    {
        $this->productFormatWidthOpen = $productFormatWidthOpen;
    }

    public function getProductFormatHeightOpen()
    {
        return $this->productFormatHeightOpen;
    }

    public function setProductFormatHeightOpen($productFormatHeightOpen)
    {
        $this->productFormatHeightOpen = $productFormatHeightOpen;
    }

    public function getAddCharge()
    {
        return $this->addCharge;
    }

    public function setAddCharge($addCharge)
    {
        $this->addCharge = $addCharge;
    }

    public function getMargin()
    {
        return $this->margin;
    }

    public function setMargin($margin)
    {
        $this->margin = $margin;
    }

    public function getDiscount()
    {
        return $this->discount;
    }

    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }
    
    public function getChromaticitiesContent()
    {
        return $this->chromaticitiesContent;
    }

    public function setChromaticitiesContent($chromaticities)
    {
        $this->chromaticitiesContent = $chromaticities;
    }
    
    public function getChromaticitiesAddContent()
    {
        return $this->chromaticitiesAddContent;
    }

    public function setChromaticitiesAddContent($chromaticities)
    {
        $this->chromaticitiesAddContent = $chromaticities;
    }
    
    public function getChromaticitiesEnvelope()
    {
        return $this->chromaticitiesEnvelope;
    }

    public function setChromaticitiesEnvelope($chromaticities)
    {
        $this->chromaticitiesEnvelope = $chromaticities;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getEnvelopeHeightOpen()
    {
        return $this->envelopeHeightOpen;
    }

    public function setEnvelopeHeightOpen($envelopeHeightOpen)
    {
        $this->envelopeHeightOpen = $envelopeHeightOpen;
    }

    public function getEnvelopeWidthOpen()
    {
        return $this->envelopeWidthOpen;
    }

    public function setEnvelopeWidthOpen($envelopeWidthOpen)
    {
        $this->envelopeWidthOpen = $envelopeWidthOpen;
    }

    public function getCalcAutoValues()
    {
        return $this->calcAutoValues;
    }

    public function setCalcAutoValues($calcAutoValues)
    {
        if($calcAutoValues == true || $calcAutoValues == 1)
            $this->calcAutoValues = 1;
        else
            $this->calcAutoValues = 0;
    }

    public function getPaperContentGrant()
    {
        return $this->paperContentGrant;
    }

    public function setPaperContentGrant($paperContentGrant)
    {
        $this->paperContentGrant = $paperContentGrant;
    }

    public function getPaperAddContentGrant()
    {
        return $this->paperAddContentGrant;
    }

    public function setPaperAddContentGrant($paperAddContentGrant)
    {
        $this->paperAddContentGrant = $paperAddContentGrant;
    }

    public function getPaperEnvelopeGrant()
    {
        return $this->paperEnvelopeGrant;
    }

    public function setPaperEnvelopeGrant($paperEnvelopeGrant)
    {
        $this->paperEnvelopeGrant = $paperEnvelopeGrant;
    }

    public function getTextProcessing()
    {
        return $this->textProcessing;
    }

    public function setTextProcessing($textProcessing)
    {
        $this->textProcessing = $textProcessing;
    }

    public function getFoldschemeContent()
    {
        return $this->foldschemeContent;
    }

    public function setFoldschemeContent($foldschemeContent)
    {
        $this->foldschemeContent = $foldschemeContent;
    }

    public function getFoldschemeAddContent()
    {
        return $this->foldschemeAddContent;
    }

    public function setFoldschemeAddContent($foldschemeAddContent)
    {
        $this->foldschemeAddContent = $foldschemeAddContent;
    }

    public function getFoldschemeEnvelope()
    {
        return $this->foldschemeEnvelope;
    }

    public function setFoldschemeEnvelope($foldschemeEnvelope)
    {
        $this->foldschemeEnvelope = $foldschemeEnvelope;
    }

    public function getArticles()
    {
        return $this->articles;
    }

    public function setArticles($articles)
    {
        $this->articles = $articles;
    }

    public function getArticleamounts()
    {
        return $this->articleamounts;
    }

    public function setArticleamounts($articleamounts)
    {
        $this->articleamounts = $articleamounts;
    }

    public function getArticlescales()
    {
        return $this->articlescales;
    }

    public function setArticlescales($articlescales)
    {
        $this->articlescales = $articlescales;
    }

	public function getPagesAddContent2()
	{
	    return $this->pagesAddContent2;
	}

	public function setPagesAddContent2($pagesAddContent2)
	{
	    $this->pagesAddContent2 = $pagesAddContent2;
	}

	public function getPaperAddContent2()
	{
	    return $this->paperAddContent2;
	}

	public function setPaperAddContent2($paperAddContent2)
	{
	    $this->paperAddContent2 = $paperAddContent2;
	}

	public function getPaperAddContent2Width()
	{
	    return $this->paperAddContent2Width;
	}

	public function setPaperAddContent2Width($paperAddContent2Width)
	{
	    $this->paperAddContent2Width = $paperAddContent2Width;
	}

	public function getPaperAddContent2Height()
	{
	    return $this->paperAddContent2Height;
	}

	public function setPaperAddContent2Height($paperAddContent2Height)
	{
	    $this->paperAddContent2Height = $paperAddContent2Height;
	}

	public function getPaperAddContent2Weight()
	{
	    return $this->paperAddContent2Weight;
	}

	public function setPaperAddContent2Weight($paperAddContent2Weight)
	{
	    $this->paperAddContent2Weight = $paperAddContent2Weight;
	}

	public function getPaperAddContent2Grant()
	{
	    return $this->paperAddContent2Grant;
	}

	public function setPaperAddContent2Grant($paperAddContent2Grant)
	{
	    $this->paperAddContent2Grant = $paperAddContent2Grant;
	}

	public function getChromaticitiesAddContent2()
	{
	    return $this->chromaticitiesAddContent2;
	}

	public function setChromaticitiesAddContent2($chromaticitiesAddContent2)
	{
	    $this->chromaticitiesAddContent2 = $chromaticitiesAddContent2;
	}

	public function getFoldschemeAddContent2()
	{
	    return $this->foldschemeAddContent2;
	}

	public function setFoldschemeAddContent2($foldschemeAddContent2)
	{
	    $this->foldschemeAddContent2 = $foldschemeAddContent2;
	}

	public function getPagesAddContent3()
	{
	    return $this->pagesAddContent3;
	}

	public function setPagesAddContent3($pagesAddContent3)
	{
	    $this->pagesAddContent3 = $pagesAddContent3;
	}

	public function getPaperAddContent3()
	{
	    return $this->paperAddContent3;
	}

	public function setPaperAddContent3($paperAddContent3)
	{
	    $this->paperAddContent3 = $paperAddContent3;
	}

	public function getPaperAddContent3Width()
	{
	    return $this->paperAddContent3Width;
	}

	public function setPaperAddContent3Width($paperAddContent3Width)
	{
	    $this->paperAddContent3Width = $paperAddContent3Width;
	}

	public function getPaperAddContent3Height()
	{
	    return $this->paperAddContent3Height;
	}

	public function setPaperAddContent3Height($paperAddContent3Height)
	{
	    $this->paperAddContent3Height = $paperAddContent3Height;
	}

	public function getPaperAddContent3Weight()
	{
	    return $this->paperAddContent3Weight;
	}

	public function setPaperAddContent3Weight($paperAddContent3Weight)
	{
	    $this->paperAddContent3Weight = $paperAddContent3Weight;
	}

	public function getPaperAddContent3Grant()
	{
	    return $this->paperAddContent3Grant;
	}

	public function setPaperAddContent3Grant($paperAddContent3Grant)
	{
	    $this->paperAddContent3Grant = $paperAddContent3Grant;
	}

	public function getChromaticitiesAddContent3()
	{
	    return $this->chromaticitiesAddContent3;
	}

	public function setChromaticitiesAddContent3($chromaticitiesAddContent3)
	{
	    $this->chromaticitiesAddContent3 = $chromaticitiesAddContent3;
	}

	public function getFoldschemeAddContent3()
	{
	    return $this->foldschemeAddContent3;
	}

	public function setFoldschemeAddContent3($foldschemeAddContent3)
	{
	    $this->foldschemeAddContent3 = $foldschemeAddContent3;
	}

    public function getCutContent()
    {
        return $this->cutContent;
    }

    public function setCutContent($cutContent)
    {
        $this->cutContent = $cutContent;
    }

    public function getCutAddContent()
    {
        return $this->cutAddContent;
    }

    public function setCutAddContent($cutAddContent)
    {
        $this->cutAddContent = $cutAddContent;
    }

    public function getCutAddContent2()
    {
        return $this->cutAddContent2;
    }

    public function setCutAddContent2($cutAddContent2)
    {
        $this->cutAddContent2 = $cutAddContent2;
    }

    public function getCutAddContent3()
    {
        return $this->cutAddContent3;
    }

    public function setCutAddContent3($cutAddContent3)
    {
        $this->cutAddContent3 = $cutAddContent3;
    }

    public function getCutEnvelope()
    {
        return $this->cutEnvelope;
    }

    public function setCutEnvelope($cutEnvelope)
    {
        $this->cutEnvelope = $cutEnvelope;
    }

    public function getColorControl()
    {
        return $this->colorControl;
    }

    public function setColorControl($colorControl)
    {
        $this->colorControl = $colorControl;
    }
	
	public function setCutterWeight($cutter_weight)
	{
		$this->cutter_weight = $cutter_weight;
	}
	
	public function getCutterWeight()
	{
		return $this->cutter_weight;
	}

	public function setCutterHeight($cutter_height)
	{
		$this->cutter_height = $cutter_height;
	}
	
	public function getCutterHeight()
	{
		return $this->cutter_height;
	}
	
	public function setRollDir($roll_dir)
	{
		$this->roll_dir = $roll_dir;
	}
	
	public function getRollDir()
	{
		return $this->roll_dir;
	}
	
	public function setTitle($title)
	{
		$this->title = $title;
	}
	
	public function getTitle()
	{
		return $this->title;
	}
	/**
     * @return the $sorts
     */
    public function getSorts()
    {
        return $this->sorts;
    }

	/**
     * @param number $sorts
     */
    public function setSorts($sorts)
    {
        $this->sorts = $sorts;
    }

	
	
}
?>