<?

//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       19.03.2012
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
require_once 'libs/modules/calculation/calculation.position.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$_REQUEST["exec"] = trim(addslashes($_REQUEST["exec"]));

// search Customer
if ($_REQUEST["exec"] == "searchCust") {
    $_REQUEST["str"] = trim(addslashes($_REQUEST["str"]));

    $customers = BusinessContact::getAllBusinessContactsForLists(BusinessContact::ORDER_NAME, BusinessContact::FILTER_CUST);

    foreach ($customers as $c) {
        if (stripos($c->getNameAsLine(), $_REQUEST["str"]) !== false || stripos($c->getAddressAsLine(), $_REQUEST["str"]) !== false) {
            echo '<option value="' . $c->getId() . '">' . $c->getNameAsLine() . '</option>';
        }
    }
}

// update Foldtypes
if ($_REQUEST["exec"] == "updateFoldtypes") {

    $_REQUEST["pages"] = (int) $_REQUEST["pages"];
    $firstId = true;
    echo '<input name="h_envpages" value="' . $_REQUEST["id"] . '" type="hidden">';
    foreach (Foldtype::getFoldTypesForPages($_REQUEST["pages"]) as $ft) {
        echo '<input type="button" class="btn btn-default btn-info" id="12_' . $ft->getId() . '" name="foldtype" value="' . $ft->getName() . '"
				onclick="clickFolding(this.id)">'."\n";
    }

}

// update Formats
if ($_REQUEST["exec"] == "updateFormats") {
    if ($_REQUEST["id"] != "frei") {
        $_REQUEST["id"] = (int) $_REQUEST["id"];
        $pf = new Paperformat($_REQUEST["id"]);
        echo $pf->getWidth() . '_' . $pf->getHeight();
    }
}

// updatePaperProps
if ($_REQUEST["exec"] == "updatePaperpropsSize") {
    $_REQUEST["id"] = (int) $_REQUEST["id"];
    $_REQUEST["product"] = (int) $_REQUEST["product"];
    $retval = "";
    $paper = new Paper($_REQUEST["id"]);
    foreach ($paper->getSizes() as $size)
        $retval = $size["width"] . "x" . $size["height"] . "_";

    $retval = substr($retval, 0, -1);
    echo $retval;
}

if ($_REQUEST["exec"] == "updatePaperpropsWeight") {
    $_REQUEST["id"] = (int) $_REQUEST["id"];
    $_REQUEST["product"] = (int) $_REQUEST["product"];

    $prod = new Product($_REQUEST["product"]);
    $papers = $prod->getSelectedPapersIds();
    $paper = $papers[$_REQUEST["id"]];
    $retval = "";

    foreach (array_keys($paper) as $weight) {
        if ($weight != "id")
            $retval .= $weight . "_";
    }
    $retval = substr($retval, 0, -1);
    echo $retval;
}

if ($_REQUEST["exec"] == "addMachineRow") {
    $x = (int) $_REQUEST["idx"];
    $group = (int) $_REQUEST["group"];
    $_REQUEST["orderId"] = (int) $_REQUEST["orderId"];
    $order = new Order($_REQUEST["orderId"]);
    $calc = new Calculation((int)$_REQUEST["calcId"]);

    $groupmachs = Array();
    foreach ($order->getProduct()->getMachines() as $m)
        if ($m->getGroup()->getId() == $group)
            $groupmachs[] = $m;



    echo '<tr id="tr_mach_'.$x.'">';
    echo '<td>';
    echo '<input type="hidden" name="mach_group_'.$x.'" value="'.$group.'">';
    echo '<select name="mach_id_'.$x.'" class="form-control" id="mach_id_'.$x.'" onchange="updateMachineProps('.$x.', this.value)">';
    echo '<option value=""></option>';
    foreach ($groupmachs as $gm)
    {
        echo '<option value="'.$gm->getId().'" ';
        echo '>'.$gm->getName().'</option>';

    }
    echo '</select>';
    echo '</td>';
    echo '<td><input class="form-control" name="mach_time_'.$x.'" value="" placeholder="Min."></td>';
    echo '<td id="td-machparts-'.$x.'"></td>';
    echo '<td id="td-machopts-'.$x.'">';
    echo '<select name="mach_part_'.$x.'" style="display:none"></select>';
    echo '</td>';
    echo '<td id="td-papersize-'.$x.'">';
    echo '</td>';
    echo '<td id="td-cost-'.$x.'">';
    echo '</td>';
    echo '<td>';
    echo '<span class="glyphicons glyphicons-plus-sign pointer" onclick="addRow('.$group.')"></span>';
    echo '<span class="glyphicons glyphicons-plus-sign pointer" onclick="deleteRow('.$x.')"></span> ';
    echo '</td>';
    echo '</tr>';
}


if ($_REQUEST["exec"] == "calculateArticlePrice"){
	$calc = new Calculation((int)$_REQUEST["calcId"]);
	$article = new Article((int)$_REQUEST["artId"]);
	$artamount = (float)$_REQUEST["amount"];
	$scale = (int)$_REQUEST["scale"];
	
	if ($scale ==0){
		$newprice = $artamount * $article->getPrice($artamount);
	} elseif ($scale==1){
		$newprice = $artamount * $article->getPrice($artamount * $calc->getAmount()) * $calc->getAmount();
	}
	
	if ($newprice>0){
		echo printPrice($newprice);
		echo " ".$_USER->getClient()->getCurrency();
	} else {
		echo "- - -";
	}
}

