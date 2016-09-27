<?php
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/basic/user/user.class.php';

class TicketCategory {
    
    private $id;
    private $title;
    private $protected = 0;
    private $sort = 0;
    
    private $groups_cansee = Array();
    private $groups_cancreate = Array();
    
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
                $this->sort		        = $r["sort"];
    
                $sql = "SELECT * FROM tickets_categories_groupperm WHERE categoryid = {$id}";
                $tmp_groups_cansee = Array();
                $tmp_groups_cancreate = Array();
                if($DB->num_rows($sql)){
                    foreach($DB->select($sql) as $r){
                    	$tmp_group = new Group((int)$r["groupid"]);
                    	if ((int)$r["cansee"] == 1){
                    	    $tmp_groups_cansee[] = $tmp_group;
                    	}
                    	if ((int)$r["cancreate"] == 1){
                    	    $tmp_groups_cancreate[] = $tmp_group;
                    	}
                    }
                }
                $this->groups_cansee = $tmp_groups_cansee;
                $this->groups_cancreate = $tmp_groups_cancreate;
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
            title 			= '{$this->title}', 
            sort 			= {$this->sort} 
            WHERE id = {$this->id}";
            $save_ok = $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO tickets_categories
            (title, protected, sort)
            VALUES
            ( '{$this->title}' , {$this->protected}, {$this->sort} )";
            $res = $DB->no_result($sql);
            if ($res) {
                $sql = "SELECT max(id) id FROM tickets_categories WHERE title = '{$this->title}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                $save_ok = true;
            } else {
                $save_ok = false;
            }
        }
        
        $sql = "DELETE FROM tickets_categories_groupperm WHERE categoryid = {$this->id}";
        $DB->no_result($sql);
        
        foreach (Group::getAllGroups() as $group){
            $cansee = 0;
            $cancreate = 0;
            if (in_array($group, $this->groups_cansee)){
                $cansee = 1;
            }
            if (in_array($group, $this->groups_cancreate)){
                $cancreate = 1;
            }
            $sql = "INSERT INTO tickets_categories_groupperm 
                    (categoryid, groupid, cansee, cancreate) 
                    VALUES ( {$this->id}, {$group->getId()}, {$cansee}, {$cancreate} )";
            $DB->no_result($sql);
        }
        return $save_ok;
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

    /**
     * @return TicketCategory[]
     */
    public static function getAllCategories()
    {
        global $DB;
        $retval = Array();
    
        $sql = "SELECT id FROM tickets_categories ORDER BY sort ASC";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new TicketCategory($r["id"]);
            }
        }
        return $retval;
    }
    
    public function cansee()
    {
        global $_USER;
        
        if ($_USER->isAdmin())
            return true;

        foreach ($this->groups_cansee as $gsee){
            if ($_USER->isInGroup($gsee)){
                return true;
            }
        }
        return false;
    }
    
    public function cancreate()
    {
        global $_USER;
        
        if ($_USER->isAdmin())
            return true;

        foreach ($this->groups_cancreate as $gsee){
            if ($_USER->isInGroup($gsee)){
                return true;
            }
        }
        return false;
    }
    
	/**
     * @return int $id
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
    
	/**
     * @return the $sort
     */
    public function getSort()
    {
        return $this->sort;
    }

	/**
     * @param number $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }
    
	/**
     * @return the $groups_cansee
     */
    public function getGroups_cansee()
    {
        return $this->groups_cansee;
    }

	/**
     * @return the $groups_cancreate
     */
    public function getGroups_cancreate()
    {
        return $this->groups_cancreate;
    }

	/**
     * @param multitype: $groups_cansee
     */
    public function setGroups_cansee($groups_cansee)
    {
        $this->groups_cansee = $groups_cansee;
    }

	/**
     * @param multitype: $groups_cancreate
     */
    public function setGroups_cancreate($groups_cancreate)
    {
        $this->groups_cancreate = $groups_cancreate;
    }
    
}


?>