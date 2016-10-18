<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
$foldtypes = Foldtype::getAllFoldTypes();
?>

<form action="index.php?page=<?= $_REQUEST['page'] ?>" class="form-horizontal" method="post" name="neworder_form" id="neworder_form">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Konfigurator</h3>
        </div>
        <div class="panel-body">
            <div id="rootwizard">
                <nav class="navbar navbar-default">
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
                    </div>
                </nav>
                <div id="bar" class="progress">
                    <div class="progress-bar progress-bar-success progress-bar-striped"></div>
                </div>
                <div class="tab-content">
                    <ul class="pager wizard">
                        <li class="previous"><a href="#" style="float: none;">Zurück</a></li>
                        <li class="next"><a href="#" style="float: none;">Weiter</a></li>
                    </ul>
                    <div class="tab-pane" id="tab1">
                        <?php include 'wizard/tab1.php'; ?>
                        <input type="hidden" name="product" id="product">
                    </div>
                    <div class="tab-pane" id="tab2">
                        <?php
                        $content_part = 1;
                        include 'wizard/parts.php';
                        ?>
                    </div>
                    <div class="tab-pane" id="tab3">
                        <?php
                        $content_part = 2;
                        include 'wizard/parts.php';
                        ?>
                    </div>
                    <div class="tab-pane" id="tab4">
                        <?php
                        $content_part = 3;
                        include 'wizard/parts.php';
                        ?>
                    </div>
                    <div class="tab-pane" id="tab5">
                        <?php
                        $content_part = 4;
                        include 'wizard/parts.php';
                        ?>
                    </div>
                    <div class="tab-pane" id="tab6">
                        <?php
                        $content_part = 5;
                        include 'wizard/parts.php';
                        ?>
                    </div>
                    <div class="tab-pane" id="tab7">
                        <div class="form-group">
                            <label for="order_title" class="col-sm-2 control-label">Titel</label>
                            <div class="col-sm-10">
                                <input name="order_title" id="order_title" value="" class="form-control">
                            </div>
                        </div>
                    </div>
                    <ul class="pager wizard">
                        <li class="previous"><a href="#" style="float: none;">Zurück</a></li>
                        <li class="next"><a href="#" style="float: none;">Weiter</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>


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
                    console.log(parts[i]);
                    if (parts[i] == 1){
                        $('#tab_part'+i).show();
                        $('#part_'+i+'_formats').load('libs/modules/calculation/order.create.ajax.php?exec=getAvailablePaperFormats&product='+id+'&part='+i);
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
        $('part_'+datapart+'_product_format').val(dataformat);

        // Class auf Buttons setzen
        $('#part_'+datapart+'_formats input').each(function() { $( this ).removeClass('btn-success'); $( this ).addClass('btn-info'); });
        $(ele).removeClass('btn-info');
        $(ele).addClass('btn-success');

        // Papierformate ziehen
        var product = $('#product').val();
        $('#part_'+datapart+'_papers').load('libs/modules/calculation/order.create.ajax.php?exec=getSelectedPapersIds&product='+product+'&part='+datapart);
    }

    // Paper Selection
    function select_paper(ele){
        // Format Inputs füllen
        var datapart = $(ele).data('part');
        var datapaper = $(ele).data('paper');

        // Wert in Formular übernehmen
        $('part_'+datapart+'_product_paper').val(datapaper);

        // Class auf Buttons setzen
        $('#part_'+datapart+'_papers input').each(function() { $( this ).removeClass('btn-success'); $( this ).addClass('btn-info'); });
        $(ele).removeClass('btn-info');
        $(ele).addClass('btn-success');

        // Gewichte ziehen
        var product = $('#product').val();
        $('#part_'+datapart+'_weights').load('libs/modules/calculation/order.create.ajax.php?exec=getAvailablePaperWeights&product='+product+'&part='+datapart+'&paper='+datapaper);
    }

    // Weight Selection
    function select_weight(ele){
        // Format Inputs füllen
        var datapart = $(ele).data('part');
        var dataweight = $(ele).data('weight');

        // Wert in Formular übernehmen
        $('part_'+datapart+'_product_paperweight').val(dataweight);

        // Class auf Buttons setzen
        $('#part_'+datapart+'_weights input').each(function() { $( this ).removeClass('btn-success'); $( this ).addClass('btn-info'); });
        $(ele).removeClass('btn-info');
        $(ele).addClass('btn-success');

        // Seiten/Umfang ziehen
        var product = $('#product').val();
        $('#part_'+datapart+'_pages').load('libs/modules/calculation/order.create.ajax.php?exec=getAvailablePages&product='+product+'&part='+datapart);
    }

    // Pages Selection
    function select_pages(ele){
        // Format Inputs füllen
        var datapart = $(ele).data('part');
        var datapages = $(ele).data('pages');

        // Wert in Formular übernehmen
        $('part_'+datapart+'_product_pages').val(datapages);

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
    }

    // Chroma Selection
    function select_chroma(ele){
        // Format Inputs füllen
        var datapart = $(ele).data('part');
        var datachroma = $(ele).data('chromapages');

        // Wert in Formular übernehmen
        $('part_'+datapart+'_product_chromaticity').val(datachroma);

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
</script>
<script>
    $(document).ready(function() {
        var $validator = $("#neworder_form").validate({
//            ignore: ":hidden:not(#product)",
            rules: {
                order_title: {
                    required: true,
                    minlength: 3
                },
                product: {
                    required: true,
                },
                part_1_product_format: {
                    required: true,
                },
                part_1_product_paper: {
                    required: true,
                },
                part_1_product_paperweight: {
                    required: true,
                },
                part_1_product_pages: {
                    required: true,
                },
                part_1_product_chromaticity: {
                    required: true,
                },
                part_2_product_format: {
                    required: true,
                },
                part_2_product_paper: {
                    required: true,
                },
                part_2_product_paperweight: {
                    required: true,
                },
                part_2_product_pages: {
                    required: true,
                },
                part_2_product_chromaticity: {
                    required: true,
                },
                part_3_product_format: {
                    required: true,
                },
                part_3_product_paper: {
                    required: true,
                },
                part_3_product_paperweight: {
                    required: true,
                },
                part_3_product_pages: {
                    required: true,
                },
                part_3_product_chromaticity: {
                    required: true,
                },
                part_4_product_format: {
                    required: true,
                },
                part_4_product_paper: {
                    required: true,
                },
                part_4_product_paperweight: {
                    required: true,
                },
                part_4_product_pages: {
                    required: true,
                },
                part_4_product_chromaticity: {
                    required: true,
                },
                part_5_product_format: {
                    required: true,
                },
                part_5_product_paper: {
                    required: true,
                },
                part_5_product_paperweight: {
                    required: true,
                },
                part_5_product_pages: {
                    required: true,
                },
                part_5_product_chromaticity: {
                    required: true,
                },
            }
        });

        $('#rootwizard').bootstrapWizard({
            'tabClass': 'nav nav-pills',
            'onTabShow': function(tab, navigation, index)
            {
                var $total = navigation.find('li').length;
                var $current = index+1;
                var $percent = ($current/$total) * 100;
                $('#rootwizard').find('.progress-bar').css({width:$percent+'%'});
            },
            'onNext': function(tab, navigation, index) {
                var $valid = $("#neworder_form").valid();
                if(!$valid) {
                    $validator.focusInvalid();
                    return false;
                }
            }
        });
    });
</script>

<script src="jscripts/jvalidation/dist/jquery.validate.min.js"></script>
<script src="jscripts/jvalidation/dist/localization/messages_de.min.js"></script>