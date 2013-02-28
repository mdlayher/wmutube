<?php
	// class_login_db.php - Khan Academy Workflow, 2/12/13
	// PHP class which provides database driven login for strategy pattern authentication
	//
	// changelog:
	//
	// 2/27/13 MDL:
	//	- renamed to login_db
	// 2/19/13 MDL:
	//	- initial stub code
	// 2/12/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	require_once __DIR__ . "/../class_config.php";
	config::load("login");

	class login_db extends login_strategy
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -

		// Debug mode
		const DEBUG = 0;

		// INSTANCE VARIABLES - - - - - - - - - - - - - - - - - -

		// CONSTRUCTOR/DESTRUCTOR - - - - - - - - - - - - - - - -

		// PRIVATE METHODS - - - - - - - - - - - - - - - - - - -

		// PUBLIC METHODS - - - - - - - - - - - - - - - - - - - -

		public function authenticate($input)
		{		
			return "login_db()";
		}

		// STATIC METHODS - - - - - - - - - - - - - - - - - - - -
	}
?>
