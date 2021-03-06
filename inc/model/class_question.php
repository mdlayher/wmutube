<?php
	// class_question.php - Khan Academy Workflow, 2/26/13
	// PHP class which contains storage and manipulation for question objects

	error_reporting(E_ALL);

	require_once __DIR__ . "/../class_config.php";
	config::load(array("database", "answer"));

	class question
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -
		
		// INSTANCE VARIABLES - - - - - - - - - - - - - - - - - -

		// Database columns
		private $id;
		private $videoid;
		private $timestamp;
		private $text;
		private $hint;

		// Helper objects
		private $answers;

		// PUBLIC PROPERTIES - - - - - - - - - - - - - - - - - - 

		// id:
		//	- get: id
		//	- set: n/a, changing an ID would be a bad idea
		public function get_id()
		{
			return $this->id;
		}

		// videoid:
		//	- get: videoid
		//	- set: videoid (validated by is_int())
		public function get_videoid()
		{
			return $this->videoid;
		}
		public function set_videoid($videoid)
		{
			// Validate using is_int()
			if (is_int($videoid))
			{
				$this->videoid = $videoid;
				return true;
			}
			
			return false;
		}

		// timestamp:
		//	- get: timestamp
		//	- set: timestamp (validated by is_int())
		public function get_timestamp()
		{
			return $this->timestamp;
		}
		public function set_timestamp($timestamp)
		{
			// Validate using is_int()
			if (is_int($timestamp))
			{
				$this->timestamp = $timestamp;
				return true;
			}
			
			return false;
		}

		// text:
		//	- get: text
		//	- set: text
		public function get_text()
		{
			return $this->text;
		}
		public function set_text($text)
		{
			$this->text = $text;
			return true;
		}

		// hint:
		//	- get: hint
		//	- set: hint
		public function get_hint()
		{
			return $this->hint;
		}
		public function set_hint($hint)
		{
			$this->hint = $hint;
			return true;
		}

		// answers:
		//	- get: answers (lazy-load, only fetch when needed)
		//	- set: n/a, not handled by this class
		public function get_answers()
		{
			// Check if answers already fetched
			if (!isset($this->answers))
			{
				// Get answers associated with this question
				$this->answers = answer::fetch_answers($this->id);
			}

			return $this->answers;
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

		// Store this question object in the database
		public function set_question()
		{
			// Check for existing question
			$result = database::query("SELECT id FROM questions WHERE id=?;", $this->id);

			// If question doesn't exist, insert this object
			if (count($result) === 0)
			{
				// Store question object by fields in database
				$success = database::query("INSERT INTO questions VALUES (null, ?, ?, ?, ?);", $this->videoid, $this->timestamp, $this->text, $this->hint);

				// Check for success
				if ($success)
				{	
					// Set question's ID from database return (last insert ID)
					$this->id = $success;
				}
				else
				{
					// On failure, trigger warning	
					trigger_error("question->set_question() database insert failed with code: '" . $success . "'", E_USER_WARNING);
				}

			}
			else
			{
				// Else, update this object
				$success = database::query("UPDATE questions SET videoid=?, timestamp=?, text=?, hint=? WHERE id=?;", $this->videoid, $this->timestamp, $this->text, $this->hint, $this->id);

				// Check for failure
				if (!$success)
				{
					// On failure, trigger warning	
					trigger_error("question->set_question() database update failed with code: '" . $success . "'", E_USER_WARNING);
				}
			}

			return $success;
		}

		// Remove this question object from the database
		public function delete_question()
		{
			// Remove question object by ID from database
			$success = database::query("DELETE FROM questions WHERE id=?;", $this->id);

			// Check for failure
			if (!$success)
			{
				// On failure, trigger warning
				trigger_error("question->delete_question() database delete failed with code: '" . $success . "'", E_USER_WARNING);
			}

			return $success;
		}

		// Flatten relevant information fields of question from object to array
		public function to_array($user_answers = false, $correct = false)
		{
			// Gather basic data
			$data = array(
				"id" => $this->id,
				"timestamp" => $this->timestamp,
				"text" => $this->text,
			);

			// Fetch answers associated with this question
			$answers = array();
			foreach (answer::fetch_answers($this->id) as $a)
			{
				$answer_array = $a->to_array();

				// Add correct if requested
				if ($correct)
				{
					$answer_array["correct"] = $a->get_correct();
				}

				$answers[] = $answer_array;
			}
			$data["answers"] = $answers;

			// If requested, add user answers
			if ($user_answers)
			{
				$results = database::query("SELECT * FROM useranswers WHERE questionid=?;", $this->id);

				$ua = array();
				foreach ($results as $a)
				{
					// Grab user object and include
					$user = user::get_user($a["userid"]);
					$a["user"] = $user->to_array();

					$ua[] = $a;
				}
				$data["user_answers"] = $ua;
			}

			return $data;
		}

		// Export question to_array() data as JSON
		public function to_json($user_answers = false)
		{
			return json_encode($this->to_array($user_answers));
		}

		// STATIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Generate and fill a new question object using pseudo-constructor
		public static function create_question($videoid, $timestamp, $text, $hint)
		{
			$instance = new self();

			$instance->set_videoid($videoid);
			$instance->set_timestamp($timestamp);
			$instance->set_text($text);
			$instance->set_hint($hint);

			return $instance;
		}

		// Populate question object from ID in database
		public static function get_question($value)
		{
			// Query for result
			$results = database::query("SELECT * FROM questions WHERE id=?;", $value);

			if ($results)
			{
				// Generate question object populated with fields from database
				$question = new self();
				foreach ($results[0] as $key => $val)
				{
					$question->{$key} = $val;
				}

				return $question;
			}
			else
			{
				// Return null if no result
				return null;
			}
		}

		// Create a list of question objects from video ID
		public static function fetch_questions($videoid)
		{
			// Query for a list of questions using specified field
			$results = database::query("SELECT id FROM questions WHERE videoid=? ORDER BY id ASC;", $videoid);

			if ($results)
			{
				// Generate list of question objects
				$questions = array();
				for ($i = 0; $i < count($results); $i++)
				{
					// Parse value from array
					$r = $results[$i]["id"];
					$questions[] = self::get_question($r);
				}

				return $questions;
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
			// Test create_question()
			$question = self::create_question(1, 1, "test", "hint");
			if (!$question)
			{
				trigger_error("question::selftest(): question::create_question() failed with status: '" . $question . "'", E_USER_WARNING);
				return false;
			}

			// Test set_question()
			$success = $question->set_question();
			if (!$success)
			{
				trigger_error("question::selftest() question->set_question() insert failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Test set_videoid()
			$success = $question->set_videoid(2);
			if (!$success)
			{
				trigger_error("question::selftest() question->set_videoid() failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Test set_question()
			$success = $question->set_question();
			if (!$success)
			{
				trigger_error("question::selftest() question->set_question() update failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Re-fetch question
			$id = $question->get_id();
			unset($question);

			// Test get_question()
			$question = question::get_question($id);
			if (!$question)
			{
				trigger_error("question::selftest(): question::get_question() failed with status: '" . $question . "'", E_USER_WARNING);
				return false;
			}

			// Test delete_question()
			$success = $question->delete_question();
			if (!$success)
			{
				trigger_error("question::selftest() question->delete_question() update failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Test fetch_questions()
			$questions = question::fetch_questions(1);
			if (!$questions)
			{
				trigger_error("question::selftest(): question::fetch_questions() failed with status: '" . $questions . "'", E_USER_WARNING);
				return false;
			}

			// If all tests pass, return true
			printf("question::selftest(): all tests passed\n");
			return true;
		}
	}
