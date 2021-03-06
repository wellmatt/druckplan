<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

require_once 'libs/modules/attachment/attachment.class.php';


$all_user = User::getAllUser(User::ORDER_NAME, $_USER->getClient()->getId());

if ($_REQUEST['subexec'] == "copy")
{
    $collectinv = new CollectiveInvoice(CollectiveInvoice::duplicate($collectinv->getId()));
    $ci = $collectinv;
}

//Falls eine neue manuelle Rechnung erzeugt wird
if($collectinv->getId()==0){
	
	//ausgew?hlten Benutzer aus der DB holen und setzen
	$selected_customer = new BusinessContact((int)$_REQUEST["order_customer"]);
	$collectinv->setBusinesscontact($selected_customer);
	$tmp_presel_cp = new ContactPerson((int)$_REQUEST["order_contactperson"]);
	$collectinv->setCustContactperson($tmp_presel_cp);
	if ($_REQUEST["order_title"])
	    $collectinv->setTitle($_REQUEST["order_title"]);
    if ($_REQUEST["order_startart"] != "")
    {
        $tmp_startart = new Article($_REQUEST["order_startart"]);
        $tmp_orderamounts = json_encode($tmp_startart->getOrderamounts());
        $tmp_type = 2;
        if ($tmp_startart->getOrderid()>0)
            $tmp_type = 1;
        ?>
        <script type="text/javascript">
        $(document).ready(function() {
        	var orderamounts = new Array(<?php echo implode(',', $tmp_startart->getOrderamounts()); ?>);
            addPositionRow(<?php echo $tmp_type;?>,<?php echo $tmp_startart->getId();?>,"<?php echo $tmp_startart->getTitle()?>",orderamounts,<?php echo $tmp_startart->getOrderid();?>);
        });
        </script>
        <?php
    }
	//Datum und Benutzer setzen, wer erstellt hat
	$collectinv->setCrtuser($_USER);
	$collectinv->setCrtdate(time());
	$all_bc_cp = ContactPerson::getAllContactPersons($collectinv->getBusinesscontact());
}else{//Falls eine bestehende Rechnung veraendert werden soll
	$selected_customer = new BusinessContact($collectinv->getBusinesscontact()->getId());
}

// Alle Zahlungsarten holen
$allpaymentterms = PaymentTerms::getAllPaymentTerms();

// Alle Versandoptionen holen
$alldeliverycondition = DeliveryTerms::getAllDeliveryConditions();

// Lieferaddressen des Geschaeftskontakts holen
$all_deliveryadress = Address::getAllAddresses($selected_customer, Address::ORDER_NAME, Address::FILTER_DELIV);
$all_invoiceadress = Address::getAllAddresses($selected_customer, Address::ORDER_NAME, Address::FILTER_INVC);


if (!empty($_REQUEST['subexec']) && $_REQUEST['subexec']){
    if ($_REQUEST['subexec'] == "movedown"){
        $i = 0;
        foreach($collectinv->getPositions() as $position){
            if ($position->getId() == $_REQUEST['posid']){
                $tmp_index = $i;
                break; 
            }
            $i++;
        }
        $all_positions = $collectinv->getPositions();
        
        $tmp_old_id1 = $all_positions[$tmp_index]->getId();
        $tmp_old_id2 = $all_positions[$tmp_index+1]->getId();
        
        $all_positions[$tmp_index]->setId($tmp_old_id2);
        $all_positions[$tmp_index+1]->setId($tmp_old_id1);
        
        Orderposition::saveMultipleOrderpositions($all_positions);
        
    } else if ($_REQUEST['subexec'] == "moveup"){
        $i = 0;
        foreach($collectinv->getPositions() as $position){
            if ($position->getId() == $_REQUEST['posid']){
                $tmp_index = $i;
                break;
            }
            $i++;
        }
        $all_positions = $collectinv->getPositions();
        
        $tmp_old_id1 = $all_positions[$tmp_index]->getId();
        $tmp_old_id2 = $all_positions[$tmp_index-1]->getId();
        
        $all_positions[$tmp_index]->setId($tmp_old_id2);
        $all_positions[$tmp_index-1]->setId($tmp_old_id1);
        
        Orderposition::saveMultipleOrderpositions($all_positions);
    }
} // &exec=edit&subexec=movedown&ciid='.$_REQUEST['ciid'].'&posid='.$position->getId().'">
$attributes = $collectinv->getActiveAttributeItemsInput();

