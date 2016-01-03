<?php

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

function log_access_activity() {
	global $db;
	// mark activity
	$updateSQL = sprintf("REPLACE into activity (email, user_agent) values (%s, %s)",
											 GetSQLValueString(USERNAME, "text"),
											 GetSQLValueString($_SERVER['HTTP_USER_AGENT'], "text"));
	mysql_query($updateSQL, $db);
}
