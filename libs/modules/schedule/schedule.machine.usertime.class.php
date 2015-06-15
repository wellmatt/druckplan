<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       29.04.2015
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/schedule/schedule.machine.class.php';

class ScheduleMachineUsertime {
    
    private $id;
    private $sched_machine;
    private $user;
    private $ticket;
    private $ticket_time = 0;
    
    function __construct($id = 0)
    {
        global $DB;
        
        $this->sched_machine = new ScheduleMachine();
        $this->user = new User();
        $this->ticket = new Ticket();
        
        if($id > 0)
        {
            $sql = "SELECT * FROM schedules_machines_usertime WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                
                $this->id = $r["id"];
                $this->sched_machine = new ScheduleMachine((int)$r["part"]);
                $this->user = new User((int)$r["user"]);
                $this->ticket = new Ticket((int)$r["ticket"]);
                $this->ticket_time = $r["ticket_time"];
                
                if ($this->ticket_time == 0){
                    $sql = "SELECT SUM(timers.stoptime-timers.starttime) as seconds 
                            FROM 
                            timers 
                            INNER JOIN tickets ON tickets.id = timers.id
                            WHERE timers.module = 'Ticket' AND tickets.state = 3 AND tickets.id = {$this->ticket->getId()}";
                    if ($DB->num_rows($sql))
                    {
                        $r = $DB->select($sql);
                        $r = $r[0];
    
                        $this->ticket_time = $r["seconds"];
                    }
                }
            }
        }
    }
    
    static function getScheduledUsers(ScheduleMachine $sm)
    {
        global $DB;
        $retval = Array();
        
        $sql = "SELECT user FROM schedules_parts_usertime WHERE sched_machine = {$sm->getId()}";
        
        if($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new User($r["user"]);
            }
        }
        return $retval;
    }
    
    function createMyTicket()
    {
        $ticket = new Ticket();
        $schedule_part = new SchedulePart($this->sched_machine->getSchedulePartId());
        $schedule = new Schedule($schedule_part->getScheduleId()); 
        $title = "".$schedule->getNumber().": ".$this->sched_machine->getMachine()->getName();
        
        $ticket->setTitle($title);
        $ticket->setDuedate($this->sched_machine->getDeadline());
        $ticket->setCustomer($schedule->getCustomer());
        $ticket->setCustomer_cp($schedule->getCustomer_cp());
        $ticket->setAssigned_user($this->user);
        $ticket->setCategory(new TicketCategory(4));
        $ticket->setState(new TicketState(2));
        $ticket->setPriority(new TicketPriority(9));
        $ticket->setSource(Ticket::SOURCE_JOB);
        $save_ok = $ticket->save();
        if ($save_ok)
            $this->ticket = $ticket;
    }
    
    static function getAllSchedulePartsUsertimes($filter)
    {
        global $DB;
        $retval = Array();
        
        $sql = "SELECT * FROM schedules_parts_usertime {$filter}";
        
        if($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new ScheduleMachineUsertime($r["id"]);
            }
        }
        return $retval;
    }
    
    function save()
    {
        global $_USER;
        global $DB;
        $set = "sched_machine = {$this->sched_machine->getId()},
                user = {$this->user->getId()},
                ticket = {$this->ticket->getId()},
                ticket_time = {$this->ticket_time}";
        
        if($this->id > 0)
        {
            $sql = "UPDATE schedules_machines_usertime SET ".$set." WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else
        {
            $sql = "INSERT INTO schedules_machines_usertime SET ".$set." ";
            $res = $DB->no_result($sql);
            
            if($res)
            {
                $sql = "SELECT max(id) id FROM schedules_machines_usertime WHERE id = {$this->id}";
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
            $sql = "DELETE FROM schedules_machines_usertime WHERE id = {$this->id}";
            $r = $DB->no_result($sql);
            if($r)
            {
                unset($this);
                return true;
            }
        }
        return false;
    }
    
	/**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

	/**
     * @return the $part
     */
    public function getPart()
    {
        return $this->part;
    }

	/**
     * @return the $user
     */
    public function getUser()
    {
        return $this->user;
    }

	/**
     * @return the $ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }

	/**
     * @return the $ticket_time
     */
    public function getTicket_time()
    {
        return $this->ticket_time;
    }

	/**
     * @param SchedulePart $part
     */
    public function setPart($part)
    {
        $this->part = $part;
    }

	/**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

	/**
     * @param Ticket $ticket
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;
    }

	/**
     * @param number $ticket_time
     */
    public function setTicket_time($ticket_time)
    {
        $this->ticket_time = $ticket_time;
    }

    
}
?>