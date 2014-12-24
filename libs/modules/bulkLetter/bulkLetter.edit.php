<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			28.10.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/businesscontact/attribute.class.php';

$bulk = new Bulkletter((int)$_REQUEST["bid"]);
$all_attributes = Attribute::getAllAttributesForCustomer();

if($_REQUEST["subexec"] == "save"){
	$bulk->setTitle(trim(addslashes($_REQUEST["bulk_title"])));
	$bulk->setText(trim(addslashes($_REQUEST["bulk_text"])));
	$bulk->setCustomerFilter($_REQUEST["bulk_filter"]);
	
	$tmp_attribs = Array();
	foreach ($_REQUEST["filter_attrib"] as $tmp_attrib){
	    if ($tmp_attrib != ""){
	       $tmp_attribs[] = $tmp_attrib;
	    }
	}
	$bulk->setCustomerAttrib($tmp_attribs);
	
	$ret_save = $bulk->save();
	$savemsg = getSaveMessage($ret_save)." ".$DB->getLastError();
	
	if($ret_save){
		$bulk->createDocument(Bulkletter::DOCTYPE_PRINT);
		$bulk->createDocument(Bulkletter::DOCTYPE_EMAIL);
	}
}


/*if($_REQUEST["subexec"] == "create_print_document"){
	$bulk->createDocument(Bulkletter::DOCTYPE_PRINT);
}

if($_REQUEST["subexec"] == "create_print"){
	$bulk->createDocument(Bulkletter::DOCTYPE_EMAIL);
}*/

?>
<table width="100%">
	<tr>
		<td width="200" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
			<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Serienbrief erstellen')?>
			<?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Serienbrief bearbeiten')?>
		</td>
		<td align="right"><?=$savemsg?></td>
	</tr>
</table>

<form 	action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="bulkletter_edit" id="bulkletter_edit"   
		onSubmit="return checkForm(new Array(this.bulk_title, this.bulk_text))">
	<div class="box1">
		<input type="hidden" name="exec" value="edit"> 
		<input type="hidden" name="subexec" value="save"> 
		<input type="hidden" name="bid" value="<?=$bulk->getId()?>">
		<table width="100%">
			<colgroup>
				<col width="170">
				<col>
			</colgroup>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Titel')?> *</td>
				<td class="content_row_clear">
				<input id="bulk_title" name="bulk_title" type="text" class="text" 
					value="<?=$bulk->getTitle()?>" style="width: 250px">
				</td>
			</tr>
			<tr>
				<td class="content_row_header" valign="top"><?=$_LANG->get('Text')?> *</td>
				<td class="content_row_clear" valign="top">
					<textarea id="bulk_text" name="bulk_text" class="text" 
						style="width: 500px; height: 150px; " ><?=$bulk->getText()?></textarea>
				</td>
			</tr>
			
			<tr>
				<td class="content_row_header" valign="top"><?=$_LANG->get('Kunden-Filter')?></td>
				<td class="content_row_clear" valign="top">
					<select name="bulk_filter" style="width: 250px"	class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
							<option value="0" <? if($bulk->getCustomerFilter()==0) echo "selected";?>><?=$_LANG->get('Interessent')?></option>
							<option value="1" <? if($bulk->getCustomerFilter()==1) echo "selected";?>><?=$_LANG->get('Bestandskunde')?></option>
							<option value="2" <? if($bulk->getCustomerFilter()==2) echo "selected";?>><?=$_LANG->get('Alle Kunden')?></option>
							<option value="3" <? if($bulk->getCustomerFilter()==3) echo "selected";?>><?=$_LANG->get('Lieferanten')?></option>
							<option value="4" <? if($bulk->getCustomerFilter()==4) echo "selected";?>><?=$_LANG->get('Alle')?></option>
					</select>
				</td>
			</tr>
			
			<tr>
				<td class="content_row_header" valign="top"><?=$_LANG->get('Kunden-Merkmale')?></td>
				<td class="content_row_clear" valign="top" id="attrib_td">
				<? if (count($bulk->getCustomerAttrib()) > 0) { foreach ($bulk->getCustomerAttrib() as $attrib){
				    $tmp_expl_attrib = explode(",",$attrib);?>
        			<select name="filter_attrib[]" style="width:150px"	onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
        				<option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
        				<? 
        				foreach ($all_attributes AS $attribute){
        				 	$allitems = $attribute->getItems();
        					foreach ($allitems AS $item){ 
        					   if ($item["id"] == $tmp_expl_attrib[1]){?>
        						  <option value="<?=$attribute->getId()?>,<?=$item["id"]?>" selected><?=$item["title"]?></option>
        					<? } else { ?>
        						  <option value="<?=$attribute->getId()?>,<?=$item["id"]?>"><?=$item["title"]?></option>
        					 <?}
        					}
        				} ?>
        			</select></br>
        		<? }} ?>
        			<select name="filter_attrib[]" style="width:150px"	onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
        				<option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
        				<? 
        				foreach ($all_attributes AS $attribute){
        				 	$allitems = $attribute->getItems();
        					foreach ($allitems AS $item){?>
        						  <option value="<?=$attribute->getId()?>,<?=$item["id"]?>"><?=$item["title"]?></option>
        				  <?}
        				} ?>
        			</select>
				</td>
			</tr>
			
			<?if ($bulk->getCrt_user()->getId() > 0){?>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Erstellt von')?></td>
					<td class="content_row_clear">
						<?if($bulk->getCrt_user()->getId() > 0) echo $bulk->getCrt_user()->getNameAsLine()?>
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Erstellt am')?></td>
					<td class="content_row_clear">
						<?if($bulk->getCrt_date() > 0) echo date('d.m.Y - H:i:s',$bulk->getCrt_date())?>
					</td>
				</tr>
					<?if($bulk->getUpd_user()->getId() > 0){?>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Bearbeitet von')?></td>
						<td class="content_row_clear">
							<?=$bulk->getUpd_user()->getNameAsLine()?>
						</td>
					</tr>
					<tr>
						<td class="content_row_header"><?=$_LANG->get('Bearbeitet am')?></td>
						<td class="content_row_clear">
							<?if($bulk->getUpd_date() > 0) echo date('d.m.Y - H:i:s',$bulk->getUpd_date())?>
						</td>
					</tr>
					<?}?>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Download')?></td>
					<td class="content_row_clear">
						<table cellpadding="0" cellspacing="0">
							<tr>
								<td>
									<ul class="postnav_save_small" style="padding:0px">
										<a href="<?=$bulk->getPdfLink(Bulkletter::DOCTYPE_EMAIL)?>"
											title="PDF mit Hintergrund"><?=$_LANG->get('E-Mail')?></a>
									</ul>
								</td>
								<td>
									<ul class="postnav_save_small" style="padding:0px">
										<a href="<?=$bulk->getPdfLink(Bulkletter::DOCTYPE_PRINT)?>"
											title="PDF ohne Hintergrund"><?=$_LANG->get('Print')?></a>
									</ul>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			<?}?>
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
	        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
	        			onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
	        </td>
	        <td class="content_row_clear" align="right">
	        	<input type="submit" value="<?=$_LANG->get('Speichern')?>">
	        </td>
	    </tr>
	</table>
</form>