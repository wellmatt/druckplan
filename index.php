<?php
ob_start();
// ---------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       04.06.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

if (strstr(__DIR__, "contilas2"))
{
    error_reporting(-1);
    ini_set('display_errors', 1);
}

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
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/associations/association.class.php';
require_once 'libs/modules/timer/timer.class.php';
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';
require_once "thirdparty/phpfastcache/phpfastcache.php";
require_once 'libs/basic/cachehandler/cachehandler.class.php';
require_once 'libs/modules/api/api.class.php';
require_once 'libs/basic/quickmove.class.php';
require_once 'libs/modules/dashboard/dashboard.class.php';
require_once 'libs/basic/eventqueue/eventqueue.class.php';
require_once 'libs/basic/files/file.class.php';
require_once 'libs/modules/organizer/caldav.event.class.php';
require_once 'libs/modules/taxkeys/taxkey.class.php';
require_once 'libs/modules/costobjects/costobject.class.php';
require_once 'libs/modules/revenueaccounts/revenueaccount.class.php';
require_once 'libs/modules/accounting/receipt.class.php';
require_once 'libs/modules/textblocks/textblock.class.php';

// Mail Stuff
require_once 'libs/modules/mail/mailmassage.class.php';
require_once 'vendor/PEAR/Net/SMTP.php';
require_once 'vendor/PEAR/Net/Socket.php';
require_once 'vendor/Horde/Autoloader.php';
require_once 'vendor/Horde/Autoloader/ClassPathMapper.php';
require_once 'vendor/Horde/Autoloader/ClassPathMapper/Default.php';
$autoloader = new Horde_Autoloader();
$autoloader->addClassPathMapper(new Horde_Autoloader_ClassPathMapper_Default('vendor'));
$autoloader->registerAutoloader();
// End Mail Stuff

$DB = new DBMysql();
$dbok = $DB->connect($_CONFIG->db);
if (!$dbok)
    die("Fehler beim Verbinden mit der Datenbank");
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
    
    $logouttimer = Timer::getLastUsed((int)$_REQUEST["userid"]);
    
    ?>
    <script language="JavaScript">location.href='index.php'</script>
    <?php die(); ?>
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
        $_SESSION["ipadress"] = $_SERVER['REMOTE_ADDR'];
        $_SESSION["useragent"] = $_SERVER['HTTP_USER_AGENT'];
        $_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
        // save user agent + ip to database
        if ($_USER){
            $_USER->setLoginip($_SESSION['ipadress']);
            $_USER->setLoginagent($_SESSION['useragent']);
            $_USER->save();
        }
    } else
    {
        $_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
        // Logout User if there was another login from different IP or agent
        if ($_USER){
            if ($_USER->getLoginip() != $_SESSION['ipadress'] || $_USER->getLoginagent() != $_SESSION["useragent"]){
                session_destroy();
                session_start();
                setcookie('vic_login', "", time());
                $_SESSION = Array();
                ?>
                <script language="JavaScript">
                    alert('Ihre Sitzung wurde aufgrund eines zweiten Logins beendet.');
                    location.href='index.php'
                </script>
                <?php
            }
        }
    }
}

