<?php
	// class_db_login.php - Khan Academy Workflow, 2/12/13
	// PHP class which provides database driven login for strategy pattern authentication
	//
	// changelog:
	//
	// 2/12/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	require_once __DIR__ "/../class_config.php";
	config::load("login");

	class db_login extends login_strategy
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -

		// Debug mode
		const DEBUG = 0;

		// INSTANCE VARIABLES - - - - - - - - - - - - - - - - - -

		// CONSTRUCTOR/DESTRUCTOR - - - - - - - - - - - - - - - -

		// PRIVATE METHODS - - - - - - - - - - - - - - - - - - -

		// PUBLIC METHODS - - - - - - - - - - - - - - - - - - - -

		public function authenticate($username, $password, $salt = null)
		{		
			return hash("sha256", $password . $salt);
		}

		// STATIC METHODS - - - - - - - - - - - - - - - - - - - -
	}
?>
