 <?php
 
 error_reporting(-1);
 ini_set('display_errors', 1);
 
// Include the main TCPDF library (search for installation path).
require_once('../tcpdf.php');

// Extend the TCPDF class to create custom Header and Footer
class MYPDF extends TCPDF {
    //Page header
    public function Header() {
        // get the current page break margin
        $bMargin = $this->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;
        // disable auto-page-break
        $this->SetAutoPageBreak(false, 0);
        // set bacground image
        $img_file = 'briefbogen_schroerluecke.jpg';
        $this->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
        // restore auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $this->setPageMark();
    }
}

// create new PDF document
$pdf = new MYPDF("L", PDF_UNIT, Array(85,55), true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('iPactor GmbH');
$pdf->SetTitle('TCPDF Example 051');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(0);
$pdf->SetFooterMargin(0);

$pdf->setFontSubsetting(false);

// $fontname = $pdf->addTTFfont('./fonts/Sansation_Regular.ttf', '', '', 32);
// echo $fontname ? 'true' : 'false';
// echo $fontname;

// set font
$pdf->SetFont('sansation_', '', 5.8);

// add a page
$pdf->AddPage();

// --- CMYK ------------------------------------------------

// $pdf->SetTextColor(100, 0, 0, 0);
// $pdf->Text(30, 92, 'Cyan', false, false, true, 1, 0, 'R');

$pdf->SetTextColor(0, 0, 0, 100);
$pdf->SetXY (5.9, 19.5, false);
$pdf->Cell(48.5, 1.49, 'Titel', 0, 0, 'L');

$pdf->SetFont($fontname, '', 8);
$pdf->SetXY (5.9, 22.354, false);
$pdf->Cell(48.5, 2.055, 'Heiko Seyfarth', 0, 0, 'L');

// $pdf->SetXY (5.9, 19.5, false);
// $pdf->Cell(48.5, 1.49, 'Titel', 0, 0, 'L');

// $pdf->SetTextColor(0, 0, 100, 0);
// $pdf->Text(30, 100, 'Yellow', false, false, true, 0, 0, 'C');

// $pdf->SetTextColor(0, 0, 0, 100);
// $pdf->Text(30, 103, 'Black', false, false, true, 0, 0, 'R');

// ---------------------------------------------------------

//Close and output PDF document
ob_end_clean();
$pdf->Output('example_051.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+