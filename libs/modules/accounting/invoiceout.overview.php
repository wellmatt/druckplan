<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Christian Schroeer <cschroeer@ipactor.de>, 2016
 *
 */

?>

<!-- DataTables Editor -->
<link rel="stylesheet" type="text/css" href="jscripts/datatableeditor/datatables.min.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/datatables.min.js"></script>

<script type="text/javascript" src="jscripts/datatableeditor/FieldType-autoComplete/editor.autoComplete.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/datatableeditor/FieldType-bootstrapDate/editor.bootstrapDate.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/FieldType-bootstrapDate/editor.bootstrapDate.js"></script>
<script type="text/javascript" src="jscripts/datatableeditor/FieldType-datetimepicker-2/editor.datetimepicker-2.js"></script>

<script type="text/javascript" src="jscripts/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="jscripts/ckeditor/config.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/ckeditor/skins/bootstrapck/editor.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/FieldType-ckeditor/editor.ckeditor.js"></script>
<!-- /DataTables Editor -->

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Rechnungsausgang
            <span class="pull-right">
                <button class="btn btn-xs btn-success" type="button" id="Export">Export CSV</button>
                <button class="btn btn-xs btn-success" type="button" id="Export2">Export TXT</button>
                <button class="btn btn-xs btn-success" type="button" id="printAll">Drucken</button>
                <button class="btn btn-xs btn-success" type="button" id="printAllEmail">Drucken (eMail)</button>
            </span>
        </h3>
    </div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Filter
                     <span class="pull-right">
                              <button class="btn btn-xs btn-success" onclick="TicketTableRefresh();" href="Javascript:">
                                  <span class="glyphicons glyphicons-refresh"></span>
                                  <?= $_LANG->get('Refresh') ?>
                              </button>
                     </span>
                </h3>
            </div>
            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Datum (erstellt)</label>
                        <div class="col-sm-4">
                            <input name="ajax_date_min" id="ajax_date_min" type="hidden"/>
                            <input name="date_min" id="date_min" class="form-control" onfocus="markfield(this,0)"
                                   onblur="markfield(this,1)">
                        </div>
                        <label for="" class="col-sm-2 control-label" style="text-align: center">Bis</label>
                        <div class="col-sm-4">
                            <input name="ajax_date_max" id="ajax_date_max" type="hidden"/>
                            <input name="date_max" id="date_max" class="form-control" onfocus="markfield(this,0)"
                                   onblur="markfield(this,1)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Kunde</label>
                        <div class="col-sm-10">
                            <input type="text" id="custsearch" name="custsearch" class="form-control"
                                   value="<?php echo $_REQUEST["custfilter_name"]; ?>">
                            <input type="hidden" id="custsearch_id" value="<?php echo $_REQUEST["custfilter_id"]; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10">
                            <select name="ajax_status" id="ajax_status" class="form-control">
                                <option value="0">- Alle -</option>
                                <option value="1">offen</option>
                                <option value="2">bezahlt</option>
                                <option value="3">storniert</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Typ</label>
                        <div class="col-sm-10">
                            <select name="ajax_type" id="ajax_type" class="form-control">
                                <option value="0">- Alle -</option>
                                <option value="1">Rechnungen</option>
                                <option value="2">Gutschriften</option>
                            </select>
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
            <table class="table table-hover" id="invouttable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Dok.-Nr.</th>
                        <th>Typ</th>
                        <th>VO-Nr.</th>
                        <th>VO-Titel</th>
                        <th>Kunde</th>
                        <th>Netto</th>
                        <th>Brutto</th>
                        <th>Erstellt</th>
                        <th>Fällig</th>
                        <th>Bezahlt</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td align="left" style="padding: 8px;">Summe:</td>
                        <td align="left" style="padding: 8px;"></td>
                        <td align="left" style="padding: 8px;"></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
            * Bitte beachten Sie: Die Summen beinhalten ALLE angezeigten Elemente!
        </div>
    </div>
</div>

