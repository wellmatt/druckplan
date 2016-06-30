<?php
// ----------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
// include 'libs/modules/businesscontact/businesscontact.class.php';
// include 'libs/modules/businesscontact/contactperson.class.php';
require_once 'libs/modules/tickets/ticket.category.class.php';
require_once 'libs/modules/tickets/ticket.priority.class.php';
require_once 'libs/modules/tickets/ticket.state.class.php';
require_once 'libs/modules/tickets/ticket.source.class.php';
require_once 'libs/modules/comment/comment.class.php';
require_once 'libs/modules/notifications/notification.class.php';
require_once 'libs/modules/timer/timer.class.php';
require_once 'libs/modules/perferences/perferences.class.php';
require_once 'libs/modules/tickets/ticket.log.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';
require_once 'libs/modules/planning/planning.job.class.php';

class Ticket {
    
    private $id = 0;
    private $title;
    private $crtdate;
    private $crtuser;
    private $duedate;
    private $closedate = 0;
    private $closeuser;
    private $editdate = 0;
    private $number;
    private $customer;
    private $customer_cp;
    private $assigned_user;
    private $assigned_group;
    private $state;
    private $category;
    private $priority;
    private $source;
    private $planned_time = 0;
    private $total_time = 0;
    
    private $associations = Array();
    
    const SOURCE_EMAIL = 1;
    const SOURCE_PHONE = 2;
    const SOURCE_OTHER = 3;
    const SOURCE_JOB   = 4;
    