if ($_REQUEST["exec"] == "updatePossibleParts") {
    $x = (int)$_REQUEST["idx"];
    $mach = new Machine((int)$_REQUEST["machId"]);
    $calc = new Calculation((int)$_REQUEST["calcId"]);
    $contents = $calc->getDetails();

    if( $mach->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ||
        $mach->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET ||
        $mach->getType() == Machine::TYPE_FOLDER ||
        $mach->getType() == Machine::TYPE_CUTTER ||
        $mach->getType() == Machine::TYPE_LASERCUTTER)
    {
        echo '<div class="form-group">';
        echo '<select name="mach_part_'.$x.'" id="mach_part_'.$x.'" onchange="updateAvailPapers('.$x.')" class="form-control">';
        foreach ($contents as $item) {
            echo '<option value="'.$item['paper'].'" ';
            if($partId == $item['paper']) echo 'selected';
            echo '>'.$item['name'].'</option>';
        }
        echo '</select></div>';
    }
}

if ($_REQUEST["exec"] == "updateMachineProps") {
    $x = (int) $_REQUEST["idx"];
    $mach = new Machine((int) $_REQUEST["machId"]);
    $calc = new Calculation((int)$_REQUEST["calcId"]);
    $partId = (int)$_REQUEST["partId"];

    // Falls Maschine manuell berechnet wird, Feld anzeigen
    if($mach->getPriceBase() == Machine::PRICE_VARIABEL)
    {
        echo '<div class="col-md-4"><div class="form-group">';
        echo '<label class="control-label">Preis</label><div class="input-group">';
        echo '<input name="mach_manprice_'.$x.'" value="'.printPrice($mach->getPrice()).'" class="form-control">';
        echo '<span class="input-group-addon">€</span></div></div></div>';
    }

    // Option Lack
    if($mach->getFinish())
    {
        $finishings = Finishing::getAllFinishings();
        echo '<div class="col-md-4"><div class="form-group">';
        echo '<label class="control-label">Lack</label><div class="input-group">';
        echo '<select name="mach_finishing_'.$x.'" id="mach_finishing_'.$x.'" class="form-control">';
        echo '<option value="0">'.$_LANG->get('kein Lack').'</option>';
        foreach($finishings as $f)
        {
            echo '<option value="'.$f->getId().'" ';
            echo '>'.$f->getName().'</option>';
        }
        echo '</select>';
        echo '</div></div></div>';
    }


    // Umschlagen / Umstuelpen
    if ($mach->getUmschlUmst() > 0)
    {
        echo '<div class="col-md-4"><div class="form-group">';
        echo '<div class="checkbox"><label>';
        echo '<input type="checkbox" onchange="switchUmschUmst('.$x.');" id="umschl_'.$x.'" name="umschl_'.$x.'" value="1">';
        echo ' Umschlagen</label></div></div></div>';

        echo '<div class="col-md-4"><div class="form-group">';
        echo '<div class="checkbox"><label>';
        echo '<input type="checkbox" onchange="switchUmschUmst('.$x.');" id="umst_'.$x.'" name="umst_'.$x.'" value="1">';
        echo ' Umst&uuml;lpen</label></div></div></div>';
        echo '<input type="hidden" id="umschl_umst_'.$x.'" name="umschl_umst_'.$x.'" value="0">';
    }
    
    // Falls Sammelhefter -> Falzbogenschema auswaehlen
    if($mach->getType() == Machine::TYPE_SAMMELHEFTER)
    {
        $schemes = $calc->getAvailableFoldschemes();

        if($calc->getPagesContent()){
            echo '<div class="col-md-4"><div class="form-group">';
            echo '<label class="control-label">Inhalt 1</label><div class="input-group">';
            echo '<select name="foldscheme_content" class="form-control">';
            foreach($schemes[1] as $scheme)
            {
                $str = '';
                if($scheme[16])
                    $str .= $scheme[16]." x 16, ";
                if($scheme[8])
                    $str .= $scheme[8]." x 8, ";
                if($scheme[4])
                    $str .= $scheme[4]." x 4, ";

                $str = substr($str, 0, -2);
                $val = preg_replace("/ /", '', $str);

                echo '<option value="'.$val.'" ';
                if($calc->getFoldschemeContent() == $val) echo "selected";
                echo '>'.$str.'</option>';
            }
            echo '</select>';
            echo '</div></div></div>';
        }
        if($calc->getPagesAddContent()){
            echo '<div class="col-md-4"><div class="form-group">';
            echo '<label class="control-label">Inhalt 2</label><div class="input-group">';
            echo '<select name="foldscheme_addcontent" class="form-control">';
            foreach($schemes[2] as $scheme)
            {
                $str = '';
                if($scheme[16])
                    $str .= $scheme[16]." x 16, ";
                if($scheme[8])
                    $str .= $scheme[8]." x 8, ";
                if($scheme[4])
                    $str .= $scheme[4]." x 4, ";

                $str = substr($str, 0, -2);
                $val = preg_replace("/ /", '', $str);

                echo '<option value="'.$val.'" ';
                if($calc->getFoldschemeAddContent() == $val) echo "selected";
                echo '>'.$str.'</option>';
            }
            echo '</select>';
            echo '</div></div></div>';
        }
        if($calc->getPagesAddContent2()){
            echo '<div class="col-md-4"><div class="form-group">';
            echo '<label class="control-label">Inhalt 3</label><div class="input-group">';
            echo '<select name="foldscheme_addcontent2" class="form-control">';
            foreach($schemes[4] as $scheme)
            {
                $str = '';
                if($scheme[16])
                    $str .= $scheme[16]." x 16, ";
                if($scheme[8])
                    $str .= $scheme[8]." x 8, ";
                if($scheme[4])
                    $str .= $scheme[4]." x 4, ";

                $str = substr($str, 0, -2);
                $val = preg_replace("/ /", '', $str);

                echo '<option value="'.$val.'" ';
                if($calc->getFoldschemeAddContent2() == $val) echo "selected";
                echo '>'.$str.'</option>';
            }
            echo '</select>';
            echo '</div></div></div>';
        }
        if($calc->getPagesAddContent3()){
            echo '<div class="col-md-4"><div class="form-group">';
            echo '<label class="control-label">Inhalt 3</label><div class="input-group">';
            echo '<select name="foldscheme_addcontent3" class="form-control">';
            foreach($schemes[5] as $scheme)
            {
                $str = '';
                if($scheme[16])
                    $str .= $scheme[16]." x 16, ";
                if($scheme[8])
                    $str .= $scheme[8]." x 8, ";
                if($scheme[4])
                    $str .= $scheme[4]." x 4, ";

                $str = substr($str, 0, -2);
                $val = preg_replace("/ /", '', $str);

                echo '<option value="'.$val.'" ';
                if($calc->getFoldschemeAddContent3() == $val) echo "selected";
                echo '>'.$str.'</option>';
            }
            echo '</select>';
            echo '</div></div></div>';
        }
    
    }
}

