<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			17.05.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//-----------------------------------------------------------------------------------

$timestamp = time();
$login_expire = mktime(23,59,59);// Uhrzeit heute Abend 23:59:59

$datum = date("d.m.Y",$login_expire);
$uhrzeit = date("H:i",$login_expire);

if($_REQUEST["subexec"] == "register_tmp"){
	
	//TODO: E-Mail mit den Login-Daten und Zeitraum loschicken
	
	// Standart-Clienten setzen, meist ID=1
	$client = new Client(1);
	
	$new_business = new BusinessContact();
	
	$new_business->setName1(trim(addslashes($_REQUEST["cust_company"])));
	$new_business->setAddress1(trim(addslashes($_REQUEST["cust_street"])));
	$new_business->setCity(trim(addslashes($_REQUEST["cust_city"])));
	$new_business->setZip(trim(addslashes($_REQUEST["cust_plz"])));
	$new_business->setEmail(trim(addslashes($_REQUEST["cust_email"])));
	
	$new_business->setShoplogin(trim(addslashes($_REQUEST["cust_username"])));
	$new_business->setShoppass(trim(addslashes($_REQUEST["cust_password"])));

	$new_business->setClient($client);
	$new_business->setSupplier(0);
	$new_business->setCustomer(1);
	$new_business->setActive(1);
	$new_business->setLanguage(new Translator(22));
	
	$login_expire = mktime(23,59,59);	// Uhrzeit heute Abend 23:59:59 holen
	$login_expire += (3*24*60*60);	 	// Login gueltig bis heute Abend + 3 Tage
	$currtime =  time();
	$expire_date = date("d.m.Y - H:i" , $login_expire);
	
	$new_business->setLoginexpire($login_expire);
	$res = $new_business->save();
	
    //error_log("MYSQL: ".mysql_error());
	
    // Wenn Kunde angelegt, gleich anmelden
	if($res){
		// select userdata for submitted credentials
		$sql = "SELECT id
				FROM businesscontact
				WHERE
				shop_login  = '{$_REQUEST["cust_username"]}' and
	            shop_pass   = '{$_REQUEST["cust_password"]}' and
				active = 1";
		$usrlogin = $DB->select($sql);
		$usrlogin = new BusinessContact($usrlogin[0]["id"]);
		
		$mailerr = 0;
		//----------------------------------------------------------------------------------
		// if user was found, register to session
		//----------------------------------------------------------------------------------
		if($usrlogin->getID() != "" && $usrlogin->getID() != 0){
			// register user data
			$_SESSION["businesscontact"]	= $usrlogin;
			$_SESSION["cust_id"]			= $usrlogin->getId();
        	$_SESSION["cust_firstname"]   	= $usrlogin->getName1();
        	$_SESSION["cust_lastname"]    	= $usrlogin->getName2();
	        $_SESSION["cust_logontime"]		= time();
	        $mailaddr 						= $usrlogin->getEmail();
	        
	        // Kunden benachrichtigen
        	if ($mailaddr != NULL && $mailaddr != ""){
        		$text = '<html>
                  		 <body class="page">
                 		 Sehr geehrte Damen und Herren,<br><br>
                    	 Sie haben sich erfolgreich beim <a href="http://www.bitterundlooseftp.de/kunden/">Kundenportal</a> von Bitter und Loose registreiert <br><br>
        				 Benutzername: {$_REQUEST["cust_username"]} <br>
        				 Password: {$_REQUEST["cust_password"]} <br><br>
        				 Der Login ist bis zum {$expire_date} aktiv.
        				 Freundliche Grüße aus Greven<br><br>
        				 Ihre Bitter & Loose GmbH
                    	 </body>
                    	 </html>';
        		/*$sentmails = sendExternalMail("Login Kundenportal",
        				$text,
        				$mailaddr,
        				"");*/
        		if ($sentmails <= 0)
        			$mailerr++;
        	}
			
			// refresh page ?>
			<script language="JavaScript">
				location.href = 'index.php?exec=index';
			</script><?
		}
	} else {
		$feedback = "Anmeldung fehlgeschlagen";
	}
}

?>

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

<div id="registerform">
	<h1>Kurzzeit-Login einrichten</h1>
	<form action="index.php" method="post" name="form_tmp_reg" id="form_tmp_reg" method="post"
		  onsubmit="return checkform(new Array(this.cust_company, this.cust_email, this.cust_username, this.cust_password))">
	    <input name="exec" type="hidden" value="register_tmp">
	    <input name="subexec" type="hidden" value="register_tmp">
		<table cellpadding="1" cellspacing="0" border="0" width="100%">
			<colgroup>
				<col width="100">
				<col>
			</colgroup>
			<tr>
				<td colspan="2"><b class="msg_error"><?=$feedback?>&nbsp;</b></td>
			</tr>
			<tr>
				<td>Firmenname*</td>
				<td><input name="cust_company" style="width: 180px"></td>
			</tr>
			<tr>
				<td>Stra&szlig;e</td>
				<td><input name="cust_street" style="width: 180px" type="text"></td>
			</tr>
			<tr>
				<td>PLZ/Stadt</td>
				<td>
					<input name="cust_plz" style="width: 49px" type="text"> 
					<input name="cust_city" style="width: 125px" type="text">
				</td>
			</tr>
			<tr>
				<td>E-Mail*</td>
				<td><input name="cust_email" style="width: 180px"></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td>Login*</td>
				<td><input name="cust_username" style="width: 110px"></td>
			</tr>
			<tr>
				<td>Passwort*</td>
				<td><input name="cust_password" style="width: 110px" type="password"></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td align="center"><a href="index.php">Normaler Login</a></td>
				<td align="right" valign="top">
					<input type="submit" value="Registrieren"> &ensp;&ensp;&ensp;
				</td>
			</tr>
		</table>
	</form>
</div>

<script language="javascript">
if (document.body.clientHeight) {
   clientWidth = document.body.clientWidth;
   clientHeight = document.body.clientHeight;
} else {
   clientWidth = window.innerWidth;
   clientHeight = window.innerHeight;
}

var centerWidth   = Math.round(clientWidth / 2);
var centerHeight  = Math.round(clientHeight / 2);
var posLeft = centerWidth - 150;
var posTop = centerHeight - 150;

document.getElementById('registerform').style.left = posLeft+'px';
document.getElementById('registerform').style.top = posTop+'px';;
</script>
