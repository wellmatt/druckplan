<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
// require_once('libs/modules/organizer/nachricht.class.php');
require_once ('libs/modules/tickets/ticket.class.php');
require_once ('libs/modules/commissioncontact/commissioncontact.class.php');

global $_CONFIG;
$_USER;

$_REQUEST["id"] = (int)$_REQUEST["id"];
$businessContact = new BusinessContact($_REQUEST["id"]);

$all_attributes = Attribute::getAllAttributesForCustomer();

// Nachricht senden, dann Speichern
if($_REQUEST["subexec"] == "send"){
	$send_mail = true;
	// Damit nach dem Senden auch gespeichert wird
	$_REQUEST["subexec"] = "save";
}

if ($_REQUEST["subexec"] == "save")
{
	if ($_REQUEST["subform"] == "user_details"){ //Form von Tab1 auslesen
		
	}
	if ($_REQUEST["subform"] == "web_login"){ // Form von Tab 4 auslesen
	
	}
	if ($_REQUEST["supplier"]==""){
		$_REQUEST["supplier"]=0;
	}
    if ($_REQUEST["commissionpartner"]==""){
        $_REQUEST["commissionpartner"]=0;
    }
	
	$positiontitles = Array();
	foreach($_REQUEST['position_titles'] as $position_title)
	{
		$positiontitles[] = $position_title;
	}
	
    $businessContact->setActive(1);
    $businessContact->setCommissionpartner(trim(addslashes($_REQUEST["commissionpartner"])));
    $businessContact->setcustomer(trim(addslashes($_REQUEST["customer"])));
    $businessContact->setSupplier(trim(addslashes($_REQUEST["supplier"])));
    $businessContact->setName1(trim(addslashes($_REQUEST["name1"])));
    $businessContact->setName2(trim(addslashes($_REQUEST["name2"])));
    $businessContact->setAddress1(trim(addslashes($_REQUEST["address1"])));
    $businessContact->setAddress2(trim(addslashes($_REQUEST["address2"])));
    $businessContact->setZip(trim(addslashes($_REQUEST["zip"])));
    $businessContact->setCity(trim(addslashes($_REQUEST["city"])));
    $businessContact->setCountry(new Country (trim(addslashes($_REQUEST["country"]))));
    
    $businessContact->setEmail(trim(addslashes($_REQUEST["email"])));
    $businessContact->setPhone(trim(addslashes($_REQUEST["phone"])));
    $businessContact->setFax(trim(addslashes($_REQUEST["fax"])));
    $businessContact->setWeb(trim(addslashes($_REQUEST["web"])));
    $businessContact->setClient(new Client((int)$_REQUEST["client"]));
    $businessContact->setLanguage(new Translator((int)$_REQUEST["language"]));
    $businessContact->setDiscount((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["discount"]))));
    $businessContact->setPaymentTerms(new PaymentTerms((int)$_REQUEST["payment"]));
    //$businessContact->setComment(trim(addslashes($_REQUEST["comment"])));
    $businessContact->setNumberatcustomer(trim(addslashes($_REQUEST["numberatcustomer"])));
    $businessContact->setCustomernumber(trim(addslashes($_REQUEST["customernumber"])));
    $businessContact->setKreditor((int)($_REQUEST["kreditor"]));
    $businessContact->setDebitor((int)($_REQUEST["debitor"]));
    $businessContact->setBic(trim(addslashes($_REQUEST["bic"])));
    $businessContact->setIban(trim(addslashes($_REQUEST["iban"])));
    
    if ($_REQUEST["notifymailadr"]){
        $tmp_array_notify_adr = Array();
        foreach ($_REQUEST["notifymailadr"] as $tmp_notify_adr){
            if ($_REQUEST["notifymailadr"] != "")
                $tmp_array_notify_adr[] = $tmp_notify_adr;
        }
    }
    
    $businessContact->setNotifymailadr($tmp_array_notify_adr);
    
    $businessContact->setShoplogin(trim(addslashes($_REQUEST["shop_login"])));
    $businessContact->setShoppass(trim(addslashes($_REQUEST["shop_pass"])));
    $businessContact->setTicketenabled((int)$_REQUEST["ticket_enabled"]);
    $businessContact->setPersonalizationenabled((int)$_REQUEST["personalization_enabled"]);
    $businessContact->setArticleenabled((int)$_REQUEST["article_enabled"]);
    if ((int)$_REQUEST["login_expire"] != 0){
    	$_REQUEST["login_expire"] = explode(".", $_REQUEST["login_expire"]);
    	$businessContact->setLoginexpire((int)mktime(12, 0, 0, $_REQUEST["login_expire"][1], $_REQUEST["login_expire"][0], $_REQUEST["login_expire"][2]));
    } else {
    	$businessContact->setLoginexpire(0);
    }
    
    $businessContact->setAlt_name1(trim(addslashes($_REQUEST["alt_name1"])));
    $businessContact->setAlt_name2(trim(addslashes($_REQUEST["alt_name2"])));
    $businessContact->setAlt_address1(trim(addslashes($_REQUEST["alt_address1"])));
    $businessContact->setAlt_address2(trim(addslashes($_REQUEST["alt_address2"])));
    $businessContact->setAlt_zip(trim(addslashes($_REQUEST["alt_zip"])));
    $businessContact->setAlt_city(trim(addslashes($_REQUEST["alt_city"])));
    $businessContact->setAlt_country(new Country (trim(addslashes($_REQUEST["alt_country"]))));
    $businessContact->setAlt_email(trim(addslashes($_REQUEST["alt_email"])));
    $businessContact->setAlt_phone(trim(addslashes($_REQUEST["alt_phone"])));
    $businessContact->setAlt_fax(trim(addslashes($_REQUEST["alt_fax"])));
    
    $businessContact->setPriv_name1(trim(addslashes($_REQUEST["priv_name1"])));
    $businessContact->setPriv_name2(trim(addslashes($_REQUEST["priv_name2"])));
    $businessContact->setPriv_address1(trim(addslashes($_REQUEST["priv_address1"])));
    $businessContact->setPriv_address2(trim(addslashes($_REQUEST["priv_address2"])));
    $businessContact->setPriv_zip(trim(addslashes($_REQUEST["priv_zip"])));
    $businessContact->setPriv_city(trim(addslashes($_REQUEST["priv_city"])));
    $businessContact->setPriv_country(new Country (trim(addslashes($_REQUEST["priv_country"]))));
    $businessContact->setPriv_email(trim(addslashes($_REQUEST["priv_email"])));
    $businessContact->setPriv_phone(trim(addslashes($_REQUEST["priv_phone"])));
    $businessContact->setPriv_fax(trim(addslashes($_REQUEST["priv_fax"])));
    
    $businessContact->setType((int)$_REQUEST["cust_type"]);
    $businessContact->setBedarf((int)$_REQUEST["cust_bedarf"]);
    $businessContact->setProdukte((int)$_REQUEST["cust_produkte"]);
    $businessContact->setBranche((int)$_REQUEST["cust_branche"]);
   
    $businessContact->setPositionTitles($positiontitles);
    
    $businessContact->setMatchcode($_REQUEST["matchcode"]);
    $businessContact->setSupervisor(new User((int)$_REQUEST["supervisor"]));
    $businessContact->setSalesperson(new User((int)$_REQUEST["salesperson"]));
    $businessContact->setTourmarker($_REQUEST["tourmarker"]);
    $businessContact->setNotes($_REQUEST["notes"]);
   
    $savemsg = getSaveMessage($businessContact->save());
	if($DB->getLastError()!=NULL && $DB->getLastError()!=""){
    	$savemsg .= $DB->getLastError();
    }
    
    if (count(Address::getAllAddresses($businessContact,Address::ORDER_NAME,Address::FILTER_INVC)) < 1) {
        if ($_REQUEST["name1"] && $_REQUEST["address1"] && $_REQUEST["zip"] && $_REQUEST["city"] && $_REQUEST["country"]) {
            $address = new Address();
            $address->setActive(1);
            $address->setName1(trim(addslashes($_REQUEST["name1"])));
            $address->setName2(trim(addslashes($_REQUEST["name2"])));
            $address->setAddress1(trim(addslashes($_REQUEST["address1"])));
            $address->setAddress2(trim(addslashes($_REQUEST["address2"])));
            $address->setZip(trim(addslashes($_REQUEST["zip"])));
            $address->setCity(trim(addslashes($_REQUEST["city"])));
            $address->setPhone(trim(addslashes($_REQUEST["phone"])));
            $address->setFax(trim(addslashes($_REQUEST["fax"])));
            $address->setCountry(new Country (trim(addslashes($_REQUEST["country"]))));
            $address->setBusinessContact($businessContact);
            $address->setDefault(1);
            $address->save();
        }
    }
    if (count(Address::getAllAddresses($businessContact,Address::ORDER_NAME,Address::FILTER_DELIV)) < 1) {
        if ($_REQUEST["name1"] && $_REQUEST["address1"] && $_REQUEST["zip"] && $_REQUEST["city"] && $_REQUEST["country"]) {
            $address = new Address();
            $address->setActive(2);
            $address->setName1(trim(addslashes($_REQUEST["name1"])));
            $address->setName2(trim(addslashes($_REQUEST["name2"])));
            $address->setAddress1(trim(addslashes($_REQUEST["address1"])));
            $address->setAddress2(trim(addslashes($_REQUEST["address2"])));
            $address->setZip(trim(addslashes($_REQUEST["zip"])));
            $address->setCity(trim(addslashes($_REQUEST["city"])));
            $address->setPhone(trim(addslashes($_REQUEST["phone"])));
            $address->setFax(trim(addslashes($_REQUEST["fax"])));
            $address->setCountry(new Country (trim(addslashes($_REQUEST["country"]))));
            $address->setShoprel(1);
            $address->setBusinessContact($businessContact);
            $address->setDefault(1);
            $address->save();
        }
    }
    
    // Merkmale speichern
    $businessContact->clearAttributes();	// Erstmal alle loeschen und dann nur aktive neu setzen
    $save_attributes = Array();
    $i=1;
    foreach ($all_attributes AS $attribute){
		$allitems = $attribute->getItems();
		foreach ($allitems AS $item){
			if((int)$_REQUEST["attribute_item_check_{$attribute->getId()}_{$item["id"]}"] == 1){
			    if($item["input"] == 1 && $_REQUEST["attribute_item_input_{$attribute->getId()}_{$item["id"]}"] != "" || $item["input"] == 0)
			    {
    				$tmp_attribute["id"] = 0;
    				$tmp_attribute["value"] = 1;
    				$tmp_attribute["attribute_id"] = $attribute->getId();
    				$tmp_attribute["item_id"] = $item["id"];
    				$tmp_attribute["inputvalue"] = $_REQUEST["attribute_item_input_{$attribute->getId()}_{$item["id"]}"];
    				$save_attributes[] = $tmp_attribute;
    				$i++;
			    }
			}
		}
	}
	$businessContact->saveActiveAttributes($save_attributes);
}

