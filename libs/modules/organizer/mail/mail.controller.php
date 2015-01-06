<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       13.03.2014
// Copyright:     2012-14 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once "mailModel.class.php";
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once $_BASEDIR . '/libs/modules/calculation/calculation.class.php';

use Zend\Mail;
use Zend\Mail\Headers;
use Zend\Mail\Storage;
use Zend\Mail\Message;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

define('IMAP_ARCHIVE_FOLDER_NAME', 'ARCHIV');
$userEmailAdresses = $_USER->getEmailAddresses();

// Fetch the selected folder.
$selectedFolder = urldecode(getRequestVarOr("folder", "INBOX"));

$emailId = getRequestVarOr("emailId", null);
if($emailId == null) {
	foreach($userEmailAdresses as $emailAddress) {
		if($emailId == null) $emailId = $emailAddress->getId();
	}
}

function createImapFolder(MailModel $mailModel, $folderName, $throwAlert = true) {
    $folderName = (empty($folderName)) ? 'Neuer_Ordner' : $folderName;
    try {
        $mailModel->getAccount()->createFolder($folderName);
    } catch(\Zend\Mail\Storage\Exception\RuntimeException $e) {
        if($throwAlert) {
            echo '<script>alert("Beim Erstellen des Ordners ist ein Fehler aufgetreten: ' . $e->getMessage() . '");</script>';
            exit;
        }
        return false;
    }
    return true;
}

function checkArchiveFolder(MailModel $mailModel) {
    foreach($mailModel->getAccount()->getFolders() as $folder) {
        /** @var $folder Zend\Mail\Storage\Folder */
        if($folder->getGlobalName() == IMAP_ARCHIVE_FOLDER_NAME) {
            /* yeah, folder exists! */
            return true;
        }
    }
    return false;
}

