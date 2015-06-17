<?php
require_once '../../config.php';


// Set login User ID for FormCraft
$user = 'demo';

// Set Password
$password = 'demo';

// Set the path to the FormCraft directory on your site
$path = $_BASEDIR.'/thirdparty/formcraft';



// Database Type. Use sqlite or mysql. If you want a zero-configuratio installation, or don't know what to write, stick to sqlite
$database = 'mysql';


// Database Name. Type in a random string, and don't share it.
$db_name = $_CONFIG->db->name;


// User Access (in case you selected mysql)

$mysql_username = $_CONFIG->db->user;
$mysql_password = $_CONFIG->db->pass;
$mysql_host = $_CONFIG->db->host;







/////////////////////////////////////////////////////////

//// We're done here. Don't look at the below lines ////

///////////////////////////////////////////////////////



error_reporting(0);

require_once 'idiorm/idiorm.php';


if (!isset($path))

{

	$temp = substr( dirname(__FILE__), strlen( $_SERVER[ 'DOCUMENT_ROOT' ] ) );

	$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

	$path = $protocol.$_SERVER['HTTP_HOST'].$temp;

}







if ($database=='mysql')

{

	ORM::configure('mysql:host='.$mysql_host.';dbname='.$db_name);

	ORM::configure('username', $mysql_username);

	ORM::configure('password', $mysql_password);	

	$db = ORM::get_db();



	$db->exec("

		CREATE TABLE IF NOT EXISTS builder(id integer PRIMARY KEY AUTO_INCREMENT, name text NOT NULL, description text NULL, html mediumblob NULL, build mediumblob NULL, options mediumblob NULL, con mediumblob NULL, recipients mediumblob NULL, added text NULL, views integer NULL, submits integer NULL );"

		);

	$db->exec("

		CREATE TABLE IF NOT EXISTS submissions(id integer PRIMARY KEY AUTO_INCREMENT, content mediumblob NULL, seen integer NULL, form_id integer NULL, added text NULL);"

		);

	$db->exec("

		CREATE TABLE IF NOT EXISTS stats(id integer NULL, time TIMESTAMP DEFAULT CURRENT_TIMESTAMP);"

		);

	$db->exec("

		CREATE TABLE IF NOT EXISTS info_table(uniq integer PRIMARY KEY AUTO_INCREMENT, form integer NULL, id text NULL, time text NOT NULL, views integer NULL, submissions integer NULL);"

		);

	$db->exec("

		CREATE TABLE IF NOT EXISTS add_table(id integer PRIMARY KEY AUTO_INCREMENT, application text NULL, code1 text NULL, code2 text NULL)"

		);

}
else
{

	ORM::configure('sqlite:'.dirname(__FILE__).'/'.$db_name.'.sqlite');

	$db = ORM::get_db();



	$db->exec("

		CREATE TABLE IF NOT EXISTS builder(id integer PRIMARY KEY, name text NOT NULL, description text NULL, html mediumblob NULL, build mediumblob NULL, options mediumblob NULL, con mediumblob NULL, recipients mediumblob NULL, added text NULL, views integer NULL, submits integer NULL );"

		);

	$db->exec("

		CREATE TABLE IF NOT EXISTS submissions(id integer PRIMARY KEY, content mediumblob NULL, seen integer NULL, form_id integer NULL, added text NULL); CREATE TABLE IF NOT EXISTS stats(id integer NULL, time TIMESTAMP DEFAULT CURRENT_TIMESTAMP);"

		);

	$db->exec("

		CREATE TABLE IF NOT EXISTS stats(id integer NULL, time TIMESTAMP DEFAULT CURRENT_TIMESTAMP);"

		);

	$db->exec("

		CREATE TABLE IF NOT EXISTS info_table(uniq integer PRIMARY KEY, form integer NULL, id text NULL, time text NOT NULL, views integer NULL, submissions integer NULL);"

		);

	$db->exec("

		CREATE TABLE IF NOT EXISTS add_table(id integer PRIMARY KEY, application text NULL, code1 text NULL, code2 text NULL)"

		);





}





global $path, $user, $password;



?>