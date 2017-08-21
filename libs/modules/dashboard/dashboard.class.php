<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/dashboard/widget.class.php';

class DashBoard{

    private $id = 0;
    private $user;
    private $row;
    private $column;
    private $module = "Keins";

    /**
     * DashBoard constructor.
     * @param int $id
     */
    function __construct($id = 0) // , $addgroups = true
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
                            if(is_object($cached->{$method}()) === false) {
                                $this->{$var} = $cached->{$method}();
                            } else {
                                $class = get_class($cached->{$method}());
                                $this->{$var} = new $class($cached->{$method}()->getId());
                            }
                        } elseif (method_exists($this,$method2)){
                            if(is_object($cached->{$method2}()) === false) {
                                $this->{$var} = $cached->{$method2}();
                            } else {
                                $class = get_class($cached->{$method2}());
                                $this->{$var} = new $class($cached->{$method2}()->getId());
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
                $sql = " SELECT * FROM dashboard_entries WHERE id = {$id}";

                if ($DB->num_rows($sql) == 1) {
                    $res = $DB->select($sql);
                    $this->id = $res[0]["id"];
                    $this->user = new User($res[0]["user"]);
                    $this->row = $res[0]["row"];
                    $this->column = $res[0]["column"];
                    $this->module = $res[0]["module"];

                    Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
                    return true;
                }
            }
        }
    }

    /**
     * @return bool
     */
    function save() {
        global $DB;
        if ($this->id > 0)
        {
            $sql = " UPDATE dashboard_entries SET
            `user` = {$this->user->getId()},
            `row` = {$this->row},
            `column` = {$this->column},
            module = '{$this->module}'
            WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
        } else
        {
            $sql = " INSERT INTO dashboard_entries
            (`user`, `row`, `column`, module)
            VALUES
            ( {$this->user->getId()}, {$this->row}, {$this->column}, '{$this->module}' )";
            $res = $DB->no_result($sql);

            if ($res)
            {
                $sql = " SELECT max(id) id FROM dashboard_entries";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
            }
        }
        if ($res)
        {
            Cachehandler::toCache(Cachehandler::genKeyword($this),$this);
            return true;
        }
        else
            return false;
    }

    /**
     * @return bool
     */
    function delete() {
        global $DB;
        $sql = "DELETE FROM dashboard_entries WHERE id = {$this->id}";
        $res = $DB->no_result($sql);
        if ($res) {
            Cachehandler::removeCache(Cachehandler::genKeyword($this));
            unset($this);
            return true;
        }
        else
            return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public static function clearForUser(User $user)
    {
        global $DB;
        $sql = "DELETE FROM dashboard_entries WHERE `user` = {$user->getId()}";
        $res = $DB->no_result($sql);
        return $res;
    }

    /**
     * @param User $user
     * @return DashBoard[]
     */
    public static function getAllForUser(User $user)
    {
        global $DB;
        $ret = Array();

        $sql = "SELECT id FROM dashboard_entries WHERE `user` = {$user->getId()}";
        if ($DB->num_rows($sql)) {
            foreach ($DB->select($sql) as $r) {
                $ret[] = new DashBoard($r["id"]);
            }
        }
        return $ret;
    }

    /**
     * @param User $user
     * @param int $row
     * @param int $column
     * @return DashBoard
     */
    public static function getForUserAndPosition(User $user, $row, $column)
    {
        global $DB;
        $ret = new DashBoard();

        $sql = "SELECT id FROM dashboard_entries WHERE `user` = {$user->getId()} AND `row` = {$row} AND `column` = {$column}";
        if ($DB->num_rows($sql)) {
            $r = $DB->select($sql);
            $ret = new DashBoard($r[0]["id"]);
        }
        return $ret;
    }

    /**
     * @param User $user
     * @return int
     */
    public static function countRowsForUser(User $user)
    {
        global $DB;
        $count = 0;

        $sql = "SELECT DISTINCT `row` FROM dashboard_entries WHERE `user` = {$user->getId()}";
        if ($DB->num_rows($sql)) {
            $r = $DB->select($sql);
            $count = count($r);
        }
        return $count;
    }

    /**
     * @return array
     */
    public static function getWidgets()
    {
        $files = [];
        $path = "./libs/modules/dashboard/widgets/";
        $widgets = array_diff(scandir($path), array('.', '..'));
        array_unshift($widgets,'Keins');

        foreach ($widgets as $widget) {
            $files[] = str_replace('.php','',$widget);
        }

        return $files;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getRow()
    {
        return $this->row;
    }

    /**
     * @param int $row
     */
    public function setRow($row)
    {
        $this->row = $row;
    }

    /**
     * @return int
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @param int $column
     */
    public function setColumn($column)
    {
        $this->column = $column;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param string $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }
}