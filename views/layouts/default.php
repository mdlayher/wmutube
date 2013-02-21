<!DOCTYPE html>
<html>
<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<script src="script/jquery.transit.min.js" type="text/javascript"></script>
	<script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script>
	<title><?=$title_for_layout?></title>
	<link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
	<div id='header_container'>
		<div id='header'>
			<div class='left'>
				CourseraClone
			</div>
			<div class='right'>
				Hello, Justin!
			</div>
		</div>
	</div>
	<div id='body_container'>
		<?=$content_for_layout?>
	</div>
</body>
</html>
