<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			28.06.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once './libs/modules/tickets/ticket.class.php';
require_once './libs/modules/organizer/nachricht.class.php';

$tktid = (int)$_REQUEST[tktid]; 

//Ticket loeschen
if((int)$_REQUEST["deltktid"] > 0){
	$del_ticket = new Ticket($_REQUEST["deltktid"]);
	$del_ticket->delete();
}

// Neues Ticket anlegen
if($_REQUEST["exec"] == "new"){
	$ticket = new Ticket();
	$header_title = $_LANG->get('Ticket erstellen');
}

// Ticket bearbeiten
if($_REQUEST["exec"] == "edit"){
	$ticket = new Ticket($tktid);
	$header_title = $_LANG->get('Ticketdetails');
}

// Aenderung eines Status eines Kommentars
if($_REQUEST["setComStatus"] != ""){
	$tktcom = new Ticketcomment((int)$_REQUEST["comid"]);
	$tktcom->setState((int)$_REQUEST["setComStatus"]);
	$savemsg = getSaveMessage($tktcom->save()).$DB->getLastError();
}

// Aenderung eines Status eines Tickets
if($_REQUEST["setStatus"] != ""){
	$ticket = new Ticket($tktid);
	$ticket->setState4((int)$_REQUEST["setStatus"]);
	$savemsg = getSaveMessage($ticket->save()).$DB->getLastError();
} 

// Nachricht senden, dann Speichern
if($_REQUEST["subexec"] == "send"){

	$text = 'Der Kunde hat im Ticket '.$ticket->getTitle().' ('.$ticket->getTicketnumber().') einen wichtigen Kommentar verfasst';
	$to = Array();
	if($ticket->getContactperson()->getId()>0){
		$to[] = $ticket->getContactperson();
	}
	if($ticket->getContactperson2()->getId()>0){
		$to[] = $ticket->getContactperson2();
	}
	if($ticket->getContactperson3()->getId()>0){
		$to[] = $ticket->getContactperson3();
	}

	$nachricht = new Nachricht();
	$nachricht->setFrom($_USER);
	$nachricht->setTo($to);
	$nachricht->setSubject("Neuer Kommentar im Ticket {$ticket->getTicketnumber()}");
	$nachricht->setText($text);
	$ret = $nachricht->send();
	// Damit nach dem Senden auch gespeichert wird
	$_REQUEST["subexec"] = "save";
}

if($_REQUEST["subexec"] == "save"){

	$ticket->setTitle(trim(addslashes($_REQUEST["tkt_title"])));
	// $ticket->setTicketnumber(trim(addslashes("TKT-00003")));
	$ticket->setCommentextern(trim(addslashes($_REQUEST["tkt_com_ext"])));
	$ticket->setCustomer(new BusinessContact((int)$_REQUEST["tkt_customer"]));
	$ticket->setState4(3);
	$ticket->setPrivat(0);
	
	// wenn neu, dann muss da hier wieder rien !!
	//$ticket->setContactperson(new User((int)$_REQUEST["tkt_contactperson"]));

	if ((int)$_REQUEST["tkt_due"] != 0){
		$_REQUEST["tkt_due"] = explode(".", $_REQUEST["tkt_due"]);
		$ticket->setDue((int)mktime(12, 0, 0, $_REQUEST["tkt_due"][1], $_REQUEST["tkt_due"][0], $_REQUEST["tkt_due"][2]));
	} else {
		$ticket->setDue(0);
	}
	$tkt_saver = $ticket->save();

	$savemsg = getSaveMessage($tkt_saver)." ".$DB->getLastError();

	if($tkt_saver){
		if($_REQUEST["tc_comment"] != NULL && $_REQUEST["tc_comment"] != ""){
			$ticketid = $ticket->getId();
			$tkt_comment = new Ticketcomment();
			$tkt_comment->setCrtuser($_USER);
			$tkt_comment->setCrtdate(time());
			$tkt_comment->setState(1);
			$tkt_comment->setCustVisible(1);
			$tkt_comment->setTicketid($ticketid);
			$tkt_comment->setComment(trim(addslashes($_REQUEST["tc_comment"])));
	
			if($tkt_comment->save()){
				$savemsg .= ".";
			} else{
				$savemsg = "Ticket gespeichert, aber: ".$DB->getLastError();
			}
		}
	}
	$tktid = $ticket->getId();
}

