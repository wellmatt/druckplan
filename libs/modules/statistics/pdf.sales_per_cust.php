<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       16.08.2012
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
require_once 'libs/modules/documents/document.class.php';
require_once 'libs/modules/schedule/schedule.machine.class.php';
require_once 'thirdparty/ezpdf/class.ezpdf.php';

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

$customers = BusinessContact::getAllBusinessContacts(BusinessContact::ORDER_NAME,BusinessContact::FILTER_CUST_IST);	

$_REQUEST["selyear"] = (int)$_REQUEST["selyear"];
if ($_REQUEST["selyear"]> 0)
{
/*****************************************************************
 * Jahr wurde ausgewählt
 */
$total = array();
$sum = array();
foreach ($customers as $customer)
{
   
   for ($m = 1; $m < 13; $m++) {
   
      $sales1 = Document::salesPerCustMonthMod1($customer->getId(), $_REQUEST["selyear"], $m);
      $sales2 = Document::salesPerCustMonthMod2($customer->getId(), $_REQUEST["selyear"], $m);
      
       $total_month1[$m] += number_format($sales1[0][0]['total'], 2, '.', '');
       $total_month2[$m] += number_format($sales2[0][0]['total'], 2, '.', '');
       $sum[$m] = printPrice($total_month1[$m] + $total_month2[$m]);
       $total[$m][$customer->getId()] = printPrice($sales1[0][0]['total'] + $sales2[0][0]['total']);
   }
}

$pdf = new Cezpdf("A4", "landscape");
$pdf->selectFont("thirdparty/ezpdf/fonts/Helvetica.afm");
$pdf->ezSetMargins(20, 20, 20, 20);

$pdf->setColor(0.0,0.0,0.0);
$pdf->ezText($_LANG->get('Statistiken'),20);
$pdf->ezSetDy(-10);
$pdf->ezText($_LANG->get('Auftr&auml;ge pro Kunde in EUR')." ".$_REQUEST["selyear"],15);
$pdf->ezSetDy(-30);


$colsarr = Array( "Name"           => Array("width" => "100"),
            "Januar"    => Array("justification"=>"right","width" => "60"),
            "Februar"   => Array("justification"=>"right","width" => "60"),
            "M&auml;rz"        => Array("justification"=>"right","width" => "60"),
            "April"       => Array("justification"=>"right","width" => "60"),
            "Mai"      => Array("justification"=>"right","width" => "60"),
            "Juni"       => Array("justification"=>"right","width" => "60"),
            "Juli"   => Array("justification"=>"right","width" => "60"),
			"August"   => Array("justification"=>"right","width" => "60"),
			"September"   => Array("justification"=>"right","width" => "60"),
			"Oktober"   => Array("justification"=>"right","width" => "60"),
			"November"   => Array("justification"=>"right","width" => "60"),
            "Dezember"  => Array("justification"=>"right","width" => "60"));

$attr = Array("showHeadings" => 0, "shaded" => 1, "width" => "790", "xpos" => "left", "showLines" => 1,
                "rowGap" => 2, "colGap" => 4, "cols" => $colsarr);

$data = array(
		array('Name' => '<b>'.$_LANG->get('Name').'</b>',
				'Januar' => '<b>'.$_LANG->get('Januar').'</b>',
				'Februar' => '<b>'.$_LANG->get('Februar').'</b>',
				'M&auml;rz' => '<b>'.$_LANG->get('M&auml;rz').'</b>',
				'April' => '<b>'.$_LANG->get('April').'</b>',
				'Mai' => '<b>'.$_LANG->get('Mai').'</b>',
				'Juni' => '<b>'.$_LANG->get('Juni').'</b>',
				'Juli' => '<b>'.$_LANG->get('Juli').'</b>',
				'August' => '<b>'.$_LANG->get('August').'</b>',
				'September' => '<b>'.$_LANG->get('September').'</b>',
				'Oktober' => '<b>'.$_LANG->get('Oktober').'</b>',
				'November' => '<b>'.$_LANG->get('November').'</b>',
				'Dezember' => '<b>'.$_LANG->get('Dezember').'</b>'));


$pdf->ezTable($data,'','',$attr);
unset($data);

$attr = Array("showHeadings" => 0, "shaded" => 1, "shadeCol" => Array(0.95,0.95,0.95),
                "width" => "790", "xpos" => "left", "showLines" => 1,
                "rowGap" => 2, "colGap" => 4, "cols" => $colsarr);


$x = 0;
        foreach ($customers as $customer)
        {
            if ($customer->getName1() != "") $cust = $customer->getName1();
   		    else $cust = $customer->getName2();
            $data[$x]["Name"] = $cust;
            $data[$x]["Januar"] = $total[1][$customer->getId()];
            $data[$x]["Februar"] = $total[2][$customer->getId()];
            $data[$x]["M&auml;rz"] = $total[3][$customer->getId()];
            $data[$x]["April"] = $total[4][$customer->getId()];
            $data[$x]["Mai"] = $total[5][$customer->getId()];
            $data[$x]["Juni"] = $total[6][$customer->getId()];
            $data[$x]["Juli"] = $total[7][$customer->getId()];
            $data[$x]["August"] = $total[8][$customer->getId()];
            $data[$x]["September"] = $total[9][$customer->getId()];
            $data[$x]["Oktober"] = $total[10][$customer->getId()];
            $data[$x]["November"] = $total[11][$customer->getId()];
            $data[$x]["Dezember"] = $total[12][$customer->getId()];
            $x++;
        }

$pdf->ezTable($data,'','',$attr);
unset($data);


$attr = Array("showHeadings" => 0, "shaded" => 1, "width" => "790", "xpos" => "left", "showLines" => 1,
                "rowGap" => 2, "colGap" => 4, "cols" => $colsarr);

$data = array(
         array('Name' => '<b>Summe</b>', 'Januar' => '<b>'.$sum[1].'</b>', 'Februar' => '<b>'.$sum[2].'</b>', 'M&auml;rz' => '<b>'.$sum[3].'</b>',
            'April' => '<b>'.$sum[4].'</b>', 'Mai' => '<b>'.$sum[5].'</b>', 'Juni' => '<b>'.$sum[6].'</b>', 'Juli' => '<b>'.$sum[7].'</b>',
            'August' => '<b>'.$sum[8].'</b>','September' => '<b>'.$sum[9].'</b>','Oktober' => '<b>'.$sum[10].'</b>', 'November' => '<b>'.$sum[11].'</b>',
            'Dezember' => '<b>'.$sum[12].'</b>'));


$pdf->ezTable($data,'','',$attr);            

} else { 
//-----------------------------------------------------------------------
// Kein Jahr ausgewÃ¤hlt
//
$sum = array();
$total = array();
foreach ($customers as $customer)
{
   for ($y = date('Y') - 3; $y <= date('Y'); $y++) {
   
  $sales1 = Document::salesPerCustMod1($customer->getId(), $y);
  $sales2 = Document::salesPerCustMod2($customer->getId(), $y);
  $total_year1[$y] += number_format($sales1[0][0]['total'], 2, '.', '');
  $total_year2[$y] += number_format($sales2[0][0]['total'], 2, '.', '');
  $total[$y][$customer->getId()] = printPrice($sales1[0][0]['total']+$sales2[0][0]['total']);
  $sum[$y] = printPrice($total_year1[$y] + $total_year2[$y]);
    
   }
}

$y1 = date('Y') - 3;
$y2 = date('Y') - 2;
$y3 = date('Y') - 1;
$y4 = date('Y');
;
 

$pdf = new Cezpdf();
$pdf->selectFont("thirdparty/ezpdf/fonts/Helvetica.afm");
$pdf->ezSetMargins(20, 20, 20, 20);

$pdf->setColor(0.0,0.0,0.0);
$pdf->ezText($_LANG->get('Statistik'),20);
$pdf->ezSetDy(-10);
$pdf->ezText($_LANG->get('Auftr&auml;ge pro Kunde in EUR'),15);
$pdf->ezSetDy(-30);

$colsarr = Array( "Name" => Array("width" => "200"),
             $y1  => Array("justification"=>"right","width" => "70"),
             $y2  => Array("justification"=>"right","width" => "70"),
             $y3  => Array("justification"=>"right","width" => "70"),
             $y4  => Array("justification"=>"right","width" => "70"),
             $y5  => Array("justification"=>"right","width" => "70"));

$attr = Array("showHeadings" => 1, "shaded" => 1, "shadeCol" => Array(0.95,0.95,0.95),
                "width" => "790", "xpos" => "left", "showLines" => 1,
                "rowGap" => 2, "colGap" => 4, "cols" => $colsarr);


$x = 0;
        foreach ($customers as $customer)
        {
            if ($customer->getName1() != "") $cust = $customer->getName1();
   		    else $cust = $customer->getName2();
            $data[$x]["Name"] = $cust;
            $data[$x][$y1] = $total[$y1][$customer->getId()];
            $data[$x][$y2] = $total[$y2][$customer->getId()];
            $data[$x][$y3] = $total[$y3][$customer->getId()];
            $data[$x][$y4] = $total[$y4][$customer->getId()];
            $x++;
        }

$pdf->ezTable($data,'','',$attr);
unset($data);

$attr = Array("showHeadings" => 0, "shaded" => 1, "width" => "790", "xpos" => "left", "showLines" => 1,
                "rowGap" => 2, "colGap" => 4, "cols" => $colsarr);

$data = array(
         array('Name' => '<b>Summe</b>', $y1 => '<b>'.$sum[$y1].'</b>', $y2 => '<b>'.$sum[$y2].'</b>', $y3 => '<b>'.$sum[$y3].'</b>',
            $y4 => '<b>'.$sum[$y4].'</b>'));


$pdf->ezTable($data,'','',$attr);  

}



header("Content-Type: application/pdf");
header("Content-disposition: attachment; filename=\"pdf.projects_per_cust.pdf\"");
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: no-cache');

ob_clean();
flush();

$pdf->ezStream();
 
?>