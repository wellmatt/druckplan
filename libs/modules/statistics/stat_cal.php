<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Christian Schroeer <cschroeer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/statistics/statistics.class.php';
require_once 'libs/basic/globalFunctions.php';
require_once 'libs/modules/calculation/calculation.class.php';

function printSubTradegroupsForSelect($parentId, $depth)
{
    $all_subgroups = Tradegroup::getAllTradegroups($parentId);
    foreach ($all_subgroups AS $subgroup) {
        global $x;
        $x++; ?>
        <option value="<?= $subgroup->getId() ?>">
            <? for ($i = 0; $i < $depth + 1; $i++) echo "&emsp;" ?>
            <?= $subgroup->getTitle() ?>
        </option>
        <? printSubTradegroupsForSelect($subgroup->getId(), $depth + 1);
    }
}

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
if ($_REQUEST["stat_status"]) {
    $stat_status = $_REQUEST["stat_status"];
}
if ($_REQUEST["stat_tradegroup"]) {
    $stat_tradegroup = $_REQUEST["stat_tradegroup"];
}
$calstats = Statistics::Calcstat( $start, $end, $businesscontact, $tradegroup, $article, $status);

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


<form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" name="stat_mach" id="stat_mach" enctype="multipart/form-data">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Kalkulationsstatistik</h3>
                </div>
                <div class="panel-body">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">Filter
                                    <span class="pull-right">
                                        <button class="btn btn-xs btn-success" onclick="$('#stat_calc').submit();">
                                            Refresh
                                        </button>
                                    </span>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="form-group">
                                    <label for="" class="col-sm-1 control-label">Von</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" name="stat_from" id="stat_from" value="<?php echo date('d.m.Y',$start);?>" placeholder="">
                                    </div>
                                    <label for="" class="col-sm-1 control-label">Bis</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" name="stat_to" id="stat_to" value="<?php echo date('d.m.Y',$end);?>" placeholder="">
                                    </div>
                                    <label for="" class="col-sm-1 control-label">Kunde</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" name="search_customer"
                                               id="search_customer">
                                        <input type="hidden" name="stat_customer" id="stat_customer">
                                    </div>
                                    <label for="" class="col-sm-1 control-label">Artikel</label>
                                    <div class="col-sm-3">
                                        <input type="text" class="form-control" name="search_article" id="search_article">
                                        <input type="hidden" name="stat_article" id="stat_article">
                                    </div>
                                    <label for="" class="col-sm-1 control-label">Status</label>
                                    <div class="col-sm-3">
                                        <select name="stat_status" id="" class="form-control">
                                            <option value="0">- Alle -</option>
                                            <option value="1">Angelegt</option>
                                            <option value="2">Gesendet u. Bestellt</option>
                                            <option value="3">angenommen</option>
                                            <option value="4">In Produktion</option>
                                            <option value="5">Erledigt</option>
                                        </select>
                                    </div>
                                    <label for="" class="col-sm-1 control-label">Warengruppe</label>
                                    <div class="col-sm-3">
                                        <select name="stat_tradegroup" id="stat_tradegroup" class="form-control">
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
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <table id="machtable" class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th>Kundenname</th>
                                <th>Anz. Aufträge</th>
                                <th>Netto</th>
                                <th>Brutto</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($calstats as $calstat) {
                                $id = $calstat['id'];
                                $bc = new BusinessContact($id);
                                $calstat ['steuer']  += $calstat['wert'];
                                ?>
                                <tr>
                                    <td><?php echo $bc->getNameAsLine();?></td>
                                    <td><?php echo $calstat['anzauft'];?></td>
                                    <td><?php echo printPrice($calstat['wert'],2);?></td>
                                    <td><?php echo printPrice($calstat ['steuer'],2);?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $(function () {
        var table_calc = $('#table_calc').DataTable({
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
        });

        $("#search_customer").autocomplete({
            delay: 0,
            source: 'libs/modules/tickets/ticket.ajax.php?ajax_action=search_customer',
            minLength: 2,
            dataType: "json",
            select: function (event, ui) {
                $('#stat_customer').val(ui.item.value);
            }
        });

        $("#search_article").autocomplete({
            delay: 0,
            source: 'libs/modules/tickets/ticket.ajax.php?ajax_action=search_article',
            minLength: 2,
            dataType: "json",
            select: function (event, ui) {
                $('#stat_article').val(ui.item.value);
            }
        });
        $("#search_tradegroup").autocomplete({
            delay: 0,
            source: 'libs/modules/tickets/ticket.ajax.php?ajax_action=search_article',
            minLength: 2,
            dataType: "json",
            select: function (event, ui) {
                $('#stat_tradegroup').val(ui.item.value);
            }
        });
        $("#search_status").autocomplete({
            delay: 0,
            source: 'libs/modules/tickets/ticket.ajax.php?ajax_action=search_article',
            minLength: 2,
            dataType: "json",
            select: function (event, ui) {
                $('#stat_status').val(ui.item.value);
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












