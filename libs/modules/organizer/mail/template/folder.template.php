<?
foreach($folders as $folder) {
	// if(strpos($folder, '/') == FALSE) {
		if($currentFolder == $folder) {
			$imageSource = "images/icons/folder-open.png";
		} else {
			$imageSource = "images/icons/folder.png";
		}
	
		$link = "index.php?page=".$_REQUEST['page']."&folder=".urlencode($folder->getGlobalName())."&emailId=".$emailId;
	
		if($folder->isSelectable()) {
			echo '<a class="mail_folder" href="'.$link.'">';
		}
	
	 	echo '<p class="msg_folder pointer"><img src="'.$imageSource.'"/> '.utf7_decode($folder->getGlobalName()).'</p>';
		
		if($folder->isSelectable()) {
			echo '</a>';
		}
	// }
}
?>
<a class="mail_folder" id="createNewImapFolder" href="">
    <img src="images/icons/folder--plus.png" alt="Neuer Ordner..."/> [Neuer Ordner &hellip;]
</a>

<form action="" method="post" style="display: none; margin-top: 1em" id="newImapFolderForm">
    <label for="imapNewFolderName">Ordername</label>
    <input type="hidden" name="exec" value="createImapFolder"/>
    <input type="text" name="imapNewFolderName" id="imapNewFolderName" placeholder="" />
    <button type="submit">Erstellen</button>
</form>

<script>
    $(document).ready(function() {
        var $newImapForm = $('#newImapFolderForm');
        $('#createNewImapFolder').on('click', function(e) {
            e.preventDefault();
            $newImapForm.toggle();
        });
        $newImapForm.on('submit', function(e) {
            var $input = $('#imapNewFolderName'),
                inputVal = $input.val();

            if($.trim(inputVal) == '') {
                alert("Bitte geben Sie einen Ordernamen ein!");
                return false;
            }

            var confirm = window.confirm('Soll der Ordner "' + inputVal + '" auf Ihrem E-Mail-Server wirklich erstellt werden?');
            if(!confirm) {
                return false;
            }

            return true; // proceed



        });
    });
</script>