<?php

// CONFIGURATIONS:




// phase values are: nomination | nomination_over | voting | voting_over
$phase = 'voting_over';
$years = '2015-2016';
$maintainer = 'rasin@mit.edu';
$maintainername = 'ezer';

// control access here:
// this has to be changed every year
$voting_list = array_merge(moira('ling-15'), moira('ling-14'), moira('ling-13'), moira('ling-12'), moira('ling-11'), moira('ling-10'));
$nomination_list = array_merge($voting_list, moira('ling-cnvs-fac'));

// END CONFIGURATIONS




// below is just global setup, like database access and a few helper functions

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

$cnvs = mysql_pconnect($db_settings['host'], $db_settings['user'], $db_settings['password']) or trigger_error(mysql_error(),E_USER_ERROR); 
mysql_select_db($db_settings['name'], $cnvs);
mysql_query('set names utf8');

// get current phase
// $query_phase = "SELECT phase, years FROM config WHERE name = 'current'";
// $phase = mysql_query($query_phase, $cnvs) or die(mysql_error());
// $row_phase = mysql_fetch_assoc($phase);

// get username
if ( isset($_SERVER['SSL_CLIENT_S_DN_Email']) )
	define('USERNAME',strtolower(trim($_SERVER['SSL_CLIENT_S_DN_Email'])));
else
	define('USERNAME',false);

// mark activity
$updateSQL = sprintf("REPLACE into activity (email, user_agent) values (%s, %s)",
										 GetSQLValueString(USERNAME, "text"),
										 GetSQLValueString($_SERVER['HTTP_USER_AGENT'], "text"));
mysql_query($updateSQL, $cnvs);

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

function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
	$theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

	$theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

	switch ($theType) {
		case "text":
			$theValue = trim($theValue);
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;		
		case "long":
		case "int":
			$theValue = ($theValue != "") ? intval($theValue) : "NULL";
			break;
		case "double":
			$theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
			break;
		case "date":
			$theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
			break;
		case "defined":
			$theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
			break;
	}
	return $theValue;
}
