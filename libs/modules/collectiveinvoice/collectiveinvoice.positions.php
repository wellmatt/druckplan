<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */

function printSubTradegroupsForSelect($parentId, $depth){
    $all_subgroups = Tradegroup::getAllTradegroups($parentId);
    foreach ($all_subgroups AS $subgroup)
    {
        global $x;
        $x++; ?>
        <option value="<?=$subgroup->getId()?>">
            <?for ($i=0; $i<$depth+1;$i++) echo "&emsp;"?>
            <?= $subgroup->getTitle()?>
        </option>
        <? printSubTradegroupsForSelect($subgroup->getId(), $depth+1);
    }
}
$partslists = Partslist::getAll();

?>

<!-- DataTables Editor -->
<link rel="stylesheet" type="text/css" href="jscripts/datatableeditor/datatables.min.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/datatables.min.js"></script>

<script type="text/javascript" src="jscripts/datatableeditor/FieldType-autoComplete/editor.autoComplete.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/datatableeditor/FieldType-bootstrapDate/editor.bootstrapDate.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/FieldType-bootstrapDate/editor.bootstrapDate.js"></script>
<script type="text/javascript" src="jscripts/datatableeditor/FieldType-datetimepicker-2/editor.datetimepicker-2.js"></script>

<script type="text/javascript" src="jscripts/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="jscripts/ckeditor/config.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/ckeditor/skins/bootstrapck/editor.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/FieldType-ckeditor/editor.ckeditor.js"></script>
<!-- /DataTables Editor -->

<script>
    function addArticle(article){
        console.log("received: "+article);
        $.ajax({
            type: 'GET',
            url: 'libs/modules/collectiveinvoice/orderposition.ajax.php',
            data: { exec: 'addPosArticle', ciid: '<?php echo $collectinv->getId();?>', aid: article },
            success: function() {
                table.ajax.reload( null, false );
                console.log("successfully added");
            },
            error: function() {
                console.log("unsuccessful");
            }
        });
    }

    function addPartslist(partslist){
        $.ajax({
            type: 'GET',
            url: 'libs/modules/collectiveinvoice/orderposition.ajax.php',
            data: { exec: 'addPosPartslist', ciid: '<?php echo $collectinv->getId();?>', plid: partslist },
            success: function() {
                table.ajax.reload( null, false );
                console.log("successfully added");
            },
            error: function() {
                console.log("unsuccessful");
            }
        });
    }

    function addManually(){
        $.ajax({
            type: 'GET',
            url: 'libs/modules/collectiveinvoice/orderposition.ajax.php',
            data: { exec: 'addPosManually', ciid: '<?php echo $collectinv->getId();?>' },
            success: function() {
                table.ajax.reload( null, false );
                console.log("successfully added");
            },
            error: function() {
                console.log("unsuccessful");
            }
        });
    }
</script>

