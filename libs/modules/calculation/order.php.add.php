<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			11.12.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

// Zusaetzlicher Inhalt 2
if ($order->getProduct()->getHasAddContent2()) {?>
    <h1><?=$_LANG->get('zus. Inhalt 2')?></h1>
    <div class="box2">
        <table width="100%">
            <colgroup>
                <col width="180">
                <col>
            </colgroup>
                <tr>
                    <td class="content_row_header" style="color:gray;">
                    	<?= $_LANG->get('zus. Inhalt 2 - Material') ?>
                    </td>
                    <td class="content_row_clear">
                        <div id="additional2_paper"> 
                            <?
                            if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {

                                echo '<input type="button" '; 
                                
                                if ($calc->getPaperAddContent2()->getId() == 0) {
                                        echo ' style="background-image:url(images/page/organizer-selected.png);color:#fff" ';
                                    }
                                
                                echo 'class="selectbutton" id="20_0" name="add2paper" value="' . $_LANG->get('nicht vorhanden') . '" 
										onclick="clickAdd2Paper(this.id)">' . "\n";
                                $addSelected = false;
                                foreach ($order->getProduct()->getSelectedPapersIds(Calculation::PAPER_CONTENT) as $paper) {
                                    $paper = new Paper($paper["id"]);
                                    echo '<input type="button"';
                                    if ($calc->getPaperAddContent2()->getId() == $paper->getId()) {
                                        $addSelected = true;
                                        echo ' style="background-image:url(images/page/organizer-selected.png);color:#fff"';
                                    }

                                    echo ' class="selectbutton" id="20_' . $paper->getId() . '" name="add2paper" value="' . $paper->getName() . '" 
												onclick="clickAdd2Paper(this.id)">' . "\n";
                                }
                                if ($addSelected === false)
                                    echo '<script language="javascript">document.getElementById(\'20_0\').style.backgroundColor = \'#e3e3e3\'</script>';
                            }
                            ?>
                        </div>
                    </td>
                </tr>
                <tr id="tr_addcontent2_weight"<? if ($calc->getPaperAddContent2()->getId() == 0)
                            echo ' style="display:none"'; ?>>
                    <td class="content_row_header" style="color:gray;"><?= $_LANG->get('zus. Inhalt 2 - Gewicht') ?>
                    </td>
                    <td class="content_row_clear">
                        <div id="additional2_paperweight"> 
                            <?
                            if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                                $_REQUEST["product"] = (int) $_REQUEST["product"];
                                $papers = $order->getProduct()->getSelectedPapersIds(Calculation::PAPER_CONTENT);

                                foreach (($papers[$calc->getPaperContent()->getId()]) as $weight => $val) {
                                    if ($weight != "id") {
                                        echo '<input type="button" ';
                                        if ($calc->getpaperAddContent2Weight() == $weight)
                                            echo ' style="background-image:url(images/page/organizer-selected.png);color:#fff"';

                                        echo 'class="selectbutton" id="21_' . $weight . '" name="add2paperweight" value="' . $weight . ' '.$_LANG->get('g').'" 
													onclick="clickAdd2PaperWeight(this.id)">' . "\n";
                                    }
                                }
                            }
                            ?>
                        </div> 
                    </td>
                </tr>

                <tr id="tr_addcontent2_chromaticity"<? if ($calc->getPaperAddContent2()->getId() == 0)
                            echo ' style="display:none"'; ?>>
                    <td class="content_row_header" style="color:gray;"><?= $_LANG->get('zus. Inhalt 2 - Farbigkeit') ?>
                    </td>
                    <td class="content_row_clear">
                        <div id="additional2_paperchroma"> <?
                        if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                            $prod = new Product($_REQUEST["product"]);
                            foreach (Chromaticity::getAllChromaticities() as $pc) {
                                echo '<input type="button"';
                                if ($calc->getChromaticitiesAddContent2()->getId() == $pc->getId()){
                                    echo ' style="background-image:url(images/page/organizer-selected.png);color:#fff"';
								}
                                echo ' class="selectbutton" id="22_' . $pc->getId() . '" name="add2paperchroma" value="' . $pc->getName() . '" 
									onclick="clickAdd2PaperChromaticity(this.id)">' . "\n";
                            }
                        }
                        ?>
                        </div> 
                    </td>
                </tr>

                <tr id="tr_addcontent2_pages"<? if ($calc->getPaperAddContent2()->getId() == 0)
                            echo ' style="display:none"'; ?>>
                    <td class="content_row_header" style="color:gray;"><?= $_LANG->get('zus. Inhalt 2 - Seitenanzahl') ?>
                    </td>
                    <td class="content_row_clear">
                        <div id="additional2_paperpages"> 
                            <?
                            if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                                if($order->getProduct()->getType() == Product::TYPE_NORMAL)
                                    foreach ($order->getProduct()->getAvailablePageCounts() as $pc) {
                                        echo '<input type="button" ';
                                        if ($calc->getPagesAddContent2() == $pc)
                                            echo ' style="background-image:url(images/page/organizer-selected.png);color:#fff"';
                                        echo 'class="selectbutton" id="23_' . $pc . '" name="add2paperpages" value="' . $pc . ' ' . $_LANG->get('Seiten') . '" 
    												onclick="clickAdd2PaperPages(this.id)">' . "\n";
                                    }
                                else 
                                    echo '<input name="numberpages_addcontent2" value="'.$calc->getPagesAddContent2().'" style="width:60px" class="text"
                                    onfocus="focusAddContent2Pages()" onblur="setAddContent2Pages(this.value)">';
                            }
                            ?>
                        </div> 
                    </td>

    		</tr>
		</table>
	</div>
       <br>
