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

    function showGoogleMaps()
    {
        newwindow = window.open('libs/modules/businesscontact/businesscontact.maps.php?bcid=<?=$order->getCustomer()->getId()?>', "_blank", "width=1000,height=800,left=0,top=0,scrollbars=yes");
        newwindow = focus();
    }

    function ManualOverride(obj)
    {
        calc_ele = obj.name;
        var id;
        id = calc_ele.match(/^man_override_(\d+)/)[1];
        var charge = document.getElementById('add_charge_'+id).value;
        charge = charge.replace(".","");
        charge = parseFloat(charge.replace(",","."));
        var endprice = document.getElementById('hidden_endprice_'+id).value;
        endprice = endprice.replace(".","");
        endprice = parseFloat(endprice.replace(",","."));
        var override = parseFloat(obj.value.replace(",","."));
        var add = "";
        var new_charge = 0;
        if (endprice > override) {
            add = "-";
            new_charge = endprice - override;
        } else {
            new_charge = override - endprice;
        }
        new_charge = new_charge.toString().replace(".",",");
        var output = add+new_charge;
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
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#step4_form').submit();",'glyphicon-floppy-disk');
if ($order->getArticleid()==0){
    $quickmove->addItem('Artikel Speichern','#',"askDel('index.php?page=libs/modules/article/article.php&exec=fromorder&orderid=".$order->getId()."')",'glyphicon-floppy-disk');
}
elseif ($order->getArticleid()>0){
    $quickmove->addItem('Zum Artikel',"index.php?page=libs/modules/article/article.php&exec=edit&aid=".$order->getArticleid(),'glyphicon-step-backward');
    $quickmove->addItem('Artikel aktualisieren','#',"askDel('index.php?page=libs/modules/article/article.php&exec=uptfromorder&orderid=".$order->getId()."&aid=".$order->getArticleid()."')",'glyphicon-floppy-disk');

}
if($_USER->hasRightsByGroup(Group::RIGHT_DELETE_ORDER) || $_USER->isAdmin()){
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
        <div class="panel-body">
            <div class="row">
                <div class="col-md-1">
                    <b><?= $_LANG->get('Produkt') ?>:</b>
                </div>
                <div class="col-md-2">
                    <?= $order->getProduct()->getName() ?>
                </div>
                <div class="col-md-2">
                    <b><?= $_LANG->get('Beschreibung') ?>:</b>
                </div>
                <div class="col-md-2">
                    <?= $order->getProduct()->getDescription() ?>
                </div>
            </div>
            <hr>
            <div class="row form-horizontal">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="" class="col-md-2 control-label">Titel</label>
                        <div class="col-md-10">
                            <input type="text" class="form-control" name="order_title" value="<?=$order->getTitle()?>" placeholder="Titel">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row form-horizontal">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="col-md-4 control-label">Rechnungsadresse</label>
                        <div class="col-md-8">
                            <select name="invoice_address" id="invoice_address" class="form-control">
                                <option value=""><?=$_LANG->get('keine gesonderte Rechnungsadresse')?></option>
                                <?
                                foreach($order->getCustomer()->getInvoiceAddresses() as $invc)
                                {
                                    echo '<option value="'.$invc->getId().'" ';
                                    if($order->getInvoiceAddress()->getId() == $invc->getId()) echo "selected";
                                    echo '>'.$invc->getNameAsLine().', '.$invc->getAddressAsLine().'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-4 control-label">Zahlungsbedingung</label>
                        <div class="col-md-8">
                            <select name="payment_terms" id="payment_terms" class="form-control">
                                <option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt</option>
                                <?
                                foreach(PaymentTerms::getAllPaymentConditions(PaymentTerms::ORDER_NAME) as $dt)
                                {
                                    echo '<option value="'.$dt->getId().'" ';
                                    if($order->getPaymentTerms()->getId() == $dt->getId()) echo "selected";
                                    echo '>'.$dt->getName1().'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-4 control-label">Lieferdatum</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" id="idx_delivery_date" name="delivery_date" value="<? if($order->getDeliveryDate() > 0) echo date('d.m.Y', $order->getDeliveryDate())?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-4 control-label">Verant. Mitarbeiter</label>
                        <div class="col-md-8">
                            <select name="intern_contactperson" id="intern_contactperson" class="form-control">
                                <option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt</option>
                                <?
                                foreach($all_user as $us)
                                {
                                    echo '<option value="'.$us->getId().'" ';
                                    if($order->getInternContact()->getId() == $us->getId()) echo "selected";
                                    echo '>'.$us->getNameAsLine().'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-4 control-label">Ansprechpartner</label>
                        <div class="col-md-8">
                            <select name="cust_contactperson" id="cust_contactperson" class="form-control">
                                <option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt</option>
                                <?
                                foreach($order->getCustomer()->getContactpersons() as $cp)
                                {
                                    echo '<option value="'.$cp->getId().'" ';
                                    if($order->getCustContactperson()->getId() == $cp->getId()) echo "selected";
                                    echo '>'.$cp->getNameAsLine().'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-4 control-label">Status</label>
                        <div class="col-md-8">
                            <div class="form-control">
                                <?php
                                $states = [
                                    ['s' => '1', 'gif' => 'red.svg'],
                                    ['s' => '2', 'gif' => 'orange.svg'],
                                    ['s' => '3', 'gif' => 'yellow.svg'],
                                    ['s' => '4', 'gif' => 'lila.svg'],
                                    ['s' => '5', 'gif' => 'green.svg'],
                                ];
                                foreach ($states as $state){
                                    ?>
                                    <a href="index.php?page=<?=$_REQUEST['page']?>&id=<?=$order->getId()?>&exec=edit&step=4&setStatus=<?=$state['s']?>">
                                        <?
                                        echo '<img class="select" title="'.getOrderStatus($state['s']).'" src="./images/status/';
                                        if($order->getStatus() == $state['s'])
                                            echo $state['gif'];
                                        else
                                            echo 'black.svg';
                                        echo '">';
                                        ?>
                                    </a>
                                    <?php
                                }
                                ?><?php echo getOrderStatus($order->getStatus(), true);?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="" class="col-md-4 control-label">Lieferadresse</label>
                        <div class="col-md-8">
                            <select name="delivery_address" id="delivery_address" class="form-control">
                                <option value=""><?=$_LANG->get('an Standardadresse')?></option>
                                <?php
                                foreach($order->getCustomer()->getDeliveryAddresses() as $deliv)
                                {
                                    echo '<option value="'.$deliv->getId().'" ';
                                    if($order->getDeliveryAddress()->getId() == $deliv->getId()) echo "selected";
                                    echo '>'.$deliv->getNameAsLine().', '.$deliv->getAddressAsLine().'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-4 control-label">Lieferbedingung</label>
                        <div class="col-md-8">
                            <select name="delivery_terms" id="delivery_terms" class="form-control">
                                <option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt</option>
                                <?
                                foreach(DeliveryTerms::getAllDeliveryConditions(DeliveryTerms::ORDER_NAME) as $dt)
                                {
                                    echo '<option value="'.$dt->getId().'" ';
                                    if($order->getDeliveryTerms()->getId() == $dt->getId()) echo "selected";
                                    echo '>'.$dt->getName1().'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-4 control-label">Lieferkosten</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text" class="form-control" id="delivery_cost" name="delivery_cost" value="<?=printPrice($order->getDeliveryCost())?>" placeholder="Lieferkosten">
                                <span class="input-group-addon">€</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-md-4 control-label">Int. Bemerkungen</label>
                        <div class="col-md-8">
                            <textarea class="form-control" id="order_notes" name="order_notes"><?=$order->getNotes()?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <?php
                        // Beilagen Export
                        if (strlen($order->getBeilagen()) > 0){
                            if (strpos($order->getBeilagen(),"\r\n") === false){
                                $filename1 = './docs/'.$_USER->getId().'-Beilagen.csv';
                                $csv_file = fopen($filename1, "w");
                                $csv_string = $order->getBeilagen();
                                $csv_string = iconv('UTF-8', 'ISO-8859-1', $csv_string);
                                fwrite($csv_file, $csv_string);
                                fclose($csv_file);
                            } else {
                                $filename1 = './docs/'.$_USER->getId().'-Beilagen.csv';
                                $csv_file = fopen($filename1, "w");
                                $csv_string = "";
                                foreach(explode("\r\n", $order->getBeilagen()) as $line) {
                                    $csv_string .= $line . ";\n";
                                }
                                $csv_string = iconv('UTF-8', 'ISO-8859-1', $csv_string);
                                fwrite($csv_file, $csv_string);
                                fclose($csv_file);
                            }
                        }
                        ?>
                        <label for="" class="col-md-4 control-label">Beilagen-Hinweis (&nbsp;<a href="./docs/<?=$_USER->getId()?>-Beilagen.csv"><span class="glyphicons glyphicons-file-export"></span></a>&nbsp;)</label>
                        <div class="col-md-8">
                            <textarea class="form-control" id="order_beilagen" name="order_beilagen"><?=$order->getBeilagen()?></textarea>
                        </div>
                    </div>
                </div>
            </div>
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

                              <? if($_USER->hasRightsByGroup(Group::RIGHT_DELETE_ORDER) || $_USER->isAdmin()){ ?>
                                  <a class="icon-link" href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$order->getId()?>&subexec=delete&calc_id=<?=$calc->getId()?>&step=4')"><span class="glyphicons glyphicons-remove"></span></a>
                              <?}?>
                          </td>
                      <? } ?>
                  </tr>
                  <!-- added by ascherer -->
                  <tr class="color1">
                      <td class="content_row_header"><?=$_LANG->get('Titel')?></td>
                      <? foreach($calculations as $calc) { ?>
                          <td class="content_row_clear value">
                              <input type="text" name="calc_title_<?=$calc->getId()?>" id="calc_title_<?=$calc->getId()?>" value="<?=$calc->getTitle()?>">
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
                                      echo $calc->getPaperContent()->getName().', '.$calc->getPaperContent()->getSelectedWeight().' '.$_LANG->get('g').'';?>
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
                                      <?=printPrice(($calc->getPaperCount(Calculation::PAPER_CONTENT) * $calc->getPaperContentHeight())/10000,2)?> <?=$_LANG->get('Laufmeter')?>
                                      <?=printPrice($calc->getPaperContent()->getSumPrice($calc->getPaperCount(Calculation::PAPER_CONTENT) + $calc->getPaperContentGrant()))?>
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
                                      <input name="grant_content_<?=$calc->getId()?>" style="width:40px;text-align:center" value="<?=printBigInt($calc->getPaperContentGrant())?>"> <?=$_LANG->get('Bogen')?>
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
                                  <?=printPrice($calc->getChromaticitiesContent()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight()/1000000) * ($calc->getPaperCount(Calculation::PAPER_CONTENT))*(1.4*0.5/1000) * ($calc->getChromaticitiesContent()->getColorsBack() + $calc->getChromaticitiesContent()->getColorsFront())))?>
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
                                  <input name="grant_addcontent_<?=$calc->getId()?>" style="width:40px;text-align:center" value="<?=printBigInt($calc->getPaperAddContentGrant())?>"> <?=$_LANG->get('Bogen')?>
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
                                  <?=printPrice($calc->getChromaticitiesAddContent()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight()/1000000) * ($calc->getPaperCount(Calculation::PAPER_ADDCONTENT))*(1.4*0.5/1000) * ($calc->getChromaticitiesAddContent()->getColorsBack() + $calc->getChromaticitiesAddContent()->getColorsFront())))?>
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
                                  <input name="grant_addcontent2_<?=$calc->getId()?>" style="width:40px;text-align:center" value="<?=printBigInt($calc->getPaperAddContent2Grant())?>">
                                  <?=$_LANG->get('Bogen')?>
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
                                  <?=printPrice($calc->getChromaticitiesAddContent2()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight()/1000000) * ($calc->getPaperCount(Calculation::PAPER_ADDCONTENT2))*(1.4*0.5/1000) * ($calc->getChromaticitiesAddContent2()->getColorsBack() + $calc->getChromaticitiesAddContent2()->getColorsFront())))?>
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
                                  <input name="grant_addcontent3_<?=$calc->getId()?>" style="width:40px;text-align:center" value="<?=printBigInt($calc->getPaperAddContent3Grant())?>">
                                  <?=$_LANG->get('Bogen')?>
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
                                  <?=printPrice($calc->getChromaticitiesAddContent3()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight()/1000000) * ($calc->getPaperCount(Calculation::PAPER_ADDCONTENT3))*(1.4*0.5/1000) * ($calc->getChromaticitiesAddContent3()->getColorsBack() + $calc->getChromaticitiesAddContent3()->getColorsFront())))?>
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
                                  <input name="grant_envelope_<?=$calc->getId()?>" style="width:40px;text-align:center" value="<?=printBigInt($calc->getPaperEnvelopeGrant())?>"> <?=$_LANG->get('Bogen')?>
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
                                  <?=printPrice($calc->getChromaticitiesEnvelope()->getPricekg() * (($calc->getProductFormatWidth() * $calc->getProductFormatHeight()/1000000) * ($calc->getPaperCount(Calculation::PAPER_ENVELOPE))*(1.4*0.5/1000) * ($calc->getChromaticitiesEnvelope()->getColorsBack() + $calc->getChromaticitiesEnvelope()->getColorsFront())))?>
                                  <?=$_USER->getClient()->getCurrency()?>
                              </td>
                          <? } ?>
                      </tr>
                  <? } ?>
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

                  <tr>
                      <td class="content_row_clear">&nbsp;</td>
                  </tr>
                  <tr>
                      <td class="content_row_header"><?=$_LANG->get('Farbkosten')?></td>
                      <? foreach($calculations as $calc) { ?>
                          <td class="content_row_clear value" align="center">
                              <?php echo printPrice($calc->getColorCost(),2);?> €
                          </td>
                      <?  } ?>
                  </tr>
                  <tr>
                      <td class="content_row_clear">&nbsp;</td>
                  </tr>

                  <? // -------- START --------------- Positionskosten ---------------------------- ?>
                  <tr>
                      <td class="content_row_header"><?=$_LANG->get('Zus. Positionen')?></td>
                  </tr>
                  <tr class="color1">
                      <td class="content_row_clear">&ensp;</td>
                      <? foreach($calculations as $calc) {
                          $all_calc_positions = $calc->getPositions()?>
                          <td class="content_row_clear value" align="center">
                              <?if (count($all_calc_positions) > 0 && $all_calc_positions != false){
                                  foreach ($all_calc_positions AS $calc_pos){
                                      $tmpart_amount = $calc_pos->getQuantity();
                                      $tmpart_scale = $calc_pos->getScale();

                                      echo $calc_pos->getComment() ." : ";

                                      if($calc_pos->getType() == CalculationPosition::TYPE_ARTICLE){
                                          // falls Position ein Artikel ist
                                          $tmp_art =  new Article($calc_pos->getObjectID());
                                          if ($tmpart_scale == CalculationPosition::SCALE_PER_KALKULATION){
                                              echo printPrice($tmpart_amount * $calc_pos->getPrice($tmpart_amount));
                                          } elseif ($tmpart_scale == CalculationPosition::SCALE_PER_PIECE){
                                              echo printPrice($tmpart_amount * $calc_pos->getPrice($tmpart_amount * $calc->getAmount()) * $calc->getAmount());
                                          }
                                      } else {
                                          // falls Position manuell ist
                                          if ($tmpart_scale == CalculationPosition::SCALE_PER_KALKULATION){
                                              echo printPrice($tmpart_amount * $calc_pos->getPrice($tmpart_amount));
                                          } elseif ($tmpart_scale == CalculationPosition::SCALE_PER_PIECE){
                                              echo printPrice($tmpart_amount * $calc_pos->getPrice($tmpart_amount) * $calc->getAmount());
                                          }
                                      }
                                      echo " ".$_USER->getClient()->getCurrency()."<br/>";
                                  }?>
                              <?} else {
                                  echo printPrice(0)." ".$_USER->getClient()->getCurrency();
                              }?>
                          </td>
                      <?}?>
                  </tr>
                  <? // -------- ENDE ---------------- Positionskosten ---------------------------- ?>

                  <tr>
                      <td class="content_row_clear">&nbsp;</td>
                  </tr>

                  <? if($_USER->hasRightsByGroup(Group::RIGHT_DETAILED_CALCULATION)) { ?>
                      <tr>
                          <td class="content_row_header"><?=$_LANG->get('Zwischensumme')?></td>
                          <? foreach($calculations as $calc) { ?>
                              <td class="content_row_header value" align="center"><?=printPrice($calc->getPricesub())?> <?=$_USER->getClient()->getCurrency()?></td>
                          <? } ?>
                      </tr>
                      <tr>
                          <td class="content_row_clear">&nbsp;</td>
                      </tr>

                      <tr>
                          <td class="content_row_header"><?=$_LANG->get('Zusatzkosten')?></td>
                      </tr>
                      <tr class="color1">
                          <td class="content_row_clear"><?=$_LANG->get('Marge')?></td>
                          <? foreach($calculations as $calc) { ?>
                              <td class="content_row_clear value">
                                  <input name="margin_<?=$calc->getId()?>" style="width:60px;text-align:center" value="<?=printPrice($calc->getMargin())?>"> %
                              </td>
                          <?  } ?>
                      </tr>
                      <tr>
                          <td class="content_row_clear">&nbsp;</td>
                      </tr>
                      <tr>
                          <td class="content_row_header"><?=$_LANG->get('Zwischensumme')?></td>
                          <? foreach($calculations as $calc) { ?>
                              <td class="content_row_header value" align="center"><?=printPrice($calc->getPricesub() + ($calc->getPricesub() / 100 * $calc->getMargin()))?> <?=$_USER->getClient()->getCurrency()?></td>
                          <? } ?>
                      </tr>
                      <tr>
                          <td class="content_row_clear">&nbsp;</td>
                      </tr>
                  <? } ?>
                  <tr>
                      <td class="content_row_header"><?=$_LANG->get('Preiskorrekturen')?></td>
                  </tr>
                  <tr class="color1">
                      <td class="content_row_clear"><?=$_LANG->get('Rabatt')?></td>
                      <? foreach($calculations as $calc) { ?>
                          <td class="content_row_clear value">
                              <input name="discount_<?=$calc->getId()?>" style="width:60px;text-align:center" value="<?=printPrice($calc->getDiscount())?>"> %
                          </td>
                      <?  } ?>
                  </tr>
                  <tr class="color1">
                      <td class="content_row_clear"><?=$_LANG->get('sonst. Auf-/Abschlag')?></td>
                      <? foreach($calculations as $calc) { ?>
                          <td class="content_row_clear value">
                              <input name="add_charge_<?=$calc->getId()?>" id="add_charge_<?=$calc->getId()?>" style="width:60px;text-align:center" value="<?=printPrice($calc->getAddCharge())?>">
                              <?=$_USER->getClient()->getCurrency()?>
                          </td>
                      <?  } ?>
                  </tr>
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

                  <tr>
                      <td class="content_row_header"><?=$_LANG->get('Man. Endpreis')?></td>
                      <? foreach($calculations as $calc) { ?>
                          <td class="content_row_header value">
                              <input name="man_override_<?=$calc->getId()?>" id="man_override_<?=$calc->getId()?>" onchange='ManualOverride(this)' style="width:60px;text-align:center" value="<?=printPrice($calc->getPricetotal())?>">
                              <?=$_USER->getClient()->getCurrency()?>
                          </td>
                      <? } ?>
                  </tr>

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