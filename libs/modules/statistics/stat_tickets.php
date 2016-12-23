<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

$start = mktime(0,0,0,date('m',time()),date('d',time()),date('Y',time()));
$end = mktime(0,0,0,date('m',time()),cal_days_in_month(CAL_GREGORIAN,date('m',time()),date('Y',time())),date('Y',time()));

if ($_REQUEST["stat_from"]){
    $start = strtotime($_REQUEST["stat_from"]);
}
if ($_REQUEST["stat_to"]){
    $end = strtotime($_REQUEST["stat_to"]);
}

$states = Ticket::StatisticsTicketStates($start,$end);
$categories = Ticket::StatisticsTicketCategories($start,$end);
$workload = Ticket::StatisticsTicketWorkload($start,$end);

if ($_REQUEST["wrkl_user"])
    $workload_user = Ticket::StatisticsTicketWorkloadUser(new User($_REQUEST["wrkl_user"]),$start,$end);
else
    $workload_user = Ticket::StatisticsTicketWorkloadUser($_USER,$start,$end);

?>

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.tableTools.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/date-uk.js"></script>

<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>

<script src="thirdparty/ckeditor/ckeditor.js"></script>
<script src="jscripts/jvalidation/dist/jquery.validate.min.js"></script>
<script src="jscripts/jvalidation/dist/localization/messages_de.min.js"></script>
<script src="jscripts/moment/moment-with-locales.min.js"></script>
<script src="jscripts/flot/jquery.flot.js"></script>
<script src="jscripts/flot/jquery.flot.pie.js"></script>
<style>
    .demo-placeholder {
        width: 100%;
        height: 100%;
        font-size: 14px;
        line-height: 1.2em;
    }
</style>

<div class="row">
    <div class="col-md-12"><h4>Ticket Statistiken</h4></div>
</div>


<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="stat_ticket" id="stat_ticket" enctype="multipart/form-data">
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-2">
            Von: <input type="text" style="width:160px" id="stat_from" name="stat_from" class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency" value="<?echo date('d.m.Y', $start);?>"/>
        </div>
        <div class="col-md-2">
            Bis: <input type="text" style="width:160px" id="stat_to" name="stat_to" class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency" value="<?echo date('d.m.Y', $end);?>"/>
        </div>
        <div class="col-md-1"><button class="btn btn-xs btn-success" type="submit">Refresh</button></div>
        <div class="col-md-1">
        <button class="btn btn-xs btn-success" value=" drucken " onClick="javascript:window.print();">
            Drucken
        </button></div>
    </div>
    </br>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading">Arbeitszeiten</div>
                <div class="panel-body">
                    Mitarbeiter wählen: <select name="wrkl_user" id="wrkl_user" onchange="$('#stat_ticket').submit();" style="width:160px" required>
                    <?php
                    $all_user = User::getAllUser(User::ORDER_NAME);

                    foreach ($all_user as $user){
                        if ($_REQUEST["wrkl_user"] == $user->getId()) {
                            echo '<option value="' . $user->getId() . '" selected>' . $user->getNameAsLine() . '</option>';
                        } elseif ($user->getId() == $_USER->getId() && (int)$_REQUEST["wrkl_user"] == 0){
                            echo '<option value="' . $user->getId() . '" selected>' . $user->getNameAsLine() . '</option>';
                        } else {
                            echo '<option value="'.$user->getId().'">'.$user->getNameAsLine().'</option>';
                        }
                    }
                    ?>
                    </select>
                    <table id="table_workload" class="stripe hover row-border order-column">
                        <thead>
                            <tr>
                                <td style="width: 10px;">ID#</td>
                                <td style="width: 60px;">TK#</td>
                                <td style="width: 100px;">Kunde</td>
                                <td style="width: 200px;">Titel</td>
                                <td style="width: 50px;">Soll</td>
                                <td style="width: 50px;">Ist</td>
                                <td style="width: 60px;">Fällig</td>
                            </tr>
                        </thead>


                        <?php
                        $planned = 0;
                        $currend = 0;
                        foreach ($workload_user as $item) {
                            echo '<tr>';
                            echo "<td>{$item['id']}</td>";
                            echo "<td>{$item['number']}</td>";
                            echo "<td>{$item['bc']}</td>";
                            echo "<td>{$item['title']}</td>";
                            echo "<td>".printPrice($item['planned_time'],2)."</td>";
                            echo "<td>".printPrice($item['curr_time'],2)."</td>";
                            echo "<td>".date('d.m.y H:i',$item["duedate"])."</td>";
                            echo '</tr>';

                            $planned += $item['planned_time'];
                            $currend += $item['curr_time'];
                        }
                        ?>
                    </table>
                    <br/>

                    <div class="form-group">

                        <label for="" class="col-sm-3 control-label">Arbeitszeiten ausgewählter Mitarbeiter</label>
                        <label for="" class="col-sm-1 control-label">Soll</label>
                        <div class="col-sm-3 form-text">
                            <?php echo printPrice($planned,2);?> Stunden
                        </div>
                        <label for="" class="col-sm-1 control-label">Ist</label>
                        <div class="col-sm-3 form-text">
                            <?php echo printPrice($currend,2);?> Stunden
                        </div>

                    </div>

                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Arbeitszeiten aller Mitarbeiter im ausgewählten Zeitraum</div>
                <div class="panel-body">
                    <table id="table_workload_summary" class="stripe hover row-border order-column">
                        <thead>
                        <tr>
                            <td style="width: 160px;">Mitarbeiter</td>
                            <td style="width: 100px;">Ist-Arbeitszeit</td>
                        </tr>
                        </thead>
                        <?php
                        $data_time =0;
                        foreach ($workload as $item) {
                            echo '<tr>';
                            echo "<td>{$item['label']}</td>";
                            echo "<td>".printPrice($item['data'],2)."</td>";
                            echo '</tr>';

                            $data_time += $item['data'];
                        }
                        ?>
                    </table>
