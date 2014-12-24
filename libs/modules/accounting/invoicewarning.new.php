<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			27.09.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/documents/document.class.php';
require_once 'warnlevel.class.php';

$warnlevel = new Warnlevel((int)$_REQUEST["warn_id"]);
$invoice = new Document((int)$_REQUEST["invid"]);

if($_REQUEST["subexec"] == "create"){
	$warn_text = trim(addslashes($_REQUEST["warn_text"]));

	$payable = time() + $warnlevel->getDeadline()*24*60*60;

	$warning = new Document();
	$warning->setType(Document::TYPE_INVOICEWARNING);
	$warning->setPayable($payable);
	$warning->setRequestModule($invoice->getRequestModule());
	$warning->setRequestId($invoice->getRequestId());
	$warning->setPriceBrutto($invoice->getPriceBrutto());
	$warning->setPriceNetto($invoice->getPriceNetto());

	$warning->createDoc(Document::VERSION_PRINT);
	$saver = $warning->save();

	$savemsg = getSaveMessage($saver).$DB->getLastError();

	// MahnungsID an der Rechnung anhaengen
	if ($saver){
		$invoice->setWarningId($warning->getId());
		$invoice->save();

		// Weiterleitung, wenn das Speichern in der invoicewarning.new.php geschehen wuerde
		echo "<script type=\"text/javascript\">location.href='index.php?page=libs/modules/accounting/invoicewarning.php';</script>";
	}
}

$all_wanlevel = Warnlevel::getAllWarnlevel(Warnlevel::ORDER_TITLE);

?>

<script language="javascript">
function updateInvoicewarningText(warnid){
	$.post("libs/modules/accounting/invoicewarning.ajax.php", 
			{exec: 'updateInvoicewarningText', invid: <?=$_REQUEST["invid"]?>, warnid:warnid}, 
			 function(data) {
				document.getElementById('warn_text').value = data;
				document.getElementById('warn_text').focus();
			});
}
</script>

<table width="100%">
	<tr>
		<td width="200" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
			<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Mahnung erstellen')?>
		</td>
		<td align="right"><?=$savemsg?></td>
	</tr>
</table>

<form action="index.php?page=libs/modules/accounting/invoicewarning.php" method="post" name="warnlevel_edit" id="warnlevel_edit"   
		onSubmit="return checkForm(new Array(this.warn_title, this.warn_text))">
	<div class="box1">
		<input type="hidden" name="exec" value="new"> 
		<input type="hidden" name="subexec" value="create"> 
		<input type="hidden" name="invid" value="<?=$_REQUEST["invid"]?>">
		<table width="100%">
			<colgroup>
				<col width="170">
				<col>
			</colgroup>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Mahnstufe')?> *</td>
				<td class="content_row_clear">
					<select id="warn_id" name="warn_id" class="text" style="width: 250px"
							onchange="updateInvoicewarningText(this.value)">
						<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
						<?foreach ($all_wanlevel AS $warn){?>
							<option value="<?=$warn->getId()?>"><?=$warn->getTitle()?></option>
						<?}?>
					</select>
				</td>
			</tr>
			<tr>
				<td class="content_row_header" valign="top"><?=$_LANG->get('Text')?> *</td>
				<td class="content_row_clear" valign="top">
					<textarea id="warn_text" name="warn_text" class="text" style="width: 500px; height: 150px; "
							  ><?=$warn_text?></textarea>
				</td>
			</tr>
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
	        	<input type="submit" value="<?=$_LANG->get('Erstellen')?>">
	        </td>
	    </tr>
	</table>
</form>