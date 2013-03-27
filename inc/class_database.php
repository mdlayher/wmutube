<?php
	// class_database.php - Khan Academy Workflow, 2/5/13
	// PHP class which abstracts PDO functionality into simple, efficient, and re-usable methods
	//
	// changelog:
	//
	// 3/3/13 MDL:
	//	- split memcache into class_cache
	// 2/27/13 MDL:
	//	- renamed flush() to cache_flush()
	// 2/14/13 MDL:
	//	- added ability to flush memcache
	// 2/12/13 MDL:
	//	- removed deprecated $fetch singleton code
	// 2/7/13 MDL:
	//	- better argument and return handling for query()
	//	- trigger_error() when really bad things happen
	//	- get_table() strips non-alphanumeric characters (e.g. users; -> users)
	// 2/6/13 MDL:
	//	- added memcache for caching
	//	- query() makes use of prepared statements with ? placeholders
	//    - database::query("SELECT * FROM users WHERE id=?;", 1);
	// 2/5/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	require_once __DIR__ . "/class_config.php";
	config::load(array("cache", "profiler"));

	class database
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -

		// Database configuration
		// Database connection parameters
		const DB_SERVER = "mysql";
		const DB_HOST = "localhost";
		const DATABASE = "khan";
		// Justin, set this file to untracked -Matt
		const DB_USER = "khan";
		const DB_PASSWORD = "tJEbu66lTpNbY%1w,aRy1SmUl2PK4pIG";
		// Default result fetch type
		const DB_FETCH = PDO::FETCH_ASSOC;

		// Allowed and disallowed query types (to prevent serious damage)
		protected static $QUERIES = array(
			"SELECT" => true,
			"INSERT" => true,
			"UPDATE" => true,
			"DELETE" => true,
		);

		// INSTANCE VARIABLES - - - - - - - - - - - - - - - - -

		// Singleton instance of database class
		protected static $instance;

		// Instance of PDO database connection
		protected $db;

		// DESTRUCTOR - - - - - - - - - - - - - - - - - - - - - -

		// Destructor handles any necessary cleanup
		function __destruct()
		{
			if (config::PROFILER)
			{
				profiler::step_start();
			}

			// Utilize singleton
			$singleton = self::singleton(false);

			// Close database connection if it isn't closed already
			if ($singleton->db)
			{
				self::pdo_close();
			}

			if (config::PROFILER)
			{
				profiler::step_stop();
			}
		}

		// PRIVATE METHODS - - - - - - - - - - - - - - - - - - -

		// Debug function
		private static function debug($debug)
		{
			if (config::DEBUG)
			{
				printf("%s\n", $debug);
			}

			return true;
		}

		// Singleton function which maintains a single instance of this class
		private static function singleton($open_connections = true)
		{
			if (config::PROFILER)
			{
				profiler::step_start();
			}

			// If instance is null, generate one
			self::$instance || self::$instance = new self();

			// Open database and memcache connections if requested and they don't already exist
			if ($open_connections)
			{
				if (!self::$instance->db)
				{
					self::$instance->db = self::pdo_open();
				}
			}

			if (config::PROFILER)
			{
				profiler::step_stop();
			}

			return self::$instance;
		}

		// Generate a common key used for caching query results
		private static function memcache_key($query, $query_args)
		{
			if (config::PROFILER)
			{
				profiler::step_start();
			}

			// Check for memcache
			if (!config::MEMCACHE)
			{
				trigger_error("database::memcache_key() memcache disabled in configuration", E_USER_WARNING);
				return null;
			}

			// Capture query table
			$query_table = self::get_table($query);

			// Build key as: query_table_version_hash
			$key = sprintf("query_%s_%s_%s", $query_table, cache::version($query_table), md5($query . serialize($query_args)));
			self::debug("key: " . $key);

			if (config::PROFILER)
			{
				profiler::step_stop();
			}

			return $key;
		}

		// Generate a PDO connection object which can be used to perform queries
		private static function pdo_open()
		{
			try
			{
				if (config::PROFILER)
				{
					profiler::step_start();
				}

				self::debug("pdo_open()");
				$conn = new PDO(sprintf("%s:host=%s;dbname=%s;", self::DB_SERVER, self::DB_HOST, self::DATABASE), self::DB_USER, self::DB_PASSWORD);
				
				if (config::PROFILER)
				{
					profiler::step_stop();
				}

				return $conn;
			}
			catch (PDOException $e)
			{
				self::debug("pdo_open() EXCEPTION");
				// Catch exception and throw error
				self::debug($e->getMessage());
				trigger_error("database::pdo_open() could not open database connection", E_USER_WARNING);
				return null;
			}
		}

		// Perform cleanup and destroy PDO connection object
		private static function pdo_close()
		{
			if (config::PROFILER)
			{
				profiler::step_start();
			}

			// Utilize singleton, close connection
			self::debug("pdo_close()");
			$singleton = self::singleton(false);
			$singleton->db = null;

			if (config::PROFILER)
			{
				profiler::step_stop();
			}

			return true;
		}

		// Capture the table from a query
		private static function get_table($query)
		{
			if (config::PROFILER)
			{
				profiler::step_start();
			}

			// Capture query type
			$query_array = explode(" ", $query);
			$query_type = $query_array[0];

			// Determine query table
			if ($query_type === "UPDATE")
			{
				$query_table = $query_array[1];
			}
			else if ($query_type === "INSERT" || $query_type === "DELETE")
			{
				$query_table = $query_array[2];
			}
			else if ($query_type === "SELECT")
			{
				// Shift array until FROM encountered, capture table name
				while (array_shift($query_array) !== "FROM");
				$query_table = $query_array[0];
			}
			else
			{
				// Should never happen due to query trap, but just in case
				$query_table = null;
			}

			// Strip any non alphanumeric characters from table
			$query_table = preg_replace("/[^A-Za-z0-9 ]/", '', $query_table);

			if (config::PROFILER)
			{
				profiler::step_stop();
			}

			return $query_table;
		}

		// PUBLIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Sanitize data not using with prepared queries
		public static function sanitize($data)
		{
			if (config::PROFILER)
			{
				profiler::step_start();
			}

			// Utilize singleton, clean data (trim quotes like MySQL)
			$singleton = self::singleton();
			$sanitized = trim($singleton->db->quote($data), "'");

			if (config::PROFILER)
			{
				profiler::step_stop();
			}
			
			return $sanitized;
		}

		// Perform a database query (fetching associative array by default)
		public static function query($query)
		{
			if (config::PROFILER)
			{
				profiler::step_start();
			}

			// Utilize singleton
			$singleton = self::singleton();

			try
			{
				self::debug("query: " . $query);

				// Check to ensure query type is valid and in array
				$query_array = explode(" ", $query);
				$query_type = $query_array[0];
				if (!array_key_exists($query_type, self::$QUERIES) || !self::$QUERIES[$query_type])
				{
					self::debug("trapped bad query: " . $query_type);
					trigger_error("database::query() trapped bad query type '" . $query_type . "'", E_USER_WARNING);
					return null;
				}

				// Capture query arguments for prepared statements and cache keying
				if (func_num_args() > 1)
				{
					$query_args = func_get_args();
					array_shift($query_args);
				}
				else
				{
					$query_args = null;
				}

				// If using memcache, capture query table and begin versioning
				if (config::MEMCACHE)
				{
					cache::version(self::get_table($query));
				}

				// Perform SELECT query and fetch results for this type
				if ($query_type === "SELECT")
				{
					if (config::PROFILER)
					{
						profiler::step_start("SELECT");
					}

					// Check if memcache is enabled and ready
					$result_cached = false;
					if (config::MEMCACHE)
					{
						self::debug("checking memcache");
						// Check if this query's results are cached
						$results = cache::get(self::memcache_key($query, $query_args));

						// If results exist, no need to query database
						if ($results)
						{
							self::debug("memcache: got result!");
							$result_cached = true;
						}
					}
					
					// If memcache disabled or result was not found in cache
					if (!$result_cached)
					{
						if (config::MEMCACHE)
						{
							self::debug("memcache: result not found!");
						}
						// Prepare query
						$prepared_query = $singleton->db->prepare($query);

						// Execute query using prepared arguments
						if (!empty($query_args))
						{
							$prepared_query->execute($query_args);
						}
						else
						{
							$prepared_query->execute();
						}

						// Fetch results
						$results = $prepared_query->fetchAll(self::DB_FETCH);

						// Store query result in memcache if applicable
						if (config::MEMCACHE && $results)
						{
							// Store query result
							cache::set(self::memcache_key($query, $query_args), $results);
						}
					}

					if (config::PROFILER)
					{
						profiler::step_stop();
					}
				}
				else
				{
					if (config::PROFILER)
					{
						profiler::step_start("INSERT/UPDATE/DELETE");
					}

					// Else, perform INSERT/UPDATE/DELETE query via transaction
					// Begin transaction
					$singleton->db->beginTransaction();

					// Prepare query
					$prepared_query = $singleton->db->prepare($query);
					
					// Execute query using prepared arguments
					$results = $prepared_query->execute($query_args);

					// Return the last insert ID on INSERT (useful for objects)
					if ($query_type === "INSERT")
					{
						$results = $singleton->db->lastInsertId("id");
					}

					// Determine success from result
					$success = false;
					if ($results > 0)
					{
						$success = true;
					}

					// Complete transaction on success, rollback on failure
					if ($success)
					{
						self::debug("commit!");
						$singleton->db->commit();

						// Invalidate caches on this table
						if (config::MEMCACHE)
						{
							cache::invalidate(self::get_table($query));
						}
					}
					else
					{
						self::debug("rollback!");
						$singleton->db->rollBack();
					}

					if (config::PROFILER)
					{
						profiler::step_stop();
					}
				}

				if (config::DEBUG)
				{
					self::debug("query results:");
					print_r($results);
				}

				// Return result set, or success of query
				if (config::PROFILER)
				{
					profiler::step_stop();
				}
				return $results;
			}
			catch (PDOException $e)
			{
				self::debug("query() EXCEPTION");
				// Rollback on failure, print exception, return failure
				$singleton->db->rollBack();
				self::debug($e->getMessage());
				trigger_error("database::query() exception occurred while performing query", E_USER_WARNING);
				return null;
			}
		}
	}
