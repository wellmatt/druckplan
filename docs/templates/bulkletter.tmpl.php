<? 
// ----------------------------------------------------------------------------------
// Author: Klein Druck+Medien GmbH
// Updated: 23.12.2014
// Copyright: Klein Druck+Medien GmbH - All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/businesscontact/businesscontact.class.php';

switch ($this->getCustomerFilter()){
    case 0: $filter = BusinessContact::FILTER_CUST_SOLL; break;
    case 1: $filter = BusinessContact::FILTER_CUST_IST; break;
    case 2: $filter = BusinessContact::FILTER_CUST; break;
    case 3: $filter = BusinessContact::FILTER_SUPP; break;
    default: $filter = BusinessContact::FILTER_ALL; break;
}

$attrib_filter = $this->getCustomerAttrib();
$all_busicon = Array();

if (count($attrib_filter)>0){
    foreach ($attrib_filter as $atfil){
        $tmp_atfil = explode(",",$atfil);
        $tmp_busicon = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, $filter, $tmp_atfil[0], $tmp_atfil[1]);
        $all_busicon = array_merge($all_busicon,$tmp_busicon);
    }
} else {
    $tmp_busicon = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, $filter);
}


// Einbindung der generellen Variablen im Templatesystem

// require 'docs/templates/generel.tmpl.php';
// temp fix

    require_once 'thirdparty/smarty/Smarty.class.php';
    require_once 'libs/modules/autodoc/smarty_functions.php';
    
    // Fehlerabfang

    // Allgemeine Funktionen

    if ($smarty == NULL || is_a($smarty, 'Smarty') == FALSE)
        $smarty = new Smarty();

    $smarty->registerPlugin("function", "PrintPrice", "smarty_function_printPrice");

    $smarty->registerPlugin("function", "ReplaceLn", "smarty_function_replace_ln");

    $smarty->registerPlugin("function", "Trim", "smarty_function_trim");


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
    
// end

$tmp = 'docs/tmpl_files/bulkletter.tmpl';
$datei = ckeditor_to_smarty($tmp);

$smarty->assign('Busicons',$all_busicon);

$smarty->assign('IfEmail', Document::VERSION_EMAIL);

$smarty->assign('IfPrint', Document::VERSION_PRINT);

$smarty->assign('Version', $version);

$smarty->assign('Img',$img_path);

$smarty->assign('Text',$this->getText());

// var_dump ($datei);

$htmldump = $smarty->fetch('string:'.$datei);

// var_dump($htmltemp);

$pdf->writeHTML($htmldump);

unset($smarty);