$all_tickets = Ticket::getAllTicketsByCustomer($busicon->getId());

if ( $_REQUEST["exec"] == "edit" || $_REQUEST["exec"] == "new"){
	
	// Ticketdetails holen
	$ticket = new Ticket($tktid);
	
	// Kommentare zu einem Ticket holen
	$all_commments = Ticketcomment::getAllTicketcommentsForCustomer($ticket->getId());
	
	// Ticket Details anzeigen ?>
	<style type="text/css"><!-- @import url( ./libs/jscripts/datepicker/datepicker.css); //--></style>
	<script language="JavaScript" >
	$(function() {
		$.datepicker.setDefaults($.datepicker.regional['de']);
		
		$('#tkt_due').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
	            showOn: "button",
	        	buttonImage: "../images/icons/calendar-blue.png",
	            buttonImageOnly: true,
	            onSelect: function ()	            {
	                this.focus();
	            }
			}
	     );
	});
	</script>
	<h3>Ticketdetails</h3>
	<form action="index.php" method="post" name="tkt_edit" id="tkt_edit"
			onSubmit="return checkform(new Array(this.tkt_title))">
		<input type="hidden" name="pid" value="<?=$_REQUEST["pid"]?>">
		<input type="hidden" name="exec" value="edit">
		<input type="hidden" name="subexec" id="subexec" value="save">
		<input type="hidden" name="tktid" value="<?=$ticket->getId()?>">
		<input type="hidden" name="tkt_customer" value="<?=$busicon->getId()?>">
		<div class="box2">
		<table width="100%">
			<colgroup>
				<col width="160">
				<col>
				<col width="160">
				<col>
			</colgroup>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Titel')?> *</td>
				<td class="content_row_clear">
					<input id="tkt_title" name="tkt_title" type="text" class="text" 
						value="<?=$ticket->getTitle()?>" style="width: 370px">
				</td>
				<td class="content_row_header"><?=$_LANG->get('Ansprechpartner')?></td>
				<td class="content_row_clear">
					<?=$ticket->getContactperson()->getNameAsLine()?>
				</td>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Ticketnummer')?></td>
				<td class="content_row_clear" height="25px">
					<?=$ticket->getTicketnumber()?>
				</td>
			</tr>
			<? // Wenn Ticket neu, darf man den Status noch nicht aendern
				if($_REQUEST["exec"] != "new"){?>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Status')?></td>
					<td class="content_row_clear">
						<table cellpadding="1" cellspacing="0">
			                <tr>
			                    <td width="25">
			                        <a href="index.php?pid=<?=$_REQUEST["pid"]?>&tktid=<?=$ticket->getId()?>&exec=edit&setStatus=3">
			                            <? 
			                            echo '<img class="select" src="../images/status/';
			                            if($ticket->getState4() == 3)
			                                echo 'tkt_k3.png';
			                            else
			                                echo 'black.gif';
			                            echo '" title="'.getTicketStatus4(3).'">';
			                            ?>
			                        </a>
			                    </td>
			                    <td width="25">
			                        <a href="index.php?pid=<?=$_REQUEST["pid"]?>&tktid=<?=$ticket->getId()?>&exec=edit&setStatus=2">
			                            <? 
			                            echo '<img class="select" src="../images/status/';
			                            if($ticket->getState4() == 2)
			                                echo 'tkt_2.png';
			                            else
			                                echo 'black.gif';
			                            echo '" title="'.getTicketStatus4(2).'">';
			                            ?>
			                        </a>
			                    </td>
			                    <td width="25">
			                        <a href="index.php?pid=<?=$_REQUEST["pid"]?>&tktid=<?=$ticket->getId()?>&exec=edit&setStatus=1">
			                            <? 
			                            echo '<img class="select" src="../images/status/';
			                            if($ticket->getState4() == 1)
			                                echo 'tkt_1.png';
			                            else
			                                echo 'black.gif';
			                            echo '" title="'.getTicketStatus4(1).'">';
			                            ?>
			                        </a>
			                    </td>
			                </tr>
		                </table>  
					</td>
				</tr>
				<? } ?>
				<!-- tr>
					<td class="content_row_header"><?=$_LANG->get('Beschreibung')?></td>
					<td class="content_row_clear">
						<textarea id="tkt_com_ext" name="tkt_com_ext" rows="4" cols="50" class="text"><?=$ticket->getCommentextern()?></textarea>
					</td>
				</tr-->
				<tr>
					<td class="content_row_header"><?=$_LANG->get('F&auml;llig am')?>: </td>
					<td class="content_row_clear">
						<input type="text" style="width:100px" id="tkt_due" name="tkt_due"
								class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency"
								value="<?if($ticket->getDue() != 0){ echo date('d.m.Y', $ticket->getDue());}?>">
					</td>
				</tr>
			</table>
		</div>
		<br/>
	<? 	if(count($all_commments) >6){
			// Speicher & Navigations-Button  doppelt einbelden wenn zu viele Kommentare ?>
			<table width="100%">
			    <colgroup>
			        <col width="180">
			        <col>
			    </colgroup> 
			    <tr>
			        <td>
			         &ensp; 
			        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
			        			onclick="window.location.href='index.php?pid=<?=$_REQUEST["pid"]?>'">
			        </td>
			        <td class="content_row_clear" align="right">
			        	<?if($_REQUEST["exec"] != "new"){?>
			        		<input type="Button" value="<?=$_LANG->get('Speichern u. Anspr. informieren')?>" class="button"
			        			   onclick=" document.getElementById('subexec').value='send';document.getElementById('tkt_edit').submit();">
						<?}?>
			        </td>
			        <td class="content_row_clear" align="center">
			        	<input type="submit" value="<?=$_LANG->get('Speichern')?>"> &ensp;
			        </td>
			    </tr>
			</table>		
			<br/>
	<?	} ?>
		
		<div class="box1" style="min-height:60px;">
		
		<table>
			<colgroup>
				<col width="550">
				<col width="400">
			</colgroup>
			<tr>
				<td><h1>&ensp;<?=$_LANG->get('Kommentare')?>:</h1></td>
				<td>&ensp;<b><?=$_LANG->get('Kommentar hinzuf&uuml;gen');?>:</b></td>
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
						<?		if ($comment->getCrtuser()->getId() > 1 ){
									$username = $comment->getCrtuser()->getNameAsLine();
								} else {
									$username = "Der Kunde ";									
								}
								
								echo "<b>".$username ."</b> ". $_LANG->get("schrieb am"). " " .date('d.m.Y - H:i',$comment->getCrtdate()); ?>
								&ensp;
								<a href="index.php?pid=<?=$_REQUEST["pid"]?>&tktid=<?=$ticket->getId()?>&exec=edit&comid=<?=$comment->getId()?>&setComStatus=1">
			                       	<img src="../images/status/<? if($comment->getState() == 1){ echo 'white_small.gif';} else {echo 'black_small.gif';}?>" 
			                       		 class="select" title="<?=getTicketcommentStatus(1)?>" width="10"></a>
			                    <a href="index.php?pid=<?=$_REQUEST["pid"]?>&tktid=<?=$ticket->getId()?>&exec=edit&comid=<?=$comment->getId()?>&setComStatus=2">
			                       	<img src="../images/status/<? if($comment->getState() == 2){ echo 'lila_small.gif';} else {echo 'black_small.gif';}?>" 
			                       		 class="select" title="<?=getTicketcommentStatus(2)?>" width="10"></a>
			                    <a href="index.php?pid=<?=$_REQUEST["pid"]?>&tktid=<?=$ticket->getId()?>&exec=edit&comid=<?=$comment->getId()?>&setComStatus=3">
			                       	<img src="../images/status/<? if($comment->getState() == 3){ echo 'green_small.gif';} else {echo 'black_small.gif';}?>" 
			                       		  class="select" title="<?=getTicketcommentStatus(3)?>"  width="10"></a>
							
							</td>
						</tr>
						<tr class="<?=getRowColor($x)?>">
							<td class="content_row">
								<?=nl2br($comment->getComment())?>
							</td>
						</tr>
						<tr class="<?=getRowColor($x)?>">
							<td class="content_row_clear">&ensp;</td>
						</tr>
					<?	$x++;
						}
						if(count($all_commments) == 0 || $all_commments == false){
							echo "&ensp; ".$_LANG->get("Keine Kommentare vorhanden");
						}?>
					
					</table>
				</td>
				<td valign="top">
					&ensp;<textarea name="tc_comment" id="tc_comment" rows="5" cols="50"></textarea>
				</td>
			</tr>
		</table>
		</div>
		
		<?// Speicher & Navigations-Button ?>
		<table width="100%">
		    <colgroup>
		        <col width="180">
		        <col>
		    </colgroup> 
		    <tr>
		        <td>
		         	&ensp; 
		        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
		        			onclick="window.location.href='index.php?pid=<?=$_REQUEST["pid"]?>'">
		        </td>
		        <td class="content_row_clear" align="right">
			        	<?if($_REQUEST["exec"] != "new"){?>
			        		<input type="Button" value="<?=$_LANG->get('Speichern u. Anspr. informieren')?>" class="button"
			        			   onclick=" document.getElementById('subexec').value='send';document.getElementById('tkt_edit').submit();">
						<?}?>
			        </td>
		        <td class="content_row_clear" align="right">
		        	<input type="submit" class="button" value="<?=$_LANG->get('Speichern')?>"> &ensp; 
		        </td>
		    </tr>
		</table>
	</form>
