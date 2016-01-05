<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       23.08.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
static $x = 0;

$all_tradegroups = Tradegroup::getAllTradegroups(0);


function printSubTradegroups($parentId, $depth){
	$all_subgroups = Tradegroup::getAllTradegroups($parentId);
	foreach ($all_subgroups AS $subgroup){
		global $x;
		$x++;
		?>
		<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$subgroup->getId()?>'">
					 <?for ($i=0; $i<$depth+1;$i++) echo "&emsp;"?> 
					 <?=$subgroup->getTitle()?>
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$subgroup->getId()?>'">
					<?=$subgroup->getDesc()?>
				</td>
				<td class="content_row pointer" align="center" 
					onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$subgroup->getId()?>'">
					<img src="images/status/
					<? if ($subgroup->getShoprel() == 0){
							echo "red_small.gif";
						} else {
							echo "green_small.gif";
						}
					?> ">
				</td>
				<td class="content_row">
                <a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$subgroup->getId()?>"><img src="images/icons/pencil.png"></a>
				&ensp;
                <a href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$subgroup->getId()?>')"><img src="images/icons/cross-script.png"></a>            
            </td>
			</tr>
		<? printSubTradegroups($subgroup->getId(), $depth+1);
	}
}


?>

<table width="100%">
	<tr>
		<td width="200" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
			<span style="font-size: 13px"><?=$_LANG->get('Warengruppen')?></span>
		</td>
		<td><?=$savemsg?></td>
		<td width="200" class="content_header" align="right">
			<span style="font-size: 13px">
				<a href="index.php?page=<?=$_REQUEST['page']?>&exec=new"><img src="images/icons/drawer--plus.png"> <?=$_LANG->get('Warengruppe hinzuf&uuml;gen')?></a>
			<span style="font-size: 13px">
		</td>
	</tr>
</table>

<div class="box1">
	<table width="100%" cellpadding="0" cellspacing="0">
		<colgroup>
			<col width="200">
			<col>
			<?if($_CONFIG->shopActivation){?>
				<col width="100">
			<?}?>
			<col width="100">
		</colgroup>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Titel')?></td>
			<td class="content_row_header"><?=$_LANG->get('Beschreibung')?></td>
			<?if($_CONFIG->shopActivation){?>
				<td class="content_row_header"><?=$_LANG->get('Shop-Freigabe')?></td>
			<?}?>
			<td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
		</tr>
		<? $x = 0;
		foreach($all_tradegroups as $tradegroup){
			?>
			<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$tradegroup->getId()?>'">
					<?=$tradegroup->getTitle()?>
				</td>
				<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$tradegroup->getId()?>'">
					<?=$tradegroup->getDesc()?>
				</td>
				<?if($_CONFIG->shopActivation){?>
					<td class="content_row pointer" align="center" 
						onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$tradegroup->getId()?>'">
						<img src="images/status/
						<? if ($tradegroup->getShoprel() == 0){
								echo "red_small.gif";
							} else {
								echo "green_small.gif";
							}
						?> ">
					</td>
				<?}?>
				<td class="content_row">
                <a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$tradegroup->getId()?>"><img src="images/icons/pencil.png"></a>
				&ensp;
                <a href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$tradegroup->getId()?>')"><img src="images/icons/cross-script.png"></a>            
            </td>
			</tr>
		<?	printSubTradegroups($tradegroup->getID(), 0); 
			$x++;
		}// Ende foreach($all_tradegroups)
		?>
	</table>
</div>