<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       15.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------
//print_r($_REQUEST);
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/paper/paper.class.php';

// Funktion liefert die Select-Felder zur Auswahl der Unterwarengruppen
function printSubTradegroupsForSelect($parentId, $depth){
	global $product;
	$all_subgroups = Tradegroup::getAllTradegroups($parentId);
	foreach ($all_subgroups AS $subgroup){
		global $x;
		$x++; ?>
			<option value="<?=$subgroup->getId()?>"	<?if ($product->getTradegroup()->getId() == $subgroup->getId()) echo "selected" ;?> >
				<?for ($i=0; $i<$depth+1;$i++) echo "&emsp;"?>
				<?= $subgroup->getTitle()?>
			</option>
		<? printSubTradegroupsForSelect($subgroup->getId(), $depth+1);
	}
}

$product = new Product($_REQUEST["id"]);
$machgroups = MachineGroup::getAllMachineGroups(MachineGroup::ORDER_POSITION);
$papers = Paper::getAllPapers(Paper::ORDER_NAME);
$all_tradegroups = Tradegroup::getAllTradegroups();

// Falls kopieren, ID loeschen -> Maschine wird neu angelegt
if($_REQUEST["exec"] == "copy")
    $product->clearId();

if($_REQUEST["deletePicture"] == 1)
{
    $product->setPicture('');
    $product->save();
}

if($_REQUEST["subexec"] == "save")
{
    
    $defmachs = Array();
    $verfmachs = Array();
    $paperweights = Array();
    $paper_formats = Array();
    foreach (array_keys($_REQUEST) as $key)
    {
        if(preg_match("/mach_def_(?P<id>\d+)/", $key, $m))
        {
            $defmachs[$m["id"]]["id"] = $m["id"];
            $defmachs[$m["id"]]["min"] = (int)$_REQUEST["mach_def_from_{$m["id"]}"];
            $defmachs[$m["id"]]["max"] = (int)$_REQUEST["mach_def_to_{$m["id"]}"];
        }
        
        if(preg_match("/mach_verf_(?P<id>\d+)/", $key, $m))
        {
            $verfmachs[$m["id"]] = $m["id"];
        }
        
        if(preg_match("/paper_weight_content_(?P<id>\d+)_(?P<paperid>\d+)_(?P<weight>\d+)/", $key, $m))
        {
            if($_REQUEST["paper_content_{$m["id"]}"] != "")
            {
                $paperweights["content"][$m["paperid"]]["id"] = $m["paperid"];
                $paperweights["content"][$m["paperid"]][$m["weight"]] = 1;
            }
        }
        
        if(preg_match("/paper_weight_envelope_(?P<id>\d+)_(?P<paperid>\d+)_(?P<weight>\d+)/", $key, $m))
        {
            if($_REQUEST["paper_envelope_{$m["id"]}"] != "")
            {
                $paperweights["envelope"][$m["paperid"]]["id"] = $m["paperid"];
                $paperweights["envelope"][$m["paperid"]][$m["weight"]] = 1;
            }
        }
        
        if(preg_match("/paper_format_(?P<id>\d+)/", $key, $m))
        {
            $paper_formats[] = new Paperformat($m["id"]);
        }
        
            
    }
    
    $product->setName(trim(addslashes($_REQUEST["product_name"])));
    $product->setDescription(trim(addslashes($_REQUEST["product_description"])));
    $product->setPicture(trim(addslashes($_REQUEST["picture"])));
    $product->setAvailableMachIds($verfmachs);
    $product->setDefaultMachIds($defmachs);
    $product->setSelectedPapersIds($paperweights);
    $product->setPagesFrom((int)$_REQUEST["pages_from"]);
    $product->setPagesTo((int)$_REQUEST["pages_to"]);
    $product->setPagesStep((int)$_REQUEST["pages_step"]);
    $product->setAvailablePaperFormats($paper_formats);
    $product->setHasContent((int)$_REQUEST["product_hascontent"]);
    $product->setHasAddContent((int)$_REQUEST["product_hasaddcontent"]);
    $product->setHasAddContent2((int)$_REQUEST["product_hasaddcontent2"]);
    $product->setHasAddContent3((int)$_REQUEST["product_hasaddcontent3"]);
    $product->setHasEnvelope((int)$_REQUEST["product_hasenvelope"]);
    $product->setFactorWidth((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["factor_width"]))));
    $product->setFactorHeight((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["factor_height"]))));
    $product->setTaxes((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["taxes"]))));
    $product->setGrantPaper((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["grantpaper"]))));
    $product->setType((int)$_REQUEST["product_type"]);
    $product->setTextOffer(trim(addslashes($_REQUEST["text_offer"])));
    $product->setTextOfferconfirm(trim(addslashes($_REQUEST["text_offerconfirm"])));
    $product->setTextInvoice(trim(addslashes($_REQUEST["text_invoice"])));
    $product->setTextProcessing(trim(addslashes($_REQUEST["text_processing"])));
    $product->setTradegroup(new Tradegroup($_REQUEST["product_tradegroup"]));
    $product->setLoadDymmyData((int)$_REQUEST["load_dummydata"]);
    $product->setSingleplateset((int)$_REQUEST["singleplateset"]);
    
    if ((int)$_REQUEST["load_dummydata"] == 1){
    	$product->setPagesFrom(1);
		$product->setPagesTo(1);
		$product->setHasContent(1);
		$product->setFactorWidth(1);
		$product->setFactorHeight(1);
		$product->setType(1);
    }
    
    if($_CONFIG->shopActivation){
    	if ($_REQUEST['product_shoprel']==1){
	    	$product->setShoprel(1);
	    }else{
	    	$product->setShoprel(0);
	    }
    }
    $savemsg = getSaveMessage($product->save());
}
?>
</pre>
<!-- FancyBox -->
<script type="text/javascript" src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

