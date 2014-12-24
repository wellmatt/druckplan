<?php
if ($_REQUEST["exec"] == "edit")
   require_once('groups.add.php');
else
{

if ($_REQUEST["exec"] == "delete")
{
   $group = new Group($_REQUEST["id"]);
   $group->delete();
}

$groups = Group::getAllGroups(Group::ORDER_NAME);
?>
<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Gruppen')?></td>
      <td></td>
      <td width="200" class="content_header" align="right"><a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit"><img src="images/icons/users.png"> <?=$_LANG->get('Gruppe hinzuf&uuml;gen')?></a></td>
   </tr>
</table>

<div class="box1">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
   <colgroup>
      <col width="20">
      <col width="150">
      <col width="200">
      <col>
      <col width="80">
   </colgroup>
   <tr>
      <td class="content_row_header"><?=$_LANG->get('ID')?></td>
      <td class="content_row_header"><?=$_LANG->get('Gruppenname')?></td>
      <td class="content_row_header"><?=$_LANG->get('Beschreibung')?></td>
      <td class="content_row_header"><?=$_LANG->get('Mitglieder')?></td>
      <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
   </tr>
   
   <?
   $x = 0;
   foreach($groups as $group)
   {?>
   <tr class="<?=getRowColor($x)?>">
      <td class="content_row"><?=$group->getId()?></td>
      <td class="content_row"><?=$group->getName()?></td>
      <td class="content_row"><?=$group->getDescription()?></td>
      <td class="content_row">
         <? $str = "";
            foreach ($group->getMembers() as $m)
            {
               $str .= $m->getLogin().", ";
            }
            echo substr($str, 0, -2);
         ?>&nbsp;
      </td>
      <td class="content_row">
         <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$group->getId()?>"><img src="images/icons/pencil.png"></a>
         <a class="icon-link" href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$group->getId()?>')"><img src="images/icons/cross-script.png"></a>
      </td>
   </tr>
   
   <?$x++;}?>
</table>
</div>
<?}?>