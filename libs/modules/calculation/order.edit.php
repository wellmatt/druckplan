<? // ------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       19.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/perferences/perferences.class.php';
// $customers = BusinessContact::getAllBusinessContacts(BusinessContact::ORDER_NAME, BusinessContact::FILTER_CUST_IST);

$perference = new Perferences();

// echo "<pre>";
// print_r($_REQUEST);
// echo "</pre>";

$products = Product::getAllProducts();
$products_nonindi = Product::getAllProductsByIndividuality();
$individualProducts = Product::getAllProductsByIndividuality(true);


$order = new Order($_REQUEST["id"]);

if($_REQUEST["clearorder"] == 1){
	$calcs = Calculation::getAllCalculations($order);
	foreach($calcs as $c)
		$c->delete();
}

// delete Calculation
if($_REQUEST["subexec"] == "delete"){
	$calc = new Calculation((int)$_REQUEST["calc_id"]);
	$savemsg = getSaveMessage($calc->delete());

}

// clone order
if($_REQUEST["subexec"] == "clone"){
    $order_id_old = $order->getId();
    $old_product = $order->getProduct();
    $calculations_old = Calculation::getAllCalculations($order, Calculation::ORDER_AMOUNT);
    $order->clearId();
    $order->setProduct($old_product);
    $order->save();
    $order_id_new = $order->getId();
    
    foreach ($calculations_old as $new_calc){
        $positions_old = CalculationPosition::getAllCalculationPositions($new_calc->getId());
        $machineentrys_old = Machineentry::getAllMachineentries($new_calc->getId());
        
        $new_calc->setOrderId($order_id_new);
        $new_calc->clearId();
        $new_calc->save();
        
        $position_array = Array();
        foreach ($positions_old as $new_position){
            $new_position->setCalculationid($new_calc->getId());
            $position_array[] = $new_position;
        }
        CalculationPosition::saveMultipleCalculationpositions($position_array);
        
        foreach ($machineentrys_old as $me_old){
            $me_old->clearId();
            $me_old->setCalcId($new_calc->getId());
            $me_old->save();
        }
    }
    
//     die;
    
    echo "<script type=\"text/javascript\">location.href='index.php?page=".$_REQUEST['page']."&id=".$order_id_new."&exec=edit&step=4';</script>";
}

// create new Order
if((int)$_REQUEST["createNew"] == 1){
	$order->setTitle(trim(addslashes($_REQUEST["order_title"])));
	$order->setCustomer(new BusinessContact((int)$_REQUEST["order_customer"]));	
	$order->setCustContactperson($order->getCustomer()->getMainContactperson());
	$order->setPaymentTerms($order->getCustomer()->getPaymentTerms());
	$order->save();
	echo "<script type=\"text/javascript\">location.href='index.php?page=".$_REQUEST['page']."&id=".$order->getId()."&exec=edit&step=1';</script>";
}

// Set product
if((int)$_REQUEST["selProduct"] != 0){
	$order->setProduct(new Product((int)$_REQUEST["selProduct"]));
	$order->setTextOffer($order->getProduct()->getTextOffer());
	$order->setTextOfferconfirm($order->getProduct()->getTextOfferconfirm());
	$order->setTextInvoice($order->getProduct()->getTextInvoice());
	$order->save();
}

