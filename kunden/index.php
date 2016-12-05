<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       13.09.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

chdir("..");
// error_reporting(-1);
// ini_set('display_errors', 1);
//----------------------------------------------------------------------------------
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
require_once("./libs/modules/businesscontact/businesscontact.class.php");
require_once('./libs/modules/businesscontact/contactperson.class.php');
require_once './libs/modules/businesscontact/address.class.php';
require_once './libs/modules/article/article.class.php';
require_once('./kunden/modules/shoppingbasket/shoppingbasket.class.php');
require_once './libs/modules/warehouse/warehouse.class.php';
require_once './libs/modules/organizer/nachricht.class.php';
require_once './libs/modules/storage/storage.area.class.php';
require_once './libs/modules/storage/storage.position.class.php';

//----------------------------------------------------------------------------------
// TODO anpassen
// error_reporting($_CONFIG[$_CONFIG["_MODUS"]]["ERROR_REPORTING"]);

//----------------------------------------------------------------------------------
session_start();

//----------------------------------------------------------------------------------
$DB = new DBMysql();
$DB->connect($_CONFIG->db);
$_DEBUG = new Debug();
$_LICENSE = new License();
$_LANG = new Translator(1, true);

if (!$_LICENSE->isValid())
    die("No valid licensefile, please contact iPactor GmbH for further assistance");

//----------------------------------------------------------------------------------
if($_REQUEST["pid"] == "" && $_REQUEST["exec"] == "logout")
{
    session_destroy();
    session_start();
    $_SESSION = Array();
}

//----------------------------------------------------------------------------------
header ('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT');
header ('Expires: '.gmdate("D, d M Y H:i:s").' GMT');
header ('Cache-Control: no-cache, must-revalidate');
header ('Pragma: no-cache');

// alte Globale Variable -> auf Dauer ueberall durch $_BUSINESSCONTACT ersetzen
$busicon = new BusinessContact((int)$_SESSION["cust_id"]);

// globale Variablen fuer das gesamte Kunden-Portal
// $_USER = NULL;
$_BUSINESSCONTACT = new BusinessContact((int)$_SESSION["cust_id"]);
$_CONTACTPERSON = new ContactPerson((int)$_SESSION["contactperson_id"]);

// Sprache laden
$_LANG = new Translator(22);

// Buttons-Anzeigen oder nicht fuer Tickets, Personalisierungen, Artikel
$enabled_tickets = "off";
$enabled_persos = "off";
$enabled_article = "off";


if ($_SESSION["login_type"] == "businesscontact"){
	// Login erfolgte ueber die Daten des Geschaeftskontakts
	if($_BUSINESSCONTACT->getTicketenabled() == 1){
		$enabled_tickets = "on";
	}
	if($_BUSINESSCONTACT->getPersonalizationenabled() == 1){
		$enabled_persos = "on";
	}
	if($_BUSINESSCONTACT->getArticleenabled() == 1){
		$enabled_article = "on";
	}
	$login_type_str = $_BUSINESSCONTACT->getNameAsLine();
} else {
	//Login erfolgte ueber einen Ansprechpartner
	if($_CONTACTPERSON->getEnabledArtikel() == 1){
		$enabled_article = "on";
	}
	if($_CONTACTPERSON->getEnabledPersonalization() == 1){
		$enabled_persos = "on";
	}
	if($_CONTACTPERSON->getEnabledTickets() == 1){
		$enabled_tickets = "on";
	}
	if($_CONTACTPERSON->getEnabledMarketing() == 1){
		$enabled_marketing = "on";
	}
	$login_type_str = $_CONTACTPERSON->getNameAsLine3();
}

?>

<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<!--    <link rel="stylesheet" type="text/css" href="css/main.css">-->
    <title>Kundenportal</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <!-- jQuery -->
	<script type="text/javascript" src="../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
	<script type="text/javascript" src="../jscripts/jquery/local/jquery.ui.datepicker-de.js"></script>
	<link type="text/css" href="../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
	<!-- /jQuery -->
	<!-- Bootstrap -->
	<link href="../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
	<link href="../thirdparty/MegaNavbar/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
	<script type="text/javascript" src="../thirdparty/MegaNavbar/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	<!-- /Bootstrap -->
	<link rel="stylesheet" type="text/css" href="../css/glyphicons-bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="../css/glyphicons.css" />
	<link rel="stylesheet" type="text/css" href="../css/glyphicons-halflings.css" />
	<link rel="stylesheet" type="text/css" href="../css/glyphicons-filetypes.css" />
	<link rel="stylesheet" type="text/css" href="../css/glyphicons-social.css" />
	<link rel="stylesheet" type="text/css" href="../css/main.css" />
	
	<script language="javascript" src="../jscripts/basic.js"></script>
