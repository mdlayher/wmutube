<?php
	// class_login_ssh.php - Khan Academy Workflow, 2/27/13
	// PHP class which provides SSH key login for strategy pattern authentication

	error_reporting(E_ALL);

	require_once __DIR__ . "/../class_config.php";
	config::load(array("database", "login"));

	class login_ssh extends login_strategy
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -

		// Authentication methods
		const AUTH_PASSWORD = "password";
		const AUTH_KEY = "key";

		// Default SSH host when none specified
		const DEFAULT_HOST = "bronco.wmich.edu";

		// Allowed SSH hosts with necessary data
		protected static $HOSTS = array(
			"bronco.wmich.edu" => array(
				"port" => 22,
				"fingerprint" => "4E062F6B4EF8FA0586054EF91ACB8E56",
				"method" => array(
					self::AUTH_PASSWORD => true,
					self::AUTH_KEY => false,
				),
			),
			"localhost" => array(
				"port" => 22,
				"fingerprint" => "fingerprint",
				"pubkey" => "/home/%s/.ssh/authorized_keys",
				"method" => array(
					self::AUTH_PASSWORD => false,
					self::AUTH_KEY => true,
				),
			),
		);

		// PUBLIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Return login method
		public function __toString()
		{
			return "SSH";
		}

		// Authenticate to a server using SSH public key authentication
		public function authenticate($input)
		{
			// Check for required parameters
			if (isset($input['username']))
			{
				// Sanitize input for safety
				$username = database::sanitize($input['username']);

				// Check for a set host, use default if not set
				if (!isset($input['host']))
				{
					$host = self::DEFAULT_HOST;
				}
				else
				{
					$host = database::sanitize($input['host']);
				}

				// Check if a method was set, but default to "key" if not
				if (!isset($input['method']))
				{
					$method = self::AUTH_KEY;
				}
				else
				{
					$method = database::sanitize($input['method']);
				}

				// Validate host against hosts array
				if (!array_key_exists($host, self::$HOSTS))
				{
					trigger_error("login_ssh->authenticate() attempted to connect to non-whitelisted host '" . $host . "'", E_USER_WARNING);
					return false;
				}

				// Validate authentication method is valid and allowed
				if (!array_key_exists($method, self::$HOSTS[$host]["method"]) || !self::$HOSTS[$host]["method"][$method])
				{
					trigger_error("login_ssh->authenticate() attempted to authenticate using disallowed method '" . $method . "'", E_USER_WARNING);
					return false;
				}

				// Check to ensure SSH2 extension is loaded
				if (!function_exists("ssh2_connect"))
				{
					trigger_error("login_ssh->authenticate() cannot authenticate, SSH2 extension not loaded", E_USER_WARNING);
					return false;
				}

				// Attempt to create SSH connection for authentication
				$ssh = ssh2_connect($host, self::$HOSTS[$host]["port"]);
				if (!$ssh)
				{
					trigger_error("login_ssh->authenticate() failed to connect to host '" . $host . "'", E_USER_WARNING);
					return false;
				}

				// Check SSH fingerprint to ensure it is valid
				$fingerprint = ssh2_fingerprint($ssh);
				if ($fingerprint !== self::$HOSTS[$host]["fingerprint"])
				{
					trigger_error("login_ssh->authenticate() CRITICAL: failed to verify host fingerprint for host '" . $host . "'!!", E_USER_ERROR);
					return false;
				}

				// Determine login method
				// Key file authentication
				if ($method === self::AUTH_KEY)
				{
					// Check to ensure keyfile parameter passed
					if (!isset($input['keyfile']))
					{
						trigger_error("login_ssh->authenticate() missing keyfile parameter for key-based authentication", E_USER_WARNING);
						return false;
					}
					$keyfile = database::sanitize($input['keyfile']);

					// Check for passphrase, sanitize if found
					if (isset($input['passphrase']))
					{
						$passphrase = database::sanitize($input['passphrase']);
					}

					// Insert username into pubkey path
					$pubkey = sprintf(self::$HOSTS[$host]["pubkey"], $username);

					// Attempt pubkey authentication via SSH, with passphrase if provided
					if (isset($passphrase))
					{
						$success = @ssh2_auth_pubkey_file($ssh, $username, $pubkey, $keyfile, $passphrase);
					}
					else
					{
						$success = @ssh2_auth_pubkey_file($ssh, $username, $pubkey, $keyfile);
					}

					return $success;
				}
				// Password authentication
				else if ($method === self::AUTH_PASSWORD)
				{
					// Check to ensure password parameter passsed
					if (!isset($input['password']))
					{
						trigger_error("login_ssh->authenticate() missing password parameter for password-based authentication", E_USER_WARNING);
						return false;
					}
					$password = database::sanitize($input['password']);

					// Attempt password authentication via SSH
					return @ssh2_auth_password($ssh, $username, $password);
				}
				// Invalid login scheme (should never happen)
				else
				{
					trigger_error("login_ssh->authenticate() invalid SSH login method specified: '" . $method . "'", E_USER_WARNING);
					return false;
				}
			}
			else
			{
				// Trigger error if missing parameters
				trigger_error("login_ssh->authenticate() missing parameters (username) to use SSH authentication", E_USER_WARNING);
				return false;
			}
		}
	}