// Bogenformat Auswahl updaten
if($_REQUEST["exec"] == "updateAvailPapers")
{
    $x = (int) $_REQUEST["idx"];
    $mach = new Machine((int) $_REQUEST["machId"]);
    $partId = (int)$_REQUEST["partId"];
    $calc = new Calculation((int)$_REQUEST["calcId"]);
    
    if($mach->getType() == Machine::TYPE_DRUCKMASCHINE_DIGITAL ||$mach->getType() == Machine::TYPE_DRUCKMASCHINE_OFFSET)
    {
        echo '<div class="form-group">';
        if ($partId == Calculation::PAPER_CONTENT)
            $sizes = $calc->getPaperContent()->getAvailablePaperSizesForMachine($mach, $calc->getProductFormatWidthOpen(), $calc->getProductFormatHeightOpen(), $calc->getPaperContent()->getRolle(), $calc->getProductFormatHeightOpen());
        else if ($partId == Calculation::PAPER_ADDCONTENT)
            $sizes = $calc->getPaperAddContent()->getAvailablePaperSizesForMachine($mach, $calc->getProductFormatWidthOpen(), $calc->getProductFormatHeightOpen(), $calc->getPaperAddContent()->getRolle(), $calc->getProductFormatHeightOpen());
        else if ($partId == Calculation::PAPER_ENVELOPE)
            $sizes = $calc->getPaperEnvelope()->getAvailablePaperSizesForMachine($mach, $calc->getEnvelopeWidthOpen(), $calc->getEnvelopeHeightOpen(), $calc->getPaperEnvelope()->getRolle(), $calc->getProductFormatHeightOpen());
        else if ($partId == Calculation::PAPER_ADDCONTENT2)
            $sizes = $calc->getPaperAddContent2()->getAvailablePaperSizesForMachine($mach, $calc->getProductFormatWidthOpen(), $calc->getProductFormatHeightOpen(), $calc->getPaperAddContent2()->getRolle(), $calc->getProductFormatHeightOpen());
        else if ($partId == Calculation::PAPER_ADDCONTENT3)
            $sizes = $calc->getPaperAddContent3()->getAvailablePaperSizesForMachine($mach, $calc->getProductFormatWidthOpen(), $calc->getProductFormatHeightOpen(), $calc->getPaperAddContent3()->getRolle(), $calc->getProductFormatHeightOpen());

        echo '<select name="mach_papersize_' . $x . '" class="form-control">';
        foreach ($sizes as $s) {
            echo '<option value="' . $s["width"] . 'x' . $s["height"] . '" ';
            if ($partId == Calculation::PAPER_CONTENT)
                if ($s["width"] . 'x' . $s["height"] == $calc->getPaperContentWidth() . 'x' . $calc->getPaperContentHeight()) echo 'selected';
            if ($partId == Calculation::PAPER_ADDCONTENT)
                if ($s["width"] . 'x' . $s["height"] == $calc->getPaperAddContentWidth() . 'x' . $calc->getPaperAddContentHeight()) echo 'selected';
            if ($partId == Calculation::PAPER_ENVELOPE)
                if ($s["width"] . 'x' . $s["height"] == $calc->getPaperEnvelopeWidth() . 'x' . $calc->getPaperEnvelopeHeight()) echo 'selected';
            if ($partId == Calculation::PAPER_ADDCONTENT2)
                if ($s["width"] . 'x' . $s["height"] == $calc->getPaperAddContent2Width() . 'x' . $calc->getPaperAddContent2Height()) echo 'selected';
            if ($partId == Calculation::PAPER_ADDCONTENT3)
                if ($s["width"] . 'x' . $s["height"] == $calc->getPaperAddContent3Width() . 'x' . $calc->getPaperAddContent3Height()) echo 'selected';
            echo '>';
            echo $s["width"] . ' x ' . $s["height"] . '</option>';
        }
        echo '</select>';
        echo '</div>';
    }
    echo '&nbsp;';
}

