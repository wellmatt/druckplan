<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';
require_once 'libs/modules/oauth/oauthclient.class.php';


class oAuthScope extends Model {
    public $_table = 'oauth_scopes';
    public $id = '';
    public $description;
    public $created_at;
    public $updated_at;

    /**
     * Model constructor.
     * @param string $id
     * @param array $params
     */
    public function __construct($id = '', Array $params = []) {
        global $DB;

        if ($id != ''){
            $sql = "SELECT * FROM {$this->_table} WHERE id = '{$id}'";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];

                foreach ($r as $key => $value) {
                    if (property_exists(get_class($this), $key)) {
                        $this->{$key} = $value;
                    }
                }
                $this->_exists = true;
            }
        }

        if (is_array($params)) {
            foreach ($params as $index => $param) {
                if (property_exists(get_class($this),$index)){
                    $this->{$index} = $param;
                }
            }
        }
        $this->bootClasses();
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
            if (!in_array($index,Array('scopes','id','_exists','_table')))
                if (is_object($var))
                    $set[] = " `{$index}` = '{$var->getId()}' ";
                else
                    $set[] = " `{$index}` = '{$var}' ";
        }
        if (!$this->id)
            $set[] = " `id` = '".generateRandomString(40)."'";
        $set = implode(',',$set);

        if ($this->id != '') {
            $sql = "UPDATE {$this->_table} SET {$set} WHERE id = '{$this->id}'";
            return $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO {$this->_table} SET {$set}";
            $res = $DB->no_result($sql);
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
        if($this->id != '')
        {
            $sql = "DELETE FROM {$this->_table} WHERE id = '{$this->id}'";
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
     * @return oAuthScope[]
     */
    public static function getAll(){
        $retval = self::fetch();
        return $retval;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

}