<?//--------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       18.09.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
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
        
//         echo $all_positions[$tmp_index]->getId() . " wird zu " . $all_positions[$tmp_index+1]->getId() . "</br>";
//         echo $all_positions[$tmp_index+1]->getId() . " wird zu " . $all_positions[$tmp_index]->getId() . "</br>";
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

<!-- DataTables -->
<link rel="stylesheet" type="text/css" href="css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="css/dataTables.bootstrap.css">
<script type="text/javascript" charset="utf8" src="jscripts/datatable/jquery.dataTables.min.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/numeric-comma.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/datatable/dataTables.bootstrap.js"></script>
<script type="text/javascript" charset="utf8" src="jscripts/tinymce/tinymce.min.js"></script>

<script type="text/javascript">
$(document).ready(function() {
    var orders = $('#order_pos').DataTable( {
        "paging": true,
		"stateSave": false,
		"pageLength": -1,
		"dom": 't',
		"order": [[ 0, "asc" ]],
		"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "Alle"] ],
		"columns": [
		            { "orderable": false, "sortable": false, },
		            { "orderable": false, "sortable": false, },
		            { "orderable": false, "sortable": false, },
		            { "orderable": false, "sortable": false, },
		            { "orderable": false, "sortable": false, },
		            { "orderable": false, "sortable": false, },
		            { "orderable": false, "sortable": false, },
		            { "orderable": false, "sortable": false, },
		            { "orderable": false, "sortable": false, }
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
					}
    } );
    tinymce.init(
    	    {
        	    selector:'.poscomment',
        	    menubar: false,
        	    statusbar: false,
        	    toolbar: false,
        	    resize: true
    	    }
    	    );
} );
</script>
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
			'scrolling'     :   'no',
			'width'		    :	1000, 
			'height'        :   800,
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
</script>
<div id="hidden_clicker95" style="display:none"><a id="hiddenclicker_artframe" href="http://www.google.com" >Hidden Clicker</a></div>

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


function printPriceJs(zahl){
    //var ret = (Math.round(zahl * 100) / 100).toString(); //100 = 2 Nachkommastellen
    ret = zahl.toFixed(2);
    ret = ret.replace(".",",");
    return ret;
}


function updatePos(id_i){
	var tmp_type= document.getElementById('orderpos_type_'+id_i).value;

	if(tmp_type > 0){
		document.getElementById('orderpos_search_'+id_i).style.display= '';
		document.getElementById('orderpos_searchbutton_'+id_i).style.display= '';
		document.getElementById('orderpos_searchlist_'+id_i).style.display = '';
		document.getElementById('orderpos_uptpricebutton_'+id_i).style.display = 'none';
		if (tmp_type == 2){
			document.getElementById('orderpos_uptpricebutton_'+id_i).style.display = '';
		}
	} else {
		document.getElementById('orderpos_search_'+id_i).style.display= 'none';
		document.getElementById('orderpos_searchbutton_'+id_i).style.display= 'none';
		document.getElementById('orderpos_searchlist_'+id_i).style.display = 'none';
		document.getElementById('orderpos_uptpricebutton_'+id_i).style.display = 'none';
		document.getElementById('orderpos_quantity_'+id_i).value = "";
		document.getElementById('orderpos_comment_'+id_i).value = "";
		document.getElementById('orderpos_price_'+id_i).value = "";
	}
}

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

function updatePosDetails(id_i){
	var tmp_type = document.getElementById('orderpos_type_'+id_i).value;
	var tmp_objid= document.getElementById('orderpos_objid_'+id_i).value;

	if(tmp_type == 1){
		$.post("libs/modules/collectiveinvoice/collectiveinvoice.ajax.php", 
			{exec: 'getArticleDetails', articleid: tmp_objid}, 
			 function(data) {
				var teile = data.split("-+-+-");
				document.getElementById('orderpos_objid_'+id_i).value = teile[0];
				document.getElementById('orderpos_price_'+id_i).value = printPriceJs(parseFloat(teile[1]));
				document.getElementById('orderpos_tax_'+id_i).value = printPriceJs(parseFloat(teile[2]));
				document.getElementById('orderpos_comment_'+id_i).value = teile[3];
				document.getElementById('orderpos_comment_'+id_i).style.height = 100;
				$('#orderpos_comment_'+id_i+'_ifr').html(teile[3]);
				tinymce.editors[id_i].setContent(teile[3]);
				document.getElementById('span_totalprice_'+id_i).value = printPriceJs(parseFloat(teile[1]))+" <?=$_USER->getClient()->getCurrency()?>";
				updateArticlePrice(id_i);
				tinymce.editors[id_i].setContent(teile[3]);
		}); 
	}
	if(tmp_type == 2){
		$.post("libs/modules/collectiveinvoice/collectiveinvoice.ajax.php", 
			{exec: 'getArticleDetails', articleid: tmp_objid}, 
			 function(data) {
				var teile = data.split("-+-+-");
				document.getElementById('orderpos_objid_'+id_i).value = teile[0];
				document.getElementById('orderpos_price_'+id_i).value = printPriceJs(parseFloat(teile[1]));
				document.getElementById('orderpos_tax_'+id_i).value = printPriceJs(parseFloat(teile[2]));
				document.getElementById('orderpos_comment_'+id_i).value = teile[3];
				document.getElementById('orderpos_comment_'+id_i).style.height = 100;
				$('#orderpos_comment_'+id_i+'_ifr').html(teile[3]);
				tinymce.editors[id_i].setContent(teile[3]);
				document.getElementById('orderpos_quantity_'+id_i).value = "1";
				document.getElementById('span_totalprice_'+id_i).value = printPriceJs(parseFloat(teile[1]))+" <?=$_USER->getClient()->getCurrency()?>";
		}); 
	}
	if(tmp_type == 3){
		$.post("libs/modules/collectiveinvoice/collectiveinvoice.ajax.php", 
			{exec: 'getPersonalizationDetails', persoid: tmp_objid}, 
			 function(data) {
				var teile = data.split("-+-+-");
				document.getElementById('orderpos_objid_'+id_i).value = teile[0];
				document.getElementById('orderpos_price_'+id_i).value = printPriceJs(parseFloat(teile[1]));
				document.getElementById('orderpos_tax_'+id_i).value = printPriceJs(parseFloat(teile[2]));
				document.getElementById('orderpos_comment_'+id_i).value = teile[3];
				document.getElementById('orderpos_comment_'+id_i).style.height = 100;
				$('#orderpos_comment_'+id_i+'_ifr').html(teile[3]);
				tinymce.editors[id_i].setContent(teile[3]);
				document.getElementById('orderpos_quantity_'+id_i).value = "1";
				document.getElementById('span_totalprice_'+id_i).value = printPriceJs(parseFloat(teile[1]))+" <?=$_USER->getClient()->getCurrency()?>";
		}); 
	}
}

