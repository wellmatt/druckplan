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
require_once 'libs/modules/autodoc/smarty_functions.php';

$calc = new Calculation($order->getPaperOrderCalc());
$tmp_supplier = new BusinessContact($order->getPaperOrderSupplier());
$tmp_paper = new Paper($this->getPaperOrderPid());

require 'docs/templates/generel.tmpl.php';
$tmp = 'docs/tmpl_files/paperorder.tmpl';
$datei = ckeditor_to_smarty($tmp);

$smarty->assign('PaperId', $tmp_paper->getName());

$smarty->assign('PaperAmount', $order->getPaperOrderBoegen());

$smarty->assign('PaperPrice', $order->getPaperOrderPrice());


$htmldump = $smarty->fetch('string:'.$datei);
$pdf->writeHTML($htmldump);

?>