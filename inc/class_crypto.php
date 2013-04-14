<?php
	// class_crypto.php - Khan Academy Workflow, 3/28/13
	// PHP class which is used to encrypt and decrypt data, primarily for sessions

	error_reporting(E_ALL);

	require_once __DIR__ . "/class_config.php";

	class crypto
	{
		// CONSTANTS - - - - - - - - - - - - - - - - - - - -

		// Encryption settings
		const CIPHER = MCRYPT_RIJNDAEL_256;
		const CIPHER_MODE = MCRYPT_MODE_CBC;
		const CIPHER_KEY = config::CIPHER_KEY;

		// PUBLIC METHODS - - - - - - - - - - - - - - - - 

		// Encrypt session data before storage
		public static function encrypt($data, $base64 = true)
		{
			// Load mcrypt
			$mcrypt = mcrypt_module_open(self::CIPHER, '', self::CIPHER_MODE, '');
		
			// Generate mcrypt IV
			$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($mcrypt), MCRYPT_RAND);

			// Init mcrypt
			mcrypt_generic_init($mcrypt, self::CIPHER_KEY, $iv);

			// Encrypt input data
			$encrypted_data = mcrypt_generic($mcrypt, $data);

			// Unload mcrypt
			mcrypt_generic_deinit($mcrypt);

			// Optionally, base64 encode data for non-binary transmission
			if ($base64)
			{
				$encrypted_data = base64_encode($iv.$encrypted_data);
			}

			return $encrypted_data;
		}

		// Decrypt session data on read
		public static function decrypt($data, $base64 = true)
		{
			// base64 decode data if it was encoded after encryption
			if ($base64)
			{
				$data = base64_decode($data);
			}

			// Load mcrypt
			$mcrypt = mcrypt_module_open(self::CIPHER, '', self::CIPHER_MODE, '');

			// Get IV size, get IV and encrypted data
			$iv_size = mcrypt_enc_get_iv_size($mcrypt);
			$iv = substr($data, 0, $iv_size);
			$data = substr($data, $iv_size);

			// Initialize mcrypt, decrypt
			mcrypt_generic_init($mcrypt, self::CIPHER_KEY, $iv);
			$decrypted_data = mdecrypt_generic($mcrypt, $data);

			// Return decrypted data, trimming padding
			return trim($decrypted_data);
		}
	}
