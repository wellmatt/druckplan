<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       13.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'machine.class.php';
$_REQUEST["id"] = (int)$_REQUEST["id"];

if($_REQUEST["exec"] == "delete")
{
    $machine = new Machine($_REQUEST["id"]);
    $machine->delete(); 
}

if($_REQUEST["exec"] == "copy" || $_REQUEST["exec"] == "edit")
{
    require_once 'machines.edit.php';
} else 
{

$machines = Machine::getAllMachines(Machine::ORDER_GROUP_NAME);

?>
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
    <script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="jscripts/datatable/numeric-comma.js"></script>
    <script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">
    <script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.tableTools.js"></script>

    <script>
        $(document).ready(function() {
            $('.datatablegeneric').DataTable( {
                "paging": true,
                "stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
                "pageLength": <?php echo $perf->getDt_show_default();?>,
                "dom": 'flrtip',
                "lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Alle"] ],
                "language": {
                    "url": "jscripts/datatable/German.json"
                }
            } );
        } );
    </script>

    <div class="panel panel-default">
          <div class="panel-heading">
                <h3 class="panel-title">
                    Maschinen
                    <span class="pull-right">
                        <button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=<?=$_REQUEST['page']?>&exec=edit';">
                            <span class="glyphicons glyphicons-plus"></span>
                            <?=$_LANG->get('Maschine hinzuf&uuml;gen')?>
                        </button>
                    </span>
                </h3>
          </div>

        <div class="table-responsive">
            <table class="table table-hover datatablegeneric">
                <thead>
                    <tr>
                        <th class="content_row_header"><?=$_LANG->get('ID')?></th>
                        <th class="content_row_header"><?=$_LANG->get('Maschinengruppe')?></th>
                        <th class="content_row_header"><?=$_LANG->get('Bezeichnung')?></th>
                        <th class="content_row_header"><?=$_LANG->get('Preis')?></th>
                        <th class="content_row_header"><?=$_LANG->get('Maschinentyp')?></th>
                        <th class="content_row_header"><?=$_LANG->get('Einheit')?></th>
                        <th class="content_row_header"><?=$_LANG->get('Importiert')?></th>
                        <th class="content_row_header"><?=$_LANG->get('Optionen')?></th>
                    </tr>
                </thead>
                <? $x = 0;
                foreach($machines as $m)
                {?>
                    <tr class=<?=getRowColor($x)?>>
                        <td class="content_row"><?=$m->getId()?></td>
                        <td class="content_row"><?=$m->getGroup()->getName()?></td>
                        <td class="content_row"><?=$m->getName()?></td>
                        <td class="content_row"><?=printPrice($m->getPrice(),4)?> <?=$_USER->getClient()->getCurrency()?></td>
                        <td class="content_row">
                            <? if($m->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) echo $_LANG->get('Druckmaschine Offset')?>
                            <? if($m->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL) echo $_LANG->get('Druckmaschine Digital')?>
                            <? if($m->getType() == Machine::TYPE_CTP) echo $_LANG->get('Computer To Plate')?>
                            <? if($m->getType() == Machine::TYPE_FOLDER) echo $_LANG->get('Falzmaschine')?>
                            <? if($m->getType() == Machine::TYPE_CUTTER) echo $_LANG->get('Schneidemaschine')?>
                            <? if($m->getType() == Machine::TYPE_LASERCUTTER) echo $_LANG->get('Stanze / Laser-Stanze')?>
                            <? if($m->getType() == Machine::TYPE_LAGENFALZ) echo $_LANG->get('Lagenfalz-/Zusammentragmaschine')?>
                            <? if($m->getType() == Machine::TYPE_SAMMELHEFTER) echo $_LANG->get('Sammelhefter')?>
                            <? if($m->getType() == Machine::TYPE_MANUELL) echo $_LANG->get('Manuelle Arbeit')?>
                            <? if($m->getType() == Machine::TYPE_OTHER) echo $_LANG->get('Andere')?>
                        </td>
                        <td class="content_row">
                            <? if($m->getPriceBase() == Machine::PRICE_AUFLAGE) echo $_LANG->get('nach Auflage')?>
                            <? if($m->getPriceBase() == Machine::PRICE_BOGEN) echo $_LANG->get('nach Bogen')?>
                            <? if($m->getPriceBase() == Machine::PRICE_DRUCKPLATTE) echo $_LANG->get('nach Druckplatten')?>
                            <? if($m->getPriceBase() == Machine::PRICE_MINUTE) echo $_LANG->get('nach Minute')?>
                            <? if($m->getPriceBase() == Machine::PRICE_PAUSCHAL) echo $_LANG->get('pauschal')?>
                            <? if($m->getPriceBase() == Machine::PRICE_VARIABEL) echo $_LANG->get('variabel')?>
                        </td>
                        <td class="content_row">
                            <? if($m->getLectorId()) echo '<span class="error">Importiert</span>'; else echo '&nbsp;'?>
                        </td>
                        <td class="content_row">
                            <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$m->getId()?>"><span class="glyphicons glyphicons-wrench"></span></a>
                            <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=copy&id=<?=$m->getId()?>"><span class="glyphicons glyphicons-file"></span></a>
                            <a class="icon-link" href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$m->getId()?>')"><span style="color: red" class="glyphicons glyphicons-remove"></span></a>
                        </td>
                    </tr>

                    <? $x++; }
                ?>
            </table>
        </div>
    </div>
<?  } ?>