<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       27.02.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

if ($_REQUEST["exec"] == "edit")
{
    require_once('user.add.php');
} else
{

    if ($_REQUEST["exec"] == "delete")
    {
        $user = new User($_REQUEST["id"]);
        $user->delete();
    }
    $users = User::getAllUser(User::ORDER_LOGIN);

	$usercount = count($users);
	if ($usercount >= $_CONFIG->maxusers){
		$newusers = false;
	} else {
		$newusers = true;
	}
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
					Benutzer
					<span class="pull-right">
						<?php if ($newusers){?>
							<button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=<?=$_REQUEST['page']?>&exec=edit';" >
								<span class="glyphicons glyphicons-plus"></span>
								<?=$_LANG->get('Benutzer hinzuf&uuml;gen')?>
							</button>
						<?php } else {?>
							Maximale Anzahl an Benutzern erreicht.
						<?php } ?>
					</span>
				</h3>
		  </div>
		  <div class="table-responsive">
			  <table class="table table-hover datatablegeneric">
				  <thead>
					  <tr>
						  <th class="content_row_header"><?=$_LANG->get('ID')?></th>
						  <th class="content_row_header"><?=$_LANG->get('Benutzername')?></th>
						  <th class="content_row_header"><?=$_LANG->get('Voller Name')?></th>
						  <th class="content_row_header"><?=$_LANG->get('Typ')?></th>
						  <th class="content_row_header"><?=$_LANG->get('Gruppen')?></th>
						  <? if($_USER->isAdmin()) echo '<th class="content_row_header">'.$_LANG->get('Mandant').'</th>';?>
						  <th class="content_row_header"><?=$_LANG->get('Aktiv')?></th>
						  <th class="content_row_header"><?=$_LANG->get('Optionen')?></th>
					  </tr>
				  </thead>

				  <?
				  $x = 0;
				  foreach ($users as $u) {

					  ?>
					  <tr class="<?= getRowColor($x) ?>">
						  <td class="content_row"><?= $u->getId() ?></td>
						  <td class="content_row"><?= $u->getLogin() ?></td>
						  <td class="content_row"><?= $u->getFirstname() ?> <?= $u->getLastname() ?>
						  </td>
						  <td class="content_row"><? if ($u->isAdmin()) echo $_LANG->get('Administrator'); else echo $_LANG->get('Benutzer'); ?>
						  </td>
						  <td class="content_row"><?
							  $groupnames = [];
							  foreach ($u->getGroups() as $group) {
								  $groupnames[] = $group->getName();
							  }
							  echo implode(',', $groupnames); ?> </td>
						  <? if ($_USER->isAdmin()) echo '<td class="content_row">' . $u->getClient()->getName() . '</td>'; ?>
						  <td class="content_row"><? if ($u->isActive()) echo '<img src="images/status/green_small.gif">'; else echo '<img src="images/status/red_small.gif">'; ?>
						  </td>
						  <td class="content_row">
							  <a class="icon-link"
								 href="index.php?page=<?= $_REQUEST['page'] ?>&exec=edit&id=<?= $u->getId() ?>"><span
									  class="glyphicons glyphicons-pencil pointer"></span></a>
							  <a class="icon-link" href="#"
								 onclick="askDel('index.php?page=<?= $_REQUEST['page'] ?>&exec=delete&id=<?= $u->getId() ?>')"><span
									  style="color: red;" class="glyphicons glyphicons-remove pointer"></span></a>
						  </td>
					  </tr>
					  <?
					  $x++;
				  }
				  ?>
			  </table>
		  </div>
	</div>

<?}
?>