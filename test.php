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
require_once 'libs/modules/accounting/receipt.class.php';
require_once 'libs/modules/textblocks/textblock.class.php';

require_once 'vendor/PEAR/Net/SMTP.php';
require_once 'vendor/PEAR/Net/Socket.php';
require_once 'vendor/Horde/Autoloader.php';
require_once 'vendor/Horde/Autoloader/ClassPathMapper.php';
require_once 'vendor/Horde/Autoloader/ClassPathMapper/Default.php';
$autoloader = new Horde_Autoloader();
$autoloader->addClassPathMapper(new Horde_Autoloader_ClassPathMapper_Default('vendor'));
$autoloader->registerAutoloader();

require_once 'vendor/autoload.php';
use \Curl\Curl;
require_once('vendor/simpleCalDAV/SimpleCalDAVClient.php');
require_once 'libs/modules/saxoprint/saxoprint.class.php';

error_reporting(-1);
ini_set('display_errors', 1);
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);

$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$me = new Machineentry(4298);
prettyPrint($me);

?>
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

<!-- Select2 -->
<link href="jscripts/select2/dist/css/select2.min.css" rel="stylesheet" />
<script src="jscripts/select2/dist/js/select2.min.js"></script>
<script src="jscripts/select2/dist/js/i18n/de.js"></script>
<!-- /Select2 -->

<!-- CKEditor -->
<script type="text/javascript" src="jscripts/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="jscripts/ckeditor/config.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/ckeditor/skins/bootstrapck/editor.css"/>
<!-- /CKEditor -->