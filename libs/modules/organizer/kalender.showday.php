<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$tme = mktime(0,0,0,$_REQUEST["cal_month"], $_REQUEST["day"], $_REQUEST["cal_year"]);
$tmeEnd = mktime(0,0,0,$_REQUEST["cal_month"], $_REQUEST["day"], $_REQUEST["cal_year"]) + 60*60*24;

$events = Event::getAllEventsOnDay($_REQUEST["day"], $_REQUEST["cal_month"], $_REQUEST["cal_year"]);

?>

<link rel="stylesheet" type="text/css" href="./css/calendar.css" />
<table width="100%">
    <tr>
        <td width="300" class="content_header">
            <img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Kalender');?> - <?=$_LANG->get('Tagesansicht')?>
            <?=$_REQUEST["day"]?>.<?=$_REQUEST["cal_month"]?>.<?=$_REQUEST["cal_year"]?>
        </td>
        <td class="content_header"><?=$savemsg?></td>
    </tr>
</table>

<table width="100%" cellspacing="0" cellpadding="0">
<? 
    $i = 0;
    $seen = Array();
    while ($tme < $tmeEnd)
    {
        unset($termine);
        $termine = Array();
        // Welche Termine gibt es in diesem Zeitslot?
        for ($x = 0; $x < count($events); $x++)
        {
            //echo $events[$x]->getBegin()." - ".$tme."<br>";
            if ($events[$x]->getBegin() <= $tme && $events[$x]->getEnd() > $tme)
                $termine[$x] = $events[$x];
            else
                $termine[$x] = false; 
        }
        
        echo "<tr>\n";
        if($i % 2 == 0) echo "<td class=\"dayviewHour\" rowspan=\"2\">".date('H:i', $tme)."</td>\n";
        for ($x = 0; $x < count($events); $x++)
        {
            if ($termine[$x] !== false)
            {
                echo "<td class=\"dayviewEventHour pointer\"";
                if ($events[$x]->getUser()->getId() == $_USER->getId()) echo " onclick=\"document.location='index.php?page=".$_REQUEST['page']."&exec=newevent&id=".$events[$x]->getId()."'\">";
                    else echo " onclick=\"document.location='index.php?page=".$_REQUEST['page']."&exec=showevent&id=".$events[$x]->getId()."&day=".$_REQUEST["day"]."'\">";
                if ($seen[$termine[$x]->getId()] == false)
                {
                    echo "<b>".$termine[$x]->getTitle()."</b> - <i>".$termine[$x]->getUser()->getNameAsLine()."</i><br>".$termine[$x]->getDesc();
                    $seen[$termine[$x]->getId()] = true;
                }
                echo "</td>\n";
            } else 
                echo "<td class=\"dayviewEmptyHour pointer\" 
                    onclick=\"document.location='index.php?page=".$_REQUEST['page']."&exec=newevent&day=".$_REQUEST["day"]."&hour=".date('H:i', $tme)."'\">&nbsp;</td>\n";
        }
        echo "<td class=\"dayviewEmptyHour pointer\"
            onclick=\"document.location='index.php?page=".$_REQUEST['page']."&exec=newevent&day=".$_REQUEST["day"]."&hour=".date('H:i', $tme)."'\">&nbsp;</td>\n";
        echo "</tr>\n";
        $tme += 60*30;
        $i++;        
    }
    
?>
</table>