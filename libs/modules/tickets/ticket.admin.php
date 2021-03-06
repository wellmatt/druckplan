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
if ($_REQUEST["delete_source"]){
    $del_source = new TicketSource((int)$_REQUEST["delete_source"]);
    $del_source->delete();
}

if ($_REQUEST["source_default"]){
    $def_source = new TicketSource((int)$_REQUEST["source_default"]);
    $def_source->setDefault();
}

if ($_REQUEST["exec"] == "save")
{
    $all_categories = (int)$_REQUEST["count_categories"];
    for ($i=0 ; $i <= $all_categories ; $i++){
        if ($_REQUEST["categories_title_".$i] != "" && $_REQUEST["categories_title_".$i]){
            $cid = (int)$_REQUEST["categories_id_".$i];
            $ctitle = $_REQUEST["categories_title_".$i];
            $csort = $_REQUEST["categories_sort_".$i];
            $cat = new TicketCategory($cid);
            $cat->setTitle($ctitle);
            $cat->setSort((int)$csort);

            $tmp_groups_cansee = Array();
            $tmp_groups_cancreate = Array();
            
            $group_rights = $_REQUEST["categories_rights_".$cid];
            if ($group_rights["cansee"]){
                foreach ($group_rights["cansee"] as $gcansee){
                    $tmp_group = new Group((int)$gcansee);
                    $tmp_groups_cansee[] = $tmp_group;
                }
            }
            if ($group_rights["cancreate"]){
                foreach ($group_rights["cancreate"] as $gcancreate){
                    $tmp_group = new Group((int)$gcancreate);
                    $tmp_groups_cancreate[] = $tmp_group;
                }
            }
            
            $cat->setGroups_cansee($tmp_groups_cansee);
            $cat->setGroups_cancreate($tmp_groups_cancreate);
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
    $all_sources = (int)$_REQUEST["count_sources"];
    for ($i=0 ; $i <= $all_sources ; $i++){
        if ($_REQUEST["sources_title_".$i] != "" && $_REQUEST["sources_title_".$i]){
            $id = (int)$_REQUEST["sources_id_".$i];
            $title = $_REQUEST["sources_title_".$i];
            $source = new TicketSource($id);
            $source->setTitle($title);
            $source->save();
        }
    }
    
	if ((int)$_REQUEST["ticket_artdesc"] == 0)
		$perf->setCommentArtDesc(0);
	else
		$perf->setCommentArtDesc(1);
	$perf->save();
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

    var insert = '<tr><td>'+count+'</td>';
	insert += '<td></td>';
	insert += '<td>';
	insert += '<input type="hidden" name="categories_id_'+count+'" id="categories_id_'+count+'" value="0">';
	insert += '<input name="categories_title_'+count+'" class="form-control" type="text"';
	insert += 'value ="">';
	insert += '</td></tr>';

	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('count_categories').value = count;
}
function addStateRow()
{
    var obj = document.getElementById('table-states');
    var count = parseInt(document.getElementById('count_states').value) + 1;

    var insert = '<tr><td>'+count+'</td>';
	insert += '<td>';
	insert += '<input name="states_title_'+count+'" class="form-control" type="text"';
	insert += 'value ="">';
	insert += '</td>';
	insert += '<td>';
	insert += '<input type="hidden" name="states_id_'+count+'" id="states_id_'+count+'" value="0">';
	insert += '<input name="states_color_'+count+'" class="form-control" type="text"';
	insert += 'value ="">';
	insert += '</td></tr>';

	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('count_states').value = count;
}
function addPrioRow()
{
    var obj = document.getElementById('table-prios');
    var count = parseInt(document.getElementById('count_prios').value) + 1;

    var insert = '<tr><td>'+count+'</td>';
	insert += '<td>';
	insert += '<input type="hidden" name="prios_id_'+count+'" id="prios_id_'+count+'" value="0">';
	insert += '<input name="prios_title_'+count+'" class="form-control" type="text"';
	insert += 'value ="">';
	insert += '</td>';
	insert += '<td>';
	insert += '<input name="prios_value_'+count+'" class="form-control" type="text"';
	insert += 'value ="">';
	insert += '</td></tr>';

	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('count_prios').value = count;
}
function addSourceRow()
{
    var obj = document.getElementById('table-sources');
    var count = parseInt(document.getElementById('count_sources').value) + 1;

    var insert = '<tr><td>'+count+'</td>';
	insert += '<td>';
	insert += '<input type="hidden" name="sources_id_'+count+'" id="sources_id_'+count+'" value="0">';
	insert += '<input name="sources_title_'+count+'" class="form-control" type="text"';
	insert += 'value ="">';
	insert += '</td>';
	insert += '<td>';
	insert += '<input name="sources_default_'+count+'" type="checkbox" value ="1">';
	insert += '</td></tr>';

	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('count_sources').value = count;
}
</script>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#ticketperf_form').submit();",'glyphicon-floppy-disk');

echo $quickmove->generate();
// end of Quickmove generation ?>
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">
				Ticket-Einstellungen
				<span class="pull-right">
				<?=$savemsg?>
				</span>
			</h3>
	  </div>
	  <div class="panel-body">

		  <form action="index.php?page=<?=$_REQUEST['page']?>" method="post" class="form-horizontal" enctype="multipart/form-data" name="ticketperf_form" id="ticketperf_form">
			  <input type="hidden" name="exec" value="save">
			  <div id="tabs">
				  <ul>
					  <li><a href="#tabs-3"><? echo $_LANG->get('Einstellungen');?></a></li>
					  <li><a href="#tabs-0"><? echo $_LANG->get('Kategorien');?></a></li>
					  <li><a href="#tabs-1"><? echo $_LANG->get('Priorit&auml;ten');?></a></li>
					  <li><a href="#tabs-2"><? echo $_LANG->get('Stati');?></a></li>
					  <li><a href="#tabs-4"><? echo $_LANG->get('Gruppenrechte');?></a></li>
					  <li><a href="#tabs-5"><? echo $_LANG->get('Herkunft');?></a></li>
				  </ul>
				  <div id="tabs-0">
					  <?php
					  $ticket_categories = TicketCategory::getAllCategories();
					  ?>
					  <input 	type="hidden" name="count_categories" id="count_categories"
								value="<? if(count($ticket_categories) > 0) echo count($ticket_categories); else echo "1";?>">
					  <div class="table-responsive">
					  	<table id="table-categories" class="table table-hover">
					  		<thead>
					  			<tr>
									<th width="10%"><?=$_LANG->get('Reihenfolge')?></th>
									<th width="10%"><?=$_LANG->get('Nr')?></th>
									<th width="20%"><?=$_LANG->get('Titel')?></th>
									<th></th>
					  			</tr>
					  		</thead>
							<?
							$x = count($ticket_categories);
							if ($x < 1){
								$x++;
							}
							for ($y=0; $y < $x ; $y++){ ?>
								<tbody>
									<tr>
										<td>
											<input 	name="categories_sort_<?=$y?>" class="form-control" type="text" value ="<?=$ticket_categories[$y]->getSort();?>">
										</td>
										<td>
											<?=$ticket_categories[$y]->getId()?>
										</td>
										<td>
											<input type="hidden" name="categories_id_<?=$y?>" id="categories_id_<?=$y?>" value="<?php echo $ticket_categories[$y]->getId();?>">
											<input 	name="categories_title_<?=$y?>" class="form-control" type="text" value ="<?=$ticket_categories[$y]->getTitle();?>">
										</td>
										<td>
											<?php if ($ticket_categories[$y]->getProtected() == 0){?>
												<a href="index.php?page=<?=$_REQUEST['page']?>&delete_category=<?=$ticket_categories[$y]->getId()?>">
													<span class="glyphicons glyphicons-remove pointer" style="color: red;"></span></a>&nbsp;
											<?php }?>
											<? if ($y == $x-1){ //Plus-Knopf nur beim letzten anzeigen
												echo '<button class="btn btn-xs btn-success"  onclick="addCategoryRow()" type="button">
												<span class="glyphicons glyphicons-plus pointer"></span>
												Kategorie hinzufügen
												</button>';
											}?>
										</td>
									</tr>
								</tbody>
							<?php
							$ticket_prios = TicketPriority::getAllPriorities();
							?>
							<? } ?>
					  	</table>
					  </div>
				  </div>
				  <div id="tabs-1">
					  <input 	type="hidden" name="count_prios" id="count_prios"
								value="<? if(count($ticket_prios) > 0) echo count($ticket_prios); else echo "1";?>">
					  <div class="table-responsive">
					  	<table id="table-prios" class="table table-hover">
					  		<thead>
					  			<tr>
					  				<th><?=$_LANG->get('Nr')?></th>
									<th><?=$_LANG->get('Titel')?></th>
									<th></th>
					  			</tr>
					  		</thead>
							<?
							$x = count($ticket_prios);
							if ($x < 1){
								$x++;
							}
							for ($y=0; $y < $x ; $y++){ ?>
					  		<tbody>
							<tr>
								<td  width="10%">
									<?=$ticket_prios[$y]->getId()?>
								</td>
								<td width="20%">
									<input type="hidden" name="prios_id_<?=$y?>" id="prios_id_<?=$y?>" value="<?php echo $ticket_prios[$y]->getId();?>">
									<input 	name="prios_title_<?=$y?>" class="form-control" type="text" value ="<?=$ticket_prios[$y]->getTitle();?>">
								</td>
								<td  width="20%">
									<input 	name="prios_value_<?=$y?>" class="form-control" type="text" value ="<?=$ticket_prios[$y]->getValue();?>" >
								</td>
								<td>
									<?php if ($ticket_prios[$y]->getProtected() == 0){?>
										<a href="index.php?page=<?=$_REQUEST['page']?>&delete_prio=<?=$ticket_prios[$y]->getId()?>">
											<span class="glyphicons glyphicons-remove pointer" style="color: red;"></span></a>&nbsp;
									<?php }?>
									<? if ($y == $x-1){ //Plus-Knopf nur beim letzten anzeigen
										echo '<button class="btn btn-xs btn-success" onclick="addPrioRow()" type="button">
												<span class="glyphicons glyphicons-plus pointer"></span>
												Priorität hinzufügen
											  </button>';
									}?>
								</td>
							</tr>
					  		</tbody>
							<? } ?>
					  	</table>
					  </div>
				  </div>
				  <div id="tabs-2">
					  <?php
					  $ticket_states = TicketState::getAllStates();
					  ?>
					  <input 	type="hidden" name="count_states" id="count_states"
								value="<? if(count($ticket_states) > 0) echo count($ticket_states); else echo "1";?>">
					  <div class="table-responsive">
					  	<table id="table-states" class="table table-hover">
					  		<thead>
					  			<tr>
					  				<th><?=$_LANG->get('Nr')?></th>
									<th><?=$_LANG->get('Titel')?></th>
									<th><?=$_LANG->get('Farbcode')?></th>
									<th></th>
					  			</tr>
					  		</thead>
							<?
							$x = count($ticket_states);
							if ($x < 1){
								$x++;
							}
							for ($y=0; $y < $x ; $y++){ ?>
					  		<tbody>
							<tr>
								<td width="10%">
									<?=$ticket_states[$y]->getId()?>
								</td>
								<td width="20%">
									<input 	name="states_title_<?=$y?>" class="form-control" type="text" value ="<?=$ticket_states[$y]->getTitle();?>">
								</td>
								<td width="20%">
									<input type="hidden" name="states_id_<?=$y?>" id="states_id_<?=$y?>" value="<?php echo $ticket_states[$y]->getId();?>">
									<input 	name="states_color_<?=$y?>" class="form-control" type="text" value ="<?=$ticket_states[$y]->getColorcode();?>">

								</td>
								<td>
									<?php if ($ticket_states[$y]->getProtected() == 0){?>
										<a href="index.php?page=<?=$_REQUEST['page']?>&delete_state=<?=$ticket_states[$y]->getId()?>">
											<span class="glyphicons glyphicons-remove pointer" style="color: red;"></span></a>&nbsp;
									<?php }?>
									<? if ($y == $x-1){ //Plus-Knopf nur beim letzten anzeigen
										echo '<button class="btn btn-xs btn-success"  onclick="addStateRow()" type="button">
												<span class="glyphicons glyphicons-plus pointer"></span>
												Stati hinzufügen
												</button>';
									}?>
								</td>
							</tr>
					  		</tbody>
							<? } ?>
					  	</table>
					  </div>
				  </div>
				  <div id="tabs-3">
					  <table width="100%" border="0" cellpadding="0" cellspacing="0">
						  <colgroup>
							  <col width="150">
							  <col>
						  </colgroup>
						  <tr>
							  <td class="content_row_header" valign="top">Artikelbeschreibung übernehmen</td>
							  <td class="content_row_clear">
								  <input type="checkbox" name="ticket_artdesc" id="ticket_artdesc" <?php if($perf->getCommentArtDesc()>0) echo ' checked ';?> value="1"/>
							  </td>
						  </tr>
					  </table>
				  </div>

				  <div id="tabs-4">
					  <table>
						  <?php foreach ($ticket_categories as $tc){?>
							  <tr>
								  <td><h4><?php echo $tc->getTitle()?></h4></td>
							  </tr>
							  <tr>
								  <td>
									  <table>
										  <thead>
										  <tr>
											  <th>Gruppe&nbsp;&nbsp;</th>
											  <th>Einsehen&nbsp;&nbsp;</th>
											  <th>Erstellen</th>
										  </tr>
										  </thead>
										  <?php foreach (Group::getAllGroups() as $group){?>
											  <tr>
												  <td><?php echo $group->getName();?></td>
												  <td><input type="checkbox" name="categories_rights_<?=$tc->getId()?>[cansee][]" id="categories_rights_<?=$tc->getId()?>[cansee][]"
															 value="<?php echo $group->getId();?>" style="width: 40px" <?php if (in_array($group, $tc->getGroups_cansee())) echo " checked ";?>/></td>
												  <td><input type="checkbox" name="categories_rights_<?=$tc->getId()?>[cancreate][]" id="categories_rights_<?=$tc->getId()?>[cancreate][]"
															 value="<?php echo $group->getId();?>" style="width: 40px" <?php if (in_array($group, $tc->getGroups_cancreate())) echo " checked ";?>/></td>
											  </tr>
										  <?php }?>
									  </table>
								  </td>
							  </tr>
						  <?php }?>
					  </table>
				  </div>
				  <div id="tabs-5">
					  <?php
					  $ticket_sources = TicketSource::getAllSources();
					  ?>
					  <input 	type="hidden" name="count_sources" id="count_sources"
								value="<? if(count($ticket_sources) > 0) echo count($ticket_sources); else echo "0";?>">

					  <div class="table-responsive">
					  	<table  id="table-sources" class="table table-hover">
					  		<thead>
					  			<tr>
					  				<th><?=$_LANG->get('Nr')?></th>
									<th><?=$_LANG->get('Titel')?></th>
									<th></th>
									<th></th>
					  			</tr>
					  		</thead>
							<?
							$x = count($ticket_sources);
							for ($y=0; $y < $x ; $y++){ ?>
					  		<tbody>
							<tr>
								<td  width="10%">
									<?=$ticket_sources[$y]->getId()?>
								</td>
								<td width="20%">
									<input type="hidden" name="sources_id_<?=$y?>" id="sources_id_<?=$y?>" value="<?php echo $ticket_sources[$y]->getId();?>">
									<input 	name="sources_title_<?=$y?>" class="form-control" type="text" value ="<?=$ticket_sources[$y]->getTitle();?>">
								</td>
								<td  width="10%">
									<?php if ($ticket_sources[$y]->getDefault() == 1) echo '<span class="glyphicons glyphicons-star"></span>';?>
								</td>
								<td width="10%">
									<a href="index.php?page=<?=$_REQUEST['page']?>&source_default=<?=$ticket_sources[$y]->getId()?>">
										<span class="glyphicons glyphicons-star pointer" title="als Standard setzen"></span></a>
								</td>
								<td>
									<?php if ($ticket_sources[$y]->getId() != 1 && $ticket_sources[$y]->getId() != 2 && $ticket_sources[$y]->getId() != 3 && $ticket_sources[$y]->getId() != 4){?>
										<a href="index.php?page=<?=$_REQUEST['page']?>&delete_source=<?=$ticket_sources[$y]->getId()?>">
											<span class="glyphicons glyphicons-remove icon-link pointer" style="color: red;"></span></a>&nbsp;
									<?php } ?>
									<? if ($y == $x-1){ //Plus-Knopf nur beim letzten anzeigen
										echo '<button class="btn btn-xs btn-success" onclick="addSourceRow()" type=button>
													<span class="glyphicons glyphicons-plus pointer" ></span>
													Herkunft hinzufügen
											  </button>';
									}?>
								</td>

							</tr>
					  		</tbody>
							<? } ?>
					  	</table>
					  </div>
				  </div>
			  </div>
		  </form>
	  </div>
</div>


