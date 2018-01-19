<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/accounting/invoiceout.class.php';

if($_REQUEST["subexec"] == "delete")
{
    $invoiceout = new InvoiceOut($_REQUEST["id"]);
    $savemsg = getSaveMessage($invoiceout->delete());
}

if ($_REQUEST["subexec"] == "save"){
    if (isset($_REQUEST['payeddate'])){
        $paydate = strtotime($_REQUEST["payeddate"]);
        if ($paydate > 0){
            $invoiceout = new InvoiceOut((int)$_REQUEST["id"]);

            if ($invoiceout->getDuedatesk2() > 0 && $invoiceout->getDuedatesk2() > time()){
                $skontovalue = $invoiceout->getGrossvalue() / 100 * $invoiceout->getSk2Percent();
            } else if ($invoiceout->getDuedatesk1() > 0 && $invoiceout->getDuedatesk1() > time()){
                $skontovalue = $invoiceout->getGrossvalue() / 100 * $invoiceout->getSk1Percent();
            } else {
                $skontovalue = 0;
            }
            $array = [
                'payeddate' => $paydate,
                'status' => 2,
                'payedskonto' => $skontovalue,
                'bank' => $_REQUEST['bank'],
            ];
            $invoiceout = new InvoiceOut((int)$_REQUEST["id"], $array);
            $invoiceout->save();
        }
    }
}
$invoiceout = new InvoiceOut((int)$_REQUEST["id"]);
$receipt = Receipt::getForOrigin($invoiceout);

?>
<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page=libs/modules/accounting/invoiceout.overview.php',null,'glyphicon-step-backward');
if ($invoiceout->getStatus() != 3)
    $quickmove->addItem('Speichern','#',"$('#invout_form').submit();",'glyphicon-floppy-disk');

echo $quickmove->generate();
// end of Quickmove generation ?>

