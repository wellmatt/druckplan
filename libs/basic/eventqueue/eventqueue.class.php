<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/basic/eventqueue/eventclass.interface.php';
require_once 'libs/basic/eventqueue/eventclass.class.php';
require_once 'libs/basic/eventqueue/eventregisterar.class.php';
require_once 'libs/basic/eventqueue/eventclasstest.class.php';

/**
 * Class EventQueue
 */
class EventQueue {
    /**
     * @var int
     */
    private $id = 0;
    /**
     * @var string
     */
    private $key = '';
    /**
     * @var int
     */
    private $status = 0;
    /**
     * @var int
     */
    private $crttime = 0;
    /**
     * @var int
     */
    private $runtime = 0;
    /**
     * @var int
     */
    private $firedby = 0;
    /**
     * @var string
     */
    private $eventclass = '';
    /**
     * @var string
     */
    private $function = '';
    /**
     * @var array
     */
    private $eventargs = [];

    /**
     * EventQueue constructor.
     * @param int $id
     * @param array $params
     */
    public function __construct($id = 0, $params = [])
    {
        global $DB;
        $this->firedby = new User();

        if($id>0){
            $sql = "SELECT * FROM eventqueue WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                $this->id 				= (int)$r["id"];
                $this->key 			    = $r["key"];
                $this->status		    = $r["status"];
                $this->crttime 		    = (int)$r["crttime"];
                $this->runtime 		    = (int)$r["runtime"];
                $this->firedby          = (int)$r["firedby"];
                $this->eventclass	    = $r["eventclass"];
                $this->function	        = $r["function"];
                $this->eventargs		= unserialize($r["eventargs"]);
            }
        }

        if (is_array($params)) {
            foreach ($params as $index => $param) {
                if (property_exists(get_class($this),$index)){
                    $this->{$index} = $param;
                }
            }
        }
    }

    /**
     * EventQueue factory.
     * @param array $params
     * @return EventQueue
     */
    public static function factory($params)
    {
        $event = new EventQueue();
        if (is_array($params)) {
            foreach ($params as $index => $param) {
                if (property_exists(get_class($event),$index)){
                    if ($index == "eventargs")
                        $event->{$index} = unserialize($param);
                    else
                        $event->{$index} = $param;
                }
            }
        }
        return $event;
    }

    /**
     * @return bool
     */
    function save()
    {
        global $DB;
        $this->crttime = time();
        $args = serialize($this->eventargs);

        if ($this->id > 0) {
            return false;
        } else {
            $sql = "INSERT INTO eventqueue
            (`key`, `status`, `crttime`, `runtime`, `firedby`, `eventclass`, `function`, `eventargs` )
            VALUES
            ( NULL, {$this->status}, {$this->crttime}, {$this->runtime}, {$this->firedby}, '{$this->eventclass}', '{$this->function}', '{$args}' )";
            $res = $DB->insert($sql);
            if ($res) {
                $this->id = $res;
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
        if ($this->id > 0) {
            $sql = "DELETE FROM eventqueue WHERE id = {$this->id}";
            if ($DB->no_result($sql)) {
                unset($this);
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    /**
     * Works the DB Queue
     * @return bool
     */
    public static function workqueue()
    {
        global $DB;
        $key = uniqid('',true);
        $now = time();

        $sql = "UPDATE eventqueue SET `key` = '{$key}' WHERE `key` IS NULL AND `runtime` <= {$now};";
        $DB->no_result($sql);
        $sql = "SELECT * FROM eventqueue WHERE `key` = '{$key}';";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $event = EventQueue::factory($r);
                if ($event->getId() > 0){
                    $res = $event->dispatch();

                    if ($res){
                        $sql = "DELETE FROM eventqueue WHERE id = {$event->getId()};";
                        $DB->no_result($sql);
                        return true;
                    } else {
                        $sql = "UPDATE eventqueue SET `status` = 2 WHERE id = {$event->getId()};";
                        $DB->no_result($sql);
                        return false;
                    }
                } else {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public function dispatch()
    {
        if ($this->id > 0 && $this->status == 0 && class_exists($this->eventclass) && is_array($this->eventargs)){
            $event = new $this->eventclass();
            $res = $event->fire($this->function, $this->eventargs);
            if ($res){
                return true;
            } else {
                return false;
            }
        } else
            return false;
    }

    /**
     * @return array
     */
    public function getEventClasses()
    {
        $children  = array();
        foreach(get_declared_classes() as $class){
            if($class instanceof EventClass) $children[] = $class;
        }
        return $children;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getCrttime()
    {
        return $this->crttime;
    }

    /**
     * @param int $crttime
     */
    public function setCrttime($crttime)
    {
        $this->crttime = $crttime;
    }

    /**
     * @return int
     */
    public function getRuntime()
    {
        return $this->runtime;
    }

    /**
     * @param int $runtime
     */
    public function setRuntime($runtime)
    {
        $this->runtime = $runtime;
    }

    /**
     * @return int
     */
    public function getFiredby()
    {
        return $this->firedby;
    }

    /**
     * @param int $firedby
     */
    public function setFiredby($firedby)
    {
        $this->firedby = $firedby;
    }

    /**
     * @return string
     */
    public function getEventclass()
    {
        return $this->eventclass;
    }

    /**
     * @param string $eventclass
     */
    public function setEventclass($eventclass)
    {
        $this->eventclass = $eventclass;
    }

    /**
     * @return array|mixed
     */
    public function getEventargs()
    {
        return $this->eventargs;
    }

    /**
     * @param array|mixed $eventargs
     */
    public function setEventargs($eventargs)
    {
        $this->eventargs = $eventargs;
    }
}