<?php
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

class TicketCategory {
    
    private $id;
    private $title;
    private $protected = 0;
    
    function __construct($id = 0){
        global $DB;
    
        if($id>0){
            $sql = "SELECT * FROM tickets_categories WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                $this->id 				= (int)$r["id"];
                $this->title 			= $r["title"];
                $this->protected		= $r["protected"];
    
            }
        }
    }

    /**
     * Speicher-Funktion fuer Ticket Kategorien
     *
     * @return boolean
     */
    function save()
    {
        global $DB;
        
        if ($this->id > 0) {
            $sql = "UPDATE tickets_categories SET
            title 			= '{$this->title}'
            WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO tickets_categories
            (title, protected)
            VALUES
            ( '{$this->title}' , {$this->protected} )";
            $res = $DB->no_result($sql);
            if ($res) {
                $sql = "SELECT max(id) id FROM tickets_categories WHERE title = '{$this->title}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Loeschfunktion fuer Ticket Kategorien.
     *
     * @return boolean
     */
    public function delete()
    {
        global $DB;
            if ($this->id > 0 && $this->protected == 0) {
            $sql = "DELETE FROM tickets_categories 
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
    

    public function getAllCategories()
    {
        global $DB;
        $retval = Array();
    
        $sql = "SELECT id FROM tickets_categories ORDER BY title";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new TicketCategory($r["id"]);
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
     * @param number $protected
     */
    public function setProtected($protected)
    {
        $this->protected = $protected;
    }
    
}


?>