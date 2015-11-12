<?php

$query_nominees = "SELECT nominees.id as id, lastname, firstname, affiliation, website, syntax, semantics, phonology, unix_timestamp(created) as created_sort, date_format(created, '%W %D, %h:%i%p') as created, email as subscribed, date_format(max(comments.when), '%W %D, %h:%i%p') as lastcomment, unix_timestamp(max(comments.when)) as lastcomment_sort, count(comments.when) as commentsno
FROM nominees
left join subscriptions on (subscriptions.id = nominees.id and subscriptions.email = '".USERNAME."')
left join comments on (comments.nomid = nominees.id)
group by nominees.id
ORDER BY lastname ASC";
$nominees = mysql_query($query_nominees, $cnvs) or die(mysql_error());
$row_nominees = mysql_fetch_assoc($nominees);
$totalRows_nominees = mysql_num_rows($nominees);
$syn = 0;
$sem = 0;
$phon = 0;

// check subscription to all
$subscribedquery = mysql_query("select count(*) as subscribed from subscriptions where email='".USERNAME."' and id = 0");
$subscribedrow = mysql_fetch_assoc($subscribedquery);
$subscribed = $subscribedrow['subscribed'];

start();
?>
	<h2>Nominations</h2>
	<div id="nomination">
		<?php if ($totalRows_nominees > 0) { // Show if recordset not empty ?>
		<p class="hideoniphone">Existing nominees - click column heading(s) to sort:
			<ul class="hideoniphone">
				<li><a href="#add_nominee_button">scroll down to add nominee</a></li>
		<?php } // Show if recordset not empty
	else { ?>
		<p class="hideoniphone"><i>(no current nominees)</i>
		<br />
			<ul>
		<?php } // Show if recordset empty?>
				<li>click <a href="/colloq/recent/" target="_self">here to see list of people who have been invited in the past four years</a> (and are therefore ineligible for nomination)</li>
			</ul></p>
		<p class="showoniphone"><a href="/colloq/recent/" target="_self">recent speakers list</a></p>
		<?php if ($totalRows_nominees > 0) { // Show if recordset not empty ?>
			<div id="datatable" align="center">
			<table class="sortable">
				<thead id="datatablehdr">
					<tr>
						<th> Last</th>
						<th> First</th>
						<th class="affiliation"> Affiliation </th>
						<th> Web </th>
						<th> Syn </th>
						<th> Sem </th>
						<th> Phon </th>
						<th> Comm<span class="hideoniphone">ents</span> </th>
						<th class="commented">Last comment</th>
						<th class="created">Nominated</th>
						</tr>
				</thead>
				<tbody>
					<?php do { ?>
						<tr>
							<td class='lastname'><a href="/colloq/view/<?php echo $row_nominees['id'] ?>" target="_self"> <?php echo $row_nominees['lastname'] ?></a> </td>
							<td><?php echo $row_nominees['firstname'] ?> </td>
							<td class="affiliation"><?php echo $row_nominees['affiliation'] ?> </td>
							<td style="text-align:center;"><?php if (trim($row_nominees['website']) != "") {
					$pieces = explode(' ', trim($row_nominees['firstname']));
				$real_firstname = $pieces[0]; 
				if (!strpos($row_nominees['website'], "://")) {
					$row_nominees['website'] = "http://".$row_nominees['website'];
				} ?>
								<a href="<?php echo $row_nominees['website'] ?>" target="_blank"><img src="i/www1.gif" width="24" height="24" border="0" title="<?php echo $real_firstname ?>'s website..."/></a>
								<?php } ?>
							</td>
							<td class="subfield" align="center"><?php if ($row_nominees['syntax']) { ?>
								+
								<?php $syn++;
								} else { ?>
								&ndash;
								<?php } ?>
							</td>
							<td class="subfield" align="center"><?php if ($row_nominees['semantics']) { ?>
								+
								<?php $sem++;
								} else { ?>
								&ndash;
								<?php } ?>
							</td>
							<td class="subfield" align="center"><?php if ($row_nominees['phonology']) { ?>
								+
								<?php $phon++;
								} else { ?>
								&ndash;
								<?php } ?>
							</td>
							<td class="subfield" align="center"><?php 
echo "<b class=\"sanishtext\"><a href=\"/colloq/view/{$row_nominees['id']}\" target=\"_self\" ".($row_nominees['subscribed']?" title='Subcribed to comments.'":'')."> ".$row_nominees['commentsno'].($row_nominees['subscribed'] && !$subscribed?" &#x2714;":'')."</a></b>";
							?>
							</td>
							<td class="subfield commented" align="left" sortable_customkey="<?php echo $row_nominees['lastcomment_sort'];?>"><?php 
if ($row_nominees['commentsno'] > 0) {
	echo "<i class=\"sanetext\">".$row_nominees['lastcomment']."</i>"; 
}
							?>
							</td>
							<td class="subfield created" align="left" sortable_customkey="<?php echo $row_nominees['created_sort'];?>"><?php echo "<i class=\"sanetext\">".$row_nominees['created']."</i>"; ?> </td>
						</tr>
						<?php } while ($row_nominees = mysql_fetch_assoc($nominees)); ?>
				</tbody>
			</table>
		</div>
		<?php } // Show if recordset not empty?>
		<p>
		<a name="add_nominee_button" id="add_nominee_button"></a>
		<input type="submit" name="add_nominee" id="add_nominee" value="Add Nominee" onclick="self.location='/colloq/add/'" class="medbutton" />
		</p>
		<br/>
<?php //if (SUPER): ?>
	<h2> Notifications </h2>
  <div>
    <form action="/colloq/subscribe/0" method="post">
    <p><input style="vertical-align:middle" type="checkbox" name="subscribed" id="subscribed" <?php if ($subscribed) echo 'checked="checked"' ?>></input> <label for="subscribed">Subscribe to all activity</label></p>
    <p class="hideoniphone"><small>When this option is on, you will receive an email whenever someone other than you(rself) leaves a comment for any nominee or when a new nominee is added. If this is off, you can still subscribe to comments for individual nominees.</small></p>
    <p><input type="submit" value="Update"/></p>
    </form>
    <br/>
  </div>
<?php //endif; ?>

		<h2>Stats</h2>
		<p>For reference: <?php echo $syn;?> syntacticians, <?php echo $sem;?> semanticists, and <?php echo $phon;?> phonologists have been nominated thus far. (Yes, I'm double-counting).</p>
	</div>
<?php
mysql_free_result($nominees);
