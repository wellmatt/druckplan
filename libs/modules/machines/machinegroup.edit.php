<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       03.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$machinegroup = new MachineGroup($_REQUEST["id"]);
if($_REQUEST["exec"] == "copy")
{
	$machinegroup->clearID();
}
if($_REQUEST["subexec"] == "save")
{
	$machinegroup->setName(trim(addslashes($_REQUEST["machinegroup_name"])));
	$machinegroup->setPosition(trim(addslashes($_REQUEST["machinegroup_position"])));
	$machinegroup->setType(trim(addslashes($_REQUEST["machinegroup_typ"])));
	$savemsg = getSaveMessage($machinegroup->save()).$DB->getLastError();
}
?>
<table width="100%">
	<tr>
		<td width="200" class="content_header">
		<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
		<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Maschinengruppe hinzuf&uuml;gen')?>
		<?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Maschinengruppe &auml;ndern')?>
		<?if ($_REQUEST["exec"] == "copy")  echo $_LANG->get('Maschinengruppe kopieren')?>
		</td>
		<td align="right"><?=$savemsg?></td>
	</tr>
</table>

<div id="fl_menu">
	<div class="label">Quick Move</div>
	<div class="menu">
        <a href="#top" class="menu_item">Seitenanfang</a>
        <a href="index.php?page=<?=$_REQUEST['page']?>" class="menu_item">Zurück</a>
        <a href="#" class="menu_item" onclick="$('#paper_form').submit();">Speichern</a>
    </div>
</div>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="paper_form" name="paper_form" onSubmit="return checkform(new Array(this.machinegroup_name,this.machinegroup_position))">
	<div class="box1">
		<input name="exec" value="edit" type="hidden">
		<input type="hidden" name="subexec" value="save">
		<input name="id" value="<?=$machinegroup->getId()?>" type="hidden">
		<table width="100%">
		    <colgroup>
		        <col width="200">
		        <col>
		    </colgroup>
		    <tr>
		        <td class="content_row_header"><?=$_LANG->get('Name')?> *</td>
		        <td class="content_row_clear">
		            <input name="machinegroup_name" value="<?=$machinegroup->getName()?>" class="text" style="width:300px">
		        </td>
		     <td valign="top">
				<table width="500" cellpadding="0" cellspacing="0" border="0">
				    <colgroup>
				        <col width="180">
				        <col>
				    </colgroup>
				    <? if($machinegroup->getLectorId() != 0) { ?>
				    <tr>
				        <td class="content_row_header"><span class="error"><?=$_LANG->get('Importiert von Lector')?></span></td>
				        <td class="content_row_clear">
				            Lector-ID: <?=$machinegroup->getLectorId()?>
				        </td>
				    </tr>
				    <? } ?>
				</table>      
		    </tr>
		    <tr>
		        <td class="content_row_header"><?=$_LANG->get('Position')?> *</td>
		        <td class="content_row_clear">
		            <input name="machinegroup_position" value="<?=$machinegroup->getPosition()?>" class="text" style="width:60px">      
		        </td>   
		    </tr>
		     <tr>
		        <td class="content_row_header"><?=$_LANG->get('Typ')?></td>
		        <td class="content_row_clear">
		        <? if ($machinegroup->getType() == 0){ ?>
		            <input type="radio" name="machinegroup_typ" value="0" checked><?=$_LANG->get('inhouse')?>  
		            <input type="radio" name="machinegroup_typ" value="1"><?=$_LANG->get('Fremdleistung')?>         
		        <?} else { ?>
		          <input type="radio" name="machinegroup_typ" value="0"><?=$_LANG->get('inhouse')?>  
		          <input type="radio" name="machinegroup_typ" value="1" checked><?=$_LANG->get('Fremdleistung')?> 
		           <? } ?>           
		        </td>   
		    </tr>
		</table>
	</div>
	<br/>
</form>