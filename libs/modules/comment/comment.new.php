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
require_once 'libs/modules/comment/comment.class.php';
require_once 'libs/modules/tickets/ticket.class.php';

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
    $comment->setTitle($_REQUEST["tktc_title"]);
    $save_ok = $comment->save();
    
    if ($save_ok){
        if ($comment->getModule() == "Ticket")
        {
            $tmp_ticket = new Ticket($comment->getObjectid());
            $logentry = '<a href="#comment_'.$comment->getId().'">(#'.$comment->getId().') Neuer Kommentar </a> von ' . $comment->getCrtuser()->getNameAsLine() . '</br>';
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
                $logentry = '<a href="#comment_'.$comment->getId().'">(#'.$comment->getId().') Neuer Kommentar </a> von ' . $comment->getCrtuser()->getNameAsLine() . '</br>';
                $ticketlog = new TicketLog();
                $ticketlog->setCrtusr($_USER);
                $ticketlog->setDate(time());
                $ticketlog->setTicket($tmp_ticket);
                $ticketlog->setEntry($logentry);
                $ticketlog->save();
            }
        }
    }
    if ($save_ok && $_REQUEST["tktc_article_id"] != "" && $_REQUEST["tktc_article_amount"] != ""){
        $tc_article = new CommentArticle();
        $tc_article->setArticle(new Article($_REQUEST["tktc_article_id"]));
        $tc_article->setAmount((float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["tktc_article_amount"]))));
        $tc_article->setState(1);
        $tc_article->setComment_id($comment->getId());
        $save_ok = $tc_article->save();
        if ($save_ok){
            $comment->setArticles(Array($tc_article));

            if ($_REQUEST["stop_timer"] == 1){
                $timer = Timer::getLastUsed();
                if ($timer->getState() == Timer::TIMER_RUNNING){
                    $timer->stop();
                    $time_ok = true;
                }
                unset($timer);
            }
        }
    }
    if ($save_ok && $_REQUEST['tktc_files']){
        foreach ($_REQUEST["tktc_files"] as $file) {
            if ($file != ""){
                $tmp_attachment = new Attachment();
                $tmp_attachment->setCrtdate(time());
                $tmp_attachment->setCrtuser($_USER);
                $tmp_attachment->setModule("Comment");
                $tmp_attachment->setObjectid($comment->getId());
                $tmp_attachment->move_uploaded_file($file);
                $save_ok2 = $tmp_attachment->save();
                $savemsg = getSaveMessage($save_ok2)." ".$DB->getLastError();
                if ($save_ok2 === false){
                    break;
                }
            }
        }
    }
    if ($save_ok){
        if ($_REQUEST["tktid"]>0)
        {
            $tmp_ticket = new Ticket($_REQUEST["tktid"]);
            
            $logentry = "";
            $tmp_array = $_REQUEST["abo_notify"];
            foreach ($tmp_array as $abouser){
                $tmp_user = new User($abouser);
                if (!Abonnement::hasAbo($tmp_ticket,$tmp_user)){
                    $abo = new Abonnement();
                    $abo->setAbouser($tmp_user);
                    $abo->setModule(get_class($tmp_ticket));
                    $abo->setObjectid($tmp_ticket->getId());
                    $abo->save();
                    $logentry .= 'Abonnement hinzugefügt: ' . $tmp_user->getNameAsLine() . '</br>';
                }
                if ($tmp_user->getId() != $_USER->getId())
                {
                    Notification::generateNotification($tmp_user, get_class($tmp_ticket), "Comment", $tmp_ticket->getNumber(), $tmp_ticket->getId());
                    $logentry .= 'Benachrichtigung generiert für User: ' . $tmp_user->getNameAsLine() . '</br>';
                }
            }
            if ($logentry != ""){
                $ticketlog = new TicketLog();
                $ticketlog->setCrtusr($_USER);
                $ticketlog->setDate(time());
                $ticketlog->setTicket($tmp_ticket);
                $ticketlog->setEntry($logentry);
                $ticketlog->save();
            }
            
            echo '<script language="JavaScript">parent.$.fancybox.close();
                  parent.location.href="../../../index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid='.$tmp_ticket->getId().'";</script>';
        } else if ($_REQUEST["tktc_module"] == "BusinessContact") {
            echo '<script language="JavaScript">parent.$.fancybox.close(); parent.location.href=parent.location.href+"&tabshow=5";</script>';
        } else {
            echo '<script language="JavaScript">parent.$.fancybox.close(); parent.location.href=parent.location.href;</script>';
        }
    }
}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-bootstrap.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-halflings.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-filetypes.css" />
<link rel="stylesheet" type="text/css" href="../../../css/glyphicons-social.css" />

