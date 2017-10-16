<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'machinegroup.class.php';

if ($_REQUEST['exec'] == "delete") {
    $delid = new MachineGroup($_REQUEST['delid']);
    $delid->delete();
    echo "<script>window.location.href='index.php?page=libs/modules/machines/machinegroup.overview.php';</script>";
}

if ($_REQUEST["exec"] == "save") {
    $array = [
        'name' => $_REQUEST["name"],
        'type' => $_REQUEST["type"]
    ];

    $machgroup = new MachineGroup((int)$_REQUEST["id"], $array);
    $machgroup->save();
    $_REQUEST["id"] = $machgroup->getId();
}

$machgroup = new MachineGroup($_REQUEST['id']);

?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang', '#top', null, 'glyphicon-chevron-up');
$quickmove->addItem('Zurück', 'index.php?page=libs/modules/machines/machinegroup.overview.php', null, 'glyphicon-step-backward');
$quickmove->addItem('Speichern', '#', "$('#form').submit();", 'glyphicon-floppy-disk');
if ($machgroup->getId() > 0) {
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/machines/machinegroup.edit.php&exec=delete&delid=" . $machgroup->getId() . "');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Maschinengruppe - <?php echo $machgroup->getName(); ?></h3>
    </div>
    <div class="panel-body">
        <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="form" id="form" method="post"
              class="form-horizontal" role="form">
            <input type="hidden" name="exec" value="save">
            <input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="name" id="name"
                           value="<?php echo $machgroup->getName(); ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Typ</label>
                <div class="col-sm-10">
                    <select name="type" id="type" class="form-control">
                        <option value="1" <?php if ($machgroup->getType() == 1) echo ' selected ';?>>Agentur</option>
                        <option value="2" <?php if ($machgroup->getType() == 2) echo ' selected ';?>>Vorstufe</option>
                        <option value="3" <?php if ($machgroup->getType() == 3) echo ' selected ';?>>Formherstellung</option>
                        <option value="4" <?php if ($machgroup->getType() == 4) echo ' selected ';?>>Druck</option>
                        <option value="5" <?php if ($machgroup->getType() == 5) echo ' selected ';?>>Großformatdruck</option>
                        <option value="6" <?php if ($machgroup->getType() == 6) echo ' selected ';?>>Weiterverarbeitung</option>
                        <option value="7" <?php if ($machgroup->getType() == 7) echo ' selected ';?>>Lettershop</option>
                        <option value="8" <?php if ($machgroup->getType() == 8) echo ' selected ';?>>Verpackung</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>