//----------------------------------- Javascript---------------------------------------?>
<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/tinymce/tinymce.min.js"></script>

<script>
	$(function() {
		$("a#hiddenclicker_artframe").fancybox({
			'type'    : 'iframe',
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600,
			'speedOut'		:	200,
			'padding'		:	25,
			'margin'        :   25,
			'scrolling'     :   'auto',
			'width'		    :	1000,
			'height'        :   900,
			'onComplete'    :   function() {
				$('#fancybox-frame').load(function() { // wait for frame to load and then gets it's height
// 	                		      $('#fancybox-content').height($(this).contents().find('body').height()+300);
					$('#fancybox-wrap').css('top','25px');
				});
			},
			'overlayShow'	:	true,
			'helpers'		:   { overlay:null, closeClick:true }
		});
		$("a#hiddenclicker_pdfframe").fancybox({
			'type'    : 'iframe',
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600,
			'speedOut'		:	200,
			'padding'		:	25,
			'margin'        :   25,
			'scrolling'     :   'auto',
			'width'		    :	1000,
			'height'        :   900,
			'onComplete'    :   function() {
				$('#fancybox-frame').load(function() { // wait for frame to load and then gets it's height
// 	                		      $('#fancybox-content').height($(this).contents().find('body').height()+300);
					$('#fancybox-wrap').css('top','25px');
				});
			},
			'overlayShow'	:	true,
			'helpers'		:   { overlay:null, closeClick:true }
		});
	});
	function callBoxFancyArtFrame(my_href) {
		var j1 = document.getElementById("hiddenclicker_artframe");
		j1.href = my_href;
		$('#hiddenclicker_artframe').trigger('click');
	}
	function callBoxFancyContentPdf(my_href) {
		var j1 = document.getElementById("hiddenclicker_pdfframe");
		j1.href = my_href;
		$('#hiddenclicker_pdfframe').trigger('click');
	}
</script>
<div id="hidden_clicker95" style="display:none"><a id="hiddenclicker_artframe" href="http://www.google.com" >Hidden Clicker</a></div>
<div id="hidden_clicker95" style="display:none"><a id="hiddenclicker_pdfframe" href="http://www.google.com" >Hidden Clicker</a></div>

<script type="text/javascript">
$(function() {
	$('#colinv_deliverydate').datetimepicker({
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
		 scrollInput:false,
		 timepicker:false,
		 format:'d.m.Y'
	});
	 $( "#add_position" ).autocomplete({
		 source: "libs/modules/collectiveinvoice/collectiveinvoice.ajax.php?ajax_action=search_position&bcid=<?php echo $collectinv->getBusinesscontact()->getId(); ?>",
		 minLength: 2,
		 focus: function( event, ui ) {
    		 $( "#add_position" ).val( ui.item.label );
    		 return false;
		 },
		 select: function( event, ui ) {
			 addPositionRow(ui.item.type,ui.item.value,ui.item.label,ui.item.orderamounts,ui.item.orderid);
			 $( "#add_position" ).val("");
    		 return false;
		 }
	 });
});

function clickSearch(id_i){
	var tmp_type= document.getElementById('orderpos_type_'+id_i).value;
	var str = document.getElementById('orderpos_search_'+id_i).value;
	
	$.post("libs/modules/collectiveinvoice/collectiveinvoice.ajax.php", 
		{exec: 'searchPositions', type : tmp_type, str : str, cust_id : <?=$selected_customer->getId()?>}, 
		 function(data) {
			document.getElementById('orderpos_searchlist_'+id_i).innerHTML = data;
			document.getElementById('orderpos_searchlist_'+id_i).style.display = "";
		});
}

function updateDeliveryPrice(){
	var del_id = document.getElementsByName('colinv_deliveryterm')[0].value;
	
	$.post("libs/modules/collectiveinvoice/collectiveinvoice.ajax.php", 
		{exec: 'getDeliveryPrice', delivid: del_id}, 
		 function(data) {
			document.getElementById('colinv_deliverycosts').value = data;
	}); 
}
</script>

