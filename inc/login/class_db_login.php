<?php
	// class_db_login.php - Khan Academy Workflow, 2/12/13
	// PHP class which provides database driven login for strategy pattern authentication
	//
	// changelog:
	//
	// 2/12/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	require_once "interface_login.php";

	class db_login extends login_strategy
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -

		// Debug mode
		const DEBUG = 0;

		// INSTANCE VARIABLES - - - - - - - - - - - - - - - - - -

		// CONSTRUCTOR/DESTRUCTOR - - - - - - - - - - - - - - - -

		// PRIVATE METHODS - - - - - - - - - - - - - - - - - - -

		// PUBLIC METHODS - - - - - - - - - - - - - - - - - - - -

		public function authenticate()
		{		
			return "db";
		}

		// STATIC METHODS - - - - - - - - - - - - - - - - - - - -
	}
?>
