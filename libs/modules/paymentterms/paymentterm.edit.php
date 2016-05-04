<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			03.09.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

?>
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
				<? if($_REQUEST["id"]){
					echo $_LANG->get('Zahlungsbedingung bearbeiten');
				} else{
					echo $_LANG->get('Zahlungsbedingung anlegen');
				}?>
				<span class="pull-right"><?=$savemsg?></span>
			</h3>
	  </div>
	<div class="panel-body">
		<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="user_form" name="user_form"
			class="form-horizontal" role="form">
			<input type="hidden" name="exec" value="save">
			<input type="hidden" name="pay_id" value="<?=$payment->getId()?>">

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Name</label>
				<div class="col-sm-10">
					<input name="pt_name" id="pt_name" type="text" class="form-control" value="<?=$payment->getName()?> " onfocus="markfield(this,0)" onblur="markfield(this,1)">
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Beschreibung</label>
				<div class="col-sm-10">
					<input id="pt_comment" name="pt_comment" type="text" class="form-control" value="<?=$payment->getComment()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Skonto Frist 1</label>
				<div class="col-sm-10">
					<div class="input-group">
						<input	name="pt_skonto_days1" type="text" class="form-control" value="<?=$payment->getSkontodays1()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						<span class="input-group-addon">Tage</span>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Skonto 1</label>
				<div class="col-sm-10">
					<div class="input-group">
						<input name="pt_skonto1" type="text" class="form-control" value="<?=$payment->getSkonto1()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						<span class="input-group-addon">%</span>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Skonto Frist 2</label>
				<div class="col-sm-10">
					<div class="input-group">
						<input name="pt_skonto_days2" type="text" class="form-control" value="<?=$payment->getSkontodays2()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						<span class="input-group-addon">Tage</span>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">Skonto 2</label>
				<div class="col-sm-10">
					<div class="input-group">
						<input name="pt_skonto2" type="text" class="form-control" value="<?=$payment->getSkonto2()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						<span class="input-group-addon">%</span>
					</div>
				</div>
			</div>

			<div class="form-group">
				<label for="" class="col-sm-2 control-label">'Nettotage</label>
				<div class="col-sm-10">
					<input id="pt_nettodays" name="pt_nettodays" type="text" class="form-control" value="<?=$payment->getNettodays()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
				</div>
			</div>
			<?/**if($_CONFIG->shopActivation){?>
			<tr>
			<td class="content_row_header"><?=$_LANG->get('Shop-Freigabe')?></td>
			<td class="content_row_clear">
			<input 	id="pt_shoprel" name="pt_shoprel" class="text" type="checkbox"
			value="1" <?if ($payment->getShoprel() == 1) echo "checked"; ?>>
			</td>
			</tr>
			<?}**/?>
		</form>
	</div>
</div>