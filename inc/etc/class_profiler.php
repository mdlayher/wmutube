<?php
	// class_profiler.php - Matt Layher, 10/23/12
	// PHP debugging class which allows profiling and execution timing of any running code

	class profiler
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - - - -

		// Enable or disable all profiler code, to be manually adjusted so we don't have to remove profiler
		// code from any other files
		const ON = true;

		// Determine if we want to echo the output instead of logging it to file
		const ECHO_INSTEAD = false;

		// Truncation for decimal places
		const DECIMAL = 4;

		// Log file storage
		const LOG_PATH = "profiler.log";

		// STATIC VARIABLES - - - - - - - - - - - - - - - - - -

		// Singleton instance for class
		private static $instance;

		// Stack of 'worker' arrays, and the count
		private static $stack = array();
		private static $worker_count = 0;

		// Determine if the profiler ran into any errors, which it will report
		private static $errors = false;

		// Determine if step_start() is being called for the first time
		private static $first_run = true;

		// INSTANCE VARIABLES - - - - - - - - - - - - - - - - -

		// Name of the event we are profiling
		private $event_name;

		// Starting time of event profiling
		private $profile_time;

		// A unique ID for the event we are profiling, generated from timestamp
		private $event_id;

		// STATIC FUNCTIONS - - - - - - - - - - - - - - - - - -

		// Unified output logger
		private static function log_output($input, $elapsed = '')
		{
			$singleton = self::singleton();

			// Use tabbing in output to demonstrate where recursion has occurred
			$tabbing = '';
			if (self::$worker_count > 0)
			{
				for ($i = 0; $i < self::$worker_count; $i++)
				{
					$tabbing .= "    |-";
				}
			}

			// Prepend time elapsed (if applicable) and tabbing to the input
			if ($elapsed !== '')
			{
				//$input = '[' . str_pad($elapsed, 7, '0', STR_PAD_LEFT) . "s]" . $tabbing . $input;
				$input = sprintf("[#%d|%ss]%s%s", $singleton->event_id, str_pad($elapsed, 7, '0', STR_PAD_LEFT), $tabbing, $input);
			}
			else
			{
				$input = $tabbing . $input;
			}

			// Are we logging or just echoing?
			if (self::ECHO_INSTEAD)
			{
				echo $input;
			}
			else
			{
				// Make sure the log exists, and attempt to create it if it doesn't
				if (!file_exists(self::LOG_PATH))
				{
					$file = fopen(self::LOG_PATH, 'w');
					fclose($file);
				}

				// For logging, ensure the log file is writable
				if (is_writable(self::LOG_PATH))
				{
					// Write output to log
					error_log($input, 3, self::LOG_PATH);
				}
				else
				{
					// Error out if the log file is not writable
					trigger_error("log file is inaccessible", E_USER_WARNING);
				}
			}
		}

		// Unified number formatting, to save us from huge sprintf() calls
		private static function n_format($number)
		{
			return number_format($number, self::DECIMAL, '.', '');
		}

		// Singleton, enables use of instance variables in static methods
		private static function singleton()
		{
			// If the instance is null, generate and return a new one
			self::$instance || self::$instance = new self();
			return self::$instance;
		}

		// Prints header and starts the profiling sequence (called on first run of step_start)
		private static function start($event)
		{
			// Only run if master profiler switch constant is enabled
			if (self::ON)
			{
				// Grab singleton instance to utilize static class variables
				$singleton = self::singleton();

				// Begin profiler setup routines
				// Capture event name, starting microtime
				$singleton->event_name = $event;
				$singleton->profile_time = microtime(true);

				// Capture current time to generate an ID, and for output
				$time_now = time();
				$singleton->event_id = substr($time_now, -3);

				// Create output header, log it
				self::log_output(sprintf("// - - - PROFILE STARTING: %s - - - [ID: %d] [START: %s]\n", $singleton->event_name, $singleton->event_id, date("m-d-Y h:i:s", $time_now)));
			
				// Log serialized input data from $_REQUEST
				self::log_output(sprintf("[#%d] INPUT: %s\n", $singleton->event_id, serialize($_REQUEST)));

				// Disable first_run so this code cannot be called twice
				self::$first_run = false;
			}
		}

		// DESTRUCTOR - - - - - - - - - - - - - - - - - - -

		// Kill the profiler, write footer
		function __destruct()
		{
			// Only run if master profiler switch constant is enabled
			if (self::ON)
			{
				// Grab singleton instance to utilize static class variables
				$singleton = self::singleton();

				// If we forget to stop a worker, print an error
				if (self::$worker_count > 0)
				{
					// Zero out worker count
					self::$worker_count = 0;

					self::log_output(sprintf("[#%d] ERROR: step_stop() was not called following step_start(); profile is inaccurate!!\n", $singleton->event_id));
					self::$errors = true;
				}

				// Add errors if applicable
				$error_msg = '';
				if (self::$errors)
				{
					$error_msg = " WITH ERRORS";
				}

				// Log completion header
				self::log_output(sprintf("// - - - PROFILE COMPLETE%s: %s - - - [ID: %d] [ END : %s] [ELAPSED: %ss]\n", $error_msg, $singleton->event_name, $singleton->event_id, date("m-d-Y h:i:s", time()), self::n_format(microtime(true) - $singleton->profile_time)));
			}
		}

		// Start a profiler worker, defaulting to 'strict' mode (meaning that the caller function must both start and stop the worker)
		public static function step_start($step_name = '', $strict = true)
		{
			// Run only if master switch constant is on
			if (self::ON)
			{
				// Grab singleton instance to utilize static class variables
				$singleton = self::singleton();

				// If we're on our first run, call start() to setup headers and timers
				if (self::$first_run)
				{
					// Capture name of calling script
					$debugArray = debug_backtrace();
					self::start(basename($debugArray[1]['file']));
				}

				// Capture name of calling function
				$debugArray = debug_backtrace();
				if (!isset($debugArray[1]))
				{
					// If called from global scope, there is no calling function.  Set it to "MAIN"
					$caller = "MAIN()";
				}
				else
				{
					// Else, capture caller from stack trace
					$caller = $debugArray[1]['function'] . "()";
				}

				// If name is blank, get the name from the caller
				if ($step_name === '')
				{
					$step_name = $caller;
				}

				// Generate a new 'worker', set initial parameters
				$worker = array();
				$worker['id'] = self::$worker_count;
				$worker['name'] = $step_name;
				$worker['start'] = microtime(true);
				$worker['caller'] = $caller;
				$worker['strict'] = $strict;

				// Print output message
				self::log_output(sprintf("  '%s' START [ID: %d]\n", $worker['name'], $worker['id']), self::n_format($worker['start'] - $singleton->profile_time));

				// Push this worker onto the stack
				self::$stack[] = $worker;

				// Increment worker count, return
				self::$worker_count++;
				return true;
			}
		}

		// Stop a profiler worker
		public static function step_stop()
		{
			// Run only if master switch constant is on
			if (self::ON)
			{
				// Grab singleton instance to utilize static class variables
				$singleton = self::singleton();

				// Capture name of calling function
				$debugArray = debug_backtrace();
				if (!isset($debugArray[1]))
				{
					// If called from global scope, there is no calling function.  Set it to "MAIN"
					$caller = "MAIN()";
				}
				else
				{
					// Else, capture caller from stack trace
					$caller = $debugArray[1]['function'] . "()";
				}

				// Pop this worker off the stack
				$worker = array_pop(self::$stack);

				// Make sure the array didn't return a null value
				if (empty($worker))
				{
					// Trigger an error, return function
					self::log_output(sprintf("[#%d] ERROR: step_end() was called without step_start(); profile is inaccurate!!\n", $singleton->event_id));
					self::$errors = true;
					return false;
				}

				// Decrement worker count
				self::$worker_count--;

				// Calculate worker's end time
				$end_time = microtime(true);

				// Calculate the total time elapsed and runtime duration of the current worker
				$elapsed = $end_time - $singleton->profile_time;
				$duration = $end_time - $worker['start'];

				// Print output message
				self::log_output(sprintf("  '%s'  END  [ID: %d] [d: %ss]\n", $worker['name'], $worker['id'], self::n_format($duration)), self::n_format($elapsed));

				// Check if this worker is in 'strict' mode
				if ($worker['strict'])
				{
					// If yes, ensure the same method that started the worker is also stopping it
					if ($worker['caller'] !== $caller)
					{
						// Print warning regarding strict mode
						self::log_output(sprintf("[#%d] WARNING: worker '%s' [ID: %d] violated strict mode; started by '%s', but stopped by '%s'!!\n", $singleton->event_id, $worker['name'], $worker['id'], $worker['caller'], $caller));
						$singleton->errors = true;
					}
				}

				// Destroy worker, return function
				unset($worker);
				return true;
			}
		}
	}
?>
