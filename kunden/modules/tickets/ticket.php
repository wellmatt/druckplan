<? // ------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			16.07.2013
// Copyright:		2013 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ---------------------------------------------------------------------------------
require_once ('libs/modules/tickets/ticket.class.php');

$_USER = new User($_BUSINESSCONTACT->getSupervisor()->getId());
Global $_USER;


if ($_USER->getId() != 0){
    switch ($_REQUEST["exec"]) {
        case "edit":
            // Daten setzen und speichern geschieht in der ticket.edit.php
            require_once 'ticket.edit.php';
            break;
        case "new":
            require_once 'ticket.edit.php';
            break;
        default:
            require_once 'ticket.overview.php';
            break;
    }
} else {
    echo "</br><b>Ihnen wurde noch kein Betreuer zugewiesen!</b>";
}
?>