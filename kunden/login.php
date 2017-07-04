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

		if($tmp_anspr != false){  

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

<link rel="stylesheet" type="text/css" href="../css/login_new.css">


<!-- jQuery -->
<link type="text/css" href="../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
<script type="text/javascript" src="../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<script type="text/javascript" src="../jscripts/jquery/js/jquery.blockUI.js"></script>
<script language="JavaScript" src="./jscripts/jquery/local/jquery.ui.datepicker-<?=$_LANG->getCode()?>.js"></script>
<script type="text/javascript" src="../jscripts/jquery.validate.min.js"></script>
<script type="text/javascript" src="../jscripts/moment/moment-with-locales.min.js"></script>
<!-- /jQuery -->

<!-- MegaNavbar -->
<link href="../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="../thirdparty/MegaNavbar/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../thirdparty/MegaNavbar/assets/css/MegaNavbar.css"/>
<link rel="stylesheet" type="text/css" href="../thirdparty/MegaNavbar/assets/css/skins/navbar-default.css" title="inverse">
<script src="../thirdparty/MegaNavbar/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="../jscripts/jquery.bootstrap.wizard.min.js"></script>
<!-- /MegaNavbar -->


<style>
	@import url('http://fonts.googleapis.com/css?family=Roboto');

	* {
		font-family: 'Roboto', sans-serif;
	}

	#login-modal .modal-dialog {
		width: 350px;
	}

	#login-modal input[type=text], input[type=password] {
		margin-top: 10px;
	}

	#div-login-msg,
	#div-lost-msg,
	#div-register-msg {
		border: 1px solid #dadfe1;
		height: 30px;
		line-height: 28px;
		transition: all ease-in-out 500ms;
	}

	#div-login-msg.success,
	#div-lost-msg.success,
	#div-register-msg.success {
		border: 1px solid #68c3a3;
		background-color: #c8f7c5;
	}

	#div-login-msg.error,
	#div-lost-msg.error,
	#div-register-msg.error {
		border: 1px solid #eb575b;
		background-color: #ffcad1;
	}

	#icon-login-msg,
	#icon-lost-msg,
	#icon-register-msg {
		width: 30px;
		float: left;
		line-height: 28px;
		text-align: center;
		background-color: #dadfe1;
		margin-right: 5px;
		transition: all ease-in-out 500ms;
	}

	#icon-login-msg.success,
	#icon-lost-msg.success,
	#icon-register-msg.success {
		background-color: #68c3a3 !important;
	}

	#icon-login-msg.error,
	#icon-lost-msg.error,
	#icon-register-msg.error {
		background-color: #eb575b !important;
	}

	/* #########################################
       #    override the bootstrap configs     #
       ######################################### */

	.modal-header {
		min-height: 16.43px;
		padding: 15px 15px 15px 15px;
		border-bottom: 0px;
	}

	.modal-body {
		position: relative;
		padding: 5px 15px 5px 15px;
	}

	.modal-footer {
		padding: 15px 15px 15px 15px;
		text-align: left;
		border-top: 0px;
	}

	.btn {
		border-radius: 0px;
	}

	.btn:focus,
	.btn:active:focus,
	.btn.active:focus,
	.btn.focus,
	.btn:active.focus,
	.btn.active.focus {
		outline: none;
	}

	.btn-lg, .btn-group-lg>.btn {
		border-radius: 0px;
	}

	.glyphicon {
		top: 0px;
	}

	.form-control {
		border-radius: 0px;
	}
</style>

<div class="container" style="
    width: 400px;
    background-color: #ececec;
    border: 1px solid #bdc3c7;
    border-radius: 0px;
    outline: 0;
    position: absolute;
    top: 50%;
    left:50%;
    transform: translate(-50%,-50%);
    ">
	<div class="modal-header" align="center">
		<img id="img_logo" src="../images/shop_logo.jpg">
	</div>

	<!-- Begin # DIV Form -->
	<div id="div-forms">

		<!-- Begin # Login Form -->
		<form id="login-form" action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="xform_login">
			<input name="exec" type="hidden" value="login">
			<div class="modal-body">
				<div id="div-login-msg">
					<div id="icon-login-msg" class="glyphicon glyphicon-chevron-right"></div>
					<?php if ($loginfailed){?>
						<span id="text-login-msg" style="color: #d9534f;">Benutzername oder Password prüfen!</span>
					<? } else {?>
						<span id="text-login-msg">Bitte loggen Sie sich ein.</span>
					<?php } ?>
				</div>
				<input id="login_username" name="user" class="form-control" placeholder="Username" required="" type="text">
				<input id="login_password" name="password" class="form-control" placeholder="Password" required="" type="password">
			</div>
			<div class="modal-footer">
				<div>
					<button type="submit" class="btn btn-primary btn-lg btn-block">Login</button>
				</div>
			</div>
		</form>
		<!-- End # Login Form -->

	</div>
	<!-- End # DIV Form -->

</div>
