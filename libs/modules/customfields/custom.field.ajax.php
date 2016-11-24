<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
//error_reporting(-1);
//ini_set('display_errors', 1);
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/modules/customfields/custom.field.class.php';


if ($_REQUEST["ajax_action"] == "getFilterField"){
    $field = new CustomField($_REQUEST["id"]);
    echo $field->generateHTML(new Article());
}