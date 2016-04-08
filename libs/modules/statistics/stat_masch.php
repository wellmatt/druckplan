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
    </div>
        <div class="col-md-1">Status:</div>
        <div class="col-md-1">Maschinengruppe:</div>
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
    <br>&nbsp;</br>
</form>


<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading"><div align="center">Pro Tag</div></div>
            <div class="panel-body">
                <table id="table_masch" class="stripe hover row-border order-column" style="width: auto" width="100%">
                    <thead>
                    <tr>
                        <td style="width: 10px;"><u><h5>ID</h5></u></td>
                        <td style="width: 60px;"><u><h5>Nummer</h5></u></td>
                        <td style="width: 200px;"><u><h5>Titel</h5></u></td>

                    </tr>
                    </thead>

                    <?php
                    $days = GetDays(date('d-m-Y', $start), date('d-m-Y', $end));
                    foreach ($days as $day) {
                        $retval = Statistics::Maschstat(strtotime($day), $stat_customer, $stat_user, $stat_article, $stat_tradegroup, $stat_status);
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
        var table_colinv_day = $('#table_masch').DataTable({
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
