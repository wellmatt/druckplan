<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       12.07.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

require_once 'paperformat.class.php';
$_REQUEST["id"] = (int)$_REQUEST["id"];
if($_REQUEST["exec"] == "delete")
{
    $format = new Paperformat($_REQUEST["id"]);
    $savemsg = getSaveMessage($format->delete());
}

if($_REQUEST["exec"] == "edit" || $_REQUEST["exec"] == "new" || $_REQUEST["exec"] == "copy")
{
    require_once 'paperformats.edit.php';
} else
{
    $formats = Paperformat::getAllPaperFormats(Paperformat::ORDER_NAME);
    ?>
<table width="100%">
	<tr>
		<td width="200" class="content_header"><img
			src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Produktformate')?>
		</td>
		<td><?=$savemsg?></td>
		<td width="200" class="content_header" align="right"><a  class="icon-link"
			href="index.php?page=<?=$_REQUEST['page']?>&exec=edit"><img
				src="images/icons/document-resize.png"> <?=$_LANG->get('Format hinzuf&uuml;gen')?>
		</a></td>
	</tr>
</table>
<div class="box1">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<colgroup>
			<col width="20">
			<col>
			<col>
			<col>
			<col width="100">
		</colgroup>
		<tr>
			<td class="content_row_header">&nbsp;</td>
			<td class="content_row_header"><?=$_LANG->get('Name')?></td>
			<td class="content_row_header"><?=$_LANG->get('Breite')?></td>
			<td class="content_row_header"><?=$_LANG->get('H&ouml;he')?></td>
			<td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
		</tr>

		<? $x = 0;
		foreach($formats as $f)
		{?>
		<tr class="<?=getRowColor($x)?>">
			<td class="content_row">&nbsp;</td>
			<td class="content_row"><?=$f->getName()?>&nbsp;</td>
			<td class="content_row"><?=$f->getWidth()?> mm</td>
			<td class="content_row"><?=$f->getHeight()?> mm</td>
			<td class="content_row">
			    <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$f->getId()?>"><img src="images/icons/pencil.png"></a>
			    <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=copy&id=<?=$f->getId()?>"><img src="images/icons/scripts.png"></a>
			    <a class="icon-link" href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$f->getId()?>')"><img src="images/icons/cross-script.png"></a>
			</td>
		</tr>

		<? $x++; }
	?>
	</table>
</div>
<? }?>