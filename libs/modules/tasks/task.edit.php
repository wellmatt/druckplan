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
//                showOn: "button",
//                buttonImage: "images/icons/calendar-blue.png",
//                buttonImageOnly: true,
                onSelect: function(selectedDate) {
                    checkDate(selectedDate);
                }
			});

});
</script>
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				<?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Aufgabe erstellen')?>
				<?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Aufgabe bearbeiten')?>
				</td>
			</h3>
	  </div>
	  <div class="panel-body">
		  <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="task_edit" id="task_edit" class="form-horizontal">
			 	  <input type="hidden" name="exec" value="edit">
				  <input type="hidden" name="subexec" value="save">
				  <input type="hidden" name="bid" value="<?=$task->getId()?>">

			  <div class="form-group">
				  <label for="" class="col-sm-1 control-label">Titel</label>
				  <div class="col-sm-4">
					  <input id="task_title" name="task_title" type="text" class="form-control"
							 value="<?=$task->getTitle()?>">
				  </div>
			  </div>

			  <div class="form-group">
				  <label for="" class="col-sm-1 control-label">Inhalt</label>
				  <div class="col-sm-4">
					 <textarea id="task_content" name="task_content" class="form-control"><?=$task->getContent()?></textarea>
				  </div>
			  </div>

			  <div class="form-group">
				  <label for="" class="col-sm-1 control-label">Fälligkeit</label>
				  <div class="col-sm-4">
					  <input name="task_due_date" id="task_due_date" class="form-control"
							 value="<? if($task->getDue_date() > 0) echo date('d.m.Y', $task->getDue_date())?>">
				  </div>
			  </div>

			  <div class="form-group">
				  <label for="" class="col-sm-1 control-label">Priorität</label>
				  <div class="col-sm-4">

					  <select name="task_prio" class="form-control">
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
				  </div>
			  </div>

			  <?if ($task->getCrt_usr()->getId() > 0){?>
			  <div class="form-group">
				  <label for="" class="col-sm-1 control-label">Erstellt von</label>
				  <div class="col-sm-4">
					  <?if($task->getCrt_usr()->getId() > 0) echo $task->getCrt_usr()->getNameAsLine()?>
				  </div>
			  </div>

			  <div class="form-group">
				  <label for="" class="col-sm-1 control-label">Erstellt am</label>
				  <div class="col-sm-4">
					  <?if($task->getCrt_date() > 0) echo date('d.m.Y - H:i:s',$task->getCrt_date())?>
				  </div>
			  </div>
			  <?}?>

	  </div>
</div>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#task_edit').submit();",'glyphicon-floppy-disk');
if ($task->getId()>0){
	$quickmove->addItem('Löschen', '#', "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&delid=".$task->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>


</form>