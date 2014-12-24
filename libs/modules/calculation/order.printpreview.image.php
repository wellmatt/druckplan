<?
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
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$calc_id = (int)$_REQUEST["calc_id"];
$part = (int)$_REQUEST["part"];
$calc = new Calculation($calc_id);
$mach = Machineentry::getMachineForPapertype($part, $calc_id);
$mach = $mach[0]->getMachine();

// Basisdaten auslesen
if($part == Calculation::PAPER_CONTENT) {
    $paper = $calc->getPaperContent();
    $paperH = $calc->getPaperContentHeight();
    $paperW = $calc->getPaperContentWidth();
} else if ($part == Calculation::PAPER_ADDCONTENT) {
    $paper = $calc->getPaperAddContent();
    $paperH = $calc->getPaperAddContentHeight();
    $paperW = $calc->getPaperAddContentWidth();
} else if ($part == Calculation::PAPER_ENVELOPE) {
    $paper = $calc->getPaperEnvelope();
    $paperH = $calc->getPaperEnvelopeHeight();
    $paperW = $calc->getPaperEnvelopeWidth();
} else if ($part == Calculation::PAPER_ADDCONTENT2) {
    $paper = $calc->getPaperAddContent2();
    $paperH = $calc->getPaperAddContent2Height();
    $paperW = $calc->getPaperAddContent2Width();
} else if ($part == Calculation::PAPER_ADDCONTENT3) {
    $paper = $calc->getPaperAddContent3();
    $paperH = $calc->getPaperAddContent3Height();
    $paperW = $calc->getPaperAddContent3Width();
} else
    die('Wrong part');

if($part != Calculation::PAPER_ENVELOPE)
{
    $width = $calc->getProductFormatWidthOpen();
    $height = $calc->getProductFormatHeightOpen();
} else {
    $width = $calc->getEnvelopeWidthOpen();
    $height = $calc->getEnvelopeHeightOpen();
}
$width_closed     = $calc->getProductFormatWidth();
$height_closed     = $calc->getProductFormatHeight();

// Bild erzeugen
header ("Content-type: image/jpeg");
$im = imagecreatetruecolor($paperW, $paperH);

// Farben setzen
$bgcolor       = ImageColorAllocate($im, 255, 255, 255);
$bordercolor   = ImageColorAllocate($im, 111, 0, 0);
$prdcolor      = ImageColorAllocate($im, 244, 244, 244);
$prdbcolor_closed     = ImageColorAllocate($im, 160, 160, 160);
$prdbcolor     = ImageColorAllocate($im, 0, 0, 160);
$arrcolor      = ImageColorAllocate($im, 0, 0, 0);
$farbcolor     = ImageColorAllocate($im, 0, 200, 0);

// Hintergund
ImageFilledRectangle($im, 0, 0, $paperW, $paperH, $bordercolor);
ImageFilledRectangle($im, $mach->getBorder_left(), $mach->getBorder_top(), 
        ($paperW - $mach->getBorder_right()), 
        ($paperH - $mach->getBorder_bottom()), $bgcolor);

// Inhalt
if ($width_closed < $width && $width_closed != 0 )
    $multiRows = floor(ceil($width * 1.01) / $width_closed);
else
    $multiRows = 1;
if ($height_closed < $height && $height_closed != 0 )
    $multiCols = floor(ceil($height * 1.01) / $height_closed);
else
    $multiCols = 1;

$product_width       = $width;
$product_height      = $height;
$product_width_closed       = $width_closed;
$product_height_closed      = $height_closed;
$usesize_width       = $product_width + $_CONFIG->anschnitt * 2;
$usesize_height      = $product_height + $_CONFIG->anschnitt * 2;
$product_per_line    = floor(($paperW - $mach->getBorder_left() - $mach->getBorder_right()) / $usesize_width);
$product_rows        = floor(($paperH - $mach->getBorder_top() - $mach->getBorder_bottom() - $_CONFIG->farbRandBreite) / $usesize_height);
$product_per_line_closed    = floor(($paperW - $mach->getBorder_left() - $mach->getBorder_right()) / $usesize_width) * $multiRows;
$product_rows_closed        = floor(($paperH - $mach->getBorder_top() - $mach->getBorder_bottom() - $_CONFIG->farbRandBreite) / $usesize_height) * $multiCols;
$product_per_paper   = $product_per_line * $product_rows;


$product_width2      = $height;
$product_height2     = $width;
$product_width2_closed      = $height_closed;
$product_height2_closed     = $width_closed;
$usesize_width2      = $product_width2 + $_CONFIG->anschnitt * 2;
$usesize_height2     = $product_height2 + $_CONFIG->anschnitt * 2;
$product_per_line2   = floor(($paperW - $mach->getBorder_left() - $mach->getBorder_right()) / $usesize_width2);
$product_rows2       = floor(($paperH - $mach->getBorder_top() - $mach->getBorder_bottom() - $_CONFIG->farbRandBreite) / $usesize_height2);
$product_per_line2_closed   = floor(($paperW - $mach->getBorder_left() - $mach->getBorder_right()) / $usesize_width2) * $multiCols;
$product_rows2_closed       = floor(($paperH - $mach->getBorder_top() - $mach->getBorder_bottom() - $_CONFIG->farbRandBreite) / $usesize_height2) * $multiRows;
$product_per_paper2  = $product_per_line2 * $product_rows2;