<?
} else { 
// Alle Tickets des Kunden anzeigen ?>
	
<script language="javascript">
	function askDel(myurl)
	{
	   if(confirm("Sind Sie sicher?"))
	   {
	      if(myurl != '')
	         location.href = myurl;
	      else
	         return true;
	   }
	   return false;
	}
</script>

<div class="box2" style="min-height:180px;">
<table cellpadding="5" cellspacing="0" border="0" width="100%">
    <colgroup>
        <col>
        <col width="150">
        <col width="120">
        <!-- <col width="120"> -->
        <col width="160">
    </colgroup>
    <tr>
        <td class="filerow_header">Titel</td>
        <td class="filerow_header">Status</td>
        <td class="filerow_header">F&auml;llig am</td>
        <td class="filerow_header">Optionen</td>
    </tr>
    
    <? $x = 0; foreach($all_tickets as $ticket){ ?>

    <tr class="filerow">
        <td class="filerow"><?=$ticket->getTitle()?></td>
        <td class="filerow">
        	<table cellpadding="1" cellspacing="0">
                <tr>
                    <td width="25">
                        <a href="index.php?pid=<?=$_REQUEST["pid"]?>&tktid=<?=$ticket->getId()?>&setStatus=1">
                            <? 
                            echo '<img class="select" src="../images/status/';
                            if($ticket->getState4() == 1)
                                echo 'tkt_1.png';
                            else
                                echo 'black.gif';
                            echo '" title="'.getTicketStatus4(1).'">';
                            ?>
                        </a>
                    </td>
                    <td width="25">
                        <a href="index.php?pid=<?=$_REQUEST["pid"]?>&tktid=<?=$ticket->getId()?>&setStatus=2">
                            <? 
                            echo '<img class="select" src="../images/status/';
                            if($ticket->getState4() == 2)
                                echo 'tkt_2.png';
                            else
                                echo 'black.gif';
                            echo '" title="'.getTicketStatus4(2).'">';
                            ?>
                        </a>
                    </td>
                    <td width="25">
                        <a href="index.php?pid=<?=$_REQUEST["pid"]?>&tktid=<?=$ticket->getId()?>&setStatus=3">
                            <? 
                            echo '<img class="select" src="../images/status/';
                            if($ticket->getState4() == 3)
                                echo 'tkt_k3.png';
                            else
                                echo 'black.gif';
                            echo '" title="'.getTicketStatus4(3).'">';
                            ?>
                        </a>
                    </td>
	             </tr>
			 </table>	
        </td>
        <td class="filerow">
        	<?if($ticket->getDue() != 0){
        		echo date('d.m.Y', $ticket->getDue());
        	}?>
        </td>
        <td class="filerow">
            <a href="index.php?pid=<?=$_REQUEST["pid"]?>&tktid=<?=$ticket->getId()?>&exec=edit" class="button">Ansehen</a>
            <a href="#" onclick="askDel('index.php?pid=<?=$_REQUEST["pid"]?>&deltktid=<?=$ticket->getId()?>')" class="button_del">L&ouml;schen</a>
        </td>
    </tr>
    <? $x++; } ?>
</table>
</div>
<? } ?>