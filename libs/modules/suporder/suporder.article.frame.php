<? /**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
chdir("../../../");
require_once("config.php");
// error_reporting(-1);
// ini_set('display_errors', 1);
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
require_once 'suporder.class.php';

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


if ($_USER == false){
    error_log("Login failed (basic-importer.php)");
    die("Login failed");
}
$perf = new Perferences();

function printSubTradegroupsForSelect($parentId, $depth){
    $all_subgroups = Tradegroup::getAllTradegroups($parentId);
    foreach ($all_subgroups AS $subgroup)
    {
        global $x;
        $x++; ?>
        <option value="<?=$subgroup->getId()?>">
            <?for ($i=0; $i<$depth+1;$i++) echo "&emsp;"?>
            <?= $subgroup->getTitle()?>
        </option>
        <? printSubTradegroupsForSelect($subgroup->getId(), $depth+1);
    }
}

if (!$_REQUEST["soid"])
    die('Keine Bestellung gefunden!');
else
    $suporder = new SupOrder($_REQUEST["soid"]);

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>

<link rel="stylesheet" type="text/css" href="../../../css/main.css"/>
<link rel="stylesheet" href="../../../css/bootstrap.min.css">
<link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet"/>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript"
        src="../../../jscripts/jquery/local/jquery.ui.datepicker-<?= $_LANG->getCode() ?>.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery.validate.min.js"></script>
<script type="text/javascript" src="../../../jscripts/moment/moment-with-locales.min.js"></script>


<!-- Lightbox -->
<link rel="stylesheet" href="../../../jscripts/lightbox/lightbox.css" type="text/css" media="screen"/>
<script type="text/javascript" src="../../../jscripts/lightbox/lightbox.js"></script>
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="../../../css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="../../../css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="../../../css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/dataTables.tableTools.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/tagit/tag-it.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../../jscripts/tagit/jquery.tagit.css" media="screen"/>
<script type="text/javascript">
    $(document).ready(function () {
        var art_table = $('#art_table').DataTable({
            // "scrollY": "600px",
            "processing": true,
            "bServerSide": true,
            "sAjaxSource": "../../../libs/modules/suporder/suporder.article.dt.ajax.php",
            "paging": true,
            "stateSave": <?php if ($perf->getDt_state_save()) {
            echo "true";
        } else {
            echo "false";
        };?>,
            "pageLength": <?php echo $perf->getDt_show_default();?>,
            "dom": 'T<"clear">flrtip',
            "tableTools": {
                "sSwfPath": "../../../jscripts/datatable/copy_csv_xls_pdf.swf",
                "aButtons": [
                    "copy",
                    "csv",
                    "xls",
                    {
                        "sExtends": "pdf",
                        "sPdfOrientation": "landscape",
                        "sPdfMessage": "Contilas - Articles"
                    },
                    "print"
                ]
            },
            "lengthMenu": [[10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"]],
            "columns": [
                null,
                {"sortable": false, "visible": false},
                null,
                null,
                {"sortable": false},
                null,
                {"sortable": false, "visible": false},
                {"sortable": false, "visible": false}
            ],
            "fnServerData": function (sSource, aoData, fnCallback) {
                var tags = document.getElementById('ajax_tags').value;
                var tg = document.getElementById('ajax_tradegroup').value;
                var bc = <?php echo $_REQUEST["supid"];?>;
                aoData.push({"name": "search_tags", "value": tags});
                aoData.push({"name": "tradegroup", "value": tg});
                aoData.push({"name": "bc", "value": bc});
                $.getJSON(sSource, aoData, function (json) {
                    fnCallback(json)
                });
            },
            "language": {
                "emptyTable": "Keine Daten vorhanden",
                "info": "Zeige _START_ bis _END_ von _TOTAL_ Eintr&auml;gen",
                "infoEmpty": "Keine Seiten vorhanden",
                "infoFiltered": "(gefiltert von _MAX_ gesamten Eintr&auml;gen)",
                "infoPostFix": "",
                "thousands": ".",
                "lengthMenu": "Zeige _MENU_ Eintr&auml;ge",
                "loadingRecords": "Lade...",
                "processing": "Verarbeite...",
                "search": "Suche:",
                "zeroRecords": "Keine passenden Eintr&auml;ge gefunden",
                "paginate": {
                    "first": "Erste",
                    "last": "Letzte",
                    "next": "N&auml;chste",
                    "previous": "Vorherige"
                },
                "aria": {
                    "sortAscending": ": aktivieren um aufsteigend zu sortieren",
                    "sortDescending": ": aktivieren um absteigend zu sortieren"
                }
            }
        });

        $("#art_table tbody td").live('click', function () {
            var aPos = $('#art_table').dataTable().fnGetPosition(this);
            var aData = $('#art_table').dataTable().fnGetData(aPos[0]);
            window.location.href = "suporder.position.frame.php?aid="+aData[0]+"&soid=<?php echo $suporder->getId();?>";
//                parent.addArticle(aData[0]);
//                parent.$.fancybox.close();
        });
    });
</script>

<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery("#tags").tagit({
            singleField: true,
            singleFieldNode: $('#tags'),
            singleFieldDelimiter: ";",
            allowSpaces: true,
            minLength: 2,
            removeConfirmation: true,
            tagSource: function (request, response) {
                $.ajax({
                    url: "../../../libs/modules/article/article.ajax.php?ajax_action=search_tags",
                    data: {term: request.term},
                    dataType: "json",
                    success: function (data) {
                        response($.map(data, function (item) {
                            return {
                                label: item.label,
                                value: item.value
                            }
                        }));
                    }
                });
            },
            afterTagAdded: function (event, ui) {
                $('#ajax_tags').val($("#tags").tagit("assignedTags"));
                $('#art_table').dataTable().fnDraw();
            },
            afterTagRemoved: function (event, ui) {
                $('#ajax_tags').val($("#tags").tagit("assignedTags"));
                $('#art_table').dataTable().fnDraw();
            }
        });
    });
</script>

<table width="100%">
    <tr>
        <td width="200" class="content_header">
            <img src="../../../images/icons/ui-radio-button-uncheck.png"><span
                style="font-size: 13px"> <?= $_LANG->get('Artikelauswahl') ?> </span></br>
        </td>
        <td valign="center" align="right">
        </td>
    </tr>
</table>

<div class="box1">

    <div class="box2">
        <table>
            <tr align="left">
                <td valing="top">Tags:&nbsp;&nbsp;</td>
                <td valign="top">
                    <input type="hidden" id="ajax_tags" name="ajax_tags"/>
                    <input name="tags" id="tags" style="width:200px;" class="text" onfocus="markfield(this,0)"
                           onblur="markfield(this,1)">
                </td>
            </tr>
            <tr align="left">
                <td valing="top">Warengruppe:&nbsp;&nbsp;</td>
                <td valign="top">
                    <input type="hidden" id="ajax_tradegroup" name="ajax_tradegroup" value="0"/>
                    <select name="tradegroup" id="tradegroup" style="width:200px;" class="text"
                            onchange="$('#ajax_tradegroup').val($('#tradegroup').val());$('#art_table').dataTable().fnDraw();"
                            onfocus="markfield(this,0)" onblur="markfield(this,1)">
                        <option value="0">- Alle -</option>
                        <?php
                        $all_tradegroups = Tradegroup::getAllTradegroups();
                        foreach ($all_tradegroups as $tg) {
                            ?>
                            <option value="<?= $tg->getId() ?>">
                                <?= $tg->getTitle() ?></option>
                            <? printSubTradegroupsForSelect($tg->getId(), 0);
                        }
                        ?>
                    </select>
                </td>
            </tr>
        </table>
    </div>
    </br>
    <table id="art_table" width="100%" cellpadding="0" cellspacing="0" class="stripe hover row-border order-column">
        <thead>
        <tr>
            <th width="15"><?= $_LANG->get('ID') ?></th>
            <th style="display: hidden;" width="105"><?= $_LANG->get('Bild') ?></th>
            <th><?= $_LANG->get('Titel') ?></th>
            <th width="80"><?= $_LANG->get('Art.-Nr.') ?></th>
            <th width="80"><?= $_LANG->get('Tags') ?></th>
            <th width="160"><?= $_LANG->get('Warengruppe') ?></th>
            <th style="display: hidden;" width="100"><?= $_LANG->get('Shop-Freigabe') ?></th>
            <th style="display: hidden;" width="120"><?= $_LANG->get('Optionen') ?></th>
        </tr>
        </thead>
    </table>
</div>