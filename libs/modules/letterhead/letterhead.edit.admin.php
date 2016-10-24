<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/letterhead/letterhead.class.php';

$letterhead = new Letterhead((int)$_REQUEST["id"]);

if ($_REQUEST["exec"] == "save")
{
    $array = [
        'name' => $_REQUEST["name"],
        'type' => $_REQUEST["type"],
        'docformat1' => $_REQUEST["docformat1"],
        'docformat2' => $_REQUEST["docformat2"],
        'uptdate' => time()
    ];
    if ((int)$_REQUEST["id"] == 0)
        $array["crtdate"] = time();

    $letterhead = new Letterhead((int)$_REQUEST["id"], $array);
    $letterhead->save();
    if ($letterhead->getId() > 0){
        if($_FILES){
            move_uploaded_file($_FILES["letterhead1"]["tmp_name"], "./docs/letterheads/".$letterhead->getId()."_1.pdf");
            move_uploaded_file($_FILES["letterhead2"]["tmp_name"], "./docs/letterheads/".$letterhead->getId()."_2.pdf");
        }
    }
    $letterhead = new Letterhead($letterhead->getId());
}
if ($_REQUEST["exec"] == "delete"){
    $letterhead->delete();
    echo '<script>window.location.href=\'index.php?page=libs/modules/letterhead/letterhead.admin.php\';</script>';
}

?>
<script src="jscripts/jvalidation/dist/jquery.validate.min.js"></script>
<script src="jscripts/jvalidation/dist/localization/messages_de.min.js"></script>

<script language="JavaScript">
    $(document).ready(function () {
        $('#letterheadform').validate({
            rules: {
                'name': {
                    required: true
                },
                'type': {
                    required: true
                },
                'letterhead1': {
                    required: true
                },
                'letterhead2': {
                    required: true
                },
                'docformat1': {
                    required: true
                },
                'docformat2': {
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
$quickmove->addItem('Zurück','index.php?page=libs/modules/letterhead/letterhead.admin.php',null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#letterheadform').submit();",'glyphicon-floppy-disk');
if ($letterhead->getId()>0){
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/letterhead/letterhead.edit.admin.php&exec=delete&id=".$letterhead->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" class="form-horizontal" name="letterheadform" id="letterheadform" role="form" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo (int)$_REQUEST["id"];?>">
    <input type="hidden" name="exec" value="save">
    <div class="panel panel-default">
          <div class="panel-heading">
                <h3 class="panel-title">Briefpapier</h3>
          </div>
          <div class="panel-body">
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Name</label>
                  <div class="col-sm-4">
                      <input type="text" class="form-control" name="name" id="name" value="<?php echo $letterhead->getName();?>" placeholder="Name" required>
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Typ</label>
                  <div class="col-sm-4">
                      <select name="type" id="type" class="form-control" required>
                          <?php
                          foreach (Document::getTypes() as $index => $value) {
                              if ($letterhead->getId() > 0 && $letterhead->getType() == $value)
                                  echo '<option selected value="' . $value . '">' . $index . '</option>';
                              else
                                  echo '<option value="' . $value . '">' . $index . '</option>';
                          }
                          ?>
                      </select>
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Dokument Format Seite 1</label>
                  <div class="col-sm-4">
                      <select name="docformat1" id="docformat1" class="form-control" required>
                          <?php
                          foreach (DocumentFormat::fetch() as $item) {
                              if ($item->getId() == $letterhead->getDocformat1()->getId())
                                  echo '<option selected value="' . $item->getId() . '">' . $item->getName() . '</option>';
                              else
                                  echo '<option value="' . $item->getId() . '">' . $item->getName() . '</option>';
                          }
                          ?>
                      </select>
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">Dokument Format Seite 2</label>
                  <div class="col-sm-4">
                      <select name="docformat2" id="docformat2" class="form-control" required>
                          <?php
                          foreach (DocumentFormat::fetch() as $item) {
                              if ($item->getId() == $letterhead->getDocformat2()->getId())
                                  echo '<option selected value="' . $item->getId() . '">' . $item->getName() . '</option>';
                              else
                                  echo '<option value="' . $item->getId() . '">' . $item->getName() . '</option>';
                          }
                          ?>
                      </select>
                  </div>
              </div>
              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">1. Seite</label>
                  <div class="col-sm-4">
                      <div class="input-group">
                          <label class="input-group-btn">
                    <span class="btn btn-file">
                        Durchsuchen <input style="display: none;"  multiple="" type="file" class="form-control" name="letterhead1" id="letterhead1" required>
                    </span>
                          </label>
                          <input class="form-control" readonly="" type="text">
                      </div>
                      * <b>NUR</b> *.pdf Dateien verwenden!
                  </div>
              </div>

              <div class="form-group">
                  <label for="" class="col-sm-2 control-label">ab 2. Seite</label>
                  <div class="col-sm-4">
                      <div class="input-group">
                          <label class="input-group-btn">
                    <span class="btn btn-file">
                        Durchsuchen <input style="display:none;"  multiple="" type="file" class="form-cntrol" name="letterhead2" id="letterhead2" required>
                    </span>
                          </label>
                          <input class="form-control" readonly="" type="text">
                      </div>
                      * <b>NUR</b> *.pdf Dateien verwenden!
                  </div>
              </div>
          </div>
    </div>
</form>
<script>
    $(function () {
        $(document).on('change', ':file', function () {
            var input = $(this), numFiles = input.get(0).files ? input.get(0).files.length : 1, label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [
                numFiles,
                label
            ]);
        });
        $(document).ready(function () {
            $(':file').on('fileselect', function (event, numFiles, label) {
                var input = $(this).parents('.input-group').find(':text'), log = numFiles > 1 ? numFiles + ' files selected' : label;
                if (input.length) {
                    input.val(log);
                } else {
                    if (log)
                        alert(log);
                }
            });
        });
    });
</script>