<link href="jscripts/magicsuggest/magicsuggest-min.css" rel="stylesheet">
<script src="jscripts/magicsuggest/magicsuggest-min.js"></script>

<script type="text/javascript">
message='<?=$_LANG->get('Sind Sie sicher?')?>';
	$(document).ready(function() {
		$("a#picture_select").fancybox({
		    'type'    : 'iframe'
		})
	});

	function addPaper(idx, part)
	{
	    count = parseInt(document.getElementById('paper_count_'+part).value);
	    if(count == 0)
		    count++;
	    insert = '<tr id="tr_paper_'+part+'_'+count+'"><td class="content_row_clear">';
        insert += '<select name="paper_'+part+'_'+count+'" id="paper_'+part+'_'+count+'" style="width:300px" class="text"';
        insert += ' onchange="updatePaperProps(this.value, '+count+', \''+part+'\')">';
        insert += '<option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>';
        <? foreach($papers as $p)
        {?>
            insert += '<option value="<?=$p->getId()?>"><?=$p->getName()?></option>';
        <? }?>                    
        insert += '</select></td><td class="content_row_clear" id="td_paperprops_'+part+'_'+count+'">&nbsp;</td><td class="content_row_clear">';
        insert += '<img src="images/icons/cross-script.png" class="pointer icon-link" onclick="deletePaper('+count+', \''+part+'\')">';
        insert += '</td></tr>';

        document.getElementById('tr_paper_'+part+'_'+idx).insertAdjacentHTML("AfterEnd", insert);
        document.getElementById('paper_count_'+part).value = count+1;
	}

	function deletePaper(idx, part)
	{
		if(confirm(message))
		{
    	    document.getElementById('paper_'+part+'_'+idx).disabled = true;
    	    document.getElementById('tr_paper_'+part+'_'+idx).style.display = 'none';
		}
	}

	function updatePaperProps(paper, idx, part)
	{

		$.post("libs/modules/products/products.ajax.php", { paperId: paper, idx: idx, part: part },
				   function(data) {
				     document.getElementById('td_paperprops_'+part+'_'+idx).innerHTML = data;
				   });
	}

	function togglePaperEnvelope()
	{
		if(document.getElementById('product_hasenvelope').checked == '')
			document.getElementById('div_paper_envelope').style.display = 'none';
		else
			document.getElementById('div_paper_envelope').style.display = '';
	}

	function toggleAmountField(idx)
	{
		if(document.getElementById('mach_def_'+idx).checked == '')
			document.getElementById('span_amount_'+idx).style.display = 'none';
		else
			document.getElementById('span_amount_'+idx).style.display = '';
	}
