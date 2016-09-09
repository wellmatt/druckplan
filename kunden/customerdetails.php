<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.06.2013
// Copyright:     2012-13 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------

$busicon = $_SESSION["businesscontact"]; // gln hier Umbau auf globale Variable aus index.php noetig

$address = new Address();
$r_address = new Address();

if($_REQUEST["subexec"] == "save"){
	
	// Standart-Clienten setzen, meist ID=1
	$client = new Client(1);
	
	/*$busicon =  $_SESSION["businesscontact"];
	$busicon->setName1(trim(addslashes($_REQUEST["name1"])));
	$busicon->setName2(trim(addslashes($_REQUEST["name2"])));
	$busicon->setAddress1(trim(addslashes($_REQUEST["adress1"])));
	$busicon->setAddress2(trim(addslashes($_REQUEST["adress2"])));
	$busicon->setCity(trim(addslashes($_REQUEST["city"])));
	$busicon->setZip(trim(addslashes($_REQUEST["plz"])));
	$busicon->setCountry(new Country (trim(addslashes($_REQUEST["country"]))));
	//$busicon->setLanguage(new Translator((int)$_REQUEST["language"]));
	
	$busicon->setPhone(trim(addslashes($_REQUEST["phone"])));
	$busicon->setEmail(trim(addslashes($_REQUEST["email"])));
	$busicon->setFax(trim(addslashes($_REQUEST["fax"])));
	$busicon->setWeb(trim(addslashes($_REQUEST["web"])));
	
	//$busicon->setShoplogin(trim(addslashes($_REQUEST["cust_username"])));
	//$busicon->setShoppass(trim(addslashes($_REQUEST["cust_password"])));
	
	$busicon->setClient($client);
	$busicon->setSupplier(0);
	$busicon->setCustomer(1);
	$busicon->setActive(1);
	$busicon->setLanguage(new Translator(22));

	$busicon->save();
	
	$savemsg = $_LANG->get("Erfolgreich gespeichert");
	
	$_SESSION["businesscontact"] = $busicon; 
	*/
	//gln, 11.02.14: neue Lieferadresse hinzufuegen
	if (strlen($_REQUEST["name1"]) > 0){ 
		$address->setActive(2);
		$address->setShoprel(1);	//gln
    	$address->setName1(trim(addslashes($_REQUEST["name1"])));
	    $address->setName2(trim(addslashes($_REQUEST["name2"])));
    	$address->setAddress1(trim(addslashes($_REQUEST["address1"])));
	    $address->setAddress2(trim(addslashes($_REQUEST["address2"])));
	    $address->setZip(trim(addslashes($_REQUEST["zip"])));
	    $address->setCity(trim(addslashes($_REQUEST["city"])));
	    $address->setMobil(trim(addslashes($_REQUEST["mobil"])));
	    $address->setPhone(trim(addslashes($_REQUEST["phone"])));
	    $address->setFax(trim(addslashes($_REQUEST["fax"])));
	    $address->setCountry(new Country (trim(addslashes($_REQUEST["country"]))));
	    $address->setBusinessContact($busicon);

		$res = getSaveMessage($address->save());
		if($res){
			$savemsg = $_LANG->get("Erfolgreich gespeichert");
		}
	}
	//ascherer, 16.09.14: neue Rechnungsadresse hinzufuegen
	if (strlen($_REQUEST["r_name1"]) > 0){ 
		$r_address->setActive(1);
		$r_address->setShoprel(1);	//gln
    	$r_address->setName1(trim(addslashes($_REQUEST["r_name1"])));
	    $r_address->setName2(trim(addslashes($_REQUEST["r_name2"])));
    	$r_address->setAddress1(trim(addslashes($_REQUEST["r_address1"])));
	    $r_address->setAddress2(trim(addslashes($_REQUEST["r_address2"])));
	    $r_address->setZip(trim(addslashes($_REQUEST["r_zip"])));
	    $r_address->setCity(trim(addslashes($_REQUEST["r_city"])));
	    $r_address->setMobil(trim(addslashes($_REQUEST["r_mobil"])));
	    $r_address->setPhone(trim(addslashes($_REQUEST["r_phone"])));
	    $r_address->setFax(trim(addslashes($_REQUEST["r_fax"])));
	    $r_address->setCountry(new Country (trim(addslashes($_REQUEST["r_country"]))));
	    $r_address->setBusinessContact($busicon);

		$res = getSaveMessage($r_address->save());
		if($res){
			$savemsg = $_LANG->get("Erfolgreich gespeichert");
		}
	}
    //$savemsg = getSaveMessage($address->save());
    //$savemsg .= $DB->getLastError();
	echo $savemsg;
	// refresh page ?>
	<script language="JavaScript">
		location.href = 'index.php?pid=<?=$_REQUEST["pid"]?>';
	</script><?
}

