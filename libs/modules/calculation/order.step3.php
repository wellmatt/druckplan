<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       22.01.2014
// Copyright:     2012-14 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$filter = BusinessContact::FILTER_SUPP;
$all_supplier = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, $filter);
$formats = Paper::getAllPapers();
$paper_sizes_unique = Paper::getAllUniquePaperSizes();
$perf = new Perferences();
$format_sizes_unique = $perf->getFormats_raw();

$machines = $order->getProduct()->getMachines();
$groups = MachineGroup::getAllMachineGroups(MachineGroup::ORDER_POSITION);
$finishings = Finishing::getAllFinishings();
$schemes = $calc->getAvailableFoldschemes();
$foldtypes = Foldtype::getAllFoldTypes(Foldtype::ORDER_NAME);
?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$tmp_all_calcs = Calculation::getAllCalculations($order,Calculation::ORDER_AMOUNT);
foreach ($tmp_all_calcs as $tmp_calc){
    $quickmove->addItem('Auflage '.$tmp_calc->getAmount(),'index.php?page=libs/modules/calculation/order.php&id='.$order->getId().'&calc_id='.$tmp_calc->getId().'&exec=edit&step=3',null,'glyphicon glyphicon-pencil');
}
$quickmove->addItem('Druckbogenvorsch.','#',"window.open('index.php?page=".$_REQUEST['page']."&id=".$_REQUEST['id']."&exec=edit&step=5');",'glyphicons-note-empty');
$quickmove->addItem('Weiter','#',"document.getElementsByName('nextstep')[0].value='4';document.step3_form.submit();",'glyphicon-chevron-right');
$quickmove->addItem('Speichern','#',"document.step3_form.submit();",'glyphicon-floppy-disk');
echo $quickmove->generate();
// end of Quickmove generation ?>


