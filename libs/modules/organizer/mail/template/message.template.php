<?php
echo $_LANG->get('Absender').':&nbsp;&nbsp;&nbsp;&nbsp;'.$messageObject["from"]."<br/>";
echo $_LANG->get('Empf&auml;nger').':&nbsp;&nbsp;'.$messageObject["to"]."<br/>";
echo $_LANG->get('Betreff').':&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$messageObject["subject"]."<br/>";
if($messageObject["content"] != "") {
	echo "<pre>".$messageObject["content"]."</pre><br><br>";
} else {
	echo "<pre>Kein reiner Text-Teil in dieser Nachricht gefunden.</pre>";
}

if(count($messageObject["attachments"]) > 0) {
	echo "Anhänge(".count($messageObject["attachments"])."):<br>";

	foreach($messageObject["attachments"] as $attachment) {
		echo '<a class="mail_attachment" href="'.$attachment["link"].'">'.$attachment["filename"].'</a><br>';
	}
}

// print_r($messageObject);

?>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post">
	<input type="hidden" name="exec" value="newmail">
	<input type="hidden" name="subexec" value="answer">
	<input type="hidden" name="folder" value="<?=urlencode($currentFolder)?>">
	<input type="hidden" name="emailId" value="<?=$emailId?>">
	<input type="hidden" name="message" value="<?=$messageObject["id"]?>">
	<input type="submit" value="<?=$_LANG->get('Antworten')?>">
</form>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post">
	<input type="hidden" name="exec" value="newmail">
	<input type="hidden" name="subexec" value="forward">
	<input type="hidden" name="folder" value="<?=urlencode($currentFolder)?>">
	<input type="hidden" name="emailId" value="<?=$emailId?>">
	<input type="hidden" name="message" value="<?=$messageObject["id"]?>">
	<input type="submit" value="<?=$_LANG->get('Weiterleiten')?>">
</form>
