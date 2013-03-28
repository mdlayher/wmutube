<?php
	require_once "header.inc.php";
?>
<div id="body_container">
	<span id="error"></span>
	<div style="margin-left: 25%">
		<pre>
		Session Dump
<?= print_r($_SESSION, true); ?>
		<br />
		Cache Version

<?= print_r(cache::version(), true); ?>
		<br />
		<a href="debug?selftest=1">Selftest</a>

		<?php
			if (isset($_GET['selftest']) && $_GET['selftest'])
			{
				echo "\n";
				include_once __DIR__ . "/../inc/test/selftest.php";
			}
		?>

		<br />
		<a href="debug?logintest=1">Login Test</a>

		<?php
			if (isset($_GET['logintest']) && $_GET['logintest'])
			{
				echo "\n";
				include_once __DIR__ . "/../inc/test/login_test.php";
			}
		?>
		</pre>
		<br />
		<br />
		<br />
	</div>
</div>
<?php
	require_once "footer.inc.php";
?>
