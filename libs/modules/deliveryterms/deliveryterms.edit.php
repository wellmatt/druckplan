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

<div id="fl_menu">
	<div class="label">Quick Move</div>
	<div class="menu">
		<a href="#top" class="menu_item">Seitenanfang</a>
		<a href="index.php?page=<?=$_REQUEST['page']?>" class="menu_item">Zurück</a>
		<a href="#" class="menu_item" onclick="$('#user_form').submit();">Speichern</a>
	</div>
</div>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
					<?if($_REQUEST["id"]){
						echo $_LANG->get('Lieferbedingung bearbeiten');
					} else {
						echo $_LANG->get('Lieferbedingung anlegen');
					}?>
				</td>
			</h3>
	  </div>
	<div class="panel-body">
		<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="user_form" name="user_form"
			  class="form-horizontal" role="form"  onSubmit="return checkform(new Array(this.deliv_name, this.deliv_comment))">
			<input type="hidden" name="exec" value="edit" />
			<input type="hidden" name="subexec" value="save" />
			<input type="hidden" name="did" value="<?=$delterm->getId()?>" />


			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Name</label>
				<div class="col-sm-10">
					<input id="deliv_comment" name="dt_comment" type="text" class="form-control" value="<?=$delterm->getComment()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)"/>
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Beschreibung</label>
				<div class="col-sm-10">
					<input id="deliv_comment" name="dt_comment" type="text" class="form-control" value="<?=$delterm->getComment()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)"/>
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Kosten</label>
				<div class="col-sm-10">
					<div class="input-group">
						<input name="dt_charges"  type="text" class="form-control" value="<?=printPrice($delterm->getCharges())?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						<span class="input-group-addon">€</span>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Mwst</label>
				<div class="col-sm-10">
					<div class="input-group">
						<input name="dt_tax"  type="text" class="form-control" value="<?=printPrice($delterm->getTax())?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						<span class="input-group-addon">%</span>
					</div>
				</div>
			</div>
			<?/***if($_CONFIG->shopActivation){?>
			<tr>
			<td class="content_row_header"><?=$_LANG->get('Shop-Freigabe')?></td>
			<td class="content_row_clear">
			<input 	id="dt_shoprel" name="dt_shoprel" class="text" type="checkbox"
			value="1" <?if ($delterm->getShoprel() == 1) echo "checked"; ?>>
			</td>
			</tr>
			<?}**/?>
		</form>
	</div>
</div>

