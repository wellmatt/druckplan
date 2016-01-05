<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       05.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
class Urlaub {
    const STATE_UNSEEN = 1;
    const STATE_WAIT = 2;
    const STATE_APPROVED = 3;
    const STATE_ALL = 0;
    
    const TYPE_URLAUB = 1;
    const TYPE_UEBERSTUNDEN = 2;
    const TYPE_KRANKHEIT = 3;
    const TYPE_SONSTIGES = 4;
    
    private $reason;
    private $notes;
    private $useddays;
    private $begin;
    private $end;
    private $state;
    private $user;
    private $id;
    
    function __construct($id = 0)
    {
        global $DB;
        if ($id > 0)
        {
            $sql = "SELECT * FROM vacation WHERE id = {$id}";
            if ($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $this->reason = $r[0]["reason"];
                $this->notes = $r[0]["notes"];
                $this->useddays = $r[0]["useddays"];
                $this->begin = $r[0]["begin"];
                $this->end = $r[0]["end"];
                $this->state = $r[0]["state"];
                $this->id = $r[0]["id"];
                $this->user = new User($r[0]["user_id"], false);
            }
        }

    }
    
    static function getAllVacations($state = self::STATE_ALL, $user = null)
    {
        global $DB;
        $retval = Array();
        
        $sql = "SELECT id FROM vacation WHERE 1 = 1 ";
        if($state > 0)
            $sql .= " AND state = {$state}";
        if($user)
            $sql .= " AND user_id = {$user->getId()}";

        if ($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $vacs)
            {
                $retval[] = new Urlaub($vacs["id"]);
            }
        } 
        return $retval;
                
    }
    
    static function getAllVacationsForUserInTimeframe($start, $end, $user = null, $state = self::STATE_APPROVED)
    {
        
        global $DB;
        $retval = Array();
        
		$start = explode("-",$start);
		$end = explode("-",$end);

        $start = mktime(0,0,0, $start[1], $start[2], $start[0]);
        $end = mktime(0,0,0, $end[1], $end[2], $end[0])+60*60*24;
        
        $sql = "SELECT id FROM vacation WHERE 1 = 1 ";
        if($state > 0)
            $sql .= " AND state = {$state}";
        if($user)
            $sql .= " AND user_id = {$user->getId()}";
        
        $sql .= " AND (begin >= {$start} AND end < {$end} OR
                 begin >= {$start} AND begin < {$end} OR
                 end >= {$start} AND end < {$end} OR
                 begin < {$start} AND end >= {$end})";
//         echo $sql . "</br>";

        if ($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $vacs)
            {
                $retval[] = new Urlaub($vacs["id"]);
            }
        } 
        return $retval;
    }
    
    static function isVacationOnDay($user, $day, $month, $year)
    {
        global $DB;
        $daystep = 60*60*24;
        $today = mktime(0,0,0, $month, $day, $year);
        $tomorrow = mktime(0,0,0, $month, $day, $year)+ $daystep;
        
        
        $sql = "SELECT id FROM vacation
                WHERE
                    state > 0 AND
                    user_id = {$user->getId()} AND
                    (begin >= {$today} AND begin < {$tomorrow} OR
                     end >= {$today} AND end < {$tomorrow} OR
                     begin < {$today} AND end > {$today})
                    ";
        if($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            return $res[0]["id"];
        }
        
        return false;
    }
    
    static function getSumVacationDays($year, $user)
    {
        global $DB;
        $days = 0;
        $year_start = mktime(0,0,0,1,1,$year);
        $year_end = mktime(23,59,59,12,31,$year);
        
        $sql = "SELECT useddays FROM vacation 
                WHERE 
                    user_id = {$user->getId()} AND
                    state = ".self::STATE_APPROVED." AND
                    begin >= {$year_start} AND
                    end <= {$year_end}";
        if ($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $days += $r["useddays"];
            }
        }
        return $days;
    }

    public function getReason()
    {
        return $this->reason;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function getUseddays()
    {
        return $this->useddays;
    }

    public function getBegin()
    {
        return $this->begin;
    }

    public function getEnd()
    {
        return $this->end;
    }

    public function getState()
    {
        return $this->state;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getUser()
    {
        return $this->user;
    }

    public function setReason($reason)
    {
        $this->reason = $reason;
    }

    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    public function setUseddays($useddays)
    {
        $this->useddays = $useddays;
    }

    public function setBegin($begin)
    {
        $this->begin = $begin;
    }

    public function setEnd($end)
    {
        $this->end = $end;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }
    
    public function save()
    {
        global $DB;
        if ($this->id > 0)
        {
            $sql = "UPDATE vacation SET
                        user_id = {$this->user->getId()},
                        reason = {$this->reason}, 
                        notes = '{$this->notes}',
                        useddays = {$this->useddays},
                        begin = {$this->begin}, 
                        end = {$this->end},
                        state = {$this->state}
                    WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else
        {
            $sql = "INSERT INTO vacation
                        (user_id, reason, notes, useddays, begin, end, state)
                    VALUES
                        ({$this->user->getId()}, {$this->reason}, '{$this->notes}',
                         {$this->useddays}, {$this->begin}, {$this->end}, 1)";
            $res = $DB->no_result($sql);
            
            if($res)
            {
                $sql = "SELECT max(id) id FROM vacation WHERE user_id = {$this->user->getId()}";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else
                return false;
        }
    }
    
    public function delete()
    {
        global $DB;
        if($this->id > 0)
        {
            $sql = "DELETE FROM vacation WHERE id = {$this->id}";
            $res = $DB->no_result($sql);
            
            if($res)
            {
                unset($this);
                return true;
            } else
                return false;
        }
    }
}
?>