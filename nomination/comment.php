<?php

// check subscription to all
$all_subscribedquery = mysql_query("select count(*) as subscribed from subscriptions where email='".USERNAME."' and id = 0");
$all_subscribedrow = mysql_fetch_assoc($all_subscribedquery);
$all_subscribed = $all_subscribedrow['subscribed'];

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
  $recordID = GetSQLValueString($_REQUEST['id'],"int");
	$insertSQL = sprintf("INSERT INTO comments (nomid, issuer, content) VALUES (%s, '%s', %s)",
											 $recordID,
						 USERNAME,
											 GetSQLValueString($_POST['content'], "text"));

	$Result1 = mysql_query($insertSQL, $cnvs) or die(mysql_error());

	if (!$all_subscribed) {
		// reset subscriptions with the new thing.
		mysql_query(sprintf("delete from subscriptions where id = %d and email = '".USERNAME."'",$recordID),$cnvs) or die(mysql_error());
		if (isset($_POST['subscribed'])) {
			$insertSQL = sprintf("insert into subscriptions (id, email) values ({$recordID},'%s')",USERNAME);
			mysql_query($insertSQL, $cnvs) or die(mysql_error());
		}
  }

  // BEGIN NOTIFICATIONS

  $subscriptionSQL = "select email from subscriptions where (id = '{$recordID}' || id = 0) and email != '".USERNAME."' group by email";
  $results = mysql_query($subscriptionSQL,$cnvs) or die(mysql_error());

  $emails = array();
  while ($row = mysql_fetch_array($results)) {
    $emails[] = $row['email'];
  }
  
  if (count($emails)) {
    $query_nominees = sprintf("SELECT * FROM nominees WHERE id = %s", GetSQLValueString($recordID, "int"));
    $nominees = mysql_query($query_nominees, $cnvs) or die(mysql_error());
    $row = mysql_fetch_array($nominees);

    $subject = str_replace('[NAME]', htmlspecialchars_decode($row['firstname'].' '.$row['lastname']), COMMENT_NOTIFICATION_SUBJECT);
    $body = str_replace('[NAME]', htmlspecialchars_decode($row['firstname'].' '.$row['lastname']), COMMENT_NOTIFICATION_BODY);
    $body = str_replace('[TEXT]',"'". htmlspecialchars_decode($_POST['content'])."'", $body);
    $body = str_replace('[LINK]', APPURL . 'view/' . $recordID, $body);

    $headers = "From: ".NOTIFICATION_FROM."\n";
    $headers .= "Bcc: ".implode(',',$emails)."\n";
    $headers .= "X-Mailer: CNVS\n";
    $headers .= "MIME-Version: 1.0\n";
    $rand = md5(time());
    $mime_boundary = "----.fay----".$rand;
    $headers .= "Content-Type: multipart/alternative; boundary=\"$mime_boundary\"\n";
  
    $message .= "--$mime_boundary\n";
//    $message .= "Content-Type: text/html; charset=UTF-8\n";
    $message .= "Content-Transfer-Encoding: 8bit\n\n";
//    $message .= "<html>\n";
//    $message .= "<body style=\"font-family:Verdana, Verdana, Geneva, sans-serif; font-size:12px; color:#666666;\">\n";
    $message .= $body;
    $message .= "\n\n";
//    $message .= "</body>\n";
//    $message .= "</html>\n";
    $message .= "--$mime_boundary--\n\n";
//    var_dump(array($mailTo, $postnotifier_subject, $message, $headers));
//    exit;
    mail($mailTo, $subject, $message, $headers);
  }

	$insertGoTo = APPURL . "view/$recordID";
	header(sprintf("Location: %s", $insertGoTo));
}

$colname_nominees = "-1";
if (isset($_GET['id'])) {
  $colname_nominees = GetSQLValueString($_GET['id'], "int");
}

$query_nominees = sprintf("SELECT * FROM nominees WHERE id = %s", $colname_nominees);
$nominees = mysql_query($query_nominees, $cnvs) or die(mysql_error());
$row_nominees = mysql_fetch_assoc($nominees);

$subscribedquery = mysql_query("select count(*) as subscribed from subscriptions where email='".USERNAME."' and id = {$colname_nominees}");
$subscribedrow = mysql_fetch_assoc($subscribedquery);
$subscribed = $subscribedrow['subscribed'];

$title = $row_nominees['firstname'] . ' ' . $row_nominees['lastname'];

start();
?>
<h2>Add Comment: <?php echo $row_nominees['firstname'] ?> <?php echo $row_nominees['lastname'] ?></h2>
<form method="post" name="form1" id="form1">
	<table align="center">
		<tr valign="baseline">
			<th nowrap="nowrap" align="right" valign="top">Comment:</th>
			<td><textarea name="content" cols="50" rows="5"></textarea>
			</td>
		</tr>
<?php if (!$all_subscribed): ?>
		<tr valign="baseline">
			<th align="right" nowrap="nowrap">Subscribe<span class="hideoniphone"> to 
comments</span>?</th>
			<td><input type="checkbox" name="subscribed" value="" checked="checked" 
/></td>
		</tr>
<?php endif; ?>
		<tr valign="baseline">
			<td colspan="2"><table width="100%">
					<tr>
						<td align="center"><input type="submit" value="Add comment" /></td>
						<td align="center"><input type="button" value="Discard" onclick="self.location='<?php echo APPURL; ?>view/<?php echo $_GET['recordID'] ?>'"/></td>
					</tr>
				</table></td>
		</tr>
	</table>
	<input type="hidden" name="MM_insert" value="form1" />
	<input type="hidden" name="recordID" value="<?php echo $_GET['recordID'] ?>" />
</form>
<?php
mysql_free_result($nominees);