if($product_per_paper2 >= $product_per_paper)
{
    $flipped = true;
    $product_rows     = $product_rows2;
    $product_per_line = $product_per_line2;
    $product_rows_closed     = $product_rows2_closed;
    $product_per_line_closed = $product_per_line2_closed;
    
    $product_width    = $product_width2;
    $product_height   = $product_height2;
    $product_width_closed    = $product_width2_closed;
    $product_height_closed   = $product_height2_closed;
    
    $t = $multiCols;
    $multiCols = $multiRows;
    $multiRows = $t;
}   

// geschlossenes Format
$posY = $mach->getBorder_top();

$countY = 1;
for($x = 0; $x < $product_rows_closed; $x++)
{
    $posX = $mach->getBorder_left();
    $countX = 1;
    
    for($y = 0; $y < $product_per_line_closed; $y++)
    {
        ImageFilledRectangle($im, $posX, $posY, $posX + $product_width_closed, $posY + $product_height_closed, $prdcolor);
        ImageRectangle($im, $posX, $posY, $posX + $product_width_closed, $posY + $product_height_closed, $prdbcolor_closed);

        $posX += $product_width_closed;
        if($countX % $multiRows == 0)
            $posX += $_CONFIG->anschnitt * 2;
        $countX++;
    }

    $posY += $product_height_closed;
    if($countY % $multiCols == 0)
        $posY += $_CONFIG->anschnitt * 2;
    $countY++;
}

// offenes Format zeichnen
$posY = $mach->getBorder_top();
for($x = 0; $x < $product_rows; $x++)
{
    $posX = $mach->getBorder_left();

    for($y = 0; $y < $product_per_line; $y++)
    {
        //         ImageFilledRectangle($im, $posX, $posY, $posX + $product_width, $posY + $product_height, $prdcolor);
        ImageRectangle($im, $posX, $posY, $posX + $product_width, $posY + $product_height, $prdbcolor);

        $posX += $product_width + $_CONFIG->anschnitt * 2;
    }

    $posY += $product_height + $_CONFIG->anschnitt * 2;
}

// Farb-Kontrollstreifen darstellen
if($_CONFIG->farbRandBreite > 0)
{
    $farbStreifenBreite = ($paperW - $mach->getBorder_left() - $mach->getBorder_right()) / 4;
    imagefilledrectangle($im, $mach->getBorder_left(), $paperH - $mach->getBorder_bottom() - $_CONFIG->farbRandBreite, 
                        $mach->getBorder_left() + $farbStreifenBreite, $paperH - $mach->getBorder_bottom(), ImageColorAllocate($im, 0, 255, 255));    
    imagefilledrectangle($im, $mach->getBorder_left() + $farbStreifenBreite, $paperH - $mach->getBorder_bottom() - $_CONFIG->farbRandBreite,
                        $mach->getBorder_left() + 2 * $farbStreifenBreite, $paperH - $mach->getBorder_bottom(), ImageColorAllocate($im, 255, 0, 255));
    imagefilledrectangle($im, $mach->getBorder_left() + 2 * $farbStreifenBreite, $paperH - $mach->getBorder_bottom() - $_CONFIG->farbRandBreite,
                        $mach->getBorder_left() + 3 * $farbStreifenBreite, $paperH - $mach->getBorder_bottom(), ImageColorAllocate($im, 255, 255, 0));
    imagefilledrectangle($im, $mach->getBorder_left() + 3 * $farbStreifenBreite, $paperH - $mach->getBorder_bottom() - $_CONFIG->farbRandBreite,
                        $mach->getBorder_left() + 4 * $farbStreifenBreite, $paperH - $mach->getBorder_bottom(), ImageColorAllocate($im, 0, 0, 0));
}
// Auf Groesse anpassen und ausgeben
$factor = $paperW / $paperH;
$im2 = imagecreatetruecolor(250, (250/$factor));
imagecopyresampled($im2, $im, 0, 0, 0, 0, 250, (250 / $factor), imagesx($im), imagesy($im));
imagedestroy($im);

// Laufrichtung einzeichnen
$direction = $paper->getPaperDirection($calc, $part);
if($direction == Paper::PAPER_DIRECTION_SMALL)
    imagettftext($im2, 8, 0, 30, 20, $arrcolor, "./fonts/arial.ttf", "".$_LANG->get('Laufrichtung').":\n".$_LANG->get('schmale Bahn')."");
else
    imagettftext($im2, 8, 0, 30, 20, $arrcolor, "./fonts/arial.ttf", "".$_LANG->get('Laufrichtung').":\n".$_LANG->get('breite Bahn')."");
imagejpeg($im2);
imagedestroy($im2);
?>
