<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       11.06.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/documents/document.class.php';
require_once 'libs/modules/personalization/personalization.order.class.php';
require_once 'libs/modules/personalization/personalization.class.php';
require_once 'libs/modules/warehouse/warehouse.class.php';

// error_reporting(-1);
// ini_set('display_errors', 1);

if ($_REQUEST["exec"]=="reset")
{
    unset($_SESSION['porder_date_min']);
    unset($_SESSION['porder_date_max']);
    unset($_SESSION['porder_customer']);
}

if((int)$_REQUEST["setStatus"] != 0){
	$perso_order = new Personalizationorder($_REQUEST["poid"]);
	$perso_order->setStatus((int)$_REQUEST["setStatus"]);
	$perso_order->save();
}

if($_REQUEST["exec"] == "delete"){
	
	$del_perso_order = new Personalizationorder((int)$_REQUEST["delid"]);
	$tmp_docs = Document::getDocuments(Array("type" => Document::TYPE_PERSONALIZATION_ORDER, 
										"requestId" => $del_perso_order->getId(), 
										 "module" => Document::REQ_MODULE_PERSONALIZATION));
	$hash = $tmp_docs[0]->getHash();
	$tmp_del = $del_perso_order->delete();
	// Wenn Datenbank angepasst ist, muss die Datei auch geloescht werden
	if($tmp_del){
		$tmp_filename1 = Personalizationorder::FILE_PATH.$_USER->getClient()->getId().".per_".$hash."_e.pdf";
		$tmp_filename2 = Personalizationorder::FILE_PATH.$_USER->getClient()->getId().".per_".$hash."_p.pdf";
		unlink($tmp_filename1);
		unlink($tmp_filename2);
	}
}

