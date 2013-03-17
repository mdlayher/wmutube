<?php
	// class_course.php - Khan Academy Workflow, 2/11/13
	// PHP class which contains storage and manipulation for course objects
	//
	// changelog:
	//
	// 2/19/13 MDL:
	//	- added year and term fields for finer granularity in courses
	// 2/11/13 MDL:
	//	- initial code - copied from class_course and tweaked

	error_reporting(E_ALL);

	require_once __DIR__ . "/../class_config.php";
	config::load("database");

	class course
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -
		
		// Allowed and disallowed fields for query
		protected static $FIELDS = array(
			"id" => true,
			// todo: composite field query (via array?)
		);

		// INSTANCE VARIABLES - - - - - - - - - - - - - - - - - -

		private $id;
		private $year;
		private $term;
		private $subject;
		private $number;
		private $title;

		// PUBLIC PROPERTIES - - - - - - - - - - - - - - - - - - 

		// id:
		//	- get: id
		//	- set: n/a, changing an ID would be a bad idea
		public function get_id()
		{
			return $this->id;
		}

		// year:
		//	- get: year
		//	- set: year (validated by is_int())
		public function get_year()
		{
			return $this->year;
		}
		public function set_year($year)
		{
			// Validate using is_int()
			if (is_int($year))
			{
				$this->year = $year;
				return true;
			}

			return false;
		}

		// term
		//	- get: term
		//	- set: term
		public function get_term()
		{
			return $this->term;
		}
		public function set_term($term)
		{
			$this->term = $term;
			return true;
		}

		// subject
		//	- get: subject
		//	- set: subject
		public function get_subject()
		{
			return $this->subject;
		}
		public function set_subject($subject)
		{
			$this->subject = $subject;
			return true;
		}

		// number
		//	- get: number
		//	- set: number (validated by is_int())
		public function get_number()
		{
			return $this->number;
		}
		public function set_number($number)
		{
			// Validate using is_int()
			if (is_int($number))
			{
				$this->number = $number;
				return true;
			}

			return false;
		}

		// title:
		//	- get: title
		//	- set: title
		public function get_title()
		{
			return $this->title;
		}
		public function set_title($title)
		{
			$this->title = $title;
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

		// Store this course object in the database
		public function set_course()
		{
			// Check for existing course
			$result = database::query("SELECT id FROM courses WHERE id=?;", $this->id);

			// If course doesn't exist, insert this object
			if (count($result) === 0)
			{
				// Store course object by fields in database
				$success = database::query("INSERT INTO courses VALUES (null, ?, ?, ?, ?, ?);", $this->year, $this->term, $this->subject, $this->number, $this->title);

				// Check for success
				if ($success)
				{	
					// Set course's ID from database return (last insert ID)
					$this->id = $success;
				}
				else
				{
					// On failure, trigger warning	
					trigger_error("course->set_course() database insert failed with code: '" . $success . "'", E_USER_WARNING);
				}

			}
			else
			{
				// Else, update this object
				$success = database::query("UPDATE courses SET year=?, term=?, subject=?, number=?, title=? WHERE id=?;", $this->year, $this->term, $this->subject, $this->number, $this->title, $this->id);

				// Check for failure
				if (!$success)
				{
					// On failure, trigger warning	
					trigger_error("course->set_course() database update failed with code: '" . $success . "'", E_USER_WARNING);
				}
			}

			return $success;
		}

		// Remove this course object from the database
		public function delete_course()
		{
			// Remove course object by ID from database
			$success = database::query("DELETE FROM courses WHERE id=?;", $this->id);

			// Check for failure
			if (!$success)
			{
				// On failure, trigger warning
				trigger_error("course->delete_course() database delete failed with code: '" . $success . "'", E_USER_WARNING);
			}

			return $success;
		}

		// STATIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Generate and fill a new course object using pseudo-constructor
		public static function create_course($year, $term, $subject, $number, $title)
		{
			$instance = new self();

			$instance->set_year($year);
			$instance->set_term($term);
			$instance->set_subject($subject);
			$instance->set_number($number);
			$instance->set_title($title);

			return $instance;
		}

		// Populate course object from fields in database
		public static function get_course($value, $field = "id")
		{
			// Sanitize course's field input, as that won't be part of the prepared query
			$field = database::sanitize($field);

			// Check for valid, unique field
			if (!array_key_exists($field, self::$FIELDS) || !self::$FIELDS[$field])
			{
				// Return null on bad field
				trigger_error("course::get_course() cannot get using invalid field '" . $field . "'", E_USER_WARNING);
				return null;
			}
			
			$results = database::query("SELECT * FROM courses WHERE $field=?;", $value);

			if ($results)
			{
				// Generate course object populated with fields from database
				$course = new self();
				foreach($results[0] as $key => $val)
				{
					$course->{$key} = $val;
				}

				return $course;
			}
			else
			{
				// Return null if no result
				return null;
			}
		}

		// Create a list of course objects using specified parameters
		public static function fetch_courses($field = "id")
		{
			// Sanitize course's field input, as that won't be part of the prepared query
			$field = database::sanitize($field);

			// Check for valid, unique field
			if (!array_key_exists($field, self::$FIELDS) || !self::$FIELDS[$field])
			{
				// Return null and trigger error on bad field
				trigger_error("course::fetch_courses() cannot fetch using invalid field '" . $field . "'", E_USER_WARNING);
				return null;
			}

			// Query for a list of courses using specified field
			$results = database::query("SELECT $field FROM courses ORDER BY $field ASC;");

			if ($results)
			{	
				// Generate list of course objects
				$courses = array();
				for ($i = 0; $i < count($results); $i++)
				{
					// Parse value from array
					$r = $results[$i][$field];
					$courses[] = self::get_course($r, $field);
				}

				return $courses;
			}
			else
			{
				// Return null if no results
				return null;
			}
		}
		
		// Selftest function for debugging
		public static function selftest()
		{
			// Test create_course()
			$course = self::create_course(2013, "Spring", "TEST", 1000, "Test Course");
			if (!$course)
			{
				trigger_error("course::selftest(): course::create_course() failed with status: '" . $course . "'", E_USER_WARNING);
				return false;
			}

			// Test set_course()
			$success = $course->set_course();
			if (!$success)
			{
				trigger_error("course::selftest() course->set_course() insert failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Test set_number()
			$success = $course->set_number(1001);
			if (!$success)
			{
				trigger_error("course::selftest() course->set_number() failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Test set_course()
			$success = $course->set_course();
			if (!$success)
			{
				trigger_error("course::selftest() course->set_course() update failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Re-fetch course
			$id = $course->get_id();
			unset($course);

			// Test get_course()
			$course = course::get_course($id);
			if (!$course)
			{
				trigger_error("course::selftest(): course::get_course() failed with status: '" . $course . "'", E_USER_WARNING);
				return false;
			}

			// Test delete_course()
			$success = $course->delete_course();
			if (!$success)
			{
				trigger_error("course::selftest() course->delete_course() update failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Test fetch_courses()
			$courses = course::fetch_courses();
			if (!$courses)
			{
				trigger_error("course::selftest(): course::fetch_courses() failed with status: '" . $courses . "'", E_USER_WARNING);
				return false;
			}

			// If all tests pass, return true
			printf("course::selftest(): all tests passed\n");
			return true;
		}
	}
