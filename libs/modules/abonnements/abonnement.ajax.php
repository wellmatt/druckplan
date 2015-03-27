<?php
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			19.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/modules/abonnements/abonnement.class.php';
require_once 'libs/modules/comment/comment.class.php';

error_reporting(-1);
ini_set('display_errors', 1);

// echo "test";

if ($_REQUEST["exec"] == "abo_add" && $_REQUEST["module"] && $_REQUEST["objectid"]){
    $abo = new Abonnement();
    $abo->setAbouser($_USER);
    $abo->setModule($_REQUEST["module"]);
    $abo->setObjectid($_REQUEST["objectid"]);
    $abo->save();
} elseif ($_REQUEST["exec"] == "abo_remove" && $_REQUEST["module"] && $_REQUEST["objectid"] && $_REQUEST["userid"]){
    $user = new User($_REQUEST["userid"]);
    $abo = Abonnement::getAbonnement($user ,$_REQUEST["module"], $_REQUEST["objectid"]);
    $abo->delete();
    
    $ticketcomment = new Comment();
    $ticketcomment->setComment("");
    $ticketcomment->setTitle("Abonnoment abbestellt");
    $ticketcomment->setCrtuser($user);
    $ticketcomment->setCrtdate(time());
    $ticketcomment->setState(1);
    $ticketcomment->setModule($_REQUEST["module"]);
    $ticketcomment->setObjectid($_REQUEST["objectid"]);
    $ticketcomment->setVisability(Comment::VISABILITY_INTERNAL);
    $ticketcomment->save();
} elseif ($_REQUEST["exec"] == "abo_getcount" && $_REQUEST["module"] && $_REQUEST["objectid"]){
    $abonnoments = Abonnement::getAbonnementsForObject($_REQUEST["module"], $_REQUEST["objectid"]);
    $count = 0;
    $count += count($abonnoments);
    echo $count;
}
?>