// Ueberpruefen, ob eine Maschine den Inhalt (und dessen Farbeinstellung) auch verarbeiten kann
if ($_REQUEST["exec"] == "checkMashineContentCombination"){
	$x = (int) $_REQUEST["idx"];
	$mach = new Machine((int) $_REQUEST["machId"]);
	$partId = (int)$_REQUEST["partId"];
	$calc = new Calculation((int)$_REQUEST["calcId"]);
	$color_possible = false;
	$chromes = $mach->getChromaticities();
	if ($partId == 1){
		foreach($chromes as $chrome){
			if ($chrome->getId() == $calc->getChromaticitiesContent()->getId()){
				$color_possible = true;
			}
		}
	}
	if ($partId == 2){
		foreach($chromes as $chrome){
			if ($chrome->getId() == $calc->getChromaticitiesAddContent()->getId()){
				$color_possible = true;
			}
		}
	}
	if ($partId == 3){
		foreach($chromes as $chrome){
			if ($chrome->getId() == $calc->getChromaticitiesEnvelope()->getId()){
				$color_possible = true;
			}
		}
	}
	if ($partId == 4){
		foreach($chromes as $chrome){
			if ($chrome->getId() == $calc->getChromaticitiesAddContent2()->getId()){
				$color_possible = true;
			}
		}
	}
	if ($partId == 5){
		foreach($chromes as $chrome){
			if ($chrome->getId() == $calc->getChromaticitiesAddContent3()->getId()){
				$color_possible = true;
			}
		}
	}
	echo $color_possible;
}

if ($_REQUEST["exec"] == "calcOpenFormat") {
    $_REQUEST["foldid"] = (int) $_REQUEST["foldid"];
    $_REQUEST["height"] = (int) $_REQUEST["height"];
    $_REQUEST["width"] = (int) $_REQUEST["width"];
	$_REQUEST["fWidth"] = (int) $_REQUEST["fWidth"];
	$_REQUEST["fHeight"] = (int) $_REQUEST["fHeight"];

	
	if($_REQUEST["foldid"] != 0)
	{
		$foldtype = new Foldtype($_REQUEST["foldid"]);
		$newWidth = $_REQUEST["width"] * ($foldtype->getVertical() + 1);
		$newHeight = $_REQUEST["height"] * ($foldtype->getHorizontal() + 1);
	} else
	{
		$newWidth = $_REQUEST["width"] * $_REQUEST["fWidth"];
		$newHeight = $_REQUEST["height"] * $_REQUEST["fHeight"];
	}
    echo $newWidth . '_' . $newHeight;
}

if ($_REQUEST["exec"] == "numberPages") {

    //$order = new Order((int)$_REQUEST["orderId"]);    
    $product = new Product((int)$_REQUEST["product"]);
    
    if($product->getType() == Product::TYPE_NORMAL){
        foreach ($product->getAvailablePageCounts() as $pc) {
            echo '<input type="button" style="margin: 5px;"  class="btn btn-default btn-info" id="04_' . $pc . '" name="numberpages_content" value="' . $pc . ' ' . $_LANG->get('Seiten') . '"
    				onclick="clickContentPages(this.id)">'."\n";
        }
    }
    else
        echo '<input name="numberpages_content" value="" style="width:60px" class="text" onfocus="focusContentPages()" onblur="setContentPages(this.value)">';
}

if ($_REQUEST["exec"] == "printChrom") {

    $product = new Product((int)$_REQUEST["product"]);
    $pages = (int)$_REQUEST["id"];

    if (count($product->getAvailableChromaticities())>0){
        $chromas = $product->getAvailableChromaticities();
    } else {
        $chromas = Chromaticity::getAllChromaticities();
    }

    foreach ($chromas as $pc) {
        if($pages == 1)
        {
            if(!$pc->getReversePrinting())
                echo '<input type="button" style="margin: 2px;"  class="btn btn-default btn-info" id="05_' . $pc->getId() . '" name="chroma" value="' . $pc->getName() . '"
                    onclick="clickContentChromaticity(this.id)">'."\n";
                
        } else 
        {
            echo '<input type="button" style="margin: 2px;"  class="btn btn-default btn-info" id="05_' . $pc->getId() . '" name="chroma" value="' . $pc->getName() . '"
            onclick="clickContentChromaticity(this.id)">'."\n";
            
        }
    }
}


if ($_REQUEST["exec"] == "updatePaperprops") {

    $prod = new Product($_REQUEST["product"]);
    echo '<input name="h_endformat" value="' . $_REQUEST["id"] . '" type="hidden">';

    foreach ($prod->getSelectedPapersIds(Calculation::PAPER_CONTENT) as $paperId) {
        $paper = new Paper($paperId["id"]);
        echo '<input type="button" style="margin: 2px;" class="btn btn-default btn-info" id="02_' . $paperId["id"] . '" name="paperprops" value="' . $paper->getName() . '"
				onclick="clickPaperContent(this.id)">'."\n";
    }
}


