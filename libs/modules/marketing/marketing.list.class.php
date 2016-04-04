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
        if ($id>0)
        {
            $sql = "SELECT * FROM marketing_lists WHERE id = {$id}";
            if ($DB->num_rows($sql))
            {
                $res = $DB->select($sql);
                $this->id = $res[0]["id"];
                $this->title = $res[0]["title"];
                $this->default = $res[0]["default"];
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
            if ($res){
                return true;
            }else{
                return false;
            }
        }else{
            $sql = "INSERT INTO marketing_lists (title, `default`)
			VALUES ('{$this->title}',{$this->default})";
            $res = $DB->no_result($sql);
//            echo $sql. "</br>";
            if ($res){
                $sql = "SELECT max(id) id FROM marketing_lists";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            }else{
                return false;
            }
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