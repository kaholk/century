<?php
// https://medoo.in/api/new

require_once 'vendor/autoload.php';
use Medoo\Medoo;

$database = new Medoo([
	// required
	'database_type' => 'mysql',
	'database_name' => 'century',
	'server' => 'localhost',
	'username' => 'root',
	'password' => '',

	'charset' => 'utf8mb4_unicode_520_ci',
	'collation' => 'utf8mb4_unicode_520_ci',
	// 'port' => 3306,
]);

?>