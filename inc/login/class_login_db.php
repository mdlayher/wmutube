<?php
	// class_login_db.php - Khan Academy Workflow, 2/12/13
	// PHP class which provides database driven login for strategy pattern authentication
	//
	// changelog:
	//
	// 2/28/13 MDL:
	//	- initial impementation of bcrypt password hash verification!
	// 2/27/13 MDL:
	//	- renamed to login_db
	// 2/19/13 MDL:
	//	- initial stub code
	// 2/12/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	require_once __DIR__ . "/../class_config.php";
	config::load(array("database", "login", "password"));

	class login_db extends login_strategy
	{
		// PUBLIC METHODS - - - - - - - - - - - - - - - - - - - -

		public function authenticate($input)
		{
			// Check to ensure all necessary parameters are set
			if (isset($input['username'], $input['password'], $input['password_hash'], $input['salt']))
			{
				// Sanitize input for safety
				$username = database::sanitize($input['username']);
				$password = database::sanitize($input['password']);
				$password_hash = database::sanitize($input['password_hash']);
				$salt = database::sanitize($input['salt']);

				// Set options for bcrypt hashing
				$options = array(
					"cost" => config::HASH_COST,
					"salt" => $salt
				);

				// Perform password hash
				$hash = password_hash($password, config::HASH_ALGORITHM, $options);

				// Attempt to match hashes for authentication
				return $success = ($hash === $password_hash) ? true : false;
			}
			else
			{
				// Trigger error if missing parameters
				trigger_error("login_db->authenticate() missing parameters to use database authentication", E_USER_WARNING);
				return false;
			}
		}
	}
?>
