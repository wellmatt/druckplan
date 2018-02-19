<?php
require_once 'libs/modules/planning/planning.job.class.php';
require_once 'libs/modules/vacation/vacation.entry.class.php';

$date_past = mktime(0,0,0); // -259200
$date_start = mktime(0,0,0,date('m',$date_past),date('d',$date_past),date('Y',$date_past));
$date_future = mktime(0,0,0)+604800;
$date_end = mktime(0,0,0,date('m',$date_future),date('d',$date_future),date('Y',$date_future));
$pl_artmachs = PlanningJob::getUniqueArtmach();



?>

<style>
	#popUpDiv{
		z-index: 100;
		background-color: rgba(123, 123,123, 0.9);
		display: none;
		border-radius: 7px;
		background:#6b6a63;
		margin:30px auto 0;
		padding:6px;
		position:absolute;
		width:300px;
		height:100px;
		top: 50%;
		left: 50%;
		margin-left: -400px;
		margin-top: -40px;
	}
	#popupSelect{
		z-index: 1000;
		position: absolute;
	}
	.group-start td{
		font-size: medium;
	}
	.group-end td{
		font-size: small;
		background-color: #f4efe0 !important;
	}
</style>

<!-- DataTables Editor -->
<link rel="stylesheet" type="text/css" href="jscripts/datatableeditor/datatables.min.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/datatables.min.js"></script>

<script type="text/javascript" src="jscripts/datatableeditor/FieldType-autoComplete/editor.autoComplete.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/datatableeditor/FieldType-bootstrapDate/editor.bootstrapDate.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/FieldType-bootstrapDate/editor.bootstrapDate.js"></script>
<script type="text/javascript" src="jscripts/datatableeditor/FieldType-datetimepicker-2/editor.datetimepicker-2.js"></script>

<link rel="stylesheet" type="text/css" href="jscripts/datatableeditor/ColReorder-1.3.2/css/colReorder.bootstrap.min.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/ColReorder-1.3.2/js/dataTables.colReorder.min.js"></script>

<link rel="stylesheet" type="text/css" href="jscripts/datatableeditor/RowGroup-1.0.2/css/rowGroup.bootstrap.min.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/RowGroup-1.0.2/js/dataTables.rowGroup.min.js"></script>

<script type="text/javascript" src="jscripts/datatableeditor/pdfmake-0.1.18/build/pdfmake.min.js"></script>
<!-- /DataTables Editor -->

<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>
<script language="JavaScript">
	$(function() {
		$('#date_start').datetimepicker({
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
			closeOnDateSelect:true,
			timepicker:false,
			format:'d.m.Y'
		});
		$('#date_end').datetimepicker({
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
			closeOnDateSelect:true,
			timepicker:false,
			format:'d.m.Y'
		});
		$('#date_move').datetimepicker({
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
			closeOnDateSelect:true,
			timepicker:true,
			format:'d.m.Y H:i'
		});
		$.ajaxSetup({
			cache:false
		});
		$( "#show" ).click(function() {
			load_content();
		});



		var popup_height = document.getElementById('popUpDiv').offsetHeight;
		var popup_width = document.getElementById('popUpDiv').offsetWidth;
		$("#popUpDiv").css('top',(($(window).height()-popup_height)/2));
		$("#popUpDiv").css('left',(($(window).width()-popup_width)/2));
		$("#popUpDiv").css('margin',0);

		load_content();
	});

	function unblock(){
		$('#planningbox').unblock();
	}

	function load_content(){
		$('#planningbox').block({ message: '<h3><img src="images/page/busy.gif"/> einen Augenblick...</h3>' });
		var stats = 0;
		if ($('#chk_statistics').is(':checked'))
			stats = 1;
		$('#planningbox').load( "libs/modules/planning/planning.table.content.php?start="+$('#date_start').val()+"&end="+$('#date_end').val()+"&artmach="+$('#artmach').val()+"&stats="+stats+"&vo="+$('#vovalue').val(), null, unblock );
	}

	function popupshow(e,jobid){
		$('#popUpDiv').hide();
		$('#popUpDiv').css({'top':e.pageY-50,'left':e.pageX, 'position':'absolute', 'border':'1px solid black', 'padding':'5px'});

		var js = "movepj("+jobid+"); return false;";
//		let newclick = new Function(js);
		// clears onclick then sets click using jQuery
		$("#popupsave").attr('onclick', js);
		$('#popUpDiv').show();
	}

	function movepj(jobid){
		console.log('Job #'+jobid+' wird verschoben nach '+$('#date_move').val());
		$('#movetext_'+jobid).text('auf '+$('#date_move').val());
		var newinput = '<input type="hidden" name="'+jobid+'" class="jobinput" value="'+$('#date_move').val()+'" id="pj_new_date_'+jobid+'">';
		$('input[name='+jobid+']').remove();
		$('#save_values').append(newinput);
		$('#popUpDiv').hide();
	}

	function move_now(){
		var jobs = {};
		$(".jobinput").each(function() {
			jobs[$(this).attr("name")] = $(this).val();
// 		alert('Debug: Job gefunden: '+$(this).attr("name")+'->'+$(this).val());
		});
		$.ajax({
				method: "POST",
				url: "libs/modules/planning/planning.ajax.php",
				data: { exec: "ajax_MoveJobs", pjtomove: jobs }
			})
			.done(function( msg ) {
				alert( "Jobs verschoben, Tabelle wird aktualisiert." );
				load_content();
			});
	}

	function print(){
		var stats = 0;
		if ($('#chk_statistics').is(':checked'))
			stats = 1;
		var url = "libs/modules/planning/planning.table.content.php?start="+$('#date_start').val()+"&end="+$('#date_end').val()+"&artmach="+$('#artmach').val()+"&stats="+stats+"&vo="+$('#vovalue').val()+"&print=1";
		window.open(url);
	}
