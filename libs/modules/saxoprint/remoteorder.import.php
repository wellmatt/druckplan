<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */
require_once 'libs/modules/saxoprint/saxoprint.class.php';

if (strstr(__DIR__, "contilas2"))
{
    dd('Import im Testsysten nicht möglich!');
}

$saxo = new Saxoprint();
$remoteOrders = $saxo->getRemoteOrders(Saxoprint::Registered);
$postarray = [];

if ($perf->getSaxoapikey() != '' && $perf->getSaxobc()>0 && $perf->getSaxocp()>0 ){

} else {
    dd('Bitte prüfen Sie die Schnittstellen-Einstellungen');
}

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Saxoprint Import</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-hover" id="saxotable">
            <thead>
                <tr>
                    <th>OrderNumber</th>
                    <th>CompletionDate</th>
                    <th>State</th>
                    <th>VO-Nummer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($remoteOrders as $remoteOrder){
                    $states = $remoteOrder->getWorkingStates();
                    $latest = $states[count($states)-1];
                    $date = new DateTime($remoteOrder->getCompletionDate());
                    $col_inv = $remoteOrder->createColinv();
                    ?>
                    <tr id="<?php echo $remoteOrder->getOrderNumber();?>">
                        <td><?php echo $remoteOrder->getOrderNumber();?></td>
                        <td><?php echo $date->format('d-m-Y H:i:s');?></td>
                        <td><?php echo $latest->getWorkingStateText();?></td>
                        <td id="vo_<?php echo $remoteOrder->getOrderNumber();?>">
                            <?php
                            if ($col_inv === false)
                                echo 'Fehler beim Import';
                            else {
                                $postarray[] = [ "OrderNumber" => (int)$col_inv->getSaxoid(), "WorkingState" => Saxoprint::Cancelled ];
                                echo '<a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid=' . $col_inv->getId() . '">' . $col_inv->getNumber() . '</a>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php }
                $saxo->postOrderStatusMultiple($postarray);
                ?>
            </tbody>
        </table>
    </div>
</div>