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
    case 0: $filter = BusinessContact::FILTER_CUST_SOLL;
    case 1: $filter = BusinessContact::FILTER_CUST_IST;
    case 2: $filter = BusinessContact::FILTER_CUST;
    case 3: $filter = BusinessContact::FILTER_SUPP;
    case 4: $filter = BusinessContact::FILTER_ALL;
}

$attrib_filter = $this->getCustomerAttrib();
$all_busicon = Array();

foreach ($attrib_filter as $atfil){
    $tmp_atfil = explode(",",$atfil);
    $tmp_busicon = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, $filter, $tmp_atfil[0], $tmp_atfil[1]);
    $all_busicon = array_merge($all_busicon,$tmp_busicon);
}


// Einbindung der generellen Variablen im Templatesystem

require 'docs/templates/generel.tmpl.php';
$tmp = 'docs/tmpl_files/bulkletter.tmpl';
$datei = ckeditor_to_smarty($tmp);

$smarty->assign('Busicons',$all_busicon);

$smarty->assign('IfEmail', self::DOCTYPE_EMAIL);

$smarty->assign('IfPrint', self::DOCTYPE_PRINT);

$smarty->assign('Version', $version);

$smarty->assign('Img',$img_path);

$smarty->assign('Text',$this->getText());

// var_dump ($datei);

$htmldump = $smarty->fetch('string:'.$datei);

// var_dump($htmltemp);

$pdf->writeHTML($htmldump);

?>