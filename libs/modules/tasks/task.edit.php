<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			08.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

$task = new Task((int)$_REQUEST["bid"]);

if($_REQUEST["subexec"] == "save"){
	$task->setTitle(trim(addslashes($_REQUEST["task_title"])));
	$task->setContent(trim(addslashes($_REQUEST["task_content"])));
	
	$due_date = 0;
	if($_REQUEST["task_due_date"] != "")
	{
	    $due_date = explode('.', trim(addslashes($_REQUEST["task_due_date"])));
	    $due_date = mktime(0,0,0,$due_date[1],$due_date[0],$due_date[2]);
	}
	
	$task->setDue_date($due_date);
	$task->setPrio($_REQUEST["task_prio"]);
	
	$ret_save = $task->save();
	$savemsg = getSaveMessage($ret_save)." ".$DB->getLastError();
}


?>

<script language="javascript">
$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
	$('#task_due_date').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "images/icons/calendar-blue.png",
                buttonImageOnly: true,
                onSelect: function(selectedDate) {
                    checkDate(selectedDate);
                }
			});

});
</script>

<table width="100%">
	<tr>
		<td width="200" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
			<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Aufgabe erstellen')?>
			<?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Aufgabe bearbeiten')?>
		</td>
		<td align="right"><?=$savemsg?></td>
	</tr>
</table>

<form 	action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="task_edit" id="task_edit"   
		onSubmit="return checkForm(new Array(this.task_title, this.task_content))">
	<div class="box1">
		<input type="hidden" name="exec" value="edit"> 
		<input type="hidden" name="subexec" value="save"> 
		<input type="hidden" name="bid" value="<?=$task->getId()?>">
		<table width="100%">
			<colgroup>
				<col width="170">
				<col>
			</colgroup>
			<tr>
				<td class="content_row_header"><?=$_LANG->get('Titel')?> *</td>
				<td class="content_row_clear">
				<input id="task_title" name="task_title" type="text" class="text" 
					value="<?=$task->getTitle()?>" style="width: 250px">
				</td>
			</tr>
			<tr>
				<td class="content_row_header" valign="top"><?=$_LANG->get('Inhalt')?> *</td>
				<td class="content_row_clear" valign="top">
					<textarea id="task_content" name="task_content" class="text" 
						style="width: 500px; height: 150px; " ><?=$task->getContent()?></textarea>
				</td>
			</tr>
			<tr>
				<td class="content_row_header" valign="top"><?=$_LANG->get('Fälligkeit')?> *</td>
				<td class="content_row_clear" valign="top">
                    <input name="task_due_date" id="task_due_date" style="width:100px"
                        value="<? if($task->getDue_date() > 0) echo date('d.m.Y', $task->getDue_date())?>">
				</td>
			</tr>
			<tr>
				<td class="content_row_header" valign="top"><?=$_LANG->get('Priorität')?> *</td>
				<td class="content_row_clear" valign="top">
                   <select name="task_prio" style="width:330px" class="text">
                       <option value="0"><?=$_LANG->get('keine Priorität')?></option>
                      <? 
                           for($i = 1; $i <= 10; $i++)
                           {
                               echo '<option value="'.$i.'" ';
                               if($task->getPrio() == $i) echo "selected";
                               echo '>'.$i.'</option>';
                           }
                       ?>
                   </select>
				</td>
			</tr>
			
			
			<?if ($task->getCrt_usr()->getId() > 0){?>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Erstellt von')?></td>
					<td class="content_row_clear">
						<?if($task->getCrt_usr()->getId() > 0) echo $task->getCrt_usr()->getNameAsLine()?>
					</td>
				</tr>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Erstellt am')?></td>
					<td class="content_row_clear">
						<?if($task->getCrt_date() > 0) echo date('d.m.Y - H:i:s',$task->getCrt_date())?>
					</td>
				</tr>
			<?}?>
		</table>
	</div>
	<br/>
	<?// Speicher & Navigations-Button ?>
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