$countries = Country::getAllCountries();
$languages = Translator::getAllLangs(Translator::ORDER_NAME);
//gln $all_deliveryAddresses = Address::getAllAddresses($busicon, Address::ORDER_NAME, Address::FILTER_DELIV);
$all_deliveryAddresses = Address::getAllAddresses($busicon, Address::ORDER_NAME, Address::FILTER_DELIV_SHOP);
$all_invoiceAddresses = Address::getAllAddresses($busicon, Address::ORDER_NAME, Address::FILTER_INVC);
?>
<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="../css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="../css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="../jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="../jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="../jscripts/datatable/dataTables.bootstrap.js"></script>

<script language="javascript">
function askDel(myurl)
{
   if(confirm("Sind Sie sicher?"))
   {
      if(myurl != '')
         location.href = myurl;
      else
         return true;
   }
   return false;
}
</script>

<script type="text/javascript">
	$(document).ready(function() {
		var table_deliv = $('#table_deliv').DataTable({
			"paging": true,
			"stateSave": false,
			"pageLength": 25,
			"dom": 'flrtip',
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
		});
		var table_inv = $('#table_inv').DataTable({
			"paging": true,
			"stateSave": false,
			"pageLength": 25,
			"dom": 'flrtip',
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
		});
	});
</script>
<form id="change_customer" method="post" class="form-horizontal">
	<input type="hidden" name="pid" value="<?=$_REQUEST["pid"]?>" >
	<?/*<input type="hidden" name="subexec" value="save" > */?>
	<div class="panel panel-default">
		  <div class="panel-heading">
				<h3 class="panel-title">
					Profil
				</h3>
		  </div>
		  <div class="panel-body">
			  <div class="panel panel-default">
			  	  <div class="panel-heading">
			  			<h3 class="panel-title">
							Adressdaten
						</h3>
			  	  </div>
				  <div class="panel-body">


					  <div class="row">
						  <div class="col-sm-3">Firmenname</div>
						  <div class="col-sm-4" >
							  <?=$busicon->getName1()?>
						  </div>
					  </div>


					  <div class="row">
						  <div class="col-sm-3">Firmenname (Zusatz)</div>
						  <div class="col-sm-4 ">
							  <?=$busicon->getName2()?>
						  </div>
					  </div>

					  <div class="row">
						  <div class="col-sm-3">Straße</div>
						  <div class="col-sm-4 ">
							  <?=$busicon->getAddress1()?>
						  </div>
					  </div>

					  <div class="row">
						  <div class="col-sm-3">Straße (Zusatz)</div>
						  <div class="col-sm-4 ">
							  <?=$busicon->getAddress2()?>
						  </div>
					  </div>

					  <div class="row">
						  <div class="col-sm-3">PLZ/Stadt</div>
						  <div class="col-sm-4 ">
							  <?=$busicon->getZip()?>&ensp;<?=$busicon->getCity()?>
						  </div>
					  </div>

					  <div class="row">
						  <div class="col-sm-3">Land</div>
						  <div class="col-sm-4 ">
							  <?$c=new Country($busicon->getCountry()->getId()); echo $c->getName()?>
						  </div>
					  </div>
				  </div>
			  </div>
			  <div class="panel panel-default">
			  	  <div class="panel-heading">
			  			<h3 class="panel-title">
							Kontaktdaten
						</h3>
			  	  </div>
			  	  <div class="panel-body">

					  <div class="row">
						  <div class="col-sm-3">E-Mail</div>
						  <div class="col-sm-4 ">
							  <?=$busicon->getEmail()?>
						  </div>
					  </div>

					  <div class="row">
						  <div class="col-sm-3">Telefon</div>
						  <div class="col-sm-4 ">
							  <?=$busicon->getPhone()?>
						  </div>
					  </div>

					  <div class="row">
						  <div class="col-sm-3">Fax</div>
						  <div class="col-sm-4 ">
							  <?=$busicon->getFax()?>
						  </div>
					  </div>

					  <div class="row">
						  <div class="col-sm-3">Web</div>
						  <div class="col-sm-4 ">
							  <?=$busicon->getWeb()?>
						  </div>
					  </div>

			  	  </div>
			  </div>
