<?php
	require_once "header.inc.php";
?>
<div id="body_container">
	<span id="error"></span>
	<div style="margin-left: 25%">
		<pre>
		Session Dump
<?= print_r($_SESSION, true); ?>
		</pre>
		<pre>
		Cache Version

<?= print_r(cache::version(), true); ?>
		</pre>
		<a href="debug?selftest=1">Selftest</a>
		<?php
			if (isset($_GET['selftest']) && $_GET['selftest'])
			{
				config::load(array("user", "video", "course", "question", "answer", "role"));
				echo "<pre>\n";
				answer::selftest();
				course::selftest();
				question::selftest();
				role::selftest();
				user::selftest();
				video::selftest();
				echo "</pre>\n";
			}
		?>
	</div>
</div>
<?php
	require_once "footer.inc.php";
?>
