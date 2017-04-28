<?
//----------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			22.08.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
require_once 'article.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/revenueaccounts/revenueaccount.class.php';

switch ($_REQUEST["exec"]) {
	case "delete":
		$del_article = new Article($_REQUEST["did"]);
		
		if ($del_article->getOrderid()>0)
		{
		    $tmp_dorder = new Order($del_article->getOrderid());
		    $tmp_dorder->setArticleid(0);
		    $tmp_dorder->save();
		}
		
		$del_article->delete();
		require_once 'article.overview.php';
		break;
	case "edit":
		// Daten setzen und speichern geschieht in der article.edit.php
		require_once 'article.edit.php';
		break;
	case "new":
		require_once 'article.edit.php';
		break;
	case "copy":
		require_once 'article.edit.php';
		break;
	case "fromorder":
	    if ($_REQUEST["orderid"])
	    {
	        $article = new Article();
	        $article->setOrderid((int)$_REQUEST["orderid"]);
	        $tmp_order = new Order((int)$_REQUEST["orderid"]);
	        $article->setTitle($tmp_order->getTitle());
			$article->setRevenueaccount(new RevenueaccountCategory());
	        $firstcalc = new Calculation();
	        if ($tmp_order->getId()>0)
	        {
    	        $res = $article->save();
    	        
    	        if ($res)
    	        {
    	            $orderamounts = Array();
    	            foreach (Calculation::getAllCalculations($tmp_order,Calculation::ORDER_AMOUNT) as $tmp_calc)
    	            {
    	                if ($tmp_calc->getState())
    	                {
    	                    $firstcalc = $tmp_calc;
    	                    $article->savePrice($tmp_calc->getAmount(), $tmp_calc->getAmount(), $tmp_calc->getSummaryPrice());
    	                    $article->saveCost($tmp_calc->getAmount(), $tmp_calc->getAmount(), $tmp_calc->getSubTotal(), 1, '');
    	                    $orderamounts[] = $tmp_calc->getAmount();
    	                }
    	            }
    	            $article->setOrderamounts($orderamounts);
    	            $article->setNumber($article->getId());
    
    	            $tmp_order->setArticleid($article->getId());
    	            $tmp_order->save();

					$tags = $article->getTags();


					$artdesc = 'Inhalt1<br>';
    	            $artdesc .= 'Produktformat: ' . $firstcalc->getProductFormat()->getName() . " (" . $firstcalc->getProductFormatWidth()."x".$firstcalc->getProductFormatHeight()."mm)<br>";
					$tags[] = $firstcalc->getProductFormat()->getName() . ' (' . $firstcalc->getProductFormatWidth()."x".$firstcalc->getProductFormatHeight().'mm)';
    	            $artdesc .= 'Material Inhalt: '. $firstcalc->getPaperContent()->getName() . ' ' . $firstcalc->getPaperContentWeight() . 'g <br>';
					$tags[] = $firstcalc->getPaperContent()->getName();
					$tags[] = $firstcalc->getPaperContentWeight() . 'g';
    	            $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesContent().'<br>';
					$artdesc .= 'Anzahl Sorten: '.$firstcalc->getSorts().'<br>';
    	            $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesContent()->getName() . '<br>';
					$tags[] = $firstcalc->getChromaticitiesContent()->getName();
    	            if ($firstcalc->getPaperAddContent()->getId()>0) {
						$tags[] = 'Inhalt 2';
    	                $artdesc .= '<br>Inhalt 2<br>';
    	                $artdesc .= 'Material Inhalt: '. $firstcalc->getPaperAddContent()->getName() . ' ' . $firstcalc->getPaperAddContentWeight() . 'g <br>';
    	                $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesAddContent().'<br>';
    	                $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesAddContent()->getName() . '<br>';
    	            }
    	            if ($firstcalc->getPaperAddContent2()->getId()>0) {
						$tags[] = 'Inhalt 3';
    	                $artdesc .= '<br>Inhalt 3<br>';
    	                $artdesc .= 'Material Inhalt: '. $firstcalc->getPaperAddContent2()->getName() . ' ' . $firstcalc->getPaperAddContent2Weight() . 'g <br>';
    	                $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesAddContent2().'<br>';
    	                $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesAddContent2()->getName() . '<br>';
    	            }
    	            if ($firstcalc->getPaperAddContent3()->getId()>0) {
						$tags[] = 'Inhalt 4';
    	                $artdesc .= '</br>Inhalt 4<br>';
    	                $artdesc .= 'Material Inhalt: '. $firstcalc->getPaperAddContent3()->getName() . ' ' . $firstcalc->getPaperAddContent3Weight() . 'g <br>';
    	                $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesAddContent3().'<br>';
    	                $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesAddContent3()->getName() . '<br>';
    	            }
    	            if ($firstcalc->getPaperEnvelope()->getId()>0) {
						$tags[] = 'Umschlag';
    	                $artdesc .= '<br>Umschlag<br>';
    	                $artdesc .= 'Material Umschlag: '. $firstcalc->getPaperEnvelope()->getName() . ' ' . $firstcalc->getPaperEnvelopeWeight() . 'g <br>';
    	                $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesEnvelope().'<br>';
    	                $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesEnvelope()->getName() . '<br>';
    	            }
					$artdesc .= '<br>Verarbeitung: '.$firstcalc->getTextProcessing().'<br>';
    	            $article->setDesc($artdesc);
    	            $article->setTags($tags);
    	            
    	            $savemsg = getSaveMessage($article->save()).$DB->getLastError();
    	            
    	            $_REQUEST["aid"] = $article->getId();
    		        require_once 'article.edit.php';
    	        }
	        }
	    }
	    break;
	case "uptfromorder":
	    if ($_REQUEST["orderid"] && $_REQUEST["aid"])
	    {
	        $article = new Article((int)$_REQUEST["aid"]);
	        $tmp_order = new Order((int)$_REQUEST["orderid"]);
	        $firstcalc = new Calculation();

	        if ($tmp_order->getId()>0)
	        {
	            $article->deltePriceSeperations();
	            $article->delteCostSeperations();
	             
	            $orderamounts = Array();
	            foreach (Calculation::getAllCalculations($tmp_order,Calculation::ORDER_AMOUNT) as $tmp_calc)
	            {
	                if ($tmp_calc->getState())
	                {
	                    $firstcalc = $tmp_calc;
	                    $article->savePrice($tmp_calc->getAmount(), $tmp_calc->getAmount(), $tmp_calc->getSummaryPrice());
						$article->saveCost($tmp_calc->getAmount(), $tmp_calc->getAmount(), $tmp_calc->getSubTotal(), 1, '');
	                    $orderamounts[] = $tmp_calc->getAmount();
	                }
	            }
	            $article->setOrderamounts($orderamounts);

				$tags = $article->getTags();

	            $artdesc = 'Inhalt 1<br>';
				$artdesc .= 'Produktformat: ' . $firstcalc->getProductFormat()->getName() . " (" . $firstcalc->getProductFormatWidth()."x".$firstcalc->getProductFormatHeight()."mm)<br>";
				$tags[] = $firstcalc->getProductFormat()->getName() . ' (' . $firstcalc->getProductFormatWidth()."x".$firstcalc->getProductFormatHeight().'mm)';
	            $artdesc .= 'Material Inhalt: '. $firstcalc->getPaperContent()->getName() . ' ' . $firstcalc->getPaperContentWeight() . 'g <br>';
				$tags[] = $firstcalc->getPaperContent()->getName();
				$tags[] = $firstcalc->getPaperContentWeight() . 'g';
	            $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesContent().'<br>';
				$artdesc .= 'Anzahl Sorten: '.$firstcalc->getSorts().'<br>';
	            $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesContent()->getName() . '<br>';
				$tags[] = $firstcalc->getChromaticitiesContent()->getName();
	            if ($firstcalc->getPaperAddContent()->getId()>0) {
					$tags[] = 'Inhalt 2';
	                $artdesc .= '<br>Inhalt 2<br>';
	                $artdesc .= 'Material Inhalt: '. $firstcalc->getPaperAddContent()->getName() . ' ' . $firstcalc->getPaperAddContentWeight() . 'g <br>';
	                $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesAddContent().'<br>';
	                $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesAddContent()->getName() . '<br>';
	            }
	            if ($firstcalc->getPaperAddContent2()->getId()>0) {
					$tags[] = 'Inhalt 3';
	                $artdesc .= '<br>Inhalt 3<br>';
	                $artdesc .= 'Material Inhalt: '. $firstcalc->getPaperAddContent2()->getName() . ' ' . $firstcalc->getPaperAddContent2Weight() . 'g <br>';
	                $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesAddContent2().'<br>';
	                $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesAddContent2()->getName() . '<br>';
	            }
	            if ($firstcalc->getPaperAddContent3()->getId()>0) {
					$tags[] = 'Inhalt 4';
	                $artdesc .= '</br>Inhalt 4<br>';
	                $artdesc .= 'Material Inhalt: '. $firstcalc->getPaperAddContent3()->getName() . ' ' . $firstcalc->getPaperAddContent3Weight() . 'g <br>';
	                $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesAddContent3().'<br>';
	                $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesAddContent3()->getName() . '<br>';
	            }
	            if ($firstcalc->getPaperEnvelope()->getId()>0) {
					$tags[] = 'Umschlag';
	                $artdesc .= '<br>Umschlag<br>';
	                $artdesc .= 'Material Umschlag: '. $firstcalc->getPaperEnvelope()->getName() . ' ' . $firstcalc->getPaperEnvelopeWeight() . 'g <br>';
	                $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesEnvelope().'<br>';
	                $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesEnvelope()->getName() . '<br>';
	            }
				$artdesc .= '<br>Verarbeitung: '.$firstcalc->getTextProcessing().'<br>';
				$artdesc .= '</tr></table>';
	            $article->setDesc($artdesc);
				$article->setTags($tags);
	            
	            $savemsg = getSaveMessage($article->save()).$DB->getLastError();
	            
	            require_once 'article.edit.php';



	        }
	    }
	    break;
	default:
		require_once 'article.overview.php';
		break;
}
?>