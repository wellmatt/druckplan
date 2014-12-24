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
	
	$order = (int)$_REQUEST["order"];
	$order_str = BusinessContact::ORDER_NAME;
	if($order == 1){ $order_str = BusinessContact::ORDER_CUST_NR;}
	if($order == 2){ $order_str = BusinessContact::ORDER_NAME;}
	if($order == 3){ $order_str = BusinessContact::ORDER_CITY;}
	if($order == 4){ $order_str = BusinessContact::ORDER_TYPE;}
	
	$filter = $_REQUEST["filter"];
	$filter_str = BusinessContact::FILTER_ALL;
	if($filter != ""){
		$filter_str = BusinessContact::FILTER_NAME1.$filter."%' ";
	}
	if($_REQUEST["attrib"] && $_REQUEST["item"]) {
		$filter_attrib = $_REQUEST["attrib"];
		$filter_item = $_REQUEST["item"];
	} else {
		$filter_attrib = 0;
		$filter_item = 0;
	}
	
	$all_attributes = Attribute::getAllAttributesForCustomer();
	$businesscontacts = BusinessContact::getAllBusinessContactsForLists($order_str, $filter_str, $filter_attrib, $filter_item);
	
	// CSV-Datei vorbereiten
	$csv_file = fopen('./docs/'.$_USER->getId().'-Kunden.csv', "w");
	//fwrite($csv_file, "Firma iPactor - Uebersicht\n");
	
	//Tabellenkopf der CSV-Datei schreiben
	$csv_string .= "Firma; Strasse; PLZ; Ort;";
	$csv_string .= "Telefon; Fax; E-Mail\n";
	
