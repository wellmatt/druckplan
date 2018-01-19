<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       03.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'machinegroup.class.php';
$_REQUEST["id"] = (int)$_REQUEST["id"];
if($_REQUEST["exec"] == "delete")
{
	$machinegroup = new MachineGroup($_REQUEST["id"]);
	$savemsg = getSaveMessage($machinegroup->delete());
}

if($_REQUEST["exec"] == "edit" || $_REQUEST["exec"] == "new" || $_REQUEST["exec"] == "copy")
{
	require_once 'machinegroup.edit.php';
} else
{
	$machinegroup = MachineGroup::getAllMachineGroups();
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
					Maschinengruppen
					<span class="pull-right">
						<button class="btn btn-xs btn-success" onclick="document.location. href='index.php?page=<?=$_REQUEST['page']?>&exec=edit';">
							<span class="glyphicons glyphicons-plus"></span>
							<?=$_LANG->get('Maschinengruppe hinzuf&uuml;gen')?>
						</button>
					</span>
				</h3>
		  </div>
		  <div class="table-responsive">
			  <table class="table table-hover datatablegeneric">
				  <thead>
					  <tr>
						  <th class="content_row_header"><?=$_LANG->get('Name')?></th>
						  <th class="content_row_header"><?=$_LANG->get('Typ')?></th>
						  <th class="content_row_header"><?=$_LANG->get('Position')?></th>
						  <th class="content_row_header"><?=$_LANG->get('Importiert')?></th>
						  <th class="content_row_header"><?=$_LANG->get('Optionen')?></th>
					  </tr>
				  </thead>

				  <? $x = 0;
				  foreach($machinegroup as $m)
				  {?>
					  <tr class="<?=getRowColor($x)?>">
						  <td class="content_row"><?=$m->getName()?>&nbsp;</td>
						  <td class="content_row"><?=$m->getTypeName()?>&nbsp;</td>
						  <td class="content_row"><?=$m->getPosition()?>&nbsp;</td>
						  <td class="content_row">
							  <? if($m->getLectorId()) echo '<span class="error">'.$_LANG->get('Importiert').'</span>'; else echo '&nbsp;'?>
						  </td>
						  <td class="content_row">
							  <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$m->getId()?>"><span class="glyphicons glyphicons-wrench"></span></a>
							  <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=copy&id=<?=$m->getId()?>"><span class="glyphicons glyphicons-copy"></span></a>
							  <a class="icon-link" href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$m->getId()?>')"><span style="color:red" class="glyphicons glyphicons-remove"></span></a>
						  </td>
					  </tr>

					  <? $x++; }
				  ?>
			  </table>
		  </div>
	</div>
<? }?>