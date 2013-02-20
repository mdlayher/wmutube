<?php
	// class_config.php - Khan Academy Workflow, 2/19/13
	// PHP class which handles all configuration for the application
	//
	// changelog:
	//
	// 2/19/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	class config
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -

		// Debug mode
		const DEBUG = 0;

		// Memcache toggle
		const MEMCACHE = 1;

		// Profiler toggle
		const PROFILER = 0;

		// Array of source files to quickly load
		protected static $SOURCE_FILES = array(
			"course" => "/model/class_course.php",
			"database" => "/class_database.php",
			"db_login" => "/login/class_db_login.php",
			"login" => "/login/interface_login.php",
			"profiler" => "/etc/class_profiler.php",
			"user" => "/model/class_user.php",
			"video" => "/model/class_video.php"
		);

		// STATIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Load a source file such as a class or interface
		public static function load($file)
		{
			// Check for existence of source file
			if (in_array($file, array_keys(self::$SOURCE_FILES)))
			{
				// If exists, require it!
				require_once __DIR__ . self::$SOURCE_FILES[$file];
				return true;
			}

			return false;
		}
	}
?>
