<?php
/**
 *  Copyright (c) 2017 Teuber Consult + IT GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <alexander.scherer@teuber-consult.de>, 2017
 *
 */

require_once 'thirdparty/smarty/Smarty.class.php';
require_once 'thirdparty/tcpdf/tcpdf.php';

// $orderpos = $order->getPositions();
$orderpos = $order->getPositions(false,true);

require 'docs/templates/generel.tmpl.php';
$tmp = 'docs/tmpl_files/coloffer.tmpl';
$datei = ckeditor_to_smarty($tmp);

// pricetable from colinv
$pricetable = $order->getPriceTable();
$gesnetto = $pricetable['total_net'];
$sum = $pricetable['total_gross'];
$taxes = $pricetable['taxvalues'];

// Table
$smarty->assign('OrderPos',$orderpos);
$smarty->assign('DeliveryCosts',$order->getDeliveryterm()->getCharges());
$smarty->assign('SumNetto',$gesnetto);

// Steuern
$smarty->assign('Taxes',$taxes);

// Gesamtsumme
$smarty->assign('SumBrutto',$sum);

// Footer
$smarty->assign('UserClient',$_USER->getClient()->getName());
$htmldump = $smarty->fetch('string:'.$datei);
$pdf->writeHTML($htmldump);