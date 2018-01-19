<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       15.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/paper/paper.class.php';
require_once 'libs/modules/paperformats/paperformat.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/tradegroup/tradegroup.class.php';
require_once 'libs/modules/chromaticity/chromaticity.class.php';

class Product {
    const ORDER_ID = "id";
    const ORDER_NAME = "name";
    
    const TYPE_NORMAL = 0;
    const TYPE_BOOKPRINT = 1;
    
    private $id;
    private $state;
    private $name;
    private $description;
    private $picture;
    private $defaultMachIds = Array();
    private $machIds = Array();
    private $machines = Array();
    private $selectedPapersIds = Array();
    private $pagesFrom = 0;
    private $pagesTo = 0;
    private $pagesStep = 0;
    private $availablePaperFormats = Array();
    private $availableChromaticities = Array();
    private $hasContent = 1;
    private $hasAddContent = 0;
    private $hasEnvelope = 0;
    private $hasAddContent2 = 0;
    private $hasAddContent3 = 0;
    private $factorWidth = 0.0;
    private $factorHeight = 0.0;
    private $grantPaper = 0;
    private $type = 0;
    private $textOffer;
    private $textOfferconfirm;
    private $textInvoice;
    private $textProcessing;
    private $shoprel = 0;
    private $tradegroup = 0;
    private $loadDymmyData = 0;
    private $isIndivual = 0;
    private $singleplateset = 0;
    private $blockplateset = 0;
    private $inkcoverage = 40.00;
    private $finishingcoverage = 40.00;
    private $setmaxproducts_content = 0;
    private $setmaxproducts_content_div = 0;
    private $setmaxproducts_addcontent = 0;
    private $setmaxproducts_addcontent_div = 0;
    private $setmaxproducts_addcontent2 = 0;
    private $setmaxproducts_addcontent2_div = 0;
    private $setmaxproducts_addcontent3 = 0;
    private $setmaxproducts_addcontent3_div = 0;
    private $setmaxproducts_envelope = 0;
    private $setmaxproducts_envelope_div = 0;
    private $default_folding = 0;
    private $usageoverride = 0;

