<? // ---------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       23.08.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------
chdir("../../../");
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once("libs/basic/countries/country.class.php");
require_once('libs/modules/businesscontact/businesscontact.class.php');
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/comment/comment.class.php';
require_once 'libs/modules/tickets/ticket.class.php';

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


if ($_USER == false){
    error_log("Login failed (basic-importer.php)");
    die("Login failed");
}
$perf = new Perferences();

function printSubTradegroupsForSelect($parentId, $depth){
    $all_subgroups = Tradegroup::getAllTradegroups($parentId);
    foreach ($all_subgroups AS $subgroup)
    {
        global $x;
		$x++; ?>
        <option value="<?=$subgroup->getId()?>">
				<?for ($i=0; $i<$depth+1;$i++) echo "&emsp;"?>
				<?= $subgroup->getTitle()?>
		</option>
        <? printSubTradegroupsForSelect($subgroup->getId(), $depth+1);
	}
}

?>


<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-bootstrap.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-halflings.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-filetypes.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-social.css" />
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" href="../../../css/bootstrap.min.css">	
<link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/local/jquery.ui.datepicker-<?=$_LANG->getCode()?>.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery.validate.min.js"></script>
<script type="text/javascript" src="../../../jscripts/moment/moment-with-locales.min.js"></script>


<!-- Lightbox -->
<link rel="stylesheet" href="../../../jscripts/lightbox/lightbox.css" type="text/css" media="screen" />
<script type="text/javascript" src="../../../jscripts/lightbox/lightbox.js"></script>
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="../../../css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="../../../css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="../../../css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/dataTables.tableTools.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/tagit/tag-it.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../../jscripts/tagit/jquery.tagit.css" media="screen" />
<script type="text/javascript">
$(document).ready(function() {
    var art_table = $('#art_table').DataTable( {
        // "scrollY": "600px",
        "autoWidth": false,
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "../../../libs/modules/article/article.dt.ajax.php",
        "paging": true,
		"stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
		"pageLength": <?php echo $perf->getDt_show_default();?>,
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
		"lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
		"columns": [
		            null,
		            { "sortable": false, "visible": false },
		            null,
		            null,
		            { "sortable": false },
		            null,
		            { "sortable": false, "visible": false },
		            { "sortable": false, "visible": false }
		          ],
  		"fnServerData": function ( sSource, aoData, fnCallback ) {
			var tags = document.getElementById('ajax_tags').value;
			var tg = document.getElementById('ajax_tradegroup').value;
			var bc = document.getElementById('ajax_bc').value;
			var cp = document.getElementById('ajax_cp').value;
		    aoData.push( { "name": "search_tags", "value": tags, } );
		    aoData.push( { "name": "tradegroup", "value": tg, } );
		    aoData.push( { "name": "bc", "value": bc, } );
		    aoData.push( { "name": "cp", "value": cp, } );
		    $.getJSON( sSource, aoData, function (json) {
		        fnCallback(json)
		    } );
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

    $("#art_table tbody td").live('click',function(){
        var aPos = $('#art_table').dataTable().fnGetPosition(this);
        var aData = $('#art_table').dataTable().fnGetData(aPos[0]);

        $.ajax({
            url: "../../../libs/modules/collectiveinvoice/orderposition.ajax.php",
            data: { exec: 'addPosArticle', ciid: '', aid: '' },
            dataType: "json",
            success: function( data ) {
                parent.addArticle(aData[0]);
                parent.$.fancybox.close();
            }
        });
    });
} );
</script>

<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery("#tags").tagit({
        singleField: true,
        singleFieldNode: $('#tags'),
        singleFieldDelimiter: ";",
        allowSpaces: true,
        minLength: 2,
        removeConfirmation: true,
        tagSource: function( request, response ) {
            $.ajax({
                url: "../../../libs/modules/article/article.ajax.php?ajax_action=search_tags", 
                data: { term:request.term },
                dataType: "json",
                success: function( data ) {
                    response( $.map( data, function( item ) {
                        return {
                            label: item.label,
                            value: item.value
                        }
                    }));
                }
            });
        },
        afterTagAdded: function(event, ui) {
            $('#ajax_tags').val($("#tags").tagit("assignedTags"));
        	$('#art_table').dataTable().fnDraw();
        },
        afterTagRemoved: function(event, ui) {
            $('#ajax_tags').val($("#tags").tagit("assignedTags"));
        	$('#art_table').dataTable().fnDraw();
        }
    });
});
</script>

<script>
$(function() {
   $( "#bc_cp" ).autocomplete({
        delay: 0,
        source: '../../../libs/modules/article/article.ajax.php?ajax_action=search_bc_cp',
		minLength: 2,
		dataType: "json",
        select: function(event, ui) {
            if (ui.item.type == 1)
            {
                $('#ajax_bc').val(ui.item.value);
                $('#bc_cp').val(ui.item.label);
            	$('#art_table').dataTable().fnDraw();
            }
            else
            {
                $('#ajax_cp').val(ui.item.value);
                $('#bc_cp').val(ui.item.label);
            	$('#art_table').dataTable().fnDraw();
            }
      		return false;
        }
    });
});
</script>
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
                Artikel
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
                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Tags</label>
                                <div class="col-sm-4">
                                    <input type="hidden" id="ajax_tags" name="ajax_tags"/>
                                    <input name="tags" id="tags" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Warengruppe</label>
                                <div class="col-sm-4">
                                    <input type="hidden" id="ajax_tradegroup" name="ajax_tradegroup" value="0"/>
                                    <select name="tradegroup" id="tradegroup" class="form-control" onchange="$('#ajax_tradegroup').val($('#tradegroup').val());$('#art_table').dataTable().fnDraw();" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                        <option value="0">- Alle -</option>
                                        <?php
                                        $all_tradegroups = Tradegroup::getAllTradegroups();
                                        foreach ($all_tradegroups as $tg)
                                        {?>
                                            <option value="<?=$tg->getId()?>">
                                                <?=$tg->getTitle()?></option>
                                            <? printSubTradegroupsForSelect($tg->getId(), 0);
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label">Kunde/Ansprechpartner</label>
                                <div class="col-sm-4">
                                    <input type="hidden" id="ajax_bc" name="ajax_bc" value="0"/>
                                    <input type="hidden" id="ajax_cp" name="ajax_cp" value="0"/>
                                    <input name="bc_cp" id="bc_cp"  onchange="Javascript: if($('#bc_cp').val()==''){$('#ajax_bc').val(0);$('#ajax_cp').val(0);$('#art_table').dataTable().fnDraw();}" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                </div>
                                <div class="col-sm-1">
                                    <span class="glyphicons glyphicons-remove pointer" onclick="$('#bc_cp').val('');$('#ajax_bc').val(0);$('#ajax_cp').val(0);$('#art_table').dataTable().fnDraw();" title="Reset"></span>
                                </div>
                            </div>
                        </div>
				  </div>
			</div>
          </br>
          <div class="table-responsive">
              <table id="art_table" class="table table-hover">
                  <thead>
                      <tr>
                          <th><?=$_LANG->get('ID')?></th>
                          <th><?=$_LANG->get('Bild')?></th>
                          <th><?=$_LANG->get('Titel')?></th>
                          <th><?=$_LANG->get('Art.-Nr.')?></th>
                          <th><?=$_LANG->get('Tags')?></th>
                          <th><?=$_LANG->get('Warengruppe')?></th>
                          <th><?=$_LANG->get('Shop-Freigabe')?></th>
                          <th><?=$_LANG->get('Optionen')?></th>
                      </tr>
                  </thead>
              </table>
          </div>
	  </div>
</div>
