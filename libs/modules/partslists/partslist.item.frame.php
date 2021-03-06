<? /**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
chdir("../../../");
//error_reporting(E_ALL);
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once("libs/basic/countries/country.class.php");
require_once('libs/modules/businesscontact/businesscontact.class.php');
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/comment/comment.class.php';
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'partslist.class.php';

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


if ($_USER == false){
    error_log("Login failed (basic-importer.php)");
    die("Login failed");
}
$perf = new Perferences();

if ($_REQUEST['exec'] == 'delete'){
    $id = $_REQUEST['id'];
    $partslistitem = new PartslistItem($id);
    $partslistitem->delete();
    echo '<script language="JavaScript">parent.$.fancybox.close(); parent.load_content();</script>';
}
if ($_REQUEST['exec'] == 'save'){
    $id = $_REQUEST['id'];
    $create = [
        'partslist'=>$_REQUEST['partslist'],
        'article'=>$_REQUEST['article'],
        'amount'=>tofloat($_REQUEST['amount']),
    ];
    $partslistitem = new PartslistItem($id,$create);
    $partslistitem->save();
    echo '<script language="JavaScript">parent.$.fancybox.close(); parent.load_content();</script>';
}

if ($_REQUEST["aid"] && $_REQUEST["list"]) {
    $article = new Article($_REQUEST["aid"]);
    $partslist = new Partslist($_REQUEST["list"]);
    $partslistitem = new PartslistItem();
    $partslistitem->setArticle($article);
    $partslistitem->setPartslist($partslist);
} elseif ($_REQUEST["id"]) {
    $partslistitem = new PartslistItem($_REQUEST["id"]);
    $article = $partslistitem->getArticle();
} else {
    die("Kein Artikel ausgewählt!");
}

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
</head>

<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/matze.css" />
<link rel="stylesheet" type="text/css" href="../../../css/ticket.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<link rel="stylesheet" type="text/css" href="../../../css/main.print.css" media="print"/>

<!-- jQuery -->
<link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery.blockUI.js"></script>
<script language="JavaScript" src="../../../jscripts/jquery/local/jquery.ui.datepicker-<?=$_LANG->getCode()?>.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery.validate.min.js"></script>
<script type="text/javascript" src="../../../jscripts/moment/moment-with-locales.min.js"></script>
<!-- /jQuery -->
<!-- FancyBox -->
<script type="text/javascript" src="../../../jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="../../../jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="../../../jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<!-- /FancyBox -->
<script language="javascript" src="../../../jscripts/basic.js"></script>
<script language="javascript" src="../../../jscripts/loadingscreen.js"></script>

<!-- MegaNavbar -->
<link href="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="../../../thirdparty/MegaNavbar/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../../../thirdparty/MegaNavbar/assets/css/MegaNavbar.css"/>
<link rel="stylesheet" type="text/css" href="../../../thirdparty/MegaNavbar/assets/css/skins/navbar-default.css" title="inverse">
<script src="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<!-- /MegaNavbar -->

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="../../../css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="../../../css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="../../../css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/dataTables.tableTools.js"></script>

<form action="partslist.item.frame.php" id="partslistitem_form" name="partslistitem_form" method="post">
    <input type="hidden" id="id" name="id" value="<?=$partslistitem->getId()?>" />
    <input type="hidden" id="article" name="article" value="<?=$partslistitem->getArticle()->getId()?>" />
    <input type="hidden" id="partslist" name="partslist" value="<?=$partslistitem->getPartslist()->getId()?>" />
    <input type="hidden" id="exec" name="exec" value="save" />

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?= $_LANG->get('Stücklisten-Position') ?><?php echo ' - '.$partslistitem->getArticle()->getTitle();?></h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Menge</label>
                        <div class="col-sm-3">
                            <input type="text" id="amount" name="amount"  value="<?=printPrice($partslistitem->getAmount(),2)?>" onfocus="markfield(this,0)" onblur="markfield(this,1)" class="form-control">
                        </div>
                    </div>
                    <span class="pull-right">
                          <?php if ($partslistitem->getId()>0){?>
                              <button type="button" class="btn btn-danger" onclick="$('#exec').val('delete');$('#partslistitem_form').submit();">Löschen</button>
                          <?php } ?>
                        <button type="submit" class="btn btn-default">Speichern</button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</form>