<?php
ob_start();
// ---------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       04.06.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
set_time_limit(150);

// error_reporting(E_ERROR | E_WARNING | E_PARSE);
error_reporting(-1);
ini_set('display_errors', 1);

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
require_once 'libs/modules/associations/association.class.php';


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
    
    $logouttimer = Timer::getLastUsed();
    if ($logouttimer->getState() != Timer::TIMER_STOP){
        $logouttimer->delete();
    }
    
    ?>
<script language="JavaScript">location.href='index.php'</script>
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
    if ($_USER){
        $logintimer = new Timer();
        $logintimer->setObjectid(0);
        $logintimer->setModule("Login");
        $now = time();
        $logintimer->setStarttime($now);
        $logintimer->setStoptime($now);
        $logintimer->save();
    }
    
}

if ($_USER == false)
{
    require_once('./libs/basic/user/login.php');
} else
{

    /* Logindaten merken?
     * Daten werden fï¿½r 365 Tage gemerkt
    */
    if ($_REQUEST["login_keeplogin"])
    {
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB), MCRYPT_RAND);
        $userstring = addslashes(trim($_REQUEST["login_login"]))." ".addslashes(trim($_REQUEST["login_password"]))." ".(int)$_REQUEST["login_domain"];
        $crypted = mcrypt_encrypt(MCRYPT_BLOWFISH, $_CONFIG->cookieSecret, $userstring, MCRYPT_MODE_CBC, $iv);
        setcookie('vic_login', $crypted, time()+60*60*24*365);
        setcookie('vic_iv', $iv, time()+60*60*24*365);
    }

    // save pid
    if ($_REQUEST["pid"] != "")
        $_SESSION["pid"] = $_REQUEST["pid"];
     
    // Sprache laden
    $_LANG = $_USER->getLang();
	$_SESSION['userid'] = $_USER->getId();
	
    if ($_REQUEST["user_time"])
    {
		$exec = $_REQUEST["user_time"];
		if ($exec == "checkin")
		{
			$time_now = time();
			$sql = "INSERT INTO user_times 
					(user_id, checkin)
					VALUES
					({$_USER->getId()}, {$time_now})";
			$res = $DB->no_result($sql);
			
			$sql = "SELECT max(id) id FROM user_times";
			$checkinid = $DB->select($sql);
			$checkinid = $checkinid[0]["id"];
			
			setcookie("checkin_id",$checkinid,time()+(3600*60*60*16));
			header("Location: index.php");
		}
		if ($exec == "pause")
		{
			if(isset($_COOKIE["checkin_id"]))
			{
				$time_now = time();
				$sql = "INSERT INTO user_times_pause 
						(user_times_id, start)
						VALUES
						({$_COOKIE["checkin_id"]}, {$time_now})";
				$res = $DB->no_result($sql);
				
				$sql = "SELECT max(id) id FROM user_times_pause";
				$pauseid = $DB->select($sql);
				$pauseid = $pauseid[0]["id"];
				
				setcookie("pause_id",$pauseid,time()+(3600*60*60*16));
				header("Location: index.php");
			}
		}
		if ($exec == "unpause")
		{
			$time_now = time();
			if(isset($_COOKIE["checkin_id"]) && isset($_COOKIE["pause_id"]))
			{
				$sql = "UPDATE user_times_pause SET  
						end = {$time_now} 
						WHERE id = {$_COOKIE["pause_id"]}";
				$res = $DB->no_result($sql);
				
				setcookie("pause_id","",time() - 3600);
				header("Location: index.php");
			}
		}
		if ($exec == "checkout")
		{
			if(isset($_COOKIE["checkin_id"]))
			{
				$time_now = time();
				$sql = "UPDATE user_times SET  
						checkout = {$time_now} 
						WHERE id = {$_COOKIE["checkin_id"]}";
				$res = $DB->no_result($sql);
			}
			
			setcookie("checkin_id",$checkinid,time() - 3600);
			setcookie("pause_id",$checkinid,time() - 3600);
			header("Location: index.php");
		}
	}
	
    /*******************************************************************/
    /* Print page header                                               */
    ?>

<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<!-- <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1"> -->
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" type="text/css" href="./css/main.css" />
<link rel="stylesheet" type="text/css" href="./css/ticket.css" />
<link rel="stylesheet" type="text/css" href="./css/menu.css" />
<link rel="stylesheet" type="text/css" href="./css/main.print.css" media="print"/>