<?	
} 

// Zusaetzlicher Inhalt 3
if ($order->getProduct()->getHasAddContent3()) {?>
    <h1><?=$_LANG->get('zus. Inhalt 3')?></h1>
    <div class="box2">
        <table width="100%">
            <colgroup>
                <col width="180">
                <col>
            </colgroup>
                <tr>
                    <td class="content_row_header" style="color:gray;">
                    	<?= $_LANG->get('zus. Inhalt 3 - Material') ?>
                    </td>
                    <td class="content_row_clear">
                        <div id="additional3_paper"> 
                            <?
                            if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {

                                echo '<input type="button" '; 
                                
                                if ($calc->getPaperAddContent3()->getId() == 0) {
                                        echo ' style="background-image:url(images/page/organizer-selected.png);color:#fff" ';
                                    }
                                
                                echo 'class="selectbutton" id="30_0" name="add3paper" value="' . $_LANG->get('nicht vorhanden') . '" 
										onclick="clickAdd3Paper(this.id)">' . "\n";
                                $addSelected = false;
                                foreach ($order->getProduct()->getSelectedPapersIds(Calculation::PAPER_CONTENT) as $paper) {
                                    $paper = new Paper($paper["id"]);
                                    echo '<input type="button"';
                                    if ($calc->getPaperAddContent3()->getId() == $paper->getId()) {
                                        $addSelected = true;
                                        echo ' style="background-image:url(images/page/organizer-selected.png);color:#fff"';
                                    }

                                    echo ' class="selectbutton" id="30_' . $paper->getId() . '" name="add3paper" value="' . $paper->getName() . '" 
												onclick="clickAdd3Paper(this.id)">' . "\n";
                                }
                                if ($addSelected === false)
                                    echo '<script language="javascript">document.getElementById(\'30_0\').style.backgroundColor = \'#e3e3e3\'</script>';
                            }
                            ?>
                        </div>
                    </td>
                </tr>
                <tr id="tr_addcontent3_weight"<? if ($calc->getPaperAddContent3()->getId() == 0)
                            echo ' style="display:none"'; ?>>
                    <td class="content_row_header" style="color:gray;"><?= $_LANG->get('zus. Inhalt 3 - Gewicht') ?>
                    </td>
                    <td class="content_row_clear">
                        <div id="additional3_paperweight"> 
                            <?
                            if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                                $_REQUEST["product"] = (int) $_REQUEST["product"];
                                $papers = $order->getProduct()->getSelectedPapersIds(Calculation::PAPER_CONTENT);

                                foreach (($papers[$calc->getPaperContent()->getId()]) as $weight => $val) {
                                    if ($weight != "id") {
                                        echo '<input type="button" ';
                                        if ($calc->getpaperAddContent3Weight() == $weight)
                                            echo ' style="background-image:url(images/page/organizer-selected.png);color:#fff"';

                                        echo 'class="selectbutton" id="31_' . $weight . '" name="add3paperweight" value="' . $weight . ' '.$_LANG->get('g').'" 
													onclick="clickAdd3PaperWeight(this.id)">' . "\n";
                                    }
                                }
                            }
                            ?>
                        </div> 
                    </td>
                </tr>

                <tr id="tr_addcontent3_chromaticity"<? if ($calc->getPaperAddContent3()->getId() == 0)
                            echo ' style="display:none"'; ?>>
                    <td class="content_row_header" style="color:gray;"><?= $_LANG->get('zus. Inhalt 3 - Farbigkeit') ?>
                    </td>
                    <td class="content_row_clear">
                        <div id="additional3_paperchroma"> <?
                        if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                            $prod = new Product($_REQUEST["product"]);
                            foreach (Chromaticity::getAllChromaticities() as $pc) {
                                echo '<input type="button"';
                                if ($calc->getChromaticitiesAddContent3()->getId() == $pc->getId()){
                                    echo ' style="background-image:url(images/page/organizer-selected.png);color:#fff"';
								}
                                echo ' class="selectbutton" id="33_' . $pc->getId() . '" name="add3paperchroma" value="' . $pc->getName() . '" 
									onclick="clickAdd3PaperChromaticity(this.id)">' . "\n";
                            }
                        }
                        ?>
                        </div> 
                    </td>
                </tr>

                <tr id="tr_addcontent3_pages"<? if ($calc->getPaperAddContent3()->getId() == 0)
                            echo ' style="display:none"'; ?>>
                    <td class="content_row_header" style="color:gray;"><?= $_LANG->get('zus. Inhalt 3 - Seitenanzahl') ?>
                    </td>
                    <td class="content_row_clear">
                        <div id="additional3_paperpages"> 
                            <?
                            if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                                if($order->getProduct()->getType() == Product::TYPE_NORMAL)
                                    foreach ($order->getProduct()->getAvailablePageCounts() as $pc) {
                                        echo '<input type="button" ';
                                        if ($calc->getPagesAddContent3() == $pc)
                                            echo ' style="background-image:url(images/page/organizer-selected.png);color:#fff"';
                                        echo 'class="selectbutton" id="33_' . $pc . '" name="add3paperpages" value="' . $pc . ' ' . $_LANG->get('Seiten') . '" 
    												onclick="clickAdd3PaperPages(this.id)">' . "\n";
                                    }
                                else 
                                    echo '<input name="numberpages_addcontent3" value="'.$calc->getPagesAddContent3().'" style="width:60px" class="text"
                                    onfocus="focusAddContent3Pages()" onblur="setAddContent3Pages(this.value)">';
                            }
                            ?>
                        </div> 
                    </td>

    		</tr>
		</table>
	</div>
       <br>
<?	
} 