// save Step2
if((int)$_REQUEST["step"] == 2){
	$calc = new Calculation((int)$_REQUEST["calc_id"]);
	
	if ($order->getProduct()->getLoadDymmyData() == 1){
		// Kalkulation mit Dummy-Daten fuellen
		
		$calc->setPagesContent(1);
		
		$tmp_formats = $order->getProduct()->getAvailablePaperFormats();
		$calc->setProductFormat($tmp_formats[0]);
		$calc->setProductFormatHeight($tmp_formats[0]->getHeight());
		$calc->setProductFormatWidth($tmp_formats[0]->getWidth());
		$calc->setProductFormatHeightOpen($tmp_formats[0]->getHeight());
		$calc->setProductFormatWidthOpen($tmp_formats[0]->getWidth());
		
		$tmp_papers = $order->getProduct()->getSelectedPapersIds(Calculation::PAPER_CONTENT);
		$tmp_paper = array_shift(array_slice($tmp_papers, 0, 1)) ;
		$tmp_paper = new Paper($tmp_paper["id"]);
		$calc->setPaperContent($tmp_paper);
		$tmp_weights = $tmp_paper->getWeights();
		$calc->setPaperContentWeight($tmp_weights[0]);
		
		$tmp_chromas = Chromaticity::getAllChromaticities();
		$calc->setChromaticitiesContent($tmp_chromas[0]);
		
		$calc->setAmount(1);
		$calc->setOrderId($order->getId());
		$calc->setState(1);
		
		$calc->save();
		
		$order->setShowProduct(0);
		$order->save();
		
	}

	if($_REQUEST["subexec"] == "copy"){
		$calc->clearId();
	}

	if($_REQUEST["subexec"] == "save")
	{
		$calc->setOrderId($order->getId());
		if ((int)$_REQUEST["order_sorts"] <= 0)
		    $tmp_sorts = 1;
		else 
		    $tmp_sorts = (int)$_REQUEST["order_sorts"];
		$calc->setSorts($tmp_sorts);
		$tmp_amount = (int)$_REQUEST["order_amount"] * $calc->getSorts();
		$calc->setAmount($tmp_amount);
		$calc->setPagesContent((int)$_REQUEST["h_content_pages"]);
		$calc->setPagesAddContent((int)$_REQUEST["h_addcontent_pages"]);
		$calc->setPagesEnvelope((int)$_REQUEST["h_envelope_pages"]);
		$calc->setPagesAddContent2((int)$_REQUEST["h_addcontent2_pages"]);
		$calc->setPagesAddContent3((int)$_REQUEST["h_addcontent3_pages"]);
		
		$calc->setProductFormat(new Paperformat((int)$_REQUEST["h_product_format"]));

		$calc->setProductFormatHeight((int)$_REQUEST["order_product_height"]);
		$calc->setProductFormatWidth((int)$_REQUEST["order_product_width"]);
		$calc->setProductFormatHeightOpen((int)$_REQUEST["order_product_height_open"]);
		$calc->setProductFormatWidthOpen((int)$_REQUEST["order_product_width_open"]);
		 
		$calc->setPaperContent(new Paper((int)$_REQUEST["h_content_paper"]));
		$calc->setPaperContentWeight((int)$_REQUEST["h_content_paper_weight"]);
		$calc->setChromaticitiesContent(new Chromaticity((int)$_REQUEST["h_content_chromaticity"]));

		$calc->setPaperAddContent(new Paper((int)$_REQUEST["h_addcontent_paper"]));
		$calc->setPaperAddContentWeight((int)$_REQUEST["h_addcontent_paper_weight"]);
		$calc->setChromaticitiesAddContent(new Chromaticity((int)$_REQUEST["h_addcontent_chromaticity"]));

		$calc->setPaperEnvelope(new Paper((int)$_REQUEST["h_envelope_paper"]));
		$calc->setPaperEnvelopeWeight((int)$_REQUEST["h_envelope_paper_weight"]);
		$calc->setChromaticitiesEnvelope(new Chromaticity((int)$_REQUEST["h_envelope_chromaticity"]));
		
		$calc->setPaperAddContent2(new Paper((int)$_REQUEST["h_addcontent2_paper"]));
		$calc->setPaperAddContent2Weight((int)$_REQUEST["h_addcontent2_paper_weight"]);
		$calc->setChromaticitiesAddContent2(new Chromaticity((int)$_REQUEST["h_addcontent2_chromaticity"]));
		
		$calc->setPaperAddContent3(new Paper((int)$_REQUEST["h_addcontent3_paper"]));
		$calc->setPaperAddContent3Weight((int)$_REQUEST["h_addcontent3_paper_weight"]);
		$calc->setChromaticitiesAddContent3(new Chromaticity((int)$_REQUEST["h_addcontent3_chromaticity"]));

		$calc->setEnvelopeWidthOpen((int)$_REQUEST["order_envelope_width_open"]);
		$calc->setEnvelopeHeightOpen((int)$_REQUEST["order_envelope_height_open"]);

		$calc->setCalcAutoValues(1);
		if((int)$_REQUEST["calc_id"] == 0)
			$calc->setTextProcessing($order->getProduct()->getTextProcessing());
		// Marge des Mandanten einsetzen
		$calc->setMargin($_USER->getClient()->getMargin());
		$calc->setDiscount($order->getCustomer()->getDiscount());

		if((int)$_REQUEST["h_folding"] > 0)
			$calc->setFolding(new Foldtype((int)$_REQUEST["h_folding"]));

		// Falzboegen speichern
		$schemes = $calc->getAvailableFoldschemes();
		if(is_array($schemes[1]))
		{
			$foldscheme_content = '';
			if($schemes[1][0][16])
				$foldscheme_content .= $schemes[1][0][16]."x16,";
			if($schemes[1][0][8])
				$foldscheme_content .= $schemes[1][0][8]."x8,";
			if($schemes[1][0][4])
				$foldscheme_content .= $schemes[1][0][4]."x4,";
			$foldscheme_content = substr($foldscheme_content, 0, -1);
		}

		if(is_array($schemes[2]))
		{
			$foldscheme_addcontent = '';
			if($schemes[2][0][16])
				$foldscheme_addcontent .= $schemes[2][0][16]."x16,";
			if($schemes[2][0][8])
				$foldscheme_addcontent .= $schemes[2][0][8]."x8,";
			if($schemes[2][0][4])
				$foldscheme_addcontent .= $schemes[2][0][4]."x4,";
			$foldscheme_addcontent = substr($foldscheme_addcontent, 0, -1);
		}
		
		if(is_array($schemes[4]))
		{
			$foldscheme_addcontent2 = '';
			if($schemes[4][0][16])
				$foldscheme_addcontent2 .= $schemes[5][0][16]."x16,";
			if($schemes[4][0][8])
				$foldscheme_addcontent2 .= $schemes[5][0][8]."x8,";
			if($schemes[4][0][4])
				$foldscheme_addcontent2 .= $schemes[5][0][4]."x4,";
			$foldscheme_addcontent2 = substr($foldscheme_addcontent2, 0, -1);
		}
		
		if(is_array($schemes[5]))
		{
			$foldscheme_addcontent3 = '';
			if($schemes[5][0][16])
				$foldscheme_addcontent3 .= $schemes[5][0][16]."x16,";
			if($schemes[5][0][8])
				$foldscheme_addcontent3 .= $schemes[5][0][8]."x8,";
			if($schemes[5][0][4])
				$foldscheme_addcontent3 .= $schemes[5][0][4]."x4,";
			$foldscheme_addcontent3 = substr($foldscheme_addcontent3, 0, -1);
		}

		$calc->setFoldschemeContent($foldscheme_content);
		$calc->setFoldschemeAddContent($foldscheme_addcontent);
		$calc->setFoldschemeAddContent2($foldschemeAddContent2);
		$calc->setFoldschemeAddContent3($foldschemeAddContent3);
		if($calc->getPagesEnvelope())
			$calc->setFoldschemeEnvelope("1x".$calc->getPagesEnvelope());
		$calc->save();

		$savemsg = getSaveMessage($calc->save());

		// Create Machineentries if Calc is new with default Machines
		$machines = $order->getProduct()->getMachines();
		$ctps = Array();

		//Einmal alle Druckmaschinen loeschen, damit die Aenderungen richtig neu gesetzt werden
		Machineentry::deleteAllPrinterForCalc($calc->getId());
		foreach ($machines as $m)
		{
			if($order->getProduct()->isDefaultMachine($m, $calc->getAmount()) && ! Machineentry::entryExists($calc->getId(), $m->getId()))
			{
				if($m->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL || $m->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
				{
					if($calc->getPagesContent())
					{
						$color_possible= false; //Pruefen, ob die Standart-Maschine die Farbe kann
						foreach ($m->getChromaticities() as $chrome){
							if( $chrome == $calc->getChromaticitiesContent()){
								$color_possible = true;
							}
						}
						if($color_possible){// Standart Maschine kann ausgewaehlte Farbe drucken
							//$me = new Machineentry();
							if (! Machineentry::entryExistsWithPart($calc->getId(), $m->getId(), 1)){
								$me = new Machineentry();
								$me->setPart(Calculation::PAPER_CONTENT);
							} else {
								$me = new Machineentry(Machineentry::entryExistsWithPart($calc->getId(), $m->getId(), 1));
							}
							$me->setMachine($m);
							$me->setMachineGroup($m->getGroup()->getId());
							$me->setCalcId($calc->getId());
							//$me->setPart(Calculation::PAPER_CONTENT);
							$me->setTime(0);
							$me->save();
							$me->setTime($me->getMachine()->getRunningTime($me));
							$me->setPrice($me->getMachine()->getMachinePrice($me));
							// $me->setInfo($calc->getChromaticitiesContent()->getName());
							$me->save();
						} else { //Standart-Maschine kann ausgewaehlte Farbe nicht drucken, also eine andere setzen
							$alt_machines = Machine::getAlternativMachines($m, $calc->getChromaticitiesContent()->getId());
							if (count($alt_machines)){ //Falls es eine Maschine gibt, die die Farbe kann => setzen
								if (! Machineentry::entryExistsWithPart($calc->getId(), $alt_machines[0]->getId(), 1)){
									$me = new Machineentry();
									$me->setPart(Calculation::PAPER_CONTENT);
								} else {
									$me = new Machineentry(Machineentry::entryExistsWithPart($calc->getId(), $alt_machines[0]->getId(), 1));
								}
								$me->setMachine($alt_machines[0]);
								$me->setMachineGroup($m->getGroup()->getId());
								$me->setCalcId($calc->getId());
								$me->setTime(0);
								if ($me->getPart()==1){
									$me->setPart(Calculation::PAPER_CONTENT);
									$me->save();
								}
								$me->setTime($me->getMachine()->getRunningTime($me));
								$me->setPrice($me->getMachine()->getMachinePrice($me));
								// $me->setInfo($calc->getChromaticitiesContent()->getName());
								if ($me->getPart()==1){
									$me->save();
								}
							}
						}

						$sizes = $calc->getPaperContent()->getMaxPaperSizeForMachine($m);

						// Update Papersize
						$calc->setPaperContentHeight($sizes["height"]);
						$calc->setPaperContentWidth($sizes["width"]);
						$calc->save();

						// Update Paper Grant
						// Zuschussbogen multipliziert Anzahl Druckplatten
						// $calc->setPaperContentGrant($order->getProduct()->getGrantPaper() * $calc->getPlateSetCount($me));
						// Zuschussbogen prozentual zu der Papiermenge
						
// 						$calc->setPaperContentGrant($order->getProduct()->getGrantPaper() * $calc->getPaperCount(Calculation::PAPER_CONTENT) / 100); //Zuschuss
						
						$calc->setPaperContentGrant($perference->getZuschussProDP() * $calc->getPlateSetCount($me));
						
						$calc->save();
					}
					if($calc->getPagesAddContent())
					{
						$color_possible= false; //Pruefen, ob die Standart-Maschine die Farbe kann
						foreach ($m->getChromaticities() as $chrome){
							if( $chrome == $calc->getChromaticitiesAddContent()){
								$color_possible = true;
							}
						}
						if($color_possible){//Standart Maschine kann ausgewaehlte Farbe drucken
							//$me = new Machineentry();
							if (! Machineentry::entryExistsWithPart($calc->getId(), $m->getId(), 2)){
								$me = new Machineentry();
								$me->setPart(Calculation::PAPER_ADDCONTENT);
							} else{
								$me = new Machineentry(Machineentry::entryExistsWithPart($calc->getId(), $m->getId(), 2));
							}
							$me->setMachine($m);
							$me->setMachineGroup($m->getGroup()->getId());
							$me->setCalcId($calc->getId());
							//$me->setPart(Calculation::PAPER_ADDCONTENT);
							$me->setTime(0);
							$me->save();
							$me->setTime($me->getMachine()->getRunningTime($me));
							$me->setPrice($me->getMachine()->getMachinePrice($me));
							// $me->setInfo($calc->getChromaticitiesAddContent()->getName());
							$me->save();
						} else { //Standart-Maschine kann ausgewaehlte Farbe nicht drucken, also eine andere setzen
							$alt_machines = Machine::getAlternativMachines($m, $calc->getChromaticitiesAddContent()->getId());
							if (count($alt_machines)){ //Falls es eine Maschine gibt, die die Farbe kann => setzen
								if (! Machineentry::entryExistsWithPart($calc->getId(), $alt_machines[0]->getId(), 2)){
									$me = new Machineentry();
									$me->setPart(Calculation::PAPER_ADDCONTENT);
								} else{
									$me = new Machineentry(Machineentry::entryExistsWithPart($calc->getId(), $alt_machines[0]->getId(), 2));
								}
								$me->setMachine($alt_machines[0]);
								$me->setMachineGroup($m->getGroup()->getId());
								$me->setCalcId($calc->getId());
								$me->setTime(0);
								if ($me->getPart()==2){
									$me->setPart(Calculation::PAPER_ADDCONTENT);
									$me->save();
								}
								$me->setTime($me->getMachine()->getRunningTime($me));
								$me->setPrice($me->getMachine()->getMachinePrice($me));
								// $me->setInfo($calc->getChromaticitiesAddContent()->getName());
								if($me->getPart()==2){
									$me->save();
								}
							}
						}

						$sizes = $calc->getPaperAddContent()->getMaxPaperSizeForMachine($m);

						// Update Papersize
						$calc->setPaperAddContentHeight($sizes["height"]);
						$calc->setPaperAddContentWidth($sizes["width"]);
						$calc->save();

						// Update Paper Grant
						// Zuschussbogen multipliziert Anzahl Druckplatten
						// $calc->setPaperAddContentGrant($order->getProduct()->getGrantPaper() * $calc->getPlateSetCount($me));
						// Zuschussbogen prozentual zu der Papiermenge
// 						$calc->setPaperAddContentGrant($order->getProduct()->getGrantPaper() * $calc->getPaperCount(Calculation::PAPER_ADDCONTENT) / 100 ); //Zuschuss
						
						$calc->setPaperAddContentGrant($perference->getZuschussProDP() * $calc->getPlateSetCount($me));
						
						$calc->save();
					}
					if($calc->getPagesEnvelope())
					{
						$color_possible= false; //Pr�fen, ob die Standart-Maschine die Farbe kann
						foreach ($m->getChromaticities() as $chrome){
							if( $chrome == $calc->getChromaticitiesEnvelope()){
								$color_possible = true;
							}
						}
						if($color_possible){//Standart Maschine kann ausgew�hlte Farbe drucken
							//$me = new Machineentry();
							if (! Machineentry::entryExistsWithPart($calc->getId(), $m->getId(), 3)){
								//Falls kein Eintrag vorhanden, neuen erstellen
								$me = new Machineentry();
								$me->setPart(Calculation::PAPER_ENVELOPE);
							} else{
								//sonst: existierenden bearbeiten
								$me = new Machineentry(Machineentry::entryExistsWithPart($calc->getId(), $m->getId(), 3));
							}
							$me->setMachine($m);
							$me->setMachineGroup($m->getGroup()->getId());
							$me->setCalcId($calc->getId());
							//$me->setPart(Calculation::PAPER_ENVELOPE);
							$me->setTime(0);
							$me->save();
							$me->setTime($me->getMachine()->getRunningTime($me));
							$me->setPrice($me->getMachine()->getMachinePrice($me));
							// $me->setInfo($calc->getChromaticitiesEnvelope()->getName());
							$me->save();
						} else { //Standart-Maschine kann ausgew�hlte Farbe nicht drucken, also eine andere setzen
							$alt_machines = Machine::getAlternativMachines($m, $calc->getChromaticitiesEnvelope()->getId());
							if (count($alt_machines)){ //Falls es eine Maschine gibt, die die Farbe kann => setzen
								if (! Machineentry::entryExistsWithPart($calc->getId(), $alt_machines[0]->getId(), 3)){
									//Falls kein Eintrag vorhanden, neuen erstellen
									$me = new Machineentry();
									$me->setPart(Calculation::PAPER_ENVELOPE);
								} else{
									//sonst: existierenden bearbeiten
									$me = new Machineentry(Machineentry::entryExistsWithPart($calc->getId(), $alt_machines[0]->getId(), 3));
								}
								$me->setMachine($alt_machines[0]);
								$me->setMachineGroup($m->getGroup()->getId());
								$me->setCalcId($calc->getId());
								$me->setTime(0);
								if($me->getPart()==3){
									$me->setPart(Calculation::PAPER_ENVELOPE);
									$me->save();
								}
								$me->setTime($me->getMachine()->getRunningTime($me));
								$me->setPrice($me->getMachine()->getMachinePrice($me));
								// $me->setInfo($calc->getChromaticitiesEnvelope()->getName());
								if($me->getPart()==3){
									$me->save();
								}
							}
						}

						$sizes = $calc->getPaperEnvelope()->getMaxPaperSizeForMachine($m);

						// Update Papersize
						$calc->setPaperEnvelopeHeight($sizes["height"]);
						$calc->setPaperEnvelopeWidth($sizes["width"]);
						$calc->save();

						// Update Paper Grant
						// Zuschussbogen multipliziert Anzahl Druckplatten
						//$calc->setPaperEnvelopeGrant($order->getProduct()->getGrantPaper() * $calc->getPlateSetCount($me));
						// Zuschussbogen prozentual zu der Papiermenge
// 						$calc->setPaperEnvelopeGrant($order->getProduct()->getGrantPaper() * $calc->getPaperCount(Calculation::PAPER_ENVELOPE) / 100); //Zuschuss
						
						$calc->setPaperEnvelopeGrant($perference->getZuschussProDP() * $calc->getPlateSetCount($me));

						$calc->save();
					}
					
					// fuer den 2. zus. Inhalt 
					if($calc->getPagesAddContent2())
					{
						$color_possible= false; //Pruefen, ob die Standart-Maschine die Farbe kann
						foreach ($m->getChromaticities() as $chrome){
							if( $chrome == $calc->getChromaticitiesAddContent2()){
								$color_possible = true;
							}
						}
						if($color_possible){//Standart Maschine kann ausgewaehlte Farbe drucken
							//$me = new Machineentry();
							if (! Machineentry::entryExistsWithPart($calc->getId(), $m->getId(), Calculation::PAPER_ADDCONTENT2)){
								$me = new Machineentry();
								$me->setPart(Calculation::PAPER_ADDCONTENT2);
							} else{
								$me = new Machineentry(Machineentry::entryExistsWithPart($calc->getId(), $m->getId(), Calculation::PAPER_ADDCONTENT2));
							}
							$me->setMachine($m);
							$me->setMachineGroup($m->getGroup()->getId());
							$me->setCalcId($calc->getId());
							//$me->setPart(Calculation::PAPER_ADDCONTENT2);
							$me->setTime(0);
							$me->save();
							$me->setTime($me->getMachine()->getRunningTime($me));
							$me->setPrice($me->getMachine()->getMachinePrice($me));
							// $me->setInfo($calc->getChromaticitiesAddContent2()->getName());
							$me->save();
						} else { //Standart-Maschine kann ausgewaehlte Farbe nicht drucken, also eine andere setzen
							$alt_machines = Machine::getAlternativMachines($m, $calc->getChromaticitiesAddContent()->getId());
							if (count($alt_machines)){ //Falls es eine Maschine gibt, die die Farbe kann => setzen
								if (! Machineentry::entryExistsWithPart($calc->getId(), $alt_machines[0]->getId(), Calculation::PAPER_ADDCONTENT2)){
									$me = new Machineentry();
									$me->setPart(Calculation::PAPER_ADDCONTENT2);
								} else{
									$me = new Machineentry(Machineentry::entryExistsWithPart($calc->getId(), $alt_machines[0]->getId(), Calculation::PAPER_ADDCONTENT2));
								}
								$me->setMachine($alt_machines[0]);
								$me->setMachineGroup($m->getGroup()->getId());
								$me->setCalcId($calc->getId());
								$me->setTime(0);
								if ($me->getPart() == Calculation::PAPER_ADDCONTENT2){
									$me->setPart(Calculation::PAPER_ADDCONTENT2);
									$me->save();
								}
								$me->setTime($me->getMachine()->getRunningTime($me));
								$me->setPrice($me->getMachine()->getMachinePrice($me));
								// $me->setInfo($calc->getChromaticitiesAddContent2()->getName());
								if($me->getPart() == Calculation::PAPER_ADDCONTENT2){
									$me->save();
								}
							}
						}
					
						$sizes = $calc->getPaperAddContent2()->getMaxPaperSizeForMachine($m);
					
						// Update Papersize
						$calc->setPaperAddContent2Height($sizes["height"]);
						$calc->setPaperAddContent2Width($sizes["width"]);
						$calc->save();
					
						// Update Paper Grant
						// Zuschussbogen multipliziert Anzahl Druckplatten
						// $calc->setPaperAddContentGrant($order->getProduct()->getGrantPaper() * $calc->getPlateSetCount($me));
						// Zuschussbogen prozentual zu der Papiermenge
// 						$calc->setPaperAddContent2Grant($order->getProduct()->getGrantPaper() * $calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) / 100 ); //Zuschuss
						
						$calc->setPaperAddContent2Grant($perference->getZuschussProDP() * $calc->getPlateSetCount($me));
					
						$calc->save();
					}
					
					// fuer den 3. zus. Inhalt
					if($calc->getPagesAddContent3())
					{
						$color_possible= false; //Pruefen, ob die Standart-Maschine die Farbe kann
						foreach ($m->getChromaticities() as $chrome){
							if( $chrome == $calc->getChromaticitiesAddContent3()){
								$color_possible = true;
							}
						}
						if($color_possible){//Standart Maschine kann ausgewaehlte Farbe drucken
							//$me = new Machineentry();
							if (! Machineentry::entryExistsWithPart($calc->getId(), $m->getId(), Calculation::PAPER_ADDCONTENT3)){
								$me = new Machineentry();
								$me->setPart(Calculation::PAPER_ADDCONTENT3);
							} else{
								$me = new Machineentry(Machineentry::entryExistsWithPart($calc->getId(), $m->getId(), Calculation::PAPER_ADDCONTENT3));
							}
							$me->setMachine($m);
							$me->setMachineGroup($m->getGroup()->getId());
							$me->setCalcId($calc->getId());
							//$me->setPart(Calculation::PAPER_ADDCONTENT3);
							$me->setTime(0);
							$me->save();
							$me->setTime($me->getMachine()->getRunningTime($me));
							$me->setPrice($me->getMachine()->getMachinePrice($me));
							// $me->setInfo($calc->getChromaticitiesAddContent3()->getName());
							$me->save();
						} else { //Standart-Maschine kann ausgewaehlte Farbe nicht drucken, also eine andere setzen
							$alt_machines = Machine::getAlternativMachines($m, $calc->getChromaticitiesAddContent()->getId());
							if (count($alt_machines)){ //Falls es eine Maschine gibt, die die Farbe kann => setzen
								if (! Machineentry::entryExistsWithPart($calc->getId(), $alt_machines[0]->getId(), Calculation::PAPER_ADDCONTENT3)){
									$me = new Machineentry();
									$me->setPart(Calculation::PAPER_ADDCONTENT3);
								} else{
									$me = new Machineentry(Machineentry::entryExistsWithPart($calc->getId(), $alt_machines[0]->getId(), Calculation::PAPER_ADDCONTENT3));
								}
								$me->setMachine($alt_machines[0]);
								$me->setMachineGroup($m->getGroup()->getId());
								$me->setCalcId($calc->getId());
								$me->setTime(0);
								if ($me->getPart() == Calculation::PAPER_ADDCONTENT3){
									$me->setPart(Calculation::PAPER_ADDCONTENT3);
									$me->save();
								}
								$me->setTime($me->getMachine()->getRunningTime($me));
								$me->setPrice($me->getMachine()->getMachinePrice($me));
								// $me->setInfo($calc->getChromaticitiesAddContent3()->getName());
								if($me->getPart() == Calculation::PAPER_ADDCONTENT3){
									$me->save();
								}
							}
						}
							
						$sizes = $calc->getPaperAddContent3()->getMaxPaperSizeForMachine($m);
							
						// Update Papersize
						$calc->setPaperAddContent3Height($sizes["height"]);
						$calc->setPaperAddContent3Width($sizes["width"]);
						$calc->save();
							
						// Update Paper Grant
						// Zuschussbogen multipliziert Anzahl Druckplatten
						// $calc->setPaperAddContentGrant($order->getProduct()->getGrantPaper() * $calc->getPlateSetCount($me));
						// Zuschussbogen prozentual zu der Papiermenge
// 						$calc->setPaperAddContent3Grant($order->getProduct()->getGrantPaper() * $calc->getPaperCount(Calculation::PAPER_ADDCONTENT3) / 100 ); //Zuschuss
						
						$calc->setPaperAddContent3Grant($perference->getZuschussProDP() * $calc->getPlateSetCount($me));
							
						$calc->save();
					}
					unset($sizes);
				} else if($m->getType() == Machine::TYPE_FOLDER)
				{
					if($calc->getPagesContent())
					{
						$me = new Machineentry();
						$me->setMachine($m);
						$me->setMachineGroup($m->getGroup()->getId());
						$me->setCalcId($calc->getId());
						$me->setPart(Calculation::PAPER_CONTENT);
						$me->setTime(0);
						$me->save();
						$me->setTime($me->getMachine()->getRunningTime($me));
						$me->setPrice($me->getMachine()->getMachinePrice($me));
						$me->save();
					}

					if($calc->getPagesAddContent())
					{
						$me = new Machineentry();
						$me->setMachine($m);
						$me->setMachineGroup($m->getGroup()->getId());
						$me->setCalcId($calc->getId());
						$me->setPart(Calculation::PAPER_ADDCONTENT);
						$me->setTime(0);
						$me->save();
						$me->setTime($me->getMachine()->getRunningTime($me));
						$me->setPrice($me->getMachine()->getMachinePrice($me));
						$me->save();
					}

					if($calc->getPagesEnvelope())
					{
						$me = new Machineentry();
						$me->setMachine($m);
						$me->setMachineGroup($m->getGroup()->getId());
						$me->setCalcId($calc->getId());
						$me->setPart(Calculation::PAPER_ENVELOPE);
						$me->setTime(0);
						$me->save();
						$me->setTime($me->getMachine()->getRunningTime($me));
						$me->setPrice($me->getMachine()->getMachinePrice($me));
						$me->save();
					}
					if($calc->getPagesAddContent2())
					{
						$me = new Machineentry();
						$me->setMachine($m);
						$me->setMachineGroup($m->getGroup()->getId());
						$me->setCalcId($calc->getId());
						$me->setPart(Calculation::PAPER_ADDCONTENT2);
						$me->setTime(0);
						$me->save();
						$me->setTime($me->getMachine()->getRunningTime($me));
						$me->setPrice($me->getMachine()->getMachinePrice($me));
						$me->save();
					}
					if($calc->getPagesAddContent3())
					{
						$me = new Machineentry();
						$me->setMachine($m);
						$me->setMachineGroup($m->getGroup()->getId());
						$me->setCalcId($calc->getId());
						$me->setPart(Calculation::PAPER_ADDCONTENT3);
						$me->setTime(0);
						$me->save();
						$me->setTime($me->getMachine()->getRunningTime($me));
						$me->setPrice($me->getMachine()->getMachinePrice($me));
						$me->save();
					}
				} else
				{
					$me = new Machineentry();
					$me->setMachine($m);
					$me->setMachineGroup($m->getGroup()->getId());
					$me->setCalcId($calc->getId());
					$me->setTime(0);
					$me->save();
					$me->setTime($me->getMachine()->getRunningTime($me));
					$me->setPrice($me->getMachine()->getMachinePrice($me));
					$me->save();

					if($m->getType() == Machine::TYPE_CTP)
						$ctps[] = $me->getId();
				}
			} 

			//----------Falzmaschinen----------
			if ($m->getType() == Machine::TYPE_FOLDER && $calc->getPagesEnvelope() > 4 && ! Machineentry::entryExists($calc->getId(), $m->getId()))
			{
				// Falzmaschine fuer den Umschlag
				$me = new Machineentry();
				$me->setMachine($m);
				$me->setMachineGroup($m->getGroup()->getId());
				$me->setCalcId($calc->getId());
				$me->setPart(Calculation::PAPER_ENVELOPE);
				$me->setTime(0);
				$me->save();
				$me->setTime($me->getMachine()->getRunningTime($me));
				$me->setPrice($me->getMachine()->getMachinePrice($me));
				$me->save();
			}
		}

		//----------CTP = Computer to Plate (dig. Plattenbelichter)----------
		foreach ($ctps as $ctp)
		{
			$me = new Machineentry($ctp);
			$me->setTime($me->getMachine()->getRunningTime($me));
			$me->setPrice($me->getMachine()->getMachinePrice($me));
			$me->save();
		}

		// Preise und Zeiten aktualisieren
		foreach(Machineentry::getAllMachineentries($calc->getId()) as $me)
		{
			if($me->getMachine()->getType() != Machine::TYPE_MANUELL)
			{
				$me->setTime($me->getMachine()->getRunningTime($me));
				$me->setPrice($me->getMachine()->getMachinePrice($me));
				$me->save();
			}
		}


	}
	
	if((int)$_REQUEST["nextstep"] > 0)
	{
		echo '<script language="javascript">';
		echo 'document.location=\'index.php?page='.$_REQUEST['page'].'&exec=edit&step='.$_REQUEST["nextstep"].'&id='.$order->getId().'&calc_id='.$calc->getId();
		if($_REQUEST["addorder_amount"])
			foreach ($_REQUEST["addorder_amount"] as $amount)
			echo '&addorder_amount[]='.$amount;
		if($_REQUEST["addorder_sorts"])
		    foreach ($_REQUEST["addorder_sorts"] as $sorts)
		        echo '&addorder_sorts[]='.$sorts;
		echo '\'</script>';
	}
}

// save Step3
if((int)$_REQUEST["step"] == 3){
	
	// Zus. Position loeschen
	$delpos = new CalculationPosition((int)$_REQUEST['delpos']);	
	$savemsg = getSaveMessage($delpos->delete());
		
	$calc = new Calculation((int)$_REQUEST["calc_id"]);

	if($_REQUEST["subexec"] == "save")
	{
		$calc->setCalcAutoValues((int)$_REQUEST["auto_calc_values"]);
		$calc->setTextProcessing(trim(addslashes($_REQUEST["text_processing"])));
		$calc->setColorControl((int)$_REQUEST["color_control"]);
		$calc->setCutContent((float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["cut_content"]))));
		$calc->setCutAddContent((float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["cut_addcontent"]))));
		$calc->setCutAddContent2((float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["cut_addcontent2"]))));
		$calc->setCutAddContent3((float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["cut_addcontent3"]))));
		$calc->setCutEnvelope((float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["cut_envelope"]))));
		$calc->save();


		// Falzboegen speichern
		$calc->setFoldschemeContent(trim(addslashes($_REQUEST["foldscheme_content"])));
		$calc->setFoldschemeAddContent(trim(addslashes($_REQUEST["foldscheme_addcontent"])));
		$calc->setFoldschemeAddContent2(trim(addslashes($_REQUEST["foldscheme_addcontent2"])));
		$calc->setFoldschemeAddContent3(trim(addslashes($_REQUEST["foldscheme_addcontent3"])));
		if($calc->getPagesEnvelope())
			$calc->setFoldschemeEnvelope("1x".$calc->getPagesEnvelope());
		$calc->save();

		Machineentry::deleteAllForCalc($calc->getId());
		foreach(array_keys($_REQUEST) as $key) // hier wird fuer alle Schluessel die Verarbeitung gestartet
		{
			if(preg_match("/mach_id_(?P<id>\d+)/", $key, $m))
			{
				$id = $m["id"];
				// "Bearbeiten"-Modus
				if($_REQUEST["mach_id_{$id}"] != "" && $_REQUEST["mach_id_{$id}"] != 0)
				{
					$entry = new Machineentry($id);
					$entry->setMachine(new Machine($_REQUEST["mach_id_{$id}"]));
					$entry->setMachineGroup($entry->getMachine()->getGroup()->getId());
					if($_REQUEST["mach_time_{$id}"] != "")
						$entry->setTime((int)$_REQUEST["mach_time_{$id}"]);

					$entry->setCalcId($calc->getId());
					$entry->setPart((int)$_REQUEST["mach_part_{$id}"]);
					$entry->setFinishing(new Finishing((int)$_REQUEST["mach_finishing_{$id}"]));
					//gln, umschlagen/umstuelpen
					$entry->setUmschlagenUmstuelpen((int)$_REQUEST["umschl_umst_{$id}"]);
					//error_log($id." umschlumst ".$_REQUEST["umschl_umst_{$id}"]);
					$entry->setInfo(trim(addslashes($_REQUEST["mach_info_{$id}"])));

					$entry->setColor_detail(trim(addslashes($_REQUEST["mach_color_detail_{$id}"])));
					
					if($entry->getMachine()->getPriceBase() == Machine::PRICE_VARIABEL){
						$entry->setPrice((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["mach_manprice_{$id}"]))));
						//error_log("Tada ".$id." __ ".$_REQUEST["mach_id_{$id}"]." _ ".$_REQUEST["mach_manprice_{$id}"]." ---");
					} else {
						$entry->setPrice($entry->getMachine()->getMachinePrice($entry));
					}
					
					// Erweiterung der Fremdleistungen uebernehmen
					$entry->setSupplierPrice((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["mach_supplierprice_{$id}"]))));
					$entry->setSupplierID((int)$_REQUEST["mach_supplierid_{$id}"]);
					$entry->setSupplierStatus((int)$_REQUEST["mach_supplierstatus_{$id}"]);
					$entry->setSupplierInfo(trim(addslashes($_REQUEST["mach_supplierinfo_{$id}"])));
					if($_REQUEST["mach_senddate_{$id}"] != ""){
						$tmp_date = explode('.', trim(addslashes($_REQUEST["mach_senddate_{$id}"])));
						$tmp_date = mktime(2,0,0,$tmp_date[1],$tmp_date[0],$tmp_date[2]);
					} else {
						$tmp_date = 0;
					}
					$entry->setSupplierSendDate($tmp_date);
					//error_log(" --- ".$id." - ".$_REQUEST["mach_supplierid_{$id}"]." --- ");
					if($_REQUEST["mach_receivedate_{$id}"] != ""){
						$tmp_date = explode('.', trim(addslashes($_REQUEST["mach_receivedate_{$id}"])));
						$tmp_date = mktime(2,0,0,$tmp_date[1],$tmp_date[0],$tmp_date[2]);
					} else {
						$tmp_date = 0;
					}
					$entry->setSupplierReceiveDate($tmp_date);
					
					$entry->setSpecial_margin((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["mach_special_margin_{$id}"]))));
					$entry->setSpecial_margin_text($_REQUEST["mach_special_margin_text_{$id}"]);

					$entry->setTime($entry->getMachine()->getRunningTime($entry));
					$entry->setPrice($entry->getMachine()->getMachinePrice($entry));
					
					$entry->save();

					// Falls Druckmaschine -> Papiergroesse setzen
					if ($entry->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL 
							|| $entry->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
					{
						$sizes = $_REQUEST["mach_papersize_{$id}"];
						$sizes = explode("x", $sizes);

						if ($entry->getPart() == Calculation::PAPER_CONTENT)
						{
							// Update Papersize
							$calc->setPaperContentHeight($sizes[1]);
							$calc->setPaperContentWidth($sizes[0]);
							$calc->save();

							// Update Grant Paper
							if($entry->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET){
								// Zuschussbogen multipliziert Anzahl Druckplatten
								// $calc->setPaperContentGrant($order->getProduct()->getGrantPaper() * $calc->getPlateSetCount($entry));
								// Zuschussbogen prozentual zu der Papiermenge
// 								$calc->setPaperContentGrant($order->getProduct()->getGrantPaper() * $calc->getPaperCount(Calculation::PAPER_CONTENT) / 100); //Zuschuss
								$calc->setPaperContentGrant($perference->getZuschussProDP() * $calc->getPlateSetCount($entry));
							} else {
								$calc->setPaperContentGrant($order->getProduct()->getGrantPaper()); //Zuschuss
							}
							$calc->save();
						} else if ($entry->getPart() == Calculation::PAPER_ADDCONTENT)
						{
							// Update Papersize
							$calc->setPaperAddContentHeight($sizes[1]);
							$calc->setPaperAddContentWidth($sizes[0]);
							$calc->save();
							// Update Grant Paper
							if($entry->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET){
								// Zuschussbogen multipliziert Anzahl Druckplatten
								// $calc->setPaperAddContentGrant($order->getProduct()->getGrantPaper() * $calc->getPlateSetCount($entry));
								// Zuschussbogen prozentual zu der Papiermenge
// 								$calc->setPaperAddContentGrant($order->getProduct()->getGrantPaper() * $calc->getPaperCount(Calculation::PAPER_ADDCONTENT) / 100); //Zuschuss
								$calc->setPaperAddContentGrant($perference->getZuschussProDP() * $calc->getPlateSetCount($entry));
							} else {
								$calc->setPaperAddContentGrant($order->getProduct()->getGrantPaper()); //Zuschuss
							}
							$calc->save();
						} else if ($entry->getPart() == Calculation::PAPER_ENVELOPE)
						{
							// Update Papersize
							$calc->setPaperEnvelopeHeight($sizes[1]);
							$calc->setPaperEnvelopeWidth($sizes[0]);
							$calc->save();
							// Update Grant Paper
							if($entry->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET){
								// Zuschussbogen multipliziert Anzahl Druckplatten
								// $calc->setPaperEnvelopeGrant($order->getProduct()->getGrantPaper() * $calc->getPlateSetCount($entry));
								// Zuschussbogen prozentual zu der Papiermenge
// 								$calc->setPaperEnvelopeGrant($order->getProduct()->getGrantPaper() * $calc->getPaperCount(Calculation::PAPER_ENVELOPE) / 100); //Zuschuss
								$calc->setPaperEnvelopeGrant($perference->getZuschussProDP() * $calc->getPlateSetCount($entry));
							} else { 
								$calc->setPaperEnvelopeGrant($order->getProduct()->getGrantPaper()); //Zuschuss
							}
							$calc->save();
						} else if ($entry->getPart() == Calculation::PAPER_ADDCONTENT2)
						{
							// Update Papersize
							$calc->setPaperAddContent2Height($sizes[1]);
							$calc->setPaperAddContent2Width($sizes[0]);
							$calc->save();
							// Update Grant Paper
							if($entry->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET){
								// Zuschussbogen multipliziert Anzahl Druckplatten
								// $calc->setPaperAddContentGrant($order->getProduct()->getGrantPaper() * $calc->getPlateSetCount($entry));
								// Zuschussbogen prozentual zu der Papiermenge
// 								$calc->setPaperAddContent2Grant($order->getProduct()->getGrantPaper() * $calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) / 100); //Zuschuss
								$calc->setPaperAddContent2Grant($perference->getZuschussProDP() * $calc->getPlateSetCount($entry));
							} else {
								$calc->setPaperAddContent2Grant($order->getProduct()->getGrantPaper()); //Zuschuss
							}
							$calc->save();
						} else if ($entry->getPart() == Calculation::PAPER_ADDCONTENT3)
						{
							// Update Papersize
							$calc->setPaperAddContent3Height($sizes[1]);
							$calc->setPaperAddContent3Width($sizes[0]);
							$calc->save();
							// Update Grant Paper
							if($entry->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET){
								// Zuschussbogen multipliziert Anzahl Druckplatten
								// $calc->setPaperAddContentGrant($order->getProduct()->getGrantPaper() * $calc->getPlateSetCount($entry));
								// Zuschussbogen prozentual zu der Papiermenge
// 								$calc->setPaperAddContent3Grant($order->getProduct()->getGrantPaper() * $calc->getPaperCount(Calculation::PAPER_ADDCONTENT3) / 100); //Zuschuss
								$calc->setPaperAddContent3Grant($perference->getZuschussProDP() * $calc->getPlateSetCount($entry));
							} else {
								$calc->setPaperAddContent3Grant($order->getProduct()->getGrantPaper()); //Zuschuss
							}
							$calc->save();
						}

						unset($sizes);
					}
					if ($entry->getMachine()->getType() == Machine::TYPE_CUTTER){
// 					    echo "Test Test Hallo Schneidemachine hier!";
					    if ($_REQUEST["mach_format_in_{$id}"] != "" && $_REQUEST["mach_format_out_{$id}"] != ""){
    					    $tmp_cut_format_in = explode("x", $_REQUEST["mach_format_in_{$id}"]);
    					    $tmp_cut_format_out = explode("x", $_REQUEST["mach_format_out_{$id}"]);
    					    $entry->setFormat_in_width((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $tmp_cut_format_in[0]))));
    					    $entry->setFormat_in_height((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $tmp_cut_format_in[1]))));
