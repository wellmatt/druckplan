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
// Start Bcontacts
	var search_bcontacts = $('#search_bcontacts').DataTable( {
		"processing": true,
		"bServerSide": true,
		"sAjaxSource": "libs/modules/search/search.ajax.php?exec=search_businesscontacts&query=<?php echo $query;?>",
		"stateSave": false,
		"pageLength": 10,
		"dom": 'lrtip',
		"lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
		"columns": [
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
			if ($("#search_bcontacts > tbody > tr > .dataTables_empty")[0])
			{
				$("#search_bcontacts").parent().parent().parent().toggle(settings.fnRecordsDisplay() > 0);
			}
		}
	} );
	$("#search_bcontacts tbody td").live('click',function(){
		var aPos = $('#search_bcontacts').dataTable().fnGetPosition(this);
		var aData = $('#search_bcontacts').dataTable().fnGetData(aPos[0]);
		document.location='index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id='+aData[0];
	});
//-> End Bcontacts
// Start Cpersons
	var search_cpersons = $('#search_cpersons').DataTable( {
		"processing": true,
		"bServerSide": true,
		"sAjaxSource": "libs/modules/search/search.ajax.php?exec=search_contactpersons&query=<?php echo $query;?>",
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
			{ "searchable": false, "orderable": false }
		],
		"language": {
			"url": "jscripts/datatable/German.json"
		},
		"fnDrawCallback": function (settings)
		{
			if ($("#search_cpersons > tbody > tr > .dataTables_empty")[0])
			{
				$("#search_cpersons").parent().parent().parent().toggle(settings.fnRecordsDisplay() > 0);
			}
		}
	} );
	$("#search_cpersons tbody td").live('click',function(){
		var aPos = $('#search_cpersons').dataTable().fnGetPosition(this);
		var aData = $('#search_cpersons').dataTable().fnGetData(aPos[0]);
		document.location='index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit_cp&cpid='+aData[0];
	});
//-> End Cpersons
// Start Articles
	var search_articles = $('#search_articles').DataTable( {
		"processing": true,
		"bServerSide": true,
		"sAjaxSource": "libs/modules/search/search.ajax.php?exec=search_articles&query=<?php echo $query;?>",
		"stateSave": false,
		"pageLength": 10,
		"dom": 'lrtip',
		"lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
		"columns": [
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
			if ($("#search_articles > tbody > tr > .dataTables_empty")[0])
			{
				$("#search_articles").parent().parent().parent().toggle(settings.fnRecordsDisplay() > 0);
			}
		}
	} );
	$("#search_articles tbody td").live('click',function(){
		var aPos = $('#search_articles').dataTable().fnGetPosition(this);
		var aData = $('#search_articles').dataTable().fnGetData(aPos[0]);
		document.location='index.php?page=libs/modules/article/article.php&exec=edit&aid='+aData[0];
	});
//-> End Articles
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
							  if ($("#search_tickets > tbody > tr > .dataTables_empty")[0])
							  {
								  $("#search_tickets").parent().parent().parent().toggle(settings.fnRecordsDisplay() > 0);
							  }
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
								if ($("#search_comments > tbody > tr > .dataTables_empty")[0])
								{
									$("#search_comments").parent().parent().parent().toggle(settings.fnRecordsDisplay() > 0);
								}
                            }
    } );
    $("#search_comments tbody td").live('click',function(){
        var aPos = $('#search_comments').dataTable().fnGetPosition(this);
        var aData = $('#search_comments').dataTable().fnGetData(aPos[0]);
        document.location=aData[7];
    });
//-> End Comments
// Start Notes
    var search_notes = $('#search_notes').DataTable( {
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/search/search.ajax.php?exec=search_notes&query=<?php echo $query;?>&access=<?php if ($_USER->hasRightsByGroup(Permission::BC_NOTES) || $_USER->isAdmin()) echo '1'; else echo '0';?>&userid=<?php echo $_USER->getId();?>",
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
		            { "searchable": false, "orderable": false, "visible": false }
		          ],
        "language": {
                    	"url": "jscripts/datatable/German.json"
          	        },
        "fnDrawCallback": function (settings) 
                            {
								if ($("#search_notes > tbody > tr > .dataTables_empty")[0])
								{
									$("#search_notes").parent().parent().parent().toggle(settings.fnRecordsDisplay() > 0);
								}
                            }
    } );
    $("#search_notes tbody td").live('click',function(){
        var aPos = $('#search_notes').dataTable().fnGetPosition(this);
        var aData = $('#search_notes').dataTable().fnGetData(aPos[0]);
        callBoxFancytktc('libs/modules/comment/comment.edit.php?cid='+aData[0]+'&tktid=0');
    });
