<?
/************************************************************************/
/* Loginform                                                            */

$clients = Client::getAllClients(Client::ORDER_NAME, true);
$_LANG = new Translator();
?>
 
<!-- <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de"> -->
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<!-- <link rel="stylesheet" type="text/css" href="./css/main.css"> -->
<link rel="stylesheet" type="text/css" href="./css/login_new.css">
</head>
<body>
   <!-- Loginform -->
    <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="xform_login">
    <input type="hidden" name="login_keeplogin" value="1"/>
    <input type="hidden" name="login_domain" value="1"/>
        <div class="login-window">
            <div class="inner">
                <p class="login-row">
                    <input type="text" name="login_login" placeholder="User Name" onLoad="focus()"/>
                </p>
                <p class="pass-row">
                    <input type="password" name="login_password" placeholder="Passwort" onLoad="focus()"/>
                </p>
                <p class="logo-row">
                    <span class="error"><? if($_USER) echo $_USER->getError()?></span>
                    <img src="images/page/contilas_logo.png" alt="logo" />
                <p class="submit-row">
                    <input type="submit" name="submit" value=""/>
                </p>
            </div>
        </div>
    </form>
</body>
</html>

