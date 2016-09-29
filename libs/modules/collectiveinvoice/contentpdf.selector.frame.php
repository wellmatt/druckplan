<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
chdir("../../../");
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once("libs/basic/countries/country.class.php");
require_once('libs/modules/businesscontact/businesscontact.class.php');
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/comment/comment.class.php';
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/collectiveinvoice/contentpdf.class.php';

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


if ($_USER == false){
    die("Login failed");
}

if ($_REQUEST["exec"] == "save" && $_REQUEST["opid"]){
    $todelete = ContentPdf::getAllForOrderposition(new Orderposition($_REQUEST["opid"]));
    foreach ($todelete as $item) {
        $item->delete();
    }
    foreach ($_REQUEST["content"] as $part => $values) {
        foreach ($values as $index => $value) {
            $array = [
                'orderposition' => $_REQUEST["opid"],
                'part' => $part,
                'pagenum' => $index,
                'file' => $value['file'],
                'pagina' => $value['pagina'],
            ];
            $contentpdf = new ContentPdf(0,$array);
            $contentpdf->save();
        }
    }
}

if ($_REQUEST["opid"]){
    $orderposition = new Orderposition((int)$_REQUEST["opid"]);
    $article = new Article((int)$orderposition->getObjectid());
    $order = new Order((int)$article->getOrderid());
    foreach (Calculation::getAllCalculations($order) as $allCalculation) {
        if ($allCalculation->getAmount() == $orderposition->getAmount())
            $calc = $allCalculation;
    }
    if ($calc == null)
        die('Keine passende Kalkulation gefunden');
    $contentpdfs = ContentPdf::getAllForOrderposition($orderposition);
    $uploadedpdfs = File::getAllForModuleAndObject(get_class($orderposition),$orderposition->getId());
} else {
    die('Keine Auftragsposition angegeben!');
}

?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-bootstrap.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-halflings.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-filetypes.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-social.css" />
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" href="../../../css/bootstrap.min.css">
<link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/local/jquery.ui.datepicker-<?=$_LANG->getCode()?>.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery.validate.min.js"></script>
<script type="text/javascript" src="../../../jscripts/moment/moment-with-locales.min.js"></script>
<style>
    #gallery { float: left; width: 65%; min-height: 12em; }
    .gallery.custom-state-active { background: #eee; }
    .gallery li { float: left; width: 150px; padding: 0.4em; margin: 0 0.4em 0.4em 0; text-align: center; }
    .gallery li h5 { margin: 0 0 0.4em; cursor: move; }
    .gallery li a { float: right; }
    .gallery li a.ui-icon-zoomin { float: left; }
    .gallery li img { width: 100%; cursor: move; }
</style>


<form action="contentpdf.selector.frame.php" action="post" id="form_pdfcontent" name="form_pdfcontent">
    <input type="hidden" name="opid" value="<?php echo $orderposition->getId();?>">
    <input type="hidden" name="exec" value="save">

    <div class="ui-widget ui-helper-clearfix">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    PDF Inhalte
                    <span class="pull-right">
                        <button type="submit" class="btn btn-success btn-xs">Speichern</button>
                        <button type="button" class="btn btn-success btn-xs" onclick="window.location.href='contentpdf.upload.frame.php?opid=<?php echo $orderposition->getId();?>';">Zum Upload</button>
                    </span>
                </h3>
            </div>
            <div class="panel-body">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Uploads</h3>
                    </div>
                    <div class="panel-body">
                        <ul id="gallery" class="gallery ui-helper-reset ui-helper-clearfix ui-droppable">
                            <?php
                            foreach ($uploadedpdfs as $uploadedpdf) {?>
                                <li class="ui-widget-content ui-corner-tr ui-draggable ui-draggable-handle">
                                    <h5 class="ui-widget-header"><?php echo $uploadedpdf->getFilename();?></h5>
                                    <img src="../../../<?php echo $uploadedpdf->getPreview(150,150);?>" data-fileid="<?php echo $uploadedpdf->getId();?>" width="150" height="150">
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                </div>
                <?php
                foreach ($calc->getDetails() as $detail) {
                    ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title"><?php echo $detail['name'];?></h3>
                        </div>
                        <div class="panel-body">
                            <ul class="gallery ui-helper-reset ui-helper-clearfix">
                                <?php
                                for ($i=0; $i<$detail['umfang'];$i++){
                                    $tmpcontentpdf = ContentPdf::getForOpPartPagenum($orderposition,$detail['paper'],$i+1);
                                    if ($tmpcontentpdf->getId()>0) {
                                        $filename = $tmpcontentpdf->getFile()->getPreview(150, 150);
                                        $heading = 'Seite '.($i+1).'<br>'.$tmpcontentpdf->getFile()->getFilename();
                                        $pagina = $tmpcontentpdf->getPagina();
                                        $fileid = $tmpcontentpdf->getFile()->getId();
                                    } else {
                                        $filename = '';
                                        $heading = 'Seite '.($i+1);
                                        $pagina = $i+1;
                                        $fileid = '';
                                    }
                                    ?>
                                        <li class="drophere ui-widget-content ui-corner-tr">
                                            <h5 class="ui-widget-header"><?php echo $heading;?></h5>
                                            <img src="../../../<?php echo $filename;?>" alt="" width="150" height="150">
                                            <input type="hidden" name="content[<?php echo $detail['paper'];?>][<?php echo $i+1;?>][file]" value="<?php echo $fileid;?>">
                                            <input type="number" name="content[<?php echo $detail['paper'];?>][<?php echo $i+1;?>][pagina]"
                                                   data-part="<?php echo $detail['paper'];?>" data-pagenum="<?php echo $i+1;?>"
                                                   onchange="updatePagina(this);" value="<?php echo $pagina;?>" style="width: 135px;">
                                        </li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</form>

<script>
    function updatePagina(ele){
        var part = $(ele).data('part');
        var number = parseInt($(ele).val());
        ele = $(ele);
        var add = 1;

        $("[data-part="+part+"]").each(function() {
            var element = $(this);
            if (parseInt(element.data('pagenum')) > parseInt(ele.data('pagenum'))){
                element.val(number+add);
                add = add+1;
            }
        });
    }
    $( function() {

        // There's the gallery and the trash
        var $gallery = $( "#gallery" );

        // Let the gallery items be draggable
        $( "li", $gallery ).draggable({
            revert: "invalid", // when not dropped, the item will revert back to its initial position
            containment: "document",
            helper: "clone",
            cursor: "move"
        });

        // Let the content be droppable, accepting the gallery items
        $( ".drophere" ).droppable({
            accept: "#gallery > li",
            classes: {
                "ui-droppable-active": "ui-state-highlight",
                "ui-droppable-hover": "ui-state-hover"
            },
            drop: function( event, ui ) {
                $(this).children('img').prop('src',$(ui.draggable).children('img').prop('src'));
                $(this).children('input').first().prop('value',$(ui.draggable).children('img').data('fileid'));
            }
        });

        // Let the gallery be droppable as well, accepting items from the trash
        $gallery.droppable({
            accept: ".drophere li",
            classes: {
                "ui-droppable-active": "custom-state-active"
            },
            drop: function( event, ui ) {
            }
        });
    } );
</script>