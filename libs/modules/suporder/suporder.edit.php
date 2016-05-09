<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/suporder/suporder.class.php';

if ($_REQUEST["subexec"] == "save"){
    $array = [
        'supplier' => $_REQUEST["supplier"],
        'title' => $_REQUEST["title"],
        'eta' => (int)strtotime($_REQUEST["eta"]),
        'paymentterm' => $_REQUEST["paymentterm"],
        'status' => $_REQUEST["status"],
        'invoiceaddress' => 0,
        'deliveryaddress' => 0,
        'crtdate' => time(),
        'crtuser' => $_USER->getId(),
        'cpinternal' => $_REQUEST["cpinternal"],
        'cpexternal' => $_REQUEST["cpexternal"],
    ];
    if ($_REQUEST['id']==0)
        $array['number'] = $_USER->getClient()->createOrderNumber(Client::NUMBER_SUPORDER);
    $suporder = new SupOrder((int)$_REQUEST["id"], $array);
    $suporder->save();
    $_REQUEST["id"] = $suporder->getId();
}

if ($_REQUEST["exec"] == "edit"){
    $suporder = new SupOrder($_REQUEST["id"]);
    $header_title = "Bestellung editieren";
} else {
    $suporder = new SupOrder();
    $header_title = "Bestellung erstellen";
}

$payterms = PaymentTerms::getAllPaymentTerms();
$suppliers = BusinessContact::getAllBusinessContacts(BusinessContact::ORDER_NAME,' AND supplier = 1 ');

?>
<script src="jscripts/jvalidation/dist/jquery.validate.min.js"></script>
<script src="jscripts/jvalidation/dist/localization/messages_de.min.js"></script>
<link rel="stylesheet" type="text/css" href="jscripts/datetimepicker/jquery.datetimepicker.css"/ >
<script src="jscripts/datetimepicker/jquery.datetimepicker.js"></script>


<div id="fl_menu">
    <div class="label">Quick Move</div>
    <div class="menu">
        <a href="#top" class="menu_item">Seitenanfang</a>
        <a href="index.php?page=libs/modules/suporder/suporder.overview.php" class="menu_item">Zurück</a>
        <a href="#" class="menu_item" onclick="$('#suporder_form').submit();">Speichern</a>
        <?php if ($suporder->getId() > 0){ ?>
        <a href="#" class="menu_item_delete" onclick="askDel('index.php?page=libs/modules/suporder/suporder.overview.php&exec=delete&id=<?=$suporder->getId()?>');">Löschen</a>
        <?php } ?>
    </div>
</div>

<?php if (isset($savemsg)) { ?>
<div class="alert">
	<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	<strong>Hinweis!</strong> <?=$savemsg?>
</div>
<?php } ?>

