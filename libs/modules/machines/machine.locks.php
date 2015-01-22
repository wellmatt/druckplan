<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       14.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/machines/machine.class.php';

if($_REQUEST["dellock"] && $_REQUEST["dellock"] != ""){
    $tmp_lock = new MachineLock((int)$_REQUEST["dellock"]);
    $tmp_lock->delete();
}

if($_REQUEST["subexec"] == "save")
{
    $machine = new Machine($_REQUEST["id"]);
    
    if ($_REQUEST["lock_start"] && $_REQUEST["lock_start"] != "" && $_REQUEST["lock_stop"] && $_REQUEST["lock_stop"] != ""){
        $mlock = new MachineLock();
        $mlock->setMachineid($machine->getId());
        $mlock->setStart(strtotime($_REQUEST["lock_start"]));
        $mlock->setStop(strtotime($_REQUEST["lock_stop"]));
        $mlock->save();
    }
}
?>

<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>


<script language="JavaScript">
$(function() {
	$('#lock_start').datetimepicker({
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
	$('#lock_stop').datetimepicker({
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
	 $( "#maschine" ).autocomplete({
		 source: "libs/modules/associations/association.ajax.php?ajax_action=search_maschine",
		 minLength: 2,
		 focus: function( event, ui ) {
    		 $( "#maschine" ).val( ui.item.label );
    		 return false;
		 },
		 select: function( event, ui ) {
    		 $( "#maschine" ).val( ui.item.label );
    		 $( "#id" ).val( ui.item.value );
    		 $( "#machine_form" ).submit();
    		 return false;
		 }
	 });
});
</script>

<table width="100%">
   <tr>
      <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> 
          <? echo $_LANG->get('Sperrzeiten')?>
      </td>
      <td align="right"><?=$savemsg?></td>
   </tr>
</table>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="machine_form" name="machine_form">
<input type="hidden" name="exec" value="edit">
<input type="hidden" name="subexec" value="save">
<input type="hidden" id="id" name="id" value="<?=$_REQUEST['id']?>">

<div class="box1">
    <table width="100%">
       <tr>
          <td width="200" class="content_header">Maschine suchen:</td>
          <td align="left">
            <input type="text" style="width:160px" id="maschine" name="maschine" onfocus="markfield(this,0)" onblur="markfield(this,1)"/>
    	  </td>
       </tr>
    </table>
</div>


<?php 
if ($_REQUEST['id'] && $_REQUEST['id'] > 0){
    $machine = new Machine((int)$_REQUEST['id']);
    if ($machine->getId() > 0){?>
    <div class="box2">
        <b><u><?=$machine->getName()?></u></b>
    	<table width="500" cellpadding="0" cellspacing="0" border="0">
    		<tr>
    			<td class="content_row_header" valign="top">Sperrzeit Start</td>
    			<td class="content_row_header" valign="top">Sperrzeit Ende</td>
    		</tr>
    		<?php 
    		$all_locks = MachineLock::getAllMachineLocksForMachine($machine->getId());
    		foreach ($all_locks as $lock){
    		    if ($lock->getStart() >= time() || $lock->getStop() >= time()){
    		?>
    		<tr>
    			<td class="content_row_clear" valign="top"><?php echo date("d.m.Y H:i", $lock->getStart());?> -</td>
    			<td class="content_row_clear" valign="top"><?php echo date("d.m.Y H:i", $lock->getStop());?> 
    			<a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$machine->getId()?>&dellock=<?=$lock->getId()?>"><img src="images/icons/cross-script.png"/></a></td>
    		</tr>
    		<?php }} ?>
    		<tr>
    			<td class="content_row_clear" valign="top">&nbsp;</td>
    			<td class="content_row_clear" valign="top">&nbsp;</td>
    		</tr>
    		<tr>
    			<td class="content_row_clear" valign="top">neue Sperrzeit:</td>
    			<td class="content_row_clear" valign="top">
    			     <input type="text" style="width:160px" id="lock_start" name="lock_start"	class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency" 
    			     onfocus="markfield(this,0)" onblur="markfield(this,1)"/> -> <input type="text" style="width:160px" id="lock_stop" name="lock_stop"	class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency" 
    			     onfocus="markfield(this,0)" onblur="markfield(this,1)"/>
    			</td>
    		</tr>
    	</table>
    </div>
    </br>
    <?php 
    } 
}?>
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