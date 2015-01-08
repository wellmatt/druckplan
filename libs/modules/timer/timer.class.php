<?php

class Timer
{

    const TIMER_DELETED = 0;

    const TIMER_RUNNING = 1;

    const TIMER_STOP = 2;

    private $id = 0;

    private $crtuser;

    private $module;

    private $objectid;

    private $state = self::TIMER_STOP;

    private $starttime = NULL;

    private $stoptime = NULL;

    function __construct($id = 0)
    {
        global $DB;
        
        $this->crtuser	= new User(0);
        
        if ($id) {
            $sql = "SELECT * FROM timer WHERE id = {$id};";
            $r = $DB->select($sql);
            $this->id = $r[0]["id"];
            $this->crtuser = new User((int)$r[0]["crtuser"]);
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
            ({$_USER->getId()}, '{$this->module}', {$this->objectid}, {$this->state},  {$this->starttime}, {$this->stoptime});";
            $res = $DB->no_result($sql);
            
            if ($res) {
                $sql = "SELECT max(id) id FROM timer WHERE
                crtuser = {$this->crtuser->getId()} AND
                module = '{$this->module}' AND
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
        $now = time();
        
        $this->setCrtuser($_USER);
        $this->setModule($module);
        $this->setObjectid($objectid);
        $this->setState(self::TIMER_RUNNING);
        
        $sql = "SELECT max(id) id FROM timer WHERE crtuser = {$this->crtuser->getId()} ;";
        $r = $DB->select($sql);
        
        $lasttimer = new Timer($r[0]["id"]);
        if($lasttimer->getState() == self::TIMER_RUNNING)
        {
            $lasttimer->stop($now);
            $lasttimer->save();
            $this->setStarttime(++$now);
        }
        else 
            $this->setStarttime($lasttimer->getStoptime()+1);
    }

    public function stop($cdate = 0)
    {
        if ($cdate)
            $this->stoptime = $cdate;
        else
            $this->stoptime = time();
        $this->state = self::TIMER_STOP;
    }

    public static function getLastUsed($crtuser = NULL)
    {
        global $DB;
        global $_USER;
        $timer = false;
        
        if (! $crtuser)
            $crtuser = $_USER;
        
        $sql = "SELECT max(id) id FROM timer WHERE crtuser = {$crtuser->getId()};";
        if($DB->num_rows($sql)){
            $timer = new Timer($thisid[0]["id"]);
        }
        
        return $timer;
    }

    public static function getTimerForObject($module, $objectid, $filter = "")
    {
        global $DB;
        $retval = Array();
    
        $sql = "SELECT id FROM timer WHERE module = '{$module}' AND objectid = {$objectid} {$filter};";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new Timer($r["id"]);
            }
        }
    
        return $retval;
    }
    
    public static function getAllTimer($filter = "")
    {
        global $DB;
        $retval = Array();
    
        $sql = "SELECT id FROM timer {$filter};";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new Timer($r["id"]);
            }
        }
    
        return $retval;
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