<?php

// BASIC CONFIG FILE

// CONFIGURATIONS:
// phase values are: nomination | nomination_over | voting | voting_over
$phase = 'voting_over';
$years = '2015-2016';
$maintainer = 'admin@admin.com';
$maintainername = 'admin';

// control access here:
// This makes reference to usernames which come from the Apache basic auth username
$voting_list = array('admin','testuser','testvoter');
$nomination_list = array('admin','testuser','testnominator');

// database connection settings:
$db_settings = array(
	'host' => 'localhost',
	'user' => 'colloq',
	'password' => '',
	'name' => 'colloq', // database name
	// 'errors'	 => 'show', // debug
	'charset' => 'utf8'
);

// END CONFIGURATIONS

if (isset($_GET['test'])) {
	ini_set('display_errors','On');
	error_reporting(E_ALL);
}
ini_set('error_reporting', E_ALL & ~E_DEPRECATED);
ini_set('display_errors','On');

define('APPPATH', dirname($_SERVER["SCRIPT_FILENAME"]));
define('APPURL', dirname($_SERVER["SCRIPT_NAME"]));

// connect to database
global $cnvs;
$cnvs = mysql_pconnect($db_settings['host'], $db_settings['user'], $db_settings['password']) or trigger_error(mysql_error(),E_USER_ERROR); 
mysql_select_db($db_settings['name'], $cnvs);
mysql_query('set names ' . $db_settings['charset']);

// get username
if ( isset($_SERVER['REMOTE_USER']) ) {
	define('USERNAME',strtolower(trim($_SERVER['REMOTE_USER'])));
} else {
	define('USERNAME',false);
}
define('USERDISPLAYNAME', USERNAME);

// define SUPER for superuser priviledges
if (USERNAME === 'admin')
	define('SUPER',true);
else
	define('SUPER',false);
  