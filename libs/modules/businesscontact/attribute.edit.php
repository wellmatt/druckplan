<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			20.09.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
$attribute = new Attribute($_REQUEST["aid"]);

if($_REQUEST["subexec"] == "save"){
	$attribute->setTitle(trim(addslashes($_REQUEST["attribute_title"])));
	$attribute->setEnable_customer((int)$_REQUEST["enable_cust"]);
	$attribute->setEnable_contact((int)$_REQUEST["enable_contact"]);
	$attribute->setEnable_colinv((int)$_REQUEST["enable_colinv"]);
	$attribute->setEnableOrder((int)$_REQUEST["enable_order"]);
	$savemsg = getSaveMessage($attribute->save()).$DB->getLastError();
	
	$attribute_items = Array();
	$xy = (int)$_REQUEST["count_quantity"];
	for ($i = 1; $i <= $xy ; $i++){
		if ($_REQUEST["item_title_{$i}"] != NULL && $_REQUEST["item_title_{$i}"] != ""){
			$attribute_items[$i]["id"] = (int)$_REQUEST["item_id_{$i}"];
			$attribute_items[$i]["title"] = trim(addslashes($_REQUEST["item_title_{$i}"]));
			$attribute_items[$i]["input"] = (int)$_REQUEST["item_input_{$i}"];
		} else {
			$attribute->deleteItem((int)$_REQUEST["item_id_{$i}"]);
		}
	}
	
	$attribute->saveItems($attribute_items);
}

$all_items = $attribute->getItems();

?>

<script language="javascript">
function addAttibuteItem()
{
	var obj = document.getElementById('table_items');
	var count = parseInt(document.getElementById('count_quantity').value) + 1;
	var insert = '<tr><td>'+count+'</td>';
	insert += '<td></td>';
	insert += '<td>';
	insert += '<input name="item_id_'+count+'" type="hidden" value="0"/>';
	insert += '<input 	name="item_title_'+count+'" class="form-control" type="text"';
	insert += ' value="">';
	insert += '</td>';
	insert += '<td>';
	insert += '<input name="item_input_'+count+'" type="checkbox" value="1">';
	insert += '</td>';
	insert += '</tr>';
	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('count_quantity').value = count;
}
</script>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#attribute_edit').submit();",'glyphicon-floppy-disk');
if ($attribute->getId()>0){
	$quickmove->addItem('Löschen', '#',  "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&aid=".$attribute->getId()."');", 'glyphicon-trash', true);
}

echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
			<? if ($_REQUEST["exec"] == "new") echo $_LANG->get('Merkmal hinzufügen') ?>
			<? if ($_REQUEST["exec"] == "edit") echo $_LANG->get('Merkmal bearbeiten') ?>
			<span class="pull-right"><?= $savemsg ?></span>
		</h3>

	</div>
	<div class="panel-body">
		<form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" name="attribute_edit" id="attribute_edit"
			  class="form-horizontal" role="form" onSubmit="return checkform(new Array(this.attribute_title))">
			<input type="hidden" name="exec" value="edit">
			<input type="hidden" name="subexec" value="save">
			<input type="hidden" name="aid" value="<?= $attribute->getId() ?>">

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Titel</label>
				<div class="col-sm-4">
					<input id="attribute_title" name="attribute_title" type="text" class="form-control" value="<?= $attribute->getTitle() ?>">
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">ID</label>
				<div class="col-sm-4">
					<input type="text" class="form-control" value="<?= $attribute->getId()?>">
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Sichtbar beim Kunden</label>
				<div class="col-sm-1">
					<input name="enable_cust" type="checkbox" class="form-control" value="1" onfocus="markfield(this,0)"
						   onblur="markfield(this,1)"
						<? if ($attribute->getEnable_customer()) echo "checked"; ?> >
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Sichtbar beim Ansprechpartner</label>
				<div class="col-sm-1">
					<input name="enable_contact" type="checkbox" class="form-control" value="1"
						   onfocus="markfield(this,0)" onblur="markfield(this,1)"
						<? if ($attribute->getEnable_contact()) echo "checked"; ?> >
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Sichtbar beim Vorgang</label>
				<div class="col-sm-1">
					<input name="enable_colinv" type="checkbox" class="form-control" value="1"
						   onfocus="markfield(this,0)" onblur="markfield(this,1)"
						<? if ($attribute->getEnable_colinv()) echo "checked"; ?>>
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Sichtbar bei Kalkulation</label>
				<div class="col-sm-1">
					<input name="enable_order" type="checkbox" class="form-control" value="1"
						   onfocus="markfield(this,0)" onblur="markfield(this,1)"
						<? if ($attribute->getEnableOrder()) echo "checked"; ?>>
				</div>
			</div>
			<br/>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						Merkmalsoptionen
					</h3>
				</div>
				<div class="panel-body">
					<input type="hidden" name="count_quantity" id="count_quantity"
						   value="<? if (count($all_items) > 0) echo count($all_items); else echo "1"; ?>">
					<div class="table-responsive">
						<table id="table_items" class="table table-hover">
							<thead>
							<tr>
								<th width="10%"><?= $_LANG->get('Nr.') ?></th>
								<th width="10%"><?= $_LANG->get('ID') ?></th>
								<th width="20%"><?= $_LANG->get('Titel') ?>*</th>
								<th width="5%"><?= $_LANG->get('Input') ?>**</th>
								<th>
									<span class="glyphicons glyphicons-plus pointer" onclick="addAttibuteItem()"></span>
								</th>
							</tr>
							</thead>
							<?
							$x = count($all_items);
							if ($x < 1) {
								//$allprices[] = new Array
								$x++;
							}
							for ($y = 0; $y < $x; $y++) { ?>
								<tbody>
								<tr>
									<td>
										<?= $y + 1 ?>
									</td>
									<td>
										<?= $all_items[$y]["id"] ?>
									</td>
									<td>
										<input name="item_id_<?= $y + 1 ?>" type="hidden" value="<?= $all_items[$y]["id"] ?>"/>
										<input name="item_title_<?= $y + 1 ?>" class="form-control" type="text" value="<?= $all_items[$y]["title"] ?>">
									</td>
									<td>
										<input name="item_input_<?= $y + 1 ?>" type="checkbox" value="1" <?php if ($all_items[$y]["input"] == 1) echo " checked "; ?>>
									</td>
									<td>
										&ensp;
									</td>
								</tr>
								</tbody>
							<? } ?>
						</table>
					</div>
					<br/>
					* <?= $_LANG->get('Merkmalsoption wird gel&ouml;scht, wenn der Titel leer ist'); ?>
					<br/>
					** <?= $_LANG->get('Erzeugt Eingabefeld für freien Wert zum Merkmal'); ?>
				</div>
			</div>
		</form>
	</div>
</div>




