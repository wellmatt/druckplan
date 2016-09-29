<?php
require_once 'marketing.class.php';
$lists = MarketingList::getAllLists();

$curr_list = $_REQUEST["list"];
if ($curr_list == null || $curr_list == 0 || !isset($curr_list))
    $curr_list = MarketingList::getDefaultList()->getId();
$columns = MarketingColumn::getAllColumnsForList($curr_list);
$marketjobs = Marketing::getAllForList($curr_list);
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

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
                <img src="<?= $_MENU->getIcon($_REQUEST['page']) ?>">
                Marketingplan
                <span class="pull-right">
                    <a href="libs/modules/marketing/marketing.full.php" target="_blank" class="icon-link">
                        <button class="btn btn-xs btn-success">
                            <?= $_LANG->get('In neuem Fenster öffnen') ?>
                        </button>
                    </a>
                    <a href="index.php?page=libs/modules/marketing/marketing.edit.php&exec=new&list=<?=$curr_list?>" class="icon-link">
                        <button class="btn btn-xs btn-success">
                            <?= $_LANG->get('Plan erstellen') ?>
                        </button>
                    </a>
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
                  <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="marketing_column_form" class="form-horizontal" id="marketing_column_form">
                      <div class="form-group">
                          <label for="" class="col-sm-2 control-label">Vorlage auswählen:</label>
                          <div  class="col-sm-2 ">
                              <select class="form-control" name="list">
                                  <?php
                                  foreach ($lists as $list) {
                                      if ($list->getId() == $curr_list)
                                          echo '<option selected value="'.$list->getId().'">'.$list->getTitle().'</option>';
                                      else
                                          echo '<option value="'.$list->getId().'">'.$list->getTitle().'</option>';
                                  }
                                  ?>
                              </select>
                          </div>
                          <div class="col-sm-2">
                              <button type="submit" class="btn btn-sm">Weiter</button>
                          </div>
                      </div>
                  </form>
              </div>
          </div>
          <br>
          <div  class="table-responsive">
              <table id="marketing_table" class="table table-hover">
                  <thead>
                  <tr>
                      <th><?= $_LANG->get('ID') ?></th>
                      <th><?= $_LANG->get('Datum') ?></th>
                      <th><?= $_LANG->get('Kunde') ?></th>
                      <th><?= $_LANG->get('Titel') ?></th>
                      <?php foreach ($columns as $column) { ?>
                          <th><?php echo $column->getTitle() ?></th>
                      <?php } ?>
                  </tr>
                  </thead>
                  <?php foreach ($marketjobs as $marketjob) { ?>
                      <tr>
                          <td><?php echo $marketjob->getId(); ?></td>
                          <td><?php echo date('d.m.y', $marketjob->getCrtdate()); ?></td>
                          <td><?php echo $marketjob->getBusinesscontact()->getNameAsLine(); ?></td>
                          <td><?php echo $marketjob->getTitle(); ?></td>
                          <?php foreach ($columns as $column) { ?>
                              <td><?php echo $marketjob->getColumnValue($column->getId()); ?></td>
                          <?php } ?>
                      </tr>
                  <?php } ?>
                  <tfoot>
                  <tr>
                      <th><?= $_LANG->get('ID') ?></th>
                      <th><?= $_LANG->get('Datum') ?></th>
                      <th><?= $_LANG->get('Kunde') ?></th>
                      <th><?= $_LANG->get('Titel') ?></th>
                      <?php foreach ($columns as $column) { ?>
                          <th><?php echo $column->getTitle() ?></th>
                      <?php } ?>
                  </tr>
                  </tfoot>
              </table>
          </div>
      </div>
</div>


<script language="JavaScript">
    $(document).ready(function () {
        var marketingtable = $('#marketing_table').DataTable({
            "scrollX": true,
            "aaSorting": [[1, "desc"]],
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

        $("#marketing_table tbody td").live('click', function () {
            var aPos = $('#marketing_table').dataTable().fnGetPosition(this);
            var aData = $('#marketing_table').dataTable().fnGetData(aPos[0]);
            document.location = 'index.php?page=libs/modules/marketing/marketing.edit.php&exec=edit&id=' + aData[0] + '&list=<?php echo $curr_list;?>';
        });
    });
</script>