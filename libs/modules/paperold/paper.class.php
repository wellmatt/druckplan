<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       12.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
class Paper {
    
    const ORDER_ID = "id";
    const ORDER_NAME = "name";
    
    const PRICE_PER_THOUSAND = 1;
    const PRICE_PER_100KG = 2;
    
    const PAPER_DIRECTION_WIDE = 1;
    const PAPER_DIRECTION_SMALL = 0;
    
    private $id = 0;
    private $name;
    private $comment;
    private $status;
    private $priceBase = 0;
    private $selectedSize;
    private $selectedWeight;
    private $sizes = Array();
    private $weights = Array();
    private $prices = Array();
	private $supplier = Array();
	private $dilivermat;
	private $glue;
	private $thickness;
	private $totalweight;
	private $price_100kg;
	private $price_1qm;
    private $volume;
    private $rolle;
    
    function __construct($id = 0) {
        global $DB;
        global $_USER;
        
        if($id > 0)
        {
            $sql = "SELECT * FROM papers WHERE id = {$id}";
            if ($DB->num_rows($sql))
            {
                $res = $DB->select($sql);
                $this->id = $res[0]["id"];
                $this->name = $res[0]["name"];
                $this->comment = $res[0]["comment"];
                $this->priceBase = $res[0]["pricebase"];
                $this->dilivermat = $res[0]["dilivermat"];
                $this->glue = $res[0]["glue"];
                $this->thickness = $res[0]["thickness"];
                $this->totalweight = $res[0]["totalweight"];
                $this->price_100kg = $res[0]["price_100kg"];
                $this->price_1qm = $res[0]["price_1qm"];
                $this->price_1qm = $res[0]["volume"];
                $this->rolle = $res[0]["rolle"];
                
                $sql = "SELECT * FROM papers_weights 
                        WHERE paper_id = {$this->id}
                        ORDER BY weight";
                if($DB->num_rows($sql))
                {
                    foreach($DB->select($sql) as $r)
                    {
                        $this->weights[] = $r["weight"];
                    }
                }
                
                $sql = "SELECT * FROM papers_sizes 
                        WHERE paper_id = {$this->id}
                        ORDER BY width";
                if($DB->num_rows($sql))
                {
                    $i = 0;
                    foreach($DB->select($sql) as $r)
                    {
                        $this->sizes[$i]["width"] = $r["width"];
                        $this->sizes[$i]["height"] = $r["height"];
                        $i++;
                    }
                }
				
                $sql = "SELECT * FROM papers_supplier 
                        WHERE paper_id = {$this->id}
                        ORDER BY supplier_id";
                if($DB->num_rows($sql))
                {
                    $i = 0;
                    foreach($DB->select($sql) as $r)
                    {
                        $this->supplier[$i] = Array('id'=>$r["supplier_id"],'descr'=>$r["description"]);
                        $i++;
                    }
                }
                
                $sql = "SELECT weight_from, weight_to, size_width, size_height,
                            quantity_from, price, weight 
                        FROM papers_prices
                        WHERE paper_id = {$this->id}
                        ORDER BY size_width, size_height, weight_from";
                if($DB->num_rows($sql))
                {
                    foreach($DB->select($sql) as $r)
                        $this->prices[] = $r;
                }
                
            }
        }
    }

