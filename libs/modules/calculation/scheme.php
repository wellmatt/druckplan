<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			27.06.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

$rolldir = $machentry->getRoll_dir();
$total_pages = 0;
// Basisdaten auslesen
if($part == Calculation::PAPER_CONTENT) {
	$paper = $calc->getPaperContent();
	$paperH = $calc->getPaperContentHeight();
	$paperW = $calc->getPaperContentWidth();
	$total_pages = $calc->getPagesContent();
	$total_pages = $total_pages * $calc->getAmount();
} else if ($part == Calculation::PAPER_ADDCONTENT) {
	$paper = $calc->getPaperAddContent();
	$paperH = $calc->getPaperAddContentHeight();
	$paperW = $calc->getPaperAddContentWidth();
	$total_pages = $calc->getPagesAddContent();
	$total_pages = $total_pages * $calc->getAmount();
} else if ($part == Calculation::PAPER_ADDCONTENT2) {
	$paper = $calc->getPaperAddContent2();
	$paperH = $calc->getPaperAddContent2Height();
	$paperW = $calc->getPaperAddContent2Width();
	$total_pages = $calc->getPagesAddContent2();
	$total_pages = $total_pages * $calc->getAmount();
} else if ($part == Calculation::PAPER_ADDCONTENT3) {
	$paper = $calc->getPaperAddContent3();
	$paperH = $calc->getPaperAddContent3Height();
	$paperW = $calc->getPaperAddContent3Width();
	$total_pages = $calc->getPagesAddContent3();
	$total_pages = $total_pages * $calc->getAmount();
} else if ($part == Calculation::PAPER_ENVELOPE) {
	$paper = $calc->getPaperEnvelope();
	$paperH = $calc->getPaperEnvelopeHeight();
	$paperW = $calc->getPaperEnvelopeWidth();
	$total_pages = $calc->getPagesEnvelope();
	$total_pages = $total_pages * $calc->getAmount();
} else
	die('Wrong part');
	
// echo "</br>Total Pages: " . $total_pages . "</br>";


// if($part != Calculation::PAPER_ENVELOPE){
	$width = $calc->getProductFormatWidthOpen();
	$height = $calc->getProductFormatHeightOpen();
// } else {
// 	$width = $calc->getEnvelopeWidthOpen();
// 	$height = $calc->getEnvelopeHeightOpen();
// }
$width_closed     = $calc->getProductFormatWidth();
$height_closed     = $calc->getProductFormatHeight();

// if ($width == $width_closed && $height == $height_closed){
//     if ($total_pages > 2)
//     {
//         $total_pages = ceil($total_pages / 2);
//     }
// } else {
//     if ($total_pages > 4)
//     {
//         $total_pages = ceil($total_pages / 4);
//     }
// }

if ($rolldir == 0)
    $direction = $paper->getPaperDirection($calc, $part);
elseif ($rolldir == 1) // breite bahn
{
    $direction = Paper::PAPER_DIRECTION_SMALL;
    if ($paperH > $paperW)
    {
        $paperH_temp = $paperH;
        $paperW_temp = $paperW;
        $paperH = $paperW;
        $paperW = $paperH_temp;
    }
}
else // schmale bahn
{
    $direction = Paper::PAPER_DIRECTION_WIDE;
    if ($paperW > $paperH)
    {
        $paperH_temp = $paperH;
        $paperW_temp = $paperW;
        $paperW = $paperH;
        $paperH = $paperW_temp;
    }
}

// Inhalt
if ($width_closed < $width && $width_closed != 0 )
	$multiRows = floor(ceil($width * 1.01) / $width_closed);
else
	$multiRows = 1;
if ($height_closed < $height && $height_closed != 0 )
	$multiCols = floor(ceil($height * 1.01) / $height_closed);
else
	$multiCols = 1;

// Anschnitt setzen
$tmp_anschnitt = $_CONFIG->anschnitt;
if($part == Calculation::PAPER_CONTENT){
	$tmp_anschnitt = $calc->getCutContent();
} else if ($part == Calculation::PAPER_ADDCONTENT){
	$tmp_anschnitt = $calc->getCutAddContent();
} else if ($part == Calculation::PAPER_ADDCONTENT2){
	$tmp_anschnitt = $calc->getCutAddContent2();
} else if ($part == Calculation::PAPER_ADDCONTENT3){
	$tmp_anschnitt = $calc->getCutAddContent3();
} elseif($part == Calculation::PAPER_ENVELOPE){
	$tmp_anschnitt = $calc->getCutEnvelope();
}

// Farbrand (Farbkontrollstreifen) setzen
$tmp_farbrand = $_CONFIG->farbRandBreite;
if($calc->getColorControl() == 0){
	// Wenn der Farbrand in der Kalkulation ausgestellt ist
	$tmp_farbrand = 0;
}

$product_width       = $width;
$product_height      = $height;
$product_width_closed       = $width_closed;
$product_height_closed      = $height_closed;
$usesize_width       = $product_width + $tmp_anschnitt * 2;
$usesize_height      = $product_height + $tmp_anschnitt * 2;
$product_per_line    = floor(($paperW - $mach->getBorder_left() - $mach->getBorder_right()) / $usesize_width);
$product_rows        = floor(($paperH - $mach->getBorder_top() - $mach->getBorder_bottom() - $tmp_farbrand) / $usesize_height);
$product_per_line_closed    = floor(($paperW - $mach->getBorder_left() - $mach->getBorder_right()) / $usesize_width) * $multiRows;
$product_rows_closed        = floor(($paperH - $mach->getBorder_top() - $mach->getBorder_bottom() - $tmp_farbrand) / $usesize_height) * $multiCols;
$product_per_paper   = $product_per_line * $product_rows;

