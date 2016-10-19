<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
$foldtypes = Foldtype::getAllFoldTypes();

//if ($_REQUEST){
//    prettyPrint($_REQUEST);
//}
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Konfigurator</h3>
    </div>
    <div class="panel-body">
        <div id="rootwizard">
            <nav class="navbar navbar-default" style="margin-bottom: 0px;">
                <div class="container-fluid">
                    <ul class="nav nav-pills">
                        <li><a href="#tab1" data-toggle="tab" style="line-height: 1.82857;">Produkt</a></li>
                        <li id="tab_part1" style="display: none"><a href="#tab2" data-toggle="tab" style="line-height: 1.82857;">Inhalt 1</a></li>
                        <li id="tab_part2" style="display: none"><a href="#tab3" data-toggle="tab" style="line-height: 1.82857;">Inhalt 2</a></li>
                        <li id="tab_part3" style="display: none"><a href="#tab4" data-toggle="tab" style="line-height: 1.82857;">Inhalt 3</a></li>
                        <li id="tab_part4" style="display: none"><a href="#tab5" data-toggle="tab" style="line-height: 1.82857;">Inhalt 4</a></li>
                        <li id="tab_part5" style="display: none"><a href="#tab6" data-toggle="tab" style="line-height: 1.82857;">Umschlag</a></li>
                        <li><a href="#tab7" data-toggle="tab" style="line-height: 1.82857;">Optionen</a></li>
                    </ul>
                    <div id="bar" class="progress" style="margin-bottom: 0px;">
                        <div class="progress-bar progress-bar-success progress-bar-striped"></div>
                    </div>
                </div>
            </nav>
            <div class="tab-content">
                <ul class="pager wizard">
                    <li class="previous"><a href="#" style="float: none;">Zurück</a></li>
                    <li class="next"><a href="#" style="float: none;">Weiter</a></li>
                    <li class="next finish" style="display:none;" onclick="doSubmit();"><a href="javascript:;" style="float: none;">Erstellen</a></li>
                </ul>
                <div class="tab-pane" id="tab1">
                    <form class="form-horizontal" name="tab1_form" id="tab1_form">
                        <?php include 'wizard/tab1.php'; ?>
                        <input type="hidden" name="product" id="product">
                    </form>
                </div>
                <div class="tab-pane" id="tab2">
                    <form class="form-horizontal" name="tab2_form" id="tab2_form">
                        <?php
                        $content_part = 1;
                        include 'wizard/parts.php';
                        ?>
                    </form>
                </div>
                <div class="tab-pane" id="tab3">
                    <form class="form-horizontal" name="tab3_form" id="tab3_form">
                        <?php
                        $content_part = 2;
                        include 'wizard/parts.php';
                        ?>
                    </form>
                </div>
                <div class="tab-pane" id="tab4">
                    <form class="form-horizontal" name="tab4_form" id="tab4_form">
                        <?php
                        $content_part = 3;
                        include 'wizard/parts.php';
                        ?>
                    </form>
                </div>
                <div class="tab-pane" id="tab5">
                    <form class="form-horizontal" name="tab5_form" id="tab5_form">
                        <?php
                        $content_part = 4;
                        include 'wizard/parts.php';
                        ?>
                    </form>
                </div>
                <div class="tab-pane" id="tab6">
                    <form class="form-horizontal" name="tab6_form" id="tab6_form">
                        <?php
                        $content_part = 5;
                        include 'wizard/parts.php';
                        ?>
                    </form>
                </div>
                <div class="tab-pane" id="tab7">
                    <form class="form-horizontal" name="tab7_form" id="tab7_form">
                        <div class="form-group">
                            <label for="order_title" class="col-sm-2 control-label">Titel</label>
                            <div class="col-sm-10">
                                <input name="order_title" id="order_title" value="" class="form-control">
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <label for="" class="col-sm-2 control-label">Rapport</label>
                            <div class="col-sm-10">
                                <div class="row">
                                    <div class="col-sm-4"></div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="" id="" placeholder="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="" id="" placeholder="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4"></div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="" id="" placeholder="">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-4"></div>
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="" id="" placeholder="">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4"></div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Auflage <span class="glyphicons glyphicons-plus pointer" onclick="addAmount()"></span></label>
                                    <div id="div_order_amount" class="col-sm-8">
                                        <input name="addorder_amount[]" class="form-control" value="" style="margin-bottom: 4px;">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="" class="col-sm-4 control-label">Sorten</label>
                                    <div id="div_order_sorts" class="col-sm-8">
                                        <input name="addorder_sorts[]" class="form-control" value="" style="margin-bottom: 4px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <ul class="pager wizard">
                    <li class="previous"><a href="#" style="float: none;">Zurück</a></li>
                    <li class="next"><a href="#" style="float: none;">Weiter</a></li>
                    <li class="next finish" style="display:none;" onclick="doSubmit();"><a href="javascript:;" style="float: none;">Erstellen</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    // Product Selection
    function selectProduct(ele,id){
        $('#table_products_nonindi tbody tr').each(function() { $( this ).removeClass('highlighttr'); });
        $('#table_individualProducts tbody tr').each(function() { $( this ).removeClass('highlighttr'); });
        $(ele).addClass('highlighttr');

        // update avail parts
        $.ajax({
            type: "GET",
            url: "libs/modules/calculation/order.create.ajax.php",
            data: { exec: "getAvailableParts", product: id },
            success: function(data)
            {
                var parts = $.parseJSON(data);
                for(let i=1;i<6;i++){
                    if (parts[i-1] == 1){
                        $('#tab_part'+i).show();
                        $('#part_'+i+'_formats').load('libs/modules/calculation/order.create.ajax.php?exec=getAvailablePaperFormats&product='+id+'&part='+i);
                    } else {
                        $('#tab_part'+i).hide();
                    }
                }
            }
        });
    }

    // Format Selection
    function select_format(ele){
        // Format Inputs füllen
        var datapart = $(ele).data('part');
        var dataformat = $(ele).data('format');
        var datawidth = $(ele).data('width');
        var dataheight = $(ele).data('height');
        $('#part_'+datapart+'_product_width').val(datawidth);
        $('#part_'+datapart+'_product_width_open').val(datawidth);
        $('#part_'+datapart+'_product_height').val(dataheight);
        $('#part_'+datapart+'_product_height_open').val(dataheight);
        $('#part_'+datapart+'_product_format').val(dataformat);

        // Wert in Formular übernehmen
        $('#part_'+datapart+'_product_format').val(dataformat);

        // Class auf Buttons setzen
        $('#part_'+datapart+'_formats input').each(function() { $( this ).removeClass('btn-success'); $( this ).addClass('btn-info'); });
        $(ele).removeClass('btn-info');
        $(ele).addClass('btn-success');

        // Papierformate ziehen
        var product = $('#product').val();
        $('#part_'+datapart+'_papers').load('libs/modules/calculation/order.create.ajax.php?exec=getSelectedPapersIds&product='+product+'&part='+datapart);

        $('html, body').animate({ scrollTop: $('#part_'+datapart+'_papers').offset().top }, 500);
    }

    // Paper Selection
    function select_paper(ele){
        // Format Inputs füllen
        var datapart = $(ele).data('part');
        var datapaper = $(ele).data('paper');

        // Wert in Formular übernehmen
        $('#part_'+datapart+'_product_paper').val(datapaper);

        // Class auf Buttons setzen
        $('#part_'+datapart+'_papers input').each(function() { $( this ).removeClass('btn-success'); $( this ).addClass('btn-info'); });
        $(ele).removeClass('btn-info');
        $(ele).addClass('btn-success');

        // Gewichte ziehen
        var product = $('#product').val();
        $('#part_'+datapart+'_weights').load('libs/modules/calculation/order.create.ajax.php?exec=getAvailablePaperWeights&product='+product+'&part='+datapart+'&paper='+datapaper);

        $('html, body').animate({ scrollTop: $('#part_'+datapart+'_weights').offset().top }, 500);
    }

    // Weight Selection
    function select_weight(ele){
        // Format Inputs füllen
        var datapart = $(ele).data('part');
        var dataweight = $(ele).data('weight');

        // Wert in Formular übernehmen
        $('#part_'+datapart+'_product_paperweight').val(dataweight);

        // Class auf Buttons setzen
        $('#part_'+datapart+'_weights input').each(function() { $( this ).removeClass('btn-success'); $( this ).addClass('btn-info'); });
        $(ele).removeClass('btn-info');
        $(ele).addClass('btn-success');

        // Seiten/Umfang ziehen
        var product = $('#product').val();
        $('#part_'+datapart+'_pages').load('libs/modules/calculation/order.create.ajax.php?exec=getAvailablePages&product='+product+'&part='+datapart);

        $('html, body').animate({ scrollTop: $('#part_'+datapart+'_pages').offset().top }, 500);
    }

    // Pages Selection
    function select_pages(ele){
        // Format Inputs füllen
        var datapart = $(ele).data('part');
        var datapages = $(ele).data('pages');

        // Wert in Formular übernehmen
        $('#part_'+datapart+'_product_pages').val(datapages);

        // Class auf Buttons setzen
        $('#part_'+datapart+'_pages input').each(function() { $( this ).removeClass('btn-success'); $( this ).addClass('btn-info'); });
        $(ele).removeClass('btn-info');
        $(ele).addClass('btn-success');

        // Select für Falzarten anzeigen und aktualisieren
        $('#folddiv_'+datapart).show();
        $('#part_'+datapart+'_product_foldtype').children().each(function() {
            // Falzart nur auswählbar wenn anz. Seiten dazu passt
            //  prettyPrint((($hori + 1) * ($verti + 1)));
            let datawidth = parseInt($(this).data('width'));
            let dataheight = parseInt($(this).data('height'));
            if (datawidth <= 0)
                datawidth = 1;
            if (dataheight <= 0)
                dataheight = 1;
            if (((datawidth+1)*(dataheight+1))<=datapages){
                $( this ).show();
            } else {
                $( this ).hide();
            }
        });

        // Farbigkeit ziehen
        var product = $('#product').val();
        $('#part_'+datapart+'_chromas').load('libs/modules/calculation/order.create.ajax.php?exec=getAvailablePaperChromas&product='+product+'&part='+datapart+'&pages='+datapages);

        $('html, body').animate({ scrollTop: $('#part_'+datapart+'_chromas').offset().top }, 500);
    }

    // Chroma Selection
    function select_chroma(ele){
        // Format Inputs füllen
        var datapart = $(ele).data('part');
        var datachroma = $(ele).data('chroma');

        // Wert in Formular übernehmen
        $('#part_'+datapart+'_product_chromaticity').val(datachroma);

        // Class auf Buttons setzen
        $('#part_'+datapart+'_chromas input').each(function() { $( this ).removeClass('btn-success'); $( this ).addClass('btn-info'); });
        $(ele).removeClass('btn-info');
        $(ele).addClass('btn-success');
    }



    // Foldtype Selection
    function select_foldtype(ele, datapart){
        var elewidth = $('#part_'+datapart+'_product_width');
        var elewidth_open = $('#part_'+datapart+'_product_width_open');
        var eleheight = $('#part_'+datapart+'_product_height');
        var eleheight_open = $('#part_'+datapart+'_product_height_open');
        var datawidthfactor = $(ele).children('option:selected').data('width');
        var dataheightfactor = $(ele).children('option:selected').data('height');
        if (datawidthfactor <= 0)
            datawidthfactor = 1;
        if (dataheightfactor <= 0)
            dataheightfactor = 1;
        elewidth_open.val(parseInt(elewidth.val()) * parseInt(datawidthfactor));
        eleheight_open.val(parseInt(eleheight.val()) * parseInt(dataheightfactor));
    }

    // Format Swapping
    function SwapFormat(datapart){
        var order_product_width = $('#part_'+datapart+'_product_width').val();
        var order_product_width_open = $('#part_'+datapart+'_product_width_open').val();
        var order_product_height = $('#part_'+datapart+'_product_height').val();
        var order_product_height_open = $('#part_'+datapart+'_product_height_open').val();
        $('#part_'+datapart+'_product_width').val(order_product_height);
        $('#part_'+datapart+'_product_width_open').val(order_product_height_open);
        $('#part_'+datapart+'_product_height').val(order_product_width);
        $('#part_'+datapart+'_product_height_open').val(order_product_width_open);
    }


    // Add Amount
    function addAmount()
    {
        var amount = '<input name="addorder_amount[]" class="form-control" value="" style="margin-bottom: 4px;">';
        $('#div_order_amount').append(amount);
        var sort = '<input name="addorder_sorts[]" class="form-control" value="1" style="margin-bottom: 4px;">';
        $('#div_order_sorts').append(sort);
    }

    // Toggle Part
    function togglePart(ele, datapart){
        $('#part_'+datapart+'_content').toggle();
        $(ele).toggleClass('btn-info');
        $(ele).toggleClass('btn-success');
        $('#tab'+(datapart+1)+'_form').find(':input').each(function(){
            if (!$(this).hasClass('btn')){
                if ($(this).attr('disabled')) {
                    $(this).removeAttr('disabled');
                } else {
                    $(this).attr('disabled', true);
                }
            }
        });
    }

    // Form Submit
    function doSubmit(){
        var data = $('#tab1_form, #tab2_form, #tab3_form, #tab4_form, #tab5_form, #tab6_form, #tab7_form').serialize();
        window.location.href = 'index.php?page=<?= $_REQUEST['page'] ?>&'+data;
    }
