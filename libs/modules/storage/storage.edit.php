<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/storage/storage.area.class.php';

if ($_REQUEST["subexec"] == "save"){
    $array = [
        'name' => $_REQUEST["st_name"],
        'description' => $_REQUEST["st_description"],
        'location' => $_REQUEST["st_location"],
        'corridor' => $_REQUEST["st_corridor"],
        'shelf' => $_REQUEST["st_shelf"],
        'line' => $_REQUEST["st_line"],
        'layer' => $_REQUEST["st_layer"],
        'prio' => $_REQUEST['st_prio'],
    ];
    $storagearea = new StorageArea((int)$_REQUEST["id"], $array);
    $storagearea->save();
    $_REQUEST["id"] = $storagearea->getId();
}

if ($_REQUEST["exec"] == "edit"){
    $storagearea = new StorageArea($_REQUEST["id"]);
    $header_title = "Lagerplatz editieren";
} else {
    $storagearea = new StorageArea();
    $header_title = "Lagerplatz erstellen";
}

?>
<script src="jscripts/jvalidation/dist/jquery.validate.min.js"></script>
<script src="jscripts/jvalidation/dist/localization/messages_de.min.js"></script>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page=libs/modules/storage/storage.overview.php',null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#storagearea_form').submit();",'glyphicon-floppy-disk');
if($_USER->hasRightsByGroup(Group::RIGHT_DELETE_COLINV) || $_USER->isAdmin()){
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/storage/storage.overview.php&exec=delete&id=".$storagearea->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>


<?php if (isset($savemsg)) { ?>
<div class="alert">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<strong>Hinweis!</strong> <?=$savemsg?>
</div>
<?php } ?>

<div class="row">
    <div class="col-md-12">
    	<div class="panel panel-default">
    		  <div class="panel-heading">
    				<h3 class="panel-title"><?=$header_title?></h3>
    		  </div>
    		  <div class="panel-body">
    				<form action="index.php?page=<?=$_REQUEST['page']?>" id="storagearea_form" name="storagearea_form" method="post" role="form" class="form-horizontal">
                        <input type="hidden" id="id" name="id" value="<?=$storagearea->getId()?>" />
                        <input type="hidden" id="exec" name="exec" value="edit" />
                        <input type="hidden" id="subexec" name="subexec" value="save" />

                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Lagerplatzname</label>
                            <div class="col-sm-4">
                                <input type="text" id="st_name" name="st_name" class="form-control" value="<?=$storagearea->getName()?>"
                                       onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Beschreibung</label>
                            <div class="col-sm-4">
                                <textarea rows="8" cols="80" type="text" id="st_description" name="st_description"
                                          class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)"><?=$storagearea->getDescription()?></textarea>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Ort</label>
                            <div class="col-sm-4">
                                <input type="text" id="st_location" name="st_location" class="form-control" value="<?=$storagearea->getLocation()?>"
                                       onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Gang</label>
                            <div class="col-sm-4">
                                <input type="text" id="st_corridor" name="st_corridor"class="form-control" value="<?=$storagearea->getCorridor()?>"
                                       onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Regal</label>
                            <div class="col-sm-4">
                                <input type="text" id="st_shelf" name="st_shelf" class="form-control" value="<?=$storagearea->getShelf()?>"
                                       onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Reihe</label>
                            <div class="col-sm-4">
                                <input type="text" id="st_line" name="st_line" class="form-control" value="<?=$storagearea->getLine()?>"
                                       onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Ebene</label>
                            <div class="col-sm-4">
                                <input type="text" id="st_layer" name="st_layer" class="form-control" value="<?=$storagearea->getLayer()?>"
                                       onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Priorität</label>
                            <div class="col-sm-4">
                                <select id="st_prio" name="st_prio" class="form-control">
                                    <option value="0" <?php if ($storagearea->getPrio() == 0) echo ' selected ';?>>Niedrig</option>
                                    <option value="1" <?php if ($storagearea->getPrio() == 1) echo ' selected ';?>>Mittel</option>
                                    <option value="2" <?php if ($storagearea->getPrio() == 2) echo ' selected ';?>>Hoch</option>
                                </select>
                            </div>
                        </div>

                        <?php if ($storagearea->getId()>0){ ?>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Belegung</label>
                            <div class="col-sm-4">
                                <?php echo StoragePosition::getAllocationForArea($storagearea);?>%
                            </div>
                        </div>
                        <?php } ?>

    				</form>
    		  </div>
    	</div>
    </div>
