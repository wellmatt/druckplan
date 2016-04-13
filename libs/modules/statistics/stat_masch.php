<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/statistics/statistics.class.php';
require_once 'libs/basic/globalFunctions.php';
require_once 'libs/modules/machines/machinegroup.class.php';

$stat_mgroup = 0;

$start = mktime(0, 0, 0, date('m', time()), 1, date('Y', time()));
$end = mktime(0, 0, 0, date('m', time()), cal_days_in_month(CAL_GREGORIAN, date('m', time()), date('Y', time())), date('Y', time()));


if ($_REQUEST["stat_from"]) {
    $start = strtotime($_REQUEST["stat_from"]);
}
if ($_REQUEST["stat_to"]) {
    $end = strtotime($_REQUEST["stat_to"]);
}
if ($_REQUEST["stat_mgroup"]) {
    $stat_mgroup = $_REQUEST["stat_mgroup"];
}

$machstats = Statistics::Maschstat($start, $end, $stat_mgroup);
$mgroups = MachineGroup::getAllMachineGroups();
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
                    <h3 class="panel-title">Maschinenstatistik</h3>
                </div>
                <div class="panel-body">
                    <div class="panel panel-default">
                          <div class="panel-heading">
                                <h3 class="panel-title">Filter
                                    <span class="pull-right">
                                        <button class="btn btn-xs btn-success" onclick="$('#stat_mach').submit();">
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
                                        <label for="" class="col-sm-1 control-label">Gruppe</label>
                                        <div class="col-sm-3">
                                            <select name="stat_mgroup" id="stat_mgroup" class="form-control">
                                                <option value="0">- Alle -</option>
                                                <?php
                                                foreach ($mgroups as $item) {
                                                    if ($item->getId() == $stat_mgroup){
                                                        echo '<option selected value="' . $item->getId() . '">' . $item->getName() . '</option>';
                                                    } else {
                                                        echo '<option value="' . $item->getId() . '">' . $item->getName() . '</option>';
                                                    }
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
                                    <th>#ID</th>
                                    <th>Name</th>
                                    <th>Soll</th>
                                    <th>Ist</th>
                                    <th>Wert</th>
                                    <th>Anz. Auftr.</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($machstats as $machstat) {?>
                                <tr>
                                    <td><?php echo $machstat['id'];?></td>
                                    <td><?php echo $machstat['name'];?></td>
                                    <td><?php echo printPrice($machstat['zeitsoll'],2);?></td>
                                    <td><?php echo printPrice($machstat['zeitist'],2);?></td>
                                    <td><?php echo printPrice($machstat['auftragswert'],2);?></td>
                                    <td><?php echo $machstat['anzahlauftraege'];?></td>
                                </tr>
                                <?php
                                $nettotal += $machstat['auftragswert'];
                            } ?>
                            <?php
                            echo '<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td class="highlight"><b>Gesamt Summe:</b></td><td>' .printPrice($nettotal,2). '</td><td></td></tr>';
                            ?>
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
        var machtable = $('#machtable').DataTable({
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
    });
</script>


<script>
    $(function () {
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
    });
</script>

<script>
    $(function () {
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