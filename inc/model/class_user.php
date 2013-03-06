<?php
	// class_user.php - Khan Academy Workflow, 2/5/13
	// PHP class which contains storage and manipulation for user objects
	//
	// changelog:
	//
	// 2/27/13 MDL:
	//	- added filter_users(), enabled fetch_users() to accept values array for fine-grained output
	//	- added fetching of associated videos for a user
	// 2/26/13 MDL:
	//	- set login_db() as default login method
	// 2/11/13 MDL:
	//	- added database sanity checks to object methods
	//	- added selftest() function for checking all class functionality
	// 2/7/13 MDL:
	//	- can now get_user() or fetch_users() by allowed unique keys, both return proper object or list
	// 2/6/13 MDL:
	//	- finalization of methods
	// 2/5/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	require_once __DIR__ . "/../class_config.php";
	config::load(array("database", "login_db", "login_ssh", "password", "video"));

	class user
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -

		// Allowed and disallowed fields for query
		protected static $FIELDS = array(
			"id" => true,
			"username" => true,
			"email" => true,
		);

		// Allowed and disallowed fields for filter
		protected static $FILTERS = array(
			"id" => true,
			"username" => true,
			"email" => true,
			"roleid" => true,
			"enabled" => true,
			"expired" => true,
			"firstname" => true,
			"lastname" => true,
		);

		// INSTANCE VARIABLES - - - - - - - - - - - - - - - - - -

		// Database columns
		private $id;
		private $username;
		private $email;
		private $roleid;
		private $enabled;
		private $password;
		private $expired;
		private $salt;
		private $firstname;
		private $lastname;

		// Helper objects
		private $login;
		private $videos;

		// PUBLIC PROPERTIES - - - - - - - - - - - - - - - - - - 

		// id:
		//	- get: id
		//	- set: n/a, changing an ID would be a bad idea
		public function get_id()
		{
			return $this->id;
		}

		// username:
		//	- get: username
		//	- set: username
		public function get_username()
		{
			return $this->username;
		}
		public function set_username($username)
		{
			$this->username = $username;
			return true;
		}

		// email
		//	- get: email
		//	- set: email (validated by filter_var())
		public function get_email()
		{
			return $this->email;
		}
		public function set_email($email)
		{
			// Validate using filter_var()
			if (filter_var($email, FILTER_VALIDATE_EMAIL))
			{
				$this->email = $email;
				return true;
			}
			
			return false;
		}

		// roleid:
		//	- get: roleid
		//	- set: roleid (validated by is_int())
		public function get_roleid()
		{
			return $this->roleid;
		}
		public function set_roleid($roleid)
		{
			// Validate using is_int()
			if (is_int($roleid))
			{
				$this->roleid = $roleid;
				return true;
			}

			return false;
		}

		// enabled:
		//	- get: enabled
		//	- set: enabled (validated by is_bool())
		public function get_enabled()
		{
			return $this->enabled;
		}
		public function set_enabled($enabled)
		{
			// Convert integer input into bool
			if (is_int($enabled))
			{
				$enabled = $enabled === 1 ? true : false;
			}

			// Validate using is_bool()
			if (is_bool($enabled))
			{
				$this->enabled = $enabled;
				return true;
			}

			return false;
		}

		// password:
		//	- get: password (hash)
		//	- set: password (hash), and salt for security
		public function get_password()
		{
			return $this->password;
		}
		public function set_password($password)
		{
			// Generate new salt using mcrypt
			$this->salt = self::generate_salt();

			// Set options array
			$options = array(
				"cost" => config::HASH_COST,
				"salt" => $this->salt
			);

			// Use PHP password API to generate new password
			$this->password = password_hash($password, config::HASH_ALGORITHM, $options);
			return true;
		}
		
		// expired:
		//	- get: expired
		//	- set: expired (validated by is_bool())
		public function get_expired()
		{
			return $this->expired;
		}
		public function set_expired($expired)
		{
			// Convert integer input into bool
			if (is_int($expired))
			{
				$expired = $expired === 1 ? true : false;
			}

			// Validate using is_bool()
			if (is_bool($expired))
			{
				$this->expired = $expired;
				return true;
			}

			return false;
		}

		// salt:
		//	- get: salt
		//	- set: n/a, done by set_password()
		public function get_salt()
		{
			return $this->salt;
		}

		// firstname:
		//	- get: firstname
		//	- set: firstname
		public function get_firstname()
		{
			return $this->firstname;
		}
		public function set_firstname($firstname)
		{
			$this->firstname = $firstname;
			return true;
		}

		// lastname:
		//	- get: lastname
		//	- set: lastname
		public function get_lastname()
		{
			return $this->lastname;
		}
		public function set_lastname($lastname)
		{
			$this->lastname = $lastname;
			return true;
		}

		// login:
		//	- get: n/a, done by class
		//	- set: login type (validated by is_object())
		public function set_login($login)
		{
			// Validate using is_object()
			if (is_object($login))
			{
				$this->login = $login;
				return true;
			}
			
			return false;
		}

		// videos:
		//	- get: videos (lazy-load, only fetch when needed)
		//	- set: n/a, not handled by this class
		public function get_videos()
		{
			// Check if videos already fetched
			if (!isset($this->videos))
			{
				// Get videos associated with this user
				$this->videos = video::fetch_videos("userid", $this->id);
			}

			return $this->videos;
		}

		// CONSTRUCTOR/DESTRUCTOR - - - - - - - - - - - - - - - -

		// Default blank constructor (helpful for filling fields)
		public function __construct()
		{

		}

		// Default blank destructor
		public function __destruct()
		{

		}

		// PRIVATE METHODS - - - - - - - - - - - - - - - - - - -

		// PUBLIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Store this user object in the database
		public function set_user()
		{
			// Check for existing user
			$result = database::query("SELECT id FROM users WHERE id=?;", $this->id);

			// If user doesn't exist, insert this object
			if (count($result) === 0)
			{
				// Store user object by fields in database
				$success = database::query("INSERT INTO users VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?);", $this->username, $this->email, $this->roleid, $this->enabled, $this->password, $this->expired, $this->salt, $this->firstname, $this->lastname);

				// Check for success
				if ($success)
				{
					// Set user's ID from database return (last insert ID)
					$this->id = $success;
				}
				else
				{
					// On failure, trigger warning	
					trigger_error("user->set_user() database insert failed with code: '" . $success . "'", E_USER_WARNING);
				}
			}
			else
			{
				// Else, update this object
				$success = database::query("UPDATE users SET username=?, email=?, roleid=?, enabled=?, password=?, expired=?, salt=?, firstname=?, lastname=? WHERE id=?;", $this->username, $this->email, $this->roleid, $this->enabled, $this->password, $this->expired, $this->salt, $this->firstname, $this->lastname, $this->id);

				// Check for failure
				if (!$success)
				{
					// On failure, trigger warning
					trigger_error("user->set_user() database update failed with code: '" . $success . "'", E_USER_WARNING);
				}
			}

			return $success;
		}

		// Remove this user object from the database
		public function delete_user()
		{
			// Remove user object by ID from database
			$success = database::query("DELETE FROM users WHERE id=?;", $this->id);

			// Check for failure
			if (!$success)
			{
				// On failure, trigger warning
				trigger_error("user->delete_user() database delete failed with code: '" . $success . "'", E_USER_WARNING);	
			}

			return $success;
		}

		// Authenticate user using strategy pattern authentication
		public function authenticate($input)
		{
			// Ensure login is enabled
			if ($this->enabled)
			{
				// Ensure input is an array
				if (!is_array($input))
				{
					$input = array($input);
				}

				// Check to ensure login set
				if (!isset($this->login))
				{
					// If it isn't, default to login_db
					$this->set_login(new login_db());

					// Set options for login_db
					$input["username"] = $this->username;
					$input["password_hash"] = $this->password;
					$input["salt"] = $this->salt;
				}
				
				// Generate login strategy based upon passed object type
				$login = new login($this->login);

				// Attempt authentication via specified strategy
				return $login->authenticate($input);
			}
			else
			{
				// Prevent authentication for disabled users
				return false;
			}
		}

		// STATIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Generate and fill a new user object using pseudo-constructor
		public static function create_user($username, $email, $roleid, $enabled, $password, $firstname, $lastname)
		{
			$instance = new self();

			$instance->set_username($username);
			$instance->set_email($email);
			$instance->set_roleid($roleid);
			$instance->set_enabled($enabled);
			$instance->set_password($password);
			$instance->set_expired(false);
			$instance->set_firstname($firstname);
			$instance->set_lastname($lastname);

			return $instance;
		}

		// Populate user object from fields in database
		public static function get_user($value, $field = "id")
		{
			// Sanitize user's field input, as that won't be part of the prepared query
			$field = database::sanitize($field);

			// Check for valid, unique field
			if (!in_array($field, array_keys(self::$FIELDS)) || !self::$FIELDS[$field])
			{
				// Return null on bad field
				trigger_error("user::get_user() cannot get using invalid field '" . $field . "'", E_USER_WARNING);
				return null;
			}
			
			$results = database::query("SELECT * FROM users WHERE $field=?;", $value);

			if ($results)
			{
				// Generate user object populated with fields from database
				$user = new user();
				foreach($results[0] as $key => $val)
				{
					$user->{$key} = $val;
				}

				return $user;
			}
			else
			{
				// Return null if no result
				return null;
			}
		}

		// Create a list of user objects using specified parameters
		public static function fetch_users($field = "id", $values = null)
		{
			// Sanitize user's field input, as that won't be part of the prepared query
			$field = database::sanitize($field);

			// Check for valid, unique field
			if (!in_array($field, array_keys(self::$FIELDS)) || !self::$FIELDS[$field])
			{
				// Return null and trigger error on bad field
				trigger_error("user::fetch_users() cannot fetch using invalid field '" . $field . "'", E_USER_WARNING);
				return null;
			}

			// Check for specified values to fetch into list
			if (isset($values) && is_array($values))
			{
				$query = "";
				// Iterate values to build query with filtering
				for ($i = 0; $i < count($values); $i++)
				{
					// Sanitize values
					$v = database::sanitize($values[$i]);

					// On last iteration, stop adding OR statements
					if ($i === count($values) - 1)
					{
						$query .= "$field='$v'";
					}
					else
					{
						$query .= "$field='$v' OR ";
					}
				}

				// Query for a list of users from values in array
				$results = database::query("SELECT $field FROM users WHERE $query ORDER BY $field ASC;");
			}
			else
			{
				// Query for a list of users using specified field
				$results = database::query("SELECT $field FROM users ORDER BY $field ASC;");
			}

			if ($results)
			{	
				// Generate list of user objects
				$users = array();
				for ($i = 0; $i < count($results); $i++)
				{
					// Parse value from array
					$r = $results[$i][$field];
					$users[] = self::get_user($r, $field);
				}

				return $users;
			}
			else
			{
				// Return null if no results
				return null;
			}
		}

		// Create a list of user objects using specified filter
		public static function filter_users($filter, $value)
		{
			// Sanitize user filter input
			$filter = database::sanitize($filter);

			// Check for valid filter field
			if (!in_array($filter, array_keys(self::$FILTERS)) || !self::$FILTERS[$filter])
			{
				// Return null and trigger error on bad filter
				trigger_error("user::filter_users() cannot filter using invalid field '" . $filter . "'", E_USER_WARNING);
				return null;
			}

			// Query for a filtered list of users matching the wildcard value for specified field
			$results = database::query("SELECT id FROM users WHERE $filter LIKE ? ORDER BY id ASC;", '%' . $value . '%');

			if ($results)
			{	
				// Generate list of user IDs
				$id = array();
				for ($i = 0; $i < count($results); $i++)
				{
					// Parse value from array
					$id[] = $results[$i]["id"];
				}

				// Hand off array to fetch_users() to generate list of user objects
				return self::fetch_users("id", $id);
			}
			else
			{
				// Return null if no results
				return null;
			}
		}

		// Generate random salt for password hashing
		public static function generate_salt()
		{
			return mcrypt_create_iv(64, MCRYPT_DEV_URANDOM);
		}

		// Selftest function for debugging
		public static function selftest()
		{
			// Test create_user()
			$user = self::create_user("test", "test@test.com", 0, false, "test", "test", "test");
			if (!$user)
			{
				trigger_error("user::selftest(): user::create_user() failed with status: '" . $user . "'", E_USER_WARNING);
				return false;
			}

			// Test set_user()
			$success = $user->set_user();
			if (!$success)
			{
				trigger_error("user::selftest() user->set_user() insert failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Test set_email()
			$success = $user->set_email("Test@test.com");
			if (!$success)
			{
				trigger_error("user::selftest() user->set_email() failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Test set_roleid()
			$success = $user->set_roleid(1);
			if (!$success)
			{
				trigger_error("user::selftest() user->set_roleid() failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Test set_user()
			$success = $user->set_user();
			if (!$success)
			{
				trigger_error("user::selftest() user->set_user() update failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Re-fetch user
			$id = $user->get_id();
			unset($user);

			// Test get_user()
			$user = user::get_user($id);
			if (!$user)
			{
				trigger_error("user::selftest(): user::get_user() failed with status: '" . $user . "'", E_USER_WARNING);
				return false;
			}

			// Test delete_user()
			$success = $user->delete_user();
			if (!$success)
			{
				trigger_error("user::selftest() user->delete_user() update failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Test fetch_users()
			$users = user::fetch_users();
			if (!$users)
			{
				trigger_error("user::selftest(): user::fetch_users() failed with status: '" . $users . "'", E_USER_WARNING);
				return false;
			}

			// Test filter_users()
			$users = user::filter_users("username", "a");
			if (!$users)
			{
				trigger_error("user::selftest(): user::filter_users() failed with status: '" . $users . "'", E_USER_WARNING);
				return false;
			}

			// If all tests pass, return true
			printf("user::selftest(): all tests passed\n");
			return true;
		}
	}
