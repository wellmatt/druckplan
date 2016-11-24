<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Matthias Welland <mwelland@ipactor.de>, 2016
 *
 */

require_once 'libs/modules/statistics/statistics.class.php';
require_once 'libs/basic/globalFunctions.php';

$start = mktime(0, 0, 0, date('m', time()), 1, date('Y', time()));
$end = mktime(0, 0, 0, date('m', time()), cal_days_in_month(CAL_GREGORIAN, date('m', time()), date('Y', time())), date('Y', time()));

if ($_REQUEST["stat_from"]) {
    $start = strtotime($_REQUEST["stat_from"]);
}
if ($_REQUEST["stat_to"]) {
    $end = strtotime($_REQUEST["stat_to"]);
}
if ($_REQUEST["stat_customer"]) {
    $stat_customer = $_REQUEST["stat_customer"];
}
if ($_REQUEST["stat_user"]) {
    $stat_user = $_REQUEST["stat_user"];
}
if ($_REQUEST["stat_status"]) {
    $stat_status = $_REQUEST["stat_status"];
}
if ($_REQUEST["stat_tradegroup"]) {
    $stat_tradegroup = $_REQUEST["stat_tradegroup"];
}
if ($_REQUEST["stat_article"]) {
    $stat_article = $_REQUEST["stat_article"];
}

?>
<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.tableTools.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/date-uk.js"></script>


<form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" name="stat_colinvcust" id="stat_colinvcust" class="form-horizontal" enctype="multipart/form-data">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Umsatzstatistik</h3>
        </div>
        <div class="panel-body">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Filter
                          <span class="pull-right">
                                <button class="btn btn-xs btn-success" onclick="$('#stat_colinvcust').submit();">
                                    Filter anwenden
                                </button>
                          </span>
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="" class="col-sm-1 control-label">Datum</label>
                        <label for="" class="col-sm-1 control-label">vom</label>
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
                            <input type="text" class="form-control" value="<?php echo $_REQUEST["search_customer"];?>" name="search_customer"
                                   id="search_customer">
                            <input type="hidden" value="<?php echo $_REQUEST["search_customer"];?>" name="stat_customer" id="stat_customer">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Benutzer</label>
                        <div class="col-sm-9">
                            <input type="text" value="<?php echo $_REQUEST["search_user"];?>" class="form-control" name="search_user" id="search_user">
                            <input type="hidden" name="stat_user" value="<?php echo $_REQUEST["search_user"];?>" id="stat_user">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Auftragsstatus</label>
                        <div class="col-sm-9">
                            <select  name="stat_status" id="stat_status"  class="form-control">
                                <option <?php if ((int)$_REQUEST["stat_status"]==0) echo ' selected ';?> value="0">- Alle -</option>
                                <option <?php if ((int)$_REQUEST["stat_status"]==1) echo ' selected ';?> value="1">Angelegt</option>
                                <option <?php if ((int)$_REQUEST["stat_status"]==2) echo ' selected ';?> value="2">Gesendet u. Bestellt</option>
                                <option <?php if ((int)$_REQUEST["stat_status"]==3) echo ' selected ';?> value="3">angenommen</option>
                                <option <?php if ((int)$_REQUEST["stat_status"]==4) echo ' selected ';?> value="4">In Produktion</option>
                                <option <?php if ((int)$_REQUEST["stat_status"]==5) echo ' selected ';?> value="5">Versandbereit</option>
                                <option <?php if ((int)$_REQUEST["stat_status"]==6) echo ' selected ';?> value="5">Ware versand</option>
                                <option <?php if ((int)$_REQUEST["stat_status"]==7) echo ' selected ';?> value="5">Erledigt</option>
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
                <table id="table_colinvcust"  class="table table-hover">
                    <thead>
                    <tr>
                        <th>Kundenname</th>
                        <th>Vorgangsnummer</th>
                        <th>Vorgangstitel</th>
                        <th>Benutzer</th>
                        <th>Auftr. Status</th>
                        <th>Umsatz netto</th>
                        <th>Umsatz brutto</th>
                        <th>Ertrag in € (netto)</th>
                        <th>Ertrag in %</th>
                    </tr>
                    </thead>
                    <?php
                    $days = GetDays(date('d-m-Y', $start), date('d-m-Y', $end));
                    foreach ($days as $day) {
                    $retval = Statistics::ColinvCust(strtotime($day), $stat_customer, $stat_user, $stat_article, $stat_tradegroup, $stat_status);
                        if (count($retval) > 0) {
                            foreach ($retval as $item) {
                                echo '<tr>';
                                echo "<td>{$item->getCustomer()->getNameAsLine()}</td>";
                                echo "<td>{$item->getNumber()}</td>";
                                echo "<td>{$item->getTitle()}</td>";
                                echo "<td>{$stat_user}</td>";
                                echo "<td>{$item->getStatus()}</td>";
                                echo "<td>" .printPrice($item->getTotalNetSum(),2). "</td>";
                                echo "<td>" .printPrice($item->getTotalGrossSum(),2). "</td>";
                                echo "<td></td>";
                                echo "<td></td>";
                                echo '</tr>';
                                $nettotal += $item->getTotalNetSum();
                                $grosstotal += $item->getTotalGrossSum();
                            }
                        }
                    }
                    ?>
                    <tfoot>
                    <tr>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th>Summe Umsatz (netto):</th>
                        <th>Summe Umsatz (brutto)</th>
                        <th>Summe Ertrag in € (netto)</th>
                        <th>Summe Ertrag in %</th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</form>
<script>
    $(function () {
        var table_colinvcust = $('#table_colinvcust').DataTable({
            "dom": 'rti',
            "ordering": false,
            "order": [],
            "paging": false,
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