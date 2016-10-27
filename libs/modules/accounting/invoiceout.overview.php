<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Christian Schroeer <cschroeer@ipactor.de>, 2016
 *
 */


?>
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
<link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Rechnungsausgang
            <span class="pull-right">
                <button class="btn btn-xs btn-success" type="button" onclick="invoutexport();">Export</button>
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
                    <label for="" class="col-sm-2 control-label">Datum (erstellt)</label>
                    <div class="col-sm-4">
                        <input name="ajax_date_min" id="ajax_date_min" type="hidden"/>
                        <input name="date_min" id="date_min" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                    </div>
                    <label for="" class="col-sm-2 control-label">Bis:</label>
                    <div class="col-sm-4">
                        <input name="ajax_date_max" id="ajax_date_max" type="hidden"/>
                        <input name="date_max" id="date_max" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-3 control-label">Suche:</label>
                    <div class="col-sm-9">
                        <input type="text" id="search" class="form-control" placeholder="">
                    </div>
                </div>
            </div>
        </div>
        <div class="table-responsive">
        	<table class="table table-hover" id="invouttable">
        		<thead>
        			<tr>
        				<th>ID</th>
        				<th>Re.-Nr.</th>
        				<th>VO-Nr.</th>
        				<th>VO-Titel</th>
        				<th>Kunde</th>
        				<th>Netto</th>
        				<th>Brutto</th>
        				<th>Erstellt</th>
        				<th>Fällig</th>
        				<th>Bezahlt</th>
        				<th>Status</th>
        			</tr>
        		</thead>
        	</table>
        </div>
    </div>
</div>

<!-- DataTables -->
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.bootstrap.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.tableTools.js"></script>
<script type="text/javascript">
    function invoutexport(){
        var datemin = parseInt($('#ajax_date_min').val());
        var datemax = parseInt($('#ajax_date_max').val());
        window.open('libs/modules/accounting/invoiceout.export.php?datemax='+datemax+'&datemin='+datemin);
    }

    $(document).ready(function() {

        $.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
        $('#date_min').datepicker(
            {
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'dd.mm.yy',
                onSelect: function(selectedDate) {
                    $('#ajax_date_min').val(moment($('#date_min').val(), "DD-MM-YYYY").unix());
                    $('#invouttable').dataTable().fnDraw();
                }
            }
        );

        $('#date_max').datepicker(
            {
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'dd.mm.yy',
                onSelect: function(selectedDate) {
                    $('#ajax_date_max').val(moment($('#date_max').val(), "DD-MM-YYYY").unix()+86340);
                    $('#invouttable').dataTable().fnDraw();
                }
            }
        );

        var invouttable = $('#invouttable').DataTable( {
            "processing": true,
            "bServerSide": true,
            "sAjaxSource": "libs/modules/accounting/invoiceout.dt.ajax.php",
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
                        "sPdfMessage": "Contilas - Businesscontacts"
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
                null,
                null,
                null,
                null,
                null,
                null,
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
            },
            "fnServerParams": function ( aoData ) {
                var iMin = document.getElementById('ajax_date_min').value;
                var iMax = document.getElementById('ajax_date_max').value;
                aoData.push( { "name": "start", "value": iMin, } );
                aoData.push( { "name": "end", "value": iMax, } );
            }
        });

        $('#search').keyup(function(){
            invouttable.search( $(this).val() ).draw();
        });

        $("#invouttable tbody td").live('click',function(){
            var aPos = $('#invouttable').dataTable().fnGetPosition(this);
            var aData = $('#invouttable').dataTable().fnGetData(aPos[0]);
            document.location='index.php?page=libs/modules/accounting/invoiceout.edit.php&exec=edit&id='+aData[0];
        });

    } );
</script>