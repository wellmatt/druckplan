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
require_once './libs/modules/organizer/nachricht.class.php';
require_once './libs/modules/warehouse/warehouse.class.php';

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
    <link rel="stylesheet" type="text/css" href="css/main.css">
    <title>Kundenportal</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <!-- jQuery -->
	<script type="text/javascript" src="../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
	<script type="text/javascript" src="../jscripts/jquery/local/jquery.ui.datepicker-de.js"></script>
	<link type="text/css" href="../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
	<!-- /jQuery -->
	
	<script language="javascript" src="../jscripts/basic.js"></script>
</head>

<body>
		<img src="images/back.jpg" id="background" border="0">

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
		
		<div id="maincontent">
		    <table cellpadding="0" cellspacing="0" border="0" width="100%" height="100%">
		    <tr  height="100">
		    	<td>&nbsp;</td>
		    	<td> 
		    		<br>
		    		<b><?=$_LANG->get("Firmendaten")?>:</b>
		    		<hr>
		    		<table width="100%">
			    		<colgroup>
					    	<col width="350">
					    	<col >
					    	<col width="250">
					    	<col width="250">
			    		</colgroup>
			    		<tr>
				    		<td><img src="./../images/icons/building-old.png" alt=""> <?=$busicon->getNameAsLine();?></td>
				    		<td> 
				    			<? if($busicon->getPhone() != "" && $busicon->getPhone() != NULL){ ?>
				    			<img src="./../images/icons/telephone.png" alt="TEL"> <?=$busicon->getPhone()?>
				    			<? } ?>  &ensp;
				    		</td>
				    		<td>&emsp;</td>
				    		<td align="right">
				    		<?	if ($busicon->getLoginexpire() != 0) { 
				    				echo '<b class="msg_error">'.$_LANG->get("Login g&uuml;ltig bis").": ".date("d.m.Y", $busicon->getLoginexpire()).'</b>';
				    			} ?>   &ensp;
				    		</td>
			    		</tr>
			    		<tr>
				  		  	<td>&emsp;&ensp; <? echo $busicon->getAddress1()." ".$busicon->getAddress2();?></td>
				  		  	<td>
				  		  	<? if($busicon->getFax() != "" && $busicon->getFax() != NULL){?>
				    			<img src="./../images/icons/telephone-fax.png" alt="FAX">  <?= $busicon->getFax()?>
				    		<? } ?>  &ensp;
				    		</td>
				    		<td>&emsp;</td>
				    		<td align="right"> 
				    			 <?=$_LANG->get('Login')." (".$login_type_str.")"?>  
				    		</td>
			  		  	</tr>
			  		  	<tr>
				  		  	<td>&emsp;&ensp; <? echo $busicon->getZip()." ".$busicon->getCity();?></td>
				  		  	<td><img src="./../images/icons/mail.png" alt="Mail"> <?= $busicon->getEmail()?></td>
				  		  	<td align="center">
				  		  		<?if($enabled_tickets == "on"){?>
				  		  		<a href="index.php?pid=20&exec=new"><img src="./../images/icons/ticket--plus.png" alt="">Neues Ticket</a>
				  		  		<? } ?>
				  		  	</td>
				  		  	<td align="right">
				  		  		<a href="index.php?exec=logout">
		    						<img src="../images/icons/door-open-out.png"> Abmelden
								</a>
				  		  	</td>
			  		  	</tr>
			   		</table>
					<br/>
		    	</td>
		    	<td>&nbsp;</td>
		    </tr>
		    <tr>
		        <td>&nbsp;</td>
		        <td id="innerFrame">
		            <div id="menuframe">
		                <ul class="menu">
		                    <li class="menu <?if($_REQUEST["pid"] == 1 || $_REQUEST["pid"] == "") echo "active";?>" onclick="location.href='index.php?pid=1'"
		                    	style="width:115px;">Meine Dateien</li>
		                    <li class="menu <?if($_REQUEST["pid"] == 2) echo "active";?>" onclick="location.href='index.php?pid=2'"
		                    	style="width:115px;">Neue Datei</li>
		                    <li class="menu <?if($_REQUEST["pid"] == 3) echo "active";?>" onclick="location.href='index.php?pid=3'"
		                    	style="width:115px;">Profil</li>
		                    <?if($enabled_tickets == "on"){?>
		                    	<li class="menu <?if($_REQUEST["pid"] == 20) echo "active";?>" onclick="location.href='index.php?pid=20'"
		                    	style="width:115px;">Tickets</li>
		                    <?}?>
		                    <?if($enabled_persos == "on"){?>
		                    	<li class="menu <?if($_REQUEST["pid"] == 40) echo "active";?>" onclick="location.href='index.php?pid=40'">Personalisierungen</li>
		                    <?}?>
		                    <?if($enabled_article == "on"){?>
		                    	<li class="menu <?if($_REQUEST["pid"] == 60) echo "active";?>" onclick="location.href='index.php?pid=60'"
		                    	style="width:115px;">Artikel</li>
		                    <?} ?>
							<?if($enabled_marketing == "on"){?>
								<li class="menu <?if($_REQUEST["pid"] == 100) echo "active";?>" onclick="location.href='index.php?pid=100'"
									style="width:115px;">Marketing</li>
							<?} ?>
		                    <li class="menu <?if($_REQUEST["pid"] == 90) echo "active";?>" onclick="location.href='index.php?pid=90'"
		                    	style="width:115px;">Historie</li>
		                </ul>
		            </div>
		            
		            <? 
		            switch($_REQUEST["pid"])
		            {
		                case 1: require_once('kunden/files.php'); break;
		                case 2: require_once('kunden/upload.php'); break;
		                case 3: require_once('kunden/customerdetails.php'); break;
		                case 20: require_once('kunden/modules/tickets/ticket.php'); break;
		                case 40: require_once('kunden/personalization.php'); break;
		                case 60: require_once('kunden/article.php'); break;
		                case 80: require_once('kunden/modules/shoppingbasket/shoppingbasket.php'); break;
		                case 90: require_once('kunden/orderhistory.php'); break;
						case 100: require_once('kunden/marketing.php'); break;
		                default: require_once 'kunden/files.php'; break;
		            }            
		            ?>
		        </td>
		        <td>&nbsp;</td>
		    </tr>
		    <tr height="100"><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
		    </table>
		</div>
	<? } 
	} ?>
</body>
</html>