if($emailId == null) {
	$error = 'Keine E-Mail Konten hinterlegt';
	$folders = array();
	$contentTemplate = '';
	require_once 'template/main.template.php';
} else {
	$emailIds = array();
	foreach($userEmailAdresses as $emailAddress) {
		$emailIds[$emailAddress->getAddress()] = $emailAddress->getId();
	}
	
	// Fetch all folders/messages for each registered account.
	foreach($userEmailAdresses as $emailAddress) {
		if($emailAddress->getId() != $emailId) {
			continue;
		}
	
		// Create a new MailModel instance.
		$mailModel = new MailModel($emailAddress, $selectedFolder);
		if(!$mailModel->getAccount()) {
			$error = '<div>Verbindung zu '.$emailAddress->getAddress().' konnte nicht hergestellt werden.</div>';
	
			$folders = array();
			$contentTemplate = '';
			require_once 'template/main.template.php';
			continue;
		}
		

        // Collect selected message ids.
        $messageIds = array();
        $error = '';
        foreach(array_keys($_REQUEST) as $key) {
            if(preg_match("/chk_msg_(?P<id>\d+)/", $key, $match)) {
                array_push($messageIds, intval($match["id"]));
            }
        }

        $moveToOrderId = getRequestVarOr("moveToOrderId", 0);

        if($moveToOrderId > 0) {

            /* move to order BEGIN */

            if(!checkArchiveFolder($mailModel)) {
                $success = createImapFolder($mailModel, IMAP_ARCHIVE_FOLDER_NAME, false);
                if($success) {
                    #echo '<script>window.location.reload();</script>';
                    #exit;
                } else {
                    echo '<script>alert("IMAP-Fehler: Konnte Archiv-Order [' . IMAP_ARCHIVE_FOLDER_NAME . '] nicht erstellen!");</script>';
                }
            }

            foreach($messageIds as $messageId) {
                $number = null;
                try {
                    $number = $mailModel->getAccount()->getNumberByUniqueId($messageId);
                } catch(Exception $e) {
                    $error = "Die Nachricht(en) konnte nicht gelesen werden.";
                }

                if($number != null) {
                    $copied = true;
                    try {
                        $mailModel->getAccount()->copyMessage($number, IMAP_ARCHIVE_FOLDER_NAME);
                    } catch(Exception $e) {
                        $copied = false;
                        $error = "Die Nachricht(en) konnte nicht kopiert werden.";
                    }
                    if($copied) {
                        // $theMessageId = $mailModel->getAccount()->getNumberByUniqueId($number);
                        // echo '<script>alert("Fetching Message ' . $theMessageId . ' ... ");</script>';
                        // $messageObject = getMessageObject($theMessageId, $mailModel);
                        $messageObject = getMessageObject($number, $mailModel);

						// echo "Content Test: </br>";
						// echo $messageObject["content"];
						// echo "End Content Test</br>";
						// die();
						
                        // echo '<script>alert("Creating Note...");</script>';
                        $note = new Notes(0);
                        $note->setTitle("[E-Mail] " . $messageObject["subject"]);
                        $note->setComment(mysql_real_escape_string($messageObject["content"]));
                        $note->setModule(Notes::MODULE_CALCULATION);
                        $note->setObjectid($moveToOrderId);
						
						$attachs = $messageObject['attachments'];
						
						// print_r ($attachs);
						if (count($attachs) > 0){
							copy('docs/attachments/' . $attachs[0]['filename'], 'docs/notes_files/' . $attachs[0]['filename']);
							$note->setFileName($attachs[0]['filename']);
						}

                        // echo '<script>alert("Note beforePersist...");</script>';

                        $note->save();
						
						try {
                            $mailModel->getAccount()->removeMessage($number);
                        } catch(Exception $e) {
                            $error = "Die Nachricht(en) konnte nicht entfernt werden.";
                        }

                        global $DB;

                        if($DB->getLastError() != NULL && $DB->getLastError() != "" ){
                            echo '<script>alert("' . $DB->getLastError() . '");</script>';
                            $error = $DB->getLastError();
                        }


                    }
                } else {
                    $error = "Die Nachricht(en) konnte nicht verschoben werden: Ungülte Nachricht (Nbr: " . var_export($number, true) . ", MessageId: " . var_export($messageId, true) . ")";
                }



                $mailModel->getAccount()->noop();
            }

            /* move to order END */



        }

		
		// ---------------------------------- E-Mails verschieben -----------------------------------------------------
		
		// Check if messages need to be moved from one folder to another.
		$move = getRequestVarOr("move", null);
	
		// Collect selected message ids.
		$messageIds = array();
		$error = '';
		foreach(array_keys($_REQUEST) as $key) {
			if(preg_match("/chk_msg_(?P<id>\d+)/", $key, $match)) {
				array_push($messageIds, intval($match["id"]));
			}
		}
	
		// Check if mails have been moved.
		if($move != null && empty($moveToOrderId)) {
			$uniqueNumbers = array();
			$destinationFolder = urldecode($move);
			foreach($messageIds as $messageId) {
				$number = null;
				try {
					$number = $mailModel->getAccount()->getNumberByUniqueId($messageId);
				} catch(Exception $e) {
	 				$error = $_LANG->get("Die Nachricht(en) konnte nicht verschoben werden.");
				}
				
				if($number != null) {
					$copied = true;
					try {
						$mailModel->getAccount()->copyMessage($number, $destinationFolder);
					} catch(Exception $e) {
						$copied = false;
						$error = $_LANG->get("Die Nachricht(en) konnte nicht verschoben werden.");
					}
					if($copied) {
							try {
								$mailModel->getAccount()->removeMessage($number);
							} catch(Exception $e) {
								$error = $_LANG->get("Die Nachricht(en) konnte nicht verschoben werden.");
							}
					}
				} else {
					$error = $_LANG->get("Die Nachricht(en) konnte nicht verschoben werden.");
				}

				$mailModel->getAccount()->noop();
			}
		}
		
		// ---------------------------------- E-Mails aufrufen --------------------------------------------------------
	
		// Check if an action has been executed.
		$action = getRequestVarOr("exec", null);
	
		$showMessage = getRequestVarOr("message", null);
		$messageObject = null;
		if($showMessage != null) {
			try {
				$messageObject = getMessageObject($mailModel->getAccount()->getNumberByUniqueId($showMessage), $mailModel);
				$messageObject["id"] = $showMessage;
			} catch(Exception $e) {
	 			$error = $_LANG->get("Die Nachricht wurde nicht gefunden.");
			}
		}
		
		if($action != null) {
            if($action == 'createImapFolder') {
                $folderName = $_GET['imapNewFolderName'];
                createImapFolder($mailModel, $folderName);
            } elseif($action == "newmail") {
				$subexec = getRequestVarOr("subexec", null);
				if($subexec != null) {
					if($subexec == "send") {
						$sender = getRequestVarOr("mail_from", null);
						$recevier = getRequestVarOr("mail_to", null);
						$subject = getRequestVarOr("mail_subject", $_LANG->get('Kein Betreff'));
						$body = getRequestVarOr("mail_body", "");

						if($sender == null || $recevier == null || $subject == null) {
							$error = $_LANG->get('Bitte Empf&auml;nger und Betreff ausf&uuml;llen!');
						} else {
							$validReceiver = true;
							$receiverMails = split(',', $recevier);
							foreach($receiverMails as $recevierMail) {
								if($recevierMail != null) {
									// if(!(preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/', trim($recevierMail)) > 0)) {
									if(!filter_var(trim($recevierMail), FILTER_VALIDATE_EMAIL)) {
										$validReceiver = false;
									}
								}
							}
							
							if($validReceiver) {
								$mimePart = new MimePart($body);
								$mimePart->type = "text/plain";
								$mimePart->charset = 'utf-8';
								
								$mimeMessage = new MimeMessage();
								$mimeMessage->setParts(array($mimePart));
								
								$mail = new Message();
								$mail->setBody($mimeMessage);
								$mail->setFrom($sender);
								$mail->addTo($recevier);
								$mail->setSubject(utf8_decode($subject));
						
								try {
									$mailModel->sendMail($mail);
									$ok = $_LANG->get('Die Nachricht wurde verschickt');
								} catch(Exception $e) {
		 							$error = $_LANG->get('Die Nachricht konnte nicht verschickt werden!');
								}
								$sender = '';
								$recevier = '';
								$subject = '';
								$body = '';
							} else {
								$error = $_LANG->get('Bitte Empf&auml;nger Adresse(n) korrigieren!');
							}
							
						}
					} else if($subexec == "answer") {
						$sender = $messageObject["to"];
						$recevier = $messageObject["from"];
						$subject = "Re:".$messageObject["subject"];
						$body = $messageObject["content"];
					} else if($subexec == "forward") {
						$sender = $messageObject["to"];
						$recevier = "";
						$subject = "Fw:".$messageObject["subject"];
						$body = $messageObject["content"];
					}
				}
			} elseif ($action == "deletesingle") {
				try {
					$mailModel->getAccount()->removeMessage($mailModel->getAccount()->getNumberByUniqueId(intval($_REQUEST["messageid"])));
				} catch(Exception $e) {
					$error = $_LANG->get('Die Nachricht konnte nicht gel&ouml;scht werden!');
				}
			} else {
				foreach($messageIds as $messageId) {
					switch($action) {
						case "delete":
							try {
								$mailModel->getAccount()->removeMessage($mailModel->getAccount()->getNumberByUniqueId($messageId));
							} catch(Exception $e) {
								$error = $_LANG->get('Die Nachricht konnte nicht gel&ouml;scht werden!');
							}
		
							break;
						case "markread":
							try {
								$mailModel->getAccount()->setFlags($mailModel->getAccount()->getNumberByUniqueId($messageId), array(Storage::FLAG_SEEN));
							} catch(Exception $e) {
								$error = $_LANG->get('Die Nachricht konnte nicht als gelesen markiert werden!');
							}
		
							break;
						case "markunread":
							try {
								$mailModel->getAccount()->setFlags($mailModel->getAccount()->getNumberByUniqueId($messageId), array(Storage::FLAG_PASSED));
							} catch(Exception $e) {
								$error = $_LANG->get('Die Nachricht konnte nicht als ungelesen markiert werden!');
							}
							
							break;
					}
					$mailModel->getAccount()->noop();
				}
			}
		}
		
		// --------------------------------------------- neue E-Mail verfassen --------------------------------------------------
		
		// Prepare template variables.
		$currentFolder = $mailModel->getAccount()->getCurrentFolder();
		$folders = new RecursiveIteratorIterator($mailModel->getAccount()->getFolders(), RecursiveIteratorIterator::SELF_FIRST);
		// foreach($folders as $folder) {
			// print_r($folder);
		// }
		
		// if action is not set to newmail and a message object is not set,
		// fetch all messages
		if($action == "newmail") {
			$contentTemplate = "newMessage.template.php";
		} else {
			if($messageObject == null) {
				$limit = 30;
				$page = getRequestVarOr("p", 1);
				$totalResults = $mailModel->getAccount()->countMessages();
				$totalPages = ceil($totalResults / $limit);
				if($page > $totalPages){
					$page = $totalPages;
				}
				
				
				$startfrom = ($totalResults - ($page * $limit)) + $limit;
				$itomin = $startfrom - $limit;
				if($itomin < 0)
					$itomin = 0;
				
				// echo "Startfrom: " . $startfrom . "</br>";
				// echo "totalResults: " . $totalResults . "</br>";
				// echo "totalPages: " . $totalPages . "</br>";
				// echo "itomin: " . $itomin . "</br>";
				
				$messages = array();
				if ($totalResults > 0) {
					for($i = $startfrom; $i > $itomin; $i--){
						$mailbox = $mailModel->getAccount();
						// $startTime = microtime(true);
						$message = $mailbox[$i];
						try {
							array_push($messages, array(
								"id"		=> $mailModel->getAccount()->getUniqueId($i),
								"from"		=> $message->from,
								"to"		=> $message->to,
								"subject" 	=> $message->subject,
								"date"		=> strtotime($message->date),
								"number"	=> $i,
								"flags"		=> $message->getFlags()
							));
						} catch(Exception $e) {}
						// echo "Time:  " . number_format(( microtime(true) - $startTime), 4) . " Seconds</br>";
						// $mailModel->getAccount()->noop();
					}
				}
				
				$contentTemplate = "overview.template.php";
			} else {
				$contentTemplate = "message.template.php";
			}
		}
	
		// Render template.
		require_once 'template/main.template.php';
	}
}