    /**
     * @param string $order
     * @return Paper[]
     */
    static function getAllPapers($order = self::ORDER_NAME)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM papers WHERE status = 1 ORDER BY {$order}";
        if($DB->num_rows($sql)) 
        {
            $res = $DB->select($sql);
            foreach($res as $r)
            {
                $retval[] = new Paper($r["id"]);
            }
        }
        return $retval;
    }

    static function getAllPapersByName($order = self::ORDER_NAME, $search = '')
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM papers WHERE status = 1 AND name LIKE '%{$search}%' ORDER BY {$order}";
        if($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            foreach($res as $r)
            {
                $retval[] = new Paper($r["id"]);
            }
        }
        return $retval;
    }

    static function getAllUniquePaperSizes()
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT DISTINCT CONCAT(width, 'x', height) as size FROM papers_sizes ORDER BY CONCAT(width, 'x', height) asc";
        if($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            foreach($res as $r)
            {
                $retval[] = $r["size"];
            }
        }
        return $retval;
    }
    
    static function getAllUniquePaperFormats()
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT DISTINCT name, width, height FROM formats ORDER BY name asc";
        if($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            foreach($res as $r)
            {
                $retval[] = $r;
            }
        }
        return $retval;
    }
    
    function getSumPrice($amount)
    {
        global $DB;
        if ($this->priceBase == Paper::PRICE_PER_100KG)
        {
            $area = $this->selectedSize["width"] * $this->selectedSize["height"];
            $weight = (($area * $this->selectedWeight / 10000 / 100) * $amount) / 1000;
            $sql = "SELECT price FROM papers_prices 
                    WHERE {$this->selectedWeight} >= weight_from
                        AND {$this->selectedWeight} <= weight_to
                        AND quantity_from <= {$weight}
                        AND paper_id = {$this->id}
                    ORDER BY quantity_from desc, price
                    LIMIT 1";
            $res = $DB->select($sql);
            $price = ($weight * $res[0]["price"]) / 100;
        } elseif ($this->rolle == 1) {
            $sql = "SELECT price, size_height FROM papers_prices
                    WHERE ((size_width = {$this->selectedSize["width"]})
                            OR (size_width = {$this->selectedSize["height"]}))
                        AND weight_from = {$this->selectedWeight}
                        AND quantity_from <= {$amount}
                        AND paper_id = {$this->id}
                    ORDER BY quantity_from desc, price
                    LIMIT 1";
            $res = $DB->select($sql);
//            prettyPrint($sql);
//            prettyPrint($amount.' * ( '.$res[0]["price"].' / ( '.$res[0]["size_height"].' / 1000 ) ) ');
            $price = $amount * ($res[0]["price"]/($res[0]["size_height"]/1000));
        } else
        {
            $sql = "SELECT price FROM papers_prices 
                    WHERE ((size_width = {$this->selectedSize["width"]}
                            AND size_height = {$this->selectedSize["height"]})
                            OR (size_width = {$this->selectedSize["height"]}
                            AND size_height = {$this->selectedSize["width"]}))
                        AND weight_from = {$this->selectedWeight}
                        AND quantity_from <= {$amount}
                        AND paper_id = {$this->id}
                    ORDER BY quantity_from desc, price
                    LIMIT 1";
            $res = $DB->select($sql);
            $price = $amount * $res[0]["price"] / 1000;
        }
        return $price;
    }
    
    function getAvailablePaperSizesForMachine(Machine $machine, $minWidth = 0, $minHeight = 0, $rolle = 0, $productheight = 0)
    {
        global $DB;
        $sqlsizes = Array();
        
        $minHeight = $minHeight + $machine->getBorder_bottom() + $machine->getBorder_top();
        $minWidth = $minWidth + $machine->getBorder_left() + $machine->getBorder_right();
    
        // get possible Papersizes
        $sql = "SELECT * FROM papers_sizes
        WHERE paper_id = {$this->id}
        AND (( width <= {$machine->getPaperSizeWidth()} AND height <= {$machine->getPaperSizeHeight()})
            OR (width <= {$machine->getPaperSizeHeight()} AND height <= {$machine->getPaperSizeWidth()}))
        AND (( width >= {$machine->getPaperSizeMinWidth()} AND height >= {$machine->getPaperSizeMinHeight()})
            OR (width >= {$machine->getPaperSizeMinHeight()} AND height >= {$machine->getPaperSizeMinWidth()}))";
        if ($rolle == 0)
            $sql .= "AND (( width >= {$minWidth} AND height >= {$minHeight}) OR (width >= {$minHeight} AND height >= {$minWidth}))";
    
//         echo $sql;
//        prettyPrint($sql);
//        prettyPrint($machine->getId());
        if($DB->num_rows($sql))
            $sqlsizes = $DB->select($sql);

        if ($rolle == 1)
            for ($i = 0; $i < count($sqlsizes); $i++)
                $sqlsizes[$i]["height"] = $productheight;

//        print_r($sqlsizes);
        return $sqlsizes;
    }
    
    function getMaxPaperSizeForMachine($machine)
    {
        global $DB;
        $size = Array();
        $size["height"] = 0;
        $size["width"] = 0;
        
        // get possible Papersizes
        $sql = "SELECT * FROM papers_sizes 
                WHERE paper_id = {$this->id}
                    AND (( width <= {$machine->getPaperSizeWidth()} AND height <= {$machine->getPaperSizeHeight()})
                        OR (width <= {$machine->getPaperSizeHeight()} AND height <= {$machine->getPaperSizeWidth()}))
                    AND (( width >= {$machine->getPaperSizeMinWidth()} AND height >= {$machine->getPaperSizeMinHeight()})
                        OR (width >= {$machine->getPaperSizeMinHeight()} AND height >= {$machine->getPaperSizeMinWidth()}))";

//        echo $sql;

        if($DB->num_rows($sql))
        {
            $sqlsizes = $DB->select($sql);
            $x = 0; $idx = 0;
            $area = 0;
            foreach($sqlsizes as $sz)
            {
                $newarea = $sz["height"] * $sz["width"];
                if($area < $newarea)
                {
                    $area = $newarea;
                    $idx = $x;
                }
                $x++;
            }
            
            $size["height"] = $sqlsizes[$idx]["height"];
            $size["width"] = $sqlsizes[$idx]["width"]; 
        }
        return $size;
    }
    
    function getPaperDirection($calc, $part) {
        global $_CONFIG;
        
        $mach = Machineentry::getMachineForPapertype($part, $calc->getId());
        $mach = $mach[0]->getMachine();
        
        // Basisdaten auslesen
        if($part == Calculation::PAPER_CONTENT) {
            $paper = $calc->getPaperContent();
            $paperH = $calc->getPaperContentHeight();
            $paperW = $calc->getPaperContentWidth();
        } else if ($part == Calculation::PAPER_ADDCONTENT) {
            $paper = $calc->getPaperAddContent();
            $paperH = $calc->getPaperAddContentHeight();
            $paperW = $calc->getPaperAddContentWidth();
        } else if ($part == Calculation::PAPER_ENVELOPE) {
            $paper = $calc->getPaperEnvelope();
            $paperH = $calc->getPaperEnvelopeHeight();
            $paperW = $calc->getPaperEnvelopeWidth();
        } else if ($part == Calculation::PAPER_ADDCONTENT2) {
            $paper = $calc->getPaperAddContent2();
            $paperH = $calc->getPaperAddContent2Height();
            $paperW = $calc->getPaperAddContent2Width();
        } else if ($part == Calculation::PAPER_ADDCONTENT3) {
            $paper = $calc->getPaperAddContent3();
            $paperH = $calc->getPaperAddContent3Height();
            $paperW = $calc->getPaperAddContent3Width();
        } else
            die('Wrong part');
        
        if($part != Calculation::PAPER_ENVELOPE)
        {
            $width = $calc->getProductFormatWidthOpen();
            $height = $calc->getProductFormatHeightOpen();
        } else {
            $width = $calc->getEnvelopeWidthOpen();
            $height = $calc->getEnvelopeHeightOpen();
        }
        $width_closed     = $calc->getProductFormatWidth();
        $height_closed     = $calc->getProductFormatHeight();
        
        // Inhalt
        if ($width_closed < $width && $width_closed != 0 )
            $multiRows = floor(ceil($width * 1.01) / $width_closed);
        else
            $multiRows = 1;
        if ($height_closed < $height && $height_closed != 0 )
            $multiCols = floor(ceil($height * 1.01) / $height_closed);
        else
            $multiCols = 1;
        
        // Anschnitt setzen
        $tmp_anschnitt = $_CONFIG->anschnitt;
        if($part == Calculation::PAPER_CONTENT){
        	$tmp_anschnitt = $calc->getCutContent();
        } else if ($part == Calculation::PAPER_ADDCONTENT){
        	$tmp_anschnitt = $calc->getCutAddContent();
        } elseif($part == Calculation::PAPER_ENVELOPE){
        	$tmp_anschnitt = $calc->getCutContent();
        }
        
        // Farbrand (Farbkontrollstreifen) setzen
        $tmp_farbrand = $_CONFIG->farbRandBreite;
        if($calc->getColorControl() == 0){
        	// Wenn der Farbrand in der Kalkulation ausgestellt ist
        	$tmp_farbrand = 0;
        }
        
        $product_width       = $width;
        $product_height      = $height;
        $product_width_closed       = $width_closed;
        $product_height_closed      = $height_closed;
        $usesize_width       = $product_width + $_CONFIG->anschnitt * 2;
        $usesize_height      = $product_height + $_CONFIG->anschnitt * 2;
        $product_per_line    = floor(($paperW - $mach->getBorder_left() - $mach->getBorder_right()) / $usesize_width);
        $product_rows        = floor(($paperH - $mach->getBorder_top() - $mach->getBorder_bottom() - $_CONFIG->farbRandBreite) / $usesize_height);
        $product_per_line_closed    = floor(($paperW - $mach->getBorder_left() - $mach->getBorder_right()) / $usesize_width) * $multiRows;
        $product_rows_closed        = floor(($paperH - $mach->getBorder_top() - $mach->getBorder_bottom() - $_CONFIG->farbRandBreite) / $usesize_height) * $multiCols;
        $product_per_paper   = $product_per_line * $product_rows;


        $product_width2      = $height;
        $product_height2     = $width;
        $product_width2_closed      = $height_closed;
        $product_height2_closed     = $width_closed;
        $usesize_width2      = $product_width2 + $_CONFIG->anschnitt * 2;
        $usesize_height2     = $product_height2 + $_CONFIG->anschnitt * 2;
        $product_per_line2   = floor(($paperW - $mach->getBorder_left() - $mach->getBorder_right()) / $usesize_width2);
        $product_rows2       = floor(($paperH - $mach->getBorder_top() - $mach->getBorder_bottom() - $_CONFIG->farbRandBreite) / $usesize_height2);
        $product_per_line2_closed   = floor(($paperW - $mach->getBorder_left() - $mach->getBorder_right()) / $usesize_width2) * $multiCols;
        $product_rows2_closed       = floor(($paperH - $mach->getBorder_top() - $mach->getBorder_bottom() - $_CONFIG->farbRandBreite) / $usesize_height2) * $multiRows;
        $product_per_paper2  = $product_per_line2 * $product_rows2;
        
        if($product_per_paper2 >= $product_per_paper)
        {
            $flipped = true;
            $product_rows     = $product_rows2;
            $product_per_line = $product_per_line2;
            $product_rows_closed     = $product_rows2_closed;
            $product_per_line_closed = $product_per_line2_closed;
        
            $product_width    = $product_width2;
            $product_height   = $product_height2;
            $product_width_closed    = $product_width2_closed;
            $product_height_closed   = $product_height2_closed;
        
            $t = $multiCols;
            $multiCols = $multiRows;
            $multiRows = $t;
        }
        
        // Height ist senkrecht, Width waagerecht
        // Schmale Bahn = 0, breite Bahn = 1
        if ($this->selectedSize["height"] <= $this->selectedSize["width"])
            $paperIsLandscape = true;
        
        if ($product_height <= $product_width)
            $placedProductIsLandscape = true;
        
        if ($height <= $width)
            $productIsLandscape = true;
    
        if ($paperIsLandscape)
        {
            if ($productIsLandscape)
            {
                if ($placedProductIsLandscape)
                {
                    return self::PAPER_DIRECTION_WIDE;
                } else
                {
                    return self::PAPER_DIRECTION_SMALL;
                }
            } else
            {
                if ($placedProductIsLandscape)
                {
                    return self::PAPER_DIRECTION_SMALL;
                } else
                {
                    return self::PAPER_DIRECTION_WIDE;
                }
            }
        } else
        {
            if ($productIsLandscape)
            {
                if ($placedProductIsLandscape)
                {
                    return self::PAPER_DIRECTION_SMALL;
                } else
                {
                    return self::PAPER_DIRECTION_WIDE;
                }
            } else
            {
                if ($placedProductIsLandscape)
                {
                    return self::PAPER_DIRECTION_WIDE;
                } else
                {
                    return self::PAPER_DIRECTION_SMALL;
                }
            }
        }
    }
    
    function save() 
    {
        global $DB;
        if($this->id > 0)
        {
            
            $sql = "UPDATE papers SET
                        name = '{$this->name}',
                        comment = '{$this->comment}',
                        dilivermat = '{$this->dilivermat}',
                        glue = '{$this->glue}',
                        thickness = '{$this->thickness}',
                        totalweight = '{$this->totalweight}',
                        price_100kg = '{$this->price_100kg}',
                        price_1qm = '{$this->price_1qm}',
                        volume = '{$this->volume}',
                        rolle = {$this->rolle},
                        pricebase = {$this->priceBase}
                    WHERE
                        id = {$this->id}";
            $DB->no_result($sql);
//            echo $sql;

            $sql = "DELETE FROM papers_sizes WHERE paper_id = {$this->id}";
            $DB->no_result($sql);
            foreach($this->sizes as $s)
            {
                $sql = "INSERT INTO papers_sizes 
                            (paper_id, width, height)
                        VALUES
                            ({$this->id}, {$s["width"]}, {$s["height"]})";
                $DB->no_result($sql);
            }
			
            $sql = "DELETE FROM papers_supplier WHERE paper_id = {$this->id}";
            $DB->no_result($sql);
            foreach($this->supplier as $s)
            {
                $sql = "INSERT INTO papers_supplier 
                            (paper_id, supplier_id, description)
                        VALUES
                            ({$this->id}, {$s['id']}, '{$s['descr']}')";
                $DB->no_result($sql);
            }

            $sql = "DELETE FROM papers_weights WHERE paper_id = {$this->id}";
            $DB->no_result($sql);
            foreach($this->weights as $w)
            {
                $sql = "INSERT INTO papers_weights
                            (paper_id, weight)
                        VALUES
                            ({$this->id}, {$w})";

                $DB->no_result($sql);

            }
            
            $sql = "DELETE FROM papers_prices WHERE paper_id = {$this->id}";
            $DB->no_result($sql);
            foreach($this->prices as $p)
            {
                $sql = "INSERT INTO papers_prices
                            (paper_id, weight_from, weight_to, size_width, size_height,
                             quantity_from, price, weight)
                        VALUES
                            ({$this->id}, {$p["weight_from"]}, {$p["weight_to"]},
                             {$p["size_width"]}, {$p["size_height"]}, {$p["quantity_from"]},
                             {$p["price"]}, {$p["weight"]})";
                $DB->no_result($sql);
            }
            return true; 
            
        } else
        {
            $sql = "INSERT INTO papers 
                        (name, comment, pricebase, dilivermat, glue, thickness, totalweight, price_100kg, price_1qm)
                    VALUES
                        ('{$this->name}', '{$this->comment}', {$this->priceBase}, 
                        '{$this->dilivermat}', '{$this->glue}', '{$this->thickness}', '{$this->totalweight}',
                        '{$this->price_100kg}', '{$this->price_1qm}')";
            $res = $DB->no_result($sql);
            if($res)
            {
                $sql = "SELECT max(id) id FROM papers WHERE name='{$this->name}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                
                foreach($this->sizes as $s)
                {
                    $sql = "INSERT INTO papers_sizes
                                (paper_id, width, height)
                            VALUES
                                ({$this->id}, {$s["width"]}, {$s["height"]})";
                    $DB->no_result($sql);
                }
            
                foreach($this->weights as $w)
                {
                    $sql = "INSERT INTO papers_weights
                                (paper_id, weight)
                            VALUES
                                ({$this->id}, {$w})";
                    $DB->no_result($sql);
                }                
                
                return true;
            } else
                return false;
        }
    }
    
    function delete() 
    {
        global $DB;
        if($this->id)
        {
            $sql = "UPDATE papers SET status = 0 WHERE id = {$this->id}";
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

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }
    
 

    public function getPriceBase()
    {
        return $this->priceBase;
    }

    public function setPriceBase($priceBase)
    {
        $this->priceBase = $priceBase;
    }
    
    public function hasPriceBase()
    {
    	return ($this->priceBase > 0);
    }
    public function getSelectedSize()
    {
        return $this->selectedSize;
    }

    public function setSelectedSize($selectedSize)
    {
        $this->selectedSize = $selectedSize;
    }

    public function getSelectedWeight()
    {
        return $this->selectedWeight;
    }

    public function setSelectedWeight($selectedWeight)
    {
        $this->selectedWeight = $selectedWeight;
    }

    public function getSizes()
    {
        return $this->sizes;
    }

    public function setSizes($sizes)
    {
        $this->sizes = $sizes;
    }

    public function getWeights()
    {
        return $this->weights;
    }

    public function setWeights($weights)
    {
        $this->weights = $weights;
    }

    public function getPrices()
    {
        return $this->prices;
    }

    public function setPrices($prices)
    {
        $this->prices = $prices;
    }

    public function getSupplier()
    {
        return $this->supplier;
    }

    public function setSupplier($supplier)
    {
        $this->supplier = $supplier;
    }
    
	/**
     * @return the $comment
     */
    public function getComment()
    {
        return $this->comment;
    }

	/**
     * @param field_type $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }
    
	/**
     * @return the $dilivermat
     */
    public function getDilivermat()
    {
        return $this->dilivermat;
    }

	/**
     * @return the $glue
     */
    public function getGlue()
    {
        return $this->glue;
    }

	/**
     * @return the $thickness
     */
    public function getThickness()
    {
        return $this->thickness;
    }

	/**
     * @return the $totalweight
     */
    public function getTotalweight()
    {
        return $this->totalweight;
    }

	/**
     * @param field_type $dilivermat
     */
    public function setDilivermat($dilivermat)
    {
        $this->dilivermat = $dilivermat;
    }

	/**
     * @param field_type $glue
     */
    public function setGlue($glue)
    {
        $this->glue = $glue;
    }

	/**
     * @param field_type $thickness
     */
    public function setThickness($thickness)
    {
        $this->thickness = $thickness;
    }

	/**
     * @param field_type $totalweight
     */
    public function setTotalweight($totalweight)
    {
        $this->totalweight = $totalweight;
    }
    
	/**
     * @return the $price_100kg
     */
    public function getPrice_100kg()
    {
        return $this->price_100kg;
    }

	/**
     * @return the $price_1qm
     */
    public function getPrice_1qm()
    {
        return $this->price_1qm;
    }

	/**
     * @param field_type $price_100kg
     */
    public function setPrice_100kg($price_100kg)
    {
        $this->price_100kg = $price_100kg;
    }

	/**
     * @param field_type $price_1qm
     */
    public function setPrice_1qm($price_1qm)
    {
        $this->price_1qm = $price_1qm;
    }

    /**
     * @return mixed
     */
    public function getVolume()
    {
        return $this->volume;
    }

    /**
     * @param mixed $volume
     */
    public function setVolume($volume)
    {
        $this->volume = $volume;
    }

    /**
     * @return mixed
     */
    public function getRolle()
    {
        return $this->rolle;
    }

    /**
     * @param mixed $rolle
     */
    public function setRolle($rolle)
    {
        $this->rolle = $rolle;
    }

}
?>