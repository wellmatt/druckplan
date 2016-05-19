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
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
               <img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
               Gruppen
               <span class="pull-right">
                  <img <img src="images/icons/users.png">
                  <button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=<?=$_REQUEST['page']?>&exec=edit';" >
                     <?=$_LANG->get('Gruppe hinzuf&uuml;gen')?>
                  </button>
               </span>
            </h3>
	  </div>
         <div class="table-responsive">
            <table class="table table-hover">
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
</div>
<?}?>