<?php

if ((isset($_POST["MM_insert"])) && ($_POST["MM_insert"] == "voteform")) {
	$insertSQL = sprintf("INSERT INTO ballots (voter, created, nom1, nom2, nom3, nom4, nom5) VALUES ('%s', Now(), %s, %s, %s, %s, %s)",
                       USERNAME,
											 str_replace("NULL","-1",GetSQLValueString(@$_POST['vote1'], "int")),
											 str_replace("NULL","-1",GetSQLValueString(@$_POST['vote2'], "int")),
											 str_replace("NULL","-1",GetSQLValueString(@$_POST['vote3'], "int")),
											 str_replace("NULL","-1",GetSQLValueString(@$_POST['vote4'], "int")),
											 str_replace("NULL","-1",GetSQLValueString(@$_POST['vote5'], "int")));

	$Result1 = mysql_query($insertSQL, $db) or die(mysql_error());

	header(sprintf("Location: %s", '/colloq/'));
}

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "voteform")) {
	$insertSQL = sprintf("UPDATE ballots SET nom1=%s, nom2=%s, nom3=%s, nom4=%s, nom5=%s WHERE voter='%s'",
											 str_replace("NULL","-1",GetSQLValueString(@$_POST['vote1'], "int")),
											 str_replace("NULL","-1",GetSQLValueString(@$_POST['vote2'], "int")),
											 str_replace("NULL","-1",GetSQLValueString(@$_POST['vote3'], "int")),
											 str_replace("NULL","-1",GetSQLValueString(@$_POST['vote4'], "int")),
											 str_replace("NULL","-1",GetSQLValueString(@$_POST['vote5'], "int")),											 USERNAME);

	$Result1 = mysql_query($insertSQL, $db) or die(mysql_error());

	header(sprintf("Location: %s", APPURL));
}

$query_nominees = "SELECT id, lastname, firstname, affiliation, website, syntax, semantics, phonology, count(comments.nomid) as comments FROM nominees left join comments on (nominees.id = comments.nomid) group by id ORDER BY lastname ASC";
$nominees = mysql_query($query_nominees, $db) or die(mysql_error());
$row_nominees = mysql_fetch_assoc($nominees);
$totalRows_nominees = mysql_num_rows($nominees);

$query_ballot = sprintf("SELECT * FROM ballots WHERE voter = %s", GetSQLValueString(USERNAME, "text"));
$ballot = mysql_query($query_ballot, $db) or die(mysql_error());
$row_ballot = mysql_fetch_assoc($ballot);
$totalRows_ballot = mysql_num_rows($ballot);