<!-- jQuery -->
<link type="text/css" href="jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
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
<!-- /MegaNavbar -->

<title>Druckplan - <?=$_USER->getClient()->getName()?></title>
</head>
<body>
<? // TODO: Apassen für neuen Timer
if((int)$_SESSION["DP_Timekeeper"][$_USER->getId()]["timer_id"] != 0){
	$active_timer = new Timekeeper($_SESSION["DP_Timekeeper"][$_USER->getId()]["timer_id"]);
	echo '<div id="active_timer_ObjectID" style="display:none;">'.$active_timer->getObjectID().'</div>';
	echo '<div id="active_timer_ModuleID" style="display:none;">'.$active_timer->getModule().'</div>';
	$active_ticket = new Ticket($active_timer->getObjectID());
	echo '<div id="active_timer_Title" style="display:none;">'.$active_ticket->getTitle().'</div>';
}
?>
<div id="logged_user_ticket" style="display:none;"><?=(int)$_SESSION["DP_Timekeeper"][$_USER->getId()]["timer_id"]?></div>
<div id="hidden_clicker_index" style="display:none">
<a id="hiddenclicker_index" href="http://www.google.com" >Hidden Clicker</a>
</div>
<script language="JavaScript">
   // showLoading();
</script>
<script type="text/javascript">
	$(document).ready(function() {
		$("a#a_timer_stop_home").fancybox({
		    'type'    : 'iframe'
		})

		$("a#hiddenclicker_index").fancybox({
			'type'          : 'iframe',
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600, 
			'speedOut'		:	200, 
			'height'		:	800, 
			'width'         :   900,
			'overlayShow'	:	true,
			'helpers'		:   { overlay:null, closeClick:true },
			'scrolling'     :   'yes',
		    'autoScale'     :   true
		});
	});
	
	function callBoxFancyIndex(my_href) {
		var j1 = document.getElementById("hiddenclicker_index");
		j1.href = my_href;
		$('#hiddenclicker_index').trigger('click');
	}
</script>

<div id="idx_loadinghide" style="display:none">

