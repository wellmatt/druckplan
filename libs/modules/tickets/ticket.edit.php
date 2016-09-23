<?php 

global $_USER;

function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}

$_REQUEST["tktid"] = (int)$_REQUEST["tktid"];

$new_ticket = false;
if($_REQUEST["exec"] == "new"){
    $new_ticket = true;
    $ticket = new Ticket();
    $header_title = $_LANG->get('Ticket erstellen');
    
    if ($_REQUEST["customer"] && $_REQUEST["contactperson"])
    {
        $tmp_customer = new BusinessContact((int)$_REQUEST["customer"]);
        $tmp_contactperson = new ContactPerson((int)$_REQUEST["contactperson"]);
        $ticket->setCustomer($tmp_customer);
        $ticket->setCustomer_cp($tmp_contactperson);
    }
    
    if ($_REQUEST["frommail"] == true)
    {
        $ticket->setSource(Ticket::SOURCE_EMAIL); // TODO: hardcoded
        
        $mailadress = new Emailaddress($_REQUEST["mailid"]);
        
        $server = $mailadress->getHost();
        $port = $mailadress->getPort();
        $user = $mailadress->getLogin();
        $password = $mailadress->getPassword();
        
        try {
            /* Connect to an IMAP server.
             *   - Use Horde_Imap_Client_Socket_Pop3 (and most likely port 110) to
             *     connect to a POP3 server instead. */
            $client = new Horde_Imap_Client_Socket(array(
                'username' => $user,
                'password' => $password,
                'hostspec' => $server,
                'port' => $port,
                'secure' => 'ssl',
        
                // OPTIONAL Debugging. Will output IMAP log to the /tmp/foo file
//                 'debug' => '/tmp/foo',
        
                // OPTIONAL Caching. Will use cache files in /tmp/hordecache.
                // Requires the Horde/Cache package, an optional dependency to
                // Horde/Imap_Client.
                'cache' => array(
                    'backend' => new Horde_Imap_Client_Cache_Backend_Cache(array(
                        'cacheob' => new Horde_Cache(new Horde_Cache_Storage_File(array(
                            'dir' => '/tmp/hordecache'
                        )))
                    ))
                )
            ));
        
            $query = new Horde_Imap_Client_Fetch_Query();
            $query->structure();
            $query->envelope();
        
            $uid = new Horde_Imap_Client_Ids($_REQUEST["muid"]);
        
            $list = $client->fetch($_REQUEST["mailbox"], $query, array(
                'ids' => $uid
            ));
        
            $orig_mail_from = $list->first()->getEnvelope()->from->__toString();
            $orig_mail_subject = $list->first()->getEnvelope()->subject;
            $orig_mail_date = date("d.m.Y H:i",$list->first()->getEnvelope()->date->__toString());
            $orig_mail_to = $list->first()->getEnvelope()->to->__toString();
        
            $part = $list->first()->getStructure();

            $map = $part->ContentTypeMap();
            $mail_attachments = array();
            foreach ( $map as $key => $value ) {
                $p = $part->getPart( $key );
                $disposition = $p->getDisposition();
                if ( ! in_array( $disposition, array( 'attachment', 'inline' ) ) ) {
                    continue;
                }
                $name = $p->getName();
                $type = $p->getType();
                if ( 'inline' === $disposition && 'text/plain' === $type ) {
                    continue;
                }
                $new_attachment = array(
                    'disposition' => $disposition,
                    'type' => $p->getPrimaryType(),
                    'mimetype' => $type,
                    'mime_id' => $key,
                    'name' => $name,
                );
                $mail_attachments[] = $new_attachment;
            }

            $content = "";
            $id = $part->findBody('html');
            if ($id == NULL)
                $id = $part->findBody();
            if ($id != NULL)
            {
                $body = $part->getPart($id);

                $query2 = new Horde_Imap_Client_Fetch_Query();
                $query2->bodyPart($id, array(
                    'decode' => true,
                    'peek' => false
                ));

                $list2 = $client->fetch($_REQUEST["mailbox"], $query2, array(
                    'ids' => $uid
                ));

                $message2 = $list2->first();
                $content = $message2->getBodyPart($id);
                if (!$message2->getBodyPartDecode($id)) {
                    $body->setContents($content);
                    $content = $body->getContents();
                }

                $content = strip_tags( $content, '<img><p><br><i><b><u><em><strong><strike><font><span><div><style><a>' );
                $content = trim( $content );
                $charset = $body->getCharset();
                if ( 'iso-8859-1' === $charset ) {
                    $content = utf8_encode( $content );
                } elseif ( function_exists( 'iconv' ) ) {
                    $content = iconv( $charset, 'UTF-8', $content );
                }
            }
        
            $signatur = $_USER->getSignature();
        
            $content =  $signatur . '<br><hr>'.
                'Von: '.$orig_mail_from.'<br>
                    Gesendet: '.$orig_mail_date.'<br>
                    An: '.$orig_mail_to.'<br>
                    Betreff: '.$orig_mail_subject.'<br><br>' . $content;
            
            $ticket->setTitle("Mail: ".$orig_mail_subject);
        
        
        } catch (Horde_Imap_Client_Exception $e) {
            fatal_error('Could not connect to Server!');
        }
    }
    
}
if($_REQUEST["commentid"] && $_REQUEST["newvis"]){
    $tmp_comm = new Comment($_REQUEST["commentid"]);
    $tmp_comm->setVisability($_REQUEST["newvis"]);
    $tmp_comm->save();
}