<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Auftragsdaten: <b><?= $order->getNumber() ?></b></h3>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-1">
                <b><?= $_LANG->get('Produkt') ?>:</b>
            </div>
            <div class="col-md-2">
                <?= $order->getProduct()->getName() ?>
            </div>
            <div class="col-md-2">
                <b><?= $_LANG->get('Beschreibung') ?>:</b>
            </div>
            <div class="col-md-2">
                <?= $order->getProduct()->getDescription() ?>
            </div>
            <div class="col-md-2">
                <b><?= $_LANG->get('Auflage') ?>:</b>
            </div>
            <div class="col-md-2">
                <?= printBigInt($calc->getAmount()) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-1">
                <b><?= $_LANG->get('Endformat') ?>:</b>
            </div>
            <div class="col-md-2">
                <?= $calc->getProductFormat()->getName() ?>
            </div>
            <div class="col-md-2">
                <b><?= $_LANG->get('Format offen') ?>:</b>
            </div>
            <div class="col-md-2">
                <?= $calc->getProductFormatWidthOpen() ?> x <?= $calc->getProductFormatHeightOpen() ?> mm
            </div>
            <div class="col-md-2">
                <b><?= $_LANG->get('Format geschlossen') ?>:</b>
            </div>
            <div class="col-md-2">
                <?= $calc->getProductFormatWidth() ?> x <?= $calc->getProductFormatHeight() ?> mm
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Teil</th>
                    <th>Material</th>
                    <th>Gewicht</th>
                    <th>Umfang</th>
                    <th>Farbigkeit</th>
                    <th>Format</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $contents = $calc->getDetails();
                foreach ($contents as $content) { ?>
                    <tr>
                        <td><?php echo $content['name']; ?></td>
                        <td><?php echo $content['material']; ?></td>
                        <td><?php echo $content['gewicht']; ?> g / qm</td>
                        <td><?php echo $content['umfang']; ?> <?= $_LANG->get('Seiten') ?></td>
                        <td><?php echo $content['farbigkeit']; ?></td>
                        <td><?php echo $content['offen']; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" name="step3_form">

    <div class="row">
        <div class="col-md-12">
            <span class="pull-right">
                <input type="checkbox" name="auto_calc_values" value="1" <?if($calc->getCalcAutoValues()) echo "checked";?>>
                * <?=$_LANG->get('Werte automatisch kalkulieren')?> &nbsp;&nbsp;&nbsp;
                    <input type="checkbox" name="debug_calc" value="1" <?if($calc->getCalcDebug()) echo "checked";?>>
                * <?=$_LANG->get('Rechnungen ausgeben')?>
            </span>
        </div>
    </div>

    <input name="step" value="3" type="hidden">
    <input name="exec" value="edit" type="hidden">
    <input name="subexec" value="save" type="hidden">
    <input name="id" value="<?=$order->getId()?>" type="hidden">
    <input name="calc_id" value="<?=$calc->getId()?>" type="hidden">
    <input name="nextstep" value="" type="hidden">
    <? if(isset($_REQUEST["addorder_amount"])){
        foreach ($_REQUEST["addorder_amount"] as $amount){
            echo '<input name ="addorder_amount[]" value="'.$amount.'" type="hidden">';
            echo '<input name="origcalc" value="'.$calc->getId().'" type="hidden">';
        }
        foreach ($_REQUEST["addorder_sorts"] as $sorts){
            echo '<input name ="addorder_sorts[]" value="'.$sorts.'" type="hidden">';
        }
    }?>

    <?php
    $x = 0;
    foreach ($groups as $group) {
        // Alle Maschinen der Gruppe ermitteln?
        $groupmachs = Array();
        foreach ($machines as $m) {
            if ($m->getGroup()->getId() == $group->getId()) {
                $groupmachs[] = $m;
            }
        }
        if (count($groupmachs)) {
            ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><?php echo $group->getName(); ?></h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table id="table_group_<?php echo $group->getId();?>" class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="15%">Maschine</th>
                                    <th width="5%">Zeit (min)</th>
                                    <th width="10%">Inhalt</th>
                                    <th width="40%">Einstellungen</th>
                                    <th width="10%">Bogengröße</th>
                                    <th width="5%">Kosten</th>
                                    <th width="5%">Optionen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $groupHasDefaultMachs = false;
                                //getAllMachineentriesByColor
                                foreach (Machineentry::getAllMachineentries($calc->getId(), Machineentry::ORDER_ID, $group->getId()) as $mach) {
                                    $groupHasDefaultMachs = true;?>
                                    <tr id="tr_mach_<? echo $x;?>">
                                        <td>
                                            <input type="hidden" name="mach_group_<? echo $x;?>" value="<? echo $group->getId();?>">
                                            <div class="form-group">
                                                <select class="form-control" name="mach_id_<? echo $x;?>" id="mach_id_<? echo $x;?>" onchange="updateMachineProps(<? echo $x;?>, this.value);">
                                                    <?php
                                                    foreach ($groupmachs as $gm) {
                                                        echo '<option value="'.$gm->getId().'" ';
                                                        if($mach->getMachine()->getId() == $gm->getId())
                                                            echo "selected";
                                                        echo '>'.$gm->getName().'</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input type="text" class="form-control" name="mach_time_<? echo $x;?>" value="<?php echo $mach->getTime();?>" placeholder="Min.">
                                            </div>
                                        </td>
                                        <?php
                                        switch ($mach->getMachine()->getType()){
                                            case Machine::TYPE_CTP:
                                                ?>
                                                <td id="td-machparts-<? echo $x;?>"></td>
                                                <td class="row" id="td-machopts-<?php echo $x;?>">
                                                    <?php
                                                    // Falls Maschine manuell berechnet wird, Feld anzeigen
                                                    if($mach->getMachine()->getPriceBase() == Machine::PRICE_VARIABEL)
                                                    {
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Preis</label><div class="input-group">';
                                                        echo '<input name="mach_manprice_'.$x.'" value="'.printPrice($mach->getPrice()).'" class="form-control">';
                                                        echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        if($mach->getMachineGroupObject()->getType() == MachineGroup::TYPE_EXTERN){
                                                            echo '<div class="col-md-4"><div class="form-group">';
                                                            echo '<label class="control-label">EK</label><div class="input-group">';
                                                            echo '<input name="mach_supplierprice_'.$x.'" class="form-control" title="'.$_LANG->get('Einkaufspreis').'"';
                                                            echo 'value="'.printPrice($mach->getSupplierPrice()).'" type="text" >';
                                                            echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td></td>
                                                <?php
                                                break;
                                            case Machine::TYPE_CUTTER:
                                                ?>
                                                <td id="td-machparts-<? echo $x;?>">
                                                    <div class="form-group">
                                                        <select name="mach_part_<? echo $x;?>" id="mach_part_<? echo $x;?>" onchange="updateAvailPapers(<? echo $x;?>)" class="form-control">
                                                            <?php
                                                            foreach ($contents as $item) {
                                                                echo '<option value="'.$item['paper'].'" ';
                                                                if($mach->getPart() == $item['paper']) echo 'selected';
                                                                echo '>'.$item['name'].'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="row" id="td-machopts-<?php echo $x;?>">
                                                    <?php
                                                    // Falls Maschine manuell berechnet wird, Feld anzeigen
                                                    if($mach->getMachine()->getPriceBase() == Machine::PRICE_VARIABEL)
                                                    {
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Preis</label><div class="input-group">';
                                                        echo '<input name="mach_manprice_'.$x.'" value="'.printPrice($mach->getPrice()).'" class="form-control">';
                                                        echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        if($mach->getMachineGroupObject()->getType() == MachineGroup::TYPE_EXTERN){
                                                            echo '<div class="col-md-4"><div class="form-group">';
                                                            echo '<label class="control-label">EK</label><div class="input-group">';
                                                            echo '<input name="mach_supplierprice_'.$x.'" class="form-control" title="'.$_LANG->get('Einkaufspreis').'"';
                                                            echo 'value="'.printPrice($mach->getSupplierPrice()).'" type="text" >';
                                                            echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        }
                                                    }

                                                    // Schnitte
                                                    echo '<div class="col-md-4"><div class="form-group">';
                                                    echo '<label class="control-label">Schnitte</label><div class="input-group">';
                                                    echo '<input type="text" name="mach_cutter_cuts_'.$x.'" id="mach_cutter_cuts_'.$x.'" value="'.$mach->getCutter_cuts().'" class="form-control">';
                                                    echo '</div></div></div>';

                                                    // Stapel
                                                    echo '<div class="col-md-4"><div class="form-group">';
                                                    echo '<label class="control-label">Stapel</label><div class="input-group">';
                                                    echo '<input type="text" disabled value="'.$mach->calcStacks().'" class="form-control" style="cursor: auto;">';
                                                    echo '</div></div></div>';

                                                    // Manueller Aufschlag
                                                    echo '<div class="col-md-4"><div class="form-group">';
                                                    echo '<label class="control-label">Man. Aufschlag</label><div class="input-group">';
                                                    echo '<input type="text" name="mach_special_margin_'.$x.'" class="form-control" value="'.str_replace(".", ",", $mach->getSpecial_margin()).'">';
                                                    echo '<span class="input-group-addon">%</span></div></div></div>';

                                                    echo '<div class="col-md-4"><div class="form-group">';
                                                    echo '<label class="control-label">Aufs. Text</label><div class="input-group">';
                                                    echo '<input type="text" name="mach_special_margin_text_'.$x.'" class="form-control" value="'.$mach->getSpecial_margin_text().'">';
                                                    echo '</div></div></div>';

                                                    // Hinweise
                                                    echo '<div class="col-md-12"><div class="form-group">';
                                                    echo '<label class="control-label">Hinweise</label><div class="input-group col-md-12">';
                                                    echo '<input name="mach_info_'.$x.'" id="mach_info_'.$x.'" class="form-control" value="'.$mach->getInfo().'">';
                                                    echo '</div></div></div>';


                                                    ?>
                                                </td>
                                                <td></td>
                                                <?php
                                                break;
                                            case Machine::TYPE_DRUCKMASCHINE_DIGITAL:
                                                ?>
                                                <td id="td-machparts-<? echo $x;?>">
                                                    <div class="form-group">
                                                        <select name="mach_part_<? echo $x;?>" id="mach_part_<? echo $x;?>" onchange="updateAvailPapers(<? echo $x;?>)" class="form-control">
                                                            <?php
                                                            foreach ($contents as $item) {
                                                                echo '<option value="'.$item['paper'].'" ';
                                                                if($mach->getPart() == $item['paper']) echo 'selected';
                                                                echo '>'.$item['name'].'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="row" id="td-machopts-<?php echo $x;?>">
                                                    <?php
                                                    // Falls Maschine manuell berechnet wird, Feld anzeigen
                                                    if($mach->getMachine()->getPriceBase() == Machine::PRICE_VARIABEL)
                                                    {
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Preis</label><div class="input-group">';
                                                        echo '<input name="mach_manprice_'.$x.'" value="'.printPrice($mach->getPrice()).'" class="form-control">';
                                                        echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        if($mach->getMachineGroupObject()->getType() == MachineGroup::TYPE_EXTERN){
                                                            echo '<div class="col-md-4"><div class="form-group">';
                                                            echo '<label class="control-label">EK</label><div class="input-group">';
                                                            echo '<input name="mach_supplierprice_'.$x.'" class="form-control" title="'.$_LANG->get('Einkaufspreis').'"';
                                                            echo 'value="'.printPrice($mach->getSupplierPrice()).'" type="text" >';
                                                            echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        }
                                                    }

                                                    // Option Lack
                                                    if($mach->getMachine()->getFinish())
                                                    {
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Lack</label><div class="input-group">';
                                                        echo '<select name="mach_finishing_'.$x.'" id="mach_finishing_'.$x.'" class="form-control">';
                                                        echo '<option value="0">'.$_LANG->get('kein Lack').'</option>';
                                                        foreach($finishings as $f)
                                                        {
                                                            echo '<option value="'.$f->getId().'" ';
                                                            if($mach->getFinishing()->getId() == $f->getId()) echo "selected";
                                                            echo '>'.$f->getName().'</option>';
                                                        }
                                                        echo '</select>';
                                                        echo '</div></div></div>';
                                                    }

                                                    // Laufrichtung
                                                    echo '<div class="col-md-4"><div class="form-group">';
                                                    echo '<label class="control-label">Laufrichtung</label><div class="input-group">';
                                                    echo '<select name="mach_roll_dir_'.$x.'" class="form-control">';
                                                    echo '<option value="0" ';
                                                    if ($mach->getRoll_dir() == 0) echo 'selected';
                                                    echo '>';
                                                    echo 'auto</option>';
                                                    echo '<option value="1" ';
                                                    if ($mach->getRoll_dir() == 1) echo 'selected';
                                                    echo '>';
                                                    echo 'breite Bahn</option>';
                                                    echo '<option value="2" ';
                                                    if ($mach->getRoll_dir() == 2) echo 'selected';
                                                    echo '>';
                                                    echo 'schmale Bahn</option>';
                                                    echo '</select>';
                                                    echo '</div></div></div>';

                                                    // Rollendruck
                                                    if ($calc->getPaperContent()->getRolle() == 1)
                                                    {
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Etiketten pro Rolle</label><div class="input-group">';
                                                        echo '<input name="mach_labelcount_'.$x.'" class="form-control" type="text" value="'.$mach->getLabelcount().'">';
                                                        echo '</div></div></div>';

                                                        /* echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Laufmeter pro Rolle</label><div class="input-group">';
                                                        echo '<input name="mach_rollcount_'.$x.'" class="form-control" type="text" value="'.$mach->getRollcount().'">';
                                                        echo '</div></div></div>';*/

                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Anz. Rollen:&nbsp;</label>';
                                                        //  (WURZEL(4*(Amount/Anzahl Nutzen)/10^3*(Höhe des Produktes)*(Paperthickness;PapierID)/PI()+(Kerndurchmesser manuelle EIngabe)^2);ELSE 0)
                                                        // prettyPrint("Debug Anz. Rollen: {$calc->getAmount()}/{$mach->getLabelcount()}");
                                                        echo ( ($calc->getAmount()/$mach->getLabelcount()));
                                                        //prettyPrint("Debug Anz. Rollen: ({$calc->getAmount()}/{$calc->getProductsPerPaper($mach->getPart())}");
                                                        //echo ({$calc->getAmount()}/{$calc->getProductsPerPaper($mach->getPart())});
                                                        echo '</div></div>';
                                                    }




                                                    // Manueller Aufschlag
                                                    echo '<div class="col-md-4"><div class="form-group">';
                                                    echo '<label class="control-label">Manueller Aufschlag</label><div class="input-group">';
                                                    echo '<input type="text" name="mach_special_margin_'.$x.'" class="form-control" value="'.str_replace(".", ",", $mach->getSpecial_margin()).'">';
                                                    echo '<span class="input-group-addon">%</span></div></div></div>';

                                                    echo '<div class="col-md-4"><div class="form-group">';
                                                    echo '<label class="control-label"><br />Aufsschlag Text</label><div class="input-group">';
                                                    echo '<input type="text" name="mach_special_margin_text_'.$x.'" class="form-control" value="'.$mach->getSpecial_margin_text().'">';
                                                    echo '</div></div></div>';

                                                    // Inline Heften
                                                    if ($mach->getMachine()->getInlineheften()){
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label"><br/>Inline Heften</label><div class="input-group">';
                                                        echo '<div class="checkbox"><label>';
                                                        if ($mach->getInlineheften())
                                                            echo '<input type="checkbox" name="mach_inlineheften_'.$x.'" checked id="mach_inlineheften_'.$x.'" value="1">';
                                                        else
                                                            echo '<input type="checkbox" name="mach_inlineheften_'.$x.'" id="mach_inlineheften_'.$x.'" value="1">';
                                                        echo '</label></div>';
                                                        echo '</div></div></div>';
                                                    }

                                                    // Zuschuss
                                                    echo '<div class="col-md-4"><div class="form-group">';
                                                    echo '<label class="control-label"><br/>Zuschuss Bogen</label><div class="input-group">';
                                                    echo '<input name="mach_digigrant_'.$x.'" id="mach_digigrant_'.$x.'" class="form-control" value="'.printPrice($mach->getDigigrant(),2).'">';
                                                    echo '</div></div></div>';

                                                    // Farbton
                                                    echo '<div class="col-md-4"><div class="form-group">';
                                                    echo '<label class="control-label">Farbton <br/>Sonderfarbe / Info</label><div class="input-group">';
                                                    echo '<input name="mach_color_detail_'.$x.'" id="mach_color_detail_'.$x.'" class="form-control" value="'.$mach->getColor_detail().'">';
                                                    echo '</div></div></div>';



                                                    // Hinweise
                                                    echo '<div class="col-md-12"><div class="form-group">';
                                                    echo '<label class="control-label">Hinweise</label><div class="input-group col-md-12">';
                                                    echo '<input name="mach_info_'.$x.'" id="mach_info_'.$x.'" class="form-control" value="'.$mach->getInfo().'">';
                                                    echo '</div></div></div>';


                                                    ?>
                                                </td>
                                                <td id="td-papersize-<? echo $x;?>">
                                                    <div class="form-group">
                                                        <?php
                                                        if ($mach->getPart() == Calculation::PAPER_CONTENT)
                                                            $sizes = $calc->getPaperContent()->getAvailablePaperSizesForMachine($mach->getMachine(), $calc->getProductFormatWidthOpen(), $calc->getProductFormatHeightOpen(), $calc->getPaperContent()->getRolle(), $calc->getProductFormatHeightOpen());
                                                        else if ($mach->getPart() == Calculation::PAPER_ADDCONTENT)
                                                            $sizes = $calc->getPaperAddContent()->getAvailablePaperSizesForMachine($mach->getMachine(), $calc->getProductFormatWidthOpen(), $calc->getProductFormatHeightOpen(), $calc->getPaperAddContent()->getRolle(), $calc->getProductFormatHeightOpen());
                                                        else if ($mach->getPart() == Calculation::PAPER_ENVELOPE)
                                                            $sizes = $calc->getPaperEnvelope()->getAvailablePaperSizesForMachine($mach->getMachine(), $calc->getEnvelopeWidthOpen(), $calc->getEnvelopeHeightOpen(), $calc->getPaperEnvelope()->getRolle(), $calc->getProductFormatHeightOpen());
                                                        else if ($mach->getPart() == Calculation::PAPER_ADDCONTENT2)
                                                            $sizes = $calc->getPaperAddContent2()->getAvailablePaperSizesForMachine($mach->getMachine(), $calc->getProductFormatWidthOpen(), $calc->getProductFormatHeightOpen(), $calc->getPaperAddContent2()->getRolle(), $calc->getProductFormatHeightOpen());
                                                        else if ($mach->getPart() == Calculation::PAPER_ADDCONTENT3)
                                                            $sizes = $calc->getPaperAddContent3()->getAvailablePaperSizesForMachine($mach->getMachine(), $calc->getProductFormatWidthOpen(), $calc->getProductFormatHeightOpen(), $calc->getPaperAddContent3()->getRolle(), $calc->getProductFormatHeightOpen());

                                                        echo '<select name="mach_papersize_'.$x.'" class="form-control">';
                                                        foreach($sizes as $s)
                                                        {
                                                            echo '<option value="'.$s["width"].'x'.$s["height"].'" ';
                                                            if($mach->getPart() == Calculation::PAPER_CONTENT)
                                                                if ($s["width"].'x'.$s["height"] == $calc->getPaperContentWidth().'x'.$calc->getPaperContentHeight()) echo 'selected';
                                                            if($mach->getPart() == Calculation::PAPER_ADDCONTENT)
                                                                if ($s["width"].'x'.$s["height"] == $calc->getPaperAddContentWidth().'x'.$calc->getPaperAddContentHeight()) echo 'selected';
                                                            if($mach->getPart() == Calculation::PAPER_ENVELOPE)
                                                                if ($s["width"].'x'.$s["height"] == $calc->getPaperEnvelopeWidth().'x'.$calc->getPaperEnvelopeHeight()) echo 'selected';
                                                            if($mach->getPart() == Calculation::PAPER_ADDCONTENT2)
                                                                if ($s["width"].'x'.$s["height"] == $calc->getPaperAddContent2Width().'x'.$calc->getPaperAddContent2Height()) echo 'selected';
                                                            if($mach->getPart() == Calculation::PAPER_ADDCONTENT3)
                                                                if ($s["width"].'x'.$s["height"] == $calc->getPaperAddContent3Width().'x'.$calc->getPaperAddContent3Height()) echo 'selected';
                                                            echo '>';
                                                            echo $s["width"].' x '.$s["height"].'</option>';
                                                        }
                                                        echo '</select>';

                                                        ?>
                                                    </div>
                                                </td>
                                                <?php
                                                break;
                                            case Machine::TYPE_DRUCKMASCHINE_OFFSET:
                                                ?>
                                                <td id="mach_parts_<? echo $x;?>">
                                                    <div class="form-group">
                                                        <select name="mach_part_<? echo $x;?>" id="mach_part_<? echo $x;?>" onchange="updateAvailPapers(<? echo $x;?>)" class="form-control">
                                                            <?php
                                                            foreach ($contents as $item) {
                                                                echo '<option value="'.$item['paper'].'" ';
                                                                if($mach->getPart() == $item['paper']) echo 'selected';
                                                                echo '>'.$item['name'].'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="row" id="td-machopts-<?php echo $x;?>">
                                                    <?php
                                                    // Falls Maschine manuell berechnet wird, Feld anzeigen
                                                    if($mach->getMachine()->getPriceBase() == Machine::PRICE_VARIABEL)
                                                    {
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Preis</label><div class="input-group">';
                                                        echo '<input name="mach_manprice_'.$x.'" value="'.printPrice($mach->getPrice()).'" class="form-control">';
                                                        echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        if($mach->getMachineGroupObject()->getType() == MachineGroup::TYPE_EXTERN){
                                                            echo '<div class="col-md-4"><div class="form-group">';
                                                            echo '<label class="control-label">EK</label><div class="input-group">';
                                                            echo '<input name="mach_supplierprice_'.$x.'" class="form-control" title="'.$_LANG->get('Einkaufspreis').'"';
                                                            echo 'value="'.printPrice($mach->getSupplierPrice()).'" type="text" >';
                                                            echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        }
                                                    }

                                                    // Option Lack
                                                    if($mach->getMachine()->getFinish())
                                                    {
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Lack</label><div class="input-group">';
                                                        echo '<select name="mach_finishing_'.$x.'" id="mach_finishing_'.$x.'" class="form-control">';
                                                        echo '<option value="0">'.$_LANG->get('kein Lack').'</option>';
                                                        foreach($finishings as $f)
                                                        {
                                                            echo '<option value="'.$f->getId().'" ';
                                                            if($mach->getFinishing()->getId() == $f->getId()) echo "selected";
                                                            echo '>'.$f->getName().'</option>';
                                                        }
                                                        echo '</select>';
                                                        echo '</div></div></div>';
                                                    }




                                                    // Rollendruck
                                                    if ($calc->getPaperContent()->getRolle() == 1)
                                                    {
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Etiketten pro Rolle</label><div class="input-group">';
                                                        echo '<input name="mach_labelcount_'.$x.'" class="form-control" type="text" value="'.$mach->getLabelcount().'">';
                                                        echo '</div></div></div>';

                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Laufmeter pro Rolle</label><div class="input-group">';
                                                        echo '<input name="mach_rollcount_'.$x.'" class="form-control" type="text" value="'.$mach->getRollcount().'">';
                                                        echo '</div></div></div>';
                                                    }

                                                    // Laufrichtung
                                                    echo '<div class="col-md-4"><div class="form-group">';
                                                    echo '<label class="control-label">Laufrichtung</label><div class="input-group">';
                                                    echo '<select name="mach_roll_dir_'.$x.'" class="form-control">';
                                                    echo '<option value="0" ';
                                                    if ($mach->getRoll_dir() == 0) echo 'selected';
                                                    echo '>';
                                                    echo 'auto</option>';
                                                    echo '<option value="1" ';
                                                    if ($mach->getRoll_dir() == 1) echo 'selected';
                                                    echo '>';
                                                    echo 'breite Bahn</option>';
                                                    echo '<option value="2" ';
                                                    if ($mach->getRoll_dir() == 2) echo 'selected';
                                                    echo '>';
                                                    echo 'schmale Bahn</option>';
                                                    echo '</select>';
                                                    echo '</div></div></div>';

                                                    // Manueller Aufschlag
                                                    echo '<div class="col-md-4"><div class="form-group">';
                                                    echo '<label class="control-label">Manueller Aufschlag</label><div class="input-group">';
                                                    echo '<input type="text" name="mach_special_margin_'.$x.'" class="form-control" value="'.str_replace(".", ",", $mach->getSpecial_margin()).'">';
                                                    echo '<span class="input-group-addon">%</span></div></div></div>';

                                                    echo '<div class="col-md-4"><div class="form-group">';
                                                    echo '<label class="control-label">Aufschlag Text</label><div class="input-group">';
                                                    echo '<input type="text" name="mach_special_margin_text_'.$x.'" class="form-control" value="'.$mach->getSpecial_margin_text().'">';
                                                    echo '</div></div></div>';



                                                    // ZuschussDP
                                                    echo '<div class="col-md-4"><div class="form-group">';
                                                    echo '<label class="control-label">Zuschuss Bogen <br/> / pro Druckplatte</label><div class="input-group">';
                                                    echo '<input name="mach_dpgrant_'.$x.'" id="mach_dpgrant_'.$x.'" class="form-control" value="'.printPrice($mach->getDpgrant(),2).'">';
                                                    echo '</div></div></div>';

                                                    // ZuschussPercent
                                                    echo '<div class="col-md-4"><div class="form-group">';
                                                    echo '<label class="control-label">Zuschuss in % Weiterverabeitung / Fortdruck</label><div class="input-group">';
                                                    echo '<input name="mach_percentgrant_'.$x.'" id="mach_percentgrant_'.$x.'" class="form-control" value="'.printPrice($mach->getPercentgrant(),2).'">';
                                                    echo '<span class="input-group-addon">%</span></div></div></div>';

                                                    // Farbton
                                                    echo '<div class="col-md-4"><div class="form-group">';
                                                    echo '<label class="control-label">Farbton <br/>Sonderfarbe / Info</label><div class="input-group">';
                                                    echo '<input name="mach_color_detail_'.$x.'" id="mach_color_detail_'.$x.'" class="form-control" value="'.$mach->getColor_detail().'">';
                                                    echo '</div></div></div>';
                                                    // Hinweise
                                                    echo '<div class="col-md-12"><div class="form-group">';
                                                    echo '<label class="control-label">Hinweise</label><div class="input-group col-md-12">';
                                                    echo '<input name="mach_info_'.$x.'" id="mach_info_'.$x.'" class="form-control" value="'.$mach->getInfo().'">';
                                                    echo '</div></div></div>';

                                                    ?>
                                                </td>
                                                <td id="td-papersize-<? echo $x;?>">
                                                    <div class="form-group">
                                                        <?php
                                                        if ($mach->getPart() == Calculation::PAPER_CONTENT)
                                                            $sizes = $calc->getPaperContent()->getAvailablePaperSizesForMachine($mach->getMachine(), $calc->getProductFormatWidthOpen(), $calc->getProductFormatHeightOpen(), $calc->getPaperContent()->getRolle(), $calc->getProductFormatHeightOpen());
                                                        else if ($mach->getPart() == Calculation::PAPER_ADDCONTENT)
                                                            $sizes = $calc->getPaperAddContent()->getAvailablePaperSizesForMachine($mach->getMachine(), $calc->getProductFormatWidthOpen(), $calc->getProductFormatHeightOpen(), $calc->getPaperAddContent()->getRolle(), $calc->getProductFormatHeightOpen());
                                                        else if ($mach->getPart() == Calculation::PAPER_ENVELOPE)
                                                            $sizes = $calc->getPaperEnvelope()->getAvailablePaperSizesForMachine($mach->getMachine(), $calc->getEnvelopeWidthOpen(), $calc->getEnvelopeHeightOpen(), $calc->getPaperEnvelope()->getRolle(), $calc->getProductFormatHeightOpen());
                                                        else if ($mach->getPart() == Calculation::PAPER_ADDCONTENT2)
                                                            $sizes = $calc->getPaperAddContent2()->getAvailablePaperSizesForMachine($mach->getMachine(), $calc->getProductFormatWidthOpen(), $calc->getProductFormatHeightOpen(), $calc->getPaperAddContent2()->getRolle(), $calc->getProductFormatHeightOpen());
                                                        else if ($mach->getPart() == Calculation::PAPER_ADDCONTENT3)
                                                            $sizes = $calc->getPaperAddContent3()->getAvailablePaperSizesForMachine($mach->getMachine(), $calc->getProductFormatWidthOpen(), $calc->getProductFormatHeightOpen(), $calc->getPaperAddContent3()->getRolle(), $calc->getProductFormatHeightOpen());

                                                        echo '<select name="mach_papersize_'.$x.'" class="form-control">';
                                                        foreach($sizes as $s)
                                                        {
                                                            echo '<option value="'.$s["width"].'x'.$s["height"].'" ';
                                                            if($mach->getPart() == Calculation::PAPER_CONTENT)
                                                                if ($s["width"].'x'.$s["height"] == $calc->getPaperContentWidth().'x'.$calc->getPaperContentHeight()) echo 'selected';
                                                            if($mach->getPart() == Calculation::PAPER_ADDCONTENT)
                                                                if ($s["width"].'x'.$s["height"] == $calc->getPaperAddContentWidth().'x'.$calc->getPaperAddContentHeight()) echo 'selected';
                                                            if($mach->getPart() == Calculation::PAPER_ENVELOPE)
                                                                if ($s["width"].'x'.$s["height"] == $calc->getPaperEnvelopeWidth().'x'.$calc->getPaperEnvelopeHeight()) echo 'selected';
                                                            if($mach->getPart() == Calculation::PAPER_ADDCONTENT2)
                                                                if ($s["width"].'x'.$s["height"] == $calc->getPaperAddContent2Width().'x'.$calc->getPaperAddContent2Height()) echo 'selected';
                                                            if($mach->getPart() == Calculation::PAPER_ADDCONTENT3)
                                                                if ($s["width"].'x'.$s["height"] == $calc->getPaperAddContent3Width().'x'.$calc->getPaperAddContent3Height()) echo 'selected';
                                                            echo '>';
                                                            echo $s["width"].' x '.$s["height"].'</option>';
                                                        }
                                                        echo '</select>';

                                                        // Umschlagen / Umstuelpen
                                                        if ($mach->getMachine()->getUmschlUmst() > 0)
                                                        {
                                                            echo '<div class="form-group">';
                                                            echo '<select name="umschl_umst_'.$x.'" class="form-control" style="margin-top:10px;">';
                                                            ?>
                                                            <option value="0" <?php if(!$mach->getUmschlagenUmstuelpen()) echo ' selected ';?>>Sch&ouml;n & Widerdruck</option>
                                                            <option value="1" <?php if($mach->getUmschlagenUmstuelpen()) echo ' selected ';?>>Umschlagen / Umst&uuml;lpen</option>
                                                            </select>
                                                            </div>
                                                            <?php
                                                        }
                                                        ?>
                                                    </div>
                                                </td>
                                                <?php
                                                break;
                                            case Machine::TYPE_DRUCKMASCHINE_ROLLENOFFSET:
                                                ?>
                                                <td id="td-machparts-<? echo $x;?>"></td>
                                                <td></td>
                                                <?php
                                                break;
                                            case Machine::TYPE_FOLDER:
                                                ?>
                                                <td id="td-machparts-<? echo $x;?>">
                                                    <div class="form-group">
                                                        <select name="mach_part_<? echo $x;?>" id="mach_part_<? echo $x;?>" onchange="updateAvailPapers(<? echo $x;?>)" class="form-control">
                                                            <?php
                                                            foreach ($contents as $item) {
                                                                echo '<option value="'.$item['paper'].'" ';
                                                                if($mach->getPart() == $item['paper']) echo 'selected';
                                                                echo '>'.$item['name'].'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="row" id="td-machopts-<?php echo $x;?>">
                                                    <?php
                                                    // Falls Maschine manuell berechnet wird, Feld anzeigen
                                                    if($mach->getMachine()->getPriceBase() == Machine::PRICE_VARIABEL)
                                                    {
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Preis</label><div class="input-group">';
                                                        echo '<input name="mach_manprice_'.$x.'" value="'.printPrice($mach->getPrice()).'" class="form-control">';
                                                        echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        if($mach->getMachineGroupObject()->getType() == MachineGroup::TYPE_EXTERN){
                                                            echo '<div class="col-md-4"><div class="form-group">';
                                                            echo '<label class="control-label">EK</label><div class="input-group">';
                                                            echo '<input name="mach_supplierprice_'.$x.'" class="form-control" title="'.$_LANG->get('Einkaufspreis').'"';
                                                            echo 'value="'.printPrice($mach->getSupplierPrice()).'" type="text" >';
                                                            echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        }
                                                    }

                                                    // Falzart
                                                    echo '<div class="col-md-4"><div class="form-group">';
                                                    echo '<label class="control-label">Falzart</label><div class="input-group">';
                                                    echo '<select name="mach_foldtype_'.$x.'" class="form-control">';
                                                    echo '<option value="0">&lt; '.$_LANG->get('Bitte w&auml;hlen').' &gt;</option>';
                                                    foreach($foldtypes as $ft)
                                                    {
                                                        if ($mach->getMachine()->getBreaks() >= $ft->getBreaks()){
                                                            echo '<option value="'.$ft->getId().'" ';
                                                            if($mach->getFoldtype()->getId() == $ft->getId()) echo "selected";
                                                            echo '>'.$ft->getName().'</option>';
                                                        }
                                                    }
                                                    echo '</select>';
                                                    echo '</div></div></div>';

                                                    // Doppelter Nutzen
                                                    echo '<div class="col-md-4"><div class="form-group">';
                                                    echo '<div class="checkbox"><label>';
                                                    echo '<input type="checkbox" id="mach_dopnutz_'.$x.'" name="mach_dopnutz_'.$x.'" value="1" ';
                                                    if($mach->getDoubleutilization()) echo ' checked="checked"';
                                                    echo '>';
                                                    echo ' Dop. Nutzen</label></div></div></div>';
                                                    ?>
                                                </td>
                                                <td></td>
                                                <?php
                                                break;
                                            case Machine::TYPE_LAGENFALZ:
                                                ?>
                                                <td id="td-machparts-<? echo $x;?>"></td>
                                                <td class="row" id="td-machopts-<?php echo $x;?>">
                                                    <?php
                                                    // Falls Maschine manuell berechnet wird, Feld anzeigen
                                                    if($mach->getMachine()->getPriceBase() == Machine::PRICE_VARIABEL)
                                                    {
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Preis</label><div class="input-group">';
                                                        echo '<input name="mach_manprice_'.$x.'" value="'.printPrice($mach->getPrice()).'" class="form-control">';
                                                        echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        if($mach->getMachineGroupObject()->getType() == MachineGroup::TYPE_EXTERN){
                                                            echo '<div class="col-md-4"><div class="form-group">';
                                                            echo '<label class="control-label">EK</label><div class="input-group">';
                                                            echo '<input name="mach_supplierprice_'.$x.'" class="form-control" title="'.$_LANG->get('Einkaufspreis').'"';
                                                            echo 'value="'.printPrice($mach->getSupplierPrice()).'" type="text" >';
                                                            echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td></td>
                                                <?php
                                                break;
                                            case Machine::TYPE_LASERCUTTER:
                                                ?>
                                                <td id="td-machparts-<? echo $x;?>">
                                                    <div class="form-group">
                                                        <select name="mach_part_<? echo $x;?>" id="mach_part_<? echo $x;?>" onchange="updateAvailPapers(<? echo $x;?>)" class="form-control">
                                                            <?php
                                                            foreach ($contents as $item) {
                                                                echo '<option value="'.$item['paper'].'" ';
                                                                if($mach->getPart() == $item['paper']) echo 'selected';
                                                                echo '>'.$item['name'].'</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                </td>
                                                <td class="row" id="td-machopts-<?php echo $x;?>">
                                                    <?php
                                                    // Falls Maschine manuell berechnet wird, Feld anzeigen
                                                    if($mach->getMachine()->getPriceBase() == Machine::PRICE_VARIABEL)
                                                    {
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Preis</label><div class="input-group">';
                                                        echo '<input name="mach_manprice_'.$x.'" value="'.printPrice($mach->getPrice()).'" class="form-control">';
                                                        echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        if($mach->getMachineGroupObject()->getType() == MachineGroup::TYPE_EXTERN){
                                                            echo '<div class="col-md-4"><div class="form-group">';
                                                            echo '<label class="control-label">EK</label><div class="input-group">';
                                                            echo '<input name="mach_supplierprice_'.$x.'" class="form-control" title="'.$_LANG->get('Einkaufspreis').'"';
                                                            echo 'value="'.printPrice($mach->getSupplierPrice()).'" type="text" >';
                                                            echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        }
                                                    }

                                                    // Doppelter Nutzen
                                                    echo '<div class="col-md-4"><div class="form-group">';
                                                    echo '<div class="checkbox"><label>';
                                                    echo '<input type="checkbox" id="mach_dopnutz_'.$x.'" name="mach_dopnutz_'.$x.'" value="1" ';
                                                    if($mach->getDoubleutilization()) echo ' checked="checked"';
                                                    echo '>';
                                                    echo ' Dop. Nutzen</label></div></div></div>';

                                                    ?>
                                                </td>
                                                <td></td>
                                                <?php
                                                break;
                                            case Machine::TYPE_MANUELL:
                                                ?>
                                                <td id="td-machparts-<? echo $x;?>"></td>
                                                <td class="row" id="td-machopts-<?php echo $x;?>">
                                                    <?php
                                                    // Falls Maschine manuell berechnet wird, Feld anzeigen
                                                    if($mach->getMachine()->getPriceBase() == Machine::PRICE_VARIABEL)
                                                    {
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Preis</label><div class="input-group">';
                                                        echo '<input name="mach_manprice_'.$x.'" value="'.printPrice($mach->getPrice()).'" class="form-control">';
                                                        echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        if($mach->getMachineGroupObject()->getType() == MachineGroup::TYPE_EXTERN){
                                                            echo '<div class="col-md-4"><div class="form-group">';
                                                            echo '<label class="control-label">EK</label><div class="input-group">';
                                                            echo '<input name="mach_supplierprice_'.$x.'" class="form-control" title="'.$_LANG->get('Einkaufspreis').'"';
                                                            echo 'value="'.printPrice($mach->getSupplierPrice()).'" type="text" >';
                                                            echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td></td>
                                                <?php
                                                break;
                                            case Machine::TYPE_OTHER:
                                                ?>
                                                <td id="td-machparts-<? echo $x;?>"></td>
                                                <td class="row" id="td-machopts-<?php echo $x;?>">
                                                    <?php
                                                    // Falls Maschine manuell berechnet wird, Feld anzeigen
                                                    if($mach->getMachine()->getPriceBase() == Machine::PRICE_VARIABEL)
                                                    {
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Preis</label><div class="input-group">';
                                                        echo '<input name="mach_manprice_'.$x.'" value="'.printPrice($mach->getPrice()).'" class="form-control">';
                                                        echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        if($mach->getMachineGroupObject()->getType() == MachineGroup::TYPE_EXTERN){
                                                            echo '<div class="col-md-4"><div class="form-group">';
                                                            echo '<label class="control-label">EK</label><div class="input-group">';
                                                            echo '<input name="mach_supplierprice_'.$x.'" class="form-control" title="'.$_LANG->get('Einkaufspreis').'"';
                                                            echo 'value="'.printPrice($mach->getSupplierPrice()).'" type="text" >';
                                                            echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        }
                                                    }
                                                    ?>
                                                </td>
                                                <td></td>
                                                <?php
                                                break;
                                            case Machine::TYPE_SAMMELHEFTER:
                                                ?>
                                                <td id="td-machparts-<? echo $x;?>"></td>
                                                <td class="row" id="td-machopts-<?php echo $x;?>">
                                                    <?php
                                                    // Falls Maschine manuell berechnet wird, Feld anzeigen
                                                    if($mach->getMachine()->getPriceBase() == Machine::PRICE_VARIABEL)
                                                    {
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Preis</label><div class="input-group">';
                                                        echo '<input name="mach_manprice_'.$x.'" value="'.printPrice($mach->getPrice()).'" class="form-control">';
                                                        echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        if($mach->getMachineGroupObject()->getType() == MachineGroup::TYPE_EXTERN){
                                                            echo '<div class="col-md-4"><div class="form-group">';
                                                            echo '<label class="control-label">EK</label><div class="input-group">';
                                                            echo '<input name="mach_supplierprice_'.$x.'" class="form-control" title="'.$_LANG->get('Einkaufspreis').'"';
                                                            echo 'value="'.printPrice($mach->getSupplierPrice()).'" type="text" >';
                                                            echo '<span class="input-group-addon">€</span></div></div></div>';
                                                        }
                                                    }

                                                    if($calc->getPagesContent()){
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Inhalt 1</label><div class="input-group">';
                                                        echo '<select name="foldscheme_content" class="form-control">';
                                                        foreach($schemes[1] as $scheme)
                                                        {
                                                            $str = '';
                                                            if($scheme[16])
                                                                $str .= $scheme[16]." x 16, ";
                                                            if($scheme[8])
                                                                $str .= $scheme[8]." x 8, ";
                                                            if($scheme[4])
                                                                $str .= $scheme[4]." x 4, ";

                                                            $str = substr($str, 0, -2);
                                                            $val = preg_replace("/ /", '', $str);

                                                            echo '<option value="'.$val.'" ';
                                                            if($calc->getFoldschemeContent() == $val) echo "selected";
                                                            echo '>'.$str.'</option>';
                                                        }
                                                        echo '</select>';
                                                        echo '</div></div></div>';
                                                    }
                                                    if($calc->getPagesAddContent()){
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Inhalt 2</label><div class="input-group">';
                                                        echo '<select name="foldscheme_addcontent" class="form-control">';
                                                        foreach($schemes[2] as $scheme)
                                                        {
                                                            $str = '';
                                                            if($scheme[16])
                                                                $str .= $scheme[16]." x 16, ";
                                                            if($scheme[8])
                                                                $str .= $scheme[8]." x 8, ";
                                                            if($scheme[4])
                                                                $str .= $scheme[4]." x 4, ";

                                                            $str = substr($str, 0, -2);
                                                            $val = preg_replace("/ /", '', $str);

                                                            echo '<option value="'.$val.'" ';
                                                            if($calc->getFoldschemeAddContent() == $val) echo "selected";
                                                            echo '>'.$str.'</option>';
                                                        }
                                                        echo '</select>';
                                                        echo '</div></div></div>';
                                                    }
                                                    if($calc->getPagesAddContent2()){
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Inhalt 3</label><div class="input-group">';
                                                        echo '<select name="foldscheme_addcontent2" class="form-control">';
                                                        foreach($schemes[4] as $scheme)
                                                        {
                                                            $str = '';
                                                            if($scheme[16])
                                                                $str .= $scheme[16]." x 16, ";
                                                            if($scheme[8])
                                                                $str .= $scheme[8]." x 8, ";
                                                            if($scheme[4])
                                                                $str .= $scheme[4]." x 4, ";

                                                            $str = substr($str, 0, -2);
                                                            $val = preg_replace("/ /", '', $str);

                                                            echo '<option value="'.$val.'" ';
                                                            if($calc->getFoldschemeAddContent2() == $val) echo "selected";
                                                            echo '>'.$str.'</option>';
                                                        }
                                                        echo '</select>';
                                                        echo '</div></div></div>';
                                                    }
                                                    if($calc->getPagesAddContent3()){
                                                        echo '<div class="col-md-4"><div class="form-group">';
                                                        echo '<label class="control-label">Inhalt 3</label><div class="input-group">';
                                                        echo '<select name="foldscheme_addcontent3" class="form-control">';
                                                        foreach($schemes[5] as $scheme)
                                                        {
                                                            $str = '';
                                                            if($scheme[16])
                                                                $str .= $scheme[16]." x 16, ";
                                                            if($scheme[8])
                                                                $str .= $scheme[8]." x 8, ";
                                                            if($scheme[4])
                                                                $str .= $scheme[4]." x 4, ";

                                                            $str = substr($str, 0, -2);
                                                            $val = preg_replace("/ /", '', $str);

                                                            echo '<option value="'.$val.'" ';
                                                            if($calc->getFoldschemeAddContent3() == $val) echo "selected";
                                                            echo '>'.$str.'</option>';
                                                        }
                                                        echo '</select>';
                                                        echo '</div></div></div>';
                                                    }

                                                    ?>
                                                </td>
                                                <td></td>
                                                <?php
                                                break;
                                        }?>
                                        <?php // Kosten ?>
                                        <td id="td-cost-<?php echo $x;?>">
                                            <?php echo printPrice($mach->getPrice())." ".$_USER->getClient()->getCurrency();?>
                                        </td>
                                        <?php // Optionen ?>
                                        <td>
                                            <?php
                                            echo '<span class="glyphicons glyphicons-plus-sign pointer" onclick="addRow('.$group->getId().')"></span>';
                                            echo '&nbsp; &nbsp;';
                                            echo '<span class="glyphicons glyphicons-minus-sign pointer" onclick="deleteRow('.$x.')"></span>';
                                            ?>
                                        </td>
                                    </tr>
                                    <?php
                                    $x++;
                                }

                                // Es sind keine Standardmaschinen eingetragen worden
                                if(!$groupHasDefaultMachs)
                                {
                                    echo '<tr id="tr_mach_'.$x.'">';
                                    echo '<td>';
                                    echo '<input type="hidden" name="mach_group_'.$x.'" value="'.$group->getId().'">';
                                    echo '<select name="mach_id_'.$x.'" class="form-control" id="mach_id_'.$x.'" onchange="updateMachineProps('.$x.', this.value)">';
                                    echo '<option value=""></option>';
                                    foreach ($groupmachs as $gm)
                                    {
                                        echo '<option value="'.$gm->getId().'" ';
                                        echo '>'.$gm->getName().'</option>';

                                    }
                                    echo '</select>';
                                    echo '</td>';
                                    echo '<td><input class="form-control" name="mach_time_'.$x.'" value="" placeholder="Min."></td>';
                                    echo '<td id="td-machparts-'.$x.'"><select name="mach_part_'.$x.'" style="display:none"></select></td>';
                                    echo '<td id="td-machopts-'.$x.'">';
                                    echo '</td>';
                                    echo '<td id="td-papersize-'.$x.'">';
                                    echo '</td>';
                                    echo '<td id="td-cost-'.$x.'">';
                                    echo '</td>';
                                    echo '<td>';
                                    echo '<span class="glyphicons glyphicons-plus-sign pointer" onclick="addRow('.$group->getId().')"></span>';
                                    echo '<span class="glyphicons glyphicons-plus-sign pointer" onclick="deleteRow('.$x.')"></span>';
                                    echo '</td>';
                                    echo '</tr>';
                                    $x++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php
        }
    }
    ?>
<hr>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Schneideoptionen</h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>&nbsp;</th>
                        <th>Inhalt 1</th>
                        <th>Inhalt 2</th>
                        <th>Inhalt 3</th>
                        <th>Inhalt 4</th>
                        <th>Umschlag</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Rohbogenformat</td>
                        <td>
                            <? if ($calc->getPagesContent() > 0 && $calc->getPaperContent()->getId() > 0) {
                                echo '<select name="format_in_content" class="form-control">';
                                foreach ($format_sizes_unique as $size)
                                {
                                    echo '<option value="'.$size['width'].'x'.$size['height'].'" ';
                                    if ($size['width'].'x'.$size['height'] == $calc->getFormat_in_content()) echo 'selected';
                                    echo '>';
                                    echo printPrice($size['width']).'x'.printPrice($size['height']).'</option>';
                                }
                                echo '</select>';
                            } ?>
                        </td>
                        <td>
                            <? 	if ($calc->getPagesAddContent() > 0 && $calc->getPaperAddContent()->getId() > 0) {
                                echo '<select name="format_in_addcontent" class="form-control">';
                                foreach ($format_sizes_unique as $size)
                                {
                                    echo '<option value="'.$size['width'].'x'.$size['height'].'" ';
                                    if ($size['width'].'x'.$size['height'] == $calc->getFormat_in_addcontent()) echo 'selected';
                                    echo '>';
                                    echo printPrice($size['width']).'x'.printPrice($size['height']).'</option>';
                                }
                                echo '</select>';
                            } ?>
                        </td>
                        <td>
                            <? 	if ($calc->getPagesAddContent2() > 0 && $calc->getPaperAddContent2()->getId() > 0) {
                                echo '<select name="format_in_addcontent2" class="form-control">';
                                foreach ($format_sizes_unique as $size)
                                {
                                    echo '<option value="'.$size['width'].'x'.$size['height'].'" ';
                                    if ($size['width'].'x'.$size['height'] == $calc->getFormat_in_addcontent2()) echo 'selected';
                                    echo '>';
                                    echo printPrice($size['width']).'x'.printPrice($size['height']).'</option>';
                                }
                                echo '</select>';
                            } ?>
                        </td>
                        <td>
                            <? 	if ($calc->getPagesAddContent3() > 0 && $calc->getPaperAddContent3()->getId() > 0) {
                                echo '<select name="format_in_addcontent3" class="form-control">';
                                foreach ($format_sizes_unique as $size)
                                {
                                    echo '<option value="'.$size['width'].'x'.$size['height'].'" ';
                                    if ($size['width'].'x'.$size['height'] == $calc->getFormat_in_addcontent3()) echo 'selected';
                                    echo '>';
                                    echo printPrice($size['width']).'x'.printPrice($size['height']).'</option>';
                                }
                                echo '</select>';
                            } ?>
                        </td>
                        <td>

                            <? 	if ($calc->getPagesEnvelope() > 0 && $calc->getPaperEnvelope()->getId() > 0) {
                                echo '<select name="format_in_envelope" class="form-control">';
                                foreach ($format_sizes_unique as $size)
                                {
                                    echo '<option value="'.$size['width'].'x'.$size['height'].'" ';
                                    if ($size['width'].'x'.$size['height'] == $calc->getFormat_in_envelope()) echo 'selected';
                                    echo '>';
                                    echo printPrice($size['width']).'x'.printPrice($size['height']).'</option>';
                                }
                                echo '</select>';
                            } ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Anschnitt</td>
                        <td>
                            <? 	if ($calc->getPagesContent() > 0 && $calc->getPaperContent()->getId() > 0) { ?>
                                <div class="input-group">
                                    <input name="cut_content" id="cut_content" class="form-control"
                                           value="<?=printPrice($calc->getCutContent())?>">
                                    <span class="input-group-addon">mm</span></div>
                            <?	} ?>
                        </td>
                        <td>
                            <? 	if ($calc->getPagesAddContent() > 0 && $calc->getPaperAddContent()->getId() > 0) { ?>
                                <div class="input-group">
                                    <input name="cut_addcontent" id="cut_addcontent" class="form-control"
                                           value="<?=printPrice($calc->getCutAddContent())?>">
                                    <span class="input-group-addon">mm</span></div>
                            <?	} ?>
                        </td>
                        <td>
                            <? 	if ($calc->getPagesAddContent2() > 0 && $calc->getPaperAddContent2()->getId() > 0) { ?>
                                <div class="input-group">
                                    <input name="cut_addcontent2" id="cut_addcontent2" class="form-control"
                                           value="<?=printPrice($calc->getCutAddContent2())?>">
                                    <span class="input-group-addon">mm</span></div>
                            <?	} ?>
                        </td>
                        <td>
                            <? 	if ($calc->getPagesAddContent3() > 0 && $calc->getPaperAddContent3()->getId() > 0) { ?>
                                <div class="input-group">
                                    <input name="cut_addcontent3" id="cut_addcontent3" class="form-control"
                                           value="<?=printPrice($calc->getCutAddContent3())?>">
                                    <span class="input-group-addon">mm</span></div>
                            <?	} ?>
                        </td>
                        <td>
                            <? 	if ($calc->getPagesEnvelope() > 0 && $calc->getPaperEnvelope()->getId() > 0) { ?>
                                <div class="input-group">
                                    <input name="cut_envelope" id="cut_envelope" class="form-control"
                                           value="<?=printPrice($calc->getCutEnvelope())?>">
                                    <span class="input-group-addon">mm</span></div>
                            <?	} ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Nutzen Rohb. </td>
                        <td>
                            <? 	if ($calc->getPagesContent() > 0 && $calc->getPaperContent()->getId() > 0) {
                                $format_in = explode("x", $calc->getFormat_in_content());
                                $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperContentHeight() * $calc->getPaperContentWidth());
                                echo 'Nutzen: ' . (int)$roh_schnitte;
                            } ?>
                        </td>
                        <td>
                            <? 	if ($calc->getPagesAddContent() > 0 && $calc->getPaperAddContent()->getId() > 0) {
                                $format_in = explode("x", $calc->getFormat_in_addcontent());
                                $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperAddContentHeight() * $calc->getPaperAddContentWidth());
                                echo 'Nutzen: ' . (int)$roh_schnitte;
                            } ?>
                        </td>
                        <td>
                            <? 	if ($calc->getPagesAddContent2() > 0 && $calc->getPaperAddContent2()->getId() > 0) {
                                $format_in = explode("x", $calc->getFormat_in_addcontent2());
                                $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperAddContent2Height() * $calc->getPaperAddContent2Width());
                                echo 'Nutzen: ' . (int)$roh_schnitte;
                            } ?>
                        </td>
                        <td>
                            <? 	if ($calc->getPagesAddContent3() > 0 && $calc->getPaperAddContent3()->getId() > 0) {
                                $format_in = explode("x", $calc->getFormat_in_addcontent3());
                                $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperAddContent3Height() * $calc->getPaperAddContent3Width());
                                echo 'Nutzen: ' . (int)$roh_schnitte;
                            } ?>
                        </td>
                        <td>
                            <? 	if ($calc->getPagesEnvelope() > 0 && $calc->getPaperEnvelope()->getId() > 0) {
                                $format_in = explode("x", $calc->getFormat_in_envelope());
                                $roh_schnitte = ((int)$format_in[0] * (int)$format_in[1]) / ($calc->getPaperEnvelopeHeight() * $calc->getPaperEnvelopeWidth());
                                echo 'Nutzen: ' . (int)$roh_schnitte;
                            } ?>
                        </td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>
<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">Verarbeitungsoptionen</h3>
	  </div>
	  <div class="panel-body">
			<div class="form-horizontal">
                <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Verarbeitung</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="text_processing" rows="5"><?=$calc->getTextProcessing()?></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="color_control" value="1" <?if($calc->getColorControl() == 1) echo 'checked="checked"';?>> Farbkontrollstreifen aktiv
                            </label>
                        </div>
                    </div>
                </div>
            </div>
	  </div>
</div>
    


<input type="hidden" name="counter_machs" id="counter_machs" value="<?=$x?>">
</form>



<script language="javascript">
	function switchUmschUmst(id)
	{
		if ($('#umschl_'+id).prop('checked'))
		{
			$('#umst_'+id).prop('checked', false);
			$('#umschl_umst_'+id).val(1);
		}
		else if ($('#umst_'+id).prop('checked'))
		{
			$('#umschl_'+id).prop('checked', false);
			$('#umschl_umst_'+id).val(1);
		}
		else
		{
			$('#umschl_umst_'+id).val(0);
		}
	}
	function addRow(group)
	{
		var counter = parseInt(document.getElementById('counter_machs').value);
		var lastcount = counter - 1;
		$.post("libs/modules/calculation/order.ajax.php",
			{exec: 'addMachineRow', group: group, idx: counter, orderId: <?=$order->getId()?>},
			function(data) {
				// Work on returned data
				document.getElementById('table_group_'+group).insertAdjacentHTML("BeforeEnd", data);
				document.getElementById('counter_machs').value = counter + 1;
			});
	}

	function addArticleRow(){
		var counter = parseInt(document.getElementById('number_of_article').value);
		var lastcount = counter - 1;
		$.post("libs/modules/calculation/order.ajax.php",
			{exec: 'addArticleRow', idy: counter, orderId: <?=$order->getId()?>},
			function(data) {
				// Work on returned data
				document.getElementById('table_article').insertAdjacentHTML("BeforeEnd", data);
				document.getElementById('number_of_article').value = counter + 1;
			});
	}

	function deleteRow(idx)
	{
		document.getElementById('mach_id_'+idx).disabled = 'true';
		document.getElementById('tr_mach_'+idx).style.display = 'none';
		document.getElementById('tr_mach_'+idx+'_1').style.display = 'none';
	}

	function deleteArticleRow(idy){
		document.getElementById('calcart_id_'+idy).disabled = 'true';
		document.getElementById('tr_calcart_'+idy).style.display = 'none';
		var num_art = parseInt(document.getElementById('number_of_article').value);
		document.getElementById('number_of_article').value = (num_art-1);
	}

	function updateMachineProps(idx, machId){
		var partId = document.getElementsByName('mach_part_'+idx)[0].value;
		$.post("libs/modules/calculation/order.ajax.php",
			{exec: 'updateMachineProps', idx: idx, machId: machId, calcId: <?=$calc->getId()?> , partId: partId},
			function(data) {
				// Work on returned data
				document.getElementById('td-machopts-'+idx).innerHTML = data;
                updatePossibleParts(idx, machId);
			});
	}

    function updatePossibleParts(idx, machId){
        $.post("libs/modules/calculation/order.ajax.php",
            {exec: 'updatePossibleParts', idx: idx, machId: machId, calcId: <?=$calc->getId()?>},
            function(data) {
                // Work on returned data
                document.getElementById('td-machparts-'+idx).innerHTML = data;
                updateAvailPapers(idx);
            });
    }

	function updateAvailPapers(idx){
		var machId = document.getElementsByName('mach_id_'+idx)[0].value;
		var partId = document.getElementsByName('mach_part_'+idx)[0].value;
		$.post("libs/modules/calculation/order.ajax.php",
			{exec: 'updateAvailPapers', idx: idx, machId: machId, calcId: <?=$calc->getId()?>, partId: partId},
			function(data) {
				// Work on returned data
				document.getElementById('td-papersize-'+idx).innerHTML = data;
				checkMashineContentCombination(idx);
			});
	}

	function updateArticle(idy){

		var amount = document.getElementById('art_amount_'+idy).value;
		amount = amount.replace("." , "");
		amount = amount.replace("," , ".");
		amount = parseFloat(amount);
		var scale = parseInt(document.getElementById('art_scale_'+idy).value);
		var artId = parseInt(document.getElementById('calcart_id_'+idy).value);


		$.post("libs/modules/calculation/order.ajax.php",
			{exec: 'calculateArticlePrice', idy: idy, artId: artId, calcId: <?=$calc->getId()?>, amount: amount, scale: scale},
			function(data) {
				// Work on returned data
				document.getElementById('art_cost_'+idy).innerHTML = data;
			});
	}

	function checkMashineContentCombination(idx){
		var alerttext = "";
		var machId = document.getElementsByName('mach_id_'+idx)[0].value;
		var partId = document.getElementsByName('mach_part_'+idx)[0].value;
		$.post("libs/modules/calculation/order.ajax.php",
			{exec: 'checkMashineContentCombination', idx: idx, machId: machId, calcId: <?=$calc->getId()?>, partId: partId},
			function(data) {
				// Work on returned data
				if (!data){
					if (partId == <?=Calculation::PAPER_CONTENT?>){
						<? echo "alerttext = '".$_LANG->get('Inhalt 1').": ".$_LANG->get('Kombination aus Maschine und Inhalt 1 pr&uuml;fen')."';";?>
					}
					if (partId == <?=Calculation::PAPER_ADDCONTENT?>){
						<? echo "alerttext = '".$_LANG->get('Inhalt 2').": ".$_LANG->get('Kombination aus Maschine und Inhalt 2 pr&uuml;fen')."';";?>
					}
					if (partId == <?=Calculation::PAPER_ENVELOPE?>){
						<? echo "alerttext = '".$_LANG->get('Umschlag').": ".$_LANG->get('Kombination aus Maschine und Umschlag pr&uuml;fen')."';";?>
					}
					if (partId == <?=Calculation::PAPER_ADDCONTENT2?>){
						<? echo "alerttext = '".$_LANG->get('Inhalt 3').": ".$_LANG->get('Kombination aus Maschine und Inhalt 3 pr&uuml;fen')."';";?>
					}
					if (partId == <?=Calculation::PAPER_ADDCONTENT3?>){
						<? echo "alerttext = '".$_LANG->get('Inhalt 4').": ".$_LANG->get('Kombination aus Maschine und Inhalt 4 pr&uuml;fen')."';";?>
					}
					alert(alerttext);
				}
			});
	}

	var errortext = "";
	<?
	// Auf Zuordnungsfehler pruefen
	// Papiere
	if($calc->getPaperContentHeight() == 0 && $calc->getPaperContentWidth() == 0 && $calc->getPaperContent()->getId())
		echo "errortext += '".$_LANG->get('Inhalt 1').": ".$_LANG->get('Zu dieser Maschine konnte kein passendes Papierformat gefunden werden')."\\n';\n";
	if($calc->getPaperAddContentHeight() == 0 && $calc->getPaperAddContentWidth() == 0 && $calc->getPaperAddContent()->getId())
		echo "errortext += '".$_LANG->get('Inhalt 2').": ".$_LANG->get('Zu dieser Maschine konnte kein passendes Papierformat gefunden werden')."\\n';\n";
	if($calc->getPaperEnvelopeHeight() == 0 && $calc->getPaperEnvelopeWidth() == 0 && $calc->getPaperEnvelope()->getId())
		echo "errortext += '".$_LANG->get('Umschlag').": ".$_LANG->get('Zu dieser Maschine konnte kein passendes Papierformat gefunden werden')."\\n';\n";
	if($calc->getPaperAddContent2Height() == 0 && $calc->getPaperAddContent2Width() == 0 && $calc->getPaperAddContent2()->getId())
		echo "errortext += '".$_LANG->get('Inhalt 3').": ".$_LANG->get('Zu dieser Maschine konnte kein passendes Papierformat gefunden werden')."\\n';\n";
	if($calc->getPaperAddContent3Height() == 0 && $calc->getPaperAddContent3Width() == 0 && $calc->getPaperAddContent3()->getId())
		echo "errortext += '".$_LANG->get('Inhalt 4').": ".$_LANG->get('Zu dieser Maschine konnte kein passendes Papierformat gefunden werden')."\\n';\n";

	// Offenes Produktformat groesser als maximales Format der ausgewaehlten Maschinen?
	// Nur in Gruppen bis einschliesslich Druck

	// Gucken, ob eine Druck-Maschine ausgewaehlt wurde, sonst koennte eine Farbeeinstellung gewaehlt werden, die nicht druckbar ist
	if (!$printer_part1_exists && $calc->getPagesContent()>0)
		echo "errortext += '".$_LANG->get('Inhalt 1').": ".$_LANG->get('Keine passende Druckmaschine gefunden')."\\n';\n";
	if (!$printer_part2_exists && $calc->getPagesAddContent()>0)
		echo "errortext += '".$_LANG->get('Inhalt 2').": ".$_LANG->get('Keine passende Druckmaschine gefunden')."\\n';\n";
	if (!$printer_part3_exists && $calc->getPagesEnvelope()>0)
		echo "errortext += '".$_LANG->get('Umschlag').": ".$_LANG->get('Keine passende Druckmaschine gefunden')."\\n';\n";
	if (!$printer_part4_exists && $calc->getPagesAddContent2()>0)
		echo "errortext += '".$_LANG->get('Inhalt 3').": ".$_LANG->get('Keine passende Druckmaschine gefunden')."\\n';\n";
	if (!$printer_part5_exists && $calc->getPagesAddContent3()>0)
		echo "errortext += '".$_LANG->get('Inhalt 4').": ".$_LANG->get('Keine passende Druckmaschine gefunden')."\\n';\n";
	?>

	// if(errortext.length > 0){
	//	alert(errortext);
	// }


	<? // ------------------------------- JavaScript fuer Zus. Positionen ---------------------------------------------- ?>

	function printPriceJs(zahl){
		//var ret = (Math.round(zahl * 100) / 100).toString(); //100 = 2 Nachkommastellen
		var ret = zahl.toFixed(2);
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
			document.getElementById('orderpos_cost_'+id_i).value = "";
		}
	}

	function clickSearch(id_i){
		var tmp_type= document.getElementById('orderpos_type_'+id_i).value;
		var str = document.getElementById('orderpos_search_'+id_i).value;

		$.post("libs/modules/calculation/order.ajax.php",
			{exec: 'searchPositions', type : tmp_type, str : str},
			function(data) {
				document.getElementById('orderpos_searchlist_'+id_i).innerHTML = data;
				document.getElementById('orderpos_searchlist_'+id_i).style.display = "";
			});
	}

	function updatePosDetails(id_i){
		var tmp_type = document.getElementById('orderpos_type_'+id_i).value;
		var tmp_objid= document.getElementById('orderpos_searchlist_'+id_i).value;

		if(tmp_type == 2){
			$.post("libs/modules/calculation/order.ajax.php",
				{exec: 'getArticleDetails', articleid: tmp_objid},
				function(data) {
					var teile = data.split("-+-+-");
					document.getElementById('orderpos_objid_'+id_i).value = teile[0];
					document.getElementById('orderpos_price_'+id_i).value = printPriceJs(parseFloat(teile[1]));
					document.getElementById('orderpos_tax_'+id_i).value = printPriceJs(parseFloat(teile[2]));
					document.getElementById('orderpos_comment_'+id_i).value = teile[4];
					document.getElementById('orderpos_comment_'+id_i).style.height = 100;
					document.getElementById('orderpos_quantity_'+id_i).value = "1";
					document.getElementById('td_totalprice_'+id_i).value = printPriceJs(parseFloat(teile[1]))+" <?=$_USER->getClient()->getCurrency()?>";
					document.getElementById('orderpos_cost_'+id_i).value = printPriceJs(parseFloat(teile[3]));
				});
		}
	}

	function updateArticlePrice(id_i){
		var type = document.getElementById('orderpos_type_'+id_i).value;
		var tmp_objid = document.getElementById('orderpos_searchlist_'+id_i).value;
		var amount = document.getElementById('orderpos_quantity_'+id_i).value;
		var scale = document.getElementById('orderpos_scale_'+id_i).value;
		var input_price = document.getElementById('orderpos_price_'+id_i).value;
		var price = 0;
		var article_price = 0;

		input_price = input_price.replace(",",".")


		if(type == <?=CalculationPosition::TYPE_ARTICLE?>){
			// Artikel Preis holen und Betrag berechnen
			$.post("libs/modules/calculation/order.ajax.php",
				{exec: 'getArticlePrice', articleid: tmp_objid, amount: amount},
				function(data) {
					var teile = data.split("-+-+-");
					// parseFloat(data) ist der Artikel Einzel-Preis
					if(scale == <?=CalculationPosition::SCALE_PER_KALKULATION?>){
						// Betrag berechnen bei Menge pro Kalkulation
						price = parseFloat(teile[0]) * amount;
					} else {
						// Betrag berechnen bei Menge pro Stueck
						if(tmp_objid == 0){
							// Kein Artikel ausgewaehlt
							article_price = parseFloat(input_price);
						} else {
							article_price = parseFloat(teile[0]);
						}
						price = article_price * amount * parseFloat(<?=$calc->getAmount()?>);
					}
					document.getElementById('td_totalprice_'+id_i).innerHTML = printPriceJs(price)+" <?=$_USER->getClient()->getCurrency()?>";
					document.getElementById('orderpos_price_'+id_i).value = printPriceJs(parseFloat(teile[0]));
					document.getElementById('orderpos_cost_'+id_i).value = printPriceJs(parseFloat(teile[1]));
				});
		} else {
			// Manuelle Position
			if(scale == <?=CalculationPosition::SCALE_PER_KALKULATION?>){
				price = amount * parseFloat(input_price);
			} else {
				price = amount * parseFloat(input_price) * parseFloat(<?=$calc->getAmount()?>);
			}
			// document.getElementById('orderpos_price_'+id_i).value = printPriceJs(parseFloat(data));
			document.getElementById('td_totalprice_'+id_i).innerHTML = printPriceJs(price)+" <?=$_USER->getClient()->getCurrency()?>";
		}
	}

	function updateArticleCosts(id_i){

	}

	<? // ---------------------------- Funktionen fuer die Erweiterung der Fremdleistungen ----------------------------- ?>

	$(function() {
		$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
		$('.mach_senddate').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
				showOn: "button",
				buttonImage: "images/icons/calendar-blue.png",
				buttonImageOnly: true,
				onSelect: function(selectedDate) {
					checkDate(selectedDate);
				}
			});
	});

	$(function() {
		$.datepicker.setDefaults($.datepicker.regional['<?=$_LANG->getCode()?>']);
		$('.mach_receivedate').datepicker(
			{
				showOtherMonths: true,
				selectOtherMonths: true,
				dateFormat: 'dd.mm.yy',
				showOn: "button",
				buttonImage: "images/icons/calendar-blue.png",
				buttonImageOnly: true,
				onSelect: function(selectedDate) {
					checkDate(selectedDate);
				}
			});
	});

	function angeschnitten(id,ele)
	{
		var cuts = parseInt($('#mach_cutter_cuts_'+id).val());
		if (isNaN(cuts))
		{
			alert("Bitte zuerst Schnitte angeben!");
		} else {
			if ($(ele).prop( "checked" )){
				cuts = cuts*2;
				$('#mach_cutter_cuts_'+id).val(cuts);
			} else {
				cuts = cuts/2;
				$('#mach_cutter_cuts_'+id).val(cuts);
			}
		}
	}
</script>
<script type="text/javascript" src="./jscripts/jquery.easing.1.3.js"></script>