</script>
<!--<script>-->
<!--    $(function () {-->
<!--        var pm = $('#paper_magic').magicSuggest({-->
<!--            data: 'libs/modules/paper/paper.ajax.php?exec=getAllPaper',-->
<!--            valueField: 'id',-->
<!--            displayField: 'name',-->
<!--            expandOnFocus: true,-->
<!--            maxSelection: 1,-->
<!--            minChars: 3,-->
<!--            selectFirst: true,-->
<!--        });-->
<!--        $(pm).on('triggerclick', function(e,m){-->
<!--            alert("don't shoot me!");-->
<!--        });-->
<!--    });-->
<!--</script>-->
<!-- /FancyBox -->

<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
          <? if ($_REQUEST["exec"] == "copy") echo $_LANG->get('Produkt kopieren')?>
          <? if ($_REQUEST["exec"] == "edit" && $product->getId() == 0) echo $_LANG->get('Produkt anlegen')?>
          <? if ($_REQUEST["exec"] == "edit" && $product->getId() != 0) echo $_LANG->get('Produkt bearbeiten')?>
      </td>
      <td align="right"><?=$savemsg?></td>
   </tr>
</table>

<div id="fl_menu">
	<div class="label">Quick Move</div>
	<div class="menu">
        <a href="#top" class="menu_item">Seitenanfang</a>
        <a href="index.php?page=<?=$_REQUEST['page']?>" class="menu_item">Zurück</a>
        <a href="#" class="menu_item" onclick="$('#machine_form').submit();">Speichern</a>
    </div>
