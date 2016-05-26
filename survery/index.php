<?php // ---------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       04.06.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
set_time_limit(150);

// error_reporting(E_ERROR | E_WARNING | E_PARSE);

chdir('../');
require_once("./config.php");
require_once("./libs/basic/mysql.php");
require_once("./libs/basic/debug.php");
require_once("./libs/basic/globalFunctions.php");
require_once("./libs/basic/user/user.class.php");
require_once("./libs/basic/groups/group.class.php");
require_once("./libs/basic/clients/client.class.php");
require_once("./libs/basic/translator/translator.class.php");
require_once("./libs/basic/countries/country.class.php");
require_once("./libs/basic/license/license.class.php");
require_once("./vendor/autoload.php");
require_once 'libs/modules/timekeeping/timekeeper.class.php';
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/organizer/mail/mailModel.class.php';
require_once 'libs/modules/surverys/surverys.class.php';


$DB = new DBMysql();
$DB->connect($_CONFIG->db);
$_DEBUG = new Debug();
$_LICENSE = new License();

if (!$_LICENSE->isValid())
    die("No valid licensefile, please contact iPactor GmbH for further assistance");

// Handle uservalidation
$_USER = new User();

// $_MENU = new Menu();

// Logout, einfach die Sessiondaten killen
if ($_REQUEST["doLogout"] == 1)
{
    session_destroy();
    session_start();
    setcookie('vic_login', "", time());
    $_SESSION = Array();
    ?>
<script language="JavaScript">location.href='../index.php'</script>
<?
} else
{
    session_start();
     
    // Anmelden
    if (trim($_REQUEST["login_login"] != "") && trim($_REQUEST["login_password"]) != "")
    {
        $_SESSION["login"] = addslashes(trim($_REQUEST["login_login"]));
        $_SESSION["password"] = addslashes(trim($_REQUEST["login_password"]));
        $_SESSION["domain"] = (int)$_REQUEST["login_domain"];
    } else if ($_COOKIE["vic_login"] && $_COOKIE["vic_iv"])
    {
        $userstring = mcrypt_decrypt(MCRYPT_BLOWFISH, $_CONFIG->cookieSecret, $_COOKIE["vic_login"], MCRYPT_MODE_CBC, $_COOKIE["vic_iv"]);
        $userdata = explode(' ', $userstring);
        $_REQUEST["login_login"] = $userdata[0];
        $_REQUEST["login_password"] = $userdata[1];
        $_REQUEST["login_domain"] = $userdata[2];
    }
     

    $_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
}

if ($_USER == false)
{
    require_once('./libs/basic/user/login.php');
} else
{
    // Sprache laden
    $_LANG = $_USER->getLang();
    $_SESSION['userid'] = $_USER->getId();
?>
    <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <!-- <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1"> -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" type="text/css" href="../css/main.css" />
        <link rel="stylesheet" type="text/css" href="../css/menu.css" />
        <link rel="stylesheet" type="text/css" href="../css/main.print.css" media="print"/>
        
        <!-- jQuery -->
        <link type="text/css" href="../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
        <script type="text/javascript" src="../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
        <script type="text/javascript" src="../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
        <script language="JavaScript" src="../jscripts/jquery/local/jquery.ui.datepicker-<?=$_LANG->getCode()?>.js"></script>
        <script type="text/javascript" src="../jscripts/jquery.validate.min.js"></script>
        <!-- /jQuery -->
        <!-- FancyBox -->
        <script type="text/javascript" src="../jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
        <script type="text/javascript" src="../jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
        <link rel="stylesheet" type="text/css" href="../jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
        <!-- /FancyBox -->
        <script language="javascript" src="../jscripts/basic.js"></script>
        
<!--         <link type="text/css" href="/cometchat/cometchatcss.php" rel="stylesheet" charset="utf-8"> -->
<!--         <script type="text/javascript" src="/cometchat/cometchatjs.php" charset="utf-8"></script> -->
        
        <title>Druckplan - <?=$_USER->getClient()->getName()?></title>
    </head>
    <img src="../images/page/page-logo.png" alt="Kleindruck">
    </br>
    <table width="100%">
    	<tr>
    		<td width="200" class="content_header">
                <span class="glyphicons glyphicons-ipad"></span><span style="font-size: 13px"> <?=$_LANG->get('Frageb&ouml;gen')?> </span>
    		</td>
    		<td width="200"><?=$savemsg?></td>
    	</tr>
    </table>
<?
    $all_surverys = Surverys::AllSurveysForList();
    
    ?>
    
    <div class="box1">
    	<table id="art_table" width="100%" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th><?=$_LANG->get('ID')?></th>
                    <th><?=$_LANG->get('Namen')?></th>
                    <th><?=$_LANG->get('Beschreibung')?></th>
                </tr>
            </thead>
    		<?
    		foreach($all_surverys as $survery){
    			?>
    			<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
    				<td class="content_row pointer" align="center" onclick="window.open('survery.php?id=<?=$survery->getId()?>','_blank');"><?=$survery->getId()?></td>
    				<td class="content_row pointer" onclick="window.open('survery.php?id=<?=$survery->getId()?>','_blank');"><?=$survery->getName()?></td>
    				<td class="content_row pointer" onclick="window.open('survery.php?id=<?=$survery->getId()?>','_blank');"><?=$survery->getDescription()?></td>
    			</tr>
    			<?
    		}// Ende foreach
    		?>
    	</table>
    </div>
    
<?
}
?>