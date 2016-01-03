<?php

$recordID = GetSQLValueString($_REQUEST['id'],"int");
mysql_query(sprintf("delete from subscriptions where id = %d and email = '".USERNAME."'",$recordID)) or die(mysql_error());
if (isset($_POST['subscribed'])) {
  $insertSQL = sprintf("insert into subscriptions (id, email) values ({$recordID},'%s')",USERNAME);
  mysql_query($insertSQL, $db) or die(mysql_error());
}

if ($recordID == 0)
	$insertGoTo = APPURL;
else
	$insertGoTo = APPURL . "view/{$recordID}";
header(sprintf("Location: %s", $insertGoTo));
