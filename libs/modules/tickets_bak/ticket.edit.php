<? //use Zend\Filter\Null;

// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			07.04.2014
// Copyright:		2013-14 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/organizer/nachricht.class.php';
require_once 'libs/modules/timekeeping/timekeeper.class.php';

$_REQUEST["tktid"] = (int)$_REQUEST["tktid"];

if($_REQUEST["exec"] == "new"){
	$ticket = new Ticket();
	$header_title = $_LANG->get('Ticket erstellen');
}

if($_REQUEST["exec"] == "edit"){
	$ticket = new Ticket($_REQUEST["tktid"]);
	$header_title = $_LANG->get('Ticketdetails');
}

if($_REQUEST["subexec"] == "newforplan"){
	$tmp_schedule = new Schedule($_REQUEST["planid"]);
	$tmp_order = new Order($tmp_schedule->getDruckplanId());
	$ticket->setCustomer($tmp_schedule->getCustomer());
	// $ticket->setCustomerContactPerson($tmp_schedule->getCustomer()->getMainContactperson());
	$ticket->setCustomerContactPerson($tmp_order->getCustContactperson());
	$ticket->setPlanning($tmp_schedule);
	$ticket->setOrder($tmp_order);
	$ticket->setTitle($tmp_order->getTitle());
	$_REQUEST["exec"] = "new";
}

// Status eines Ticket-Kommentars anpassen
if($_REQUEST["setComStatus"] != ""){
	$tktcom = new Ticketcomment((int)$_REQUEST["comid"]);
	$tktcom->setState((int)$_REQUEST["setComStatus"]);
	$savemsg = getSaveMessage($tktcom->save()).$DB->getLastError();
}

// Kommentar loeschen
if($_REQUEST["subexec"] == "deleteComment"){
	$tktcom = new Ticketcomment((int)$_REQUEST["delid"]);
	$savemsg = getSaveMessage($tktcom->delete()).$DB->getLastError();
}


// Sichtbarkeit eines Ticket-Kommentars anzeigen
if($_REQUEST["setComVisi"] != ""){
	$tktcom = new Ticketcomment((int)$_REQUEST["comid"]);
	$tktcom->setCustVisible((int)$_REQUEST["setComVisi"]);
	$savemsg = getSaveMessage($tktcom->save()).$DB->getLastError();
}

// Nachricht senden, dann Speichern
if($_REQUEST["subexec"] == "send"){
	
	if((int)$_REQUEST["tkt_customer"] >0){
		$busicon = new BusinessContact($_REQUEST["tkt_customer"]);
		$text = 'Im Ticket '.$ticket->getTitle().' ('.$ticket->getTicketnumber().') wurde ein wichtiger Kommentar verfasst';
		$to = Array();
		$to[] = $busicon;
		
		$nachricht = new Nachricht();
		$nachricht->setFrom($_USER);
		$nachricht->setTo($to);
		$nachricht->setSubject("Neuer Kommentar in einem Ticket ({$ticket->getTicketnumber()})");
		$nachricht->setText($text);
		$ret = $nachricht->send();
	}
	// Damit nach dem Senden auch gespeichert wird
	$_REQUEST["subexec"] = "save";
}

if($_REQUEST["subexec"] == "save"){
	
	$ticket->setTitle(trim(addslashes($_REQUEST["tkt_title"])));
	//$ticket->setTicketnumber(trim(addslashes($_REQUEST["tkt_number"])));
	$ticket->setCommentextern(trim(addslashes($_REQUEST["tkt_com_ext"])));
	$ticket->setCommentintern(trim(addslashes($_REQUEST["tkt_com_int"])));
	$ticket->setCustomer(new BusinessContact((int)$_REQUEST["tkt_customer"]));
	$ticket->setCustomerContactPerson(new ContactPerson((int)$_REQUEST["tkt_customer_cp"]));
	$ticket->setContactperson(new User((int)$_REQUEST["tkt_contactperson"]));
	$ticket->setContactperson2(new User((int)$_REQUEST["tkt_contactperson2"]));
	$ticket->setContactperson3(new User((int)$_REQUEST["tkt_contactperson3"]));
	$ticket->setOrder(new Order((int)$_REQUEST["tkt_order"]));
	$ticket->setPlanning(new Schedule((int)$_REQUEST["tkt_planning"]));
	if ((int)$_REQUEST["tkt_status1"] == 1){	
		$ticket->setState1(11);					// Status 1 ist in 2 eingebaut und nur noch fuer das Archiv verantwortlich
	} else {
		$ticket->setState1(1);
	}
	$ticket->setState2((int)$_REQUEST["tkt_status2"]);
	$ticket->setState3((int)$_REQUEST["tkt_status3"]);
	$ticket->setState4((int)$_REQUEST["tkt_status4"]);
	
	if ((int)$_REQUEST["tkt_due"] != 0){
		$_REQUEST["tkt_due"] = explode(".", $_REQUEST["tkt_due"]);
		$ticket->setDue((int)mktime(12, 0, 0, $_REQUEST["tkt_due"][1], $_REQUEST["tkt_due"][0], $_REQUEST["tkt_due"][2]));
	} else {
		$ticket->setDue(0);
	}

	//gln, Kennz. Privat 
	if ((int)$_REQUEST["tkt_privat"] != 0){
		$ticket->setPrivat(1);
	} else {
		$ticket->setPrivat(0);
	}
	
	$savemsg = getSaveMessage($ticket->save())." ".$DB->getLastError();
	
	if($_REQUEST["tc_comment"] != NULL && $_REQUEST["tc_comment"] != ""){
		
		/* gln */
		if ($_REQUEST["tc_id"] != NULL && $_REQUEST["tc_id"] != ""){
			$tkt_comment = new Ticketcomment($_REQUEST["tc_id"]);
		} else{
			$tkt_comment = new Ticketcomment();
			$tkt_comment->setCrtuser($_USER);
			$tkt_comment->setCrtdate(time());
			$ticketid = $ticket->getId();
			$tkt_comment->setTicketid($ticketid);
			$tkt_comment->setState(1);
			$tkt_comment->setCustVisible((int)$_REQUEST["tc_cust_visible"]);
		}
		$tkt_comment->setComment(trim(addslashes($_REQUEST["tc_comment"])));
		
		if($tkt_comment->save()){
			$savemsg .= ".";
		} else{
			$savemsg = "Ticket gespeichert, aber: ".$DB->getLastError();
		}
	}
}