<script>
	$(function() {
		$("a#association_hiddenclicker").fancybox({
			'type'    : 'iframe',
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600, 
			'speedOut'		:	200, 
			'height'		:	350, 
			'overlayShow'	:	true,
			'helpers'		:   { overlay:null, closeClick:true }
		});
	});
	function callBoxFancyAsso(my_href) {
		var j1 = document.getElementById("association_hiddenclicker");
		j1.href = my_href;
		$('#association_hiddenclicker').trigger('click');
	}
</script>
<div id="association_hidden_clicker" style="display:none"><a id="association_hiddenclicker" href="http://www.google.com" >Hidden Clicker</a></div>

<script>
	$(function() {
		$("a#hiddenclicker").fancybox({
			'type'          :   'iframe',
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600, 
			'speedOut'		:	200, 
			'width'         :   1024,
			'height'		:	768, 
		    'scrolling'     :   'yes',
			'overlayShow'	:	true,
			'helpers'		:   { overlay:null, closeClick:true }
		});
	});
	function callBoxFancyPreview(my_href) {
		var j1 = document.getElementById("hiddenclicker");
		j1.href = my_href;
		$('#hiddenclicker').trigger('click');
	}
</script>
<div id="hidden_clicker" style="display:none"><a id="hiddenclicker" href="http://www.google.com" >Hidden Clicker</a></div>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
if ($collectinv->getLocked() == 0){
	$quickmove->addItem('Speichern','#',"$('#form_collectiveinvoices').submit();",'glyphicon-floppy-disk');
	if($_USER->hasRightsByGroup(Group::RIGHT_DELETE_COLINV) || $_USER->isAdmin()){
		$quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=delete&del_id=".$collectinv->getId()."');", 'glyphicon-trash', true);
	}
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<?//--------------------------------------HTML ----------------------------------------?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
			Vorgang<?php if ($collectinv->getId() > 0) echo ': ' . $collectinv->getNumber(); ?>
			<? if ($collectinv->getId() > 0) {
				$all_bc_cp = ContactPerson::getAllContactPersons($collectinv->getBusinesscontact()); ?>
				<span class="pull-right" style="margin-top: -6px;">
						<div class="btn-group" role="group">
							<?php
							if ($collectinv->getId()>0 && $collectinv->getTicket()>0) {
								?>
								<button type="button"
										onclick="askDel('index.php?page=<?= $_REQUEST['page'] ?>&exec=updatefromticket&ciid=<?= $collectinv->getId() ?>');"
										class="btn btn-sm btn-default">Aus Ticket aktualisieren
								</button>
								<?php
							}
							?>
							<button type="button"
									onclick="window.location='index.php?page=<?= $_REQUEST['page'] ?>&exec=docs&ciid=<?= $collectinv->getId() ?>';"
									class="btn btn-sm btn-default">Dokumente
							</button>
							<button type="button"
									onclick="window.location='index.php?page=<?= $_REQUEST['page'] ?>&exec=notes&ciid=<?= $collectinv->getId() ?>';"
									class="btn btn-sm btn-default"><?php if ($collectinv->getId() > 0) echo '<span id="notify_count" class="badge">' . Comment::getCommentCountForObject("CollectiveInvoice", $collectinv->getId()) . '</span>'; ?>
								VO-Notizen
							</button>
							<?php
							$association_object = $collectinv;
							$associations = Association::getAssociationsForObject(get_class($association_object), $association_object->getId());
							?>
							<script type="text/javascript">
								function removeAsso(id) {
									$.ajax({
										type: "POST",
										url: "libs/modules/associations/association.ajax.php",
										data: {ajax_action: "delete_asso", id: id}
									})
								}
							</script>
							<div class="btn-group dropdown">
								<button type="button" class="btn btn-sm dropdown-toggle btn-default"
										data-toggle="dropdown" aria-expanded="false">
									Verknüpfungen <span class="badge"><?php echo count($associations); ?></span> <span
										class="caret"></span>
								</button>
								<ul class="dropdown-menu" role="menu">
									<?php
									if (count($associations) > 0) {
										$as = 0;
										foreach ($associations as $association) {
											if ($association->getModule1() == get_class($association_object) && $association->getObjectid1() == $association_object->getId()) {
												$classname = $association->getModule2();
												$object = new $classname($association->getObjectid2());
												$link_href = Association::getPath($classname);
												$object_name = Association::getName($object);
											} else {
												$classname = $association->getModule1();
												$object = new $classname($association->getObjectid1());
												$link_href = Association::getPath($classname);
												$object_name = Association::getName($object);
											}
											echo '<li id="as_' . $as . '"><a href="index.php?page=' . $link_href . $object->getId() . '">';
											echo $object_name;
											echo '</a>';
											if ($_USER->isAdmin() || $_USER->hasRightsByGroup(Group::RIGHT_ASSO_DELETE))
												echo '<span class="glyphicons glyphicons-remove pointer" onclick=\'removeAsso(' . $association->getId() . '); $("#as_' . $as . '").remove();\'></span>';
											echo '</li>';
											$as++;
										}
									}
									echo '<li class="divider"></li>';
									echo '<li><a href="#" onclick="callBoxFancyAsso(\'libs/modules/associations/association.frame.php?module=' . get_class($association_object) . '&objectid=' . $association_object->getId() . '\');">Neue Verknüpfung</a></li>';
									?>
								</ul>
							</div>
							<?php if ($collectinv->getId() > 0) { ?>

								<div class="btn-group dropdown" style="margin-left: 0px;">
									<button type="button" class="btn btn-sm dropdown-toggle btn-default"
											data-toggle="dropdown" aria-expanded="false">
										Vorschau <span class="caret"></span>
									</button>
									<ul class="dropdown-menu" role="menu">
										<li>
											<a href="#"
											   onclick="callBoxFancyPreview('libs/modules/collectiveinvoice/collectiveinvoice.preview.php?ciid=<?php echo $collectinv->getId(); ?>&type=1');">Angebot</a>
											<a href="#"
											   onclick="callBoxFancyPreview('libs/modules/collectiveinvoice/collectiveinvoice.preview.php?ciid=<?php echo $collectinv->getId(); ?>&type=2');">Auftragsbestätigung</a>
											<a href="#"
											   onclick="callBoxFancyPreview('libs/modules/collectiveinvoice/collectiveinvoice.preview.php?ciid=<?php echo $collectinv->getId(); ?>&type=5');">Auftragstasche</a>
											<a href="#"
											   onclick="callBoxFancyPreview('libs/modules/collectiveinvoice/collectiveinvoice.preview.php?ciid=<?php echo $collectinv->getId(); ?>&type=3');">Lieferschein</a>
											<a href="#"
											   onclick="callBoxFancyPreview('libs/modules/collectiveinvoice/collectiveinvoice.preview.php?ciid=<?php echo $collectinv->getId(); ?>&type=15');">Etiketten</a>
											<a href="#"
											   onclick="callBoxFancyPreview('libs/modules/collectiveinvoice/collectiveinvoice.preview.php?ciid=<?php echo $collectinv->getId(); ?>&type=4');">Rechnung</a>
											<a href="#"
											   onclick="callBoxFancyPreview('libs/modules/collectiveinvoice/collectiveinvoice.preview.php?ciid=<?php echo $collectinv->getId(); ?>&type=7');">Gutschrift</a>
										</li>
									</ul>
								</div>

								<div class="btn-group dropdown" style="margin-left: 0px;">
									<button type="button" class="btn btn-sm dropdown-toggle btn-default"
											data-toggle="dropdown" aria-expanded="false">
										Neu <span class="caret"></span>
									</button>
									<ul class="dropdown-menu" role="menu">
										<li>
											<a href="#"
											   onclick="askDel('index.php?page=libs/modules/tickets/ticket.php&exec=new&customer=<?php echo $collectinv->getCustomer()->getId(); ?>&contactperson=<?php echo $collectinv->getCustContactperson()->getId(); ?>&asso_class=<?php echo get_class($collectinv); ?>&asso_object=<?php echo $collectinv->getId() ?>&tkt_title=<?php echo $collectinv->getNumber() . ' - ' . $collectinv->getTitle(); ?>');">Ticket
												erstellen (verknüpft)</a>
										</li>
										<li>
											<a href="#" onclick="window.location.href='libs/modules/export/export.download.php?function=aepos_export&colinvid=<?php echo $collectinv->getId();?>';">
												AEPOS Export
											</a>
										</li>
									</ul>
								</div>
							<?php } ?>
						</div>
					</span>
			<?php } ?>
		</h3>
	</div>
	<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" class="form-horizontal" id="form_collectiveinvoices" name="form_collectiveinvoices" onsubmit="return checkform(new Array(colinv_title))">
		<input 	type="hidden" name="exec" value="save">
		<input 	type="hidden" name="ciid" value="<?=$collectinv->getId()?>">
		<input type="hidden" name="asso_class" value="<?php echo $_REQUEST["asso_class"];?>">
		<input type="hidden" name="asso_object" value="<?php echo $_REQUEST["asso_object"];?>">
		<input 	type="hidden" name="colinv_businesscontact"  value="<?=$collectinv->getBusinesscontact()->getId()?>">

		<div class="panel-body">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Kopfdaten</h3>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label for="" class="col-sm-2 control-label">Vorgangstitel</label>
						<div class="col-sm-10">
							<input name="colinv_title" class="form-control" value="<?= $collectinv->getTitle()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Kunde</label>
								<div class="col-sm-8 form-text">
									<a href="index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id=<?=$selected_customer->getId()?>"><?= $selected_customer->getNameAsLine()?></a>
								</div>
							</div>
							<?if ($collectinv->getCrtdate() != 0){?>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Erstellt am</label>
								<div class="col-sm-8 form-text">
									<?if ($collectinv->getCrtdate() != 0) echo date("d.m.Y H:i:s",$collectinv->getCrtdate())?>
								</div>
							</div>
							<?php }?>
							<?php /*
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Status</label>
								<div class="col-sm-8 form-text">
									<div class="progress" style="margin-bottom: 0px;">
										<div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
											 style="width: <?php echo 100/7*$collectinv->getStatus();?>%; background-color: <?php echo $collectinv->getStatusColor();?>;">
											<?php echo getOrderStatus($collectinv->getStatus());?>
										</div>
									</div>
								</div>
							</div>
 							*/ ?>

							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Status</label>
								<div class="col-sm-4">
									<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?= $collectinv->getId() ?>&exec=setState2&state=1">
										<img class="select" title="<?php echo getOrderStatus(1);?>" src="./images/status/<?
										if($collectinv->getStatus() == 1)
											echo 'red.svg';
										else
											echo 'black.svg'; ?>">
									</a>
									<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=setState2&state=2">
										<img class="select" title="<?php echo getOrderStatus(2);?>" src="./images/status/<?
										if($collectinv->getStatus() == 2)
											echo 'orange.svg';
										else
											echo 'black.svg';?>">
									</a>
									<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=setState2&state=3">
										<img class="select" title="<?php echo getOrderStatus(3);?>" src="./images/status/<?
										if($collectinv->getStatus() == 3)
											echo 'yellow.svg';
										else
											echo 'black.svg'; ?>">
									</a>
									<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=setState2&state=4">
										<img class="select" title="<?php echo getOrderStatus(4);?>" src="./images/status/<?
										if($collectinv->getStatus() == 4)
											echo 'lila.svg';
										else
											echo 'black.svg';?>">
									</a>
									<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=setState2&state=5">
										<img class="select" title="<?php echo getOrderStatus(5);?>" src="./images/status/<?
										if($collectinv->getStatus() == 5)
											echo 'blue.svg';
										else
											echo 'black.svg';?>">
									</a>
									<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=setState2&state=6">
										<img class="select" title="<?php echo getOrderStatus(6);?>" src="./images/status/<?
										if($collectinv->getStatus() == 6)
											echo 'light_blue.svg';
										else
											echo 'black.svg';?>">
									</a>
									<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=setState2&state=7">
										<img class="select" title="<?php echo getOrderStatus(7);?>" src="./images/status/<?
										if($collectinv->getStatus() == 7)
											echo 'green.svg';
										else
											echo 'black.svg';?>">
									</a>
								</div>
								<div class="col-sm-4">
									<?=getOrderStatus($collectinv->getStatus(), true)?>
								</div>
							</div>

							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Zahlungsart</label>
								<div class="col-sm-8">
									<select name="colinv_paymentterm" id="colinv_paymentterm" class="form-control">
										<option value="0"> &lt; <?=$_LANG->get('Bitte w&auml;hlen') ?> &gt;</option>
										<?	foreach($allpaymentterms as $payterm){
											echo '<option value="'. $payterm->getId() . '"';
											if($payterm->getId() == $collectinv->getPaymentTerm()->getId())
											{
												echo ' selected="selected"';
											} else if ($collectinv->getId() <= 0 && $payterm->getId() == $collectinv->getBusinesscontact()->getPaymentTerms()->getId())
											{
												echo ' selected="selected"';
											}

											echo ">".$payterm->getName1() . "</option>";
										} ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Lief. Adr.</label>
								<div class="col-sm-8">
									<select name="colinv_deliveryadress" id="colinv_deliveryadress" class="form-control">
										<?	foreach($all_deliveryadress as $adress){
											if ($collectinv->getId() == 0)
											{
												echo '<option value="'. $adress->getId() . '"';
												if($adress->getDefault()){ echo ' selected="selected"'; }
												echo ">".$adress->getAddressAsLine() . "</option>";
											} else {
												echo '<option value="'. $adress->getId() . '"';
												if($adress->getId() == $collectinv->getDeliveryaddress()->getId()){ echo ' selected="selected"'; }
												echo ">".$adress->getAddressAsLine() . "</option>";
											}
										} ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Versandart</label>
								<div class="col-sm-8">
									<select name="colinv_deliveryterm" id="colinv_deliveryterm" class="form-control">
										<option value="0"> &lt; <?=$_LANG->get('Bitte w&auml;hlen') ?> 	&gt;</option>
										<?foreach($alldeliverycondition as $delcon){
											echo '<option value="' . $delcon->getId() . '"';
											if ($delcon->getId() == $collectinv->getDeliveryTerm()->getID()){ echo 'selected="selected" ';}
											echo ">".$delcon->getName1() . "</option>";
										}?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Kosten St.</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="colinv_intent" id="colinv_intent" value="<?=$collectinv->getIntent()?>">
								</div>
							</div>
							<?php if ($collectinv->getId()>0){?>
								<div class="form-group">
									<label for="" class="col-sm-4 control-label">Merkmale</label>
									<div class="col-sm-8 form-text">
										<span class="pointer" onclick="callBoxFancyArtFrame('libs/modules/collectiveinvoice/collectiveinvoice.attribute.frame.php?ciid=<?php echo $collectinv->getId();?>');"><a>anzeigen</a> (<?php echo count($attributes);?>)</span>
									</div>
								</div>
							<?php }?>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">MA</label>
								<div class="col-sm-8">
									<select name="intern_contactperson" id="intern_contactperson" class="form-control">
										<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt</option>
										<?
										foreach($all_user as $us)
										{
											if ($collectinv->getId() == 0)
											{
												echo '<option value="'.$us->getId().'" ';
												if($_USER->getId() == $us->getId()) echo "selected";
												echo '>'.$us->getNameAsLine().'</option>';
											} else {
												echo '<option value="'.$us->getId().'" ';
												if($collectinv->getInternContact()->getId() == $us->getId()) echo "selected";
												echo '>'.$us->getNameAsLine().'</option>';
											}
										}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Fremdleistung</label>
								<div class="col-sm-8">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="thirdparty" id="thirdparty" value="1" <?php if ($collectinv->getThirdparty()) echo ' checked ';?>>
										</label>
									</div>
								</div>
							</div>
							<div class="form-group" id="thirdpartycomment_title" style="<?php if ($collectinv->getThirdparty() == 0) echo 'display: none;';?>">
								<label for="" class="col-sm-4 control-label">Bem.Fremdl.</label>
								<div class="col-sm-8">
									<textarea name="thirdpartycomment" id="thirdpartycomment" class="form-control" style="<?php if ($collectinv->getThirdparty() == 0) echo 'display: none;';?>"><?php echo $collectinv->getThirdpartycomment();?></textarea>
								</div>
							</div>
							<?php /**if ($collectinv->getSavedcost() == 1){?>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Errechneter Profit</label>
								<div class="col-sm-8 form-text">
									<?php echo printPrice($collectinv->getMyProfit());?>€
								</div>
							</div>
							<?php }**/ ?>
						</div> <!-- ENDE COL LINKS -->

						<div class="col-md-6">
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">ASP Kunde</label>
								<div class="col-sm-8">
									<select name="custContactperson" id="custContactperson" class="form-control">
										<option value="0">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt</option>
										<?
										foreach($all_bc_cp as $cp)
										{
											echo '<option value="'.$cp->getId().'" ';
											if($collectinv->getCustContactperson()->getId() == $cp->getId()) echo "selected";
											echo '>'.$cp->getNameAsLine().'</option>';
										}
										?>
									</select>
								</div>
							</div>
							<?if ($collectinv->getCrtuser()->getId() != 0){?>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Erstellt von</label>
								<div class="col-sm-8 form-text">
									<?php echo $collectinv->getCrtuser()->getNameAsLine();?>
								</div>
							</div>
							<?php }?>
							<?if ($collectinv->getUptdate() != 0){?>
								<div class="form-group">
									<label for="" class="col-sm-4 control-label">Geändert</label>
									<div class="col-sm-8 form-text">
										<?if ($collectinv->getUptdate() != 0) echo date("d.m.Y H:i:s",$collectinv->getUptdate()).' ('.$collectinv->getUptuser()->getNameAsLine().')'?>
									</div>
								</div>
							<?php }?>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Rech. Adr.</label>
								<div class="col-sm-8">
									<select name="invoice_address" id="invoice_address" class="form-control">
										<?
										foreach($all_invoiceadress as $invc)
										{
											if ($collectinv->getId() == 0)
											{
												echo '<option value="'.$invc->getId().'" ';
												if($invc->getDefault()) echo "selected";
												echo '>'.$invc->getNameAsLine().', '.$invc->getAddressAsLine().'</option>';
											} else {
												echo '<option value="'.$invc->getId().'" ';
												if($collectinv->getInvoiceAddress()->getId() == $invc->getId()) echo "selected";
												echo '>'.$invc->getNameAsLine().', '.$invc->getAddressAsLine().'</option>';
											}
										}
										?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Lieferdatum</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="colinv_deliverydate" id="colinv_deliverydate"
										   value="<?php if ($collectinv->getDeliverydate()>0) echo date('d.m.Y',$collectinv->getDeliverydate()); else echo date('d.m.Y');?>">
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Vers. Kosten</label>
								<div class="col-sm-8">
									<div class="input-group">
										<input type="text" class="form-control" name="colinv_deliverycosts" id="colinv_deliverycosts" value="<?= printPrice($collectinv->getDeliveryCosts())?>">
										<span class="input-group-addon"><?= $_USER->getClient()->getCurrency()?></span>
									</div>
								</div>
							</div>
							<?if ($collectinv->getId()>0){?>
								<div class="form-group">
									<label for="" class="col-sm-4 control-label">Planung</label>
									<div class="col-sm-8 form-text">
										<?php if ($collectinv->getNeeds_planning()) { echo 'Ja'; } else { echo 'Nein'; }?>
									</div>
								</div>
							<?php }?>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Bem. Ext.</label>
								<div class="col-sm-8">
									<textarea name="colinv_extcomment" id="colinv_extcomment" class="form-control"><?php echo $collectinv->getExt_comment();?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label for="" class="col-sm-4 control-label">Bem. Int.</label>
								<div class="col-sm-8">
									<textarea name="colinv_comment" id="colinv_comment" class="form-control"><?php echo $collectinv->getComment();?></textarea>
								</div>
							</div>
						</div> <!-- ENDE COL RECHTS -->
					</div>
				</div>
			</div>

			<?php include 'collectiveinvoice.positions.php';?>

		</div> <!-- ENDE KOPF PANEL -->
	</form>
</div>



<input type="hidden" id="poscount" value="<?php echo $i;?>"/>



<script language="JavaScript">
	$(function(){
		$('#thirdparty').change(function(){
			if($(this).is(":checked")) {
				$('#thirdpartycomment_title').show();
				$('#thirdpartycomment').show();
			} else {
				$('#thirdpartycomment_title').hide();
				$('#thirdpartycomment').hide();
			}
		});
	});
</script>