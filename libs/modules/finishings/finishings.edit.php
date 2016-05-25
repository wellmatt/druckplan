<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       03.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$finishing = new Finishing($_REQUEST["id"]);
if($_REQUEST["exec"] == "copy")
{
	$finishing->clearID();
}
if($_REQUEST["subexec"] == "save")
{
	$finishing->setName(trim(addslashes($_REQUEST["finishing_name"])));
	$finishing->setBeschreibung(trim(addslashes($_REQUEST["finishing_beschreibung"])));
	$finishing->setKosten((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["finishing_kosten"]))));
	$savemsg = getSaveMessage($finishing->save()).$DB->getLastError();
}
?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#paper_form').submit();",'glyphicon-floppy-disk');

if ($finishing->getId()>0){
	$quickmove->addItem('Löschen', '#',  "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&id=".$finishing->getId()."');", 'glyphicon-trash', true);
}

echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Lacke hinzuf&uuml;gen')?>
				<?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Lacke &auml;ndern')?>
				<?if ($_REQUEST["exec"] == "copy")  echo $_LANG->get('Lacke kopieren')?>
				<span class="pull-right"><?=$savemsg?></span>
			</h3>
	  </div>
	  <div class="panel-body">
		  <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="paper_form" name="paper_form"
				class="form-horizontal" role="form" onSubmit="return checkform(new Array(this.finishing_name))">
			  <input name="exec" value="edit" type="hidden">
			  <input type="hidden" name="subexec" value="save">
			  <input name="id" value="<?=$finishing->getId()?>" type="hidden">


			  <div class="form-group">
				  <label for="" class="col-sm-2 control-label">Name</label>
				  <div class="col-sm-10">
					  <input type="text" class="form-control" id="finishing_name" name="finishing_name" value="<?=$finishing->getName()?>" placeholder="Bitte Lacknamen eintragen...">
				  </div>
			  </div>

			  <div class="form-group">
				  <label for="" class="col-sm-2 control-label">Beschreibung</label>
				  <div class="col-sm-10">
					  <input type="text" class="form-control" id="finishing_beschreibung" name="finishing_beschreibung" value="<?=$finishing->getBeschreibung()?>" placeholder="Beschreibung">
				  </div>
			  </div>

			  <div class="form-group">
				  <label for="" class="col-sm-2 control-label">Kosten</label>
				  <div class="col-sm-10">
					  <div class="input-group">
						  <input type="text" class="form-control" id="finishing_kosten" name="finishing_kosten" value="<?=printPrice($finishing->getKosten())?>" placeholder="bitte Lacknamen eintragen...">
						  <span class="input-group-addon">€</span>
					  </div>
				  </div>
			  </div>
		  </form>
	  </div>
</div>