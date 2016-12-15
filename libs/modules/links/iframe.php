<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/links/link.class.php';

if (!$_REQUEST["link"])
    die('Keine Link ID!');

$link = new Link((int)$_REQUEST["link"]);

?>
<style>
    /* iframe's parent node */
    div#root {
        position: fixed;
        width: 100%;
        height: 100%;
    }

    /* iframe itself */
    div#root > iframe {
        display: block;
        width: 100%;
        height: 100%;
        border: none;
    }
</style>
<script>
    $(function () {
        $('div.content').css('width','100%');
    });
</script>

<div id="root">
    <iframe src="<?php echo $link->getUrl();?>">
        Your browser does not support inline frames.
    </iframe>
</div>