<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" href="../../../css/bootstrap.min.css">
<link type="text/css" href="../../../jscripts/jquery-ui-1.11.4.custom/jquery-ui.min.css" rel="stylesheet" />	
<script src="../../../jscripts/jquery/js/jquery-1.11.1.min.js"></script>
<script src="../../../jscripts/jquery-ui-1.11.4.custom/jquery-ui.min.js"></script>
<script src="../../../thirdparty/ckeditor/ckeditor.js"></script>
<script src="../../../jscripts/jvalidation/dist/jquery.validate.min.js"></script>
<script src="../../../jscripts/jvalidation/dist/localization/messages_de.min.js"></script>
<script src="../../../jscripts/moment/moment-with-locales.min.js"></script>

<!-- file upload -->
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="../../../css/jquery.fileupload.css">
<script src="../../../jscripts/jquery/js/jquery.ui.widget.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="../../../jscripts/jquery/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="../../../jscripts/jquery/js/jquery.fileupload.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
<script src="../../../jscripts/bootstrap.min.js"></script>
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
                $('#files').append('<input name="tktc_files[]" type="hidden" value="'+file.name+'"/>');
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

<script>
	$(function() {
		CKEDITOR.replace( 'tktc_comment' );
		
        $( "#tktc_article" ).autocomplete({
        	 source: "../../../libs/modules/tickets/ticket.ajax.php?ajax_action=search_article",
        	 minLength: 2,
        	 focus: function( event, ui ) {
        	 $( "#tktc_article" ).val( ui.item.label );
        	 return false;
        	 },
        	 select: function( event, ui ) {
        		 $( "#tktc_article" ).val( ui.item.label );
        		 $( "#tktc_article_id" ).val( ui.item.value );
        		 if ($("#tktc_article_amount").val() == ""){
        			 $( "#tktc_article_amount" ).val("1");
        		 }
        		 return false;
        	 }
        });
    	
      	 $( "#tktc_new_notify_user" ).autocomplete({
      		 source: "../../../libs/modules/tickets/ticket.ajax.php?ajax_action=search_user",
      		 minLength: 2,
      		 focus: function( event, ui ) {
      		 $( "#tktc_new_notify_user" ).val( ui.item.label );
      		 return false;
      		 },
      		 select: function( event, ui ) {
          		 $( "#tktc_new_notify_user" ).val( ui.item.label );
          		 $( "#abo_notify" )
          		 $('#abo_notify').append($('<option>', {
                      value: ui.item.value,
                      text: ui.item.label,
                      selected: true
                   }));
          		 $( "#tktc_new_notify_user" ).val("");
          		 return false;
      		 }
      	 });
	});
    function copyfromtimer()
    {
        var article = $("#tktc_article",parent.document).val();
        var articleid = $("#tktc_article_id",parent.document).val();
        var amount = $("#tktc_article_amount",parent.document).val();
        $("#tktc_article").val(article);
        $("#tktc_article_id").val(articleid);
        $("#tktc_article_amount").val(amount);
        $("#stop_timer").val(1);
    }
