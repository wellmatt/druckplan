<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			17.09.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
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
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/collectiveinvoice/orderposition.class.php';
require_once 'libs/modules/personalization/personalization.order.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$_REQUEST["exec"] = trim(addslashes($_REQUEST["exec"]));

// Such nach Artikel oder Kalkulationen
if ($_REQUEST["exec"] == "searchPositions") {
	$str = trim(addslashes($_REQUEST["str"]));
	$type = (int)$_REQUEST["type"];
	$customerId = (int)$_REQUEST['cust_id'];

	if($type == 1){
		$all_orders = Order::searchOrderByTitleNumber($str, $customerId);
		echo '<option value=""> &lt; '.$_LANG->get('Auftrag w�hlen...').'&gt;</option>';							
		foreach ($all_orders as $order) {
			echo '<option value="'. $order->getId() .'">'. $order->getNumber() ." - ". $order->getTitle() .'</option>';
		}
	}
	if($type == 2){
		$all_article = Article::searchArticleByTitleNumber($str);
		echo '<option value=""> &lt; '.$_LANG->get('Artikel w�hlen...').'&gt;</option>';
		foreach ($all_article as $article) {
			echo '<option value="'. $article->getId() .'">'. $article->getNumber()." - ".$article->getTitle() .'</option>';
		}
	}
	if($type == Orderposition::TYPE_PERSONALIZATION){
		$all_persos = Personalizationorder::getAllPersonalizationorders($customerId, Personalizationorder::ORDER_CRTDATE, true);

		echo '<option value=""> &lt; '.$_LANG->get('Bitte  w&auml;hlen...').'&gt;</option>';
		foreach ($all_persos AS $perso){
			echo '<option value="'.$perso->getId().'">'.$perso->getTitle().'</option>';			// TODO TESTEN !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		}
	}
}

// Details eines Auftrags holen
if ($_REQUEST['exec'] == 'getOrderDetails'){
	$orderid = (int)$_REQUEST['orderid'];
	$order = new Order($orderid);
	$calcs = Calculation::getAllCalculations($order);
	$sum_price = 0;
	$detailtext = "";
	$i=1;
	
	$detailtext .= $_LANG->get('Produkt').": ".$order->getProduct()->getName()."\n";
	$detailtext .= $_LANG->get('Beschreibung').": ".$order->getProduct()->getDescription()."\n \n";
	$detailtext .= "------------------------- \n";
	foreach ($calcs as $calc){
		if ($calc->getState() > 0){ // nur rechnungsrelevante/beauftragte Kalkulationen verwenden
			$sum_price += $calc->getSummaryPrice();
			$detailtext .= $_LANG->get('Kalkulation')." ".$i.": \n";
			$detailtext .= " ".$_LANG->get('Format').": ".$calc->getProductFormat()->getName()."\n";
			$detailtext .= " ".$_LANG->get('Auflage').": ".$calc->getAmount()." ".$_LANG->get('Stk.')."\n \n";
			//Inhalt
			$detailtext .= $_LANG->get('Inhalt')." \n";
			$detailtext .= " ".$_LANG->get('Seiten').": ".$calc->getPagesContent()."\n";
			$detailtext .= " ".$_LANG->get('Papier').": ".$calc->getPaperContent()->getName();
			$detailtext .= " ".$_LANG->get('in')." ".$calc->getPaperContentWeight()." g \n";
			$detailtext .= " ".$_LANG->get('Farbigkeit').": ".$calc->getChromaticitiesContent()->getName()."\n";
			if($calc->getPaperAddContent()->getId() != 0){
				$detailtext .= "\n".$_LANG->get('Zus. Inhalt')." \n";
				$detailtext .= " ".$_LANG->get('Seiten').": ".$calc->getPagesAddContent()."\n";
				$detailtext .= " ".$_LANG->get('Papier').": ".$calc->getPaperAddContent()->getName();
				$detailtext .= " in ".$calc->getPaperAddContentWeight()." g \n";
				$detailtext .= " ".$_LANG->get('Farbigkeit').": ".$calc->getChromaticitiesAddContent()->getName()."\n";
			}
			if($calc->getPaperEnvelope()->getId() != 0){
				$detailtext .= "\n".$_LANG->get('Umschlag')." \n";
				$detailtext .= " ".$_LANG->get('Seiten').": ".$calc->getPagesEnvelope()."\n";
				$detailtext .= " ".$_LANG->get('Papier').": ".$calc->getPaperEnvelope()->getName();
				$detailtext .= " in ".$calc->getPaperEnvelopeWeight()." g \n";
				$detailtext .= " ".$_LANG->get('Farbigkeit').": ".$calc->getChromaticitiesEnvelope()->getName()."\n";
			}
			$detailtext .= "\n----- -----\n";
			$i++;
		}
	}
	$detailtext = substr( $detailtext, 0, -12);
	if($order->getTextInvoice()){
		$detailtext .= "\n------------------------- \n".$order->getTextInvoice();
	}
	
	echo $order->getId()."-+-+-";
	echo $sum_price."-+-+-";
	echo $order->getProduct()->getTaxes()."-+-+-";
	echo $detailtext;
}

// Details eines Artikels holen
if ($_REQUEST['exec'] == 'getArticleDetails'){
	$articleid = (int)$_REQUEST['articleid'];
	$article = new Article($articleid);
	$price = 0;
	$detailtext = "";

	$detailtext .= $article->getDesc()."\n";
	$price = $article->getPrice(1);

	echo $article->getId()."-+-+-";
	echo $price."-+-+-";
	echo $article->getTax()."-+-+-";
	echo $detailtext;
}

/**************************** Details eines Artikels holen *************************************************/

if ($_REQUEST['exec'] == 'getPersonalizationDetails'){
	$price = 0;
	$detailtext = "";
	
	$persoid = (int)$_REQUEST['persoid'];
	$persoorder = new Personalizationorder($persoid);

	$detailtext .= $persoorder->getTitle()." (".$persoorder->getAmount() ."Stk.) \n";
	$price = $persoorder->getPrice($persoorder->getAmount());

	echo $persoorder->getId()."-+-+-";
	echo $price."-+-+-";
	echo "19 -+-+-";				// MWST ist auf 19 festgesetzt, muesste mal Hinterlegt werden.
	echo $detailtext;
}
//Staffelpreis eines Artikels holen
if ($_REQUEST['exec'] == 'getArticlePrice'){
	$articleid = (int)$_REQUEST['articleid'];
	$article = new Article($articleid);
	$amount = (float) $_REQUEST['amount'];
	$price = 0;
	$price = (float)sprintf("%.2f",$article->getPrice($amount));
	echo $price;
}

//Staffelpreis eines Artikels holen
if ($_REQUEST['exec'] == 'getDeliveryPrice'){
	$del_term = new DeliveryTerms((int)$_REQUEST['delivid']);
	echo printPrice($del_term->getCharges());
}


if ($_REQUEST["ajax_action"] == "search_position"){
    $retval = Array();
    
    $allArticle = Article::getAllArticle(Article::ORDER_TITLE, " AND (title LIKE '%{$_REQUEST['term']}%' OR number LIKE '%{$_REQUEST['term']}%' OR matchcode LIKE '%{$_REQUEST['term']}%') ");
    foreach ($allArticle as $a){
        if ($a->getOrderid()>0)
            $type = 1;
        else
            $type = 2;
        $orderamounts = $a->getOrderamounts();
        $retval[] = Array("type"=> $type, "value" => $a->getId(), "label" => "Artikel: " . $a->getTradegroup()->getTitle() . ": " . $a->getTitle() . " (" . $a->getNumber() . ")", "orderamounts"=>$orderamounts, "orderid"=>$a->getOrderid());
    }
    
    $allPersos = Personalizationorder::getAllPersonalizationorders((int)$_REQUEST["bcid"],Personalizationorder::ORDER_TITLE,true);
    foreach ($allPersos as $a){
        $retval[] = Array("type"=> 3, "value" => $a->getId(), "label" => "Perso: " . $a->getTitle(), "orderamounts"=>null, "orderid"=>null);
    }
    
    $retval = json_encode($retval);
    header("Content-Type: application/json");
    echo $retval;
}
if ($_REQUEST["ajax_action"] == "getArtData"){
    $retval = Array();

    $tmp_art = new Article($_REQUEST["artid"]);
    $type = 2;
    if ($tmp_art->getOrderid()>0)
        $type = 1;
    $retval[] = Array("type"=>$type, "id"=>$tmp_art->getId(),"title"=>$tmp_art->getTitle(),"orderamounts"=>$tmp_art->getOrderamounts(),"orderid"=>$tmp_art->getOrderid());
    
    $retval = json_encode($retval);
    header("Content-Type: application/json");
    echo $retval;
}

?>

