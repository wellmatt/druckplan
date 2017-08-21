<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/model.php';
require_once 'libs/modules/oauth/oauthscope.class.php';


class oAuthClient extends Model {
    public $_table = 'oauth_clients';
    public $id = '';
    public $secret;
    public $name;
    public $created_at;
    public $updated_at;
    public $scopes = [];

    /**
     * @return oAuthClient[]
     */
    public static function getAll(){
        $retval = self::fetch();
        return $retval;
    }

    private function loadScopes(){
        global $DB;

        $sql = "SELECT scope_id FROM oauth_client_scopes WHERE client_id = '{$this->id}'";
        if($DB->num_rows($sql)) {
            foreach ($DB->select($sql) as $r) {
                $this->scopes[] = $r["scope_id"];
            }
        }
    }

    public function saveScopes($scopes)
    {
        global $DB;
        $sql = "DELETE FROM oauth_client_scopes WHERE client_id = '{$this->id}'";
        $DB->no_result($sql);

        foreach ($scopes as $scope) {
            $sql = "INSERT INTO oauth_client_scopes (client_id, scope_id) VALUES ('{$this->id}','{$scope}')";
//            prettyPrint($sql);
            $DB->no_result($sql);
        }
    }

    public function hasScope($hasscope)
    {
        $ret = false;
        foreach ($this->scopes as $scope) {
            if ($scope == $hasscope)
                $ret = true;
        }
        return $ret;
    }

    protected function bootClasses()
    {
        $this->loadScopes();
    }

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
            if (!in_array($index,Array('updated_at','created_at','scopes','id','_exists','_table')))
                if (is_object($var))
                    $set[] = " `{$index}` = '{$var->getId()}' ";
                else
                    $set[] = " `{$index}` = '{$var}' ";
        }
        if ($this->id == '') {
            $set[] = " `id` = '" . generateRandomString(40) . "'";
            $set[] = " `created_at` = '".date('Y-m-d G:i:s')."'";
        } else {
            $set[] = " `updated_at` = '".date('Y-m-d G:i:s')."'";
        }
        $set = implode(',',$set);

        if ($this->id != '') {
            $sql = "UPDATE {$this->_table} SET {$set} WHERE id = '{$this->id}'";
//            prettyPrint($sql);
            return $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO {$this->_table} SET {$set}";
//            prettyPrint($sql);
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
     * @return mixed
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param mixed $secret
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
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

    /**
     * @return array
     */
    public function getScopes()
    {
        return $this->scopes;
    }
}