    function __construct($id = 0){
        global $DB;
        global $_USER;
        $this->tradegroup = new Tradegroup();
        $this->default_folding = new Foldtype();

        if($id > 0)
        {
            $sql = "SELECT * FROM products WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $res = $DB->select($sql);
                $res = $res[0];
                
                $this->id = $res["id"];
                $this->state = $res["state"];
                $this->name = $res["name"];
                $this->description = $res["description"];
                $this->picture = $res["picture"];
                $this->pagesFrom = $res["pages_from"];
                $this->pagesTo = $res["pages_to"];
                $this->pagesStep = $res["pages_step"];
                $this->hasContent = $res["has_content"];
                $this->hasAddContent = $res["has_addcontent"];
                $this->hasAddContent2 = $res["has_addcontent2"];
                $this->hasAddContent3 = $res["has_addcontent3"];
                $this->hasEnvelope = $res["has_envelope"];
                $this->factorWidth = $res["factor_width"];
                $this->factorHeight = $res["factor_height"];
                $this->grantPaper = $res["grant_paper"];
                $this->type = $res["type"];
                $this->textOffer = $res["text_offer"];
                $this->textOfferconfirm = $res["text_offerconfirm"];
                $this->textInvoice = $res["text_invoice"];
                $this->textProcessing = $res["text_processing"];
                $this->shoprel = $res["shop_rel"];
                $this->isIndivual = $res["is_individual"];
                $this->tradegroup = new Tradegroup($res["tradegroup"]);
                $this->singleplateset = $res["singleplateset"];
                $this->blockplateset = $res["blockplateset"];
                $this->loadDymmyData = $res["load_dummydata"];
                $this->inkcoverage = $res["inkcoverage"];
                $this->finishingcoverage = $res["finishingcoverage"];
                $this->setmaxproducts_content = $res["setmaxproducts_content"];
                $this->setmaxproducts_content_div = $res["setmaxproducts_content_div"];
                $this->setmaxproducts_addcontent = $res["setmaxproducts_addcontent"];
                $this->setmaxproducts_addcontent_div = $res["setmaxproducts_addcontent_div"];
                $this->setmaxproducts_addcontent2 = $res["setmaxproducts_addcontent2"];
                $this->setmaxproducts_addcontent2_div = $res["setmaxproducts_addcontent2_div"];
                $this->setmaxproducts_addcontent3 = $res["setmaxproducts_addcontent3"];
                $this->setmaxproducts_addcontent3_div = $res["setmaxproducts_addcontent3_div"];
                $this->setmaxproducts_envelope = $res["setmaxproducts_envelope"];
                $this->setmaxproducts_envelope_div = $res["setmaxproducts_envelope_div"];
                $this->default_folding = new Foldtype($res["default_folding"]);
                $this->usageoverride = $res["usageoverride"];

                //-------------------------------------------------------------------
                // Get Machines
                $sql = "SELECT * FROM products_machines WHERE product_id = {$this->id}";

                if ($DB->no_result($sql))
                {
                    foreach ($DB->select($sql) as $mach)
                    {
                        $this->machines[] = new Machine($mach["machine_id"]);
                        $this->machIds[$mach["machine_id"]] = $mach["machine_id"];
                        if($mach["default"])
                        {
                            $this->defaultMachIds[$mach["machine_id"]]["id"] = $mach["machine_id"];
                            $this->defaultMachIds[$mach["machine_id"]]["min"] = $mach["minimum"];
                            $this->defaultMachIds[$mach["machine_id"]]["max"] = $mach["maximum"];
                        }
                        
                    }
                }
                
                //-------------------------------------------------------------------
                // Get selected Papers and weights
                // Content (1)
                $this->selectedPapersIds["content"] = Array();
                $sql = "SELECT * FROM products_papers WHERE product_id = {$this->id} and part = 1";
                if($DB->num_rows($sql))
                {
                    foreach($DB->select($sql) as $paper)
                    {
                        $this->selectedPapersIds["content"][$paper["paper_id"]]["id"] = $paper["paper_id"];
                        $this->selectedPapersIds["content"][$paper["paper_id"]][$paper["weight"]] = 1;
                    }
                }
                
                // Umschlag (2)
                $this->selectedPapersIds["envelope"] = Array();
                $sql = "SELECT * FROM products_papers WHERE product_id = {$this->id} and part = 2";
                if($DB->num_rows($sql))
                {
                    foreach($DB->select($sql) as $paper)
                    {
                        $this->selectedPapersIds["envelope"][$paper["paper_id"]]["id"] = $paper["paper_id"];
                        $this->selectedPapersIds["envelope"][$paper["paper_id"]][$paper["weight"]] = 1;
                    }
                }
                
                //--------------------------------------------------------------------
                // Get available Paperformats
                $sql = "SELECT * FROM products_formats WHERE product_id = {$this->id}";
                if($DB->num_rows($sql))
                {
                    foreach($DB->select($sql) as $r)
                        $this->availablePaperFormats[] = new Paperformat($r["format_id"]);
                }

                //--------------------------------------------------------------------
                // Get available Chromaticities
                $sql = "SELECT * FROM products_chromaticity WHERE product_id = {$this->id}";
                if($DB->num_rows($sql))
                {
                    foreach($DB->select($sql) as $r)
                        $this->availableChromaticities[] = new Chromaticity($r["chromaticity_id"]);
                }
            }
        }
    }

    /**
     * @param string $order
     * @return Product[]
     */
    static function getAllProducts($order = self::ORDER_ID)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM products WHERE state = 1 ORDER BY {$order}";
        if($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
                $retval[] = new Product($r["id"]);
        }
        return $retval;
    }
    
    static function getAllProductsByIndividuality($whereIsIndividual = false, $order = self::ORDER_ID)
    {
    	global $DB;
    	$retval = array();
    	$whereIsIndividual = ($whereIsIndividual) ? '1' : '0';
    	$sql = "SELECT id FROM products WHERE state = 1 AND is_individual = '{$whereIsIndividual}' ORDER BY {$order}";
    	if($DB->num_rows($sql))
    	{
    		foreach ($DB->select($sql) as $r)
    			$retval[] = new Product($r["id"]);
    	}
    	return $retval;
    }
    
    /**
     * ... liefert alle Produkte einer Warengruppe, die im Onlineshop zur Verf�gung stehen
     * 
     * @param int $tg_id : Id der Warengruppe, in der die Artikel sein sollen
     * @param String $order : Sortierreihenfolge
     * @return Array : Product
     */
    static public function getAllShopProductsByGroup($tg_id, $order = self::ORDER_ID){
    	global $DB;
    	$retval = Array();
    	$sql = "SELECT id FROM products WHERE 
    			state = 1 AND 
    			shop_rel = 1 AND
    			tradegroup = ".$tg_id."
    			ORDER BY {$order}";
    	if($DB->num_rows($sql))
    	{
    		foreach ($DB->select($sql) as $r)
    			$retval[] = new Product($r["id"]);
    	}
    	return $retval;
    }
    
    function save()
    {
        global $DB;
        if($this->getTradegroup()==0 || $this->getTradegroup()==NULL){
        	$this->setTradegroup(new Tradegroup());
        }
        if($this->id > 0)
        {
            $sql = "UPDATE products SET
                        name = '{$this->name}',
                        state = {$this->state},
                        description = '{$this->description}',
                        picture = '{$this->picture}',
                        pages_from = {$this->pagesFrom},
                        pages_to = {$this->pagesTo},
                        pages_step = {$this->pagesStep},
                        has_content = {$this->hasContent},
                        has_addcontent = {$this->hasAddContent},
                        has_addcontent2 = {$this->hasAddContent2},
                        has_addcontent3 = {$this->hasAddContent3},
                        has_envelope = {$this->hasEnvelope},
                        factor_width = {$this->factorWidth},
                        factor_height = {$this->factorHeight},
                        grant_paper = {$this->grantPaper},
                        type = {$this->type},
                        text_offer = '{$this->textOffer}',
                        text_offerconfirm = '{$this->textOfferconfirm}',
                        text_invoice = '{$this->textInvoice}',
                        text_processing = '{$this->textProcessing}',  
                        shop_rel = {$this->shoprel},
                        tradegroup = {$this->getTradegroup()->getId()},
                        is_individual = '{$this->isIndivual}',
                        singleplateset = '{$this->singleplateset}',
                        setmaxproducts_content = '{$this->setmaxproducts_content}',
                        setmaxproducts_content_div = '{$this->setmaxproducts_content_div}',
                        setmaxproducts_addcontent = '{$this->setmaxproducts_addcontent}',
                        setmaxproducts_addcontent_div = '{$this->setmaxproducts_addcontent_div}',
                        setmaxproducts_addcontent2 = '{$this->setmaxproducts_addcontent2}',
                        setmaxproducts_addcontent2_div = '{$this->setmaxproducts_addcontent2_div}',
                        setmaxproducts_addcontent3 = '{$this->setmaxproducts_addcontent3}',
                        setmaxproducts_addcontent3_div = '{$this->setmaxproducts_addcontent3_div}',
                        setmaxproducts_envelope = '{$this->setmaxproducts_envelope}',
                        setmaxproducts_envelope_div = '{$this->setmaxproducts_envelope_div}',
                        blockplateset = '{$this->blockplateset}',
                        usageoverride = '{$this->usageoverride}',
                        default_folding = '{$this->getDefaultFolding()->getId()}',
                        inkcoverage = '{$this->inkcoverage}',
                        finishingcoverage = '{$this->finishingcoverage}',
                        load_dummydata = {$this->loadDymmyData}
                    WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
            echo $DB->getLastError();
            
            //--------------------------------------------------------------------
            // Maschinen
            $sql = "DELETE FROM products_machines WHERE product_id = {$this->id}";
            $DB->no_result($sql);
            foreach($this->machIds as $m)
            {
                $sql = "INSERT INTO products_machines 
                            (product_id, machine_id, `default`, minimum, maximum)
                        VALUES
                            ({$this->id}, {$m}, ";
                if(array_key_exists($m, $this->defaultMachIds))
                    $sql .= "1, {$this->defaultMachIds[$m]["min"]}, {$this->defaultMachIds[$m]["max"]})";
                else 
                    $sql .= "0, 0, 0)";
                
                $DB->no_result($sql);
            }
            
            //---------------------------------------------------------------------
            // Papiere
            // Inhalt (1)
            $sql = "DELETE FROM products_papers WHERE product_id = {$this->id} and part = 1";
            $DB->no_result($sql);
            foreach($this->selectedPapersIds["content"] as $paper)
            {
                foreach(array_keys($paper) as $key)
                {
                    if($key != "id")
                    {
                        $sql = "INSERT INTO products_papers 
                                    (product_id, paper_id, weight, part)
                                VALUES
                                    ({$this->id}, {$paper["id"]}, {$key}, 1)";
                        $DB->no_result($sql);
                    }
                }
            }

            // Umschlag (2)
            $sql = "DELETE FROM products_papers WHERE product_id = {$this->id} and part = 2";
            $DB->no_result($sql);
            foreach($this->selectedPapersIds["envelope"] as $paper)
            {
                foreach(array_keys($paper) as $key)
                {
                    if($key != "id")
                    {
                        $sql = "INSERT INTO products_papers
                                    (product_id, paper_id, weight, part)
                                VALUES
                                    ({$this->id}, {$paper["id"]}, {$key}, 2)";
                        $DB->no_result($sql);
                    }
                }
            }
            
            //---------------------------------------------------------------------
            // Papierformate
            $sql = "DELETE FROM products_formats WHERE product_id = {$this->id}";
            $DB->no_result($sql);
            foreach($this->availablePaperFormats as $pf)
            {
                $sql = "INSERT INTO products_formats
                (product_id, format_id)
                VALUES
                ({$this->id}, {$pf->getId()})";
                $DB->no_result($sql);
            }

            //---------------------------------------------------------------------
            // Farbigkeiten
            $sql = "DELETE FROM products_chromaticity WHERE product_id = {$this->id}";
            $DB->no_result($sql);
            foreach($this->availableChromaticities as $pf)
            {
                $sql = "INSERT INTO products_chromaticity
                (product_id, chromaticity_id)
                VALUES
                ({$this->id}, {$pf->getId()})";
                $DB->no_result($sql);
            }
        } else
        {
            $sql = "INSERT INTO products
                        (name, state, description, picture, pages_from, pages_to, pages_step,
                         has_content, has_addcontent, has_envelope, factor_width, factor_height,
                         grant_paper, type, text_offer, text_offerconfirm, text_invoice,
                         text_processing, shop_rel, tradegroup, is_individual, 
                         has_addcontent2, has_addcontent3, load_dummydata, inkcoverage, finishingcoverage,
                         setmaxproducts_content, setmaxproducts_content_div, setmaxproducts_addcontent, setmaxproducts_addcontent_div,
                         setmaxproducts_addcontent2, setmaxproducts_addcontent2_div, setmaxproducts_addcontent3, setmaxproducts_addcontent3_div,
                         setmaxproducts_envelope, setmaxproducts_envelope_div, default_folding, usageoverride)
                    VALUES
                        ('{$this->name}', 1, '{$this->description}', '{$this->picture}',
                         {$this->pagesFrom}, {$this->pagesTo}, {$this->pagesStep}, {$this->hasContent},
                         {$this->hasAddContent}, {$this->hasEnvelope}, {$this->factorWidth}, {$this->factorHeight},
                         {$this->grantPaper}, {$this->type}, '{$this->textOffer}',
                         '{$this->textOfferconfirm}', '{$this->textInvoice}', '{$this->textProcessing}',
                         {$this->shoprel}, {$this->getTradegroup()->getId()}, {$this->isIndivual},
                         {$this->hasAddContent2}, {$this->hasAddContent3}, {$this->loadDymmyData}, {$this->inkcoverage}, {$this->finishingcoverage}, 
                          {$this->setmaxproducts_content}, {$this->setmaxproducts_content_div}, {$this->setmaxproducts_addcontent}, {$this->setmaxproducts_addcontent_div},
                          {$this->setmaxproducts_addcontent2}, {$this->setmaxproducts_addcontent2_div}, {$this->setmaxproducts_addcontent3}, {$this->setmaxproducts_addcontent3_div},
                          {$this->setmaxproducts_envelope}, {$this->setmaxproducts_envelope_div}, {$this->getDefaultFolding()->getId()}, {$this->usageoverride})";
            $res = $DB->no_result($sql);
            
            if($res)
            {
                $sql = "SELECT max(id) id FROM products WHERE name = '{$this->name}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                
                $sql = "DELETE FROM products_machines WHERE product_id = {$this->id}";
                $DB->no_result($sql);
                foreach($this->machIds as $m)
                {
                    $sql = "INSERT INTO products_machines 
                                (product_id, machine_id, `default`, minimum, maximum)
                            VALUES
                                ({$this->id}, {$m}, ";
                    if(array_key_exists($m, $this->defaultMachIds))
                        $sql .= "1, {$this->defaultMachIds[$m]["min"]}, {$this->defaultMachIds[$m]["max"]})";
                    else 
                        $sql .= "0, 0, 0)";
                    
                    $DB->no_result($sql);
                }
                
                //---------------------------------------------------------------------
                // Papiere
                       
                // Inhalt (1)
                $sql = "DELETE FROM products_papers WHERE product_id = {$this->id} and part = 1";
                $DB->no_result($sql);
                foreach($this->selectedPapersIds["content"] as $paper)
                {
                    foreach(array_keys($paper) as $key)
                    {
                        if($key != "id")
                        {
                            $sql = "INSERT INTO products_papers 
                                        (product_id, paper_id, weight, part)
                                    VALUES
                                        ({$this->id}, {$paper["id"]}, {$key}, 1)";
                            $DB->no_result($sql);
                        }
                    }
                }
    
                // Umschlag (2)
                $sql = "DELETE FROM products_papers WHERE product_id = {$this->id} and part = 2";
                $DB->no_result($sql);
                foreach($this->selectedPapersIds["envelope"] as $paper)
                {
                    foreach(array_keys($paper) as $key)
                    {
                        if($key != "id")
                        {
                            $sql = "INSERT INTO products_papers
                                        (product_id, paper_id, weight, part)
                                    VALUES
                                        ({$this->id}, {$paper["id"]}, {$key}, 2)";
                            $DB->no_result($sql);
                        }
                    }
                }
                
                //---------------------------------------------------------------------
                // Papierformate
                $sql = "DELETE FROM products_formats WHERE product_id = {$this->id}";
                $DB->no_result($sql);
                foreach($this->availablePaperFormats as $pf)
                {
                    $sql = "INSERT INTO products_formats
                                (product_id, format_id)
                            VALUES
                                ({$this->id}, {$pf->getId()})";
                    $DB->no_result($sql);
                }

                //---------------------------------------------------------------------
                // Farbigkeiten
                $sql = "DELETE FROM products_chromaticity WHERE product_id = {$this->id}";
                $DB->no_result($sql);
                foreach($this->availableChromaticities as $pf)
                {
                    $sql = "INSERT INTO products_chromaticity
                              (product_id, chromaticity_id)
                            VALUES
                              ({$this->id}, {$pf->getId()})";
                    $DB->no_result($sql);
                }
                
            } 
        }
        return $res;
    }
    
    function delete()
    {
        global $DB;
        if($this->id > 0)
        {
            $sql = "UPDATE products SET state = 0 WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
            if($res)
            {
                unset($this);
                return true;
            } else
                return false;
        }
    }
    
    function clearId()
    {
        $this->id = 0;
    }

    function isDefaultMachine($mach, $amount = 0)
    {
        if (array_key_exists($mach->getId(), $this->defaultMachIds))
        {
            if($amount > 0)
            {
                // Falls Auflage > min und ( Auflage < max oder max == 0) 
                if($amount >= $this->defaultMachIds[$mach->getId()]["min"]
                    && (($amount <= $this->defaultMachIds[$mach->getId()]["max"]
                            && $this->defaultMachIds[$mach->getId()]["max"] > 0)
                        || $this->defaultMachIds[$mach->getId()]["max"] == 0)
                  )
                    return true;
            } else
                return true;
        }

        return false;
    }

    public function evalMaxProducts($ppp, $part, $pages, $debug = false)
    {
        if ($debug) prettyPrint("++## evalMaxProducts started... ##++");
        switch ($part){
            case Calculation::PAPER_CONTENT:
                $active = $this->getSetmaxproductsContent();
                $div = $this->getSetmaxproductsContentDiv();
                break;
            case Calculation::PAPER_ADDCONTENT:
                $active = $this->getSetmaxproductsAddcontent();
                $div = $this->getSetmaxproductsAddcontentDiv();
                break;
            case Calculation::PAPER_ADDCONTENT2:
                $active = $this->getSetmaxproductsAddcontent2();
                $div = $this->getSetmaxproductsAddcontent2Div();
                break;
            case Calculation::PAPER_ADDCONTENT3:
                $active = $this->getSetmaxproductsAddcontent3();
                $div = $this->getSetmaxproductsAddcontent3Div();
                break;
            case Calculation::PAPER_ENVELOPE:
                $active = $this->getSetmaxproductsEnvelope();
                $div = $this->getSetmaxproductsEnvelopeDiv();
                break;
            default:
                $active = 0;
                $div = 0;
                break;
        }
//        if ($active){
//            if ($debug) prettyPrint('Restrict Max Products is active:');
//            if ($debug) prettyPrint("starting out with {$ppp}");
//            $tosub = $ppp % $div;
//            if ($debug) prettyPrint("check if we need to subtract remainder: tosub = {$tosub}");
//            $retval = $ppp-$tosub;
//            if ($debug) prettyPrint("returning {$retval}");
//            return $retval;
//        }
        if ($active){
            if ($debug) prettyPrint('Restrict Max Products is active:');
            $maxcalced = floor($pages / $div);
            if ($debug) prettyPrint('maxcalced: '.$maxcalced);
            if ($ppp > $maxcalced && $maxcalced > 0){
                if ($debug) prettyPrint("products per paper is > maxcalced   ( {$ppp} > {$maxcalced} )");
                if (($ppp / $maxcalced) > 2){
                    if ($debug) prettyPrint("products per paper / maxcalced is greather than 2  ( ({$ppp}/{$maxcalced})>2 )");
                    $tosub = $ppp % $maxcalced;
                    if ($debug) prettyPrint("check if we need to subtract remainder: tosub = {$tosub}");
                    $retval = $ppp-$tosub;
                    if ($debug) prettyPrint("returning {$retval}");
                    return $retval;
                }
                if ($debug) prettyPrint("returning maxcalced: {$maxcalced}");
                return $maxcalced;
            }
        }
        if ($debug) prettyPrint("returning ppp: {$ppp}");
        return $ppp;
    }
    
    function getMaxForDefaultMachine($mach)
    {
        if (array_key_exists($mach->getId(), $this->defaultMachIds))
            return $this->defaultMachIds[$mach->getId()]["max"];
        else
            return 0;
    }

    function getMinForDefaultMachine($mach)
    {
        if (array_key_exists($mach->getId(), $this->defaultMachIds))
            return $this->defaultMachIds[$mach->getId()]["min"];
        else
            return 0;
    }
    
    function isAvailableMachine($mach)
    {
        if (array_key_exists($mach->getId(), $this->machIds))
            return true;
        else
            return false;
    }
    
    function isSelectedPaper($p)
    {
        if(array_key_exists($p->getId(), $this->selectedPapersIds))
            return true;
        else
            return false;
    }
    
    public function getAvailablePageCounts()
    {
        $retval = Array();
        $x = $this->pagesFrom;
        if($this->pagesStep == 0)
            $this->pagesStep = 1;
        while ($x <= $this->pagesTo)
        {
            $retval[] = $x;
            $x += $this->pagesStep;
        }
        return $retval;
    }
    
    public function hasMachineOfType($type)
    {
        foreach ($this->machines as $m)
        {
            if($m->getType() == $type)
                return true;
        }
        return false;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getState()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture($picture)
    {
        $this->picture = $picture;
    }

    public function getMachines()
    {
        return $this->machines;
    }

    public function setMachines($machines)
    {
        $this->machines = $machines;
    }
    
    public function setDefaultMachIds($defaultMachIds)
    {
        $this->defaultMachIds = $defaultMachIds;
    }

    public function setAvailableMachIds($machIds)
    {
        $this->machIds = $machIds;
    }

    public function getSelectedPapersIds($part = 0)
    {
        if($part == 0)
            return $this->selectedPapersIds;
        else if($part == Calculation::PAPER_CONTENT || $part == Calculation::PAPER_ADDCONTENT)
            return $this->selectedPapersIds["content"];
        else if ($part == Calculation::PAPER_ENVELOPE)
            return $this->selectedPapersIds["envelope"];
    }

    public function setSelectedPapersIds($selectedPapersIds)
    {
        $this->selectedPapersIds = $selectedPapersIds;
    }

    public function getPagesFrom()
    {
        return $this->pagesFrom;
    }

    public function setPagesFrom($pagesFrom)
    {
        $this->pagesFrom = $pagesFrom;
    }

    public function getPagesTo()
    {
        return $this->pagesTo;
    }

    public function setPagesTo($pagesTo)
    {
        $this->pagesTo = $pagesTo;
    }

    public function getPagesStep()
    {
        return $this->pagesStep;
    }

    public function setPagesStep($pagesStep)
    {
        $this->pagesStep = $pagesStep;
    }

    /**
     * @return Paperformat[]
     */
    public function getAvailablePaperFormats()
    {
        return $this->availablePaperFormats;
    }

    public function setAvailablePaperFormats($availablePaperFormats)
    {
        $this->availablePaperFormats = $availablePaperFormats;
    }

    public function getHasContent()
    {
        return $this->hasContent;
    }

    public function setHasContent($hasContent)
    {
        if($hasContent === true || $hasContent == 1)
            $this->hasContent = 1;
        else
            $this->hasContent = 0;
    }

    public function getHasAddContent()
    {
        return $this->hasAddContent;
    }

    public function setHasAddContent($hasAddContent)
    {
        if($hasAddContent === true || $hasAddContent == 1)
            $this->hasAddContent = 1;
        else
            $this->hasAddContent = 0;
    }

    public function getHasEnvelope()
    {
        return $this->hasEnvelope;
    }

    public function setHasEnvelope($hasEnvelope)
    {
        if($hasEnvelope === true || $hasEnvelope == 1)
            $this->hasEnvelope = 1;
        else
            $this->hasEnvelope = 0;
    }

    public function getFactorWidth()
    {
        return $this->factorWidth;
    }

    public function setFactorWidth($factorWidth)
    {
        $this->factorWidth = $factorWidth;
    }

    public function getFactorHeight()
    {
        return $this->factorHeight;
    }

    public function setFactorHeight($factorHeight)
    {
        $this->factorHeight = $factorHeight;
    }

    public function getGrantPaper()
    {
        return $this->grantPaper;
    }

    public function setGrantPaper($grantPaper)
    {
        $this->grantPaper = $grantPaper;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = (int)$type;
    }

    public function getTextOffer()
    {
        return $this->textOffer;
    }

    public function setTextOffer($textOffer)
    {
        $this->textOffer = $textOffer;
    }

    public function getTextOfferconfirm()
    {
        return $this->textOfferconfirm;
    }

    public function setTextOfferconfirm($textOfferconfirm)
    {
        $this->textOfferconfirm = $textOfferconfirm;
    }

    public function getTextInvoice()
    {
        return $this->textInvoice;
    }

    public function setTextInvoice($textInvoice)
    {
        $this->textInvoice = $textInvoice;
    }

    public function getTextProcessing()
    {
        return $this->textProcessing;
    }

    public function setTextProcessing($textProcessing)
    {
        $this->textProcessing = $textProcessing;
    }

	public function getShoprel()
	{
	    return $this->shoprel;
	}

	public function setShoprel($shoprel)
	{
	    $this->shoprel = $shoprel;
	}

	public function getTradegroup()
	{
	    return $this->tradegroup;
	}

	public function setTradegroup($tradegroup)
	{
	    $this->tradegroup = $tradegroup;
	}

    public function getHasAddContent2()
    {
        return $this->hasAddContent2;
    }

    public function setHasAddContent2($hasAddContent2)
    {
        $this->hasAddContent2 = $hasAddContent2;
    }

    public function getHasAddContent3()
    {
        return $this->hasAddContent3;
    }

    public function setHasAddContent3($hasAddContent3)
    {
        $this->hasAddContent3 = $hasAddContent3;
    }

	public function getLoadDymmyData()
	{
	    return $this->loadDymmyData;
	}

	public function setLoadDymmyData($loadDymmyData)
	{
	    $this->loadDymmyData = $loadDymmyData;
	}
	
	public function setIsIndividual($state) {
		$this->isIndivual = $state;
	}
	
	public function getIsIndividual() {
		return (bool) $this->isIndivual;
	}
	/**
     * @return the $singleplateset
     */
    public function getSingleplateset()
    {
        return $this->singleplateset;
    }

	/**
     * @param number $singleplateset
     */
    public function setSingleplateset($singleplateset)
    {
        $this->singleplateset = $singleplateset;
    }

    /**
     * @return int
     */
    public function getBlockplateset()
    {
        return $this->blockplateset;
    }

    /**
     * @param int $blockplateset
     */
    public function setBlockplateset($blockplateset)
    {
        $this->blockplateset = $blockplateset;
    }

    /**
     * @return array
     */
    public function getAvailableChromaticities()
    {
        return $this->availableChromaticities;
    }

    /**
     * @param array $availableChromaticities
     */
    public function setAvailableChromaticities($availableChromaticities)
    {
        $this->availableChromaticities = $availableChromaticities;
    }

    /**
     * @return float
     */
    public function getInkcoverage()
    {
        return $this->inkcoverage;
    }

    /**
     * @param float $inkcoverage
     */
    public function setInkcoverage($inkcoverage)
    {
        $this->inkcoverage = $inkcoverage;
    }

    /**
     * @return int
     */
    public function getSetmaxproductsContent()
    {
        return $this->setmaxproducts_content;
    }

    /**
     * @param int $setmaxproducts_content
     */
    public function setSetmaxproductsContent($setmaxproducts_content)
    {
        $this->setmaxproducts_content = $setmaxproducts_content;
    }

    /**
     * @return int
     */
    public function getSetmaxproductsContentDiv()
    {
        return $this->setmaxproducts_content_div;
    }

    /**
     * @param int $setmaxproducts_content_div
     */
    public function setSetmaxproductsContentDiv($setmaxproducts_content_div)
    {
        $this->setmaxproducts_content_div = $setmaxproducts_content_div;
    }

    /**
     * @return int
     */
    public function getSetmaxproductsAddcontent()
    {
        return $this->setmaxproducts_addcontent;
    }

    /**
     * @param int $setmaxproducts_addcontent
     */
    public function setSetmaxproductsAddcontent($setmaxproducts_addcontent)
    {
        $this->setmaxproducts_addcontent = $setmaxproducts_addcontent;
    }

    /**
     * @return int
     */
    public function getSetmaxproductsAddcontentDiv()
    {
        return $this->setmaxproducts_addcontent_div;
    }

    /**
     * @param int $setmaxproducts_addcontent_div
     */
    public function setSetmaxproductsAddcontentDiv($setmaxproducts_addcontent_div)
    {
        $this->setmaxproducts_addcontent_div = $setmaxproducts_addcontent_div;
    }

    /**
     * @return int
     */
    public function getSetmaxproductsAddcontent2()
    {
        return $this->setmaxproducts_addcontent2;
    }

    /**
     * @param int $setmaxproducts_addcontent2
     */
    public function setSetmaxproductsAddcontent2($setmaxproducts_addcontent2)
    {
        $this->setmaxproducts_addcontent2 = $setmaxproducts_addcontent2;
    }

    /**
     * @return int
     */
    public function getSetmaxproductsAddcontent2Div()
    {
        return $this->setmaxproducts_addcontent2_div;
    }

    /**
     * @param int $setmaxproducts_addcontent2_div
     */
    public function setSetmaxproductsAddcontent2Div($setmaxproducts_addcontent2_div)
    {
        $this->setmaxproducts_addcontent2_div = $setmaxproducts_addcontent2_div;
    }

    /**
     * @return int
     */
    public function getSetmaxproductsAddcontent3()
    {
        return $this->setmaxproducts_addcontent3;
    }

    /**
     * @param int $setmaxproducts_addcontent3
     */
    public function setSetmaxproductsAddcontent3($setmaxproducts_addcontent3)
    {
        $this->setmaxproducts_addcontent3 = $setmaxproducts_addcontent3;
    }

    /**
     * @return int
     */
    public function getSetmaxproductsAddcontent3Div()
    {
        return $this->setmaxproducts_addcontent3_div;
    }

    /**
     * @param int $setmaxproducts_addcontent3_div
     */
    public function setSetmaxproductsAddcontent3Div($setmaxproducts_addcontent3_div)
    {
        $this->setmaxproducts_addcontent3_div = $setmaxproducts_addcontent3_div;
    }

    /**
     * @return int
     */
    public function getSetmaxproductsEnvelope()
    {
        return $this->setmaxproducts_envelope;
    }

    /**
     * @param int $setmaxproducts_envelope
     */
    public function setSetmaxproductsEnvelope($setmaxproducts_envelope)
    {
        $this->setmaxproducts_envelope = $setmaxproducts_envelope;
    }

    /**
     * @return int
     */
    public function getSetmaxproductsEnvelopeDiv()
    {
        return $this->setmaxproducts_envelope_div;
    }

    /**
     * @param int $setmaxproducts_envelope_div
     */
    public function setSetmaxproductsEnvelopeDiv($setmaxproducts_envelope_div)
    {
        $this->setmaxproducts_envelope_div = $setmaxproducts_envelope_div;
    }

    /**
     * @return Foldtype
     */
    public function getDefaultFolding()
    {
        return $this->default_folding;
    }

    /**
     * @param Foldtype $default_folding
     */
    public function setDefaultFolding($default_folding)
    {
        $this->default_folding = $default_folding;
    }

    /**
     * @return int
     */
    public function getUsageoverride()
    {
        return $this->usageoverride;
    }

    /**
     * @param int $usageoverride
     */
    public function setUsageoverride($usageoverride)
    {
        $this->usageoverride = $usageoverride;
    }

    /**
     * @return float
     */
    public function getFinishingcoverage()
    {
        return $this->finishingcoverage;
    }

    /**
     * @param float $finishingcoverage
     */
    public function setFinishingcoverage($finishingcoverage)
    {
        $this->finishingcoverage = $finishingcoverage;
    }
}