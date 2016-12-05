<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Matthias Welland <mwelland@ipactor.de>, 2016
 *
 */

require_once 'libs/modules/statistics/statistics.class.php';
require_once 'libs/modules/article/article.class.php';

if ($_REQUEST["stat_from"]) {
    $start = strtotime($_REQUEST["stat_from"]);
}
if ($_REQUEST["stat_to"]) {
    $end = strtotime($_REQUEST["stat_to"]);
}
if ($_REQUEST["stat_customer"]) {
    $stat_customer = $_REQUEST["stat_customer"];
}
if ($_REQUEST["stat_status"]) {
    $stat_status = $_REQUEST["stat_status"];
}
if ($_REQUEST["stat_article"]) {
    $stat_article = $_REQUEST["stat_article"];
}
$articels = new Article($_REQUEST["id"]);
?>


<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Artikelumsatzstatistik</h3>
    </div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Filter</h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Datum vom</label>
                        <div class="col-sm-4">
                            <input type="text" id="stat_from" name="stat_from"
                                   class="form-control text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
                                   value="<? echo date('d.m.Y', $start); ?>"/>
                        </div>
                    <label for="" class="col-sm-1 control-label">bis</label>
                    <div class="col-sm-4">
                        <input type="text" id="stat_to" name="stat_to"
                               class="form-control text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
                               value="<? echo date('d.m.Y', $end); ?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Kunde</label>
                    <div class="col-sm-9">
                        <select name="" id="" class="form-control">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Auftragsstatus</label>
                    <div class="col-sm-9">
                        <select  name="stat_status" id=""  class="form-control">
                            <option <?php if ((int)$_REQUEST["stat_status"]==0) echo ' selected ';?> value="0">- Alle -</option>
                            <option <?php if ((int)$_REQUEST["stat_status"]==1) echo ' selected ';?> value="1">Angelegt</option>
                            <option <?php if ((int)$_REQUEST["stat_status"]==2) echo ' selected ';?> value="2">Gesendet u. Bestellt</option>
                            <option <?php if ((int)$_REQUEST["stat_status"]==3) echo ' selected ';?> value="3">angenommen</option>
                            <option <?php if ((int)$_REQUEST["stat_status"]==4) echo ' selected ';?> value="4">In Produktion</option>
                            <option <?php if ((int)$_REQUEST["stat_status"]==5) echo ' selected ';?> value="5">Erledigt</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Jahr</label>
                    <div class="col-sm-9">
                        <select name="" id="" class="form-control">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Monat</label>
                    <div class="col-sm-9">
                        <select name="" id="" class="form-control">
                            <option value=""></option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Suche</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="" id="" placeholder="">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table id="art_table" class="table table-hover">
                <thead>
                <tr>

                    <th>Artikel-ID</th>
                    <th>Artikelname</th>
                    <th>Beschreibung</th>
                    <th>Einkaufspreis</th>
                    <th>Verkaufspreis</th>
                    <th>Marge in %</th>
                    <th>Umsatz netto</th>
                    <th>Umsatz brutto</th>
                    <th>Ertrag in € (netto)</th>
                    <th>Ertrag in %</th>
                </tr>
                </thead>
                <?php $articel = Article::getAllArticle($articels, Article::ORDER_TITLE);
                foreach ($articel as $ar) {

                        echo '<tr>';
                        echo "<td>{$ar->getID()}</td>";
                        echo "<td>{$ar->getDesc()}</td>";
                        echo "<td>{$ar->getId()}</td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td>}</td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo "<td></td>";
                        echo '</tr>';

                    } ?>
            </table>
        </div>
    </div>
</div>
<script>
    $(function () {
        var art_table = $('#art_table').DataTable({
            "dom": 'rti',
            "ordering": false,
            "order": [],
            "paging": false,
            "tableTools": {
                "sSwfPath": "jscripts/datatable/copy_csv_xls_pdf.swf",
                "aButtons": [
                    "copy",
                    "csv",
                    "xls",
                    {
                        "sExtends": "pdf",
                        "sPdfOrientation": "landscape",
                        "sPdfMessage": "Contilas - Businesscontacts"
                    },
                    "print"
                ]
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
                "aria": {
                    "sortAscending": ": aktivieren um aufsteigend zu sortieren",
                    "sortDescending": ": aktivieren um absteigend zu sortieren"
                }
            }
        } );
        $("#search_user").autocomplete({
            delay: 0,
            source: 'libs/modules/tickets/ticket.ajax.php?ajax_action=search_user',
            minLength: 2,
            dataType: "json",
            select: function (event, ui) {
                $('#stat_user').val(ui.item.value);
                $('#search_user').val(ui.item.label);
                return false;
            }
        });
        $("#search_customer").autocomplete({
            delay: 0,
            source: 'libs/modules/tickets/ticket.ajax.php?ajax_action=search_customer',
            minLength: 2,
            dataType: "json",
            select: function (event, ui) {
                $('#stat_customer').val(ui.item.value);
                $('#search_customer').val(ui.item.label);
                return false;
            }
        });

        $('#stat_from').datetimepicker({
            lang: 'de',
            i18n: {
                de: {
                    months: [
                        'Januar', 'Februar', 'März', 'April',
                        'Mai', 'Juni', 'Juli', 'August',
                        'September', 'Oktober', 'November', 'Dezember',
                    ],
                    dayOfWeek: [
                        "So.", "Mo", "Di", "Mi",
                        "Do", "Fr", "Sa.",
                    ]
                }
            },
            timepicker: false,
            format: 'd.m.Y'
        });
        $('#stat_to').datetimepicker({
            lang: 'de',
            i18n: {
                de: {
                    months: [
                        'Januar', 'Februar', 'März', 'April',
                        'Mai', 'Juni', 'Juli', 'August',
                        'September', 'Oktober', 'November', 'Dezember',
                    ],
                    dayOfWeek: [
                        "So.", "Mo", "Di", "Mi",
                        "Do", "Fr", "Sa.",
                    ]
                }
            },
            timepicker: false,
            format: 'd.m.Y'
        });
    });
</script>
