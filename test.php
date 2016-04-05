<?php
require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
require_once 'libs/modules/paper/paper.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/foldtypes/foldtype.class.php';
require_once 'libs/modules/paperformats/paperformat.class.php';
require_once 'libs/modules/products/product.class.php';
require_once 'libs/modules/machines/machine.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/chromaticity/chromaticity.class.php';
require_once 'libs/modules/calculation/calculation.class.php';
require_once 'libs/modules/finishings/finishing.class.php';
require_once 'libs/modules/article/article.class.php';
require_once 'libs/modules/collectiveinvoice/orderposition.class.php';
require_once 'libs/modules/personalization/personalization.order.class.php';
session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
$_USER = new User();
$_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
$_LANG = $_USER->getLang();


error_reporting(-1);
ini_set('display_errors', 1);

class TestWupp
{

    private $id;
    private $name;
    private $a;
    private $b;

    /**
     * @param int $test
     * @param string $test2
     */
    public function test123($test = 0, $test2 = '')
    {

    }

    /**
     * @param int $test
     * @return bool
     */
    public function test321($test = 0)
    {
        return true;
    }

    /**
     * @param $timestamp
     * @return int
     */
    public static function ColinvCountDay($timestamp)
    {
        global $DB;
        $count = 0;

        $start = mktime(0, 0, 0, date('m', $timestamp), date('d', $timestamp), date('Y', $timestamp));
        $end = mktime(23, 59, 59, date('m', $timestamp), date('d', $timestamp), date('Y', $timestamp));

        $sql = "SELECT count(id) as count FROM `collectiveinvoice`
                where crtdate >= {$start}
                and crtdate <= {$end}";

        if ($DB->no_result($sql)) {
            $result = $DB->select($sql);
            $r = $result[0];
            $count = $r['count'];
        }
        return $count;
    }
}

$tage = Array(1452678017,1452579011);

foreach ($tage as $item) {
    $retval = TestWupp::ColinvCountDay($item);
    echo 'Tag: '.date('d-m-Y',$item).' // Anzahl: '.$retval;
    echo '</br>';
}
