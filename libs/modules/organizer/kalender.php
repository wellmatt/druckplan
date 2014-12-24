<? 
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
$path = $_BASEDIR . 'thirdparty/';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
require_once 'event.class.php';
require_once 'Calendar/Month/Weekdays.php';
require_once 'Date/Holidays.php';
require_once 'Date/Holidays/Filter/'.$_USER->getClient()->getCountry()->getNameInt().'/Official.php';

if((int)$_REQUEST["sel_year"] > 0)
    $_SESSION["cal_year"] = (int)$_REQUEST["sel_year"];
if((int)$_REQUEST["sel_month"] > 0)
    $_SESSION["cal_month"] = (int)$_REQUEST["sel_month"];

if($_SESSION["cal_month"] == "")
    $_SESSION["cal_month"] = date('n');
if($_SESSION["cal_year"] == "")
    $_SESSION["cal_year"] = date('Y');
	
$month_names = array("Januar","Februar","März","April","Mai","Juni","Juli","August","September","Oktober","November","Dezember");



$weekNum = (int)date('W', mktime(0,0,0,$_SESSION["cal_month"],1,$_SESSION["cal_year"]));

if ($_REQUEST["exec"] == "delevent")
{
    $_REQUEST["id"] = (int)$_REQUEST["id"];
    $event = new Event($_REQUEST["id"]);
    if ($event->getUser()->getId() == $_USER->getId())
    {
        $savemsg = getSaveMessage($event->delete());
        $_REQUEST["exec"] = "";
    }
}

