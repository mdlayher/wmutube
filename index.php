<?php
	// index.php - Khan Academy Workflow, 3/20/13
	// PHP router using Slim microframework to handle all routing of HTTP calls within the application
	//
	// changelog:
	//
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

	// Check for which session handler (5.3 vs 5.4) to use
	if (config::SESSION_HANDLER === "/class_session.php")
	{
		// PHP 5.4: Use custom memcache+database session handler with SessionHandlerInterface
		session_set_save_handler(new session(), true);
		session_set_cookie_params(strtotime(config::SESSION_EXPIRE, true), '/', null);
		session_start();
	}
	else
	{
		// PHP 5.3: Use custom memcache+database session handler with old method
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
	}

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
			// Session user object
			"session_user" => session_user(),
		);
	}

	// ROUTING - - - - - - - - - - - - - - - - - - - - - - -
	// The views of the application, rendered for user

	// VIEWS - - - - - - - - - - - - - - - - - - - - - - - -

	// Application root
	$app->get("/", function() use ($app)
	{
		echo "Session Dump:\n";
		echo print_r($_SESSION, true);
	});

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
			// Pull standard render variables, render create page
			$std = std_render();
			return $app->render("create.php", $std += array(
				"page_title" => TITLE_PREFIX . "Create",
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
	$app->map("/ajax/login", function() use ($app)
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
	})->via("GET", "POST");

	// Logout, destroy current session
	$app->map("/ajax/logout", function() use ($app)
	{
		// Expire session cookie, remove session in database
		session_destroy();
		echo json_status("success");
	})->via("GET", "POST");

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
			$target = sprintf("%s/%s/%s_%s", __DIR__, "uploads", $session_user->get_username(), $req->post("Filename"));

			// Attempt to store file
			try
			{
				move_uploaded_file($_FILES['Filedata']['tmp_name'], $target);
			}
			catch (\Exception $e)
			{
				echo json_status($e->getMessage());
				return;
			}

			// Store this upload's location in session, for use with video editor
			$_SESSION['upload'] = $target;
			echo json_status("success");
			return;
		}

		echo json_status("bad file upload");
		return;
	});


	// AJAX METADATA - - - - - - - - - - - - - - - - - - - -

	// Fetch user information by field and value
	$app->get("/ajax/user/:field(/:value)", function($field, $value = null) use ($app)
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
	$app->get("/ajax/video/:field(/:value)", function($field, $value = null) use ($app)
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
