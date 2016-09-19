<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       12.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/businesscontact/businesscontact.class.php';
$paper = new Paper($_REQUEST["id"]);

$bogen_str = "Bogen";
if ($paper->getId()>0 && $paper->getRolle()==1)
    $bogen_str = "Rolle";
    $bogen1_str = "Bogen";

$allcustomer = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, BusinessContact::FILTER_SUPP);

if(isset($_GET['returnTo']) && $_GET['cloneProductId']) {
    /*
       * ds / 08.05.2014
     * This GET param 'returnTo' is set via order.step2.php. It contains the URL for the redirection
     * if a new paper type was created completely (Paper::hasPriceBase() )
     * If this param is set, the new paper type will be assigned to a cloned version of
     * the product. (@todo: store product id in session?`)
     *
     * Keep in mind that this get param will be given only for te first call, so we have
     * to store this information in a session.
     *
     *
     * 'cloneProductId' contains the product id which has to be cloned later. We have to store this
     * id in a session, too.
     *
     */
    $_SESSION['_alternativePaperMode'] = array(
        'alternativePaperModeReturnUrl' => $_GET['returnTo'],
        'alternativePaperModeProductId' => $_GET['cloneProductId'],
    );
}
$alternativePaperMode = (isset($_SESSION['_alternativePaperMode']) && is_array($_SESSION['_alternativePaperMode']));


if($_REQUEST["exec"] == "copy")
    $paper->clearId();

