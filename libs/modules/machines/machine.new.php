<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'machine.class.php';

if ($_REQUEST["exec"] == "save") {
    $array = [
        'title' => $_REQUEST["title"],
        'type' => $_REQUEST["type"]
    ];

    $machine = new Machine(0, $array);
    $machine->save();
    $_REQUEST["id"] = $machine->getId();
    echo "<script>document.location = 'index.php?page=libs/modules/machines/machine.edit.php&id={$machine->getId()}';</script>";
}

?>
<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang', '#top', null, 'glyphicon-chevron-up');
$quickmove->addItem('Zurück', 'index.php?page=libs/modules/machines/machine.overview.php', null, 'glyphicon-step-backward');
$quickmove->addItem('Weiter', '#', "$('#form').submit();", 'glyphicon-floppy-disk');
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Neue Maschine</h3>
    </div>
    <div class="panel-body">
        <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="form" id="form" method="post"
              class="form-horizontal" role="form">
            <input type="hidden" name="exec" value="save">
            <input type="hidden" name="id" value="<?php echo $_REQUEST['id']; ?>">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="title" id="title">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Typ</label>
                <div class="col-sm-10">
                    <select name="type" id="type" class="form-control">
                        <?php
                        $tmp = new Machine();
                        $types = $tmp->getTypes();
                        for ($i = 1; $i < 7; $i++){
                            switch ($i){
                                case 1:
                                    echo '<optgroup label="Agentur">';
                                    break;
                                case 2:
                                    echo '<optgroup label="Vorstufe">';
                                    break;
                                case 3:
                                    echo '<optgroup label="Formherstellung">';
                                    break;
                                case 4:
                                    echo '<optgroup label="Druck">';
                                    break;
                                case 5:
                                    echo '<optgroup label="Großformatdruck">';
                                    break;
                                case 6:
                                    echo '<optgroup label="Weiterverarbeitung">';
                                    break;
                            }
                            foreach ($types as $type) {
                                if ($type['cat'] == $i){
                                    echo '<option value="' . $type['id'] . '">' . $type['name'] . '</option>';
                                }
                            }
                            echo '</optgroup>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>