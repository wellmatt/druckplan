<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			25.06.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/notifications/notification.class.php';

?>

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.tableTools.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/date-uk.js"></script>

<script type="text/javascript">

jQuery.fn.dataTableExt.oSort['uk_date-asc']  = function(a,b) {
    var ukDatea = a.split('.');
    var ukDateb = b.split('.');
     
    var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;
     
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['uk_date-desc'] = function(a,b) {
    var ukDatea = a.split('.');
    var ukDateb = b.split('.');
     
    var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;
     
    return ((x < y) ? 1 : ((x > y) ?  -1 : 0));
};

$(document).ready(function() {
    var notifytable = $('#notifytable').DataTable( {
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/notifications/notification.dt.ajax.php?userid=<?php echo $_USER->getId();?>",
        "paging": true,
		"stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
		"pageLength": <?php echo $perf->getDt_show_default();?>,
		"dom": 'T<"clear">flrtip',   
		"aaSorting": [[ 0, "desc" ]],     
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
		            null
		          ],
        "columnDefs": [
                       { type: 'date-uk', targets: 2 }
                      ],
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
                          if ( aData[4] == "1" )
                          {
//                             $('td:eq(1)', nRow).html( '<b>A</b>' );
                            $(nRow).addClass('highlight');
                          }
        },
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
    $("#notifytable tbody td").live('click',function(){
        var aPos = $('#notifytable').dataTable().fnGetPosition(this);
        var aData = $('#notifytable').dataTable().fnGetData(aPos[0]);
        document.location="index.php?page=libs/modules/notifications/notification.redirect.php&exec=redirect&nid="+aData[0];
    });
} );
</script>

<table width="100%">
	<tr>
		<td width="150" class="content_header"><img
			src="images/icons/exclamation-diamond.png"> <span
			style="font-size: 13px"><?=$_LANG->get('Benachrichtigungen')?></span></td>
		<td width="250" class="content_header" align="right">
		<?=$savemsg?>
		</td>
	</tr>
</table>
<br />

<div class="box1">
	<table id="notifytable" width="100%" cellpadding="0" cellspacing="0" class="stripe hover row-border order-column pointer">
		<thead>
			<tr>
				<th><?=$_LANG->get('ID')?></th>
				<th><?=$_LANG->get('Titel')?></th>
				<th><?=$_LANG->get('Datum')?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><?=$_LANG->get('ID')?></th>
				<th><?=$_LANG->get('Titel')?></th>
				<th><?=$_LANG->get('Datum')?></th>
			</tr>
		</tfoot>
	</table>
</div>