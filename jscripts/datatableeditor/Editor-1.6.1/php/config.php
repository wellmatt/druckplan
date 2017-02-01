<?php if (!defined('DATATABLES')) exit(); // Ensure being used in DataTables env.

// Enable error reporting for debugging (remove for production)
//error_reporting(E_ALL);
//ini_set('display_errors', '1');
global $_CONFIG;

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Database user / pass
 */
$sql_details = array(
	"type" => "Mysql",  // Database type: "Mysql", "Postgres", "Sqlserver", "Sqlite" or "Oracle"
	"user" => $_CONFIG->db->user,       // Database user name
	"pass" => $_CONFIG->db->pass,       // Database password
	"host" => $_CONFIG->db->host,       // Database host
	"port" => "",       // Database connection port (can be left empty for default)
	"db"   => $_CONFIG->db->name,       // Database name
	"dsn"  => "charset=utf8"        // PHP DSN extra information. Set as `charset=utf8` if you are using MySQL
);


