<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('libs/modules/organizer/nachricht.class.php');
require_once ('libs/modules/tickets/ticket.class.php');
require_once ('libs/modules/notes/notes.class.php');

global $_CONFIG;
$_USER;

$_REQUEST["id"] = (int)$_REQUEST["id"];
//$businessContact = new BusinessContact($_REQUEST["id"]);

//$all_attributes = Attribute::getAllAttributesForCustomer();

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

//// Nachricht senden, dann Speichern
//if($_REQUEST["subexec"] == "send"){
//	$send_mail = true;
//	// Damit nach dem Senden auch gespeichert wird
//	$_REQUEST["subexec"] = "save";
//}
   
//   error_log( "vor sichere1");
//   error_log("-".$_REQUEST["subexec"]."-");
if ($_REQUEST["subexec"] == "save")
{  

//   error_log( "sichere1");
    // Notizen speichern
    if($_REQUEST["notes_title"] != NULL && $_REQUEST["notes_title"] != ""){
	    $note = new Notes((int)$_REQUEST["notes_id"]);
	    $note->setComment(trim(addslashes($_REQUEST["notes_comment"])));
	    $note->setTitle(trim(addslashes($_REQUEST["notes_title"])));
	    $note->setModule(Notes::MODULE_CALCULATION); //gln
	    $note->setObjectid($order->getId()); //gln
	    
	    /*echo "<pre>";
	    var_dump($_FILES["file_comment"]);
	    echo "</pre>";*/
	    
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
		// error_log( "sichere");
	    	$note->save();
	    }
	    
	    if($DB->getLastError()!=NULL && $DB->getLastError()!=""){
	    	$savemsg .= $DB->getLastError();
	    }
    }
    

}

//$show_tab=(int)$_REQUEST["tabshow"];
//$languages = Translator::getAllLangs(Translator::ORDER_NAME);
//$countries = Country::getAllCountries();
$all_notes = Notes::getAllNotes(Notes::ORDER_CRTDATE, Notes::MODULE_CALCULATION, $order->getId()); //gln
//$all_active_attributes = $businessContact->getActiveAttributeItems();


/**************************************************************************
 ******* 				Java-Script									*******
 *************************************************************************/
?>


<script language="javascript">
	$(function() {
		$( "#tabs" ).tabs({ selected: <?=$show_tab?> });
	});
</script>


<script language="javascript">
function checkpass(obj){
	
	// war mal angedacht als Sicherung fuer die Eingabe von 2 Passworten zum Vergleich auf Gleichheit
	
	// var shop_pass1 = document.getElementById('shop_pass1').value;
	// var shop_pass2 = document.getElementById('shop_pass2').value;
	// if (shop_pass1 != shop_pass2){
	// 	alert('<? // =$_LANG->get('Passw&ouml;rter stimmen nicht &uuml;berein')?>');
	// 	document.getElementById('shop_pass1').focus();
	// 	return false;
	// }
	return checkform(obj);
}

function dialNumber(link){
	$.get('http://'+link, //{exec: 'dial_number', link: link},
			function(data) {
				alert('Nummer gesendet.');
			}
	);
}

