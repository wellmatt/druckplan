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

$monate = array(1=>"Januar",
    2=>"Februar",
    3=>"M&auml;rz",
    4=>"April",
    5=>"Mai",
    6=>"Juni",
    7=>"Juli",
    8=>"August",
    9=>"September",
    10=>"Oktober",
    11=>"November",
    12=>"Dezember");



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


<form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" name="stat_colinv" id="stat_colinv" enctype="multipart/form-data">
    <div class="panel panel-default">
          <div class="panel-heading">
                <h3 class="panel-title">Auftragsstatistik</h3>
          </div>
          <div class="panel-body">
                <div class="panel panel-default">
                      <div class="panel-heading">
                            <h3 class="panel-title">Filter</h3>
                      </div>
                      <div class="panel-body">
                          <div class="row">
                              <div class="form-group">
                                  <label for="" class="col-sm-1 control-label">Von</label>
                                  <div class="col-sm-3">
                                      <input type="text" id="stat_from" name="stat_from"
                                             class="form-control text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
                                             value="<? echo date('d.m.Y', $start); ?>"/>
                                  </div>
                                  <label for="" class="col-sm-1 control-label">Bis</label>
                                  <div class="col-sm-3">
                                      <input type="text" id="stat_to" name="stat_to"
                                             class="form-control text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
                                             value="<? echo date('d.m.Y', $end); ?>"/>
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
                              </div>
                          </div>
                          <div class="row">
                              <div class="form-group">
                                  <label for="" class="col-sm-1 control-label">Kunde</label>
                                  <div class="col-sm-3">
                                      <input type="text" class="form-control" name="search_customer"
                                             id="search_customer">
                                      <input type="hidden" name="stat_customer" id="stat_customer">
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
                                  <label for="" class="col-sm-1 control-label">Artikel</label>
                                  <div class="col-sm-3">
                                      <input type="text" class="form-control" name="search_article" id="search_article">
                                      <input type="hidden" name="stat_article" id="stat_article">
                                  </div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="form-group">
                                  <label for="" class="col-sm-1 control-label">Benutzer</label>
                                  <div class="col-sm-3">
                                      <input type="text" class="form-control" name="search_user" id="search_user">
                                      <input type="hidden" name="stat_user" id="stat_user">
                                  </div>
                                  <div class="col-md-offset-6 col-md-2">
                                      <button class="btn btn-sm btn-success" type="submit">Refresh</button>
                                  </div>
                              </div>
                          </div>
                          <br>

                          <div class="row">
                              <div class="col-md-6">
                                  <div class="panel panel-default">
                                      <div class="panel-heading"><div align="center">Pro Tag</div></div>
                                      <div class="panel-body">
                                          <table id="table_colinv_day" class="stripe hover row-border order-column" style="width: auto" width="100%">
                                              <thead>
                                              <tr>
                                                  <td style="width: 10px;"><u><h5>ID</h5></u></td>
                                                  <td style="width: 60px;"><u><h5>Nummer</h5></u></td>
                                                  <td style="width: 200px;"><u><h5>Titel</h5></u></td>
                                                  <td style="width: 80px;"><u><h5>Netto</h5></u></td>
                                                  <td style="width: 80px;"><u><h5>Brutto</h5></u></td>

                                              </tr>
                                              </thead>

                                              <?php
                                              $days = GetDays(date('d-m-Y', $start), date('d-m-Y', $end));
                                              foreach ($days as $day) {
                                                  $retval = Statistics::ColinvCountDay(strtotime($day), $stat_customer, $stat_user, $stat_article, $stat_tradegroup, $stat_status);
                                                  if (count($retval) > 0) {
                                                      $nettotal = 0;
                                                      $grosstotal = 0;
                                                      echo '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="highlight">' . date('d.m.y', strtotime($day)) . ' // Anzahl: ' . count($retval) . '</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
                                                      foreach ($retval as $item) {
                                                          echo '<tr>';
                                                          echo "<td>{$item->getId()}</td>";
                                                          echo "<td>{$item->getNumber()}</td>";
                                                          echo "<td>{$item->getTitle()}</td>";
                                                          echo "<td>" .printPrice($item->getTotalNetSum(),2). "</td>";
                                                          echo "<td>" .printPrice($item->getTotalGrossSum(),2). "</td>";
                                                          echo '</tr>';
                                                          $nettotal += $item->getTotalNetSum();
                                                          $grosstotal += $item->getTotalGrossSum();
                                                          $nettotalge += $item->getTotalNetSum();
                                                          $grosstotalge += $item->getTotalGrossSum();

                                                      }
                                                      echo '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="highlight">Summe pro Tag: </td><td>' .printPrice($nettotal,2). '</td><td>' .printPrice($grosstotal,2). '</td></tr>';

                                                  }
                                              }
                                              echo '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="highlight"><b>Gesamt Summe:</b></td><td>' .printPrice($nettotalge,2). '</td><td>' .printPrice($grosstotalge,2). '</td></tr>';
                                              ?>
                                          </table>
                                      </div>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="panel panel-default">
                                      <div class="panel-heading"><div align="center">Monat</div></div>
                                      <div class="panel-body">
                                          <table id="table_colinv_month" class="stripe hover row-border order-column" style="width: auto" width="100%">
                                              <thead>
                                              <tr>
                                                  <td style="width: 10px;"><u><h5>ID</h5></u></td>
                                                  <td style="width: 60px;"><u><h5>Nummer</h5></u></td>
                                                  <td style="width: 200px;"><u><h5>Titel</h5></u></td>
                                                  <td style="width: 80px;"><u><h5>Netto</h5></u></td>
                                                  <td style="width: 80px;"><u><h5>Brutto</h5></u></td>

                                              </tr>
                                              </thead>

                                              <?php
                                              $months = GetMonths(date('Y-m-d', $start), date('Y-m-d', $end));
                                              foreach ($months as $month) {
                                                  $retval = Statistics::ColinvCountMonth(strtotime($month), $stat_customer, $stat_user, $stat_article, $stat_tradegroup, $stat_status);
                                                  if (count($retval) > 0) {
                                                      $nettotal = 0;
                                                      $grosstotal = 0;
                                                      echo '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="highlight">' . $monate[date('n', strtotime($month))] . ' // ' .  date('Y', strtotime($month)) . ' // Anzahl: ' . count($retval) . '</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
                                                      foreach ($retval as $item) {
                                                          echo '<tr>';
                                                          echo "<td>{$item->getId()}</td>";
                                                          echo "<td>{$item->getNumber()}</td>";
                                                          echo "<td>{$item->getTitle()}</td>";
                                                          echo "<td>" .printPrice($item->getTotalNetSum(),2). "</td>";
                                                          echo "<td>" .printPrice($item->getTotalGrossSum(),2). "</td>";
                                                          echo '</tr>';
                                                          $nettotal += $item->getTotalNetSum();
                                                          $grosstotal += $item->getTotalGrossSum();
                                                          $nettotalge1 += $item->getTotalNetSum();
                                                          $grosstotalge1 += $item->getTotalGrossSum();

                                                      }
                                                      echo '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="highlight">Summe pro Monat: </td><td>' .printPrice($nettotal,2). '</td><td>' .printPrice($grosstotal,2). '</td></tr>';

                                                  }
                                              }
                                              echo '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="highlight"><b>Gesamt Summe:</b></td><td>' .printPrice($nettotalge1,2). '</td><td>' .printPrice($grosstotalge1,2). '</td></tr>';
                                              ?>
                                          </table>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="row">
                              <div class="col-md-6">
                                  <div class="panel panel-default">
                                      <div class="panel-heading"><div align="center">Jahr</div></div>
                                      <div class="panel-body">
                                          <table id="table_colinv_year" class="stripe hover row-border order-column" style="width: auto" width="100%">
                                              <thead>
                                              <tr>
                                                  <td style="width: 10px;"><u><h5>ID</h5></u></td>
                                                  <td style="width: 60px;"><u><h5>Nummer</h5></u></td>
                                                  <td style="width: 200px;"><u><h5>Titel</h5></u></td>
                                                  <td style="width: 80px;"><u><h5>Netto</h5></u></td>
                                                  <td style="width: 80px;"><u><h5>Brutto</h5></u></td>

                                              </tr>
                                              </thead>

                                              <?php
                                              $years = GetYears(date('Y-m-d', $start), date('Y-m-d', $end));
                                              foreach ($years as $year) {
                                                  $retval = Statistics::ColinvCountYear(strtotime($year), $stat_customer, $stat_user, $stat_article, $stat_tradegroup, $stat_status);
                                                  if (count($retval) > 0) {
                                                      $nettotal = 0;
                                                      $grosstotal = 0;
                                                      echo '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="highlight">' . date('Y', strtotime($year)) . ' // Anzahl: ' . count($retval) . '</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
                                                      foreach ($retval as $item) {
                                                          echo '<tr>';
                                                          echo "<td>{$item->getId()}</td>";
                                                          echo "<td>{$item->getNumber()}</td>";
                                                          echo "<td>{$item->getTitle()}</td>";
                                                          echo "<td>" .printPrice($item->getTotalNetSum(),2). "</td>";
                                                          echo "<td>" .printPrice($item->getTotalGrossSum(),2). "</td>";
                                                          echo '</tr>';

                                                          $nettotal += $item->getTotalNetSum();
                                                          $grosstotal += $item->getTotalGrossSum();
                                                          $nettotalge2 += $item->getTotalNetSum();
                                                          $grosstotalge2 += $item->getTotalGrossSum();



                                                      }
                                                      echo '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="highlight">Summe pro Jahr: </td><td>' .printPrice($nettotal,2). '</td><td>' .printPrice($grosstotal,2). '</td></tr>';

                                                  }

                                              }
                                              echo '<tr><td>&nbsp;</td><td>&nbsp;</td><td class="highlight"><b>Gesamt Summe:</b></td><td>' .printPrice($nettotalge2,2). '</td><td>' .printPrice($grosstotalge2,2). '</td></tr>';




                                              ?>
                                          </table>
                                      </div>
                                  </div>
                              </div>
                          </div>
                    </div>
                </div>
          </div>
    </div>
</form>




<script>
    $(function () {
        var table_colinv_day = $('#table_colinv_day').DataTable({
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

        var table_colinv_month = $('#table_colinv_month').DataTable({
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

        var table_colinv_year = $('#table_colinv_year').DataTable({
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

