<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'producttemplate.class.php';


if ($_REQUEST["exec"] == "save"){
    $array = [
        'name' => $_REQUEST["name"],
        'contents' => $_REQUEST["contents"],
        'envelope_pages_from' => $_REQUEST["envelope_pages_from"],
        'envelope_pages_to' => $_REQUEST["envelope_pages_to"],
        'envelope_pages_interval' => $_REQUEST["envelope_pages_interval"],
        'envelope_factor_width' => $_REQUEST["envelope_factor_width"],
        'envelope_factor_height' => $_REQUEST["envelope_factor_height"],
        'description' => $DB->escape(trim($_REQUEST["description"]))
    ];

    if ($_REQUEST['envelope'] == 1)
        $array['envelope'] = 1;
    else
        $array['envelope'] = 0;

    $array['uptuser'] = $_USER->getId();
    $array['uptdate'] = time();

    $producttemplate = new Producttemplate((int)$_REQUEST["id"], $array);
    $producttemplate->save();
    $_REQUEST["id"] = $producttemplate->getId();

    ProducttemplateMachine::deleteAllForProducttemplate($producttemplate);

    $machs = $_REQUEST['mach'];
    foreach ($machs as $mach) {
        if ($mach['enabled']){
            $macharr = [
                'producttemplate' => $producttemplate->getId(),
                'machine' => $mach['id'],
                'amount_from' => $mach['from'],
                'amount_to' => $mach['to']
            ];
            if ($mach['default'] == true)
                $macharr['default'] = 1;
            else
                $macharr['default'] = 0;
            $ptm = new ProducttemplateMachine(0, $macharr);
            $ptm->save();
            unset($ptm);
        }
    }

    ProducttemplateProductformat::deleteAllForProducttemplate($producttemplate);

    $paperformats = $_REQUEST['paperformat'];
    foreach ($paperformats as $paperformat) {
        if ($paperformat['enabled']){
            $pfarr = [
                'producttemplate' => $producttemplate->getId(),
                'paperformat' => $paperformat['id']
            ];
            $ptpf = new ProducttemplateProductformat(0, $pfarr);
            $ptpf->save();
            unset($ptpf);
        }
    }

    ProducttemplateChromaticity::deleteAllForProducttemplate($producttemplate);

    $paperchromas = $_REQUEST['paperchroma'];
    foreach ($paperchromas as $paperchroma) {
        if ($paperchroma['enabled']){
            $pcarr = [
                'producttemplate' => $producttemplate->getId(),
                'chromaticity' => $paperchroma['id']
            ];
            $ptpc = new ProducttemplateChromaticity(0, $pcarr);
            $ptpc->save();
            unset($ptpc);
        }
    }
}

$producttemplate = new Producttemplate((int)$_REQUEST["id"]);


?>

