<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *  
 */

require_once 'emailaddress.class.php';
$_REQUEST["id"] = (int)$_REQUEST["id"];
if ($_REQUEST["exec"] == "delete") {
    $mailaddress = new Emailaddress($_REQUEST["id"]);
    $savemsg = getSaveMessage($mailaddress->delete());
}

if ($_REQUEST["exec"] == "edit" || $_REQUEST["exec"] == "new" || $_REQUEST["exec"] == "copy") {
    require_once 'emailaddress.edit.php';
} else {
    $mailaddress = Emailaddress::getAllEmailaddress();
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">
                eMail-Adressen
				<span class="pull-right">
					<button class="btn btn-xs btn-success"
                            onclick="document.location.href='index.php?page=<?= $_REQUEST['page'] ?>&exec=new';">
                        <span class="glyphicons glyphicons-plus"></span>
                        <?= $_LANG->get('eMail-Adresse hinzuf&uuml;gen') ?>
                    </button>
				</span>
            </h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th><?= $_LANG->get('ID') ?></th>
                    <th><?= $_LANG->get('Adresse') ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    <? foreach ($mailaddress as $e) { ?>
                        <tr>
                            <td><?=$e->getId() ?></td>
                            <td><?=$e->getAddress()?></td>
                            <td>
                                <a class="icon-link"
                                   href="index.php?page=<?= $_REQUEST['page'] ?>&exec=edit&id=<?= $e->getId() ?>"><span
                                        class="glyphicons glyphicons-pencil"></span></a>
                                <a class="icon-link"
                                   href="index.php?page=<?= $_REQUEST['page'] ?>&exec=copy&id=<?= $e->getId() ?>"><span
                                        class="glyphicons glyphicons-file"></span></a>
                                <a class="icon-link" href="#"
                                   onclick="askDel('index.php?page=<?= $_REQUEST['page'] ?>&exec=delete&id=<?= $e->getId() ?>')"><span
                                        class="glyphicons glyphicons-remove"></span></a>
                            </td>
                        </tr>
                    <? } ?>
                </tbody>
            </table>
        </div>
    </div>
<? } ?>
