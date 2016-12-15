<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/links/link.class.php';

$links_priv = Link::getAllPrivate();
$links_pub = Link::getAllPublic();

?>

<table cellspacing="0" cellpadding="0" width="100%" style="margin-top: 5px;">
	<tr>
        <td align="right"><b style="font-size: 14px;">Private</b></td>
	</tr>
    <?php foreach ($links_priv as $link) {?>
        <tr>
            <td align="right" style="font-size: 12px;">
                <a href="index.php?page=libs/modules/links/iframe.php&link=<?php echo $link->getId();?>"><?php echo $link->getTitle();?></a>
                <a href="<?php echo $link->getUrl();?>" target="_blank"><span class="glyphicons glyphicons-new-window-alt" style="margin: 0 0 0"></span></a>
                <a href="index.php?page=libs/modules/links/link.edit.php&id=<?php echo $link->getId();?>"><span class="glyphicons glyphicons-pencil" style="margin: 0 0 0"></span></a>
            </td>
        </tr>
    <?php } ?>

    <tr>
        <td align="right" style="padding-top: 8px;"><b style="font-size: 14px;">Öffentliche</b></td>
    </tr>
    <?php foreach ($links_pub as $link) {?>
        <tr>
            <td align="right" style="font-size: 12px;">
                <a href="index.php?page=libs/modules/links/iframe.php&link=<?php echo $link->getId();?>"><?php echo $link->getTitle();?></a>
                <a href="<?php echo $link->getUrl();?>" target="_blank"><span class="glyphicons glyphicons-new-window-alt" style="margin: 0 0 0"></span></a>
                <?php if ($_USER->isAdmin()){?>
                    <a href="index.php?page=libs/modules/links/link.edit.php&id=<?php echo $link->getId();?>"><span class="glyphicons glyphicons-pencil" style="margin: 0 0 0"></span></a>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
</table>
<div style="text-align: right; margin-top: 10px;">
    <button class="btn btn-xs btn-success" onclick="window.location.href='index.php?page=libs/modules/links/link.edit.php';">
        <span class="glyphicons glyphicons-plus" style="margin: 0 0 0"></span>
        Neuer Link
    </button>
</div>