if($_REQUEST["exec"] == "edit"){
    $ticket = new Ticket($_REQUEST["tktid"]);
    $header_title = $_LANG->get('Ticketdetails');
    
    if($_REQUEST["subexec"] == "save"){
        
        $logentry = "";
        if ($ticket->getTitle() != $_REQUEST["tkt_title"])
            $logentry .= "Titel: " . $ticket->getTitle() . " >> " . $_REQUEST["tkt_title"] . "</br>";
        if ($ticket->getDuedate() != strtotime($_REQUEST["tkt_due"]))
        {
            if ($_REQUEST["tkt_due"] != "" && $ticket->getDuedate() > 0)
                $logentry .= "Fälligkeit: " . date('d.m.Y H:i',$ticket->getDuedate()) . " >> " . date('d.m.Y H:i',strtotime($_REQUEST["tkt_due"])) . "</br>";
            elseif ($ticket->getDuedate() == 0 && $_REQUEST["tkt_due"] != "")
                $logentry .= "Fälligkeit: ohne Fälligkeit >> " . date('d.m.Y H:i',strtotime($_REQUEST["tkt_due"])) . "</br>";
            else
                $logentry .= "Fälligkeit: " . date('d.m.Y H:i',strtotime($ticket->getDuedate())) . " >> ohne Fälligkeit</br>";
        }
        $newcustomer = new BusinessContact($_REQUEST["tkt_customer_id"]);
        if ($ticket->getCustomer()->getId() != $_REQUEST["tkt_customer_id"])
            $logentry .= "Kunde: " . $ticket->getCustomer()->getNameAsLine() . " >> " . $newcustomer->getNameAsLine() . "</br>";
        $newcustomercp = new ContactPerson($_REQUEST["tkt_customer_cp_id"]);
        if ($ticket->getCustomer_cp()->getId() != $_REQUEST["tkt_customer_cp_id"])
            $logentry .= "Ansprechpartner: " . $ticket->getCustomer_cp()->getNameAsLine() . " >> " . $newcustomercp->getNameAsLine() . "</br>";
        if (substr($_REQUEST["tkt_assigned"], 0, 2) == "u_"){
            $tmp_newuser = new User((int)substr($_REQUEST["tkt_assigned"], 2));
            if ($ticket->getAssigned_user()->getId() != $tmp_newuser->getId())
                $logentry .= "Zug. MA: " . $ticket->getAssigned_user()->getNameAsLine() . " >> " . $tmp_newuser->getNameAsLine() . "</br>";
        }
        if (substr($_REQUEST["tkt_assigned"], 0, 2) == "g_"){
            $tmp_newgroup = new Group((int)substr($_REQUEST["tkt_assigned"], 2));
            if ($ticket->getAssigned_group()->getId() != $tmp_newgroup->getId())
                $logentry .= "Zug. MA: " . $ticket->getAssigned_group()->getName() . " >> " . $tmp_newgroup->getName() . "</br>";
        }
        $tmp_newcat = new TicketCategory((int)$_REQUEST["tkt_category"]);
        if ($ticket->getCategory()->getId() != $_REQUEST["tkt_category"])
            $logentry .= "Kategorie: " . $ticket->getCategory()->getTitle() . " >> " . $tmp_newcat->getTitle() . "</br>";
        $tmp_newstate = new TicketState((int)$_REQUEST["tkt_state"]);
        if ($ticket->getState()->getId() != $_REQUEST["tkt_state"])
            $logentry .= "Status: " . $ticket->getState()->getTitle() . " >> " . $tmp_newstate->getTitle() . "</br>";
        $tmp_newprio = new TicketPriority((int)$_REQUEST["tkt_prio"]);
        if ($ticket->getPriority()->getId() != $_REQUEST["tkt_prio"])
            $logentry .= "Priorität: " . $ticket->getPriority()->getTitle() . " >> " . $tmp_newprio->getTitle() . "</br>";
        if ($ticket->getPlanned_time() != tofloat($_REQUEST["tkt_planned_time"]))
            $logentry .= "Gepl. Zeit: " . printPrice($ticket->getPlanned_time(),2) . " >> " . printPrice(tofloat($_REQUEST["tkt_planned_time"]),2) . "</br>";
        if ($_REQUEST["tkt_crtusr"])
        {
            $tmp_newcrtusr = new User($_REQUEST["tkt_crtusr"]);
            if ($ticket->getCrtuser()->getId() != $tmp_newcrtusr->getId())
                $logentry .= "Tkt-Ersteller: " . $ticket->getCrtuser()->getNameAsLine() . " >> " . $tmp_newcrtusr->getNameAsLine() . "</br>";
        }
        
        $ticket->setTitle($_REQUEST["tkt_title"]);
        if ($_REQUEST["tkt_due"] != "" && $_REQUEST["tkt_due"] != 0){
            if ($ticket->getDuedate() != strtotime($_REQUEST["tkt_due"]))
            {
                $myjob = PlanningJob::getJobForTicket($ticket->getId());
                if ($myjob->getId()>0)
                {
                    $myjob->setStart(strtotime($_REQUEST["tkt_due"]));
                    $myjob->save();
                }
            }
            $ticket->setDuedate(strtotime($_REQUEST["tkt_due"]));
        } else {
            $ticket->setDuedate(0);
        }
        if ($_REQUEST["tkt_crtusr"])
            $ticket->setCrtuser(new User($_REQUEST["tkt_crtusr"]));
        $ticket->setCustomer(new BusinessContact($_REQUEST["tkt_customer_id"]));
        $ticket->setCustomer_cp(new ContactPerson($_REQUEST["tkt_customer_cp_id"]));
        $assigned = "";
        if (substr($_REQUEST["tkt_assigned"], 0, 2) == "u_"){
            $tmp_newuser = new User((int)substr($_REQUEST["tkt_assigned"], 2));
            if ($ticket->getAssigned_user()->getId() != $tmp_newuser->getId())
            {
                $ticket->setAssigned_group(new Group(0));
                $ticket->setAssigned_user(new User((int)substr($_REQUEST["tkt_assigned"], 2)));
                $new_assiged = "Benutzer <b>" . $ticket->getAssigned_user()->getNameAsLine() . "</b>";
                $assigned = "user";
            }
        } elseif (substr($_REQUEST["tkt_assigned"], 0, 2) == "g_") {
            $tmp_newgroup = new Group((int)substr($_REQUEST["tkt_assigned"], 2));
            if ($ticket->getAssigned_group()->getId() != $tmp_newgroup->getId())
            {
                $ticket->setAssigned_group(new Group((int)substr($_REQUEST["tkt_assigned"], 2)));
                $ticket->setAssigned_user(new User(0));
                $new_assiged = "Gruppe <b>" . $ticket->getAssigned_group()->getName() . "</b>";
                $assigned = "group";
            }
        }
        $ticket->setCategory(new TicketCategory((int)$_REQUEST["tkt_category"]));
        if ($ticket->getCategory()->getId() == 1){
            $ticket->setState(new TicketState(3));
        } else {
            $ticket->setState(new TicketState((int)$_REQUEST["tkt_state"]));
        }
        if ($ticket->getState()->getId()==3)
        {
    	    $ticket->setClosedate(time());
    	    $ticket->setCloseuser($_USER);
        }
        $ticket->setPriority(new TicketPriority((int)$_REQUEST["tkt_prio"]));
        $ticket->setSource((int)$_REQUEST["tkt_source"]);
        if ($ticket->getId() > 0){
            $ticket->setEditdate(time());
        }
        $ticket->setPlanned_time(tofloat($_REQUEST["tkt_planned_time"]));
        $save_ok = $ticket->save();
        if ($save_ok){
            if (!$_REQUEST["tktid"]){
                if (!Abonnement::hasAbo($ticket,$_USER)){
                    $abo = new Abonnement();
                    $abo->setAbouser($_USER);
                    $abo->setModule(get_class($ticket));
                    $abo->setObjectid($ticket->getId());
                    $abo->save();
                    unset($abo);
                }
            }
            if ($assigned == "group")
            {
                foreach ($ticket->getAssigned_group()->getMembers() as $grmem){
                    if (!Abonnement::hasAbo($ticket,$grmem)){
                        $abo = new Abonnement();
                        $abo->setAbouser($grmem);
                        $abo->setModule(get_class($ticket));
                        $abo->setObjectid($ticket->getId());
                        $abo->save();
                        unset($abo);
                    }
                    if ($grmem->getId() != $_USER->getId()){
                        Notification::generateNotification($grmem, get_class($ticket), "AssignGroup", $ticket->getNumber(), $ticket->getId(), $ticket->getAssigned_group()->getName());
                        $logentry .= 'Benachrichtigung generiert für User: ' . $grmem->getNameAsLine() . '</br>';
                    }
                }
            } else if ($assigned == "user")
            {
                if (!Abonnement::hasAbo($ticket,$ticket->getAssigned_user())){
                    $abo = new Abonnement();
                    $abo->setAbouser($ticket->getAssigned_user());
                    $abo->setModule(get_class($ticket));
                    $abo->setObjectid($ticket->getId());
                    $abo->save();
                    unset($abo);
                }
                if ($ticket->getAssigned_user()->getId() != $_USER->getId()){
                    Notification::generateNotification($ticket->getAssigned_user(), get_class($ticket), "Assign", $ticket->getNumber(), $ticket->getId());
                    $logentry .= 'Benachrichtigung generiert für User: ' . $ticket->getAssigned_user()->getNameAsLine() . '</br>';
                }
            }
        }
        $savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
        if ($save_ok){
            if ($_REQUEST["tktc_comment"] != ""){
                $ticketcomment = new Comment();
                $ticketcomment->setComment($_REQUEST["tktc_comment"]);
                if ($ticket->getId() <= 0){
                    $ticketcomment->setTitle("Ticket wurde erstellt");
                } elseif ($_REQUEST["tkt_assigned"] != "0" && ($assigned == "group" || $assigned == "user")){
                    $ticketcomment->setTitle("Ticket ".$new_assiged." zugewiesen");
                }
                $ticketcomment->setCrtuser($_USER);
                $ticketcomment->setCrtdate(time());
                $ticketcomment->setState(1);
                $ticketcomment->setModule("Ticket");
                $ticketcomment->setObjectid($ticket->getId());
                $ticketcomment->setVisability((int)$_REQUEST["tktc_type"]);
                $save_ok = $ticketcomment->save();
                $savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
                if ($save_ok){
                    $logentry .= '(#'.$ticketcomment->getId().') <a href="#comment_'.$ticketcomment->getId().'">Neues Kommentar </a> von ' . $ticketcomment->getCrtuser()->getNameAsLine() . '</br>';

                    $tmp_array = $_REQUEST["abo_notify"];
                    foreach ($tmp_array as $abouser){
                        $tmp_user = new User($abouser);
                        if (!Abonnement::hasAbo($ticket,$tmp_user)){
                            $abo = new Abonnement();
                            $abo->setAbouser($tmp_user);
                            $abo->setModule(get_class($ticket));
                            $abo->setObjectid($ticket->getId());
                            $abo->save();
                            $logentry .= 'Abonnement hinzugefügt: ' . $tmp_user->getNameAsLine() . '</br>';
                        }
                        if ($tmp_user->getId() != $_USER->getId())
                        {
                            Notification::generateNotification($tmp_user, get_class($ticket), "Comment", $ticket->getNumber(), $ticket->getId());
                            $logentry .= 'Benachrichtigung generiert für User: ' . $tmp_user->getNameAsLine() . '</br>';
                        }
                    }
//                     Notification::generateNotificationsFromAbo(get_class($ticket), "Comment", $ticket->getNumber(), $ticket->getId());

                    if ($ticketcomment->getVisability() == Comment::VISABILITY_PUBLICMAIL)
                    {
                        $mailer = new Horde_Mail_Transport_Mail();
                        $mail = new Horde_Mime_Mail();
                        $mail->addHeader('Date', date('r'));
                        $mail->addHeader('From', $_USER->getEmail());
                        $mail_subject = "Ticket #".$ticket->getNumber()." - ".$ticket->getTitle();
                        $mail->addHeader('Subject', $mail_subject);
                        $mail_text = "Sehr geehrte(r) ".$ticket->getCustomer_cp()->getTitle()." ".$ticket->getCustomer_cp()->getName1().",<br>
                                      <br>
                                      Eine neue Nachricht im Ticket #".$ticket->getNumber()." - ".$ticket->getTitle()." wurde verfasst.<br>
                                      <br><br>------------------"
                                      .$ticketcomment->getComment().
                                      "<br>------------------<br><br>".$_USER->getSignature();
                        $mail->setHtmlBody($mail_text);
                        $mail->addRecipients($ticket->getCustomer_cp()->getEmail());
                        $mail->send($mailer);
                        $logentry .= "Kommentar wurde per eMail an Kunden geschickt</br>";
                    }
                }
                if ($save_ok && $_REQUEST["tktc_article_id"] != "" && $_REQUEST["tktc_article_amount"] != ""){
                    $tc_article = new CommentArticle();
                    $tc_article->setArticle(new Article($_REQUEST["tktc_article_id"]));
                    $tc_article->setAmount((float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["tktc_article_amount"]))));
                    $tc_article->setState(1);
                    $tc_article->setComment_id($ticketcomment->getId());
                    $save_ok = $tc_article->save();
                    $savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
                    if ($save_ok){
                        $ticketcomment->setArticles(Array($tc_article));
                    }
                }
                if ($save_ok && $_REQUEST["stop_timer"] == 1){ //  && $_REQUEST["ticket_timer_timestamp"]
                    $timer = Timer::getLastUsed();
                    if ($timer->getState() == Timer::TIMER_RUNNING){
                        $timer->stop();
                        $time_ok = true;
                    }
                    unset($timer);
                }
                $save_ok = $ticketcomment->save();
                $savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
                if ($save_ok) {
                    if ($_REQUEST["tktc_files"]) {
                        foreach ($_REQUEST["tktc_files"] as $file) {
                            if ($file != ""){
                                $tmp_attachment = new Attachment();
                                $tmp_attachment->setCrtdate(time());
                                $tmp_attachment->setCrtuser($_USER);
                                $tmp_attachment->setModule("Comment");
                                $tmp_attachment->setObjectid($ticketcomment->getId());
                                $tmp_attachment->move_uploaded_file($file);
                                $save_ok = $tmp_attachment->save();
                                $savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
                                if ($save_ok === false){
                                    break;
                                }
                            }
                        }
                    }
                }

                if ($save_ok &&
                    $_REQUEST["mail_fetch_attach_mailid"] &&
                    $_REQUEST["mail_fetch_attach_muid"] &&
                    $_REQUEST["mail_fetch_attach_mailbox"]) {
                    $mailadress = new Emailaddress($_REQUEST["mail_fetch_attach_mailid"]);

                    $server = $mailadress->getHost();
                    $port = $mailadress->getPort();
                    $user = $mailadress->getLogin();
                    $password = $mailadress->getPassword();

                    try {
                        $client = new Horde_Imap_Client_Socket(array(
                            'username' => $user,
                            'password' => $password,
                            'hostspec' => $server,
                            'port' => $port,
                            'secure' => 'ssl',
                            'cache' => array(
                                'backend' => new Horde_Imap_Client_Cache_Backend_Cache(array(
                                    'cacheob' => new Horde_Cache(new Horde_Cache_Storage_File(array(
                                        'dir' => '/tmp/hordecache'
                                    )))
                                ))
                            )
                        ));

                        $query = new Horde_Imap_Client_Fetch_Query();
                        $query->structure();
                        $query->envelope();

                        $uid = new Horde_Imap_Client_Ids($_REQUEST["mail_fetch_attach_muid"]);

                        $list = $client->fetch($_REQUEST["mail_fetch_attach_mailbox"], $query, array(
                            'ids' => $uid
                        ));

                        $orig_mail_from = $list->first()->getEnvelope()->from->__toString();
                        $orig_mail_subject = $list->first()->getEnvelope()->subject;
                        $orig_mail_date = date("d.m.Y H:i", $list->first()->getEnvelope()->date->__toString());
                        $orig_mail_to = $list->first()->getEnvelope()->to->__toString();

                        $part = $list->first()->getStructure();

                        $map = $part->ContentTypeMap();
                        $attachments = array();
                        foreach ($map as $key => $value) {
                            $p = $part->getPart($key);
                            $disposition = $p->getDisposition();
                            if (!in_array($disposition, array('attachment', 'inline'))) {
                                continue;
                            }
                            $name = $p->getName();
                            $type = $p->getType();
                            if ('inline' === $disposition && 'text/plain' === $type) {
                                continue;
                            }
                            $new_attachment = array(
                                'disposition' => $disposition,
                                'type' => $p->getPrimaryType(),
                                'mimetype' => $type,
                                'mime_id' => $key,
                                'name' => $name,
                            );
                            $attachments[] = $new_attachment;
                        }

                        if (count($attachments)>0){
                            foreach ($attachments as $attachment) {

                                $uid = new Horde_Imap_Client_Ids( $_REQUEST["mail_fetch_attach_muid"] );
                                $mime_id = $attachment["mime_id"];

                                $query = new Horde_Imap_Client_Fetch_Query();
                                $query->bodyPart( $mime_id, array(
                                        'decode' => true,
                                        'peek' => true,
                                    )
                                );
                                $list = $client->fetch( $_REQUEST["mail_fetch_attach_mailbox"], $query, array(
                                        'ids' => $uid,
                                    )
                                );
                                $message = $list->first();

                                $image_data = $message->getBodyPart( $mime_id );
                                $image_data_decoded = base64_decode( $image_data );

                                $name = $attachment["name"];

                                $destination = __DIR__."/../../../docs/attachments/";

                                $filename = md5($attachment["name"].time());
                                $new_filename = $destination.$filename;

                                if(!file_exists($new_filename)) {
                                    $fh = fopen($new_filename, "w");
                                    fwrite($fh, $image_data_decoded);
                                    fclose($fh);
                                }

                                $tmp_attachment = new Attachment();
                                $tmp_attachment->setCrtdate(time());
                                $tmp_attachment->setCrtuser($_USER);
                                $tmp_attachment->setModule("Comment");
                                $tmp_attachment->setObjectid($ticketcomment->getId());
                                $tmp_attachment->setFilename($filename);
                                $tmp_attachment->setOrig_filename($attachment["name"]);
                                $save_ok = $tmp_attachment->save();
                                $savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
                                if ($save_ok === false){
                                    break;
                                }
                            }
                        }

                    } catch (Horde_Imap_Client_Exception $e) {
                        fatal_error('Could not connect to Server!');
                    }
                }
            } else {
                $tmp_array = $_REQUEST["abo_notify"];
                foreach ($tmp_array as $abouser){
                    $tmp_user = new User($abouser);
                    if (!Abonnement::hasAbo($ticket,$tmp_user)){
                        $abo = new Abonnement();
                        $abo->setAbouser($tmp_user);
                        $abo->setModule(get_class($ticket));
                        $abo->setObjectid($ticket->getId());
                        $abo->save();
                        $logentry .= 'Abonnement hinzugefügt: ' . $tmp_user->getNameAsLine() . '</br>';
                    }
                    if ($tmp_user->getId() != $_USER->getId())
                    {
                        Notification::generateNotification($tmp_user, get_class($ticket), "Change", $ticket->getNumber(), $ticket->getId());
                        $logentry .= 'Benachrichtigung generiert für User: ' . $tmp_user->getNameAsLine() . '</br>';
                    }
                }
//                $abos = Abonnement::getAbonnementsForObject(get_class($ticket), $ticket->getId());
//                foreach ($abos as $abo) {
//                    if ($abo->getAbouser()->getId() != $_USER->getId())
//                        $logentry .= 'Benachrichtigung generiert für User: ' . $abo->getAbouser()->getNameAsLine() . '</br>';
//                }
//                Notification::generateNotificationsFromAbo(get_class($ticket), "Change", $ticket->getNumber(), $ticket->getId());
            }
            if ($logentry != "")
            {
                $ticketlog = new TicketLog();
                $ticketlog->setCrtusr($_USER);
                $ticketlog->setDate(time());
                $ticketlog->setTicket($ticket);
                $ticketlog->setEntry($logentry);
                $ticketlog->save();
            }
        }
    }
    if ($_REQUEST["asso_class"] && $_REQUEST["asso_object"])
    {
        $new_asso = new Association();
        $new_asso->setModule1(get_class($ticket));
        $new_asso->setObjectid1((int)$ticket->getId());
        $new_asso->setModule2($_REQUEST["asso_class"]);
        $new_asso->setObjectid2((int)$_REQUEST["asso_object"]);
        $new_asso->save();
    }
}

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>

