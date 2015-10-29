<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       29.04.2015
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';
require_once 'libs/modules/collectiveinvoice/orderposition.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/machines/machine.class.php';

class PlanningJob {

    private $id;
    private $object;
    private $type;
    private $subobject;
    private $assigned_user;
    private $ticket;
    private $start;
    private $end;
    private $state = 1;
    private $artmach;
    
    const TYPE_V = 1;
    const TYPE_K = 2;

    function __construct($id = 0)
    {
        global $DB;

        $this->assigned_user = new User();
        $this->ticket = new Ticket();

        if($id > 0)
        {
            $sql = "SELECT * FROM planning_jobs WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];

                $this->id = $r["id"];
                $this->type = $r["type"];
                if ($this->type == PlanningJob::TYPE_V)
                {
                    $this->object = new CollectiveInvoice((int)$r["object"]);
                    $this->subobject = new Orderposition((int)$r["subobject"]);
                    $this->artmach = new Article((int)$r["artmach"]);
                }
                elseif ($this->type == PlanningJob::TYPE_K)
                {
                    $this->object = new Order((int)$r["object"]);
                    $this->subobject = new Machineentry((int)$r["subobject"]);
                    $this->artmach = new Machine((int)$r["artmach"]);
                }
                $this->assigned_user = new User((int)$r["assigned_user"]);
                $this->ticket = new Ticket((int)$r["ticket"]);
                $this->start = $r["start"];
                $this->end = $r["end"];
                $this->state = $r["state"];

            }
        }
    }

    static function getAllJobs($filter = null)
    {
        global $DB;
        $retval = Array();
    
        $sql = "SELECT id FROM planning_jobs WHERE state > 0 {$filter}";
    
        if($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new PlanningJob($r["id"]);
            }
        }
        return $retval;
    }

    static function getUniqueArtmach()
    {
        global $DB;
        $machines = Array();
        $articles = Array();
    
        $sql = "SELECT DISTINCT artmach, type FROM planning_jobs WHERE state = 1";
    
        if($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                if ($r["type"] == PlanningJob::TYPE_K)
                {
                    $machines[] = new Machine($r["artmach"]);
                } else {
                    $articles[] = new Article($r["artmach"]);
                }
            }
        }
        $retval = Array('machines'=>$machines,'articles'=>$articles);
        return $retval;
    }

    function createMyTicket()
    {
        global $_USER;
        $ticket = new Ticket();
        if ($this->type == PlanningJob::TYPE_V)
        {
            $title = "PL-Job - " .$this->object->getNumber(). " - " .$this->artmach->getTitle();
            $ticket->setCustomer($this->object->getCustomer());
            $ticket->setCustomer_cp($this->object->getCustContactperson());
            $comm = "";
        }
        elseif ($this->type == PlanningJob::TYPE_K)
        {
            $title = "PL-Job - " .$this->object->getNumber(). " - " .$this->artmach->getName();
            $ticket->setCustomer($this->object->getCustomer());
            $ticket->setCustomer_cp($this->object->getCustContactperson());
            $comm = Order::generateSummary($this->object->getId());
        }
        $ticket->setTitle($title);
        $ticket->setDuedate($this->start);
        $ticket->setAssigned_user($this->assigned_user);
        $ticket->setCategory(new TicketCategory(2));
        $ticket->setState(new TicketState(2));
        $ticket->setPriority(new TicketPriority(1));
        $ticket->setSource(Ticket::SOURCE_JOB); // TODO: hardcoded
        $ticket->setPlanned_time(($this->end - $this->start)/60/60);
        $ticket->setCrtuser($_USER);
        $save_ok = $ticket->save();
        if ($save_ok)
        {
            $this->ticket = $ticket;
            $comment = new Comment();
            $comment->setCrtuser($_USER);
            $comment->setCrtdate(time());
            $comment->setModule("Ticket");
            $comment->setObjectid($ticket->getId());
            $comment->setTitle("aus Planung generiert");
            $comment->setVisability(Comment::VISABILITY_INTERNAL);
            $comment->setComment($comm);
            $comment->save();
        }
    }

    function save()
    {
        global $_USER;
        global $DB;
        $set = "object = {$this->object->getId()},
                type = {$this->type},
                subobject = {$this->subobject->getId()},
                assigned_user = {$this->assigned_user->getId()},
                ticket = {$this->ticket->getId()},
                start = {$this->start},
                end = {$this->end},
                artmach = {$this->artmach->getId()},
                state = {$this->state}";
        
        if ($this->id > 0) {
            $sql = "UPDATE planning_jobs SET " . $set . " WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO planning_jobs SET " . $set . " ";
            $res = $DB->no_result($sql);
            
            if ($res) {
                $sql = "SELECT max(id) id FROM planning_jobs WHERE id = {$this->id}";
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
        if ($this->id > 0) {
            $sql = "UPDATE planning_jobs SET state = 0 WHERE id = {$this->id}";
            $r = $DB->no_result($sql);
            if ($r) {
                unset($this);
                return true;
            }
        }
        return false;
    }
    
    public function getTitle()
    {
        if ($this->type == PlanningJob::TYPE_V)
        {
            $art = new Article($this->subobject->getObjectid());
            return $art->getTitle();
        }
        elseif ($this->type == PlanningJob::TYPE_K)
        {
            return $this->subobject->getMachine()->getName();
        }
    }
    
    public function getPlannedTimeOrig()
    {
        if ($this->type == PlanningJob::TYPE_V)
        {
            return $this->subobject->getQuantity();
        }
        elseif ($this->type == PlanningJob::TYPE_K)
        {
            return $this->subobject->getTime()/60;
        }
    }
    
    public function getPlannedTime()
    {
        return ($this->end-$this->start)/60/60;
    }
    
    public function getTime()
    {
        return $this->ticket->getTotal_time();
    }
    
	/**
     * @return the $id
     */
    public function getId()
    {
        return $this->id;
    }

	/**
     * @return the $object
     */
    public function getObject()
    {
        return $this->object;
    }

	/**
     * @return the $type
     */
    public function getType()
    {
        return $this->type;
    }

	/**
     * @return the $subobject
     */
    public function getSubobject()
    {
        return $this->subobject;
    }

	/**
     * @return the $assigned_user
     */
    public function getAssigned_user()
    {
        return $this->assigned_user;
    }

	/**
     * @return the $ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }

	/**
     * @return the $state
     */
    public function getState()
    {
        return $this->state;
    }

	/**
     * @param Ambigous <CollectiveInvoice, Order> $object
     */
    public function setObject($object)
    {
        $this->object = $object;
    }

	/**
     * @param field_type $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

	/**
     * @param Ambigous <Machineentry, Orderposition> $subobject
     */
    public function setSubobject($subobject)
    {
        $this->subobject = $subobject;
    }

	/**
     * @param User $assigned_user
     */
    public function setAssigned_user($assigned_user)
    {
        $this->assigned_user = $assigned_user;
    }

	/**
     * @param Ticket $ticket
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;
    }

	/**
     * @param number $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }
    
	/**
     * @return the $start
     */
    public function getStart()
    {
        return $this->start;
    }

	/**
     * @return the $end
     */
    public function getEnd()
    {
        return $this->end;
    }

	/**
     * @return the $artmach
     */
    public function getArtmach()
    {
        return $this->artmach;
    }

	/**
     * @param field_type $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

	/**
     * @param field_type $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

	/**
     * @param Ambigous <Machine, Article> $artmach
     */
    public function setArtmach($artmach)
    {
        $this->artmach = $artmach;
    }
}
?>