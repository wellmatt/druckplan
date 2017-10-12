<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'material.class.php';

if ($_REQUEST['exec'] == "delete"){
    $delmatpaper = new MaterialPaper($_REQUEST['delid']);
    $delmatpaper->delete();
    echo "<script>window.location.href='index.php?page=libs/modules/materials/material.overview.php';</script>";
}

if ($_REQUEST["exec"] == "save") {
    $array = [
        'name' => $_REQUEST["name"],
        'type' => 1,
        'description' => $DB->escape(trim($_REQUEST["description"])),
        'article' => $_REQUEST["article"],
        'info' => $_REQUEST["info"],
        'number' => $_REQUEST["number"],
        'weight' => $_REQUEST["weight"],
        'width' => $_REQUEST["width"],
        'height' => $_REQUEST["height"],
        'direction' => $_REQUEST["direction"],
        'color' => $_REQUEST["color"],
        'weightper1000' => $_REQUEST["weightper1000"],
        'ream' => (int)$_REQUEST["ream"],
    ];

    $matpaper = new MaterialPaper((int)$_REQUEST["id"], $array);
    $matpaper->save();
    $_REQUEST["id"] = $matpaper->getId();
}

$matpaper = new MaterialPaper($_REQUEST['id']);

?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page=libs/modules/materials/material.overview.php',null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#form').submit();",'glyphicon-floppy-disk');
if ($matpaper->getId()>0){
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/materials/materials.paper.edit.php&exec=delete&delid=".$matpaper->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Papier - <?php echo $matpaper->getName();?></h3>
    </div>
    <div class="panel-body">
        <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="form" id="form" method="post" class="form-horizontal" role="form">
            <input type="hidden" name="exec" value="save">
            <input type="hidden" name="id" value="<?php echo $_REQUEST['id'];?>">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="name" id="name" value="<?php echo $matpaper->getName();?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Beschreibung</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="description" id="description"><?php echo $matpaper->getDescription();?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Zug. Artikel</label>
                <div class="col-sm-10">
                    <select id="article" name="article" class="form-control">
                        <?php if ($matpaper->getArticle()->getId() > 0) echo '<option value="'.$matpaper->getArticle()->getId().'">'.$matpaper->getArticle()->getTitle().' ('.$matpaper->getArticle()->getNumber().')</option>';?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Info</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="info" id="info" value="<?php echo $matpaper->getInfo();?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Nummer</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="number" id="number" value="<?php echo $matpaper->getNumber();?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Grammatur (g)</label>
                <div class="col-sm-10">
                    <input type="number" step="1.0" class="form-control" name="weight" id="weight" value="<?php echo $matpaper->getWeight();?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Breite (cm)</label>
                <div class="col-sm-10">
                    <input type="number" step="0.1" class="form-control" name="width" id="width" value="<?php echo $matpaper->getWidth();?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Höhe (cm)</label>
                <div class="col-sm-10">
                    <input type="number" step="0.1" class="form-control" name="height" id="height" value="<?php echo $matpaper->getHeight();?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Laufrichtung</label>
                <div class="col-sm-10">
                    <select name="direction" id="direction" class="form-control">
                        <option value="1" <?php if ($matpaper->getDirection() == 1) echo ' selected ';?>>Schmale Bahn</option>
                        <option value="2" <?php if ($matpaper->getDirection() == 2) echo ' selected ';?>>Breite Bahn</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Farbe</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="color" id="color" value="<?php echo $matpaper->getColor();?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Gewicht (1000 Bogen)</label>
                <div class="col-sm-10">
                    <input type="number" step="0.1" class="form-control" name="weightper1000" id="weightper1000" value="<?php echo $matpaper->getWeightper1000();?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Ries (Verpackte Einheit)</label>
                <div class="col-sm-10">
                    <input type="number" step="1.0" class="form-control" name="ream" id="ream" value="<?php echo $matpaper->getReam();?>">
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(function () {
        $("#article").select2({
            ajax: {
                url: "libs/basic/ajax/select2.ajax.php?ajax_action=search_article",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 3,
            language: "de",
            multiple: false,
            allowClear: false,
            tags: false
        }).val(<?php echo $matpaper->getArticle()->getId();?>).trigger('change');
    });
</script>
<script language="JavaScript">
    $(function () {
        var editor = CKEDITOR.replace( 'description', {
            // Define the toolbar groups as it is a more accessible solution.
            toolbarGroups: [
                { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
                { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
                { name: 'links' },
                { name: 'insert' },
                { name: 'tools' },
                { name: 'others' },
                '/',
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
                { name: 'styles' },
                { name: 'colors' }
            ]
            // Remove the redundant buttons from toolbar groups defined above.
            //removeButtons: 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar'
        } );
    });
</script>