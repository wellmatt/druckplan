<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       15.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'product.class.php';
$_REQUEST["id"] = (int)$_REQUEST["id"];

if($_REQUEST["exec"] == "delete")
{
    $product = new Product($_REQUEST["id"]);
    $product->delete();
}

if($_REQUEST["exec"] == "copy" || $_REQUEST["exec"] == "edit")
{
    require_once 'products.edit.php';
} else
{

$products = Product::getAllProducts();

?>

<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Produkte')?></td>
      <td><?=$savemsg?></td>
      <td width="200" class="content_header" align="right"><a class=" icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit"><img src="images/icons/zone--plus.png"> <?=$_LANG->get('Produkt hinzuf&uuml;gen')?></a></td>
   </tr>
</table>

<div class="box1">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <colgroup>
        <col width="20">
        <col width="200">
        <col>
        <col width="150">
		<?/*if($_CONFIG->shopActivation){?><col width="100"><?}*/?>
        <col width="100">
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('ID')?></td>
        <td class="content_row_header"><?=$_LANG->get('Produkt')?></td>
        <td class="content_row_header"><?=$_LANG->get('Beschreibung')?></td>
        <td class="content_row_header"><?=$_LANG->get('Typ')?></td>
        <?/*if($_CONFIG->shopActivation){?><td class="content_row_header"><?=$_LANG->get('Shop-Freigabe')?></td><?}*/?>
        <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
    </tr>
    <? 
    $x = 0;
    foreach ($products as $p)
    {?>
        <tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
            <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$p->getId()?>'">
            	<?=$p->getId()?>&nbsp;
            </td>
            <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$p->getId()?>'">
            	<?=$p->getName()?>&nbsp;
            </td>
            <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$p->getId()?>'">
            	<?=$p->getDescription()?>&nbsp;
            </td>
            <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$p->getId()?>'">
                <?
                    if($p->getType() == Product::TYPE_NORMAL) echo $_LANG->get('Normal');
                    else if ($p->getType() == Product::TYPE_BOOKPRINT) echo $_LANG->get('Buchdruck');
                ?>&ensp;
            </td>
            <?/*if($_CONFIG->shopActivation){?>
            	<td class="content_row pointer" align="center" 
					onclick="document.location='index.php?exec=edit&id=<?=$p->getId()?>'">
					<img src="images/status/
					<? if ($p->getShoprel() == 0){
							echo "red_small.gif";
						} else {
							echo "green_small.gif";
						}
					?> ">
				</td>
			<?}*/?>
            <td class="content_row">
                <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$p->getId()?>"><img src="images/icons/pencil.png"></a>
                <a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=copy&id=<?=$p->getId()?>"><img src="images/icons/scripts.png"></a>
                <a class="icon-link" href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$p->getId()?>')"><img src="images/icons/cross-script.png"></a>
            </td>
        </tr>
    <? $x++; } ?>
</table>
</div>
<? } ?>