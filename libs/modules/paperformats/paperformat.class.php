<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       19.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

class Paperformat
{
    const ORDER_NAME = "name";
    const ORDER_ID = "id";
    
    private $id;
    private $name;
    private $width;
    private $height;  
    
    function __construct($id = 0)
    {
        global $DB;
        if($id > 0)
        {
            $sql = "SELECT * FROM formats WHERE id = {$id}";
            if ($DB->num_rows($sql))
            {
                $res = $DB->select($sql);
                $res = $res[0];
                $this->id = $res["id"];
                $this->name = $res["name"];
                $this->width = $res["width"];
                $this->height = $res["height"];
            }
        }
    }
    
    public function save()
    {
        global $DB;
        
        $set = "name = '{$this->name}',
                width = {$this->width},
                height = {$this->height}";
        
        if ($this->id > 0)
        {
            $sql = "UPDATE formats SET {$set}
                    WHERE id = '{$this->id}'";
            $res = $DB->no_result($sql);
            if ($res)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            $sql = "INSERT INTO formats SET {$set}";
            $res = $DB->no_result($sql);
            if ($res)
            {
                $sql = "SELECT max(id) id FROM formats";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            }
            else
            {
                return false;
            }
        }
    }
    
    public function delete()
    {
        global $DB;
        if($this->id)
        {
            $sql = "DELETE FROM formats WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
            if($res)
            {
                unset($this);
                return true; 
            }
            else
            {
                return false;
            }
        }
    }

    /**
     * @param string $order
     * @return Paperformat[]
     */
    static function getAllPaperFormats($order = self::ORDER_ID)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM formats ORDER BY {$order}";
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new Paperformat($r["id"]); 
            }
        }
        return $retval;
    }
    
    public function clearId()
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

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setWidth($width)
    {
        $this->width = $width;
    }

    public function setHeight($height)
    {
        $this->height = $height;
    }
}
?>