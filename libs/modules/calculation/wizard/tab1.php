<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

$products_nonindi = Product::getAllProductsByIndividuality();
$individualProducts = Product::getAllProductsByIndividuality(true);

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Produktauswahl</h3>
    </div>
    <div class="table-responsive">
        <table id="table_products_nonindi" class="table table-hover">
            <thead>
            <tr>
                <th>ID</th>
                <th>Bild</th>
                <th>Produkt</th>
            </tr>
            </thead>
            <tbody>
            <?
            foreach ($products_nonindi as $p) {
                ?>
                <tr class="pointer" onclick="selectProduct(this,<?php echo $p->getId();?>);">
                    <td><?= $p->getId() ?></td>
                    <td><img src="images/products/<?= $p->getPicture() ?>"></td>
                    <td><?= $p->getName() ?></td>
                </tr>
                <?
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<?php if(!empty($individualProducts)){ ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Individuelle Produkte</h3>
        </div>
        <div class="table-responsive">
            <table id="table_individualProducts" class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Bild</th>
                    <th>Produkt</th>
                </tr>
                </thead>
                <tbody>
                <?
                foreach($individualProducts as $p)
                {
                    ?>
                    <tr class="pointer" onclick="selectProduct(this,<?php echo $p->getId();?>">
                        <td><?=$p->getId()?></td>
                        <td><img src="images/products/<?=$p->getPicture()?>"></td>
                        <td><?=$p->getName()?></td>
                    </tr>
                    <?
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
<?php } ?>


<!-- DataTables -->
<style>
    .highlighttr {
        background-color: aqua !important;
    },
    .highlighttr td {
        background-color: aqua !important;
    }
</style>
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
<link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.bootstrap.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.tableTools.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
        var table_products_nonindi = $('#table_products_nonindi').DataTable({
            "paging": true,
            "stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
            "pageLength": <?php echo $perf->getDt_show_default();?>,
            "dom": 'flrtip',
            "lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
            "language": {
                "url": "jscripts/datatable/German.json"
            },
        });

        $("#table_products_nonindi tbody td").live('click',function(){
            var aPos = $('#table_products_nonindi').dataTable().fnGetPosition(this);
            var aData = $('#table_products_nonindi').dataTable().fnGetData(aPos[0]);
            $('#product').val(aData[0]);
        });

        var table_individualProducts = $('#table_individualProducts').DataTable({
            "paging": true,
            "stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
            "pageLength": <?php echo $perf->getDt_show_default();?>,
            "dom": 'flrtip',
            "lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
            "language": {
                "url": "jscripts/datatable/German.json"
            },
        });

        $("#table_individualProducts tbody td").live('click',function(){
            var aPos = $('#table_individualProducts').dataTable().fnGetPosition(this);
            var aData = $('#table_individualProducts').dataTable().fnGetData(aPos[0]);
            $('#product').val(aData[0]);
        });
    } );
</script>
