<?php 

global $_USER;
global $_CONTACTPERSON;

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

$_REQUEST["tktid"] = (int)$_REQUEST["tktid"];

$new_ticket = false;
if($_REQUEST["exec"] == "new"){
    $new_ticket = true;
    $ticket = new Ticket();
    $header_title = $_LANG->get('Ticket erstellen');
}

if($_REQUEST["exec"] == "edit"){
    $ticket = new Ticket($_REQUEST["tktid"]);
    $header_title = $_LANG->get('Ticketdetails');
    
    if($_REQUEST["subexec"] == "save"){
        $ticket->setTitle($_REQUEST["tkt_title"]);
        if ($_REQUEST["tkt_due"] != "" && $_REQUEST["tkt_due"] != 0){
            $ticket->setDuedate(strtotime($_REQUEST["tkt_due"]));
        } else {
            $ticket->setDuedate(0);
        }
        $ticket->setCustomer(new BusinessContact($_REQUEST["tkt_customer_id"]));
        $ticket->setCustomer_cp(new ContactPerson($_REQUEST["tkt_customer_cp_id"]));
        $ticket->setCategory(new TicketCategory((int)$_REQUEST["tkt_category"]));
        if ($ticket->getCategory()->getId() == 1){
            $ticket->setState(new TicketState(3));
        } else {
            $ticket->setState(new TicketState((int)$_REQUEST["tkt_state"]));
        }
        $ticket->setPriority(new TicketPriority((int)$_REQUEST["tkt_prio"]));
        $ticket->setSource((int)$_REQUEST["tkt_source"]);
        if ($ticket->getId() > 0){
            $ticket->setEditdate(time());
        }
        $ticket->setCrtuser($_USER);
        $save_ok = $ticket->save();
        if ($_REQUEST["tktid"] == NULL || $_REQUEST["tktid"] == "" || !$_REQUEST["tktid"])
        {
            Notification::generateNotification($_USER, "Ticket", "NewFromCP", $ticket->getNumber(), $ticket->getId());
        }
        $savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
        if ($save_ok){
            if ($_REQUEST["tktc_comment"] == ""){
                $_REQUEST["tktc_comment"] = "kein Kommentar";
            }
            $ticketcomment = new Comment();
            $ticketcomment->setComment($_REQUEST["tktc_comment"]);
            if ($ticket->getId() <= 0){
                $ticketcomment->setTitle("Ticket wurde erstellt");
            }
            $ticketcomment->setCrtcp($_CONTACTPERSON);
            $ticketcomment->setCrtdate(time());
            $ticketcomment->setState(1);
            $ticketcomment->setModule("Ticket");
            $ticketcomment->setObjectid($ticket->getId());
            $ticketcomment->setVisability(Comment::VISABILITY_PUBLIC);
            $save_ok = $ticketcomment->save();
            $savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
            if ($save_ok){
                Notification::generateNotificationsFromAbo(get_class($ticket), "CommentCP", $ticket->getNumber(), $ticket->getId());
            }
            if ($save_ok && $_REQUEST["tktc_article_id"] != "" && $_REQUEST["tktc_article_amount"] != ""){
                $tc_article = new CommentArticle();
                $tc_article->setArticle(new Article($_REQUEST["tktc_article_id"]));
                $tc_article->setAmount($_REQUEST["tktc_article_amount"]);
                $tc_article->setState(1);
                $tc_article->setComment_id($ticketcomment->getId());
                $save_ok = $tc_article->save();
                $savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
                if ($save_ok){
                    $ticketcomment->setArticles(Array($tc_article));
                }
            }
            $save_ok = $ticketcomment->save();
            $savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
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
        }
    }
}

?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>

<link rel="stylesheet" type="text/css" href="../../../jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="../../../jscripts/datetimepicker/jquery.datetimepicker.js"></script>

<script src="../../../thirdparty/ckeditor/ckeditor.js"></script>
<script src="../../../jscripts/jvalidation/dist/jquery.validate.min.js"></script>
<script src="../../../jscripts/jvalidation/dist/localization/messages_de.min.js"></script>

