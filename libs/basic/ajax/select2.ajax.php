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


if ($_REQUEST["ajax_action"] == "search_cp"){
    $items = [];
    $allContactPerson = ContactPerson::getAllContactPersons(NULL, ContactPerson::ORDER_NAME, " AND (name1 LIKE '%{$_REQUEST['term']}%' OR name2 LIKE '%{$_REQUEST['term']}%') ");
    foreach ($allContactPerson as $cp){
        $items[] = Array("id" => $cp->getId(), "text" => $cp->getNameAsLine());
    }
    $retval['items'] = $items;
    header("Content-Type: application/json");
    echo json_encode($items);
}