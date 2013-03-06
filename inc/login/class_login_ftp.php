<?php
	// class_login_ftp.php - Khan Academy Workflow, 3/4/13
	// PHP class which provides FTP login for strategy pattern authentication
	//
	// changelog:
	//
	// 3/4/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	require_once __DIR__ . "/../class_config.php";
	config::load(array("database", "login"));

	class login_ftp extends login_strategy
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -

		// Allowed FTP hosts with necessary data
		protected static $HOSTS = array(
			"homepages.wmich.edu" => array(
				"port" => 21,
				"timeout" => 5,
			),
		);

		// PUBLIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Perform authentication against FTP server
		public function authenticate($input)
		{
			// Check for required parameters
			if (isset($input['host'], $input['username'], $input['password']))
			{
				// Sanitize input for safety
				$host = database::sanitize($input['host']);
				$username = database::sanitize($input['username']);
				$password = database::sanitize($input['password']);

				// Validate host against hosts array
				if (!in_array($host, array_keys(self::$HOSTS)))
				{
					trigger_error("login_ftp->authenticate() attempted to connect to non-whitelisted host '" . $host . "'", E_USER_WARNING);
					return false;
				}

				// Check to ensure FTP extension is loaded
				if (!function_exists("ftp_connect"))
				{
					trigger_error("login_ftp->authenticate() cannot authenticate, FTP extension not loaded", E_USER_WARNING);
					return false;
				}

				// Attempt to connect to FTP server
				$ftp = ftp_connect($host, self::$HOSTS[$host]["port"], self::$HOSTS[$host]["timeout"]);
				if (!$ftp)
				{
					trigger_error("login_ftp->authenticate() failed to connect to host '" . $host . "'", E_USER_WARNING);
					return false;
				}

				// Attempt to bind to FTP server using credentials
				$success = @ftp_login($ftp, $username, $password);

				// Disconnect from FTP server, return authentication status
				ftp_close($ftp);
				return $success;
			}
			else
			{
				// Trigger error if missing parameters
				trigger_error("login_ftp->authenticate() missing parameters to use FTP authentication", E_USER_WARNING);
				return false;
			}
		}
	}