// Notiz loeschen
if ($_REQUEST["subexec"] == "deletenote"){
	$del_note = new Notes($_REQUEST["delnoteid"]);
	$del_note->delete();
}

// Datei-Anhang einer Notiz loeschen
if ($_REQUEST["subexec"] == "deletenotefile"){
	$tmp_note = new Notes($_REQUEST["delnoteid"]);
	$del_filename = Notes::FILE_DESTINATION.$tmp_note->getFileName();
	unlink($del_filename);
	$tmp_note->setFileName("");
	$tmp_note->save();
}

// Notizen speichern
if ($_REQUEST["subexec"] == "save_notes"){
	if($_REQUEST["notes_title"] != NULL && $_REQUEST["notes_title"] != ""){
		$note = new Notes((int)$_REQUEST["notes_id"]);
		$note->setComment(trim(addslashes($_REQUEST["notes_comment"])));
		$note->setTitle(trim(addslashes($_REQUEST["notes_title"])));
		$note->setModule(Notes::MODULE_TICKETS); 
		$note->setObjectid($ticket->getId()); 
	  
		if (isset($_FILES["file_comment"])) {
			if ($_FILES["file_comment"]["name"] != "" && $_FILES["file_comment"]["name"] != NULL){

				$destination = Notes::FILE_DESTINATION;
				 
				// alte Datei loeschen, falls eine neue Datei hochgeladen wird
				$old_filename = $destination.$note->getFileName();
				unlink($old_filename);
				 
				$filename = date("Y_m_d-H_i_s_").$_FILES["file_comment"]["name"];
				$new_filename = $destination.$filename;
				$tmp_outer = move_uploaded_file($_FILES["file_comment"]["tmp_name"], $new_filename);
				 
				$note->setFileName($filename);
			}
		}
	  
		// Nur Admins und der Ersteller der Notiz duerfen diese bearbeiten und wenn es eine neue ist, muss Sie auch gespeichert werden
		if ($note->getCrtuser()->getId() == $_USER->getId() || $_USER->isAdmin() || $note->getId() == 0){
			$note->save();
		}
	  
		if($DB->getLastError()!=NULL && $DB->getLastError()!=""){
			$savemsg .= $DB->getLastError();
		}
	}
}

$alluser = User::getAllUser(User::ORDER_NAME);
$all_commments = Ticketcomment::getAllTicketcomments($ticket->getId());
$allorders = Order::getAllOrdersByCustomer(Order::ORDER_TITLE, $ticket->getCustomer()->getId());

if ($ticket->getCustomer()->getId() > 0){
	// $all_schedules = Schedule::getAllSchedulesByCalculation(Schedule::ORDER_NUMBER, $ticket->getOrder()->getId());
	$all_schedules = Schedule::getAllSchedulesForCustomer(Schedule::ORDER_NUMBER, $ticket->getCustomer()->getId());
} else {
	$all_schedules = Schedule::getAllSchedules(Schedule::ORDER_NUMBER);
} 

$allcustomer = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, BusinessContact::FILTER_ALL);
if ($ticket->getCustomer()->getId() != 0){
	$allcontactpersons = ContactPerson::getAllContactPersons($ticket->getCustomer(), ContactPerson::ORDER_NAME);
	$view_cust_cp = ''; 
} else {
	$allcontactpersons = NULL;
	$view_cust_cp = 'none';
}

$all_notes = Notes::getAllNotes(Notes::ORDER_CRTDATE, Notes::MODULE_TICKETS, $ticket->getId());
$note_counter = count($all_notes);



?>

<script type="text/javascript">
<? // Ansprechpartner von Geschaeftskontakten aktualisieren?>
function updateCustomerCP(custID){

	if(custID == 0){
		document.getElementById('tkt_customer_cp').innerHTML = '';
		document.getElementById('tkt_customer_cp').style.display='none';
		document.getElementById('span_customer_cp').style.display='none';
		updateOrderList(custID);
		//gln 24.01.14, Link zum Geschaeftskontakt aktualisieren
		updateCustomerHref(custID, 'href_customer');
	} else {
		$.post("libs/modules/tickets/ticket.ajax.php", 
			{ajax_action: 'updateCustomerCP', customerID : custID}, 
			 function(data) {
				// alert("-"+data+"-");
				var input = '<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>'+data;
				document.getElementById('tkt_customer_cp').innerHTML = input;
				document.getElementById('tkt_customer_cp').style.display='';
				document.getElementById('span_customer_cp').style.display='';
				updateOrderList(custID);
				updatePlanningList(custID);
				//gln 24.01.14, Link zum Geschaeftskontakt aktualisieren
				updateCustomerHref(custID, 'href_customer');
			});
	}
}

<? // Auftraege von Geschaeftskontakten aktualisieren?>
function updateOrderList(custID){

	$.post("libs/modules/tickets/ticket.ajax.php", 
		{ajax_action: 'updateOrderList', customerID : custID}, 
		 function(data) {
			// alert("-"+data+"-");
			var input = '<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>'+data;
			document.getElementById('tkt_order').innerHTML = input;
			updateOrderStatus(0);
		});
}

