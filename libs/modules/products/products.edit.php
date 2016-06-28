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
        insert += '<span class="glyphicons glyphicons-remove pointer" onclick="deletePaper('+count+', \''+part+'\')"></span>';
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
<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#machine_form').submit();",'glyphicon-floppy-disk');
if ($product->getId()>0){
    $quickmove->addItem('Löschen', '#',  "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&id=".$product->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" id="machine_form" name="machine_form"
      class="form-horizontal" onsubmit="return checkform(new Array(this.product_name))">
    <input type="hidden" name="exec" value="edit">
    <input type="hidden" name="subexec" value="save">
    <input type="hidden" name="id" value="<?= $product->getId() ?>">
    <input type="hidden" name="picture" id="picture" value="<?= $product->getPicture() ?>">
    <input type="hidden" name="paper_count_content" id="paper_count_content"
           value="<?= count($product->getSelectedPapersIds(Calculation::PAPER_CONTENT)) ?>">
    <input type="hidden" name="paper_count_envelope" id="paper_count_envelope"
           value="<?= count($product->getSelectedPapersIds(Calculation::PAPER_ENVELOPE)) ?>">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                <? if ($_REQUEST["exec"] == "copy") echo $_LANG->get('Produkt kopieren') ?>
                <? if ($_REQUEST["exec"] == "edit" && $product->getId() == 0) echo $_LANG->get('Produkt anlegen') ?>
                <? if ($_REQUEST["exec"] == "edit" && $product->getId() != 0) echo $_LANG->get('Produkt bearbeiten') ?>
                <span class="pull-right">
                  <?= $savemsg ?>
                </span>
            </h3>

        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Produktname</label>
                        <div class="col-sm-9">
                            <input name="product_name" value="<?= $product->getName() ?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Beschreibung</label>
                        <div class="col-sm-9">
                            <textarea name="product_description"
                                      class="form-control"><?= $product->getDescription() ?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Produkt besteht aus</label>
                        <div class="col-sm-9">
                            <input type="checkbox" value="1" name="product_hascontent"
                                <? if ($product->getHasContent()) echo "checked"; ?>> <?= $_LANG->get('Inhalt') ?><br>
                            <input type="checkbox" value="1" name="product_hasaddcontent"
                                <? if ($product->getHasAddContent()) echo "checked"; ?>> <?= $_LANG->get('zus&auml;tzlichem Inhalt') ?>
                            <br>
                            <input type="checkbox" value="1" name="product_hasaddcontent2"
                                <? if ($product->getHasAddContent2()) echo "checked"; ?>> <?= $_LANG->get('zus&auml;tzlichem Inhalt 2') ?>
                            <br>
                            <input type="checkbox" value="1" name="product_hasaddcontent3"
                                <? if ($product->getHasAddContent3()) echo "checked"; ?>> <?= $_LANG->get('zus&auml;tzlichem Inhalt 3') ?>
                            <br>
                            <input type="checkbox" value="1" name="product_hasenvelope" onChange="togglePaperEnvelope()"
                                   id="product_hasenvelope"
                                <? if ($product->getHasEnvelope()) echo "checked"; ?>> <?= $_LANG->get('Umschlag') ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Bild</label>
                        <div class="col-sm-9" id="picture_show">
                            <?php if ($product->getPicture() != "") { ?>
                                <img src="images/products/<?=$product->getPicture()?>">&nbsp;
                            <?php }?>
                        </div>
                    </div>
                    </br>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Produktbild</label>
                        <div class="col-sm-9">
                            <a href="libs/modules/products/picture.iframe.php" id="picture_select"
                               class="products">
                                <button class="btn btn-xs btn-success">
                                    <?= $_LANG->get('&auml;ndern') ?>
                                </button></a>
                            <? if ($product->getPicture() != "") { ?>
                                <button class="btn btn-xs btn-success" onclick="document.location='index.php?page=<?= $_REQUEST['page'] ?>&exec=edit&id=<?= $product->getId() ?>&deletePicture=1'">
                                    <?= $_LANG->get('L&ouml;schen') ?>
                                </button>
                            <? } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Umsatzsteuer</label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input name="taxes" value="<?= printPrice($product->getTaxes()) ?>"
                                       class="form-control">
                                <span class="input-group-addon">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Produkttyp</label>
                        <div class="col-sm-9">
                            <input type="radio" name="product_type"
                                   value="<?= Product::TYPE_NORMAL ?>" <? if ($product->getType() == Product::TYPE_NORMAL) echo "checked"; ?>> <?= $_LANG->get('Normal') ?>
                            &nbsp;
                            <input type="radio" name="product_type"
                                   value="<?= Product::TYPE_BOOKPRINT ?>" <? if ($product->getType() == Product::TYPE_BOOKPRINT) echo "checked"; ?>> <?= $_LANG->get('Buchdruck') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Warengruppe</label>
                        <div class="col-sm-9">
                            <select id="product_tradegroup" name="product_tradegroup" class="form-control">
                                <option value="0">&lt; <?= $_LANG->get('Bitte w&auml;hlen') ?> &gt;</option>
                                <? foreach ($all_tradegroups as $tg) { ?>
                                    <option value="<?= $tg->getId() ?>"
                                        <? if ($product->getTradegroup()->getId() == $tg->getId()) echo "selected" ?>><?= $tg->getTitle() ?></option>
                                    <? printSubTradegroupsForSelect($tg->getId(), 0);
                                } //Ende foreach($all_tradegroups) ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Selben Plattensatz</label>
                        <div class="col-sm-2">
                            <input class="form-control" type="checkbox" value="1"
                                   name="singleplateset" <? if ($product->getSingleplateset()) echo 'checked="checked"'; ?>
                                   title="">
                        </div>
                    </div>
                </div>
            </div>
            </br>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <?= $_LANG->get('Papiere') ?> <?= $_LANG->get('Inhalt') ?> / <?= $_LANG->get('zus. Inhalt') ?>
                    </h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                        <tr>
                            <th class="content_row_header"><?= $_LANG->get('Papier') ?></th>
                            <th class="content_row_header"><?= $_LANG->get('Gewichte') ?></th>
                            <th class="content_row_header">&nbsp;</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?
                        $x = 0;
                        foreach ($product->getSelectedPapersIds(Calculation::PAPER_CONTENT) as $paperId) {
                            $selPaper = new Paper($paperId["id"]);
                            ?>
                            <tr id="tr_paper_content_<?php echo $x; ?>">
                                <td>
                                    <select name="paper_content_<?php echo $x; ?>" id="paper_content_<?php echo $x; ?>"
                                            class="form-control"
                                            onchange="updatePaperProps(this.value, <?php echo $x; ?>, 'content')">
                                        <?php
                                        foreach ($papers as $p) {
                                            ?>
                                            <option
                                                value="<?php echo $p->getId(); ?>" <?php if ($paperId["id"] == $p->getId()) echo "selected"; ?>><?php echo $p->getName(); ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </td>
                                <td id="td_paperprops_content_<?php echo $x; ?>">
                                    <?php
                                    foreach ($selPaper->getWeights() as $w) {
                                        ?>
                                        <input type="checkbox"
                                               name="paper_weight_content_<?php echo $x; ?>_<?php echo $paperId["id"]; ?>_<?php echo $w; ?>"
                                               value="1" <?php if ($paperId[$w] == 1) echo "checked"; ?>>
                                        <?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <span class="glyphicons glyphicons-remove pointer"
                                          onclick="deletePaper(<?php echo $x; ?>, 'content')"></span>
                                    <?php
                                    if ($x == count($product->getSelectedPapersIds(Calculation::PAPER_CONTENT)) - 1) {
                                        ?>
                                        <span class="glyphicons glyphicons-plus pointer"
                                              onclick="addPaper(<?php echo $x; ?>, 'content')"></span>
                                        <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                            $x++;
                        }
                        if ($x == 0) {
                            ?>
                            <tr id="tr_paper_content_0">
                                <td>
                                    <select name="paper_content_0"  class="form-control" onchange="updatePaperProps(this.value, 0, 'content')">
                                        <option value="">&lt;' .('Bitte w&auml;hlen'). '&gt;</option>
                                        <?php
                                        foreach ($papers as $p)
                                        {
                                            ?>
                                            <option value="<?php echo $p->getId();?>"><?php echo $p->getName();?></option>
                                            <?php
                                        }?>

                                    </select>
                                </td>
                                <td id="td_paperprops_content_0"></td>
                                <td>
                                    <span class="glyphicons glyphicons-remove pointer"onclick="deletePaper(0, 'content')"></span>
                                    <span class="glyphicons glyphicons-plus pointer" onclick="addPaper(0, 'content')"></span>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="div_paper_envelope" <? if (!$product->getHasEnvelope()) {
                echo 'style="display:none"';
            } ?>>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Papiere/Umschlag
                        </h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th class="envelope_row_header"><?= $_LANG->get('Papier') ?></th>
                                <th class="envelope_row_header"><?= $_LANG->get('Gewichte') ?></th>
                                <th class="envelope_row_header">&nbsp;</th>
                            </tr>
                            </thead>
                            <tbody>

                            <?
                            $x = 0;
                            foreach ($product->getSelectedPapersIds(Calculation::PAPER_ENVELOPE) as $paperId) {
                                $selPaper = new Paper($paperId["id"]);
                                ?>
                                <tr id="tr_paper_envelope_<?php echo $x; ?>">
                                    <td>
                                        <select name="paper_envelope_<?php echo $x; ?>"
                                                id="paper_envelope_<?php echo $x; ?>"
                                                class="form-control"
                                                onchange="updatePaperProps(this.value, <?php echo $x; ?>, 'envelope')">
                                            <?php
                                            foreach ($papers as $p) {
                                                ?>
                                                <option
                                                    value="<?php echo $p->getId(); ?>" <?php if ($paperId["id"] == $p->getId()) echo "selected"; ?>><?php echo $p->getName(); ?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td id="td_paperprops_envelope_<?php echo $x; ?>">
                                        <?php
                                        foreach ($selPaper->getWeights() as $w) {
                                            ?>
                                            <input type="checkbox"
                                                   name="paper_weight_envelope_<?php echo $x; ?>_<?php echo $paperId["id"]; ?>_<?php echo $w; ?>"
                                                   value="1" <?php if ($paperId[$w] == 1) echo "checked"; ?>>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td>
                                    <span class="glyphicons glyphicons-remove pointer"
                                          onclick="deletePaper(<?php echo $x; ?>, 'envelope')"></span>
                                        <?php
                                        if ($x == count($product->getSelectedPapersIds(Calculation::PAPER_CONTENT)) - 1) {
                                            ?>
                                            <span class="glyphicons glyphicons-plus pointer"
                                                  onclick="addPaper(<?php echo $x; ?>, 'envelope')"></span>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                $x++;
                            }
                            if ($x == 0) {
                                ?>
                                <tr id="tr_paper_envelope_0">
                                    <td>
                                        <select name="paper_envelope_0" class="form-control"
                                                onchange="updatePaperProps(this.value, 0, 'content')">
                                            <option value="">&lt;' .('Bitte w&auml;hlen'). '&gt;</option>
                                            <?php
                                            foreach ($papers as $p) {
                                                ?>
                                                <option
                                                    value="<?php echo $p->getId(); ?>"><?php echo $p->getName(); ?></option>
                                                <?php
                                            } ?>

                                        </select>
                                    </td>
                                    <td id="td_paperprops_envelope_0"></td>
                                    <td>
                                        <span class="glyphicons glyphicons-remove pointer"
                                              onclick="deletePaper(0, 'envelope')"></span>
                                        <span class="glyphicons glyphicons-plus pointer"
                                              onclick="addPaper(0, 'envelope')"></span>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            </br>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        <?= $_LANG->get('Maschinen') ?>
                    </h3>
                </div>
                <div class="panel-body">

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th class="content_row_header"><?= $_LANG->get('Verf.') ?></th>
                                <th class="content_row_header"><?= $_LANG->get('Std.') ?></th>
                                <th class="content_row_header"><?= $_LANG->get('Maschine') ?></th>
                                <th class="content_row_header"><?= $_LANG->get('Standard bei Auflage') ?></th>
                                <th class="content_row_header"><?= $_LANG->get('Verf.') ?></th>
                                <th class="content_row_header"><?= $_LANG->get('Std.') ?></th>
                                <th class="content_row_header"><?= $_LANG->get('Maschine') ?></th>
                                <th class="content_row_header"><?= $_LANG->get('Standard bei Auflage') ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            foreach ($machgroups as $mg) {
                            ?>
                            <tr>
                                <td class="content_row_clear" colspan="4"><?php echo $mg->getName(); ?></td>
                            </tr>
                            <?php
                            $machines = Machine::getAllMachines(Machine::ORDER_NAME, $mg->getId());
                            ?>
                            <tr class=" getRowColor(0)">
                                <td>
                                </td>
                            </tr>
                            <?php
                            $x = 0;
                            $i = 1;
                            foreach ($machines as $m) {

                            if ($x % 2 == 0 && $x > 0) {

                            }?>
                            <tr class="getRowColor(<?php $i ?>)">

                                <?php
                                $i++;
                                }
                                }
                                ?>
                                <td class="content_row">
                                    <input name="mach_verf_' <?php echo $m->getId()?>" type="checkbox" value="1"
                                        <?php if ($product->isAvailableMachine($m)) echo "checked";?>>
                                </td>
                                <td class="content_row">
                                    <input name="mach_def_<?php echo $m->getId()?>" id="mach_def_<?php echo $m->getId()?>" type="checkbox" value="1"
                                        <?php if ($product->isDefaultMachine($m)) echo "checked";?>
                                           onchange="toggleAmountField(<?php echo $m->getId()?>)">
                                </td>
                                <td class="content_row"><?php echo $m->getName()?></td>
                                <td class="content_row">
                                    <span id="span_amount_'<?php echo $m->getId()?>"<?php if (!$product->isDefaultMachine($m))?> style="display:none">
                                    </span>
                                </td>

                            </tbody>


                            <? foreach ($machgroups as $mg) {
                                echo '<tr><td class="content_row_clear" colspan="4">' . $mg->getName() . '</td></tr>';
                                $machines = Machine::getAllMachines(Machine::ORDER_NAME, $mg->getId());

                                echo '<tr class="' . getRowColor(0) . '">';
                                $x = 0;
                                $i = 1;
                                foreach ($machines as $m) {
                                    if ($x % 2 == 0 && $x > 0) {
                                        echo '</tr><tr class="' . getRowColor($i) . '">';
                                        $i++;
                                    }

                                    echo '<td class="content_row">
                <input name="mach_verf_' . $m->getId() . '" type="checkbox" value="1" ';
                                    if ($product->isAvailableMachine($m)) echo "checked";
                                    echo '>
            </td>';
                                    echo '<td class="content_row">
                <input name="mach_def_' . $m->getId() . '" id="mach_def_' . $m->getId() . '" type="checkbox" value="1" ';
                                    if ($product->isDefaultMachine($m)) echo "checked";
                                    echo ' onchange="toggleAmountField(' . $m->getId() . ')">
            </td>';
                                    echo '<td class="content_row">' . $m->getName() . '</td>';
                                    echo '<td class="content_row"><span id="span_amount_' . $m->getId() . '" ';
                                    if (!$product->isDefaultMachine($m)) echo 'style="display:none"';
                                    echo '>';
                                    echo $_LANG->get('von') . ' <input name="mach_def_from_' . $m->getId() . '" style="width:50px;text-align:center" ';
                                    echo 'value="' . $product->getMinForDefaultMachine($m) . '"> ';
                                    echo $_LANG->get('bis') . ' <input name="mach_def_to_' . $m->getId() . '" style="width:50px;text-align:center" ';
                                    echo 'value="' . $product->getMaxForDefaultMachine($m) . '">';
                                    echo '</span>&nbsp;</td>';

                                    $x++;
                                }
                                if ($x % 2 == 1)
                                    echo '<td class="content_row" colspan="4">&nbsp;</td>';
                                echo '</tr>';
                            }
                            ?>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            </br>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Produktformate
                    </h3>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <? $i = 1; ?>
                        <tr class="<?php echo getRowColor($i) ?>">
                            <?
                            $avail = Array();
                            foreach ($product->getAvailablePaperFormats() as $pf)
                                $avail[$pf->getId()] = true;
                            $x = 1;
                            foreach (Paperformat::getAllPaperFormats() as $pf) {
                            ?>
                            <td class="content_row">
                                <input name="paper_format_<?php echo $pf->getId()?>" type="checkbox" value="1"
                                    <?php if ($avail[$pf->getId()]) echo "checked"; ?>>
                                <?php echo $pf->getName()?>(<?php echo $pf->getWidth()?> x<?php echo $pf->getHeight()?> mm)
                            </td>
                            <?php

                            if ($x % 4 == 0) {
                            $i++;
                            ?>
                        </tr><tr class="<?php echo getRowColor($i)?>">
                            <?php
                            }
                            $x++;
                            }
                            ?>
                        </tr>
                    </table>
                </div>
            </div>
            <br>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Weitere Angaben
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="col-sm-12 control-label">Verf&uuml;gbare Seiten</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="col-sm-4 control-label">Von</label>
                                <div class="col-sm-6">
                                    <input name="pages_from" class="form-control" value="<?= $product->getPagesFrom() ?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="col-sm-4 control-label">Bis</label>
                                <div class="col-sm-6">
                                    <input name="pages_to" class="form-control" value="<?= $product->getPagesTo() ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="col-sm-12 control-label"></label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="col-sm-4 control-label">Interval</label>
                                <div class="col-sm-6">
                                    <input name="pages_step" class="form-control" value="<?= $product->getPagesStep() ?>">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="col-sm-3 control-label"></label>
                                <div class="col-sm-9"></div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="col-sm-12 control-label">Faktor offen/geschlossen</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="col-sm-4 control-label">Breite *</label>
                                <div class="col-sm-6">
                                    <input name="factor_width" class="form-control" value="<?= printPrice($product->getFactorWidth()) ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="" class="col-sm-4 control-label">H&ouml;he * </label>
                                <div class="col-sm-6">
                                    <input name="factor_height" class="form-control" value="<?= printPrice($product->getFactorHeight()) ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </br>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Zusatztexte
                    </h3>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Angebot</label>
                            <div class="col-sm-6">
                                <textarea rows="5" name="text_offer" class="form-control"><?= $product->getTextOffer() ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Angebotsbest&auml;tigung</label>
                            <div class="col-sm-6">
                                <textarea rows="5" name="text_offerconfirm" class="form-control"><?= $product->getTextOfferconfirm() ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Rechnung</label>
                            <div class="col-sm-6">
                                <textarea rows="5" class="form-control" ><?= $product->getTextInvoice() ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Standardtext "Verarbeitung"</label>
                            <div class="col-sm-6">
                                <textarea rows="5" name="text_processing" class="form-control"><?= $product->getTextProcessing() ?></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- tr>
        <td class="content_row_header" valign="top"><?= $_LANG->get('Zuschussbogen') ?></td>
        <td class="content_row_clear" valign="top">
            <input name="grantpaper" value="<?= printBigInt($product->getGrantPaper()) ?>" class="text" style="width:60px">
            <span title="<?= $_LANG->get('Prozentual zur Anzahl der B&ouml;gen der Bestellung') ?>">%</span>
        </td>
    </tr -->


    <? /*if($_CONFIG->shopActivation){?>
	        <td class="content_row_header" valign="top"><?=$_LANG->get('Shop-Freigabe')?></td>
	        <td class="content_row_clear" valign="top">
	            <input type="checkbox" value="1" name="product_shoprel" id="product_shoprel"
	                <?if($product->getShoprel()==1) echo "checked";?>>
	        </td>
        <?}*/ ?>

    <!--<tr>
    	<td class="content_row_header" valign="top"><?= $_LANG->get('Dummy-Daten Laden') ?></td>
    	<td class="content_row_header" >
    		<input type="checkbox" value="1" name="load_dummydata" <? if ($product->getLoadDymmyData()) echo 'checked="checked"'; ?>
    				title="<? echo $_LANG->get('Laden von Dummy-Daten in Kalkulationen.') . "\n";
    echo $_LANG->get('Produktformate und Papier m&uuml;ssen trotzdem angegeben werden'); ?>" >
		</td>
    </tr> -->
</form>