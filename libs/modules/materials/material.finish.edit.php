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
    $delmatfinish = new MaterialPaper($_REQUEST['delid']);
    $delmatfinish->delete();
    echo "<script>window.location.href='index.php?page=libs/modules/materials/material.overview.php';</script>";
}

if ($_REQUEST["exec"] == "save") {
    $array = [
        'name' => $_REQUEST["name"],
        'type' => 1,
        'description' => $DB->escape(trim($_REQUEST["description"])),
        'article' => $_REQUEST["article"],
    ];

    $matfinish = new MaterialFinish((int)$_REQUEST["id"], $array);
    $matfinish->save();
    $_REQUEST["id"] = $matfinish->getId();
}

$matfinish = new MaterialFinish($_REQUEST['id']);

?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page=libs/modules/materials/material.overview.php',null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#form').submit();",'glyphicon-floppy-disk');
if ($matfinish->getId()>0){
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/materials/materials.finish.edit.php&exec=delete&delid=".$matfinish->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Lack - <?php echo $matfinish->getName();?></h3>
    </div>
    <div class="panel-body">
        <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="form" id="form" method="post" class="form-horizontal" role="form">
            <input type="hidden" name="exec" value="save">
            <input type="hidden" name="id" value="<?php echo $_REQUEST['id'];?>">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="name" id="name" value="<?php echo $matfinish->getName();?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Beschreibung</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="description" id="description"><?php echo $matfinish->getDescription();?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Zug. Artikel</label>
                <div class="col-sm-10">
                    <select id="article" name="article" class="form-control">
                        <?php if ($matfinish->getArticle()->getId() > 0) echo '<option value="'.$matfinish->getArticle()->getId().'">'.$matfinish->getArticle()->getTitle().' ('.$matfinish->getArticle()->getNumber().')</option>';?>
                    </select>
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
        }).val(<?php echo $matfinish->getArticle()->getId();?>).trigger('change');
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