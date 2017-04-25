<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'textblock.class.php';


if ($_REQUEST["exec"] == "save"){
    $array = [
        'name' => $_REQUEST["name"],
        'text' => $DB->escape(trim($_REQUEST["text"]))
    ];

    if ($_REQUEST['mod_ticket'] == 1)
        $array['mod_ticket'] = 1;
    else
        $array['mod_ticket'] = 0;

    if ($_REQUEST['mod_mail'] == 1)
        $array['mod_mail'] = 1;
    else
        $array['mod_mail'] = 0;

    if ($_REQUEST['id'] == 0) {
        $array['crtuser'] = $_USER->getId();
        $array['crtdate'] = time();
    } else {
        $array['uptuser'] = $_USER->getId();
        $array['uptdate'] = time();
    }

    $textblock = new TextBlock((int)$_REQUEST["id"], $array);
    $textblock->save();
    $_REQUEST["id"] = $textblock->getId();

    foreach (TextBlockGroup::getAllForTextblock($textblock) as $textblockgroup) {
        $textblockgroup->delete();
    }

    foreach ($_REQUEST["groups"] as $group) {
        $grparray = [
            'textblock' => $textblock->getId(),
            'group' => $group
        ];
        $textblockgrp = new TextBlockGroup(0, $grparray);
        $textblockgrp->save();
    }
}

$textblock = new TextBlock((int)$_REQUEST["id"]);


?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page=libs/modules/textblocks/textblock.overview.php',null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#textblock_form').submit();",'glyphicon-floppy-disk');
if ($textblock->getId()>0){
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/textblocks/textblock.overview.php&exec=delete&delid=".$textblock->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Textbaustein - <?php echo $textblock->getName();?></h3>
    </div>
    <div class="panel-body">
        <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="textblock_form" id="textblock_form" method="post" class="form-horizontal" role="form">
            <input type="hidden" name="id" value="<?php echo $_REQUEST["id"];?>">
            <input type="hidden" name="exec" value="save">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="name" id="name" value="<?php echo $textblock->getName();?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Text</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="text" id="text"><?php echo $textblock->getText();?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Tickets</label>
                <div class="col-sm-10">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="mod_ticket" id="mod_ticket" value="1" <?php if ($textblock->getModTicket() == 1) echo ' checked ';?>>
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Mails</label>
                <div class="col-sm-10">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="mod_mail" id="mod_mail" value="1" <?php if ($textblock->getModMail() == 1) echo ' checked ';?>>
                        </label>
                    </div>
                </div>
            </div>
            <?php if ($textblock->getId() > 0){?>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Erstellt</label>
                    <div class="col-sm-10 form-text">
                        <?php echo date('d.m.y H:i',$textblock->getCrtdate()).' von '.$textblock->getCrtuser()->getNameAsLine();?>
                    </div>
                </div>
                <?php if ($textblock->getUptdate() > 0){?>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Geändert</label>
                        <div class="col-sm-10 form-text">
                            <?php echo date('d.m.y H:i',$textblock->getUptdate()).' von '.$textblock->getUptuser()->getNameAsLine();?>
                        </div>
                    </div>
                <?php } ?>
                <br/>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Freigegeben für</h3>
                    </div>
                    <div class="panel-body">
                        <?php foreach (Group::getAllGroups() as $allGroup) {
                            $selected = false;
                            foreach ($textblock->getGroups() as $group) {
                                if ($allGroup->getId() == $group->getGroup()->getId())
                                    $selected = true;
                            }
                            ?>
                            <div class="form-group">
                                <label for="" class="col-sm-4 control-label"><?php echo $allGroup->getName();?></label>
                                <div class="col-sm-8">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="groups[]" id="group_<?php echo $allGroup->getId();?>" value="<?php echo $allGroup->getId();?>" <?php if ($selected) echo ' checked ';?>>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </form>
    </div>
</div>

<script language="JavaScript">
    $(function () {
        var editor = CKEDITOR.replace( 'text', {
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
