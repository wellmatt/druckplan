<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/backup/backup.class.php';

if ($_REQUEST["exec"] == "new"){
    $backup = new Backup();
    $backup->saveFiles();
    $backup->saveDB();
} else if ($_REQUEST["exec"] == "unlink" && $_REQUEST["filename"]){
    unlink($_REQUEST["filename"]);
}

$file_backups = Backup::getFileBackups();
$sql_backups = Backup::getSqlBackups();

?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">
            Backups
            <span class="pull-right">
                <button type="button" class="btn btn-xs btn-success" onclick="askDel('index.php?page=<?php echo $_REQUEST["page"];?>&exec=new');">
                    Neues Backup
                </button>
            </span>
        </h3>
    </div>
    <div class="panel-body">
        Hinweis: Bei dem Erstellen eines neuen Backups kann die Ladezeit je nach größe des Systems variieren, bitte lassen Sie die Seite vollständig zu Ende laden!<br>&nbsp;
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Daten</h3>
            </div>
            <div class="table-responsive">
            	<table class="table table-hover">
            		<thead>
            			<tr>
            				<th>Datei</th>
            				<th>Größe</th>
            				<th>Datum</th>
            				<th></th>
            			</tr>
            		</thead>
                    <tbody>
                        <?php
                        foreach ($file_backups as $backup) {
                            ?>
                            <tr>
                                <td><?php echo $backup['name'];?></td>
                                <td><?php echo $backup['size'];?></td>
                                <td><?php echo $backup['time'];?></td>
                                <td>
                                    <span title="Löschen" class="pointer glyphicons glyphicons-remove" onclick="askDel('index.php?page=<?php echo $_REQUEST["page"];?>&exec=unlink&filename=backups/files/<?php echo $backup['name'];?>');"></span>
                                    <span title="Download" class="pointer glyphicons glyphicons-download-alt" onclick="window.location.href='backups/files/<?php echo $backup['name'];?>'"></span>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
            	</table>
            </div>
        </div>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Datenbank</h3>
            </div>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Datei</th>
                        <th>Größe</th>
                        <th>Datum</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($sql_backups as $backup) {
                            ?>
                            <tr>
                                <td><?php echo $backup['name'];?></td>
                                <td><?php echo $backup['size'];?></td>
                                <td><?php echo $backup['time'];?></td>
                                <td>
                                    <span title="Löschen" class="pointer glyphicons glyphicons-remove" onclick="askDel('index.php?page=<?php echo $_REQUEST["page"];?>&exec=unlink&filename=backups/sql/<?php echo $backup['name'];?>');"></span>
                                    <span title="Download" class="pointer glyphicons glyphicons-download-alt" onclick="window.location.href='backups/sql/<?php echo $backup['name'];?>'"></span>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
