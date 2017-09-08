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
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.select.min.js"></script>

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

var selected = [];
var comb_colinv;
$(document).ready(function() {
    comb_colinv = $('#comb_colinv').DataTable( {
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/collectiveinvoice/collectiveinvoice.combine.dt.ajax.php",
        "paging": true,
		"stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
		"pageLength": 10,
		"dom": 'flrtip',
		"order": [[ 4, "desc" ]],
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
        "rowCallback": function( row, data ) {
            if ( $.inArray(data[0], selected) !== -1 ) {
                $(row).addClass('selected');
            }
        },
		"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Alle"] ],
		"columns": [ null, null, null, null, null, null ],
		"language": 
					{
                        "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/German.json"
					}
    } );

    $("#comb_colinv tbody td").live('click',function(){
        var aPos = $('#comb_colinv').dataTable().fnGetPosition(this);
        var aData = $('#comb_colinv').dataTable().fnGetData(aPos[0]);
        var id = aData[0];
        var index = $.inArray(id, selected);

        if ( index === -1 ) {
            selected.push( id );
        } else {
            selected.splice( index, 1 );
        }

        $(this).parent().toggleClass('selected');
        $('#selcount').text(selected.length.toString());
    } );

	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	$('#date_min').datepicker(
        {
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: 'dd.mm.yy',
            onSelect: function(selectedDate) {
                $('#ajax_date_min').val(moment($('#date_min').val(), "DD-MM-YYYY").unix());
                $('#comb_colinv').dataTable().fnDraw();
            }
	});
	$('#date_max').datepicker(
        {
            showOtherMonths: true,
            selectOtherMonths: true,
            dateFormat: 'dd.mm.yy',
            onSelect: function(selectedDate) {
                $('#ajax_date_max').val(moment($('#date_max').val(), "DD-MM-YYYY").unix()+86340);
                $('#comb_colinv').dataTable().fnDraw();
            }
	});
	$('#customer').change(function(){	
		$('#ajax_customer').val($(this).val()); 
		$('#comb_colinv').dataTable().fnDraw();
	})
	
} );

function resetSelected(){
    $('#comb_colinv > tbody  > tr').each(function(){
        if ($(this).hasClass('selected'))
            $(this).removeClass('selected');
    });
    selected = [];
    $('#selcount').text(selected.length.toString());
}

function combineNow(){
    if (getConfirmation('Vorgänge jetzt zusammenführen?')){
        var data = '';
        for (var i = 0; i < selected.length; i++) {
            data += '&comb_ids[]='+selected[i];
        }
        window.location.href = 'index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.combine.php&exec=combine'+data;
    }
}

function selectAll(){
    $('#comb_colinv > tbody  > tr').each(function(){
        $(this).addClass('selected');
        var id = $(this).find('td:eq(0)').text();
        var index = $.inArray(id, selected);

        if ( index === -1 ) {
            selected.push( id );
        }
        $('#selcount').text(selected.length.toString());
    });
}
</script>
 <div class="panel panel-default">
 	  <div class="panel-heading">
 			<h3 class="panel-title">
                <?=$_LANG->get('Vorg&auml;nge zusammenf&uuml;gen')?> (<span id="selcount">0</span> ausgewählt)
                <span class="pull-right">
                    <button class="btn btn-small btn-success" type="button" onclick="selectAll();">Alle Auswählen</button>
                    <button class="btn btn-small btn-info" type="button" onclick="resetSelected();">Zurücksetzen</button>
                    <button class="btn btn-small btn-warning" type="button" onclick="combineNow();">Zusammenführen</button>
                </span>
            </h3>
 	  </div>
 	  <div class="panel-body">
          <div class="panel panel-default">
              <div class="panel-body">
                  ** Bitte beachten Sie, es können nur Vorgänge zusammengeführt werden die noch nicht im Status "Erledigt" bzw. wo noch keine Rechnung geschrieben wurde!<br>
                  ** Zusammengeführte Vorgänge werden automatisch auf "Erledigt" gestellt.<br>
                  ** Es ist nicht möglich mehrere Sammelvorgänge zusammenzuführen.<br><br>
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
                      <input name="ajax_customer" id="ajax_customer" type="hidden"/>
                      <select id="customer" name="customer" onfocus="markfield(this,0)" onblur="markfield(this,1)" class="form-control">
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
              <div class="table-responsive">
                  <table id="comb_colinv" class="table table-hover" >
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
          </div>
 	  </div>
  </div>