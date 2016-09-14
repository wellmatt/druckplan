<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/documentformats/documentformat.class.php';

if ($_REQUEST["exec"] == "star"){
    if ($_REQUEST["id"]){
        $star = new DocumentFormat($_REQUEST["id"]);
        $star->setStandard();
    }
}
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Dokumentenformate
            <span class="pull-right">
                <button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=libs/modules/documentformats/documentformat.edit.admin.php&exec=edit';">
                    <span class="glyphicons glyphicons-plus"></span>
                    Dokumentenformat hinzufügen
                </button>
            </span>
        </h3>
    </div>
    <div class="table-responsive">
    	<table class="table table-hover">
    		<thead>
    			<tr>
                    <th>Name</th>
                    <th>Typ</th>
                    <th>Ausrichtung</th>
                    <th>Breite (mm)</th>
                    <th>Höhe (mm)</th>
                    <th>Standard</th>
    			</tr>
    		</thead>
    		<tbody>
                <?php
                $docformats = DocumentFormat::fetch();
                foreach ($docformats as $docformat) {
                    ?>
                    <tr class="pointer" onclick="window.location.href='index.php?page=libs/modules/documentformats/documentformat.edit.admin.php&exec=edit&id=<?php echo $docformat->getId();?>';">
                        <td><?php echo $docformat->getName();?></td>
                        <?php
                        foreach (Document::getTypes() as $index => $value) {
                            if ($docformat->getDoctype() == $value)
                                echo '<td>'.$index.'</td>';
                        }
                        ?>
                        <td><?php if ($docformat->getOrientation() == DocumentFormat::ORI_PORTRAIT) echo 'Portrait'; else echo 'Landscape';?></td>
                        <td><?php echo $docformat->getWidth();?></td>
                        <td><?php echo $docformat->getHeight();?></td>
                        <td>
                            <?php
                            if ($docformat->getStd() == 0){
                                echo '<a href="index.php?page='.$_REQUEST['page'].'&exec=star&id='.$docformat->getId().'"><img src="images/icons/star-empty.png"/></a>&nbsp;';
                            } else {
                                echo '<img src="images/icons/star.png"/>';
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
    		</tbody>
    	</table>
    </div>
</div>
