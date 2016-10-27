<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *  
 */
require_once 'libs/modules/oauth/oauthclient.class.php';

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            oAuth Clients
            <span class="pull-right">
                <button class="btn btn-xs btn-success" onclick="window.location.href='index.php?page=libs/modules/oauth/oauthclient.edit.php';">
                    <span class="glyphicons glyphicons-plus"></span>
                    Client erstellen
                </button>
            </span>
        </h3>
    </div>
    <div class="table-responsive">
    	<table class="table table-hover">
    		<thead>
    			<tr>
    				<th>Name</th>
                    <th>Erstellt</th>
                    <th>Geändert</th>
    			</tr>
    		</thead>
    		<tbody>
                <?php
                $oaclients = oAuthClient::getAll();

                foreach ($oaclients as $oaclient) {
                    ?>
                    <tr class="pointer" onclick="window.location.href='index.php?page=libs/modules/oauth/oauthclient.edit.php&id=<?php echo urlencode($oaclient->getId());?>';">
                        <td><?php echo $oaclient->getName()?></td>
                        <td><?php echo $oaclient->getCreatedAt()?></td>
                        <td><?php echo $oaclient->getUpdatedAt()?></td>
                    </tr>
                    <?php
                } ?>
    		</tbody>
    	</table>
    </div>
</div>
