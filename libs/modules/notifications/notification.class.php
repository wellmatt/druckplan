<? 
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			05.01.2015
// Copyright:		2015 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/abonnements/abonnement.class.php';

class Notification {

    private $id;
    private $user;
    private $title;
    private $path;
    private $crtdate;
    private $state = 1;
    private $crtmodule;
    private $count = 1;
    private $objectid = 0;
    
    const NOTIF_UNREAD = 1;
    const NOTIF_READ = 0;

    function __construct($id = 0){
        global $DB;
        global $_USER;

        $this->user	= new User(0);

        if($id>0){
            $sql = "SELECT * FROM notifications WHERE id = {$id}";
            if($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $r = $r[0];
                $this->id 				= (int)$r["id"];
                $this->user 			= new User((int)$r["user"]);
                $this->title		    = $r["title"];
                $this->path		        = $r["path"];
                $this->crtdate 			= (int)$r["crtdate"];
                $this->state	        = (int)$r["state"];
                $this->crtmodule		= $r["crtmodule"];
                $this->count		    = $r["count"];
                $this->objectid		    = $r["objectid"];
            }
        }
    }

    /**
     * Speicher-Funktion fuer Notifications
     *
     * @return boolean
     */
    function save()
    {
        global $DB;
        global $_USER;
        $now = time();

        if ($this->id > 0) {
            $sql = "UPDATE notifications SET 
            state			= {$this->state}, 
            title			= '{$this->title}', 
            path			= '{$this->path}', 
            count			= {$this->count} 
            WHERE id = {$this->id}";
//             echo $sql . "</br>";
            return $DB->no_result($sql);
        } else {
            $sql = "INSERT INTO notifications
            (`user`, title, path, crtdate, state, crtmodule, objectid, count )
            VALUES
            ( {$this->user->getId()}, '{$this->title}', '{$this->path}',
            {$now}, {$this->state}, '{$this->crtmodule}', {$this->objectid}, {$this->count} )";
            $res = $DB->no_result($sql);
//             echo $sql . "</br>";
            if ($res) {
                $sql = "SELECT max(id) id FROM notifications WHERE crtdate = '{$now}'";
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
     * Loeschfunktion fuer Notifications.
     * Die Notification wird nicht entgueltig geloescht, der Status wird auf 0 gesetzt (gelesen)
     *
     * @return boolean
     */
    public function delete()
    {
        global $DB;
        if ($this->id > 0) {
            $sql = "UPDATE notifications SET
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
    
    public function readAll()
    {
        global $DB;
        global $_USER;
        $sql = "UPDATE notifications SET
        state = 0
        WHERE user = {$_USER->getId()}";
        if ($DB->no_result($sql)) {
            return true;
        } else {
            return false;
        }
    }
    
    public static function getMyNotifications($limit = 10){
        global $DB;
        global $_USER;
        $retval = Array();
        
        $sql = "SELECT id FROM notifications WHERE user = {$_USER->getId()} AND state > 0 ORDER BY crtdate DESC LIMIT {$limit}";
//         echo $sql;
        if($DB->num_rows($sql)){
            foreach($DB->select($sql) as $r){
                $retval[] = new Notification($r["id"]);
            }
        }
        return $retval;
    }
    
    public static function checkUnreadNotification($user,$crtmodule,$objectid)
    {
        global $DB;
        global $_USER;
        
        $retval = new Notification();
        $sql = "SELECT id FROM notifications WHERE user = {$user} AND crtmodule = '{$crtmodule}' AND objectid = {$objectid}";
//         echo $sql . "</br>";
        if($DB->num_rows($sql)){
            $r = $DB->select($sql);
            $r = $r[0];
            $retval = new Notification((int)$r["id"]);
        }
        return $retval;
    }
    
    public static function removeForObject($crtmodule,$objectid)
    {
        global $DB;
        $sql = "DELETE FROM notifications 
                WHERE crtmodule = '{$crtmodule}' AND objectid = {$objectid}";
        if ($DB->no_result($sql)) {
            unset($this);
            return true;
        } else {
            return false;
        }
    }
     
    public static function generateNotification($touser, $crtmodule, $type, $reference, $objectid, $group = "", $notes = Array()){
        global $_USER;
        if ($touser->getId() == $_USER->getId() && $type != "NewOrderShop")
            return true;
        $tmp_notification = self::checkUnreadNotification($touser->getId(), $crtmodule, $objectid);
        if ($tmp_notification->getId()>0)
        {
            $tmp_notification->setCount($tmp_notification->getCount()+1);
            $tmp_notification->setState(1);
        }
        $tmp_notification->setObjectid($objectid);
        $tmp_notification->setUser($touser);
        switch($crtmodule){
            case "CollectiveInvoice":
                switch ($type){
                    case "NewOrderShop":
                        $tmp_notification->setTitle("Neue Bestellung von \"".$reference."\"");
                        $tmp_notification->setPath("index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid=".$objectid);
                        $tmp_notification->setCrtmodule($crtmodule);
                        $tmp_notification->save();
                        break;
                }
                break;
            case "Event":
                switch ($type){
                    case "NewEvent":
                        $tmp_notification->setTitle("Neue Termineinladung von \"".$reference."\"");
                        $tmp_notification->setPath("index.php?page=libs/modules/organizer/calendar.php&exec=showevent&id=".$objectid);
                        $tmp_notification->setCrtmodule($crtmodule);
                        $tmp_notification->save();
                        break;
                    case "ChangeEvent":
                        if (count($notes)>0)
                        {
                            $notes = implode(", ", $notes);
                            $tmp_notification->setTitle("Terminänderung von \"".$reference."\" ( ".$notes." )");
                        } else {
                            $tmp_notification->setTitle("Terminänderung von \"".$reference."\"");
                        }
                        $tmp_notification->setPath("index.php?page=libs/modules/organizer/calendar.php&exec=showevent&id=".$objectid);
                        $tmp_notification->setCrtmodule($crtmodule);
                        $tmp_notification->save();
                        break;
                }
                break;
            case "Ticket":
                switch ($type){
                    case "Comment":
                        $tmp_notification->setTitle("Neues Kommentar von ".$_USER->getNameAsLine()." in Ticket #".$reference);
                        $tmp_notification->setPath("index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid=".$objectid);
                        $tmp_notification->setCrtmodule($crtmodule);
                        $tmp_notification->save();
                        break;
                    case "CommentCP":
                        $tmp_notification->setTitle("Neues Kommentar des Kunden in Ticket #".$reference);
                        $tmp_notification->setPath("index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid=".$objectid);
                        $tmp_notification->setCrtmodule($crtmodule);
                        $tmp_notification->save();
                        break;
                    case "NewFromCP":
                        $tmp_notification->setTitle("Neues Ticket vom Kunden #".$reference);
                        $tmp_notification->setPath("index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid=".$objectid);
                        $tmp_notification->setCrtmodule($crtmodule);
                        $tmp_notification->save();
                        break;
                    case "CommentEdit":
                        $tmp_notification->setTitle("Ein Kommentar wurde von ".$_USER->getNameAsLine()." in Ticket #".$reference. ' bearbeitet');
                        $tmp_notification->setPath("index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid=".$objectid);
                        $tmp_notification->setCrtmodule($crtmodule);
                        $tmp_notification->save();
                        break;
                    case "Assign":
                        $tmp_notification->setTitle("Ticket Zuweisung durch ".$_USER->getNameAsLine()." von Ticket #".$reference);
                        $tmp_notification->setPath("index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid=".$objectid);
                        $tmp_notification->setCrtmodule($crtmodule);
                        $tmp_notification->save();
                        break;
                    case "AssignGroup":
                        $tmp_notification->setTitle("Ticket Zuweisung (".$group.") durch ".$_USER->getNameAsLine()." von Ticket #".$reference);
                        $tmp_notification->setPath("index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid=".$objectid);
                        $tmp_notification->setCrtmodule($crtmodule);
                        $tmp_notification->save();
                        break;
                }
                break;
        }
        return true;
    }
    
    public static function generateNotificationsFromAbo($crtmodule, $type, $reference, $objectid, $group = "")
    {
        global $_USER;
        $abos = Abonnement::getAbonnementsForObject($crtmodule, $objectid);
        
        if (count($abos)>0){
            foreach ($abos as $abo){
                if ($abo->getAbouser()->getId() != $_USER->getId())
                    Notification::generateNotification($abo->getAbouser(), $crtmodule, $type, $reference, $objectid, $group = "");
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
     * @return the $user
     */
    public function getUser()
    {
        return $this->user;
    }

	/**
     * @return the $title
     */
    public function getTitle()
    {
        return $this->title;
    }

	/**
     * @return the $path
     */
    public function getPath()
    {
        return $this->path;
    }

	/**
     * @return the $crtdate
     */
    public function getCrtdate()
    {
        return $this->crtdate;
    }

	/**
     * @return the $state
     */
    public function getState()
    {
        return $this->state;
    }

	/**
     * @return the $crtmodule
     */
    public function getCrtmodule()
    {
        return $this->crtmodule;
    }

	/**
     * @param number $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

	/**
     * @param Ambigous <User, boolean, string> $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

	/**
     * @param field_type $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

	/**
     * @param field_type $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

	/**
     * @param number $crtdate
     */
    public function setCrtdate($crtdate)
    {
        $this->crtdate = $crtdate;
    }

	/**
     * @param number $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

	/**
     * @param field_type $crtmodule
     */
    public function setCrtmodule($crtmodule)
    {
        $this->crtmodule = $crtmodule;
    }
    
	/**
     * @return the $count
     */
    public function getCount()
    {
        return $this->count;
    }

	/**
     * @param field_type $count
     */
    public function setCount($count)
    {
        $this->count = $count;
    }
    
	/**
     * @return the $objectid
     */
    public function getObjectid()
    {
        return $this->objectid;
    }

	/**
     * @param number $objectid
     */
    public function setObjectid($objectid)
    {
        $this->objectid = $objectid;
    }
}


?>