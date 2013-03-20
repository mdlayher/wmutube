<?php
	// interface_login.php - Khan Academy Workflow, 2/12/13
	// PHP interface and class for implementation of strategy pattern for user login
	//
	// changelog:
	//
	// 3/6/13 MDL:
	//	- added __toString() to identify login strategies
	// 2/27/13 MDL:
	//	- changed input to array
	// 2/12/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	// Interface which enforces implementation of login method
	interface i_login
	{
		// Ensure implementation of __toString()
		public function __toString();

		// Ensure implementation of authenticate()
		public function authenticate($input);
	}

	// Abstract class which provides strategy selection for login
	abstract class login_strategy implements i_login
	{
		// Default blank constructor
		public function __construct()
		{

		}

		// Unimplemented __toString due to abstract class
		public function __toString()
		{

		}

		// Unimplemented authenticate due to abstract class
		public function authenticate($input)
		{

		}
	}

	// Class which utilizes provided method for authentication
	class login
	{
		// Login method enumerations
		const DB = 0;
		const FTP = 1;
		const IMAP = 2;
		const LDAP = 3;
		const SSH = 4;
		const WAVEBOX = 5;

		// Authentication method
		private $method;

		// Constructor to set authentication method
		public function __construct(login_strategy $method)
		{
			if (is_object($method))
			{
				$this->method = $method;
			}
			else
			{
				trigger_error("login->__construct(): invalid login strategy specified: '" . (string)$method . "'", E_USER_WARNING);
			}
		}

		// Return type of login method
		public function __toString()
		{
			return "login";
		}

		// Perform authentication using specified method
		public function authenticate($input)
		{
			return $this->method->authenticate($input);
		}
	}
