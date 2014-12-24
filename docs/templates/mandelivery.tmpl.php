<?
$pdf->ezText("<b>Lieferschein Nr. {$this->name}         Kunden-Nr. {$order->getCustomer()->getId()}</b>", 14);
$pdf->ezText("Auftrag: {$order->getTitle()}", 12);
$pdf->ezText("", 14);
$pdf->ezText("Sehr geehrte Damen und Herren,", 10);

$pdf->ezText("", 10);
$pdf->ezText("hiermit erhalten Sie den Lieferschein zum Auftrag {$order->getNumber()}",10);
$pdf->ezText("", 10);
//----------------------------------------------------------------------------------
unset($data);
$data = Array();
$attr = Array  (  "showHeadings" => 1, "shaded" => 1, "shadeCol" => Array(0.95,0.95,0.95), "width" => "535", "xpos" => "left", "showLines" => 1,
        "rowGap" => 2, "colGap" => 4,
        "cols" =>   Array (
                "Pos."               => Array("width" => "30", "justification" => "center"),
                "Beschreibung"       => Array("width" => "450"),
                "Menge"              => Array("width" => "55")

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
            "Menge"              => $op->getQuantity()

            );
    $x++;

}
$pdf->ezTable($data,$type,$dummy,$attr);
$pdf->ezText("", 10);


// Fußblock
$pdf->ezText("", 10);
$pdf->ezText("Ware bleibt bis zur vollst&auml;ndigen Bezahlung unser Eigentum.");
$pdf->ezText("", 10);
$pdf->ezText("Ware unbesch&auml;digt erhalten", 10);
$pdf->ezText("", 16);
$pdf->ezText("___________________________", 10);
$pdf->ezText("", 10);
$pdf->ezText("F&uuml;r R&uuml;ckfragen stehen wir Ihnen selbstverst&auml;ndlich gerne zur Verf&uuml;gung.", 10);
$pdf->ezText("", 10);
$pdf->ezText("", 10);
$pdf->ezText("Mit freundlichen Gr&uuml;&szlig;en", 10);
$pdf->ezText("{$_USER->getFirstname()} {$_USER->getLastname()}", 10);

?>