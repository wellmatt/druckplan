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

<table width="100%">
	<tr>
		<td width="200" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
			<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Warengruppe hinzuf&uuml;gen')?>
			<?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Warengruppe bearbeiten')?>
			<?//if ($_REQUEST["exec"] == "copy")  echo $_LANG->get('Warengruppe kopieren')?>
		</td>
		<td align="right"><?=$savemsg?></td>
	</tr>
</table>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="tradegroup_edit"
		  onSubmit="return checkform(new Array(this.tradegroup_title))">
	<div class="box1">
		<input type="hidden" name="exec" value="edit"> 
		<input type="hidden" name="subexec" value="save"> 
		<input type="hidden" name="id" value="<?=$tradegroup->getId()?>">
		<table width="100%">
			<colgroup>
				<col width="170">
				<col>
			</colgroup>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Titel')?> *</td>
				<td class="content_row_clear">
				<input id="tradegroup_title" name="tradegroup_title" type="text" class="text" 
					value="<?=$tradegroup->getTitle()?>" style="width: 370px">
				</td>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Beschreibung')?></td>
				<td class="content_row_clear">
					<textarea id="tradegroup_desc" name="tradegroup_desc" rows="4" cols="50" class="text"><?=$tradegroup->getDesc()?></textarea>
				</td>
			</tr>
			<tr>
			<?if($_CONFIG->shopActivation){?>
				<td class="content_row_header"><?=$_LANG->get('Shop-Freigabe')?></td>
				<td class="content_row_clear">
					<input 	id="tradegroup_shoprel" name="tradegroup_shoprel" class="text" type="checkbox" 
							value="1" <?if ($tradegroup->getShoprel() == 1) echo "checked"; ?>>
				</td>
			<?}?>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('&Uuml;bergeordnete Gruppe')?></td>
				<td class="content_row_clear">
					<select id="tradegroup_parentid" name="tradegroup_parentid" style="width: 170px">
					<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
					<?	foreach ($all_tradegroups as $tg){
							if ($tg->getId() != $tradegroup->getId()){ ?>
								<option value="<?=$tg->getId()?>"
								<?if ($tradegroup->getParentID() == $tg->getId()) echo "selected" ;?> ><?= $tg->getTitle()?></option>
					<?		}
						printSubTradegroupsForSelect($tg->getID(), 0);
						} ?>
				</select>
				</td>
			</tr>
		</table>
	</div>
	<br/>
	<?// Speicher & Navigations-Button ?>
	<table width="100%">
	    <colgroup>
	        <col width="180">
	        <col>
	    </colgroup> 
	    <tr>
	        <td class="content_row_header">
	        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
	        			onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
	        </td>
	        <td class="content_row_clear" align="right">
	        	<input type="submit" value="<?=$_LANG->get('Speichern')?>">
	        </td>
	    </tr>
	</table>
</form>