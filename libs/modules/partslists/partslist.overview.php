<?
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'partslist.class.php';

$partslists = Partslist::getAll();
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
            Stücklisten
            <span class="pull-right">
                <button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=libs/modules/partslists/partslist.edit.php&exec=edit';">
                    <span class="glyphicons glyphicons-plus"></span>
                    <?= $_LANG->get('Stückliste hinzuf&uuml;gen') ?>
                </button>
            </span>
        </h3>
    </div>
    <div class="table-responsive">
        <table class="table table-hover" id="partslisttable">
            <thead>
            <tr>
                <th><?=$_LANG->get('ID')?></th>
                <th><?=$_LANG->get('Name')?></th>
                <th><?=$_LANG->get('Datum')?></th>
                <th><?=$_LANG->get('User')?></th>
            </tr>
            </thead>
            <tbody>
                <? foreach($partslists as $partslist){?>
                    <tr>
                        <td><?=$partslist->getId()?></td>
                        <td><?=$partslist->getTitle()?></td>
                        <td><?=date('d.m.y',$partslist->getCrtdate())?></td>
                        <td><?=$partslist->getCrtuser()->getNameAsLine()?></td>
                    </tr>
                <? } ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var partslisttable = $('#partslisttable').DataTable( {
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

        $("#partslisttable tbody td").live('click',function(){
            var aPos = $('#partslisttable').dataTable().fnGetPosition(this);
            var aData = $('#partslisttable').dataTable().fnGetData(aPos[0]);
            document.location='index.php?page=libs/modules/partslists/partslist.edit.php&exec=edit&id='+aData[0];
        });
    } );
</script>