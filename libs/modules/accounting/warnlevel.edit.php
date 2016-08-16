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

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
			<img src="<?= $_MENU->getIcon($_REQUEST['page']) ?>">
			<? if ($_REQUEST["exec"] == "new") echo $_LANG->get('Mahnstufe hinzufügen') ?>
			<? if ($_REQUEST["exec"] == "edit") echo $_LANG->get('Mahnstufe bearbeiten') ?>
		</h3>
	</div>
	<div class="panel-body">
		<form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" class="form-horizontal"
			  name="warnlevel_edit" id="warnlevel_edit"
			  onSubmit="return checkForm(new Array(this.warn_title, this.warn_text))">
			<input type="hidden" name="exec" value="edit">
			<input type="hidden" name="subexec" value="save">
			<input type="hidden" name="wid" value="<?= $warn->getId() ?>">

			<div class="row">
				<div class="col-md-6">
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Titel</label>
						<div class="col-sm-9">
							<input name="warn_title" id="warn_title" value="<?= $warn->getTitle() ?>"
								   class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Text</label>
						<div class="col-sm-9">
							<textarea rows="6" id="warn_text" name="warn_text"
									  class="form-control"><?= $warn->getText() ?></textarea>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-3 control-label">Mahnfrist(Tage)</label>
						<div class="col-sm-5">
							<input name="warn_deadline" id="warn_deadline" value="<?= $warn->getDeadline() ?>"
								   class="form-control">
						</div>
					</div>
					<? if ($warn->getCrt_user()->getId() > 0) { ?>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Erstellt von</label>
							<div class="col-sm-5">
								<div class="form-control">
									<? if ($warn->getCrt_user()->getId() > 0) echo $warn->getCrt_user()->getNameAsLine() ?>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="" class="col-sm-3 control-label">Erstellt am</label>
							<div class="col-sm-5">
								<div class="form-control">
									<? if ($warn->getCrt_date() > 0) echo date('d.m.Y - H:i:s', $warn->getCrt_date()) ?>
								</div>
							</div>
						</div>
						<? if ($warn->getUpd_user()->getId() > 0) { ?>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Bearbeitet von</label>
								<div class="col-sm-9">
									<?= $warn->getUpd_user()->getNameAsLine() ?>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-3 control-label">Bearbeitet am</label>
								<div class="col-sm-9">
									<? if ($warn->getUpd_date() > 0) echo date('d.m.Y - H:i:s', $warn->getUpd_date()) ?>
								</div>
							</div>

						<? } ?>
					<? } ?>
				</div>
				<div class="col-md-6">
					<b><?=$_LANG->get('Platzhalter:');?></b><br/>
					%RECHNUNGSDATUM% = <?=$_LANG->get('Rechnungsdatum');?> <br/>
					%RECHNUNGSBETRAG% = <?=$_LANG->get('Rechnungsbetrag');?><br/>
					%RECHNUNGSNUMMER% = <?=$_LANG->get('Rechnungsnummer');?><br/>
					%RECHNUNGSFRIST% = <?=$_LANG->get('Frist der Rechnung');?><br/>
					%MAHNFRIST% = <?=$_LANG->get('Frist der Mahnung');?><br/>
				</div>
			</div>
		</form>
	</div>
</div>