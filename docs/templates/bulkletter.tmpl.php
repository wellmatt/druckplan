<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			28.10.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
require_once 'libs/modules/businesscontact/businesscontact.class.php';

switch ($this->getCustomerFilter()){
    case 0: $filter = BusinessContact::FILTER_CUST_SOLL;
    case 1: $filter = BusinessContact::FILTER_CUST_IST;
    case 2: $filter = BusinessContact::FILTER_CUST;
    case 3: $filter = BusinessContact::FILTER_SUPP;
    case 4: $filter = BusinessContact::FILTER_ALL;
}

$attrib_filter = $this->getCustomerAttrib();
$all_busicon = Array();

foreach ($attrib_filter as $atfil){
    $tmp_atfil = explode(",",$atfil);
    $tmp_busicon = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, $filter, $tmp_atfil[0], $tmp_atfil[1]);
    $all_busicon = array_merge($all_busicon,$tmp_busicon);
}

$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetFont($font, '', 11);

$font = "helvetica";
$pdf->SetMargins(30, 30, 15, TRUE);
$pdf->AddPage();

foreach ($all_busicon AS $busicon){
    
    if($version == self::DOCTYPE_EMAIL)
        $pdf->Image($img_path, '', 0, '', '', '', '', 'R');
    
    $pdf->Ln(8);
    $pdf->Ln(8);
    $pdf->Ln(8);
    $pdf->Ln(8);
    
    $pdf->Cell(0, 0,$busicon->getNameAsLine(), 0, 1);
    $pdf->MultiCell(0, 0, $busicon->getAddressAsLine(), 0,'L');
    
    $pdf->Ln(8);
    $pdf->Ln(8);
    $pdf->SetMargins(15, 30, 15, TRUE);
    $pdf->Ln(8);
	
	// $this ist bezogen auf den Serienbrief (libs/modules/bulkLetter/bulkletter.class.php)
	$pdf->MultiCell(0, 0, $this->getText(), 0,'L');
	
	if (count($all_busicon) > 1) {
	    $pdf->SetMargins(30, 30, 15, TRUE);
	   	$pdf->AddPage();
	}
}

?>