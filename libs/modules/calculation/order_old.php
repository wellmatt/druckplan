<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       16.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'order.class.php';
$_REQUEST["id"] = (int)$_REQUEST["id"];

if($_REQUEST["setStatus"] != "")
{
    $order = new Order((int)$_REQUEST["id"]);
    $order->setStatus((int)$_REQUEST["setStatus"]);
    $savemsg = getSaveMessage($order->save());
}

if($_REQUEST["exec"] == "delete")
{
    $order = new Order($_REQUEST["id"]);
    $order->delete();
}

if(($_REQUEST["exec"] == "copy" && !$_REQUEST['cust_id']) || ($_REQUEST["exec"] == "edit" && !$_REQUEST['cust_id']))
{
    require_once 'order.edit.php';
} else if($_REQUEST["exec"] == "new")
{
    require_once 'order.new.php';
} else
{

    if(!empty($_GET['s'])) {
        $orders = Order::getAllOrdersByNumber($_GET['s'], Order::ORDER_NUMBER);
    } else {
		$collectiveinvoices = CollectiveInvoice::getAllCollectiveInvoice(CollectiveInvoice::ORDER_CRTDATE_DESC);
        $orders = Order::getAllOrders(Order::ORDER_NUMBER);
    }
	
	if(!empty($_REQUEST['cust_id'])){
		$collectiveinvoices = CollectiveInvoice::getAllCollectiveInvoiceForBcon(CollectiveInvoice::ORDER_CRTDATE_DESC, (int)$_REQUEST['cust_id']);
        $orders = Order::getAllOrdersByCustomer(Order::ORDER_NUMBER, (int)$_REQUEST['cust_id']);
	}

?>
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/date-uk.js"></script>

<script type="text/javascript">


jQuery.fn.dataTableExt.oSort['uk_date-asc']  = function(a,b) {
    var ukDatea = a.split('.');
    var ukDateb = b.split('.');
     
    var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;
     
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};
 
jQuery.fn.dataTableExt.oSort['uk_date-desc'] = function(a,b) {
    var ukDatea = a.split('.');
    var ukDateb = b.split('.');
     
    var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
    var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;
     
    return ((x < y) ? 1 : ((x > y) ?  -1 : 0));
};

$(document).ready(function() {
    var orders = $('#orders').DataTable( {
        // "scrollY": "1000px",
        "paging": true,
		"stateSave": true,
		"dom": 'flrtip',
		"pageLength": 50,
		"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Alle"] ],
		"aoColumnDefs": [ { "sType": "uk_date", "aTargets": [ 4 ] } ],
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
} );
</script>

<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Vorg&auml;nge')?></td>
      <td><?=$savemsg?></td>
      <td width="200" class="content_header" align="right">
      	<a class="icon-link" href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=select_user"><img src="images/icons/calculator--plus.png">
      	<span style="font-size:13px"><?=$_LANG->get('Vorgang hinzuf&uuml;gen')?></span></a>
      </td>
      <td width="200" class="content_header" align="right">
      	<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=new"><img src="images/icons/calculator--plus.png">
      	<span style="font-size:13px"><?=$_LANG->get('Kalkulation hinzuf&uuml;gen')?></span></a>
      </td>
   </tr>
</table>

<div class="box1">
<!--
    <form action="" method="post">
        <label for="s">Suche nach Auftragsnummer: </label>
        <input type="text" name="s" id="s" value="<?= $_GET['s'] ?>" />
        <button type="submit">Suche starten &raquo;</button>
    </form>
-->

<table id="orders" width="100%" cellpadding="0" cellspacing="0">
	<thead>
		<tr>
			<th width="100"><?=$_LANG->get('Nummer')?></th>
			<th><?=$_LANG->get('Kunde')?></th>
			<th><?=$_LANG->get('Titel')?></th>
			<th width="200"><?=$_LANG->get('Fremdleistungen')?></th>
			<th width="90"><?=$_LANG->get('Angelegt am')?></th>
			<th width="150"><?=$_LANG->get('Status')?></th>
			<th width="80"><?=$_LANG->get('Optionen')?></th>
		</tr>
	</thead>
    <? $x = 0;
    foreach($orders as $o)
    {
        ?>
        <tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
            <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$o->getId()?>&step=4'"><?=$o->getNumber()?>&nbsp;</td>
            <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$o->getId()?>&step=4'">
            	<?=$o->getCustomer()->getNameAsLine()?>&nbsp;
            </td>
            <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$o->getId()?>&step=4'"><?=$o->getTitle()?>&nbsp;</td>
            <td class="content_row pointer">
            <?	$all_fl = $o->getFL();
            
            	/*echo "<pre>";
            	var_dump($all_fl);
            	echo "</pre>";*/
            
            	foreach ($all_fl AS $fl){
					$tmp_supp = new BusinessContact($fl->getSupplierID()); 
            		if($fl->getSupplierStatus() == 0){ $status = Machineentry::SUPPLIER_STATUS_0; $img="red.gif";};
            		if($fl->getSupplierStatus() == 1){ $status = Machineentry::SUPPLIER_STATUS_1; $img="orange.gif";};
            		if($fl->getSupplierStatus() == 2){ $status = Machineentry::SUPPLIER_STATUS_2; $img="lila.gif";};
            		if($fl->getSupplierStatus() == 3){ $status = Machineentry::SUPPLIER_STATUS_3; $img="green.gif";};
            		
            		$title = $status." \n";
            		if ($fl->getSupplierID() > 0 ) $title .= $tmp_supp->getNameAsLine()."\n";
            		if ($fl->getSupplierSendDate() > 0 ) $title .= "Liefer-/Bestelldatum: ".date("d.m.Y", $fl->getSupplierSendDate())." \n";
            		if ($fl->getSupplierReceiveDate() > 0 ) $title .= "Retour: ".date("d.m.Y", $fl->getSupplierReceiveDate())." \n";
            		$title .= $fl->getSupplierInfo()." \n";
            		?>
            		<img src="images/status/<?=$img?>" alt="<?=$status?>" title="<?=$title?>" 
            			 onClick="document.getElementById('span_fl_<?=$o->getId()?>_<?=$fl->getId()?>').style.display=''; ">
            		<span id="span_fl_<?=$o->getId()?>_<?=$fl->getId()?>" style="display:none">
            			<table>
						<tr>
							<td>
								<?=$tmp_supp->getNameAsLine();?>
							</td>
						</tr>
						<tr>
                        	<td><b><?=$_LANG->get('Lieferung')?>:</b>
                        		<? if ($fl->getSupplierSendDate() > 0 ) echo date("d.m.Y", $fl->getSupplierSendDate());?> 
							</td>
						</tr>
						<tr>
							<td><b><?=$_LANG->get('Retour')?></b>
								<? if ($fl->getSupplierReceiveDate() > 0 ) echo date("d.m.Y", $fl->getSupplierReceiveDate());?>
							</td>
						</tr>
						<tr>
							<td><b><?=$_LANG->get('Info')?>:</b> 
								<?=$fl->getSupplierInfo()?>
							</td>
						</tr>
						</table>
            		</span>
            <?	} ?>
            	&nbsp;
            </td>
            <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$o->getId()?>&step=4'">
            	<?=date('d.m.Y', $o->getCrtdat())?>
            </td>
            <td class="content_row">
                <table border="0" cellpadding="1" cellspacing="0">
                <tr>
                    <td width="25">
                        <a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$o->getId()?>&setStatus=1">
                            <? 
                            echo '<img class="select" src="./images/status/';
                            if($o->getStatus() == 1)
                                echo 'red.gif';
                            else
                                echo 'black.gif';
                            echo '" title="'.getOrderStatus(1).'">';
                            ?>
                        </a>
                    </td>
                    <td width="25">
                        <a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$o->getId()?>&setStatus=2">
                            <? 
                            echo '<img class="select" src="./images/status/';
                            if($o->getStatus() == 2)
                                echo 'orange.gif';
                            else
                                echo 'black.gif';
                            echo '" title="'.getOrderStatus(2).'">';
                            ?>
                        </a>
                    </td>
                    <td width="25">
                        <a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$o->getId()?>&setStatus=3">
                            <? 
                            echo '<img class="select" src="./images/status/';
                            if($o->getStatus() == 3)
                                echo 'yellow.gif';
                            else
                                echo 'black.gif';
                            echo '" title="'.getOrderStatus(3).'">';
                            ?>
                        </a>
                    </td>
                    <td width="25">
                        <a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$o->getId()?>&setStatus=4">
                            <? 
                            echo '<img class="select" src="./images/status/';
                            if($o->getStatus() == 4)
                                echo 'lila.gif';
                            else
                                echo 'black.gif';
                            echo '" title="'.getOrderStatus(4).'">';
                            ?>
                        </a>
                    </td>
                    <td width="25">
                        <a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$o->getId()?>&setStatus=5">
                            <? 
                            echo '<img class="select" src="./images/status/';
                            if($o->getStatus() == 5)
                                echo 'green.gif';
                            else
                                echo 'black.gif';
                            echo '" title="'.getOrderStatus(5).'">';
                            ?>
                        </a>
                    </td>
                </tr>
                </table>                                  
            </td>
            <td class="content_row">
                <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$o->getId()?>&step=4"><img src="images/icons/pencil.png"></a>
                <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=copy&id=<?=$o->getId()?>"><img src="images/icons/scripts.png"></a>
                <a class="icon-link" href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$o->getId()?>')"><img src="images/icons/cross-script.png"></a>
            </td>
        </tr>
    <? $x++;       
    }
    foreach($collectiveinvoices as $ci)
    {
        ?>
        <tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
            <td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid=<?=$ci->getId()?>'"><?=$ci->getNumber()?>&nbsp;</td>
            <td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid=<?=$ci->getId()?>'">
            	<?=$ci->getBusinessContact()->getNameAsLine()?>&nbsp;
            </td>
            <td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid=<?=$ci->getId()?>'"><?=$ci->getTitle()?>&nbsp;</td>
            <td class="content_row pointer">&nbsp;</td>
            <td class="content_row pointer" onclick="document.location='index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid=<?=$ci->getId()?>'">
            	<?=date('d.m.Y', $ci->getcrtdate())?>
            </td>
            <td class="content_row">
                <table border="0" cellpadding="1" cellspacing="0">
                <tr>
                    <td width="25">
                        <a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&ciid=<?= $ci->getId() ?>&exec=setState&state=1">
                            <? 
                            echo '<img class="select" src="./images/status/';
                            if($ci->getStatus() == 1)
                                echo 'red.gif';
                            else
                                echo 'black.gif';
                            echo '" title="'.getOrderStatus(1).'">';
                            ?>
                        </a>
                    </td>
                    <td width="25">
                        <a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&ciid=<?= $ci->getId() ?>&exec=setState&state=2">
                            <? 
                            echo '<img class="select" src="./images/status/';
                            if($ci->getStatus() == 2)
                                echo 'orange.gif';
                            else
                                echo 'black.gif';
                            echo '" title="'.getOrderStatus(2).'">';
                            ?>
                        </a>
                    </td>
                    <td width="25">
                        <a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&ciid=<?= $ci->getId() ?>&exec=setState&state=3">
                            <? 
                            echo '<img class="select" src="./images/status/';
                            if($ci->getStatus() == 3)
                                echo 'yellow.gif';
                            else
                                echo 'black.gif';
                            echo '" title="'.getOrderStatus(3).'">';
                            ?>
                        </a>
                    </td>
                    <td width="25">
                        <a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&ciid=<?= $ci->getId() ?>&exec=setState&state=4">
                            <? 
                            echo '<img class="select" src="./images/status/';
                            if($ci->getStatus() == 4)
                                echo 'lila.gif';
                            else
                                echo 'black.gif';
                            echo '" title="'.getOrderStatus(4).'">';
                            ?>
                        </a>
                    </td>
                    <td width="25">
                        <a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&ciid=<?= $ci->getId() ?>&exec=setState&state=5">
                            <? 
                            echo '<img class="select" src="./images/status/';
                            if($ci->getStatus() == 5)
                                echo 'green.gif';
                            else
                                echo 'black.gif';
                            echo '" title="'.getOrderStatus(5).'">';
                            ?>
                        </a>
                    </td>
                </tr>
                </table>                                  
            </td>
            <td class="content_row">
                <a class="icon-link" href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid=<?=$ci->getId()?>"><img src="images/icons/pencil.png"></a>
                <a class="icon-link" href="#"	onclick="askDel('index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=delete&del_id=<?=$ci->getId()?>')"><img src="images/icons/cross-script.png"></a>
            </td>
        </tr>
    <? $x++;       
    }
    ?>
</table>
</div>
<? } ?>