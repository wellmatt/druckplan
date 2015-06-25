<?php
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			22.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
chdir("../../../");
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once("libs/basic/countries/country.class.php");
// require_once 'libs/modules/attachment/attachment.class.php';
require_once 'libs/modules/comment/comment.class.php';
require_once 'libs/modules/tickets/ticket.class.php';
// error_reporting(-1);
// ini_set('display_errors', 1);

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


if ($_USER == false){
	error_log("Login failed (basic-importer.php)");
	die("Login failed");
}

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

// error_reporting(-1);
// ini_set('display_errors', 1);
	
if($_REQUEST["exec"] == "save" && $_REQUEST["tktc_module"] && $_REQUEST["tktc_objectid"]){
    $comment = new Comment();
    $comment->setComment($_REQUEST["tktc_comment"]);
    $comment->setCrtuser($_USER);
    $comment->setCrtdate(time());
    $comment->setState(1);
    $comment->setModule($_REQUEST["tktc_module"]);
    $comment->setObjectid($_REQUEST["tktc_objectid"]);
    $comment->setVisability((int)$_REQUEST["tktc_type"]);
    $save_ok = $comment->save();
    
    if ($save_ok && $_FILES['tktc_attachments']){
        if ($comment->getModule() == "Ticket")
        {
            $tmp_ticket = new Ticket($comment->getObjectid());
            $logentry = 'Neues <a href="#comment_'.$comment->getId().'">Kommentar (#'.$comment->getId().')</a> von ' . $comment->getCrtuser()->getNameAsLine() . '</br>';
            $ticketlog = new TicketLog();
            $ticketlog->setCrtusr($_USER);
            $ticketlog->setDate(time());
            $ticketlog->setTicket($tmp_ticket);
            $ticketlog->setEntry($logentry);
            $ticketlog->save();
        } else if ($comment->getModule() == "Comment")
        {
            $tmp_comment = new Comment($comment->getObjectid());
            if ($tmp_comment->getModule() == "Ticket")
            {
                $tmp_ticket = new Ticket($tmp_comment->getObjectid());
                $logentry = 'Neues <a href="#comment_'.$comment->getId().'">Kommentar (#'.$comment->getId().')</a> von ' . $comment->getCrtuser()->getNameAsLine() . '</br>';
                $ticketlog = new TicketLog();
                $ticketlog->setCrtusr($_USER);
                $ticketlog->setDate(time());
                $ticketlog->setTicket($tmp_ticket);
                $ticketlog->setEntry($logentry);
                $ticketlog->save();
            }
        }
        
        $file_ary = reArrayFiles($_FILES['tktc_attachments']);
    
        foreach ($file_ary as $file) {
            if ($file["name"] != ""){
                $tmp_attachment = new Attachment();
                $tmp_attachment->setCrtdate(time());
                $tmp_attachment->setCrtuser($_USER);
                $tmp_attachment->setModule("Comment");
                $tmp_attachment->setObjectid($comment->getId());
                $tmp_attachment->move_save_file($file);
                $save_ok = $tmp_attachment->save();
                $savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
                if ($save_ok === false){
                    break;
                }
            }
        }
    }
    if ($save_ok){
        $tmp_ticket = new Ticket($_REQUEST["tktid"]);
        Notification::generateNotificationsFromAbo(get_class($tmp_ticket), "Comment", $tmp_ticket->getNumber(), $tmp_ticket->getId());
        echo '<script language="JavaScript">parent.$.fancybox.close(); 
              parent.location.href="../../../index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid='.$tmp_ticket->getId().'";</script>';
    }
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<!-- jQuery -->
<link type="text/css" href="../../../jscripts/jquery/css/smoothness/jquery-ui-1.8.18.custom.css" rel="stylesheet" />	
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="../../../jscripts/jquery/js/jquery-ui-1.8.18.custom.min.js"></script>
<!-- /jQuery -->
<script src="../../../thirdparty/ckeditor/ckeditor.js"></script>
<script language="javascript" src="../../../jscripts/basic.js"></script>
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />

<script>
	$(function() {
		CKEDITOR.replace( 'tktc_comment' );
	});
</script>

<div class="box1" style="text-align: center;">
<br>
<form action="comment.new.php" method="post" name="comment_edit" enctype="multipart/form-data">
	<input type="hidden" name="exec" value="save">
	<input type="hidden" name="tktid" value="<?php echo $_REQUEST["tktid"];?>">
	<input type="hidden" name="tktc_module" value="<?php echo $_REQUEST["tktc_module"];?>">
	<input type="hidden" name="tktc_objectid" value="<?php echo $_REQUEST["tktc_objectid"];?>">
	<table style="width:100%">
      <tr>
          <td width="25%">Typ:</td>
          <td width="75%">
                <input type="radio" name="tktc_type" value="<?php echo Comment::VISABILITY_PUBLIC;?>"> Offiz. Kommentar<br>
                <input type="radio" name="tktc_type" value="<?php echo Comment::VISABILITY_PUBLICMAIL;?>"> Offiz. Antwort (Mail)<br>
                <input type="radio" name="tktc_type" checked value="<?php echo Comment::VISABILITY_INTERNAL;?>"> inter. Kommentar<br>
                <input type="radio" name="tktc_type" value="<?php echo Comment::VISABILITY_PRIVATE;?>"> priv. Kommentar
          </td>
      </tr>
      <tr>
          <td width="100%" colspan="2"><textarea name="tktc_comment" id="tktc_comment" rows="10" cols="80"></textarea></td>
      </tr>
      <tr>
          <td width="25%">Anhänge:</td>
          <td width="25%">
              <input type="file" multiple="multiple" name="tktc_attachments[]" width="100%" />
          </td>
      </tr>
	</table>
	
	<table width="100%">
	    <colgroup>
	        <col width="180">
	        <col>
	    </colgroup> 
	    <tr>
	        <td class="content_row_header">
	        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
	        			onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
	        </td>
	        <td class="content_row_clear" align="right">
	        	<input type="submit" value="<?=$_LANG->get('Speichern')?>">
	        </td>
	    </tr>
	</table>
</form>
</div>