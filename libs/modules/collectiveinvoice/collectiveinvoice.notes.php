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
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
                VO-Notizen
            </h3>
	  </div>
	  <div class="panel-body">
          <div class="table-responsive">
              <table class="table table-hover">
                  <thead>
                      <tr>
                          <td><?=$_LANG->get('Kundennummer')?>:</td>
                          <td><?=$_LANG->get('Name')?>:</td>
                          <td><?=$_LANG->get('Auftrag')?>:</td>
                          <td><?=$_LANG->get('Adresse')?>:</td>
                          <td><?=$_LANG->get('Telefon')?></td>
                          <td><?=$_LANG->get('E-Mail')?></td>
                      </tr>
                  </thead>
                  <tbody>
                      <tr>
                          <td><?=$collectinv->getBusinessContact()->getId()?></td>
                          <td><?=nl2br($collectinv->getBusinessContact()->getNameAsLine())?></td>
                          <td><?=$collectinv->getNumber()?></td>
                          <td><?=nl2br($collectinv->getBusinessContact()->getAddressAsLine())?></td>
                          <td><?=$collectinv->getBusinessContact()->getPhone()?></td>
                          <td><?=$collectinv->getBusinessContact()->getEmail()?></td>
                      </tr>
                  </tbody>
              </table>
          </div>
          <div class="panel panel-default">
          	  <div class="panel-heading">
          			<h3 class="panel-title">
                        Notizen
                        <span class="pull-right">
                            <button class="btn btn-xs pointer btn-success" onclick="callBoxFancytktc('libs/modules/comment/comment.new.php?tktid=0&tktc_module=<?php echo get_class($collectinv);?>&tktc_objectid=<?php echo $collectinv->getId();?>');">
                                <span class="glyphicons glyphicons-plus"></span>
                                <?= $_LANG->get('Neu') ?>
                            </button>
                        </span>
                    </h3>
          	  </div>
              <br>
              <div class="table-responsive">
                  <table id="comment_table" class="table table-hover">
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
              </div>
          </div>
          <span class="pull-right">
              <button class="btn btn-sm btn-default" onclick="window.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&ciid=<?=$collectinv->getId()?>';">
                  <?= $_LANG->get('Zurück') ?>
              </button>
          </span>
	  </div>
</div>
