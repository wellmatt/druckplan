<?php
// ----------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

class Attachment {
    
    private $id;
    private $title;
    private $crtdate;
    private $crtuser;
    private $state = 1;
    private $module;
    private $objectid;
    private $filename;
    private $orig_filename;
    
    const FILE_DESTINATION = "./docs/attachments/";
    
    function __construct($id = 0){
        global $DB;
        global $_USER;
    
        $this->crtuser	= new User(0);
    
        if($id>0){
            $sql = "SELECT * FROM attachments WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                $this->id 				= (int)$r["id"];
                $this->title 			= $r["title"];
                $this->crtdate		    = $r["crtdate"];
                $this->state 			= (int)$r["state"];
                $this->module	        = $r["module"];
                $this->objectid			= (int)$r["objectid"];
                $this->filename   	    = $r["filename"];
                $this->orig_filename   	= $r["orig_filename"];
    
                if ($r["crtuser"] > 0){
                    $this->crtuser		= new User($r["crtuser"]);
                } else {
                    $this->crtuser		= new User(0);
                }
    
            }
        }
    }

    /**
     * Speicher-Funktion fuer Kommentare
     *
     * @return boolean
     */
    function save()
    {
        global $DB;
        global $_USER;
        $now = time();
        
        if ($this->id > 0) {
            $sql = "UPDATE attachments SET
            title 			= '{$this->title}',
            state			= {$this->state},
            module			= '{$this->module}',
            objectid		= {$this->objectid},
            orig_filename	= {$this->orig_filename},
            filename		= {$this->filename}
            WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO attachments
            (title, crtdate, crtuser, state, module, objectid, filename, orig_filename )
            VALUES
            ( '{$this->title}' , {$now}, {$_USER->getId()}, {$this->state}, '{$this->module}',
            {$this->objectid}, '{$this->filename}', '{$this->orig_filename}' )";
            $res = $DB->no_result($sql);
            if ($res) {
                $sql = "SELECT max(id) id FROM attachments WHERE filename = '{$this->filename}'";
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
            $sql = "UPDATE attachments SET
    					state = 0
    					WHERE id = {$this->id}";
            echo $sql;
            if ($DB->no_result($sql)) {
                unset($this);
                return true;
            } else {
                return false;
            }
        }
    }


    public static function getAttachmentsForObject($module,$objectid)
    {
        global $DB;
        $retval = Array();
    
        $sql = "SELECT id FROM attachments WHERE module = '{$module}' AND objectid = {$objectid}";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new Attachment($r["id"]);
            }
        }
        return $retval;
    }
    
    public function move_save_file($file)
    {
        $destination = "./docs/attachments/";
        
        $filename = md5($file["name"].time());
        $new_filename = $destination.$filename;
        $tmp_outer = move_uploaded_file($file["tmp_name"], $new_filename);
        if ($tmp_outer) {
            $this->orig_filename = $file["name"];
            $this->filename = $filename;
            return true;
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
     * @return the $state
     */
    public function getState()
    {
        return $this->state;
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
     * @return the $filename
     */
    public function getFilename()
    {
        return $this->filename;
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
     * @param number $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

	/**
     * @param number $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

	/**
     * @param number $objectid
     */
    public function setObjectid($objectid)
    {
        $this->objectid = $objectid;
    }

	/**
     * @param field_type $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }
    
	/**
     * @return the $orig_filename
     */
    public function getOrig_filename()
    {
        return $this->orig_filename;
    }

	/**
     * @param field_type $orig_filename
     */
    public function setOrig_filename($orig_filename)
    {
        $this->orig_filename = $orig_filename;
    }
    
}


?>