<!--   <div class="container"> -->
    <nav class="navbar navbar-default" id="main_navbar" role="navigation">
      <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><img src="images/page/page-logo.png" alt="Kleindruck" width="100%" height="100%"></a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="navbar-collapse-1">

          <ul class="nav navbar-nav navbar-left">
            
            <!-- divider -->
            <li class="divider"></li>
          
            <? require_once('./libs/basic/menu/menu.php'); ?>
          


          </ul>
          <ul class="nav navbar-nav navbar-right">

            <!-- search form -->
            
            <script language="javascript">
                function setValues(submit){
            
                    var search = document.getElementById('mainsearch_string').value;
            
                	document.getElementById('hidden_oid').value=search;
                	document.getElementById('hidden_title').value=search;
                	document.getElementById('hidden_inv').value=search;
                	document.getElementById('hidden_cust').value=search;
            
                    if (submit = 1){
                        document.getElementById('mainsearch_form').submit();
                    }
                  	return true;
                }
            </script>
            
            <form class="navbar-form-expanded navbar-form navbar-left visible-lg-block visible-md-block visible-xs-block" role="search" 
            action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="mainsearch_form" id="mainsearch_form" onsubmit="return setvalues()">
              <div class="input-group">
            	<input type="hidden" name="pid" id="hidden_pid" value="0">
            	<input type="hidden" name="submit_search" id="hidden_sub" value="true">
            	<input type="hidden" name="header_search" id="hidden_hs" value="true">
            	<input type="hidden" name="search_orderId" id="hidden_oid" value ="">
            	<input type="hidden" name="search_orderTitle" id="hidden_title" value ="">
            	<input type="hidden" name="search_invoiceId" id="hidden_inv" value ="">
            	<input type="hidden" name="search_orderCustomer" id="hidden_cust" value ="">
                <input type="text" id="mainsearch_string" name="mainsearch_string"  class="form-control" data-width="80px" data-width-expanded="170px" 
                value="<?=$_REQUEST["mainsearch_string"] ?>" placeholder="Search..." name="query">
                <span class="input-group-btn"><button class="btn btn-default" type="submit"><i class="fa fa-search"></i>&nbsp;</button></span>
              </div>
            </form>
            <li class="dropdown-grid visible-sm-block">
              <a data-toggle="dropdown" href="javascript:;" class="dropdown-toggle"><i class="fa fa-search"></i> Search</a>
              <div class="dropdown-grid-wrapper" role="menu">
                <ul class="dropdown-menu col-sm-6">
                  <li>
                    <form class="no-margin">
				        <div class="input-group">
					        <input type="text" class="form-control">
					        <span class="input-group-btn"><button class="btn btn-default" type="button">&nbsp;<i class="fa fa-search"></i></button></span>
				        </div>
			        </form>
                  </li>
                </ul>
              </div>
            </li>


            <!-- divider -->
            <li class="divider"></li>
                        
                        
            <!-- calendar -->
            <li class="dropdown-grid">
              <a data-toggle="dropdown" href="javascript:;" class="dropdown-toggle" onclick="sleep(500, updateNotifications);"><i class="fa fa-newspaper-o"></i>&nbsp;<span class="hidden-sm">Benachrichtigungen</span><span id="notify_count"></span><span class="caret"></span></a>
              <div class="dropdown-grid-wrapper" role="menu">
                <ul class="dropdown-menu col-xs-12 col-sm-10 col-md-8 col-lg-4"> 
                  <li>
                      <h3 class="text-right" style="padding-top:0px; border-bottom: 1px solid #555;"><i class="fa fa-newspaper-o"></i> Benachrichtigungen</h3>
							<? 
								require_once 'libs/modules/notifications/notification.frame.php'; 
							?>
                  
                  </li>
                </ul>
              </div>
            </li>
            <!-- /calendar -->
            
            
            <!-- divider -->
            <li class="divider"></li>
                        
                        
            <!-- calendar -->
            <li class="dropdown-grid">
              <a data-toggle="dropdown" href="javascript:;" class="dropdown-toggle" onclick="sleep(500, cal_refresh);"><i class="fa fa-calendar"></i>&nbsp;<span class="hidden-sm">Kalender</span><span class="caret"></span></a>
              <div class="dropdown-grid-wrapper" role="menu">
                <ul class="dropdown-menu col-xs-12 col-sm-10 col-md-8 col-lg-7">
                  <li>
                      <h3 class="text-right" style="padding-top:0px; border-bottom: 1px solid #555;"><i class="fa fa-calendar"></i> Kalender</h3>
							<? 
								require_once 'libs/modules/organizer/calendar.showday.home.php'; 
							?>
                  
                  </li>
                </ul>
              </div>
            </li>
            <!-- /calendar -->

            <!-- divider -->
            <li class="divider"></li>
                        
                        
            <!-- mails -->
            <li class="dropdown-grid">
              <a data-toggle="dropdown" href="javascript:;" class="dropdown-toggle"><i class="fa fa-inbox"></i>&nbsp;<span class="hidden-sm">Mails</span><span class="caret"></span></a>
              <div class="dropdown-grid-wrapper" role="menu">
                <ul class="dropdown-menu col-xs-12 col-sm-10 col-md-8 col-lg-7">
                  <li>
                    
                  
							<? 
								require_once 'libs/modules/organizer/nachrichten.showmails.home.php'; 
							?>
                  
                  </li>
                </ul>
              </div>
            </li>
            <!-- /mails -->

            <!-- divider -->
            <li class="divider"></li>
                        
                        
            <!-- account -->
            <li class="dropdown-grid">
              <a data-toggle="dropdown" href="javascript:;" class="dropdown-toggle"><i class="fa fa-lock"></i>&nbsp;<span class="hidden-sm">Account</span><span class="caret"></span></a>
              <div class="dropdown-grid-wrapper" role="menu">
                <ul class="dropdown-menu col-xs-12 col-sm-10 col-md-8 col-lg-4">
                  <li>
                      <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="item active">
                              <h3 class="text-right" style="padding-top:0px; border-bottom: 1px solid #555;"><i class="fa fa-lock"></i> Angemeldet als <?=$_USER->getFirstname()?> <?=$_USER->getLastname()?></h3>
                              <br>
                              <form class="form-horizontal" role="form">
                                <div class="form-group" style='margin-left: 200px; margin-right: -120px;'>
                                  <label class="col-sm-3 control-label">Posteingang</label>
                                  <div class="col-sm-6">
                                        <?
                                        $totalCount = 0;
                                        foreach($_USER->getEmailAddresses() as $emailAddress) {
                                        
                                        // 	Create a new MailModel instance.										// TODO: Wieder einkommentieren
                                        	$mailModel = new MailModel($emailAddress, "INBOX");
                                        	$totalCount +=$mailModel->getAccount()->countMessages(); 
                                        }
                                        	echo '<label class="col-sm-12 control-label">'.$totalCount.'</label>';
                                        ?>
                                  </div>
                                </div>
                                <div class="form-group" style='margin-left: 200px; margin-right: -120px;'>
                                  <label class="col-sm-3 control-label">Checkin</label>
                                  <div class="col-sm-6">
                    					<? if (!isset($_COOKIE["checkin_id"])) { echo '<label class="col-sm-12 control-label"><a href="index.php?user_time=checkin">Kommen</a></label> '; } ?>
                    					<? if (!isset($_COOKIE["pause_id"]) && isset($_COOKIE["checkin_id"])) { echo '<label class="col-sm-12 control-label"><a href="index.php?user_time=pause">Pause</a></label>'; } 
                    						else { if (isset($_COOKIE["checkin_id"])) { echo '<label class="col-sm-12 control-label"><a href="index.php?user_time=unpause">Pause beenden</a></label> '; } } ?>
                    					<? if (isset($_COOKIE["checkin_id"])) { echo '<label class="col-sm-12 control-label"><a href="index.php?user_time=checkout">Gehen</a></label>'; } ?>
                                  </div>
                                </div>
                                <div class="form-group" style='margin-left: 200px; margin-right: -120px;'>
                                  <label class="col-sm-3 control-label">N&uuml;tzliche Links</label>
                                  <div class="col-sm-6">
                                        <label class="col-sm-12 control-label">
                                            <a href="JavaScript: callBoxFancyIndex('http://dev.mein-druckplan.de/changelog.php');">Changelog</a></br>
			                                <a href="JavaScript: callBoxFancyIndex('http://support.mein-druckplan.de/open.php');">Support</a></br>
			                                <a href="JavaScript: document.location='index.php?doLogout=1';">Logout</a></br>
                                        </label>
                                  </div>
                                </div>
                               </form>
                            </div>
                      </div>
                     </div>
                  </li>
                </ul>
              </div>
            </li>
            <!-- /account -->
            
            
            
            
          </ul>
        </div>
      </div>
    </nav>
