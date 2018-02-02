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
    $order->setArticleid(0);
	$newnumber = $_USER->getClient()->createOrderNumber(Client::NUMBER_ORDER);
	$order->setNumber($newnumber);
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
	$order->save();
	
	if ($_REQUEST["asso_class"] && $_REQUEST["asso_object"])
	{
	    $new_asso = new Association();
	    $new_asso->setModule1(get_class($order));
	    $new_asso->setObjectid1((int)$order->getId());
	    $new_asso->setModule2($_REQUEST["asso_class"]);
	    $new_asso->setObjectid2((int)$_REQUEST["asso_object"]);
	    $new_asso->save();
	}
	
	echo "<script type=\"text/javascript\">location.href='index.php?page=".$_REQUEST['page']."&id=".$order->getId()."&exec=edit&step=1';</script>";
}

// Set product
if((int)$_REQUEST["selProduct"] != 0){
	$order->setProduct(new Product((int)$_REQUEST["selProduct"]));
	$order->save();
}

// save Step2
if((int)$_REQUEST["step"] == 2){
	$calc = new Calculation((int)$_REQUEST["calc_id"]);

	if($_REQUEST["subexec"] == "copy"){
		$calc->clearId();
	}

	if($_REQUEST["subexec"] == "save") {
		$calc->setOrderId($order->getId());
		if ((int)$_REQUEST["order_sorts"] <= 0)
			$tmp_sorts = 1;
		else
			$tmp_sorts = (int)$_REQUEST["order_sorts"];
		$tmp_amount = (int)$_REQUEST["order_amount"] * $calc->getSorts();

		// Set Array
		$setarray = [];
		$setarray["setSorts"] = $tmp_sorts;
		$setarray["setAmount"] = $tmp_amount;
		$setarray["setPagesContent"] = (int)$_REQUEST["h_content_pages"];
		$setarray["setPagesAddContent"] = (int)$_REQUEST["h_addcontent_pages"];
		$setarray["setPagesEnvelope"] = (int)$_REQUEST["h_envelope_pages"];
		$setarray["setPagesAddContent2"] = (int)$_REQUEST["h_addcontent2_pages"];
		$setarray["setPagesAddContent3"] = (int)$_REQUEST["h_addcontent3_pages"];
		$setarray["setProductFormat"] = new Paperformat((int)$_REQUEST["h_product_format"]);
		$setarray["setProductFormatHeight"] = (int)$_REQUEST["order_product_height"];
		$setarray["setProductFormatWidth"] = (int)$_REQUEST["order_product_width"];
		$setarray["setProductFormatHeightOpen"] = (int)$_REQUEST["order_product_height_open"];
		$setarray["setProductFormatWidthOpen"] = (int)$_REQUEST["order_product_width_open"];
		$setarray["setPaperContent"] = new Paper((int)$_REQUEST["h_content_paper"]);
		$setarray["setPaperContentWeight"] = (int)$_REQUEST["h_content_paper_weight"];
		$setarray["setChromaticitiesContent"] = new Chromaticity((int)$_REQUEST["h_content_chromaticity"]);
		$setarray["setPaperAddContent"] = new Paper((int)$_REQUEST["h_addcontent_paper"]);
		$setarray["setPaperAddContentWeight"] = (int)$_REQUEST["h_addcontent_paper_weight"];
		$setarray["setChromaticitiesAddContent"] = new Chromaticity((int)$_REQUEST["h_addcontent_chromaticity"]);
		$setarray["setPaperEnvelope"] = new Paper((int)$_REQUEST["h_envelope_paper"]);
		$setarray["setPaperEnvelopeWeight"] = (int)$_REQUEST["h_envelope_paper_weight"];
		$setarray["setChromaticitiesEnvelope"] = new Chromaticity((int)$_REQUEST["h_envelope_chromaticity"]);
		$setarray["setPaperAddContent2"] = new Paper((int)$_REQUEST["h_addcontent2_paper"]);
		$setarray["setPaperAddContent2Weight"] = (int)$_REQUEST["h_addcontent2_paper_weight"];
		$setarray["setChromaticitiesAddContent2"] = new Chromaticity((int)$_REQUEST["h_addcontent2_chromaticity"]);
		$setarray["setPaperAddContent3"] = new Paper((int)$_REQUEST["h_addcontent3_paper"]);
		$setarray["setPaperAddContent3Weight"] = (int)$_REQUEST["h_addcontent3_paper_weight"];
		$setarray["setChromaticitiesAddContent3"] = new Chromaticity((int)$_REQUEST["h_addcontent3_chromaticity"]);
		$setarray["setEnvelopeWidthOpen"] = (int)$_REQUEST["order_envelope_width_open"];
		$setarray["setEnvelopeHeightOpen"] = (int)$_REQUEST["order_envelope_height_open"];
		$setarray["setCalcAutoValues"] = 1;
		$setarray["setMargin"] = $_USER->getClient()->getMargin();
		$setarray["setDiscount"] = 0;

		if ((int)$_REQUEST["calc_id"] == 0)
			$setarray["setTextProcessing"] = $order->getProduct()->getTextProcessing();


		if ((int)$_REQUEST["h_folding"] > 0)
			$setarray["setFolding"] = new Foldtype((int)$_REQUEST["h_folding"]);

		// Falzboegen speichern
		$schemes = $calc->getAvailableFoldschemes();
		if (is_array($schemes[1])) {
			$foldscheme_content = '';
			if ($schemes[1][0][16])
				$foldscheme_content .= $schemes[1][0][16] . "x16,";
			if ($schemes[1][0][8])
				$foldscheme_content .= $schemes[1][0][8] . "x8,";
			if ($schemes[1][0][4])
				$foldscheme_content .= $schemes[1][0][4] . "x4,";
			$foldscheme_content = substr($foldscheme_content, 0, -1);
		}

		if (is_array($schemes[2])) {
			$foldscheme_addcontent = '';
			if ($schemes[2][0][16])
				$foldscheme_addcontent .= $schemes[2][0][16] . "x16,";
			if ($schemes[2][0][8])
				$foldscheme_addcontent .= $schemes[2][0][8] . "x8,";
			if ($schemes[2][0][4])
				$foldscheme_addcontent .= $schemes[2][0][4] . "x4,";
			$foldscheme_addcontent = substr($foldscheme_addcontent, 0, -1);
		}

		if (is_array($schemes[4])) {
			$foldscheme_addcontent2 = '';
			if ($schemes[4][0][16])
				$foldscheme_addcontent2 .= $schemes[5][0][16] . "x16,";
			if ($schemes[4][0][8])
				$foldscheme_addcontent2 .= $schemes[5][0][8] . "x8,";
			if ($schemes[4][0][4])
				$foldscheme_addcontent2 .= $schemes[5][0][4] . "x4,";
			$foldscheme_addcontent2 = substr($foldscheme_addcontent2, 0, -1);
		}

		if (is_array($schemes[5])) {
			$foldscheme_addcontent3 = '';
			if ($schemes[5][0][16])
				$foldscheme_addcontent3 .= $schemes[5][0][16] . "x16,";
			if ($schemes[5][0][8])
				$foldscheme_addcontent3 .= $schemes[5][0][8] . "x8,";
			if ($schemes[5][0][4])
				$foldscheme_addcontent3 .= $schemes[5][0][4] . "x4,";
			$foldscheme_addcontent3 = substr($foldscheme_addcontent3, 0, -1);
		}

		$setarray["setFoldschemeContent"] = $foldscheme_content;
		$setarray["setFoldschemeAddContent"] = $foldscheme_addcontent;
		$setarray["setFoldschemeAddContent2"] = $foldschemeAddContent2;
		$setarray["setFoldschemeAddContent3"] = $foldschemeAddContent3;
		if ($calc->getPagesEnvelope())
			$setarray["setFoldschemeEnvelope"] = "1x" . $calc->getPagesEnvelope();

		CalculationService::CalculateAndSave($order,$calc,$setarray,2);

		if ((int)$_REQUEST["nextstep"] > 0) {
			echo '<script language="javascript">';
			echo 'document.location=\'index.php?page=' . $_REQUEST['page'] . '&exec=edit&step=' . $_REQUEST["nextstep"] . '&id=' . $order->getId() . '&calc_id=' . $calc->getId();
			if ($_REQUEST["addorder_amount"])
				foreach ($_REQUEST["addorder_amount"] as $amount)
					echo '&addorder_amount[]=' . $amount;
			if ($_REQUEST["addorder_sorts"])
				foreach ($_REQUEST["addorder_sorts"] as $sorts)
					echo '&addorder_sorts[]=' . $sorts;
			echo '\'</script>';

		}
	}
}

