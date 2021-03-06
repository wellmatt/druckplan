<?php

$mailadresses = $_USER->getEmailAddresses();
$mailsettings = false;
$savemsg = "";
$mail_servers = Array();

$unique_logins = Array();

//dd($mailadresses);

if (count($mailadresses)>0)
{
    foreach ($mailadresses as $mailadress)
    {
//		prettyPrint($mailadress);die();
		if (!in_array($mailadress->getLogin(),$unique_logins))
		{
			$unique_logins[] = $mailadress->getLogin();
		} else {
			continue;
		}
        
        try {
            /* Connect to an IMAP server.
             *   - Use Horde_Imap_Client_Socket_Pop3 (and most likely port 110) to
             *     connect to a POP3 server instead. */
            $client = new Horde_Imap_Client_Socket(array(
                'username' => $mailadress->getLogin(),
                'password' => $mailadress->getPassword(),
                'hostspec' => $mailadress->getHost(),
                'port' => $mailadress->getPort(),
                'secure' => 'ssl',
        
                // OPTIONAL Debugging. Will output IMAP log to the /tmp/foo file
//                 'debug' => '/tmp/foo',
        
                // OPTIONAL Caching. Will use cache files in /tmp/hordecache.
                // Requires the Horde/Cache package, an optional dependency to
                // Horde/Imap_Client.
                'cache' => array(
                    'backend' => new Horde_Imap_Client_Cache_Backend_Cache(array(
                        'cacheob' => new Horde_Cache(new Horde_Cache_Storage_File(array(
                            'dir' => '/tmp/hordecache'
                        )))
                    ))
                )
            ));
            $mail_servers[] = Array("mail"=>$mailadress->getAddress(), "mailid"=>$mailadress->getId(), "imap"=> $client);
            $mailsettings = true;
        } catch (Horde_Imap_Client_Exception $e) {
             var_dump($e);
        }
    }
} else 
{
    $savemsg = '<span class="label label-danger">Keine Mail-Konten hinterlegt</span>';
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

<style>
#popUpDiv{
    z-index: 100;
    background-color: rgba(123, 123,123, 0.8);
    display: none;
    border-radius: 7px;
/*     background:#6b6a63; */
    margin:30px auto 0;
    padding:6px;  
    position:absolute;
    width:300px;
    height:100px;
    top: 50%; 
    left: 50%; 
    margin-left: -400px;
    margin-top: -40px;
}
#popupSelect{
    z-index: 1000;
    position: absolute;
}
</style>

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
    var mailtable = $('#mailtable').DataTable( {
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/mail/mail.dt.ajax.php",
        "paging": true,
		"stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
		"pageLength": <?php echo $perf->getDt_show_default();?>,
		"dom": 'T<"clear">flrtip',        
		"tableTools": {
            "sRowSelect": "multi",
            "sRowSelector": "td:not(:first-child,:last-child)",
			"sSwfPath": "jscripts/datatable/copy_csv_xls_pdf.swf",
            "aButtons": [
                         "copy",
                         "csv",
                         "xls",
                         {
                             "sExtends": "pdf",
                             "sPdfOrientation": "landscape",
                             "sPdfMessage": "Contilas - Mails - <?php echo $_USER->getNameAsLine();?>"
                         },
                         "print",
                         "select_all", 
                         "select_none"
                     ]
                 },
		"lengthMenu": [ [10, 25, 50], [10, 25, 50] ],
		"columns": [            
		    		{
                        "className":      'details-control',
                        "orderable":      false,
                        "data":           null,
                        "defaultContent": ''
                    },
		            null,
		            null,
		            null,
		            null,
					{sortable: false, searchable: false}
		          ],
		"aaSorting": [[ 3, "desc" ]],
		"fnServerData": function ( sSource, aoData, fnCallback ) {
			var mailbox = document.getElementById('mailbox').value;
		    aoData.push( { "name": "mailbox", "value": mailbox, } );
			var mailid = document.getElementById('mailid').value;
		    aoData.push( { "name": "mailid", "value": mailid, } );
		    $.getJSON( sSource, aoData, function (json) {
		        fnCallback(json)
		    } );
		},
        "fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
			if ( aData[7] == 0 )
			{
				$(nRow).addClass('highlight');
			}
			$(nRow).addClass('dragmail');
			$(nRow).attr('muid',aData[6]);
			$(nRow).attr('mailbox',$('#mailbox').val());
			$(nRow).attr('mailid',$('#mailid').val());
			$(nRow).draggable(
				{
					opacity: 0.6,
					revert: "invalid",
					helper: "clone",
					cursor: "move",
					start: function(event, ui){
						ui.helper.children('td:first-child').remove();
						ui.helper.children('td:last-child').remove();
					}
				}
			);
        },
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

    // Array to track the ids of the details displayed rows
    var detailRows = [];
 
    $('#mailtable tbody').on( 'click', 'tr td:first-child', function () {
        var tr = $(this).closest('tr');
        var row = mailtable.row( tr );
        var idx = $.inArray( tr.attr('id'), detailRows );
        var control = $(this);
 
        if ( row.child.isShown() ) {
            tr.removeClass( 'details' );
            row.child.hide();
            detailRows.splice( idx, 1 );
        }
        else {
            tr.addClass( 'details' );
            $(this).addClass( 'details-control-loading' );
            get_child(row.data(),row,idx,tr,control);
        }
    } );
 
    // On each draw, loop over the `detailRows` array and show any child rows
    mailtable.on( 'draw', function () {
        $.each( detailRows, function ( i, id ) {
            $('#'+id+' td:first-child').trigger( 'click' );
        } );
    } );
    
} );