<!--   </div> -->

<div class="content">
<!-- 	<div class="container"> -->
<!-- 	   <center> -->
		<?
		if ($_REQUEST['page']) {
			require_once($_REQUEST['page']);
		} else {
			require_once('./libs/basic/home.php');
		}
		?>
<!--        </center> -->
<!-- 	</div> -->
</div>

<script language="javascript">
// hideLoading();
unhideWindow();
</script>
<script>
  //Start Fix MegaNavbar on scroll page
  var navHeight = $('#main_navbar').offset().top;
  FixMegaNavbar(navHeight);
  $(window).bind('scroll', function() {FixMegaNavbar(navHeight);});

  function FixMegaNavbar(navHeight) {
      if (!$('#main_navbar').hasClass('navbar-fixed-bottom')) {
          if ($(window).scrollTop() > navHeight) {
              $('#main_navbar').addClass('navbar-fixed-top')
              $('body').css({'margin-top': $('#main_navbar').height()+'px'});
              if ($('#main_navbar').parent('div').hasClass('container')) $('#main_navbar').children('div').addClass('container').removeClass('container-fluid');
              else if ($('#main_navbar').parent('div').hasClass('container-fluid')) $('#main_navbar').children('div').addClass('container-fluid').removeClass('container');
          }
          else {
              $('#main_navbar').removeClass('navbar-fixed-top');
              $('#main_navbar').children('div').addClass('container-fluid').removeClass('container');
              $('body').css({'margin-top': ''});
          }
      }
  }
  //Start Fix MegaNavbar on scroll page

  //Next code used to prevent unexpected menu close when using some components (like accordion, tabs, forms, etc), please add the next JavaScript to your page
  $( window ).load(function() {
      $(document).on('click', '.navbar .dropdown-menu', function(e) {e.stopPropagation();});
  });

</script>
</body>
</html>

<? } ?>