</div>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="machine_form" name="machine_form" onsubmit="return checkform(new Array(this.product_name))">
<input type="hidden" name="exec" value="edit">
<input type="hidden" name="subexec" value="save">
<input type="hidden" name="id" value="<?=$product->getId()?>">
<input type="hidden" name="picture" id="picture" value="<?=$product->getPicture()?>">
<input type="hidden" name="paper_count_content" id="paper_count_content" value="<?=count($product->getSelectedPapersIds(Calculation::PAPER_CONTENT))?>">
<input type="hidden" name="paper_count_envelope" id="paper_count_envelope" value="<?=count($product->getSelectedPapersIds(Calculation::PAPER_ENVELOPE))?>">
<div class="box1">
<table width="100%">
    <colgroup>
        <col width="180">
        <col width="300">
        <col width="180">
        <col width="300">
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Produktname')?> *</td>
        <td class="content_row_clear">
            <input name="product_name" value="<?=$product->getName()?>" style="width:300px;" class="text">
        </td>
        <td class="content_row_header" rowspan="2" valign="top">
            <?=$_LANG->get('Produktbild')?> *<br>
            <a href="libs/modules/products/picture.iframe.php" id="picture_select" class="products"><input type="button" class="button" value="<?=$_LANG->get('&auml;ndern')?>"></a>
            <? if($product->getPicture() != "") {?>
                <input type="button" class="buttonRed" value="<?=$_LANG->get('L&ouml;schen')?>" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$product->getId()?>&deletePicture=1'">
            <? } ?>
        </td>
        <td class="content_row_clear" rowspan="2" valign="top" id="picture_show">
            <img src="images/products/<?=$product->getPicture()?>">&nbsp;
        </td>        
    </tr>
    <tr>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Beschreibung')?></td>
        <td class="content_row_clear">
            <textarea name="product_description" style="width:300px;height:100px" class="text"><?=$product->getDescription()?></textarea>
        </td>
    </tr>
    <tr>
        <td class="content_row_header" valign="top" rowspan="4"><?=$_LANG->get('Produkt besteht aus')?></td>
        <td class="content_row_clear" rowspan="4">
            <input type="checkbox" value="1" name="product_hascontent" 
                <?if($product->getHasContent()) echo "checked";?>> <?=$_LANG->get('Inhalt')?><br>
            <input type="checkbox" value="1" name="product_hasaddcontent" 
                <?if($product->getHasAddContent()) echo "checked";?>> <?=$_LANG->get('zus&auml;tzlichem Inhalt')?><br>
			<input type="checkbox" value="1" name="product_hasaddcontent2" 
                <?if($product->getHasAddContent2()) echo "checked";?>> <?=$_LANG->get('zus&auml;tzlichem Inhalt 2')?><br>
            <input type="checkbox" value="1" name="product_hasaddcontent3" 
                <?if($product->getHasAddContent3()) echo "checked";?>> <?=$_LANG->get('zus&auml;tzlichem Inhalt 3')?><br>
            <input type="checkbox" value="1" name="product_hasenvelope" onChange="togglePaperEnvelope()" id="product_hasenvelope"
                <?if($product->getHasEnvelope()) echo "checked";?>> <?=$_LANG->get('Umschlag')?>
        </td>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Umsatzsteuer')?></td>
        <td class="content_row_clear" valign="top">
            <input name="taxes" value="<?=printPrice($product->getTaxes())?>" class="text" style="width:60px"> %
        </td>
    </tr>
    <!-- tr>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Zuschussbogen')?></td>
        <td class="content_row_clear" valign="top">
            <input name="grantpaper" value="<?=printBigInt($product->getGrantPaper())?>" class="text" style="width:60px"> 
            <span title="<?=$_LANG->get('Prozentual zur Anzahl der B&ouml;gen der Bestellung')?>">%</span>
        </td>
    </tr -->
    <tr>
        <td class="content_row_header" valign="top"><?=$_LANG->get('Produkttyp')?></td>
        <td class="content_row_clear" valign="top">
            <input type="radio" name="product_type" value="<?=Product::TYPE_NORMAL?>" <?if($product->getType() == Product::TYPE_NORMAL) echo "checked";?>> <?=$_LANG->get('Normal')?>
            <input type="radio" name="product_type" value="<?=Product::TYPE_BOOKPRINT?>" <?if($product->getType() == Product::TYPE_BOOKPRINT) echo "checked";?>> <?=$_LANG->get('Buchdruck')?>
        </td>
    </tr>
    <tr>
    	<td class="content_row_header" valign="top"><?=$_LANG->get('Warengruppe')?></td>
    	<td class="content_row_clear" valign="top">
    		<select id="product_tradegroup" name="product_tradegroup" style="width: 170px">
					<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
					<?	foreach ($all_tradegroups as $tg){?>
							<option value="<?=$tg->getId()?>"
							<?if ($product->getTradegroup()->getId() == $tg->getId()) echo "selected" ?>><?= $tg->getTitle()?></option>
						<?	printSubTradegroupsForSelect($tg->getId(), 0);
						} //Ende foreach($all_tradegroups) ?>
			</select>
    	</td>
    	<?/*if($_CONFIG->shopActivation){?>
	        <td class="content_row_header" valign="top"><?=$_LANG->get('Shop-Freigabe')?></td>
	        <td class="content_row_clear" valign="top">
	            <input type="checkbox" value="1" name="product_shoprel" id="product_shoprel"
	                <?if($product->getShoprel()==1) echo "checked";?>>
	        </td>
        <?}*/?>
    </tr>
<!--<tr>
    	<td class="content_row_header" valign="top"><?=$_LANG->get('Dummy-Daten Laden')?></td>
    	<td class="content_row_header" >
    		<input type="checkbox" value="1" name="load_dummydata" <?if($product->getLoadDymmyData()) echo 'checked="checked"';?>
    				title="<?echo $_LANG->get('Laden von Dummy-Daten in Kalkulationen.')."\n";
    						 echo $_LANG->get('Produktformate und Papier m&uuml;ssen trotzdem angegeben werden');?>" >
		</td>
    </tr> -->
    <tr>
    	<td class="content_row_header" valign="top"><?=$_LANG->get('Selben Plattensatz')?></td>
    	<td class="content_row_header" >
    		<input type="checkbox" value="1" name="singleplateset" <?if($product->getSingleplateset()) echo 'checked="checked"';?> title="" >
		</td>
    </tr>
