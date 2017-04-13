<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'FibuXML.class.php';

$date_min = date("d.m.Y",mktime(0,0,1,date("m",time()),1,date("Y",time())));
$date_max = date("d.m.Y",mktime(0,0,1,date("m",time()),date("t",time()),date("Y",time())));
$show_exported = 0;

if ($_REQUEST["exec"] == "filter"){
    if (isset($_REQUEST["date_min"]) && strtotime($_REQUEST["date_min"]) > 0)
        $date_min = $_REQUEST["date_min"];
    if (isset($_REQUEST["date_max"]) && strtotime($_REQUEST["date_max"]) > 0)
        $date_max = $_REQUEST["date_max"];
    if (isset($_REQUEST["show_exported"]) && $_REQUEST["show_exported"] == 1)
        $show_exported = 1;
}

if ($_REQUEST["subexec"] == "reset" && $_REQUEST["reset"] > 0){
    $reset_receipt = new Receipt((int)$_REQUEST["reset"]);
    $reset_receipt->setExported(0);
    $reset_receipt->save();
}

$receipts = Receipt::getAllFiltered($date_min, $date_max, $show_exported);

if ($_REQUEST["subexec"] == "export"){
    $export_receipts = [];
    $export_busicons_ids = [];
    $export_busicons = [];

    foreach ($receipts as $receipt) {
        $errors = $receipt->validate();
        if (count($errors['fatal']) == 0) {
            $export_receipts[] = $receipt;
            if (!in_array($receipt->getOrigin()->getColinv()->getBusinesscontact()->getId(),$export_busicons_ids)){
                $export_busicons_ids[] = $receipt->getOrigin()->getColinv()->getBusinesscontact()->getId();
                $export_busicons[] = $receipt->getOrigin()->getColinv()->getBusinesscontact();
            }
        }
    }

    $fibuexport = new FibuXML($export_receipts, $export_busicons);

    // Generate receipt xml
    $xml = $fibuexport->generateReceiptXML();
    $dom = dom_import_simplexml($xml)->ownerDocument;
    $dom->formatOutput = true;
    $s = simplexml_import_dom($dom);
    $name = date('YmdHi',time()).'_Buchungen_'.$_USER->getLogin().'.xml';
    $s->saveXML('docs/fibuexports/'.$name);

    // Generate busicon xml
    $xml = $fibuexport->generateBusiconXML();
    $dom = dom_import_simplexml($xml)->ownerDocument;
    $dom->formatOutput = true;
    $s = simplexml_import_dom($dom);
    $name = date('YmdHi',time()).'_Personenkonten_'.$_USER->getLogin().'.xml';
    $s->saveXML('docs/fibuexports/'.$name);


    if (file_exists('docs/fibuexports/'.$name)){
        foreach ($export_receipts as $item) {
            $item->setExported(time());
            $item->save();
        }
    }
}

?>