    function __construct($id = 0){
        global $DB;
        global $_USER;
    
        $this->crtuser	        = new User(0);
        $this->closeuser        = new User(0);
        $this->customer	        = new BusinessContact(0);
        $this->customer_cp	    = new ContactPerson(0);
        $this->assigned_user    = new User(0);
        $this->assigned_group   = new Group(0);
        $this->category         = new TicketCategory(0);
        $this->priority         = new TicketPriority(0);
        $this->state            = new TicketState(0);
        
    
        if($id>0){
            $sql = "SELECT * FROM tickets WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                $this->id 				= (int)$r["id"];
                $this->title 			= $r["title"];
                $this->crtdate		    = $r["crtdate"];
                $this->crtuser		    = new User($r["crtuser"]);
                $this->duedate		    = $r["duedate"];
                $this->closedate		= $r["closedate"];
                $this->closeuser		= new User($r["closeuser"]);
                $this->editdate		    = $r["editdate"];
                $this->number		    = $r["number"];
                $this->customer		    = new BusinessContact((int)$r["customer"]);
                $this->customer_cp	    = new ContactPerson((int)$r["customer_cp"]);
                $this->assigned_user	= new User($r["assigned_user"]);
                $this->assigned_group	= new Group($r["assigned_group"]);
                $this->state 			= new TicketState((int)$r["state"]);
                $this->category	        = new TicketCategory((int)$r["category"]);
                $this->priority			= new TicketPriority((int)$r["priority"]);
                $this->source   	    = $r["source"];
                $this->planned_time   	= (float)$r["planned_time"];
            }
            
            $sql = "SELECT SUM(stoptime-starttime) as time FROM timers WHERE module = 'Ticket' AND objectid = {$this->id} AND state = 2";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                $this->total_time = $r["time"]/60/60;
            }
        }
    }

    /**
     * Speicher-Funktion fuer Tickets
     *
     * @return boolean
     */
    function save()
    {
        global $DB;
        global $_USER;
        $now = time();
        
        if ($this->id > 0) {
            $sql = "UPDATE tickets SET
            title 			= '{$this->title}',
            duedate 		= {$this->duedate},
            closedate 		= {$this->closedate},
            closeuser 		= {$this->closeuser->getId()},
            crtuser 		= {$this->crtuser->getId()},
            editdate 		= {$this->editdate},
            customer 		= {$this->customer->getId()},
            customer_cp 	= {$this->customer_cp->getId()},
            assigned_user 	= {$this->assigned_user->getId()},
            assigned_group 	= {$this->assigned_group->getId()},
            state			= {$this->state->getId()},
            category		= {$this->category->getId()}, 
            priority		= {$this->priority->getId()}, 
            planned_time	= {$this->planned_time}, 
            source		    = {$this->source} 
            WHERE id = {$this->id}";
//             var_dump($sql);
            return $DB->no_result($sql);
        } else {
            if ($this->crtuser)
                $tmp_crtuser = $this->crtuser->getId();
            else
                $tmp_crtuser = $_USER->getId();
            $this->number = $_USER->getClient()->createTicketnumber();
            $sql = "INSERT INTO tickets
            (title, duedate, closedate, closeuser, editdate, number, customer, 
             customer_cp, assigned_user, assigned_group, state, category, priority,
             source, crtdate, crtuser, planned_time)
            VALUES
            ( '{$this->title}' , {$this->duedate}, {$this->closedate}, {$this->closeuser->getId()}, {$this->editdate}, '{$this->number}', {$this->customer->getId()},
              {$this->customer_cp->getId()}, {$this->assigned_user->getId()}, {$this->assigned_group->getId()}, {$this->state->getId()}, {$this->category->getId()}, {$this->priority->getId()},
              {$this->source}, {$now}, $tmp_crtuser, {$this->planned_time})";
            $res = $DB->no_result($sql);
//             echo $sql . "</br>";
            if ($res) {
                $sql = "SELECT max(id) id FROM tickets WHERE title = '{$this->title}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                $this->crtdate = $now;
                $this->crtuser = new User($tmp_crtuser);
                return true;
            } else {
                return false;
            }
        }
    }
    
    /**
     * Loeschfunktion fuer Kommentare.
     * Das Kommentare wird nicht entgueltig geloescht, der Status wird auf 0 gesetzt
     *
     * @return boolean
     */
    public function delete()
    {
        global $DB;
        if ($this->id > 0) {
            $sql = "UPDATE tickets SET 
                    state = 1 
    				WHERE id = {$this->id}";
            if ($DB->no_result($sql)) {
                Notification::removeForObject("Ticket", $this->getId());
                unset($this);
                return true;
            } else {
                return false;
            }
        }
    }
    
    public static function getUserSpareTime(User $user)
    {
        global $DB;
        $userid = $user->getId();
        $now = time();
        $usertickets = Ticket::getAllTickets(" WHERE assigned_user = {$userid} AND duedate > {$now}");
        $totaltime = $user->getW_month();
        foreach ($usertickets as $ticket)
        {
            if ($ticket->getTotal_time()>0)
            {
                $totaltime -= $ticket->getTotal_time();
            } else {
                $totaltime -= $ticket->getPlanned_time();
            }
        }
        return $totaltime;
    }
    
    static function getDueTicketsWithinTimeFrame($start, $end, User $user, $ticketstates = null) {
        global $DB;
        $retval = Array();
    
        $foruser = $user;
        $forname = $foruser->getFirstname() . " " . $foruser->getLastname();
        $forgroups = $foruser->getGroups();
        if (count($forgroups) > 0){
            $groupsql = " OR assigned IN (";
            foreach ($forgroups as $ugroup){
                $groupsql .= "'".$ugroup->getName() . "',";
            }
            $groupsql = substr($groupsql, 0, strlen($groupsql)-1);
            $groupsql .= ") ";
        }
        $sWhere .= " WHERE (assigned = '" . $forname . "' " . $groupsql . " OR crtuser = '" . $forname . "') ";
        if (isset($ticketstates) && $ticketstates != null && $ticketstates != "")
        {
            $tsids = implode(",", $ticketstates);
            $sWhere .= " AND tsid in ({$tsids}) ";
        
            $sql = "SELECT id, assigned, crtuser FROM (SELECT
            tickets.id, tickets_categories.title as category, tickets.crtdate, tickets.duedate, tickets.title, tickets_states.title as state, tickets_states.id as tsid,
            businesscontact.name1 as customer, businesscontact.id as bcid, tickets_priorities.value as priority, tickets_priorities.title as priority_title,
            IF (`user`.login != '', CONCAT(`user`.user_firstname,' ',`user`.user_lastname), groups.group_name) assigned,
            CONCAT(user2.user_firstname,' ',user2.user_lastname) AS crtuser
            FROM tickets
            LEFT JOIN businesscontact ON businesscontact.id = tickets.customer
            LEFT JOIN tickets_states ON tickets_states.id = tickets.state
            LEFT JOIN tickets_priorities ON tickets_priorities.id = tickets.priority
            LEFT JOIN tickets_categories ON tickets_categories.id = tickets.category
            LEFT JOIN `user` ON `user`.id = tickets.assigned_user
            LEFT JOIN groups ON groups.id = tickets.assigned_group
            LEFT JOIN `user` AS user2 ON user2.id = tickets.crtuser
            HAVING duedate >= {$start} AND duedate <= {$end}
            ) tickets
            $sWhere 
            ORDER BY id ASC";
            
            if($DB->num_rows($sql)) {
                foreach($DB->select($sql) as $r){
                    $retval[] = new Ticket((int)$r["id"]);
                }
            }
        }
        return $retval;
    }
    
    public static function getAllTickets($filter = '')
    {
        global $DB;
        global $_USER;
        $retval = Array();
    
        $sql = "SELECT id FROM tickets {$filter}";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new Ticket($r["id"]);
            }
        }
    
        return $retval;
    }
    
    public static function getAllTicketsFlatAjax($filter = '')
    {
        global $DB;
        global $_USER;
        $retval = Array();
    
        $sql = "SELECT
                tickets.id,
                tickets.title,
                tickets.number,
                businesscontact.name1,
                businesscontact.matchcode
                FROM
                tickets
                INNER JOIN businesscontact ON tickets.customer = businesscontact.id {$filter}";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = Array("label" => $r["name1"] . ": " . $r["number"] . " - " . $r["title"], "value" => $r["id"]);
            }
        }
    
        return $retval;
    }
    
    public static function getAllTicketsCount($filter = '')
    {
        global $DB;
        global $_USER;
        $retval = 0;
    
        $sql = "SELECT count(id) FROM tickets {$filter}";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval = (int)$r["count(id)"];
            }
        }
    
        return $retval;
    }

    /**
     * @return float
     */
    public function getTimeFromArticles()
    {
        $all_comments = Comment::getCommentsForObjectSummary(get_class($this),$this->id);
        $total = 0.0;
        foreach ($all_comments as $comment){
            if ($comment->getState() > 0 && count($comment->getArticles()) > 0){
                foreach ($comment->getArticles() as $c_article){
                    if ($c_article->getState() > 0)
                    {
                        if ($c_article->getArticle()->getIsWorkHourArt())
                            $total += $c_article->getAmount();
                    }
                }
            }
        }
        return $total;
    }

    public static function StatisticsTicketStates($from,$to)
    {
        global $DB;
        $retval = false;
        $where = "";
        if ($from & $to){
            $where .= " WHERE ";
            $where .= " tickets.crtdate <= {$to}";
            $where .= " AND ";
            $where .= " tickets.crtdate >= {$from}";
        }
        $sql = "SELECT
                tickets_states.title,
                count(tickets.id) as amount
                FROM
                tickets_states
                INNER JOIN tickets ON tickets_states.id = tickets.state
                {$where}
                GROUP BY
                tickets_states.id";

        if($DB->num_rows($sql)){
            $retval = Array();
            foreach($DB->select($sql) as $r){
                $retval[] = Array('label'=>$r["title"],'data'=>$r["amount"]);
            }
        }
        return $retval;
    }

    public static function StatisticsTicketCategories($from,$to)
    {
        global $DB;
        $retval = false;
        $where = "";
        if ($from & $to){
            $where .= " WHERE ";
            $where .= " tickets.crtdate <= {$to}";
            $where .= " AND ";
            $where .= " tickets.crtdate >= {$from}";
        }
        $sql = "SELECT
                tickets_categories.title,
                count(tickets.id) as amount
                FROM
                tickets_categories
                INNER JOIN tickets ON tickets_categories.id = tickets.category
                {$where}
                GROUP BY
                tickets_categories.id";

        if($DB->num_rows($sql)){
            $retval = Array();
            foreach($DB->select($sql) as $r){
                $retval[] = Array('label'=>$r["title"],'data'=>$r["amount"]);
            }
        }
        return $retval;
    }

    public static function StatisticsTicketWorkload($from,$to)
    {
        global $DB;
        $retval = false;
        $where = "";
        if ($from & $to){
            $where .= " AND ";
            $where .= " tickets.crtdate <= {$to}";
            $where .= " AND ";
            $where .= " tickets.crtdate >= {$from}";
        }
        $sql = "SELECT
                `user`.login,
                SUM(comments_article.amount) as total
                FROM
                tickets
                INNER JOIN comments ON comments.objectid = tickets.id
                INNER JOIN comments_article ON comments_article.comment_id = comments.id
                INNER JOIN article ON comments_article.articleid = article.id
                INNER JOIN `user` ON comments.crtuser = `user`.id
                WHERE
                comments.state = 1 AND
                comments_article.state = 1 AND
                tickets.state > 1 AND
                article.`status` > 0
                {$where}
                GROUP BY `user`.id";
//        echo $sql;
        if($DB->num_rows($sql)){
            $retval = Array();
            foreach($DB->select($sql) as $r){
                $retval[] = Array('label'=>$r["login"],'data'=>$r["total"]);
            }
        }
        return $retval;
    }

    public static function StatisticsTicketWorkloadUser(User $user,$from,$to)
    {
        global $DB;
        $retval = false;
        $where = "";
        if ($from & $to){
            $where .= " AND ";
            $where .= " comments.crtdate <= {$to}";
            $where .= " AND ";
            $where .= " comments.crtdate >= {$from}";
        }
        $sql = "SELECT
                tickets.id,
                tickets.title,
                businesscontact.name1 as bc,
                tickets.number,
                tickets.duedate,
                tickets.planned_time,
                SUM(comments_article.amount) as curr_time
                FROM
                tickets
                INNER JOIN comments ON comments.objectid = tickets.id
                INNER JOIN comments_article ON comments_article.comment_id = comments.id
                INNER JOIN article ON comments_article.articleid = article.id
                INNER JOIN businesscontact ON tickets.customer = businesscontact.id
                WHERE
                article.isworkhourart = 1 AND
                comments.state = 1 AND
                article.`status` = 1 AND
                tickets.state > 1 AND
                comments.module = 'Ticket' AND
                comments.crtuser = {$user->getId()}
                {$where}
                GROUP BY
                tickets.id
                ";
        if($DB->num_rows($sql)){
            $retval = Array();
            foreach($DB->select($sql) as $r){
                $retval[] = $r;
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
     * @return the $title
     */
    public function getTitle()
    {
        return $this->title;
    }

	/**
     * @return the $crtdate
     */
    public function getCrtdate()
    {
        return $this->crtdate;
    }

	/**
     * @return the $crtuser
     */
    public function getCrtuser()
    {
        return $this->crtuser;
    }

	/**
     * @return the $duedate
     */
    public function getDuedate()
    {
        return $this->duedate;
    }

	/**
     * @return the $closedate
     */
    public function getClosedate()
    {
        return $this->closedate;
    }

	/**
     * @return the $closeuser
     */
    public function getCloseuser()
    {
        return $this->closeuser;
    }

	/**
     * @return the $editdate
     */
    public function getEditdate()
    {
        return $this->editdate;
    }

	/**
     * @return the $number
     */
    public function getNumber()
    {
        return $this->number;
    }

	/**
     * @return the $customer
     */
    public function getCustomer()
    {
        return $this->customer;
    }

	/**
     * @return the $customer_cp
     */
    public function getCustomer_cp()
    {
        return $this->customer_cp;
    }

	/**
     * @return the $assigned_user
     */
    public function getAssigned_user()
    {
        return $this->assigned_user;
    }

	/**
     * @return the $assigned_group
     */
    public function getAssigned_group()
    {
        return $this->assigned_group;
    }

	/**
     * @return the $state
     */
    public function getState()
    {
        return $this->state;
    }

	/**
     * @return the $category
     */
    public function getCategory()
    {
        return $this->category;
    }

	/**
     * @return the $priority
     */
    public function getPriority()
    {
        return $this->priority;
    }

	/**
     * @return the $source
     */
    public function getSource()
    {
        return $this->source;
    }

	/**
     * @param number $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

	/**
     * @param field_type $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

	/**
     * @param Ambigous <number, unknown> $crtdate
     */
    public function setCrtdate($crtdate)
    {
        $this->crtdate = $crtdate;
    }

	/**
     * @param Ambigous <User, boolean, string> $crtuser
     */
    public function setCrtuser($crtuser)
    {
        $this->crtuser = $crtuser;
    }

	/**
     * @param field_type $duedate
     */
    public function setDuedate($duedate)
    {
        $this->duedate = $duedate;
    }

	/**
     * @param number $closedate
     */
    public function setClosedate($closedate)
    {
        $this->closedate = $closedate;
    }

	/**
     * @param User $closeuser
     */
    public function setCloseuser($closeuser)
    {
        $this->closeuser = $closeuser;
    }

	/**
     * @param number $editdate
     */
    public function setEditdate($editdate)
    {
        $this->editdate = $editdate;
    }

	/**
     * @param field_type $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

	/**
     * @param BusinessContact $customer
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;
    }

	/**
     * @param ContactPerson $customer_cp
     */
    public function setCustomer_cp($customer_cp)
    {
        $this->customer_cp = $customer_cp;
    }

	/**
     * @param User $assigned_user
     */
    public function setAssigned_user($assigned_user)
    {
        $this->assigned_user = $assigned_user;
    }

	/**
     * @param Group $assigned_group
     */
    public function setAssigned_group($assigned_group)
    {
        $this->assigned_group = $assigned_group;
    }

	/**
     * @param number $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

	/**
     * @param TicketCategory $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

	/**
     * @param TicketPriority $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

	/**
     * @param field_type $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }
    
	/**
     * @return the $tourmarker
     */
    public function getTourmarker()
    {
        return $this->customer->getTourmarker();
    }
    
	/**
     * @return the $planned_time
     */
    public function getPlanned_time()
    {
        return $this->planned_time;
    }

	/**
     * @param number $planned_time
     */
    public function setPlanned_time($planned_time)
    {
        $this->planned_time = $planned_time;
    }
    
	/**
     * @return the $total_time
     */
    public function getTotal_time()
    {
        return $this->total_time;
    }

	/**
     * @param number $total_time
     */
    public function setTotal_time($total_time)
    {
        $this->total_time = $total_time;
    }
    
}


?>