</table>
</div>
<br>
<h1><?=$_LANG->get('Papiere')?> <?=$_LANG->get('Inhalt')?> / <?=$_LANG->get('zus. Inhalt')?></h1>
<div class="box2">
<table width="100%">
    <colgroup>
        <col width="310">
        <col>
        <col width="100">
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Papier')?></td>
        <td class="content_row_header"><?=$_LANG->get('Gewichte')?></td>
        <td class="content_row_header">&nbsp;</td>
    </tr>
    <?
        $x = 0;
        foreach($product->getSelectedPapersIds(Calculation::PAPER_CONTENT) as $paperId)
        {

            $selPaper = new Paper($paperId["id"]);
            echo '<tr id="tr_paper_content_'.$x.'">
                <td class="content_row_clear">
                    <select name="paper_content_'.$x.'" id="paper_content_'.$x.'" style="width:300px" class="text" onchange="updatePaperProps(this.value, '.$x.', \'content\')">';
            foreach($papers as $p)
            {
                echo '<option value="'.$p->getId().'" ';
                if($paperId["id"] == $p->getId()) echo "selected";
                echo '>'.$p->getName().'</option>';
            }                    
            echo '</select>
                </td>
                <td class="content_row_clear" id="td_paperprops_content_'.$x.'">';
            foreach($selPaper->getWeights() as $w) {
                echo '<input type="checkbox" name="paper_weight_content_'.$x.'_'.$paperId["id"].'_'.$w.'" value="1" ';
                if($paperId[$w] == 1) echo "checked";
                echo '>'.$w." ";
            }
            echo '</td>
                <td class="content_row_clear">
                    <img src="images/icons/cross-script.png" class="pointer icon-link" onclick="deletePaper('.$x.', \'content\')">';
            if($x == count($product->getSelectedPapersIds(Calculation::PAPER_CONTENT)) -1)
                echo '
                    <img src="images/icons/plus.png" class="pointer icon-link" onclick="addPaper('.$x.', \'content\')">';

            echo '</td>
            </tr>';
            $x++;
        } 

    
        if($x == 0)
        {
            echo '<tr id="tr_paper_content_0"><td class="content_row">';
            echo '<select name="paper_content_0" style="width:300px" class="text" onchange="updatePaperProps(this.value, 0, \'content\')">';
            echo '<option value="">&lt;'.$_LANG->get('Bitte w&auml;hlen').'&gt;</option>';
            foreach($papers as $p)
                echo '<option value="'.$p->getId().'">'.$p->getName().'</option>';
            echo '</select>';
            echo '</td>
            <td class="content_row" id="td_paperprops_content_0">
            </td>
            <td class="content_row">
                <img src="images/icons/cross-script.png" class="pointer icon-link" onclick="deletePaper(0, \'content\')">
                <img src="images/icons/plus.png" class="pointer icon-link" onclick="addPaper(0, \'content\')">
            </td>
        </tr>';
        }
?>
</table>
</div>
<br>
<div id="div_paper_envelope" <? if (!$product->getHasEnvelope()) { echo 'style="display:none"'; } ?>>
<h1><?=$_LANG->get('Papiere')?> <?=$_LANG->get('Umschlag')?></h1>
<div class="box2">
<table width="100%">
    <colgroup>
        <col width="310">
        <col>
        <col width="100">
    </colgroup>
    <tr>
        <td class="envelope_row_header"><?=$_LANG->get('Papier')?></td>
        <td class="envelope_row_header"><?=$_LANG->get('Gewichte')?></td>
        <td class="envelope_row_header">&nbsp;</td>
    </tr>
    <?
        $x = 0;
        foreach($product->getSelectedPapersIds(Calculation::PAPER_ENVELOPE) as $paperId)
        {

            $selPaper = new Paper($paperId["id"]);
            echo '<tr id="tr_paper_envelope_'.$x.'">
                <td class="envelope_row_clear">
                    <select name="paper_envelope_'.$x.'" id="paper_envelope_'.$x.'" style="width:300px" class="text" onchange="updatePaperProps(this.value, '.$x.', \'envelope\')">';
            foreach($papers as $p)
            {
                echo '<option value="'.$p->getId().'" ';
                if($paperId["id"] == $p->getId()) echo "selected";
                echo '>'.$p->getName().'</option>';
            }                    
            echo '</select>
                </td>
                <td class="envelope_row_clear" id="td_paperprops_envelope_'.$x.'">';
            foreach($selPaper->getWeights() as $w) {
                echo '<input type="checkbox" name="paper_weight_envelope_'.$x.'_'.$paperId["id"].'_'.$w.'" value="1" ';
                if($paperId[$w] == 1) echo "checked";
                echo '>'.$w." ";
            }
            echo '</td>
                <td class="envelope_row_clear">
                    <img src="images/icons/cross-script.png" class="pointer icon-link" onclick="deletePaper('.$x.', \'envelope\')">';
            if($x == count($product->getSelectedPapersIds(Calculation::PAPER_ENVELOPE)) -1)
                echo '
                    <img src="images/icons/plus.png" class="pointer icon-link" onclick="addPaper('.$x.', \'envelope\')">';

            echo '</td>
            </tr>';
            $x++;
        } 

    
        if($x == 0)
        {
            echo '<tr id="tr_paper_envelope_0"><td class="envelope_row">';
            echo '<select name="paper_envelope_0" style="width:300px" class="text" onchange="updatePaperProps(this.value, 0, \'envelope\')">';
            echo '<option value="">&lt;'.$_LANG->get('Bitte w&auml;hlen').'&gt;</option>';
            foreach($papers as $p)
                echo '<option value="'.$p->getId().'">'.$p->getName().'</option>';
            echo '</select>';
            echo '</td>
            <td class="envelope_row" id="td_paperprops_envelope_0">
            </td>
            <td class="envelope_row">
                <img src="images/icons/cross-script.png" class="pointer icon-link" onclick="deletePaper(0, \'envelope\')">
                <img src="images/icons/plus.png" class="pointer icon-link" onclick="addPaper(0, \'envelope\')">
            </td>
        </tr>';
        }
