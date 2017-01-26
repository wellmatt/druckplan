<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			05.01.2015
// Copyright:		2015 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

if ($from_busicon){
    if ($notes_only){
        $ajax_url = "libs/modules/tickets/ticket.dt.ajax.php?notes_only=1&bcid=".$contactID;
    } else {
        $ajax_url = "libs/modules/tickets/ticket.dt.ajax.php?bcid=".$contactID;
    }
} elseif ($from_cc){
    $ajax_url = "libs/modules/tickets/ticket.dt.ajax.php?ccid=".$contactID;
}

?>


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
        "sAjaxSource": '<?php echo $ajax_url;?>',
		"stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
		"pageLength": <?php echo $perf->getDt_show_default();?>,
		"stateSave": true,
// 		"dom": 'flrtip',        
		"dom": 'T<"clear">lrtip',
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
		    aoData.push( { "name": "start", "value": iMin, } );
		    aoData.push( { "name": "end", "value": iMax, } );
		    aoData.push( { "name": "start_due", "value": iMinDue, } );
		    aoData.push( { "name": "end_due", "value": iMaxDue, } );
		    aoData.push( { "name": "category", "value": category, } );
		    aoData.push( { "name": "state", "value": state, } );
		    aoData.push( { "name": "crtuser", "value": crtuser, } );
		    aoData.push( { "name": "assigned", "value": assigned, } );
		    aoData.push( { "name": "showclosed", "value": showclosed, } );
		    aoData.push( { "name": "withoutdue", "value": "1", } );
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
    $('#search').keyup(function(){
        ticketstable.search( $(this).val() ).draw();
    })

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
            	$('#ticketstable').dataTable().fnDraw();
            }
	});
	$('#date_max').datepicker(
		{
			showOtherMonths: true,
			selectOtherMonths: true,
			dateFormat: 'dd.mm.yy',
//            showOn: "button",
//            buttonImage: "images/icons/calendar-blue.png",
//            buttonImageOnly: true,
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
//	            showOn: "button",
//	            buttonImage: "images/icons/calendar-blue.png",
//	            buttonImageOnly: true,
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
//            showOn: "button",
//            buttonImage: "images/icons/calendar-blue.png",
//            buttonImageOnly: true,
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
	$('#crtuser').change(function(){	
		$('#ajax_crtuser').val($(this).val()); 
		$('#ticketstable').dataTable().fnDraw(); 
	})
	$('#assigned').change(function(){	
		$('#ajax_assigned').val($(this).val()); 
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
	$('#ajax_tourmarker').change(function(){	
		$('#ajax_tourmarker').val($(this).val()); 
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
</script>
<?php
if ($from_busicon){
    $tmp_bc = new BusinessContact((int)$contactID);
    $main_contact = ContactPerson::getMainContact($tmp_bc);
    if ($tmp_bc->getId()>0 && $main_contact->getId()>0)
        $link = 'index.php?page=libs/modules/tickets/ticket.php&exec=new&customer='.$tmp_bc->getId().'&contactperson='.$main_contact->getId().'';
    else
        $link = 'index.php?page=libs/modules/tickets/ticket.php&exec=new"';
} else {
    $link = 'index.php?page=libs/modules/tickets/ticket.php&exec=new"';
}
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Verknüpfte Tickets
                <span class="pull-right">
                    <?= $savemsg ?>
                    <button class="btn btn-xs btn-success" type="button"
                            onclick="document.location.href='<?php echo $link; ?>';">
                        <span class="glyphicons glyphicons-plus"></span>
                        <?= $_LANG->get('Ticket erstellen') ?>
                    </button>
                </span>
        </h3>
    </div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Filter</h3>
            </div>
            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Datum (erstellt)</label>
                        <div class="col-sm-4">
                            <input name="ajax_date_min" id="ajax_date_min"
                                   type="hidden" <?php if ($_SESSION['tkt_date_min']) echo 'value="' . $_SESSION['tkt_date_min'] . '"'; ?> />
                            <input name="date_min" id="date_min"
                                <?php if ($_SESSION['tkt_date_min']) echo 'value="' . date('d.m.Y', $_SESSION['tkt_date_min']) . '"'; ?>
                                   class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                        </div>
                        <label for="" class="col-sm-2 control-label">Bis:</label>
                        <div class="col-sm-4">
                            <input name="ajax_date_max" id="ajax_date_max"
                                   type="hidden" <?php if ($_SESSION['tkt_date_max']) echo 'value="' . $_SESSION['tkt_date_max'] . '"'; ?> />
                            <input name="date_max" id="date_max"
                                <?php if ($_SESSION['tkt_date_max']) echo 'value="' . date('d.m.Y', $_SESSION['tkt_date_max']) . '"'; ?>
                                   class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Datum (fällig)</label>
                        <div class="col-sm-4">
                            <input name="ajax_date_due_min" id="ajax_date_due_min"
                                   type="hidden" <?php if ($_SESSION['tkt_date_due_min']) echo 'value="' . $_SESSION['tkt_date_due_min'] . '"'; ?> />
                            <input name="date_due_min" id="date_due_min"
                                <?php if ($_SESSION['tkt_date_due_min']) echo 'value="' . date('d.m.Y', $_SESSION['tkt_date_due_min']) . '"'; ?>
                                   class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                        </div>
                        <label for="" class="col-sm-2 control-label">Bis:</label>
                        <div class="col-sm-4">
                            <input name="ajax_date_due_max" id="ajax_date_due_max"
                                   type="hidden" <?php if ($_SESSION['tkt_date_due_max']) echo 'value="' . $_SESSION['tkt_date_due_max'] . '"'; ?> />
                            <input name="date_due_max" id="date_due_max"
                                <?php if ($_SESSION['tkt_date_due_max']) echo 'value="' . date('d.m.Y', $_SESSION['tkt_date_due_max']) . '"'; ?>
                                   class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Kategorie</label>
                        <div class="col-sm-10">
                            <input name="ajax_category" id="ajax_category"
                                   type="hidden" <?php if ($_SESSION['tkt_ajax_category']) echo ' value="' . $_SESSION['tkt_ajax_category'] . '" '; ?>/>
                            <select name="category" id="category" class="form-control">
                                <option
                                    value="" <?php if (!$_SESSION['tkt_ajax_category']) echo ' selected '; ?>></option>
                                <?php
                                $tkt_all_categories = TicketCategory::getAllCategories();
                                foreach ($tkt_all_categories as $tkt_category) {
                                    if ($tkt_category->cansee()) {
                                        echo '<option value="' . $tkt_category->getId() . '"';
                                        if ($_SESSION['tkt_ajax_category'] == $tkt_category->getId()) {
                                            echo ' selected ';
                                        }
                                        echo '>' . $tkt_category->getTitle() . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-10">
                            <input name="ajax_state" id="ajax_state"
                                   type="hidden" <?php if ($_SESSION['tkt_ajax_state']) echo ' value="' . $_SESSION['tkt_ajax_state'] . '" '; ?>/>
                            <select name="state" id="state" class="form-control">
                                <option value="" <?php if (!$_SESSION['tkt_ajax_state']) echo ' selected '; ?>></option>
                                <?php
                                $tkt_all_states = TicketState::getAllStates();
                                foreach ($tkt_all_states as $tkt_state) {
                                    if ($tkt_state->getId() != 1) {
                                        echo '<option value="' . $tkt_state->getId() . '"';
                                        if ($_SESSION['tkt_ajax_state'] == $tkt_state->getId()) {
                                            echo ' selected ';
                                        }
                                        echo '>' . $tkt_state->getTitle() . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">erst.von</label>
                        <div class="col-sm-10">
                            <input name="ajax_crtuser" id="ajax_crtuser"
                                   type="hidden" <?php if ($_SESSION['tkt_ajax_crtuser']) echo ' value="' . $_SESSION['tkt_ajax_crtuser'] . '" '; ?>/>
                            <select name="crtuser" id="crtuser" class="form-control">
                                <option
                                    value="" <?php if (!$_SESSION['tkt_ajax_crtuser']) echo ' selected '; ?>></option>
                                <?php
                                $all_user = User::getAllUser(User::ORDER_NAME);
                                foreach ($all_user as $tkt_user) {
                                    echo '<option value="' . $tkt_user->getId() . '"';
                                    if ($_SESSION['tkt_ajax_crtuser'] == $tkt_user->getId()) {
                                        echo ' selected ';
                                    }
                                    echo '>' . $tkt_user->getNameAsLine() . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">zugewiesen an</label>
                        <div class="col-sm-10">
                            <input name="ajax_assigned" id="ajax_assigned"
                                   type="hidden" <?php if ($_SESSION['tkt_ajax_assigned']) echo ' value="' . $_SESSION['tkt_ajax_assigned'] . '" '; ?>/>
                            <select name="assigned" id="assigned" class="form-control">
                                <option
                                    value="" <?php if (!$_SESSION['tkt_ajax_assigned']) echo ' selected '; ?>></option>
                                <option disabled>-- Users --</option>
                                <?php
                                $all_user = User::getAllUser(User::ORDER_NAME);
                                $all_groups = Group::getAllGroups(Group::ORDER_NAME);
                                foreach ($all_user as $tkt_user) {
                                    echo '<option value="u_' . $tkt_user->getId() . '"';
                                    if ($_SESSION['tkt_ajax_assigned'] == 'u_' . $tkt_user->getId()) {
                                        echo ' selected ';
                                    }
                                    echo '>' . $tkt_user->getNameAsLine() . '</option>';
                                }
                                ?>
                                <option disabled>-- Groups --</option>
                                <?php
                                foreach ($all_groups as $tkt_groups) {
                                    echo '<option value="g_' . $tkt_groups->getId() . '"';
                                    if ($_SESSION['tkt_ajax_assigned'] == 'g_' . $tkt_groups->getId()) {
                                        echo ' selected ';
                                    }
                                    echo '>' . $tkt_groups->getName() . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Suche</label>
                        <div class="col-sm-10">
                            <input type="text" id="search" class="form-control" placeholder="">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label for="" class="col-sm-2 control-label">zeige geschlossene</label>
                                <div class="col-sm-2">
                                    <input name="ajax_showclosed" id="ajax_showclosed" class="form-control"
                                           type="hidden" <?php if ($_SESSION['tkt_ajax_showclosed']) echo ' value="' . $_SESSION['tkt_ajax_showclosed'] . '" '; ?>/>
                                    <input name="showclosed" id="showclosed" type="checkbox" class="form-control"
                                           value="1" <?php if ($_SESSION['tkt_ajax_showclosed']) echo ' checked '; ?>/>
                                </div>
                            </div>
                        </div>
                    </div>

                    <table>
                        <tr align="left"
                            id="tr_cl_dates" <?php if (!$_SESSION['tkt_ajax_showclosed']) echo ' style="display: none" '; ?>>
                            <td>Datum (geschlossen):&nbsp;&nbsp;</td>
                            <td valign="left">
                                <input name="ajax_cl_date_min" id="ajax_cl_date_min"
                                       type="hidden" <?php if ($_SESSION['tkt_cl_date_min']) echo 'value="' . $_SESSION['tkt_cl_date_min'] . '"'; ?> />
                                <input name="date_cl_min" id="date_cl_min"
                                       style="width:70px;" <?php if ($_SESSION['tkt_cl_date_min']) echo 'value="' . date('d.m.Y', $_SESSION['tkt_cl_date_min']) . '"'; ?>
                                       class="text"
                                       onfocus="markfield(this,0)" onblur="markfield(this,1)"
                                       title="<?= $_LANG->get('von'); ?>">&nbsp;&nbsp;
                            </td>
                            <td valign="left">
                                <input name="ajax_cl_date_max" id="ajax_cl_date_max"
                                       type="hidden" <?php if ($_SESSION['tkt_cl_date_max']) echo 'value="' . $_SESSION['tkt_cl_date_max'] . '"'; ?> />
                                bis: <input name="date_cl_max" id="date_cl_max"
                                            style="width:70px;" <?php if ($_SESSION['tkt_cl_date_max']) echo 'value="' . date('d.m.Y', $_SESSION['tkt_cl_date_max']) . '"'; ?>
                                            class="text"
                                            onfocus="markfield(this,0)" onblur="markfield(this,1)"
                                            title="<?= $_LANG->get('bis'); ?>">&nbsp;&nbsp;
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>


        <table>
            <tr align="left"
                id="tr_cl_dates" <?php if (!$_SESSION['tkt_ajax_showclosed']) echo ' style="display: none" '; ?>>
                <td>Datum (geschlossen):&nbsp;&nbsp;</td>
                <td valign="left">
                    <input name="ajax_cl_date_min" id="ajax_cl_date_min"
                           type="hidden" <?php if ($_SESSION['tkt_cl_date_min']) echo 'value="' . $_SESSION['tkt_cl_date_min'] . '"'; ?> />
                    <input name="date_cl_min" id="date_cl_min"
                           style="width:70px;" <?php if ($_SESSION['tkt_cl_date_min']) echo 'value="' . date('d.m.Y', $_SESSION['tkt_cl_date_min']) . '"'; ?>
                           class="text"
                           onfocus="markfield(this,0)" onblur="markfield(this,1)"
                           title="<?= $_LANG->get('von'); ?>">&nbsp;&nbsp;
                </td>
                <td valign="left">
                    <input name="ajax_cl_date_max" id="ajax_cl_date_max"
                           type="hidden" <?php if ($_SESSION['tkt_cl_date_max']) echo 'value="' . $_SESSION['tkt_cl_date_max'] . '"'; ?> />
                    bis: <input name="date_cl_max" id="date_cl_max"
                                style="width:70px;" <?php if ($_SESSION['tkt_cl_date_max']) echo 'value="' . date('d.m.Y', $_SESSION['tkt_cl_date_max']) . '"'; ?>
                                class="text"
                                onfocus="markfield(this,0)" onblur="markfield(this,1)"
                                title="<?= $_LANG->get('bis'); ?>">&nbsp;&nbsp;
                </td>
            </tr>
        </table>
        <div class="table-responsive">
            <table id="ticketstable" width="100%" cellpadding="0" cellspacing="0" class="table table-hover">
                <thead>
                <tr>
                    <th><?= $_LANG->get('ID') ?></th>
                    <th><?= $_LANG->get('#') ?></th>
                    <th><?= $_LANG->get('Kategorie') ?></th>
                    <th><?= $_LANG->get('Datum') ?></th>
                    <th><?= $_LANG->get('erst. von') ?></th>
                    <th><?= $_LANG->get('Fällig') ?></th>
                    <th><?= $_LANG->get('Betreff') ?></th>
                    <th><?= $_LANG->get('Status') ?></th>
                    <th><?= $_LANG->get('Von') ?></th>
                    <th><?= $_LANG->get('Priorität') ?></th>
                    <th><?= $_LANG->get('Zugewiesen an') ?></th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

