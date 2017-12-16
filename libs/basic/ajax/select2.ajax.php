<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
error_reporting(-1);
ini_set('display_errors', 1);
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/modules/taxkeys/taxkey.class.php';
require_once 'libs/modules/partslists/partslist.class.php';
require_once 'libs/modules/saxoprint/saxoprint.class.php';


if ($_REQUEST["ajax_action"] == "search_cp"){
    $items = [];
    $allContactPerson = ContactPerson::getAllContactPersons(NULL, ContactPerson::ORDER_NAME, " AND (name1 LIKE '%{$_REQUEST['term']}%' OR name2 LIKE '%{$_REQUEST['term']}%') ");
    foreach ($allContactPerson as $cp){
        $items[] = Array("id" => $cp->getId(), "text" => $cp->getNameAsLine());
    }
    $retval['items'] = $items;
    header("Content-Type: application/json");
    echo json_encode($items);
} elseif ($_REQUEST["ajax_action"] == "search_businesscontact"){
    $items = [];
    $allbcs = BusinessContact::getAllBusinessContacts( BusinessContact::ORDER_NAME, " (name1 LIKE '%{$_REQUEST['term']}%' OR name2 LIKE '%{$_REQUEST['term']}%') ");
    foreach ($allbcs as $bc){
        $items[] = Array("id" => $bc->getId(), "text" => $bc->getNameAsLine());
    }
    $retval['items'] = $items;
    header("Content-Type: application/json");
    echo json_encode($items);
} elseif ($_REQUEST["ajax_action"] == "search_collectiveinvoice"){
    $items = [];
    $allcolinvs = CollectiveInvoice::getAllCollectiveInvoice( CollectiveInvoice::ORDER_NUMBER, " AND (number LIKE '%{$_REQUEST['term']}%' OR title LIKE '%{$_REQUEST['term']}%') AND `status` = 5 ");
    foreach ($allcolinvs as $ci){
        $items[] = Array("id" => $ci->getId(), "text" => $ci->getNumber() . ' - ' . $ci->getTitle());
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
        $items[] = Array("id" => $tk->getId(), "text" => $tk->getValue().'% ('.$tk->getTypeText().')');
    }
    $retval['items'] = $items;
    header("Content-Type: application/json");
    echo json_encode($items);
}elseif ($_REQUEST["ajax_action"] == "search_revenue"){
    $items = [];
    $revenueaccounts = RevenueAccount::getAll();
    foreach ($revenueaccounts as $revenueaccount){
        $items[] = Array("id" => $revenueaccount->getId(), "text" =>$revenueaccount->getTitle());
    }
    $retval['items'] = $items;
    header("Content-Type: application/json");
    echo json_encode($items);
}elseif ($_REQUEST["ajax_action"] == "search_article"){
    $items = [];
    if (strlen($_REQUEST['term']) > 0)
        $filter = " AND (title LIKE '%{$_REQUEST['term']}%' OR number LIKE '%{$_REQUEST['term']}%' OR matchcode LIKE '%{$_REQUEST['term']}%') ";
    else
        $filter = "";
    $articles = Article::getAllArticle(Article::ORDER_ID, $filter);
    foreach ($articles as $article){
        $items[] = Array("id" => $article->getId(), "text" =>$article->getTitle().' ('.$article->getNumber().')');
    }
    $retval['items'] = $items;
    header("Content-Type: application/json");
    echo json_encode($items);
}elseif ($_REQUEST["ajax_action"] == "search_partslist"){
    $items = [];
    if (strlen($_REQUEST['term']) > 0)
        $filter = [
            [
                'column'=>'title',
                'value'=>'%'.$_REQUEST['term'].'%',
                'operator'=>' LIKE '
            ]
        ];
    else
        $filter = [];
    $partslists = Partslist::fetch();
    foreach ($partslists as $partslist){
        $items[] = Array("id" => $partslist->getId(), "text" =>$partslist->getTitle());
    }
    $retval['items'] = $items;
    header("Content-Type: application/json");
    echo json_encode($items);
}elseif ($_REQUEST["ajax_action"] == "search_saxomaterial"){
    $items = [];
    $items[] = Array("id" => 'null', "text" =>'Alle');
    if (strlen($_REQUEST['term']) > 0)
        $filter = [
            [
                'column'=>'material',
                'value'=>'%'.$_REQUEST['term'].'%',
                'operator'=>' LIKE '
            ]
        ];
    else
        $filter = [];
    $saxoinfos = SaxoprintCollectiveinvoiceInfo::fetch();
    foreach ($saxoinfos as $saxoinfo){
        $exists = false;
        foreach ($items as $item) {
            if ($item['id'] == $saxoinfo->getMaterial())
                $exists = true;
        }
        if ($exists == false)
            $items[] = Array("id" => $saxoinfo->getMaterial(), "text" =>$saxoinfo->getMaterial());
    }
    $retval['items'] = $items;
    header("Content-Type: application/json");
    echo json_encode($items);
}elseif ($_REQUEST["ajax_action"] == "search_saxoformat"){
    $items = [];
    $items[] = Array("id" => 'null', "text" =>'Alle');
    if (strlen($_REQUEST['term']) > 0)
        $filter = [
            [
                'column'=>'format',
                'value'=>'%'.$_REQUEST['term'].'%',
                'operator'=>' LIKE '
            ]
        ];
    else
        $filter = [];
    $saxoinfos = SaxoprintCollectiveinvoiceInfo::fetch();
    foreach ($saxoinfos as $saxoinfo){
        $exists = false;
        foreach ($items as $item) {
            if ($item['id'] == $saxoinfo->getFormat())
                $exists = true;
        }
        if ($exists == false)
            $items[] = Array("id" => $saxoinfo->getFormat(), "text" =>$saxoinfo->getFormat());
    }
    $retval['items'] = $items;
    header("Content-Type: application/json");
    echo json_encode($items);
}elseif ($_REQUEST["ajax_action"] == "search_saxoprodgrp"){
    $items = [];
    $items[] = Array("id" => 'null', "text" =>'Alle');
    if (strlen($_REQUEST['term']) > 0)
        $filter = [
            [
                'column'=>'prodgrp',
                'value'=>'%'.$_REQUEST['term'].'%',
                'operator'=>' LIKE '
            ]
        ];
    else
        $filter = [];
    $saxoinfos = SaxoprintCollectiveinvoiceInfo::fetch();
    foreach ($saxoinfos as $saxoinfo){
        $exists = false;
        foreach ($items as $item) {
            if ($item['id'] == $saxoinfo->getProdgrp())
                $exists = true;
        }
        if ($exists == false)
            $items[] = Array("id" => $saxoinfo->getProdgrp(), "text" =>$saxoinfo->getProdgrp());
    }
    $retval['items'] = $items;
    header("Content-Type: application/json");
    echo json_encode($items);
}