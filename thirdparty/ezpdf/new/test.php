<?php
//===================================================================================================
// this is the php file which creates the readme.pdf file, this is not seriously 
// suggested as a good way to create such a file, nor a great example of prose,
// but hopefully it will be useful
//
// adding ?d=1 to the url calling this will cause the pdf code itself to ve echoed to the 
// browser, this is quite useful for debugging purposes.
// there is no option to save directly to a file here, but this would be trivial to implement.
//
// note that this file comprisises both the demo code, and the generator of the pdf documentation
//
//===================================================================================================


// don't want any warnings turning up in the pdf code if the server is set to 'anal' mode.
//error_reporting(7);
error_reporting(-1);

include './src/Cezpdf.php';


$pdf = new Cezpdf("A4", "portrait");

// $pdf->ezImage("HWD.pdf",5,500,'none','center');

$pdf->ezText("Test<t>1234", 14);
$pdf->ezText("Testo<tab>1234", 14);


$pdf->ezStream();
// $fp = fopen("test.pdf", "w");
// $pdfdata    = $pdf->output();
// fwrite($fp, $pdfdata);
// fclose($fp);
?>