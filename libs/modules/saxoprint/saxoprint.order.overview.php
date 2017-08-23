<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/modules/saxoprint/saxoprint.class.php';
require_once 'libs/basic/csv/CsvWriter.class.php';

$colinvs = CollectiveInvoice::getAllSaxoOpen();

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
<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Saxoprint Aufträge
            <span class="pull-right">
                <button class="btn btn-xs btn-success" type="button" onclick="
                window.location.href='libs/modules/saxoprint/saxoprint.orders.export.php?date_min='+$('#ajax_date_min').val()+'&date_max='+$('#ajax_date_max').val()+'&saxomaterial='+$('#saxomaterial').val()+'&saxoformat='+$('#saxoformat').val()+'&saxoprodgrp='+$('#saxoprodgrp').val();">
                    <span class="glyphicons glyphicons-disk-import"></span> Export
                </button>
            </span>
        </h3>
    </div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Filter Optionen
                </h3>
            </div>
            <div class="panel-body">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Datum Von</label>
                    <div class="col-sm-4">
                        <input name="ajax_date_min" id="ajax_date_min" type="hidden" value="<?php echo strtotime(date('01.m.Y',time()));?>"/>
                        <input name="date_min" id="date_min" class="form-control" onfocus="markfield(this,0)"
                               onblur="markfield(this,1)" title="von" value="<?php echo date('01.m.Y',time());?>">
                    </div>
                    <label for="" class="col-sm-2 control-label" style="text-align: center">Bis</label>
                    <div class="col-sm-4">
                        <input name="ajax_date_max" id="ajax_date_max" type="hidden" value="<?php echo strtotime(date('t.m.Y',time()));?>"/>
                        <input name="date_max" id="date_max" class="form-control" onfocus="markfield(this,0)"
                               onblur="markfield(this,1)" title="bis" value="<?php echo date('t.m.Y',time());?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Material</label>
                    <div class="col-sm-10">
                        <select id="saxomaterial" name="saxomaterial" class="form-control"></select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Format</label>
                    <div class="col-sm-10">
                        <select id="saxoformat" name="saxoformat" class="form-control"></select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Produktg.</label>
                    <div class="col-sm-10">
                        <select id="saxoprodgrp" name="saxoprodgrp" class="form-control"></select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover" id="saxotable">
            <thead>
                <tr>
                    <th>VO-Nr.</th>
                    <th>ContractID</th>
                    <th>ReferenzID</th>
                    <th>Compl.Date</th>
                    <th>Prod.Grp.</th>
                    <th>Material</th>
                    <th>Format</th>
                    <th>Auflage</th>
                    <th>Farbe</th>
                    <th>Stanzen</th>
                    <th>Form</th>
                    <th>Buchstanze</th>
                    <th>Logistik</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript" language="javascript" class="init">
    var table; // use global for table

    $(document).ready(function() {

        table = $('#saxotable').DataTable( {
            dom: "<'row'<'col-sm-12'tr>>",
            ajax: {
                url: 'libs/basic/datatables/saxoprintorders.php',
                data: function (d) {
                    return {
                        "date_min": $('#ajax_date_min').val(),
                        "date_max": $('#ajax_date_max').val(),
                        "saxomaterial": $('#saxomaterial').val(),
                        "saxoformat": $('#saxoformat').val(),
                        "saxoprodgrp": $('#saxoprodgrp').val()
                    };
                }
            },
            paging: false,
            searching: false,
            columns: [
                { data: 'collectiveinvoice.number', className: 'pointer', orderable: true },
                { data: "collectiveinvoice_saxoinfo.contractid", orderable: true, className: 'pointer' },
                { data: "collectiveinvoice_saxoinfo.referenceid", orderable: true, className: 'pointer' },
                { data: "collectiveinvoice_saxoinfo.compldate", orderable: true, className: 'pointer' },
                { data: "collectiveinvoice_saxoinfo.prodgrp", orderable: true, className: 'pointer' },
                { data: "collectiveinvoice_saxoinfo.material", orderable: true, className: 'pointer' },
                { data: "collectiveinvoice_saxoinfo.format", orderable: true, className: 'pointer' },
                { data: "collectiveinvoice_saxoinfo.amount", orderable: true, className: 'pointer' },
                { data: "collectiveinvoice_saxoinfo.chroma", orderable: true, className: 'pointer' },
                { data: "collectiveinvoice_saxoinfo.stamp", orderable: true, className: 'pointer' },
                { data: "collectiveinvoice_saxoinfo.form", orderable: true, className: 'pointer' },
                { data: "collectiveinvoice_saxoinfo.bookstamp", orderable: true, className: 'pointer' },
                { data: "collectiveinvoice_saxoinfo.logistic", orderable: true, className: 'pointer' },
                { data: "collectiveinvoice.status", orderable: true, className: 'pointer' }
            ],
            select: false,
            buttons: [],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.13/i18n/German.json'
            }
        } );

        $.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
        $('#date_min').datepicker(
            {
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'dd.mm.yy',
                onSelect: function(selectedDate) {
                    $('#ajax_date_min').val(moment($('#date_min').val(), "DD-MM-YYYY").unix());
                    table.ajax.reload();
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
                    table.ajax.reload();
                }
            }
        );
    } );
</script>
<script>
    $(function () {
        $("#saxomaterial").select2({
            ajax: {
                url: "libs/basic/ajax/select2.ajax.php?ajax_action=search_saxomaterial",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            language: "de",
            multiple: false,
            allowClear: false,
            tags: false
        });
        $("#saxomaterial").on("select2:select", function (e) { table.ajax.reload(); });
    });
</script>
<script>
    $(function () {
        $("#saxoformat").select2({
            ajax: {
                url: "libs/basic/ajax/select2.ajax.php?ajax_action=search_saxoformat",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            language: "de",
            multiple: false,
            allowClear: false,
            tags: false
        });
        $("#saxoformat").on("select2:select", function (e) { table.ajax.reload(); });
    });
</script>
<script>
    $(function () {
        $("#saxoprodgrp").select2({
            ajax: {
                url: "libs/basic/ajax/select2.ajax.php?ajax_action=search_saxoprodgrp",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            language: "de",
            multiple: false,
            allowClear: false,
            tags: false
        });
        $("#saxoprodgrp").on("select2:select", function (e) { table.ajax.reload(); });
    });
</script>