function getMessageObject($messageId, $usedMailModel) {
	$messageObject = array();
	
	$message = $usedMailModel->getAccount()->getMessage($messageId);
	
// 	var_dump($message);
	
	$attachments = array();
	$mailContent = "";
	
	// get the mail content and attachments.
	if($message->isMultipart()) {
		$mailContent = "";
		foreach (new RecursiveIteratorIterator($message) as $part) {
			try {
				$contentType = strtok($part->contentType, ";");
				if($mailContent == "") {
					$mailContent = getDecodedTextMessage($part);
				}
			} catch(Exception $e) {}

			try {
				$filename = $part->getHeaderField("Content-Disposition", "filename");
				if(isset($filename)) {
					// File operations
					if(!file_exists("docs/attachments/".$filename)) {
						$fh = fopen("docs/attachments/".$filename, "w");
						fwrite($fh, base64_decode($part->getContent()));
						fclose($fh);
					}
						
					array_push($attachments, array("filename" => $filename, "link" => "docs/attachments/".$filename));
				}
			} catch(Exception $e) {}
		}
	} else {
		$mailContent = getDecodedTextMessage($message);
	}	

	if($mailContent != "") {
 		if(!(iconv('utf-8', 'utf-8//IGNORE', $mailContent) == $mailContent)) {
			$mailContent = utf8_encode($mailContent);
		}
	}
	
	// prepare the object.
	$fromAddr = $message->from;
	if(strpos($fromAddr,'<') !== false) {
		$start  = strpos($fromAddr, '<');
		$email  = substr($fromAddr, $start, -1);
		$fromAddr = str_replace('<', '', $email);
	}
	
	$toAddr = $message->to;
	if(strpos($toAddr,'<') !== false) {
		$start  = strpos($toAddr, '<');
		$email  = substr($toAddr, $start, -1);
		$toAddr = str_replace('<', '', $email);
	}

	$messageObject['from'] = $fromAddr;
	$messageObject['to'] = $toAddr;
	$messageObject['subject'] = $message->subject;
	$messageObject['content'] = $mailContent;
	$messageObject['attachments'] = $attachments;
	
	return $messageObject;
}


