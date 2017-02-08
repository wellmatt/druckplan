<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

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

    protected function bootClasses(){}

    /**
     * @return bool
     */
    public function save()
    {
        global $DB;
        self::hook_beforeSave();

        $set = [];
        $vars = get_object_vars($this);
        foreach ($vars as $index => $var) {
            if (!in_array($index,Array('id','_exists','_table')) && substr($index,0,1) != '_')
                if (is_object($var))
                    $set[] = " `{$index}` = '{$var->getId()}' ";
                else
                    $set[] = " `{$index}` = '{$var}' ";
        }
        $set = implode(',',$set);

        if ($this->id > 0) {
            $sql = "UPDATE {$this->_table} SET {$set} WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
            if ($res){
                self::hook_afterSave();
                return true;
            } else {
                return false;
            }
        } else {
            $sql = "INSERT INTO {$this->_table} SET {$set}";
//            prettyPrint($sql);
            $res = $DB->no_result($sql);
            if ($res) {
                $sql = "SELECT max(id) id FROM {$this->_table}";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                self::hook_afterSave();
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Function that is executed before the object is saved to DB
     */
    protected function hook_beforeSave(){}

    /**
     * Function that is executed after the object is saved to DB (successfully)
     */
    protected function hook_afterSave(){}

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
     * Gibt ein Array aus Objekten zurück bei dem die angegebene Spalte dem angegebenen Wert entspricht
     *
     * $filterarray format: Array( Array('column'=>'Spalte','operator'=>'>','value'=>'0') )
     * $filterarray: falls operator nicht angegeben dann wird = genutzt
     * $filterarray: es kann ein Array angegeben werden um die Rückgabe zu sortieren Array('orderby'=>Spalte,'orderbydir'=>'desc')
     *
     * @param $filterarray
     * @param int $single
     * @return array
     */
    public static function fetch($filterarray = Array(), $single = 0)
    {
        global $DB;
        $retval = [];
        $class = get_called_class();
        $filter = [];
        $orderby = [];
        $limit = '';

        if ($single == 1)
            $limit = ' LIMIT 1 ';

        $tmp_obj = new $class();
        $table = $tmp_obj->_table;

        if ($filterarray){
            foreach ($filterarray as $array) {
                if ($array){
                    if (array_key_exists('column',$array) && array_key_exists('operator',$array) && array_key_exists('value',$array)){
                        if (property_exists($class, $array['column'])){
                            $filter[] = ' '.$array['column']." {$array['operator']} "."'{$array['value']}' ";
                        }
                    } elseif (array_key_exists('column',$array) && array_key_exists('value',$array)){
                        if (property_exists($class, $array['column'])){
                            $filter[] = ' '.$array['column']." = "."'{$array['value']}' ";
                        }
                    } elseif (array_key_exists('orderby',$array) && array_key_exists('orderbydir',$array)){
                        if (property_exists($class, $array['orderby'])){
                            if ($array['orderbydir'] == 'asc' || $array['orderbydir'] == 'desc')
                                $orderby[] = ' '.$array['orderby'].' '.$array['orderbydir'].' ';
                        }
                    } elseif (array_key_exists('orderby',$array)){
                        if (property_exists($class, $array['orderby'])){
                            $orderby[] = ' '.$array['orderby'].' asc ';
                        }
                    }
                }
            }
        }

        if (count($orderby)>0)
            $orderby = ' ORDER BY ' . implode(',',$orderby);
        else
            $orderby = '';

        if (count($filter)>0){
            $filter = implode(' AND ',$filter);
            $sql = "SELECT id FROM {$table} WHERE {$filter} {$orderby} {$limit}";
//            prettyPrint($sql);
            if ($DB->num_rows($sql)){
                foreach($DB->select($sql) as $r){
                    $obj = new $class($r['id']);
                    /* @var $obj $class */
                    $retval[] = $obj;
                }
                return $retval;
            } else {
                return [];
            }
        } else {
            $sql = "SELECT id FROM {$table} {$orderby} {$limit}";
            if ($DB->num_rows($sql)){
                foreach($DB->select($sql) as $r){
                    $obj = new $class($r['id']);
                    /* @var $obj $class */
                    $retval[] = $obj;
                }
                return $retval;
            } else {
                return [];
            }
        }
    }

    /**
     * Gibt ein Objekten zurück bei dem die angegebene Spalte dem angegebenen Wert entspricht
     *
     * $filterarray format: Array( Array('column'=>'Spalte','operator'=>'>','value'=>'0') )
     * $filterarray: falls operator nicht angegeben dann wird = genutzt
     * $filterarray: es kann ein Array angegeben werden um die Rückgabe zu sortieren Array('orderby'=>Spalte,'oderbydir'=>'desc')
     *
     * @param $filterarray
     * @return object
     */
    public static function fetchSingle($filterarray)
    {
        $class = get_called_class();
        $retval = self::fetch($filterarray,1);
        if (count($retval)>0)
            $retval = $retval[0];
        else
            $retval = new $class();
        return $retval;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
}