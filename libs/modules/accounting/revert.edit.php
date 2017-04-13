<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */
require_once 'libs/modules/accounting/revert.class.php';

if($_REQUEST["subexec"] == "delete")
{
    $revert = new Revert($_REQUEST["id"]);
    $savemsg = getSaveMessage($revert->delete());
}

if ($_REQUEST["subexec"] == "save"){
    if (isset($_REQUEST['payeddate'])){
        $paydate = strtotime($_REQUEST["payeddate"]);
        if ($paydate > 0){
            $array = [
                'payeddate' => $paydate,
                'status' => 2,
            ];
            $revert = new Revert((int)$_REQUEST["id"], $array);
            $revert->save();
        }
    }
}
$revert = new Revert((int)$_REQUEST["id"]);

?>
<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page=libs/modules/accounting/invoiceout.overview.php',null,'glyphicon-step-backward');
if ($revert->getStatus() != 3)
    $quickmove->addItem('Speichern','#',"$('#revert_form').submit();",'glyphicon-floppy-disk');

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
                    Gutschrift - <?php echo $revert->getNumber();?>
                    <span class="pull-right">
                        <button class="btn btn-xs btn-success" type="button" onclick="window.location.href='libs/modules/documents/document.get.iframe.php?getDoc=<?php echo $revert->getDoc();?>&version=email';">
                            <span class="filetypes filetypes-pdf"></span>
                            E-Mail
                        </button>
                        <button class="btn btn-xs btn-success" type="button" onclick="window.location.href='libs/modules/documents/document.get.iframe.php?getDoc=<?php echo $revert->getDoc();?>&version=print';">
                            <span class="filetypes filetypes-pdf"></span>
                            Print
                        </button>
                    </span>
                </h3>
            </div>
            <div class="panel-body">
                <form action="index.php?page=<?=$_REQUEST['page']?>" id="revert_form" name="revert_form" method="post" role="form" class="form-horizontal">
                    <input type="hidden" id="id" name="id" value="<?=$revert->getId()?>" />
                    <input type="hidden" id="exec" name="exec" value="edit" />
                    <input type="hidden" id="subexec" name="subexec" value="save" />
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Titel</label>
                        <div class="col-sm-4 form-text">
                            <?php echo $revert->getColinv()->getTitle();?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">VO-Nummer</label>
                        <div class="col-sm-4 form-text">
                            <?php echo $revert->getColinv()->getNumber();?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Status</label>
                        <div class="col-sm-4 form-text">
                            <?php
                            switch ($revert->getStatus()){
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
                            <?php echo printPrice($revert->getNetvalue(),2);?>€
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Brutto Betrag</label>
                        <div class="col-sm-4 form-text">
                            <?php echo printPrice($revert->getGrossvalue(),2);?>€
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Erstellt</label>
                        <div class="col-sm-4 form-text">
                            <?php echo date('d.m.y',$revert->getCrtdate());?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Fälligkeit</label>
                        <div class="col-sm-4 form-text">
                            <?php echo date('d.m.y',$revert->getDuedate());?>
                        </div>
                    </div>

                    <?php if ($revert->getPayeddate()>0){?>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Ausgezahlt am</label>
                            <div class="col-sm-4 form-text">
                                <?php echo date('d.m.y',$revert->getPayeddate());?>
                            </div>
                        </div>
                    <?php } elseif ($revert->getStatus() != 3) { ?>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Ausgezahlt am</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" name="payeddate" id="payeddate"
                                       value="<?php if ($revert->getPayeddate() > 0) echo date('d.m.y',$revert->getPayeddate());?>">
                            </div>
                        </div>
                    <?php } ?>

                    <?php if ($revert->getStatus() < 3){?>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Storno</label>
                        <div class="col-sm-4">
                            <button class="btn btn-xs btn-success" type="button"
                                    onclick="doStornoRevert(<?php echo $revert->getId();?>);">
                                     <?= $_LANG->get('Storno'); ?>
                            </button>
                        </div>
                    </div>
                    <?php }?>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <?php
        $fibuxml = new FibuXML([Receipt::getForOrigin($revert)]);
        $xml = $fibuxml->generateXML1();
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        prettyPrint(htmlentities($dom->saveXML(NULL, LIBXML_NOEMPTYTAG)));
        ?>
    </div>
</div>

<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>
<script>
    function doStornoRevert(id){
        $.ajax({
                type: "POST",
                url: "libs/modules/accounting/accounting.ajax.php",
                data: { exec: "doStornoRevert", id: id }
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