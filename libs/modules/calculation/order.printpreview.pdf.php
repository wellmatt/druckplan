<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			14.04.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
chdir('../../../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/paper/paper.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/foldtypes/foldtype.class.php';
require_once 'libs/modules/paperformats/paperformat.class.php';
require_once 'libs/modules/products/product.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/chromaticity/chromaticity.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/finishings/finishing.class.php';
// require_once 'thirdparty/ezpdf/class.ezpdf.php';
require_once 'thirdparty/ezpdf/new/src/Cezpdf.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

/**
 * Konvertiert Milimeter in Pixel, bei 72 DPI (Vorgabe von EzPdf) 
 * @param int $mm
 * @return number
 */
function convertMmInPx ($mm){
	return ($mm / 10 * 72 / 2.54);
}

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$calc_id = (int)$_REQUEST["calc_id"];
$part = (int)$_REQUEST["part"];
$calc = new Calculation($calc_id);
$order = new Order($calc->getOrderId());
$mach_entry = Machineentry::getMachineForPapertype($part, $calc_id);
$mach = null;
foreach ($mach_entry as $me)
{
    if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ||
        $me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
    {
        $mach = $me->getMachine();
        $machentry = $me;
    }
}
$rolldir = $machentry->getRoll_dir();

if ($mach == null)
    die('Keine passende Maschine gefunden!');

$product_max_open = (int)$_REQUEST["max"];
$product_counted = (bool)$_REQUEST["counted"];

// Basisdaten auslesen
if($part == Calculation::PAPER_CONTENT) {
	$paper = $calc->getPaperContent();
	$paper_weight = $calc->getPaperContentWeight();
	$paperH = $calc->getPaperContentHeight();
	$paperW = $calc->getPaperContentWidth();
} else if ($part == Calculation::PAPER_ADDCONTENT) {
	$paper = $calc->getPaperAddContent();
	$paper_weight = $calc->getPaperAddContentWeight();
	$paperH = $calc->getPaperAddContentHeight();
	$paperW = $calc->getPaperAddContentWidth();
} else if ($part == Calculation::PAPER_ADDCONTENT2) {
	$paper = $calc->getPaperAddContent2();
	$paper_weight = $calc->getPaperAddContent2Weight();
	$paperH = $calc->getPaperAddContent2Height();
	$paperW = $calc->getPaperAddContent2Width();
} else if ($part == Calculation::PAPER_ADDCONTENT3) {
	$paper = $calc->getPaperAddContent3();
	$paper_weight = $calc->getPaperAddContent3Weight();
	$paperH = $calc->getPaperAddContent3Height();
	$paperW = $calc->getPaperAddContent3Width();
} else if ($part == Calculation::PAPER_ENVELOPE) {
	$paper = $calc->getPaperEnvelope();
	$paper_weight = $calc->getPaperEnvelopeWeight();
	$paperH = $calc->getPaperEnvelopeHeight();
	$paperW = $calc->getPaperEnvelopeWidth();
} else
	die('Wrong part');


if($part != Calculation::PAPER_ENVELOPE){
	$width = $calc->getProductFormatWidthOpen();
	$height = $calc->getProductFormatHeightOpen();
} else {
	// $width = $calc->getEnvelopeWidthOpen();
	// $height = $calc->getEnvelopeHeightOpen();
	$width = $calc->getProductFormatWidthOpen();
	$height = $calc->getProductFormatHeightOpen();
}
$width_closed     = $calc->getProductFormatWidth();
$height_closed     = $calc->getProductFormatHeight();

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
    
if ($height == $height_closed && $width == $width_closed){
    $product_max_closed = $product_max_open;
} else {
    $product_max_closed = $product_max_open * 2;
}

// if ($product_max_open > 1) {
//     $product_max_closed = $product_max_open * 2;
// }


// Bild erzeugen
// header ("Content-type: image/jpeg");
// $im = imagecreatetruecolor($paperW, $paperH);
//header("Content-type: application/pdf");
//header("Content-disposition: inline; filename=\"Vorschau\"");


/*** / Farben setzen
$bgcolor       = ImageColorAllocate($im, 255, 255, 255);
$bordercolor   = ImageColorAllocate($im, 111, 0, 0);
$prdcolor      = ImageColorAllocate($im, 244, 244, 244);
$prdbcolor_closed     = ImageColorAllocate($im, 160, 160, 160);
$prdbcolor     = ImageColorAllocate($im, 0, 0, 160);
$arrcolor      = ImageColorAllocate($im, 0, 0, 0);
$farbcolor     = ImageColorAllocate($im, 0, 200, 0);*/

// Hintergund
// ImageFilledRectangle($im, 0, 0, $paperW, $paperH, $bordercolor);
// ImageFilledRectangle($im, $mach->getBorder_left(), $mach->getBorder_top(), 
//						($paperW - $mach->getBorder_right()), ($paperH - $mach->getBorder_bottom()), $bgcolor);

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


if($product_per_paper2 >= $product_per_paper){ //  || $rolldir == 1
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
// echo $calc->getPagesContent() . "</br>";

if ($tmp_anschnitt > 0){
    $schnitte = (4 + ($product_per_line-1)*2 + (($product_rows-1))*2);
} else {
    $schnitte = (4 + ($product_per_line-1) + ($product_rows-1));
}


// PDF initialisieren
if ($direction == Paper::PAPER_DIRECTION_SMALL){
	$pdf_direction = 'landscape';
} else {
	$pdf_direction = 'portrait';
}
$format[0] = $paperW/10;
$format[1] = $paperH/10;

$pdf = new Cezpdf($format, $pdf_direction);
$pdf->selectFont('thirdparty/ezpdf/fonts/Helvetica.afm');
$pdf->ezSetMargins(40, 40, 40, 40);


// --------------- offenes Format zeichnen---------------
//$posY = $mach->getBorder_bottom(); // Bei nicht zentraler Ausrichtung

$posY = ($paperH - ( ($product_height + $tmp_anschnitt * 2 * $multiCols) * $product_rows)) / 2;

//$tmp_freespaceH = $paperH - $mach->getBorder_bottom() - $mach->getBorder_top() - $tmp_farbrand - $usesize_height*$product_rows;
// error_log("".$posY);
// $posY = ($tmp_freespaceH/2) + $mach->getBorder_bottom();


$pdf->setColor(0.9, 0.9, 0.9);
$pdf->setStrokeColor(0.4, 0.4, 0.4);
$product_count1 = 0;
for($x = 0; $x < $product_rows; $x++){
	
	$posX = ($paperW - ( ($product_width + $tmp_anschnitt * 2 * $multiRows) * $product_per_line )) / 2;
	
	for($y = 0; $y < $product_per_line; $y++){
		
		if ($product_count1 >= $product_max_open && $product_counted == true)
			break;
		
		$tmp_xpos = convertMmInPx($posX+$tmp_anschnitt*$multiRows);
		$tmp_ypos = convertMmInPx($posY+$tmp_anschnitt);
		$tmp_width = convertMmInPx($product_width);
		$tmp_height = convertMmInPx($product_height);
		
		$pdf->filledRectangle($tmp_xpos, $tmp_ypos, $tmp_width, $tmp_height);
		$pdf->rectangle($tmp_xpos, $tmp_ypos, $tmp_width, $tmp_height);
	
		$posX += $product_width + $tmp_anschnitt * 2;
		$product_count1 = $product_count1 +1;
	}

	$posY += $product_height + $tmp_anschnitt * 2;
}


// --------------- geschlossenes Format ---------------
$posY = ($paperH - ( ($product_height_closed + $tmp_anschnitt * 2) * $product_rows_closed )) / 2;
//$posY += $mach->getBorder_bottom();

$pdf->setColor(0.9, 0.9, 0.9);
$pdf->setStrokeColor(0.4, 0.4, 0.4);
$countY = 1;
$product_count2 = 0;
for($x = 0; $x < $product_rows_closed; $x++){
	
	$posX = ($paperW - ( ($product_width_closed + $tmp_anschnitt * 2) * $product_per_line_closed )) / 2;
	$countX = 1;

	for($y = 0; $y < $product_per_line_closed; $y++){
	
		if ($product_count2 >= $product_max_closed && $product_counted == true)
			break;
	
		$tmp_xpos = convertMmInPx($posX+$tmp_anschnitt*$multiRows);
		$tmp_ypos = convertMmInPx($posY+$tmp_anschnitt);
		$tmp_width = convertMmInPx($product_width_closed);
		$tmp_height = convertMmInPx($product_height_closed);

		$pdf->filledRectangle($tmp_xpos, $tmp_ypos, $tmp_width, $tmp_height, $prdcolor);
		$pdf->rectangle($tmp_xpos, $tmp_ypos, $tmp_width, $tmp_height);

		$posX += $product_width_closed;
		if($countX % $multiRows == 0)
			$posX += $tmp_anschnitt * 2;
		$countX++;
		$product_count2 = $product_count2 +1;
	}

	$posY += $product_height_closed;
	if($countY % $multiCols == 0)
		$posY += $tmp_anschnitt * 2;
	$countY++;
}


// --------------- Farb-Kontrollstreifen darstellen ---------------
$mach_top = $pdf->ez["pageHeight"] - convertMmInPx($mach->getBorder_top());
if($tmp_farbrand > 0){
	//$farbStreifenBreite = ($paperW - $mach->getBorder_left() - $mach->getBorder_right()) / 4;
	$farbStreifenBreite =  ($pdf->ez['pageWidth'] - (($mach->getBorder_left()/10 * 72) / 2.54) - (($mach->getBorder_right()/10 * 72) / 2.54) ) /4;
	$farbStreifenHoehe = (($tmp_farbrand / 10 * 72) / 2.54);
	$x = $pdf->ez['pageWidth']/2;
	
	$pdf->setColor(1,1,0);
	$pdf->filledRectangle($x, $mach_top, $farbStreifenBreite, $farbStreifenHoehe);
	$pdf->setColor(1,0,1);
	$pdf->filledRectangle($x-$farbStreifenBreite, $mach_top, $farbStreifenBreite, $farbStreifenHoehe);
	$pdf->setColor(0,1,1);
	$pdf->filledRectangle($x-$farbStreifenBreite*2, $mach_top, $farbStreifenBreite, $farbStreifenHoehe);
	$pdf->setColor(0,0,0);
	$pdf->filledRectangle($x+$farbStreifenBreite, $mach_top, $farbStreifenBreite, $farbStreifenHoehe);
	
}

// Durckma�e etc auf dem PDF ausgeben:

$text= "Auftrag: " . $order->getNumber();

if($rolldir == 0)
{
    if($calc->getPaperContent()->getPaperDirection($calc, Calculation::PAPER_CONTENT) == Paper::PAPER_DIRECTION_SMALL){
    	$laufrichtung = "schmale Bahn";
    } else {
    	$laufrichtung = "breite Bahn";
    }
} elseif ($rolldir == 1)
{
    $laufrichtung = "breite Bahn";
} elseif ($rolldir == 2)
{
    $laufrichtung = "schmale Bahn";
}

$pdf->setColor(0, 0, 0);
$text_options=array(left=>50,right=>50,justification=>"center");
$pdf->ezText($text, 50, $text_options);

// Laufrichtung einzeichnen
$pdf->y = $mach_top - 30;
$pdf->setColor(0.4, 0.4, 0.4);

$pdf->y = $mach_top - 160;
 $data = Array();
 $attr = Array  ("showHeadings" => 0, "shaded" => 0, "width"  => "950",   "xPos" => "left", "xOrientation" => "right",
		"showLines"    => 0, "rowGap" => 1, "colGap" => 2,   "titleFontSize" => 8,
		"fontSize"     => 28, "cols" => Array		(	 	"Eigenschaft"   => Array("width" => "350", "justification" => "right"),
															"Wert"          => Array("width" => "600", "justification" => "left")
		));
//  $data[] = Array(	"Eigenschaft"		=> "Kunde: ",
// 					"Wert" 				=> $order->getCustomer()->getNameAsLine());
					
//  $data[] = Array(	"Eigenschaft"		=> "Anschrift: ",
// 					"Wert" 				=> $order->getCustomer()->getAddress1() . "\n" .  $order->getCustomer()->getAddress2() . "\n" .  $order->getCustomer()->getZip() . " " .  $order->getCustomer()->getCity() . "\n");
					
 if($order->getCrtdat() > 0)
	$crt_date = date('d.m.Y', $order->getCrtdat());
 else
	$crt_date = "";

$data[] = Array(	"Eigenschaft"		=> "Auftrag: ",
					"Wert" 				=> $order->getNumber());

$data[] = Array(	"Eigenschaft"		=> "Titel: ",
					"Wert" 				=> $order->getTitle());

 $data[] = Array(	"Eigenschaft"		=> "Auft.-Datum: ",
					"Wert" 				=> $crt_date);

$data[] = Array(	"Eigenschaft"		=> " ",
					"Wert" 				=> " ");
					
 $data[] = Array(	"Eigenschaft"		=> "Maschine: ",
					"Wert" 				=> $mach->getName());
					
 $data[] = Array(	"Eigenschaft"		=> "Laufrichtung: ",
					"Wert" 				=> $laufrichtung);
 
 if($mach->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET) {
     if ((int)$machentry->getUmschl() == 1)
     {
         $data[] = Array(	"Eigenschaft"		=> "Druckart: ",
        					"Wert" 				=> "Umschlagen");
     }
     elseif ((int)$machentry->getUmst() == 1)
     {
         $data[] = Array(	"Eigenschaft"		=> "Druckart: ",
        					"Wert" 				=> "Umschtuelpen");
     }
     else
     {
         $data[] = Array(	"Eigenschaft"		=> "Druckart: ",
        					"Wert" 				=> "Schoen & Wider");
     }
 }

 $data[] = Array(	"Eigenschaft"		=> " ",
					"Wert" 				=> " ");
					
 $data[] = Array(	"Eigenschaft"		=> "Papier: ",
					"Wert" 				=> $paper->getName());
					
 $data[] = Array(	"Eigenschaft"		=> "Papiergewicht: ",
					"Wert" 				=> $paper_weight . 'g');
					
 $data[] = Array(	"Eigenschaft"		=> "Bogengroesse: ",
					"Wert" 				=> $paperW . "x" . $paperH);
					
 $data[] = Array(	"Eigenschaft"		=> " ",
					"Wert" 				=> " ");
					
 $data[] = Array(	"Eigenschaft"		=> "Produkt: ",
					"Wert" 				=> $order->getProduct()->getName());
					
 $data[] = Array(	"Eigenschaft"		=> "Gesch. Format: ",
					"Wert" 				=> $product_width_closed . "x" . $product_height_closed);
					
 $data[] = Array(	"Eigenschaft"		=> "Offenes Format: ",
					"Wert" 				=> $product_width . "x" . $product_height);
					
 $data[] = Array(	"Eigenschaft"		=> " ",
					"Wert" 				=> " ");
					
 $data[] = Array(	"Eigenschaft"		=> "Anschnitt: ",
					"Wert" 				=> printPrice($tmp_anschnitt) . 'mm');
 
 $data[] = Array(	"Eigenschaft"		=> "Schnitte: ",
					"Wert" 				=> $schnitte);
					
 $data[] = Array(	"Eigenschaft"		=> " ",
					"Wert" 				=> " ");
 
 if($part == Calculation::PAPER_CONTENT) {
     $sheets = ceil($calc->getPagesContent() / $calc->getProductsPerPaper(Calculation::PAPER_CONTENT) * $calc->getAmount());
     $sheets += $calc->getPaperContentGrant();

     $format_in_raw = $calc->getFormat_in_content();
     $format_in = explode("x", $calc->getFormat_in_content());
     $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperContentHeight() * $calc->getPaperContentWidth()));
     $roh2 = ceil($sheets / $roh);
 } else if ($part == Calculation::PAPER_ADDCONTENT) {
     $sheets = ceil($calc->getPagesAddContent() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT) * $calc->getAmount());
     $sheets += $calc->getPaperAddContentGrant();

     $format_in_raw = $calc->getFormat_in_addcontent();
     $format_in = explode("x", $calc->getFormat_in_addcontent());
     $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperAddContentHeight() * $calc->getPaperAddContentWidth()));
     $roh2 = ceil($sheets / $roh);
 } else if ($part == Calculation::PAPER_ADDCONTENT2) {
     $sheets = ceil($calc->getPagesAddContent2() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT2) * $calc->getAmount());
     $sheets += $calc->getPaperAddContent2Grant();

     $format_in_raw = $calc->getFormat_in_addcontent2();
     $format_in = explode("x", $calc->getFormat_in_addcontent2());
     $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperAddContent2Height() * $calc->getPaperAddContent2Width()));
     $roh2 = ceil($sheets / $roh);
 } else if ($part == Calculation::PAPER_ADDCONTENT3) {
     $sheets = ceil($calc->getPagesAddContent3() / $calc->getProductsPerPaper(Calculation::PAPER_ADDCONTENT3) * $calc->getAmount());
     $sheets += $calc->getPaperAddContent3Grant();

     $format_in_raw = $calc->getFormat_in_addcontent3();
     $format_in = explode("x", $calc->getFormat_in_addcontent3());
     $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperAddContent3Height() * $calc->getPaperAddContent3Width()));
     $roh2 = ceil($sheets / $roh);
 } else if ($part == Calculation::PAPER_ENVELOPE) {
     $sheets = ceil($calc->getPagesEnvelope() / $calc->getProductsPerPaper(Calculation::PAPER_ENVELOPE) * $calc->getAmount());
     $sheets += $calc->getPaperEnvelopeGrant();

     $format_in_raw = $calc->getFormat_in_envelope();
     $format_in = explode("x", $calc->getFormat_in_envelope());
     $roh = floor(($format_in[0] * $format_in[1]) / ($calc->getPaperEnvelopeHeight() * $calc->getPaperEnvelopeWidth()));
     $roh2 = ceil($sheets / $roh);
 }
					
 $data[] = Array(	"Eigenschaft"		=> "Anz. Bogen: ",
					"Wert" 				=> printPrice($sheets));
 
 $data[] = Array(	"Eigenschaft"		=> "Format Roh-Bogen: ",
					"Wert" 				=> $format_in_raw);
 
 $data[] = Array(	"Eigenschaft"		=> "Anz. Roh-Bogen: ",
					"Wert" 				=> printPrice($roh2));

$pdf->ezNewPage();
					
 $pdf->ezTable($data,$type,$dummy,$attr);



// Senden der Datei an den Browser
ob_clean();
$pdf->ezStream();
//imagejpeg($im2);
//imagedestroy($im2);
// echo "</br>" . $product_count;
?>
