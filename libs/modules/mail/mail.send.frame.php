<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.09.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
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

$mailadresses = $_USER->getEmailAddresses();
$mailsettings = false;
$savemsg = "";
$mail_servers = Array();


if ($_REQUEST["exec"] == "send")
{
    $mailadress_send = new Emailaddress($_REQUEST["mail_from"]);
    
    $mailer = new Horde_Mail_Transport_Mail();
    
    // New Horde MIME_Mail Object
    $mail = new Horde_Mime_Mail();
    
    // Set the header date
    $mail->addHeader('Date', date('r'));
    
    // Set the from address
    $mail_from = $mailadress_send->getAddress();
    $mail->addHeader('From', $mail_from);
    
    // Set the subject of the mail
    $mail_subject = $_REQUEST["mail_subject"];
    $mail->addHeader('Subject', $mail_subject);
    
    // Set the text message body
    $mail_text = $_REQUEST["mail_text"];
    $mail->setHtmlBody($mail_text);
    
    // Add the file as an attachment, set the file name and what kind of file it is.
    if ($_REQUEST['mail_files']) {
        foreach ($_REQUEST['mail_files'] as $file) {
            if ($file != ""){
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $ftype = finfo_file($finfo, 'libs/modules/attachment/files/'.$file);
                finfo_close($finfo);
                $mail->addAttachment('libs/modules/attachment/files/'.$file, $file, $ftype);
            }
        }
    }
    if ($_REQUEST['old_attach']){
        foreach ($_REQUEST['old_attach'] as $old_attach) {
            $file = $old_attach;
            if ($file != ""){
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $ftype = finfo_file($finfo, 'docs/attachments/'.$file);
                finfo_close($finfo);
                $mail->addAttachment('docs/attachments/'.$file, $file, $ftype);
            }
        }
    }

    // Add CC
    $mail_cc = trim($_REQUEST["mail_cc"]);
    if (!strstr($mail_cc, ",")===false)
    {
        $mail_cc = explode(",", $mail_cc);
        foreach ($mail_cc as $recipients)
        {
            if ($recipients != "")
                $mail->addHeader('CC', $recipients);
        }
    } else {
        $mail->addHeader('CC', $mail_cc);
    }

    // Add BCC
    $mail_bcc = trim($_REQUEST["mail_bcc"]);
    if (!strstr($mail_bcc, ",")===false)
    {
        $mail_bcc = explode(",", $mail_bcc);
        foreach ($mail_bcc as $recipients)
        {
            if ($recipients != "")
                $mail->addHeader('BCC', $recipients);
        }
    } else {
        $mail->addHeader('BCC', $mail_bcc);
    }
    
    // Add recipients
    $mail_to = trim($_REQUEST["mail_to"]);
    if (!strstr($mail_to, ",")===false)
    {
        $mail_to = explode(",", $mail_to);
        foreach ($mail_to as $recipients)
        {
            if ($recipients != "")
                $mail->addHeader('TO', $recipients);
//                $mail->addRecipients($recipients);
        }
    } else {
        $mail->addHeader('TO', $mail_to);
//        $mail->addRecipients($mail_to);
    }
    
    // Send the mail
    $mail->send($mailer);
    
    try {
        /* Connect to an IMAP server.
         *   - Use Horde_Imap_Client_Socket_Pop3 (and most likely port 110) to
         *     connect to a POP3 server instead. */
        $client = new Horde_Imap_Client_Socket(array(
            'username' => $mailadress_send->getLogin(),
            'password' => $mailadress_send->getPassword(),
            'hostspec' => $mailadress_send->getHost(),
            'port' => $mailadress_send->getPort(),
            'secure' => 'ssl',
            'cache' => array(
                'backend' => new Horde_Imap_Client_Cache_Backend_Cache(array(
                    'cacheob' => new Horde_Cache(new Horde_Cache_Storage_File(array(
                        'dir' => '/tmp/hordecache'
                    )))
                ))
            )
        ));
//        prettyPrint($mail->getRaw(false));
//        die();

        $message_array = Array( Array("data"=>Array(Array("t"=>"text","v"=>$mail->getRaw(false)))) );
//        var_dump($message_array);
        $client->append("contilas-sent", $message_array, Array("create"=>true));
    } catch (Horde_Imap_Client_Exception $e) {
        var_dump($e->details);
        echo "</br>";
    }

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

if ($_REQUEST["exec"] == "save")
{
    if ($_REQUEST["debug"] == true)
    {
        $_REQUEST["mail_from"] = 47;
        $_REQUEST["mail_subject"] = "Test";
        $_REQUEST["mail_text"] = "Test 123";
        $_REQUEST["mail_to"] = "ascherer@ipactor.de";
    }


    $mailadress_send = new Emailaddress($_REQUEST["mail_from"]);

    $mailer = new Horde_Mail_Transport_Mail();

    $headers = new Horde_Mime_Headers();

    // New Horde MIME_Mail Object
    $mail = new Horde_Mime_Mail();

    // Set the header date
    $mail->addHeader('Date', date('r'));

    // Set the from address
    $mail_from = $mailadress_send->getAddress();
    $mail->addHeader('From', $mail_from);

    // Set the subject of the mail
    $mail_subject = $_REQUEST["mail_subject"];
    $mail->addHeader('Subject', $mail_subject);

    // Set the text message body
    $mail_text = $_REQUEST["mail_text"];
    $mail->setHtmlBody($mail_text);

    // Add the file as an attachment, set the file name and what kind of file it is.
    $mime_parts = Array();
    if ($_REQUEST['mail_files']) {
        foreach ($_REQUEST['mail_files'] as $file) {
            if ($file != ""){
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $ftype = finfo_file($finfo, 'libs/modules/attachment/files/'.$file);
                finfo_close($finfo);
                $mail->addAttachment('libs/modules/attachment/files/'.$file, $file, $ftype);

                $part = new Horde_Mime_Part();
                $part->setType($ftype);
                $part->setCharset('us-ascii');
                $part->setDisposition('attachment');
                $part->setContents(file_get_contents('libs/modules/attachment/files/'.$file));
                $part->setName($file);
                $part->setTransferEncoding('base64',false);
                $mime_parts[] = $part;
            }
        }
    }

    // Add CC
    $mail_cc = trim($_REQUEST["mail_cc"]);
    if (!strstr($mail_cc, ",")===false)
    {
        $mail_cc = explode(",", $mail_cc);
        foreach ($mail_cc as $recipients)
        {
            if ($recipients != "")
                $mail->addHeader('CC', $recipients);
        }
    } else {
        $mail->addHeader('CC', $mail_cc);
    }

    // Add BCC
    $mail_bcc = trim($_REQUEST["mail_bcc"]);
    if (!strstr($mail_bcc, ",")===false)
    {
        $mail_bcc = explode(",", $mail_bcc);
        foreach ($mail_bcc as $recipients)
        {
            if ($recipients != "")
                $mail->addHeader('BCC', $recipients);
        }
    } else {
        $mail->addHeader('BCC', $mail_bcc);
    }

    // Add recipients
    $mail_to = trim($_REQUEST["mail_to"]);
    if (!strstr($mail_to, ",")===false)
    {
        $mail_to = explode(",", $mail_to);
        foreach ($mail_to as $recipients)
        {
            if ($recipients != "")
                $mail->addHeader('TO', $recipients);
//                $mail->addRecipients($recipients);
        }
    } else {
        $mail->addHeader('TO', $mail_to);
//        $mail->addRecipients($mail_to);
    }

    try {
        /* Connect to an IMAP server.
         *   - Use Horde_Imap_Client_Socket_Pop3 (and most likely port 110) to
         *     connect to a POP3 server instead. */
        $client = new Horde_Imap_Client_Socket(array(
            'username' => $mailadress_send->getLogin(),
            'password' => $mailadress_send->getPassword(),
            'hostspec' => $mailadress_send->getHost(),
            'port' => $mailadress_send->getPort(),
            'secure' => 'ssl',
            'cache' => array(
                'backend' => new Horde_Imap_Client_Cache_Backend_Cache(array(
                    'cacheob' => new Horde_Cache(new Horde_Cache_Storage_File(array(
                        'dir' => '/tmp/hordecache'
                    )))
                ))
            )
        ));

        $mail->addHeaderOb(Horde_Mime_Headers_MessageId::create());
        $mail->addHeaderOb(Horde_Mime_Headers_UserAgent::create());
        $mail->addHeaderOb(Horde_Mime_Headers_Date::create());

        $htmlBody = new Horde_Mime_Part();
        $htmlBody->setType('text/html');
        $htmlBody->setCharset('UTF-8');
        $htmlBody->setContents($mail_text);
        $htmlBody->setDescription(Horde_Mime_Translation::t("HTML Version of Message"));
        $htmlBody->toString();

        $plainText = Horde_Text_Filter::filter($mail_text, 'Html2text', array('charset' => 'UTF-8', 'wrap' => false));

        $textBody = new Horde_Mime_Part();
        $textBody->setType('text/plain');
        $textBody->setCharset('UTF-8');
        $textBody->setContents($plainText);
        $textBody->setDescription(Horde_Mime_Translation::t("Plaintext Version of Message"));
        $flowed = new Horde_Text_Flowed($textBody->getContents(), $textBody->getCharset());
        $flowed->setDelSp(true);
        $textBody->setContentTypeParameter('format', 'flowed');
        $textBody->setContentTypeParameter('DelSp', 'Yes');
        $textBody->setContents($flowed->toFlowed());
        $textBody->toString();

        $body = new Horde_Mime_Part();
        $body->setType('multipart/alternative');
        $body->addPart($textBody);
        $body->addPart($htmlBody);
        $body->setTransferEncoding('binary',false);
        $body->toString();

        $basepart = new Horde_Mime_Part();
        $basepart->setType('multipart/mixed');
        $basepart->addPart($body);
        $basepart->isBasePart(true);

        if (count($mime_parts)) {
            foreach ($mime_parts as $mime_part) {
                $basepart->addPart($mime_part);
            }
        }
        $basepart->setHeaderCharset('UTF-8');
        $basepart->setMimeId("1");
        $basepart->addMimeHeaders();
        $basepart->buildMimeIds($basepart->getMimeId());
        $basepart->toString();
        $boundary_base = $basepart->getContentTypeParameter('boundary');

        $mail->setBasePart($basepart);
        $mail->removeHeader('MIME-Version');



        $mail_header = new Horde_Mime_Headers_ContentParam_ContentType('Content-Type','multipart/mixed');
        $mail_header->unserialize(
            serialize(
                array(
                    '_params'=> Array(
                        'boundary' => $boundary_base,
                    ),
                    '_values'=> Array(
                        'multipart/mixed',
                    )
                )
            )
        );
//        prettyPrint($mail_header);
//        die();

        $mail->addHeaderOb($mail_header);

//        prettyPrint($basepart);
//        echo '</br>';
//        prettyPrint($mail->getRaw(false));
//        die();

        $message_array = Array( Array("data"=>Array(Array("t"=>"text","v"=>$mail->getRaw(false)))) );
        $client->append("contilas-draft", $message_array, Array("create"=>true));
    } catch (Horde_Imap_Client_Exception $e) {
        var_dump($e->details);
        echo "</br>";
    }

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

if (count($mailadresses)>0)
{
    foreach ($mailadresses as $mailadress)
    {

        try {
            /* Connect to an IMAP server.
             *   - Use Horde_Imap_Client_Socket_Pop3 (and most likely port 110) to
             *     connect to a POP3 server instead. */
            $client = new Horde_Imap_Client_Socket(array(
                'username' => $mailadress->getAddress(),
                'password' => $mailadress->getPassword(),
                'hostspec' => $mailadress->getHost(),
                'port' => $mailadress->getPort(),
                'secure' => 'ssl',

                // OPTIONAL Debugging. Will output IMAP log to the /tmp/foo file
//                 'debug' => '/tmp/foo',

                // OPTIONAL Caching. Will use cache files in /tmp/hordecache.
                // Requires the Horde/Cache package, an optional dependency to
                // Horde/Imap_Client.
                'cache' => array(
                    'backend' => new Horde_Imap_Client_Cache_Backend_Cache(array(
                        'cacheob' => new Horde_Cache(new Horde_Cache_Storage_File(array(
                            'dir' => '/tmp/hordecache'
                        )))
                    ))
                )
            ));
            $mail_servers[] = Array("mail"=>$mailadress->getAddress(), "mailid"=>$mailadress->getId(), "imap"=> $client);
            $mailsettings = true;
        } catch (Horde_Imap_Client_Exception $e) {
            //             var_dump($e);
        }
    }
} else
{
    $savemsg = '<span class="label label-danger">Keine Mail-Konten hinterlegt</span>';
}

if ($_REQUEST["preset"] == "FW" || $_REQUEST["preset"] == "RE" || $_REQUEST["preset"] == "REALL")
{
    $mailadress = new Emailaddress($_REQUEST["mailid"]);
    
    $server = $mailadress->getHost();
    $port = $mailadress->getPort();
    $user = $mailadress->getLogin();
    $password = $mailadress->getPassword();
    
    try {
        /* Connect to an IMAP server.
         *   - Use Horde_Imap_Client_Socket_Pop3 (and most likely port 110) to
         *     connect to a POP3 server instead. */
        $client = new Horde_Imap_Client_Socket(array(
            'username' => $user,
            'password' => $password,
            'hostspec' => $server,
            'port' => $port,
            'secure' => 'ssl',
    
            // OPTIONAL Debugging. Will output IMAP log to the /tmp/foo file
//             'debug' => '/tmp/foo',
    
            // OPTIONAL Caching. Will use cache files in /tmp/hordecache.
            // Requires the Horde/Cache package, an optional dependency to
            // Horde/Imap_Client.
            'cache' => array(
                'backend' => new Horde_Imap_Client_Cache_Backend_Cache(array(
                    'cacheob' => new Horde_Cache(new Horde_Cache_Storage_File(array(
                        'dir' => '/tmp/hordecache'
                    )))
                ))
            )
        ));
    
        $query = new Horde_Imap_Client_Fetch_Query();
        $query->structure();
        $query->envelope();
    
        $uid = new Horde_Imap_Client_Ids($_REQUEST["muid"]);
    
        $list = $client->fetch($_REQUEST["mailbox"], $query, array(
            'ids' => $uid
        ));

        $orig_mail_fromall = $list->first()->getEnvelope()->from->__toString().', '.$list->first()->getEnvelope()->cc->__toString();
        $orig_mail_fromall = str_replace('"', '', $orig_mail_fromall);
        
        $orig_mail_from = $list->first()->getEnvelope()->from->__toString();
        $orig_mail_from = str_replace('"', '', $orig_mail_from);
        $orig_mail_subject = $list->first()->getEnvelope()->subject;
        $orig_mail_date = date("d.m.Y H:i",$list->first()->getEnvelope()->date->__toString());
        $orig_mail_to = $list->first()->getEnvelope()->to->__toString();
    
        $part = $list->first()->getStructure();

//        if ($_REQUEST["preset"] == "FW")
//        {

            $map = $part->ContentTypeMap();
            $attachments = array();
            foreach ( $map as $key => $value ) {
                $p = $part->getPart( $key );
                $disposition = $p->getDisposition();
                if ( ! in_array( $disposition, array( 'attachment', 'inline' ) ) ) {
                    continue;
                }
                $name = $p->getName();
                $type = $p->getType();
                if ( 'inline' === $disposition && 'text/plain' === $type ) {
                    continue;
                }
                $new_attachment = array(
                    'disposition' => $disposition,
                    'type' => $p->getPrimaryType(),
                    'mimetype' => $type,
                    'mime_id' => $key,
                    'name' => $name,
                );


                $filename = $_REQUEST["mailid"].'_'.$_REQUEST["muid"].'_'.$name;
                if(!file_exists("docs/attachments/".$filename)) {
                    $uid = new Horde_Imap_Client_Ids( $_REQUEST["muid"] );
                    $mime_id = $new_attachment["mime_id"];

                    $query = new Horde_Imap_Client_Fetch_Query();
                    $query->bodyPart( $mime_id, array(
                            'decode' => true,
                            'peek' => true,
                        )
                    );
                    $list = $client->fetch( $_REQUEST["mailbox"], $query, array(
                            'ids' => $uid,
                        )
                    );
                    $message = $list->first();

                    $image_data = $message->getBodyPart( $mime_id );
                    $image_data_decoded = base64_decode( $image_data );

                    $p = $part->getPart( $mime_id );
                    $name = $p->getName();

                    $filename = $_REQUEST["mailid"].'_'.$_REQUEST["muid"].'_'.$name;
                    $fh = fopen("docs/attachments/".$filename, "w");
                    fwrite($fh, $image_data_decoded);
                    fclose($fh);
                }
                $new_attachment['filename'] = $filename;
                $attachments[] = $new_attachment;
            }
//        }

        $id = $part->findBody('html');
        
        $content = "";
        $id = $part->findBody('html');
        if ($id == NULL)
            $id = $part->findBody();
        if ($id != NULL)
        {
            $body = $part->getPart($id);
            
        
            $query2 = new Horde_Imap_Client_Fetch_Query();
            $query2->bodyPart($id, array(
                'decode' => true,
                'peek' => false
            ));
        
            $list2 = $client->fetch($_REQUEST["mailbox"], $query2, array(
                'ids' => $uid
            ));
        
            $message2 = $list2->first();
            $content = $message2->getBodyPart($id);
            if (!$message2->getBodyPartDecode($id)) {
                $body->setContents($content);
                $content = $body->getContents();
            }
        
            $content = strip_tags( $content, '<img><p><br><i><b><u><em><strong><strike><font><span><div><style><a>' );
            $content = trim( $content );
            $charset = $body->getCharset();
            if ( 'iso-8859-1' === $charset ) {
                $content = utf8_encode( $content );
            } elseif ( function_exists( 'iconv' ) ) {
                $content = iconv( $charset, 'UTF-8', $content );
            }
        }
        
        $signatur = $_USER->getSignature();
        
        $content =  $signatur . '<br><hr>'. 
                    'Von: '.$orig_mail_from.'<br>
                    Gesendet: '.$orig_mail_date.'<br>
                    An: '.$orig_mail_to.'<br>
                    Betreff: '.$orig_mail_subject.'<br><br>' . $content;
        
        $new_subject = $_REQUEST["preset"] . ": " .$orig_mail_subject;

    
    } catch (Horde_Imap_Client_Exception $e) {
        fatal_error('Could not connect to Server!');
    }
} else {
    $signatur = $_USER->getSignature();
    
    $content =  '<br><br>'.$signatur;
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
	    $( "#mail_to" ).bind( "keydown", function( event ) {
    		 if ( event.keyCode === $.ui.keyCode.TAB && $( this ).autocomplete( "instance" ).menu.active ) {
        		 event.preventDefault();
    		 }
		 }).autocomplete({
    		 source: function( request, response ) {
        		 $.getJSON( "mail.ajax.php?exec=searchrcpt", {
            		 term: extractLast( request.term )
        		 }, response );
    		 },
    		 search: function() {
        		 // custom minLength
        		 var term = extractLast( this.value );
        		 if ( term.length < 2 ) {
            		 return false;
        		 }
    		 },
    		 focus: function() {
        		 // prevent value inserted on focus
        		 return false;
    		 },
    		 select: function( event, ui ) {
        		 var terms = split( this.value );
        		 // remove the current input
        		 terms.pop();
        		 // add the selected item
        		 terms.push( ui.item.value );
        		 // add placeholder to get the comma-and-space at the end
        		 terms.push( "" );
        		 this.value = terms.join( ", " );
        		 return false;
    		 }
		 });
	    $( "#mail_cc" ).bind( "keydown", function( event ) {
	   		 if ( event.keyCode === $.ui.keyCode.TAB && $( this ).autocomplete( "instance" ).menu.active ) {
	       		 event.preventDefault();
	   		 }
			 }).autocomplete({
	   		 source: function( request, response ) {
	       		 $.getJSON( "mail.ajax.php?exec=searchrcpt", {
	           		 term: extractLast( request.term )
	       		 }, response );
	   		 },
	   		 search: function() {
	       		 // custom minLength
	       		 var term = extractLast( this.value );
	       		 if ( term.length < 2 ) {
	           		 return false;
	       		 }
	   		 },
	   		 focus: function() {
	       		 // prevent value inserted on focus
	       		 return false;
	   		 },
	   		 select: function( event, ui ) {
	       		 var terms = split( this.value );
	       		 // remove the current input
	       		 terms.pop();
	       		 // add the selected item
	       		 terms.push( ui.item.value );
	       		 // add placeholder to get the comma-and-space at the end
	       		 terms.push( "" );
	       		 this.value = terms.join( ", " );
	       		 return false;
	   		 }
		 });
	    $( "#mail_bcc" ).bind( "keydown", function( event ) {
	   		 if ( event.keyCode === $.ui.keyCode.TAB && $( this ).autocomplete( "instance" ).menu.active ) {
	       		 event.preventDefault();
	   		 }
			 }).autocomplete({
	   		 source: function( request, response ) {
	       		 $.getJSON( "mail.ajax.php?exec=searchrcpt", {
	           		 term: extractLast( request.term )
	       		 }, response );
	   		 },
	   		 search: function() {
	       		 // custom minLength
	       		 var term = extractLast( this.value );
	       		 if ( term.length < 2 ) {
	           		 return false;
	       		 }
	   		 },
	   		 focus: function() {
	       		 // prevent value inserted on focus
	       		 return false;
	   		 },
	   		 select: function( event, ui ) {
	       		 var terms = split( this.value );
	       		 // remove the current input
	       		 terms.pop();
	       		 // add the selected item
	       		 terms.push( ui.item.value );
	       		 // add placeholder to get the comma-and-space at the end
	       		 terms.push( "" );
	       		 this.value = terms.join( ", " );
	       		 return false;
	   		 }
		 });
		 function split( val ) {
			 return val.split( /,\s*/ );
		 }
		 function extractLast( term ) {
			 return split( term ).pop();
		 }
	} );
</script>

</head>
<body>

<div id="msg_content" style="display: none;"><?php echo $content;?></div>

<div style="width: 100%; overflow: hidden;">
    <div class="row col-xs-12">
        <div class="col-xs-4"<span class="glyphicons glyphicons-message-plus"></span><span style="font-size: 13px"><?=$_LANG->get('eMail')?></span></div>
        <div class="col-xs-4" style="text-align: right;"><?=$savemsg?></div>
        <div class="col-xs-2" style="text-align: right;"><span onclick="$('#exec').val('save');$('#mail_form').submit();" class="btn btn-success">Speichern</span></div>
        <div class="col-xs-2" style="text-align: right;"><span onclick="$('#mail_form').submit();" class="btn btn-info">Senden</span></div>
    </div>
    </br>
    </br>

    <form action="mail.send.frame.php" method="post" id="mail_form" name="mail_form" enctype="multipart/form-data">
        <input type="hidden" id="exec" name="exec" value="send">
        <input type="hidden" id="mailid" name="mailid" value="<?php echo $_REQUEST["mailid"];?>">
        <div class="row">
          <div class="form-group col-xs-12">
            <div class=" col-xs-1">
                <label class="control-label" for="mail_from">Von</label>
            </div>
            <div class=" col-xs-11">
              <div class="input-group">
                  <span class="input-group-addon"></span>
                  <select id="mail_from" name="mail_from" class="form-control">
                      <?php 
                      $first = true;
                      foreach ($mail_servers as $mail_server)
                      {
                          if ($first)
                          {
                              echo '<option selected value="'.$mail_server["mailid"].'">'.$mail_server["mail"].'</option>';
                              $first = false;
                          }
                          else
                              echo '<option value="'.$mail_server["mailid"].'">'.$mail_server["mail"].'</option>';
                      }
                      ?>
                  </select>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-xs-12">
            <div class=" col-xs-1">
                <label class="control-label" for="mail_to">An</label>
            </div>
            <div class=" col-xs-11">
                <div class="input-group">
                    <span class="input-group-addon">@</span>
                    <input type="text" id="mail_to" name="mail_to"
                           value="<?php if ($_REQUEST["preset"]=="RE") echo $orig_mail_from; else if ($_REQUEST["preset"]=="REALL") echo $orig_mail_fromall;?>"
                           class="form-control">
                </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-xs-12">
            <div class=" col-xs-1">
                <label class="control-label" for="mail_cc">CC</label>
            </div>
            <div class=" col-xs-11">
                <div class="input-group">
                    <span class="input-group-addon">@</span>
                    <input type="text" id="mail_cc" name="mail_cc" class="form-control">
                </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-xs-12">
            <div class=" col-xs-1">
                <label class="control-label" for="mail_bcc">BCC</label>
            </div>
            <div class=" col-xs-11">
                <div class="input-group">
                    <span class="input-group-addon">@</span>
                    <input type="text" id="mail_bcc" name="mail_bcc" class="form-control">
                </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-xs-12">
            <div class=" col-xs-1">
                <label class="control-label" for="mail_subject">Betreff</label>
            </div>
            <div class=" col-xs-11">
                <div class="input-group">
                    <span class="input-group-addon"></span>
                    <input type="text" id="mail_subject" name="mail_subject" value="<?php echo $new_subject;?>" class="form-control">
                </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-xs-12">
            <div class=" col-xs-1">
                <label class="control-label" for="mail_text">Nachricht</label>
            </div>
            <div class=" col-xs-11">
                <div class="input-group">
                    <textarea id="mail_text" name="mail_text" rows="10" class="form-control" cols="80"></textarea>
                </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-xs-12">
            <div class=" col-xs-1">
                <label class="control-label" for="mail_attachments">Anhänge</label>
            </div>
            <div class=" col-xs-11">
                <div class="input-group">
                      <span class="input-group-addon">
                      <span class="btn btn-success btn-xs fileinput-button">
                          <span>Hinzufügen...</span>
                          <input type="file" multiple="multiple" id="fileupload" name="files[]" width="100%" />
                      </span>
                      <div id="files" class="files">
                          <?php
                          if ($_REQUEST["preset"] == "FW" && count($attachments))
                          {
                              foreach ($attachments as $attachment) {
                                  echo '<p>'.$attachment['name'].'<input type="hidden" name="old_attach[]" value="'.$attachment["filename"].'"><span class="glyphicons glyphicons-remove pointer" onclick="$(this).parent().remove();"></span></p>';
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
        </div>
    </form>
</div>
</br>
</body>
</html>