function updateArticlePrice(id_i){
	var tmp_objid= document.getElementById('orderpos_objid_'+id_i).value;
	var amount = document.getElementById('orderpos_quantity_'+id_i).value;
	var type = $('#orderpos_type_'+id_i).val();
	
	$.post("libs/modules/collectiveinvoice/collectiveinvoice.ajax.php", 
		{exec: 'getArticlePrice', articleid: tmp_objid, amount: amount}, 
		 function(data) {
			document.getElementById('orderpos_price_'+id_i).value = printPriceJs(parseFloat(data));
			if (type==1)
				document.getElementById('span_totalprice_'+id_i).innerHTML = printPriceJs(parseFloat(data))+" <?=$_USER->getClient()->getCurrency()?>";
			else
				document.getElementById('span_totalprice_'+id_i).innerHTML = printPriceJs(parseFloat(data) * amount)+" <?=$_USER->getClient()->getCurrency()?>";
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

function addArticle(artid)
{
	$.post("libs/modules/collectiveinvoice/collectiveinvoice.ajax.php", 
			{ajax_action: 'getArtData', artid: artid}, 
			 function(data) {
				var data = data[0];
				addPositionRow(data.type,data.id,data.title,data.orderamounts,data.orderid)
// 				document.getElementById('colinv_deliverycosts').value = data;
	}); 
}

function addPositionRow(type,objectid,label,orderamounts,orderid){
	$('.dataTables_empty').parent().remove();
	var count = parseInt($('#poscount').val());
    var newrow = "";
    newrow += '<tr>';
    newrow += '<td valign="top" class="content_row">&nbsp;</td>';
    newrow += '<td valign="top" class="content_row">';
    switch (type) {
    case 0:
    	newrow += 'Manuell';
        break;
    case 1:
    	newrow += 'Artikel (Kalk)';
        break;
    case 2:
    	newrow += 'Artikel';
        break;
    case 3:
    	newrow += 'Perso';
        break;
    } 
    newrow += '<input type="hidden" name="orderpos['+count+'][id]" value="">';
    newrow += '<input type="hidden" name="orderpos['+count+'][orderid]" id="orderpos_orderid_'+count+'" value="'+orderid+'">';
	newrow += '<input type="hidden" name="orderpos['+count+'][obj_id]" id="orderpos_objid_'+count+'" value="'+objectid+'">';
	newrow += '<input type="hidden" name="orderpos['+count+'][type]" id="orderpos_type_'+count+'" value="'+type+'"></td>';
	newrow += '<td valign="top" class="content_row"><span id="orderpos_name_'+count+'">'+label+'</br></span>';
	newrow += '<textarea name="orderpos['+count+'][comment]" id="orderpos_comment_'+count+'" class="text poscomment" style="width: 440px; height: 100px" ></textarea>';
	newrow += '</td><td valign="top" class="content_row">';
	if (orderamounts.length>0)
	{
	    newrow += '<select name="orderpos['+count+'][quantity]" id="orderpos_quantity_'+count+'" style="width: 60px" onchange="updateArticlePrice('+count+')">';
	    orderamounts.forEach(function(entry) {
	        newrow += '<option value="'+entry+'">'+entry+'</option>';
	    });
	    newrow += '</select>';
	} else {
		newrow += '<input 	name="orderpos['+count+'][quantity]" id="orderpos_quantity_'+count+'" value="1" style="width: 60px" onfocus="markfield(this,0)" onblur="markfield(this,1)">';
	}
    newrow += ' Stk.<br/> &ensp;&ensp;&ensp;';
    newrow += '<img src="images/icons/arrow-circle-double-135.png" class="pointer" id="orderpos_uptpricebutton_'+count+'"';
	newrow += 'onclick="updateArticlePrice('+count+')"';
	if (type != 2 && type != 1)
		newrow += ' style="display:none" ';
	newrow += ' title="Staffelpreis aktualisieren">';
	newrow += '</td><td valign="top" class="content_row"><input name="orderpos['+count+'][price]" id="orderpos_price_'+count+'" value=""'; 
	newrow += 'style="width: 60px" onfocus="markfield(this,0)" onblur="markfield(this,1)">';
    newrow += '<?= $_USER->getClient()->getCurrency()?></td><td valign="top" class="content_row">';
	newrow += '<input name="orderpos['+count+'][tax]" id="orderpos_tax_'+count+'" value="19,00" style="width: 60px"'; 
	newrow += 'onfocus="markfield(this,0)" onblur="markfield(this,1)"> %</td>';
	newrow += '<td valign="top" class="content_row"><span id="span_totalprice_'+count+'"></span>&nbsp;</td>';
	newrow += '<td valign="top" class="content_row">';
	newrow += '<input type="checkbox" checked value="1" name="orderpos['+count+'][inv_rel]">';		
	newrow += '</td><td valign="top" class="content_row">';		
	newrow += '<input type="checkbox" checked value="1" name="orderpos['+count+'][rev_rel]">';	
	newrow += '</td><td class="content_row" valign="top"><img src="images/icons/cross-script.png" onclick="$(this).parent().parent().remove(); $(\'#poscount\').val($(\'#poscount\').val()-1);" class="pointer" title="Position löschen">&ensp;</td></tr>';
    $('#poscount').val(count+1);
    $('#order_pos').append(newrow);
    if (type != 0)
    	updatePosDetails(count);
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
// 		    'fitToView'     :   true,
// 		    'autoSize'      :   true,
// 		    'autoCenter'    :   true,
// 		    'height'        :   'auto',
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

<?//--------------------------------------HTML ----------------------------------------?>
<?if ($collectinv->getId() >0){

	$all_bc_cp = ContactPerson::getAllContactPersons($collectinv->getBusinesscontact());?>
  <table border="0" cellpadding="2" cellspacing="0" width="100%">
        <tbody>
        	<tr>
                <td width="100%" align="left">
                    <div class="btn-group" role="group">
                      <button type="button" onclick="window.location='index.php?page=<?=$_REQUEST['page']?>&exec=docs&ciid=<?=$collectinv->getId()?>';" class="btn btn-sm btn-default">Dokumente</button>
                      <button type="button" onclick="window.location='index.php?page=<?=$_REQUEST['page']?>&exec=notes&ciid=<?=$collectinv->getId()?>';" class="btn btn-sm btn-default"><?php if ($collectinv->getId()>0) echo '<span id="notify_count" class="badge">'.Comment::getCommentCountForObject("CollectiveInvoice", $collectinv->getId()).'</span>';?>Notizen</button>
                      <?php 
                      $association_object = $collectinv;
                      $associations = Association::getAssociationsForObject(get_class($association_object), $association_object->getId());
                      ?>
                      <script type="text/javascript">
                      function removeAsso(id)
                      {
                    	  $.ajax({
                        		type: "POST",
                        		url: "libs/modules/associations/association.ajax.php",
                        		data: { ajax_action: "delete_asso", id: id }
                        		})
                      }
                      </script>
                      <div class="btn-group dropdown">
                      <button type="button" class="btn btn-sm dropdown-toggle btn-default" data-toggle="dropdown" aria-expanded="false">
                        Verknüpfungen <span class="badge"><?php echo count($associations);?></span> <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu" role="menu">
                        <?php 
                            if (count($associations)>0){
                                $as = 0;
                                foreach ($associations as $association){
                                    if ($association->getModule1() == get_class($association_object) && $association->getObjectid1() == $association_object->getId()){
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
                                    echo '<li id="as_'.$as.'"><a href="index.php?page='.$link_href.$object->getId().'">';
                                    echo $object_name;
                                    echo '</a>';
                                    if ($_USER->isAdmin() || $_USER->hasRightsByGroup(Group::RIGHT_ASSO_DELETE))
                                        echo '<img class="pointer" src="images/icons/cross.png" onclick=\'removeAsso('.$association->getId().'); $("#as_'.$as.'").remove();\'/>';
                                    echo '</li>';
                                    $as++;
                                }
                            }
                            echo '<li class="divider"></li>';
                            echo '<li><a href="#" onclick="callBoxFancyAsso(\'libs/modules/associations/association.frame.php?module='.get_class($association_object).'&objectid='.$association_object->getId().'\');">Neue Verknüpfung</a></li>';
                        ?>
                      </ul>
                      </div>
                      <?php if ($collectinv->getId()>0){?>

					  <div class="btn-group dropdown" style="margin-left: 0px;">
						  <button type="button" class="btn btn-sm dropdown-toggle btn-default" data-toggle="dropdown" aria-expanded="false">
							  Vorschau <span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu" role="menu">
							  <li>
								  <a href="#" onclick="callBoxFancyPreview('libs/modules/collectiveinvoice/collectiveinvoice.preview.php?ciid=<?php echo $collectinv->getId();?>&type=1');">Angebot</a>
                                  <a href="#" onclick="callBoxFancyPreview('libs/modules/collectiveinvoice/collectiveinvoice.preview.php?ciid=<?php echo $collectinv->getId();?>&type=2');">Auftragsbestätigung</a>
                                  <a href="#" onclick="callBoxFancyPreview('libs/modules/collectiveinvoice/collectiveinvoice.preview.php?ciid=<?php echo $collectinv->getId();?>&type=5');">Auftragstasche</a>
                                  <a href="#" onclick="callBoxFancyPreview('libs/modules/collectiveinvoice/collectiveinvoice.preview.php?ciid=<?php echo $collectinv->getId();?>&type=3');">Lieferschein</a>
                                  <a href="#" onclick="callBoxFancyPreview('libs/modules/collectiveinvoice/collectiveinvoice.preview.php?ciid=<?php echo $collectinv->getId();?>&type=15');">Etiketten</a>
                                  <a href="#" onclick="callBoxFancyPreview('libs/modules/collectiveinvoice/collectiveinvoice.preview.php?ciid=<?php echo $collectinv->getId();?>&type=4');">Rechnung</a>
                                  <a href="#" onclick="callBoxFancyPreview('libs/modules/collectiveinvoice/collectiveinvoice.preview.php?ciid=<?php echo $collectinv->getId();?>&type=7');">Gutschrift</a>
							  </li>
						  </ul>
					  </div>

                      <div class="btn-group dropdown" style="margin-left: 0px;">
                          <button type="button" class="btn btn-sm dropdown-toggle btn-default" data-toggle="dropdown" aria-expanded="false">
                            Neu <span class="caret"></span>
                          </button>
                          <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="#" onclick="askDel('index.php?page=libs/modules/tickets/ticket.php&exec=new&customer=<?php echo $collectinv->getCustomer()->getId();?>&contactperson=<?php echo $collectinv->getCustContactperson()->getId();?>&asso_class=<?php echo get_class($collectinv);?>&asso_object=<?php echo $collectinv->getId()?>&tkt_title=<?php echo $collectinv->getNumber().' - '.$collectinv->getTitle();?>');">Ticket erstellen (verknüpft)</a>
                                </li>
                          </ul>
                      </div>
                      <?php }?>
                  </div>
                </td>
        	</tr>
        </tbody>
  </table>
<?php }?>
<div class="box1" <?if ($collectinv->getId() >0){?>style="margin-top:50px;"<?}?>>
	<table width="100%">
		<tr>
			<td  class="content_row_header">
				<?= $_LANG->get('Kundendaten')?>
			</td>
			<td align="right">
			<? echo $savemsg; ?>
			</td>
		</tr>
	</table>
	<table width'="100%">
		<colgroup>
			<col width="180">
			<col width="350">
			<col width="180">
			<col width="300">
		</colgroup>
		<tr>
			<td class="content_row_header">
				<?= $_LANG->get('Firmenname') ?>
			</td>
			<td class="content_row_clear">
				<a href="index.php?page=libs/modules/businesscontact/businesscontact.php&exec=edit&id=<?=$selected_customer->getId()?>"><?= $selected_customer->getNameAsLine()?></a>			
			</td>
			<td class="content_row_header">
				<?= $_LANG->get('E-Mail') ?>
			</td>
			<td class="content_row_clear">
				<?= $selected_customer->getEmail()?>
			</td>
		</tr>
		<tr>
			<td class="content_row_header">
				<?= $_LANG->get('Vor-/Nachname') ?>
			</td>
			<td class="content_row_clear">			
			</td>
			<td class="content_row_header">
				<?= $_LANG->get('Telefon') ?>
			</td>
			<td class="content_row_clear">
				<?= $selected_customer->getPhone()?>
			</td>
		</tr><tr>
			<td class="content_row_header">
				<?= $_LANG->get('Strasse')?>
			</td>
			<td class="content_row_clear">
				<?= $selected_customer->getAddress1()?>			
			</td>
			<td class="content_row_header">
				<?= $_LANG->get('Fax')?>
			</td>
			<td class="content_row_clear">
				<?= $selected_customer->getFax()?>
			</td>
		</tr><tr>
			<td class="content_row_header">
				<?= $_LANG->get('Postleitzahl/Ort')?>
			</td>
			<td class="content_row_clear">
				<?= $selected_customer->getZip()." ".$selected_customer->getCity()?>
			</td>
			<td class="content_row_header">
				<?= $_LANG->get('Webseite')?>
			</td>
			<td class="content_row_clear">
				<?= $selected_customer->getWeb()?>
			</td>
		</tr><tr>
			<td class="content_row_header">
				<?= $_LANG->get('Land')?>
			</td>
			<td class="content_row_clear">
				<?= $selected_customer->getCountry()->getName()?>			
			</td>
			<td class="content_row_header">
				<?= $_LANG->get('Bemerkungen')?>
			</td>
			<td class="content_row_clear">
				<?= $selected_customer->getComment() ?>
			</td>
		</tr>
	</table>
</div>
<br/>
	
<div id="fl_menu">
	<div class="label">Quick Move</div>
	<div class="menu">
        <a href="#top" class="menu_item">Seitenanfang</a>
        <a href="index.php?page=<?=$_REQUEST['page']?>" class="menu_item">Zurück</a>
        <a href="#" class="menu_item" onclick="$('#form_collectiveinvoices').submit();">Speichern</a>
        <? if($_USER->hasRightsByGroup(Group::RIGHT_DELETE_COLINV) || $_USER->isAdmin()){ ?>
            <a href="#" class="menu_item_delete" onclick="askDel('index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=delete&del_id=<?php echo $collectinv->getId();?>')">Löschen</a>
        <?}?>
    </div>
</div>
	
<?//--------------------------Beginn Formular----------------------------?>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="form_collectiveinvoices" name="form_collectiveinvoices" onsubmit="return checkform(new Array(colinv_title))">
	
	<input 	type="hidden" name="exec" value="save">
	<input 	type="hidden" name="ciid" value="<?=$collectinv->getId()?>">
	
    <input type="hidden" name="asso_class" value="<?php echo $_REQUEST["asso_class"];?>"> 
    <input type="hidden" name="asso_object" value="<?php echo $_REQUEST["asso_object"];?>"> 


	<input 	type="hidden" name="colinv_businesscontact"  value="<?=$collectinv->getBusinesscontact()->getId()?>">  

<div class="box1">
	<table width="100%">
		<tr>
			<td class="content_row_header">
			<?= $_LANG->get('Vorgangskopfdaten') ?>
			</td>
		</tr>
	</table>
	<table width'="100%">
		<colgroup>
			<col width="180">
			<col width="350">
			<col width="180">
			<col width="300">
		</colgroup>
		<tr>
			<td class="content_header"><?= $_LANG->get('Vorgangstitel')?></td>
			<td class="content_row_clear" colspan="3">
				<input name="colinv_title" style="width: 850px" class="text" 
				value="<?= $collectinv->getTitle()?>" 
				onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
			<td class="content_row"><?=$_LANG->get('Vorgangsnummer')?></td>
			<td class="content_row"><?=$collectinv->getNumber()?></td>
			<td class="content_row"><?=$_LANG->get('Erstellt am')?></td>
			<td class="content_row">
				<?if ($collectinv->getCrtdate() != 0) echo date("d.m.Y H:i:s",$collectinv->getCrtdate())?>
			</td>
		</tr>
		<tr>
			<td class="content_row"><?= $_LANG->get('Status')?></td>
			<td class="content_row">
				<table>
					<tr>
						<td>
							<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?= $collectinv->getId() ?>&exec=setState2&state=1">
	            				<img class="select" title="<?php echo getOrderStatus(1);?>" src="./images/status/<?
	            				if($collectinv->getStatus() == 1)
	            	    				echo 'red.gif';
					            	else
										echo 'black.gif'; ?>">
	                		</a>
	                	</td>
	                	<td>
							<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=setState2&state=2">
	            				<img class="select" title="<?php echo getOrderStatus(2);?>" src="./images/status/<?
	            					if($collectinv->getStatus() == 2)
					                	echo 'orange.gif';
					                else
					                	echo 'black.gif';?>">
							</a>
						</td>
						<td>
							<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=setState2&state=3">
								<img class="select" title="<?php echo getOrderStatus(3);?>" src="./images/status/<?
									if($collectinv->getStatus() == 3)
					                	echo 'yellow.gif';
					                else
					                	echo 'black.gif'; ?>">
							</a>
						</td>
						<td>
							<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=setState2&state=4">
	            				<img class="select" title="<?php echo getOrderStatus(4);?>" src="./images/status/<?
	            					if($collectinv->getStatus() == 4)
	            						echo 'lila.gif';
									else
										echo 'black.gif';?>">
							</a>
						</td>
						<td>
							<a href="index.php?page=<?=$_REQUEST['page']?>&ciid=<?=$collectinv->getId()?>&exec=setState2&state=5">
	            				<img class="select" title="<?php echo getOrderStatus(5);?>" src="./images/status/<?
	            					if($collectinv->getStatus() == 5)
	            						echo 'green.gif';
									else
										echo 'black.gif';?>">
							</a>
						</td>
                        <td>
                            &nbsp;<?=getOrderStatus($collectinv->getStatus(), true)?>
                        </td>
					</tr>
				</table>
			</td>
			<td class="content_row"><?= $_LANG->get('Erstellt von')?></td>
			<td class="content_row">
				<?if ($collectinv->getCrtuser()->getId() != 0){
					echo $collectinv->getCrtuser()->getNameAsLine();
				}?>
			</td>
		</tr>
		<tr>
			<td class="content_row">&ensp;</td>
			<td class="content_row">&ensp;</td>
			<td class="content_row">
				<?= $_LANG->get('Ge&auml;ndert am')?>
			</td>
			<td class="content_row">
				<?if ($collectinv->getUptdate() != 0) echo date("d.m.Y H:i:s",$collectinv->getUptdate())?>
			</td>
		</tr>
		<tr>
			<td class="content_row"><?= $_LANG->get('Zahlungsart')?></td>
			<td class="content_row" colspawn="7">
				<select name="colinv_paymentterm" style="width: 300px" class="text" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
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
			</td> 
			<td class="content_row"><?=$_LANG->get('Ge&auml;ndert von')?></td>
			<td class="content_row">
				<?if ($collectinv->getUptuser()->getId()){
					echo $collectinv->getUptuser()->getNameAsLine();
				}?>
			</td>
		</tr>
		<tr>
			<td class="content_row"><?= $_LANG->get('Lieferadresse')?></td>
			<td class="content_row" colspawn="7">
				<select name="colinv_deliveryadress" style="width: 300px" class="text" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
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
			</td> 
			<td class="content_row" valign="top">
				<?=$_LANG->get('Lieferdatum')?>
			</td>
			<td class="content_row"  valign="top">
				<input 	name="colinv_deliverydate" id="colinv_deliverydate" style="width: 80px" class="text" 
						value="<?php if ($collectinv->getDeliverydate()>0) echo date('d.m.Y',$collectinv->getDeliverydate()); else echo date('d.m.Y');?>" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>
            <td class="content_row" valign="top"><b><?=$_LANG->get('Rechnungsadresse')?></b></td>
            <td class="content_row" valign="top">
               <select name="invoice_address" style="width:300px" class="text">
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
            </td>
			<td class="content_row" valign="top" rowspan="2">
				<?= $_LANG->get('Bemerkungen (intern)')?>
			</td>
			<td class="content_row"  rowspan="2">
				<textarea name="colinv_comment" style="width: 300px; height: 40px"
						  onfocus="markfield(this,0)" onblur="markfield(this,1)"><?= $collectinv->getComment()?></textarea>
			</td>
		</tr>
		<tr>
			<td class="content_row"><?=$_LANG->get('Versandart')?></td>
			<td class="content_row" colspawn="7">
				<select name="colinv_deliveryterm" style="width: 300px" class="text" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)"
						onchange="updateDeliveryPrice()">
						<option value="0"> &lt; <?=$_LANG->get('Bitte w&auml;hlen') ?> 	&gt;</option>
					<?foreach($alldeliverycondition as $delcon){
							echo '<option value="' . $delcon->getId() . '"';
							if ($delcon->getId() == $collectinv->getDeliveryTerm()->getID()){ echo 'selected="selected" ';} 
							echo ">".$delcon->getName1() . "</option>";
						}?>
				</select>
			</td>
		</tr>
		<tr>
			<td class="content_row"><?= $_LANG->get('Versandkosten')?></td>
			<td class="content_row">
				<input 	name="colinv_deliverycosts" id="colinv_deliverycosts" style="width: 80px" class="text" 
						value="<?= printPrice($collectinv->getDeliveryCosts())?>" 
						onfocus="markfield(this,0)" onblur="markfield(this,1)">
						<?= $_USER->getClient()->getCurrency()?>
			</td>
			<td class="content_row" valign="top" rowspan="2">
				<?= $_LANG->get('Bemerkungen (extern)')?>
			</td>
			<td class="content_row"  rowspan="2">
				<textarea name="colinv_extcomment" style="width: 300px; height: 40px"
						  onfocus="markfield(this,0)" onblur="markfield(this,1)"><?= $collectinv->getExt_comment()?></textarea>
			</td>
		</tr>
		<tr>
			<td class="content_row" valign="top">
				<?= $_LANG->get('Zweck')?> / <?= $_LANG->get('Kostenstelle')?> <br/> <?= $_LANG->get('des Kunden')?>
			</td>
			<td class="content_row"  valign="top">
				<input 	name="colinv_intent" id="colinv_intent" style="width: 300px" class="text" 

						value="<?=$collectinv->getIntent()?>" onfocus="markfield(this,0)" onblur="markfield(this,1)">
			</td>
		</tr>
		<tr>    
		    <td class="content_row" valign="top"><b><?=$_LANG->get('Dokumente')?> - <?=$_LANG->get('Ihre Nachricht')?></b></td>
            <td class="content_row" valign="top">
                <input name="cust_message" id="cust_message" style="width:300px"
                    value="<?=$collectinv->getCustMessage()?>">
            </td>
			<td class="content_row" valign="top">
				<?=$_LANG->get('Benötigt Planung')?>
			</td>
			<td class="content_row"  valign="top">
				<?php if ($collectinv->getNeeds_planning()) { echo 'Ja'; } else { echo 'Nein'; }?>
			</td>
        </tr>
        <tr>
        	<td class="content_row" valign="top"><b><?=$_LANG->get('Dokumente')?> - <?=$_LANG->get('Ihr Zeichen')?></b></td>
            <td class="content_row" valign="top">
                <input name="cust_sign" id="cust_sign" style="width:300px"
                    value="<?=$collectinv->getCustSign()?>">
            </td>
            <?php if ($collectinv->getId()>0){?>
			<td class="content_row" valign="top">
				<?=$_LANG->get('Merkmale')?>
			</td>
			<td class="content_row"  valign="top">
				<span class="pointer" onclick="callBoxFancyArtFrame('libs/modules/collectiveinvoice/collectiveinvoice.attribute.frame.php?ciid=<?php echo $collectinv->getId();?>');"><a>anzeigen</a> (<?php echo count($attributes);?>)</span>
			</td>
			<?php }?>
        </tr>
        <tr>
            <td class="content_row" valign="top"><b><?=$_LANG->get('Ansprechpartner')?></b></td>
            <td class="content_row" valign="top">
                <select name="intern_contactperson" style="width:300px" class="text">
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
            </td>
        </tr>
        <tr>
            <td class="content_row" valign="top"><b><?=$_LANG->get('Ansprechp. d. Kunden')?></b></td>
            <td class="content_row" valign="top">
                <select name="custContactperson" style="width:300px" class="text">
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
            </td>
        </tr>
	</table>
</div>
<br/>

<div class="box2">
    <div class="box1">
        <b>neue Position:</b>
        <img src="images/icons/plus.png" title="neue manuelle Position" class="pointer" onclick="addPositionRow(0,0,'Manuell',0,0);"/> 
        <img src="images/icons/plus.png" title="neuer Artikel" class="pointer" onclick="callBoxFancyArtFrame('libs/modules/collectiveinvoice/collectiveinvoice.articleselector.php');"/>
    </div>
    <table id="order_pos" width="100%" cellpadding="0" cellspacing="0" class="stripe hover row-border order-column">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Typ</th>
                    <th>Beschreibung</th>
                    <th>Menge</th>
                    <th>Preis</th>
                    <th>Steuer</th>
                    <th>Betrag</th>
                    <th>Dok.-rel.</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
    		<? $i = 0;
    		if(count($collectinv->getPositions(true))>0){
    			foreach($collectinv->getPositions(true) as $position){?>
    				<tr<?php if ($position->getStatus() == 2) echo ' style="background-color:rgba(255, 0, 0, 0.5);"';?>>
    					<td valign="top" class="content_row"><?php echo $position->getId();?></td>
    					<td valign="top" class="content_row">
        					<?php 
        					switch ($position->getType())
        					{
                                case 0: 
                                    echo "Manuell"; 
                                    break;
                                case 1: 
                                    echo "Artikel (Kalk)"; 
                                    break;
                                case 2: 
                                    echo "Artikel"; 
                                    break;
                                case 3: 
                                    echo "Perso"; 
                                    break;
                                default: 
                                    echo "kein"; 
                                    break;
        					}   
        					?>
        					<input type="hidden" name="orderpos[<?=$i?>][id]" value="<?= $position->getId()?>">
        					<input type="hidden" name="orderpos[<?=$i?>][obj_id]" id="orderpos_objid_<?=$i?>" value="<?= $position->getObjectid()?>">
        					<input type="hidden" name="orderpos[<?=$i?>][type]" id="orderpos_type_<?=$i?>" value="<?= $position->getType()?>">
    					</td>
    					<td valign="top" class="content_row">
    					    <span id="orderpos_name_<?=$i?>">
    					    <?php
    					    if($position->getType() == 1){
    					        $tmp_art = new Article($position->getObjectid());
    					        echo "<b>".$tmp_art->getTitle()."</b></br>";
    					        echo '<input type="hidden" name="orderpos['.$i.'][orderid]" id="orderpos_orderid_'.$i.'" value="'.$tmp_art->getOrderid().'">';
    					    }
    					    if($position->getType() == 2){
    					        $tmp_art = new Article($position->getObjectid());
    					        echo "<b>".$tmp_art->getTitle()."</b></br>";
    					    }
    					    if($position->getType() == 3){
    					        $tmp_perso = new Personalization($position->getObjectid());
    					        echo "<b>".$tmp_perso->getTitle()."</b></br>";
    					    } 
    					    ?>
    					    </span>
    						<textarea name="orderpos[<?=$i?>][comment]" class="text poscomment" id="orderpos_comment_<?=$i?>"
    								  style="width: 440px; height: 100px"><?=$position->getComment()?></textarea>
    					</td>
    					<td valign="top" class="content_row">
    					<?php
                        	if ($position->getType() == 1 || $position->getType() == 2)
                        	{
                        	    if (count($tmp_art->getOrderamounts())>0)
                        	    {
                            	    echo '<select name="orderpos['.$i.'][quantity]" id="orderpos_quantity_'.$i.'" style="width: 60px" onchange="updateArticlePrice('.$i.')">';
                            	    foreach ($tmp_art->getOrderamounts() as $orderamount)
                            	    {
                            	        echo '<option value="'.$orderamount.'" ';
                            	        if ($position->getQuantity() == $orderamount)
                            	            echo ' selected ';
                            	        echo ' >'.$orderamount.'</option>';
                            	    }
                            	    echo '</select>';
                        	    } else {
                        	        echo '<input name="orderpos['.$i.'][quantity]" id="orderpos_quantity_'.$i.'" value="'.printPrice($position->getQuantity(),2).'" style="width: 60px" onfocus="markfield(this,0)" onblur="markfield(this,1)">';
                        	    }
                        	} else {
                        		echo '<input name="orderpos['.$i.'][quantity]" id="orderpos_quantity_'.$i.'" value="'.printPrice($position->getQuantity(),2).'" style="width: 60px" onfocus="markfield(this,0)" onblur="markfield(this,1)">';
                        	}
    					?>
    						<?=$_LANG->get('Stk.')?> <br/>
    						&ensp;&ensp;&ensp;
    						<img src="images/icons/arrow-circle-double-135.png" class="pointer" id="orderpos_uptpricebutton_<?=$i?>"
    							 onclick="updateArticlePrice(<?=$i?>)" title="<?=$_LANG->get('Staffelpreis aktualisieren')?>"
    							 <?if($position->getType() == 3) echo 'style="display:none"';?>>
    					</td>
    					<td valign="top" class="content_row">
    						<input 	name="orderpos[<?=$i?>][price]" id="orderpos_price_<?=$i?>" class="text"
    								value="<?= printPrice($position->getPrice())?>" style="width: 60px" 
    								onfocus="markfield(this,0)" onblur="markfield(this,1)"> 
    						<?=$_USER->getClient()->getCurrency()?>
    					</td>
    					<td valign="top" class="content_row">
    						<input 	name="orderpos[<?=$i?>][tax]" class="text" id="orderpos_tax_<?=$i?>"
    								value="<?= printPrice($position->getTax())?>" style="width: 60px;" > %
    					</td>
    					<td valign="top" class="content_row">
    						<span id="span_totalprice_<?=$i?>"><?= printPrice($position->getNetto())." ". $_USER->getClient()->getCurrency()?></span>
    					</td>
    					<td valign="top" class="content_row">
    						<input type="checkbox" value="1" name="orderpos[<?=$i?>][inv_rel]"
    						<?if ($position->getInvrel() == 1) echo "checked";?>>
    					</td>
    					<td valign="top" class="content_row">
                            <?php if ($position->getStatus() == 2){?>
    						<a href="index.php?page=<?=$_REQUEST['page']?>&exec=restorepos&ciid=<?=$_REQUEST["ciid"]?>&delpos=<?=$position->getId()?>">
    							<img src="images/icons/asterisk.png" title="<?= $_LANG->get('Wiederherstellen')?>"></a>
    						<a href="index.php?page=<?=$_REQUEST['page']?>&exec=deletepos&ciid=<?=$_REQUEST["ciid"]?>&delpos=<?=$position->getId()?>">
    							<img src="images/icons/cross-script.png" title="<?= $_LANG->get('Endgültig löschen')?>"></a>
    					    <?php } else {?>
    						<a href="index.php?page=<?=$_REQUEST['page']?>&exec=softdeletepos&ciid=<?=$_REQUEST["ciid"]?>&delpos=<?=$position->getId()?>">
    							<img src="images/icons/cross-script.png" title="<?= $_LANG->get('Vorrübergehend löschen')?>"></a>
    						<?
    						} 
    						if ($i == 0){
    						    echo '<a href="index.php?page='.$_REQUEST['page'].'&exec=edit&subexec=movedown&ciid='.$_REQUEST['ciid'].'&posid='.$position->getId().'">
                                      <img src="images/icons/arrow-270.png" title="nach unten bewegen"></a>';
    						} else if ($i+1 >= count($collectinv->getPositions())){
    						    echo '<a href="index.php?page='.$_REQUEST['page'].'&exec=edit&subexec=moveup&ciid='.$_REQUEST['ciid'].'&posid='.$position->getId().'">
                                      <img src="images/icons/arrow-090.png" title="nach oben bewegen"></a>';
    			            } else {
    						    echo '<a href="index.php?page='.$_REQUEST['page'].'&exec=edit&subexec=movedown&ciid='.$_REQUEST['ciid'].'&posid='.$position->getId().'">
                                      <img src="images/icons/arrow-270.png" title="nach unten bewegen"></a>';
    						    echo '<a href="index.php?page='.$_REQUEST['page'].'&exec=edit&subexec=moveup&ciid='.$_REQUEST['ciid'].'&posid='.$position->getId().'">
                                      <img src="images/icons/arrow-090.png" title="nach oben bewegen"></a>';
    			            }
    			            if ($position->getFile_attach()>0){
    			                $tmp_attach = new Attachment($position->getFile_attach());
    			                echo '<a href="'.Attachment::FILE_DESTINATION.$tmp_attach->getFilename().'" download="'.$tmp_attach->getOrig_filename().'">
                                      <img src="images/icons/disk--arrow.png" title="Angehängte Datei herunterladen"></a>';
    			            } elseif ($position->getPerso_order()>0){
    			                echo '</br>';
    			                $perso_order = new Personalizationorder($position->getPerso_order());
    			                $docs = Document::getDocuments(Array("type" => Document::TYPE_PERSONALIZATION_ORDER,
    			                                                     "requestId" => $perso_order->getId(),
    			                                                     "module" => Document::REQ_MODULE_PERSONALIZATION));
    			                if (count($docs) > 0)
    			                {
    			                    $tmp_id = $_USER->getClient()->getId();
    			                    $hash = $docs[0]->getHash();
        			                echo '<a class="icon-link" target="_blank" href="./docs/personalization/'.$tmp_id.'.per_'.$hash.'_e.pdf">
                                          <img src="images/icons/application-browser.png" title="Download mit Hintergrund" alt="Download"></a>
        			                      <a href="./docs/personalization/'.$tmp_id.'.per_'.$hash.'_p.pdf" class="icon-link" target="_blank">
        			                      <img src="images/icons/application.png" title="Download ohne Hintergrund" alt="Download"></a></br>';
    			                }
    			            }
    						    
    						?>
    					</td>
    				</tr>
    			<?
    			$i++;
    		    }
    		}?>
    </table>
</div>

<br/>

<input type="hidden" id="poscount" value="<?php echo $i;?>"/>
</form>