<div class="panel panel-default" id="search_art" style="display: none">
    <div class="panel-heading">
        <h3 class="panel-title">
            Hinzufügen: Artikel
            <span class="pull-right">
                <!-- Suche -->
                <label for="" class="control-label">Suche</label>
                <input id="article_search" class="form-control" style="display: inline-block; width: 200px">
            </span>
        </h3>
    </div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Filter
                </h3>
            </div>
            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Tags</label>
                        <div class="col-sm-4">
                            <input type="hidden" id="ajax_tags" name="ajax_tags"/>
                            <input name="art_tags" id="art_tags" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Warengruppe</label>
                        <div class="col-sm-4">
                            <input type="hidden" id="ajax_tradegroup" name="ajax_tradegroup" value="0"/>
                            <select name="tradegroup" id="tradegroup" class="form-control"
                                    onchange="$('#ajax_tradegroup').val($('#tradegroup').val());$('#art_table').dataTable().fnDraw();"
                                    onfocus="markfield(this,0)" onblur="markfield(this,1)">
                                <option value="0">- Alle -</option>
                                <?php
                                $all_tradegroups = Tradegroup::getAllTradegroups();
                                foreach ($all_tradegroups as $tg) {
                                    ?>
                                    <option value="<?= $tg->getId() ?>">
                                        <?= $tg->getTitle() ?></option>
                                    <? printSubTradegroupsForSelect($tg->getId(), 0);
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Kunde/Ansprechpartner</label>
                        <div class="col-sm-4">
                            <input type="hidden" id="ajax_bc" name="ajax_bc" value="0"/>
                            <input type="hidden" id="ajax_cp" name="ajax_cp" value="0"/>
                            <input name="bc_cp" id="bc_cp" class="form-control"
                                   onchange="Javascript: if($('#bc_cp').val()==''){$('#ajax_bc').val(0);$('#ajax_cp').val(0);$('#art_table').dataTable().fnDraw();}"
                                   onfocus="markfield(this,0)" onblur="markfield(this,1)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Suche</label>
                        <div class="col-sm-4">
                            <input type="text" id="inpt_search_art" class="form-control" placeholder="">
                        </div>
                    </div>

                    <?php
                    $article_fields = CustomField::fetch([
                        [
                            'column'=>'class',
                            'value'=>'Article'
                        ]
                    ]);
                    if (count($article_fields) > 0){?>
                        <br>
                        <div class="panel panel-default collapseable">
                            <div class="panel-heading">
                                <h3 class="panel-title">
                                    Freifeld Filter
                                    <span class="pull-right clickable panel-collapsed"><i class="glyphicon glyphicon glyphicon-chevron-down"></i></span>
                                </h3>
                            </div>
                            <div class="panel-body" style="display: none;">
                                <div id="cfield_div"></div>
                                <div class="form-group">
                                    <label for="" class="col-sm-2 control-label">Neuer Filter</label>
                                    <div class="col-sm-4">
                                        <select id="addfilterselect" class="form-control">
                                            <?php
                                            foreach ($article_fields as $article_field) {
                                                echo '<option value="' . $article_field->getId() . '">' . $article_field->getName() . '</option>';
                                            }
                                            ?>
                                        </select>
                                        <select id="addfilter_backup" style="display: none;">
                                            <?php
                                            foreach ($article_fields as $article_field) {
                                                echo '<option value="' . $article_field->getId() . '">' . $article_field->getName() . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-sm-3">
                                        <button class="btn btn-sm btn-success" onclick="addFilter();">
                                            Hinzufügen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="col-sm-12">
                         <span class="pull-right">
                             <button class="btn btn-xs btn-warning"
                                     onclick="resetFilter();">
                                 <span class="glyphicons glyphicons-ban-circle pointer"></span>
                                 <?= $_LANG->get('Reset') ?>
                             </button>
                         </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover" id="art_table">
            <thead>
                <tr>
                    <th><?= $_LANG->get('ID') ?></th>
                    <th><?= $_LANG->get('Bild') ?></th>
                    <th><?= $_LANG->get('Titel') ?></th>
                    <th><?= $_LANG->get('Art.-Nr.') ?></th>
                    <th><?= $_LANG->get('Tags') ?></th>
                    <th><?= $_LANG->get('Warengruppe') ?></th>
                    <th><?= $_LANG->get('Shop-Freigabe') ?></th>
                    <th><?= $_LANG->get('Optionen') ?></th>
                </tr>
            </thead>
        </table>
    </div>
</div>


<div class="panel panel-default" id="search_plist" style="display: none">
    <div class="panel-heading">
        <h3 class="panel-title">Hinzufügen: Stücklisten</h3>
    </div>
    <div class="panel-body">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Filter
                </h3>
            </div>
            <div class="panel-body">
                <div class="form-horizontal">
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Suche</label>
                        <div class="col-sm-4">
                            <input type="text" id="inpt_search_plist" class="form-control" placeholder="">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover" id="partslisttable">
            <thead>
            <tr>
                <th><?=$_LANG->get('ID')?></th>
                <th><?=$_LANG->get('Name')?></th>
                <th><?=$_LANG->get('Datum')?></th>
                <th><?=$_LANG->get('User')?></th>
            </tr>
            </thead>
            <tbody>
            <? foreach($partslists as $partslist){?>
                <tr>
                    <td><?=$partslist->getId()?></td>
                    <td><?=$partslist->getTitle()?></td>
                    <td><?=date('d.m.y',$partslist->getCrtdate())?></td>
                    <td><?=$partslist->getCrtuser()->getNameAsLine()?></td>
                </tr>
            <? } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Positionen
            <?php if ($collectinv->getLocked() == 0 && $collectinv->getId() > 0){?>
                <span class="pull-right" style="width: 75%; text-align: right; white-space: nowrap;">
                    <button class="btn btn-xs btn-success" id="add_btn" type="button" onclick="$('#search_art').toggle();$('#search_art').focus()">Artikel</button>
                    <button class="btn btn-xs btn-success" id="add_btn" type="button" onclick="$('#search_plist').toggle();$('#search_plist').focus()">Stückliste</button>
                    <?php if ($perf->getDeactivateManualArticles() == 0){?>
                        <button class="btn btn-xs btn-success" type="button" onclick="addManually();"><span class="glyphicons glyphicons-plus"></span>Manuell</button>
                    <?php } ?>
                </span>
            <?php } ?>
        </h3>
    </div>
    <div class="table-responsive" style="margin: -7px -1px -7px -1px;">
        <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
            <thead>
            <tr>
                <th>#</th>
                <th>ID</th>
                <th>Typ</th>
                <th>Status</th>
                <th>Artikel</th>
                <th>Menge</th>
                <th>GP (€)</th>
                <th>EP (€)</th>
                <th>Steuer</th>
                <th></th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>Summe:</th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
            </tfoot>
        </table>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var partslisttable = $('#partslisttable').DataTable( {
            "paging": true,
            "stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
            "pageLength": <?php echo $perf->getDt_show_default();?>,
            "dom": 'T<"clear">lrtip',
            "tableTools": {
                "sSwfPath": "jscripts/datatable/copy_csv_xls_pdf.swf",
                "aButtons": [
                    "copy",
                    "csv",
                    "xls",
                    {
                        "sExtends": "pdf",
                        "sPdfOrientation": "landscape",
                        "sPdfMessage": "Contilas - Articles"
                    },
                    "print"
                ]
            },
            "lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
            "language":
            {
                "emptyTable":     "Keine Daten vorhanden",
                "info":           "Zeige _START_ bis _END_ von _TOTAL_ Eintr&auml;gen",
                "infoEmpty": 	  "Keine Seiten vorhanden",
                "infoFiltered":   "(gefiltert von _MAX_ gesamten Eintr&auml;gen)",
                "infoPostFix":    "",
                "thousands":      ".",
                "lengthMenu":     "Zeige _MENU_ Eintr&auml;ge",
                "loadingRecords": "Lade...",
                "processing":     "Verarbeite...",
                "search":         "Suche:",
                "zeroRecords":    "Keine passenden Eintr&auml;ge gefunden",
                "paginate": {
                    "first":      "Erste",
                    "last":       "Letzte",
                    "next":       "N&auml;chste",
                    "previous":   "Vorherige"
                },
                "aria": {
                    "sortAscending":  ": aktivieren um aufsteigend zu sortieren",
                    "sortDescending": ": aktivieren um absteigend zu sortieren"
                }
            }
        } );
        $('#inpt_search_plist').keyup(function(){
            partslisttable.search( $(this).val() ).draw();
        });

        $("#partslisttable tbody td").live('click',function(){
            var aPos = $('#partslisttable').dataTable().fnGetPosition(this);
            var aData = $('#partslisttable').dataTable().fnGetData(aPos[0]);
            addPartslist(aData[0]);
            $('#search_plist').hide();
        });
    } );
</script>

<script type="text/javascript">
    $(document).ready(function() {
        var art_table = $('#art_table').DataTable( {
            // "scrollY": "600px",
            "autoWidth": false,
            "processing": true,
            "bServerSide": true,
            "sAjaxSource": "libs/modules/article/article.dt.ajax.php",
            "paging": true,
            "stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
            "pageLength": 10,
            "dom": 'T<"clear">lrtip',
            "tableTools": {
                "sSwfPath": "jscripts/datatable/copy_csv_xls_pdf.swf",
                "aButtons": [
                    "copy",
                    "csv",
                    "xls",
                    {
                        "sExtends": "pdf",
                        "sPdfOrientation": "landscape",
                        "sPdfMessage": "Contilas - Articles"
                    },
                    "print"
                ]
            },
            "lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
            "columns": [
                null,
                { "visible": false },
                null,
                null,
                { "sortable": false },
                null,
                { "visible": false },
                { "visible": false }
            ],
            "fnServerData": function ( sSource, aoData, fnCallback ) {
                var tags = document.getElementById('ajax_tags').value;
                var tg = document.getElementById('ajax_tradegroup').value;
                var bc = document.getElementById('ajax_bc').value;
                var cp = document.getElementById('ajax_cp').value;
                aoData.push( { "name": "search_tags", "value": tags, } );
                aoData.push( { "name": "tradegroup", "value": tg, } );
                aoData.push( { "name": "bc", "value": bc, } );
                aoData.push( { "name": "cp", "value": cp, } );

                // Custom Fields
                var cfields = $("[data-fieldid]");
                cfields.each(function(){
                    if ($(this).data("type") == "checkbox" && $(this).is( ":checked" )){
                        aoData.push( { "name": "cfield_"+$(this).data("fieldid"), "value": 1 } );
                    } else if ($(this).data("type") != "checkbox") {
                        aoData.push( { "name": "cfield_"+$(this).data("fieldid"), "value": $(this).val() } );
                    }
                });

                $.getJSON( sSource, aoData, function (json) {
                    fnCallback(json)
                } );
            },
            "language":
            {
                "emptyTable":     "Keine Daten vorhanden",
                "info":           "Zeige _START_ bis _END_ von _TOTAL_ Eintr&auml;gen",
                "infoEmpty": 	  "Keine Seiten vorhanden",
                "infoFiltered":   "(gefiltert von _MAX_ gesamten Eintr&auml;gen)",
                "infoPostFix":    "",
                "thousands":      ".",
                "lengthMenu":     "Zeige _MENU_ Eintr&auml;ge",
                "loadingRecords": "Lade...",
                "processing":     "Verarbeite...",
                "search":         "Suche:",
                "zeroRecords":    "Keine passenden Eintr&auml;ge gefunden",
                "paginate": {
                    "first":      "Erste",
                    "last":       "Letzte",
                    "next":       "N&auml;chste",
                    "previous":   "Vorherige"
                },
                "aria": {
                    "sortAscending":  ": aktivieren um aufsteigend zu sortieren",
                    "sortDescending": ": aktivieren um absteigend zu sortieren"
                }
            }
        } );

        $("#art_table tbody td").live('click',function(){
            var aPos = $('#art_table').dataTable().fnGetPosition(this);
            var aData = $('#art_table').dataTable().fnGetData(aPos[0]);
            addArticle(aData[0]);
            $('#search_art').hide();
        });

        $('#inpt_search_art').keyup(function(){
            art_table.search( $(this).val() ).draw();
        })
    } );
</script>

<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery("#art_tags").tagit({
            singleField: true,
            singleFieldNode: $('#art_tags'),
            singleFieldDelimiter: ";",
            allowSpaces: true,
            minLength: 2,
            removeConfirmation: true,
            tagSource: function( request, response ) {
                $.ajax({
                    url: "libs/modules/article/article.ajax.php?ajax_action=search_tags",
                    data: { term:request.term },
                    dataType: "json",
                    success: function( data ) {
                        response( $.map( data, function( item ) {
                            return {
                                label: item.label,
                                value: item.value
                            }
                        }));
                    }
                });
            },
            afterTagAdded: function(event, ui) {
                $('#ajax_tags').val($("#art_tags").tagit("assignedTags"));
                $('#art_table').dataTable().fnDraw();
            },
            afterTagRemoved: function(event, ui) {
                $('#ajax_tags').val($("#art_tags").tagit("assignedTags"));
                $('#art_table').dataTable().fnDraw();
            }
        });
    });
