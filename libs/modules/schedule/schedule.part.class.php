<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       02.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
class SchedulePart {
    
    private $id;
    private $scheduleId = 0;
    private $finished = 0;
    private $lectorId = 0;
    private $druckplanId = 0;
    private $machines = Array();
    
    function __construct($id = 0)
    {
        global $DB;
        if($id > 0)
        {
            $sql = "SELECT * FROM schedules_parts WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                
                $this->id = $r["id"];
                $this->scheduleId = $r["schedule_id"];
                $this->finished = $r["finished"];
                $this->lectorId = $r["lector_id"];
                $this->druckplanId = $r["druckplan_id"];
            }
        }
    }
    
    static function getAllScheduleParts($scheduleId)
    {
        global $DB;
        $retval = Array();
        
        $sql = "SELECT * FROM schedules_parts WHERE schedule_id = {$scheduleId}";
        
        if($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new SchedulePart($r["id"]);
            }
        }
        return $retval;
    }
    
    function save()
    {
        global $_USER;
        global $DB;
        $set = "finished = {$this->finished},
                schedule_id = {$this->scheduleId},
                lector_id = {$this->lectorId},
                druckplan_id= {$this->druckplanId}";
        
        if($this->id > 0)
        {
            $sql = "UPDATE schedules_parts SET ".$set.", upddat = UNIX_TIMESTAMP(), updusr = {$_USER->getId()} WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else
        {
            $sql = "INSERT INTO schedules_parts SET ".$set.", crtdat = UNIX_TIMESTAMP(), crtusr = {$_USER->getId()}";
            $res = $DB->no_result($sql);
            
            if($res)
            {
                $sql = "SELECT max(id) id FROM schedules_parts WHERE schedule_id = {$this->scheduleId}";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            }
        }
        return false;
    }
    
    function delete()
    {
        global $DB;
        if($this->id > 0)
        {
            $sql = "DELETE FROM schedules_parts WHERE id = {$this->id}";
            $r = $DB->no_result($sql);
            if($r)
            {
                unset($this);
                return true;
            }
        }
        return false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getScheduleId()
    {
        return $this->scheduleId;
    }

    public function setScheduleId($scheduleId)
    {
        $this->scheduleId = $scheduleId;
    }

    public function getFinished()
    {
        return $this->finished;
    }

    public function setFinished($finished)
    {
        $this->finished = $finished;
    }

    public function getLectorId()
    {
        return $this->lectorId;
    }

    public function setLectorId($lectorId)
    {
        $this->lectorId = (int)$lectorId;
    }

    public function getMachines()
    {
        return $this->machines;
    }

    public function setMachines($machines)
    {
        $this->machines = $machines;
    }

    public function getDruckplanId()
    {
        return $this->druckplanId;
    }

    public function setDruckplanId($druckplanId)
    {
        $this->druckplanId = (int)$druckplanId;
    }

}
?>