<br/>
                    <br/>
                        <div class="form-group">

                            <label for="" class="col-sm-3 control-label">Arbeitszeiten </label>
                            <label for="" class="col-sm-1 control-label">Ist</label>
                            <div class="col-sm-3 form-text">
                                <?php echo printPrice($data_time,2);?> Stunden
                            </div>


                        </div>


                </div>
            </div>
            </br>
            <div class="panel panel-default">
                <div class="panel-heading">Stati</div>
                <div class="panel-body">
                    <div id="stati_donut" style="width: 600px; height: 600px;"></div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading">Kategorien</div>
                <div class="panel-body">
                    <div id="category_donut" style="width: 600px; height: 600px;"></div>
                </div>
            </div>
        </div>
    </div>
</form>

<script language="JavaScript">
    $(function() {
        var table_workload = $('#table_workload').DataTable({
            "aaSorting": [[1, "desc"]],
            "dom": 'rti',
            "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                if ( aData[5] > aData[4] )
                {
                    $(nRow).addClass('highlight');
                }
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

        var DELAY = 500, clicks = 0, timer = null;
        $('#table_workload tbody td').live('click', function(e){
                clicks++;
                var aPos = $('#table_workload').dataTable().fnGetPosition(this);
                var aData = $('#table_workload').dataTable().fnGetData(aPos[0]);
                if(clicks === 1) {
                    timer = setTimeout(function() {
                        clicks = 0;             //after action performed, reset counter
                        timer = null;
                        window.location = 'index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid='+aData[0];
                    }, DELAY);
                } else {
                    clearTimeout(timer);    //prevent single-click action
                    clicks = 0;             //after action performed, reset counter
                    timer = null;
                    var win = window.open('index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid='+aData[0], '_blank');
                    win.focus();
                }
            })
        .on("dblclick", function(e){
            e.preventDefault();  //cancel system double-click event
        });

        var table_workload_summary = $('#table_workload_summary').DataTable({
            "aaSorting": [[0, "asc"]],
            "dom": 'rti',
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
            timepicker: true,
            format: 'd.m.Y'
        });

        var data_stati = <?php echo json_encode($states);?>;
        $.plot('#stati_donut', data_stati, {
            series: {
                pie: {
                    innerRadius: 0.3,
                    show: true,
                    label: {
                        show: true,
                        radius: 3/4,
                        formatter: function(label, series){
                            var percent = Math.round(series.percent);
                            return (label+'<br/><b>' + percent + '%</b>'); // custom format
                        },
                        background: {
                            opacity: 0.5
                        }
                    }
                }
            }
        });

        var data_category = <?php echo json_encode($categories);?>;
        $.plot('#category_donut', data_category, {
            series: {
                pie: {
                    innerRadius: 0.3,
                    show: true,
                    label: {
                        show: true,
                        radius: 3/4,
                        formatter: function(label, series){
                            var percent = Math.round(series.percent);
                            return (label+'<br/><b>' + percent + '%</b>'); // custom format
                        },
                        background: {
                            opacity: 0.5
                        }
                    }
                }
            }
        });
    });
</script>