<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang','#top',null,'glyphicon-chevron-up');
$quickmove->addItem('Zurück','index.php?page=libs/modules/producttemplate/producttemplate.overview.php',null,'glyphicon-step-backward');
$quickmove->addItem('Speichern','#',"$('#form').submit();",'glyphicon-floppy-disk');
if ($producttemplate->getId()>0){
    $quickmove->addItem('Löschen', '#', "askDel('index.php?page=libs/modules/producttemplate/producttemplate.overview.php&exec=delete&delid=".$producttemplate->getId()."');", 'glyphicon-trash', true);
}
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Product Schablone - <?php echo $producttemplate->getName();?></h3>
    </div>
    <div class="panel-body">
        <form action="index.php?page=<?php echo $_REQUEST['page']; ?>" name="form" id="form" method="post" class="form-horizontal" role="form">
            <input type="hidden" name="id" value="<?php echo $_REQUEST["id"];?>">
            <input type="hidden" name="exec" value="save">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" name="name" id="name" value="<?php echo $producttemplate->getName();?>">
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Beschreibung</label>
                <div class="col-sm-10">
                    <textarea class="form-control" name="description" id="description"><?php echo $producttemplate->getDescription();?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Anz. Inhalte</label>
                <div class="col-sm-10">
                    <input type="number" class="form-control" name="contents" id="contents" value="<?php echo $producttemplate->getContents();?>">
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Umschlag</h3>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Umschlag</label>
                        <div class="col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="envelope" id="envelope" value="1" <?php if ($producttemplate->getEnvelope() == 1) echo ' checked ';?>>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Umschlag Seiten von</label>
                        <div class="col-sm-4">
                            <input type="number" class="form-control" name="envelope_pages_from" id="envelope_pages_from" value="<?php echo $producttemplate->getEnvelopePagesFrom();?>">
                        </div>
                        <label for="" class="col-sm-2 control-label">Umschlag Seiten bis</label>
                        <div class="col-sm-4">
                            <input type="number" class="form-control" name="envelope_pages_to" id="envelope_pages_to" value="<?php echo $producttemplate->getEnvelopePagesTo();?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Umschlag Seiten Interval</label>
                        <div class="col-sm-4">
                            <input type="number" class="form-control" name="envelope_pages_interval" id="envelope_pages_interval" value="<?php echo $producttemplate->getEnvelopePagesInterval();?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Umschlag Faktor Breite</label>
                        <div class="col-sm-4">
                            <input type="number" class="form-control" step="0.1" name="envelope_factor_width" id="envelope_factor_width" value="<?php echo $producttemplate->getEnvelopeFactorWidth();?>">
                        </div>
                        <label for="" class="col-sm-2 control-label">Umschlag Faktor Höhe</label>
                        <div class="col-sm-4">
                            <input type="number" class="form-control" step="0.1" name="envelope_factor_height" id="envelope_factor_height" value="<?php echo $producttemplate->getEnvelopeFactorHeight();?>">
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($producttemplate->getId() > 0){?>
                <!-- Maschinen -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">Maschinen</h3>
                    </div>
                    <div class="panel-body">
                        <?php foreach (MachineGroup::getAllMachineGroups() as $machineGroup){?>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title"><?php echo $machineGroup->getName();?></h3>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                        <tr>
                                            <th width="5%">Verf.</th>
                                            <th width="5%">Std.</th>
                                            <th>Machine</th>
                                            <th width="10%">Auflage von</th>
                                            <th width="10%">Auflage bis</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach (Machine::getAllMachines(Machine::ORDER_ID,$machineGroup->getId()) as $allMachine) {
                                            $prodtempmach = ProducttemplateMachine::getForProducttemplateAndMachine($producttemplate,$allMachine);
                                            if ($prodtempmach->getId() > 0){?>
                                                <tr>
                                                    <td><input type="hidden" name="mach[<?php echo $allMachine->getId();?>][id]" value="<?php echo $allMachine->getId();?>"><input type="checkbox" name="mach[<?php echo $allMachine->getId();?>][enabled]" checked></td>
                                                    <td><input type="checkbox" name="mach[<?php echo $allMachine->getId();?>][default]" <?php if ($prodtempmach->getDefault() == 1) echo ' checked ';?>></td>
                                                    <td><?php echo $allMachine->getName();?></td>
                                                    <td><input type="number" name="mach[<?php echo $allMachine->getId();?>][from]" value="<?php echo $prodtempmach->getAmountFrom();?>"></td>
                                                    <td><input type="number" name="mach[<?php echo $allMachine->getId();?>][to]" value="<?php echo $prodtempmach->getAmountTo();?>"></td>
                                                </tr>
                                            <?php } else {?>
                                                <tr>
                                                    <td><input type="hidden" name="mach[<?php echo $allMachine->getId();?>][id]" value="<?php echo $allMachine->getId();?>"><input type="checkbox" name="mach[<?php echo $allMachine->getId();?>][enabled]"></td>
                                                    <td><input type="checkbox" name="mach[<?php echo $allMachine->getId();?>][default]"></td>
                                                    <td><?php echo $allMachine->getName();?></td>
                                                    <td><input type="number" name="mach[<?php echo $allMachine->getId();?>][from]" value="1"></td>
                                                    <td><input type="number" name="mach[<?php echo $allMachine->getId();?>][to]" value="0"></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <!-- /Maschinen -->
                <!-- Productformate -->
                <?php $selprodformats = ProducttemplateProductformat::getAllIdsForProducttemplate($producttemplate);?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Produktformate
                        </h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                            <? $i = 1;?>
                            <tr class="<?=getRowColor($i)?>">
                                <?
                                $x = 1;
                                foreach(Paperformat::getAllPaperFormats() as $pf)
                                {
                                    echo '<td><input type="hidden" name="paperformat['.$pf->getId().'][id]" value="'.$pf->getId().'"><input name="paperformat['.$pf->getId().'][enabled]" type="checkbox" value="1" ';
                                    if(in_array($pf->getId(),$selprodformats)) echo "checked";
                                    echo '> ' .$pf->getName().' ('.$pf->getWidth().' x '.$pf->getHeight().' mm)</td>';

                                    if($x % 4 == 0)
                                    {
                                        $i++;
                                        echo '</tr><tr class="'.getRowColor($i).'">';
                                    }
                                    $x++;
                                }
                                ?>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /Productformate -->
                <!-- Farbigkeit -->
                <?php $selchromas = ProducttemplateChromaticity::getAllIdsForProducttemplate($producttemplate);?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Farbigkeit
                            <span class="pull-right">* Keine Auswahl = Alle möglich</span>
                        </h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                            <? $i = 1;?>
                            <tr class="<?=getRowColor($i)?>">
                                <?
                                $x = 1;
                                foreach(Chromaticity::getAllChromaticities() as $pf)
                                {
                                    echo '<td><input type="hidden" name="paperchroma['.$pf->getId().'][id]" value="'.$pf->getId().'"><input name="paperchroma['.$pf->getId().'][enabled]" type="checkbox" value="1" ';
                                    if(in_array($pf->getId(),$selchromas)) echo "checked";
                                    echo '> ' .$pf->getName().' </td>';

                                    if($x % 4 == 0)
                                    {
                                        $i++;
                                        echo '</tr><tr class="'.getRowColor($i).'">';
                                    }
                                    $x++;
                                }
                                ?>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /Farbigkeit -->
                <?php if ($producttemplate->getUptdate() > 0){?>
                    <div class="form-group">
                        <label for="" class="col-sm-2 control-label">Geändert</label>
                        <div class="col-sm-10 form-text">
                            <?php echo date('d.m.y H:i',$producttemplate->getUptdate()).' von '.$producttemplate->getUptuser()->getNameAsLine();?>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        </form>
    </div>
</div>

<script language="JavaScript">
    $(function () {
        var editor = CKEDITOR.replace( 'description', {
            // Define the toolbar groups as it is a more accessible solution.
            toolbarGroups: [
                { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
                { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
                { name: 'links' },
                { name: 'insert' },
                { name: 'tools' },
                { name: 'others' },
                '/',
                { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
                { name: 'paragraph',   groups: [ 'list', 'indent', 'blocks', 'align' ] },
                { name: 'styles' },
                { name: 'colors' }
            ]
            // Remove the redundant buttons from toolbar groups defined above.
            //removeButtons: 'Underline,Strike,Subscript,Superscript,Anchor,Styles,Specialchar'
        } );
    });
</script>
