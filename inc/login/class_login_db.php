<?php
	// class_login_db.php - Khan Academy Workflow, 2/12/13
	// PHP class which provides database driven login for strategy pattern authentication
	//
	// changelog:
	//
	// 3/2/13 MDL:
	//	- improvements to password hashing, resistance to timing attacks, rehash if needed
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

		// Perform authentication against application's database
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

				// Verify password hash, return false on failure
				if (!password_verify($password, $password_hash))
				{
					return false;
				}

				// Determine if password needs to be rehashed in database due to updated algorithm
				if (password_needs_rehash($password_hash, PASSWORD_DEFAULT, $options))
				{
					// Retrieve user object, update password, save state
					$user = user::get_user($username, "username");
					$user->set_password($password);
					$user->set_user();
				}

				// Return true on successful login
				return true;
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