function get_child ( d,row,idx,tr,control ) {
	var mailbox = document.getElementById('mailbox').value;
	var mailid = document.getElementById('mailid').value;
	var body = $.ajax({
		type: "GET",
		url: "libs/modules/mail/mail.ajax.php",
		data: { exec: "getMailBody", mailid: mailid, mailbox: mailbox, muid: d[6] },
		success: function(data) 
		    {
			    row.child( '<div class="panel panel-default"><div class="panel-body">'+data+'</div></div>' ).show();
                $( ".details-control-loading" ).removeClass( 'details-control-loading' );
                tr.removeClass('highlight');
                if ( idx === -1 ) {
                    detailRows.push( tr.attr('id') );
                }
		    }
	});
}

function MailTableRefresh()
{
	$('#mailtable').dataTable().fnDraw(); 
}
function FoldersRemoveClass()
{
	$('.folderspan').removeClass('label-success');
}

function mail_markasread(obj,mailid,mailbox,muid)
{
	$.ajax({
		type: "GET",
		url: "libs/modules/mail/mail.ajax.php",
		data: { exec: "mark_as_read", mailid: mailid, mailbox: mailbox, muid: muid },
		success: function(data) 
		    {
			  $(obj).closest('tr').removeClass('highlight');
		    }
	});
}
function mail_markasread_multiple()
{
	var table = $('#mailtable').DataTable();
	var mailbox = document.getElementById('mailbox').value;
	var mailid = document.getElementById('mailid').value;
	var ids = getSelectedRowsMids();
	var idx = getSelectedRows();
	if (idx.length>0)
	{
    	$.ajax({
    		type: "GET",
    		url: "libs/modules/mail/mail.ajax.php",
    		data: { exec: "mark_as_read_multiple", mailid: mailid, mailbox: mailbox, muids: ids },
    		success: function(data) 
    		    {
  			        $( ".selected" ).removeClass( 'highlight' );
    			    var oTT = TableTools.fnGetInstance( 'mailtable' );
	  		        oTT.fnSelectNone();
        			return;
    		    }
    	});
	} else {
	    alert("Keine Mails ausgewählt!");
	    return;
	}
}
function mail_markasunread(obj,mailid,mailbox,muid)
{
	$.ajax({
		type: "GET",
		url: "libs/modules/mail/mail.ajax.php",
		data: { exec: "mark_as_unread", mailid: mailid, mailbox: mailbox, muid: muid },
		success: function(data) 
		    {
			  $(obj).closest('tr').addClass('highlight');
		    }
	});
}
function mail_markasunread_multiple()
{
	var table = $('#mailtable').DataTable();
	var mailbox = document.getElementById('mailbox').value;
	var mailid = document.getElementById('mailid').value;
	var ids = getSelectedRowsMids();
	var idx = getSelectedRows();
	if (idx.length>0)
	{
    	$.ajax({
    		type: "GET",
    		url: "libs/modules/mail/mail.ajax.php",
    		data: { exec: "mark_as_unread_multiple", mailid: mailid, mailbox: mailbox, muids: ids },
    		success: function(data) 
    		    {
    		        $( ".selected" ).addClass( 'highlight' );
    		        var oTT = TableTools.fnGetInstance( 'mailtable' );
    		        oTT.fnSelectNone();
        			return;
    		    }
    	});
	} else {
	    alert("Keine Mails ausgewählt!");
	    return;
	}
}
function mail_delete(obj,mailid,mailbox,muid)
{
	var r = confirm("Möchten Sie diese Mail wirklich löschen?");
	if (r == true) {
    	$.ajax({
    		type: "GET",
    		url: "libs/modules/mail/mail.ajax.php",
    		data: { exec: "delete", mailid: mailid, mailbox: mailbox, muid: muid },
    		success: function(data) 
    		    {
    	    	  var table = $('#mailtable').DataTable();
        		  var tr = $(obj).closest('tr');
     			  var row = table.row( tr );
        	      row.child.hide();
    			  $(obj).closest('tr').remove();
    		    }
    	});
	}
}
function mail_delete_multiple()
{
	var r = confirm("Möchten Sie diese Mails wirklich löschen?");
	if (r == true) {
    	var table = $('#mailtable').DataTable();
    	var mailbox = document.getElementById('mailbox').value;
    	var mailid = document.getElementById('mailid').value;
    	var ids = getSelectedRowsMids();
    	var idx = getSelectedRows();
    	if (idx.length>0)
    	{
        	$.ajax({
        		type: "GET",
        		url: "libs/modules/mail/mail.ajax.php",
        		data: { exec: "delete_multiple", mailid: mailid, mailbox: mailbox, muids: ids },
        		success: function(data) 
        		    {
        			    MailTableRefresh();
            			return;
        		    }
        	});
    	} else {
    	    alert("Keine Mails ausgewählt!");
    	    return;
    	}
	}
}
function drop_move(from_muid,from_mailbox,from_mailid,targ_mailbox,targ_mailid)
{
	$.ajax({
		type: "GET",
		url: "libs/modules/mail/mail.ajax.php",
		data: { exec: "dropcopy", mailid: from_mailid, mailbox: from_mailbox, muid: from_muid, dest_mailbox: targ_mailbox, dest_mailid: targ_mailid },
		success: function(data)
		{
			var r = confirm('Mail wurde nach "'+targ_mailbox+'" kopiert,\nSoll die ursprüngliche eMail gelöscht werden?');
			if (r == true) {
				$.ajax({
					type: "GET",
					url: "libs/modules/mail/mail.ajax.php",
					data: { exec: "delete", mailid: from_mailid, mailbox: from_mailbox, muid: from_muid },
					success: function(data)
					{

					}
				});
			}
			MailTableRefresh();
			return;
		}
	});
}
function mail_move()
{
	var table = $('#mailtable').DataTable();
	var mailbox = document.getElementById('mailbox').value;
	var mailid = document.getElementById('mailid').value;
	var dest_mailbox = $('#popupSelect').val();
	var muid = document.getElementById('muid').value;
	if (muid != "0")
	{
    	$.ajax({
    		type: "GET",
    		url: "libs/modules/mail/mail.ajax.php",
    		data: { exec: "copy", mailid: mailid, mailbox: mailbox, dest_mailbox: dest_mailbox, muid: muid },
    		success: function(data) 
    		    {
    			    $('#popUpDiv').hide();
    			    MailTableRefresh();
        			return;
    		    }
    	});
	} else 
	{
		var ids = getSelectedRowsMids();
		var idx = getSelectedRows();
		if (idx.length>0)
		{
	    	$.ajax({
	    		type: "GET",
	    		url: "libs/modules/mail/mail.ajax.php",
	    		data: { exec: "copy_multiple", mailid: mailid, mailbox: mailbox, dest_mailbox: dest_mailbox, muids: ids },
	    		success: function(data) 
	    		    {
    			        $('#popUpDiv').hide();
	    			    MailTableRefresh();
	        			return;
	    		    }
	    	});
		} else {
		    alert("Keine Mails ausgewählt!");
		    return;
		}
	}
	$('#muid').val(0);
}
function getSelectedRowsMids()
{
	var table = $('#mailtable').DataTable();
	var ids = new Array();
	table.rows('.selected').indexes().each( function (idx) {
	    var d = table.row( idx ).data();
	    ids.push(d[6]);
	} );
	return ids;
}
function getSelectedRows()
{
	var table = $('#mailtable').DataTable();
	var idx = new Array();
	table.rows('.selected').indexes().each( function (id) {
		var d = table.row( id ).data();
	    idx.push(id);
	} );
	return idx;
}
function MailBoxSelectPopup()
{
	$('#popupSelect').empty();
	var mailid = document.getElementById('mailid').value;
	$('#popupSelect').append('<option value=""></option>');
	$('.mailid_'+mailid).each(function(i, obj) {
		if ($(this).html().indexOf("Entwürfe") == -1)
			$('#popupSelect').append('<option value="'+$(this).html()+'">'+$(this).html()+'</option>');
	});

	var muid = document.getElementById('muid').value;
	var idx = getSelectedRows();
	
	if (idx.length>0 || muid != "0")
		$('#popUpDiv').show();
}
$(document).ready(function(){
    var popup_height = document.getElementById('popUpDiv').offsetHeight;
    var popup_width = document.getElementById('popUpDiv').offsetWidth;
    $("#popUpDiv").css('top',(($(window).height()-popup_height)/2));
    $("#popUpDiv").css('left',(($(window).width()-popup_width)/2));
    $("#popUpDiv").css('margin',0);
});

