<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			07.07.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
chdir('../../../');
error_reporting(-1);
ini_set('display_errors', 1);
require_once('vendor/autoload.php');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/paper/paper.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/foldtypes/foldtype.class.php';
require_once 'libs/modules/paperformats/paperformat.class.php';
require_once 'libs/modules/products/product.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/chromaticity/chromaticity.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/finishings/finishing.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/collectiveinvoice/orderposition.class.php';
require_once 'libs/modules/personalization/personalization.order.class.php';
require_once 'libs/modules/notes/notes.class.php';
require_once "libs/modules/organizer/mail/mailModel.class.php";
use Zend\Mail;
use Zend\Mail\Headers;
use Zend\Mail\Storage;
use Zend\Mail\Message;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
session_start();
$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;
// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();
$_REQUEST["exec"] = trim(addslashes($_REQUEST["exec"]));
if ($_REQUEST["exec"] == "getOrdersForCustomer") {
	$customerId = (int)$_REQUEST['cust_id'];
	$all_orders = Order::getAllOrdersByCustomer("status DESC", $customerId);
	echo '<option value=""> &lt; '.$_LANG->get('Auftrag w&auml;hlen...').'&gt;</option>';								
	foreach ($all_orders as $order) {
		switch ($order->getStatus()) {
			case 2:
				$style = 'style="background-color: orange"';
				break;
			case 3:
				$style = 'style="background-color: yellow"';
				break;
			case 4:
				$style = 'style="background-color: purple; color: white;"';
				break;
			case 5:
				$style = 'style="background-color: green"';
				break;
			default:
				$style = 'style="background-color: black; color: white;"';
				break;
		}
		echo '<option value="'. $order->getId() .'" '.$style.'>'. $order->getNumber() ." - ". $order->getTitle().'</option>';
	}
}
if ($_REQUEST["exec"] == "getMessageContent") {
	$emailId = (int)$_REQUEST['emailId'];
	$selectedFolder = $_REQUEST['emailFolder'];
	$messageId = $_REQUEST['messageId'];
	$userEmailAdresses = $_USER->getEmailAddresses();
	foreach($userEmailAdresses as $emailAddress) {
		if($emailAddress->getId() != $emailId) {
			continue;
		}
		// Create a new MailModel instance.
		$mailModel = new MailModel($emailAddress, $selectedFolder);
		if(!$mailModel->getAccount()) {
			$error = 'Verbindung zu '.$emailAddress->getAddress().' konnte nicht hergestellt werden.';
			echo '<font color="#FF0000">'.$error.'</font><br/><br/>';
			continue;
		}
		else
		{
			$mailModel->getAccount()->setFlags($mailModel->getAccount()->getNumberByUniqueId($messageId), array(Storage::FLAG_SEEN));
			$messageObject = getMessageObject($mailModel->getAccount()->getNumberByUniqueId($messageId), $mailModel);
			// $messageObject["content"] = str_replace("\n", "</br>", $messageObject["content"]);
			// $messageObject["content"] = str_replace("</br></br>", "</br>", $messageObject["content"]);
			
			// $messageObject["content"] = nl2br($messageObject["content"]);
			// $messageObject["content"] = str_replace("<br />\n <br />\n<br />", "</br>", $messageObject["content"]);
			$html = '<td colspan="6"></br>';
			if($messageObject["content"]) {
				$html .= "<pre>".$messageObject["content"]."</pre><br><br>";
				// $html .= $messageObject["content"]."<br><br>";
			} else {
				$html .= "<pre>Kein reiner Text-Teil in dieser Nachricht gefunden.</pre>";
			}
			if(count($messageObject["attachments"]) > 0) {
				$html .= "Anhänge(".count($messageObject["attachments"])."):<br>";
				foreach($messageObject["attachments"] as $attachment) {
					$html .= '<a class="mail_attachment" target="_blank" href="'.$attachment["link"].'">'.$attachment["filename"].'</a><br>';
				}
			}
			$html .= '</br></td>';
			echo $html;
			// var_dump($messageObject["content"]);
		}
	}
	// echo "test";
}

function getMessageObject($messageId, $usedMailModel) {
	$messageObject = array();
	$message = $usedMailModel->getAccount()->getMessage($messageId);
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
					// $mailContent = imap_utf8($message);
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
		// $mailContent = imap_utf8($message);
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