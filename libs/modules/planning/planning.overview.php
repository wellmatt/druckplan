<?php


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
    var planning_table = $('#planning_table').DataTable( {
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/planning/planning.overview.dt.ajax.php",
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
                             "sPdfMessage": "Contilas - Planung - <?php echo $_USER->getNameAsLine();?>"
                         },
                         "print"
                     ]
                 },
		"lengthMenu": [ [10, 25, 50], [10, 25, 50] ],
		"aoColumnDefs": [ { "sType": "uk_date", "aTargets": [ 5 ] } ],
		"columns": [            
		    		{
                        "className":      'details-control',
                        "orderable":      false,
                        "data":           null,
                        "defaultContent": ''
                    },
		            null,
		            null,
		            null,
		            null,
		            null,
		            null,
		            null
		          ],
		"aaSorting": [[ 5, "desc" ]],
		"fnServerData": function ( sSource, aoData, fnCallback ) {
		    $.getJSON( sSource, aoData, function (json) {
		        fnCallback(json)
		    } );
		},
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
        	var res = aData[7].split("/");
        	var pl = parseInt(res[0]);
        	var jobs = parseInt(res[1]);
        	if (pl < jobs)
            	$(nRow).addClass('highlight');
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
    
    // Array to track the ids of the details displayed rows
    var detailRows = [];

    var DELAY = 500, clicks = 0, timer = null;
    $("#planning_table tbody td:not(:first-child)").live('click', function(e){

        clicks++;  //count clicks

        var aPos = $('#planning_table').dataTable().fnGetPosition(this);
        var aData = $('#planning_table').dataTable().fnGetData(aPos[0]);
        
        if(clicks === 1) {

            timer = setTimeout(function() {
                clicks = 0;             //after action performed, reset counter
                timer = null;
                window.location = 'index.php?page=libs/modules/planning/planning.job.php&id='+aData[1]; 
            }, DELAY);

        } else {

            clearTimeout(timer);    //prevent single-click action
            clicks = 0;             //after action performed, reset counter
            timer = null;
            var win = window.open('index.php?page=libs/modules/planning/planning.job.php&id='+aData[1], '_blank');
            win.focus();
        }

    })
    .on("dblclick", function(e){
        e.preventDefault();  //cancel system double-click event
    });
 
    $('#planning_table tbody').on( 'click', 'tr td:first-child', function () {
        var tr = $(this).closest('tr');
        var row = planning_table.row( tr );
        var idx = $.inArray( tr.attr('id'), detailRows );
        var control = $(this);
 
        if ( row.child.isShown() ) {
            tr.removeClass( 'details' );
            row.child.hide();
            detailRows.splice( idx, 1 );
        }
        else {
            tr.addClass( 'details' );
            $(this).addClass( 'details-control-loading' );
            get_child(row.data(),row,idx,tr,control);
        }
    } );
 
    // On each draw, loop over the `detailRows` array and show any child rows
    planning_table.on( 'draw', function () {
        $.each( detailRows, function ( i, id ) {
            $('#'+id+' td:first-child').trigger( 'click' );
        } );
    } );
    
} );

function get_child ( d,row,idx,tr,control ) {
	var body = $.ajax({
		type: "GET",
		url: "libs/modules/planning/planning.ajax.php",
		data: { "exec": "ajax_getJobDataForOverview", "id": d[1] },
		success: function(data) 
		    {
			    row.child( '<div class="box1">'+data+'</div>' ).show();
                $( ".details-control-loading" ).removeClass( 'details-control-loading' );
                if ( idx === -1 ) {
                    detailRows.push( tr.attr('id') );
                }
		    }
	});
}

function TableRefresh()
{
	$('#planning_table').dataTable().fnDraw(); 
}
</script>
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				Planungsübersicht
			</h3>
	  </div>
	  <div class="panel-body">
	  </div>

</br>

	<div class="table-responsive">
		<table id="planning_table"class="table table-hover">
			<thead>
				<tr>
					<th></th>
					<th><?=$_LANG->get('ID')?></th>
					<th><?=$_LANG->get('Auftrag')?></th>
					<th><?=$_LANG->get('Titel')?></th>
					<th><?=$_LANG->get('Kunde')?></th>
					<th><?=$_LANG->get('Fällig')?></th>
					<th><?=$_LANG->get('Bemerkung')?></th>
					<th><?=$_LANG->get('verpl. Jobs')?></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th></th>
					<th><?=$_LANG->get('ID')?></th>
					<th><?=$_LANG->get('Auftrag')?></th>
					<th><?=$_LANG->get('Titel')?></th>
					<th><?=$_LANG->get('Kunde')?></th>
					<th><?=$_LANG->get('Fällig')?></th>
					<th><?=$_LANG->get('Bemerkung')?></th>
					<th><?=$_LANG->get('verpl. Jobs')?></th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>