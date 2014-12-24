<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       06.03.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------

require_once 'libs/modules/organizer/event.class.php';
require_once 'Calendar/Month/Weekdays.php';
require_once 'Date/Holidays.php';
$_SESSION["cal_year"] = date('Y');
$_SESSION["cal_month"] = date('n');

$tme = mktime(0,0,0,date('j'), date('n'), date('Y'));
$tmeEnd = mktime(0,0,0,date('j'), date('n'), date('Y')) + 60*60*24;
$events = Event::getAllEventsOnDay(date('j'), date('n'), date('Y'), null, true);
$tage = array("Sonntag", "Montag", "Dienstag", "Mittwoch", "Donnerstag", "Freitag", "Samstag");
$monate = array(1=>"Januar",
                2=>"Februar",
                3=>"M&auml;rz",
                4=>"April",
                5=>"Mai",
                6=>"Juni",
                7=>"Juli",
                8=>"August",
                9=>"September",
                10=>"Oktober",
                11=>"November",
                12=>"Dezember");
// echo "Debug: " . date('j');
// print_r($events);
?>

<link rel="stylesheet" type="text/css" href="./css/calendar.css" />
<table width="100%">
    <tr>
        <td width="300" class="content_header" valign="left"><h1>
            <?=$_LANG->get('Tagesansicht')?> - <? echo $tage[date('w')] . ", " . date('j') . ". " . $monate[date('n')] . " " . date('Y'); ?>
			<img src="images/page/icon_kalender.png" width="32" height="32">
			</h1>
        </td>
        <td class="content_header"><?=$savemsg?></td>
    </tr>
</table>

<table width="100%" cellspacing="0" cellpadding="0">
<? 

if ($events != false) {
	for ($x = 0; $x < count($events); $x++)
	{
		if ($events[$x] !== false)
		{
			?>
			<tr class="tabellenlinie">
				<td><?=date('H:i',$events[$x]->getBegin())?> - <?=date('H:i',$events[$x]->getEnd())?> Uhr </br>&nbsp;</td>
				<?
				if($events[$x]->getOrder() == 0 && $events[$x]->getTicket() == 0) {
					?>
					<td><a href='index.php?page=libs/modules/organizer/calendar.php&exec=newevent&amp;id=<?=$events[$x]->getId()?>'><b><?=$events[$x]->getTitle()?></b></a></br>&nbsp;</td>
					<?
				}
				else if ($events[$x]->getOrder() != 0) { // ?exec=edit&id=89&step=4
					?> 
					<td><a href='index.php?page=libs/modules/calculation/order.php&exec=edit&amp;id=<?=$events[$x]->getOrder()->getId()?>&amp;step=4'><b><?=$events[$x]->getTitle()?></b></a></br>&nbsp;</td>
					<?
				}
				else { // ?exec=edit&tktid=535
					?>
					<td><a href='index.php?page=libs/modules/tickets/tickets.php&exec=edit&amp;tktid=<?=$events[$x]->getTicket()->getId()?>'><b><?=$events[$x]->getTitle()?></b></a></br>&nbsp;</td>
					<?
				}
				?>
			</tr>
			<?
		}
	}
}
?>
	<tr class="tabellenlinie">
		<td colspan="2">
		<center>
			<a href='index.php?page=libs/modules/organizer/calendar.php'>zum Kalender</a> | <a href='index.php?page=libs/modules/organizer/calendar.php&exec=newevent'>neuer Termin</a>
		</center>
		</td>
	</tr>
</table>