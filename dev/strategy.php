<?php
	interface i_login_strategy
	{
		function authenticate();
	}

	abstract class login_strategy implements i_login_strategy
	{
		function __construct()
		{

		}
		
		function authenticate()
		{

		}
	}

	class ldap_login extends login_strategy
	{
		function authenticate()
		{
			return "ldap";
		}
	}
	
	class db_login extends login_strategy
	{
		function authenticate()
		{
			return "db";
		}
	}

	class login
	{
		public $strategy;

		function __construct(login_strategy $strategy)
		{
			$this->strategy = $strategy;
		}
	}

	$login = new login(new ldap_login());
	printf("login: %s\n", $login->strategy->authenticate());

	$login = new login(new db_login());
	printf("login: %s\n", $login->strategy->authenticate());
?>
