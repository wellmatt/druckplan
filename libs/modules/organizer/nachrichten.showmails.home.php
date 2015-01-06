<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------


require_once "libs/modules/organizer/mail/mailModel.class.php";
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/calculation/calculation.class.php';

use Zend\Mail;
use Zend\Mail\Headers;
use Zend\Mail\Storage;
use Zend\Mail\Message;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;
use Zend\Mail\Transport\Sendmail;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
$gotmail = false;
$gotaccount = false;

$userEmailAdresses = $_USER->getEmailAddresses();

// Fetch the selected folder.
$selectedFolder = "INBOX";

if($emailId == null) {
	foreach($userEmailAdresses as $emailAddress) {
		if($emailId == null) $emailId = $emailAddress->getId();
	}
}

if($emailId == null) {
	$error = 'Keine E-Mail Konten hinterlegt';
	echo '<font color="#FF0000">'.$error.'</font><br/><br/>';
	$gotmail = false;
}
else
{
	$emailIds = array();
	foreach($userEmailAdresses as $emailAddress) {
		$emailIds[$emailAddress->getAddress()] = $emailAddress->getId();
	}
	
	// Fetch all folders/messages for each registered account.
	$messages = array();
	
	foreach($userEmailAdresses as $emailAddress) {
		if($emailAddress->getId() != $emailId) {
			continue;
		}

		// Create a new MailModel instance.
		$mailModel = new MailModel($emailAddress, $selectedFolder);
		if(!$mailModel->getAccount()) {
			$error = 'Verbindung zu '.$emailAddress->getAddress().' konnte nicht hergestellt werden.';
			echo '<font color="#FF0000">'.$error.'</font><br/><br/>';
			$gotmail = false;
			$gotaccount = false;
			$folders = array();
			continue;
		}
		else
		{
			
			$totalResults = $mailModel->getAccount()->countMessages();
			// echo "TotalResults = " . $totalResults . "</br>";
			
			$itomin = $totalResults-11;
			if ($itomin < 0)
				$itomin = 0;
			
			for($i = $totalResults; $i > $itomin; $i--){
				// echo "MessageID: " . $i . "</br>";
				$mailbox = $mailModel->getAccount();
				$message = $mailbox[$i];
				try {
					array_push($messages, array(
						"id"		=> $mailModel->getAccount()->getUniqueId($i),
						"from"		=> $message->from,
						"to"		=> $message->to,
						"subject" 	=> $message->subject,
						"date"		=> strtotime($message->date),
						"number"	=> $i
					));
				} catch(Exception $e) {}
				$mailModel->getAccount()->noop();
			}
			$currentFolder = $mailModel->getAccount()->getCurrentFolder();
		}
	}
}
?>

<h3 class="text-right" style="padding-top:0px; border-bottom: 1px solid #555;"><i class="fa fa-inbox"></i> meine E-Mails
<? if ($emailId != null) { ?>
<a href="index.php?page=libs/modules/organizer/nachrichten.php&exec=newmail&folder=INBOX&emailId=<?=$emailId?>"><img src="images/icons/mail--plus.png" width="25" height="25"></a>
<? } ?>
</h3>

<table width="100%" cellspacing="0" cellpadding="0">
	<colgroup>
		<col width="20">
		<col width="150">
		<col width="150">
		<col width="150">
	</colgroup>
	<tr>
		<td class="content_row_header">&nbsp;</td>
		<td class="content_row_header"><?=$_LANG->get('Von')?></td>
		<td class="content_row_header"><?=$_LANG->get('Betreff')?></td>
		<td class="content_row_header"><?=$_LANG->get('Datum')?></td>
	</tr>
<?
if (count($messages) > 0) {
	$x = 1;
	foreach ($messages as $message) {
		$from = $message["from"];
		$subject = $message["subject"];
		$date = date('d.m.Y - H:m:s', $message["date"]);
		$link = "index.php?page=libs/modules/organizer/nachrichten.php&folder=".urlencode($currentFolder)."&message=".$message["id"]."&emailId=".$emailId;
	?>
			<tr class="pointer <?=getRowColor($x)?>" id="msg_<?=$message["id"]?>" onmouseover="mark(this,0)" onmouseout="mark(this,1)">
				<td class="content_row icon-link" onclick="document.location='<?=$link?>'"><img src="images/icons/mail.png" /></td>
				<td class="content_row icon-link" onclick="document.location='<?=$link?>'"><?=$from?></td>
				<td class="content_row icon-link" onclick="document.location='<?=$link?>'"><?=$subject?></td>
				<td class="content_row icon-link" onclick="document.location='<?=$link?>'"><?=$date?></td>
			</tr>
	<?
	$x++;
	if ($x == 10) { break; }
	}
}
?>
</table>