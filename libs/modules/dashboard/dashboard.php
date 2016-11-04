<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */


$dashentries = DashBoard::getAllForUser($_USER);
$rows = DashBoard::countRowsForUser($_USER);

for ($i = 1; $i <= $rows; $i++) {
    $wid1 = DashBoard::getForUserAndPosition($_USER,$i,1);
    $wid2 = DashBoard::getForUserAndPosition($_USER,$i,2);
    $wid3 = DashBoard::getForUserAndPosition($_USER,$i,3);

    echo '<div class="row">';
    if ($wid2->getModule() == "Keins" && $wid3->getModule() == "Keins"){
        echo '<div class="col-md-12">';
        if (file_exists('./libs/modules/dashboard/widgets/'.$wid1->getModule().'.php'))
            include './libs/modules/dashboard/widgets/'.$wid1->getModule().'.php';
        echo '</div>';
    } elseif ($wid2->getModule() == "Keins") {
        foreach (Array($wid1,$wid3) as $widget) {
            if ($widget->getModule() != "Keins") {
                echo '<div class="col-md-6">';
                if (file_exists('./libs/modules/dashboard/widgets/'.$widget->getModule().'.php'))
                    include './libs/modules/dashboard/widgets/' . $widget->getModule().'.php';
                echo '</div>';
            }
        }
    } else {
        foreach (Array($wid1,$wid2,$wid3) as $widget) {
            if ($widget->getModule() != "Keins") {
                echo '<div class="col-md-4">';
                if (file_exists('./libs/modules/dashboard/widgets/'.$widget->getModule().'.php'))
                    include './libs/modules/dashboard/widgets/' . $widget->getModule().'.php';
                echo '</div>';
            }
        }
    }
    echo '</div>';
}