<?php
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

class TicketLog {
    
    private $id;
    private $ticket;
    private $crtusr;
    private $date;
    private $entry;
    
    function __construct($id = 0){
        global $DB;
        
        $this->ticket = new Ticket();
        $this->crtusr = new User();
    
        if($id>0){
            $sql = "SELECT * FROM tickets_logs WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                $this->id 				= (int)$r["id"];
                $this->ticket 			= new Ticket((int)$r["ticket"]);
                $this->crtusr 			= new User((int)$r["crtusr"]);
                $this->date		        = $r["date"];
                $this->entry		    = $r["entry"];
    
            }
        }
    }

    /**
     * Speicher-Funktion fuer Ticket Logs
     *
     * @return boolean
     */
    function save()
    {
        global $DB;
        global $_USER;
        
        if ($this->id == 0) {
            $sql = "INSERT INTO tickets_logs
            (ticket, crtusr, date, entry)
            VALUES
            ( '{$this->ticket->getId()}', {$_USER->getId()}, {$this->date}, '{$this->entry}' )";
            $res = $DB->no_result($sql);
            if ($res) {
                $sql = "SELECT max(id) id FROM tickets_logs WHERE ticket = {$this->ticket->getId()} AND date = {$this->date}";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else {
                return false;
            }
        }
    }
    
    public static function getAllForTicket(Ticket $ticket)
    {
        global $DB;
        $retval = Array();
    
        $sql = "SELECT id FROM tickets_logs WHERE ticket = {$ticket->getId()} ORDER BY date DESC";
//         echo $sql;
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new TicketLog($r["id"]);
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
     * @return the $ticket
     */
    public function getTicket()
    {
        return $this->ticket;
    }

	/**
     * @return the $crtusr
     */
    public function getCrtusr()
    {
        return $this->crtusr;
    }

	/**
     * @return the $date
     */
    public function getDate()
    {
        return $this->date;
    }

	/**
     * @return the $entry
     */
    public function getEntry()
    {
        return $this->entry;
    }

	/**
     * @param Ticket $ticket
     */
    public function setTicket($ticket)
    {
        $this->ticket = $ticket;
    }

	/**
     * @param User $crtusr
     */
    public function setCrtusr($crtusr)
    {
        $this->crtusr = $crtusr;
    }

	/**
     * @param field_type $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

	/**
     * @param field_type $entry
     */
    public function setEntry($entry)
    {
        $this->entry = $entry;
    }
    
}


?>