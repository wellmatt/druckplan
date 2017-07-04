<?//--------------------------------------------------------------------------------
// Author:			iPactor GmbH
// Updated:			18.09.2012
// Copyright:		2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
chdir('../../../');
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/chromaticity/chromaticity.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/planning/planning.job.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();

$dates = GetDays(date('Y-m-d',strtotime($_REQUEST["start"])),date('Y-m-d',strtotime($_REQUEST["end"])), $format = "d.m.Y");

$type = substr($_REQUEST["artmach"], 0, 1);
$artmach = substr($_REQUEST["artmach"], 1);
$statsenabled = $_REQUEST["stats"];
$print = $_REQUEST["print"];
$vo = $_REQUEST["vo"];
if (isset($vo) && $vo != "" && is_numeric($vo)){
    $vostring = ' AND object = '.$vo.' ';
}

if ($print == 1)
{
    ?>
<link rel="stylesheet" type="text/css" href="../../../css/main.css" />
<link rel="stylesheet" type="text/css" href="../../../css/ticket.css" />
<link rel="stylesheet" type="text/css" href="../../../css/menu.css" />
<link rel="stylesheet" type="text/css" href="../../../css/main.print.css" media="print"/>
<link href="../../../thirdparty/MegaNavbar/assets/plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
<link href="../../../thirdparty/MegaNavbar/assets/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="../../../thirdparty/MegaNavbar/assets/css/MegaNavbar.css"/>
<link rel="stylesheet" type="text/css" href="../../../thirdparty/MegaNavbar/assets/css/skins/navbar-default.css" title="inverse">

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
<body OnLoad="printPage()">
<h2>Planungstabelle - Druckansicht</h2>


<?php 

$html = '';
foreach ($dates as $date)
{
    $day_start = mktime(0, 0, 0, date('m', strtotime($date)), date('d', strtotime($date)), date('Y', strtotime($date)));
    $day_end = mktime(23,59,59,date('m',strtotime($date)),date('d',strtotime($date)),date('Y',strtotime($date)));
    if ($type == "0")
        $jobs = PlanningJob::getAllJobs(" AND start >= {$day_start} AND start <= {$day_end} $vostring");
    else if ($type == "K")
    {
        $jobs = PlanningJob::getAllJobs(" AND type = 2 AND artmach = {$artmach} AND start >= {$day_start} AND start <= {$day_end} $vostring");
    }
    else if ($type == "V")
    {
        $jobs = PlanningJob::getAllJobs(" AND type = 1 AND artmach = {$artmach} AND start >= {$day_start} AND start <= {$day_end} $vostring");
    }
    
    $stats = Array();
    $statshtml = '';
    $subhtml = '';
    if (count($jobs)>0)
    {
        foreach ($jobs as $pj)
        {
            $subhtml .= '<div class="panel panel-default">';

            $subhtml .= '<div class="table-responsive">';
            $subhtml .= '<table class="table table-hover">';
            $subhtml .= '<thead>';
            $subhtml .= '<tr>';
            $subhtml .= '<td width="20">ID</td>';
            $subhtml .= '<td width="400">Objekt</td>';
            $subhtml .= '<td width="150">MA</td>';
            $subhtml .= '<td width="50">Vorgang</td>';
            $subhtml .= '<td width="50">Ticket</td>';
            $subhtml .= '<td width="80">Prod. Beginn</td>';
            $subhtml .= '<td width="70">Lief.-Datum</td>';
            $subhtml .= '<td width="50">S-Zeit</td>';
            $subhtml .= '<td width="50">I-Zeit</td>';
            $subhtml .= '<td width="80">Status</td>';
            $subhtml .= '</tr>';
            $subhtml .= '</thead>';
            $subhtml .= '<tr>';
            $subhtml .= '<td>#'.$pj->getId().'</td>';
            if ($pj->getType()==2)
                $subhtml .= '<td>'.$pj->getArtmach()->getGroup()->getName() . ' - ' . $pj->getArtmach()->getName().'</td>';
            else
                $subhtml .= '<td>'.$pj->getArtmach()->getTitle().'</td>';
            if ($pj->getAssigned_user()->getId()>0)
                $subhtml .= '<td>'.$pj->getAssigned_user()->getNameAsLine().'</td>';
            else 
                $subhtml .= '<td>'.$pj->getAssigned_group()->getName().'</td>';
            $subhtml .= '<td>#'.$pj->getObject()->getNumber().'</td>';
            $subhtml .= '<td>#'.$pj->getTicket()->getNumber().'</td>';
            $subhtml .= '<td>'.date("d.m.Y H:i",$pj->getTicket()->getDuedate()).'</td>';
            $subhtml .= '<td>'.date("d.m.Y",$pj->getObject()->getDeliverydate()).'</td>';
            $subhtml .= '<td>'.number_format($pj->getTplanned(), 2, ",", "").'</td>';
            if ($pj->getTactual()>$pj->getTplanned())
                $style = ' style="background-color: red;"';
            $subhtml .= '<td '.$style.'>'.number_format($pj->getTactual(), 2, ",", "").'</td>';
            $subhtml .= '<td><span style="display: inline-block; font-size: medium; vertical-align: top; background-color: '.$pj->getTicket()->getState()->getColorcode().'" class="label">';
            $subhtml .= $pj->getTicket()->getState()->getTitle().'</span></td>';
            $subhtml .= '</tr>';
            $subhtml .= '</table>';
            $subhtml .= '</div>';


            if ($pj->getType()==2){
                $contents = Calculation::contentArray();
                $me = $pj->getMe();
                $calc = new Calculation($me->getCalcId());
                if ($calc->getId()>0){
                    foreach ($contents as $content) {
                        if ($content['const'] == $me->getPart()){
                            if ($calc->$content['id']()->getId()>0) {
                                $subhtml .= '<div class="table-responsive">';
                                $subhtml .= '<table class="table table-hover" id="art_table">';
                                $subhtml .= '<thead>';
                                $subhtml .= '<tr>';
                                $subhtml .= '<td>Material</td>';
                                $subhtml .= '<td>Grammatur</td>';
                                $subhtml .= '<td>Farbigkeit</td>';
                                $subhtml .= '<td>Bogenformat</td>';
                                $subhtml .= '<td>Produktformat</td>';
                                $subhtml .= '<td>Produktformat offen</td>';
                                $subhtml .= '<td>Nutzen</td>';
                                $subhtml .= '<td>Druckbogen insgesamt</td>';
                                $subhtml .= '</tr>';
                                $subhtml .= '</thead>';

                                $subhtml .= '<tr>';
                                $subhtml .= '<td>'.$calc->$content['id']()->getName().'</td>';
                                $subhtml .= '<td>'.$calc->$content['weight']().'g</td>';
                                $subhtml .= '<td>'.$calc->$content['chr']()->getName().'</td>';
                                $subhtml .= '<td>'.$calc->$content['width']().'mm x '.$calc->$content['height']().'mm</td>';
                                $subhtml .= '<td>'.$calc->getProductFormatWidth().'mm x '.$calc->getProductFormatHeight().'mm</td>';
                                $subhtml .= '<td>'.$calc->getProductFormatWidthOpen().'mm x '.$calc->getProductFormatHeightOpen().'mm</td>';
                                $subhtml .= '<td>'.$calc->getProductsPerPaper($content['const']).'</td>';
                                $subhtml .= '<td>'.printBigInt($calc->getPaperCount($content['const']) + $calc->$content['grant']()).'</td>';
                                $subhtml .= '</tr>';

                                $subhtml .= '</table>';
                                $subhtml .= '</div>';
                            }
                        }
                    }
                }
            }
            
            if ($pj->getType() == PlanningJob::TYPE_K && $statsenabled == 1)
            {
                if(array_key_exists($pj->getArtmach()->getId(), $stats))
                {
                    $stats[$pj->getArtmach()->getId()]['ptime'] += $pj->getTplanned();
                    $stats[$pj->getArtmach()->getId()]['atime'] += $pj->getTactual();
                } else {
                    $stats[$pj->getArtmach()->getId()] = Array('Name'=>$pj->getArtmach()->getName(),'ptime'=>$pj->getTplanned(),'atime'=>$pj->getTactual(),'avtime'=>$pj->getArtmach()->getRunningtimeForDay($day_start)/60/60);
                }
            }

            $subhtml .= '</div>';
        }
        if (count($stats)>0 && $statsenabled == 1)
        { 
            $statshtml .= '<div class=table-responsive" style="border-radius: 0px;margin-bottom:1px;"><b style="font-size: 12px;"><u>Statistik (soll / ist / verfügbar)</b></u>:</br>';
            $statshtml .= '<table width="100%">';
            foreach ($stats as $stat)
            {
                $statshtml .= '<tr><td style="font-size: 14px;" width="300">';
                $statshtml .= $stat['Name'] . ': ';
                $statshtml .= '</td><td style="font-size: 14px;">';
                if ($stat['atime']>=$stat['ptime'])
                    $statshtml .= printPrice($stat['ptime']) . ' / <b><font color="red">' . printPrice($stat['atime']) . '</font></b>';
                else 
                    $statshtml .= printPrice($stat['ptime']) . ' / ' . printPrice($stat['atime']);
                if ($stat['ptime']>=$stat['avtime'] || $stat['atime']>=$stat['avtime'])
                    $statshtml .= ' / <b><font color="red">' . printPrice($stat['avtime']) . '</font></b>&nbsp;<span class="glyphicons glyphicons-exclamation-sign"></span></br>';
                else 
                    $statshtml .= ' / ' . printPrice($stat['avtime']) . '</br>';
                $statshtml .= '</td></tr>';
            }
            $statshtml .= '</table></div>';
        }
    } else {
//            $subhtml .= '<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">keine Jobs</h3></div></div>';
    }

    if (count($jobs)>0){
        $html .= '<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">'.$date.'</h3></div><div class="panel-body">';
        if ($statsenabled == 1)
            $html .= $statshtml;
        $html .= $subhtml;
        $html .= '</div></div>';
    }
}

echo $html;

} else {
    $html = '';
    foreach ($dates as $date)
    {
        $day_start = mktime(0, 0, 0, date('m', strtotime($date)), date('d', strtotime($date)), date('Y', strtotime($date)));
        $day_end = mktime(23,59,59,date('m',strtotime($date)),date('d',strtotime($date)),date('Y',strtotime($date)));
        if ($type == "0")
            $jobs = PlanningJob::getAllJobs(" AND start >= {$day_start} AND start <= {$day_end} $vostring");
        else if ($type == "K")
        {
            $jobs = PlanningJob::getAllJobs(" AND type = 2 AND artmach = {$artmach} AND start >= {$day_start} AND start <= {$day_end} $vostring");
        }
        else if ($type == "V")
        {
            $jobs = PlanningJob::getAllJobs(" AND type = 1 AND artmach = {$artmach} AND start >= {$day_start} AND start <= {$day_end} $vostring");
        }
    
        $stats = Array();
        $statshtml = '';
        $subhtml = '';
        if (count($jobs)>0)
        {
            foreach ($jobs as $pj)
            {
                if ($pj->getStart() < time())
                    $subhtml .= '<div class="panel panel-warning" style="background-color: #f2dede;">';
                else
                    $subhtml .= '<div class="panel panel-default">';

                $subhtml .= '<div class="table-responsive">';
                $subhtml .= '<table class="table table-hover" id="art_table">';
                $subhtml .= '<thead style="font-weight: bold;">';
                $subhtml .= '<tr>';
                $subhtml .= '<td width="20">ID</td>';
                $subhtml .= '<td width="400">Objekt</td>';
                $subhtml .= '<td width="150">MA</td>';
                $subhtml .= '<td width="50">Vorgang</td>';
                $subhtml .= '<td width="50">Ticket</td>';
                $subhtml .= '<td width="90">Prod. Beginn</td>';
                $subhtml .= '<td width="80">Lief.-Datum</td>';
                $subhtml .= '<td width="50">S-Zeit</td>';
                $subhtml .= '<td width="50">I-Zeit</td>';
                $subhtml .= '<td width="80">Status</td>';
                $subhtml .= '</tr>';
                $subhtml .= '</thead>';
                $subhtml .= '<tr>';
                $subhtml .= '<td>#'.$pj->getId().'</td>';
                if ($pj->getType()==2)
                    $subhtml .= '<td>'.$pj->getArtmach()->getGroup()->getName() . ' - ' . $pj->getArtmach()->getName().'</td>';
                else
                    $subhtml .= '<td>'.$pj->getArtmach()->getTitle().'</td>';
                if ($pj->getAssigned_user()->getId()>0)
                    $subhtml .= '<td>'.$pj->getAssigned_user()->getNameAsLine().'</td>';
                else
                    $subhtml .= '<td>'.$pj->getAssigned_group()->getName().'</td>';
                $subhtml .= '<td><a target="_blank" href="index.php?page=libs/modules/collectiveinvoice/collectiveinvoice.php&exec=edit&ciid='.$pj->getObject()->getId().'">#'.$pj->getObject()->getNumber().'</a></td>';
                $subhtml .= '<td><a target="_blank" href="index.php?page=libs/modules/tickets/ticket.php&exec=edit&returnhome=1&tktid='.$pj->getTicket()->getId().'">#'.$pj->getTicket()->getNumber().'</a></td>';
                $subhtml .= '<td>'.date("d.m.Y H:i",$pj->getTicket()->getDuedate()).'</td>';
                $subhtml .= '<td>'.date("d.m.Y",$pj->getObject()->getDeliverydate()).'</td>';
                $subhtml .= '<td>'.number_format($pj->getTplanned(), 2, ",", "").'</td>';
                if ($pj->getTactual()>$pj->getTplanned())
                    $style = ' style="background-color: red;"';
                $subhtml .= '<td '.$style.'>'.number_format($pj->getTactual(), 2, ",", "").'</td>';
                $subhtml .= '<td><span style="display: inline-block; font-size: medium; vertical-align: top; background-color: '.$pj->getTicket()->getState()->getColorcode().'" class="label">';
                $subhtml .= $pj->getTicket()->getState()->getTitle().'</span></td>';
                $subhtml .= '<td>';
                if ($pj->getTicket()->getState()->getId() == TicketState::STATE_OPEN)
                {
                    $subhtml .= '<a onclick="popupshow(event,'.$pj->getId().');">verschieben</a> <span id="movetext_'.$pj->getId().'"></span>';
                } else {
                    $subhtml .= '&nbsp;';
                }
                $subhtml .= '</td>';
                $subhtml .= '</tr>';
                $subhtml .= '</table>';
                $subhtml .= '</div>';


                if ($pj->getType()==2){
                    $contents = Calculation::contentArray();
                    $me = $pj->getMe();
                    $calc = new Calculation($me->getCalcId());
                    if ($calc->getId()>0){
                        foreach ($contents as $content) {
                            if ($content['const'] == $me->getPart()){
                                if ($calc->$content['id']()->getId()>0) {
                                    $subhtml .= '<div class="table-responsive">';
                                    $subhtml .= '<table class="table table-hover" id="art_table">';
                                    $subhtml .= '<thead style="font-weight: bold;">';
                                    $subhtml .= '<tr>';
                                    $subhtml .= '<td>Material</td>';
                                    $subhtml .= '<td>Grammatur</td>';
                                    $subhtml .= '<td>Farbigkeit</td>';
                                    $subhtml .= '<td>Bogenformat</td>';
                                    $subhtml .= '<td>Produktformat</td>';
                                    $subhtml .= '<td>Produktformat offen</td>';
                                    $subhtml .= '<td>Nutzen</td>';
                                    $subhtml .= '<td>Druckbogen insgesamt</td>';
                                    $subhtml .= '</tr>';
                                    $subhtml .= '</thead>';

                                    $subhtml .= '<tr>';
                                    $subhtml .= '<td>'.$calc->$content['id']()->getName().'</td>';
                                    $subhtml .= '<td>'.$calc->$content['weight']().'g</td>';
                                    $subhtml .= '<td>'.$calc->$content['chr']()->getName().'</td>';
                                    $subhtml .= '<td>'.$calc->$content['width']().'mm x '.$calc->$content['height']().'mm</td>';
                                    $subhtml .= '<td>'.$calc->getProductFormatWidth().'mm x '.$calc->getProductFormatHeight().'mm</td>';
                                    $subhtml .= '<td>'.$calc->getProductFormatWidthOpen().'mm x '.$calc->getProductFormatHeightOpen().'mm</td>';
                                    $subhtml .= '<td>'.$calc->getProductsPerPaper($content['const']).'</td>';
                                    $subhtml .= '<td>'.printBigInt($calc->getPaperCount($content['const']) + $calc->$content['grant']()).'</td>';
                                    $subhtml .= '</tr>';

                                    $subhtml .= '</table>';
                                    $subhtml .= '</div>';
                                }
                            }
                        }
                    }
                }


    
                if ($pj->getType() == PlanningJob::TYPE_K && $statsenabled == 1)
                {
                    if(array_key_exists($pj->getArtmach()->getId(), $stats))
                    {
                        $stats[$pj->getArtmach()->getId()]['ptime'] += $pj->getTplanned();
                        $stats[$pj->getArtmach()->getId()]['atime'] += $pj->getTactual();
                    } else {
                        $stats[$pj->getArtmach()->getId()] = Array('Name'=>$pj->getArtmach()->getName(),'ptime'=>$pj->getTplanned(),'atime'=>$pj->getTactual(),'avtime'=>$pj->getArtmach()->getRunningtimeForDay($day_start)/60/60);
                    }
                }

                $subhtml .= '</div>';

            }
            if (count($stats)>0 && $statsenabled == 1)
            {
                $statshtml .= '<div class="box2" style="border-radius: 0px;margin-bottom:1px;"><b style="font-size: 12px;"><u>Statistik (soll / ist / verfügbar)</b></u>:</br>';
                $statshtml .= '<table width="100%">';
                foreach ($stats as $stat)
                {
                    $statshtml .= '<tr><td style="font-size: 14px;" width="300">';
                    $statshtml .= $stat['Name'] . ': ';
                    $statshtml .= '</td><td style="font-size: 14px;">';
                    if ($stat['atime']>=$stat['ptime'])
                        $statshtml .= printPrice($stat['ptime']) . ' / <b><font color="red">' . printPrice($stat['atime']) . '</font></b>';
                    else
                        $statshtml .= printPrice($stat['ptime']) . ' / ' . printPrice($stat['atime']);
                    if ($stat['ptime']>=$stat['avtime'] || $stat['atime']>=$stat['avtime'])
                        $statshtml .= ' / <b><font color="red">' . printPrice($stat['avtime']) . '</font></b>&nbsp;<span class="glyphicons glyphicons-exclamation-sign"></span></br>';
                    else
                        $statshtml .= ' / ' . printPrice($stat['avtime']) . '</br>';
                    $statshtml .= '</td></tr>';
                }
                $statshtml .= '</table></div>';
            }
        } else {
//            $subhtml .= '<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">keine Jobs</h3></div></div>';
        }

        if (count($jobs)>0){
            $html .= '<div class="panel panel-default"><div class="panel-heading"><h3 class="panel-title">'.$date.'</h3></div><div class="panel-body">';
            if ($statsenabled == 1)
                $html .= $statshtml;
            $html .= $subhtml;
            $html .= '</div></div>';
        }
    }
    echo $html;
}



?>


