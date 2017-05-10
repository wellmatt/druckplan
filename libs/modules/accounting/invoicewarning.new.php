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

	$res = $warning->createDoc(Document::VERSION_PRINT);
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
				 CKEDITOR.instances.warn_text.setData( data, function()
				 {
					 this.checkDirty();  // true
				 });
				document.getElementById('warn_text').focus();
			});
}
</script>
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				Mahnung erstellen
				<span class="pull-right">
					<?=$savemsg?>
				</span>
			</h3>
	  </div>
	  <div class="panel-body">
		  <form action="index.php?page=libs/modules/accounting/invoicewarning.php" method="post" class="form-horizontal" name="warnlevel_edit" id="warnlevel_edit"
				onSubmit="return checkForm(new Array(this.warn_title, this.warn_text))">
			  <input type="hidden" name="exec" value="new">
			  <input type="hidden" name="subexec" value="create">
			  <input type="hidden" name="invid" value="<?=$_REQUEST["invid"]?>">

			  <div class="form-group">
				  <label for="" class="col-sm-3 control-label">Mahnstufe</label>
				  <div class="col-sm-9">
					  <select id="warn_id" name="warn_id" class="form-control" onchange="updateInvoicewarningText(this.value)">
						  <option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
						  <?foreach ($all_wanlevel AS $warn){?>
							  <option value="<?=$warn->getId()?>"><?=$warn->getTitle()?></option>
						  <?}?>
					  </select>
				  </div>
			  </div>
			  <div class="form-group">
				  <label for="" class="col-sm-3 control-label">Text</label>
				  <div class="col-sm-9">
					  <textarea id="warn_text" name="warn_text" class="form-control"  rows="6"><?=$warn_text?></textarea>
				  </div>
			  </div>
			  <br>
			  <br>
			  <button class="btn btn-origin btn-success" type="button" onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>';">
				  <?= $_LANG->get('Zur&uuml;ck') ?>
			  </button>
			  <span class="pull-right">
				   <button class="btn btn-origin btn-success" type="submit">
					   <?=$_LANG->get('Erstellen')?>
				   </button>
			  </span>
		  </form>
	  </div>
</div>


<script>
	$(function () {
		CKEDITOR.replace( 'warn_text' );
	});
</script>