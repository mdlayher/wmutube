<!DOCTYPE html>
<html>
<head>
	<link href="./css/main.css" rel="stylesheet" type="text/css"/>
	<link href="http://vjs.zencdn.net/c/video-js.css" rel="stylesheet"/>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<script src="./js/jquery.transit.min.js" type="text/javascript"></script>
	<script src="./js/main.js" type="text/javascript"></script>
	<script src="http://vjs.zencdn.net/c/video.js"></script>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<script src="./js/jquery.transit.min.js" type="text/javascript"></script>
	<script src="./js/jquery.scrollTo.js"></script>
	<!--<script type="text/javascript">| document.write('</' + 'script>')</script>-->
	<title><?= $page_title ?></title>
</head>
<body>
<a href="#" id="previous">Previous</a><a href="#" id="next">Next</a>
<div id="header_container">
	<div class="centered_on_page headerfooter" id="header">
		<div class="left">
			<?= $project_title ?>
		</div>
		<div class="leftish">
			<a href="#" id="videos_link">Videos</a>
		</div>
		<div class="right">
			<?php
				// Check for existing session
				if (!empty($session_user))
				{
					printf("Hello, %s %s! %s", $session_user->get_firstname(), $session_user->get_lastname(), "[logout]");
				}
				else
				{
			?>
			<input type="text" value="username" />
			<input type="password" value="password" />
			<input type="submit" value="login" />
			<?php
				}
			?>
		</div>
	</div>
</div>
<div id="browse_drawer">
</div>
