<?php
	// class_cache.php - Khan Academy Workflow, 3/3/13
	// PHP class which abstracts cache functionality into simple, efficient, and re-usable methods
	//
	// changelog:
	//
	// 3/3/13 MDL:
	//	- initial code

	error_reporting(E_ALL);

	require_once __DIR__ . "/class_config.php";

	class cache
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - -

		// Memcache configuration
		// Connection parameters
		const CACHE_HOST = "localhost";
		// Cache set parameters
		const CACHE_FLAG = MEMCACHE_COMPRESSED;
		const CACHE_EXPIRE = 600;
		// Array key versioning
		const VERSION_KEY = "version_";

		// INSTANCE VARIABLES - - - - - - - - - - - - - -

		// Singleton instance of cache class
		protected static $instance;

		// Instance of memcache connection
		protected $cache;

		// Instance of cache versioning array
		protected $version = array();

		// DESTRUCTOR - - - - - - - - - - - - - - - - - -

		// Destructor handles cleanup
		function __destruct()
		{
			$singleton = self::singleton(false);

			// Close memcache conenction if it isn't closed
			if ($singleton->cache)
			{
				self::memcache_close();
			}
		}

		// PRIVATE METHODS - - - - - - - - - - - - - - - - 

		// Singleton function which maintains a single instance of this class
		private static function singleton($open_connections = true)
		{
			// Ensure memcache enabled
			if (!config::MEMCACHE)
			{
				trigger_error("cache::singleton() memcache disabled in configuration!", E_USER_ERROR);
				return null;
			}

			// If instance is null, generate a new one
			self::$instance || self::$instance = new self();

			// Open memcache connection if requested, and not already open
			if ($open_connections)
			{
				if (config::MEMCACHE && !self::$instance->cache)
				{
					self::$instance->cache = self::memcache_open();
				}
			}

			return self::$instance;
		}

		// Generate a memcache connection object which can be used to perform cache get/set
		private static function memcache_open()
		{
			try
			{
				// Create and connect memcache instance
				$memcache = new Memcache;
				$memcache->connect(self::CACHE_HOST);

				return $memcache;
			}
			catch (Exception $e)
			{
				// Catch exception and throw error
				trigger_error("database::memcache_open() could not open connection to memcache", E_USER_WARNING);
				return null;
			}
		}

		// Perform cleanup and destroy memcache connection object
		private static function memcache_close()
		{
			// Close cache connection
			$singleton = self::singleton(false);
			$singleton->cache->close();

			return true;
		}

		// PUBLIC METHODS - - - - - - - - - - - - - - - - -

		// Flush all data in cache
		public static function flush()
		{
			// Flush cache
			$singleton = self::singleton();
			$singleton->cache->flush();

			// Hold for 1 second to ensure cache integrity
			$time = time() + 1;
			while (time() < $time);

			return true;
		}

		// Retrieve specified data from cache
		public static function get($key)
		{
			// Fetch data from cache instance
			$singleton = self::singleton();
			return $singleton->cache->get($key);
		}

		// Set specified data into cache with given key
		public static function set($key, $value)
		{
			// Set data into cache
			$singleton = self::singleton();
			return $singleton->cache->set($key, $value, self::CACHE_FLAG, self::CACHE_EXPIRE);
		}

		// Invalidate cache data stored under specified subkey
		public static function invalidate($subkey)
		{
			// Increment cache version counter for subkey, invalidating previous keys
			$singleton = self::singleton();

			// Load version
			$singleton->version[$subkey] = self::version($subkey) + 1;
			self::set(self::VERSION_KEY . $subkey, $singleton->version[$subkey]);
		}

		// Retrieve version number for data stored under specified subkey
		public static function version($subkey = null)
		{
			// Fetch subkey version
			$singleton = self::singleton();

			// If subkey null (not specified, return array)
			if (empty($subkey))
			{
				return $singleton->version;
			}

			// Attempt to fetch subkey version if it doesn't exist
			if (!array_key_exists($subkey, $singleton->version))
			{
				// Try to fetch from cache
				$version = self::get(self::VERSION_KEY . $subkey);
				// If version not in cache, set to 0
				if (!$version)
				{
					$version = 0;
				}
				
				// Set version in array
				$singleton->version[$subkey] = $version;
			}

			return $singleton->version[$subkey];
		}
	}
