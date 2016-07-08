<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       03.05.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('incominginvoicetemplate.class.php');
require_once 'libs/modules/businesscontact/businesscontact.class.php';

if($_REQUEST["exec"] == "del")
{

	$inv = new Incominginvoicetemplate($_REQUEST["id"]);
	$ret = $inv->delete();

	$savemsg = getSaveMessage($ret);

}

if($_SESSION["invoiceem"]["month"] == "")
{
	$_SESSION["invoiceem"]["month"]     = (int)date('m');
	$_SESSION["invoiceem"]["year"]      = (int)date('Y');
	$_SESSION["invoiceem"]["companyid"] = $companies[0]["id"];
}
if($_REQUEST["filter_month"] != "")
$_SESSION["invoiceem"]["month"]  = $_REQUEST["filter_month"];
if($_REQUEST["filter_year"] != "")
$_SESSION["invoiceem"]["year"]   = $_REQUEST["filter_year"];
if($_REQUEST["filter_companyid"] != "")
$_SESSION["invoiceem"]["companyid"] = $_REQUEST["filter_companyid"];

if($_REQUEST["exec"] == "save")
{

	foreach(array_keys($_REQUEST) AS $reqkey)
	{
		if(strpos($reqkey, "invc_title_") !== false)
		{
			$idx = substr($reqkey, strrpos($reqkey, "_") +1);
			if((!empty($_REQUEST["invc_title_{$idx}"])) && (!empty($_REQUEST["invc_price_netto_{$idx}"]))) {
				$inv = new Incominginvoicetemplate((int)$_REQUEST["invc_existingid_{$idx}"]);

				$inv->setInvc_title(trim(addslashes($_REQUEST["invc_title_{$idx}"])));
				$inv->setInvc_taxes_active((int)$_REQUEST["invc_taxes_active_{$idx}"]);
				$inv->setInvc_supplierid((int)$_REQUEST["invc_supplierid_{$idx}"]);
				$invc_price_netto   = $_REQUEST["invc_price_netto_{$idx}"];
				$inv->setInvc_price_netto( (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $invc_price_netto))));
				$inv->setInvc_crtdat(mktime(0, 0, 0, $_SESSION["invoiceem"]["month"], 1, $_SESSION["invoiceem"]["year"]));

				if(!$_REQUEST["invc_uses_supplier_{$idx}"])
				$inv->setInvc_supplierid(0);

				$ret = $inv->save($idx);
			}
		}
		$savemsg = getSaveMessage($ret).$DB->getLastError();
	}
}