// save Step3
if((int)$_REQUEST["step"] == 3){
	$calc = new Calculation((int)$_REQUEST["calc_id"]);

	if($_REQUEST["subexec"] == "save")
	{
		// Set Array
		$setarray = [];
		$setarray["setCalcAutoValues"] = (int)$_REQUEST["auto_calc_values"];
		$setarray["setCalcDebug"] = (int)$_REQUEST["debug_calc"];
		$setarray["setTextProcessing"] = trim(addslashes($_REQUEST["text_processing"]));
		$setarray["setColorControl"] = (int)$_REQUEST["color_control"];
		$setarray["setCutContent"] = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["cut_content"])));
		$setarray["setCutAddContent"] = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["cut_addcontent"])));
		$setarray["setCutAddContent2"] = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["cut_addcontent2"])));
		$setarray["setCutAddContent3"] = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["cut_addcontent3"])));
		$setarray["setCutEnvelope"] = (float)sprintf("%.2f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["cut_envelope"])));
		$setarray["setFormat_in_content"] = $_REQUEST["format_in_content"];
		$setarray["setFormat_in_addcontent"] = $_REQUEST["format_in_addcontent"];
		$setarray["setFormat_in_addcontent2"] = $_REQUEST["format_in_addcontent2"];
		$setarray["setFormat_in_addcontent3"] = $_REQUEST["format_in_addcontent3"];
		$setarray["setFormat_in_envelope"] = $_REQUEST["format_in_envelope"];
		$setarray["setFoldschemeContent"] = trim(addslashes($_REQUEST["foldscheme_content"]));
		$setarray["setFoldschemeAddContent"] = trim(addslashes($_REQUEST["foldscheme_addcontent"]));
		$setarray["setFoldschemeAddContent2"] = trim(addslashes($_REQUEST["foldscheme_addcontent2"]));
		$setarray["setFoldschemeAddContent3"] = trim(addslashes($_REQUEST["foldscheme_addcontent3"]));
		if($calc->getPagesEnvelope())
			$setarray["setFoldschemeEnvelope"] = "1x".$calc->getPagesEnvelope();

		CalculationService::CalculateAndSave($order,$calc,$setarray,3,$_REQUEST);

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
				CalculationService::CalculateAndSave($order,$calc,[],3,null,(int)$_REQUEST["origcalc"]);


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
//										$grant = ($perference->getZuschussProDP() * $calc->getPlateCount($me)) + ($calc->getPaperCount(Calculation::PAPER_CONTENT) / 100 * $perference->getZuschussPercent());
										$grant = ($me->getDpgrant() * $calc->getPlateCount($me)) + ($calc->getPaperCount(Calculation::PAPER_CONTENT) / 100 * $me->getPercentgrant());
										$calc->setPaperContentGrant($grant);
										$calc->save();
									} elseif($me->getPart() == Calculation::PAPER_ADDCONTENT)
									{
										$sizes = $calc->getPaperAddContent()->getMaxPaperSizeForMachine($me->getMachine());
										// Update Papersize
										$calc->setPaperAddContentHeight($sizes["height"]);
										$calc->setPaperAddContentWidth($sizes["width"]);
//										$grant = ($perference->getZuschussProDP() * $calc->getPlateCount($me)) + ($calc->getPaperCount(Calculation::PAPER_ADDCONTENT) / 100 * $perference->getZuschussPercent());
										$grant = ($me->getDpgrant() * $calc->getPlateCount($me)) + ($calc->getPaperCount(Calculation::PAPER_ADDCONTENT) / 100 * $me->getPercentgrant());
										$calc->setPaperAddContentGrant($grant);
										$calc->save();
									} elseif($me->getPart() == Calculation::PAPER_ENVELOPE)
									{
										//
										$sizes = $calc->getPaperEnvelope()->getMaxPaperSizeForMachine($me->getMachine());
										// Update Papersize
										$calc->setPaperEnvelopeHeight($sizes["height"]);
										$calc->setPaperEnvelopeWidth($sizes["width"]);
//										$grant = ($perference->getZuschussProDP() * $calc->getPlateCount($me)) + ($calc->getPaperCount(Calculation::PAPER_ENVELOPE) / 100 * $perference->getZuschussPercent());
										$grant = ($me->getDpgrant() * $calc->getPlateCount($me)) + ($calc->getPaperCount(Calculation::PAPER_ENVELOPE) / 100 * $me->getPercentgrant());
										$calc->setPaperEnvelopeGrant($grant);
										$calc->save();
									} elseif($me->getPart() == Calculation::PAPER_ADDCONTENT2)
									{
										//
										$sizes = $calc->getPaperAddContent2()->getMaxPaperSizeForMachine($me->getMachine());
										// Update Papersize
										$calc->setPaperAddContent2Height($sizes["height"]);
										$calc->setPaperAddContent2Width($sizes["width"]);
//										$grant = ($perference->getZuschussProDP() * $calc->getPlateCount($me)) + ($calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) / 100 * $perference->getZuschussPercent());
										$grant = ($me->getDpgrant() * $calc->getPlateCount($me)) + ($calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) / 100 * $me->getPercentgrant());
										$calc->setPaperAddContent2Grant($grant);
										$calc->save();
									} elseif($me->getPart() == Calculation::PAPER_ADDCONTENT3)
									{
										//
										$sizes = $calc->getPaperAddContent3()->getMaxPaperSizeForMachine($me->getMachine());
										// Update Papersize
										$calc->setPaperAddContent3Height($sizes["height"]);
										$calc->setPaperAddContent3Width($sizes["width"]);
//										$grant = ($perference->getZuschussProDP() * $calc->getPlateCount($me)) + ($calc->getPaperCount(Calculation::PAPER_ADDCONTENT3) / 100 * $perference->getZuschussPercent());
										$grant = ($me->getDpgrant() * $calc->getPlateCount($me)) + ($calc->getPaperCount(Calculation::PAPER_ADDCONTENT3) / 100 * $me->getPercentgrant());
										$calc->setPaperAddContent3Grant($grant);
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
					// Lackberechnung
					if($me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL || $me->getMachine()->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
					{
						if($me->getPart() == Calculation::PAPER_CONTENT)
						{
							if ($me->getMachine()->getFinish()){
								if ($me->getFinishing()->getId()>0){
									$finishcontent = CalculationService::CalculateFinishUsed(
										$calc->getAmount(),
										$calc->getPagesContent(),
										$calc->getProductFormatWidth(),
										$calc->getProductFormatHeight(),
										$me->getFinishingcoverage(),
										$calc->getSorts(),
										$calc->getCutContent()
									);
									$calc->setFinishusedcontent($finishcontent);
									$calc->setFinishContent($me->getFinishing());
								}
							}
						} elseif($me->getPart() == Calculation::PAPER_ADDCONTENT)
						{
							if ($me->getMachine()->getFinish()){
								if ($me->getFinishing()->getId()>0){
									$finishaddcontent = CalculationService::CalculateFinishUsed(
										$calc->getAmount(),
										$calc->getPagesAddContent(),
										$calc->getProductFormatWidth(),
										$calc->getProductFormatHeight(),
										$me->getFinishingcoverage(),
										$calc->getSorts(),
										$calc->getCutAddContent()
									);
									$calc->setFinishusedaddcontent($finishaddcontent);
									$calc->setFinishAddContent($me->getFinishing());
								}
							}
						} elseif($me->getPart() == Calculation::PAPER_ENVELOPE)
						{
							if ($me->getMachine()->getFinish()){
								if ($me->getFinishing()->getId()>0){
									$finishenvelope = CalculationService::CalculateFinishUsed(
										$calc->getAmount(),
										$calc->getPagesEnvelope(),
										$calc->getProductFormatWidth(),
										$calc->getProductFormatHeight(),
										$me->getFinishingcoverage(),
										$calc->getSorts(),
										$calc->getCutEnvelope()
									);
									$calc->setFinishusedenvelope($finishenvelope);
									$calc->setFinishEnvelope($me->getFinishing());
								}
							}
						} elseif($me->getPart() == Calculation::PAPER_ADDCONTENT2)
						{
							if ($me->getMachine()->getFinish()){
								if ($me->getFinishing()->getId()>0){
									$finishaddcontent2 = CalculationService::CalculateFinishUsed(
										$calc->getAmount(),
										$calc->getPagesAddContent2(),
										$calc->getProductFormatWidth(),
										$calc->getProductFormatHeight(),
										$me->getFinishingcoverage(),
										$calc->getSorts(),
										$calc->getCutAddContent2()
									);
									$calc->setFinishusedaddcontent2($finishaddcontent2);
									$calc->setFinishAddContent2($me->getFinishing());
								}
							}
						} elseif($me->getPart() == Calculation::PAPER_ADDCONTENT3)
						{
							if ($me->getMachine()->getFinish()){
								if ($me->getFinishing()->getId()>0){
									$finishaddcontent3 = CalculationService::CalculateFinishUsed(
										$calc->getAmount(),
										$calc->getPagesAddContent3(),
										$calc->getProductFormatWidth(),
										$calc->getProductFormatHeight(),
										$me->getFinishingcoverage(),
										$calc->getSorts(),
										$calc->getCutAddContent3()
									);
									$calc->setFinishusedaddcontent3($finishaddcontent3);
									$calc->setFinishAddContent3($me->getFinishing());
								}
							}
						}
						$calc->save();
					}
					if($me->getMachine()->getType() != Machine::TYPE_MANUELL)
					{
						$me->setTime($me->getMachine()->getRunningTime($me));
						$me->setPrice($me->getMachine()->getMachinePrice($me));
						$me->save();
					}
					unset($me);
				}

				// Farbkostenberechnung
				if($calc->getPagesContent()){
					$inkcontent = CalculationService::CalculateColorUsed(
						$calc->getAmount(),
						$calc->getPagesContent(),
						$calc->getProductFormatWidth(),
						$calc->getProductFormatHeight(),
						$order->getProduct()->getInkcoverage(),
						$calc->getChromaticitiesContent()->getColorsFront(),
						$calc->getSorts(),
						$calc->getCutContent()
					);
					$calc->setInkusedcontent($inkcontent);
				}
				if($calc->getPagesEnvelope()){
					$inkenvelope = CalculationService::CalculateColorUsed(
						$calc->getAmount(),
						$calc->getPagesEnvelope(),
						$calc->getProductFormatWidth(),
						$calc->getProductFormatHeight(),
						$order->getProduct()->getInkcoverage(),
						$calc->getChromaticitiesEnvelope()->getColorsFront(),
						$calc->getSorts(),
						$calc->getCutEnvelope()
					);
					$calc->setInkusedenvelope($inkenvelope);
				}
				if($calc->getPagesAddContent()){
					$inkaddcontent = CalculationService::CalculateColorUsed(
						$calc->getAmount(),
						$calc->getPagesAddContent(),
						$calc->getProductFormatWidth(),
						$calc->getProductFormatHeight(),
						$order->getProduct()->getInkcoverage(),
						$calc->getChromaticitiesAddContent()->getColorsFront(),
						$calc->getSorts(),
						$calc->getCutAddContent()
					);
					$calc->setInkusedaddcontent($inkaddcontent);
				}
				if($calc->getPagesAddContent2()){
					$inkaddcontent2 = CalculationService::CalculateColorUsed(
						$calc->getAmount(),
						$calc->getPagesAddContent2(),
						$calc->getProductFormatWidth(),
						$calc->getProductFormatHeight(),
						$order->getProduct()->getInkcoverage(),
						$calc->getChromaticitiesAddContent2()->getColorsFront(),
						$calc->getSorts(),
						$calc->getCutAddContent2()
					);
					$calc->setInkusedaddcontent2($inkaddcontent2);
				}
				if($calc->getPagesAddContent3()){
					$inkaddcontent3 = CalculationService::CalculateColorUsed(
						$calc->getAmount(),
						$calc->getPagesAddContent3(),
						$calc->getProductFormatWidth(),
						$calc->getProductFormatHeight(),
						$order->getProduct()->getInkcoverage(),
						$calc->getChromaticitiesAddContent3()->getColorsFront(),
						$calc->getSorts(),
						$calc->getCutAddContent3()
					);
					$calc->setInkusedaddcontent3($inkaddcontent3);
				}

				$savemsg = ""; //l�schen
				$calc->setPricesub(tofloat($calc->getSubTotal()));
				$calc->setPricetotal(tofloat($calc->getSummaryPrice()));
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
		$order->setInternContact(new User((int)$_REQUEST["intern_contactperson"]));
		$order->setTitle(trim(addslashes($_REQUEST["order_title"])));
		$order->setBeilagen(trim(addslashes($_REQUEST["order_beilagen"])));
		$order->save();

		// Kalkulationsdaten
		foreach(array_keys($_REQUEST) as $key)
		{
			if(preg_match("/add_charge_(?P<id>\d+)/", $key, $m))
			{
				$calc = new Calculation($m["id"]);
				if (isset($_REQUEST["add_charge_{$m["id"]}"]))
					$calc->setAddCharge((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["add_charge_{$m["id"]}"]))));
				if (isset($_REQUEST["material_charge_{$m["id"]}"]))
					$calc->setMaterialCharge((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["material_charge_{$m["id"]}"]))));
				if (isset($_REQUEST["process_charge_{$m["id"]}"]))
					$calc->setProcessCharge((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["process_charge_{$m["id"]}"]))));
				if (isset($_REQUEST["material_percent_{$m["id"]}"]))
					$calc->setMaterialPercent((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["material_percent_{$m["id"]}"]))));
				if (isset($_REQUEST["process_percent_{$m["id"]}"]))
					$calc->setProcessPercent((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["process_percent_{$m["id"]}"]))));
				if($_REQUEST["margin_{$m["id"]}"] != "")
					$calc->setMargin((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["margin_{$m["id"]}"]))));
				if (isset($_REQUEST["discount_{$m["id"]}"]))
					$calc->setDiscount((float)sprintf("%.4f", (float)str_replace(",", ".", str_replace(".", "", $_REQUEST["discount_{$m["id"]}"]))));
				$calc->setState((int)$_REQUEST["state_{$m["id"]}"]);
				$calc->setTitle($_REQUEST["calc_title_{$m["id"]}"]);
				$calc->setPricesub(tofloat($calc->getSubTotal()));
				$calc->setPricetotal(tofloat($calc->getSummaryPrice()));
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

