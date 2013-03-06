<?php
	// interface_login.php - Khan Academy Workflow, 2/12/13
	// PHP interface and class for implementation of strategy pattern for user login
	//
	// changelog:
	//
	// 2/27/13 MDL:
	//	- changed input to array
	// 2/12/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	// Interface which enforces implementation of authenticate() method
	interface i_login
	{
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

		// Unimplemented authenticate due to abstract class
		public function authenticate($input)
		{

		}
	}

	// Class which utilizes provided method for authentication
	class login
	{
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

		// Perform authentication using specified method
		public function authenticate($input)
		{
			return $this->method->authenticate($input);
		}
	}