if ($_REQUEST["exec"] == "updatePaperFree") {
    
 $calc = new Calculation($_REQUEST[orderId]);
    $prod = new Product($_REQUEST["product"]);
echo '
    <td class="content_row_clear">
                      <div id="paper_free"></div>'.$_LANG->get('Breite').'
 
                      <input name="order_product_width" style="width:40px;text-align:center" class="text" id="order_product_width" 
                          value="'.$calc->getProductFormatWidth().'" onchange="calcOpenFormat()"> '.$_LANG->get('mm').'
'. $_LANG->get('H&ouml;he').'
                      <input name="order_product_height" style="width:40px;text-align:center" class="text" id="order_product_height" 
                          value="'. $calc->getProductFormatHeight().'" onchange="calcOpenFormat()"> '.$_LANG->get('mm').'<br>
'.$_LANG->get('Breite').'
                      <input name="order_product_width_open" style="width:40px;text-align:center" class="text" id="order_product_width_open" 
                          value="'.$calc->getProductFormatWidthOpen().'"> '.$_LANG->get('mm').'
'.$_LANG->get('H&ouml;he').'
                      <input name="order_product_height_open" style="width:40px;text-align:center" class="text" id="order_product_height_open" 
                          value="'.$calc->getProductFormatHeightOpen().'"> '.$_LANG->get('mm').'
'.$_LANG->get('offenes Format').'
                  </td>';
}

if ($_REQUEST["exec"] == "updatePaperWeight") {

//    $_REQUEST["id"] = (int) $_REQUEST["id"];
    $_REQUEST["product"] = (int) $_REQUEST["product"];
    $_REQUEST["oldval"] = (int) $_REQUEST["oldval"];

    $prod = new Product($_REQUEST["product"]);
    $papers = $prod->getSelectedPapersIds(Calculation::PAPER_CONTENT);
    $paper = $papers[$_REQUEST["id"]];

    foreach (array_keys($paper) as $weight) {
        if ($weight != "id") {

            echo '<input type="button" style="margin: 2px;" class="btn btn-default btn-info" id="03_' . $weight . '" name="paperweight" value="' . $weight . ''.$_LANG->get('g').'"
				onclick="clickContentWeight(this.id)"'; if($weight == $_REQUEST["oldval"]) echo ' '; echo'>'."\n";
        }
    }
}


if ($_REQUEST["exec"] == "updateAddedPaper") {
    $prod = new Product($_REQUEST["product"]);
    echo '<input type="button" style="margin: 2px;" class="btn btn-default btn-info" id="06_0" name="addpaper" value="' . $_LANG->get('nicht vorhanden') . '"
				onclick="clickAddPaper(this.id)"';  if($_REQUEST["hiddenAddPaper"] == 0) echo ''; echo'>'."\n";
    foreach ($prod->getSelectedPapersIds(Calculation::PAPER_CONTENT) as $paperId) {
        $paper = new Paper($paperId["id"]);
        echo '<input type="button" style="margin: 2px;"  class="btn btn-default btn-info" id="06_' . $paperId["id"] . '" name="addpaper" value="' . $paper->getName() . '"
				onclick="clickAddPaper(this.id)">'."\n";
    }
}


if ($_REQUEST["exec"] == "updateAddPaperWeight") {

    //$_REQUEST["id"] = (int) $_REQUEST["id"];
    $_REQUEST["product"] = (int) $_REQUEST["product"];
    $_REQUEST["oldval"] = (int)$_REQUEST["oldval"];

    $prod = new Product($_REQUEST["product"]);
    $papers = $prod->getSelectedPapersIds(Calculation::PAPER_CONTENT);
    $paper = $papers[$_REQUEST["id"]];

    foreach (array_keys($paper) as $weight) {
        if ($weight != "id") {

            echo '<input type="button" style="margin: 2px;"  class="btn btn-default btn-info" id="07_' . $weight . '" name="addpaperweight" value="' . $weight . ' '.$_LANG->get('g').'"
				onclick="clickAddPaperWeight(this.id)"'; if($weight == $_REQUEST["oldval"]) echo ' '; echo '>'."\n";
        }
    }
}


if ($_REQUEST["exec"] == "updateAddPaperChroma") {

    $order = new Order((int)$_REQUEST[orderId]);

    if (count($order->getProduct()->getAvailableChromaticities())>0){
        $chromas = $order->getProduct()->getAvailableChromaticities();
    } else {
        $chromas = Chromaticity::getAllChromaticities();
    }

    $prod = new Product($_REQUEST["product"]);
    foreach ($chromas as $pc) {
        echo '<input type="button" style="margin: 2px;"  class="btn btn-default btn-info" id="14_' . $pc->getId() . '" name="addpaperchroma" value="' . $pc->getName() . '"
				onclick="clickAddPaperChromaticity(this.id)">'."\n";
    }   
}

if ($_REQUEST["exec"] == "updateAddPaperPages") {

    $order = new Order((int)$_REQUEST[orderId]);
    $prod = new Product($_REQUEST["product"]);

    if($prod->getType() == Product::TYPE_NORMAL)
        foreach ($prod->getAvailablePageCounts() as $pc) {
            echo '<input type="button" style="margin: 2px;"  class="btn btn-default btn-info" id="08_' . $pc . '" name="addpaperpages" value="' . $pc . ' ' . $_LANG->get('Seiten') . '"
    				onclick="clickAddPaperPages(this.id)">'."\n";
        }
    else
        echo '<input name="numberpages_addcontent" value="" style="width:60px" class="text"
        onfocus="focusAddContentPages()" onblur="setAddContentPages(this.value)">';
            
}



