<?php
// -------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			19.12.2014
// Copyright:		2014 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
// ----------------------------------------------------------------------------------

// error_reporting(-1);
// ini_set('display_errors', 1);

chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/timer/timer.class.php';
require_once 'libs/modules/perferences/perferences.class.php';


$_REQUEST["tktid"] = (int)$_REQUEST["tktid"];
$ticket = new Ticket($_REQUEST["tktid"]);

?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/calcoverview.css" />
<script language="javascript" src="jscripts/basic.js"></script>
</head>
<body>
<h1>Ticket-Summary</h1>
<div class="outer">
<table cellpadding="0" cellspacing="0" border="0" width="100%">
    <colgroup>
        <col width="25%">
        <col width="25%">
        <col width="25%">
        <col width="25%">
    </colgroup>
    <tr>
        <td><b>Ticket:</b></td>
        <td><?php echo $ticket->getTitle();?></td>
        <td><b>Kunde:</b></td>
        <td><?php echo $ticket->getCustomer()->getNameAsLine(); echo " - "; echo $ticket->getCustomer_cp()->getNameAsLine2();?></td>
    </tr>
    <tr>
        <td valign="top"><b>Kategorie:</b></td>
        <td valign="top"><?php echo $ticket->getCategory()->getTitle();?></td>
        <td valign="top"><b>Telefon:</b></td>
        <td valign="top"><?php echo $ticket->getCustomer_cp()->getPhone();?></td>
    </tr>
    <tr>
        <td valign="top"><b>Status:</b></td>
        <td valign="top"><?php echo $ticket->getState()->getTitle();?></td>
        <td valign="top"><b>eMail:</b></td>
        <td valign="top"><?php echo $ticket->getCustomer_cp()->getEmail();?></td>
    </tr>
    <tr>
        <td valign="top"><b>Priorität:</b></td>
        <td valign="top"><?php echo $ticket->getPriority()->getTitle();?></td>
        <td valign="top"><b>Herkunft:</b></td>
        <td valign="top">
            <?php
            if ($ticket->getSource() == Ticket::SOURCE_EMAIL)
                echo "per eMail";
            if ($ticket->getSource() == Ticket::SOURCE_PHONE)
                echo "per Telefon";
            if ($ticket->getSource() == Ticket::SOURCE_OTHER)
                echo "andere";
            ?>
        </td>
    </tr>
    <tr>
        <td valign="top"><b>Fällig:</b></td>
        <td valign="top"><?php echo date('d.m.Y H:i', $ticket->getDuedate());?></td>
        <td valign="top"><b>letzte Mitteilung:</b></td>
        <td valign="top"><?php if ($ticket->getEditdate() > 0) echo date("d.m.Y H:i",$ticket->getEditdate());?></td>
    </tr>
    <tr>
        <td valign="top"><b>Erstellt am:</b></td>
        <td valign="top"><?php echo date("d.m.Y H:i",$ticket->getCrtdate()) . " von " . $ticket->getCrtuser()->getNameAsLine();?></td>
        <td valign="top"><b>zugewiesen an:</b></td>
        <td valign="top">
            <?php 
            if ($ticket->getAssigned_group()->getId() > 0){
                echo $ticket->getAssigned_group()->getName();
            } else {
                echo $ticket->getAssigned_user()->getNameAsLine();
            }
            ?>
        </td>
    </tr>
</table>
</div>
<br>

<?php 

$all_comments = Comment::getCommentsForObject(get_class($ticket),$ticket->getId());

$art_array = Array();

foreach ($all_comments as $comment){
    if ($comment->getState() > 0 && count($comment->getArticles()) > 0){
        foreach ($comment->getArticles() as $c_article){
            $art_array[$c_article->getArticle()->getId()]["name"] = $c_article->getArticle()->getTitle();
            $art_array[$c_article->getArticle()->getId()]["count"] += $c_article->getAmount();
            $art_array[$c_article->getArticle()->getId()]["id"] = $c_article->getArticle()->getId();
        }
    }
}

$totalprice = 0;

?>

<h1>Artikel Zusammenfassung</h1>
<div class="outer">
    <table cellpadding="0" cellspacing="0" border="0" width="50%">
		<thead>
			<tr>
				<th><u>Artikel</u></th>
				<th><u>Menge</u></th>
				<th><u>Preis</u></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><u>Artikel</u></th>
				<th><u>Menge</u></th>
				<th><u>Preis</u></th>
			</tr>
		</tfoot>
		<?php foreach ($art_array as $art){
		  $tmp_art = new Article((int)$art["id"]);
		  $totalprice += $tmp_art->getPrice($art["count"])*$art["count"];
		  ?>
        <tr>
            <td valign="top"><?php echo $art["name"];?></td>
            <td valign="top"><?php echo $art["count"];?></td>
            <td valign="top"><?php echo $tmp_art->getPrice($art["count"])*$art["count"]?> €</td>
        </tr>
        <?php }?>
    </table>
    </br>
    <b><u>Gesamtpreis: <?php echo $totalprice;?> €</u></b>
</div>
<br>
<h1>Artikel Einzelübersicht</h1>
<div class="outer">
    <table cellpadding="0" cellspacing="0" border="0" width="50%">
		<thead>
			<tr>
				<th><u>Artikel</u></th>
				<th><u>Menge</u></th>
				<th><u>Kommentar</u></th>
				<th><u>User</u></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><u>Artikel</u></th>
				<th><u>Menge</u></th>
				<th><u>Kommentar</u></th>
				<th><u>User</u></th>
			</tr>
		</tfoot>
		<?php foreach ($all_comments as $comment){
		    if ($comment->getState() > 0 && count($comment->getArticles()) > 0){
		        foreach ($comment->getArticles() as $c_article){?>
        <tr>
            <td valign="top"><?php echo $c_article->getArticle()->getTitle();?></td>
            <td valign="top"><?php echo $c_article->getAmount();?></td>
            <td valign="top"><?php echo $comment->getComment();?></td>
            <td valign="top"><?php echo $comment->getCrtuser()->getNameAsLine();?></td>
        </tr>
        <?php }}}?>
    </table>
</div>
<br>
<h1>Kommentar Übersicht</h1>
<div class="outer">
    <table cellpadding="0" cellspacing="0" border="0" width="50%">
		<thead>
			<tr>
				<th><u>User</u></th>
				<th><u>Kommentar</u></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th><u>User</u></th>
				<th><u>Kommentar</u></th>
			</tr>
		</tfoot>
		<?php $x=0;foreach ($all_comments as $comment){
		    if ($comment->getState() > 0){?>
        <tr class="<?=getRowColor($x)?>">
            <td valign="top"><?php echo $comment->getCrtuser()->getNameAsLine();?></td>
            <td valign="top"><?php echo $comment->getComment();?></td>
        </tr>
        <?php $x++;}}?>
    </table>
</div>
</body>
</html>
