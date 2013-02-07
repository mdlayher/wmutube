<?php
	interface login_factory
	{
		public function generate_auth();
	}

	interface login
	{
		public function authenticate();
	}

	class dblogin_factory implements login_factory
	{
		public function generate_auth()
		{
			return new dblogin();
		}
	}

	class dblogin implements login
	{
		public function authenticate()
		{
			return "Database Login";
		}
	}

	class ldaplogin_factory implements login_factory
	{
		public function generate_auth()
		{
			return new ldaplogin();
		}
	}

	class ldaplogin implements login
	{
		public function authenticate()
		{
			return "LDAP Login";
		}
	}

	$factory = new ldaplogin_factory();
	$auth = $factory->generate_auth();
	echo $auth->authenticate();
?>