</div>
<?php if ($storagearea->getId()>0){
    $book_entries = StorageBookEnrty::getAllForArea($storagearea);?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                  <div class="panel-heading">
                        <h3 class="panel-title">Lagerpositionen <img src="images/icons/plus.png" title="neuer Artikel" class="pointer" onclick="callBoxFancyArtFrame('libs/modules/storage/storage.article.frame.php?stid=<?php echo $storagearea->getId();?>');"/></h3>
                  </div>
                  <div class="panel-body" id="storagepositionbox">
                  </div>
            </div>
        </div>
    </div>
    <?php if (count($book_entries)>0){ ?>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Letzte 25 Buchungen</h3>
                    </div>
                    <br>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Artikel</th>
                                <th>Anzahl</th>
                                <th>Belegung</th>
                                <th>Herkunft</th>
                                <th>Datum</th>
                                <th>User</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($book_entries as $booke){ ?>
                                <tr>
                                    <td><?php echo $booke->getId();?></td>
                                    <td><?php echo $booke->getArticle()->getTitle();?></td>
                                    <?php if ($booke->getType() == 1){?>
                                        <td>+<?php echo $booke->getAmount();?></td>
                                    <?php } elseif ($booke->getType() == 2){?>
                                        <td>-<?php echo $booke->getAmount();?></td>
                                    <?php } ?>
                                    <td><?php echo $booke->getAlloc();?>%</td>
                                    <td><?php echo $booke->getOrigin()->getNumber();?></td>
                                    <td><?php echo date('d.m.y H:i',$booke->getCrtdate());?></td>
                                    <td><?php echo $booke->getCrtuser()->getNameAsLine();?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } ?>


<script>
    $(function(){
        load_content();
    });
    function unblock(){
        $('#storagepositionbox').unblock();
    }
    function load_content(){
        $('#storagepositionbox').block({ message: '<h3><img src="images/page/busy.gif"/> einen Augenblick...</h3>' });
        $('#storagepositionbox').load( "libs/modules/storage/storage.edit.positions.php?area="+$('#id').val(), null, unblock );
    }
</script>

<script language="JavaScript">
    $(document).ready(function () {
        $('#storagearea_form').validate({
            rules: {
                'st_name': {
                    required: true
                },
                'st_layer': {
                    required: false
                },
                'st_line': {
                    required: false
                },
                'st_shelf': {
                    required: false
                },
                'st_corridor': {
                    required: false
                },
                'st_location': {
                    required: false
                },
                'st_description': {
                    required: false
                },
            },
            ignore: []
        });
    });
</script>
<script>
    $(function() {
        $("a#hiddenclicker_artframe").fancybox({
            'type'    : 'iframe',
            'transitionIn'	:	'elastic',
            'transitionOut'	:	'elastic',
            'speedIn'		:	600,
            'speedOut'		:	200,
            'padding'		:	25,
            'margin'        :   25,
            'scrolling'     :   'no',
            'width'		    :	1000,
            'height'        :   800,
            'onComplete'    :   function() {
                $('#fancybox-frame').load(function() { // wait for frame to load and then gets it's height
// 	                		      $('#fancybox-content').height($(this).contents().find('body').height()+300);
                    $('#fancybox-wrap').css('top','25px');
                });
            },
            'overlayShow'	:	true,
            'helpers'		:   { overlay:null, closeClick:true }
        });
    });
    function callBoxFancyArtFrame(my_href) {
        var j1 = document.getElementById("hiddenclicker_artframe");
        j1.href = my_href;
        $('#hiddenclicker_artframe').trigger('click');
    }
</script>
<div id="hidden_clicker95" style="display:none"><a id="hiddenclicker_artframe" href="http://www.google.com" >Hidden Clicker</a></div>