//     					    echo "setFormat_in_width = " . $tmp_cut_format_in[0] . "</br>";
//     					    echo "setFormat_in_height = " . $tmp_cut_format_in[1] . "</br>";
    					    $entry->setFormat_out_width((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $tmp_cut_format_out[0]))));
    					    $entry->setFormat_out_height((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $tmp_cut_format_out[1]))));
//     					    echo "setFormat_out_width = " . $tmp_cut_format_out[0] . "</br>";
//     					    echo "setFormat_out_height = " . $tmp_cut_format_out[1] . "</br>";
					    }
					    
					    $entry->setRoll_dir((int)$_REQUEST["mach_roll_dir_{$id}"]);
					    $entry->setCutter_cuts($_REQUEST["mach_cutter_cuts_{$id}"]);
					    
					    $entry->setPrice($entry->getMachine()->getMachinePrice($entry));
					    $entry->save();
					}
					
					if ($entry->getMachine()->getType() == Machine::TYPE_FOLDER){
					    $entry->setFoldtype(new Foldtype((int)$_REQUEST["mach_foldtype_{$id}"]));
					    $entry->save();
					}
					
					if ($entry->getMachine()->getType() == Machine::TYPE_OTHER){
						// $entry->set
					}

					//echo $DB->getLastError();
				}
			}
		}
		
		

		if($calc->getCalcAutoValues())
			// Zeiten kalkulieren
			foreach(Machineentry::getAllMachineentries($calc->getId()) as $me)
			{
				if($me->getMachine()->getType() != Machine::TYPE_MANUELL)
				{
					$me->setTime($me->getMachine()->getRunningTime($me));
					$me->save();
				}
			}

			
			/*
			 * TODO: Muss hier nicht neher nach dem Preistyp geschaut werden, nicht nach dem Maschinentyp
			 * 		 "Preis variabel" mit Maschinentyp "Andere" wird immer mit 0 ueberschrieben
			 * ODER: Es muss die Funktion getMachinePrice angepasst werden
			 */
			
		// Preise kalkulieren
		foreach(Machineentry::getAllMachineentries($calc->getId()) as $me){
			if($me->getMachine()->getType() != Machine::TYPE_MANUELL){
				$me->setPrice($me->getMachine()->getMachinePrice($me));
				$me->save();
			}
		}
			
		/****
		$new_artlist = Array();
		$art_amounts = Array();
		$art_scales = Array();
		$number_of_article = (int)$_REQUEST["number_of_article"];
		if ($number_of_article > 0){
			for($i=0; $i<$number_of_article; $i++){
				$tmp_id = (int)$_REQUEST['calcart_id_'.$i];
				$tmp_amount = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST['art_amount_'.$i])));				
				$tmp_scale = (int)$_REQUEST['art_scale_'.$i];
				if($tmp_id != "" && $tmp_id != 0){
					$art_amounts[$tmp_id] = $tmp_amount;
					$art_scales[$tmp_id] = $tmp_scale;
					$new_artlist[] = new Article($tmp_id); 
				}
			}
		}
		$calc->setArticles($new_artlist);
		$calc->setArticleamounts($art_amounts);
		$calc->setArticlescales($art_scales);
		**/
		
		$xi=0;
		$calc_pos = Array();
		foreach ($_REQUEST["orderpos"] as $single_order){
			if ( !( $_REQUEST["orderpos"][$xi]["id"] == "0" && 			// Wenn in den "Neu"-Feldern nichts drin steht
					$_REQUEST["orderpos"][$xi]["comment"] == "" &&		// soll er auch nichts speichern
					$_REQUEST["orderpos"][$xi]["quantity"] == "")){
					
				$newpos = new CalculationPosition((int)$_REQUEST["orderpos"][$xi]["id"]);
				//Daten passend konvertieren
				$tmp_price = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["orderpos"][$xi]["price"])));
				$tmp_cost = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["orderpos"][$xi]["cost"])));
					
				$newpos->setPrice($tmp_price);
				$newpos->setCost($tmp_cost);
				$newpos->setComment(trim(addslashes($_REQUEST["orderpos"][$xi]["comment"])));
				$newpos->setQuantity((int)$_REQUEST["orderpos"][$xi]["quantity"]);
				$newpos->setType((int)$_REQUEST["orderpos"][$xi]["type"]);
				$newpos->setInvrel((int)$_REQUEST["orderpos"][$xi]["inv_rel"]);
				$newpos->setScale((int)$_REQUEST["orderpos"][$xi]["scale"]);
