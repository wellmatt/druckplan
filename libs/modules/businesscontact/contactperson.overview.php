<?php
// ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       29.08.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/businesscontact/contactperson.class.php';
require_once 'libs/modules/businesscontact/attribute.class.php';

?>

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.tableTools.js"></script>
<!-- <script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.columnFilter.js"></script> -->
<script type="text/javascript">
$(document).ready(function() {
    var cp_table = $('#cp_table').DataTable( {
        // "scrollY": "600px",
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/businesscontact/contactperson.dt.ajax.php",
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
                "sPdfMessage": "Contilas - Ansprechpartner"
            },
            "print"
                ]
    },
    "lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
    "columns": [
        null,
        null,
        null,
        { "sortable": false }
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
} );
</script>
<table width="100%">
<tr>
<td width="220" class="content_header">
<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <span style="font-size: 13px"><?=$_LANG->get('Ansprechpartner')?></span>
		</td>
	</tr>
</table>
<div class="box1">
	<table id="cp_table" width="100%" class="stripe hover row-border order-column">
        <thead>
            <tr>
                <th width="10">ID</th>
                <th>Name</th>
                <th>Firma</th>
                <th width="80">Optionen</th>
            </tr>
        </thead>
	</table>
</div>