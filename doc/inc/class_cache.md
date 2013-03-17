# class_cache.php

### Description:
PHP class which abstracts cache functionality into simple, efficient, and re-usable methods

### Constants:
* `const CACHE_*` - memcache default configuration
* `const VERSION_KEY` - key to prepend and store versioning array in cache

### Constructor / Destructor:

#### `function __destruct()`
> Handles cleanup and closes memcache connection on class destruct
	
### Private Methods:

#### `private static function singleton($open_connections = true)`
> Generates or returns a singleton instance of cache class, opening memcache connection by default

#### `private static function memcache_open()`
> Opens connection to memcache via Memcache class using specified host

#### `private static function memcache_close()`
> Closes singleton instance of cache connection

### Public Methods:

#### `public static function flush()`
> Flushes all data from memcache.  Note, this function will block for 1 second to ensure cache integrity.

#### `public static function get($key)`
> Fetch data from singleton instance of memcache using specified key

#### `public static function set($key, $value, $expire = self::CACHE_EXPIRE)`
> Set data into cache using specified key, value, and timeout (defaults to const default)

#### `public static function invalidate($subkey)`
> Increments cache version on specified subkey, invalidating previous versions.  Should be called on any destructive operations to ensure cache is not serving stale data.

#### `public static function version($subkey = null)`
> Returns cache version for specified subkey, or entire version array if subkey is null