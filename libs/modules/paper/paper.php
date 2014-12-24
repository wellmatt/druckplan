<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       12.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

require_once 'paper.class.php';

$_REQUEST["id"] = (int)$_REQUEST["id"];

if($_REQUEST["exec"] == "delete")
{
    $paper = new Paper($_REQUEST["id"]);
    $savemsg = getSaveMessage($paper->delete());
}

if($_REQUEST["exec"] == "edit" || $_REQUEST["exec"] == "new" || $_REQUEST["exec"] == "copy")
{
    require_once 'paper.add.php';
} else
{
    $papers = Paper::getAllPapers(Paper::ORDER_NAME);
?>

<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Papiere')?></td>
      <td><?=$savemsg?></td>
      <td width="200" class="content_header" align="right"><a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit"><img src="images/icons/script--plus.png"> <?=$_LANG->get('Papier hinzuf&uuml;gen')?></a></td>
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
        <td class="content_row_header"><?=$_LANG->get('Gr&ouml;&szlig;en')?></td>
        <td class="content_row_header"><?=$_LANG->get('Grammaturen')?></td>
        <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
    </tr>
    
    <? $x = 0;
    foreach($papers as $p)
    {?>
    <tr class="<?=getRowColor($x)?>">
        <td class="content_row">&nbsp;</td>
        <td class="content_row"><?=$p->getName()?></td>
        <td class="content_row"><? foreach($p->getSizes() as $s) echo $s["width"]."x".$s["height"]." "?></td>
        <td class="content_row"><? foreach($p->getWeights() as $w) echo $w."g "?></td>
        <td class="content_row">
            <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$p->getId()?>"><img src="images/icons/pencil.png"></a>
            <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=copy&id=<?=$p->getId()?>"><img src="images/icons/scripts.png"></a>
            <a class="icon-link" href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$p->getId()?>')"><img src="images/icons/cross-script.png"></a>
        </td>
    </tr>
      
    <? $x++; }
    ?>
</table>
</div>
<?  } ?>





