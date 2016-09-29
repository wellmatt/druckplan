<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/suporder/suporder.class.php';

if ($_REQUEST["exec"] == "delete"){
    $del_order = new SupOrder($_REQUEST["id"]);
    $del_order->delete();
}
?>
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.tableTools.js"></script>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Lief. Bestellungen
            <span class="pull-right">
                <button class="btn btn-xs btn-success"
                        onclick="window.location.href='index.php?page=libs/modules/suporder/suporder.collective.php';">
                    <span class="glyphicons glyphicons-plus-sign"></span>
                    Sammelbestellung erstellen
                </button>
                <button class="btn btn-xs btn-success"
                        onclick="window.location.href='index.php?page=libs/modules/suporder/suporder.edit.php&exec=new';">
                    <span class="glyphicons glyphicons-plus-sign"></span>
                    Bestellung erstellen
                </button>
            </span>
        </h3>
    </div>
    <div class="panel-body">
        <div class="panel panel-default">
              <div class="panel-heading">
                    <h3 class="panel-title">Filter</h3>
              </div>
              <div class="panel-body">
                  <div class="form-group">
                      <label for="" class="col-sm-2 control-label">Suche</label>
                      <div class="col-sm-10">
                          <input type="text" id="search" class="form-control" placeholder="">
                      </div>
                  </div>
              </div>
        </div>
        <div class="table-responsive">
        	<table id="supordertable" class="table table-hover">
                <thead>
                    <tr>
                        <th><?= $_LANG->get('ID') ?></th>
                        <th><?= $_LANG->get('Nummer') ?></th>
                        <th><?= $_LANG->get('Titel') ?></th>
                        <th><?= $_LANG->get('Lieferant') ?></th>
                        <th><?= $_LANG->get('Status') ?></th>
                        <th><?= $_LANG->get('Datum') ?></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th><?= $_LANG->get('ID') ?></th>
                        <th><?= $_LANG->get('Nummer') ?></th>
                        <th><?= $_LANG->get('Titel') ?></th>
                        <th><?= $_LANG->get('Lieferant') ?></th>
                        <th><?= $_LANG->get('Status') ?></th>
                        <th><?= $_LANG->get('Datum') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        var supordertable = $('#supordertable').DataTable( {
            "processing": true,
            "autoWidth": false,
            "bServerSide": true,
            "sAjaxSource": "libs/modules/suporder/suporder.dt.ajax.php",
            "paging": true,
            "stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
            "pageLength": <?php echo $perf->getDt_show_default();?>,
            "dom": 'T<"clear">lrtip',
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
        } );

        $("#supordertable tbody td").live('click',function(){
            var aPos = $('#supordertable').dataTable().fnGetPosition(this);
            var aData = $('#supordertable').dataTable().fnGetData(aPos[0]);
            document.location='index.php?page=libs/modules/suporder/suporder.edit.php&exec=edit&id='+aData[0];
        });

        $('#search').keyup(function(){
            supordertable.search( $(this).val() ).draw();
        })
    } );
</script>