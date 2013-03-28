<!DOCTYPE html>
<html>
<head>
	<link rel="icon" type="image/ico" href="./img/favicon.ico" />
	<link href="./css/main.css" rel="stylesheet" type="text/css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<script src="./js/login.js" type="text/javascript"></script>
	<!--<script type="text/javascript">| document.write('</' + 'script>')</script>-->
	<title><?= $page_title ?></title>
</head>
<body>
<div id="header_container">
	<div class="centered_on_page headerfooter" id="header">
		<div class="left"><?= $project_title ?></div>
		<div class="leftish">
			<a href="./" title="Home">Home</a> 
			| <a href="#" id="videos_link" title="View learning module videos">Videos</a>
			<?php
				// Check for existing session
				if (!empty($session_user))
				{
					// Display appropriate links for each user class
					// Instructor
					if ($session_user->has_permission(role::INSTRUCTOR))
					{
						echo " | <a href=\"create\" title=\"Create a learning module\">Create</a>\n";
					}
					// Administrator
					if ($session_user->has_permission(role::ADMINISTRATOR))
					{
						echo " | <a href=\"#\" title=\"Create, modify, and delete users\">Manage Users</a>\n";
						echo " | <a href=\"#\" title=\"Create, modify, and delete videos\">Manage Videos</a>\n";
					}
					// Developer
					if ($session_user->has_permission(role::DEVELOPER))
					{
						echo " | <a href=\"#\" title=\"Configure aspects of the system\">Configuration</a>\n";
						echo " | <a href=\"#\" title=\"View metrics regarding the system\">Metrics</a>\n";
					}
				}
			?>
		</div>
		<div class="right">
			<?php
				// Check for existing session
				if (!empty($session_user))
				{
					// Display title for Instructor+
					$role = null;
					if ($session_user->has_permission(role::INSTRUCTOR))
					{
						$role = $session_user->get_role()->get_title();
					}
					printf("Hello, %s %s %s! ", $role, $session_user->get_firstname(), $session_user->get_lastname());
					echo "<input type=\"button\" id=\"logout_button\" value=\"logout\" />\n";
				}
				else
				{
			?>
			<div id="login">
				<input type="text" id="login_username" placeholder="username" />
				<input type="password" id="login_password" placeholder="password" />
				<input type="button" id="login_button" value="login" />
				<span id="login_status"></span>
			</div>
			<?php
				}
			?>
		</div>
	</div>
</div>
<div id="browse_drawer">
</div>
