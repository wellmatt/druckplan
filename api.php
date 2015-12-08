<?php
error_reporting(-1);
ini_set('display_errors', 1);

require_once("config.php");
require_once("libs/basic/mysql.php");
require_once("libs/basic/globalFunctions.php");
require_once("libs/basic/user/user.class.php");
require_once("libs/basic/groups/group.class.php");
require_once("libs/basic/clients/client.class.php");
require_once("libs/basic/translator/translator.class.php");
require_once 'libs/basic/countries/country.class.php';
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
require_once 'libs/modules/api/api.class.php';

$DB = new DBMysql();
$DB->connect($_CONFIG->db);

if ($_REQUEST["token"])
{
    $token = $_REQUEST["token"];
    $api = API::findByToken($token);
    if ($api->getId()>0)
    {
        if ($_REQUEST["item"])
        {
            $item = (int)$_REQUEST["item"];
            if ($item>0)
                echo $api->returnApiValues($item);
        } else
        {
            echo $api->returnApiValues();
        }
    } else {
        echo "invalid token received!";
    }
} else {
    echo "no token received!";
}