<?php
	// index.php - Khan Academy Workflow, 3/20/13
	// PHP router using Slim microframework to handle all routing of HTTP calls within the application
	//
	// changelog:
	//
	// 3/20/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	// Composer autoloader
	require_once "vendor/autoload.php";

	// Data model configuration
	require_once "inc/class_config.php";

	// CONFIGURATION - - - - - - - - - - - - - - - - - - - 

	// Create an instance of Slim, set configuration
	use \Slim\Slim as Slim;
	$app = new Slim(array(
		// Enable debug
		"debug" => true,
		// Define path to render templates
		"templates.path" => "./views",
	));
	// Set application's name
	$app->setName("khan");

	// FUNCTIONS - - - - - - - - - - - - - - - - - - - - - -

	function json_status($status)
	{
		return json_encode(array("status" => $status));
	}

	// ROUTING - - - - - - - - - - - - - - - - - - - - - - -

	// VIEWS - - - - - - - - - - - - - - - - - - - - - - - -

	// Application root
	$app->get("/", function() use ($app)
	{
		echo "Hello world!\n";
	});

	// Video upload page
	$app->get("/upload", function() use ($app)
	{
		return $app->render("upload.php");
	});

	// LOGIN - - - - - - - - - - - - - - - - - - - - - - - -

	// Login using specified method
	$app->post("/login", function() use ($app)
	{
		// Parse username and password from request
		$req = $app->request();
		$username = $req->post("username");
		$password = $req->post("password");
		$method = $req->post("method");

		// Check for required parameters
		if (isset($username, $password))
		{
			// Pull user object
			config::load("user");
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
				config::load("login*");
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

	// AJAX - - - - - - - - - - - - - - - - - - - - - - - -

	// Fetch user by ID
	$app->get("/ajax/user/id/:id", function($id) use ($app)
	{
		// Temporary: work out way to implement session security for these
		if ($app->request()->get("s") !== "tempdev")
		{
			$app->notFound();
			return;
		}

		config::load("user");
		$user = user::get_user($id);

		if ($user)
		{
			echo $user->to_json();
			return;
		}
		else
		{
			$app->notFound();
			return;
		}
	});

	// Fetch video by ID
	$app->get("/ajax/video/id/:id", function($id) use ($app)
	{
		// Temporary: work out way to implement session security for these
		if ($app->request()->get("s") !== "tempdev")
		{
			$app->notFound();
			return;
		}

		config::load("video");
		$video = video::get_video($id);

		if ($video)
		{
			printf("%s - %s\n", $video->get_title(), $video->get_filename());
		}
		else
		{
			$app->notFound();
		}
	});

	// RUN IT!
	$app->run();