<div class="row">
    <div class="col-md-12">
    	<div class="panel panel-default">
    		  <div class="panel-heading">
    				<h3 class="panel-title">
                        <?=$header_title?>
                        <?php if ($suporder->getId()>0){?>
                        <span class="pull-right">
                            <button class="btn btn-xs btn-default" onclick="window.open('libs/modules/suporder/suporder.print.php?id=<?php echo $suporder->getId();?>');">Drucken</button>
                        </span>
                        <?php } ?>
                    </h3>
    		  </div>
    		  <div class="panel-body">
    				<form action="index.php?page=<?=$_REQUEST['page']?>" id="suporder_form" name="suporder_form" method="post" role="form" class="form-horizontal">
                        <input type="hidden" id="id" name="id" value="<?=$suporder->getId()?>" />
                        <input type="hidden" id="exec" name="exec" value="edit" />
                        <input type="hidden" id="subexec" name="subexec" value="save" />

                        <table border="0" cellpadding="3" cellspacing="1" width="100%">
                            <colgroup>
                                <col width="130">
                                <col>
                            </colgroup>
                            <tr>
                                <td class="content_header"><?=$_LANG->get('Titel')?>: </td>
                                <td class="content_row_clear">
                                    <input type="text" id="title" name="title" style="width:300px" value="<?=$suporder->getTitle()?>"
                                           onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                                </td>
                            </tr>
                            <?php if ($suporder->getId()>0){?>
                            <tr>
                                <td class="content_header"><?=$_LANG->get('Nummer')?>:</td>
                                <td class="content_row_clear">
                                    <?=$suporder->getNumber()?>
                                </td>
                            </tr>
                            <?php } ?>
                            <tr>
                                <td class="content_header"><?=$_LANG->get('Zahlungsbedingung')?>: </td>
                                <td class="content_row_clear">
                                    <select id="paymentterm" name="paymentterm" style="width:300px">
                                        <?php
                                        foreach ($payterms as $payterm) {
                                            if ($payterm->getId() == $suporder->getPaymentterm()->getId())
                                                echo '<option selected value="'.$payterm->getId().'">'.$payterm->getName().'</option>';
                                            else
                                                echo '<option value="'.$payterm->getId().'">'.$payterm->getName().'</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="content_header"><?=$_LANG->get('Lieferant')?>: </td>
                                <td class="content_row_clear">
                                    <input type="text" id="supplier_name" name="supplier_name" value="<?php if ($suporder->getSupplier()->getId()>0) { echo $suporder->getSupplier()->getNameAsLine()." - ".$suporder->getCpexternal()->getNameAsLine2(); } ?>" style="width:300px" required/>
                                    <input type="hidden" id="supplier" name="supplier" value="<?php echo $suporder->getSupplier()->getId();?>" required/>
                                    <input type="hidden" id="cpexternal" name="cpexternal" value="<?php echo $suporder->getSupplier()->getId();?>" required/>
                                </td>
                            </tr>
                            <tr>
                                <td class="content_header"><?=$_LANG->get('Lieferdatum')?>: </td>
                                <td class="content_row_clear">
                                    <input type="text" id="eta" name="eta" style="width:300px" value="<?php if ($suporder->getEta()>0) echo date('d.m.y',$suporder->getEta()); else echo date('d.m.y');?>"
                                           onfocus="markfield(this,0)" onblur="markfield(this,1)" class="text">
                                </td>
                            </tr>
                            <tr>
                                <td class="content_header"><?=$_LANG->get('Verant. MA')?>: </td>
                                <td class="content_row_clear">
                                    <select name="cpinternal" id="cpinternal" style="width:300px" required>
                                        <?php
                                        $all_user = User::getAllUser(User::ORDER_NAME);
                                        foreach ($all_user as $so_user){
                                            if ($suporder->getId() == 0 && $so_user->getId() == $_USER->getId()){
                                                echo '<option value="'.$so_user->getId().'" selected>'.$so_user->getNameAsLine().'</option>';
                                            } elseif ($suporder->getCpinternal()->getId() == $so_user->getId()){
                                                echo '<option value="'.$so_user->getId().'" selected>'.$so_user->getNameAsLine().'</option>';
                                            } else {
                                                echo '<option value="'.$so_user->getId().'">'.$so_user->getNameAsLine().'</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="content_header"><?=$_LANG->get('Status')?>: </td>
                                <td class="content_row_clear">
                                    <select id="status" name="status" style="width:300px">
                                        <option value="1" <?php if ($suporder->getStatus() == 1) echo ' selected ';?>>Offen</option>
                                        <option value="2" <?php if ($suporder->getStatus() == 2) echo ' selected ';?>>Bestellt</option>
                                        <option value="3" <?php if ($suporder->getStatus() == 3) echo ' selected ';?>>Ware Eingegangen</option>
                                        <option value="4" <?php if ($suporder->getStatus() == 4) echo ' selected ';?>>Bezahlt</option>
                                        <option value="5" <?php if ($suporder->getStatus() == 5) echo ' selected ';?>>Erledigt</option>
                                    </select>
                                </td>
                            </tr>
                            <?php if ($suporder->getId()>0){ ?>
                                <tr>
                                    <td class="content_header"><?=$_LANG->get('Erstellt')?>: </td>
                                    <td class="content_row_clear">
                                        <?php echo date('d.m.Y H:i',$suporder->getCrtdate()).' von '.$suporder->getCrtuser()->getNameAsLine(); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </table>
    				</form>
    		  </div>
    	</div>
    </div>
</div>
<?php if ($suporder->getId()>0){ ?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
        	  <div class="panel-heading">
        			<h3 class="panel-title">Positionen <img src="images/icons/plus.png" title="neuer Artikel" class="pointer" onclick="callBoxFancyArtFrame('libs/modules/suporder/suporder.article.frame.php?soid=<?php echo $suporder->getId();?>&supid=<?php echo $suporder->getSupplier()->getId();?>');"/></h3>
        	  </div>
        	  <div class="panel-body" id="suporderpositionbox">
        	  </div>
        </div>
    </div>
</div>
<?php } ?>


<script>
    $(function(){
        load_content();
    });
    function unblock(){
        $('#suporderpositionbox').unblock();
    }
    function load_content(){
        $('#suporderpositionbox').block({ message: '<h3><img src="images/page/busy.gif"/> einen Augenblick...</h3>' });
        $('#suporderpositionbox').load( "libs/modules/suporder/suporder.edit.positions.php?suporder="+$('#id').val(), null, unblock );
    }
</script>

<script language="JavaScript">
    $(document).ready(function () {
        $( "#supplier_name" ).autocomplete({
            source: "libs/modules/tickets/ticket.ajax.php?ajax_action=search_customer_and_cp",
            minLength: 2,
            focus: function( event, ui ) {
                $( "#supplier_name" ).val( ui.item.label );
                return false;
            },
            select: function( event, ui ) {
                $( "#supplier_name" ).val( ui.item.label );
                $( "#supplier" ).val( ui.item.bid );
                $( "#cpexternal" ).val( ui.item.cid );
                return false;
            }
        });
        $('#suporder_form').validate({
            rules: {
                'title': {
                    required: true
                },
                'paymentterm': {
                    required: true
                },
                'supplier': {
                    required: true
                },
                'eta': {
                    required: true
                },
                'status': {
                    required: true
                },
            },
            ignore: []
        });
    });
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
<script>
    $(function () {
        $('#eta').datetimepicker({
            lang: 'de',
            i18n: {
                de: {
                    months: [
                        'Januar', 'Februar', 'März', 'April',
                        'Mai', 'Juni', 'Juli', 'August',
                        'September', 'Oktober', 'November', 'Dezember',
                    ],
                    dayOfWeek: [
                        "So.", "Mo", "Di", "Mi",
                        "Do", "Fr", "Sa.",
                    ]
                }
            },
            timepicker: false,
            format: 'd.m.Y'
        });
    });
</script>
<div id="hidden_clicker95" style="display:none"><a id="hiddenclicker_artframe" href="http://www.google.com" >Hidden Clicker</a></div>