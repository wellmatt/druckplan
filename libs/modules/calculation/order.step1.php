<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       27.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//---------------------------------------------------------------------------------- 
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
            checkCalc(aData[0]);
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
            checkCalc(aData[0]);
        });
    } );
</script>

<script type="text/javascript">
message = '<?=$_LANG->get('Mit Wechsel des Produkts werden alle Kalkulationen/Teilauftr%E4ge gel%F6scht. Weiter?')?>';

function checkCalc(product)
{
    <?
	$calculations = Calculation::getAllCalculations($order, Calculation::ORDER_AMOUNT);
	if(count($calculations)==0){ 
        ?>
             location.href='index.php?page=<?=$_REQUEST["page"]?>&exec=edit&id=<?=$_REQUEST["id"]?>&selProduct='+product+'&step=2';
        <?
    } else {
    
        ?>
        if(product != <?=$order->getProduct()->getId()?>)
        {
            Check = confirm(unescape(message));
            if (Check == false)
                return false;
            else
            	location.href='index.php?page=<?=$_REQUEST["page"]?>&exec=edit&id=<?=$_REQUEST["id"]?>&selProduct='+product+'&clearorder=1&step=2';
        } else
        {
            var subexec = "<?=$_REQUEST["subexec"]?>";
            if (subexec == "copy"){
            	location.href='index.php?page=<?=$_REQUEST["page"]?>&exec=edit&subexec=copy&calc_id=<?=$calculations[0]->getId()?>&id=<?=$_REQUEST["id"]?>&step=2';
            } else {
            	location.href='index.php?page=<?=$_REQUEST["page"]?>&exec=edit&id=<?=$_REQUEST["id"]?>&step=4';
            }
        }
        <?
    }
?>
	
}
</script>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Produktauswahl</h3>
    </div>
    <br>
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
                foreach($products_nonindi as $p)
                {
                    ?>
                    <tr class="pointer" onclick="checkCalc(<?=$p->getId()?>)">
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
                <tr class="pointer" onclick="checkCalc(<?=$p->getId()?>)">
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