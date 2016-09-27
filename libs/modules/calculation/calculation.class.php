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

    const PRODUCT_ROWS = 1;
    const PRODUCT_COLS = 2;
    
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
    private $calcDebug = 0;
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
    private $colorControl = 0;					// Farbkontrollstreifen anzeigen
	
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
	
	private $format_in_content;
	private $format_in_addcontent;
	private $format_in_addcontent2;
	private $format_in_addcontent3;
	private $format_in_envelope;

    // Prices

    private $pricesub = 0.0;
    private $pricetotal = 0.0;

	// other
	
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
                $sql = "SELECT * FROM orders_calculations WHERE id = {$id}";
                if ($DB->num_rows($sql)) {
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
                    $this->calcDebug = $r["calc_debug"];
                    $this->chromaticitiesContent = new Chromaticity($r["chromaticities_content"]);
                    $this->chromaticitiesAddContent = new Chromaticity($r["chromaticities_addcontent"]);
                    $this->chromaticitiesEnvelope = new Chromaticity($r["chromaticities_envelope"]);
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

                    $this->format_in_content = $r["format_in_content"];
                    $this->format_in_addcontent = $r["format_in_addcontent"];
                    $this->format_in_addcontent2 = $r["format_in_addcontent2"];
                    $this->format_in_addcontent3 = $r["format_in_addcontent3"];
                    $this->format_in_envelope = $r["format_in_envelope"];

                    $this->title = $r["title"];

                    $this->pricesub = $r["pricesub"];
                    $this->pricetotal = $r["pricetotal"];
                }

                //--------------------------Artikel----------------------------------------------
                $sql = "SELECT * FROM orders_articles WHERE calc_id = {$id}";
                $all_art = Array();
                if ($DB->num_rows($sql)) {
                    $rows = $DB->select($sql);
                    foreach ($rows as $ro) {
                        $all_art[] = new Article($ro['article_id']);
                        $this->articleamounts[$ro['article_id']] = $ro['amount'];
                        $this->articlescales[$ro['article_id']] = $ro['scale'];
                    }
                    $this->articles = $all_art;
                }
                Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
            }
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
    }

    /**
     * @param $order
     * @param string $itemorder
     * @return Calculation[]
     */
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
                        calc_debug = {$this->calcDebug},
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
						
						format_in_content =	'{$this->format_in_content}',
						format_in_addcontent =	'{$this->format_in_addcontent}',
						format_in_addcontent2 =	'{$this->format_in_addcontent2}',
						format_in_addcontent3 =	'{$this->format_in_addcontent3}',
						format_in_envelope =	'{$this->format_in_envelope}',
						
						title =	'{$this->title}',
						pricesub = {$this->pricesub},
						pricetotal = {$this->pricetotal},
        				color_control = {$this->colorControl}, ";
        
        if($this->id > 0){
        	// Erst Artikel speichern
        	$sql = "DELETE FROM orders_articles WHERE calc_id = {$this->id}";
        	$DB->no_result($sql);

        	if (count($this->articleamounts) > 0){
	        	$sql = "INSERT INTO orders_articles (calc_id, article_id, amount, scale) VALUES ";
	        	foreach ($this->articleamounts as $key => $value){
	        		$sql .= "( {$this->id}, {$key} , {$value} , {$this->articlescales[$key]}), ";
	        	}
	        	$sql = substr($sql, 0, -2); // Das letzte Komma und Leerzeichen entfernen
	        	$DB->no_result($sql);
        	}
        	       	
        	// Dann Kalkulation speichern
            $sql = "UPDATE orders_calculations
                    SET
                        {$set}
                        upddat = UNIX_TIMESTAMP(),
                        updusr = {$_USER->getId()}
                    WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
        } else
        {
            $sql = "INSERT INTO orders_calculations SET 
                    {$set}
                    crtdat = UNIX_TIMESTAMP(),
                    crtusr = {$_USER->getId()}";
            $res = $DB->no_result($sql);
            if($res)
            {
                $sql = "SELECT max(id) id FROM orders_calculations WHERE order_id = {$this->orderId}";
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

    public function delete()
    {
        global $DB;
        if($this->id > 0)
        {
            $sql = "DELETE FROM orders_calculations WHERE id = {$this->id}";
            if($DB->no_result($sql))
            {
                Cachehandler::removeCache(Cachehandler::genKeyword($this));
                unset($this);
                return true;
            } else 
                return false;
        }
    }

    public static function contentArray()
    {

        $contents = [
            [
                'name'=>'Inhalt 1',
                'id'=>'getPaperContent',
                'weight'=>'getPaperContentWeight',
                'chr'=>'getChromaticitiesContent',
                'width'=>'getPaperContentWidth',
                'height'=>'getPaperContentHeight',
                'pages'=>'getPagesContent',
                'grant'=>'getPaperContentGrant',
                'const'=>Calculation::PAPER_CONTENT,
            ],
            [
                'name'=>'Inhalt 2',
                'id'=>'getPaperAddContent',
                'weight'=>'getPaperAddContentWeight',
                'chr'=>'getChromaticitiesAddContent',
                'width'=>'getPaperAddContentWidth',
                'height'=>'getPaperAddContentHeight',
                'pages'=>'getPagesAddContent',
                'grant'=>'getPaperAddContentGrant',
                'const'=>Calculation::PAPER_ADDCONTENT,
            ],
            [
                'name'=>'Inhalt 3',
                'id'=>'getPaperAddContent2',
                'weight'=>'getPaperAddContent2Weight',
                'chr'=>'getChromaticitiesAddContent2',
                'width'=>'getPaperAddContent2Width',
                'height'=>'getPaperAddContent2Height',
                'pages'=>'getPagesAddContent2',
                'grant'=>'getPaperAddContent2Grant',
                'const'=>Calculation::PAPER_ADDCONTENT2,
            ],
            [
                'name'=>'Inhalt 4',
                'id'=>'getPaperAddContent3',
                'weight'=>'getPaperAddContent3Weight',
                'chr'=>'getChromaticitiesAddContent3',
                'width'=>'getPaperAddContent3Width',
                'height'=>'getPaperAddContent3Height',
                'pages'=>'getPagesAddContent3',
                'grant'=>'getPaperAddContent3Grant',
                'const'=>Calculation::PAPER_ADDCONTENT3,
            ],
            [
                'name'=>'Umschlag',
                'id'=>'getPaperEnvelope',
                'weight'=>'getPaperEnvelopeWeight',
                'chr'=>'getChromaticitiesEnvelope',
                'width'=>'getPaperEnvelopeWidth',
                'height'=>'getPaperEnvelopeHeight',
                'pages'=>'getPagesEnvelope',
                'grant'=>'getPaperEnvelopeGrant',
                'const'=>Calculation::PAPER_ENVELOPE,
            ],
        ];
        return $contents;
    }
    
    public function getPaperCount($papertype, Machineentry $me = null)
    {
        if ($me == null){
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
        } else {

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
    }


    /**
     * liefert Array mit details zu verwendeten inhalten / umschlag
     * @return array
     */
    public function getDetails()
    {
        $retval = [];

        if ($this->getPaperContent()->getId()>0){
            $data = [];
            $data['paper'] = self::PAPER_CONTENT;
            $data['papername'] = $this->getPaperContent()->getName();
            $data['name'] = 'Inhalt 1';
            $data['material'] = $this->getPaperContent()->getName();
            $data['gewicht'] = $this->getPaperContentWeight();
            $data['umfang'] = $this->getPagesContent();
            $data['farbigkeit'] = $this->getChromaticitiesContent()->getName();
            $data['offen'] = $this->getProductFormatWidthOpen() . ' x ' . $this->getProductFormatHeightOpen() . ' mm';
            $retval[] = $data;
        }
        if ($this->getPaperAddContent()->getId()>0){
            $data = [];
            $data['paper'] = self::PAPER_ADDCONTENT;
            $data['papername'] = $this->getPaperAddContent()->getName();
            $data['name'] = 'Inhalt 2';
            $data['material'] = $this->getPaperAddContent()->getName();
            $data['gewicht'] = $this->getPaperAddContentWeight();
            $data['umfang'] = $this->getPagesAddContent();
            $data['farbigkeit'] = $this->getChromaticitiesAddContent()->getName();
            $data['offen'] = $this->getProductFormatWidthOpen() . ' x ' . $this->getProductFormatHeightOpen() . ' mm';
            $retval[] = $data;
        }
        if ($this->getPaperAddContent2()->getId()>0){
            $data = [];
            $data['paper'] = self::PAPER_ADDCONTENT2;
            $data['papername'] = $this->getPaperAddContent2()->getName();
            $data['name'] = 'Inhalt 3';
            $data['material'] = $this->getPaperAddContent2()->getName();
            $data['gewicht'] = $this->getPaperAddContent2Weight();
            $data['umfang'] = $this->getPagesAddContent2();
            $data['farbigkeit'] = $this->getChromaticitiesAddContent2()->getName();
            $data['offen'] = $this->getProductFormatWidthOpen() . ' x ' . $this->getProductFormatHeightOpen() . ' mm';
            $retval[] = $data;
        }
        if ($this->getPaperAddContent3()->getId()>0){
            $data = [];
            $data['paper'] = self::PAPER_ADDCONTENT3;
            $data['papername'] = $this->getPaperAddContent3()->getName();
            $data['name'] = 'Inhalt 4';
            $data['material'] = $this->getPaperAddContent3()->getName();
            $data['gewicht'] = $this->getPaperAddContent3Weight();
            $data['umfang'] = $this->getPagesAddContent3();
            $data['farbigkeit'] = $this->getChromaticitiesAddContent3()->getName();
            $data['offen'] = $this->getProductFormatWidthOpen() . ' x ' . $this->getProductFormatHeightOpen() . ' mm';
            $retval[] = $data;
        }
        if ($this->getPaperEnvelope()->getId()>0){
            $data = [];
            $data['paper'] = self::PAPER_ENVELOPE;
            $data['papername'] = $this->getPaperEnvelope()->getName();
            $data['name'] = 'Umschlag';
            $data['material'] = $this->getPaperEnvelope()->getName();
            $data['gewicht'] = $this->getPaperEnvelopeWeight();
            $data['umfang'] = $this->getPagesEnvelope();
            $data['farbigkeit'] = $this->getChromaticitiesEnvelope()->getName();
            $data['offen'] = $this->getEnvelopeWidthOpen() . ' x ' . $this->getEnvelopeHeightOpen() . ' mm ';
            $retval[] = $data;
        }
        return $retval;
    }

    /**
     * @param int $ptype
     * @param Machineentry $me
     * @return float|int
     */
    function getProductsPerPaperForMe($ptype, Machineentry $me)
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
            $anz_cols = floor(ceil($height * 1.01) / $height_closed);
        else
            $anz_cols = 1;

        // Maschine fuer ausgewaehlten Papiertyp
        $mach = $me;
        if($mach)
        {
            $calc = $this;
            if($mach->getPart() == Calculation::PAPER_CONTENT)
                $chr = $calc->getChromaticitiesContent();
            else if ($mach->getPart() == Calculation::PAPER_ADDCONTENT)
                $chr = $calc->getChromaticitiesAddContent();
            else if($mach->getPart() == Calculation::PAPER_ENVELOPE)
                $chr = $calc->getChromaticitiesEnvelope();
            else if ($mach->getPart() == Calculation::PAPER_ADDCONTENT2)
                $chr = $calc->getChromaticitiesAddContent2();
            else if ($mach->getPart() == Calculation::PAPER_ADDCONTENT3)
                $chr = $calc->getChromaticitiesAddContent3();
//            print_r($chr);

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
            $paperH = $paperH - $mach->getMachine()->getBorder_bottom() - $mach->getMachine()->getBorder_top() - $tmp_farbrand;
            $paperW = $paperW - $mach->getMachine()->getBorder_left() - $mach->getMachine()->getBorder_right();

//            echo '$width:' . $width . '</br>';
//            echo '$height:' . $height . '</br>';
//            echo '$height_closed:' . $height_closed . '</br>';
//            echo '$width_closed:' . $width_closed . '</br>';
//            echo '$paperH:' . $paperH . '</br>';
//            echo '$paperW:' . $paperW . '</br>';

            // Ausrechnen
            $productRows = floor($paperH / ($height + $tmp_anschnitt * 2));
            $productCols = floor($paperW / ($width + $tmp_anschnitt * 2));
            $productPerPaper1 = $productCols * $productRows;

            $productCols = floor($paperW / ($height + $tmp_anschnitt * 2));
            $productRows = floor($paperH / ($width + $tmp_anschnitt * 2));
            $productPerPaper2 = $productCols * $productRows;

//            echo '$anz_cols:' . $anz_cols . '</br>';
//            echo '$anz_rows:' . $anz_rows . '</br>';
//            echo '$duplex:' . $duplex . '</br>';

            if($productPerPaper1 > $productPerPaper2)
                $rv = $productPerPaper1 * $anz_cols * $anz_rows * $duplex;
            else
                $rv = $productPerPaper2 * $anz_cols * $anz_rows * $duplex;

//            echo '$productPerPaper1:' . $productPerPaper1 . '</br>';
//            echo '$productPerPaper2:' . $productPerPaper2 . '</br>';

//            echo 'produkte: ' . $rv . '</br>';
            return $rv;
        } else
//            echo 'produkte: 0</br>';
            return 0;
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
            $anz_cols = floor(ceil($height * 1.01) / $height_closed);
        else
            $anz_cols = 1;
        
        // Maschine fuer ausgewaehlten Papiertyp
        $mach = Machineentry::getMachineForPapertype($ptype, $this->id);
//        print_r($mach);
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
//            print_r($chr);
            
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
            $paperH = $paperH - $mach[0]->getMachine()->getBorder_bottom() - $mach[0]->getMachine()->getBorder_top() - $tmp_farbrand;
            $paperW = $paperW - $mach[0]->getMachine()->getBorder_left() - $mach[0]->getMachine()->getBorder_right();

//            echo '$width:' . $width . '</br>';
//            echo '$height:' . $height . '</br>';
//            echo '$height_closed:' . $height_closed . '</br>';
//            echo '$width_closed:' . $width_closed . '</br>';
//            echo '$paperH:' . $paperH . '</br>';
//            echo '$paperW:' . $paperW . '</br>';
    
            // Ausrechnen
            $productRows = floor($paperH / ($height + $tmp_anschnitt * 2));
            $productCols = floor($paperW / ($width + $tmp_anschnitt * 2));
            $productPerPaper1 = $productCols * $productRows;
            
            $productCols = floor($paperW / ($height + $tmp_anschnitt * 2));
            $productRows = floor($paperH / ($width + $tmp_anschnitt * 2));
            $productPerPaper2 = $productCols * $productRows;

//            echo '$anz_cols:' . $anz_cols . '</br>';
//            echo '$anz_rows:' . $anz_rows . '</br>';
//            echo '$duplex:' . $duplex . '</br>';
                        
            if($productPerPaper1 > $productPerPaper2)
                $rv = $productPerPaper1 * $anz_cols * $anz_rows * $duplex;
            else 
                $rv = $productPerPaper2 * $anz_cols * $anz_rows * $duplex;

//            echo '$productPerPaper1:' . $productPerPaper1 . '</br>';
//            echo '$productPerPaper2:' . $productPerPaper2 . '</br>';

//            echo 'produkte: ' . $rv . '</br>';
            return $rv;
        } else
//            echo 'produkte: 0</br>';
            return 0;
    }

    function getPaperSize($ptype)
    {
        if($ptype == Calculation::PAPER_CONTENT)
            return Array('paperH'=>$this->paperContentHeight,'paperW'=>$this->paperContentWidth);
        else if ($ptype == Calculation::PAPER_ADDCONTENT)
            return Array('paperH'=>$this->paperAddContentHeight,'paperW'=>$this->paperAddContentWidth);
        elseif($ptype == Calculation::PAPER_ENVELOPE)
            return Array('paperH'=>$this->paperEnvelopeHeight,'paperW'=>$this->paperEnvelopeWidth);
        else if ($ptype == Calculation::PAPER_ADDCONTENT2)
            return Array('paperH'=>$this->paperAddContent2Height,'paperW'=>$this->paperAddContent2Width);
        else if ($ptype == Calculation::PAPER_ADDCONTENT3)
            return Array('paperH'=>$this->paperAddContent3Height,'paperW'=>$this->paperAddContent3Width);
        else
            return false;
    }

    function getPaperWeight($ptype)
    {
//        prettyPrint($ptype);
        if($ptype == Calculation::PAPER_CONTENT)
            return $this->paperContentWeight;
        else if ($ptype == Calculation::PAPER_ADDCONTENT)
            return $this->paperAddContentWeight;
        elseif($ptype == Calculation::PAPER_ENVELOPE)
            return $this->paperEnvelopeWeight;
        else if ($ptype == Calculation::PAPER_ADDCONTENT2)
            return $this->paperAddContent2Weight;
        else if ($ptype == Calculation::PAPER_ADDCONTENT3)
            return $this->paperAddContent3Weight;
        else
            return false;
    }

    public function getAnschnitt($ptype)
    {
        if($ptype == Calculation::PAPER_CONTENT){
            return $this->getCutContent();
        } else if ($ptype == Calculation::PAPER_ADDCONTENT){
            return $this->getCutAddContent();
        } else if ($ptype == Calculation::PAPER_ADDCONTENT2){
            return $this->getCutAddContent2();
        } else if ($ptype == Calculation::PAPER_ADDCONTENT3){
            return $this->getCutAddContent3();
        } elseif($ptype == Calculation::PAPER_ENVELOPE){
            return $this->getCutEnvelope();
        }
        return false;
    }

    /**
     * @param $ptype
     * @param Machineentry $mach
     * @return array|bool
     */
    function getProductsPerRowForMe($ptype, Machineentry $mach)
    {
        $debug = false;
        global $_CONFIG;
        $psize = self::getPaperSize($ptype);
        $paperH = $psize["paperH"];
        $paperW = $psize["paperW"];

        if($ptype != Calculation::PAPER_ENVELOPE){
            $width = $this->productFormatWidthOpen;
            $height = $this->productFormatHeightOpen;
        } else {
            $width = $this->envelopeWidthOpen;
            $height = $this->envelopeHeightOpen;
        }

        if($mach)
        {
            $tmp_anschnitt = 0;
            if($ptype == Calculation::PAPER_CONTENT){
                $tmp_anschnitt = $this->getCutContent();
            } else if ($ptype == Calculation::PAPER_ADDCONTENT){
                $tmp_anschnitt = $this->getCutAddContent();
            } else if ($ptype == Calculation::PAPER_ADDCONTENT2){
                $tmp_anschnitt = $this->getCutAddContent2();
            } else if ($ptype == Calculation::PAPER_ADDCONTENT3){
                $tmp_anschnitt = $this->getCutAddContent3();
            } elseif($ptype == Calculation::PAPER_ENVELOPE){
                $tmp_anschnitt = $this->getCutEnvelope();
            }

            // Farbrand (Farbkontrollstreifen) setzen
            $tmp_farbrand = $_CONFIG->farbRandBreite;
            if($this->getColorControl() == 0){
                // Wenn der Farbrand in der Kalkulation ausgestellt ist
                $tmp_farbrand = 0;
            }

            $paperH = $paperH - $mach->getMachine()->getBorder_bottom() - $mach->getMachine()->getBorder_top() - $tmp_farbrand;
            $paperW = $paperW - $mach->getMachine()->getBorder_left() - $mach->getMachine()->getBorder_right();

            $productRows1 = floor($paperH / ($height + $tmp_anschnitt * 2));
            $productCols1 = floor($paperW / ($width + $tmp_anschnitt * 2));
            $productPerPaper1 = $productCols1 * $productRows1;
            $productCols2 = floor($paperW / ($height + $tmp_anschnitt * 2));
            $productRows2 = floor($paperH / ($width + $tmp_anschnitt * 2));
            $productPerPaper2 = $productCols2 * $productRows2;

            if ($debug){
                echo $mach->getMachine()->getName().'</br>';
                echo '$productRows1: '.$productRows1.'</br>';
                echo '$productCols1: '.$productCols1.'</br>';
                echo '$productPerPaper1: '.$productPerPaper1.'</br>';
                echo '$productCols2: '.$productCols2.'</br>';
                echo '$productRows2: '.$productRows2.'</br>';
                echo '$productPerPaper2: '.$productPerPaper2.'</br></br>';
            }

            if($productPerPaper1 > $productPerPaper2)
                $retval = Array('rows'=>$productRows1,'cols'=>$productCols1);
            else
                $retval = Array('rows'=>$productRows2,'cols'=>$productCols2);

            return $retval;
        } else
            return false;
    }

    /**
     * @param $ptype
     * @return array|bool
     */
    function getProductsPerRow($ptype)
    {
        $debug = false;
        global $_CONFIG;
        $psize = self::getPaperSize($ptype);
        $paperH = $psize["paperH"];
        $paperW = $psize["paperW"];

        if($ptype != Calculation::PAPER_ENVELOPE){
            $width = $this->productFormatWidthOpen;
            $height = $this->productFormatHeightOpen;
        } else {
            $width = $this->envelopeWidthOpen;
            $height = $this->envelopeHeightOpen;
        }

        $mach = Machineentry::getMachineForPapertype($ptype, $this->id);
        if($mach)
        {
            $calc = new Calculation($mach[0]->getCalcId());

            $tmp_anschnitt = 0;
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

            $paperH = $paperH - $mach[0]->getMachine()->getBorder_bottom() - $mach[0]->getMachine()->getBorder_top() - $tmp_farbrand;
            $paperW = $paperW - $mach[0]->getMachine()->getBorder_left() - $mach[0]->getMachine()->getBorder_right();

            $productRows1 = floor($paperH / ($height + $tmp_anschnitt * 2));
            $productCols1 = floor($paperW / ($width + $tmp_anschnitt * 2));
            $productPerPaper1 = $productCols1 * $productRows1;
            $productCols2 = floor($paperW / ($height + $tmp_anschnitt * 2));
            $productRows2 = floor($paperH / ($width + $tmp_anschnitt * 2));
            $productPerPaper2 = $productCols2 * $productRows2;

            if ($debug){
                echo '$productRows1: '.$productRows1.'</br>';
                echo '$productCols1: '.$productCols1.'</br>';
                echo '$productPerPaper1: '.$productPerPaper1.'</br>';
                echo '$productCols2: '.$productCols2.'</br>';
                echo '$productRows2: '.$productRows2.'</br>';
                echo '$productPerPaper2: '.$productPerPaper2.'</br>';
            }

            if($productPerPaper1 > $productPerPaper2)
                $retval = Array('rows'=>$productRows1,'cols'=>$productCols1);
            else
                $retval = Array('rows'=>$productRows2,'cols'=>$productCols2);

            return $retval;
        } else
            return false;
    }


    
    public function getPlateCount(Machineentry $machineEntry = null) {
        $plates = 0;
        if($machineEntry == null)
        {
            $machEntries = Machineentry::getAllMachineentries($this->getId());
//            prettyPrint(count($machEntries));

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
                    if($me->getPart() == Calculation::PAPER_CONTENT && $order->getProduct()->getSingleplateset() == 0 && $order->getProduct()->getBlockplateset() == 0)
                    {
                        $papers = $this->getPagesContent() / $this->getProductsPerPaper(Calculation::PAPER_CONTENT); 
                    } else if($me->getPart() == Calculation::PAPER_ADDCONTENT && $order->getProduct()->getSingleplateset() == 0 && $order->getProduct()->getBlockplateset() == 0)
                    {
                        $papers = $this->getPagesAddContent() / $this->getProductsPerPaper(Calculation::PAPER_ADDCONTENT); 
                    } else if($me->getPart() == Calculation::PAPER_ENVELOPE && $order->getProduct()->getSingleplateset() == 0 && $order->getProduct()->getBlockplateset() == 0)
                    {
                        $papers = $this->getPagesEnvelope() / $this->getProductsPerPaper(Calculation::PAPER_ENVELOPE); 
                    } else if($me->getPart() == Calculation::PAPER_ADDCONTENT2 && $order->getProduct()->getSingleplateset() == 0 && $order->getProduct()->getBlockplateset() == 0)
                    {
                        $papers = $this->getPagesAddContent2() / $this->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2); 
                    }
                    else if($me->getPart() == Calculation::PAPER_ADDCONTENT3 && $order->getProduct()->getSingleplateset() == 0 && $order->getProduct()->getBlockplateset() == 0)
                    {
                    	$papers = $this->getPagesAddContent3() / $this->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3);
                    }
                    if ($order->getProduct()->getSingleplateset() == 0 && $order->getProduct()->getBlockplateset() == 0){
                        $tmp_plates += $chr->getColorsFront() * ceil($papers);
                        $tmp_plates += $chr->getColorsBack() * ceil($papers); // round($papers, 0, PHP_ROUND_HALF_DOWN);
                    } elseif ($order->getProduct()->getSingleplateset() == 1 && $me->getPart() == Calculation::PAPER_CONTENT){
                        $tmp_plates += $chr->getColorsFront();
                        $tmp_plates += $chr->getColorsBack();
                    } elseif ($order->getProduct()->getBlockplateset() == 1){
                        $tmp_plates += $chr->getColorsFront();
                        $tmp_plates += $chr->getColorsBack();
                    }
	            	//gln, umschlagen/umstuelpen
            		//if($me->getMachine()->getUmschlUmst())
            		if($me->getUmschlagenUmstuelpen() && $order->getProduct()->getSingleplateset() == 0 && $order->getProduct()->getBlockplateset() == 0)
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
                $tmp_plates = 0;
                
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
                if($machineEntry->getPart() == Calculation::PAPER_CONTENT && $order->getProduct()->getSingleplateset() == 0 && $order->getProduct()->getBlockplateset() == 0)
                {
                    $papers = $this->getPagesContent() / $this->getProductsPerPaper(Calculation::PAPER_CONTENT);
                } else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT && $order->getProduct()->getSingleplateset() == 0 && $order->getProduct()->getBlockplateset() == 0)
                {
                    $papers = $this->getPagesAddContent() / $this->getProductsPerPaper(Calculation::PAPER_ADDCONTENT);
                } else if($machineEntry->getPart() == Calculation::PAPER_ENVELOPE && $order->getProduct()->getSingleplateset() == 0 && $order->getProduct()->getBlockplateset() == 0)
                {
                    $papers = $this->getPagesEnvelope() / $this->getProductsPerPaper(Calculation::PAPER_ENVELOPE);
                } else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT2 && $order->getProduct()->getSingleplateset() == 0 && $order->getProduct()->getBlockplateset() == 0)
                {
                    $papers = $this->getPagesAddContent2() / $this->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2);
                } else if($machineEntry->getPart() == Calculation::PAPER_ADDCONTENT3 && $order->getProduct()->getSingleplateset() == 0 && $order->getProduct()->getBlockplateset() == 0)
                {
                    $papers = $this->getPagesAddContent3() / $this->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3);
                }
                if ($order->getProduct()->getSingleplateset() == 0 && $order->getProduct()->getBlockplateset() == 0){
                    $tmp_plates += $chr->getColorsFront() * ceil($papers);
                    $tmp_plates += $chr->getColorsBack() * ceil($papers); // round($papers, 0, PHP_ROUND_HALF_DOWN);
                } elseif ($order->getProduct()->getSingleplateset() == 1 && $machineEntry->getPart() == Calculation::PAPER_CONTENT){
                    $tmp_plates += $chr->getColorsFront();
                    $tmp_plates += $chr->getColorsBack();
                } elseif ($order->getProduct()->getBlockplateset() == 1){
                    $tmp_plates += $chr->getColorsFront();
                    $tmp_plates += $chr->getColorsBack();
                }
            	//gln, umschlagen/umstuelpen
           		//if($machineEntry->getMachine()->getUmschlUmst())
           		if($machineEntry->getUmschlagenUmstuelpen() && $order->getProduct()->getSingleplateset() == 0 && $order->getProduct()->getBlockplateset() == 0)
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
        $tmp_platesets = 0;
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

    public function getColorCost()
    {
        $calc = $this;
        $sum = 0.0;
        if ($calc->getPagesContent())
            $hasContent = true;
        if ($calc->getPagesAddContent())
            $hasAddContent = true;
        if ($calc->getPagesEnvelope())
            $hasEnvelope = true;
        if ($calc->getPagesAddContent2())
            $hasAddContent2 = true;
        if ($calc->getPagesAddContent3())
            $hasAddContent3 = true;

        if ($hasContent) {
            $sum += $calc->getChromaticitiesContent()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight()/1000000) * ($calc->getPaperCount(Calculation::PAPER_CONTENT))*(1.4*0.5/1000) * ($calc->getChromaticitiesContent()->getColorsBack() + $calc->getChromaticitiesContent()->getColorsFront()));
        }
        if ($hasAddContent){
            $sum += $calc->getChromaticitiesAddContent()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight()/1000000) * ($calc->getPaperCount(Calculation::PAPER_ADDCONTENT))*(1.4*0.5/1000) * ($calc->getChromaticitiesAddContent()->getColorsBack() + $calc->getChromaticitiesAddContent()->getColorsFront()));
        }
        if ($hasEnvelope){
            $sum += $calc->getChromaticitiesEnvelope()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight()/1000000) * ($calc->getPaperCount(Calculation::PAPER_ENVELOPE))*(1.4*0.5/1000) * ($calc->getChromaticitiesEnvelope()->getColorsBack() + $calc->getChromaticitiesEnvelope()->getColorsFront()));
        }
        if ($hasAddContent2){
            $sum += $calc->getChromaticitiesAddContent2()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight()/1000000) * ($calc->getPaperCount(Calculation::PAPER_ADDCONTENT2))*(1.4*0.5/1000) * ($calc->getChromaticitiesAddContent2()->getColorsBack() + $calc->getChromaticitiesAddContent2()->getColorsFront()));
        }
        if ($hasAddContent3){
            $sum += $calc->getChromaticitiesAddContent3()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight()/1000000) * ($calc->getPaperCount(Calculation::PAPER_ADDCONTENT3))*(1.4*0.5/1000) * ($calc->getChromaticitiesAddContent3()->getColorsBack() + $calc->getChromaticitiesAddContent3()->getColorsFront()));
        }
        return tofloat($sum);
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
        $machines = Array();
    	
        $me = Machineentry::getAllMachineentries($this->getId());
        foreach($me as $m){
            $machines[$m->getMachine()->getId()] += $m->getPrice();
        }
        
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
        $sum += $this->getPaperAddContent2()->getSumPrice($this->getPaperCount(Calculation::PAPER_ADDCONTENT2) + $this->paperAddContent2Grant);
        $sum += $this->getPaperAddContent3()->getSumPrice($this->getPaperCount(Calculation::PAPER_ADDCONTENT3) + $this->paperAddContent3Grant);
        $colorcost = $this->getColorCost();
        $sum += $colorcost;
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
    
	/**
     * @return the $format_in_content
     */
    public function getFormat_in_content()
    {
        return $this->format_in_content;
    }

	/**
     * @return the $format_in_addcontent
     */
    public function getFormat_in_addcontent()
    {
        return $this->format_in_addcontent;
    }

	/**
     * @return the $format_in_addcontent2
     */
    public function getFormat_in_addcontent2()
    {
        return $this->format_in_addcontent2;
    }

	/**
     * @return the $format_in_addcontent3
     */
    public function getFormat_in_addcontent3()
    {
        return $this->format_in_addcontent3;
    }

	/**
     * @return the $format_in_envelope
     */
    public function getFormat_in_envelope()
    {
        return $this->format_in_envelope;
    }

	/**
     * @param field_type $format_in_content
     */
    public function setFormat_in_content($format_in_content)
    {
        $this->format_in_content = $format_in_content;
    }

	/**
     * @param field_type $format_in_addcontent
     */
    public function setFormat_in_addcontent($format_in_addcontent)
    {
        $this->format_in_addcontent = $format_in_addcontent;
    }

	/**
     * @param field_type $format_in_addcontent2
     */
    public function setFormat_in_addcontent2($format_in_addcontent2)
    {
        $this->format_in_addcontent2 = $format_in_addcontent2;
    }

	/**
     * @param field_type $format_in_addcontent3
     */
    public function setFormat_in_addcontent3($format_in_addcontent3)
    {
        $this->format_in_addcontent3 = $format_in_addcontent3;
    }

	/**
     * @param field_type $format_in_envelope
     */
    public function setFormat_in_envelope($format_in_envelope)
    {
        $this->format_in_envelope = $format_in_envelope;
    }

    /**
     * @return int
     */
    public function getCalcDebug()
    {
        return $this->calcDebug;
    }

    /**
     * @param int $calcDebug
     */
    public function setCalcDebug($calcDebug)
    {
        $this->calcDebug = $calcDebug;
    }

    /**
     * @return float
     */
    public function getPricetotal()
    {
        return $this->pricetotal;
    }

    /**
     * @param float $pricetotal
     */
    public function setPricetotal($pricetotal)
    {
        $this->pricetotal = $pricetotal;
    }

    /**
     * @return float
     */
    public function getPricesub()
    {
        return $this->pricesub;
    }

    /**
     * @param float $pricesub
     */
    public function setPricesub($pricesub)
    {
        $this->pricesub = $pricesub;
    }
}