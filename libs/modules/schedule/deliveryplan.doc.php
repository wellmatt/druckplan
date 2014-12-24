<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       26.04.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
chdir('../../../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/schedule/schedule.class.php';
require_once 'libs/modules/schedule/schedule.part.class.php';
require_once 'libs/modules/schedule/schedule.machine.class.php';
// require_once 'thirdparty/ezpdf/class.ezpdf.php';
require_once 'thirdparty/ezpdf/new/src/Cezpdf.php';

if(file_exists('libs/modules/calculation/order.class.php'))
    require_once 'libs/modules/calculation/order.class.php';

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

// Init pdffile
$pdf = new Cezpdf("A4", "landscape");
$pdf->selectFont("thirdparty/ezpdf/fonts/Helvetica.afm");
$pdf->ezSetMargins(20, 20, 20, 20);


$scheds = Schedule::getAllSchedules(Schedule::ORDER_DELIVERY_DATE, Schedule::STATUS_ORDER_OPEN);

foreach($scheds as $s)
{
    if($s->getStatus() == 1)
    {
        if($_REQUEST["day"] == "all")
            $struct[$s->getDeliveryDate()][$s->getDeliveryterms()->getName1()][] = $s;
        else
            if($s->getDeliveryDate() == $_REQUEST["day"])
                $struct[$s->getDeliveryDate()][$s->getDeliveryterms()->getName1()][] = $s;
    }
}
//----------------------------------------------------------------------------------
foreach(array_keys($struct) AS $day)
{

    $pdf->ezText("<b>".$_LANG->get('Auslieferungsplan')." ".date('d.m.Y', $day)."</b>",16, Array("justification" => "center"));

    $colsarr = Array( "ID"           => Array("width" => "20"),
            "Ersteller"    => Array("width" => "80"),
            "Auftr.-Nr."   => Array("width" => "80"),
            "Kunde"        => Array("width" => "120"),
            "Objekt"       => Array("width" => "110"),
            "Auflage"      => Array("width" => "45"),
            "Farben"       => Array("width" => "125"),
            "Adresse"   => Array("width" => "90"),
            "Bemerkungen"  => Array("width" => "130"));
    
    foreach(array_keys($struct[$day]) AS $location)
    {
        unset($data);
        $data = Array();
        $attr = Array("showHeadings" => 1, "shaded" => 1, "shadeCol" => Array(0.95,0.95,0.95),
                "width" => "790", "xpos" => "left", "showLines" => 1,
                "rowGap" => 2, "colGap" => 4, "cols" => $colsarr);

        $pdf->ezText("", 10);
        $pdf->ezText("<b>".$location."</b>", 10);
        $pdf->ezText("", 10);

        $x = 0;
        foreach($struct[$day][$location] as $s)
        {
            $data[$x]["ID"] = $s->getId();
            $data[$x]["Ersteller"] = $s->getCreateuser();
            $data[$x]["Auftr.-Nr."] = $s->getNumber();
            $data[$x]["Kunde"] = $s->getCustomer()->getNameAsLine();
            $data[$x]["Objekt"] = $s->getObject();
            $data[$x]["Auflage"] = $s->getAmount();
            $data[$x]["Farben"] = $s->getColors();
            $data[$x]["Adresse"] = $s->getDeliveryLocation();
            $data[$x]["Bemerkungen"] = $s->getNotes();
            $x++;
        }
        
        $pdf->ezTable($data,$type,$dummy,$attr);
        $pdf->ezNewPage();

    }

}

header("Content-Type: application/pdf");
header("Content-disposition: attachment; filename=\"deliveryplan.pdf\"");
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');

ob_clean();
flush();

$pdf->ezStream();
?>