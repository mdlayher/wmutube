<?php
	require_once "header.inc.php";
?>
<link rel="stylesheet" type="text/css" href="<?= $root_uri ?>/css/videos.css">
<script src="<?= $root_uri ?>/js/jquery.scrollTo.js"></script>
<script src="<?= $root_uri ?>/js/videoList.js" type="text/javascript"></script>
<div id="body_container">
	<span id="error"></span>
	<?php
		// Iterate all display content
		foreach ($content as $c)
		{
			// Print department header first time
			$first = true;
			foreach ($c["courses"] as $course)
			{
				// Check if videos exist
				$video_list = $course->get_videos();
				if (!empty($video_list))
				{
					// Print department header
					if ($first)
					{
						echo "<div class='site-section videos_list centered_on_page'>\n";
						printf("<header>%s</header>\n", $course->get_department());
						$first = false;
					}

					// Print course information
					echo "<ul>\n";
					printf("<li>%s%d - %s</li>\n", $course->get_subject(), $course->get_number(), $course->get_title());

					// Print video links
					foreach ($video_list as $video)
					{
						printf("<li><a href=\"%s/watch/%d\" title=\"%s\">%s</a></li>\n", $root_uri, $video->get_id(), $video->get_title(), $video->get_title());
					}
				}
				echo "</ul>\n";
			}
			echo "</div>\n";
		}
	?>
</div>
<?php
	require_once "footer.inc.php";
?>
