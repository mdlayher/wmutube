<?php
	// class_role.php - Khan Academy Workflow, 3/6/13
	// PHP class which contains storage and manipulation for role objects
	//
	// changelog:
	//
	// 3/6/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	require_once __DIR__ . "/../class_config.php";

	class role
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -

		// Role enumerations
		const GUEST = 1;
		const USER = 2;
		const INSTRUCTOR = 4;
		const ADMINISTRATOR = 8;
		const DEVELOPER = 16;

		// Array of roles, not stored in database as they should (almost) never change
		protected static $ROLES = array(
			self::GUEST => array(
				"id" => self::GUEST,
				"title" => "Guest",
				"description" => "Guest user",
			),
			self::USER => array(
				"id" => self::USER,
				"title" => "User",
				"description" => "Standard user",
			),
			self::INSTRUCTOR => array(
				"id" => self::INSTRUCTOR,
				"title" => "Instructor",
				"description" => "Instructor",
			),
			self::ADMINISTRATOR => array(
				"id" => self::ADMINISTRATOR,
				"title" => "Administrator",
				"description" => "Administrator",
			),
			self::DEVELOPER => array(
				"id" => self::DEVELOPER,
				"title" => "Developer",
				"description" => "Developer",
			),
		);

		// INSTANCE VARIABLES - - - - - - - - - - - - - - - - - -

		private $id;
		private $title;
		private $description;

		// PUBLIC PROPERTIES - - - - - - - - - - - - - - - - - - 

		// id:
		//	- get: id
		//	- set: n/a, constant
		public function get_id()
		{
			return $this->id;
		}

		// title:
		//	- get: title
		//	- set: n/a, constant
		public function get_title()
		{
			return $this->title;
		}

		// description:
		//	- get: description
		//	- set: n/a, constant
		public function get_description()
		{
			return $this->description;
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

		// STATIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Populate role object from ID in array
		public static function get_role($id)
		{
			// Check if ID is in array
			if (array_key_exists($id, self::$ROLES))
			{
				// Generate object from array
				$role = new self();
				foreach (self::$ROLES[$id] as $key => $val)
				{
					$role->{$key} = $val;
				}

				return $role;
			}

			return null;
		}

		// Create a list of role objects
		public static function fetch_roles()
		{
			// Generate array of role objects
			$roles = array();
			foreach (array_keys(self::$ROLES) as $r)
			{
				$roles[] = self::get_role($r);
			}

			return $roles;
		}

		// Test all functionality of role class
		public static function selftest()
		{
			// Test get_role()
			$role = role::get_role(1);
			if (!$role)
			{
				trigger_error("role::selftest(): role::get_role() failed with status: '" . $role . "'", E_USER_WARNING);
				return false;
			}

			// Test fetch_roles()
			$roles = role::fetch_roles();
			if (!$roles)
			{
				trigger_error("role::selftest(): role::fetch_roles() failed with status: '" . $roles . "'", E_USER_WARNING);
				return false;
			}

			// If all tests pass, return true
			printf("role::selftest(): all tests passed\n");
			return true;
		}
	}
