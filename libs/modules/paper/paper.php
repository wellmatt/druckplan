<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       12.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

require_once 'paper.class.php';

$_REQUEST["id"] = (int)$_REQUEST["id"];

if($_REQUEST["exec"] == "delete")
{
    $paper = new Paper($_REQUEST["id"]);
    $savemsg = getSaveMessage($paper->delete());
    $_REQUEST["exec"] = '';
}

if($_REQUEST["exec"] == "edit" || $_REQUEST["exec"] == "new" || $_REQUEST["exec"] == "copy")
{
    require_once 'paper.add.php';
} else
{
    $papers = Paper::getAllPapers(Paper::ORDER_NAME);
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
    var paper_table = $('#paper_table').DataTable( {
        // "scrollY": "600px",
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/paper/paper.dt.ajax.php",
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
		            null,
		            { "sortable": false },
		            { "sortable": false },
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

    $("#paper_table tbody td:not(:nth-child(5))").live('click',function(){
        var aPos = $('#paper_table').dataTable().fnGetPosition(this);
        var aData = $('#paper_table').dataTable().fnGetData(aPos[0]);
        document.location='index.php?page=libs/modules/paper/paper.php&exec=edit&id='+aData[0];
    });
} );
</script>

<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Papiere')?></td>
      <td><?=$savemsg?></td>
      <td width="200" class="content_header" align="right"><a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit"><img src="images/icons/script--plus.png"> <?=$_LANG->get('Papier hinzuf&uuml;gen')?></a></td>
   </tr>
</table>
<div class="box1">
<table width="100%" border="0" cellpadding="0" cellspacing="0" id="paper_table" class="stripe hover row-border order-column">
    <thead>
        <tr>
            <th width="20"><?=$_LANG->get('ID')?></th>
            <th><?=$_LANG->get('Name')?></th>
            <th><?=$_LANG->get('Gr&ouml;&szlig;en')?></th>
            <th><?=$_LANG->get('Grammaturen')?></th>
            <th width="100"><?=$_LANG->get('Optionen')?></th>
        </tr>
    </thead>
</table>
</div>
<?  } ?>





