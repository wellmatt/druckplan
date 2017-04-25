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
require_once 'libs/modules/collectiveinvoice/collectiveinvoice.class.php';


require_once __DIR__.'/../../../vendor/Horde/Autoloader.php';
require_once __DIR__.'/../../../vendor/Horde/Autoloader/ClassPathMapper.php';
require_once __DIR__.'/../../../vendor/Horde/Autoloader/ClassPathMapper/Default.php';

$autoloader = new Horde_Autoloader();
$autoloader->addClassPathMapper(new Horde_Autoloader_ClassPathMapper_Default(__DIR__.'/../../../vendor'));
$autoloader->registerAutoloader();

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$perf = new Perferences();
/*
 * Local functions
 */
function reArrayFiles(&$file_post) {

    $file_ary = array();
    $file_count = count($file_post['name']);
    $file_keys = array_keys($file_post);

    for ($i=0; $i<$file_count; $i++) {
        foreach ($file_keys as $key) {
            $file_ary[$i][$key] = $file_post[$key][$i];
        }
    }

    return $file_ary;
}
function fatal_error ( $sErrorMessage = '' )
{
    header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error' );
    die( $sErrorMessage );
}

$savemsg = "";

// Prefill form inputs
$preset_mail_to = '';
$preset_mail_cc = '';
$preset_mail_subject = '';
$preset_mail_content = '';

if ($_REQUEST["fromColinv"] > 0){
    $colinv = new CollectiveInvoice((int)$_REQUEST["fromColinv"]);
    $attachments = [];
    $docs = Document::getDocuments(Array("sent" => false, "requestId" => $colinv->getId(), "module" => Document::REQ_MODULE_COLLECTIVEORDER));
    foreach ($docs as $doc) {
        $attachments[] = ["name"=>$doc->getName(), "filename"=>$doc->getFilename(Document::VERSION_EMAIL), "docid"=>$doc->getId()];
    }
    $preset_mail_to = $colinv->getCustContactperson()->getEmail();
    $preset_mail_subject = 'Ihr Vorgang: '.$colinv->getNumber().' - '.$colinv->getTitle();

    $preset_mail_content = 'Sehr geehrte/r Frau/Herr '. $colinv->getCustContactperson()->getName1().',';
    $preset_mail_content .= '<br><br>bitte entnehmen Sie die Dokumente zum oben genannten Vorgang aus dem Anhang.';
    $preset_mail_content .= '<br>';
    $preset_mail_content .= $perf->getSystemSignature();
}

