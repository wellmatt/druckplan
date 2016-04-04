<?php
error_reporting(-1);
ini_set('display_errors', 1);

require_once 'config.php';
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once("libs/basic/countries/country.class.php");
require_once 'libs/basic/cachehandler/cachehandler.class.php';
require_once 'thirdparty/phpfastcache/phpfastcache.php';
require_once 'libs/modules/organizer/contact.class.php';
require_once 'libs/modules/businesscontact/businesscontact.class.php';
require_once 'libs/modules/chat/chat.class.php';
require_once 'libs/modules/calculation/order.class.php';
require_once 'libs/modules/schedule/schedule.class.php';
require_once 'libs/modules/tickets/ticket.class.php';
require_once 'libs/modules/comment/comment.class.php';
require_once 'libs/modules/abonnements/abonnement.class.php';
require_once 'libs/basic/model.php';
require_once 'libs/modules/storage/storage.area.class.php';


session_start();

$DB = new DBMysql();
$DB->connect($_CONFIG->db);
global $_LANG;

// Login
if ($_REQUEST["userid"]){
    $_USER = new User((int)$_REQUEST["userid"]);
} else {
    $_USER = new User();
    $_USER = User::login($_SESSION["login"], $_SESSION["password"], $_SESSION["domain"]);
}

if ($_USER == false){
    error_log("Login failed (basic-importer.php)");
    die("Login failed");
}

$test = 1;


//class Test extends Model {
//    public $_table = 'test';
//    public $a = 0;
//    public $b = 1;
//    public $c = 2;
//    public $d = 'test';
//    public $user = 0;
//
//    protected function bootClasses()
//    {
//        $this->user = new User($this->user);
//    }
//}

//$test = new Test(3);
//prettyPrint($test->save());