function getDecodedTextMessage($encodedMessage) {
	$decodedMessage = "";

	$contentType = "";
	try {
		$contentType = strtok($encodedMessage->contentType, ";");
	} catch(Exception $e) {}

	if($contentType == "text/plain") {
		try {
			$contentEncoding = strtolower($encodedMessage->contentTransferEncoding);
			if($contentEncoding == "quoted-printable") {
				$decodedMessage = quoted_printable_decode($encodedMessage);
			} else if($contentEncoding == "base64") {
				$decodedMessage = base64_decode($encodedMessage);
			} else {
				$decodedMessage = $encodedMessage;
			}
		} catch(Exception $e) {
 			$decodedMessage = $encodedMessage;
		}
	} else {
	    $decodedMessage = $encodedMessage;
	}
	
	return $decodedMessage;
}

// Update the mail counter.
$totalCount = 0;
foreach($_USER->getEmailAddresses() as $emailAddress) {

	try {
		// Create a new MailModel instance.
		$mailModel = new MailModel($emailAddress, "INBOX");
		if($mailModel->getAccount()) {
			$totalCount +=$mailModel->getAccount()->countMessages();
		}
	} catch(Exception $e) {} 
}

echo '<script language="JavaScript">document.getElementById("mail_counter").innerHTML = "'.$_LANG->get('Posteingang: ').$totalCount.'";</script>';
?>