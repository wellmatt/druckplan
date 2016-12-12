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
<form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" id="machine_form" class="form-horizontal"
	  name="machine_form">
	<input type="hidden" name="exec" value="edit">
	<input type="hidden" name="subexec" value="save">
	<input type="hidden" id="id" name="id" value="<?= $_REQUEST['id'] ?>">

	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">
				Sperrzeiten
			</h3>
		</div>
		<div class="panel-body">
			<div class="panel panel-default">
				  <div class="panel-heading">
						<h3 class="panel-title">
							Filter
						</h3>
				  </div>
				  <div class="panel-body">
					  <div class="form-group">
						  <label for="" class="col-sm-2 control-label">Maschine suchen:</label>
						  <div class="col-sm-4">
							  <input type="text" class="form-control" id="maschine" name="maschine" onfocus="markfield(this,0)" onblur="markfield(this,1)"/>
						  </div>
					  </div>
				  </div>
			</div>
		<?php
		if ($_REQUEST['id'] && $_REQUEST['id'] > 0) {
			$machine = new Machine((int)$_REQUEST['id']);
			if ($machine->getId() > 0) {
				?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">
							<?= $machine->getName() ?>
						</h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Neue Sperrzeit:</label>
							<div class="col-sm-2">
								<input type="text" id="lock_start" name="lock_start" class="form-control format-d-m-y divider-dot highlight-days-67 no-locale no-transparency" onfocus="markfield(this,0)" onblur="markfield(this,1)"/>
							</div>
							<label for="" class="col-sm-1 control-label">bis</label>
							<div class="col-sm-2">
								<input type="text" id="lock_stop" name="lock_stop" class="form-control format-d-m-y divider-dot highlight-days-67 no-locale no-transparency" onfocus="markfield(this,0)" onblur="markfield(this,1)"/>
							</div>
						</div>
						 </br>
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Sperrzeit Start</th>
										<th>Sperrzeit Ende</th>
										<th>Option</th>
									</tr>
								</thead>
								<?php
								$all_locks = MachineLock::getAllMachineLocksForMachine($machine->getId());
								foreach ($all_locks as $lock) {
									if ($lock->getStart() >= time() || $lock->getStop() >= time()) {
										?>
										<tr>
											<td><?php echo date("d.m.Y H:i", $lock->getStart()); ?> -
											</td>
											<td><?php echo date("d.m.Y H:i", $lock->getStop()); ?>
												<a href="index.php?page=<?= $_REQUEST['page'] ?>&exec=edit&id=<?= $machine->getId() ?>&dellock=<?= $lock->getId() ?>"><span
														class="glyphicons glyphicons-remove"></span></a></td>
											<td></td>
										</tr>
									<?php }
								} ?>
							</table>
						</div>
					</div>
				</div>
				</br>
				<?php
			}
		} ?>
			<button class="btn btn-origin btn-success" type="button" onclick="window.location.href='index.php?page=<?= $_REQUEST['page'] ?>'">
				<?= $_LANG->get('Zur&uuml;ck') ?>
			</button>
			<span class="pull-right">
					<button class="btn btn-origin btn-success" type="submit">
						<?= $_LANG->get('Speichern') ?>
					</button>
				</span>
		</div>
	</div>
</form>