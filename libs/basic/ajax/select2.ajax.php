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
require_once 'libs/modules/taxkeys/taxkey.class.php';


if ($_REQUEST["ajax_action"] == "search_cp"){
    $items = [];
    $allContactPerson = ContactPerson::getAllContactPersons(NULL, ContactPerson::ORDER_NAME, " AND (name1 LIKE '%{$_REQUEST['term']}%' OR name2 LIKE '%{$_REQUEST['term']}%') ");
    foreach ($allContactPerson as $cp){
        $items[] = Array("id" => $cp->getId(), "text" => $cp->getNameAsLine());
    }
    $retval['items'] = $items;
    header("Content-Type: application/json");
    echo json_encode($items);
} elseif ($_REQUEST["ajax_action"] == "search_commissionpartner"){
    $items = [];
    $allcommissionpartners = BusinessContact::getAllCommissionpartners(" (name1 LIKE '%{$_REQUEST['term']}%' OR name2 LIKE '%{$_REQUEST['term']}%') ");
    foreach ($allcommissionpartners as $bc){
        $items[] = Array("id" => $bc->getId(), "text" => $bc->getNameAsLine());
    }
    $retval['items'] = $items;
    header("Content-Type: application/json");
    echo json_encode($items);
} elseif ($_REQUEST["ajax_action"] == "search_taxkey"){
    $items = [];
    $alltaxkeys = TaxKey::getAll();
    foreach ($alltaxkeys as $tk){
        $items[] = Array("id" => $tk->getId(), "text" => $tk->getValue().'%');
    }
    $retval['items'] = $items;
    header("Content-Type: application/json");
    echo json_encode($items);
}