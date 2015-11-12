<?php

$colname_nominees = "-1";
if (isset($_REQUEST['id'])) {
	$colname_nominees = GetSQLValueString($_REQUEST['id'], "int");
}

$query_nominees = sprintf("SELECT id, lastname, firstname, affiliation, nominator, website, syntax, semantics, phonology FROM nominees WHERE id = %s ORDER BY lastname ASC", $colname_nominees);
$nominees = mysql_query($query_nominees, $cnvs) or die(mysql_error());
$row_nominees = mysql_fetch_assoc($nominees);
$pieces = explode(' ', trim($row_nominees['firstname']));
$real_firstname = $pieces[0];

// check subscription to all
$all_subscribedquery = mysql_query("select count(*) as subscribed from subscriptions where email='".USERNAME."' and id = 0");
$all_subscribedrow = mysql_fetch_assoc($all_subscribedquery);
$all_subscribed = $all_subscribedrow['subscribed'];

// check subscription
$subscribedquery = mysql_query("select count(*) as subscribed from subscriptions where email='".USERNAME."' and id = {$colname_nominees}");
$subscribedrow = mysql_fetch_assoc($subscribedquery);
$subscribed = $subscribedrow['subscribed'];

$query_comments = sprintf("SELECT date_format(`when`, '%%W %%D, %%h:%%i%%p') as `when`, content FROM comments WHERE nomid = %s ORDER BY comments.when ASC", $colname_nominees);
$comments = mysql_query($query_comments, $cnvs) or die(mysql_error());
$row_comments = mysql_fetch_assoc($comments);
$totalRows_comments = mysql_num_rows($comments);

$title = $row_nominees['firstname'] . ' ' . $row_nominees['lastname'];
start();
?>
	<h2> Nominee Details </h2>
	<div id="datatable" align="center">
		<table>
			<tr>
				<th>First Name</th>
				<td><?php echo $row_nominees['firstname'] ?> </td>
			</tr>
			<tr>
				<th>Last Name</th>
				<td><?php echo $row_nominees['lastname'] ?> </td>
			</tr>
			<tr>
				<th>Affiliation</th>
				<td><?php echo $row_nominees['affiliation'] ?> </td>
			</tr>
			<tr>
				<th>Website</th>
				<td><?php if (trim($row_nominees['website']) != "") {
			$pieces = explode(' ', trim($row_nominees['firstname']));
			$real_firstname = $pieces[0]; 
				if (!strpos($row_nominees['website'], "://")) {
					$row_nominees['website'] = "http://".$row_nominees['website'];
				} ?>
					<i><a href="<?php echo $row_nominees['website'] ?>" target="_blank"><?php echo $real_firstname ?>'s website...</a></i>
					<?php } ?>
				</td>
			</tr>
			<tr>
				<th>Syntax?</th>
				<td class="subfield"><?php if ($row_nominees['syntax']) { ?>
					+
					<?php } else { ?>
					&ndash;
					<?php } ?>
				</td>
			</tr>
			<tr>
				<th>Semantics?</th>
				<td class="subfield"><?php if ($row_nominees['semantics']) { ?>
					+
					<?php } else { ?>
					&ndash;
					<?php } ?>
				</td>
			</tr>
			<tr>
				<th>Phonology?</th>
				<td class="subfield"><?php if ($row_nominees['phonology']) { ?>
					+
					<?php } else { ?>
					&ndash;
					<?php } ?>
				</td>
			</tr>
<?php if ($row_nominees['firstname'] == 'David' and $row_nominees['lastname'] == 'Bowie'): ?>
			<tr>
				<th>Glam rock?</th>
				<td class="subfield"><?php if (true) { ?>
					+
					<?php } else { ?>
					&ndash;
					<?php } ?>
				</td>
			</tr>
<?php endif; ?>
		</table>
	</div>
	<p align="center">
		<?php if ($row_nominees['nominator'] == USERNAME || SUPER) { ?>
		<input type="button" value="Edit nominee details" onclick="self.location='/colloq/edit/<?php echo $_REQUEST['id']; ?>'"/>
		<?php }
	else { ?>
		<span class="hideoniphone">Only the user who nominated <i><?php echo $real_firstname ?></i> can edit these details.</span>
		<?php } ?>
	</p>
	<p align="center">
	<td><input type="button" name="goback" id="goback" value="Return to list of nominees" onclick="self.location='/colloq/'"/></td>
	</p>
	<h2> Comments on <i><?php echo $real_firstname ?></i>: </h2>
	<table align="center" cellspacing="20">
		<?php if ($totalRows_comments > 0) { // Show if recordset not empty ?>
		<tr>
			<td><div id="datatable" align="center">
					<table>
						<?php do { ?>
							<tr>
								<th valign="middle"><?php echo $row_comments['when'] ?></th>
								<td style="padding-top:10px; padding-bottom:10px; "><?php echo nl2br($row_comments['content']); ?></td>
							</tr>
							<?php } while ($row_comments = mysql_fetch_assoc($comments)); ?>
					</table>
				</div></td>
		</tr>
		<?php } // Show if recordset not empty
	else { ?>
		<p align="center"><i>(no comments on <?php echo $real_firstname ?> yet)</i></p>
		<?php } // Show if recordset empty ?>
		<tr>
			<td><div align="center" id="buttontable">
					<table>
						<tr>
							<td><input type="button" value="Add comment" onclick="self.location='/colloq/comment/<?php echo $row_nominees['id'] ?>'" /></td>
							<td><input type="button" name="goback" id="goback" value="Return to list of nominees..." onclick="self.location='/colloq/'"/></td>
						</tr>
					</table>
				</div></td>
		</tr>
	</table>
	<p>&nbsp;</p>
<?php if (!$all_subscribed): ?>
	<h2> Comment notification: </h2>
  <div align="center" id="buttontable">
    <form action="/colloq/subscribe/<?php echo $row_nominees['id'] ?>" method="post">
    <p><input type="checkbox" name="subscribed" id="subscribed" <?php if ($subscribed) echo 'checked="checked"' ?>></input> <label for="subscribed">Subscribe to comments</label></p>
    <p><small>When "subscribe to comments" is on, you will receive an email whenever someone other than you(rself) leaves a comment for <?php echo $row_nominees['firstname'].' '.$row_nominees['lastname'];?>.</small></p>
    <p><input type="submit" value="Update"/></p>
    </form>
  </div>
<?php else: ?>
	<p>You have subscribed to all activity, so you will be emailed whenever someone other than you(rself) leaves a comment for <?php echo $row_nominees['firstname'].' '.$row_nominees['lastname'];?>.</p>
<?php endif;

mysql_free_result($nominees);
mysql_free_result($comments);
