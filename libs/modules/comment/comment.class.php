<?php
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/article/article.class.php';
require_once 'comment.article.class.php';
require_once 'libs/modules/attachment/attachment.class.php';
require_once 'libs/modules/businesscontact/contactperson.class.php';

class Comment {
    
    private $id;
    private $title;
    private $crtdate;
    private $crtuser;
    private $crtcp;
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
    const VISABILITY_PUBLICMAIL = 4;
    
    function __construct($id = 0){
        global $DB;

        $this->crtuser	= new User(0);
        $this->crtcp	= new ContactPerson(0);
    
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
                $this->crtcp		    = new ContactPerson($r["crtcp"]);
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
        $now = time();
        $tmp_com = addslashes($this->comment);
        
        if ($this->id > 0) {
            $sql = "UPDATE comments SET
            title 			= '{$this->title}',
            state			= {$this->state},
            comment			= '{$tmp_com}',
            visability			= {$this->visability},
            mailed			= {$this->mailed}
            WHERE id = {$this->id}";
            return $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO comments
            (title, crtdate, crtuser, crtcp, state, module, objectid, comment, visability, mailed )
            VALUES
            ( '{$this->title}' , {$now}, {$this->crtuser->getId()}, {$this->crtcp->getId()}, {$this->state}, '{$this->module}',
            {$this->objectid}, '{$tmp_com}', {$this->visability}, {$this->mailed} )";
            $res = $DB->no_result($sql);
            if ($res) {
                $sql = "SELECT max(id) id FROM comments WHERE title = '{$this->title}'";
                $thisid = $DB->select($sql);
                $this->id = $thisid[0]["id"];
                $this->crtdate = $now;
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
    
    public static function showComment($id)
    {
        $comment = new Comment($id);
        $classname = $comment->getModule();
        
        switch ($classname)
        {
            case "Ticket":
                return 'libs/modules/tickets/ticket.php&exec=edit&tktid='.$comment->getObjectid();
                break;
            case "Comment":
                $tmp_comment = new Comment($comment->getObjectid());
                Comment::showComment($tmp_comment->getModule());
                break;
        }
    }
    
    public static function getCommentCountForObject($module,$objectid)
    {
        global $DB;
        $retval = 0;
        
        $sql = "SELECT count(id) as count FROM comments WHERE module = '{$module}' AND objectid = {$objectid} AND state > 0";
        if($DB->num_rows($sql)){
            $r = $DB->select($sql);
            $r = $r[0];
            $retval = $r["count"];
        }
        
        return $retval;
    }

    /**
     * @param $module
     * @param $objectid
     * @param string $orderby
     * @return Comment[]
     */
    public static function getCommentsForObject($module,$objectid,$orderby = 'crtdate')
    {
        global $DB;
        $retval = Array();
        
        $sql = "SELECT id FROM comments WHERE module = '{$module}' AND objectid = {$objectid} ORDER BY {$orderby} ASC";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
            	$retval[] = new Comment($r["id"]);
            }
        }
        
        return $retval;
    }

    /**
     * @param $module
     * @param $objectid
     * @return Comment[]
     */
    public static function getCommentsForObjectSummary($module,$objectid)
    {
        global $DB;
        $retval = Array();
        
        $sql = "SELECT id FROM comments WHERE module = '{$module}' AND objectid = {$objectid} ORDER BY id DESC";
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
            	$retval[] = new Comment($r["id"]);
            }
        }
        
        return $retval;
    }
    
    public static function getLatestCommentsForObject($module,$objectid)
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
    
    public static function getObjectParticipants($module,$objectid){
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
     * @return CommentArticle[]
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
    
	/**
     * @return the $crtcp
     */
    public function getCrtcp()
    {
        return $this->crtcp;
    }

	/**
     * @param ContactPerson $crtcp
     */
    public function setCrtcp($crtcp)
    {
        $this->crtcp = $crtcp;
    }
    
}


?>