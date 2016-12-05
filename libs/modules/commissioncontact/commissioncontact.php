<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       08.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/commissioncontact/commissioncontact.class.php';

if($_REQUEST["exec"] == "delete")
{
	$bc = new CommissionContact((int)$_REQUEST["id"]);
	$bc->delete();
}

if ($_REQUEST["exec"] == "edit")
{
	require_once('commissioncontact.add.php');
}
elseif ($_REQUEST["exec"] == "edit_ad" OR $_REQUEST["exec"] == "edit_ai" OR $_REQUEST["exec"] == "save_a")
{
	require_once('address.add.php');
}
elseif ($_REQUEST["exec"] == "edit_cp" OR $_REQUEST["exec"] == "save_cp")
{
	require_once('libs/modules/businesscontact/contactperson.add.php');
}
elseif ($_REQUEST["exec"] == "delete_a")
{
	$a = new Address(trim(addslashes($_REQUEST["id_a"])));
	$a->delete();
	require_once('commissioncontact.add.php');
}
elseif ($_REQUEST["exec"] == "delete_cp")
{
	$cp = new ContactPerson(trim(addslashes($_REQUEST["cpid"])));
	$cp->delete();
	require_once('commissioncontact.add.php');
} else {
	
	$order = (int)$_REQUEST["order"];
	$order_str = CommissionContact::ORDER_NAME;
	if($order == 1){ $order_str = CommissionContact::ORDER_ID;}
	if($order == 2){ $order_str = CommissionContact::ORDER_NAME;}
	if($order == 3){ $order_str = CommissionContact::ORDER_CITY;}
	if($order == 4){ $order_str = CommissionContact::ORDER_TYPE;}
	
	$filter = $_REQUEST["filter"];
	$filter_str = CommissionContact::FILTER_ALL;
	if($filter != ""){
		$filter_str = CommissionContact::FILTER_NAME1.$filter."%' ";
	}
	
	$only = ""; // Damit bei den Buchstaben auch korrekt nur Kunder o. Lieferant gefiltert wird
	if($_REQUEST["only_cust"] == 1){
		$filter_str .= " AND ".CommissionContact::FILTER_ONLY_CUST;
		$only = "&only_cust=1";
	}
	if($_REQUEST["only_supp"] == 1){
		$filter_str .= " AND ".CommissionContact::FILTER_ONLY_SUPP;
		$only = "&only_supp=1";
	}
	
	$commissioncontacts = CommissionContact::getAllCommissionContacts($order_str, $filter_str);
	
	// CSV-Datei vorbereiten
	$csv_file = fopen('./docs/'.$_USER->getId().'-Kunden.csv', "w");
	//fwrite($csv_file, "Firma iPactor - �bersicht\n");
	
	//Tabellenkopf der CSV-Datei schreiben
	$csv_string .= "Firma; Strasse; PLZ; Ort;";
	$csv_string .= "Telefon; Fax; E-Mail\n";
?>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
			Provisionskontakte
			&emsp; &emsp;
			<span class="center">
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>"><span
								class="glyphicons glyphicons-file-lock" title="<?= $_LANG->get('Alle') ?>"></span> </a>
				&emsp;
				<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=a<?= $only ?>"><img
						src="images/icons/document-attribute.png" alt="A"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=b<?= $only ?>"><img
								src="images/icons/document-attribute-b.png" alt="B"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=c<?= $only ?>"><img
								src="images/icons/document-attribute-c.png" alt="C"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=d<?= $only ?>"><img
								src="images/icons/document-attribute-d.png" alt="D"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=e<?= $only ?>"><img
								src="images/icons/document-attribute-e.png" alt="E"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=f<?= $only ?>"><img
								src="images/icons/document-attribute-f.png" alt="F"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=g<?= $only ?>"><img
								src="images/icons/document-attribute-g.png" alt="G"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=h<?= $only ?>"><img
								src="images/icons/document-attribute-h.png" alt="H"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=i<?= $only ?>"><img
								src="images/icons/document-attribute-i.png" alt="I"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=j<?= $only ?>"><img
								src="images/icons/document-attribute-j.png" alt="J"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=k<?= $only ?>"><img
								src="images/icons/document-attribute-k.png" alt="K"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=l<?= $only ?>"><img
								src="images/icons/document-attribute-l.png" alt="L"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=m<?= $only ?>"><img
								src="images/icons/document-attribute-m.png" alt="M"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=n<?= $only ?>"><img
								src="images/icons/document-attribute-n.png" alt="N"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=o<?= $only ?>"><img
								src="images/icons/document-attribute-o.png" alt="O"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=p<?= $only ?>"><img
								src="images/icons/document-attribute-p.png" alt="P"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=q<?= $only ?>"><img
								src="images/icons/document-attribute-q.png" alt="Q"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=r<?= $only ?>"><img
								src="images/icons/document-attribute-r.png" alt="R"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=s<?= $only ?>"><img
								src="images/icons/document-attribute-s.png" alt="S"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=t<?= $only ?>"><img
								src="images/icons/document-attribute-t.png" alt="T"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=u<?= $only ?>"><img
								src="images/icons/document-attribute-u.png" alt="U"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=v<?= $only ?>"><img
								src="images/icons/document-attribute-v.png" alt="V"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=w<?= $only ?>"><img
								src="images/icons/document-attribute-w.png" alt="W"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=x<?= $only ?>"><img
								src="images/icons/document-attribute-x.png" alt="X"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=y<?= $only ?>"><img
								src="images/icons/document-attribute-y.png" alt="Y"/></a>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=<?= $order ?>&filter=z<?= $only ?>"><img
								src="images/icons/document-attribute-z.png" alt="Z"/></a>
				&emsp;
				<a href="./docs/<?= $_USER->getId() ?>-Provisionsempfänger.csv"><span
						class="glyphicons glyphicons-file-lock" title="Aktuelle Liste exportieren"></span> </a>
					</span>
					<span class="pull-right">
						<a
							<button class="btn btn-xs btn-success" href="index.php?page=<?= $_REQUEST['page'] ?>&exec=edit">
								<span style="color: white;" class="glyphicons glyphicons-plus pointer"></span>
									<?= $_LANG->get('Provisionskontakt hinzuf&uuml;gen') ?>
							</button>
						</a>
					</span>
		</h3>
	</div>
	<div class="panel-body">

		<div class="table-responsive">
			<table class="table table-hover">
				<thead>
				<tr>
					<th>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=1&filter=<?= $filter ?>"><?= $_LANG->get('ID') ?></a>
					</th>
					<th>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=2&filter=<?= $filter ?>"><?= $_LANG->get('Firma') ?></a>
					</th>
					<th>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=3&filter=<?= $filter ?>"><?= $_LANG->get('Ort') ?></a>
					</th>
					<th>
						<a href="index.php?page=<?= $_REQUEST['page'] ?>&order=4&filter=<?= $filter ?>"><?= $_LANG->get('Typ') ?></a>
					</th>
					<? if ($_USER->isAdmin()) echo '<th>' . $_LANG->get('Mandant') . '</th>'; ?>
					<th>
						<?= $_LANG->get('Optionen') ?>
					</th>
				</tr>
				</thead>
				<? $x = 0;
				foreach ($commissioncontacts as $bc) {
					$csv_string .= $bc->getNameAsLine() . ";" . $bc->getAddress1() . " " . $bc->getAddress2() . ";";
					$csv_string .= $bc->getZip() . ";" . $bc->getCity() . ";";
					$csv_string .= $bc->getPhone() . ";" . $bc->getFax() . ";" . $bc->getEmail() . " \n";
					?>
					<tbody>
					<tr class="<?= getRowColor($x) ?>">
						<td><?= $bc->getId() ?>&nbsp;</td>
						<td><?= $bc->getNameAsLine() ?>&nbsp;</td>
						<td><?= $bc->getCity() ?>&nbsp;</td>
						<td><?
							if ($bc->getCustomer() <= 0) {
								echo ($bc->isSupplier()) ? $_LANG->get('Lieferant') : "";
							} else {
								echo ($bc->isSupplier() == 1) ? $_LANG->get('Lieferant') . ', ' . $_LANG->get('Kunde') : $_LANG->get('Kunde');
							}
							?>
						</td>
						<? if ($_USER->isAdmin()) echo '<td class="content_row">' . $bc->getClient()->getName() . '&nbsp;</td>'; ?>
						<td>
							<a href="index.php?page=<?= $_REQUEST['page'] ?>&exec=edit&id=<?= $bc->getId() ?>"><span
									class="glyphicons glyphicons-pencil"></span></a>
							&ensp;
							<a href="#"
							   onclick="askDel('index.php?page=<?= $_REQUEST['page'] ?>&exec=delete&id=<?= $bc->getId() ?>')"><span
									class="glyphicons glyphicons-remove" style="color: red;"></span></a>
						</td>
					</tr>
					</tbody>
					<? $x++;
				}
				// $csv_string = iconv('UTF-8', 'ISO-8859-1', $csv_string);
				fwrite($csv_file, $csv_string);
				fclose($csv_file);
				?>
			</table>
		</div>
		<?
		}
		?>
	</div>
</div>