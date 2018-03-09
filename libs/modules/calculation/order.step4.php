<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       02.12.2013
// Copyright:     2012-14 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$calculations = Calculation::getAllCalculations($order, Calculation::ORDER_AMOUNT);

$all_user = User::getAllUser(User::ORDER_NAME, $_USER->getClient()->getId());

$machines = Array();
foreach ($calculations as $c){
    $me = Machineentry::getAllMachineentries($c->getId());

    foreach($me as $m){
        $machines[$m->getMachine()->getId()]["name"] = $m->getMachine()->getName();
        $machines[$m->getMachine()->getId()][$c->getId()]["price"] += $m->getPrice();
        $machines[$m->getMachine()->getId()][$c->getId()]["object"] = $m;
    }
}

?>
<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>

<script>
    function checkPercentMax(type,ele){
        var element = $(ele);
        var notify = false;
        switch (type){
            case 'material':
                if (parseFloat(element.val()) > <?php echo $perf->getCalcPercentMaterial();?>) {
                    element.val(printPriceJs(<?php echo $perf->getCalcPercentMaterial();?>));
                    notify = true;
                }
                break;
            case 'process':
                if (parseFloat(element.val()) > <?php echo $perf->getCalcPercentProcessing();?>) {
                    element.val(printPriceJs(<?php echo $perf->getCalcPercentProcessing();?>));
                    notify = true;
                }
                break;
            case 'margin':
                if (parseFloat(element.val()) > <?php echo $perf->getCalcMargin();?>) {
                    element.val(printPriceJs(<?php echo $perf->getCalcMargin();?>));
                    notify = true;
                }
                break;
            case 'discount':
                if (parseFloat(element.val()) > <?php echo $perf->getCalcDiscount();?>) {
                    element.val(printPriceJs(<?php echo $perf->getCalcDiscount();?>));
                    notify = true;
                }
                break;
        }
        if (notify == true)
            alert('Eingabe überschreitet maximalen Wert!');
    }
</script>
<script>
    $(function () {
        $('#idx_delivery_date').datetimepicker({
            lang: 'de',
            i18n: {
                de: {
                    months: [
                        'Januar', 'Februar', 'März', 'April',
                        'Mai', 'Juni', 'Juli', 'August',
                        'September', 'Oktober', 'November', 'Dezember',
                    ],
                    dayOfWeek: [
                        "So.", "Mo", "Di", "Mi",
                        "Do", "Fr", "Sa.",
                    ]
                }
            },
            select: function(){
                return true;
            },
            timepicker: false,
            format: 'd.m.Y'
        });
    });
</script>

<script>
    $(function() {
        $("a#hiddenclicker_artframe").fancybox({
            'type'    : 'iframe',
            'transitionIn'	:	'elastic',
            'transitionOut'	:	'elastic',
            'speedIn'		:	600,
            'speedOut'		:	200,
            'padding'		:	25,
            'margin'        :   25,
            'scrolling'     :   'auto',
            'width'		    :	1000,
            'height'        :   900,
            'onComplete'    :   function() {
                $('#fancybox-frame').load(function() { // wait for frame to load and then gets it's height
// 	                		      $('#fancybox-content').height($(this).contents().find('body').height()+300);
                    $('#fancybox-wrap').css('top','25px');
                });
            },
            'overlayShow'	:	true,
            'helpers'		:   { overlay:null, closeClick:true }
        });
    });
    function callBoxFancyArtFrame(my_href) {
        var j1 = document.getElementById("hiddenclicker_artframe");
        j1.href = my_href;
        $('#hiddenclicker_artframe').trigger('click');
    }
</script>
<div id="hidden_clicker95" style="display:none"><a id="hiddenclicker_artframe" href="http://www.google.com" >Hidden Clicker</a></div>
<script language="javascript">
    function updateCosts(val)
    {
        $.post("libs/modules/calculation/order.ajax.php", {id: val, exec: 'getDeliveryCost'}, function(data) {
            // Work on returned data
            document.getElementById('delivery_cost').value = data;
        });
    }

    function showDetailedOverview()
    {
        newwindow = window.open('libs/modules/calculation/showDetailed.php?order=<?=$order->getId()?>', "_blank", "width=1000,height=800,left=0,top=0,scrollbars=yes");
        newwindow = focus();
    }

    function EndpriceCheck(num1, num2){
        return (num1 > num2)? num1-num2 : num2-num1;
    }

    function ManualOverride(obj)
    {
        var id;
        calc_ele = obj.name;
        id = calc_ele.match(/^man_override_(\d+)/)[1];
        var endprice = document.getElementById('hidden_endprice_'+id).value;
        endprice = endprice.replace(".","");
        endprice = parseFloat(endprice.replace(",","."));

        var override = document.getElementById('man_override_'+id).value;
        override = override.replace(".","");
        override = parseFloat(override.replace(",","."));

        var charge = document.getElementById('hidden_add_charge_'+id).value;
        charge = charge.replace(".","");
        charge = parseFloat(charge.replace(",","."));

        if (override>endprice){
            var diff = override-endprice;
            var output = charge+diff;
        } else {
            var diff = endprice-override;
            var output = charge-diff;
        }

        output = output.toString().replace(".",",");
        document.getElementById('add_charge_'+id).value = output;
    }

    $(document).ready(function() {
        $("a#idx_change_customer").fancybox({
            'type'    : 'iframe'
        })
    });
