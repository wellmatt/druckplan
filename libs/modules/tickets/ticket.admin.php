<?php

require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/perferences/perferences.class.php';

$perf = new Perferences();

if ($_REQUEST["delete_category"]){
    $del_cat = new TicketCategory((int)$_REQUEST["delete_category"]);
    $del_cat->delete();
}
if ($_REQUEST["delete_prio"]){
    $del_pri = new TicketPriority((int)$_REQUEST["delete_prio"]);
    $del_pri->delete();
}
if ($_REQUEST["delete_state"]){
    $del_sta = new TicketState((int)$_REQUEST["delete_state"]);
    $del_sta->delete();
}

if ($_REQUEST["exec"] == "save")
{
    $all_categories = (int)$_REQUEST["count_categories"];
    for ($i=0 ; $i <= $all_categories ; $i++){
        if ($_REQUEST["categories_title_".$i] != "" && $_REQUEST["categories_title_".$i]){
            $cid = (int)$_REQUEST["categories_id_".$i];
            $ctitle = $_REQUEST["categories_title_".$i];
            $cat = new TicketCategory($cid);
            $cat->setTitle($ctitle);
            $cat->save();
        }
    }
    $all_states = (int)$_REQUEST["count_states"];
    for ($i=0 ; $i <= $all_states ; $i++){
        if ($_REQUEST["states_title_".$i] != "" && $_REQUEST["states_title_".$i]){
            $sid = (int)$_REQUEST["states_id_".$i];
            $stitle = $_REQUEST["states_title_".$i];
            $scolor = $_REQUEST["states_color_".$i];
            $state = new TicketState($sid);
            $state->setTitle($stitle);
            $state->setColorcode($scolor);
            $state->save();
        }
    }
    $all_prios = (int)$_REQUEST["count_prios"];
    for ($i=0 ; $i <= $all_prios ; $i++){
        if ($_REQUEST["prios_title_".$i] != "" && $_REQUEST["prios_title_".$i] && $_REQUEST["prios_value_".$i] != "" && $_REQUEST["prios_value_".$i]){
            $pid = (int)$_REQUEST["prios_id_".$i];
            $ptitle = $_REQUEST["prios_title_".$i];
            $pvalue = $_REQUEST["prios_value_".$i];
            $prio = new TicketPriority($pid);
            $prio->setTitle($ptitle);
            $prio->setValue($pvalue);
            $prio->save();
        }
    }
    
    if ($_REQUEST["ticket_id"] > 0){
        $perf->setDefault_ticket_id((int)$_REQUEST["ticket_id"]);
        $perf->save();
    }
}

?>

<script>
$(function() {
     $( "#tabs" ).tabs({ selected: 0 });

	 $( "#ticket" ).autocomplete({
		 source: "libs/modules/associations/association.ajax.php?ajax_action=search_ticket",
		 minLength: 2,
		 focus: function( event, ui ) {
  		 $( "#ticket" ).val( ui.item.label );
  		 return false;
		 },
		 select: function( event, ui ) {
  		 $( "#ticket" ).val( ui.item.label );
  		 $( "#ticket_id" ).val( ui.item.value );
  		 return false;
		 }
	 });
});
</script>
<script type="text/javascript">
function addCategoryRow()
{
    var obj = document.getElementById('table-categories');
    var count = parseInt(document.getElementById('count_categories').value) + 1;

    var insert = '<tr><td class="content_row_clear">'+count+'</td>';
	insert += '<td class="content_row_clear">';
	insert += '<input type="hidden" name="categories_id_'+count+'" id="categories_id_'+count+'" value="0">';
	insert += '<input name="categories_title_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 200px">';
	insert += '</td></tr>';

	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('count_categories').value = count;
}
function addStateRow()
{
    var obj = document.getElementById('table-states');
    var count = parseInt(document.getElementById('count_states').value) + 1;

    var insert = '<tr><td class="content_row_clear">'+count+'</td>';
	insert += '<td class="content_row_clear">';
	insert += '<input name="states_title_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 200px">';
	insert += '</td>';
	insert += '<td class="content_row_clear">';
	insert += '<input type="hidden" name="states_id_'+count+'" id="states_id_'+count+'" value="0">';
	insert += '<input name="states_color_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 200px">';
	insert += '</td></tr>';

	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('count_states').value = count;
}
function addPrioRow()
{
    var obj = document.getElementById('table-prios');
    var count = parseInt(document.getElementById('count_prios').value) + 1;

    var insert = '<tr><td class="content_row_clear">'+count+'</td>';
	insert += '<td class="content_row_clear">';
	insert += '<input type="hidden" name="prios_id_'+count+'" id="prios_id_'+count+'" value="0">';
	insert += '<input name="prios_title_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 200px">';
	insert += '</td>';
	insert += '<td class="content_row_clear">';
	insert += '<input name="prios_value_'+count+'" class="text" type="text"';
	insert += 'value ="" style="width: 200px">';
	insert += '</td></tr>';

	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('count_prios').value = count;
}
</script>