$customers = Personalizationorder::getAllCustomerWithOrders();
?>
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.tableTools.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    var art_table = $('#persoorder_table').DataTable( {
        // "scrollY": "600px",
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/personalization/personalization.order.dt.ajax.php",
        "paging": true,
		"stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
		"pageLength": <?php echo $perf->getDt_show_default();?>,
		"dom": 'T<"clear">flrtip',  
		"aaSorting": [[ 5, "desc" ]],      
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
 		"fnServerData": function ( sSource, aoData, fnCallback ) {
			var iMin = document.getElementById('ajax_date_min').value;
			var iMax = document.getElementById('ajax_date_max').value;
			var customer = document.getElementById('ajax_customer').value;
		    aoData.push( { "name": "start", "value": iMin, } );
		    aoData.push( { "name": "end", "value": iMax, } );
		    aoData.push( { "name": "customer", "value": customer, } );
		    $.getJSON( sSource, aoData, function (json) {
		        fnCallback(json)
		    } );
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
// 		            { "sortable": false },
		            { "sortable": false },
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

	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	$('#date_min').datepicker(
		{
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'dd.mm.yy',
//            showOn: "button",
//            buttonImage: "images/icons/calendar-blue.png",
//            buttonImageOnly: true,
            onSelect: function(selectedDate) {
                $('#ajax_date_min').val(moment($('#date_min').val(), "DD-MM-YYYY").unix());
                $.post("libs/modules/personalization/personalization.ajax.php", {"ajax_action": "setFilter_date_min", "porder_date_min": moment($('#date_min').val(), "DD-MM-YYYY").unix()});
            	$('#persoorder_table').dataTable().fnDraw();
            }
	});
	$('#date_max').datepicker(
		{
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'dd.mm.yy',
//			showOn: "button",
//			buttonImage: "images/icons/calendar-blue.png",
//			buttonImageOnly: true,
            onSelect: function(selectedDate) {
                $('#ajax_date_max').val(moment($('#date_max').val(), "DD-MM-YYYY").unix()+86340);
                $.post("libs/modules/personalization/personalization.ajax.php", {"ajax_action": "setFilter_date_max", "porder_date_max": moment($('#date_max').val(), "DD-MM-YYYY").unix()+86340});
            	$('#persoorder_table').dataTable().fnDraw();
            }
	});
	$('#customer').change(function(){	
		$('#ajax_customer').val($(this).val()); 
        $.post("libs/modules/personalization/personalization.ajax.php", {"ajax_action": "setFilter_ajax_customer", "porder_ajax_customer": $(this).val()});
		$('#persoorder_table').dataTable().fnDraw();  
	})
} );
function PersoOrderTableRefresh()
{
	$('#persoorder_table').dataTable().fnDraw(); 
}
</script>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				Bestellungen aus Web-to-Print
	  </div>
	  <div class="panel-body">
		  <div class="panel panel-default">
		  	  <div class="panel-heading">
		  			<h3 class="panel-title">Filter</h3>
		  	  </div>
		  	  <div class="panel-body">
				  <div class="form-horizontal">
						   <div class="form-group">
							   <label for="" class="col-sm-1 control-label">Datum</label>
							   <div class="col-sm-3">
								   <input name="ajax_date_min" id="ajax_date_min" type="hidden" <?php if ($_SESSION['porder_date_min']) echo 'value="'.$_SESSION['porder_date_min'].'"';?> />
								   <input name="date_min" id="date_min" <?php if ($_SESSION['porder_date_min']) echo 'value="'.date('d.m.Y',$_SESSION['porder_date_min']).'"';?>  class="form-control"
										  onfocus="markfield(this,0)" onblur="markfield(this,1)" title="<?=$_LANG->get('von');?>">
							   </div>
							   <label for="" class="col-sm-1 control-label">Bis:</label>
							   <div class="col-sm-3">
								   <input name="ajax_date_max" id="ajax_date_max" type="hidden" <?php if ($_SESSION['porder_date_max']) echo 'value="'.$_SESSION['porder_date_max'].'"';?> />
								   <input name="date_max" id="date_max"<?php if ($_SESSION['porder_date_max']) echo 'value="'.date('d.m.Y',$_SESSION['porder_date_max']).'"';?> class="form-control"
										  onfocus="markfield(this,0)" onblur="markfield(this,1)" title="<?=$_LANG->get('bis');?>">
							   </div>
						   </div>
					  <div class="form-group">
						  <label for="" class="col-sm-1 control-label">Kunde</label>
						  <div class="col-sm-7">
							  <input name="ajax_customer" id="ajax_customer" type="hidden" <?php if ($_SESSION['porder_customer']) echo ' value="'.$_SESSION['porder_customer'].'" ';?>/>
							  <select name="customer" id="customer" class="form-control">
								  <option value="" <?php if (!$_SESSION['porder_customer']) echo ' selected ';?>></option>
								  <?php
								  foreach ($customers as $customer){
									  echo '<option value="'.$customer->getId().'"';
									  if ($_SESSION['porder_customer'] == $customer->getId())
									  {
										  echo ' selected ';
									  }
									  echo '>'.$customer->getNameAsLine().'</option>';
								  }
								  ?>
							  </select>
						  </div>
					  </div>
					  <br>

					  <button class="btn btn-xs btn-success" onclick="PersoOrderTableRefresh();" href="Javascript:">
						  <span class="glyphicons glyphicons-refresh"></span>
						  <?= $_LANG->get('Refresh') ?>
					  </button>
					  <button class="btn btn-xs btn-warning" onclick="document.location.href='index.php?page=libs/modules/personalization/personalization.order.overview.php&exec=reset';">
						  <span class="glyphicons glyphicons-ban-circle"></span>
						  <?= $_LANG->get(' Reset') ?>
					  </button>
				  </div>
		  	  </div>
		  </div>
	  </div>
    </br>
<div class="table-responsive">
	<table id="persoorder_table" class="table table-hover">
			<thead>
				<tr>
					<th width="20"><?=$_LANG->get('ID')?></th>
					<th width="220"><?=$_LANG->get('Titel')?></th>
					<th width="180"><?=$_LANG->get('Verkn. Artikel')?></th>
					<th width="180"><?=$_LANG->get('Kunde')?></th>
					<th width="120"><?=$_LANG->get('Lagermenge')?></th>
					<th width="110"><?=$_LANG->get('Bestelldatum')?></th>
					<th width="110" align="right"><?=$_LANG->get('Bestellmenge')?></th>
					<?php /*<th width="130" align="center"><?=$_LANG->get('Status')?></th>*/?>
					<th width="40"><?=$_LANG->get('Kommentar')?></th>
					<th width="80"><?=$_LANG->get('Optionen')?></th>
				</tr>
			</thead>
		</table>
	</div>
</div>