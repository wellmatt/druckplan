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
    var ticketstable = $('#ticketstable').DataTable( {
        // "scrollY": "600px",
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/tickets/ticket.dt.ajax.php?forme=<?php echo $_USER->getId();?>&withoutdue=1",
        "paging": true,
		"stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
		"pageLength": <?php echo $perf->getDt_show_default();?>,
		"aaSorting": [[ 5, "desc" ]],
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
                             "sPdfMessage": "Contilas - Tickets - Meine Tickets - <?php echo $_USER->getNameAsLine()?>"
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


    var DELAY = 500, clicks = 0, timer = null;
	$("#ticketstable tbody td:not(:nth-child(8))").live('click', function(e){

        clicks++;  //count clicks

        var aPos = $('#ticketstable').dataTable().fnGetPosition(this);
        var aData = $('#ticketstable').dataTable().fnGetData(aPos[0]);
        
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
	$("#ticketstable tbody td:nth-child(8)").live('click', function(e){
        var aPos = $('#ticketstable').dataTable().fnGetPosition(this);
        var aData = $('#ticketstable').dataTable().fnGetData(aPos[0]);
        var tktid = aData[0];

        callBoxFancyForMe("http://contilas2.mein-druckplan.de/libs/modules/tickets/ticket.summary.php?tktid="+tktid);
    });
	$("a#hiddenclickerforme").fancybox({
		'type'    : 'iframe',
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'padding'		:	25, 
		'margin'        :   25,
		'scrolling'     :   'no',
		'width'		    :	1000, 
		'onComplete'    :   function() {
                			  $('#fancybox-frame').load(function() { // wait for frame to load and then gets it's height
                		      $('#fancybox-content').height($(this).contents().find('body').height()+30);
                		      $('#fancybox-wrap').css('top','25px');
                		    });
                			},
		'overlayShow'	:	true,
		'helpers'		:   { overlay:null, closeClick:true }
	});
	function callBoxFancyForMe(my_href) {
		var j1 = document.getElementById("hiddenclickerforme");
		j1.href = my_href;
		$('#hiddenclickerforme').trigger('click');
	}
} );
</script>
<div id="hidden_clicker" style="display:none">
	<a id="hiddenclickerforme" href="http://www.google.com" >Hidden Clicker</a>
</div>
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				Meine Tickets
				<span class="pull-right">
					<img src="images/icons/ticket--plus.png">
					<button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=libs/modules/tickets/ticket.php&exec=new';">
						<?=$_LANG->get('Ticket erstellen') ?>
					</button>
				</span>
			</h3>
	  </div>
<br/>
	<div class="table-responsive">
	<table id="ticketstable" width="100%" cellpadding="0" cellspacing="0" class="stripe hover row-border order-column table-hover">
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
</div>