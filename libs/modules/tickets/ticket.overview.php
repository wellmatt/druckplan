<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			25.06.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/schedule/schedule.class.php';

if ($_REQUEST["exec"]=="reset")
{
    unset($_SESSION['tkt_date_min']);
    unset($_SESSION['tkt_date_max']);
    unset($_SESSION['tkt_date_due_min']);
    unset($_SESSION['tkt_date_due_max']);
    unset($_SESSION['tkt_ajax_category']);
    unset($_SESSION['tkt_ajax_state']);
    unset($_SESSION['tkt_ajax_crtuser']);
    unset($_SESSION['tkt_ajax_assigned']);
    unset($_SESSION['tkt_ajax_showclosed']);
    unset($_SESSION['tkt_ajax_showdeleted']);
    unset($_SESSION['tkt_ajax_tourmarker']);
    unset($_SESSION['tkt_cl_date_min']);
    unset($_SESSION['tkt_cl_date_max']);
}

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
        "sAjaxSource": "libs/modules/tickets/ticket.dt.ajax.php",
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
                             "sPdfMessage": "Contilas - Articles"
                         },
                         "print"
                     ]
                 },
		"fnServerData": function ( sSource, aoData, fnCallback ) {
			var iMin = document.getElementById('ajax_date_min').value;
			var iMax = document.getElementById('ajax_date_max').value;
			var iMinDue = document.getElementById('ajax_date_due_min').value;
			var iMaxDue = document.getElementById('ajax_date_due_max').value;
			var category = document.getElementById('ajax_category').value;
			var state = document.getElementById('ajax_state').value;
			var crtuser = document.getElementById('ajax_crtuser').value;
			var assigned = document.getElementById('ajax_assigned').value;
			var showclosed = document.getElementById('ajax_showclosed').value;
			var showdeleted = document.getElementById('ajax_showdeleted').value;
			var tourmarker = document.getElementById('ajax_tourmarker').value;
			var iMin_cl = document.getElementById('ajax_cl_date_min').value;
			var iMax_cl = document.getElementById('ajax_cl_date_max').value;
		    aoData.push( { "name": "start", "value": iMin, } );
		    aoData.push( { "name": "end", "value": iMax, } );
		    aoData.push( { "name": "start_due", "value": iMinDue, } );
		    aoData.push( { "name": "end_due", "value": iMaxDue, } );
		    aoData.push( { "name": "category", "value": category, } );
		    aoData.push( { "name": "state", "value": state, } );
		    aoData.push( { "name": "crtuser", "value": crtuser, } );
		    aoData.push( { "name": "assigned", "value": assigned, } );
		    aoData.push( { "name": "showclosed", "value": showclosed, } );
		    aoData.push( { "name": "showdeleted", "value": showdeleted, } );
		    aoData.push( { "name": "tourmarker", "value": tourmarker, } );
		    aoData.push( { "name": "cl_start", "value": iMin_cl, } );
		    aoData.push( { "name": "cl_end", "value": iMax_cl, } );
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

	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	$('#date_min').datepicker(
		{
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'dd.mm.yy',
            showOn: "button",
            buttonImage: "images/icons/calendar-blue.png",
            buttonImageOnly: true,
            onSelect: function(selectedDate) {
                $('#ajax_date_min').val(moment($('#date_min').val(), "DD-MM-YYYY").unix());
                $.post("libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_date_min", "tkt_date_min": moment($('#date_min').val(), "DD-MM-YYYY").unix()});
            	$('#ticketstable').dataTable().fnDraw();
            }
	});
	$('#date_max').datepicker(
		{
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'dd.mm.yy',
            showOn: "button",
            buttonImage: "images/icons/calendar-blue.png",
            buttonImageOnly: true,
            onSelect: function(selectedDate) {
                $('#ajax_date_max').val(moment($('#date_max').val(), "DD-MM-YYYY").unix()+86340);
                $.post("libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_date_max", "tkt_date_max": moment($('#date_max').val(), "DD-MM-YYYY").unix()+86340});
            	$('#ticketstable').dataTable().fnDraw();
            }
	});
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	$('#date_cl_min').datepicker(
		{
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'dd.mm.yy',
            showOn: "button",
            buttonImage: "images/icons/calendar-blue.png",
            buttonImageOnly: true,
            onSelect: function(selectedDate) {
                $('#ajax_cl_date_min').val(moment($('#date_cl_min').val(), "DD-MM-YYYY").unix());
                $.post("libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_cl_date_min", "tkt_cl_date_min": moment($('#date_cl_min').val(), "DD-MM-YYYY").unix()});
            	$('#ticketstable').dataTable().fnDraw();
            }
	});
	$('#date_cl_max').datepicker(
		{
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'dd.mm.yy',
            showOn: "button",
            buttonImage: "images/icons/calendar-blue.png",
            buttonImageOnly: true,
            onSelect: function(selectedDate) {
                $('#ajax_cl_date_max').val(moment($('#date_cl_max').val(), "DD-MM-YYYY").unix()+86340);
                $.post("libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_cl_date_max", "tkt_cl_date_max": moment($('#date_cl_max').val(), "DD-MM-YYYY").unix()+86340});
            	$('#ticketstable').dataTable().fnDraw();
            }
	});
	$('#date_due_min').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
	            showOn: "button",
	            buttonImage: "images/icons/calendar-blue.png",
	            buttonImageOnly: true,
	            onSelect: function(selectedDate) {
	                $('#ajax_date_due_min').val(moment($('#date_due_min').val(), "DD-MM-YYYY").unix());
	                $.post("libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_date_due_min", "tkt_date_due_min": moment($('#date_due_min').val(), "DD-MM-YYYY").unix()});
	            	$('#ticketstable').dataTable().fnDraw();
	            }
		});
	$('#date_due_max').datepicker(
		{
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'dd.mm.yy',
            showOn: "button",
            buttonImage: "images/icons/calendar-blue.png",
            buttonImageOnly: true,
            onSelect: function(selectedDate) {
                $('#ajax_date_due_max').val(moment($('#date_due_max').val(), "DD-MM-YYYY").unix()+86340);
                $.post("libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_date_due_max", "tkt_date_due_max": moment($('#date_due_max').val(), "DD-MM-YYYY").unix()+86340});
            	$('#ticketstable').dataTable().fnDraw();
            }
	});

	$('#category').change(function(){	
	    $('#ajax_category').val($(this).val()); 
        $.post("libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_category", "tkt_ajax_category": $(this).val()});
	    $('#ticketstable').dataTable().fnDraw();
	})
	$('#state').change(function(){	
		$('#ajax_state').val($(this).val()); 
        $.post("libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_state", "tkt_ajax_state": $(this).val()});
		$('#ticketstable').dataTable().fnDraw();  
	})
	$('#crtuser').change(function(){	
		$('#ajax_crtuser').val($(this).val()); 
        $.post("libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_crtuser", "tkt_ajax_crtuser": $(this).val()});
		$('#ticketstable').dataTable().fnDraw(); 
	})
	$('#assigned').change(function(){	
		$('#ajax_assigned').val($(this).val()); 
        $.post("libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_assigned", "tkt_ajax_assigned": $(this).val()});
		$('#ticketstable').dataTable().fnDraw(); 
	})
	$('#showclosed').change(function(){	
		if ($('#showclosed').prop('checked')){
			$('#ajax_showclosed').val(1); 
			$('#ajax_showdeleted').val(0); 
			$('#showdeleted').prop('checked', false);
	        $.post("libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_showclosed", "tkt_ajax_showclosed": "1"});
		} else {
			$('#ajax_showclosed').val(0); 
			$('#date_cl_min').val('');
			$('#date_cl_max').val('');
			$('#ajax_date_cl_min').val('');
			$('#ajax_date_cl_max').val('');
	        $.post("libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_showclosed", "tkt_ajax_showclosed": "0"});
		}
		$('#tr_cl_dates').toggle();
		$('#ticketstable').dataTable().fnDraw(); 
	})
	$('#showdeleted').change(function(){	
		if ($('#showdeleted').prop('checked')){
			$('#tr_cl_dates').hide();
			$('#date_cl_min').val('');
			$('#date_cl_max').val('');
			$('#ajax_date_cl_min').val('');
			$('#ajax_date_cl_max').val('');
			$('#ajax_showclosed').val(0); 
			$('#ajax_showdeleted').val(1); 
			$('#showclosed').prop('checked', false);
	        $.post("libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_showdeleted", "tkt_ajax_showdeleted": "1"});
		} else {
			$('#ajax_showdeleted').val(0); 
	        $.post("libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_showdeleted", "tkt_ajax_showdeleted": "0"});
		}
		$('#ticketstable').dataTable().fnDraw(); 
	})
	$('#ajax_tourmarker').change(function(){	
		$('#ajax_tourmarker').val($(this).val()); 
        $.post("libs/modules/tickets/ticket.ajax.php", {"ajax_action": "setFilter_ajax_tourmarker", "tkt_ajax_tourmarker": $(this).val()});
		$('#ticketstable').dataTable().fnDraw(); 
	})


    var DELAY = 500, clicks = 0, timer = null;
    $("#ticketstable tbody td").live('click', function(e){

        clicks++;  //count clicks

        var aPos = $('#ticketstable').dataTable().fnGetPosition(this);
        var aData = $('#ticketstable').dataTable().fnGetData(aPos[0]);
        
        if(clicks === 1) {

            timer = setTimeout(function() {
                clicks = 0;             //after action performed, reset counter
                timer = null;
                window.location = 'index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid='+aData[0]; 
            }, DELAY);

        } else {

            clearTimeout(timer);    //prevent single-click action
            clicks = 0;             //after action performed, reset counter
            timer = null;
            var win = window.open('index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid='+aData[0], '_blank');
            win.focus();
        }

    })
    .on("dblclick", function(e){
        e.preventDefault();  //cancel system double-click event
    });
	
} );

