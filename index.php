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
			echo json_encode($user->to_array());
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
