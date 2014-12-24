<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       11.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/calculation/order.class.php';
require_once 'orderimport.class.php';

$orders = Order::getAllOrders(Order::ORDER_NUMBER, Order::FILTER_CONFIRMED);

if($_REQUEST["exec"] == "transfer")
    require_once 'orderimport.transfer.php';

if($_REQUEST["exec"] == "") { ?>

<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Auftragsimport')?></td>
      <td align="right"><?=$savemsg?></td>
   </tr>
</table>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post">
<input type="hidden" name="exec" value="transfer">
<table width="800"><tr><td>
<div class="box1">
<table width="100%">
    <tr>
        <td class="content_row_clear" width="200"><?=$_LANG->get('Best&auml;tigte Auftr&auml;ge')?></td>
        <td class="content_row_clear">
            <select name="order_number" style="width:600px" class="text">
            <? 
            foreach($orders as $o)
            {
                echo '<option value="'.$o->getNumber().'">'.$o->getNumber().' - '.$o->getTitle().' - '.$o->getCustomer()->getNameAsLine().'</option>';
            }
            ?>
            </select>
        </td>
    </tr>
</table>
</div>
<table width="100%">
    <tr>
        <td class="content_row_clear" align="right">
            <input type="submit" value="<?=$_LANG->get('Auftrag importieren')?>">
        </td>
    </tr>
</table>
</td></tr></table>
</form>

<?  } ?>