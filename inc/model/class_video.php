<?php
	// class_video.php - Khan Academy Workflow, 2/11/13
	// PHP class which contains storage and manipulation for video objects
	//
	// changelog:
	//
	// 3/7/13 MDL:
	//	- query optimizations
	// 2/27/13 MDL:
	//	- added filter_videos(), enabled fetch_videos() to accept values array for fine-grained output
	//	- added associated course and user object fetching for videos
	// 2/26/13 MDL:
	//	- added question fetching ability
	// 2/11/13 MDL:
	//	- ported database sanity checks from class_user
	//	- added selftest() for checking all class functionality
	//	- minor tweaks and testing
	// 2/11/13 EJL:
	//	- initial code - copied from class_user and tweaked

	error_reporting(E_ALL);

	require_once __DIR__ . "/../class_config.php";
	config::load(array("database", "course", "question", "user"));

	class video
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -
		
		// Allowed and disallowed fields for query
		protected static $FIELDS = array(
			"id" => true,
			"userid" => true,
			"courseid" => true,
		);

		// Allowed and disallowed fields for filter
		protected static $FILTERS = array(
			"id" => true,
			"userid" => true,
			"courseid" => true,
			"filename" => true,
			"title" => true,
			"keywords" => true,
		);

		// INSTANCE VARIABLES - - - - - - - - - - - - - - - - - -

		// Database columns
		private $id;
		private $userid;
		private $courseid;
		private $filename;
		private $title;
		private $keywords;

		// Helper objects
		private $user;
		private $course;
		private $questions;

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
		public function set_filename($filename)
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

		// user:
		//	- get: user (lazy-load, only fetch when needed)
		//	- set: n/a, not handled by this class
		public function get_user()
		{
			// Check if user already fetched
			if (!isset($this->user))
			{
				// Get user associated with this video
				$this->user = user::get_user($this->userid);
			}

			return $this->user;
		}

		// course:
		//	- get: course (lazy-load, only fetch when needed)
		//	- set: n/a, not handled by this class
		public function get_course()
		{
			// Check if course already fetched
			if (!isset($this->course))
			{
				// Get course associated with this video
				$this->course = course::get_course($this->courseid);
			}

			return $this->course;
		}

		// questions:
		//	- get: questions (lazy-load, only fetch when needed)
		//	- set: n/a, not handled by this class
		public function get_questions()
		{
			// Check if questions already fetched
			if (!isset($this->questions))
			{
				// Get questions associated with this video
				$this->questions = question::fetch_questions($this->id);
			}

			return $this->questions;
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

				// Check for success
				if ($success)
				{	
					// Set video's ID from database return (last insert ID)
					$this->id = $success;
				}
				else
				{
					// On failure, trigger warning	
					trigger_error("video->set_video() database insert failed with code: '" . $success . "'", E_USER_WARNING);
				}

			}
			else
			{
				// Else, update this object
				$success = database::query("UPDATE videos SET userid=?, courseid=?, filename=?, title=?, keywords=? WHERE id=?;", $this->userid, $this->courseid, $this->filename, $this->title, $this->keywords, $this->id);

				// Check for failure
				if (!$success)
				{
					// On failure, trigger warning	
					trigger_error("video->set_video() database update failed with code: '" . $success . "'", E_USER_WARNING);
				}
			}

			return $success;
		}

		// Remove this video object from the database
		public function delete_video()
		{
			// Remove video object by ID from database
			$success = database::query("DELETE FROM videos WHERE id=?;", $this->id);

			// Check for failure
			if (!$success)
			{
				// On failure, trigger warning
				trigger_error("video->delete_video() database delete failed with code: '" . $success . "'", E_USER_WARNING);
			}

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
			if (!array_key_exists($field, self::$FIELDS) || !self::$FIELDS[$field])
			{
				// Return null on bad field
				trigger_error("video::get_video() cannot get using invalid field '" . $field . "'", E_USER_WARNING);
				return null;
			}
			
			$results = database::query("SELECT * FROM videos WHERE $field=?;", $value);

			if ($results)
			{
				// Generate video object populated with fields from database
				$video = new self();
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
		public static function fetch_videos($field = "id", $values = null)
		{
			// Sanitize video's field input, as that won't be part of the prepared query
			$field = database::sanitize($field);

			// Check for valid, unique field
			if (!array_key_exists($field, self::$FIELDS) || !self::$FIELDS[$field])
			{
				// Return null and trigger error on bad field
				trigger_error("video::fetch_videos() cannot fetch using invalid field '" . $field . "'", E_USER_WARNING);
				return null;
			}

			// Check for specified values to fetch into list
			if (isset($values) && is_array($values))
			{
				// Sanitize all values to fetch into list
				$values = array_map(function($v)
				{
					return database::sanitize($v);
				}, $values);
				$query = implode(", ", $values);

				// Query for a list of videos matching values in array
				$results = database::query("SELECT $field FROM videos WHERE $field IN ($query) ORDER BY $field ASC;");
			}
			else
			{
				// Query for a list of videos using specified field
				$results = database::query("SELECT $field FROM videos ORDER BY $field ASC;");
			}

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

		// Create a list of video objects using specified filter
		public static function filter_videos($filter, $value)
		{
			// Sanitize video filter input
			$filter = database::sanitize($filter);

			// Check for valid filter field
			if (!array_key_exists($filter, self::$FILTERS) || !self::$FILTERS[$filter])
			{
				// Return null and trigger error on bad filter
				trigger_error("video::filter_videos() cannot filter using invalid field '" . $filter . "'", E_USER_WARNING);
				return null;
			}

			// Query for a filtered list of s matching the wildcard value for specified field
			$results = database::query("SELECT id FROM videos WHERE $filter LIKE ? ORDER BY id ASC;", '%' . $value . '%');

			if ($results)
			{	
				// Generate list of video IDs
				$id = array();
				for ($i = 0; $i < count($results); $i++)
				{
					// Parse value from array
					$id[] = $results[$i]["id"];
				}

				// Hand off array to fetch_videos() to generate list of video objects
				return self::fetch_videos("id", $id);
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
			// Test create_video()
			$video = self::create_video(1, 1, "testvideo.mp4", "Test Video", "test video stuff");
			if (!$video)
			{
				trigger_error("video::selftest(): video::create_video() failed with status: '" . $video . "'", E_USER_WARNING);
				return false;
			}

			// Test set_video()
			$success = $video->set_video();
			if (!$success)
			{
				trigger_error("video::selftest() video->set_video() insert failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Test set_userid()
			$success = $video->set_userid(2);
			if (!$success)
			{
				trigger_error("video::selftest() video->set_userid() failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Test set_courseid()
			$success = $video->set_courseid(2);
			if (!$success)
			{
				trigger_error("video::selftest() video->set_courseid() failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Test set_video()
			$success = $video->set_video();
			if (!$success)
			{
				trigger_error("video::selftest() video->set_video() update failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Re-fetch video
			$id = $video->get_id();
			unset($video);

			// Test get_video()
			$video = video::get_video($id);
			if (!$video)
			{
				trigger_error("video::selftest(): video::get_video() failed with status: '" . $video . "'", E_USER_WARNING);
				return false;
			}

			// Test delete_video()
			$success = $video->delete_video();
			if (!$success)
			{
				trigger_error("video::selftest() video->delete_video() update failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Test fetch_videos()
			$videos = video::fetch_videos();
			if (!$videos)
			{
				trigger_error("video::selftest(): video::fetch_videos() failed with status: '" . $videos . "'", E_USER_WARNING);
				return false;
			}
			
			// Test filter_videos()
			$videos = video::filter_videos("title", "a");
			if (!$videos)
			{
				trigger_error("video::selftest(): video::filter_videos() failed with status: '" . $videos . "'", E_USER_WARNING);
				return false;
			}

			// If all tests pass, return true
			printf("video::selftest(): all tests passed\n");
			return true;
		}
	}