$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	
	$('#login_expire').datepicker(
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

function loadNoteDetails(noteid){
	$.post("libs/modules/notes/notes.ajax.php", 
        {exec: 'loadNoteDetails', noteid: noteid}, 
        function(data) {
        	var data = data.split('_+-+_+-+_');
        	document.getElementById('table_notes').style.display="";
        	document.getElementById('notes_id').value = data[0];
        	document.getElementById('notes_title').value = data[1];
            document.getElementById('notes_comment').innerHTML = data[2];
            document.getElementById('img_notes_clear').style.display="";
        });
}

function showNoteFields(){
	document.getElementById('table_notes').style.display="";
	document.getElementById('notes_id').value = '';
	document.getElementById('notes_title').value = '';
	document.getElementById('notes_comment').innerHTML = '';
}

</script>
<?
/**************************************************************************
 ******* 				HTML-Bereich								*******
 *************************************************************************/?>
	
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="user_form" id="user_form"
	onSubmit="return checkpass(new Array(this.name1));" enctype="multipart/form-data"> 
	<?// gucken, ob die Passwoerter (Webshop-Login) gleich sind und ob alle notwendigen Felder gef�llt sind?>
	
	<input type="hidden" name="exec" value="edit"> 
	<input type="hidden" name="step" value="7"> 
	<input type="hidden" name="subexec" id="subexec" value="save"> 
	<input type="hidden" name="subform" value="user_details">
	<input type="hidden" name="id" value="<?=$order->getId()?>"> <?/*gln*/?>
	
<div class="box1">	
	<? // ------------------------------------- Notizen ----------------------------------------------?>
	<?if($order->getId()){?>	
		<table width="100%">
				<tr>
					<td width="200" class="content_header">
						<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
						<?=$_LANG->get('Notizen');?>
						&emsp;
						<img id="img_notes_clear" src="images/icons/card--plus.png" title="<?=$_LANG->get('Neue Notiz');?>" 
							 class="pointer" onclick="showNoteFields()">
					</td>
					<td></td>
					<td width="200" class="content_header" align="right"><?=$savemsg?></td>
				</tr>
		</table>
		<table width="100%" cellpadding="0" cellspacing="0">
			<colgroup>
				<col width="250">
				<col>
				<col width="170">
				<col width="200">
				<col width="130">
			</colgroup>
			<tr>
			<? 
			if ($all_notes != false && count($all_notes) > 0){ ?>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Titel')?></td>
					<td class="content_row_header"><?=$_LANG->get('Inhalt')?></td>
					<td class="content_row_header"><?=$_LANG->get('Datei-Anhang')?></td>
					<td class="content_row_header"><?=$_LANG->get('Erstellt')?></td>
					<td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
				</tr>
			<?	foreach ($all_notes AS $n){ ?>
					<tr onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
						<td class="content_row" valign="top"><?=$n->getTitle()?></td>
						<td class="content_row">
							<?=nl2br(substr($n->getComment(), 0, 50))?>
						</td>
						<td class="content_row" valign="top">
						<? if ($n->getFileName() != "" && $n->getFileName() != NULL){?>
							<a href="<?=Notes::FILE_DESTINATION.$n->getFileName()?>" target="_blank"
								><img src="images/icons/navigation-270-frame.png" alt="<?=$_LANG->get('Datei aufrufen');?>" 
										title="<?=$n->getFileName()?>&ensp;<?=$_LANG->get('aufrufen');?>"></a>
							&emsp;
							<a href="#" 
								onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=edit&step=7&subexec=deletenotefile&id=<?=$order->getId()?>&delnoteid=<?=$n->getId()?>')"
								><img src="images/icons/cross-script.png" alt="<?=$_LANG->get('Anhang l&ouml;schen');?>" 
										title="<?=$_LANG->get('Anhang l&ouml;schen');?>"></a>
						<? } ?>&nbsp;
						</td>
						<td class="content_row" valign="top">
							<? echo date('d.m.Y', $n->getCrtdate())." ".$_LANG->get('von')."  ".$n->getCrtuser()->getNameAsLine();?>
						</td>
						<td class="content_row" valign="top">
							<?if($_USER->getId() == $n->getCrtuser()->getId()  || $_USER->isAdmin()){ ?>
								<img src="images/icons/application-import.png" title="<?=$_LANG->get('Notiz laden');?>" class="pointer" 
									 onclick="loadNoteDetails(<?=$n->getId();?>)"> 
							<? } ?>
							&ensp;
							<?if($_USER->getId() == $n->getCrtuser()->getId()  || $_USER->isAdmin()){?>
								<a href="#" 
									onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=edit&step=7&subexec=deletenote&id=<?=$order->getId()?>&delnoteid=<?=$n->getId()?>')"
									><img src="images/icons/cross-script.png" alt="<?=$_LANG->get('Notiz l&ouml;schen');?>" 
											title="<?=$_LANG->get('Notiz l&ouml;schen');?>"></a>
							<? } ?>
						</td>
					</tr>
			<? } 
			} else {?>
				<tr><td colspan="2"><span class="msg_error"><?=$_LANG->get('Keine Notizen hinterlegt');?></span></td></tr>
			<? } ?>
		</table>
		<br/>
		<table id="table_notes" style="display:none">
			<tr>
				<td class="content_row_header" colspan="2"><?=$_LANG->get('Notiz verfassen')?></td>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Titel')?></td>
				<td class="content_row_clear">
					<input name="notes_id" id="notes_id" value="" type="hidden" >
					<input 	name="notes_title" id="notes_title" style="width: 300px;" class="text" 
							value="" type="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">
				</td>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Bemerkung')?></td>
				<td class="content_row_clear">
					<textarea name="notes_comment" id="notes_comment" style="width: 482px;height: 150px;"><?//=$n->getComment()?></textarea>
				</td>
			</tr>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Datei-Anhang');?></td>
				<td class="content_row_clear">
					<input type="file" name="file_comment" id="file_comment" style="width: 480px;">
				</td>
			</tr>
		</table>
	<? } ?>
</div>
		
<? // ------------------------------------- Navigations und Speicher Buttons ------------------------------------?>
<table width="100%">
    <colgroup>
        <col width="150">
        <col width="380">
        <col width="420">
        <col>
    </colgroup> 
    <tr>
        <td class="content_row_header">
        </td>
        <td class="content_row_clear" align="right">
        </td>
        <td class="content_row_clear" align="right">
        </td>
        <td class="content_row_clear" align="center">
        	<?if($_USER->getId() != 14){ ?>
        		<input type="submit" value="<?=$_LANG->get('Speichern')?>">
        	<?}?>
        </td>
    </tr>
</table>
</form>		
<!--//-->