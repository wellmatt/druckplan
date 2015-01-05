<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       29.08.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('businesscontact.class.php');
require_once 'attribute.class.php';


if($_REQUEST["exec"] == "delete"){
	$bc = new BusinessContact((int)$_REQUEST["id"]);
	$bc->delete();
}

if($_REQUEST["exec"] == "delete_attribute"){
	$att = new Attribute((int)$_REQUEST["attid"]);
	$att->delete();
	$_REQUEST["exec"] = "edit_cp";
}

if ($_REQUEST["exec"] == "edit")
{
	require_once('businesscontact.add.php');
}
elseif ($_REQUEST["exec"] == "edit_ad" OR $_REQUEST["exec"] == "edit_ai" OR $_REQUEST["exec"] == "save_a")
{
	require_once('address.add.php');
}
elseif ($_REQUEST["exec"] == "edit_cp" OR $_REQUEST["exec"] == "save_cp")
{
	require_once('contactperson.add.php');
}
elseif ($_REQUEST["exec"] == "delete_a")
{
	$a = new Address(trim(addslashes($_REQUEST["id_a"])));
	$a->delete();
	require_once('businesscontact.add.php');
}
elseif ($_REQUEST["exec"] == "delete_cp")
{
	$cp = new ContactPerson(trim(addslashes($_REQUEST["cpid"])));
	$cp->delete();
	require_once('businesscontact.add.php');
} else {
	
	$all_attributes = Attribute::getAllAttributesForCustomer();
	$businesscontacts = BusinessContact::getAllBusinessContactsForLists($order_str, $filter_str, $filter_attrib, $filter_item);
	
?>
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.bootstrap.js"></script>
<link rel="stylesheet" type="text/css" href="css/dataTables.tableTools.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.tableTools.js"></script>
<!-- <script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.columnFilter.js"></script> -->
<script type="text/javascript">
$(document).ready(function() {
    var bcon_table = $('#bcon_table').DataTable( {
        // "scrollY": "600px",
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/businesscontact/businesscontact.dt.ajax.php",
        "paging": true,
		"stateSave": true,  
		"dom": 'T<"clear">flrtip',        
		"tableTools": {
			"sSwfPath": "jscripts/datatable/copy_csv_xls_pdf.swf",
            "aButtons": [
                         "copy",
                         "csv",
                         "xls",
                         {
                             "sExtends": "pdf",
                             "sPdfOrientation": "landscape",
                             "sPdfMessage": "Contilas - Businesscontacts"
                         },
                         "print"
                     ]
                 },
		"pageLength": 50,
		"lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
		"columns": [
		            null,
		            null,
		            null,
		            null,
		            null,
		            null,
		            null,
		            { "sortable": false },
		            { "sortable": false }
		          ],
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
					},
    	"fnServerParams": function ( aoData ) {
    	      aoData.push( { "name": "filter_attrib", "value": $('#filter_attrib').val() } );
    	}
    } );
    $('#filter_attrib').on("change", function () {
    	$('#bcon_table').dataTable().fnDraw();
    });


    $("#bcon_table tbody td").live('click',function(){
        var aPos = $('#bcon_table').dataTable().fnGetPosition(this);
        var aData = $('#bcon_table').dataTable().fnGetData(aPos[0]);
//         alert(aData.join('\n'));
        document.location='index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id='+aData[0];
        // at this point aData is an array containing all the row info, use it to retrieve what you need.
    });
    
} );
</script>
<table width="100%">
	<tr>
		<td width="220" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <span style="font-size: 13px"><?=$_LANG->get('Gesch&auml;ftskontakte')?></span>
		</td>
		<td align="center">
			&ensp; 
		</td>
		<td class="content_row_clear">
			Merkmal-Filter: <select id="filter_attrib" name="filter_attrib" style="width:110px"	onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
				<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
				<? 
				foreach ($all_attributes AS $attribute){
				 	$allitems = $attribute->getItems();
					foreach ($allitems AS $item){ ?>
						<option value="<?=$attribute->getId()?>|<?=$item["id"]?>"><?=$item["title"]?></option>
					<? }
				} ?>
			</select>
		</td>
		<td width="290" class="content_header" align="right">
			<span style="font-size: 13px">
			<a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&tabshow=1" class="icon-link"
				><img src="images/icons/plus.png"> <?=$_LANG->get('Gesch&auml;ftskontakte hinzuf&uuml;gen')?></a>
			</span>
		</td>
	</tr>
</table>
<div class="box1">
	<table id="bcon_table" width="100%" class="stripe hover row-border order-column">
        <thead>
            <tr>
                <th width="10">ID</th>
                <th width="80">Nr.</th>
                <th width="100">Matchcode</th>
                <th>Firma</th>
                <th width="100">Ort</th>
                <th width="120">Typ</th>
                <th width="120">Lieferant</th>
                <th width="180">Merkmale</th>
                <th width="80">Optionen</th>
            </tr>
        </thead>
	</table>
</div>
<?}
?>