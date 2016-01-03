<?php

// This config file is for use with MIT scripts (scripts.mit.edu)

// CONFIGURATIONS:
// phase values are: nomination | nomination_over | voting | voting_over
$phase = 'voting_over';
$years = '2015-2016';
$maintainer = 'rasin@mit.edu';
$maintainername = 'ezer';

// control access here:
// this has to be changed every year
// uses the moira function defined below, which will only work on athena (scripts)
$voting_list = array_merge(moira('ling-15'), moira('ling-14'), moira('ling-13'), moira('ling-12'), moira('ling-11'), moira('ling-10'));
$nomination_list = array_merge($voting_list, moira('ling-cnvs-fac'));

// END CONFIGURATIONS


if (isset($_GET['test'])) {
	ini_set('display_errors','On');
	error_reporting(E_ALL);
}

// below is just global setup for MIT scripts, like database access:

ini_set('error_reporting', E_ALL & ~E_DEPRECATED);
ini_set('display_errors','On');

define('APPPATH', preg_replace('!web_scripts.*$!', 'web_scripts', __FILE__));
define('APPURL', 'https://linguistics.mit.edu:444/colloq/');

if ( isset($_GET['test']) )
	var_dump($_SERVER);

if ( !isset($_SERVER['SSL_CLIENT_S_DN_Email']) ) {
	header( 'Location: ' . APPURL );
	exit;
}

// get db settings
$db_settings = @parse_ini_file(APPPATH . '/../.my.cnf');
$db_settings['name'] = ($db_settings['host'] === 'sql.mit.edu' ? $db_settings['user'] . '+' : '') . 'colloq';
// $db_settings['errors'] = 'show'; // debug
// $db_settings['charset'] = 'utf8';

// connect to database
global $cnvs;
$cnvs = mysql_pconnect($db_settings['host'], $db_settings['user'], $db_settings['password']) or trigger_error(mysql_error(),E_USER_ERROR); 
mysql_select_db($db_settings['name'], $cnvs);
mysql_query('set names utf8');

// get username
if ( isset($_SERVER['SSL_CLIENT_S_DN_Email']) )
	define('USERNAME',strtolower(trim($_SERVER['SSL_CLIENT_S_DN_Email'])));
else
	define('USERNAME',false);

// define SUPER for superuser priviledges
if (USERNAME === 'mitcho@mit.edu')
	define('SUPER',true);
else
	define('SUPER',false);
  
function moira($list) {
	$members = "";
	exec("blanche -r -noauth $list", $members);
	return array_map('strtolower',array_map('trim', $members));
}