function TicketTableRefresh()
{
	$('#ticketstable').dataTable().fnDraw(); 
}

</script>

<table width="100%">
	<tr>
		<td width="150" class="content_header"><img
			src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <span
			style="font-size: 13px"><?=$_LANG->get('Tickets')?></span></td>
		<td width="250" class="content_header" align="right">
		<?=$savemsg?>
		</td>
		<td class="content_header" align="right"><a
			href="index.php?page=<?=$_REQUEST['page']?>&exec=new"
			class="icon-link"><img src="images/icons/ticket--plus.png"> <span
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
                <input name="ajax_date_min" id="ajax_date_min" type="hidden" <?php if ($_SESSION['tkt_date_min']) echo 'value="'.$_SESSION['tkt_date_min'].'"';?> />  
                <input name="date_min" id="date_min" style="width:70px;" <?php if ($_SESSION['tkt_date_min']) echo 'value="'.date('d.m.Y',$_SESSION['tkt_date_min']).'"';?>  class="text" 
                onfocus="markfield(this,0)" onblur="markfield(this,1)" title="<?=$_LANG->get('von');?>">&nbsp;&nbsp;
            </td>
            <td valign="left">
                <input name="ajax_date_max" id="ajax_date_max" type="hidden" <?php if ($_SESSION['tkt_date_max']) echo 'value="'.$_SESSION['tkt_date_max'].'"';?> />  
                bis: <input name="date_max" id="date_max" style="width:70px;" <?php if ($_SESSION['tkt_date_max']) echo 'value="'.date('d.m.Y',$_SESSION['tkt_date_max']).'"';?> class="text" 
                onfocus="markfield(this,0)" onblur="markfield(this,1)" title="<?=$_LANG->get('bis');?>">&nbsp;&nbsp;
            </td>
        </tr>
        <tr align="left">
            <td>Datum (fällig):&nbsp;&nbsp;</td>
            <td valign="left">
                <input name="ajax_date_due_min" id="ajax_date_due_min" type="hidden" <?php if ($_SESSION['tkt_date_due_min']) echo 'value="'.$_SESSION['tkt_date_due_min'].'"';?> />  
                <input name="date_due_min" id="date_due_min" style="width:70px;" <?php if ($_SESSION['tkt_date_due_min']) echo 'value="'.date('d.m.Y',$_SESSION['tkt_date_due_min']).'"';?> class="text" 
                onfocus="markfield(this,0)" onblur="markfield(this,1)" title="<?=$_LANG->get('von');?>">&nbsp;&nbsp;
            </td>
            <td valign="left">
                <input name="ajax_date_due_max" id="ajax_date_due_max" type="hidden" <?php if ($_SESSION['tkt_date_due_max']) echo 'value="'.$_SESSION['tkt_date_due_max'].'"';?> />  
                bis: <input name="date_due_max" id="date_due_max" style="width:70px;" <?php if ($_SESSION['tkt_date_due_max']) echo 'value="'.date('d.m.Y',$_SESSION['tkt_date_due_max']).'"';?> class="text" 
                onfocus="markfield(this,0)" onblur="markfield(this,1)" title="<?=$_LANG->get('bis');?>">&nbsp;&nbsp;
            </td>
        </tr>
        <tr align="left">
            <td>Kategorie:&nbsp;&nbsp;</td>
            <td valign="left">
                <input name="ajax_category" id="ajax_category" type="hidden" <?php if ($_SESSION['tkt_ajax_category']) echo ' value="'.$_SESSION['tkt_ajax_category'].'" ';?>/>  
                <select name="category" id="category" style="width:160px">
                    <option value="" <?php if (!$_SESSION['tkt_ajax_category']) echo ' selected ';?>></option> 
                    <?php 
                    $tkt_all_categories = TicketCategory::getAllCategories();
                    foreach ($tkt_all_categories as $tkt_category){
                        if ($tkt_category->cansee())
                        {
                            echo '<option value="'.$tkt_category->getId().'"';
                            if ($_SESSION['tkt_ajax_category'] == $tkt_category->getId())
                            {
                                echo ' selected ';
                            }
                            echo '>'.$tkt_category->getTitle().'</option>';
                        }
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr align="left">
            <td>Status:&nbsp;&nbsp;</td>
            <td valign="left">
                <input name="ajax_state" id="ajax_state" type="hidden" <?php if ($_SESSION['tkt_ajax_state']) echo ' value="'.$_SESSION['tkt_ajax_state'].'" ';?>/>  
                <select name="state" id="state" style="width:160px">
                <option value="" <?php if (!$_SESSION['tkt_ajax_state']) echo ' selected ';?>></option> 
                <?php 
                $tkt_all_states = TicketState::getAllStates();
                foreach ($tkt_all_states as $tkt_state){
                    if ($tkt_state->getId() != 1){
                        echo '<option value="'.$tkt_state->getId().'"';
                        if ($_SESSION['tkt_ajax_state'] == $tkt_state->getId())
                        {
                            echo ' selected ';
                        }
                        echo '>'.$tkt_state->getTitle().'</option>';
                    }
                }
                ?>
                </select>
            </td>
        </tr>
        <tr align="left">
            <td>erst. von:&nbsp;&nbsp;</td>
            <td valign="left">
                <input name="ajax_crtuser" id="ajax_crtuser" type="hidden" <?php if ($_SESSION['tkt_ajax_crtuser']) echo ' value="'.$_SESSION['tkt_ajax_crtuser'].'" ';?>/>  
                <select name="crtuser" id="crtuser" style="width:160px">
                <option value="" <?php if (!$_SESSION['tkt_ajax_crtuser']) echo ' selected ';?>></option> 
                <?php 
                $all_user = User::getAllUser(User::ORDER_NAME);
                foreach ($all_user as $tkt_user){
                    echo '<option value="'.$tkt_user->getId().'"';
                    if ($_SESSION['tkt_ajax_crtuser'] == $tkt_user->getId())
                    {
                        echo ' selected ';
                    }
                    echo '>'.$tkt_user->getNameAsLine().'</option>';
                }
                ?>
                </select>
            </td>
        </tr>
        <tr align="left">
            <td>zugewiesen an:&nbsp;&nbsp;</td>
            <td valign="left">
                <input name="ajax_assigned" id="ajax_assigned" type="hidden" <?php if ($_SESSION['tkt_ajax_assigned']) echo ' value="'.$_SESSION['tkt_ajax_assigned'].'" ';?>/>  
                <select name="assigned" id="assigned" style="width:160px">
                <option value="" <?php if (!$_SESSION['tkt_ajax_assigned']) echo ' selected ';?>></option> 
                <option disabled>-- Users --</option>
                <?php 
                $all_user = User::getAllUser(User::ORDER_NAME);
                $all_groups = Group::getAllGroups(Group::ORDER_NAME);
                foreach ($all_user as $tkt_user){
                    echo '<option value="u_'.$tkt_user->getId().'"';
                    if ($_SESSION['tkt_ajax_assigned'] == 'u_'.$tkt_user->getId())
                    {
                        echo ' selected ';
                    }
                    echo '>'.$tkt_user->getNameAsLine().'</option>';
                }
                ?>
                <option disabled>-- Groups --</option>
                <?php 
                foreach ($all_groups as $tkt_groups){
                    echo '<option value="g_'.$tkt_groups->getId().'"';
                    if ($_SESSION['tkt_ajax_assigned'] == 'g_'.$tkt_groups->getId())
                    {
                        echo ' selected ';
                    }
                    echo '>'.$tkt_groups->getName().'</option>';
                }
                ?>
                </select>
            </td>
        </tr>
        <tr align="left">
            <td>Tourenmerkmal:&nbsp;&nbsp;</td>
            <td valign="left">
                <input name="ajax_tourmarker" id="ajax_tourmarker" type="text" <?php if ($_SESSION['tkt_ajax_tourmarker']) echo ' value="'.$_SESSION['tkt_ajax_tourmarker'].'" ';?>/>
            </td>
        </tr>
        <tr align="left" id="tr_cl_dates" <?php if (!$_SESSION['tkt_ajax_showclosed']) echo ' style="display: none" ';?>>
            <td>Datum (geschlossen):&nbsp;&nbsp;</td>
            <td valign="left">
                <input name="ajax_cl_date_min" id="ajax_cl_date_min" type="hidden" <?php if ($_SESSION['tkt_cl_date_min']) echo 'value="'.$_SESSION['tkt_cl_date_min'].'"';?> />  
                <input name="date_cl_min" id="date_cl_min" style="width:70px;" <?php if ($_SESSION['tkt_cl_date_min']) echo 'value="'.date('d.m.Y',$_SESSION['tkt_cl_date_min']).'"';?>  class="text" 
                onfocus="markfield(this,0)" onblur="markfield(this,1)" title="<?=$_LANG->get('von');?>">&nbsp;&nbsp;
            </td>
            <td valign="left">
                <input name="ajax_cl_date_max" id="ajax_cl_date_max" type="hidden" <?php if ($_SESSION['tkt_cl_date_max']) echo 'value="'.$_SESSION['tkt_cl_date_max'].'"';?> />  
                bis: <input name="date_cl_max" id="date_cl_max" style="width:70px;" <?php if ($_SESSION['tkt_cl_date_max']) echo 'value="'.date('d.m.Y',$_SESSION['tkt_cl_date_max']).'"';?> class="text" 
                onfocus="markfield(this,0)" onblur="markfield(this,1)" title="<?=$_LANG->get('bis');?>">&nbsp;&nbsp;
            </td>
        </tr>
        <tr align="left">
            <td>zeige geschlossene:&nbsp;&nbsp;</td>
            <td valign="left">
                <input name="ajax_showclosed" id="ajax_showclosed" type="hidden" <?php if ($_SESSION['tkt_ajax_showclosed']) echo ' value="'.$_SESSION['tkt_ajax_showclosed'].'" ';?>/>
                <input name="showclosed" id="showclosed" type="checkbox" value="1" <?php if ($_SESSION['tkt_ajax_showclosed']) echo ' checked ';?>/>
            </td>
        </tr>
        <?php if ($_USER->isAdmin()){?>
        <tr align="left">
            <td>zeige gelöschte:&nbsp;&nbsp;</td>
            <td valign="left">
                <input name="ajax_showdeleted" id="ajax_showdeleted" type="hidden" <?php if ($_SESSION['tkt_ajax_showdeleted']) echo ' value="'.$_SESSION['tkt_ajax_showdeleted'].'" ';?>/>
                <input name="showdeleted" id="showdeleted" type="checkbox" value="1" <?php if ($_SESSION['tkt_ajax_showdeleted']) echo ' checked ';?>/>
            </td>
        </tr>
        <?php } else {?>
                <input name="ajax_showdeleted" id="ajax_showdeleted" type="hidden" value="0"/>
                <input name="showdeleted" id="showdeleted" type="hidden" value="0"/>
        <?php }?>
        <tr align="left">
            <td><a onclick="TicketTableRefresh();" href="Javascript:"><img src="images/icons/arrow-circle-double-135.png"/> Refresh</a></td>
        </tr>
        <tr align="left">
            <td><a href="index.php?page=libs/modules/tickets/ticket.php&exec=reset"><img src="images/icons/slash.png"/> Reset</a></td>
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