<script type="text/javascript">
    function TicketTableRefresh()
    {
        $('#invouttable').dataTable().fnDraw();
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
                $('#invouttable').dataTable().fnDraw();
                return false;
            }
        });

        $('#ajax_status').change(function(){
            $('#invouttable').dataTable().fnDraw();
        });

        $('#ajax_type').change(function(){
            $('#invouttable').dataTable().fnDraw();
        });

        var invouttable = $('#invouttable').DataTable( {
            "autoWidth": false,
            "processing": true,
            "bServerSide": true,
            "sAjaxSource": "libs/modules/accounting/invoiceout.dt.ajax.php",
            "paging": true,
            "stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
            "pageLength": <?php echo $perf->getDt_show_default();?>,
            "dom": 'T<"clear">lrtip',
            "aaSorting": [[ 9, "desc" ]],
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
                null,
                null,
                null
            ],
            "columnDefs": [
                {
                    "targets": [ 13 ],
                    "visible": false,
                    "searchable": false
                },
                {
                    "targets": [ 14 ],
                    "visible": false,
                    "searchable": false
                }
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
                var status = document.getElementById('ajax_status').value;
                var type = document.getElementById('ajax_type').value;
                aoData.push( { "name": "type", "value": type, } );
                aoData.push( { "name": "status", "value": status, } );
                aoData.push( { "name": "bcid", "value": customer, } );
                aoData.push( { "name": "start", "value": iMin, } );
                aoData.push( { "name": "end", "value": iMax, } );
            },
            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api(), data;

                // Total over this page
                pageTotal_price_net = api
                    .column( 6 )
                    .data()
                    .reduce( function (a, b) {
//                        console.log("current: "+parseFloat(a)+" | adding: "+parseFloat(b.replace(".","").replace(",",".")));
                        return parseFloat(a) + parseFloat(b.replace(".","").replace(",","."));
                    }, 0 );

                // Total over this page
                pageTotal_price_gross = api
                    .column( 7 )
                    .data()
                    .reduce( function (a, b) {
//                        console.log("current: "+parseFloat(a)+" | adding: "+parseFloat(b.replace(".","").replace(",",".")));
                        return parseFloat(a) + parseFloat(b.replace(".","").replace(",","."));
                    }, 0 );

                // Update footer
                $( api.column( 6 ).footer() ).html(
                    printPriceJs(pageTotal_price_net, 2) + " €"
                );
                $( api.column( 7 ).footer() ).html(
                    printPriceJs(pageTotal_price_gross, 2) + " €"
                );
            }
        });

        $('#printAll').on( 'click', function () {
            var ids = [];
            invouttable
                .column( 13 )
                .data()
                .each( function ( value, index ) {
                    ids.push(value);
                } );
            window.open('libs/modules/accounting/invoiceout.print.php?ids='+ids.join());
        } );

        $('#printAllEmail').on( 'click', function () {
            var ids = [];
            invouttable
                .column( 13 )
                .data()
                .each( function ( value, index ) {
                    ids.push(value);
                } );
            window.open('libs/modules/accounting/invoiceout.print.php?version=email&ids='+ids.join());
        } );

        $('#Export').on( 'click', function () {
            var elements = [];
            invouttable.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
                var data = this.data();
                elements.push([data[0],data[14]]);
            } );
            window.open('libs/modules/accounting/invoiceout.export.php?param='+JSON.stringify(elements));
        } );

        $('#Export2').on( 'click', function () {
            var elements = [];
            invouttable.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
                var data = this.data();
                elements.push([data[0],data[14]]);
            } );
            window.open('libs/modules/accounting/invoiceout.export2.php?param='+JSON.stringify(elements));
        } );

        $('#search').keyup(function(){
            invouttable.search( $(this).val() ).draw();
        });

        $("#invouttable tbody td:not(:last-child)").live('click',function(){
            var aPos = $('#invouttable').dataTable().fnGetPosition(this);
            var aData = $('#invouttable').dataTable().fnGetData(aPos[0]);
            if (aData[2] == 'Rechnung')
                document.location='index.php?page=libs/modules/accounting/invoiceout.edit.php&exec=edit&id='+aData[0];
            if (aData[2] == 'Gutschrift')
                document.location='index.php?page=libs/modules/accounting/revert.edit.php&exec=edit&id='+aData[0];
        });

    } );
</script>