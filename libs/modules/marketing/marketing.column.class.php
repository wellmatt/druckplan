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
        $this->list = new MarketingList();
        global $DB;
        if ($id>0)
        {
            $sql = "SELECT * FROM marketing_columns WHERE id = {$id}";
            if ($DB->num_rows($sql))
            {
                $res = $DB->select($sql);
                $this->id = $res[0]["id"];
                $this->title = $res[0]["title"];
                $this->sort = $res[0]["sort"];
                $this->list = new MarketingList($res[0]["list"]);
            }
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
            if ($res){
                return true;
            }else{
                return false;
            }
        }else{
            $sql = "INSERT INTO marketing_columns (title, sort, list)
			VALUES ('{$this->title}', {$this->sort}, {$this->list->getId()})";
            $res = $DB->no_result($sql);
            if ($res){
                $sql = "SELECT max(id) id FROM marketing_columns";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            }else{
                return false;
            }
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