 <?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       16.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';

if ($_REQUEST["exec"] == "combine" && $_REQUEST["comb_ids"])
{
    $ids = Array();
    foreach ($_REQUEST["comb_ids"] as $id)
    {
        $ids[] = $id;
    }
    $ids = array_unique($ids);
    $comb_colinv = CollectiveInvoice::combineColInvs($ids);
    if ($comb_colinv !== false){
        echo '<script language="JavaScript">
              document.location.href = "index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid='.$comb_colinv->getId().'";
              </script>';
    }
}

$customers = CollectiveInvoice::getAllCustomerWithColInvs();
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
    var comb_colinv = $('#comb_colinv').DataTable( {
        // "scrollY": "1000px",
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/collectiveinvoice/collectiveinvoice.combine.dt.ajax.php",
        "paging": true,
		"stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
		"pageLength": <?php echo $perf->getDt_show_default();?>,
		"dom": 'T<"clear">flrtip',
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
			var customer = document.getElementById('ajax_customer').value;
		    aoData.push( { "name": "cust_id", "value": customer, } );
		    aoData.push( { "name": "start", "value": iMin, } );
		    aoData.push( { "name": "end", "value": iMax, } );
		    $.getJSON( sSource, aoData, function (json) {
		        fnCallback(json)
		    } );
		},
		"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Alle"] ],
		"columns": [
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

    $("#comb_colinv tbody td").live('click',function(){
        var aPos = $('#comb_colinv').dataTable().fnGetPosition(this);
        var aData = $('#comb_colinv').dataTable().fnGetData(aPos[0]);
        $("#sel_colinv").append('<span onclick="$(this).remove();">'+aData[1]+' - '+aData[3]+'<span class="glyphicons glyphicons-remove"></span></br>'+
                      		    '<input type="hidden" value="'+aData[0]+'" name="comb_ids[]"/></span>');
	    $('#btn_submit').show();
    });

	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	$('#date_min').datepicker(
        {
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: 'dd.mm.yy',
//            showOn: "button",
//			buttonImage: "images/icons/calendar-blue.svg",
//            buttonImageOnly: true,
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
//            showOn: "button",
//			buttonImage: "images/icons/calendar-blue.svg",
//            buttonImageOnly: true,
            onSelect: function(selectedDate) {
                $('#ajax_date_max').val(moment($('#date_max').val(), "DD-MM-YYYY").unix()+86340);
                $('#colinv').dataTable().fnDraw();
            }
	});
	$('#customer').change(function(){	
		$('#ajax_customer').val($(this).val()); 
		$('#comb_colinv').dataTable().fnDraw();  
	})
	
} );
</script>
 <div class="panel panel-default">
 	  <div class="panel-heading">
 			<h3 class="panel-title">
                <?=$_LANG->get('Vorg&auml;nge zusammenf&uuml;gen')?>
            </h3>
 	  </div>
 	  <div class="panel-body">

          <div class="panel panel-default">
              <div class="panel-heading">
                  <h3 class="panel-title">
                      Ausgewählte Vorgänge:<br>
                      <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="form_comb_colinv">
                          <input 	type="hidden" name="exec" value="combine">
                          <span id="sel_colinv"></span>
                          <br>
                          <br>
                          <input id="btn_submit" class="button" style="display: none" type="submit" value="Zusammenführen"/><br>
                          Kopfdaten werden vom ersten ausgewählten Vorgang übernommen!
                  </h3>
              </div>

                  <div class="panel-body">
                      <div class="form-group">

                              <label for="" class="col-sm-2 control-label">Datum Filter &nbsp;&nbsp;Von:</label>
                              <div class="col-sm-2">
                                  <input name="ajax_date_min" id="ajax_date_min" type="hidden"/>
                                  <input name="date_min" id="date_min" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)" title="<?=$_LANG->get('von');?>">&nbsp;&nbsp;
                              </div>

                              <label for="" class="col-sm-1 control-label">Bis:</label>
                              <div class="col-sm-2">
                                  <input name="ajax_date_max" id="ajax_date_max" type="hidden"/>
                                  <input name="date_max" id="date_max" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)" title="<?=$_LANG->get('bis');?>">&nbsp;&nbsp;
                              </div>
                        </div>
                      <label for="" class="col-sm-2 control-label">Kunde:</label>
                      <div class="col-sm-3">
                          <select id="filter_attrib" name="filter_attrib" onfocus="markfield(this,0)" onblur="markfield(this,1)" class="form-control">
                              <option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
                              <?php
                              foreach ($customers as $customer){
                                  echo '<option value="'.$customer->getId().'"';
                                  echo '>'.$customer->getNameAsLine().'</option>';
                              }
                              ?>
                          </select>
                      </div>

                  </div>


                  <br>
              <div class="table-responsive">
                  <table id="colinv" class="table table-hover" >
                      <thead>
                      <tr>
                          <th><?=$_LANG->get('ID')?></th>
                          <th><?=$_LANG->get('Nummer')?></th>
                          <th><?=$_LANG->get('Kunde')?></th>
                          <th><?=$_LANG->get('Titel')?></th>
                          <th><?=$_LANG->get('Angelegt am')?></th>
                          <th><?=$_LANG->get('Status')?></th>

                      </tr>
                      </thead>
                  </table>
              </div>

              <br>
 	  </div>
  </div>