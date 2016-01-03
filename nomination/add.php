<?php 
if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "form1")) {
	$insertSQL = sprintf("INSERT INTO nominees (lastname, firstname, affiliation, website, nominator, created, syntax, semantics, phonology) VALUES (%s, %s, %s, %s, '%s', Now(), %s, %s, %s)",
											 GetSQLValueString($_POST['lastname'], "text"),
											 GetSQLValueString($_POST['firstname'], "text"),
											 GetSQLValueString($_POST['affiliation'], "text"),
											 GetSQLValueString($_POST['website'], "text"),
						 					 USERNAME,
											 GetSQLValueString(isset($_POST['syntax']) ? "true" : "", "defined","1","0"),
											 GetSQLValueString(isset($_POST['semantics']) ? "true" : "", "defined","1","0"),
											 GetSQLValueString(isset($_POST['phonology']) ? "true" : "", "defined","1","0"));

	$Result1 = mysql_query($insertSQL, $cnvs) or die(mysql_error());
	mysql_free_result($Result1);

  $new_recordID = mysql_insert_id($cnvs);

  // BEGIN SUBSCRIPTIONS
  
  if (isset($_POST['subscribe']) && !empty($new_recordID)) {
    $insertSQL = sprintf("insert into subscriptions (id, email) values ({$new_recordID},'%s')",USERNAME);
    mysql_query($insertSQL, $cnvs) or die(mysql_error());
  }
  // END SUBSCRIPTIONS
  
  // BEGIN NOTIFICATIONS

  $subscriptionSQL = "select email from subscriptions where id = 0 and email != '".USERNAME."'";
  $results = mysql_query($subscriptionSQL,$cnvs) or die(mysql_error());

  $emails = array();
  while ($row = mysql_fetch_array($results)) {
    $emails[] = $row['email'];
  }
  
  if (count($emails)) {
    $query_nominees = sprintf("SELECT * FROM nominees WHERE id = %s", GetSQLValueString($new_recordID, "int"));
    $nominees = mysql_query($query_nominees, $cnvs) or die(mysql_error());
    $row = mysql_fetch_array($nominees);

    $subject = str_replace('[NAME]', htmlspecialchars_decode($row['firstname'].' '.$row['lastname']), NOMINEE_NOTIFICATION_SUBJECT);
    $body = str_replace('[NAME]', htmlspecialchars_decode($row['firstname'].' '.$row['lastname']), NOMINEE_NOTIFICATION_BODY);
    $body = str_replace('[LINK]', APPURL . 'view/' . $new_recordID, $body);

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
	
	$insertGoTo = "/colloq/view/" . $new_recordID;
	header(sprintf("Location: %s", $insertGoTo));
}

// check subscription to all
$all_subscribedquery = mysql_query("select count(*) as subscribed from subscriptions where email='".USERNAME."' and id = 0");
$all_subscribedrow = mysql_fetch_assoc($all_subscribedquery);
$all_subscribed = $all_subscribedrow['subscribed'];

start();
?>
<h2> Add Nominee </h2>
<p><strong>Note:</strong> Before adding a new nominee, please consult <a href="/colloq/recent/" target="_self">the list of people who have been invited in the past 5 years</a> (and are therefore ineligible for nomination), as well as <a href="/colloq/" target="_self">the list of current nominees</a>.</p>
<form method="post" name="form1" id="form1" action="/colloq/add/">
	<table align="center">
		<tr valign="baseline">
			<th style="color:#CC0000">First Name:</th>
			<td><input type="text" name="firstname" value="" size="32" required /></td>
		</tr>
		<tr valign="baseline">
			<th style="color:#CC0000">Last Name:</th>
			<td><input type="text" name="lastname" value="" size="32" required /></td>
		</tr>
		<tr valign="baseline">
			<th>Affiliation:</th>
			<td><input type="text" name="affiliation" value="" size="32" /></td>
		</tr>
		<tr valign="baseline">
			<th>Website:</th>
			<td><input type="text" name="website" value="" size="32" /></td>
		</tr>
		<tr valign="baseline">
			<th>Syntax?</th>
			<td><input type="checkbox" name="syntax" value="" /></td>
		</tr>
		<tr valign="baseline">
			<th>Semantics?</th>
			<td><input type="checkbox" name="semantics" value="" /></td>
		</tr>
		<tr valign="baseline">
			<th>Phonology?</th>
			<td><input type="checkbox" name="phonology" value="" /></td>
		</tr>
<?php if (!$all_subscribed): ?>
		<tr valign="baseline">
			<th>Subscribe to comments?</th>
			<td><input type="checkbox" name="subscribe" value="" checked="checked" /></td>
		</tr>
<?php endif; ?>
  </table>
	<br />
	<table align="center" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td align="left" style="padding-left:10px; padding-right:10px; "><input type="submit" value="Submit new nominee" /></td>
			<td align="right" style="padding-left:10px; padding-right:10px; "><input type="button" value="Discard &amp; return to list of nominees" onclick="self.location='/colloq/'"/></td>
		</tr>
	</table>
	<input type="hidden" name="MM_insert" value="form1" />
</form>
