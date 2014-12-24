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
include 'libs/modules/tickets/ticket.category.class.php';
include 'libs/modules/tickets/ticket.priority.class.php';
include 'libs/modules/tickets/ticket.state.class.php';
include 'libs/modules/comment/comment.class.php';

class Ticket {
    
    private $id;
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
    
    private $associations = Array();
    
    const SOURCE_EMAIL = 1;
    const SOURCE_PHONE = 2;
    const SOURCE_OTHER = 3;
    
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
            }
            $sql = "SELECT * FROM tickets_association WHERE ticketid = {$id} ORDER BY module ASC";
            if($DB->num_rows($sql))
            {
                $retarray = Array();
                foreach($DB->select($sql) as $r){
                    $retarray[] = new TicketAssociation($r["id"]);
                }
                $this->associations = $retarray;
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
            editdate 		= {$this->editdate},
            customer 		= {$this->customer->getId()},
            customer_cp 	= {$this->customer_cp->getId()},
            assigned_user 	= {$this->assigned_user->getId()},
            assigned_group 	= {$this->assigned_group->getId()},
            state			= {$this->state->getId()},
            category		= {$this->category->getId()},
            priority		= {$this->priority->getId()},
            source		    = {$this->source}
            WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else {
            $this->number = $_USER->getClient()->createTicketnumber();
            $sql = "INSERT INTO tickets
            (title, duedate, closedate, closeuser, editdate, number, customer, 
             customer_cp, assigned_user, assigned_group, state, category, priority,
             source, crtdate, crtuser)
            VALUES
            ( '{$this->title}' , {$this->duedate}, {$this->closedate}, {$this->closeuser->getId()}, {$this->editdate}, '{$this->number}', {$this->customer->getId()},
              {$this->customer_cp->getId()}, {$this->assigned_user->getId()}, {$this->assigned_group->getId()}, {$this->state->getId()}, {$this->category->getId()}, {$this->priority->getId()},
              {$this->source}, {$now}, {$_USER->getId()})";
            $res = $DB->no_result($sql);
            if ($res) {
                $sql = "SELECT max(id) id FROM tickets WHERE title = '{$this->title}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                $this->crtdate = $now;
                $this->crtuser = $_USER;
                return true;
            } else {
                return false;
            }
        }
    }


    public function getTicketsForObject($module,$objectid)
    {
        $retval = Array();
    
        $sql = "SELECT ticketid FROM tickets_association WHERE module = {$module} AND objectid = {$objectid}";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new Ticket($r["id"]);
            }
        }
    
        return $retval;
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
                unset($this);
                return true;
            } else {
                return false;
            }
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
    
    
}


?>