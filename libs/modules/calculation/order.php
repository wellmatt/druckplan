<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       16.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------


//if ($_USER->getLogin() != "ascherer") {
//    ob_clean();
//    die('Kalkulationen sind im Wartungsmodus (18.10.2016 by A.Scherer)');
//}

require_once 'libs/modules/tickets/ticket.class.php';
require_once 'order.class.php';
$_REQUEST["id"] = (int)$_REQUEST["id"];


if($_REQUEST["setStatus"] != "")
{
    $order = new Order((int)$_REQUEST["id"]);
    $order->setStatus((int)$_REQUEST["setStatus"]);
    $savemsg = getSaveMessage($order->save());
}

if($_REQUEST["exec"] == "export")
{
    $order = new Order((int)$_REQUEST["id"]);
    $csv_file = fopen('./docs/calc/order-'.$order->getId().'.csv', "w");
    $csv_string .= " ;\n";
    $csv_string .= " Kunde;\n";
    $csv_string .= " ".$order->getCustomer()->getNameAsLine().";\n";
    $csv_string .= " ;\n";
    $csv_string .= " Produkt;\n";
    $csv_string .= " ".$order->getProduct()->getName().";\n";
    $csv_string .= " ;\n";
    $csv_string .= " Titel;\n";
    $csv_string .= " ".$order->getTitle().";\n";
    $csv_string .= " ;\n";
    $csv_string .= " ;\n";
    $csv_string .= " ;\n";
    $csv_string .= " Titel; Format; Auflage; Inhalt; Zus. Inhalt 1; Zus. Inhalt 2; Zus. Inhalt 3; Endpreis; Stueckpreis;\n";
    $tmp_csv_calcs = Calculation::getAllCalculations($order);
    foreach ($tmp_csv_calcs as $csv_calc){
        $csv_string .= " ".$csv_calc->getTitle()."; ".$csv_calc->getProductFormatWidth()." x ".$csv_calc->getProductFormatHeight()." mm (".$csv_calc->getProductFormat()->getName().");". 
                       $csv_calc->getAmount()."; ".$csv_calc->getPaperContent()->getName()." ".$csv_calc->getPaperContent()->getSelectedWeight()." g; ".
                       $csv_calc->getPaperAddContent()->getName()." ".$csv_calc->getPaperAddContent()->getSelectedWeight() ."g;".
                       $csv_calc->getPaperAddContent2()->getName()." ".$csv_calc->getPaperAddContent2()->getSelectedWeight()."g;".
                       $csv_calc->getPaperAddContent3()->getName()." ".$csv_calc->getPaperAddContent3()->getSelectedWeight()."g;".
                       printPrice($csv_calc->getSummaryPrice())."; ".printPrice($csv_calc->getSummaryPrice() / $csv_calc->getAmount()).";\n";
    }
    $csv_string = iconv('UTF-8', 'ISO-8859-1', $csv_string);
    fwrite($csv_file, $csv_string);
    fclose($csv_file);
    echo '<script type="text/javascript">document.location.href = "/docs/calc/order-'.$order->getId().'.csv";</script>';
}

if($_REQUEST["exec"] == "delete")
{
    $order = new Order($_REQUEST["id"]);
    $order->delete();
}

if(($_REQUEST["exec"] == "copy" && !$_REQUEST['cust_id']) || ($_REQUEST["exec"] == "edit" && !$_REQUEST['cust_id']))
{
    require_once 'order.edit.php';
} else if($_REQUEST["exec"] == "new")
{
    require_once 'order.new.php';
} else
{


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
    var orders = $('#orders').DataTable( {
        // "scrollY": "1000px",
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/calculation/calculation.dt.ajax.php<?php if ($_REQUEST['cust_id']) echo '?cust_id='.$_REQUEST["cust_id"];?>",
        "paging": true,
		"stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
		"pageLength": <?php echo $perf->getDt_show_default();?>,
        "dom": 'T<"clear">lrtip',
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
            var product = document.getElementById('ajax_product').value;
		    aoData.push( { "name": "start", "value": iMin, } );
		    aoData.push( { "name": "end", "value": iMax, } );
            aoData.push( { "name": "product", "value": product, } );
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
    $('#search').keyup(function(){
        orders.search( $(this).val() ).draw();
    })

    $("#orders tbody td").live('click',function(){
        var aPos = $('#orders').dataTable().fnGetPosition(this);
        var aData = $('#orders').dataTable().fnGetData(aPos[0]);
        document.location='index.php?page=libs/modules/calculation/order.php&exec=edit&id='+aData[0]+'&step=4';
    });

	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	$('#date_min').datepicker(
		{
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'dd.mm.yy',
            onSelect: function(selectedDate) {
                $('#ajax_date_min').val(moment($('#date_min').val(), "DD-MM-YYYY").unix());
            	$('#orders').dataTable().fnDraw();
            }
	});
	$('#date_max').datepicker(
		{
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'dd.mm.yy',
            onSelect: function(selectedDate) {
                $('#ajax_date_max').val(moment($('#date_max').val(), "DD-MM-YYYY").unix()+86340);
            	$('#orders').dataTable().fnDraw();
            }
	});
	
} );
</script>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Kalkulationen
				<span class="pull-right">
					<button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=<?=$_REQUEST['page'] ?>&exec=new';">
                        <span class="glyphicons glyphicons-plus"></span>
                        <?=$_LANG->get('Kalkulation hinzuf&uuml;gen') ?>
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
                <div class="form-horizontal">
                    <input name="ajax_date_min" id="ajax_date_min" type="hidden"/>
                    <input name="ajax_date_max" id="ajax_date_max" type="hidden"/>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Datum &nbsp; Von:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" name="date_min" id="date_min" placeholder="">
                        </div>
                        <label for="" class="col-sm-1 control-label">Bis:</label>
                        <div class="col-sm-2">
                            <input type="text" class="form-control" name="date_max" id="date_max" placeholder="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Produkt:</label>
                        <div class="col-sm-5">
                            <select name="ajax_product" id="ajax_product" class="form-control" onchange="$('#orders').dataTable().fnDraw();">
                                <option value="0">-- Alle --</option>
                                <?php
                                foreach (Product::getAllProducts() as $item) {
                                    echo '<option value="' . $item->getId() . '">' . $item->getName() . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Suche</label>
                        <div class="col-sm-5">
                            <input type="text" id="search" class="form-control" placeholder="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </br>
    <div class="table-responsive">
        <table id="orders" class="table table-hover">
	        <thead>
                <tr>
                    <th><?=$_LANG->get('ID')?></th>
                    <th><?=$_LANG->get('Nummer')?></th>
                    <th><?=$_LANG->get('Titel')?></th>
                    <th><?=$_LANG->get('Produkt')?></th>
                    <th><?=$_LANG->get('Angelegt am')?></th>
                    <th><?=$_LANG->get('Optionen')?></th>
                </tr>
	        </thead>
        </table>
    </div>
</div>
<? } ?>