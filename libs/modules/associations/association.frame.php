<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       22.05.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

// error_reporting(-1); 
// ini_set('display_errors', 1);

chdir("../../../");
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once("libs/basic/countries/country.class.php");
require_once 'libs/modules/organizer/contact.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/businesscontact/contactperson.class.php';
require_once 'libs/modules/organizer/event.class.php';
require_once 'libs/modules/associations/association.class.php';
require_once 'libs/modules/organizer/event.class.php';

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


if ($_USER == false)
    die("Login failed");


if ($_REQUEST["delete"] && $_REQUEST["id"])
{
    $association = new Association((int)$_REQUEST["id"]);
    $association->delete();
    unset($association);
    echo '<script language="JavaScript">parent.$.fancybox.close(); window.opener.location.href = window.opener.location.href;</script>';
} elseif($_REQUEST["exec"] == "save" && $_REQUEST["module"] && $_REQUEST["objectid"])
{
    if ($_REQUEST["order_id"] != ""){
        $association = new Association();
        $association->setModule1($_REQUEST["module"]);
        $association->setObjectid1((int)$_REQUEST["objectid"]);
        $association->setModule2("Order");
        $association->setObjectid2((int)$_REQUEST["order_id"]);
        $save_ok = $association->save();
    }
    if ($_REQUEST["colinv_id"] != ""){
        $association = new Association();
        $association->setModule1($_REQUEST["module"]);
        $association->setObjectid1((int)$_REQUEST["objectid"]);
        $association->setModule2("CollectiveInvoice");
        $association->setObjectid2((int)$_REQUEST["colinv_id"]);
        $save_ok = $association->save();
    }
    if ($_REQUEST["event_id"] != ""){
        $association = new Association();
        $association->setModule1($_REQUEST["module"]);
        $association->setObjectid1((int)$_REQUEST["objectid"]);
        $association->setModule2("Event");
        $association->setObjectid2((int)$_REQUEST["event_id"]);
        $save_ok = $association->save();
    }
    if ($_REQUEST["schedule_id"] != ""){
        $association = new Association();
        $association->setModule1($_REQUEST["module"]);
        $association->setObjectid1((int)$_REQUEST["objectid"]);
        $association->setModule2("Schedule");
        $association->setObjectid2((int)$_REQUEST["schedule_id"]);
        $save_ok = $association->save();
    }
    if ($_REQUEST["maschine_id"] != ""){
        $association = new Association();
        $association->setModule1($_REQUEST["module"]);
        $association->setObjectid1((int)$_REQUEST["objectid"]);
        $association->setModule2("Machine");
        $association->setObjectid2((int)$_REQUEST["machine_id"]);
        $save_ok = $association->save();
    }
    if ($_REQUEST["ticket_id"] != ""){
        $association = new Association();
        $association->setModule1($_REQUEST["module"]);
        $association->setObjectid1((int)$_REQUEST["objectid"]);
        $association->setModule2("Ticket");
        $association->setObjectid2((int)$_REQUEST["ticket_id"]);
        $save_ok = $association->save();
    }
    echo '<script language="JavaScript">parent.$.fancybox.close(); window.opener.location.href = window.opener.location.href;</script>';
} elseif($_REQUEST["module"] && $_REQUEST["objectid"]) {

$module = $_REQUEST["module"];
$objectid = $_REQUEST["objectid"];
    
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<link rel="stylesheet" type="text/css" href="../../../css/main.print.css" media="print"/>

<!-- MegaNavbar -->
<link href="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="../../../thirdparty/MegaNavbar/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../../../thirdparty/MegaNavbar/assets/css/MegaNavbar.css"/>
<link rel="stylesheet" type="text/css" href="../../../thirdparty/MegaNavbar/assets/css/skins/navbar-default.css" title="inverse">
<script src="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<!-- /MegaNavbar -->

<!-- jQuery -->
<link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script language="JavaScript" src="../../../jscripts/jquery/local/jquery.ui.datepicker-<?=$_LANG->getCode()?>.js"></script>
<!-- /jQuery -->
<!-- FancyBox -->
<script	type="text/javascript" src="../../../jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script	type="text/javascript" src="../../../jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="../../../jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
</head>
<body>


<script language="JavaScript" >
$(function() {
	 $( "#search" ).autocomplete({
		 source: "association.ajax.php?ajax_action=search_all",
		 minLength: 2,
		 focus: function( event, ui ) {
    		 $( "#ticket" ).val( ui.item.label );
    		 return false;
		 },
		 select: function( event, ui ) {
			 switch (ui.item.type) {
			    case "Ticket":
		    		 $( "#search" ).val( ui.item.label );
		    		 $( "#ticket_id" ).val( ui.item.value );
		    		 return false;
			        break;
			    case "Machine":
		    		 $( "#search" ).val( ui.item.label );
		    		 $( "#maschine_id" ).val( ui.item.value );
		    		 return false;
			        break;
			    case "Event":
		    		 $( "#search" ).val( ui.item.label );
		    		 $( "#event_id" ).val( ui.item.value );
		    		 return false;
			        break;
			    case "CollectiveInvoice":
		    		 $( "#search" ).val( ui.item.label );
		    		 $( "#colinv_id" ).val( ui.item.value );
		    		 return false;
			        break;
			    case "Order":
		    		 $( "#search" ).val( ui.item.label );
		    		 $( "#order_id" ).val( ui.item.value );
		    		 return false;
			        break;
			} 
		 }
	 });
});
</script>
<form action="association.frame.php"  method="post" name="association_form">
<input type="hidden" name="exec" value="save">
<input type="hidden" name="module" value="<?php echo $_REQUEST["module"];?>">
<input type="hidden" name="objectid" value="<?php echo $_REQUEST["objectid"];?>">

    <div class="panel panel-default">
          <div class="panel-heading">
                <h3 class="panel-title">
                    Neue Verknüpfung
                    <span class="pull-right">
                        <button class="btn btn-xs btn-success" type="submit">
                            <?= $_LANG->get('Speichern') ?>
                        </button>
                    </span>
                </h3>
          </div>
         <br>
        <div class="form-horizontal">
            <div class="form-group">
                <label for="" class="col-sm-1 control-label">Suche</label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="search" name="search" value="" />
                    <input type="hidden" id="ticket_id" name="ticket_id" value=""/>
                    <input type="hidden" id="order_id" name="order_id" value=""/>
                    <input type="hidden" id="colinv_id" name="colinv_id" value=""/>
                    <input type="hidden" id="event_id" name="event_id" value=""/>
                    <input type="hidden" id="schedule_id" name="schedule_id" value=""/>
                    <input type="hidden" id="maschine_id" name="maschine_id" value=""/>
                </div>
            </div>
        </div>
        <br>
        * Suche in Tickets, Kalkulationen, Vorgänge, Events, Planung, Maschinen</br>
        ** Suche kann bei vielen Resultaten einige Sekunden dauern!
    </div>
</form>

<?php } ?>