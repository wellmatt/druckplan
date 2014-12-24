
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<?
error_reporting(-1);
ini_set('display_errors', 1);
if ($_REQUEST['id']){
    chdir('../thirdparty/formcraft');
    require_once('config.fc.php');
    require_once('function.php');
    formcraft($_REQUEST['id']);
}
?>