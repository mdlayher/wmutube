<?php
	// class_answer.php - Khan Academy Workflow, 2/26/13
	// PHP class which contains storage and manipulation for answer objects
	//
	// changelog:
	//
	// 3/6/13 MDL:
	//	- typo fix
	// 2/26/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	require_once __DIR__ . "/../class_config.php";
	config::load("database");

	class answer
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -
		
		// INSTANCE VARIABLES - - - - - - - - - - - - - - - - - -

		private $id;
		private $questionid;
		private $text;
		private $hint;
		private $correct;

		// PUBLIC PROPERTIES - - - - - - - - - - - - - - - - - - 

		// id:
		//	- get: id
		//	- set: n/a, changing an ID would be a bad idea
		public function get_id()
		{
			return $this->id;
		}

		// questionid:
		//	- get: questionid
		//	- set: questionid (validated by is_int())
		public function get_questionid()
		{
			return $this->questionid;
		}
		public function set_questionid($questionid)
		{
			// Validate using is_int()
			if (is_int($questionid))
			{
				$this->questionid = $questionid;
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

		// correct:
		//	- get: correct
		//	- set: correct (validated by is_bool())
		public function get_correct()
		{
			return $this->correct;
		}
		public function set_correct($correct)
		{
			// Convert integer input into bool
			if (is_int($correct))
			{
				$correct = $correct === 1 ? true : false;
			}

			// Validate using is_bool()
			if (is_bool($correct))
			{
				$this->correct = $correct;
				return true;
			}
			
			return false;
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

		// Store this answer object in the database
		public function set_answer()
		{
			// Check for existing answer
			$result = database::query("SELECT id FROM answers WHERE id=?;", $this->id);

			// If answer doesn't exist, insert this object
			if (count($result) === 0)
			{
				// Store answer object by fields in database
				$success = database::query("INSERT INTO answers VALUES (null, ?, ?, ?, ?);", $this->questionid, $this->text, $this->hint, $this->correct);

				// Check for success
				if ($success)
				{	
					// Set answer's ID from database return (last insert ID)
					$this->id = $success;
				}
				else
				{
					// On failure, trigger warning	
					trigger_error("answer->set_answer() database insert failed with code: '" . $success . "'", E_USER_WARNING);
				}

			}
			else
			{
				// Else, update this object
				$success = database::query("UPDATE answers SET questionid=?, text=?, hint=?, correct=? WHERE id=?;", $this->questionid, $this->text, $this->hint, $this->correct, $this->id);

				// Check for failure
				if (!$success)
				{
					// On failure, trigger warning	
					trigger_error("answer->set_answer() database update failed with code: '" . $success . "'", E_USER_WARNING);
				}
			}

			return $success;
		}

		// Remove this answer object from the database
		public function delete_answer()
		{
			// Remove answer object by ID from database
			$success = database::query("DELETE FROM answers WHERE id=?;", $this->id);

			// Check for failure
			if (!$success)
			{
				// On failure, trigger warning
				trigger_error("answer->delete_answer() database delete failed with code: '" . $success . "'", E_USER_WARNING);
			}

			return $success;
		}

		// STATIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Generate and fill a new answer object using pseudo-constructor
		public static function create_answer($questionid, $text, $hint, $correct)
		{
			$instance = new self();

			$instance->set_questionid($questionid);
			$instance->set_text($text);
			$instance->set_hint($hint);
			$instance->set_correct($correct);

			return $instance;
		}

		// Populate answer object from ID in database
		public static function get_answer($value)
		{
			// Query for result
			$results = database::query("SELECT * FROM answers WHERE id=?;", $value);

			if ($results)
			{
				// Generate answer object populated with fields from database
				$answer = new self();
				foreach($results[0] as $key => $val)
				{
					$answer->{$key} = $val;
				}

				return $answer;
			}
			else
			{
				// Return null if no result
				return null;
			}
		}

		// Create a list of answer objects from question ID
		public static function fetch_answers($questionid)
		{
			// Query for a list of shuffled answers using specified field
			$results = database::query("SELECT id FROM answers WHERE questionid=? ORDER BY RAND();", $questionid);

			if ($results)
			{	
				// Generate list of answer objects
				$answers = array();
				for ($i = 0; $i < count($results); $i++)
				{
					// Parse value from array
					$r = $results[$i]["id"];
					$answers[] = self::get_answer($r);
				}

				return $answers;
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
			// Test create_answer()
			$answer = self::create_answer(1, "test text", "test hint", false);
			if (!$answer)
			{
				trigger_error("answer::selftest(): answer::create_answer() failed with status: '" . $answer . "'", E_USER_WARNING);
				return false;
			}

			// Test set_answer()
			$success = $answer->set_answer();
			if (!$success)
			{
				trigger_error("answer::selftest() answer->set_answer() insert failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Test set_questionid()
			$success = $answer->set_questionid(2);
			if (!$success)
			{
				trigger_error("answer::selftest() answer->set_questionid() failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Test set_answer()
			$success = $answer->set_answer();
			if (!$success)
			{
				trigger_error("answer::selftest() answer->set_answer() update failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Re-fetch answer
			$id = $answer->get_id();
			unset($answer);

			// Test get_answer()
			$answer = answer::get_answer($id);
			if (!$answer)
			{
				trigger_error("answer::selftest(): answer::get_answer() failed with status: '" . $answer . "'", E_USER_WARNING);
				return false;
			}

			// Test delete_answer()
			$success = $answer->delete_answer();
			if (!$success)
			{
				trigger_error("answer::selftest() answer->delete_answer() update failed with status: '" . $success . "'", E_USER_WARNING);
				return false;
			}

			// Test fetch_answers()
			$answers = answer::fetch_answers(1);
			if (!$answers)
			{
				trigger_error("answer::selftest(): answer::fetch_answers() failed with status: '" . $answers . "'", E_USER_WARNING);
				return false;
			}

			// If all tests pass, return true
			printf("answer::selftest(): all tests passed\n");
			return true;
		}
	}
