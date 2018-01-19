<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */

$saascustid = $perf->getSaasdoCustomerId();
if ($saascustid > 0) $enabled = true; else $enabled = false;

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

<?php if (!$enabled){?>
<div class="alert alert-warning">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
    <strong>Modul deaktiviert!</strong> Bitte tragen Sie zuerst Ihre KundenID in den 'Einstellungen' unter 'Schnittstellen' ein.
</div>
<?php } ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Sendungen
            <?php if ($enabled){?>
            <span class="pull-right">
				<button class="btn btn-xs btn-success" type="button"
                        onclick="window.location.href='index.php?page=libs/modules/shipment/shipment.new.php';">
                    <span class="glyphicons glyphicons-plus"></span>
                    Sendungen hinzufügen
                </button>
			</span>
            <?php } ?>
        </h3>
    </div>
    <div class="table-responsive">
        <table class="table table-hover" id="datatable">
            <thead>
            <tr>
                <th>ID</th>
                <th>Vorgang</th>
                <th>Kunde</th>
                <th>Paketnummer</th>
                <th>Datum</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th>ID</th>
                <th>Vorgang</th>
                <th>Kunde</th>
                <th>Paketnummer</th>
                <th>Datum</th>
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
                url: 'libs/basic/datatables/shipment.php',
                data: {}
            },
            order: [[1, 'desc']],
            columns: [
                {data: "shipments.id", orderable: true},
                {data: "collectiveinvoice.number", orderable: true},
                {data: "businesscontact.name1", orderable: true},
                {data: "shipments.parcelNumber", orderable: true},
                {data: "shipments.shipDate", orderable: true},
            ],
            select: false,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.13/i18n/German.json'
            }
        });

        $("#datatable").on('click', 'tbody td', function () {
            var data = table.row(this).data();
            var id = data.shipments.id;
            document.location = 'index.php?page=libs/modules/shipment/shipment.view.php&id=' + id;
        });
    });
</script>