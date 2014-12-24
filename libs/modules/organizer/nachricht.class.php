<?php 
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       01.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('msgFolder.class.php');
require_once 'contact.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'Mail.php';
require_once 'libs/modules/documents/document.class.php';
require_once 'thirdparty/swift-4.1.6/swift_required.php';
class Nachricht 
{
    const ORDER_DATE = "created";
    const ORDER_NAME = "subject";
    const ORDER_FROM = "from";
    
    private $to = Array();
    private $from;
    private $subject;
    private $text;
    private $created = 0;
    private $id;
    private $read;
    private $answered;
    private $attachments = Array();

    function __construct($id = 0) 
    {
        global $DB;
        global $_USER;
        // Nachricht angelegt jetzt
        $this->created = time();
        
        if ($id)
        {
            $sql = "SELECT t1.*, t2.gelesen, t2.geantwortet 
                    FROM msg t1, msg_in_folder t2
                    WHERE
                        t2.user_id = {$_USER->getId()} AND
                        t2.msg_id = t1.id AND
                        t1.id = {$id}";
            if ($DB->num_rows($sql))
            {
                $r = $DB->select($sql);
                $this->text = $r[0]["text"]; 
                $this->subject = $r[0]["subject"];
                $this->created = $r[0]["created"];
                $this->from = new User($r[0]["from_id"], false);
                $this->id = $r[0]["id"];
                $this->read = $r[0]["gelesen"];
                $this->answered = $r[0]["geantwortet"];
            } 
            
            $sql = "SELECT * FROM msg_in_folder
                    WHERE msg_id = {$this->id}";
            if($DB->num_rows($sql))
            {
                $res = $DB->select($sql);
                foreach ($res as $r)
                    $this->to[] = new User($r["user_id"]);
            }
            
            $sql = "SELECT * FROM msg_docs
            WHERE msg_id = {$this->id}";
            if($DB->num_rows($sql))
            {
                $res = $DB->select($sql);
                foreach ($res as $r)
                    $this->attachments[] = new Document($r["document_id"]);
            }
        }
    }
    
    static function getAllNachrichten($folder = 0, $order = self::ORDER_DATE)
    {
        if ($folder == 0)
            return false;
        
        global $_USER;
        global $DB;
        $msgs = Array();
        
        $sql = "SELECT t1.id 
                FROM msg t1, msg_in_folder t2
                WHERE 
                    t2.user_id = {$_USER->getId()} AND
                    t2.folder_id = {$folder} AND
                    t2.msg_id = t1.id
                ORDER BY {$order}";

        if ($DB->num_rows($sql))
        {
            $res = $DB->select($sql);
            foreach ($res as $r)
            {
                $msgs[] = new Nachricht($r["id"]);
            }
        }
        return $msgs;
    }
    