$invoices = Incominginvoicetemplate::getAllTemplates();
$suppliers = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME,BusinessContact::FILTER_SUPP);
?>
<form action="index.php?page=<?=$_REQUEST['page']?>" class="form-horizontal" method="post" name="idx_invcform">
	<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				Standardrechnungen
				<span class="pull-right">
					<?=$savemsg?>
				</span>
			</h3>
	  </div>
	  <div class="panel-body">
			<div class="panel panel-default">
				  <div class="panel-heading">
						<h3 class="panel-title">
							Erfassung / Offen
						</h3>
				  </div>
				  <div class="panel-body">
					  <input type="hidden" name="exec" value="save" />
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th class="content_row_header"><?=$_LANG->get('Grund der Ausgabe')?></th>
										<th class="content_row_header"><?=$_LANG->get('Lief')?></th>
										<th class="content_row_header"><?=$_LANG->get('Netto-Betrag')?></th>
										<th class="content_row_header"><?=$_LANG->get('MwSt-Satz')?></th>
										<th class="content_row_header"><?=$_LANG->get('MwSt-Betrag')?></th>
										<th class="content_row_header"><?=$_LANG->get('Brutto-Betrag')?></th>
										<th class="content_row_header">&nbsp;</th>
										<th class="content_row_header">&nbsp;</th>
									</tr>
								</thead>
								<tbody>
								<?

								$x = 0;
								foreach ($invoices as $invoice)
								{  ?>
									<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)"
										onmouseout="mark(this, 1)">
										<td>
											<input type="text" class="form-control" name="invc_title_<?=$x?>" value="<?=$invoice->getInvc_title()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)" />
											<input type="hidden" name="invc_existingid_<?=$x?>" value="<?=$invoice->getId()?>" />
											<select class="form-control"  name="invc_supplierid_<?=$x?>" id="invc_supplierid_<?=$x?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
												<option value="">&lt;<?=$_LANG->get('Lieferant ausw�hlen')?>&gt;</option>
												<?
												foreach($suppliers AS $supplier)
												{
													?>
													<option value="<?=$supplier->getId()?>"
														<? if($supplier->getId() == $invoice->getInvc_supplierid()) echo 'selected="selected"'?>><?=$supplier->getNameAsLine()?></option>
													<?
												}
												?>
											</select>
										</td>
										<td>
											<input type="checkbox" class="form-control" name="invc_uses_supplier_<?=$x?>" value="1" onclick="if(this.checked)document.getElementById('invc_supplierid_<?=$x?>').style.display='';
												else
												document.getElementById('invc_supplierid_<?=$x?>').style.display='none';"
												<? if($invoice->getInvc_supplierid()) echo "checked='checked'"?> />
										</td>
										<td>
											<input type="text" class="form-control" name="invc_price_netto_<?=$x?>" value="<?echo printPrice($invoice->getInvc_price_netto());?> " onfocus="markfield(this,0)" onblur="markfield(this,1)" /> <?=$_USER->getClient()->getCurrency()?>
										</td>
										<td>
											<div  class="input-group">
												<input type="text" class="form-control" name="invc_taxes_active_<?=$x?>" id="invc_taxes_active_<?=$x?>" value="<?=$invoice->getInvc_taxes_active();?>" onfocus="markfield(this,0)" onblur="markfield(this,1)" />
												<span class="input-group-addon">%</span>
											</div>
										</td>
										<td><?if($invoice->getInvc_price_netto()) {echo $invoice->getTaxPrice();echo " ".$_USER->getClient()->getCurrency();}?></td>
										<td><?if($invoice->getInvc_price_netto()) {echo $invoice->getBruttoPrice();echo " ".$_USER->getClient()->getCurrency();}?></td>
										<td><?
											if($invoice->getId())
											{  ?>
												<ul class="postnav_del" style="margin-top: 7px;">
													<a href="#"
													   onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&id=<?=$invoice->getId()?>&exec=del')"><?=$_LANG->get('L&ouml;schen')?></a>
												</ul>
												<?
											}
											else
												echo "&nbsp;";
											?></td>
									</tr>
									<?
									$x++;
								}


								$y=0;
								for($y=$x;$y<$x+5;$y++)
								{  ?>
									<tr class="<?=getRowColor($y)?>" onmouseover="mark(this, 0)"
										onmouseout="mark(this, 1)">
										<td>
											<input type="text" class="form-control" name="invc_title_<?=$y?>" value="" onfocus="markfield(this,0)" onblur="markfield(this,1)">
											<input type="hidden" name="invc_existingid_<?=$y?>" name="invc_existingid_<?=$y?>" value=0 />
											<select class="form-control" name="invc_supplierid_<?=$y?>" id="invc_supplierid_<?=$y?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
												<option value="">&lt; <?=$_LANG->get('Lieferant ausw&auml;hlen')?> &gt;</option>
												<?
												foreach($suppliers AS $supplier)
												{
													?>
													<option value="<?=$supplier->getId()?>"><?=$supplier->getNameAsLine()?></option>
													<?
												}
												?>
											</select>
										</td>
										<td>
											<input type="checkbox" class="form-control" name="invc_uses_supplier_<?=$y?>" value="1" onclick="if(this.checked)document.getElementById('invc_supplierid_<?=$y?>').style.display='';
												else
												document.getElementById('invc_supplierid_<?=$y?>').style.display='none';">
										</td>
										<td>
											<div class="input-group">
												<input class="form-control" name="invc_price_netto_<?=$y?>" value="" onfocus="markfield(this,0)" onblur="markfield(this,1)">
												<span class="input-group-addon"><?=$_USER->getClient()->getCurrency()?></span>
											</div>
										</td>
										<td>
											<div class="input-group">
												<input class="form-control" name="invc_taxes_active_<?=$y?>" id="invc_taxes_active_<?=$y?>" value="<?=$_USER->getClient()->getTaxes()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)" onclick="this.value=''" />
												<span class="input-group-addon">%</span>
											</div>
										</td>
										<td><? echo "- - - "?></td>
										<td><? echo "- - - "?></td>
										<td>&nbsp;</td>
										<td>
											<ul class="postnav_del">
												<a
													<button href="#" class="btn btn-xs btn-danger" >
													<?=$_LANG->get('L&ouml;schen')?>
													</button>
												</a>
											</ul>
										</td>
									</tr>
									<?
								}
								?>
								</tbody>
							</table>
						</div>
				  </div>
				<span class="pull-right">
					<button class="btn btn-primary btn-success" type="submit" >
						<?=$_LANG->get('Speichern')?>
					</button>
				</span>
			</div>
	  </div>
	</div>
</form>
