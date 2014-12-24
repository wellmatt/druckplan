<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="msg_list_form" name="msg_list_form">
	<input name="folder" value="<?=urlencode($currentFolder)?>" type="hidden"/>
	<table width="100%" cellspacing="0" cellpadding="0">
		<colgroup>
			<col width="20">
			<col width="20">
			<col width="150">
			<col>
			<col width="150">
		</colgroup>
		<tr>
			<td class="content_row_header">&nbsp;</td>
			<td class="content_row_header">&nbsp;</td>
			<td class="content_row_header"><?=$_LANG->get('Von')?></td>
			<td class="content_row_header"><?=$_LANG->get('Betreff')?></td>
			<td class="content_row_header"><?=$_LANG->get('Datum')?></td>
		</tr>
<?
use Zend\Mail\Headers;

$x = 1;
foreach ($messages as $message) {
	$from = $message["from"];
	$subject = $message["subject"];
	$date = date('d.m.Y - H:m:s', $message["date"]);
	
	$link = "index.php?folder=".urlencode($currentFolder)."&message=".$message["id"];
?>
		<tr class="pointer <?=getRowColor($x)?>" id="msg_<?=$message["id"]?>" onmouseover="mark(this,0)" onmouseout="mark(this,1)">
			<td class="content_row"><input type="checkbox" id="chk_msg" name="chk_msg_<?=$message["id"]?>" value="1" /></td>
			<td class="content_row" onclick="document.location='<?=$link?>'"><img src="images/icons/mail.png" /></td>
			<td class="content_row" onclick="document.location='<?=$link?>'"><?=$from?></td>
			<td class="content_row" onclick="document.location='<?=$link?>'"><?=$subject?></td>
			<td class="content_row" onclick="document.location='<?=$link?>'"><?=$date?></td>
		</tr>
<?
$x++;}
?>
	</table>
	<br><br>
	<select name="exec" style="width:180px" onchange="document.msg_list_form.submit();">
	    <option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
	    <option value="delete"><?=$_LANG->get('L&ouml;schen')?></option>
	    <option value="markread"><?=$_LANG->get('Als gelesen markieren')?></option>
	    <option value="markunread"><?=$_LANG->get('Als ungelesen markieren')?></option>
	</select>
	&nbsp;&nbsp;
	<select name="move" style="width:180px" onchange="document.msg_list_form.submit();">
	    <option value="">&lt; <?=$_LANG->get('Verschieben nach...')?> &gt;</option>
<?
foreach($folders as $folder) {
echo '<option value="'.urlencode($folder->getGlobalName()).'">'.utf7_decode($folder->getLocalName()).'</option>';
}
?>
	</select>
</form>