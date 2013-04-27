<?php
	require_once "header.inc.php";
?>
<link rel="stylesheet" type="text/css" href="<?= $root_uri ?>/css/manage.css">
<script src="<?= $root_uri ?>/js/jquery.scrollTo.js"></script>
<script src="<?= $root_uri ?>/js/manage.js" type="text/javascript"></script>
<div id="body_container">
	<span id="error"></span>
	<div class='site-section videos_list centered_on_page'>
		<header>My Videos</header>
		<ul>
			<?php
				// Iterate over the video list and display it appropriately
				foreach($videos as $video)
				{
					printf("<li><div><a href='%s/watch/%d' title='%s'>%s</a></div><div><a href='#' class='delete_video' title='Delete this video'>Delete</a></div></li>\n", $root_uri, $video->get_id(), $video->get_title(), $video->get_title());
				}
			?>
		</ul>
	</div>

	<div class="site-section videos_list centered_on_page">
		<header>User Management</header>
		<div>
			<div>
				<label for='username' class='stack'>User lookup</label> 
				<input id='username' class='stack' type='text' length='20'>
				<input type='submit'>
			</div>
			<div class="hidden">
				<ul>
					<li><div>jdw8256</div><div><a href='#' title='Disable this user'>Disable</a></div></li>
					<li><div>jdw8256</div><div><a href='#' title='Disable this user'>Disable</a></div></li>
					<li><div>jdw8256</div><div><a href='#' title='Disable this user'>Disable</a></div></li>
					<li><div>jdw8256</div><div><a href='#' title='Disable this user'>Disable</a></div></li>
					<li><div>jdw8256</div><div><a href='#' title='Disable this user'>Disable</a></div></li>
				</ul>
			</div>
		</div>
	</div>

	<div class="site-section videos_list centered_on_page">
		<header>Department Management</header>
		<div>
			<label for='dept_delete_select'>Delete department</label>
			<select id='dept_delete_select'>
				<option data-dept_id="14">Math</option>
				<option data-dept_id="15">Science</option>
				<option data-dept_id="16">CS</option>
			</select>
			<input id="dept_delete_submit" type='submit'>
		</div>
		
		<div>
			<label for='dept_add_text'>Add department</label>
			<input id='dept_add_text' type='text' length='20' id='add_dept'>
			<input id='dept_add_submit' type='submit'>
		</div>
	</div>

	<div class="site-section videos_list centered_on_page">
		<header>Course Management</header>
		<div>
			<label for='course_delete_select'>Delete course</label>
			<select id='course_delete_select'>
				<option data-course_id="14">Math</option>
				<option data-course_id="15">Science</option>
				<option data-course_id="16">CS</option>
			</select>
			<input id='course_delete_submit' type='submit'>
		</div>
		
		<div>
			<label for='course_add_text'>Add course</label>
			<input id='course_add_text' type='text' length='20' id='add_dept'>
			<input id='course_add_submit' type='submit'>
		</div>
	</div>
</div>
<?php
	require_once "footer.inc.php";
?>
