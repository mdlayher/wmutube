<?php
	// class_template.php - Khan Academy Workflow, 2/5/13
	// PHP class which contains storage and manipulation for video objects
	//
	// changelog:
	//
	// 2/11/13 EJL:
	//	- initial code - copied from class_user and tweaked

	error_reporting(E_ALL);

	require_once "class_database.php";

	class video
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -

		// Allowed and disallowed fields for query
		protected static $FIELDS = array(
			"id" => true,
			"userid" => true,
			"courseid" => true
		);

		// INSTANCE VARIABLES - - - - - - - - - - - - - - - - - -

		private $id;
		private $userid;
		private $courseid;
		private $filename;
		private $title;
		private $keywords;

		// PUBLIC PROPERTIES - - - - - - - - - - - - - - - - - - 

		// id:
		//	- get: id
		//	- set: n/a, changing an ID would be a bad idea
		public function get_id()
		{
			return $this->id;
		}

		// userid:
		//	- get: userid
		//	- set: userid (validated by is_int())
		public function get_userid()
		{
			return $this->userid;
		}
		public function set_userid($userid)
		{
			// Validate using is_int()
			if (is_int($userid))
			{
				$this->userid = $userid;
				return true;
			}
			
			return false;
		}

		// courseid
		//	- get: courseid
		//	- set: courseid (validated by is_int())
		public function get_courseid()
		{
			return $this->courseid;
		}
		public function set_courseid($courseid)
		{
			// Validate using is_int()
			if (is_int($courseid))
			{
				$this->courseid = $courseid;
				return true;
			}
			
			return false;
		}

		// filename:
		//	- get: filename
		//	- set: filename
		public function get_filename()
		{
			return $this->filename;
		}
		public function set_roleid($filename)
		{
			$this->filename = $filename; 
			return true;
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
		
		// keywords:
		//	- get: keywords
		//	- set: keywords
		public function get_keywords()
		{
			return $this->keywords;
		}
		public function set_keywords($keywords)
		{
			$this->keywords = $keywords;
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

		// Store this video object in the database
		public function set_video()
		{
			// Check for existing video
			$result = database::query("SELECT id FROM videos WHERE id=?;", $this->id);

			// If video doesn't exist, insert this object
			if (count($result) === 0)
			{
				// Store video object by fields in database
				$success = database::query("INSERT INTO videos VALUES (null, ?, ?, ?, ?, ?);", $this->userid, $this->courseid, $this->filename, $this->title, $this->keywords);

				// Set video's ID from database return (last insert ID)
				$this->id = $success;
			}
			else
			{
				// Else, update this object
				$success = database::query("UPDATE videos SET userid=?, courseid=?, filename=?, title=?, keywords=? WHERE id=?;", $this->userid, $this->courseid, $this->filename, $this->title, $this->keywords, $this->id);
			}

			return $success;
		}

		// Remove this video object from the database
		public function delete_video()
		{
			// Remove video object by ID from database
			$success = database::query("DELETE FROM videos WHERE id=?;", $this->id);
			return $success;
		}

		// STATIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Generate and fill a new video object using pseudo-constructor
		public static function create_video($userid, $courseid, $filename, $title, $keywords)
		{
			$instance = new self();

			$instance->set_userid($userid);
			$instance->set_courseid($courseid);
			$instance->set_filename($filename);
			$instance->set_title($title);
			$instance->set_keywords($keywords);

			return $instance;
		}

		// Populate video object from fields in database
		public static function get_video($value, $field = "id")
		{
			// Sanitize video's field input, as that won't be part of the prepared query
			$field = database::sanitize($field);

			// Check for valid, unique field
			if (!in_array($field, array_keys(self::$FIELDS)) || !self::$FIELDS[$field])
			{
				// Return null on bad field
				trigger_error("video::get_video() cannot get using invalid field '" . $field . "'", E_USER_WARNING);
				return null;
			}
			
			$results = database::query("SELECT * FROM videos WHERE $field=?;", $value);

			if ($results)
			{
				// Generate video object populated with fields from database
				$video = new video();
				foreach($results[0] as $key => $val)
				{
					$video->{$key} = $val;
				}

				return $video;
			}
			else
			{
				// Return null if no result
				return null;
			}
		}

		// Create a list of video objects using specified parameters
		public static function fetch_videos($field = "id")
		{
			// Sanitize video's field input, as that won't be part of the prepared query
			$field = database::sanitize($field);

			// Check for valid, unique field
			if (!in_array($field, array_keys(self::$FIELDS)) || !self::$FIELDS[$field])
			{
				// Return null and trigger error on bad field
				trigger_error("video::fetch_videos() cannot fetch using invalid field '" . $field . "'", E_USER_WARNING);
				return null;
			}

			// Query for a list of videos using specified field
			$results = database::query("SELECT $field FROM videos ORDER BY $field ASC;");

			if ($results)
			{	
				// Generate list of video objects
				$videos = array();
				for ($i = 0; $i < count($results); $i++)
				{
					// Parse value from array
					$r = $results[$i][$field];
					$videos[] = self::get_video($r, $field);
				}

				return $videos;
			}
			else
			{
				// Return null if no results
				return null;
			}
		}
	}
