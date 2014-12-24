<?//--------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       29.09.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

$delterm = new DeliveryTerms($_REQUEST["did"]);

if($_REQUEST["subexec"] == "save"){
	$delterm->setCharges((float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["dt_charges"]))));
	$delterm->setTax((float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["dt_tax"]))));
	$delterm->setName1(trim(addslashes($_REQUEST["dt_name"])));
	$delterm->setComment(trim(addslashes($_REQUEST["dt_comment"])));
	
	if($_CONFIG->shopActivation){
		$delterm->setShoprel((int)$_REQUEST["dt_shoprel"]);
	} else {
		$delterm->setShoprel(0);
	}
	
	$savemsg = getSaveMessage($delterm->save()).$DB->getLastError();
}?>

<table width="100%">
	<tr>
		<td width="300" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
				<?if($_REQUEST["id"]){
					echo $_LANG->get('Lieferbedingung bearbeiten');
				} else { 
					echo $_LANG->get('Lieferbedingung anlegen');
				}?>
		</td>
		<td align="right"><?=$savemsg?></td>
	</tr>
</table>
<form 	action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="user_form"
		onSubmit="return checkform(new Array(this.deliv_name, this.deliv_comment))">
<div class="box1">
	<input type="hidden" name="exec" value="edit" />
	<input type="hidden" name="subexec" value="save" />
	<input type="hidden" name="did" value="<?=$delterm->getId()?>" />
	<table width="500">
		<colgroup>
			<col width=180px>
			<col>
		</colgroup>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Name')?>*</td>
			<td class="content_row_clear">
				<input 	id="deliv_name" name="dt_name" style="width: 300px;" class="text" 
						value="<?=$delterm->getName1()?>" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)" />
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Beschreibung')?>*</td>
			<td class="content_row_clear">
				<input 	id="deliv_comment" name="dt_comment" style="width: 300px" class="text" 
						value="<?=$delterm->getComment()?>" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)" />
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Kosten')?></td>
			<td class="content_row_clear">
				<input name="dt_charges" style="width: 100px" class="text" 
						value="<?=printPrice($delterm->getCharges())?>" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
				<?=$_USER->getClient()->getCurrency()?>
			</td>
		</tr>
		<tr>
			<td class="content_row_header"><?=$_LANG->get('Mwst.')?></td>
			<td class="content_row_clear">
				<input name="dt_tax" style="width: 100px" class="text" 
						value="<?=printPrice($delterm->getTax())?>" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
				%
			</td>
		</tr>
		<?/***if($_CONFIG->shopActivation){?>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Shop-Freigabe')?></td>
				<td class="content_row_clear">
					<input 	id="dt_shoprel" name="dt_shoprel" class="text" type="checkbox" 
							value="1" <?if ($delterm->getShoprel() == 1) echo "checked"; ?>>
				</td>
			</tr>
		<?}**/?>
	</table>
</div>
<br/>
<?// Speicher & Navigations-Button ?>
<table width="100%">
	<colgroup>
		<col width="180">
		<col>
	</colgroup>
	<tr>
		<td class="content_row_header">
			<input type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
					onclick="window.location.href='index.php?page=<?=$_REQUEST["page"]?>'">
		</td>
		<td class="content_row_clear" align="right">
			<input type="submit" value="<?=$_LANG->get('Speichern')?>">
		</td>
	</tr>
</table>
</form>

