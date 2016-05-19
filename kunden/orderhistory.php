<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			11.03.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

?>
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="../css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="../jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="../jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="../jscripts/datatable/dataTables.bootstrap.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		var ordhistable = $('#ordhistable').DataTable( {
			"processing": true,
			"bServerSide": true,
			"sAjaxSource": "orderhistory.dt.ajax.php?customerid=<?php echo $busicon->getId();?>",
			"paging": true,
			"stateSave": false,
			"pageLength": "10",
			"aaSorting": [[ 1, "desc" ]],
			"dom": 'flrtip',
			"lengthMenu": [ [10, 25], [10, 25] ],
			"columns": [
				null,
				null,
				{ "sortable": false, "searchable": false },
				{ "searchable": false },
				{ "searchable": false },
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
	} );
</script>
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<b>Ihre Bestellungen</b>
			</h3>
	  </div>
	<div class="table-responsive">
		<table class="table table-hover" id="ordhistable">
			<thead>
			<tr>
				<td class="content_row_header">Auftrag-Nr.</td>
				<td class="content_row_header">Bestelldatum</td>
				<td class="content_row_header">Positionen</td>
				<td class="content_row_header">Lieferadresse</td>
				<td class="content_row_header">Rechnungsadresse</td>
				<td class="content_row_header">Status</td>
			</tr>
			</thead>
		</table>
	</div>
</div>