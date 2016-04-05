<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Christian Schroeer <cschroeer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/statistics/statistics.class.php';


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
if ($_REQUEST["stat_user"]) {
    $stat_user = $_REQUEST["stat_user"];
}
if ($_REQUEST["stat_status"]) {
    $stat_status = $_REQUEST["stat_status"];
}
if ($_REQUEST["stat_tradegroup"]) {
    $stat_tradegroup = $_REQUEST["stat_tradegroup"];
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


<form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" name="stat_colinv" id="stat_colinv"
      enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-1">Von:</div>
        <div class="col-md-3">
            <input type="text" style="width:220px" id="stat_from" name="stat_from"
                   class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
                   value="<? echo date('d.m.Y', $start); ?>"/>
        </div>
        <div class="col-md-1">Bis:</div>
        <div class="col-md-3">
            <input type="text" style="width:220px" id="stat_to" name="stat_to"
                   class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
                   value="<? echo date('d.m.Y', $end); ?>"/>
        </div>
        <div class="col-md-1">Kunde:</div>
        <div class="col-md-3">
            <input type="text" name="search_customer" id="search_customer" style="width:220px">
            <input type="hidden" name="stat_customer" id="stat_customer">
        </div>
    </div>
    <div class="row">
        <div class="col-md-1">Benutzer:</div>
        <div class="col-md-3">
            <input type="text" name="search_user" id="search_user" style="width:220px">
            <input type="hidden" name="stat_user" id="stat_user">
        </div>
        <div class="col-md-1">Status:</div>
        <div class="col-md-3">
            <select name="stat_status" style="width:220px">
                <option value="0">- Alle -</option>
                <option value="1">Angelegt</option>
                <option value="2">Gesendet u. Bestellt</option>
                <option value="3">angenommen</option>
                <option value="4">In Produktion</option>
                <option value="5">Erledigt</option>
            </select>
        </div>
        <div class="col-md-1">Warengruppe:</div>
        <div class="col-md-3">
            <select name="stat_tradegroup" id="stat_tradegroup" style="width:220px;" class="text"
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
        </div>
    </div>
    <div class="row">
        <div class="col-md-1">Artikel:</div>
        <div class="col-md-3">
            <input type="text" name="search_article" id="search_article" style="width:220px">
            <input type="hidden" name="stat_article" id="stat_article">
        </div>
        <div class="col-md-1">
            <button type="submit">Refresh</button>
        </div>
    </div>
</form>


<div class="row">
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">Pro Tag</div>
            <div class="panel-body">
                <table id="table_colinv" class="stripe hover row-border order-column" style="width: auto" width="100%">
                    <thead>
                    <tr>
                        <td style="width: 20px;">ID#</td>
                        <td style="width: 80px;">Nummer</td>
                        <td style="width: 1600px;">Titel</td>
                    </tr>
                    </thead>
                    <?php
                    $days = GetDays(date('d-m-Y', $start), date('d-m-Y', $end));
                    foreach ($days as $day) {
                        $retval = Statistics::ColinvCountDay(strtotime($day), $stat_customer, $stat_user, $stat_article, $stat_tradegroup, $stat_status);
                        if (count($retval) > 0) {
                            echo '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="highlight">' . date('d.m.y', strtotime($day)) . ' // Anzahl: ' . count($retval) . '</td></tr>';
                            foreach ($retval as $item) {
                                echo '<tr>';
                                echo "<td>{$item->getId()}</td>";
                                echo "<td>{$item->getNumber()}</td>";
                                echo "<td>{$item->getTitle()}</td>";
                                echo '</tr>';
                            }
                        }
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">Pro Tag</div>
            <div class="panel-body">
                <table id="table_colinv" class="stripe hover row-border order-column" style="width: auto" width="100%">
                    <thead>
                    <tr>
                        <td style="width: 20px;">ID#</td>
                        <td style="width: 80px;">Nummer</td>
                        <td style="width: 1600px;">Titel</td>
                    </tr>
                    </thead>
                    <?php
                    $days = GetDays(date('d-m-Y', $start), date('d-m-Y', $end));
                    foreach ($days as $day) {
                        $retval = Statistics::ColinvCountDay(strtotime($day), $stat_customer, $stat_user, $stat_article, $stat_tradegroup, $stat_status);
                        if (count($retval) > 0) {
                            echo '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="highlight">' . date('d.m.y', strtotime($day)) . ' // Anzahl: ' . count($retval) . '</td></tr>';
                            foreach ($retval as $item) {
                                echo '<tr>';
                                echo "<td>{$item->getId()}</td>";
                                echo "<td>{$item->getNumber()}</td>";
                                echo "<td>{$item->getTitle()}</td>";
                                echo '</tr>';
                            }
                        }
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="panel panel-default">
            <div class="panel-heading">Pro Tag</div>
            <div class="panel-body">
                <table id="table_colinv" class="stripe hover row-border order-column" style="width: auto" width="100%">
                    <thead>
                    <tr>
                        <td style="width: 20px;">ID#</td>
                        <td style="width: 80px;">Nummer</td>
                        <td style="width: 1600px;">Titel</td>
                    </tr>
                    </thead>
                    <?php
                    $days = GetDays(date('d-m-Y', $start), date('d-m-Y', $end));
                    foreach ($days as $day) {
                        $retval = Statistics::ColinvCountDay(strtotime($day), $stat_customer, $stat_user, $stat_article, $stat_tradegroup, $stat_status);
                        if (count($retval) > 0) {
                            echo '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="highlight">' . date('d.m.y', strtotime($day)) . ' // Anzahl: ' . count($retval) . '</td></tr>';
                            foreach ($retval as $item) {
                                echo '<tr>';
                                echo "<td>{$item->getId()}</td>";
                                echo "<td>{$item->getNumber()}</td>";
                                echo "<td>{$item->getTitle()}</td>";
                                echo '</tr>';
                            }
                        }
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>
</div>


<script>
    $(function () {
        var table_colinv = $('#table_colinv').DataTable({
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
        $("#search_user").autocomplete({
            delay: 0,
            source: 'libs/modules/tickets/ticket.ajax.php?ajax_action=search_user',
            minLength: 2,
            dataType: "json",
            select: function (event, ui) {
                $('#stat_user').val(ui.item.value);
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

