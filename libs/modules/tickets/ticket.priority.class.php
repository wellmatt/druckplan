<?php
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

class TicketPriority {
    
    private $id;
    private $title;
    private $value;
    private $protected = 0;
    
    function __construct($id = 0){
        global $DB;
    
        if($id>0){
            $sql = "SELECT * FROM tickets_priorities WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                $this->id 				= (int)$r["id"];
                $this->title 			= $r["title"];
                $this->value 			= $r["value"];
                $this->protected		= $r["protected"];
    
            }
        }
    }

    /**
     * Speicher-Funktion fuer Ticket Prioritaeten
     *
     * @return boolean
     */
    function save()
    {
        global $DB;
        
        if ($this->id > 0) {
            $sql = "UPDATE tickets_priorities SET
            title 			= '{$this->title}',
            value 			= '{$this->value}'
            WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO tickets_priorities
            (title, value, protected)
            VALUES
            ( '{$this->title}', {$this->value}, {$this->protected} )";
            $res = $DB->no_result($sql);
            if ($res) {
                $sql = "SELECT max(id) id FROM tickets_priorities WHERE title = '{$this->title}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Loeschfunktion fuer Ticket Prioritaeten.
     *
     * @return boolean
     */
    public function delete()
    {
        global $DB;
            if ($this->id > 0 && $this->protected == 0) {
            $sql = "DELETE FROM tickets_priorities 
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
    
    public static function getAllPriorities()
    {
        global $DB;
        $retval = Array();
    
        $sql = "SELECT id FROM tickets_priorities ORDER BY value ASC";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new TicketPriority($r["id"]);
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
     * @return the $value
     */
    public function getValue()
    {
        return $this->value;
    }

	/**
     * @return the $protected
     */
    public function getProtected()
    {
        return $this->protected;
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
     * @param field_type $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

	/**
     * @param number $protected
     */
    public function setProtected($protected)
    {
        $this->protected = $protected;
    }

    
    
    
}


?>