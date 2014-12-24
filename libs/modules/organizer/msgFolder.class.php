<?php 
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       01.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

class MsgFolder {
    
    const ORDER_NAME = "name";
    
    private $name;
    private $parent;
    private $sub;
    private $id;
    
    public function __construct($id = 0)
    {
        global $DB;
        if ($id)
        {
            $sql = "SELECT * FROM msg_folder
                    WHERE id = {$id}";
            if ($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $this->name = $r[0]["name"];
                $this->parent = $r[0]["parent"];
                $this->id = $r[0]["id"];
            }
                
        }
    }
    
    public static function getSubFolders($parent = 0, $order = self::ORDER_NAME) {
        global $DB;
        global $_USER;
        $i = 0;
        
        $folders = Array();
        
        $sql = "SELECT * FROM msg_folder 
                WHERE
                    user_id = {$_USER->getId()} AND
                    parent = {$parent}
                ORDER BY {$order}";
        
        if ($DB->num_rows($sql) > 0)
        {
            $res = $DB->select($sql);
            foreach ($res as $r)
            {
                $folders[$i] = new MsgFolder($r["id"]);
                $retval = self::getSubFolders($r["id"]);
                if ($retval)
                    $folders[$i]->setSub($retval);  
                $i++;  
            } 
        }
        
        if (count($folders)>0)
            return $folders;
        else
            return false;
    }

    static function getIdForName($name, $user = 0)
    {
        global $DB;
        global $_USER;
        if ($user == 0)
            $user = $_USER;
        $sql = "SELECT id FROM msg_folder 
                WHERE user_id = {$user->getId()} AND
                    name like '{$name}'";
        if ($DB->num_rows($sql))
        {
            $r = $DB->select($sql);
            return $r[0]["id"];
        } else 
            return false;
    }
    
    /**Prüft ob die Ordner Posteingang, Postausgang und Papierkorb existieren, und legt diese im Zweifel an
     * @param int $uid
     */
    static function checkFolders($uid = 0)
    {
        global $DB;
        global $_USER;
        
        if ($uid == 0)
            $uid = $_USER;
        
        // Existiert die Inbox
        $sql = "SELECT id FROM msg_folder 
                WHERE 
                    name = 'Posteingang' AND
                    user_id = {$uid->getId()}";
        if ($DB->num_rows($sql) < 1)
        {
            $sql = "INSERT INTO msg_folder
                        (user_id, name, parent)
                    VALUES
                        ({$uid->getId()}, 'Posteingang', 0)";
            $DB->no_result($sql);
        }
        
        // Existiert die Outbox?
        $sql = "SELECT id FROM msg_folder
                WHERE
                    name = 'Postausgang' AND
                    user_id = {$uid->getId()}";
        if ($DB->num_rows($sql) < 1)
        {
            $sql = "INSERT INTO msg_folder
                        (user_id, name, parent)
                    VALUES
                        ({$uid->getId()}, 'Postausgang', 0)";
            $DB->no_result($sql);
        }
        
        // Existiert der Papierkorb?
        $sql = "SELECT id FROM msg_folder
                WHERE
                    name = 'Papierkorb' AND
                    user_id = {$uid->getId()}";
        if ($DB->num_rows($sql) < 1)
        {
            $sql = "INSERT INTO msg_folder
                        (user_id, name, parent)
                    VALUES
                        ({$uid->getId()}, 'Papierkorb', 0)";
            $DB->no_result($sql);
        }
        
    }
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    
    

    public function getSub()
    {
        return $this->sub;
    }

    public function setSub($sub)
    {
        $this->sub = $sub;
    }
    

    public function getParent()
    {
        return $this->parent;
    }

    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    public function getId()
    {
        return $this->id;
    }

}
?>