<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */


?>
<!-- DataTables Editor -->
<link rel="stylesheet" type="text/css" href="jscripts/datatableeditor/datatables.min.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/datatables.min.js"></script>

<script type="text/javascript" src="jscripts/datatableeditor/FieldType-autoComplete/editor.autoComplete.js"></script>
<link rel="stylesheet" type="text/css"
      href="jscripts/datatableeditor/FieldType-bootstrapDate/editor.bootstrapDate.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/FieldType-bootstrapDate/editor.bootstrapDate.js"></script>
<script type="text/javascript"
        src="jscripts/datatableeditor/FieldType-datetimepicker-2/editor.datetimepicker-2.js"></script>
<!-- /DataTables Editor -->

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Maschinengruppen
            <span class="pull-right">
				<button class="btn btn-xs btn-success" type="button"
                        onclick="window.location.href='index.php?page=libs/modules/machines/machinegroup.edit.php';">
                    <span class="glyphicons glyphicons-plus"></span>
                    Maschinengruppe hinzufügen
                </button>
			</span>
        </h3>
    </div>
    <div class="table-responsive">
        <table class="table table-hover" id="datatable">
            <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Typ</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Typ</th>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<script type="text/javascript" language="javascript" class="init">
    var editor; // use a global for the submit and return data rendering in the examples
    var table; // use global for table

    $(document).ready(function () {

        table = $('#datatable').DataTable({
            dom: "<'row'<'col-sm-4'l><'col-sm-4'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>",
            ajax: {
                url: 'libs/basic/datatables/machinegroups.php',
                data: {}
            },
            order: [[1, 'desc']],
            columns: [
                {data: "id", orderable: true},
                {data: "name", orderable: true},
                {data: "type", orderable: true},
            ],
            select: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.13/i18n/German.json'
            }
        });

        $("#datatable").on('click', 'tbody td', function () {
            var data = table.row(this).data();
            var id = data.id;
            document.location = 'index.php?page=libs/modules/machines/machinegroup.edit.php&id=' + id;
        });
    });
</script>