<table width="100%">
    <tr>
        <td width="200" class="content_header"><img src="images/icons/gear.png"> <?=$_LANG->get('Ticket-Einstellungen')?></td>
        <td align="right"><?=$savemsg?></td>
    </tr>
</table>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" enctype="multipart/form-data" name="ticketperf_form" id="ticketperf_form">
<input type="hidden" name="exec" value="save">
<div class="box1">
	<div id="tabs">
		<ul>
			<li><a href="#tabs-0"><? echo $_LANG->get('Kategorien');?></a></li>
			<li><a href="#tabs-1"><? echo $_LANG->get('Priorit&auml;ten');?></a></li>
			<li><a href="#tabs-2"><? echo $_LANG->get('Stati');?></a></li>
			<?php /*<li><a href="#tabs-3"><? echo $_LANG->get('Einstellungen');?></a></li>*/?>
		</ul>
       <div id="tabs-0">
            <?php 
            $ticket_categories = TicketCategory::getAllCategories();
            ?>
   			<input 	type="hidden" name="count_categories" id="count_categories" 
				value="<? if(count($ticket_categories) > 0) echo count($ticket_categories); else echo "1";?>">
            <table border="0" cellpadding="0" cellspacing="0" id="table-categories">
				<colgroup>
		        	<col width="40">
		        	<col width="300">
		    	</colgroup>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Nr')?></td>
					<td class="content_row_header"><?=$_LANG->get('Titel')?></td>
				</tr>
				<?
				$x = count($ticket_categories);
				if ($x < 1){
					$x++;
				}
				for ($y=0; $y < $x ; $y++){ ?>
					<tr>
						<td class="content_row_clear">
						<?=$ticket_categories[$y]->getId()?>
						</td>
						<td class="content_row_clear">
						    <input type="hidden" name="categories_id_<?=$y?>" id="categories_id_<?=$y?>" value="<?php echo $ticket_categories[$y]->getId();?>">
							<input 	name="categories_title_<?=$y?>" class="text" type="text"
									value ="<?=$ticket_categories[$y]->getTitle();?>" style="width: 200px">
							&nbsp;&nbsp;&nbsp;
							<?php if ($ticket_categories[$y]->getProtected() == 0){?>
							     <a href="index.php?page=<?=$_REQUEST['page']?>&delete_category=<?=$ticket_categories[$y]->getId()?>">
							     <img src="images/icons/cross-script.png" class="pointer icon-link"></a>&nbsp;
							<?php }?>
							<? if ($y == $x-1){ //Plus-Knopf nur beim letzten anzeigen
								echo '<img src="images/icons/plus.png" class="pointer icon-link" onclick="addCategoryRow()">';
							}?> 
						</td>
					</tr>
				<? } ?>
			</table>
       </div>
       <div id="tabs-1">
            <?php 
            $ticket_prios = TicketPriority::getAllPriorities();
            ?>
   			<input 	type="hidden" name="count_prios" id="count_prios" 
				value="<? if(count($ticket_prios) > 0) echo count($ticket_prios); else echo "1";?>">
            <table border="0" cellpadding="0" cellspacing="0" id="table-prios">
				<colgroup>
		        	<col width="40">
		        	<col width="300">
		    	</colgroup>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Nr')?></td>
					<td class="content_row_header"><?=$_LANG->get('Titel')?></td>
				</tr>
				<?
				$x = count($ticket_prios);
				if ($x < 1){
					$x++;
				}
				for ($y=0; $y < $x ; $y++){ ?>
					<tr>
						<td class="content_row_clear">
						<?=$ticket_prios[$y]->getId()?>
						</td>
						<td class="content_row_clear">
						    <input type="hidden" name="prios_id_<?=$y?>" id="prios_id_<?=$y?>" value="<?php echo $ticket_prios[$y]->getId();?>">
							<input 	name="prios_title_<?=$y?>" class="text" type="text"
									value ="<?=$ticket_prios[$y]->getTitle();?>" style="width: 200px">
						</td>
						<td class="content_row_clear">
							<input 	name="prios_value_<?=$y?>" class="text" type="text"
									value ="<?=$ticket_prios[$y]->getValue();?>" style="width: 200px">
							&nbsp;&nbsp;&nbsp;
							<?php if ($ticket_prios[$y]->getProtected() == 0){?>
							     <a href="index.php?page=<?=$_REQUEST['page']?>&delete_prio=<?=$ticket_prios[$y]->getId()?>">
							     <img src="images/icons/cross-script.png" class="pointer icon-link"></a>&nbsp;
							<?php }?>
							<? if ($y == $x-1){ //Plus-Knopf nur beim letzten anzeigen
								echo '<img src="images/icons/plus.png" class="pointer icon-link" onclick="addPrioRow()">';
							}?> 
						</td>
					</tr>
				<? } ?>
			</table>
       </div>
       <div id="tabs-2">
            <?php 
            $ticket_states = TicketState::getAllStates();
            ?>
   			<input 	type="hidden" name="count_states" id="count_states" 
				value="<? if(count($ticket_states) > 0) echo count($ticket_states); else echo "1";?>">
            <table border="0" cellpadding="0" cellspacing="0" id="table-states">
				<colgroup>
		        	<col width="40">
		        	<col width="200">
		        	<col width="280">
		    	</colgroup>
				<tr>
					<td class="content_row_header"><?=$_LANG->get('Nr')?></td>
					<td class="content_row_header"><?=$_LANG->get('Titel')?></td>
					<td class="content_row_header"><?=$_LANG->get('Farbcode')?></td>
				</tr>
				<?
				$x = count($ticket_states);
				if ($x < 1){
					$x++;
				}
				for ($y=0; $y < $x ; $y++){ ?>
					<tr>
						<td class="content_row_clear">
						<?=$ticket_states[$y]->getId()?>
						</td>
						<td class="content_row_clear">
						    <input 	name="states_title_<?=$y?>" class="text" type="text"
									value ="<?=$ticket_states[$y]->getTitle();?>" style="width: 200px">
						</td>
						<td class="content_row_clear">
						    <input type="hidden" name="states_id_<?=$y?>" id="states_id_<?=$y?>" value="<?php echo $ticket_states[$y]->getId();?>">
							<input 	name="states_color_<?=$y?>" class="text" type="text"
									value ="<?=$ticket_states[$y]->getColorcode();?>" style="width: 200px">
							&nbsp;&nbsp;&nbsp;
							<?php if ($ticket_states[$y]->getProtected() == 0){?>
							     <a href="index.php?page=<?=$_REQUEST['page']?>&delete_state=<?=$ticket_states[$y]->getId()?>">
							     <img src="images/icons/cross-script.png" class="pointer icon-link"></a>&nbsp;
							<?php }?>
							<? if ($y == $x-1){ //Plus-Knopf nur beim letzten anzeigen
								echo '<img src="images/icons/plus.png" class="pointer icon-link" onclick="addStateRow()">';
							}?> 
						</td>
					</tr>
				<? } ?>
			</table>
       </div>
       <?php /*
       <div id="tabs-3">
       <?php 
       if ($perf->getDefault_ticket_id() > 0){
           $tmp_def_ticket = new Ticket($perf->getDefault_ticket_id());
           $tmp_ticket_label = $tmp_def_ticket->getNumber() . " - " . $tmp_def_ticket->getTitle();
           $tmp_ticket_id = $tmp_def_ticket->getId();
       }
       ?>
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
               <colgroup>
                  <col width="150">
                  <col>
               </colgroup>
               <tr>
                  <td class="content_row_header" valign="top">Login Timer Ticket:</td>
                  <td class="content_row_clear">
                     <input type="text" name="ticket" id="ticket" value="<?php echo $tmp_ticket_label;?>" style="width: 250px"/>
                     <input type="hidden" name="ticket_id" id="ticket_id" value="<?php echo $tmp_ticket_id;?>"/>
                  </td>
               </tr>
            </table>
       </div>
       */?>

    <table border="0" class="content_table" cellpadding="3" cellspacing="0" width="100%">
    <tr>
       <td class="content_row_clear">
       		<input 	type="button" value="<?=$_LANG->get('Zur&uuml;ck')?>" class="button"
    	    		onclick="window.location.href='index.php?page=<?=$_REQUEST['page']?>'">
       </td>
       <td align="right" width="150">
          	<input type="submit" value="<?=$_LANG->get('Speichern')?>">
       </td>
    </tr>
    </table>
</div>
</form>