// 				echo '<h1>'.(int)$_REQUEST["orderpos"][$xi]["scale"].'</h1></br>';
// 				$newpos->setScale(CalculationPosition::SCALE_PER_PIECE);
				$newpos->setObjectid((int)$_REQUEST["orderpos"][$xi]["obj_id"]);
				$newpos->setTax((int)$_REQUEST["orderpos"][$xi]["tax"]);
				$newpos->setCalculationid((int)$calc->getId());
				$newpos->setShowQuantity((int)$_REQUEST["orderpos"][$xi]["show_doc_quantity"]);
				$newpos->setShowPrice((int)$_REQUEST["orderpos"][$xi]["show_doc_price"]);
				
				$calc_pos[] = $newpos;
				$xi++;
			}
		}
		//Positionen der Rechnung speichern
		CalculationPosition::saveMultipleCalculationpositions($calc_pos);
		echo $DB->getLastError();
		
		$calc->save();
		echo $DB->getLastError();
	}
	
	//Berechnen, ob es eingestellte Druckmaschinen gibt
	$tmp_s3_machs = Machineentry::getAllMachineentries($calc->getId());
	$printer_entrys = Array();		//Alle Drucker einer Kalkulation
	$printer_part1_exists = false; 	//Es gibt (k)einen Drucker fuer den Inhalt
	$printer_part2_exists = false; 	//Es gibt (k)einen Drucker fuer den zus. Inhalt
	$printer_part3_exists = false; 	//Es gibt (k)einen Drucker fuer den Umschlag
	$printer_part4_exists = false; 	//Es gibt (k)einen Drucker fuer den zus. Inhalt 2
	$printer_part5_exists = false; 	//Es gibt (k)einen Drucker fuer den zus. Inhalt 3
	foreach( $tmp_s3_machs as $tmp_s3_mach){
		if ($tmp_s3_mach->getPart() > 0 ){
			$printer_entrys[] = $tmp_s3_mach;
	
			if($tmp_s3_mach->getPart() == Calculation::PAPER_CONTENT){
				$printer_part1_exists = true;
			}
			if($tmp_s3_mach->getPart() == Calculation::PAPER_ADDCONTENT){
				$printer_part2_exists = true;
			}
			if($tmp_s3_mach->getPart() == Calculation::PAPER_ENVELOPE){
				$printer_part3_exists = true;
			}
			if($tmp_s3_mach->getPart() == Calculation::PAPER_ADDCONTENT2){
				$printer_part4_exists = true;
			}
			if($tmp_s3_mach->getPart() == Calculation::PAPER_ADDCONTENT3){
				$printer_part5_exists = true;
			}
		}
	}
	
	// Weiterleiten zu Step 4 oder in Step 3 bleiben
	if((int)$_REQUEST["nextstep"] > 0){
		echo '<script language="javascript">';
		echo 'document.location=\'index.php?page='.$_REQUEST['page'].'&exec=edit&step='.$_REQUEST["nextstep"].'&id='.$order->getId().'&calc_id='.$calc->getId();
		if($_REQUEST["addorder_sorts"]){
		    foreach ($_REQUEST["addorder_sorts"] as $sorts){
		        echo '&addorder_sorts[]='.$sorts;
		    }
		}
		if($_REQUEST["addorder_amount"]){
			foreach ($_REQUEST["addorder_amount"] as $amount){
			    echo '&addorder_amount[]='.$amount;
			}
		}
		echo "&origcalc=".$calc->getId();
		echo '\'</script>';
	}
}

