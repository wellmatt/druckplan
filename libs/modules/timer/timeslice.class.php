<?php

class TimeSlice
{

    private $id = 0;
    private $timerid;
    private $starttime = 0;
    private $stoptime = 0;

    function __construct($id = 0)
    {
        global $DB;
        
        $this->crtuser	= new User(0);
        
        if ($id) {
            $sql = "SELECT * FROM timers_slices WHERE id = {$id};";
            $r = $DB->select($sql);
            $this->id = $r[0]["id"];
            $this->timerid = $r[0]["timerid"];
            $this->starttime = $r[0]["starttime"];
            $this->stoptime = $r[0]["stoptime"];
        }
    }

    function save()
    {
        global $DB;
        global $_USER;
        if ($this->id > 0) {
            $sql = "UPDATE timers_slices SET 
            timerid = {$this->timerid}, 
            starttime = {$this->starttime}, 
            stoptime = {$this->stoptime} 
            WHERE id = {$this->id};";
            return $DB->no_result($sql);
        } else {
            
            $sql = "INSERT INTO timers_slices 
            (timerid, starttime, stoptime) VALUES 
            ({$this->timerid},  {$this->starttime}, {$this->stoptime});";
            $res = $DB->no_result($sql);
            
            if ($res) {
                $sql = "SELECT max(id) id FROM timers_slices WHERE
                timerid = {$this->timerid};";
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
        if ($this->id > 0) {
            $sql = "DELETE FROM timers_slices
                    WHERE id = {$this->id}";
            if ($DB->no_result($sql)) {
                unset($this);
                return true;
            } else {
                return false;
            }
        }
    }

    public static function getSlicesForTimer($timerid)
    {
        global $DB;
        $retval = Array();
    
        $sql = "SELECT id FROM timers_slices WHERE timerid = {$timerid};";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new TimeSlice($r["id"]);
            }
        }
    
        return $retval;
    }
    
	/**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

	/**
     * @return the $timerid
     */
    public function getTimerid()
    {
        return $this->timerid;
    }

	/**
     * @return the $starttime
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

	/**
     * @return the $stoptime
     */
    public function getStoptime()
    {
        return $this->stoptime;
    }

	/**
     * @param number $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

	/**
     * @param field_type $timerid
     */
    public function setTimerid($timerid)
    {
        $this->timerid = $timerid;
    }

	/**
     * @param number $starttime
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;
    }

	/**
     * @param number $stoptime
     */
    public function setStoptime($stoptime)
    {
        $this->stoptime = $stoptime;
    }

    
    
}
?>