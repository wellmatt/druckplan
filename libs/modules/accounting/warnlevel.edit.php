<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			27.09.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

$warn = new Warnlevel((int)$_REQUEST["wid"]);

if($_REQUEST["subexec"] == "save"){
	$warn->setTitle(trim(addslashes($_REQUEST["warn_title"])));
	$warn->setText(trim(addslashes($_REQUEST["warn_text"])));
	$warn->setDeadline((int)$_REQUEST["warn_deadline"]);
	$savemsg = getSaveMessage($warn->save()).$DB->getLastError();
}

?>

<table width="100%">
	<tr>
		<td width="200" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
			<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Mahnstufe hinzuf�gen')?>
			<?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Mahnstufe bearbeiten')?>
		</td>
		<td align="right"><?=$savemsg?></td>
	</tr>
</table>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#warnlevel_edit').submit();",'glyphicon-floppy-disk');
if ($warn->getId()>0){
	$quickmove->addItem('Löschen', '#',  "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&delid=".$warn->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>


<form 	action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="warnlevel_edit" id="warnlevel_edit"   
		onSubmit="return checkForm(new Array(this.warn_title, this.warn_text))">
	<div class="box1">
		<input type="hidden" name="exec" value="edit"> 
		<input type="hidden" name="subexec" value="save"> 
		<input type="hidden" name="wid" value="<?=$warn->getId()?>">
		<table width="100%">
			<colgroup>
				<col width="170">
				<col>
			</colgroup>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Titel')?> *</td>
				<td class="content_row_clear">
				<input id="warn_title" name="warn_title" type="text" class="text" 
					value="<?=$warn->getTitle()?>" style="width: 250px">
				</td>
			</tr>
			<tr>
				<td class="content_row_header" valign="top"><?=$_LANG->get('Text')?> *</td>
				<td class="content_row_clear" valign="top">
				<table>
					<tr>
						<td><textarea id="warn_text" name="warn_text" class="text" style="width: 500px; height: 150px; "
									  ><?=$warn->getText()?></textarea>
						</td>
						<td valign="top">
							<b><?=$_LANG->get('Platzhalter:');?></b><br/>
							%RECHNUNGSDATUM% = <?=$_LANG->get('Rechnungsdatum');?> <br/>
							%RECHNUNGSBETRAG% = <?=$_LANG->get('Rechnungsbetrag');?><br/>
							%RECHNUNGSNUMMER% = <?=$_LANG->get('Rechnungsnummer');?><br/>
							%RECHNUNGSFRIST% = <?=$_LANG->get('Frist der Rechnung');?><br/>
							%MAHNFRIST% = <?=$_LANG->get('Frist der Mahnung');?><br/>
						</td>
					</tr>
				</table>
				</td>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Mahnfrist (Tage)')?></td>
				<td class="content_row_clear">
				<input id="warn_deadline" name="warn_deadline" type="text" class="text" 
					value="<?=$warn->getDeadline()?>" style="width: 75px">
				</td>
			</tr>
			<?if ($warn->getCrt_user()->getId() > 0){?>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Erstellt von')?></td>
				<td class="content_row_clear">
					<?if($warn->getCrt_user()->getId() > 0) echo $warn->getCrt_user()->getNameAsLine()?>
				</td>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Erstellt am')?></td>
				<td class="content_row_clear">
					<?if($warn->getCrt_date() > 0) echo date('d.m.Y - H:i:s',$warn->getCrt_date())?>
				</td>
			</tr>
				<?if($warn->getUpd_user()->getId() > 0){?>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Bearbeitet von')?></td>
					<td class="content_row_clear">
						<?=$warn->getUpd_user()->getNameAsLine()?>
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Bearbeitet am')?></td>
					<td class="content_row_clear">
						<?if($warn->getUpd_date() > 0) echo date('d.m.Y - H:i:s',$warn->getUpd_date())?>
					</td>
				</tr>
				<?}?>
			<?}?>
		</table>
	</div>
</form>