<script language="JavaScript">
	$(function() {
		var editor = CKEDITOR.replace( 'tktc_comment', {
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
	$('#tkt_due').datetimepicker({
		 lang:'de',
		 i18n:{
		  de:{
		   months:[
		    'Januar','Februar','März','April',
		    'Mai','Juni','Juli','August',
		    'September','Oktober','November','Dezember',
		   ],
		   dayOfWeek:[
		    "So.", "Mo", "Di", "Mi", 
		    "Do", "Fr", "Sa.",
		   ]
		  }
		 },
		 timepicker:true,
		 format:'d.m.Y H:i'
	});
});
</script>
<script language="JavaScript">
$(document).ready(function () {
    $('#ticket_edit').validate({
        rules: {
            'tktc_comment': {
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


<body>


<div class="ticket_view">
<table width="100%">
	<tr>
		<td class="content_header">
			 <h2><?=$header_title?></h2>
		</td>
		<td align="right">
			<?=$savemsg?>
		</td>
	</tr>
</table>

  <table border="0" cellpadding="2" cellspacing="0" width="100%">
        <tbody>
        	<tr>
                <td width="50%">
                     <h3><?php if ($ticket->getId()>0){?><a href="index.php?pid=20&exec=edit&tktid=<?=$ticket->getId()?>" title="Reload">
                     <i class="icon-refresh"></i> Ticket #<?php echo $ticket->getNumber();?> - <?php echo $ticket->getTitle();?></a><?php } ?></h3>
                </td>
                <td width="50%" align="right">&nbsp;
                </td>
        	</tr>
        </tbody>
  </table>
  
  <form action="index.php?pid=20" method="post" name="ticket_edit" id="ticket_edit" enctype="multipart/form-data">
  <input type="hidden" name="exec" value="edit"> 
  <input type="hidden" name="subexec" value="save"> 
  <input type="hidden" name="tktid" value="<?=$ticket->getId()?>">
  
  <div class="ticket_header">
  	<table width="100%" border="1">
      <tr>
        <td width="25%">Titel:</td>
        <td width="25%">
            <input type="text" id="tkt_title" name="tkt_title" value="<?php echo $ticket->getTitle();?>" style="width:160px" required/>
        </td>
        <td width="25%">Kunde:</td>
        <td width="25%">
            <input type="text" id="tkt_customer" name="tkt_customer" value="<?php if ($new_ticket == false) { 
                                                                                        echo $ticket->getCustomer()->getNameAsLine()." - ".$ticket->getCustomer_cp()->getNameAsLine2(); 
                                                                                    } else {
                                                                                        echo $_BUSINESSCONTACT->getNameAsLine()." - ".$_CONTACTPERSON->getNameAsLine2();
                                                                                    }?>" style="width:160px" disabled/>
            <input type="hidden" id="tkt_customer_id" name="tkt_customer_id" value="<?php if ($new_ticket == true) { echo $_BUSINESSCONTACT->getId(); } else { echo $ticket->getCustomer()->getId();}?>"/>
            <input type="hidden" id="tkt_customer_cp_id" name="tkt_customer_cp_id" value="<?php if ($new_ticket == true) { echo $_CONTACTPERSON->getId(); } else { echo $ticket->getCustomer_cp()->getId();}?>"/>
        </td>
      </tr>
      <tr>
        <td width="25%">Kategorie:</td>
        <td width="25%">
            <select name="tkt_category" id="tkt_category" style="width:160px" required>
            <?php 
            $tkt_all_categories = TicketCategory::getAllCategories();
            foreach ($tkt_all_categories as $tkt_category){
                if ($ticket->getId()>0){
                    if ($ticket->getCategory() == $tkt_category){
                        echo '<option value="'.$tkt_category->getId().'" selected>'.$tkt_category->getTitle().'</option>';
                    }
                } else {
                    if ($_CONTACTPERSON->TC_cancreate($tkt_category))
                        echo '<option value="'.$tkt_category->getId().'">'.$tkt_category->getTitle().'</option>';
                }
            }
            ?>
            </select>
        </td>
        <td width="25%">Telefon:</td>
        <td width="25%"><div id="cp_phone"><?php if ($new_ticket == false) { echo $ticket->getCustomer_cp()->getPhone(); } else { $_CONTACTPERSON->getPhone(); }?></div></td>
      </tr>
      <tr>
        <td width="25%">Status:</td>
        <td width="25%">
            <select name="tkt_state" id="tkt_state" style="width:160px" required>
            <?php 
            $tkt_all_states = TicketState::getAllStates();
            foreach ($tkt_all_states as $tkt_state){
                if ($tkt_state->getId() != 1){
                    if ($ticket->getId() == 0 && $tkt_state->getId() == 2){
                        echo '<option value="'.$tkt_state->getId().'" selected>'.$tkt_state->getTitle().'</option>';
                    } else if ($ticket->getState() == $tkt_state){
                        echo '<option value="'.$tkt_state->getId().'" selected>'.$tkt_state->getTitle().'</option>';
                    } else {
                        echo '<option value="'.$tkt_state->getId().'">'.$tkt_state->getTitle().'</option>';
                    }
                }
            }
            ?>
            </select>
        </td>
        <td width="25%">eMail-Adresse:</td>
        <td width="25%"><div id="cp_mail"><?php if ($new_ticket == false) { echo $ticket->getCustomer_cp()->getEmail();} else { $_CONTACTPERSON->getEmail(); }?></div></td>
      </tr>
      <tr>
        <td width="25%">Priorität:</td>
        <td width="25%">
            <select name="tkt_prio" id="tkt_prio" style="width:160px" required>
            <?php 
            $tkt_all_prios = TicketPriority::getAllPriorities();
            foreach ($tkt_all_prios as $tkt_prio){
                if ($ticket->getPriority() == $tkt_prio){
                    echo '<option value="'.$tkt_prio->getId().'" selected>'.$tkt_prio->getTitle().' ('.$tkt_prio->getValue().') </option>';
                } else {
                    echo '<option value="'.$tkt_prio->getId().'">'.$tkt_prio->getTitle().' ('.$tkt_prio->getValue().') </option>';
                }
            }
            ?>
            </select>
        </td>
        <td width="25%">Herkunft:</td>
        <td width="25%">
            <select name="tkt_source" id="tkt_source" style="width:160px" required>
                <option value="<?php echo Ticket::SOURCE_EMAIL?>" <?php if ($ticket->getSource() == Ticket::SOURCE_EMAIL) echo "selected"; ?>>per E-Mail</option>
                <option value="<?php echo Ticket::SOURCE_PHONE?>" <?php if ($ticket->getSource() == Ticket::SOURCE_PHONE) echo "selected"; ?>>per Telefon</option>
                <option value="<?php echo Ticket::SOURCE_OTHER?>" <?php if ($ticket->getSource() == Ticket::SOURCE_OTHER || $ticket->getId() == 0) echo "selected"; ?>>andere</option>
            </select>
        </td>
      </tr>
      <tr>
        <td width="25%">Fälligkeitsdatum:</td>
        <td width="25%">
			<input type="text" style="width:160px" id="tkt_due" name="tkt_due"
			class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
			onfocus="markfield(this,0)" onblur="markfield(this,1)"
			value="<?if($ticket->getDuedate() != 0){ echo date('d.m.Y H:i', $ticket->getDuedate());} elseif ($ticket->getId()==0) { echo date('d.m.Y H:i'); }?>"/>
            <input type="checkbox" id="tkt_due_enabled" name="tkt_due_enabled" value="1" onclick="JavaScript: if ($('#tkt_due_enabled').prop('checked')) {$('#tkt_due').val('')};" 
             <?php if ($ticket->getDuedate()==0 && $ticket->getId()!=0) echo " checked ";?>/> ohne
        </td>
        <td width="25%">Letzte Mitteilung:</td>
        <td width="25%"><?php if ($ticket->getId()>0 && $ticket->getEditdate() > 0) echo date("d.m.Y H:i",$ticket->getEditdate());?>&nbsp;</td>
      </tr>
      <tr>
        <td width="25%">Erstellt am:</td>
        <td width="25%"><?php if ($ticket->getId()>0) echo date("d.m.Y H:i",$ticket->getCrtdate())?>&nbsp;</td>
        <td width="25%">&nbsp;</td>
        <td width="25%">&nbsp;</td>
      </tr>
      <tr>
        <td width="25%">Zugewiesen an:</td>
        <td width="25%">
        <?php 
        if ($ticket->getId() > 0) {
            if ($ticket->getAssigned_group()->getId() > 0){
                echo $ticket->getAssigned_group()->getName();
            } else {
                echo $ticket->getAssigned_user()->getNameAsLine();
            }
        }?>
        </td>
        <td width="25%">&nbsp;</td>
        <td width="25%">&nbsp;</td>
      </tr>
    </table>
  </div>
  </br>
  	<div class="ticket_comment">
  	     <table width="100%" border="1">
  	         <tr>
  	             <td width="100%" colspan="2">
  	                 <textarea name="tktc_comment" id="tktc_comment" rows="10" cols="80"></textarea>
  	             </td>
  	         </tr>
		     <tr>
		          <td width="50%">Anhänge:</td>
		          <td width="50%">
		              <input type="file" multiple="multiple" name="tktc_attachments[]" width="100%" />
		          </td>
		     </tr>
  	     </table>
	</div>
  </br>
  
  <table width="100%">
    <colgroup>
        <col width="180">
        <col>
    </colgroup> 
    <tr>
        <td class="content_row_header">
        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
        			onclick="window.location.href='index.php?pid=20'">
        </td>
        <td class="content_row_clear" align="right">
        	<input type="submit" value="<?=$_LANG->get('Speichern')?>">
        </td>
    </tr>
  </table>
  </br>
  <?php 
  $all_comments = Comment::getCommentsForObject(get_class($ticket),$ticket->getId());
  $all_comments = array_reverse($all_comments);
  if ($_REQUEST["sort"] == "asc"){
      $all_comments = array_reverse($all_comments);
  }
  
  if (count($all_comments) > 0){?>
  <div class="ticket_comments">
    <table><tr><td align="left"><h3><i class="icon-comment"></i> Kommentare <a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$ticket->getId()?>&sort=asc"><img src="../../../images/icons/arrow-090.png"/></a></h3></td></tr></table>
    
    <?php 
    foreach ($all_comments as $comment){
        if ($comment->getVisability() == Comment::VISABILITY_PUBLIC || $comment->getVisability() == Comment::VISABILITY_PUBLICMAIL)
        {
            ?>
          	<table width="100%" border="1">
              <tr>
                <td width="25%"><?php echo date("d.m.Y H:i",$comment->getCrtdate());?>
                <?php
                if ($comment->getState() == 0) { echo '[GELÖSCHT]'; }
                ?>
                </td>
                <td width="50%"><?php echo $comment->getTitle();?></td>
                <?php 
                if ($comment->getCrtuser()->getId()>0){
                    $crtby = $comment->getCrtuser()->getNameAsLine();
                } elseif ($comment->getCrtcp()->getId()>0){
                    $crtby = $comment->getCrtcp()->getNameAsLine2();
                }
                ?>
                <td width="25%"><?php echo $crtby;?></td>
              </tr>
              <tr>
                <td colspan="3"><?php echo $comment->getComment();?></td>
              </tr>
              <?php if (count(Attachment::getAttachmentsForObject(get_class($comment),$comment->getId())) > 0){ ?>
              <tr>
                <td width="25%">Anhänge:</td>
                <td colspan="2">
                    <?php 
                        foreach (Attachment::getAttachmentsForObject(get_class($comment),$comment->getId()) as $c_attachment){
                            if ($c_attachment->getState() == 1)
                                echo '<span><a href="../../.'.Attachment::FILE_DESTINATION.$c_attachment->getFilename().'" download="'.$c_attachment->getOrig_filename().'">'.$c_attachment->getOrig_filename().'</a></span></br>';
                        }
                    ?>
                    &nbsp;
                </td>
              </tr>
              <?php }?>
              <?php if (count($comment->getArticles()) > 0){?>
              <tr>
                <td width="25%">Artikel:</td>
                <td colspan="2">
                    <?php 
                        foreach ($comment->getArticles() as $c_article){
                            echo '<span>'.$c_article->getAmount().'x '.$c_article->getArticle()->getTitle().'</span></br>';
                        }
                    ?>
                    &nbsp;
                </td>
              </tr>
              <?php }?>
            </table>
            </br>
            <?php 
        }
    }
    ?>
  </div>
  <?php 
  }
  ?>
  </br>
  </form>
</div>
</body>
</html>
