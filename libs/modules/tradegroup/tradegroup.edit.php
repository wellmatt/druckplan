<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       22.08.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$_REQUEST["id"] = (int)$_REQUEST["id"];

if($_REQUEST["exec"] == "new"){
	$tradegroup = new Tradegroup();
}

if($_REQUEST["exec"] == "edit"){
	$tradegroup = new Tradegroup($_REQUEST["id"]);
}

if($_REQUEST["subexec"] == "save"){
	if ($_REQUEST["tradegroup_shoprel"]==1){
		$tradegroup->setShoprel(1);
	} else {
		$tradegroup->setShoprel(0);
	}
	$tradegroup->setShoprel((int)$_REQUEST["tradegroup_shoprel"]);
	$tradegroup->setTitle(trim(addslashes($_REQUEST["tradegroup_title"])));
	$tradegroup->setDesc(trim(addslashes($_REQUEST["tradegroup_desc"])));
	if ((int)$_REQUEST["tradegroup_parentid"] != $tradegroup->getId()){
		$tradegroup->setParentID((int)$_REQUEST["tradegroup_parentid"]);
	} else {
		
	}
	
	$savemsg = getSaveMessage($tradegroup->save())." ".$DB->getLastError();
}

$all_tradegroups = Tradegroup::getAllTradegroups(0);

function printSubTradegroupsForSelect($parentId, $depth){
	global $tradegroup;
	$all_subgroups = Tradegroup::getAllTradegroups($parentId);
	foreach ($all_subgroups AS $subgroup){
		global $x;
		$x++; ?>
			<option value="<?=$subgroup->getId()?>"	<?if ($tradegroup->getParentID() == $subgroup->getId()) echo "selected" ;?> >
				<?for ($i=0; $i<$depth+1;$i++) echo "&emsp;"?>
				<?= $subgroup->getTitle()?>
			</option>
		<? printSubTradegroupsForSelect($subgroup->getId(), $depth+1);
	}
}
?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#tradegroup_edit').submit();",'glyphicon-floppy-disk');
if ($tradegroup->getId()>0){
	$quickmove->addItem('Löschen', '#',  "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&id=".$tradegroup->getId()."');", 'glyphicon-trash', true);
}



echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
				<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Warengruppe hinzuf&uuml;gen')?>
				<?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Warengruppe bearbeiten')?>
				<?//if ($_REQUEST["exec"] == "copy")  echo $_LANG->get('Warengruppe kopieren')?>
			</h3>
	  </div>
	<div class="panel-body">
		<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="tradegroup_edit" id="tradegroup_edit"
			  class="form-horizontal" role="form" onSubmit="return checkform(new Array(this.tradegroup_title))">
			<input type="hidden" name="exec" value="edit">
			<input type="hidden" name="subexec" value="save">
			<input type="hidden" name="id" value="<?=$tradegroup->getId()?>">

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Titel</label>
				<div class="col-sm-10">
					<input id="tradegroup_title" name="tradegroup_title" type="text" class="form-control" value="<?=$tradegroup->getTitle()?>">
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Beschreibung</label>
				<div class="col-sm-10">
					<textarea id="tradegroup_desc" name="tradegroup_desc" type="text" class="form-control"><?=$tradegroup->getDesc()?></textarea>
					</div>
				</div>

				<div class="form-group">
					<label for="" class="col-sm-2 control-label">Shop-Freigabe</label>
					<div class="col-sm-10">
						<input 	id="tradegroup_shoprel" name="tradegroup_shoprel" class="text" type="checkbox" value="1" <?if ($tradegroup->getShoprel() == 1) echo "checked"; ?>>
						</div>
					</div>


			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Übergeordnete Gruppe</label>
				<div class="col-sm-10">
					<select id="tradegroup_parentid" name="tradegroup_parentid" type="text" class="form-control">
						<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
						<?	foreach ($all_tradegroups as $tg){
							if ($tg->getId() != $tradegroup->getId()){ ?>
								<option value="<?=$tg->getId()?>"
									<?if ($tradegroup->getParentID() == $tg->getId()) echo "selected" ;?> ><?= $tg->getTitle()?></option>
							<?		}
							printSubTradegroupsForSelect($tg->getID(), 0);
						} ?>
					</select>
				</div>
			</div>
		</form>
	</div>
</div>

