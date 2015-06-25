<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			05.01.2015
// Copyright:		2015 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

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
    var ticketstable = $('#ticketsabotable').DataTable( {
        // "scrollY": "600px",
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/tickets/ticket.dt.ajax.php?formeabo=<?php echo $_USER->getId();?>",
        "paging": true,
		"stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
		"pageLength": <?php echo $perf->getDt_show_default();?>,
		"aaSorting": [[ 5, "desc" ]],
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
                             "sPdfMessage": "Contilas - Tickets - Abonnements - <?php echo $_USER->getNameAsLine()?>"
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

//     $("#ticketstable tbody td").live('click',function(){
//         var aPos = $('#ticketstable').dataTable().fnGetPosition(this);
//         var aData = $('#ticketstable').dataTable().fnGetData(aPos[0]);
//         document.location='index.php?page=libs/modules/tickets/ticket.php&exec=edit&returnhome=1&tktid='+aData[0];
//     });


    var DELAY = 500, clicks = 0, timer = null;
	$("#ticketsabotable tbody td").live('click', function(e){

        clicks++;  //count clicks

        var aPos = $('#ticketsabotable').dataTable().fnGetPosition(this);
        var aData = $('#ticketsabotable').dataTable().fnGetData(aPos[0]);
        
        if(clicks === 1) {

            timer = setTimeout(function() {
                clicks = 0;             //after action performed, reset counter
                timer = null;
                window.location = 'index.php?page=libs/modules/tickets/ticket.php&exec=edit&returnhome=1&tktid='+aData[0]; 
            }, DELAY);

        } else {

            clearTimeout(timer);    //prevent single-click action
            clicks = 0;             //after action performed, reset counter
            timer = null;
            var win = window.open('index.php?page=libs/modules/tickets/ticket.php&exec=edit&returnhome=1&tktid='+aData[0], '_blank');
            win.focus();
        }

    })
    .on("dblclick", function(e){
        e.preventDefault();  //cancel system double-click event
    });
} );
</script>

<table width="100%">
	<tr>
		<td width="250" class="content_header">
			<img src="images/icons/clipboard-task.png"> 
			<span style="font-size: 13px"><?=$_LANG->get('Meine Abonnements')?></span> <small>(nicht direkt beteiligt)</small>
		</td>
		<td width="150" class="content_header" align="right">
		<?=$savemsg?>
		</td>
	</tr>
</table>


<br/>
<div class="box1">
	<table id="ticketsabotable" width="100%" cellpadding="0" cellspacing="0" class="stripe hover row-border order-column">
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
        </thead>
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