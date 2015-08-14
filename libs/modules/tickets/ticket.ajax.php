<?php
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			19.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
chdir("../../../");
require_once 'libs/basic/basic.importer.php';

/*
 * Suchfeld BusinessContacts
 */
if ($_REQUEST["ajax_action"] == "search_customer_and_cp"){
    $retval = Array();
    $cp_with_bc = ContactPerson::getAllContactPersonsWithBC($_REQUEST['term']);
    $retval = json_encode($cp_with_bc);
    header("Content-Type: application/json");
    echo $retval;
}
if ($_REQUEST["ajax_action"] == "search_customer"){
    $retval = Array();
    $customers = BusinessContact::getAllBusinessContacts(BusinessContact::ORDER_NAME, " (name1 LIKE '%{$_REQUEST['term']}%' OR name2 LIKE '%{$_REQUEST['term']}%' OR matchcode LIKE '%{$_REQUEST['term']}%') ");
    foreach ($customers as $c){
        $retval[] = Array("label" => $c->getNameAsLine(), "value" => $c->getId());
	} 
	$retval = json_encode($retval);
	header("Content-Type: application/json");
	echo $retval;
}
if ($_REQUEST["ajax_action"] == "search_customer_cp"){
    $retval = Array();
	$customer = new BusinessContact((int)$_REQUEST["customerID"]);
	$allContactPerson = ContactPerson::getAllContactPersons($customer, ContactPerson::ORDER_NAME, " AND (name1 LIKE '%{$_REQUEST['term']}%' OR name2 LIKE '%{$_REQUEST['term']}%') ");
	foreach ($allContactPerson as $cp){
	    $retval[] = Array("value" => $cp->getId(), "label" => $cp->getNameAsLine(), "mail" => $cp->getEmail(), "phone" => $cp->getPhone());
	} 
	$retval = json_encode($retval);
	header("Content-Type: application/json");
	echo $retval;
}
if ($_REQUEST["ajax_action"] == "search_article"){
    $retval = Array();
    $allArticle = Article::getAllArticle(Article::ORDER_TITLE, " AND (title LIKE '%{$_REQUEST['term']}%' OR number LIKE '%{$_REQUEST['term']}%' OR matchcode LIKE '%{$_REQUEST['term']}%') ");
    foreach ($allArticle as $a){
        $retval[] = Array("value" => $a->getId(), "label" => $a->getTitle());
    }
    $retval = json_encode($retval);
    header("Content-Type: application/json");
    echo $retval;
}
if ($_REQUEST["ajax_action"] == "setFilter_date_min"){
    $_SESSION['tkt_date_min'] = $_POST['tkt_date_min'];
}
if ($_REQUEST["ajax_action"] == "setFilter_date_max"){
    $_SESSION['tkt_date_max'] = $_POST['tkt_date_max'];
}
if ($_REQUEST["ajax_action"] == "setFilter_date_due_min"){
    $_SESSION['tkt_date_due_min'] = $_POST['tkt_date_due_min'];
}
if ($_REQUEST["ajax_action"] == "setFilter_date_due_max"){
    $_SESSION['tkt_date_due_max'] = $_POST['tkt_date_due_max'];
}
if ($_REQUEST["ajax_action"] == "setFilter_ajax_category"){
    $_SESSION['tkt_ajax_category'] = $_POST['tkt_ajax_category'];
}
if ($_REQUEST["ajax_action"] == "setFilter_ajax_state"){
    $_SESSION['tkt_ajax_state'] = $_POST['tkt_ajax_state'];
}
if ($_REQUEST["ajax_action"] == "setFilter_ajax_crtuser"){
    $_SESSION['tkt_ajax_crtuser'] = $_POST['tkt_ajax_crtuser'];
}
if ($_REQUEST["ajax_action"] == "setFilter_ajax_assigned"){
    $_SESSION['tkt_ajax_assigned'] = $_POST['tkt_ajax_assigned'];
}
if ($_REQUEST["ajax_action"] == "setFilter_ajax_showclosed"){
    $_SESSION['tkt_ajax_showclosed'] = $_POST['tkt_ajax_showclosed'];
    if ($_POST['tkt_ajax_showclosed'] == 0)
    {
        unset($_SESSION['tkt_cl_date_min']);
        unset($_SESSION['tkt_cl_date_max']);
    }
}
if ($_REQUEST["ajax_action"] == "setFilter_ajax_showdeleted"){
    $_SESSION['tkt_ajax_showdeleted'] = $_POST['tkt_ajax_showdeleted'];
    if ($_POST['tkt_ajax_showdeleted'] == 1)
    {
        unset($_SESSION['tkt_cl_date_min']);
        unset($_SESSION['tkt_cl_date_max']);
        unset($_SESSION['tkt_ajax_showclosed']);
    }
}
if ($_REQUEST["ajax_action"] == "setFilter_ajax_tourmarker"){
    $_SESSION['tkt_ajax_tourmarker'] = $_POST['tkt_ajax_tourmarker'];
}
if ($_REQUEST["ajax_action"] == "setFilter_cl_date_min"){
    $_SESSION['tkt_cl_date_min'] = $_POST['tkt_cl_date_min'];
}
if ($_REQUEST["ajax_action"] == "setFilter_cl_date_max"){
    $_SESSION['tkt_cl_date_max'] = $_POST['tkt_cl_date_max'];
}
if ($_REQUEST["ajax_action"] == "setFilter_ajax_withoutdue"){
    $_SESSION['tkt_ajax_withoutdue'] = $_POST['tkt_ajax_withoutdue'];
}
?>