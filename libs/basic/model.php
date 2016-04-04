<?php

class Model {

    public $id = 0;
    public $_exists = false;
    public $_table = null;

    /**
     * Model constructor.
     * @param int $id
     * @param array $params
     */
    public function __construct($id = 0, Array $params = []) {
        global $DB;

        if ($id > 0){
            $sql = "SELECT * FROM {$this->_table} WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];

                foreach ($r as $key => $value) {
                    if (property_exists(get_class($this), $key)) {
                        $this->$key = $value;
                    }
                }
                $this->_exists = true;
            }
        }

        if (is_array($params)) {
            foreach ($params as $index => $param) {
                if (property_exists(get_class($this),$index)){
                    $this->$index = $param;
                }
            }
        }
        $this->bootClasses();
    }

    protected function bootClasses()
    {

    }

    /**
     * @return bool
     */
    public function save()
    {
        global $DB;

        $set = [];
        $vars = get_object_vars($this);
        foreach ($vars as $index => $var) {
            if (!in_array($index,Array('id','_exists','_table')))
                if (is_object($var))
                    $set[] = " `{$index}` = '{$var->getId()}' ";
                else
                    $set[] = " `{$index}` = '{$var}' ";
        }
        $set = implode(',',$set);

        if ($this->id > 0) {
            $sql = "UPDATE {$this->_table} SET {$set} WHERE id = {$this->id}";
//            prettyPrint($sql);
            return $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO {$this->_table} SET {$set}";
            $res = $DB->no_result($sql);
//            prettyPrint($sql);
            if ($res) {
                $sql = "SELECT max(id) id FROM {$this->_table}";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {
        global $DB;
        if($this->id)
        {
            $sql = "DELETE FROM {$this->_table} WHERE id = {$this->id}";
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
}