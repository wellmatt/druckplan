<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */

$revenueaccountcategory = new RevenueaccountCategory((int)$_REQUEST["id"]);

if ($_REQUEST["subexec"] == "save"){
    $array = [
        "title" => $_REQUEST["title"]
    ];
    $revenueaccountcategory = new RevenueaccountCategory((int)$_REQUEST["id"], $array);
    $ret = $revenueaccountcategory->save();
    $_REQUEST["id"] = $revenueaccountcategory->getId();
}

?>
<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang', '#top', null, 'glyphicon-chevron-up');
$quickmove->addItem('Zurück', 'index.php?page=libs/modules/revenueaccounts/revenueaccount.overview.php', null, 'glyphicon-step-backward');
$quickmove->addItem('Speichern', '#', "$('#edit_form').submit();", 'glyphicon-floppy-disk');
if ($_USER->isAdmin() && $revenueaccountcategory->getId() > 0) {
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/revenueaccounts/revenueaccount.overview.php&remove=" .$revenueaccountcategory->getId() . "');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Erlöskontokategorie bearbeiten</h3>
    </div>
    <div class="panel-body">
        <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="edit_form" id="edit_form" method="post"
              class="form-horizontal" role="form">
            <input type="hidden" name="subexec" value="save">
            <input type="hidden" name="id" value="<?php echo $revenueaccountcategory->getId();?>">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Titel</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="title" id="title" placeholder="Erlöse Digital" value="<?php echo $revenueaccountcategory->getTitle();?>">
                </div>
            </div>
        </form>
    </div>
</div>