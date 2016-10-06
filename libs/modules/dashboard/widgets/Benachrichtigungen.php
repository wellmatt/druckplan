<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/notifications/notification.class.php';

$mynotifications = Notification::getAllMyNotifications(5);
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Benachrichtigungen</h3>
    </div>
    <div class="table-responsive">
        <table class="table table-hover" style="height: 330px;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titel</th>
                    <th>Datum</th>
                </tr>
            </thead>
            <?php foreach ($mynotifications as $mynotification){?>
                <tbody>
                    <tr class="pointer" onclick="window.location.href='index.php?page=libs/modules/notifications/notification.redirect.php&exec=redirect&nid=<?php echo $mynotification->getId();?>';">
                        <td><?php echo $mynotification->getId();?></td>
                        <td><?php echo $mynotification->getTitle();?></td>
                        <td><?php echo date('d.m.y H:i',$mynotification->getCrtdate());?></td>
                    </tr>
                </tbody>
            <?php } ?>
        </table>
    </div>
</div>