if($businessContact->getId()){
	$contactPersons = ContactPerson::getAllContactPersons($businessContact,ContactPerson::ORDER_NAME, "", ContactPerson::LOADER_BASIC);
	$deliveryAddresses = Address::getAllAddresses($businessContact,Address::ORDER_NAME,Address::FILTER_DELIV);
	$invoiceAddresses = Address::getAllAddresses($businessContact,Address::ORDER_NAME,Address::FILTER_INVC);
    $ticketcount = Ticket::getAllTicketsCount(" WHERE customer = {$businessContact->getId()} AND state > 1 ");
}

$show_tab=(int)$_REQUEST["tabshow"];
$languages = Translator::getAllLangs(Translator::ORDER_NAME);
$countries = Country::getAllCountries();
$all_active_attributes = $businessContact->getActiveAttributeItemsInput();
$all_user = User::getAllUser(User::ORDER_NAME);


/**************************************************************************
 ******* 				Java-Script									*******
 *************************************************************************/
?>

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css"
	href="css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8"
	src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8"
	src="jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8"
	src="jscripts/datatable/dataTables.bootstrap.js"></script>
<script type="text/javascript" charset="utf8"
	src="jscripts/datatable/date-uk.js"></script>
	
<div id="tktc_hidden_clicker" style="display:none"><a id="tktc_hiddenclicker" href="http://www.google.com" >Hidden Clicker</a></div>

<script type="text/javascript">
function callBoxFancytktc(my_href) {
	var j1 = document.getElementById("tktc_hiddenclicker");
	j1.href = my_href;
	$('#tktc_hiddenclicker').trigger('click');
}
$(document).ready(function() {
    var search_tickets = $('#comment_table').DataTable( {
        "processing": true,
        "bServerSide": true,
        "sAjaxSource": "libs/modules/businesscontact/businesscontact.comments.dt.ajax.php?bcid=<?php echo $businessContact->getId();?>&access=<?php if ($_USER->hasRightsByGroup(Group::RIGHT_NOTES_BC) || $_USER->isAdmin()) echo '1'; else echo '0';?>&userid=<?php echo $_USER->getId();?>",
		"stateSave": false,
		"pageLength": 10,
		"dom": 'flrtip',
		"aaSorting": [[ 1, "desc" ]],
		"lengthMenu": [ [10, 25, 50, 100, 250, -1], [10, 25, 50, 100, 250, "Alle"] ],
		"columns": [
		    		{
                        "className":      'details-control',
                        "orderable":      false,
                        "data":           null,
                        "defaultContent": ''
                    },
		            { "searchable": false},
		            { "searchable": true},
		            { "searchable": true},
		            { "searchable": false},
		            { "searchable": false},
		            { "visible": false}
		          ],
        "language": {
                    	"url": "jscripts/datatable/German.json"
          	        }
    } );
    $("#comment_table tbody td:not(:first-child)").live('click',function(){
        var aPos = $('#comment_table').dataTable().fnGetPosition(this);
        var aData = $('#comment_table').dataTable().fnGetData(aPos[0]);
        callBoxFancytktc('libs/modules/comment/comment.edit.php?cid='+aData[1]+'&tktid=0');
    });

	$("a#tktc_hiddenclicker").fancybox({
		'type'          :   'iframe',
		'transitionIn'	:	'elastic',
		'transitionOut'	:	'elastic',
		'speedIn'		:	600,
		'speedOut'		:	200,
		'width'         :   1024,
		'height'		:	768,
		'overlayShow'	:	true,
		'helpers'		:   { overlay:null, closeClick:true }
	});

	$('#comment_table tbody').on('click', 'tr td:first-child', function () {
        var tr = $(this).closest('tr');
        var row = search_tickets.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Open this row
            row.child( child_comment(row.data()) ).show();
            tr.addClass('shown');
        }
    } );

	function child_comment ( d ) {
	    // `d` is the original data object for the row
	    return '<div class="box2">'+d[6]+'</div>';
	}
} );
</script>

 <script>