</script>

<script>
    $(function() {
        $( "#bc_cp" ).autocomplete({
            delay: 0,
            source: 'libs/modules/article/article.ajax.php?ajax_action=search_bc_cp',
            minLength: 2,
            dataType: "json",
            select: function(event, ui) {
                if (ui.item.type == 1)
                {
                    $('#ajax_bc').val(ui.item.value);
                    $('#bc_cp').val(ui.item.label);
                    $('#art_table').dataTable().fnDraw();
                }
                else
                {
                    $('#ajax_cp').val(ui.item.value);
                    $('#bc_cp').val(ui.item.label);
                    $('#art_table').dataTable().fnDraw();
                }
                return false;
            }
        });
    });
</script>


<script>
    function resetFilter(){
        $('#bc_cp').val('');
        $('#ajax_bc').val(0);
        $('#ajax_cp').val(0);
        $('#cfield_div').html("");

        var $options = $("#addfilter_backup > option").clone();
        $('#addfilterselect').html("");
        $('#addfilterselect').append($options);

        $('#art_table').dataTable().fnDraw();
    }
    function addFilter(){
        var filterid = $('#addfilterselect').val();
        $('#addfilterselect option:selected').remove();

        $.ajax({
            type: "GET",
            url: "libs/modules/customfields/custom.field.ajax.php",
            data: { ajax_action: "getFilterField", id: filterid },
            success: function(data)
            {
                $('#cfield_div').append(data);
                $("[data-fieldid]").each(function(){
                    $(this).change(function(){
                        $('#art_table').dataTable().fnDraw();
                    });
                });
            }
        });
    }
    $(document).on('click', '.panel-heading span.clickable', function(e){
        var $this = $(this);
        if(!$this.hasClass('panel-collapsed')) {
            $this.parents('.collapseable').find('.panel-body').slideUp();
            $this.addClass('panel-collapsed');
            $this.find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
        } else {
            $this.parents('.collapseable').find('.panel-body').slideDown();
            $this.removeClass('panel-collapsed');
            $this.find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
        }
    })
