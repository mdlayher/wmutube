<?php
	// class_login_wavebox.php - Khan Academy Workflow, 3/3/13
	// PHP class which provides experimental WaveBox login for strategy pattern authentication
	//
	// changelog:
	//
	// 3/3/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	require_once __DIR__ . "/../class_config.php";
	config::load(array("database", "login"));

	class login_wavebox extends login_strategy
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -

		// Allowed WaveBox hosts with necessary data
		protected static $HOSTS = array(
			"localhost" => array(
				"port" => 6500,
			),
		);

		// PUBLIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Perform authentication against WaveBox server
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
					trigger_error("login_wavebox->authenticate() attempted to connect to non-whitelisted host '" . $host . "'", E_USER_WARNING);
					return false;
				}

				// Create WaveBox server connection string
				$wavebox = sprintf("http://%s:%s/api/login?u=%s&p=%s", $host, self::$HOSTS[$host]["port"], $username, $password);

				// Attempt to create WaveBox connection for authentication
				$response = file_get_contents($wavebox);
				if (!strpos($response, "sessionId"))
				{
					// Return false on failure
					return false;
				}

				// Close connection, return true on successful login
				return true;
			}
			else
			{
				// Trigger error if missing parameters
				trigger_error("login_wavebox->authenticate() missing parameters to use WaveBox authentication", E_USER_WARNING);
				return false;
			}
		}
	}
