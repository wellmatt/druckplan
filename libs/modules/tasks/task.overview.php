<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			27.09.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/tasks/task.class.php';

if($_REQUEST["exec"] == "new" || $_REQUEST["exec"] == "edit"){
	// Mahnstufe bearbeiten
	require_once 'libs/modules/tasks/task.edit.php';
} else {    
    
	// Uebersicht ausgeben
	
	if ($_REQUEST["exec"] == "delete") {
		$del_task = new Task($_REQUEST["delid"]);
		$del_task->delete();
	}
	
	$all_tasks = Task::getAllTasks();
	?>
	
    <!-- DataTables -->
    <link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
    <script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
    <script type="text/javascript" charset="utf8" src="jscripts/datatable/numeric-comma.js"></script>
    <script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">
    <script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.tableTools.js"></script>
    <script type="text/javascript" charset="utf8" src="jscripts/datatable/date-uk.js"></script>
    
    <script type="text/javascript">
    
    jQuery.fn.dataTableExt.oSort['uk_date-asc']  = function(a,b) {
        var ukDatea = a.split('.');
        var ukDateb = b.split('.');
         
        var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
        var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;
         
        return ((x < y) ? -1 : ((x > y) ?  1 : 0));
    };
     
    jQuery.fn.dataTableExt.oSort['uk_date-desc'] = function(a,b) {
        var ukDatea = a.split('.');
        var ukDateb = b.split('.');
         
        var x = (ukDatea[2] + ukDatea[1] + ukDatea[0]) * 1;
        var y = (ukDateb[2] + ukDateb[1] + ukDateb[0]) * 1;
         
        return ((x < y) ? 1 : ((x > y) ?  -1 : 0));
    };
    
    $(document).ready(function() {
        var tasks = $('#tasks').DataTable( {
            "paging": true,
    		"stateSave": <?php if($perf->getDt_state_save()) {echo "true";}else{echo "false";};?>,
    		"pageLength": <?php echo $perf->getDt_show_default();?>,
    		"dom": 'flrtip',
    		"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Alle"] ],
    		"aoColumnDefs": [ { "sType": "uk_date", "aTargets": [ 4, 5 ] } ],
    		"language": 
    					{
    						"emptyTable":     "Keine Daten vorhanden",
    						"info":           "Zeige _START_ bis _END_ von _TOTAL_ Eintr&auml;gen",
    						"infoEmpty": 	  "Keine Seiten vorhanden",
    						"infoFiltered":   "(gefiltert von _MAX_ gesamten Eintr&auml;gen)",
    						"infoPostFix":    "",
    						"thousands":      ".",
    						"lengthMenu":     "Zeige _MENU_ Eintr&auml;ge",
    						"loadingRecords": "Lade...",
    						"processing":     "Verarbeite...",
    						"search":         "Suche:",
    						"zeroRecords":    "Keine passenden Eintr&auml;ge gefunden",
    						"paginate": {
    							"first":      "Erste",
    							"last":       "Letzte",
    							"next":       "N&auml;chste",
    							"previous":   "Vorherige"
    						},
    						"aria": {
    							"sortAscending":  ": aktivieren um aufsteigend zu sortieren",
    							"sortDescending": ": aktivieren um absteigend zu sortieren"
    						}
    					}
        } );
    } );
</script>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
		Aufgaben
			<span class = pull-right>
				<button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=<?=$_REQUEST['page']?>&exec=new';">
					<span class="glyphicons glyphicons-plus-sign"></span>
					 <?=$_LANG->get('Aufgabe erstellen') ?>
			</span>
		</h3>
	</div>
	<div class="table-responsive">
		<table class="table table-hover">
            <thead>
                <tr>
    				<td width="40" class="content_row_header"><?=$_LANG->get('ID')?></td>
    				<td width="100" class="content_row_header"><?=$_LANG->get('Titel')?></td>
    				<td class="content_row_header"><?=$_LANG->get('Text')?></td>
                    <td width="80" class="content_row_header"><?=$_LANG->get('Priorität')?></td>
                    <td width="80" class="content_row_header"><?=$_LANG->get('Erstellt am')?></td>
                    <td width="80" class="content_row_header"><?=$_LANG->get('Fällig am')?></td>
                    <td width="45" class="content_row_header"><?=$_LANG->get('Erstellt von')?></td>
    				<td width="45" class="content_row_header"><?=$_LANG->get('Optionen')?></td>
    
    			</tr>
    		</thead>
			<? $x = 0;
			foreach($all_tasks AS $task){?>
				<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
					<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&bid=<?=$task->getId()?>'">
						<?=$task->getId()?>&ensp;
					</td>
					<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&bid=<?=$task->getId()?>'">
						<?=$task->getTitle()?>
					</td>
					<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&bid=<?=$task->getId()?>'">
						<?=substr($task->getContent(), 0, 250)?> <?if(strlen($task->getContent()) > 250 ) echo "...";?>
					</td>
                    <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&bid=<?=$task->getId()?>'">
                        <?=$task->getPrio()?>
                    </td>
					<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&bid=<?=$task->getId()?>'">
						<?=date('d.m.Y',$task->getCrt_date())?>
					</td>
					<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&bid=<?=$task->getId()?>'">
						<?=date('d.m.Y',$task->getDue_date())?>
					</td>
                    <td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&bid=<?=$task->getId()?>'">
                        <?=$task->getCrt_usr()->getNameAsLine()?>
                    </td>
					<td class="content_row">
		                <a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&bid=<?=$task->getId()?>"><span class="glyphicons glyphicons-pencil" title="<?=$_LANG->get('Bearbeiten')?>"></span></a>
						&ensp;
		                <a href="#"	onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&delid=<?=$task->getId()?>')"
		                	><span class="glyphicons glyphicons-remove" title="<?=$_LANG->get('Entfernen')?>"></span></a>
	            	</td>
				</tr>
				<? $x++;
			}// Ende foreach($all_tasks)
			?>
		</table>
	</div>
</div>
<?} ?>