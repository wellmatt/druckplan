<?
// ----------------------------------------------------------------------------------
// Author: Klein Druck+Medien GmbH
// Updated: 23.12.2014
// Copyright: Klein Druck+Medien GmbH - All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

require_once 'thirdparty/smarty/Smarty.class.php';
require_once 'thirdparty/tcpdf/tcpdf.php';


$orderpos = $order->getPositions();

require 'docs/templates/generel.tmpl.php';
$tmp = 'docs/tmpl_files/coldelivery.tmpl';
$datei = ckeditor_to_smarty($tmp);

$smarty->assign('OrderPos',$orderpos);

$smarty->assign('UserClient',$_USER->getClient()->getName());

$htmldump = $smarty->fetch('string:'.$datei);

// var_dump($htmltemp);

$pdf->writeHTML($htmldump);

?>