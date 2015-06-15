<?php
require_once 'libs/modules/search/search.class.php';

$query = urlencode($_REQUEST["mainsearch_string"]);

?>

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css"
	href="css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8"
	src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8"
	src="jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8"
	src="jscripts/datatable/dataTables.bootstrap.js"></script>
<script type="text/javascript" charset="utf8"
	src="jscripts/datatable/date-uk.js"></script>
	
<script type="text/javascript">
$(document).ready(function() {
// Start Tickets
    var search_tickets = $('#search_tickets').DataTable( {
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/search/search.ajax.php?exec=search_tickets&query=<?php echo $query;?>",
		"stateSave": false,
		"pageLength": 10,
		"dom": 'lrtip',
		"lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
		"columns": [
		            { "searchable": false, "orderable": false },
		            { "searchable": false, "orderable": false },
		            { "searchable": false, "orderable": false },
		            { "searchable": false, "orderable": false },
		            { "searchable": false, "orderable": false },
		            { "searchable": false, "orderable": false },
		            { "searchable": false, "orderable": false },
		            { "searchable": false, "orderable": false },
		            { "searchable": false, "orderable": false },
		            { "searchable": false, "orderable": false },
		            { "searchable": false, "orderable": false },
		            { "searchable": false, "orderable": false }
		          ],
        "language": {
                    	"url": "jscripts/datatable/German.json"
          	        },
        "fnDrawCallback": function (settings) 
                          {
            	           $("#search_tickets").parent().parent().toggle(settings.fnRecordsDisplay() > 0);
            	          }
    } );
    $("#search_tickets tbody td").live('click',function(){
        var aPos = $('#search_tickets').dataTable().fnGetPosition(this);
        var aData = $('#search_tickets').dataTable().fnGetData(aPos[0]);
        document.location='index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid='+aData[0];
    });
//-> End Tickets
// Start Comments
    var search_comments = $('#search_comments').DataTable( {
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/search/search.ajax.php?exec=search_comments&query=<?php echo $query;?>",
		"stateSave": false,
		"pageLength": 10,
		"dom": 'lrtip',
		"lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
		"columns": [
		            { "searchable": false, "orderable": false },
		            { "searchable": false, "orderable": false },
		            { "searchable": false, "orderable": false },
		            { "searchable": false, "orderable": false },
		            { "searchable": false, "orderable": false },
		            { "searchable": false, "orderable": false },
		            { "searchable": false, "orderable": false, "visible": false },
		            { "searchable": false, "orderable": false, "visible": false }
		          ],
        "language": {
                    	"url": "jscripts/datatable/German.json"
          	        },
        "fnDrawCallback": function (settings) 
                            {
                             $("#search_comments").parent().parent().toggle(settings.fnRecordsDisplay() > 0);
                            }
    } );
    $("#search_comments tbody td").live('click',function(){
        var aPos = $('#search_comments').dataTable().fnGetPosition(this);
        var aData = $('#search_comments').dataTable().fnGetData(aPos[0]);
        document.location=aData[7];
    });
//-> End Comments
} );
</script>

<div class="box1">
	<h3>Suchergebnisse für '<?php echo $_REQUEST["mainsearch_string"];?>':</h3>
	<div class="row">
		<div class="col-md-12">
	        <!-- start row -->
        	<div class="row">
        		<div class="col-md-12">
        			<div class="box1">
        	            <h4>Tickets</h4>
        				<table id="search_tickets" width="100%" cellpadding="0"
        					cellspacing="0" class="stripe hover row-border order-column">
        					<thead>
        						<tr>
                    				<th><?=$_LANG->get('ID')?></th>
                    				<th><?=$_LANG->get('#')?></th>
                    				<th><?=$_LANG->get('Kategorie')?></th>
                    				<th><?=$_LANG->get('Datum')?></th>
                    				<th><?=$_LANG->get('erst. von')?></th>
                    				<th><?=$_LANG->get('Fälligkeit')?></th>
                    				<th><?=$_LANG->get('Betreff')?></th>
                    				<th><?=$_LANG->get('Status')?></th>
                    				<th><?=$_LANG->get('Von')?></th>
                    				<th><?=$_LANG->get('Priorität')?></th>
                    				<th><?=$_LANG->get('Zugewiesen an')?></th>
        							<th><?=$_LANG->get('Relevanz')?></th>
        						</tr>
        					</thead>
        				</table>
        			</div>
    			</div>
			</div>
			</br>
	        <!-- end row -->
	        <!-- start row -->
        	<div class="row">
        		<div class="col-md-12">
        			<div class="box1">
        	            <h4>Kommentare</h4>
        				<table id="search_comments" width="100%" cellpadding="0"
        					cellspacing="0" class="stripe hover row-border order-column">
        					<thead>
        						<tr>
        							<th><?=$_LANG->get('ID')?></th>
        							<th><?=$_LANG->get('Kommentar')?></th>
                    				<th><?=$_LANG->get('Datum')?></th>
                    				<th><?=$_LANG->get('erst. von')?></th>
        							<th><?=$_LANG->get('Modul')?></th>
        							<th><?=$_LANG->get('Relevanz')?></th>
        						</tr>
        					</thead>
        				</table>
        			</div>
    			</div>
			</div>
	        <!-- end row -->
		</div>
	</div>
</div>