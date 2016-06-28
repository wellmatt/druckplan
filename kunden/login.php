<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			17.05.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//-----------------------------------------------------------------------------------
global $DB;

if($_REQUEST["exec"] == "login"){
	
	$username = $_REQUEST["user"];
	$pass = $_REQUEST["password"];
	
    // select userdata for submitted credentials
    $sql = "SELECT *
            FROM businesscontact
            WHERE
            shop_login  = '{$username}' and
            shop_pass   = '{$pass}' and
            active = 1";
    $usrlogin = $DB->select($sql);
    $usrlogin = new BusinessContact($usrlogin[0]["id"]);
    
    $crttime = time();

    //----------------------------------------------------------------------------------
    // Falls ein Gesch.-Kontakt einen validen Login hat -> in die Session schreiben ...
    //----------------------------------------------------------------------------------
    if($usrlogin->getId() != 0){
    	
        if ($usrlogin->getLoginexpire() > $crttime || $usrlogin->getLoginexpire() == 0){
        	// register user data
        	$_SESSION["businesscontact"]	= $usrlogin;
        	$_SESSION["cust_id"]			= $usrlogin->getId();
        	$_SESSION["cust_firstname"]   	= $usrlogin->getName1();
        	$_SESSION["cust_lastname"]    	= $usrlogin->getName2();
        	$_SESSION["cust_logontime"]   	= time();
        	$_SESSION["login_type"]			= "businesscontact";
        	
	        // ...und zur Index.php weiterleiten
	        ?>
	        <script language="JavaScript">
	        	location.href = 'index.php';
	        </script> <?
        } else {
			$loginmsg = " Login nicht mehr aktiv";	
		}
    } else {
		//----------------------------------------------------------------------------------
		// Falls ein Gesch.-Kontakt keinen validen Login hat -> bei Anspr. nachsehen
		//----------------------------------------------------------------------------------
		
		$tmp_anspr = ContactPerson::getBusinessContactsByShoplogin($username, $pass);
	   	error_log( "Hallo1");

		if($tmp_anspr != false){  
	      	error_log("  Hallo 2");

			// falls es eine Ansprechpartner mit den Login-Daten gibt, Daten setzen
			$_SESSION["contactperson"]		= $tmp_anspr;
			$_SESSION["contactperson_id"]	= $tmp_anspr->getId();
			$_SESSION["businesscontact"]	= $tmp_anspr->getBusinessContact();
			$_SESSION["cust_id"]			= $tmp_anspr->getBusinessContact()->getId();
			$_SESSION["cust_firstname"]   	= $tmp_anspr->getBusinessContact()->getName1();
			$_SESSION["cust_lastname"]    	= $tmp_anspr->getBusinessContact()->getName2();
			$_SESSION["cust_logontime"]   	= time();
			$_SESSION["login_type"]			= "contactperson";
			
			// Erfolgreicher Login ueber den Ansprechpartner ?>
				<script language="JavaScript">
					location.href = 'index.php';
				</script> <?
		} else {
			$loginmsg .= "Anmeldung fehlgeschlagen";
		}
    }
}?>

<script language="Javascript">
function checkform(obj){
	var xc = 0;
	for(x=0; x < obj.length; x++){
		if(obj[x].value == ''){
			xc++;
			obj[x].style.backgroundColor  = '#d3d3d3';
			obj[x].style.borderColor      = '#FF6600';
			if(xc==1){
				obj[x].focus();
			}
		} else {
			obj[x].style.backgroundColor  = '';
			obj[x].style.borderColor      = '';
		}
	}
	if(xc>0){
		alert("Bitte fehlende Daten eingeben.");
		return false;
	}
	return true;
}
</script>
<link rel="stylesheet" type="text/css" href="../css/login_new.css">

<!-- Loginform -->
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="xform_login">
	<div id="loginform">
		<input name="exec" type="hidden" value="login">
		<div class="login-window">
			<div class="inner">
				<p class="login-row">
					<input type="text" name="user" placeholder="User Name" onLoad="focus()"/>
				</p>
				<p class="pass-row">
					<input type="password" name="password" placeholder="Passwort" onLoad="focus()"/>
				</p>
				<p class="logo-row">
					<span class="error"><? if($_USER) echo $_USER->getError()?></span>
					<img src="../images/page/Logo_Contilas_Kundenportal.png" alt="logo" />
				<p class="submit-row">
					<input type="submit" name="submit" value=""/>
				</p>
			</div>
		</div>
	</div>
</form>

<script language="javascript">
if (document.body.clientHeight)
{
   clientWidth = document.body.clientWidth;
   clientHeight = document.body.clientHeight;
}
else
{
   clientWidth = window.innerWidth;
   clientHeight = window.innerHeight;
}

var centerWidth   = Math.round(clientWidth / 2);
var centerHeight  = Math.round(clientHeight / 2);
var posLeft = centerWidth - 150;
var posTop = centerHeight - 100;

document.getElementById('loginform').style.left = posLeft+'px';
document.getElementById('loginform').style.top = posTop+'px';;
</script>
