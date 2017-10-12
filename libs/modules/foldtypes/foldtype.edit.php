<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'foldtype.class.php';

if ($_REQUEST["exec"] == "save") {
    $array = [
        'name' => $_REQUEST["name"],
        'type' => $_REQUEST["type"],
        'breaks' => $_REQUEST["breaks"],
        'imageid' => $_REQUEST["imageid"],
        'description' => $DB->escape(trim($_REQUEST["description"]))
    ];

    $foldtype = new FoldType((int)$_REQUEST["id"], $array);
    $foldtype->save();
    $_REQUEST["id"] = $foldtype->getId();
}

$foldtype = new FoldType($_REQUEST['id']);

?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page=libs/modules/foldtypes/foldtype.overview.php',null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#form').submit();",'glyphicon-floppy-disk');
if ($foldtype->getId()>0){
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/foldtypes/foldtype.overview.php&exec=delete&delid=".$foldtype->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Falzart - <?php echo $foldtype->getName();?></h3>
    </div>
    <div class="panel-body">
        <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="form" id="form" method="post" class="form-horizontal" role="form">
            <input type="hidden" name="exec" value="save">
            <input type="hidden" name="id" value="<?php echo $_REQUEST['id'];?>">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="name" id="name" value="<?php echo $foldtype->getName();?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Beschreibung</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="description" id="description"><?php echo $foldtype->getDescription();?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Typ</label>
                <div class="col-sm-10">
                    <select name="type" id="type" class="form-control">
                        <?php
                        $types = $foldtype->getTypes();
                        foreach ($types as $type) {
                            if ($type['id'] == $foldtype->getType())
                                echo '<option selected value="' . $type['id'] . '">' . $type['name'] . '</option>';
                            else
                                echo '<option value="' . $type['id'] . '">' . $type['name'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Bild</label>
                <div class="col-sm-10">
                    <input type="hidden" name="imageid" id="fileid" value="<?php echo $foldtype->getImageid();?>">
                    <?php
                    if ($foldtype->getId() > 0 && $foldtype->getImageid() > 0){
                        $image = new Filestorage($foldtype->getImageid());
                        $imagedata = base64_encode($image->getContent());
                        ?>
                        <img src="data:image/jpg;base64,<?php echo $imagedata;?>">
                    <?php } ?>
                    <br>
                    <button class="btn btn-xs btn-success" type="button" onclick="callBoxFancyUploadFile('libs/modules/filestorage/filestorage.upload.frame.php?module=Foldtype')">
                        <span class="glyphicons glyphicons-plus"></span>
                        Bild ändern
                    </button>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Brüche</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="breaks" id="breaks" value="<?php echo $foldtype->getBreaks();?>">
                </div>
            </div>
        </form>
    </div>
</div>

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