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
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
				Merkmale
				<span class="pull-right">
					<span class="glyphicons glyphicons-display"></span>
					<button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=<?=$_REQUEST['page']?>&exec=edit&exec=new';" >
						<?=$_LANG->get('Merkmal erstellen')?>
					</button>
				</span>
			</h3>
	  </div>
		<div class="table-responsive">
			<table class="table table-hover">
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
								echo "<span class=\"glyphicons glyphicons-ticket\"></span>";
							}
							?>
						</td>
						<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&aid=<?=$attribute->getId()?>'" align="center">
							<? if ($attribute->getEnable_contact() == 1 ){
								echo "<span class=\"glyphicons glyphicons-ticket\"></span>";
							}
							?>
						</td>
						<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&aid=<?=$attribute->getId()?>'" align="center">
							<? if ($attribute->getEnable_colinv() == 1 ){
								echo "<span class=\"glyphicons glyphicons-ticket\"></span>";
							}
							?>
						</td>
						<td class="content_row" align="center">
							<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&aid=<?=$attribute->getId()?>"><span class="glyphicons glyphicons-pencil"></span></a>
							&ensp;
							<a class="icon-link" href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&aid=<?=$attribute->getId()?>')"><span class="glyphicons glyphicons-remove"></span></a>
						</td>
					</tr>
					<?		$x++;
				} ?>
			</table>
		</div>
</div>


<? } // Ende der Uebersicht?>