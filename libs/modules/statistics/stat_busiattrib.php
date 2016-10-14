<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/businesscontact/attribute.class.php';

$all_attributes = Attribute::getAllAttributesForCustomer();

?>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">Geschäftskunden Vorgangsanzahl mit Merkmal</h3>
	  </div>
	  <div class="panel-body">
			<div class="panel panel-default">
				  <div class="panel-heading">
						<h3 class="panel-title">Filter</h3>
				  </div>
				  <div class="panel-body">
                      <label for="" class="col-sm-2 control-label">Merkmal-Filter:</label>
                      <div class="col-sm-3">
                          <select id="filter_attrib" name="filter_attrib" onfocus="markfield(this,0)" onblur="markfield(this,1)" class="form-control">
                              <option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
                              <?
                              foreach ($all_attributes AS $attribute){
                                  $allitems = $attribute->getItems();
                                  foreach ($allitems AS $item){ ?>
                                      <option value="<?=$attribute->getId()?>|<?=$item["id"]?>"><?=$item["title"]?></option>
                                  <? }
                              } ?>
                          </select>
                      </div>
				  </div>
			</div>
	  </div>
</div>