if ($_USER == false)
{
    require_once('./libs/basic/user/login.php');
} else
{

//    if ($_USER->getLogin() != "ascherer")
//        die('contilas2 ist im Wartungsmodus (22.06.2017 by A.Scherer)');
     
    // Sprache laden
    $_LANG = $_USER->getLang();
	$_SESSION['userid'] = $_USER->getId();
	
	$perf = new Perferences();

	$_CACHE = phpFastCache("memcached");
	
	
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

<?php
if ($_REQUEST["pagetitle"]){
    $_SESSION["pagetitle"] = "Contilas > ".$_REQUEST["pagetitle"];
}
if (!$_SESSION["pagetitle"] || $_SESSION["pagetitle"] == null){
    $_SESSION["pagetitle"] = "Contilas > ".$_USER->getClient()->getName();
}
?>
<title><?php echo $_SESSION["pagetitle"];?></title>
</head>
<body>
<div id="active_timer_ObjectID" style="display:none;">0</div>
<div id="active_timer_ModuleID" style="display:none;"></div>
<div id="active_timer_Title" style="display:none;"></div>
<div id="logged_user_ticket" style="display:none;">0</div>
<div id="hidden_clicker_index" style="display:none">
<a id="hiddenclicker_index" href="http://www.google.com" >Hidden Clicker</a>
</div>
<script language="JavaScript">
   // showLoading();
function sleep(millis, callback) {
    setTimeout(function()
            { callback(); }
    , millis);
}
</script>
<script type="text/javascript">
	$(document).ready(function() {
		$("a[href='#top']").click(function() {
			  $("html, body").animate({ scrollTop: 0 }, "slow");
			  return false;
		});
		
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

<a name="top"></a> 
<div id="idx_loadinghide" style="display:none">

<!--   <div class="container"> -->
    <nav class="navbar navbar-default" id="main_navbar" role="navigation">
      <div class="container-fluid" style="padding-right: 0px;padding-left: 0px;">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-collapse-1">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><img src="images/page_logo.jpg" alt="Contilas" height="50"></a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="navbar-collapse-1">

          <ul class="nav navbar-nav navbar-left">
            
            
            <!-- divider -->
            <li class="divider"></li>
            
            <!-- Ticketliste -->
            <li>
              <a href="index.php?page=libs/modules/tickets/ticket.php&pagetitle=Tickets"><i class="fa"></i>&nbsp;<span class="hidden-sm">Ticketliste</span></a>
            </li>
            <!-- /Ticketliste -->
          
            <? require_once('./libs/basic/menu/menu.php'); ?>
          


          </ul>
          <ul class="nav navbar-nav navbar-right">

            <!-- search form -->
            
            <script language="javascript">
                function checkSearch(){
                    var search = document.getElementById('mainsearch_string').value;
                    if (search.length > 0){
                        document.getElementById('mainsearch_form').submit();
                      	return true;
                    }
                }
            </script>
            
            <form class="navbar-form-expanded navbar-form navbar-left visible-lg-block visible-md-block visible-xs-block" role="search" 
            action="index.php?page=libs/modules/search/search.php&pagetitle=Suche" method="post" name="mainsearch_form" id="mainsearch_form" onsubmit="return checkSearch()">
              <div class="input-group">
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

            <?php 
            if(Timer::getLastUsed()->getId() > 0){
                $active_timer = Timer::getLastUsed();
                if ($active_timer->getState() == Timer::TIMER_RUNNING){
                    $tmp_ticket_home = new Ticket($active_timer->getObjectid());
                    $timer_start_home = $active_timer->getStarttime();
                    ?>
                        <!-- divider -->
                        <li class="divider"></li>
                        <li style="padding-bottom:10px;padding-top:10px;display:block;position:relative;">
                          <a href="index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid=<?=$tmp_ticket_home->getId()?>&pagetitle=Ticket" style="padding-bottom:0px;padding-top:5px;display:block;position:relative;">
                            <span id="ticket_timer_home" class="timer duration btn btn-warning" data-duration="0" style="padding-bottom:0px;padding-top:0px;display:block;position:relative;"></span>
                          </a>
		                  <input id="ticket_timer_timestamp_home" name="ticket_timer_timestamp_home" type="hidden" value="<?php echo $timer_start_home;?>"/>
                        </li>
                        
                        <script>
                            $(document).ready(function () {
                            	var clock_home;
                            	var sec_home = moment().unix();
                            	var start_home = parseInt($('#ticket_timer_timestamp_home').val());
                            	if (start_home != 0){
                            		clock_home = setInterval(stopWatch_home,1000);
                            	}
                                function stopWatch_home() {
                                	sec_home++;
                                	var timestamp = sec_home-start_home;
                                	$("#ticket_timer_home").html(rectime(timestamp));
                                }
                                function rectime(secs) {
                                	var hr = Math.floor(secs / 3600);
                                	var min = Math.floor((secs - (hr * 3600))/60);
                                	var sec = Math.floor(secs - (hr * 3600) - (min * 60));
                                	
                                	if (hr < 10) {hr = "0" + hr; }
                                	if (min < 10) {min = "0" + min;}
                                	if (sec < 10) {sec = "0" + sec;}
//                                 	if (hr) {hr = "00";}
                                	return hr + ':' + min + ':' + sec;
                                }
                                function precise_round(num, decimals) {
                                	var t=Math.pow(10, decimals);   
                             	    return (Math.round((num * t) + (decimals>0?1:0)*(Math.sign(num) * (10 / Math.pow(100, decimals)))) / t).toFixed(decimals);
                               	}
                            });
                            </script>
                    <?php
                }
            }
            ?>


              <!-- divider -->
              <li class="divider"></li>

              <!-- calendar -->
              <li class="dropdown-grid">
                  <a data-toggle="dropdown" href="javascript:;" style="padding-bottom: 6px;padding-top: 8px;">
                      <span class="glyphicons glyphicons-link" style="font-size: 24px;" title="Links"></span>
                  </a>
                  <div class="dropdown-grid-wrapper" role="menu">
                      <ul class="dropdown-menu col-xs-6 col-sm-5 col-md-4 col-lg-2">
                          <li>
                              <h4 class="text-right" style="padding-top:0px; border-bottom: 1px solid #555;padding-bottom: 0px;"><i class="fa fa-newspaper-o"></i> Links</h4>
                              <?
                              require_once 'libs/modules/links/link.frame.php';
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
              <a data-toggle="dropdown" href="javascript:;" class="dropdown-toggle" onclick="sleep(500, updateNotifications);" style="padding-bottom: 6px;padding-top: 8px;">
                  <span class="glyphicons glyphicons-exclamation-sign" style="font-size: 24px;" title="Benachrichtigungen"></span>
                  <span id="notify_count" class="badge"></span>
              </a>
              <div class="dropdown-grid-wrapper" role="menu">
                <ul class="dropdown-menu col-xs-12 col-sm-10 col-md-8 col-lg-4"> 
                  <li>
                      <h4 class="text-right" style="padding-top:0px; border-bottom: 1px solid #555;padding-bottom: 0px;"><i class="fa fa-newspaper-o"></i> Benachrichtigungen</h4>
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
            <li id='li_calendar' class="dropdown-grid">
              <a href="index.php?page=libs/modules/organizer/calendar.php&pagetitle=Kalender" style="padding-bottom: 6px;padding-top: 8px;">
                  <span class="glyphicons glyphicons-calendar" style="font-size: 24px;" title="Kalender"></span>
              </a>
<!--                <a href="index.php?page=libs/modules/organizer/davcalendar.php&pagetitle=Kalender" style="padding-bottom: 6px;padding-top: 8px;">-->
<!--                    <span class="glyphicons glyphicons-calendar" style="font-size: 24px;" title="Kalender"></span>-->
<!--                </a>-->
            </li>
            <!-- /calendar -->

              <!-- divider -->
              <li class="divider"></li>
                        
            <!-- mails -->
            <li>
                <script type="text/javascript">
                    $(document).ready(function() {
                        refreshMailCount();
                        setInterval( refreshMailCount, 2*60*1000 );
                    });
                    function refreshMailCount() {
                        $.ajax({
                            url: "libs/modules/mail/mail.ajax.php?exec=getNewCount",
                            type: "GET",
                            dataType: "html",
                            success: function (data) {
                                if (parseInt(data) > 0)
                                    $('#nav_mail_count').html(data);
                            }
                        });
                    }
                </script>
                <a href="index.php?page=libs/modules/mail/mail.overview.php&pagetitle=Mail" style="padding-bottom: 6px;padding-top: 8px;">
                    <span class="glyphicons glyphicons-envelope" style="font-size: 24px;" title="E-Mails"></span><span id="nav_mail_count" class="badge"></span>
                </a>
            </li>
            <!-- /mails -->

              <!-- divider -->
              <li class="divider"></li>


            <!-- account -->
            <li class="dropdown-grid user-menu">
              <a data-toggle="dropdown" href="javascript:;" class="dropdown-toggle" style="padding-top: 7.5px;padding-bottom: 7.5px;">
                  <img alt="User Image" class="user-image" src="libs/basic/user/user.avatar.get.php?uid=<?php echo $_USER->getId();?>">
              </a>
              <div class="dropdown-grid-wrapper" role="menu">
                <ul class="dropdown-menu col-xs-12 col-sm-10 col-md-8 col-lg-4" style="border-top-left-radius: 0;border-top-right-radius: 0;border-top-width: 0;padding: 1px 0 0;width: 280px;">
                  <li>
                      <!-- The user image in the menu -->
                      <li class="user-header">
                          <img alt="User Image" class="img-circle" src="libs/basic/user/user.avatar.get.php?uid=<?php echo $_USER->getId();?>">
                          <p>
                              <?php echo $_USER->getNameAsLine();?>
                              <br><small><?php if($_USER->isAdmin()) echo 'Administrator'; else echo 'Benutzer';?></small>
                          </p>
                      </li>
                      <!-- Menu Footer-->
                      <li class="user-footer">
                          <div class="col-xs-4 text-center">
                              <a href="http://kb.mein-druckplan.de" target="_blank" class="btn btn-default btn-flat btn-xs">FAQ</a>
                          </div>
                          <div style="" class="col-xs-4 text-center">
                              <a href="mailto:support@contilas.de?Subject=Support" class="btn btn-default btn-flat btn-xs">Support</a>
                          </div>
                          <div class="col-xs-4 text-center">
                              <?php
                              if(Timer::getLastUsed()->getId() > 0){
                                  $active_timer = Timer::getLastUsed();
                                  if ($active_timer->getState() == Timer::TIMER_RUNNING){
                                      if ((time()-300) > $active_timer->getStarttime()){
                                          echo 'Logout (Timer läuft!)</br>';
                                      } else {
                                          echo '<a href="JavaScript: document.location=\'index.php?doLogout=1&userid='.$_USER->getId().'\';" class="btn btn-default btn-flat btn-xs">Logout</a></br>';
                                      }
                                  } else {
                                      echo '<a href="JavaScript: document.location=\'index.php?doLogout=1&userid='.$_USER->getId().'\';" class="btn btn-default btn-flat btn-xs">Logout</a></br>';
                                  }
                              } else {
                                  echo '<a href="JavaScript: document.location=\'index.php?doLogout=1&userid='.$_USER->getId().'\';" class="btn btn-default btn-flat btn-xs">Logout</a></br>';
                              }
                              ?>
                          </div>
                      </li>
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

<div class="content" style="border: none;"> <!-- content -->
<!-- 	<div class="container"> -->
		<?
		if ($_REQUEST['page']) {
			require_once($_REQUEST['page']);
		} else {
            if ($_USER->getHomepage() != '')
            {
                require_once('./'.$_USER->getHomepage());
            } else {
                require_once('./libs/basic/home.php');
            }
		}
		?>
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