<?php
	// class_login_ssh.php - Khan Academy Workflow, 2/12/13
	// PHP class which provides SSH key login for strategy pattern authentication
	//
	// changelog:
	//
	// 2/27/13 MDL:
	//	- initial stub code

	error_reporting(E_ALL);

	require_once __DIR__ . "/../class_config.php";
	config::load("login");

	class login_ssh extends login_strategy
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
			return "ssh_login()";
		}

		// STATIC METHODS - - - - - - - - - - - - - - - - - - - -
	}
?>
