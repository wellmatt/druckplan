<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       05.06.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------

$stock_id = (int)$_REQUEST["stockid"];

if($_REQUEST["subexec"] == "new"){
	$stock = new Warehouse();
	$tmp_title = "Lagerplatz erstellen";
}else{
	$stock = new Warehouse($stock_id);
	$tmp_title = "Lagerplatz-Daten &auml;ndern";
}

if($_REQUEST["subexec"] == "save"){
	
	$stock->setName(trim($_REQUEST["st_name"]));
	$stock->setCustomer(new BusinessContact((int)$_REQUEST["st_customer"]));
	$stock->setInput(trim(addslashes($_REQUEST["st_input"])));
	$stock->setOrdernumber(trim($_REQUEST["st_ordernumber"]));
	$stock->setAmount((int)$_REQUEST["st_amount"]);
	$stock->setMinimum((int)$_REQUEST["st_minimum"]);
	$stock->setComment(trim(addslashes($_REQUEST["st_comment"])));
	$stock->setContactperson(new User((int)$_REQUEST["st_contactperson"]));
	$stock->setArticle(new Article((int)$_REQUEST["st_article"]));
	
	if ((int)$_REQUEST["st_recall"] != 0){
		$_REQUEST["st_recall"] = explode(".", $_REQUEST["st_recall"]);
		$stock->setRecall((int)mktime(12, 0, 0, $_REQUEST["st_recall"][1], $_REQUEST["st_recall"][0], $_REQUEST["st_recall"][2]));
	} else {
		$stock->setRecall(0);
	}
	
	$savemsg = getSaveMessage($stock->save());
}

$all_article = Article::getAllArticle(Article::ORDER_TITLE);
$allcustomer = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME);
$alluser = User::getAllUser();
?>
<table border="0" cellpadding="0" cellspacing="0" width="1000">
	<tr>
		<td height="30">
			<b class="content_header"><?=$tmp_title?></b>
		</td>
		<td align="right" style="padding-right:10px"><?=$savemsg?></td>
		<td align="right" width="50">&ensp;</td>
	</tr>
</table>

<?//------------------ F�r Datumsfelder -------------------------------------------------------------------?>
<style type="text/css"><!-- @import url(./libs/jscripts/datepicker/datepicker.css); //--></style>
<script language="JavaScript" >
$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	
	$('#st_recall').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "images/icons/calendar-blue.png",
                buttonImageOnly: true
			}
     );
});

</script>
<script src="./libs/jscripts/autoresize.jquery.js" type="text/javascript"></script>
<script language="JavaScript">
function openSearchWindow(id)
{
   var centerWidth   = (screen.width / 2)  - (650 / 2);
   var centerHeight  = (screen.height / 2) - (450 / 2);
   var MyURL         = './libs/modules/items/search.php?id='+id;
   
   var winA = window.open(MyURL, 'NEW', "width=650,height=450,top="+centerHeight+",left="+centerWidth+",scrollbars=1,resizeable=0,toolbar=0,location=0,menubar=0");
   winA.focus();
}
</script>
<link rel="stylesheet" type="text/css" href="./css/urlaub.css" />

<?//------------------ Tabelle mit den Demodaten ----------------------------------------------------------?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#stock_form').submit();",'glyphicon-floppy-disk');
if ($stock->getId()>0){
	$quickmove->addItem('Löschen', '#',"askDel('index.php?page=".$_REQUEST['page']."&exec=delete&stockid=".$stock->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>


<form id="stock_form" name="stock_form" method="post">
<input type="hidden" id="stockid" name="stockid" value="<?=$stock->getId()?>" />
<input type="hidden" id="subexec" name="subexec" value="save" />
<div class="box1">
	<table border="0" cellpadding="3" cellspacing="1" width="100%">
	<colgroup>
		<col width="200">
		<col>
	</colgroup>
	<tr>
		<td class="content_tbl_subheader">&ensp;<!-- b>Lagerplatz-Daten</b--></td>
		<td class="content_tbl_subheader">&ensp;</td>
	</tr>
	<tr>
		<td class="content_row"><?=$_LANG->get('Lagerplatzname')?>: </td>
		<td class="content_row">
			<input type="text" id="st_name" name="st_name" style="width:200px" value="<?=$stock->getName()?>"
					onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
		</td>
	</tr>
	<tr>
		<td class="content_row"><?=$_LANG->get('Kunde/Lieferant')?>:</td>
		<td class="content_row">
			<select type="text" id="st_customer" name="st_customer" style="width:200px"
					onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
				<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
			<? 	foreach ($allcustomer as $cust){?>
					<option value="<?=$cust->getId()?>"
						<?if ($stock->getCustomer()->getId() == $cust->getId()) echo "selected" ?>><?= $cust->getNameAsLine()?></option>
			<?	} ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="content_row"><?=$_LANG->get('Artikel / Material / Inhalt')?>:</td>
		<td class="content_row">
			<textarea rows="8" cols="80" type="text" id="st_input" name="st_input"
					class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)"><?=$stock->getInput()?></textarea>
		</td>
	</tr>
	<tr>
		<td class="content_row"><?=$_LANG->get('Auftragsnummer')?>: </td>
		<td class="content_row">
			<input type="text" id="st_ordernumber" name="st_ordernumber" style="width:200px" value="<?=$stock->getOrdernumber()?>"
					onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
		</td>
	</tr>
	<tr>
		<td class="content_row"><?=$_LANG->get('Lagermenge')?>: </td>
		<td class="content_row">
			<input type="text" id="st_amount" name="st_amount" style="width:100px" value="<?=$stock->getAmount()?>"
					onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
		</td>
	</tr>
	<tr>
		<td class="content_row"><?=$_LANG->get('Mindestmenge u. Verantwortlicher')?>: </td>
		<td class="content_row">
			<input type="text" id="st_minimum" name="st_minimum" style="width:100px" value="<?=$stock->getMinimum()?>"
					onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
			&ensp;
			<select type="text" id="st_contactperson" name="st_contactperson" style="width:130px"
					onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
				<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
			<? 	foreach ($alluser as $us){?>
					<option value="<?=$us->getId()?>"
						<?if ($stock->getContactperson()->getId() == $us->getId()) echo "selected" ?>><?= $us->getNameAsLine()?></option>
			<?	} //Ende ?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="content_row"><?=$_LANG->get('Artikel')?></td>
		<td class="content_row">
			<select id="st_article" name="st_article" style="width: 200px">
				<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
			<? 	foreach ($all_article as $art){?>
					<option value="<?=$art->getId()?>"
						<?if ($stock->getArticle()->getId() == $art->getId()) echo "selected" ?>><?= $art->getTitle()?></option>
			<?	}?>
			</select>
		</td>
	</tr>
	<tr>
		<td class="content_row"><?=$_LANG->get('Abrufdatum')?>: </td>
		<td class="content_row">
			<input type="text" style="width:100px" id="st_recall" name="st_recall"
					class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
					onfocus="markfield(this,0)" onblur="markfield(this,1)"
					value="<?if($stock->getRecall() != 0){ echo date('d.m.Y', $stock->getRecall());}?>">
		</td>
	</tr>
	<tr>
		<td class="content_row"><?=$_LANG->get('Bemerkungen')?>: </td>
		<td class="content_row">
			<textarea rows="4" cols="80" type="text" id="st_comment" name="st_comment"
					class="text" onfocus="markfield(this,0)" onblur="markfield(this,1)"><?=$stock->getComment()?></textarea>
		</td>
	</tr>
</table>
</div>
</form>
