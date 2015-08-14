<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			25.06.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once './libs/modules/schedule/schedule.class.php';
?>

<? // echo $DB->getLastError();?>

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="../../../css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="../../../css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="../../../css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/dataTables.tableTools.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/date-uk.js"></script>

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
        "sAjaxSource": "../../../libs/modules/tickets/ticket.dt.ajax.php?bcid=<?=$busicon->getId()?>&withoutdue=1&portal=1&userid=<?=$_USER->getId()?>&cpid=<?=$_CONTACTPERSON->getId();?>",
        "paging": true,
		"stateSave": true,
// 		"dom": 'flrtip',        
		"dom": 'T<"clear">flrtip',        
		"tableTools": {
			"sSwfPath": "../../../jscripts/datatable/copy_csv_xls_pdf.swf",
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
		"pageLength": 50,
		"fnServerData": function ( sSource, aoData, fnCallback ) {
			var iMin = document.getElementById('ajax_date_min').value;
			var iMax = document.getElementById('ajax_date_max').value;
			var iMinDue = document.getElementById('ajax_date_due_min').value;
			var iMaxDue = document.getElementById('ajax_date_due_max').value;
			var category = document.getElementById('ajax_category').value;
			var showclosed = document.getElementById('ajax_showclosed').value;
		    aoData.push( { "name": "start", "value": iMin, } );
		    aoData.push( { "name": "end", "value": iMax, } );
		    aoData.push( { "name": "start_due", "value": iMinDue, } );
		    aoData.push( { "name": "end_due", "value": iMaxDue, } );
		    aoData.push( { "name": "category", "value": category, } );
		    aoData.push( { "name": "showclosed", "value": showclosed, } );
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

    $("#ticketstable tbody td").live('click',function(){
        var aPos = $('#ticketstable').dataTable().fnGetPosition(this);
        var aData = $('#ticketstable').dataTable().fnGetData(aPos[0]);
        document.location='index.php?pid=20&exec=edit&tktid='+aData[0];
    });

	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	$('#date_min').datepicker(
		{
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'dd.mm.yy',
            showOn: "button",
            buttonImage: "../../../images/icons/calendar-blue.png",
            buttonImageOnly: true,
            onSelect: function(selectedDate) {
                $('#ajax_date_min').val(moment($('#date_min').val(), "DD-MM-YYYY").unix());
            	$('#ticketstable').dataTable().fnDraw();
            }
	});
	$('#date_max').datepicker(
		{
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'dd.mm.yy',
            showOn: "button",
            buttonImage: "../../../images/icons/calendar-blue.png",
            buttonImageOnly: true,
            onSelect: function(selectedDate) {
                $('#ajax_date_max').val(moment($('#date_max').val(), "DD-MM-YYYY").unix()+86340);
            	$('#ticketstable').dataTable().fnDraw();
            }
	});
	$('#date_due_min').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
	            showOn: "button",
	            buttonImage: "../../../images/icons/calendar-blue.png",
	            buttonImageOnly: true,
	            onSelect: function(selectedDate) {
	                $('#ajax_date_due_min').val(moment($('#date_due_min').val(), "DD-MM-YYYY").unix());
	            	$('#ticketstable').dataTable().fnDraw();
	            }
		});
	$('#date_due_max').datepicker(
		{
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'dd.mm.yy',
            showOn: "button",
            buttonImage: "../../../images/icons/calendar-blue.png",
            buttonImageOnly: true,
            onSelect: function(selectedDate) {
                $('#ajax_date_due_max').val(moment($('#date_due_max').val(), "DD-MM-YYYY").unix()+86340);
            	$('#ticketstable').dataTable().fnDraw();
            }
	});

	$('#category').change(function(){	
	    $('#ajax_category').val($(this).val()); 
	    $('#ticketstable').dataTable().fnDraw();
	})
	$('#state').change(function(){	
		$('#ajax_state').val($(this).val()); 
		$('#ticketstable').dataTable().fnDraw();  
	})
	$('#showclosed').change(function(){	
		if ($('#showclosed').prop('checked')){
			$('#ajax_showclosed').val(1); 
		} else {
			$('#ajax_showclosed').val(0); 
		}
		$('#ticketstable').dataTable().fnDraw(); 
	})
} );
</script>

<table width="100%">
	<tr>
		<td width="150" class="content_header"><span
			style="font-size: 13px"><?=$_LANG->get('Tickets')?></span></td>
		<td width="250" class="content_header" align="right">
		<?=$savemsg?>
		</td>
		<td class="content_header" align="right"><a
			href="index.php?pid=20&exec=new"
			class="icon-link"><img src="../../../images/icons/ticket--plus.png"> <span
				style="font-size: 13px"><?=$_LANG->get('Ticket erstellen')?></span></a>
		</td>
	</tr>
</table>
<br />

<div class="box2">
    <table>
        <tr align="left">
            <td>Datum (erstellt):&nbsp;&nbsp;</td>
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
        </tr>
        <tr align="left">
            <td>Datum (fällig):&nbsp;&nbsp;</td>
            <td valign="left">
                <input name="ajax_date_due_min" id="ajax_date_due_min" type="hidden"/>  
                <input name="date_due_min" id="date_due_min" style="width:70px;" class="text" 
                onfocus="markfield(this,0)" onblur="markfield(this,1)" title="<?=$_LANG->get('von');?>">&nbsp;&nbsp;
            </td>
            <td valign="left">
                <input name="ajax_date_due_max" id="ajax_date_due_max" type="hidden"/>  
                bis: <input name="date_due_max" id="date_due_max" style="width:70px;" class="text" 
                onfocus="markfield(this,0)" onblur="markfield(this,1)" title="<?=$_LANG->get('bis');?>">&nbsp;&nbsp;
            </td>
        </tr>
        <tr align="left">
            <td>Kategorie:&nbsp;&nbsp;</td>
            <td valign="left">
                <input name="ajax_category" id="ajax_category" type="hidden"/>  
                <select name="category" id="category" style="width:160px">
                    <option value="" selected></option> 
                    <?php 
                    $tkt_all_categories = TicketCategory::getAllCategories();
                    foreach ($tkt_all_categories as $tkt_category){
                        if ($_CONTACTPERSON->TC_cansee($tkt_category))
                            echo '<option value="'.$tkt_category->getId().'">'.$tkt_category->getTitle().'</option>';
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr align="left">
            <td>zeige geschlossene:&nbsp;&nbsp;</td>
            <td valign="left">
                <input name="ajax_showclosed" id="ajax_showclosed" type="hidden"/>
                <input name="showclosed" id="showclosed" type="checkbox" value="1"/>
            </td>
        </tr>
        <tr align="left">
            <td><a href="index.php?pid=20">Reset</a><img src="../../../images/icons/slash.png"/></td>
        </tr>
        </br>
    </table>
</div>
</br>

<div class="box1">
	<table id="ticketstable" width="100%" cellpadding="0" cellspacing="0" class="stripe hover row-border order-column">
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
			</tr>
		
		
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