if($_REQUEST["subexec"] == "save")
{
    echo "Papierpreis in €: " . $_REQUEST['paper_100kg'] . "</br>";
    $sizes = Array();
    $weights = Array();
    $prices = Array();
    $supplier = Array();
    foreach(array_keys($_REQUEST) as $key)
    {
        if(preg_match("/paper_size_width_(?P<id>\d+)/", $key, $match))
        {
            if(trim($_REQUEST["paper_size_width_{$match["id"]}"]) != "" && trim($_REQUEST["paper_size_height_{$match["id"]}"]) != "")
            {
                $t["width"] = trim($_REQUEST["paper_size_width_{$match["id"]}"]);
                $t["height"] = trim($_REQUEST["paper_size_height_{$match["id"]}"]);
                $sizes[] = $t;
            }
        }

        if(preg_match("/paper_weight_(?P<id>\d+)/", $key, $match))
        {
            if(trim($_REQUEST["paper_weight_{$match["id"]}"]) != "")
                $weights[] = trim($_REQUEST["paper_weight_{$match["id"]}"]);
        }

        if(preg_match("/price_quantity_from_(?P<id>\d+)/", $key, $match))
        {
            $t["size_width"] = (int)$_REQUEST["price_size_width_{$match["id"]}"];
            $t["size_height"] = (int)$_REQUEST["price_size_height_{$match["id"]}"];
            $t["weight_from"] = (int)$_REQUEST["price_weight_from_{$match["id"]}"];
            $t["weight_to"] = (int)$_REQUEST["price_weight_to_{$match["id"]}"];
            $t["quantity_from"] = (int)$_REQUEST["price_quantity_from_{$match["id"]}"];
            $t["price"] = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["price_{$match["id"]}"])));
            if ($t["weight_from"] && $t["weight_to"] && $t["price"])
                $prices[] = $t;
        }

        if(preg_match("/priceperthousand_(?P<width>\d+)x(?P<height>\d+)_(?P<weight>\d+)_(?P<id>\d+)/", $key, $match))
        {
            $t["size_width"] = (int)$match["width"];
            $t["size_height"] = (int)$match["height"];
            $t["weight_from"] = (int)$match["weight"];
            $t["weight_to"] = (int)$match["weight"];
            $t["quantity_from"] = (int)$_REQUEST["quantity_{$t["size_width"]}x{$t["size_height"]}_{$t["weight_from"]}_{$match["id"]}"];
            $t["weight"] = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["kgperthousand_{$t["size_width"]}x{$t["size_height"]}_{$t["weight_from"]}_{$match["id"]}"])));
            $t["price"] = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST[$key])));
            if($_REQUEST['paper_100kg']){
                $t["price"] = ((($t["size_width"] * $t["size_height"] / 1000) * ($match["weight"]))*(($_REQUEST['paper_100kg'] / 100) * ($t["quantity_from"])) / 1000);
            }
            if ($_REQUEST['paper_1qm'])
            {
                $t["price"] = ((($t["size_width"] * $t["size_height"] / 1000) * ($match["weight"]))*(($_REQUEST['paper_1qm'] / 100) * ($t["quantity_from"])) / 1000);
            }
            //             var_dump($t);
            if ($t["weight_from"] && $t["weight_to"] && $t["price"])
                $prices[] = $t;
        }
    }

    for ($i = 0; $i < $_REQUEST["supplier_counter"]; $i++) {
        if ($_REQUEST["supplier_".$i] != "") {
            if (count($supplier) > 0 && !in_array($_REQUEST["supplier_".$i],$supplier)) {
                $supplier[] = $_REQUEST["supplier_".$i];
            }
            else if (count($supplier) == 0) {
                $supplier[] = $_REQUEST["supplier_".$i];
            }
        }
    }
    $paper->setPrices($prices);
    $paper->setSupplier($supplier);

    if($paper->getPriceBase() != (int)$_REQUEST["paper_pricebase"])
        $paper->setPrices(Array());

    $paper->setName(trim(addslashes($_REQUEST["paper_name"])));
    $paper->setComment(trim(addslashes($_REQUEST["paper_comment"])));
    $paper->setPriceBase((int)$_REQUEST["paper_pricebase"]);
    $paper->setDilivermat(trim(addslashes($_REQUEST["paper_dilivermat"])));
    $paper->setGlue(trim(addslashes($_REQUEST["paper_glue"])));
    $paper->setThickness(trim(addslashes($_REQUEST["paper_thickness"])));
    $paper->setTotalweight(trim(addslashes($_REQUEST["paper_totalweight"])));
    $paper->setPrice_100kg(trim(addslashes($_REQUEST["paper_100kg"])));
    $paper->setPrice_1qm(trim(addslashes($_REQUEST["paper_1qm"])));
    $paper->setVolume(trim(addslashes($_REQUEST["paper_volume"])));
    $paper->setRolle(trim(addslashes($_REQUEST["paper_rolle"])));

    $paper->setWeights($weights);
    $paper->setSizes($sizes);
    $savemsg = getSaveMessage($paper->save());
}
?>
<script language="javascript">
    message = '<?=$_LANG->get('Achtung \n\nDurch Aendern der Preisbasis werden alle Preise geloescht!')?>';
    function warnPBChange(val)
    {
        alert('message');
    }

    function removeOption(what, id)
    {
        if(what == 'size')
        {
            document.getElementById('paper_size_width_'+id).disabled = true;
            document.getElementById('paper_size_height_'+id).disabled = true;
            document.getElementById('paper_size_'+id).style.display = 'none';
        } else if(what == 'weight')
        {
            document.getElementById('paper_weight_'+id).disabled = true;
            document.getElementById('sp_paper_weight_'+id).style.display = 'none';
        } else if(what == 'supplier')
        {
            var element = document.getElementById('supplier_tr_'+id);
            element.parentNode.removeChild(element);
            var count = parseInt(document.getElementById('supplier_counter').value);
            var count = count -1;
            document.getElementById('supplier_counter').value = count;
        }
    }

    function addSizeField()
    {
        obj = document.getElementById('span-size');
        var count = parseInt(document.getElementById('count_size').value);

        var insert = '<div id="paper_size_'+count+'">';
        insert +='<tr>';
        insert +='<td width="25%">';
        insert +='<div class="form-group"><div class="col-sm-12"><input name="paper_size_width_'+count+'" id="paper_size_width_'+count+'" class="form-control"></div></div>';
        insert +='</td>';
        insert +='<td width="25%">';
        insert +='<div class="form-group"><div class="col-sm-12"><input name="paper_size_height_'+count+'" id="paper_size_height_'+count+'" class="form-control">';
        insert +='</td>';
        insert +='<td width="10%">';
        insert +='<span class="glyphicons glyphicons-minus pointer" onclick="removeOption(\'size\','+count+')"></span>';
        insert +='</td></tr></div>';
        obj.insertAdjacentHTML("BeforeEnd", insert);
        document.getElementById('count_size').value = count + 1;
    }

    function addWeightField()
    {
        obj = document.getElementById('span-weight');
        var count = parseInt(document.getElementById('count_weight').value);

        var insert = '<span id="sp_paper_weight_'+count+'"><input name="paper_weight_'+count+'" id="paper_weight_'+count+'" class="form-control">';
        insert += '<span class="glyphicons glyphicons-remove pointer" onclick="removeOption(\'weight\', '+count+')"></span>&nbsp;&nbsp;&nbsp;</span>';
        obj.insertAdjacentHTML("BeforeEnd", insert);
        document.getElementById('count_weight').value = count + 1;
    }

    <? if($paper->getPriceBase() == Paper::PRICE_PER_100KG) { ?>
    function addPriceRow()
    {
        var obj = document.getElementById('table-prices');
        var count = parseInt(document.getElementById('price_counter').value);
        var lastcount = count - 1;

        var weight_from = parseInt(document.getElementById('price_weight_from_'+lastcount).value);
        var weight_to = parseInt(document.getElementById('price_weight_to_'+lastcount).value);

        var insert = '<tr><td>';
        insert += 'Von <input name="price_weight_from_'+count+'" id="price_weight_from_'+count+'" class="form-control" value="'+weight_from+'"> g';
        insert += ' bis <input name="price_weight_to_'+count+'" id="price_weight_to_'+count+'" class="form-control" value="'+weight_to+'"> g';
        insert += '</td><td>';
        insert += 'ab <input name="price_quantity_from_'+count+'" id="price_quantity_from_'+count+'" class="form-control" value=""> kg';
        insert += ' <input name="price_'+count+'" id="price_'+count+'" class="form-control" value="">';
        insert += ' <?=$_USER->getClient()->getCurrency()?></td></tr>';
        obj.insertAdjacentHTML("BeforeEnd", insert);
        document.getElementById('price_counter').value = count + 1;
        document.getElementById('price_quantity_from_'+count).focus();
    }
    <?  } else { ?>
    function addPriceRow(config, qty)
    {


        var obj = document.getElementById('price_'+config+'_'+qty);
        var count = parseInt(document.getElementById('count_quantity_'+config).value);
        var insert = '<tr><td>&nbsp;</td><td>&nbsp;</td>';
        insert += '<td><div class="form-group"><div class="col-sm-12"><div class="input-group"><input name="quantity_'+config+'_'+count+'" ';
        insert += 'class="form-control" value="1"><span class="input-group-addon"> <?if ($paper->getRolle() == 0){echo $_LANG->get('Bogen')." ";} else {echo $_LANG->get('Rolle')." ";}?></span></div></div></td>';
        insert += '<td><div class="form-group"><div class="col-sm-12"><div class="input-group">';
        insert += '<input name="priceperthousand_'+config+'_'+count+'" class="form-control"';
        insert += ' value="1"><span class="input-group-addon"><?=$_USER->getClient()->getCurrency()?></span></div></div></div>';
        insert += '</td>';
        // insert += '<td class="content_row_clear"><?=$_LANG->get('KG pro 1000 Bogen')?> ';
        // insert += ' &nbsp;&nbsp;<input name="kgperthousand_'+config+'_'+count+'" class="text"';
        // insert += 'style="width:60px;text-align:right;" value="">';
        // insert += '</td>';
        insert += '</tr>';
        obj.insertAdjacentHTML("AfterEnd", insert);
    }
    <?  } ?>

    function prepareProductCloning() {
        var newProductName = window.prompt("Das Produkt wird für diese neue Papiersorte kopiert. Bitte geben Sie einen neuen Namen für das Produkt an.", "Neues Produkt"),
            redirectTo = "<?= $_SESSION['_alternativePaperMode']['alternativePaperModeReturnUrl'] ?>&newProductName=" + encodeURIComponent(newProductName) + "&paperId=<?= $paper->getId()?>";

        window.location.href = redirectTo;


    }

    function addSupplierRow()
    {
        var obj = document.getElementById('table-supplier');
        var count = parseInt(document.getElementById('supplier_counter').value);






        var insert = '<div id="supplier_tr_'+count+'"><div class="form-group"><div class="col-sm-4">';
        insert += '<select id="supplier_'+count+'" name="supplier_'+count+'" onfocus="markfield(this,0)" onblur="markfield(this,1)" class="form-control">';
        insert += '<option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>';
        insert += '<? 	foreach ($allcustomer as $cust){?>';
        insert += '<option value="<?=$cust->getId()?>"><?=str_replace("'", "\'", $cust->getNameAsLine())?></option>';
        insert += '<?	} //Ende ?>';
        insert += '</select></div><label for="" class="col-sm-3 control-label"> Papierbez. b. Lief.:</label><div class="col-sm-3"><input name="supplier_descr_'+count+'" class="form-control" id="supplier_descr_'+count+'" value=""></div>';
        insert += '<div class="col-sm-1"> <span class="glyphicons glyphicons-remove pointer" onclick="removeOption(\'supplier\', '+count+')"></div></div></div>';
        obj.insertAdjacentHTML("BeforeEnd", insert);
        document.getElementById('supplier_counter').value = count + 1;
        document.getElementById('supplier_'+count).focus();
    }
