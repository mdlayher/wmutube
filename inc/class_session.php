<?php
	// class_session_compat.php - Khan Academy Workflow, 3/21/13
	// PHP class which handles sessions using cache + database
	// Note: this class for compatibility with PHP5.3
	//
	// changelog:
	//
	// 3/28/13 MDL:
	//	- encrypt session data using crypto class
	// 3/23/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	require_once __DIR__ . "/class_config.php";
	config::load(array("cache", "database", "crypto"));

	class session
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - -

		// Key to identify sessions in memcache
		const SESSION_KEY = "wmutube_session_";

		// INSTANCE VARIABLES - - - - - - - - - - - - - -

		// Session ID
		private $sessionid;

		// PRIVATE METHODS - - - - - - - - - - - - - - - - 

		// Generate memcache key with versioning
		private static function memcache_key($session_id)
		{
			return sprintf("%s_%s_%s", self::SESSION_KEY, cache::version(self::SESSION_KEY . $session_id), $session_id);
		}

		// PUBLIC METHODS - - - - - - - - - - - - - - - - -

		// Close session
		public function close()
		{
			return true;
		}

		// Destroy this session
		public function destroy($session_id)
		{
			if (config::MEMCACHE)
			{
				cache::invalidate(self::SESSION_KEY . $session_id);
			}
			return database::query("DELETE FROM session WHERE sessionid=?;", $session_id);
		}

		// Destroy old sessions
		public function gc($maxlifetime)
		{
			if (config::MEMCACHE)
			{
				cache::invalidate(self::SESSION_KEY . $session_id);
			}
			return database::query("DELETE FROM session WHERE updated < ?;", time() - $maxlifetime);
		}

		// Open session
		public function open($save_path, $name)
		{
			$this->sessionid = session_id();

			return true;
		}

		// Read from session
		public function read($session_id)
		{
			// Attempt to read data from memcache
			$session = null;
			if (config::MEMCACHE)
			{
				$session = cache::get(self::SESSION_KEY . '_' . $session_id);
			}

			// If data not found in cache, query database, set in cache
			if (!$session)
			{
				$session = database::query("SELECT data FROM session WHERE sessionid=?;", $session_id);
				if (config::MEMCACHE)
				{
					cache::set(self::memcache_key($session_id), $session);
				}
			}

			// Return session data, decrypted
			return empty($session[0]["data"]) ? null : crypto::decrypt($session[0]["data"]);
		}

		// Write to session
		public function write($session_id, $session_data)
		{
			// Check for previous session in database
			$res = database::query("SELECT sessionid,data FROM session WHERE sessionid=?;", $session_id);

			// If none, INSERT
			if (!$res)
			{
				// Encrypt session data on storage
				$session_data = crypto::encrypt($session_data);

				$ret = database::query("INSERT INTO session VALUES (null, ?, ?, ?, ?);", $session_id, time(), time(), $session_data);
				if (config::MEMCACHE)
				{
					cache::invalidate(self::SESSION_KEY . $session_id);
				}
			}
			// Else, UPDATE
			else
			{
				// Pull old data
				$old_data = $res[0]["data"];

				// If no changes, don't update!
				if ($old_data == $session_data)
				{
					return 1;
				}

				// Encrypt session data on storage
				$session_data = crypto::encrypt($session_data);
				
				$ret = database::query("UPDATE session SET sessionid=?, updated=?, data=? WHERE sessionid=?;", $session_id, time(), $session_data, $this->sessionid);
				if (config::MEMCACHE)
				{
					cache::invalidate(self::SESSION_KEY . $session_id);
				}
			}

			return $ret;
		}
	}