<script src="thirdparty/ckeditor/ckeditor.js"></script>
<script src="jscripts/jvalidation/dist/jquery.validate.min.js"></script>
<script src="jscripts/jvalidation/dist/localization/messages_de.min.js"></script>
<script src="jscripts/moment/moment-with-locales.min.js"></script>

<!-- file upload -->
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="css/jquery.fileupload.css">
<script src="jscripts/jquery/js/jquery.ui.widget.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="jscripts/jquery/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="jscripts/jquery/js/jquery.fileupload.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
<!-- // file upload -->

<script>
/*jslint unparam: true */
/*global window, $ */
$(function () {
    'use strict';
    // Change this to the location of your server-side upload handler:
    var url = 'libs/modules/attachment/attachment.handler.php';
    $('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data) {
            $.each(data.result.files, function (index, file) {
                $('<p/>').text(file.name).appendTo('#files');
                $('#files').append('<input name="tktc_files[]" type="hidden" value="'+file.name+'"/>');
            });
        },
        progressall: function (e, data) {
            var progress = parseInt(data.loaded / data.total * 100, 10);
            $('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
        }
    }).prop('disabled', !$.support.fileInput)
        .parent().addClass($.support.fileInput ? undefined : 'disabled');
});
</script>

<script language="JavaScript">
	$(function() {
		var editor = CKEDITOR.replace( 'tktc_comment', {
			// Define the toolbar groups as it is a more accessible solution.
			toolbarGroups: [
                { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
                { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
                { name: 'links' },
                { name: 'insert' },
                { name: 'tools' },
                { name: 'others' },
                '/',
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
                { name: 'styles' },
                { name: 'colors' }
			]
			// Remove the redundant buttons from toolbar groups defined above.
			//removeButtons: 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar'
		} );
		var comment = $('#msg_content').html();
		CKEDITOR.instances.tktc_comment.setData( comment, function()
				{
				    this.checkDirty();  // true
				});
	} );
</script>
<script language="JavaScript">
$(function() {
	$('#tkt_due').datetimepicker({
		 lang:'de',
		 i18n:{
		  de:{
		   months:[
		    'Januar','Februar','März','April',
		    'Mai','Juni','Juli','August',
		    'September','Oktober','November','Dezember',
		   ],
		   dayOfWeek:[
		    "So.", "Mo", "Di", "Mi", 
		    "Do", "Fr", "Sa.",
		   ]
		  }
		 },
		 timepicker:true,
		 format:'d.m.Y H:i'
	});
	
	 $( "#tkt_customer" ).autocomplete({
		 source: "libs/modules/tickets/ticket.ajax.php?ajax_action=search_customer_and_cp",
		 minLength: 2,
		 focus: function( event, ui ) {
		 $( "#tkt_customer" ).val( ui.item.label );
		 return false;
		 },
		 select: function( event, ui ) {
    		 $( "#tkt_customer" ).val( ui.item.label );
    		 $( "#tkt_customer_id" ).val( ui.item.bid );
    		 $( "#tkt_customer_cp_id" ).val( ui.item.cid );
    		 $( "#tkt_tourmarker" ).html( ui.item.tourmarker );
    		 return false;
    	 }
	 });
	
	 $( "#tktc_article" ).autocomplete({
		 source: "libs/modules/tickets/ticket.ajax.php?ajax_action=search_article",
		 minLength: 2,
		 focus: function( event, ui ) {
		 $( "#tktc_article" ).val( ui.item.label );
		 return false;
		 },
		 select: function( event, ui ) {
			 CKEDITOR.instances.tktc_comment.focus();
    		 $( "#tktc_article" ).val( ui.item.label );
    		 $( "#tktc_article_id" ).val( ui.item.value );
    		 if ($("#tktc_article_amount").val() == ""){
    			 $( "#tktc_article_amount" ).val("1");
    		 }
             if (<?php echo $perf->getCommentArtDesc();?>==1){
                 $.ajax({
                     type: "POST",
                     url: "libs/modules/article/article.ajax.php",
                     data: { ajax_action: "getDescription", id: ui.item.value },
                     success: function(data)
                     {
                         CKEDITOR.instances.tktc_comment.setData( data, function()
                         {
                             this.checkDirty();  // true
                         });
                     }
                 });
             }
    		 return false;
		 }
	 });
	
	 $( "#tktc_new_notify_user" ).autocomplete({
		 source: "libs/modules/tickets/ticket.ajax.php?ajax_action=search_user",
		 minLength: 2,
		 focus: function( event, ui ) {
		 $( "#tktc_new_notify_user" ).val( ui.item.label );
		 return false;
		 },
		 select: function( event, ui ) {
    		 $( "#tktc_new_notify_user" ).val( ui.item.label );
    		 $( "#abo_notify" )
    		 $('#abo_notify').append($('<option>', {
                value: ui.item.value,
                text: ui.item.label,
                selected: true
             }));
    		 $( "#tktc_new_notify_user" ).val("");
    		 return false;
		 }
	 });
});
</script>
<script language="JavaScript">
function Abo_Refresh()
{
	$.ajax({
		type: "POST",
		url: "libs/modules/abonnements/abonnement.ajax.php",
		data: { exec: "abo_getcount", module: "<?php echo get_class($ticket);?>", objectid: "<?php echo $ticket->getId();?>" },
		success: function(data) 
		    {
			 if (parseInt(data) > 0)
				 $("#abo_count").html(data);
		    }
	});
}
$(document).ready(function () {
    $('#ticket_edit').validate({
        rules: {
            'tktc_comment': {
                required: false
            },
            'tktc_article_id': {
            	required: "#stop_timer:checked"
            },
            'tktc_article': {
            	required: "#stop_timer:checked"
            }
        },
        errorPlacement: function(error, $elem) {
            if ($elem.is('textarea')) {
                $elem.next().css('border', '1px solid red');
            }
        },
        ignore: []
    });
    CKEDITOR.on('instanceReady', function () {
        $.each(CKEDITOR.instances, function (instance) {
            CKEDITOR.instances[instance].document.on("keyup", CK_jQ);
            CKEDITOR.instances[instance].document.on("paste", CK_jQ);
            CKEDITOR.instances[instance].document.on("keypress", CK_jQ);
            CKEDITOR.instances[instance].document.on("blur", CK_jQ);
            CKEDITOR.instances[instance].document.on("change", CK_jQ);
        });
    });

    function CK_jQ() {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
    }

	$("a#hiddenclicker_tkcframe").fancybox({
		'type'          :   'iframe',
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'width'         :   1024,
		'height'		:	768, 
		'overlayShow'	:	true,
		'helpers'		:   { overlay:null, closeClick:true }
	});
	
	$("a#tktc_hiddenclicker").fancybox({
		'type'          :   'iframe',
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'width'         :   1024,
		'height'		:	768, 
		'overlayShow'	:	true,
		'helpers'		:   { overlay:null, closeClick:true }
	});

	$("a#abo_hiddenclicker").fancybox({
		'type'          :   'iframe',
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'width'         :   600,
		'height'		:	800, 
		'overlayShow'	:	true,
		'helpers'		:   { overlay:null, closeClick:true },
		'onClosed'      : function() {
			Abo_Refresh();
	        return;
	    }
	});
});
function showSummary()
{
	newwindow = window.open('libs/modules/tickets/ticket.summary.php?tktid=<?=$ticket->getId()?>', "_blank", "width=1000,height=800,left=0,top=0,scrollbars=yes");
	newwindow = focus();
}
function showSummaryExt()
{
	newwindow = window.open('libs/modules/tickets/ticket.summary.external.php?tktid=<?=$ticket->getId()?>', "_blank", "width=1000,height=800,left=0,top=0,scrollbars=yes");
	newwindow = focus();
}
function callBoxFancytktc(my_href) {
	var j1 = document.getElementById("tktc_hiddenclicker");
	j1.href = my_href;
	$('#tktc_hiddenclicker').trigger('click');
}
function callBoxFancyAbo(my_href) {
	var j1 = document.getElementById("abo_hiddenclicker");
	j1.href = my_href;
	$('#abo_hiddenclicker').trigger('click');
}
</script>

<script>
	$(document).ready(function () {
		$( "#abo_remove" ).click(function() {
			var r = confirm("Möchten Sie das Abo wirklich abbestellen?");
			if (r == true) {
    			$.ajax({
    				type: "POST",
    				url: "libs/modules/abonnements/abonnement.ajax.php",
    				data: { exec: "abo_remove", module: "<?php echo get_class($ticket);?>", objectid: "<?php echo $ticket->getId();?>", userid: "<?php echo $_USER->getId();?>" }
    			})
    			.done(function( msg ) {
    			    $( "#abo_remove" ).toggle();
    			    $( "#abo_add" ).toggle();
    			    Abo_Refresh();
    			});
			}
		});
		$( "#abo_add" ).click(function() {
			$.ajax({
				type: "POST",
				url: "libs/modules/abonnements/abonnement.ajax.php",
				data: { exec: "abo_add", module: "<?php echo get_class($ticket);?>", objectid: "<?php echo $ticket->getId();?>" }
			})
			.done(function( msg ) {
			    $( "#abo_remove" ).toggle();
			    $( "#abo_add" ).toggle();
			    Abo_Refresh();
			});
		});
	});
</script>
<script>
	$(function() {
		$("a#association_hiddenclicker").fancybox({
			'type'    : 'iframe',
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600, 
			'speedOut'		:	200,
            'width'         :  1024,
			'height'		:	768,
			'overlayShow'	:	true,
			'helpers'		:   { overlay:null, closeClick:true }
		});
	});
	function callBoxFancyAsso(my_href) {
		var j1 = document.getElementById("association_hiddenclicker");
		j1.href = my_href;
		$('#association_hiddenclicker').trigger('click');
	}
</script>


<script>
    $(function() {
        $("a#hiddenclicker_preview").fancybox({
            'type'          :   'iframe',
            'transitionIn'	:	'elastic',
            'transitionOut'	:	'elastic',
            'speedIn'		:	600,
            'speedOut'		:	200,
            'width'         :   1024,
            'height'		:	768,
            'scrolling'     :   'yes',
            'overlayShow'	:	true,
            'helpers'		:   { overlay:null, closeClick:true }
        });
    });
    function callBoxFancyPreview(my_href) {
        var j1 = document.getElementById("hiddenclicker_preview");
        j1.href = my_href;
        $('#hiddenclicker_preview').trigger('click');
    }
    function createFromSelectedColInv(){
        var elements = [];
        $( ".comment-select" ).each(function( index ) {
            if ($(this).is(':checked')){
                elements.push($(this).val());
            }
        });
        if (elements.length > 0) {
            elements = elements.join(",");
            askDel('index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=createFromTicketComments&tktcids=' + elements + '&tktid=<?= $ticket->getId() ?>');
        } else {
            alert("Bitte mindestens ein Kommentar auswählen!");
        }
    }
</script>
<div id="hidden_clicker_preview" style="display:none"><a id="hiddenclicker_preview" href="http://www.google.com" >Hidden Clicker</a></div>

<body>

<script type="text/javascript" src="jscripts/jquery.easing.1.3.js"></script>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Ticketdetails','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Kommentare','#ticket_comments',null,'glyphicon-comment');
$quickmove->addItem('Log','#ticket_logs',null,'glyphicon-book');
if ($_REQUEST["returnhome"] == 1){
    $quickmove->addItem('Zurück','index.php',null,'glyphicon-step-backward');
} else {
    $quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
}
$quickmove->addItem('Aktualisieren','#',"window.location='index.php?page=".$_REQUEST['page']."&exec=edit&tktid=".$ticket->getId()."';",'glyphicon-refresh');
$quickmove->addItem('Speichern','#',"$('#ticket_edit').submit();",'glyphicon-floppy-disk');

if ($_USER->getId() == $ticket->getCrtuser()->getId() || $_USER->getId() == $ticket->getAssigned_user()->getId() || $_USER->isAdmin()){
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&tktid=".$ticket->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<div id="tktc_hidden_clicker" style="display:none"><a id="tktc_hiddenclicker" href="http://www.google.com" >Hidden Clicker</a></div>
<div id="abo_hidden_clicker" style="display:none"><a id="abo_hiddenclicker" href="http://www.google.com" >Hidden Clicker</a></div>
<div id="association_hidden_clicker" style="display:none"><a id="association_hiddenclicker" href="http://www.google.com" >Hidden Clicker</a></div>
<div id="msg_content" style="display: none;"><?php echo $content;?></div>

<?php if ($savemsg != null && $savemsg != ''){?>
<div class="row">
    <div class="col-md-12">
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?php echo $savemsg; ?>
        </div>
    </div>
</div>
<?php } ?>

<div class="ticket_view">
    <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="ticket_edit" id="ticket_edit" class="form-horizontal" enctype="multipart/form-data">
        <input type="hidden" name="exec" value="edit">
        <input type="hidden" name="subexec" value="save">
        <input type="hidden" name="tktid" value="<?=$ticket->getId()?>">
        <input type="hidden" name="returnhome" value="<?=$_REQUEST["returnhome"]?>">
        <input type="hidden" name="asso_class" value="<?php echo $_REQUEST["asso_class"];?>">
        <input type="hidden" name="asso_object" value="<?php echo $_REQUEST["asso_object"];?>">
        <?php
        if ($_REQUEST["frommail"] == true)
        {
            if (count($mail_attachments)>0)
            {
                echo '<input type="hidden" name="mail_fetch_attach_mailid" value="'.$_REQUEST["mailid"].'">';
                echo '<input type="hidden" name="mail_fetch_attach_muid" value="'.$_REQUEST["muid"].'">';
                echo '<input type="hidden" name="mail_fetch_attach_mailbox" value="'.$_REQUEST["mailbox"].'">';
            }
        }
        ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <?= $header_title ?>
                    <?php if ($ticket->getId() > 0) { ?>
                        <span style="color:#777;">#<?php echo $ticket->getNumber(); ?>
                            - <?php echo $ticket->getTitle(); ?></span>
                    <?php } ?>
                    <span class="pull-right">
                    <?php if ($ticket->getId() > 0) { ?>
                        <div class="btn-group" role="group">
                            <button type="button"
                                    onclick="window.location='index.php?page=<?= $_REQUEST['page'] ?>&exec=edit&tktid=<?= $ticket->getId() ?>';"
                                    class="btn btn-xs btn-default">Refresh
                            </button>
                            <div class="btn-group dropdown">
                                <button type="button" class="btn btn-xs dropdown-toggle btn-default"
                                        data-toggle="dropdown" aria-expanded="false">
                                    Summary <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="#" onclick="showSummary();">Interne Summary</a>
                                        <a href="#" onclick="showSummaryExt();">Öffentliche Summary</a>
                                    </li>
                                </ul>
                            </div>
                            <?php
                            $association_object = $ticket;
                            $associations = Association::getAssociationsForObject(get_class($association_object), $association_object->getId(), true);
                            ?>
                            <script type="text/javascript">
                                function removeAsso(id) {
                                    $.ajax({
                                        type: "POST",
                                        url: "libs/modules/associations/association.ajax.php",
                                        data: {ajax_action: "delete_asso", id: id}
                                    })
                                }
                            </script>
                            <div class="btn-group dropdown">
                                <button type="button" class="btn btn-xs dropdown-toggle btn-default"
                                        data-toggle="dropdown" aria-expanded="false">
                                    Verkn. u. Status <span class="badge"><?php echo count($associations); ?></span>
                                    <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <?php
                                    if (count($associations) > 0) {
                                        $as = 0;
                                        foreach ($associations as $association) {
                                            if ($association->getModule1() == get_class($association_object) && $association->getObjectid1() == $association_object->getId()) {
                                                $classname = $association->getModule2();
                                                $object = new $classname($association->getObjectid2());
                                                $link_href = Association::getPath($classname);
                                                $object_name = Association::getName($object);
                                            } else {
                                                $classname = $association->getModule1();
                                                $object = new $classname($association->getObjectid1());
                                                $link_href = Association::getPath($classname);
                                                $object_name = Association::getName($object);
                                            }
                                            echo '<li id="as_' . $as . '"><a href="index.php?page=' . $link_href . $object->getId() . '">';
                                            echo $object_name;
                                            echo '</a>';
                                            if ($_USER->isAdmin() || $_USER->hasRightsByGroup(Group::RIGHT_ASSO_DELETE))
                                                echo '<span class="glyphicons glyphicons-remove pointer" onclick=\'removeAsso(' . $association->getId() . '); $("#as_' . $as . '").remove();\'/></span>';
                                            echo '</li>';
                                            $as++;
                                        }
                                    }
                                    ?>
                                </ul>
                            </div>
                            <?php
                            if (Abonnement::hasAbo($ticket)) {
                                $abonnoments = Abonnement::getAbonnementsForObject(get_class($ticket), $ticket->getId());

                                $abo_title = "";
                                if (count($abonnoments) > 0) {
                                    foreach ($abonnoments as $abonnoment) {
                                        $abo_title .= $abonnoment->getAbouser()->getNameAsLine() . "\n";
                                    }
                                }
                            }
                            ?>
                            <button type="button"
                                <?php if ($_USER->getId() == $ticket->getCrtuser()->getId() || $_USER->getId() == $ticket->getAssigned_user()->getId() || $_USER->isAdmin()) { ?>
                                    onclick="callBoxFancyAbo('libs/modules/abonnements/abonnement.add.frame.php?module=<?php echo get_class($ticket); ?>&objectid=<?php echo $ticket->getId(); ?>');"
                                <?php } ?>
                                    class="btn btn-xs btn-default" title="<?php echo $abo_title; ?>">
                                Abonnoments&nbsp;<span id="abo_count"
                                                       class="badge"><?php if (count($abonnoments) > 0) echo count($abonnoments); ?></span>
                            </button>

                            <div class="btn-group dropdown" style="margin-left: 0px;">
                                <button type="button" class="btn btn-xs dropdown-toggle btn-default"
                                        data-toggle="dropdown" aria-expanded="false">
                                    Neu <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li>
                                        <a href="#"
                                           onclick="callBoxFancyAsso('libs/modules/associations/association.frame.php?module=<?php echo get_class($association_object); ?>&objectid=<?php echo $association_object->getId(); ?>');">Verknüpfung</a>
                                        <a href="#"
                                           onclick="askDel('index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=createFromTicket&tktid=<?= $ticket->getId() ?>');">Ticket
                                            Vorgang</a>
                                        <a href="#"
                                           onclick="createFromSelectedColInv();">Ticket
                                            Vorgang (nur ausgewählte)</a>
                                        <a href="#"
                                           onclick="askDel('index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&order_customer=<?php echo $ticket->getCustomer()->getId(); ?>&order_contactperson=<?php echo $ticket->getCustomer_cp()->getId(); ?>&order_title=<?php echo $ticket->getTitle(); ?>');">Vorgang
                                            erstellen</a>
                                        <a href="#"
                                           onclick="askDel('index.php?page=libs/modules/calculation/order.php&exec=edit&createNew=1&order_customer=<?php echo $ticket->getCustomer()->getId(); ?>&order_contactperson=<?php echo $ticket->getCustomer_cp()->getId(); ?>&order_title=<?php echo $ticket->getTitle(); ?>');">Kalkulation
                                            erstellen</a>
                                        <a href="#"
                                           onclick="askDel('index.php?page=libs/modules/tickets/ticket.php&exec=new&customer=<?php echo $ticket->getCustomer()->getId(); ?>&contactperson=<?php echo $ticket->getCustomer_cp()->getId(); ?>');">Ticket
                                            erstellen</a>
                                        <a href="#"
                                           onclick="askDel('index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&order_customer=<?php echo $ticket->getCustomer()->getId(); ?>&order_contactperson=<?php echo $ticket->getCustomer_cp()->getId(); ?>&order_title=<?php echo $ticket->getTitle(); ?>&asso_class=<?php echo get_class($ticket); ?>&asso_object=<?php echo $ticket->getId() ?>');">Vorgang
                                            erstellen (verknüpft)</a>
                                        <a href="#"
                                           onclick="askDel('index.php?page=libs/modules/calculation/order.php&exec=edit&createNew=1&order_customer=<?php echo $ticket->getCustomer()->getId(); ?>&order_contactperson=<?php echo $ticket->getCustomer_cp()->getId(); ?>&order_title=<?php echo $ticket->getTitle(); ?>&asso_class=<?php echo get_class($ticket); ?>&asso_object=<?php echo $ticket->getId() ?>');">Kalkulation
                                            erstellen (verknüpft)</a>
                                        <a href="#"
                                           onclick="askDel('index.php?page=libs/modules/tickets/ticket.php&exec=new&customer=<?php echo $ticket->getCustomer()->getId(); ?>&contactperson=<?php echo $ticket->getCustomer_cp()->getId(); ?>&asso_class=<?php echo get_class($ticket); ?>&asso_object=<?php echo $ticket->getId() ?>');">Ticket
                                            erstellen (verknüpft)</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    <?php } ?>
                </span>
                </h3>
            </div>
            <div class="panel-body">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Kopfdaten</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row ticket_header">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tkt_title" class="col-sm-2 control-label">Titel</label>
                                    <div class="col-sm-10">
                                        <?php
                                        if ($ticket->getId() == 0 && $_REQUEST["tkt_title"]) {
                                            ?>
                                            <input type="text" id="tkt_title" name="tkt_title"
                                                   value="<?php echo $_REQUEST["tkt_title"]; ?>" class="form-control"
                                                   required/>
                                            <?php
                                        } else {
                                            ?>
                                            <input type="text" id="tkt_title" name="tkt_title"
                                                   value="<?php echo $ticket->getTitle(); ?>" class="form-control"
                                                   required/>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Kategorie</label>
                                    <div class="col-sm-10">
                                        <select name="tkt_category" id="tkt_category" class="form-control" required>
                                            <?php
                                            $tkt_all_categories = TicketCategory::getAllCategories();
                                            foreach ($tkt_all_categories as $tkt_category) {
                                                if ($ticket->getId() > 0) {
                                                    if ($ticket->getCategory() == $tkt_category) {
                                                        echo '<option value="' . $tkt_category->getId() . '" selected>' . $tkt_category->getTitle() . '</option>';
                                                    } else {
                                                        echo '<option value="' . $tkt_category->getId() . '">' . $tkt_category->getTitle() . '</option>';
                                                    }
                                                } else {
                                                    if ($tkt_category->cancreate())
                                                        echo '<option value="' . $tkt_category->getId() . '">' . $tkt_category->getTitle() . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Status</label>
                                    <div class="col-sm-10">
                                        <?php
                                        $tmp_tktstate_color = "";
                                        if ($ticket->getState()->getId() > 0)
                                            $tmp_tktstate_color = 'background: ' . $ticket->getState()->getColorcode();
                                        ?>
                                        <select name="tkt_state" id="tkt_state" class="form-control"
                                                style="<?php echo $tmp_tktstate_color; ?>"
                                                onChange="this.style.backgroundColor=this.options[this.selectedIndex].style.backgroundColor"
                                                required>
                                            <?php
                                            $tkt_all_states = TicketState::getAllStates();
                                            foreach ($tkt_all_states as $tkt_state) {
                                                if ($tkt_state->getId() != 1 || $ticket->getState()->getId() == 1) {
                                                    if ($ticket->getId() == 0 && $tkt_state->getId() == 2) {
                                                        echo '<option style="background: ' . $tkt_state->getColorcode() . '" value="' . $tkt_state->getId() . '" selected>' . $tkt_state->getTitle() . '</option>';
                                                    } else if ($ticket->getState() == $tkt_state) {
                                                        echo '<option style="background: ' . $tkt_state->getColorcode() . '" value="' . $tkt_state->getId() . '" selected>' . $tkt_state->getTitle() . '</option>';
                                                    } else {
                                                        echo '<option style="background: ' . $tkt_state->getColorcode() . '" value="' . $tkt_state->getId() . '">' . $tkt_state->getTitle() . '</option>';
                                                    }
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Priorität</label>
                                    <div class="col-sm-10">
                                        <select name="tkt_prio" id="tkt_prio" class="form-control" required>
                                            <?php
                                            $tkt_all_prios = TicketPriority::getAllPriorities();
                                            foreach ($tkt_all_prios as $tkt_prio) {
                                                if ($ticket->getPriority() == $tkt_prio) {
                                                    echo '<option value="' . $tkt_prio->getId() . '" selected>' . $tkt_prio->getTitle() . ' (' . $tkt_prio->getValue() . ') </option>';
                                                } else {
                                                    echo '<option value="' . $tkt_prio->getId() . '">' . $tkt_prio->getTitle() . ' (' . $tkt_prio->getValue() . ') </option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Fälligkeit</label>
                                    <div class="col-sm-4">
                                        <input type="text" style="width:160px" id="tkt_due" name="tkt_due"
                                               class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency form-control"
                                               onfocus="markfield(this,0)" onblur="markfield(this,1)"
                                               value="<? if ($ticket->getDuedate() != 0) {
                                                   echo date('d.m.Y H:i', $ticket->getDuedate());
                                               } elseif ($ticket->getId() == 0) {
                                                   echo date('d.m.Y H:i');
                                               } ?>"/>
                                    </div>
                                    <label for="" class="col-sm-1 control-label">ohne</label>
                                    <div class="col-sm-1">
                                        <input type="checkbox" id="tkt_due_enabled" name="tkt_due_enabled"
                                               class="form-control" value="1"
                                               onclick="JavaScript: if ($('#tkt_due_enabled').prop('checked')) {$('#tkt_due').val('')};"
                                            <?php if ($ticket->getDuedate() == 0 && $ticket->getId() != 0) echo " checked "; ?>/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Erstellt</label>
                                    <?php
                                    if ($_USER->hasRightsByGroup(Group::RIGHT_TICKET_CHANGE_OWNER) || $_USER->isAdmin()) {
                                        ?>
                                        <div class="col-sm-5">
                                            <select name="tkt_crtusr" id="tkt_crtusr" class="form-control">
                                                <?php
                                                $all_user = User::getAllUser(User::ORDER_NAME);
                                                foreach ($all_user as $tkt_crtusr) {
                                                    if ($ticket->getId() == 0 && $tkt_crtusr->getId() == $_USER->getId()) {
                                                        echo '<option value="' . $tkt_crtusr->getId() . '" selected>' . $tkt_crtusr->getNameAsLine() . '</option>';
                                                    } elseif ($ticket->getCrtuser()->getId() == $tkt_crtusr->getId()) {
                                                        echo '<option value="' . $tkt_crtusr->getId() . '" selected>' . $tkt_crtusr->getNameAsLine() . '</option>';
                                                    } else {
                                                        echo '<option value="' . $tkt_crtusr->getId() . '">' . $tkt_crtusr->getNameAsLine() . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <?php
                                        echo '<div class="col-sm-5"><div class="form-control">' . date("d.m.Y H:i", $ticket->getCrtdate()) . '</div></div>';
                                    } else {
                                        if ($ticket->getId() > 0)
                                            echo '<div class="col-sm-5"><div class="form-control">' . $ticket->getCrtuser()->getNameAsLine() . '</div><div class="col-sm-5"><div class="form-control">' . date("d.m.Y H:i", $ticket->getCrtdate()) . '</div>';
                                        else if ($ticket->getId() == 0)
                                            echo '<input type="hidden" name="tkt_crtusr" id="tkt_crtusr" value="' . $_USER->getId() . '"/>';
                                    }
                                    ?>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Zugewiesen</label>
                                    <div class="col-sm-10">
                                        <select name="tkt_assigned" id="tkt_assigned" class="form-control" required>
                                            <option disabled>-- Users --</option>
                                            <?php
                                            $all_user = User::getAllUser(User::ORDER_NAME);
                                            $all_groups = Group::getAllGroups(Group::ORDER_NAME);
                                            foreach ($all_user as $tkt_user) {
                                                if ($ticket->getId() == 0 && $tkt_user->getId() == $_USER->getId()) {
                                                    echo '<option value="u_' . $tkt_user->getId() . '" selected>' . $tkt_user->getNameAsLine() . '</option>';
                                                } elseif ($ticket->getAssigned_user() == $tkt_user) {
                                                    echo '<option value="u_' . $tkt_user->getId() . '" selected>' . $tkt_user->getNameAsLine() . '</option>';
                                                } else {
                                                    echo '<option value="u_' . $tkt_user->getId() . '">' . $tkt_user->getNameAsLine() . '</option>';
                                                }
                                            }
                                            ?>
                                            <option disabled>-- Groups --</option>
                                            <?php
                                            foreach ($all_groups as $tkt_groups) {
                                                if ($ticket->getAssigned_group() == $tkt_groups) {
                                                    echo '<option value="g_' . $tkt_groups->getId() . '" selected>' . $tkt_groups->getName() . '</option>';
                                                } else {
                                                    echo '<option value="g_' . $tkt_groups->getId() . '">' . $tkt_groups->getName() . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div> <!-- end left detail div -->

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Kunde</label>
                                    <div class="col-sm-10">
                                        <input type="text" id="tkt_customer" name="tkt_customer"
                                               value="<?php if ($ticket->getCustomer()->getId() > 0) {
                                                   echo $ticket->getCustomer()->getNameAsLine() . " - " . $ticket->getCustomer_cp()->getNameAsLine2();
                                               } ?>" class="form-control" required/>
                                        <input type="hidden" id="tkt_customer_id" name="tkt_customer_id"
                                               value="<?php echo $ticket->getCustomer()->getId(); ?>" required/>
                                        <input type="hidden" id="tkt_customer_cp_id" name="tkt_customer_cp_id"
                                               value="<?php echo $ticket->getCustomer_cp()->getId(); ?>" required/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Telefon</label>
                                    <div class="col-sm-10">
                                        <div id="cp_phone">
                                            <?php if ($ticket->getCustomer_cp()->getId() > 0) { ?>
                                                <span
                                                    onClick="dialNumber('<?php echo $_USER->getTelefonIP(); ?>/command.htm?number=<?php echo $ticket->getCustomer_cp()->getPhoneForDial(); ?>')"
                                                    title="<?php echo $ticket->getCustomer_cp()->getPhoneForDial() . " " . $_LANG->get('anrufen'); ?>"
                                                    class="pointer icon-link form-control">
									       <span class="glyphicons glyphicons-phone-alt"></span> <?php echo $ticket->getCustomer_cp()->getPhone(); ?>
							            </span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">eMail</label>
                                    <div class="col-sm-10">
                                        <div class="form-control"
                                             id="cp_mail"><?php if ($ticket->getCustomer_cp()->getId() > 0) echo $ticket->getCustomer_cp()->getEmail(); ?></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Herkunft</label>
                                    <div class="col-sm-10">
                                        <select name="tkt_source" id="tkt_source" class="form-control" required>
                                            <?php
                                            $tkt_all_sources = TicketSource::getAllSources();
                                            foreach ($tkt_all_sources as $tkt_source) {
                                                if ($ticket->getSource() == $tkt_source->getId()) {
                                                    echo '<option value="' . $tkt_source->getId() . '" selected>' . $tkt_source->getTitle() . '</option>';
                                                } elseif ($ticket->getId() == 0 && $tkt_source->getDefault()) {
                                                    echo '<option value="' . $tkt_source->getId() . '" selected>' . $tkt_source->getTitle() . '</option>';
                                                } else {
                                                    echo '<option value="' . $tkt_source->getId() . '">' . $tkt_source->getTitle() . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Geändert</label>
                                    <div class="col-sm-10">
                                        <div
                                            class="form-control"><?php if ($ticket->getId() > 0 && $ticket->getEditdate() > 0) echo date("d.m.Y H:i", $ticket->getEditdate()); ?>
                                            &nbsp;</div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Tour</label>
                                    <div class="col-sm-10">
                                        <div class="form-control"><span
                                                id="tkt_tourmarker"><?php if ($ticket->getId() > 0) echo $ticket->getTourmarker(); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Gepl. Zeit</label>
                                    <div class="col-sm-3">
                                        <input type="text" id="tkt_planned_time" name="tkt_planned_time"
                                               class="form-control"
                                               value="<?php echo printPrice($ticket->getPlanned_time(), 2); ?>"
                                               style="<?php if ($ticket->getTotal_time() > 0 && $ticket->getTotal_time() > $ticket->getPlanned_time()) echo ' ;background-color: #bbb; '; ?>"/>
                                    </div>
                                    <div class="col-sm-7">
                                <span class="form-control">
                                    Zeit-Artikel: <?php if ($ticket->getTimeFromArticles() > 0) echo printPrice($ticket->getTimeFromArticles(), 2); else echo '0,00'; ?>
                                    / Gen.-Artikel: <?php if ($ticket->getNonTimeArticles() > 0) echo printPrice($ticket->getNonTimeArticles(), 2); else echo '0,00'; ?>
                                </span>
                                    </div>
                                </div>
                            </div> <!-- end right detail div -->
                        </div>
                    </div> <!-- end ticket detail panel body -->
                </div> <!-- end ticket detail panel-->

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Neuer Kommentar</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row ticket_comment">
                            <div class="form-group">
                                <?php ////// TIMER STUFF >>> ///?>
                                <?php if ($ticket->getId() > 0){ ?>
                                    <label for="" class="col-sm-2 control-label">Timer</label>
                                    <div class="col-sm-2">
                                        <?php
                                        $timer = Timer::getLastUsed();
                                        $timer_start = 0;
                                        $timer_running = 0;
                                        $reset_disabled = false;


                                        if ($timer->getId() > 0){
                                            if ($timer->getState() == Timer::TIMER_RUNNING){

                                                $timer_start = $timer->getStarttime();
                                                $timer_running = 1;

                                                if ($timer->getModule() == "Ticket" && $timer->getObjectid() == $ticket->getId()){ // Timer läuft für dieses Ticket
                                                    ?>
                                                    <span id="ticket_timer" class="timer duration btn btn-warning" data-duration="0"></span> läuft
                                                    <a id="hiddenclicker_tkcframe" href="libs/modules/tickets/ticket.commentframe.php?exec=edit&tktid=<?=$timer->getObjectid()?>&this_tktid=<?=$ticket->getId()?>" style="display: none">Hidden Clicker</a>
                                                    <?php
                                                } else { // Timer läuft für anderes Ticket
                                                    $reset_disabled = true;
                                                    $tmp_ticket = new Ticket($timer->getObjectid());
                                                    ?>
                                                    <span id="ticket_timer" class="timer duration btn btn-error" data-duration="0"></span> läuft für '<a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$tmp_ticket->getId()?>"><?php echo $tmp_ticket->getNumber() . " - " . $tmp_ticket->getTitle(); ?></a>'
                                                    <a id="hiddenclicker_tkcframe" href="libs/modules/tickets/ticket.commentframe.php?exec=edit&tktid=<?=$timer->getObjectid()?>&this_tktid=<?=$ticket->getId()?>" style="display: none">Hidden Clicker</a>
                                                    <?php
                                                }
                                            } else { // Timer läuft nicht
                                                $timer_start = 0;
                                                $reset_disabled = true;
                                                $timer_running = 0;
                                                ?>
                                                <span id="ticket_timer" class="timer duration btn" data-duration="0">00:00:00</span>
                                                <?php
                                            }
                                        } else { // kein Timer gefunden
                                            $timer_start = 0;
                                            $reset_disabled = true;
                                            $timer_running = 0;
                                            ?>
                                            <span id="ticket_timer" class="timer duration btn" data-duration="0">00:00:00</span>
                                            <?php
                                        }
                                        ?>
                                        <input id="ticket_timer_timestamp" name="ticket_timer_timestamp" type="hidden" value="<?php echo $timer_start;?>"/>
                                        <input id="ticket_timer_running" name="ticket_timer_running" type="hidden" value="<?php echo $timer_running;?>"/>
                                        <input id="stop_timer" name="stop_timer" type="hidden" value="0"/>


                                        <script>
                                            $(document).ready(function () {
                                                var clock;
                                                var sec = moment().unix();
                                                var start = parseInt($('#ticket_timer_timestamp').val());
                                                var running = parseInt($('#ticket_timer_running').val());
                                                if (start != 0){
                                                    var timestamp = sec-start;
                                                    $("#ticket_timer").html(rectime(timestamp));
                                                }
                                                if (start != 0 && running == 1){
                                                    clock = setInterval(stopWatch,1000);
                                                }
                                                $( "#ticket_timer" ).click(function() {
                                                    if ($( "#ticket_timer" ).hasClass("btn-warning")){
                                                        $( "#tktc_article" ).focus();
                                                        $( "#stop_timer" ).val("1");
                                                        var amount = precise_round((sec-start)/60/60,2);
                                                        if ( amount < 0.1){
                                                            $( "#tktc_article_amount" ).val("0,1");
                                                        } else {
                                                            amount = amount.replace(".",",");
                                                            $( "#tktc_article_amount" ).val(amount);
                                                        }
                                                        clearInterval(clock);
                                                        $( "#ticket_timer" ).removeClass("btn-warning");
                                                        clearInterval(clock_home);
                                                        $( "#ticket_timer_home" ).removeClass("btn-warning");
                                                    } else {
                                                        if (!$( "#ticket_timer" ).hasClass("btn-error")){
                                                            if ($( "#stop_timer" ).val() != 1){
                                                                $.ajax({
                                                                        type: "POST",
                                                                        url: "libs/modules/timer/timer.ajax.php",
                                                                        data: { ajax_action: "start", module: "<?php echo get_class($ticket);?>", objectid: "<?php echo $ticket->getId();?>" }
                                                                    })
                                                                    .done(function( msg ) {
                                                                        if (start == 0){
                                                                            start = moment().unix();
                                                                        }
                                                                        sec = moment().unix();
                                                                        clock = setInterval(stopWatch,1000);
                                                                        $( "#ticket_timer" ).addClass("btn-warning");
                                                                        clock_home = setInterval(stopWatch_home,1000);
                                                                        $( "#ticket_timer_home" ).addClass("btn-warning");
                                                                        $('#stop_timer').prop('disabled', false);
                                                                    });
                                                            }
                                                        } else {
                                                            $('#hiddenclicker_tkcframe').trigger('click');
                                                        }
                                                    }
                                                });
                                                function stopWatch() {
                                                    sec++;
                                                    var timestamp = sec-start;
                                                    $("#ticket_timer").html(rectime(timestamp));
                                                    if ($("#stop_timer").prop('checked')){
                                                        var amount = precise_round((sec-start)/60/60,2);
                                                        amount = amount.replace(".",",");
                                                        $("#tktc_article_amount").val(amount);
                                                    }
                                                }
                                                function rectime(secs) {
                                                    var hr = Math.floor(secs / 3600);
                                                    var min = Math.floor((secs - (hr * 3600))/60);
                                                    var sec = Math.floor(secs - (hr * 3600) - (min * 60));

                                                    if (hr < 10) {hr = "0" + hr; }
                                                    if (min < 10) {min = "0" + min;}
                                                    if (sec < 10) {sec = "0" + sec;}
                                                    return hr + ':' + min + ':' + sec;
                                                }
                                                $( "#stop_timer" ).click(function() {
                                                    if ($("#stop_timer").prop('checked')){
                                                        var amount = precise_round((sec-start)/60/60,2);
                                                        amount = amount.replace(".",",");
                                                        $("#tktc_article_amount").val(amount);
                                                    }
                                                });
                                                function precise_round(num, decimals) {
                                                    var t=Math.pow(10, decimals);
                                                    return (Math.round((num * t) + (decimals>0?1:0)*(Math.sign(num) * (10 / Math.pow(100, decimals)))) / t).toFixed(decimals);
                                                }

                                                <?php
                                                if ($_REQUEST["start_timer"] == 1){
                                                ?>
                                                $('#ticket_timer').trigger('click');
                                                <?php
                                                }
                                                ?>
                                            });
                                        </script>
                                    </div>
                                <?php }// <<< TIMER STUFF //////?>
                                <label for="" class="col-sm-1 control-label">Tätigkeit</label>
                                <div class="col-sm-2">
                                    <input type="text" id="tktc_article" name="tktc_article" class="form-control"/>
                                    <input type="hidden" id="tktc_article_id" name="tktc_article_id"/>
                                </div>
                                <label for="" class="col-sm-1 control-label">Menge</label>
                                <div class="col-sm-2">
                                    <input type="text" id="tktc_article_amount" name="tktc_article_amount" class="form-control"/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">Kommentar Typ</label>
                                <div class="col-sm-4">
                                    <select name="tktc_type" id="tktc_type" class="form-control">
                                        <option value="<?php echo Comment::VISABILITY_INTERNAL;?>">inter. Kommentar</option>
                                        <option value="<?php echo Comment::VISABILITY_PUBLIC;?>">Offiz. Kommentar</option>
                                        <option value="<?php echo Comment::VISABILITY_PRIVATE;?>">priv. Kommentar</option>
                                        <option value="<?php echo Comment::VISABILITY_PUBLICMAIL;?>">Offiz. Antwort (Mail)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-9">
                                <textarea name="tktc_comment" id="tktc_comment" rows="10" cols="80"></textarea>
                            </div>
                            <div class="col-md-3">
                                <script type="text/javascript">
                                    function select_all()
                                    {
                                        $('#abo_notify').children().each(function(index,item){
                                            $(this).attr('selected', 'selected');
                                        });
                                    }
                                    function deselect_all()
                                    {
                                        $('#abo_notify').children().each(function(index,item){
                                            $(this).removeAttr('selected');
                                        });
                                    }
                                </script>
                                <b>Benachrichtigen:</b> <span class="pointer" onclick="select_all();">alle</span> - <span class="pointer" onclick="deselect_all();">keiner</span></br>
                                <select id="abo_notify" name="abo_notify[]" size="15" multiple class="form-control">
                                    <?php
                                    if (Abonnement::hasAbo($ticket))
                                    {
                                        $abonnoments = Abonnement::getAbonnementsForObject(get_class($ticket), $ticket->getId());
                                        foreach ($abonnoments as $abonnoment){
                                            echo '<option value="'.$abonnoment->getAbouser()->getId().'">'.$abonnoment->getAbouser()->getNameAsLine().'</option>';
                                        }
                                    }
                                    ?>
                                </select></br>
                                <b>Hinzufügen:</b></br>
                                <input type="text" id="tktc_new_notify_user" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Anhänge</label>
                                    <div class="col-sm-3">
                                        <span class="btn btn-success btn-xs fileinput-button">
                                            <span>Hinzufügen...</span>
                                            <input type="file" multiple="multiple" id="fileupload" name="files[]"
                                                   width="100%"/>
                                        </span>
                                        <div id="files" class="files">
                                            <?php
                                            if ($_REQUEST["frommail"] == true) {
                                                if (count($mail_attachments) > 0) {
                                                    foreach ($mail_attachments as $mail_attachment) {
                                                        echo "<p>" . $mail_attachment["name"] . "</p>";
                                                    }
                                                }
                                            }
                                            ?>
                                        </div>
                                        <div id="progress" class="progress">
                                            <div class="progress-bar progress-bar-success"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end ticket comment panel body -->
                </div> <!-- end ticket comment panel -->

                <?php
                switch ($_REQUEST["sort"]){
                    case "asc":
                        $all_comments = Comment::getCommentsForObject(get_class($ticket), $ticket->getId());
                        break;
                    case "tasc":
                        $all_comments = Comment::getCommentsForObject(get_class($ticket), $ticket->getId(), 'visability, crtdate');
                        break;
                    case "tdesc":
                        $all_comments = Comment::getCommentsForObject(get_class($ticket), $ticket->getId(), 'visability, crtdate');
                        $all_comments = array_reverse($all_comments);
                        break;
                    default:
                        $all_comments = Comment::getCommentsForObject(get_class($ticket), $ticket->getId());
                        $all_comments = array_reverse($all_comments);
                        break;
                }
                if (count($all_comments) > 0 && $ticket->getId() > 0) {
                    ?>
                    <a name="ticket_comments"></a>
                    <div class="ticket_comments">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    Kommentare
                                    <span class="pull-right">
                                        <?php if ($_REQUEST["sort"] != "tasc") { ?>
                                            <a href="index.php?page=<?= $_REQUEST['page'] ?>&exec=edit&tktid=<?=$ticket->getId()?>&sort=tasc">
                                                <span class="glyphicons glyphicons-arrow-up" style="color: blue;" title="Sortierung: Typ aufsteigend"></span>
                                            </a>
                                        <?php } else { ?>
                                            <a href="index.php?page=<?= $_REQUEST['page'] ?>&exec=edit&tktid=<?=$ticket->getId()?>&sort=tdesc">
                                                <span class="glyphicons glyphicons-arrow-down" style="color: blue;" title="Sortierung: Typ absteigend"></span>
                                            </a>
                                        <?php } ?>
                                        <?php if ($_REQUEST["sort"] != "asc") { ?>
                                            <a href="index.php?page=<?= $_REQUEST['page'] ?>&exec=edit&tktid=<?=$ticket->getId()?>&sort=asc">
                                                <span class="glyphicons glyphicons-arrow-up" title="Sortierung: Datum aufsteigend"></span>
                                            </a>
                                        <?php } else { ?>
                                            <a href="index.php?page=<?= $_REQUEST['page'] ?>&exec=edit&tktid=<?=$ticket->getId()?>&sort=desc">
                                                <span class="glyphicons glyphicons-arrow-down" title="Sortierung: Datum absteigend"></span>
                                            </a>
                                        <?php } ?>
                                    </span>
                                </h3>
                            </div>
                            <div class="panel-body" style="background: #f7f7f7 none repeat scroll 0 0;">

                                <?php
                                foreach ($all_comments as $comment) {
                                    if ($_USER->isAdmin()
                                        || $comment->getVisability() == Comment::VISABILITY_PUBLIC
                                        || $comment->getVisability() == Comment::VISABILITY_INTERNAL
                                        || $comment->getVisability() == Comment::VISABILITY_PUBLICMAIL
                                        || $comment->getCrtuser() == $_USER
                                    ) {
                                        if ($comment->getCrtuser()->getId() > 0) {
                                            $crtby = $comment->getCrtuser()->getNameAsLine();
                                        } elseif ($comment->getCrtcp()->getId() > 0) {
                                            $crtby = $comment->getCrtcp()->getNameAsLine2();
                                        }
                                        ?>

                                        <a name="comment_<?php echo $comment->getId(); ?>"></a>
                                        <div
                                            style="padding: 8px 0; background: #f7f7f7 none repeat scroll 0 0; border-bottom: 1px solid #eee;">
                                            <img alt="User Image" src="libs/basic/user/user.avatar.get.php?uid=<?php echo $comment->getCrtuser()->getId();?>" class="img-circle img-sm"
                                                 style="height: 30px !important; width: 30px !important; float: left;">
                                            <div style="color: #555; margin-left: 40px;">
                                              <span style="color: #444; display: block; font-weight: 600;">
                                                  <?php echo $crtby; ?>
                                                  <?php if ($comment->getTitle() != '') echo ' - ' . $comment->getTitle(); ?>
                                                  <span class="pull-right" style="font-size: 12px; font-weight: 400;">
                                                      <?php if ($_USER->isAdmin()){
                                                          switch ($comment->getVisability()) {
                                                              case Comment::VISABILITY_PUBLIC:
                                                                  $style = 'style="width: 100px; background-color: #449d44;"';
                                                                  break;
                                                              case Comment::VISABILITY_PUBLICMAIL:
                                                                  $style = 'style="width: 100px; background-color: #449d44;"';
                                                                  break;
                                                              case Comment::VISABILITY_INTERNAL:
                                                                  $style = 'style="width: 100px; background-color: #31b0d5;"';
                                                                  break;
                                                              case Comment::VISABILITY_PRIVATE:
                                                                  $style = 'style="width: 100px; background-color: #428bca;"';
                                                                  break;
                                                          }
                                                          ?>
                                                          <select name="comment_state" onchange="window.location.href='index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid=<?php echo $ticket->getId();?>&commentid=<?php echo $comment->getId();?>&newvis='+$(this).val()" class="small" <?php echo $style;?>>
                                                              <?php
                                                              foreach (Array(Comment::VISABILITY_INTERNAL,Comment::VISABILITY_PUBLIC,Comment::VISABILITY_PRIVATE,Comment::VISABILITY_PUBLICMAIL) as $item) {
                                                                  if ($item == Comment::VISABILITY_INTERNAL) {
                                                                      echo '<option class="label" value="' . $item . '" style="background: #31b0d5;" ';
                                                                      if ($item == $comment->getVisability())
                                                                          echo ' selected ';
                                                                      echo '>[INTERN]</option>';
                                                                  } else if ($item == Comment::VISABILITY_PUBLIC) {
                                                                      echo '<option class="label" value="' . $item . '" style="background: #449d44;" ';
                                                                      if ($item == $comment->getVisability())
                                                                          echo ' selected ';
                                                                      echo '>[PUBLIC]</option>';
                                                                  } else if ($item == Comment::VISABILITY_PRIVATE) {
                                                                      echo '<option class="label" value="' . $item . '" style="background: #428bca;" ';
                                                                      if ($item == $comment->getVisability())
                                                                          echo ' selected ';
                                                                      echo '>[PRIVATE]</option>';
                                                                  } else if ($item == Comment::VISABILITY_PUBLICMAIL) {
                                                                      echo '<option class="label" value="' . $item . '" style="background: #449d44;" ';
                                                                      if ($item == $comment->getVisability())
                                                                          echo ' selected ';
                                                                      echo '>[PUBLIC-MAIL]</option>';
                                                                  }
                                                              }
                                                              ?>
                                                          </select>
                                                          <?php
                                                      } else {
                                                          switch ($comment->getVisability()) {
                                                              case Comment::VISABILITY_PUBLIC:
                                                                  echo '<span class="label" style="background-color: #449d44;">[PUBLIC]</span>';
                                                                  break;
                                                              case Comment::VISABILITY_PUBLICMAIL:
                                                                  echo '<span class="label" style="background-color: #449d44;">[PUBLIC-MAIL]</span>';
                                                                  break;
                                                              case Comment::VISABILITY_INTERNAL:
                                                                  echo '<span class="label" style="background-color: #31b0d5;">[INTERN]</span>';
                                                                  break;
                                                              case Comment::VISABILITY_PRIVATE:
                                                                  echo '<span class="label" style="background-color: #428bca;">[PRIVATE]</span>';
                                                                  break;
                                                          }
                                                      }?>
                                                      <?php
                                                      if ($comment->getState() == 0) {
                                                          echo '<span class="label" style="background-color: #f0ad4e;">[GELÖSCHT]</span>';
                                                      }
                                                      ?>
                                                      <?php echo date("d.m.Y H:i", $comment->getCrtdate()); ?>
                                                      <?php
                                                      if (($_USER->isAdmin() || $_USER == $comment->getCrtuser()) || ($comment->getVisability() == Comment::VISABILITY_INTERNAL && $_USER->hasRightsByGroup(Group::RIGHT_TICKET_EDIT_INTERNAL)) || ($comment->getVisability() == Comment::VISABILITY_PUBLIC && $_USER->hasRightsByGroup(Group::RIGHT_TICKET_EDIT_OFFICAL)) || ($comment->getVisability() == Comment::VISABILITY_PUBLICMAIL && $_USER->hasRightsByGroup(Group::RIGHT_TICKET_EDIT_OFFICAL))) {
                                                          echo '<span class="glyphicons glyphicons-pencil pointer" onclick="callBoxFancytktc(\'libs/modules/comment/comment.edit.php?cid=' . $comment->getId() . '&tktid=' . $ticket->getId() . '\');"/></span>';
                                                      }
                                                      ?>
                                                      <input type="checkbox" class="comment-select" value="<?php echo $comment->getId(); ?>">
                                                  </span>
                                              </span><!-- /.username -->
                                                <div class="row" style="margin-left: 0px;">
                                                    <?php if ($comment->getState() > 0) { ?>
                                                        <?php echo $comment->getComment(); ?>
                                                    <?php } ?>
                                                    <span class="pointer pull-right"
                                                          onclick="callBoxFancytktc('libs/modules/comment/comment.new.php?tktid=<?php echo $ticket->getId(); ?>&tktc_module=<?php echo get_class($comment); ?>&tktc_objectid=<?php echo $comment->getId(); ?>');">Kommentieren</span>
                                                </div>
                                            </div>
                                            <?php if (count(Attachment::getAttachmentsForObject(get_class($comment), $comment->getId())) > 0) { ?>
                                                <div style="color: #555; margin-top: 10px; margin-left: 40px;">
                                                    <div class="row">
                                                        <div class="col-md-1">
                                                            <span>Anhänge:</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <?php
                                                            foreach (Attachment::getAttachmentsForObject(get_class($comment), $comment->getId()) as $c_attachment) {
                                                                if ($c_attachment->getState() == 1)
                                                                    if (strstr($c_attachment->getOrig_filename(),'.pdf')){
                                                                        echo '<span><a class="pointer" onclick="callBoxFancyPreview(\'libs/modules/tickets/ticket.pdfviewer.php?pdffile='.$c_attachment->getFilename().'\');">' . $c_attachment->getOrig_filename() . '</a></span></br>';
                                                                    } else {
                                                                        echo '<span><a href="' . Attachment::FILE_DESTINATION . $c_attachment->getFilename() . '" download="' . $c_attachment->getOrig_filename() . '">' . $c_attachment->getOrig_filename() . '</a></span></br>';
                                                                    }
                                                                elseif ($c_attachment->getState() == 0 && $_USER->isAdmin())
                                                                    if (strstr($c_attachment->getOrig_filename(),'.pdf')){
                                                                        echo '<span><del><a class="pointer" onclick="callBoxFancyPreview(\'libs/modules/tickets/ticket.pdfviewer.php?pdffile='.$c_attachment->getFilename().'\');">' . $c_attachment->getOrig_filename() . '</a></del></span></br>';
                                                                    } else {
                                                                        echo '<span><del><a href="' . Attachment::FILE_DESTINATION . $c_attachment->getFilename() . '" download="' . $c_attachment->getOrig_filename() . '">' . $c_attachment->getOrig_filename() . '</a></del></span></br>';
                                                                    }
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <?php if (count($comment->getArticles()) > 0) { ?>
                                                <div style="color: #555; margin-top: 10px; margin-left: 40px;">
                                                    <div class="row">
                                                        <div class="col-md-1">
                                                            <span>Artikel:</span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <?php
                                                            foreach ($comment->getArticles() as $c_article) {
                                                                if ($c_article->getState() == 1)
                                                                    echo '<span>' . $c_article->getAmount() . 'x <a target="_blank" href="index.php?page=libs/modules/article/article.php&exec=edit&aid=' . $c_article->getArticle()->getId() . '">' . $c_article->getArticle()->getTitle() . '</a></span></br>';
                                                                elseif ($c_article->getState() == 0 && $_USER->isAdmin())
                                                                    echo '<span><del>' . $c_article->getAmount() . 'x <a target="_blank" href="index.php?page=libs/modules/article/article.php&exec=edit&aid=' . $c_article->getArticle()->getId() . '">' . $c_article->getArticle()->getTitle() . '</a></del></span></br>';
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <?php
                                            $all_comments_sub = Comment::getCommentsForObject(get_class($comment), $comment->getId());
                                            if (count($all_comments_sub) > 0) {
                                                ?>

                                                <?php
                                                foreach ($all_comments_sub as $subcom) {
                                                    if ($_USER->isAdmin()
                                                        || $subcom->getVisability() == Comment::VISABILITY_PUBLIC
                                                        || $subcom->getVisability() == Comment::VISABILITY_PUBLICMAIL
                                                        || $subcom->getVisability() == Comment::VISABILITY_INTERNAL
                                                        || $subcom->getCrtuser() == $_USER
                                                    ) {
                                                        if ($comment->getCrtuser()->getId() > 0) {
                                                            $crtby = $subcom->getCrtuser()->getNameAsLine();
                                                        } elseif ($comment->getCrtcp()->getId() > 0) {
                                                            $crtby = $subcom->getCrtcp()->getNameAsLine2();
                                                        }
                                                        ?>
                                                        <div style="padding: 8px 0; background: #f7f7f7 none repeat scroll 0 0; border-bottom: 1px solid #eee; margin-left: 40px;">
                                                            <img alt="User Image" src="libs/basic/user/user.avatar.get.php?uid=<?php echo $subcom->getCrtuser()->getId();?>" class="img-circle img-sm" style="height: 30px !important; width: 30px !important; float: left;">
                                                            <div style="color: #555; margin-left: 40px;">
                                                                <span
                                                                    style="color: #444; display: block; font-weight: 600;">
                                                                    <?php echo $crtby; ?>
                                                                    <?php if ($subcom->getTitle() != '') echo ' - ' . $subcom->getTitle(); ?>
                                                                    <span class="pull-right" style="font-size: 12px; font-weight: 400;">
                                                                        <?php if ($_USER->isAdmin()){
                                                                            switch ($subcom->getVisability()) {
                                                                                case Comment::VISABILITY_PUBLIC:
                                                                                    $style = 'style="width: 100px; background-color: #449d44;"';
                                                                                    break;
                                                                                case Comment::VISABILITY_PUBLICMAIL:
                                                                                    $style = 'style="width: 100px; background-color: #449d44;"';
                                                                                    break;
                                                                                case Comment::VISABILITY_INTERNAL:
                                                                                    $style = 'style="width: 100px; background-color: #31b0d5;"';
                                                                                    break;
                                                                                case Comment::VISABILITY_PRIVATE:
                                                                                    $style = 'style="width: 100px; background-color: #428bca;"';
                                                                                    break;
                                                                            }
                                                                            ?>
                                                                            <select name="comment_state" onchange="window.location.href='index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid=<?php echo $ticket->getId();?>&commentid=<?php echo $subcom->getId();?>&newvis='+$(this).val()" class="small" <?php echo $style;?>>
                                                                                <?php
                                                                                foreach (Array(Comment::VISABILITY_INTERNAL,Comment::VISABILITY_PUBLIC,Comment::VISABILITY_PRIVATE,Comment::VISABILITY_PUBLICMAIL) as $item) {
                                                                                    if ($item == Comment::VISABILITY_INTERNAL) {
                                                                                        echo '<option class="label" value="' . $item . '" style="background: #31b0d5;" ';
                                                                                        if ($item == $subcom->getVisability())
                                                                                            echo ' selected ';
                                                                                        echo '>[INTERN]</option>';
                                                                                    } else if ($item == Comment::VISABILITY_PUBLIC) {
                                                                                        echo '<option class="label" value="' . $item . '" style="background: #449d44;" ';
                                                                                        if ($item == $subcom->getVisability())
                                                                                            echo ' selected ';
                                                                                        echo '>[PUBLIC]</option>';
                                                                                    } else if ($item == Comment::VISABILITY_PRIVATE) {
                                                                                        echo '<option class="label" value="' . $item . '" style="background: #428bca;" ';
                                                                                        if ($item == $subcom->getVisability())
                                                                                            echo ' selected ';
                                                                                        echo '>[PRIVATE]</option>';
                                                                                    } else if ($item == Comment::VISABILITY_PUBLICMAIL) {
                                                                                        echo '<option class="label" value="' . $item . '" style="background: #449d44;" ';
                                                                                        if ($item == $subcom->getVisability())
                                                                                            echo ' selected ';
                                                                                        echo '>[PUBLIC-MAIL]</option>';
                                                                                    }
                                                                                }
                                                                                ?>
                                                                            </select>
                                                                            <?php
                                                                        } else {
                                                                            switch ($comment->getVisability()) {
                                                                                case Comment::VISABILITY_PUBLIC:
                                                                                    echo '<span class="label" style="background-color: #449d44;">[PUBLIC]</span>';
                                                                                    break;
                                                                                case Comment::VISABILITY_PUBLICMAIL:
                                                                                    echo '<span class="label" style="background-color: #449d44;">[PUBLIC-MAIL]</span>';
                                                                                    break;
                                                                                case Comment::VISABILITY_INTERNAL:
                                                                                    echo '<span class="label" style="background-color: #31b0d5;">[INTERN]</span>';
                                                                                    break;
                                                                                case Comment::VISABILITY_PRIVATE:
                                                                                    echo '<span class="label" style="background-color: #428bca;">[PRIVATE]</span>';
                                                                                    break;
                                                                            }
                                                                        }?>
                                                                        <?php
                                                                        if ($subcom->getState() == 0) {
                                                                            echo '<span class="label" style="background-color: #f0ad4e;">[GELÖSCHT]</span>';
                                                                        }
                                                                        ?>
                                                                        <?php echo date("d.m.Y H:i", $subcom->getCrtdate()); ?>
                                                                        <?php
                                                                        if ($_USER->isAdmin() || $_USER == $subcom->getCrtuser()) {
                                                                            echo '<span class="glyphicons glyphicons-pencil pointer" onclick="callBoxFancytktc(\'libs/modules/comment/comment.edit.php?cid=' . $subcom->getId() . '&tktid=' . $ticket->getId() . '\');"/></span>';
                                                                        }
                                                                        ?>
                                                                        <input type="checkbox" class="comment-select" value="<?php echo $comment->getId(); ?>">
                                                                    </span>
                                                                </span><!-- /.username -->
                                                            <?php if ($subcom->getState() > 0) { ?>
                                                                <?php echo $subcom->getComment(); ?>
                                                            <?php } ?>
                                                            </div>
                                                            <?php if (count(Attachment::getAttachmentsForObject(get_class($subcom), $subcom->getId())) > 0) { ?>
                                                                <div
                                                                    style="color: #555; margin-top: 10px; margin-left: 40px;">
                                                                    <div class="row">
                                                                        <div class="col-md-1">
                                                                            <span>Anhänge:</span>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <?php
                                                                            foreach (Attachment::getAttachmentsForObject(get_class($subcom), $subcom->getId()) as $c_attachment) {
                                                                                if ($c_attachment->getState() == 1)
                                                                                    echo '<span><a href="' . Attachment::FILE_DESTINATION . $c_attachment->getFilename() . '" download="' . $c_attachment->getOrig_filename() . '">' . $c_attachment->getOrig_filename() . '</a></span></br>';
                                                                                elseif ($c_attachment->getState() == 0 && $_USER->isAdmin())
                                                                                    echo '<span><del><a href="' . Attachment::FILE_DESTINATION . $c_attachment->getFilename() . '" download="' . $c_attachment->getOrig_filename() . '">' . $c_attachment->getOrig_filename() . '</a></del></span></br>';
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                            <?php if (count($subcom->getArticles()) > 0) { ?>
                                                                <div
                                                                    style="color: #555; margin-top: 10px; margin-left: 40px;">
                                                                    <div class="row">
                                                                        <div class="col-md-1">
                                                                            <span>Artikel:</span>
                                                                        </div>
                                                                        <div class="col-md-5">
                                                                            <?php
                                                                            foreach ($subcom->getArticles() as $c_article) {
                                                                                if ($c_article->getState() == 1)
                                                                                    echo '<span>' . $c_article->getAmount() . 'x <a target="_blank" href="index.php?page=libs/modules/article/article.php&exec=edit&aid=' . $c_article->getArticle()->getId() . '">' . $c_article->getArticle()->getTitle() . '</a></span></br>';
                                                                                elseif ($c_article->getState() == 0 && $_USER->isAdmin())
                                                                                    echo '<span><del>' . $c_article->getAmount() . 'x <a target="_blank" href="index.php?page=libs/modules/article/article.php&exec=edit&aid=' . $c_article->getArticle()->getId() . '">' . $c_article->getArticle()->getTitle() . '</a></del></span></br>';
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } ?>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <?php
                $all_logs = TicketLog::getAllForTicket($ticket);
                if (count($all_logs) > 0 && $ticket->getId()>0){?>
                    <a name="ticket_logs"></a>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Log</h3>
                        </div>
                        <div class="table-responsive">
                        	<table class="table table-hover">
                        		<tbody>
                                    <?php
                                    foreach ($all_logs as $log){
                                        ?>
                                        <tr>
                                            <td width="15%"><?php echo date("d.m.Y H:i",$log->getDate());?></td>
                                            <td width="15%"><?php echo $log->getCrtusr()->getNameAsLine();?></td>
                                            <td width="70%"><?php echo $log->getEntry();?></td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                        		</tbody>
                        	</table>
                        </div>
                    </div>
                    <?php
                }
                ?>

            </div> <!-- end main panel -->
        </div>
    </form>
</div>
</body>
</html>
