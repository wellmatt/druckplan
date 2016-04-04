<?php
require_once('document.format.class.php');

if ($_REQUEST["exec"] == "save")
{

    foreach (DocumentFormat::getAllTypes() as $Type => $TypeName) {
        $docformat = DocumentFormat::getForDocType($Type);
        $docformat->setDoctype($_REQUEST[$Type.'_type']);
        $docformat->setOrientation($_REQUEST[$Type.'_orientation']);
        $docformat->setWidth(tofloat($_REQUEST[$Type.'_width']));
        $docformat->setHeight(tofloat($_REQUEST[$Type.'_height']));
        $docformat->setMarginTop(tofloat($_REQUEST[$Type.'_mtop']));
        $docformat->setMarginLeft(tofloat($_REQUEST[$Type.'_mleft']));
        $docformat->setMarginRight(tofloat($_REQUEST[$Type.'_mright']));
        $docformat->setMarginBottom(tofloat($_REQUEST[$Type.'_mbottom']));
        $docformat->save();
    }
}
?>

<table width="100%">
    <tr>
        <td width="200" class="content_header"><img src="images/icons/gear.png"> <?=$_LANG->get('Dokumenten Einstellungen')?></td>
        <td align="right"><?=$savemsg?></td>
    </tr>
</table>

<div id="fl_menu">
    <div class="label">Quick Move</div>
    <div class="menu">
        <a href="#top" class="menu_item">Seitenanfang</a>
        <a href="index.php?page=<?=$_REQUEST['page']?>" class="menu_item">Zurück</a>
        <a href="#" class="menu_item" onclick="$('#form').submit();">Speichern</a>
    </div>
</div>

<form action="index.php?page=<?= $_REQUEST['page'] ?>" method="post" enctype="multipart/form-data" name="form"
      id="form">
    <input type="hidden" name="exec" value="save">
    <div class="box1">
        <div id="tabs-4">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <thead>
                    <tr>
                        <td>Dokument</td>
                        <td>Ausrichtung</td>
                        <td>Breite (mm)</td>
                        <td>Höhe (mm)</td>
                    </tr>
                </thead>
                <?php
                foreach (DocumentFormat::getAllTypes() as $Type => $TypeName) {
                    $docformat = DocumentFormat::getForDocType($Type);
                    ?>
                    <input type="hidden" name="<?php echo $Type;?>_id" value="<?php echo $docformat->getId();?>">
                    <input type="hidden" name="<?php echo $Type;?>_type" value="<?php echo $Type;?>">
                    <tr>
                        <td class="content_row_header" valign="top"><?php echo $TypeName;?></td>
                        <td class="content_row_header" valign="top">
                            <select name="<?php echo $Type;?>_orientation">
                                <option <?php if ($docformat->getOrientation() == DocumentFormat::ORI_PORTRAIT) echo ' selected ';?> value="P">Portrait</option>
                                <option <?php if ($docformat->getOrientation() == DocumentFormat::ORI_LANDSCAPE) echo ' selected ';?> value="L">Landscape</option>
                            </select>
                        </td>
                        <td class="content_row_header" valign="top">
                            <input type="number" step="0.1" name="<?php echo $Type;?>_width" value="<?php echo $docformat->getWidth();?>">
                        </td>
                        <td class="content_row_header" valign="top">
                            <input type="number" step="0.1" name="<?php echo $Type;?>_height" value="<?php echo $docformat->getHeight();?>">
                        </td>
                    </tr>
                    <tr>
                        <td>Margins Oben: <input type="number" step="0.5" name="<?php echo $Type;?>_mtop" value="<?php echo $docformat->getMarginTop();?>"></td>
                        <td>Links: <input type="number" step="0.5" name="<?php echo $Type;?>_mleft" value="<?php echo $docformat->getMarginLeft();?>"></td>
                        <td>Rechts: <input type="number" step="0.5" name="<?php echo $Type;?>_mright" value="<?php echo $docformat->getMarginRight();?>"></td>
                        <td>Unten: <input type="number" step="0.5" name="<?php echo $Type;?>_mbottom" value="<?php echo $docformat->getMarginBottom();?>"></td>
                    </tr>
                    <tr>
                        <td colspan="4">&nbsp;</td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        </div>
    </div>
</form>