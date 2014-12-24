<?
//----------------------------------------------------------------------------------
// Author:        iPactor GmbH
// Updated:       22.05.2012
// Copyright:     2012 by iPactor GmbH. All Rights Reserved.
// Any unauthorized redistribution, reselling, modifying or reproduction of part
// or all of the contents in any form is strictly prohibited.
//----------------------------------------------------------------------------------
chdir("../../../");
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';

function isHalfDay($date) {
    // Christmas Eve
    if(date('d', $date) == 24 && date('m', $date) == 12)
        return true;
    // Silvester
    if(date('d', $date) == 31 && date('m', $date) == 12)
        return true;
    return false;
}

session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);

error_reporting(E_ALL &~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
require_once 'Date/Holidays.php';
require_once 'Date/Holidays/Filter/'.$_USER->getClient()->getCountry()->getNameInt().'/Official.php';

//set up filter
$filter = new Date_Holidays_Filter_Germany_Official();
//then the driver
$driver = &Date_Holidays::factory($_USER->getClient()->getCountry()->getNameInt(), $_SESSION["vac_year"]);

$_REQUEST["from"]       = explode(".", $_REQUEST["from"]);
$_REQUEST["from"]       = (int)mktime(0, 0, 0, $_REQUEST["from"][1], $_REQUEST["from"][0], $_REQUEST["from"][2]);

$_REQUEST["to"]       = explode(".", $_REQUEST["to"]);
$_REQUEST["to"]       = (int)mktime(0, 0, 0, $_REQUEST["to"][1], $_REQUEST["to"][0], $_REQUEST["to"][2]);

$temp = $_REQUEST["from"];
$days = 0;
$daystep = 60 * 60 * 24;
while ($temp < $_REQUEST["to"] + $daystep)
{
    if (!isWeekend(date('w', $temp)))
    {
        if (!$driver->isHoliday($temp, $filter) && !isHalfDay($temp))
            $days++;
        if (!$driver->isHoliday($temp, $filter) && isHalfDay($temp))
            $days += 0.5;            
    }
    $temp += $daystep;
} 

echo $days;

?>
