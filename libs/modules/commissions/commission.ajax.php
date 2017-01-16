<?php
/**
 *  Copyright (c) 2017 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2017
 *
 */
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/modules/commissions/commissionlink.class.php';
require_once 'libs/modules/commissions/commission.class.php';

if ($_REQUEST["ajax_action"] == "create"){
    if ($_REQUEST["commission"] > 0){
        $commission = new Commission((int)$_REQUEST["commission"]);
        $commission->generateColinv();
        $arr = ["result"=>"true"];
        echo json_encode($arr);
    }
}