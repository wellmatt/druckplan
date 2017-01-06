<? // ---------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       23.08.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------
require_once('libs/modules/businesscontact/businesscontact.class.php');

function printSubTradegroupsForSelect($parentId, $depth){
	$all_subgroups = Tradegroup::getAllTradegroups($parentId);
	foreach ($all_subgroups AS $subgroup)
	{
		global $x;
		$x++; ?>
		<option value="<?=$subgroup->getId()?>">
			<?for ($i=0; $i<$depth+1;$i++) echo "&emsp;"?>
			<?= $subgroup->getTitle()?>
		</option>
		<? printSubTradegroupsForSelect($subgroup->getId(), $depth+1);
	}
}

?>
<!-- Lightbox -->
<link rel="stylesheet" href="../../../jscripts/lightbox/lightbox.css" type="text/css" media="screen" />
<script type="text/javascript" src="../../../jscripts/lightbox/lightbox.js"></script>
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="../../../css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="../../../css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="../../../css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="../../../jscripts/datatable/dataTables.tableTools.js"></script>
<script type="text/javascript" charset="utf8" src="../../../jscripts/tagit/tag-it.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../../jscripts/tagit/jquery.tagit.css" media="screen" />
<script type="text/javascript">
	$(document).ready(function() {
		var art_table = $('#art_table').DataTable( {
			// "scrollY": "600px",
			"processing": true,
			"bServerSide": true,
			"sAjaxSource": "modules/article/article.dt.ajax.php",
			"paging": true,
			"stateSave": false,
			"pageLength": 25,
			"dom": 'T<"clear">lrtip',
			"tableTools": {
				"sSwfPath": "../../../jscripts/datatable/copy_csv_xls_pdf.swf",
				"aButtons": [
					"copy",
					"csv",
					"xls",
					{
						"sExtends": "pdf",
						"sPdfOrientation": "landscape",
						"sPdfMessage": "Contilas - Articles"
					},
					"print"
				]
			},
			"lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
			"columns": [
				null,
				{ "sortable": false },
				null,
				null,
				null,
				{ "searchable": false },
				{ "searchable": false }
			],
			"fnServerData": function ( sSource, aoData, fnCallback ) {
				var tags = document.getElementById('ajax_tags').value;
				var bc = <?php echo (int)$_SESSION["cust_id"];?>;
				var cp = <?php echo (int)$_SESSION["contactperson_id"];?>;
				aoData.push( { "name": "search_tags", "value": tags, } );
				aoData.push( { "name": "bc", "value": bc, } );
				aoData.push( { "name": "cp", "value": cp, } );

				// Custom Fields
				var cfields = $("[data-fieldid]");
				cfields.each(function(){
					if ($(this).data("type") == "checkbox" && $(this).is( ":checked" )){
						aoData.push( { "name": "cfield_"+$(this).data("fieldid"), "value": 1 } );
					} else if ($(this).data("type") != "checkbox") {
						aoData.push( { "name": "cfield_"+$(this).data("fieldid"), "value": $(this).val() } );
					}
				});

				$.getJSON( sSource, aoData, function (json) {
					fnCallback(json)
				} );
			},
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

		$("#art_table tbody td").live('click',function(){
			var aPos = $('#art_table').dataTable().fnGetPosition(this);
			var aData = $('#art_table').dataTable().fnGetData(aPos[0]);
			document.location='index.php?pid=61&articleid='+aData[0];
		});

		$('#search').keyup(function(){
			art_table.search( $(this).val() ).draw();
		})
	} );
</script>

<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery("#tags").tagit({
			singleField: true,
			singleFieldNode: $('#tags'),
			singleFieldDelimiter: ";",
			allowSpaces: true,
			minLength: 2,
			removeConfirmation: true,
			tagSource: function( request, response ) {
				$.ajax({
					url: "../../../libs/modules/article/article.ajax.php?ajax_action=search_tags",
					data: { term:request.term },
					dataType: "json",
					success: function( data ) {
						response( $.map( data, function( item ) {
							return {
								label: item.label,
								value: item.value
							}
						}));
					}
				});
			},
			afterTagAdded: function(event, ui) {
				$('#ajax_tags').val($("#tags").tagit("assignedTags"));
				$('#art_table').dataTable().fnDraw();
			},
			afterTagRemoved: function(event, ui) {
				$('#ajax_tags').val($("#tags").tagit("assignedTags"));
				$('#art_table').dataTable().fnDraw();
			}
		});
	});
</script>

<script>
	$(function() {
		$( "#bc_cp" ).autocomplete({
			delay: 0,
			source: '../../../libs/modules/article/article.ajax.php?ajax_action=search_bc_cp',
			minLength: 2,
			dataType: "json",
			select: function(event, ui) {
				if (ui.item.type == 1)
				{
					$('#ajax_bc').val(ui.item.value);
					$('#bc_cp').val(ui.item.label);
					$('#art_table').dataTable().fnDraw();
				}
				else
				{
					$('#ajax_cp').val(ui.item.value);
					$('#bc_cp').val(ui.item.label);
					$('#art_table').dataTable().fnDraw();
				}
				return false;
			}
		});
	});
