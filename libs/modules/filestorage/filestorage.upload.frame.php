<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
error_reporting(-1);
ini_set('display_errors', 1);
chdir('../../../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/perferences/perferences.class.php';
require_once 'libs/modules/mail/mailmassage.class.php';
require_once 'libs/modules/textblocks/textblock.class.php';

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$perf = new Perferences();

$module = 'None';
if ($_REQUEST['module'])
    $module = $_REQUEST['module'];
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="../../../css/main.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/main.print.css" media="print"/>


    <!-- jQuery -->
    <link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />
    <script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
    <script language="JavaScript" src="../../../jscripts/jquery/local/jquery.ui.datepicker-<?=$_LANG->getCode()?>.js"></script>
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.1/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/jquery.dataTables.min.js"></script>
    <script language="javascript" src="../../../jscripts/basic.js"></script>
    <script language="javascript" src="../../../jscripts/loadingscreen.js"></script>
    <script	type="text/javascript" src="../../../jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
    <script	type="text/javascript" src="../../../jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
    <link rel="stylesheet" type="text/css" href="../../../jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
    <link href="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <script src="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../../thirdparty/ckeditor/ckeditor.js"></script>
    <script src="../../../jscripts/jvalidation/dist/jquery.validate.min.js"></script>
    <link rel="stylesheet" href="../../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../css/jquery.fileupload.css">
    <script src="../../../jscripts/jquery/js/jquery.ui.widget.js"></script>
    <link rel="stylesheet" type="text/css" href="../../../css/glyphicons-bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/glyphicons.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/glyphicons-halflings.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/glyphicons-filetypes.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/glyphicons-social.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/main.css" />

    <script>
        $(function () {
            $('#btn').click(function () {
                $('.myprogress').css('width', '0');
                $('.msg').text('');
                var uploadfile = $('#uploadfile').val();
                if (uploadfile == '') {
                    alert('Bitte eine Datei auswählen!');
                    return;
                }
                var formData = new FormData();
                formData.append('uploadfile', $('#uploadfile')[0].files[0]);
                formData.append('ajax_action', 'upload');
                formData.append('module', '<?php echo $module;?>');
                $('#btn').attr('disabled', 'disabled');
                $('.msg').text('Uploading in progress...');
                $.ajax({
                    url: 'filestorage.upload.ajax.php',
                    data: formData,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    // this part is progress bar
                    xhr: function () {
                        var xhr = new window.XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function (evt) {
                            if (evt.lengthComputable) {
                                var percentComplete = evt.loaded / evt.total;
                                percentComplete = parseInt(percentComplete * 100);
                                $('.myprogress').text(percentComplete + '%');
                                $('.myprogress').css('width', percentComplete + '%');
                            }
                        }, false);
                        return xhr;
                    },
                    success: function (data) {
                        $('.msg').text(data);
                        $('#fileid', window.parent.document).val(data);
                        parent.$.fancybox.close();
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Dateiupload</h3>
        </div>
        <div class="panel-body">
            <form id="myform" method="post">
                <div class="form-group">
                    <label>Datei auswählen: </label>
                    <input class="form-control" type="file" id="uploadfile" />
                </div>
                <div class="form-group">
                    <div class="progress">
                        <div class="progress-bar progress-bar-success myprogress" role="progressbar" style="width:0%">0%</div>
                    </div>

                    <div class="msg"></div>
                </div>

                <input type="button" id="btn" class="btn-success" value="Upload" />
            </form>
        </div>
    </div>
</body>
</html>
