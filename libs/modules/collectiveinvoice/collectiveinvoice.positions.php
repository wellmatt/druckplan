<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */



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
    function addArticle(){
        var article = $('#article_selector').val();
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

    function addPartslist(){
        var partslist = $('#partslist_selector').val();
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

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Positionen
            <?php if ($collectinv->getLocked() == 0 && $collectinv->getId() > 0){?>
                <span class="pull-right" style="width: 75%; text-align: right; white-space: nowrap;">
                    <!-- Artikel -->
                    <label for="" class="control-label">Artikel</label>
                    <select id="article_selector" style="width: 200px"></select>
                    <button class="btn btn-xs btn-success" id="article_add_btn" disabled type="button" onclick="addArticle();"><span class="glyphicons glyphicons-plus"></span></button>

                    <!-- Stücklisten -->
                    <label for="" class="control-label">Stückliste</label>
                    <select id="partslist_selector" style="width: 200px"></select>
                    <button class="btn btn-xs btn-success" id="partslist_add_btn" disabled type="button" onclick="addPartslist();"><span class="glyphicons glyphicons-plus"></span></button>
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
                <th>Nettopreis (€)</th>
                <th>Steuer</th>
                <th></th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>Summen:</th>
                <th></th>
                <th></th>
                <th></th>
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


<script type="text/javascript" language="javascript" class="init">
    var editor; // use a global for the submit and return data rendering in the examples
    var table; // use global for table

    $(document).ready(function() {

        $("#article_selector").select2({
            ajax: {
                url: "libs/basic/ajax/select2.ajax.php?ajax_action=search_article",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
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
        });
        $('#article_selector').on('select2:select', function (evt) {
            $('#article_add_btn').prop('disabled', false);
        });

        $("#partslist_selector").select2({
            ajax: {
                url: "libs/basic/ajax/select2.ajax.php?ajax_action=search_partslist",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        term: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
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
        });
        $('#partslist_selector').on('select2:select', function (evt) {
            $('#partslist_add_btn').prop('disabled', false);
        });

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
                        return parseFloat(a) + parseFloat(b);
                    }, 0 );

                // Total over this page
                pageTotal_price = api
                    .column( 6, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0 );

                // Total over all pages
                total_qty = api
                    .column( 5 )
                    .data()
                    .reduce( function (a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0 );

                // Total over this page
                pageTotal_qty = api
                    .column( 5, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return parseFloat(a) + parseFloat(b);
                    }, 0 );

                // Update footer
                $( api.column( 6 ).footer() ).html(
                    pageTotal_price +' ( '+ total_price +' )'
                );
                $( api.column( 5 ).footer() ).html(
                    pageTotal_qty +' ( '+ total_qty +' )'
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