if((int)$_REQUEST["step"] == 4)
{
	//Weitere Auflagen anlegen
	if($_REQUEST["addorder_sorts"] && $_REQUEST["addorder_amount"] && (int)$_REQUEST["origcalc"] > 0)
	{
	    Calculation::deleteCalculationsForUpdate($order->getId(), (int)$_REQUEST["origcalc"]); //gln
	    $tmp_calcs_arr = Array();
	    
	    foreach($_REQUEST["addorder_sorts"] as $sorts)
	    {
			// Kopie der Kalkulation anlegen
			$calc = new Calculation((int)$_REQUEST["origcalc"]);
			if ($sorts <= 0)
			    $sorts = 1;
	        $calc->setSorts($sorts);
			$calc->clearId();
	        $calc->save();
	        
	        $tmp_calcs_arr[] = $calc->getId();
	    }
	    
// 	    echo "</br>";
// 	    var_dump($tmp_calcs_arr);
// 	    echo "</br>";
	   
	    
	    $calc_i = 0;
		foreach($_REQUEST["addorder_amount"] as $amount)
		{
	        $calc = new Calculation($tmp_calcs_arr[$calc_i]);

			$sourceAmount = $calc->getAmount();
			$amount = $amount * $calc->getSorts();
			$calc->setAmount($amount);
			$ok = $calc->save();

			//Maschineneintraege kopieren
			if($ok)
			{
				$hasCTP = false;
				$needsCTP = false;
				// Preise und Zeiten aktualisieren
				foreach(Machineentry::getAllMachineentries((int)$_REQUEST["origcalc"]) as $me)
				{
					if(!$order->getProduct()->isDefaultMachine($me->getMachine(), $calc->getAmount())
							&& $order->getProduct()->isDefaultMachine($me->getMachine(), $sourceAmount))
					{
						// Maschine ist fuer diese Auflage nicht Standard, fuer die kleinere jedoch schon
						//  -> neue Maschine finden und setzen
						$groupMachs = Machine::getAllMachines(Machine::ORDER_ID, $me->getMachineGroup());
						foreach($groupMachs as $gm)
						{
							if($order->getProduct()->isDefaultMachine($gm, $amount))
							{
								$me->clearId();
								$me->setCalcId($calc->getId());
								$me->setMachine($gm);

								if($me->getFinishing() && !$me->getMachine()->getFinish())
									$me->setFinishing(new Finishing(0));

								$me->save();

								if($me->getMachine()->getType() == Machine::TYPE_CTP)
									$hasCTP = true;

								if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL || $me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
								{
									// Papiergroesse updaten
									if($me->getPart() == Calculation::PAPER_CONTENT)
									{
										//
										$sizes = $calc->getPaperContent()->getMaxPaperSizeForMachine($me->getMachine());
										// Update Papersize
										$calc->setPaperContentHeight($sizes["height"]);
										$calc->setPaperContentWidth($sizes["width"]);
										$calc->save();
									} elseif($me->getPart() == Calculation::PAPER_ADDCONTENT)
									{
										//
										$sizes = $calc->getPaperAddContent()->getMaxPaperSizeForMachine($me->getMachine());
										// Update Papersize
										$calc->setPaperAddContentHeight($sizes["height"]);
										$calc->setPaperAddContentWidth($sizes["width"]);
										$calc->save();
									} elseif($me->getPart() == Calculation::PAPER_ENVELOPE)
									{
										//
										$sizes = $calc->getPaperEnvelope()->getMaxPaperSizeForMachine($me->getMachine());
										// Update Papersize
										$calc->setPaperEnvelopeHeight($sizes["height"]);
										$calc->setPaperEnvelopeWidth($sizes["width"]);
										$calc->save();
									} elseif($me->getPart() == Calculation::PAPER_ADDCONTENT2)
									{
										//
										$sizes = $calc->getPaperAddContent2()->getMaxPaperSizeForMachine($me->getMachine());
										// Update Papersize
										$calc->setPaperAddContent2Height($sizes["height"]);
										$calc->setPaperAddContent2Width($sizes["width"]);
										$calc->save();
									} elseif($me->getPart() == Calculation::PAPER_ADDCONTENT3)
									{
										//
										$sizes = $calc->getPaperAddContent3()->getMaxPaperSizeForMachine($me->getMachine());
										// Update Papersize
										$calc->setPaperAddContent3Height($sizes["height"]);
										$calc->setPaperAddContent3Width($sizes["width"]);
										$calc->save();
									}
								} // if (Type Offset || Type Digital)

								if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
									$needsCTP = true;
							}
						}
					} else
					{
						$me->clearId();
						$me->setCalcId($calc->getId());
						$me->save();

						if($me->getMachine()->getType() == Machine::TYPE_CTP)
							$hasCTP = true;

					}
					unset($me);
				} // foreach MachineEntries

				// Plattenbelichter fehlt
				if($needsCTP && !$hasCTP)
				{
					// CTP suchen
					foreach(Machine::getAllMachines(Machine::ORDER_ID) as $mach)
					{
						if($mach->getType() == Machine::TYPE_CTP && $order->getProduct()->isDefaultMachine($mach, $amount))
						{
							$me = new Machineentry();
							$me->setMachine($mach);
							$me->setMachineGroup($mach->getGroup()->getId());
							$me->setCalcId($calc->getId());
							$me->setTime(0);
							$me->save();
						}
					}
				}

				foreach(Machineentry::getAllMachineentries($calc->getId()) as $me)
				{
					if($me->getMachine()->getType() != Machine::TYPE_MANUELL)
					{
						$me->setTime($me->getMachine()->getRunningTime($me));
						$me->setPrice($me->getMachine()->getMachinePrice($me));
						$me->save();
					}
					unset($me);
				}
				
				$savemsg = ""; //l�schen
				//Artikel kopieren oder auf die Auflage umrechnen
				$new_articlelist = Array();
				$new_scalelist = Array();
				$new_amountlist = Array();
				$original_calc = new Calculation((int)$_REQUEST["origcalc"]);
				$original_articles = $original_calc->getArticles();
				
				if (count($original_articles) > 0){
    				foreach ($original_articles as $orig_art){
    					// Artikel in die neue Liste hinzuf�gen
    					$new_articlelist[]= new Article($orig_art->getId());
    					
    					// Richtige Anzahl setzen
    								//if($original_calc->getArticlescale($orig_art->getId()) == 0){
    					$new_amountlist[$orig_art->getId()] = $original_calc->getArticleamount($orig_art->getId());
    								/*} //Wenn Umgerechnet werden muss
    								elseif ($original_calc->getArticlescale($orig_art->getId()) == 1){
    									$tmp_amount = $original_calc->getArticleamount($orig_art->getId());
    									$tmp_newamount = $tmp_amount / $original_calc->getAmount();
    									$new_amountlist[$orig_art->getId()] = ceil($tmp_newamount * $calc->getAmount());
    								}*/
    					
    					// Staffelung setzen
    					$new_scalelist[$orig_art->getId()] = $original_calc->getArticlescale($orig_art->getId());
    				}
				}
				
				$calc->setArticles($new_articlelist);
				$calc->setArticleamounts($new_amountlist);
				$calc->setArticlescales($new_scalelist);
				$calc->save();
				
				
			} // if ok
			$calc_i++;
		} // Foreach amount
	}


	if($_REQUEST["subexec"] == "save")
	{
		// Auftragsdaten
		$deliveryDate = 0;
		if($_REQUEST["delivery_date"] != "")
		{
			$deliveryDate = explode('.', trim(addslashes($_REQUEST["delivery_date"])));
			$deliveryDate = mktime(0,0,0,$deliveryDate[1],$deliveryDate[0],$deliveryDate[2]);
		}

		$order->setNotes(trim(addslashes($_REQUEST["order_notes"])));
		$order->setInvoiceAddress(new Address((int)$_REQUEST["invoice_address"]));
		$order->setDeliveryAddress(new Address((int)$_REQUEST["delivery_address"]));
		$order->setDeliveryTerms(new DeliveryTerms((int)$_REQUEST["delivery_terms"]));
		$order->setPaymentTerms(new PaymentTerms((int)$_REQUEST["payment_terms"]));
		$order->setDeliveryDate($deliveryDate);
		$order->setDeliveryCost((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["delivery_cost"]))));
		$order->setCustContactperson(new ContactPerson((int)$_REQUEST["cust_contactperson"]));
		$order->setInternContact(new User((int)$_REQUEST["intern_contactperson"]));
		$order->setTitle(trim(addslashes($_REQUEST["order_title"])));
		$order->setCustMessage(trim(addslashes($_REQUEST["cust_message"])));
		$order->setCustSign(trim(addslashes($_REQUEST["cust_sign"])));
		$order->setBeilagen(trim(addslashes($_REQUEST["order_beilagen"])));
		$order->save();

		// Kalkulationsdaten
		foreach(array_keys($_REQUEST) as $key)
		{
			if(preg_match("/add_charge_(?P<id>\d+)/", $key, $m))
			{
				// print_r($m);
				$calc = new Calculation($m["id"]);
				$calc->setAddCharge((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["add_charge_{$m["id"]}"]))));
				if($_REQUEST["margin_{$m["id"]}"] != "")
					$calc->setMargin((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["margin_{$m["id"]}"]))));
				$calc->setDiscount((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["discount_{$m["id"]}"]))));
				$calc->setState((int)$_REQUEST["state_{$m["id"]}"]);
				$calc->setPaperContentGrant((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["grant_content_{$m["id"]}"]))));
				$calc->setPaperAddContentGrant((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["grant_addcontent_{$m["id"]}"]))));
				$calc->setPaperEnvelopeGrant((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["grant_envelope_{$m["id"]}"]))));
				$calc->setPaperAddContent2Grant((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["grant_addcontent2_{$m["id"]}"]))));
				$calc->setPaperAddContent3Grant((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["grant_addcontent3_{$m["id"]}"]))));
				$calc->setTitle($_REQUEST["calc_title_{$m["id"]}"]);
				$calc->save();
				//echo $DB->getLastError();
			}
		}
	}
}


