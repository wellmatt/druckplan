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
               Gruppen
               <span class="pull-right">
                  <button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=<?=$_REQUEST['page']?>&exec=edit';" >
                     <span class="glyphicons glyphicons-plus pointer"></span>
                     <?=$_LANG->get('Gruppe hinzuf&uuml;gen')?>
                  </button>
               </span>
            </h3>
	  </div>
         <div class="table-responsive">
            <table class="table table-hover">
               <tr>
                  <td><?=$_LANG->get('ID')?></td>
                  <td><?=$_LANG->get('Gruppenname')?></td>
                  <td><?=$_LANG->get('Beschreibung')?></td>
                  <td><?=$_LANG->get('Mitglieder')?></td>
                  <td><?=$_LANG->get('Optionen')?></td>
               </tr>

               <?
               $x = 0;
               foreach($groups as $group)
               {?>
                  <tr class="<?=getRowColor($x)?>">
                     <td><?=$group->getId()?></td>
                     <td><?=$group->getName()?></td>
                     <td><?=$group->getDescription()?></td>
                     <td>
                        <?php
                        $userlogins = [];
                        foreach ($group->getMembers() as $member) {
                           $userlogins[] = $member->getLogin();
                        }
                        echo implode(', ',$userlogins);
                        ?>&nbsp;
                     </td>
                     <td class="content_row">
                        <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$group->getId()?>"><span class="glyphicons glyphicons-pencil pointer"></span></a>
                        <a class="icon-link" href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$group->getId()?>')">
                           <span style="color: red;" class="glyphicons glyphicons-remove pointer"></span>
                        </a>
                     </td>
                  </tr>

                  <?$x++;}?>
            </table>
         </div>
</div>
<?}?>