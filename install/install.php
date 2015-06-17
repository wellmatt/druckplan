<?php
// error_reporting(-1);
// ini_set('display_errors', 1);
require_once('github.updater.class.php');

ini_set('output_buffering', 'off');
ini_set('zlib.output_compression', false);
ob_end_flush();
header('Cache-Control: no-cache');

// Get all submitted data
// Client -->
$cl_name = $_REQUEST["name"];
$cl_adr1 = $_REQUEST["adr1"];
$cl_adr2 = $_REQUEST["adr2"];
$cl_adr3 = $_REQUEST["adr3"];
$cl_plz = $_REQUEST["plz"];
$cl_city = $_REQUEST["city"];
// Client end-->
// DB -->
$db_host = $_REQUEST["db_host"];
$db_name = $_REQUEST["db_name"];
$db_user = $_REQUEST["db_user"];
$db_pass = $_REQUEST["db_pass"];
// DB end-->
// User -->
$usr_username = $_REQUEST["usr_username"];
$usr_fname = $_REQUEST["usr_fname"];
$usr_lname = $_REQUEST["usr_lname"];
$usr_pass = $_REQUEST["usr_pass"];
$usr_pass_md5 = md5($usr_pass);
$usr_mail = $_REQUEST["usr_mail"];
// User end-->

$tmp_path = __DIR__ . "/tmp";
$app_path = str_replace("/install", "", __DIR__);

// Check folder perms

echo "Checking if the application path is writable...</br>";
if (is_writable($app_path)) {
    echo 'Application path is writable.</br>';
} else {
    die('No write permissions on application path</br>');
}

// Check MySQL
echo "Checking MySQL connection...</br>";

$conn = new mysqli($db_host, $db_user, $db_pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully</br>";
echo "Checking Database...</br>";

if (!$conn->select_db($db_name)) {
    echo "Database '{$db_name}' does not exist, trying to create...</br>";
    $sql = "CREATE DATABASE {$db_name}";

    if (mysqli_query($conn,$sql)) {
        echo "Database '{$db_name}' created successfully</br>";
    } else {
        die('Error creating database: ' . mysql_error());
    }
}

echo 'Deleting all existing tables...</br>';
$conn->query('SET foreign_key_checks = 0');
if ($result = $conn->query("SHOW TABLES"))
{
    while($row = $result->fetch_array(MYSQLI_NUM))
    {
        $conn->query('DROP TABLE IF EXISTS '.$row[0]);
    }
}

$conn->query('SET foreign_key_checks = 1');
echo 'Cleanup done.</br>';

echo 'Importing contilas table structure...</br>';
$filename = 'contilas.sql';
$templine = '';
$lines = file($filename);
foreach ($lines as $line)
{
    if (substr($line, 0, 2) == '--' || $line == '')
        continue;
    $templine .= $line;
    if (substr(trim($line), -1, 1) == ';')
    {
        mysqli_query($conn,$templine) or die('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
        $templine = '';
    }
}
echo "Tables imported successfully</br>";
echo "Creating first Admin account...</br>";

$sql = " INSERT INTO user
        (user_firstname, user_lastname, user_email, user_phone, user_signature,
        user_active, user_level, login, password, client, user_forwardmail, user_lang,
        telefon_ip, cal_birthdays, cal_tickets, cal_orders) 
        VALUES 
        ('{$usr_fname}', '{$usr_lname}', '{$usr_mail}', '0', '', 1, 32769, '{$usr_username}',
        '{$usr_pass_md5}', 1, 0, 22, '0', 1, 1, 1 )";
if (mysqli_query($conn,$sql)) {
    echo "User '{$usr_username}' created successfully</br>";
} else {
    die('Error creating user: ' . mysql_error());
}
echo "Inserting Client data...</br>";

$sql = "INSERT INTO clients
        (client_name, client_street1, client_street2, client_street3,
        client_postcode, client_city, client_country, active, client_decimal, client_thousand,
        client_currency, client_taxes, client_margin )
        VALUES
        ('{$cl_name}', '{$cl_adr1}', '{$cl_adr2}', '{$cl_adr3}',
        '{$cl_plz}', '{$cl_city}', '55', 1, ',', '.','€', 19, 10 )";
if (mysqli_query($conn,$sql)) {
    echo "Client '{$cl_name}' created successfully</br>";
} else {
    die('Error creating user: ' . mysql_error());
}

$user = 'schealex';
$repository = 'contilas';
$localVersion = 'v0';

echo "Connecting to Hub and checking for latest version...</br>";
echo "Downloading and installing contilas files...</br>";

$updater = new PhpGithubUpdater($user, $repository);
if ($updater->installLatestVersion($app_path,$tmp_path))
{
    echo "installed successful!</br>";
    echo "writing config file...</br>";
    
    $myfile = fopen("../config.setup.php", "w") or die("Unable to write config!");
    $secret = md5($cl_name.$db_user.$db_pass.$usr_username.time());
    $txt = '<?php
$_CONFIG->db = new ConfigContainer();
$_CONFIG->db->host = "'.$db_host.'";
$_CONFIG->db->name = "'.$db_name.'";
$_CONFIG->db->user = "'.$db_user.'";
$_CONFIG->db->pass = "'.$db_pass.'";
$_CONFIG->cookieSecret = "'.$secret.'";';
    fwrite($myfile, $txt);
    fclose($myfile);
    
    echo "click <a href='../index.php'>here</a> to start contilas</br>";
}
else
    echo "error during file download from hub!</br>";