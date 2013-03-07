<?php
	// class_session.php - Khan Academy Workflow, 3/6/13
	// PHP class which abstracts session functionality into simple, efficient, and re-usable methods
	//
	// changelog:
	//
	// 3/6/13 MDL:
	//	- initial skeleton

	error_reporting(E_ALL);

	require_once __DIR__ . "/class_config.php";
	config::load(array("cache", "database"));

	// Implement SessionHandlerInterface for easy use with session_*()
	class session implements SessionHandlerInterface
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - -

		// Session lifetime in seconds
		const SESSION_EXPIRE = 3600;

		// Session key identifier prepended to cache key
		const SESSION_KEY = "session_";

		// PUBLIC METHODS - - - - - - - - - - - - - - - - -

		// close() - called automatically when closing the session
		public function close()
		{
			return true;
		}

		// destroy($session_id) - called to destroy a session
		public function destroy($session_id)
		{
			return true;
		}

		// gc($maxlifetime) - cleans up sessions which have not been changed in $maxlifetime seconds
		public function gc($maxlifetime = self::SESSION_EXPIRE)
		{
			return true;
		}

		// open($save_path, $name) - re-initialize or create a session at given path with this name
		public function open($save_path, $name)
		{

		}

		// read($session_id) - read session data from storage
		public function read($session_id)
		{

		}

		// write($session_id, $session_data) - write session data to storage
		public function write($session_id, $session_data)
		{

		}
	}
