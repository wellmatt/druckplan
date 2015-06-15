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


if($_REQUEST["exec"] == "save" && $_REQUEST["module"] && $_REQUEST["objectid"])
{

    echo '<script language="JavaScript">parent.$.fancybox.close(); window.opener.location.href = window.opener.location.href;</script>';
}

$module = $_REQUEST["module"];
$objectid = $_REQUEST["objectid"];
    
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<link rel="stylesheet" type="text/css" href="../../../css/main.print.css" media="print"/>


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
});
</script>

<form action="association.frame.php" method="post" name="association_form">
<input type="hidden" name="exec" value="save">
<input type="hidden" name="module" value="<?php echo $_REQUEST["module"];?>">
<input type="hidden" name="objectid" value="<?php echo $_REQUEST["objectid"];?>">
<table width="100%">
    <tr>
        <td width="300" class="content_header">
            <h1><img src="../../../images/icons/node-select.png"> <?php echo 'Neue Verknüpfung';?></h1>
        </td>
    </tr>
</table>

<input type="submit" value="<?php echo $_LANG->get('Speichern');?>" class="text">

<div class="box1"> 
		<table id="association_table" width="500">
    		<tr>
    		    <td class="content_header"><?php echo $_LANG->get('Ticket');?></td>
    			<td class="content_row_clear">
    			     <input type="text" id="ticket" name="ticket" value="" style="width:160px"/>
                     <input type="hidden" id="ticket_id" name="ticket_id" value=""/>
                </td>
    		</tr>
		</table>
		<br>
</div>
<br>
</form>