?>
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    var bcon_table = $('#bcon_table').DataTable( {
        // "scrollY": "600px",
        "paging": true,
		"stateSave": true,
		"dom": 'flrtip',
		"pageLength": 50,
		"lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
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
<table width="100%">
	<tr>
		<td width="220" class="content_header">
			<img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <span style="font-size: 13px"><?=$_LANG->get('Gesch&auml;ftskontakte')?></span>
		</td>
		<td align="center">
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>"><img src="images/icons/document-number.png" alt="Alle" title="<?=$_LANG->get('Alle')?>" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=a"><img src="images/icons/document-attribute.png" alt="A" /></a>  
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=b"><img src="images/icons/document-attribute-b.png" alt="B" /></a>  
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=c"><img src="images/icons/document-attribute-c.png" alt="C" /></a>  
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=d"><img src="images/icons/document-attribute-d.png" alt="D" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=e"><img src="images/icons/document-attribute-e.png" alt="E" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=f"><img src="images/icons/document-attribute-f.png" alt="F" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=g"><img src="images/icons/document-attribute-g.png" alt="G" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=h"><img src="images/icons/document-attribute-h.png" alt="H" /></a>        
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=i"><img src="images/icons/document-attribute-i.png" alt="I" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=j"><img src="images/icons/document-attribute-j.png" alt="J" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=k"><img src="images/icons/document-attribute-k.png" alt="K" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=l"><img src="images/icons/document-attribute-l.png" alt="L" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=m"><img src="images/icons/document-attribute-m.png" alt="M" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=n"><img src="images/icons/document-attribute-n.png" alt="N" /></a>    
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=o"><img src="images/icons/document-attribute-o.png" alt="O" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=p"><img src="images/icons/document-attribute-p.png" alt="P" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=q"><img src="images/icons/document-attribute-q.png" alt="Q" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=r"><img src="images/icons/document-attribute-r.png" alt="R" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=s"><img src="images/icons/document-attribute-s.png" alt="S" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=t"><img src="images/icons/document-attribute-t.png" alt="T" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=u"><img src="images/icons/document-attribute-u.png" alt="U" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=v"><img src="images/icons/document-attribute-v.png" alt="V" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=w"><img src="images/icons/document-attribute-w.png" alt="W" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=x"><img src="images/icons/document-attribute-x.png" alt="X" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=y"><img src="images/icons/document-attribute-y.png" alt="Y" /></a> 
			<a class="icon-link" href="index.php?page=<?=$_REQUEST["page"]?>&order=<?=$order?>&filter=z"><img src="images/icons/document-attribute-z.png" alt="Z" /></a>
			&ensp; 
		</td>
		<td class="content_row_clear">
			<select id="filter_attrib" name="filter_attrib" onchange="document.getElementById('attrib_link').href='index.php?page=<?=$_REQUEST["page"]?>'+document.getElementById('filter_attrib').value;" style="width:110px"	onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
				<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
				<? 
				foreach ($all_attributes AS $attribute){
				 	$allitems = $attribute->getItems();
					foreach ($allitems AS $item){ ?>
						<option value="&attrib=<?=$attribute->getId()?>&item=<?=$item["id"]?>"><?=$item["title"]?></option>
					<? }
				} ?>
			</select><a id="attrib_link" class="icon-link" href=""><img src="images/icons/magnifier-left.png" alt="" style="cursor: pointer;"></a>
		</td>
		<td width="290" class="content_header" align="right">
				<a href="./docs/<?=$_USER->getId()?>-Kunden.csv"><img src="images/icons/arrow-turn.png" title="Aktuelle Liste exportieren"
					><?=$_LANG->get('Export');?></a>
			<span style="font-size: 13px">
			<a href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&tabshow=1" class="icon-link"
				><img src="images/icons/plus.png"> <?=$_LANG->get('Gesch&auml;ftskontakte hinzuf&uuml;gen')?></a>
			</span>
		</td>
	</tr>
</table>
<div class="box1">
	<table id="bcon_table" width="100%" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th width="80"><?=$_LANG->get('Nr.')?></th>
                <th><?=$_LANG->get('Firma')?></th>
                <th width="200"><?=$_LANG->get('Ort')?></th>
                <th width="120"><?=$_LANG->get('Typ')?></th>
                <th width="140"><?=$_LANG->get('Merkmal (Typ)')?></th>
                <th width="80"><?=$_LANG->get('Optionen')?></th>
            </tr>
        </thead>
		<?
		/*******  Pruefen .......    ***********************/
		ob_implicit_flush(1);
		//ob_start();
		ob_end_flush();
		ob_start();
		/*****    .... ob das hier noch gebraucht wird bzw. funktioniert *****/
		
		$x = 0;
		foreach ($businesscontacts as $bc){
			$csv_string .= $bc->getNameAsLine().";".$bc->getAddress1()." ".$bc->getAddress2().";";
			$csv_string .= $bc->getZip().";".$bc->getCity().";";
			$csv_string .= $bc->getPhone().";".$bc->getFax().";".$bc->getEmail()." \n";
			?>
		<tr class="<?=getRowColor($x)?>" onmouseover="mark(this, 0)" onmouseout="mark(this,1)">
			<td class="content_row" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$bc->getId()?>'">
				<?=$bc->getCustomernumber()?>&nbsp;
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$bc->getId()?>'">
				<?=$bc->getNameAsLine()?>&nbsp;
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$bc->getId()?>'">
				<?=$bc->getCity()?>&nbsp;
			</td>
			<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$bc->getId()?>'">
			<? 
			$tmp_output = "";
			if($bc->isExistingCustomer()){
				$tmp_output .= $_LANG->get('Best-Kunde').", ";
			}
			if($bc->isPotentialCustomer()){
				$tmp_output .= $_LANG->get('Interessent').", ";
			}
			if($bc->isSupplier()){
				$tmp_output .= $_LANG->get('Lieferant').", ";
			}
			if($bc->isSpezialCustomer()){
				$tmp_output .= $_LANG->get('Spezial').", ";
			}
			echo substr($tmp_output, 0, -2);
			?> &ensp;
			</td>
			<? /* if($_USER->isAdmin()){ ?>
				<td class="content_row pointer" onclick="document.location='index.php?exec=edit&id=<?=$bc->getId()?>'">
					<?=$bc->getClient()->getName()?> &nbsp;
				</td>
			<?}*/?>
			<td class="content_row pointer" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$bc->getId()?>'">
			<?	$attribute_id = 25; 				// ID des Attributes Typ fuer GEschaeftskontakte	
				$attribute = new Attribute($attribute_id);
				$all_attribue_items = $attribute->getItems();
				$tmp_output = "";
				foreach ($all_attribue_items AS $aai){
					if($bc->getIsAttributeItemActive($attribute_id, $aai["id"])){
						$tmp_output .= $aai["title"].", ";
					}
				}
				echo substr($tmp_output, 0, -2);
				?>&nbsp;
			</td>
			<td class="content_row" align="right" onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$bc->getId()?>'">
				<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit&id=<?=$bc->getId()?>"><img src="images/icons/pencil.png"></a>
				&ensp;
				<? if($_USER->isAdmin() && $_USER->getId() != 14){ ?>
				<a class="icon-link" href="#" onclick="askDel('index.php?page=<?=$_REQUEST['page']?>&exec=delete&id=<?=$bc->getId()?>')"><img	src="images/icons/cross-script.png"> </a>
				<?}?>
			</td>
		</tr>
		<? 	$x++;
		}
		$csv_string = iconv('UTF-8', 'ISO-8859-1', $csv_string);
		fwrite($csv_file, $csv_string);
		fclose($csv_file);
		?>
	</table>
</div>
<?}
?>