?>
</table>
</div>
</div>
<br>

<h1><?=$_LANG->get('Maschinen')?></h1>
<div class="box2">
<table width="100%" cellpadding="0" cellspacing="0">
    <colgroup>
        <col width="20">
        <col width="20">
        <col width="240">
        <col width="200">
        <col width="20">
        <col width="20">
        <col width="240">
        <col width="200">
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Verf.')?></td>
        <td class="content_row_header"><?=$_LANG->get('Std.')?></td>
        <td class="content_row_header"><?=$_LANG->get('Maschine')?></td>
        <td class="content_row_header"><?=$_LANG->get('Standard bei Auflage')?></td>
        <td class="content_row_header"><?=$_LANG->get('Verf.')?></td>
        <td class="content_row_header"><?=$_LANG->get('Std.')?></td>
        <td class="content_row_header"><?=$_LANG->get('Maschine')?></td>    
        <td class="content_row_header"><?=$_LANG->get('Standard bei Auflage')?></td>
    </tr>

    <? foreach($machgroups as $mg) { 
        echo '<tr><td class="content_row_clear" colspan="4">'.$mg->getName().'</td></tr>';
        $machines = Machine::getAllMachines(Machine::ORDER_NAME, $mg->getId());
        
        echo '<tr class="'.getRowColor(0).'">';
        $x = 0; $i = 1;
        foreach($machines as $m)
        {
            if($x % 2 == 0 && $x > 0)
            {
                echo '</tr><tr class="'.getRowColor($i).'">'; $i++;
            }
            
            echo '<td class="content_row">
                <input name="mach_verf_'.$m->getId().'" type="checkbox" value="1" ';
            if($product->isAvailableMachine($m)) echo "checked";
            echo '>
            </td>';
            echo '<td class="content_row">
                <input name="mach_def_'.$m->getId().'" id="mach_def_'.$m->getId().'" type="checkbox" value="1" ';
            if($product->isDefaultMachine($m)) echo "checked";
            echo ' onchange="toggleAmountField('.$m->getId().')">
            </td>';
            echo '<td class="content_row">'.$m->getName().'</td>';
            echo '<td class="content_row"><span id="span_amount_'.$m->getId().'" ';
            if(!$product->isDefaultMachine($m)) echo 'style="display:none"';
            echo '>';
            echo $_LANG->get('von').' <input name="mach_def_from_'.$m->getId().'" style="width:50px;text-align:center" ';
            echo 'value="'.$product->getMinForDefaultMachine($m).'"> ';
            echo $_LANG->get('bis').' <input name="mach_def_to_'.$m->getId().'" style="width:50px;text-align:center" ';
            echo 'value="'.$product->getMaxForDefaultMachine($m).'">';
            echo '</span>&nbsp;</td>';
            
            $x++;
        }
        if($x % 2 == 1)
            echo '<td class="content_row" colspan="4">&nbsp;</td>';
        echo '</tr>';
    }
    ?>        
    </tr>
