<?php

class Timer
{

    const TIMER_DELETE = 0;

    const TIMER_RUNNING = 1;

    const TIMER_STOP = 2;

    private $id = 0;

    private $crtuser;

    private $module;

    private $objectid;

    private $state = Timer::TIMER_STOP;

    private $starttime = NULL;

    private $stoptime = NULL;

    function __construct($id = 0)
    {
        global $DB;
        if ($id) {
            $sql = "SELECT * FROM timer WHERE id = {$id};";
            $r = $DB->select($sql);
            $this->id = $r[0]["id"];
            $this->crtuser = $r[0]["crtuser"];
            $this->module = $r[0]["module"];
            $this->objectid = $r[0]["objectid"];
            $this->state = $r[0]["state"];
            $this->starttime = $r[0]["starttime"];
        }
    }

    function save()
    {
        global $DB;
        global $_USER;
        if ($this->id > 0) {
            $sql = "UPDATE timer SET
            crtuser = '{$this->crtuser}',
            module = '{$this->module}',
            objectid = '{$this->objectid}',
            state = '{$this->state}',
            starttime = '{$this->starttime}'
            stoptime = '{$this->stoptime}' 
            WHERE id = {$this->id};";
            return $DB->no_result($sql);
        } else {
            
            $sql = "INSERT INTO timer 
            (crtuser, module, objectid, state, starttime, stoptime) VALUES
            ({$_USER->getId()}, {$this->module}, {$this->objectid}, {$this->state},  {$this->starttime}, {$this->stoptime});";
            $res = $DB->no_result($sql);
            
            if ($res) {
                $sql = "SELECT max(id) id FROM timer WHERE
                crtuser = {$this->crtuser->getId()} AND
                module = {$this->module} AND
                objectid = {$this->objectid} AND
                state = {$this->state};";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else {
                return false;
            }
        }
    }

    public function delete()
    {
        global $DB;
        if ($this->id > 0) {
            $sql = "UPDATE timer SET
            state = {$this->TIMER_DELETE}
            WHERE id = {$this->id}";
            if ($DB->no_result($sql)) {
                unset($this);
                return true;
            } else {
                return false;
            }
        }
    }

    public function start($module, $objectid)
    {
        global $DB;
        global $_USER;
        
        $this->setCrtuser($_USER);
        $this->setModule($module);
        $this->setObjectid($objectid);
        $this->setState(Timer::TIMER_RUNNING);
        
        $sql = "SELECT SELECT max(stoptimer) stoptimer FROM timer WHERE crtuser = {$this->crtuser->getId()} ;";
        $r = $DB->select($sql);
        
        $this->setStarttime($r[0]["stoptimer"]);
        $this->setStoptime(NULL);
    }

    public function stop($cdate = 0)
    {
        if ($cdate)
            $this->stoptime = time();
        else
            $this->stoptime = time();
        $this->state = Timer::TIMER_STOP;
    }

    public static function getLastUsed($crtuser = NULL)
    {
        global $DB;
        global $_USER;
        
        if (! $crtuser)
            $crtuser = $_USER;
        
        $sql = "SELECT max(starttime) id FROM timer WHERE crtuser = {$crtuser};";
        $r = $DB->select($sql);
        $thisid = $DB->select($sql);
        
        $timer = new Timer($thisid[0]["id"]);
        return $timer;
    }

    public static function getTimerList($module, $objectid, $crtuser = NULL)
    {
        global $_USER;
        global $DB;
        $timerlist = array();
        
        if (! $crtuser)
            $crtuser = $_USER;
        
        $sql = "SELECT id FROM timer WHERE 
        module = {$module} AND
        objectid = {$objectid} AND 
        crtuser = {$crtuser};";
        $r = $DB->select($sql);
        
        foreach ($r as $pos) 
            $timerlist[] = new Timer($pos);
        return $timerlist;
    }

    /**
     *
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return the $crtuser
     */
    public function getCrtuser()
    {
        return $this->crtuser;
    }

    /**
     *
     * @return the $module
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     *
     * @return the $objectid
     */
    public function getObjectid()
    {
        return $this->objectid;
    }

    /**
     *
     * @return the $state
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     *
     * @return the $starttime
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

    /**
     *
     * @return the $stoptime
     */
    public function getStoptime()
    {
        return $this->stoptime;
    }

    /**
     *
     * @param field_type $id            
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param
     *            Ambigous <unknown, string> $crtuser
     */
    public function setCrtuser($crtuser)
    {
        $this->crtuser = $crtuser;
    }

    /**
     *
     * @param field_type $module            
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     *
     * @param field_type $objectid            
     */
    public function setObjectid($objectid)
    {
        $this->objectid = $objectid;
    }

    /**
     *
     * @param number $state            
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     *
     * @param string $starttime            
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;
    }

    /**
     *
     * @param string $stoptime            
     */
    public function setStoptime($stoptime)
    {
        $this->stoptime = $stoptime;
    }
}
?>