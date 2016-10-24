<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *  
 */
require_once 'libs/modules/documentformats/documentformat.class.php';

$docformat = new DocumentFormat((int)$_REQUEST["id"]);

if ($_REQUEST["exec"] == "save")
{
    $array = [
        'name' => $_REQUEST["name"],
        'width' => $_REQUEST["width"],
        'height' => $_REQUEST["height"],
        'orientation' => $_REQUEST["orientation"],
        'doctype' => $_REQUEST["doctype"],
        'margin_top' => $_REQUEST["margin_top"],
        'margin_bottom' => $_REQUEST["margin_bottom"],
        'margin_left' => $_REQUEST["margin_left"],
        'margin_right' => $_REQUEST["margin_right"]
    ];
    $docformat = new DocumentFormat((int)$_REQUEST["id"], $array);
    $docformat->save();
    $docformat = new DocumentFormat($docformat->getId());
}
if ($_REQUEST["exec"] == "delete"){
    $docformat->delete();
    echo '<script>window.location.href=\'index.php?page=libs/modules/documentformats/documentformat.admin.php\';</script>';
}

?>
<script src="jscripts/jvalidation/dist/jquery.validate.min.js"></script>
<script src="jscripts/jvalidation/dist/localization/messages_de.min.js"></script>

<script language="JavaScript">
    $(document).ready(function () {
        $('#DocumentFormatform').validate({
            rules: {
                'name': {
                    required: true
                },
                'width': {
                    required: true
                },
                'height': {
                    required: true
                },
                'orientation': {
                    required: true
                },
                'doctype': {
                    required: true
                },
                'margin_top': {
                    required: true
                },
                'margin_bottom': {
                    required: true
                },
                'margin_left': {
                    required: true
                },
                'margin_right': {
                    required: true
                }
            },
            ignore: []
        });
    });
</script>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page=libs/modules/documentformats/documentformat.admin.php',null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#documentformatform').submit();",'glyphicon-floppy-disk');
if ($docformat->getId()>0){
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/documentformats/documentformat.edit.admin.php&exec=delete&id=".$docformat->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" class="form-horizontal" name="documentformatform" id="documentformatform" role="form" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo (int)$_REQUEST["id"];?>">
    <input type="hidden" name="exec" value="save">
    <div class="panel panel-default">
          <div class="panel-heading">
                <h3 class="panel-title">Dokumentenformat</h3>
          </div>
          <div class="panel-body">
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Name</label>
                  <div class="col-sm-4">
                      <input type="text" class="form-control" name="name" id="name" value="<?php echo $docformat->getName();?>" placeholder="Name" required>
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Typ</label>
                  <div class="col-sm-4">
                      <select name="doctype" id="doctype" class="form-control" required>
                          <?php
                          foreach (Document::getTypes() as $index => $value) {
                              if ($docformat->getId() > 0 && $docformat->getDoctype() == $value)
                                  echo '<option selected value="' . $value . '">' . $index . '</option>';
                              else
                                  echo '<option value="' . $value . '">' . $index . '</option>';
                          }
                          ?>
                      </select>
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Ausrichtung</label>
                  <div class="col-sm-4">
                      <select name="orientation" id="orientation" class="form-control">
                          <option value="<?php echo DocumentFormat::ORI_PORTRAIT;?>" <?php if ($docformat->getOrientation() == DocumentFormat::ORI_PORTRAIT) echo ' selected ';?>>Portrait</option>
                          <option value="<?php echo DocumentFormat::ORI_LANDSCAPE;?>" <?php if ($docformat->getOrientation() == DocumentFormat::ORI_LANDSCAPE) echo ' selected ';?>>Landscape</option>
                      </select>
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Breite (mm)</label>
                  <div class="col-sm-4">
                      <input type="text" class="form-control" name="width" id="width" value="<?php echo $docformat->getWidth();?>" placeholder="">
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Höhe (mm)</label>
                  <div class="col-sm-4">
                      <input type="text" class="form-control" name="height" id="height" value="<?php echo $docformat->getHeight();?>" placeholder="">
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Margin Oben (mm)</label>
                  <div class="col-sm-4">
                      <input type="text" class="form-control" name="margin_top" id="margin_top" value="<?php echo $docformat->getMarginTop();?>" placeholder="">
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Margin Unten (mm)</label>
                  <div class="col-sm-4">
                      <input type="text" class="form-control" name="margin_bottom" id="margin_bottom" value="<?php echo $docformat->getMarginBottom();?>" placeholder="">
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Margin Links (mm)</label>
                  <div class="col-sm-4">
                      <input type="text" class="form-control" name="margin_left" id="margin_left" value="<?php echo $docformat->getMarginLeft();?>" placeholder="">
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Margin Rechts (mm)</label>
                  <div class="col-sm-4">
                      <input type="text" class="form-control" name="margin_right" id="margin_right" value="<?php echo $docformat->getMarginRight();?>" placeholder="">
                  </div>
              </div>
          </div>
    </div>
</form>