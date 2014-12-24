<?php
echo "<pre>".$mailContent."</pre><br><br>";
if(count($attachments) > 0) {
	echo "Anhänge(".count($attachments)."):<br>";

	foreach($attachments as $attachment) {
		echo '<a class="mail_attachment" href="'.$attachment["link"].'">'.$attachment["filename"].'</a><br>';
	}
} 
?>