</script>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Speichern','#',"$('#paper_form').submit();",'glyphicon-floppy-disk');
if($paper->hasPriceBase() && $alternativePaperMode) {
    $quickmove->addItem('Zurück zur Auftragsbearbeitung','#',"prepareProductCloning()",'glyphicon-step-backward');
}
else{
    $quickmove->addItem('Zurück','index.php?page='.$_REQUEST['page'],null,'glyphicon-step-backward');
}
if ($paper->getId()>0){
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/paper/paper.php&exec=delete&id=".$aRow[ $aColumns[0] ]."');", 'glyphicon-trash', true);
}


echo $quickmove->generate();
// end of Quickmove generation ?>
<div class="col-md-12"></div>
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" class="form-horizontal" id="paper_form" name="paper_form" onSubmit="return checkform(new Array(this.paper_name))">
    <input name="exec" value="edit" type="hidden">
    <input name="subexec" value="save" type="hidden">
    <input name="id" value="<?=$paper->getId()?>" type="hidden">
    <input name="count_weight" id="count_weight" type="hidden" value="<?=count($paper->getWeights())?>">
    <input name="count_size" id="count_size" type="hidden" value="<?=count($paper->getSizes())?>">
    <div class="panel panel-default">
    	  <div class="panel-heading">
    			<h3 class="panel-title">
                    <img src="<?=$_MENU->getIcon($_REQUEST['page'])?>">
                    <?if ($_REQUEST["exec"] == "new")  echo $_LANG->get('Papier hinzuf&uuml;gen')?>
                    <?if ($_REQUEST["exec"] == "edit")  echo $_LANG->get('Papier &auml;ndern')?>
                    <?if ($_REQUEST["exec"] == "copy")  echo $_LANG->get('Papier kopieren')?>
                    <span class="pull-right">
                        <?=$savemsg?>
                    </span>
                </h3>
    	  </div>
    	  <div class="panel-body">
              <div class="panel panel-default">
              	  <div class="panel-heading">
              			<h3 class="panel-title">
                           Kopfdaten
                        </h3>
              	  </div>
              	  <div class="panel-body">
                      <div class="row">
                          <div class="col-md-6"><!-Linke Seite->
                              <div class="form-group">
                                  <label for="" class="col-sm-3 control-label">Name</label>
                                  <div class="col-sm-9">
                                      <input name="paper_name" value="<?=$paper->getName()?>" class="form-control">
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label for="" class="col-sm-3 control-label">Preisbasis</label>
                                  <div class="col-sm-9">
                                      <? if ($paper->getRolle() == 0){ ?>
                                          <?=$_LANG->get('pro')?> 1.000 <?=$_LANG->get($bogen1_str)?>
                                      <? } else { ?>
                                          <?=$_LANG->get('pro')?> <?=$_LANG->get($bogen_str)?>
                                      <? } ?>
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label for="" class="col-sm-3 control-label">Trägermaterial</label>
                                  <div class="col-sm-9">
                                      <input name="paper_dilivermat" value="<?=$paper->getDilivermat()?>" class="form-control">
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label for="" class="col-sm-3 control-label">Dicke gesamt in µm</label>
                                  <div class="col-sm-9">
                                      <input name="paper_thickness" value="<?=$paper->getThickness()?>" class="form-control">
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label for="" class="col-sm-3 control-label">100Kg-Preis</label>
                                  <div class="col-sm-9">
                                      <input name="paper_100kg" value="<?php echo $paper->getPrice_100kg();?>" class="form-control">
                                      <?=$_LANG->get('* Überschreibt alle 1000 Bogenpreise "Ab 1 Bogen"  !!!')?>
                                  </div>
                              </div>
                              <br>
                              <div class="form-group">
                                  <label for="" class="col-sm-3 control-label">1qm-Preis</label>
                                  <div class="col-sm-9">
                                      <input name="paper_1qm" value="<?php echo $paper->getPrice_1qm();?>" class="form-control"><br>
                                      <? if ($paper->getRolle() == 0){ ?>
                                          <?=$_LANG->get('* Überschreibt alle 1000 Bogenpreise "Ab 1 Bogen"  !!!')?>
                                      <? } else { ?>
                                          <?=$_LANG->get('* Überschreibt alle Rollenpreise "Ab 1 Rolle"  !!!')?>
                                      <? } ?>
                                  </div>
                              </div>
                          </div>
                          <div class="col-md-6"> <!-Rechte Seite->
                              <div class="form-group">
                                  <label for="" class="col-sm-3 control-label">Kleber</label>
                                  <div class="col-sm-9">
                                      <input name="paper_glue" value="<?=$paper->getGlue()?>" class="form-control">
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label for="" class="col-sm-3 control-label">Gesamtgewicht in g/m2</label>
                                  <div class="col-sm-9">
                                      <input name="paper_totalweight" value="<?=$paper->getTotalweight()?>" class="form-control">
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label for="" class="col-sm-3 control-label">Volumen</label>
                                  <div class="col-sm-9">
                                      <input name="paper_volume" value="<?php echo $paper->getVolume();?>" class="form-control">
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label for="" class="col-sm-3 control-label">Rolle</label>
                                  <div class="col-sm-2">
                                      <input name="paper_rolle" value="1" class="form-control" <?php if ($paper->getRolle()) echo ' checked ';?> type="checkbox">
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
               <div class="row">
                   <div class="col-md-6">
                       <div class="panel panel-default">
                           <div class="panel-heading">
                               <h3 class="panel-title">Verf&uuml;gbare Gr&ouml;&szlig;en (BxH)</h3>
                           </div>
                           <div class="panel-body">
                               <div class="form-group">
                                   <div class="col-sm-12">
                                     <span id="span-size">
                                         <div class="table-responsive">
                                             <table class="table table-hover">
                                                 <thead>
                                                 <tr>
                                                     <th>Breite in mm </th>
                                                     <th> Höhe in mm </th>
                                                     <th></th>
                                                     <th></th>
                                                 </tr>
                                                 </thead>
                                                 <tbody>
                                                 <? $i = 0;
                                                 foreach ($paper->getSizes() as $s) { ?>
                                                 <div id="paper_size_<?= $i ?>">
                                                     <tr>
                                                         <td width="25%">
                                                             <input name="paper_size_width_<?= $i ?>" id="paper_size_width_<?= $i ?>" class="form-control" value="<?= $s["width"] ?>">
                                                         </td>
                                                         <td  width="25%">
                                                             <input name="paper_size_height_<?= $i ?>" id="paper_size_height_<?= $i ?>" class="form-control" value="<?= $s["height"] ?>">
                                                         </td>
                                                         <td  width="10%">
                                                             <span class="glyphicons glyphicons-minus pointer" onclick="removeOption('size', <?= $i ?>)"></span>
                                                         </td>
                                                         <? $i++;
                                                         } ?>
                                                         <td  width="25%">
                                                             <span class="glyphicons glyphicons-plus pointer" onclick="addSizeField()"></span>
                                                         </td>
                                                     </tr>
                                                 </div>
                                                 </tbody>
                                             </table>
                                         </div>
                                     </span>

                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
                   <div class="col-md-6">
                       <div class="panel panel-default">
                           <div class="panel-heading">
                               <h3 class="panel-title">
                                   Verf&uuml;gbare Grammaturen
                               </h3>
                           </div>
                           <div class="panel-body">
                               <div class="form-group">
                                   <div class="col-sm-12">
                                       <div class="table-responsive">
                                           <table class="table table-hover">
                                               <thead>
                                               <tr>
                                                   <th></th>
                                                   <th></th>
                                                   <th></th>
                                               </tr>
                                               </thead>
                                               <tbody>
                                               <div id="span-weight">
                                                   <? $i = 0;
                                                   foreach ($paper->getWeights() as $w) { ?>
                                                   <div id="sp_paper_weight_<?= $i ?>">
                                                       <tr>
                                                           <td  width="15%">
                                                               <input name="paper_weight_<?= $i ?>" id="paper_weight_<?= $i ?>" class="form-control" value="<?= $w ?>">
                                                           </td>
                                                           <td  width="10%">
                                                               <span class="glyphicons glyphicons-minus pointer" onclick="removeOption('weight', <?= $i ?>)"></span>
                                                           </td>
                                                           <? $i++;
                                                           } ?>
                                                           <td  width="33%">
                                                               <span class="glyphicons glyphicons-plus pointer" onclick="addWeightField()"></span>
                                                           </td>
                                                       </tr>
                                                   </div>
                                               </div>
                                               </tbody>
                                           </table>
                                       </div>
                                   </div>
                               </div>
                           </div>
                       </div>
                   </div>
               </div>

              <div class="panel panel-default">
                  <div class="panel-heading">
                      <h3 class="panel-title">Lieferanten</h3>
                  </div>
                  <div class="panel-body">
                      <input type="hidden" name="supplier_counter" id="supplier_counter" value="<? if(count($paper->getSupplier()) > 0) echo count($paper->getSupplier()); else echo "0";?>">
                      <div id="table-supplier" >
                          <? $i = 0; foreach($paper->getSupplier() as $s) {
                              $tmp_supplier = new BusinessContact($s);
                              ?>
                              <div id="supplier_tr_<?=$i?>">
                                  <div class="form-group">
                                      <div class="col-sm-4">
                                          <select id="supplier_<?=$i?>" name="supplier_<?=$i?>" onfocus="markfield(this,0)" onblur="markfield(this,1)" class="form-control">
                                              <option value="">&lt; <?=$_LANG->get('Bitte w&auml;hlen')?> &gt;</option>
                                              <? 	foreach ($allcustomer as $cust){?>
                                                  <option value="<?=$cust->getId()?>"
                                                      <?if ($tmp_supplier->getId() == $cust->getId()) echo "selected" ?>><?= $cust->getNameAsLine()?></option>
                                              <?	} //Ende ?>
                                          </select>
                                      </div>
                                      <label for="" class="col-sm-3 control-label"> Papierbez. b. Lief.:</label>
                                      <div class="col-sm-2">
                                          <input name="supplier_descr_<?=$i?>" id="supplier_descr_<?=$i?>" value="<?php echo $s['descr'];?>" class="form-control">
                                      </div>
                                      <div class="col-sm-1">
                                          <span class="glyphicons glyphicons-remove pointer" onclick="removeOption('supplier', <?=$i?>)"></span>
                                      </div>
                                      <div class="col-sm-1">
                                          <span class="glyphicons glyphicons-plus pointer" onclick="addSupplierRow()"></span>
                                      </div>
                                  </div>
                              </div>
                              <?  $i++; } ?>
                      </div>
                  </div>
              </div>
              <br>
              <div class="panel panel-default">
                  <div class="panel-heading">
                      <h3 class="panel-title">
                          Preise
                      </h3>
                  </div>
                  <? if($paper->getPriceBase() == Paper::PRICE_PER_100KG) { ?>
                  <input type="hidden" name="price_counter" id="price_counter" value="<? if(count($paper->getPrices()) > 0) echo count($paper->getPrices()); else echo "1";?>">
                  <div class="table-responsive">
                      <table class="table table-hover" id="table-prices">
                          <? $i = 0; foreach($paper->getPrices() as $p) { ?>
                              <tr>
                                  <td>
                                      <?=$_LANG->get('Von')?> <input name="price_weight_from_<?=$i?>" id="price_weight_from_<?=$i?>" class="form-control" value="<?=$p["weight_from"]?>"> g
                                      <?=$_LANG->get('bis')?> <input name="price_weight_to_<?=$i?>" id="price_weight_to_<?=$i?>" class="form-control" value="<?=$p["weight_to"]?>"> g
                                  </td>
                                  <td>
                                      <?=$_LANG->get('ab')?> <input name="price_quantity_from_<?=$i?>" id="price_quantity_from_<?=$i?>" class="form-control"  value="<?=$p["quantity_from"]?>"> kg
                                      <input name="price_<?=$i?>" id="price_<?=$i?>" class="form-control" value="<?=printPrice($p["price"])?>">
                                      <?=$_USER->getClient()->getCurrency()?>
                                      <? if($i == count($paper->getPrices())-1)
                                          echo '&nbsp;&nbsp;&nbsp;<span class="glyphicons glyphicons-plus pointer" onclick="addPriceRow()"></span>'; ?>
                                  </td>
                              </tr>
                              <?  $i++; }
                          if(count($paper->getPrices())== 0) { ?>
                              <tr>
                                  <td>
                                      Von <input name="price_weight_from_0" id="price_weight_from_0" class="form-control"> g
                                      bis <input name="price_weight_to_0" id="price_weight_to_0" class="form-control"> g
                                  </td>
                                  <td>
                                      ab <input name="price_quantity_from_0" id="price_quantity_from_0" class="form-control"> kg
                                      <input name="price_0" id="price_0" class="form-control">
                                      <?=$_USER->getClient()->getCurrency()?>
                                      &nbsp;&nbsp;&nbsp;<span class="glyphicons glyphicons-plus pointer" onclick="addPriceRow()"></span>
                                  </td>
                              </tr>

                          <? } ?>
                      </table>
                      <?  } else { ?>
                      <div class="table-responsive">
                          <table width="1200px" class="table table-hover">
                              <thead>
                                  <tr>
                                      <th width="10%">Format</th>
                                      <th width="10%">Gewicht</th>
                                      <th width="20%">Ab</th>
                                      <th width="20%"> <?if ($paper->getRolle() == 0){
                                          echo $_LANG->get('Preis pro 1000 Bogen')." ";
                                          } else {
                                          echo $_LANG->get('Preis pro Rolle')." ";
                                          } ?>
                                      </th>
                                      <th width="10%" ></th>
                                  </tr>
                              </thead>
                              <? foreach ($paper->getSizes() as $s) {
                                  $firstSize = true;
                                  foreach ($paper->getWeights() as $w)
                                  {
                                      $firstWeight = true;
                                      $paper_prices = $paper->getPrices();
                                      $i = 0;
                                      $prices = Array();
                                      while($i < count($paper_prices))
                                      {
                                          if($paper_prices[$i]["size_width"] == $s["width"] &&
                                              $paper_prices[$i]["size_height"] == $s["height"] &&
                                              $paper_prices[$i]["weight_from"] == $w)
                                          {
                                              $prices[] = $paper_prices[$i];
                                          }
                                          $i++;
                                      }

                                      // Falls noch keine Preise existieren, Preise vorgaukeln
                                      if(count($prices) == 0)
                                          $prices[] = Array("size_width" => $s["width"], "size_height" => $s["height"]);

                                      $x = 0;
                                      foreach($prices as $price)
                                      {

                                          echo '<tr id="price_'.$s["width"].'x'.$s["height"].'_'.$w.'_'.$price["quantity_from"].'">
                    <td>';

                                          if ($firstSize)
                                              echo $s["width"]." x ".$s["height"];
                                          else
                                              echo "&nbsp;";
                                          $firstSize = false;
                                          echo '<td>';

                                          if ($firstWeight)
                                              echo $w.' g';
                                          else
                                              echo '&nbsp;';

                                          if ($price["quantity_from"]<=0)
                                              $price["quantity_from"] = 1;
                                          if ($price["price"]<=0)
                                              $price["price"] = 1;








                                          echo '<td>

                                                <input name="count_quantity_'.$s["width"].'x'.$s["height"].'_'.$w.'" value="'.count($prices).'" type="hidden" id="count_quantity_'.$s["width"].'x'.$s["height"].'_'.$w.'">
                                            <div class="form-group">
                                              <div class="col-sm-12">
                                                   <div class="input-group">
                                                      <input name="quantity_'.$s["width"].'x'.$s["height"].'_'.$w.'_'.$x.'" class="form-control" value="'.$price["quantity_from"].'">
                                                       <span class="input-group-addon">';  if ($paper->getRolle() == 0){
                                                                                              echo $_LANG->get('Bogen')." ";
                                                                                          } else {
                                                                                              echo $_LANG->get('Rolle')." ";
                                      }'</span>
                                                  </div>
                                            </div>

                      </td>';



                                          echo '</td>
                      <td>';


                                          echo '
                                          <div class="form-group">
                                              <div class="col-sm-12">
                                                  <div class="input-group">
                                                      <input name="priceperthousand_'.$s["width"].'x'.$s["height"].'_'.$w.'_'.$x.'" class="form-control" value="'.printPrice($price["price"]).'">
                                                       <span class="input-group-addon">'.$_USER->getClient()->getCurrency().'</span>
                                                  </div>
                                              </div>
                                          </div>

                                           </td>';

                                          echo '</td>
                    <td>';


                                          echo '<span class="glyphicons glyphicons-plus pointer" onclick="addPriceRow(\''.$s["width"].'x'.$s["height"].'_'.$w.'\', \''.$price["quantity_from"].'\')"></span>';
                                          $firstWeight = false;
                                          $x++;
                                      }
                                      echo '</tr>';

                                  }
                              } ?>
                          </table>
                          <?  } ?>
                      </div>
                  </div>
              </div>


              <?php if($paper->hasPriceBase() && $alternativePaperMode) : ?>
                  <td>
                      <button type="button" class="btn btn-origin btn-success" onclick="prepareProductCloning()">
                          <?= $_LANG->get('&laquo; Zur&uuml;ck zur Auftragsbearbeitung') ?>
                      </button>
                  </td>
              <?php else : ?>
                  <td>

                  </td>
              <?php endif; ?>
    	  </div>
    </div>
</form>