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

    private $stoptime = 0;
    
    private $timeslices = Array();

    function __construct($id = 0)
    {
        global $DB;
        
        $this->crtuser	= new User(0);
        
        if ($id) {
            $sql = "SELECT * FROM timers WHERE id = {$id};";
            $r = $DB->select($sql);
            $this->id = $r[0]["id"];
            $this->crtuser = new User((int)$r[0]["crtuser"]);
            $this->module = $r[0]["module"];
            $this->objectid = $r[0]["objectid"];
            $this->state = $r[0]["state"];
            $this->starttime = $r[0]["starttime"];
            $this->stoptime = $r[0]["stoptime"];
        }
    }

    function save()
    {
        global $DB;
        global $_USER;
        if ($this->id > 0) {
            $sql = "UPDATE timers SET 
            state = {$this->state}, 
            starttime = {$this->starttime}, 
            stoptime = {$this->stoptime} 
            WHERE id = {$this->id};";
//             echo $sql;
            return $DB->no_result($sql);
        } else {
            
            $sql = "INSERT INTO timers 
            (crtuser, module, objectid, state, starttime, stoptime) VALUES 
            ({$_USER->getId()}, '{$this->module}', {$this->objectid}, {$this->state},  {$this->starttime}, {$this->stoptime});";
            $res = $DB->no_result($sql);
//             echo $sql;
            
            if ($res) {
                $sql = "SELECT max(id) id FROM timers WHERE
                crtuser = {$this->crtuser->getId()} AND
                module = '{$this->module}' AND
                objectid = {$this->objectid} AND
                state = {$this->state};";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                $this->crtuser = $_USER;
                return true;
            } else {
                return false;
            }
        }
    }

    public function delete()
    {
        global $DB;
        $status = Timer::TIMER_DELETED;
        if ($this->id > 0) {
            $sql = "UPDATE timers SET
            state = {$status}  
            WHERE id = {$this->id}";
            if ($DB->no_result($sql)) {
                unset($this);
                return true;
            } else {
                return false;
            }
        }
    }

    public function start($module, $objectid, $timestamp = 0)
    {
        global $DB;
        global $_USER;
        if ($timestamp == 0){
            $now = time();
        } else {
            $now = $timestamp;
        }
        
        $this->setModule($module);
        $this->setObjectid($objectid);
        $this->setState(self::TIMER_RUNNING);

        $this->setStarttime($now);
        
        $sql = "SELECT max(id) id FROM timers WHERE crtuser = {$_USER->getId()} ;";
        if($DB->num_rows($sql)){
            $r = $DB->select($sql);
            $lasttimer = new Timer($r[0]["id"]);
            if ($lasttimer->getState() == self::TIMER_RUNNING){
                $lasttimer->stop($now);
                $lasttimer->save();
                $this->setStarttime($lasttimer->getStoptime()+1);
            }
        }
        
        $this->save();
    }

    public function stop($cdate = 0)
    {
        if ($cdate)
            $this->stoptime = $cdate;
        else
            $this->stoptime = time();
        $this->state = self::TIMER_STOP;
        
        $this->save();
    }

    public static function getLastUsed($userid = 0)
    {
        global $DB;
        global $_USER;
        $timer = new Timer();
        
        if ($userid == 0)
            $userid = $_USER->getId();
        
        $sql = "SELECT max(id) id FROM timers WHERE crtuser = {$userid};";
        if($DB->num_rows($sql)){
            $r = $DB->select($sql);
            $timer = new Timer($r[0]["id"]);
        }
//         echo $sql;
        
        return $timer;
    }

    public static function getTimerForObject($module, $objectid, $filter = "")
    {
        global $DB;
        $retval = Array();
    
        $sql = "SELECT id FROM timers WHERE module = '{$module}' AND objectid = {$objectid} {$filter};";
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
    
        $sql = "SELECT id FROM timers {$filter};";
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