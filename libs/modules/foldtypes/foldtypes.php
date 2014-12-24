<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       14.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'foldtype.class.php';
$_REQUEST["id"] = (int)$_REQUEST["id"];

if($_REQUEST["exec"] == "delete")
{
    $ft = new Foldtype($_REQUEST["id"]);
    $savemsg = getSaveMessage($ft->delete());
} 

if($_REQUEST["exec"] == "copy" || $_REQUEST["exec"] == "edit")
{
    require_once 'foldtypes.edit.php';
} else
{
    $foldtypes = Foldtype::getAllFoldTypes(Foldtype::ORDER_NAME);
    
?>

<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Falzarten')?></td>
      <td><?=$savemsg?></td>
      <td width="200" class="content_header" align="right"><a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit"><img src="images/icons/foldtypes--plus.png"> <?=$_LANG->get('Falzart hinzuf&uuml;gen')?></a></td>
   </tr>
</table>
<div class="box1">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <colgroup>
        <col width="20">
        <col width="200">
        <col>
        <col width="80">
        <col width="80">
        <col width="100">
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('ID')?></td>
        <td class="content_row_header"><?=$_LANG->get('Bezeichnung')?></td>
        <td class="content_row_header"><?=$_LANG->get('Beschreibung')?></td>
        <td class="content_row_header"><?=$_LANG->get('Anz. vert.')?></td>
        <td class="content_row_header"><?=$_LANG->get('Anz. hor.')?></td>
        <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
    </tr>
    <? $x = 0;
    foreach($foldtypes as $f)
    {?>
        <tr class="<?=getRowColor($x)?>">
            <td class="content_row"><?=$f->getId()?></td>
            <td class="content_row"><?=$f->getName()?></td>
            <td class="content_row"><?=$f->getDescription()?></td>
            <td class="content_row" align="center"><?=$f->getVertical()?></td>
            <td class="content_row" align="center"><?=$f->getHorizontal()?></td>
            <td class="content_row">
                <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$f->getId()?>"><img src="images/icons/pencil.png"></a>
                <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=copy&id=<?=$f->getId()?>"><img src="images/icons/scripts.png"></a>
                <a class="icon-link" href="#"	onclick="askDel('index.php?page=<?=$_REQUEST["page"]?>&exec=delete&id=<?=$f->getId()?>')"><img src="images/icons/cross-script.png"></a>
            </td>
        </tr>
    
    <? $x++; }
    ?>
</table>
</div>
<? } ?>