<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       21.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

class Chromaticity {
    const ORDER_COLORS = "colors_front, colors_back";
    const ORDER_NAME = "name";
    const ORDER_ID = "id";
    
    private $id = 0;
    private $name;
    private $colorsFront = 0;
    private $colorsBack = 0;
    private $reversePrinting = 0;
    private $markup = 0.0;
    private $pricekg = 0;
    
    function __construct($id = 0)
    {
        global $DB;
        if($id > 0)
        {
            $sql = "SELECT * FROM chromaticities WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                $this->id = $r["id"];
                $this->name = $r["name"];
                $this->colorsFront = $r["colors_front"];
                $this->colorsBack = $r["colors_back"];
                $this->reversePrinting = $r["reverse_printing"];
                $this->markup = $r["markup"];
                $this->pricekg = $r["pricekg"];
            }
        }
    }
    
    static function getAllChromaticities($order = self::ORDER_NAME)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM chromaticities ORDER BY {$order}";
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new Chromaticity($r["id"]);
            }
        }
        return $retval;
    }
    
    function save()
    {
        global $DB;
        if($this->id > 0)
        {
            $sql = "UPDATE chromaticities SET
                        name = '{$this->name}',
                        colors_front = {$this->colorsFront},
                        colors_back = {$this->colorsBack},
                        reverse_printing = {$this->reversePrinting},
                        markup = {$this->markup},
                        pricekg = {$this->pricekg}
                    WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else
        {
            $sql = "INSERT INTO chromaticities
                        (name, colors_front, colors_back, reverse_printing, markup, pricekg)
                    VALUES
                        ('{$this->name}', {$this->colorsFront}, {$this->colorsBack},
                         {$this->reversePrinting}, {$this->markup}, {$this->pricekg})";
            $res = $DB->no_result($sql);
            
            if($res)
            {
                $sql = "SELECT max(id) id from chromaticities WHERE name = '{$this->name}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else 
                return false;
        }
    }
    
    function delete()
    {
        global $DB;
        if($this->id > 0)
        {
            $sql = "DELETE FROM chromaticities WHERE id = {$this->id}";
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

    public function getColorsFront()
    {
        return $this->colorsFront;
    }

    public function setColorsFront($colorsFront)
    {
        $this->colorsFront = $colorsFront;
    }

    public function getColorsBack()
    {
        return $this->colorsBack;
    }

    public function setColorsBack($colorsBack)
    {
        $this->colorsBack = $colorsBack;
    }

    public function getReversePrinting()
    {
        return $this->reversePrinting;
    }

    public function setReversePrinting($reversePrinting)
    {
        if ($reversePrinting === true || $reversePrinting == 1)
            $this->reversePrinting = 1;
        else
            $this->reversePrinting = 0;
    }

    public function getMarkup()
    {
        return $this->markup;
    }

    public function setMarkup($markup)
    {
        $this->markup = $markup;
    }

    /**
     * @return int
     */
    public function getPricekg()
    {
        return $this->pricekg;
    }

    /**
     * @param int $pricekg
     */
    public function setPricekg($pricekg)
    {
        $this->pricekg = $pricekg;
    }


}

?>