<div class="navbar navbar-default">
	<div class="container-fluid">
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
			<ul class="nav navbar-nav">
				<li class="menu_order"><a href="#"
										  onclick="location.href='index.php?page=<?= $_REQUEST['page'] ?>&id=<?= $_REQUEST['id'] ?>&exec=edit&step=1'">Produktauswahl</a>
				</li>
				<li class="dropdown">
					<a href="#" data-toggle="dropdown" class="dropdown-toggle">Produktkonfiguration <b
							class="caret"></b></a>
					<ul class="dropdown-menu">
						<?php
						$menu_counter = 0;
						foreach (Calculation::getAllCalculations($order, Calculation::ORDER_AMOUNT) as $tmp_menu_calc) {
							echo '<li><a href="#"  onclick="location.href=\'index.php?page=' . $_REQUEST['page'] . '&id=' . $_REQUEST['id'] . '&calc_id=' . $tmp_menu_calc->getId() . '&exec=edit&step=2\'">';
							echo '#' . $menu_counter . ' ' . $tmp_menu_calc->getTitle() . " ( Aufl. " . $tmp_menu_calc->getAmount() . " ) ";
							echo '</a></li>';
							$menu_counter++;
						}
						?>
					</ul>
				</li>
				<li class="dropdown">
					<a href="#" data-toggle="dropdown" class="dropdown-toggle">Fertigungsprozess <b
							class="caret"></b></a>
					<ul class="dropdown-menu">
						<?php
						$menu_counter = 0;
						foreach (Calculation::getAllCalculations($order, Calculation::ORDER_AMOUNT) as $tmp_menu_calc) {
							echo '<li><a href="#"  onclick="location.href=\'index.php?page=' . $_REQUEST['page'] . '&id=' . $_REQUEST['id'] . '&calc_id=' . $tmp_menu_calc->getId() . '&exec=edit&step=3\'">';
							echo '#' . $menu_counter . ' ' . $tmp_menu_calc->getTitle() . " ( Aufl. " . $tmp_menu_calc->getAmount() . " ) ";
							echo '</a></li>';
							$menu_counter++;
						}
						?>
					</ul>
				</li>
				<li><a href="#"
					   onclick="location.href='index.php?page=<?= $_REQUEST['page'] ?>&id=<?= $_REQUEST['id'] ?>&exec=edit&step=4'">Kalkulations&uuml;bersicht</a>
				</li>
				<li><a href="#"
					   onclick="location.href='index.php?page=<?= $_REQUEST['page'] ?>&id=<?= $_REQUEST['id'] ?>&exec=edit&step=7'">Detailierte
						Übersicht</a></li>
				<li><a href="#"
					   onclick="location.href='index.php?page=<?= $_REQUEST['page'] ?>&id=<?= $_REQUEST['id'] ?>&exec=edit&step=5'">Druckbogenvorschau</a>
				</li>
				<?php
				if ($order->getId() > 0) {
					// Associations
					$association_object = $order;
					include 'libs/modules/associations/association.include.php';
					//-> END Associations
				}
				?>
			</ul>
		</div>
	</div>
</div>


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
		case 7:
			require_once 'order.showDetailed.php';
			break;
		default:
			require_once 'order.step1.php';
	}
	?>

</div>
