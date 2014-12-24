<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       25.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once('libs/modules/paymentterms/paymentterms.class.php');


$pdf->ezText("<b>Rechnung Nr. {$this->name}         Kunden-Nr. {$order->getCustomer()->getId()}</b>", 14);
$pdf->ezText("Auftrag: {$order->getTitle()}", 12);
$pdf->ezText("", 14);
$pdf->ezText("Sehr geehrte Damen und Herren,", 10);

$pdf->ezText("", 10);
$pdf->ezText("hiermit erhalten Sie die Rechnung zum Auftrag {$order->getNumber()}",10);
$pdf->ezText("", 10);


//----------------------------------------------------------------------------------
unset($data);
$data = Array();
$attr = Array  (  "showHeadings" => 1, "shaded" => 1, "shadeCol" => Array(0.95,0.95,0.95), "width" => "535", "xpos" => "left", "showLines" => 1,
        "rowGap" => 2, "colGap" => 4,
        "cols" =>   Array (
                "Pos."               => Array("width" => "30", "justification" => "center"),
                "Beschreibung"       => Array("width" => "285"),
                "Menge"              => Array("width" => "55"),
                "USt."               => Array("width" => "45"),
                "EP (Netto)"         => Array("width" => "60", "justification" => "right"),
                "GP (Netto)"         => Array("width" => "60", "justification" => "right")
        )
);

$orderpos = $order->getOrderPositions();
$x = 0;
$taxes = Array();
foreach($orderpos as $op)
{
    $data[$x] = Array(
            "Pos."              => ($x +1),
            "Beschreibung"       => $op->getComment(),
            "Menge"              => $op->getQuantity(),
            "USt."               => $op->getVat()." % ",
            "EP (Netto)"         => printPrice($op->getPrice())." {$_USER->getClient()->getCurrency()}  ",
            "GP (Netto)"         => printPrice($op->getNetto())." {$_USER->getClient()->getCurrency()}  "
            );
    $x++;
    $gesnetto   += $op->getNetto();
    $taxes[$op->getVat()] += $op->getNetto() / 100 * $op->getVat();
}
$pdf->ezTable($data,$type,$dummy,$attr);
$pdf->ezText("", 10);

//----------------------------------------------------------------------------------
unset($data);
$data = Array();
$attr = Array  (  "showHeadings" => 0, "shaded" => 0, "width" => "535", "xpos" => "left", "showLines" => 0,
        "rowGap" => 2, "colGap" => 4, "protectRows" => 3,
        "cols" =>   Array (
                "Eigenschaft"   => Array("width" => "455", "justification" => "right"),
                "Wert"          => Array("width" => "80", "justification" => "right")
        )
);

$sum = $gesnetto;
$data[] = Array("Eigenschaft" => "Gesamtsumme (Netto)",
        "Wert" => printPrice($gesnetto)." {$_USER->getClient()->getCurrency()}  ");
foreach($taxes as $key => $t)
{
    $data[] = Array("Eigenschaft" => "USt. ({$key}%)",
    "Wert" => printPrice($t)." {$_USER->getClient()->getCurrency()}  ");
    $sum += $t;
}
$data[] = Array("Eigenschaft" => "<b>Gesamtsumme (Brutto)</b>",
        "Wert" => "<b>".printPrice($sum)." {$_USER->getClient()->getCurrency()}  </b>");

$pdf->ezTable($data,$type,$dummy,$attr);
$pdf->ezText("", 10);

// Fußblock
unset($data);
$data = Array();
$attr = Array  (  "showHeadings" => 0, "shaded" => 0, "width" => "535", "xpos" => "left", "showLines" => 0,
        "rowGap" => 0, "colGap" => 4, "protectRows" => 18,
        "cols" =>   Array (
                "Eigenschaft"   => Array("width" => "535")
        )
);

$tmp_pay = new PaymentTerms($order->getPaymentTerm());

$data[] = Array( "Eigenschaft" => "");
$data[] = Array( "Eigenschaft" => $_LANG->get('Zahlung').": ".$tmp_pay->getComment());
$data[] = Array( "Eigenschaft" => "");
$data[] = Array( "Eigenschaft" => "Mit freundlichen Gr&uuml;&szlig;en");
$data[] = Array( "Eigenschaft" => "{$_USER->getFirstname()} {$_USER->getLastname()}");
$data[] = Array( "Eigenschaft" => "");
$data[] = Array( "Eigenschaft" => "<b>{$_USER->getClient()->getName()}</b>");
$pdf->ezTable($data,$type,$dummy,$attr);

// Werte setzen
$this->priceNetto = $gesnetto;
$this->priceBrutto = $sum;
$this->payable = mktime(0,0,0, date('m'), date('d'), date('Y')) + ($tmp_pay->getNettodays() * 86400);
?>