<?php
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

class CommentArticle {
    
    private $id;
    private $comment_id;
    private $state = 1;
    private $article;
    private $amount;
    
    function __construct($id = 0){
        global $DB;
    
        $this->article = new Article(0);
        
        if($id>0){
            $sql = "SELECT * FROM comments_article WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                $this->id 				= (int)$r["id"];
                $this->comment_id 		= $r["comment_id"];
                $this->state		    = $r["state"];
                $this->article		    = new Article($r["articleid"]);
                $this->amount		    = $r["amount"];
    
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
        
        if ($this->id>0)
        {
            $sql = "UPDATE comments_article SET 
            comment_id = {$this->comment_id}, 
            state = {$this->state}, 
            articleid = {$this->article->getId()}, 
            amount = {$this->amount} 
            WHERE id = {$this->id}";
//             echo $sql;
            $res = $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO comments_article
            (comment_id, state, articleid, amount)
            VALUES
            ( {$this->comment_id} , {$this->state}, {$this->article->getId()}, {$this->amount} )";
//             echo $sql;
            $res = $DB->no_result($sql);
        }
        
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
            $sql = "UPDATE comments_article 
                    SET state = 0 
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
     * @return the $comment_id
     */
    public function getComment_id()
    {
        return $this->comment_id;
    }

	/**
     * @return the $state
     */
    public function getState()
    {
        return $this->state;
    }

	/**
     * @return the $article
     */
    public function getArticle()
    {
        return $this->article;
    }

	/**
     * @return the $amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

	/**
     * @param number $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

	/**
     * @param field_type $comment_id
     */
    public function setComment_id($comment_id)
    {
        $this->comment_id = $comment_id;
    }

	/**
     * @param number $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

	/**
     * @param Article $article
     */
    public function setArticle($article)
    {
        $this->article = $article;
    }

	/**
     * @param field_type $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    
    
}


?>