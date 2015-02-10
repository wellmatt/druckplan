<?php 

global $_USER;

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
        $ticket->setDuedate(strtotime($_REQUEST["tkt_due"]));
        $ticket->setCustomer(new BusinessContact($_REQUEST["tkt_customer_id"]));
        $ticket->setCustomer_cp(new ContactPerson($_REQUEST["tkt_customer_cp_id"]));
        if (substr($_REQUEST["tkt_assigned"], 0, 2) == "u_"){
            $ticket->setAssigned_group(new Group(0));
            $ticket->setAssigned_user(new User((int)substr($_REQUEST["tkt_assigned"], 2)));
            $new_assiged = "Benutzer <b>" . $ticket->getAssigned_user()->getNameAsLine() . "</b>";
            if ($ticket->getAssigned_user() != $_USER){
                Notification::generateNotification($ticket->getAssigned_user(), get_class($ticket), "Assign", $ticket->getNumber(), $ticket->getId());
            }
        } elseif (substr($_REQUEST["tkt_assigned"], 0, 2) == "g_") {
            $ticket->setAssigned_group(new Group((int)substr($_REQUEST["tkt_assigned"], 2)));
            $ticket->setAssigned_user(new User(0));
            $new_assiged = "Gruppe <b>" . $ticket->getAssigned_group()->getName() . "</b>";
            foreach ($ticket->getAssigned_group()->getMembers() as $grmem){
                if ($grmem != $_USER){
                    Notification::generateNotification($grmem, get_class($ticket), "AssignGroup", $ticket->getNumber(), $ticket->getId());
                }
            }
        }
        $ticket->setState(new TicketState((int)$_REQUEST["tkt_state"]));
        $ticket->setCategory(new TicketCategory((int)$_REQUEST["tkt_category"]));
        $ticket->setPriority(new TicketPriority((int)$_REQUEST["tkt_prio"]));
        $ticket->setSource((int)$_REQUEST["tkt_source"]);
        if ($ticket->getId() > 0){
            $ticket->setEditdate(time());
        }
        $save_ok = $ticket->save();
        $savemsg = getSaveMessage($save_ok)." ".$DB->getLastError();
        if ($save_ok && $_REQUEST["tktc_comment"] != ""){
            $ticketcomment = new Comment();
            $ticketcomment->setComment($_REQUEST["tktc_comment"]);
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
                if ($save_ok){
                    $ticketcomment->setArticles(Array($tc_article));
                }
            }
            if ($save_ok && $_REQUEST["stop_timer"] == 1 && $_REQUEST["ticket_timer_timestamp"]){
                $timer = Timer::getLastUsed();
                if ($timer->getState() == Timer::TIMER_RUNNING){
                    $timer->stop();
                    $timer->save();
                    $time_ok = true;
                    
                    $perf = new Perferences();
                    if ($perf->getDefault_ticket_id() > 0){
                        $tmp_def_ticket = new Ticket($perf->getDefault_ticket_id());
                        $tmp_ticket_id = $tmp_def_ticket->getId();
                    
                        $logintimer = new Timer();
                        $logintimer->setObjectid($tmp_ticket_id);
                        $logintimer->setModule("Ticket");
                        $now = time();
                        $logintimer->setStarttime($now);
                        $logintimer->setState(Timer::TIMER_RUNNING);
                        $logintimer->save();
                    }
                } else {
                    $timer = new Timer();
                    $timer->start(get_class($ticket), $ticket->getId(), (int)$_REQUEST["ticket_timer_timestamp"]);
                    $timer->save();
                    $timer->stop();
                    $timer->save();
                }
                unset($timer);
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

<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>

<script src="thirdparty/ckeditor/ckeditor.js"></script>
<script src="jscripts/jvalidation/dist/jquery.validate.min.js"></script>
<script src="jscripts/jvalidation/dist/localization/messages_de.min.js"></script>

<script language="JavaScript">
	$(function() {
		CKEDITOR.replace( 'tktc_comment', {
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
	
	 $( "#tkt_customer" ).autocomplete({
		 source: "libs/modules/tickets/ticket.ajax.php?ajax_action=search_customer_and_cp",
		 minLength: 2,
		 focus: function( event, ui ) {
		 $( "#tkt_customer" ).val( ui.item.label );
		 return false;
		 },
		 select: function( event, ui ) {
		 $( "#tkt_customer" ).val( ui.item.label );
		 $( "#tkt_customer_id" ).val( ui.item.bid );
		 $( "#tkt_customer_cp_id" ).val( ui.item.cid );
		 return false;
		 }
		 });
	
	 $( "#tktc_article" ).autocomplete({
		 source: "libs/modules/tickets/ticket.ajax.php?ajax_action=search_article",
		 minLength: 2,
		 focus: function( event, ui ) {
		 $( "#tktc_article" ).val( ui.item.label );
		 return false;
		 },
		 select: function( event, ui ) {
		 $( "#tktc_article" ).val( ui.item.label );
		 $( "#tktc_article_id" ).val( ui.item.value );
		 if (!$("#stop_timer").prop('checked')){
			 $( "#tktc_article_amount" ).val("1");
		 }
		 return false;
		 }
		 });
});
</script>
<script language="JavaScript">
$(document).ready(function () {
    $('#ticket_edit').validate({
        rules: {
            'tktc_comment': {
                required: true
            },
            'tktc_article_id': {
            	required: "#stop_timer:checked"
            },
            'tktc_article': {
            	required: "#stop_timer:checked"
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
			 <h2><?=$header_title?><a name="top" href="#comment"><img height="32" width="32" src="images/icons/arrow-skip-270.png" title="Nach unten springen"/></a></h2>
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
                     <h3><?php if ($ticket->getId()>0){?><a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$ticket->getId()?>" title="Reload">
                     <i class="icon-refresh"></i> Ticket #<?php echo $ticket->getNumber();?> - <?php echo $ticket->getTitle();?></a><?php } ?></h3>
                     <?php if ($ticket->getId()>0){
                      
                      // Associations
                      $association_object = $ticket;
                      include 'libs/modules/associations/association.include.php';
                      //-> END Associations
                     } ?>
                </td>
                <td width="50%" align="right">
               	  <?php if ($ticket->getId()>0){?><a href="index.php?page=<?=$_REQUEST['page']?>&exec=delete&tktid=<?=$ticket->getId()?>"><?php } ?><i class="icon-trash"></i> Löschen<?php if ($ticket->getId()>0){?></a><?php } ?>
                  <?php if ($ticket->getId()>0){?><a href="index.php?page=<?=$_REQUEST['page']?>&exec=close&tktid=<?=$ticket->getId()?>"><?php } ?><i class="icon-remove-circle"></i> Schließen<?php if ($ticket->getId()>0){?></a><?php } ?>
                </td>
        	</tr>
        </tbody>
  </table>
  
  <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="ticket_edit" id="ticket_edit" enctype="multipart/form-data">
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
            <input type="text" id="tkt_customer" name="tkt_customer" value="<?php if ($new_ticket == false) { echo $ticket->getCustomer()->getNameAsLine()." - ".$ticket->getCustomer_cp()->getNameAsLine2(); } ?>" style="width:160px" required/>
            <input type="hidden" id="tkt_customer_id" name="tkt_customer_id" value="<?php echo $ticket->getCustomer()->getId();?>" required/>
            <input type="hidden" id="tkt_customer_cp_id" name="tkt_customer_cp_id" value="<?php echo $ticket->getCustomer_cp()->getId();?>" required/>
        </td>
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
        <td width="25%">&nbsp;</td>
        <td width="25%">&nbsp;</td>
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
        <td width="25%">&nbsp;</td>
        <td width="25%">&nbsp;</td>
      </tr>
      <tr>
        <td width="25%">Kategorie:</td>
        <td width="25%">
            <select name="tkt_category" id="tkt_category" style="width:160px" required>
            <?php 
            $tkt_all_categories = TicketCategory::getAllCategories();
            foreach ($tkt_all_categories as $tkt_category){
                if ($ticket->getCategory() == $tkt_category){
                    echo '<option value="'.$tkt_category->getId().'" selected>'.$tkt_category->getTitle().'</option>';
                } else {
                    echo '<option value="'.$tkt_category->getId().'">'.$tkt_category->getTitle().'</option>';
                }
            }
            ?>
            </select>
        </td>
        <td width="25%">eMail-Adresse:</td>
        <td width="25%"><div id="cp_mail"><?php if ($ticket->getId()>0) echo $ticket->getCustomer_cp()->getEmail();?></div></td>
      </tr>
      <tr>
        <td width="25%">Erstellt am:</td>
        <td width="25%"><?php if ($ticket->getId()>0) echo date("d.m.Y H:i",$ticket->getCrtdate()) . " von " . $ticket->getCrtuser()->getNameAsLine();?>&nbsp;</td>
        <td width="25%">Telefon:</td>
        <td width="25%"><div id="cp_phone"><?php if ($ticket->getId()>0) echo $ticket->getCustomer_cp()->getPhone();?></div></td>
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
        } else {?>
            <select name="tkt_assigned" id="tkt_assigned" style="width:160px" required>
            <option disabled>-- Users --</option>
            <?php 
            $all_user = User::getAllUser(User::ORDER_NAME);
            $all_groups = Group::getAllGroups(Group::ORDER_NAME);
            foreach ($all_user as $tkt_user){
                if ($ticket->getId() == 0 && $tkt_user->getId() == $_USER->getId()){
                    echo '<option value="u_'.$tkt_user->getId().'" selected>'.$tkt_user->getNameAsLine().'</option>';
                } elseif ($ticket->getAssigned_user() == $tkt_user){
                    echo '<option value="u_'.$tkt_user->getId().'" selected>'.$tkt_user->getNameAsLine().'</option>';
                } else {
                    echo '<option value="u_'.$tkt_user->getId().'">'.$tkt_user->getNameAsLine().'</option>';
                }
            }
            ?>
            <option disabled>-- Groups --</option>
            <?php 
            foreach ($all_groups as $tkt_groups){
                if ($ticket->getAssigned_group() == $tkt_groups){
                    echo '<option value="g_'.$tkt_groups->getId().'" selected>'.$tkt_groups->getName().'</option>';
                } else {
                    echo '<option value="g_'.$tkt_groups->getId().'">'.$tkt_groups->getName().'</option>';
                }
            }
            ?>
            </select>
        <?php }?>
        </td>
        <td width="25%">Herkunft:</td>
        <td width="25%">
            <select name="tkt_source" id="tkt_source" style="width:160px" required>
                <option value="<?php echo Ticket::SOURCE_EMAIL?>" <?php if ($ticket->getSource() == Ticket::SOURCE_EMAIL) echo "selected"; ?>>per E-Mail</option>
                <option value="<?php echo Ticket::SOURCE_PHONE?>" <?php if ($ticket->getSource() == Ticket::SOURCE_PHONE) echo "selected"; ?>>per Telefon</option>
                <option value="<?php echo Ticket::SOURCE_OTHER?>" <?php if ($ticket->getSource() == Ticket::SOURCE_OTHER) echo "selected"; ?>>andere</option>
            </select>
        </td>
      </tr>
      <tr>
        <td width="25%">Fälligkeitsdatum:</td>
        <td width="25%">
			<input type="text" style="width:160px" id="tkt_due" name="tkt_due"
			class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
			onfocus="markfield(this,0)" onblur="markfield(this,1)"
			value="<?if($ticket->getDuedate() != 0){ echo date('d.m.Y H:i', $ticket->getDuedate());}?>" required/>
        </td>
        <td width="25%">Letzte Mitteilung:</td>
        <td width="25%"><?php if ($ticket->getId()>0 && $ticket->getEditdate() > 0) echo date("d.m.Y H:i",$ticket->getEditdate());?>&nbsp;</td>
      </tr>
    </table>
  </div>
  </br>
  <?php 
  $all_comments = Comment::getCommentsForObject(get_class($ticket),$ticket->getId());
  if ($_REQUEST["sort"] == "desc"){
      $all_comments = array_reverse($all_comments);
  }
  
  if (count($all_comments) > 0){?>
  <div class="ticket_comments">
    <table><tr><td align="left"><h3><i class="icon-comment"></i> Kommentare <a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$ticket->getId()?>&sort=desc"><img src="images/icons/arrow-270.png"/></a></h3></td></tr></table>
    
    <?php 
    foreach ($all_comments as $comment){
        if ($_USER->isAdmin() 
            || $comment->getVisability() == Comment::VISABILITY_PUBLIC 
            || $comment->getVisability() == Comment::VISABILITY_INTERNAL 
            || $comment->getCrtuser() == $_USER)
        {
            ?>
          	<table width="100%" border="1">
              <tr onclick="callBoxFancy('libs/modules/comment/comment.edit.php?cid=<?php echo $comment->getId();?>&tktid=<?php echo $ticket->getId();?>');">
                <td width="25%"><?php echo date("d.m.Y H:i",$comment->getCrtdate());?>
                <?php 
                switch ($comment->getVisability())
                {
                    case Comment::VISABILITY_PUBLIC:
                        echo "[PUBLIC]";
                        break;
                    case Comment::VISABILITY_INTERNAL:
                        echo "[INTERN]";
                        break;
                    case Comment::VISABILITY_PRIVATE:
                        echo "[PRIVATE]";
                        break;
                }
                if ($comment->getState() == 0) { echo '[GELÖSCHT]'; }
                ?>
                </td>
                <td width="50%"><?php echo $comment->getTitle();?></td>
                <td width="25%"><?php echo $comment->getCrtuser()->getNameAsLine();?></td>
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
                            echo '<span><a href="'.Attachment::FILE_DESTINATION.$c_attachment->getFilename().'" download="'.$c_attachment->getOrig_filename().'">'.$c_attachment->getOrig_filename().'</a></span></br>';
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
                            echo '<span>'.$c_article->getAmount().'x <a target="_blank" href="index.php?page=libs/modules/article/article.php&exec=edit&aid='.$c_article->getArticle()->getId().'">'.$c_article->getArticle()->getTitle().'</a></span></br>';
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
    <p align="left"><a href="#top"><img height="32" width="32" src="images/icons/arrow-skip-090.png" title="Nach oben springen"/></a></p>
	<div class="ticket_comment">
		  <textarea name="tktc_comment" id="tktc_comment" rows="10" cols="80" required></textarea>
		  </br>
		  <table width="100%" border="1">
		      <tr>
		          <td width="25%">Artikel:</td>
		          <td width="75%">
		              <input type="text" id="tktc_article" name="tktc_article"/> Menge: <input type="text" id="tktc_article_amount" name="tktc_article_amount"/>
                      <input type="hidden" id="tktc_article_id" name="tktc_article_id"/>
		          </td>
		      </tr>
		      <tr>
		          <td width="25%">Anhänge:</td>
		          <td width="75%" colspan="2">
		              <input type="file" multiple="multiple" name="tktc_attachments[]" width="100%" />
		          </td>
		      </tr>
		      <tr>
		          <td colspan="2">&nbsp;</td>
		      </tr>
		      <?php ////// TIMER STUFF >>> ///?>
		      <?php if ($ticket->getId() > 0){ ?>
		      <tr>
		          <td width="25%">Timer:</td>
		          <td width="75%" colspan="2">
		              <?php
		              $timer = Timer::getLastUsed();
		              $timer_start = 0;
		              $reset_disabled = false;
		              if ($timer->getId() > 0){
		                  if ($timer->getState() == Timer::TIMER_RUNNING){
		                      $timer_start = $timer->getStarttime();
		                      if ($timer->getModule() == "Ticket" && $timer->getObjectid() == $ticket->getId()){ // Timer läuft für dieses Ticket
		                          ?>
		                          <span id="ticket_timer" class="timer duration btn btn-warning" data-duration="0"></span> läuft für dieses Ticket
		                          <?php
		                      } else { // Timer läuft für anderes Ticket
		                          $reset_disabled = true;
		                          $tmp_ticket = new Ticket($timer->getObjectid());
		                          ?>
		                          <span id="ticket_timer" class="timer duration btn btn-error" data-duration="0"></span> läuft für '<a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$tmp_ticket->getId()?>"><?php echo $tmp_ticket->getNumber() . " - " . $tmp_ticket->getTitle(); ?></a>'
		                          <?php
		                      }
		                  } else { // Timer läuft nicht
		                      $timer_start = $timer->getStoptime()+1;
	                          ?>
	                          <span id="ticket_timer" class="timer duration btn" data-duration="0">00:00:00</span> läuft (noch nicht zugeordnet)
	                          <?php
		                  }
		              } else { // kein Timer gefunden
		                  $timer_start = time();
                          ?>
                          <span id="ticket_timer" class="timer duration btn" data-duration="0">00:00:00</span> kein Timer gefunden
                          <?php
		              }
		              ?>
		              <input id="ticket_timer_timestamp" name="ticket_timer_timestamp" type="hidden" value="<?php echo $timer_start;?>"/>
		          </td>
		      </tr>
		      <tr>
		          <td width="25%">&nbsp;</td>
		          <td width="75%"><input type="checkbox" name="stop_timer" id="stop_timer" value="1" <?php if ($reset_disabled == true) echo "disabled";?>> Zeit eintragen und zurücksetzen?</td>
		      </tr>
		      <tr>
		          <td colspan="2">&nbsp;</td>
		      </tr>
		      <?php } ?>
                <script>
                $(document).ready(function () {
                	var clock;
                	var sec = moment().unix();
                	var start = parseInt($('#ticket_timer_timestamp').val());
                	if (start != 0){
                		clock = setInterval(stopWatch,1000);
                	}
                	<?php /*
                    $( "#ticket_timer" ).click(function() {
                        if ($( "#ticket_timer" ).hasClass("btn-warning")){
                        	window.clearInterval(clock);
                        	$( "#ticket_timer" ).removeClass("btn-warning");
                        	$.ajax({
                        		type: "POST",
                        		url: "libs/modules/timer/timer.ajax.php",
                        		data: { ajax_action: "stop", module: "<?php echo get_class($ticket);?>", objectid: "<?php echo $ticket->getId();?>" }
                        		})
                        		.done(function( msg ) {
                        		alert( "Data Saved: " + msg );
                        		});
                        } else {
                            sec = moment().unix();
                            start = moment().unix();
                        	clock = setInterval(stopWatch,1000);
                        	$( "#ticket_timer" ).addClass("btn-warning");
                        	$.ajax({
                        		type: "POST",
                        		url: "libs/modules/timer/timer.ajax.php",
                        		data: { ajax_action: "start", module: "<?php echo get_class($ticket);?>", objectid: "<?php echo $ticket->getId();?>" }
                        		})
                        		.done(function( msg ) {
                        		alert( "Data Saved: " + msg );
                        		});
                        }
                    });
                	*/?>
                    function stopWatch() {
                    	sec++;
                    	var timestamp = sec-start;
                    	$("#ticket_timer").html(rectime(timestamp));
                        if ($("#stop_timer").prop('checked')){
                            $("#tktc_article_amount").val(precise_round((sec-start)/60/60,2));
                        }
                    }
                    function rectime(secs) {
                    	var hr = Math.floor(secs / 3600);
                    	var min = Math.floor((secs - (hr * 3600))/60);
                    	var sec = Math.floor(secs - (hr * 3600) - (min * 60));
                    	
                    	if (hr < 10) {hr = "0" + hr; }
                    	if (min < 10) {min = "0" + min;}
                    	if (sec < 10) {sec = "0" + sec;}
                    	if (hr) {hr = "00";}
                    	return hr + ':' + min + ':' + sec;
                    }
                    $( "#stop_timer" ).click(function() {
                        if ($("#stop_timer").prop('checked')){
                            $("#tktc_article_amount").val(precise_round((sec-start)/60/60,2));
                        }
                    });
                    function precise_round(num, decimals) {
                    	var t=Math.pow(10, decimals);   
                 	    return (Math.round((num * t) + (decimals>0?1:0)*(Math.sign(num) * (10 / Math.pow(100, decimals)))) / t).toFixed(decimals);
                   	}
                });
                </script>
              <?php // <<< TIMER STUFF //////?>
		      <tr>
		          <td width="25%">Typ:</td>
		          <td width="75%">
                    <input type="radio" name="tktc_type" checked value="<?php echo Comment::VISABILITY_INTERNAL;?>"> inter. Kommentar<br>
                    <input type="radio" name="tktc_type" value="<?php echo Comment::VISABILITY_PUBLIC;?>"> Offiz. Antwort<br>
                    <input type="radio" name="tktc_type" value="<?php echo Comment::VISABILITY_PRIVATE;?>"> priv. Kommentar
		          </td>
		      </tr>
		      <?php if ($new_ticket == false){?>
		      <tr>
		          <td width="25%">neuen MA zuweisen:</td>
		          <td width="75%">
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
	</div>
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
	<a name="comment"></a>
</div>
</body>
</html>