start();
?>
<script type='text/javascript'>
jQuery(document).ready(function($){
	$('input.vote').change(function(){
		var id = $(this).val();
		console.log(this, id, $('input.vote[value='+id+']:checked').length);
		if($('input.vote[value='+id+']:checked').length > 1)
			$(this).prop('checked',false);
		if($('input.vote[value='+id+']:checked').length) {
			$('input.vote[value='+id+']:checked').prop('checked',false);
			$(this).prop('checked',true);
		}
	});
	
	var printBallot = function printBallot(list) { // list is the ol to be used.
		if (!list)
			return;
		var list = jQuery(list);
		list.empty();
		for (i=1;i<=5;i++) {
			var input = $('input[name=vote'+i+']:checked');
			var li = $('<li></li>');
			var points = [0,10,7,4,2,1];
			if (input.length) {
				var id = input.val();
				li.text($.trim($('#'+id+' td').eq(0).text()) + ', ' + $('#'+id+' td').eq(1).text() + ' [+' + points[i] + ']');
			}
			list.append(li);
		}
		if ($('input[type=radio]:checked').length) {
			$('.ballots').show();
			list.parent().show();
		}
	}
	
	printBallot($('.currentballot ol'));
	
	$('input[type=radio]').change(function() {
		printBallot($('.newballot ol'));
		if ($('.currentballot ol').length)
			$('.ballotarrow').show().css('margin-top',($('.ballots').height() - $('.ballotarrow').height()) / 2);
	});
		
});
</script>
<style type="text/css">
.ballots {
	border:gray 1px solid;
	padding:15px;
	display:none;
	margin-bottom: 10px;
	overflow: auto;
}
.ballots h2 {
	margin-top: 0;
}
.ballots p {
	margin-bottom: 0;
}
.newballot, .currentballot {
	width: 45%;
	float:left;
}
.ballotarrow {
	float:left;
	width: 10%;
	text-align: center;
	vertical-align: center;
	font-size: 40px;
	font-weight: bold;
}
.newballot, .ballotarrow {
	display: none;
}
</style>
	<div id="voting">
	
        <div class="ballots">
      <?php if ($totalRows_ballot > 0) { ?>
          <div class="currentballot">
            <h2>Current ballot:</h2>
            <ol>
            </ol>
            <p><em>you last updated your ballot at: <?php echo $row_ballot['modified']; ?></em></p>
          </div>
      <?php } ?>
          <div class="ballotarrow">&rarr;</div>
          <div class="newballot">
            <h2 style="margin-top:0">Your current selection:</h2>
            <ol>
            </ol>
            <p><em>You must press "update ballot" for your current selection to be registered as your ballot.</em></p>
          </div>
        </div>

	
		<form method="post" name="voteform" id="voteform">
			<input class="bigbutton" name="votesubmit" type="submit" value="<?php echo ($totalRows_ballot?'Update':'Submit');?> ballot" /> &nbsp; (you can modify this later, until the time when voting closes for everybody)
			<br />
			<p>Nominees - click column heading(s) to sort, click on nominee's last name to see comments:</p>
			<div id="datatable" align="center">
				<table class="sortable">
					<tr>
						<th>Last</th>
						<th>First</th>
						<th class="affiliation">Affiliation</th>
						<th>Web</th>
						<th>Syn?</th>
						<th>Sem?</th>
						<th>Phon?</th>
						<th><small>Comm<span class="hideoniphone">ents</span></small></th>
						<!--<th>votes:</th>-->
						<th style="border-left-width:thick">1st</th>
						<th>2nd</th>
						<th>3rd</th>
						<th>4th</th>
						<th>5th</th>
					</tr>
					<?php do { ?>
						<tr height="40px" id="<?php echo $row_nominees['id'];?>">
							<td><a href="/colloq/view/<?php echo $row_nominees['id'] ?>" target="_blank"> <?php echo $row_nominees['lastname'] ?></a> </td>
							<td><?php echo $row_nominees['firstname']; ?></td>
							<td class="affiliation"><?php echo $row_nominees['affiliation']; ?></td>
							<td><?php if (trim($row_nominees['website']) != "") {
					$pieces = explode(' ', trim($row_nominees['firstname']));
				$real_firstname = $pieces[0]; 
					if (!strpos($row_nominees['website'], "://")) {
						$row_nominees['website'] = "http://".$row_nominees['website'];
					} ?>
								<a href="<?php echo $row_nominees['website'] ?>" target="_blank"><img src="i/www1.gif" width="24" height="24" border="0" title="<?php echo $real_firstname ?>'s website..."/></a>
								<?php } ?>						</td>
							<td class="subfield" align="center"><?php if ($row_nominees['syntax']) { ?>
								+
								<?php } else { ?>
								&ndash;
								<?php } ?>						</td>
							<td class="subfield" align="center"><?php if ($row_nominees['semantics']) { ?>
								+
								<?php } else { ?>
								&ndash;
								<?php } ?>						</td>
							<td class="subfield" align="center"><?php if ($row_nominees['phonology']) { ?>
								+
								<?php } else { ?>
								&ndash;
								<?php } ?>						</td>
						  <td align="center"><a href="/colloq/view/<?php echo $row_nominees['id'] ?>" target="_blank"> <?php echo $row_nominees['comments'] ?></a></td>
							<!--<td style="background-color:#003366"></td>-->
							<td style="background-color:#FF0033; border-left-width:thick"><input name="vote1" class="vote" type="radio" value=<?php echo '"'.$row_nominees['id'].'"'; if ($row_ballot['nom1']==$row_nominees['id']) { echo "CHECKED"; } ?> /></td>	
							<td style="background-color:#FF9900"><input name="vote2" class="vote" type="radio" value=<?php echo '"'.$row_nominees['id'].'"'; if ($row_ballot['nom2']==$row_nominees['id']) { echo "CHECKED"; } ?> /></td>	
							<td style="background-color:#FFFF33"><input name="vote3" class="vote" type="radio" value=<?php echo '"'.$row_nominees['id'].'"'; if ($row_ballot['nom3']==$row_nominees['id']) { echo "CHECKED"; } ?> /></td>	
							<td style="background-color:#66CC33"><input name="vote4" class="vote" type="radio" value=<?php echo '"'.$row_nominees['id'].'"'; if ($row_ballot['nom4']==$row_nominees['id']) { echo "CHECKED"; } ?> /></td>	
							<td style="background-color:#3366CC"><input name="vote5" class="vote" type="radio" value=<?php echo '"'.$row_nominees['id'].'"'; if ($row_ballot['nom5']==$row_nominees['id']) { echo "CHECKED"; } ?> /></td>	
						</tr>
					<?php } while ($row_nominees = mysql_fetch_assoc($nominees)); ?>
				</table>
			</div>
			<br />

        <div class="ballots">
      <?php if ($totalRows_ballot > 0) { ?>
          <div class="currentballot">
            <h2>Current ballot:</h2>
            <ol>
            </ol>
            <p><em>you last updated your ballot at: <?php echo $row_ballot['modified']; ?></em></p>
          </div>
      <?php } ?>
          <div class="ballotarrow">&rarr;</div>
          <div class="newballot">
            <h2 style="margin-top:0">Your current selection:</h2>
            <ol>
            </ol>
            <p><em>You must press "update ballot" for your current selection to be registered as your ballot.</em></p>
          </div>
        </div>
        
			<input class="bigbutton" name="votesubmit" type="submit" value="<?php echo ($totalRows_ballot?'Update':'Submit');?> ballot" /> &nbsp; (you can modify this later, until the time when voting closes for everybody)
			<?php if ($totalRows_ballot > 0) { ?>
				<input type="hidden" name="MM_update" value="voteform" />
			<?php }
			else { ?>
				<input type="hidden" name="MM_insert" value="voteform" />
			<?php
			}
			?>
		</form>
		<br />
		<br />
	</div>
<?php
@mysql_free_result($nominees);
@mysql_free_result($ballot);
