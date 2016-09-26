<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/storage/storage.area.class.php';

if ($_REQUEST["exec"] == "delete"){
    $del_area = new StorageArea($_REQUEST["id"]);
    $del_area->delete();
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

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
                <img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
                Lagerplätze
                <span class="pull-right">
                     <?= $savemsg ?>
                    <button class="btn btn-xs btn-success"
                            onclick="document.location. href='index.php?page=libs/modules/storage/storage.article.summary.php';">
                    <?= $_LANG->get('Lagerartikel') ?>
                    </button>
                    <button class="btn btn-xs btn-success" onclick="document.location. href='index.php?page=libs/modules/storage/storage.edit.php&exec=new';">
                    <?= $_LANG->get('Lagerplatz erstellen') ?>
                    </button>
		  		</span>
            </h3>
	  </div>
    <br>
    <div class="table-responsive">
        <table  id="storagetable" class="table table-hover">
            <thead>
            <tr>
                <th><?=$_LANG->get('ID')?></th>
                <th><?=$_LANG->get('Name')?></th>
                <th><?=$_LANG->get('Belegung')?></th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th><?=$_LANG->get('ID')?></th>
                <th><?=$_LANG->get('Name')?></th>
                <th><?=$_LANG->get('Belegung')?></th>
            </tr>
            </tfoot>
        </table>
    </div>

</div>


<script type="text/javascript">
    $(document).ready(function() {
        var storagetable = $('#storagetable').DataTable( {
            "processing": true,
            "bServerSide": true,
            "sAjaxSource": "libs/modules/storage/storage.dt.ajax.php",
            "paging": true,
            "stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
            "pageLength": <?php echo $perf->getDt_show_default();?>,
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

        $("#storagetable tbody td").live('click',function(){
            var aPos = $('#storagetable').dataTable().fnGetPosition(this);
            var aData = $('#storagetable').dataTable().fnGetData(aPos[0]);
            document.location='index.php?page=libs/modules/storage/storage.edit.php&exec=edit&id='+aData[0];
        });
    } );
</script>