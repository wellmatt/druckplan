<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			25.06.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/schedule/schedule.class.php';

?>

<? // echo $DB->getLastError();?>

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
    var ticketstable = $('#ticketstable').DataTable( {
        // "scrollY": "600px",
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/tickets/ticket.dt.ajax.php",
        "paging": true,
		"stateSave": true,
// 		"dom": 'flrtip',        
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
                             "sPdfMessage": "Contilas - Articles"
                         },
                         "print"
                     ]
                 },
		"pageLength": 50,
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

    $("#ticketstable tbody td").live('click',function(){
        var aPos = $('#ticketstable').dataTable().fnGetPosition(this);
        var aData = $('#ticketstable').dataTable().fnGetData(aPos[0]);
        document.location='index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid='+aData[0];
    });
} );
</script>

<table width="100%">
	<tr>
		<td width="150" class="content_header"><img
			src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <span
			style="font-size: 13px"><?=$_LANG->get('Tickets')?></span></td>
		<td width="250" class="content_header" align="right">
		<?=$savemsg?>
		</td>
		<td class="content_header" align="right"><a
			href="index.php?page=<?=$_REQUEST['page']?>&exec=new"
			class="icon-link"><img src="images/icons/ticket--plus.png"> <span
				style="font-size: 13px"><?=$_LANG->get('Ticket erstellen')?></span></a>
		</td>
	</tr>
</table>


<br />
<div class="box1">
	<table id="ticketstable" width="100%" cellpadding="0" cellspacing="0" class="stripe hover row-border order-column">
		<thead>
			<tr>
				<th><?=$_LANG->get('ID')?></th>
				<th><?=$_LANG->get('#')?></th>
				<th><?=$_LANG->get('Kategorie')?></th>
				<th><?=$_LANG->get('Datum')?></th>
				<th><?=$_LANG->get('erst. von')?></th>
				<th><?=$_LANG->get('Fällig')?></th>
				<th><?=$_LANG->get('Betreff')?></th>
				<th><?=$_LANG->get('Status')?></th>
				<th><?=$_LANG->get('Von')?></th>
				<th><?=$_LANG->get('Priorität')?></th>
				<th><?=$_LANG->get('Zugewiesen an')?></th>
			</tr>
		
		
		<tfoot>
			<tr>
				<th><?=$_LANG->get('ID')?></th>
				<th><?=$_LANG->get('#')?></th>
				<th><?=$_LANG->get('Kategorie')?></th>
				<th><?=$_LANG->get('Datum')?></th>
				<th><?=$_LANG->get('erst. von')?></th>
				<th><?=$_LANG->get('Fällig')?></th>
				<th><?=$_LANG->get('Betreff')?></th>
				<th><?=$_LANG->get('Status')?></th>
				<th><?=$_LANG->get('Von')?></th>
				<th><?=$_LANG->get('Priorität')?></th>
				<th><?=$_LANG->get('Zugewiesen an')?></th>
			</tr>
		</tfoot>
	</table>
</div>