if ($_REQUEST["exec"] == "updateEnvPaper") {

    $prod = new Product($_REQUEST["product"]);
    echo '<input type="button" style="margin: 2px;"  class="btn btn-default btn-info" id="09_0"  name="envpaper" value="' . $_LANG->get('nicht vorhanden') . '"
				onclick="clickEnvelopePaper(this.id)"'; if($_REQUEST["hiddenEnvPaper"] == 0) echo ' '; echo'>'."\n";
    foreach ($prod->getSelectedPapersIds(Calculation::PAPER_ENVELOPE) as $paperId) {
        $paper = new Paper($paperId["id"]);
        echo '<input type="button" style="margin: 2px;"  class="btn btn-default btn-info" id="09_' . $paperId["id"] . '" name="envpaper" value="' . $paper->getName() . '"
				onclick="clickEnvelopePaper(this.id)">'."\n";
    }
}


if ($_REQUEST["exec"] == "updateEnvPaperWeight") {

    $_REQUEST["id"] = (int) $_REQUEST["id"];
    $_REQUEST["product"] = (int) $_REQUEST["product"];

    $prod = new Product($_REQUEST["product"]);
    $papers = $prod->getSelectedPapersIds(Calculation::PAPER_ENVELOPE);
    $paper = $papers[$_REQUEST["id"]];
    foreach (array_keys($paper) as $weight) {
        if ($weight != "id") {

            echo '<input type="button" style="margin: 2px;" class="btn btn-default btn-info" id="10_' . $weight . '" name="envpaperweight" value="' . $weight . ' '.$_LANG->get('g').'"
				onclick="clickEnvelopeWeight(this.id)"'; if($weight == $_REQUEST["oldval"]) echo ' '; echo '>'."\n";
        }
    }
}


if ($_REQUEST["exec"] == "updateEnvPaperChroma") {

    $order = new Order((int)$_REQUEST[orderId]);

    if (count($order->getProduct()->getAvailableChromaticities())>0){
        $chromas = $order->getProduct()->getAvailableChromaticities();
    } else {
        $chromas = Chromaticity::getAllChromaticities();
    }

    $prod = new Product($_REQUEST["product"]);
    
    foreach ($chromas as $pc) {
        echo '<input type="button" style="margin: 2px;"  class="btn btn-default btn-info" id="15_' . $pc->getId() . '" name="envpaperchroma" value="' . $pc->getName() . '"
				onclick="clickEnvelopeChromaticity(this.id)">'."\n";
    }
    
}

if ($_REQUEST["exec"] == "updateEnvPaperPages") {
    
    
     $order = new Order((int)$_REQUEST[orderId]);
    
     $pages = array("2", "4", "6", "8");
     foreach ($pages as $pc) {
         echo '<input type="button" style="margin: 2px;"  class="btn btn-default btn-info" id="11_' . $pc . '" name="envpaperpages" value="' . $pc . ' ' . $_LANG->get('Seiten') . '"
				onclick="clickEnvelopePages(this.id)">'."\n";
     }
}


if ($_REQUEST["exec"] == "updateOrderAmount") {
    echo '<input name="order_amount" id="13_0" style="width:60px" class="text" value="" onchange="">';

    
}

if($_REQUEST["exec"] == "getDeliveryCost")
{
    $dt = new DeliveryTerms((int)$_REQUEST["id"]);
    echo printPrice($dt->getCharges());
}

if($_REQUEST["exec"] == "getReversePrinting")
{
    $chroma = new Chromaticity((int)$_REQUEST["chromaId"]);
    echo $chroma->getReversePrinting();
}


// -------------------- Funktionen fuer den zus. Inhalt 2 -------------------------------

/*
 * Papiere fuer den Inhalt 2 auflisten
 */
if ($_REQUEST["exec"] == "updateAdded2Paper") {

	$prod = new Product($_REQUEST["product"]);
	echo '<input type="button" style="margin: 2px;"  class="btn btn-default btn-info" id="20_0" name="add2paper" value="' . $_LANG->get('nicht vorhanden') . '"
				onclick="clickAdd2Paper(this.id)"';  if($_REQUEST["hiddenAddPaper"] == 0) echo ' '; echo'>'."\n";
	foreach ($prod->getSelectedPapersIds(Calculation::PAPER_CONTENT) as $paperId) {
		$paper = new Paper($paperId["id"]);
		echo '<input type="button" style="margin: 2px;"  class="btn btn-default btn-info" id="20_' . $paperId["id"] . '" name="add2paper" value="' . $paper->getName() . '"
				onclick="clickAdd2Paper(this.id)">'."\n";
	}
}

/*
 * Gewichte fuer das Papier des zus. Inhalts 2 auflisten 
 */
if ($_REQUEST["exec"] == "updateAdd2PaperWeight") {

	//$_REQUEST["id"] = (int) $_REQUEST["id"];
	$_REQUEST["product"] = (int) $_REQUEST["product"];
	$_REQUEST["oldval"] = (int)$_REQUEST["oldval"];

	$prod = new Product($_REQUEST["product"]);
	$papers = $prod->getSelectedPapersIds(Calculation::PAPER_CONTENT); // PAPER_CONTENT lassen, da Papier fuer Inhalt + zus. Inhalte gilt
	$paper = $papers[$_REQUEST["id"]];

	foreach (array_keys($paper) as $weight) {
		if ($weight != "id") {

			echo '<input type="button" style="margin: 2px;"  class="btn btn-default btn-info" id="21_' . $weight . '" name="add2paperweight" value="' . $weight . ' '.$_LANG->get('g').'"
				onclick="clickAdd2PaperWeight(this.id)"'; if($weight == $_REQUEST["oldval"]) echo ' '; echo '>'."\n";
		}
	}
}

/*
 * Farben fuer den zus. Inhalt 2 auflisten
 */