<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Buchhaltungs-Export
            <span class="pull-right">
                <button class="btn btn-xs btn-success" type="button" onclick="ExportCSV();">Export</button>
            </span>
        </h3>
    </div>
    <div class="panel-body">
        <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="fibuexportform" id="fibuexportform" method="post" class="form-horizontal" role="form">
            <input type="hidden" name="exec" id="exec" value="filter">
            <input type="hidden" name="subexec" id="subexec" value="">
            <input type="hidden" name="export" id="export" value="">
            <input type="hidden" name="reset" id="reset" value="">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Filter
                         <span class="pull-right">
                                  <button class="btn btn-xs btn-success" type="submit">
                                      <span class="glyphicons glyphicons-refresh"></span>
                                      <?= $_LANG->get('Refresh') ?>
                                  </button>
                         </span>
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Datum (erstellt)</label>
                            <div class="col-sm-4">
                                <input name="ajax_date_min" id="ajax_date_min" type="hidden" value="<?php if (isset($_REQUEST["date_min"])) echo strtotime($_REQUEST["date_min"]); else echo strtotime($date_min);?>"/>
                                <input name="date_min" id="date_min" class="form-control" onfocus="markfield(this,0)"
                                       onblur="markfield(this,1)" value="<?php if (isset($_REQUEST["date_min"])) echo $_REQUEST["date_min"]; else echo $date_min;?>">
                            </div>
                            <label for="" class="col-sm-2 control-label" style="text-align: center">Bis</label>
                            <div class="col-sm-4">
                                <input name="ajax_date_max" id="ajax_date_max" type="hidden" value="<?php if (isset($_REQUEST["date_max"])) echo strtotime($_REQUEST["date_max"]); else echo strtotime($date_max);?>"/>
                                <input name="date_max" id="date_max" class="form-control" onfocus="markfield(this,0)"
                                       onblur="markfield(this,1)" value="<?php if (isset($_REQUEST["date_max"])) echo $_REQUEST["date_max"]; else  echo $date_max;?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Exportierte zeigen</label>
                            <div class="col-sm-10">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="show_exported" id="show_exported" value="1" <?php if ($_REQUEST["show_exported"] == 1) echo " checked ";?>>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Export
                        <span class="pull-right">(Aktuelle gesetzte Filter)</span>
                    </h3>
                </div>
                <div class="panel-body">
                    <button class="btn btn-xs btn-success" type="button" onclick="export_sap();">SAP</button>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Elemente</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover" id="fibuexport">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Dok.-Nr.</th>
                            <th>Typ</th>
                            <th>Netto</th>
                            <th>Brutto</th>
                            <th>Erstellt</th>
                            <th>Exportiert</th>
                            <th>Fehler</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $sum_net = 0.0;
                        $sum_gross = 0.0;
                        foreach ($receipts as $receipt) {
                            $class = 'success';
                            $errors = $receipt->validate();
                            if (count($errors['warning'])>0)
                                $class = 'warning';
                            if (count($errors['fatal'])>0)
                                $class = 'danger';
                            ?>
                            <div id="colinv_<?php echo $receipt->getId();?>" style="display: none;"><?php echo $receipt->getOrigin()->getColinv()->getId();?></div>
                            <div id="origin_<?php echo $receipt->getId();?>" style="display: none;">
                                <?php
                                if ($receipt->getOriginType() == Receipt::ORIGIN_INVOICE){
                                    echo 'index.php?page=libs/modules/accounting/invoiceout.edit.php&exec=edit&id='.$receipt->getOriginId();
                                } else {
                                    echo 'index.php?page=libs/modules/accounting/revert.edit.php&exec=edit&id='.$receipt->getOriginId();
                                }
                                ?>
                            </div>
                            <tr class="<?php echo $class;?>">
                                <td><?php echo $receipt->getId();?></td>
                                <td id="number_<?php echo $receipt->getId();?>"><?php echo $receipt->getNumber();?></td>
                                <td><?php if ($receipt->getOriginType() == Receipt::ORIGIN_INVOICE) echo 'Rechnung'; else echo 'Gutschrift';?></td>
                                <?php if ($receipt->getOriginType() == Receipt::ORIGIN_INVOICE){
                                    $sum_net += $receipt->getOrigin()->getNetvalue();
                                    $sum_gross += $receipt->getOrigin()->getGrossvalue();
                                    ?>
                                    <td><?php echo printPrice($receipt->getOrigin()->getNetvalue(),2);?></td>
                                    <td><?php echo printPrice($receipt->getOrigin()->getGrossvalue(),2);?></td>
                                <?php } else {
                                    $sum_net -= $receipt->getOrigin()->getNetvalue();
                                    $sum_gross -= $receipt->getOrigin()->getGrossvalue();
                                    ?>
                                    <td><?php echo "-".printPrice($receipt->getOrigin()->getNetvalue(),2);?></td>
                                    <td><?php echo "-".printPrice($receipt->getOrigin()->getGrossvalue(),2);?></td>
                                <?php } ?>
                                <td><?php echo date('d.m.y',$receipt->getDate());?></td>
                                <td><?php if ($receipt->getExported() > 0) echo date('d.m.y',$receipt->getExported());?></td>
                                <td>
                                    <div id="errors_<?php echo $receipt->getId();?>" style="display: none">
                                        <?php echo Receipt::formatValidation($errors,'<br/>');?>
                                    </div>
                                    <button class="btn btn-xs btn-info" type="button" onclick="InfoModal(<?php echo $receipt->getId();?>);">Info</button>
                                    <?php if ($_USER->hasRightsByGroup(Group::RIGHT_FIBU_ADMIN) || $_USER->isAdmin()){?>
                                        <?php if ($receipt->getExported()>0){?>
                                            <button class="btn btn-xs btn-warning" type="button" onclick="Reset(<?php echo $receipt->getId();?>);">Reset</button>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                            <tr>
                                <td></td>
                                <td>Anzahl: <?php echo count($receipts);?></td>
                                <td>Summe:</td>
                                <td><?php echo printPrice($sum_net,2);?>€</td>
                                <td><?php echo printPrice($sum_gross,2);?>€</td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Exporte</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Datei</th>
                        <th>Datum</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $exports = FibuXML::listExports();
                    foreach ($exports as $export) { ?>
                        <tr>
                            <td><a href="docs/fibuexports/<?php echo $export;?>" target="_blank"><?php echo $export;?></a></td>
                            <td><?php echo date('d.m.y H:i',filemtime('docs/fibuexports/'.$export));?></td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function ExportCSV(){
        var datemin = parseInt($('#ajax_date_min').val());
        var datemax = parseInt($('#ajax_date_max').val());
        window.open('libs/modules/accounting/receipt.export.php?datemax='+datemax+'&datemin='+datemin);
    }
    function export_sap(){
        $('#export').val('sap');
        $('#subexec').val('export');
        $('#fibuexportform').submit();
    }
    function Reset(id){
        $('#reset').val(id);
        $('#subexec').val('reset');
        $('#fibuexportform').submit();
    }
    function InfoModal(id){
        $( "#errors_"+id ).dialog({
            title: "Bericht zu "+$('#number_'+id).text(),
            modal: true,
            height: 500,
            width: 900,
            buttons: {
                Vorgang: function() {
                    window.open('index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid='+$('#colinv_'+id).text());
                },
                Ursprung: function() {
                    window.open($('#origin_'+id).text());
                },
                Ok: function() {
                    $( this ).dialog( "close" );
                }
            }
        });
    }

    $(function () {
        $.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
        $('#date_min').datepicker(
            {
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'dd.mm.yy',
                onSelect: function(selectedDate) {
                    $('#ajax_date_min').val(moment($('#date_min').val(), "DD-MM-YYYY").unix());
                }
            }
        );

        $('#date_max').datepicker(
            {
                showOtherMonths: true,
                selectOtherMonths: true,
                dateFormat: 'dd.mm.yy',
                onSelect: function(selectedDate) {
                    $('#ajax_date_max').val(moment($('#date_max').val(), "DD-MM-YYYY").unix()+86340);
                }
            }
        );
    });
</script>