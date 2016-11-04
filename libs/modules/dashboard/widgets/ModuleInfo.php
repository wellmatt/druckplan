<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */


?>

<div class="row">
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-aqua">
            <div class="inner">
                <h3><?php echo CollectiveInvoice::getAllNewCount();?></h3>

                <p>Neue Vorgänge</p>
            </div>
            <div class="icon">
                <i class="glyphicons glyphicons-shopping-bag"></i>
            </div>
            <a href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.overview.php&filter_status=1" class="small-box-footer">Zu den Vorgängen <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-green">
            <div class="inner">
                <h3><?php echo BusinessContact::getAllBusinessContactsCount();?></h3>

                <p>Kunden</p>
            </div>
            <div class="icon">
                <i class="glyphicons glyphicons-group"></i>
            </div>
            <a href="index.php?page=libs/modules/businesscontact/businesscontact.php" class="small-box-footer">Zu den Geschäftskontakten <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-red">
            <div class="inner">
                <h3><?php echo ContactPerson::getAllContactPersonsCount();?></h3>

                <p>Ansprechpartner</p>
            </div>
            <div class="icon">
                <i class="glyphicons glyphicons-parents"></i>
            </div>
            <a href="index.php?page=libs/modules/businesscontact/contactperson.overview.php" class="small-box-footer">Zu den Ansprechpartnern <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
        <!-- small box -->
        <div class="small-box bg-yellow">
            <div class="inner">
                <h3><?php echo Ticket::getAllTicketsCount(' WHERE assigned_user = '.$_USER->getId());?></h3>

                <p>Meine Tickets</p>
            </div>
            <div class="icon">
                <i class="glyphicons glyphicons-notes-2"></i>
            </div>
            <a href="index.php?page=libs/modules/tickets/ticket.overview.php&filter_assigned=<?php echo $_USER->getId();?>" class="small-box-footer">Zu meinen Tickets <i class="fa fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
</div>
