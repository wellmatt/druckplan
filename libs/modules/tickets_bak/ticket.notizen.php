<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       07.04.2014
// Copyright:     2012-14 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('libs/modules/organizer/nachricht.class.php');
require_once ('libs/modules/tickets/ticket.class.php');
require_once ('libs/modules/notes/notes.class.php');

global $_CONFIG;
$_USER;

$_REQUEST["id"] = (int)$_REQUEST["id"];

/**************************************************************************
 ******* 				Java-Script									*******
 *************************************************************************/
?>

<script language="javascript">

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
<br/>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="user_form" id="user_form"
	onSubmit="return checkpass(new Array(this.name1));" enctype="multipart/form-data"> 
	<?// gucken, ob die Passwoerter (Webshop-Login) gleich sind und ob alle notwendigen Felder gef�llt sind?>
	
	<input type="hidden" name="exec" value="edit"> 
	<input type="hidden" name="step" value="7"> 
	<input type="hidden" name="subexec" id="subexec" value="save_notes"> 
	<input type="hidden" name="subform" value="user_details">
	<input type="hidden" name="tktid" value="<?=$ticket->getId()?>"> <?/*gln*/?>
	
<div class="box1">	
		<? // ------------------------------------- Notizen ----------------------------------------------?>
		<?if($ticket->getId() > 0){?>	
			<table width="100%">
					<tr>
						<td width="400" class="content_header">
							<h1>
								<!-- img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"-->  
								<?=$_LANG->get('Notizen / Dateien zu ');?> <?=$ticket->getTicketnumber()?>
								&emsp; &emsp;
								<img id="img_notes_clear" src="images/icons/card--plus.png" title="<?=$_LANG->get('Neue Notiz');?>" 
									 class="pointer icon-link" onclick="showNoteFields()">
							</h1>
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
								<a  class="icon-link" href="<?=Notes::FILE_DESTINATION.$n->getFileName()?>" target="_blank"
									><img src="images/icons/navigation-270-frame.png" alt="<?=$_LANG->get('Datei aufrufen');?>" 
											title="<?=$n->getFileName()?>&ensp;<?=$_LANG->get('aufrufen');?>"></a>
								&emsp;
								<a href="#"  class="icon-link"
									onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=edit&step=7&subexec=deletenotefile&tktid=<?=$ticket->getId()?>&delnoteid=<?=$n->getId()?>')"
									><img src="images/icons/cross-script.png" alt="<?=$_LANG->get('Anhang l&ouml;schen');?>" 
											title="<?=$_LANG->get('Anhang l&ouml;schen');?>"></a>
							<? } ?>&nbsp;
							</td>
							<td class="content_row" valign="top">
								<? echo date('d.m.Y', $n->getCrtdate())." ".$_LANG->get('von')."  ".$n->getCrtuser()->getNameAsLine();?>
							</td>
							<td class="content_row" valign="top">
								<?if($_USER->getId() == $n->getCrtuser()->getId()  || $_USER->isAdmin()){ ?>
									<img src="images/icons/application-import.png" title="<?=$_LANG->get('Notiz laden');?>" class="pointer icon-link"
										 onclick="loadNoteDetails(<?=$n->getId();?>)"> 
								<? } ?>
								&ensp;
								<?if($_USER->getId() == $n->getCrtuser()->getId()  || $_USER->isAdmin()){?>
									<a href="#"  class="icon-link"
										onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=edit&step=7&subexec=deletenote&tktid=<?=$ticket->getId()?>&delnoteid=<?=$n->getId()?>')"
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
					<td class="content_row_header"><?=$_LANG->get('Titel')?>*</td>
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
	        	<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
	        			onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'"> <? /* &tktid=<?=$ticket->getId()?>&exec=edit */ ?>
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