$(function() {
   $( "#name1" ).autocomplete({
        delay: 0,
        source: 'libs/modules/businesscontact/businesscontact.ajax.autocomplete.php',
		minLength: 4,
		dataType: "json",
		select: function( event, ui ) {return false;},
	    focus: function( event, ui ) {return false;}
    });
});
</script>

<script>
	$(function() {
		$( "#tabs" ).tabs({ selected: <?=$show_tab?> });
	});
</script>


<script language="javascript">
function checkpass(obj){
	return checkform(obj);
}

function dialNumber(link){
	$.get('http://'+link, //{exec: 'dial_number', link: link},
			function(data) {
				alert('Nummer gesendet.');
			}
	);
}

$(function() {
	$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);

	$('#login_expire').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
                showOn: "button",
                buttonImage: "images/icons/calendar-blue.png",
                buttonImageOnly: true
			}
     );
});

<? /*************************************
	* Liefert eine neue Debitoren-Nr.
	***********************************/ ?>
function generadeDebitorNumber(){
	$.post("libs/modules/businesscontact/businesscontact.ajax.php", {exec: 'generadeDebitorNr'}, function(data) {
		// Work on returned data
		document.getElementById('debitor').value= data;
	});
}

<? /*************************************
	* Liefert eine neue Debitoren-Nr.
	***********************************/ ?>
function generadeCreditorNumber(){
	$.post("libs/modules/businesscontact/businesscontact.ajax.php", {exec: 'generadeCreditorNr'}, function(data) {
		// Work on returned data
		document.getElementById('kreditor').value= data;
	});
}

<? /*************************************
	* Liefert eine neue Debitoren-Nr.
	***********************************/ ?>
function generadeCustomerNumber(){
	$.post("libs/modules/businesscontact/businesscontact.ajax.php", {exec: 'generadeCustomerNr'}, function(data) {
		// Setzen der neu-generierten Nummer
		document.getElementById('customernumber').value= data;
	});
}

<? /*******************************************************
	* Prueft, ob die Kundennummer bereits vergeben
	******************************************************/ ?>
function checkCustomerNumber(obj){
	var thisnumber = '<?=$businessContact->getCustomernumber()?>';
	var newnumber  = document.getElementById('customernumber').value;

	<?//Erst ueberpruefen ob Art-Nr leer ist, dann ob vorhanden?>
	if (newnumber == "" || parseInt(newnumber) == 0){
		return checkform(obj);
	}

	if (thisnumber != newnumber){
		$.post("libs/modules/businesscontact/businesscontact.ajax.php",
				{exec: 'checkCustomerNumber', newnumber : newnumber},
				 function(data) {
					 data = data.substring(0,2);
					if(data == "DA"){
						alert('<?=$_LANG->get('Kundennummer bereits vergeben!') ?>');
						document.getElementById('customernumber').focus();
						return false;
					} else {
						if (checkform(obj)==true){
							document.getElementById('user_form').submit();
						}
					}
				});
	} else {
		return checkform(obj);
	}
	return false;
}

function addTitlePosition(){
	var count = parseInt(document.getElementById('position_titles_count').value) + 1;
	var obj = document.getElementById('table_positiontitles');
	var insert = '<tr id="tr_positiontitle_'+count+'"><td colspan="3"><div class="col-md-5"><input type="text" style="width:350px" class="form-control" name="position_titles[]"></div><div class="col-md-2"><span class="glyphicons glyphicons-remove" onclick="removeTitlePosition(this)" alt="'+count+'"></span></div></td></tr>';
	obj.insertAdjacentHTML("BeforeEnd", insert);
	document.getElementById('position_titles_count').value = count;
}

function removeTitlePosition(item){
	var id = parseInt(item.alt);
	document.getElementById('tr_positiontitle_'+id).remove();
	var count = parseInt(document.getElementById('position_titles_count').value) - 1;
	document.getElementById('position_titles_count').value = count;
}

function commi_checkbox(){
  if(document.getElementById('has_commissionpartner').checked){
	document.getElementById('commi_title').style.display= '';


	document.getElementById('commissionpartner').style.display= '';
  }
  else {
	document.getElementById('commi_title').style.display= 'none';
	document.getElementById('commissionpartner').style.display= 'none';
  }
}
</script>
<?
/**************************************************************************
 ******* 				HTML-Bereich								*******
 *************************************************************************/?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
if ($_REQUEST["returntosales"] == 1)
	$quickmove->addItem('Zurück','index.php?page=libs/modules/salescontacts/salescontacts.overview.php',null,'glyphicon-step-backward');
else
	$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');

if ($_USER->hasRightsByGroup(Group::RIGHT_EDIT_BC) || $_USER->isAdmin()) {
	$quickmove->addItem('Speichern', '#', "$('#user_form').submit();", 'glyphicon-floppy-disk');
}
if($_USER->hasRightsByGroup(Group::RIGHT_DELETE_BC) || $_USER->isAdmin()){
	if($_REQUEST["exec"] != "new"){
		$quickmove->addItem('Löschen', '#',  "askDel('index.php?page=".$_REQUEST['page']."&exec=delete&id=".$businessContact->getId()."');", 'glyphicon-trash', true);
}
}
echo $quickmove->generate();
// end of Quickmove generation ?>


<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="user_form" id="user_form" enctype="multipart/form-data"
	onSubmit="return checkCustomerNumber(new Array(this.name1));" >
	<?// gucken, ob die Passwoerter (Webshop-Login) gleich sind und ob alle notwendigen Felder gef�llt sind?>

	<input type="hidden" name="exec" value="edit">
	<input type="hidden" name="subexec" id="subexec" value="save">
	<input type="hidden" name="subform" value="user_details">
	<input type="hidden" name="id" value="<?=$businessContact->getId()?>">

