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
   <!-- DataTables -->
   <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
   <link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
   <script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
   <script type="text/javascript" charset="utf8" src="jscripts/datatable/numeric-comma.js"></script>
   <script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.bootstrap.js"></script>
   <link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">
   <script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.tableTools.js"></script>

   <script>
      $(document).ready(function() {
         $('.datatablegeneric').DataTable( {
            "paging": true,
            "stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
            "pageLength": <?php echo $perf->getDt_show_default();?>,
            "dom": 'flrtip',
            "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Alle"] ],
            "language": {
               "url": "jscripts/datatable/German.json"
            }
         } );
      } );
   </script>

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
               <table class="table table-hover datatablegeneric">
                  <thead>
                     <tr>
                        <th><?=$_LANG->get('ID')?></th>
                        <th><?=$_LANG->get('Gruppenname')?></th>
                        <th><?=$_LANG->get('Beschreibung')?></th>
                        <th><?=$_LANG->get('Mitglieder')?></th>
                        <th><?=$_LANG->get('Optionen')?></th>
                     </tr>
                  </thead>

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