</script>
<script>
    $(document).ready(function() {

        // Validation
        var t1_validator = $("#tab1_form").validate({
            errorPlacement: function(error,element) {
                return true;
            },
            ignore: [],
            rules: {
                product: {
                    required: true
                }
            }
        });
        for (let i=1;i<6;i++){
            let rules = {};
            rules['part_'+i+'_product_format'] = { required: true };
            rules['part_'+i+'_product_paper'] = { required: true };
            rules['part_'+i+'_product_paperweight'] = { required: true };
            rules['part_'+i+'_product_pages'] = { required: true };
            rules['part_'+i+'_product_chromaticity'] = { required: true };
            rules['part_'+i+'_product_foldtype'] = { required: true };
            rules['part_'+i+'_product_width'] = { required: true };
            rules['part_'+i+'_product_width_open'] = { required: true };
            rules['part_'+i+'_product_height'] = { required: true };
            rules['part_'+i+'_product_height_open'] = { required: true };

            $("#tab"+(i+1)+"_form").validate({
                errorPlacement: function(error,element) {
                    return true;
                },
                ignore: ":disabled",
                rules: rules
            });
        }


        $('#rootwizard').bootstrapWizard({
            'tabClass': 'nav nav-pills',
            'onTabShow': function(tab, navigation, index)
            {
                var $total = navigation.find('li').length;
                var $current = index+1;
                var $percent = ($current/$total) * 100;
                $('#rootwizard').find('.progress-bar').css({width:$percent+'%'});

                // If it's the last tab then hide the last button and show the finish instead
                if($current >= $total) {
                    $('#rootwizard').find('.pager .next').hide();
                    $('#rootwizard').find('.pager .finish').css('display','inline');
                    $('#rootwizard').find('.pager .finish').removeClass('disabled');
                } else {
                    $('#rootwizard').find('.pager .next').show();
                    $('#rootwizard').find('.pager .finish').hide();
                }
            },
            'onNext': function(tab, navigation, index) {
                return validateStep(index);
            },
            'onTabClick': function(tab, navigation, index) {
                return false;
            }
        });
    });

    function validateStep(index){
        var $valid = $("#tab"+index+"_form").valid();
        if(!$valid) {
            return false;
        }
        $('html, body').animate({ scrollTop: 0 }, 500);
    }
</script>

<script src="jscripts/jvalidation/dist/jquery.validate.min.js"></script>
<script src="jscripts/jvalidation/dist/localization/messages_de.min.js"></script>