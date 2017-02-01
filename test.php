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

    <link rel="stylesheet" type="text/css" href="jscripts/datatableeditor/datatables.min.css"/>
    <script type="text/javascript" src="jscripts/datatableeditor/datatables.min.js"></script>
    <link rel="stylesheet" type="text/css" href="jscripts/datatableeditor/Editor-1.6.1/css/dataTables.editor.css"/>
    <script type="text/javascript" src="jscripts/datatableeditor/Editor-1.6.1/js/dataTables.editor.min.js"></script>



    <script type="text/javascript" language="javascript" class="init">
        var editor; // use a global for the submit and return data rendering in the examples

        $(document).ready(function() {
            editor = new $.fn.dataTable.Editor( {
                ajax: "libs/basic/datatables/user.php",
                table: "#example",
                fields: [ {
                    label: "id:",
                    name: "id",
                    type: "readonly"
                }, {
                    label: "login:",
                    name: "login"
                }, {
                    label: "user_firstname:",
                    name: "user_firstname"
                }, {
                    label: "user_lastname:",
                    name: "user_lastname"
                }, {
                    label: "user_email:",
                    name: "user_email"
                }
                ]
            } );

            // Activate an inline edit on click of a table cell
            $('#example').on( 'click', 'tbody td:not(:first-child)', function (e) {
                editor.inline( this );
            } );

            $('#example').DataTable( {
                dom: "Bfrtip",
                ajax: "libs/basic/datatables/user.php",
                order: [[ 1, 'asc' ]],
                columns: [
                    {
                        data: null,
                        defaultContent: '',
                        className: 'select-checkbox',
                        orderable: false
                    },
                    { data: "id" },
                    { data: "login" },
                    { data: "user_firstname" },
                    { data: "user_lastname" },
                    { data: "user_email" }
                ],
                select: {
//                    style:    'os',
                    selector: 'td:first-child'
                },
                buttons: [
                    { extend: "create", editor: editor },
                    { extend: "edit",   editor: editor },
                    { extend: "remove", editor: editor }
                ]
            } );
        } );
    </script>

</head>

<div class="container">
    <table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th></th>
            <th>id</th>
            <th>login</th>
            <th>user_firstname</th>
            <th>user_lastname</th>
            <th>user_email</th>
        </tr>
        </thead>
    </table>
</div>