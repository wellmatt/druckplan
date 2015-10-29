<?php
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

class TicketSource {
    
    private $id;
    private $title;
    private $default = 0;
    
    function __construct($id = 0){
        global $DB;
    
        if($id>0){
            $sql = "SELECT * FROM tickets_sources WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                $this->id 				= (int)$r["id"];
                $this->title 			= $r["title"];
                $this->default		    = $r["default"];
    
            }
        }
    }

    /**
     * Speicher-Funktion fuer Ticket Stati
     *
     * @return boolean
     */
    function save()
    {
        global $DB;
        
        if ($this->id > 0) {
            $sql = "UPDATE tickets_sources SET
                    title 	 = '{$this->title}' 
                    WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO tickets_sources
                    (title)
                    VALUES
                    ( '{$this->title}' )";
            $res = $DB->no_result($sql);
            if ($res) {
                $sql = "SELECT max(id) id FROM tickets_states WHERE title = '{$this->title}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Loeschfunktion fuer Ticket Stati.
     *
     * @return boolean
     */
    public function delete()
    {
        global $DB;
        if ($this->id > 0 && $this->id != 1 && $this->id != 2 && $this->id != 3 && $this->id != 4) {
            $sql = "DELETE FROM tickets_sources 
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
    
    public static function getAllSources()
    {
        global $DB;
        $retval = Array();
    
        $sql = "SELECT id FROM tickets_sources ORDER BY id ASC";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new TicketSource($r["id"]);
            }
        }
        return $retval;
    }
    
    public function setDefault()
    {
        global $DB;
        
        $sql = "UPDATE tickets_sources SET `default` = 0 ";
        if ($DB->no_result($sql)) {
            $sql = "UPDATE tickets_sources SET `default` = 1 WHERE id = {$this->id} ";
            if ($DB->no_result($sql)) {
                return true;
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
     * @return the $default
     */
    public function getDefault()
    {
        return $this->default;
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
    
}
?>