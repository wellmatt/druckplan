<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       03.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'marketing.column.class.php';
require_once 'marketing.list.class.php';

class Marketing{
    private $id = 0;
    private $title;
    private $businesscontact;
    private $crtdate = 0;
    private $crtuser;
    private $data;
    private $list;

    function __construct($id = 0)
    {
        global $DB;
        global $_USER;

        $this->businesscontact = new BusinessContact();
        $this->crtuser = new User($_USER->getId());
        $this->list = new MarketingList();
        if ($id>0)
        {
            $sql = "SELECT * FROM marketing WHERE id = {$id}";
            if ($DB->num_rows($sql))
            {
                $res = $DB->select($sql);
                $this->id = $res[0]["id"];
                $this->title = $res[0]["title"];
                $this->businesscontact = new BusinessContact($res[0]["businesscontact"]);
                $this->crtdate = $res[0]["crtdate"];
                $this->crtuser = new User($res[0]["crtuser"]);
                $this->data = self::fetchData();
                $this->list = new MarketingList();
            }
        }
    }

    function save()
    {
        global $DB;
        if ($this->id > 0)
        {
            $sql = "UPDATE marketing SET
			title = '{$this->title}',
			crtdate = {$this->crtdate},
			businesscontact = {$this->businesscontact->getId()},
			list = {$this->list->getId()}
			WHERE id = '{$this->id}'";
            $res = $DB->no_result($sql);
            if ($res){
                self::deleteData();
                $data = $this->data;
                foreach ($data as $key => $value) {
                    $sql = "INSERT INTO marketing_data (`marketing`, `column`, `value`) VALUES ({$this->id},{$key},'{$value}')";
                    $DB->no_result($sql);
                }
                return true;
            }else{
                return false;
            }
        }else{
            $sql = "INSERT INTO marketing (title, businesscontact, crtuser, crtdate, list)
			VALUES ('{$this->title}', {$this->businesscontact->getId()}, {$this->crtuser->getId()}, {$this->crtdate}, {$this->list->getId()})";
            $res = $DB->no_result($sql);
            if ($res){
                $sql = "SELECT max(id) id FROM marketing";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];

                self::deleteData();
                $data = $this->data;
                foreach ($data as $key => $value) {
                    $sql = "INSERT INTO marketing_data (`marketing`, `column`, `value`) VALUES ({$this->id},{$key},'{$value}')";
                    $DB->no_result($sql);
                }

                return true;
            }else{
                return false;
            }
        }
    }

    /**
     * Delete Marketings Data table entries
     * @return bool
     */
    public function deleteData()
    {
        global $DB;
        $sql = "DELETE FROM marketing_data WHERE marketing = {$this->id}";
        $DB->no_result($sql);
        return true;
    }

    /**
     * Fetch Marketings Data table entries
     * @return array
     */
    public function fetchData()
    {
        global $DB;
        $data = Array();

        $sql = "SELECT `column`, `value` FROM marketing_data WHERE marketing = {$this->id}";
        if($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            foreach($res as $r)
            {
                $data[$r["column"]] = $r["value"];
            }
        }
        return $data;
    }

    public function getColumnValue($column)
    {
        if (is_array($this->data))
            if (array_key_exists($column,$this->data))
                return $this->data[$column];
            else
                return '';
        else
            return '';
    }

    /**
     * @return Marketing[]
     */
    static function getAll()
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM marketing ORDER BY id DESC";
        if($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            foreach($res as $r)
            {
                $retval[] = new Marketing($r["id"]);
            }
        }
        return $retval;
    }

    /**
     * @param $listid
     * @return Marketing[]
     */
    static function getAllForList($listid)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM marketing WHERE list = {$listid} ORDER BY id DESC";
        if($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            foreach($res as $r)
            {
                $retval[] = new Marketing($r["id"]);
            }
        }
        return $retval;
    }

    /**
     * @param $listid
     * @param $businessc BusinessContact
     * @return Marketing[]
     */
    static function getAllForListAndBc($listid, BusinessContact $businessc)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM marketing WHERE list = {$listid} AND businesscontact = {$businessc->getId()} ORDER BY id DESC";
        if($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            foreach($res as $r)
            {
                $retval[] = new Marketing($r["id"]);
            }
        }
        return $retval;
    }

    /**
     * @return bool
     */
    function delete()
    {
        global $DB;
        if($this->id)
        {
            $sql = "DELETE FROM marketing WHERE id = {$this->id}";
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
        return false;
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
     * @return BusinessContact
     */
    public function getBusinesscontact()
    {
        return $this->businesscontact;
    }

    /**
     * @param BusinessContact $businesscontact
     */
    public function setBusinesscontact($businesscontact)
    {
        $this->businesscontact = $businesscontact;
    }

    /**
     * @return mixed
     */
    public function getCrtdate()
    {
        return $this->crtdate;
    }

    /**
     * @param mixed $crtdate
     */
    public function setCrtdate($crtdate)
    {
        $this->crtdate = $crtdate;
    }

    /**
     * @return User
     */
    public function getCrtuser()
    {
        return $this->crtuser;
    }

    /**
     * @param User $crtuser
     */
    public function setCrtuser($crtuser)
    {
        $this->crtuser = $crtuser;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
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