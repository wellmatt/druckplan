<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/storage/storage.area.class.php';
require_once 'libs/modules/storage/storage.position.class.php';

$sarticles = Article::getAllArticlesNeedingStorage();

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
            Lagerartikel
                <span class="pull-right">
                     <?=$savemsg?>
                    <button class="btn btn-xs btn-success" onclick="document.location. href='index.php?page=libs/modules/storage/storage.edit.php&exec=new';"
                         <span class="glyphicons glyphicons-plus-sign"></span>
                    <?=$_LANG->get('Lagerplatz erstellen')?>
                    </button>
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
    </div>
    <br>
    <div class="table-responsive">
        <table  id="storagearttable" class="table table-hover">
            <thead>
            <tr>
                <th><?=$_LANG->get('Art.-Nr.')?></th>
                <th><?=$_LANG->get('Art.-Name')?></th>
                <th><?=$_LANG->get('Auf Lager')?></th>
                <th><?=$_LANG->get('Mindestmenge')?></th>
                <th><?=$_LANG->get('Lager Plätze')?></th>
            </tr>
            </thead>
            <tbody>
                <?php foreach ($sarticles as $sarticle) {
                    $spositions = StoragePosition::getAllForArticle($sarticle);
                    $stored = 0;
                    $storages = [];
                    foreach ($spositions as $sposition) {
                        $stored += $sposition->getAmount();
                        $storages[] = ['name'=>$sposition->getArea()->getName(),'id'=>$sposition->getArea()->getId(),'minstorage'=>$sposition->getMinAmount()];
                    }
                    ?>
                    <tr>
                        <td><?php echo $sarticle->getNumber();?></td>
                        <td><?php echo $sarticle->getTitle();?></td>
                        <td><?php echo $stored;?></td>
                        <td><?php echo StoragePosition::getTotalMinStoredForArticle($sarticle);?></td>
                        <td>
                            <?php
                            $i = 1;
                            foreach ($storages as $storage) {
                                echo '<a href="index.php?page=libs/modules/storage/storage.edit.php&exec=edit&id='.$storage['id'].'">';
                                if ($storage['minstorage']>0){
                                    echo '<b><u>'.$storage['name'].'</u></b>';
                                } else {
                                    echo $storage['name'];
                                }
                                echo '</a>';
                                if ($i < count($storages))
                                    echo ', ';
                                $i++;
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                } ?>
            </tbody>
            <tfoot>
            <tr>
                <th><?=$_LANG->get('Art.-Nr.')?></th>
                <th><?=$_LANG->get('Art.-Name')?></th>
                <th><?=$_LANG->get('Auf Lager')?></th>
                <th><?=$_LANG->get('Mindestmenge')?></th>
                <th><?=$_LANG->get('Lager Plätze')?></th>
            </tr>
            </tfoot>
        </table>
    </div>

</div>


<script type="text/javascript">
    $(document).ready(function() {
        var storagearttable = $('#storagearttable').DataTable( {
            "paging": true,
            "stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
            "pageLength": 50,
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
                        "sPdfMessage": "Contilas - Storage - Articles"
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
        });
        $('#search').keyup(function(){
            storagearttable.search( $(this).val() ).draw();
        });
    } );
</script>