<div class="demo">
	<div id="tabs">
		<ul>
			<li><a href="#tabs-0"><? echo $_LANG->get('&Uuml;bersicht');?></a></li>
			<?php if ($_USER->getBCshowOnlyOverview() == 0){?>
			<li><a href="#tabs-1"><? echo $_LANG->get('Stammdaten');?></a></li>
			<li><a href="#tabs-5"><? echo $_LANG->get('Merkmale');?></a></li>
			<li><a href="#tabs-2"><? echo $_LANG->get('Adressen');?></a></li>
			<li><a href="#tabs-3"><? echo $_LANG->get('Ansprechpartner');?></a></li>
			<?php if($_USER->hasRightsByGroup(Group::RIGHT_DELETE_BC) || $_USER->isAdmin()){?>
		    <li><a href="#tabs-12"><? echo $_LANG->get('Notizen');?><?php if ($businessContact->getId()) echo ' <span id="notify_count" class="badge">'.Comment::getCommentCountForObject("BusinessContact", $businessContact->getId()).'</span>';?></a></li>
            <?php } ?>
			<li><a href="#tabs-7"><? echo $_LANG->get('Tickets');?><?php if ($businessContact->getId()) echo ' <span id="notify_count" class="badge">'.$ticketcount.'</span>';?></a></li>
			<li><a href="#tabs-8"><? echo $_LANG->get('Personalisierung');?></a></li>
			<li><a href="#tabs-9"><? echo $_LANG->get('Rechnungsausgang');?></a></li>
			<li><a href="#tabs-10"><? echo $_LANG->get('Rechnungseingang');?></a></li>
			<li><a href="#tabs-11"><? echo $_LANG->get('Vorg&auml;nge');?></a></li>
			<?php } ?>
		</ul>

		<? // ---------------------------- Uebesicht ueber den Geschaeftskontakt --------------------------?>

	<div id="tabs-0">
	<?php if ($businessContact->getId()>0){?>
    <div style="text-align: right;"><a href="libs/modules/businesscontact/businesscontact.print.card.php?id=<?php echo $businessContact->getId();?>" target="_blank"><span class="glyphicons glyphicons-print"></span></a></div>
    <?php }?>
		<br>
		<table width="100%">
		<tr>
			<td width="700" class="content_header">
			    <h3><?=$businessContact->getNameAsLine() ?><small><span style="display: inline-block; vertical-align: top;" class="
			    <?php
			    if($businessContact->isExistingCustomer())
			    {
			        echo "label label-success";
			    } else if($businessContact->isPotentialCustomer())
			    {
			        echo "label label-info";
			    }
			    ?>">
			    <?php
			    if($businessContact->isExistingCustomer())
			    {
			        echo $_LANG->get('Bestandskunde');
			    } else if($businessContact->isPotentialCustomer())
			    {
			        echo $_LANG->get('Interessent');
			    }
			    ?>
			    </span></small>
			    <?php
			    if($businessContact->isSupplier())
			    {
			        echo '<small><span style="display: inline-block; vertical-align: top;" class="label label-warning">Lieferant</span></small>';
			    }
			    ?>
			    </h3>
			</td>
			<td align="left"></td>
			<td width="200" class="content_header" align="right"><?=$savemsg?></td>
		</tr>
		</table>

		<table width="100%">
			<colgroup>
			<col width="750">
			<col>
			</colgroup>
			<tr>
			<td valign="top">
				<table width="100%">
						<colgroup>
							<col width="350">
							<col>
						</colgroup>
						<tr>
							<td class="content_row_clear" valign="top">
								<?=$businessContact->getAddress1()?> <?=$businessContact->getAddress2()?> <br>
								<?=$businessContact->getZip()?> <?=$businessContact->getCity()?><br>
								<?=$businessContact->getCountry()->getName()?>
							</td>
							<td class="content_row_clear" valign="top">
								<span  onClick="dialNumber('<?=$_USER->getTelefonIP()?>/command.htm?number=<?=$businessContact->getPhoneForDial()?>')"
									title="<?=$businessContact->getPhoneForDial()." ".$_LANG->get('anrufen');?>" class="pointer icon-link">
									<span class="glyphicons glyphicons-earphone"></span>&nbsp;<?=$businessContact->getPhone()?>
								</span>
								<br>
								<span class="glyphicons glyphicons-fax"></span>&nbsp;<?=$businessContact->getFax()?><br>
								<span class="glyphicons glyphicons-globe-af"></span>
									<a class="icon-link" href="<?=$businessContact->getWebForHref()?>" target="_blank"><?=$businessContact->getWeb()?></a> <br>
								<span class="glyphicons glyphicons-envelope"></span>&nbsp;<?=$businessContact->getEmail()?>
							</td>
						</tr>
						<tr>
							<td class="content_row_clear">&ensp;</td>
						</tr>
						<tr>
							<td class="content_row_clear" valign="top">
							<?	if ($businessContact->getCustomernumber() != NULL && $businessContact->getCustomernumber() != ""){
									echo $_LANG->get('Kundennummer').": ".$businessContact->getCustomernumber(). " <br/>";
								} ?>
							<?	if ($businessContact->getNumberatcustomer() != NULL && $businessContact->getNumberatcustomer() != ""){
									echo $_LANG->get('Unsere KD-Nr.').": ".$businessContact->getNumberatcustomer(). " <br/>";
								} ?>
							<?	if ($businessContact->getMatchcode() != NULL && $businessContact->getMatchcode() != ""){
									echo $_LANG->get('Matchcode').": ".$businessContact->getMatchcode(). " <br/>";
								} ?>
							<?	if ($businessContact->getSupervisor()->getId()>0){
									echo $_LANG->get('Betreuer Web').": ".$businessContact->getSupervisor()->getNameAsLine(). " <br/>";
								} ?>
							<?	if ($businessContact->getSalesperson()->getId()>0){
									echo $_LANG->get('Betreuer Vertrieb').": ".$businessContact->getSalesperson()->getNameAsLine(). " <br/>";
								} ?>
							<?	if ($businessContact->getTourmarker()){
									echo $_LANG->get('Tourenmerkmal').": ".$businessContact->getTourmarker(). " <br/>";
								} ?>
							</td>
							<td class="content_row_clear" valign="top">
								<?=$_LANG->get('Tickets:');?>:
								<?if ($businessContact->getTicketenabled() == 1) echo $_LANG->get('AN'); else echo $_LANG->get('AUS'); ?> <br/>
								<?=$_LANG->get('Personalisierung:');?>:
								<?if ($businessContact->getPersonalizationenabled() == 1) echo $_LANG->get('AN'); else echo $_LANG->get('AUS'); ?> <br/>
								<?=$_LANG->get('Artikel:');?>:
								<?if ($businessContact->getArticleenabled() == 1) echo $_LANG->get('AN'); else echo $_LANG->get('AUS'); ?> <br/>
								</br>
								<?if ($businessContact->getNotes()!="") echo "Bemerkung:</br>".$businessContact->getNotes();?></br>
							</td>
						</tr>
						<tr>
							<td class="content_row_clear" valign="top">
							<?	foreach ($all_attributes AS $attribute){
									$tmp_output = "<b>".$attribute->getTitle().":</b> ";
									$allitems = $attribute->getItems();
									$j=0;
									foreach ($allitems AS $item){
										if ($all_active_attributes["{$attribute->getId()}_{$item["id"]}"]["value"] == 1){
											$tmp_output .= $item["title"];
											if ($all_active_attributes["{$attribute->getId()}_{$item["id"]}"]["inputvalue"] != ""){
											    $tmp_output .= ": '".$all_active_attributes["{$attribute->getId()}_{$item["id"]}"]["inputvalue"]."'";
											}
											$tmp_output .= ", ";
											$j++;
										}
									}
									$tmp_output = substr($tmp_output, 0, -2); // Letztes Komma entfernen
									if($j>0){
										echo $tmp_output.= "<br/>";
									}
								}?>
							</td>
						</tr>
				</table>
			</td>
			<td valign="top">
			&ensp;<b><?=$_LANG->get('Ansprechpartner');?></b>
			<br/><br/>
			<table>
				<colgroup>
					<col width="200">
					<col>
				</colgroup>
			<?	foreach($contactPersons as $cp) {
					$phone = $cp->getPhoneForDial("n");
					$mobilephone = $cp->getPhoneForDial("m");?>
					<tr <?if($cp->isMainContact())echo 'style="font-weight:bold;"'?>>
						<td class="content_row_clear">
							<?php echo $cp->getNameAsLine(); ?>
						</td>
						<td class="content_row_clear">
							<?	$contact_attributes = Attribute::getAllAttributesForContactperson();
								$active_cust_attributes= $cp->getActiveAttributeItemsInput();
								foreach ($contact_attributes AS $attribute){
									$allitems = $attribute->getItems();
									$j=0; $tmp_output = "";
									foreach ($allitems AS $item){
										if ($active_cust_attributes["{$attribute->getId()}_{$item["id"]}"]["value"] == 1){
											$tmp_output .= $item["title"];
											if ($active_cust_attributes["{$attribute->getId()}_{$item["id"]}"]["inputvalue"] != ""){
											    $tmp_output .= ": '".$active_cust_attributes["{$attribute->getId()}_{$item["id"]}"]["inputvalue"]."'";
											}
											$tmp_output .= ", ";
											$j++;
										}
									}
									$tmp_output = substr($tmp_output, 0, -2); // Letztes Komma entfernen
									if($j>0 && $attribute->getId() == 21){ 		// An dieser STelle nur Attribut "Funktion" ausgeben
										echo $tmp_output;
									}
								}?>
						</td>
						<td class="content_row_clear">
							<!-- index.php?exec=edit&id=<?=$businessContact->getID()?>&subexec=phone -->
							<table>
								<colgroup>
								<col width="25">
								<col width="25">
								<col width="25">
								</colgroup>
								<tr>
									<td>
										<?if ($phone != false){?>
										<a class="icon-link" href="#" title="<?=$phone." ".$_LANG->get('anrufen');?>"
											onClick="dialNumber('<?=$_USER->getTelefonIP()?>/command.htm?number=<?=$phone?>')"
											><span class="glyphicons glyphicons-earphone"></span></a>
										<?}?>
									</td>
									<td>
										<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=phone" title="<?=$cp->getEmail();?>"><span class="glyphicons glyphicons-envelope"></span></a>
									</td>
									<td>
										<?if ($mobilephone != false){?>
										<a class="icon-link" href="#" title="<?=$mobilephone." ".$_LANG->get('anrufen');?>"
											onClick="dialNumber('<?=$_USER->getTelefonIP()?>/command.htm?number=<?=$mobilephone?>')"
										 ><span class="glyphicons glyphicons-iphone"></span></a>
										<?}?>
									</td>
								</tr>
							</table>
						</td>
			        </tr>
	        <?	}  ?>
			</table>
			</td>
			</tr>
		</table>
	</div>

	<?php if ($_USER->getBCshowOnlyOverview() == 0){?>
	<? /* ------------------------------------- STAMMDATEN ------------------------------------------------------ */ ?>

	<div id="tabs-1">
		<div class="panel panel-default">
			  <div class="panel-heading">
					<h3 class="panel-title">
						<? if ($businessContact->getId()) echo $_LANG->get('Gesch&auml;ftskontakt &auml;ndern'); else echo $_LANG->get('Gesch&auml;ftskontakt hinzuf&uuml;gen');?>
						<span class="pull-right">
							<?=$savemsg?>
						</span>
					</h3>
			  </div>
			  <div class="panel-body">
					<div class="form-horizontal">
						 <div class="row">
							 <div class="col-md-6">
								 <div class="form-group">
									 <label for="" class="col-sm-5 control-label">Adresse (allgemein)</label>
								 </div>
								 </br>
								 <div class="form-group">
									 <label for="" class="col-sm-3 control-label">Firma</label>
									 <div class="col-sm-9">
										 <input name="name1" id="name1" value="<?=$businessContact->getName1()?>" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-3 control-label">Firmenzusatz</label>
									 <div class="col-sm-9">
										 <input name="name2" class="form-control" value="<?=$businessContact->getName2()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-3 control-label">Adresse</label>
									 <div class="col-sm-9">
										 <input name="address1" class="form-control" value="<?=$businessContact->getAddress1()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-3 control-label">Adresszusatz</label>
									 <div class="col-sm-9">
										 <input name="address2" class="form-control" value="<?=$businessContact->getAddress2()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-3 control-label">Postleitzahl</label>
									 <div class="col-sm-9">
										 <input name="zip" class="form-control" value="<?=$businessContact->getZip()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-3 control-label">Stadt</label>
									 <div class="col-sm-9">
										 <input name="city" class="form-control" value="<?=$businessContact->getCity()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-3 control-label">Land</label>
									 <div class="col-sm-9">
										 <select name="country" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
											 <?
											 foreach($countries as $c)
											 {?>
												 <option value="<?=$c->getId()?>"
													 <?if ($businessContact->getCountry()->getId() == $c->getId()) echo "selected";?>>
													 <?=$c->getName()?>
												 </option>
											 <?}

											 ?>
										 </select>
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-3 control-label">Telefon</label>
									 <div class="col-sm-9">
										 <input name="phone" class="form-control" value="<?=$businessContact->getPhone()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-3 control-label">Fax</label>
									 <div class="col-sm-9">
										 <input name="fax" class="form-control" value="<?=$businessContact->getFax()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-3 control-label">E-Mail</label>
									 <div class="col-sm-9">
										 <input name="email" class="form-control" value="<?=$businessContact->getEmail()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-3 control-label">Internetseite</label>
									 <div class="col-sm-9">
										 <input name="web" class="form-control" value="<?=$businessContact->getWeb()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-3 control-label">Bemerkung</label>
									 <div class="col-sm-9">
										 <textarea name="notes" class="form-control" rows="2" onfocus="markfield(this,0)" onblur="markfield(this,1)"><?=$businessContact->getNotes()?></textarea>
									 </div>
								 </div>
							 </div>
							 <div class="col-md-6">
								 <div class="form-group">
									 <label for="" class="col-sm-4 control-label">Kundennummer</label>
									 <div class="col-sm-6">
										 <input class="form-control" name="customernumber" id="customernumber" value="<?=$businessContact->getCustomernumber()?>">
									 </div>
									 <div class="col-sm-2">
										 <span class="glyphicons glyphicons-unshare pointer" onclick="generadeCustomerNumber()" title="<?=$_LANG->get('Neue Kunden-Nr. erzeugen');?>"></span>
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-4 control-label">Betreuer Vertrieb</label>
									 <div class="col-sm-8">
										 <select name="salesperson" id="salesperson" class="form-control">
											 <option value="0"></option>
											 <?php
											 foreach ($all_user as $sup_user){
												 if ($businessContact->getSalesperson()->getId() == $sup_user->getId()){
													 echo '<option value="'.$sup_user->getId().'" selected>'.$sup_user->getNameAsLine().'</option>';
												 } else {
													 echo '<option value="'.$sup_user->getId().'">'.$sup_user->getNameAsLine().'</option>';
												 }
											 }
											 ?>
										 </select>
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-4 control-label">Matchcode</label>
									 <div class="col-sm-8">
										 <input class="form-control" name="matchcode" id="matchcode" value="<?=$businessContact->getMatchcode()?>">
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-4 control-label">Kunde</label>
									 <div class="col-sm-8">
										 <select name="customer" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
											 <option value="0" <? if(! ($businessContact->isExistingCustomer() && $businessContact->isPotentialCustomer())) echo "selected";?>></option>
											 <option value="1" <? if($businessContact->isExistingCustomer()) echo "selected";?>>
												 <?=$_LANG->get('Bestandskunde')?>
											 </option>
											 <option value="2" <? if($businessContact->isPotentialCustomer()) echo "selected";?>>
												 <?=$_LANG->get('Interessent')?>
											 </option>
										 </select>
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-4 control-label">Lieferant</label>
									 <div class="col-sm-2">
										 <input name="supplier" type="checkbox" class="form-control" value="1" onfocus="markfield(this,0)" onblur="markfield(this,1)"<? if ($businessContact->isSupplier()) echo "checked";?>>
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-4 control-label">Unsere KD-Nr.</label>
									 <div class="col-sm-8">
										 <input class="form-control" name="numberatcustomer" value="<?=$businessContact->getNumberatcustomer()?>">
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-4 control-label">Zahlungsart</label>
									 <div class="col-sm-8">
										 <select name="payment" class="form-control">
											 <option value="0" <? if ($businessContact->getPaymentTerms()->getId() == 0)
												 echo "selected"?> >
											 </option>
											 <?
											 foreach(PaymentTerms::getAllPaymentConditions(PaymentTerms::ORDER_NAME) as $pt)
											 {
												 echo '<option value="'.$pt->getId().'"';
												 if ($pt->getId() == $businessContact->getPaymentTerms()->getId()){
													 echo "selected";
												 }
												 echo'>'.$pt->getName().'</option>';
											 }
											 ?>
										 </select>
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-4 control-label">IBAN</label>
									 <div class="col-sm-8">
										 <input class="form-control" name="iban" value="<?=$businessContact->getIban()?>">
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-4 control-label">BIC</label>
									 <div class="col-sm-8">
										 <input class="form-control" name="bic" value="<?=$businessContact->getBic()?>">
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-4 control-label">Rabatt</label>
									 <div class="col-sm-8">
										 <div class="input-group">
											 <input class="form-control" name="discount" value="<?=printPrice($businessContact->getDiscount())?>">
											 <span class="input-group-addon">%</span>
										 </div>
									 </div>
								 </div>
								 <? if($businessContact->getLectorId() > 0) { ?>
								 <div class="form-group">
									 <label for="" class="col-sm-4 control-label"><span class="error"><?=$_LANG->get('Lector-Import')?>: </span></label>
									 <div class="col-sm-8">
										 ID: <?=$businessContact->getId()?>
									 </div>
								 </div>
								 <?  } ?>
								 <div class="form-group">
									 <label for="" class="col-sm-4 control-label">Kreditor-Nr.</label>
									 <div class="col-sm-6">
										 <input class="form-control" name="kreditor" id="kreditor" value="<?=$businessContact->getKreditor()?>">
									 </div>
									 <div class="col-sm-2">
										 <span class="glyphicons glyphicons-unshare pointer" onclick="generadeCreditorNumber()" title="<?=$_LANG->get('Neue Kreditoren-Nr. erzeugen');?>"></span>
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-4 control-label">Debitor-Nr.</label>
									 <div class="col-sm-6">
										 <input class="form-control" name="debitor" id="debitor" value="<?=$businessContact->getDebitor()?>">
									 </div>
									 <div class="col-sm-2">
										 <span class="glyphicons glyphicons-unshare pointer" onclick="generadeDebitorNumber()" title="<?=$_LANG->get('Neue Debitoren-Nr. erzeugen');?>"></span>
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-4 control-label">Provisionspartner</label>
									 <div class="col-sm-2">
										 <input name="has_commissionpartner" id="has_commissionpartner" type="checkbox" class="form-control" value="1"
											 <? if ($businessContact->getCommissionpartner()>0) echo "checked";?> onclick="commi_checkbox()">
									 </div>
								 </div>
								 <div  style="display: <? if (!$businessContact->getCommissionpartner()>0) echo 'none';?>" id="commi_title">
									 <label for="" class="col-sm-4 control-label"><?=$_LANG->get('Partner: ')?></label>
									 <div class="col-sm-8">
										 <select name="commissionpartner" class="form-control" id="commissionpartner">
											 <option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
											 <? $commissioncontacts = CommissionContact::getAllCommissionContacts(CommissionContact::ORDER_ID, CommissionContact::FILTER_ALL, CommissionContact::LOADER_BASIC);
											 foreach($commissioncontacts as $comcon)
											 {
												 echo '<option value="'.$comcon->getId().'" ';
												 if($businessContact->getCommissionpartner() == $comcon->getId()) echo 'selected="selected"';
												 echo '>'.$comcon->getName1().', '.$comcon->getCity().'</option>';
											 }?>
										 </select>
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-4 control-label">Betreuer Web</label>
									 <div class="col-sm-8">
										 <select name="supervisor" class="form-control" id="supervisor">
											 <option value="0"></option>
											 <?php
											 foreach ($all_user as $sup_user){
												 if ($businessContact->getSupervisor()->getId() == $sup_user->getId()){
													 echo '<option value="'.$sup_user->getId().'" selected>'.$sup_user->getNameAsLine().'</option>';
												 } else {
													 echo '<option value="'.$sup_user->getId().'">'.$sup_user->getNameAsLine().'</option>';
												 }
											 }
											 ?>
										 </select>
									 </div>
								 </div>
								 <div class="form-group">
									 <label for="" class="col-sm-4 control-label">Tourenmerkmal</label>
									 <div class="col-sm-8">
										 <input class="form-control" name="tourmarker" id="tourmarker" value="<?=$businessContact->getTourmarker()?>">
									 </div>
								 </div>

							 </div>
						 </div>
					</div>
			  </div>
		</div>
	</div>

	<? // -------------------------------- ADRESSEN -------------------------------------------------------?>

	<div id="tabs-2">
	<?if($businessContact->getId()){?>
		<div class="panel panel-default">
			  <div class="panel-heading">
					<h3 class="panel-title">
						Adressen
					</h3>
			  </div>

			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
					<tr>
						<th width="25%"><?php echo $_LANG->get('Rechnungsadresse');?></th>
						<th width="25%">&nbsp;</th>
						<th width="25%">&nbsp;</th>
						<th width="25%">
							<button class="btn btn-xs btn-success" type="button" onclick="document.location.href='index.php?page=<?=$_REQUEST['page']?>&exec=edit_ai&id=<?=$businessContact->getID()?>';">
								<span class="glyphicons glyphicons-plus"></span>
								<?=$_LANG->get('Rechnungsadresse hinzuf&uuml;gen')?>
							</button>
						</th>
					</tr>
					</thead>
					<?php $addressInvoice = Address::getAllAddresses($businessContact,Address::ORDER_NAME,Address::FILTER_INVC);
					foreach($addressInvoice as $ai)
					{
						?>
						<tbody>
						<tr>
							<td><? echo $ai->getName1() . ' ' . $ai->getName2();
								if ($ai->getDefault() == 1) echo ' (Standard)';?>
							</td>
							<td><? echo $ai->getAddress1() ." ". $ai->getAddress2();?></td>
							<td><? echo $ai->getZip()." ".$ai->getCity();?></td>
							<td>
								<a class="icon-link" href="index.php?page=<?=$_REQUEST['page']?>&exec=edit_ai&id_a=<?=$ai->getId()?>&id=<?=$businessContact->getID()?>"><span class="glyphicons glyphicons-pencil"></span></a>
							</td>
						</tr>
						</tbody>
						<?php

					}
					?>
				</table>
			</div>
			<?}?>

			<? if ($businessContact->getId()) { ?>
				<div class="table-responsive">
					<table class="table table-hover">
						<thead>
						<tr>
							<th width="25%"><?php echo $_LANG->get('Lieferadresse');?></th>
							<th width="25%">&nbsp;</th>
							<th width="25%">&nbsp;</th>
							<th width="25%">
								<button class="btn btn-xs btn-success" type="button" onclick="document.location.href='index.php?page=<?=$_REQUEST['page']?>&exec=edit_ai&id=<?=$businessContact->getID()?>';">
									<span class="glyphicons glyphicons-plus"></span>
									<?=$_LANG->get('Lieferadresse hinzuf&uuml;gen')?>
								</button>
							</th>
						</tr>
						</thead>
						<?php
						$addressDelivery = Address::getAllAddresses($businessContact, Address::ORDER_NAME, Address::FILTER_DELIV);
						foreach ($addressDelivery as $ad) {
							?>
							<tbody>
							<tr>
								<td><? echo $ad->getName1() . ' ' . $ad->getName2();
									if ($ad->getDefault() == 1) echo ' (Standard)'; ?>
								</td>
								<td><? echo $ad->getAddress1() . " " . $ad->getAddress2(); ?></td>
								<td><? echo $ad->getZip() . " " . $ad->getCity(); ?></td>
								<td>
									<?/*gln*/
									?>
									<img src="images/status/
					<? if ($ad->getShoprel() == 0) {
										echo "red_small.gif";
									} else {
										echo "green_small.gif";
									}
									?>" title="<?= $_LANG->get('Shop-Freigabe') ?>"> &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
									<a href="index.php?page=<?= $_REQUEST['page'] ?>&exec=edit_ad&id_a=<?= $ad->getId() ?>&id=<?= $businessContact->getID() ?>"><span
											class="glyphicons glyphicons-pencil"></span></a>
								</td>
							</tr>
							</tbody>

							<?php
						}
						?>
					</table>
				</div>
			<? } ?>
		</div>
	</div>

		<? // ------------------------------------- Ansprechpartner ----------------------------------------------?>
		<div id="tabs-3">
			<? if ($businessContact->getId() > 0) { ?>
				<div class="panel panel-default">
					  <div class="panel-heading">
							<h3 class="panel-title">
								Ansprechpartner
								<span class="pull-right">
									<button class="btn btn-xs btn-success" type="button"
											onclick="document.location.href='index.php?page=<?= $_REQUEST['page'] ?>&exec=edit_cp&id=<?= $businessContact->getID() ?>';">
										<span class="glyphicons glyphicons-plus"></span>
										<?= $_LANG->get('Ansprechpartner hinzuf&uuml;gen') ?>
									</button>
								</span>
							</h3>
					  </div>

					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
							<tr>
							</tr>
							</thead>
							<? //$contactPerson = ContactPerson::getAllContactPersons($businessContact,ContactPerson::ORDER_NAME);
							foreach ($contactPersons as $cp) {
								?>
								<tbody>
								<tr <? if ($cp->isMainContact()) echo 'style="font-weight:bold;"' ?>>
									<td class="content_row pointer"
										onclick="document.location='index.php?page=<?= $_REQUEST['page'] ?>&exec=edit_cp&cpid=<?= $cp->getId() ?>&id=<?= $businessContact->getID() ?>'"
										valign="top">
										<?php echo $cp->getNameAsLine(); ?> &ensp; </br>
										<? if ($cp->getBirthDate()) echo '<span class="glyphicons glyphicons-cake"></span>&nbsp;' . date("d.m.Y", $cp->getBirthDate()); ?>
									</td>
									<td class="content_row pointer"
										onclick="document.location='index.php?page=<?= $_REQUEST['page'] ?>&exec=edit_cp&cpid=<?= $cp->getId() ?>&id=<?= $businessContact->getID() ?>'"
										valign="top">
										<?
										$contact_attributes = Attribute::getAllAttributesForContactperson();
										$active_cust_attributes = $cp->getActiveAttributeItemsInput();
										foreach ($contact_attributes AS $attribute) {
											$tmp_output = "<i>" . $attribute->getTitle() . ":</i> ";
											$allitems = $attribute->getItems();
											$j = 0;
											foreach ($allitems AS $item) {
												if ($active_cust_attributes["{$attribute->getId()}_{$item["id"]}"]["value"] == 1) {
													$tmp_output .= $item["title"];
													if ($active_cust_attributes["{$attribute->getId()}_{$item["id"]}"]["inputvalue"] != "") {
														$tmp_output .= ": '" . $active_cust_attributes["{$attribute->getId()}_{$item["id"]}"]["inputvalue"] . "'";
													}
													$tmp_output .= ", ";
													$j++;
												}
											}
											$tmp_output = substr($tmp_output, 0, -2); // Letztes Komma entfernen
											if ($j > 0) {
												echo $tmp_output . "<br/>";
											}
										} ?> &ensp;
									</td>
									<td class="content_row pointer" valign="top"
										onclick="document.location='index.php?page=<?= $_REQUEST['page'] ?>&exec=edit_cp&cpid=<?= $cp->getId() ?>&id=<?= $businessContact->getID() ?>'">
										<? if ($cp->getActiveAdress() == 1) {
											echo $cp->getPhone();
										}
										if ($cp->getActiveAdress() == 2) {
											echo $cp->getAlt_phone();
										}
										if ($cp->getActiveAdress() == 3) {
											echo $cp->getPriv_phone();
										}
										if ($cp->getEmail()) echo "</br>" . $cp->getEmail();
										?> &ensp;
									</td>
									<td class="content_row pointer"
										onclick="document.location='index.php?page=<?= $_REQUEST['page'] ?>&exec=edit_cp&cpid=<?= $cp->getId() ?>&id=<?= $businessContact->getID() ?>'"> &ensp;</td>
									<td class="content_row" align="right">
										<a class="icon-link"
										   href="index.php?page=<?= $_REQUEST['page'] ?>&exec=edit_cp&cpid=<?= $cp->getId() ?>&id=<?= $businessContact->getID() ?>"><span
												class="glyphicons glyphicons-pencil"></span></a>
									</td>
								</tr>
								</tbody>
								<?
							}
							?>
						</table>
					</div>
					<? } ?>
				</div>
			<p></p>
		</div>
	<? // ------------------------------------- Merkmale ----------------------------------------------?>
		<div id="tabs-5">
			<div class="form-horizontal">
				<div class="panel panel-default">
					  <div class="panel-heading">
							<h3 class="panel-title">
								Merkmale
							</h3>
					  </div>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
							<tr>
								<th></th>
							</tr>
							</thead>
							<tbody>
							<tr>
								<td><?= $_LANG->get('Sprache') ?></td>
								<td>
									<div class="col-md-4">
										<select name="language" class="form-control" onfocus="markfield(this,0)" onblur="markfield(this,1)">
											<? foreach ($languages as $l) { ?>
												<option value="<?= $l->getId() ?>"
													<? if ($businessContact->getLanguage()->getId() == $l->getId()) echo "selected"; ?>>
													<?= $l->getName() ?>
												</option>
											<? } ?>
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<td><?= $_LANG->get('Mandant') ?></td>
								<td>
									<div class="col-md-4">
										<select name="client" class="form-control" onfocus="markfield(this,0)"
												onblur="markfield(this,1)">
											<option value="<?= $_USER->getClient()->getId() ?>" selected>
												<? if (!$_USER->getClient()->isActive()) echo '<span color="red">'; ?>
												<?= $_USER->getClient()->getName() ?>
												<? if (!$_USER->getClient()->isActive()) echo '</span>'; ?>
											</option>
										</select>
									</div>
								</td>
							</tr>
							<tr>
								<td colspan="2">&ensp;</td>
							</tr>
							<? foreach ($all_attributes AS $attribute) { ?>
								<tr>
									<td valign="top"><?= $attribute->getTitle() ?></td>
									<td>
										<? $allitems = $attribute->getItems(); ?>
										<table>
											<? $x = 0;
											foreach ($allitems AS $item) {
												if ($x % 5 == 0) echo "<tr>";
												echo '<td width="200px">';
												echo '<input name="attribute_item_check_' . $attribute->getId() . '_' . $item["id"] . '" ';
												echo ' value="1" type="checkbox" onfocus="markfield(this,0)" onblur="markfield(this,1)"';
												if ($all_active_attributes["{$attribute->getId()}_{$item["id"]}"]["value"] == 1) echo "checked";
												echo ">";
												echo $item["title"];
												if ($item["input"] == 1) {
													echo ' <input name="attribute_item_input_' . $attribute->getId() . '_' . $item["id"] . '" ';
													echo ' value="';
													echo $all_active_attributes["{$attribute->getId()}_{$item["id"]}"]["inputvalue"];
													echo '" type="text" onfocus="markfield(this,0)" onblur="markfield(this,1)">';
												}
												echo "</td>";
												if ($x % 5 == 4) echo "</tr>";
												$x++;
											} ?>
										</table>
									</td>
								</tr>
							<? } ?>
							</tbody>
						</table>
					</div>
				</div>

				<p></p>
			</div>
		</div>

		<? // ------------------------------------- verbundene Tickets ----------------------------------------------?>

		<div id="tabs-7">
		<?if($businessContact->getId()){?>

			<? // Tickets laden, die dem Kunden zugeordnet wurden
				$from_busicon = true;
				$notes_only = false;
				$contactID = $businessContact->getId();
				require_once 'libs/modules/tickets/ticket.for.php';?>
		<? } ?>
		</div>

		<? // ------------------------------------- Personalisierung ----------------------------------------------?>

		<div id="tabs-8">
		<?if($businessContact->getId()){?>
			<div class="form-horizontal">
				<div class="panel panel-default">
					  <div class="panel-heading">
							<h3 class="panel-title">
								Personalisierung (Positions Titel)
								<span class="glyphicons glyphicons-plus pointer" onclick="addTitlePosition()"></span>
							</h3>
					  </div>
					<div class="table-responsive">
						<table id="table_positiontitles" class="table table-hover">
							<thead>
							<tr>
							</tr>
							</thead>
							<tbody>
							<?
							if (count($businessContact->getPositionTitles()) > 0 ) {
								$i = 0;
								foreach($businessContact->getPositionTitles() as $position_title)
								{?>
									<tr id="tr_positiontitle_<?=$i?>">
										<td colspan="3">
											<input type="text" value="<?=$position_title?>" class="form-control" name="position_titles[]"><span class="glyphicons glyphicons-remove pointer" onclick="removeTitlePosition(this)"></span>
										</td>
									</tr>
									<?$i++; }
							}
							?>
							</tbody>
							<input type="hidden" value="<?=$i?>" name="position_titles_count" id="position_titles_count">
						</table>
						<? } ?>
					</div>
				</div>
			</div>
		</div>
</form>

		<? // ------------------------------------- Rechnungsausgang ----------------------------------------------?>

		<div id="tabs-9">
		<?if($businessContact->getId()){
		    $_REQUEST['page'] = "libs/modules/accounting/outgoinginvoice.php";
			$_REQUEST["filter_from"] = 1;
			$_REQUEST["filter_cust"] = $businessContact->getId();
			require_once('libs/modules/accounting/outgoinginvoice.php');
		} ?>
		</div>

		<? // ------------------------------------- Rechnungseingang ----------------------------------------------?>

		<div id="tabs-10">
		<?if($businessContact->getId()){
		    $_REQUEST['page'] = "libs/modules/accounting/incominginvoice.php";
			$_REQUEST["filter_from"] = 1;
			$_REQUEST["filter_cust"] = $businessContact->getId();
			require_once('libs/modules/accounting/incominginvoice.php');
		} ?>
		</div>

		<? // ------------------------------------- Vorgänge ----------------------------------------------?>

		<div id="tabs-11">
		<?if($businessContact->getId()){
		    $_REQUEST['page'] = "libs/modules/collectiveinvoice/collectiveinvoice.overview.php";
			$_REQUEST['cust_id'] = $businessContact->getId();
			require_once('libs/modules/collectiveinvoice/collectiveinvoice.overview.php');
		}
		$_REQUEST['page'] = "libs/modules/businesscontact/businesscontact.php";?>
		</div>

		<? // ------------------------------------- Notizen ----------------------------------------------?>

		<div id="tabs-12">
			<?if($businessContact->getId()){?>
			<div class="panel panel-default">
				  <div class="panel-heading">
						<h3 class="panel-title">
							Notizen
							<span class="pull-right">
				 <?php if ($_USER->hasRightsByGroup(Group::RIGHT_NOTES_BC) || $_USER->isAdmin()){?>
								<span
									<button class="btn btn-xs btn-success" onclick="callBoxFancytktc('libs/modules/comment/comment.new.php?tktid=0&tktc_module=<?php echo get_class($businessContact);?>&tktc_objectid=<?php echo $businessContact->getId();?>');">
										<span class="glyphicons glyphicons-plus pointer" style="float:right;" </span>
											<?= $_LANG->get(' Neu') ?>
									</button>
							</span>
							<?php }?>
							</span>
						</h3>
				  </div>
				<br>
				<table id="comment_table" width="100%" cellpadding="0"
					   cellspacing="0" class="stripe hover row-border order-column">
					<thead>
					<tr>
						<th></th>
						<th><?=$_LANG->get('ID')?></th>
						<th><?=$_LANG->get('Titel')?></th>
						<th><?=$_LANG->get('erst. von')?></th>
						<th><?=$_LANG->get('Datum')?></th>
						<th><?=$_LANG->get('Sichtbarkeit')?></th>
					</tr>
					</thead>
				</table>
				<?php }?>
			</div>
		</div>

	<? // ------------------------------------- Navigations und Speicher Buttons ------------------------------------?>
	<?php } ?>
	</div>
</div>
<!--//-->