</script>
<form action="comment.new.php" method="post" class="form-horizontal" name="comment_edit" enctype="multipart/form-data">
    <input type="hidden" name="exec" value="save">
    <input type="hidden" name="tktid" value="<?php echo $_REQUEST["tktid"]; ?>">
    <input type="hidden" name="tktc_module" value="<?php echo $_REQUEST["tktc_module"]; ?>">
    <input type="hidden" name="tktc_objectid" value="<?php echo $_REQUEST["tktc_objectid"]; ?>">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                Notiz anlegen
            </h3>
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="" class="col-sm-1 control-label">Titel</label>
                <div class="col-sm-11">
                    <input name="tktc_title" type="text" class="form-control">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-1 control-label">Typ</label>
                <div class="col-sm-11">
                    <input type="radio" name="tktc_type" value="<?php echo Comment::VISABILITY_PUBLIC;?>"> Offiz. Kommentar<br>
                    <input type="radio" name="tktc_type" value="<?php echo Comment::VISABILITY_PUBLICMAIL;?>"> Offiz. Antwort (Mail)<br>
                    <input type="radio" name="tktc_type" checked value="<?php echo Comment::VISABILITY_INTERNAL;?>"> inter. Kommentar<br>
                    <input type="radio" name="tktc_type" value="<?php echo Comment::VISABILITY_PRIVATE;?>"> priv. Kommentar
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-12">
                    <textarea name="tktc_comment" class="form-control" id="tktc_comment" rows="10" cols="80"></textarea>
                </div>
            </div>
        <?php
        if ($_REQUEST["tktid"]>0)
        {
        $tmp_ticket = new Ticket($_REQUEST["tktid"]);
        ?>
        <div class="form-group">
            <label for="" class="col-sm-3 control-label"></label>
            <div class="col-sm-9">
                <input name="" id="" value="" class="form-control">
            </div>
        </div>
    </div>




	<table style="width:100%">
      <tr>
          <td width="25%">Benachrichtigen:</td>
          <td width="25%">
              <script type="text/javascript">
	              function select_all()
	              {
	            	$('#abo_notify').children().each(function(index,item){
	            		$(this).attr('selected', 'selected');
            		});
	              }
	              function deselect_all()
	              {
	            	$('#abo_notify').children().each(function(index,item){
	            		$(this).removeAttr('selected');
            		});
	              }
              </script>
              <span class="pointer" onclick="select_all();">alle</span> - <span class="pointer" onclick="deselect_all();">keiner</span></br>
              <select id="abo_notify" name="abo_notify[]" size="5" multiple> 
                 <?php  
                    if (Abonnement::hasAbo($tmp_ticket))
                    {
                        $abonnoments = Abonnement::getAbonnementsForObject(get_class($tmp_ticket), $tmp_ticket->getId());
                        foreach ($abonnoments as $abonnoment){
                            echo '<option value="'.$abonnoment->getAbouser()->getId().'">'.$abonnoment->getAbouser()->getNameAsLine().'</option>';
                        }
                    }
                 ?>
              </select></br>
              <b>Hinzufügen:</b></br>
              <input type="text" id="tktc_new_notify_user"></br>
          </td>
      </tr>
      <?php }?>
      <tr>
          <td>&nbsp;</td><td>&nbsp;</td>
      </tr>
      <tr>
          <td width="25%">Tätigkeit Nr.:</td>
          <td width="25%">
              <input type="text" id="tktc_article" name="tktc_article"/> Menge: <input type="text" id="tktc_article_amount" name="tktc_article_amount"/>
              <input type="hidden" id="tktc_article_id" name="tktc_article_id"/></br>
          </td>
      </tr>
        <tr>
            <td width="25%"><input type="hidden" id="stop_timer" name="stop_timer" value="0"></td>
            <td width="25%"><a href="#" onclick="copyfromtimer();">aus gestopptem Timer übernehmen</a></td>
        </tr>
      <tr>
          <td width="25%">Anhänge:</td>
          <td width="25%" valign="top">
              <span class="btn btn-success btn-xs fileinput-button">
                  <span>Hinzufügen...</span>
                  <input type="file" multiple="multiple" id="fileupload" name="files[]" width="100%" />
              </span>
              <div id="files" class="files"></div>
              <div id="progress" class="progress">
                  <div class="progress-bar progress-bar-success"></div>
              </div>
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
</html>