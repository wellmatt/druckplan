<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       05.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
error_reporting(0);
ini_set('display_errors', 0);
//     error_reporting(-1);
//     ini_set('display_errors', 1);

require_once 'urlaub.class.php';
require_once 'Calendar/Month/Weekdays.php';
require_once 'Date/Holidays.php';
require_once 'Date/Holidays/Filter/'.$_USER->getClient()->getCountry()->getNameInt().'/Official.php';

//----------------------------------------------------------------------------------
// Hilfsfunktion zum sortieren
function orderBegin($a, $b)
{
    if ($a->getBegin() == $b->getBegin()) {
        return 0;
    }
    return ($a->getBegin() < $b->getBegin()) ? -1 : 1;
}

$users = User::getAllUser(User::ORDER_NAME);

if((int)$_REQUEST["sel_year"] > 0)
    $_SESSION["vac_year"] = (int)$_REQUEST["sel_year"];
if((int)$_REQUEST["sel_month"] > 0)
    $_SESSION["vac_month"] = (int)$_REQUEST["sel_month"];

if($_SESSION["vac_month"] == "")
    $_SESSION["vac_month"] = date('n');
if($_SESSION["vac_year"] == "")
    $_SESSION["vac_year"] = date('Y');

if($_REQUEST["exec"] == "delvacation")
{
    $vacation = new Urlaub((int)$_REQUEST["id"]);
    if($_USER->getId() == $vacation->getUser()->getId() || $_USER->isAdmin() || $_USER->hasRightsByGroup(Group::RIGHT_URLAUB))
        $savemsg = getSaveMessage($vacation->delete());
}

if ($_REQUEST["exec"] == "editvacation" || $_REQUEST["exec"] == "newvacation")
    require_once 'urlaub.edit.php';
