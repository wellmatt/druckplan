<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */

require_once 'shipment.class.php';

$shipment = new Shipment($_REQUEST['id']);
?>


<?php // Qickmove generation
$quickmove = new QuickMove();
$quickmove->addItem('Seitenanfang', '#top', null, 'glyphicon-chevron-up');
$quickmove->addItem('Zurück', 'index.php?page=libs/modules/shipment/shipment.overview.php', null, 'glyphicon-step-backward');
echo $quickmove->generate();
// end of Quickmove generation ?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Sendung zu <?php echo $shipment->getColinv()->getNumber();?></h3>
    </div>
    <div class="panel-body">
        <form class="form-horizontal">
            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Vorgang</label>
                <div class="col-sm-10 form-text">
                    <?php echo $shipment->getColinv()->getNumber() . " " . $shipment->getColinv()->getTitle()?>
                </div>
            </div>

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Paketnummer</label>
                <div class="col-sm-10 form-text">
                    <?php echo $shipment->getParcelNumber();?>
                </div>
            </div>

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Dienstleister</label>
                <div class="col-sm-10 form-text">
                    <?php
                    switch ($shipment->getShippingService()){
                        case "dpd":
                            echo 'DPD';
                            break;
                        case "dpd_test":
                            echo 'DPD Test';
                            break;
                    }
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Label-Größe</label>
                <div class="col-sm-10 form-text">
                    <?php
                    switch ($shipment->getLabelSize()){
                        case "PDF_A4":
                            echo 'A4';
                            break;
                        case "PDF_A6":
                            echo 'A6';
                            break;
                    }
                    if ($shipment->getPackageLabel() != '')
                        echo ' <a href="libs/modules/shipment/getlabel.php?id='.$shipment->getId().'" target="_blank">anzeigen</a>';
                    ?>
                </div>
            </div>

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Gewicht (kg)</label>
                <div class="col-sm-10 form-text">
                    <?php echo printPrice($shipment->getWeight(),2);?>
                </div>
            </div>

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Breite (cm)</label>
                <div class="col-sm-10 form-text">
                    <?php echo (int)$shipment->getWidth();?>
                </div>
            </div>

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Länge (cm)</label>
                <div class="col-sm-10 form-text">
                    <?php echo (int)$shipment->getLength();?>
                </div>
            </div>

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Höhe (cm)</label>
                <div class="col-sm-10 form-text">
                    <?php echo (int)$shipment->getHeight();?>
                </div>
            </div>

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Inhalt</label>
                <div class="col-sm-10 form-text">
                    <?php echo $shipment->getContent();?>
                </div>
            </div>

            <div class="form-group">
                <label for="" class="col-sm-2 control-label">Referenz</label>
                <div class="col-sm-10 form-text">
                    <?php echo $shipment->getReference();?>
                </div>
            </div>
        </form>
    </div>
</div>
