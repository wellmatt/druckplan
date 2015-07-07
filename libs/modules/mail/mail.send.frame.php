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
    if ($_FILES['mail_attachments']) {
        $file_ary = reArrayFiles($_FILES['mail_attachments']);
                
        foreach ($file_ary as $file) {
            if ($file["name"] != ""){
                $mail->addAttachment($file["tmp_name"], $file["name"], $file["type"]);
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
        $mail->addHeader('CC', $mail_to);
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
                $mail->addRecipients($recipients);
        }
    } else {
        $mail->addRecipients($mail_to);
    }
    
    // Send the mail
    $mail->send($mailer);
    
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

if ($_REQUEST["preset"] == "FW" || $_REQUEST["preset"] == "RE")
{
    $mailadress = new Emailaddress($_REQUEST["mailid"]);
    
    $server = $mailadress->getHost();
    $port = $mailadress->getPort();
    $user = $mailadress->getAddress();
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
        
        $orig_mail_from = $list->first()->getEnvelope()->from->__toString();
        $orig_mail_subject = $list->first()->getEnvelope()->subject;
        $orig_mail_date = date("d.m.Y H:i",$list->first()->getEnvelope()->date->__toString());
        $orig_mail_to = $list->first()->getEnvelope()->to->__toString();
    
        $part = $list->first()->getStructure();
    
        $id = $part->findBody('html');
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
      <div class="col-xs-4"><img src="../../../images/icons/mail--plus.png"><span style="font-size: 13px"><?=$_LANG->get('eMail')?></span></div>
      <div class="col-xs-4" style="text-align: right;"><?=$savemsg?></div>
      <div class="col-xs-4" style="text-align: right;"><span onclick="$('#mail_form').submit();" class="btn btn-success">Senden</span></div>
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
                          if (!filter_var($mail_server["mail"], FILTER_VALIDATE_EMAIL))
                          {
                              $mail_server["mail"] = $mail_server["mail"] . $perf->getMail_domain();
                          }
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
                    <input type="text" id="mail_to" name="mail_to" value="<?php if ($_REQUEST["preset"]=="RE") echo $orig_mail_from;?>" class="form-control">
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
                    <span class="input-group-addon"></span>
                    <input type="file" multiple="multiple" id="mail_attachments" name="mail_attachments[]">
                </div>
            </div>
          </div>
        </div>
    </form>
</div>
</br>
</body>
</html>