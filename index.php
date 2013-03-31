<?php
	// index.php - Khan Academy Workflow, 3/20/13
	// PHP router using Slim microframework to handle all routing of HTTP calls within the application
	//
	// changelog:
	//
	// 3/27/13 MDL:
	//	- lots of changes past few days, working sessions, login, etc.
	// 3/23/13 MDL:
	//	- unified endpoint return data
	// 3/20/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	// Composer autoloader
	require_once "vendor/autoload.php";

	// Data model configuration
	require_once "inc/class_config.php";
	config::load(array("cache", "login*", "session", "user", "video"));

	// CONFIGURATION - - - - - - - - - - - - - - - - - - - 

	// Page naming convention
	define("TITLE_PREFIX", config::PROJECT_TITLE . " - ");

	// Create an instance of Slim, set configuration
	use \Slim\Slim as Slim;
	$app = new Slim(array(
		// Enable debug
		"debug" => true,
		// Define path to render templates
		"templates.path" => "./views",
	));

	// Set application's name
	$app->setName(config::PROJECT_TITLE);

	// Use custom memcache+database session handler, using PHP5.3 method
	$session = new session();
	session_set_save_handler(
		array($session, 'open'),
		array($session, 'close'),
		array($session, 'read'),
		array($session, 'write'),
		array($session, 'destroy'),
		array($session, 'gc')
	);

	// Register shutdown function
	register_shutdown_function('session_write_close');

	// Setup cookie handling
	session_set_cookie_params(strtotime(config::SESSION_EXPIRE, true), '/', null);
	session_start();

	// FUNCTIONS - - - - - - - - - - - - - - - - - - - - - -

	// Return a JSON status message
	function json_status($status)
	{
		return json_encode(array("status" => $status));
	}

	// Set this user's session to logged in, or return its status
	function logged_in($set = false)
	{
		// Set if needed
		if ($set)
		{
			$_SESSION['login'] = true;
		}

		// Check for valid login
		if (!isset($_SESSION['login']))
		{
			return false;
		}

		return $_SESSION['login'];
	}

	// Generate, cache, and return this session's user object
	function session_user()
	{
		// Ensure session user ID set
		if (!isset($_SESSION['user']['id']))
		{
			return null;
		}

		// Check cache
		$user = null;
		if (config::MEMCACHE)
		{
			$user = cache::get(config::SESSION_NAME . '_' . $_SESSION['user']['id']);
		}

		// If user cached, unserialize and return
		if ($user)
		{
			return unserialize($user);
		}
		else
		{
			// Else, pull user from database
			$user = user::get_user($_SESSION['user']['id']);

			// Serialize and store in cache
			if (config::MEMCACHE)
			{
				cache::set(config::SESSION_NAME . '_' . $_SESSION['user']['id'], serialize($user));
			}

			return $user;
		}
	}

	// Standard variables to be included in all rendered page
	function std_render()
	{
		return array(
			// Title of project
			"project_title" => config::PROJECT_TITLE,
			// View key, to protect pages from being accessed manually
			"view_key" => config::VIEW_KEY,
			// Session user object
			"session_user" => session_user(),
		);
	}

	// ROUTING - - - - - - - - - - - - - - - - - - - - - - -
	// The views of the application, rendered for user

	// VIEWS - - - - - - - - - - - - - - - - - - - - - - - -

	// Permission: All users
	// Application root
	$index = function() use ($app)
	{
		// Render the index
		$std = std_render();
		return $app->render("index.php", $std += array(
			"page_title" => TITLE_PREFIX . "Home",
		));
	};
	$app->get("/", $index);
	$app->get("/index", $index);
	$app->get("/home", $index);

	// Video upload page
	$app->get("/create", function() use ($app)
	{
		// Ensure user is logged in
		if (!logged_in())
		{
			return $app->forbidden();
		}

		// Get session user, permission check (Instructor+)
		$session_user = session_user();
		if ($session_user->has_permission(role::INSTRUCTOR))
		{
			// Query for list of valid subjects
			$subjects = database::raw_query("SELECT DISTINCT subject FROM courses ORDER BY subject;");
			$subject_list = array();
			foreach ($subjects as $s)
			{
				$subject_list[] = $s["subject"];
			}

			// Pull standard render variables, render create page
			$std = std_render();
			return $app->render("create.php", $std += array(
				"page_title" => TITLE_PREFIX . "Create",
				"subject_list" => $subject_list,
			));
		}
		else
		{
			return $app->forbidden();
		}
	});

	// Video display page
	$app->get("/videos", function() use ($app)
	{
		// Pull standard render variables, render videos page
		$std = std_render();
		return $app->render("videos.php", $std += array(
			"page_title" => TITLE_PREFIX . "Videos",
		));
	});

	// Permission: Instructor+

	// Permission: Administrator+

	// Permission: Developer+

	// PHP debug page
	$app->get("/debug", function() use ($app)
	{
		// Ensure user is logged in
		if (!logged_in())
		{
			return $app->forbidden();
		}

		// Get session user, permission check (Developer+)
		$session_user = session_user();
		if ($session_user->has_permission(role::DEVELOPER))
		{
			// Pull standard render variables, render create page
			$std = std_render();
			return $app->render("debug.php", $std += array(
				"page_title" => TITLE_PREFIX . "Debug",
			));
		}
		else
		{
			return $app->forbidden();
		}
	});

	// AJAX - - - - - - - - - - - - - - - - - - - - - - - -
	// Data endpoints which are sent GET/POST data and return JSON

	// LOGIN - - - - - - - - - - - - - - - - - - - - - - - -

	// Login using specified method, start a session
	$app->post("/ajax/login", function() use ($app)
	{
		// Parse username and password from request
		$req = $app->request();
		$username = $req->params("username");
		$password = $req->params("password");
		$method = $req->params("method");

		// Check for required parameters
		if (isset($username, $password))
		{
			// Pull user object
			$user = user::get_user($username, "username");

			// Check for valid username
			if (!$user)
			{
				echo json_status("bad username");
				return;
			}

			// Check if a method was specified, use it if available
			if (isset($method))
			{
				switch ($method)
				{
					case login::DB:
						$method = new login_db();
						break;
					case login::FTP:
						$method = new login_ftp();
						break;
					case login::IMAP:
						$method = new login_imap();
						break;
					case login::LDAP:
						$method = new login_ldap();
						break;
					case login::SSH:
						$method = new login_ssh();
						break;
					case login::WAVEBOX:
						$method = new login_wavebox();
						break;
					default:
						echo json_status("bad login method");
						return;
						break;
				}
				$user->set_login($method);
			}

			// Attempt authentication
			try
			{
				if ($user->authenticate($password))
				{
					echo json_status("success");

					// On success, store user array, log in user
					$_SESSION['user'] = $user->to_array();
					logged_in(true);
				}
				else
				{
					echo json_status("bad password");
				}
			}
			// Catch any exceptions, useful for catching programmer errors
			catch (\Exception $e)
			{
				echo json_status($e->getMessage());
			}

			return;
		}
		else
		{
			echo json_status("missing required parameters");
		}

		return;
	});

	// Logout, destroy current session
	$app->post("/ajax/logout", function() use ($app)
	{
		// Expire session cookie, remove session in database
		session_destroy();
		echo json_status("success");
	});

	// VIDEO UPLOAD - - - - - - - - - - - - - - - - - - - -

	// Video upload via POST
	$app->post("/ajax/upload", function() use ($app)
	{
		// Ensure user is logged in
		if (!logged_in())
		{
			echo json_status("403 Forbidden");
			return;
		}

		// Get session user, permission check (Instructor+)
		$session_user = session_user();
		if (!$session_user->has_permission(role::INSTRUCTOR))
		{
			echo json_status("403 Forbidden");
			return;
		}

		// Parse video metadata from request
		$req = $app->request();

		// Check for file upload
		if (!empty($_FILES))
		{
			// Set path for upload target, identify video with filename and username
			$target = sprintf("/uploads/tmp/%s_%s", $session_user->get_username(), $req->post("Filename"));

			// Attempt to store file
			try
			{
				move_uploaded_file($_FILES['Filedata']['tmp_name'], __DIR__ . $target);
			}
			catch (\Exception $e)
			{
				echo json_status($e->getMessage());
				return;
			}

			// Store this upload's location in session, for use with video editor
			$_SESSION['upload'] = $target;
			echo stripslashes(json_encode(array("status" => "success", "filename" => $target)));
			return;
		}

		echo json_status("bad file upload");
		return;
	});

	// Video creation via POST
	$app->post("/ajax/create", function() use ($app)
	{
		// Ensure user is logged in
		if (!logged_in())
		{
			echo json_status("403 Forbidden");
			return;
		}

		// Get session user, permission check (Instructor+)
		$session_user = session_user();
		if (!$session_user->has_permission(role::INSTRUCTOR))
		{
			echo json_status("403 Forbidden");
			return;
		}

		// Check for video filename and existence stored in session from upload
		if (empty($_SESSION['upload']) || !file_exists(__DIR__ . $_SESSION['upload']))
		{
			echo json_status("upload not found");
			return;
		}

		// Check the sanity of all video upload variables from post ...
		$req = $app->request();

		echo json_status("success");
		return;
	});

	// AJAX METADATA - - - - - - - - - - - - - - - - - - - -

	// Fetch course information by subject and number
	$app->get("/ajax/course/:subject(/(:number))", function($subject, $number = null) use ($app)
	{
		// Ensure course is logged in
		if (!logged_in())
		{
			echo json_status("403 Forbidden");
			$app->halt(403);
			return;
		}

		// Get session user and check permissions
		$session_user = session_user();

		// Check if trying to query all courses (Administrator+), or a single course (User+)
		if ((empty($value) && !$session_user->has_permission(role::ADMINISTRATOR)) || !$session_user->has_permission(role::USER))
		{
			echo json_status("403 Forbidden");
			$app->halt(403);
			return;
		}

		// If no value specified, fetch list
		if (empty($number))
		{
			try
			{
				$course = course::filter_courses("subject", $subject);
			}
			catch (\Exception $e)
			{
				$course = null;
			}
		}
		else
		{
			// Else, fetch single course
			// Grab course from database using field and value combination
			try
			{
				$course = course::get_course($subject, "subject", $number);
			}
			catch (\Exception $e)
			{
				$course = null;
			}
		}

		// If found, return
		if ($course)
		{
			// For single course, convert to JSON and send
			if (!is_array($course))
			{
				echo $course->to_json();
				return;
			}

			// For multiple courses, turn them into arrays, encode and send
			$courses = array();
			foreach ($course as $c)
			{
				$courses[] = $c->to_array();
			}
			echo json_encode($courses);
			return;
		}
		else
		{
			echo json_status("404 Not Found");
			$app->halt(404);
			return;
		}
	});

	// Fetch user information by field and value
	$app->get("/ajax/user(/(:field(/(:value))))", function($field = "id", $value = null) use ($app)
	{
		// Ensure user is logged in
		if (!logged_in())
		{
			echo json_status("403 Forbidden");
			return;
		}

		// Get session user and check permissions
		$session_user = session_user();

		// Check if trying to query all users (Administrator+), or a single user (User+)
		if ((empty($value) && !$session_user->has_permission(role::ADMINISTRATOR)) || !$session_user->has_permission(role::USER))
		{
			echo json_status("bad permissions");
			return;
		}

		// If no value specified, fetch list
		if (empty($value))
		{
			try
			{
				$user = user::fetch_users($field);
			}
			catch (\Exception $e)
			{
				$user = null;
			}
		}
		else
		{
			// Else, fetch single user
			// Grab user from database using field and value combination
			try
			{
				$user = user::get_user($value, $field);
			}
			catch (\Exception $e)
			{
				$user = null;
			}
		}

		// If found, return
		if ($user)
		{
			// For single user, convert to JSON and send
			if (!is_array($user))
			{
				echo $user->to_json();
				return;
			}

			// For multiple users, turn them into arrays, encode and send
			$users = array();
			foreach ($user as $u)
			{
				$users[] = $u->to_array();
			}
			echo json_encode($users);
			return;
		}
		else
		{
			echo json_status("404 Not Found");
			return;
		}
	});

	// Fetch video information by field and value
	$app->get("/ajax/video(/(:field(/(:value))))", function($field = "id", $value = null) use ($app)
	{
		// Ensure user is logged in
		if (!logged_in())
		{
			echo json_status("403 Forbidden");
			return;
		}

		// Get session user and check permissions
		$session_user = session_user();

		// Check if trying to query all users (Administrator+), or a single user (User+)
		if ((empty($value) && !$session_user->has_permission(role::ADMINISTRATOR)) || !$session_user->has_permission(role::USER))
		{
			echo json_status("bad permissions");
			return;
		}

		// If no value specified, fetch list
		if (empty($value))
		{
			try
			{
				$video = video::fetch_videos($field);
			}
			catch (\Exception $e)
			{
				$video = null;
			}
		}
		else
		{
			// Else, fetch single video
			// Grab video from database using field and value combination
			try
			{
				$video = video::get_video($value, $field);
			}
			catch (\Exception $e)
			{
				$video = null;
			}
		}

		// If found, return
		if ($video)
		{
			// For single video, convert to JSON and send
			if (!is_array($video))
			{
				echo $video->to_json();
				return;
			}

			// For multiple videos, turn them into arrays, encode and send
			$videos = array();
			foreach ($video as $v)
			{
				$videos[] = $v->to_array();
			}
			echo json_encode($videos);
			return;
		}
		else
		{
			echo json_status("404 Not Found");
			return;
		}
	});

	// RUN IT! - - - - - - - - - - - - - - - - - - - - - - -
	$app->run();
