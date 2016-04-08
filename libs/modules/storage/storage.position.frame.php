<? /**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
chdir("../../../");
require_once("config.php");
// error_reporting(-1);
// ini_set('display_errors', 1);
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
require_once 'storage.area.class.php';
require_once 'storage.position.class.php';

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

if ($_REQUEST['exec'] == 'delete'){
    $id = $_REQUEST['id'];
    $storageposition = new StoragePosition($id);
    $storageposition->delete();
    echo '<script language="JavaScript">parent.$.fancybox.close(); parent.load_content();</script>';
}
if ($_REQUEST['exec'] == 'save'){
    $id = $_REQUEST['id'];
    $create = [
        'area'=>$_REQUEST['area'],
        'article'=>$_REQUEST['article'],
        'businesscontact'=>$_REQUEST['stp_businesscontact'],
        'amount'=>$_REQUEST['stp_amount'],
        'min_amount'=>$_REQUEST['stp_min_amount'],
        'respuser'=>$_REQUEST['stp_respuser'],
        'description'=>$_REQUEST['stp_description'],
        'note'=>$_REQUEST['stp_note'],
        'dispatch'=>$_REQUEST['stp_dispatch'],
        'packaging'=>$_REQUEST['stp_packaging'],
        'allocation'=>$_REQUEST['stp_allocation'],
    ];
    $storageposition = new StoragePosition($id,$create);
    $storageposition->save();
    echo '<script language="JavaScript">parent.$.fancybox.close(); parent.load_content();</script>';
}

$all_bcs = BusinessContact::getAllBusinessContacts();
$all_users = User::getAllUser();

if ($_REQUEST["aid"] && $_REQUEST["stid"]) {
    $article = new Article($_REQUEST["aid"]);
    $storagearea = new StorageArea($_REQUEST["stid"]);
    $position = new StoragePosition();
    $position->setArticle($article);
    $position->setArea($storagearea);
    $maxallocation = 100 - StoragePosition::getAllocationForArea($storagearea);
} elseif ($_REQUEST["id"]) {
    $position = new StoragePosition($_REQUEST["id"]);
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

<form action="storage.position.frame.php" id="storagearea_form" name="storagearea_form" method="post">
    <input type="hidden" id="id" name="id" value="<?=$position->getId()?>" />
    <input type="hidden" id="article" name="article" value="<?=$position->getArticle()->getId()?>" />
    <input type="hidden" id="area" name="area" value="<?=$position->getArea()->getId()?>" />
    <input type="hidden" id="exec" name="exec" value="save" />

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="panel panel-default">
                  <div class="panel-heading">
                        <h3 class="panel-title"><?= $_LANG->get('Lagerposition') ?></h3>
                  </div>
                  <div class="panel-body">
                      <table border="0" cellpadding="3" cellspacing="1" width="100%">
                          <colgroup>
                              <col width="130">
                              <col>
                          </colgroup>
                          <tr>
                              <td class="content_header"><?= $_LANG->get('Kunde') ?>:</td>
                              <td class="content_row_clear">
                                  <input type="text" id="stp_businesscontact_name" name="stp_businesscontact_name"
                                         style="width:200px"
                                         value="<?php if ($position->getBusinesscontact()->getId() > 0) echo $position->getBusinesscontact()->getNameAsLine(); ?>"
                                         onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                                  <input type="hidden" id="stp_businesscontact" name="stp_businesscontact"
                                         value="<?= $position->getBusinesscontact()->getId() ?>">
                              </td>
                          </tr>
                          <tr>
                              <td class="content_header"><?= $_LANG->get('Lagermenge') ?>:</td>
                              <td class="content_row_clear">
                                  <input type="text" id="stp_amount" name="stp_amount" style="width:200px"
                                         value="<?= $position->getAmount() ?>"
                                         onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                              </td>
                          </tr>
                          <tr>
                              <td class="content_header"><?= $_LANG->get('Mindestmenge') ?>:</td>
                              <td class="content_row_clear">
                                  <input type="text" id="stp_min_amount" name="stp_min_amount" style="width:200px"
                                         value="<?= $position->getMinAmount() ?>"
                                         onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                              </td>
                          </tr>
                          <tr>
                              <td class="content_header"><?= $_LANG->get('Verantwortlicher') ?>:</td>
                              <td class="content_row_clear">
                                  <input type="text" id="stp_respuser_name" name="stp_respuser_name" style="width:200px"
                                         value="<?php if ($position->getRespuser()->getId() > 0) echo $position->getRespuser()->getNameAsLine(); ?>"
                                         onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                                  <input type="hidden" id="stp_respuser" name="stp_respuser"
                                         value="<?= $position->getRespuser()->getId() ?>">
                              </td>
                          </tr>
                          <tr>
                              <td class="content_header"><?= $_LANG->get('Beschreibung') ?>:</td>
                              <td class="content_row_clear">
                                  <textarea rows="8" cols="80" type="text" id="stp_description" name="stp_description"
                                          class="text" onfocus="markfield(this,0)"
                                          onblur="markfield(this,1)"><?= $position->getDescription() ?></textarea>
                              </td>
                          </tr>
                          <tr>
                              <td class="content_header"><?= $_LANG->get('Bemerkung') ?>:</td>
                              <td class="content_row_clear">
                                  <textarea rows="8" cols="80" type="text" id="stp_note" name="stp_note"
                                          class="text" onfocus="markfield(this,0)"
                                          onblur="markfield(this,1)"><?= $position->getNote() ?></textarea>
                              </td>
                          </tr>
                          <tr>
                              <td class="content_header"><?= $_LANG->get('Versandart') ?>:</td>
                              <td class="content_row_clear">
                                  <input type="text" id="stp_dispatch" name="stp_dispatch" style="width:200px"
                                         value="<?= $position->getDispatch() ?>"
                                         onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                              </td>
                          </tr>
                          <tr>
                              <td class="content_header"><?= $_LANG->get('Verpackungsart') ?>:</td>
                              <td class="content_row_clear">
                                  <input type="text" id="stp_packaging" name="stp_packaging" style="width:200px"
                                         value="<?= $position->getPackaging() ?>"
                                         onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                              </td>
                          </tr>
                          <tr>
                              <td class="content_header"><?= $_LANG->get('Belegung') ?>:</td>
                              <td class="content_row_clear">
                                  <input type="text" id="stp_allocation" name="stp_allocation" readonly
                                         style="width:200px" value="<?= $position->getAllocation() ?>"
                                         onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text"><label
                                      for="stp_allocation">%</label>
                                  <div id="stp_allocation_slider" style="width: 210px;"></div>
                              </td>
                          </tr>
                      </table>
                  </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="form-group">
                <div class="col-sm-offset-8 col-sm-2">
                    <?php if ($position->getId()>0){?>
                    <button type="button" class="btn btn-danger" onclick="$('#exec').val('delete');$('#storagearea_form').submit();">Löschen</button>
                    <?php } ?>
                </div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-default">Speichern</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $(function() {
        $( "#stp_businesscontact_name" ).autocomplete({
            delay: 0,
            source: '../../../libs/modules/tickets/ticket.ajax.php?ajax_action=search_customer',
            minLength: 2,
            dataType: "json",
            select: function(event, ui) {
                $('#stp_businesscontact').val(ui.item.value);
                $('#stp_businesscontact_name').val(ui.item.label);
                return false;
            },
            focus: function( event, ui ) {

            }
        });
        $( "#stp_respuser_name" ).autocomplete({
            delay: 0,
            source: '../../../libs/modules/tickets/ticket.ajax.php?ajax_action=search_user',
            minLength: 2,
            dataType: "json",
            select: function(event, ui) {
                $('#stp_respuser').val(ui.item.value);
                $('#stp_respuser_name').val(ui.item.label);
                return false;
            },
            focus: function( event, ui ) {

            }
        });
        $( "#stp_allocation_slider" ).slider({
            range: "max",
            min: 1,
            max: <?php echo $maxallocation; ?>,
            value: $('#stp_allocation').val(),
            slide: function( event, ui ) {
                $( "#stp_allocation" ).val( ui.value );
            }
        });
        $( "#stp_allocation" ).val( $( "#stp_allocation_slider" ).slider( "value" ) );
    });
</script>