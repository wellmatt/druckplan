<?php
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
error_reporting(-1);
ini_set('display_errors', 1);
chdir("../../../");
require_once 'libs/basic/basic.importer.php';
require_once 'libs/basic/globalFunctions.php';
require_once ('libs/modules/tickets/ticket.class.php');
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/planning/planning.job.class.php';

$_REQUEST["id"] = (int)$_REQUEST["id"];
$_REQUEST["type"] = (int)$_REQUEST["type"];

$start = $_REQUEST["start"];
$end = $_REQUEST["end"];

$mjs = PlanningJob::getAllJobs(" AND type = {$_REQUEST["type"]} AND artmach = {$_REQUEST["id"]} AND start >= {$start} AND end <= {$end} ORDER BY start, object, subobject, artmach ASC");

?>

<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<!-- <meta http-equiv="content-type" content="text/html; charset=ISO-8859-1"> -->
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/ticket.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<link rel="stylesheet" type="text/css" href="../../../css/main.print.css" media="print"/>
<link href="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">

<script type="text/javascript" language="JavaScript">
function printPage() {
    focus();
    if (window.print) 
    {
        jetztdrucken = confirm('Seite drucken ?');
        if (jetztdrucken) 
            window.print();
    }
}
</script>

<?
/**************************************************************************
 ******* 				HTML-Bereich								*******
 *************************************************************************/?>
<body OnLoad="printPage()">

<h2><?php if ($_REQUEST["type"] == 2) echo $mjs[0]->getArtmach()->getName(); elseif ($_REQUEST["type"]==1) echo $mjs[0]->getArtmach()->getTitle();?> <span class="small"><?php echo date('d.m.Y',$start) . " > " . date('d.m.Y',$end)?></span></h2>

<table width="100%">
    <tr>
    <td>ID</td>
    <td>MA</td>
    <td>Ticket</td>
    <td>gepl. für</td>
    <td>S-Zeit</td>
    <td>I-Zeit</td>
    <td>Status</td>
    </tr>
    <?php $i = 0;
    foreach ($mjs as $pj)
    {
        if ($pj->getTicket()->getState()->getId() != 1 && $pj->getTicket()->getState()->getId() != 3)
        {
            if($i % 2 != 0) $color = '#eaeaea'; else $color = '#eadddd';
            echo '<tr  style="background-color: '.$color.';">';
            
            echo '<td>#'.$pj->getId().'</td>';
            echo '<td>'.$pj->getAssigned_user()->getNameAsLine().'</td>';
            echo '<td>#'.$pj->getTicket()->getNumber().'</td>';
            echo '<td>'.date("d.m.Y H:i",$pj->getStart()).' -> '.date("d.m.Y H:i",$pj->getEnd()).'</td>';
            echo '<td>'.number_format($pj->getPlannedTime(), 2, ",", "").'</td>';
            if ($pj->getTime()>$pj->getPlannedTime())
                $style = ' style="background-color: red;"';
            echo '<td '.$style.'>'.number_format($pj->getTime(), 2, ",", "").'</td>';
            echo '<td><span style="display: inline-block; vertical-align: top; background-color: '.$pj->getTicket()->getState()->getColorcode().'" class="label">';
            echo $pj->getTicket()->getState()->getTitle().'</span></td>';
            
            echo '</tr>';
        }
    $i++;}
    ?>
</table>