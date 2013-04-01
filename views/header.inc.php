<?php
	// Protect against direct access to views
	require_once __DIR__ . "/../inc/class_config.php";
	if (!isset($view_key) || $view_key !== config::VIEW_KEY)
	{
		// Return user to the framework view of this page
		header(sprintf("Location: /%s/%s", strtolower(config::PROJECT_TITLE), basename($_SERVER["REQUEST_URI"], ".php")));
		exit(0);
	}

	// Check for root URI
	if (!isset($root_uri))
	{
		$root_uri = ".";
	}
?>

<!DOCTYPE html>
<html>
<head>
	<link rel="icon" type="image/ico" href="<?= $root_uri ?>/img/favicon.ico" />
	<link href="<?= $root_uri ?>/css/site.css" rel="stylesheet" type="text/css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
	<script src="<?= $root_uri ?>/js/jquery.transit.min.js" type="text/javascript"></script>
	<script src="<?= $root_uri ?>/js/login.js" type="text/javascript"></script>
	<script src="<?= $root_uri ?>/js/drawer.js" type="text/javascript"></script>
	<!--<script type="text/javascript">| document.write('</' + 'script>')</script>-->
	<title><?= $page_title ?></title>
</head>
<body>
<div id="header_container">
	<div class="centered_on_page headerfooter" id="header">
		<div class="left"><?= $project_title ?></div>
		<div class="leftish">
			<a href="<?= $root_uri ?>/" title="Home">Home</a> 
			| <a href="<?= $root_uri ?>/videos" id="videos_link" title="View learning module videos">Videos &darr;</a>
			<?php
				// Check for existing session
				if (!empty($session_user))
				{
					// Display appropriate links for each user class
					// Instructor
					if ($session_user->has_permission(role::INSTRUCTOR))
					{
						printf(" | <a href=\"%s/create\" title=\"Create a learning module\">Create</a>\n", $root_uri);
					}
					// Administrator
					if ($session_user->has_permission(role::ADMINISTRATOR))
					{

					}
					// Developer
					if ($session_user->has_permission(role::DEVELOPER))
					{
						printf(" | <a href=\"%s/debug\" title=\"Debug information about the system\">Debug</a>\n", $root_uri);
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
	<a href="/video" title="videos">Videos!</a>
</div>
