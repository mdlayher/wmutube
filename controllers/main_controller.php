<?php
	require_once __DIR__ . "/../inc/class_config.php";
	config::load("user");

	class MainController extends AppController
	{
		function index()
		{
			$this->setPageTitle("Khan Academy Clone");

			// Load user object
			$this->set('user', user::get_user(1));

			return;
		}
	}
?>
