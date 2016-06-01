<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       03.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
class MarketingColumn{
    private $id = 0;
    private $title;
    private $sort;
    private $list;

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
                $sql = "SELECT * FROM marketing_columns WHERE id = {$id}";
                if ($DB->num_rows($sql)) {
                    $res = $DB->select($sql);
                    $this->id = $res[0]["id"];
                    $this->title = $res[0]["title"];
                    $this->sort = $res[0]["sort"];
                    $this->list = new MarketingList($res[0]["list"]);

                    Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
                }
            }
        } else {
            $this->list = new MarketingList();
        }
    }

    function save()
    {
        global $DB;
        if ($this->id > 0)
        {
            $sql = "UPDATE marketing_columns SET
			title = '{$this->title}',
			sort = {$this->sort}
			WHERE id = '{$this->id}'";
            $res = $DB->no_result($sql);
        }else{
            $sql = "INSERT INTO marketing_columns (title, sort, list)
			VALUES ('{$this->title}', {$this->sort}, {$this->list->getId()})";
            $res = $DB->no_result($sql);
            if ($res){
                $sql = "SELECT max(id) id FROM marketing_columns";
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
     * @return MarketingColumn[]
     */
    static function getAllColumns()
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM marketing_columns ORDER BY sort ASC";
        if($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            foreach($res as $r)
            {
                $retval[] = new MarketingColumn($r["id"]);
            }
        }
        return $retval;
    }

    /**
     * @param $listid MarketingList-ID
     * @return MarketingColumn[]
     */
    static function getAllColumnsForList($listid)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM marketing_columns WHERE list = {$listid} ORDER BY sort ASC";
        if($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            foreach($res as $r)
            {
                $retval[] = new MarketingColumn($r["id"]);
            }
        }
        return $retval;
    }

    function delete()
    {
        global $DB;
        if($this->id)
        {
            $sql = "DELETE FROM marketing_columns WHERE id = {$this->id}";
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
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param mixed $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }

    /**
     * @return MarketingList
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param MarketingList $list
     */
    public function setList($list)
    {
        $this->list = $list;
    }
}
?>