</script>

<script>
	$(document).ready(function(){
		$( ".folderspan" ).droppable({
			activeClass: "ui-state-default",
			hoverClass: "ui-state-hover",
			accept: ":not(.ui-sortable-helper)",
			drop: function( event, ui ) {
				alert('Mail wird kopiert, einen Augenblick...');
				var muid = ui.draggable.attr('muid');
				var mailbox = ui.draggable.attr('mailbox');
				var mailid = ui.draggable.attr('mailid');
				var targ_mailbox = $(this).attr('mailbox');
				var targ_mailid = $(this).attr('mailid');
				drop_move(muid,mailbox,mailid,targ_mailbox,targ_mailid);
				$(ui).remove();
			}
		});
	});
</script>


<script>
	$(function() {
		$("a#newmail_hiddenclicker").fancybox({
			'type'          :   'iframe',
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600, 
			'speedOut'		:	200, 
			'width'         :   1024,
			'height'		:	800,
		    'scrolling'     :   'yes',
// 			'overlayShow'	:	true,
// 		    'fitToView'     :   true,
// 		    'autoSize'      :   true,
// 		    'autoCenter'    :   true,
// 		    'height'        :   'auto',
			'helpers'		:   { overlay:null, closeClick:true }
		});
	});
	function callBoxFancyNewMail(my_href) {
		var j1 = document.getElementById("newmail_hiddenclicker");
		j1.href = my_href;
		$('#newmail_hiddenclicker').trigger('click');
	}
