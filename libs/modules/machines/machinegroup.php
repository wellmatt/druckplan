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
	<table width="100%">
	<tr>
	<td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Maschinengruppen')?></td>
	<td><?=$savemsg?></td>
	<td width="200" class="content_header" align="right"><a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit"><img src="images/icons/gear--plus.png"> <?=$_LANG->get('Maschinengruppe hinzuf&uuml;gen')?></a></td>
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
	<col width="100">
	</colgroup>
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
	<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$m->getId()?>"><img src="images/icons/pencil.png"></a>
	<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=copy&id=<?=$m->getId()?>"><img src="images/icons/scripts.png"></a>
 	<a class="icon-link" href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$m->getId()?>')"><img src="images/icons/cross-script.png"></a>
	</td>
	</tr>
	
	<? $x++; }
	?>
	</table>
	</div>
	<? }?>