    public function getTo()
    {
        return $this->to;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getSubject()
    {
        return stripslashes($this->subject);
    }

    public function getText()
    {
        return stripslashes($this->text);
    }

    public function getCreated()
    {
        return $this->created;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getRead()
    {
        return $this->read;
    }
    
    public function getAnswered()
    {
        return $this->answered;
    }
    
    public function setTo($to)
    {
        $this->to = $to;
    }

    public function setFrom($from)
    {
        $this->from = $from;
    }

    public function setSubject($subject)
    {
        $this->subject = addslashes($subject);
    }

    public function setText($text)
    {
        $this->text = addslashes($text);
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }
    
    
 public function setRead($read)
    {
        global $DB;
        if ($read === true || $read == 1){
            $this->read = 1;
            
             $sql = "UPDATE msg_in_folder SET
                    gelesen = 1
                WHERE msg_id = {$this->id}  AND
                   folder_id = {$_REQUEST['folder']}";
        return $DB->no_result($sql);  
        }
        else {
            $this->read = 0;
        
      echo  $sql = "UPDATE msg_in_folder SET
                    gelesen = 0
                WHERE msg_id = {$this->id}  AND
                   folder_id = {$_REQUEST['folder']}";
        return $DB->no_result($sql);
        }
    }

    public function setAnswered($answered)
    {
        global $DB;
        if ($answered === true || $answered == 1)
            $this->read = 1;
        else
            $this->read = 0;
        
       $sql = "UPDATE msg_in_folder SET
                    geantwortet = 1
                WHERE msg_id = {$this->id} AND
                   folder_id = {$_REQUEST['folder']}";
        return $DB->no_result($sql);
    }
    
    public function send()
    {
        $mail_obj =& Mail::factory('mail');
        global $DB;
        $ret = true;
        if ($this->from == null)
            $this->from == $_USER;
        
        // Falls kein Empfänger angegeben ist, abbrechen
        if (count($this->to) == 0)
            return false;
        
        if ($this->subject == "")
            $this->subject = "Kein Betreff";
        
        if ($this->created == 0)
            $this->created = time();
        
        // Nachricht anlegen
        $sql = "INSERT INTO msg
                    (from_id, subject, text, created)
                VALUES
                    ({$this->getFrom()->getId()}, '{$this->subject}',
                     '{$this->text}', {$this->created})";
        $res = $DB->no_result($sql);
        
        if ($res)
        {
            $sql = "SELECT max(id) cc FROM msg";
            $thisid = $DB->select($sql);
            $this->id = $thisid[0]["cc"];

            // Anhänge abspeichern
            foreach($this->attachments as $at)
            {
                $sql = "INSERT INTO msg_docs
                (msg_id, document_id)
                VALUES
                ({$this->id}, {$at->getId()})";
                $DB->no_result($sql);
            }
            
          
            // Beim Absender unter Gesendet einsortieren
            $paus = MsgFolder::getIdForName("Postausgang", $this->from);
            $sql = "INSERT INTO msg_in_folder
                        (msg_id, user_id, folder_id, gelesen, geantwortet)
                    VALUES
                        ({$this->id}, {$this->from->getId()}, {$paus}, 1, 0)";
            if(!$DB->no_result($sql))
                return false;

            // Beim Empfänger unter Posteingang einsortieren
            foreach($this->to as $to) {
                if(is_a($to, "User"))
                {
                    // Standardordner anlegen
                    MsgFolder::checkFolders($to);
                    $pein = MsgFolder::getIdForName("Posteingang", $to);
                    $sql = "INSERT INTO msg_in_folder
                                (msg_id, user_id, folder_id, gelesen, geantwortet)
                            VALUES
                                ({$this->id}, {$to->getId()}, {$pein}, 0, 0)";
    
                    if (!$DB->no_result($sql))
                        return false;


                    
                    if($to->getForwardMail())
                    {
                        $mailtext = 'Hallo '.$to->getFirstname().' '.$to->getLastname().'<br><br>';
						$mailtext .= "es gibt eine neue Nachricht im System:<br><br>";
						$mailtext .= "-----------------------------------------------------------------<br><br>".$this->text;
                        
                        $header["From"] = "{$this->getFrom()->getFirstname()} {$this->getFrom()->getLastname()} <{$this->getFrom()->getEmail()}>";
                        $header["Subject"] = $this->subject;
                        $header["To"] = "{$to->getFirstname()} {$to->getLastname()} <{$to->getEmail()}>";
                        
                        $mail_obj->send($to->getEmail(), $header, $mailtext);
                    }
                } else if(is_a($to, "Group"))
                {
                    foreach($to->getMembers() as $m)
                    {
                        // Standardordner anlegen
                        MsgFolder::checkFolders($m);
                        $pein = MsgFolder::getIdForName("Posteingang", $m);
                        $sql = "INSERT INTO msg_in_folder
                                    (msg_id, user_id, folder_id, gelesen, geantwortet)
                                VALUES
                                    ({$this->id}, {$m->getId()}, {$pein}, 0, 0)";
                        
                        if (!$DB->no_result($sql))
                            return false;
                        
                        if($m->getForwardMail())
                        {
                            $mailtext = "Hallo {$m->getFirstname()} {$m->getLastname()},<br><br>
es gibt eine neue Nachricht im System:<br><br>
-----------------------------------------------------------------<br><br>".$this->text;
                        
                            $header["From"] = "{$this->getFrom()->getFirstname()} {$this->getFrom()->getLastname()} <{$this->getFrom()->getEmail()}>";
                            $header["Subject"] = $this->subject;
                            $header["To"] = "{$m->getFirstname()} {$m->getLastname()} <{$m->getEmail()}>";
                        
                            $mail_obj->send($to->getEmail(), $header, $mailtext);
                        }
                        
                    }
                } else if(is_a($to, "UserContact"))
                {
                    if(count($this->attachments) > 0)
                    {
                        $x = 0;
                        foreach ($this->attachments as $at)
                        {
                            $files[$x]["file"] = $at->getFilename(Document::VERSION_EMAIL);
                            $files[$x]["name"] = $at->getName().".pdf";
                            $x++;
                        }
                        
                        $this->sendExternalMail($this->subject, 
                                                $this->text, 
                                                $to->getEmail(), $to->getName1(),
                                                $this->getFrom()->getEmail(), 
                                                "{$this->getFrom()->getFirstname()} {$this->getFrom()->getLastname()}",
                                                $files);
                    } else
                    {
                        $this->sendExternalMail($this->subject,
                                $this->text,
                                $to->getEmail(), $to->getName1(),
                                $this->getFrom()->getEmail(),
                                "{$this->getFrom()->getFirstname()} {$this->getFrom()->getLastname()}");
                    }   
                } else if(is_a($to, "BusinessContact"))
                {
                    if(count($this->attachments) > 0)
                    {
                        $x = 0;
                        foreach ($this->attachments as $at)
                        {
                            $files[$x]["file"] = $at->getFilename(Document::VERSION_EMAIL);
                            $files[$x]["name"] = $at->getName().".pdf";
                            $x++;
                        }
                        
                        $this->sendExternalMail($this->subject, 
                                                $this->text, 
                                                $to->getEmail(), $to->getName1(),
                                                $this->getFrom()->getEmail(), 
                                                "{$this->getFrom()->getFirstname()} {$this->getFrom()->getLastname()}",
                                                $files);
                    } else
                    {
                        $this->sendExternalMail($this->subject,
				                                $this->text,
				                                $to->getEmail(), $to->getName1(),
				                                $this->getFrom()->getEmail(),
				                                "{$this->getFrom()->getFirstname()} {$this->getFrom()->getLastname()}");
                    }   
                } else if(is_a($to, "ContactPerson"))
                { 
                    if(count($this->attachments) > 0)
                    {
                        $x = 0;
                        foreach ($this->attachments as $at)
                        {
                            $files[$x]["file"] = $at->getFilename(Document::VERSION_EMAIL);
                            $files[$x]["name"] = $at->getName().".pdf";
                            $x++;
                        }
                        
                        $this->sendExternalMail($this->subject, 
                                                $this->text, 
                                                $to->getEmail(), $to->getName1(),
                                                $this->getFrom()->getEmail(), 
                                                "{$this->getFrom()->getFirstname()} {$this->getFrom()->getLastname()}",
                                                $files);
                    } else
                    {
                        $this->sendExternalMail($this->subject,
                                $this->text,
                                $to->getEmail(), $to->getName1(),
                                $this->getFrom()->getEmail(),
                                "{$this->getFrom()->getFirstname()} {$this->getFrom()->getLastname()}");
                    }                   
                } else if(is_string($to))
                { 
                    if(count($this->attachments) > 0)
                    {
                        $x = 0;
                        foreach ($this->attachments as $at)
                        {
                            $files[$x]["file"] = $at->getFilename(Document::VERSION_EMAIL);
                            $files[$x]["name"] = $at->getName().".pdf";
                            $x++;
                        }
                        
                        $this->sendExternalMail($this->subject, 
                                                $this->text, 
                                                $to, "",
                                                $this->getFrom()->getEmail(), 
                                                "{$this->getFrom()->getFirstname()} {$this->getFrom()->getLastname()}",
                                                $files);
                    } else
                    {
                        $this->sendExternalMail($this->subject,
                                $this->text,
                                $to, "",
                                $this->getFrom()->getEmail(),
                                "{$this->getFrom()->getFirstname()} {$this->getFrom()->getLastname()}");
                    }                   
                }
            }
            return true;
        }
        return false;
    }
    
    function delete()
    {
        global $DB;
        global $_USER;
        $papierkorb = MsgFolder::getIdForName("Papierkorb", $_USER);
        
       
       $sql = "SELECT name FROM msg_folder
                WHERE
                    id = {$_REQUEST['folder']}";
            $thisid = $DB->select($sql);
            $this->folder_name = $thisid[0]["name"];
            
            
        if($this->folder_name == "Papierkorb")  {
        	$sql = "DELETE from  msg_in_folder
                WHERE
                    user_id = {$_USER->getId()} AND
                    msg_id = {$this->id} AND
                   folder_id = {$_REQUEST['folder']}";
        $DB->no_result($sql);
        }  
        else {
         $sql = "UPDATE msg_in_folder
                    SET folder_id = {$papierkorb} 
                WHERE
                    user_id = {$_USER->getId()} AND
                    msg_id = {$this->id} AND
                   folder_id = {$_REQUEST['folder']}";
        $DB->no_result($sql);
        }
        
        unset($this);
    }
    
    private function sendExternalMail($title, $body, $rcpt_addr, $rcpt_name = "", $from_addr = "", $from_name = "", $attachments = NULL)
    {
        global $_USER;
        if($from_addr == "")
            $from_addr = $_USER->getEmail();
        if($from_name == "")
            $from_name = "{$_USER->getFirstname()} {$_USER->getLastname()}";
        
        // Change this for different transports
        $smtp = Swift_Mailer::newInstance(Swift_MailTransport::newInstance());
        $message = Swift_Message::newInstance($title, $body, "text/html");
        $message->setContentType('text/html');
        
        // Set parameters
        $message->setFrom(Array($from_addr => $from_name));
        //$message->setSubject($title);
        //$message->setBody($body, "text/html"); //Damit der Inhalt auch von Mail-Programm als html gelesen wird
        if(count($attachments) > 0)
        {
            foreach($attachments as $at)
                $message->attach(Swift_Attachment::fromPath($at["file"])->setFilename($at["name"]));
        }

        $message->setTo(Array($rcpt_addr => $rcpt_name));

        $numsent = $smtp->send($message);
         
        if ($numsent > 0)
            return true;
        else
            return false;
    }

    public function getAttachments()
    {
        return $this->attachments;
    }

    public function setAttachments($attachments)
    {
        $this->attachments = $attachments;
    }
    
 public function moveToFolder()
    {
        global $DB;
    	 $sql = "UPDATE msg_in_folder SET
                   folder_id = {$_REQUEST['folder']}
                WHERE msg_id = {$this->id} AND
                   folder_id = {$_REQUEST['crt_folder']}
                ";
        return $DB->no_result($sql);
    }
}

?>