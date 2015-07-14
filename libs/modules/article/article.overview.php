<? // ---------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       23.08.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------
require_once('libs/modules/businesscontact/businesscontact.class.php');

?>
<!-- Lightbox -->
<link rel="stylesheet" href="jscripts/lightbox/lightbox.css" type="text/css" media="screen" />
<script type="text/javascript" src="jscripts/lightbox/lightbox.js"></script>
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
    var art_table = $('#art_table').DataTable( {
        // "scrollY": "600px",
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/article/article.dt.ajax.php",
        "paging": true,
		"stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
		"pageLength": <?php echo $perf->getDt_show_default();?>,
// 		"dom": 'flrtip',        
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
		"lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
		"columns": [
		            null,
		            { "sortable": false },
		            null,
		            null,
		            null,
		            null,
		            null,
		            <?if($_CONFIG->shopActivation){?>
		            { "sortable": false },
		            <?}?>
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

    $("#art_table tbody td").live('click',function(){
        var aPos = $('#art_table').dataTable().fnGetPosition(this);
        var aData = $('#art_table').dataTable().fnGetData(aPos[0]);
        document.location='index.php?page=libs/modules/article/article.php&exec=edit&aid='+aData[0];
    });
} );
</script>

<table width="100%">
	<tr>
		<td width="200" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"><span style="font-size: 13px"> <?=$_LANG->get('Artikel')?> </span>
		</td>
        <td valign="center" align="right">
		</td>
		<td width="200"><?=$savemsg?></td>
		<td width="200" class="content_header" align="right">
			<span style="font-size: 14px">
				<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=new"><img src="images/icons/sticky-note--plus.png"> <?=$_LANG->get('Artikel hinzuf&uuml;gen')?></a>
			</span>
		</td>
	</tr>
</table>

<div class="box1">
	<table id="art_table" width="100%" cellpadding="0" cellspacing="0" class="stripe hover row-border order-column">
        <thead>
            <tr>
                <th width="15"><?=$_LANG->get('ID')?></th>
                <th width="105"><?=$_LANG->get('Bild')?></th>
                <th><?=$_LANG->get('Titel')?></th>
                <th width="80"><?=$_LANG->get('Art.-Nr.')?></th>
                <th width="80"><?=$_LANG->get('Matchcode')?></th>
                <th width="160"><?=$_LANG->get('Warengruppe')?></th>
                <th width="160"><?=$_LANG->get('zug. Kunde')?></th>
				<?if($_CONFIG->shopActivation){?>
					<th width="100"><?=$_LANG->get('Shop-Freigabe')?></th>
				<?}?>
                <th width="120"><?=$_LANG->get('Optionen')?></th>
            </tr>
        </thead>
	</table>
</div>