if ($_REQUEST["exec"] == "updateAdd2PaperChroma") {

    $order = new Order((int)$_REQUEST[orderId]);

    if (count($order->getProduct()->getAvailableChromaticities())>0){
        $chromas = $order->getProduct()->getAvailableChromaticities();
    } else {
        $chromas = Chromaticity::getAllChromaticities();
    }

	$prod = new Product($_REQUEST["product"]);
	foreach ($chromas as $pc) {
		echo '<input type="button" style="margin: 2px;"  class="btn btn-default btn-info" id="22_' . $pc->getId() . '" name="add2paperchroma" value="' . $pc->getName() . '"
				onclick="clickAdd2PaperChromaticity(this.id)">'."\n";
	}
}

/*
 * Buttons fuer die Seitenauswahl generieren oder Eingabefeld anzeigen, jenachdem, was im Produkt ausgwaehlt ist
 */
if ($_REQUEST["exec"] == "updateAdd2PaperPages") {

	$prod = new Product($_REQUEST["product"]);

	if($prod->getType() == Product::TYPE_NORMAL)
		foreach ($prod->getAvailablePageCounts() as $pc) {
		echo '<input type="button" style="margin: 2px;"  class="btn btn-default btn-info" id="23_' . $pc . '" name="add2paperpages" value="' . $pc . ' ' . $_LANG->get('Seiten') . '"
    				onclick="clickAdd2PaperPages(this.id)">'."\n";
	}
	else
		echo '<input name="numberpages_addcontent2" value="" style="width:60px" class="text"
        onfocus="focusAddContent2Pages()" onblur="setAddContent2Pages(this.value)">';

}

// -------------------- Funktionen fuer den zus. Inhalt 3 -------------------------------

/*
 * Papiere fuer den Inhalt 2 auflisten
*/
if ($_REQUEST["exec"] == "updateAdded3Paper") {

	$prod = new Product($_REQUEST["product"]);
	echo '<input type="button" style="margin: 2px;" class="btn btn-default btn-info" id="30_0" name="add3paper" value="' . $_LANG->get('nicht vorhanden') . '"
				onclick="clickAdd3Paper(this.id)"';  if($_REQUEST["hiddenAddPaper"] == 0) echo ' '; echo'>'."\n";
	foreach ($prod->getSelectedPapersIds(Calculation::PAPER_CONTENT) as $paperId) {
		$paper = new Paper($paperId["id"]);
		echo '<input type="button" style="margin: 2px;"  class="btn btn-default btn-info" id="30_' . $paperId["id"] . '" name="add3paper" value="' . $paper->getName() . '"
				onclick="clickAdd3Paper(this.id)">'."\n";
	}
}

/*
 * Gewichte fuer das Papier des zus. Inhalts 3 auflisten
*/
if ($_REQUEST["exec"] == "updateAdd3PaperWeight") {

	//$_REQUEST["id"] = (int) $_REQUEST["id"];
	$_REQUEST["product"] = (int) $_REQUEST["product"];
	$_REQUEST["oldval"] = (int)$_REQUEST["oldval"];

	$prod = new Product($_REQUEST["product"]);
	$papers = $prod->getSelectedPapersIds(Calculation::PAPER_CONTENT); // PAPER_CONTENT lassen, da das Papier fuer Inhalt + zus. Inhalte gilt
	$paper = $papers[$_REQUEST["id"]];

	foreach (array_keys($paper) as $weight) {
		if ($weight != "id") {

			echo '<input type="button" style="margin: 2px;"  class="btn btn-default btn-info" id="31_' . $weight . '" name="add3paperweight" value="' . $weight . ' '.$_LANG->get('g').'"
					onclick="clickAdd3PaperWeight(this.id)"'; 
				if($weight == $_REQUEST["oldval"]) echo ' ';
			echo '>'."\n";
		}
	}
}

/*
 * Farben fuer den zus. Inhalt 3 auflisten
*/
if ($_REQUEST["exec"] == "updateAdd3PaperChroma") {

    $order = new Order((int)$_REQUEST[orderId]);

    if (count($order->getProduct()->getAvailableChromaticities())>0){
        $chromas = $order->getProduct()->getAvailableChromaticities();
    } else {
        $chromas = Chromaticity::getAllChromaticities();
    }

	$prod = new Product($_REQUEST["product"]);
	foreach ($chromas as $pc) {
		echo '<input type="button" style="margin: 2px;" class="btn btn-default btn-info" id="32_' . $pc->getId() . '" name="add3paperchroma" value="' . $pc->getName() . '"
				onclick="clickAdd3PaperChromaticity(this.id)">'."\n";
	}
}

/*
 * Buttons fuer die Seitenauswahl generieren oder Eingabefeld anzeigen, jenachdem, was im Produkt ausgwaehlt ist
*/
if ($_REQUEST["exec"] == "updateAdd3PaperPages") {

	$prod = new Product($_REQUEST["product"]);

	if($prod->getType() == Product::TYPE_NORMAL)
		foreach ($prod->getAvailablePageCounts() as $pc) {
		echo '<input type="button" style="margin: 2px;" class="btn btn-default btn-info" id="33_' . $pc . '" name="add3paperpages" value="' . $pc . ' ' . $_LANG->get('Seiten') . '"
    				onclick="clickAdd3PaperPages(this.id)">'."\n";
	}
	else
		echo '<input name="numberpages_addcontent3" value="" style="width:60px" class="text"
        onfocus="focusAddContent3Pages()" onblur="setAddContent3Pages(this.value)">';

}


