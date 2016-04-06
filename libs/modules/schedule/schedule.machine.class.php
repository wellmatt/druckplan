<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       02.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/machines/machine.class.php';
require_once 'schedule.part.class.php';
require_once 'schedule.class.php';
require_once 'schedule.downtime.class.php';

class ScheduleMachine {
    const FILTER_MACHINE = 1;
    const FILTER_PART = 2;
    const FILTER_MACHINEGROUP = 3;
    
    const ORDER_STATUS_OPEN = 1;
    const ORDER_STATUS_CLOSED = 2;
    
    private $id = 0;
    private $schedulePartId;
    private $machineGroup = 0;
    private $machine = null;
    private $targetTime = 0;
    private $actualTime = 0;
    private $downTime = 0;
    private $downTimeType = 0;
    private $deadline = '0000-00-00';
    private $notes = '';
    private $priority = 0;
    private $finished = 0;
    private $lectorId = 0;
    private $amount = 0;
    private $colors = '';
    private $finishing = '';
    
    public function __construct($id = 0)
    {
        $this->machine = new Machine();
        global $DB;
        if($id > 0)
        {
            $sql = "SELECT * FROM schedules_machines WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                
                $this->id = $r["id"];
                $this->schedulePartId = $r["schedules_part_id"];
                $this->machineGroup = $r["machine_group"];
                $this->machine = new Machine($r["machine_id"]);
                $this->targetTime = $r["target_time"];
                if($r["actual_time"])
                    $this->actualTime = $r["actual_time"];
                if($r["down_time"])
                    $this->downTime = $r["down_time"];
                if($r["down_time_type"])
                    $this->downTimeType = $r["down_time_type"];
                $this->deadline = $r["deadline"];
                $this->notes = $r["notes"];
                $this->priority = $r["priority"];
                $this->finished = $r["finished"];
                $this->lectorId = $r["lector_id"];
                $this->amount = $r["amount"];
                $this->colors = $r["colors"];
                $this->finishing = $r["finishing"];
                
            }
        }
    }
    
    static function getAllScheduleMachines($partId, $filterBy = self::FILTER_PART, $filterId = 0) 
    {
        global $DB;
        $retval = Array();

        $sql = "SELECT id FROM schedules_machines WHERE 1=1 ";
        if($partId > 0)
            $sql .= " AND schedules_part_id = {$partId}";
        if($filterBy == self::FILTER_MACHINE)
            $sql .= " AND machine_id = {$filterId}";
        if($filterBy == self::FILTER_MACHINEGROUP)
            $sql .= " AND machine_group = {$filterId}";
        
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new ScheduleMachine($r["id"]);
            }
        }
        return $retval;
    }
    
    function save()
    {
        if($this->machineGroup == 0 && $this->machine != null)
            $this->machineGroup = $this->machine->getGroup()->getId();
            
        global $DB;
        global $_USER;
        $set = "schedules_part_id = {$this->schedulePartId},
                machine_group = {$this->machineGroup},
                machine_id = {$this->machine->getId()},
                target_time = {$this->targetTime},
                actual_time = {$this->actualTime},
                down_time = {$this->downTime},
                down_time_type = {$this->downTimeType},
                deadline = '{$this->deadline}',
                notes = '{$this->notes}',
                priority = {$this->priority},
                finished = {$this->finished},
                lector_id = {$this->lectorId},
                amount = {$this->amount},
                colors = '{$this->colors}',
                finishing = '{$this->finishing}'";
        
        if($this->id > 0)
        {
            $sql = "UPDATE schedules_machines SET ".$set.", upddat = UNIX_TIMESTAMP(), updusr = {$_USER->getId()} WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else 
        {
            $sql = "INSERT INTO schedules_machines SET ".$set.", crtdat = UNIX_TIMESTAMP(), crtusr = {$_USER->getId()}";
            $r = $DB->no_result($sql);
            
            if($r)
            {
                $sql = "SELECT max(id) id FROM schedules_machines WHERE schedules_part_id = {$this->schedulePartId}";
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
        if ($this->id > 0)
        {
            $sql = "DELETE FROM schedules_machines WHERE id = {$this->id}";
            if($DB->no_result($sql))
            {
                unset($this);
                return true;
            }
        }
        return false;
    }
    
    public static function getAllForTimeFrame($start,$end)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM schedules_machines
                WHERE deadline > {$start} 
                AND deadline < {$end} 
                ORDER BY id"; 
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new ScheduleMachine($r["id"]);
            }
            return $retval;
        }
        return false;
    }

    public static function getMachineTimeForDay($day, $machineId, $filterStatus = null)
    {
        global $DB;
        $sql = "SELECT SUM(target_time) tme 
                FROM schedules_machines 
                WHERE machine_id = {$machineId} 
                    AND deadline = '{$day}'";

        if($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            return $res[0]["tme"];
        } else
            return false;
    }
    
    public static function getOpenScheduledDays($machineId = 0, $limit = 10)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT t1.deadline
                FROM schedules_machines t1,
                    schedules_parts t2,
                    schedules t3
                WHERE t1.machine_id = {$machineId}
                    AND t2.id = t1.schedules_part_id
                    AND t3.id = t2.schedule_id
                    AND t3.finished = 0
                    AND t3.status > 0
                GROUP BY t1.deadline
                LIMIT {$limit}";
        if($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            foreach($res as $r)
            {
                $retval[] = $r["deadline"];
            }
            return $retval;
        } else
            return false;
    }
    
    public static function getPartsForDay($day, $machineId = 0, $filterFinished = false)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT distinct t2.id
                FROM schedules_machines t1,
                    schedules_parts t2,
                    schedules t3
                WHERE t1.machine_id = {$machineId}
                    AND t2.id = t1.schedules_part_id
                    AND t3.id = t2.schedule_id
                    AND t3.finished = 0
                    AND t3.status > 0
                    AND t1.deadline = '{$day}'
                ORDER BY t1.priority";
        if($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            foreach($res as $r)
            {
                if($filterFinished)
                {
                    $part = new SchedulePart($r["id"]);
                    if($part->getFinished())
                        $retval[] = $part;
                }
                else
                    $retval[] = new SchedulePart($r["id"]);
            }
            return $retval;
        } else
            return false;
        
    }
    
    public static function getSmEntriesForMachineAndPart($partId, $machineId)
    {
        global $DB;
        $retval = Array();
        $sql = "SELECT id FROM schedules_machines
                WHERE schedules_part_id = {$partId}
                    AND machine_id = {$machineId}
                ORDER BY priority";
        if($DB->num_rows($sql))
        {
            foreach($DB->select($sql) as $r)
            {
                $retval[] = new ScheduleMachine($r["id"]);
            }
            return $retval;
        }
        return false;
    }
    
    public function getId()
    {
        return $this->id;
    }

    public function getSchedulePartId()
    {
        return $this->schedulePartId;
    }

    public function setSchedulePartId($schedulePartId)
    {
        $this->schedulePartId = $schedulePartId;
    }

    public function getMachineGroup()
    {
        return $this->machineGroup;
    }

    public function setMachineGroup($machineGroup)
    {
        $this->machineGroup = $machineGroup;
    }

    public function getMachine()
    {
        return $this->machine;
    }

    public function setMachine($machine)
    {
        $this->machine = $machine;
    }

    public function getTargetTime()
    {
        return $this->targetTime;
    }

    public function setTargetTime($targetTime)
    {
        $this->targetTime = $targetTime;
    }

    public function getActualTime()
    {
        return $this->actualTime;
    }

    public function setActualTime($actualTime)
    {
        $this->actualTime = $actualTime;
    }

    public function getDownTime()
    {
        return $this->downTime;
    }

    public function setDownTime($downTime)
    {
        $this->downTime = $downTime;
    }

    public function getDownTimeType()
    {
        return $this->downTimeType;
    }

    public function setDownTimeType($downTimeType)
    {
        $this->downTimeType = $downTimeType;
    }

    public function getDeadline()
    {
        return $this->deadline;
    }

    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    public function getLectorId()
    {
        return $this->lectorId;
    }

    public function setLectorId($lectorId)
    {
        $this->lectorId = $lectorId;
    }

    public function getFinished()
    {
        return $this->finished;
    }

    public function setFinished($finished)
    {
        if($finished == true || $finished == 1)
            $this->finished = 1;
        else 
            $this->finished = 0;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    public function getColors()
    {
        return $this->colors;
    }

    public function setColors($colors)
    {
        $this->colors = $colors;
    }

    public function getFinishing()
    {
        return $this->finishing;
    }

    public function setFinishing($finishing)
    {
        $this->finishing = $finishing;
    }
}
?>
