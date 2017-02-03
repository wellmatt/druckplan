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
                        "collectiveinvoice": 141
                    }
                },
                table: "#datatable",
                fields: [
                    {
                        label: "Status:",
                        name: "status",
                        type: "select"
                    },
                    {
                        label: "Beschreibung:",
                        name: "comment",
                        type: "ckeditor",
                        opts: {
                            toolbarGroups: [
                                { name: 'forms', groups: [ 'forms' ] },
                                { name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
                                { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
                                { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
                                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                                { name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
                                { name: 'links', groups: [ 'links' ] },
                                { name: 'insert', groups: [ 'insert' ] },
                                { name: 'styles', groups: [ 'styles' ] },
                                { name: 'colors', groups: [ 'colors' ] },
                                { name: 'tools', groups: [ 'tools' ] },
                                { name: 'others', groups: [ 'others' ] },
                                { name: 'about', groups: [ 'about' ] }
                            ],
                            removeButtons: 'Source,Save,Templates,NewPage,Preview,Print,Cut,Copy,Paste,PasteText,PasteFromWord,Find,SelectAll,Scayt,Replace,Form,Checkbox,TextField,Radio,Textarea,Select,Button,ImageButton,HiddenField,Subscript,Superscript,CreateDiv,BidiLtr,BidiRtl,Language,Anchor,Image,Flash,Smiley,SpecialChar,Iframe,Font,About,Maximize'
                        }
                    }, {
                        label: "Menge:",
                        name: "quantity"
                    }, {
                        label: "Preis:",
                        name: "price"
                    }, {
                        label: "Steuer:",
                        name: "tax",
                        type: "select"
                    }
                ]
            } );
            // Disable KeyTable while the main editing form is open
            editor
                .on( 'open', function () {
                    table.keys.disable();
                } )
                .on( 'close', function () {
                    table.keys.enable();
                } );

            // Activate an inline edit on click of a table cell
            $('#datatable').on( 'click', 'tbody td:not(:first-child)', function (e) {
                editor.inline( this, {
                    // Submit on leaving field
                    onBlur: 'submit'
                } );
            } );


            table = $('#datatable').DataTable( {
//                dom: "Bfrtip",
                dom: "<'row'<'col-sm-4'l><'col-sm-4'B><'col-sm-4'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>",
                ajax: {
                    url: 'libs/basic/datatables/orderposition.php',
                    data: {
                        "collectiveinvoice": 141
                    }
                },
                order: [[ 1, 'asc' ]],
                columns: [
                    { data: "id" },
                    { data: "type" },
                    { data: "status" },
                    { data: "comment" },
                    { data: "quantity" },
                    { data: "price" },
                    { data: "tax" }
                ],
                // Keyboard Navigation
//                keys: {
//                    columns: ':not(:first-child)',
//                    editor:  editor
//                },
                select: false,
                buttons: [
//                    { extend: "create", editor: editor },
//                    { extend: "edit",   editor: editor },
//                    { extend: "remove", editor: editor },
                    // Export Button
                    {
                        extend: 'collection',
                        text: 'Export',
                        buttons: [
                            'copy',
                            'excel',
                            'csv',
                            'pdf',
                            'print'
                        ]
                    },
                    // Duplicate Button
//                    {
//                        extend: "selectedSingle",
//                        text: 'Duplicate',
//                        action: function ( e, dt, node, config ) {
//                            // Place the selected row into edit mode (but hidden),
//                            // then get the values for all fields in the form
//                            var values = editor.edit(
//                                table.row( { selected: true } ).index(),
//                                false
//                                )
//                                .val();
//
//                            // Create a new entry (discarding the previous edit) and
//                            // set the values from the read values
//                            editor
//                                .create( {
//                                    title: 'Duplicate record',
//                                    buttons: 'Create from existing'
//                                } )
//                                .set( values );
//                        }
//                    }
                ],
                footerCallback: function ( row, data, start, end, display ) {
                    var api = this.api(), data;

                    // Total over all pages
                    total_price = api
                        .column( 5 )
                        .data()
                        .reduce( function (a, b) {
                            return parseFloat(a) + parseFloat(b);
                        }, 0 );

                    // Total over this page
                    pageTotal_price = api
                        .column( 5, { page: 'current'} )
                        .data()
                        .reduce( function (a, b) {
                            return parseFloat(a) + parseFloat(b);
                        }, 0 );

                    // Total over all pages
                    total_qty = api
                        .column( 4 )
                        .data()
                        .reduce( function (a, b) {
                            return parseFloat(a) + parseFloat(b);
                        }, 0 );

                    // Total over this page
                    pageTotal_qty = api
                        .column( 4, { page: 'current'} )
                        .data()
                        .reduce( function (a, b) {
                            return parseFloat(a) + parseFloat(b);
                        }, 0 );

                    // Update footer
                    $( api.column( 5 ).footer() ).html(
                        pageTotal_price +' ( '+ total_price +' )'
                    );
                    $( api.column( 4 ).footer() ).html(
                        pageTotal_qty +' ( '+ total_qty +' )'
                    );
                }
            } );
        } );
    </script>

</head>

<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Test</h3>
        </div>
        <div class="table-responsive">
            <table id="datatable" class="table table-striped table-bordered" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Typ</th>
                        <th>Status</th>
                        <th>Beschreibung</th>
                        <th>Menge</th>
                        <th>Preis in €</th>
                        <th>Steuer</th>
                    </tr>
                </thead>
                <tfoot>
                    <th>Summen:</th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tfoot>
            </table>
        </div>
    </div>
</div>