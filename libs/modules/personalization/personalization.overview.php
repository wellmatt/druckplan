<? // ---------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       23.08.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------


if ($_REQUEST["exec"]=="reset")
{
    unset($_SESSION['perso_customer']);
}

$customers = Personalization::getAllCustomerWithPersos();
?>

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.tableTools.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    var art_table = $('#persos_table').DataTable( {
        // "scrollY": "600px",
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/personalization/personalization.dt.ajax.php",
        "paging": true,
		"stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
		"pageLength": <?php echo $perf->getDt_show_default();?>,
// 		"dom": 'flrtip',        
		"dom": 'T<"clear">flrtip',        
		"aaSorting": [[ 2, "asc" ]],
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
			var customer = document.getElementById('ajax_customer').value;
		    aoData.push( { "name": "customer", "value": customer, } );
		    $.getJSON( sSource, aoData, function (json) {
		        fnCallback(json)
		    } );
		},
		"lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
		"columns": [
		            null,
		            { "sortable": false },
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

    $("#persos_table tbody td").live('click',function(){
        var aPos = $('#persos_table').dataTable().fnGetPosition(this);
        var aData = $('#persos_table').dataTable().fnGetData(aPos[0]);
        document.location='index.php?page=libs/modules/personalization/personalization.php&exec=edit&id='+aData[0];
    });
	$('#customer').change(function(){	
		$('#ajax_customer').val($(this).val()); 
        $.post("libs/modules/personalization/personalization.ajax.php", {"ajax_action": "setFilter_perso_ajax_customer", "perso_ajax_customer": $(this).val()});
		$('#persos_table').dataTable().fnDraw();  
	})
} );
function PersoOrderTableRefresh()
{
	$('#persos_table').dataTable().fnDraw(); 
}
</script>

<table width="100%">
	<tr>
		<td width="200" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"><span style="font-size: 13px"> <?=$_LANG->get('Personalisierungen')?> </span>
		</td>
		<td><?=$savemsg?></td>
		<td width="300" class="content_header" align="right">
			<span style="font-size: 13px">
				<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=new"><img src="images/icons/applications-stack.png"> <?=$_LANG->get('Personalisierung hinzuf&uuml;gen')?></a>
			</span>
		</td>
	</tr>
</table>

<div class="box1">
    <div class="box2">
        <table>
            <tr align="left">
                <td>Kunde:&nbsp;&nbsp;</td>
                <td valign="left">
                    <input name="ajax_customer" id="ajax_customer" type="hidden" <?php if ($_SESSION['perso_ajax_customer']) echo ' value="'.$_SESSION['perso_ajax_customer'].'" ';?>/>  
                    <select name="customer" id="customer" style="width:160px">
                    <option value="" <?php if (!$_SESSION['perso_ajax_customer']) echo ' selected ';?>></option> 
                    <?php 
                    foreach ($customers as $customer){
                        echo '<option value="'.$customer->getId().'"';
                        if ($_SESSION['perso_ajax_customer'] == $customer->getId())
                        {
                            echo ' selected ';
                        }
                        echo '>'.$customer->getNameAsLine().'</option>';
                    }
                    ?>
                    </select>
                </td>
            </tr>
            <tr align="left">
                <td><a onclick="PersoTableRefresh();" href="Javascript:"><img src="images/icons/arrow-circle-double-135.png"/> Refresh</a></td>
            </tr>
            <tr align="left">
                <td><a href="index.php?page=libs/modules/personalization/personalization.php&exec=reset"><img src="images/icons/slash.png"/> Reset</a></td>
            </tr>
        </table>
    </div>
    </br>
	<table id="persos_table" width="100%" cellpadding="0" cellspacing="0" class="stripe hover row-border order-column">
        <thead>
            <tr>
                <th width="20"><?=$_LANG->get('ID')?></th>
                <th width="105"><?=$_LANG->get('Bild')?></th>
                <th><?=$_LANG->get('Titel')?></th>
                <th width="170"><?=$_LANG->get('Kunde')?></th>
                <th width="170"><?=$_LANG->get('Artikel')?></th>
                <th width="70"><?=$_LANG->get('Shop')?></th>
            </tr>
        </thead>
	</table>
</div>