</script>
<script language="JavaScript" >
	$(function() {
		$( "#voselector" ).autocomplete({
			source: "libs/modules/associations/association.ajax.php?ajax_action=search_colinv",
			minLength: 2,
			focus: function( event, ui ) {
				$( "#voselector" ).val( ui.item.label );
				return false;
			},
			select: function( event, ui ) {
				$( "#voselector" ).val( ui.item.label );
				$( "#vovalue" ).val( ui.item.value );
				return false;
			}
		});
	});
</script>

<div id="popUpDiv">
	<span>Datum/Zeit auswählen:</span></br>
	<input type="text" style="width:160px" id="date_move" class="text format-d-m-y divider-dot highlight-days-67 no-locale no-transparency dateselect"
		   onfocus="markfield(this,0)" onblur="markfield(this,1)" value="<? echo date('d.m.Y H:i', $date_start);?>"/>
	<div class="row">
		<div class="col-md-6" style="text-align: left;"><span onclick="$('#popUpDiv').hide();" class="btn btn-sm btn-default">Abbrechen</span></div>
		<div class="col-md-6" style="text-align: right;"><span id="popupsave" onclick="" class="btn btn-sm btn-default">Speichern</span></div>
	</div>
</div>

<div id="save_values" style="display: hidden;"></div>
<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Jetzt verschieben','#',"move_now();",'glyphicons glyphicons-random');
echo $quickmove->generate();
// end of Quickmove generation ?>


<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
			Planungstabelle
			<span class="pull-right">
				<?= $savemsg ?>
			</span>
		</h3>
	</div>
	<div class="panel-body">
		<?php
		if ($_USER->hasRightsByGroup(Permission::vacation_grant)){
			$vacations = VacationEntry::getAll();
			?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">
						Urlaube / Krankheit
					</h3>
				</div>
				<div class="table-responsive">
					<table class="table table-hover" id="vacs">
						<thead>
						<tr>
							<th><?=$_LANG->get('ID')?></th>
							<th><?=$_LANG->get('Benutzer')?></th>
							<th><?=$_LANG->get('Tage')?></th>
							<th><?=$_LANG->get('Von')?></th>
							<th><?=$_LANG->get('Bis')?></th>
							<th><?=$_LANG->get('Status')?></th>
							<th><?=$_LANG->get('Typ')?></th>
							<th><?=$_LANG->get('Kommentar')?></th>
						</tr>
						</thead>
						<tbody>
						<?php
						foreach ($vacations as $vacation) {
							if ($vacation->getState() == VacationEntry::STATE_APPROVED) {
								?>
								<tr>
									<td><?php echo $vacation->getId(); ?></td>
									<td><?php echo $vacation->getUser()->getNameAsLine(); ?></td>
									<td><?php echo $vacation->getDays(); ?></td>
									<td><?php echo date('d.m.Y', $vacation->getStart()); ?></td>
									<td><?php echo date('d.m.Y', $vacation->getEnd()); ?></td>
									<td><?php echo $vacation->getStateFormated(); ?></td>
									<td><?php echo $vacation->getTypeFormated(); ?></td>
									<td><?php echo $vacation->getComment(); ?></td>
								</tr>
								<?php
							}
						}
						?>
						</tbody>
					</table>
				</div>
			</div>
		<?php } ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">
					Filter
				</h3>
			</div>
			<div class="panel-body">
				<form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" class="form-horizontal"
					  name="planning_table" id="planning_table" enctype="multipart/form-data">
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Datum Von</label>
						<div class="col-sm-4">
							<input type="text" id="date_start" name="date_start"
								   class="form-control format-d-m-y divider-dot highlight-days-67 no-locale no-transparency dateselect"
								   onfocus="markfield(this,0)" onblur="markfield(this,1)"
								   value="<? echo date('d.m.Y', $date_start); ?>"/>
						</div>
						<label for="" class="col-sm-2 control-label" style="text-align: center">Bis</label>
						<div class="col-sm-4">
							<input type="text" id="date_end" name="date_end"
								   class="form-control format-d-m-y divider-dot highlight-days-67 no-locale no-transparency dateselect"
								   onfocus="markfield(this,0)" onblur="markfield(this,1)"
								   value="<? echo date('d.m.Y', $date_end); ?>"/>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Object</label>
						<div class="col-sm-10">
							<select name="artmach" id="artmach" class="form-control">
								<option value="0" selected>alle</option>
								<option value="" disabled>Maschinen</option>
								<? foreach ($pl_artmachs['machines'] as $mach) { ?>
									<option value="K<?= $mach->getId() ?>"><?= $mach->getName() ?></option>
								<? } ?>
								<option value="" disabled>Artikel</option>
								<? foreach ($pl_artmachs['articles'] as $art) { ?>
									<option value="V<?= $art->getId() ?>"><?= $art->getTitle() ?></option>
								<? } ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Vorgang</label>
						<div class="col-sm-10">
							<input type="text" id="voselector" class="form-control"><input type="hidden" id="vovalue">
						</div>
					</div>
					<span class="pull-right">
						<button type="button" class="btn btn-sm btn-success" id="show">anzeigen</button>
					</span>
				</form>
			</div>
		</div>
		<div id="planningbox">
		</div>
	</div>
</div>