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
				// For basic authentication
				"port" => 389,
				"dn" => "wmuuid=%s,ou=People,o=wmich.edu,dc=wmich,dc=edu",
				// For synchronization of users from LDAP to our database
				"sync" => true,
				"filter" => "(&(uid=%s)(objectclass=wmichEduPerson))",
				"attributes" => array("givenname", "sn", "mail"),
			),
		);

		// PUBLIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Return login method
		public function __toString()
		{
			return "LDAP";
		}

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
				if (!array_key_exists($host, self::$HOSTS))
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

				// See if user already exists in database
				$user = user::get_user($username, "username");

				// Check for successful bind, but user not in database
				if ($success && !$user)
				{
					if (!self::$HOSTS[$host]["sync"])
					{
						trigger_error("login_ldap->authenticate() user sync disallowed for host '" . $host . "'", E_USER_WARNING);
						return false;
					}

					// Set filter to gather information about this user
					$filter = sprintf(self::$HOSTS[$host]["filter"], $username);

					// Query LDAP for information about this user
					$search = ldap_read($ldap, $ldapdn, $filter, self::$HOSTS[$host]["attributes"]);
					$query_result = ldap_get_entries($ldap, $search);

					// Check for query result
					if (!isset($query_result[0]))
					{
						trigger_error("login_ldap->authenticate() failed to query LDAP for user '" . $username . "'", E_USER_WARNING);
						return false;
					}

					// Iterate query results to grab firstname, lastname, email address
					$user = array();
					foreach (self::$HOSTS[$host]["attributes"] as $a)
					{
						if (isset($query_result[0][$a][0]))
						{
							$user[] = $query_result[0][$a][0];
						}
						else
						{
							trigger_error("login_ldap->authenticate() failed to parse LDAP query results for user '" . $username . "'", E_USER_WARNING);
							return false;
						}
					}

					// Split user into fields
					list($firstname, $lastname, $email) = $user;

					// Generate a new user account from this LDAP result
					$user = user::create_user($username, $email, role::USER, 1, $password, $firstname, $lastname);
					$user->set_user();
				}

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
