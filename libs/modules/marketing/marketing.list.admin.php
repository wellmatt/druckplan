<?php
require_once 'marketing.class.php';

if ($_REQUEST["exec"] == "save"){
    if ($_REQUEST["newlist"]){
        $newlist = new MarketingList();
        $newlist->setTitle($_REQUEST["newlist"]);
        $newlist->save();
    }
}
if ($_REQUEST["exec"] == "star"){
    if ($_REQUEST["id"]){
        $list_star = new MarketingList($_REQUEST["id"]);
        $list_star->setDefault(1);
        $list_star->save();
    }
}
if ($_REQUEST["exec"] == "delete"){
    if ($_REQUEST["id"]){
        $delete_list = new MarketingList($_REQUEST["id"]);
        $delete_list->delete();
    }
}

$lists = MarketingList::getAllLists();
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
<form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" class="form-horizontal"
      name="marketing_column_form" id="marketing_column_form">
    <input type="hidden" name="exec" value="save"/>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                Marketing-Vorlagen
                <span class="pull-right">
                  <?= $savemsg ?>
                    <div class="form-group">
                        <div class="col-sm-6">
                            <input class="form-control" id="newlist" name="newlist" type="text">
                        </div>
                        <div class="col-sm-6">
                            <button type="submit" class="btn btn-sm btn-success form-control">
                                <span class="glyphicons glyphicons-plus"></span>
                                Marketingliste Hinzufügen
                            </button>
                        </div>
                    </div>
                 </span>
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
                            <label for="" class="col-sm-2 control-label">Suche</label>
                            <div class="col-sm-4">
                                <input type="text" id="search" class="form-control" placeholder="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table id="marketing_table" class="table table-hover">
                    <thead>
                    <tr>
                        <th><?= $_LANG->get('ID') ?></th>
                        <th><?= $_LANG->get('Titel') ?></th>
                        <th><?= $_LANG->get('Standard') ?></th>
                    </tr>
                    </thead>
                    <?php foreach ($lists as $list) { ?>
                        <tr>
                            <td><?php echo $list->getId(); ?></td>
                            <td><?php echo $list->getTitle(); ?></td>
                            <td>
                                <?php
                                if ($list->getDefault() == 0) {
                                    echo '<a href="index.php?page=' . $_REQUEST['page'] . '&exec=star&id=' . $list->getId() . '"><span class="glyphicons glyphicons-star-empty"></span></a>&nbsp;';
                                    echo '<a href="index.php?page=' . $_REQUEST['page'] . '&exec=delete&id=' . $list->getId() . '"><span style="color:red" class="glyphicons glyphicons-remove"></span></a>';
                                } else {
                                    echo '<span class="glyphicons glyphicons-star"></span>';
                                }
                                ?>
                            </td>
                        </tr>
                    <?php } ?>
                    <tfoot>
                    <tr>
                        <th><?= $_LANG->get('ID') ?></th>
                        <th><?= $_LANG->get('Titel') ?></th>
                        <th><?= $_LANG->get('Standard') ?></th>
                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</form>




<script language="JavaScript">
    $(document).ready(function () {
        var marketing_table = $('#marketing_table').DataTable({
            "aaSorting": [[1, "asc"]],
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
            "lengthMenu": [[10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"]],
            "language": {
                "emptyTable": "Keine Daten vorhanden",
                "info": "Zeige _START_ bis _END_ von _TOTAL_ Eintr&auml;gen",
                "infoEmpty": "Keine Seiten vorhanden",
                "infoFiltered": "(gefiltert von _MAX_ gesamten Eintr&auml;gen)",
                "infoPostFix": "",
                "thousands": ".",
                "lengthMenu": "Zeige _MENU_ Eintr&auml;ge",
                "loadingRecords": "Lade...",
                "processing": "Verarbeite...",
                "search": "Suche:",
                "zeroRecords": "Keine passenden Eintr&auml;ge gefunden",
                "paginate": {
                    "first": "Erste",
                    "last": "Letzte",
                    "next": "N&auml;chste",
                    "previous": "Vorherige"
                },
                "aria": {
                    "sortAscending": ": aktivieren um aufsteigend zu sortieren",
                    "sortDescending": ": aktivieren um absteigend zu sortieren"
                }
            }
        });
        $('#search').keyup(function(){
            marketing_table.search( $(this).val() ).draw();
        });

        $("#marketing_table tbody td").live('click',function(){
            var aPos = $('#marketing_table').dataTable().fnGetPosition(this);
            var aData = $('#marketing_table').dataTable().fnGetData(aPos[0]);
            document.location='index.php?page=libs/modules/marketing/marketing.column.admin.php&exec=edit&id='+aData[0];
        });
    });
</script>