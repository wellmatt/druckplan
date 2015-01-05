<?php
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/article/article.class.php';
include 'comment.article.class.php';
include 'libs/modules/attachment/attachment.class.php';

class Comment {
    
    private $id;
    private $title;
    private $crtdate;
    private $crtuser;
    private $state = 1;
    private $module;
    private $objectid;
    private $comment;
    private $visability;
    private $mailed = 0;
    
    private $articles = Array();
    
    const VISABILITY_PUBLIC = 1;
    const VISABILITY_INTERNAL = 2;
    const VISABILITY_PRIVATE = 3;
    
    function __construct($id = 0){
        global $DB;
        global $_USER;
    
        $this->crtuser	= new User(0);
    
        if($id>0){
            $sql = "SELECT * FROM comments WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                $this->id 				= (int)$r["id"];
                $this->title 			= $r["title"];
                $this->crtdate		    = $r["crtdate"];
                $this->crtuser		    = new User($r["crtuser"]);
                $this->state 			= (int)$r["state"];
                $this->module	        = $r["module"];
                $this->objectid			= (int)$r["objectid"];
                $this->comment			= $r["comment"];
                $this->visability   	= (int)$r["visability"];
                $this->mailed       	= (int)$r["mailed"];
    
            }
            
            $sql = "SELECT id FROM comments_article WHERE comment_id = {$id}";
        	if($DB->num_rows($sql)){
        	    $artretval = Array();
    			foreach($DB->select($sql) as $r){
    				$artretval[] = new CommentArticle($r["id"]);
    			}
    			$this->articles = $artretval;
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
            $sql = "UPDATE comments SET
            title 			= '{$this->title}',
            state			= {$this->state},
            comment			= '{$this->comment}',
            visability			= {$this->visability},
            mailed			= {$this->mailed}
            WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO comments
            (title, crtdate, crtuser, state, module, objectid, comment, visability, mailed )
            VALUES
            ( '{$this->title}' , {$now}, {$_USER->getId()}, {$this->state}, '{$this->module}',
            {$this->objectid}, '{$this->comment}', {$this->visability}, {$this->mailed} )";
            $res = $DB->no_result($sql);
            if ($res) {
                $sql = "SELECT max(id) id FROM comments WHERE title = '{$this->title}'";
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
            $sql = "UPDATE comments SET
    					state = 0
    					WHERE id = {$this->id}";
            if ($DB->no_result($sql)) {
                unset($this);
                return true;
            } else {
                return false;
            }
        }
    }
    
    public function getCommentsForObject($module,$objectid)
    {
        global $DB;
        $retval = Array();
        
        $sql = "SELECT id FROM comments WHERE module = '{$module}' AND objectid = {$objectid} ORDER BY crtdate ASC";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
            	$retval[] = new Comment($r["id"]);
            }
        }
        
        return $retval;
    }
    
    public function getLatestCommentsForObject($module,$objectid)
    {
        global $DB;
        $retval = Array();
    
        $sql = "SELECT id FROM comments WHERE module = '{$module}' AND objectid = {$objectid} AND state > 0 ORDER BY crtdate DESC LIMIT 3";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new Comment($r["id"]);
            }
        }
    
        return $retval;
    }
    
    public function getObjectParticipants($module,$objectid){
        global $DB;
        $retval = Array();
        
        $sql = "SELECT crtuser FROM comments WHERE module = '{$module}' AND objectid = {$objectid} AND state > 0 GROUP BY crtuser ORDER BY crtdate";
//         echo $sql;
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new User($r["crtuser"]);
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
     * @return the $comment
     */
    public function getComment()
    {
        return $this->comment;
    }

	/**
     * @return the $visability
     */
    public function getVisability()
    {
        return $this->visability;
    }

	/**
     * @return the $mailed
     */
    public function getMailed()
    {
        return $this->mailed;
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
     * @param field_type $comment
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    }

	/**
     * @param number $visability
     */
    public function setVisability($visability)
    {
        $this->visability = $visability;
    }

	/**
     * @param number $mailed
     */
    public function setMailed($mailed)
    {
        $this->mailed = $mailed;
    }
    
	/**
     * @return the $articles
     */
    public function getArticles()
    {
        return $this->articles;
    }

	/**
     * @param multitype: $articles
     */
    public function setArticles($articles)
    {
        $this->articles = $articles;
    }
}


?>