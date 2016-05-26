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
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
				Maschinengruppen
				<span class="pull-right">
					<button class="btn btn-xs btn-success" onclick="document.location. href='index.php?page=<?=$_REQUEST['page']?>&exec=edit';">
						<span class="glyphicons glyphicons-cogwheel"></span>
						<?=$_LANG->get('Maschinengruppe hinzuf&uuml;gen')?>
					</button>
				</span>
			</h3>
	  </div>
	  <div class="table-responsive">
		  <table class="table table-hover">
			  <tr>
				  <td class="content_row_header">&nbsp;</td>
				  <td class="content_row_header"><?=$_LANG->get('Name')?></td>
				  <td class="content_row_header"><?=$_LANG->get('Typ')?></td>
				  <td class="content_row_header"><?=$_LANG->get('Position')?></td>
				  <td class="content_row_header"><?=$_LANG->get('Importiert')?></td>
				  <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
			  </tr>

			  <? $x = 0;
			  foreach($machinegroup as $m)
			  {?>
				  <tr class="<?=getRowColor($x)?>">
					  <td class="content_row">&nbsp;</td>
					  <td class="content_row"><?=$m->getName()?>&nbsp;</td>
					  <td class="content_row"><?=$m->getTypeName()?>&nbsp;</td>
					  <td class="content_row"><?=$m->getPosition()?>&nbsp;</td>
					  <td class="content_row">
						  <? if($m->getLectorId()) echo '<span class="error">'.$_LANG->get('Importiert').'</span>'; else echo '&nbsp;'?>
					  </td>
					  <td class="content_row">
						  <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$m->getId()?>"><span class="glyphicons glyphicons-pencil"></span></a>
						  <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=copy&id=<?=$m->getId()?>"><span class="glyphicons glyphicons-pencil"></span></a>
						  <a class="icon-link" href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$m->getId()?>')"><span class="glyphicons glyphicons-remove"></span></a>
					  </td>
				  </tr>

				  <? $x++; }
			  ?>
		  </table>
	  </div>
</div>
	<? }?>