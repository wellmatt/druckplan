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

<div id="fl_menu">
    <div class="label">Quick Move</div>
    <div class="menu">
        <a href="#top" class="menu_item">Seitenanfang</a>
        <a href="index.php?page=libs/modules/storage/storage.overview.php" class="menu_item">Zurück</a>
        <a href="#" class="menu_item" onclick="$('#storagearea_form').submit();">Speichern</a>
        <?php if ($storagearea->getId() > 0){ ?>
        <a href="#" class="menu_item_delete" onclick="askDel('index.php?page=libs/modules/storage/storage.overview.php&exec=delete&id=<?=$storagearea->getId()?>');">Löschen</a>
        <?php } ?>
    </div>
</div>

<table width="100%">
    <tr>
        <td width="150" class="content_header"><span style="font-size: 13px"><?=$header_title?></span></td>
        <td width="250" class="content_header" align="right">
            <?=$savemsg?>
        </td>
    </tr>
</table>
<br />

<form id="storagearea_form" name="storagearea_form" method="post">
    <input type="hidden" id="id" name="id" value="<?=$storagearea->getId()?>" />
    <input type="hidden" id="exec" name="exec" value="edit" />
    <input type="hidden" id="subexec" name="subexec" value="save" />
    <div class="box1">
        <table border="0" cellpadding="3" cellspacing="1" width="100%">
            <colgroup>
                <col width="130">
                <col>
            </colgroup>
            <tr>
                <td class="content_header"><?=$_LANG->get('Lagerplatzname')?>: </td>
                <td class="content_row_clear">
                    <input type="text" id="st_name" name="st_name" style="width:200px" value="<?=$storagearea->getName()?>"
                           onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                </td>
            </tr>
            <tr>
                <td class="content_header"><?=$_LANG->get('Beschreibung')?>:</td>
                <td class="content_row_clear">
			<textarea rows="8" cols="80" type="text" id="st_description" name="st_description"
                      class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)"><?=$storagearea->getDescription()?></textarea>
                </td>
            </tr>
            <tr>
                <td class="content_header"><?=$_LANG->get('Ort')?>: </td>
                <td class="content_row_clear">
                    <input type="text" id="st_location" name="st_location" style="width:200px" value="<?=$storagearea->getLocation()?>"
                           onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                </td>
            </tr>
            <tr>
                <td class="content_header"><?=$_LANG->get('Gang')?>: </td>
                <td class="content_row_clear">
                    <input type="text" id="st_corridor" name="st_corridor" style="width:200px" value="<?=$storagearea->getCorridor()?>"
                           onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                </td>
            </tr>
            <tr>
                <td class="content_header"><?=$_LANG->get('Regal')?>: </td>
                <td class="content_row_clear">
                    <input type="text" id="st_shelf" name="st_shelf" style="width:200px" value="<?=$storagearea->getShelf()?>"
                           onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                </td>
            </tr>
            <tr>
                <td class="content_header"><?=$_LANG->get('Reihe')?>: </td>
                <td class="content_row_clear">
                    <input type="text" id="st_line" name="st_line" style="width:200px" value="<?=$storagearea->getLine()?>"
                           onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                </td>
            </tr>
            <tr>
                <td class="content_header"><?=$_LANG->get('Ebene')?>: </td>
                <td class="content_row_clear">
                    <input type="text" id="st_layer" name="st_layer" style="width:200px" value="<?=$storagearea->getLayer()?>"
                           onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                </td>
            </tr>
        </table>
    </div>
    <?php if ($storagearea->getId()>0){ ?>
    <div class="box2">
        <div class="box1">
            <b>neue Position:</b>
            <img src="images/icons/plus.png" title="neuer Artikel" class="pointer" onclick="callBoxFancyArtFrame('libs/modules/storage/storage.article.frame.php?stid=<?php echo $storagearea->getId();?>');"/>
        </div>
        <table id="order_pos" width="100%" cellpadding="0" cellspacing="0" class="stripe hover row-border order-column">
            <thead>
            <tr>
                <th>#</th>
                <th>Kunde</th>
                <th>Lagermenge</th>
                <th>Mindestmenge</th>
                <th>Verantwortlicher</th>
                <th>Beschreibung</th>
                <th>Bemerkung</th>
                <th>Versandart</th>
                <th>Verpackungsart</th>
                <th>Belegung</th>
            </tr>
            </thead>
        </table>
    </div>
    <?php } ?>
</form>

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