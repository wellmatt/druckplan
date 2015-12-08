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
	        
	        $res = $article->save();
	        
	        if ($res)
	        {
	            $orderamounts = Array();
	            foreach (Calculation::getAllCalculations($tmp_order) as $tmp_calc)
	            {
	                if ($tmp_calc->getState())
	                {
	                    $article->savePrice($tmp_calc->getAmount(), $tmp_calc->getAmount(), $tmp_calc->getSummaryPrice());
	                    $article->saveCost($tmp_calc->getAmount(), $tmp_calc->getAmount(), $tmp_calc->getSubTotal());
	                    $orderamounts[] = $tmp_calc->getAmount();
	                }
	            }
	            $article->setOrderamounts($orderamounts);
	            $article->setNumber($article->getId());
	            $savemsg = getSaveMessage($article->save()).$DB->getLastError();
	            
	            $tmp_order->setArticleid($article->getId());
	            $tmp_order->save();
	            
	            $_REQUEST["aid"] = $article->getId();
		        require_once 'article.edit.php';
	        }
	    }
	    break;
	case "uptfromorder":
	    if ($_REQUEST["orderid"] && $_REQUEST["aid"])
	    {
	        $article = new Article((int)$_REQUEST["aid"]);
	        $tmp_order = new Order((int)$_REQUEST["orderid"]);
	        
	        $article->deltePriceSeperations();
	        $article->delteCostSeperations();
	        
            $orderamounts = Array();
            foreach (Calculation::getAllCalculations($tmp_order) as $tmp_calc)
            {
                if ($tmp_calc->getState())
                {
                    $article->savePrice($tmp_calc->getAmount(), $tmp_calc->getAmount(), $tmp_calc->getSummaryPrice());
                    $article->saveCost($tmp_calc->getAmount(), $tmp_calc->getAmount(), $tmp_calc->getSubTotal());
                    $orderamounts[] = $tmp_calc->getAmount();
                }
            }
            $article->setOrderamounts($orderamounts);
	        $savemsg = getSaveMessage($article->save()).$DB->getLastError();
            
	        require_once 'article.edit.php';
	    }
	    break;
	default:
		require_once 'article.overview.php';
		break;
}
?>