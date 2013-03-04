<?php
	// class_config.php - Khan Academy Workflow, 2/19/13
	// PHP class which handles all configuration for the application
	//
	// changelog:
	//
	// 2/27/13 MDL:
	//	- added ability to load array of source files
	// 2/26/13 MDL:
	//	- added question and answer classes
	// 2/19/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	require_once __DIR__ . "/login/password.php";

	class config
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -

		// Debug mode
		const DEBUG = 0;

		// Memcache toggle
		const MEMCACHE = 1;

		// Profiler toggle
		const PROFILER = 0;

		// Password hash algorithm
		const HASH_ALGORITHM = PASSWORD_DEFAULT;

		// Password hash cost
		const HASH_COST = 14;

		// Array of source files to quickly load
		protected static $SOURCE_FILES = array(
			"answer" => "/model/class_answer.php",
			"cache" => "/class_cache.php",
			"course" => "/model/class_course.php",
			"database" => "/class_database.php",
			"login" => "/login/interface_login.php",
			"login_db" => "/login/class_login_db.php",
			"login_imap" => "/login/class_login_imap.php",
			"login_ldap" => "/login/class_login_ldap.php",
			"login_ssh" => "/login/class_login_ssh.php",
			"password" => "/login/password.php",
			"profiler" => "/etc/class_profiler.php",
			"question" => "/model/class_question.php",
			"user" => "/model/class_user.php",
			"video" => "/model/class_video.php",
		);

		// STATIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Load one or more source files such as classes or interfaces
		public static function load($files)
		{
			// Ensure files are an array
			if (!is_array($files))
			{
				$files = array($files);
			}

			// Check for existence of source files
			foreach ($files as $f)
			{
				if (in_array($f, array_keys(self::$SOURCE_FILES)))
				{
					// If exists, require it!
					require_once __DIR__ . self::$SOURCE_FILES[$f];
				}
				else
				{
					// Trigger error on non-existant file
					trigger_error("config::load() could not load invalid source file '" . $f . "'", E_USER_WARNING);
					return false;
				}
			}

			return true;
		}
	}
