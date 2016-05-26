<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			12.09.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

require_once 'libs/modules/chat/chat.class.php';

if($_REQUEST["exec"]=="setchatread"){
	$chat_edit = new Chat((int)$_REQUEST["chatid"]);
	$chat_edit->setState(2);
	$chat_edit->save();
}

$partner_id = (int)$_REQUEST["chat_partner"];

$all_chats = Chat::getChatProtokoll(Chat::ORDER_DATE, $partner_id, $_USER->getID());
$all_user = User::getAllUser(); 
?>

<script language="javascript">
function markasread(id){
	$.post("libs/modules/chat/chat.ajax.php", 
			{exec: 'markasread', id:id}, 
			 function(data) {
				 if(data == 1){
					 document.getElementById('chat_'+id).style.display='none' ;
					 document.getElementById('chat2_'+id).style.display='none' ;
				 }
			});
	return false;
}
</script>

<table width="100%">
	<tr>
		<td width="200" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"><span style="font-size: 13px"> <?=$_LANG->get('Artikel')?> </span>
		</td>
		<td><?=$savemsg?></td>
		<td width="200" class="content_header" align="right">
			&emsp;
		</td>
	</tr>
</table>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="xform_ticketsearch">
	<input type="hidden" name="subexec" value="search">
	<input type="hidden" name="mid" value="<?=$_REQUEST["mid"]?>">
	<div class="box2" style="width:600px">
		<table width="100%">
			<colgroup>
				<col width="120">
				<col>
				<col width="140">
			</colgroup>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('ChatPartner');?></td>
				<td>
					<select type="text" id="chat_partner" name="chat_partner" style="width:200px"
							onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
						<option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
						<? 	foreach ($all_user as $us){?>
							<option value="<?=$us->getId()?>"
									<?if($us->getID() == $partner_id) echo 'selected';?>><?= $us->getNameAsLine()?></option>
						<?	} //Ende ?>
					</select>
				</td>
				<td class="content_row_clear" align="right" colspan="4">
					<input type="submit" value="<?=$_LANG->get('Suche starten')?>">
				</td>
			</tr>
		</table>
	</div>
</form>

<div class="box1">
<? if($partner_id == 0){
	echo '<p style="text-align:center" ><b>Bitte Chat-Partner ausw&auml;hlen</b></p>';	
}else{
	if(count($all_chats) == 0 || $all_chats == false){
		echo '<p style="text-align:center" ><b>Keine Chats vorhanden</b></p>';
	}else{?>
		<table width="100%">
		<tr><td class="content_row_header"><?= $_LANG->get('Chat Protokoll');?></td></tr>
	<?	$x=0;
		foreach ($all_chats AS $chat){ ?>
			<tr class="<?=getRowColor($x)?>">
				<td class="content_row">
			<?	echo "<b>".$chat->getFrom()->getNameAsLine()."</b> ".$_LANG->get('schrieb am')." <b>".date("d.m.Y - H:i", $chat->getCrtdate()).":</b> ";
				//  && ($chat->getTo()->getId() != $_USER->getId() || $partner_id == $_USER->getId())
				if ($chat->getState() == 1){?>
					<span class="glyphicons glyphicons-option-horizontal" id="chat2_<?=$chat->getId()?>"
						 <?if ($chat->getTo()->getId() == $_USER->getId()){?>
						 	onclick="markasread(<?=$chat->getId()?>)" class="pointer"  title="<?=$_LANG->get('Als gelesen markieren');?>" 
						 <?} else {?>
						 	 title="<?=$_LANG->get('Ungelesen');?>" 
						 <?}?>
					></span>
			<?	}
				echo " ".$chat->getTitle()."<br/>";
				echo " ".nl2br($chat->getComment() );?>
				</td>
			</tr>
	<?		$x++;
		} ?>	
		</table>	
<?	} 
}?>

</div>