<? // Status-Bild vom Auftrag aktualisieren?>
function updateOrderStatus(orderID){

	if(orderID == 0){
		document.getElementById('img_orderstatus').style.display='none';
		document.getElementById('href_calculator').style.display='none';						
	} else {
		$.post("libs/modules/tickets/ticket.ajax.php", 
			{ajax_action: 'updateOrderStatus', orderID : orderID}, 
			 function(data) {
				// alert("-"+data+"-");
				document.getElementById('img_orderstatus').src = data;
				document.getElementById('img_orderstatus').style.display='';
				document.getElementById('href_calculator').style.display='';
				updateOrderHref(orderID);
			});
	}
}
<? // Link zu Planung aktualisieren?>
function updateOrderHref(orderID){

	if(orderID == 0){
		document.getElementById('href_calculator').style.display='none';
	} else {
		$.post("libs/modules/tickets/ticket.ajax.php", 
			{ajax_action: 'updateOrderHref', orderID : orderID}, 
			 function(data) {
				document.getElementById('href_calculator').href = data;
				document.getElementById('href_calculator').style.display='';
			});
	}
}

<? // Planungen von Geschaeftskontakten aktualisieren?>
function updatePlanningList(custID){
		
	$.post("libs/modules/tickets/ticket.ajax.php", 
		{ajax_action: 'updatePlanningList', customerID : custID}, 
		 function(data) {
			// alert("-"+data+"-");
			var input = '<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>'+data;
			document.getElementById('tkt_planning').innerHTML = input;
			updatePlanningStatus(0);
		});
}

<? // Statusbild der Planung aktualisieren?>
function updatePlanningStatus(planID){

	if(planID == 0){
		document.getElementById('href_schedule').style.display='none';
		document.getElementById('img_planningstatus').style.display='none';
	} else {
		$.post("libs/modules/tickets/ticket.ajax.php", 
			{ajax_action: 'updatePlanningStatus', planID : planID}, 
			 function(data) {
				document.getElementById('img_planningstatus').src = data;
				document.getElementById('img_planningstatus').style.display='';
				updatePlanningHref(planID);
			});
	}
}
<? // Link zu Planung aktualisieren ?>
function updatePlanningHref(planID){

	if(planID == 0){
		document.getElementById('href_schedule').style.display='none';
	} else {
		$.post("libs/modules/tickets/ticket.ajax.php", 
			{ajax_action: 'updatePlanningHref', planID : planID}, 
			 function(data) {
				document.getElementById('href_schedule').href = data;
				document.getElementById('href_schedule').style.display='';
			});
	}
}

<? // 31.01.14 gln Kommentar zum Editieren laden  ?>
function updateComment(commentID){


	if(commentID == 0){
		document.getElementById('tc_comment').value = '';
		document.getElementById('tc_comment').style.display='';
		document.getElementById('tc_id').value = '';
	} else {
		$.post("libs/modules/tickets/ticket.ajax.php", 
			{ajax_action: 'updateComment', commentID : commentID}, 
			 function(data) {
				document.getElementById('tc_comment').value = data;
				document.getElementById('tc_comment').style.display='';
				document.getElementById('tc_id').value = commentID;
				
			});
	}
}

<?/*gln 24.01.14, Link zum Geschaeftskontakt aktualisieren*/?>
function updateCustomerHref(id, idFeld){
	var verw = document.getElementById(idFeld);
	if(id == 0){
		verw.style.display='none';
	}
	else{							
		verw.style.display='';
		verw.href="index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id="+id;
	}
	return;
}

</script>

<?//------------------ Fuer Datumsfelder -------------------------------------------------------------------?>
<style type="text/css"><!-- @import url(./libs/jscripts/datepicker/datepicker.css); //--></style>
<script language="JavaScript" >
$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	
	$('#tkt_due').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "images/icons/calendar-blue.png",
                buttonImageOnly: true
			}
     );
});
</script>
<!-- script src="./libs/jscripts/autoresize.jquery.js" type="text/javascript"></script-->

<!-- <link rel="stylesheet" href="css/order.css" type="text/css" /> -->

<div class="menuorder">

	<span class="menu_order"  <?/*gln*/?>
		onclick="location.href='index.php?page=<?=$_REQUEST['page']?>&tktid=<?=$_REQUEST['tktid'] ?>&exec=edit'"><?=$_LANG->get('Ticket Details')?>
	</span>
	<?if($_REQUEST["exec"] != "new" && $ticket->getId() != 0){ ?>
		<span class="menu_order"  <?/*gln*/?>
			onclick="location.href='index.php?page=<?=$_REQUEST['page']?>&tktid=<?=$ticket->getId()?>&exec=edit&step=7'">
			<?=$_LANG->get('Notizen / Dateien')?> 
			<?if($note_counter > 0 && $note_counter != false){
				echo "(".$note_counter.")";	
			} ?>
		</span>
	<?}?>
</div>

