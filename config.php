<?php

// BASIC CONFIG FILE

// CONFIGURATIONS:
// phase values are: nomination | nomination_over | voting | voting_over
$phase = 'nomination';
$years = '2015-2016';
define("NOTIFICATION_FROM", "Linguistics Colloquium <admin@colloq.com>");
define("NOTIFICATION_TO", "noreply@colloq.com");

// control access here:
// This makes reference to usernames which come from the Apache basic auth username
$voting_list = array('admin@colloq.com','testuser@colloq.com','testvoter@colloq.com');
$nomination_list = array('admin@colloq.com','testuser@colloq.com','testnominator@colloq.com');

// database connection settings:
$db_settings = array(
	'host' => 'localhost',
	'user' => 'colloq',
	'password' => '',
	'name' => 'colloq', // database name
	// 'errors'	 => 'show', // debug
	'charset' => 'utf8'
);

// END BASIC CONFIGURATIONS

if (isset($_GET['test'])) {
	ini_set('display_errors','On');
	error_reporting(E_ALL);
}
ini_set('error_reporting', E_ALL & ~E_DEPRECATED);
ini_set('display_errors','On');

define('APPPATH', dirname($_SERVER["SCRIPT_FILENAME"]));
define('APPURL', 'http://' . $_SERVER["HTTP_HOST"] . dirname($_SERVER["SCRIPT_NAME"]));

// connect to database
global $db;
$db = mysql_pconnect($db_settings['host'], $db_settings['user'], $db_settings['password']) or trigger_error(mysql_error(),E_USER_ERROR); 
mysql_select_db($db_settings['name'], $db);
mysql_query('set names ' . $db_settings['charset']);

// get username
if ( isset($_SERVER['REMOTE_USER']) ) {
	define('USERNAME',strtolower(trim($_SERVER['REMOTE_USER'])));
} else {
	define('USERNAME',false);
}
define('USERDISPLAYNAME', USERNAME);

// define SUPER for superuser priviledges
if (USERNAME === 'admin@colloq.com')
	define('SUPER',true);
else
	define('SUPER',false);

// messages
global $messages;
$messages = array(
	'footer' => '<!--<p>Contact <a href="mailto:admin@colloq.com">admin</a> for support.</p>-->',
	'nologin' => '<p>Your username could not be retrieved. You must log in to use the application.</p>',
	'wronggroup' => '<p>You do not have the proper credentials to participate in this round.</p>',
	'nothingtoseehere' => '<h2>Nothing to see here.</h2><p>Please check back later!</p>',
);
