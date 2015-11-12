<?php

$colname_nominees = "-1";
if (isset($_GET['id'])) {
	$colname_nominees = $_GET['id'];
}

$query_nominees = sprintf("SELECT id, lastname, firstname, affiliation, nominator, website, syntax, semantics, phonology FROM nominees WHERE id = %s ORDER BY lastname ASC", GetSQLValueString($colname_nominees, "int"));
$nominees = mysql_query($query_nominees, $cnvs) or die(mysql_error());
$row_nominees = mysql_fetch_assoc($nominees);
$pieces = explode(' ', trim($row_nominees['firstname']));
$real_firstname = $pieces[0];

$colname_comments = $colname_nominees;
$query_comments = sprintf("SELECT `when`, content FROM comments WHERE nomid = %s ORDER BY `when` ASC", GetSQLValueString($colname_comments, "int"));
$comments = mysql_query($query_comments, $cnvs) or die(mysql_error());
$row_comments = mysql_fetch_assoc($comments);
$totalRows_comments = mysql_num_rows($comments);

$title = $row_nominees['firstname'] . ' ' .$row_nominees['lastname'];
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
		</table>
	</div>
	<h2> Comments on <i><?php echo $real_firstname ?></i>: </h2>
	<table align="center" cellspacing="20">
		<?php if ($totalRows_comments > 0) { // Show if recordset not empty ?>
		<tr>
			<td><div id="datatable" align="center">
					<table>
						<?php do { ?>
							<tr>
								<th valign="middle"><?php echo $row_comments['when'] ?></th>
								<td style="padding-top:10px; padding-bottom:10px; "><?php echo $row_comments['content'] ?></td>
							</tr>
							<?php } while ($row_comments = mysql_fetch_assoc($comments)); ?>
					</table>
				</div></td>
		</tr>
		<?php } // Show if recordset not empty
	else { ?>
		<p align="center"><i>(no comments on <?php echo $real_firstname ?>)</i></p>
		<?php } // Show if recordset empty ?>
	</table>
<?php