if((int)$_REQUEST["step"] == 7){
	
	// Notiz loeschen
	if ($_REQUEST["subexec"] == "deletenote"){
		$del_note = new Notes($_REQUEST["delnoteid"]);
		$del_note->delete();
	}
	
	// Datei-Anhang einer Notiz loeschen
	if ($_REQUEST["subexec"] == "deletenotefile"){
		$tmp_note = new Notes($_REQUEST["delnoteid"]);
		$del_filename = Notes::FILE_DESTINATION.$tmp_note->getFileName();
		unlink($del_filename);
		$tmp_note->setFileName("");
		$tmp_note->save();
	}
	
	// Nozi speichern
	if ($_REQUEST["subexec"] == "save"){
		if($_REQUEST["notes_title"] != NULL && $_REQUEST["notes_title"] != ""){
			$note = new Notes((int)$_REQUEST["notes_id"]);
			$note->setComment(trim(addslashes($_REQUEST["notes_comment"])));
			$note->setTitle(trim(addslashes($_REQUEST["notes_title"])));
			$note->setModule(Notes::MODULE_CALCULATION);
			$note->setObjectid($order->getId());
		  
			if (isset($_FILES["file_comment"])) {
				if ($_FILES["file_comment"]["name"] != "" && $_FILES["file_comment"]["name"] != NULL){
	
					$destination = Notes::FILE_DESTINATION;
					 
					// alte Datei loeschen, falls eine neue Datei hochgeladen wird
					$old_filename = $destination.$note->getFileName();
					unlink($old_filename);
					 
					$filename = date("Y_m_d-H_i_s_").$_FILES["file_comment"]["name"];
					$new_filename = $destination.$filename;
					$tmp_outer = move_uploaded_file($_FILES["file_comment"]["tmp_name"], $new_filename);
					 
					$note->setFileName($filename);
				}
			}
		  
			// Nur Admins und der Ersteller der Notiz duerfen diese bearbeiten und wenn es eine neue ist, muss Sie auch gespeichert werden
			if ($note->getCrtuser()->getId() == $_USER->getId() || $_USER->isAdmin() || $note->getId() == 0){
				// error_log( "sichere");
				$note->save();
			}
		  
			if($DB->getLastError()!=NULL && $DB->getLastError()!=""){
				$savemsg .= $DB->getLastError();
			}
		}
	}
	
}

