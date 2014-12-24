<?
$font = "helvetica";

$pdf->setPrintFooter(false);

$sender = $_USER->getClient()->getName()." // ";
$sender .= $_USER->getClient()->getStreet1()." // ";
$sender .= strtoupper($_USER->getClient()->getCountry()->getCode())."-". $_USER->getClient()->getPostcode()." ".$_USER->getClient()->getCity();

$pdf->SetMargins(30, 50, 15, TRUE);
$pdf->AddPage();


$pdf->SetFont($font, '', 8);
$pdf->Cell(0, 0, $sender, 0, 1);
$pdf->SetFont($font, '', 11);
$pdf->Ln(4);
$pdf->Cell(0, 0, $order->getCustomer()
    ->getNameAsLine(), 0, 1);
$pdf->MultiCell(0, 0, $order->getCustomer()->getAddressAsLine(), 0,'L');

$pdf->Ln(15);

$columsize = 47;
$pdf->Cell($columsize, 0, $_LANG->get('Ihre Nachricht'), 0, 0);
$pdf->Cell($columsize, 0, $_LANG->get('Ihr Zeichen'), 0, 0);
$pdf->Cell($columsize, 0, $_LANG->get('Unser Zeichen'), 0, 0);
$pdf->Cell($columsize, 0, $_LANG->get('Datum'), 0, 1);

$pdf->MultiCell($columsize, 0, $order->getCustMessage(), 0,'L', 0, 0);
$pdf->MultiCell($columsize, 0,  $order->getCustSign(), 0,'L', 0, 0);
$pdf->MultiCell($columsize, 0, substr($order->getInternContact()->getFirstname(),0,2).substr($order->getInternContact()->getLastname(),0,2), 0,'L', 0, 0);
$pdf->Cell($columsize, 0, date('d.m.Y'), 0, 1);
$pdf->Ln(14);
?>