/**************************** Details eines Artikels holen *************************************************/

if ($_REQUEST['exec'] == 'getArticleDetails'){
	$articleid = (int)$_REQUEST['articleid'];
	$article = new Article($articleid);
	$price = 0;
	$detailtext = "";

	$detailtext .= $article->getDesc()."\n";
	$price = $article->getPrice(1);
	$costs = $article->getCost(1);

	echo $article->getId()."-+-+-";
	echo $price."-+-+-";
	echo $article->getTax()."-+-+-";
	echo $costs."-+-+-";
	echo $detailtext;
}


/**************************** Staffelpreis eines Artikels holen ********************************************/

if ($_REQUEST['exec'] == 'getArticlePrice'){
	$articleid = (int)$_REQUEST['articleid'];
	$article = new Article($articleid);
	$amount = (int) $_REQUEST['amount'];
	$price = 0;
	$costs = 0;
	
	$price = (float)sprintf("%.2f",$article->getPrice($amount));
	$costs = (float)sprintf("%.2f",$article->getCost($amount));
	echo $price."-+-+-";
	echo $costs;
}


/**************************** Such nach Artikeln, Kalkulationen, Personalisiereungen, usw. *****************/

if ($_REQUEST["exec"] == "searchPositions") {
	$str = trim(addslashes($_REQUEST["str"]));
	$type = (int)$_REQUEST["type"];
	//$customerId = (int)$_REQUEST['cust_id'];

	/*if($type == CalculationPosition::TYPE_ORDER){
		$all_orders = Order::searchOrderByTitleNumber($str, $customerId);
		echo '<option value=""> &lt; '.$_LANG->get('Auftrag w&auml;hlen...').'&gt;</option>';
		foreach ($all_orders as $order) {
			echo '<option value="'. $order->getId() .'">'. $order->getNumber() ." - ". $order->getTitle() .'</option>';
		}
	}*/
	if($type == CalculationPosition::TYPE_ARTICLE){
		$all_article = Article::searchArticleByTitleNumber($str);
		echo '<option value=""> &lt; '.$_LANG->get('Artikel w&auml;hlen...').'&gt;</option>';
		foreach ($all_article as $article) {
			echo '<option value="'. $article->getId() .'">'. $article->getNumber()." - ".$article->getTitle() .'</option>';
		}
	}
	/*
	if($type == Orderposition::TYPE_PERSONALIZATION){
		$all_persos = Personalizationorder::getAllPersonalizationorders($customerId, Personalizationorder::ORDER_CRTDATE, true);

		echo '<option value=""> &lt; '.$_LANG->get('Bitte  w&auml;hlen...').'&gt;</option>';
		foreach ($all_persos AS $perso){
			echo '<option value="'.$perso->getId().'">'.$perso->getTitle().'</option>';
		}
	}*/
}

/**************************** Details einer Papierbestellung holen *************************************************/

if ($_REQUEST['exec'] == 'getPaperOrder'){
	$paperid = (int)$_REQUEST['paperid'];
	$order = new Order($_REQUEST['orderid']);

	$boegen = 0;
	$price = 0;
	foreach(Calculation::getAllCalculations($order) as $calc) {
		if ($calc->getState() == 1) {
			if($calc->getPaperContent()->getId() == $paperid) {
				$boegen += $calc->getPaperCount(Calculation::PAPER_CONTENT) + $calc->getPaperContentGrant();
				$price += $calc->getPaperContent()->getSumPrice($calc->getPaperCount(Calculation::PAPER_CONTENT) + $calc->getPaperContentGrant());
			}
			if($calc->getPaperAddContent()->getId() == $paperid) {
				$boegen += $calc->getPaperCount(Calculation::PAPER_ADDCONTENT) + $calc->getPaperAddContentGrant();
            	$price += $calc->getPaperAddContent()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT) + $calc->getPaperAddContentGrant());
			}
			if($calc->getPaperAddContent2()->getId() == $paperid) {
    			$boegen += $calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) + $calc->getPaperAddContent2Grant();
            	$price += $calc->getPaperAddContent2()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT2) + $calc->getPaperAddContent2Grant());
			}
			if($calc->getPaperAddContent3()->getId() == $paperid) {
	    		$boegen += $calc->getPaperCount(Calculation::PAPER_ADDCONTENT3) + $calc->getPaperAddContent3Grant();
	        	$price += $calc->getPaperAddContent3()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ADDCONTENT3) + $calc->getPaperAddContent3Grant());
			}
			if($calc->getPaperEnvelope()->getId() == $paperid) {
				$boegen += $calc->getPaperCount(Calculation::PAPER_ENVELOPE) + $calc->getPaperEnvelopeGrant();
				$price += $calc->getPaperEnvelope()->getSumPrice($calc->getPaperCount(Calculation::PAPER_ENVELOPE) + $calc->getPaperEnvelopeGrant());
			}
		}
	} 
	
	$boegen = printBigInt($boegen);
	$price = printPrice($price);
	$paper = new Paper($paperid);
	$paper_supplier = $paper->getSupplier();
	
	$supplier = Array();
	foreach ($paper_supplier as $s) {
		$tmp_busicon = new BusinessContact($s);
		$supplier[] = $tmp_busicon->getId() . "+|+" . $tmp_busicon->getNameAsLine();
	}

	echo $boegen."-+-+-";
	echo $price."-+-+-";
	echo json_encode($supplier);
}
?>