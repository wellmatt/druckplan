<? // -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			11.12.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

// Zusaetzlicher Inhalt 2
if ($order->getProduct()->getHasAddContent2()) {?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                Inhalt 3
            </h3>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>
                            <?= $_LANG->get('Inhalt 3') ?>
                        </td>
                        <td>

                            <div id="additional2_paper">
                                <?
                                if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {

                                    echo '<input style="margin: 2px;" type="button" ';

                                    if ($calc->getPaperAddContent2()->getId() == 0) {
                                        echo ' ';
                                    }

                                    echo ' class="btn btn-default btn-info" id="20_0" name="add2paper" value="' . $_LANG->get('nicht vorhanden') . '"
										onclick="clickAdd2Paper(this.id)">' . "\n";
                                    $addSelected = false;
                                    foreach ($order->getProduct()->getSelectedPapersIds(Calculation::PAPER_CONTENT) as $paper) {
                                        $paper = new Paper($paper["id"]);
                                        echo '<input type="button"';
                                        if ($calc->getPaperAddContent2()->getId() == $paper->getId()) {
                                            $addSelected = true;
                                            echo ' class="btn btn-default btn-success" ';
                                        }
                                        else {
                                            echo ' class="btn btn-default btn-info" ';
                                        }

                                        echo '  class="btn btn-default btn-info" style="margin: 2px;" id="20_' . $paper->getId() . '" name="add2paper" value="' . $paper->getName() . '"
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
                        <td><?= $_LANG->get('Inhalt 3 Gewicht') ?>
                        </td>
                        <td>
                            <div id="additional2_paperweight">
                                <?
                                if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                                    $_REQUEST["product"] = (int) $_REQUEST["product"];
                                    $papers = $order->getProduct()->getSelectedPapersIds(Calculation::PAPER_CONTENT);

                                    foreach (($papers[$calc->getPaperContent()->getId()]) as $weight => $val) {
                                        if ($weight != "id") {
                                            echo '<input style="margin: 2px;" type="button" ';
                                            if ($calc->getpaperAddContent2Weight() == $weight)
                                                echo ' class="btn btn-default btn-success" ';
                                        }
                                        else {
                                            echo ' class="btn btn-default btn-info" ';

                                            echo ' class="btn btn-default btn-info" id="21_' . $weight . '" name="add2paperweight" value="' . $weight . ' '.$_LANG->get('g').'"
													onclick="clickAdd2PaperWeight(this.id)">' . "\n";
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </td>
                    </tr>

                    <tr id="tr_addcontent2_pages"<? if ($calc->getPaperAddContent2()->getId() == 0)
                        echo ' style="display:none"'; ?>>
                        <td><?= $_LANG->get('Inhalt 3 bedr. Seiten') ?>
                        </td>
                        <td>
                            <div id="additional2_paperpages">
                                <?
                                if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                                    if($order->getProduct()->getType() == Product::TYPE_NORMAL)
                                        foreach ($order->getProduct()->getAvailablePageCounts() as $pc) {
                                            echo '<input style="margin: 2px;" type="button" ';
                                            if ($calc->getPagesAddContent2() == $pc)
                                                echo ' class="btn btn-default btn-success" ';
                                            else
                                                echo ' class="btn btn-default btn-info" ';

                                            echo ' class="btn btn-default btn-info" id="23_' . $pc . '" name="add2paperpages" value="' . $pc . ' ' . $_LANG->get('Seiten') . '"
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

                    <tr id="tr_addcontent2_chromaticity"<? if ($calc->getPaperAddContent2()->getId() == 0)
                        echo ' style="display:none"'; ?>>
                        <td><?= $_LANG->get('Inhalt 3 Farbigkeit') ?>
                        </td>
                        <td>
                            <div id="additional2_paperchroma"> <?
                                if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                                    if (count($order->getProduct()->getAvailableChromaticities())>0){
                                        $chromas = $order->getProduct()->getAvailableChromaticities();
                                    } else {
                                        $chromas = Chromaticity::getAllChromaticities();
                                    }

                                    $prod = new Product($_REQUEST["product"]);
                                    foreach ($chromas as $pc) {
                                        echo '<input style="margin: 2px;" type="button"';
                                        if ($calc->getChromaticitiesAddContent2()->getId() == $pc->getId()){
                                            echo ' class="btn btn-default btn-success" ';
                                        }
                                        else {
                                            echo ' class="btn btn-default btn-info" ';
                                        }
                                        echo ' class="btn btn-default btn-info" id="22_' . $pc->getId() . '" name="add2paperchroma" value="' . $pc->getName() . '"
									onclick="clickAdd2PaperChromaticity(this.id)">' . "\n";
                                    }
                                }
                                ?>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?
}

// Zusaetzlicher Inhalt 3
if ($order->getProduct()->getHasAddContent3()) {?>
    <div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Inhalt 4
        </h3>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    <?= $_LANG->get('Inhalt 4') ?>
                </td>
                <td>
                    <div id="additional3_paper">
                        <?
                        if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {

                            echo '<input style="margin: 2px;" type="button" ';

                            if ($calc->getPaperAddContent3()->getId() == 0) {
                                echo ' class="btn btn-default btn-success" ';
                            }
                            else {
                                echo ' class="btn btn-default btn-info" ';
                            }

                            echo ' class="btn btn-default btn-info" id="30_0" name="add3paper" value="' . $_LANG->get('nicht vorhanden') . '"
										onclick="clickAdd3Paper(this.id)">' . "\n";
                            $addSelected = false;
                            foreach ($order->getProduct()->getSelectedPapersIds(Calculation::PAPER_CONTENT) as $paper) {
                                $paper = new Paper($paper["id"]);
                                echo '<input style="margin: 2px;" type="button"';
                                if ($calc->getPaperAddContent3()->getId() == $paper->getId()) {
                                    $addSelected = true;
                                    echo ' class="btn btn-default btn-success" ';
                                }
                                else {
                                    echo ' class="btn btn-default btn-info" ';
                                }

                                echo '  class="btn btn-default btn-info" id="30_' . $paper->getId() . '" name="add3paper" value="' . $paper->getName() . '"
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
                <td><?= $_LANG->get('Inhalt 4 Gewicht') ?>
                </td>
                <td>
                    <div id="additional3_paperweight">
                        <?
                        if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                            $_REQUEST["product"] = (int) $_REQUEST["product"];
                            $papers = $order->getProduct()->getSelectedPapersIds(Calculation::PAPER_CONTENT);

                            foreach (($papers[$calc->getPaperContent()->getId()]) as $weight => $val) {
                                if ($weight != "id") {
                                    echo '<input style="margin: 2px;" type="button" ';
                                    if ($calc->getpaperAddContent3Weight() == $weight)
                                        echo ' class="btn btn-default btn-success" ';
                                    else
                                        echo ' class="btn btn-default btn-info" ';

                                    echo ' class="btn btn-default btn-info" id="31_' . $weight . '" name="add3paperweight" value="' . $weight . ' '.$_LANG->get('g').'"
													onclick="clickAdd3PaperWeight(this.id)">' . "\n";
                                }
                            }
                        }
                        ?>
                    </div>
                </td>
            </tr>

            <tr id="tr_addcontent3_pages"<? if ($calc->getPaperAddContent3()->getId() == 0)
                echo ' style="display:none"'; ?>>
                <td><?= $_LANG->get('Inhalt 4 bedr. Seiten') ?>
                </td>
                <td>
                    <div id="additional3_paperpages">
                        <?
                        if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                            if($order->getProduct()->getType() == Product::TYPE_NORMAL)
                                foreach ($order->getProduct()->getAvailablePageCounts() as $pc) {
                                    echo '<input style="margin: 2px;" type="button" ';
                                    if ($calc->getPagesAddContent3() == $pc)
                                        echo ' class="btn btn-default btn-success" ';
                                    else
                                        echo ' class="btn btn-default btn-info" ';
                                    echo ' class="btn btn-default btn-info" id="33_' . $pc . '" name="add3paperpages" value="' . $pc . ' ' . $_LANG->get('Seiten') . '"
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

            <tr id="tr_addcontent3_chromaticity"<? if ($calc->getPaperAddContent3()->getId() == 0)
                echo ' style="display:none"'; ?>>
                <td><?= $_LANG->get('Inhalt 4 Farbigkeit') ?>
                </td>
                <td>
                    <div id="additional3_paperchroma"> <?
                        if ($calc->getId() > 0 || $_REQUEST["subexec"] == "copy") {
                            if (count($order->getProduct()->getAvailableChromaticities())>0){
                                $chromas = $order->getProduct()->getAvailableChromaticities();
                            } else {
                                $chromas = Chromaticity::getAllChromaticities();
                            }

                            $prod = new Product($_REQUEST["product"]);
                            foreach ($chromas as $pc) {
                                echo '<input style="margin: 2px;" type="button"';
                                if ($calc->getChromaticitiesAddContent3()->getId() == $pc->getId()){
                                    echo ' class="btn btn-default btn-success" ';
                                }
                                else {
                                    echo ' class="btn btn-default btn-info" ';
                                }
                                echo ' class="btn btn-default btn-info" id="33_' . $pc->getId() . '" name="add3paperchroma" value="' . $pc->getName() . '"
									onclick="clickAdd3PaperChromaticity(this.id)">' . "\n";
                            }
                        }
                        ?>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
    <?
}