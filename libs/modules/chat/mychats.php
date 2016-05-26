<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			10.09.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
//
//	Auflistung der eigenen Chat-Nachrichten, z.B. fuer den Seitenkopf
//
// ----------------------------------------------------------------------------------
require_once 'libs/modules/chat/chat.class.php';

if($_REQUEST["exec"]=="setchatread"){
	$chat_edit = new Chat((int)$_REQUEST["chatid"]);
	$chat_edit->setState(2);
	$chat_edit->save();
}

$all_chats = Chat::getAllChatsForMe(Chat::ORDER_DATE, $_USER->getID());
?>
<table>
<?
foreach ($all_chats AS $chat){
	echo '<tr class="tabellenlinie"><td>';
	echo "<u><b>".$chat->getFrom()->getNameAsLine()."</b> ".$_LANG->get('schrieb am')." <b>".date("d.m.Y - H:i", $chat->getCrtdate()).":</b></u> ";
	if ($chat->getState() == 1){?>
		<span class="glyphicons glyphicons-option-horizontal pointer" title="<?=$_LANG->get('Als gelesen markieren');?>" id="chat_<?=$chat->getId()?>"
			  onclick="markasread(<?=$chat->getId()?>)"></span>
		<?	}
	echo " <i>".$chat->getTitle()."</i><br/>";
	echo " ".$chat->getComment()."<br/>";
	echo '</td></tr>';
}


?>
<script language="javascript">
function markasread(id){
	$.post("libs/modules/chat/chat.ajax.php", 
			{exec: 'markasread', id:id}, 
			 function(data) {
				 if(data == 1){
					 document.getElementById('chat_'+id).style.display='none' ;
				 }
			});
	return false;
}
</script>