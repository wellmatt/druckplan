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
    if ($_REQUEST['payeddate'] > 0){
        $status = 2;
    }
    $array = [
        'number' => $_REQUEST["number"],
        'supplier' => $_REQUEST["supplier"],
        'status' => $status,
        'description' => $_REQUEST["description"],
        'netvalue' => $_REQUEST["netvalue"],
        'taxkey' => $_REQUEST["taxkey"],
        'redate' => strtotime($_REQUEST["redate"]),
        'payeddate' => strtotime($_REQUEST["payeddate"]),
        'duedate' => strtotime($_REQUEST["duedate"]),
        'grossvalue' =>$_REQUEST["netvalue"] * (1 +  $_REQUEST["tax"]/100),
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

if ($invoicein->getId()>0){
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/accounting/invoicein.overview.php&exec=delete&id=".$invoicein->getId()."');", 'glyphicon-trash', true);
}
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
                     <div class="row">
                         <div class="col-md-12">

                             <div class="form-group">
                                 <label for="" class="col-sm-2 control-label">Grund der Ausgabe</label>
                                 <div class="col-sm-10">
                                     <input name="description" id="description" value="<?php echo $invoicein->getDescription();?>" class="form-control">
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-2 control-label">Lieferant</label>
                                 <div class="col-sm-10">
                                     <input type="text" id="search_supplier" name="search_supplier"
                                            value="<?php if ($invoicein->getSupplier()->getId() > 0) {
                                                echo $invoicein->getSupplier()->getNameAsLine();
                                            } ?>" class="form-control"/>
                                     <input type="hidden" id="supplier" name="supplier"
                                            value="<?php echo $invoicein->getSupplier()->getId(); ?>"/>
                                 </div>
                             </div>
                         </div>
                     </div>
                    <br>
                     <div class="row">
                         <div class="col-md-6">
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Re.-Nr.</label>
                                 <div class="col-sm-8">
                                     <input name="number" id="number" value="<?php echo $invoicein->getNumber();?>" class="form-control">
                                 </div>
                             </div>
                             <?php if ($invoicein->getRedate()>0){?>
                                 <div class="form-group">
                                     <label for="" class="col-sm-4 control-label">Re.-Datum</label>
                                     <div class="col-sm-8">
                                         <input name="redate" id="redate"
                                                value="<?php echo date('d.m.y',$invoicein->getRedate());?>" class="form-control">
                                     </div>
                                 </div>
                             <?php } else { ?>
                                 <div class="form-group">
                                     <label for="" class="col-sm-4 control-label">Re.-Datum</label>
                                     <div class="col-sm-8">
                                         <input type="text" class="form-control" name="redate" id="redate"
                                                value="<?php if ($invoicein->getRedate() > 0) echo date('d.m.y',$invoicein->getRedate());?>">
                                     </div>
                                 </div>
                             <?php } ?>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Fällig</label>
                                 <div class="col-sm-8">
                                     <input name="duedate" id="duedate"
                                            value="<?php echo date('d.m.y',$invoicein->getDuedate());?>" class="form-control">
                                 </div>
                             </div>
                         </div>
                         <div class="col-md-6">
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Netto</label>
                                 <div class="col-sm-8">
                                     <div class="input-group">
                                         <input name="netvalue" id="netvalue" value="<?php echo $invoicein->getNetvalue();?>" class="form-control">
                                         <span class="input-group-addon">€</span>
                                     </div>
                                 </div>
                             </div>
                             <div class="form-group">
                                 <label for="" class="col-sm-4 control-label">Umsatzsteuer</label>
                                 <div class="col-sm-8">
                                     <select id="taxkey" name="taxkey" class="form-control">
                                         <?php if ($invoicein->getTaxkey()->getId() > 0) echo '<option value="'.$invoicein->getTaxkey()->getId().'">'.$invoicein->getTaxkey()->getValue().'%</option>';?>
                                     </select>
                                 </div>
                             </div>
                             <?php if ($invoicein->getPayeddate()>0){?>
                                 <div class="form-group">
                                     <label for="" class="col-sm-4 control-label">Bezahlt</label>
                                     <div class="col-sm-8">
                                         <input name="payeddate" id="payeddate"
                                                value="<?php echo date('d.m.y',$invoicein->getPayeddate());?>" class="form-control">
                                     </div>
                                 </div>
                             <?php } else { ?>
                                 <div class="form-group">
                                     <label for="" class="col-sm-4 control-label">Bezahlt</label>
                                     <div class="col-sm-8">
                                         <input type="text" class="form-control" name="payeddate" id="payeddate"
                                                value="<?php if ($invoicein->getPayeddate() > 0) echo date('d.m.y',$invoicein->getPayeddate());?>">
                                     </div>
                                 </div>
                             <?php } ?>
                         </div>
                     </div>
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
<script>
    $(function () {
        $("#taxkey").select2({
            ajax: {
                url: "libs/basic/ajax/select2.ajax.php?ajax_action=search_taxkey",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            minimumInputLength: 0,
            language: "de",
            multiple: false,
            allowClear: false,
            tags: false
        }).val(<?php echo $invoicein->getTaxkey()->getId();?>).trigger('change');
    });
</script>
