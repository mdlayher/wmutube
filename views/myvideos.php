<?php
	require_once "header.inc.php";
?>
<link rel="stylesheet" type="text/css" href="<?= $root_uri ?>/css/myvideos.css">
<script src="<?= $root_uri ?>/js/jquery.scrollTo.js"></script>
<script src="<?= $root_uri ?>/js/videoList.js" type="text/javascript"></script>
<div id="body_container">
	<span id="error"></span>
	<div class='site-section videos_list centered_on_page'>
		<header>My Videos</header>
		<ul>
			<?php
				// Iterate over the video list and display it appropriately
				foreach($videos as $video)
				{
					printf("<li><div><a href='%s/watch/%d' title='%s'>%s</a></div><div><a href='#' title='Delete this video'>Delete</a></div></li>\n", $root_uri, $video->get_id(), $video->get_title(), $video->get_title());
				}
			?>
		</ul>
	</div>
</div>
<?php
	require_once "footer.inc.php";
?>