if($_REQUEST["exec"] == "showday")
{
    require_once 'kalender.showday.php';
} elseif($_REQUEST["exec"] == "showevent")
{
    require_once 'kalender.showevent.php';
} else if ($_REQUEST["exec"] == "newevent")
{
    require_once 'kalender.newevent.php';
} else  
{


// Feiertage vorher berechnen. Sehr performancelastig, daher nur einmal am Anfang.
//set up filter
$filter = new Date_Holidays_Filter_Germany_Official();
//then the driver
$driver = &Date_Holidays::factory($_USER->getClient()->getCountry()->getNameInt(), $_SESSION["cal_year"]);
$feiertage = Array();

global $_USER;
if((int)$_REQUEST["sel_user"] > 0)
    $sel_user = new User((int)$_REQUEST["sel_user"]);
else
	$sel_user = $_USER;



?>

<link rel="stylesheet" type="text/css" href="./css/calendar.css" />
<form action="index.php?page=<?=$_REQUEST['page']?>" method="post" id="cal_seldate_form">
<input type="hidden" name="exec" value="<?=$_REQUEST["exec"]?>">
<table width="100%">
    <tr>
        <td width="300" class="content_header">
            <img src="<?=$_MENU->getIcon($_REQUEST['page'])?>"> <?=$_LANG->get('Kalender')?>
        </td>
        <td class="content_header"><?=$savemsg?></td>
		<td width="250" class="content_header">
		<?
		if ($_USER->hasRightsByGroup(Group::RIGHT_ALL_CALENDAR)) {
			$users = User::getAllUser(User::ORDER_LOGIN);
			?>
            F&uuml;r Benutzer: <select name="sel_user" style="width:150px" onchange="document.getElementById('cal_seldate_form').submit()" class="text">
                <? foreach ($users as $user) {?>
                <option value="<?=$user->getId()?>" 
				<?
					if($user->getId() == $_REQUEST["sel_user"]) 
						echo "selected>";
					elseif($user->getId() == $_USER->getId() && !$_REQUEST["sel_user"])
						echo "selected>";
				?>
				><?=$user->getFirstname()?> <?=$user->getLastname()?></option>
                <? } ?>
            </select>
			<?
		}
		?>
		</td>
        <td width="300" class="content_header" align="right">
            <?=$_LANG->get('drei Monate ab Monat ')?>:

            <select name="sel_month" style="width:40px" onchange="document.getElementById('cal_seldate_form').submit()" class="text">
                <? for ($i = 1; $i <= 12; $i++) {?>
                <option value="<?=$i?>" <?if($i == $_SESSION["cal_month"]) echo "selected"?>><?=$i?></option>
                <? } ?>
            </select>
            <?=$_LANG->get('Jahr')?>
            <select name="sel_year" style="width:80px" onchange="document.getElementById('cal_seldate_form').submit()" class="text">
                <? for ($i = date('Y')-2; $i <= date('Y')+1; $i++) {?>
                <option value="<?=$i?>" <?if($i == $_SESSION["cal_year"]) echo "selected"?>><?=$i?></option>
                <? } ?>
            </select>
        </td>
    </tr>
</table>
</form>
<?
$cal_month = $_SESSION["cal_month"];
$cal_year = $_SESSION["cal_year"];
$Month = new Calendar_Month_Weekdays($cal_year, $cal_month);
$Month->build();
while ($day = $Month->fetch())
{
    if($driver->isHoliday(mktime(0,0,0,$cal_month, $day->thisDay(), $cal_year), $filter))
        $feiertage[$day->thisDay()] = 1;
}
echo '<h2>'.$month_names[$cal_month-1].'</h2>';
?>
<table width="100%">
    <tr>
        <td>&nbsp;</td>
        <td class="weekDay"><?=$_LANG->get('Montag')?></td>
        <td class="weekDay"><?=$_LANG->get('Dienstag')?></td>
        <td class="weekDay"><?=$_LANG->get('Mittwoch')?></td>
        <td class="weekDay"><?=$_LANG->get('Donnerstag')?></td>
        <td class="weekDay"><?=$_LANG->get('Freitag')?></td>
        <td class="weekDay"><?=$_LANG->get('Samstag')?></td>
        <td class="weekDay"><?=$_LANG->get('Sonntag')?></td>
    </tr>

<?
while ($Day = $Month->fetch()) {

    if ($Day->isFirst()) {
        echo "<tr>\n<td class=\"weekNum\">".$weekNum."</td>\n";
        $weekNum++;
        $i = 1;
    }
    
    if ($Day->isEmpty()) {
        echo "<td class=\"calendarDayOut"; if (isWeekend($i)) echo "Weekend"; echo "\">".$Day->thisDay()."</td>\n";
    } else {
        $events = Event::getAllEventsOnDay($Day->thisDay(), $cal_month, $cal_year, $sel_user);
        echo '<td class="calendarDay'; 
        if (isWeekend($i) || array_key_exists($Day->thisDay(), $feiertage)) echo "Weekend";
        echo ' pointer" id="day_'.$Day->thisDay().'"';
        if ($events)
        {
            echo ' onclick="document.location=\'index.php?page='.$_REQUEST['page'].'&exec=showday&day='.$Day->thisDay().'&cal_month='.$cal_month.'&cal_year='.$cal_year.'\'"';
        }
            echo ' onclick="document.location=\'index.php?page='.$_REQUEST['page'].'&exec=newevent&day='.$Day->thisDay().'&cal_month='.$cal_month.'&cal_year='.$cal_year.'\'"';
        echo '>';
        echo $Day->thisDay()."<br>";
        if($events)
        {
            echo '<img src="images/icons/clock.png" /> ';
            if(count($events) == 1)
                echo "1 ".$_LANG->get('Termin');
            else
                echo count($events)." ".$_LANG->get('Termine');
        }
        echo "</td>\n";
    }

    if ($Day->isLast()) {
        echo "</tr>\n";
    }
    $i++;
}
?>
</table>
<?
if ($cal_month == 12) {
	$cal_month = 1;
	$cal_year = $cal_year +1;
} else {
	$cal_month = $cal_month +1;
}
$Month2 = new Calendar_Month_Weekdays($cal_year, $cal_month);
$Month2->build();
while ($day = $Month2->fetch())
{
    if($driver->isHoliday(mktime(0,0,0,$cal_month, $day->thisDay(), $cal_year), $filter))
        $feiertage[$day->thisDay()] = 1;
}
echo '<h2>'.$month_names[$cal_month-1].'</h2>';
?>
<table width="100%">
    <tr>
        <td>&nbsp;</td>
        <td class="weekDay"><?=$_LANG->get('Montag')?></td>
        <td class="weekDay"><?=$_LANG->get('Dienstag')?></td>
        <td class="weekDay"><?=$_LANG->get('Mittwoch')?></td>
        <td class="weekDay"><?=$_LANG->get('Donnerstag')?></td>
        <td class="weekDay"><?=$_LANG->get('Freitag')?></td>
        <td class="weekDay"><?=$_LANG->get('Samstag')?></td>
        <td class="weekDay"><?=$_LANG->get('Sonntag')?></td>
    </tr>
<?
while ($Day = $Month2->fetch()) {

    if ($Day->isFirst()) {
        echo "<tr>\n<td class=\"weekNum\">".$weekNum."</td>\n";
        $weekNum++;
        $i = 1;
    }
    
    if ($Day->isEmpty()) {
        echo "<td class=\"calendarDayOut"; if (isWeekend($i)) echo "Weekend"; echo "\">".$Day->thisDay()."</td>\n";
    } else {
        $events = Event::getAllEventsOnDay($Day->thisDay(), $cal_month, $cal_year, $sel_user);
        echo '<td class="calendarDay'; 
        if (isWeekend($i) || array_key_exists($Day->thisDay(), $feiertage)) echo "Weekend";
        echo ' pointer" id="day_'.$Day->thisDay().'"';
        if ($events)
        {
            echo ' onclick="document.location=\'index.php?page='.$_REQUEST['page'].'&exec=showday&day='.$Day->thisDay().'&cal_month='.$cal_month.'&cal_year='.$cal_year.'\'"';
        }
            echo ' onclick="document.location=\'index.php?page='.$_REQUEST['page'].'&exec=newevent&day='.$Day->thisDay().'&cal_month='.$cal_month.'&cal_year='.$cal_year.'\'"';
        echo '>';
        echo $Day->thisDay()."<br>";
        if($events)
        {
            echo '<img src="images/icons/clock.png" /> ';
            if(count($events) == 1)
                echo "1 ".$_LANG->get('Termin');
            else
                echo count($events)." ".$_LANG->get('Termine');
        }
        echo "</td>\n";
    }

    if ($Day->isLast()) {
        echo "</tr>\n";
    }
    $i++;
}
?>
</table>
<?
if ($cal_month == 12) {
	$cal_month = 1;
	$cal_year = $cal_year +1;
} else {
	$cal_month = $cal_month +1;
}
$Month3 = new Calendar_Month_Weekdays($cal_year, $cal_month);
$Month3->build();
while ($day = $Month3->fetch())
{
    if($driver->isHoliday(mktime(0,0,0,$cal_month, $day->thisDay(), $cal_year), $filter))
        $feiertage[$day->thisDay()] = 1;
}
echo '<h2>'.$month_names[$cal_month-1].'</h2>';
?>
<table width="100%">
    <tr>
        <td>&nbsp;</td>
        <td class="weekDay"><?=$_LANG->get('Montag')?></td>
        <td class="weekDay"><?=$_LANG->get('Dienstag')?></td>
        <td class="weekDay"><?=$_LANG->get('Mittwoch')?></td>
        <td class="weekDay"><?=$_LANG->get('Donnerstag')?></td>
        <td class="weekDay"><?=$_LANG->get('Freitag')?></td>
        <td class="weekDay"><?=$_LANG->get('Samstag')?></td>
        <td class="weekDay"><?=$_LANG->get('Sonntag')?></td>
    </tr>
<?
while ($Day = $Month3->fetch()) {

    if ($Day->isFirst()) {
        echo "<tr>\n<td class=\"weekNum\">".$weekNum."</td>\n";
        $weekNum++;
        $i = 1;
    }
    
    if ($Day->isEmpty()) {
        echo "<td class=\"calendarDayOut"; if (isWeekend($i)) echo "Weekend"; echo "\">".$Day->thisDay()."</td>\n";
    } else {
        $events = Event::getAllEventsOnDay($Day->thisDay(), $cal_month, $cal_year, $sel_user);
        echo '<td class="calendarDay'; 
        if (isWeekend($i) || array_key_exists($Day->thisDay(), $feiertage)) echo "Weekend";
        echo ' pointer" id="day_'.$Day->thisDay().'"';
        if ($events)
        {
            echo ' onclick="document.location=\'index.php?page='.$_REQUEST['page'].'&exec=showday&day='.$Day->thisDay().'&cal_month='.$cal_month.'&cal_year='.$cal_year.'\'"';
        }
            echo ' onclick="document.location=\'index.php?page='.$_REQUEST['page'].'&exec=newevent&day='.$Day->thisDay().'&cal_month='.$cal_month.'&cal_year='.$cal_year.'\'"';
        echo '>';
        echo $Day->thisDay()."<br>";
        if($events)
        {
            echo '<img src="images/icons/clock.png" /> ';
            if(count($events) == 1)
                echo "1 ".$_LANG->get('Termin');
            else
                echo count($events)." ".$_LANG->get('Termine');
        }
        echo "</td>\n";
    }

    if ($Day->isLast()) {
        echo "</tr>\n";
    }
    $i++;
}
?>
</table>

<?  } ?>