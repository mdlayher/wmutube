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

	// Set session name
	ini_set("session.name", config::PROJECT_TITLE);

	// Run garbage collection more aggressively
	ini_set("session.gc_probability", 50);

	// Use /dev/urandom for better session entropy
	ini_set("session.entropy_file", "/dev/urandom");

	// Use sha1 hash for session IDs
	ini_set("session.hash_function", 1);

	// Use more bits per character stored in session
	ini_set("session.hash_bits_per_character", 6);

	// Disallow transient session IDs in URLs
	ini_set("session.use_trans_sid", 0);

	// Send cookies over HTTP only, to help mitigate XSS
	ini_set("session.cookie_httponly", 1);

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

	// Return user's login status
	function logged_in()
	{
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
		// Ensure user logged in
		if (!logged_in())
		{
			return null;
		}

		// Ensure session user ID set
		if (!isset($_SESSION['id']))
		{
			return null;
		}

		// Return user
		return user::get_user($_SESSION['id']);
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

	// Video display page
	$app->get("/videos", function() use ($app)
	{
		// Get a list of subjects for videos
		$subjects = course::fetch_subjects();

		// Build array of information about each department, its courses, its videos
		$content = array();
		foreach ($subjects as $s)
		{
			// Grab a list of all courses associated with department
			$department = array();
			$department["courses"] = course::filter_courses("subject", $s);

			// Add data to array
			$content[] = $department;
		}

		$std = std_render();
		return $app->render("videos.php", $std += array(
			"page_title" => TITLE_PREFIX . "Videos",
			"content" => $content,
		));
	});

	// Watch video page
	$app->get("/watch(/(:id))", function($id = null) use ($app)
	{
		// If no ID, redirect to /videos
		if (empty($id))
		{
			$app->response()->redirect("./videos");
			return;
		}

		// Pull video object, ensure it exists
		$video = video::get_video($id);
		if (!$video)
		{
			$app->response()->redirect("../videos");
			return;
		}

		$std = std_render();
		return $app->render("watch.php", $std += array(
			"page_title" => TITLE_PREFIX . "Watch - " . $video->get_title(),
			"root_uri" => $app->request()->getRootUri(),
			"video" => $video,
		));
	});

	// Permission: Instructor+

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
				"subject_list" => course::fetch_subjects(),
			));
		}
		else
		{
			return $app->forbidden();
		}
	});

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
				// Call LDAP sync to try to pull credentials into our database, and authenticate
				$login = new login(new login_ldap());
				if ($login->authenticate(array("username" => $username, "password" => $password)))
				{
					echo json_status("success");

					// On success, store user ID, log in user
					$user = user::get_user($username, "username");
					$_SESSION['id'] = $user->get_id();
					$_SESSION['login'] = 1;

					// Regenerate session ID
					session_regenerate_id();

					return;
				}
				else
				{
					echo json_status("bad username or password");
					return;
				}
			}

			// Ensure user is enabled for login
			if (!$user->get_enabled())
			{
				echo json_status("account disabled");
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

					// On success, store user ID, log in user
					$_SESSION['id'] = $user->get_id();
					$_SESSION['login'] = 1;

					// Regenerate session ID
					session_regenerate_id();
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
			$app->halt(403);
			return;
		}

		// Get session user, permission check (Instructor+)
		$session_user = session_user();
		if (!$session_user->has_permission(role::INSTRUCTOR))
		{
			echo json_status("403 Forbidden");
			$app->halt(403);
			return;
		}

		// Parse video metadata from request
		$req = $app->request();

		// Check for file upload
		if (!empty($_FILES))
		{
			// Set path for upload target, identify video with filename and username
			$target = sprintf("/uploads/tmp/%s_%s", $session_user->get_username(), md5($req->post("Filename")) . ".mp4");

			// Check for upload error
			if ($_FILES['Filedata']['error'] > 0)
			{
				echo json_status("file upload error '" . $_FILES['Filedata']['error'] . "'");
				$app->halt(400);
				return;
			}

			// Attempt to store file
			try
			{
				error_log(print_r($_FILES, true));
				error_log(sprintf("moving '%s' to '%s'", $_FILES['Filedata']['tmp_name'], __DIR__ . $target));
				move_uploaded_file($_FILES['Filedata']['tmp_name'], __DIR__ . $target);
			}
			catch (\Exception $e)
			{
				echo json_status($e->getMessage());
				$app->halt(400);
				return;
			}

			// Store this upload's location in session, for use with video editor
			$_SESSION['upload'] = $target;
			echo stripslashes(json_encode(array("status" => "success", "filename" => $target)));
			return;
		}

		echo json_status("bad file upload");
		$app->halt(400);
		return;
	});

	// Video creation via POST
	$app->post("/ajax/create", function() use ($app)
	{
		// Ensure user is logged in
		if (!logged_in())
		{
			echo json_status("403 Forbidden");
			$app->halt(403);
			return;
		}

		// Get session user, permission check (Instructor+)
		$session_user = session_user();
		if (!$session_user->has_permission(role::INSTRUCTOR))
		{
			echo json_status("403 Forbidden");
			$app->halt(403);
			return;
		}

		// Check for video filename and existence stored in session from upload
		if (empty($_SESSION['upload']) || !file_exists(__DIR__ . $_SESSION['upload']))
		{
			echo json_status("upload not found");
			return;
		}

		// Check the sanity of all video upload variables from post ...
		$video_obj = json_decode($app->request()->post("videoInfo"));

		// Generate new video filename hash
		$name = md5(uniqid()) . ".mp4";

		// Generate video object from parameters
		$video = video::create_video((int)$session_user->get_id(), (int)$video_obj->course, $name, $video_obj->title, $video_obj->description);

		// Store video object
		if (!$video->set_video())
		{
			echo json_status("failed to save video");
			return;
		}

		// Store questions
		foreach ($video_obj->questions as $q)
		{
			// Convert to timestamp format
			list($m, $s) = explode(':', $q->timestamp);
			$time = mktime(0, $m, $s) - mktime(0, 0, 0);

			// Generate question object
			$question = question::create_question((int)$video->get_id(), $time, $q->text, $q->hint);

			// Store question object
			if (!$question->set_question())
			{
				echo json_status("failed to save questions");
				return;
			}

			// Store answers
			foreach ($q->answers as $a)
			{
				// Generate answer object
				$answer = answer::create_answer((int)$question->get_id(), $a->text, $a->correct);

				// Store answer object
				if (!$answer->set_answer())
				{
					echo json_status("failed to save answers");
					return;
				}
			}
		}

		// Move file into its permanent home
		if (!rename(__DIR__ . $_SESSION['upload'], __DIR__ . "/uploads/" . $name))
		{
			echo json_status("failed to move file");
			return;
		}

		echo json_encode(array("status" => "success", "id" => $video->get_id()));
		return;
	});

	// AJAX QUIZ OPERATIONS - - - - - - - - - - - - - - - -

	// Return if specified answer is correct
	$app->get("/ajax/answer/correct/:id", function($id) use ($app)
	{
		// Get answer by ID
		$answer = answer::get_answer($id);

		// Check if exists
		if (!$answer)
		{
			echo json_status("bad answer ID");
			$app->halt(400);
			return;
		}

		// Return if answer is correct
		echo json_encode(array("correct" => $answer->get_correct()));
		return;
	});

	// Return the hint for specified question
	$app->get("/ajax/question/hint/:id", function($id) use ($app)
	{
		// Get question by ID
		$question = question::get_question($id);

		// Check if exists
		if (!$question)
		{
			echo json_status("bad question ID");
			$app->halt(400);
			return;
		}

		// Return question hint
		echo json_encode(array("hint" => $question->get_hint()));
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
			$app->halt(403);
			return;
		}

		// Get session user and check permissions
		$session_user = session_user();

		// Check if trying to query all users (Administrator+), or a single user (User+)
		if ((empty($value) && !$session_user->has_permission(role::ADMINISTRATOR)) || !$session_user->has_permission(role::USER))
		{
			echo json_status("403 Forbidden");
			$app->halt(403);
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
			$app->halt(404);
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
			$app->halt(403);
			return;
		}

		// Get session user and check permissions
		$session_user = session_user();

		// Check if trying to query all users (Administrator+), or a single user (User+)
		if ((empty($value) && !$session_user->has_permission(role::ADMINISTRATOR)) || !$session_user->has_permission(role::USER))
		{
			echo json_status("403 Forbidden");
			$app->halt(403);
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
			$app->halt(404);
			return;
		}
	});

	// RUN IT! - - - - - - - - - - - - - - - - - - - - - - -
	$app->run();
