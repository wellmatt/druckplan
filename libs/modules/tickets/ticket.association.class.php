<?php
// ----------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------


class TicketAssociation {
    
    private $id;
    private $ticketid;
    private $module;
    private $objectid;
    
    
    function __construct($id = 0){
        global $DB;
    
        if($id>0){
            $sql = "SELECT * FROM tickets_association WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                $this->id 				= (int)$r["id"];
                $this->ticketid 		= $r["ticketid"];
                $this->module		    = $r["module"];
                $this->objectid		    = $r["objectid"];
    
            }
        }
    }

    /**
     * Speicher-Funktion fuer Ticket Verknuepfungen
     *
     * @return boolean
     */
    function save()
    {
        global $DB;
        
        $sql = "INSERT INTO tickets_association
        (ticketid, module, objectid)
        VALUES
        ( {$this->ticketid} , {$this->module}, {$this->objectid} )";
        $res = $DB->no_result($sql);
        if ($res) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Loeschfunktion fuer Ticket Verknuepfungen.
     *
     * @return boolean
     */
    public function delete()
    {
        global $DB;
        if ($this->id > 0) {
            $sql = "DELETE FROM tickets_association 
    			    WHERE id = {$this->id}";
            if ($DB->no_result($sql)) {
                unset($this);
                return true;
            } else {
                return false;
            }
        } else {
            return false;
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
     * @return the $ticketid
     */
    public function getTicketid()
    {
        return $this->ticketid;
    }

	/**
     * @return the $module
     */
    public function getModule()
    {
        return $this->module;
    }

	/**
     * @return the $objectid
     */
    public function getObjectid()
    {
        return $this->objectid;
    }

	/**
     * @param number $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

	/**
     * @param field_type $ticketid
     */
    public function setTicketid($ticketid)
    {
        $this->ticketid = $ticketid;
    }

	/**
     * @param field_type $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

	/**
     * @param field_type $objectid
     */
    public function setObjectid($objectid)
    {
        $this->objectid = $objectid;
    }

    
    
}


?>