</script>



<script type="text/javascript" language="javascript" class="init">
    var editor; // use a global for the submit and return data rendering in the examples
    var table; // use global for table

    $(document).ready(function() {

        editor = new $.fn.dataTable.Editor( {
            ajax: {
                url: 'libs/basic/datatables/orderposition.php',
                data: {
                    "collectiveinvoice": <?php echo $collectinv->getId();?>
                }
            },
            table: "#datatable",
            fields: [
                {
                    label: 'Sortierung',
                    name: 'collectiveinvoice_orderposition.sequence',
                    multiEditable: false
                }
            ]
        } );

        editor
            .on( 'initCreate', function () {
                // Enable order for create
                editor.field( 'collectiveinvoice_orderposition.sequence' ).enable();
            } )
            .on( 'initEdit', function () {
                // Disable for edit (re-ordering is performed by click and drag)
                editor.field( 'collectiveinvoice_orderposition.sequence' ).disable();
            } );


        table = $('#datatable').DataTable( {
            dom: "<'row'<'col-sm-12'tr>>",
            ajax: {
                url: 'libs/basic/datatables/orderposition.php',
                data: {
                    "collectiveinvoice": <?php echo $collectinv->getId();?>
                }
            },
            paging: false,
            searching: false,
//                order: [[ 1, 'asc' ]],
            columns: [
                { data: 'collectiveinvoice_orderposition.sequence', className: 'reorder', orderable: false },
                { data: "collectiveinvoice_orderposition.id", orderable: false, className: 'pointer' },
                { data: "collectiveinvoice_orderposition.type", orderable: false, className: 'pointer' },
                { data: "collectiveinvoice_orderposition.status", orderable: false, className: 'pointer' },
                { data: "title", orderable: false, className: 'pointer' },
                { data: "collectiveinvoice_orderposition.quantity", orderable: false, className: 'pointer' },
                { data: "collectiveinvoice_orderposition.price", orderable: false, className: 'pointer' },
                { data: "price_single", orderable: false, className: 'pointer' },
                { data: "collectiveinvoice_orderposition.taxkey", orderable: false, className: 'pointer' },
                { data: "options", orderable: false }
            ],
            rowReorder: {
                dataSrc: 'collectiveinvoice_orderposition.sequence',
                editor:  editor
            },
//                columnDefs: [
//                    { orderable: false, targets: [ 0,1,2,3,4,5,6,7,8 ] }
//                ],
            select: false,
            buttons: [],
            fnRowCallback: function(  nRow, mData, iDisplayIndex ) {
                if (mData['collectiveinvoice_orderposition']['status'] == "deaktiviert") {
                    $('td', nRow).css('background-color', 'rgba(255, 0, 0, 0.5)');
                }
            },
            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api(), data;

                // Total over all pages
                total_price = api
                    .column( 6 )
                    .data()
                    .reduce( function (a, b) {
                        return parseFloat(a) + parseFloat(b.toString().replace(".","").replace(",","."));
                    }, 0 );

                // Total over all pages
                total_qty = api
                    .column( 5 )
                    .data()
                    .reduce( function (a, b) {
                        return parseFloat(a) + parseFloat(b.toString().replace(".","").replace(",","."));
                    }, 0 );

                // Update footer
                $( api.column( 6 ).footer() ).html(
                    printPriceJs(total_price, 2) + " €"
                );
                $( api.column( 5 ).footer() ).html(
                    printPriceJs(total_qty, 2)
                );
            },
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.13/i18n/German.json'
            }
        } );

        // Add event listener for opening and closing details
        // table.ajax.reload( null, false );
        $('#datatable tbody').on('click', 'td:not(:first-child,:last-child)', function () {
            var tr = $(this).closest('tr');
            var row = table.row( tr );

            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.remove();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                // fetch position form
                get_child( row.data(), row, tr );
            }
        } );
    } );

    function get_child ( data, row, tr ) {
        $.ajax({
            dataType: "html",
            type: "GET",
            url: "libs/modules/collectiveinvoice/orderposition.ajax.php",
            data: { "exec": "getPosForm", "oid": data["collectiveinvoice_orderposition"]["id"] },
            success: function(data)
            {
                row.child( data ).show();
                CKEDITOR.replace( 'opos_comment' );
                tr.addClass('shown');
            }
        });
    }

    function submitForm(form){
        CKupdate();
        $.ajax({
            type: 'GET',
            url: 'libs/modules/collectiveinvoice/orderposition.ajax.php?exec=updatePos',
            data: $(form).serialize(),
            success: function() {
                table.ajax.reload( null, false );
                console.log("successful");
            },
            error: function() {
                console.log("unsuccessful");
            }
        });
    }

    function getUpdatedPrice(oid, quantity){
        $.ajax({
            type: 'GET',
            dataType: "html",
            url: 'libs/modules/collectiveinvoice/orderposition.ajax.php',
            data: { "exec": "getUpdatedPrice", "oid": oid, "quantity": quantity },
            success: function(data) {
                var obj = jQuery.parseJSON(data);
                console.log(data);
                $('#opos_price_'+oid).val(obj.price);
                console.log("successful");
            },
            error: function() {
                console.log("unsuccessful");
            }
        });
    }

    function CKupdate(){
        for ( instance in CKEDITOR.instances )
            CKEDITOR.instances[instance].updateElement();
    }
</script>