</table>
</div>
<br>
<h1><?=$_LANG->get('Produktformate')?></h1>
<div class="box2">
<table width="100%" cellpadding="0" cellspacing="0">
    <colgroup>
        <col width="25%">
        <col width="25%">
        <col width="25%">
        <col width="25%">
    </colgroup>
    <? $i = 1;?>
    <tr class="<?=getRowColor($i)?>">
        <?  
            $avail = Array();
            foreach($product->getAvailablePaperFormats() as $pf)
                $avail[$pf->getId()] = true;
            $x = 1;
            foreach(Paperformat::getAllPaperFormats() as $pf)
            {
                echo '<td class="content_row">
                    <input name="paper_format_'.$pf->getId().'" type="checkbox" value="1" ';
                if($avail[$pf->getId()]) echo "checked";
                echo '>
                    '.$pf->getName().' ('.$pf->getWidth().' x '.$pf->getHeight().' mm)
                </td>';
                
                if($x % 4 == 0)
                {
                    $i++;
                    echo '</tr><tr class="'.getRowColor($i).'">';
                }
                $x++;
            } 
        ?>
    </tr>
</table>
</div>
<br>
<h1><?=$_LANG->get('Weitere Angaben')?></h1>
<div class="box2">
<table width="500">
    <colgroup>
        <col width="180">
        <col width="300">
    </colgroup>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Verf&uuml;gbare Seiten')?></td>
        <td class="content_row_clear">
            <?=$_LANG->get('Von')?>: <input name="pages_from" style="width:40px;text-align:center" class="text" value="<?=$product->getPagesFrom()?>">
            <?=$_LANG->get('Bis')?>: <input name="pages_to" style="width:40px;text-align:center" class="text" value="<?=$product->getPagesTo()?>">
            <?=$_LANG->get('Interval')?>: <input name="pages_step" style="width:40px;text-align:center" class="text"  value="<?=$product->getPagesStep()?>">
        </td>
    </tr>
    <tr>
        <td class="content_row_header"><?=$_LANG->get('Faktor offen/geschlossen')?></td>
        <td class="content_row_clear">
            <input name="factor_width" class="text" style="width:60px;text-align:center" value="<?=printPrice($product->getFactorWidth())?>"> <?=$_LANG->get('mal Breite')?>
            <input name="factor_height" class="text" style="width:60px;text-align:center" value="<?=printPrice($product->getFactorHeight())?>"> <?=$_LANG->get('mal H&ouml;he')?>
        </td>
    </tr>    
</table>
</div>
<br>

<h1><?=$_LANG->get('Zusatztexte')?></h1>
<div class="box2">
<table width="100%" cellspacing="0" cellpadding="0" border="0">
    <tr>
        <td width="33%"><?=$_LANG->get('Angebot')?></td>
        <td width="34%"><?=$_LANG->get('Angebotsbest&auml;tigung')?></td>
        <td width="33%"><?=$_LANG->get('Rechnung')?></td>
    </tr>
    <tr>
        <td width="33%">
            <textarea name="text_offer" class="text" style="width:320px;height:100px;"><?=$product->getTextOffer()?></textarea>
        </td>
        <td width="34%">
            <textarea name="text_offerconfirm" class="text" style="width:330px;height:100px;"><?=$product->getTextOfferconfirm()?></textarea>
        </td>
        <td width="33%">
            <textarea name="text_invoice" class="text" style="width:320px;height:100px;"><?=$product->getTextInvoice()?></textarea>
        </td>
    </tr>
    <tr>
        <td width="33%"><?=$_LANG->get('Standardtext "Verarbeitung"')?></td>
        <td width="34%">&nbsp;</td>
        <td width="33%">&nbsp;</td>
    </tr>
    <tr>
        <td width="33%">
            <textarea name="text_processing" class="text" style="width:320px;height:100px;"><?=$product->getTextProcessing()?></textarea>
        </td>
        <td width="34%">
            &nbsp;
        </td>
        <td width="33%">
            &nbsp;
        </td>
    </tr>
</table>
</div>
</form>