//-> End Comments
} );
</script>
	
<div id="tktc_hidden_clicker" style="display:none"><a id="tktc_hiddenclicker" href="http://www.google.com" >Hidden Clicker</a></div>

<script type="text/javascript">
function callBoxFancytktc(my_href) {
	var j1 = document.getElementById("tktc_hiddenclicker");
	j1.href = my_href;
	$('#tktc_hiddenclicker').trigger('click');
}

$(document).ready(function() {
    $("a#tktc_hiddenclicker").fancybox({
    	'type'          :   'iframe',
    	'transitionIn'	:	'elastic',
    	'transitionOut'	:	'elastic',
    	'speedIn'		:	600, 
    	'speedOut'		:	200, 
    	'width'         :   1024,
    	'height'		:	768, 
    	'overlayShow'	:	true,
    	'helpers'		:   { overlay:null, closeClick:true }
    });
} );
</script>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">Suchergebnisse für "<?php echo $_REQUEST["mainsearch_string"];?>"</h3>
	  </div>
	  <div class="panel-body">
		  <div class="panel panel-default">
			  <div class="panel-heading">
				  <h3 class="panel-title">Geschäftskontakte</h3>
			  </div>
			  <div class="table-responsive">
				  <table id="search_bcontacts" width="100%" cellpadding="0"
						 cellspacing="0" class="table table-hover stripe hover row-border order-column">
					  <thead>
					  <tr>
						  <th><?=$_LANG->get('ID')?></th>
						  <th><?=$_LANG->get('#')?></th>
						  <th><?=$_LANG->get('Matchcode')?></th>
						  <th><?=$_LANG->get('Name')?></th>
						  <th><?=$_LANG->get('Relevanz')?></th>
					  </tr>
					  </thead>
				  </table>
			  </div>
		  </div>
		  <div class="panel panel-default">
			  <div class="panel-heading">
				  <h3 class="panel-title">Ansprechpartner</h3>
			  </div>
			  <div class="table-responsive">
				  <table id="search_cpersons" width="100%" cellpadding="0"
						 cellspacing="0" class="table table-hover stripe hover row-border order-column">
					  <thead>
					  <tr>
						  <th><?=$_LANG->get('ID')?></th>
						  <th><?=$_LANG->get('Name')?></th>
						  <th><?=$_LANG->get('Firma')?></th>
						  <th><?=$_LANG->get('Tel.')?></th>
						  <th><?=$_LANG->get('eMail')?></th>
						  <th><?=$_LANG->get('Relevanz')?></th>
					  </tr>
					  </thead>
				  </table>
			  </div>
		  </div>
		  <div class="panel panel-default">
			  <div class="panel-heading">
				  <h3 class="panel-title">Artikel</h3>
			  </div>
			  <div class="table-responsive">
				  <table id="search_articles" width="100%" cellpadding="0"
						 cellspacing="0" class="table table-hover stripe hover row-border order-column">
					  <thead>
					  <tr>
						  <th><?=$_LANG->get('ID')?></th>
						  <th><?=$_LANG->get('#')?></th>
						  <th><?=$_LANG->get('Name')?></th>
						  <th><?=$_LANG->get('Relevanz')?></th>
					  </tr>
					  </thead>
				  </table>
			  </div>
		  </div>
		  <div class="panel panel-default">
		  	  <div class="panel-heading">
		  			<h3 class="panel-title">Tickets</h3>
		  	  </div>
			  <div class="table-responsive">
					<table id="search_tickets" width="100%" cellpadding="0"
						   cellspacing="0" class="table table-hover stripe hover row-border order-column">
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
		  <div class="panel panel-default">
		  	  <div class="panel-heading">
		  			<h3 class="panel-title">Kommentare</h3>
		  	  </div>
			  <div class="table-responsive">
				  <table id="search_comments" width="100%" cellpadding="0"
						 cellspacing="0" class="table table-hover stripe hover row-border order-column">
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
		  <div class="panel panel-default">
			  <div class="panel-heading">
				  <h3 class="panel-title">Notizen</h3>
			  </div>
			  <div class="table-responsive">
				  <table id="search_notes" width="100%" cellpadding="0"
						 cellspacing="0" class="table table-hover stripe hover row-border order-column">
					  <thead>
					  <tr>
						  <th><?=$_LANG->get('ID')?></th>
						  <th><?=$_LANG->get('Titel')?></th>
						  <th><?=$_LANG->get('Datum')?></th>
						  <th><?=$_LANG->get('erst. von')?></th>
						  <th><?=$_LANG->get('Kunde')?></th>
						  <th><?=$_LANG->get('Relevanz')?></th>
					  </tr>
					  </thead>
				  </table>
			  </div>
		  </div>
	  </div>
</div>