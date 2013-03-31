<?php
	require_once "header.inc.php";
?>
<link rel="stylesheet" type="text/css" href="./css/videos.css">
<script src="./js/jquery.scrollTo.js"></script>
<script src="./js/videoList.js" type="text/javascript"></script>
<div id="body_container">

	<div class='site-section centered_on_page'>
		<header>Departments</header>
		<p>Lorem ipsum Voluptate ad quis do do magna dolor deserunt laboris nostrud adipisicing non id ex aute tempor consectetur est aute eu veniam dolore anim sed in id sint consectetur proident Excepteur.</p>
	</div>
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
						printf("<li><a href=\"./watch/%d\" title=\"%s\">%s</a></li>\n", $video->get_id(), $video->get_title(), $video->get_title());
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
