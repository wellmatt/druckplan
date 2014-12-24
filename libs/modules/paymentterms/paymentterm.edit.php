<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			03.09.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

?>

<table width="100%">
	<tr>
		<td width="300" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
			<? if($_REQUEST["id"]){
				echo $_LANG->get('Zahlungsbedingung bearbeiten');
			} else{
				echo $_LANG->get('Zahlungsbedingung anlegen');
			}?>
		</td>
		<td align="right"><?=$savemsg?></td>
	</tr>
</table>
<form 	action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="user_form"
		onsubmit="return checkform(new Array(this.pt_name,this.pt_comment,this.pt_netto_days))">
	<input type="hidden" name="exec" value="save">
	<input type="hidden" name="pay_id" value="<?=$payment->getId()?>">
	<div class="box1">
	<table>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Name')?>*</td>
			<td class="content_row_clear">
				<input	name="pt_name" id="pt_name" style="width: 300px;"  
						value="<?=$payment->getName()?>" class="text"
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Beschreibung')?>*</td>
			<td class="content_row_clear">
				<input 	id="pt_comment" name="pt_comment" style="width: 300px;" 
						class="text" value="<?=$payment->getComment()?>" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Skonto Frist 1')?></td>
			<td class="content_row_clear">
				<input	name="pt_skonto_days1" style="width: 80px" 
						class="text" value="<?=$payment->getSkontodays1()?>" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
				<?=$_LANG->get('Tage')?>
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Skonto 1')?></td>
			<td class="content_row_clear">
				<input	name="pt_skonto1" style="width: 80px" 
						class="text" value="<?=$payment->getSkonto1()?>" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)"> %
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Skonto Frist 2')?></td>
			<td class="content_row_clear">
				<input	name="pt_skonto_days2" style="width: 80px" 
						class="text" value="<?=$payment->getSkontodays2()?>" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
				<?=$_LANG->get('Tage')?>
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Skonto 2')?></td>
			<td class="content_row_clear">
				<input	name="pt_skonto2" style="width: 80px" 
						class="text" value="<?=$payment->getSkonto2()?>" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)"> %
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Nettotage')?>*</td>
			<td class="content_row_clear">
				<input 	id="pt_nettodays" name="pt_nettodays" style="width: 80px" 
						class="text" value="<?=$payment->getNettodays()?>" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<?/**if($_CONFIG->shopActivation){?>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Shop-Freigabe')?></td>
			<td class="content_row_clear">
				<input 	id="pt_shoprel" name="pt_shoprel" class="text" type="checkbox" 
						value="1" <?if ($payment->getShoprel() == 1) echo "checked"; ?>>
			</td>
		</tr>
		<?}**/?>
	</table>
	</div>
	<br/>
	<table width="100%">
		<colgroup>
			<col width="180">
			<col>
		</colgroup>
		<tr>
			<td class="content_row_header">
				        	
				<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
						onclick="window.location.href='index.php?page=<?=$_REQUEST["page"]?>'">
			</td>
			<td class="content_row_clear" align="right">
				<input type="submit" value="<?=$_LANG->get('Speichern')?>">
			</td>
		</tr>
	</table>
</form>