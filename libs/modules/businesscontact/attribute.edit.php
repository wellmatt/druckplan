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
	var insert = '<tr><td class="content_row_clear">'+count+'</td>';
	insert += '<td class="content_row_clear">';
	insert += '<input name="item_id_'+count+'" type="hidden" value="0"/>';
	insert += '<input 	name="item_title_'+count+'" class="text" type="text"';
	insert += ' value="" style="width: 140px">';
	insert += '</td>';
	insert += '<td class="content_row_clear">';
	insert += '<input name="item_input_'+count+'" type="checkbox" value="1" style="width: 40px">';
	insert += '</td>';
	insert += '</tr>';
	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('count_quantity').value = count;
}
</script>

<table width="100%">
	<tr>
		<td width="200" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
			<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Merkmal hinzufügen')?>
			<?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Merkmal bearbeiten')?>
		</td>
		<td align="right"><?=$savemsg?></td>
	</tr>
</table>

<div id="fl_menu">
	<div class="label">Quick Move</div>
	<div class="menu">
        <a href="#top" class="menu_item">Seitenanfang</a>
        <a href="index.php?page=<?=$_REQUEST['page']?>" class="menu_item">Zurück</a>
        <a href="#" class="menu_item" onclick="$('#attribute_edit').submit();">Speichern</a>
    </div>
</div>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="attribute_edit" id="attribute_edit"
		onSubmit="return checkform(new Array(this.attribute_title))">
<div class="box1">
	<input type="hidden" name="exec" value="edit"> 
	<input type="hidden" name="subexec" value="save"> 
	<input type="hidden" name="aid" value="<?=$attribute->getId()?>">
	<table width="100%">
		<colgroup>
			<col width="180">
			<col>
		</colgroup>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Titel')?> *</td>
			<td class="content_row_clear">
			<input id="attribute_title" name="attribute_title" type="text" class="text" 
				value="<?=$attribute->getTitle()?>" style="width: 370px">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('ID')?></td>
			<td class="content_row_clear"><?=$attribute->getId()?></td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Sichtbar beim Kunden')?></td>
			<td class="content_row_clear">
				<input name="enable_cust" type="checkbox" value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)"
						<? if ($attribute->getEnable_customer()) echo "checked";?> >
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Sichtbar beim Ansprechpartner')?></td>
			<td class="content_row_clear">
				<input name="enable_contact" type="checkbox" value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)"
					<? if ($attribute->getEnable_contact()) echo "checked";?> >
			</td>
		</tr>	
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Sichtbar beim Vorgang')?></td>
			<td class="content_row_clear">
				<input name="enable_colinv" type="checkbox" value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)"
					<? if ($attribute->getEnable_colinv()) echo "checked";?> >
			</td>
		</tr>	
	</table>
</div> 
<br/>
<input 	type="hidden" name="count_quantity" id="count_quantity" 
		value="<? if(count($all_items) > 0) echo count($all_items); else echo "1";?>">
<h1><?=$_LANG->get('Merkmalsoptionen')?></h1>
<div class="box2">
	<table id="table_items">
		<colgroup>
        	<col width="40">
        	<col width="40">
        	<col width="160">
        	<col width="40">
        	<col>
    	</colgroup>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Nr.')?></td>
			<td class="content_row_header"><?=$_LANG->get('ID')?></td>
			<td class="content_row_header"><?=$_LANG->get('Titel')?>*</td>
			<td class="content_row_header"><?=$_LANG->get('Input')?>**</td>
			<td class="content_row_header">
				&emsp;
				<img src="images/icons/plus.png" class="pointer icon-link" onclick="addAttibuteItem()">
			</td>
		</tr>
		<?
		$x = count($all_items);
		if ($x < 1){
			//$allprices[] = new Array
			$x++;
		}
		for ($y=0; $y < $x ; $y++){ ?>
			<tr>
				<td class="content_row_clear">
					<?=$y+1?>
				</td>
				<td class="content_row_clear">
					<?=$all_items[$y]["id"]?>
				</td>
				<td class="content_row_clear">
					<input name="item_id_<?=$y+1?>" type="hidden" value="<?=$all_items[$y]["id"]?>"/>
					<input 	name="item_title_<?=$y+1?>" class="text" type="text"
							value ="<?=$all_items[$y]["title"]?>" style="width: 140px">
				</td>
				<td class="content_row_clear">
					<input 	name="item_input_<?=$y+1?>" type="checkbox" value="1" <?php if ($all_items[$y]["input"] == 1) echo " checked ";?> style="width: 40px">
				</td>
				<td class="content_row_clear">
					&ensp;
				</td>
			</tr>
		<? } ?>
	</table>
	<br/>
	* <?=$_LANG->get('Merkmalsoption wird gel&ouml;scht, wenn der Titel leer ist');?>
	<br/>
	** <?=$_LANG->get('Erzeugt Eingabefeld für freien Wert zum Merkmal');?>
</div>
<br/>
</form>