<?php
	// class_template.php - Khan Academy Workflow, 2/5/13
	// PHP class which contains storage and manipulation for user objects
	//
	// changelog:
	//
	// 2/7/13 MDL:
	//	- can now get_user() or fetch_users() by allowed unique keys, both return proper object or list
	// 2/6/13 MDL:
	//	- finalization of methods
	// 2/5/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	require_once "class_database.php";

	class user
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -

		// Allowed and disallowed fields for query
		protected static $FIELDS = array(
			"id" => true,
			"username" => true,
			"email" => true
		);

		// INSTANCE VARIABLES - - - - - - - - - - - - - - - - - -

		private $id;
		private $username;
		private $email;
		private $roleid;
		private $password;
		private $salt;
		private $firstname;
		private $lastname;

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

			// Perform hash function with salt and store new hashed password
			$this->password = self::password_hash($this->salt . $password);
			return true;
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
				$success = database::query("INSERT INTO users VALUES (null, ?, ?, ?, ?, ?, ?, ?);", $this->username, $this->email, $this->roleid, $this->password, $this->salt, $this->firstname, $this->lastname);

				// Set user's ID from database return (last insert ID)
				$this->id = $success;
			}
			else
			{
				// Else, update this object
				$success = database::query("UPDATE users SET username=?, email=?, roleid=?, password=?, salt=?, firstname=?, lastname=? WHERE id=?;", $this->username, $this->email, $this->roleid, $this->password, $this->salt, $this->firstname, $this->lastname, $this->id);
			}

			return $success;
		}

		// Remove this user object from the database
		public function delete_user()
		{
			// Remove user object by ID from database
			$success = database::query("DELETE FROM users WHERE id=?;", $this->id);
			return $success;
		}

		// STATIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Generate and fill a new user object using pseudo-constructor
		public static function create_user($username, $email, $roleid, $password, $firstname, $lastname)
		{
			$instance = new self();

			$instance->set_username($username);
			$instance->set_email($email);
			$instance->set_roleid($roleid);
			$instance->set_password($password);
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
		public static function fetch_users($field = "id")
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

			// Query for a list of users using specified field
			$results = database::query("SELECT $field FROM users ORDER BY $field ASC;");

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

		// Generate appropriate hash for password from input
		public static function password_hash($input)
		{
			return hash("sha256", $input);
		}

		// Generate random salt for password hashing
		public static function generate_salt()
		{
			return mcrypt_create_iv(64, MCRYPT_DEV_URANDOM);
		}
	}
