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
    private $opos;
    private $subobject;
    private $assigned_user;
    private $assigned_group;
    private $ticket;
    private $start = 0;
    private $tplanned = 0;
    private $tactual = 0;
    private $state = 1;
    private $artmach;
    
    const TYPE_V = 1;
    const TYPE_K = 2;

    function __construct($id = 0)
    {
        global $DB;

        $this->assigned_user = new User();
        $this->assigned_group = new Group();
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
                $this->object = new CollectiveInvoice((int)$r["object"]);
                $this->opos = new Orderposition((int)$r["opos"]);
                if ($this->type == PlanningJob::TYPE_V)
                {
                    $this->subobject = new Article((int)$r["subobject"]);
                    $this->artmach = new Article((int)$r["artmach"]);
                }
                elseif ($this->type == PlanningJob::TYPE_K)
                {
                    $this->subobject = new Order((int)$r["subobject"]);
                    $this->artmach = new Machine((int)$r["artmach"]);
                }
                $this->assigned_user = new User((int)$r["assigned_user"]);
                $this->assigned_group = new Group((int)$r["assigned_group"]);
                $this->ticket = new Ticket((int)$r["ticket"]);
                $this->start = $r["start"];
                $this->tplanned = $r["tplanned"];
                $this->tactual = $r["tactual"];
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
            $comm = $this->opos->getComment();
        }
        elseif ($this->type == PlanningJob::TYPE_K)
        {
            $title = "PL-Job - " .$this->object->getNumber(). " - " .$this->artmach->getName();
            $comm = Order::generateSummary($this->object->getId());
        }
        $ticket->setCustomer($this->object->getCustomer());
        $ticket->setCustomer_cp($this->object->getCustContactperson());
        $ticket->setTitle($title);
        $ticket->setDuedate($this->start);
        if ($this->assigned_user->getId()>0)
            $ticket->setAssigned_user($this->assigned_user);
        else
            $ticket->setAssigned_group($this->assigned_group);
        $ticket->setCategory(new TicketCategory(2));
        $ticket->setState(new TicketState(2));
        $ticket->setPriority(new TicketPriority(1));
        $ticket->setSource(Ticket::SOURCE_JOB); // TODO: hardcoded
        $ticket->setPlanned_time($this->tplanned);
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
            
            $asso = new Association();
            $asso->setCrtdate(time());
            $asso->setCrtuser($_USER);
            $asso->setModule1("Ticket");
            $asso->setModule2("CollectiveInvoice");
            $asso->setObjectid1($ticket->getId());
            $asso->setObjectid2($this->object->getId());
            $asso->save();
        }
    }

    function save()
    {
        global $_USER;
        global $DB;
        $set = "object = {$this->object->getId()},
                type = {$this->type},
                opos = {$this->opos->getId()},
                subobject = {$this->subobject->getId()},
                assigned_user = {$this->assigned_user->getId()},
                assigned_group = {$this->assigned_group->getId()},
                ticket = {$this->ticket->getId()},
                `start` = {$this->start},
                tplanned = {$this->tplanned},
                tactual = {$this->tactual},
                artmach = {$this->artmach->getId()},
                state = {$this->state}";
        
        if ($this->id > 0) {
            $sql = "UPDATE planning_jobs SET " . $set . " WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO planning_jobs SET " . $set . " ";
//             echo $sql."</br>";
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
    
    public static function getJobsForObjectAndOpos($object,$opos)
    {
        global $DB;
        $retval = Array();
        
        $sql = "SELECT id FROM planning_jobs WHERE state > 0 AND object = {$object} AND opos = {$opos}";
        
        if($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new PlanningJob($r["id"]);
            }
        }
        return $retval;
    }
    
    public static function getJobsForObjectAndOposAndArtmach($object,$type,$opos,$artmach)
    {
        global $DB;
        $retval = Array();
        
        $sql = "SELECT id FROM planning_jobs WHERE state > 0 AND object = {$object} AND type = {$type} AND opos = {$opos} AND artmach = {$artmach}";
        
        if($DB->num_rows($sql))
        {
            foreach ($DB->select($sql) as $r)
            {
                $retval[] = new PlanningJob($r["id"]);
            }
        }
        return $retval;
    }
    
    public function getTitle()
    {
        if ($this->type == PlanningJob::TYPE_V)
        {
            return $this->artmach->getTitle();
        }
        elseif ($this->type == PlanningJob::TYPE_K)
        {
            return $this->artmach->getName();
        }
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
     * @return the $start
     */
    public function getStart()
    {
        return $this->start;
    }

	/**
     * @return the $tplanned
     */
    public function getTplanned()
    {
        return $this->tplanned;
    }

	/**
     * @return the $tactual
     */
    public function getTactual()
    {
        return $this->tactual;
    }

	/**
     * @return the $state
     */
    public function getState()
    {
        return $this->state;
    }

	/**
     * @return the $artmach
     */
    public function getArtmach()
    {
        return $this->artmach;
    }

	/**
     * @param CollectiveInvoice $object
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
     * @param Ambigous <Order, Article> $subobject
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
     * @param number $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

	/**
     * @param number $tplanned
     */
    public function setTplanned($tplanned)
    {
        $this->tplanned = $tplanned;
    }

	/**
     * @param number $tactual
     */
    public function setTactual($tactual)
    {
        $this->tactual = $tactual;
    }

	/**
     * @param number $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

	/**
     * @param Ambigous <Article, Machine> $artmach
     */
    public function setArtmach($artmach)
    {
        $this->artmach = $artmach;
    }
    
	/**
     * @return the $opos
     */
    public function getOpos()
    {
        return $this->opos;
    }

	/**
     * @param Orderposition $opos
     */
    public function setOpos($opos)
    {
        $this->opos = $opos;
    }
    
	/**
     * @return the $assigned_group
     */
    public function getAssigned_group()
    {
        return $this->assigned_group;
    }

	/**
     * @param Group $assigned_group
     */
    public function setAssigned_group($assigned_group)
    {
        $this->assigned_group = $assigned_group;
    }
}
?>