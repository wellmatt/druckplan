<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */

chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/modules/export/exportjson.class.php';

if ($_REQUEST["function"]){
    if (strlen($_REQUEST["datefrom"])>0)
        $datefrom = strtotime($_REQUEST["datefrom"]);
    else
        $datefrom = null;

    if (strlen($_REQUEST["dateto"])>0)
        $dateto = strtotime($_REQUEST["dateto"]);
    else
        $dateto = null;

    $func = $_REQUEST["function"];
    $exporter = new ExportJson($datefrom, $dateto);
    $json = $exporter->$func();
}

header('Content-disposition: attachment; filename=export.json');
header('Content-type: application/json');
echo $json;
