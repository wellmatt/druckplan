<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */

chdir("../../../");
require_once 'libs/basic/basic.importer.php';

if ($_REQUEST["ajax_action"] == "getText"){
    $retval = '';
    if ($_REQUEST["id"]){
        $textblock = new TextBlock((int)$_REQUEST["id"]);
        $retval = $textblock->getText();
    }
    echo $retval;
}