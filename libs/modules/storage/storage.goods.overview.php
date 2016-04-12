<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/suporder/suporder.class.php';
require_once 'libs/modules/storage/storage.area.class.php';
require_once 'libs/modules/storage/storage.goods.position.class.php';

$goods_inc = StorageGoods::getAllIncoming();
$goods_out = StorageGoods::getAllOutgoing();
$open_suporder = SupOrder::getAllOrdered();
$open_colinv = CollectiveInvoice::getAllRdyForDispatch();
$open = array_merge($open_suporder,$open_colinv);
?>
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.tableTools.js"></script>

<?php if (isset($savemsg)) { ?>
    <div class="alert alert-info">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Hinweis!</strong> <?= $savemsg ?>
    </div>
<?php } ?>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Warenein-/Ausgänge</h3>
            </div>
            <div class="panel-body">
                <!-- TAB NAVIGATION -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active"><a href="#tab1" role="tab" data-toggle="tab">Offen</a></li>
                    <li><a href="#tab2" role="tab" data-toggle="tab">Eingänge</a></li>
                    <li><a href="#tab3" role="tab" data-toggle="tab">Ausgänge</a></li>
                </ul>
                <!-- TAB CONTENT -->
                <div class="tab-content">
                    <div class="active tab-pane" id="tab1">
                        <br>
                        <table class="table table-striped table-hover" id="goods_open">
                            <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Typ</th>
                                <th>Herkunft</th>
                                <th>Erst. von</th>
                                <th>am</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($open as $item) {
                                if (is_a($item,'SupOrder')){
                                    $type = 'Eingang';
                                    $arg = 1;
                                } else if ((is_a($item,'CollectiveInvoice'))){
                                    $type = 'Ausgang';
                                    $arg = 2;
                                }
                                ?>
                                <tr class="pointer" onclick="window.location.href='index.php?page=libs/modules/storage/storage.goods.create.php&type=<?=$arg?>&obj=<?=$item->getId()?>';">
                                    <td><?php echo $item->getId();?></td>
                                    <td><?php echo $type;?></td>
                                    <td><?php echo $item->getNumber();?></td>
                                    <td><?php echo $item->getCrtuser()->getNameAsLine();?></td>
                                    <td><?php echo date('d.m.Y H:i',$item->getCrtdate());?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="tab2">
                        <br>
                        <table class="table table-striped table-hover" id="goods_inc">
                        	<thead>
                        		<tr>
                        			<th>#ID</th>
                                    <th>Herkunft</th>
                                    <th>Erst. von</th>
                                    <th>am</th>
                        		</tr>
                        	</thead>
                        	<tbody>
                                <?php foreach ($goods_inc as $item) {?>
                                    <tr>
                                        <td><?php echo $item->getId();?></td>
                                        <td><?php echo $item->getOrigin()->getNumber();?></td>
                                        <td><?php echo $item->getCrtuser()->getNameAsLine();?></td>
                                        <td><?php echo date('d.m.Y H:i',$item->getCrtdate());?></td>
                                    </tr>
                                <?php } ?>
                        	</tbody>
                        </table>
                    </div>
                    <div class="tab-pane" id="tab3">
                        <br>
                        <table class="table table-striped table-hover" id="goods_out">
                            <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Herkunft</th>
                                <th>Erst. von</th>
                                <th>am</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($goods_out as $item) {?>
                                <tr>
                                    <td><?php echo $item->getId();?></td>
                                    <td><?php echo $item->getOrigin()->getNumber();?></td>
                                    <td><?php echo $item->getCrtuser()->getNameAsLine();?></td>
                                    <td><?php echo date('d.m.Y H:i',$item->getCrtdate());?></td>
                                </tr>
                            <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        var goods_open = $('#goods_open').DataTable( {
            "paging": true,
            "stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
            "pageLength": <?php echo $perf->getDt_show_default();?>,
            "dom": 'T<"clear">flrtip',
            "tableTools": {
                "sSwfPath": "jscripts/datatable/copy_csv_xls_pdf.swf",
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
            "lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
            "columns": [
                null,
                null,
                null,
                null,
                null
            ],
            "language":
            {
                "emptyTable":     "Keine Daten vorhanden",
                "info":           "Zeige _START_ bis _END_ von _TOTAL_ Eintr&auml;gen",
                "infoEmpty": 	  "Keine Seiten vorhanden",
                "infoFiltered":   "(gefiltert von _MAX_ gesamten Eintr&auml;gen)",
                "infoPostFix":    "",
                "thousands":      ".",
                "lengthMenu":     "Zeige _MENU_ Eintr&auml;ge",
                "loadingRecords": "Lade...",
                "processing":     "Verarbeite...",
                "search":         "Suche:",
                "zeroRecords":    "Keine passenden Eintr&auml;ge gefunden",
                "paginate": {
                    "first":      "Erste",
                    "last":       "Letzte",
                    "next":       "N&auml;chste",
                    "previous":   "Vorherige"
                },
                "aria": {
                    "sortAscending":  ": aktivieren um aufsteigend zu sortieren",
                    "sortDescending": ": aktivieren um absteigend zu sortieren"
                }
            }
        });

        var goods_inc = $('#goods_inc').DataTable( {
            "paging": true,
            "stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
            "pageLength": <?php echo $perf->getDt_show_default();?>,
            "dom": 'T<"clear">flrtip',
            "tableTools": {
                "sSwfPath": "jscripts/datatable/copy_csv_xls_pdf.swf",
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
            "lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
            "columns": [
                null,
                null,
                null,
                null
            ],
            "language":
            {
                "emptyTable":     "Keine Daten vorhanden",
                "info":           "Zeige _START_ bis _END_ von _TOTAL_ Eintr&auml;gen",
                "infoEmpty": 	  "Keine Seiten vorhanden",
                "infoFiltered":   "(gefiltert von _MAX_ gesamten Eintr&auml;gen)",
                "infoPostFix":    "",
                "thousands":      ".",
                "lengthMenu":     "Zeige _MENU_ Eintr&auml;ge",
                "loadingRecords": "Lade...",
                "processing":     "Verarbeite...",
                "search":         "Suche:",
                "zeroRecords":    "Keine passenden Eintr&auml;ge gefunden",
                "paginate": {
                    "first":      "Erste",
                    "last":       "Letzte",
                    "next":       "N&auml;chste",
                    "previous":   "Vorherige"
                },
                "aria": {
                    "sortAscending":  ": aktivieren um aufsteigend zu sortieren",
                    "sortDescending": ": aktivieren um absteigend zu sortieren"
                }
            }
        });

        var goods_out = $('#goods_out').DataTable( {
            "paging": true,
            "stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
            "pageLength": <?php echo $perf->getDt_show_default();?>,
            "dom": 'T<"clear">flrtip',
            "tableTools": {
                "sSwfPath": "jscripts/datatable/copy_csv_xls_pdf.swf",
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
            "lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
            "columns": [
                null,
                null,
                null,
                null
            ],
            "language":
            {
                "emptyTable":     "Keine Daten vorhanden",
                "info":           "Zeige _START_ bis _END_ von _TOTAL_ Eintr&auml;gen",
                "infoEmpty": 	  "Keine Seiten vorhanden",
                "infoFiltered":   "(gefiltert von _MAX_ gesamten Eintr&auml;gen)",
                "infoPostFix":    "",
                "thousands":      ".",
                "lengthMenu":     "Zeige _MENU_ Eintr&auml;ge",
                "loadingRecords": "Lade...",
                "processing":     "Verarbeite...",
                "search":         "Suche:",
                "zeroRecords":    "Keine passenden Eintr&auml;ge gefunden",
                "paginate": {
                    "first":      "Erste",
                    "last":       "Letzte",
                    "next":       "N&auml;chste",
                    "previous":   "Vorherige"
                },
                "aria": {
                    "sortAscending":  ": aktivieren um aufsteigend zu sortieren",
                    "sortDescending": ": aktivieren um absteigend zu sortieren"
                }
            }
        });
    });
</script>