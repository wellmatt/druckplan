<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			20.09.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/businesscontact/attribute.class.php';

if ($_REQUEST["exec"] == "delete"){
	$del_attribute = new Attribute((int)$_REQUEST["aid"]);
	$del_attribute->delete();
}

if($_REQUEST["exec"] == "edit" || $_REQUEST["exec"] == "new"){
	require_once 'libs/modules/businesscontact/attribute.edit.php';
} else {

	$all_attributes = Attribute::getAllAttributes(Attribute::ORDER_TITLE);
?>

<table width="100%">
	<tr>
		<td width="200" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"><span style="font-size: 13px"> <?=$_LANG->get('Merkmale')?> </span>
		</td>
		<td><?=$savemsg?></td>
		<td width="200" class="content_header" align="right">
			&emsp;
			<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&exec=new"><img src="images/icons/application-task.png"> <?=$_LANG->get('Merkmal erstellen')?></a>
			&emsp;
		</td>
	</tr>
</table>

<div class="box1">
	<table width="100%" cellpadding="0" cellspacing="0">
		<colgroup>
			<col width="300px">
			<col width="60px">
			<col width="60px">
			<col width="60px">
			<col width="60px">
		</colgroup>
		<tr>
			<td class="content_row_header"><?= $_LANG->get('Name');?></td>
			<td class="content_row_header" align="center"><?= $_LANG->get('Bei Kunden');?></td>
			<td class="content_row_header" align="center"><?= $_LANG->get('Bei Ansprechpartner');?></td>
			<td class="content_row_header" align="center"><?= $_LANG->get('Bei Vorgängen');?></td>
			<td class="content_row_header" align="center"><?= $_LANG->get('Optionen');?></td>
		</tr>
	<?	$x=0;
		foreach ($all_attributes AS $attribute){ ?>
			<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&aid=<?=$attribute->getId()?>'">
					<?=$attribute->getTitle()?>
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&aid=<?=$attribute->getId()?>'" align="center">
						<? if ($attribute->getEnable_customer() == 1){
								echo "<img src='images/icons/tick.png'>";
							}
							?>
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&aid=<?=$attribute->getId()?>'" align="center">
						<? if ($attribute->getEnable_contact() == 1 ){
								echo "<img src='images/icons/tick.png'>";
							}
							?>
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&aid=<?=$attribute->getId()?>'" align="center">
						<? if ($attribute->getEnable_colinv() == 1 ){
								echo "<img src='images/icons/tick.png'>";
							}
							?>
				</td>
				<td class="content_row" align="center">
					<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&aid=<?=$attribute->getId()?>"><img src="images/icons/pencil.png"></a>
					&ensp;
					<a class="icon-link" href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&aid=<?=$attribute->getId()?>')"><img	src="images/icons/cross-script.png"> </a>
				</td>
			</tr>
	<?		$x++;
		} ?>	
	</table>	
</div>
<? } // Ende der Uebersicht?>