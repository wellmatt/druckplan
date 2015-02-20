<?php
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			19.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once ('libs/modules/tickets/ticket.class.php');

global $_USER;

$_REQUEST["tktid"] = (int)$_REQUEST["tktid"];

if($_REQUEST["exec"] == "edit"){
    $ticket = new Ticket($_REQUEST["tktid"]);
    $header_title = $_LANG->get('Ticketdetails');
    $timer = Timer::getLastUsed();

    if($_REQUEST["subexec"] == "save"){
        if ($_REQUEST["tktc_comment_frame"] == ""){
            $_REQUEST["tktc_comment_frame"] = "kein Kommentar";
        }
        $ticketcomment = new Comment();
        $ticketcomment->setComment($_REQUEST["tktc_comment_frame"]);
        if ($ticket->getId() <= 0){
            $ticketcomment->setTitle("Ticket wurde erstellt");
        } elseif ($_REQUEST["tkt_assigned"] != "0"){
            $ticketcomment->setTitle("Ticket ".$new_assiged." zugewiesen");
        }
        $ticketcomment->setCrtuser($_USER);
        $ticketcomment->setCrtdate(time());
        $ticketcomment->setState(1);
        $ticketcomment->setModule("Ticket");
        $ticketcomment->setObjectid($ticket->getId());
        $ticketcomment->setVisability((int)$_REQUEST["tktc_type"]);
        $save_ok = $ticketcomment->save();
        $savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
        echo $savemsg . "</br>";
        if ($save_ok){
            $participants = Comment::getObjectParticipants(get_class($ticket),$ticket->getId());
            if (count($participants) > 0){
                foreach ($participants as $participant){
                    if ($participant->getId() != $_USER->getId()){
                        Notification::generateNotification($participant, get_class($ticket), "Comment", $ticket->getNumber(), $ticket->getId());
                    }
                }
            }
        }
        if ($save_ok && $_REQUEST["tktc_article_id"] != "" && $_REQUEST["tktc_article_amount"] != ""){
            $tc_article = new CommentArticle();
            $tc_article->setArticle(new Article($_REQUEST["tktc_article_id"]));
            $tc_article->setAmount($_REQUEST["tktc_article_amount"]);
            $tc_article->setState(1);
            $tc_article->setComment_id($ticketcomment->getId());
            $save_ok = $tc_article->save();
            $savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
            echo $savemsg . "</br>";
            if ($save_ok){
                $ticketcomment->setArticles(Array($tc_article));
            }
        }
        if ($save_ok && $_REQUEST["stop_timer"] == 1){
            $timer = Timer::getLastUsed();
            if ($timer->getState() == Timer::TIMER_RUNNING){
                $timer->stop();
                $time_ok = true;
            }
        }
        unset($timer);
        
        $save_ok = $ticketcomment->save();
        $savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
        echo $savemsg . "</br>";
        if ($save_ok) {
            if ($_FILES['tktc_attachments']) {
                $file_ary = reArrayFiles($_FILES['tktc_attachments']);

                foreach ($file_ary as $file) {
                    if ($file["name"] != ""){
                        $tmp_attachment = new Attachment();
                        $tmp_attachment->setCrtdate(time());
                        $tmp_attachment->setCrtuser($_USER);
                        $tmp_attachment->setModule("Comment");
                        $tmp_attachment->setObjectid($ticketcomment->getId());
                        $tmp_attachment->move_save_file($file);
                        $save_ok = $tmp_attachment->save();
                        $savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
                        if ($save_ok === false){
                            break;
                        }
                    }
                }
            }
        }
        echo '<script language="JavaScript">parent.$.fancybox.close(); parent.location.href="../../../index.php?page=libs/modules/tickets/ticket.php&exec=edit&tktid='.$_REQUEST["this_tktid"].'&start_timer=1";</script>';
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

<script language="javascript" src="../../../jscripts/basic.js"></script>
<script language="javascript" src="../../../jscripts/loadingscreen.js"></script>
<!-- FancyBox -->
<script	type="text/javascript" src="../../../jscripts/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<script	type="text/javascript" src="../../../jscripts/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="../../../jscripts/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

<link rel="stylesheet" type="text/css" href="../../../jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="../../../jscripts/datetimepicker/jquery.datetimepicker.js"></script>

<script src="../../../thirdparty/ckeditor/ckeditor.js"></script>
<script src="../../../jscripts/jvalidation/dist/jquery.validate.min.js"></script>
<script src="../../../jscripts/jvalidation/dist/localization/messages_de.min.js"></script>

<script type="text/javascript" src="../../../jscripts/moment/moment-with-locales.min.js"></script>

<script language="JavaScript">
	$(function() {
		var editor = CKEDITOR.replace( 'tktc_comment_frame', {
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
	} );
</script>
<script language="JavaScript">
$(function() {
	 $( "#tktc_article" ).autocomplete({
		 source: "../../../libs/modules/tickets/ticket.ajax.php?ajax_action=search_article",
		 minLength: 2,
		 focus: function( event, ui ) {
		 $( "#tktc_article" ).val( ui.item.label );
		 return false;
		 },
		 select: function( event, ui ) {
			 CKEDITOR.instances.tktc_comment_frame.focus();
    		 $( "#tktc_article" ).val( ui.item.label );
    		 $( "#tktc_article_id" ).val( ui.item.value );
    		 if ($("#tktc_article_amount").val() == ""){
    			 $( "#tktc_article_amount" ).val("1");
    		 }
    		 return false;
		 }
		 });
});
</script>
<script language="JavaScript">
$(document).ready(function () {
    $('#ticket_frame_form').validate({
        rules: {
            'tktc_comment_frame': {
                required: false
            },
            'tktc_article_id': {
            	required: true
            },
            'tktc_article': {
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
    CKEDITOR.on('instanceReady', function () {
        $.each(CKEDITOR.instances, function (instance) {
            CKEDITOR.instances[instance].document.on("keyup", CK_jQ);
            CKEDITOR.instances[instance].document.on("paste", CK_jQ);
            CKEDITOR.instances[instance].document.on("keypress", CK_jQ);
            CKEDITOR.instances[instance].document.on("blur", CK_jQ);
            CKEDITOR.instances[instance].document.on("change", CK_jQ);
        });
    });

    function CK_jQ() {
        for (instance in CKEDITOR.instances) {
            CKEDITOR.instances[instance].updateElement();
        }
    }
});
</script>
<script language="JavaScript">
$(document).ready(function () {
	var sec = moment().unix();
	var start = parseInt(<?php echo $timer->getStarttime();?>);
	$( "#tktc_article_amount" ).val(precise_round((sec-start)/60/60,2));
	$( "#tktc_article" ).focus();
	
});

function precise_round(num, decimals) {
	var t=Math.pow(10, decimals);   
	return (Math.round((num * t) + (decimals>0?1:0)*(Math.sign(num) * (10 / Math.pow(100, decimals)))) / t).toFixed(decimals);
}
</script>

<form action="ticket.commentframe.php" method="post" name="ticket_frame_form">
<input type="hidden" name="exec" value="edit"> 
<input type="hidden" name="subexec" value="save"> 
<input type="hidden" name="tktid" value="<?=$ticket->getId()?>">
<input type="hidden" name="this_tktid" value="<?=$_REQUEST["this_tktid"]?>">
<input type="hidden" name="stop_timer" value="1">


<div class="box1">
     <table width="100%" border="1">
         <tr>
             <td rowspan="6" width="50%">
                 <textarea name="tktc_comment_frame" id="tktc_comment_frame" rows="10" cols="80"></textarea>
             </td>
         </tr>
	     <tr>
	          <td width="25%">Tätigkeit Nr.:</td>
	          <td width="25%">
	              <input type="text" id="tktc_article" name="tktc_article"/> Menge: <input type="text" id="tktc_article_amount" name="tktc_article_amount"/>
                  <input type="hidden" id="tktc_article_id" name="tktc_article_id"/>
	          </td>
	     </tr>
	     <tr>

	          <td width="25%">Kommentar Typ:</td>
	          <td width="25%">
                <input type="radio" name="tktc_type" checked value="<?php echo Comment::VISABILITY_INTERNAL;?>"> inter. Kommentar<br>
                <input type="radio" name="tktc_type" value="<?php echo Comment::VISABILITY_PUBLIC;?>"> Offiz. Antwort<br>
                <input type="radio" name="tktc_type" value="<?php echo Comment::VISABILITY_PRIVATE;?>"> priv. Kommentar
	          </td>
	     </tr>
	     <tr>
	          <td width="25%">Anhänge:</td>
	          <td width="25%">
	              <input type="file" multiple="multiple" name="tktc_attachments[]" width="100%" />
	          </td>
	     </tr>
	     <?php if ($new_ticket == false){?>
	     <tr>
	          <td width="25%">neuen MA zuweisen:</td>
	          <td width="25%">
                <select name="tkt_assigned" id="tkt_assigned" style="width:160px" required>
                <option value="0" selected>&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
                <option disabled>-- Users --</option>
                <?php 
                $all_user = User::getAllUser(User::ORDER_NAME);
                $all_groups = Group::getAllGroups(Group::ORDER_NAME);
                foreach ($all_user as $tkt_user){
                    echo '<option value="u_'.$tkt_user->getId().'">'.$tkt_user->getNameAsLine().'</option>';
                }
                ?>
                <option disabled>-- Groups --</option>
                <?php 
                foreach ($all_groups as $tkt_groups){
                    echo '<option value="g_'.$tkt_groups->getId().'">'.$tkt_groups->getName().'</option>';
                }
                ?>
                </select>
	          </td>
	     </tr>
	     <?php } ?>
         
     </table>
     
     <table width="100%">
        <tr>
            <td class="content_row_clear" align="right">
            	<input type="submit" value="<?=$_LANG->get('Speichern')?>">
            </td>
        </tr>
     </table>
</div>
<br>
</form>