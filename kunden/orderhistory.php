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

<div class="box2" style="min-height:180px;">
<table style="width:100%">
	<tr>
		<td width="400px">
    		<h1><?=$_LANG->get('Ihre Bestellungen');?></h1>
    	</td>
    	<td width="200px" align="right">
    	</td>
    </tr>
</table>

<table cellpadding="2" cellspacing="0" border="0" width="100%" id="ordhistable" class="stripe hover row-border order-column">
	<thead>
		<tr>
			<td>Auftrag-Nr.</td>
			<td align="center">Bestelldatum</td>
			<td>Positionen</td>
			<td>Lieferadresse</td>
			<td>Rechnungsadresse</td>
			<td align="center">Status</td>
		</tr>
	</thead>
</table>
</div>