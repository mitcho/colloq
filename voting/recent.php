<?php

$query_recent = "SELECT * FROM recent WHERE (year+5 > YEAR(NOW()) OR (year+5 = YEAR(NOW()) AND term = 'fall')) ORDER BY lastname ASC";
$recent = mysql_query($query_recent, $cnvs) or die(mysql_error());
$row_recent = mysql_fetch_assoc($recent);
$totalRows_recent = mysql_num_rows($recent);

$title = "Recent speakers";
$header = "Recent speakers";
start();
?>
	<p>Recent Speakers - click column heading(s) to sort:</p>
	<div id="recent">
		<div id="datatable" align="center">
			<p><a href="javascript:history.back(1)">(back to previous page)</a></p>
			<table class="sortable">
				<thead id="datatablehdr">
					<tr>
						<th> Last Name </th>
						<th> First Name </th>
						<th> Year </th>
						<th> Term </th>
					</tr>
			</thead>
			<tbody>
				<?php do { ?>
					<tr>
						<td><?php echo $row_recent['lastname']; ?></td>
						<td><?php echo $row_recent['firstname']; ?></td>
						<td><?php echo $row_recent['year']; ?></td>
						<td><?php echo $row_recent['term']; ?></td>
					</tr>
					<?php } while ($row_recent = mysql_fetch_assoc($recent)); ?>
				</tbody>
			</table>
			<p><a href="javascript:history.back(1)">(back to previous page)</a></p>
		</div>
	</div>
<?php
@mysql_free_result($recent);
