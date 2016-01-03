<?php

define("NOTIFICATION_FROM","Linguistics Colloquium <mitcho+cnvs@mit.edu>");
define("COMMENT_NOTIFICATION_SUBJECT","[Colloq] New comment for [NAME]");
define("COMMENT_NOTIFICATION_BODY",<<<BODY
A new comment was added to [NAME]:

[TEXT]

Visit the Linguistics Colloquium site to reply and/or change your notification setting:
[LINK]
BODY
);

define("NOMINEE_NOTIFICATION_SUBJECT","[Colloq] New nominee: [NAME]");
define("NOMINEE_NOTIFICATION_BODY",<<<BODY
A new nominee was added: [NAME]

Visit the Linguistics Colloquium site to comment and/or change your notification setting:
[LINK]
BODY
);

function start() {
	global $header, $title, $starttime, $years, $phase;
	$starttime = microtime(true);
?><!doctype html>
<!--[if lt IE 7 ]> <html lang="en" class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html lang="en" class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html lang="en" class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html lang="en" class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html lang="en"> <!--<![endif]-->
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	
	<title><?php echo $title; ?> | Linguistics Colloquium <?php echo $years; ?></title>
	<meta name="description" content="">
	<meta name="author" content="">
	
	<meta name="viewport" content="width=device-width; initial-scale=1;">
	
	<link rel="stylesheet" href="<?php echo APPURL; ?>css/style.css?v=2">
	<link rel="stylesheet" media="handheld" href="<?php echo APPURL; ?>css/handheld.css?v=2">
	
	<script type='text/javascript' src='<?php echo APPURL; ?>sortable/sortable.js'></script>
	<script type='text/javascript' src='//code.jquery.com/jquery-1.11.3.min.js'></script>
	<script type='text/javascript'>
	jQuery(document).ready(function(){
		$('header').click(function() {window.location='<?php echo APPURL; ?>';}).css({cursor: 'pointer'});
		$('.nomination #datatable tbody tr').each(function() {
			var row = $(this);
			row.click(function(e) {
//				console.log(e.target.tagName);
				if (row.find('.lastname a').length && e.target.tagName != "A" && e.target.tagName != "IMG")
					window.location = row.find('.lastname a').eq(0).attr('href');
			}).css({cursor: 'pointer'});
		});
	});
	</script>

</head>
<body class="<?php echo $phase; ?>">
	<div id="container">
		<header>
	<h1>Ling<span class="hideoniphone">uistics</span> Colloq<span class="hideoniphone">uium <?php echo $years; ?></span>: <?php echo $header; ?></h1>
	<?php if (USERNAME): ?>
	<p class="hideoniphone">You have been identified as: <span class="lookedup"><?php echo USERDISPLAYNAME; ?></span></p>
	<?php endif; ?>
		</header>

		<div id="main" role="main">
<?php
}

function stop() {
	global $starttime, $maintainer, $maintainername;
	$stoptime = microtime(true);
?>
</div><!--main-->

		<footer class="hideoniphone">
		<p>CNVS is currently maintained by <a href="mailto:<?php echo $maintainer; ?>"><em><?php echo $maintainername; ?></em></a></p>
		<p><?php echo number_format($stoptime - $starttime,3);?>s</p>
		</footer>
	</div>
</body>
</html>
<?php
}

function nologin() {
	global $messages;
	echo $messages['nologin'];
}

function wronggroup() {
	global $messages;
	echo $messages['wronggroup'];
}

function nothingtoseehere() {
	global $messages;
	echo $messages['nothingtoseehere'];
}
