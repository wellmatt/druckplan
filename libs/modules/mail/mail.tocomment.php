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
require_once 'libs/modules/comment/comment.class.php';
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/attachment/attachment.class.php';


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

if ($_REQUEST["exec"] == "save")
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
        
        $orig_mail_from = $list->first()->getEnvelope()->from->__toString();
        $orig_mail_subject = $list->first()->getEnvelope()->subject;
        $orig_mail_date = date("d.m.Y H:i",$list->first()->getEnvelope()->date->__toString());
        $orig_mail_to = $list->first()->getEnvelope()->to->__toString();
    
        $part = $list->first()->getStructure();
    
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
            $attachments[] = $new_attachment;
        }

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
    
        $content =  '<br><hr>'.
                    'Von: '.$orig_mail_from.'<br>
                    Gesendet: '.$orig_mail_date.'<br>
                    An: '.$orig_mail_to.'<br>
                    Betreff: '.$orig_mail_subject.'<br><br>' . $content;
        
        $comment = new Comment();
	    $comment->setState(1);
	    $comment->setVisability(Comment::VISABILITY_INTERNAL);
	    $comment->setComment($content);
	    $comment->setTitle("Kommentar aus eMail generiert");
        $comment->setCrtuser($_USER);
        $comment->setCrtdate(time());
        $comment->setModule("Ticket");
        $comment->setObjectid((int)$_REQUEST["ticket_id"]);
	    $save_ok = $comment->save();
	    
        if ($save_ok && count($attachments)>0){
            foreach ($attachments as $attachment) {
                                
                $uid = new Horde_Imap_Client_Ids( $_REQUEST["muid"] );
                $mime_id = $attachment["mime_id"];
                
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
                
                $name = $attachment["name"];
                
                $destination = __DIR__."/../../../docs/attachments/";
                
                $filename = md5($attachment["name"].time());
                $new_filename = $destination.$filename;
                
                if(!file_exists($new_filename)) {
                    $fh = fopen($new_filename, "w");
                    fwrite($fh, $image_data_decoded);
                    fclose($fh);
                }
                
                $tmp_attachment = new Attachment();
                $tmp_attachment->setCrtdate(time());
                $tmp_attachment->setCrtuser($_USER);
                $tmp_attachment->setModule("Comment");
                $tmp_attachment->setObjectid($comment->getId());
                $tmp_attachment->setFilename($filename);
                $tmp_attachment->setOrig_filename($attachment["name"]);
                $save_ok = $tmp_attachment->save();
                $savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
                if ($save_ok === false){
                    break;
                }
            }
        }
	    if ($save_ok){
	        $tmp_ticket = new Ticket($comment->getObjectid());
            Notification::generateNotificationsFromAbo(get_class($tmp_ticket), "Comment", $tmp_ticket->getNumber(), $tmp_ticket->getId());
// 	        echo '<script language="JavaScript">parent.$.fancybox.close(); parent.location.href="../../../index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid='.$comment->getObjectid().'";</script>';
	    }
    
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
<!-- FancyBox -->
<script	type="text/javascript" src="../../../jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script	type="text/javascript" src="../../../jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="../../../jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
</head>
<body>

<script language="JavaScript" >
$(function() {
	 $( "#ticket" ).autocomplete({
		 source: "../mail/mail.ajax.php?ajax_action=search_ticket",
		 minLength: 2,
		 focus: function( event, ui ) {
    		 $( "#ticket" ).val( ui.item.label );
    		 return false;
		 },
		 select: function( event, ui ) {
    		 $( "#ticket" ).val( ui.item.label );
    		 $( "#ticket_id" ).val( ui.item.value );
    		 return false;
		 }
	 });
});
</script>

<form action="mail.tocomment.php" method="post" name="tocomment_form">
<input type="hidden" name="exec" value="save">
<input type="hidden" name="mailid" value="<?php echo $_REQUEST["mailid"];?>">
<input type="hidden" name="mailbox" value="<?php echo $_REQUEST["mailbox"];?>">
<input type="hidden" name="muid" value="<?php echo $_REQUEST["muid"];?>">
<table width="100%">
    <tr>
        <td width="300" class="content_header">
            <h1><img src="../../../images/icons/node-select.png"> <?php echo 'Mail-to-Comment';?></h1>
        </td>
    </tr>
</table>

<input type="submit" value="<?php echo $_LANG->get('Speichern');?>" class="text">

<div class="box1"> 
		<table id="association_table" width="500">
    		<tr>
    		    <td class="content_header"><?php echo $_LANG->get('Ticket');?></td>
    			<td class="content_row_clear">
    			     <input type="text" id="ticket" name="ticket" value="" style="width:160px"/>
                     <input type="hidden" id="ticket_id" name="ticket_id" value=""/>
                </td>
    		</tr>
		</table>
		<br>
</div>
<br>
</form>
