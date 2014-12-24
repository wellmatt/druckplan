<?
foreach($folders as $folder) {
	if($currentFolder == $folder->getGlobalName()) {
		$imageSource = "images/icons/folder-open.png";
	} else {
		$imageSource = "images/icons/folder.png";
	}

	$link = "index.php?folder=".urlencode($folder->getGlobalName());

	if($folder->isSelectable()) {
		echo '<a class="mail_folder" href="'.$link.'">';
	}

	echo '<p class="msg_folder pointer"><img src="'.$imageSource.'"/> '.utf7_decode($folder->getLocalName()).'</p>';

	if($folder->isSelectable()) {
		echo '</a>';
	}
}
?>