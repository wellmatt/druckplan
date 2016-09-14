<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/letterhead/letterhead.class.php';

if ($_REQUEST["exec"] == "star"){
    if ($_REQUEST["id"]){
        $star = new Letterhead($_REQUEST["id"]);
        $star->setStandard();
    }
}
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Briefpapiere
            <span class="pull-right">
                <button class="btn btn-xs btn-success" onclick="document.location.href='index.php?page=libs/modules/letterhead/letterhead.edit.admin.php&exec=edit';">
                    <span class="glyphicons glyphicons-plus"></span>
                    Briefpapier hinzufügen
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
    				<th>Erstellt</th>
    				<th>Geändert</th>
    				<th>Standard</th>
    			</tr>
    		</thead>
    		<tbody>
                <?php
                $letterheads = Letterhead::fetch();
                foreach ($letterheads as $letterhead) {
                    ?>
                    <tr class="pointer" onclick="window.location.href='index.php?page=libs/modules/letterhead/letterhead.edit.admin.php&exec=edit&id=<?php echo $letterhead->getId();?>';">
                        <td><?php echo $letterhead->getName();?></td>
                        <?php
                        foreach (Document::getTypes() as $index => $value) {
                            if ($letterhead->getType() == $value)
                                echo '<td>'.$index.'</td>';
                        }
                        ?>
                        <td><?php echo date("d.m.y H:i",$letterhead->getCrtdate());?></td>
                        <td><?php echo date("d.m.y H:i",$letterhead->getUptdate());?></td>
                        <td>
                            <?php
                            if ($letterhead->getStd() == 0){
                                echo '<a href="index.php?page='.$_REQUEST['page'].'&exec=star&id='.$letterhead->getId().'"><img src="images/icons/star-empty.png"/></a>&nbsp;';
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
