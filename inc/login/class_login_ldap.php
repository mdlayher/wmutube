<?php
	// class_login_ldap.php - Khan Academy Workflow, 3/3/13
	// PHP class which provides experimental LDAP login for strategy pattern authentication
	//
	// changelog:
	//
	// 3/6/13 MDL:
	//	- set default host constant
	// 3/3/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	require_once __DIR__ . "/../class_config.php";
	config::load(array("database", "login"));

	class login_ldap extends login_strategy
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -

		// Default LDAP host when none specified
		const DEFAULT_HOST = "ldap.wmich.edu";

		// Allowed LDAP hosts with necessary data
		protected static $HOSTS = array(
			"ldap.wmich.edu" => array(
				"port" => 389,
				"dn" => "wmuuid=%s,ou=People,o=wmich.edu,dc=wmich,dc=edu",
			),
		);

		// PUBLIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Perform authentication against LDAP server
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
				if (!in_array($host, array_keys(self::$HOSTS)))
				{
					trigger_error("login_ldap->authenticate() attempted to connect to non-whitelisted host '" . $host . "'", E_USER_WARNING);
					return false;
				}

				// Check to ensure LDAP extension is loaded
				if (!function_exists("ldap_connect"))
				{
					trigger_error("login_ldap->authenticate() cannot authenticate, LDAP extension not loaded", E_USER_WARNING);
					return false;
				}

				// Attempt to connect to LDAP server
				$ldap = ldap_connect($host, self::$HOSTS[$host]["port"]);
				if (!$ldap)
				{
					trigger_error("login_ldap->authenticate() failed to connect to host '" . $host . "'", E_USER_WARNING);
					return false;
				}

				// Generate LDAP DN using input parameters
				$ldapdn = sprintf(self::$HOSTS[$host]["dn"], $username);

				// Attempt to bind to LDAP server using credentials
				$success = @ldap_bind($ldap, $ldapdn, $password);

				// Disconnect from LDAP server, return authentication status
				ldap_unbind($ldap);
				return $success;
			}
			else
			{
				// Trigger error if missing parameters
				trigger_error("login_ldap->authenticate() missing parameters (username, password) to use LDAP authentication", E_USER_WARNING);
				return false;
			}
		}
	}
