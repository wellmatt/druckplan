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
        if ($_REQUEST["tkt_due"] != "" && $_REQUEST["tkt_due"] != 0){
            $ticket->setDuedate(strtotime($_REQUEST["tkt_due"]));
        } else {
            $ticket->setDuedate(0);
        }
        $ticket->setCustomer(new BusinessContact($_REQUEST["tkt_customer_id"]));
        $ticket->setCustomer_cp(new ContactPerson($_REQUEST["tkt_customer_cp_id"]));
        $assigned = "";
        if (substr($_REQUEST["tkt_assigned"], 0, 2) == "u_"){
            $ticket->setAssigned_group(new Group(0));
            $ticket->setAssigned_user(new User((int)substr($_REQUEST["tkt_assigned"], 2)));
            $new_assiged = "Benutzer <b>" . $ticket->getAssigned_user()->getNameAsLine() . "</b>";
            $assigned = "user";
        } elseif (substr($_REQUEST["tkt_assigned"], 0, 2) == "g_") {
            $ticket->setAssigned_group(new Group((int)substr($_REQUEST["tkt_assigned"], 2)));
            $ticket->setAssigned_user(new User(0));
            $new_assiged = "Gruppe <b>" . $ticket->getAssigned_group()->getName() . "</b>";
            $assigned = "group";
        }
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
        $ticket->setTourmarker($_REQUEST["tkt_tourmarker"]);
        $save_ok = $ticket->save();
        if ($save_ok){
            if (!$_REQUEST["tktid"]){
                if (!Abonnement::hasAbo($ticket,$_USER)){
                    $abo = new Abonnement();
                    $abo->setAbouser($_USER);
                    $abo->setModule(get_class($ticket));
                    $abo->setObjectid($ticket->getId());
                    $abo->save();
                    unset($abo);
                }
            }
            if ($assigned == "group")
            {
                foreach ($ticket->getAssigned_group()->getMembers() as $grmem){
                    if (!Abonnement::hasAbo($ticket,$grmem)){
                        $abo = new Abonnement();
                        $abo->setAbouser($grmem);
                        $abo->setModule(get_class($ticket));
                        $abo->setObjectid($ticket->getId());
                        $abo->save();
                        unset($abo);
                    }
                    if ($grmem != $_USER){
                        Notification::generateNotification($grmem, get_class($ticket), "AssignGroup", $ticket->getNumber(), $ticket->getId(), $ticket->getAssigned_group()->getName());
                    }
                }
            } else if ($assigned == "user")
            {
                if (!Abonnement::hasAbo($ticket,$ticket->getAssigned_user())){
                    $abo = new Abonnement();
                    $abo->setAbouser($ticket->getAssigned_user());
                    $abo->setModule(get_class($ticket));
                    $abo->setObjectid($ticket->getId());
                    $abo->save();
                    unset($abo);
                }
                if ($ticket->getAssigned_user() != $_USER){
                    Notification::generateNotification($ticket->getAssigned_user(), get_class($ticket), "Assign", $ticket->getNumber(), $ticket->getId());
                }
            }
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
                Notification::generateNotificationsFromAbo(get_class($ticket), "Comment", $ticket->getNumber(), $ticket->getId());
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
            if ($save_ok && $_REQUEST["stop_timer"] == 1){ //  && $_REQUEST["ticket_timer_timestamp"]
                $timer = Timer::getLastUsed();
                if ($timer->getState() == Timer::TIMER_RUNNING){
                    $timer->stop();
                    $time_ok = true;
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
    		 $( "#tkt_tourmarker" ).val( ui.item.tourmarker );
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
			 CKEDITOR.instances.tktc_comment.focus();
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
function Abo_Refresh()
{
	$.ajax({
		type: "POST",
		url: "libs/modules/abonnements/abonnement.ajax.php",
		data: { exec: "abo_getcount", module: "<?php echo get_class($ticket);?>", objectid: "<?php echo $ticket->getId();?>" },
		success: function(data) 
		    {
			 if (parseInt(data) > 0)
				 $("#abo_count").html(data);
		    }
	});
}
$(document).ready(function () {
    $('#ticket_edit').validate({
        rules: {
            'tktc_comment': {
                required: false
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

	$("a#hiddenclicker_tkcframe").fancybox({
		'type'          :   'iframe',
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'width'         :   1024,
		'height'		:	768, 
		'overlayShow'	:	true,
		'helpers'		:   { overlay:null, closeClick:true }
	});
	
	$("a#tktc_hiddenclicker").fancybox({
		'type'          :   'iframe',
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'width'         :   1024,
		'height'		:	768, 
		'overlayShow'	:	true,
		'helpers'		:   { overlay:null, closeClick:true }
	});

	$("a#abo_hiddenclicker").fancybox({
		'type'          :   'iframe',
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'width'         :   600,
		'height'		:	800, 
		'overlayShow'	:	true,
		'helpers'		:   { overlay:null, closeClick:true },
		'onClosed'      : function() {
			Abo_Refresh();
	        return;
	    }
	});
});
function showSummary()
{
	newwindow = window.open('libs/modules/tickets/ticket.summary.php?tktid=<?=$ticket->getId()?>', "_blank", "width=1000,height=800,left=0,top=0,scrollbars=yes");
	newwindow = focus();
}
function callBoxFancytktc(my_href) {
	var j1 = document.getElementById("tktc_hiddenclicker");
	j1.href = my_href;
	$('#tktc_hiddenclicker').trigger('click');
}
function callBoxFancyAbo(my_href) {
	var j1 = document.getElementById("abo_hiddenclicker");
	j1.href = my_href;
	$('#abo_hiddenclicker').trigger('click');
}
</script>

<script>
	$(document).ready(function () {
		$( "#abo_remove" ).click(function() {
			var r = confirm("Möchten Sie das Abo wirklich abbestellen?");
			if (r == true) {
    			$.ajax({
    				type: "POST",
    				url: "libs/modules/abonnements/abonnement.ajax.php",
    				data: { exec: "abo_remove", module: "<?php echo get_class($ticket);?>", objectid: "<?php echo $ticket->getId();?>", userid: "<?php echo $_USER->getId();?>" }
    			})
    			.done(function( msg ) {
    			    $( "#abo_remove" ).toggle();
    			    $( "#abo_add" ).toggle();
    			    Abo_Refresh();
    			});
			}
		});
		$( "#abo_add" ).click(function() {
			$.ajax({
				type: "POST",
				url: "libs/modules/abonnements/abonnement.ajax.php",
				data: { exec: "abo_add", module: "<?php echo get_class($ticket);?>", objectid: "<?php echo $ticket->getId();?>" }
			})
			.done(function( msg ) {
			    $( "#abo_remove" ).toggle();
			    $( "#abo_add" ).toggle();
			    Abo_Refresh();
			});
		});
	});
</script>
<script>
	$(function() {
		$("a#association_hiddenclicker").fancybox({
			'type'    : 'iframe',
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600, 
			'speedOut'		:	200, 
			'height'		:	350, 
			'overlayShow'	:	true,
			'helpers'		:   { overlay:null, closeClick:true }
		});
	});
	function callBoxFancyAsso(my_href) {
		var j1 = document.getElementById("association_hiddenclicker");
		j1.href = my_href;
		$('#association_hiddenclicker').trigger('click');
	}
</script>

<body>

<div id="tktc_hidden_clicker" style="display:none"><a id="tktc_hiddenclicker" href="http://www.google.com" >Hidden Clicker</a></div>
<div id="abo_hidden_clicker" style="display:none"><a id="abo_hiddenclicker" href="http://www.google.com" >Hidden Clicker</a></div>
<div id="association_hidden_clicker" style="display:none"><a id="association_hiddenclicker" href="http://www.google.com" >Hidden Clicker</a></div>

<div class="ticket_view">
<table width="100%">
	<tr>
		<td class="content_header">
    		<div class="page-header" style="margin: 0 0 0;">
              <h2><?=$header_title?> 
              <?php if ($ticket->getId()>0){?>
                <small>#<?php echo $ticket->getNumber();?> - <?php echo $ticket->getTitle();?></small>
              <?php }?>
              </h2>
            </div>
		</td>
		<td align="right">
			<?=$savemsg?>
		</td>
	</tr>
</table>

  <table border="0" cellpadding="2" cellspacing="0" width="100%">
        <tbody>
        	<tr>
                <td width="100%" align="right">
                    <?php if ($ticket->getId()>0){?>
                    <div class="btn-group" role="group">
                      <button type="button" onclick="showSummary();" class="btn btn-sm btn-default">Summary</button>
                      <?php 
                      $association_object = $ticket;
                      $associations = Association::getAssociationsForObject(get_class($association_object), $association_object->getId());
                      ?>
                      <div class="btn-group dropdown">
                      <button type="button" class="btn btn-sm dropdown-toggle btn-default" data-toggle="dropdown" aria-expanded="false">
                        Verknüpfungen <span class="badge"><?php echo count($associations);?></span> <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" role="menu">
                        <?php 
                            if (count($associations)>0){
                                foreach ($associations as $association){
                                    if ($association->getModule1() == get_class($association_object) && $association->getObjectid1() == $association_object->getId()){
                                        $classname = $association->getModule2();
                                        $object = new $classname($association->getObjectid2());
                                        $link_href = Association::getPath($classname);
                                        $object_name = Association::getName($object);
                                    } else {
                                        $classname = $association->getModule1();
                                        $object = new $classname($association->getObjectid1());
                                        $link_href = Association::getPath($classname);
                                        $object_name = Association::getName($object);
                                    }
                                    echo '<li><a href="index.php?page='.$link_href.$object->getId().'" target="_blank">';
                                    echo $object_name;
                                    echo '</a></li>';
                                }
                            }
                            echo '<li class="divider"></li>';
                            echo '<li><a href="#" onclick="callBoxFancyAsso(\'libs/modules/associations/association.frame.php?module='.get_class($association_object).'&objectid='.$association_object->getId().'\');">Neue Verknüpfung</a></li>';
                        ?>
                      </ul>
                      </div>
                      <button type="button" onclick="window.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$ticket->getId()?>';" class="btn btn-sm btn-default">Refresh</button>
                      <button type="button" onclick="askDel('index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=createFromTicket&tktid=<?=$ticket->getId()?>');" class="btn btn-sm btn-default">Vorgang erstellen</button>
                      <?php 
                      if (Abonnement::hasAbo($ticket))
                      {?>
                        <button id="abo_remove" type="button" class="btn btn-sm btn-default">Abo entfernen</button>
                        <button id="abo_add" type="button"  style="display: none" class="btn btn-sm btn-default">Abonnieren</button>
                      <?php } else { ?>
                        <button id="abo_remove" type="button" style="display: none" class="btn btn-sm btn-default">Abo entfernen</button>
                        <button id="abo_add" type="button" class="btn btn-sm btn-default">Abonnieren</button>
                      <?php } 
                        $abonnoments = Abonnement::getAbonnementsForObject(get_class($ticket), $ticket->getId());
                        $abo_title = "";
                        if (count($abonnoments)>0){
                            foreach ($abonnoments as $abonnoment){
                                $abo_title .= $abonnoment->getAbouser()->getNameAsLine() . "\n";
                            }
                        }
                      ?>
                        <button type="button" onclick="callBoxFancyAbo('libs/modules/abonnements/abonnement.add.frame.php?module=<?php echo get_class($ticket);?>&objectid=<?php echo $ticket->getId();?>');" 
                        class="btn btn-sm btn-default" title="<?php echo $abo_title;?>">Abonnoments&nbsp;<span id="abo_count" class="badge"><?php if (count($abonnoments)>0) echo count($abonnoments);?></span></button>
                      <button type="button" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=close&tktid=<?=$ticket->getId()?>');" class="btn btn-sm btn-warning">Schließen</button>
                      <button type="button" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&tktid=<?=$ticket->getId()?>');" class="btn btn-sm btn-danger">Löschen</button>
                    </div>
                    <?php }?>
                </td>
        	</tr>
        </tbody>
  </table>
  </br>
  
  <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="ticket_edit" id="ticket_edit" enctype="multipart/form-data">
  <input type="hidden" name="exec" value="edit"> 
  <input type="hidden" name="subexec" value="save"> 
  <input type="hidden" name="tktid" value="<?=$ticket->getId()?>">
  <input type="hidden" name="returnhome" value="<?=$_REQUEST["returnhome"]?>">
  
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
        <td width="25%">Kategorie:</td>
        <td width="25%">
            <select name="tkt_category" id="tkt_category" style="width:160px" required>
            <?php 
            $tkt_all_categories = TicketCategory::getAllCategories();
            foreach ($tkt_all_categories as $tkt_category){
                if ($ticket->getId()>0){
                    if ($ticket->getCategory() == $tkt_category){
                        echo '<option value="'.$tkt_category->getId().'" selected>'.$tkt_category->getTitle().'</option>';
                    } else {
                        echo '<option value="'.$tkt_category->getId().'">'.$tkt_category->getTitle().'</option>';
                    }
                } else {
                    if ($tkt_category->cancreate())
                        echo '<option value="'.$tkt_category->getId().'">'.$tkt_category->getTitle().'</option>';
                }
            }
            ?>
            </select>
        </td>
        <td width="25%">Telefon:</td>
        <td width="25%"><div id="cp_phone">
                            <?php if ($ticket->getId()>0){?>
                            <span  onClick="dialNumber('<?php echo $_USER->getTelefonIP();?>/command.htm?number=<?php echo $ticket->getCustomer_cp()->getPhoneForDial();?>')"
									title="<?php echo $ticket->getCustomer_cp()->getPhoneForDial()." ".$_LANG->get('anrufen');?>" class="pointer icon-link">
									<img src="images/icons/telephone.png" alt="TEL"> <?php echo $ticket->getCustomer_cp()->getPhone();?>
							</span>
							<?php }?>
                        </div>
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
        <td width="25%">eMail-Adresse:</td>
        <td width="25%"><div id="cp_mail"><?php if ($ticket->getId()>0) echo $ticket->getCustomer_cp()->getEmail();?></div></td>
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
        <td width="25%"><?php if ($ticket->getId()>0) echo date("d.m.Y H:i",$ticket->getCrtdate()) . " von " . $ticket->getCrtuser()->getNameAsLine();?>&nbsp;</td>
        <td width="25%">Tourenmerkmal:</td>
        <td width="25%"><input type="text" id="tkt_tourmarker" name="tkt_tourmarker" value="<?php if ($ticket->getId()>0) echo $ticket->getTourmarker();?>" style="width:160px"/></td>
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
        <td width="25%">&nbsp;</td>
        <td width="25%">&nbsp;</td>
      </tr>
    </table>
  </div>
  </br>
  	<div class="ticket_comment">
  	     <table width="100%" border="1">
  	         <tr>
  	             <td rowspan="6" width="50%">
  	                 <textarea name="tktc_comment" id="tktc_comment" rows="10" cols="80"></textarea>
  	             </td>
		         <?php ////// TIMER STUFF >>> ///?>
		         <?php if ($ticket->getId() > 0){ ?>
		         <td width="25%">Timer:</td>
		         <td width="25%">
		              <?php
// 		              $timer = Timer::getLastUsed();
		              $timer = Timer::getLastUsed();
		              $timer_start = 0;
		              $timer_running = 0;
		              $reset_disabled = false;

		              
		              if ($timer->getId() > 0){
		                  if ($timer->getState() == Timer::TIMER_RUNNING){
		                      
		                      $timer_start = $timer->getStarttime();
		                      $timer_running = 1;
		                      
		                      if ($timer->getModule() == "Ticket" && $timer->getObjectid() == $ticket->getId()){ // Timer läuft für dieses Ticket
		                          ?>
		                          <span id="ticket_timer" class="timer duration btn btn-warning" data-duration="0"></span> läuft
		                          <a id="hiddenclicker_tkcframe" href="libs/modules/tickets/ticket.commentframe.php?exec=edit&tktid=<?=$timer->getObjectid()?>&this_tktid=<?=$ticket->getId()?>" style="display: none">Hidden Clicker</a>
		                          <?php
		                      } else { // Timer läuft für anderes Ticket
		                          $reset_disabled = true;
		                          $tmp_ticket = new Ticket($timer->getObjectid());
		                          ?>
		                          <span id="ticket_timer" class="timer duration btn btn-error" data-duration="0"></span> läuft für '<a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$tmp_ticket->getId()?>"><?php echo $tmp_ticket->getNumber() . " - " . $tmp_ticket->getTitle(); ?></a>'
		                          <a id="hiddenclicker_tkcframe" href="libs/modules/tickets/ticket.commentframe.php?exec=edit&tktid=<?=$timer->getObjectid()?>&this_tktid=<?=$ticket->getId()?>" style="display: none">Hidden Clicker</a>
		                          <?php
		                      }
		                  } else { // Timer läuft nicht
    		                  $timer_start = 0;
    		                  $reset_disabled = true;
    		                  $timer_running = 0;
                              ?>
                              <span id="ticket_timer" class="timer duration btn" data-duration="0">00:00:00</span>
                              <?php
		                  }
		              } else { // kein Timer gefunden
		                  $timer_start = 0;
		                  $reset_disabled = true;
		                  $timer_running = 0;
                          ?>
                          <span id="ticket_timer" class="timer duration btn" data-duration="0">00:00:00</span>
                          <?php
		              }
		              ?>
		              <input id="ticket_timer_timestamp" name="ticket_timer_timestamp" type="hidden" value="<?php echo $timer_start;?>"/>
		              <input id="ticket_timer_running" name="ticket_timer_running" type="hidden" value="<?php echo $timer_running;?>"/>
		              <input id="stop_timer" name="stop_timer" type="hidden" value="0"/>
		         </td>
  	         </tr>
		     <!-- <tr>
		          <td width="25%">&nbsp;</td>
		          <td width="25%"><input type="checkbox" name="stop_timer" id="stop_timer" value="1" <?php if ($reset_disabled == true) echo "disabled";?>> Zeit eintragen und zurücksetzen?</td>
		     </tr>  -->               
		     <script>
                $(document).ready(function () {
                	var clock;
                	var sec = moment().unix();
                	var start = parseInt($('#ticket_timer_timestamp').val());
                	var running = parseInt($('#ticket_timer_running').val());
                    if (start != 0){
                		var timestamp = sec-start;
                		$("#ticket_timer").html(rectime(timestamp));
                    }
                	if (start != 0 && running == 1){
                		clock = setInterval(stopWatch,1000);
                	}
                    $( "#ticket_timer" ).click(function() {
                        if ($( "#ticket_timer" ).hasClass("btn-warning")){
                         	$( "#tktc_article" ).focus();
                         	$( "#stop_timer" ).val("1");
                        	$( "#tktc_article_amount" ).val(precise_round((sec-start)/60/60,2));
                        	if ( $( "#tktc_article_amount" ).val() < 0.25){
                        		$( "#tktc_article_amount" ).val("0.25");
                        	}
                         	clearInterval(clock);
                         	$( "#ticket_timer" ).removeClass("btn-warning");
                         	clearInterval(clock_home);
                         	$( "#ticket_timer_home" ).removeClass("btn-warning");

                        	<?php /*
                        	$.ajax({
                        		type: "POST",
                        		url: "libs/modules/timer/timer.ajax.php",
                        		data: { ajax_action: "stop", module: "<?php echo get_class($ticket);?>", objectid: "<?php echo $ticket->getId();?>" }
                        		})
                        		.done(function( msg ) {
                                	window.clearInterval(clock);
                                	$( "#ticket_timer" ).removeClass("btn-warning");
                                	window.clearInterval(clock_home);
                                	$( "#ticket_timer_home" ).removeClass("btn-warning");
                                	$('#stop_timer').prop('disabled', false);
                        		});
                        	*/?>
                        } else {
                        	if (!$( "#ticket_timer" ).hasClass("btn-error")){
                            	if ($( "#stop_timer" ).val() != 1){
                                	$.ajax({
                                		type: "POST",
                                		url: "libs/modules/timer/timer.ajax.php",
                                		data: { ajax_action: "start", module: "<?php echo get_class($ticket);?>", objectid: "<?php echo $ticket->getId();?>" }
                                		})
                                		.done(function( msg ) {
                                			if (start == 0){
                                			 start = moment().unix();
                                			}
                                			sec = moment().unix();
                                        	clock = setInterval(stopWatch,1000);
                                        	$( "#ticket_timer" ).addClass("btn-warning");
                                        	clock_home = setInterval(stopWatch_home,1000);
                                        	$( "#ticket_timer_home" ).addClass("btn-warning");
                                        	$('#stop_timer').prop('disabled', false);
        //                       			  alert( "Data Saved: " + msg );
                                		});
                            	}
                        	} else {
                        		$('#hiddenclicker_tkcframe').trigger('click');
                        	}
                        }
                    });
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
//                     	alert (hr + ':' + min + ':' + sec);
                    	
                    	if (hr < 10) {hr = "0" + hr; }
                    	if (min < 10) {min = "0" + min;}
                    	if (sec < 10) {sec = "0" + sec;}
//                     	if (hr) {hr = "00";}
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
                   	
                   	<?php 
                   	if ($_REQUEST["start_timer"] == 1){
                   	?>
                   	$('#ticket_timer').trigger('click');
                   	<?php   
                   	}
                   	?>
                });
             </script>
		     <?php } else { ?>
		             <td width="25%">&nbsp;</td>
                     <td width="25%">&nbsp;</td>
             </tr>
             <?php }// <<< TIMER STUFF //////?>
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
	</div>
  </br>
  
  <table width="100%">
    <colgroup>
        <col width="180">
        <col>
    </colgroup> 
    <tr>
        <td class="content_row_header">
        <?php 
        if ($_REQUEST["returnhome"] == 1){?>
        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
        			onclick="window.location.href='index.php'">
        <?} else {?>
        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
        			onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
        <?}?>
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
    <table><tr><td align="left"><h3><i class="icon-comment"></i> Kommentare <a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$ticket->getId()?>&sort=asc"><img src="images/icons/arrow-090.png"/></a></h3></td></tr></table>
    
    <?php 
    foreach ($all_comments as $comment){
        if ($_USER->isAdmin() 
            || $comment->getVisability() == Comment::VISABILITY_PUBLIC 
            || $comment->getVisability() == Comment::VISABILITY_INTERNAL 
            || $comment->getCrtuser() == $_USER)
        {
            ?>
          	<table width="100%" border="1">
              <tr <?php 
              if ($_USER->isAdmin() || $_USER == $comment->getCrtuser()){
                  echo 'onclick="callBoxFancytktc(\'libs/modules/comment/comment.edit.php?cid='.$comment->getId().'&tktid='.$ticket->getId().'\');"';
              }
              ?>>
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
                <?php 
                if ($comment->getCrtuser()->getId()>0){
                    $crtby = $comment->getCrtuser()->getNameAsLine();
                } elseif ($comment->getCrtcp()->getId()>0){
                    $crtby = $comment->getCrtcp()->getNameAsLine2();
                }
                ?>
                <td width="25%"><?php echo $crtby;?></td>
              </tr>
              <?php if ($comment->getState() > 0){?>
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
                                echo '<span><a href="'.Attachment::FILE_DESTINATION.$c_attachment->getFilename().'" download="'.$c_attachment->getOrig_filename().'">'.$c_attachment->getOrig_filename().'</a></span></br>';
                            elseif ($c_attachment->getState() == 0 && $_USER->isAdmin())
                                echo '<span><del><a href="'.Attachment::FILE_DESTINATION.$c_attachment->getFilename().'" download="'.$c_attachment->getOrig_filename().'">'.$c_attachment->getOrig_filename().'</a></del></span></br>';
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
