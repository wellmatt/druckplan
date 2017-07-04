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

    <!-- jQuery -->
    <link type="text/css" href="jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="jscripts/jquery/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
    <script type="text/javascript" src="jscripts/jquery/js/jquery.blockUI.js"></script>
    <script language="JavaScript" src="./jscripts/jquery/local/jquery.ui.datepicker-<?=$_LANG->getCode()?>.js"></script>
    <script type="text/javascript" src="jscripts/jquery.validate.min.js"></script>
    <script type="text/javascript" src="jscripts/moment/moment-with-locales.min.js"></script>
    <!-- /jQuery -->

    <!-- MegaNavbar -->
    <link href="thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="thirdparty/MegaNavbar/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="thirdparty/MegaNavbar/assets/css/MegaNavbar.css"/>
    <link rel="stylesheet" type="text/css" href="thirdparty/MegaNavbar/assets/css/skins/navbar-default.css" title="inverse">
    <script src="thirdparty/MegaNavbar/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="jscripts/jquery.bootstrap.wizard.min.js"></script>
    <!-- /MegaNavbar -->


<style>
    @import url(http://fonts.googleapis.com/css?family=Roboto);

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

</head>
<body>
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
            <img id="img_logo" src="./images/login_logo.jpg">
        </div>

        <!-- Begin # DIV Form -->
        <div id="div-forms">

            <!-- Begin # Login Form -->
            <form id="login-form" action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="xform_login">
                <input type="hidden" name="login_keeplogin" value="1"/>
                <input type="hidden" name="login_domain" value="1"/>
                <div class="modal-body">
                    <div id="div-login-msg">
                        <div id="icon-login-msg" class="glyphicon glyphicon-chevron-right"></div>
                        <?php if ($loginfailed){?>
                            <span id="text-login-msg" style="color: #d9534f;">Benutzername oder Password prüfen!</span>
                        <? } else {?>
                            <span id="text-login-msg">Bitte loggen Sie sich ein.</span>
                        <?php } ?>
                    </div>
                    <input id="login_username" name="login_login" class="form-control" placeholder="Username" required="" type="text">
                    <input id="login_password" name="login_password" class="form-control" placeholder="Password" required="" type="password">
                </div>
                <div class="modal-footer">
                    <div>
                        <button type="submit" class="btn btn-primary btn-lg btn-block">Login</button>
                    </div>
                    <div>
                        <button type="button" class="btn btn-info btn-lg btn-block" onclick="window.location.href='/kunden/index.php';">Zum Kundenportal</button>
                    </div>
                </div>
            </form>
            <!-- End # Login Form -->

        </div>
        <!-- End # DIV Form -->

    </div>
</body>
</html>