if ($_REQUEST["exec"] == "send")
{
    $mail_subject = $_REQUEST["mail_subject"];
    $mail_text = $_REQUEST["mail_text"];
    $attachments = [];
    $mail_to = explode(",", $_REQUEST["mail_to"]);
    $mail_ccs = explode(",", $_REQUEST["mail_cc"]);
    $mail_bcc = explode(",", $_REQUEST["mail_bcc"]);

    // Add the file as an attachment, set the file name and what kind of file it is.
    if ($_REQUEST['mail_files']) {
        foreach ($_REQUEST['mail_files'] as $file) {
            if ($file != ""){
                $attachments[$file] = 'libs/modules/attachment/files/'.$file;
            }
        }
    }

    if ($_REQUEST['attach']) {
        foreach ($_REQUEST['attach'] as $attach) {
            $file = $attach;
            if ($file != "") {
                $attachments[$file] = 'libs/modules/attachment/files/' . $file;
            }
        }
    }

    if ($_REQUEST["fromColinv"] > 0) {
        if (count($_REQUEST["sentdocs"]) > 0) {
            foreach ($_REQUEST['sentdocs'] as $sentdoc) {
                $doc = new Document((int)$sentdoc);
                $attachments[$doc->getName()] = $doc->getFilename(Document::VERSION_EMAIL);
                $doc->setSent(1);
                $doc->save();
            }
        }
    }

    $message = new MailMessage(null,$mail_to,$mail_subject,$mail_text,$mail_ccs,$mail_bcc,$attachments);
    $message->send();

    if ($_REQUEST['mail_files']) {
        foreach ($_REQUEST['mail_files'] as $file) {
            if ($file != ""){
                unlink('libs/modules/attachment/files/'.$file);
            }
        }
    }

    echo '<script type="text/javascript">';
    echo 'parent.$.fancybox.close();';
    echo '</script>';
}
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
    <!-- /jQuery -->

    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.1/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/jquery.dataTables.min.js"></script>


    <script language="javascript" src="../../../jscripts/basic.js"></script>
    <script language="javascript" src="../../../jscripts/loadingscreen.js"></script>
    <!-- FancyBox -->
    <script	type="text/javascript" src="../../../jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
    <script	type="text/javascript" src="../../../jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
    <link rel="stylesheet" type="text/css" href="../../../jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

    <link href="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
    <script src="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/js/bootstrap.min.js"></script>

    <script src="../../../thirdparty/ckeditor/ckeditor.js"></script>
    <script src="../../../jscripts/jvalidation/dist/jquery.validate.min.js"></script>

    <!-- file upload -->
    <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
    <link rel="stylesheet" href="../../../css/bootstrap.min.css">
    <link rel="stylesheet" href="../../../css/jquery.fileupload.css">
    <script src="../../../jscripts/jquery/js/jquery.ui.widget.js"></script>
    <!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
    <script src="../../../jscripts/jquery/js/jquery.iframe-transport.js"></script>
    <!-- The basic File Upload plugin -->
    <script src="../../../jscripts/jquery/js/jquery.fileupload.js"></script>
    <!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
    <link rel="stylesheet" type="text/css" href="../../../css/glyphicons-bootstrap.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/glyphicons.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/glyphicons-halflings.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/glyphicons-filetypes.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/glyphicons-social.css" />
    <link rel="stylesheet" type="text/css" href="../../../css/main.css" />
    <link rel="stylesheet" type="text/css" href="../../../jscripts/tagit/jquery.tagit.css" media="screen" />
    <script type="text/javascript" charset="utf8" src="../../../jscripts/tagit/tag-it.min.js"></script>

    <!-- // file upload -->

    <script>
        /*jslint unparam: true */
        /*global window, $ */
        $(function () {
            'use strict';
            // Change this to the location of your server-side upload handler:
            var url = '../../../libs/modules/attachment/attachment.handler.php';
            $('#fileupload').fileupload({
                url: url,
                dataType: 'json',
                done: function (e, data) {
                    $.each(data.result.files, function (index, file) {
                        $('<p/>').text(file.name).appendTo('#files');
                        $('#files').append('<input name="mail_files[]" type="hidden" value="'+file.name+'"/>');
                    });
                },
                progressall: function (e, data) {
                    var progress = parseInt(data.loaded / data.total * 100, 10);
                    $('#progress .progress-bar').css(
                        'width',
                        progress + '%'
                    );
                }
            }).prop('disabled', !$.support.fileInput)
                .parent().addClass($.support.fileInput ? undefined : 'disabled');
        });
    </script>

    <script language="JavaScript">
        $(function() {
            var mail_text = CKEDITOR.replace( 'mail_text', {
                // Define the toolbar groups as it is a more accessible solution.
                toolbarGroups: [
                    { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
                    { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
                    { name: 'links' },
                    { name: 'insert' },
                    { name: 'tools' },
                    { name: 'others' },
                    '/',
                    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                    { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
                    { name: 'styles' },
                    { name: 'colors' }
                ]
                // Remove the redundant buttons from toolbar groups defined above.
                //removeButtons: 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar'
            } );
            var comment = $('#msg_content').html();
            CKEDITOR.instances.mail_text.setData( comment, function()
            {
                this.checkDirty();  // true
            });
            $("a#add_to").fancybox({
                'type'    : 'iframe',
                'transitionIn'	:	'elastic',
                'transitionOut'	:	'elastic',
                'speedIn'		:	600,
                'speedOut'		:	200,
                'width'         :   1024,
                'height'		:	768,
                'overlayShow'	:	true,
                'helpers'		:   { overlay:null, closeClick:true }
            });

            $('#mail_form').validate({
                rules: {
                    mail_to: {
                        required: function()
                        {
                            CKEDITOR.instances.mail_to.updateElement();
                        }
                    },
                    'mail_to': {
                        required: true
                    },
                    'mail_subject': {
                        required: true
                    }
                },
                errorPlacement: function(error, $elem) {
                    if ($elem.is('textarea')) {
                        $elem.next().css('border', '1px solid red');
                    }
                },
                ignore: []
            });
            jQuery("#mail_to").tagit({
                singleField: true,
                singleFieldNode: $('#mail_to'),
                singleFieldDelimiter: ",",
                allowSpaces: false,
                minLength: 2,
                removeConfirmation: true,
                tagSource: function( request, response ) {
                    $.ajax({
                        url: "mail.ajax.php?exec=searchrcpt",
                        data: { term:request.term },
                        dataType: "json",
                        success: function( data ) {
                            response( $.map( data, function( item ) {
                                return {
                                    label: item.label,
                                    value: item.value
                                }
                            }));
                        }
                    });
                }
            });
            jQuery("#mail_cc").tagit({
                singleField: true,
                singleFieldNode: $('#mail_cc'),
                singleFieldDelimiter: ",",
                allowSpaces: false,
                minLength: 2,
                removeConfirmation: true,
                tagSource: function( request, response ) {
                    $.ajax({
                        url: "mail.ajax.php?exec=searchrcpt",
                        data: { term:request.term },
                        dataType: "json",
                        success: function( data ) {
                            response( $.map( data, function( item ) {
                                return {
                                    label: item.label,
                                    value: item.value
                                }
                            }));
                        }
                    });
                }
            });
            jQuery("#mail_bcc").tagit({
                singleField: true,
                singleFieldNode: $('#mail_bcc'),
                singleFieldDelimiter: ",",
                allowSpaces: false,
                minLength: 2,
                removeConfirmation: true,
                tagSource: function( request, response ) {
                    $.ajax({
                        url: "mail.ajax.php?exec=searchrcpt",
                        data: { term:request.term },
                        dataType: "json",
                        success: function( data ) {
                            response( $.map( data, function( item ) {
                                return {
                                    label: item.label,
                                    value: item.value
                                }
                            }));
                        }
                    });
                }
            });
            function split( val ) {
                return val.split( /,\s*/ );
            }
        } );
    </script>

</head>
<body>
    <div id="msg_content" style="display: none;"><?php echo $preset_mail_content;?></div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                Neue eMail
                <span class="pull-right">
                    <button class="btn btn-xs btn-success" type="button" onclick="$('#mail_form').submit();">
                        <span class="glyphicons glyphicons-envelope"></span>
                        Senden
                    </button>
                </span>
            </h3>
        </div>
        <div class="panel-body">
            <form action="mail.send.system.frame.php" method="post" id="mail_form" name="mail_form" enctype="multipart/form-data">
                <input type="hidden" id="exec" name="exec" value="send">
                <input type="hidden" id="fromColinv" name="fromColinv" value="<?php echo $_REQUEST["fromColinv"];?>">
                <div class="form-group">
                    <div class=" col-xs-1">
                        <label class="control-label" for="mail_to">An</label>
                    </div>
                    <div class=" col-xs-11">
                        <input type="text" id="mail_to" name="mail_to" value="<?php echo $preset_mail_to; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class=" col-xs-1">
                        <label class="control-label" for="mail_cc">CC</label>
                    </div>
                    <div class=" col-xs-11">
                        <input type="text" id="mail_cc" name="mail_cc" value="<?php echo $preset_mail_cc; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class=" col-xs-1">
                        <label class="control-label" for="mail_bcc">BCC</label>
                    </div>
                    <div class=" col-xs-11">
                        <input type="text" id="mail_bcc" name="mail_bcc">
                    </div>
                </div>
                <div class="form-group">
                    <div class=" col-xs-1">
                        <label class="control-label" for="mail_subject">Betreff</label>
                    </div>
                    <div class=" col-xs-11">
                        <input type="text" id="mail_subject" name="mail_subject" value="<?php echo $preset_mail_subject; ?>"
                               class="form-control" style="margin-bottom: 10px;">
                    </div>
                </div>
                <div class="form-group">
                    <div class=" col-xs-1">
                        <label class="control-label" for="mail_text">Nachricht</label>
                    </div>
                    <div class=" col-xs-11">
                        <div class="input-group">
                            <textarea id="mail_text" name="mail_text" rows="10" class="form-control"
                                      cols="80"></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class=" col-xs-1">
                        <label class="control-label" for="mail_attachments">Anhänge</label>
                    </div>
                    <div class=" col-xs-11">
                        <div class="input-group">
                      <span class="input-group-addon">
                      <span class="btn btn-success btn-xs fileinput-button">
                          <span>Hinzufügen...</span>
                          <input type="file" multiple="multiple" id="fileupload" name="files[]" width="100%"/>
                      </span>
                      <div id="files" class="files">
                          <?php
                          if (count($attachments) > 0) {
                              if ($_REQUEST["fromColinv"] > 0){
                                  foreach ($attachments as $attachment) {
                                      echo '<p>' . $attachment['name'] . '<input type="hidden" name="sentdocs[]" value="'.$attachment["docid"].'"><span class="glyphicons glyphicons-remove pointer" onclick="$(this).parent().remove();"></span></p>';
                                  }
                              }
                          }
                          ?>
                      </div>
                      <div id="progress" class="progress">
                          <div class="progress-bar progress-bar-success"></div>
                      </div>
                      </span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>