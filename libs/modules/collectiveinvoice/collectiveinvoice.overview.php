<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       16.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

require_once ('libs/modules/businesscontact/attribute.class.php');
$all_attributes = Attribute::getAllAttributesForCollectiveinvoice();

?>
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.tableTools.js"></script>

<script type="text/javascript" charset="utf8" src="jscripts/moment/moment-with-locales.min.js"></script>
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
    var colinv = $('#colinv').DataTable( {
        // "scrollY": "1000px",
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/collectiveinvoice/collectiveinvoice.dt.ajax.php<?php if ($_REQUEST['cust_id']) echo '?cust_id='.$_REQUEST["cust_id"];?>",
        "paging": true,
		"stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
		"pageLength": <?php echo $perf->getDt_show_default();?>,
		"dom": 'T<"clear">flrtip',
		"aaSorting": [[ 4, "desc" ]],
		"order": [[ 4, "desc" ]],
		"tableTools": {
			"sSwfPath": "jscripts/datatable/copy_csv_xls_pdf.swf",
            "aButtons": [
                         "copy",
                         "csv",
                         "xls",
                         {
                             "sExtends": "pdf",
                             "sPdfOrientation": "landscape",
                             "sPdfMessage": "Contilas - Orders"
                         },
                         "print"
                     ]
                 },
		"fnServerData": function ( sSource, aoData, fnCallback ) {
			var iMin = document.getElementById('ajax_date_min').value;
			var iMax = document.getElementById('ajax_date_max').value;
			aoData.push( { "name": "filter_attrib", "value": $('#filter_attrib').val() } );
		    aoData.push( { "name": "start", "value": iMin, } );
		    aoData.push( { "name": "end", "value": iMax, } );
		    $.getJSON( sSource, aoData, function (json) {
		        fnCallback(json)
		    } );
		},
		"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Alle"] ],
		"aoColumnDefs": [ { "sType": "uk_date", "aTargets": [ 4 ] } ],
		"columns": [
		            null,
		            null,
		            null,
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

    $("#colinv tbody td:not(:last-child)").live('click',function(){
        var aPos = $('#colinv').dataTable().fnGetPosition(this);
        var aData = $('#colinv').dataTable().fnGetData(aPos[0]);
    	document.location='index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid='+aData[0];
    });
	$('#filter_attrib').on("change", function () {
		$('#colinv').dataTable().fnDraw();
	});

	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	$('#date_min').datepicker(
		{
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'dd.mm.yy',
            showOn: "button",
			buttonImage: "images/icons/calendar-blue.svg",
            buttonImageOnly: true,
            onSelect: function(selectedDate) {
                $('#ajax_date_min').val(moment($('#date_min').val(), "DD-MM-YYYY").unix());
            	$('#colinv').dataTable().fnDraw();
            }
	});
	$('#date_max').datepicker(
		{
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'dd.mm.yy',
            showOn: "button",
			buttonImage: "images/icons/calendar-blue.svg",
            buttonImageOnly: true,
            onSelect: function(selectedDate) {
                $('#ajax_date_max').val(moment($('#date_max').val(), "DD-MM-YYYY").unix()+86340);
            	$('#colinv').dataTable().fnDraw();
            }
	});
	
} );
</script>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
			Vorgänge
				<?php if ($_USER->isAdmin() || $_USER->hasRightsByGroup(Group::RIGHT_COMBINE_COLINV)){?>
				<span class="pull-right">
					<button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.combine.php';">
						<span class="glyphicons glyphicons-paired"></span>
						<?=$_LANG->get('Vorgänge zusammenführen') ?>
					</button>
				</span>
				<?php }?>

				<span class="pull-right">
					<button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=select_user';">
						<span class="glyphicons glyphicons-plus"></span>
						<?=$_LANG->get('Vorgang hinzuf&uuml;gen') ?>
					</button>
				</span>
		</h3>
	</div>
	<div class="panel-body">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					Filter
				</h3>
			</div>
			<div class="panel-body">
				<table>
					<tr align="left">
						<td>Datum Filter:&nbsp;&nbsp;</td>
						<td valign="left">
							<input name="ajax_date_min" id="ajax_date_min" type="hidden"/>
							<input name="date_min" id="date_min" style="width:70px;" class="text"
								   onfocus="markfield(this,0)" onblur="markfield(this,1)" title="<?=$_LANG->get('von');?>">&nbsp;&nbsp;
						</td>
						<td valign="left">
							<input name="ajax_date_max" id="ajax_date_max" type="hidden"/>
							bis: <input name="date_max" id="date_max" style="width:70px;" class="text"
										onfocus="markfield(this,0)" onblur="markfield(this,1)" title="<?=$_LANG->get('bis');?>">&nbsp;&nbsp;
						</td>
						<td valign="left">
							Merkmal-Filter: <select id="filter_attrib" name="filter_attrib" style="width:110px"	onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
								<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
								<?
								foreach ($all_attributes AS $attribute){
									$allitems = $attribute->getItems();
									foreach ($allitems AS $item){ ?>
										<option value="<?=$attribute->getId()?>|<?=$item["id"]?>"><?=$item["title"]?></option>
									<? }
								} ?>
							</select>
						</td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	<div class="table-responsive">
		<table id="colinv" class="table table-hover">
			<thead>
			<tr>
				<th><?=$_LANG->get('ID')?></th>
				<th><?=$_LANG->get('Nummer')?></th>
				<th><?=$_LANG->get('Kunde')?></th>
				<th><?=$_LANG->get('Titel')?></th>
				<th><?=$_LANG->get('Angelegt am')?></th>
				<th><?=$_LANG->get('Status')?></th>
				<th><?=$_LANG->get('Optionen')?></th>
			</tr>
			</thead>
		</table>
	</div>
</div>
