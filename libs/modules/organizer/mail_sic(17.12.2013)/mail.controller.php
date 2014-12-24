<?
require_once "mailModel.class.php";

use Zend\Mail;
use Zend\Mail\Headers;
use Zend\Mail\Storage;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;

// Fetch the selected folder.
$selectedFolder = urldecode(getRequestVarOr("folder", "INBOX"));

// Fetch all folders/messages for each registered account.
foreach($_USER->getEmailAddresses() as $emailAddress) {

	// Create a new MailModel instance.
	$mailModel = new MailModel($emailAddress, $selectedFolder);

	// Check if messages need to be moved from one folder to another.
	$move = getRequestVarOr("move", null);

	// Collect selected message ids.
	$messageIds = array();
	foreach(array_keys($_REQUEST) as $key) {
		if(preg_match("/chk_msg_(?P<id>\d+)/", $key, $match)) {
			array_push($messageIds, intval($match["id"]));
		}
	}

	if($move !=null) {
		$destinationFolder = urldecode($move);
		foreach($messageIds as $messageId) {
			$mailModel->getAccount()->moveMessage($mailModel->getAccount()->getNumberByUniqueId($messageId), $destinationFolder);
		}
	}
	
	// Check if an action has been executed.
	$action = getRequestVarOr("exec", null);
	
	if($action != null) {
		if($action == "newmail" && getRequestVarOr("subexec", null) == "send") {
			$recevier = getRequestVarOr("mail_to", null);
			$subject = getRequestVarOr("mail_subject", $_LANG->get('Kein Betreff'));
			$body = getRequestVarOr("mail_body", "");

			if($subject != null) {	
				$mail = new Message();
				$mail->setBody($body);
				$mail->setFrom($emailAddress->getAddress());
				$mail->addTo($recevier);
				$mail->setSubject($subject);
				
				$mailModel->sendMail($mail);
			}
		} else {
			foreach($messageIds as $messageId) {
				$mailNumber = $mailModel->getAccount()->getNumberByUniqueId($messageId);
				switch($action) {
					case "delete":
						$mailModel->getAccount()->removeMessage($mailNumber);
	
						break;
					case "markread":
						$mailModel->getAccount()->setFlags($mailNumber, array(Storage::FLAG_SEEN));
	
						break;
					case "markunread":
						$mailModel->getAccount()->setFlags($mailNumber, array(Storage::FLAG_RECENT));
	
						break;
				}
			}
		}
	}
	
	// Prepare template variables.
	$currentFolder = $mailModel->getAccount()->getCurrentFolder();
	$folders = new RecursiveIteratorIterator($mailModel->getAccount()->getFolders(), RecursiveIteratorIterator::SELF_FIRST);
	$showMessage = getRequestVarOr("message", null);
	
	// if action is not set to newmail and message is not set,
	// fetch all messages
	if($action == "newmail") {
		$contentTemplate = "newMessage.template.php";
	} else {
		if($showMessage == null) {
			$messages = array();
			foreach($mailModel->getAccount() as $messageNumber => $message) {
				array_push($messages, array(
					"id"		=> $mailModel->getAccount()->getUniqueId($messageNumber),
					"from"		=> $message->from,
					"subject" 	=> $message->subject,
					"date"		=> strtotime($message->date)	
				));
			}
			
			$contentTemplate = "overview.template.php";
		} else {
			$message = $mailModel->getAccount()->getMessage($mailModel->getAccount()->getNumberByUniqueId($showMessage));

			$attachments = array();
			if($message->isMultipart()) {
				$mailContent = "";
				foreach (new RecursiveIteratorIterator($message) as $part) {
					$contentType = strtok($part->contentType, ";");
					if($contentType == "text/plain") {
						try {
							if(strtolower($part->contentTransferEncoding) == "quoted-printable") {
								$mailContent = quoted_printable_decode($part);
							} else {
								$mailContent = $part;
							}
						} catch(Exception $e) {
							$mailContent = $part;
						}
					}
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
				$mailContent = $message->getContent();
			}

			$contentTemplate = "message.template.php";
		}
	}
	
	// Render template.
	require_once 'template/main.template.php';
}

// Update the mail counter.
$totalCount = 0;
foreach($_USER->getEmailAddresses() as $emailAddress) {

	// Create a new MailModel instance.
	$mailModel = new MailModel($emailAddress, "INBOX");
	$totalCount +=$mailModel->getAccount()->countMessages(); 
}


echo '<script language="JavaScript">document.getElementById("mail_counter").innerHTML = "'.$_LANG->get('Posteingang: ').$totalCount.'";</script>';
?>