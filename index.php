<?php
session_start();

if (isset($_GET['test'])) {
	ini_set('display_errors','On');
	error_reporting(E_ALL);
}

require_once('config.php');
require_once('templates.php');

$access = array();
switch ($phase) {
	case 'nomination':
		$access = $nomination_list;
		$title = 'Nomination';
		$header = 'Nomination';
		break;
	case 'nomination_over':
		$title = 'Nomination Over';
		$header = 'Nomination Over';
		break;
	case 'voting':
		$access = $voting_list;
		// $access = array_merge($access, moira('ling-cnvs-fac'));
		$title = 'Voting';
		$header = 'Voting';
		break;
	case 'voting_over':
		$title = 'Voting Over';
		$header = 'Voting Over';
		break;
}

if (empty($access)) {
	start();
	nothingtoseehere();
	stop();
	exit;
}

$colname_activity = "-1";
if (USERNAME) {
	if ( !in_array(strtolower(str_replace('@mit.edu', '', USERNAME)), $access) ) {
		start();
		wronggroup();
		stop();
		exit;
	}
} else {
	$title = "Certificate required";
	$header = "Certificate required";
	start();
	nologin();
	stop();
	exit;
}

if (isset($_REQUEST['action']))
	$action = $_REQUEST['action'];
if (empty($action))
	$action = 'index';
if (file_exists("$phase/$action.php"))
	include("$phase/$action.php");
else {
	start();
	echo "<p>Invalid action (<code>" . $action . "</code>). Please tell the maintainer that you encountered an error.</p>";
}

stop();