</form>
			  <form method="post" id="neue_lieferadr" class="form-horizontal" style="display:none" >
					<input type="hidden" name="subexec" value="save" >
					<div class="panel panel-default">
						  <div class="panel-heading">
								<h3 class="panel-title">
									Neue Lieferadresse
								</h3>
						  </div>


						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Firma</label>
							<div class="col-sm-5">
								<input name="name1"
									   class="form-control" value="<?=$address->getName1()?>"
									   onfocus="markfield(this,0)" onblur="markfield(this,1)">
							</div>
						</div>


						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Firmenzusatz</label>
							<div class="col-sm-5">
								<input name="name2"
									   class="form-control" value="<?=$address->getName2()?>"
									   onfocus="markfield(this,0)" onblur="markfield(this,1)">
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Adresse</label>
							<div class="col-sm-5">
								<input name="address1"
									   class="form-control" value="<?=$address->getAddress1()?>"
									   onfocus="markfield(this,0)" onblur="markfield(this,1)">
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Adresszusatz</label>
							<div class="col-sm-5">
								<input name="address2"
									   class="form-control" value="<?=$address->getAddress2()?>"
									   onfocus="markfield(this,0)" onblur="markfield(this,1)">
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label"></label>
							<div class="col-sm-2">
								<input name="zip"  class="form-control" value="<?=$address->getZip()?>"
									   onfocus="markfield(this,0)" onblur="markfield(this,1)" placeholder="PLZ">
							</div>
							<div class="col-sm-3">
								<input name="city"  class="form-control" value="<?=$address->getCity()?>"
									   onfocus="markfield(this,0)" onblur="markfield(this,1)" placeholder="Stadt">
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Land</label>
							<div class="col-sm-5">
								<select name="country"
										class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
									<?
									foreach($countries as $c)
									{?>
										<option value="<?=$c->getId()?>"
											<?if ($address->getCountry()->getId() == $c->getId()) echo "selected";?>>
											<?=$c->getName()?>
										</option>
									<?}?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Telefon</label>
							<div class="col-sm-5">
								<input name="phone"  class="form-control" value="<?=$address->getPhone()?>"
									   onfocus="markfield(this,0)" onblur="markfield(this,1)">
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Fax</label>
							<div class="col-sm-5">
								<input name="fax"  class="form-control" value="<?=$address->getFax()?>"
									   onfocus="markfield(this,0)" onblur="markfield(this,1)">
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Mobil</label>
							<div class="col-sm-5">
								<input name="mobil"  class="form-control" value="<?=$address->getMobil()?>"
									   onfocus="markfield(this,0)" onblur="markfield(this,1)">
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label"></label>
							<div class="col-sm-5">
								<input type="submit" name="submit" value="<?=$_LANG->get('Speichern')?>">
								<?=$savemsg?>
							</div>
						</div>
					</div>
				</form>


				<form method="post" id="neue_rechadr" class=form-horizontal style="display:none">
					<input type="hidden" name="subexec" value="save" >
					<div class="panel panel-default">
						  <div class="panel-heading">
								<h3 class="panel-title">
									Neue Rechnungsadresse
								</h3>
						  </div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Firma</label>
							<div class="col-sm-5">
								<input name="r_name1"
									   class="form-control" value="<?=$address->getName1()?>"
									   onfocus="markfield(this,0)" onblur="markfield(this,1)">
							</div>
						</div>


						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Firmenzusatz</label>
							<div class="col-sm-5">
								<input name="r_name2"
									   class="form-control" value="<?=$address->getName2()?>"
									   onfocus="markfield(this,0)" onblur="markfield(this,1)">
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Adresse</label>
							<div class="col-sm-5">
								<input name="r_address1"
									   class="form-control" value="<?=$address->getAddress1()?>"
									   onfocus="markfield(this,0)" onblur="markfield(this,1)">
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Adresszusatz</label>
							<div class="col-sm-5">
								<input name="r_address2"
									   class="form-control" value="<?=$address->getAddress2()?>"
									   onfocus="markfield(this,0)" onblur="markfield(this,1)">
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label"></label>
							<div class="col-sm-2">
									<input name="r_zip"  class="form-control" value="<?=$address->getZip()?>"
										   onfocus="markfield(this,0)" onblur="markfield(this,1)" placeholder="PLZ">
							</div>
							<div class="col-sm-3">
									<input name="r_city"  class="form-control" value="<?=$address->getCity()?>"
										   onfocus="markfield(this,0)" onblur="markfield(this,1)" placeholder="Stadt">
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Land</label>
							<div class="col-sm-5">
								<select name="r_country"
										class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
									<?
									foreach($countries as $c)
									{?>
										<option value="<?=$c->getId()?>"
											<?if ($address->getCountry()->getId() == $c->getId()) echo "selected";?>>
											<?=$c->getName()?>
										</option>
									<?}?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Telefon</label>
							<div class="col-sm-5">
								<input name="r_phone"  class="form-control" value="<?=$address->getPhone()?>"
									   onfocus="markfield(this,0)" onblur="markfield(this,1)">
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Fax</label>
							<div class="col-sm-5">
								<input name="r_fax"  class="form-control" value="<?=$address->getFax()?>"
									   onfocus="markfield(this,0)" onblur="markfield(this,1)">
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label">Mobil</label>
							<div class="col-sm-5">
								<input name="r_mobil"  class="form-control" value="<?=$address->getMobil()?>"
									   onfocus="markfield(this,0)" onblur="markfield(this,1)">
							</div>
						</div>

						<div class="form-group">
							<label for="" class="col-sm-2 control-label"></label>
							<div class="col-sm-5">
								<input type="submit" name="submit" value="<?=$_LANG->get('Speichern')?>">
								<?=$savemsg?>
							</div>
						</div>
					</div>
				</form>
				<?=$savemsg?>

				<?/*11.02.2014,gln: Anzeige Lieferadressen */?>
				<div class="panel panel-default">
					  <div class="panel-heading">
							<h3 class="panel-title">
								Lieferadressen
								<span class="glyphicons glyphicons-user-add pointer"
									onclick="document.getElementById('neue_lieferadr').style.display=''"
									 title="<?=$_LANG->get('Neue Lieferadresse anlegen');?>">
								</span>
							</h3>
					  </div>
						  <div class="table-responsive">
							  <table class="table table-hover" id="table_deliv">
								  <thead>
									  <tr>
										  <th class="content_row"><b>Firma</b></th>
										  <th class="content_row"><b>Adresse</b></th>
										  <th class="content_row"><b>PLZ/Ort</b></th>
										  <th class="content_row"><b>Land</b></th>
										  <th class="content_row"><b>Telefon</b></th>
									  </tr>
								  </thead>
								  <?	foreach($all_deliveryAddresses AS $deliv){ ?>
									  <tr>
										  <td>
											  <?=$deliv->getNameAsLine()?>
											  <? if ($deliv->getDefault() == 1) echo ' (Standard)'; ?>
										  </td>
										  <td>
											  <?=$deliv->getAddress1()?> <?=$deliv->getAddress2()?>
										  </td>
										  <td>
											  <?=$deliv->getZip()?> <?=$deliv->getCity()?>
										  </td>
										  <td>
											  <?$c=new Country($deliv->getCountry()->getId()); echo $c->getName()?>
										  </td>
										  <td>
											  <?=$deliv->getPhone()?>
										  </td>
									  </tr>
								  <?	} ?>
							  </table>
						  </div>
				</div>


				<div class="panel panel-default">
					  <div class="panel-heading">
							<h3 class="panel-title">
								Rechnungsadressen
								<span class="glyphicons glyphicons-user-add pointer"
									  onclick="document.getElementById('neue_rechadr').style.display='' "
									  title="<?=$_LANG->get('Neue Rechnungsadresse anlegen');?>">
								</span>
							</h3>
					  </div>
						  <div class="table-responsive">
							  <table class="table table-hover" id="table_inv">
								  <thead>
									  <tr>
										  <th class="content_row"><b>Firma</b></th>
										  <th class="content_row"><b>Adresse</b></th>
										  <th class="content_row"><b>PLZ/Ort</b></th>
										  <th class="content_row"><b>Land</b></th>
										  <th class="content_row"><b>Telefon</b></th>
									  </tr>
								  </thead>
								  <?	foreach($all_invoiceAddresses AS $invoiceadr){ ?>
									  <tr>
										  <td>
											  <?=$invoiceadr->getNameAsLine()?>
											  <? if ($invoiceadr->getDefault() == 1) echo ' (Standard)'; ?>
										  </td>
										  <td>
											  <?=$invoiceadr->getAddress1()?> <?=$invoiceadr->getAddress2()?>
										  </td>
										  <td>
											  <?=$invoiceadr->getZip()?> <?=$invoiceadr->getCity()?>
										  </td>
										  <td>
											  <?$c=new Country($invoiceadr->getCountry()->getId()); echo $c->getName()?>
										  </td>
										  <td>
											  <?=$invoiceadr->getPhone()?>
										  </td>
									  </tr>
								  <?	} ?>
							  </table>
						  </div>
				</div>
		  </div>
	</div>



