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


<table width="100%">
    <tr>
        <td width="150" class="content_header"><img src="<?= $_MENU->getIcon($_REQUEST['page']) ?>">
            <span style="font-size: 13px"><?= $_LANG->get('Marketing-Listen') ?></span>
        </td>
        <td width="250" class="content_header" align="right">
            <?= $savemsg ?>
        </td>
        <td class="content_header" align="right">
            <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="marketing_column_form" id="marketing_column_form">
                <input type="hidden" name="exec" value="save"/>
                Neue Liste: <input id="newlist" name="newlist" type="text"><button type="submit" class="btn btn-sm">Hinzufügen</button>
            </form>
        </td>
    </tr>
</table>

<div class="box1">
    <table id="marketing_table" width="100%" cellpadding="0" cellspacing="0"
           class="display stripe hover row-border order-column">
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
                    if ($list->getDefault() == 0){
                        echo '<a href="index.php?page='.$_REQUEST['page'].'&exec=star&id='.$list->getId().'"><img src="images/icons/star-empty.png"/></a>&nbsp;';
                        echo '<a href="index.php?page='.$_REQUEST['page'].'&exec=delete&id='.$list->getId().'"><img src="images/icons/cross.png"/></a>';
                    } else {
                        echo '<img src="images/icons/star.png"/>';
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


<script language="JavaScript">
    $(document).ready(function () {
        var marketingtable = $('#marketing_table').DataTable({
            "aaSorting": [[1, "asc"]],
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

        $("#marketing_table tbody td").live('click',function(){
            var aPos = $('#marketing_table').dataTable().fnGetPosition(this);
            var aData = $('#marketing_table').dataTable().fnGetData(aPos[0]);
            document.location='index.php?page=libs/modules/marketing/marketing.column.admin.php&exec=edit&id='+aData[0];
        });
    });
</script>