<div class="orderMainform">

	<? 
	$step = (int)$_REQUEST["step"];
	switch($step)
	{
		case 7:		//gln 
			require_once 'ticket.notizen.php';
			break;
		default:?>

</div>
<br/>
<br/>
<br/>
<br/>

<table width="100%">
	<tr>
		<td width="200" class="content_header">
			 <h1><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"><?=$header_title?></h1>
		</td>
		<td align="right">
			<?=$savemsg?>
		</td>
	</tr>
</table>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="tkt_edit" id="tkt_edit"
		  onSubmit="return checkform(new Array(this.tkt_title))">
	<table>
	<tr>
		<td width="750px">
		<div class="box1" style="min-height:300px;">
			<input type="hidden" name="step" value="1"> 
			<input type="hidden" name="exec" value="edit"> 
			<input type="hidden" name="subexec" id="subexec" value="save"> 
			<input type="hidden" name="tktid" value="<?=$ticket->getId()?>">
			
			<table cellpadding="1" cellspacing="1">
				<colgroup>
					<col width="180">
					<col width="100">
					<col width="180" style="padding-right:16px;">
					<col>
				</colgroup>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Titel')?> *</td>
					<td class="content_row_clear" colspan="3">
						<input id="tkt_title" name="tkt_title" type="text" class="text" 
								value="<?=$ticket->getTitle()?>" style="width: 370px">
						 &emsp; &emsp; &emsp; &emsp; &ensp; &ensp;
					<?	/*$notes_info = $ticket->getNotesInfo();
						if($notes_info != "" && $notes_info != NULL){ ?>
							<img src="./images/icons/navigation-090-white.png" 
									title="<?=$notes_info?>" alt="<?=$notes_info?> " >
					<?	} else {
							echo "&ensp;";
						} */?>
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Ticketnummer')?></td>
					<td class="content_row_clear" colspan="3">
						<? /*** ?><input id="tkt_number" name="tkt_number" type="text" class="text" 
								value="<?=$ticket->getTicketnumber()?>" style="width: 370px"><? ***/ ?>
						<?=$ticket->getTicketnumber()?>
					</td>
				</tr>	
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Kunde/Lieferant')?>:</td>
					<td class="content_row_clear" colspan="3">
						<select id="tkt_customer" name="tkt_customer" style="width:370px"
								onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text"
								onchange="updateCustomerCP(this.value)">
							<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
						<? 	foreach ($allcustomer as $cust){?>
								<option value="<?=$cust->getId()?>"
									<?if ($ticket->getCustomer()->getId() == $cust->getId()) echo "selected" ?>><?= $cust->getNameAsLine()?></option>
						<?	} //Ende ?>
						</select>
						<?//gln 24.01.14, Link zum Geschaeftskontakt?>
            	       	&emsp; &emsp; &emsp; &emsp; &ensp; &ensp;
        	            <a id="href_customer" <?//gln?>
						   	style="<?if($ticket->getCustomer()->getId() == 0) echo "display:none;";?>"  class="icon-link"
	            	       	href="index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id=<?=$ticket->getCustomer()->getId()?>">
                    	  	<img alt="" src="images/icons/user--arrow.png" title="<?=$_LANG->get('Kunde/Lieferant aufrufen');?>" > 
							<?//<span id="test1"> Hallo hier! </span>?>
						</a> 				
					</td>
				</tr>
				<tr>
					<td class="content_row_header">
						<span style="display:<?=$view_cust_cp?>;" id="span_customer_cp">
							<?=$_LANG->get('Ansprechpartner')?>:
						</span>
					</td>
					<td class="content_row_clear" colspan="3">
						<select id="tkt_customer_cp" name="tkt_customer_cp" 
								onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text"
								style="width:370px; display:<?=$view_cust_cp?>; ">
							<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
						<? 	if(count($allcontactpersons) > 0){
								foreach ($allcontactpersons as $cp){?>
									<option value="<?=$cp->getId()?>"
										<?if ($ticket->getCustomerContactPerson()->getId() == $cp->getId()) echo "selected" ?>
										><?= $cp->getNameAsLine()?></option>
						<?		} 
							} ?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Vorgang')?>:</td>
					<td class="content_row_clear" colspan="3">
						<select id="tkt_order" name="tkt_order" style="width:370px"
								onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text"
								onchange="updateOrderStatus(this.value)">
							<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
						<? 	foreach ($allorders as $order){?>
								<option value="<?=$order->getId()?>"
									<?if ($ticket->getOrder()->getId() == $order->getId()) echo "selected" ?>>
										<?=$order->getNumber()?> - <?=$order->getTitle()?> &emsp; (<?=$order->getCustomer()->getNameAsLine()?>)</option>
						<?	} //Ende ?>
						</select>
						&emsp; 
						<img id="img_orderstatus" src="<?=$ticket->getOrder()->getStatusImage();?>" alt="Status" class="select"
							 style="<?if($ticket->getOrder()->getId() == 0) echo "display:none;";?>">
						&emsp; &emsp;
					<?if($_REQUEST["exec"] != "new" && $ticket->getId() != 0){ ?>
						<a href="index.php?page=libs/modules/calculation/order.php&exec=edit&id=<?=$ticket->getOrder()->getId()?>&step=4"
							id="href_calculator" style="<?if($ticket->getOrder()->getId() == 0) echo "display:none;";?>"  class="icon-link"
							><img src="images/icons/calculator--arrow.png" title="<?=$_LANG->get('Kalkulation aufrufen');?>"></a>
					<? 	} ?>
					</td>
				</tr>	
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Planung')?>:</td>
					<td class="content_row_clear" colspan="3">
						<select id="tkt_planning" name="tkt_planning" style="width:370px"
								onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text"
								onchange="updatePlanningStatus(this.value)">
							<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
						<? 	foreach ($all_schedules as $schedule){?>
								<option value="<?=$schedule->getId()?>"
									<?if ($ticket->getPlanning()->getId() == $schedule->getId()) echo "selected" ?>>
										<?=$schedule->getNumber()?> &emsp; (<?=$schedule->getCustomer()->getNameAsLine()?>)</option>
						<?	} //Ende ?>
						</select>
						&emsp; 
						<img id="img_planningstatus" src="<?=$ticket->getPlanning()->getStatusImage();?>" alt="Status" class="select"
							 style="<?if($ticket->getPlanning()->getId() == 0) echo "display:none;";?>">
						&emsp; &emsp;
					<?if($_REQUEST["exec"] != "new" && $ticket->getId() != 0){ ?>
						<a href="index.php?page=libs/modules/schedule/schedule.php&exec=parts&id=<?=$ticket->getPlanning()->getId()?>"
							id="href_schedule" style="<?if($ticket->getPlanning()->getId() == 0) echo "display:none;";?>"  class="icon-link"
							><img src="images/icons/calendar--arrow.png" title="<?=$_LANG->get('Planung aufrufen');?>"></a>
					<? 	} ?>
					</td>
				</tr>
				<tr><td colspan="4">&emsp;</td></tr>	
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Verantwortlicher')?> 1 </td>
					<td class="content_row_clear">
						<select id="tkt_contactperson" name="tkt_contactperson" class="text"
								onfocus="markfield(this,0)" onblur="markfield(this,1)" style="width:150px">
							<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
						<? 	foreach ($alluser as $us){?>
								<option value="<?=$us->getId()?>"
									<?if ($ticket->getContactperson()->getId() == $us->getId()) echo "selected" ?>><?= $us->getNameAsLine()?></option>
						<?	} //Ende ?>
						</select>
					</td>	
					<td class="content_row_header" align="right">
						<?=$_LANG->get('Vertrieb')?></td>
					<td class="content_row_clear">
						<select name="tkt_status3" style="width:118px" class="text"
								onfocus="markfield(this,0)" onblur="markfield(this,1)">
							<option value="10" <?if ($ticket->getState3() == 10) echo "selected" ?>><?=getTicketStatus3(10)?></option>
							<option value="3" <?if ($ticket->getState3() == 3) echo "selected" ?>><?=getTicketStatus3(3)?></option>
							<option value="4" <?if ($ticket->getState3() == 4) echo "selected" ?>><?=getTicketStatus3(4)?></option>
							<option value="5" <?if ($ticket->getState3() == 5) echo "selected" ?>><?=getTicketStatus3(5)?></option>
							<option value="6" <?if ($ticket->getState3() == 6) echo "selected" ?>><?=getTicketStatus3(6)?></option>
							<option value="7" <?if ($ticket->getState3() == 7) echo "selected" ?>><?=getTicketStatus3(7)?></option>
							<option value="8" <?if ($ticket->getState3() == 8) echo "selected" ?>><?=getTicketStatus3(8)?></option>
							<option value="9" <?if ($ticket->getState3() == 9) echo "selected" ?>><?=getTicketStatus3(9)?></option>
							<option value="11" <?if ($ticket->getState3() == 11) echo "selected" ?>><?=getTicketStatus3(11)?></option>
							<option value="1" <?if ($ticket->getState3() == 1) echo "selected" ?>><?=getTicketStatus3(1)?></option>
							<option value="0" <?if ($ticket->getState3() == 0) echo "selected" ?>><?=getTicketStatus3(0)?></option>
						</select>
					</td>
						<?/**
						<? // Ticket Status 1 (Allgemein)?>
						<?=$_LANG->get('Allgemein')?>
							<select name="tkt_status1" style="width:118px" class="text"
									onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
								<?for($i=1; $i<=4;$i++){?>
								<option value="<?=$i?>" <?if ($ticket->getState1() == $i) echo "selected" ?>><?=getTicketStatus1($i)?></option>
								<?}?>
								<option value="0" <?if ($ticket->getState1() == 0) echo "selected" ?>><?=$_LANG->get('Aus');?></option>
								<option value="11" <?if ($ticket->getState1() == 11) echo "selected" ?>><?=getTicketStatus1(11)?></option>
							</select>   ***/?>
				</tr>
				<tr>
					<td class="content_row_header"> <?=$_LANG->get('Verantwortlicher');?> 2 </td>
					<td class="content_row_clear">
						<select id="tkt_contactperson2" name="tkt_contactperson2" style="width:150px"
								onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
							<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
						<? 	foreach ($alluser as $us){?>
								<option value="<?=$us->getId()?>"
									<?if ($ticket->getContactperson2()->getId() == $us->getId()) echo "selected" ?>><?= $us->getNameAsLine()?></option>
						<?	} //Ende ?>
						</select>
					</td>
					<td class="content_row_header" align="right">
						<?=$_LANG->get('Grafik/Prod.');?>
					</td>
					<td class="content_row_clear">
						<select name="tkt_status2" style="width:118px" class="text"
									onfocus="markfield(this,0)" onblur="markfield(this,1)">
							<option value="7" <?if ($ticket->getState2() == 7) echo "selected" ?>><?=getTicketStatus2(7)?></option>
							<option value="2" <?if ($ticket->getState2() == 2) echo "selected" ?>><?=getTicketStatus2(2)?></option>
							<option value="3" <?if ($ticket->getState2() == 3) echo "selected" ?>><?=getTicketStatus2(3)?></option>
							<option value="4" <?if ($ticket->getState2() == 4) echo "selected" ?>><?=getTicketStatus2(4)?></option>
							<option value="5" <?if ($ticket->getState2() == 5) echo "selected" ?>><?=getTicketStatus2(5)?></option>
							<option value="6" <?if ($ticket->getState2() == 6) echo "selected" ?>><?=getTicketStatus2(6)?></option>
							<option value="1" <?if ($ticket->getState2() == 1) echo "selected" ?>><?=getTicketStatus2(1)?></option>
							<option value="0" <?if ($ticket->getState2() == 0) echo "selected" ?>><?=getTicketStatus2(0)?></option>
						</select>
					</td>
				</tr>
				<!-- tr>
					<td class="content_row_header"><?=$_LANG->get('Beschreibung')?></td>
					<td class="content_row_clear" colspan="2">
						<b><?=$_LANG->get('Intern');?></b> <br/>
						<textarea id="tkt_com_int" name="tkt_com_int" rows="4" cols="50" class="text"><?=$ticket->getCommentintern()?></textarea>
					</td>
					<td class="content_row_clear">
						&emsp; &emsp;
						<b><?=$_LANG->get('Extern');?></b> <br/>
						&emsp; &emsp;
						<textarea id="tkt_com_ext" name="tkt_com_ext" rows="4" cols="50" class="text"><?=$ticket->getCommentextern()?></textarea>
					</td>
				</tr-->
				
				<tr>
					<td class="content_row_header"> <?=$_LANG->get('Verantwortlicher');?> 3 </td>				
					<td class="content_row_clear">
						<select id="tkt_contactperson3" name="tkt_contactperson3" style="width:150px"
								onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
							<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
						<? 	foreach ($alluser as $us){?>
								<option value="<?=$us->getId()?>"
									<?if ($ticket->getContactperson3()->getId() == $us->getId()) echo "selected" ?>><?= $us->getNameAsLine()?></option>
						<?	} //Ende ?>
						</select>
					</td>
					<td class="content_row_header" align="right"><?=$_LANG->get('Kunde')?></td>
					<td class="content_row_clear">	
						<select name="tkt_status4" style="width:118px" class="text"
								onfocus="markfield(this,0)" onblur="markfield(this,1)">
							<?for($i=1; $i<=3;$i++){?>
							<option value="<?=$i?>" <?if ($ticket->getState4() == $i) echo "selected" ?>><?=getTicketStatus4($i)?></option>
							<?}?>
							<option value="0" <?if ($ticket->getState4() == 0) echo "selected" ?>><?=$_LANG->get('Aus');?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('F&auml;llig am')?>: </td>
					<td class="content_row_clear">
						<input type="text" style="width:132px" id="tkt_due" name="tkt_due"
								class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
								onfocus="markfield(this,0)" onblur="markfield(this,1)"
								value="<?if($ticket->getDue() != 0){ echo date('d.m.Y', $ticket->getDue());}?>">
					</td>
					<td class="content_row_header" align="right"><?=$_LANG->get('Archiv');?></td>
					<td class="content_row_clear">
						<input type="checkbox" value="1" name="tkt_status1" class="text"
								<?if($ticket->getState1() > 10) echo 'checked="checked"';?>>
				</tr>
			<?//gln 22.01.14, Aenderung Kennzeichen Privat ?>	
			<?//<tr><td colspan="4">&emsp;</td></tr> ?>
				<tr>
					<td colspan="2">&emsp;</td>
					<td class="content_row_header" align="right"><?=$_LANG->get('Privat');?></td>
					<td class="content_row_clear">
    	                <input type="checkbox" name="tkt_privat" value="1" class="text" <?if ($ticket->getPrivat()== 1) echo'checked="checked"'?>">
	                </td>
				</tr>	
				<tr>
				 	<td class="content_row_header">
						<?if($_REQUEST["exec"] != "new" && $ticket->getId() != 0){ 
							echo $_LANG->get('Erstellt');
						}?> 
					</td>
					<td class="content_row_clear" colspan="3">
					<?	if($_REQUEST["exec"] != "new" && $ticket->getId() != 0){
							if ($ticket->getCrtdate() > 0)
								echo date('d.m.Y H:i',$ticket->getCrtdate());
						}
						if($_REQUEST["exec"] != "new" && $ticket->getId() != 0){
							if ($ticket->getCrtuser()->getId() > 1){
								echo $_LANG->get(' von ').$ticket->getCrtuser()->getNameAsLine();
							} else {
								echo $_LANG->get(' vom Kunden');
							}
						}?>
					</td>
				</tr>
			</table>
		</div>
		</td>
		<td>&emsp;</td>
		<td>
			<?if($_REQUEST["exec"] != "new" && $ticket->getId() != 0){ ?>
				<div class="box2" style="min-height:300px;padding-left:16px;">
					<div style="overflow: auto; height:280px;">
					<?	$timer_moduleID = Timekeeper::MODULE_TICKET;
						$timer_objectID = $ticket->getId();
						// $div_height = "260px";
						require_once 'libs/modules/timekeeping/timekeeper.import.php';?>
					</div>
				</div>
			<?}?>
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<?// Speicher & Navigations-Button ?>
			<table width="100%">
			    <colgroup>
			        <col width="440">
			        <col width="340">
			        <col width="160">
			        <col>
			    </colgroup> 
			    <tr>
			        <td class="content_row_header">
			        <?	if($_REQUEST["exec"] != "new" && $ticket->getId() != 0){ ?>
			        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
			        			onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
			        <?	} ?>
			        </td>
			        <td class="content_row_clear" align="left">
			        	<input type="submit" value="<?=$_LANG->get('Speichern')?>">
			        </td>
			        <td class="content_row_clear" align="right">
			        	<? /**if($_REQUEST["exec"] != "new"){?>
			        		<input type="Button" value="<?=$_LANG->get('Speichern u. Kunden informieren')?>" class="button"
			        			   onclick=" document.getElementById('subexec').value='send'; askSubmit(document.getElementById('tkt_edit'));">
						<?}**/?> &emsp;
			        </td>
			        <td class="content_row_clear" align="right">
			        	<?if($_REQUEST["exec"] != "new" && $ticket->getId() != 0 && $_USER->isAdmin()){?>
			        		<input type="button" class="buttonRed" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&delid=<?=$ticket->getId()?>')" 
			        				value="<?=$_LANG->get('L&ouml;schen')?>">
			        	<?}?>
			        </td>
			    </tr>
			</table>
		</td>
	</tr>
	<?	if($_REQUEST["exec"] != "new" && $ticket->getId() != 0){ ?>
	<tr>
		<td>
			<div class="box2" style="min-height:250px;">	
			<table>
				<colgroup>
					<col width="670">
					<col width="20">
				</colgroup>
				<tr>
					<td>
						<?php // dsr, Kommentar-Export ?>
						<a class="link" target="_blank" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?= $_REQUEST['tktid']?>&commentexport=1" style="float: right; vertical-align: middle; text-decoration: none; color: inherit;" ><img style="vertical-align: top;" src="images/icons/table-export.png" /> Kommentar-Export (CSV)</a>
						
						<h1><?=$_LANG->get('Nachrichten')?> / <?=$_LANG->get('Kommentare')?>:</h1>
					</td>
					
				</tr>
				<tr>
					<td valign="top">
						<table width="100%" cellpadding="0" cellspacing="0">
							<colgroup>
								<col width="300">
							</colgroup>
						<?	$x=0;
							foreach ($all_commments as $comment){ ?>
								<tr class="<?=getRowColor($x)?>">
									<td class="content_row_clear">
									<?	if ($comment->getCrtuser()->getId() > 1 ){
											$username = $comment->getCrtuser()->getNameAsLine();
										} else {
											$username = "Der Kunde ";									
										}
										echo "<b>".$username."</b> ". $_LANG->get("schrieb am"). " " .date('d.m.Y - H:i',$comment->getCrtdate()); ?>
									</td>
									<td class="content_row_clear">
										<a href="index.php?page=<?=$_REQUEST['page']?>&tktid=<?=$ticket->getId()?>&exec=edit&comid=<?=$comment->getId()?>&setComStatus=1">
					                       	<img class="select" title="<?=getTicketcommentStatus(1)?>" width="10"
					                       		 src="./images/status/<? if($comment->getState() == 1){ echo 'white_small.gif';} else {echo 'black_small.gif';}?>"></a>
					                    <a href="index.php?page=<?=$_REQUEST['page']?>&tktid=<?=$ticket->getId()?>&exec=edit&comid=<?=$comment->getId()?>&setComStatus=2">
					                       	<img class="select" title="<?=getTicketcommentStatus(2)?>" width="10"
					                       		 src="./images/status/<? if($comment->getState() == 2){ echo 'lila_small.gif';} else {echo 'black_small.gif';}?>" ></a>
					                    <a href="index.php?page=<?=$_REQUEST['page']?>&tktid=<?=$ticket->getId()?>&exec=edit&comid=<?=$comment->getId()?>&setComStatus=3">
					                       	<img class="select" title="<?=getTicketcommentStatus(3)?>"  width="10"
												 src="./images/status/<? if($comment->getState() == 3){ echo 'green_small.gif';} else {echo 'black_small.gif';}?>"></a>
									</td>
									<td class="content_row_clear">
										<?=$_LANG->get('Sichtbarkeit (Kunde)');?>
										<a href="index.php?page=<?=$_REQUEST['page']?>&tktid=<?=$ticket->getId()?>&exec=edit&comid=<?=$comment->getId()?>&setComVisi=0">
					                       	<img class="select" title="<?=$_LANG->get('Nur Intern');?>"  width="10"
					                       		  src="./images/status/<? if($comment->getCustVisible() == 0){ echo 'light_blue_small.gif';} else {echo 'black_small.gif';}?>"></a>
					                    
					                       	<img class="select pointer" title="<?=$_LANG->get('F&uuml;r Kunde sichtbar');?>"  width="10"
					                       		  src="./images/status/<? if($comment->getCustVisible() == 1){ echo 'orange_small.gif';} else {echo 'black_small.gif';}?>"
					                       		  onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&tktid=<?=$ticket->getId()?>&exec=edit&comid=<?=$comment->getId()?>&setComVisi=1');">
									</td>
									<td class="content_row_clear">
										<?if($_USER->isAdmin() || $_USER->getID() == $comment->getCrtuser()->getId()){?>
											<?/* gln 29.01.14 Kommentar editierbar */?>
											<img alt="<?=$_LANG->get('Kommentar editieren');?>" src="images/icons/pencil.png" class="pointer icon-link"
											onclick="updateComment(<?=$comment->getId()?>)">
											&ensp;
											<img alt="<?=$_LANG->get('Kommentar entfernen');?>" src="images/icons/cross-script.png" class="pointer icon-link"
												 onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=edit&tktid=<?=$ticket->getId()?>&subexec=deleteComment&delid=<?=$comment->getId()?>')">
										<?}?>
									</td>
								</tr>
								<tr class="<?=getRowColor($x)?>">
									<td class="content_row" colspan="4">
										<?=nl2br($comment->getComment())?>
									</td>
								</tr>
								<tr class="<?=getRowColor($x)?>">
									<td class="content_row_clear" colspan="4">&ensp;</td>
								</tr>
							<?	$x++;
							}
							if(count($all_commments) == 0 || $all_commments == false){
								echo "&ensp; ".$_LANG->get("Keine Kommentare vorhanden");
							}?>
						
						</table>
					</td>
					<td valign="top">
						&emsp;
					</td>
				</tr>
			</table>
			</div>
		</td>
		<td>&emsp;</td>
		<td valign="top" width="220px" >
			<div class="box2" style="min-height:250px;">
				<?/*gln 03.02.14 Kommentare editierbar */?>
				<input type="hidden" id="tc_id" name="tc_id" value="">
				<table width="100%">
					<tr>
						<td>
							<h1><?=$_LANG->get('Nachricht')?> / <?=$_LANG->get('Kommentar');?></h1>
						</td>
						<td align="right" style="padding-right: 25px;">
							<? /** =$_LANG->get('F&uuml;r Kunde sichtbar');?>
							<input type="checkbox" name="tc_cust_visible" id="tc_cust_visible" value="1" />
							<? **/ ?>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<textarea name="tc_comment" id="tc_comment" style="width: 550px; height:150px"></textarea>
						</td>
					</tr>
				</table>
			</div>
		</td>
	</tr>
<? 	} ?>
</table>

<? 	if(count($all_commments) >6){
		// Speicher & Navigations-Button  doppelt einbelden wenn zu viele Kommentare ?>
		<table width="100%">
	    <colgroup>
	        <col width="280">
	        <col width="500">
	        <col width="160">
	        <col>
	    </colgroup> 
	    <tr>
	        <td class="content_row_header">
	        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
	        			onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
	        </td>
	        <td class="content_row_clear" align="left">
	        	<input type="submit" value="<?=$_LANG->get('Speichern')?>">
	        </td>
	        	<td class="content_row_clear" align="right">
	        	<? /*** if($_REQUEST["exec"] != "new"){?>
	        		<input type="Button" value="<?=$_LANG->get('Speichern u. Kunden informieren')?>" class="button"
	        			   onclick=" document.getElementById('subexec').value='send'; askSubmit(document.getElementById('tkt_edit'));">
				<?} **/ ?> &emsp;
	        </td>
	        <td class="content_row_clear" align="right">
	        	<?if($_REQUEST["exec"] != "new" && $ticket->getId() != 0 && $_USER->isAdmin()){?>
	        		<input type="button" class="buttonRed" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&delid=<?=$ticket->getId()?>')" 
	        				value="<?=$_LANG->get('L&ouml;schen')?>">
	        	<?}?>
	        </td>
	    </tr>
	</table>
		
		<br/>
<?	} ?>

</form>
<?
/***** Status Algemein ******************************
 * <table cellpadding="1" cellspacing="0">
		                <tr>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus1=3">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState1() == 3)
		                                echo 'tkt_a3.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus1(3).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus1=4">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState1() == 4)
		                                echo 'tkt_a4.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus1(4).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="29"> &ensp; </td>
		                    <td width="29"> &ensp; </td>
		                    <td width="29"> &ensp; </td>
		                    <td width="29"> &ensp; </td>
		                    <td width="29"> &ensp; </td>
		                    <td width="30"> &ensp; </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus1=2">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState1() == 2)
		                                echo 'tkt_2.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus1(2).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus1=1">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState1() == 1)
		                                echo 'tkt_1.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus1(1).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus1=0">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState1() == 0)
		                                echo 'black.gif';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus1(0).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="22"> &emsp; </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus1=11">
		                            <? 
		                            echo '<img class="select" src="./images/';
		                            if($ticket->getState1() == 11)
		                                echo 'icons/edit-outline.png';
		                            else
		                                echo 'status/gray.gif';
		                            echo '" title="'.getTicketStatus1(11).'">';
		                            ?>
		                        </a>
		                    </td>
		                </tr>
	                </table> *****/

/***** Satus Vertrieb ******
 * <table cellpadding="1" cellspacing="0">
		                <tr>
		                	<td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus3=3">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState3() == 3)
		                                echo 'tkt_v3.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus3(3).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus3=4">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState3() == 4)
		                                echo 'tkt_v4.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus3(4).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus3=5">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState3() == 5)
		                                echo 'tkt_v5.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus3(5).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus3=6">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState3() == 6)
		                                echo 'tkt_v6.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus3(6).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="29"> &ensp; </td>
		                    <td width="29"> &ensp; </td>
		                    <td width="29"> &ensp; </td>
		                    <td width="30"> &ensp; </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus3=2">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState3() == 2)
		                                echo 'tkt_2.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus3(2).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus3=1">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState3() == 1)
		                                echo 'tkt_1.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus3(1).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus3=0">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState3() == 0)
		                                echo 'black.gif';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus3(0).'">';
		                            ?>
		                        </a>
		                    </td>
		                </tr>
	                </table>  ****/


/********* Satus Produktion **********
 * <table cellpadding="1" cellspacing="0">
		                <tr>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus2=3">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState2() == 3)
		                                echo 'tkt_p3.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus2(3).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus2=4">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState2() == 4)
		                                echo 'tkt_p4.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus2(4).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus2=5">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState2() == 5)
		                                echo 'tkt_p5.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus2(5).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus2=6">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState2() == 6)
		                                echo 'tkt_p6.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus2(6).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus2=7">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState2() == 7)
		                                echo 'tkt_p7.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus2(7).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus2=8">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState2() == 8)
		                                echo 'tkt_p8.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus2(8).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus2=9">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState2() == 9)
		                                echo 'tkt_p9.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus2(9).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="30"> &ensp; </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus2=2">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState2() == 2)
		                                echo 'tkt_2.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus2(2).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus2=1">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState2() == 1)
		                                echo 'tkt_1.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus2(1).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus2=0">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState2() == 0)
		                                echo 'black.gif';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus2(0).'">';
		                            ?>
		                        </a>
		                    </td>
		                </tr>
	                </table>  ******/

/****** Status Kunden ******
 * <table cellpadding="1" cellspacing="0">
		                <tr>
		                	<td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus4=3">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState4() == 3)
		                                echo 'tkt_k3.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus4(3).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="29"> &ensp; </td>
		                    <td width="29"> &ensp; </td>
		                    <td width="29"> &ensp; </td>
		                    <td width="29"> &ensp; </td>
		                    <td width="29"> &ensp; </td>
		                    <td width="29"> &ensp; </td>
		                    <td width="30"> &ensp; </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus4=2">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState4() == 2)
		                                echo 'tkt_2.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus4(2).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus4=1">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState4() == 1)
		                                echo 'tkt_1.png';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus4(1).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus4=0">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState4() == 0)
		                                echo 'black.gif';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus4(0).'">';
		                            ?>
		                        </a>
		                    </td>
		                    <td width="30"> &ensp; </td>
		                    <!-- td width="25">
		                        <a href="index.php?tktid=<?=$ticket->getId()?>&exec=edit&setStatus4=4">
		                            <? 
		                            echo '<img class="select" src="./images/status/';
		                            if($ticket->getState4() == 4)
		                                echo 'white.gif';
		                            else
		                                echo 'gray.gif';
		                            echo '" title="'.getTicketStatus4(4).'">';
		                            ?>
		                        </a>
		                    </td-->
		                </tr>
	                </table>  *******/
?>

		<?	break;
	}
	?>

