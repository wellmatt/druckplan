<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       27.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//---------------------------------------------------------------------------------- 
?>
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
<div class="box1">
<table width="100%">
    <colgroup>
        <col width="10%">
        <col width="23%">
        <col width="10%">
        <col width="23%">
        <col width="10%">
        <col>
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Kundennummer')?>:</td>
        <td class="content_row_clear"><?=$order->getCustomer()->getCustomernumber()?></td>
        <td class="content_row_header"><?=$_LANG->get('Vorgang')?>:</td>
        <td class="content_row_clear"><?=$order->getCustomer()->getId()?></td>
        <td class="content_row_header"><?=$_LANG->get('Auftrag')?>:</td>
        <td class="content_row_clear"><?=$order->getNumber()?></td>
        <td class="content_row_header"><?=$_LANG->get('Telefon')?></td>
        <td class="content_row_clear"><?=$order->getCustomer()->getPhone()?></td>
    </tr>
    <tr>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Name')?>:</td>
        <td class="content_row_clear" valign="top"><?=nl2br($order->getCustomer()->getNameAsLine())?></td>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Adresse')?>:</td>
        <td class="content_row_clear"  valign="top"><?=nl2br($order->getCustomer()->getAddressAsLine())?></td>
        <td class="content_row_header"  valign="top"><?=$_LANG->get('E-Mail')?></td>
        <td class="content_row_clear" valign="top"><?=$order->getCustomer()->getEmail()?></td>
    </tr>
</table>
</div>
<br>
<h1><?=$_LANG->get('Produktauswahl')?></h1>
<div class="box1">
    <table width="100%" cellpadding="0" cellspacing="0">
        <colgroup>
            <col width="163">
            <col width="163">
            <col width="164">
            <col width="163">
            <col width="163">
            <col width="164">
        </colgroup>
        <tr>
            <? $x = 1;
            foreach($products_nonindi as $p)
            {
                ?>
                <td class="content_row_clear" align="center" style="height:130px" valign="bottom">
                    <!--  a href="index.php?exec=edit&id=<?=$order->getId()?>&selProduct=<?=$p->getId()?>&delCalc=1&step=1"-->
                     <img src="images/products/<?=$p->getPicture()?>" class="pointer" onclick="checkCalc(<?=$p->getId()?>)">
                    <!--  /a-->
                    <div class="producttitle"><?=$p->getName()?></div>
                </td>
                <?
                if($x % 6 == 0)
                    echo '</tr><tr>';
                $x++; 
            } 
            
            for($i = $x; $i <= 6; $i++)
                echo '<td class="content_row_clear">&nbsp;</td>';
                
            ?>
        </tr>
    </table>
</div>
<p>&nbsp;</p>
<?php if(!empty($individualProducts)) : ?>
<h1><?=$_LANG->get('Individuelle Produkte')?></h1>
<div class="box1">
    <table width="100%" cellpadding="0" cellspacing="0">
        <colgroup>
            <col width="163">
            <col width="163">
            <col width="164">
            <col width="163">
            <col width="163">
            <col width="164">
        </colgroup>
        <tr>
            <? $x = 1;
            foreach($individualProducts as $p)
            {
                ?>
                <td class="content_row_clear" align="center" style="height:130px" valign="bottom">
                    <!--  a href="index.php?exec=edit&id=<?=$order->getId()?>&selProduct=<?=$p->getId()?>&delCalc=1&step=1"-->
                     <img src="images/products/<?=$p->getPicture()?>" class="pointer" onclick="checkCalc(<?=$p->getId()?>)">
                    <!--  /a-->
                    <div class="producttitle"><?=$p->getName()?></div>
                </td>
                <?
                if($x % 6 == 0)
                    echo '</tr><tr>';
                $x++; 
            } 
            
            for($i = $x; $i <= 6; $i++)
                echo '<td class="content_row_clear">&nbsp;</td>';
                
            ?>
        </tr>
    </table>
</div>
<?php endif; ?>