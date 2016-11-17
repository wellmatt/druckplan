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
    $storagearea = $position->getArea();
    $maxallocation = 100 - StoragePosition::getAllocationForArea($storagearea);
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

<form action="storage.position.frame.php" id="storagearea_form" name="storagearea_form" class="form-horizontal" method="post">
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


                      <div class="form-group">
                          <label for="" class="col-sm-3 control-label">Kunde</label>
                          <div class="col-sm-4">
                              <input type="text" id="stp_businesscontact_name" name="stp_businesscontact_name"
                                     class="form-control"
                                     value="<?php if ($position->getBusinesscontact()->getId() > 0) echo $position->getBusinesscontact()->getNameAsLine(); ?>"
                                     onfocus="markfield(this,0)" onblur="markfield(this,1)">
                              <input type="hidden" id="stp_businesscontact" name="stp_businesscontact"
                                     value="<?= $position->getBusinesscontact()->getId() ?>">
                          </div>
                      </div>

                      <div class="form-group">
                          <label for="" class="col-sm-3 control-label">Lagermenge</label>
                          <div class="col-sm-4">
                              <input type="text" id="stp_amount" name="stp_amount" value="<?= $position->getAmount() ?>"
                                     onfocus="markfield(this,0)" onblur="markfield(this,1)" class="form-control">
                          </div>
                      </div>

                      <div class="form-group">
                          <label for="" class="col-sm-3 control-label">Mindestmenge</label>
                          <div class="col-sm-4">
                              <input type="text" id="stp_min_amount" name="stp_min_amount"
                                     value="<?= $position->getMinAmount() ?>"
                                     onfocus="markfield(this,0)" onblur="markfield(this,1)" class="form-control">
                          </div>
                      </div>

                      <div class="form-group">
                          <label for="" class="col-sm-3 control-label">Verantwortlicher</label>
                          <div class="col-sm-4">
                              <input type="text" id="stp_respuser_name" name="stp_respuser_name"
                                     value="<?php if ($position->getRespuser()->getId() > 0) echo $position->getRespuser()->getNameAsLine(); ?>"
                                     onfocus="markfield(this,0)" onblur="markfield(this,1)" class="form-control">
                              <input type="hidden" id="stp_respuser" name="stp_respuser"
                                     value="<?= $position->getRespuser()->getId() ?>">
                          </div>
                      </div>

                      <div class="form-group">
                          <label for="" class="col-sm-3 control-label">Versandart</label>
                          <div class="col-sm-4">
                              <input type="text" id="stp_dispatch" name="stp_dispatch"
                                     value="<?= $position->getDispatch() ?>"
                                     onfocus="markfield(this,0)" onblur="markfield(this,1)" class="form-control">
                          </div>
                      </div>
                      <div class="form-group">
                          <label for="" class="col-sm-3 control-label">Verpackungsart</label>
                          <div class="col-sm-4">
                              <input type="text" id="stp_packaging" name="stp_packaging"
                                     value="<?= $position->getPackaging() ?>"
                                     onfocus="markfield(this,0)" onblur="markfield(this,1)" class="form-control">
                          </div>
                      </div>
                      <div class="form-group">
                          <label for="" class="col-sm-3 control-label">Belegung</label>
                          <div class="col-sm-4">
                              <div class="input-group">
                                  <div class="input-group-btn">
                                      <button type="button" class="btn btn-xs btn-default dropdown-toggle"
                                              data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                          &nbsp;<span class="caret"></span></button>
                                      <ul class="dropdown-menu">
                                          <li><a href="#" onclick="setAlloc(0);">0%</a></li>
                                          <li><a href="#" onclick="setAlloc(25);">25%</a></li>
                                          <li><a href="#" onclick="setAlloc(50);">50%</a></li>
                                          <li><a href="#" onclick="setAlloc(75);">75%</a></li>
                                          <li><a href="#" onclick="setAlloc(100);">100%</a></li>
                                      </ul>
                                  </div>
                                  <input type="number" min="0" max="<?php echo $maxallocation; ?>" name="stp_allocation"
                                         id="stp_allocation" class="numberBox">%
                              </div>
                          </div>
                      </div>
                      <div class="form-group">
                          <label for="" class="col-sm-3 control-label">Beschreibung</label>
                          <div class="col-sm-9">
                                       <textarea rows="8" cols="80" type="text" id="stp_description"
                                                 name="stp_description" class="form-control" onfocus="markfield(this,0)"
                                                 onblur="markfield(this,1)"><?= $position->getDescription() ?>
                                       </textarea>
                          </div>
                      </div>
                      <div class="form-group">
                          <label for="" class="col-sm-3 control-label">Bemerkung</label>
                          <div class="col-sm-9">
                                       <textarea rows="8" cols="80" type="text" id="stp_note" name="stp_note"
                                                 class="form-control" onfocus="markfield(this,0)"
                                                 onblur="markfield(this,1)"><?= $position->getNote() ?>
                                       </textarea>
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
        $( ".numberBox" ).change(function() {
            var max = parseInt($(this).attr('max'));
            var min = parseInt($(this).attr('min'));
            if ($(this).val() > max)
            {
                $(this).val(max);
            }
            else if ($(this).val() < min)
            {
                $(this).val(min);
            }
        });
    });
    function setAlloc(perc){
        var input = $('#stp_allocation');
        if (perc <= parseInt(input.attr('max'))){
            input.val(perc);
        } else {
            input.val(input.attr('max'));
        }
    }
</script>