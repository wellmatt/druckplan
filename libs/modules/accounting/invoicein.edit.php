<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Christian Schroeer <cschroeer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/accounting/invoicein.class.php';

if ($_REQUEST["subexec"] == "save"){
    $status = 1;
    if (isset($_REQUEST['payeddate'])){
        $status = 2;
    }
    $array = [
        'number' => $_REQUEST["number"],
        'supplier' => $_REQUEST["supplier"],
        'status' => $status,
        'description' => $_REQUEST["description"],
        'netvalue' => $_REQUEST["netvalue"],
        'tax' => $_REQUEST["tax"],
        'redate' => strtotime($_REQUEST["redate"]),
        'payeddate' => strtotime($_REQUEST["payeddate"]),
        'duedate' => strtotime($_REQUEST["duedate"]),
    ];
    $invoicein = new InvoiceIn((int)$_REQUEST["id"], $array);
    $invoicein->save();
    $_REQUEST["id"] = $invoicein->getId();
}
$invoicein = new InvoiceIn((int)$_REQUEST["id"]);

?>
<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page=libs/modules/accounting/invoicein.overview.php',null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#invin_form').submit();",'glyphicon-floppy-disk');
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
                    Rechnung -  <?php echo $invoicein->getNumber();?> Neu
                </h3>
            </div>
            <div class="panel-body">
                <form action="index.php?page=<?=$_REQUEST['page']?>" id="invin_form" name="invin_form" method="post" role="form" class="form-horizontal">
                    <input type="hidden" id="id" name="id" value="<?=$invoicein->getId()?>" />
                    <input type="hidden" id="exec" name="exec" value="edit" />
                    <input type="hidden" id="subexec" name="subexec" value="save" />

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Re.-Nr.</label>
                        <div class="col-sm-9">
                            <input name="number" id="number" value="<?php echo $invoicein->getNumber();?>" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Lieferant</label>
                        <div class="col-sm-9">
                            <input type="text" id="search_supplier" name="search_supplier"
                                   value="<?php if ($invoicein->getSupplier()->getId() > 0) {
                                       echo $invoicein->getSupplier()->getNameAsLine();
                                   } ?>" class="form-control"/>
                            <input type="hidden" id="supplier" name="supplier"
                                   value="<?php echo $invoicein->getSupplier()->getId(); ?>"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Grund der Ausgabe</label>
                        <div class="col-sm-9">
                            <input name="description" id="description" value="<?php echo $invoicein->getDescription();?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Netto</label>
                        <div class="col-sm-9">
                            <input name="netvalue" id="netvalue" value="<?php echo $invoicein->getNetvalue();?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">MwSt-Satz</label>
                        <div class="col-sm-9">
                            <input name="tax" id="tax" value="<?php echo $invoicein->getTax();?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">RE-Datum</label>
                        <div class="col-sm-9">
                            <input name="redate" id="redate"
                                   value="<?php echo date('d.m.y',$invoicein->getRedate());?>" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Fällig</label>
                        <div class="col-sm-9">
                            <input name="duedate" id="duedate"
                                   value="<?php echo date('d.m.y',$invoicein->getDuedate());?>" class="form-control">
                        </div>
                    </div>
                    <?php if ($invoicein->getPayeddate()>0){?>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Bezahlt</label>
                            <div class="col-sm-9">
                                <input name="payeddate" id="payeddate"
                                       value="<?php echo date('d.m.y',$invoicein->getPayeddate());?>" class="form-control">
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="form-group">
                            <label for="" class="col-sm-3 control-label">Bezahlt</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" name="payeddate" id="payeddate"
                                       value="<?php if ($invoicein->getPayeddate() > 0) echo date('d.m.y',$invoicein->getPayeddate());?>">
                            </div>
                        </div>
                    <?php } ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>
<script>
    $(function () {
        $( "#search_supplier" ).autocomplete({
            source: "libs/modules/tickets/ticket.ajax.php?ajax_action=search_customer",
            minLength: 2,
            focus: function( event, ui ) {
                $( "#search_supplier" ).val( ui.item.label );
                return false;
            },
            select: function( event, ui ) {
                $( "#search_supplier" ).val( ui.item.label );
                $( "#supplier" ).val( ui.item.value );
                return false;
            }
        });
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
        $('#duedate').datetimepicker({
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
        $('#redate').datetimepicker({
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