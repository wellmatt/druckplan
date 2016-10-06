<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       21.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'chromaticity.class.php';
$_REQUEST["id"] = (int)$_REQUEST["id"];

if($_REQUEST["exec"] == "delete")
{
    $chr = new Chromaticity($_REQUEST["id"]);
    $savemsg = getSaveMessage($chr->delete());
}

if($_REQUEST["exec"] == "copy" || $_REQUEST["exec"] == "edit")
{
    require_once 'chromaticity.edit.php';
} else
{
    $chromaticities = Chromaticity::getAllChromaticities(Chromaticity::ORDER_NAME);
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
            var chromatable = $('#chromatable').DataTable( {
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

            $("#chromatable tbody td:not(:nth-child(7))").live('click',function(){
                var aPos = $('#chromatable').dataTable().fnGetPosition(this);
                var aData = $('#chromatable').dataTable().fnGetData(aPos[0]);
                document.location='index.php?page=libs/modules/chromaticity/chromaticity.php&exec=edit&id='+aData[0];
            });
        } );
    </script>

    <div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
                <img  src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
                Farbigkeit
                <span class="pull-right">
                    <button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=<?=$_REQUEST['page']?>&exec=edit';">
                        <span class="glyphicons glyphicons-plus"></span>
                        <?=$_LANG->get('Farbigkeit hinzuf&uuml;gen')?>
                    </button>
                </span>
            </h3>
	  </div>
        <br>
      <div class="table-responsive">
          <table id="chromatable" class="table table-hover">
              <thead>
                  <tr>
                      <th class="content_row_header"><?=$_LANG->get('ID')?></th>
                      <th class="content_row_header"><?=$_LANG->get('Bezeichnung')?></th>
                      <th class="content_row_header"><?=$_LANG->get('Vorderseite')?></th>
                      <th class="content_row_header"><?=$_LANG->get('R&uuml;ckseite')?></th>
                      <th class="content_row_header"><?=$_LANG->get('Sch&ouml;n-/Widerdruck')?></th>
                      <th class="content_row_header"><?=$_LANG->get('Aufschlag')?></th>
                      <th class="content_row_header"><?=$_LANG->get('Optionen')?></th>
                  </tr>
              </thead>
              <? $x = 1;
              foreach($chromaticities as $chr)
              {
                  ?>
                  <tr class="<?=getRowColor($x)?>">
                      <td class="content_row"><?=$chr->getId()?></td>
                      <td class="content_row"><?=$chr->getName()?></td>
                      <td class="content_row"><?=$chr->getColorsFront()?></td>
                      <td class="content_row"><?=$chr->getColorsBack()?></td>
                      <td class="content_row">
                          <?if($chr->getReversePrinting())
                              echo $_LANG->get('Sch&ouml;n- und Widerdruck');
                          else
                              echo $_LANG->get('Sch&ouml;ndruck');
                          ?></td>
                      <td class="content_row"><?=printPrice($chr->getMarkup())?> %</td>
                      <td class="content_row">
                          <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=copy&id=<?=$chr->getId()?>"><span class="glyphicons glyphicons-copy"></span></a>
                          <a class="icon-link" href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$chr->getId()?>')"><span class="glyphicons glyphicons-remove"></span></a>
                      </td>
                  </tr>
                  <?
                  $x++;}
              ?>
          </table>
      </div>
</div>
<? } ?>