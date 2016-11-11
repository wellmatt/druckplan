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
<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#paper_form').submit();",'glyphicon-floppy-disk');
if ($machinegroup->getId()>0){
	$quickmove->addItem('Löschen', '#',  "askDel('index.php?page".$_REQUEST['page']."&exec=delete&id".$machinegroup->getId()."');", 'glyphicon-trash', true);
}


echo $quickmove->generate();
// end of Quickmove generation ?>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="paper_form" name="paper_form"
	  class="form-horizontal" role="form" onSubmit="return checkform(new Array(this.machinegroup_name,this.machinegroup_position))">
	<input name="exec" value="edit" type="hidden">
	<input type="hidden" name="subexec" value="save">
	<input name="id" value="<?=$machinegroup->getId()?>" type="hidden">
	<div class="panel panel-default">
		  <div class="panel-heading">
				<h3 class="panel-title">
					<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Maschinengruppe hinzuf&uuml;gen')?>
					<?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Maschinengruppe &auml;ndern')?>
					<?if ($_REQUEST["exec"] == "copy")  echo $_LANG->get('Maschinengruppe kopieren')?>
					<span class="pull-right"><?=$savemsg?></span>
				</h3>
		  </div>
		  <div class="panel-body">
				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Name</label>
					<div class="col-sm-4">
						<input name="machinegroup_name" class="form-control" type="text" value="<?=$machinegroup->getName()?>">
						<? if($machinegroup->getLectorId() != 0) { ?>
							<div class="input-group">
								<input type="text" class="content_row_header">
								<span class="error"><?=$_LANG->get('Importiert von Lector')?></span>
							</div>
							<div class="input-group">
								<input type="text" class=""content_row_clear">
								Lector-ID: <?=$machinegroup->getLectorId()?>
							</div>
						<? } ?>
					</div>
				</div>

				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Position</label>
					<div class="col-sm-4">
						<input name="machinegroup_position" class="form-control" type="text" value="<?=$machinegroup->getPosition()?>">
					</div>
				</div>

				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Typ</label>
					<div class="col-sm-10">
						<? if ($machinegroup->getType() == 0){ ?>
							<input name="machinegroup_typ"  type="radio" value="0" checked>&nbsp;<?=$_LANG->get('inhouse')?>
							&nbsp;
							<input name="machinegroup_typ"  type="radio" value="1">&nbsp;<?=$_LANG->get('Fremdleistung')?>
						<?} else { ?>
							<input name="machinegroup_typ" type="radio" value="0">&nbsp;<?=$_LANG->get('inhouse')?>
							&nbsp;
							<input name="machinegroup_typ" type="radio" value="1" checked>&nbsp;<?=$_LANG->get('Fremdleistung')?>
						<? } ?>
					</div>
				</div>
		</div>
	</div>
</form>