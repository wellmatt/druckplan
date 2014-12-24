<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			27.09.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/accounting/warnlevel.class.php';

if($_REQUEST["exec"] == "new" || $_REQUEST["exec"] == "edit"){
	// Mahnstufe bearbeiten
	require_once 'libs/modules/accounting/warnlevel.edit.php';
} else {
	
	// Uebersicht ausgeben
	
	if ($_REQUEST["exec"] == "delete") {
		$del_warnlevel = new Warnlevel($_REQUEST["delid"]);
		$del_warnlevel->delete();
	}
	
	$all_warnlevel = Warnlevel::getAllWarnlevel();
	?>
	
	<table width="100%">
		<tr>
			<td width="200" class="content_header">
				<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>" /> <?=$_LANG->get('Mahnstufen')?>
			</td>
			<td><?=$savemsg?></td>
			<td width="200" class="content_header" align="right">
				<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=new"><img src="images/icons/bank--plus.png"><?=$_LANG->get('Mahnstufe hinzuf&uuml;gen')?> </a>
			</td>
		</tr>
	</table>
	
	<div class="box1">
		<table width="100%" cellpadding="0" cellspacing="0">
			<colgroup>
				<col width="80">
				<col width="150">
				<col>
				<col width="100">
			</colgroup>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('ID')?></td>
				<td class="content_row_header"><?=$_LANG->get('Titel')?></td>
				<td class="content_row_header"><?=$_LANG->get('Text')?></td>
				<td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
			</tr>
			<? $x = 0;
			foreach($all_warnlevel as $warn){ ?>
				<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
					<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&wid=<?=$warn->getId()?>'">
						<?=$warn->getId()?>&ensp;
					</td>
					<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&wid=<?=$warn->getId()?>'">
						<?=$warn->getTitle()?>
					</td>
					<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&wid=<?=$warn->getId()?>'">
						<?=substr($warn->getText(), 0, 250)?> <?if(strlen($warn->getText()) > 250 ) echo "...";?>
					</td>
					<td class="content_row">
		                <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&wid=<?=$warn->getId()?>"><img src="images/icons/pencil.png" title="<?=$_LANG->get('Bearbeiten')?>"></a>
						&ensp;
		                <a class="icon-link" href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&delid=<?=$warn->getId()?>')"><img src="images/icons/cross-script.png" title="<?=$_LANG->get('L&ouml;schen')?>"></a>
	            	</td>
				</tr>
				<? $x++;
			}// Ende foreach($all_article)
			?>
		</table>
	</div>
<?} ?>