<?php

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form1")) {
	$updateSQL = sprintf("UPDATE nominees SET affiliation=%s, website=%s, syntax=%s, semantics=%s, phonology=%s WHERE id=%s",
											 GetSQLValueString($_POST['affiliation'], "text"),
											 GetSQLValueString($_POST['website'], "text"),
											 GetSQLValueString(isset($_POST['syntax']) ? "true" : "", "defined","1","0"),
											 GetSQLValueString(isset($_POST['semantics']) ? "true" : "", "defined","1","0"),
											 GetSQLValueString(isset($_POST['phonology']) ? "true" : "", "defined","1","0"),
											 GetSQLValueString($_POST['recordID'], "int"));

	mysql_select_db($database_cnvs, $db);
	$Result1 = mysql_query($updateSQL, $db) or die(mysql_error());

	$updateGoTo = APPURL . "view/{$_POST['recordID']}";
	if (isset($_SERVER['QUERY_STRING'])) {
		$updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
		$updateGoTo .= $_SERVER['QUERY_STRING'];
	}
	header(sprintf("Location: %s", $updateGoTo));
}

$colname_nominees = "-1";
if (isset($_REQUEST['id'])) {
	$colname_nominees = $_REQUEST['id'];
}

$query_nominees = sprintf("SELECT * FROM nominees WHERE id = %s", GetSQLValueString($colname_nominees, "int"));
$nominees = mysql_query($query_nominees, $db) or die(mysql_error());
$row_nominees = mysql_fetch_assoc($nominees);
$totalRows_nominees = mysql_num_rows($nominees);

$title = $row_nominees['firstname'] . ' ' . $row_nominees['lastname'];

start();
?>
<h2> Edit Nominee </h2>
<form method="post" name="form1" id="form1">
	<table align="center">
		<tr valign="baseline">
			<th>First Name:</th>
			<td><?php echo $row_nominees['firstname'] ?></td>
		</tr>
		<tr valign="baseline">
			<th>Last Name:</th>
			<td><?php echo $row_nominees['lastname'] ?></td>
		</tr>
		<tr valign="baseline">
			<th>Affiliation:</th>
			<td><input type="text" name="affiliation" value="<?php echo htmlentities($row_nominees['affiliation'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
		</tr>
		<tr valign="baseline">
			<th>Website:</th>
			<td><input type="text" name="website" value="<?php echo htmlentities($row_nominees['website'], ENT_COMPAT, 'UTF-8'); ?>" size="32" /></td>
		</tr>
		<tr valign="baseline">
			<th>Syntax?</th>
			<td><input type="checkbox" name="syntax" value=""	<?php if ($row_nominees['syntax']) {echo "checked";} ?> /></td>
		</tr>
		<tr valign="baseline">
			<th>Semantics?</th>
			<td><input type="checkbox" name="semantics" value=""	<?php if ($row_nominees['semantics']) {echo "checked";} ?> /></td>
		</tr>
		<tr valign="baseline">
			<th>Phonology?</th>
			<td><input type="checkbox" name="phonology" value=""	<?php if ($row_nominees['phonology']) {echo "checked";} ?> /></td>
		</tr>
	</table>
	<br />
	<table align="center" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td align="left" style="padding-left:10px; padding-right:10px; "><input type="submit" value="Submit changes" /></td>
			<td align="right" style="padding-left:10px; padding-right:10px; "><input type="button" value="Discard &amp; return to nominee details" onclick="self.location='<?php echo APPURL; ?>view/<?php echo $row_nominees['id'];?>'"/></td>
		</tr>
	</table>
	<input type="hidden" name="MM_update" value="form1" />
	<input type="hidden" name="recordID" value="<?php echo $row_nominees['id'] ?>" />
</form>
<?php
@mysql_free_result($nominees);
