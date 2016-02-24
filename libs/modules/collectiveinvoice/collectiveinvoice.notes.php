<?php

?>


<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css"
	href="css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8"
	src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8"
	src="jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8"
	src="jscripts/datatable/dataTables.bootstrap.js"></script>
<script type="text/javascript" charset="utf8"
	src="jscripts/datatable/date-uk.js"></script>
	
<div id="tktc_hidden_clicker" style="display:none"><a id="tktc_hiddenclicker" href="http://www.google.com" >Hidden Clicker</a></div>

<script type="text/javascript">
function callBoxFancytktc(my_href) {
	var j1 = document.getElementById("tktc_hiddenclicker");
	j1.href = my_href;
	$('#tktc_hiddenclicker').trigger('click');
}
$(document).ready(function() {
    var search_tickets = $('#comment_table').DataTable( {
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/collectiveinvoice/collectiveinvoice.comments.dt.ajax.php?ciid=<?php echo $collectinv->getId();?>",
		"stateSave": false,
		"pageLength": 10,
		"dom": 'flrtip',
		"lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
		"columns": [            
		    		{
                        "className":      'details-control',
                        "orderable":      false,
                        "data":           null,
                        "defaultContent": ''
                    },
		            { "searchable": false},
		            { "searchable": true},
		            { "searchable": true},
		            { "searchable": false},
		            { "searchable": false},
		            { "visible": false}
		          ],
        "language": {
                    	"url": "jscripts/datatable/German.json"
          	        }
    } );
    $("#comment_table tbody td:not(:first-child)").live('click',function(){
        var aPos = $('#comment_table').dataTable().fnGetPosition(this);
        var aData = $('#comment_table').dataTable().fnGetData(aPos[0]);
        callBoxFancytktc('libs/modules/comment/comment.edit.php?cid='+aData[1]+'&tktid=0');
    });
	
	$("a#tktc_hiddenclicker").fancybox({
		'type'          :   'iframe',
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'width'         :   1024,
		'height'		:	768, 
		'overlayShow'	:	true,
		'helpers'		:   { overlay:null, closeClick:true }
	});

	$('#comment_table tbody').on('click', 'tr td:first-child', function () {
        var tr = $(this).closest('tr');
        var row = search_tickets.row( tr );
 
        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( child_comment(row.data()) ).show();
            tr.addClass('shown');
        }
    } );

	function child_comment ( d ) {
	    // `d` is the original data object for the row
	    return '<div class="box2">'+d[6]+'</div>';
	}
} );
</script>

<table border="0" cellpadding="2" cellspacing="0" width="100%">
    <tbody>
    	<tr>
            <td width="100%" align="left">
                <div class="btn-group" role="group">
                  <button type="button" onclick="window.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&ciid=<?=$collectinv->getId()?>';" class="btn btn-sm btn-default">Zurück</button>
                </div>
            </td>
    	</tr>
    </tbody>
</table>
<div class="box1" style="margin-top:50px;">
<table width="100%">
    <colgroup>
        <col width="10%">
        <col width="23%">
        <col width="10%">
        <col width="23%">
        <col width="10%">
        <col>
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Kundennummer')?>:</td>
        <td class="content_row_clear"><?=$collectinv->getBusinessContact()->getId()?></td>
        <td class="content_row_header"><?=$_LANG->get('Auftrag')?>:</td>
        <td class="content_row_clear"><?=$collectinv->getNumber()?></td>
        <td class="content_row_header"><?=$_LANG->get('Telefon')?></td>
        <td class="content_row_clear"><?=$collectinv->getBusinessContact()->getPhone()?></td>
    </tr>
    <tr>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Name')?>:</td>
        <td class="content_row_clear" valign="top"><?=nl2br($collectinv->getBusinessContact()->getNameAsLine())?></td>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Adresse')?>:</td>
        <td class="content_row_clear"  valign="top"><?=nl2br($collectinv->getBusinessContact()->getAddressAsLine())?></td>
        <td class="content_row_header"  valign="top"><?=$_LANG->get('E-Mail')?></td>
        <td class="content_row_clear" valign="top"><?=$collectinv->getBusinessContact()->getEmail()?></td>
    </tr>
</table>
</div>
<br>
<h4>Notizen</h4>
<span style="float:right;" class="pointer" onclick="callBoxFancytktc('libs/modules/comment/comment.new.php?tktid=0&tktc_module=<?php echo get_class($collectinv);?>&tktc_objectid=<?php echo $collectinv->getId();?>');">Neu</span>
<table id="comment_table" width="100%" cellpadding="0"
	cellspacing="0" class="stripe hover row-border order-column">
	<thead>
		<tr>
			<th></th>
			<th><?=$_LANG->get('ID')?></th>
			<th><?=$_LANG->get('Titel')?></th>
			<th><?=$_LANG->get('erst. von')?></th>
			<th><?=$_LANG->get('Datum')?></th>
			<th><?=$_LANG->get('Sichtbarkeit')?></th>
		</tr>
	</thead>
</table>