</script>

<div id="newmail_hidden_clicker" style="display:none"><a id="newmail_hiddenclicker" href="http://www.google.com" >Hidden Clicker</a></div>
<div id="popUpDiv">
    <span>Ziel-Mailbox auswählen:</span></br>
    <select id="popupSelect">
    </select></br>
    <input type="checkbox" value="1" checked id="mail_copy_move"/> <span>verschieben?</span></br>
    <div class="row">
      <div class="col-md-6" style="text-align: left;"><span onclick="$('#muid').val(0);$('#popUpDiv').hide();" class="btn btn-sm btn-default">Abbrechen</span></div>
      <div class="col-md-6" style="text-align: right;"><span onclick="mail_move();" class="btn btn-sm btn-default">Speichern</span></div>
    </div>
</div>
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				Mail-Client
				<span class="pull-right">
					<?=$savemsg?>
					<button type="button" onclick="callBoxFancyNewMail('libs/modules/mail/mail.send.frame.php');" class="btn btn-sm btn-default">Neue Mail</button>
					<!--          <button type="button" onclick="mail_markasread_multiple();" class="btn btn-sm btn-default">Als gelesen markieren</button>-->
					<!--          <button type="button" onclick="mail_markasunread_multiple();" class="btn btn-sm btn-default">Als ungelesen markieren</button>-->
					<!--          <button type="button" onclick="MailBoxSelectPopup();" class="btn btn-sm btn-default">verschieben</button>-->
         		 	<button type="button" onclick="mail_delete_multiple();" class="btn btn-sm btn-danger">Löschen</button>
				</span>
			</h3>
	  </div>
	  <div class="panel-body">
		  <input name="muid" id="muid" type="hidden" value="0"/>
		  <input name="mailbox" id="mailbox" type="hidden" value="INBOX"/>
		  <input name="mailid" id="mailid" type="hidden" value="<?php echo $mail_servers[0]["mailid"];?>"/>

		  <div class="row">
			  <div class="col-md-3">
				  <div class="panel panel-default">
				  	  <div class="panel-heading">
				  			<h3 class="panel-title">
								Mail-Konten
							</h3>
				  	  </div>
				  	  <div class="panel-body">
						  <?php
						  if ($mailsettings)
						  {
							  $first = true;
							  foreach ($mail_servers as $mail_server)
							  {
								  echo '<ul class="list-unstyled">';
								  echo '<span class="label label-info" style="text-align: right;">'.$mail_server["mail"].'</span></br></br>';

								  unset($folders);
								  try {
									  $folders = $mail_server["imap"]->listMailboxes('*',Horde_Imap_Client::MBOX_SUBSCRIBED_EXISTS,Array("flat"=>false,"recursivematch"=>true,"attributes"=>true,"children"=>true));
								  } catch (Horde_Imap_Client_Exception $e) {

								  }

								  foreach ($folders as $key => $value)
								  {
									  if ($key == "INBOX" && $first)
									  {
										  echo '<li><span mailid="'.$mail_server["mailid"].'" mailbox="'.$key.'" onclick="$(\'#mailbox\').val(\''.$key.'\');$(\'#mailid\').val(\''.$mail_server["mailid"].'\');MailTableRefresh();FoldersRemoveClass();$(this).addClass(\'label-success\');" class="mailid_'.$mail_server["mailid"].' folderspan label label-default label-success pointer">'.$key.'</span></li>';
										  $first = false;
									  }
									  else
										  echo '<li><span mailid="'.$mail_server["mailid"].'" mailbox="'.$key.'" onclick="$(\'#mailbox\').val(\''.$key.'\');$(\'#mailid\').val(\''.$mail_server["mailid"].'\');MailTableRefresh();FoldersRemoveClass();$(this).addClass(\'label-success\');" class="mailid_'.$mail_server["mailid"].' folderspan label label-default pointer">'.$key.'</span></li>';
								  }

								  echo '</ul>';
								  echo '</br>';
							  }
						  }
						  ?>
				  	  </div>
				  </div>

			  </div>
			  <div class="col-md-9">
				  <div class="table-responsive">
					  <table id="mailtable" class="table table-hover">
						  <thead>
						  <tr>
							  <th></th>
							  <th><?=$_LANG->get('Von')?></th>
							  <th><?=$_LANG->get('An')?></th>
							  <th><?=$_LANG->get('Betreff')?></th>
							  <th><?=$_LANG->get('Datum')?></th>
							  <th><?=$_LANG->get('Optionen')?></th>
						  </tr>
						  </thead>
						  <tfoot>
						  <tr>
							  <th></th>
							  <th><?=$_LANG->get('Von')?></th>
							  <th><?=$_LANG->get('An')?></th>
							  <th><?=$_LANG->get('Betreff')?></th>
							  <th><?=$_LANG->get('Datum')?></th>
							  <th><?=$_LANG->get('Optionen')?></th>
						  </tr>
						  </tfoot>
					  </table>
				  </div>
			  </div>
	  </div>
</div>