else 
{
    $Month = new Calendar_Month($_SESSION["vac_year"], $_SESSION["vac_month"]);
    $Month->build();
    $Date = new Date();
    
    // Feiertage vorher berechnen. Sehr performancelastig, daher nur einmal am Anfang.
    //set up filter
    $filter = new Date_Holidays_Filter_Germany_Official();
    //then the driver
    $driver = &Date_Holidays::factory($_USER->getClient()->getCountry()->getNameInt(), $_SESSION["vac_year"]);
    $feiertage = Array();
    while ($day = $Month->fetch())
    {
        if($driver->isHoliday(mktime(0,0,0,$_SESSION["vac_month"], $day->thisDay(), $_SESSION["vac_year"]), $filter))
            $feiertage[$day->thisDay()] = 1;
    }
    
    ?>
    <link rel="stylesheet" type="text/css" href="./css/urlaub.css" />
    <form action="index.php?page=<?=$_REQUEST['page']?>" name="vacation_seldate_form" method="post" id="vacation_seldate_form">
    <table width="100%">
       <tr>
          <td width="200" class="content_header"><img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Urlaubsplanung')?></td>
          <td class="content_header"><?=$savemsg?></td>
          <td width="300" class="content_header" align="right">
              <?=$_LANG->get('Monat')?>:
              <select name="sel_month" style="width:40px" onchange="document.getElementById('vacation_seldate_form').submit()" class="text">
                  <? for ($i = 1; $i <= 12; $i++) {?>
                  <option value="<?=$i?>" <?if($i == $_SESSION["vac_month"]) echo "selected"?>><?=$i?></option>
                  <? } ?>
              </select>
              <?=$_LANG->get('Jahr')?>
              <select name="sel_year" style="width:80px" onchange="document.getElementById('vacation_seldate_form').submit()" class="text">
                  <? for ($i = date('Y')-2; $i <= date('Y')+1; $i++) {?>
                  <option value="<?=$i?>" <?if($i == $_SESSION["vac_year"]) echo "selected"?>><?=$i?></option>
                  <? } ?>
              </select>
          </td>
       </tr>
    </table>
    </form>
    <?
        if($_USER->isAdmin || $_USER->hasRightsByGroup(Group::RIGHT_URLAUB))
        {
            $openvacs = Urlaub::getAllVacations(Urlaub::STATE_UNSEEN);
            // Wartende Urlaube hinzuf�gen
            foreach(Urlaub::getAllVacations(Urlaub::STATE_WAIT) as $vac)
                array_push($openvacs, $vac);
            
            usort($openvacs, "orderBegin");
            if (count($openvacs))
            {
    ?>
    <div class="box1">
    <table width="100%" class="openUrlaubTable" cellpadding="0" cellspacing="0">
        <colgroup>
            <col width="250">
            <col width="80">
            <col width="80">
            <col width="100">
            <col>
            <col width="80">
            <col width="60">
        </colgroup>
        <tr>
            <td colspan="6" class="content_header"><?=$_LANG->get('Offene Urlaubsantr&auml;ge')?></td>
        </tr>    
        <tr>
            <td class="content_row_header"><?=$_LANG->get('Name')?></td>
            <td class="content_row_header"><?=$_LANG->get('Von')?></td>
            <td class="content_row_header"><?=$_LANG->get('Bis')?></td>
            <td class="content_row_header"><?=$_LANG->get('Grund')?></td>
            <td class="content_row_header"><?=$_LANG->get('Bemerkung')?></td>
            <td class="content_row_header"><?=$_LANG->get('Status')?></td>
            <td class="content_row_header"><?=$_LANG->get('Optionen')?></td>
        </tr>
    <? $x =1;
    foreach($openvacs as $vac)
    {
        echo '<tr class="'.getRowColor($x).'">
            <td class="content_row">'.$vac->getUser()->getNameAsLine().'</td>
            <td class="content_row">'.date('d.m.Y', $vac->getBegin()).'</td>
            <td class="content_row">'.date('d.m.Y', $vac->getEnd()).'</td>
            <td class="content_row">';
        
        if($vac->getReason() == Urlaub::TYPE_KRANKHEIT) echo $_LANG->get('Krankheit');
        if($vac->getReason() == Urlaub::TYPE_URLAUB) echo $_LANG->get('Urlaub');
        if($vac->getReason() == Urlaub::TYPE_UEBERSTUNDEN) echo $_LANG->get('&Uuml;berstunden');
        if($vac->getReason() == Urlaub::TYPE_SONSTIGES) echo $_LANG->get('Sonstiges');
        
        echo '</td>
            <td class="content_row">'.$vac->getNotes().'&nbsp;</td>
            <td class="content_row">';
        if ($vac->getState() == Urlaub::STATE_UNSEEN) echo $_LANG->get('Offen');
        if ($vac->getState() == Urlaub::STATE_WAIT) echo $_LANG->get('Wartend');
        echo '</td>
            <td class="content_row">
                <a class="icon-link" href="index.php?page='.$_REQUEST['page'].'&exec=editvacation&id='.$vac->getId().'"><img src="images/icons/pencil.png" /></a>
            </td>
        </tr>';
        $x++;
    }
    ?>
    
    </table>
    </div>
    <?  } } ?>
    <br>
    <div class="box1">
    <table width="100%" class="urlaubTable" cellpadding="0" cellspacing="0">
        <tr>
            <td class="content_header" colspan="6"><?=$_LANG->get('Urlaubskalender')?></td>
        </tr>
        <tr>
            <td class="urlaubHeader" style="border-top:1px solid;border-left:1px solid;border-right:1px solid">&nbsp;</td>
            <td class="urlaubHeader" style="border-top:1px solid;border-right:1px solid">&nbsp;</td> <!-- Genommene / Restliche Urlaubstage -->
            <td width="5">&nbsp;</td> <!-- trenner -->
            <? while ($day = $Month->fetch()) { 
            ?>
                <td class="urlaubHeaderDays<?if($day->thisDay() == 1) echo "First"?> 
                    <?if(isWeekend(date('N', mktime(0,0,0,$_SESSION["vac_month"], $day->thisDay(), $_SESSION["vac_year"])))) echo "weekend"?>
                    <? if(array_key_exists($day->thisDay(), $feiertage)) echo " feiertag";?>">
                    <?=$day->thisDay()?>
                </td>
            <? } ?>
        </tr>
        <tr>
            <td class="urlaubHeader" style="border-left:1px solid;border-bottom:1px solid;border-right:1px solid"><?=$_LANG->get('Benutzer')?></td>
            <td class="urlaubHeader" style="border-right:1px solid;border-bottom:1px solid">&nbsp;</td> <!-- Genommene / Restliche Urlaubstage -->
            <td width="5">&nbsp;</td> <!-- trenner -->
            <? while ($day = $Month->fetch()) { ?>
                <td class="urlaubHeaderDaysNames<?if($day->thisDay() == 1) echo "First"?>
                    <?if(isWeekend(date('N', mktime(0,0,0,$_SESSION["vac_month"], $day->thisDay(), $_SESSION["vac_year"])))) echo "weekend"?>
                    <? if(array_key_exists($day->thisDay(), $feiertage)) echo " feiertag";?>">
                    <?=getDayNameForDayOfWeek(date('N', mktime(0,0,0,$_SESSION["vac_month"], $day->thisDay(), $_SESSION["vac_year"])))?>
                </td>
            <? } ?>
        </tr>
        
        <?
        // REIHEN START 
        //-----------------------------------------------------------------------
        $i = 0;
        foreach($users as $u) 
        { 
            ?>
            <tr class="color<?=$i?>">
                <td class="urlaubNames"><?=$u->getNameAsLine()?></td>
                <td class="urlaubDaysLeft">
                    <? if($_USER->getId() == $u->getId() || $_USER->isAdmin() || $_USER->hasRightsByGroup(Group::RIGHT_URLAUB))
                        echo Urlaub::getSumVacationDays($_SESSION["vac_year"], $u)?>&nbsp;
                </td> <!-- Genommene / Restliche Urlaubstage -->
                <td>&nbsp;</td> <!-- Genommene / Restliche Urlaubstage -->
                <? while ($day = $Month->fetch()) 
                { 
                    $vacOnDay = 0;
                    $vacOnDay = Urlaub::isVacationOnDay($u, $day->thisDay(), $_SESSION["vac_month"], $_SESSION["vac_year"]);
                    if ($vacOnDay)
                        $vac = new Urlaub($vacOnDay);
                    else
                        $vac = new Urlaub(0);
                    
                    //---------------------------------------------------------------------------------------------
                    // Datumsfeld
                    ?>
                
                    <td class="urlaubDays<?if($day->thisDay() == 1) echo "First"?> 
                        <?
                        if(isWeekend(date('N', mktime(0,0,0,$_SESSION["vac_month"], $day->thisDay(), $_SESSION["vac_year"])))) 
                            echo " weekend"; 
                        if(array_key_exists($day->thisDay(), $feiertage))
                            echo " feiertag";
                        echo $vac->getState();
                        if ($vac->getState() == Urlaub::STATE_UNSEEN)
                            echo " unseen";
                        elseif ($vac->getState() == Urlaub::STATE_WAIT)
                            echo " wait";
                        elseif ($vac->getState() == Urlaub::STATE_APPROVED)
                            echo " approved";
                        
                        if ($vacOnDay) 
                        {
                            //------------------------------------------------------------------------------------------------
                            // edit or show vacation 
                            if ($vac->getUser()->getId() == $_USER->getId() || $_USER->isAdmin() || $_USER->hasRightsByGroup(Group::RIGHT_URLAUB))
                            {
                        ?>
                                pointer"
                                onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=editvacation&id=<?=$vac->getId()?>'"
                                onmouseover="hilight(this, 1)" onmouseout="hilight(this, 0)">
                               
                        <?
                            } else
                            {
                                echo '">';
                            }
                            switch($vac->getReason())
                            {
                                case Urlaub::TYPE_URLAUB:
                                    echo "U"; break;
                                case Urlaub::TYPE_KRANKHEIT:
                                    echo "K"; break;
                                case Urlaub::TYPE_UEBERSTUNDEN:
                                    echo "M"; break;
                                case Urlaub::TYPE_SONSTIGES:
                                    echo "S"; break;
                            }
                            
                        } else {
                            //------------------------------------------------------------------------------------------------
                            // create new vacation
                            if ($u->getId() == $_USER->getId() || $_USER->isAdmin() || $_USER->hasRightsByGroup(Group::RIGHT_URLAUB))
                            { 
                        ?>
                                pointer" 
                                onclick="document.location='index.php?page=<?=$_REQUEST['page']?>&exec=newvacation&uid=<?=$u->getId()?>&day=<?=$day->thisDay()?>'"
                                onmouseover="hilight(this, 1)" onmouseout="hilight(this, 0)">
                        <?  
                            } else
                            {
                                echo '">';
                            }
                                
                        }
                        ?>&nbsp;
                    </td>
                <? }
                // Datumsfeld Ende
                //-----------------------------------------------------------------------------------------?>
            </tr> 
        <? 
            $i++;
        } // REIHE ENDE ?>
    </table>
    </div>
<?  } ?>