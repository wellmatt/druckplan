<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/modules/saxoprint/saxoprint.class.php';

$saxo = new Saxoprint();
$remoteOrders = $saxo->getRemoteOrders(Saxoprint::Registered);

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Saxoprint Aufträge
            <span class="pull-right">
                <button class="btn btn-xs btn-success" type="button" onclick="window.location.href='index.php?page=libs/modules/saxoprint/remoteorder.import.php&doimport=1';">
                    <span class="glyphicons glyphicons-disk-import"></span> Alle Importieren
                </button>
            </span>
        </h3>
    </div>
    <div class="table-responsive">
        <table class="table table-hover" id="saxotable">
            <thead>
                <tr>
                    <th>OrderNumber</th>
                    <th>CompletionDate</th>
                    <th>State</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($remoteOrders as $remoteOrder){
                    $states = $remoteOrder->getWorkingStates();
                    $latest = $states[count($states)-1];
                    $date = date('d-m-Y H:i:s',$remoteOrder->getCompletionDate());
                    ?>
                    <tr id="<?php echo $remoteOrder->getOrderNumber();?>">
                        <td><?php echo $remoteOrder->getOrderNumber();?></td>
                        <td><?php echo $date;?></td>
                        <td><?php echo $latest->getWorkingStateText();?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>