?>

<link rel="stylesheet" href="css/order.css" type="text/css" />

<ul class="nav nav-pills">
  <li class="menu_order"><a href="#" onclick="location.href='index.php?page=<?=$_REQUEST['page']?>&id=<?=$_REQUEST['id'] ?>&exec=edit&step=1'">Produktauswahl</a></li>
  <li class="dropdown">
    <a href="#" data-toggle="dropdown" class="dropdown-toggle">Produktkonfiguration <b class="caret"></b></a>
    <ul class="dropdown-menu">
    <?php 
        $menu_counter = 0;
        foreach (Calculation::getAllCalculations($order) as $tmp_menu_calc){
            echo '<li><a href="#"  onclick="location.href=\'index.php?page='.$_REQUEST['page'].'&id='.$_REQUEST['id'].'&calc_id='.$tmp_menu_calc->getId().'&exec=edit&step=2\'">';
            echo '#' . $menu_counter . ' ' . $tmp_menu_calc->getTitle() . " ( Aufl. " . $tmp_menu_calc->getAmount() . " ) ";
            echo '</a></li>';
            $menu_counter++;
        }
    ?>
    </ul>
  </li>
  <li class="dropdown">
    <a href="#" data-toggle="dropdown" class="dropdown-toggle">Fertigungsprozess <b class="caret"></b></a>
    <ul class="dropdown-menu">
    <?php 
        $menu_counter = 0;
        foreach (Calculation::getAllCalculations($order) as $tmp_menu_calc){
            echo '<li><a href="#"  onclick="location.href=\'index.php?page='.$_REQUEST['page'].'&id='.$_REQUEST['id'].'&calc_id='.$tmp_menu_calc->getId().'&exec=edit&step=3\'">';
            echo '#' . $menu_counter . ' ' . $tmp_menu_calc->getTitle() . " ( Aufl. " . $tmp_menu_calc->getAmount() . " ) ";
            echo '</a></li>';
            $menu_counter++;
        }
    ?>
    </ul>
  </li>
  <li><a href="#" onclick="location.href='index.php?page=<?=$_REQUEST['page']?>&id=<?=$_REQUEST['id'] ?>&exec=edit&step=4'">Kalkulations&uuml;bersicht</a></li>
  <li><a href="#" onclick="location.href='index.php?page=<?=$_REQUEST['page']?>&id=<?=$_REQUEST['id'] ?>&exec=edit&step=6'">Dokumente</a></li>
  <li><a href="#" onclick="location.href='index.php?page=<?=$_REQUEST['page']?>&id=<?=$_REQUEST['id'] ?>&exec=edit&step=5'">Druckbogenvorschau</a></li>
  <?php 
  if ($order->getId() > 0){
      // Associations
      $association_object = $order;
      include 'libs/modules/associations/association.include.php';
      //-> END Associations
  }
  ?>
</ul> 


<div class="orderMainform">

	<? 
	$step = (int)$_REQUEST["step"];
	switch($step)
	{
		case 1:
			require_once 'order.step1.php';
			break;
		case 2:
			require_once 'order.step2.php';
			break;
		case 3:
			require_once 'order.step3.php';
			break;
		case 4:
			require_once 'order.step4.php';
			break;
		case 5:
			require_once 'order.printpreview.php';
			break;
		case 6:
			require_once 'order.documents.php';
			break;
		default:
			require_once 'order.step1.php';
	}
	?>

</div>
