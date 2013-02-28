<?php
	// class_login_ssh.php - Khan Academy Workflow, 2/27/13
	// PHP class which provides SSH key login for strategy pattern authentication
	//
	// changelog:
	//
	// 2/27/13 MDL:
	//	- initial code
	//	- IT WORKS!!

	error_reporting(E_ALL);

	require_once __DIR__ . "/../class_config.php";
	config::load(array("database", "login"));

	class login_ssh extends login_strategy
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -

		// Allowed SSH hosts with necessary data
		protected static $HOSTS = array(
			"localhost" => array(
				"port" => 22,
				"fingerprint" => "fingerprint",
				"pubkey" => "/path/to/pubkey"
			),
		);

		// PUBLIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Authenticate to a server using SSH public key authentication
		public function authenticate($input)
		{
			// Check for required parameters
			if (isset($input['host'], $input['username'], $input['keyfile']))
			{
				// Sanitize input for safety
				$host = database::sanitize($input['host']);
				$username = database::sanitize($input['username']);
				$keyfile = database::sanitize($input['keyfile']);

				// Validate host against hosts array
				if (!in_array($host, array_keys(self::$HOSTS)))
				{
					trigger_error("login_ssh->authenticate() attempted to connect to non-whitelisted host '" . $host . "'", E_USER_WARNING);
					return false;
				}

				// Attempt to create SSH connection for authentication
				$ssh = ssh2_connect($host, self::$HOSTS[$host]["port"]);
				if (!$ssh)
				{
					// Trigger error on failure
					trigger_error("login_ssh->authenticate() failed to connect to host '" . $host . "'", E_USER_WARNING);
					return false;
				}

				// Check SSH fingerprint to ensure it is valid
				$fingerprint = ssh2_fingerprint($ssh);
				if ($fingerprint !== self::$HOSTS[$host]["fingerprint"])
				{
					// Trigger error on failure
					trigger_error("login_ssh->authenticate() CRITICAL: failed to verify host fingerprint for host '" . $host . "'!!", E_USER_ERROR);
					return false;
				}

				// Attempt pubkey authentication via SSH
				return ssh2_auth_pubkey_file($ssh, $username, self::$HOSTS[$host]["pubkey"], $keyfile);
			}
			else
			{
				// Trigger error if missing parameters
				trigger_error("login_ssh->authenticate() missing parameters to use SSH authentication", E_USER_WARNING);
				return false;
			}
		}
	}
?>
