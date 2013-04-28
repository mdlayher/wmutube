<?php
	require_once "header.inc.php";
?>
<link rel="stylesheet" type="text/css" href="<?= $root_uri ?>/css/manage.css">
<script src="<?= $root_uri ?>/js/jquery.scrollTo.js"></script>
<script src="<?= $root_uri ?>/js/manage.js" type="text/javascript"></script>
<div id="body_container">
	<span id="error"></span>
	<?php
		// Check if user has videos to display
		if (!empty($videos))
		{
	?>
	<div class='site-section videos_list centered_on_page'>
		<header>My Videos</header>
		<ul>
			<?php
				// Iterate over the video list and display it appropriately
				foreach ($videos as $video)
				{
					printf("<li><div><a href='%s/watch/%d' title='%s'>%s</a></div><div><a href='#' class='delete_video' title='Delete this video'>Delete</a></div></li>\n", $root_uri, $video->get_id(), $video->get_title(), $video->get_title());
				}
			?>
		</ul>
	</div>
	<?php
		}

		// User and Course management are Administrator+
		if ($session_user->has_permission(role::ADMINISTRATOR))
		{
	?>
	<div class="site-section videos_list centered_on_page">
		<header>User Management</header>
		<div>
			<div>
				<label for='username' class='stack'>Username</label> 
				<input id='username' class='stack' type='text' length='20'>
				<input id="user_enable_submit" type="submit" value="Enable">
				<input id="user_disable_submit" type='submit' value="Disable">
			</div>
		</div>
	</div>
	<div class="site-section videos_list centered_on_page">
		<header>Course Management</header>
		<?php
			// Check if courses exist
			if (!empty($courses))
			{
		?>
		<div>
			<label for='course_delete_select'>Delete Course</label>
			<select id='course_delete_select'>
			<?php
				foreach ($courses as $c)
				{
					printf("<option data-course_id=\"%s\">%s%s - %s</option>\n", $c->get_id(), $c->get_subject(), $c->get_number(), $c->get_title());
				}
			?>
			</select>
			<input id='course_delete_submit' type='submit'>
		</div>
		<?php
			}
		?>
		<div>
			<label for='course_add_text'>Add Course</label>
			<select id='course_add_subject' placeholder="Subject" length="5" required>
			<option></option>
			<?php
				// Check if subjects exist
				if (!empty($subjects))
				{
					foreach ($subjects as $s)
					{
						printf("<option>%s</option>\n", $s);
					}
				}
			?>
			</select>
			<input id='course_add_number' placeholder="Course Number" type='number' min="1000" max="9999" length='20' id='add_dept' required>
			<input id='course_add_title' placeholder="Course Title" type='text' length='20' required>

			<input id='course_add_submit' type='submit'>
		</div>
	</div>
	<?php
		}
	?>
</div>
<?php
	require_once "footer.inc.php";
?>
