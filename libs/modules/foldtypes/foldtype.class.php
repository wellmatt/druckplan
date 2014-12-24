<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       14.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
class Foldtype
{
    const ORDER_NAME = "name";
    const ORDER_ID = "id";

    private $id = 0;
    private $name;
    private $description;
    private $vertical = 0;
    private $horizontal = 0;
    private $status;
    private $picture;

    function __construct($id = 0)
    {
        global $DB;
        if($id > 0)
        {
            $sql = "SELECT * FROM foldtypes WHERE status=1 AND id={$id}";
            if($DB->num_rows($sql))
            {
                $res = $DB->select($sql);
                $res = $res[0];

                $this->id = $res["id"];
                $this->name = $res["name"];
                $this->description = $res["beschreibung"];
                $this->vertical = $res["vertical"];
                $this->horizontal = $res["horizontal"];
                $this->status = $res["status"];
                $this->picture = $res["picture"];
            }
        }
    }

    static function getAllFoldTypes($order = self::ORDER_ID)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM foldtypes WHERE status=1 
        ORDER BY {$order}";
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql)as $r)
            {
                $retval[] = new Foldtype($r["id"]);
            }
        }
        return $retval;
    }

    static function getFoldTypesForPages($pages)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM foldtypes WHERE status=1 
                AND 
                    (vertical + 1) * (horizontal + 1) = {$pages}
                    OR (vertical + 1) * (horizontal + 1) * 2 = {$pages}";
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql)as $r)
            {
                $retval[] = new Foldtype($r["id"]);
            }
        }
        return $retval;
        
    }
    
    function save()
    {
        global $DB;
        if($this->id > 0)
        {
            $sql = "UPDATE foldtypes SET
                        name = '{$this->name}',
                        beschreibung = '{$this->description}',
                        status = {$this->status},
                        vertical = {$this->vertical},
                        horizontal = {$this->horizontal},
                        picture = '{$this->picture}'
                    WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else
        {
            $sql = "INSERT INTO foldtypes
                        (name, beschreibung, status, vertical, horizontal, picture)
                    VALUES
                        ('{$this->name}', '{$this->description}', 1, {$this->vertical},
                         {$this->horizontal}, '{$this->picture}')";
            $res = $DB->no_result($sql);
            
            if($res)
            {
                $sql = "SELECT max(id) id FROM foldtypes WHERE name = '{$this->name}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            }
        }
        return false;
    }
    
    function delete()
    {
        global $DB;
        $sql = "UPDATE foldtypes SET status = 0 WHERE id = {$this->id}";
        if($DB->no_result($sql))
        {
            unset($this);
            return true;
        } else
            return false;
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

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getVertical()
    {
        return $this->vertical;
    }

    public function setVertical($vertical)
    {
        $this->vertical = $vertical;
    }

    public function getHorizontal()
    {
        return $this->horizontal;
    }

    public function setHorizontal($horizontal)
    {
        $this->horizontal = $horizontal;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture($picture)
    {
        $this->picture = $picture;
    }
}
?>