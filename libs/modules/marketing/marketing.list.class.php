<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       03.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
class MarketingList{
    private $id = 0;
    private $title;
    private $default = 0;

    function __construct($id = 0)
    {
        global $DB;
        if ($id > 0){
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
                $sql = "SELECT * FROM marketing_lists WHERE id = {$id}";
                if ($DB->num_rows($sql)) {
                    $res = $DB->select($sql);
                    $this->id = $res[0]["id"];
                    $this->title = $res[0]["title"];
                    $this->default = $res[0]["default"];

                    Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
                }
            }
        }
    }

    function save()
    {
        global $DB;
        if ($this->id > 0)
        {
            $sql = "UPDATE marketing_lists SET
			title = '{$this->title}',
			`default` = '{$this->default}'
			WHERE id = '{$this->id}'";
            $res = $DB->no_result($sql);
//            echo $sql. "</br>";
        }else{
            $sql = "INSERT INTO marketing_lists (title, `default`)
			VALUES ('{$this->title}',{$this->default})";
            $res = $DB->no_result($sql);
//            echo $sql. "</br>";
            if ($res){
                $sql = "SELECT max(id) id FROM marketing_lists";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
            }
        }
        if ($res){
            Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return MarketingList
     */
    static function getDefaultList()
    {
        global $DB;
        $retval = new MarketingList();
        $sql = "SELECT id FROM marketing_lists WHERE `default` = 1";
        if($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            foreach($res as $r)
            {
                $retval = new MarketingList($r["id"]);
            }
        }
        return $retval;
    }

    /**
     * @return MarketingList[]
     */
    static function getAllLists()
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM marketing_lists";
        if($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            foreach($res as $r)
            {
                $retval[] = new MarketingList($r["id"]);
            }
        }
        return $retval;
    }

    /**
     * @param $businessc BusinessContact
     * @return MarketingList[]
     */
    static function getAllListsForBC(BusinessContact $businessc)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT DISTINCT
                marketing.list
                FROM
                marketing
                INNER JOIN marketing_lists ON marketing.list = marketing_lists.id
                WHERE
                marketing.businesscontact = {$businessc->getId()}";
//        echo $sql;
        if($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            foreach($res as $r)
            {
                $retval[] = new MarketingList($r["list"]);
            }
        }
        return $retval;
    }

    /**
     * @return MarketingColumn[]
     */
    public function getMyColumns()
    {
        return MarketingColumn::getAllColumnsForList($this->id);
    }

    function delete()
    {
        global $DB;
        if($this->id)
        {
            $sql = "DELETE FROM marketing_lists WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
            if($res)
            {
                Cachehandler::removeCache(Cachehandler::genKeyword($this));
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
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param int $default
     */
    public function setDefault($default)
    {
        if ($default == 1){
            $lists = self::getAllLists();
            foreach ($lists as $list) {
                $list->setDefault(0);
                $list->save();
            }
        }
        $this->default = $default;
    }
}
?>