</head>

<body>

<? 
if ($_REQUEST["exec"] == "register_tmp"){
	require_once('kunden/register_tmp.php');
} else {
	if(!$_SESSION["cust_logontime"])
	    require_once('kunden/login.php');
	else {	
	
	    Global $_USER;
	    if ($_BUSINESSCONTACT->getSupervisor()){
	        $_USER = new User($_BUSINESSCONTACT->getSupervisor()->getId());
	    }
	    ?>
		<div id="logout">
		&emsp;
		</div>
		

		<!-- Fixed navbar -->
		<nav class="navbar navbar-default navbar-fixed-top">
			<div class="container">
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				</div>
				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav navbar-nav">
						<li class="<?if($_REQUEST["pid"] == 3 || $_REQUEST["pid"] == "") echo "active";?>"><a href="index.php?pid=3">Profil</a></li>
						<li class="<?if($_REQUEST["pid"] == 2) echo "active";?>"><a href="index.php?pid=2">Upload</a></li>
						<li class="<?if($_REQUEST["pid"] == 1) echo "active";?>"><a href="index.php?pid=1">Dateien</a></li>
						<?if($enabled_tickets == "on"){?>
							<li class="<?if($_REQUEST["pid"] == 20) echo "active";?>"><a href="index.php?pid=20">Tickets</a></li>
						<?}?>
						<?if($enabled_persos == "on"){?>
							<li class="<?if($_REQUEST["pid"] == 40) echo "active";?>"><a href="index.php?pid=40">Personalisierungen</a></li>
						<?}?>
						<?if($enabled_article == "on"){?>
							<li class="<?if($_REQUEST["pid"] == 60 || $_REQUEST["pid"] == 61) echo "active";?>"><a href="index.php?pid=60">Artikel</a></li>
						<?}?>
						<?if($enabled_marketing == "on"){?>
							<li class="<?if($_REQUEST["pid"] == 100) echo "active";?>"><a href="index.php?pid=100">Marketing</a></li>
						<?}?>
						<li class="<?if($_REQUEST["pid"] == 90) echo "active";?>"><a href="index.php?pid=90">Historie</a></li>
					</ul>
					<ul class="nav navbar-nav navbar-right">
						<!-- shoppingcart -->
						<?php
						$shopping_basket = new Shoppingbasket();
						$shopping_basket_entrys = Array ();

						if ($_SESSION["shopping_basket"]){
							$shopping_basket = $_SESSION["shopping_basket"];
							$shopping_basket_entrys = $shopping_basket->getEntrys();
						}
						?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
								<span class="glyphicon glyphicon-shopping-cart" aria-hidden="true"></span>
								<span class="badge"><?php echo count($shopping_basket_entrys);?></span>
								<span class="caret">
							</a>
							<ul class="dropdown-menu" style="margin: 0 0 0; padding: 0; border: none;">
								<li>
									<div class="row">
										<div class="col-lg-12 col-md-12 col-sm-12">
											<div class="item active">
												<? // Warenkorb laden
												require_once 'kunden/modules/shoppingbasket/shopping_sidebar.php';?>
											</div>
										</div>
									</div>
								</li>
							</ul>
						</li>
						<!-- /shoppingcart -->
						<?if($enabled_tickets == "on"){?>
							<li><a href="index.php?pid=20&exec=new">Neues Ticket</a></li>
						<? } ?>
						<!-- glyphicon-shopping-cart -->
						<li><a href="index.php?exec=logout">Logout (<?php echo $_CONTACTPERSON->getNameAsLine();?>)</a></li>
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</nav>

		<div class="container" style="padding-top: 60px;">
			<?
			switch($_REQUEST["pid"])
			{
				case 1: require_once('kunden/files.php'); break;
				case 2: require_once('kunden/upload.php'); break;
				case 3: require_once('kunden/customerdetails.php'); break;
				case 20: require_once('kunden/modules/tickets/ticket.php'); break;
				case 40: require_once('kunden/personalization.php'); break;
				case 60: require_once('kunden/modules/article/article.php'); break;
				case 61: require_once('kunden/modules/article/article.detail.php'); break;
				case 80: require_once('kunden/modules/shoppingbasket/shoppingbasket.php'); break;
				case 90: require_once('kunden/orderhistory.php'); break;
				case 100: require_once('kunden/marketing.php'); break;
				default: require_once 'kunden/customerdetails.php'; break;
			}
			?>
		</div> <!-- /container -->



	<? } 
	} ?>
</body>
</html>