<?php
// ----------------------------------------------------------------------------------
// Author: Klein Druck+Medien GmbH
// Updated: 23.12.2014
// Copyright: Klein Druck+Medien GmbH - All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'thirdparty/smarty/Smarty.class.php';
require_once 'libs/modules/autodoc/smarty_functions.php';

// Fehlerabfang

// Allgemeine Funktionen

if ($smarty == NULL || is_a($smarty, 'Smarty') == FALSE)
    $smarty = new Smarty();

$smarty->registerPlugin("function", "PrintPrice", "smarty_function_printPrice");
$smarty->registerPlugin("function", "PrintPrice2", "smarty_function_printPrice2");

$smarty->registerPlugin("function", "ReplaceLn", "smarty_function_replace_ln");

$smarty->registerPlugin("function", "Trim", "smarty_function_trim");
// Allgemeine Variablen

$smarty->assign('CurrentDate', date('d.m.Y'));

// Self
try {
    $smarty->assign('Id', $this->name);
} catch (Exception $e) {}

if (is_a($_USER, "User")) {
    
    // User
    
    $smarty->assign('User', $_USER);
    
    $smarty->assign('UserFirstname', $_USER->getFirstname());
    
    $smarty->assign('UserLastname', $_USER->getLastname());
    
    // Client
    
    if (is_a($_USER->getClient(), "Client")) 

    {
        $smarty->assign('Client', $_USER->getClient());
        
        $smarty->assign('Currency', $_USER->getClient()
            ->getCurrency());
        
        $smarty->assign('CName', $_USER->getClient()
            ->getName());
        
        $smarty->assign('CStreet', $_USER->getClient()
            ->getStreets()[0]);
        
        $smarty->assign('CCounty', $_USER->getClient()
            ->getCountry());
        
        $smarty->assign('CPostcode', $_USER->getClient()
            ->getPostcode());
        
        $smarty->assign('CCity', $_USER->getClient()
            ->getCity());
        
        $smarty->assign('CPhone', $_USER->getClient()
            ->getPhone());
        
        $smarty->assign('CEmail', $_USER->getClient()
            ->getEmail());
        
        $smarty->assign('CWebsite', $_USER->getClient()
            ->getWebsite());
    }
}

if (1 == 1) { // is_a($order, "Order")
    
    // Header
    
    $smarty->assign('TheirMessage', $order->getCustMessage());
    
    $smarty->assign('TheirSign', $order->getCustSign());
    
    $smarty->assign('OurSign', substr($order->getInternContact()
        ->getFirstname(), 0, 2) . substr($order->getInternContact()
        ->getLastname(), 0, 2));
    
    // Order
    
    $smarty->assign('Order', $order);
    
    $smarty->assign('OrderAttributes', $order->getActiveAttributeItemsInput());
    
    $date = new DateTime();
    $date->setTimestamp($order->getCrtdat());
    $smarty->assign('OrderCreation', $date->format('d.m.Y'));
    
    $smarty->assign('OrderTitle', $order->getTitle());
    
    $smarty->assign('OrderId', $order->getNumber());
    
    $smarty->assign('ContactPerson', $order->getCustContactperson());
    
    $paydays = $order->getPaymentterm()->getNettodays();
    $payday = date("d.m.Y",strtotime('+'.$paydays.' days', time()));
    
    $smarty->assign('PayDate', $payday);
    
    // Customer
    
    $smarty->assign('Customer', $order->getCustomer());
    
    $smarty->assign('CustomerAttributes', $order->getCustomer()->getActiveAttributeItemsInput());
    
    $smarty->assign('CustomerId', $order->getCustomer()
        ->getId());
    
    $smarty->assign('CustomerNameSD', $order->getCustomer()
        ->getNameAsLine());
    
    $smarty->assign('CustomerName', $order->getCustomer()
        ->getNameAsLine());
    
    $smarty->assign('CustomerAddress', str_replace("\n", "<br />", $order->getCustomer()
        ->getAddressAsLine()));
    
    $smarty->assign('CustomerAddressSD', str_replace("\n", "<br />", $order->getCustomer()
        ->getAddressAsLine()));
    
    if ($order->getInvoiceAddress()->getId() >0)
    {
        $smarty->assign('CustomerName', $order->getInvoiceAddress()->getNameAsLine());
        $smarty->assign('CustomerAddress', str_replace("\n", "<br />", $order->getInvoiceAddress()->getAddressAsLine()));
    }
    
    $smarty->assign('CustomerEmail', $order->getCustomer()
        ->getEmail());
    
    $smarty->assign('CustomerPhone', $order->getCustomer()
        ->getPhone());
    
    $smarty->assign('CustomerFax', $order->getCustomer()
        ->getFax());
    
    $smarty->assign('CustomerWebsite', $order->getCustomer()
        ->getWeb());
}
?>