</script>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
if ($order->getArticleid()==0){
    $quickmove->addItem('Artikel Speichern','#',"askDel('index.php?page=libs/modules/article/article.php&exec=fromorder&orderid=".$order->getId()."')",'glyphicons glyphicons-article');
}
elseif ($order->getArticleid()>0){
    $quickmove->addItem('Zum Artikel',"index.php?page=libs/modules/article/article.php&exec=edit&aid=".$order->getArticleid(),'glyphicon-redo');
    $quickmove->addItem('Artikel aktualisieren','#',"askDel('index.php?page=libs/modules/article/article.php&exec=uptfromorder&orderid=".$order->getId()."&aid=".$order->getArticleid()."')",'glyphicon-refresh');

}
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#step4_form').submit();",'glyphicon-floppy-disk');
if($_USER->hasRightsByGroup(Permission::calc_delete) || $_USER->isAdmin()){
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/calculation/order.php&exec=delete&id=".$order->getId()."')", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="step4_form" name="step4_form">
    <input name="step" value="4" type="hidden">
    <input name="exec" value="edit" type="hidden">
    <input name="subexec" value="save" type="hidden">
    <input name="id" value="<?=$order->getId()?>" type="hidden">


    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Auftragsdaten: <b><?= $order->getNumber() ?></b></h3>
        </div>
        <div class="row form-horizontal" style="margin-top: 5px;">
            <div class="form-group">
                <label for="" class="col-md-2 control-label">Titel</label>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="order_title" value="<?=$order->getTitle()?>" placeholder="Titel">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Produkt</label>
                <div class="col-sm-10 form-text">
                    <?= $order->getProduct()->getName() ?>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Beschreibung</label>
                <div class="col-sm-10 form-text">
                    <?= $order->getProduct()->getDescription() ?>
                </div>
            </div>
            <?php if ($order->getId()>0){
                $attributes = $order->getActiveAttributeItemsInput();?>
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Merkmale</label>
                    <div class="col-sm-10 form-text">
                        <span class="pointer" onclick="callBoxFancyArtFrame('libs/modules/calculation/order.attribute.frame.php?oid=<?php echo $order->getId();?>');"><a>anzeigen</a> (<?php echo count($attributes);?>)</span>
                    </div>
                </div>
            <?php }?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                Kalkulationsübersicht
                    <span class="pull-right">
                         <button class="btn btn-xs btn-success"  onclick="document.location.href="index.php?page=<?=$_REQUEST['page']?>&exec=export&id=<?=$order->getId()?>">
                             <span class="glyphicons glyphicons-file-export"></span>
                             <?=$_LANG->get('Exportieren')?>
                         </button>
                    </span>
            </h3>
        </div>
        <div class="panel-body">
            <table cellpadding="0" cellspacing="0">
                <colgroup>
                    <col width="180">
                    <? for ($i = 0; $i < count($calculations); $i++)
                        echo '<col width="200">'; ?>
                </colgroup>
                <tr>
                    <td class="content_row_clear">&nbsp;</td>
                    <? $x = 1; foreach($calculations as $calc) { ?>
                        <td class="content_row_clear" align="center">
                            <b><?=$_LANG->get('Kalkulation')?> # <?=$x?></b>
                        </td>
                        <?  $x++;} ?>
                </tr>
                <tr>
                    <td class="content_row_clear">&nbsp;</td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_clear value" align="center">
                            <span class="glyphicons glyphicons-pencil pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&calc_id=<?=$calc->getId()?>&exec=edit&step=2'"></span>
                            <span class="glyphicons glyphicons-file pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&calc_id=<?=$calc->getId()?>&exec=edit&subexec=copy&step=2'"></span>

                            <? if($_USER->hasRightsByGroup(Permission::calc_delete) || $_USER->isAdmin()){ ?>
                                <a class="icon-link" href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$order->getId()?>&subexec=delete&calc_id=<?=$calc->getId()?>&step=4')"><span style="color:red" class="glyphicons glyphicons-remove"></span></a>
                            <?}?>
                        </td>
                    <? } ?>
                </tr>

                <!-- added by ascherer -->
                <tr class="color1">
                    <td class="content_row_header"><?=$_LANG->get('Untertitel')?></td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_clear value">
                            <div class="col-sm-12">
                                <input class="form-control" type="text" name="calc_title_<?=$calc->getId()?>" id="calc_title_<?=$calc->getId()?>" value="<?=$calc->getTitle()?>">
                            </div>
                        </td>
                    <? } ?>
                </tr>
                <!-- end added by ascherer -->
                <tr class="color1">
                    <td class="content_row_header"><?=$_LANG->get('Format')?></td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_clear value">
                            <?=$calc->getProductFormatWidth()?> x <?=$calc->getProductFormatHeight()?> <?=$_LANG->get('mm')?>
                            <?  if($calc->getProductFormat()) echo "(".$calc->getProductFormat()->getName().")";?>
                        </td>
                    <? } ?>
                </tr>
                <tr class="color1">
                    <td class="content_row_header"><?=$_LANG->get('Auflage')?></td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_clear value"><?=printBigInt($calc->getAmount()/$calc->getSorts())?></td>
                    <? } ?>
                </tr>
                <tr class="color1">
                    <td class="content_row_header"><?=$_LANG->get('Sorten')?></td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_clear value"><?=printBigInt($calc->getSorts())?></td>
                    <? } ?>
                </tr>
                <!-- / Kalkulationskopf -->
                <tr>
                    <td class="content_row_clear">&nbsp;</td>
                </tr>

                <!-- ---------------------------------------------------------------------- -->
                <!-- Materialkosten                                                         -->

                <? foreach($calculations as $calc) {
                    if ($calc->getPagesContent())
                        $hasContent = true;
                    if ($calc->getPagesAddContent())
                        $hasAddContent = true;
                    if ($calc->getPagesEnvelope())
                        $hasEnvelope = true;
                    if ($calc->getPagesAddContent2())
                        $hasAddContent2 = true;
                    if ($calc->getPagesAddContent3())
                        $hasAddContent3 = true;
                }?>

                <tr>
                    <td class="content_row_header"><?=$_LANG->get('Materialkosten')?></td>

                </tr>

                <? if ($hasContent) {?>
                    <tr class="color1">
                        <td class="content_row_clear"><?=$_LANG->get('Papier Inhalt 1')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <? if($calc->getPaperContent()->getId() > 0)
                                    echo $calc->getPaperContent()->getName().', '.$calc->getPaperContent()->getSelectedWeight().' '.$_LANG->get('g').'';
                                ?>
                                &nbsp;
                            </td>
                        <? }?>
                    </tr>

                    <tr class="color1">
                        <td class="content_row_clear">&nbsp;</td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?php
                                if ($calc->getPaperContent()->getRolle() != 1){
                                    ?>
                                    <?=printBigInt($calc->getPaperCount(Calculation::PAPER_CONTENT) + $calc->getPaperContentGrant())?> <?=$_LANG->get('Bogen')?>
                                    <?=printPrice($calc->getPaperContent()->getSumPrice($calc->getPaperCount(Calculation::PAPER_CONTENT) + $calc->getPaperContentGrant()))?>
                                    <?=$_USER->getClient()->getCurrency()?>
                                    <?php
                                } else {
                                    ?>
                                    <?=printPrice(($calc->getPaperCount(Calculation::PAPER_CONTENT) * $calc->getPaperContentHeight())/1000,2)?> <?=$_LANG->get('Laufmeter')?> <?=printPrice((($calc->getPaperCount(Calculation::PAPER_CONTENT) * $calc->getPaperContentHeight())/1000) * tofloat($calc->getPaperContent()->getSelectedSize()["width"])/1000,2)?> <?=$_LANG->get('qm')?>
                                    <?=printPrice($calc->getPaperContent()->getSumPrice(($calc->getPaperCount(Calculation::PAPER_CONTENT) * $calc->getPaperContentHeight())/1000,2))?>
                                    <?=$_USER->getClient()->getCurrency()?>
                                    <?php
                                }
                                ?>
                            </td>
                        <? } ?>
                    </tr>


                    <?php
                    if ($calc->getPaperContent()->getRolle() != 1){?>
                        <tr class="color1">
                            <td class="content_row_clear"><i><?=$_LANG->get('inkl. Zuschuss')?></i></td>
                            <? foreach($calculations as $calc) { ?>
                                <td class="content_row_clear value">
                                    <?=printBigInt($calc->getPaperContentGrant())?> <?=$_LANG->get('Bogen')?>
                                </td>
                            <? } ?>
                        </tr>
                    <?php } ?>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Farbe Inhalt 1')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <? if($calc->getChromaticitiesContent()->getId() > 0)
                                    echo $calc->getChromaticitiesContent()->getName().', '.$calc->getChromaticitiesContent()->getPricekg().' '.$_LANG->get('€/KG').'';?>
                                &nbsp;
                            </td>
                        <? }?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Preis Farbe Inhalt 1')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?=printPrice($calc->getChromaticitiesContent()->getPricekg() * $calc->getInkusedcontent()/1000)?>
                                <?=$_USER->getClient()->getCurrency()?>
                            </td>
                        <? } ?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Lack Inhalt 1')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <? if($calc->getFinishContent()->getId() > 0)
                                    echo $calc->getFinishContent()->getName().', '.$calc->getFinishContent()->getKosten().' '.$_LANG->get('€/KG').'';?>
                            </td>
                        <? }?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Preis Lack Inhalt 1')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?=printPrice($calc->getFinishContent()->getKosten() * $calc->getFinishusedcontent()/1000)?>
                                <?=$_USER->getClient()->getCurrency()?>
                            </td>
                        <? } ?>
                    </tr>





                <? } ?>

                <? if ($hasAddContent) { ?>
                    <tr class="color1">
                        <td class="content_row_clear"><?=$_LANG->get('Papier Inhalt 2')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?  if($calc->getPaperAddContent()->getId() > 0) { ?>
                                    <?=$calc->getPaperAddContent()->getName()?>, <?=$calc->getPaperAddContent()->getSelectedWeight()?> <?=$_LANG->get('g')?>
                                <?  } ?>&nbsp;
                            </td>
                        <? }?>
                    </tr>

                    <tr class="color1">
                        <td class="content_row_clear">&nbsp;</td>

                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?=printBigInt($calc->getPaperCount(Calculation::PAPER_ADDCONTENT) + $calc->getPaperAddContentGrant())?> <?=$_LANG->get('Bogen')?>
                                <?=printPrice($calc->getPaperAddContent()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT) + $calc->getPaperAddContentGrant()))?>
                                <?=$_USER->getClient()->getCurrency()?>
                            </td>
                        <?  } ?>
                        </td>
                    </tr>

                    <tr class="color1">
                        <td class="content_row_clear"><i><?=$_LANG->get('inkl. Zuschuss')?></i></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?=printBigInt($calc->getPaperAddContentGrant())?> <?=$_LANG->get('Bogen')?>
                            </td>
                        <? } ?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Farbe Inhalt 2')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <? if($calc->getChromaticitiesAddContent()->getId() > 0)
                                    echo $calc->getChromaticitiesAddContent()->getName().', '.$calc->getChromaticitiesAddContent()->getPricekg().' '.$_LANG->get('€/KG').'';?>
                                &nbsp;
                            </td>
                        <? }?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Preis Farbe Inhalt 2')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?=printPrice($calc->getChromaticitiesAddContent()->getPricekg() * $calc->getInkusedaddcontent()/1000)?>
                                <?=$_USER->getClient()->getCurrency()?>
                            </td>
                        <? } ?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Lack Inhalt 2')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <? if($calc->getFinishAddContent()->getId() > 0)
                                    echo $calc->getFinishAddContent()->getName().', '.$calc->getFinishAddContent()->getKosten().' '.$_LANG->get('€/KG').'';?>
                            </td>
                        <? }?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Preis Lack Inhalt 2')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?=printPrice($calc->getFinishAddContent()->getKosten() * $calc->getFinishusedaddcontent()/1000)?>
                                <?=$_USER->getClient()->getCurrency()?>
                            </td>
                        <? } ?>
                    </tr>


                <? } ?>

                <? if ($hasAddContent2) { ?>
                    <tr class="color1">
                        <td class="content_row_clear"><?=$_LANG->get('Papier Inhalt 3')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?  if($calc->getPaperAddContent2()->getId() > 0) { ?>
                                    <?=$calc->getPaperAddContent2()->getName()?>, <?=$calc->getPaperAddContent2()->getSelectedWeight()?> <?=$_LANG->get('g')?>
                                <?  } ?>&nbsp;
                            </td>
                        <? }?>
                    </tr>

                    <tr class="color1">
                        <td class="content_row_clear">&nbsp;</td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?=printBigInt($calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) + $calc->getPaperAddContent2Grant())?> <?=$_LANG->get('Bogen')?>
                                <?=printPrice($calc->getPaperAddContent2()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) + $calc->getPaperAddContent2Grant()))?>
                                <?=$_USER->getClient()->getCurrency()?>
                            </td>
                        <? } ?>
                    </tr>

                    <tr class="color1">
                        <td class="content_row_clear"><i><?=$_LANG->get('inkl. Zuschuss')?></i></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?=printBigInt($calc->getPaperAddContent2Grant())?> <?=$_LANG->get('Bogen')?>
                            </td>
                        <? } ?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Farbe Inhalt 3')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <? if($calc->getChromaticitiesAddContent2()->getId() > 0)
                                    echo $calc->getChromaticitiesAddContent2()->getName().', '.$calc->getChromaticitiesAddContent2()->getPricekg().' '.$_LANG->get('€/KG').'';?>
                                &nbsp;
                            </td>
                        <? }?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Preis Farbe Inhalt 3')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?=printPrice($calc->getChromaticitiesAddContent2()->getPricekg() * $calc->getInkusedaddcontent2()/1000)?>
                                <?=$_USER->getClient()->getCurrency()?>
                            </td>
                        <? } ?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Lack Inhalt 3')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <? if($calc->getFinishAddContent2()->getId() > 0)
                                    echo $calc->getFinishAddContent2()->getName().', '.$calc->getFinishAddContent2()->getKosten().' '.$_LANG->get('€/KG').'';?>
                            </td>
                        <? }?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Preis Lack Inhalt 3')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?=printPrice($calc->getFinishAddContent2()->getKosten() * $calc->getFinishusedaddcontent2()/1000)?>
                                <?=$_USER->getClient()->getCurrency()?>
                            </td>
                        <? } ?>
                    </tr>
                <? } ?>

                <? if ($hasAddContent3) { ?>
                    <tr class="color1">
                        <td class="content_row_clear"><?=$_LANG->get('Papier Inhalt 4')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?  if($calc->getPaperAddContent3()->getId() > 0) { ?>
                                    <?=$calc->getPaperAddContent3()->getName()?>, <?=$calc->getPaperAddContent3()->getSelectedWeight()?> <?=$_LANG->get('g')?>
                                <?  } ?>&nbsp;
                            </td>
                        <? } ?>
                    </tr>

                    <tr class="color1">
                        <td class="content_row_clear">&nbsp;</td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?=printBigInt($calc->getPaperCount(Calculation::PAPER_ADDCONTENT3) + $calc->getPaperAddContent3Grant())?> <?=$_LANG->get('Bogen')?>
                                <?=printPrice($calc->getPaperAddContent3()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT3) + $calc->getPaperAddContent3Grant()))?>
                                <?=$_USER->getClient()->getCurrency()?>
                            </td>
                        <?  } ?>
                    </tr>

                    <tr class="color1">
                        <td class="content_row_clear"><i><?=$_LANG->get('inkl. Zuschuss')?></i></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?=printBigInt($calc->getPaperAddContent3Grant())?> <?=$_LANG->get('Bogen')?>
                            </td>
                        <? } ?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Farbe Inhalt 4')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <? if($calc->getChromaticitiesAddContent3()->getId() > 0)
                                    echo $calc->getChromaticitiesAddContent3()->getName().', '.$calc->getChromaticitiesAddContent3()->getPricekg().' '.$_LANG->get('€/KG').'';?>
                                &nbsp;
                            </td>
                        <? }?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Preis Farbe Inhalt 4')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?=printPrice($calc->getChromaticitiesAddContent3()->getPricekg() * $calc->getInkusedaddcontent3()/1000)?>
                                <?=$_USER->getClient()->getCurrency()?>
                            </td>
                        <? } ?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Lack Inhalt 4')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <? if($calc->getFinishAddContent3()->getId() > 0)
                                    echo $calc->getFinishAddContent3()->getName().', '.$calc->getFinishAddContent3()->getKosten().' '.$_LANG->get('€/KG').'';?>
                            </td>
                        <? }?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Preis Lack Inhalt 4')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?=printPrice($calc->getFinishAddContent3()->getKosten() * $calc->getFinishusedaddcontent3()/1000)?>
                                <?=$_USER->getClient()->getCurrency()?>
                            </td>
                        <? } ?>
                    </tr>
                <? } ?>

                <? if ($hasEnvelope) { ?>
                    <tr class="color1">
                        <td class="content_row_clear"><?=$_LANG->get('Papier Umschlag')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?  if($calc->getPaperEnvelope()->getId()) { ?>
                                    <?=$calc->getPaperEnvelope()->getName()?>, <?=$calc->getPaperEnvelope()->getSelectedWeight()?> <?=$_LANG->get('g')?>
                                <?  } ?>&nbsp;
                            </td>
                        <? }?>
                    </tr>


                    <tr class="color1">
                        <td class="content_row_clear">&nbsp;</td>

                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?=printBigInt($calc->getPaperCount(Calculation::PAPER_ENVELOPE) + $calc->getPaperEnvelopeGrant())?> <?=$_LANG->get('Bogen')?>
                                <?=printPrice($calc->getPaperEnvelope()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ENVELOPE) + $calc->getPaperEnvelopeGrant()))?>
                                <?=$_USER->getClient()->getCurrency()?>
                            </td>
                        <? } ?>
                    </tr>

                    <tr class="color1">
                        <td class="content_row_clear"><i><?=$_LANG->get('inkl. Zuschuss')?></i></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?=printBigInt($calc->getPaperEnvelopeGrant())?> <?=$_LANG->get('Bogen')?>
                            </td>
                        <? } ?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Farbe Umschlag')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <? if($calc->getChromaticitiesEnvelope()->getId() > 0)
                                    echo $calc->getChromaticitiesEnvelope()->getName().', '.$calc->getChromaticitiesEnvelope()->getPricekg().' '.$_LANG->get('€/KG').'';?>
                                &nbsp;
                            </td>
                        <? }?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Preis Farbe Umschlag')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?=printPrice($calc->getChromaticitiesEnvelope()->getPricekg() * $calc->getInkusedenvelope()/1000)?>
                                <?=$_USER->getClient()->getCurrency()?>
                            </td>
                        <? } ?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Lack Umschlag')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <? if($calc->getFinishEnvelope()->getId() > 0)
                                    echo $calc->getFinishEnvelope()->getName().', '.$calc->getFinishEnvelope()->getKosten().' '.$_LANG->get('€/KG').'';?>
                            </td>
                        <? }?>
                    </tr>

                    <tr class="color2">
                        <td class="content_row_clear"><?=$_LANG->get('Preis Lack Umschlag')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <?=printPrice($calc->getFinishEnvelope()->getKosten() * $calc->getFinishusedenvelope()/1000)?>
                                <?=$_USER->getClient()->getCurrency()?>
                            </td>
                        <? } ?>
                    </tr>
                <? } ?>

                <?php if ($_USER->hasRightsByGroup(Permission::calc_materialcharges)){?>
                    <?php if ($perf->getCalcMaterialProcessingCharge() == 1){?>
                        <tr>
                            <td class="content_row_header"><?=$_LANG->get('Material Auf-/Abschlag')?></td>
                            <? foreach($calculations as $calc) { ?>
                                <td class="content_row_clear value">
                                    <div class="col-sm-12">
                                        <div class="input-group">
                                            <input name="material_charge_<?=$calc->getId()?>" id="material_charge_<?=$calc->getId()?>" class="form-control" style="text-align:center" value="<?=printPrice($calc->getMaterialCharge())?>">
                                            <span class="input-group-addon"><?=$_USER->getClient()->getCurrency()?></span>
                                        </div>
                                    </div>
                                </td>
                            <?  } ?>
                        </tr>
                    <?php } ?>
                <tr>
                    <td class="content_row_clear"></td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_clear value">
                            <div class="col-sm-12">
                                <div class="input-group">
                                    <input onchange="checkPercentMax('material',this)" name="material_percent_<?=$calc->getId()?>" class="form-control" style="text-align:center" value="<?=printPrice($calc->getMaterialPercent())?>">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </td>
                    <?  } ?>
                </tr>
                <?php } ?>
                <tr>
                    <td class="content_row_header"><?=$_LANG->get('Materialkosten')?></td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_header value">
                            <?=printPrice($calc->getSubMaterial()) ; ?>
                            <?=$_USER->getClient()->getCurrency()?>
                            ( <?php echo printPrice(($calc->getSubMaterial() / $calc->getPricesub() * 100));?>% )
                        </td>
                    <? } ?>
                </tr>

                <!-- / Materialkosten                                                       -->
                <!-- ---------------------------------------------------------------------- -->

                <tr>
                    <td class="content_row_clear">&nbsp;</td>
                </tr>
                <tr>
                    <td class="content_row_header"><?=$_LANG->get('Fertigungsprozess')?></td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_clear value" align="center">
                            <span class="glyphicons glyphicons-pencil pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&calc_id=<?=$calc->getId()?>&exec=edit&step=3'"></span>
                        </td>
                    <?  } ?>
                </tr>


                <!-- ---------------------------------------------------------------------- -->
                <!-- Fertigungsprozess                                                      -->
                <? foreach ($machines as $m) { ?>
                    <tr class="color1">
                        <td class="content_row_clear"><?=$m["name"]?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value" align="center">
                                <?=printPrice($m[$calc->getId()]["price"])?> <?=$_USER->getClient()->getCurrency()?></b>
                                <?	// Weitere Details bei Fremdleistungen ausweisen

                                $mach = $m[$calc->getId()]["object"];

                                if($mach != NULL && $mach->getMachineGroupObject()->getType() == MachineGroup::TYPE_EXTERN){
                                    $tmp_supp = new BusinessContact($mach->getSupplierID());
                                    if($mach->getSupplierStatus() == 0){ $status = Machineentry::SUPPLIER_STATUS_0; $img="red.gif";};
                                    if($mach->getSupplierStatus() == 1){ $status = Machineentry::SUPPLIER_STATUS_1; $img="orange.gif";};
                                    if($mach->getSupplierStatus() == 2){ $status = Machineentry::SUPPLIER_STATUS_2; $img="lila.gif";};
                                    if($mach->getSupplierStatus() == 3){ $status = Machineentry::SUPPLIER_STATUS_3; $img="green.gif";};

                                    $title = $status." \n";
                                    if ($mach->getSupplierID() > 0 ) $title .= $tmp_supp->getNameAsLine()."\n";
                                    if ($mach->getSupplierSendDate() > 0 ) $title .= "Liefer-/Bestelldatum: ".date("d.m.Y", $mach->getSupplierSendDate())." \n";
                                    if ($mach->getSupplierReceiveDate() > 0 ) $title .= "Retour: ".date("d.m.Y", $mach->getSupplierReceiveDate())." \n";
                                    $title .= $mach->getSupplierInfo()." \n";
                                    ?>
                                    <br>
                                    <img src="images/status/<?=$img?>" alt="<?=$status?>" title="<?=$title?>" >
                                <?	} ?>
                            </td>
                        <?  } ?>
                    </tr>
                <? } ?>
                <!-- / Fertigungsprozess                                                    -->
                <!-- ---------------------------------------------------------------------- -->

                <?php if ($_USER->hasRightsByGroup(Permission::calc_processingcharges)){?>
                    <?php if ($perf->getCalcMaterialProcessingCharge() == 1){?>
                        <tr>
                            <td class="content_row_header"><?=$_LANG->get('Fertigung Auf-/Abschlag')?></td>
                            <? foreach($calculations as $calc) { ?>
                                <td class="content_row_clear value">
                                    <div class="col-sm-12">
                                        <div class="input-group">
                                            <input name="process_charge_<?=$calc->getId()?>" id="process_charge_<?=$calc->getId()?>" class="form-control" style="text-align:center" value="<?=printPrice($calc->getProcessCharge())?>">
                                            <span class="input-group-addon"><?=$_USER->getClient()->getCurrency()?></span>
                                        </div>
                                    </div>
                                </td>
                            <?  } ?>
                        </tr>
                    <?php } ?>
                <tr>
                    <td class="content_row_clear"></td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_clear value">
                            <div class="col-sm-12">
                                <div class="input-group">
                                    <input onchange="checkPercentMax('process',this)" name="process_percent_<?=$calc->getId()?>" class="form-control" style="text-align:center" value="<?=printPrice($calc->getProcessPercent())?>">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </td>
                    <?  } ?>
                </tr>
                <?php } ?>
                <tr>
                    <td class="content_row_header"><?=$_LANG->get('Fertigungskosten')?></td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_header value" align="center">
                            <?=printPrice($calc->getSubProcessing());?> <?=$_USER->getClient()->getCurrency()?>
                            ( <?php echo printPrice(($calc->getSubProcessing() / $calc->getPricesub() * 100));?>% )
                        </td>
                    <? } ?>
                </tr>

                <tr>
                    <td class="content_row_clear">&nbsp;</td>
                </tr>

                <tr>
                    <td class="content_row_header"><?=$_LANG->get('Artikel')?></td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_clear value" align="center">
                            <span class="glyphicons glyphicons-pencil pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&calc_id=<?=$calc->getId()?>&exec=edit&step=3'"></span>
                        </td>
                    <?  } ?>
                </tr>

                <!-- ---------------------------------------------------------------------- -->
                <!-- Artikelkosten                                                      -->
                <tr class="color1">
                    <td class="content_row_header"></td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_clear value" align="center">
                            <table style="width: 100%;"><tr>
                                <?php
                                $artlist = CalculationArticle::getAllForCalc($calc);
                                foreach ($artlist as $art){?>
                                    <td class="content_row_clear" align="center">
                                        <?php echo $art->getTotalAmount() . 'x ' . $art->getArticle()->getTitle() . ' für ' . printPrice(($art->getTotalAmount()*$art->getArticle()->getPrice($art->getTotalAmount()))) . '€';?>
                                    </td>
                                <?php } ?>
                            </tr></table>
                        </td>
                    <?  } ?>
                </tr>
                <!-- / Artikelkosten                                                    -->
                <!-- ---------------------------------------------------------------------- -->

                <tr>
                    <td class="content_row_header"><?=$_LANG->get('Artikelkosten')?></td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_header value" align="center">
                            <?=printPrice($calc->getSubArticles());?> <?=$_USER->getClient()->getCurrency()?>
                            ( <?php echo printPrice(($calc->getSubArticles() / $calc->getPricesub() * 100));?>% )
                        </td>
                    <? } ?>
                </tr>


                <tr>
                    <td class="content_row_clear">&nbsp;</td>
                </tr>


                <? if($_USER->hasRightsByGroup(Permission::calc_detail)) { ?>

                    <tr>
                        <td class="content_row_header"><?=$_LANG->get('Zwischensumme')?></td>

                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_header value" align="center"><?=printPrice($calc->getPricesub())?> <?=$_USER->getClient()->getCurrency()?></td>
                        <? } ?>
                    </tr>
                    <tr>
                        <td class="content_row_clear">&nbsp;</td>
                    </tr>
                <? } ?>
                <tr>
                    <td class="content_row_header"><?=$_LANG->get('Preiskorrekturen')?></td>
                </tr>
                <?php if ($_USER->hasRightsByGroup(Permission::calc_margin)){?>
                    <tr class="color1">
                        <td class="content_row_clear"><?=$_LANG->get('Marge')?></td>
                        <? foreach($calculations as $calc) { ?>
                            <td class="content_row_clear value">
                                <div class="col-sm-12">
                                    <div class="input-group">
                                        <input onchange="checkPercentMax('margin',this)" name="margin_<?=$calc->getId()?>" class="form-control" style="text-align:center" value="<?=printPrice($calc->getMargin())?>">
                                        <span class="input-group-addon">%</span>
                                    </div>
                                </div>
                            </td>
                        <?  } ?>
                    </tr>
                <?php } ?>
                <?php if ($_USER->hasRightsByGroup(Permission::calc_discount)){?>
                <tr class="color1">
                    <td class="content_row_clear"><?=$_LANG->get('Rabatt')?></td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_clear value">
                            <div class="col-sm-12">
                                <div class="input-group">
                                    <input onchange="checkPercentMax('discount',this)" name="discount_<?=$calc->getId()?>" class="form-control" style="text-align:center" value="<?=printPrice($calc->getDiscount())?>">
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </td>
                    <?  } ?>
                </tr>
                <?php } ?>
                <?php if ($_USER->hasRightsByGroup(Permission::calc_addcharge)){?>
                <tr class="color1">
                    <td class="content_row_clear"><?=$_LANG->get('sonst. Auf-/Abschlag')?></td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_clear value">
                            <div class="col-sm-12">
                                <div class="input-group">
                                    <input name="add_charge_<?=$calc->getId()?>" id="add_charge_<?=$calc->getId()?>" class="form-control" style="text-align:center" value="<?=printPrice($calc->getAddCharge())?>">
                                    <input name="hidden_add_charge_<?=$calc->getId()?>" id="hidden_add_charge_<?=$calc->getId()?>" value="<?=printPrice($calc->getAddCharge())?>" type="hidden">
                                    <span class="input-group-addon"><?=$_USER->getClient()->getCurrency()?></span>
                                </div>
                            </div>

                        </td>
                    <?  } ?>
                </tr>
                <?php } ?>
                <tr>
                    <td class="content_row_header"><?=$_LANG->get('Endsumme')?></td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_header value">
                            <? if($calc->getPricetotal() < $calc->getPricesub()) echo '<span class="error">';?>
                            <?=printPrice($calc->getPricetotal())?> <?=$_USER->getClient()->getCurrency()?>
                            <? if($calc->getPricetotal() < $calc->getPricesub()) echo '</span>';?>
                            <input name="hidden_endprice_<?=$calc->getId()?>" id="hidden_endprice_<?=$calc->getId()?>" type="hidden" value="<?=printPrice($calc->getPricetotal())?>">
                        </td>
                    <? } ?>
                </tr>
                <?php if ($_USER->hasRightsByGroup(Permission::calc_manoverride)){?>
                <tr>
                    <td class="content_row_header"><?=$_LANG->get('Man. Endpreis')?></td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_header value">
                            <div class="col-sm-12">
                                <div class="input-group">
                                    <input name="man_override_<?=$calc->getId()?>" id="man_override_<?=$calc->getId()?>" onchange='ManualOverride(this)' class="form-control"  style="text-align:center" value="<?=printPrice($calc->getPricetotal())?>">
                                    <span class="input-group-addon"><?=$_USER->getClient()->getCurrency()?></span>
                                </div>
                            </div>
                        </td>
                    <? } ?>
                </tr>
                <?php } ?>

                <tr>
                    <td class="content_row_clear"><?=$_LANG->get('St&uuml;ckpreis')?></td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_clear value"><?=printPrice($calc->getPricetotal() / $calc->getAmount())?> <?=$_USER->getClient()->getCurrency()?></td>
                    <? } ?>
                </tr>

                <tr>
                    <td class="content_row_clear"><?=$_LANG->get('Kalkulation beauftragt')?></td>
                    <? foreach($calculations as $calc) { ?>
                        <td class="content_row_clear value"><input type="checkbox" name="state_<?=$calc->getId()?>" value="1" <?if($calc->getState()) echo "checked"?>></td>
                    <? } ?>
                </tr>

            </table>
        </div>
    </div>

    <!-- Kalkulationskopf -->
    <? echo $savemsg; ?>
</form>