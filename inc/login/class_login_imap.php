<?php
	// class_login_imap.php - Khan Academy Workflow, 3/3/13
	// PHP class which provides experimental IMAP login for strategy pattern authentication
	// Note: IMAP login is terribly slow on password failure.  Need to research this.

	error_reporting(E_ALL);

	require_once __DIR__ . "/../class_config.php";
	config::load(array("database", "login"));

	class login_imap extends login_strategy
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -

		// Default IMAP host when none specified
		const DEFAULT_HOST = "imap.wmich.edu";

		// Allowed IMAP hosts with necessary data
		protected static $HOSTS = array(
			"imap.wmich.edu" => array(
				"port" => 993,
				"path" => "/ssl/novalidate-cert",
				"folder" => "INBOX",
				"options" => OP_HALFOPEN,
				"retries" => 1,
				"params" => array('DISABLE_AUTHENTICATOR' => 'GSSAPI'),
			),
		);

		// PUBLIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Return login method
		public function __toString()
		{
			return "IMAP";
		}

		// Perform authentication against IMAP server
		public function authenticate($input)
		{
			// Check for required parameters
			if (isset($input['username'], $input['password']))
			{
				// Sanitize input for safety
				$username = database::sanitize($input['username']);
				$password = database::sanitize($input['password']);

				// Check for a set host, use default if not set
				if (!isset($input['host']))
				{
					$host = self::DEFAULT_HOST;
				}
				else
				{
					$host = database::sanitize($input['host']);
				}

				// Validate host against hosts array
				if (!array_key_exists($host, self::$HOSTS))
				{
					trigger_error("login_imap->authenticate() attempted to connect to non-whitelisted host '" . $host . "'", E_USER_WARNING);
					return false;
				}

				// Check to ensure IMAP extension is loaded
				if (!function_exists("imap_open"))
				{
					trigger_error("login_imap->authenticate() cannot authenticate, IMAP extension not loaded", E_USER_WARNING);
					return false;
				}

				// Generate IMAP mailbox string using input parameters
				$mailbox = sprintf("{%s:%d/imap%s}%s", $host, self::$HOSTS[$host]["port"], self::$HOSTS[$host]["path"], self::$HOSTS[$host]["folder"]);

				// Attempt to create IMAP connection for authentication
				$imap = @imap_open($mailbox, $username, $password, self::$HOSTS[$host]["options"], self::$HOSTS[$host]["retries"], self::$HOSTS[$host]["params"]);
				imap_errors();
				if (!$imap)
				{
					// Return false on failure
					return false;
				}

				// Close connection, return true on successful login
				imap_close($imap);
				return true;
			}
			else
			{
				// Trigger error if missing parameters
				trigger_error("login_imap->authenticate() missing parameters (username, password) to use IMAP authentication", E_USER_WARNING);
				return false;
			}
		}
	}
