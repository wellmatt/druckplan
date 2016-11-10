<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/customfields/custom.field.class.php';

function printSubTradegroupsForSelect($parentId, $depth, $filter){
    $all_subgroups = Tradegroup::getAllTradegroups($parentId);
    foreach ($all_subgroups AS $subgroup)
    {
        global $x;
        $x++; ?>
        <option value="<?=$subgroup->getId()?>" <?php if ($filter == $subgroup->getId()) echo ' selected ';?>>
            <?for ($i=0; $i<$depth+1;$i++) echo "&emsp;"?>
            <?= $subgroup->getTitle()?>
        </option>
        <? printSubTradegroupsForSelect($subgroup->getId(), $depth+1, $filter);
    }
}

$customfield = new CustomField((int)$_REQUEST['id']);

if ($_REQUEST["exec"] == "deletefield"){
    $del_poss_id = (int)$_REQUEST["fdelid"];
    $del_poss = new CustomFieldPossibleValue($del_poss_id);
    $del_poss->delete();
    $customfield = new CustomField((int)$_REQUEST['id']);
}

if ($_REQUEST["exec"] == "save"){
    $id = (int)$_REQUEST["id"];
    if ($_REQUEST["possiblevalues"]){
        foreach ($_REQUEST["possiblevalues"] as $possiblevalue) {
            if (!CustomFieldPossibleValue::exists($id,$possiblevalue)){
                $possarray = [
                    "field" => $id,
                    "value" => $possiblevalue,
                ];
                $customfieldpossvalue = new CustomFieldPossibleValue(0, $possarray);
                $customfieldpossvalue->save();
            }
        }
    }

    $array = [
        "class" => "Article",
        "name" => $_REQUEST["name"],
        "type" => $_REQUEST["type"],
        "filter" => $_REQUEST["filter"],
    ];
    $customfield = new CustomField($id, $array);
    $customfield->save();
}

?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page=libs/modules/customfields/custom.field.article.overview.admin.php',null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#custom_field').submit();",'glyphicon-floppy-disk');
if ($_USER->isAdmin() && $customfield->getId()>0){
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/customfields/custom.field.article.overview.admin.php&exec=delete&did=".$customfield->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">Artikel Freifeld</h3>
	  </div>
	  <div class="panel-body">
          <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="custom_field" id="custom_field" method="post"
                class="form-horizontal" role="form">
              <input type="hidden" name="exec" value="save">
              <input type="hidden" name="id" value="<?php echo $customfield->getId();?>">
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Feldname</label>
                  <div class="col-sm-10">
                      <input type="text" class="form-control" name="name" id="name" value="<?php echo $customfield->getName();?>">
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Typ</label>
                  <div class="col-sm-10">
                      <select name="type" id="type" class="form-control">
                          <option value="1" <?php if ($customfield->getType() == CustomField::TYPE_INPUT) echo ' selected ';?>>Input</option>
                          <option value="2" <?php if ($customfield->getType() == CustomField::TYPE_SELECT) echo ' selected ';?>>Select</option>
                          <option value="3" <?php if ($customfield->getType() == CustomField::TYPE_CHECKBOX) echo ' selected ';?>>Checkbox</option>
                      </select>
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Warengruppe:</label>
                  <div class="col-sm-3">
                      <select name="filter" id="filter" class="form-control">
                          <option value="0">- Alle -</option>
                          <?php
                          $all_tradegroups = Tradegroup::getAllTradegroups();
                          foreach ($all_tradegroups as $tg) {
                              ?>
                              <option value="<?= $tg->getId() ?>" <?php if ($customfield->getFilter() == $tg->getId()) echo ' selected ';?>>
                                  <?= $tg->getTitle() ?></option>
                              <? printSubTradegroupsForSelect($tg->getId(), 0, $customfield->getFilter());
                          }
                          ?>
                      </select>
                  </div>
              </div>
              <?php if ($customfield->getId() > 0 && $customfield->getType() == CustomField::TYPE_SELECT){?>
                  <div class="panel panel-default">
                      <div class="panel-heading">
                          <h3 class="panel-title">
                              Mögliche Werte
                              <span class="pull-right">
                                <button class="btn btn-xs btn-success" type="button"
                                        onclick="addField();">
                                    <span class="glyphicons glyphicons-plus"></span>
                                    Neuer Wert
                                </button>
                              </span>
                          </h3>
                      </div>
                      <div class="panel-body">
                          <div id="possvalues">
                              <?php foreach ($customfield->getPossiblevalues() as $possiblevalue) { ?>
                                  <div class="row">
                                      <div class="col-md-10">
                                          <input type="text" readonly class="form-control" name="possiblevalues[]"
                                                 value="<?php echo $possiblevalue->getValue(); ?>">
                                      </div>
                                      <div class="col-md-2">
                                          <span class="glyphicons glyphicons-remove pointer"
                                                onclick="window.location.href='index.php?page=libs/modules/customfields/custom.field.article.edit.admin.php&id=<?php echo $customfield->getId();?>&exec=deletefield&fdelid=<?php echo $possiblevalue->getId();?>';"></span>
                                      </div>
                                  </div>
                              <?php } ?>
                          </div>
                      </div>
                  </div>
              <?php } ?>
          </form>
	  </div>
</div>

<script language="JavaScript">
    function addField(){
        var insert = '<div><input type="text" class="form-control" name="possiblevalues[]"></div>';
        $('#possvalues').append(insert);
    }
</script>