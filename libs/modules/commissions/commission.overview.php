<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
?>
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
<link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">


<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Provisionsübersicht</h3>
            </div>
            <div class="panel-body">
                <div class="panel panel-default">
                	  <div class="panel-heading">
                			<h3 class="panel-title">Filter</h3>
                	  </div>
                	  <div class="panel-body">
                          <div class="form-horizontal">
                              <div class="form-group">
                                  <label for="" class="col-sm-2 control-label">Datum</label>
                                  <div class="col-sm-4">
                                      <input name="ajax_date_min" id="ajax_date_min" type="hidden"/>
                                      <input name="date_min" id="date_min" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                  </div>
                                  <label for="" class="col-sm-2 control-label" style="text-align: center">Bis</label>
                                  <div class="col-sm-4">
                                      <input name="ajax_date_max" id="ajax_date_max" type="hidden"/>
                                      <input name="date_max" id="date_max" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label for="" class="col-sm-2 control-label">Kunde</label>
                                  <div class="col-sm-10">
                                      <input type="text" id="custsearch" name="custsearch" class="form-control">
                                      <input type="hidden" id="custsearch_id">
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label for="" class="col-sm-2 control-label">Partner</label>
                                  <div class="col-sm-10">
                                      <input type="text" id="partnersearch" name="partnersearch" class="form-control">
                                      <input type="hidden" id="partnersearch_id">
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label for="" class="col-sm-2 control-label">Bereits gutgeschrieben</label>
                                  <div class="col-sm-10">
                                      <div class="checkbox">
                                          <label>
                                              <input type="checkbox" id="credited" value="1">
                                          </label>
                                      </div>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label for="" class="col-sm-2 control-label">Suche</label>
                                  <div class="col-sm-10">
                                      <input type="text" id="search" class="form-control" placeholder="">
                                  </div>
                              </div>
                          </div>
                	  </div>
                </div>
                <div class="table-responsive">
                	<table class="table table-hover" id="comtable">
                		<thead>
                			<tr>
                				<th>ID</th>
                				<th>Empfänger</th>
                				<th>Datum</th>
                				<th>Ursprung</th>
                				<th>Kunde</th>
                				<th>Prozent</th>
                				<th>Betrag</th>
                				<th>Datum-GS</th>
                				<th>Vorgang-GS</th>
                				<th></th>
                			</tr>
                		</thead>
                	</table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables -->
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.bootstrap.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.tableTools.js"></script>
<script type="text/javascript">
    function createGS(id, ele)
    {
        $.ajax({
            type: "GET",
            url: "libs/modules/commissions/commission.ajax.php",
            data: { ajax_action: "create", commission: id },
            success: function(data)
            {
                $(ele).html('erstellt');
                $('#comtable').dataTable().fnDraw();
            }
        });
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
                    $('#comtable').dataTable().fnDraw();
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
                    $('#comtable').dataTable().fnDraw();
                }
            }
        );

        $( "#custsearch" ).autocomplete({
            source: "libs/modules/tickets/ticket.ajax.php?ajax_action=search_customer",
            minLength: 2,
            focus: function( event, ui ) {
                $( "#custsearch" ).val( ui.item.label );
                return false;
            },
            select: function( event, ui ) {
                $( "#custsearch" ).val( ui.item.label );
                $( "#custsearch_id" ).val( ui.item.value );
                $('#comtable').dataTable().fnDraw();
                return false;
            }
        });

        $( "#partnersearch" ).autocomplete({
            source: "libs/modules/tickets/ticket.ajax.php?ajax_action=search_commissionpartner",
            minLength: 2,
            focus: function( event, ui ) {
                $( "#partnersearch" ).val( ui.item.label );
                return false;
            },
            select: function( event, ui ) {
                $( "#partnersearch" ).val( ui.item.label );
                $( "#partnersearch_id" ).val( ui.item.value );
                $('#comtable').dataTable().fnDraw();
                return false;
            }
        });

        $('#credited').change(function(){
            $('#comtable').dataTable().fnDraw();
        });

        var comtable = $('#comtable').DataTable( {
            "autoWidth": false,
            "processing": true,
            "bServerSide": true,
            "sAjaxSource": "libs/modules/commissions/commission.dt.ajax.php",
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
                        "sPdfMessage": "Contilas - Provisionen"
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
            },
            "fnServerParams": function ( aoData ) {
                var iMin = document.getElementById('ajax_date_min').value;
                var iMax = document.getElementById('ajax_date_max').value;
                var customer = document.getElementById('custsearch_id').value;
                var partner = document.getElementById('partnersearch_id').value;
                var credited = 0;
                if ($('#credited').prop( "checked" ))
                    credited = 1;
                aoData.push( { "name": "credited", "value": credited } );
                aoData.push( { "name": "bcid", "value": customer } );
                aoData.push( { "name": "partnerid", "value": partner } );
                aoData.push( { "name": "start", "value": iMin } );
                aoData.push( { "name": "end", "value": iMax } );
            }
        });

        $('#search').keyup(function(){
            comtable.search( $(this).val() ).draw();
        });

    } );
</script>