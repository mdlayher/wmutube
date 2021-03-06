<?php
	// class_database.php - Khan Academy Workflow, 2/5/13
	// PHP class which abstracts PDO functionality into simple, efficient, and re-usable methods

	error_reporting(E_ALL);

	require_once __DIR__ . "/class_config.php";
	config::load(array("cache"));

	class database
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -

		// Database configuration
		// Database connection parameters
		const DB_SERVER = "mysql";
		const DB_HOST = "localhost";
		const DATABASE = config::DB_NAME;
		const DB_USER = config::DB_USER;
		const DB_PASSWORD = config::DB_PASSWORD;
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

		// Cache control
		protected $cache = true;

		// DESTRUCTOR - - - - - - - - - - - - - - - - - - - - - -

		// Destructor handles any necessary cleanup
		function __destruct()
		{
			// Utilize singleton
			$singleton = self::singleton(false);

			// Close database connection if it isn't closed already
			if ($singleton->db)
			{
				self::pdo_close();
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

			return self::$instance;
		}

		// Generate a common key used for caching query results
		private static function memcache_key($query, $query_args)
		{
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

			return $key;
		}

		// Generate a PDO connection object which can be used to perform queries
		private static function pdo_open()
		{
			try
			{
				self::debug("pdo_open()");
				$conn = new PDO(sprintf("%s:host=%s;dbname=%s;", self::DB_SERVER, self::DB_HOST, self::DATABASE), self::DB_USER, self::DB_PASSWORD);
				
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
			// Utilize singleton, close connection
			self::debug("pdo_close()");
			$singleton = self::singleton(false);
			$singleton->db = null;

			return true;
		}

		// Capture the table from a query
		private static function get_table($query)
		{
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

			return $query_table;
		}

		// PUBLIC METHODS - - - - - - - - - - - - - - - - - - - -

		// Force cache enable/disable
		public static function do_cache($enable = true)
		{
			$singleton = self::singleton();
			$singleton->cache = $enable;
			return true;
		}

		// Sanitize data not using with prepared queries
		public static function sanitize($data)
		{
			// Utilize singleton, clean data (trim quotes like MySQL)
			$singleton = self::singleton();
			$sanitized = trim($singleton->db->quote($data), "'");

			return $sanitized;
		}

		// Perform a raw query
		public static function raw_query($query, $fetch = self::DB_FETCH)
		{
			$singleton = self::singleton();

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
			return $prepared_query->fetchAll($fetch);
		}

		// Perform a database query (fetching associative array by default)
		public static function query($query)
		{
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
						if (config::MEMCACHE && $results && $singleton->cache)
						{
							// Store query result
							cache::set(self::memcache_key($query, $query_args), $results);
						}
					}
				}
				else
				{
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
				}

				if (config::DEBUG)
				{
					self::debug("query results:");
					print_r($results);
				}

				// Return result set, or success of query
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
