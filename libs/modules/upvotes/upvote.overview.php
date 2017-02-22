<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */
require_once 'upvote.class.php';


if ($_REQUEST["exec"] == "delete"){
	$delvote = new UpVote((int)$_REQUEST["delid"]);
	$delvote->delete();
}

?>

<!-- DataTables Editor -->
<link rel="stylesheet" type="text/css" href="jscripts/datatableeditor/datatables.min.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/datatables.min.js"></script>

<script type="text/javascript" src="jscripts/datatableeditor/FieldType-autoComplete/editor.autoComplete.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/datatableeditor/FieldType-bootstrapDate/editor.bootstrapDate.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/FieldType-bootstrapDate/editor.bootstrapDate.js"></script>
<script type="text/javascript" src="jscripts/datatableeditor/FieldType-datetimepicker-2/editor.datetimepicker-2.js"></script>

<script type="text/javascript" src="jscripts/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="jscripts/ckeditor/config.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/ckeditor/skins/bootstrapck/editor.css"/>
<script type="text/javascript" src="jscripts/datatableeditor/FieldType-ckeditor/editor.ckeditor.js"></script>
<!-- /DataTables Editor -->

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
			UpVotes
			<span class="pull-right">
				<button class="btn btn-xs btn-success" type="button" onclick="window.location.href='index.php?page=libs/modules/upvotes/upvote.edit.php';">
					<span class="glyphicons glyphicons-plus"></span>
					UpVote hinzufügen
				</button>
			</span>
		</h3>
    </div>
    <div class="table-responsive">
    	<table class="table table-hover" id="datatable">
    		<thead>
    			<tr>
    				<th>ID</th>
    				<th>Titel</th>
    				<th>Kunde</th>
    				<th>User</th>
    				<th>Datum</th>
    				<th>Up</th>
    				<th>Down</th>
    			</tr>
    		</thead>
    		<tbody>
    			<tr>
					<th>ID</th>
					<th>Titel</th>
					<th>Kunde</th>
					<th>User</th>
					<th>Datum</th>
					<th>Up</th>
					<th>Down</th>
    			</tr>
    		</tbody>
    	</table>
    </div>
</div>

<script type="text/javascript" language="javascript" class="init">
	var editor; // use a global for the submit and return data rendering in the examples
	var table; // use global for table

	$(document).ready(function() {

		table = $('#datatable').DataTable( {
			dom: "<'row'<'col-sm-4'l><'col-sm-4'B><'col-sm-4'f>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>",
			ajax: {
				url: 'libs/basic/datatables/upvotes.php',
				data: {	}
			},
			order: [[ 6, 'desc' ]],
			columns: [
				{ data: "upvotes.id", orderable: true },
				{ data: "upvotes.title", orderable: true },
				{ data: "businesscontact.name1", orderable: true },
				{ data: 'user',
					render: function ( data, type, row ) {
						return data.user_firstname +' '+ data.user_lastname;
					}, orderable: true },
				{ data: "upvotes.crtdate", orderable: true },
				{ data: "upvotes.upvotes", orderable: true },
				{ data: "upvotes.downvotes", orderable: true },
			],
			select: false,
			buttons: [
				// Export Button
				{
					extend: 'collection',
					text: 'Export',
					buttons: [
						'copy',
						'excel',
						'csv',
						'pdf',
						'print'
					]
				}
			],
			language: {
				url: '//cdn.datatables.net/plug-ins/1.10.13/i18n/German.json'
			}
		} );

		$("#datatable").on('click', 'tbody td', function(){
			var data = table.row( this ).data();
			var id = data.upvotes.id;
			document.location='index.php?page=libs/modules/upvotes/upvote.edit.php&id='+id;
		});
	} );
</script>