</script>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
			Artikel
			 <span class="pull-right">
				<button class="btn btn-xs btn-success"
						onclick="document.location.href='index.php?page=<?= $_REQUEST['page'] ?>&exec=new';">
					<span class="glyphicons glyphicons-plus"></span>
					<?= $_LANG->get('Artikel hinzuf&uuml;gen') ?>
				</button>
			</span>
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
				<div class="form-horizontal">
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Tags</label>
						<div class="col-sm-3">
							<input type="hidden" id="ajax_tags" name="ajax_tags"/>
							<input name="tags" id="tags" class="form-control" onfocus="markfield(this,0)"
								   onblur="markfield(this,1)">
						</div>
					</div>

					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Suche</label>
						<div class="col-sm-3">
							<input type="text" id="search" class="form-control" placeholder="">
						</div>
					</div>

					<?php
					$article_fields = CustomField::fetch([
						[
							'column'=>'class',
							'value'=>'Article'
						]
					]);
					if (count($article_fields) > 0){?>
						<br>
						<div class="panel panel-default collapseable">
							<div class="panel-heading">
								<h3 class="panel-title">
									Freifeld Filter
									<span class="pull-right clickable panel-collapsed"><i class="glyphicon glyphicon glyphicon-chevron-down"></i></span>
								</h3>
							</div>
							<div class="panel-body" style="display: none;">
								<div id="cfield_div"></div>
								<div class="form-group">
									<label for="" class="col-sm-2 control-label">Neuer Filter</label>
									<div class="col-sm-3">
										<select id="addfilterselect" class="form-control">
											<?php
											foreach ($article_fields as $article_field) {
												echo '<option value="' . $article_field->getId() . '">' . $article_field->getName() . '</option>';
											}
											?>
										</select>
										<select id="addfilter_backup" style="display: none;">
											<?php
											foreach ($article_fields as $article_field) {
												echo '<option value="' . $article_field->getId() . '">' . $article_field->getName() . '</option>';
											}
											?>
										</select>
									</div>
									<div class="col-sm-3">
										<button class="btn btn-sm btn-warning" onclick="addFilter();">
											Hinzufügen
										</button>
									</div>
								</div>
							</div>
						</div>
					<?php } ?>

					<div class="col-sm-12">
					 <span class="pull-right">
						 <button class="btn btn-xs btn-warning"
								 onclick="resetFilter();">
							 <span class="glyphicons glyphicons-ban-circle pointer"></span>
							 <?= $_LANG->get('Reset') ?>
						 </button>
					 </span>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="table-responsive">
		<table class="table table-hover" id="art_table">
			<thead>
			<tr>
				<th><?= $_LANG->get('ID') ?></th>
				<th><?= $_LANG->get('Bild') ?></th>
				<th><?= $_LANG->get('Titel') ?></th>
				<th><?= $_LANG->get('Art.-Nr.') ?></th>
				<th><?= $_LANG->get('Tags') ?></th>
				<th><?= $_LANG->get('Min. Lagerm.') ?></th>
				<th><?= $_LANG->get('Auf Lager') ?></th>
			</tr>
			</thead>
		</table>
	</div>
</div>

<script>
	function resetFilter(){
		$('#bc_cp').val('');
		$('#ajax_bc').val(0);
		$('#ajax_cp').val(0);
		$('#cfield_div').html("");

		var $options = $("#addfilter_backup > option").clone();
		$('#addfilterselect').html("");
		$('#addfilterselect').append($options);

		$('#art_table').dataTable().fnDraw();
	}
	function addFilter(){
		var filterid = $('#addfilterselect').val();
		$('#addfilterselect option:selected').remove();

		$.ajax({
			type: "GET",
			url: "../../../libs/modules/customfields/custom.field.ajax.php",
			data: { ajax_action: "getFilterField", id: filterid },
			success: function(data)
			{
				$('#cfield_div').append(data);
				$("[data-fieldid]").each(function(){
					$(this).change(function(){
						$('#art_table').dataTable().fnDraw();
					});
				});
			}
		});
	}
	$(document).on('click', '.panel-heading span.clickable', function(e){
		var $this = $(this);
		if(!$this.hasClass('panel-collapsed')) {
			$this.parents('.collapseable').find('.panel-body').slideUp();
			$this.addClass('panel-collapsed');
			$this.find('i').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
		} else {
			$this.parents('.collapseable').find('.panel-body').slideDown();
			$this.removeClass('panel-collapsed');
			$this.find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
		}
	})
</script>