<?//--------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       29.09.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

$delivterms = DeliveryTerms::getAllDeliveryConditions();
?>

<table width="100%">
	<tr>
		<td width="180" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
			<span style="font-size: 13px"><?=$_LANG->get('Lieferbedingungen')?></span>
		</td>
		<td><?=$savemsg?></td>
		<td width="300" class="content_header" align="right">
			<span style="font-size: 12px">
			<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit"><img src="images/icons/user--plus.png"><?=$_LANG->get('Lieferbedingungen hinzuf&uuml;gen')?></a>
			</span>
		</td>
	</tr>
</table>

<div class="box1">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<colgroup>
		<col with="20">
		<col with="190">
		<col>
		<col with="80">
		<?/**if($_CONFIG->shopActivation){?><td class="content_row_header"><col with="80"></td><?}**/?>
		<col with="100" >
	</colgroup>
	<tr>
		<td class="content_row_header"><?=$_LANG->get('ID')?></td>
		<td class="content_row_header"><?=$_LANG->get('Name')?></td>
		<td class="content_row_header"><?=$_LANG->get('Beschreibung')?></td>
		<td class="content_row_header"><?=$_LANG->get('Kosten')?></td>
		<?/**if($_CONFIG->shopActivation){?><td class="content_row_header" align="center"><?=$_LANG->get('Shop-Freigabe')?></td><?}**/?>
		<td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
	</tr>
	<?$x = 0;
	foreach($delivterms as $dt){?>
		<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
			<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&did=<?=$dt->getId()?>'">
				<?=$dt->getId()?>
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&did=<?=$dt->getId()?>'">
				<?=$dt->getName1()?>
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&did=<?=$dt->getId()?>'">
				<?=$dt->getComment()?>
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&did=<?=$dt->getId()?>'">
				<?=printPrice($dt->getCharges())?> <?=$_USER->getClient()->getCurrency()?>
			</td>
			<?/**if($_CONFIG->shopActivation){?>
				<td class="content_row pointer" align="center" 
					onclick="document.location='index.php?exec=edit&did=<?=$dt->getId()?>'">
					<img src="images/status/
					<? if ($dt->getShoprel() == 0){
							echo "red_small.gif";
						} else {
							echo "green_small.gif";
						}
					?> ">
				</td>
			<?}**/?>
			<td class="content_row">
				<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&did=<?=$dt->getId()?>"><img src="images/icons/pencil.png"></a>
				&ensp;
				<a class="icon-link" href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$dt->getId()?>')"><img src="images/icons/cross-script.png"></a>
			</td>
		</tr>
		<?$x++;
	}?>
</table>
</div>

