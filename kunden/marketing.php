<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

require_once './libs/modules/marketing/marketing.class.php';
$lists = MarketingList::getAllListsForBC($_BUSINESSCONTACT);

$curr_list = $_REQUEST["list"];
if (!$curr_list)
    $curr_list = MarketingList::getDefaultList()->getId();
$columns = MarketingColumn::getAllColumnsForList($curr_list);
$marketjobs = Marketing::getAllForListAndBc($curr_list,$_BUSINESSCONTACT);
?>

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="../css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="../jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="../jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="../jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="../css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="../jscripts/datatable/dataTables.tableTools.js"></script>
<script type="text/javascript" charset="utf8" src="../jscripts/datatable/date-uk.js"></script>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
                <b>Marketingplan</b>
                <span class="pull-right"><?=$savemsg?></span>
            </h3>
	  </div>
	  <div class="panel-body">
          <div class="panel panel-default">
          	  <div class="panel-heading">
          			<h3 class="panel-title">
                       <b>Filter</b>
                    </h3>
          	  </div>
          	  <div class="panel-body">
                  <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="marketing_column_form" id="marketing_column_form">
                      <input type="hidden" name="pid" value="100">
                      Liste auswählen: <select name="list">
                          <?php
                          foreach ($lists as $list) {
                              echo '<option value="'.$list->getId().'">'.$list->getTitle().'</option>';
                          }
                          ?>
                      </select>
                      <button type="submit" class="btn btn-sm">Weiter</button>
                  </form>
          	  </div>
          </div>
	  </div>
    <div class="table-responsive">
        <table class="table table-hover" id="marketing_table">
            <thead>
            <tr>
                <th><?= $_LANG->get('ID') ?></th>
                <th><?= $_LANG->get('Titel') ?></th>
                <th><?= $_LANG->get('Kunde') ?></th>
                <th><?= $_LANG->get('Datum') ?></th>
                <?php foreach ($columns as $column) { ?>
                    <th><?php echo $column->getTitle() ?></th>
                <?php } ?>
            </tr>
            </thead>
            <?php foreach ($marketjobs as $marketjob) { ?>
                <tr>
                    <td><?php echo $marketjob->getId(); ?></td>
                    <td><?php echo $marketjob->getTitle(); ?></td>
                    <td><?php echo $marketjob->getBusinesscontact()->getNameAsLine(); ?></td>
                    <td><?php echo date('d.m.y H:i', $marketjob->getCrtdate()); ?></td>
                    <?php foreach ($columns as $column) { ?>
                        <td><?php echo $marketjob->getColumnValue($column->getId()); ?></td>
                    <?php } ?>
                </tr>
            <?php } ?>
            <tfoot>
            <tr>
                <th><?= $_LANG->get('ID') ?></th>
                <th><?= $_LANG->get('Titel') ?></th>
                <th><?= $_LANG->get('Kunde') ?></th>
                <th><?= $_LANG->get('Datum') ?></th>
                <?php foreach ($columns as $column) { ?>
                    <th><?php echo $column->getTitle() ?></th>
                <?php } ?>
            </tr>
            </tfoot>
        </table>
    </div>
</div>





<script language="JavaScript">
    $(document).ready(function () {
        var marketingtable = $('#marketing_table').DataTable({
            "scrollX": true,
            "aaSorting": [[3, "desc"]],
            "dom": 'T<"clear">flrtip',
            "tableTools": {
                "sSwfPath": "../jscripts/datatable/copy_csv_xls_pdf.swf",
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
    });
</script>