$product_width2      = $height;
$product_height2     = $width;
$product_width2_closed      = $height_closed;
$product_height2_closed     = $width_closed;
$usesize_width2      = $product_width2 + $tmp_anschnitt * 2;
$usesize_height2     = $product_height2 + $tmp_anschnitt * 2;
$product_per_line2   = floor(($paperW - $mach->getBorder_left() - $mach->getBorder_right()) / $usesize_width2);
$product_rows2       = floor(($paperH - $mach->getBorder_top() - $mach->getBorder_bottom() - $tmp_farbrand) / $usesize_height2);
$product_per_line2_closed   = floor(($paperW - $mach->getBorder_left() - $mach->getBorder_right()) / $usesize_width2) * $multiCols;
$product_rows2_closed       = floor(($paperH - $mach->getBorder_top() - $mach->getBorder_bottom() - $tmp_farbrand) / $usesize_height2) * $multiRows;
$product_per_paper2  = $product_per_line2 * $product_rows2;


if($product_per_paper2 >= $product_per_paper){ //  || $rolldir == 2
	$flipped = true;
	$product_rows     = $product_rows2;
	$product_per_line = $product_per_line2;
	$product_rows_closed     = $product_rows2_closed;
	$product_per_line_closed = $product_per_line2_closed;
	
	$usesize_width = $usesize_width2;
	$usesize_height = $usesize_height2;

	$product_width    = $product_width2;
	$product_height   = $product_height2;
	$product_width_closed    = $product_width2_closed;
	$product_height_closed   = $product_height2_closed;

	$t = $multiCols;
	$multiCols = $multiRows;
	$multiRows = $t;
}

$product_per_paper   = $product_per_line * $product_rows;

if ($product_counted)
	$product_per_paper = $product_max;

// echo "</br>";
// echo "product_width       = " . $width;
// echo "</br>";
// echo "product_height      = " . $height;
// echo "</br>";
// echo "product_width_closed       = " . $width_closed;
// echo "</br>";
// echo "product_height_closed      = " . $height_closed;
// echo "</br>";
// echo "usesize_width       = " . $usesize_width;
// echo "</br>";
// echo "usesize_height      = " . $usesize_height;
// echo "</br>";
// echo "product_per_line    = " . $product_per_line;
// echo "</br>";
// echo "product_rows        = " . $product_rows;
// echo "</br>";
// echo "product_per_line_closed    = " . $product_per_line_closed;
// echo "</br>";
// echo "product_rows_closed        = " . $product_rows_closed;
// echo "</br>";
// echo "product_per_paper   = " . $product_per_line * $product_rows;
// echo "</br>";
// echo "Total Page count: " . $total_pages . "</br>";

// SCHNITTE TEST

if ($tmp_anschnitt > 0){
//     echo '</br>Berechnung mit Zwischenschnitt</br>';
//     echo 'Basis Schnitte: 4 (außen) </br>';
//     echo 'Schnitte Horizontal: ' . ($product_per_line-1)*2 . '</br>';
//     echo 'Schnitte Vertikal: ' . ($product_rows-1)*2 . '</br>';
//     echo 'Schnitte gesamt: ' . (4 + ($product_per_line-1)*2 + (($product_rows-1))*2) . '</br>';
} else {
//     echo '</br>Berechnung <u>ohne</u> Zwischenschnitt</br>';
//     echo '</br>Basis Schnitte: 4 (außen) </br>';
//     echo 'Schnitte Horizontal: ' . ($product_per_line-1)/2 . '</br>';
//     echo 'Schnitte Vertikal: ' . ($product_rows-1)/2 . '</br>';
//     echo 'Schnitte gesamt: ' . (4 + ($product_per_line-1) + ($product_rows-1)) . '</br>';
}

$schemes = Array();
// $x = 0;
$rest = 1;

$product_per_paper_tmp = $product_per_paper;
$p = 0;
if ($product_per_paper_tmp > 0)
{
	while ($total_pages > 0)
	{
		// if ($product_per_paper_tmp > $total_pages)
		// {
			// $schemes[$p] = $product_per_paper_tmp;
			// break;
		// }
		$pages = floor($total_pages / $product_per_paper_tmp);
		$rest = $total_pages % $product_per_paper_tmp;
// 		echo "Rest bei " . $total_pages . "/" . $product_per_paper_tmp . " = " . $rest . "</br>";
// 		echo "Anzahl Seiten mit " . $product_per_paper_tmp . " pro Seite: " . $pages . "</br>";
		$total_pages = $total_pages - ($pages * $product_per_paper_tmp);
		if($pages > 0)
		{
			$schemes[$p]['nutzen'] = $product_per_paper_tmp;
			$schemes[$p]['count'] = $pages;
			$p++;
		}
		// if($rest != 0 && $rest < 2)
		// {
			// $schemes[$p] = ceil($product_per_paper_tmp * $rest);
			// break;
		// }
		$product_per_paper_tmp = $product_per_paper_tmp -1;
	}
}
?>
