<?php
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/paper/paper.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/foldtypes/foldtype.class.php';
require_once 'libs/modules/paperformats/paperformat.class.php';
require_once 'libs/modules/products/product.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/chromaticity/chromaticity.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/finishings/finishing.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/collectiveinvoice/orderposition.class.php';
require_once 'libs/modules/personalization/personalization.order.class.php';
require_once "thirdparty/phpfastcache/phpfastcache.php";
require_once 'libs/basic/cachehandler/cachehandler.class.php';
require_once 'libs/basic/eventqueue/eventqueue.class.php';
require_once 'libs/basic/eventqueue/eventclass.interface.php';
require_once 'libs/modules/mail/mailmassage.class.php';
require_once 'libs/modules/organizer/caldav.service.class.php';
require_once 'libs/modules/storage/storage.position.class.php';

require_once 'vendor/PEAR/Net/SMTP.php';
require_once 'vendor/PEAR/Net/Socket.php';
require_once 'vendor/Horde/Autoloader.php';
require_once 'vendor/Horde/Autoloader/ClassPathMapper.php';
require_once 'vendor/Horde/Autoloader/ClassPathMapper/Default.php';
$autoloader = new Horde_Autoloader();
$autoloader->addClassPathMapper(new Horde_Autoloader_ClassPathMapper_Default('vendor'));
$autoloader->registerAutoloader();

require_once('vendor/simpleCalDAV/SimpleCalDAVClient.php');

error_reporting(-1);
ini_set('display_errors', 1);
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);

$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();
?>

<!DOCTYPE html
PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <!-- <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" type="text/css" href="./css/matze.css" />
    <link rel="stylesheet" type="text/css" href="./css/ticket.css" />
    <link rel="stylesheet" type="text/css" href="./css/menu.css" />
    <link rel="stylesheet" type="text/css" href="./css/main.print.css" media="print"/>
    <link rel="stylesheet" type="text/css" href="./css/quickmove.css" />

    <!-- jQuery -->
    <link type="text/css" href="jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="jscripts/jquery/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
    <script type="text/javascript" src="jscripts/jquery/js/jquery.blockUI.js"></script>
    <script language="JavaScript" src="./jscripts/jquery/local/jquery.ui.datepicker-<?=$_LANG->getCode()?>.js"></script>
    <script type="text/javascript" src="jscripts/jquery.validate.min.js"></script>
    <script type="text/javascript" src="jscripts/moment/moment-with-locales.min.js"></script>
    <!-- /jQuery -->
    <!-- FancyBox -->
    <script type="text/javascript" src="jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
    <script type="text/javascript" src="jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
    <link rel="stylesheet" type="text/css" href="jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
    <!-- /FancyBox -->
    <script language="javascript" src="jscripts/basic.js"></script>
    <script language="javascript" src="jscripts/loadingscreen.js"></script>

    <link type="text/css" href="/cometchat/cometchatcss.php" rel="stylesheet" charset="utf-8">
    <script type="text/javascript" src="/cometchat/cometchatjs.php" charset="utf-8"></script>

    <!-- MegaNavbar -->
    <link href="thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="thirdparty/MegaNavbar/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="thirdparty/MegaNavbar/assets/css/MegaNavbar.css"/>
    <link rel="stylesheet" type="text/css" href="thirdparty/MegaNavbar/assets/css/skins/navbar-default.css" title="inverse">
    <script src="thirdparty/MegaNavbar/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="jscripts/jquery.bootstrap.wizard.min.js"></script>
    <!-- /MegaNavbar -->

    <!-- PACE -->
    <script src="jscripts/pace/pace.min.js"></script>
    <link href="jscripts/pace/pace-theme-big-counter.css" rel="stylesheet" />
    <!-- /PACE -->
    <link rel="stylesheet" type="text/css" href="./css/glyphicons-bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="./css/glyphicons.css" />
    <link rel="stylesheet" type="text/css" href="./css/glyphicons-halflings.css" />
    <link rel="stylesheet" type="text/css" href="./css/glyphicons-filetypes.css" />
    <link rel="stylesheet" type="text/css" href="./css/glyphicons-social.css" />
    <link rel="stylesheet" type="text/css" href="./css/main.css" />

    <!-- FLOT -->
    <!--[if lte IE 8]><script language="javascript" type="text/javascript" src="jscripts/flot/excanvas.min.js"></script><![endif]-->
    <script language="javascript" type="text/javascript" src="jscripts/flot/jquery.flot.js"></script>
    <script language="javascript" type="text/javascript" src="jscripts/flot/jquery.flot.pie.js"></script>
    <script language="javascript" type="text/javascript" src="jscripts/flot/jquery.flot.categories.js"></script>
    <!-- /FLOT -->

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


    <script type="text/javascript" language="javascript" class="init">
        var editor; // use a global for the submit and return data rendering in the examples
        var table; // use global for table

        $(document).ready(function() {

            editor = new $.fn.dataTable.Editor( {
                ajax: {
                    url: 'libs/basic/datatables/orderposition.php',
                    data: {
                        "collectiveinvoice": 142
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
                    editor.field( 'sequence' ).enable();
                } )
                .on( 'initEdit', function () {
                    // Disable for edit (re-ordering is performed by click and drag)
                    editor.field( 'sequence' ).disable();
                } );


            table = $('#datatable').DataTable( {
                dom: "<'row'<'col-sm-12'tr>>",
                ajax: {
                    url: 'libs/basic/datatables/orderposition.php',
                    data: {
                        "collectiveinvoice": 142
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

        function CKupdate(){
            for ( instance in CKEDITOR.instances )
                CKEDITOR.instances[instance].updateElement();
        }
    </script>

</head>

<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Test</h3>
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
                        <th>Preis in €</th>
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
</div>