<?php if (isset($savemsg)) { ?>
    <div class="alert">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <strong>Hinweis!</strong> <?=$savemsg?>
    </div>
<?php } ?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Rechnung - <?php echo $invoiceout->getNumber();?>
                    <span class="pull-right">
                        <button class="btn btn-xs btn-success" type="button" onclick="window.location.href='libs/modules/documents/document.get.iframe.php?getDoc=<?= $invoiceout->getDoc() ?>&version=email';">
                            <span class="filetypes filetypes-pdf"></span>
                            E-Mail
                        </button>
                        <button class="btn btn-xs btn-success" type="button" onclick="window.location.href='libs/modules/documents/document.get.iframe.php?getDoc=<?= $invoiceout->getDoc() ?>&version=print';">
                            <span class="filetypes filetypes-pdf"></span>
                            Print
                        </button>
                    </span>
                </h3>
            </div>
            <div class="panel-body">
                <form action="index.php?page=<?=$_REQUEST['page']?>" id="invout_form" name="invout_form" method="post" role="form" class="form-horizontal">
                    <input type="hidden" id="id" name="id" value="<?=$invoiceout->getId()?>" />
                    <input type="hidden" id="exec" name="exec" value="edit" />
                    <input type="hidden" id="subexec" name="subexec" value="save" />
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Titel</label>
                        <div class="col-sm-4 form-text">
                            <?php echo $invoiceout->getColinv()->getTitle();?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">VO-Nummer</label>
                        <div class="col-sm-4 form-text">
                            <a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid=<?php echo $invoiceout->getColinv()->getId();?>"><?php echo $invoiceout->getColinv()->getNumber();?></a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-4 form-text">
                            <?php
                            switch ($invoiceout->getStatus()){
                                case 0:
                                    echo 'gelöscht';
                                    break;
                                case 1:
                                    echo 'offen';
                                    break;
                                case 2:
                                    echo 'bezahlt';
                                    break;
                                case 3:
                                    echo 'storniert';
                                    break;
                            }
                            ;?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Netto Betrag</label>
                        <div class="col-sm-4 form-text">
                            <?php echo printPrice($invoiceout->getNetvalue(),2);?>€
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Brutto Betrag</label>
                        <div class="col-sm-4 form-text">
                            <?php echo printPrice($invoiceout->getGrossvalue(),2);?>€
                        </div>
                    </div>

                    <?php
                    if ($invoiceout->getDuedatesk2() > 0 && $invoiceout->getDuedatesk2() > time()){
                        $gross = ($invoiceout->getGrossvalue() - ($invoiceout->getGrossvalue() / 100 * $invoiceout->getSk2Percent()));
                        $net = ($invoiceout->getNetvalue() - ($invoiceout->getNetvalue() / 100 * $invoiceout->getSk2Percent()));
                        $skontovalue = $invoiceout->getGrossvalue() / 100 * $invoiceout->getSk2Percent();
                        $skonto = 'Skonto 2';
                    } else if ($invoiceout->getDuedatesk1() > 0 && $invoiceout->getDuedatesk1() > time()){
                        $gross = ($invoiceout->getGrossvalue() - ($invoiceout->getGrossvalue() / 100 * $invoiceout->getSk1Percent()));
                        $net = ($invoiceout->getNetvalue() - ($invoiceout->getNetvalue() / 100 * $invoiceout->getSk1Percent()));
                        $skontovalue = $invoiceout->getGrossvalue() / 100 * $invoiceout->getSk2Percent();
                        $skonto = 'Skonto 1';
                    } else {
                        $gross = $invoiceout->getGrossvalue();
                        $net = $invoiceout->getNetvalue();
                        $skontovalue = 0;
                        $skonto = 'Kein Skonto';
                    }
                    ?>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Netto Betrag nach Skonto</label>
                        <div class="col-sm-4 form-text">
                            <?php echo printPrice($net,2);?>€ (<?php echo $skonto;?>)
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Brutto Betrag nach Skonto</label>
                        <div class="col-sm-4 form-text">
                            <?php echo printPrice($gross,2);?>€ (<?php echo $skonto;?>)
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Abzug Skonto Brutto</label>
                        <div class="col-sm-4 form-text">
                            <?php echo printPrice($skontovalue,2);?>€ (<?php echo $skonto;?>)
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Erstellt</label>
                        <div class="col-sm-4 form-text">
                            <?php echo date('d.m.y',$invoiceout->getCrtdate());?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Fälligkeit</label>
                        <div class="col-sm-4 form-text">
                            <?php echo date('d.m.y',$invoiceout->getDuedate());?>
                        </div>
                    </div>

                    <?php if ($invoiceout->getPayeddate()>0){?>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Bezahlt am</label>
                            <div class="col-sm-4 form-text">
                                <?php echo date('d.m.y',$invoiceout->getPayeddate());?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Bank</label>
                            <div class="col-sm-4 form-text">
                                <?php echo $invoiceout->getBank();?>
                            </div>
                        </div>
                    <?php } else if($invoiceout->getStatus() != 3) { ?>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Bezahlt am</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="payeddate" id="payeddate"
                                       value="<?php if ($invoiceout->getPayeddate() > 0) echo date('d.m.y',$invoiceout->getPayeddate());?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Bank</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="bank" id="bank" value="<?php echo $invoiceout->getBank();?>">
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($invoiceout->getStatus() < 3 && $receipt->getExported() == 0){?>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Storno</label>
                        <div class="col-sm-4">
                            <button class="btn btn-xs btn-success" type="button"
                                    onclick="doStorno(<?php echo $invoiceout->getId();?>);">
                                     <?= $_LANG->get('Storno'); ?>
                            </button>
                        </div>
                    </div>
                    <?php }?>

                    <?php if ($invoiceout->getStatus() == 1) {?>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Mahnung</label>
                        <div class="col-sm-4">
                            <button class="btn btn-xs btn-success" type="button"
                                    onclick="document.location.href='index.php?page=libs/modules/accounting/invoicewarning.php&exec=new&invid=<?php echo $invoiceout->getDoc(); ?>';">
                                <?= $_LANG->get('Mahnung') ?>
                            </button>
                        </div>
                    </div>
                    <?php }?>
                </form>
            </div>
        </div>
    </div>
</div>

<?php /*
<div class="row">
    <div class="col-md-12">
        <?php
            $fibuxml = new FibuXML([Receipt::getForOrigin($invoiceout)],[]);
            $xml = $fibuxml->generateReceiptXML();
            $dom = dom_import_simplexml($xml)->ownerDocument;
            $dom->formatOutput = true;
            prettyPrint(htmlentities($dom->saveXML(NULL, LIBXML_NOEMPTYTAG)));
        ?>
    </div>
</div>
 */ ?>

<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>
<script>
    function doStorno(id){
        $.ajax({
                type: "POST",
                url: "libs/modules/accounting/accounting.ajax.php",
                data: { exec: "doStorno", id: id }
            })
            .done(function( msg ) {
                document.location.href='index.php?page=libs/modules/accounting/invoiceout.overview.php';
            });
    }
    function doDelete(id){
        $.ajax({
                type: "POST",
                url: "libs/modules/accounting/accounting.ajax.php",
                data: { exec: "doDelete", id: id }
            })
            .done(function( msg ) {
                document.location.href='index.php?page=libs/modules/accounting/invoiceout.overview.php';
            });
    }
    $(function () {
        $('#payeddate').datetimepicker({
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
            timepicker: false,
            format: 'd.m.Y'
        });
    });
</script>