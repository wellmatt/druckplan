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
	        $firstcalc = new Calculation();
	        if ($tmp_order->getId()>0)
	        {
    	        $res = $article->save();
    	        
    	        if ($res)
    	        {
    	            $orderamounts = Array();
    	            foreach (Calculation::getAllCalculations($tmp_order) as $tmp_calc)
    	            {
    	                if ($tmp_calc->getState())
    	                {
    	                    $firstcalc = $tmp_calc;
    	                    $article->savePrice($tmp_calc->getAmount(), $tmp_calc->getAmount(), $tmp_calc->getSummaryPrice());
    	                    $article->saveCost($tmp_calc->getAmount(), $tmp_calc->getAmount(), $tmp_calc->getSubTotal());
    	                    $orderamounts[] = $tmp_calc->getAmount();
    	                }
    	            }
    	            $article->setOrderamounts($orderamounts);
    	            $article->setNumber($article->getId());
    
    	            $tmp_order->setArticleid($article->getId());
    	            $tmp_order->save();
    
    	            $artdesc = 'Inhalt 1<br>';
    	            $artdesc .= 'Produktformat: ' . $firstcalc->getProductFormat()->getName() . " (" . $firstcalc->getProductFormatWidth()."x".$firstcalc->getProductFormatHeight()."mm)<br>";
    	            $artdesc .= 'Material Inhalt: '. $firstcalc->getPaperContent()->getName() . ' ' . $firstcalc->getPaperContentWeight() . 'g <br>';
    	            $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesContent().'<br>';
    	            $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesContent()->getName() . '<br>';
    	            if ($firstcalc->getPaperAddContent()->getId()>0) {
    	                $artdesc .= '<br>Inhalt 2<br>';
    	                $artdesc .= 'Material Inhalt: '. $firstcalc->getPaperAddContent()->getName() . ' ' . $firstcalc->getPaperAddContentWeight() . 'g <br>';
    	                $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesAddContent().'<br>';
    	                $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesAddContent()->getName() . '<br>';
    	            }
    	            if ($firstcalc->getPaperAddContent2()->getId()>0) {
    	                $artdesc .= '<br>Inhalt 3<br>';
    	                $artdesc .= 'Material Inhalt: '. $firstcalc->getPaperAddContent2()->getName() . ' ' . $firstcalc->getPaperAddContent2Weight() . 'g <br>';
    	                $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesAddContent2().'<br>';
    	                $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesAddContent2()->getName() . '<br>';
    	            }
    	            if ($firstcalc->getPaperAddContent3()->getId()>0) {
    	                $artdesc .= '</br>Inhalt 4<br>';
    	                $artdesc .= 'Material Inhalt: '. $firstcalc->getPaperAddContent3()->getName() . ' ' . $firstcalc->getPaperAddContent3Weight() . 'g <br>';
    	                $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesAddContent3().'<br>';
    	                $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesAddContent3()->getName() . '<br>';
    	            }
    	            if ($firstcalc->getPaperEnvelope()->getId()>0) {
    	                $artdesc .= '<br>Umschlag<br>';
    	                $artdesc .= 'Material Inhalt: '. $firstcalc->getPaperEnvelope()->getName() . ' ' . $firstcalc->getPaperEnvelopeWeight() . 'g <br>';
    	                $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesEnvelope().'<br>';
    	                $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesEnvelope()->getName() . '<br>';
    	            }
    	            $article->setDesc($artdesc);
    	            
    	            
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
	            foreach (Calculation::getAllCalculations($tmp_order) as $tmp_calc)
	            {
	                if ($tmp_calc->getState())
	                {
	                    $firstcalc = $tmp_calc;
	                    $article->savePrice($tmp_calc->getAmount(), $tmp_calc->getAmount(), $tmp_calc->getSummaryPrice());
	                    $article->saveCost($tmp_calc->getAmount(), $tmp_calc->getAmount(), $tmp_calc->getSubTotal());
	                    $orderamounts[] = $tmp_calc->getAmount();
	                }
	            }
	            $article->setOrderamounts($orderamounts);
	            
	            $artdesc = 'Inhalt 1<br>';
				$artdesc .= 'Produktformat: ' . $firstcalc->getProductFormat()->getName() . " (" . $firstcalc->getProductFormatWidth()."x".$firstcalc->getProductFormatHeight()."mm)<br>";
	            $artdesc .= 'Material Inhalt: '. $firstcalc->getPaperContent()->getName() . ' ' . $firstcalc->getPaperContentWeight() . 'g <br>';
	            $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesContent().'<br>';
	            $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesContent()->getName() . '<br>';
	            if ($firstcalc->getPaperAddContent()->getId()>0) {
	                $artdesc .= '<br>Inhalt 2<br>';
	                $artdesc .= 'Material Inhalt: '. $firstcalc->getPaperAddContent()->getName() . ' ' . $firstcalc->getPaperAddContentWeight() . 'g <br>';
	                $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesAddContent().'<br>';
	                $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesAddContent()->getName() . '<br>';
	            }
	            if ($firstcalc->getPaperAddContent2()->getId()>0) {
	                $artdesc .= '<br>Inhalt 3<br>';
	                $artdesc .= 'Material Inhalt: '. $firstcalc->getPaperAddContent2()->getName() . ' ' . $firstcalc->getPaperAddContent2Weight() . 'g <br>';
	                $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesAddContent2().'<br>';
	                $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesAddContent2()->getName() . '<br>';
	            }
	            if ($firstcalc->getPaperAddContent3()->getId()>0) {
	                $artdesc .= '</br>Inhalt 4<br>';
	                $artdesc .= 'Material Inhalt: '. $firstcalc->getPaperAddContent3()->getName() . ' ' . $firstcalc->getPaperAddContent3Weight() . 'g <br>';
	                $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesAddContent3().'<br>';
	                $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesAddContent3()->getName() . '<br>';
	            }
	            if ($firstcalc->getPaperEnvelope()->getId()>0) {
	                $artdesc .= '<br>Umschlag<br>';
	                $artdesc .= 'Material Inhalt: '. $firstcalc->getPaperEnvelope()->getName() . ' ' . $firstcalc->getPaperEnvelopeWeight() . 'g <br>';
	                $artdesc .= 'Anzahl Seiten: '.$firstcalc->getPagesEnvelope().'<br>';
	                $artdesc .= 'Farbigkeit: ' . $firstcalc->getChromaticitiesEnvelope()->getName() . '<br>';
	            }
	            $article->setDesc($artdesc);
	            
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